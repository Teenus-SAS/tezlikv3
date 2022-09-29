// $(document).ready(function () {
//   /* ACCESOS DE USUARIO */
//   $.ajax({
//     type: 'POST',
//     url: `/api/costcostUserAccess`,
//     success: function (resp) {
//       let acces = {
//         costCreateProducts: resp.create_product,
//         costCreateMaterials: resp.create_materials,
//         costCreateMachines: resp.create_machines,
//         costCreateProcess: resp.create_process,
//         productsMaterials: resp.product_materials,
//         productsProcess: resp.product_process,
//         factoryLoad: resp.factory_load,
//         servicesExternal: resp.external_service,
//         payroll: resp.payroll_load,
//         generalExpenses: resp.expense,
//         distributionExpenses: resp.expense_distribution,
//         users: resp.costUser,
//       };

//       $.each(acces, (index, value) => {
//         if (value === 0) {
//           $(`.${index}`).remove();
//         }

//         if (
//           acces.costCreateProducts === 0 &&
//           acces.costCreateMaterials === 0 &&
//           acces.costCreateMachines === 0 &&
//           acces.costCreateProcess === 0
//         ) {
//           $('#navBasics').remove();
//         }

//         if (
//           acces.productsMaterials === 0 &&
//           acces.productsProcess === 0 &&
//           acces.factoryLoad === 0 &&
//           acces.servicesExternal === 0
//         ) {
//           $('#navSetting').remove();
//         }

//         if (
//           acces.payroll === 0 &&
//           acces.generalExpenses === 0 &&
//           acces.distributionExpenses === 0
//         ) {
//           $('#navGeneral').remove();
//         }

//         if (acces.users === 0) {
//           $('#navAdmin').remove();
//         }
//       });
//     },
//   });
// });
