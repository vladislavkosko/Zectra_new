Zectranet.controller('DocumentsController', ['$scope', '$http', '$rootScope', '$modal','$sce','$compile',
    function($scope, $http, $rootScope, $modal , $sce ,$compile){
    console.log('DocumentsController was loaded!');

    $scope.promise = null;
    $scope.documents = [];
    $scope.urlDeleteFile = JSON_URLS.deleteFile;
    $scope.urlRenameFile = JSON_URLS.renameFile;
    $scope.urlsDocumentsGet = JSON_URLS.documentsGet;
    $scope.asset = JSON_URLS.asset;
    $scope.curr_doc_id = null;
    $scope.newName = null;
    $scope.getDocuments_ = getDocuments;
    $rootScope.DocumentsInChat = [];

    getDocuments();

    function getDocuments()
    {
        $http({ method: "POST", url: $scope.urlsDocumentsGet })
            .success(function(response) {
                if (response.result) {
                    $scope.documents = response.result;
                }
            })
    }

    $scope.delete = function(docid){
        var url = $scope.urlDeleteFile.replace('0', docid);
        $http({ method: "GET", url: url })
            .success(function(response) {
                if(response.message = "OK")
                    getDocuments();
            })
    };

    $scope.addDocInChat = function()
    {
        var docsinchat = [];
        var textarea = $('#textarea-post');
        for(var i=0;i < $scope.documents.length;i++)
        {
           if($scope.documents[i].checked)
           {
               docsinchat = $scope.documents[i];
               var Tags = '<img style=\"display: inline !important;width: 150px;height: 150px;margin: 10px;border: 4px solid #495b79;border-radius: 5%; \" src=\"'+$scope.asset + $scope.documents[i].url +'\" class=\"img-screenshots\" /> ';
                Tags = $compile(Tags)($scope);
               document.getElementById('div-screenshot').style.display = 'block';
               $('#slide-down-menu-screenshots').fadeIn(1500);
               $('#div-screenshot').append(Tags);
               $(Tags).fadeIn(1500);
               var a = ' <a data-lightbox=\"some\" class=\"doc-show\"  href=\"'+$scope.asset + $scope.documents[i].url  +'\" > '+
                   '<img style=\"display: inline !important;width: 150px;height: 150px;margin: 10px;border: 4px solid #495b79;border-radius: 5%; \" src=\"'+$scope.asset + $scope.documents[i].url +'\" class=\"zoom-images\" /> '+
                   ' </a> ';
               $rootScope.DocumentsInChat.push(a) ;
           }
        }
        $('#add_dialog').modal('hide');
        textarea.focus();
    };

    $scope.rename = function() {
        var url = $scope.urlRenameFile.replace('0', $scope.curr_doc_id);
        $http.post(url, { 'NewName': $scope.newName })
            .success(function(response) {
                if(response.message = "OK") {
                    getDocuments();
                }
            })
    };

    $scope.setCurrentDocId = function (new_id) {
        $scope.curr_doc_id = new_id;
    }

}]);