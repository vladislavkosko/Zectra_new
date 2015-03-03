var taskController = Zectranet.controller('TaskController', ['$scope', '$http', '$paginator',
    function($scope, $http, $paginator) {
        console.log('Task Controller was loaded');

        $scope.tasks = null;
        $scope.task = null;

        $scope.urlGetTasks = JSON_URLS.getTasks;

        $scope.addTask = function(taskName,taskDescription,taskType,taskPriority,taskStartDate,taskEndDate){
            console.log(taskName);
            console.log(taskDescription);
            console.log(taskType);
            console.log(taskPriority);
            console.log(taskStartDate);
            console.log(taskEndDate);
        };

        $scope.getTasks = function () {
            $http.post($scope.urlGetTasks)
                .success(function (response) {
                    $scope.tasks = response.Tasks;
                    console.log(response.Tasks);
                });
        };
    }]);