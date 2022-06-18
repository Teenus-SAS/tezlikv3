<?php

use tezlikv3\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(__DIR__))) . "../api/src/dao/app/global/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>
<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Actualización Licencias</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Actualización e Información de Licencias</li>
                    </ol>
                </div>
            </div>
            <!-- <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <button class="btn btn-warning" id="btnNewExpense">Nuevo Gasto</button>
                    <button class="btn btn-info ml-3" id="btnImportNewExpenses">Importar Gastos</button>
                </div>
            </div> -->
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45 mb-5 cardUpdLicense">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formUpdateLicense">
                            <div class="row align-items-center">
                                <div class="col-sm">
                                    <div class="form-group m-0">
                                        <label for="company">Empresa</label>
                                        <input id="company" name="company" type="text" readonly class="form-control bg-light text-center" disabled>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="form-group m-0">
                                        <label for="license_start">Inicio Licencia</label>
                                        <input id="license_start" name="license_start" type="date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="form-group m-0">
                                        <label for="license_end">Final Licencia</label>
                                        <input id="license_end" name="license_end" type="date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="form-group m-0">
                                        <label for="quantityUsers">Usuarios</label>
                                        <input id="quantityUsers" name="quantityUsers" type="number" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group m-auto text-center" style="width: 100px">
                                        <button class="btn btn-primary" id="btnUpdLicense">Actualizar</button>
                                    </div>
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
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped text-center" id="tblCompaniesLicense" name="tblCompaniesLicense">
                                <!-- <tfoot>
                                    <tr>
                                        <th></th>
                                    </tr>
                                </tfoot> -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/admin/js/companies/tblCompaniesLicense.js"></script>
<script src="/admin/js/companies/companiesLicense.js"></script>