var lat;
var long;
function getLoc(){
	if(navigator.geolocation) {
	   navigator.geolocation.getCurrentPosition( function (position) {
	   lat = position.coords.latitude;
	   long = position.coords.longitude;
	   $('#ibw_websitebundle_stairsactivitytype_lat').val(lat);
	   $('#ibw_websitebundle_stairsactivitytype_lng').val(long);
	   });
	} 
	else {
		alert('The browser does not support geolocation');
	}
}