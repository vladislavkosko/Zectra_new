Zectranet.controller('DocumentsController', ['$scope', '$http', '$rootScope', '$modal','$sce','$compile', '$documents',
    function($scope, $http, $rootScope, $modal, $sce, $compile, $documents){
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
        $scope.DocsAttachmens = [];
        $scope.InsertScreenshotsInPHP = JSON_URLS.InsertScreenshotsInPHP;
        $scope.urlDeleteScreenshots = JSON_URLS.deleteScrenshots;

        getDocuments_ = getDocuments;

        getDocuments();

        function getDocuments() {
            $documents.getDocuments().then(function (response) {
                    $scope.documents = response.result;
                }
            );
        }

        $scope.delete = function(docid){
            var url = $scope.urlDeleteFile.replace('0', docid);
            $http({ method: "GET", url: url })
                .success(function(response) {
                    if(response.message = "OK")
                        getDocuments();
                }
            );
        };

        $scope.addDocumentsToPost = function () {
            for(var i = 0; i < $scope.DocsAttachmens.length; i++)
            {
                var a = ' <div style=\" display: inline-block;width: 120px; \"  ><a data-lightbox=\"some\" class=\"doc-show\"  href=\"' + $scope.asset +  $scope.DocsAttachmens[i].url + '\" > ' +
                    '<img style=\"display: inline !important;width: 100px;height: 100px;margin: 10px;border: 4px solid #495b79;border-radius: 5%; \" src=\"' + $scope.asset +  $scope.DocsAttachmens[i].Img + '\" class=\"zoom-images\" /> ' +
                    ' </a> '+
                    '<br> <a style=\"width: 100px;white-space: normal; \" download=\"'+ $scope.DocsAttachmens[i].name + '\"  href=\"' + $scope.asset +  $scope.DocsAttachmens[i].Img+ '\">'+
                    '<i class=\" fa fa-download \"></i>'
                    + ' ' +'<span>'+  $scope.DocsAttachmens[i].name +'</span> '+
                    ' </a>'
                    + '</div>';
                $rootScope.DocumentsInChat.push(a);
            }
            $scope.DocsAttachmens = [];
        };

        $scope.addDocInChat = function()
        {
            var docsinchat = [];
            var textarea = $('#textarea-post');
            for(var i = 0; i < $scope.documents.length; i++)
            {
                if($scope.documents[i].checked && $.inArray($scope.documents[i],$scope.DocsAttachmens) == -1 )
                {
                    docsinchat = $scope.documents[i];
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
                        docsinchat.Img = docsinchat.url;
                        $scope.DocsAttachmens.push(docsinchat);
                    }
                    else
                    {
                        docsinchat.Img = extensions[extension];
                        $scope.DocsAttachmens.push(docsinchat);
                    }
                }
            }
            $('#add_dialog').modal('hide');
            textarea.focus();
            $('#slide-down-menu-screenshots').fadeIn(1500);
            $('#div-screenshot').fadeIn(1500);
        };

        $scope.deleteDocWhenAdding = function(index){
            if($scope.DocsAttachmens[index].IsScreenshot == 1)
            {
                var url =  $scope.urlDeleteScreenshots.replace('0', $scope.DocsAttachmens[index].id);
                $scope.documentPromise = $http({ method: "GET", url: url })
            }
            $scope.DocsAttachmens.splice(index,1);
        };


        //InsertScreenshots ctrl + V
        {
            var atachments = [];
            window.onload = function () {
                document.getElementById('textarea-post').addEventListener('paste', function (event) {

                    var cbd = event.clipboardData;
                    if (cbd.items && cbd.items.length) {
                        var cbi = cbd.items[0];
                        if (/^image\/(png|gif|jpe?g)$/.test(cbi.type)) {
                            event.stopPropagation();
                            event.preventDefault();
                            var f = cbi.getAsFile();
                            var fr = new FileReader();
                            fr.onload = function () {
                                var im = new Image();
                                im.src = this.result;
                                atachments.push($(im).attr('src'));
                                if (atachments.length > 0) {
                                    $scope.documentPromise = $http({
                                        method: "POST",
                                        url: $scope.InsertScreenshotsInPHP,
                                        data: atachments
                                    })
                                        .success(function (response) {
                                            if (response.result) {
                                                atachments =[];
                                                response.result.Img = response.result.url;
                                                response.result.IsScreenshot = 1;
                                                $scope.DocsAttachmens.push(response.result);
                                                $('#slide-down-menu-screenshots').fadeIn(1500);
                                                $('#div-screenshot').fadeIn(1500);
                                            }
                                        });
                                }
                            };
                            fr.readAsDataURL(f);
                        }

                    }

                }, false);
            };


        }
        //End InsertScreenshots ctrl + V

        $scope.deleteScreenshot = function(screenName) {
            var url =  $scope.urlDeleteScreenshots.replace('0', $scope.screenshots[screenName].id);
            $scope.documentPromise = $http({ method: "GET", url: url })
                .success(function(response) {
                    if(response.message = "OK")
                        $('#screenshot'+screenName).remove();
                    var index = $.inArray(' <div style=\" display: inline-block;width: 120px; \"  ><a data-lightbox=\"some\" class=\"doc-show\"  href=\"' + $scope.urlAsset +  $scope.screenshots[screenName].url + '\" > ' +
                    '<img style=\"display: inline !important;width: 100px;height: 100px;margin: 10px;border: 4px solid #495b79;border-radius: 5%; \" src=\"' + $scope.urlAsset +  $scope.screenshots[screenName].url + '\" class=\"zoom-images\" /> ' +
                    ' </a> '+
                    '<br> <a style=\"width: 100px;white-space: normal; \" download=\"'+ $scope.screenshots[screenName].name + '\"  href=\"' + $scope.urlAsset + $scope.screenshots[screenName].url+ '\">'+
                    '<i class=\" fa fa-download \"></i>'
                    + ' ' +'<span>'+  $scope.screenshots[screenName].name +'</span> '+
                    ' </a></div>',$rootScope.DocumentsInChat);
                    $rootScope.DocumentsInChat.splice(index,1);

                })
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

    }
]);