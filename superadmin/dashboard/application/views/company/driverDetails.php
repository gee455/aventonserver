<?php date_default_timezone_set('UTC');
$rupee = "$";
error_reporting(0);
?>
<script>
    $(document).ready(function(){
        $('#searchData').click(function(){
             if($("#start").val() && $("#end").val())
            {
            var dateObject = $("#start").datepicker("getDate"); // get the date object
            var st = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format
            var dateObject = $("#end").datepicker("getDate"); // get the date object
            var end = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format

//           $('#createcontrollerurl').attr('href','<?php // echo base_url()?>//index.php/superadmin/Get_dataformdate/'+st+'/'+end);

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
                "sAjaxSource": '<?php echo base_url()?>index.php/superadmin/DriverDetails_form_Date/'+st+'/'+end+'/'+$('#companyid').val()+'/'+<?php echo $mas_id?>,
                "bJQueryUI": true,
                "sPaginationType": "full_numbers",
                "iDisplayStart ":20,
                "oLanguage": {
                    "sProcessing": "<img src='<?php echo base_url(); ?>theme/assets/img/ajax-loader_dark.gif'>"
                },
                "fnInitComplete": function() {
                    //oTable.fnAdjustColumnSizing();
                },
                'fnServerData': function(sSource, aoData, fnCallback)
                {
                    $.ajax
                    ({
                        'dataType': 'json',
                        'type'    : 'POST',
                        'url'     : sSource,
                        'data'    : aoData,
                        'success' : fnCallback
                    });
                }
            };

            table.dataTable(settings);

            // search box for table
            $('#search-table').keyup(function() {
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

    });

    function refreshTableOnCityChange(){

        var table = $('#big_table');
        var url = '';

        if($('#start').val() != '' ||$('#end').val() != '' ){

            var dateObject = $("#start").datepicker("getDate"); // get the date object
            var st = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format
            var dateObject = $("#end").datepicker("getDate"); // get the date object
            var end = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format

            url =  '<?php echo base_url()?>index.php/superadmin/DriverDetails_form_Date/'+st+'/'+end+'/'+$('#companyid').val()+'/'+<?php echo $mas_id?>;

        }else{
            url = '<?php echo base_url(); ?>index.php/superadmin/DriverDetails_ajax/'+<?php echo $mas_id?>;
        }
        var settings = {
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
            "iDisplayStart ":20,
            "oLanguage": {
                "sProcessing": "<img src='<?php echo base_url(); ?>theme/assets/img/ajax-loader_dark.gif'>"
            },
            "fnInitComplete": function() {
                //oTable.fnAdjustColumnSizing();
            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            }
        };

        table.dataTable(settings);

        // search box for table
        $('#search-table').keyup(function() {
            table.fnFilter($(this).val());
        });

    }

</script>


<script type="text/javascript">
    $(document).ready(function() {


        $('#datepicker-component').on('changeDate', function(){
            $(this).datepicker('hide');
        });



//        $("#datepicker1").datepicker({ minDate: 0});
        var date = new Date();
        $('#datepicker-component').datepicker({
            startDate: date
        });

        var table = $('#big_table');

        var settings = {
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
            "sAjaxSource": '<?php echo base_url(); ?>index.php/superadmin/DriverDetails_ajax/'+<?php echo $mas_id?>,
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "iDisplayStart ":20,
            "oLanguage": {
                "sProcessing": "<img src='<?php echo base_url(); ?>theme/assets/img/ajax-loader_dark.gif'>"
            },
            "fnInitComplete": function() {
                //oTable.fnAdjustColumnSizing();
            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            }
        };

        table.dataTable(settings);

        // search box for table
        $('#search-table').keyup(function() {
            table.fnFilter($(this).val());
        });

    } );
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
                      
                        <li><a href="<?php echo base_url('index.php/superadmin/payroll')?>" class="">Payroll</a>
                        </li>
                        <li><a href="#" class="active">Driver</a>
                        </li>
                    </ul>
                    <!-- END BREADCRUMB -->
                </div>





                <div class="container-fluid container-fixed-lg bg-white">
                    <!-- START PANEL -->
                    <div class="panel panel-transparent">
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
<!--                                        <div class="pull-right"> <a href="--><?php //echo base_url()?><!--index.php/companyadmin/callExel_payroll"> <button class="btn btn-primary" type="submit">Export</button></a></div>-->
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
                                    <?php echo $this->table->generate();?>
<!--                                    <table class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info">-->
<!--                                        <thead>-->
<!---->
<!--                                        <tr role="row">-->
<!--                                            <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 68px;">SLNO</th>-->
<!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 68px;">Booking Id</th>-->
<!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 150px;">Customer Name</th>-->
<!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 98px;">Customer Paid</th>-->
<!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 140px;">App Commission </th>-->
<!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 140px;">Payment Gateway</th>-->
<!--                                            <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 149px;"> Driver Earnings </th>-->
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
//                                        foreach ($driverdetails as $result) {
//                                            ?>
<!---->
<!---->
<!--                                            <tr role="row"  class="gradeA odd">-->
<!--                                                <td id = "d_no" class="v-align-middle sorting_1"> <p>--><?php //echo $slno; ?><!--</p></td>-->
<!--                                                <td id = "d_no" class="v-align-middle sorting_1"> <p>--><?php //echo $result->appointment_id; ?><!--</p></td>-->
<!--                                                <td class="v-align-middle">--><?php //echo $result->slv_fname;?><!--</td>-->
<!--                                                <td class="v-align-middle">--><?php //echo $rupee. $result->amount; ?><!--</td>-->
<!--                                                <td class="v-align-middle">--><?php //echo $rupee. (float)$result->amount * (10 / 100) ?><!--</td>-->
<!--                                                <td class="v-align-middle">--><?php //echo $rupee. ((float)($result->amount * (2.9 / 100)) + 0.3) ?><!--</td>-->
<!--                                                <td class="v-align-middle">--><?php //echo  $rupee. (float) (($result->amount - ($result->amount * (10 / 100)) - (float)(($result->amount * (2.9 / 100)) + 0.3))); ?><!--</td>-->
<!---->
<!--                                            </tr>-->
<!--                                            --><?php
//                                            $slno++;
//                                        }
//                                                              ?>
<!--                                        </tbody>-->
<!--                                    </table>-->
                                </div><div class="row"></div></div>
                        </div>
                    </div>
                    <!-- END PANEL -->
                </div>



            </div>


        </div>
        <!-- END JUMBOTRON -->



    </div>
    <!-- END PAGE CONTENT -->
    <!-- START FOOTER -->

    <!-- END FOOTER -->
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