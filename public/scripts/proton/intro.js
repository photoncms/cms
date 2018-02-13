$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.intro build');
    
    proton.intro.build();
});

proton.intro = {
	build: function () {
		if (!$('.sidebar').is('.extended')){
			proton.sidebar.toogleSidebar();
		}
		setTimeout(function() {
			introJs().setOptions({'showStepNumbers': false}).start();
		}, 1300);
	}	
}