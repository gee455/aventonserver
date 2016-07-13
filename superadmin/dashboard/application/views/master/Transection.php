<?php
date_default_timezone_set('UTC');
$rupee = "$";
error_reporting(0);
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


<script>
    $(document).ready(function () {
        $('#searchData').click(function () {


            var dateObject = $("#start").datepicker("getDate"); // get the date object
            var st = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format
            var dateObject = $("#end").datepicker("getDate"); // get the date object
            var end = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format

//           $('#createcontrollerurl').attr('href','<?php // echo base_url() ?>//index.php/masteradmin/Get_dataformdate/'+st+'/'+end);

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
                "autoWidth": false,
                "iDisplayLength": 20,
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?php echo base_url() ?>index.php/masteradmin/transection_data_form_date/' + st + '/' + end + '/' + $('#search_by_select').val() ,
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

        $('#search_by_select').change(function () {


//          $('#atag').attr('href','<?php //echo base_url() ?>//index.php/masteradmin/search_by_select/'+$('#search_by_select').val());

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
                "sAjaxSource": '<?php echo base_url() ?>index.php/masteradmin/search_by_select/' + $('#search_by_select').val(),
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


//            $("#callone").trigger("click");
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

            url = '<?php echo base_url() ?>index.php/masteradmin/transection_data_form_date/' + st + '/' + end + '/' + $('#search_by_select').val() + '/' + $('#companyid').val();

        } else {
            url = '<?php echo base_url() ?>index.php/masteradmin/search_by_select/' + $('#search_by_select').val();
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

<script type="text/javascript">
    $(document).ready(function () {


        $('.transection').addClass('active');
         $('.transection').attr('src',"<?php echo base_url();?>/theme/icon/accounting_on.png");
//        $('.transection .icon-thumbnail').addClass("bg-success");

        $('#datepicker-component').on('changeDate', function () {
            $(this).datepicker('hide');
        });



//        $("#datepicker1").datepicker({ minDate: 0});
        var date = new Date();
        $('#datepicker-component').datepicker({
            startDate: date
        });

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
            "sAjaxSource": '<?php echo base_url(); ?>index.php/masteradmin/transection_data_ajax',
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
                <div class="inner">
                    <!-- START BREADCRUMB -->
                    <ul class="breadcrumb">
                        <li>
                            <p>Master</p>
                        </li>
                        <li><a href="#" class="active">ACCOUNTING</a>
                        </li>
                    </ul>
                    <!-- END BREADCRUMB -->
                </div>





                <div class="container-fluid container-fixed-lg bg-white">
                    <!-- START PANEL -->
                    <div class="panel panel-transparent">
                        <div class="panel-heading">



                            <div class="row clearfix">

                                <div class="col-sm-2">
                                    <div class="">
                                        <div class="form-group ">
                                            <select  class="full-width select2-offscreen" id="search_by_select" data-init-plugin="select2" tabindex="-1" title="select" >
                                                <optgroup label="Payment Type">
                                                    <option selected>Payment Method...</option>
                                                    <option value="0" selected>Any</option>
                                                    <option value="2">Cash</option>
                                                    <option value="1">Card</option>
                                                </optgroup>

                                            </select>
                                            <input type="button" id="callone" style="display: none;"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="" aria-required="true">

                                        <div class="input-daterange input-group" id="datepicker-range">
                                            <input type="text" class="input-sm form-control" name="start" id="start" placeholder="From">
                                            <span class="input-group-addon">to</span>
                                            <input type="text" class="input-sm form-control" name="end"  id="end" placeholder="To">

                                        </div>

                                    </div>

                                </div>

                                <div class="col-sm-1">

                                    <div class="" style="margin-left: 31px;font-size: 20px;">

                                        or
                                    </div>
                                </div>

                                <div class="col-sm-2">

                                    <div class="">

                                        <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="Search"> </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="">
                                        <button class="btn btn-primary" type="button" id="searchData">Search</button>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="">

                                        <div class="pull-right"> <a href="<?php echo base_url() ?>index.php/masteradmin/callExel/<?php echo $stdate; ?>/<?php echo $enddate ?>"> 
                                                <button class="btn btn-primary" type="submit">Export</button></a></div>
                                    </div>
                                </div>
                            </div>




                        </div>
                        <div class="panel-body">
                            <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer">
                                <div class="table-responsive">

<?php echo $this->table->generate(); ?>

                                </div><div class="row"></div></div>
                        </div>
                    </div>
                    <!-- END PANEL -->
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



<!--                                    <table class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info">-->
<!--                                        <thead>-->
<!---->
<!--                                        <tr role="row">
<!--                                                                                            <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 68px;">SLNO</th>-->
<!--                                                                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 68px;">Booking ID</th>-->
<!--                                                                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 150px;">Date</th>-->
<!--                                                                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 80px;">Total Fare</th>-->
<!--                                                                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 98px;">App commission</th>-->
<!--                                                                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 90px;">Payment Gateway commission</th>-->
<!--                                                                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 70px;">Driver Earning </th>-->
<!--<!--                                                                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 149px;">Transection Id </th>-->
<!--                                                                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:75px;">Booking Status</th>-->
<!--                                                                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 149px;"> Download </th>-->
<!--                                                                                        </tr>-->
<!---->
<!---->
<!--                                        </thead>-->
<!--                                        <tbody>-->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!--                                        --><?php
//                                                                                    $slno = 1;
//
//                                                                                    foreach ($transection_data as $result) {
//                                                                                        
?>
<!---->
<!---->
<!--                                                                                        <tr role="row"  class="gradeA odd">-->
<!--                                                                                            <td id = "d_no" class="v-align-middle sorting_1"> <p>--><?php //echo $slno;  ?><!--</p></td>-->
<!--                                                                                            <td id = "d_no" class="v-align-middle sorting_1"> <p>--><?php //echo $result->appointment_id;  ?><!--</p></td>-->
<!--                                                                                            <td class="v-align-middle">--><?php //echo date("M d Y g:i A", strtotime($result->appointment_dt));  ?><!--</td>-->
<!--                                                                                            <td class="v-align-middle">--><?php //echo $rupee. $result->amount;  ?><!--</td>-->
<!--                                                                                            <td class="v-align-middle">--><?php //echo $rupee. (float)$result->amount * (10 / 100)  ?><!--</td>-->
<!--                                                                                            <td class="v-align-middle">--><?php //echo $rupee. ((float)($result->amount * (2.9 / 100)) + 0.3)  ?><!--</td>-->
<!--                                                                                            <td class="v-align-middle">--><?php //echo  $rupee. (float) (($result->amount - ($result->amount * (10 / 100)) - (float)(($result->amount * (2.9 / 100)) + 0.3)));  ?><!--</td>-->
<!--<!--                                                                                            <td class="v-align-middle">--><?php ////echo  $result->tr_id; ?><!--<!--</td>-->
<!--                                                                                            --><?php
//                                                                                            if ($result->status == '1')
//                                                                                                $status = 'Appointment requested';
//                                                                                            else if ($result->status == '2')
//                                                                                                $status = 'Driver accepted.';
//                                                                                            else if ($result->status == '3')
//                                                                                                $status = 'Driver rejected.';
//                                                                                            else if ($result->status == '4')
//                                                                                                $status = 'Passenger has cancelled.';
//                                                                                            else if ($result->status == '5')
//                                                                                                $status = 'You have cancelled.';
//                                                                                            else if ($result->status == '6')
//                                                                                                $status = 'Driver is on the way.';
//                                                                                            else if ($result->status == '7')
//                                                                                                $status = 'Appointment started.';
//                                                                                            else if ($result->status == '8')
//                                                                                                $status = 'Driver Arrived';
//                                                                                            else if ($result->status == '9')
//                                                                                                $status = 'Appointment completed.';
//                                                                                            else if ($result->status == '10')
//                                                                                                $status = 'Appointment Timed out.';
//                                                                                            else
//                                                                                                $status = 'Status unavailable.';
//                                                                                            
?>
<!--                                                                                            <td class="v-align-middle">--><?php //echo $status;  ?><!--</td>-->
<!--                                                                                            --><?php // if( $result->inv_id){ ?>
<!--                                                                                            <td class="v-align-middle"><a href="http://107.170.66.211/roadyo_live/invoice/--><?php //echo $result->inv_id  ?><!--.pdf" target="_blank"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Download</button></a></td>-->
<!--                                                                                            --><?php //} else{ ?>
<!--                                                                                                <td class="v-align-middle">--</td>-->
<!--                                                                                            --><?php //}?>
<!--                                                                                        </tr>-->
<!--                                                                                        --><?php
//                                                                                        $slno++;
//                                                                                    }
//                                        //                                            
?>
<!--                                        </tbody>-->
<!--                                    </table>-->