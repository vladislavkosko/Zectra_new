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
        var urlSearch = JSON_URLS.search;
        var urlHomeOffice = JSON_URLS.homeOfficeShow;
        var urlSearchPage = JSON_URLS.searchPage;
        var homeOfficeID = userHomeOfficeID;
        var userID = USER_ID;
        $scope.searchInput = '';
        $scope.miniSearchResults = miniSearchTemp;
        $scope.urlMiniSearchThread = JSON_URLS.urlMiniSearchHeaderThread
            .replace('0', 'HFForumID')
            .replace('1', 'subHeaderID')
            .replace('2', 'id');
        $scope.urlMiniSearchQnaThread = JSON_URLS.urlMiniSearchQnaThread
            .replace('0', 'forumID' )
            .replace('1', 'threadID' );
        $scope.urlMiniSearchProject = JSON_URLS.urlMiniSearchProject
            .replace('0', 'projectID' );
        $scope.urlMiniSearchQnAForum = JSON_URLS.urlMiniSearchQnAForum
            .replace('0', 'projectID' );
        $scope.urlMiniSearchHeaderForum = JSON_URLS.urlMiniSearchHeaderForum
            .replace('0', 'projectID' );
        $scope.urlMiniSearchTask = JSON_URLS.urlMiniSearchTask
            .replace('0', 'taskID' );

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

            var headerForums = results.HFForums.forums;
            for( i = 0; i < headerForums.length; i++ ) {
                headerForums[i].href = $scope.urlMiniSearchHeaderForum
                    .replace('projectID', headerForums[i].id);
            }

            var headerThreads = results.HFForums.threads;
            for( i = 0;i < headerThreads.length;i++ ) {
                headerThreads[i].href = $scope.urlMiniSearchThread
                    .replace('HFForumID', headerThreads[i].HFForumID)
                    .replace('subHeaderID', headerThreads[i].subHeaderID)
                    .replace('id', headerThreads[i].id);
            }

            var headerPosts = results.HFForums.posts;
            for( i = 0;i < headerPosts.length;i++ ) {
                headerPosts[i].href = $scope.urlMiniSearchThread
                    .replace('HFForumID',headerPosts[i].HFForumID)
                    .replace('subHeaderID',headerPosts[i].subHeaderID)
                    .replace('id',headerPosts[i].threadID);
            }

            var QnAForums = results.QnAForums.forums;
            for( i = 0; i < QnAForums.length; i++ ) {
                QnAForums[i].href = $scope.urlMiniSearchQnAForum
                    .replace('projectID', QnAForums[i].id);
            }

            var QnAThreads = results.QnAForums.threads;
            for( i = 0;i < QnAThreads.length;i++ ) {
                QnAThreads[i].href = $scope.urlMiniSearchQnaThread
                    .replace('forumID', QnAThreads[i].forumID)
                    .replace('threadID', QnAThreads[i].id);
            }

            var QnAPosts = results.QnAForums.posts;
            for( i = 0;i < QnAPosts.length;i++ ) {
                QnAPosts[i].href = $scope.urlMiniSearchQnaThread
                    .replace('forumID', QnAPosts[i].forumID)
                    .replace('threadID', QnAPosts[i].threadID);
            }

            var projects = results.Projects.projects;
            for( i = 0; i < projects.length; i++ ) {
                projects[i].href = $scope.urlMiniSearchProject
                    .replace('projectID', projects[i].id);
            }

            var projectPosts = results.Projects.posts;
            for( i = 0; i < projectPosts.length; i++ ) {
                projectPosts[i].href = $scope.urlMiniSearchProject
                    .replace('projectID', projectPosts[i].projectID);
            }

            var tasks = results.Projects.tasks;
            for( i = 0; i < tasks.length; i++ ) {
                tasks[i].href = $scope.urlMiniSearchTask
                    .replace('taskID', tasks[i].id);
            }

            var tasksPosts = results.Projects.taskPosts;
            for( i = 0; i < tasksPosts.length; i++ ) {
                tasksPosts[i].href = $scope.urlMiniSearchTask
                    .replace('taskID', tasksPosts[i].taskid);
            }
            return results;
        }

        function miniSearch(slug, taskSearch, extended) {
            slug = prepareSlug(slug);

            $scope.searhPromise = $http.post(urlMiniSearch, { 'slug': slug, 'task': taskSearch, 'extended': extended })
                .success(function (response) {
                    $scope.miniSearchResults = prepareSearchResults(response);
                    $scope.miniSearchResults.total = response.QnAForums.threads.length + response.QnAForums.posts.length + response.QnAForums.forums.length;
                    $scope.miniSearchResults.total += response.HFForums.threads.length + response.HFForums.posts.length + response.HFForums.forums.length;
                    $scope.miniSearchResults.total += response.Projects.tasks.length + response.Projects.taskPosts.length + response.Projects.posts.length + response.Projects.projects.length;
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

        $scope.InputChange = function (searchInput, extended) {
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
                    miniSearch(searchInput, true, extended);
                } else if (searchInput && searchInput.length >= 3) {
                    highlightSearchWords(searchInput);
                    miniSearch(searchInput, false, extended);
                } else if(searchInput.length < 3) {
                    var pageWrapper = $('#page-wrapper');
                    pageWrapper.removeHighlight();
                }
            }, 700);
        };

        $scope.searchAction = function (slug) {
            if (slug) {
                location.href = urlSearchPage.replace('slug', slug);
            }
        }
    }
]);
