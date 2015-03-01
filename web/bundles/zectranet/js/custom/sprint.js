Intranet.controller('SprintController', ['$scope', '$http', '$modal', function($scope, $http, $modal) {
    console.log('SprintController was loaded!');
    $scope.urlAddSprint = JSON_URLS.addSprint;
    $scope.urlAddTaskToSprint = JSON_URLS.addTaskToSprint;
    $scope.urlChangeStatus = JSON_URLS.changeStatus;
    $scope.urlShowSprint = JSON_URLS.showSprint;
    $scope.urlDelSprint = JSON_URLS.delSprint;
    $scope.showOffice = JSON_URLS.officeShow;
    $scope.urlGetTasks = JSON_URLS.getTasksURL;
    $scope.urlsTasksEdit = JSON_URLS.tasksEdit;
    $scope.urlsTopicsShow = JSON_URLS.topicsShow;
    $scope.urlSendPrivateMsg = JSON_URLS.sendPrivateMsg;
    $scope.urlSendPrivateMsgOffice = JSON_URLS.urlsSendPrivateMessageOffice;
    $scope.urlSendPrivateMsgTopic = JSON_URLS.urlsSendPrivateMessageTopic;
    $scope.urlsOfficeShow = JSON_URLS.urlOfficeForTasks;
    STATUSESSprint = [];
    $scope.tasksSprint = [];

    $scope.filterSprint = {
        status: [],
        priority: [],
        name: "",
        user: [],
        topic: []
    };
    $scope.showTaskPanel = false;
    $scope.tooltips = "Show chat";
    $scope.displayRHP = 'displayNone';

    $scope.closeModal = function(event) {
        event.preventDefault();
        $('.status').css('white-space', 'nowrap');
        $('#pagination').removeClass('col-md-6').addClass('col-md-8');
        $('#chevron').addClass('fa-angle-double-left').removeClass('fa-angle-double-right');
        $scope.showTaskPanel = false;
        $scope.showChat = false;
        $scope.addingDocuments = false;
        $scope.displayRHP = 'displayNone';
    };

    $scope.showChatPanel = function() {
        $scope.showChat = !$scope.showChat;
        if($scope.showChat) $scope.tooltips = "Hide chat";
        else $scope.tooltips = "Show chat";
        if ($('#chevron').hasClass('fa-angle-double-left')) {
            $('#chevron').removeClass('fa-angle-double-left').addClass('fa-angle-double-right');
        } else {
            $('#chevron').removeClass('fa-angle-double-right').addClass('fa-angle-double-left');
        }
    };

    $scope.addSprint = function()
    {
        $http({
            method: "GET",
            url: $scope.urlAddSprint
        })
            .success(function(response) {
                var modalInstance = $modal.open({
                    template: response,
                    controller: 'AddSprintController'
                })
            })
    };

    $scope.addToSprint = function(taskid)
    {
        var url = $scope.urlAddTaskToSprint.replace('0', taskid);
        $http({
            method: "GET",
            url: url
        })
            .success(function(response) {
                var modalInstance = $modal.open({
                    template: response,
                    controller: 'AddTaskToSprintController'
                })
            })
    };

    $scope.changeStatus = function(sprintid)
    {
        var url = $scope.urlChangeStatus.replace('0', sprintid);
        $http({
            method: "GET",
            url: url
        })
            .success(function(response) {
                if(response.message) {
                    document.location.href = $scope.urlShowSprint;;
                }
            })
    }

    $scope.deleteSprint = function(sprintid){
        message = confirm("Delete this sprint?");
        if(message){
            var url = $scope.urlDelSprint.replace('0', sprintid);
            $http({
                method: "GET",
                url: url
            })
                .success(function(response){
                    if(response.message == "ok")
                    {
                        var url = $scope.showOffice.replace('0', 1);
                        document.location.href = url;
                    }
                    else
                        alert(response.message);
                })
        }
    }

    $scope.changeDrop = function(task)
    {
        if (task.subtasks.length == 0) return;
        task.dropped = !task.dropped;
    };

    function prepareTasks(tasks)
    {
        _.map(tasks, function(task){
            //$scope.changeHrefTopic(task);
            if (task.parentid == null) task.subtasks = [];
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

    function totalEstimated()
    {
        var regex = new RegExp('bug', 'mig');
        _.map($scope.tasksSprint, function(task){
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
    
    function getTasks() {
        $http({
            method: "GET",
            url: $scope.urlGetTasks
        })
            .success(function(response){
               if(response.result)
               {
                   $scope.tasksSprint = prepareTasks(response.result);
                   setTimeout(function() {
                       addTooltips();
                       return;
                   }, 100);
               }
                else alert(response.message);
            });
    }

    $scope.InitTasks = getTasks;

    $scope.changeHrefTopic = function(task)
    {
        url = $scope.urlsTopicsShow.replace('0', task.topicid);
        task.hrefTopic = url;
    };

    $scope.getTopicHrefSprint = function (task) {
        url = $scope.urlsTopicsShow.replace('0', task.topicid);
        task.hrefTopic = url;
        return task.hrefTopic;
    }

    $scope.getOfficeHrefSprint = function (task) {
        url = $scope.urlsOfficeShow.replace('0', task.officeid);
        task.hrefOffice = url;
        return task.hrefOffice;
    }

    if (typeof (STATUSES) != 'undefined') {
        STATUSES.forEach(function (status) {
            if ($scope.taskSprint) {
                if (status.initial && $scope.taskSprint.statusid == null) {
                    $scope.taskSprint.statusid = status.id;
                }
            }
        });
    }

    function checkEstimatedSprint()
    {
        if (typeof (STATUSES) != 'undefined') {
            _.map(STATUSES, function (s) {
                if (s.id == $scope.taskSprint.statusid)
                    $scope.estimatedSprint = s.updateEstimate;
            });
        }
    }

    if (typeof (STATUSES) != 'undefined') {
        _.map(STATUSES, function(s){
            $scope.filterSprint.status.push(s.id);
        });
    }
    $scope.filterSprint.status.splice(14, 1);

    $scope.openTaskSprint = function(task)
    {
        var url = $scope.urlsTasksEdit.replace('0', task.id);
        task.newCommentsCount = 0;
        $http({
            method: "GET",
            url: url
        })
            .success(function(response){
                if(response.result){
                    $scope.taskSprint = response.result.task;
                    (function(){
                        $scope.taskSprint.esth = Math.floor($scope.taskSprint.estimated/60);
                        $scope.taskSprint.estm = $scope.taskSprint.estimated%60;
                    })();
                    $scope.timestamp = response.result.timestamp;
                    $scope.token = response.result.token;
                    $scope.taskSprint.topicsIdsCurrent = _.map($scope.taskSprint.topics, function(t){return t.id;});
                    $scope.comment = "";
                    $scope.editingPostSprint = null;
                    $scope.entityid = task.id;
                    $scope.userid = window.USER.id;
                    $scope.users = response.result.users;
                    $scope.checkEstimatedSprint = checkEstimatedSprint;
                    $scope.urlsPostsTaskAdd = JSON_URLS.urlsPostsTaskAdd.replace('0', task.id);
                    $scope.availableStatusSprint = response.result.availableStatus;
                    $scope.availableTypes = response.result.availableTypes;
                    $scope.topics = response.result.topics;
                    $scope.postsSprint = response.result.postsTask;
                    $scope.containerTask = $('#conversation-task');
                    $scope.messageContainerTask = $('#write-message-task');
                    $scope.postsSprint = response.result.posts;
                    $scope.containerTask.animate({ scrollTop: $scope.containerTask.height()+1900 },1000);
                    $scope.avatarURL = JSON_URLS.avatar;
                    $scope.showTaskPanel = true;
                    $scope.displayRHP = 'displayBlock';
                    if ($scope.taskSprint.name != undefined)
                        event.preventDefault();

                    if (typeof (STATUSES) != 'undefined') {
                        _.map(STATUSES, function(s){
                            if ((s.id == $scope.taskSprint.statusid) && (s.updateEstimate == true))
                            {
                                $scope.taskSprint.estimated = parseInt($scope.taskSprint.esth) *
                                60 + parseInt($scope.taskSprint.estm);
                            }
                        });
                    }
                }
            })
    };

    $scope.editTaskSprint = function(event){
        event.preventDefault();
        var url = $scope.urlsTasksEdit.replace('0', $scope.taskSprint.id);
        if (typeof (STATUSES) != 'undefined') {
            _.map(STATUSES, function(s){
                if ((s.id == $scope.taskSprint.statusid) && (s.updateEstimate == true))
                {
                    $scope.taskSprint.estimated = parseInt($scope.taskSprint.esth)*60 +
                    parseInt($scope.taskSprint.estm);
                }
            });
        }
        $http({
            method: "POST",
            url: url,
            data: $scope.taskSprint
        })
            .success(function(response){
                if (response.result) {
                    $scope.showTaskPanel = ! $scope.showTaskPanel;
                    $('#chat').css('width','80%');
                    $('#panel').css('width','97%');
                    $('.status').css('white-space', 'nowrap');
                    $('.conteinerForPanel').css('font-size', '12px');
                    $('#pagination').removeClass('col-md-6').addClass('col-md-8');
                    $('#chevron').removeClass('fa-angle-double-right').addClass('fa-angle-double-left');
                    $scope.showChat = false;
                    $scope.addingDocuments = false;
                    $scope.displayRHP = 'displayNone';
                    getTasks();
                } else {
                    if (response.message != undefined) {
                        alert(response.message);
                    }
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
        $('.finish').click(function() {
            $(this).parent().toggleClass('finished');
            $(this).toggleClass('fa-square-o');
        });
    }

    $scope.documents = [];
    $scope.urlsDocumentsGet = JSON_URLS.documentsGet;

    function prepareDocuments(documents)
    {
        return _.map(documents, function(d){d.checked = false; return d;});
    }

    function getDocuments()
    {
        $http({
            method: "GET",
            url: $scope.urlsDocumentsGet,
            params: {
                userid: USER.id
            }
        })
            .success(function(response){
                if (response.result)
                {
                    $scope.documents = prepareDocuments(response.result);
                    setTimeout(bindList, 500);
                }
            })
    }

    function addTooltips() {
        $('.tooltips').tooltip({
            selector: "a",
            container: "body"
        });
        $("[data-toggle=popover]").popover();
    }

    $scope.isEditableSprint = function(post)
    {
        /*var postedTime = new Date(Date.parse(post.posted.date));
        var now = new Date();
        var utc = new Date(now.getTime() + now.getTimezoneOffset() * 60000);
        var minutesAgo = Date.minutesBetween(postedTime, utc);
        return (minutesAgo <= 5 && post.userid == $scope.userid);*/
        return false;
    };

    $scope.editPostSprint = function(post)
    {
        if ((!$scope.isEditableSprint(post)) || ($scope.editingPostSprint != null)) return;
        $scope.editingPostSprint = post;
        $scope.messageContainerTask.val(post.message);
    };

    $scope.pressEnter = function(e)
    {
        if ((e.shiftKey == false) && ( e.keyCode == 13 ))
        {
            e.preventDefault();
            $scope.addPostSprint();
        }
    };

    $scope.addPostSprint = function()
    {
        $scope.comment = $scope.messageContainerTask.val();
        var msg = $scope.comment;
        var symb_index = msg.indexOf("@");
        var tmp_str = msg.substring(symb_index);
        var space_index = tmp_str.indexOf(" ");
        var user_to_send_name = "";
        if(space_index != -1) {
            user_to_send_name = tmp_str.substring('1',space_index);
        } else {
            user_to_send_name = tmp_str.substring('1');
        }

        //var regex = new RegExp('@');


        var post = {
            entityid: $scope.entityid,
            userid: $scope.userid,
            message: $scope.comment,
            posted: new Date(),
            usertosendname: user_to_send_name
        };

        if ($scope.taskSprint.topicid != null) {
            $scope.urlSendPrivateMsg = $scope.urlSendPrivateMsgTopic.replace('0', $scope.taskSprint.topicid);
        } else {
            $scope.urlSendPrivateMsg = $scope.urlSendPrivateMsgOffice.replace('0', $scope.taskSprint.officeid);
        }

        if(symb_index != -1){
            $http({
                method: "POST",
                url: $scope.urlSendPrivateMsg,
                data: post })
                .success(function(response2){
                })
        }
        if ($scope.editingPostSprint)
            post.postid = $scope.editingPost.id;
        $http({
            method: "POST",
            url: $scope.urlsPostsTaskAdd,
            data: post })
            .success(function(response){
                console.log("Created post for task: ", response.result);
                if (response.result)
                {
                    if ($scope.editingPostSprint == null) {
                        $scope.postsSprint.push(response.result);
                        $scope.containerTask.animate({ scrollTop: $scope.containerTask.height()+1900 },1000);
                    } else {
                        _.map($scope.postsSprint, function(p, i){
                            if (p.id == response.result.id)
                                $scope.postsSprint[i] = response.result;
                        });
                    }
                }
                $scope.editingPostSprint = null;
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
        if ($scope.addingDocuments == false)
        {
            getDocuments();
        }else
        {
            insertDocumentsLinks(_.filter($scope.documents, function(d){return d.checked;}));
        }
        $scope.addingDocuments = !$scope.addingDocuments;
    };

    $scope.addDocSprint = function () {
        $scope.addingDocuments = !$scope.addingDocuments;
    };

    $scope.showPosts = function(task)
    {
        var url = $scope.urlsPostsTaskShow.replace('0', task.id);
        $http({
            method: "GET",
            url: url
        })
            .success(function(response) {
                var modalInstance = $modal.open({
                    template: response,
                    controller: 'ShowPostsController',
                    resolve: {
                        task: function(){return task;}
                    }
                });
                modalInstance.result.finally(function () {
                    if (modalInstance.result.response.result != null) {
                        _.map($scope.tasksSprint, function(t) {
                            if(t.id == modalInstance.result.response.result) {
                                t.newCommentsCount = 0;
                            }
                        });
                    }
                    clearInterval(refreshIntervalId);
                });
                var refreshIntervalId = setInterval(function() {
                    if (modalInstance.result.response.result != null) {
                        _.map($scope.tasksSprint, function(t){
                            if(t.id == modalInstance.result.response.result) {
                                t.newCommentsCount = 0;
                            }
                        });
                    } }, 2000);
            });
    };

}]);
Intranet.controller('AddSprintController', ['$scope', '$http', '$modalInstance', function($scope, $http, $modalInstance) {
    console.log('AddSprintController was loaded!');
    $scope.urlAddSprint = JSON_URLS.addSprint;
    $scope.urlShowSprint = JSON_URLS.showSprint;
    $scope.sprint = {};
    $scope.addSprint = function(event)
    {
        if ($scope.sprint.name != undefined && $scope.sprint.description != undefined){
            event.preventDefault();
        }
        $http({
            method: "POST",
            url: $scope.urlAddSprint,
            data: $scope.sprint
        })
            .success(function(response){
                if (response.result) {
                    $modalInstance.close(response.result);
                }
                var id = response.result.id;
                var url = $scope.urlShowSprint.replace('0', id)
                document.location.href = url;
            })
    }
}]);
Intranet.controller('AddTaskToSprintController', ['$scope', '$http', '$modalInstance', function($scope, $http, $modalInstance) {
    console.log('AddTaskToSprintController was loaded!');
    $scope.urlAddTaskToSprint = JSON_URLS.addTaskToSprint;
    $scope.urlShowSprint = JSON_URLS.showSprint;
    $scope.data = {};
    $scope.addTaskToSprint = function(event, taskid)
    {
        if ($scope.data.sprintid != undefined){
            event.preventDefault();
        }
        var url = $scope.urlAddTaskToSprint.replace('0', taskid);
        $http({
            method: "POST",
            url: url,
            data: $scope.data
        })
            .success(function(response){
                if (response.result) {
                    $modalInstance.close(response.result);
                } else {
                    alert(response.message);
                    $modalInstance.close(response);
                }
            });
    }

}]);