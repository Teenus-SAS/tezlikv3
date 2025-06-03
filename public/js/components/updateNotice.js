$(document).ready(function () {
    const modalElement = document.getElementById('updatesNotices');
    const updateFlagKey = 'updates_notice';

    // Verifica si ya se aceptó
    const updatesNotice = parseInt(localStorage.getItem(updateFlagKey), 10);

    // Crea y guarda la instancia del modal
    const updateModal = new bootstrap.Modal(modalElement, {
        backdrop: 'static',
        keyboard: false
    });

    if (!updatesNotice) {
        updateModal.show();
    }

    // Evento aceptar actualización
    $(document).on('click', '.btnAcceptUpdatePlatform', function (e) {
        e.preventDefault();
        updateModal.hide(); // Usamos la instancia ya creada

        $.ajax({
            url: "/api/updatesNotice",
            success: function (response) {
                localStorage.setItem(updateFlagKey, '1');
                console.log(response.message);
            },
            error: function () {
                console.error("Error al guardar la aceptación.");
            }
        });
    });
});

