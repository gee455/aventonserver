<!DOCTYPE html>
<?php require_once 'language.php'; ?>
<html>

    <head>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta charset="utf-8" />
        <link rel="shortcut icon" href="<?php echo base_url(); ?>theme/icon/roadyo_logo.png" />
        <title>Roadyo</title>
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
         <link href="<?php echo base_url(); ?>theme/assets/cssextra/style.css" rel="stylesheet" type="text/css" media="screen"/>
       
        <!-- BEGIN Pages CSS-->
        <link rel="stylesheet" href="<?php echo base_url()?>theme/assets/css/jquery-ui.css" type="text/css" media="screen"/>
        <link href="<?php echo base_url(); ?>theme/pages/css/pages-icons.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url(); ?>theme/assets/plugins/dropzone/css/dropzone.css" rel="stylesheet" type="text/css" />
        <link class="main-stylesheet" href="<?php echo base_url(); ?>theme/pages/css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/simple-line-icons/simple-line-icons.css" rel="stylesheet" type="text/css" media="screen" />

        <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery/jquery-1.8.3.min.js" type="text/javascript"></script>



        <link href="<?php echo base_url(); ?>theme/assets/plugins/nvd3/nv.d3.min.css" rel="stylesheet" type="text/css" media="screen" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/mapplic/css/mapplic.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/rickshaw/rickshaw.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
        <link href="<?php echo base_url(); ?>theme/assets/plugins/jquery-metrojs/MetroJs.css" rel="stylesheet" type="text/css" media="screen" />

        <link href="<?php echo base_url(); ?>theme/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">

        <!--[if lte IE 9]>
            <link href="pages/css/ie9.css" rel="stylesheet" type="text/css" />
        <![endif]-->

        <script type="text/javascript">
            window.onload = function() {
                // fix for windows 8
                if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
                    document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/pages/css/windows.chrome.fix.css" />'
            }
        </script>

    </head>

    <body class="fixed-header">
        <!-- BEGIN SIDEBAR -->
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

                    
                    
                      <li class="dashboard">
                        <a href="<?php echo base_url(); ?>index.php/masteradmin/Dashboard">
                            <span class="title"><?php echo NAV_DASHBOARD; ?></span>
                        </a>
                        <span class="icon-thumbnail <?php echo (base_url() . "index.php/masteradmin/Dashboard" == $request_uri ? "bg-success" : ""); ?> dashboard_thumb" >
                            <img src="<?php echo base_url();?>/theme/icon/dasboard_off.png" class="dashboard_thumb">
                        </span>
                    </li>
                    

                    
                    
                    <li class="payroll">
                        <a href="<?php echo base_url(); ?>index.php/masteradmin/driverDetails">
                            <span class="title"><?php echo NAV_PAYROLL; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail payroll_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/payroll_off.png" class="payroll">
                        </span>
                    </li>



                    <li class="transection">
                        <a href="<?php echo base_url(); ?>index.php/masteradmin/transection">
                            <span class="title"><?php echo NAV_ACCOUNTING; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail">
                            <img src="<?php echo base_url();?>/theme/icon/accounting_off.png" class="transection">
                        </span>
                    </li>
                    
                    
                    <li class="bookings">
                        <a href="<?php echo base_url(); ?>index.php/masteradmin/bookings/11">
                            <span class="title"><?php echo NAV_BOOKINGS; ?></span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail booking_thumb">
                            <img src="<?php echo base_url();?>/theme/icon/all_booking_off.png" class="bookings">
                        </span>
                    </li>
                     <li class="bank">
                        <a href="<?php echo base_url(); ?>index.php/masteradmin/Bank">
                            <span class="title">Banking</span>
                            <!--<span class="details">Details</span>-->
                        </a>
                        <span class="icon-thumbnail"><img src="<?php echo base_url()?>/theme/icon/payroll_off.png"></span>
                    </li>
                    
<!--                   <li class="banking">
                        <a href="<?php echo base_url(); ?>index.php/masteradmin/Banking">
                            <span class="title"><?php echo NAV_BANKING; ?></span>
                            <span class="details">Details</span>
                        </a>
                        <span class="icon-thumbnail banking_pg-calender"><i class="pg-calender"></i></span>
                    </li>-->

                </ul>
                <div class="clearfix"></div>
            </div>
            <!-- END SIDEBAR MENU -->
        </div>

                  
