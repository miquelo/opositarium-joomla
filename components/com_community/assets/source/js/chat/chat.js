(function ($, _, Backbone, factory) {

    joms.onStart(function () {
        var Chat = factory($, _, Backbone);
        joms.chat = new Chat();
    });

})(joms_libs.$, joms_libs._, joms_libs.Backbone, function ($, _, Backbone) {

    var Notification = require( './notification' ),
        HeaderView = require( './header' ),
        SidebarView = require( './sidebar' ),
        MessagesView = require( './messages' ),
        MessageBox = require( './messagebox' );

    /**
     * Conversation main class.
     * @class Chat
     */
    function Chat() {
        this.init();
    }

    Chat.prototype = {

        /**
         * Current user information.
         */
        me: {id: 0, name: '', avatar: ''},

        /**
         * Buddy list.
         */
        buddies: {},

        /**
         * Conversation list.
         * @type {object}
         */
        conversations: {},

        /**
         * Active conversation.
         */
        active: {},

        ping: {},

        ajax_get_chat_by_user: {},

        last_activity: 0,

        show_seen: 0,

        ping_time: 1000,

        /**
         * Chat initialization.
         */
        init: function () {
            this.render();

            var id = +window.joms_my_id;
            if (!id) {
                joms_observer.do_action('chat_user_logout');
                return false;
            }
            if (+joms_vars.chat_enablereadstatus) {
                this.show_seen = 1;
            }

            this.me.id = id;
            joms_observer.do_action('chat_user_login');

            joms_observer.add_action('chat_sidebar_select', this.conversationOpen, 10, 1, this);
            joms_observer.add_action('chat_messagebox_send', this.messageSend, 10, 2, this);
            joms_observer.add_action('chat_message_recall', this.messageRecall, 10, 1, this);
            joms_observer.add_action('chat_single_conversation_get_by_user', this.singleConversationGetByUser, 1, 1, this);
            joms_observer.add_action('chat_update_draft_conversation', this.updateDraftConversation, 1, 2, this);
            joms_observer.add_action('chat_create_draft_conversation', this.createDraftConversation, 1, 0, this);
            joms_observer.add_action('chat_remove_draft_conversation', this.removeDraftConversation, 1, 0, this);
            joms_observer.add_action('chat_set_location_hash', this.setLocationHash, 1, 1, this);
            joms_observer.add_action('chat_leave', this.leaveChat, 2, 1, this);
            joms_observer.add_action('chat_add_people', this.addPeople, 1, 1, this);
            joms_observer.add_action('chat_buddy_add', this.buddyAdd, 1, 3, this);
            joms_observer.add_action('chat_get_previous_messages', this.conversationGetPrevMessages, 1, 2, this);
            joms_observer.add_action('chat_mute', this.muteChat, 2, 1, this);

            var noFriend = 1,
                noConversation = 1;

            this.friendListUpdate().done(function( friends ) {
                if ( _.isArray( friends ) && friends.length ) {
                    noFriend = 0;
                }
                this.conversationListUpdate().done(function( data ) {
                    var $startScreen = $('.joms-js-page-chat-loading'),
                        $chatScreen = $('.joms-js-page-chat');

                    if ( data && $.isPlainObject( data.list ) && _.keys( data.list ).length ) {
                        noConversation = 0;
                    }

                    if ( noConversation ) {
                        if ( noFriend ) {
                            $startScreen.find('.joms-js-loading-no-friend').show();
                        } else {
                            $startScreen.find('.joms-js-loading-no-conv').show().on('click', function() {
                                $startScreen.hide();
                                $chatScreen.show();
                            });
                        }

                        $startScreen.find('.joms-js-loading').hide();
                        $startScreen.find('.joms-js-loading-empty').show();
                    } else {
                        $startScreen.hide();
                        $chatScreen.show();
                    }

                    // Update ping time when browser is in the background.
                    $( window ).on( 'blur', $.proxy( function() {
                        var pingTime = +joms_vars.chat_pooling_time_inactive;
                        if ( pingTime >= 1 ) {
                            this.ping_time = pingTime * 1000;
                        }
                    }, this ) ).on( 'focus', $.proxy( function() {
                        var pingTime = +joms_vars.chat_pooling_time_active;
                        if ( pingTime >= 1 ) {
                            this.ping_time = pingTime * 1000;
                        }
                        this.conversationPing();
                    }, this ) ).triggerHandler( 'focus' );


                });
            });
        },

        render: function () {
            // initialize views
            var header = new HeaderView();
            var sidebar = new SidebarView();
            var messages = new MessagesView();
            var messageBox = new MessageBox();
            var notification = new Notification();
        },

        muteChat: function(mute) {
            this.active.mute = +mute ? 0 : 1;
            joms.ajax({
                func: 'chat,ajaxMuteChat',
                data: [this.active.chat_id, this.active.mute]
            });
        },

        addPeople: function( friends ) {
            var ids = [],
                key;

            for ( key in friends ) {
                this.buddyAdd( friends[key].id, friends[key].name, friends[key].avatar );
                ids.push( key );
            }

            joms.ajax({
                func: 'chat,ajaxAddPeople',
                data: [ this.active.chat_id, JSON.stringify( ids ) ],
                callback: $.proxy( function() {
                    var ids = [ this.active.chat_id ];
                    this.updateChatList( JSON.stringify( ids ) );
                }, this )
            });
        },

        leaveChat: function() {
            var chat_id = this.active.chat_id;
            joms.ajax({
                func: 'chat,ajaxLeaveChat',
                data: [chat_id]
            });
            this.active = {};
            delete this.conversations['chat_'+ chat_id];
            joms_observer.do_action('chat_remove_window', chat_id);
            joms_observer.do_action('chat_removemove_notification', chat_id);
            joms_observer.do_action('chat_empty_message_view');

        },

        setLocationHash: function(chat_id) {
            window.location.hash = chat_id;
        },

        getLocationHash: function() {
            var hash = window.location.hash.replace('#','');
            return +hash;
        },

        updateDraftConversation: function (name, partner) {
            if (this.conversations['chat_0'].temp_chat_id) {
                delete this.conversations['chat_0'].temp_chat_id;
            }
            this.conversations['chat_0'].name = name;
            this.conversations['chat_0'].partner = partner;
            this.active = this.conversations['chat_0'];
        },

        createDraftConversation: function () {
            if (!this.conversations.hasOwnProperty('chat_0')) {
                var conversation = {
                    chat_id: '0',
                    name: '',
                    partner: [],
                    type: 'new',
                    thumb: '/components/com_community/assets/mood_21.png'
                };

                this.conversations['chat_0'] = conversation;
                joms_observer.do_action('chat_render_draft_conversation', conversation);
            }
            joms_observer.do_action('chat_hightlight_active_window', 0);
            joms_observer.do_action('chat_conversation_open');
            this.conversationOpen(0);
        },

        removeDraftConversation: function () {
            delete this.conversations['chat_0'];
        },

        singleConversationGetByUser: function (user_id) {
            return $.Deferred($.proxy(function (defer) {
                joms_observer.do_action('chat_messages_loading');
                joms.ajax({
                    func: 'chat,ajaxGetSingleChatByUser',
                    data: [user_id],
                    callback: $.proxy(function (json) {
                        if (json.partner) {
                            if (!this.buddies.hasOwnProperty(json.partner.id)) {
                                this.buddyAdd(json.partner.id, json.partner.name, json.partner.avatar);
                            }
                            if (json.messages && _.isArray(json.messages)) {
                                joms_observer.do_action('chat_messages_loaded', json.messages, this.buddies);
                            } else {
                                joms_observer.do_action('chat_empty_message_view');
                            }
                            this.conversations['chat_0'].name = json.partner.name;
                            this.conversations['chat_0'].partner = [json.partner.id];
                            if (json.chat_id) {
                                this.conversations['chat_0'].temp_chat_id = json.chat_id;
                                this.doSeen(json.chat_id);
                                this.setSeen(json.chat_id);
                            }
                            this.active = this.conversations['chat_0'];
                        }
                        defer.resolveWith(this, [json]);
                    }, this)
                });
            }, this));
        },

        /**
         * Get list of conversation by current user.
         * @return {jQuery.Deferred}
         */
        conversationListUpdate: function() {
            return $.Deferred( $.proxy( function( defer ) {
                joms.ajax({
                    func: 'chat,ajaxInitializeChatData',
                    callback: $.proxy( function( json ) {
                        var hash;

                        this.last_activity = json.last_activity ? json.last_activity : 0;

                        _.each( json.buddies, function( buddy ) {
                            this.buddyAdd( buddy.id, buddy.name, buddy.avatar );
                        }, this );

                        // Update conversation listing.
                        if ( json.list && Object.keys( json.list ).length ) {
                            this.conversations = json.list;
                            joms_observer.do_action( 'chat_conversation_render', this.conversations );
                            hash = this.getLocationHash();
                            if ( hash ) {
                                joms_observer.do_action( 'chat_open_window_by_chat_id', hash );
                            } else {
                                joms_observer.do_action( 'chat_open_first_window' );
                            }
                        }

                        this.updateConversations();

                        defer.resolveWith( this, [ json ] );
                    }, this )
                });
            }, this ) );
        },

        setActiveChat: function (chat_id) {
            this.active = this.conversations[ 'chat_' + chat_id ];
            this.updateConversations();
        },

        formatData: function (data, buddies) {
            for (var i in data) {
                if (data[i].type === 'single') {
                    var partner = data[i].partner;
                    data[i].thumb = buddies[partner].avatar;
                    data[i].name = buddies[partner].name;
                } else {
                    data[i].thumb = '/components/com_community/assets/group_thumb.jpg';
                }
            }
            return data;
        },

        /**
         * Open a conversation with specified ID.
         * @param {number} [chat_id]
         * @return {jQuery.Deferred}
         */
        conversationOpen: function (chat_id) {
            // BUG: when send msg from draft conversation
            if (joms_vars.is_chat_view) {
                this.setLocationHash(chat_id);
            }

            if (+chat_id === +this.active.chat_id) {
                return;
            }

            if (this.active.temp_chat_id && this.active.temp_chat_id == chat_id) {
                this.setActiveChat(chat_id);
                return;
            }

            if (+chat_id === 0 && this.conversations['chat_0'].temp_chat_id && this.conversations['chat_0'].temp_chat_id == this.active.chat_id) {
                this.setActiveChat(0);
                return;
            }

            this.setActiveChat(chat_id);

            if (this.active.temp_chat_id) {
                chat_id = this.active.temp_chat_id;
            }

            joms_observer.do_action( 'chat_conversation_open', this.active.type, this.active.participants );
            joms_observer.do_action( 'chat_empty_message_view' );
            joms_observer.do_action( 'chat_render_option_dropdown', this.active.type, this.active.mute );

            return $.Deferred( $.proxy( function( defer ) {
                if ( chat_id ) {
                    // get previous messages
                    this.conversationGetPrevMessages( chat_id, 0 ).done( $.proxy( function( json ) {
                        defer.resolveWith( this );
                    }, this ) );
                }
            }, this ) );
        },

        /**
         * Get conversation messages before specific message defined it's ID.
         * @param {number} chatId
         * @param {number} [lastMessageId]
         * @returns jQuery.Deferred
         */
        conversationGetPrevMessages: function (chat_id, offset) {
            if (this.getting_previous_messagse) {
                return;
            }
            if (chat_id) {
                joms_observer.do_action('chat_messages_loading');
            } else {
                if (+this.active.chat_id) {
                    chat_id = +this.active.chat_id;
                } else if (this.active.temp_chat_id) {
                    chat_id = +this.active.temp_chat_id;
                } else {
                    joms_observer.do_action('chat_previous_messages_loaded',[]);
                    return;
                }
            }
            this.getting_previous_messagse = 1;
            return $.Deferred($.proxy(function (defer) {
                joms.ajax({
                    func: 'chat,ajaxGetLastChat',
                    data: [chat_id, offset],
                    callback: $.proxy(function (json) {
                        this.getting_previous_messagse = 0;
                        if ( _.isArray(json.messages) && _.isArray(json.seen)) {
                            if (offset) {
                                if ( ! json.messages.length ) {
                                    json.messages.push({
                                       id: 0,
                                       message: null,
                                       attachment: null,
                                       user: null,
                                       timestamp: null,
                                       action: 'end'
                                    });
                                }
                                joms_observer.do_action('chat_previous_messages_loaded', json.messages, this.buddies);
                            } else {
                                joms_observer.do_action('chat_messages_loaded', json.messages, this.buddies);
                                if (this.show_seen) {
                                    joms_observer.do_action('chat_seen_message', json.seen, this.me, this.buddies);
                                }
                                this.doSeen(chat_id);
                                this.setSeen(chat_id);
                            }
                        }
                        defer.resolveWith(this, [json]);
                    }, this)
                });
            }, this));
        },

        /**
         * Ping server for any update on conversations.
         */
        conversationPing: function() {
            var timestamp;

            // Cancel scheduled next ping.
            clearTimeout( this._conversationPingTimer );

            // Remember timestamp.
            this._conversationPingTimestamp = timestamp = ( new Date ).getTime();

            // Perform ping to server.
            this._conversationPing().done( $.proxy( function( json ) {
                var active = [],
                    seen = [],
                    leave = [],
                    inactive = [],
                    newchat = [],
                    ids = [],
                    add = [];

                // Do not proceed if timestamp is different, meaning that ping was called again during request.
                if ( this._conversationPingTimestamp !== timestamp ) {
                    return;
                }

                // Add new buddy list if any.
                if ( json.newcomer && _.isArray( json.newcomer ) && json.newcomer.length ) {
                    _.each( json.newcomer, function( buddy ) {
                        if ( +this.me.id !== +buddy.id ) {
                            this.buddyAdd( buddy.id, buddy.name, buddy.avatar );
                        }
                    }, this );
                }

                // Handle received messages.
                if ( json.activities && _.isArray( json.activities ) && json.activities.length ) {
                    data = json.activities;
                    this.last_activity = data[ data.length - 1 ].id;

                    if (Object.keys(this.active).length) {
                        active = _.filter(data, $.proxy(function (item) {
                            var same_chat_id = +this.active.chat_id === +item.chat_id,
                                temp_chat_active = this.active.temp_chat_id && +this.active.temp_chat_id === +item.chat_id,
                                is_msg = item.action === 'sent' || item.action === 'leave' || item.action === 'add',
                                not_my_msg = +item.user_id !== +this.me.id;
                            return is_msg && not_my_msg && (same_chat_id || temp_chat_active);
                        }, this));
                        if (active.length) {
                            joms_observer.do_action('chat_messages_received', active, this.buddies);
                            if (+this.active.chat_id) {
                                this.doSeen(this.active.chat_id);
                            } else {
                                this.doSeen(this.active.temp_chat_id);
                            }
                            joms_observer.do_action('chat_remove_seen_message');
                            joms_observer.do_action('chat_move_window_to_top', active);
                            joms_observer.do_action('chat_move_notification_to_top', active);
                        }

                        seen = _.filter(data, $.proxy(function (item) {
                            return item.action === 'seen';
                        }, this));

                        if (seen.length) {
                            for (var i = 0; i < seen.length; i++) {
                                if ( +seen[i].user_id === +this.me.id) {
                                    this.setSeen(seen[i].chat_id);
                                }
                            }
                            var seen_active = _.filter(seen, function(item) {
                                return +item.chat_id === +this.active.chat_id ;
                            }, this);
                            if (this.show_seen) {
                                joms_observer.do_action('chat_seen_message', seen_active, this.me, this.buddies);
                            }
                        }
                    }

                    inactive = _.filter(data, $.proxy(function (item) {
                        var is_msg = item.action === 'sent',
                            not_in_active_chat = +this.active.chat_id !== +item.chat_id,
                            not_new_chat = this.conversations.hasOwnProperty('chat_' + item.chat_id),
                            not_my_msg = +item.user_id !== +this.me.id;
                        return is_msg && not_my_msg && not_in_active_chat && not_new_chat;
                    }, this));

                    if ( inactive.length ) {
                        for (var i = 0; i < inactive.length; i++) {
                            this.setUnread(inactive[i].chat_id);
                        }
                        joms_observer.do_action('chat_highlight_unread_windows', inactive);
                        joms_observer.do_action('chat_move_window_to_top', inactive);
                        joms_observer.do_action('chat_move_notification_to_top', inactive);
                    }

                    // Update sidebar if necessary.
                    var needUpdateSidebar = [];
                    _.each( data, function( item ) {
                        var needUpdate = false,
                            chatId = +item.chat_id;

                        // Exclude my own activities.
                        if ( +this.me.id !== +item.user_id ) {

                            // When somebody is leaving or added to the chat.
                            if ( item.action === 'leave' || item.action === 'add' ) {
                                needUpdate = true;

                            // When there is a new conversation.
                            } else if ( ! _.has( this.conversations, 'chat_' + chatId ) ) {
                                needUpdate = true;
                            }
                        }

                        if ( needUpdate && needUpdateSidebar.indexOf( chatId ) < 0 ) {
                            needUpdateSidebar.push( chatId );
                        }
                    }, this );

                    if ( needUpdateSidebar.length ) {
                        needUpdateSidebar = JSON.stringify( needUpdateSidebar );
                        this.updateChatList( needUpdateSidebar );
                    }

                    joms_observer.do_action('chat_set_notification_label', this.countUnread(this.conversations));
                }

                // Schedule next ping.
                this._conversationPingTimer = setTimeout( $.proxy( function() {
                    this.conversationPing();
                }, this ), this.ping_time );

            }, this ) );
        },

        countUnread: function(data) {
            var count = 0;
            for(var key in data) {
                if ( +data[key].seen === 0 && +data[key].mute === 0 ) {
                    count++;
                }
            }
            return count;
        },

        setUnread: function (chat_id) {
            this.conversations['chat_' + chat_id].seen = 0;
            joms_observer.do_action('chat_set_notification_label_unread', chat_id);
        },

        setSeen: function (chat_id) {
            if (this.conversations['chat_' + chat_id]) {
                this.conversations['chat_' + chat_id].seen = 1;
                joms_observer.do_action('chat_set_notification_label', this.countUnread(this.conversations));
                joms_observer.do_action('chat_set_notification_label_seen', chat_id);
                joms_observer.do_action('chat_set_window_seen', chat_id);
            }
        },

        doSeen: function (chat_id) {
            return $.Deferred($.proxy(function (defer) {
                joms.ajax({
                    func: 'chat,ajaxSeen',
                    data: [chat_id]
                });
            }, this));
        },

        _conversationPing: function () {
            return $.Deferred($.proxy(function (defer) {
                this.ping = joms.ajax({
                    func: 'chat,ajaxPingChat',
                    data: [this.last_activity],
                    callback: $.proxy(function (json) {
                        defer.resolveWith(this, [json]);
                    }, this)
                });
            }, this));
        },

        /**
         * Get latest chat listing.
         * @param {number[]} ids
         * @return {_.debounce}
         */
        updateChatList: _.debounce( function( ids ) {
            joms.ajax({
                func: 'chat,ajaxGetChatList',
                data: [ ids ],
                callback: $.proxy( function( json ) {
                    var list, unread;

                    _.each( json.buddies, function( buddy ) {
                        this.buddyAdd( buddy.id, buddy.name, buddy.avatar );
                    }, this );

                    // Format conversation listing.
                    list = this.formatData( json.list, this.buddies );

                    // Update conversation listing.
                    _.each( list, function( item ) {
                        this.conversations[ 'chat_' + item.chat_id ] = item;
                    }, this );

                    // Get total unread.
                    unread = this.countUnread( this.conversations );

                    joms_observer.do_action( 'chat_conversation_render', list );
                    joms_observer.do_action( 'chat_highlight_unread_windows', list );
                    joms_observer.do_action( 'chat_set_notification_label', unread );
                    joms_observer.do_action( 'chat_move_window_to_top', list );
                    joms_observer.do_action( 'chat_move_notification_to_top', list );

                    this.updateConversations();

                }, this )
            });
        }, 1000 ),

        /**
         * Sends message.
         * @param {string} message
         * @returns jQuery.Deferred
         */
        messageSend: function (message, attachment) {
            this.ping.abort();
            var partner = [], name = '', chat_id = 0;
            if (+this.active.chat_id === 0) {
                partner = this.active.partner;
                name = this.active.name;
                joms_observer.do_action('chat_selector_hide');
                joms_observer.do_action('chat_selector_reset');
                if (this.active.temp_chat_id) {
                    joms_observer.do_action('chat_hightlight_active_window', this.active.temp_chat_id);
                    joms_observer.do_action('chat_remove_draft_conversation');

                    this.removeDraftConversation();
                    chat_id = this.active.temp_chat_id;
                    this.setActiveChat(chat_id);
                }
            } else {
                chat_id = +this.active.chat_id;
            }
            joms_observer.do_action('chat_move_window_to_top', [this.active]);
            joms_observer.do_action('chat_move_notification_to_top', [this.active]);
            joms_observer.do_action('chat_remove_seen_message');

            return $.Deferred($.proxy(function (defer) {
                var now = (new Date()).getTime();
                joms_observer.do_action('chat_message_sending', message, attachment, this.me, now);

                // Remove unneeded information.
                attachment = $.extend({}, attachment || {});
                delete attachment.name;
                delete attachment.url

                joms.ajax({
                    func: 'chat,ajaxAddChat',
                    data: [chat_id, message, JSON.stringify(attachment), JSON.stringify(partner), name],
                    callback: $.proxy(function (json) {
                        joms_observer.do_action( 'chat_message_sent', json.reply_id, now, json.attachment || {} );
                        if (chat_id === 0) {
                            this.conversations['chat_' + json.chat.chat_id] = this.formatData([json.chat], this.buddies)[0];
                            this.active = this.conversations['chat_' + json.chat_id];
                            this.setLocationHash(this.active.chat_id);
                            joms_observer.do_action('chat_override_draft_chat_window', this.active);
                            joms_observer.do_action('chat_hightlight_active_window', this.active.chat_id);
                            joms_observer.do_action('chat_render_option_dropdown', this.active.type, this.active.mute);
                            this.removeDraftConversation();
                        }
                        defer.resolveWith(this, [json]);
                    }, this)
                });
            }, this));
        },

        /**
         * Recall sent message.
         * @param {number} msgId
         * @returns jQuery.Deferred
         */
        messageRecall: function( msgId ) {
            return $.Deferred( $.proxy(function( defer ) {
                joms.ajax({
                    func: 'chat,ajaxRecallMessage',
                    data: [ msgId ],
                    callback: $.proxy(function( json ) {
                        defer.resolveWith( this, [ json ]);
                    }, this  )
                });
            }, this  ));
        },

        /**
         * Naively get friend list from `window.joms_friends` value.
         * @returns jQuery.Deferred
         */
        friendListUpdate: function () {
            return $.Deferred($.proxy(function (defer) {
                var timer = setInterval($.proxy(function () {
                    if (!_.isUndefined(window.joms_friends)) {
                        clearInterval(timer);
                        var friends = [];
                        var joms_friends = window.joms_friends;
                        for (var i = 0; i < joms_friends.length; i++) {
                            if (+joms_friends[i].id === +this.me.id) {
                                this.buddyAdd(this.me.id, 'You', joms_friends[i].avatar);
                                this.me.name = 'You';
                                this.me.avatar = joms_friends[i].avatar;
                            } else {
                                friends.push(joms_friends[i]);
                            }
                        }
                        defer.resolveWith(this, [ friends ]);
                    }
                }, this), 100);
            }, this));
        },

        /**
         * Add buddy list.
         * @param {number} id
         * @param {string} name
         * @param {string} avatar
         */
        buddyAdd: function (id, name, avatar) {
            id = +id;
            if (!this.buddies.hasOwnProperty(id)) {
                this.buddies[ id ] = {
                    id: id,
                    name: name,
                    avatar: avatar
                };
            }
        },

        /**
         * Update conversations based on current state.
         * @return {_.debounce}
         */
        updateConversations: _.throttle( function() {
            var active = this.active || {},
                activeId = +active.chat_id;

            _.each( this.conversations, function( item ) {
                var isActive = +item.chat_id === activeId,
                    copy = $.extend( {}, item, { active: isActive } );

                joms_observer.do_action( 'chat_conversation_update', copy );
            }, this );
        }, 500 )

    };

    return Chat;

});
