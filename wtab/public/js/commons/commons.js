function unselect(btn){
    $(btn).removeClass('selected');
    $(btn).removeAttr('disabled', 'disabled');
    $('#caricamento').hide();
}

$( document ).ready(function() {
    'use strict';

    $('body').append('<div id="caricamento"><i class="fa fa-spinner fa-spin" style="font-size:34px"></i></div>');
    $('body').on('click', '.load', function() {
        $(this).addClass('selected');
        $(this).attr('disabled', 'disabled');
        $('#caricamento').show();
    });
});

function _euro(num) {
    num = parseFloat(num);
    if(num !== ''){
        return num.toFixed(2);
    }
}