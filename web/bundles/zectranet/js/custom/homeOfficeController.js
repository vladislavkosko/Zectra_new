Zectranet.controller('HomeOfficeController', ['$scope', '$http', '$rootScope',
    function($scope, $http, $rootScope) {

        $scope.urlGetContactList = JSON_URLS.getContactList;
        $scope.urlGetConversation = JSON_URLS.getConversation;
        $scope.urlSendConversationMessage = JSON_URLS.urlSendConversationMessage;
        $scope.urlEditPost = JSON_URLS.urlEditPost;

        $scope.conversation = null;
        $scope.conv_id = 0;
        $scope.asset = JSON_URLS.asset;
        $scope.message = '';
        $scope.projectName = '';
        $scope.USER_ID = USER_ID;

        $scope.contactListPromise = null;
        $scope.conversationChatPromise = null;
        $scope.editPostButtonVisible = false;
        $scope.homeOfficeInputs =
        {
            'projectNameError': false
        };
        $scope.editedPost = null;

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

        $scope.getConversation = function GetConversation(id) {
            for(var i = 0; i < $rootScope.contacts.length; i++) {
                $rootScope.contacts[i].checked = ($rootScope.contacts[i].id == id) ;
                if($rootScope.contacts[i].checked == true)
                {
                    $rootScope.ContactID = $rootScope.contacts[i].id;
                    if ($scope.$parent.notificationsLength) {
                        $scope.$parent.notificationsLength -= $scope.contacts[i].notificationsLength;
                    }
                }
            }
            $scope.conversationChatPromise = $http
                .get($scope.urlGetConversation.replace('0' , id))
                .success(function (response) {
                    $scope.conversation = response;
                }
            );
        };

        $rootScope.dynamicChatRefresh = function (id) {
            $scope.getConversation(id);
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
                        /*setTimeout(function () {
                            scrollChat();
                            return false;
                        }, 300);*/
                    }
                );
            }

        };

        $scope.pressEnter = function ($event, message, conv_id) {
            if ($event.keyCode == '13' && !$event.shiftKey && !$event.ctrlKey && $scope.editPostButtonVisible == false) {
                $event.preventDefault();
                $scope.SendConversationMessage(message, conv_id);
            }
            else if($event.keyCode == '13' && !$event.shiftKey && !$event.ctrlKey && $scope.editPostButtonVisible == true)
            {
                $scope.EditMessage($('#textarea-post').val());
            }
        };

        $scope.EditMessage = function(message)
        {
            $http.post($scope.urlEditPost.replace('0',$scope.editedPost.id),{'message': message})
                .success(function(response)
                {
                    if(response == 1)
                    {
                        $scope.editPostButtonVisible = false;
                        $scope.editedPost = null;
                        $scope.getConversation($scope.conversation.id);
                        $('#textarea-post').val('');
                    }
                })
        };

        $scope.testEditPostButtonVisible = function($event)
        {
            if($event.keyCode == '38' && !$event.shiftKey && !$event.ctrlKey && $('#textarea-post').val() == '')
            {
                var messages = $scope.conversation.messages;
                var messages_user = [];
                for(var i = 0; i < messages.length; i++)
                {
                    if($scope.conversation.user1.id == $scope.USER_ID)
                    {
                        messages_user.push(messages[i]);
                    }
                }
                var last_post = messages_user[messages_user.length-1];
                var one_minute = 1000 * 60;
                var now = new Date();
                now = now.getTime();
                var timepost = new Date(last_post.posted);
                timepost = timepost.getTime();
                var difference_ms = now - timepost;
                difference_ms = difference_ms / one_minute;
                if(difference_ms <= 20)
                {

                    $('#textarea-post').val(last_post.message);
                    $scope.editPostButtonVisible = true;
                    $scope.editedPost = last_post;

                }
                else{
                    $('#textarea-post').val('Editing time are gone');
                }

            }


        }
    }
]);