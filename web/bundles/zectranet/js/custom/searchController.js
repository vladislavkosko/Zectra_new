function PrepareTextToSearch(text)
{
    text = text.replace(new RegExp('-','g'), "%");
    text = text.replace(new RegExp('/','g'), "%");
    text = text.replace(new RegExp(',','g'), "%");
    text = text.replace(new RegExp('_','g'), "%");
    text = text.replace(new RegExp(';','g'), "%");
    text = text.replace(new RegExp(':','g'), "%");
    text = text.replace(new RegExp(' ','g'), "%");
    return text;
}

Intranet.controller('SearchController', ['$scope', '$http', '$rootScope', function($scope, $http, $rootScope) {
    console.log('SearchController was loaded!');

    $scope.search_tasks = null;
    $scope.search_chats = null;
    $scope.search_projects = null;
    $scope.searchCount = 0;

    $rootScope.ChatLoaded = 0;
    $rootScope.TopicsLoaded = 0;
    $scope.HighLightNext = NextSearch;
    $scope.HighLightPrev = PrevSearch;
    $scope.PageSearchCount = 0;
    $scope.searchControls = 'hidden-element';
    var QuickSearchUrl = JSON_URLS.quickSearch;

    $scope.InputChange = function () {
        if ($scope.searchInput.length >= 3 || !isNaN(parseInt($scope.searchInput))) {
            $scope.PageSearchCount = Highlight($scope.searchInput);
            if ($scope.PageSearchCount > 0) $scope.searchControls = 'visible-element';
            FastSearch($scope.searchInput);
        } else {
            $scope.search_tasks = null;
            $scope.search_chats = null;
            $scope.search_projects = null;
            $scope.search_task_posts = null;
            $scope.searchCount = 0;
            $scope.PageSearchCount = 0;
            $('body').removeHighlight();
        }
    };

    $rootScope.$watch('ChatLoaded', function() {
        if ($rootScope.ChatLoaded == 1) {
            $scope.searchInput = SearchText;
            $rootScope.ChatLoaded = 0;
            $scope.PageSearchCount = Highlight($scope.searchInput);
            if ($scope.PageSearchCount > 0) $scope.searchControls = 'visible-element';
        }
    });

    $scope.PreCheck = function(e, LocateURL) {
        if ($scope.searchInput && ($scope.searchInput.length >= 3 || !isNaN(parseInt($scope.searchInput)))) {
            location.href = LocateURL + $scope.searchInput;
        } else {
            e.preventDefault();
        }
    };

    $scope.pressEnter = function(e, LocateURL) {
        if (( e.keyCode == 13 ) && $scope.searchInput) {
            if (!isNaN(parseInt($scope.searchInput)) || $scope.searchInput.length >= 3) {
                location.href = LocateURL + $scope.searchInput;
            }
        }
    };

    function FastSearch(SearchRequest) {
        $http.post(QuickSearchUrl, {'request': PrepareTextToSearch(SearchRequest)})
            .success(function(response) {
                if (response.result.searchCount != 0) {
                    $scope.search_tasks = response.result.search_tasks;
                    $scope.search_chats = response.result.search_chats;
                    $scope.search_projects = response.result.search_projects;
                    $scope.search_task_posts = response.result.search_task_posts;
                    $scope.searchCount = response.result.searchCount;
                } else {
                    $scope.search_tasks = null;
                    $scope.search_task_posts = null;
                    $scope.search_projects = null;
                    $scope.search_chats = null;
                    $scope.searchCount = 0;
                }
            });
    }
}]);

Intranet.controller('ExtendedSearchController', ['$scope', '$http', function($scope, $http) {
    console.log('ExtendedSearchController was loaded...');
    $scope.search_tasks = null;
    $scope.search_office_chats = null;
    $scope.search_topic_chats = null;
    $scope.search_projects = null;
    $scope.searchCount = 0;
    var ExtendedSearchUrl = JSON_URLS.ExtendedSearchUrl;

    $scope.InputChange = function ()
    {
        if ($scope.searchInput.length >= 3 || !isNaN(parseInt($scope.searchInput))) {
            $scope.searchCount = null;
            $scope.search_tasks = null;
            $scope.search_chats = null;
            $scope.search_projects = null;
            $scope.search_task_posts = null;
            $scope.StartSearch($scope.searchInput);
        } else {
            $scope.search_tasks = null;
            $scope.search_chats = null;
            $scope.search_projects = null;
            $scope.search_task_posts = null;
            $scope.searchCount = 0;
        }
    };

    $scope.StartSearch = function Search(SearchRequest)
    {
        $http.post(ExtendedSearchUrl, {'request': PrepareTextToSearch(SearchRequest)})
            .success(function(response) {
                if (response.result.searchCount != 0) {
                    $scope.search_tasks = response.result.search_tasks;
                    $scope.search_chats = response.result.search_chats;
                    $scope.searchCount = response.result.searchCount;
                    $scope.search_projects = response.result.search_projects;
                    $scope.search_task_posts = response.result.search_task_posts;
                } else {
                    $scope.search_task_posts = null;
                    $scope.search_tasks = null;
                    $scope.search_chats = null;
                    $scope.search_projects = null;
                    $scope.searchCount = 0;
                }
            });
    }
}]);


var next,prev, next_element,
    prev_element, parent,tr_class_success,table_users_table_table_condensed_table_hover, body;

var animationOptions = {
    duration: '200',
    easing: 'linear'
};

function Highlight(searchTerm) {
    table_users_table_table_condensed_table_hover = document.getElementsByClassName('table users-table table-condensed table-hover ');
    tr_class_success =document.getElementsByClassName('success ng-scope ng-hide');
    parent = document.getElementById('conversation');
    body =  document.getElementsByTagName('body');
    $('body').removeHighlight();
    if (searchTerm) {
        $(table_users_table_table_condensed_table_hover).highlight(searchTerm);
        $(parent).highlight(searchTerm);
        $(tr_class_success).removeHighlight();

        var $ch =  $(body).find('span.highlight');
        if ($ch.length == 0) return 0;

        for (var i = 0; i < $ch.length; i++)
        {
            $ch.eq(i).attr('id', i);
        }

        next = 0;
        next_element = document.getElementById(next);

        var parent_next_element = $(next_element).parent().parent();
        var class_parent_of_parent_next_element = $(parent_next_element).attr('class');
        var position_tasks = $(table_users_table_table_condensed_table_hover).offset().top - ($(table_users_table_table_condensed_table_hover).offset().top / 3);
        var position_chats = (parent) ? $(parent).offset().top - ($(parent).offset().top / 3) : 5000;
        var position_next_element = $(next_element).offset().top - ($(next_element).offset().top / 3);

        if(position_next_element <= position_chats) // scroll to tasks
        {

            if(class_parent_of_parent_next_element == 'list-group-item ng-scope' && position_next_element < position_chats )
            {
                $(parent).animate({scrollTop: $(parent).scrollTop() + $(next_element).offset().top - $(parent).offset().top - 150}, animationOptions);
                $(body).animate({scrollTop: position_chats  }, animationOptions);
            }
            else
            {
                $(body).animate({scrollTop: position_tasks  }, animationOptions);
            }

        }
        if(position_next_element >= position_chats) // scroll to chats
        {


            $(body).animate({scrollTop: position_chats  }, animationOptions);
            $(parent).animate({scrollTop: $(parent).scrollTop() + $(next_element).offset().top - $(parent).offset().top - 150}, animationOptions);
        }

        if(parent != null && table_users_table_table_condensed_table_hover != null) {
            $ch.eq(next).css('background-color', ' #32CD32');
            $ch.eq(next).css('color', ' black');
        }
        return $ch.length;
    }
    return 0;
}

function NextSearch() {
    table_users_table_table_condensed_table_hover = document.getElementsByClassName('table users-table table-condensed table-hover ');
    tr_class_success =document.getElementsByClassName('success ng-scope ng-hide');
    parent = document.getElementById('conversation');
    body =  document.getElementsByTagName('body');

    var $ch =  $(body).find('span.highlight');


    if(next <= $ch.length - 2)
    {
        next= next + 1;
    }
    else
    {
        next=0;
    }

    prev = 0;
    if(next == 0) { prev=$ch.length - 1; } else { prev=next - 1;}

    next_element = document.getElementById(next);
    var parent_next_element = $(next_element).parent().parent();
    var class_parent_of_parent_next_element = $(parent_next_element).attr('class');
    var position_tasks = $(table_users_table_table_condensed_table_hover).offset().top - ($(table_users_table_table_condensed_table_hover).offset().top / 3);
    var position_chats = $(parent).offset().top - ($(parent).offset().top / 3);
    var position_next_element = $(next_element).offset().top ;

    if(position_next_element <= position_chats  ) // scroll to tasks
    {

        if(class_parent_of_parent_next_element == 'list-group-item ng-scope' && position_next_element < position_chats )
        {
            $(parent).animate({scrollTop: $(parent).scrollTop() + $(next_element).offset().top - $(parent).offset().top - 150}, animationOptions);
            $(body).animate({scrollTop: position_chats  }, animationOptions);
        }
        else
        {
            $(body).animate({scrollTop: position_tasks  }, animationOptions);
        }

    }
    if( position_next_element >= position_chats  ) // scroll to chats
    {


        $(body).animate({scrollTop: position_chats  }, animationOptions);
        $(parent).animate({scrollTop: $(parent).scrollTop() + $(next_element).offset().top - $(parent).offset().top - 150}, animationOptions);
    }

    if(parent != null && table_users_table_table_condensed_table_hover != null)
    {
        $ch.eq(next).css('background-color', ' #32CD32');
        $ch.eq(next).css('color', 'black');
        $ch.eq(prev).css('background-color', '#fff34d');
        $ch.eq(prev).css('color', 'black');
    }
}

function PrevSearch() {
    table_users_table_table_condensed_table_hover = document.getElementsByClassName('table users-table table-condensed table-hover ');
    tr_class_success = document.getElementsByClassName('success ng-scope ng-hide');
    parent = document.getElementById('conversation');
    body = document.getElementsByTagName('body');

    var $ch = $(body).find('span.highlight');

    prev = 0;
    if (next == 0) {
        next = $ch.length - 1;
        prev = 0;
    }
    else {
        next = next - 1;
        prev = next + 1;
    }

    next_element = document.getElementById(next);
    var parent_next_element = $(next_element).parent().parent();
    var class_parent_of_parent_next_element = $(parent_next_element).attr('class');
    var position_tasks = $(table_users_table_table_condensed_table_hover).offset().top - ($(table_users_table_table_condensed_table_hover).offset().top / 3);
    var position_chats = $(parent).offset().top - ($(parent).offset().top / 3);
    var position_next_element = $(next_element).offset().top ;

    if(position_next_element <= position_chats  ) // scroll to tasks
    {

        if(class_parent_of_parent_next_element == 'list-group-item ng-scope' && position_next_element < position_chats )
        {
            $(body).animate({scrollTop: position_chats  }, animationOptions);
            $(parent).animate({scrollTop: $(parent).scrollTop() + $(next_element).offset().top - $(parent).offset().top - 150}, animationOptions);
        }
        else
        {
            $(body).animate({scrollTop: position_tasks  }, animationOptions);
        }

    }
    if( position_next_element >= position_chats  ) // scroll to chats
    {
        $(body).animate({scrollTop: position_chats  }, animationOptions);
        $(parent).animate({scrollTop: $(parent).scrollTop() + $(next_element).offset().top - $(parent).offset().top - 150}, animationOptions);
    }

    if (parent != null && table_users_table_table_condensed_table_hover != null) {
        $ch.eq(next).css('background-color', ' #32CD32');
        $ch.eq(next).css('color', 'black');
        $ch.eq(prev).css('background-color', '#fff34d');
        $ch.eq(prev).css('color', 'black');
    }
}