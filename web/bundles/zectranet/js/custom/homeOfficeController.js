Zectranet.controller('HomeOfficeController', ['$scope', '$http',
    function($scope, $http) {

        $scope.urlGetContactList = JSON_URLS.getContactList;
        $scope.urlGetConversation = JSON_URLS.getConversation;
        $scope.urlSendConversationMessage = JSON_URLS.urlSendConversationMessage;
        $scope.contacts = [];
        $scope.conversation = null;
        $scope.asset = JSON_URLS.asset;
        $scope.message = '';

        $scope.getContactList = function () {
            $http.get($scope.urlGetContactList)
                .success(function (response) {
                    $scope.contacts = response;
                    for(var i=0; i<$scope.contacts.length;i++ )
                    {
                        $scope.contacts[i].checked = false;
                    }
                }
            );
        };
        
        $scope.getConversation = function (id) {

            for(var i=0; i<$scope.contacts.length;i++ )
            {
                $scope.contacts[i].checked = ($scope.contacts[i].id == id) ;
            }
            $http.get($scope.urlGetConversation.replace('0' , id))
                .success(function (response) {
                    $scope.conversation = response;
                });
        };

        $scope.SendConversationMessage = function (message,conversation_id) {
            $scope.message = '';
            $http.post($scope.urlSendConversationMessage.replace('0',conversation_id), {'message': message})
                .success(function (response) {
                    $scope.conversation.messages.push(response);
                })


        }
    }
]);