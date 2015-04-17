Zectranet.controller('HomeOfficeController', ['$scope', '$http',
    function($scope, $http) {
        $scope.urlGetContactList = function () {
            $scope.urlGetContactList = JSON_URLS.getContactList;

            $scope.getContactList = function () {
                $http.get($scope.urlGetContactList)
                    .success(function (response) {
                        /// Bla bla bla
                    }
                );
            };
        };
    }
]);