$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.dashdemo build');
    proton.dashdemo.build();
});

proton.dashdemo = {
	build: function () {
		setTimeout(function() {
			$('.widget-progress-bar .progress-bar').css({
				width: '63%'
			});
		}, 800);

		setTimeout(function() {
			proton.dashdemo.newMessage();
		}, 3500);

		setTimeout(function() {
			proton.dashdemo.newUser();
		}, 4500);

		!verboseBuild || console.log('            proton.dashdemo build DONE');
	},

	newMessageTimer : false,
	messageTemplate : '<li class="list-group-item new-item generated-item"><i><img src="images/user-icons/user{{userNum}}.jpg" alt="User Icon"></i><div class="text-holder"><span class="title-text">{{from}}:</span><span class="description-text">{{content}}</span></div><span class="stat-value">a minut ago</span></li>',
	newMessage : function () {
		var messageWidgetFlipped = false;
		if (proton.dashdemo.newMessageTimer){
			clearTimeout(proton.dashdemo.newMessageTimer);
		}

		var f = proton.dashdemo.randomNum(0,9);
		var l = proton.dashdemo.randomNum(0,9);
		var g = proton.dashdemo.randomNum(0,9);
		var c = proton.dashdemo.randomNum(0,14);
		var newMessage = proton.dashdemo.messageTemplate;
		newMessage = newMessage.replace("{{userNum}}", proton.dashdemo.randomNum(1,10));
		newMessage = newMessage.replace("{{from}}", proton.dashdemo.firstname[f] + ' ' + proton.dashdemo.lastname[l]);
		newMessage = newMessage.replace("{{content}}", proton.dashdemo.greeting[g] + ' ' + proton.dashdemo.content[c]);
		$(newMessage).prependTo('.messages .front .list-group');

		setTimeout(function() {
			if ($('.messages').is('.setup'))
				messageWidgetFlipped = true;
			$('.messages .front .list-group .generated-item').eq(1).find('.stat-value').text('2 mins ago');
			$('.messages .front .list-group .generated-item').eq(2).find('.stat-value').text('3 mins ago');
			$('.messages .front .list-group .generated-item').eq(3).find('.stat-value').text('5 mins ago');
			$('.messages .front .list-group .generated-item').eq(4).find('.stat-value').text('6 mins ago');
			$('.messages .front .list-group li').eq(5).remove();
			var $new = $('.messages .new-item');
			$new.removeClass('new-item');
			setTimeout(function() {
				!!messageWidgetFlipped || $new.toggleClass('animated flash');
				!!messageWidgetFlipped || setTimeout(function() {
					$new.toggleClass('animated flash');
				}, 1000);
			}, 100);
		}, 1000);

		proton.dashdemo.newMessageTimer = setTimeout(proton.dashdemo.newMessage, 1000 * proton.dashdemo.randomNum(5,12));
	},

	newUserTimer : false,
	newUserCount : 2512,
	userTemplate : '<li class="list-group-item new-item generated-item"><i><img src="images/user-icons/user{{userNum}}.jpg" alt="User Icon"></i><div class="text-holder"><span class="title-text">{{from}}</span></div><span class="stat-value">a minut ago</span></li>',
	newUser : function () {
		var statWidgetFlipped = false;
		var latestWidgetFlipped = false;
		if (proton.dashdemo.newUserTimer){
			clearTimeout(proton.dashdemo.newUserTimer);
		}

		var f = proton.dashdemo.randomNum(0,9);
		var l = proton.dashdemo.randomNum(0,9);
		var newUser = proton.dashdemo.userTemplate;
		newUser = newUser.replace("{{userNum}}", proton.dashdemo.randomNum(1,10));
		newUser = newUser.replace("{{from}}", proton.dashdemo.firstname[f] + ' ' + proton.dashdemo.lastname[l]);
		$(newUser).prependTo('.latest-users .front .list-group');

		setTimeout(function() {
			proton.dashdemo.newUserCount++;
			$('.latest-users .front .list-group .generated-item').eq(1).find('.stat-value').text('2 mins ago');
			$('.latest-users .front .list-group .generated-item').eq(2).find('.stat-value').text('3 mins ago');
			$('.latest-users .front .list-group .generated-item').eq(3).find('.stat-value').text('5 mins ago');
			$('.latest-users .front .list-group .generated-item').eq(4).find('.stat-value').text('5 mins ago');
			$('.latest-users .front .list-group .generated-item').eq(5).find('.stat-value').text('6 mins ago');
			$('.latest-users .front .list-group li').eq(6).remove();
			
			$userCount = $('.general-stats .front .list-group li').eq(0);
			// check if widgets are flipped, disable flash animation if true
			if ($('.general-stats').is('.setup'))
				statWidgetFlipped = true;
			if ($('.latest-users').is('.setup'))
				latestWidgetFlipped = true;

			!!statWidgetFlipped || $userCount.toggleClass('animated flash');
			!!statWidgetFlipped || setTimeout(function() {
				$userCount.toggleClass('animated flash');
			}, 1000);
			setTimeout(function() {
				$userCount.find('.title-text').text(numeral(proton.dashdemo.newUserCount).format('0,0'));
			}, 200);

			var $new = $('.latest-users .new-item');
			$new.removeClass('new-item');
			!!latestWidgetFlipped || $new.toggleClass('animated flash');
			!!latestWidgetFlipped || setTimeout(function() {
				$new.toggleClass('animated flash');
			}, 1000);
		}, 1000);

		proton.dashdemo.newUserTimer = setTimeout(proton.dashdemo.newUser, 1000 * proton.dashdemo.randomNum(1,8));
	},

	randomNum : function (from,to) {
		return Math.floor(Math.random()*(to-from+1)+from);
	},
	
	firstname : ['Colin','Belshazzar','Chinyere','Sanyu','Roan','Fernando','Lilianne','Robert','Graeme','Artemisios'],

	lastname : ['Menachem','Holgersen','Reuter','MacBride','Van','Moore','Grant','Daubney','Toset','McGee'],

	greeting : ['Hi, ', 'Hi, ', 'Hi there, ', 'Hello, ', 'Hi all, ', 'Hey, ', 'Hey, ', '', '', ''],

	content : [
		'just saying hi. :)',
		'did you go out last night?',
		'where have they all gone?',
		'we have a meeting tomorrom morning.',
		'do you want to go out tonight?',
		'buy Proton now!',
		'you want this UI theme!',
		'you want Proton UI theme.',
		'you need Proton UI theme.',
		'get Proton now!',
		'Proton is the best!',
		'you wish to buy Proton!',
		'do you want Proton?',
		'Proton can run your site.',
		'buy Proton!'
	],

	capitaliseFirstLetter : function (string){
	    return string.charAt(0).toUpperCase() + string.slice(1);
	}
}