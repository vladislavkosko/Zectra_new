Zectranet.controller('OfficeArchiveController', ['$scope', '$http', '$rootScope',
    function($scope, $http, $rootScope) {

        var urlGetOfficeArchive = JSON_URLS.getOfficeArchive;
        var urlRestoreFromArchive = JSON_URLS.restoreFromArchive;
        var urlDeleteFromArchive = JSON_URLS.deleteFromArchive;

        $scope.archives = {};
        $scope.logs = null;
        $scope.officeArchivePromise = null;
        $scope.showLogs = false;

        function prepareArchives (archives) {
            for (var i = 0; i < archives.QnAForums.length; i++) {
                archives.QnAForums[i].href = urlRestoreFromArchive.replace('project_id', archives.QnAForums[i].id)
            }

            for (i = 0; i < archives.hfForums.length; i++) {
                archives.hfForums[i].href = urlRestoreFromArchive.replace('project_id', archives.hfForums[i].id)
            }

            for (i = 0; i < archives.projects.length; i++) {
                archives.projects[i].href = urlRestoreFromArchive.replace('project_id', archives.projects[i].id)
            }

            return archives;
        }

        $scope.getArchive = function () {
            $scope.officeArchivePromise = $http
                .get(urlGetOfficeArchive)
                .success(function (response) {
                    if (response.archives)
                        $scope.archives = prepareArchives(response.archives);
                    if (response.logs)
                        $scope.logs = response.logs;
                }
            );
        };

        $scope.deleteFromArchive = function (project_id, project_type, index) {
            $scope.officeArchivePromise = $http
                .post(urlDeleteFromArchive.replace('project_id', project_id), { 'project_type': project_type })
                .success(function () {
                    switch (project_type) {
                        case 1: $scope.archives.QnAForums.splice(index, 1); break;
                        case 2: $scope.archives.hfForums.splice(index, 1); break;
                        case 3: break;
                        case 4: $scope.archives.projects.splice(index, 1); break;
                    }
                }
            );
        };
    }
]);