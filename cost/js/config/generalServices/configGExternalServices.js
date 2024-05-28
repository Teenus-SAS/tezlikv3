$(document).ready(function () { 
    loadAllDataGServices = async (op) => {
        try {
            const services = await searchData('/api/generalExternalservices');

            sessionStorage.setItem('dataGServices', JSON.stringify(services)); 
            let $select = $(`#generalServices`);
            $select.empty();
            $select.append(`<option disabled selected>Seleccionar</option>`);
            $.each(services, function (i, value) {
                $select.append(
                    `<option value ="${value.id_general_service}"> ${value.name_service} </option>`
                );
            });

            // setSelectsServices(services);
            if(op == 1)
                loadTableGExternalServices(services);  
        } catch (error) {
            console.error('Error loading data:', error);
        }
    };     
    // setSelectsServices = (data) => {
    //     let $select = $(`#generalServices`);
    //     $select.empty();

    //     $select.append(`<option disabled selected>Seleccionar</option>`);
    //     $.each(data, function (i, value) {
    //         $select.append(
    //             `<option value = ${value.id_service}> ${value.name_service} </option>`
    //         );
    //     });
    // };
});