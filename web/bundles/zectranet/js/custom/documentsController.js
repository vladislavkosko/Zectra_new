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
               var Tags = ' <a data-lightbox=\"some\" class=\"doc-show\"  href=\"'+ $scope.asset + $scope.documents[i].url +'\" > '+
                           ' <img src=\"'+$scope.asset + $scope.documents[i].url +'\" class=\"zoom-images\" /> '+
                           ' </a> ';

               $rootScope.message += Tags;
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