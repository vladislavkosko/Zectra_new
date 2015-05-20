var Zectranet = angular.module('Zectranet', [
    'ngRoute',
    'ui.bootstrap',
    'ngSanitize',
    'ngAnimate',
    'cgBusy',
    'ang-drag-drop',
    'luegg.directives',
    'ngMessages',
    'Zectranet.data',
    'Zectranet.project',
    'Zectranet.homeOffice'
])
	.config(['$interpolateProvider', '$httpProvider',
		function ($interpolateProvider, $httpProvider) {
			$interpolateProvider.startSymbol('[[');
			$interpolateProvider.endSymbol(']]');
			$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
		}
	]
);

Zectranet.filter('range', function() {
    return function(array, from, to) {
        from--;
        to = (array.length < to) ? array.length : to - 1;
        array = array.slice(from, to);
        return array;
    };
});

Zectranet.directive('document', function() {
	return {
		restrict: 'E',
		priority: 5000,
		transclude: true,
		templateUrl: function(element, attrs) {
			return JSON_URLS.documentTemplate;
		},
		link: function($scope, element, attrs) {
			var extensions = {
				'.doc': 'icons/DOC.png', '.docx': 'icons/DOCX.png', '.xlsx': 'icons/XLSX.png',
				'.avi': 'icons/AVI.png', '.pdf': 'icons/PDF.png', '.mp3': 'icons/MP3.png',
				'.zip': 'icons/ZIP.png', '.txt': 'icons/TXT.png', '.xml': 'icons/XML.png',
				'.xps': 'icons/XPS.png', '.rtf': 'icons/RTF.png', '.odt': 'icons/ODT.png',
				'.htm': 'icons/HTM.png', '.html': 'icons/HTML.png', '.ods': 'icons/ODS.png'
			};

			var regex = new RegExp('[.][A-Za-z0-9]{3,4}', '');
			var nameRegex = new RegExp('[A-Za-z0-9_-]{1,40}[.][A-Za-z0-9]{3,4}', '');
			var file = attrs.file;
			var extension = file.match(regex);
			$scope.filename = file.match(nameRegex)[0];

			var image = element.find('.zoom-images');
			image.css('object-fit', 'cover');
			image.css('width', attrs.width);
			image.css('height', attrs.height);
			image.css('top', 'auto');
			image.css('left', 'auto');
			image.css('margin', 'auto');

			if (extension != '.png' && extension != '.gif' && extension != '.jpeg' && extension != '.jpg') {
				$scope.image = JSON_URLS.asset + 'bundles/zectranet/' + extensions[extension];
				$scope.download = JSON_URLS.asset + file;
			} else {
				$scope.image = JSON_URLS.asset + file;
				$scope.download = JSON_URLS.asset + file;
			}
		}
	}
});

Zectranet.directive('highlight', ['$sce', function($sce) {
	return {
		restrict: 'A',
		link: function($scope, element, attrs) {
			var username = true;

			var message = $sce.getTrustedHtml($scope.post.message);

			regex = new RegExp('@all', 'mig');
			matches = message.match(regex);
			if (matches) {
				username = false;
				message = message.replace(regex,
					'<span class="global-msg-highlight">' + matches[0] + '</span>');
			}

			var msg = message;
			if (username) {
				var regex = new RegExp('@[A-Za-z]{1,20}', 'mig');
				var matches = msg.match(regex);
				if (matches != null) {
					for (var i = 0; i < matches.length; i++) {
						msg = msg.replace(matches[i],
							'<span class="user-names-highlight">' + matches[i] + '</span>');
					}
					message = msg;
				}
			}
			$scope.post.message = $sce.trustAsHtml(message);
			//$scope.post.message = message;
		}
	}
}]);

Zectranet.directive('paginator', function () {
    return {
        restrict: 'AE',
        scope: { from: '=', to: '=', total: '=', itemsPerPage: '=' },
        templateUrl: JSON_URLS.asset + "bundles/zectranet/templates/paginator.html",
        controller: function ($scope) {

            $scope.paginator = {
                'first': 0,
                'current': 0,
                'last': ($scope.$parent.tasks) ? ~~($scope.$parent.tasks.length / $scope.itemsPerPage) : 0
            };

            $scope.$parent.$watch('tasks', function(){
                if ($scope.$parent.tasks != null) {
                    $scope.paginator.last = ~~($scope.$parent.tasks.length / $scope.itemsPerPage);
                }
            });

            var paginator = $scope.paginator;

            $scope.prev = function () {
                if (paginator.current - 1 >= paginator.first) {
                    paginator.current--;
                    $scope.$parent.tablePages.from -= $scope.itemsPerPage;
                    $scope.$parent.tablePages.to -= $scope.itemsPerPage;
                }
            };

            $scope.next = function () {
                if (paginator.current + 1 <= paginator.last) {
                    paginator.current++;
                    $scope.$parent.tablePages.from += $scope.itemsPerPage;
                    $scope.$parent.tablePages.to += $scope.itemsPerPage;
                }
            };

            $scope.first = function () {
                paginator.current = paginator.first;
                $scope.$parent.tablePages.from = (paginator.current) * $scope.itemsPerPage + 1;
                $scope.$parent.tablePages.to = (paginator.current + 1) * $scope.itemsPerPage;
            };

            $scope.last = function () {
                paginator.current = paginator.last;
                $scope.$parent.tablePages.from = (paginator.current) * $scope.itemsPerPage + 1;
                $scope.$parent.tablePages.to= (paginator.current + 1) * $scope.itemsPerPage;
            };
            
            $scope.goToPage = function (page) {
                paginator.current = (page <= paginator.last && page >= paginator.first)
                    ? page : paginator.current;
                $scope.$parent.tablePages.from = (paginator.current) * $scope.itemsPerPage + 1;
                $scope.$parent.tablePages.to= (paginator.current + 1) * $scope.itemsPerPage;
            };
        }
    };
});

Zectranet.directive("calendar", function() {
    return {
        restrict: "E",
        templateUrl: JSON_URLS.asset + "bundles/zectranet/templates/calendar.html",
        scope: {
            selected: "="
        },
        link: function(scope) {
            scope.selected = _removeTime(scope.selected || moment());
            scope.month = scope.selected.clone();

            var start = scope.selected.clone();
            start.date(1);
            _removeTime(start.day(0));

            _buildMonth(scope, start, scope.month);

            scope.select = function(day) {
                scope.selected = day.date;
            };

            scope.next = function() {
                var next = scope.month.clone();
                _removeTime(next.month(next.month()+1).date(1));
                scope.month.month(scope.month.month()+1);
                _buildMonth(scope, next, scope.month);
            };

            scope.previous = function() {
                var previous = scope.month.clone();
                _removeTime(previous.month(previous.month()-1).date(1));
                scope.month.month(scope.month.month()-1);
                _buildMonth(scope, previous, scope.month);
            };
        }
    };

    function _removeTime(date) {
        return date.day(0).hour(0).minute(0).second(0).millisecond(0);
    }

    function _buildMonth(scope, start, month) {
        scope.weeks = [];
        var done = false, date = start.clone(), monthIndex = date.month(), count = 0;
        while (!done) {
            scope.weeks.push({ days: _buildWeek(date.clone(), month) });
            date.add(1, "w");
            done = count++ > 2 && monthIndex !== date.month();
            monthIndex = date.month();
        }
    }

    function _buildWeek(date, month) {
        var days = [];
        for (var i = 0; i < 7; i++) {
            days.push({
                name: date.format("dd").substring(0, 1),
                number: date.date(),
                isCurrentMonth: date.month() === month.month(),
                isToday: date.isSame(new Date(), "day"),
                date: date
            });
            date = date.clone();
            date.add(1, "d");
        }
        return days;
    }
});