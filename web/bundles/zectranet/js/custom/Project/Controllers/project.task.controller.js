(function () {
    angular.module('Zectranet').filter('statusFilter', function () {
        function findParent(parentid, tasks) {
            var taskIndex = -1;
            for (var i = 0; i < tasks.length; i++) {
                if (tasks[i].id == parentid) {
                    taskIndex = i; break;
                }
            }
            return taskIndex;
        }

        function findPermission(permissions, user_id) {
            for (var i = 0; i < permissions.length; i++)
            {
                if ((permissions[i].userid == user_id))
                    return permissions[i];
            }
            return null;
        }

        return function (statuses, task, tasks, isOvner, USER_ID) {
            if (isOvner) return statuses;

            if (task.parentid)
                task = tasks[findParent(task.parentid, tasks)];

            if (task.sprint.name === 'none') return statuses;
            if (!angular.isDefined(task.sprint.permissions)) return statuses;

            var permission = findPermission(task.sprint.permissions, USER_ID);
            if (permission) {
                if (permission.enableChangeTaskStatusToSignedOff)
                    return statuses;
                else {
                    for (var i = 0; i < statuses.length; i++) {
                        if (statuses[i].label === 'signed off') {
                            statuses.splice(i, 1);
                            break;
                        }
                    }
                    return statuses;
                }
            }
        }

    }).controller('TaskController', TaskController);

    TaskController.$inject = [
        '$scope',
        '$rootScope',
        '$tasks',
        '$tasksSort',
        '$tasksFilter'
    ];

    function TaskController($scope, $rootScope, $tasks, $tasksSort, $tasksFilter) {

        var self = this;
        this.page_id = null;

        $scope.timeNow = TEMPPARAMS.NOW;
        $scope.USER_ID = TEMPPARAMS.USER_ID;

        $scope.filterFunc = $tasksFilter.filterFunction;
        $scope.filterOptions = {};
        $scope.sprint_id = null;
        $scope.tasks = null;
        $scope.taskStatuses = null;
        $scope.promise = null;

        $scope.taskModel = {
            'id': null, 'name': null, 'description': null, 'type': 1,
            'priority': 1, 'startdate': new Date($scope.timeNow),
            'enddate': new Date($scope.timeNow), 'parent': null,
            'sprintID': null
        };

        $scope.subtask = {
            'id': null, 'name': null, 'description': null, 'type': 1,
            'priority': 1, 'startdate': new Date($scope.timeNow),
            'enddate': new Date($scope.timeNow), 'parent': null,
            'sprintID': null
        };

        $scope.tempUser = {
            'username': 'none', 'name': 'none', 'surname': 'none'
        };

        $rootScope.initTaskController = function (page_id) {
            self.page_id = page_id;
            $scope.getTasks();
        };

        $scope.tablePages = {
            'from': 1,
            'to': 20,
            'itemsPerPage': 20
        };

        function prepareShowStatusLabel(tasks) {
            for (var i = 0; i < tasks.length; i++)
            {
                if (tasks[i].subtasks.length == 0)
                {
                    if (!angular.isDefined(tasks[i].showStatusLabel))
                    {
                        tasks[i].showStatusLabel = false;
                        tasks[i].status.selected = tasks[i].status.id;
                    }
                }
                else
                {
                    if (angular.isDefined(tasks[i].showStatusLabel))
                        delete tasks[i].showStatusLabel;

                    for (var j = 0; j < tasks[i].subtasks.length; j++)
                    {
                        if (!angular.isDefined(tasks[i].subtasks[j].showStatusLabel))
                        {
                            tasks[i].subtasks[j].showStatusLabel = false;
                            tasks[i].subtasks[j].status.selected = tasks[i].subtasks[j].status.id;
                        }
                    }
                }
            }

            return tasks;
        }

        $scope.clickOnStatus = function (showStatus) {
            if (angular.isDefined(showStatus))
                showStatus = true;
            return showStatus;
        };

        $scope.NeedClassHover = function (task) {
            return (angular.isDefined(task.showStatusLabel));
        };

        $scope.changeStatus = function (task) {
            if (task.status.id != task.status.selected) {
                var objTask = { 'id': task.id, 'statusId': task.status.selected };

                $scope.promise = $tasks.changeTaskStatus(objTask);
                $scope.promise.then(function (response) {
                    response = response.data;
                    var tempTask = response.task;
                    task.status.id = tempTask.status.id;
                    task.status.label = tempTask.status.label;
                    task.status.color = tempTask.status.color;

                    if (!task.parentid) {
                        recalculateExistingTask(task, false);
                        $scope.tasks = prepareShowStatusLabel($scope.tasks);
                    } else {
                        recalculateExistingSubTask(task, true);
                    }

                    togglePopups();
                    return task;
                });
            }
        };

        $scope.getTasks = function () {
            $scope.promise = $tasks.getTasks(self.page_id);
            $scope.promise.then(function (response) {
                response = response.data;
                $scope.tasks = response.Tasks;
                $scope.taskStatuses = response.taskStatuses;
                $scope.isOvner = response.isOvner;
                $scope.tasks = $tasksSort.calculateTasksInfo($scope.tasks);
                var data = $tasksFilter.prepareTasks($scope.tasks);
                $scope.tasks = data.tasks;
                $scope.filterFields = data.fieldList;
                $scope.tasks = CalculateTaskNewCommentsCount($rootScope.NOTIFICATIONS, $scope.tasks);
                $scope.tasks = prepareShowStatusLabel($scope.tasks);
                togglePopups();
                dropdownsNoCLose();
            });
        };

        function findPermission(permissions, user_id) {
            for (var i = 0; i < permissions.length; i++)
            {
                if ((permissions[i].userid == user_id))
                    return permissions[i];
            }
            return null;
        }


        $scope.enableAddSubtaskBug = function (task) {
            if (task.sprint.name === 'none') return true;
            if (!angular.isDefined(task.sprint.permissions))
                return false;
            var permission = findPermission(task.sprint.permissions, $scope.USER_ID);
            return (permission && permission.enableAddSubtaskBug);
        };

        function recalculateExistingSubTask(subtask, isNew) {
            var parentIndex = findParent(subtask.parentid, $scope.tasks);
            if (parentIndex != -1) {
                var task = $scope.tasks[parentIndex];
                if (isNew) {
                    task.subtasks.push(subtask);
                }
                $tasksFilter.removeFromUniqueFields(task, false);
                task = $tasksSort.calculateTasksInfo([task])[0];
                $scope.filterFields = $tasksFilter.addToUniueFields(task);
                $scope.tasks[parentIndex] = task;
                $scope.tasks = prepareShowStatusLabel($scope.tasks);

            }
        }

        function recalculateExistingTask(task, isNew) {
            task = $tasksSort.calculateTasksInfo([task])[0];
            var data = $tasksFilter.prepareSingleTask(task, isNew);
            $scope.filterFields = data.fieldList;
            return task;
        }

        $scope.addTask = function (task) {
            if (task && task.name && task.description && task.priority && task.type
                && task.startdate && task.enddate) {
                $('#add_new_task').modal('hide');
                $scope.promise = $tasks.addTask(task, self.page_id);
                $scope.promise.then(function (response) {
                    response = response.data;
                    if (response) {
                        var task = recalculateExistingTask(response, true);
                        $scope.tasks.push(task);
                        $scope.tasks = prepareShowStatusLabel($scope.tasks);
                        togglePopups();
                    }
                });
            }
        };

        $scope.addParentIdToSubTask = function (task) {
            $scope.subtask.parent = task.id;
            $scope.subtask.parentTask = task;
            if ($scope.subtask.parentTask.sprintID != null)
                $scope.subtask.type = '2';
            else
                $scope.subtask.type = '1';
        };

        $scope.addSubTask = function (task) {
            if (task && task.name && task.description && task.priority && task.type
                && task.startdate && task.enddate) {
                $('#add_new_subtask').modal('hide');
                $scope.promise = $tasks.addSubTask(task, self.page_id);
                $scope.promise.then(function (response) {
                    response = response.data;
                    if (response) {
                        recalculateExistingSubTask(response, true);
                        togglePopups();
                    }
                });
            }
        };

        $scope.filterByField = function (filtered, count, property, nested, value, label) {
            if (count > 0 || filtered) {
                var data = $tasksFilter.filterByField(value, $scope.tasks, property, nested, label);
                $scope.tasks = data.tasks;
                $scope.filterFields = data.fieldList;
            }
        };

        function dropdownsNoCLose() {
            setTimeout(function () {
                $('.noclose').click(function (event) {
                    event.stopPropagation();
                });
                return false;
            }, 1000);
        }

        function togglePopups() {
            setTimeout(function () {
                var popovers = $('[data-toggle="popover"]');
                popovers.popover();
                popovers.bind('mouseenter', function () {
                    $(this).popover('show');
                });
                popovers.bind('mouseleave', function () {
                    $(this).popover('hide');
                });
                return false;
            }, 500);
        }

        function commentsCalculate(task) {
            if (task.newCommentsCount) {
                task.newCommentsCount++;
            } else {
                task.newCommentsCount = 1;
            }
            return task;
        }

        function subCommentsCalculate(task) {
            if (task.newSubCommentsCount) {
                task.newSubCommentsCount++;
            } else {
                task.newSubCommentsCount = 1;
            }
            return task;
        }

        function giveNewCommentsCount(tasks, notification) {
            for (var j = 0; j < tasks.length; j++) {
                if (tasks[j].id == notification.destinationid && !tasks[j].parentid) {
                    tasks[j] = commentsCalculate(tasks[j]);
                } else if (tasks[j].subtasks) {
                    var res = findInSubTasks(notification.destinationid, tasks[j].subtasks);
                    if (res.changed) {
                        tasks[j].subtasks = res.subtasks;
                        tasks[j] = subCommentsCalculate(tasks[j]);
                    }
                }
            }
            return tasks;
        }

        function findParent(parentid, tasks) {
            var taskIndex = -1;
            for (var i = 0; i < tasks.length; i++) {
                if (tasks[i].id == parentid) {
                    taskIndex = i; break;
                }
            }
            return taskIndex;
        }

        function findInSubTasks(task_id, subtasks) {
            var changed = false;
            for (var i = 0; i < subtasks.length; i++){
                if (subtasks[i].id == task_id) {
                    subtasks[i] = commentsCalculate(subtasks[i]);
                    changed = true;
                    break;
                }
            }
            return { 'subtasks': subtasks, 'changed': changed };
        }

        function clearOldTasksCount(tasks) {
            for (var i = 0; i < tasks.length; i++) {
                tasks[i].newCommentsCount = 0;
                if (!tasks[i].parentid) {
                    tasks[i].newSubCommentsCount = 0;
                }
                if (tasks[i].subtasks) {
                    tasks[i].subtasks = clearOldTasksCount(tasks[i].subtasks);
                }
            }
            return tasks;
        }

        function CalculateTaskNewCommentsCount(notifications, tasks) {
            if (notifications && tasks) {
                tasks = clearOldTasksCount(tasks);
                for (var i = 0; i < notifications.length; i++) {
                    switch (notifications[i].type) {
                        case "private_message_task":
                            if (notifications[i].user.id == $scope.USER_ID) {
                                tasks = giveNewCommentsCount(tasks, notifications[i]);
                            }
                            break;
                        case "message_task":
                            tasks = giveNewCommentsCount(tasks, notifications[i]);
                            break;
                        default:
                            break;
                    }
                }
            }
            return tasks;
        }

        $rootScope.$watch('NOTIFICATIONS', function() {
            if ($rootScope.NOTIFICATIONS && $scope.tasks) {
                $scope.tasks = CalculateTaskNewCommentsCount($rootScope.NOTIFICATIONS, $scope.tasks);
            }
        });

        $scope.addDeleteTaskId = function (task_id) {
            $scope.taskModel.id = task_id;
        };

        $scope.deleteTask = function (task_id) {
            if (task_id) {
                $scope.promise = $tasks.deleteTask(task_id);
                $scope.promise.then($scope.getTasks);
                $('#delete_task').modal('hide');
            }
        };

        // TODO Check working of this function please
        $scope.addTaskToSprint = function (task, sprint_id) {
            if (task.id && sprint_id) {
                $('#add_task_to_sprint').modal('hide');
                var tasks = []; tasks.push(task);
                $scope.promise = $tasks.addTaskToSprint(tasks, sprint_id);
                $scope.promise.then(function (response) {
                    response = response.data;
                    for (var i = 0; i < response.length; i++) {
                        for (var j = 0; j < $scope.tasks.length; j++) {
                            if ($scope.tasks[j].id == response[i].id) {
                                $scope.tasks[j].sprint = response[i].sprint;
                                $scope.tasks[j].sprintID = response[i].sprint.id;
                                $scope.tasks[j].sprintHref = $tasksSort.assignSprintHref($scope.tasks[j].projectid, $scope.tasks[j].sprint.id);
                                break;
                            }
                        }
                    }
                });
            }
        };

        // TODO Check working of this function please
        $scope.detachTaskFromSprint = function (task, $index) {
            $scope.promise = $tasks.detachTaskFromSprint(task.id);
            $scope.promise.then(function (response) {
                response = response.data;
                for (var i = 0; i < $scope.tasks.length; i++) {
                    if ($scope.tasks[i].id == response.id) {
                        $scope.tasks[i].sprint = {
                            'name': 'none'
                        }
                    }
                }
            });
        };

        $scope.tasksOrderBy = ['-id', null, null, null, null, null, null];
    }

})();