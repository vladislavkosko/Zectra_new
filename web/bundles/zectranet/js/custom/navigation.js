Zectranet.controller('NavigationController', ['$rootScope', '$scope', '$http', function($rootScope, $scope, $http){
	console.log('NavigationController was loaded!');

	var title = $('title');
	var titleValue = title.text();
	var notificationsGetUrl = JSON_URLS.notificationsGet;
	var officeShowUrlBase = JSON_URLS.officeShow;
	var topicShowUrlBase = JSON_URLS.topicShow;
	var userSettingsUrl = JSON_URLS.userSettings;
	var checkUserByUsernameUrl = JSON_URLS.checkUserByUsername;
	$scope.userSettings = null;
	$scope.notifications = [];
	$scope.notifyHandler = null;
	$scope.NewNotificationsCount = 0;

	getUserSettings();

	function getUserSettings() {
		$http({
			method: "GET",
			url: userSettingsUrl
		})
			.success(function(response) {
				if(response.result) {
					$scope.userSettings = response.result;
					getNotifications();
				}
			})
	}

	function Notify() {
		title.text((title.text() == titleValue) ? 'Incoming notifications!' : titleValue);
	}

	function StartNotify() {
		$scope.notifyHandler = setInterval(Notify, 3000);
	}

	function StopNotify() {
		clearInterval($scope.notifyHandler);
		title.text(titleValue);
		$scope.notifyHandler = null;
	}

	function getNotifications() {
		$http.get(notificationsGetUrl)
			.success(function(response){
				if (response.result && response.result.length > 0)
				{
					var notification_to_show = true;
					var length = response.result.length;

					for(var i = 0; i < length; i++) {
						switch (response.result[i].type) {
							case 'message_office' :
								notification_to_show = $scope.userSettings.user_settings_notifications.msg_site_message_office;
								if ($scope.NewNotificationsCount != response.result.length) {
								}
								break;
							case 'message_topic' :
								notification_to_show = $scope.userSettings.user_settings_notifications.msg_site_message_topic;
								if ($rootScope.NEW_POSTS && $scope.NewNotificationsCount != response.result.length) {
									$rootScope.NEW_POSTS();
									document.getElementById('notif_sound').play();
								}
								break;
							case 'membership_own' :
								notification_to_show = $scope.userSettings.user_settings_notifications.msg_site_membership_own;
								if ($scope.NewNotificationsCount != response.result.length) {
									document.getElementById('notif_sound').play();
								}
								break;
							case 'membership_user' :
								notification_to_show = $scope.userSettings.user_settings_notifications.msg_site_membership_user;
								if ($scope.NewNotificationsCount != response.result.length) {
									document.getElementById('notif_sound').play();
								}
								break;
							case 'removed_office' :
								notification_to_show = $scope.userSettings.user_settings_notifications.msg_site_removed_office;
								if ($scope.NewNotificationsCount != response.result.length) {
									document.getElementById('notif_sound').play();
								}
								break;
							case 'removed_topic' :
								notification_to_show = $scope.userSettings.user_settings_notifications.msg_site_removed_topic;
								if ($scope.NewNotificationsCount != response.result.length) {
									document.getElementById('notif_sound').play();
								}
								break;
							case 'topic_added' :
								notification_to_show = $scope.userSettings.user_settings_notifications.msg_site_topic_added;
								if ($scope.NewNotificationsCount != response.result.length) {
									document.getElementById('notif_sound').play();
								}
								break;
							case 'membership_own_out' :
								notification_to_show = $scope.userSettings.user_settings_notifications.msg_site_membership_own_out;
								if ($scope.NewNotificationsCount != response.result.length) {
									document.getElementById('notif_sound').play();
								}
								break;
							case 'membership_user_out' :
								notification_to_show = $scope.userSettings.user_settings_notifications.msg_site_membership_user_out;
								if ($scope.NewNotificationsCount != response.result.length) {
									document.getElementById('notif_sound').play();
								}
								break;
							case 'task_assigned' :
								notification_to_show = $scope.userSettings.user_settings_notifications.msg_site_task_assigned;
								if ($scope.NewNotificationsCount != response.result.length) {
									document.getElementById('notif_sound').play();
								}
								break;
							case 'task_comment' :
								notification_to_show = $scope.userSettings.user_settings_notifications.msg_site_task_comment;
								if ($scope.NewNotificationsCount != response.result.length) {
									document.getElementById('notif_sound').play();
								}
								break;
							default:
								notification_to_show = true;
								if ($scope.NewNotificationsCount != response.result.length) {
									document.getElementById('notif_sound').play();
								}
								break;
						}
						if(notification_to_show == false){
							response.result.splice(i, 1);
							i--;
						}
					}

					$scope.NewNotificationsCount = response.result.length;

					if(!$scope.userSettings.user_settings.disable_message_on_site){
						if(response.result.length > 0){
							if ($scope.notifyHandler == null) StartNotify();
							$scope.notifications = prepareNotifications(response.result);
						} else {
							StopNotify();
							$scope.notifications = [];
						}
					} else {
						var notification_to_show2 = true;
						var length = response.result.length;
						for(var i = 0; i < length; i++){
							switch (response.result[i].type) {
								case 'private_message_office' :
									notification_to_show2 = true;
									if ($rootScope.NEW_POSTS && $scope.NewNotificationsCount != response.result.length) {
										$rootScope.NEW_POSTS();
										document.getElementById('notif_sound').play();
									}
									break;
								case 'private_message_topic' :
									notification_to_show2 = true;
									if ($rootScope.NEW_POSTS && $scope.NewNotificationsCount != response.result.length) {
										$rootScope.NEW_POSTS();
										document.getElementById('notif_sound').play();
									}
									break;
								case 'private_message_task' :
									notification_to_show2 = true;
									if ($rootScope.NEW_POSTS && $scope.NewNotificationsCount != response.result.length) {
										$rootScope.NEW_POSTS();
										document.getElementById('notif_sound').play();
									}
									break;
								default:
									notification_to_show2 = false;
									if ($rootScope.NEW_POSTS && $scope.NewNotificationsCount != response.result.length) {
										$rootScope.NEW_POSTS();
										document.getElementById('notif_sound').play();
									}
									break;
							}
							if(notification_to_show2 == false) {
								response.result.splice(i, 1);
								i--;
							}
						}
						if(response.result.length > 0) {
							if ($scope.notifyHandler == null) StartNotify();
							$scope.notifications = prepareNotifications(response.result);
						} else {
							StopNotify();
							$scope.notifications = [];
						}
					}
				} else {
					StopNotify();
					$scope.notifications = [];
				}
				setTimeout(getNotifications, 3000);
			})
	}

	function prepareNotifications(notifications) {
		notifications = _.map(notifications, function(n) {
			if (['private_message_office', 'task_comment', 'task_assigned',
					'membership_own', 'membership_own_out', 'membership_user',
					'membership_user_out', 'message_office', 'removed_office'].indexOf(n.type) != -1) {
				n.href = officeShowUrlBase.replace('0', n.destinationid);
			} else {
				n.href = topicShowUrlBase.replace('0', n.destinationid);
			}

			return n;
		});
		TASKS_NOTIFICATIONS =_.map(notifications,function(n){
			if(n.type=='task_comment')
				return n;
		});

		notifications = _.filter(notifications, function(n){
			return n.href != window.location.href;
		});

		$http({
			method: "GET",
			url: window.location.href
		});
		return notifications;

	}

	$(".write-message").keyup(function(){
		var msg = $(this).val();
		var symb_index = msg.indexOf("@");
		if(symb_index != -1){
			var tmp_str = msg.substring(symb_index);
			var space_index = tmp_str.indexOf(" ");
			var user_to_light_name = "";
			if(space_index != -1){
				user_to_light_name = tmp_str.substring('0',space_index);
			} else {
				user_to_light_name = tmp_str.substring('1');
			}
		}
	});
}]);