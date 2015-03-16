var chatController = Zectranet.controller('ChatController', ['$scope', '$http', '$rootScope', '$sce', '$paginator','$compile',
    function($scope, $http, $rootScope, $sce, $paginator, $compile) {

        // ------------ BEGIN OF SCOPE VARIABLES ------------ \\
        {
            $scope.posts = null;
            $rootScope.message = '';

            $scope.urlAddPost = JSON_URLS.addPost;
            $scope.urlGetPosts = JSON_URLS.getPosts;
            $scope.InsertScreenshotsInPHP = JSON_URLS.InsertScreenshotsInPHP;
            $scope.urlAsset = JSON_URLS.asset;
            $scope.USER_ID = TEMPPARAMS.USER_ID;
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
                   $scope.posts = preparePosts(response.Posts);
                    $scope.posts.reverse();
                    $('#posts-panel').animate({"scrollTop":$('#posts-panel').height()+3500},1000);

                });
        };

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

            var documents = '';
            for(var i=0;i < $rootScope.DocumentsInChat.length;i++)
            {
                documents = documents + $rootScope.DocumentsInChat[i]
            }
            $('#textarea-post').val('');
          $scope.documentPromise = $http.post($scope.urlAddPost, {'message': message + '<br>' + documents, 'usersForPrivateMessage': usersForPrivateMessage})
                .success(function (response) {
                    $scope.getPosts(0, 100);
                    $rootScope.DocumentsInChat = [];
                    $('#slide-down-menu-screenshots').fadeOut(1500);
                    setTimeout(function () {
                        $('.img-screenshots').remove();
                    }, 1500);

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
                                atachments.push($(im).attr('src'));
                                    if (atachments.length > 0) {
                                        $scope.documentPromise = $http({
                                            method: "POST",
                                            url: $scope.InsertScreenshotsInPHP,
                                            data: atachments
                                        })
                                            .success(function (response) {
                                                if (response.result) {
                                                    var screenshots = response.result;

                                                    for(var i=0;i<screenshots.length;i++)
                                                    {
                                                       var Tags = '<img  src=\"' + $scope.urlAsset + screenshots[i].url + '\" class=\"img-screenshots\" /> ';
                                                        Tags = $compile(Tags)($scope);
                                                        document.getElementById('div-screenshot').style.display = 'block';
                                                        $('#slide-down-menu-screenshots').fadeIn(1500);
                                                        $('#div-screenshot').append(Tags);
                                                        $(Tags).fadeIn(1500);
                                                        atachments = [];
                                                       var a = ' <div style=\" display: inline-block;width: 120px; \"  ><a data-lightbox=\"some\" class=\"doc-show\"  href=\"' + $scope.urlAsset + screenshots[i].url + '\" > ' +
                                                        '<img style=\"display: inline !important;width: 100px;height: 100px;margin: 10px;border: 4px solid #495b79;border-radius: 5%; \" src=\"' + $scope.urlAsset + screenshots[i].url + '\" class=\"zoom-images\" /> ' +
                                                        ' </a> '+
                                                        '<br> <a style=\"width: 100px;white-space: normal; \" download=\"'+ screenshots[i].name + '\"  href=\"' + $scope.urlAsset + screenshots[i].url+ '\">'+
                                                        '<i class=\" fa fa-download \"></i>'
                                                        + ' ' +'<span>'+ screenshots[i].name +'</span> '+
                                                        ' </a></div>';
                                                        $rootScope.DocumentsInChat.push(a);

                                                    }

                                                }
                                            });
                                    }
                            };
                            fr.readAsDataURL(f);
                        }

                    }

                }, false);
            };


        }
        //End InsertScreenshots ctrl + V

        console.log('Chat Controller was loaded');

    }]);
