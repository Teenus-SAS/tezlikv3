$(document).ready(function() {
    $('#btnForgotPass').on('click', function(e) {
        let email = $('#email').val()

        $.ajax({
            type: 'POST',
            url: '../../../api/forgotPassword',
            data: { data: email },
            success: function(data, textStatus, xhr) {
                if (data.success == true) {
                    toastr.success(data.message)
                    setTimeout(() => {
                        location.href = '../../../'
                    }, 2000);

                } else if (data.error == true)
                    toastr.error(data.message)
            },
        })
    })
})