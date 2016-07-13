<script src="<?php echo serverdata_folder; ?>pubnub.js"></script>
<style>
    .new-container {
        padding-left: 4px;
        padding-right: 2px;
    }
    .new-12{
        padding-right: 0px;
        padding-left: 0px;
    }

</style>
<script>

    var Diveid = 0;
    var pubnub = PUBNUB.init({
        publish_key: '<?php echo publish_key ?>',
        subscribe_key: '<?php echo subscribe_key ?>',
        ssl: false,
        jsonp: false
    });



    $(document).ready(function () {
        $('.competeBooking').click(function () {
            var bid = $(this).attr('id');
            var data = $(this).attr('data');
            var amount = $('#amounttocharge').val();
            if (confirm('Are you sure.!')) {
                $.ajax({
                    type: 'post',
                    url: '<?php echo base_url('index.php/superadmin/CompleteBooking') ?>',
                    data: {app_id: bid, data: data,amount:amount},
                    dataType: 'json',
                    success: function (row1) {
                        if (row1.flag == 1)
                            alert(row1.msg);
                        else
                            $('#modal-container-186699441').modal('hide');
                    }

                });
            }
        });


        $('.idonclick').click(function () {
            var bid = $(this).attr('data');
            $.ajax({
                type: 'post',
                url: '<?php echo base_url('index.php/superadmin/get_appointmentDetials') ?>',
                data: {app_id: bid},
                dataType: 'json',
                success: function (row1) {

//                    $.each(result, function (index, row1) {
                    $('.competeBooking').attr('id', row1.appointment_id);
                    $('.bookingid').html("Booking Id : " + row1.appointment_id);
                    $('.pickupaddress').html(row1.address_line1);
                    $('.BookingTime').html(row1.appointment_dt);
                    $('.vehicleType').html(row1.typename);

                    $('.approxAmount').html(row1.apprxAmt);
                    $('#amounttocharge').val(row1.apprxAmt);


                    $('.approxAmount').html(row1.apprxAmt);
                    $('.paymentstatus').html(row1.paymentstatus);

                    $('.bookingstatus').html(row1.status_result);

                    $('.driverName').html(row1.first_name);
                    $('.driverPhone').html(row1.mobile);

                    $('.passengerName').html(row1.sname);
                    $('.passengerPhone').html(row1.phone);


//                     });

                    $('#modal-container-186699441').modal('show');

                }

            });
        });

        var table = $('#tableWithSearch').DataTable();

        var data = table.rows().data();

        var arrayD = [];
        $.each(data, function (index, row) {

            arrayD.push(parseInt(row[0].replace(/[<p>/]+/g, "")));

        });

//    alert(arrayD.indexOf(25));
        $('#pub').click(function () {

//            alert('called');
        });

        // INIT PubNub

        // LISTEN
        pubnub.subscribe({
            channel: "<?php echo channel ?>",
            message: function (m) {

                if (m.a == 14) {


                    if (arrayD.indexOf(parseInt(m.bid)) == -1) {

                        $.ajax({
                            type: 'post',
                            url: '<?php echo base_url('index.php/superadmin/get_appointment_details') ?>',
                            data: {app_id: m.bid},
                            dataType: 'json',
                            success: function (result) {
                                var t = $('#tableWithSearch').DataTable();
                                arrayD.push(parseInt(m.bid));
                                $.each(result.data, function (index, row1) {

                                    var rownod = t.row.add([
                                        row1.appointment_id,
                                        row1.mas_id,
                                        row1.first_name,
                                        row1.mobile,
                                        row1.pessanger_fname,
                                        row1.phone,
                                        row1.appointment_dt,
                                        row1.address_line1,
                                        row1.drop_addr1 + row1.drop_addr2,
                                        '<span class="app_id_' + row1.appointment_id + '">' + m.m + '</span>'


                                    ]).order([[0, 'desc']])
                                            .draw().node();
                                });

                            }



                        });


                    }


                    $('.app_id_' + m.bid).html(m.m);
                    if (m.s == 9) {

                        $('.app_id_' + m.bid).closest("tr").remove();
                    }


                }





            }
        });









        var table1 = $('#tableWithSearch1');
//
        var settings1 = {
            "sDom": "<'table-responsive't><'row'<p i>>",
            "sPaginationType": "bootstrap",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },
            "iDisplayLength": 20,
            "order": [[0, 'desc']]

        };

//
        table1.dataTable(settings1);
//
//    // search box for table
        $('#search-table1').keyup(function () {
            table1.fnFilter($(this).val());
        });




    });

    function oneFunction() {
        $.ajax({
            type: 'post',
            url: '<?php echo base_url('index.php/superadmin/filter_AllOnGoing_jobs') ?>',
            dataType: 'json',
            success: function (result) {


                var t = $('#tableWithSearch').DataTable();
                t
                        .clear()
                        .draw();
                $.each(result.aaData, function (index, row1) {
                    var status = 'Status unavailable.';
                    if (row1.status == '1')
                        status = 'request';
                    else if (row1.status == '2')
                        status = 'accepted.';
                    else if (row1.status == '3')
                        status = 'rejected.';
                    else if (row1.status == '4')
                        status = 'Passenger has cancelled.';
                    else if (row1.status == '5')
                        status = 'Driver has canceled';
                    else if (row1.status == '6')
                        status = 'Driver on  way.';
                    else if (row1.status == '7')
                        status = 'Driver arrived';
                    else if (row1.status == '8')
                        status = 'Journey started';
                    else if (row1.status == '9')
                        status = 'Appointment completed';
                    else if (row1.status == '10')
                        status = 'Appointment Timed out.';


                    t.row.add([
                        row1.appointment_id,
                        row1.mas_id,
                        row1.first_name,
                        row1.dphone,
                        row1.pessanger_fname,
                        row1.phone,
                        row1.appointment_dt,
                        row1.address_line1,
                        row1.drop_addr1 + row1.drop_addr2,
                        '<span class="app_id_' + row1.appointment_id + '">' + status + '</span>'


                    ]).order([[0, 'desc']])
                            .draw().node();
                });

            }



        });
    }


    function refreshTableOnCityChange() {
        oneFunction();
    }

    function refreshTableOnActualcitychagne() {
        oneFunction();
    }


</script>


<div class="tab-pane slide-left" id="slide5">
    <div class="row column-seperation">
        <div class="col-md-12 new-12">



            <div class="container-fluid container-fixed-lg bg-white  new-container">
                <!-- START PANEL -->
                <div class="panel panel-transparent">
                    <div class="panel-heading">
                        <div class="panel-title">
                        </div>
                        <div class="pull-right">
                            <div class="col-xs-12">
                                <input type="text" id="search-table" class="form-control pull-right" placeholder="Search">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body m-t-20">
                        <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive">
                                <table class="table table-hover  table-detailed dataTable no-footer " id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info">
                                    <thead>
                                        <tr role="row">
                                            <!--                                        <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Title: activate to sort column ascending" style="width: 247px;">SLNO</th>-->
                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Places: activate to sort column ascending" style="width: 275px;">BOOKING ID</th>
                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Places: activate to sort column ascending" style="width: 275px;">DRIVER ID</th>
                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Places: activate to sort column ascending" style="width: 275px;">DRIVER NAME</th>
                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Places: activate to sort column ascending" style="width: 275px;">DRIVER PHONE</th>
                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Activities: activate to sort column ascending" style="width: 304px;">PASSENGER NAME</th>
                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 175px;">PASSENGER PHONE</th>
                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 232px;">PICKUP D & T</th>
                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 232px;">PICKUP ADDRESS</th>
                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 232px;">DROP ADDRESS</th>
                                            <!--                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 232px;">VEHICLE TYPE</th>-->
                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 232px;">STATUS</th>

                                        </tr>
                                    </thead>
                                    <tbody>






                                        <?php
                                        //                                    $slno = 1;

                                        foreach ($ongoing_booking as $result) {
                                            ?>



                                            <tr role="row"  class="gradeA odd">
                                                <!--                                            <td id = "d_no" class="v-align-middle sorting_1"> <p>--><?php //echo $slno;          ?><!--</p></td>-->
                                                <td id = "d_no" class="v-align-middle sorting_1"><a class="idonclick" data="<?php echo $result->appointment_id; ?>" style="cursor: pointer"> <p><?php echo $result->appointment_id; ?></p></a></td>
                                                <td class="v-align-middle"><?php echo $result->mas_id ?></td>
                                                <td class="v-align-middle"><?php echo $result->first_name . ' ' . $result->last_name; ?></td>
                                                <td class="v-align-middle"><?php echo $result->dphone ?></td>
                                                <td class="v-align-middle"><?php echo $result->pessanger_fname . $result->pessanger_lname; ?></td>
                                                <td class="v-align-middle"><?php echo $result->phone; ?></td>
                                                <td class="v-align-middle"><?php echo date("M d Y g:i A", strtotime($result->appointment_dt)); ?></td>
                                                <td class="v-align-middle"><?php echo $result->address_line1 . $result->address_line2; ?></td>
                                                <td class="v-align-middle"><?php echo $result->drop_addr1 . $result->drop_addr2; ?></td>
                                                <!--                                            <td class="v-align-middle">BMW</td>-->
                                                <?php
                                                if ($result->status == '1')
                                                    $status = 'request';
                                                else if ($result->status == '2')
                                                    $status = $result->driver_fname . ' accepted.';
                                                else if ($result->status == '3')
                                                    $status = $result->driver_fname . ' rejected.';
                                                else if ($result->status == '4')
                                                    $status = 'Passenger has cancelled.';
                                                else if ($result->status == '5')
                                                    $status = $result->driver_fname . ' ' . $result->driver_lname . ' has canceled';
                                                else if ($result->status == '6')
                                                    $status = $result->driver_fname . ' on the way.';
                                                else if ($result->status == '7')
                                                    $status = 'Driver Arrived';
                                                else if ($result->status == '8')
                                                    $status = 'Journey  Started';
                                                else if ($result->status == '9')
                                                    $status = 'Appointment completed.';
                                                else if ($result->status == '10')
                                                    $status = 'Appointment Timed out.';
                                                else
                                                    $status = 'Status unavailable.';
                                                ?>
                                                <td class="v-align-middle"><span class="app_id_<?php echo $result->appointment_id; ?>"><?php echo $status; ?></span></td>

                                            </tr>

                                            <?php
                                            $slno++;
                                        }
                                        //                                            
                                        ?>

                                    </tbody>
                                </table>

                            </div><div class="row"><div></div></div></div>
                    </div>
                </div>
                <!-- END PANEL -->
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






<div class="modal in" id="modal-container-186699441" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="widget-15 panel  no-border no-margin widget-loader-circle">
                <div class="panel-heading">

                    <div class="panel-title bookingid">
                        Booking Id : 101
                    </div>
                    <div class="panel-controls">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                        </button>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="p-l-3">
                        <div class="row">
                            <div class="col-md-12 col-xlg-6">

                                <div class="b-b b-t b-grey m-b-10">
                                    <!--pickpu address-->
                                    <div class="row m-t-10">
                                        <div class="col-md-5">
                                            <div class="panel-title">
                                                <i class="pg-map"></i>Pickup
                                                <span class="caret"></span>
                                            </div>
                                            <p class="small hint-text pickupaddress">9th August 2014</p>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="pull-left">
                                                <p class="small hint-text no-margin">Passenger</p>

                                                <span class="small hint-text passengerName">Ashish</span>
                                                <span class="small hint-text passengerPhone"> / 8892656768</span>

                                            </div>
                                            <div class="pull-right">
                                                <canvas height="64" width="64" class="clear-day"></canvas>
                                            </div>
                                        </div>
                                    </div>

                                    <!--dropp address--> 
                                    <div class="row m-t-1">
                                        <div class="col-md-5">
                                            <div class="panel-title">
                                                <i class="pg-map text-danger"></i> Drop
                                                <span class="caret"></span>
                                            </div>
                                            <p class="small hint-text Dropaddresss"> -------------- </p>
                                        </div>
                                        <div class="col-md-7" style="margin-bottom: -25px;">
                                            <p class="small hint-text no-margin">Driver</p>

                                            <span class="small hint-text driverName">Ashish</span>
                                            <span class="small hint-text driverPhone"> / 8892656768</span>

                                            <div class="pull-right">
                                                <canvas height="64" width="64" class="clear-day"></canvas>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <p class="bold">Booking Details</p>
                                <div class="widget-17-weather b-b b-grey">
                                    <div class="row">
                                        <div class="col-sm-6 p-r-10">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p class="pull-left ">Currunt Status</p>
                                                    <p class="pull-right bookingstatus"> On the way</p>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p class="pull-left">Payment Method</p>
                                                    <p class="pull-right paymentstatus">Cash</p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p class="pull-left bold">Approx Amount</p>
                                                    <p class="pull-right text-danger approxAmount"> <?php echo currency; ?> 500 </p>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-sm-6 p-l-10">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p class="pull-left">Booking Time</p>
                                                    <p class="pull-right BookingTime">1 hour 30 m </p>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p class="pull-left">Vehicle Type</p>
                                                    <p class="pull-right vehicleType">Silver</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row m-t-10 m-l-5">
                                    <div class="col-sm-6 p-r-10">
                                        <div class="row">
                                            <button class="btn btn-success btn-cons competeBooking" data="1">Complete (Don't Charge)</button>
                                        </div>

                                    </div>
                                    <input type="hidden" id="amounttocharge"> 
                                    <div class="col-sm-6 p-l-10 ">
                                        <div class="row">
                                            <button class="btn btn-danger btn-cons pull-right m-r-20 competeBooking" data="2">Complete (Charge) </button>
                                        </div>
                                    </div>
                                </div>



                            </div>

                        </div>
                    </div>
                </div>
                <img src="pages/img/progress/progress-circle-master.svg" style="display:none"></div>


        </div>
    </div>
</div>
