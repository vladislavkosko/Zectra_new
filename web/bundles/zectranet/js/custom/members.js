Intranet.controller('MembersController', ['$scope', '$http', function($scope, $http){
	membersAlert = $('#members-alert');
	$scope.users = window.USERS;
	$scope.members = window.MEMBERS;
	$scope.avatarURL = JSON_URLS.avatar;
	$scope.membersURL = JSON_URLS.editMembers;
	
	(function(){
		membersIds = _.map($scope.members, function(e){e.selected = false; return e.id;});
		_.map($scope.users, function(e){e.selected = false;});
		$scope.users = _.filter($scope.users, function(e){return !_.contains(membersIds, e.id)});
	})();
	
	function showAlert(message)
	{
		$scope.alert = message;
		membersAlert.show('slow', function(){
			setTimeout(function(){membersAlert.hide('slow');$scope.alert='';}, 2000);
		});
	}
	
	function sendMembers()
	{
		$http({
			method: "POST", 
			url: $scope.membersURL, 
			data: {'members': _.map($scope.members, function(u){return u.id;})}})
		.success(function(response){
			showAlert(response.message);
		})
	}
	
	$scope.selection = function(array, index, $event)
	{
		if ($event) {
			if ($event.button == 2) {
				array[index].selected = !array[index].selected;
				canRemove();
			} else if ($event.button == 0) {
				var TextArea = document.getElementById('write-message');
				TextArea.value = (TextArea.value) ?
				TextArea.value + '@' + array[index].username + ' ' :
				'@' + array[index].username + ' ';
				$('#write-message').focus();
			}
		}
	};

	$scope.addBox = function(e)
	{
		e.preventDefault();
		$scope.showAddBox = !$scope.showAddBox;
	};
	
	$scope.showAddBox = false;
	$scope.removeEnabled = false;
	$scope.alert = '';
	
	function canRemove()
	{
		var selected = _.findWhere($scope.members, {'selected': true});
		if (selected === undefined)
			$scope.removeEnabled = false;
		else
			$scope.removeEnabled = true;
		
		return $scope.removeEnabled;
	}
	
	$scope.removeFromMembers = function(e)
	{
		e.preventDefault();
		if (!canRemove()) return;
		_.map($scope.members, function(u){
			if (u.selected)
			{
				u.selected = false;
				$scope.users.push(u);
			}
				
		});
		usersIds = _.map($scope.users, function(e){return e.id;});
		$scope.members = _.filter($scope.members, function(e){return !_.contains(usersIds, e.id)});
		
		sendMembers();
		canRemove();
	};
	
	$scope.addToMembers = function(e)
	{
		e.preventDefault();
		if ($scope.users.length == 0) return;
		_.map($scope.users, function(u){
			if (u.selected)
			{
				u.selected = false;
				$scope.members.push(u);
			}
				
		});
		membersIds = _.map($scope.members, function(e){return e.id;});
		$scope.users = _.filter($scope.users, function(e){return !_.contains(membersIds, e.id)});
		
		sendMembers();
	}
	
	
}]);