$( document ).ready(function() {
    'use strict';

    $('body').on('click', "#uploadimportfile", function(e) {
        var file_data = $('#sortpicture').prop('files')[0];   
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        $.ajax({
            url: APPLICATION_URL + 'datamng/upload',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post'
        })
        .done(function(data) {
            console.log(data);
        })
        .fail(function(data) {
            console.log("error");
            console.log(data);
        });
    });
});