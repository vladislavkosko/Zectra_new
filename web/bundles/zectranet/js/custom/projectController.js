Zectranet.controller('ProjectController', ['$scope', '$http', '$rootScope',
    function ($scope, $http, $rootScope) {

        // -------------------- Begin of Scope Variables --------------------\\
        {
            $scope.currentProjectId = null;
            $scope.epicStories = null;

            $scope.projectMembers = null;
            $scope.users = null;

            $scope.projectOffices = null;
            $scope.offices = null;

            $scope.urlGetEpicStories = JSON_URLS.getEpicStories;
            $scope.urlAddEpicStory = JSON_URLS.addEpicStory;
            $scope.urlDeleteEpicStories = JSON_URLS.deleteEpicStories;
            $scope.urlGetProjectMembers = JSON_URLS.getMembers;
            $scope.urlSaveProjectMembers = JSON_URLS.saveMembers;
            $scope.urlgetProjectOffices = JSON_URLS.getOffices;
            $scope.urlAddOffices = JSON_URLS.addOffices;
            $scope.urlRemoveOffices = JSON_URLS.removeOffices;
        }
        // -------------------- End of Scope Variables ----------------------\\



        // -------------------- Begin of Project Functions --------------------\\
        {
            $scope.getEpicStories = function (project_id) {
                $scope.currentProjectId = project_id;
                $scope.promiseProject = $http
                    .get($scope.urlGetEpicStories)
                    .success(function (response) {
                        $scope.epicStories = response.EpicStories;
                    });
            };

            $scope.changeCurrentPage = function (project_id) {
                $scope.urlCurrentProject = project_id;
                $rootScope.initTaskController(project_id);
                $rootScope.initChatController(project_id);
            };

            $scope.highlightCurrentEpicStory = function (epic_story_id) {
                for (var i = 0; i < $scope.epicStories.length; i++) {
                    $scope.epicStories[i].selected = ($scope.epicStories[i].id == epic_story_id);
                }
            };

            $scope.removeHighlightFromEpicStories = function () {
                for (var i = 0; i < $scope.epicStories.length; i++) {
                    $scope.epicStories[i].selected = false;
                }
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
            
            $scope.deleteEpicStories = function () {
                $('#delete_project_epic_story').modal('hide');
                var idsToRemove = [];
                for (var i = 0; i < $scope.epicStories.length; i++) {
                    if ($scope.epicStories[i].selected) {
                        idsToRemove.push($scope.epicStories[i]);
                    }
                }

                for (i = 0; i < idsToRemove.length; i++) {
                    $scope.epicStories.splice(findElementById(idsToRemove[i], $scope.epicStories), 1);
                }

                var ids = [];
                for (i = 0; i < idsToRemove.length; i++) {
                    ids.push(idsToRemove[i].id);
                }

                if (idsToRemove.length > 0) {
                    $scope.promiseProject = $http
                        .post($scope.urlDeleteEpicStories, { 'epicStories': ids });
                }
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
        }
        // -------------------- End of Project Functions ----------------------\\



        // -------------------- Begin of Single Users Manage --------------------\\
        {
            $scope.getMembers = function () {
                $scope.membersPromise = $http
                    .get($scope.urlGetProjectMembers)
                    .success(function (response) {
                        $scope.projectMembers = response.projectMembers;
                        $scope.users = response.users;
                    });
            };

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

                if (idsToRemove.length > 0) {
                    $scope.saveMembersState();
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

                if (idsToRemove.length > 0) {
                    $scope.saveMembersState();
                }
            };

            $scope.saveMembersState = function () {
                $scope.membersPromise = $http
                    .post($scope.urlSaveProjectMembers, {'users': $scope.projectMembers})
                    .success(function (response) {
                    });
            };
        }
        // -------------------- End of Single Users Manage ----------------------\\



        // -------------------- Begin of Offices Manage --------------------\\
        {
            $scope.getOffices = function () {
                $scope.officesPromise = $http
                    .get($scope.urlgetProjectOffices)
                    .success(function (response) {
                        $scope.projectOffices = response.projectOffices;
                        $scope.offices = response.offices;
                    });
            };

            $scope.addOfficesToProject = function () {
                var idsToRemove = [];
                for (var i = 0; i < $scope.offices.length; i++) {
                    if ($scope.offices[i].selected) {
                        $scope.projectOffices.push($scope.offices[i]);
                        idsToRemove.push($scope.offices[i]);
                    }
                }

                for (i = 0; i < idsToRemove.length; i++) {
                    $scope.offices.splice(findElementById(idsToRemove[i], $scope.offices), 1);
                }

                if (idsToRemove.length > 0) {
                    $scope.promiseProject = $http
                        .post($scope.urlAddOffices, { 'offices': idsToRemove });
                }
            };

            $scope.removeOfficesFromProject = function () {
                var idsToRemove = [];
                for (var i = 0; i < $scope.projectOffices.length; i++) {
                    if ($scope.projectOffices[i].selected) {
                        $scope.offices.push($scope.projectOffices[i]);
                        idsToRemove.push($scope.projectOffices[i]);
                    }
                }

                for (i = 0; i < idsToRemove.length; i++) {
                    $scope.projectOffices.splice(findElementById(idsToRemove[i], $scope.projectOffices), 1);
                }

                if (idsToRemove.length > 0) {
                    $scope.promiseProject = $http
                        .post($scope.urlRemoveOffices, { 'offices': idsToRemove });
                }
            };
        }
        // -------------------- End of Offices Manage ----------------------\\

        console.log('Project Controller was loaded...');
    }]);


