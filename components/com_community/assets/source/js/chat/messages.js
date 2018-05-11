(function( $, _, Backbone ) {

    var util = require( './util' );

    /**
     * Conversation messages view.
     * @class {Backbone.View}
     */
    module.exports = Backbone.View.extend({

        el: '.joms-chat__messages',

        events: {
            'click .joms-chat__message-actions a': 'recallMessage',
            'wheel .joms-js--chat-conversation-messages': 'scrollMessages'
        },

        initialize: function (config) {
            this.$loading = this.$('.joms-js--chat-conversation-loading');
            this.$messages = this.$('.joms-js--chat-conversation-messages');
            this.$noParticipants = this.$('.joms-js--chat-conversation-no-participants');

            joms_observer.add_action('chat_conversation_open', this.render, 10, 2, this);
            joms_observer.add_action('chat_conversation_update', this.update, 10, 1, this);
            joms_observer.add_action('chat_messages_loading', this.messagesLoading, 10, 1, this);
            joms_observer.add_action('chat_messages_loaded', this.messagesLoaded, 10, 3, this);
            joms_observer.add_action('chat_messages_received', this.messagesReceived, 10, 3, this);
            joms_observer.add_action('chat_message_sending', this.messageSending, 10, 5, this);
            joms_observer.add_action('chat_message_sent', this.messageSent, 10, 3, this);
            joms_observer.add_action('chat_empty_message_view', this.emptyView, 1, 0, this);
            joms_observer.add_action('chat_seen_message', this.seenMessage, 1, 3, this);
            joms_observer.add_action('chat_remove_seen_message', this.removeSeenMessage, 1, 2, this);
            joms_observer.add_action('chat_previous_messages_loaded', this.previousMessagesLoaded, 1, 2, this);
        },

        render: function () {
            this.$messages.empty().hide();
            this._updateRecallAbility();
        },

        update: function( item ) {
            var participants;

            if ( ! item.active ) {
                return;
            }

            participants = +item.participants;
            if ( item.type !== 'group' ) {
                participants = 1;
            }

            this._toggleEmptyParticipants( participants );
        },

        seenMessage: function( data, me, buddies ) {
            var seen, names, template, html, $seen;

            if ( ! ( _.isArray( data ) && data.length ) ) {
                return;
            }

            seen = _.chain( data )
                .filter(function( item ) { return ( +me.id !== +item.user_id ) })
                .map(function( item ) { return buddies[ item.user_id ] })
                .value();

            if ( ! seen.length ) {
                return;
            }

            // Merge with previous seen users.
            this._seen = _.chain( ( this._seen || [] ).concat( seen ) )
                .uniq(function( item ) { return +item.id })
                .sortBy(function( item ) { return item.name })
                .value();

            // Removes previous seen html.
            $seen = this.$messages.children( '.joms-js--seen' );
            if ( $seen.length ) {
                $seen.remove();
            }

            // Render new seen html.
            template = util.getTemplateById( 'joms-js-template-chat-seen-by' );
            names = _.map( this._seen, function( item ) { return item.name });
            html = template({ seen: this._seen, names: util.formatName( names ) });
            $seen = $( html ).addClass( 'joms-js--seen' );

            this.$messages.append( $seen );
            this.scrollToBottom();
        },

        removeSeenMessage: function() {
            this._seen = false;
            this.$messages.children( '.joms-js--seen' ).remove();
        },

        scrollMessages: function( e ) {
            var height = this.$messages.height();
            var scrollHeight = this.$messages[0].scrollHeight;
            var scrollTop = this.$messages[0].scrollTop;
            var delta = e.originalEvent.deltaY;
            var padding_top = +this.$messages.css('padding-top').replace('px', '');
            var $end, $oldest, oldestId;

            // Reaching the bottom-most of the div.
            if ( scrollTop === ( scrollHeight - height - padding_top ) && delta > 0 ) {
                e.preventDefault();
            }

            // Reaching the top-most of the div.
            if ( scrollTop === 0 && delta < 0 ) {
                e.preventDefault();

                // Load older messages.
                $end = this.$messages.children( '.joms-js--chat-conversation-end' );
                if ( ! $end.length ) {
                    $oldest = this.$messages.children( '.joms-chat__message-item' ).first();
                    if ( ! ( oldestId = $oldest.data( 'id' ) ) ) {
                        $oldest = $oldest.find( '.joms-chat__message-content' ).first();
                        if ( ! ( oldestId = $oldest.data( 'id' ) ) ) {
                            return;
                        }
                    }
                    this.$loading.show();
                    joms_observer.do_action( 'chat_get_previous_messages', null, oldestId );
                }
            }
        },

        emptyView: function () {
            this.$loading.hide();
            this.$messages.empty().show();
        },

        messagesLoading: function () {
            this.$messages.hide();
            this.$loading.show();
        },

        messagesLoaded: function (data, buddies) {
            this.$loading.hide();
            this.$messages.show();

            data.reverse();
            _.each(data, function (item) {
                var user = buddies[item.user_id];
                var time = item.created_at * 1000;
                this.messagesRender(item.id, item.content, JSON.parse(item.attachment), user, time, item.action);
            }, this);
            this._updateRecallAbility();
            this.scrollToBottom();
        },

        messagesRender: function(id, message, attachment, user, timestamp, action) {
            var showTimestamp = +joms_vars.chat_show_timestamp,
                date = new Date( timestamp ),
                $last, template, html, minDiff, name, mine, time, timeFormatted;

            // Format timestamp.
            if ( showTimestamp ) {
                time = '<small style="text-align:center;display:block;">'+ date +'</small>';
                timeFormatted = util.formatDateTime( timestamp );
            }

            mine = +user.id === +window.joms_my_id;
            name = mine ? 'you' : '';

            if ( action === 'sent' ) {

                // Replace newlines.
                message = message.replace( /\\n/g, '<br />' );
                message = message.replace( /\r?\n/g, '<br />' );

                message = message.replace(/((http|https):\/\/.*?[^\s]+)/g, '<a target="_blank" style="text-decoration: underline" href="$1">$1</a>');
                var att = '';
                if (attachment.type) {
                    att = this.attachmentView(attachment);
                }

                $last = this.$messages.find('.joms-chat__message-item').last();

                // Add time separator.
                if ( showTimestamp && ! $last.length ) {
                    template = util.getTemplateById( 'joms-js-template-chat-message-time' );
                    html = template({ time: timeFormatted });
                    this.$messages.append( html );
                }

                if ( ! $last.length || +$last.data( 'user-id' ) !== +user.id ) {
                    template = util.getTemplateById( 'joms-js-template-chat-message' );
                    html = template({
                        timestamp: timestamp,
                        name: name,
                        user_id: user.id,
                        user_name: user.name,
                        user_avatar: user.avatar
                    });

                    $last = $( html );
                    $last.appendTo( this.$messages );
                }

                template = util.getTemplateById( 'joms-js-template-chat-message-content' );
                html = template({
                    message: util.getEmoticon( message ),
                    time: timeFormatted,
                    timestamp: timestamp,
                    id: id,
                    attachment: att,
                    mine: mine
                });
                $last.find( '.joms-js-chat-message-item-body' ).append( html );

            } else if ( action === 'leave' ) {
                template = util.getTemplateById( 'joms-js-template-chat-leave' );
                html = template({
                    id: id,
                    mine: mine,
                    name: user.name,
                    time: timeFormatted
                });
                this.$messages.append( html );
            } else if ( action === 'add' ) {
                template = util.getTemplateById( 'joms-js-template-chat-added' );
                html = template({
                    id: id,
                    mine: mine,
                    name: user.name,
                    time: timeFormatted
                });
                this.$messages.append( html );
            }
        },

        previousMessagesLoaded: function (data, buddies) {
            this.$loading.hide();
            if (!data.length) {
                return;
            }

            _.each(data, function (item) {
                var user = buddies[item.user_id];
                var time = item.created_at * 1000;
                this.preMessagesRender(item.id, item.content, JSON.parse(item.attachment), user, time, item.action);
            }, this);

            this._updateRecallAbility();

            var parent_offset = this.$messages.offset();
            var first_element = data[0];
            var first_item = this.$messages.find('.joms-chat__message-content[data-id="'+first_element.id+'"]');
            var offset = first_item.offset();
            var padding_top = +this.$messages.css('padding-top').replace('px', '');
            this.$messages.scrollTop(offset.top - parent_offset.top - padding_top);
        },

        preMessagesRender: function(id, message, attachment, user, timestamp, action) {
            var showTimestamp = +joms_vars.chat_show_timestamp,
                date = new Date( timestamp ),
                template, html, $first, name, mine, time, timeFormatted;

            // Format timestamp.
            if ( showTimestamp ) {
                time = '<small style="text-align:center;display:block;">'+ date +'</small>';
                timeFormatted = util.formatDateTime( timestamp );
            }

            mine = user && ( +user.id === +window.joms_my_id ) || false;
            name = mine ? 'you' : '';

            if ( action === 'sent' ) {

                // Replace newlines.
                message = message.replace( /\\n/g, '<br />' );
                message = message.replace( /\r?\n/g, '<br />' );

                message = message.replace(/((http|https):\/\/.*?[^\s]+)/g, '<a target="_blank" style="text-decoration: underline" href="$1">$1</a>');

                var att = '';
                if (attachment.type) {
                    att = this.attachmentView(attachment);
                }

                $first = this.$messages.find('.joms-chat__message-item').first();

                if ( ! $first.length || +$first.data( 'user-id' ) !== +user.id ) {
                    template = util.getTemplateById( 'joms-js-template-chat-message' );
                    html = template({
                        timestamp: timestamp,
                        name: name,
                        user_id: user.id,
                        user_avatar: user.avatar
                    });

                    $first = $( html );
                    $first.prependTo( this.$messages );
                }

                template = util.getTemplateById( 'joms-js-template-chat-message-content' );
                html = template({
                    message: util.getEmoticon( message ),
                    time: timeFormatted,
                    timestamp: timestamp,
                    id: id,
                    date: date,
                    attachment: att,
                    mine: mine
                });
                $first.find( '.joms-js-chat-message-item-body' ).prepend( html );

            } else if ( action === 'leave' ) {
                template = util.getTemplateById( 'joms-js-template-chat-leave' );
                html = template({
                    id: id,
                    mine: mine,
                    name: user.name,
                    time: timeFormatted
                });
                this.$messages.prepend( html );
            } else if ( action === 'add' ) {
                template = util.getTemplateById( 'joms-js-template-chat-added' );
                html = template({
                    id: id,
                    mine: mine,
                    name: user.name,
                    time: timeFormatted
                });
                this.$messages.prepend( html );
            } else if ( action === 'end' ) {
                template = util.getTemplateById( 'joms-js-template-chat-message-end' );
                html = template();
                this.$messages.prepend( html );
            }
        },

        messagesReceived: function (data, buddies) {
            if (data.length > 0) {
                _.each(data, function (item) {
                    var user = buddies[item.user_id];
                    var time = item.created_at * 1000;
                    this.messagesRender(item.id, item.content, JSON.parse(item.attachment), user, time, item.action);
                }, this);
                this.scrollToBottom();
            }
        },

        attachmentView: function( attachment ) {
            var type = attachment.type,
                template;

            if ( type === 'file' ) {
                template = util.getTemplateById( 'joms-js-template-chat-message-file' );
                return template({ url: attachment.url, name: attachment.name });
            } else if ( type === 'image' ) {
                template = util.getTemplateById( 'joms-js-template-chat-message-image' );
                return template({ url: attachment.url });
            } else if ( type === 'video' ) {
                template = util.getTemplateById( 'joms-js-template-chat-message-video' );
                return template( $.extend( { url: attachment.url }, attachment.video ) );
            } else if ( type === 'url' ) {
                template = util.getTemplateById( 'joms-js-template-chat-message-url' );
                return template({
                    url: attachment.url,
                    title: attachment.title,
                    images: attachment.images,
                    description: attachment.description
                });
            }
        },

        escapeHtml: function (text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };

            return text.replace(/[&<>"']/g, function (m) {
                return map[m];
            });
        },

        messageAppend: function (message, attachment, me, timestamp) {
            this.messagesRender(null, message, attachment, me, timestamp, 'sent');
        },

        messageSending: function (message, attachment, me, timestamp) {
            message = this.escapeHtml(message);
            this.messageAppend(message, attachment, me, timestamp);
            this.scrollToBottom();

            // Show loading if ajax request is taking too long.
            setTimeout( $.proxy( function() {
                var $msg = this.$messages.find( '.joms-js-chat-content.' + timestamp ),
                    $loading = $msg.siblings( '.joms-js-chat-loading' );

                if ( $loading.length ) {
                    $loading.show();
                }
            }, this ), 1500 );
        },

        messageSent: function ( id, timestamp, attachment ) {
            var $msg = this.$messages.find( '.joms-js-chat-content.' + timestamp ),
                $loading = $msg.siblings( '.joms-js-chat-loading' ),
                $attachment;

            $msg.attr( 'data-id', id );
            $loading.remove();

            // Updates link preview.
            if ( attachment && ( attachment.type === 'url' || attachment.type === 'video' ) ) {
                $attachment = $msg.next( '.joms-js-chat-attachment' );
                if ( $attachment ) {
                    $attachment.remove();
                }
                $attachment = $( this.attachmentView( attachment ) );
                $attachment.insertAfter( $msg );
            }
        },

        recallMessage: function( e ) {
            var $btn = $( e.currentTarget ).closest( '.joms-chat__message-actions' ),
                $msg = $btn.siblings( '.joms-chat__message-content' ),
                $group = $msg.closest( '.joms-chat__message-item' ),
                isMine = +$group.data( 'user-id' ) === +window.joms_my_id,
                id = +$msg.data( 'id' ),
                $prevGroup, $nextGroup;

            e.preventDefault();
            e.stopPropagation();

            if ( isMine ) {
                $msg = $msg.parent();

                if ( $msg.siblings().length ) {
                    $msg.remove();
                } else {
                    $prevGroup = $group.prev();
                    $nextGroup = $group.next();
                    $group.remove();

                    if ( +$prevGroup.data( 'user-id' ) === +$nextGroup.data( 'user-id' ) ) {
                        $prevGroup.find( '.joms-chat__message-body' ).children()
                            .prependTo( $nextGroup.find( '.joms-chat__message-body' ) );
                        $prevGroup.remove();
                    }
                }

                joms_observer.do_action( 'chat_message_recall', id );
            }
        },

        scrollToBottom: function () {
            var div = this.$messages[0];
            div.scrollTop = div.scrollHeight;
        },

        _updateRecallAbility: function() {
            var now = ( new Date() ).getTime(),
                maxElapsed = +joms_vars.chat_recall,
                $btns;

            if ( ! maxElapsed ) {
                return;
            }

            $btns = this.$messages.find( '.joms-chat__message-actions' );
            if ( $btns.length ) {
                maxElapsed = maxElapsed * 60 * 1000;
                $btns.each(function() {
                    var $btn = $( this ),
                        ts = +$btn.parent().data( 'timestamp' );

                    if ( ts && ( now - ts > maxElapsed ) ) {
                        $btn.remove();

                    }
                });
            }

            // Check every 30s.
            clearInterval( this._checkRecallTimer );
            this._checkRecallTimer = setInterval( $.proxy( this._updateRecallAbility, this ), 30 * 1000 );
        },

        _toggleEmptyParticipants: function( count ) {
            if ( count > 0 ) {
                this.$noParticipants.hide();
            } else {
                this.$noParticipants.show();
            }
        }

    });

})( joms_libs.$, joms_libs._, joms_libs.Backbone );
