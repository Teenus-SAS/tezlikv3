window.PricingSpinner = {
    show: function (message = 'Cargando') {
        const spinner = document.getElementById('pricing-spinner');
        if (spinner) {
            // Actualizar el mensaje
            const messageElement = spinner.querySelector('.spinner-message');
            if (messageElement) {
                messageElement.innerHTML = `${message}<span class="dot one">.</span><span class="dot two">.</span><span class="dot three">.</span>`;
            }

            spinner.classList.remove('hidden');
        }
    },
    hide: function () {
        const spinner = document.getElementById('pricing-spinner');
        if (spinner) {
            spinner.classList.add('hidden');
        }
    }
};