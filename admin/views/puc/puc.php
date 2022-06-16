<?php

use tezlikv2\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(__DIR__))) . "../api/src/dao/app/cost/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>

<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Cuentas</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Ingreso, Actualización e Información de Cuentas</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">

                <div class="form-inline justify-content-sm-end">
                    <div>
                        <button class="btn btn-warning" id="btnNewPUC">Nueva Cuenta</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="page-content-wrapper mt--45 mb-5 createPUC">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formCreatePuc">
                            <div class="row align-items-center">
                                <div class="col-sm">
                                    <div class="form-group m-0">
                                        <label for="accountNumber">Número de Cuenta</label>
                                        <input id="accountNumber" name="accountNumber" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="form-group m-0">
                                        <label for="account">Cuenta</label>
                                        <input id="account" name="account" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-1 p-0 mt-4">
                                    <div class="form-group m-auto text-center" style="width: 100px">
                                        <button class="btn btn-primary" id="btnCreatePuc"></button>
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
                            <table class="table table-striped text-center" id="tblPUC" name="tblPUC">
                                <tfoot>
                                    <tr>
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

<script src="/admin/js/puc/puc.js"></script>
<script src="/admin/js/puc/tblPuc.js"></script>