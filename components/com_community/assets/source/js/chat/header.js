(function( $, _, Backbone ) {

    var util = require( './util' );

    /**
     * Conversation header view.
     * @class {Backbone.View}
     */
    module.exports = Backbone.View.extend({

        el: '.joms-js--chat-header',

        events: {
            'click .joms-js--chat-new-message': 'createDraftConversation',
            'keyup .joms-input': 'selectorHideOnEscape',
            'click .joms-js--chat-header-selector-div a': 'selectorSelect',
            'click .joms-js--chat-leave': 'leaveChat',
            'click .joms-js--chat-mute': 'muteChat',
            'keyup .joms-chat__search_user': 'searchUser',
            'keydown .joms-chat__search_user': 'deleteSelected'
        },

        initialize: function () {
            this.$info = this.$('.joms-js--chat-header-info');
            this.$recipients = this.$info.find('.joms-chat__recipents');
            this.$selector = this.$('.joms-js--chat-header-selector');
            this.$selected = this.$selector.find('.joms-chat-selected');
            this.$selectorInput = this.$selector.find('.joms-input');
            this.$selectorDiv = this.$selector.find('.joms-js--chat-header-selector-div');
            this.$optionDropdown = this.$('.joms-js--chat-dropdown');
            this.$searchInput = this.$('.joms-chat__search_user');
            this.count = 0;
            joms_observer.add_action('chat_selector_hide', this.selectorHide, 1, 0, this);
            joms_observer.add_action('chat_selector_show', this.selectorShow, 1, 0, this);
            joms_observer.add_action('chat_selector_reset', this.selectorReset, 1, 0, this);
            joms_observer.add_action('chat_update_info', this.updateChatInfo, 1, 0, this);
            joms_observer.add_action('chat_render_option_dropdown', this.renderOptionDropdown, 1, 2, this);
        },

        searchUser: function(e) {
            var header = this;
            header.count = header.count + 1;
            var count = header.count;
            var keyword = header.$searchInput.val().trim();
            var selected = this.$selected.find('.user-selected');
            var exclusion = '';
            if (selected.length) {
                exclusion = _.map(selected, function(item) {
                    return $(item).data('user-id');
                }).join(',');
            }
            header.$selectorDiv.empty();
            header.$selectorDiv.hide();
            clearTimeout(header.search);
            if (!keyword) {
                return;
            }
            if ((e.which < 112 && e.which > 47) || e.which === 8 || e.which === 16) {
                header.search = setTimeout( function() {
                    header.$selectorDiv.append('<img src="/components/com_community/assets/ajax-loader.gif" alt="loader" />');
                    header.$selectorDiv.show();
                    joms.ajax({
                        func: 'chat,ajaxGetFriendListByName',
                        data: [keyword, exclusion],
                        callback: function (json) {
                            if (header.count !== count) {
                                return;
                            }
                            if (_.isArray(json) && json.length) {
                                header.$selectorDiv.find('img').remove();
                                header.selectorRender(json);
                            } else {
                                header.$selectorDiv.text( header.$selectorDiv.data('textNoResult') );
                            }
                        }
                    });
                },500);
            }
        },

        leaveChat: function() {
            joms_observer.do_action('chat_leave');
            this.$optionDropdown.hide();
        },

        muteChat: function( e ) {
            var $dd = this.$optionDropdown,
                $btn = $( e.currentTarget ),
                mute = +$btn.data( 'mute' ),
                text = $btn.data( mute ? 'textMute' : 'textUnmute' );

            $dd.hide();
            $btn.html( text ).data( 'mute', mute ? 0 : 1 );
            joms_observer.do_action( 'chat_mute', mute );
        },

        renderOptionDropdown: function( type, mute ) {
            var $dd = this.$optionDropdown,
                $mute = $dd.find( '.joms-js--chat-mute' ),
                $add = $dd.find( '.joms-js--chat-add-people' ),
                $leave = $dd.find( '.joms-js--chat-leave' );

            $mute.data( 'mute', +mute ).html( $mute.data( +mute ? 'textUnmute' : 'textMute' ) );
            $add.css({ display: type === 'group' ? '' : 'none' });
        },

        deleteSelected: function (e) {
            var keyword = this.$searchInput.val().trim();
            if (e.which === 8 && !keyword) {
                var selected = this.$selected.find('.user-selected').last(),
                    user_id = selected.data('user-id');
                selected.remove();
                this.updateChatInfo();
            }
        },

        createDraftConversation: function () {
            this.selectorShow();
            joms_observer.do_action('chat_create_draft_conversation');
        },

        /**
         * Render friend selector.
         * @param {object} buddies
         */
        selectorRender: function (buddies) {
            _.each(buddies, function (buddy) {
                this.$selectorDiv.append([
                    '<a href="javascript:" class="joms-selector-', buddy.id, '" data-user-id="', buddy.id, '" style="display:block;padding:5px">',
                        '<img src="'+buddy.avatar+'" style="width: 30px; padding: 4px;">',
                        '<span>', buddy.name, '</span>',
                    '</a>'
                ].join(''));
            }, this);
        },

        /**
         * Show new chat selector.
         * @params {HTMLEvent} e
         */
        selectorShow: function () {
            this.$info.hide();
            this.$selector.show();
            this.$selectorInput.val('').focus();
        },

        /**
         * Hide new chat selector.
         */
        selectorHide: function () {
            this.$selectorInput.val('');
            this.$selector.hide();
            this.$info.show();
            this.$selectorDiv.hide();
        },

        selectorReset: function () {
            this.$selected.empty();
            this.$selectorDiv.empty();
        },

        /**
         * Hide new chat selector if Esc key is pressed.
         * @params {HTMLEvent} e
         */
        selectorHideOnEscape: function (e) {
            if (e.which === 27 /* Esc key */) {
                this.selectorHide();
                joms_observer.do_action('chat_selector_hide');
                joms_observer.do_action('chat_selector_reset');
                joms_observer.do_action('chat_remove_draft_conversation');
                joms_observer.do_action('chat_open_first_window');
            }
        },

        /**
         * Hide new chat selector on input blur.
         * @params {HTMLEvent} e
         */
        selectorHideOnBlur: function (e) {
            this.selectorHide();
        },

        /**
         * Create new conversation with friend.
         * @params {HTMLEvent} e
         */
        selectorSelect: function (e) {
            var el, name, user_id, span, avatar;
            el = $(e.currentTarget);
            name = el.find('span').text();
            user_id = el.data('user-id');
            avatar = el.find('img').attr('src');
            span = '<button data-user-id="' + user_id + '" class="btn user-selected">' + name + '</button>';
            this.$selected.append(span);
            el.hide();
            this.$searchInput.val('').focus();
            this.$selectorDiv.hide();
            this.updateChatInfo();
            joms_observer.do_action('chat_buddy_add', user_id, name, avatar);
        },

        updateChatInfo: function () {
            var user_selected,
                partner = [],
                name = [],
                chatname = '';
            user_selected = this.$selected.find('.user-selected');
            if (user_selected.length < 1) {
                joms_observer.do_action('chat_empty_message_view');
            } else if (user_selected.length === 1) {
                name.push(user_selected.text());
                joms_observer.do_action('chat_single_conversation_get_by_user', user_selected.data('user-id'));
            } else if (user_selected.length > 1) {
                _.each(user_selected, function (item) {
                    partner.push($(item).data('user-id'));
                    name.push($(item).text());
                }, this);
                joms_observer.do_action('chat_empty_message_view');
            }

            if ( name.length ) {
                chatname = util.formatName( name );
            }

            joms_observer.do_action('chat_update_draft_conversation', chatname, partner);
            joms_observer.do_action('rename_chat_title', chatname);

        }

    });

})( joms_libs.$, joms_libs._, joms_libs.Backbone );
