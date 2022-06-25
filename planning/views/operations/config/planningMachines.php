<?php

use tezlikv3\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(dirname(__DIR__)))) . "../api/src/dao/app/global/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>

<?php require_once dirname(dirname(dirname(__DIR__))) . '/modals/createPlanMachine.php'; ?>

<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Máquinas</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Programacion de Máquinas</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnNewPlanMachine" name="btnNewPlanMachine">Adicionar Plan Máquina</button>
                    <button class="btn btn-info ml-3" id="btnImportNewPlanMachines" name="btnNewImportPlanMachines">Importar Máquinas</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardImportPlanMachines">
    <div class="container-fluid">
        <div class="row">
            <form id="formImportPlanMachines" enctype="multipart/form-data">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-0">
                            <div class="gridx4ip">
                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                    <input class="form-control" type="file" id="filePlanMachines" accept=".xls,.xlsx">
                                    <label for="formFile" class="form-label"> Importar Máquinas</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-success" id="btnImportPlanMachines">Importar</button>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                    <button type="text" class="btn btn-info" id="btnDownloadImportsPlanMachines">Descarga Formato</button>
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
                    <div class="card-header">
                        <h5 class="card-title">Máquinas</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblPlanMachines">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/planning/js/planningMachines/datePicker.js"></script>
<script src="/planning/js/planningMachines/tblPlanningMachines.js"></script>
<script src="/planning/js/planningMachines/planningMachines.js"></script>
<script src="/planning/js/machines/configMachines.js"></script>
<script src="../global/js/import/import.js"></script>
<script src="/planning/js/planningMachines/importPlanningMachines.js"></script>
<script src="../global/js/import/file.js"></script>
<script src="/global/js/global/validateExt.js"></script>