(function () {
    angular.module('Zectranet.project').controller('SprintController', SprintController);

    SprintController.$inject = [
        '$scope',
        '$tasks'
    ];

    function SprintController($scope, $tasks) {

        var self = this;
        this.sprint_id = TEMPPARAMS.sprintID;

        $scope.urlGetTasks = JSON_URLS.getTasks;
        $scope.urlShowTask = JSON_URLS.showTask;
        $scope.urlAsset = JSON_URLS.asset;

        $scope.totalEstimation = {
            'h': 0, 'm': 0, 'bh': 0, 'bm': 0
        };
        $scope.totalProgress = 0;

        function calculateTaskTotalEstimation(task) {
            var est = {
                'h': 0, 'm': 0, 'bh': 0, 'bm': 0
            };

            if (task.type.id !== 2) {
                est.h += task.estimatedHours;
                est.m += task.estimatedMinutes;
            } else {
                est.bh += task.estimatedHours;
                est.bm += task.estimatedMinutes;
            }

            return est;
        }

        function calcuateTotalEstimation(tasks) {
            var est = {
                'h': 0, 'm': 0, 'bh': 0, 'bm': 0
            };

            for (var i = 0; i < tasks.length; i++) {
                if (!tasks[i].parentid) {
                    var time = calculateTaskTotalEstimation(tasks[i]);
                    est.h += time.h; est.m += time.m;
                    est.bh += time.bh; est.bm += time.bm;
                    for (var j = 0; j < tasks[i].subtasks.length; j++) {
                        time = calculateTaskTotalEstimation(tasks[i].subtasks[j]);
                        est.h += time.h; est.m += time.m;
                        est.bh += time.bh; est.bm += time.bm;
                    }
                }
            }

            est.bh += est.bm / 60; est.bh = ~~est.bh;
            est.bm = est.m % 60; est.bm = ~~est.bm;

            est.h += est.m / 60; est.h = ~~est.h;
            est.m = est.m % 60; est.m = ~~est.m;
            return est;
        }

        function calculateTotalProgress (tasks) {
            var totalProgress = 0;
            for (var i = 0; i < tasks.length; i++) {
                totalProgress += tasks[i].progress;
            }
            return ~~(totalProgress / tasks.length);
        }

        $scope.getTasks = function () {
            $scope.promise = $tasks.getSprintTasks(self.sprint_id);
            $scope.promise.then(function (response) {
                response = response.data;
                $scope.totalEstimation = calcuateTotalEstimation(response.Tasks);
                $scope.tasks = $scope.prepareTasks(response.Tasks);
                $scope.totalProgress = calculateTotalProgress($scope.tasks);
            });
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
            tasks = calculateTasksInfo(tasks);
            return tasks;
        };

        //// ------- BEGIN OF PREPARE TASKS FUNCTIONS ------- \\\\
        {
            function executeCalculateOperations(task) {
                if (task.subtasks && task.subtasks.length > 0) {
                    task.subtasks = giveSubtaskIndex(task.subtasks);
                    task.progress = calculateMeanProgress(task.subtasks, task.progress);
                    var estimatedTime = calculateMeanEstimation(
                        task.subtasks, task.estimatedHours, task.estimatedMinutes
                    );
                    task.estimatedHours = estimatedTime.hours;
                    task.estimatedMinutes = estimatedTime.minutes;
                    task.status = calculateMeanStatus(task.subtasks);
                }
                return task;
            }

            function calculateTasksInfo (tasks) {
                $scope.agileStorySubtasks = [];
                $scope.agileTodoSubtasks = [];
                $scope.agileInProgressSubtasks = [];
                $scope.agileDoneSubtasks = [];
                tasks = giveTasksHref(tasks);
                for (var i = 0; i < tasks.length; i++) {
                    tasks[i] = executeCalculateOperations(tasks[i]);
                }

                return tasks;
            }

            function calculateMeanProgress (subtasks, progress) {
                var meanNumber = 0;
                for (var i = 0; i < subtasks.length; i++) {
                    meanNumber += subtasks[i].progress;
                }
                return ~~((meanNumber + progress) / (subtasks.length));
            }

            function calculateMeanEstimation (subtasks, hours, minutes) {
                var estimatedTime = {
                    'hours': hours, 'minutes': minutes
                };
                for (var i = 0; i < subtasks.length; i++) {
                    estimatedTime.hours += subtasks[i].estimatedHours;
                    estimatedTime.minutes += subtasks[i].estimatedMinutes;
                }
                estimatedTime.hours += ~~(estimatedTime.minutes / 60);
                estimatedTime.minutes = estimatedTime.minutes % 60;

                return estimatedTime;
            }

            function calculateMeanStatus (subtasks) {
                var statuses = {
                    'story': 0, 'todo': 0,
                    'in_progress': 0, 'done': 0
                };
                for (var i = 0; i < subtasks.length; i++) {
                    switch (subtasks[i].status.id) {
                        case 1:
                            statuses.story++;
                            break;
                        case 2:
                            statuses.todo++;
                            break;
                        case 3:
                            statuses.in_progress++;
                            break;
                        case 4:
                            statuses.done++;
                            break;
                    }
                }
                if (statuses.done == subtasks.length) {
                    return { id: 4, label: 'done', 'color': 'green' };
                } else if (statuses.in_progress > 0) {
                    return { id: 3, label: 'in-progress', 'color': 'violet' };
                } else if (statuses.todo > 0 || statuses.done > 0) {
                    return { id: 2, label: 'todo', 'color': 'blue' };
                } else {
                    return { id: 1, label: 'story', 'color': 'lightgray' };
                }
            }

            function giveSubtaskIndex(subtasks) {
                for (var i = 0; i < subtasks.length; i++) {
                    subtasks[i].subindex = i + 1;
                    switch (subtasks[i].status.id) {
                        case 1:
                            $scope.agileStorySubtasks.push(subtasks[i]);
                            break;
                        case 2:
                            $scope.agileTodoSubtasks.push(subtasks[i]);
                            break;
                        case 3:
                            $scope.agileInProgressSubtasks.push(subtasks[i]);
                            break;
                        case 4:
                            $scope.agileDoneSubtasks.push(subtasks[i]);
                            break;
                    }
                }
                return subtasks;
            }

            function giveTasksHref (tasks) {
                for (var i = 0; i < tasks.length; i++) {
                    tasks[i].href = $scope.assignTaskHref(tasks[i].id);
                    if (tasks[i].assigned) {
                        tasks[i].assigned.avatar = $scope.generateAsset($scope.urlAsset, 'documents/' + tasks[i].assigned.avatar);
                    }
                    if (tasks[i].subtasks.length > 0) {
                        tasks[i].subtasks = giveTasksHref(tasks[i].subtasks);
                    }
                }
                return tasks;
            }
        }
        //// ------- END OF PREPARE TASKS FUNCTIONS --------- \\\\


        $scope.generateAsset = function (asset, url) {
            return asset + url;
        };

        $scope.assignTaskHref = function (task_id) {
            return $scope.urlShowTask.replace('0', task_id);
        };
    }
})();