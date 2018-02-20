$(document).ready(function(){

    $('form#login-form').submit(function(){
             

        var path = '/ajax/auth.php';
        var formData = $(this).serialize();
        var success = function( response ){
            if (response == 'Y')
            {
                window.location.href = window.location.href;
            }
            else
            {
                $('#auth-error').html( response ).show();
            }           
        };
 
        var responseType = 'html';
 
        $.post( path, formData, success, responseType );
 
        return false;
    });
});