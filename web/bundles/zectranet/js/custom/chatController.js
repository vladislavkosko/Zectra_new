var chatController = Zectranet.controller('ChatController', ['$scope', '$http', '$rootScope', '$sce', '$paginator','$compile',
    function($scope, $http, $rootScope, $sce, $paginator, $compile) {

        // ------------ BEGIN OF SCOPE VARIABLES ------------ \\
        {
            $scope.posts = null;
            $rootScope.message = '';

            $scope.urlAddPost = JSON_URLS.addPost;
            $scope.urlGetPosts = JSON_URLS.getPosts;

            $scope.urlAsset = JSON_URLS.asset;
            $scope.USER_ID = TEMPPARAMS.USER_ID;
            $rootScope.DocumentsInChat = [];

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
            $('#textarea-post').val('');
          $scope.documentPromise = $http.post($scope.urlAddPost, {'message': message + '<br>' + documents, 'usersForPrivateMessage': usersForPrivateMessage})
                .success(function (response) {
                    $scope.getPosts(0, 100);
                    $('#slide-down-menu-screenshots').fadeOut(1500);
                    setTimeout(function () {
                        $('.img-screenshots').remove();
                    }, 1500);
                  $rootScope.DocumentsInChat =[];

                });
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





        console.log('Chat Controller was loaded');

    }]);
