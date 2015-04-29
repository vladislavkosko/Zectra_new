var sprintController = Zectranet.controller('SprintController', ['$scope', '$http', '$rootScope',
    function($scope, $http, $rootScope) {

        $scope.urlGetTasks = JSON_URLS.getTasks;
        $scope.urlShowTask = JSON_URLS.showTask;
        $scope.urlAsset = JSON_URLS.asset;

        $scope.getTasks = function () {
            $scope.promise = $http.get($scope.urlGetTasks)
                .success(function (response) {
                    $scope.tasks = $scope.prepareTasks(response.Tasks);
                }
            );
        };

        $scope.prepareTasks = function (tasks) {
            for (var i = 0; i < tasks.length; i++) {
                tasks[i].href = $scope.assignTaskHref(tasks[i].id);
                if (!tasks[i].assigned) {
                    tasks[i].assigned = { 'name': 'Not', 'surname': 'Assigned' };
                }
                if (tasks[i].subtasks) {
                    tasks[i].subtasks = $scope.prepareTasks(tasks[i].subtasks);
                }
            }
            return tasks;
        };

        $scope.generateAsset = function (asset, url) {
            return asset + url;
        };

        $scope.assignTaskHref = function (task_id) {
            return $scope.urlShowTask.replace('0', task_id);
        };

        console.log('Sprint Controller was loaded...');
    }
]);