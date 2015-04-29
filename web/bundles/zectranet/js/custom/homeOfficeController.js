Zectranet.controller('HomeOfficeController', ['$scope', '$http', '$rootScope',
    function($scope, $http, $rootScope) {

        $scope.urlGetContactList = JSON_URLS.getContactList;
        $scope.urlGetConversation = JSON_URLS.getConversation;
        $scope.urlSendConversationMessage = JSON_URLS.urlSendConversationMessage;

        $scope.contacts = [];
        $scope.conversation = null;
        $scope.conv_id = 0;
        $scope.asset = JSON_URLS.asset;
        $scope.message = '';
        $scope.projectName = '';

        $scope.contactListPromise = null;
        $scope.conversationChatPromise = null;

        $scope.homeOfficeInputs =
        {
            'projectNameError': false
        };


        $scope.testInputs = function (projectName) {
            if(projectName == '')
            {
                $scope.homeOfficeInputs.projectNameError = true;
            }
        };

        $scope.getContactList = function (conv_id) {
            $scope.contactListPromise = $http
                .get($scope.urlGetContactList)
                .success(function (response) {
                    $rootScope.contacts = response;
                    for(var i=0; i<$rootScope.contacts.length; i++) {
                        $rootScope.contacts[i].checked = false;
                    }
                    if ($rootScope.contacts.length > 0) {
                        if (conv_id != 0)
                        {
                            $scope.returnConv_id($rootScope.contacts, conv_id);
                            $scope.getConversation($rootScope.contacts[$scope.conv_id].id);
                        }
                        else
                            $scope.getConversation($rootScope.contacts[0].id);
                    }
                }
            );
        };

        $scope.returnConv_id = function(contacts, conv_id)
        {
            for (var i = 0; i < contacts.length; i++)
            {
                if (contacts[i].id == conv_id)
                {
                    $scope.conv_id = i;
                    break;
                }
            }
        };
        
        $scope.getConversation = function (id) {
            for(var i=0; i<$rootScope.contacts.length; i++) {
                $rootScope.contacts[i].checked = ($rootScope.contacts[i].id == id) ;
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

            $scope.conversationChatPromise.then(function () {
                $scope.prepareCountOfNotifications($rootScope.contacts, $scope.contactNotifications);
            });
        };

        $scope.SendConversationMessage = function (message, conversation_id) {
            if(message != '')
            {
                $scope.homeOfficeInputs.messageError = false;
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
            }

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