<!-- jquery -->
<!-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- ================== BEGIN BASE JS ================== -->
<script src="/assets/js/vendor.min.js"></script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="/assets/libs/flatpicker/js/flatpickr.js"></script>
<!-- <script src="/assets/libs/apexcharts/apexcharts.min.js"></script> -->
<!-- <script src="/assets/libs/chartjs/js/Chart.bundle.min.js"></script> -->
<script src="/assets/js/utils/colors.js"></script>
<!-- <script src="assets/js/pages/dashboard.init.js"></script> -->
<script src="/assets/libs/dragula/dragula.min.js"></script>
<script src="/assets/js/pages/dragula.init.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<!-- page JS -->
<script src="/assets/js/app.js"></script>
<script src="/global/js/global/sessionUser.js"></script>

<!-- Datatables -->
<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.2.0/js/dataTables.rowGroup.min.js"></script>
<script src="https://unpkg.com/file-saverjs@latest/FileSaver.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script> -->
<script src="https://unpkg.com/xlsx@latest/dist/xlsx.full.min.js"></script>
<!-- <script src="https://cdn.datatables.net/select/1.4.0/js/dataTables.select.min.js"></script> -->

<!-- Notifications -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

<!-- Confirms -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.6.6/dragula.min.js"></script> -->

<!-- xlsx -->
<!-- <script lang="javascript" src="/app/assets/libs/xlsx/xlsx.full.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.6/xlsx.full.min.js"></script> -->

<!-- Charts -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.0/chart.min.js" integrity="sha512-asxKqQghC1oBShyhiBwA+YgotaSYKxGP1rcSYTDrB0U6DxwlJjU59B67U8+5/++uFjcuVM8Hh5cokLjZlhm3Vg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0/chartjs-plugin-datalabels.min.js" integrity="sha512-R/QOHLpV1Ggq22vfDAWYOaMd5RopHrJNMxi8/lJu8Oihwi4Ho4BRFeiMiCefn9rasajKjnx9/fTQ/xkWnkDACg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- Data Picker -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js" integrity="sha512-k6/Bkb8Fxf/c1Tkyl39yJwcOZ1P4cRrJu77p83zJjN2Z55prbFHxPs9vN7q3l3+tSMGPDdoH51AEU8Vgo1cgAA==" crossorigin="anonymous"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.5/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.11.0/pdf-lib.min.js"></script>

<script src="/assets/libs/ckeditor/js/ckeditor.min.js"></script>
<script src="/assets/js/pages/compose-mail.init.js"></script>

<script src="/global/js/global/logout.js"></script>

<script src="/global/js/global/searchData.js"></script>
<script src="/global/js/global/number.js"></script>

<?php if ($_SESSION['case'] == '1') { ?>
    <script src="/global/js/global/loadNotifications.js"></script>
    <script>
        flag_composite_product = "<?= $_SESSION['flag_composite_product'] ?>";
        flag_currency_usd = "<?= $_SESSION['flag_currency_usd'] ?>";
        export_import = "<?= $_SESSION['export_import'] ?>";
        flag_export_import = "<?= $_SESSION['flag_export_import'] ?>";
        flag_employee = "<?= $_SESSION['flag_employee'] ?>";
        anual_expense = "<?= $_SESSION['anual_expense'] ?>";
        idUser = "<?= $_SESSION['idUser'] ?>";

        // Guardar los valores específicos de sessionStorage antes de limpiar
        const preservedValues = {
            indirect: sessionStorage.getItem('indirect'),
            typeCurrency: sessionStorage.getItem('typeCurrency'),
            flag_type_price: sessionStorage.getItem('flag_type_price'),
            selectTypeExpense: sessionStorage.getItem('selectTypeExpense'),
            idProduct: sessionStorage.getItem('idProduct')
        };

        // Limpiar sessionStorage
        sessionStorage.clear();

        // Restaurar los valores específicos en sessionStorage
        for (const key in preservedValues) {
            if (preservedValues[key] !== null) {
                sessionStorage.setItem(key, preservedValues[key]);
            }
        }
    </script>
    <script src="/cost/js/admin/backup/backup.js"></script>
    <script src="/cost/js/report/generalCostReport/exportReport.js"></script>
<?php } ?>

<script src="/global/js/global/lastText.js"></script>