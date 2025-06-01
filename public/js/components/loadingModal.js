$(document).ready(function () {
    // Modal de Carga (versión mejorada)
    window.showLoadingModal = (title, message = "", options = {}) => {
        const config = {
            showProgress: false,
            spinnerColor: '#4361ee',
            ...options
        };

        const centeredTemplate = `
            <div class="centered-loading-container">
            <div class="spinner-container">
                <div class="spinner" style="border-color: ${config.spinnerColor} transparent transparent transparent;"></div>
            </div>
            <div class="text-content">
                <h5 class="loading-title">${title}</h5>
                <p class="loading-message">${message}</p>
            </div>
            ${config.showProgress ? `
            <div class="progress-container">
                <div class="progress-bar">
                <div class="progress-fill" style="width: 0%; background: ${config.spinnerColor};"></div>
                </div>
            </div>
            ` : ''}
            </div>
        `;

        const modal = bootbox.dialog({
            message: centeredTemplate,
            closeButton: false,
            onEscape: false,
            className: 'centered-loading-modal',
            buttons: {},
            backdrop: 'static',
            size: 'small'
        });

        // Asegurar que el modal se muestre completo
        modal.on('shown.bs.modal', function () {
            const $modalDialog = modal.find('.modal-dialog');
            $modalDialog.css({
                'margin': '0 auto',
                'top': '50%',
                'left': '50%',
                'transform': 'translate(-50%, -50%)',
                'position': 'fixed'
            });
        });

        // API básica
        modal.api = {
            updateProgress: (value) => {
                if (config.showProgress)
                    modal.find('.progress-fill').css('width', `${value}%`);

                return modal.api;
            },
            updateMessage: (newMessage) => {
                modal.find('.loading-message').text(newMessage);
                return modal.api;
            },
            close: () => modal.modal('hide')
        };

        return modal;
    };
});