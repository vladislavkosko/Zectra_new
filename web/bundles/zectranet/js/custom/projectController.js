Zectranet.controller('ProjectController', ['$scope', '$http', '$rootScope',
    function ($scope, $http, $rootScope) {

        $scope.currentProjectId = null;
        $scope.epicStories = null;
        $scope.projectMembers = null;
        $scope.users = null;

        $scope.urlGetEpicStories = JSON_URLS.getEpicStories;
        $scope.urlAddEpicStory = JSON_URLS.addEpicStory;
        $scope.urlGetProjectMembers = JSON_URLS.getMembers;
        $scope.urlSaveProjectMembers = JSON_URLS.saveMembers;

        $scope.getEpicStories = function (project_id) {
            $scope.currentProjectId = project_id;
            $scope.promiseProject = $http
                .get($scope.urlGetEpicStories)
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
                $scope.promiseProject = $http
                    .post($scope.urlAddEpicStory, {'story': story})
                    .success(function (response) {
                       $scope.epicStories.push(response.EpicStory);
                    });
            }
        };

        $scope.getMembers = function () {
            $scope.membersPromise = $http
                .get($scope.urlGetProjectMembers)
                .success(function (response) {
                    $scope.projectMembers = response.projectMembers;
                    $scope.users = response.users;
                });
        };
        
        function findElementById(what, from) {
            var index = -1;
            for (var i = 0; i < from.length; i++) {
                if (what.id == from[i].id) {
                    index = i;
                    break;
                }
            }
            return index;
        }

        $scope.addUsersToProject = function () {
            var idsToRemove = [];
            for (var i = 0; i < $scope.users.length; i++) {
                if ($scope.users[i].selected) {
                    $scope.projectMembers.push($scope.users[i]);
                    idsToRemove.push($scope.users[i]);
                }
            }

            for (i = 0; i < idsToRemove.length; i++) {
                $scope.users.splice(findElementById(idsToRemove[i], $scope.users), 1);
            }
        };

        $scope.removeUsersFromProject = function () {
            var idsToRemove = [];
            for (var i = 0; i < $scope.projectMembers.length; i++) {
                if ($scope.projectMembers[i].selected) {
                    $scope.users.push($scope.projectMembers[i]);
                    idsToRemove.push($scope.projectMembers[i]);
                }
            }

            for (i = 0; i < idsToRemove.length; i++) {
                $scope.projectMembers.splice(findElementById(idsToRemove[i], $scope.projectMembers), 1);
            }
        };

        $scope.saveMembersState = function () {
            $scope.membersPromise = $http
                .post($scope.urlSaveProjectMembers, { 'users': $scope.projectMembers })
                .success(function (response) {

                });
        };

        console.log('Project Controller was loaded...');
    }]);