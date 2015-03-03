var Zectranet = angular.module('Zectranet', ['ui.bootstrap','ngSanitize'])
.config(['$interpolateProvider', '$httpProvider',
		function ($interpolateProvider, $httpProvider) {
			$interpolateProvider.startSymbol('[[');
			$interpolateProvider.endSymbol(']]');
			$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
		}]);

Zectranet.factory('$paginator', function() {
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
 });

console.log('Angular core is loaded...');