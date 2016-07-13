<?php
date_default_timezone_set('UTC');
$rupee = "$";
error_reporting(0);
$completed = 'active';
$pending = '';
$rejecte = '';
$status == 5;
if ($status == 5) {
    $vehicle_status = 'New';
    $completed = "active";
} else if ($status == 2) {
    $vehicle_status = 'Accepted';
    $pending = "active";
} else if ($status == 4) {
    $vehicle_status = 'Rejected';
    $rejecte = 'active';
} else if ($status == 1) {
    $vehicle_status = 'Free';
    $free = 'active';
}
?>
<script>
    $(document).ready(function () {
        $("#define_page").html("Payroll");
        $('.payroll').addClass('active');
        $('.payroll_thumb').addClass("bg-success");


//        $('#searchData').click(function(){
//
//
//            var dateObject = $("#start").datepicker("getDate"); // get the date object
//            var st = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format
//            var dateObject = $("#end").datepicker("getDate"); // get the date object
//            var end = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format
//
//            $('#createcontrollerurl').attr('href','<?php // echo base_url() ?>//index.php/superadmin/Get_dataformdate/'+st+'/'+end);
//
//        });
//
//        $('#search_by_select').change(function(){
//
//
//            $('#atag').attr('href','<?php //echo base_url() ?>//index.php/superadmin/search_by_select/'+$('#search_by_select').val());
//
//            $("#callone").trigger("click");
//        });



        $('#searchData').click(function () {


            var dateObject = $("#start").datepicker("getDate"); // get the date object
            var st = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format
            var dateObject = $("#end").datepicker("getDate"); // get the date object
            var end = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format

//           $('#createcontrollerurl').attr('href','<?php // echo base_url() ?>//index.php/superadmin/Get_dataformdate/'+st+'/'+end);

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
                "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/payroll_data_form_date/' + st + '/' + end + '/' + $('#companyid').val(),
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

            url = '<?php echo base_url() ?>index.php/superadmin/payroll_data_form_date/' + st + '/' + end + '/' + $('#companyid').val();

        } else {
            url = '<?php echo base_url(); ?>index.php/superadmin/payroll_ajax';
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
            "sAjaxSource": '<?php echo base_url(); ?>index.php/superadmin/payroll_ajax',
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


                <div class="panel panel-transparent ">
                    <!-- Nav tabs -->
                    <!--            <ul class="nav nav-tabs nav-tabs-fillup  bg-white">-->
                    <!--                <li class="--><?php //echo $completed ?><!--">-->
                    <!--                    <a  href="--><?php //echo base_url();  ?><!--index.php/superadmin/payroll"><span>Completed</span></a>-->
                    <!--                </li>-->
                    <!--                <li class="--><?php //echo $pending ?><!--">-->
                    <!--                    <a  href="--><?php //echo base_url();  ?><!--index.php/superadmin/payroll"><span>Pending</span></a>-->
                    <!--                </li>-->
                    <!--                <li class="--><?php //echo $reject ?><!--">-->
                    <!--                    <a  href="--><?php //echo base_url();  ?><!--index.php/superadmin/payroll"><span>Rejected</span></a>-->
                    <!--                </li>-->
                    <!---->
                    <!---->
                    <!--            </ul>-->
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="container-fluid container-fixed-lg bg-white">
                            <!-- START PANEL -->
                            <div class="panel panel-transparent m-t-20">
                                <div class="panel-heading">

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
                                        <div class="">
                                            <button class="btn btn-primary" type="button" id="searchData">Search</button>
                                        </div>
                                    </div>


                                    <div class="row clearfix">







                                        <!--                                <div class="col-sm-2">-->
                                        <!---->
                                        <!--                                    <div class="">-->
                                        <!---->
                                        <!--                                        <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="Search by id"> </div>-->
                                        <!--                                    </div>-->
                                        <!--                                </div>-->
                                        <!---->
                                        <!---->
                                        <!--                                <div class="col-sm-3 pull-right">-->
                                        <!--                                    <div class="">-->
                                        <!---->
                                        <!--                                        <div class="pull-right"> <a href="--><?php //echo base_url() ?><!--index.php/superadmin/callExel_payroll"> <button class="btn btn-primary" type="submit">Export</button></a></div>-->
                                        <!--                                    </div>-->
                                        <!--                                </div>-->
                                        <div class="">

                                            <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="Search by id"> </div>
                                        </div>
                                    </div>




                                </div>
                                <div class="panel-body">
                                    <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer">
                                        <div class="table-responsive">
<?php echo $this->table->generate(); ?>

<!--                                    <table class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info">-->
<!--                                        <thead>-->
                                            <!---->
                                            <!--                                        <tr role="row">-->
                                            <!--                                            <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 68px;">SLNO</th>-->
                                            <!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 88px;">DRIVER ID</th>-->
                                            <!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 150px;">NAME</th>-->
                                            <!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 145px;">TODAY EARNINGS</th>-->
                                            <!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 131px;">WEEK EARNINGS</th>-->
                                            <!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 140px;">MONTH EARNINGS</th>-->
                                            <!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 140px;">LIFE TIME Earning </th>-->
                                            <!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 149px;"> SHOW </th>-->
                                            <!--                                        </tr>-->
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
//                                        $slno = 1;
//
//                                        foreach ($payroll as $result) {
//                                            
?>
                                            <!---->
                                            <!---->
                                            <!--                                            <tr role="row"  class="gradeA odd">-->
                                            <!--                                                <td id = "d_no" class="v-align-middle sorting_1"> <p>--><?php //echo $slno;  ?><!--</p></td>-->
                                            <!--                                                <td id = "d_no" class="v-align-middle sorting_1"> <p>--><?php //echo $result->mas_id;  ?><!--</p></td>-->
                                            <!--                                                <td class="v-align-middle">--><?php //echo $result->first_name; ?><!--</td>-->
                                            <!--                                                <td class="v-align-middle">--><?php //echo  $rupee.number_format((float)$result->today_earnings, 2, '.', '');  ?><!--</td>-->
                                            <!--                                                <td class="v-align-middle">--><?php //echo  $rupee.number_format((float)$result->week_earnings, 2, '.', '');   ?><!--</td>-->
                                            <!--                                                <td class="v-align-middle">--><?php //echo  $rupee. number_format((float)$result->month_earnings, 2, '.', ''); ?><!--</td>-->
                                            <!--                                                <td class="v-align-middle">--><?php //echo  $rupee. number_format((float)$result->total_earnings, 2, '.', '');  ?><!--</td>-->
                                            <!--                                                <td class="v-align-middle"><a href="--><?php //echo base_url('index.php/superadmin/DriverDetails/'.$result->mas_id) ?><!--"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>-->
                                            <!--                                                    <a href="--><?php //echo base_url('index.php/superadmin/Driver_pay/'.$result->mas_id) ?><!--"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button></a>-->
                                            <!--                                                </td>-->
                                            <!---->
                                            <!--                                            </tr>-->
                                            <!--                                            --><?php
//                                            $slno++;
//                                        }
//                                        //                                            
?>
                                            <!--                                        </tbody>-->
                                            <!--                                    </table>-->
                                        </div><div class="row"></div></div>
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