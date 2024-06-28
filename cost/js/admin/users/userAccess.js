$(document).ready(function () {
    loadUserAccess = async () => {
        let data = await searchData('/api/costUserAccess');

        let access = {
            aProducts: data.create_product,
            aMaterials: data.create_materials,
            aMachines: data.create_machines,
            aProcess: data.create_process,
            aProductsMaterials: data.product_materials, 
            aFactoryLoad: data.factory_load,
            aGServices: data.external_service,
            aPayroll: data.payroll_load, 
            aCustomPrices: data.custom_price,
            aBackup: data.backup,
            aGeneralCostReport: data.general_cost_report,
            aUsers: data.user,
            aPaymentMethods: data.quote_payment_method,
            aCompanies: data.quote_company,
            aContacts: data.quote_contact,
            aPricesCOP: data.price, 
            aAnalysisMaterials: data.analysis_material,
            aEconomyScale: data.economy_scale,
            aSaleObjectives: data.sale_objectives,
            aPriceObjectives: data.price_objectives,
            aMultiproducts: data.multiproduct,
            aSimulator: data.simulator,
            aHistorical: data.historical,
            aQuotes: data.quote,
            aSupport: data.support,
        }

        $.each(access, (index, value) => {
            if (value == 0) {
                $(`.${index}`).hide();
            } else
                $(`.${index}`).show();
        });

        if (data.expense == 0 && data.expense_distribution == 0 &&
            data.cost_multiproduct == 0 && data.production_center == 0) {
            $('.aExpenses').hide();
        } else {
            $('.aExpenses').show();
        }

        if (
            access.aProducts == 0 &&
            access.aMaterials == 0 &&
            access.aMachines == 0 &&
            access.aProcess == 0
        ) {
            $('#navCostBasics').hide();
        } else
            $('#navCostBasics').show();

        if (
            access.aProductsMaterials == 0 && 
            access.aFactoryLoad == 0 &&
            access.aGServices == 0
        ) {
            $('#navCostSetting').hide();
        } else
            $('#navCostSetting').show();

        if (
            access.aPayroll == 0 &&
            access.aExpenses == 0 && 
            access.aExpensesDistribution == 0 && 
            access.aCustomPrices == 0
        ) {
            $('#navCostGeneral').hide();
        } else
            $('#navCostGeneral').show();

        if (
            access.aGeneralCostReport == 0
        ) {
            $('#navCostReport').hide();
        } else
            $('#navCostReport').show();
        
        if (
            access.aBackup == 0 &&
            access.aUsers == 0
        ) {
            $('#navCostAdmin').hide();
        } else
            $('#navCostAdmin').show();

        if (
            access.aPaymentMethods == 0 &&
            access.aCompanies == 0 &&
            access.aContacts == 0
        ) {
            $('#navCostQuotesBasics').hide();
        } else
            $('#navCostQuotesBasics').show();

        if (
            access.aCustomPrices == 0 &&
            access.aPricesCOP == 0 
        ) {
            $('#navPrices').hide();
        } else
            $('#navPrices').show();

        if (
            access.aAnalysisMaterials == 0 &&
            access.aEconomyScale == 0 &&
            access.aSaleObjectives == 0 &&
            access.aPriceObjectives == 0 &&
            access.aMultiproducts == 0 &&
            access.aSimulator == 0 &&
            access.aHistorical == 0
        ) {
            $('#navTools').hide();
        } else
            $('#navTools').show();
        
        if ( 
            access.aEconomyScale == 0 &&
            access.aSaleObjectives == 0&&
            access.aPriceObjectives == 0
        ) {
            $('#navbarEconomy').hide();
        } else
            $('#navbarEconomy').show();
    }
});