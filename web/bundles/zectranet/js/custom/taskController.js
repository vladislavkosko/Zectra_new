var taskController = Zectranet.controller('TaskController', ['$scope', '$http', '$rootScope','$paginator',
    function($scope, $http, $rootScope, $paginator) {

        $scope.task = {
            'id': null, 'name': null, 'description': null, 'type': null,
            'priority': null, 'startdate': null, 'enddate': null, 'parent': null
        };

        $scope.subtask = {
            'name': null, 'description': null, 'type': null, 'priority': null,
            'startdate': null, 'enddate': null, 'parent': null
        };

        $scope.tasks = null;
        $scope.urlGetTasks = null;
        $scope.urlAddTask  = null;
        $scope.urlAddSubTask = null;
        $scope.urlDeleteTask = JSON_URLS.deleteTask;
        $scope.urlShowTask = JSON_URLS.showTask;

        $scope.USER_ID = USER_ID;

        $scope.urlTaskTable = JSON_URLS.urlTaskTable;
        $scope.urlTaskList = JSON_URLS.urlTaskList;
        $scope.urlTaskAgile = JSON_URLS.urlTaskAgile;
        $scope.urlAsset = JSON_URLS.asset;

        $rootScope.initTaskController = function (page_id) {
            $scope.urlGetTasks = JSON_URLS.getTasks.replace('0', page_id);
            $scope.urlAddTask  = JSON_URLS.addTask.replace('0', page_id);
            $scope.urlAddSubTask = JSON_URLS.addSubTask.replace('0', page_id);
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
                $('#add_new_task').modal('hide');
                $scope.promise = $http.post($scope.urlAddTask, {'task': task})
                    .success(function (response) {
                        $scope.getTasks();
                    });
            }
        };
        
        $scope.addParentIdToSubTask = function (parent_id) {
            $scope.subtask.parent = parent_id;
        };
        
        $scope.addSubTask = function (task) {
            if (task && task.name && task.description && task.priority && task.type
                && task.startdate && task.enddate) {
                $('#add_new_subtask').modal('hide');
                $scope.promise = $http.post($scope.urlAddSubTask, {'task': task})
                    .success(function (response) {
                        $scope.getTasks();
                    });
            }
        };

        $scope.addDeleteTaskId = function (task_id) {
            $scope.task.id = task_id;
        };

        $scope.deleteTask = function (task_id) {
            if (task_id) {
                $('#delete_task').modal('hide');
                var url = $scope.urlDeleteTask.replace('0', task_id);
                $scope.promise = $http.post(url, {'task_id': task_id})
                    .success(function (response) {
                        $scope.getTasks();
                    });
            }
        };

        $scope.assignTaskHref = function (task_id) {
            return $scope.urlShowTask.replace('0', task_id);
        };
        
        console.log('Task Controller was loaded');
    }]);