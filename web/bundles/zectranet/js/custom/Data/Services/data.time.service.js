(function () {
    angular.module('Zectranet.data').factory('timeService', timeService);

    timeService.$inject = ['$interval'];

    function timeService($interval){
        var self = this;
        this.time = new Date(TIME_NOW);

        var timeOut = {
            'getTime': getTime
        };

        $interval(function () {
            self.time = new Date(self.time.getTime() + 1000);
        }, 1000);

        return timeOut;

        function getTime () {
            return self.time;
        }
    }
})();