var sprintController = Zectranet.controller('SprintController', ['$scope', '$http', '$rootScope',
    function($scope, $http, $rootScope) {

        $scope.urlGetTasks = JSON_URLS.getTasks;
        $scope.urlShowTask = JSON_URLS.showTask;
        $scope.urlAsset = JSON_URLS.asset;

        $scope.totalEstimation = {
            'h': 0,
            'm': 0
        };

        function calcuateTotalEstimation(tasks) {
            var est = {
                'h': 0,
                'm': 0
            };

            for (var i = 0; i < tasks.length; i++) {
                if (!tasks[i].parentid) {
                    est.h += tasks[i].estimatedHours;
                    est.m += tasks[i].estimatedMinutes;
                    for (var j = 0; j < tasks[i].subtasks.length; j++) {
                        est.h += tasks[i].subtasks[j].estimatedHours;
                        est.m += tasks[i].subtasks[j].estimatedMinutes;
                    }
                }
            }

            est.h += est.m / 60;
            est.h = ~~est.h;
            est.m = est.m % 60;
            est.m = ~~est.m;
            return est;
        }

        $scope.getTasks = function () {
            $scope.promise = $http.get($scope.urlGetTasks)
                .success(function (response) {
                    $scope.tasks = $scope.prepareTasks(response.Tasks);
                    $scope.totalEstimation = calcuateTotalEstimation($scope.tasks);
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