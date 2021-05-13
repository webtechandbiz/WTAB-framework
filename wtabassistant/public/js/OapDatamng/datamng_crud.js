$( document ).ready(function() {
    'use strict';


    $('body').on('click', "#createCRUDcode", function(e) {
        $.post( APPLICATION_URL + "datamng/upload/getGeneratedCodeByTable", { tablename: $('#tablename').val() })
        .done(function(data) {
            console.log(data);
            $('#tablecontent').html(data.tabledata);

            $('#tablecontent_generated__application_config').html(data.application_config);
            //# View
            $('#tablecontent_generated__foreign_tables').html(data.foreign_tables);
            $('#tablecontent_generated__menu').html(data.menu);
            $('#tablecontent_generated__moduleconfigview').html(data.module_config_get_data);
            $('#tablecontent_generated__query').html(data.selectjoin);
            $('#tablecontent_generated__html').html(data.html_getdata);
            $('#tablecontent_generated__phpview').html(data.php_getdata);
            $('#tablecontent_generated__html_HTMLviewfield').html(data.html_HTMLviewfield);
            $('#tablecontent_generated__html_JSviewfield').html(data.html_JSviewfield);
            
            $('#tablecontent_generated__jsview').html(data.js_getdata);

            //# Edit
            $('#tablecontent_generated__jsedit').html(data.jsedit);
            $('#tablecontent_generated__moduleconfigedit').html(data.module_config_set_data);
            $('#tablecontent_generated__htmledit').html(data.htmledit);
            $('#tablecontent_generated__phpedit').html(data.php_edit);
        })
        .fail(function(data) {
            console.log( "error" );
            console.log(data.responseText);
        });

        return false;
    });

});
