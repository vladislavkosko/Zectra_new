(function() {
    angular.module('Zectranet.data')
        .factory('$tasksSort', tasksSortService);

    tasksSortService.$inject = [
        '$http',
        '$q'
    ];

    function tasksSortService() {
        var tasksSort = {
            'calculateTasksInfo': calculateTasksInfo,
            'assignSprintHref': assignSprintHref
        };

        return tasksSort;

        function calculateTasksInfo(tasks) {
            tasks = giveTasksHref(tasks);
            var newTasks = [];
            for (var i = 0; i < tasks.length; i++) {
                if (!tasks[i].parentid) {
                    tasks[i] = executeCalculateOperations(tasks[i]);
                    tasks[i] = initNullFields(tasks[i]);
                    newTasks.push(tasks[i]);
                }
            }
            tasks = newTasks;

            return tasks;
        }

        function initNullFields(task) {
            if (angular.isUndefined(task.assigned) || task.assigned == null) {
                task.assigned = { 'username': 'none', name: 'none', 'surname': 'none' };
            }
            if (angular.isUndefined(task.sprint) || task.sprint == null) {
                task.sprint = { 'name': 'none' };
            }

            return task;
        }

        function executeCalculateOperations(task) {
            if (task.subtasks && task.subtasks.length > 0) {
                task.subtasks = giveSubtaskIndex(task.subtasks);
                task.progress = calculateMeanProgress(task.subtasks);
                var estimatedTime = calculateMeanEstimation(
                    task.subtasks, task.estimatedHours, task.estimatedMinutes
                );
                task.estimatedHours = estimatedTime.hours;
                task.estimatedMinutes = estimatedTime.minutes;
                task.status = calculateMeanStatus(task.subtasks);
            }

            return task;
        }

        function calculateMeanProgress (subtasks) {
            var meanNumber = 0;
            for (var i = 0; i < subtasks.length; i++) {
                meanNumber += subtasks[i].progress;
            }
            return ~~(meanNumber / subtasks.length);
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
                'in_progress': 0, 'done': 0,
                canceled: 0, signedOff: 0
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
                    case 6:
                        statuses.canceled++;
                        break;
                    case 7:
                        statuses.signedOff++;
                        break;
                }
            }
            if(statuses.signedOff == subtasks.length) {
                return { 'id': 7, 'label': 'signed off', 'color': 'darkgreen' };
            } else if (statuses.canceled == subtasks.length) {
                return { 'id': 6, 'label': 'canceled', 'color': 'red' };
            } else if ((statuses.done + statuses.signedOff) == subtasks.length) {
                return { 'id': 4, 'label': 'done', 'color': 'green' };
            } else if (statuses.in_progress > 0) {
                return { 'id': 3, 'label': 'in-progress', 'color': 'violet' };
            } else if (statuses.todo > 0 || statuses.done > 0) {
                return { 'id': 2, 'label': 'todo', 'color': 'blue' };
            } else {
                return { 'id': 1, 'label': 'story', 'color': 'lightgray' };
            }
        }

        function giveSubtaskIndex(subtasks) {
            for (var i = 0; i < subtasks.length; i++) {
                subtasks[i].subindex = i + 1;
            }

            return subtasks;
        }

        function generateAsset(asset, url) {
            return asset + url;
        }

        function assignTaskHref(task_id) {
            return JSON_URLS.showTask.replace('0', task_id);
        }

        function assignSprintHref(project_id, sprint_id) {
            var url = JSON_URLS.showSprint.replace('0', 'project_id').replace('1', 'sprint_id');
            return url.replace('project_id', project_id).replace('sprint_id', sprint_id);
        }

        function giveTasksHref (tasks) {
            for (var i = 0; i < tasks.length; i++) {
                tasks[i].href = assignTaskHref(tasks[i].id);
                if (tasks[i].assigned) {
                    tasks[i].assigned.avatar = generateAsset(JSON_URLS.asset, 'documents/' + tasks[i].assigned.avatar);
                }
                if (tasks[i].sprint) {
                    tasks[i].sprintHref = assignSprintHref(tasks[i].sprint.projectid, tasks[i].sprint.id);
                }
                if (tasks[i].subtasks.length > 0) {
                    tasks[i].subtasks = giveTasksHref(tasks[i].subtasks);
                }
            }
            return tasks;
        }
    }

})();