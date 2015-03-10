var chatController = Zectranet.controller('ChatController', ['$scope', '$http', '$rootScope','$paginator',
    function($scope, $http, $rootScope, $paginator) {

        // ------------ BEGIN OF SCOPE VARIABLES ------------ \\
        {
            $scope.posts = null;
            $scope.message = '';

            $scope.urlAddPost = JSON_URLS.addPost;
            $scope.urlGetPosts = JSON_URLS.getPosts;

            $scope.urlAsset = JSON_URLS.asset;
            $scope.USER_ID = USER_ID;
        }
        // ------------ END OF SCOPE VARIABLES --------------- \\

        $rootScope.initChatController = function (page_id) {
            $scope.urlAddPost = JSON_URLS.addPost.replace('0', page_id);
            $scope.urlGetPosts  = JSON_URLS.getPosts.replace('0', page_id);
            $scope.getPosts(0, 100);
        };

        $scope.getPosts = function (offset, count) {
            $scope.promise = $http.post($scope.urlGetPosts, {'offset': offset , 'count': count})
                .success(function (response) {
                   $scope.posts = response.Posts;
                });
        };

        $scope.generateAsset = function (asset, url) {
            return asset + url;
        };

        $scope.SendPost = function (message) {
            $scope.message = '';
            var usersForPrivateMessage = $scope.getUsersForPrivateMessage(message);

            $http.post($scope.urlAddPost, {'message': message, 'usersForPrivateMessage': usersForPrivateMessage})
                .success(function (response) {
                    $scope.getPosts(0, 100);
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

        //InsertScreenshots ctrl + V
        {
            var atachments = [];
            window.onload = function () {
                document.getElementById('textarea-post').addEventListener('paste', function (event) {

                    var cbd = event.clipboardData;
                    if (cbd.items && cbd.items.length) {
                        var cbi = cbd.items[0];
                        if (/^image\/(png|gif|jpe?g)$/.test(cbi.type)) {
                            event.stopPropagation();
                            event.preventDefault();
                            var f = cbi.getAsFile();
                            var fr = new FileReader();
                            fr.onload = function () {
                                var im = new Image();
                                im.src = this.result;
                                im.style.display = 'block';
                                im.setAttribute('class', 'img-screenshot');
                                im.setAttribute('onclick', '$(this).remove(DeleteLastScreenshot());');
                                document.getElementById('div-screenshot').style.display = 'block';
                                $('#slide-down-menu-screenshots').fadeIn(1500);
                                document.getElementById('div-screenshot').appendChild(im);
                                $(im).fadeIn(1500);
                                atachments.push($(im).attr('src'));
                            };
                            fr.readAsDataURL(f);
                        }

                    }

                }, false);
            };

            $scope.InsertScreenshotsInChat = function () {

            };
        }
        //End InsertScreenshots ctrl + V

        console.log('Chat Controller was loaded');

    }]);
