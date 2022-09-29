// $(document).ready(function () {
//   /* ACCESOS DE USUARIO */
//   $.ajax({
//     type: 'POST',
//     url: `/api/planningUserAccess`,
//     success: function (resp) {
//       let acces = {
//         planningCreateProducts: resp.create_product,
//         planningCreateMaterials: resp.create_materials,
//         planningCreateMachines: resp.create_machines,
//         planningCreateProcess: resp.create_process,
//         planningProductsMaterials: resp.product_materials,
//         planningProductsProcess: resp.product_process,
//         factoryLoad: resp.factory_load,
//         servicesExternal: resp.external_service,
//         payroll: resp.payroll_load,
//         generalExpenses: resp.expense,
//         distributionExpenses: resp.expense_distribution,
//         users: resp.user,
//       };

//       $.each(acces, (index, value) => {
//         if (value === 0) {
//           $(`.${index}`).remove();
//         }

//         if (
//           acces.planningCreateProducts === 0 &&
//           acces.planningCreateMaterials === 0 &&
//           acces.planningCreateMachines === 0 &&
//           acces.planningCreateProcess === 0
//         ) {
//           $('#navBasics').remove();
//         }

//         if (
//           acces.planningProductsMaterials === 0 &&
//           acces.planningProductsProcess === 0 &&
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
