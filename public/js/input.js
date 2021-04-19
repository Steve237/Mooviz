var yourPreferredLength = 25;

$(function() {
    $('#uploadspan').on('click', function() {
        $('#uploadspan').hide();
        $('#inputfield').show();
    });
    
    $('#inputfield').on('blur change', function() {
        $('#uploadspan').show().text( $('#inputfield').val().substring( 0, yourPreferredLength)+'...' );
        $('#inputfield').hide();
    });
});