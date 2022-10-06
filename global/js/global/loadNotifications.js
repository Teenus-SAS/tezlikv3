$(document).ready(function () {
  loadNotification = () => {
    $.ajax({
      url: '/api/recentNotification',
      success: function (resp) {
        getNotifications(resp);
      },
    });
  };

  loadNotification();

  /* Cargar notificaciones */
  getNotifications = (data) => {
    if (data.length == 0) {
      $('#clear').css('display', 'none');
      $('#showAll').css('display', 'none');
    }
    $('#notify-scrollbar').empty();

    let n = 0;

    data.length > 5 ? (count = 5) : (count = data.length);

    for (i = 0; i < count; i++) {
      if (data[i].check_notification == 1) {
        n = n + 1;
        font = 'bold';
      } else font = 'normal';

      $('#notify-scrollbar').append(`
          <a href="javascript:void(0);" class="dropdown-item notification-item" style="font-weight: ${font}">
            <div class="media">
              <div class="avatar avatar-xs">
                <i class="bx bx-user-plus"></i>
              </div>
              <p class="media-body">
                ${data[i].description}
                <small class="text-muted">${data[i].date_notification}</small>
              </p>
            </div>
          </a>
      `);
    }
    $('#count').html(n);
  };

  /* Limpiar notificaciones */
  $('#clear').click(function (e) {
    e.preventDefault();
    $.get('/api/updateCheckNotification', function (data, textStatus, jqXHR) {
      msgNotification(data);
    });
  });

  msgNotification = (data) => {
    if (data.success == true) {
      loadNotification();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
