<?php
error_reporting(0);
$rupee = "$";
$dataArr = array();
$dataArr[] = array('Time Period', 'Total');
$dataArr[] = array('Today', $todaybooking['today']);
$dataArr[] = array('This Week', $todaybooking['week']);
$dataArr[] = array('This Month', $todaybooking['month']);
$dataArr[] = array('LifeTime', $todaybooking['lifetime']);

$dataArrearning = array();
$dataArrearning[] = array('Time Period', 'Total');
$dataArrearning[] = array('Today', $todaybooking['todayearning'] > 0 ? $todaybooking['todayearning'] : 0 );
$dataArrearning[] = array('This Week', $todaybooking['weekearning'] > 0 ? $todaybooking['weekearning'] : 0 );
$dataArrearning[] = array('This Month', $todaybooking['monthearning'] > 0 ? $todaybooking['monthearning'] : 0 );
$dataArrearning[] = array('LifeTime', $todaybooking['lifetimeearning'] > 0 ? $todaybooking['lifetimeearning'] : 0 );

?>
<style>
    .ui-autocomplete{
        z-index: 5000;
    }
    #selectedcity,#companyid{
        display: none;
    }
    
    .ui-menu-item{cursor: pointer;background: black;color:white;border-bottom: 1px solid white;width: 200px;}
</style>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1.1", {packages: ["bar"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        $('.dashboard_thumb').attr('src',"<?php echo base_url();?>/theme/icon/dasboard_on.png");

        var data = google.visualization.arrayToDataTable(<?php echo json_encode($dataArr)?>);
        var dataearning = google.visualization.arrayToDataTable(<?php echo json_encode($dataArrearning)?>);

        var chart = new google.charts.Bar(document.getElementById('AppUsersChart'));
        var chartearning = new google.charts.Bar(document.getElementById('AppUsersChartearning'));

        chart.draw(data);
        chartearning.draw(dataearning);
    }
</script>
<style>
    .panel-controls{
        display: none;
    }
</style>
<div class="page-content-wrapper">
<!-- START PAGE CONTENT -->
<div class="content">
<!-- START JUMBOTRON -->
<div class="jumbotron bg-white" data-pages="parallax">
<div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">
<div class="inner">
    <!-- START BREADCRUMB -->
    
        <div class="brand inline" style="  width: auto;
             font-size: 20px;
             color: gray;
             margin-left: 30px;padding-top: 20px;">
           <!--                    <img src="--><?php //echo base_url();               ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();               ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();               ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

            <strong style="color:#0090d9;">DASHBOARD</strong><!-- id="define_page"-->
        </div>

    <h3>Total Completed Session. </h3>
    <!-- END BREADCRUMB -->
</div>


<div class="row">
    <div class="col-md-3">
        <div class="widget-9 panel no-border bg-primary no-margin widget-loader-bar">
            <div class="container-xs-height full-height">
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="panel-heading  top-left top-right">
                            <div class="panel-title text-black">
                                <!--                                                <span class="font-montserrat fs-11 all-caps">Weekly Sales <i class="fa fa-chevron-right"></i>-->
                                </span>
                            </div>
                            <div class="panel-controls">
                                <ul>
                                    <li><a href="#" class="portlet-refresh text-black" data-toggle="refresh"><i class="portlet-icon portlet-icon-refresh"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="p-l-20">
                            <h3 class="no-margin p-b-5 text-white">Today</h3>
                            <!--                                            <a href="#" class="btn-circle-arrow text-white"><i class="pg-arrow_minimize"></i>-->
                            <!--                                            </a>-->

                            <div style="font-size: 42px;margin-left: 10%;">
                                <?php echo $todaybooking['today'];?>

                            </div>
                            <!--                                            <span class="label  font-montserrat m-r-5">--><?php //echo $todaybooking['today'];?><!--</span>-->
                        </div>

                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-bottom">
                        <div class="progress progress-small m-b-20">
                            <!-- START BOOTSTRAP PROGRESS (http://getbootstrap.com/components/#progress) -->
                            <div class="progress-bar progress-bar-white" data-percentage="<?php echo ($todaybooking['today']/$todaybooking['total']) * 100 ?>%" style="width: 45%;"></div>
                            <!-- END BOOTSTRAP PROGRESS -->
                        </div>
                    </div>
                </div>
            </div>
            <img src="pages/img/progress/progress-bar-master.svg" style="display:none"></div>
    </div>
    <div class="col-md-3">
        <div class="widget-9 panel no-border bg-success no-margin widget-loader-bar">
            <div class="container-xs-height full-height">
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="panel-heading  top-left top-right">
                            <div class="panel-title text-black">
                                <!--                                                <span class="font-montserrat fs-11 all-caps">Weekly Sales <i class="fa fa-chevron-right"></i>-->
                                </span>
                            </div>
                            <div class="panel-controls">
                                <ul>
                                    <li><a href="#" class="portlet-refresh text-black" data-toggle="refresh"><i class="portlet-icon portlet-icon-refresh"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="p-l-20">
                            <h3 class="no-margin p-b-5 text-white">This week</h3>
                            <!--                                            <a href="#" class="btn-circle-arrow text-white"><i class="pg-arrow_minimize"></i>-->
                            <!--                                            </a>-->

                            <div style="font-size: 42px;margin-left: 10%;">
                                <?php echo $todaybooking['week'];?>

                            </div>
                            <!--                                            <span class="label  font-montserrat m-r-5">--><?php //echo $todaybooking['today'];?><!--</span>-->
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-bottom">
                        <div class="progress progress-small m-b-20">
                            <!-- START BOOTSTRAP PROGRESS (http://getbootstrap.com/components/#progress) -->
                            <div class="progress-bar progress-bar-white" data-percentage="<?php echo ($todaybooking['week']/$todaybooking['total']) * 100 ?>%" style="width: 45%;"></div>
                            <!-- END BOOTSTRAP PROGRESS -->
                        </div>
                    </div>
                </div>
            </div>
            <img src="pages/img/progress/progress-bar-master.svg" style="display:none"></div>
    </div>
    <div class="col-md-3">
        <div class="widget-9 panel no-border bg-primary no-margin widget-loader-bar">
            <div class="container-xs-height full-height">
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="panel-heading  top-left top-right">
                            <div class="panel-title text-black">
                                <!--                                                <span class="font-montserrat fs-11 all-caps">Weekly Sales <i class="fa fa-chevron-right"></i>-->
                                </span>
                            </div>
                            <div class="panel-controls">
                                <ul>
                                    <li><a href="#" class="portlet-refresh text-black" data-toggle="refresh"><i class="portlet-icon portlet-icon-refresh"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="p-l-20">
                            <h3 class="no-margin p-b-5 text-white">This Month</h3>
                            <!--                                            <a href="#" class="btn-circle-arrow text-white"><i class="pg-arrow_minimize"></i>-->
                            <!--                                            </a>-->

                            <div style="font-size: 42px;margin-left: 10%;">
                                <?php echo $todaybooking['month'];?>

                            </div>
                            <!--                                            <span class="label  font-montserrat m-r-5">--><?php //echo $todaybooking['today'];?><!--</span>-->
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-bottom">
                        <div class="progress progress-small m-b-20">
                            <!-- START BOOTSTRAP PROGRESS (http://getbootstrap.com/components/#progress) -->
                            <div class="progress-bar progress-bar-white" data-percentage="<?php echo ($todaybooking['month']/$todaybooking['total']) * 100 ?>%" style="width: 45%;"></div>
                            <!-- END BOOTSTRAP PROGRESS -->
                        </div>
                    </div>
                </div>
            </div>
            <img src="pages/img/progress/progress-bar-master.svg" style="display:none"></div>
    </div>
    <div class="col-md-3">
        <div class="widget-9 panel no-border btn-complete no-margin widget-loader-bar">
            <div class="container-xs-height full-height">
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="panel-heading  top-left top-right">
                            <div class="panel-title text-black">
                                <!--                                                <span class="font-montserrat fs-11 all-caps">Weekly Sales <i class="fa fa-chevron-right"></i>-->
                                </span>
                            </div>
                            <div class="panel-controls">
                                <ul>
                                    <li><a href="#" class="portlet-refresh text-black" data-toggle="refresh"><i class="portlet-icon portlet-icon-refresh"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="p-l-20">
                            <h3 class="no-margin p-b-5 text-white">LifeTime</h3>
                            <!--                                            <a href="#" class="btn-circle-arrow text-white"><i class="pg-arrow_minimize"></i>-->
                            <!--                                            </a>-->

                            <div style="font-size: 42px;margin-left: 10%;">
                                <?php echo $todaybooking['lifetime'];?>

                            </div>
                            <!--                                            <span class="label  font-montserrat m-r-5">--><?php //echo $todaybooking['today'];?><!--</span>-->
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-bottom">
                        <div class="progress progress-small m-b-20">
                            <!-- START BOOTSTRAP PROGRESS (http://getbootstrap.com/components/#progress) -->
                            <div class="progress-bar progress-bar-white" data-percentage="<?php echo ($todaybooking['lifetime']/$todaybooking['total']) * 100 ?>%" style="width: 45%;"></div>
                            <!-- END BOOTSTRAP PROGRESS -->
                        </div>
                    </div>
                </div>
            </div>
            <img src="pages/img/progress/progress-bar-master.svg" style="display:none"></div>
    </div>
</div>




</div>
<!--

<div class="container-fluid container-fixed-lg bg-white">
     START PANEL 
    <div class="panel panel-transparent">
        <div class="panel-heading ">
            <div class="panel-title">
            </div>
            <div class="panel-controls">
                <ul>
                    <li><a href="#" class="portlet-collapse" data-toggle="collapse"><i class="pg-arrow_maximize"></i></a>
                    </li>
                    <li><a href="#" class="portlet-refresh" data-toggle="refresh"><i class="pg-refresh_new"></i></a>
                    </li>
                    <li><a href="#" class="portlet-close" data-toggle="close"><i class="pg-close"></i></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="panel-body">

            <div class="tab-content no-padding bg-transparent">
                <div id="AppUsersChart"></div>
            </div>
        </div>

    </div>



     END PANEL 
</div>-->






</div>


<div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">


<!--<div class="inner">


    <h3>Total Earnings. </h3>
     END BREADCRUMB 
</div>-->
<!--<div class="row">
    <div class="col-md-3">
        <div class="widget-9 panel no-border bg-primary no-margin widget-loader-bar">
            <div class="container-xs-height full-height">
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="panel-heading  top-left top-right">
                            <div class="panel-title text-black">
                                                                                <span class="font-montserrat fs-11 all-caps">Weekly Sales <i class="fa fa-chevron-right"></i>
                                </span>
                            </div>
                            <div class="panel-controls">
                                <ul>
                                    <li><a href="#" class="portlet-refresh text-black" data-toggle="refresh"><i class="portlet-icon portlet-icon-refresh"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="p-l-20">
                            <h3 class="no-margin p-b-5 text-white">Today</h3>
                                                                        <a href="#" class="btn-circle-arrow text-white"><i class="pg-arrow_minimize"></i>
                                                                        </a>

                            <div style="font-size: 42px;margin-left: 10%;">
                                <?php echo $rupee; if($todaybooking['todayearning'] >=0) echo number_format((float)$todaybooking['todayearning'], 2, '.', '') ;else echo "0";?>

                            </div>
                                                                        <span class="label  font-montserrat m-r-5"><?php //echo $todaybooking['today'];?></span>
                        </div>

                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-bottom">
                        <div class="progress progress-small m-b-20">
                             START BOOTSTRAP PROGRESS (http://getbootstrap.com/components/#progress) 
                            <div class="progress-bar progress-bar-white" data-percentage="<?php

                            echo ($todaybooking['todayearning']/$todaybooking['totalearning']) * 100;

                            ?>" style="width: 45%;"></div>
                             END BOOTSTRAP PROGRESS 
                        </div>
                    </div>
                </div>
            </div>
            <img src="pages/img/progress/progress-bar-master.svg" style="display:none"></div>
    </div>
    <div class="col-md-3">
        <div class="widget-9 panel no-border bg-success no-margin widget-loader-bar">
            <div class="container-xs-height full-height">
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="panel-heading  top-left top-right">
                            <div class="panel-title text-black">
                                                                                <span class="font-montserrat fs-11 all-caps">Weekly Sales <i class="fa fa-chevron-right"></i>
                                </span>
                            </div>
                            <div class="panel-controls">
                                <ul>
                                    <li><a href="#" class="portlet-refresh text-black" data-toggle="refresh"><i class="portlet-icon portlet-icon-refresh"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="p-l-20">
                            <h3 class="no-margin p-b-5 text-white">This week</h3>
                                                                        <a href="#" class="btn-circle-arrow text-white"><i class="pg-arrow_minimize"></i>
                                                                        </a>

                            <div style="font-size: 42px;margin-left: 10%;">
                                <?php echo $rupee; if($todaybooking['weekearning'] >=0) echo number_format((float)$todaybooking['weekearning'], 2, '.', '') ;else echo "0";s?>

                            </div>
                                                                        <span class="label  font-montserrat m-r-5"><?php //echo $todaybooking['today'];?></span>
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-bottom">
                        <div class="progress progress-small m-b-20">
                             START BOOTSTRAP PROGRESS (http://getbootstrap.com/components/#progress) 
                            <div class="progress-bar progress-bar-white" data-percentage="<?php echo ($todaybooking['weekearning']/$todaybooking['totalearning']) * 100 ?>%" style="width: 45%;"></div>
                             END BOOTSTRAP PROGRESS 
                        </div>
                    </div>
                </div>
            </div>
            <img src="pages/img/progress/progress-bar-master.svg" style="display:none"></div>
    </div>
    <div class="col-md-3">
        <div class="widget-9 panel no-border bg-primary no-margin widget-loader-bar">
            <div class="container-xs-height full-height">
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="panel-heading  top-left top-right">
                            <div class="panel-title text-black">
                                                                                <span class="font-montserrat fs-11 all-caps">Weekly Sales <i class="fa fa-chevron-right"></i>
                                </span>
                            </div>
                            <div class="panel-controls">
                                <ul>
                                    <li><a href="#" class="portlet-refresh text-black" data-toggle="refresh"><i class="portlet-icon portlet-icon-refresh"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="p-l-20">
                            <h3 class="no-margin p-b-5 text-white"> <a data-toggle="tab" href="#tab-nvd3-area">This Month</a></h3>
                                                                        <a href="#" class="btn-circle-arrow text-white"><i class="pg-arrow_minimize"></i>
                                                                        </a>

                            <div style="font-size: 42px;margin-left: 10%;">
                                <?php echo $rupee; if($todaybooking['monthearning'] >=0) echo number_format((float)$todaybooking['monthearning'], 2, '.', '');else echo "0";?>

                            </div>
                                                                        <span class="label  font-montserrat m-r-5"><?php //echo $todaybooking['today'];?></span>
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-bottom">
                        <div class="progress progress-small m-b-20">
                             START BOOTSTRAP PROGRESS (http://getbootstrap.com/components/#progress) 
                            <div class="progress-bar progress-bar-white" data-percentage="<?php echo ($todaybooking['monthearning']/$todaybooking['totalearning']) * 100 ?>%" style="width: 45%;"></div>
                             END BOOTSTRAP PROGRESS 
                        </div>
                    </div>
                </div>
            </div>
            <img src="pages/img/progress/progress-bar-master.svg" style="display:none"></div>
    </div>
    <div class="col-md-3">
        <div class="widget-9 panel no-border btn-complete no-margin widget-loader-bar">
            <div class="container-xs-height full-height">
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="panel-heading  top-left top-right">
                            <div class="panel-title text-black">
                                                                                <span class="font-montserrat fs-11 all-caps">Weekly Sales <i class="fa fa-chevron-right"></i>
                                </span>
                            </div>
                            <div class="panel-controls">
                                <ul>
                                    <li><a href="#" class="portlet-refresh text-black" data-toggle="refresh"><i class="portlet-icon portlet-icon-refresh"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-top">
                        <div class="p-l-20">
                            <h3 class="no-margin p-b-5 text-white">LifeTime</h3>
                                                                        <a href="#" class="btn-circle-arrow text-white"><i class="pg-arrow_minimize"></i>
                                                                        </a>

                            <div style="font-size: 42px;margin-left: 10%;">
                                <?php echo $rupee; if($todaybooking['lifetimeearning'] >=0) echo number_format((float)$todaybooking['lifetimeearning'], 2, '.', ''); else echo "0";?>

                            </div>
                                                                        <span class="label  font-montserrat m-r-5"><?php //echo $todaybooking['today'];?></span>
                        </div>
                    </div>
                </div>
                <div class="row-xs-height">
                    <div class="col-xs-height col-bottom">
                        <div class="progress progress-small m-b-20">
                             START BOOTSTRAP PROGRESS (http://getbootstrap.com/components/#progress) 
                            <div class="progress-bar progress-bar-white" data-percentage="<?php echo ($todaybooking['lifetimeearning']/$todaybooking['totalearning']) * 100 ?>%" style="width: 45%;"></div>
                             END BOOTSTRAP PROGRESS 
                        </div>
                    </div>
                </div>
            </div>
            <img src="pages/img/progress/progress-bar-master.svg" style="display:none"></div>
    </div>
</div>-->




</div>

<div class="container-fluid container-fixed-lg bg-white">
<!--     START PANEL -->
    <div class="panel panel-transparent">
        <div class="panel-heading ">
            <div class="panel-title">
            </div>
            <div class="panel-controls">
                <ul>
                    <li><a href="#" class="portlet-collapse" data-toggle="collapse"><i class="pg-arrow_maximize"></i></a>
                    </li>
<!--                    <li><a href="#" class="portlet-refresh" data-toggle="refresh"><i class="pg-refresh_new"></i></a>
                    </li>
                    <li><a href="#" class="portlet-close" data-toggle="close"><i class="pg-close"></i></a>
                    </li>-->
                </ul>
            </div>
        </div>
        <div class="panel-body">

            <div id="AppUsersChartearning"></div>
        </div>

    </div>



<!--     END PANEL -->
</div>



<!-- END JUMBOTRON -->

<!-- START CONTAINER FLUID -->
<div class="container-fluid container-fixed-lg">
    <!-- BEGIN PlACE PAGE CONTENT HERE -->

    <!-- END PLACE PAGE CONTENT HERE -->
</div>
<!-- END CONTAINER FLUID -->

</div>
<!-- END PAGE CONTENT -->
<!-- START FOOTER -->

  <div class="container-fluid container-fixed-lg footer">
        <div class="copyright sm-text-center">
            <p class="small no-margin pull-left sm-pull-reset">
               <span class="hint-text">Copyright @ 3Embed software technologies, All rights reserved</span>
               
            </p>
           
            <div class="clearfix"></div>
        </div>
    </div>



<!-- END FOOTER -->
</div>