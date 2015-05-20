var chatController = Zectranet.controller('ChatController', ['$scope', '$http', '$rootScope', '$sce', 'timeService',
    function($scope, $http, $rootScope, $sce, timeService) {

        // ------------ BEGIN OF SCOPE VARIABLES ------------ \\
        {
            $scope.posts = null;
            $rootScope.message = '';
            $scope.urlAddPost = JSON_URLS.addPost;
            $scope.urlGetPosts = JSON_URLS.getPosts;
            $scope.urlEditPost = JSON_URLS.editPost;

            $scope.urlAsset = JSON_URLS.asset;
            $scope.USER_ID = TEMPPARAMS.USER_ID;
            $rootScope.DocumentsInChat = [];
            $scope.editPostButtonVisible = false;
            $scope.editedPostID = null;

        }
        // ------------ END OF SCOPE VARIABLES --------------- \\

        $rootScope.initChatController = function (page_id) {
            $scope.urlAddPost = JSON_URLS.addPost.replace('0', page_id);
            $scope.urlGetPosts  = JSON_URLS.getPosts.replace('0', page_id);
            $scope.getPosts(0, 100);
        };

        $scope.getPosts = function GetPosts(offset, count) {
            $scope.promise = $http.post($scope.urlGetPosts, {'offset': offset , 'count': count})
                .success(function (response) {
                   $scope.posts = preparePosts(response.Posts);
                    $scope.posts.reverse();
                    var chatList = $('.chat');
                    setTimeout(function () {
                        var postsPanel = $('#posts-panel');
                        postsPanel.animate(
                            {
                                'scrollTop': $(this).height() + chatList.height() + 500
                            }, 1000
                        );
                        return false;
                    }, 200);

                });
        };

        $rootScope.updateChat = $scope.getPosts;

        $scope.userHref = function (user_id) {

            return JSON_URLS.userPage.replace('0', user_id );

        };

        function preparePosts(posts) {
            for (var i = 0; i < posts.length; i++) {
                posts[i].message = $sce.trustAsHtml(posts[i].message);
            }
            return posts;
        }

        $scope.generateAsset = function (asset, url) {
            return asset + url;
        };

        $scope.SendPost = function (message) {
            $rootScope.message = '';

            var usersForPrivateMessage = $scope.getUsersForPrivateMessage(message);
            $scope.addDocumentsToPost();
            var documents = '';
            for(var i=0; i < $rootScope.DocumentsInChat.length; i++)
            {
                documents = documents + $rootScope.DocumentsInChat[i];
            }
            if($scope.editPostButtonVisible == true)
            {
                $scope.EditPost($('#textarea-post').val() + '<br>' + documents);
            }
            else {

                $('#textarea-post').val('');
                $scope.documentPromise = $http.post($scope.urlAddPost, {
                    'message': message + '<br>' + documents,
                    'usersForPrivateMessage': usersForPrivateMessage
                })
                    .success(function (response) {
                        $scope.getPosts(0, 100);
                        $('#slide-down-menu-screenshots').fadeOut(1500);
                        setTimeout(function () {
                            $('.img-screenshots').remove();
                        }, 1500);
                        $rootScope.DocumentsInChat = [];

                    });
            }
            };

        // ---- generate users for send private message to Email ----

        $scope.getUsersForPrivateMessage = function(msg){

            var regex = new RegExp('@[A-Za-z0-9]{1,20}', 'mig');
            var matches = msg.match(regex);

            if (matches != null){
                for (var i = 0; i < matches.length; i++){
                    matches[i] = matches[i].replace('@', '');
                    if (matches[i].toLowerCase() == 'all'){
                        matches = 'all';
                        break;
                    }
                }
            }

            return matches;
        };

        $scope.isEditable = function($index) {
            var one_minute = 1000 * 60;
            var now = new Date();
            now = now.getTime();
            var timepost = new Date($scope.posts[$index].posted);
            timepost = timepost.getTime();
            var difference_ms = now - timepost;
            difference_ms = difference_ms / one_minute;
            return (difference_ms <= 20 && $scope.posts[$index].user.id == $scope.USER_ID);
        };

        $scope.EditPost = function (post) {
            $http.post($scope.urlEditPost.replace('0',$scope.editedPost.id),{'message': post})
                .success(function(response)
                {
                    if(response == 1)
                    {
                        $scope.editPostButtonVisible = false;
                        $scope.editedPost = null;
                        $scope.getPosts(0,100);
                        $('#textarea-post').val('');
                    }
                })

        };


        $scope.pressEnter = function ($event, message) {
            if ($event.keyCode == '13' && !$event.shiftKey && !$event.ctrlKey && $scope.editPostButtonVisible == false) {
                $event.preventDefault();
                $scope.SendPost($('#textarea-post').val());
            }
            else if($event.keyCode == '13' && !$event.shiftKey && !$event.ctrlKey && $scope.editPostButtonVisible == true)
            {
                $scope.SendPost($('#textarea-post').val());
            }
            if ($event.keyCode == '38' && !$event.shiftKey && !$event.ctrlKey && $('#textarea-post').val() == '') {
                var user_posts = [];
               for(var i=0;i<$scope.posts.length;i++)
               {
                   if($scope.posts[i].user.id == $scope.USER_ID)
                   {
                       user_posts.push($scope.posts[i]);
                   }
               }
                var last_post = user_posts[user_posts.length-1];
                var one_minute = 1000 * 60;
                var now = timeService.getTime();
                now = now.getTime();
                var timepost = new Date(last_post.posted);
                timepost = timepost.getTime();
                var difference_ms = now - timepost;
                difference_ms = difference_ms / one_minute;
                if(difference_ms <= 20) {
                    $('#textarea-post').val(last_post.message);
                    $scope.editPostButtonVisible = true;
                    $scope.editedPost = last_post;
                } else {
                    $('#textarea-post').val('Editing time are gone');
                }
            }

        };

        console.log('Chat Controller was loaded');
    }
]);
