$.ajax({
    type     : 'GET',
    //url      : "https://maps.googleapis.com/maps/api/geocode/json?address=<?php echo $address_city; ?>,+<?php echo $address_state; ?>,+<?php echo '$project->country->name'; ?>&key=AIzaSyD99kTAY6833t9YvRSQCfPmdeP9Hq67W5c",
    url      : "https://maps.googleapis.com/maps/api/geocode/json?address=<?php echo $address_street; ?>,+<?php echo $address_city; ?>,+<?php echo $state_name; ?>&key=AIzaSyD99kTAY6833t9YvRSQCfPmdeP9Hq67W5c",
    datatype : 'json',
    success  : function(data) {
            console.log(data);	
            //console.log(data.results[0]['geometry']['location']);
            if($.isEmptyObject(data.results)) {
                //address_map("<?php echo $state_name; ?>,+<?php echo '$project->country->name'; ?>");
                address_map("<?php echo $state_name; ?>");
            }
            else {
                var lat = data.results[0]['geometry']['location']['lat'];
                var lng = data.results[0]['geometry']['location']['lng'];

                var myLatLng = {lat: lat, lng: lng};
                map.setCenter(myLatLng);
                map.setZoom(3);
                var marker = new google.maps.Marker({
                    map: map,
                    position: myLatLng,
                    title: data.results[0]['formatted_address']
                });

                infowindow = new google.maps.InfoWindow({
                    content: data.results[0].formatted_address
                });

                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map,marker);
                });
            }
    },
    error: function(data) {
        // Error...
        //var errors = $.parseJSON(data.responseText);
        console.log(data);

        //address_map("<?php echo $state_name; ?>,+<?php echo '$project->country->name'; ?>");
        address_map("<?php echo $address_street; ?>,+<?php echo $state_name; ?>");
    }
})


function address_map(address) 
{
    $.ajax({
        type     : 'GET',
        url      : "https://maps.googleapis.com/maps/api/geocode/json?address="+address+"&key=AIzaSyD99kTAY6833t9YvRSQCfPmdeP9Hq67W5c",
        datatype : 'json',
        success  : function(data) {
                //console.log(data);	
                console.log(data.results[0]['geometry']['location']);
                var lat = data.results[0]['geometry']['location']['lat'];
                var lng = data.results[0]['geometry']['location']['lng'];

                var myLatLng = {lat: lat, lng: lng};
                map.setCenter(myLatLng);
                map.setZoom(3);
                var marker = new google.maps.Marker({
                    map: map,
                    position: myLatLng,
                    title: data.results[0]['formatted_address']
                });

                infowindow = new google.maps.InfoWindow({
                    content: data.results[0].formatted_address
                });

                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map,marker);
                });

        },
        error: function(data) {
            // Error...
            //var errors = $.parseJSON(data.responseText);
            console.log(data);
        }
    })
}