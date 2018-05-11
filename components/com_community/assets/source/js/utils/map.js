define([ 'core' ], function() {

var API_KEY = window.joms_gmap_key,
    $ = jQuery,
    instance;

function GoogleMaps() {
    if ( ! instance ) {
        instance = this;
    }
}

GoogleMaps.prototype = {

    search: function( keyword ) {
        var that = this;

        return $.Deferred(function( defer ) {
            that.autocompleteService().then(function( service ) {
                service.getPlacePredictions({ input: keyword }, function( results, status ) {
                    if ( status === 'OK' ) {
                        defer.resolve( that.mapResult( results ) );
                    } else {
                        defer.reject( status );
                    }
                });
            });
        });
    },

    mapResult: function( results ) {
        var data = [];

        if ( _.isArray( results ) ) {
            _.each( results, function( item ) {
                var id = item.place_id,
                    nameParts = item.description.split( /,\s(.+)?/ ),
                    name = nameParts[0],
                    description = nameParts[1] || '';

                data.push({ id: id, name: name, description: description });
            });
        }

        return data;
    },

    render: function( elem, place ) {
        var $elem = $( elem ).show(),
            map = $elem.data( 'joms-map' ),
            marker = $elem.data( 'joms-map-marker' ),
            name = place.formatted_address || '',
            location, viewport;

        if ( place.geometry ) {
            location = place.geometry.location;
            viewport = place.geometry.viewport;
        } else {
            location = new google.maps.LatLng( place.latitude, place.longitude );
        }

        if ( ! map ) {
            $elem.data( 'joms-map', map = new google.maps.Map( elem, {
                center: location,
                zoom: 15,
                draggable: false,
                scrollwheel: false,
                disableDefaultUI: true
            }) );
        }

        if ( ! marker ) {
            $elem.data( 'joms-map-marker', marker = new google.maps.Marker({
                map: map,
                position: location,
                title: name
            }) );
        }

        map.setCenter( location );
        marker.setPosition( location );
        if ( viewport ) {
            map.fitBounds( viewport );
        } else {
            map.setZoom( 15 );
        }
    },

    loadAPI: function() {
        var that = this;

        return $.Deferred(function( defer ) {
            var script, callback;

            if ( window.google && window.google.maps && window.google.maps.places ) {
                defer.resolve();
                return;
            }

            if ( that.loaded ) {
                defer.resolve();
                return;
            }

            that.queue = that.queue || [];
            that.queue.push( defer );

            if ( that.loading ) {
                return;
            }

            that.loading = true;

            callback = _.uniqueId( 'jomsCallback' );

            script = document.createElement( 'script' );
            script.type = 'text/javascript';
            script.src = 'https://maps.googleapis.com/maps/api/js?libraries=places' +
                ( API_KEY ? ( '&key=' + API_KEY ) : '' ) + '&callback=' + callback;

            window[ callback ] = function() {
                that.loaded = true;
                that.loading = false;
                while ( that.queue.length ) {
                    ( that.queue.shift() ).resolve();
                }
                delete window[ callback ];
            };

            document.body.appendChild( script );
        });
    },

    autocompleteService: function() {
        var that = this;

        return $.Deferred(function( defer ) {
            that.loadAPI().then(function() {
                var cache = '_cacheAutocompleteService';

                that[ cache ] = that[ cache ] || new google.maps.places.AutocompleteService();
                defer.resolve( that[ cache ] );
            });
        });
    },

    placeService: function() {
        var that = this;

        return $.Deferred(function( defer ) {
            that.loadAPI().then(function() {
                var cache = '_cachePlaceService',
                    div = document.createElement( 'div' );

                document.body.appendChild( div );
                that[ cache ] = that[ cache ] || new google.maps.places.PlacesService( div );
                defer.resolve( that[ cache ] );
            });
        });
    },

    placeDetail: function( id ) {
        var that = this;

        return $.Deferred(function( defer ) {
            that.placeService().then(function( service ) {
                var cache = '_cachePlaceDetail';

                that[ cache ] = that[ cache ] || {};

                if ( that[ cache ][ id ] ) {
                    defer.resolve( that[ cache ][ id ] );
                } else {
                    service.getDetails({ placeId: id }, function( place, status ) {
                        if ( status === 'OK' ) {
                            that[ cache ][ id ] = place;
                            defer.resolve( place );
                        } else {
                            defer.reject( status );
                        }
                    });
                }
            });
        });
    }

};

// Export as `joms.util.map`.
joms.util || (joms.util = {});
joms.util.map = function( callback ) {
    var gmap;

    if ( typeof callback !== 'function' ) {
        return;
    }

    gmap = new GoogleMaps();
    gmap.loadAPI().done(function() {
        callback();
    });
};

joms.util.map.search = function( keyword ) {
    return $.Deferred(function( defer ) {
        var gmap = new GoogleMaps();
        gmap.search( keyword ).done(function( results ) {
            defer.resolve( results );
        });
    });
};

joms.util.map.detail = function( id ) {
    return $.Deferred(function( defer ) {
        var gmap = new GoogleMaps();
        gmap.placeDetail( id ).done(function( place ) {
            defer.resolve( place );
        });
    });
};

});
