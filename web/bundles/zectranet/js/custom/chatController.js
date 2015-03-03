var chatController = Zectranet.controller('ChatController', ['$scope', '$http', '$paginator',
    function($scope, $http, $paginator) {
        console.log('Chat Controller was loaded');

        // ------------ BEGIN OF SCOPE VARIABLES ------------ \\
        {
            $scope.posts = null;
            $scope.message = '';

            $scope.urlAddPost = JSON_URLS.addPost;
            $scope.urlGetPosts = JSON_URLS.getPosts;
        }
        // ------------ END OF SCOPE VARIABLES --------------- \\


        $scope.SendPost = function (message) {
            $scope.message = '';
            $http.post($scope.urlAddPost, {'message': message})
                .success(function (response) {
                    $scope.getPosts(0, 100);
                });
        };

        $scope.getPosts = function (offset, count) {
            $http.post($scope.urlGetPosts, {'offset': offset , 'count': count})
                .success(function (response) {
                   $scope.posts = response.Posts;
                    console.log(response.Posts);
                });
        };

        $scope.generateAsset = function (asset, url) {
            return asset + url;
        };


        //InsertScreenshots ctrl + V
        {
            var atachments = [];
            window.onload = function () {
                document.getElementById('textarea-post').addEventListener('paste', function (event) {

                    var cbd = event.clipboardData;
                    if (cbd.items && cbd.items.length) {
                        var cbi = cbd.items[0];
                        console.log(cbi);
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

    }]);
