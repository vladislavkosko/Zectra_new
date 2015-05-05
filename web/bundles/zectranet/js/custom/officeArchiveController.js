Zectranet.controller('OfficeArchiveController', ['$scope', '$http', '$rootScope',
    function($scope, $http, $rootScope) {

        var urlGetOfficeArchive = JSON_URLS.getOfficeArchive;
        var urlRestoreFromArchive = JSON_URLS.restoreFromArchive;
        var urlDeleteFromArchive = JSON_URLS.deleteFromArchive ;

        $scope.archives = {};

        $scope.getArchive = function () {
            $http.get(urlGetOfficeArchive)
                .success(function (response) {
                    $scope.archives = response;
                }
            );
        };

        $scope.restoreFromArchive = function (project_id, project_type) {
            $http.post(urlRestoreFromArchive.replace('0', project_id), { 'project_type': project_type })
                .success(function (response) {

                }
            );
        };

        $scope.deleteFromArchive = function (project_id, project_type) {
            $http.post(urlDeleteFromArchive.replace('0', project_id), { 'project_type': project_type })
                .success(function (response) {

                }
            );
        };

    }
]);