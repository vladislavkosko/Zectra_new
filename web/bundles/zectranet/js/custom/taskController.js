var taskController = Zectranet.controller('TaskController', ['$scope', '$http', '$paginator',
    function($scope, $http, $paginator) {
        console.log('Task Controller was loaded');

        $scope.tasks = null;
        $scope.tasks = [];

        $scope.urlGetTasks = JSON_URLS.getTasks;

        $scope.addTask = function (){
            console.log($scope.task);

        };

        $scope.getTasks = function () {
            $http.post($scope.urlGetTasks)
                .success(function (response) {
                    $scope.tasks = response.Tasks;
                    console.log(response.Tasks);
                });
        };
    }]);