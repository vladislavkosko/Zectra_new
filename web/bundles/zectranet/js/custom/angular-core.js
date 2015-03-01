'use strict';
var Intranet = angular.module('Intranet', ['ngRoute','ui.bootstrap','ngSanitize'])
.config(['$interpolateProvider', '$httpProvider', '$routeProvider', '$locationProvider',
		function ($interpolateProvider, $httpProvider, $routeProvider, $locationProvider) {
			$interpolateProvider.startSymbol('[[');
			$interpolateProvider.endSymbol(']]');
			$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

			$routeProvider
				.when('/Chat', {
					templateUrl: JSON_URLS.chatTemplate,
					controller: 'ChatController',
					controllerAs: 'chatctrl'
				})
				.when('/Tasks', {
					templateUrl: JSON_URLS.taskboardTemplate,
					controller: 'TasksController',
					controllerAs: 'chapter'
				});

			$locationProvider.html5Mode({
				enabled: false,
				requireBase: false
			});
		}]);

Intranet.factory('$paginator', function() {
    var paginator = {};
    
    paginator.countPages = 5;
    paginator.curPageId = 1;
    paginator.pages = [];
    paginator.postsPerPage = 5;
    paginator.postsPerPageValues = [5, 10, 20, 50, 100];
    paginator.countPosts = 100;
	paginator.showPagesOnPage = [];

    paginator.nextPage = function(event){
    	event.preventDefault();
    	if (this.curPageId < this.countPages)
    		this.curPageId++;
		this.showPages();
    };
    
    paginator.prevPage = function(event){
    	event.preventDefault();
    	if (this.curPageId > 1)
    		this.curPageId--;
		this.showPages();
    };
    
    paginator.toPage = function(event, id){
    	event.preventDefault();
    	if ((id > 0) && (id <= this.countPages))
    		this.curPageId = id;
		this.showPages();
    };
    
    paginator.firstPage = function(event){
    	event.preventDefault();
    	this.curPageId = 1;
		this.showPages();
    };
    
    paginator.lastPage = function(event){
    	event.preventDefault();
    	this.curPageId = this.countPages;
		this.showPages();
    };

	paginator.showPages = function() {
		if (this.countPages < 4) this.pages = _.range(1, this.countPages + 1);
		else
		{
			if (this.curPageId < 4)
			{
				if (this.curPageId + 3 > this.countPages)
					this.pages = _.range(1, this.countPages + 1);
				else
					this.pages = _.range(1, this.curPageId + 4);
			}
			else if (this.curPageId > this.countPages - 3)
				this.pages = _.range(this.curPageId - 3, this.countPages + 1);
			else
			{
				if (this.curPageId + 3 > this.countPages)
					this.pages = _.range(this.curPageId - 3, this.countPages + 1);
				else
					this.pages = _.range(this.curPageId - 3, this.curPageId + 4);
			}
		}
	};
    
    paginator.init = function(postsCount, postsPerPage){
    	this.countPosts = postsCount;
    	this.postsPerPage = postsPerPage;
    	this.countPages = Math.ceil(postsCount/postsPerPage);
    	this.curPageId = 1;
    	
    	if(this.countPages == 0) this.countPages++;

		this.showPages();
    	
    	return this;
    };
	
	return paginator;
 })
 .filter('estimatedTime', function(){
	 return function(minutes){
		 return Math.floor(minutes/60) + ' h ' + minutes%60 + ' m';
	 }
 })
.directive('datetime', function(){
	var directive = {};
	directive.restrict = 'E';
	directive.template = "<label class='label' style='background-color: [[color]];'>[[dateTimeString]]</label>";

	directive.link = function (scope, element, attr) {
		scope.color = (attr.datetime > 0) ? 'green' : 'red';
		var sign = (attr.datetime >= 0) ? '' : '- ';
		attr.datetime = Math.abs(attr.datetime);
        scope.dateTimeString = sign + Math.floor(attr.datetime/(60*60)) + ' h ' + Math.floor(attr.datetime%(60*60)/60) + ' m ';
    };

	return directive;
});

Intranet.directive('document', function() {
	return {
		restrict: 'E',
		priority: 5000,
		transclude: true,
		template: function(element, attrs) {
			var htmlElement =
				'<div style="display: block;">'
					+ '<div class="not-modal"><img/></div>'
					+ '<a class="show-element"></a>'
				+ '</div>'
				/*+ '<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'
					+ '<div class="modal-dialog">'
					+ '<img class="img-responsive" style="max-width: 100%; max-height: 100%; width: 100%; height: 100%;" alt=""/>'
					+ '</div>'
				+ '</div>'*/;
			return htmlElement;
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
			var file = attrs.file;
			var extension = file.match(regex);

			var link = element.find('a');
			var image = element.find('img');
			var imageContainer = image.parent();

			image.css('object-fit', 'cover');
			image.css('width', attrs.width);
			image.css('height', attrs.height);
			image.css('top', 'auto');
			image.css('left', 'auto');
			image.css('margin', 'auto');
			link.css('width', attrs.width);

			var img = null;
			if (extension != '.png' && extension != '.gif' && extension != '.jpeg' && extension != '.jpg') {
				img = JSON_URLS.asset + 'bundles/intranet/' + extensions[extension];
				image.css('cursor', 'pointer');
				image.bind('click', function () {
					document.location = JSON_URLS.asset + file;
				});
			} else {
				img = JSON_URLS.asset + file;
				link.attr('href', JSON_URLS.asset + file);
				link.html('<i class="fa fa-download"></i> ' + attrs.name);
				image.attr('src', img);
			}
		}
	}
});

Intranet.directive('highlight', ['$sce', function($sce) {
	return {
		restrict: 'A'
		,
		link: function($scope, element, attrs) {
			var username = true;
			var message = $sce.getTrustedHtml($scope.post.message);
			if (typeof (OFFICE_NAME) != "undefined") {
				regex = new RegExp('@' + OFFICE_NAME, 'mig');
				matches = message.match(regex);
				if (matches) {
					username = false;
					for (var i = 0; i < matches.length; i++) {
						message = message.replace(matches[i], '<span class="global-office-msg-highlight">' + matches[i] + '</span>');
					}
				}
			}
			regex = new RegExp('@all', 'mig');
			matches = message.match(regex);
			if (matches) {
				username = false;
				message = message.replace(regex, '<span class="global-office-msg-highlight">' + matches[0] + '</span>');
			}

			var msg = message;
			if (username) {
				var regex = new RegExp('@[A-Za-z]{1,20}', 'mig');
				var matches = msg.match(regex);
				if (matches != null) {
					for (var i = 0; i < matches.length; i++) {
						msg = msg.replace(matches[i], '<span class="user-names-highlight">' + matches[i] + '</span>');
					}
					message = msg;
				}
			}
			$scope.post.message = $sce.trustAsHtml(message);
		}
	}
}]);

Intranet.directive('chatrepeatend', function($rootScope) {
	return {
		restrict: 'A'
		,
		link: function($scope, element, attrs) {
			if ($scope.$last) {
				$rootScope.ChatLoaded = 1;
			}
		}
	}
});

console.log('Angular core is loaded...');