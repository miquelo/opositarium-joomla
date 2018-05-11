(function( $, _, Backbone ) {

    /**
     * Conversation sidebar view.
     * @class {Backbone.View}
     */
    module.exports = Backbone.View.extend({

        el: '.joms-chat__messagebox',

        events: {
            'click .joms-js--send': 'messageSend',
            'keydown textarea': 'messageSendOnEnter'
        },

        initialize: function () {
            this.$wrapper = this.$( '.joms-js-wrapper' );
            this.$disabler = this.$( '.joms-js-disabler' );
            this.$textarea = this.$( 'textarea' );
            this.$thumbnail = this.$( '.joms-textarea__attachment--thumbnail' );

            joms_observer.add_action( 'chat_conversation_open', this.render, 10, 2, this );
            joms_observer.add_action( 'chat_conversation_update', this.update, 10, 1, this );
        },

        render: function() {
            this.$textarea.val( '' );
        },

        update: function( item ) {
            if ( ! item.active ) {
                return;
            }

            if ( item.type === 'group' && ! ( +item.participants ) ) {
                this.$disabler.show();
                this.$textarea.attr( 'disabled', 'disabled' );
            } else {
                this.$disabler.hide();
                this.$textarea.removeAttr( 'disabled' )
            }
        },

        messageSend: function( e ) {
            var msg = $.trim( this.$textarea.val() ),
                $draft = $( '.joms-js--chat-item-0 ' ),
                $attachment = jQuery( '.joms-textarea__attachment--thumbnail' ),
                $file = $attachment.children( 'b' ),
                $img = $attachment.children( 'img' );

            // Exit on new message if no user is selected.
            if ( $draft.length && ! $( '.user-selected' ).length ) {
                return;
            }

            // Handle file attachment parameter.
            if ( $file.length ) {
                attachment = {
                    type: 'file',
                    id: $file.data( 'id' ),
                    url: $file.data( 'path' ),
                    name: $file.data( 'name' )
                };
                this.$wrapper.find( '.removeAttachment' ).click();
                $file.remove();

            // Handle image attachment parameter.
            } else if ( $img.attr( 'src' ) ) {
                attachment = {
                    type: 'image',
                    id: $img.data( 'photo_id' ),
                    url: $img.attr( 'src' )
                };
                this.$wrapper.find( '.removeAttachment' ).click();

            // Handle empty attachment.
            } else {
                attachment = '';
            }

            if ( msg || attachment ) {
                joms_observer.do_action( 'chat_messagebox_send', msg, attachment );
                this.$textarea.val( '' );
                e.preventDefault();
            }
        },

        messageSendOnEnter: function( e ) {
            if ( e.which === 13 && e.shiftKey ) {
                this.messageSend( e );
            }
        }
    });

})( joms_libs.$, joms_libs._, joms_libs.Backbone );
