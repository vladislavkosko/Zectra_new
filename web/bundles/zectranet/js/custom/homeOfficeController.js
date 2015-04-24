Zectranet.controller('HomeOfficeController', ['$scope', '$http',
    function($scope, $http) {

        $scope.urlGetContactList = JSON_URLS.getContactList;
        $scope.urlGetConversation = JSON_URLS.getConversation;
        $scope.urlSendConversationMessage = JSON_URLS.urlSendConversationMessage;
        $scope.contacts = [];
        $scope.conversation = null;
        $scope.conv_id = null;
        $scope.asset = JSON_URLS.asset;
        $scope.message = '';

        $scope.contactListPromise = null;
        $scope.conversationChatPromise = null;

        $scope.getContactList = function (conv_id) {
            $scope.contactListPromise = $http
                .get($scope.urlGetContactList)
                .success(function (response) {
                    $scope.contacts = response;
                    for(var i=0; i<$scope.contacts.length; i++) {
                        $scope.contacts[i].checked = false;
                    }
                    if ($scope.contacts.length > 0) {
                        if ($scope.conv_id != null)
                        {
                            $scope.returnConv_id($scope.contacts);
                            $scope.getConversation($scope.contacts[$scope.conv_id].id);
                        }
                        else
                            $scope.getConversation($scope.contacts[0].id);
                    }
                }
            );
        };

        $scope.returnConv_id = function(contacts)
        {
            for (var i = 0; i < contacts.length; i++)
            {
                if (contacts[i].id == $scope.conv_id)
                {
                    $scope.conv_id = i;
                    break;
                }
            }
        };
        
        $scope.getConversation = function (id) {
            for(var i=0; i<$scope.contacts.length; i++) {
                $scope.contacts[i].checked = ($scope.contacts[i].id == id) ;
            }
            $scope.conversationChatPromise = $http
                .get($scope.urlGetConversation.replace('0' , id))
                .success(function (response) {
                    $scope.conversation = response;
                    setTimeout(function () {
                        scrollChat();
                        return false;
                    }, 300);
                }
            );
        };

        $scope.SendConversationMessage = function (message, conversation_id) {
            $scope.message = '';
            $scope.conversationChatPromise = $http
                .post($scope.urlSendConversationMessage.replace('0',conversation_id), {'message': message})
                .success(function (response) {
                    $scope.conversation.messages.push(response);
                    setTimeout(function () {
                        scrollChat();
                        return false;
                    }, 300);
                }
            );
        };

        function scrollChat() {
            var chat = $('#conversation-chat');
            chat.animate(
                {
                    'scrollTop': $(this).height() + 500
                }, 1000
            );
        }
    }
]);