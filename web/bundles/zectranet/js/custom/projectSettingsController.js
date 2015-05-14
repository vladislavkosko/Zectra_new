Zectranet.controller('ProjectSettingsController', ['$scope', '$http',
    function($scope, $http) {

        $scope.urlGetProjectSettingInfo = JSON_URLS.urlGetProjectSettingInfo;
        $scope.urlSendProjectRequest = JSON_URLS.urlSendProjectRequest;
        $scope.urlDeleteProjectRequest = JSON_URLS.urlDeleteProjectRequest;
        $scope.ReSendProjectRequest = JSON_URLS.ReSendProjectRequest;

        $scope.HO_Contacts = [];
        $scope.All_Contacts = [];
        $scope.Project_Team = [];

        $scope.HO_contact_message = '';
        $scope.All_contact_message = '';
        $scope.HO_Contacts_test = false;
        $scope.All_Contacts_test = false;

        $scope.QnALogsVisible = false;


        $scope.QNASettingsErrors = {
            'HO_Contact_message_Error' : false,
            'All_Contact_message_Error' : false
        };

        setInterval( function() {
            $scope.getProjectSettingInfo();
        }, 60000);

        $scope.getProjectSettingInfo = function () {
            $http.get($scope.urlGetProjectSettingInfo)
                .success(function (response) {
                    $scope.HO_Contacts = response.HO_Contacts;
                    $scope.All_Contacts = response.All_Contacts;
                    $scope.Project_Team = response.Project_Team;

                    $scope.ProjectLogs = response.ProjectLogs;

                    for(var i = 0; i < $scope.HO_Contacts.length;i++)
                    {
                        $scope.HO_Contacts[i].checked = false;
                    }
                    for(i = 0; i < $scope.All_Contacts.length;i++)
                    {
                        $scope.All_Contacts[i].checked = false;
                    }
                    for( i = 0; i < $scope.Project_Team.length;i++)
                    {
                        $scope.Project_Team[i].reSendVisibleButton = false;
                        var one_minute = 1000 * 60;
                        var now = new Date(response.timeNow);
                        var timeRequest = new Date($scope.Project_Team[i].date);
                        var difference_miliseconds = now - timeRequest;
                        difference_miliseconds = difference_miliseconds / one_minute;

                        if($scope.Project_Team[i].status.id == 1 && difference_miliseconds >= 0.5) {
                            $scope.Project_Team[i].reSendVisibleButton = true;
                        }
                    }
                }
            );
        };

        $scope.contactChecked = function (type, index, array) {
            for(var i = 0; i < array.length; i++)
            {
                array[i].checked = (i == index);
            }
            $scope.testClickableButton(type, array);
        };


        $scope.SendRequest = function (type, message, array)
        {
            if((type == 1) && (message == ''))
            {
                $scope.QNASettingsErrors.HO_Contact_message_Error = true;
                $scope.QNASettingsErrors.All_Contact_message_Error = false;
            } else if(type == 2 && message == '') {
                $scope.QNASettingsErrors.HO_Contact_message_Error = false;
                $scope.QNASettingsErrors.All_Contact_message_Error = true;
            } else {
                var user_id = 0;
                for (var i = 0; i < array.length; i++) {
                    if (array[i].checked) {
                        user_id = array[i].id;
                    }
                }

                $http.post($scope.urlSendProjectRequest, {'message': message, 'user_id': user_id})
                    .success(function (response) {
                        if (response == 1) {
                            if (type == 1) {
                                $scope.QNASettingsErrors.HO_Contact_message_Error = false;
                                $('#send_request_by_HO_contacts').modal('hide');
                            }
                            else if (type == 2) {
                                $scope.QNASettingsErrors.All_Contact_message_Error = false;
                                $('#send_request_by_All_contacts').modal('hide');
                            }
                            $scope.getProjectSettingInfo();
                        }
                    }
                );
            }
        };

        $scope.testClickableButton = function (type,array) {
            for(var i = 0; i < array.length; i++)
            {
                if(type == 1)
                {
                    if(array[i].checked)
                    {
                        $scope.HO_Contacts_test = true;
                        break;
                    }
                    else
                    {
                        $scope.HO_Contacts_test = false;
                    }
                }
                if(type == 2)
                {
                    if(array[i].checked) {
                        $scope.All_Contacts_test = true;
                        break;
                    } else {
                        $scope.All_Contacts_test = false;
                    }
                }
            }
        };

        $scope.deleteProjectRequest = function (request_id) {
            var urlDeleteProjectRequest = $scope.urlDeleteProjectRequest.replace('requestid', request_id);
            $http.delete(urlDeleteProjectRequest)
                .success(function (response) {
                    switch (response)
                    {
                        case 0:

                            break;
                        case 1:

                            break;
                        case -1:

                            break;
                    }
                    $scope.getProjectSettingInfo();
                })
        };

        $scope.reSendRequest = function (request) {
            $http.post($scope.ReSendProjectRequest,{
                'id': request.id,
                'user_id': request.user.id,
                'message': request.message,
                'request_status': request.status.id
            })
                .success(function (response) {
                    if(response == 1)
                    {
                        $scope.getProjectSettingInfo();
                    }

                })

        };

    }
]);