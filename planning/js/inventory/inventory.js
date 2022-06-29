$(document).ready(function () {
  //   $('.cardTableInvProducts').hide();
  //   $('.cardTableInvMaterials').hide();
  //   $('.cardTableInvSupplies').hide();

  $('#category').change(function (e) {
    e.preventDefault();
    value = this.value;

    if (value == 1) {
      $('.cardTableInvProducts').show(800);
      $('.cardTableInvMaterials').hide(800);
      $('.cardTableInvSupplies').hide(800);
    }
    if (value == 2) {
      $('.cardTableInvMaterials').show(800);
      $('.cardTableInvProducts').hide(800);
      $('.cardTableInvSupplies').hide(800);
    }
    if (value == 3) {
      $('.cardTableInvSupplies').show(800);
      $('.cardTableInvProducts').hide(800);
      $('.cardTableInvMaterials').hide(800);
    }
    if (value == 4) {
      $('.cardTableInvProducts').show(800);
      $('.cardTableInvMaterials').show(800);
      $('.cardTableInvSupplies').show(800);
    }
  });
});
