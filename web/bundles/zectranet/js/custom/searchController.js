Zectranet.controller('SearchController', ['$scope', '$http', '$rootScope',
    function($scope, $http, $rootScope) {
        var miniSearchTemp = {
            'homeOffice': [],
            'HFForums': [],
            'QnAForums': [],
            'Projects': [],
            'total': 0
        };
        var timerHandler = null;
        var urlMiniSearch = JSON_URLS.miniSearch;
        var urlHomeOffice = JSON_URLS.homeOfficeShow;
        var homeOfficeID = userHomeOfficeID;
        var userID = USER_ID;
        $scope.searchInput = '';
        $scope.miniSearchResults = miniSearchTemp;
        $scope.urlMiniSearchThread = JSON_URLS.urlMiniSearchThread;
        $scope.urlMiniSearchThread.replace(0,'HFForumID');
        $scope.urlMiniSearchThread.replace(1,'subHeaderID');
        $scope.urlMiniSearchThread.replace(2,'id');

        function prepareSlug(slug) {
            slug = slug.replace(new RegExp('-', 'gi'), '\\W')
                .replace(new RegExp('-','g'), "\\W")
                .replace(new RegExp('/','g'), "\\W")
                .replace(new RegExp(',','g'), "\\W")
                .replace(new RegExp('_','g'), "\\W")
                .replace(new RegExp(';','g'), "\\W")
                .replace(new RegExp(':','g'), "\\W")
                .replace(new RegExp(' ','g'), "\\W");
            return slug;
        }

        function prepareSearchResults(results) {
            var homeOffice = results.homeOffice;
            for (var i = 0; i < homeOffice.length; i++) {
                homeOffice[i].href = urlHomeOffice.replace('0', homeOfficeID)
                    .replace('conv_id', (homeOffice[i].contact_id));
            }
            var threads = results.HFForums.threads;
            for( i = 0;i < threads.length;i++ ) {
                threads[i].href = $scope.urlMiniSearchThread
                    .replace('HFForumID',threads[i].HFForumID)
                    .replace('subHeaderID',threads[i].subHeaderID)
                    .replace('id',threads[i].id);
            }
            var posts = results.HFForums.posts;
            for( i = 0;i < posts.length;i++ ) {
                posts[i].href = $scope.urlMiniSearchThread
                    .replace('HFForumID',posts[i].HFForumID)
                    .replace('subHeaderID',posts[i].subHeaderID)
                    .replace('id',posts[i].threadID);
            }
            return results;
        }

        function miniSearch(slug, taskSearch) {
            slug = prepareSlug(slug);

            $http.post(urlMiniSearch, { 'slug': slug, 'task': taskSearch })
                .success(function (response) {
                    $scope.miniSearchResults = prepareSearchResults(response);
                    $scope.miniSearchResults.total = response.QnAForums.length;
                    $scope.miniSearchResults.total += response.HFForums.threads.length + response.HFForums.posts.length;
                    $scope.miniSearchResults.total += response.Projects.length;
                    $scope.miniSearchResults.total += response.homeOffice.length;
                    if ($scope.miniSearchResults.total == 0) {
                        $scope.miniSearchResults.total = -1;
                    }
                }
            );
        }

        function highlightSearchWords(searchInput) {
            var pageWrapper =  $('#page-wrapper');
            pageWrapper.removeHighlight();
            setTimeout(function () {
                pageWrapper.highlight(searchInput);
                return false;
            }, 200);
        }

        $scope.InputChange = function (searchInput) {
            if (timerHandler) {
                clearTimeout(timerHandler);
                $scope.miniSearchResults = miniSearchTemp;
            }
            if (searchInput.length == 0) {
                $scope.miniSearchResults = miniSearchTemp;
                return;
            }
            timerHandler = setTimeout(function () {
                if(!isNaN(parseInt(searchInput))) {
                    highlightSearchWords(searchInput);
                    miniSearch(searchInput, true);
                } else if (searchInput && searchInput.length >= 3) {
                    highlightSearchWords(searchInput);
                    miniSearch(searchInput, false);
                } else if(searchInput.length < 3) {
                    var pageWrapper =  $('#page-wrapper');
                    pageWrapper.removeHighlight();
                }
            }, 700);
        };
    }
]);
