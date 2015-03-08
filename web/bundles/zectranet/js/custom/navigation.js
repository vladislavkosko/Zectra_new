Zectranet.controller('NavigationController', ['$scope', '$http', function($scope, $http){

	var title = $('title');
	var titleValue = title.text();
	var notificationsGetUrl = JSON_URLS.notificationsGet;
	var officeShowUrlBase = JSON_URLS.officeShow;
	var projectShowUrlBase = JSON_URLS.projectShow;
	var taskShowUrlBase = JSON_URLS.taskShow;

	$scope.notifications = [];
	$scope.notifyHandler = null;
    $scope.notificationsLength = null;

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

	$scope.getNotification =  function getNotifications() {
		$http.get(notificationsGetUrl)
			.success(function(response){
				if (response.result && response.result.length > 0)
				{
                    if (response.result.length != $scope.notificationsLength)
                    {
                        StartNotify();
                        document.getElementById('notif_sound').play();
                        $scope.notifications = prepareNotifications(response.result);
                        $scope.notificationsLength = response.result.length;
                    }
                }

                else {
					StopNotify();
					$scope.notifications = [];
				}
				setTimeout(getNotifications, 5000);
			})
	};

	function prepareNotifications(notifications) {
		notifications = _.map(notifications, function(n) {
			if (["message_office", "request_office", "private_message_office"].indexOf(n.type) != -1)
                n.href = officeShowUrlBase.replace('0', n.destinationid);
            else if (["message_task", "request_assign_task", "private_message_task"].indexOf(n.type) != -1)
                n.href = taskShowUrlBase.replace('0', n.destinationid);
            else {
				n.href = projectShowUrlBase.replace('0', n.destinationid);
			}

			return n;
		});

		return notifications;
	}

    console.log('NavigationController was loaded!');

}]);