$( document ).ready(function() {
    'use strict';

    var _currentFile = '';

    $('body').on('click', ".openfile", function(e) {
        console.log($(this).html());
        _currentFile = $(this).html();
        $.post( APPLICATION_URL + "datamng/upload/getUploadedFile", { filename: $(this).html() })
        .done(function(data) {
            $('#_view_filecontent').html(data.content);
            $('#view_filecontent').modal('show');
        })
        .fail(function(data) {
            console.log( "error" );
            console.log(data.responseText);
        });

        return false;
    });

    $('body').on('click', "#confirm_upload", function(e) {
        var selectedkey = [];
        var clmposition = [];
        var clmlength = [];
        var clmtype = [];
        var selectedclm = [];

        var view_filecontent = $('#_view_filecontent table tr th span');

        $( view_filecontent ).each(function( index ) {
            if($(this).hasClass('selectedkey')){
                console.log( $(this).data('clm') );
                selectedkey.push({col: $(this).data('clm'), pos: $(this).data('clmposition')});
            }
            if($(this).hasClass('selectedclm')){
                selectedclm.push($(this).data('clm'));
                clmlength.push($(this).find('input').val());
                clmposition.push($(this).data('clmposition'));                
                clmtype.push($(this).find('.clmtype').val());
            }
        });

        var _addautoincrement = $('#addautoincrement:checked').length > 0;
        if(_addautoincrement){
            _addautoincrement = 1;
        }else{
            _addautoincrement = 0;
        }
        $.post( APPLICATION_URL + "datamng/upload/confirmupload", {
            addautoincrement: _addautoincrement,
            currentFile: _currentFile,
            selectedkey: selectedkey,
            selectedclm: selectedclm,
            clmposition: clmposition,
            clmlength: clmlength,
            clmtype: clmtype
        })
        .done(function(data) {
            console.log('datadata');
            console.log(data);
        })
        .fail(function(data) {
            console.log( "error" );
            console.log(data.responseText);
        });
        return false;
    });
    
    $('body').on('click', ".slc", function(e) {
        $(this).addClass('selectedclm');
        return false;
    });
    
    $('body').on('click', ".key", function(e) {
        $(this).addClass('selectedkey');
        return false;
    });

});
