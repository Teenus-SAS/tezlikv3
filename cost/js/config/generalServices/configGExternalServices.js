$(document).ready(function () { 
    loadAllDataServices = async () => {
        try {
            const services = await searchData('/api/generalExternalservices');

            sessionStorage.setItem('dataGServices', JSON.stringify(services));
            // dataServices = services;

            setSelectsServices(services);
            loadTableExternalServices(services);
        } catch (error) {
            console.error('Error loading data:', error);
        }
    };

    loadAllDataServices();
    
    setSelectsServices = (data) => {
        let $select = $(`#generalServices`);
        $select.empty();

        $select.append(`<option disabled selected>Seleccionar</option>`);
        $.each(data, function (i, value) {
            $select.append(
                `<option value = ${value.id_service}> ${value.name_service} </option>`
            );
        });
    };
});