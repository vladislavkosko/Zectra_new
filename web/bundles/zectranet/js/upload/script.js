$(function(){
    var allowable_extension = new Array(
        "doc", "docx", "xls", "xlsx",
        "jpeg", "gif", "png", "avi",
        "pdf", "mp3", "zip", "txt",
        "xml", "xps", "rtf", "odt",
        "htm", "html", "ods", "jpg");
    var a;

    var ul = $('#upload ul');

    function in_array(value, array)
    {
        for(var i = 0; i < array.length; i++)
        {
            if(array[i] == value) return true;
        }
        return false;
    }

    $('#drop_upload a').click(function(){
        // Simulate a click on the file input button
        // to show the file browser dialog
        $(this).parent().find('input').click();
    });

    // Initialize the jQuery File Upload plugin
    $('#upload').fileupload({

        // This element will accept file drag/drop uploading
        dropZone: $('#drop_upload'),

        add: function (e, data) {
            if(data.files[0].name != undefined) {
                a = data.files[0].name.split('.');
                var ext = a[a.length - 1];

                if (in_array(ext, allowable_extension) == true) {
                    var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"' +
                    ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');

                    tpl.find('p').text(data.files[0].name)
                        .append('<i>' + formatFileSize(data.files[0].size) + '</i>');

                    data.context = tpl.appendTo(ul);

                    tpl.find('input').knob();

                    tpl.find('span').click(function () {

                        if (tpl.hasClass('working')) {
                            jqXHR.abort();
                        }

                        tpl.fadeOut(function () {
                            tpl.remove();
                        });

                    });

                    // Automatically upload the file once it is added to the queue
                    var jqXHR = data.submit();
                }
                else {

                    var tpl = $('<li class="extention"><p> ' + data.files[0].name + '.' + ' <br> ' + ' Error: Extension .' + ext + ' is not supported  </p><span></span></li>');

                    data.context = tpl.appendTo(ul);
                    tpl.find('span').click(function () {

                        if (tpl.hasClass('extention')) {
                            jqXHR.abort();
                        }

                        tpl.fadeOut(function () {
                            tpl.remove();
                        });

                    });

                    var jqXHR = data.submit();
                }
            }

        }
            ,

            progress: function (e, data) {
                var a = data.files[0].name.split('.');
                var ext = a[a.length - 1];

                if (in_array(ext,allowable_extension) == true)
                {
                    // Calculate the completion percentage of the upload
                    var progress = parseInt(data.loaded / data.total * 100, 10);

                    // Update the hidden input field and trigger a change
                    // so that the jQuery knob plugin knows to update the dial
                    data.context.find('input').val(progress).change();

                    if (progress == 100) {
                        data.context.removeClass('working');

                    }
                }
            }

            ,

            fail: function (e, data) {
                var a = data.files[0].name.split('.');
                var ext = a[a.length - 1];

                if (in_array(ext,allowable_extension) == true){
                    // Something has gone wrong!
                    data.context.addClass('error');
                }
            }

    });


    // Prevent the default action when a file is dropped on the window
    $(document).on('drop dragover', function (e) {

            e.preventDefault();

    });

    // Helper function that formats the file sizes
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }

});