<?php date_default_timezone_set('UTC'); ?>

<script>

  $(document).ready(function () {



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
            "sAjaxSource": '<?php echo base_url(); ?>index.php/passengeradmin/bookings_data_ajax',
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
                "sAjaxSource": '<?php echo base_url(); ?>index.php/passengeradmin/bookings_data_ajax',
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




</script>
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
                            <p>Booking</p>
                        </li>
                        <li><a href="#" class="active"> </a>
                        </li>
                    </ul>
                    <!-- END BREADCRUMB -->
                </div>


                <div class="container-fluid container-fixed-lg bg-white">
                    <!-- START PANEL -->
                    <div class="panel panel-transparent">
                        <div class="panel-heading">

                            <div class="pull-right">
                                <div class="col-xs-12">
                                    <input type="text" id="search-table" class="form-control pull-right" placeholder="Search">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <?php echo $this->table->generate(); ?>
<!--                            <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info">
                                        <thead>
                                            <tr role="row">
                                                <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Title: activate to sort column ascending" style="width: 247px;font-size: 14px;">
                                                    Slno</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Places: activate to sort column ascending" style="width: 102px !important;font-size: 14px;">Booking Id</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 175px;font-size: 14px;">Driver Name</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Activities: activate to sort column ascending" style="width: 170px;font-size: 14px;">Driver Photo</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 232px;font-size: 14px;">
                                                    Pickup Address</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 232px;font-size: 14px;">
                                                    Drop Address</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 120px !important;font-size: 14px;">
                                                    Pickup Time & Date</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 120px !important;font-size: 14px;">
                                                    Drop Time & Date</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 232px;font-size: 14px;">
                                                    Fare</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Last Update: activate to sort column ascending" style="width: 232px;font-size: 14px;">
                                                    Invoice</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            $slno = 1;

                                            foreach ($bookinlist as $result) {
                                                ?>


                                                <tr role="row" class="odd">
                                                    <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $slno; ?></p></td>
                                                    <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->appointment_id; ?></p></td>

                                                    <?php $fullname_doc = $result->doc_firstname . " " . $result->doc_lastname; ?>
                                                    <td id = "d_lname" class="v-align-middle"> <p><?php echo $fullname_doc; ?></p></td>
                                                    <td id = "d_fname" class="v-align-middle"><p> <img src="http://107.170.66.211/roadyo_live/pics/<?php echo $result->doc_profile; ?>"></p></td>
                                                    <td class="v-align-middle"><img src="<?php echo PIC_PATH . $result->doc_profile; ?>" style="width: 63px;
                                                                                    "></td>                                                                      
                                                                                    <?php $fullname_patient = $result->patient_firstname . " " . $result->patient_lastname; ?>
                                                                                    <?php $address = $result->address_line1 . " " . $result->address_line2; ?>
                                                                                    <?php $pickup = $result->drop_addr1 . " " . $result->drop_addr2; ?>

                                                    <td class="v-align-middle"><?php echo trim($pickup, "%20"); ?></td>
                                                    <td class="v-align-middle"><?php echo trim($address, "%20"); ?></td>
                                                    <td class="v-align-middle"><?php echo date("M d Y g:i A", strtotime($result->appointment_dt)); ?></td>
                                                    <td class="v-align-middle"><?php echo date("M d Y g:i A", strtotime($result->complete_dt)); ?></td>
                                                    <td class="v-align-middle"><?php echo "$" . $result->amount; ?></td>
                                                    <td class="v-align-middle"><a href="<?php echo base_url();?>../../getPDF.php?apntId=<?php echo $result->appointment_id; ?>" target="_blank"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Download</button></a></td>
                                                </tr>
                                                <?php
                                                $slno++;
                                            }
                                            ?> 

                                        </tbody>
                                    </table></div></div>-->
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