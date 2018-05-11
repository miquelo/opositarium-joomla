(function( $, _, Backbone ) {

    var util = require( './util' );

    /**
     * Conversation sidebar view.
     * @class {Backbone.View}
     */
    module.exports = Backbone.View.extend({

        el: '.joms-chat__conversations-wrapper',

        events: {
            'click .joms-chat__item': 'itemSelect',
            'wheel .joms-js-list': 'scrollSidebar',
            'keyup .joms-chat__search_conversation': 'searchConversation'
        },

        initialize: function () {
            this.$loading = this.$('.joms-js-loading');
            this.$list = this.$('.joms-js-list');
            this.$notice = this.$('.joms-js-notice');
            this.$searchInput = this.$('.joms-chat__search_conversation');

            joms_observer.add_action('chat_user_login', this.userLogin, 10, 1, this);
            joms_observer.add_action('chat_user_logout', this.userLogout, 10, 1, this);
            joms_observer.add_action('chat_conversation_render', this.renderListConversation, 1, 1, this);
            joms_observer.add_action('chat_conversation_open', this.conversationOpen, 10, 1, this);
            joms_observer.add_action('chat_update_preview_message', this.updatePreviewMessage, 10, 5, this);
            joms_observer.add_action('chat_highlight_unread_windows', this.hightlighUnreadWindows, 1, 1, this);
            joms_observer.add_action('chat_hightlight_active_window', this.highlightActiveWindow, 1, 1, this);
            joms_observer.add_action('rename_chat_title', this.renameChatTitle, 1, 1, this);
            joms_observer.add_action('chat_override_draft_chat_window', this.overrideDraftChatWindow, 1, 1, this);
            joms_observer.add_action('chat_remove_draft_conversation', this.removeDraftConversation, 1, 0, this);
            joms_observer.add_action('chat_open_first_window', this.openFirstWindow, 1, 0, this);
            joms_observer.add_action('chat_render_draft_conversation', this.renderDraftConversation, 1, 1, this);
            joms_observer.add_action('chat_open_window_by_chat_id', this.openWindowByChatId, 1, 1, this);
            joms_observer.add_action('chat_set_window_seen', this.setWindowSeen, 1, 1, this);
            joms_observer.add_action('chat_move_window_to_top', this.moveWindowToTop, 1, 1, this);
            joms_observer.add_action('chat_remove_window', this.removeWindow, 1, 1, this);
            joms_observer.add_action('chat_mute', this.muteChat, 1, 1, this);
        },

        /**
         * Update sidebar on login event.
         */
        userLogin: function () {
            this.$loading.hide();
            this.$notice.hide();
            this.$list.show();
        },

        /**
         * Update sidebar on logout event.
         */
        userLogout: function () {
            this.$loading.hide();
            this.$list.hide();
            this.$notice.show();
        },

        searchConversation: function(e) {
            var keyword = this.$searchInput.val().toLowerCase();
            if (!keyword) {
                this.$list.find('.joms-chat__item').show();
                return;
            }

            if (e.which < 112 && e.which > 47 || e.which === 8 || e.which === 16) {
                var items = this.$list.find('.joms-chat__item');
                items.hide();
                _.each(items, function(item) {
                    var name = $(item).find('a').text().toLowerCase();
                    if (name.indexOf(keyword) > -1) {
                        $(item).show();
                    }
                });
            }
        },

        scrollSidebar: function(e) {
            var height = this.$list.height();
            var scrollHeight = this.$list[0].scrollHeight;
            var scrollTop = this.$list[0].scrollTop;
            var delta = e.originalEvent.deltaY;
            if((scrollTop === (scrollHeight - height) && delta > 0)) {
                e.preventDefault();
            }

            if (scrollTop === 0 && delta < 0) {
                e.preventDefault();
            }
        },

        muteChat: function(mute) {
            var mute_icon = [
                '<div class="joms-chat__item-actions">',
                    '<svg viewBox="0 0 16 16" class="joms-icon">',
                      '<use xlink:href="#joms-icon-close"></use>',
                    '</svg>',
                '</div>'
            ].join('');
            var active = this.$list.find('.active');
            if (mute) {
                active.find('.joms-chat__item-actions').remove();
            } else {
                active.append(mute_icon);
            }
        },

        removeWindow: function(chat_id) {
            this.$list.find('.joms-js--chat-item-'+chat_id).remove();
        },

        moveWindowToTop: function(list) {
            for (var i = 0; i < list.length; i++) {
                var item = this.$list.find('.joms-js--chat-item-'+list[i].chat_id);
                this.$list.prepend(item);
            }
        },

        setWindowSeen: function(chat_id) {
            this.$list.find('.joms-js--chat-item-'+chat_id).removeClass('unread');
        },

        renderDraftConversation: function( data ) {
            var template = util.getTemplateById( 'joms-js-template-chat-sidebar-draft' ),
                html = template();

            this.$list.prepend( html );
        },

        openFirstWindow: function () {
            var item = this.$list.find('.joms-chat__item').first(),
                chat_id = item.data('chat-id');
            if (chat_id) {
                this.itemSetActive(item);
                joms_observer.do_action('chat_sidebar_select', item.data('chat-id'));
            }
        },

        openWindowByChatId: function(chat_id) {
            var item = this.$list.find('.joms-js--chat-item-'+chat_id);
            this.itemSetActive(item);
            joms_observer.do_action('chat_sidebar_select', chat_id);
        },

        removeDraftConversation: function () {
            this.$list.find('.joms-js--chat-item-0').remove();
        },

        overrideDraftChatWindow: function (data) {
            var item = $(this.$list.find('.active')),
                avatar = item.find('.joms-avatar img');
            item.attr('data-chat-type', data.type);
            item.attr('data-chat-id', data.chat_id);
            item.removeClass('joms-js--chat-item-0').addClass('joms-js--chat-item-' + data.chat_id);
            avatar.attr('src', data.thumb);
        },

        renameChatTitle: function (name) {
            var item = this.$list.find('.active').find('.joms-chat__item-body a');
            item.text(name);
        },

        /**
         * Render all conversation items.
         * @param {object[]} data
         */
        renderListConversation: function( data ) {
            var $startScreen = $('.joms-js-page-chat-loading'),
                $chatScreen = $('.joms-js-page-chat'),
                key;

            if ( $chatScreen.is(':hidden') ) {
                $chatScreen.show();
                $startScreen.hide();
            }

            for (key in data) {
                this.render(data[key]);
            }
        },

        /**
         * Render a conversation item.
         * @param {object} data
         */
        render: function( data ) {
            var template = util.getTemplateById( 'joms-js-template-chat-sidebar-item' ),
                isActive = false,
                isUnread = ! ( +data.seen ),
                html, $item;

            // Check if item is already exist.
            $item = this.$list.children( '.joms-js--chat-item-' + data.chat_id );
            if ( $item.length && $item.hasClass( 'active' ) ) {
                isActive = true;
                isUnread = false;
            }

            // Generate html from template.
            html = template({
                id: data.chat_id,
                type: data.type,
                name: util.formatName( data.name ),
                unread: isUnread,
                active: isActive,
                avatar: data.thumb
            });

            if ( $item.length ) {
                $item.replaceWith( html );
            } else {
                this.$list.append( html );
            }
        },

        prependRender: function (data) {
            var template, html;
            template = typeof window.joms_vars.chat_page_list === 'string' && window.joms_vars.chat_page_list || '';
            html = template
                .replace(/##type##/g, data.type)
                .replace(/##chat_id##/g, data.chat_id)
                .replace(/##name##/g, data.name)
                .replace(/##thumb##/g, data.thumb)
                .replace(/##unread##/g, '')
                .replace(/##mute##/g, '');
            this.$list.prepend(html);
        },

        /**
         * Show particular conversation item.
         * @param {HTMLEvent} e
         */
        itemSelect: function (e) {
            e.preventDefault();
            var $item = $(e.currentTarget),
                chatId = $item.data('chat-id');
            this.itemSetActive($item);
            if (this.$searchInput.val()) {
                this.$searchInput.val('');
                this.$list.find('.joms-chat__item').show();
            }
            joms_observer.do_action('chat_sidebar_select', chatId);
            if (chatId > 0) {
                joms_observer.do_action('chat_selector_hide');
            } else {
                joms_observer.do_action('chat_selector_show');
            }
        },

        /**
         * Set active item on conversation open.
         * @param {jQuery} $item
         */
        itemSetActive: function ($item) {
            $item.siblings('.active').removeClass('active');
            $item.removeClass('unread').addClass('active');
        },

        /**
         * Handle open conversation.
         * @param {number} userId
         */
        conversationOpen: function (chatId) {
            var $item = this.$list.find('.joms-js--chat-item-' + chatId);
            if ($item.length) {
                this.itemSetActive($item);
            }
        },

        /**
         * Change display message below avatar.
         * @param {object} message
         * @param {object} active
         */
        updatePreviewMessage: function (message, active) {
            var $item;
            if (active && active.user_id) {
                $item = this.$list.find('.joms-js--chat-item-user-' + active.user_id);
                if ($item.length) {
                    $item.find('.joms-js--chat-item-msg').text(message);
                }
            }
        },

        /**
         * Highlight active sidebar item.
         * @param {Number} chat_id
         */
        highlightActiveWindow: function( chat_id ) {
            var $item = this.$list.find( '.joms-js--chat-item-' + chat_id );
            this.itemSetActive( $item );
        },

        /**
         * Highlight unread sidebar items.
         * @param {Object[]} data
         */
        hightlighUnreadWindows: function( data ) {
            _.each( data, function( item ) {
                var $item = this.$( '.joms-js--chat-item-' + item.chat_id );
                if ( ! $item.hasClass( 'active' ) ) {
                    $item.addClass( 'unread' );
                }
            }, this );
        }

    });

})( joms_libs.$, joms_libs._, joms_libs.Backbone );
