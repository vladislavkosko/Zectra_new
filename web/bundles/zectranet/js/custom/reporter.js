Intranet.controller('ReporterController', ['$scope', '$http', '$modal', function($scope, $http, $modal){
	console.log('ReporterController was loaded!');
	
	$scope.filter = {
			query: 'type1'
	}
	
	$scope.table = null;
	$scope.groupBy = -1;
	$scope.tasks = TASKS;
	$scope.users = USERS;
	$scope.statuses = STATUSES;
	$scope.urlsQueryReport = JSON_URLS.queryReport;
	
	$scope.groupByChanged = function()
	{
		if ($scope.groupBy == -1) return;
		
		var values = [];
		
		_.map($scope.table.rows, function(row){
			if (row.spaned == false)
			{
				if (!_.contains(values, row[$scope.groupBy].value))
					values.push(row[$scope.groupBy].value);
			}
		});
		
		var table = {};
		table.cols = $scope.table.cols;
		table.rows = [];
		
		_.map(values, function(value){
			var rowSpaned = [];
			rowSpaned.push({value: value});
			rowSpaned.spaned = true;
			table.rows.push(rowSpaned);
			_.map($scope.table.rows, function(row){
				if (row.spaned == false)
				{
					if (row[$scope.groupBy].value == value)
					{
						table.rows.push(row);
					}
				}
			});
		});
		
		$scope.table = table;
	}
	
	$scope.queryReport = function ()
	{
		$http({
			method: "POST", 
			url: $scope.urlsQueryReport,
			data: $scope.filter
			  })
		.success(function(response){
			if (response.result)
			{
				$scope.groupBy = -1;
				_.map(response.result.rows, function(row){
					row.spaned = false;
				});
				$scope.table = response.result;
			}
		})
	}
}]);