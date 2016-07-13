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


<script type="text/javascript">
    $(document).ready(function () {


        var status = '<?php echo $status; ?>';

        var table = $('#big_table');
         $('#big_table_processing').show();

        var settings = {
            "autoWidth": false,
            "sDom": "<'table-responsive't><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "iDisplayLength": 20,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/datatable_vehicletype/' + status,
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "iDisplayStart ": 20,
            "oLanguage": {
                "sProcessing": "<img src='http://107.170.66.211/roadyo_live/sadmin/theme/assets/img/ajax-loader_dark.gif'>"
            },
            "fnInitComplete": function () {
                //oTable.fnAdjustColumnSizing();
                 $('#big_table_processing').hide();
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



                table.on('click', '.ordering', function () {

            var data = $(this).attr('data');
            var typeid = $(this).attr('id');
            var row = $(this).closest('tr');
            var currid = $(this).closest('tr').children('td:eq(0)').text();
//            alert(currid);
            var previd = $(this).closest('tr').next().find("td:eq(0)").text();
//            alert(previd);
            var nextid = $(this).closest('tr').prev().find("td:eq(0)").text();
//            alert(nextid);
            var prev_id = '', curr_id = '', next_id = '';

            if (data == '2') {
                curr_id = currid;
//                alert(curr_id);
                prev_id = previd;
//                alert(prev_id);
            } else if (data == '1') {
                curr_id = currid;
                prev_id = nextid;
            }
            
//            alert(curr_id);
//            alert(prev_id);

            if (typeof curr_id === "undefined" || typeof prev_id === "undefined" || curr_id === '' || prev_id === '' ) {
                alert("Can't perform.");
            }
            else {
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('index.php/superadmin') ?>/vehicletype_reordering",
                    data: {prev_id: prev_id, curr_id: curr_id},
                    dataType: "JSON",
                    success: function (result) {
//                            alert(result.flag);
                        if (result.flag == '1') {
                        
                            if (data == '2') {
                                row.insertAfter(row.next());
//                                alert("changed");
                            } else if (data == '1') {
                                row.prev().insertAfter(row);
//                                alert("changed");
                            }
                        }
                            
                       else {
                            alert("Sorry,Not changed try again.");

                        }


                    },
                    error: function ()
                    {
                        alert("Sorry,Not changed try again.");
                    }
                });
            }

        });



        table.dataTable(settings);

        // search box for table
        $('#search-table').keyup(function () {
            table.fnFilter($(this).val());
        });
//           

        $('#companyid').change(function () {

            var table = $('#big_table');
             $('#big_table_processing').show();

            var settings = {
                "autoWidth": false,
                "sDom": "<'table-responsive't><'row'<p i>>",
                "destroy": true,
                "scrollCollapse": true,
                "iDisplayLength": 20,
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/datatable_vehicletype',
                "bJQueryUI": true,
                "sPaginationType": "full_numbers",
                "iDisplayStart ": 20,
                "oLanguage": {
                    "sProcessing": "<img src='http://107.170.66.211/roadyo_live/sadmin/theme/assets/img/ajax-loader_dark.gif'>"
                },
                "fnInitComplete": function () {
                    //oTable.fnAdjustColumnSizing();
                     $('#big_table_processing').hide();
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
                },
            };

            table.dataTable(settings);

            // search box for table
            $('#search-table').keyup(function () {
                table.fnFilter($(this).val());
            });

        });

    });
</script>
<style>
    #companyid{
        display: none;
    }
</style>

<script>
    $(document).ready(function () {
        $("#define_page").html("Vehicle Type");
        $('.vehicle_type').addClass('active');
        $('.vehicle_type').attr('src',"<?php echo base_url();?>/theme/icon/vehicle types_on.png");
//        $('.vehicletype_thumb').addClass("bg-success");



        var table = $('#tableWithSearch1');

        var settings = {
            "autoWidth": false,
            "sDom": "<'table-responsive't><'row'<p i>>",
            "sPaginationType": "bootstrap",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },
            "iDisplayLength": 20,
            "order": [[0, "desc"]]
        };

//        table.dataTable(settings);

//          
//         $('#search-table1').keyup(function() {
//            table.fnFilter($(this).val());
//        });

        $("#edit_vehicletype").click(function () {

            $("#display-data").text("");


            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();

            if (val.length == 0) {
//                alert("please select any one vehicletype");
                $("#display-data").text(<?php echo json_encode(POPUP_VEHICLETYPE_ANYONE); ?>);
            }
            else if (val.length > 1)
            {
//                 alert("please select only one vehicletype");
                $("#display-data").text(<?php echo json_encode(POPUP_VEHICLETYPE_ONLYONE); ?>);
            }
            else {
                $.ajax({
                    type: 'POST',
                    url: "<?php echo base_url('index.php/superadmin') ?>/editvehicletype",
                    data: {val: val},
                    dataType: 'JSON',
                    success: function (response)
                    {

                    }


                });
                window.location = "<?php echo base_url() ?>index.php/superadmin/vehicletype_addedit/edit/" + val;



            }
        });


        $("#delete_vehicletype").click(function () {

            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();

            if (val.length == 0) {
//                alert("please select atleast one vehicletype");
                $("#display-data").text(<?php echo json_encode(POPUP_VEHICLETYPE_ATLEASTONE); ?>);
            }
            else if (val.length >= 1)
            {

                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#confirmmodel');
                if (size == "mini")
                {
                    $('#modalStickUpSmall').modal('show')
                }
                else
                {
                    $('#confirmmodel').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    }
                    else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
                $("#errorboxdata").text(<?php echo json_encode(POPUP_VEHICLETYPE_SUREDELETE); ?>);

                $("#confirmed").click(function () {


                    //    if(confirm("Are you sure to Delete " +val.length + " vehicletypes")){
                    $.ajax({
                        type: 'POST',
                        url: "<?php echo base_url('index.php/superadmin') ?>/delete_vehicletype",
                        data: {val: val},
                        dataType: 'JSON',
                        success: function (response)
                        {
                            //  alert(response.msg);

                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                            });
                            $(".close").trigger("click");

                        }


                    });
                });
            }

        });




    });


    function refreshTableOnActualcitychagne(){

        var table = $('#big_table');
         $('#big_table_processing').show();

        var settings = {
            "autoWidth": false,
            "sDom": "<'table-responsive't><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "iDisplayLength": 20,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/datatable_vehicletype',
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "iDisplayStart ": 20,
            "oLanguage": {
                "sProcessing": "<img src='<?php echo base_url(); ?>theme/assets/img/ajax-loader_dark.gif'>"
            },
            "fnInitComplete": function () {
                //oTable.fnAdjustColumnSizing();
                 $('#big_table_processing').hide();
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
<div class="page-content-wrapper" style="padding-top: 20px">
    <!-- START PAGE CONTENT -->
    <div class="content">


        <div class="brand inline" style="  width: auto;
             font-size: 20px;
             color: gray;
             margin-left: 30px;padding-top: 20px;">
           <!--                    <img src="--><?php //echo base_url();     ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();     ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();     ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

            <strong style="color:#0090d9;">VEHICLE TYPES</strong><!-- id="define_page"-->
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
                <!--                    <li><a href="#" class="active">--><?php //echo $vehicle_status;   ?><!--</a>-->
                <!--                    </li>-->
                <!--                </ul>-->
                <!--                <!-- END BREADCRUMB -->
                <!--            </div>-->






                <div class="panel panel-transparent ">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-fillup  bg-white">
                        <li class="active">
                            <a  href="#"><span><?php echo LIST_VEHICLETYPE; ?></span></a>
                        </li>


                        <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="edit_vehicletype"><?php echo BUTTON_EDIT; ?></button></a></div>
                        <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="delete_vehicletype"><?php echo BUTTON_DELETE; ?></button></a></div>

                        <div class="pull-right m-t-10"><a href="<?php echo base_url() ?>index.php/superadmin/vehicletype_addedit/add"> <button class="btn btn-primary btn-cons" ><?php echo BUTTON_ADD; ?></button></a></div>

                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="container-fluid container-fixed-lg bg-white">
                            <!-- START PANEL -->
                            <div class="panel panel-transparent">
                                <div class="panel-heading">

                                    <div class="error-box" id="display-data" style="text-align:center"></div>
                                    <div id="big_table_processing" class="dataTables_processing" style=""><img src="http://www.ahmed-samy.com/demos/datatables_2/assets/images/ajax-loader_dark.gif"></div>


                                    <div class="searchbtn row clearfix pull-right" >

                                        <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="<?php echo SEARCH; ?>"> </div>
                                    </div>




                                </div>
                                &nbsp;
                                <div class="panel-body">

                                    <?php echo $this->table->generate(); ?>
<!--                                <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" id="big_table" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">
            <thead>

            <tr role="row">
                <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 87px;font-size:15px"><?php echo VTYPE_TABLE_TYPEID; ?></th>
                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo VTYPE_TABLE_TYPENAME; ?></th>
                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 150px;font-size:15px"><?php echo VTYPE_TABLE_MAXSIZE; ?></th>
                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 131px;font-size:15px"><?php echo VTYPE_TABLE_BASEFARE; ?></th>
                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 98px;font-size:15px"><?php echo VTYPE_TABLE_MINFARE; ?></th>
                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 153px;font-size:15px"><?php echo VTYPE_TABLE_PRICEMINUTE; ?></th>
                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 157px;font-size:15px"><?php echo VTYPE_TABLE_PRICRMILE; ?></th>
                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:157px;font-size:15px"><?php echo VTYPE_TABLE_TYPEDESCRIPTION; ?></th>
                
                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:157px;font-size:15px"><?php echo VTYPE_TABLE_CITY; ?></th>
                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:75px;font-size:15px"><?php echo VTYPE_TABLE_SELECT; ?></th>
                

            </tr>


            </thead>
          
            
            <tbody>












                                    <?php
                                    $i = '1';
                                    foreach ($vehicletype as $result) {
                                        ?>


                            <tr role="row"  class="gradeA odd">
                                <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->type_id; ?></p></td>
                                <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->type_name; ?></p></td>
                                <td class="v-align-middle"><?php echo $result->max_size; ?></td>
                                <td class="v-align-middle"><?php echo $result->basefare; ?></td>
                                <td class="v-align-middle"><?php echo $result->min_fare; ?></td>
                                <td class="v-align-middle"><?php echo $result->price_per_min; ?></td>
                                <td class="v-align-middle"><?php echo $result->price_per_km; ?></td>
                                <td class="v-align-middle"><?php echo $result->type_desc; ?></td>
                                <td class="v-align-middle"><?php echo $result->City_Name; ?></td>
                                 <td class="v-align-middle">
                                    <div class="checkbox check-primary">
                                        <input type="checkbox" value="<?php echo $result->type_id; ?>" id="checkbox<?php echo $i; ?>" class="checkbox">
                                        <label for="checkbox<?php echo $i; ?>">Mark</label>
                                    </div>
                                </td>
                               
                            </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
            </tbody>
        </table></div><div class="row"></div></div>-->
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


<div class="modal fade stick-up" id="confirmmodel" tabindex="-1" role="dialog" aria-hidden="true">
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

                    <div class="error-box" id="errorboxdata" style="font-size: large;text-align:center"><?php echo VEHICLEMODEL_DELETE; ?></div>

                </div>
            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4" ></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4" >
                        <button type="button" class="btn btn-primary pull-right" id="confirmed" ><?php echo BUTTON_YES; ?></button>
                    </div>
                </div>
            </div>
        </div>  
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

