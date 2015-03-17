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

        getDocuments_ = getDocuments;

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
               var Tags = '';
               var a= '';
               var regex = new RegExp('[.][A-Za-z0-9]{3,4}', '');
               var extension = docsinchat.name.match(regex);
               extension = extension[extension.length - 1];
               var extensions = {
                   '.doc': 'bundles/zectranet/icons/DOC.png', '.docx': 'bundles/zectranet/icons/DOCX.png', '.xlsx': 'bundles/zectranet/icons/XLSX.png',
                   '.avi': 'bundles/zectranet/icons/AVI.png', '.pdf': 'bundles/zectranet/icons/PDF.png', '.mp3': 'bundles/zectranet/icons/MP3.png',
                   '.zip': 'bundles/zectranet/icons/ZIP.png', '.txt': 'bundles/zectranet/icons/TXT.png', '.xml': 'bundles/zectranet/icons/XML.png',
                   '.xps': 'bundles/zectranet/icons/XPS.png', '.rtf': 'bundles/zectranet/icons/RTF.png', '.odt': 'bundles/zectranet/icons/ODT.png',
                   '.htm': 'bundles/zectranet/icons/HTM.png', '.html': 'bundles/zectranet/icons/HTML.png', '.ods': 'bundles/zectranet/icons/ODS.png'
               };
               if (extension == ".png" || extension == ".gif" || extension == ".jpeg" || extension == ".jpg") {
                   Tags = '<div  style=\"  display: inline-block;position: relative;\"><img  src=\"' + $scope.asset + docsinchat.url + '\" class=\"img-screenshots\" /> <i  class=\" fa fa-close close-img \" ></i>  </div>';
                   Tags = $compile(Tags)($scope);
                   document.getElementById('div-screenshot').style.display = 'block';
                   $('#slide-down-menu-screenshots').fadeIn(1500);
                   $('#div-screenshot').append(Tags);
                   $(Tags).fadeIn(1500);
                    a = ' <div style=\" display: inline-block;width: 120px; \"  ><a data-lightbox=\"some\" class=\"doc-show\"  href=\"' + $scope.asset + $scope.documents[i].url + '\" > ' +
                       '<img style=\"display: inline !important;width: 100px;height: 100px;margin: 10px;border: 4px solid #495b79;border-radius: 5%; \" src=\"' + $scope.asset + $scope.documents[i].url + '\" class=\"zoom-images\" /> ' +
                       ' </a> '+
                       '<br> <a style=\"width: 100px;white-space: normal; \" download=\"'+ $scope.documents[i].name + '\"  href=\"' + $scope.asset + $scope.documents[i].url + '\">'+
                       '<i class=\" fa fa-download \"></i>'
                        + ' '+'<span>'+ $scope.documents[i].name +'</span>'+
                      ' </a></div>';
                   $rootScope.DocumentsInChat.push(a);
               }
               else
               {
                   Tags = '<div  style=\"  display: inline-block;position: relative;\"><img  src=\"' + $scope.asset +  extensions[extension] + '\" class=\"img-screenshots\" /> <i  class=\" fa fa-close close-img \" ></i>  </div>';
                   Tags = $compile(Tags)($scope);
                   document.getElementById('div-screenshot').style.display = 'block';
                   $('#slide-down-menu-screenshots').fadeIn(1500);
                   $('#div-screenshot').append(Tags);
                   $(Tags).fadeIn(1500);
                    a = '<div style=\" display: inline-block;width: 120px; \"  ><img style=\"display: inline !important;width: 100px;height: 100px;margin: 10px;border: 4px solid #495b79;border-radius: 5%; \" src=\"' + $scope.asset +  extensions[extension] + '\"  /> '+
                    ' </a> '+
                    '<br> <a style=\"width: 100px;white-space: normal; \" download=\"'+ $scope.documents[i].name + '\"  href=\"' + $scope.asset + $scope.documents[i].url + '\">'+
                    '<i class=\" fa fa-download \"></i>'
                    + ' '+'<span>'+ $scope.documents[i].name +'</span>'+
                    ' </a></div>';
                   $rootScope.DocumentsInChat.push(a);
               }
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