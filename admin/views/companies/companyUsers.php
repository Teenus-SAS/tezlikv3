<?php

use tezlikv3\dao\UserInactiveTimeDao;

// require_once(dirname(dirname(dirname(dirname(__DIR__)))) . "/api/src/dao/app/login/UserInactiveTimeDao.php");
require_once(dirname(dirname(dirname(__DIR__))) . "/api/src/dao/app/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>
<?php require_once dirname(dirname(__DIR__)) . '/modals/createCompany.php'; ?>
<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Usuarios</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Usuarios de la Empresa</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-7 col-xl-6">

                <div class="form-inline justify-content-sm-end">
                    <!-- <div class="card my-0 mx-3">
                        <div class="card-body p-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input statusCompanies" id="status" checked>
                                <label class="custom-control-label text-secondary" for="status"></label>
                            </div>
                        </div>
                    </div> -->
                    <div>
                        <button class="btn btn-warning" id="btnReturnCompanies" onclick="loadContent('page-content','views/companies/companiesLicenses.php')">Volver</button>
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
                            <table class="table table-striped text-center" id="tblCompanyUsers" name="tblCompanyUsers">
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


<script src="/admin/js/companies/companyUsers.js"></script>
<script src="/admin/js/companies/tblCompanyUsers.js"></script>