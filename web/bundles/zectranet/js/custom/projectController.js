Zectranet.controller('ProjectController', ['$scope', '$http', '$rootScope',
    function ($scope, $http, $rootScope) {

        $scope.currentProjectId = null;
        $scope.epicStories = null;
        $scope.projectMembers = null;
        $scope.users = null;

        $scope.urlGetEpicStories = JSON_URLS.getEpicStories;
        $scope.urlAddEpicStory = JSON_URLS.addEpicStory;
        $scope.urlGetProjectMembers = JSON_URLS.getMembers;

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

        $scope.getMembers = function () {
            $scope.membersPromise = $http.get($scope.urlGetProjectMembers)
                .success(function (response) {
                    $scope.projectMembers = response.projectMembers;
                    $scope.users = response.users;
                });
        };

        $scope.addUsersToProject = function () {
            var users_array = $scope.users;
            _.map(users_array, function (user) {
                if (user.selected) {
                    $scope.projectMembers.push(user);
                    $scope.users.splice(user.index, 1);
                }
            });
        };

        $scope.removeFromProject = function (user) {
            $scope.users.push(user);
            $scope.projectMembers.remove(user);
        };

        console.log('Project Controller was loaded...');
    }]);