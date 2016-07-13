<?php require_once 'language.php'; ?>
<!DOCTYPE html>
<html>

    <head>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta charset="utf-8" />
      <link rel="shortcut icon" href="<?php echo base_url(); ?>theme/icon/admin_logo.png" />
        <title><?php echo Appname; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <link rel="apple-touch-icon" href="<?php echo base_url(); ?>theme/pages/ico/60.png">
        <link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url(); ?>theme/pages/ico/76.png">
        <link rel="apple-touch-icon" sizes="120x120" href="<?php echo base_url(); ?>theme/pages/ico/120.png">
        <link rel="apple-touch-icon" sizes="152x152" href="<?php echo base_url(); ?>theme/pages/ico/152.png">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN Vendor CSS-->
        <link href="<?php echo base_url(); ?>theme/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/bootstrap-select2/select2.css" rel="stylesheet" type="text/css" media="screen" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" type="text/css" media="screen" />
        <link href="<?php echo base_url(); ?>theme/assets/css/style.css" rel="stylesheet" type="text/css" media="screen"/>
        <!-- BEGIN Pages CSS-->
        <link rel="stylesheet" href="<?php echo base_url()?>theme/assets/css/jquery-ui.css" type="text/css" media="screen"/>
        <link href="<?php echo base_url(); ?>theme/pages/css/pages-icons.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url(); ?>theme/assets/plugins/dropzone/css/dropzone.css" rel="stylesheet" type="text/css" />
        <link class="main-stylesheet" href="<?php echo base_url(); ?>theme/pages/css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/simple-line-icons/simple-line-icons.css" rel="stylesheet" type="text/css" media="screen" />

        <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery/jquery-1.8.3.min.js" type="text/javascript"></script>
        <!--<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
        <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>-->


        <link href="<?php echo base_url(); ?>theme/assets/plugins/nvd3/nv.d3.min.css" rel="stylesheet" type="text/css" media="screen" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/mapplic/css/mapplic.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/rickshaw/rickshaw.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
        <link href="<?php echo base_url(); ?>theme/assets/plugins/jquery-metrojs/MetroJs.css" rel="stylesheet" type="text/css" media="screen" />

        <!--<link href="<?php echo base_url(); ?>theme/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">-->

        <!--[if lte IE 9]>
            <link href="pages/css/ie9.css" rel="stylesheet" type="text/css" />
        <![endif]-->

        <script type="text/javascript">
            window.onload = function () {
                // fix for windows 8
                if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
                    document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/pages/css/windows.chrome.fix.css" />'
            }
        </script>
        <style>
           .form-control{  height: 38px;
           }
           span .title{

                width: 100% !important;
            }
            .table tbody tr td {
             font-size: 12px;
            }
            strong{
                font-size: 16px;
            }
            
            .page-sidebar .sidebar-menu .menu-items > li > a {
                  font-size: 12px;
            }
           .form-control {
            background-color: aliceblue;
            }
        </style>

    </head>

    <body class="fixed-header">
        <!-- BEGIN SIDEBAR -->
        
       <?php if($pagename != "company/godsview"){?>
        <div class="page-sidebar" data-pages="sidebar">
            <div id="appMenu" class="sidebar-overlay-slide from-top">
            </div>
            <!-- BEGIN SIDEBAR HEADER -->
            <div class="sidebar-header">

<!--                <div class="sidebar-header-controls " class="pull-left" style="margin-left:-60px" >
                    <button data-pages-toggle="#appMenu" class="btn btn-xs sidebar-slide-toggle btn-link m-l-20" type="button"><i class="fa fa-angle-down fs-16"></i></button>
                    <button data-toggle-pin="sidebar" class="btn btn-link visible-lg-inline" type="button"><i class="fa fs-12"></i></button>
                    <h3 style="color:white" ><?php echo Appname; ?></h3>
                </div>-->
                <div>
                    <img   src="<?php echo base_url(); ?>theme/icon/roadyo_admin_logo.png" alt="logo" class="brand" data-src="<?php echo base_url(); ?>theme/icon/roadyo_admin_logo.png" data-src-retina="<?php echo base_url(); ?>theme/icon/roadyo_admin_logo.png" width="279" height="58" style="margin-left:-33px">
                </div>
            </div>
            <!-- END SIDEBAR HEADER -->
            <!-- BEGIN SIDEBAR MENU -->
            <div class="sidebar-menu">
                <ul class="menu-items">
                    <?php $request_uri = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>
                    <!--                    <li class="m-t-30">
                                            <a href="<?php echo base_url(); ?>index.php/admin/loadDashbord" class="detailed">
                                                <span class="title">PROFILE</span>
                                                <span class="details">234 notifications</span>
                                            </a>
                                            <span class="icon-thumbnail bg-success"><i class="pg-home"></i></span>
                                        </li>-->
                    
<!--                    <li class="startpage">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/startpage">
                            <span class="title"><?php echo NAV_START_PAGE; ?></span>
                        </a>
                        <span class="icon-thumbnail startpage_thumb"></span>
                    </li>-->
                    
                    
                    <li class="dashboard">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/Dashboard">
                            <span class="title"><?php echo NAV_DASHBOARD; ?></span>
                        </a>
                        <span class="icon-thumbnail <?php echo (base_url() . "index.php/superadmin/Dashboard" == $request_uri ? "bg-success" : ""); ?>">
                            <img src="<?php echo base_url();?>/theme/icon/dasboard_off.png" class="dashboard_thumb"></i></span>
                    </li>

                    <li class="cities">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/cities">
                            <span class="title"><?php echo NAV_CITIES; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail cities_thumb" >
                           <img src="<?php echo base_url();?>/theme/icon/cities_off.png" class="cities_thumb">
                                
                            
                        </span>
                    </li>


                    <li class="company_s">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/company_s/1">
                            <span class="title"><?php echo NAV_COMPANYS; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail company_sthumb">
                           <img src="<?php echo base_url();?>/theme/icon/companies_off.png" class="company_s"></span>
                    </li>



                    <li class="vehicle_type">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/vehicle_type">
                            <span class="title"><?php echo NAV_VEHICLETYPES; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail vehicletype_thumb">
                              <img src="<?php echo base_url();?>/theme/icon/vehicle types_off.png" class="vehicle_type"></span>
                    </li>
                    
                   

                    <li class="Vehicles">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/Vehicles/5">
                            <span class="title"><?php echo NAV_VEHICLES; ?></span>
                        </a>
                        <span class="icon-thumbnail vehicles_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/vehicele model.png" class="Vehicles">
                        </span>
                    </li>




                    <li class="Drivers">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/Drivers/my/1">
                            <span class="title"><?php echo NAV_DRIVERS; ?></span>
                        </a>
                        <span class="icon-thumbnail  driver_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/drivers_off.png" class="Drivers">
                        </span>
                    </li>

                    <li class="passengers">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/passengers/3">
                            <span class="title"><?php echo NAV_PASSENGERS; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail passengers_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/passanger_off.png" class="passengers">
                        </span>
                    </li>

                     
                     <li class="on_Going_jobs">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/onGoing_jobs">
                            <span class="title">ON GOING JOBS</span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail dispatches_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/all_booking_off.png" class="dispatches">
                        </span>
                    </li>
                    
                     <li class="completed_jobs">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/completed_jobs">
                            <span class="title">COMPLETED JOBS</span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail dispatches_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/all_booking_off.png" class="dispatches">
                        </span>
                    </li>

                    <li class="bookings">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/bookings/11">
                            <span class="title"><?php echo NAV_BOOKINGS; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail booking_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/all_booking_off.png" class="bookings">
                        </span>
                    </li>


                    <li class="dispatches">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/dispatched/1">
                            <span class="title"><?php echo NAV_DISPATCHERS; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail dispatches_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/dispatcher_off.png" class="dispatches">
                        </span>
                    </li>




                    <li class="payroll">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/payroll">
                            <span class="title"><?php echo NAV_PAYROLL; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail payroll_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/payroll_off.png" class="payroll">
                        </span>
                    </li>


                    <li class="transection">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/transection">
                            <span class="title"><?php echo NAV_ACCOUNTING; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail">
                            <img src="<?php echo base_url();?>/theme/icon/accounting_off.png" class="transection">
                        </span>
                    </li>


                    <li class="driver_review">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/driver_review/1">
                            <span class="title"><?php echo NAV_DRIVERREVIEW; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail driver_review_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/driver review_off.png" class="driver_review">
                        </span>
                    </li>


                    <li class="passenger_rating">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/passenger_rating">
                            <span class="title"><?php echo NAV_PASSENGERRATING; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail passenger_rating_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/passanger rating_off.png" class="passenger_rating">
                        </span>
                    </li>
                    
                    <li class="disputes">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/disputes/1">
                            <span class="title"><?php echo NAV_DISPUTES; ?></span>
<!--                            <span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail disputes_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/dispuite_off.png" class="disputes">
                        </span>
                    </li>


                    <li class="vehicle_models">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/vehicle_models/1">
                            <span class="title"><?php echo NAV_VEHICLEMODELS; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail vehicle_models_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/vehicele model.png" class="vehicle_models">
                        </span>
                    </li>

                    <li class="delete">
                        <a href="<?php echo base_url(); ?>index.php/superadmin/delete">
                            <span class="title"><?php echo NAV_DELETE; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail delete_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/delete_off.png" class="delete">
                        </span>
                    </li>

                    
                    
                    


                </ul>
                <div class="clearfix"></div>
            </div>
            <!-- END SIDEBAR MENU -->
        </div>
       <?php } ?>















