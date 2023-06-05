<!-- Modal -->
<div class="modal fade come-from-modal right" id="modalSimulator" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Simulador</h5>
                <button type="button" class="close closeModalSimulator" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt-2">
                    <div class="container-fluid">
                        <div class="form-row cardGeneralBtnsSimulator mb-5">
                            <div class="pos-f-t">
                                <nav class="navbar navbar-expand-lg navbar-light topnav-menu">
                                    <div class="collapse navbar-collapse" id="topnav-menu-content">
                                        <ul class="navbar-nav mr-auto">
                                            <li class="nav-item dropdown">
                                                <a class="nav-link dropdown-toggle" href="javascript:;" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="navbar-toggler-icon"></span>
                                                </a>
                                                <ul class="dropdown-menu bg-light" aria-labelledby="navbarDropdown" style="margin-left: 37px; margin-top:-37px; width:300px">
                                                    <a class="btn btn-outline-light dropdown-item" id="1"> Productos</a>
                                                    <a class="btn btn-outline-light dropdown-item" id="2"> Maquinas</a>
                                                    <a class="btn btn-outline-light dropdown-item" id="3"> Materias Prima</a>
                                                    <a class="btn btn-outline-light dropdown-item" id="4"> F. Tecnica Materia Prima</a>
                                                    <a class="btn btn-outline-light dropdown-item" id="5"> F. Tecnica Procesos</a>
                                                    <a class="btn btn-outline-light dropdown-item" id="6"> Carga Fabril</a>
                                                    <a class="btn btn-outline-light dropdown-item" id="7"> Servicios Externos</a>
                                                    <a class="btn btn-outline-light dropdown-item" id="8"> Nomina</a>
                                                    <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                                                        <a class="btn btn-outline-light dropdown-item" id="9"> Distribucion de Gastos</a>
                                                    <?php } ?>
                                                    <?php if ($_SESSION['flag_expense'] == 2) { ?>
                                                        <a class="btn btn-outline-light dropdown-item" id="10"> Recuperacion Gastos</a>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </nav>
                            </div>
                        </div>

                        <div class="col-md-12 col-lg-12 cardTableSimulator" style="display:none;">
                            <div class="card cardAddDataSimulator">
                                <div class="card-body">
                                    <form id="formDataSimulator">
                                        <div class="form-row" id="cardAddDataSimulator">

                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="tblSimulator" style="width: 380px;">
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default closeModalSimulator">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnSaveSimulator">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>