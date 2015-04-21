Zectranet.controller('HeaderForumController', ['$scope', '$http',
    function($scope, $http) {
        $scope.urlGetHeaders = JSON_URLS.getHeaderForumHeaders;
        $scope.urlAddHeader = JSON_URLS.addHeaderForumHeaders;
        $scope.urlAddSubHeader = JSON_URLS.addSubHeaderForumHeaders;
        $scope.urlDeleteHeader = JSON_URLS.deleteHeaderForumHeaders;
        $scope.urlGetProjectSettingInfo = JSON_URLS.urlGetProjectSettingInfo;
        $scope.urlSendProjectRequest = JSON_URLS.urlSendProjectRequest;

        $scope.headers = null;

        $scope.header = {
            'title': null,
            'bgColor': '#BBBBBB',
            'textColor': '#000000'
        };

        $scope.subheader = {
            'title': null,
            'header_id': null,
            'description': null,
            'admin': false
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

        $scope.getHeaders = function () {
            $http.get($scope.urlGetHeaders)
                .success(function (response) {
                    $scope.headers = response;
                }
            );
        };

        function somethingWentWrong() {
            $scope.modal.class = 'label-danger';
            $scope.modal.message = 'Something went wrong.';
            $scope.modal.title = 'Error';
            $('#header_forum_messages_modal').modal('show');
        }

        $scope.addNewHeader = function (header) {
            if (!header.title || !header.bgColor || !header.textColor) return;
            $http.post($scope.urlAddHeader, { 'header': header })
                .success(function (response) {
                    if (response) {
                        $scope.modal.class = 'label-success';
                        $scope.modal.message = 'Header has been added.';
                        $scope.modal.title = 'Success';
                        $('#header_forum_messages_modal').modal('show');
                        $scope.headers = response;
                    } else {
                        somethingWentWrong();
                    }
                }
            );
        };

        $scope.addNewSubHeader = function (subHeader) {
            var addSubHeaderUrl = $scope.urlAddSubHeader.replace('0', subHeader.header_id);
            $http.post(addSubHeaderUrl, { 'subheader': subHeader })
                .success(function (response) {
                    if (response) {
                        $scope.modal.class = 'label-success';
                        $scope.modal.message = 'Subheader has been added.';
                        $scope.modal.title = 'Success';
                        $('#header_forum_messages_modal').modal('show');
                        $scope.headers = response;
                    } else {
                        somethingWentWrong();
                    }
                }
            );
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

        $scope.getProjectSettingInfo = function () {
            $http.get($scope.urlGetProjectSettingInfo)
                .success(function (response) {
                    $scope.HO_Contacts = response.HO_Contacts;
                    $scope.All_Contacts = response.All_Contacts;
                    $scope.Project_Team = response.Project_Team;

                    for(var i = 0; i < $scope.HO_Contacts.length;i++)
                    {
                        $scope.HO_Contacts[i].checked = false;
                    }
                    for(i = 0; i < $scope.All_Contacts.length;i++)
                    {
                        $scope.All_Contacts[i].checked = false;
                    }

                })
        };

        $scope.contactChecked = function (type,index, array) {
            for(var i=0;i<array.length;i++)
            {
                    array[i].checked = ( i == index) ;
            }
            $scope.testClickableButton(type,array);
        };


        $scope.SendRequest = function (type,message,array)
        {
            var user_id = 0;

            for(var i=0;i<array.length;i++)
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
                            $('#send_request_by_HO_contacts').modal('hide');
                        }
                        else if(type == 2)
                        {
                            $scope.All_contact_message ='';
                            $('#send_request_by_All_contacts').modal('hide');
                        }
                        $scope.getProjectSettingInfo();
                    }
                })
        };

        $scope.testClickableButton = function (type,array) {
            for(var i=0; i< array.length; i++)
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
                    if(array[i].checked)
                    {
                        $scope.All_Contacts_test = true;
                        break;
                    }
                    else
                    {
                        $scope.All_Contacts_test = false;
                    }
                }
            }
        }

    }
]);