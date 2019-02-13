function organic_beauty_googlemap_init(dom_obj, coords) {
	"use strict";
	if (typeof ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'] == 'undefined') organic_beauty_googlemap_init_styles();
	ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'].geocoder = '';
	try {
		var id = dom_obj.id;
		ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id] = {
			dom: dom_obj,
			markers: coords.markers,
			geocoder_request: false,
			opt: {
				zoom: coords.zoom,
				center: null,
				scrollwheel: false,
				scaleControl: false,
				disableDefaultUI: false,
				panControl: true,
				zoomControl: true, //zoom
				mapTypeControl: false,
				streetViewControl: false,
				overviewMapControl: false,
				styles: ORGANIC_BEAUTY_STORAGE['googlemap_styles'][coords.style ? coords.style : 'default'],
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
		};

		organic_beauty_googlemap_create(id);

	} catch (e) {
        if (window.dcl!==undefined)
            dcl(ORGANIC_BEAUTY_STORAGE['strings']['googlemap_not_avail']);

	}
}

function organic_beauty_googlemap_create(id) {
	"use strict";

	// Create map
	ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].map = new google.maps.Map(ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].dom, ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].opt);

	// Add markers
	for (var i in ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers)
		ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].inited = false;
	organic_beauty_googlemap_add_markers(id);
	
	// Add resize listener
	jQuery(window).resize(function() {
		if (ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].map)
			ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].map.setCenter(ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].opt.center);
	});
}

function organic_beauty_googlemap_add_markers(id) {
	"use strict";

    for (var i in ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers) {
		
		if (ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].inited) continue;
		
		if (ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].latlng == '') {
			
			if (ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].geocoder_request!==false) continue;
			
			if (ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'].geocoder == '') ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'].geocoder = new google.maps.Geocoder();
			ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].geocoder_request = i;
			ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'].geocoder.geocode({address: ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].address}, function(results, status) {
				"use strict";
				if (status == google.maps.GeocoderStatus.OK) {
					var idx = ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].geocoder_request;
					if (results[0].geometry.location.lat && results[0].geometry.location.lng) {
						ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[idx].latlng = '' + results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng();
					} else {
						ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[idx].latlng = results[0].geometry.location.toString().replace(/\(\)/g, '');
					}
					ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].geocoder_request = false;
					setTimeout(function() { 
						organic_beauty_googlemap_add_markers(id); 
						}, 200);
				} else {
                    if (window.dcl!==undefined)
                        dcl(ORGANIC_BEAUTY_STORAGE['strings']['geocode_error'] + ' ' + status);
				}

			});
		
		} else {
			
			// Prepare marker object
			var latlngStr = ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].latlng.split(',');
			var markerInit = {
				map: ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].map,
				position: new google.maps.LatLng(latlngStr[0], latlngStr[1]),
				clickable: ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].description!=''
			};
			if (ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].point) markerInit.icon = ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].point;
			if (ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].title) markerInit.title = ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].title;
			ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].marker = new google.maps.Marker(markerInit);
			
			// Set Map center
			if (ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].opt.center == null) {
				ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].opt.center = markerInit.position;
				ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].map.setCenter(ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].opt.center);				
			}
			
			// Add description window
			if (ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].description!='') {
				ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].infowindow = new google.maps.InfoWindow({
					content: ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].description
				});
				google.maps.event.addListener(ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].marker, "click", function(e) {
					var latlng = e.latLng.toString().replace("(", '').replace(")", "").replace(" ", "");
					for (var i in ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers) {
						if (latlng == ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].latlng) {
							ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].infowindow.open(
								ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].map,
								ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].marker
							);
							break;
						}
					}
				});
			}
			
			ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'][id].markers[i].inited = true;
		}
	}
}

function organic_beauty_googlemap_refresh() {
	"use strict";
	for (var id in ORGANIC_BEAUTY_STORAGE['googlemap_init_obj']) {
		organic_beauty_googlemap_create(id);
	}
}

function organic_beauty_googlemap_init_styles() {
	// Init Google map
	ORGANIC_BEAUTY_STORAGE['googlemap_init_obj'] = {};
	ORGANIC_BEAUTY_STORAGE['googlemap_styles'] = {
		'default': []
	};
	if (window.organic_beauty_theme_googlemap_styles!==undefined)
		ORGANIC_BEAUTY_STORAGE['googlemap_styles'] = organic_beauty_theme_googlemap_styles(ORGANIC_BEAUTY_STORAGE['googlemap_styles']);
}