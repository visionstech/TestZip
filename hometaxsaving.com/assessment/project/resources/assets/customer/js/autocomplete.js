

   // This example displays an address form, using the autocomplete feature
      // of the Google Places API to help users fill in the information.

      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

	  
	  
	  
    var placeSearch, autocomplete;

    var state_name = '';
    var county_name = '';
    var same_state = '0';
    
    state_name = $('#administrative_area_level_1').val();
    county_name = $('#administrative_area_level_2').val();
    
    console.log(county_name);

    var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        administrative_area_level_2: 'long_name',
        postal_code: 'short_name'
    };

	  	  
    function initAutocomplete() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */
            (document.getElementById('autocomplete')),
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
		
        for (var component in componentForm) {
            document.getElementById(component).value = '';
            document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                document.getElementById(addressType).value = val;
                if(addressType == 'administrative_area_level_2') {
                    if(val.slice(-6) == 'County' || val.slice(-6) == 'county') {
                        var county_val = val.slice(0,-7);
                    }
                    else {
                        var county_val = val;
                    }    
                    //console.log(val);
                    console.log(county_val);
                    //$('#administrative_area_level_2').val(county_val);
                    county_name = county_val;
                    //console.log(document.getElementById(addressType));
                    //$('#administrative_area_level_2').val(county_val);
                    //document.getElementById(addressType).value = county_val;
                    //console.log('county if '+$('#administrative_area_level_2').val());
                }   
            }
        }
        document.getElementById("county_id").value = county_name;
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
	  
	 
    