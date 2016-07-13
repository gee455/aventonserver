<?php
date_default_timezone_set('UTC');
$rupee = "$";
//error_reporting(0);

if ($status == 5) {
    $vehicle_status = 'New';
    $new = "active";
    echo '<style> .searchbtn{float: left;  margin-right: 63px;}.dltbtn{float: right;}</style>';
} else if ($status == 2) {
    $vehicle_status = 'Accepted';
    $accept = "active";
} else if ($status == 4) {
    $vehicle_status = 'Rejected';
    $reject = 'active';
} else if ($status == 2) {
    $vehicle_status = 'Free';
    $free = 'active';
} else if ($status == 1) {
    $active = 'active';
}
?>

<script src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>s

<!--<script>

    var map; //= new google.maps.Map(document.getElementById("googleMap"), mapProp);
    function initialize() {
        var mapProp = {
            center: new google.maps.LatLng(51.508742, -0.120850),
            zoom: 5,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

    }



</script>-->
<style>
    .select2-results {
  max-height: 192px;
    }
  
</style>
<script>

    var map; //= new google.maps.Map(document.getElementById("googleMap"), mapProp);
    var markers = [];

    function initialize() {

   
        var mapProp = {
            center: markers[0],
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        
        map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

//        markers.push(new google.maps.LatLng(31.176453, 29.973970));
//        markers.push(new google.maps.LatLng(31.175079, 29.969212));
//        markers.push(new google.maps.LatLng(31.176453, 29.965643));
//        markers.push(new google.maps.LatLng(31.179199, 29.962073));
//        markers.push(new google.maps.LatLng(31.181946, 29.958504));
//         alert('1');
//
        var latLngBounds = new google.maps.LatLngBounds();
       
        for (var i = 0; i < markers.length; i++) {
            latLngBounds.extend(markers[i]);
            // Place the marker
            if (i == 0 || i == (markers.length - 1))
                new google.maps.Marker({
                    map: map,
                    position: markers[i],
                    title: "Point " + (i + 1)
                });
        }
       
        // Creates the polyline object
        var polyline = new google.maps.Polyline({
            map: map,
            path: markers,
            strokeColor: '#0000FF',
//            strokeOpacity: 0.7,
            strokeWeight: 2
        });

        // Fit the bounds of the generated points
        map.fitBounds(latLngBounds);


    }


</script>
<script>

    $(document).ready(function () {


        $("#define_page").html("Bookings");
        $('.bookings').addClass('active');
        $('.bookings').attr('src', "<?php echo base_url(); ?>/theme/icon/all_booking_on.png");
//        $('.booking_thumb').addClass("bg-success");



        $('#searchData').click(function () {

/*
* start time cannot be empty or null
* end time cannot be empty or null
 */

            if($("#start").val() && $("#end").val())
            {
            var dateObject = $("#start").datepicker("getDate"); // get the date object
            var st = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format
            var dateObject = $("#end").datepicker("getDate"); // get the date object
            var end = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format

//            $('#createcontrollerurl').attr('href','<?php // echo base_url()  ?>//index.php/superadmin/Get_dataformdate_for_all_bookingspg/'+st+'/'+end+'/'+$(this).val()+'/'+$('#companyid').val());


            var table = $('#big_table');

            var settings = {
                "autoWidth": false,
                "sDom": "<'table-responsive't><'row'<p i>>",
                "destroy": true,
                "scrollCollapse": true,
                "iDisplayLength": 20,
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/Get_dataformdate_for_all_bookingspg/' + st + '/' + end + '/' + $('#Sortby').val() + '/' + $('#companyid').val(),
                "bJQueryUI": true,
                "sPaginationType": "full_numbers",
                "iDisplayStart ": 20,
                "oLanguage": {
                    "sProcessing": "<img src='<?php echo base_url(); ?>theme/assets/img/ajax-loader_dark.gif'>"
                },
                "fnInitComplete": function () {
                    //oTable.fnAdjustColumnSizing();
                },
                'fnServerData': function (sSource, aoData, fnCallback)
                {
                    $.ajax
                            ({
                                'dataType': 'json',
                                'type': 'POST',
                                'url': sSource,
                                'data': aoData,
                                'success': fnCallback
                            });
                }
            };

            table.dataTable(settings);

            // search box for table
            $('#search-table').keyup(function () {
                table.fnFilter($(this).val());
            });
            }
            else
            {
                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#confirmmodels');
                if (size == "mini")
                {
                    $('#modalStickUpSmall').modal('show')
                }
                else
                {
                    $('#confirmmodels').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    }
                    else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
                $("#errorboxdatas").text(<?php echo json_encode(POPUP_DRIVERS_DEACTIVAT_DATEOFBOOKING); ?>);

                $("#confirmeds").click(function () {
                    $('.close').trigger('click');
                });
            }
        });

        $('#search_by_select').change(function () {


            $('#atag').attr('href', '<?php echo base_url() ?>index.php/superadmin/search_by_select/' + $('#search_by_select').val());

            $("#callone").trigger("click");
        });



        $("#chekdel").click(function () {
            var val = [];
            $('.checkbox:checked').each(function (i) {
                val[i] = $(this).val();
            });

            if (val.length > 0) {
                if (confirm("Are you sure to Delete " + val.length + " Vehicle")) {
                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/deleteVehicles",
                        type: "POST",
                        data: {val: val},
                        dataType: 'json',
                        success: function (result) {
                            alert(result.affectedRows)

                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                            });
                        }
                    });
                }

            } else {
                alert("Please mark any one of options");
            }

        });


    });


        
    function route_map($dis) {

        var val = $dis;
        var mapval = $("#bookingid").val();
        markers = [];

        $.ajax({
            url: "<?php echo base_url('index.php/superadmin') ?>/getmap_values",
            type: "POST",
            data: {mapval: val},
            dataType: 'json',
            success: function (response) {
                alert(JSON.stringify(response));
                 $.each(response, function (index, row) {

//                            markers.push({'lat':row.latitude,'lng' : row.longitude}) 
                    markers.push(new google.maps.LatLng(row.latitude, row.longitude));
                });
               
                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#myModalmap');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show');
                }
                else {
                    $('#myModalmap').modal('show');
                    setTimeout(initialize, 300);
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }


            }
        });


    }



</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#datepicker-component').on('changeDate', function () {
            $(this).datepicker('hide');
        });



//        $("#datepicker1").datepicker({ minDate: 0});
//        var date = new Date();
//        $('#datepicker-component').datepicker({
//            startDate: date
//        });

//        var oTable = $('#big_table').dataTable( {
//            "bProcessing": true,
//            "bServerSide": true,
//            "sAjaxSource": '<?php //echo base_url();   ?>//index.php/superadmin/bookings_data_ajax',
//            "bJQueryUI": true,
//            "sPaginationType": "full_numbers",
//            "iDisplayStart ":20,
//            "oLanguage": {
//                "sProcessing": "<img src='<?php //echo base_url();   ?>//assets/images/ajax-loader_dark.gif'>"
//            },
//            "fnInitComplete": function() {
//                //oTable.fnAdjustColumnSizing();
//            },
//            'fnServerData': function(sSource, aoData, fnCallback)
//            {
//                $.ajax
//                ({
//                    'dataType': 'json',
//                    'type'    : 'POST',
//                    'url'     : sSource,
//                    'data'    : aoData,
//                    'success' : fnCallback
//                });
//            }
//        } );


        var table = $('#big_table');

        var settings = {
            "autoWidth": false,
            "sDom": "<'table-responsive't><'row'<p i>>",
//            "sPaginationType": "bootstrap",
            "destroy": true,
            "scrollCollapse": true,
//            "oLanguage": {
//                "sLengthMenu": "_MENU_ ",
//                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
//            },
            "iDisplayLength": 20,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?php echo base_url(); ?>index.php/superadmin/bookings_data_ajax/' + '<?php echo $status ?>' + '/' + $('#companyid').val(),
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "iDisplayStart ": 20,
            "oLanguage": {
                "sProcessing": "<img src='<?php echo base_url(); ?>theme/assets/img/ajax-loader_dark.gif'>"
            },
            "fnInitComplete": function () {
                //oTable.fnAdjustColumnSizing();
            },
            'fnServerData': function (sSource, aoData, fnCallback)
            {
                $.ajax
                        ({
                            'dataType': 'json',
                            'type': 'POST',
                            'url': sSource,
                            'data': aoData,
                            'success': fnCallback
                        });
            }
        };

        table.dataTable(settings);

        // search box for table
        $('#search-table').keyup(function () {
            table.fnFilter($(this).val());
        });

        $('#Sortby').change(function () {

            var table = $('#big_table');

            var settings = {
                "autoWidth": false,
                "sDom": "<'table-responsive't><'row'<p i>>",
                "destroy": true,
                "scrollCollapse": true,
                "iDisplayLength": 20,
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?php echo base_url(); ?>index.php/superadmin/bookings_data_ajax/' + $(this).val() + '/' + $('#companyid').val(),
                "bJQueryUI": true,
                "sPaginationType": "full_numbers",
                "iDisplayStart ": 20,
                "oLanguage": {
                    "sProcessing": "<img src='<?php echo base_url(); ?>theme/assets/img/ajax-loader_dark.gif'>"
                },
                "fnInitComplete": function () {
                    //oTable.fnAdjustColumnSizing();
                },
                'fnServerData': function (sSource, aoData, fnCallback)
                {
                    $.ajax
                            ({
                                'dataType': 'json',
                                'type': 'POST',
                                'url': sSource,
                                'data': aoData,
                                'success': fnCallback
                            });
                }
            };

            table.dataTable(settings);

            // search box for table
            $('#search-table').keyup(function () {
                table.fnFilter($(this).val());
            });

        });

    });


    function refreshTableOnCityChange() {

        var table = $('#big_table');
        var url = '';

        if ($('#start').val() != '' || $('#end').val() != '') {

            var dateObject = $("#start").datepicker("getDate"); // get the date object
            var st = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format
            var dateObject = $("#end").datepicker("getDate"); // get the date object
            var end = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format

            url = '<?php echo base_url() ?>index.php/superadmin/Get_dataformdate_for_all_bookingspg/' + st + '/' + end + '/' + $('#Sortby').val() + '/' + $('#companyid').val();

        } else {
            url = '<?php echo base_url(); ?>index.php/superadmin/bookings_data_ajax/' + $('#Sortby').val() + '/' + $('#companyid').val();
        }
        var settings = {
            "autoWidth": false,
            "sDom": "<'table-responsive't><'row'<p i>>",
//            "sPaginationType": "bootstrap",
            "destroy": true,
            "scrollCollapse": true,
//            "oLanguage": {
//                "sLengthMenu": "_MENU_ ",
//                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
//            },
            "iDisplayLength": 20,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": url,
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "iDisplayStart ": 20,
            "oLanguage": {
                "sProcessing": "<img src='<?php echo base_url(); ?>theme/assets/img/ajax-loader_dark.gif'>"
            },
            "fnInitComplete": function () {
                //oTable.fnAdjustColumnSizing();
            },
            'fnServerData': function (sSource, aoData, fnCallback)
            {
                $.ajax
                        ({
                            'dataType': 'json',
                            'type': 'POST',
                            'url': sSource,
                            'data': aoData,
                            'success': fnCallback
                        });
            }
        };

        table.dataTable(settings);

        // search box for table
        $('#search-table').keyup(function () {
            table.fnFilter($(this).val());
        });

    }
</script>

<style>
    .exportOptions{
        display: none;
    }
</style>
<div class="page-content-wrapper"style="padding-top: 20px">
    <!-- START PAGE CONTENT -->
    <div class="content">
 <div class="content"style="padding-top: 3px">
 <div class="brand inline" style="  width: auto;
             font-size: 20px;
             color: gray;
             margin-left: 30px;">
           <!--                    <img src="--><?php //echo base_url();       ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();       ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();       ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

            <strong style="color:#0090d9;">BOOKINGS</strong><!-- id="define_page"-->
        </div>
        <!-- START JUMBOTRON -->
        <div class="jumbotron" data-pages="parallax">
            <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">
                <!--            <div class="inner">-->
                <!--                <!-- START BREADCRUMB -->
                <!--                <ul class="breadcrumb">-->
                <!--                    <li>-->
                <!--                        <p>Company</p>-->
                <!--                    </li>-->
                <!--                    <li><a>Vehicles</a>-->
                <!--                    </li>-->
                <!--                    <li><a href="#" class="active">--><?php //echo $vehicle_status;  ?><!--</a>-->
                <!--                    </li>-->
                <!--                </ul>-->
                <!--                <!-- END BREADCRUMB -->
                <!--            </div>-->






                <div class="panel panel-transparent ">
                    <!-- Nav tabs -->
                    <!--                <ul class="nav nav-tabs nav-tabs-fillup  bg-white">-->
                    <!--                    <li class="--><?php //echo ($status ==1 ? "active": "");  ?><!--">-->
                    <!--                        <a  href="--><?php //echo base_url();   ?><!--index.php/superadmin/bookings/1"><span>DRIVER ON THE WAY</span></a>-->
                    <!--                    </li>-->
                    <!--                   <li class="--><?php //echo ($status ==2 ? "active": "");   ?><!--">-->
                    <!--                        <a  href="--><?php //echo base_url();   ?><!--index.php/superadmin/bookings/2"><span>DRIVER ARRIVED</span></a>-->
                    <!--                    </li>-->
                    <!--                     <li class="--><?php //echo ($status ==3 ? "active": "");   ?><!--">-->
                    <!--                        <a  href="--><?php //echo base_url();   ?><!--index.php/superadmin/bookings/3"><span>BOOKING STARTED</span></a>-->
                    <!--                    </li>-->
                    <!--                  -->
                    <!--                     <li class="--><?php //echo ($status ==4 ? "active": "");   ?><!--">-->
                    <!--                        <a  href="--><?php //echo base_url();   ?><!--index.php/superadmin/bookings/4"><span>BOOKING COMPLETED</span></a>-->
                    <!--                    </li>-->
                    <!--                      <li class="--><?php //echo ($status == 5 ? "active": "");   ?><!--">-->
                    <!--                        <a  href="--><?php //echo base_url();   ?><!--index.php/superadmin/bookings/5"><span>BOOKING CANCELLED</span></a>-->
                    <!--                    </li>-->
                    <!--                    -->
                    <!--                </ul>-->

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="container-fluid container-fixed-lg bg-white">
                            <!-- START PANEL -->
                            <div class="panel panel-transparent">
                                <div class="panel-heading">
                                    <!--                                --><?php //if($status == '5') {  ?>
                                    <!--                                    <div class="pull-left"><a href="--><?php //echo base_url()  ?><!--index.php/superadmin/addnewvehicle"> <button class="btn btn-primary btn-cons">ADD</button></a></div>-->
                                    <!--                                --><?php //}  ?>
                                    <div class="pull-left">
                                        <select class="full-width select2-offscreen" id="Sortby" data-init-plugin="select2" tabindex="-1" title="select" style="width: 238px;">
                                            <option value="9">BOOKING COMPLETED</option>
                                            <option value="6" >DRIVER ON THE WAY</option>
                                            <option value="7" >DRIVER ARRIVED</option>
                                            <option value="8" >BOOKING STARTED</option>
                                            <option value="4" >CANCELED BY PASSENGER</option>
                                            <option value="3" >DRIVER REJECTED</option>
                                            <option value="5" >CANCELED BY DRIVER</option>
                                            <option value="10" >TIMED OUT</option>
                                            <option value="11"  selected>  ALL   </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="" aria-required="true">

                                            <div class="input-daterange input-group" id="datepicker-component">
                                                <input type="text" class="input-sm form-control" name="start" id="start" placeholder="From">
                                                <span class="input-group-addon">to</span>
                                                <input type="text" class="input-sm form-control" name="end" id="end" placeholder="To">

                                            </div>

                                        </div>

                                    </div>
                                    <div class="col-sm-1">
                                        <div class="">
                                            <button class="btn btn-primary" type="button" id="searchData">Search</button>
                                        </div>
                                    </div>
                                    <div class="row clearfix pull-right" >


                                        <div class="col-sm-12">
                                            <div class="searchbtn" >

                                                <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="Search"> </div>
                                            </div>
                                            <!--                                        <div class="dltbtn">
                                            
                                                                                                                            <div class="pull-right"> <a href="<?php //echo base_url()  ?>index.php/superadmin/callExel/<?php //echo $stdate;  ?>/<?php //echo $enddate  ?>"> <button class="btn btn-primary" type="submit">Export</button></a></div>
                                            <?php if ($status == '5') { ?>
                                                                                                    <div class="btn-group">
                                                                                                        <button type="button" class="btn btn-success" id="chekdel"><i class="fa fa-trash-o"></i>
                                                                                                        </button>
                                                                                                    </div>
                                            <?php } ?>
                                                                                    </div>-->
                                        </div>
                                    </div>




                                </div>
                                <div class="panel-body">
                                    <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer">
                                        <div class="table-responsive">

                                            <?php
                                            $this->table->function = 'htmlspecialchars';

                                            echo $this->table->generate();
                                            ?>

                                        </div><div class="row"></div></div>
                                </div>
                            </div>
                            <!-- END PANEL -->
                        </div>
                    </div>


                </div>









            </div>


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


<div class="modal fade stick-up" id="myModalmap" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">

                <div class="modal-header">

                    <div class=" clearfix text-left">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                        </button>

                    </div>
                    <h3> <?php echo LIST_MAP; ?></h3>
                </div>


                <div class="modal-body">

                    <div id="googleMap" style="width:500px;height:380px;"></div>
                    <BR>

                    <div class="row">
                        <div class="col-sm-4" ></div>
                        <div class="col-sm-4 error-box" id="errorpass"></div>
                        <div class="col-sm-4" >
                            <button type="button" class="btn btn-primary pull-right" id="documentok" ><?php echo BUTTON_OK; ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


</div>




<div class="modal fade stick-up" id="confirmmodels" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <div class=" clearfix text-left">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                    </button>

                </div>

            </div>
            <br>
            <div class="modal-body">
                <div class="row">

                    <div class="error-box" id="errorboxdatas" style="font-size: large;text-align:center"><?php echo VEHICLEMODEL_DELETE; ?></div>

                </div>
            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4" ></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4" >
                        <button type="button" class="btn btn-primary pull-right" id="confirmeds" ><?php echo BUTTON_OK; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
