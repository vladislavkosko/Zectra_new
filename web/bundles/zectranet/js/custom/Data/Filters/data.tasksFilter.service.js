(function() {
    angular.module('Zectranet.data')
        .factory('$tasksFilter', tasksFilterService);

    tasksFilterService.$inject = [];

    function tasksFilterService() {
        var self = this;

        this.fieldList = clearFields();

        var tasksFilter = {
            'prepareTasks': prepareTasks,
            'prepareSingleTask': prepareSingleTask,
            'addToUniueFields': addToUniueFields,
            'removeFromUniqueFields': removeFromUniqueFields,
            'filterByField': filterByField,
            'filterFunction': filterFunction
        };

        return tasksFilter;

        function prepareTasks(tasks) {
            self.fieldList = clearFields();
            for (var i = 0; i < tasks.length; i++) {
                addToUniueFields(tasks[i]);
                tasks[i].excludedBy = angular.isDefined(tasks[i].excludedBy)
                    ? tasks[i].excludedBy
                    : null;
            }
            return {
                'tasks': tasks,
                'fieldList': self.fieldList
            };
        }

        function prepareSingleTask(task, isNew) {
            if (!isNew) {
                removeFromUniqueFields(task);
            }
            addToUniueFields(task);
            task.excludedBy = angular.isDefined(task.excludedBy)
                ? task.excludedBy
                : null;
            return {
                'task': task,
                'fieldList': self.fieldList
            };
        }

        function clearFields() {
            var fieldList = {
                'priority': { }, 'status': { },
                'progress': { }, 'owner': { },
                'assigned': { }, 'sprint': { }
            };
            return fieldList;
        }

        function addToUniueFields(task) {
            addProperty(task, 'priority', 'label');
            addProperty(task, 'status', 'label');
            addProperty(task, 'progress');
            addProperty(task, 'owner', 'username');
            addProperty(task, 'assigned', 'username');
            addProperty(task, 'sprint', 'name');
            return self.fieldList;
        }

        function addProperty(object, property, nestedProperty) {
            var field = (angular.isDefined(nestedProperty))
                ? object[property][nestedProperty]
                : object[property];

            if (!(field in self.fieldList[property])) {
                self.fieldList[property][field] = {
                    'label': field,
                    'count': 1,
                    'checked': true,
                    'filtered': false
                }
            } else {
                self.fieldList[property][field].count++;
            }
        }

        function removeFromUniqueFields(task, deleteField) {
            removeProperty(task, 'priority', deleteField, 'label');
            removeProperty(task, 'status', deleteField, 'label');
            removeProperty(task, 'progress', deleteField);
            removeProperty(task, 'owner', deleteField, 'username');
            removeProperty(task, 'assigned', deleteField, 'username');
            removeProperty(task, 'sprint', deleteField, 'name');
            return self.fieldList;
        }

        function removeProperty(object, property, deleteField, nestedProperty) {
            var field = (angular.isDefined(nestedProperty))
                ? object[property][nestedProperty]
                : object[property];

            if (field in self.fieldList[property]) {
                if (self.fieldList[property][field].count > 1 || !deleteField) {
                    self.fieldList[property][field].count--;
                } else if (deleteField){
                    delete self.fieldList[property];
                }
            }
        }

        function filterByField(value, tasks, property, nestedProperty, label) {
            var field = (angular.isDefined(nestedProperty))
                ? self.fieldList[property][nestedProperty]
                : self.fieldList[property];

            if (value === true) {
                for (var i = 0; i < tasks.length; i++) {
                    if (tasks[i].excludedBy === property + '.' + nestedProperty) {
                        tasks[i].excludedBy = null;
                        addToUniueFields(tasks[i], false);
                    }
                }
                self.fieldList[property][nestedProperty].filtered = false;
                self.fieldList[property][nestedProperty].checked = true;
            } else {
                for (var i = 0; i < tasks.length; i++) {
                    var taskField = (angular.isDefined(label))
                        ? tasks[i][property][label]
                        : tasks[i][property];

                    if (tasks[i].excludedBy == null && taskField === field.label) {
                        tasks[i].excludedBy = property + '.' + nestedProperty;
                        removeFromUniqueFields(tasks[i], false);
                    }
                }
                self.fieldList[property][nestedProperty].filtered = true;
                self.fieldList[property][nestedProperty].checked = false;
            }

            return {
                'tasks': tasks,
                'fieldList': self.fieldList
            };
        }

        function filterFunction(task) {
            if (task.excludedBy == null) {
                return task;
            }
        }
    }
})();