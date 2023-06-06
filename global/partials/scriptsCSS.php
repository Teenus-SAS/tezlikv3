<!-- ================== BEGIN PAGE LEVEL CSS START ================== -->
<link rel="stylesheet" href="/assets/css/icons.css" />
<link rel="stylesheet" href="/assets/libs/wave-effect/css/waves.min.css" />
<link rel="stylesheet" href="/assets/libs/owl-carousel/css/owl.carousel.min.css" />
<!-- ================== BEGIN PAGE LEVEL END ================== -->
<!-- ================== Plugins CSS  ================== -->
<link rel="stylesheet" href="/assets/libs/flatpicker/css/flatpickr.min.css">
<!-- ================== Plugins CSS ================== -->
<!-- ================== BEGIN APP CSS  ================== -->
<link rel="stylesheet" href="/assets/css/bootstrap.css" />
<link rel="stylesheet" href="/assets/css/print.css" />
<link rel="stylesheet" href="/assets/css/styles.css" />
<link rel="stylesheet" href="/assets/css/personalizestyle.css" />
<!-- ================== END APP CSS ================== -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css">
<!-- ================== BEGIN POLYFILLS  ================== -->
<!--[if lt IE 9]>
      <script src="assets/libs/html5shiv/js/html5shiv.js"></script>
      <script src="assets/libs/respondjs/js/respond.min.js"></script>
   <![endif]-->
<!-- ================== END POLYFILLS  ================== -->

<!-- notifications -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet">

<!-- Datapicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" integrity="sha512-3JRrEUwaCkFUBLK1N8HehwQgu8e23jTH4np5NHOmQOobuC4ROQxFwFgBLTnhcnQRMs84muMh0PnnwXlPq5MGjg==" crossorigin="anonymous" />

<!-- RowReorder -->
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/ui/trumbowyg.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

<!-- Image -->
<style type="text/css">
   /*Profile Pic Start*/
   .picture-container {
      position: relative;
      cursor: pointer;
      text-align: center;
   }

   .picture {
      width: 106px;
      height: 106px;
      background-color: #999999;
      border: 4px solid #CCCCCC;
      color: #FFFFFF;
      border-radius: 50%;
      margin: 0px auto;
      overflow: hidden;
      transition: all 0.2s;
      -webkit-transition: all 0.2s;
   }

   .pictureC {
      width: 250px;
      height: 106px;
      background-color: #999999;
      border: 4px solid #CCCCCC;
      color: #FFFFFF;
      margin: 0px auto;
      overflow: hidden;
      transition: all 0.2s;
      -webkit-transition: all 0.2s;
   }

   .picture:hover {
      border-color: #2ca8ff;
   }

   .content.ct-wizard-green .picture:hover {
      border-color: #05ae0e;
   }

   .content.ct-wizard-blue .picture:hover {
      border-color: #3472f7;
   }

   .content.ct-wizard-orange .picture:hover {
      border-color: #ff9500;
   }

   .content.ct-wizard-red .picture:hover {
      border-color: #ff3b30;
   }

   .picture input[type="file"] {
      cursor: pointer;
      display: block;
      height: 100%;
      left: 0;
      opacity: 0 !important;
      position: absolute;
      top: 0;
      width: 100%;
   }

   .pictureC input[type="file"] {
      cursor: pointer;
      display: block;
      height: 100%;
      left: 0;
      opacity: 0 !important;
      position: absolute;
      top: 0;
      width: 100%;
   }

   .picture-src {
      width: 100%;

   }

   /*Profile Pic End*/
</style>

<!-- Navbar submenu -->
<style type="text/css">
   .dropdown-submenu {
      position: relative;
   }

   .dropdown-submenu .dropdown-menu1 {
      top: 10%;
      left: 100%;
      margin-top: -1px;
   }

   .navbar-nav li:hover>ul.dropdown-menu1 {
      display: block;
   }
</style>

<!-- Loading screen -->
<style type="text/css">
   .loading {
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: 99999;
      background: #fff;
      top: 0;
      left: 0;
      display: none;
   }

   .loader {
      left: 50%;
      margin-left: -4em;
      font-size: 10px;
      border: .8em solid rgba(218, 219, 223, 1);
      border-left: .8em solid rgba(58, 166, 165, 1);
      animation: spin 1.1s infinite linear;
   }

   .loader,
   .loader:after {
      border-radius: 50%;
      width: 8em;
      height: 8em;
      display: block;
      position: absolute;
      top: 50%;
      margin-top: -4.05em;
   }

   @keyframes spin {
      0% {
         transform: rotate(360deg);
      }

      100% {
         transform: rotate(0deg);
      }
   }

   .disable-select {
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
   }
</style>