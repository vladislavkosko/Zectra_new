Zectranet.controller('NavigationController', ['$scope', '$http', '$rootScope',
    function($scope, $http, $rootScope) {
        var title = $('title');
        var titleValue = title.text();
        var notificationsGetUrl = JSON_URLS.notificationsGet;
        var officeShowUrlBase = JSON_URLS.officeShow;
        var projectShowUrlBase = JSON_URLS.projectShow;
        var taskShowUrlBase = JSON_URLS.taskShow;

        var approveContactMembershipRequest = JSON_URLS.approveContactMembershipRequest;
        var approveHFMembershipRequest = JSON_URLS.approveHFMembershipRequest;
        var approveQnAMembershipRequest = JSON_URLS.approveQnAMembershipRequest;

        $scope.requests = {};
        $scope.notifications = null;
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

        function prepareRequests (requests) {
            var newRequests = {
                'contactRequests': [],
                'hfRequests': [],
                'QnARequests': []
            };

            for (var i = 0; i < requests.length; i++) {
                switch (requests[i].type.id) {
                    case 1: break;
                    case 2: break;
                    case 3: break;
                    case 4: break;
                    case 5: newRequests.contactRequests.push(requests[i]); break;
                    case 6: break;
                    case 7: break;
                    case 8: newRequests.hfRequests.push(requests[i]); break;
                    case 9: break;
                    case 10: break;
                    case 13: newRequests.QnARequests.push(requests[i]); break;
                    case 14: break;
                    case 15: break;
                }
            }
            return newRequests;
        }

        $scope.acceptContactMembershipRequest = function (request_id, index) {
            var approveUrl = approveContactMembershipRequest.replace('request_id', request_id);
            $http.post(approveUrl, { 'answer': true })
                .success(function (response) {
                    $scope.requests.contactRequests.splice(index, 1);
                    $('#request_more_info').modal('hide');
                }
            );
        };

        $scope.declineContactMembershipRequest = function (request_id, index) {
            var approveUrl = approveContactMembershipRequest.replace('request_id', request_id);
            $http.post(approveUrl, { 'answer': false })
                .success(function (response) {
                    $scope.requests.contactRequests.splice(index, 1);
                    $('#request_more_info').modal('hide');
                }
            );
        };

        $scope.acceptHFMembershipRequest = function (request_id, index) {
            var approveUrl = approveHFMembershipRequest.replace('request_id', request_id);
            $http.post(approveUrl, { 'answer': true })
                .success(function (response) {
                    $scope.requests.contactRequests.splice(index, 1);
                    $('#request_more_info').modal('hide');
                }
            );
        };

        $scope.declineHFMembershipRequest = function (request_id, index) {
            var approveUrl = approveHFMembershipRequest.replace('request_id', request_id);
            $http.post(approveUrl, { 'answer': false })
                .success(function (response) {
                    $scope.requests.contactRequests.splice(index, 1);
                    $('#request_more_info').modal('hide');
                }
            );
        };

        $scope.acceptQnAMembershipRequest = function (request_id, index) {
            var approveUrl = approveQnAMembershipRequest.replace('request_id', request_id);
            $http.post(approveUrl, { 'answer': true })
                .success(function (response) {
                    $scope.requests.contactRequests.splice(index, 1);
                    $('#request_more_info').modal('hide');
                }
            );
        };

        $scope.declineQnAMembershipRequest = function (request_id, index) {
            var approveUrl = approveQnAMembershipRequest.replace('request_id', request_id);
            $http.post(approveUrl, { 'answer': false })
                .success(function (response) {
                    $scope.requests.contactRequests.splice(index, 1);
                    $('#request_more_info').modal('hide');
                }
            );
        };

        $scope.moreInfoContactMembershipRequest = function (request) {
            $scope.requestMoreInfo = request;
            $('#request_more_info').modal('show');
        };

        $scope.getNotification = function getNotifications() {
            $http.get(notificationsGetUrl)
                .success(function(response) {
                    if (response.result.requests){
                        $scope.requests = prepareRequests(response.result.requests);
                    }

                    if (response.result.notifications && response.result.notifications.length > 0){
                        if (response.result.notifications.length != $scope.notificationsLength){
                            StartNotify();
                            document.getElementById('notif_sound').play();
                            var chatUpdate = false;
                            $scope.notifications = prepareNotifications(response.result.notifications);
                            for (var i = 0; i < $scope.notifications.length; i++) {
                                if ($scope.notifications[i].type == 'message_office'
                                    || $scope.notifications[i].type == 'message_project'
                                    || $scope.notifications[i].type == 'message_task'
                                    || $scope.notifications[i].type == 'message_epic_story'
                                    || $scope.notifications[i].type == 'private_message_office'
                                    || $scope.notifications[i].type == 'private_message_project'
                                    || $scope.notifications[i].type == 'private_message_task'
                                    || $scope.notifications[i].type == 'private_message_epic_story'
                                ) {
                                    chatUpdate = true;
                                }

                                if (chatUpdate) {
                                    $rootScope.updateChat(0, 100);
                                }

                            }
                            $scope.notificationsLength = response.result.notifications.length;
                            $rootScope.NOTIFICATIONS = $scope.notifications;
                        }
                    } else {
                        StopNotify();
                        $scope.notifications = [];
                    }
                    setTimeout(getNotifications, 5000);
                })
        };

        function prepareNotifications(notifications) {
            notifications = _.map(notifications, function(n) {
                if (["message_home_office", "message_office", "request_office", "private_message_office"].indexOf(n.type) != -1)
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