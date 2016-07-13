<?php
date_default_timezone_set('UTC');
$rupee = "$";
error_reporting(0);

$total = $unsettled_amount_count = 0;
foreach ($driverdata as $result) {

    $drivername = $result->first_name;
    $total = round($result->total, 2);
    $unsettled_amount_count = $result->unsettled_amount_count;
//    $new_amount = round($result->new_amount, 2);
    $last_unsettled_appointment_id = $result->last_unsettled_appointment_id;
}

echo 'unsettled_amount_count' . $unsettled_amount_count;

$driverpaid = 0;
foreach ($totalamountpaid as $result) {
    $driverpaid = $result->totalamt;
}


//echo 'total amt '.$total;
//echo 'driver apid up to'.$driverpaid;


$ClosingAmount = round(($total - $driverpaid), 2);


//echo $totalamountpaid[0]['pay_amount'];
?>
<script>
    $(document).ready(function () {
        
         $('.payroll').addClass('active');
         $('.payroll').attr('src',"<?php echo base_url();?>/theme/icon/payroll_on.png");
//        $("#tableWithSearch tr:last").find('.close_bal').text('<?php echo $ClosingAmount; ?>');
//
//        var colonneSelected = $("#tableWithSearch tr:last").find('.close_bal').text();
//        $('#currunEarnigs').val(colonneSelected);


        $('#payamount').click(function () {

            $('#myModal').modal('show');
            var colonneSelected = $('#due_amt').html();// $("#tableWithSearch tr:last").find('.close_bal').text();
            if (colonneSelected == 0) {
                $('.r_amount,.cr_amount,.p_amount').hide();
            }
            else {
                $('.r_amount,.cr_amount,.p_amount').show();
            }

        });

        $("#releasedamt").blur(function () {

            var firstamt = parseFloat($('#due_amt').html());

            var secondamt = parseFloat($('#releasedamt').val());

            if (firstamt >= secondamt) {
                $('#error_msg').html("");

            } else {
                $('#error_msg').html("Released Amount Is grater then Due Amount ! ");
                $('#releasedamt').focus();
            }

        });






        $('#submit_form').click(function () {


            var firstamt = parseInt($('#releasedamt').val());

            var secondamt = parseInt($('#creleasedamt').val());

            if (firstamt == secondamt) {
                if (firstamt > 0 || secondamt > 0) {
                    $('#error_msg_chk').html("");
//                 var ctime = new Date().getTime();

                    var currentdate = new Date();
                    var datetime = currentdate.getFullYear() + "-"
                            + (currentdate.getMonth() + 1) + "-"
                            + currentdate.getDate() + " "
                            + currentdate.getHours() + ":"
                            + currentdate.getMinutes() + ":"
                            + currentdate.getSeconds();


                    $('#hdate').val(datetime);
//                var message;
                    var colonneSelected = $("#tableWithSearch tr:last").find('.close_bal').text();
//                var idSequenceChosen = $(colonneSelected[0]).text();
//                var nomSequenceChosen = $(colonneSelected[1]).text();
//                message = "You have chosen the " + nomSequenceChosen + " sequence with ID of " + idSequenceChosen;
//                alert(colonneSelected)
//                    $('#due_amt').html(colonneSelected);
                    $('#currunEarnigs').val(colonneSelected);

                    $('#form-work').submit();
                }
                else {
                    $('#error_msg_chk').html("Amount Must be Greater then 0!");
                }

            }
            else {
                $('#error_msg_chk').html("Amount does't match !");
            }

        });

    });

    function validate(evt) {
        var theEvent = evt || window.event;
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode(key);
        var regex = /[0-9]|\./;
        if (!regex.test(key)) {
            theEvent.returnValue = false;
            if (theEvent.preventDefault)
                theEvent.preventDefault();
        }
    }
</script>

<style>
    .exportOptions{
        display: none;
    }
</style>
<div class="page-content-wrapper">
    <!-- START PAGE CONTENT -->
    <div class="content">
        <!-- START JUMBOTRON -->
        <div class="jumbotron" data-pages="parallax">

            <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">

                <div class="inner" style="-webkit-transform: translateY(0px); opacity: 1;">
                    <!-- START BREADCRUMB -->
                    <ul class="breadcrumb">
                        <li><a href="<?php echo base_url() ?>index.php/masteradmin/driverDetails" class="">Payroll</a>
                        </li>
                        <li><a href="#" class="active"><?php echo $drivername; ?></a>
                        </li>
                    </ul>
                    <!-- END BREADCRUMB -->
                </div>

                <div class="panel panel-transparent ">
                    <!-- Nav tabs -->
                    <!--                    <ul class="nav nav-tabs nav-tabs-fillup  bg-white">-->
                    <!--                        <li class="--><?php //echo $completed          ?><!--">-->
                    <!--                            <a  href="--><?php //echo base_url();           ?><!--index.php/masteradmin/payroll"><span>Completed</span></a>-->
                    <!--                        </li>-->
                    <!--                        <li class="--><?php //echo $pending          ?><!--">-->
                    <!--                            <a  href="--><?php //echo base_url();           ?><!--index.php/masteradmin/payroll"><span>Pending</span></a>-->
                    <!--                        </li>-->
                    <!--                        <li class="--><?php //echo $reject          ?><!--">-->
                    <!--                            <a  href="--><?php //echo base_url();           ?><!--index.php/masteradmin/payroll"><span>Rejected</span></a>-->
                    <!--                        </li>-->
                    <!---->
                    <!---->
                    <!--                    </ul>-->
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="container-fluid container-fixed-lg bg-white">
                            <!-- START PANEL -->
                            <div class="panel panel-transparent">
                                <div class="panel-heading">



                                    <div class="row clearfix">
<!--                                        <div class="pull-left">
                                            <div class="col-md-6">
                                            <button id="payamount" class="btn btn-primary btn-cons m-b-10 m-l-10 pull-left" type="button" ><i class="pg-form"></i> <span class="bold">PAY</span>
                                        </button>
                                                </div>
                                            <div class="col-md-6">
                                         <input type="text" id="search-table" class="form-control pull-right" placeholder="Search by id">
                                            </div>
                                        </div>-->
                                        

<!--                                        <div class="">
                                            <div class="pull-right">
                                               
                                            </div>
                                        </div>-->
                                        
                                        <div class="pull-right">
                                                <?php // if (!empty($payrolldata)) {
                                                 foreach ($payrolldata as $result) {
                                                
                                                        $lastDue = $result->closing_balance;
                                                
                                                    }
                                                ?>
            
               &nbsp;&nbsp; <span class="semi-bold"> Closing Balance :</span>
             <?php echo $lastDue; ?>
                                          
               &nbsp;&nbsp; <span class="semi-bold">Latest Bookings</span>
                   (<?php echo $unsettled_amount_count; ?>): <?php echo $total; ?>
            
               &nbsp;&nbsp; <span class="semi-bold">Total : </span>
               <?php
                                                $totalAmount = $total + $lastDue;
                                                echo $totalAmount;
                                                ?>
<?php // }    ?>

                                            </div>

                                    </div>




                                </div>
                                <div class="panel-body">
                                    <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info">
                                                <thead>

                                                    <tr role="row">
                                                        <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 68px;">SLNO</th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 88px;">OPENING BALANCE (<?php echo $rupee;?>)</th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 88px;">LAST PAID AMOUNT(<?php echo $rupee;?>)</th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 88px;">LAST PAID DATE</th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 50px;">CLOSING BALANCE (<?php echo $rupee;?>)</th>
                                                    </tr>


                                                </thead>
                                                <tbody>



                                                    <?php
                                                    $slno = 1;
                                                    $lastDue = 0;
                                                    if (empty($payrolldata)) {
                                                        ?>


                                                    <td class="v-align-middle sorting_1"> <p>-</p></td>
                                                    <td class="v-align-middle sorting_1"> <p>-</p></td>
                                                    <td class="v-align-middle sorting_1"> <p>-</p></td>
                                                    <td class="v-align-middle sorting_1"> <p>-</p></td>
                                                    <td class="v-align-middle sorting_1"> <p>-</p></td>

                                                    <?php
                                                    $slno += 1;
                                                } else {

                                                    foreach ($payrolldata as $result) {
                                                        ?>


                                                        <tr role="row"  class="gradeA odd">
                                                            <td class="v-align-middle sorting_1"> <p><?php echo $slno; ?></p></td>
                                                            <td class="v-align-middle sorting_1"> <p><?php echo $result->opening_balance; ?></p></td>
                                                            <td class="v-align-middle"><?php echo $result->pay_amount; ?></td>
                                                            <td class="v-align-middle"><?php echo date("M d Y g:i:s A", strtotime($result->pay_date)); ?></td>
                                                            <td class="v-align-middle close_bal"><?php echo $result->closing_balance; ?></td>

                                                        </tr>
                                                        <?php
                                                        $lastDue = $result->closing_balance;
                                                        $slno++;
                                                    }
                                                }
                                                //                                            
                                                ?>
                                                </tbody>
                                            </table></div><div class="row">
                                            
                                        </div></div>
                                </div>
                            </div>
                            <!-- END PANEL -->
                        </div>
                    </div>
                </div>







            </div>


        </div>

    </div>
    <!-- END PAGE CONTENT -->
    <!-- START FOOTER -->

    <!-- END FOOTER -->
</div>


<div class="modal fade stick-up in" id="myModal" tabindex="-1" role="dialog" aria-hidden="false" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header clearfix text-left">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                </button>
                <h5>Payment <span class="semi-bold">Information</span></h5>
<!--                <p>We need payment information inorder to process your order</p>-->
            </div>
            <div class="modal-body">
                <form id="form-work" class="form-horizontal" role="form" autocomplete="off" novalidate="novalidate" action="<?php echo base_url() ?>index.php/masteradmin/pay_driver_amount/<?php echo $mas_id ?>" method="post">
                    <div class="form-group-attached">
                        <!--                        <div class="form-group">-->
                        <!--                            <label for="position" class="col-sm-3 control-label">Current Account Balance </label>-->
                        <!--                            <div class="col-sm-9">-->
                        <!--                                <input type="text" class="form-control" id="position" placeholder=" Current Balance" required="" aria-required="true" aria-invalid="true">-->
                        <!--                            </div>-->
                        <!--                        </div>-->

                        <input type='hidden' name='ctime' id="hdate" value==''>
                        <input type='hidden' name='currunEarnigs' value='<?php echo $totalAmount; ?>'>
                        <input type='hidden' name='last_unsettled_appointment_id' id="last_unsettled_appointment_id" value='<?php echo $last_unsettled_appointment_id; ?>'>

                        <div class="form-group">
                            <label for="position" class="col-sm-3 control-label">Due Amount</label>
                            <div class="col-sm-9">
<!--                                <input type="text" class="form-control" id="position" placeholder="Due Amount" required="" aria-required="true" aria-invalid="true">-->
<?php echo $rupee ?> <span id="due_amt"><?php echo $totalAmount; ?></span>
                            </div>
                        </div>

                        <div class="form-group r_amount">
                            <label for="position" class="col-sm-3 control-label">Enter Amount To Be Released </label>
                            <div class="col-sm-9">
                                <input type="text" min="1" class="form-control " id="releasedamt" placeholder="Released Amount" onkeypress='validate(event)' required="" aria-required="true" aria-invalid="true" >
                                <span id="error_msg" style="color: red"></span>
                            </div>
                        </div>

                        <div class="form-group cr_amount">
                            <label for="position" class="col-sm-3 control-label">Re-Confirm Amount</label>
                            <div class="col-sm-9">
                                <input type="text" min="1" class="form-control " id="creleasedamt" placeholder="Confirm Amount" onkeypress='validate(event)' required="" aria-required="true" aria-invalid="true" name="paid_amount">
                                <span id="error_msg_chk" style="color: red"></span>
                            </div>
                        </div>

                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-8">
                        <div class="p-t-20 clearfix p-l-10 p-r-10">
                            <div class="pull-left">
<!--                                <p class="bold font-montserrat text-uppercase">TOTAL</p>-->
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-4 m-t-10 sm-m-t-10 p_amount">
                        <button type="button" id="submit_form" class="btn btn-primary btn-block m-t-5">Pay Now</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>