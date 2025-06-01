<div class="modal fade" id="modalHistorical" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content compact-modal">
            <!-- Encabezado minimalista -->
            <div class="modal-header compact-header">
                <h6 class="modal-title">
                    <i class="bi bi-calendar3 me-2"></i> Guardar Histórico
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Cuerpo compacto -->
            <div class="modal-body p-3">
                <form id="compactHistoricalForm">
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Seleccione mes y año</label>
                        <input type="month" class="form-control form-control-sm" id="compactDate" required>
                        <div class="invalid-feedback small">Selección requerida</div>
                    </div>
                </form>
            </div>

            <!-- Pie compacto -->
            <div class="modal-footer compact-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-sm btn-primary" id="btnCompactSave">Guardar</button>
            </div>
        </div>
    </div>
</div>