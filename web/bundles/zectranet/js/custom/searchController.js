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
        $scope.searchInput = '';
        $scope.miniSearchResults = miniSearchTemp;

        function prepareSlug(slug) {
            slug = slug.replace(new RegExp('-', 'gi'), '%')
                .replace(new RegExp('-','g'), "%")
                .replace(new RegExp('/','g'), "%")
                .replace(new RegExp(',','g'), "%")
                .replace(new RegExp('_','g'), "%")
                .replace(new RegExp(';','g'), "%")
                .replace(new RegExp(':','g'), "%")
                .replace(new RegExp(' ','g'), "%");
            return slug;
        }

        function miniSearch(slug, taskSearch) {
            slug = prepareSlug(slug);

            $http.post(urlMiniSearch, { 'slug': slug, 'task': taskSearch })
                .success(function (response) {
                    $scope.miniSearchResults = response;
                    $scope.miniSearchResults.total = response.QnAForums.length;
                    $scope.miniSearchResults.total += response.HFForums.length;
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
