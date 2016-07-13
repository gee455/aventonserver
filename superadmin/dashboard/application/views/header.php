<!DOCTYPE html>
<html>

    <head>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta charset="utf-8" />
        <title>Pages - Admin Dashboard UI Kit</title>
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
        <link href="<?php echo base_url(); ?>theme/assets/plugins/codrops-stepsform/css/component.css" rel="stylesheet" type="text/css" media="screen" />
        <link class="" href="<?php echo base_url(); ?>theme/pages/css/themes/simple.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
        <!-- BEGIN Pages CSS-->
        <link href="<?php echo base_url(); ?>theme/pages/css/pages-icons.css" rel="stylesheet" type="text/css">
        <link class="main-stylesheet" href="<?php echo base_url(); ?>theme/pages/css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>theme/assets/plugins/simple-line-icons/simple-line-icons.css" rel="stylesheet" type="text/css" media="screen" />

        <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery/jquery-1.8.3.min.js" type="text/javascript"></script>


        <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery/jquery-1.8.3.min.js" type="text/javascript"></script>

        <link href="<?php echo base_url(); ?>theme/assets/plugins/mapplic/css/mapplic.css" rel="stylesheet" type="text/css" />

        <link class="main-stylesheet" href="<?php echo base_url(); ?>theme/pages/css/pages.css" rel="stylesheet" type="text/css" />
        <!--[if lte IE 9]>
        <link href="<?php echo base_url(); ?>theme/pages/css/ie9.css" rel="stylesheet" type="text/css" />
        <![endif]-->
        <!--[if lt IE 9]>
        <link href="<?php echo base_url(); ?>theme/assets/plugins/mapplic/css/mapplic-ie.css" rel="stylesheet" type="text/css" />




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
<style>
    .dropdown:hover .dropdown-menu {
        display: block;
    }
</style>
    </head>

    <body class="fixed-header">
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar" data-pages="sidebar" style="display: none;">
            <div id="appMenu" class="sidebar-overlay-slide from-top">
            </div>
            <!-- BEGIN SIDEBAR HEADER -->
            <div class="sidebar-header">
                <img src="<?php echo base_url(); ?>theme/assets/img/logo.png" alt="logo" class="brand" data-src="<?php echo base_url(); ?>theme/assets/img/logo.png" data-src-retina="<?php echo base_url(); ?>theme/assets/img/logo.png" width="93" height="25">
                <div class="sidebar-header-controls">
                    <button data-pages-toggle="#appMenu" class="btn btn-xs sidebar-slide-toggle btn-link m-l-20" type="button"><i class="fa fa-angle-down fs-16"></i></button>
                    <button data-toggle-pin="sidebar" class="btn btn-link visible-lg-inline" type="button"><i class="fa fs-12"></i></button>
                </div>
            </div>
            <!-- END SIDEBAR HEADER -->
            <!-- BEGIN SIDEBAR MENU -->
            <div class="sidebar-menu">
                <ul class="menu-items">
                    <!--                    <li class="m-t-30">
                                            <a href="<?php echo base_url(); ?>index.php/admin/loadDashbord" class="detailed">
                                                <span class="title">PROFILE</span>
                                                <span class="details">234 notifications</span>
                                            </a>
                                            <span class="icon-thumbnail bg-success"><i class="pg-home"></i></span>
                                        </li>-->
                    <li class="">
                        <a href="<?php echo base_url(); ?>index.php/admin/booking">
                            <span class="title">BOOKING</span>
                        </a>
                        <span class="icon-thumbnail"><i class="pg-bag"></i></span>
                    </li>

                </ul>
                <div class="clearfix"></div>
            </div>
            <!-- END SIDEBAR MENU -->
        </div>



        <!--for tabs we are makeing it Globle    -->

        <div class="page-content-wrapper">
            <!-- START PAGE CONTENT -->
            <div class="content" style="padding-top: 2px !important;">
                <!-- START JUMBOTRON -->
                <div class="jumbotron bg-white" data-pages="parallax">
                    <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">



                        <div class="row">






                            <div class="panel panel-transparent ">
                                <!-- Nav tabs -->
                                <!--                        <div id="circle"></div>-->



                                <div id="headerpart">
                                <div class=" pull-left sm-table" style="margin-top: -9px;">
                                    <div class="header-inner">
                                        <div class="brand inline">
                                            <!--                                        <img src="--><?php //echo base_url(); ?><!--theme/assets/img/logo.png" alt="Rlogo" class="brand" data-src="--><?php //echo base_url(); ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url(); ?><!--theme/assets/img/Rlogo.png" >-->


                                        </div>




                                    </div>
                                </div>
                                <div class=" pull-right">
                                    <!-- START User Info-->
                                    <div class="visible-lg visible-md m-t-10" id="caldw">
                                        <div class="pull-left p-r-10 p-t-10 fs-16 font-heading">
                                            <span class="semi-bold"><?php echo $this->session->userdata("first_name"); ?></span>
                                            <span class="text-master"><?php echo $this->session->userdata("last_name"); ?></span>
                                        </div>

                                        <!--                                    <a  href="--><?php //echo base_url(); ?><!--index.php/dispatch/Logout" style="z-index: 9999">Logout</a>-->
                                        <div class="btn-group">


                                            <img id="nav_user_img" data-toggle="dropdown" style="border-radius: 28px;margin-top: 4px;margin-right: 7px;cursor: pointer;" data-hover="dropdown" src="http://www.3embed.com/images/newlogo.jpg" alt="" data-src="http://www.3embed.com/images/newlogo.jpg" data-src-retina="http://www.3embed.com/images/newlogo.jpg" width="32" height="32">
                                            <ul class="dropdown-menu" style="margin-left: -135px;margin-top: 14px;background: #ffffff;width: 171px;">
                                               
                                                <li>

                                                    <center><a tabindex="-1" href="<?php echo base_url(); ?>index.php/dispatch/Logout">Logout</a></center>
                                                </li>

                                            </ul>
                                        </div>

                                    </div>
                                    <!-- END User Info-->
                                </div>
                                <ul class="nav nav-tabs nav-tabs-fillup" style="width: 92%;">
                                    <li class="dispatcher" id="apbkg">

                                        <a  href="<?php echo base_url();?>index.php/dispatch/dispather_bookingsControllers"><span>DISPATCHER BOOKINGS</span></a>


                                    </li>
                                                                <li class="phonebooking">
                                                                    <a  class="iframe" href="<?php echo base_url();?>index.php/dispatch/phonebooking_controller"><span>PHONE BOOKINGS</span></a>
                                                                </li>
                                    <li class="drivers">
                                        <a  href="<?php echo base_url();?>index.php/dispatch/DriversController"><span>DRIVERS</span></a>
                                    </li>
                                    <li class="ongoing">
                                        <a  href="<?php echo base_url();?>index.php/dispatch/Ongoing_bookings"><span>ONGOING BOOKINGS</span></a>
                                    </li>
                                    <li class="bookingH">
                                        <a  href="<?php echo base_url();?>index.php/dispatch/BookingHistoryController" style="min-width: 172px !important;"><span>BOOKING HISTORY</span></a>
                                    </li>
                                    <li class="customer">
                                        <a  href="<?php echo base_url();?>index.php/dispatch/CustomerController"><span>CUSTOMERS</span></a>
                                    </li>
                                </ul>
                                </div>

                                <div class="tab-content" style="padding: 0px;">