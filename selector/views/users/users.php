<?php

use tezlikv3\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(__DIR__))) . "../api/src/dao/app/global/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>

<?php require_once dirname(dirname(dirname(__DIR__))) . '/selector/modals/createUserAccess.php'; ?>

<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Usuarios y Accesos</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Creación de Usuario y Configuración de accesos</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">
                <div class="form-inline justify-content-sm-end">
                    <select class="form-control" name="rol" id="rol" style="margin-right: 15px;">
                        <option value="" selected disabled>Roles</option>
                        <option value="1">Costos</option>
                        <option value="2">Planeación</option>
                    </select>
                    <button class="btn btn-warning" id="btnNewUser">Nuevo Usuario y Accesos</button>
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
                            <table class="table table-striped" id="tblUsers">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/selector/js/users/tblUsers.js"></script>
<script src="/selector/js/users/users.js"></script>