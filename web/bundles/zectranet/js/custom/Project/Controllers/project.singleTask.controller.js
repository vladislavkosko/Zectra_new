(function () {
    angular.module('Zectranet.project').controller('SingleTaskController', SingleTaskController);

    SingleTaskController.$inject = [
        '$scope',
        '$tasks'
    ];

    function SingleTaskController($scope, $tasks) {

        var self = this;
        this.task_id = TEMPPARAMS.TASK_ID;

        $scope.taskInfoEdit = {};

        $scope.getSingleTask = function () {
            $scope.taskPromise = $tasks.getTask(self.task_id);
            $scope.taskPromise.then(function (response) {
                response = response.data;
                $scope.taskInfoEdit = response.task;
                if ($scope.taskInfoEdit.assigned == null) {
                    $scope.taskInfoEdit.assigned = 'Not Assigned';
                } else {
                    $scope.taskInfoEdit.assigned = $scope.taskInfoEdit.assigned.id;
                }
                $scope.taskInfoEdit.startDate = new Date($scope.taskInfoEdit.startDate);
                $scope.taskInfoEdit.endDate = new Date($scope.taskInfoEdit.endDate);
            });
        };

        $scope.saveSingleTaskInfo = function (task) {
            $scope.taskPromise = $tasks.saveTask(task);
        };

    }
})();