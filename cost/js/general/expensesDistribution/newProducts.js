$(document).ready(function () {
    $('.cardNewProduct').hide();
    
    $('#btnNewProducts').click(function (e) {
        e.preventDefault();
        
        $('.cardImportExpenses').hide(800);
        $('.cardExpensesDistribution').hide(800);
        $('.cardNewProduct').toggle(800);
        $('#formNewProduct').trigger('reset');
    });

    const syncSelectOptions = (sourceSelector, targetSelector) => {
        $(sourceSelector).change(function (e) {
            e.preventDefault();
            let id = this.value;

            $(targetSelector + ' option').prop('selected', function () {
                return $(this).val() == id;
            });

            if (sourceSelector.includes('Old')) {
                let data = JSON.parse(sessionStorage.getItem('dataProducts'));
                let product = data.find(item => item.id_product == id);
                if (product) {
                    $('#pAssignableExpense').val((parseFloat(product.assignable_expense)).toFixed(2));
                }
            }
        });
    };

    syncSelectOptions('#newRefProduct', '#newNameProduct');
    syncSelectOptions('#newNameProduct', '#newRefProduct');
    syncSelectOptions('#refOldProduct', '#nameOldProduct');
    syncSelectOptions('#nameOldProduct', '#refOldProduct');

    // $('#newRefProduct').change(function (e) {
    //     e.preventDefault(); 
    //     let id = this.value;
        
    //     $('#newNameProduct option').prop('selected', function () {
    //         return $(this).val() == id;
    //     }); 
    // });

    // $('#newNameProduct').change(function (e) {
    //     e.preventDefault(); 
    //     let id = this.value;
        
    //     $('#newRefProduct option').prop('selected', function () {
    //         return $(this).val() == id;
    //     }); 
    // });

    // $('#refOldProduct').change(function (e) {
    //     e.preventDefault();
    //     let id = this.value;

    //     $('#nameOldProduct option').prop('selected', function () {
    //         return $(this).val() == id;
    //     }); 

    //     let data = JSON.parse(sessionStorage.getItem('dataProducts'));

    //     data = data.find(item => item.id_product == id);

    //     $('#pAssignableExpense').val((parseFloat(data.assignable_expense)).toFixed(2));
    // });

    // $('#nameOldProduct').change(function (e) {
    //     e.preventDefault();
    //     let id = this.value;

    //     $('#refOldProduct option').prop('selected', function () {
    //         return $(this).val() == id;
    //     }); 
    //     let data = JSON.parse(sessionStorage.getItem('dataProducts'));

    //     data = data.find(item => item.id_product == id);

    //     $('#pAssignableExpense').val((parseFloat(data.assignable_expense)).toFixed(2));
    // });

    $('#btnAddNewProduct').click(function (e) {
        e.preventDefault();

        let newProduct = $('#newNameProduct').val();
        let oldProduct = $('#nameOldProduct').val();

        let data = newProduct * oldProduct;

        if (!data || data <= 0) {
            toastr.error('Ingrese todos los campos');
            return false;
        }
        
        let dataExpense = $('#formNewProduct').serialize();

        $.post('/api/saveNewProduct', dataExpense,
            function (data, textStatus, jqXHR) {
                messageDistribution(data, 1);
            },
        );
    });
});