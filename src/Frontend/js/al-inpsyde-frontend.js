(function($, undefined) {

	'use strict';

	var alPlugin = {};

	// Set as a browser global
	window.alPlugin = alPlugin;

	alPlugin.AlRefreshUsersTable = function () {

		$('#al_users_table').css('filter', 'blur(2px)');

		$.ajax({
			url: alAjaxVars.ajaxurl,
			method: 'POST',
			data: {
				action: 'al_refresh_users_table',
				nonce: alAjaxVars.nonce
			},
			error : function(error){
				console.log(error)
			}
		}).done(function(response) {
			if (true === response.success) {
				$('#al_users_table_container').replaceWith(response.data);
			}
			else {
				alert('Request error! See console for details.');
				console.log(response);
			}
		});
	}



	alPlugin.AlDeleteTransients = function () {

		if (!confirm('Are you sure to delete all related transients?')) return false;

		$.ajax({
			url: alAjaxVars.ajaxurl,
			method: 'POST',
			data: {
				action: 'al_delete_transients',
				nonce: alAjaxVars.nonce
			},
			error : function(error){
				console.log(error)
			}
		}).done(function(response) {
			if (true === response.success) {
				alert('Related transients successfully deleted.');
			}
			else {
				alert('Request error! See console for details.');
				console.log(response);
			}
		});
	}

	alPlugin.AlGetUserDetails = function (user_id, selector) {

		let userDetailsTR = $('#user_' + user_id + '_details'),
			userTR = userDetailsTR.prev(),
			loadingHTML = '<h4 style="text-align: center">Loading...</h4>';

		if (userTR.hasClass('active_user')) {
			userTR.removeClass('active active_user');
			userDetailsTR.removeClass('active');
			return;
		} else {
			$('#al_users_table tr').removeClass('active active_user');
			userTR.addClass('active_user');
			userDetailsTR.addClass('active').children('td').html(loadingHTML);
		}

		$.ajax({
			url: alAjaxVars.ajaxurl,
			method: 'POST',
			data: {
				action: 'al_get_user_details',
				user_id: user_id,
				nonce: alAjaxVars.nonce
			},
			error : function(error){
				console.log(error)
			}
		}).done(function(response) {
			if (true === response.success) {
				$('#user_' + user_id + '_details td').html(response.data);
			}
			else {
				alert('Request error! See console for details.');
				console.log(response);
			}
		});
	}

})(jQuery);
