Intranet.controller('AvatarController', ['$scope', '$http', function($scope, $http) {
    $scope.GenerateAvatar = function (link)
    {
        $http.get(link)
            .success(function (response) {
               location.reload(true);
            });
    }
}]);