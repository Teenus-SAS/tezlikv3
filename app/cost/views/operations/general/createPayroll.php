<?php

use tezlikv2\dao\UserInactiveTimeDao;

require_once dirname(dirname(dirname(dirname(__DIR__)))) . "../../api/src/dao/app/cost/login/UserInactiveTimeDao.php";
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>

<?php require_once dirname(dirname(dirname(__DIR__))) . '/modals/createPayroll.php'; ?>

<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Nómina</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Creación de Nómina</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnNewPayroll">Nueva Nómina</button>
                    <button class="btn btn-info ml-3" id="btnImportNewPayroll">Importar Nómina</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportPayroll">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportPayroll" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="filePayroll" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label"> Importar Nómina</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportPayroll">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsPayroll">Descarga Formato</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- page content -->
<div class="page-content-wrapper mt--45">
    <div class="container-fluid">
        <!-- Row 5 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblPayroll">
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Total:</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="../../app/cost/js/global/number.js"></script>
<script src="../../app/cost/js/payroll/tblPayroll.js"></script>
<script src="../../app/cost/js/process/configProcess.js"></script>
<script src="../../app/cost/js/payroll/payroll.js"></script>
<script src="../../app/cost/js/import/import.js"></script>
<script src="../../app/cost/js/payroll/importPayroll.js"></script>
<script src="../../app/cost/js/import/file.js"></script>