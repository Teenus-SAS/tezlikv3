$(document).ready(function () {
    loadAllDataPCenter = async () => {
        try { 
            let $select = $(`.selectProductionCenter`);
            $select.empty(); 
            let $firstTwoSelect = $('.first-two-selectors select'); // Selecciona los primeros dos selectores independientemente de su orden
            let $otherSelect = $('.selectProductionCenter').not('.first-two-selectors select'); // Selecciona los otros selectores

            const dataPCenter = await searchData('/api/productionCenter');

            // Vacía los selectores
            $firstTwoSelect.empty();
            $otherSelect.empty();

            // Agrega las opciones a los primeros dos selectores
            $select.append(`<option disabled selected>Seleccionar</option>`);
            $firstTwoSelect.append(`<option value='0'>Todos</option>`);
            $.each(dataPCenter, function (i, value) {
                $firstTwoSelect.append(
                    `<option value=${value.id_production_center}>${value.production_center}</option>`
                );
            });

            // Agrega las opciones a los selectores restantes sin la opción 'Todos'
            $.each(dataPCenter, function (i, value) {
                $otherSelect.append(
                    `<option value=${value.id_production_center}>${value.production_center}</option>`
                );
            });

            loadTblPCenter(dataPCenter);
        } catch (error) {
            console.error('Error loading data:', error);
        }
    };

    loadAllDataPCenter();

    $('.selectProductionCenter').change(function (e) { 
        e.preventDefault();
        let id = this.id;
        let id_production_center = this.value;

        let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));

        if (id_production_center == '0') {
            let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));
            var summarizedExpenses = sumAndGroupExpenses(dataExpenses);
            summarizedExpenses.sort((a, b) => a.puc.localeCompare(b.puc));
            data = summarizedExpenses;
        } else 
            data = dataExpenses.filter(item => item.id_production_center == id_production_center);
        
        if (id === 'selectProductionCenterExpenses1') { 
            loadTblAssExpenses(data, 2);
        } else {
            let totalExpense = 0;

            data.forEach(item =>{
                totalExpense += parseFloat(item.expense_value)
            });

            $('#expensesToDistribution').val(`$ ${totalExpense.toLocaleString('es-CO', { maximumFractionDigits: 2 })}`);

            let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpensesDistribution'));

            if (id_production_center == '0') {
                data = dataExpenses;
            } else
                data = dataExpenses.filter(item => item.id_production_center == id_production_center);
            
            loadTableExpensesDistribution(data);
        }
    });
});