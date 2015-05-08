var taskController = Zectranet.controller('TaskController', ['$scope', '$http', '$rootScope',
    function($scope, $http, $rootScope) {

        $scope.timeNow = TEMPPARAMS.NOW;
        $scope.USER_ID = TEMPPARAMS.USER_ID;

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

        $scope.sprint_id = null;
        $scope.tasks = null;
        $scope.taskStatuses = null;
        $scope.tasksFilter = null;
        $scope.promise = null;
        $scope.taskInfoEdit = null;

        $scope.agileStorySubtasks = [];
        $scope.agileTodoSubtasks = [];
        $scope.agileInProgressSubtasks = [];
        $scope.agileDoneSubtasks = [];
        $scope.storyTasks = [] ;
        $scope.todoTasks = [] ;
        $scope.inProgresTasks = [] ;
        $scope.doneTasks = [] ;

        $scope.urlGetTasks = null;
        $scope.urlAddTask = null;
        $scope.urlAddSubTask = null;
        $scope.urlDeleteTask = JSON_URLS.deleteTask;
        $scope.urlShowTask = JSON_URLS.showTask;
        $scope.urlAddTasksToSprint = JSON_URLS.sprintAddTasks;
        $scope.urlShowSprint = JSON_URLS.showSprint;
        $scope.urlAsset = JSON_URLS.asset;
        $scope.urlgetSingleTask = JSON_URLS.getSingleTask;
        $scope.urlSaveTaskInfo = JSON_URLS.saveTaskInfo;
        $scope.urlChangeStatusTask = JSON_URLS.changeStatusTask;
        var urlSprintDetachTask = JSON_URLS.sprintDetachTask;

        $rootScope.initTaskController = function (page_id) {
            $scope.urlGetTasks = JSON_URLS.getTasks.replace('0', page_id);
            $scope.urlAddTask = JSON_URLS.addTask.replace('0', page_id);
            $scope.urlAddSubTask = JSON_URLS.addSubTask.replace('0', page_id);
            $scope.getTasks();
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
            var temp = false;
            if (angular.isDefined(task.showStatusLabel))
                temp = true;

            return temp;
        };

        $scope.changeStatus = function (task) {
            if (task.status.id != task.status.selected)
            {
                var objTask = {
                    'id': task.id,
                    'statusId': task.status.selected
                };

                var url = $scope.urlChangeStatusTask.replace('0', task.id);

                $scope.promise =  $http.post(url, {'objTask': objTask})
                    .success(function (response) {
                        var tempTask = response.task;
                        task.status.id = tempTask.status.id;
                        task.status.label = tempTask.status.label;
                        task.status.color = tempTask.status.color;
                    }
                );

                $scope.promise.then(function () {
                    var arr = [];
                    arr.push(task);
                    calculateUniques(task);
                    if (!task.parent) {
                        executeCalculateOperations(task);
                    }
                    return task;
                });
            }
        };

        $scope.getTasks = function () {
            $scope.promise = $http.get($scope.urlGetTasks)
                .success(function (response) {
                    $scope.tasks = response.Tasks;
                    $scope.taskStatuses = response.taskStatuses;
                    separationTasksByStatus(response.Tasks);
                }
            );

            $scope.promise.then(function () {
                initFilter();
                $scope.tasks = calculateTasksInfo($scope.tasks);
                var tasks = $scope.tasks;
                for (var i = 0; i < tasks.length; i++) {
                    tasks[i] = initTaskFields(tasks[i]);
                }
                $scope.tasks = tasks;
                tasks = $scope.tasks;
                $scope.tasks = prepareShowStatusLabel(tasks);
                initUniquesCount();
                calculateUniques($scope.tasks);
                var status = {
                    status: { 'id': 7, 'label': 'signed off', 'color': 'darkgreen' },
                    'checked': false
                };
                var statuses = $scope.uniqueFilterOptions.status;
                $scope.filterByStatus(status);
                for (i = 0; i < statuses.length; i++) {
                    if (statuses[i].status.id == 7) {
                        statuses[i].checked = false;
                        break;
                    }
                }
            });
        };

        function initTaskFields(task) {
            if ($.inArray(task, $scope.tasksFilter) < 0) {
                task.selectedInFilter = {
                    'id': true, 'priority': true,
                    'status': true, 'progress': true,
                    'owner': true, 'assigned': true,
                    'sprint': true
                };
                if (!task.assigned) {
                    task.assigned = $scope.tempUser;
                }
                if (!task.sprint) {
                    task.sprint = {'name': 'none'};
                }
                if (task.parentid == null) {
                    initUniqueFilterOptions(task);
                }
                $scope.tasksFilter.push(task);
                task.excludedBy = null;
                task.expand = false;
            }
            return task;
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

        function separationTasksByStatus(tasks) {
            $scope.storyTasks = [];
            $scope.todoTasks = [];
            $scope.inProgresTasks = [];
            $scope.doneTasks = [];
            $scope.canceledTasks = [];
            $scope.signedOffTasks = [];

            for(var i = 0; i < tasks.length; i++) {
                addTaskToStatusCategory(tasks[i]);
            }
        }

        function addTaskToStatusCategory(task) {
            switch (task.status.label) {
                case 'story':
                    $scope.storyTasks.push(task);
                    break;
                case 'todo':
                    $scope.todoTasks.push(task);
                    break;
                case 'in-progress':
                    $scope.inProgresTasks.push(task);
                    break;
                case 'done':
                    $scope.doneTasks.push(task);
                    break;
                case 'canceled':
                    $scope.canceledTasks.push(task);
                    break;
                case 'signed off':
                    $scope.signedOffTasks.push(task);
                    break;
            }
        }


        //// ------- BEGIN OF PREPARE TASKS FUNCTIONS ------- \\\\
        {
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

            function calculateTasksInfo (tasks) {
                $scope.agileStorySubtasks = [];
                $scope.agileTodoSubtasks = [];
                $scope.agileInProgressSubtasks = [];
                $scope.agileDoneSubtasks = [];
                tasks = giveTasksHref(tasks);
                for (var i = 0; i < tasks.length; i++) {
                    tasks[i] = executeCalculateOperations(tasks[i]);
                }
                if ($rootScope.NOTIFICATIONS && $scope.tasks) {
                    tasks = CalculateTaskNewCommentsCount($rootScope.NOTIFICATIONS, tasks);
                }
                return tasks;
            }

            function calculateMeanProgress (subtasks, progress) {
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
                        case 6:
                            $scope.agileStorySubtasks.push(subtasks[i]);
                            break;
                        case 7:
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

        $scope.getSingleTask = function () {
            $scope.taskPromise = $http
                .get($scope.urlgetSingleTask)
                .success(function (response) {
                    $scope.taskInfoEdit = response.task;
                    if ($scope.taskInfoEdit.assigned == null) {
                        $scope.taskInfoEdit.assigned = 'Not Assigned';
                    } else {
                        $scope.taskInfoEdit.assigned = $scope.taskInfoEdit.assigned.id;
                    }
                    $scope.taskInfoEdit.startDate = new Date($scope.taskInfoEdit.startDate);
                    $scope.taskInfoEdit.endDate = new Date($scope.taskInfoEdit.endDate);
                }
            );
        };

        $scope.saveSingleTaskInfo = function (task) {
            $scope.taskPromise = $http
                .post($scope.urlSaveTaskInfo, { 'task': task });
        };

        $scope.generateAsset = function (asset, url) {
            return asset + url;
        };

        $scope.addTask = function (task) {
            if (task && task.name && task.description && task.priority && task.type
                && task.startdate && task.enddate) {
                $('#add_new_task').modal('hide');
                $scope.promise = $http.post($scope.urlAddTask, {'task': task})
                    .success(function (response) {
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
                        }
                    }
                );
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
                        }
                    }
                );
            }
        };

        $scope.addDeleteTaskId = function (task_id) {
            $scope.taskModel.id = task_id;
        };

        $scope.deleteTask = function (task_id) {
            if (task_id) {
                $('#delete_task').modal('hide');
                var url = $scope.urlDeleteTask.replace('0', task_id);
                $scope.promise = $http.post(url, {'task_id': task_id})
                    .success(function (response) {
                        $scope.getTasks();
                    }
                );
            }
        };

        $scope.addTaskToSprint = function (task, sprint_id) {
            if (task.id && sprint_id) {
                $('#add_task_to_sprint').modal('hide');
                var tasks = []; tasks.push(task);
                $scope.promise = $http
                    .post($scope.urlAddTasksToSprint.replace('0', sprint_id), { 'tasks': tasks })
                    .success(function (response) {
                        for (var i = 0; i < response.length; i++) {
                            for (var j = 0; j < $scope.tasks.length; j++) {
                                if ($scope.tasks[j].id == response[i].id) {
                                    $scope.tasks[j].sprint = response[i].sprint;
                                    break;
                                }
                            }
                        }
                    }
                );
            }
        };

        $scope.detachTaskFromSprint = function (task, $index) {
            $scope.promise = $http.get(urlSprintDetachTask.replace('0', task.id))
                .success(function (response) {
                    for (var i = 0; i < $scope.tasks.length; i++) {
                        if ($scope.tasks[i].id == response.id) {
                            $scope.tasks[i].sprint = {
                                'name': 'none'
                            }
                        }
                    }
                }
            );
        };

        $scope.assignTaskHref = function (task_id) {
            return $scope.urlShowTask.replace('0', task_id);
        };

        $scope.assignSprintHref = function (office_id, sprint_id) {
            var url = $scope.urlShowSprint.replace('0', 'office_id').replace('1', 'sprint_id');
            return url.replace('office_id', office_id).replace('sprint_id', sprint_id);
        };

        // ------------ Begin of filter functions ------------ \\
        {
            $scope.tasksOrderBy = ['-id', null, null, null, null, null, null];

            function initFilter() {
                $scope.tasksFilter =
                    [
                        {
                            'id': 'none',
                            'priority': {'id': 'none', 'label': 'none', 'color': 'none'},
                            'status': {'id': 'none', 'label': 'none', 'color': 'none'},
                            'progress': 'none',
                            'owner': {'username': 'none', 'name': 'none', 'surname': 'none'},
                            'assigned': {'username': 'none', 'name': 'none', 'surname': 'none'},
                            'sprint': {'name': 'none'},
                            'selectedInFilter': true
                        }
                    ];

                $scope.uniqueFilterOptions =
                {
                    'id': [], 'priority': [],
                    'status': [], 'progress': [],
                    'owner': [], 'assigned': [],
                    'sprint': [],
                    'uniques':
                    {
                        'id': [], 'priority': [],
                        'status': [], 'progress': [],
                        'owner': [], 'assigned': [],
                        'sprint': []
                    }
                };

                initUniquesCount();
            }

            $scope.selectAll = function (array, filterFunc) {
                for (var i = 0; i < array.length; i++) {
                    array[i].checked = true;
                    filterFunc(array[i]);
                }
            };

            $scope.clearAll = function (array, filterFunc) {
                for (var i = 0; i < array.length; i++) {
                    array[i].checked = false;
                    filterFunc(array[i]);
                }
            };

            function initUniqueFilterOptions(task) {
                if ($.inArray(task.id, $scope.uniqueFilterOptions.uniques.id) < 0) {
                    $scope.uniqueFilterOptions.uniques.id.push(task.id);
                    $scope.uniqueFilterOptions.id.push({'id': task.id, 'checked': true });
                }
                if ($.inArray(task.priority.label, $scope.uniqueFilterOptions.uniques.priority) < 0) {
                    $scope.uniqueFilterOptions.uniques.priority.push(task.priority.label);
                    $scope.uniqueFilterOptions.priority.push({'priority': task.priority, 'checked': true });
                }
                if ($.inArray(task.status.label, $scope.uniqueFilterOptions.uniques.status) < 0) {
                    $scope.uniqueFilterOptions.uniques.status.push(task.status.label);
                    $scope.uniqueFilterOptions.status.push({'status': task.status, 'checked': true });
                }
                if ($.inArray(task.progress, $scope.uniqueFilterOptions.uniques.progress) < 0) {
                    $scope.uniqueFilterOptions.uniques.progress.push(task.progress);
                    $scope.uniqueFilterOptions.progress.push({'progress': task.progress, 'checked': true });
                }
                if ($.inArray(task.owner.username, $scope.uniqueFilterOptions.uniques.owner) < 0) {
                    $scope.uniqueFilterOptions.uniques.owner.push(task.owner.username);
                    $scope.uniqueFilterOptions.owner.push({'owner': task.owner, 'checked': true });
                }
                if ($.inArray(task.assigned.username, $scope.uniqueFilterOptions.uniques.assigned) < 0) {
                    $scope.uniqueFilterOptions.uniques.assigned.push(task.assigned.username);
                    $scope.uniqueFilterOptions.assigned.push({'assigned': task.assigned, 'checked': true });
                }
                if ($.inArray(task.sprint.name, $scope.uniqueFilterOptions.uniques.sprint) < 0) {
                    $scope.uniqueFilterOptions.uniques.sprint.push(task.sprint.name);
                    $scope.uniqueFilterOptions.sprint.push({'sprint': task.sprint, 'checked': true });
                }
            }

            $scope.filterByID = function (id) {
                for (var i = 0; i < $scope.tasks.length; i++) {
                    if ($scope.tasks[i].id == id.id) {
                        $scope.tasks[i].selectedInFilter.id = id.checked;
                        $scope.tasks[i].excludedBy = (!id.checked) ? 'id' : null;
                    }
                }
                calculateUniques($scope.tasks);
            };

            $scope.filterByPriority = function (priority) {
                for (var i = 0; i < $scope.tasks.length; i++) {
                    if ($scope.tasks[i].priority.label == priority.priority.label) {
                        $scope.tasks[i].selectedInFilter.priority = priority.checked;
                        $scope.tasks[i].excludedBy = (!priority.checked) ? 'priority' : null;
                    }
                }
                calculateUniques($scope.tasks);
            };

            $scope.filterByStatus = function (status) {
                for (var i = 0; i < $scope.tasks.length; i++) {
                    if ($scope.tasks[i].status.id == status.status.id) {
                        $scope.tasks[i].selectedInFilter.status = status.checked;
                        $scope.tasks[i].excludedBy = (!status.checked) ? 'status' : null;
                    }
                }
                calculateUniques($scope.tasks);
            };

            $scope.filterByProgress = function (progress) {
                for (var i = 0; i < $scope.tasks.length; i++) {
                    if ($scope.tasks[i].progress == progress.progress) {
                        $scope.tasks[i].selectedInFilter.progress = progress.checked;
                        $scope.tasks[i].excludedBy = (!progress.checked) ? 'progress' : null;
                    }
                }
                calculateUniques($scope.tasks);
            };

            $scope.filterByOwner = function (owner) {
                for (var i = 0; i < $scope.tasks.length; i++) {
                    if ($scope.tasks[i].owner.id == owner.owner.id) {
                        $scope.tasks[i].selectedInFilter.owner = owner.checked;
                        $scope.tasks[i].excludedBy = (!owner.checked) ? 'owner' : null;
                    }
                }
            };

            $scope.filterByAssigned = function (assigned) {
                for (var i = 0; i < $scope.tasks.length; i++) {
                    if ($scope.tasks[i].assigned.id == assigned.assigned.id) {
                        $scope.tasks[i].selectedInFilter.assigned = assigned.checked;
                        $scope.tasks[i].excludedBy = (!assigned.checked) ? 'assigned' : null;
                    }
                }
            };

            $scope.filterBySprint = function (sprint) {
                for (var i = 0; i < $scope.tasks.length; i++) {
                    if ($scope.tasks[i].sprint.id == sprint.sprint.id) {
                        $scope.tasks[i].selectedInFilter.sprint = sprint.checked;
                        $scope.excludedBy = (!sprint.checked) ? 'sprint' : null;
                    }
                }
            };

            $scope.execTaskFilter = function (obj) {
                if (obj.selectedInFilter && obj.selectedInFilter.id &&
                    obj.selectedInFilter.priority && obj.selectedInFilter.status &&
                    obj.selectedInFilter.progress && obj.selectedInFilter.owner &&
                    obj.selectedInFilter.assigned && obj.selectedInFilter.sprint)
                {
                    return obj;
                }
            };

            function initUniquesCount () {
                $scope.uniqesCount = {
                    'id': {}, 'priority': {},
                    'status': {}, 'progress': {},
                    'owner': {}, 'assigned': {},
                    'sprint': {}
                };
            }

            function pushKeyInArray(key, array) {
                if (key in array) {
                    array[key]++;
                } else {
                    array[key] = 1;
                }
                return array;
            }

            function calculateUniques(tasks) {

                for (var i = 0; i < tasks.length; i++) {
                    if (!tasks[i].excludedBy || tasks[i].excludedBy == 'priority') {
                        $scope.uniqesCount.priority = pushKeyInArray(tasks[i].priority.label, $scope.uniqesCount.priority);
                    }
                    if (!tasks[i].excludedBy || tasks[i].excludedBy == 'status') {
                        $scope.uniqesCount.status = pushKeyInArray(tasks[i].status.label, $scope.uniqesCount.status);
                    }
                    if (!tasks[i].excludedBy || tasks[i].excludedBy == 'progress') {
                        $scope.uniqesCount.progress = pushKeyInArray(tasks[i].progress, $scope.uniqesCount.progress);
                    }
                    if (!tasks[i].excludedBy || tasks[i].excludedBy == 'owner') {
                        $scope.uniqesCount.owner = pushKeyInArray(tasks[i].owner.username, $scope.uniqesCount.owner);
                    }
                    if (!tasks[i].excludedBy || tasks[i].excludedBy == 'assigned') {
                        $scope.uniqesCount.assigned = pushKeyInArray(tasks[i].assigned.username, $scope.uniqesCount.assigned);
                    }
                    if (!tasks[i].excludedBy || tasks[i].excludedBy == 'sprint') {
                        $scope.uniqesCount.sprint = pushKeyInArray(tasks[i].sprint.name, $scope.uniqesCount.sprint);
                    }
                    if (!tasks[i].excludedBy || tasks[i].excludedBy == 'id') {
                        $scope.uniqesCount.id = pushKeyInArray(tasks[i].id, $scope.uniqesCount.id);
                    }
                }
            }

            $scope.filterForID = function (obj) {
                if (obj.id in $scope.uniqesCount.id &&
                    $scope.uniqesCount.id[obj.id] > 0) {
                    return obj;
                }
            };

            $scope.filterForPriority = function (obj) {
                if (obj.priority.label in $scope.uniqesCount.priority &&
                    $scope.uniqesCount.priority[obj.priority.label] > 0) {
                    return obj;
                }
            };

            $scope.filterForStatus = function (obj) {
                if (obj.status.label in $scope.uniqesCount.status &&
                    $scope.uniqesCount.status[obj.status.label] > 0) {
                    return obj;
                }
            };

            $scope.filterForProgress = function (obj) {
                if (obj.progress in $scope.uniqesCount.progress &&
                    $scope.uniqesCount.progress[obj.progress] > 0) {
                    return obj;
                }
            };

            $scope.filterForOwner = function (obj) {
                if (obj.owner.username in $scope.uniqesCount.owner &&
                    $scope.uniqesCount.owner[obj.owner.username] > 0) {
                    return obj;
                }
            };

            $scope.filterForAssigned = function (obj) {
                if (obj.assigned.username in $scope.uniqesCount.assigned &&
                    $scope.uniqesCount.assigned[obj.assigned.username] > 0) {
                    return obj;
                }
            };

            $scope.filterForSprint = function (obj) {
                if (obj.sprint.name in $scope.uniqesCount.sprint &&
                    $scope.uniqesCount.sprint[obj.sprint.name] > 0) {
                    return obj;
                }
            };

        }
        // ------------ End of filter functions -------------- \\


        $scope.onDrop = function($event,type){
            $scope.taskstatusForDragAndDrop = '';
            $scope.taskStatusForDragAndDrop = type;
        };

        $scope.dropSuccessHandler = function($event,$index,task){
            var draggebletask = null;

            switch (task.status.label)
            {
                case 'story':
                    draggebletask = $scope.storyTasks[$index];
                    $scope.storyTasks.splice($index,1);
                    break;
                case 'todo':
                    draggebletask = $scope.todoTasks[$index];
                    $scope.todoTasks.splice($index,1);
                    break;
                case 'in-progress':
                    draggebletask = $scope.inProgresTasks[$index];
                    $scope.inProgresTasks.splice($index,1);
                    break;
                case 'done':
                    draggebletask = $scope.doneTasks[$index];
                    $scope.doneTasks.splice($index,1);
                    break;
            }
            switch ($scope.taskStatusForDragAndDrop)
            {
                case 'story':
                    draggebletask.status.label = $scope.taskStatusForDragAndDrop;
                    draggebletask.status.id = 1;
                    $scope.storyTasks.push(draggebletask);
                    break;
                case 'todo':
                    draggebletask.status.label = $scope.taskStatusForDragAndDrop;
                    draggebletask.status.id = 2;
                    $scope.todoTasks.push(draggebletask);
                    break;
                case 'in-progress':
                    draggebletask.status.label = $scope.taskStatusForDragAndDrop;
                    draggebletask.status.id = 3;
                    $scope.inProgresTasks.push(draggebletask);
                    break;
                case 'done':
                    draggebletask.status.label = $scope.taskStatusForDragAndDrop;
                    draggebletask.status.id = 4;
                    $scope.doneTasks.push(draggebletask);
                    break;
            }

            $scope.urlSaveTaskInfo = $scope.urlSaveTaskInfo.replace('0', draggebletask.id);
            $scope.promise =  $http.post($scope.urlSaveTaskInfo, { 'task': draggebletask })
                .success(function () {
                    $scope.getTasks();
                }
            );
        };
    }
]);