var chat = Intranet.controller('ChatController', ['$rootScope', '$scope', '$http', '$paginator', '$modal', '$sce',
	function($rootScope, $scope, $http, $paginator, $modal, $sce) {

		console.log('Chat Controller was loaded');

		// ====================== BEGIN OF SCOPE VARIABLES ====================== \\
		{
			container = $('.panel-body');
			messageContainer = $('#write-message');
			$scope.paginator = $paginator;
			$scope.postsPerPage = 20;
			$scope.paginator.postsPerPage = $scope.postsPerPage;
			$scope.availableMembers = MEMBERS;
			$scope.posts = [];
			$scope.members = [];
			$scope.message = '';
			$scope.editingPost = null;
			$scope.lastDate = null;
			$scope.entityid = window.ENTITY.id;
			$scope.userid = window.USER.id;
			$scope.currentOffice = (typeof (OFFICE_NAME) != "undefined") ? OFFICE_NAME : null;

			// --------------- BEGIN OF URL VARIABLES --------------- \\
			{
				$scope.InsertScreenshotsInPHP = JSON_URLS.InsertScreenshotsInPHP;
				$scope.postsGetURL = JSON_URLS.posts;
				$scope.avatarURL = JSON_URLS.avatar;
				$scope.postAddURL = JSON_URLS.post_add;
				$scope.membersURL = JSON_URLS.members;
				$scope.postsNewURL = JSON_URLS.posts_new;
				$scope.postsCountURL = JSON_URLS.post_count;
				$scope.urlsDocumentsAdd = JSON_URLS.documentsAdd;
				$scope.urlsBase = JSON_URLS.baseUrl;
				$scope.urlSendPrivateMsg = JSON_URLS.sendPrivateMsg;
				$scope.urlsSendGlobalPrivateMsg = JSON_URLS.urlsSendOfficePrivateMsg;
			}
		}
		// ====================== END OF SCOPE VARIABLES ======================== \\

		// ====================== BEGIN OF SCOPE WATCHES ======================== \\
		{
			$scope.$watch('paginator.curPageId', function () {
				var offset = $scope.paginator.postsPerPage * ($scope.paginator.curPageId - 1);
				var limit = $scope.paginator.postsPerPage;
				getPosts(offset, limit);
			});

			$scope.$watch('posts', function () {
				if ($scope.posts && $scope.posts.length > 0) {
					setTimeout(function () {
						var images = $('.zoom-images');
						images.attr('onclick', "ShowPicture($(this));");
						return;
					}, 1000)
				}
			}, true);
		}
		// ====================== END OF SCOPE WATCHES ========================== \\

		//InsertScreenshots ctrl + V
		{
			var atachments = [];
			window.onload = function () {
				document.addEventListener('paste', function (event) {
					event.stopPropagation();
					event.preventDefault();
					var cbd = event.clipboardData;
					if (cbd.items && cbd.items.length) {
						var cbi = cbd.items[0];
						if (/^image\/(png|gif|jpe?g)$/.test(cbi.type)) {
							var f = cbi.getAsFile();
							var fr = new FileReader();
							fr.onload = function () {
								var im = new Image();
								im.src = this.result;
								im.style.display = 'block';
								im.setAttribute('class', 'img-screenshot');
								im.setAttribute('onclick', '$(this).remove(DeleteLastScreenshot());');
								document.getElementById('div-screenshot').style.display = 'block';
								$('#slide-down-menu-screenshots').fadeIn(1500);
								document.getElementById('div-screenshot').appendChild(im);
								$(im).fadeIn(1500);
								atachments.push($(im).attr('src'));
							};
							fr.readAsDataURL(f);
						}
					}
				}, false);
			};

			$scope.InsertScreenshotsInChat = function () {
				if (atachments.length > 0) {
					$http({
						method: "POST",
						url: $scope.InsertScreenshotsInPHP,
						data: atachments
					})
						.success(function (response) {
							if (response.result) {
								atachments = [];
								$('#slide-down-menu-screenshots').fadeOut(1500);
								setTimeout(function () {
									$('.img-screenshot').remove();
								}, 1500);
								console.log(response.result);
							}
						});
				}

			};
		}
		//End InsertScreenshots ctrl + V

		// ====================== BEGIN OF MEMBER FUNCTIONS ===================== \\
		{
			function getMembers() {
				$http({
					method: "GET",
					url: $scope.membersURL
				})
					.success(function (response) {
						if (response.result) {
							$scope.members = response.result;
						}
					})
			}

			$scope.selectionUser = function (array, index, $event) {
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
		}
		// ====================== END OF MEMBER FUNCTIONS ======================= \\

	//todo
		$rootScope.NEW_POSTS = getNewPosts;

		// ====================== BEGIN OF DATETIME FUNCTIONS ======================== \\
		{
			Date.minutesBetween = function (date1, date2) {
				var one_minute = 1000 * 60;
				var date1_ms = date1.getTime();
				var date2_ms = date2.getTime();
				var difference_ms = date2_ms - date1_ms;
				return Math.ceil(difference_ms / one_minute);
			};

			Date.milisecondsBetween = function (date1, date2) {
				var date1_ms = date1.getTime();
				var date2_ms = date2.getTime();
				var difference_ms = date2_ms - date1_ms;
				return difference_ms;
			};

			Date.inMyString = function (date) {
				return date.getFullYear() + "-" + date.getMonth() + 1 + "-" + date.getDate() + " " + date.getHours() + ":" +
					date.getMinutes() + ":" + date.getSeconds();
			};
		}
		// ====================== END OF DATETIME FUNCTIONS ========================== \\

		// ====================== BEGIN OF POST FUNCTIONS ============================ \\
		{
			$scope.pressEnter = function(e) {
				if ((e.shiftKey == false) && ( e.keyCode == 13 )) {
					e.preventDefault();
					$scope.sendPost();
				}
			};

			function getNewPosts() {
				if ($scope.paginator.curPageId == 1) {
					$http({
						method: "GET",
						url: $scope.postsNewURL,
						params: {last_posted: $scope.lastDate}})
						.success(function(response) {
							if ((response.result) && (response.result.length > 0)) {
								var onlyUpdated = updatePosts(response.result);
								if (onlyUpdated == false) {
									getPostsCount(function(postsCount) {
										$scope.paginator.init(postsCount, $scope.postsPerPage);
										var offset = $scope.paginator.postsPerPage*($scope.paginator.curPageId - 1);
										var limit = $scope.paginator.postsPerPage;
										getPosts(offset, limit);
										getMembers();
									});
								}
							}
						});
				}
			}

			function updatePosts(posts) {
				var editedMessages = _.filter(posts, function(p){return p.edited;});
				if (editedMessages.length == posts.length) {
					_.map(posts, function(post) {
						_.map($scope.posts, function(p, i) {
							if (p.id == post.id) {
								$scope.posts[i] = post;
							}
						});
					});
					return true;
				}
				return false;
			}

			function removeUnnecesaryTags(message) {
				message = message.replace('<button class="fa fa-times-circle"></button>', '<br>');
				message = message.replace('onclick="ShowPicture($(this));"', '');
				return message;
			}

			function getPosts(offset, limit)
			{
				$http({
					method: "GET",
					url: $scope.postsGetURL,
					params: {offset: offset, limit: limit}})
					.success(function(response){
						if (response.result) {
							$scope.posts = response.result.reverse();
							_.map($scope.posts, function(post) {
								post.message = removeUnnecesaryTags(post.message);
								post.message = $sce.trustAsHtml(post.message);
							});
						}
						if (response.result.length>0) {
							$scope.lastDate = (_.last($scope.posts)).posted.date;
						}
						$scope.chatLoaded = 'visible-element';
						container.animate({ scrollTop: container.height() + 3000 }, 1000);
						setTimeout(function(){
							$.call(ImagesPreviewFunction);
							return;
						}, 500)
					})
			}

			function getPostsCount(callback) {
				$http({
					method: "GET",
					url: $scope.postsCountURL
				})
					.success(function(response) {
						if (response.result)
							callback(response.result);
					})
			}

			$scope.editPost = function (post) {
				if ((!$scope.isEditable(post)) || ($scope.editingPost != null)) return;
				$scope.editingPost = post;
				messageContainer.val(post.message);
			};

			$scope.isEditable = function (post) {
				var postedTime = new Date(Date.parse(post.posted.date));
				var now = new Date();
				var utc = new Date(now.getTime() + now.getTimezoneOffset() * 60000);
				var minutesAgo = Date.minutesBetween(postedTime, utc);
				return (minutesAgo <= 5 && post.userid == $scope.userid);
			};

			$scope.sendPost = function () {
				$scope.InsertScreenshotsInChat();

				var msg = $scope.message;
				var symb_index = msg.indexOf("@");
				var tmp_str = msg.substring(symb_index);
				var space_index = tmp_str.indexOf(" ");
				var user_to_send_name = "";
				if (space_index != -1) {
					user_to_send_name = tmp_str.substring('1', space_index);
				} else {
					user_to_send_name = tmp_str.substring('1');
				}

				var projectMessage = false;
				var message = $scope.message;
				var regex = new RegExp('@' + $scope.currentOffice, 'mig');
				var matches = message.match(regex);
				projectMessage = (matches) ? true : false;

				regex = new RegExp('@all', 'mig');
				matches = message.match(regex);
				projectMessage = (matches || projectMessage) ? true : false;

				var post = {
					entityid: $scope.entityid,
					userid: $scope.userid,
					message: $scope.message,
					posted: new Date(),
					usertosendname: user_to_send_name
				};

				if (symb_index != -1 && projectMessage == false) {
					$http({
						method: "POST",
						url: $scope.urlSendPrivateMsg,
						data: post
					})
						.success(function (response) {
							if (response.result) {
								if ($scope.editingPost == null) {
									$scope.posts.push(response.result);
									container.animate({scrollTop: container.height() + 3000}, 1000);
								} else {
									_.map($scope.posts, function (p, i) {
										if (p.id == response.result.id) {
											$scope.posts[i] = response.result;
										}
									});
								}
								if ($scope.editingPost) {
									post.postid = $scope.editingPost.id;
								}
								$scope.editingPost = null;
								$scope.message = "";
								messageContainer.val("");
								messageContainer.focus();
								getMembers();
							}
						})
				} else if (projectMessage) {
					var users_to_send = [];
					_.map($scope.availableMembers, function (member) {
						users_to_send.push(member.username)
					});
					var post = {
						entityid: $scope.entityid,
						userid: $scope.userid,
						message: $scope.message,
						posted: new Date(),
						users: users_to_send
					};

					$http({
						method: "POST",
						url: $scope.urlsSendGlobalPrivateMsg,
						data: post
					})
						.success(function (response) {
							if (response.result) {
								if ($scope.editingPost == null) {
									$scope.posts.push(response.result);
									container.animate({scrollTop: container.height() + 3000}, 1000);
								} else {
									_.map($scope.posts, function (p, i) {
										if (p.id == response.result.id) {
											$scope.posts[i] = response.result;
										}
									});
								}
								if ($scope.editingPost) {
									post.postid = $scope.editingPost.id;
								}
								$scope.editingPost = null;
								$scope.message = "";
								messageContainer.val("");
								messageContainer.focus();
								getMembers();
							}
						})
				} else {
					$http({
						method: "POST",
						url: $scope.postAddURL,
						data: post
					})
						.success(function (response) {
							if (response.result) {
								if ($scope.editingPost == null) {
									$scope.posts.push(response.result);
									container.animate({scrollTop: container.height() + 3000}, 1000);
								} else {
									_.map($scope.posts, function (p, i) {
										if (p.id == response.result.id) {
											$scope.posts[i] = response.result;
										}
									});
								}
								if ($scope.editingPost) {
									post.postid = $scope.editingPost.id;
								}
								$scope.editingPost = null;
								$scope.message = "";
								messageContainer.val("");
								messageContainer.focus();
								getMembers();
							}
						})
				}
			};

			$scope.changePostsPerPage = function () {
				getPostsCount(function (postsCount) {
					$scope.paginator.init(postsCount, $scope.postsPerPage);
					var offset = $scope.paginator.postsPerPage * ($scope.paginator.curPageId - 1);
					var limit = $scope.paginator.postsPerPage;
					getPosts(offset, limit);
				});
			};

			function startChat() {
				getMembers();
				getPostsCount(function (postsCount) {
					$scope.paginator.init(postsCount, $scope.postsPerPage);
				});
				getNewPosts();
			}

			startChat();
		}
		// ====================== END OF POST FUNCTIONS ============================== \\

		// ====================== BEGIN OF DOCUMENT FUNCTIONS ======================== \\
		{
			$scope.addDocuments = function () {
				$http({
					method: "GET",
					url: $scope.urlsDocumentsAdd
				})
					.success(function (response) {
						var modalInstance = $modal.open({
							template: response,
							controller: 'AddDocumentsController'
						});

						modalInstance.result.then(function (addedDocuments) {
							insertDocumentsLinks(addedDocuments);
						}, function () {
						});
					})
			};

			function insertDocumentsLinks(documents) {
				_.map(documents, function (d) {
					$scope.message +=
						'\n<div style="display: block; clear: both;">'
							+ '<a href="' + $scope.urlsBase + d.url + '" download="' + d.name + '">'
								+ d.name
							+ '</a><br>'
							+ '<a href="' + $scope.urlsBase + d.url + '" data-lightbox="some-user-image" class="click-to-toggle">'
								+ '<img class="zoom-images" ' + 'src="' + $scope.urlsBase + d.url + '">'
							+'</a>'
						+'</div>';
				});
			}
		}
		// ====================== END OF DOCUMENT FUNCTIONS ========================== \\
}])
.controller('AddDocumentsController', ['$scope', '$http', '$modalInstance', function($scope, $http, $modalInstance){
	console.log('AddDocumentsController was loaded!');

	$scope.documents = [];
	$scope.urlsDocumentsGet = JSON_URLS.documentsGet;

	function bindList() {
		$('.finish').click(function(){
			$(this).parent().toggleClass('finished');
			$(this).toggleClass('fa-square-o');
		});
	}

	function prepareDocuments(documents) {
		return _.map(documents, function(d){d.checked = false; return d;});
	}

	function getDocuments() {
		$http({
			method: "GET",
			url: $scope.urlsDocumentsGet,
			params: {
				userid: USER.id
			}
			  })
		.success(function(response) {
			if (response.result)
			{
				$scope.documents = prepareDocuments(response.result);
				setTimeout(bindList, 1000);
			}
		})
	}

	getDocuments();
	getDocuments_ = getDocuments;
	$scope.addDocuments = function() {
		$modalInstance.close(_.filter($scope.documents, function(d){return d.checked;}));
	};

	$scope.checkItem = function(documentid) {
		_.map($scope.documents, function(d){
			if (d.id == documentid) d.checked = !d.checked;
		});
	}
}]);