(function () {
    angular.module('Zectranet').controller('TaskController', TaskController);

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
                var objTask = {
                    'id': task.id,
                    'statusId': task.status.selected
                };

                $scope.promise = $tasks.changeTaskStatus(objTask);
                $scope.promise.then(function (response) {
                    response = response.data;
                    var tempTask = response.task;
                    task.status.id = tempTask.status.id;
                    task.status.label = tempTask.status.label;
                    task.status.color = tempTask.status.color;
                });

                $scope.promise.then(function () {
                    var arr = [];
                    arr.push(task);
                    calculateUniques(task);
                    var tasks = $scope.tasks;
                    if (task.parentid) {
                        for (var i = 0; i < tasks.length; i++) {
                            if (task.parentid == tasks[i].id) {
                                executeCalculateOperations(tasks[i]);
                            }
                        }
                    }
                    return task;
                });
            }
        };

        // todo sort
        $scope.getTasks = function () {
            $scope.promise = $tasks.getTasks(self.page_id);
            $scope.promise.then(function (response) {
                response = response.data;
                $scope.tasks = response.Tasks;
                $scope.taskStatuses = response.taskStatuses;
            });

            $scope.promise.then(function () {
                $scope.tasks = $tasksSort.calculateTasksInfo($scope.tasks);
                var data = $tasksFilter.prepareTasks($scope.tasks);
                $scope.tasks = data.tasks;
                $scope.filterFields = data.fieldList;
                if ($rootScope.NOTIFICATIONS && $scope.tasks) {
                    $scope.tasks = CalculateTaskNewCommentsCount($rootScope.NOTIFICATIONS, $scope.tasks);
                }
                $scope.tasks = prepareShowStatusLabel($scope.tasks);
                togglePopups();
                dropdownsNoCLose();
            });
        };

        $scope.filterByField = function (property, nested, value, label) {
            var data = $tasksFilter.filterByField(value, $scope.tasks, property, nested, label);
            $scope.tasks = data.tasks;
            $scope.filterFields = data.fieldList;
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
            return tasks;
        }

        $rootScope.$watch('NOTIFICATIONS', function() {
            if ($rootScope.NOTIFICATIONS && $scope.tasks) {
                $scope.tasks = CalculateTaskNewCommentsCount($rootScope.NOTIFICATIONS, $scope.tasks);
            }
        });

        // todo sort
        $scope.addTask = function (task) {
            if (task && task.name && task.description && task.priority && task.type
                && task.startdate && task.enddate) {
                $('#add_new_task').modal('hide');
                $scope.promise = $tasks.addTask(task, self.page_id);
                $scope.promise.then(function (response) {
                    response = response.data;
                    if (response) {
                        var arr = [];
                        var task = initTaskFields(response);
                        arr.push(task);
                        calculateUniques(task);
                        executeCalculateOperations(task);
                        addTaskToStatusCategory(task);
                        task.expand = false;
                        $scope.tasks.push(task);
                        var tasks = $scope.tasks;
                        $scope.tasks = prepareShowStatusLabel(tasks);
                        togglePopups();
                    }
                });
            }
        };

        $scope.addParentIdToSubTask = function (parent_id) {
            $scope.subtask.parent = parent_id;
        };

        // todo sort
        $scope.addSubTask = function (task) {
            if (task && task.name && task.description && task.priority && task.type
                && task.startdate && task.enddate) {
                $('#add_new_subtask').modal('hide');
                $scope.promise = $tasks.addSubTask(task, self.page_id);
                $scope.promise.then(function (response) {
                    response = response.data;
                    if (response) {
                        var subtask = initTaskFields(response);
                        calculateUniques(subtask);
                        var parentIndex = -1;
                        for (var i = 0; i < $scope.tasks.length; i++) {
                            if ($scope.tasks[i].id == subtask.parentid) {
                                parentIndex = i;
                            }
                        }
                        if (parentIndex != -1) {
                            var task = $scope.tasks[parentIndex];
                            task.subtasks.push(subtask);
                            var tasks = $scope.tasks;
                            $scope.tasks = prepareShowStatusLabel(tasks);
                            task = executeCalculateOperations(task);
                            $scope.tasks[parentIndex] = task;
                        }
                        addTaskToStatusCategory(subtask);
                        togglePopups();
                    }
                });
            }
        };

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