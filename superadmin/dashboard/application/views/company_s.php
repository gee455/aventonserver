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
        
            $('.whenclicked li').click(function (){
            // alert($(this).attr('id'));
            
            if($(this).attr('id') == 1){
                $('#add').show();
                 $('#activate').show();
                 $('#deactivate').show();
                  $('#suspend').show();
            }
            else if($(this).attr('id') == 3){
                 $('#activate').hide();
                 $('#deactivate').show();
                  $('#suspend').show();
             }
             else if($(this).attr('id') == 5){
                 $('#deactivate').hide();
                  $('#activate').show();
                   $('#suspend').show();

                }else if($(this).attr('id') == 6){
                $('#suspend').hide();
                $('#deactivate').hide();
                $('#activate').show();
                    }

            });
 

        var settings = {
            "sDom": "<'table-responsive't><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "iDisplayLength": 20,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/datatable_companys/' + status,
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "iDisplayStart ": 20,
            "oLanguage": {
                "sProcessing": "<img src='http://107.170.66.211/roadyo_live/sadmin/theme/assets/img/ajax-loader_dark.gif'>"
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
            },
//                    "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
//
//
//                alert(aData.indexOf(12));
////                break;
//        }
        };




        table.dataTable(settings);

        // search box for table
        $('#search-table').keyup(function () {
            table.fnFilter($(this).val());
        });

    });
</script>

<script>

    $(document).ready(function () {
        $("#define_page").html("Companies");

        $('.company_s').addClass('active');
        $('.company_sthumb').addClass("bg-success");


        $("#editcompany").click(function () {

            var status = '<?php echo $status; ?>';

            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();

            if (val.length == 0) {
//                alert("please select any one company");
                $("#display-data").text(<?php echo json_encode(POPUP_COMPANY_ANYONE); ?>);
            } else if (val.length > 1) {

//                alert("please select only one company to edit");
                $("#display-data").text(<?php echo json_encode(POPUP_COMPANY_ONLYONE); ?>);
            }
            else {

                window.location = "<?php echo base_url() ?>index.php/superadmin/add_edit/edit/" + val;

            }


        });

        $("#activate").click(function () {

            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();

            if (val.length == 0) {
                //        alert("please select atleast one company");
                $("#display-data").text(<?php echo json_encode(POPUP_COMPANY_ATLEASTONENAME); ?>);
            }
            else if (val.length > 0)
            {
                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#confirmmodel');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#confirmmodel').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
                $("#errorboxdata").text(<?php echo json_encode(POPUP_ACCEPTED); ?>);

                $("#confirmed").click(function () {

                    $.ajax({
                        type: 'POST',
                        url: "<?php echo base_url('index.php/superadmin') ?>/activatecompany",
                        data: {val: val},
                        dataType: 'JSON',
                        success: function (response)
                        {
                            
                               $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                               
                           
                            });
                             $(".close").trigger("click");
                            //     alert(response.msg);
//                            window.location = "<?php echo base_url(); ?>index.php/superadmin/company_s/3";
                        }, error: function (e) {

                            alert('error' + e.message);
                        }


                    });


                });

            }

        });



        $("#deactivate").click(function () {

            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();

            if (val.length == 0) {
                //  alert("please select atleast one company");

                $("#display-data").text(<?php echo json_encode(POPUP_COMPANY_ATLEASTONENAME); ?>);
            }
            else if (val.length > 0) {

                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#confirmmodels');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#confirmmodels').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
                $("#errorboxdatas").text(<?php echo json_encode(POPUP_REJECTED); ?>);

                $("#confirmeds").click(function () {


                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/deactivatecompany",
                        type: 'POST',
                        data: {val: val},
                        dataType: 'JSON',
                        success: function (response)
                        {
                            
                                $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                               
                           
                            });
                             $(".close").trigger("click");     
//                             alert(response.msg);
//                            window.location = "<?php echo base_url(); ?>index.php/superadmin/company_s/5";
                        }

                    });


                });
            }

        });


        $("#suspend").click(function () {
            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();

            if (val.length == 0) {
//                alert("please select atleast one company");
                $("#display-data").text(<?php echo json_encode(POPUP_COMPANY_ATLEASTONENAME); ?>);
            }
            else if (val.length > 0) {

                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#confirmmodelss');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#confirmmodelss').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
                $("#errorboxdatass").text(<?php echo json_encode(POPUP_SUSPENDED); ?>);

                $("#confirmedss").click(function () {

                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/suspendcompany",
                        type: 'POST',
                        data: {val: val},
                        dataType: 'JSON',
                        success: function (response)
                        {
                            //    alert(response.msg);
                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                               
                           
                            });
                             $(".close").trigger("click");
//                            window.location = "<?php echo base_url(); ?>index.php/superadmin/company_s/6";
                        }

                    });


                });
            }



        });

        $("#delete").click(function () {
            $("#display-data").text("");

            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();

            if (val.length == 0) {
                //         alert("please select atleast one company");
                $("#display-data").text(<?php echo json_encode(POPUP_COMPANY_ATLEASTONENAME); ?>);
            }
            else if (val.length >= 1)
            {
//                 if(confirm("Are you sure to Delete " +val.length + " companys")){
                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#confirmmode');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#confirmmode').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
                $("#errorboxdat").text(<?php echo json_encode(POPUP_DELETE); ?>);

                $("#confirme").click(function () {
                    
                    

                    $.ajax({
                        type: 'POST',
                        url: "<?php echo base_url('index.php/superadmin') ?>/delete_company",
                        data: {val: val},
                        dataType: 'JSON',
                        success: function (response)
                        {
                            //      alert(response.msg);

                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                            });
                            $(".close").trigger("click");
                        }


                    });


                });
            }

        });


        $('.changeMode').click(function () {

            var table = $('#big_table');


            var settings = {
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

            $('.tabs_active').removeClass('active');

            $(this).parent().addClass('active');



            table.dataTable(settings);

            // search box for table
            $('#search-table').keyup(function () {
                table.fnFilter($(this).val());
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
           <!--                    <img src="--><?php //echo base_url();            ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();            ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();            ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

            <strong style="color:#0090d9;">COMPANIES</strong><!-- id="define_page"-->
        </div>
        <!-- START JUMBOTRON -->
        <div class="jumbotron" data-pages="parallax">
            <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">






                <div class="panel panel-transparent ">
                    <!-- Nav tabs --> 
                    <ul class="nav nav-tabs nav-tabs-fillup  bg-white whenclicked">


                        <li id="1" class="tabs_active  <?php echo ($status == 1 ? "active" : ""); ?> ">
                            <a class="changeMode"  data="<?php echo base_url(); ?>index.php/superadmin/datatable_companys/1"><span><?php echo LIST_NEW; ?></span></a>
                        </li>
                        <li id="3" class="tabs_active <?php echo ($status == 3 ? "active" : ""); ?>">
                            <a  class="changeMode"   data="<?php echo base_url(); ?>index.php/superadmin/datatable_companys/3"><span><?php echo LIST_ACCEPTED; ?> </span></a>
                        </li>
                        <li id="5" class="tabs_active <?php echo ($status == 5 ? "active" : "") ?>">
                            <a  class="changeMode"   data="<?php echo base_url(); ?>index.php/superadmin/datatable_companys/5"><span><?php echo LIST_REJECTED; ?></span></a>
                        </li>

                        <li id="6" class="tabs_active <?php echo ($status == 6 ? "active" : "") ?>">
                            <a  class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_companys/6"><span><?php echo LIST_SUSPENDED; ?></span></a>
                        </li>


                        <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="delete"><?php echo BUTTON_DELETE; ?></button></div>

                      
                            <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons action_buttons" id="suspend"><?php echo BUTTON_SUSPEND; ?></button></div>
                       
                        <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="editcompany"><?php echo BUTTON_EDIT; ?></button></div>

                 
                            <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons action_buttons" id="deactivate"><?php echo BUTTON_DEACTIVE; ?></button></div>
                       
                            <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons action_buttons" id="activate"><?php echo BUTTON_ACTIVATE; ?></button></div>
                      

                            <div id="add" class="pull-right m-t-10"><a href="<?php echo base_url() ?>index.php/superadmin/add_edit/add"> <button class="btn btn-primary btn-cons" ><?php echo BUTTON_ADD; ?></button></a></div>
                      


                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="container-fluid container-fixed-lg bg-white">
                            <!-- START PANEL -->
                            <div class="panel panel-transparent">
                                <div class="panel-heading">

                                    <div class="error-box" id="display-data" style="text-align:center"></div>

                                    <div cass="col-sm-6">
                                        <div class="searchbtn row clearfix pull-right" >

                                            <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="<?php echo SEARCH; ?>"> </div>
                                        </div>
                                    </div>




                                </div>



                                <div class="panel-body">

                                    <?php echo $this->table->generate(); ?>
<!--                                    <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" id="big_table" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">
            <thead>

                <tr role="row">
                    <th id="companyid"  tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="descending" aria-label="Rendering engine: activate to sort column descending" style="width: 87px;font-size:15px"><?php echo COMPANY_TABLE_COMPANYID; ?></th>
                    <th id="companyname"class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo COMPANY_TABLE_COMPANYNAME; ?></th>
                    <th id="address" class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 150px;font-size:15px"><?php echo COMPANY_TABLE_ADDRESSLINE; ?></th>
                    <th id="city" class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 131px;font-size:15px"><?php echo COMPANY_TABLE_CITY; ?></th>
                    <th id="state" class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 98px;font-size:15px"><?php echo COMPANY_TABLE_STATE; ?></th>
                    <th id="postalcode" class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 153px;font-size:15px"><?php echo COMPANY_TABLE_POSTALCODE; ?></th>
                    <th id="firstnama" class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 157px;font-size:15px"><?php echo COMPANY_TABLE_FIRSTNAME; ?></th>
                    <th id="lastnama"class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:157px;font-size:15px"><?php echo COMPANY_TABLE_LASTNAME; ?></th>

                    <th id="email"class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:157px;font-size:15px"><?php echo COMPANY_TABLE_EMAIL; ?></th>
                    <th id="mobile"class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:75px;font-size:15px"><?php echo COMPANY_TABLE_MOBILE; ?></th>
                    <th id="select"class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:75px;font-size:15px"><?php echo COMPANY_TABLE_SELECT; ?></th>
                   </tr>


            </thead>


            <tbody>


                                    <?php
                                    $i = 1;

                                    foreach ($company as $result) {
                                        ?>

                                                        <tr role="row" class="gradeA odd">
                                                        <td id="d_no" class="v-align-middle sorting_1"> <p><?php echo $result->company_id; ?></p></td>
                                                        <td id="d_no" class="v-align-middle sorting_1"> <p><?php echo $result->companyname; ?></p></td>
                                                        <td class="v-align-middle"><?php echo $result->addressline1; ?></td>
                                                                <td class="v-align-middle"><?php
                                        foreach ($city as $val) {
                                            if ($val->City_Id == $result->city)
                                                echo $val->City_Name;
                                        }
                                        ?></td>
                                                                <td class="v-align-middle"><?php echo $result->state; ?></td>
                                                                
                                                                
                                                        <td class="v-align-middle"><?php echo $result->postcode; ?></td>
                                                        <td class="v-align-middle"><?php echo $result->firstname; ?></td>
                                                        
                                                        <td class="v-align-middle"><?php echo $result->lastname; ?></td>
                                                        <td class="v-align-middle"><?php echo $result->email; ?></td>
                                                                <td class="v-align-middle"><?php echo $result->mobile; ?></td>
                                                                
                                                       
                                                         <td class="v-align-middle">
                                                              <div class="checkbox check-primary">
                                                                <input type="checkbox" value="<?php echo $result->company_id; ?>" id="checkbox<?php echo $i; ?>" class="checkbox">
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

                    <div class="error-box" id="errorboxdata" style="font-size: large;text-align:center"><?php echo COMPAIGNS_DISPLAY; ?></div>

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

                    <div class="error-box" id="errorboxdatas" style="font-size: large;text-align:center"><?php echo COMPAIGNS_DISPLAY; ?></div>

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




<div class="modal fade stick-up" id="confirmmodelss" tabindex="-1" role="dialog" aria-hidden="true">
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

                    <div class="error-box" id="errorboxdatass" style="font-size: large;text-align:center"><?php echo COMPAIGNS_DISPLAY; ?></div>

                </div>
            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4" ></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4" >
                        <button type="button" class="btn btn-primary pull-right" id="confirmedss" ><?php echo BUTTON_YES; ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<div class="modal fade stick-up" id="confirmmode" tabindex="-1" role="dialog" aria-hidden="true">
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

                    <div class="error-box" id="errorboxdat" style="font-size: large;text-align:center"><?php echo COMPAIGNS_DISPLAY; ?></div>

                </div>
            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4" ></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4" >
                        <button type="button" class="btn btn-primary pull-right" id="confirme" ><?php echo BUTTON_YES; ?></button>
                    </div>
                </div>
            </div>
        </div>  

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

