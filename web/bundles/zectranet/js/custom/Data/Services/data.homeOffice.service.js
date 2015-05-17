(function() {
    angular.module('Zectranet.data')
        .factory('$homeOffice', homeOfficeService);

    homeOfficeService.$inject = [
        '$http',
        '$q'
    ];

    function homeOfficeService($http, $q) {
        var homeOffice = {
            'getContactList': getContactList,
            'getConversation': getConversation,
            'sendConversationMessage': sendConversationMessage,
            'editConversationMessage': editConversationMessage
        };

        return homeOffice;

        function getContactList(conv_id) {
            var deffered = $q.defer();
            var promise = $http.get(JSON_URLS.getContactList.replace('0', conv_id));
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }

        function getConversation(conv_id) {
            var deffered = $q.defer();
            var promise = $http.get(JSON_URLS.getConversation.replace('0', conv_id));
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }

        function sendConversationMessage(conv_id, message) {
            var deffered = $q.defer();
            var promise = $http.post(JSON_URLS.urlSendConversationMessage.replace('0', conv_id),
                {'message': message}
            );
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }

        function editConversationMessage(conv_id, message) {
            var deffered = $q.defer();
            var promise = $http.post(JSON_URLS.urlEditPost.replace('0', conv_id),
                {'message': message}
            );
            promise.then(function (data) {
                deffered.resolve(data);
            });
            return deffered.promise;
        }
    }
})();