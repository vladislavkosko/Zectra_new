var chatController = Zectranet.controller('ChatController', ['$scope', '$http', '$paginator',
    function($scope, $http, $paginator) {
        console.log('Chat Controller was loaded');

        $scope.posts = null;

        $scope.urlAddPost = JSON_URLS.addPost;
        $scope.urlGetPosts = JSON_URLS.getPosts;

        $scope.SendPost = function (message) {
           $http.post($scope.urlAddPost, {'message': message})
               .success(function (response) {
                    console.log(response);
                   $scope.getPosts(0,100);
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
        }

    }]);
