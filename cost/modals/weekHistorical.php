<div class="modal fade" id="modalWeekly" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <!-- Encabezado -->
            <div class="modal-header bg-light">
                <h6 class="modal-title">
                    <i class="bi bi-calendar-week me-2"></i> Seleccionar Semana
                </h6>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">
                <form id="weeklyForm">
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-2">Semana que inicia en Lunes</label>
                        <input type="week" class="form-control" id="datepicker"
                            min="2000-W01" max="2030-W52"
                            pattern="[0-9]{4}-W[0-9]{2}" required
                            onchange="validateWeekInput(this)">
                        <div class="invalid-feedback small">Seleccione una semana válida</div>
                        <div class="form-text small">Formato: AAAA-Wnn (ej. 2023-W24)</div>
                    </div>
                </form>
            </div>

            <!-- Pie -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnSaveHistorical">Guardar</button>
            </div>
        </div>
    </div>
</div>