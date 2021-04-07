$( document ).ready(function() {
    'use strict';


    $('body').on('click', "#createCRUDcode", function(e) {
        $.post( APPLICATION_URL + "datamng/upload/getGeneratedCodeByTable", { tablename: $('#tablename').val() })
        .done(function(data) {
            console.log(data);
            $('#tablecontent').html(data.tabledata);
            $('#tablecontent_generated__html').html(data.tabledata_generated);
            
        })
        .fail(function(data) {
            console.log( "error" );
            console.log(data.responseText);
        });

        return false;
    });

});
