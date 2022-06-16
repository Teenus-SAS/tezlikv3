<?php

use tezlikv3\dao\UserInactiveTimeDao;

require_once dirname(dirname(dirname(__DIR__))) . "/api/src/dao/app/login/UserInactiveTimeDao.php";
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>

<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-5 col-xl-6">
                <div class="page-title">
                    <h3 class="mb-1 font-weight-bold text-dark">Tutoriales</h3>
                    <ol class="breadcrumb mb-3 mb-md-0">
                        <li class="breadcrumb-item active">Aprende fácil y rápido</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper mt--45">
    <div class="container-fluid">
        <div class="row align-items-stretch">
            <div class="col-md-8 col-lg-12">
                <div class="inbox-rightbar card">
                    <div class="card-body">
                        <h5><b>Configuración básica</b></h5>
                        <p>En esta sección vamos a aprender como cargar todos los productos, máquinas, materias primas y procesos con los que cuenta la empresa y que son necesarios al fabricar un producto</p>
                        <div>
                            <button class="btn btn-warning">Productos</button>
                            <button class="btn btn-warning">Máquinas</button>
                            <button class="btn btn-warning">Materia prima</button>
                            <button class="btn btn-warning">Procesos</button>
                        </div>
                        <hr>
                        <h5><b>Como configurar los productos, materias primas, máquinas y procesos</b></h5>
                        <p>En esta acción realizaremos las configuraciones para los productos en cuanto a sus materias primas, máquinas que usa y procesos por los que debe pasar al momento de fabricarse</p>
                        <div>
                            <button class="btn btn-info">Configure los productos y sus materias primas</button>
                            <button class="btn btn-info">Configure los productos y sus máquinas y procesos</button>
                            <button class="btn btn-info">Configure la carga fabril de las máquinas</button>
                            <button class="btn btn-info">Configure los servicios externos</button>
                        </div>
                        <hr>
                        <h5><b>Como configurar los gastos generales de la empresa</b></h5>
                        <p>En esta seccion vamos a cargar la nomina y gastos generales</p>
                        <div>
                            <button class="btn btn-success">Cargue la Nómina</button>
                            <button class="btn btn-success">Cargue los Gastos Generales</button>
                            <button class="btn btn-success">Genere la Distribución de Gastos</button>
                        </div>
                        <hr>
                        <h5><b>Usuarios</b></h5>
                        <p>En esta seccion vamos a aprender a crear, eliminar y actualizar usuarios, ademas de otorgarles los diferentes permisos para acceder a la plataforma</p>
                        <div>
                            <button class="btn btn-secondary">Cree, actualice y elimine los usuarios</button>
                            <button class="btn btn-secondary">Otorgue los permisos necesarios</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>