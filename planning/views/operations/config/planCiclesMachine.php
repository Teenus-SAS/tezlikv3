<?php

use tezlikv3\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(dirname(__DIR__)))) . "../api/src/dao/app/global/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>

<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Ciclos Maquina</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Plan de Ciclos Máquinas</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnNewPlanCiclesMachine" name="btnNewPlanCiclesMachine">Nuevo Ciclo Máquina</button>
                    <!-- <button class="btn btn-info ml-3" id="btnImportNewMachines" name="btnNewImportMachines">Importar Máquinas</button> -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardCreatePlanCiclesMachine">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formCreatePlanCiclesMachine">
                            <div class="gridx4cm">
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <select class="form-control" name="selectNameProduct" id="selectNameProduct"></select>
                                    <label for="">Producto</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <select class="form-control" name="idMachine" id="idMachine"></select>
                                    <label for="">Maquina</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <input type="text" class="form-control text-center number" name="ciclesHour" id="ciclesHour">
                                    <label for="">Ciclo x Hora</label>
                                </div>
                                <div style="margin-bottom:0px;margin-top:5px;">
                                    <button class="btn btn-success" id="btnCreatePlanCiclesMachine">Crear Ciclo Máquina</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
                    <!--<div class="card-header">
                        <h5 class="card-title">Máquinas</h5>
                    </div>-->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblPlanCiclesMachine">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/global/js/global/number.js"></script>
<script src="/planning/js/products/configProducts.js"></script>
<script src="/planning/js/machines/configMachines.js"></script>
<script src="/planning/js/planCiclesMachine/planCiclesMachine.js"></script>
<script src="/planning/js/planCiclesMachine/tblPlanCiclesMachine.js"></script>
<!-- <script src="../global/js/import/import.js"></script>
<script src="../planning/js/productMaterials/importProductMaterials.js"></script>
<script src="../global/js/import/file.js"></script> -->