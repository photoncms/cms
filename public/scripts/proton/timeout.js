$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.timeout build');
    
    proton.timeout.build();
});

proton.timeout = {
	build: function () {
		$.sessionTimeout({
			message: 'Your session will be locked in 5 seconds.',
			keepAlive: false,
			logoutUrl: 'login.html',
			redirUrl: 'locked.html',
			warnAfter: 3000,
			redirAfter: 8000
		});
	}
}