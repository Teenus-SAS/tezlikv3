<?php

use tezlikv2\dao\UserInactiveTimeDao;

require_once(dirname(dirname(dirname(dirname(__DIR__)))) . "/api/src/dao/app/login/UserInactiveTimeDao.php");
$userinactivetimeDao = new UserInactiveTimeDao();
$userinactivetimeDao->findSession();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="MatrrDigital">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Multipurpose | Letstart - Responsive Admin Dashboard Template</title>
    <link rel="shortcut icon" href="/assets/images/favicon.png" type="image/x-icon" />
</head>

<body class="horizontal-navbar">
    <!-- Begin Page -->
    <div class="page-wrapper">
        <!-- Begin Header -->
        <!-- Begin Header -->
        <header id="page-topbar" class="topbar-header">
            <div class="navbar-header">
                <div class="left-bar">
                    <div class="navbar-brand-box">
                        <a href="index.html" class="logo logo-dark">
                            <span class="logo-sm"><img src="/assets/images/logo-white-sm.png" alt="Lettstart Admin"></span>
                            <span class="logo-lg"><img src="/assets/images/logo-white.png" alt="Lettstart Admin"></span>
                        </a>
                        <a href="index.html" class="logo logo-light">
                            <span class="logo-sm"><img src="/assets/images/logo-sm.png" alt="Lettstart Admin"></span>
                            <span class="logo-lg"><img src="/assets/images/logo.png" alt="Lettstart Admin"></span>
                        </a>
                    </div>
                    <a class="navbar-toggle collapsed" href="javascript:void(0)" data-toggle="collapse" data-target="#topnav-menu-content" aria-expanded="false">
                        <span></span>
                        <span></span>
                        <span></span>
                    </a>
                    <div class="dropdown-mega dropdown d-none d-lg-block ml-2">
                        <a href="javascript:void(0)" data-toggle="dropdown" id="mega-dropdown" aria-haspopup="true" aria-expanded="false" class="btn header-item">
                            Mega Menu <i class="bx bx-chevron-down"></i>
                        </a>
                        <div class="dropdown-megamenu dropdown-menu" aria-labelledby="mega-dropdown">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h5 class="font-size-14 font-weight-600">UI Components</h5>
                                            <ul class="list-unstyled megamenu-list">
                                                <li><a href="javascript:void(0);">Lightbox</a></li>
                                                <li><a href="javascript:void(0);">Range Slider</a></li>
                                                <li><a href="javascript:void(0);">Sweet Alert</a></li>
                                                <li><a href="javascript:void(0);">Rating</a></li>
                                                <li><a href="javascript:void(0);">Forms</a></li>
                                                <li><a href="javascript:void(0);">Tables</a></li>
                                                <li><a href="javascript:void(0);">Charts</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-4">
                                            <h5 class="font-size-14 font-weight-600">Applications</h5>
                                            <ul class="list-unstyled megamenu-list">
                                                <li><a href="javascript:void(0);">Ecommerce</a></li>
                                                <li><a href="javascript:void(0);">Calendar</a></li>
                                                <li><a href="javascript:void(0);">Email</a></li>
                                                <li><a href="javascript:void(0);">Projects</a></li>
                                                <li><a href="javascript:void(0);">Tasks</a></li>
                                                <li><a href="javascript:void(0);">Contacts</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-4">
                                            <h5 class="font-size-14 font-weight-600">Extra Pages</h5>
                                            <ul class="list-unstyled megamenu-list">
                                                <li><a href="javascript:void(0);">Light Sidebar</a></li>
                                                <li><a href="javascript:void(0);">Compact Sidebar</a></li>
                                                <li><a href="javascript:void(0);">Horizontal layout</a></li>
                                                <li><a href="javascript:void(0);">Maintenance</a></li>
                                                <li><a href="javascript:void(0);">Coming Soon</a></li>
                                                <li><a href="javascript:void(0);">Timeline</a></li>
                                                <li><a href="javascript:void(0);">FAQs</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="row align-items-center">
                                        <div class="col-sm-6">
                                            <h5 class="font-size-14 font-weight-600">UI Components</h5>
                                            <ul class="list-unstyled megamenu-list">
                                                <li><a href="javascript:void(0);">Lightbox</a></li>
                                                <li><a href="javascript:void(0);">Range Slider</a></li>
                                                <li><a href="javascript:void(0);">Sweet Alert</a></li>
                                                <li><a href="javascript:void(0);">Rating</a></li>
                                                <li><a href="javascript:void(0);">Forms</a></li>
                                                <li><a href="javascript:void(0);">Tables</a></li>
                                                <li><a href="javascript:void(0);">Charts</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mega-dd-slider">
                                                <div class="owl-carousel">
                                                    <div class="item">
                                                        <img src="/assets/images/megamenu-img.svg" alt="Lettstart Admin" class="img-fluid mx-auto d-block">
                                                    </div>
                                                    <div class="item">
                                                        <img src="/assets/images/megamenu-img2.svg" alt="Lettstart Admin" class="img-fluid mx-auto d-block">
                                                    </div>
                                                    <div class="item">
                                                        <img src="/assets/images/megamenu-img3.svg" alt="Lettstart Admin" class="img-fluid mx-auto d-block">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right-bar">
                    <form class="app-search mr-2 d-none d-lg-block">
                        <div class="search-box position-relative">
                            <input type="text" placeholder="Search..." class="form-control">
                            <span class="bx bx-search"></span>
                        </div>
                    </form>
                    <div class="d-inline-flex ml-0 ml-sm-2 d-lg-none dropdown">
                        <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-search-dropdown" aria-expanded="false" class="btn header-item notify-icon">
                            <i class="bx bx-search"></i>
                        </button>
                        <div aria-labelledby="page-header-search-dropdown" class="dropdown-menu-lg dropdown-menu-right p-0 dropdown-menu">
                            <form class="p-3">
                                <div class="search-box">
                                    <div class="position-relative">
                                        <input type="text" placeholder="Search..." class="form-control form-control-sm">
                                        <i class="bx bx-search icon"></i>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="d-inline-flex ml-0 ml-sm-2 dropdown">
                        <button aria-haspopup="true" data-toggle="dropdown" type="button" id="page-header-country-dropdown" aria-expanded="false" class="btn header-item">
                            <img src="/assets/images/flags/us.svg" class="mh-16" alt="USA">
                            <span class="ml-2 d-none d-sm-inline-block">EN</span>
                        </button>
                        <div aria-labelledby="page-header-country-dropdown" id="countries" class="dropdown-menu-right dropdown-menu">
                            <a href="javascript:void(0);" class="dropdown-item">
                                <img class="mr-1 mh-12" src="/assets/images/flags/us.svg" alt="USA">
                                <span class="align-middle" data-lang="en">USA</span>
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <img class="mr-1 mh-12" src="/assets/images/flags/ge.svg" alt="German">
                                <span class="align-middle" data-lang="ge">German</span>
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <img class="mr-1 mh-12" src="/assets/images/flags/ru.svg" alt="Russia">
                                <span class="align-middle" data-lang="ru">Russia</span>
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <img class="mr-1 mh-12" src="/assets/images/flags/in.svg" alt="India">
                                <span class="align-middle" data-lang="in">India</span>
                            </a>
                        </div>
                    </div>
                    <div class="d-none d-lg-inline-flex ml-2 dropdown">
                        <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-app-dropdown" aria-expanded="false" class="btn header-item notify-icon">
                            <i class="bx bx-customize"></i>
                        </button>
                        <div aria-labelledby="page-header-app-dropdown" class="dropdown-menu-lg dropdown-menu-right dropdown-menu">
                            <div class="px-lg-2">
                                <div class="row no-gutters">
                                    <div class="col">
                                        <a href="javascript: void(0);" class="dropdown-icon-item">
                                            <img src="/assets/images/brands/github.png" alt="Github">
                                            <span>GitHub</span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript: void(0);" class="dropdown-icon-item">
                                            <img src="/assets/images/brands/bitbucket.png" alt="bitbucket">
                                            <span>Bitbucket</span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript: void(0);" class="dropdown-icon-item">
                                            <img src="/assets/images/brands/dribbble.png" alt="dribbble">
                                            <span>Dribbble</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="row no-gutters">
                                    <div class="col">
                                        <a href="javascript: void(0);" class="dropdown-icon-item">
                                            <img src="/assets/images/brands/dropbox.png" alt="dropbox">
                                            <span>Dropbox</span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript: void(0);" class="dropdown-icon-item">
                                            <img src="/assets/images/brands/mail_chimp.png" alt="mail_chimp">
                                            <span>Mail Chimp</span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript: void(0);" class="dropdown-icon-item">
                                            <img src="/assets/images/brands/slack.png" alt="slack">
                                            <span>Slack</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-none d-lg-inline-flex ml-2">
                        <button type="button" data-toggle="fullscreen" class="btn header-item notify-icon" id="full-screen">
                            <i class="bx bx-fullscreen"></i>
                        </button>
                    </div>
                    <div class="d-inline-flex ml-0 ml-sm-2 dropdown">
                        <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-notification-dropdown" aria-expanded="false" class="btn header-item notify-icon position-relative">
                            <i class="bx bx-bell bx-tada"></i>
                            <span class="badge badge-danger badge-pill notify-icon-badge">3</span>
                        </button>
                        <div aria-labelledby="page-header-notification-dropdown" class="dropdown-menu-lg dropdown-menu-right p-0 dropdown-menu">
                            <div class="notify-title p-3">
                                <h5 class="font-size-14 font-weight-600 mb-0">
                                    <span>Notification</span>
                                    <a class="text-primary" href="javascript: void(0);">
                                        <small>Clear All</small>
                                    </a>
                                </h5>
                            </div>
                            <div class="notify-scroll">
                                <div class="scroll-content" id="notify-scrollbar">
                                    <div class="scroll-content">
                                        <a href="javascript:void(0);" class="dropdown-item notification-item">
                                            <div class="media">
                                                <div class="avatar avatar-xs bg-primary">
                                                    <i class="bx bx-user-plus"></i>
                                                </div>
                                                <p class="media-body">
                                                    New user registered.
                                                    <small class="text-muted">5 hours ago</small>
                                                </p>
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item notification-item">
                                            <div class="media">
                                                <div class="avatar avatar-xs">
                                                    <img alt="Lettstart Admin" class="img-fluid rounded-circle" src="/assets/images/users/avatar-1.jpg">
                                                </div>
                                                <p class="media-body">
                                                    John likes your photo
                                                    <small class="text-muted">5 hours ago</small>
                                                </p>
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item notification-item">
                                            <div class="media">
                                                <div class="avatar avatar-xs">
                                                    <img alt="Lettstart Admin" class="img-fluid rounded-circle" src="/assets/images/users/avatar-2.jpg">
                                                </div>
                                                <p class="media-body">
                                                    Johnson
                                                    <small class="text-muted">Wow! admin looks good</small>
                                                </p>
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item notification-item">
                                            <div class="media">
                                                <div class="avatar avatar-xs bg-danger">
                                                    <i class="bx bx-server"></i>
                                                </div>
                                                <p class="media-body">
                                                    Server getting down
                                                    <small class="text-muted">1 min ago</small>
                                                </p>
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item notification-item">
                                            <div class="media">
                                                <div class="avatar avatar-xs bg-info">
                                                    <i class="bx bx-tag"></i>
                                                </div>
                                                <p class="media-body">
                                                    Someone tag you
                                                    <small class="text-muted">2 hours ago</small>
                                                </p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="notify-all">
                                    <a href="javascript: void(0);" class="text-primary text-center p-3">
                                        <small>View All</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-inline-flex ml-0 ml-sm-2 dropdown">
                        <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-profile-dropdown" aria-expanded="false" class="btn header-item">
                            <img src="/assets/images/users/avatar-1.jpg" alt="Header Avatar" class="avatar avatar-xs mr-0">
                            <span class="d-none d-xl-inline-block ml-1">Henry</span>
                            <i class="bx bx-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div aria-labelledby="page-header-profile-dropdown" class="dropdown-menu-right dropdown-menu">
                            <a href="javascript: void(0);" class="dropdown-item">
                                <i class="bx bx-user mr-1"></i> Profile
                            </a>
                            <a href="javascript: void(0);" class="dropdown-item">
                                <i class="bx bx-wrench mr-1"></i> Settings
                            </a>
                            <a href="javascript: void(0);" class="dropdown-item">
                                <i class="bx bx-wallet mr-1"></i> My Wallet
                            </a>
                            <a href="javascript: void(0);" class="dropdown-item">
                                <i class="bx bx-lock mr-1"></i> Lock screen
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript: void(0);" class="text-danger dropdown-item">
                                <i class="bx bx-log-in mr-1 text-danger"></i> Logout
                            </a>
                        </div>
                    </div>
                    <div class="d-inline-flex">
                        <button type="button" id="layout" class="btn header-item notify-icon">
                            <i class="bx bx-cog bx-spin"></i>
                        </button>
                    </div>
                </div>
            </div>
        </header>
        <!-- Header End -->
        <!-- Header End -->
        <!-- Begin Left Navigation -->
        <div class="horizontal-topnav shadow-sm">
            <div class="container-fluid">
                <nav class="navbar navbar-expand-lg topnav-menu">
                    <div id="topnav-menu-content" class="collapse navbar-collapse">
                        <ul id="side-menu" class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bxs-dashboard mr-1"></i> Dashboards
                                    <i class="bx bx-chevron-down"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-dashboard">
                                    <a class="dropdown-item" href="index.html"> Multi Purpose </a>
                                    <a class="dropdown-item" href="dashboard2.html"> E-commerce </a>
                                    <a class="dropdown-item" href="dashboard3.html"> Server Statistics </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bxs-customize mr-1"></i> Apps
                                    <i class="bx bx-chevron-down"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-apps">
                                    <a class="dropdown-item" href="calender.html">
                                        <i class="bx bx-calendar mr-1"></i>
                                        <span> Calender</span>
                                    </a>
                                    <a class="dropdown-item" href="chat.html">
                                        <i class="bx bx-chat mr-1"></i>
                                        <span> Chat</span>
                                    </a>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-haspopup="true" data-toggle="dropdown" aria-expanded="false">
                                            <i class="bx bxs-user-detail mr-1"></i>
                                            <span> Contacts</span>
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-contact">
                                            <a class="dropdown-item" href="member-create.html"> Add Member </a>
                                            <a class="dropdown-item" href="member-list.html"> Member List </a>
                                            <a class="dropdown-item" href="member-grid.html"> Member Grid </a>
                                            <a class="dropdown-item" href="member-profile.html"> Member Profile </a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                            <i class="bx bx-store mr-1"></i>
                                            <span> Ecommerce</span>
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-contact">
                                            <a class="dropdown-item" href="ecommerce-add-product.html"> Add Product </a>
                                            <a class="dropdown-item" href="ecommerce-product.html"> Products </a>
                                            <a class="dropdown-item" href="ecommerce-product-details.html"> Product Detail </a>
                                            <a class="dropdown-item" href="ecommerce-orders.html"> Orders </a>
                                            <a class="dropdown-item" href="ecommerce-customers.html"> Customers </a>
                                            <a class="dropdown-item" href="ecommerce-cart.html"> Cart </a>
                                            <a class="dropdown-item" href="ecommerce-checkout.html"> Checkout </a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                            <i class="bx bx-envelope mr-1"></i>
                                            <span> Email</span>
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-contact">
                                            <a class="dropdown-item" href="email-inbox.html"> Inbox </a>
                                            <a class="dropdown-item" href="email-read.html"> Read Mail </a>
                                            <a class="dropdown-item" href="email-compose.html"> Compose Mail </a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                            <i class="bx bx-receipt mr-1"></i>
                                            <span> Invoices</span>
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-contact">
                                            <a class="dropdown-item" href="invoice-list.html"> Invoice List </a>
                                            <a class="dropdown-item" href="invoice-details.html"> Invoice Detail </a>
                                            <a class="dropdown-item" href="invoice-grid.html"> Invoice Grid </a>
                                            <a class="dropdown-item" href="invoice-create.html"> Generate Invoice </a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                            <i class="bx bx-briefcase-alt-2 mr-1"></i>
                                            <span> Projects</span>
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-contact">
                                            <a class="dropdown-item" href="project-list.html"> Project List </a>
                                            <a class="dropdown-item" href="project-grid.html"> Project Grid </a>
                                            <a class="dropdown-item" href="project-overview.html"> Project Overview </a>
                                            <a class="dropdown-item" href="project-create.html"> Create New </a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                            <i class="bx bx-task mr-1"></i>
                                            <span> Tasks</span>
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-contact">
                                            <a class="dropdown-item" href="task-list.html"> Task List </a>
                                            <a class="dropdown-item" href="kanban-board.html"> Kanban Board </a>
                                            <a class="dropdown-item" href="task-overview.html"> Task Overview </a>
                                            <a class="dropdown-item" href="task-create.html"> Create Task </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="topnav-ui" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bx-tone mr-1"></i> UI Elements
                                    <i class="bx bx-chevron-down"></i>
                                </a>

                                <div class="dropdown-menu mega-dropdown-menu dropdown-mega-menu-xl" aria-labelledby="topnav-ui">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <a class="dropdown-item" href="ui-buttons.html">Buttons</a>
                                            <a class="dropdown-item" href="ui-cards.html">Cards</a>
                                            <a class="dropdown-item" href="ui-avatars.html">Avatars</a>
                                            <a class="dropdown-item" href="ui-portlets.html">Portlets</a>
                                            <a class="dropdown-item" href="ui-tabs-accordions.html">Tabs &amp; Accordions</a>
                                            <a class="dropdown-item" href="ui-modal.html">Modals</a>
                                            <a class="dropdown-item" href="ui-progress.html">Progress</a>
                                        </div>
                                        <div class="col-lg-4">
                                            <a class="dropdown-item" href="ui-notifications.html">Notifications</a>
                                            <a class="dropdown-item" href="ui-spinners.html">Spinners</a>
                                            <a class="dropdown-item" href="ui-images.html">Images</a>
                                            <a class="dropdown-item" href="ui-carousel.html">Carousel</a>
                                            <a class="dropdown-item" href="ui-list-group.html">List Group</a>
                                            <a class="dropdown-item" href="ui-video.html">Embed Video</a>
                                        </div>
                                        <div class="col-lg-4">
                                            <a class="dropdown-item" href="ui-dropdowns.html">Dropdowns</a>
                                            <a class="dropdown-item" href="ui-ribbons.html">Ribbons</a>
                                            <a class="dropdown-item" href="ui-tooltips-popovers.html">Tooltips &amp; Popovers</a>
                                            <a class="dropdown-item" href="ui-general.html">General UI</a>
                                            <a class="dropdown-item" href="ui-typography.html">Typography</a>
                                            <a class="dropdown-item" href="ui-grid.html">Grid</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="topnav-component" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bx-layer mr-1"></i> Component
                                    <i class="bx bx-chevron-down"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-components">
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                            <i class="bx bxs-layer-plus mr-1"></i>
                                            <span> Advanced UI</span>
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-advanced UI">
                                            <a class="dropdown-item" href="advanced-confirmation-box.html"> Confirmation Box </a>
                                            <a class="dropdown-item" href="advanced-bootstrap-tour.html"> Bootstrap Tour </a>
                                            <a class="dropdown-item" href="advanced-dragula.html"> Dragula </a>
                                            <a class="dropdown-item" href="advanced-loading-buttons.html"> Loading Buttons </a>
                                            <a class="dropdown-item" href="advanced-nestable.html"> nestable </a>
                                            <a class="dropdown-item" href="advanced-range-slider.html"> Range Slider </a>
                                            <a class="dropdown-item" href="advanced-scrollspy.html"> Scroll Spy </a>
                                            <a class="dropdown-item" href="advanced-sweet-alert.html"> Sweet Alert </a>
                                            <a class="dropdown-item" href="advanced-tour.html"> Hopscotch Tour </a>
                                            <a class="dropdown-item" href="advanced-rating.html"> Rating </a>
                                            <a class="dropdown-item" href="advanced-alertify.html"> Alertify </a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                            <i class="bx bxs-eraser mr-1"></i>
                                            <span> Forms </span>
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-forms">
                                            <a class="dropdown-item" href="forms-elements.html"> General Elements </a>
                                            <a class="dropdown-item" href="forms-advanced.html"> Advanced </a>
                                            <a class="dropdown-item" href="forms-validation.html">Validation </a>
                                            <a class="dropdown-item" href="forms-pickers.html">Pickers </a>
                                            <a class="dropdown-item" href="forms-ckeditors.html"> CK Editors </a>
                                            <a class="dropdown-item" href="forms-quilljs.html">Quill Editor </a>
                                            <a class="dropdown-item" href="forms-summernote.html">Summernote </a>
                                            <a class="dropdown-item" href="forms-file-uploads.html"> File Uploads </a>
                                            <a class="dropdown-item" href="forms-masks.html"> Form Masks </a>
                                            <a class="dropdown-item" href="forms-wizards.html">Wizard</a>
                                            <a class="dropdown-item" href="forms-xeditable.html">X-Editable </a>
                                            <a class="dropdown-item" href="forms-image-crop.html"> Image Cropper </a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                            <i class="bx bx-table mr-1"></i>
                                            <span> Tables </span>
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-tables">
                                            <a class="dropdown-item" href="table-basic.html"> Basic Table </a>
                                            <a class="dropdown-item" href="table-bootstrap.html"> Bootstrap Table </a>
                                            <a class="dropdown-item" href="table-datatables.html"> Datatables Table </a>
                                            <a class="dropdown-item" href="table-editable.html"> Editable Table </a>
                                            <a class="dropdown-item" href="table-footables.html"> Footable Table </a>
                                            <a class="dropdown-item" href="table-responsive.html"> Responsive Table </a>
                                            <a class="dropdown-item" href="table-tablesaw.html"> Tablesaw Table </a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                            <i class="bx bxs-bar-chart-alt-2 mr-1"></i>
                                            <span> Charts</span>
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-Charts">
                                            <a class="dropdown-item" href="charts-apex.html"> Apex </a>
                                            <a class="dropdown-item" href="charts-c3.html"> C3 </a>
                                            <a class="dropdown-item" href="charts-chartist.html">Chartist </a>
                                            <a class="dropdown-item" href="charts-chartjs.html"> Chart JS </a>
                                            <a class="dropdown-item" href="charts-flot.html"> Flot </a>
                                            <a class="dropdown-item" href="charts-knob.html"> Knob </a>
                                            <a class="dropdown-item" href="charts-morris.html"> Morris </a>
                                            <a class="dropdown-item" href="charts-peity.html"> Peity </a>
                                            <a class="dropdown-item" href="charts-sparklines.html"> Sparklines </a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
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
                                    </div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                            <i class="bx bx-map mr-1"></i>
                                            <span> Map </span>
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-contact">
                                            <a class="dropdown-item" href="maps-vector.html"> Vector Map </a>
                                            <a class="dropdown-item" href="maps-google.html"> Google Map </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="topnav-component" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bx-cube-alt mr-1"></i> Pages
                                    <i class="bx bx-chevron-down"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-components">
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle" aria-expanded="false">
                                            <span> Auth Style 1</span>
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
                                            <span> Auth Style 2 </span>
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
                                            <span> Auth Style 3 </span>
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
                                            <span> Extra Auth </span>
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
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Left Navigation End -->
        <!-- Begin main content -->
        <div class="main-content">
            <!-- content -->
            <div class="page-content">
                <!-- page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">MultiPurpose</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Welcome to Admin Dashboard</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6">
                                <form class="form-inline justify-content-sm-end">
                                    <div class="d-inline-flex mr-2 input-date input-date-sm">
                                        <input class="form-control form-control-sm" type="text" id="dashdaterange" placeholder="03-10-19 To 04-06-20">
                                        <div class="date-icon">
                                            <i class="bx bx-calendar fs-sm"></i>
                                        </div>
                                    </div>
                                    <div class="btn-group dropdown">
                                        <button type="button" data-toggle="dropdown" class="btn btn-primary dropdown-toggle">
                                            <i class="bx bx-download mr-1"></i> Download <i class="bx bx-chevron-down"></i>
                                        </button>
                                        <div class="dropdown-menu-right dropdown-menu">
                                            <a href="javascript: void(0);" class="dropdown-item">
                                                <i class="bx bx-mail-send mr-1"></i> Email
                                            </a>
                                            <a href="javascript: void(0);" class="dropdown-item">
                                                <i class="bx bx-printer mr-1"></i> Print
                                            </a>
                                            <a href="javascript: void(0);" class="dropdown-item">
                                                <i class="bx bx-file mr-1"></i> Re-Generate
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Widget  -->
                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">today
                                                    revenue</span>
                                                <h2 class="mb-0 mt-1">5000</h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-rev"></div>
                                                <span class="text-success font-weight-bold font-size-13">
                                                    <i class="bx bx-up-arrow-alt"></i> 10.21%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">today
                                                    orders</span>
                                                <h2 class="mb-0 mt-1">2000</h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-order"></div>
                                                <span class="text-danger font-weight-bold font-size-13">
                                                    <i class="bx bx-down-arrow-alt"></i> 5.05%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">new
                                                    users</span>
                                                <h2 class="mb-0 mt-1">800</h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-user"></div>
                                                <span class="text-success font-weight-bold font-size-13">
                                                    <i class="bx bx-up-arrow-alt"></i> 25.21%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">new
                                                    visitors</span>
                                                <h2 class="mb-0 mt-1">1500</h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-visitor"></div>
                                                <span class="text-danger font-weight-bold font-size-13">
                                                    <i class="bx bx-down-arrow-alt"></i> 5.16%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row 2-->
                        <div class="row align-items-stretch">
                            <div class="col-md-4 col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Sales Status</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <p class="text-muted mb-2">Number of Sales</p>
                                                        <h4 class="mb-0">1,625</h4>
                                                    </div>
                                                    <div class="avatar avatar-md bg-info mr-0 align-self-center">
                                                        <i class="bx bx-layer fs-lg"></i>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <p class="text-muted mb-2">Sales Revenue </p>
                                                        <h4 class="mb-0">$ 42,235</h4>
                                                    </div>
                                                    <div class="avatar avatar-md bg-primary mr-0 align-self-center">
                                                        <i class="bx bx-bar-chart-alt fs-lg"></i>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <p class="text-muted mb-2">Product Sold</p>
                                                        <h4 class="mb-0">8,235</h4>
                                                    </div>
                                                    <div class="avatar avatar-md bg-success mr-0 align-self-center">
                                                        <i class="bx bx-chart fs-lg"></i>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- Begin total revenue chart -->
                            <div class="col-md-8 col-lg-9">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Statistics</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <div id="stats-chart"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- End total revenue chart -->
                        </div>
                        <!-- Row 3-->
                        <div class="row">
                            <!-- Begin recent orders -->
                            <div class="col-12 col-lg-8">
                                <div class="card">
                                    <div class="card-header dflex-between-center">
                                        <h5 class="card-title">Recent Orders</h5>
                                        <div class="export-fnc">
                                            <button class="btn btn-primary btn-sm mr-3 ml-1" data-effect="wave">
                                                <i class="bx bx-export"></i> Export
                                            </button>
                                            <div class="arrow-pagination">
                                                <ul class="pagination mb-0">
                                                    <li class="page-item disabled"><a class="page-link" data-effect="wave" href="javascript:void(0)"><i class="bx bx-chevron-left"></i></a></li>
                                                    <li class="page-item"><a class="page-link" data-effect="wave" href="javascript:void(0)"><i class="bx bx-chevron-right"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-nowrap mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Product</th>
                                                        <th>Customer</th>
                                                        <th>Price</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>#1</td>
                                                        <td>Bicycle</td>
                                                        <td>Otto B</td>
                                                        <td>$124</td>
                                                        <td><span class="badge py-1 badge-soft-danger">Declined</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>#2</td>
                                                        <td>Addidas Shoes</td>
                                                        <td>Danny Johnson</td>
                                                        <td>$100</td>
                                                        <td><span class="badge py-1 badge-soft-warning">Pending</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>#3</td>
                                                        <td>Cut Sleeve Jacket</td>
                                                        <td>Alvin Newton</td>
                                                        <td>$50</td>
                                                        <td><span class="badge py-1 badge-soft-success">Delivered</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>#4</td>
                                                        <td>Half Shirt</td>
                                                        <td>Bennie Perez</td>
                                                        <td>$80</td>
                                                        <td><span class="badge py-1 badge-soft-success">Delivered</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End recent orders -->
                            <!-- Begin quarter sale -->
                            <div class="col-12 col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Quartly Sale</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <div id="quartly-sale"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- End quarter sale -->
                        </div>
                        <!-- Row 4-->
                        <div class="row">
                            <!-- Begin total sales chart -->
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Total Sales</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <div class="chart">
                                                <canvas id="total-sale"></canvas>
                                            </div>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Total Sale </p>
                                                <h4 class="mb-0 font-weight-bold">130</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End total sales chart -->
                            <!-- Begin earning chart -->
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header dflex-between-center">
                                        <h5 class="card-title">Earning Statastics</h5>
                                        <div class="btn-group earningTabs">
                                            <button class="btn btn-primary btn-sm" data-effect="wave" data-type="weekly">
                                                Weekly
                                            </button>
                                            <button class="btn btn-outline-primary btn-sm" data-effect="wave" data-type="monthly">
                                                Monthly
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body pt-2">
                                        <div id="sales-order"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- End earning chart -->
                            <!-- Begin today sale -->
                            <div class="col-lg-3">
                                <div class="card revenue-card">
                                    <div class="card-header bg-info">
                                        <h5 class="card-title text-white">Revenue</h5>
                                    </div>
                                    <div class="card-body bg-info position-relative">
                                        <div class="chart-container">
                                            <div class="chart h-150">
                                                <canvas id="today-revenue"></canvas>
                                            </div>
                                        </div>
                                        <div class="center-text">
                                            <p class="text-light mb-1 font-weight-600">Sale </p>
                                            <h4 class="text-white mb-0 font-weight-bold">$600</h4>
                                        </div>
                                    </div>
                                    <div class="revenue-stats p-4">
                                        <div>
                                            <p class="text-muted">Target</p>
                                            <h4>$2000</h4>
                                        </div>
                                        <div>
                                            <p class="text-muted">Current</p>
                                            <h4>$1500</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End today sale -->
                        </div>
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Inventory Stock</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>Serial</th>
                                                        <th>Code</th>
                                                        <th>Date</th>
                                                        <th>Stock</th>
                                                        <th>Stock Left</th>
                                                        <th>Status</th>
                                                        <th>Ratings</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>#1</td>
                                                        <td><strong>8765482</strong></td>
                                                        <td>November 14, 2019</td>
                                                        <td>15000</td>
                                                        <td>10000</td>
                                                        <td><span class="badge badge-soft-success">In Stock</span></td>
                                                        <td>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star-half text-warning"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>#2</td>
                                                        <td><strong>2366482</strong></td>
                                                        <td>November 15, 2019</td>
                                                        <td>15000</td>
                                                        <td>100</td>
                                                        <td><span class="badge badge-soft-danger">Out Stock</span></td>
                                                        <td>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star-half text-warning"></i></a>
                                                            <a href="#"><i class="bx bx-star text-warning"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>#3</td>
                                                        <td><strong>3557477</strong></td>
                                                        <td>November 16, 2019</td>
                                                        <td>15000</td>
                                                        <td>7000</td>
                                                        <td><span class="badge badge-soft-success">In Stock</span></td>
                                                        <td>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>#4</td>
                                                        <td><strong>8747754</strong></td>
                                                        <td>November 17, 2019</td>
                                                        <td>15000</td>
                                                        <td>8000</td>
                                                        <td><span class="badge badge-soft-success">In Stock</span></td>
                                                        <td>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star-half text-warning"></i></a>
                                                            <a href="#"><i class="bx bx-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bx-star text-warning"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>#5</td>
                                                        <td><strong>9874745</strong></td>
                                                        <td>November 18, 2019</td>
                                                        <td>15000</td>
                                                        <td>50</td>
                                                        <td><span class="badge badge-soft-danger">Out Stock</span></td>
                                                        <td>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star text-warning"></i></a>
                                                            <a href="#"><i class="bx bxs-star-half text-warning"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="my-3 d-flex justify-content-end">
                                            <ul class="pagination  flat-rounded-pagination">
                                                <li class="page-item disabled">
                                                    <a href="javascript:void(0)" class="page-link" data-effect="wave" aria-label="Previous" tabindex="-1" aria-disabled="true">
                                                        <i class="bx bx-chevron-left"></i>
                                                    </a>
                                                </li>
                                                <li class="page-item active" aria-current="page">
                                                    <a href="javascript:void(0)" class="page-link" data-effect="wave">1</a>
                                                </li>
                                                <li class="page-item" aria-current="page">
                                                    <a href="javascript:void(0)" class="page-link" data-effect="wave">2</a>
                                                </li>
                                                <li class="page-item" aria-current="page">
                                                    <a href="javascript:void(0)" class="page-link" data-effect="wave">3</a>
                                                </li>
                                                <li class="page-item">
                                                    <a href="javascript:void(0)" class="page-link" data-effect="wave" aria-label="Next">
                                                        <i class="bx bx-chevron-right"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- main content End -->
        <!-- footer -->
        <!-- footer -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 mb-1 mb-md-0">
                        <span>2020 &copy; Marvel.</span>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <span>Design and Develop By <span class="text-primary font-weight-500">Lettstart Design</span></span>
                    </div>
                </div>
            </div>
        </footer>

        <div class="setting-sidebar">
            <div class="card mb-0">
                <div class="card-header">
                    <h5 class="card-title dflex-between-center">
                        Layouts
                        <a href="javascript:void(0)"><i class="mdi mdi-close fs-sm"></i></a>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="layout">
                        <a href="index-horizontal.html">
                            <img src="/assets/images/horizontal.png" alt="Lettstart Admin" class="img-fluid" />
                            <h6 class="font-size-16">Horizontal Layout</h6>
                        </a>
                    </div>
                    <div class="layout">
                        <a href="index.html">
                            <img src="/assets/images/vertical.png" alt="Lettstart Admin" class="img-fluid" />
                            <h6 class="font-size-16">Vertical Layout</h6>
                        </a>
                    </div>
                    <div class="layout">
                        <a href="layout-dark-sidebar.html">
                            <img src="/assets/images/dark.png" alt="Lettstart Admin" class="img-fluid" />
                            <h6 class="font-size-16">Dark Sidebar</h6>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page End -->
    <!-- ================== BEGIN BASE JS ================== -->
    <script src="/assets/js/vendor.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <!-- ================== END BASE JS ================== -->

    <!-- ================== BEGIN PAGE LEVEL JS ================== -->
    <script src="/assets/libs/flatpicker/js/flatpickr.js"></script>
    <script src="/assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="/assets/libs/chartjs/js/Chart.bundle.min.js"></script>
    <script src="/assets/js/utils/colors.js"></script>
    <script src="/assets/js/pages/dashboard.init.js"></script>
    <!-- ================== END PAGE LEVEL JS ================== -->
    <!-- ================== BEGIN PAGE JS ================== -->
    <script src="/assets/js/app.js"></script>
    <!-- ================== END PAGE JS ================== -->
</body>

</html>