(function( root, $, _, factory ) {

    var LocationAutocomplete = factory( root, $, _ );

    define([ 'utils/map' ], function() {
        return LocationAutocomplete;
    });

    // make jQuery plugin
    $.fn.locationAutocomplete = function() {
        return this.each(function() {
            var key = 'location-autocomplete',
                $el = $( this );
            if ( ! $el.data( key ) ) {
                $el.data( key, new LocationAutocomplete( this ));
            }
        });
    };

    // detect input on page load
    $(function() {
        $('.joms-input--location').locationAutocomplete();
    });

})( window, joms.jQuery, window._, function( window, $, _ ) {

function LocationAutocomplete( el ) {
    this.$input = $( el );

    this.$wrapper = this.$input.parent('.joms-location__wrapper');
    this.$inputDesc = this.$input.siblings('.js-desc');
    this.$inputLat = this.$input.siblings('.js-lat');
    this.$inputLng = this.$input.siblings('.js-lng');
    this.$description = this.$input.siblings('.joms-location__description');
    this.$dropdown = this.$input.siblings('.joms-location__dropdown');
    this.$loading = this.$dropdown.find('.joms-location__loading');
    this.$result = this.$dropdown.find('.joms-location__result');
    this.$header = this.$dropdown.find('.joms-location__header');
    this.$map = this.$dropdown.find('.joms-location__map');
    this.$list = this.$dropdown.find('.joms-location__list');

    this.$input.on('focus.joms-location', $.proxy( this.focus, this ));
    this.$input.on('blur.joms-location', $.proxy( this.hide, this ));
    this.$input.on('input.joms-location', $.proxy( this.search, this ));
    this.$dropdown.on('mousedown', '.joms-location__list a', $.proxy( this.select, this ));
    this.$dropdown.on('click', '.joms-location__close', $.proxy( this.hide, this ));
}

LocationAutocomplete.prototype.show = function() {
    this.$dropdown.show();
};

LocationAutocomplete.prototype.hide = function() {
    this.$dropdown && this.$dropdown.hide();
};

LocationAutocomplete.prototype.focus = function() {
    this._search_success && this.show();
};

LocationAutocomplete.prototype.search = function() {
    var query = $.trim( this.$input.val() );
    this.$wrapper.css('display', 'inline-block');
    this.$description.html( this.$description.data('tips') );
    if ( query ) {
        this.$result.hide();
        this.$loading.show();
        this.$dropdown.show();
        this._search( query );
    }
    setTimeout( $.proxy(function() {
        this.$wrapper.css('display', '');
    }, this ), 0 );
};

LocationAutocomplete.prototype._search = _.debounce(function( query ) {
    this._get_autocomplete_service().done(function( service ) {
        service.getPlacePredictions({ input: query }, $.proxy(function( results, status ) {
            this.$loading.hide();
            this.$result.show();
            if ( status === 'OK' ) {
                this.$list.html( this._format_result( results ));
                this._search_success = true;
            }
        }, this ));
    });
}, 1000 );

LocationAutocomplete.prototype.select = function( e ) {
    var $item = $( e.currentTarget ),
        name = $item.find('.js-name').text(),
        desc = $item.find('.js-desc').text(),
        placeId = $item.data('place-id');

    e.preventDefault();
    e.stopPropagation();

    if ( placeId ) {
        this.$input.val( name );
        this.$inputDesc.val( desc );
        this.$description.html( desc );
        this._get_location_detail( placeId ).done(function( place ) {
            var loc = place.geometry.location;
            this.$inputLat.val( loc.lat() );
            this.$inputLng.val( loc.lng() );
            this._render_map( this.$map[0], place );
        });
    }
};

LocationAutocomplete.prototype._load_library = function() {
    return $.Deferred( $.proxy(function( defer ) {
        joms.util.map( $.proxy(function() {
            defer.resolveWith( this, [ window.google ]);
        }, this ));
    }, this ));
};

LocationAutocomplete.prototype._get_autocomplete_service = function() {
    return $.Deferred( $.proxy(function( defer ) {
        if ( this._autocomplete_service ) {
            defer.resolveWith( this, [ this._autocomplete_service ]);
        } else {
            this._load_library().done(function( google ) {
                this._autocomplete_service = new google.maps.places.AutocompleteService();
                defer.resolveWith( this, [ this._autocomplete_service ]);
            });
        }
    }, this ));
};

LocationAutocomplete.prototype._get_location_service = function() {
    return $.Deferred( $.proxy(function( defer ) {
        if ( this._location_service ) {
            defer.resolveWith( this, [ this._location_service ]);
        } else {
            this._load_library().done(function( google ) {
                var div = document.createElement('div');
                document.body.appendChild( div );
                this._location_service = new google.maps.places.PlacesService( div );
                defer.resolveWith( this, [ this._location_service ]);
            });
        }
    }, this ));
};

LocationAutocomplete.prototype._get_location_detail = function( id ) {
    return $.Deferred( $.proxy(function( defer ) {
        if ( this._location_cache && this._location_cache[ id ] ) {
            defer.resolveWith( this, [ this._location_cache[ id ] ]);
        } else {
            this._get_location_service().done(function( service ) {
                service.getDetails({ placeId: id }, $.proxy(function( place, status ) {
                    if ( status === 'OK' ) {
                        this._location_cache || (this._location_cache = {});
                        this._location_cache[ id ] = place;
                        defer.resolveWith( this, [ place ]);
                    } else {
                        defer.rejectWith( this, [ status ]);
                    }
                }, this ));
            });
        }
    }, this ));
};

LocationAutocomplete.prototype._render_map = function( div, place ) {
    var location = place.geometry.location,
        viewport = place.geometry.viewport,
        map, marker;

    div = $( div ).show();
    map = div.data('joms-map');
    marker = div.data('joms-map-marker');

    if ( !map ) {
        map = new window.google.maps.Map( div[0], {
            center: location,
            zoom: 15,
            draggable: false,
            scrollwheel: false,
            disableDefaultUI: true
        });
        div.data('joms-map', map );
    }

    if ( !marker ) {
        marker = new window.google.maps.Marker({
            position: location,
            map: map,
            title: 'You are here (more or less)'
        });
        div.data('joms-map-marker', marker );
    }

    map.setCenter( location );
    marker.setPosition( location );
    if ( viewport ) {
        map.fitBounds( viewport );
    } else {
        map.setZoom( 15 );
    }
};

LocationAutocomplete.prototype._format_result = function( results ) {
    var html = [],
        template, description, item, i;

    template = [
        '<a href="javascript:" data-place-id="{place_id}">',
        '<span class="joms-location__name js-name">{name}</span>',
        '<span class="joms-location__desc js-desc">{description}</span>',
        '</a>'
    ].join('');

    for ( i = 0; i < results.length; i++ ) {
        description = results[i].description;
        description = description.split(/,\s(.+)?/);
        item = template
            .replace('{place_id}', results[ i ].place_id )
            .replace('{name}', description[0] )
            .replace('{description}', description[1] || '&nbsp;' );
        html.push( item );
    }
    return html.join('');
};

return LocationAutocomplete;

});
