(function() {
    angular.module('Zectranet.data')
        .factory('$tasksSort', tasksSortService);

    tasksSortService.$inject = [
        '$http',
        '$q'
    ];

    function tasksSortService() {
        var tasksSort = {

        };

        return tasksSort;

        function prepareTasks(tasks) {
            //JSON_URLS.showTask;
            //JSON_URLS.showSprint;
            //JSON_URLS.asset;
        }
    }
})();