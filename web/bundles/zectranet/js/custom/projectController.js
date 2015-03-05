Zectranet.controller('ProjectController', ['$scope', '$http', '$rootScope',
    function ($scope, $http, $rootScope) {

        $scope.currentProjectId = null;
        $scope.epicStories = null;
        $scope.urlGetEpicStories = JSON_URLS.getEpicStories;
        $scope.urlAddEpicStory = JSON_URLS.addEpicStory;

        $scope.getEpicStories = function (project_id) {
            $scope.currentProjectId = project_id;
            $scope.promiseProject = $http.get($scope.urlGetEpicStories)
                .success(function(response) {
                    $scope.epicStories = response.EpicStories;
                });
        };

        $scope.changeCurrentPage = function(project_id) {
            $scope.urlCurrentProject = project_id;
            $rootScope.initTaskController(project_id);
            $rootScope.initChatController(project_id);
        };

        $scope.addNewEpicStory = function (story) {
            if (story.name && story.description) {
                $('#add_epic_story').modal('hide');
                $scope.promiseProject = $http.post($scope.urlAddEpicStory, {'story': story})
                    .success(function (response) {
                       $scope.epicStories.push(response.EpicStory);
                    });
            }
        };

        console.log('Project Controller was loaded...');
    }]);