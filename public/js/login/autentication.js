$(document).ready(function () {
  $('.auth-user-testimonial .owl-carousel').owlCarousel({
    loop: true,
    margin: 0,
    nav: false,
    dots: false,
    autoplay: true,
    autoplayTimeout: 4000,
    responsive: {
      0: { items: 1, },
      600: { items: 1, },
      1000: { items: 1, },
    },
  });

  //Initialize form
  $('#loginForm').validate({
    focusInvalid: false,
    rules: {
      'validation-email': { required: true, email: true, },
      'validation-password': { required: true, },
    },
    errorPlacement: function errorPlacement(error, element) {
      $(element).siblings('.validation-error').removeClass('d-none');
      return true;
    },
    highlight: function (element) {
      var $el = $(element);
      var $parent = $el.parents('.form-group');
      $parent.addClass('invalid-field');
    },
    unhighlight: function (element) {
      var $el = $(element);
      var $parent = $el.parents('.form-group');
      $parent.removeClass('invalid-field');
      $(element).siblings('.validation-error').addClass('d-none');
    },
    submitHandler: function (form) {
      var formdata = $(form).serializeArray();
      var data = {};
      $(formdata).each(function (index, obj) {
        data[obj.name] = obj.value;
      });

      login(data);
      $(form).trigger('reset');
      $('.floating-label').removeClass('enable-floating-label');
    },
  });

  const login = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/login/userAutentication',
      data: data,
      success: function (response, textStatus, xhr) {
        if (response.error) {
          toastr.error(response.message);
          return false;
        }

        if (response.success) {
          localStorage.setItem('companyConfigHistory', response.companyConfigHistory);
          localStorage.setItem('authToken', response.token);
          localStorage.setItem('updates_notice', response.updates_notice);
          window.location.href = response.location;
        }
      },
      error: function (xhr, textStatus, errorThrown) {
        const errorMessage = xhr.responseJSON?.message || 'Error during authentication';
        toastr.error(errorMessage);
      }
    });
  };

});
