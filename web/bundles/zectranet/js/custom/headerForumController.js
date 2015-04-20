Zectranet.controller('HeaderForumController', ['$scope', '$http',
    function($scope, $http) {
        $scope.urlGetHeaders = JSON_URLS.getHeaderForumHeaders;
        $scope.urlAddHeader = JSON_URLS.addHeaderForumHeaders;
        $scope.urlAddSubHeader = JSON_URLS.addSubHeaderForumHeaders;
        $scope.urlDeleteHeader = JSON_URLS.deleteHeaderForumHeaders;
        $scope.urlGetProjectSettingInfo = JSON_URLS.urlGetProjectSettingInfo;

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

        $scope.HO_Contacts = [
            {'username':'User1'},{'username':'User2'},{'username':'User3'}];
        $scope.All_Contacts = [{'username':'User4'},{'username':'User5'},{'username':'User6'}];
        $scope.Project_Team = [{},{},{}];

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
            $http.get(urlGetProjectSettingInfo)
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
    }
]);