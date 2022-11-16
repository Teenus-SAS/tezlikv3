$(document).ready(function () {
  loadNotification = () => {
    $.ajax({
      url: '/api/recentNotification',
      success: function (resp) {
        if (resp.length == 0) {
          $('#clear').css('display', 'none');
          $('#showAll').css('display', 'none');
        }
        fetchNotification(resp);
      },
    });
  };

  loadNotification();

  fetchNotification = async (data) => {
    $('.notify-scrollbar').empty();

    let notify_scrollbar = document.getElementsByClassName('notify-scrollbar');

    notify_scrollbar[0].insertAdjacentHTML(
      'beforeend',
      `<div class="notify-title p-3">
          <h5 class="font-size-14 font-weight-600 mb-0">
              <span>Notificationes</span>
              <a class="text-primary" href="javascript: void(0);" onclick="clearNotification()">
                  <small>Limpiar Todo</small>
              </a>
          </h5>
      </div>`
    );
    await getNotifications(data);

    notify_scrollbar[0].insertAdjacentHTML(
      'beforeend',
      `<div class="notify-all">
        <a href="javascript: void(0);" class="text-primary text-center p-3" id="showAll">
            <small>Mostrar todo</small>
        </a>
      </div>`
    );
  };

  /* Cargar notificaciones */
  getNotifications = (data) => {
    let n = 0;

    data.length > 5 ? (count = 5) : (count = data.length);

    for (i = 0; i < count; i++) {
      if (data[i].check_notification == 1) {
        n = n + 1;
        font = 'bold';
      } else font = 'normal';

      if (!data[i].logo) img = '';
      else img = data[i].logo;

      // Calcular tiempo transcurrido
      let fecha = new Date(data[i].date_notification);
      let lateDay = new Date(fecha.getFullYear(), fecha.getMonth() + 1, 0);
      let hoy = new Date();

      let fecha1 = moment(fecha);
      let fecha2 = moment(hoy);

      let dias = fecha2.diff(fecha1, 'd');
      let horas = fecha2.diff(fecha1, 'h');
      let minutos = fecha2.diff(fecha1, 'm');
      let segundos = fecha2.diff(fecha1, 's');

      if (segundos <= 60) time = `${segundos} seconds`;
      else if (minutos <= 60) time = `${minutos} mins`;
      else if (horas <= 24) time = `${horas} hours`;
      else if (dias <= lateDay.getDate()) time = `${dias} days`;

      notify_scrollbar = document.getElementsByClassName('notify-scrollbar');

      notify_scrollbar[0].insertAdjacentHTML(
        'beforeend',
        `
      <a href="javascript:void(0);" class="dropdown-item notification-item" style="font-weight: ${font}">
        <div class="media">
          <div class="avatar-xs">
            <img class="img-fluid rounded-circle" src="${img}"> 
          </div>
          <p class="media-body">
            ${data[i].description}
            <small class="text-muted">${time} ago </small>
          </p>
        </div>
      </a>
     `
      );
    }
    $('#count').html(n);
  };

  /* Limpiar notificaciones */
  clearNotification = () => {
    $.get('/api/updateCheckNotification', function (data, textStatus, jqXHR) {
      msgNotification(data);
    });
  };

  msgNotification = (data) => {
    if (data.success == true) {
      loadNotification();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
