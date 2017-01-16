/// <reference path="/Includes/jquery-2.0.2-vsdoc.js" />
// NOTE Some bespoke code in getIcon for H and H Events - safe for other events.
var gMap
   ,gDRenderer = new google.maps.DirectionsRenderer({suppressMarkers: true,preserveViewport:true})
   ,gDService = new google.maps.DirectionsService()
   ,gMarkers
   ,infowindow
   ,gMapBounds
   ,postcode;
$(document).ready(function (){
    $.when(applySettings())
     .then(function(mySettings){
         // mysites is populated in map_setup.php
        createMap(mySites);
        if(getFromArray(mySettings,'Show Results','N')=='Y'){writeToMenu('results.php','Results');}
        if(getFromArray(mySettings,'Show Times','N')=='Y'){writeToMenu('times.php','Times');}
    })
});
$(document).on('keyup','#postcode', function(e){
    if(e.which==13) {validatePostcode();}
});
/* ===================================================================== Maps */
function validatePostcode(){
    postcode = $('#postcode').val().toUpperCase();
    $('#postcode').val(postcode);
    if(postcode>"") {gGeocode(postcode);}
}
function gGeocode(vPostcode){
    var gGeocoder = new google.maps.Geocoder();
    var formattedPostcode =vPostcode;
    gGeocoder.geocode({address: vPostcode, region:"GB"}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK && results[0].geometry.location.lat()>5) {
            formattedPostcode = results[0]['address_components'][0]['short_name'];
            var homeLatLng = new google.maps.LatLng(results[0].geometry.location.lat()
                                                   ,results[0].geometry.location.lng());
            gCalculateRoute(homeLatLng);
            gMapBounds.extend(homeLatLng);
            localStorage.setItem('postcode',formattedPostcode);
            $('#postcode').val(formattedPostcode);
            createInfowindow(homeLatLng,formattedPostcode);
        }
    });
}
function gCalculateRoute(homeLatLng){
    var request = {
        origin: homeLatLng,
        destination: mySites[0]['LatLng'],
        travelMode: google.maps.TravelMode.DRIVING
    };
    gDService.route(request,gShowDirections);
}
function gShowDirections(result,status){
    if (status == google.maps.DirectionsStatus.OK) {
        gDRenderer.set('directions', null);
        gDRenderer.setMap(gMap);
        gDRenderer.setDirections(result);
        gMap.fitBounds (gMapBounds);
        var metres = result.routes[0].legs[0].distance.value;
        var seconds = result.routes[0].legs[0].duration.value;
        var miles = Math.floor(metres*0.000621371192);
        var minutes = Math.ceil(seconds/60);
        var hours = Math.floor(minutes/60);
        minutes = minutes - (hours*60);
        var vText=$('#postcode').val() + " to " + mySites[0]['title'] + " is " + miles + " miles, taking ";
        if(hours >1) {vText+=(hours+" hours");}
        if(hours==1) {vText+=(hours+" hour");}
        if(hours >0) {vText+=(" and ");}
        if(minutes >1) {vText+=(minutes+" minutes");}
        if(minutes==1) {vText+=(minutes+" minute");}
        vText+='. Please note that this route may be inappropriate for larger lorries or vehicles with trailers.';
        $('#dMapMessage').html(vText);
    } else {
        $('#dMapMessage').html("We were unable to look up your route at this time");
    }
}
function createMap(mySites){
    var mapExists = false;
    var myEvent;
    $.each(mySites,function(a,b){
        if(!mapExists){
            var mapOptions = {
                streetViewControl: false,
                panControl: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoom: 10,
                center: b.LatLng
            };
            gMap = new google.maps.Map(document.getElementById('gMap'), mapOptions);
            gMapBounds = new google.maps.LatLngBounds();
            mapExists = true;
            google.maps.event.addDomListener(window, 'resize', function() {
                gMap.fitBounds (gMapBounds);
            });
        }
        gMapBounds.extend(b.LatLng);
        setMarker(b.LatLng,b.title,getIcon(b.title));
        myEvent=b;
    });
    $('#postcode').val(localStorage.getItem('postcode'));
    validatePostcode(myEvent);
}
function getIcon(iEvent){
    var image = "http://maps.google.com/mapfiles/marker" + iEvent.substring(0,1) + ".png";
    if(iEvent == "Howick"){
        image = {
            url: '/images/Howick.png',
            size: new google.maps.Size(60,60),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(60, 60)
        }
    }
    if(iEvent == "Mount Ballan"){
        image = {
            url: '/images/MountBallan.png',
            size: new google.maps.Size(100,32),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(100, 32)
        }
    }
    if(iEvent == "Chepstow"){
        image = {
            url: '/images/Chepstow.png',
            size: new google.maps.Size(60,60),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(0,60)
        }
    }
    return image;
}
function setSimpleMarker(position){
    gMarkers = new google.maps.Marker({
         position: position
        ,map: gMap
    });
}
function setMarker(position,title,image){
    var marker = new google.maps.Marker({
            position: position,
            map: gMap,
            icon: image
        });
}
function getInfoCallback(position,content) {
    return function() {
        infowindow.setPosition(position);
        infowindow.setContent(content);
        infowindow.open(gMap, this);
    };
}
function createInfowindow(position,title){
    var content = '<div class="infoWindow myButton">'+title+'</div>';
    var infowindow = new google.maps.InfoWindow({content:content,position:position});
    infowindow.open(gMap);
}    
    