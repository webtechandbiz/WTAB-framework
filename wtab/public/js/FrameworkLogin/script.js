$( document ).ready(function() {
    'use strict';


    $('#username').focus();

    $('body').on('click', '#login', function() {
        var position = 'login';
        $.post( APPLICATION_URL + "/login/login/checklogin", { username: $('#username').val(), password: $('#password').val() })
        .done(function(data) {
            console.log('data');
            console.log(data);
            if(data === 'empty'){
                $('#caricamento').hide();
                alert(empty)
            }            
            if(data === 'login-error'){
                $('#caricamento').hide();
                alert(email_or_password_error);
            }
            if(data === 'expired-token'){
                location.reload();
            }
            if(data === $('#username').val()){
                location.href = APPLICATION_HOME;
            }
        })
        .fail(function(data) {
            console.log( "error" );
            console.log(data.responseText);
            sendError(position, '', 'script.js', 'login-fail', '0', data);
        });
    });

    $("form input").keypress(function (e) {
        if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
            $('#login').click();
            return false;
        } else {
            return true;
        }
    });
    
});