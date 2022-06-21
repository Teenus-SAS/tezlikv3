$(document).ready(function () {
  $('.btnLocation').click(function (e) {
    e.preventDefault();
    id = this.id;

    data = new Object();

    if (id == 1) {
      data.cost = 1;
      data.planning = 0;
    } else {
      data.cost = 0;
      data.planning = 1;
    }

    $.ajax({
      type: 'POST',
      url: '/api/updateCompanyLicense',
      data: data,
      success: function (r) {
        if (r.error) {
          toastr.error(r.message);
          return false;
        } else if (r.success) location.href = r.location;
      },
    });
  });
});
