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
                }
            );
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

        $scope.trueorfalse = function($index) {

            var now = new Date();
            var now_times = [];
            now_times.month = now.getMonth();
            now_times.day = now.getDate() ;
            now_times.hour = now.getHours() ;
            now_times.minutes = now.getMinutes();

            var timepost = new Date($scope.posts[$index].posted);
            var post_times = [];

            post_times.month = timepost.getMonth();
            post_times.day = timepost.getDate();
            post_times.hour = timepost.getHours();
            post_times.minutes = timepost.getMinutes();
          if( now_times.month - post_times.month == 0 &&  now_times.day - post_times.day == 0 && now_times.hour - post_times.hour == 0 &&  now_times.minutes - post_times.minutes <= 10 )
          {
              return true;
          }
        };

        $scope.pressEnter = function ($event, message) {
            if ($event.keyCode == '13' && !$event.shiftKey && !$event.ctrlKey) {
                $event.preventDefault();
                $scope.SendPost(message);
            }
        };

        console.log('Chat Controller was loaded');
    }
]);
