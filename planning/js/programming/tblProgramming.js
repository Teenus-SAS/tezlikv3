$(document).ready(function () {
  tblProgramming = $('#tblProgramming').DataTable({
    rowReorder: true,
  });
  tblProgramming.on('row-reorder', function (e, diff, edit) {
    debugger;
  });
});
