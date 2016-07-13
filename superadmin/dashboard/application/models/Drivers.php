<?php
////
//// error_reporting(E_ALL);
//echo 'up to herer';
//exit();
//?>

<script>
$(document).ready(function(){

    $('.drivers').addClass('active');
});
</script>

<style>


   
    html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
    }
    #map-canvas {
        margin: 0;
        padding: 0;
        height: 600px;
        border: 1px solid #ccc;
        /*display: none;*/

    }
    .nav-tabs-fillup li {
        margin-top: 0px !important;
    }
    .radio label, .checkbox label {
        display: inline-block;
        cursor: pointer;
        position: relative;
        padding-left: 23px !important;
        margin-right: 7px;
        font-size: 13px;
    }
    .datepicker{z-index:1151 !important;}
    #map_canvas {display:none;}
    /*#map img {*/
    /*max-width: none;*/
    /*}*/
</style>

<link href="<?php echo base_url();?>theme/pages/css/whirly.css" rel="stylesheet" type="text/css" />
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
<!---->
<link rel="stylesheet" href="http://www.jacklmoore.com/colorbox/example3/colorbox.css" />

<script src="http://www.jacklmoore.com/colorbox/jquery.colorbox.js"></script>
<script>
    $(document).ready(function(){
        $(".iframe").colorbox({iframe:true, width:"100%", height:"100%"});
//        alert = function(){};
    });
</script>
<script>
    // Note: This example requires that you consent to location sharing when
    // prompted by your browser. If you see a blank space instead of the map, this
    // is probably because you have denied permission for location sharing.


    //
    var map;

    function initialize() {
        var mapOptions = {
            zoom: 6
        };

        map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        // Try HTML5 geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var pos = new google.maps.LatLng(position.coords.latitude,
                    position.coords.longitude);

                var infowindow = new google.maps.InfoWindow({
                    map: map,
                    position: pos,
                    content: 'Location found using HTML5.'
                });

                map.setCenter(pos);
            }, function () {
                handleNoGeolocation(true);
            });
        } else {
            // Browser doesn't support Geolocation
            handleNoGeolocation(false);
        }

    }

    function handleNoGeolocation(errorFlag) {
        if (errorFlag) {
            var content = 'Error: The Geolocation service failed.';
        } else {
            var content = 'Error: Your browser doesn\'t support geolocation.';
        }

        var options = {
            map: map,
            position: new google.maps.LatLng(60, 105),
            content: content
        };

        var infowindow = new google.maps.InfoWindow(options);
        map.setCenter(options.position);

    }

    $('#map-canvas').on('shown', function (e) {

        google.maps.event.trigger(map, 'resize');

    });
    //    google.maps.event.trigger(map, 'resize');
    google.maps.event.addDomListener(window, 'load', initialize);

    google.maps.event.addDomListener(window, 'load', initialize);
    function displayMap() {

        var lat = $('#lat').val();
        var long = $('#long').val();
        if(lat||long)
            initialize(lat,long);
        else
            initialize('13.0288555','77.58961360000001');
    }

    function initialize(lat,long) {
        var myLatlng = new google.maps.LatLng(lat,long);
        var mapOptions = {
            zoom:14,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);

        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: 'Hello World!'
        });
        google.maps.event.trigger(map, 'resize');
    }




</script>


<!--/** end of map script-->



<script type="text/javascript">
$(document).ready(function(){
    $('.whirly-loader').hide();

    var settings = {
        "sDom": "<'table-responsive't><'row'<p i>>",
        "sPaginationType": "bootstrap",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
            "sLengthMenu": "_MENU_ ",
            "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        "iDisplayLength": 11
    };

//                var reviewtable = $('#tableWithSearchforreview');
//
//                reviewtable.dataTable(settings);





    var jobstable= $('#tableWithSearchforjobs');
    jobstable.dataTable(settings);

    var tableWithSearchforreview= $('#tableWithSearchforreview');
    tableWithSearchforreview.dataTable(settings);

//    $('#loading').html("Loading...");
    $('#tableWithSearchDriverList_data').hide();

    $.ajax({

        type: "post",
        url: "get_all_drivers",
        data :{status : this.value},
        dataType: "json",
        success:function(result) {
//
//            table1
//                .clear()
//                .draw();
            $('#loading').hide();
            var table1 = $('#tableWithSearchDriverList').DataTable();

            table1
                .clear()
                .draw();

            $.each(result.html , function(index , row1){
                table1.row.add([
                    row1.data
                ]).draw();

            });
            $('#loading').hide();
            $('#tableWithSearchDriverList_data').show();

        }

    });

    $('input:radio[name="statuschk"]').change(
        function(){
            if (this.checked) {
//                alert(this.value);
                $('.whirly-loader').show();
                $.ajax({
                    type: "post",
                    url: "get_drivers_available",
                    data :{status : this.value},
                    dataType: "json",
                    success:function(result){

//                        alert(result[0].html);
//                        alert(result.user);
//                        $('#test').html(result.user);
//                        $('#dynamic_status_change1').html(result.html);
                        var table1 = $('#tableWithSearchDriverList').DataTable();
                        table1
                            .clear()
                            .draw();

                        $.each(result.html , function(index , row1){
                            table1.row.add([
                                row1.data
                            ]).draw();

                        });
                        $('.whirly-loader').hide();
                    }




                });


            }
        });



    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var lat = $('#lat').val();
        var long = $('#long').val();
        if(lat||long)
            initialize(lat,long);
        else
            initialize('13.0288555','77.58961360000001');
    });
//
//        $("#resize").click(function() {
////            alert('testing');
//        google.maps.event.trigger(map, 'resize');
//        });

    $('#userstatus').hide();

    $('.nav-tabs-fillup').click(function(){


        if($('#apbkg').hasClass('active')){
            $('#circle').hide();

        }
        else{
            $('#circle').show();

        }

//                 $('#circle').show();


    });




    var table = $('#tableWithSearchDriver');

    var settings = {
        "sDom": "<'table-responsive't><'row'<p i>>",
        "sPaginationType": "bootstrap",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
            "sLengthMenu": "_MENU_ ",
            "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        "iDisplayLength": 11
    };

    table.dataTable(settings);


    $('#search-tableDriver').keyup(function() {
        table.fnFilter($(this).val());
    });



//        var jobstable= $('#tableWithSearchforjobs');
//        jobstable.dataTable(settings);

//        var reviewtable = $('#tableWithSearchforreview');
//
//        reviewtable.dataTable(settings);

    $('#search-tableclient').keyup(function() {
        tableclient.fnFilter($(this).val());
    });

    var tableclient = $('#tableWithSearchClient');
    tableclient.dataTable(settings);

    var tablewithdriverlist = $('#tableWithSearchDriverList');

    tablewithdriverlist.dataTable(settings);

    $('.tableWithSearchDriverListsearch').keyup(function() {
        tablewithdriverlist.fnFilter($(this).val());
    });





});

//    if(typeof(EventSource) !== "undefined") {
//
//        var source = new EventSource("<?php //echo base_url()?>//serverdata/demo_sse.php");
//        source.onmessage = function(event) {
//
//
//
//                var Returneddata = $.parseJSON(event.data);
//
//                document.getElementById("circle").innerHTML = '<div id="newbkng" style="padding: 3px 9px;color: white;">' + Returneddata.booking + '</div>';
//
//
//        };
//    } else {
//        document.getElementById("result").innerHTML = "Sorry, your browser does not support server-sent events...";
//    }


var interval;

function get_driver_data(userid){
    $('td').css('background','white');
$('.u'+userid).closest('td').css('background','#A9F5F2');
//alert(userid);
//        var source1;
    $.ajax({
        type: "POST",
        url: 'get_driver_Data',
        data: { did: userid },
        dataType: "json",
        success: function(data) {
//                $('#query').html(data.test);
//            $('#userjob').html(data.test);


            var t = $('#tableWithSearchforjobs').DataTable();
if(data.jobs.length > 0){
    t
        .clear()
        .draw();
    $.each(data.jobs,function (index,row1){
        t.row.add([
            row1.appointment_id,
            row1.p_name,
            row1.phone,
            row1.app_dt,
            row1.address_pick,
            row1.drop_address,
            row1.status
        ]).draw();
    });
}



            var t2 = $('#tableWithSearchforreview').DataTable();
            if(data.reviewtable.length > 0){
                t2
                    .clear()
                    .draw();
                $.each(data.reviewtable,function (index,row1){
                    if(row1.review != '') {
                        t2.row.add([
                            row1.appointment_id,
                            row1.review,
                            row1.app_dt,
                            row1.star,
                        ]).draw();
                    }
                 });


            }


//            $.each(data.jobs,function(index,row1){
//
//                jobstable.row.add([
////                    row1.slno,
////                    row1.appointment_id,
////                    row1.p_name,
////                    row1.phone,
////                    row1.app_dt,
////                    row1.address_pick,
////                    row1.drop_address,
//                    'BMW',
//                    'BMW',
//                    'BMW',
//                    'BMW',
//                    'BMW',
//                    'BMW',
//                    'BMW',
//                    'BMW',
//                    'BMW'
////                    row1.status
//                ]).draw();
//
//                alert(row1.slno);
//            });

            if(data.rest == 2){
                $('#nodata').hide();
                $('#tab1data').show();
//                $('#userjob').html(data.test);
            }

            else if(data.rest == 1)
            {
                $('#tab1data').hide();
                $('#nodata').show();
//                $('#nodata').html(data.test);

            }

            $('#tab2profile').html(data.profile);
//                $('#userreview').html(data.review);
            $('#userstatus').show();
            $('#lat').val(data.lat);
            $('#long').val(data.long);
//                $('#test').html(data.test);

//                var t = $('#tableWithSearchforreview').DataTable();
//                t.row.add(
//                    data.review
//                 ).draw();

//            $("#userreview").html(data.review);
            displayMap();






//                source.close();
//                var source1 = new EventSource("<?php //echo base_url()?>//serverdata/userstatus.php?uid="+userid);
//                source1.onmessage = function(event) {
//
//
//
//                    var Returneddata = $.parseJSON(event.data);
//
//                     if(Returneddata.status == 1)
//                     document.getElementById("userstatus").innerHTML = '<span style="color: #008000;">Online</div>';
//                    else if(Returneddata.status == 2)
//                         document.getElementById("userstatus").innerHTML = '<span style="color:red;">Offline</div>';
//
//
//
//                };

//                to know whether driver is online or not


            interval = window.setInterval(function () {

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url()?>index.php/dispatch/userstatus",
                    data: {uid: userid},
                    dataType: "JSON",
                    success: function (Returneddata) {
                        if(Returneddata.status == 1) {

                            document.getElementById("userstatus").innerHTML = '<span style="color: #008000;">Online</div>';

//                            $('.u'+Returneddata.user).parent('tr').remove();
                            if($('.u'+Returneddata.user).closest('tr').find('.domstatus').attr('alt') == 4 )
                                $('.u'+Returneddata.user).closest('tr').remove();

                        }
                        else if(Returneddata.status == 2) {

                            document.getElementById("userstatus").innerHTML = '<span style="color:red;">Offline</div>';

                            if($('.u'+Returneddata.user).closest('tr').find('.domstatus').attr('alt') == 3 )
                                $('.u'+Returneddata.user).closest('tr').remove();
//                            $('.u'+Returneddata.user).parent('tr').remove();
                        }

                    }
                                    ,
                                    error: function () {
                                        alert('Error');
                                    }
                });
                //                                            navigator.geolocation.getCurrentPosition(function(position) {
                //                                            });
            }, 3000);






//                source.close();

        }


    });
    window.clearInterval(interval);



}

function call_modal(){
    $('#myModal').modal('show');
}

</script>
<style>
    .panel-controls{
        display: none;
    }

    .mapplic-map{
        position: relative !important;
    }
    #circle {
        width: 28px;
        height: 28px;
        background: #9BCA3E;
        -moz-border-radius: 50px;
        -webkit-border-radius: 50px;
        border-radius: 50px;
        position: absolute;
        margin-left: 15%;
        z-index: 100;
        display: none;
    }
    .nav-tabs-fillup li{
        margin-top: 17px;
    }

</style>


<div id="query"></div>

<div class="whirly-loader" style="margin-left: 50%;z-index: 100;margin-top: 15%;position: absolute;">
    Loading…
</div>

<div class="tab-pane slide-left" id="slide5">
    <div class="row column-seperation">

    <div class="col-md-12">



    <div class="col-md-3" style="border: 1px solid rgba(0, 0, 0, 0.07);min-height: 701px;width: 23%;">

        <div class="tab-pane fade in active no-padding" id="quickview-chat">
            <div class="view-port clearfix" id="chat">
                <div class="view bg-white" style="min-height: 500px;">
                    <!-- BEGIN View Header !-->
                    <div class="navbar navbar-default">
                        <div class="navbar-inner">
                            <!-- BEGIN Header Controler !-->
                            <a href="javascript:;" class="inline action p-l-10 link text-master" data-navigate="view" data-view-port="#chat" data-view-animation="push-parrallax">

                            </a>
                            <!-- END Header Controler !-->
                            <div class="view-heading">
                                Drivers

                                <div class="input-group transparent col-md-11 center-margin" style="padding: 7px 0px 14px;margin-left: 0px;">
                                    <input type="text" class="form-control tableWithSearchDriverListsearch" placeholder="Search Driver" id="icon-filter " name="icon-filter">
                                                      <span class="input-group-addon ">
                                                                    <i class="pg-search"></i>
                                                                </span>

                                </div>

                            </div>

                            <!-- BEGIN Header Controler !-->

                            <!-- END Header Controler !-->

                        </div>

                    </div>
                    <div class="col-sm-10">
                        <div class="radio radio-success">
                            <input type="radio" checked="" value="2" name="statuschk" id="all" >
                            <label for="all">All </label>
                            <input type="radio"  value="3" name="statuschk" id="online">
                            <label for="online">Online</label>
                            <input type="radio"  value="4" name="statuschk" id="offline">
                            <label for="offline">Offline</label>
                        </div>
                    </div>
                    <!-- END View Header !-->
                    <div id="loading"  class="hexdots-loader" style="position: absolute;
margin-top: 125px;
margin-left: 44px; "> <img src="<?php echo base_url()?>theme/assets/img/loading.gif"></div>


                    <div id="tableWithSearchDriverList_data">
                    <table class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearchDriverList" role="grid" aria-describedby="tableWithSearch_info">
                        <thead>

                        <tr role="row">
                            <th  class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Title: activate to sort column ascending" style="width: 247px;display: none">name</th>
                        </tr>

                        </thead>
                        <tbody id="dynamic_status_change">




                        </tbody>
                    </table>

</div>

                </div>

            </div>
        </div>











    </div>
    <div class="col-md-9" style="border: 1px solid rgba(0, 0, 0, 0.07);min-height: 700px;width: 77%;">

    <div class="panel">
    <ul class="nav nav-tabs nav-tabs-simple" role="tablist" id="tablist">
        <li class="active"><a href="#tab2hellowWorld" data-toggle="tab" role="tab">Jobs</a>
        </li>
        <li class=""><a onclick="displayMap()" href="#tab2FollowUs" data-toggle="tab" role="tab" >Locate</a>
        </li>
        <li class=""><a href="#tab2profile" data-toggle="tab" role="tab">Profile</a>
        </li>
        <li class=""><a href="#tab2Inspire" data-toggle="tab" role="tab">Reviews</a>
        </li>
        <div class="pull-right" style="margin-top: 13px;color: red" id="userstatus">Offline</div>
    </ul>

    <div class="tab-content">
    <div class="tab-pane active" id="tab2hellowWorld">
        <div class="row column-seperation">
            <div id="nodata"></div>
            <div class="col-md-12" id="tab1data">


                <table class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearchforjobs" role="grid" aria-describedby="tableWithSearch_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Places: activate to sort column ascending" style="width: 170px;">BOOKING ID</th>
                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 75px;">Patient Name</th>
                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 75px;">PHONE</th>
                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 232px;">PICKUP D & T</th>
                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 300px;">PICKUP ADDRESS</th>
                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 300px;">DROP ADDRESS</th>
                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 232px;">STATUS</th>
                    </tr>
                    </thead>
                    <tbody id="userjob">








                    </tbody>
                </table>


            </div>

        </div>
    </div>





    <!--                                                     // locate  driver-->


    <div class="tab-pane" id="tab2FollowUs">
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" id="lat">
                <input type="hidden" id="long">
                <div style="height: 8px;">
<!--                    <p class="pull-right">-->
<!--                        <button type="button" class="btn btn-success btn-cons" onclick="call_modal()">Add Order</button>-->
<!---->
<!--                    </p>-->

                </div>
                <div id="mapouter">
                    <div id="map-canvas"></div>

                </div>


            </div>
        </div>
    </div>





    <!--                                                     //end of locate driver-->


    <div class="tab-pane" id="tab2profile">
    Select Driver First !
    </div>

    <div class="tab-pane" id="tab2Inspire">
        <div class="row">
            <div class="col-md-12">




                <table class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearchforreview" role="grid" aria-describedby="tableWithSearch_info">
                    <thead>
                    <tr role="row">
<!--                        <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Title: activate to sort column ascending" style="width: 50px !important;padding-left: 0px !important;">SLNO</th>-->
                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Places: activate to sort column ascending" style="width: 106px;">BOOKING ID</th>
                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Activities: activate to sort column ascending" style="width: 140px;">REVIEW</th>
<!--                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 333px;">Review</th>-->
                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 182px;">Review Date</th>

                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 50px;">Rating</th>
                    </tr>
                    </thead>
                    <tbody id="userreview">




                    </tbody>
                </table>






            </div>
        </div>
    </div>

    </div>
    </div>


    </div>



    </div>
   </div>
</div>


<!--this is the end of customers tab-->



<!--the div which we needs to close is it follows-->
</div>





</div>








</div>




</div>









</div>



<div class="modal fade stick-up in" id="myModal" tabindex="-1" role="dialog" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header clearfix text-left">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                </button>
                <h5>Create New Order </h5>

            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="form-group-attached">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group form-group-default">
                                    <label>Order Title</label>
                                    <input type="email" class="form-control">
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="form-group"></div>
                    <br>
                    <div class="form-group-attached">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group form-group-default">
                                    <label>To Whom ?</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group form-group-default">
                                    <label>Email</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group form-group-default">
                                    <label>Phone</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group form-group-default">
                                    <label>Where To ?</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group form-group-default">
                                    <label>Time</label>
                                    <div id="datepicker-component" class="input-group date col-sm-8">
                                        <input type="text" class="form-control"><span class="input-group-addon" style="background: white;border: 0px solid rgba(0, 0, 0, -4.93);"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-8">
                            <div class="p-t-20 clearfix p-l-10 p-r-10">
                                <div class="pull-left">
                                    <p class="bold font-montserrat text-uppercase"></p>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default btn-cons no-margin pull-left inline" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 m-t-10 sm-m-t-10" style="margin-top: 16px">
                            <button type="button" class="btn btn-primary btn-block m-t-5">Add Order</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



<div id="test"></div>


