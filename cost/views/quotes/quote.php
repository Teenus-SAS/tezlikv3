<?php
if (!isset($_SESSION)) {
   session_start();
   if (sizeof($_SESSION) == 0)
      header('location: /');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="description" content="">
   <meta name="keywords" content="">
   <meta name="author" content="Teenus SAS">
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <title>Invoice Details | Letstart - Responsive Admin Dashboard Template</title>
   <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon" />

   <!-- ================== BEGIN PAGE LEVEL CSS START ================== -->
   <link rel="stylesheet" href="assets/css/icons.css" />
   <link rel="stylesheet" href="assets/libs/wave-effect/css/waves.min.css" />
   <link rel="stylesheet" href="assets/libs/owl-carousel/css/owl.carousel.min.css" />
   <!-- ================== BEGIN PAGE LEVEL END ================== -->
   <!-- ================== Plugins CSS  ================== -->
   <!-- ================== Plugins CSS ================== -->
   <!-- ================== BEGIN APP CSS  ================== -->
   <link rel="stylesheet" href="assets/css/bootstrap.css" />
   <link rel="stylesheet" href="assets/css/styles.css" />
   <!-- ================== END APP CSS ================== -->

   <!-- ================== BEGIN POLYFILLS  ================== -->
   <!--[if lt IE 9]>
       <script src="assets/libs/html5shiv/js/html5shiv.js"></script>
       <script src="assets/libs/respondjs/js/respond.min.js"></script>
    <![endif]-->
   <!-- ================== END POLYFILLS  ================== -->
</head>

<body>
   <!-- Begin Page -->
   <div class="page-wrapper">
      <!-- Begin Header -->
      <!-- Begin Header -->
      <header id="page-topbar" class="topbar-header">
         <div class="navbar-header">
            <div class="left-bar">
               <div class="navbar-brand-box">
                  <a href="index.html" class="logo logo-dark">
                     <span class="logo-sm"><img src="assets/images/logo-white-sm.png" alt="Lettstart Admin"></span>
                     <span class="logo-lg"><img src="assets/images/logo-white.png" alt="Lettstart Admin"></span>
                  </a>
                  <a href="index.html" class="logo logo-light">
                     <span class="logo-sm"><img src="assets/images/logo-sm.png" alt="Lettstart Admin"></span>
                     <span class="logo-lg"><img src="assets/images/logo.png" alt="Lettstart Admin"></span>
                  </a>
               </div>
               <button type="button" id="vertical-menu-btn" class="btn hamburg-icon">
                  <i class="mdi mdi-menu"></i>
               </button>
               <form class="app-search d-none d-lg-block">
                  <div class="search-box position-relative">
                     <input type="text" placeholder="Search..." class="form-control">
                     <span class="bx bx-search"></span>
                  </div>
               </form>
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
                                          <img src="assets/images/megamenu-img.svg" alt="Lettstart Admin" class="img-fluid mx-auto d-block">
                                       </div>
                                       <div class="item">
                                          <img src="assets/images/megamenu-img2.svg" alt="Lettstart Admin" class="img-fluid mx-auto d-block">
                                       </div>
                                       <div class="item">
                                          <img src="assets/images/megamenu-img3.svg" alt="Lettstart Admin" class="img-fluid mx-auto d-block">
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
                     <img src="assets/images/flags/us.svg" class="mh-16" alt="USA">
                     <span class="ml-2 d-none d-sm-inline-block">EN</span>
                  </button>
                  <div aria-labelledby="page-header-country-dropdown" id="countries" class="dropdown-menu-right dropdown-menu">
                     <a href="javascript:void(0);" class="dropdown-item">
                        <img class="mr-1 mh-12" src="assets/images/flags/us.svg" alt="USA">
                        <span class="align-middle" data-lang="en">USA</span>
                     </a>
                     <a href="javascript:void(0);" class="dropdown-item">
                        <img class="mr-1 mh-12" src="assets/images/flags/ge.svg" alt="German">
                        <span class="align-middle" data-lang="ge">German</span>
                     </a>
                     <a href="javascript:void(0);" class="dropdown-item">
                        <img class="mr-1 mh-12" src="assets/images/flags/ru.svg" alt="Russia">
                        <span class="align-middle" data-lang="ru">Russia</span>
                     </a>
                     <a href="javascript:void(0);" class="dropdown-item">
                        <img class="mr-1 mh-12" src="assets/images/flags/in.svg" alt="India">
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
                                 <img src="assets/images/brands/github.png" alt="Github">
                                 <span>GitHub</span>
                              </a>
                           </div>
                           <div class="col">
                              <a href="javascript: void(0);" class="dropdown-icon-item">
                                 <img src="assets/images/brands/bitbucket.png" alt="bitbucket">
                                 <span>Bitbucket</span>
                              </a>
                           </div>
                           <div class="col">
                              <a href="javascript: void(0);" class="dropdown-icon-item">
                                 <img src="assets/images/brands/dribbble.png" alt="dribbble">
                                 <span>Dribbble</span>
                              </a>
                           </div>
                        </div>
                        <div class="row no-gutters">
                           <div class="col">
                              <a href="javascript: void(0);" class="dropdown-icon-item">
                                 <img src="assets/images/brands/dropbox.png" alt="dropbox">
                                 <span>Dropbox</span>
                              </a>
                           </div>
                           <div class="col">
                              <a href="javascript: void(0);" class="dropdown-icon-item">
                                 <img src="assets/images/brands/mail_chimp.png" alt="mail_chimp">
                                 <span>Mail Chimp</span>
                              </a>
                           </div>
                           <div class="col">
                              <a href="javascript: void(0);" class="dropdown-icon-item">
                                 <img src="assets/images/brands/slack.png" alt="slack">
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
                                       <img alt="Lettstart Admin" class="img-fluid rounded-circle" src="assets/images/users/avatar-1.jpg">
                                    </div>
                                    <p class="media-body">
                                       John likes your photo
                                       <small class="text-muted">5 hours ago</small>
                                    </p>
                                 </div>
                              </a><a href="javascript:void(0);" class="dropdown-item notification-item">
                                 <div class="media">
                                    <div class="avatar avatar-xs">
                                       <img alt="Lettstart Admin" class="img-fluid rounded-circle" src="assets/images/users/avatar-2.jpg">
                                    </div>
                                    <p class="media-body">
                                       Johnson
                                       <small class="text-muted">Wow! admin looks good</small>
                                    </p>
                                 </div>
                              </a><a href="javascript:void(0);" class="dropdown-item notification-item">
                                 <div class="media">
                                    <div class="avatar avatar-xs bg-danger">
                                       <i class="bx bx-server"></i>
                                    </div>
                                    <p class="media-body">
                                       Server getting down
                                       <small class="text-muted">1 min ago</small>
                                    </p>
                                 </div>
                              </a><a href="javascript:void(0);" class="dropdown-item notification-item">
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
                     <img src="assets/images/users/avatar-1.jpg" alt="Header Avatar" class="avatar avatar-xs mr-0">
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
      <!-- Begin Left Navigation -->
      <aside class="side-navbar">
         <div class="scroll-content" id="metismenu">
            <ul id="side-menu" class="metismenu list-unstyled">
               <li class="side-nav-title side-nav-item menu-title">Menu</li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-home-circle"></i>
                     <span> Dashboard</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="index.html"> Multi Purpose </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="dashboard2.html"> E-commerce </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="dashboard3.html"> Server Statistics </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-layout"></i>
                     <span> Layouts</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="layout-compact-side-menu.html"> Compact Sidebar </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="layout-dark-sidebar.html"> Dark Sidebar </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="layout-icon-sidebar.html"> Icon Sidebar </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="layout-box.html"> Box Layout </a>
                     </li>
                  </ul>
               </li>
               <li class="side-nav-title side-nav-item menu-title">Apps</li>
               <li>
                  <a class="side-nav-link" href="calender.html">
                     <i class="bx bx-calendar"></i>
                     <span> Calender</span>
                  </a>
               </li>
               <li>
                  <a class="side-nav-link" href="chat.html">
                     <i class="bx bx-chat"></i>
                     <span> Chat</span>
                  </a>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bxs-user-detail"></i>
                     <span> Contacts</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="member-create.html"> Add Member </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="member-list.html"> Member List </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="member-grid.html"> Member Grid </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="member-profile.html"> Member Profile </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-store"></i>
                     <span> Ecommerce</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ecommerce-add-product.html"> Add Product </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ecommerce-product.html"> Products </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ecommerce-product-details.html"> Product Detail </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ecommerce-orders.html"> Orders </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ecommerce-customers.html"> Customers </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ecommerce-cart.html"> Cart </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ecommerce-checkout.html"> Checkout </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-envelope"></i>
                     <span> Email</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="email-inbox.html"> Inbox </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="email-read.html"> Read Mail </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="email-compose.html"> Compose Mail </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-receipt"></i>
                     <span> Invoices</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="invoice-list.html"> Invoice List </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="invoice-details.html"> Invoice Detail </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="invoice-grid.html"> Invoice Grid </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="invoice-create.html"> Generate Invoice </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-briefcase-alt-2"></i>
                     <span> Projects</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="project-list.html"> Project List </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="project-grid.html"> Project Grid </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="project-overview.html"> Project Overview </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="project-create.html"> Create New </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-task"></i>
                     <span> Tasks</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="task-list.html"> Task List </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="kanban-board.html"> Kanban Board </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="task-overview.html"> Task Overview </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="task-create.html"> Create Task </a>
                     </li>
                  </ul>
               </li>
               <li class="side-nav-title side-nav-item menu-title">Pages</li>

               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-user-circle"></i>
                     <span> Authentication</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-login.html">Login </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-login-basic.html"> Login 2 </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-login-full.html"> Login 3 </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-signup.html"> Register </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-signup-basic.html"> Register 2 </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-signup-full.html"> Register 3 </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-recover.html"> Recover Password </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-recover-basic.html"> Recover Password 2</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-recover-full.html"> Recover Password 3</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-lockscreen.html"> Lock Screen </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-confirmation.html"> Confirmation Screen </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-400.html"> 400 </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-404.html"> 404 </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="auth-500.html"> 500 </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-file"></i>
                     <span> Utility</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-animation.html"> Animation </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-activity.html"> Activity </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-coming-soon.html"> Coming Soon </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-faq.html"> FAQs </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-fix-left.html"> Fix Left Sidebar </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-fix-right.html"> Fix Right Sidebar </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-gallery.html"> Gallery </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-helperclasses.html"> Helper Classes </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-lightbox.html"> Lightbox </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-maintenance.html"> Maintenance </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-pricing.html"> Pricing </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-scrollbar.html"> Scrollbar </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-search-result.html"> Search Result </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-starterpage.html"> Starter Page </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-timeline.html"> Timeline </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-timeline-horizontal.html"> Timeline Horizontal </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="utility-treeview.html"> Tree View </a>
                     </li>
                  </ul>
               </li>
               <li class="side-nav-title side-nav-item menu-title">Components</li>
               <li><a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-tone"></i>
                     <span> UI Components</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-buttons.html">Buttons</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-cards.html">Cards</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-avatars.html">Avatars</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-portlets.html">Portlets</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-tabs-accordions.html">Tabs & Accordions</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-modal.html">Modals</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-progress.html">Progress</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-notifications.html">Notifications</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-spinners.html">Spinners</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-images.html">Images</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-carousel.html">Carousel</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-list-group.html">List Group</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-video.html">Embed Video</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-dropdowns.html">Dropdowns</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-ribbons.html">Ribbons</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-tooltips-popovers.html">Tooltips & Popovers</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-general.html">General UI</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-typography.html">Typography</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="ui-grid.html">Grid</a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-layer-plus"></i>
                     <span> Advance UI</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="advanced-confirmation-box.html"> Confirmation Box </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="advanced-bootstrap-tour.html"> Bootstrap Tour </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="advanced-dragula.html"> Dragula </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="advanced-loading-buttons.html"> Loading Buttons </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="advanced-nestable.html"> nestable </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="advanced-range-slider.html"> Range Slider </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="advanced-scrollspy.html"> Scroll Spy </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="advanced-sweet-alert.html"> Sweet Alert </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="advanced-tour.html"> Hopscotch Tour </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="advanced-rating.html"> Rating </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="advanced-alertify.html"> Alertify </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bxs-eraser"></i>
                     <span> Forms</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="forms-elements.html"> General Elements </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="forms-advanced.html"> Advanced </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="forms-validation.html">Validation </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="forms-pickers.html">Pickers </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="forms-ckeditors.html"> CK Editors </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="forms-quilljs.html">Quill Editor </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="forms-summernote.html">Summernote </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="forms-file-uploads.html"> File Uploads </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="forms-masks.html"> Form Masks </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="forms-wizards.html">Wizard</a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="forms-xeditable.html">X-Editable </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="forms-image-crop.html"> Image Cropper </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-table"></i>
                     <span> Tables</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="table-basic.html"> Basic Table </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="table-bootstrap.html"> Bootstrap Table </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="table-datatables.html"> Datatables Table </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="table-editable.html"> Editable Table </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="table-footables.html"> Footable Table </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="table-responsive.html"> Responsive Table </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="table-tablesaw.html"> Tablesaw Table </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bxs-bar-chart-alt-2"></i>
                     <span> Charts</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="charts-apex.html"> Apex </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="charts-c3.html"> C3 </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="charts-chartist.html">Chartist </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="charts-chartjs.html"> Chart JS </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="charts-flot.html"> Flot </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="charts-knob.html"> Knob </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="charts-morris.html"> Morris </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="charts-peity.html"> Peity </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="charts-sparklines.html"> Sparklines </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-aperture"></i>
                     <span> Icons</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="icons-boxicons.html"> Box Icon </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="icons-feather.html"> Feather Icon </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="icons-mdi.html"> Material Design Icons </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="icons-simple-line.html"> Simple Line Icons </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="icons-themify.html"> Themify Icons </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="icons-two-tone.html"> Two Tone Icons </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="icons-font-awesome.html"> Font Awesome 5 </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-map"></i>
                     <span> Map</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="maps-vector.html"> Vector Map </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="maps-google.html"> Google Map </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="javascript:void(0);" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-share-alt"></i>
                     <span> Multi Level</span>
                     <span class="menu-arrow"></span>
                  </a>
                  <ul aria-expanded="false" class="nav-second-level">
                     <li class="side-nav-item">
                        <a href="javascript:void(0);" class="side-nav-link-a" aria-expanded="false"> Level 1 <span class="menu-arrow"></span></a>
                        <ul aria-expanded="false" class="nav-third-level">
                           <li>
                              <a class="side-nav-link" href="javascript:void(0)"> Level 2 </a>
                           </li>
                           <li><a class="side-nav-link" href="javascript:void(0)"> Level 2 </a></li>
                        </ul>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="javascript:void(0)"> Level 1 </a>
                     </li>
                     <li class="side-nav-item">
                        <a class="side-nav-link" href="javascript:void(0)"> Level 1 </a>
                     </li>
                  </ul>
               </li>
               <li>
                  <a href="../documentation/index.html" target="_blank" class="side-nav-link" aria-expanded="false">
                     <i class="bx bx-file"></i>
                     <span> Documentation</span>
                  </a>
               </li>
            </ul>
         </div>
      </aside>
      <!-- Left Navigation End -->
      <!-- Left Navigation End -->
      <!-- Begin main content -->
      <div class="main-content">
         <!-- content -->
         <div class="page-content">
            <!-- page header -->
            <div class="page-title-box">
               <div class="container-fluid">
                  <div class="page-title dflex-between-center">
                     <h3 class="mb-1 font-weight-bold">Invoice Detail</h3>
                     <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item">
                           <a href="../index.html">
                              <i class="bx bx-home fs-xs"></i>
                           </a>
                        </li>
                        <li class="breadcrumb-item">
                           <a href="calender.html">
                              Apps
                           </a>
                        </li>
                        <li class="breadcrumb-item active">Invoice Detail</li>
                     </ol>
                  </div>
               </div>
            </div>
            <!-- page content -->
            <div class="page-content-wrapper mt--45">
               <div class="container-fluid">
                  <div class="card">
                     <div class="card-body">
                        <div class="invoice-title">
                           <h4 class="float-right font-size-16">Order # 20202044874</h4>
                           <div class="mb-4">
                              <img src="assets/images/logo.png" alt="logo" height="20">
                           </div>
                        </div>
                        <hr />
                        <div class="row">
                           <div class="col-sm-6">
                              <address>
                                 <strong>Billed To:</strong><br>
                                 John Doe<br>
                                 1234 Main Road<br>
                                 Apt. 48<br>
                                 Frankfurt, GE 54321
                              </address>
                           </div>
                           <div class="col-sm-6 text-sm-right">
                              <address class="mt-2 mt-sm-0">
                                 <strong>Shipped To:</strong><br>
                                 Mark Henry<br>
                                 1234 Main Road<br>
                                 Apt. 4B<br>
                                 Frankfurt, GE 54321
                              </address>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-6 mt-3">
                              <address>
                                 <strong>Payment Method:</strong><br>
                                 Visa ending **** 8080<br>
                                 jDoe@example.com
                              </address>
                           </div>
                           <div class="col-sm-6 mt-3 text-sm-right">
                              <address>
                                 <strong>Order Date:</strong><br>
                                 May 16, 2020<br><br>
                              </address>
                           </div>
                        </div>
                        <div class="py-2 mt-3">
                           <h3 class="font-size-15 font-weight-bold">Order summary</h3>
                        </div>
                        <div class="table-responsive">
                           <table class="table mt-4 table-centered">
                              <thead class="thead-dark">
                                 <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Hours</th>
                                    <th>Hours Rate</th>
                                    <th class="text-right">Total</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr>
                                    <td>1</td>
                                    <td>
                                       <h5 class="mb-2">Web Design</h5>
                                       <p class="text-muted mb-0">2 Pages static website - my website</p>
                                    </td>
                                    <td>22</td>
                                    <td>$30</td>
                                    <td class="text-right">$660.00</td>
                                 </tr>
                                 <tr>
                                    <td>2</td>
                                    <td>
                                       <h5 class="mb-2">Software Development</h5>
                                       <p class="text-muted mb-0">Invoice editor software -
                                          Letstart
                                          Software</p>
                                    </td>
                                    <td>112</td>
                                    <td>$35</td>
                                    <td class="text-right">$3920</td>
                                 </tr>
                                 <tr>
                                    <td>3</td>
                                    <td>
                                       <h5 class="mb-2">App Development</h5>
                                       <p class="text-muted mb-0">Invoice editor software -
                                          XZY
                                          Software</p>
                                    </td>
                                    <td>100</td>
                                    <td>$35</td>
                                    <td class="text-right">$3500</td>
                                 </tr>
                                 <tr>
                                    <td colspan="4" class="text-right">Sub Total</td>
                                    <td class="text-right">$8080.00</td>
                                 </tr>
                                 <tr>
                                    <td colspan="4" class="border-0 text-right">
                                       <strong>Shipping</strong>
                                    </td>
                                    <td class="border-0 text-right">$20.00</td>
                                 </tr>
                                 <tr>
                                    <td colspan="4" class="border-0 text-right">
                                       <strong>Total</strong>
                                    </td>
                                    <td class="border-0 text-right">
                                       <h4 class="m-0">$9000.00</h4>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                        <div class="d-print-none">
                           <div class="float-right">
                              <a href="javascript:window.print()" class="btn btn-info waves-effect waves-light mr-1"><i class="bx bxs-printer"></i> Print</a>
                              <a href="javascript:void(0)" class="btn btn-primary w-md" data-effect="wave">Send</a>
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
                     <img src="assets/images/horizontal.png" alt="Lettstart Admin" class="img-fluid" />
                     <h6 class="font-size-16">Horizontal Layout</h6>
                  </a>
               </div>
               <div class="layout">
                  <a href="index.html">
                     <img src="assets/images/vertical.png" alt="Lettstart Admin" class="img-fluid" />
                     <h6 class="font-size-16">Vertical Layout</h6>
                  </a>
               </div>
               <div class="layout">
                  <a href="layout-dark-sidebar.html">
                     <img src="assets/images/dark.png" alt="Lettstart Admin" class="img-fluid" />
                     <h6 class="font-size-16">Dark Sidebar</h6>
                  </a>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- Page End -->
   <!-- ================== BEGIN BASE JS ================== -->
   <script src="assets/js/vendor.min.js"></script>
   <!-- ================== END BASE JS ================== -->

   <!-- ================== BEGIN PAGE LEVEL JS ================== -->
   <!-- ================== END PAGE LEVEL JS ================== -->
   <!-- ================== BEGIN PAGE JS ================== -->
   <script src="assets/js/app.js"></script>
   <!-- ================== END PAGE JS ================== -->
</body>

</html>