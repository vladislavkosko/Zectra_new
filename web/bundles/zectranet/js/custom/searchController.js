Zectranet.controller('SearchController', ['$scope', '$http', '$rootScope',
    function($scope, $http, $rootScope) {
        console.log('SearchController was loaded!');
        $scope.searchInput = '';

        $scope.InputChange = function (searchInput)
        {
            var pageWrapper =  $('#page-wrapper');
                if(searchInput && searchInput.length >= 3 || !isNaN(parseInt(searchInput)))
                {

                    pageWrapper.removeHighlight();
                    setTimeout(function () {
                        pageWrapper.highlight(searchInput);
                    },100);

                }
                else if(searchInput.length < 3)
                {
                    pageWrapper.removeHighlight();
                }


        };


    }
]);
