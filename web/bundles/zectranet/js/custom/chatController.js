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
        }

    }]);
