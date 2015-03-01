Intranet.controller('LogsController', ['$scope', '$http', '$modal', function($scope, $http, $modal){
	console.log('LogsController was loaded!');
	
	$scope.filter = {
	}
	$scope.timeoutHandler = null;
	
	$scope.$watch('filter', function(){
		if ($scope.timeoutHandler != null) clearTimeout($scope.timeoutHandler);
		$scope.timeoutHandler = setTimeout(getLogs, 3000);
	}, true);
	
	$scope.logs = [];
	$scope.tasks = TASKS;
	$scope.users = USERS;
	$scope.urlsLogsGet = JSON_URLS.logsGet;
	
	function getLogs()
	{
		$http({
			method: "POST", 
			url: $scope.urlsLogsGet,
			data: $scope.filter
			  })
		.success(function(response){
			if (response.result) {
				$scope.logs = response.result;
			}
		})
	}
}]);