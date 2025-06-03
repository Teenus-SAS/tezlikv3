<div class="modal fade" id="updatesNotices" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content updates-window">
            <!-- Encabezado con gradiente y versión -->
            <div class="modal-header updates-header">
                <div class="d-flex align-items-center">
                    <div class="update-icon">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div>
                        <h2 class="modal-title text-white">Actualizaciones de la Plataforma</h2>
                        <div class="version-badge">Mayo 2025 (Versión 2.30)</div>
                    </div>
                </div>
            </div>

            <!-- Cuerpo del modal -->
            <div class="modal-body">
                <div class="update-intro">
                    <p class="lead">Bienvenido a la versión de mayo de 2025 de TezlikSoftware. Hay muchas actualizaciones en esta versión que esperamos que les guste, algunos de los aspectos más destacados incluyen:</p>
                </div>

                <!-- Listado de actualizaciones -->
                <div class="updates-container">
                    <!-- Actualización 1 -->
                    <div class="update-card security">
                        <div class="update-header">
                            <span class="update-badge">Actualización 2.30.1</span>
                            <h3>Seguridad Reforzada</h3>
                        </div>
                        <ul class="update-features">
                            <li>
                                <i class="bi bi-shield-lock"></i>
                                <span><strong>Protección de Rutas:</strong> Mejoras en backend y frontend para prevenir accesos no autorizados</span>
                            </li>
                            <li>
                                <i class="bi bi-clock-history"></i>
                                <span><strong>Temporizador de Inactividad:</strong> Cierre de sesión automático después de 10 minutos de inactividad</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Actualización 2 -->
                    <div class="update-card optimization">
                        <div class="update-header">
                            <span class="update-badge">Actualización 2.30.2</span>
                            <h3>Optimización de DataTables</h3>
                        </div>
                        <ul class="update-features">
                            <li>
                                <i class="bi bi-search"></i>
                                <span><strong>Selects con Select2:</strong> Integración de Select2 para búsquedas más intuitivas y rápidas</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Actualización 3 -->
                    <div class="update-card fixes">
                        <div class="update-header">
                            <span class="update-badge">Actualización 2.30.3</span>
                            <h3>Correcciones Menores</h3>
                        </div>
                        <ul class="update-features">
                            <li>
                                <i class="bi bi-bug"></i>
                                <span>Solucionados errores de rendimiento en carga de datos</span>
                            </li>
                            <li>
                                <i class="bi bi-browser-chrome"></i>
                                <span>Mejoras en compatibilidad con navegadores modernos</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Novedades destacadas -->
                <div class="highlighted-features">
                    <h3 class="highlight-title">
                        <i class="bi bi-stars"></i> Novedades Destacadas
                    </h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon security">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <h4>Seguridad Mejorada</h4>
                                <ul>
                                    <li>Rutas críticas con autenticación reforzada</li>
                                    <li>Validación de headers HTTP contra CSRF</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon ux">
                                    <i class="bi bi-person-check"></i>
                                </div>
                                <h4>Experiencia de Usuario</h4>
                                <ul>
                                    <li>Búsquedas en DataTables más eficientes</li>
                                    <li>Interfaz optimizada para móviles</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie del modal -->
            <div class="modal-footer updates-footer">
                <!-- <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button> -->
                <button type="button" class="btn btn-primary btnAcceptUpdatePlatform">
                    <i class="bi bi-check-circle me-1"></i> Entendido
                </button>
            </div>
        </div>
    </div>
</div>