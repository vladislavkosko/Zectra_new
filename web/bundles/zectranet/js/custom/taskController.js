var taskController = Zectranet.controller('TaskController', ['$scope', '$http', '$rootScope','$paginator',
    function($scope, $http, $rootScope, $paginator) {
        console.log('Task Controller was loaded');

        $scope.tasks = null;
        $scope.urlGetTasks = null;
        $scope.urlAddTask  = null;

        $scope.urlTaskTable = JSON_URLS.urlTaskTable;
        $scope.urlTaskList = JSON_URLS.urlTaskList;
        $scope.urlTaskAgile = JSON_URLS.urlTaskAgile;
        $scope.urlAsset = JSON_URLS.asset;

        $scope.USER_ID = USER_ID;

        $rootScope.initTaskController = function (page_id) {
            $scope.urlGetTasks = JSON_URLS.getTasks.replace('0', page_id);
            $scope.urlAddTask  = JSON_URLS.addTask.replace('0', page_id);
            $scope.getTasks();
        };

        $scope.getTasks = function () {
            $scope.promise = $http.post($scope.urlGetTasks)
                .success(function (response) {
                    $scope.tasks = response.Tasks;
                });
        };

        $scope.generateAsset = function (asset, url) {
            return asset + url;
        };

        $scope.addTask = function (task) {
            if (task && task.Name && task.Description && task.Priority && task.Type
                && task.StartDate && task.EndDate) {

                $http.post($scope.urlAddTask, {'task': task})
                    .success(function (response) {
                        $scope.getTasks();
                    });
            }
        };
    }]);