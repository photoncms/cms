$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.sidebar build');
    
    proton.sidebar.build();
});

proton.sidebar = {
	build: function () {
		// Initiate sidebar events
		proton.sidebar.events();

		// Build Advanced Search sidebar feature
		!$('.advanced-search').length || proton.sidebar.buildAdvancedSearch();

		// Initiate sidebar retraction on smaller screen sizes
		proton.sidebar.retractOnResize();

		// Sets max-heigh for sidbar menu in mobile mode (needed for CSS transitions)
		proton.sidebar.setSidebarMobHeight();

		// Builds page data for sidebar menu
		proton.sidebar.buildPageData();

		// Check if jstree plugin exists, initiate if true
		!$.jstree || proton.sidebar.jstreeSetup();

		!verboseBuild || console.log('            proton.sidebar build DONE');

	},
	buildAdvancedSearch: function () {
		!verboseBuild || console.log('            proton.sidebar.buildAdvancedSearch()');
        $('.select2').select2({ minimumResultsForSearch: 6 });
        $('.datetimepicker-search').datetimepicker({
        	format : 'yyyy-mm-dd',
        	minView : 'month',
        	maxView : 'month',
        	autoclose : true
        });
        $('input[type="radio"], input[type="checkbox"]').uniform();
	},
	events : function () {
		!verboseBuild || console.log('            proton.sidebar binding events');

		$(document).on('click', '.sidebar-handle', function(event) {
			event.preventDefault();
			proton.sidebar.toogleSidebar();
		});

		$(document).on('click', '.btn-advanced-search, .close-advanced-search', function(event) {
			event.preventDefault();
			proton.sidebar.toogleAdvancedSearch();
		});
	},
	toogleAdvancedSearch: function () {
		!verboseBuild || console.log('            proton.sidebar.toogleAdvancedSearch()');
		$('.sidebar').toggleClass('search-mode');
	},
	toogleSidebar: function () {
		!verboseBuild || console.log('            proton.sidebar.toogleSidebar()');

		$('.sidebar').toggleClass('extended').toggleClass('retracted');
		$('.wrapper').toggleClass('extended').toggleClass('retracted');
		
		if ($('.sidebar').is('.search-mode')){
			proton.sidebar.toogleAdvancedSearch();
		}

		if ($('.sidebar').is('.retracted')){
		    $.cookie('protonSidebar', 'retracted', {
		        expires: 7,
		        path: '/'
		    });
		}
		else {
		    $.cookie('protonSidebar', 'extended', {
		        expires: 7,
		        path: '/'
		    });
		}
		setTimeout(function() {
			!(proton.graphsStats && proton.graphsStats.redrawCharts) || proton.graphsStats.redrawCharts();
		}, 1000);
	},
	retractOnResize: function () {
		!verboseBuild || console.log('            proton.sidebar.retractOnResize()');
		
		if(!ltIE9 && !Modernizr.mq('(min-width:' + (screenMd) + 'px)')){
			if ($('.sidebar').is('.extended')){
				proton.sidebar.toogleSidebar();
				
			}
		}
	},
	jstreeSetup : function () {
		!verboseBuild || console.log('            proton.sidebar.jstreeSetup()');
		
		$.jstree._themes = "./styles/vendor/jstree-theme/";

		$("#proton-tree").jstree({
			"json_data" : proton.sidebar.treeJson,
			// the `plugins` array allows you to configure the active plugins on this instance
			"plugins" : ["themes","json_data","ui","crrm"],
			// each plugin you have included can have its own config object
			"core" : {
				"animation" : 100,
				"initially_open" : [ "proton-lvl-0" ]
			},
			// set a theme
			"themes" : {
	            "theme" : "proton"
	        }
		})
		.on('click', 'a', function(event) {
			var treeLink = $(this).attr("href");
			if (treeLink !== "#")
				document.location.href = $(this).attr("href");
			return false;
		});
	},
	setSidebarMobHeight : function () {
		!verboseBuild || console.log('            proton.sidebar.setSidebarMobHeight()');

		if(ltIE9 || Modernizr.mq('(min-width:' + (screenXs) + 'px)')){
			$('.sidebar').css('max-height','none');
		}
		else{
			$('.sidebar').css('max-height','none');
			setTimeout(function() {
				var sidebarMaxH = $('.sidebar > .panel').height() + 30 + 'px';
				$('.sidebar').css('max-height',sidebarMaxH);
			}, 200);
		}
	},
	doThislater : function (){
		$('.sidebar .sidebar-handle').on('click', function(){
		    $('.panel, .main-content').toggleClass('retracted');
		});

		// APPLY THEME COLOR
		if ($.cookie('themeColor') == 'light') {
		    $('body').addClass('light-version');
		}
		if($.cookie('jsTreeMenuNotification')!='true') {
		    $.cookie('jsTreeMenuNotification', 'true', {
		        expires: 7,
		        path: '/'
		    });
		    $.pnotify({
		        title: 'Slide Menu Remembers It\'s State',
		        type: 'info',
		        text: 'Slide menu will remain closed when you browse other pages, until you open it again.'
		    });
		}
	},
	buildPageData : function () {
		!verboseBuild || console.log('            proton.sidebar.buildPageData()');

		var pageTitle = document.title;
		$('.page-title').text(pageTitle);
		pageTitle = pageTitle.replace("Proton UI - ", "");
		$('.bread-page-title').text(pageTitle);
		
		$('.preface p').text(pageTitle + ' include: ');
		proton.sidebar.treeJson = {
		    'data' : [
			    {
			        'data' : { 
			            'title' : pageTitle, 
			            'attr' : { 
			            	'href' : '#top',
			            	'id' : 'proton-lvl-0'
			            } 
			        },
			        'children' : [ 
			            
			        ]
			    }
			]
		}

		var numSections = $('.section-title').length;
		$('.section-title').each(function(index, el) {
			if ($(this).is('.preface-title')) return;
			
			var sectionTitle = $.trim($(this).text());
			var sectionId = sectionTitle.replace(/\s+/g, '-').toLowerCase()

			// creates dash-case anchor ID to be used with sidebar links
			$(this).parents('.list-group-item').attr('id', sectionId);

			// Add item to breadcrumb nav
			$('<li role="presentation"><a role="menuitem" tabindex="-1" href="#' + sectionId + '">' + sectionTitle + '</a></li>').appendTo('.breadcrumb-nav .active .dropdown-menu');


			// creates sidebar link object
			var newLinkObject = {
				'data' : {
					'title' : sectionTitle, 
					'attr' : { 'href' : '#' + sectionId }
				}
			};
			proton.sidebar.treeJson.data[0].children.push(newLinkObject);

			// Add item to title bar
			if ((index + 1) !== numSections)
				$('.preface p').text($('.preface p').text() + sectionTitle + ', ');
			else
				$('.preface p').text($('.preface p').text().slice(0, -2) + ' and ' + sectionTitle + '.');
		});
	}
}