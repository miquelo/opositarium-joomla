(function( $, _, Backbone, observer ) {

    var util = require( './util' );

    /**
     * Conversation notification view.
     * @class
     */
    function Notification() {
        $( $.proxy( this.initialize, this ) );
    }

    Notification.prototype = {

        initialize: function() {
            this.$el = $( '.joms-js--notification-chat-list' ).add( '.joms-js--notification-chat-list-mobile' );
            this.$popover = $( '.joms-popover--toolbar-chat' );
            this.$counter = $( '.joms-js--notiflabel-chat' );

            observer.add_action( 'chat_conversation_render', this.render, 10, 1, this );
            observer.add_action( 'chat_set_notification_label', this.updateCounter, 10, 1, this );
            observer.add_action( 'chat_set_notification_label_seen', this.markItemAsRead, 10, 1, this );
            observer.add_action( 'chat_set_notification_label_unread', this.markItemAsUnread, 10, 1, this );
            observer.add_action( 'chat_move_notification_to_top', this.moveItemToTop, 10, 1, this );
            observer.add_action( 'chat_removemove_notification', this.removeItem, 10, 1, this );

            $( document ).on( 'click', '.joms-js-chat-notif', $.proxy( this.onItemClick, this ) );
        },

        render: function( data ) {
            var html = '',
                template;

            if ( ! ( template = this._renderTemplate ) ) {
                template = this._renderTemplate = util.template( joms_vars.chat_template_notification_item );
            }

            data = $.extend( {}, data || {} );

            _.each( data, function( item ) {
                item.name = util.formatName( item.name );

                // Normalize avatar url.
                if ( item.thumb && ! item.thumb.match( /^https?:\/\//i ) ) {
                    item.thumb = joms_vars.chat_base_uri + item.thumb;
                }

                html += template( item );
            }, this );

            this.$popover.prepend( html );
            this.$popover.children( '.joms-js--empty' ).remove();
        },

        updateCounter: function( newValue ) {
            var oldValue = +this.$counter.text();
            if ( +newValue !== oldValue ) {
                this.$counter.text( +newValue || '' );
            }
        },

        markItemAsRead: function( id ) {
            this.$popover.find( '.joms-js-chat-notif-' + id ).removeClass( 'unread' );
        },

        markItemAsUnread: function( id ) {
            this.$popover.find( '.joms-js-chat-notif-' + id ).addClass( 'unread' );
        },

        moveItemToTop: function( list ) {
            _.each( list, function( item ) {
                this.$popover.each(function() {
                    $( this ).prepend( $( this ).find( '.joms-js-chat-notif-' + item.chat_id ) );
                });
            }, this );
        },

        removeItem: function( id ) {
            this.$popover.find( '.joms-js-chat-notif-' + id ).remove();
        },

        onItemClick: function( e ) {
            var $item = $( e.currentTarget ),
                id = $item.data( 'chat-id' ),
                $popover;

            e.preventDefault();
            e.stopPropagation();

            if ( typeof window.joms_vars !== 'undefined' && joms_vars.is_chat_view ) {
                $popover = $( '.joms-popover--toolbar-chat' );
                $popover.hide();
                $popover.closest( '.joms-popup__wrapper' ).click();
                observer.do_action( 'chat_open_window_by_chat_id', id );
                observer.do_action( 'chat_sidebar_select', id );
                observer.do_action( 'chat_set_location_hash', id );
                return;
            }

            window.location = joms_vars.chat_uri + '#' + id;
        }

    };

    module.exports = Notification;

})( joms_libs.$, joms_libs._, joms_libs.Backbone, joms_observer );
