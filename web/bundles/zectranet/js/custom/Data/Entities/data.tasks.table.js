function TasksTable() {
    var self = this;

    this.constructor = function (tasks) {
        self.tasks = tasks;
        self.paginator = {};
    };

    this.updateData = function (tasks) {
        self.tasks = tasks;
    };

    this.getTasks = function () {
        return self.tasks;
    };
}
