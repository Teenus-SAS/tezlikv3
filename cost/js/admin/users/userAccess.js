$(document).ready(function () {
    loadUserAccess = async () => {
        let data = await searchData('/api/costUserAccess');

        let access = {
            aProducts: data.create_product,
            aMaterials: data.create_materials,
            aMachines: data.create_machines,
            aProcess: data.create_process,
            aProductsMaterials: data.product_materials,
            aProductsProcess: data.product_process,
            aFactoryLoad: data.factory_load,
            aServices: data.external_service,
            aPayroll: data.payroll_load,
            aExpenses: data.expense,
            aExpensesDistribution: data.expense_distribution,
            aCustomPrices: data.custom_price,
            aBackup: data.backup,
            aUsers: data.user,
            aPaymentMethods: data.quote_payment_method,
            aCompanies: data.quote_company,
            aContacts: data.quote_contact,
            aPricesCOP: data.price,
            aPricesUSD: data.price_usd,
            aAnalysisMaterials: data.analysis_material,
            aEconomyScale: data.economy_scale,
            aMultiproducts: data.multiproduct,
            aSimulator: data.simulator,
            aQuotes: data.quote,
            aSupport: data.support,
        }

        $.each(access, (index, value) => {
            if (value == 0) {
                $(`.${index}`).hide();
            } else
                $(`.${index}`).show();
        });

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
            access.aProductsProcess == 0 &&
            access.aFactoryLoad == 0 &&
            access.aServices == 0
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
            access.aPricesCOP == 0 &&
            access.aPricesUSD == 0
        ) {
            $('#navPrices').hide();
        } else
            $('#navPrices').show();

        if (
            access.aAnalysisMaterials == 0 &&
            access.aEconomyScale == 0 &&
            access.aMultiproducts == 0 &&
            access.aSimulator == 0
        ) {
            $('#navTools').hide();
        } else
            $('#navTools').show();
    }
});