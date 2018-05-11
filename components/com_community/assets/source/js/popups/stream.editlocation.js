(function( root, $, factory ) {

    joms.popup || (joms.popup = {});
    joms.popup.stream || (joms.popup.stream = {});
    joms.popup.stream.editLocation = factory( root, $ );

    define([ 'utils/popup' ], function() {
        return joms.popup.stream.editLocation;
    });

})( window, joms.jQuery, function( window, $ ) {

var popup, elem, id;

function render( _popup, _id ) {
    if ( elem ) elem.off();
    popup = _popup;
    id = _id;

    joms.ajax({
        func: 'activities,ajaxeditLocation',
        data: [ id ],
        callback: function( json ) {
            popup.items[0] = {
                type: 'inline',
                src: buildHtml( json )
            };

            popup.updateItemHTML();

            elem = popup.contentContainer;
            elem.on( 'click', '[data-ui-object=popup-button-cancel]', cancel );
            elem.on( 'click', '[data-ui-object=popup-button-save]', save );

            _.defer(function() {
                initMap( json );
            });
        }
    });
}

function cancel() {
    elem.off();
    popup.close();
}

function save() {
    var item = elem.find('.joms-js--location-input'),
        name = item.data('name'),
        lat  = item.data('lat'),
        lng  = item.data('lng');

    joms.ajax({
        func: 'activities,ajaxSaveLocation',
        data: [ id, name, lat, lng ],
        callback: function( json ) {
            var stream;

            elem.off();
            popup.close();

            if ( json.success ) {
                stream = $('.joms-stream').filter('[data-stream-id=' + id + ']');
                stream.find('.joms-status-location a').html( name );
            }
        }
    });
}

function initMap( json ) {
    joms.util.map(function() {
        var map = elem.find('.joms-js--location-map'),
            input = elem.find('.joms-js--location-input'),
            loading = elem.find('.joms-js--location-loading'),
            selector = elem.find('.joms-js--location-selector'),
            position, options, map, marker;

        position = new window.google.maps.LatLng( json.latitude, json.longitude );

        options = {
            center: position,
            zoom: 14,
            mapTypeId: window.google.maps.MapTypeId.ROADMAD,
            mapTypeControl: false,
            disableDefaultUI: true,
            draggable: false,
            scaleControl: false,
            scrollwheel: false,
            navigationControl: false,
            streetViewControl: false,
            disableDoubleClickZoom: true
        };

        map = new window.google.maps.Map( map[0], options );
        marker = new window.google.maps.Marker({
            draggable: false,
            map: map
        });

        marker.setPosition( position );
        map.panTo( position );

        input.on( 'input', function() {
            selector.hide()
            loading.show();
            onInput( this.value );
        });

        var onInput = _.debounce(function( keyword ) {
            keyword = $.trim( keyword );
            if ( ! keyword ) {
                return;
            }

            joms.util.map.search( keyword ).done(function( data ) {
                var html = '';
                if ( _.isArray( data ) ) {
                    _.each( data, function( item ) {
                        html += '<a class="joms-map--location-item" data-id="' + item.id + '"">'
                            + '<strong>' + item.name + '</strong><br />'
                            + '<span>' + ( item.description || '' ) + '</span>'
                            + '</a>';
                    });
                }

                loading.hide();
                selector.html( html );
                selector.show();
            });
        }, 1000 );

        input.val( json.location );
        onInput( json.location );

        selector.on( 'click', '.joms-map--location-item', function() {
            var elem = $( this ),
                data = elem.data(),
                name = elem.find( 'strong' ).text(),
                position;

            input.data( 'name', name );
            joms.util.map.detail( data.id ).done(function( place ) {
                var geometry = place.geometry,
                    location = geometry.location,
                    viewport = geometry.viewport;

                input.data( 'lat', location.lat() );
                input.data( 'lng', location.lng() );

                map.setCenter( location );
                marker.setPosition( location );
                if ( viewport ) {
                    map.fitBounds( viewport );
                } else {
                    map.setZoom( 15 );
                }
            });
        });

    });
}

function buildHtml( json ) {
    json || (json = {});

    return [
        '<div class="joms-popup joms-popup--whiteblock">',
        '<div class="joms-popup__title"><button class="mfp-close" type="button" title="Close (Esc)">×</button>', json.title, '</div>',
        '<div>',
            '<div class="joms-popup__content">', ( json.html || json.error ), '</div>',
            '<div class="joms-popup__action">',
            '<a href="javascript:" class="joms-button--neutral joms-button--small joms-left" data-ui-object="popup-button-cancel">', json.btnCancel, '</a> &nbsp;',
            '<button class="joms-button--primary joms-button--small" data-ui-object="popup-button-save">', json.btnEdit, '</button>',
            '</div>',
        '</div>',
        '</div>'
    ].join('');
}

// Exports.
return function( id ) {
    joms.util.popup.prepare(function( mfp ) {
        render( mfp, id );
    });
};

});
