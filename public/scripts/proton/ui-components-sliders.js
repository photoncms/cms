$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.uiComponentsSliders build');
    
    proton.uiComponentsSliders.build();
});

proton.uiComponentsSliders = {
	build: function () {
		// Initiate Sliders
		$('.bslider').slider();
		
		!verboseBuild || console.log('            proton.uiComponentsSliders build DONE');
	}
}