(function( _ ) {

    var lang = window.joms_lang && joms_lang.date || {},
        moment = require( 'moment' ),
        templatesCache = {};

    moment.defineLocale( 'jomsocial', {
        parentLocale: 'en',
        months: lang.months,
        monthsShort: _.map( lang.months, function( s ) { return s.substr( 0, 3 ); }),
        weekdays: lang.days,
        weekdaysShort: _.map( lang.days, function( s ) { return s.substr( 0, 3 ); }),
        weekdaysMin: _.map( lang.days, function( s ) { return s.substr( 0, 2 ); })
    });

    module.exports = {

        /**
         * Underscore template wrapper.
         * @param {String} templateString
         * @return {Function}
         */
        template: function( templateString, settings ) {
            return _.template( templateString, {
                variable: 'data',
                evaluate: /\{\{([\s\S]+?)\}\}/g,
                interpolate: /\{\{=([\s\S]+?)\}\}/g,
                escape: /\{\{-([\s\S]+?)\}\}/g
            } );
        },

        /**
         * Get template already defined in the HTML document.
         * @param {String} id
         * @return {Function}
         */
        getTemplateById: function( id ) {
            var template = templatesCache[ id ];

            if ( ! template ) {
                template = document.getElementById( id ).innerText;
                // HACK: Joomla (or is it the browser?) is automatically added relative path after an `src="` string. Duh!
                template = template.replace( /(src|href)="[^"]+\{\{/g, '$1="{{' );
                template = templatesCache[ id ] = this.template( template );
            }

            return template;
        },

        /**
         * Format timestamp to a human-readable date string.
         * @param {Number} timestamp
         * @return {String}
         */
        formatDate: function( timestamp ) {
            var now = moment(),
                date = moment( timestamp ),
                format = 'D MMM';

            if ( now.year() !== date.year() ) {
                format = 'D/MMM/YY';
            }

            return date.format( format );
        },

        /**
         * Format timestamp to a human-readable time string.
         * @param {Number} timestamp
         * @return {String}
         */
        formatTime: function( timestamp ) {
            var time = moment( timestamp ),
                format = joms_vars.chat_time_format || 'g:i A';

            // PHP-to-Moment time format conversion.
            format = format
                .replace( /[GH]/g, 'H' )
                .replace( /[gh]/g, 'h' )
                .replace( /i/ig, 'mm' )
                .replace( /s/ig, 'ss' );

            return time.format( format );
        },

        /**
         * Format timestamp to a human-readable datetime string.
         * @param {Number} timestamp
         * @return {String}
         */
        formatDateTime: function( timestamp ) {
            var dateStr = this.formatDate( timestamp ),
                timeStr = this.formatTime( timestamp );

            return dateStr + ' ' + timeStr;
        },

        /**
         * Format name to proper punctuation.
         * @param {String|String[]} names
         * @return {String}
         */
        formatName: function( names ) {
            if ( ! _.isArray( names ) ) {
                names = [ names ];
            }

            if ( names.length === 1 ) {
                return names[0];
            }

            if ( names.length > 1 ) {
                names = _.map( names, function( str ) {
                    str = str.split( ' ' );
                    return str[0];
                });
                names = names.sort();
                names = names.join( ', ' );
                names = names.replace( /,\s([^\s]*)$/, ' ' + joms_vars.chat_text_and + ' $1' );
                return names;
            }

            return '';
        },

        /**
         * Convert emoticon code into actual emoticon.
         * @param {String} str
         * @return {String}
         */
        getEmoticon: function( str ) {
            var emoticons = {
                happy2    : /(:happy:|:\)\))/g,         // [ ':happy:', ':))' ]
                smiley2   : /(:smile:|:\)|:-\))/g,      // [ ':smile:', ':)', ':-)' ]
                tongue2   : /(:tongue:|:p|:P)/g,        // [ ':tongue:', ':p', ':P' ]
                wink2     : /(:wink:|;\))/g,            // [ ':wink:', ';)' ]
                cool2     : /(:cool:|B\))/g,            // [ ':cool:', 'B)' ]
                angry2    : /(:angry:|>:\(|&gt;:\()/g,  // [ ':angry:', '>:(', '&gt;:(' ]
                sad2      : /(:sad:|:\()/g,             // [ ':sad:', ':(' ]
                evil2     : /(:evil:|>:D|&gt;:D)/g,     // [ ':evil:', '>:D', '&gt;:D' ]
                grin2     : /(:grin:|:D)/g,             // [ ':grin:', ':D' ]
                shocked2  : /(:shocked:|:o|:O)/g,       // [ ':shocked:', ':o', ':O' ]
                confused2 : /(:confused:|:\?)/g,        // [ ':confused:', ':?' ]
                neutral2  : /(:neutral:|:\|)/g,         // [ ':neutral:', ':|' ]
                heart     : /(:love:|<3|&lt;3)/g        // [ ':love:', '<3', '&lt;3' ]
            };

            _.each( emoticons, function( regex, key ) {
                var replace = '<i class="joms-status-emoticon joms-icon-' + key + '"></i>';
                str = str.replace( regex, replace );
            });

            return str;
        }

    };

})( joms_libs._ );
