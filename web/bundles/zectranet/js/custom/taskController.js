var taskController = Zectranet.controller('TaskController', ['$scope', '$http', '$rootScope',
    function($scope, $http, $rootScope) {

        $scope.timeNow = TEMPPARAMS.NOW;
        $scope.USER_ID = TEMPPARAMS.USER_ID;

        $scope.taskModel = {
            'id': null, 'name': null, 'description': null, 'type': 1,
            'priority': 1, 'startdate': new Date($scope.timeNow),
            'enddate': new Date($scope.timeNow), 'parent': null
        };

        $scope.subtask = {
            'id': null, 'name': null, 'description': null, 'type': 1,
            'priority': 1, 'startdate': new Date($scope.timeNow),
            'enddate': new Date($scope.timeNow), 'parent': null
        };

        $scope.tempUser = {
            'username': 'none', 'name': 'none', 'surname': 'none'
        };

        $scope.sprint_id = null;
        $scope.tasks = null;
        $scope.tasksFilter = null;
        $scope.promise = null;
        $scope.taskInfoEdit = null;

        $scope.agileStorySubtasks = [];
        $scope.agileTodoSubtasks = [];
        $scope.agileInProgressSubtasks = [];
        $scope.agileDoneSubtasks = [];

        $scope.urlGetTasks = null;
        $scope.urlAddTask = null;
        $scope.urlAddSubTask = null;
        $scope.urlDeleteTask = JSON_URLS.deleteTask;
        $scope.urlShowTask = JSON_URLS.showTask;
        $scope.urlAddTasksToSprint = JSON_URLS.sprintAddTasks;
        $scope.urlShowSprint = JSON_URLS.showSprint;
        $scope.urlAsset = JSON_URLS.asset;
        $scope.urlgetSingleTask = JSON_URLS.getSingleTask;
        $scope.urlSaveTaskMainInfo = JSON_URLS.saveMainTaskInfo;
        $scope.urlSaveTaskDetailsInfo = JSON_URLS.saveDetailsTaskInfo;

        $rootScope.initTaskController = function (page_id) {
            $scope.urlGetTasks = JSON_URLS.getTasks.replace('0', page_id);
            $scope.urlAddTask = JSON_URLS.addTask.replace('0', page_id);
            $scope.urlAddSubTask = JSON_URLS.addSubTask.replace('0', page_id);
            $scope.getTasks();
        };

        $scope.getTasks = function () {
            $scope.promise = $http.get($scope.urlGetTasks)
                .success(function (response) {
                    $scope.tasks = response.Tasks;
                }
            );

            $scope.promise.then(function () {
                initFilter();
                $scope.tasks = calculateTasksInfo($scope.tasks);
                var tasks = $scope.tasks;
                for (var i = 0; i < tasks.length; i++) {
                    if ($.inArray(tasks[i], $scope.tasksFilter) < 0) {
                        tasks[i].selectedInFilter = {
                            'id': true, 'priority': true,
                            'status': true, 'progress': true,
                            'owner': true, 'assigned': true,
                            'sprint': true
                        };
                        if (!tasks[i].assigned) {
                            tasks[i].assigned = $scope.tempUser;
                        }
                        if (!tasks[i].sprint) {
                            tasks[i].sprint = {'name': 'none'};
                        }
                        if (tasks[i].parentid == null) {
                            initUniqueFilterOptions(tasks[i]);
                        }
                        $scope.tasksFilter.push(tasks[i]);
                        tasks[i].excludedBy = null;
                    }
                }
                $scope.tasks = tasks;
                calculateUniques($scope.tasks);
            });
        };

        $scope.$watch('tasks', function (){
            if ($scope.tasks) {
                $scope.tasks = calculateTasksInfo($scope.tasks);
            }
        });

        //// ------- BEGIN OF PREPARE TASKS FUNCTIONS ------- \\\\
        {
            function calculateTasksInfo (tasks) {
                $scope.agileStorySubtasks = [];
                $scope.agileTodoSubtasks = [];
                $scope.agileInProgressSubtasks = [];
                $scope.agileDoneSubtasks = [];
                tasks = giveTasksHref(tasks);
                for (var i = 0; i < tasks.length; i++) {
                    if (tasks[i].subtasks && tasks[i].subtasks.length > 0) {
                        tasks[i].subtasks = giveSubtaskIndex(tasks[i].subtasks);
                        tasks[i].progress = calculateMeanProgress(tasks[i].subtasks, tasks[i].progress);
                        var estimatedTime = calculateMeanEstimation(tasks[i].subtasks);
                        tasks[i].estimatedHours += estimatedTime.hours;
                        tasks[i].estimatedMinutes += estimatedTime.minutes;
                        tasks[i].status = calculateMeanStatus(tasks[i].subtasks);
                    }
                }
                return tasks;
            }

            function calculateMeanProgress (subtasks, progress) {
                var meanNumber = 0;
                for (var i = 0; i < subtasks.length; i++) {
                    meanNumber += subtasks[i].progress;
                }
                return Math.round((meanNumber + progress) / (subtasks.length + 1));
            }

            function calculateMeanEstimation (subtasks) {
                var estimatedTime = {
                    'hours': 0, 'minutes': 0
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
                    if (subtasks[i].type.id != 2) {
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

        $scope.saveSingleTaskMainInfo = function (task) {
            $scope.mainInfoPromise = $http
                .post($scope.urlSaveTaskMainInfo, { 'task': task });
        };

        $scope.saveSingleTaskDetailsInfo = function (task) {
            if (task.assigned == 'Not Assigned') task.assigned = null;
            $scope.detailsInfoPromise = $http
                .post($scope.urlSaveTaskDetailsInfo, { 'task': task });
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
                        $scope.getTasks();
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
                        $scope.getTasks();
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
                    });
            }
        };

        $scope.addTaskToSprint = function (task, sprint_id) {
            if (task.id && sprint_id) {
                $('#add_task_to_sprint').modal('hide');
                var tasks = []; tasks.push(task);
                $scope.promise = $http
                    .post($scope.urlAddTasksToSprint.replace('0', sprint_id), { 'tasks': tasks })
                    .success(function (response) {
                        if (response.success) {
                            $scope.getTasks();
                        }
                    }
                );
            }
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
                    'sprint': [], 'uniques':
                {
                    'id': [], 'priority': [],
                    'status': [], 'progress': [],
                    'owner': [], 'assigned': [],
                    'sprint': []
                }
                };

                $scope.tasksOrderBy = [null, null, null, null, null, null, null];
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
                initUniquesCount();
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

        console.log('Task Controller was loaded');
    }
]);