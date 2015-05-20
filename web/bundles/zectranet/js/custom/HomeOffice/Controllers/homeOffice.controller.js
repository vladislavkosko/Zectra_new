(function () {
    angular.module('Zectranet.homeOffice').controller('HomeOfficeController', HomeOfficeController);

    HomeOfficeController.$inject = [
        '$scope',
        '$rootScope',
        '$homeOffice',
        'timeService'
    ];

    function HomeOfficeController($scope, $rootScope, $homeOffice, timeService) {
        $scope.conversation = null;
        $scope.conv_id = 0;
        $scope.asset = JSON_URLS.asset;
        $scope.message = '';
        $scope.projectName = '';
        $scope.USER_ID = USER_ID;

        $scope.contactListPromise = null;
        $scope.conversationChatPromise = null;
        $scope.editPostButtonVisible = false;
        $scope.homeOfficeInputs = {
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
            $scope.contactListPromise = $homeOffice.getContactList(conv_id);
            $scope.contactListPromise.then(function (response) {
                response = response.data;
                $rootScope.contacts = response;
                for(var i = 0; i < $rootScope.contacts.length; i++) {
                    $rootScope.contacts[i].checked = false;
                }

                if ($rootScope.contacts.length > 0) {
                    if (conv_id != 0) {
                        $scope.returnConv_id($rootScope.contacts, conv_id);
                        $scope.getConversation($rootScope.contacts[$scope.conv_id].id);
                    }
                    else {
                        $scope.getConversation($rootScope.contacts[0].id);
                    }
                }
            });
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

            $scope.conversationChatPromise = $homeOffice.getConversation(id);
            $scope.conversationChatPromise.then(function(response) {
                $scope.conversation = response.data;
            });
        };

        $rootScope.dynamicChatRefresh = function (id) {
            $scope.getConversation(id);
        };

        $scope.SendConversationMessage = function (message, conv_id, edit) {
            if(message && message != '')
            {
                $scope.homeOfficeInputs.messageError = false;
                $scope.message = '';
                if (edit) {
                    $scope.conversationChatPromise = $homeOffice.editConversationMessage(conv_id, message);
                    $scope.conversationChatPromise.then(function (response) {
                        response = response.data;
                        if(response == 1)
                        {
                            $scope.editPostButtonVisible = false;
                            $scope.editedPost = null;
                            var contact_id = ($scope.conversation.user1.id == $scope.USER_ID)
                                ? $scope.conversation.user2.id
                                : $scope.conversation.user1.id;
                            $scope.getConversation(contact_id);
                            $('#textarea-post').val('');
                        }
                    });
                } else {
                    $scope.conversationChatPromise = $homeOffice.sendConversationMessage(conv_id, message);
                    $scope.conversationChatPromise.then(function (response) {
                        $scope.conversation.messages.push(response.data);
                    });
                }
            }

        };

        $scope.pressEnter = function ($event, message, conv_id) {
            if ($event.keyCode == '13' && !$event.shiftKey && !$event.ctrlKey && $scope.editPostButtonVisible === false) {
                $event.preventDefault();
                $scope.SendConversationMessage(message, conv_id, false);
            }

            else if($event.keyCode == '13' && !$event.shiftKey && !$event.ctrlKey && $scope.editPostButtonVisible === true)
            {
                $event.preventDefault();
                $scope.SendConversationMessage($('#textarea-post').val(), $scope.editedPost.id, true);
            }

            if($event.keyCode == '38' && !$event.shiftKey && !$event.ctrlKey && $('#textarea-post').val() == '')
            {
                var messages = $scope.conversation.messages;
                var messages_user = [];
                for(var i = 0; i < messages.length; i++)
                {
                    if(($scope.conversation.user1.id == $scope.USER_ID)
                        || ($scope.conversation.user2.id == $scope.USER_ID)) {
                        messages_user.push(messages[i]);
                    }
                }
                var last_post = messages_user[messages_user.length-1];
                var one_minute = 1000 * 60;
                var now = timeService.getTime();
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
                else {
                    $('#textarea-post').val('Editing time are gone');
                }
            }
        };
    }
})();