  
  var placeSearch, autocomplete;
  
  var map;
  var marker;
  var infowindow;


  var componentForm = {
	street_number: 'short_name',
	route: 'long_name',
	locality: 'long_name',
	administrative_area_level_1: 'short_name',
	administrative_area_level_2: 'short_name',
	postal_code: 'short_name'
  };

  function initialize() {
	  initMap();
	  initAutocomplete();
  }
  
  function initAutocomplete() {
	// Create the autocomplete object, restricting the search to geographical
	// location types.
	autocomplete = new google.maps.places.Autocomplete(
		/** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
		{types: ['geocode'],
			componentRestrictions: {country: 'us'}
		});

	// When the user selects an address from the dropdown, populate the address
	// fields in the form.
	autocomplete.addListener('place_changed', fillInAddress);
}

  function fillInAddress() {
	// Get the place details from the autocomplete object.
	var place = autocomplete.getPlace();

	if (place.geometry.viewport) {
		map.fitBounds(place.geometry.viewport);
	} else {
		map.setCenter(place.geometry.location);
		map.setZoom(17); // Why 17? Because it looks good.
	}
	if (!marker) {
		marker = new google.maps.Marker({
		  map: map,
		  anchorPoint: new google.maps.Point(0, -29)
		});
	} else marker.setMap(null);
	  marker.setOptions({
		position: place.geometry.location,
		map: map
	});
	
	for (var component in componentForm) {
	  document.getElementById(component).value = '';
	  document.getElementById(component).disabled = false;
	}

	// Get each component of the address from the place details
	// and fill the corresponding field on the form.
	var county_name = "";
	for (var i = 0; i < place.address_components.length; i++) {

        var addressType = place.address_components[i].types[0];
        if (componentForm[addressType]) {
			var val = place.address_components[i][componentForm[addressType]];
			document.getElementById(addressType).value = val;
                
            if(addressType == 'administrative_area_level_2') {
            	if(val.slice(-6) == 'County' || val.slice(-6) == 'county') {
                    var county_val = val.slice(0,-7);
                } else {
                    var county_val = val;
                }    
                county_name = county_val;
            }   
        }
	}
	document.getElementById("county_name").value = county_name;
	state_name = $('#administrative_area_level_1').val();
	$('#administrative_area_level_1').trigger('change');
	 
  }

  // Bias the autocomplete object to the user's geographical location,
  // as supplied by the browser's 'navigator.geolocation' object.
  function geolocate() {
	if (navigator.geolocation) {
	  navigator.geolocation.getCurrentPosition(function(position) {
		var geolocation = {
		  lat: position.coords.latitude,
		  lng: position.coords.longitude
		};
		var circle = new google.maps.Circle({
		  center: geolocation,
		  radius: position.coords.accuracy
		});
		autocomplete.setBounds(circle.getBounds());
	  });
	}
  }
  
function initMap() 
{
	var myLatLng = {lat: 41.850033, lng: -87.6500523};
	map = new google.maps.Map(document.getElementById('map_canvas'), {
		zoom: 3,
		center: myLatLng
	});
	
}