$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.userProfile build');
    
    proton.userProfile.build();
});

proton.userProfile = {
	build: function () {
		// Morris Charts
		!(Morris && $('.graph').length) || proton.userProfile.drawCharts();
		!($('#hybridMap').length) || proton.userProfile.map();
		$("#about-me").textareaCounter();

		$('.avatar').on('click', '.trash-item, .remove-cancel', function(event) {
			event.preventDefault();
			$('.avatar .controls').fadeToggle('150');
		});

		!verboseBuild || console.log('            proton.userProfile build DONE');
	},
	randomNum : function (from,to) {
		return Math.floor(Math.random()*(to-from+1)+from);
	},
	statChange : false,
	graph : {},
	redrawCharts : function () {
		!verboseBuild || console.log('            proton.userProfile.redrawCharts()');

		$.each(proton.userProfile.graph, function(index, val) {
			this.redraw();
		});
	},
	drawCharts : function () {
		!verboseBuild || console.log('            proton.userProfile.drawCharts()');

		if($('#hero-bar').length)
		proton.userProfile.graph.Bar = Morris.Bar({
		    element: 'hero-bar',
		    data: [
		      {language: 'CSS 3', skill: 5},
		      {language: 'HTML 5', skill: 4},
		      {language: 'JavaScript', skill: 4},
		      {language: 'Photoshop', skill: 3},
		      {language: 'PHP', skill: 2}
		    ],
		    ymax: 5,
		    ymin: 1,
		    grid: false,
		    xkey: 'language',
		    ykeys: ['skill'],
		    labels: ['Self Assessment'],
		    xLabelAngle: 90,
		    hideHover: 'auto',
		});
	},
	map: function () {
		var poly, map, plainMap;
		var markers = [];
		var path = new google.maps.MVCArray;
		function initialize() {
		    var hybridMap = new google.maps.Map(document.getElementById("hybridMap"), {
		        zoom: 19,
		        center: new google.maps.LatLng(40.78,-73.95),
		        mapTypeId: google.maps.MapTypeId.HYBRID
		    });
		}
		// START MAPS
		initialize();
	}
}