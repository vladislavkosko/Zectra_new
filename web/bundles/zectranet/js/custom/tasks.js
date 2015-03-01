Intranet.controller('TasksController', ['$scope', '$http', '$modal', function($scope, $http, $modal) {

	console.log('TasksController was loaded!');

	// ====================== BEGIN OF SCOPE WATCHES ====================== \\
	{
		// Prevent to closing a filter window
		$scope.$watch('tasks', function () {
			if ($scope.tasks && $scope.tasks.length > 0) {
				setTimeout(function () {
					$('.noclose').click(function (e) {
						e.stopPropagation();
					});
					return;
				}, 1000)
			}
		}, true);

		$scope.$watch(function () {
			return TASKS_NOTIFICATIONS;
		}, function (input) {
			calculateNotifications();
		});
	}
	// ====================== END OF SCOPE WATCHES ========================== \\

	// ====================== BEGIN OF SCOPE VARIABLES ====================== \\
	{
		$scope.filter = {
			status: [],
			priority: [],
			name: "",
			user: [],
			topic: []
		};

		$scope.availablePrioritetsTask = ['high', 'medium', 'low'];

		$scope.taskLoaded = 'none';
		$scope.tasksCount = 0;
		$scope.checked = true;
		$scope.tasks = [];
		$scope.users = USERS;
		$scope.office_users = OFFICE_USERS;
		$scope.topics = TOPICS;
		$scope.tasksNotification = TASKS_NOTIFICATIONS;
		$scope.curentTopic = ENTITY.id;
		$scope.urlsTasksGet = JSON_URLS.getTasksURL;
		$scope.urlsTasksRemove = JSON_URLS.tasksRemove;
		$scope.urlsTasksEdit = JSON_URLS.tasksEdit;
		$scope.urlsTopicsShow = JSON_URLS.topicsShow;
		$scope.urlsTasksAdd = JSON_URLS.tasksAdd;
		$scope.urlsPostsTaskShow = JSON_URLS.urlsPostsTaskShow;
		$scope.urlsOfficeShow = JSON_URLS.urlOfficeForTasks;
		$scope.showChat = false;
		$scope.urlsTasksEdit = JSON_URLS.tasksEdit;
		$scope.asset = JSON_URLS.asset;

		$scope.estimated = false;
		$scope.uploaderSWF = JSON_URLS.uploaderSWF;
		$scope.uploaderUpload = JSON_URLS.uploaderUpload;

		$scope.addingDocuments = false;
		$scope.posts = [];
		$scope.avatarURL = JSON_URLS.avatar;
		$scope.urlsBase = JSON_URLS.baseUrl;
		$scope.urlSendPrivateMsg = JSON_URLS.sendPrivateMsg;
		$scope.showTaskPanel = false;
		$scope.tooltips = "Show chat";
		$scope.displayRHP = 'displayNone';
	}
	// ====================== END OF SCOPE VARIABLES ======================== \\

	// ====================== BEGIN OF FILTERS SECTION ====================== \\
	{
		// ---------------- begin of filter variables ---------------- \\
		{
			$scope.statusesSelected = null;
			$scope.prioritetsSelected = true;
			$scope.assignedSelected = true;
			$scope.parentSelected = true;
			$scope.sprintSelected = true;
			$scope.ownerSelected = true;

			$scope.nameIncludes = [];

			$scope.statusIncludes = [];
			$scope.StatusesTask = [];

			$scope.priorityIncludes = [];
			$scope.availablePrioritetsTask = [];

			$scope.assignedIncludes = [];
			$scope.availableAssigned = [];

			$scope.parentIncludes = [];
			$scope.availableParent = [];

			$scope.sprintIncludes = [];
			$scope.availableSprint = [];

			$scope.ownerIncludes = [];
			$scope.availableOwner = [];
		}
		// ---------------- end of filter variables ------------------ \\

		// ---------------- begin of filter functions ---------------- \\
		{
			function includeToFilter(obj, where) {
				var i = $.inArray(obj, where);
				if (i > -1) {
					where.splice(i, 1);
				} else {
					where.push(obj);
				}
			}

			function executeFilter(obj, where) {
				if (where.length > 0) {
					if ($.inArray(obj, where) < 0)
						return;
				}
				return obj;
			}

			function selectAll(where, from, selector) {
				if (inArray('~', where)) {
					where = [];
					_.map(from, function (f) {
						where.push(f);
					});
					selector = true;
				} else {
					where = [];
					where.push('~');
					selector = false;
				}
				var params = {
					where: where,
					selector: selector
				};
				return params;
			}

			function inArray(needle, haystack) {
				var length = haystack.length;
				for (var i = 0; i < length; i++) {
					if (haystack[i] == needle) return true;
				}
				return false;
			}
		}
		// ---------------- end of filter functions ------------------ \\

		// ---------------- begin of scope filters ------------------- \\
		{
			$scope.nameIncludes = [];
			$scope.includeName = function (obj) {
				includeToFilter(obj.name, $scope.nameIncludes);
			}

			$scope.nameFilter = function (obj) {
				return executeFilter(obj.name, $scope.nameIncludes);
			}

			$scope.statusIncludes = [];
			$scope.includeStatus = function (obj) {
				includeToFilter(obj.status.label, $scope.statusIncludes);
				if ($scope.statusIncludes.length == 0) {
					$scope.statusIncludes.push('~');
				}
			}

			$scope.statusFilter = function (obj) {
				return executeFilter(obj.status.label, $scope.statusIncludes);
			}

			$scope.priorityIncludes = [];
			$scope.includePriority = function (obj) {
				includeToFilter(obj.priority, $scope.priorityIncludes);
				if ($scope.priorityIncludes.length == 0) {
					$scope.priorityIncludes.push('~');
				}
			}

			$scope.priorityFilter = function (obj) {
				return executeFilter(obj.priority, $scope.priorityIncludes);
			}

			$scope.assignedIncludes = [];
			$scope.availableAssigned = [];
			$scope.includeAssigned = function (obj) {
				if (obj.user) {
					includeToFilter(obj.user.name, $scope.assignedIncludes);
				}
				if ($scope.assignedIncludes.length == 0) {
					$scope.assignedIncludes.push('~');
				}
			}

			$scope.assignedFilter = function (obj) {
				if (obj.user) {
					return executeFilter(obj.user.name, $scope.assignedIncludes);
				} else {
					return executeFilter('null', $scope.assignedIncludes);
				}
			}

			$scope.parentIncludes = [];
			$scope.availableParent = [];
			$scope.includeParent = function (obj) {
				(obj.topic) ? includeToFilter(obj.topic.name, $scope.parentIncludes) : null;
				if ($scope.parentIncludes.length == 0) {
					$scope.parentIncludes.push('~');
				}
			}

			$scope.parentFilter = function (obj) {
				if (obj.topic) {
					return executeFilter(obj.topic.name, $scope.parentIncludes);
				} else {
					return executeFilter('null', $scope.parentIncludes);
				}
			}

			$scope.sprintIncludes = [];
			$scope.availableSprint = [];
			$scope.includeSprint = function (obj) {
				(obj.sprint) ? includeToFilter(obj.sprint.name, $scope.sprintIncludes) : null;
				if ($scope.sprintIncludes.length == 0) {
					$scope.sprintIncludes.push('~');
				}
			}

			$scope.sprintFilter = function (obj) {
				if (obj.sprint) {
					return executeFilter(obj.sprint.name, $scope.sprintIncludes);
				} else {
					return executeFilter('null', $scope.sprintIncludes);
				}
			}

			$scope.ownerIncludes = [];
			$scope.availableOwner = [];
			$scope.includeOwner = function (obj) {
				(obj.owner) ? includeToFilter(obj.owner.name, $scope.ownerIncludes) : null;
				if ($scope.ownerIncludes.length == 0) {
					$scope.ownerIncludes.push('~');
				}
			}

			$scope.ownerFilter = function (obj) {
				if (obj.owner) {
					return executeFilter(obj.owner.name, $scope.ownerIncludes);
				} else {
					return executeFilter('null', $scope.ownerIncludes);
				}
			}
		}
		// ---------------- end of scope filters --------------------- \\

		$scope.$watch('tasks', function() {
			if ($scope.tasks) {
				$scope.resolvedAproved = false;
				_.map($scope.tasks, function(s) {
					$scope.nameIncludes.push(s.name);

					if(s.priority && !inArray(s.priority, $scope.priorityIncludes)) {
						$scope.priorityIncludes.push(s.priority);
						if (s.subtasks) {
							_.map(s.subtasks, function(sub) {
								if(sub.priority && !inArray(sub.priority, $scope.priorityIncludes)) {
									$scope.priorityIncludes.push(sub.priority);
								}
							});
						}
					}

					if(s.user && !inArray(s.user.name, $scope.assignedIncludes)) {
						$scope.assignedIncludes.push(s.user.name);
						if (s.subtasks) {
							_.map(s.subtasks, function(sub) {
								if(sub.user && !inArray(sub.user.name, $scope.assignedIncludes)) {
									$scope.assignedIncludes.push(sub.user.name);
								}
								else if (!sub.user && !inArray('null', $scope.assignedIncludes)) {
									$scope.assignedIncludes.push('null');
								}
							});
						}
					} else if (!s.user && !inArray('null', $scope.assignedIncludes)) {
						$scope.assignedIncludes.push('null');
					}

					if(s.topic && !inArray(s.topic.name, $scope.parentIncludes)) {
						$scope.parentIncludes.push(s.topic.name);
						if (s.subtasks) {
							_.map(s.subtasks, function(sub) {
								if(sub.topic && !inArray(sub.topic.name, $scope.parentIncludes)) {
									$scope.parentIncludes.push(sub.topic.name);
								} else if (!sub.topic && !inArray('null', $scope.parentIncludes)) {
									$scope.parentIncludes.push('null');
								}
							});
						}
					} else if (!s.topic && !inArray('null', $scope.parentIncludes)) {
						$scope.parentIncludes.push('null');
					}

					if(s.sprint && !inArray(s.sprint.name, $scope.sprintIncludes)) {
						$scope.sprintIncludes.push(s.sprint.name);
						if (s.subtasks) {
							_.map(s.subtasks, function(sub) {
								if(sub.sprint && !inArray(sub.sprint.name, $scope.sprintIncludes)) {
									$scope.sprintIncludes.push(sub.sprint.name);
								} else if (!sub.sprint && !inArray('null', $scope.sprintIncludes)) {
									$scope.sprintIncludes.push('null');
								}
							});
						}
					} else if (!s.sprint && !inArray('null', $scope.sprintIncludes)) {
						$scope.sprintIncludes.push('null');
					}

					if(s.owner && !inArray(s.owner.name, $scope.ownerIncludes)) {
						$scope.ownerIncludes.push(s.owner.name);
						if (s.subtasks) {
							_.map(s.subtasks, function(sub) {
								if(sub.owner && !inArray(sub.owner.name, $scope.ownerIncludes)) {
									$scope.ownerIncludes.push(sub.owner.name);
								} else if (!sub.owner && !inArray('null', $scope.ownerIncludes)) {
									$scope.ownerIncludes.push('null');
								}
							});
						}
					} else if (!s.owner && !inArray('null', $scope.ownerIncludes)) {
						$scope.ownerIncludes.push('null');
					}

					if (s.status && !inArray(s.status.label, $scope.statusIncludes)) {
						if (s.status.label != 'Resolved: approved') {
							$scope.statusIncludes.push(s.status.label);
						} else {
							$scope.resolvedAproved = true;
						}
						if (s.subtasks) {
							_.map(s.subtasks, function(sub) {
								if(sub.status && sub.status.label != 'Resolved: approved' && !inArray(sub.status.label, $scope.statusIncludes)) {
									$scope.statusIncludes.push(sub.status.label);
								} else if (sub.status.label == 'Resolved: approved') {
									$scope.resolvedAproved = true;
								}
							});
						}
					}
				});

				_.map($scope.priorityIncludes, function (s) {
					$scope.availablePrioritetsTask.push(s);
				});

				_.map($scope.statusIncludes, function(s) {
					$scope.StatusesTask.push(s);
				});

				_.map($scope.assignedIncludes, function(assigned) {
					$scope.availableAssigned.push(assigned);
				});

				_.map($scope.parentIncludes, function(parent) {
					$scope.availableParent.push(parent);
				});

				_.map($scope.sprintIncludes, function(sprint) {
					$scope.availableSprint.push(sprint);
				});

				_.map($scope.ownerIncludes, function(sprint) {
					$scope.availableOwner.push(sprint);
				});
			}
		});

		// ---------------- begin of select functions ---------------- \\
		{
			$scope.selectAllStatuses = function () {
				var params = selectAll($scope.statusIncludes, $scope.StatusesTask, $scope.statusesSelected);
				$scope.statusIncludes = params.where;
				$scope.statusesSelected = params.selector;
				if ($scope.resolvedAproved && $scope.statusesSelected) {
					$scope.statusIncludes.push('Resolved: approved');
				}
			}

			$scope.selectAllPriority = function () {
				var params = selectAll($scope.priorityIncludes, $scope.availablePrioritetsTask, $scope.prioritetsSelected);
				$scope.priorityIncludes = params.where;
				$scope.prioritetsSelected = params.selector;
			}

			$scope.selectAllAssigned = function () {
				var params = selectAll($scope.assignedIncludes, $scope.availableAssigned, $scope.assignedSelected);
				$scope.assignedIncludes = params.where;
				$scope.assignedSelected = params.selector;
			}

			$scope.selectAllParent = function () {
				var params = selectAll($scope.parentIncludes, $scope.availableParent, $scope.parentSelected);
				$scope.parentIncludes = params.where;
				$scope.parentSelected = params.selector;
			}

			$scope.selectAllSprint = function () {
				var params = selectAll($scope.sprintIncludes, $scope.availableSprint, $scope.sprintSelected);
				$scope.sprintIncludes = params.where;
				$scope.sprintSelected = params.selector;
			}

			$scope.selectAllOwner = function () {
				var params = selectAll($scope.ownerIncludes, $scope.availableOwner, $scope.ownerSelected);
				$scope.ownerIncludes = params.where;
				$scope.ownerSelected = params.selector;
			}
		}
		// ---------------- end of select functions ------------------ \\
	}
	// ====================== END OF FILTERS SECTION ======================== \\

	// ====================== BEGIN OF RHP FUNCTIONS ======================== \\
	{
		$scope.close = function (event) {
			event.preventDefault();
			$('#chat').css('width', '80%');
			$('#panel').css('width', '97%');
			$('#controlButtons').css('width', '100%');
			$('.conteinerForPanel').css('font-size', '12px');
			$('#pagination').removeClass('col-md-6').addClass('col-md-8');
			$('#chevron').removeClass('fa-angle-double-right').addClass('fa-angle-double-left');
			$scope.showTaskPanel = !$scope.showTaskPanel;
			$scope.showChat = false;
			$scope.addingDocuments = false;
			$scope.displayRHP = 'displayNone';
		};

		$scope.showChatPanel = function () {
			$scope.showChat = !$scope.showChat;
			if ($scope.showChat) {
				$scope.tooltips = "Hide chat";
			}
			else $scope.tooltips = "Show chat";
			if ($('#chevron').hasClass('fa-angle-double-left')) {
				$('#chevron').removeClass('fa-angle-double-left').addClass('fa-angle-double-right');
			} else {
				$('#chevron').removeClass('fa-angle-double-right').addClass('fa-angle-double-left');
			}
			if (!$scope.showChat) {
				$('#delpos').css('position', 'relative');
			} else {
				$('#delpos').css('position', 'inherit');
			}
		};

		$scope.addDoc = function () {
			$scope.addingDocuments = !$scope.addingDocuments;
		};

		function calculateNotifications() {
			$scope.tasksNotification = TASKS_NOTIFICATIONS;
			_.map($scope.tasks, function (task) {
				task.newCommentsCount = 0;
				_.map($scope.tasksNotification, function (tn) {
					if (tn != undefined)
						if (tn.resourceid != undefined)
							if (tn.resourceid == task.id && tn.type == 'task_comment') {
								task.newCommentsCount++;
							}
				});
				if (typeof task.subtasks != undefined) {
					_.map(task.subtasks, function (subtask) {
						subtask.newCommentsCount = 0;
						_.map($scope.tasksNotification, function (stn) {
							if (stn != undefined)
								if (stn.resourceid != undefined)
									if (stn.resourceid == subtask.id)
										subtask.newCommentsCount++;
						});
					});
				}
			});

		}
	}
	// ====================== END OF RHP FUNCTIONS ========================== \\

	// ====================== BEGIN OF TASK FUNCTIONS ======================= \\
	{
		$scope.getTopicHref = function (task) {
			url = $scope.urlsTopicsShow.replace('0', task.topicid);
			task.hrefTopic = url;
			return task.hrefTopic;
		}

		$scope.getOfficeHref = function (task) {
			url = $scope.urlsOfficeShow.replace('0', task.officeid);
			task.hrefOffice = url;
			return task.hrefOffice;
		}


		// Calculate sum of subtasks estimatimated time + parent task estimated
		function totalEstimated()
		{
			var regex = new RegExp('bug', 'mig');
			_.map($scope.tasks, function(task){
				if(task.subtasks && task.subtasks.length > 0)
				{
					_.map(task.subtasks, function(sub){
						if(!task.name.match(regex))
						{
							task.estimated += sub.estimated;
						}
					})
				}
			})
		}

		function resetTasks(task)
		{
			_.map($scope.tasksNotification, function(tn){
				if(tn.resourceid==task.id)
					task.newCommentsCount=0;
			});
		}

		function getTasks()
		{
			$http({
				method: "POST",
				url: $scope.urlsTasksGet,
				data: $scope.filter
			})
				.success(function(response){
					if (response.result) {
						$scope.tasks = prepareTasks(response.result);
						calculateNotifications();
						setTimeout(addTooltips, 100);
						$scope.taskLoaded = 'visible-element';
						totalEstimated();
					}
					$scope.taskCount = ($scope.tasks) ? $scope.tasks.length : 0;
				})
		}
		function prepareTasks(tasks)
		{
			_.map(tasks, function(task){
				$scope.changeHrefTopic(task);
				if (task.parentid == null) task.subtasks = [];
				if(task.startdate != undefined)
				{
					task.startdate.date = task.startdate.date.substring(0, 16);
					task.startdate.date = task.startdate.date.substring(5, 16);
				}
				if(task.enddate != undefined)
				{
					task.enddate.date = task.enddate.date.substring(0, 16);
					task.enddate.date = task.enddate.date.substring(5, 16);
				}


			});

			var groupedList = _.groupBy(tasks, function(task){ return task.parentid; });

			var topList = groupedList[null];
			delete groupedList[null];
			_.map(topList, function(task){
				if (typeof groupedList[task.id] != 'undefined')
				{
					task.subtasks = groupedList[task.id];
					delete groupedList[task.id];
				}
			});
			for (key in groupedList)
				topList = topList.concat(groupedList[key]);
			return topList;
		}

		$scope.removeTask = function(task)
		{
			var remove = confirm("Realy want to remove?");
			if (!remove) return;
			var url = $scope.urlsTasksRemove.replace('0', task.id);

			$http({
				method: "GET",
				url: url
			})
				.success(function(response){
					if (response.result)
					{
						_.map($scope.tasks, function(task, i){
							if (task.id == response.result) $scope.tasks.splice(i, 1);
						});
					}
					var taskCnt = ($scope.tasks) ? $scope.tasks.length : null;
				})
		};

	$scope.changeDrop = function(task)
	{
		if (task.subtasks.length == 0) return;
		task.dropped = !task.dropped;
	};

	$scope.changeHrefTopic = function(task)
	{
		url = $scope.urlsTopicsShow.replace('0', task.topicid);
		task.hrefTopic = url;

		url = $scope.urlsOfficeShow.replace('0', task.officeid);
		task.hrefOffice = url;
	};

	function addTooltips()
	{
		$('.tooltips').tooltip({
		      selector: "a",
		      container: "body"
		    });
		$("[data-toggle=popover]").popover();
	}

	$scope.addTask = function(task)
	{
		var parentid = (typeof task != 'undefined') ? task.id : null;
		if(ENTITY.type == "topic")
			$scope.curentTopic = ENTITY.id;
		else
			$scope.curentTopic = null;
		$http({
			method: "GET",
			url: $scope.urlsTasksAdd,
			params: { parentid: parentid }
			  })
		.success(function(response){
			var modalInstance = $modal.open({
			      template: response,
			      controller: 'AddTasksController',
			      resolve: {
			    	  users: function(){return $scope.office_users;},
			    	  parentid: function(){return parentid;},
			    	  curentTopic: function(){return $scope.curentTopic}
			      }
			    });
				modalInstance.result.then(function (addedTask) {
					$scope.changeHrefTopic(addedTask);
					getTasks();
				}, function(){});
			});
	};

		$scope.somevar = STATUSES;

	function checkEstimated()
	{
		_.map(STATUSES, function(s){
			if (s.id == $scope.task.statusid)
				$scope.estimated = s.updateEstimate;
		});
	}

	$scope.openTask = function(task)
	{
		var url = $scope.urlsTasksEdit.replace('0', task.id);
        task.newCommentsCount = 0;
		$http({
			method: "GET",
			url: url
			  })
		.success(function(response){
				if(response.result){
					$scope.task = response.result.task;
					(function(){
						$scope.task.esth = Math.floor($scope.task.estimated/60);
						$scope.task.estm = $scope.task.estimated%60;
					})();
					$scope.timestamp = response.result.timestamp;
					$scope.token = response.result.token;
					$scope.task.topicsIdsCurrent = _.map($scope.task.topics, function(t){return t.id;});
					$scope.comment = "";
					$scope.editingPost = null;
					$scope.entityid = task.id;
					$scope.userid = window.USER.id;
					$scope.checkEstimated = checkEstimated;
					$scope.urlsPostsTaskAdd = JSON_URLS.urlsPostsTaskAdd.replace('0', task.id);
					$scope.availableStatus = response.result.availableStatus;
					$scope.availableTypes = response.result.availableTypes;
					$scope.topics = response.result.topics;
					$scope.posts = response.result.postsTask;
					$scope.containerTask = $('#conversation-task');
					$scope.messageContainerTask = $('#write-message-task');
					$scope.posts = response.result.posts;
					$scope.containerTask.animate({ scrollTop: $scope.containerTask.height()+1900 },1000);
					$scope.showTaskPanel = true;
					$scope.displayRHP = 'displayBlock';

					if ($scope.task.name != undefined)
						event.preventDefault();
					$('#controlButtons').css('width', '72%');
					$('#panel').css('width', '67%');
					$('#chat').css('width', '50%');

					$('#pagination').removeClass('col-md-8').addClass('col-md-6');
					setTimeout(function(){
						$('#conversation-task1').height(0);
						var RSB = document.getElementById("right-sidebar").offsetHeight;
						var chattop = $('#conversation-task1').position().top;
						var newchatheigth = RSB - chattop;
						$('#conversation-task1').height(newchatheigth-30);
					},100);
					_.map(STATUSES, function(s){
						if ((s.id == $scope.task.statusid) && (s.updateEstimate == true))
						{
							$scope.task.estimated = parseInt($scope.task.esth)*60 + parseInt($scope.task.estm);
						}
					});
					$scope.task = task;
				}
		})


	};

	$scope.editTask = function(event){
		event.preventDefault();
		var url = $scope.urlsTasksEdit.replace('0', $scope.task.id);
		_.map(STATUSES, function(s){
			if ((s.id == $scope.task.statusid) && (s.updateEstimate == true))
			{
				$scope.task.estimated = parseInt($scope.task.esth)*60 + parseInt($scope.task.estm);
			}

		});
		$http({
			method: "POST",
			url: url,
			data: $scope.task
		})
			.success(function(response){
				if (response.result) {
					$scope.task = response.result;
					$('#chat').css('width','80%');
					$('#controlButtons').css('width', '100%');
					$('#panel').css('width','97%');
					$('.conteinerForPanel').css('font-size', '12px');
					$('#pagination').removeClass('col-md-6').addClass('col-md-8');
					$('#chevron').removeClass('fa-angle-double-right').addClass('fa-angle-double-left');
					$scope.showTaskPanel = !$scope.showTaskPanel;
					$scope.showChat = false;
					$scope.addingDocuments = false;
					$scope.displayRHP = 'displayNone';
					getTasks();
				}
				else
				if (response.message != undefined)
				{
					alert(response.message);
				}
			});
	};

	$scope.checkItem = function(documentid)
	{
		_.map($scope.documents, function(d){
			if (d.id == documentid) d.checked = !d.checked;
		});
	};

	function bindList()
	{
		$('.finish').click(function(){
			$(this).parent().toggleClass('finished');
			$(this).toggleClass('fa-square-o');
		});
	}

	_.map(STATUSES, function(s){
		$scope.filter.status.push(s.id);
	});
	$scope.filter.status.splice(14, 1);

	getTasks();

	}
	// ====================== END OF TASK FUNCTIONS ========================= \\

	// ====================== BEGIN OF DOCUMENTS FUNCTIONS ================== \\
	{
		$scope.documents = [];
		$scope.urlsDocumentsGet = JSON_URLS.documentsGet;

		function prepareDocuments(documents) {
			return _.map(documents, function (d) {
				d.checked = false;
				return d;
			});
		}

		function getDocuments() {
			$http({
				method: "GET",
				url: $scope.urlsDocumentsGet,
				params: {
					userid: USER.id
				}
			})
				.success(function (response) {
					if (response.result) {
						$scope.documents = prepareDocuments(response.result);
						setTimeout(bindList, 500);
					}
				})
		}
	}
	// ====================== END OF DOCUMENTS FUNCTIONS ==================== \\

	$scope.isEditable = function(post) {
		var postedTime = new Date(Date.parse(post.posted.date));
		var now = new Date();
		var utc = new Date(now.getTime() + now.getTimezoneOffset() * 60000);
		var minutesAgo = Date.minutesBetween(postedTime, utc);
		return (minutesAgo <= 5 && post.userid == $scope.userid);
	};

	$scope.editPost = function(post) {
		if ((!$scope.isEditable(post)) || ($scope.editingPost != null)) return;
		$scope.editingPost = post;
		$scope.messageContainerTask.val(post.message);
	};

	$scope.pressEnter = function(e) {
		if ((e.shiftKey == false) && ( e.keyCode == 13 )) {
			e.preventDefault();
			$scope.addPost();
		}
	};

	$scope.addPost = function() {
		$scope.comment = $scope.messageContainerTask.val();
		var msg = $scope.comment;
		var symb_index = msg.indexOf("@");
		var tmp_str = msg.substring(symb_index);
		var space_index = tmp_str.indexOf(" ");
		var user_to_send_name = "";
		if(space_index != -1){
			user_to_send_name = tmp_str.substring('1',space_index);
		} else {
			user_to_send_name = tmp_str.substring('1');
		}
		var post = {
			entityid: $scope.entityid,
			userid: $scope.userid,
			message: $scope.comment,
			posted: new Date(),
			usertosendname: user_to_send_name
		};

		if(symb_index != -1){
			$http({
				method: "POST",
				url: $scope.urlSendPrivateMsg,
				data: post })
				.success(function(response2){
				})
		}
		if ($scope.editingPost)
			post.postid = $scope.editingPost.id;
		$http({
			method: "POST",
			url: $scope.urlsPostsTaskAdd,
			data: post })
			.success(function(response){
				if (response.result)
				{
					if ($scope.editingPost == null)
					{
						$scope.posts.push(response.result);
						$scope.containerTask.animate({ scrollTop: $scope.containerTask.height()+1900 },1000);
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
				$scope.comment = "";
				$scope.messageContainerTask.val("");
				$scope.messageContainerTask.focus();
			})
	};

	function insertDocumentsLinks(documents)
	{
		_.map(documents, function(d){
			$scope.comment += '\n<a href="'+$scope.urlsBase+d.url+'" download="'+d.name+'">'+d.name+'</a>';
		});
	}

	$scope.addDocuments = function(event)
	{
		event.preventDefault();
		if ($scope.addingDocuments == false) {
			getDocuments();
		} else {
			insertDocumentsLinks(_.filter($scope.documents, function(d){return d.checked;}));
		}
		$scope.addingDocuments = !$scope.addingDocuments;
	};

	$scope.showPosts = function(task)
	{
		var url = $scope.urlsPostsTaskShow.replace('0', task.id);
		$http({
			method: "GET",
			url: url
			  })
		.success(function(response){
			var modalInstance = $modal.open({
			      template: response,
			      controller: 'ShowPostsController',
			      resolve: {
						task: function()
						{
							return task;
						}
					}
			    });
				modalInstance.result.finally(function () {
				if (modalInstance.result.response.result != null)
				{
					_.map($scope.tasks, function(t) {
						if(t.id == modalInstance.result.response.result)
						{
							t.newCommentsCount = 0;
						}
					});
				}
				clearInterval(refreshIntervalId);
			});
            var refreshIntervalId = setInterval(function(){
            	if (modalInstance.result.response.result != null)
			{
				_.map($scope.tasks, function(t){
					if(t.id == modalInstance.result.response.result)
					{
						t.newCommentsCount = 0;
					}
				});
			} }, 2000);
		});
	}

}])
.controller('AddTasksController', ['$scope', '$http', '$modalInstance', 'users', 'parentid', 'curentTopic', function($scope, $http, $modalInstance, users, parentid, curentTopic){
	console.log('AddTasksController was loaded!');

	$scope.urlsTasksAdd = JSON_URLS.tasksAdd;
	$scope.estimated = false;
	$scope.users = users;
	$scope.task = {
			statusid: null,
			priority: 'high',
			userid: null,
			parentid: parentid,
			esth: 0,
			estm: 0,
			topicid: ENTITY
	};

	setTimeout(function(){
		var topics = angular.element('#topics');
		if (topics[0].length>0)
		{
			$(topics[0][1]).attr('selected', true);
			$scope.task.topicid = curentTopic;
			topics[0][0] = null;
		}
	}, 500);

	STATUSES.forEach(function(status){
		if (status.initial && $scope.task.statusid == null)
			$scope.task.statusid = status.id;
	});
	checkEstimated();

	$scope.checkEstimated = checkEstimated;
	function checkEstimated()
	{
		_.map(STATUSES, function(s){
			if (s.id == $scope.task.statusid)
				$scope.estimated = s.updateEstimate;
		});
	}

	$scope.addTaskAction = function(event)
	{
		if ($scope.task.name != undefined && $scope.task.description != undefined){
			event.preventDefault();
		}
		$scope.task.estimated = parseInt($scope.task.esth)*60 + parseInt($scope.task.estm);
		$http({
			method: "POST",
			url: $scope.urlsTasksAdd,
			data: $scope.task
			  })
		.success(function(response){
			if (response.result) {
				$modalInstance.close(response.result);
			}
		});
	}
}]);
