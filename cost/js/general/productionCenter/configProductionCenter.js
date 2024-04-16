$(document).ready(function () {
    loadAllDataPCenter = async () => {
        try {
            const dataPCenter = await searchData('/api/productionCenter')
            let $select = $(`.selectProductionCenter`);
            $select.empty();

            $select.append(`<option disabled selected>Seleccionar</option>`);
            // $select.append(`<option value='0'>Todos</option>`);
            $.each(dataPCenter, function (i, value) {
                $select.append(
                    `<option value = ${value.id_production_center}> ${value.production_center} </option>`
                );
            });

            loadTblPCenter(dataPCenter);
        } catch (error) {
            console.error('Error loading data:', error);
        }
    } 

    loadAllDataPCenter();

    $('.selectProductionCenter').change(function (e) { 
        e.preventDefault();
        let id = this.id;
        let id_production_center = this.value;

        if (id === 'selectProductionCenterExpenses') {
            let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));

            if (id_production_center == '0') {
                data = dataExpenses;
            } else 
                data = dataExpenses.filter(item => item.id_production_center == id_production_center);
            
            loadTblAssExpenses(data);
        } else {
            let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpensesDistribution'));

            if (id_production_center == '0') {
                data = dataExpenses;
            } else 
                data = dataExpenses.filter(item => item.id_production_center == id_production_center);
            
            loadTableExpensesDistribution(data);
        }
    });
});