var taskController = Zectranet.controller('TaskController', ['$scope', '$http', '$paginator',
    function($scope, $http, $paginator) {
        console.log('Task Controller was loaded');

        $scope.tasks = null;

        $scope.urlGetTasks = JSON_URLS.getTasks;

        $scope.getTasks = function () {
            $http.post($scope.urlGetTasks)
                .success(function (response) {
                    $scope.tasks = response.Tasks;
                    console.log(response.Tasks);
                });
        };
    }]);