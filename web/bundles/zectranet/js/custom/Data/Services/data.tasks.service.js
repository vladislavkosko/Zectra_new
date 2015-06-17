(function() {
    angular.module('Zectranet.data')
        .factory('$tasks', tasksService);

    tasksService.$inject = [
        '$http',
        '$q'
    ];

    function tasksService($http, $q) {

        var tasks = {
            'getTasks': getTasks,
            'getTask': getTask,
            'addTask': addTask,
            'addSubTask': addSubTask,
            'deleteTask': deleteTask,
            'saveTask': saveTask,
            'changeTaskStatus': changeTaskStatus,
            'getSprintTasks': getSprintTasks,
            'addTaskToSprint': addTaskToSprint,
            'detachTaskFromSprint': detachTaskFromSprint,
        };

        return tasks;

        function getTasks(project_id) {
            var deffered = $q.defer();
            var promise = $http.get(JSON_URLS.getTasks.replace('0', project_id));
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }

        function getTask(task_id) {
            var deffered = $q.defer();
            var promise = $http.get(JSON_URLS.getSingleTask.replace('0', task_id));
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }

        function addTask(task, project_id) {
            var deffered = $q.defer();
            var promise = $http.post(JSON_URLS.addTask.replace('0', project_id),
                {'task': task}
            );
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }

        function addSubTask(task, project_id) {
            var deffered = $q.defer();
            var promise = $http.post(JSON_URLS.addSubTask.replace('0', project_id),
                {'task': task}
            );
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }

        function deleteTask(task_id) {
            var deffered = $q.defer();
            var promise = $http.post(JSON_URLS.deleteTask.replace('0', task_id),
                {'task_id': task_id}
            );
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }

        function saveTask(task) {
            var deffered = $q.defer();
            var promise = $http.post(JSON_URLS.saveTaskInfo.replace('0', task.id),
                { 'task': task }
            );
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }

        function changeTaskStatus(task) {
            var deffered = $q.defer();
            var promise = $http.post(JSON_URLS.changeStatusTask.replace('0', task.id),
                { 'objTask': task }
            );
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }

        function addTaskToSprint(tasks, sprint_id) {
            var deffered = $q.defer();
            var promise = $http.post(JSON_URLS.sprintAddTasks.replace('0', sprint_id),
                { 'tasks': tasks }
            );
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }

        // TODO need check for working
        function detachTaskFromSprint(task_id) {
            var deffered = $q.defer();
            var promise = $http.post(JSON_URLS.sprintDetachTask.replace('0', task_id),
                { 'tasks': tasks }
            );
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }

        // TODO need check for working
        function getSprintTasks(sprint_id) {
            var deffered = $q.defer();
            var promise = $http.get(JSON_URLS.getSprintTasks.replace('0', sprint_id));
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }
    }
})();