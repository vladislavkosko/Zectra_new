Zectranet.controller('HeaderForumController', ['$scope', '$http',
    function($scope, $http) {
        $scope.urlGetHeaders = JSON_URLS.getHeaderForumHeaders;
        $scope.urlAddHeader = JSON_URLS.addHeaderForumHeaders;
        $scope.urlAddSubHeader = JSON_URLS.addSubHeaderForumHeaders;
        $scope.urlDeleteHeader = JSON_URLS.deleteHeaderForumHeaders;
        $scope.urlGetProjectSettingInfo = JSON_URLS.urlGetProjectSettingInfo;
        $scope.urlSendProjectRequest = JSON_URLS.urlSendProjectRequest;
        $scope.urlDeleteProjectRequest = JSON_URLS.urlDeleteProjectRequest;
        $scope.urlReSendProjectRequest =  JSON_URLS.urlReSendProjectRequest;
        $scope.urlShowHeaderForumSubheader =  JSON_URLS.urlShowHeaderForumSubheader;

        $scope.headers = null;
        $scope.addnewheader = false;
        var timeNow = new Date(TIME_NOW);




        $scope.quickheader = {
            'title': '',
            'bgColor': '#BBBBBB',
            'textColor': '#000000'
        };



        $scope.quicksubheader = {
            'title': '',
            'description': '',
            'admin': false
        };

        $scope.headerForumErrors = {
            'subHeaderTitleError' : false,
            'subHeaderDescriptionError' : false,
            'headerTitleError' : false
        };

        $scope.headerForumSettingsErrors = {

            'HO_Contact_message_Error' : false,
            'All_Contact_message_Error' : false
        };

        $scope.modal = {
            'title': '',
            'class': '',
            'message': ''
        };

        $scope.HO_Contacts = [];
        $scope.All_Contacts = [];
        $scope.Project_Team = [];

        $scope.HO_contact_message = '';
        $scope.All_contact_message = '';
        $scope.HO_Contacts_test = false;
        $scope.All_Contacts_test = false;
        $scope.HFLogsVisible = false;


        $scope.getHeaders = function () {
            $http.get($scope.urlGetHeaders)
                .success(function (response) {
                    $scope.headers = response;
                    for(var i = 0; i < $scope.headers.length; i++) {
                        for(var j = 0; j < $scope.headers[i].subHeaders.length; j++) {
                            $scope.headers[i].subHeaders[j].urlShowHeaderForumSubheader = $scope.urlShowHeaderForumSubheader.replace('subheaderID', $scope.headers[i].subHeaders[j].id);
                        }
                    }
                }
            );
        };

        function somethingWentWrong() {
            $scope.modal.class = 'label-danger';
            $scope.modal.message = 'Something went wrong.';
            $scope.modal.title = 'Error';
            $('#header_forum_messages_modal').modal('show');
        }

        $scope.addNewHeaderQuick = function (header) {
            if($scope.quickheader.title == '')
            {
                $scope.headerForumErrors.headerTitleError = true;
            }
            else
            {
                $scope.headerForumErrors.headerTitleError = false;
                $scope.addnewheader = false;
                if (!header.title || !header.bgColor || !header.textColor) return;
                $http.post($scope.urlAddHeader, { 'header': header })
                    .success(function (response) {
                        if (response) {
                            $scope.quickheader = {
                                'title': null,
                                'bgColor': '#BBBBBB',
                                'textColor': '#000000'
                            };
                            $scope.getHeaders();
                        }
                    }
                );
            }
        };

        $scope.addNewSubHeaderQuick = function (oneheader, quicksubheader) {
            if($scope.quicksubheader.title == '' && $scope.quicksubheader.description == '')
            {
                $scope.headerForumErrors.subHeaderTitleError = true;
                $scope.headerForumErrors.subHeaderDescriptionError = true;
            }
            else if($scope.quicksubheader.title == '')
            {
                $scope.headerForumErrors.subHeaderTitleError = true;
                $scope.headerForumErrors.subHeaderDescriptionError = false;
            }
            else if($scope.quicksubheader.description == '')
            {
                $scope.headerForumErrors.subHeaderDescriptionError = true;
                $scope.headerForumErrors.subHeaderTitleError = false;
            } else {
                $scope.headerForumErrors.subHeaderTitleError = false;
                $scope.headerForumErrors.subHeaderDescriptionError = false;
                oneheader.addnewsubheader = false;
                var addSubHeaderUrl = $scope.urlAddSubHeader.replace('0', oneheader.id);
                $http.post(addSubHeaderUrl, {'subheader': quicksubheader})
                    .success(function (response) {
                        if (response) {
                            $scope.quicksubheader = {
                                'title': null,
                                'description': null,
                                'admin': false
                            };
                            $scope.getHeaders();

                        }
                    }
                );
            }
        };

        $scope.headerTrue = function () {
            $scope.addnewheader = true;
        };

        $scope.headerFalse = function () {
            $scope.addnewheader = false;
        };


        $scope.deleteHeader = function (header_id) {
            if (header_id) {
                var deleteHeaderUrl = $scope.urlDeleteHeader.replace('0', header_id);
                $http.get(deleteHeaderUrl)
                    .success(function (response) {
                        if (response) {
                            $scope.modal.class = 'label-success';
                            $scope.modal.message = 'Header has been deleted.';
                            $scope.modal.title = 'Success';
                            $('#header_forum_messages_modal').modal('show');
                            $scope.headers = response;
                        } else {
                            somethingWentWrong();
                        }
                    }
                );
            }
        };

        setInterval( function() {
            $scope.getProjectSettingInfo()
        }, 30000);


        $scope.getProjectSettingInfo = function () {
            $http.get($scope.urlGetProjectSettingInfo)
                .success(function (response) {
                    $scope.HO_Contacts = response.HO_Contacts;
                    $scope.All_Contacts = response.All_Contacts;
                    $scope.Project_Team = response.Project_Team;
                    $scope.HFLogs = response.HFLogs;

                    for(var i = 0; i < $scope.HO_Contacts.length; i++)
                    {
                        $scope.HO_Contacts[i].checked = false;
                    }
                    for(i = 0; i < $scope.All_Contacts.length; i++)
                    {
                        $scope.All_Contacts[i].checked = false;
                    }
                    for( i = 0; i < $scope.Project_Team.length; i++)
                    {
                        $scope.Project_Team[i].reSendVisibleButton = false;
                        var one_minute = 1000 * 60;
                        var now = new Date(response.timeNow);
                        var timeRequest = new Date($scope.Project_Team[i].date);
                        var difference_miliseconds = now - timeRequest;
                        difference_miliseconds = difference_miliseconds / one_minute;

                        if(($scope.Project_Team[i].status.id == 1 || $scope.Project_Team[i].status.id == 3)
                            && difference_miliseconds >= 5) {
                            $scope.Project_Team[i].reSendVisibleButton = true;
                        }
                    }
                }
            );
        };

        $scope.contactChecked = function (type, index, array) {
            for(var i = 0; i < array.length; i++) {
                array[i].checked = (i == index);
            }
            $scope.testClickableButton(type, array);
        };


        $scope.SendRequest = function (type, message, array) {
            if(type == 1 && message == '') {
                $scope.headerForumSettingsErrors.HO_Contact_message_Error =true;
                $scope.headerForumSettingsErrors.All_Contact_message_Error =false;
            }
            else if(type == 2 && message == '') {
                $scope.headerForumSettingsErrors.HO_Contact_message_Error =false;
                $scope.headerForumSettingsErrors.All_Contact_message_Error =true;
            } else {
                var user_id = 0;

                for(var i=0; i<array.length; i++)
                {
                    if(array[i].checked)
                    {
                        user_id = array[i].id;
                    }
                }

                $http.post($scope.urlSendProjectRequest,{'message':message,'user_id': user_id})
                    .success(function (response) {
                        if(response == 1)
                        {
                            if(type == 1)
                            {
                                $scope.HO_contact_message ='';
                                $scope.headerForumSettingsErrors.HO_Contact_message_Error =false;
                                $('#send_request_by_HO_contacts').modal('hide');

                            }
                            else if(type == 2)
                            {
                                $scope.All_contact_message ='';
                                $scope.headerForumSettingsErrors.All_Contact_message_Error =false;
                                $('#send_request_by_All_contacts').modal('hide');
                            }
                            $scope.getProjectSettingInfo();
                        }
                    }
                );
            }
        };

        $scope.testClickableButton = function (type,array) {
            for(var i=0; i< array.length; i++)
            {
                if(type == 1) {
                    if(array[i].checked) {
                        $scope.HO_Contacts_test = true;

                        break;
                    } else {
                        $scope.HO_Contacts_test = false;
                    }
                } if(type == 2) {
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
            var urlDeleteProjectRequest = $scope.urlDeleteProjectRequest.replace('requestid',request_id);
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
            $http.post($scope.urlReSendProjectRequest, {
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
                }
            );
        };
    }
]);