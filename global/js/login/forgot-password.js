$(document).ready(function() {
    $('#btnForgotPass').on('click', function(e) {
        email = $('#email').val();

        if (!email || email == '') {
            toastr.error('Ingrese email');
            return false;
        }

        $.ajax({
            type: 'POST',
            url: '/api/forgotPassword',
            data: { data: email },
            success: function(data, textStatus, xhr) {
                if (data.success == true) {
                    toastr.success(data.message);
                    setTimeout(() => {
                        location.href = '../../../';
                    }, 10000);
                } else if (data.error == true) toastr.error(data.message);
            },
        });
    });
});