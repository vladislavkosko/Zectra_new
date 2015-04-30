var userController = Zectranet.controller('UserController', ['$scope', '$http', '$rootScope',
    function($scope, $http, $rootScope) {

        $scope.atclRequest = {
            'user_id': USER_ID,
            'app_user_id': APP_USER_ID,
            'message': ''
        };

        $scope.response_by_send_request = {
            'body': '',
            'type': '',
            'visible': false
        };

        $scope.urlSendRequest = JSON_URLS.urlSendRequest;
        $scope.urlGetContactList = JSON_URLS.urlGetContactList;


        $scope.contacts = [];

        $scope.SendRequest = function () {
            $http.post($scope.urlSendRequest,
                {
                    'message': $scope.atclRequest.message,
                    'user_id': $scope.atclRequest.user_id,
                    'app_user_id':  $scope.atclRequest.app_user_id
                })
                .success(function (response) {
                    switch (response)
                    {
                        case 1:
                            $scope.response_by_send_request.type = 'alert-success' ;
                            $scope.response_by_send_request.body = 'Request successfully sent !!!' ;
                            $scope.response_by_send_request.visible = true ;
                        break;
                        case -1:
                            $scope.response_by_send_request.type = 'alert-danger' ;
                            $scope.response_by_send_request.body = 'Unexpected Error !!!' ;
                            $scope.response_by_send_request.visible = true ;
                        break;
                    }
                    $('#add_to_contact_list').modal('hide');
                }
            );
        };

        $scope.closeNotification = function () {
            $('#add_to_contact_list').modal('hide');
            $scope.response_by_send_request.visible = false;
        };

        console.log('UserController was loaded !!!')
    }
]);
