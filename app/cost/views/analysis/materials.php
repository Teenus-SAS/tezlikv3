<?php

use tezlikv2\dao\UserInactiveTimeDao;

require_once dirname(dirname(dirname(__DIR__))) . "/api/src/dao/app/login/UserInactiveTimeDao.php";
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>

<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Materias Primas</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Análisis de Materias Primas</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnComposition">Composición</button>
                    <button class="btn btn-info ml-3" id="btnRawMaterialsAnalysis">Análisis de Materias Primas</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardCreateRawMaterials">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body gridx2">
                        <div class="form-group floating-label enable-floating-label show-label">
                            <select class="form-control" name="refProduct" id="refProduct"></select>
                            <label for="">Referencia</label>
                        </div>
                        <div class="form-group floating-label enable-floating-label show-label">
                            <select class="form-control" name="selectNameProduct" id="selectNameProduct"></select>
                            <label for="">Producto</label>
                        </div>
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
            <div class="col-12 cardTableRawMaterials">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Materias Primas</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tblMaterials">
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Total:</th>
                                        <th></th>
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
            <div class="col-12 cardRawMaterialsAnalysis">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Análisis Materia Prima</h5>
                    </div>
                    <div class="card-body">
                        <div class="gridx2">
                            <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                <input class="form-control text-center number" type="text" id="unitsmanufacturated" style="width: 200px;" />
                                <label>Unidades a Fabricar</label>
                            </div>
                            <div class="gridx2">
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <input class="form-control text-center" id="monthlySavings" style="width: 200px;" readonly />
                                    <label>Ahorro Mensual</label>
                                </div>
                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                    <input class="form-control text-center" id="annualSavings" style="width: 200px;" readonly />
                                    <label>Ahorro Anual</label>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-success mt-3" role="alert">
                            Cantidad de Materias primas que consumen el 80% del valor del costo
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped text-center" id="tblAnalysisMaterials">
                                <thead>
                                    <tr>
                                        <th scope="col">No.</th>
                                        <th scope="col">Participación</th>
                                        <th scope="col">Materia Prima</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Precio Actual</th>
                                        <th scope="col">Precio a Negociar</th>
                                        <th scope="col">Porcentaje</th>
                                        <th scope="col">Costo Unidad</th>
                                        <th scope="col">Costo Total</th>
                                        <th scope="col">Costo Proyectado</th>
                                    </tr>
                                </thead>
                                <tbody class="colMaterials">
                                </tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="../../app/js/global/number.js"></script>
<script src="../../app/js/products/configProducts.js"></script>
<script src="../../app/js/rawMaterials/configRawMaterials.js"></script>
<script src="../../app/js/analysis/materials/tblmaterials.js"></script>
<script src="../../app/js/analysis/materials/tblAnalysisMaterials.js"></script>
<script src="../../app/js/analysis/materials/materials.js"></script>