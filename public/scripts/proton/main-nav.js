$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.mainNav build');
    proton.mainNav.build();
});

proton.mainNav = {
	build: function () {
		// Initiate mainNav events
		proton.mainNav.events();
		!verboseBuild || console.log('            proton.mainNav build DONE');
	},
	events : function () {
		!verboseBuild || console.log('            proton.mainNav binding events');
		
		// nav expansion for touch devices
		$('.touch nav.main-menu').on('click', function(event) {
			$(this).addClass('expanded');
			return false;
		});
		$('.main-menu-access').on('click', function(event) {
			$('nav.user-menu > section .active').removeClass('active');
			$('.nav-view').fadeOut(30);
			if ($(this).is('.active')){
				$(this).removeClass('active');
				$('nav.main-menu').removeClass('expanded');
			}
			else{
				$('nav.main-menu').addClass('expanded');
				$(this).addClass('active');
			}
			return false;
		});

		// nav retraction for touch devices
		$('.touch body').on('click touchstart', function(event){

			if($('.touch nav.main-menu').is('.expanded')){
				$('.main-menu-access').removeClass('active');
				$('nav.main-menu').find('.active').removeClass('active');
				$('.touch nav.main-menu').removeClass('expanded');
				$("html, body").animate({scrollTop: 0}, 300, 'swing');
			}
			$('nav.user-menu > section .active').removeClass('active');
			$('.nav-view').fadeOut(30);
		});
		$('.touch nav.main-menu, .touch nav.user-menu').on('touchstart', function(event){
			event.stopPropagation();
		});

		// nav item activation for touch devices
		$('.touch nav.main-menu ul').on('click', 'li', function(event) {
			if($(this).is('.active')) {
				$(this).removeClass('active');
				event.stopPropagation();
				// if($(this).not('.has-subnav')) $('nav.main-menu').removeClass('expanded');
				return;
			}
			$('nav.main-menu').find('li').removeClass('active');
			$(this).addClass('active');
		});
	}
}