var taskController = Zectranet.controller('TaskController', ['$scope', '$http', '$paginator',
    function($scope, $http, $paginator) {
        console.log('Task Controller was loaded');

        $scope.tasks = null;

        $scope.urlGetTasks = JSON_URLS.getTasks;
        $scope.urlAddTask  = JSON_URLS.addTask;

        $scope.addTask = function (task){
            if (task && task.Name && task.Description && task.Priority && task.Type
                && task.StartDate && task.EndDate) {

                $http.post($scope.urlAddTask, {'task': task})
                    .success(function (response) {
                        $scope.getTasks();
                    });
            }
        };

        $scope.getTasks = function () {
            $http.post($scope.urlGetTasks)
                .success(function (response) {
                    $scope.tasks = response.Tasks;
                });
        };

        $scope.generateAsset = function (asset, url) {
            return asset + url;
        };
    }]);