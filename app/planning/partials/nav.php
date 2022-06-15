<div class="horizontal-topnav shadow-sm">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg topnav-menu">
            <div id="topnav-menu-content" class="collapse navbar-collapse">
                <ul id="side-menu" class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="index.php" id="topnav-dashboard" role="button">
                            <i class="bx bxs-dashboard mr-1"></i> Dashboards
                            <i class="bx bx-chevron-down"></i>
                        </a>
                        <!-- <div class="dropdown-menu" aria-labelledby="topnav-dashboard">
                            <a class="dropdown-item" href="index.html"> Multi Purpose </a>
                            <a class="dropdown-item" href="dashboard2.html"> E-commerce </a>
                            <a class="dropdown-item" href="dashboard3.html"> Server Statistics </a>
                        </div> -->
                    </li>

                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle" href="#" onclick="loadContent('page-content','../app/views/analysis/prices.php')" id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bxs-customize mr-1"></i> Generar Precios
                            <i class="bx bx-chevron-down"></i>
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" onclick="loadContent('page-content','../app/views/analysis/materials.php')" id="topnav-ui" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-tone mr-1"></i> Análisis Materia Prima
                            <i class="bx bx-chevron-down"></i>
                        </a>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="topnav-component" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-cube-alt mr-1"></i> Reportes
                            <i class="bx bx-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <span> Reporte 1</span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-Auth">
                                    <a class="dropdown-item" href="auth-login.html">Login </a>
                                    <a class="dropdown-item" href="auth-signup.html"> Register </a>
                                    <a class="dropdown-item" href="auth-recover.html"> Recover Password </a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <span> Reporte 2 </span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-forms">
                                    <a class="dropdown-item" href="auth-login-basic.html"> Login </a>
                                    <a class="dropdown-item" href="auth-signup-basic.html"> Register </a>
                                    <a class="dropdown-item" href="auth-recover-basic.html"> Recover Password</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <span> Reporte 3 </span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-forms">
                                    <a class="dropdown-item" href="auth-login-full.html"> Login </a>
                                    <a class="dropdown-item" href="auth-signup-full.html"> Register </a>
                                    <a class="dropdown-item" href="auth-recover-full.html"> Recover Password</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <span> Reporte 4 </span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-forms">
                                    <a class="dropdown-item" href="auth-lockscreen.html"> Lock Screen </a>
                                    <a class="dropdown-item" href="auth-confirmation.html"> Confirmation Screen </a>
                                    <a class="dropdown-item" href="auth-400.html"> 400 </a>
                                    <a class="dropdown-item" href="auth-404.html"> 404 </a>
                                    <a class="dropdown-item" href="auth-500.html"> 500 </a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="topnav-component" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-layer mr-1"></i> Herramientas
                            <i class="bx bx-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <i class="bx bxs-layer-plus mr-1"></i>
                                    <span> Calculadora Horas Extras</span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-advanced UI">
                                    <a class="dropdown-item" href="advanced-confirmation-box.html"> Confirmation Box </a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <i class="bx bxs-eraser mr-1"></i>
                                    <span> Conversor de Unidades </span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-forms">
                                    <a class="dropdown-item" href="forms-elements.html"> General Elements </a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" onclick="loadContent('page-content','../app/views/tutorials/tutorials.php')" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <i class="bx bx-table mr-1"></i>
                                    <span> Tutoriales </span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <!-- <div class="dropdown-menu" aria-labelledby="topnav-tables">
                                    <a class="dropdown-item" href="table-basic.html"> Basic Table </a>
                                </div> -->
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" onclick="loadContent('page-content','../app/views/support/emailSupport.php')" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <i class="bx bxs-bar-chart-alt-2 mr-1"></i>
                                    <span> Soporte</span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <!-- <div class="dropdown-menu" aria-labelledby="topnav-Charts">
                                    <a class="dropdown-item" href="charts-apex.html"> Apex </a>
                                    <a class="dropdown-item" href="charts-c3.html"> C3 </a>
                                    <a class="dropdown-item" href="charts-chartist.html">Chartist </a>
                                    <a class="dropdown-item" href="charts-chartjs.html"> Chart JS </a>
                                    <a class="dropdown-item" href="charts-flot.html"> Flot </a>
                                    <a class="dropdown-item" href="charts-knob.html"> Knob </a>
                                    <a class="dropdown-item" href="charts-morris.html"> Morris </a>
                                    <a class="dropdown-item" href="charts-peity.html"> Peity </a>
                                    <a class="dropdown-item" href="charts-sparklines.html"> Sparklines </a>
                                </div> -->
                            </div>
                            <!-- <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <i class="bx bx-aperture mr-1"></i>
                                    <span> Icons</span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-icons">
                                    <a class="dropdown-item" href="icons-boxicons.html"> Box Icon </a>
                                    <a class="dropdown-item" href="icons-feather.html"> Feather Icon </a>
                                    <a class="dropdown-item" href="icons-mdi.html"> Material Design Icons </a>
                                    <a class="dropdown-item" href="icons-simple-line.html"> Simple Line Icons </a>
                                    <a class="dropdown-item" href="icons-themify.html"> Themify Icons </a>
                                    <a class="dropdown-item" href="icons-two-tone.html"> Two Tone Icons </a>
                                    <a class="dropdown-item" href="icons-font-awesome.html"> Font Awesome 5 </a>
                                </div>
                            </div> -->
                            <!-- <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                    <i class="bx bx-map mr-1"></i>
                                    <span> Map </span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-contact">
                                    <a class="dropdown-item" href="maps-vector.html"> Vector Map </a>
                                    <a class="dropdown-item" href="maps-google.html"> Google Map </a>
                                </div>
                            </div> -->
                        </div>
                    </li>
                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="topnav-ui" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-file mr-1"></i> Utility
                            <i class="bx bx-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu mega-dropdown-menu dropdown-mega-menu-xl dropdown-menu-right" aria-labelledby="topnav-utility">
                            <div class="row">
                                <div class="col-lg-4">
                                    <a class="dropdown-item" href="utility-animation.html"> Animation </a>
                                    <a class="dropdown-item" href="utility-activity.html"> Activity </a>
                                    <a class="dropdown-item" href="utility-coming-soon.html"> Coming Soon </a>
                                    <a class="dropdown-item" href="utility-faq.html"> FAQs </a>
                                    <a class="dropdown-item" href="utility-fix-left.html"> Fix Left Sidebar </a>
                                    <a class="dropdown-item" href="utility-fix-right.html"> Fix Right Sidebar </a>
                                    <a class="dropdown-item" href="utility-gallery.html"> Gallery </a>
                                </div>
                                <div class="col-lg-4">
                                    <a class="dropdown-item" href="utility-helperclasses.html"> Helper Classes </a>
                                    <a class="dropdown-item" href="utility-lightbox.html"> Lightbox </a>
                                    <a class="dropdown-item" href="utility-maintenance.html"> Maintenance </a>
                                    <a class="dropdown-item" href="utility-pricing.html"> Pricing </a>
                                    <a class="dropdown-item" href="utility-scrollbar.html"> Scrollbar </a>
                                </div>
                                <div class="col-lg-4">
                                    <a class="dropdown-item" href="utility-search-result.html"> Search Result </a>
                                    <a class="dropdown-item" href="utility-starterpage.html"> Starter Page </a>
                                    <a class="dropdown-item" href="utility-timeline.html"> Timeline </a>
                                    <a class="dropdown-item" href="utility-timeline-horizontal.html"> Timeline Horizontal </a>
                                    <a class="dropdown-item" href="utility-treeview.html"> Tree View </a>
                                </div>
                            </div>
                        </div>
                    </li> -->
                </ul>
            </div>
        </nav>
    </div>
</div>