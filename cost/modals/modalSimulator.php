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
                                <div class="collapse" id="navbarToggleExternalContent">
                                    <div class="form-row bg-light p-4">
                                        <div class="col-sm mb-2">
                                            <button type="button" class="btn btn-outline-secondary" value="1"> Productos</button>
                                        </div>
                                        <div class="col-sm">
                                            <button type="button" class="btn btn-outline-secondary" value="2"> Maquinas</button>
                                        </div>
                                        <div class="col-sm-6 mb-2">
                                            <button type="button" class="btn btn-outline-secondary" value="3"> Materias Prima</button>
                                        </div>
                                        <div class="col-sm-6">
                                            <button type="button" class="btn btn-outline-secondary" value="4"> F. Tecnica Materia Prima</button>
                                        </div>
                                        <div class="col-sm-6 mb-2">
                                            <button type="button" class="btn btn-outline-secondary" value="5"> F. Tecnica Procesos</button>
                                        </div>
                                        <div class="col-sm-6">
                                            <button type="button" class="btn btn-outline-secondary" value="6"> Carga Fabril</button>
                                        </div>
                                        <div class="col-sm-6 mb-2">
                                            <button type="button" class="btn btn-outline-secondary" value="7"> Servicios Externos</button>
                                        </div>
                                        <div class="col-sm-6 mb-2">
                                            <button type="button" class="btn btn-outline-secondary" value="8"> Nomina</button>
                                        </div>
                                        <?php if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { ?>
                                            <div class="col-sm-6">
                                                <button type="button" class="btn btn-outline-secondary" value="9"> Distribucion de Gastos</button>
                                            </div>
                                        <?php } ?>
                                        <?php if ($_SESSION['flag_expense'] == 2) { ?>
                                            <div class="col-sm">
                                                <button type="button" class="btn btn-outline-secondary" value="10"> Recuperacion Gastos</button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <nav class="navbar navbar-light bg-light form-row">
                                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                                        <span class="navbar-toggler-icon"></span>
                                    </button>
                                </nav>
                            </div>
                        </div>

                        <!-- Productos -->
                        <div class="col-md-12 col-lg-12 cardTableSimulator cardTableSimulatorProducts" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tblSimulatorProducts" style="width: 380px;">
                                </table>
                            </div>
                        </div>

                        <!-- Materia Prima -->
                        <div class="col-md-12 col-lg-12 cardTableSimulator cardTableSimulatorMaterials" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tblSimulatorMaterials" style="width: 380px;">
                                </table>
                            </div>
                        </div>

                        <!-- Maquinas -->
                        <div class="col-md-12 col-lg-12 cardTableSimulator cardTableSimulatorMachines" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tblSimulatorMachines" style="width: 380px;">
                                </table>
                            </div>
                        </div>

                        <!-- Productos Materiales -->
                        <div class="col-md-12 col-lg-12 cardTableSimulator cardTableSimulatorProductsMaterials" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tblSimulatorProductsMaterials" style="width: 380px;">
                                </table>
                            </div>
                        </div>

                        <!-- Productos Procesos -->
                        <div class="col-md-12 col-lg-12 cardTableSimulator cardTableSimulatorProductsProcess" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tblSimulatorProductsProcess" style="width: 380px;">
                                </table>
                            </div>
                        </div>

                        <!-- Carga Fabril -->
                        <div class="col-md-12 col-lg-12 cardTableSimulator cardTableSimulatorFactoryLoad" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tblSimulatorFactoryLoad" style="width: 380px;">
                                </table>
                            </div>
                        </div>

                        <!-- Nomina -->
                        <div class="col-md-12 col-lg-12 cardTableSimulator cardTableSimulatorPayroll" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tblSimulatorPayroll" style="width: 380px;">
                                </table>
                            </div>
                        </div>

                        <!-- Servicios Externos -->
                        <div class="col-md-12 col-lg-12 cardTableSimulator cardTableSimulatorServices" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tblSimulatorServices" style="width: 380px;">
                                </table>
                            </div>
                        </div>

                        <!-- Distribucion de Gastos -->
                        <div class="col-md-12 col-lg-12 cardTableSimulator cardTableSimulatorExpensesDistribution" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tblSimulatorExpensesDistribution" style="width: 380px;">
                                </table>
                            </div>
                        </div>

                        <!-- Recuperacion de Gastos -->
                        <div class="col-md-12 col-lg-12 cardTableSimulator cardTableSimulatorExpensesRecover" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tblSimulatorExpensesRecover" style="width: 380px;">
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