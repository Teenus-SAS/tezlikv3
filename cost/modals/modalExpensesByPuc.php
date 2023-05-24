<div class="modal fade" id="modalExpensesByPuc" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pucName"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-10 ml-5">
                    <div class="chart-container">
                        <canvas id="chartExpensesByPuc" style="width: 85%;"></canvas>

                        <div class="center-text">
                            <p class="text-muted mb-1 font-weight-600">Total Gasto </p>
                            <h4 class="mb-0 font-weight-bold" id="totalExpenseByCount"></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>