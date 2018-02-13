var proton = {};
var verboseBuild = true;
var screenXs = 480; // JS equivalent to @screen-xs less variable, if you change this in proton-variables.less, you must also change it here
var screenMd = 992; // JS equivalent to @screen-md less variable, if you change this in proton-variables.less, you must also change it here

// Check if browser is internet explorer later than version 9
var ltIE9 = false;
!$('html').is('.lt-ie9') || (ltIE9 = true);
if(ltIE9){
	verboseBuild = false;
}

// Adds iOS modernizr tests
Modernizr.addTest('ipad', function () {
  return !!navigator.userAgent.match(/iPad/i);
});
 
Modernizr.addTest('iphone', function () {
  return !!navigator.userAgent.match(/iPhone/i);
});
 
Modernizr.addTest('ipod', function () {
  return !!navigator.userAgent.match(/iPod/i);
});
 
Modernizr.addTest('appleios', function () {
  return (Modernizr.ipad || Modernizr.ipod || Modernizr.iphone);
});

// add ios-device class to html if ios is used to view
if(Modernizr.appleios){
	$('html').addClass('ios-device');
}

!verboseBuild || console.log('Starting builds:');

