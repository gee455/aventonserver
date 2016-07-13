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
} else if ($status == 6) {
    $vehicle_status = 'Free';
    $free = 'active';
} else if ($status == 1) {
    $active = 'active';
}
?>




<script>
    $(document).ready(function () {

        $("#define_page").html("Vehicles");

        $('.vehicles').addClass('active');
        $('.Vehicles').attr('src',"<?php echo base_url();?>/theme/icon/vehicele model_on.png");
//        $('.vehicles_thumb').addClass("bg-success");

        $('#searchData').click(function () {


            var dateObject = $("#start").datepicker("getDate"); // get the date object
            var st = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format
            var dateObject = $("#end").datepicker("getDate"); // get the date object
            var end = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format

            $('#createcontrollerurl').attr('href', '<?php echo base_url() ?>index.php/superadmin/Get_dataformdate/' + st + '/' + end);

        });



//             $('#search-table').keyup(function() {
//                table.fnFilter($(this).val());
//            });

//        $('#search_by_select').change(function () {
//
//
//            $('#atag').attr('href', '<?php echo base_url() ?>index.php/superadmin/search_by_select/' + $('#search_by_select').val());
//
//            $("#callone").trigger("click");
//        });


        $("#chekdel").click(function () {
            $("#display-data").text("");
            var val = [];
            $('.checkbox:checked').each(function (i) {
                val[i] = $(this).val();
            });

            if (val.length > 0) {

                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#confirmmod');
                if (size == "mini")
                {
                    $('#modalStickUpSmall').modal('show')
                }
                else
                {
                    $('#confirmmod').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    }
                    else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
                $("#errorboxda").text(<?php echo json_encode(POPUP_VEHICLE_DELETE); ?>);

                $("#confirm").click(function () {

//                if (confirm("Are you sure to Delete " + val.length + " Vehicle")) {
                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/deleteVehicles",
                        type: "POST",
                        data: {val: val},
                        dataType: 'json',
                        success: function (result) {
//                            alert(result.affectedRows)


                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                            });
                            $(".close").trigger('click');
                        }

                    });

                });
            }


            else {
//                alert("Please mark any one of options");
                $("#display-data").text(<?php echo json_encode(POPUP_VEHICLE_ATLEAST); ?>);

            }

        });




        $("#reject").click(function () {
            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).
                    get();
//              var appid=$('#appid').val();
//              
              var workplace_id = val.toString();
//     

            if (val.length > 0) {

                //if (confirm("Are you sure you want to deactivate " + val.length + " driver review/reviews"))

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
                $("#errorboxdata").text(<?php echo json_encode(POPUP_VEHICLE_DEACTIVATE); ?>);

                $("#confirmed").click(function () {



                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/reject_vehicle",
                        type: "POST",
                        data: {val: val},
                        dataType: 'json',
                        success: function (result)
                        {

                               $.ajax({
                                    url: "<?php echo base_url();?>../../services.php/PushFromAdmin",
                                    type: "POST",
                                    data: {
                                        Workplace_id: workplace_id
                                        },
                                    dataType: 'JSON',
                                    success: function (result)
                                    {
                                        $('#confirmmodels').modal('hide');

                                        $('.checkbox:checked').each(function (i) {
                                            $(this).closest('tr').remove();
                                        });

                                    }
                                });


                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                            });
                            $(".close").trigger('click');
                        }
                    });
                    
                    


                });
            }
            else
            {
                //    alert("select atleast one passenger");
                $("#display-data").text(<?php echo json_encode(POPUP_VEHICLE_ATLEAST); ?>);
            }

        });


        $("#edit").click(function () {
            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();

            if (val.length > 1)
            {
                $("#display-data").text(<?php echo json_encode(POPUP_VEHICLE_ONE); ?>);
                // if (confirm("Are you sure to activate " + val.length + " driver review/reviews"))
            }
            else if (val.length == 1)

            {
                window.location = "<?php echo base_url() ?>index.php/superadmin/editvehicle/" + val;

            }

            else
            {
                //      alert("select atleast one passenger");
                $("#display-data").text(<?php echo json_encode(POPUP_VEHICLE_ATLEAST); ?>);
            }

        });









        $("#active").click(function () {
            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();

            if (val.length > 0)
            {

                // if (confirm("Are you sure to activate " + val.length + " driver review/reviews"))


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
                $("#errorboxdatas").text(<?php echo json_encode(POPUP_VEHICLE_ACTIVATE); ?>);

                $("#confirmeds").click(function () {
                    {
                        $.ajax({
                            url: "<?php echo base_url('index.php/superadmin') ?>/activate_vehicle",
                            type: "POST",
                            data: {val: val},
                            dataType: 'json',
                            success: function (result)
                            {

                                $('.checkbox:checked').each(function (i) {
                                    $(this).closest('tr').remove();
                                });
                                $(".close").trigger('click');
                            }
                        });
                    }

                });

            }
            else
            {
                //      alert("select atleast one passenger");
                $("#display-data").text(<?php echo json_encode(POPUP_VEHICLE_ATLEAST); ?>);
            }

        });



    });


    function refreshTableOnCityChange() {

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
            "sAjaxSource": $(".whenclicked li.active").children('a').attr('data'),
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
            },
        };

        table.dataTable(settings);

        // search box for table
        $('#search-table').keyup(function () {
            table.fnFilter($(this).val());
        });


    }




    function refreshTableOnActualcitychagne() {

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
            "sAjaxSource": $(".whenclicked li.active").children('a').attr('data'),
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "iDisplayStart ": 20,
            "oLanguage": {
                 "sProcessing": "<img src='<?php echo base_url()?>theme/assets/img/ajax-loader_dark.gif'>"
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


<script type="text/javascript">
    $(document).ready(function () {

        var status = '<?php echo $status; ?>';

        var table = $('#big_table');


        $('.whenclicked li').click(function () {
            // alert($(this).attr('id'));

            if ($(this).attr('id') == 5) {
                $('#add').show();
                $('#active').show();
                $('#reject').show();

            }
            else if ($(this).attr('id') == 2) {
                $('#active').hide();
                $('#reject').show();

            }
            else if ($(this).attr('id') == 4) {
                $('#reject').hide();
                $('#active').show();

            } else if ($(this).attr('id') == 1) {
                $('#add').show();
                $('#active').show();
            } else if ($(this).attr('id') == 3) {
                $('#add').show();
                $('#reject').show();
            }

        });
         $('#big_table_processing').show();

        var settings = {
            "autoWidth": false,
            "sDom": "<'table-responsive't><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "iDisplayLength": 20,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/datatable_vehicles/' + status,
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
        
         $('#big_table').on('init.dt', function () {

            var urlChunks = $("li.active").find('.changeMode').attr('data').split('/');
            var status = urlChunks[urlChunks.length - 1];
           
                if( status == 1 ){
                     $('#big_table').dataTable().fnSetColumnVis([1,2,7,8,10,11], false);
                }   
                else
                    $('#big_table').dataTable().fnSetColumnVis([4,5,6], false);
               
            

        });


        $('.changeMode').click(function () {
            
            var tab_id = $(this).attr('data-id');
           
            if(tab_id != 1)
                $('#addTab').hide();
            
            if(tab_id == 1)
                $('#addTab').show();

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
                "sAjaxSource": $(this).attr('data'),
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

            $('.tabs_active').removeClass('active');

            $(this).parent().addClass('active');



            table.dataTable(settings);

            // search box for table
            $('#search-table').keyup(function () {
                table.fnFilter($(this).val());
            });
            
             $('#big_table').on('init.dt', function () {

            var urlChunks = $("li.active").find('.changeMode').attr('data').split('/');
            var status = urlChunks[urlChunks.length - 1];
           
                if( status == 1 ){
                     $('#big_table').dataTable().fnSetColumnVis([1,2,7,8,10,11], false);
                }   
                else
                    $('#big_table').dataTable().fnSetColumnVis([4,5,6], false);
               
            

        });

        });

        $("#document_data").click(function () {

//       alert("hai");
            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();


            if (val.length == 0) {
                //         alert("please select any one dispatcher");
                $("#display-data").text(<?php echo json_encode(POPUP_VEHICLE_DOCUMENT); ?>);

            } else if (val.length > 1)
            {

                //     alert("please select only one to edit");
                $("#display-data").text(<?php echo json_encode(POPUP_DRIVER_ONLYEDIT_DOCUMENT); ?>);
            }
            else
            {
                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#myModaldocument');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#myModaldocument').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }

            }
                 $('#doc_body').html("");
            $.ajax({
                url: "<?php echo base_url('index.php/superadmin') ?>/documentgetdatavehicles",
                type: "POST",
                data: {val: val},
                dataType: 'json',
                success: function (result)
                {
//                    alert(JSON.stringify(result));

                    $('#doc_body').html("");

                    $.each(result, function (index, vehicle) {

                        if (vehicle.doctype == '99') {
                            $('#view_vehicle_image').attr('href', "<?php echo base_url() ?>../../pics/" + vehicle.urls);
                        } else {
                            var html = "<tr><td>";

                            if (vehicle.doctype == '2')
                                html += "Insurance certificate</td><td>" + vehicle.expirydate + "</td>";
                            else if (vehicle.doctype == '1')
                                html += "Certificate of registration</td><td>" + vehicle.expirydate + "</td>";
                            else if (vehicle.doctype == '3')
                                html += "Carriage permit</td><td>" + vehicle.expirydate + "</td>";

                            html += "<td>" + "<a target=__blank href=<?php echo base_url() ?>../../pics/" + vehicle.url + "><button>view</button></a>\n\
                            <a target=__blank href=<?php echo base_url() ?>../../pics/" + vehicle.url + " download=" + vehicle.url + "><button>download</button></a>" + "</td>";

                            html += "</tr>";

                            $('#doc_body').append(html);
                        }




                        $("#ok").click(function () {
                            $(".close").trigger('click');
                        });
                    });

                }

            });

        });

    });

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
           <!--                    <img src="--><?php //echo base_url();       ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();       ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();       ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

            <strong style="color:#0090d9;">VEHICLES</strong><!-- id="define_page"-->
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
                <!--                    <li><a href="#" class="active">--><?php //echo $vehicle_status;       ?><!--</a>-->
                <!--                    </li>-->
                <!--                </ul>-->
                <!--                <!-- END BREADCRUMB -->
                <!--            </div>-->






                <div class="panel panel-transparent ">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-fillup  bg-white whenclicked">
                        <li  id= "5" class="tabs_active <?php echo ($status == 5 ? "active" : ""); ?> " style="cursor:pointer">
                            <a  class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_vehicles/5" data-id="1"><span><?php echo LIST_NEW; ?></span></a>
                        </li>
                        <li id= "2"  class="tabs_active <?php echo ($status == 2 ? "active" : ""); ?> " style="cursor:pointer" >
                            <a class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_vehicles/12" data-id="2"><span><?php echo LIST_ACCEPTED; ?></span></a>
                        </li>
                        <li id= "4" class="tabs_active <?php echo ($status == 4 ? "active" : ""); ?> " style="cursor:pointer">
                            <a class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_vehicles/4" data-id="3"><span><?php echo LIST_REJECTED; ?></span></a>
                        </li>
                        <li id= "1" class="tabs_active <?php echo ($status == 1 ? "active" : ""); ?> " style="cursor:pointer">
                            <a class="changeMode"  data="<?php echo base_url(); ?>index.php/superadmin/datatable_vehicles/2" data-id="4"><span><?php echo LIST_FREE; ?></span></a>
                        </li>
                        <li id= "3" class="tabs_active <?php echo ($status == 1 ? "active" : ""); ?> " style="cursor:pointer">
                            <a class="changeMode"  data="<?php echo base_url(); ?>index.php/superadmin/datatable_vehicles/1" data-id="5"><span><?php echo LIST_ASSIGNED; ?></span></a>
                        </li>
                        <div class="dltbtn">


                            <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="chekdel"><?php echo BUTTON_DELETE; ?></button></a></div>
                            <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="edit"><?php echo BUTTON_EDIT; ?></button></a></div>

                            <div class="pull-right m-t-10"><button class="btn btn-primary btn-cons" id="document_data" ><?php echo BUTTON_DOCUMENT?></button></div>

                        </div>

                        <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="reject"><?php echo BUTTON_REJECT; ?></button></div>


                        <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="active"><?php echo BUTTON_ACTIVATE; ?></button></div>

                        <div class="pull-right m-t-10" id="addTab"><a href="<?php echo base_url() ?>index.php/superadmin/addnewvehicle/"> <button class="btn btn-primary btn-cons" id="add"><?php echo BUTTON_ADD; ?></button></a></div>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="container-fluid container-fixed-lg bg-white">
                            <!-- START PANEL -->
                            <div class="panel panel-transparent">
                                <div class="panel-heading">
                                    <!--                                --><?php //if($status == '5') {       ?>
                                    <!--                                    <div class="pull-left"><a href="--><?php //echo base_url()       ?><!--index.php/superadmin/addnewvehicle"> <button class="btn btn-primary btn-cons">ADD</button></a></div>-->
                                    <!--                                --><?php //}       ?>
                                    <div class="error-box" id="display-data" style="text-align:center"></div>
                                    <div id="big_table_processing" class="dataTables_processing" style=""><img src="http://www.ahmed-samy.com/demos/datatables_2/assets/images/ajax-loader_dark.gif"></div>

                                    <!--                                        <div class="row clearfix pull-right" style="margin-right: 10px; width: 150px;">
                                    
                                    
                                                                                <select id="cityid" name="company_select"  class="form-control"  style="margin-left: 20px" >
                                                                                    <option value="0"><?php echo SELECT_COMPANY; ?></option>
                                    <?php
                                    foreach ($company as $result) {

                                        echo "<option value=" . $result->company_id . ">" . $result->companyname . "</option>";
                                    }
                                    ?>
                                    
                                                                                </select>
                                    
                                                                            </div>-->

                                    <div class="row clearfix pull-right" >



                                        <div class="col-sm-12">
                                            <div class="searchbtn" >

                                                <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="<?php echo SEARCH; ?> "> </div>
                                            </div>

                                        </div>
                                    </div>







                                </div>
                                 &nbsp;
                                <div class="panel-body">

                                    <?php echo $this->table->generate(); ?>
<!--                                    <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" id="big_table" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">
            <thead>

                <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 87px;"><?php echo VEHICLES_TABLE_VEHICLEID; ?></th>
                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;"><?php echo VEHICLES_TABLE_TITLE; ?></th>
                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 150px;"><?php echo VEHICLES_TABLE_VMODAL; ?></th>
                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 131px;"><?php echo VEHICLES_TABLE_VTYPE; ?></th>
                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 98px;"><?php echo VEHICLES_TABLE_VREGNO; ?></th>
                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 153px;"><?php echo VEHICLES_TABLE_LICENSEPLATNO; ?></th>
                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 157px;"><?php echo VEHICLES_TABLE_INSURENCENUMBER; ?></th>
                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:75px;"><?php echo VEHICLES_TABLE_VCOLOR; ?></th>
                     <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:75px;"><?php echo VEHICLES_TABLE_OPTION; ?></th>  

                </tr>


            </thead>
            <tbody>












                                    <?php
                                    $unq = '1';
                                    foreach ($vehicles as $result) {
                                        ?>


                                    <tr role="row"  class="gradeA odd">
                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->uniq_identity; ?></p></td>
                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->vehicletype; ?></p></td>
                                        <td class="v-align-middle"><?php echo $result->vehiclemodel; ?></td>
                                        <td class="v-align-middle"><?php echo $result->type_name; ?></td>
                                        <td class="v-align-middle"><?php echo $result->Vehicle_Reg_No; ?></td>
                                        <td class="v-align-middle"><?php echo $result->License_Plate_No; ?></td>
                                        <td class="v-align-middle"><?php echo $result->Vehicle_Insurance_No; ?></td>
                                        <td class="v-align-middle"><?php echo $result->Vehicle_Color; ?></td>
                                        <td class="v-align-middle">
                                                <div class="checkbox check-primary">
                                                    <input type="checkbox" value="<?php echo $result->workplace_id; ?>" id="checkbox<?php echo $unq; ?>" class="checkbox">
                                                    <label for="checkbox<?php echo $unq; ?>">Mark</label>
                                                </div>
                                            </td>
                                       
                                    </tr>
                                        <?php
                                        $unq++;
                                    }
//                                            
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
                <span class="hint-text">Copyright @ 3Embed software technologies, All right reserved</span>

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
                        <button type="button" class="btn btn-primary pull-right" id="confirmeds" ><?php echo BUTTON_YES; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



<div class="modal fade stick-up" id="confirmmod" tabindex="-1" role="dialog" aria-hidden="true">
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

                    <div class="error-box" id="errorboxda" style="font-size: large;text-align:center"><?php echo VEHICLEMODEL_DELETE; ?></div>

                </div>
            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4" ></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4" >
                        <button type="button" class="btn btn-primary pull-right" id="confirm" ><?php echo BUTTON_YES; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



<div class="modal fade stick-up" id="myModaldocument" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">

                <div class="modal-header">

                    <div class=" clearfix text-left">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                        </button>

                    </div>
                    <h3> <?php echo VEHICLEDOCUMENTS; ?></h3>
                </div>


                <br>
                <br>

                <div class="modal-body">

                    <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" id="big_table" role="grid" aria-describedby="tableWithSearch_info">


                                <thead>

                                    <tr role="row">
                                        <th  rowspan="1" colspan="1" aria-sort="ascending"  style="width: 100PX;font-size: 14px"><?php echo DRIVERS_TABLE_DRIVER_DOCUMENT; ?></th>
                                        <th  rowspan="1" colspan="1" aria-sort="ascending"  style="width: 100PX;font-size: 14px"><?php echo DRIVERS_TABLE_DRIVER_EXPIREDATE; ?></th>
                                        <th  rowspan="1" colspan="1" aria-sort="ascending"  style="width: 100PX;font-size: 14px"><?php echo DRIVERS_TABLE_DRIVER_VIEW; ?></th>

                                    </tr>


                                </thead>
                                <tbody id="doc_body">

                                </tbody>
                            </table>

                            <div>
                                <label>vehicle image</label>
                                <a  target=__blank href="<?php echo base_url() ?>../../pics/" id="view_vehicle_image"><button>vehicle image</button></a>
                            </div>

                            <div class="row">
                                <div class="col-sm-4" ></div>
                                <div class="col-sm-4 error-box" id="errorpass"></div>
                                <div class="col-sm-4" >
                                    <button type="button" class="btn btn-primary pull-right" id="ok" ><?php echo BUTTON_OK; ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

        </div>