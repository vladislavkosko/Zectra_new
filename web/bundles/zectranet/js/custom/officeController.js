Zectranet.controller('OfficeController', ['$scope', '$http', '$rootScope',
    function ($scope, $http, $rootScope) {

        $scope.urlChangeVisibleState = JSON_URLS.changeVisibleState;
        $scope.urlSaveOfficeMembers = JSON_URLS.saveMembers;
        $scope.urlGetOfficeMembers = JSON_URLS.getMembers;

        $scope.officeVisible = null;

        $scope.changeVisibleState = function (visible) {
            $scope.visiblePromise = $http
                .post($scope.urlChangeVisibleState, { 'visible': visible });
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

        // -------------------- Begin of Office Members Manage --------------------\\
        {
            $scope.getMembers = function () {
                $scope.membersPromise = $http
                    .get($scope.urlGetOfficeMembers)
                    .success(function (response) {
                        $scope.officeMembers = response.officeMembers;
                        $scope.users = response.users;
                    });
            };

            $scope.addUsersToOffice = function () {
                var idsToRemove = [];
                for (var i = 0; i < $scope.users.length; i++) {
                    if ($scope.users[i].selected) {
                        $scope.officeMembers.push($scope.users[i]);
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

            $scope.removeUsersFromOffice = function () {
                var idsToRemove = [];
                for (var i = 0; i < $scope.officeMembers.length; i++) {
                    if ($scope.officeMembers[i].selected) {
                        $scope.users.push($scope.officeMembers[i]);
                        idsToRemove.push($scope.officeMembers[i]);
                    }
                }

                for (i = 0; i < idsToRemove.length; i++) {
                    $scope.officeMembers.splice(findElementById(idsToRemove[i], $scope.officeMembers), 1);
                }

                if (idsToRemove.length > 0) {
                    $scope.saveMembersState();
                }
            };

            $scope.saveMembersState = function () {
                $scope.membersPromise = $http
                    .post($scope.urlSaveOfficeMembers, {'users': $scope.officeMembers})
                    .success(function (response) {
                    }
                );
            };
        }
        // -------------------- End of Office Members Manage ----------------------\\

    }
]);