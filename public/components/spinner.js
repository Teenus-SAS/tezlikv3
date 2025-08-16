window.MedicalSpinner = {
    show: function () {
        const spinner = document.getElementById('medical-spinner');
        if (spinner) {
            spinner.classList.remove('hidden');
        }
    },
    hide: function () {
        const spinner = document.getElementById('medical-spinner');
        if (spinner) {
            spinner.classList.add('hidden');
        }
    }
};
