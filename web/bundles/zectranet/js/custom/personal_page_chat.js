
Intranet.controller('PersonalOfficeChatController',['$http', '$scope', '$paginator', function($http, $scope,  $paginator) {
	container = $('#conversation');
	messageContainer = $('#write-message');
	
	$scope.officesIdArray = DATA_ID.officesid;
	$scope.paginator = $paginator;
	$scope.postsPerPage = 10;
	$scope.paginator.postsPerPage = $scope.postsPerPage;
	$scope.posts = [];
	$scope.members = [];
	$scope.message = '';
	$scope.editingPost = null;
	$scope.lastDate = null;	
	
	$scope.avatarURL = JSON_URLS_FOR_PERSONAL_PAGE.avatar;
	
	$scope.postsOfficeGetURL = JSON_URLS_FOR_PERSONAL_PAGE.postsOffice;
	$scope.postOfficeAddURL = JSON_URLS_FOR_PERSONAL_PAGE.post_addOffice; 
	$scope.membersOfficeURL = JSON_URLS_FOR_PERSONAL_PAGE.membersOffice;
	$scope.postsOfficeNewURL = JSON_URLS_FOR_PERSONAL_PAGE.posts_newOffice;
	$scope.postsOfficeCountURL = JSON_URLS_FOR_PERSONAL_PAGE.post_countOffice;
	
	$scope.posts = [];
	$scope.members = [];
	$scope.message = '';
	$scope.editingPost = null;
	$scope.lastDate = null;
	
	$scope.$watch('paginator.curPageId', function(){
		var offset = $scope.paginator.postsPerPage*($scope.paginator.curPageId - 1);
		var limit = $scope.paginator.postsPerPage;
		getOfficesPosts(offset, limit, $scope.officesIdArray);
	});
	
	$scope.pressEnter = function(e)
	{
		if ((e.shiftKey == false) && ( e.keyCode == 13 ))
		{
			e.preventDefault();
			$scope.sendPostOffice();
		}
	}
	
	$scope.init = function (count )
	{
		$scope.count = count;
	}
	
	function updatePosts(posts)
	{
		var editedMessages = _.filter(posts, function(p){return p.edited;});
		if (editedMessages.length == posts.length)
		{
			_.map(posts, function(post){
				_.map($scope.posts, function(p, i){
					if (p.id == post.id)
						$scope.posts[i] = post;
				});
			});
			return true;
		}
		
		return false;
	}
	
	function getOfficesPosts(offset, limit)
	{
			var url = $scope.postsOfficeGetURL.replace('0', $scope.count);
			$http({
				method: "GET", 
				url: url, 
				params: {offset: offset, limit: limit}})
			.success(function(response){
				if (response.result)
				{
					$scope.posts = response.result.reverse();
				}
				if (response.result.length>0)
				{
					$scope.lastDate = (_.last($scope.posts)).posted.date;
				}
				container.animate({ scrollTop: container.height()+1900 },1000);
			})
	}
	
	function getMembersForOffices()
	{
        _.map($scope.officesIdArray, function (officeId) {
            var url = $scope.membersOfficeURL.replace('0', $scope.count);
			$http({
				method: "GET", 
				url: url
            })
			.success(function(response){
				if (response.result)
					$scope.members = response.result;
			})
		})
	}
	
	function getPostsCountForOffice(callback)
	{
		var url = $scope.postsOfficeCountURL.replace('0', $scope.count);
				$http({
					method: "GET", 
					url: url
                })
				.success(function(response){
					if (response.result)
						callback(response.result);
				})
	}

	function getNewPostsForOffice()
	{
		var url = $scope.postsOfficeNewURL.replace('0',$scope.count);
			if ($scope.paginator.curPageId == 1)
			{
				$http({
					method: "GET", 
					url: url, 
					params: {last_posted: $scope.lastDate}})
				.success(function(response){
					if ((response.result) && (response.result.length > 0))
					{	
						var onlyUpdated = updatePosts(response.result);
						if (onlyUpdated == false)
						{
							getPostsCountForOffice(function(postsCount){
								$scope.paginator.init(postsCount, $scope.postsPerPage);
								var offset = $scope.paginator.postsPerPage*($scope.paginator.curPageId - 1);
								var limit = $scope.paginator.postsPerPage;
								getOfficesPosts(offset, limit);
								getMembersForOffices();
							});
						}
							
					}
				})
			}
		setInterval(getNewPostsForOffice, 3000);
	}
	
	$scope.editPost = function(post)
	{
		if ((!$scope.isEditable(post)) || ($scope.editingPost != null)) return;
		$scope.editingPost = post;
		messageContainer.val(post.message);
	}
	
	Date.minutesBetween = function( date1, date2 ) {
		  var one_minute=1000*60;
		  var date1_ms = date1.getTime();
		  var date2_ms = date2.getTime();
		  var difference_ms = date2_ms - date1_ms;
		  
		  return Math.ceil(difference_ms/one_minute); 
	}
	
	Date.milisecondsBetween = function( date1, date2 ) {
		  var date1_ms = date1.getTime();
		  var date2_ms = date2.getTime();
		  var difference_ms = date2_ms - date1_ms;
		  
		  return difference_ms; 
	}
	
	Date.inMyString = function(date)
	{
		return date.getFullYear()+"-"+date.getMonth()+1+"-"+date.getDate()+" "+date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
	}
	
	$scope.isEditable = function(post)
	{
		var postedTime = new Date(Date.parse(post.posted.date));
		var now = new Date();
		var utc = new Date(now.getTime() + now.getTimezoneOffset() * 60000);
		
		var minutesAgo = Date.minutesBetween(postedTime, utc);

		return (minutesAgo <= 5 && post.userid == $scope.userid);
	}
	
	$scope.sendPostOffice = function()
	{
		var post = {
				entityid: $scope.count, 
				userid: window.USER.id,
				message: $scope.message,
				posted: new Date()
		};
		
		if ($scope.editingPost)
			post.postid = $scope.editingPost.id;
		var url = $scope.postOfficeAddURL.replace('0', $scope.count);
		$http({
			method: "POST", 
			url: url, 
			data: post })
		.success(function(response){
			if (response.result)
			{
				// maybe need to request for posts and init paginator!!!
				if ($scope.editingPost == null)
				{
					$scope.posts.push(response.result);
					container.animate({ scrollTop: container.height()+1900 },1000);
				}
				else
				{
					_.map($scope.posts, function(p, i){
						if (p.id == response.result.id)
							$scope.posts[i] = response.result;
					});
				}
			}
			$scope.editingPost = null;
			$scope.message = "";
			messageContainer.val("");
			messageContainer.focus();
			getMembersForOffices();
		})
	}
	
	$scope.changePostsPerPageOffice = function(){
		getPostsCountForOffice(function(postsCount){
			$scope.paginator.init(postsCount, $scope.postsPerPage);
			var offset = $scope.paginator.postsPerPage*($scope.paginator.curPageId - 1);
			var limit = $scope.paginator.postsPerPage;
			getOfficesPosts(offset, limit);
		});
	}
	
	function startChat()
	{
		//console.log('start');
		getMembersForOffices();
		getPostsCountForOffice(function(postsCount){
			$scope.paginator.init(postsCount, 100);
		});
		setInterval(getNewPostsForOffice, 3000);
	}
	
	startChat();
	
  }]);

Intranet.controller('PersonalTopicChatController',['$http', '$scope', '$paginator', function($http, $scope,  $paginator) {
	container = $('#conversation');
	messageContainer = $('#write-message');
	
	$scope.paginator = $paginator;
	
	$scope.postsPerPage = 10;
	$scope.paginator.postsPerPage = $scope.postsPerPage;
	$scope.posts = [];
	$scope.members = [];
	$scope.message = '';
	$scope.editingPost = null;
	$scope.lastDate = null;	
	
	$scope.avatarURL = JSON_URLS_FOR_PERSONAL_PAGE.avatar;
	
	$scope.postsTopicGetURL = JSON_URLS_FOR_PERSONAL_PAGE.postsTopic;
	$scope.postTopicAddURL = JSON_URLS_FOR_PERSONAL_PAGE.post_addTopic;
	$scope.membersTopicURL = JSON_URLS_FOR_PERSONAL_PAGE.membersTopic;
	$scope.postsTopicNewURL = JSON_URLS_FOR_PERSONAL_PAGE.posts_newTopic;
	$scope.postsTopicCountURL = JSON_URLS_FOR_PERSONAL_PAGE.post_countTopic;
	
	$scope.posts = [];
	$scope.members = [];
	$scope.message = '';
	$scope.editingPost = null;
	$scope.lastDate = null;
	
	$scope.$watch('paginator.curPageId', function(){
		var offset = $scope.paginator.postsPerPage*($scope.paginator.curPageId - 1);
		var limit = $scope.paginator.postsPerPage;
		getTopicsPosts(offset, limit, $scope.topicsArray);
	});
	
	$scope.pressEnter = function(e)
	{
		if ((e.shiftKey == false) && ( e.keyCode == 13 ))
		{
			e.preventDefault();
			$scope.sendPostTopic();
		}
	}
	
	$scope.init = function (topicId )
	{
		//console.log('Topic',topicId);
		$scope.topicId = topicId;
	}
	
	function updatePosts(posts)
	{
		var editedMessages = _.filter(posts, function(p){return p.edited;});
		if (editedMessages.length == posts.length)
		{
			_.map(posts, function(post){
				_.map($scope.posts, function(p, i){
					if (p.id == post.id)
						$scope.posts[i] = post;
				});
			});
			return true;
		}
		
		return false;
	}
	
	function getTopicsPosts(offset, limit)
	{
		var url = $scope.postsTopicGetURL.replace('0', $scope.topicId);
			$http({
				method: "GET", 
				url: url, 
				params: {offset: offset, limit: limit}})
			.success(function(response){
				//console.log("posts: ",response.result);
				if (response.result)
				{
					$scope.posts = response.result.reverse();
				}
				if (response.result.length>0)
				{
					$scope.lastDate = (_.last($scope.posts)).posted.date;
				}
				container.animate({ scrollTop: container.height()+1900 },1000);
			})
	}
	function getMembersForTopics()
	{
			var url = $scope.membersTopicURL.replace('0', $scope.topicId);
			$http({
				method: "GET", 
				url: url
            })
			.success(function(response){
				//console.log("members: ", response.result);
				if (response.result)
					$scope.members = response.result;
			})
	}
	
	function getPostsCountForTopic(callback)
	{
		var url = $scope.postsTopicCountURL.replace('0', $scope.topicId);
			$http({
				method: "GET", 
				url: url
            })
			.success(function(response){
				//console.log("posts count: ", response.result);
				if (response.result)
					callback(response.result);
			})
	}
	
	function getNewPostsForTopic(f)
	{
			if ($scope.paginator.curPageId == 1)
			{
				$http({
					method: "GET", 
					url: $scope.postsTopicNewURL.replace('0', $scope.topicId), 
					params: {last_posted: $scope.lastDate}})
				.success(function(response){
					//console.log("new posts: ", response.result);
					if ((response.result) && (response.result.length > 0))
					{	
						var onlyUpdated = updatePosts(response.result);
						if (onlyUpdated == false)
						{
							getNewPostsForTopic(function(postsCount){
								$scope.paginator.init(postsCount, $scope.postsPerPage);
								var offset = $scope.paginator.postsPerPage*($scope.paginator.curPageId - 1);
								var limit = $scope.paginator.postsPerPage;
								getPostsForTopics(offset, limit);
								getMembersForTopics();
							});
						}
							
					}
				})
			}
		setInterval(getNewPostsForTopic, 3000);
	}
	
	$scope.editPost = function(post)
	{
		if ((!$scope.isEditable(post)) || ($scope.editingPost != null)) return;
		//console.log(post);
		$scope.editingPost = post;
		messageContainer.val(post.message);
	}
	
	Date.minutesBetween = function( date1, date2 ) {
		  var one_minute=1000*60;
		  var date1_ms = date1.getTime();
		  var date2_ms = date2.getTime();
		  var difference_ms = date2_ms - date1_ms;
		  
		  return Math.ceil(difference_ms/one_minute); 
	}
	
	Date.milisecondsBetween = function( date1, date2 ) {
		  var date1_ms = date1.getTime();
		  var date2_ms = date2.getTime();
		  var difference_ms = date2_ms - date1_ms;
		  
		  return difference_ms; 
	}
	
	Date.inMyString = function(date)
	{
		return date.getFullYear()+"-"+date.getMonth()+1+"-"+date.getDate()+" "+date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
	}
	
	$scope.isEditable = function(post)
	{
		var postedTime = new Date(Date.parse(post.posted.date));
		var now = new Date();
		var utc = new Date(now.getTime() + now.getTimezoneOffset() * 60000);
		
		var minutesAgo = Date.minutesBetween(postedTime, utc);

		return (minutesAgo <= 5 && post.userid == $scope.userid);
	};
	
	$scope.sendPostTopic = function()
	{
		var post = {
				entityid: $scope.topicId, 
				userid: window.USER.id,
				message: $scope.message,
				posted: new Date()
		};
		
		if ($scope.editingPost)
			post.postid = $scope.editingPost.id;
		var url = $scope.postTopicAddURL.replace('0', $scope.topicId);
		$http({
			method: "POST", 
			url: url, 
			data: post })
		.success(function(response){
			//console.log("Created post: ", response.result);
			if (response.result)
			{
				// maybe need to request for posts and init paginator!!!
				if ($scope.editingPost == null)
				{
					$scope.posts.push(response.result);
					container.animate({ scrollTop: container.height()+1900 },1000);
				}
				else
				{
					_.map($scope.posts, function(p, i){
						if (p.id == response.result.id)
							$scope.posts[i] = response.result;
					});
				}
			}
			$scope.editingPost = null;
			$scope.message = "";
			messageContainer.val("");
			messageContainer.focus();
			getMembersForTopic();
		})
	};
	
	$scope.changePostsPerPageTopic = function(){
		getPostsCountForTopic(function(postsCount){
			$scope.paginator.init(postsCount, $scope.postsPerPage);
			var offset = $scope.paginator.postsPerPage*($scope.paginator.curPageId - 1);
			var limit = $scope.paginator.postsPerPage;
			getTopicPosts(offset, limit);
		});
	};
	
	function startChatTopic()
	{
		//console.log('chat topic started');
		getMembersForTopics();
		getPostsCountForTopic(function(postsCount){
			$scope.paginator.init(postsCount, $scope.postsPerPage);
		});
		setInterval(getNewPostsForTopic, 3000);
	}
	getMembersForTopics();
	getPostsCountForTopic(function(postsCount){
		$scope.paginator.init(postsCount, $scope.postsPerPage);
	});
}]);