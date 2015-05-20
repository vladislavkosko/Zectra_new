(function () {
    angular.module('Zectranet.data').factory('$documents', documentsService);

    documentsService.$inject = [
        '$http',
        '$q'
    ];

    function documentsService($http, $q) {
        return {
            getDocuments: function () {
                var deffered = $q.defer();
                var promise = $http.get(JSON_URLS.documents.getDocuments);
                promise.then(function (data) {
                    deffered.resolve(data);
                });
                return deffered.promise;
            }
        };
    }
})();