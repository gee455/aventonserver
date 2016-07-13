<?php
date_default_timezone_set('UTC');
$rupee = "$";
//error_reporting(0);

if ($status == 1) {
    $passenger_status = 'active';
    $active = "active";
    echo '<style> .searchbtn{float: left;  margin-right: 63px;}.dltbtn{float: right;}</style>';
} else if ($status == 2) {
    $passenger_status = 'deactive';
    $deactive = "active";
}
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
<script type="text/javascript">
    $(document).ready(function () {
        
        
        
         var status = '<?php echo $status; ?>';
      
      
       if(status == 1){
                $('#delete').show();
                 $('#btnStickUpSizeToggler').show();
                 $('#deletes').hide();
                 
                  $('#vehiclemodal_addbutton').hide();
                  
//                   $('#big_table').find('td:eq(2),th:eq(2)').hide();
//            $('#big_table').find('td:eq(3),th:eq(3)').hide();
                  
              }

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
            "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/datatable_vehiclemodels/' + status,
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "iDisplayStart ":20,
            "oLanguage": {
                "sProcessing": "<img src='http://107.170.66.211/roadyo_live/sadmin/theme/assets/img/ajax-loader_dark.gif'>"
            },
            "fnInitComplete": function() {
                //oTable.fnAdjustColumnSizing();
                 $('#big_table_processing').hide();
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
    
        
        $('.changeMode').click(function () {

            var table = $('#big_table');
            
            
            
           $('.whenclicked li').click(function (){
            // alert($(this).attr('id'));
            
            if($(this).attr('id') == 1){
                $('#delete').show();
                 $('#btnStickUpSizeToggler').show();
                 $('#deletes').hide();
                  $('#vehiclemodal_addbutton').hide();
                  
//                   $('#big_table').find('td:eq(2),th:eq(2)').hide();
//            $('#big_table').find('td:eq(3),th:eq(3)').hide();
            }
            else if($(this).attr('id') == 2){
                  $('#delete').hide();
                 $('#btnStickUpSizeToggler').hide();
                 $('#deletes').show();
                  $('#vehiclemodal_addbutton').show();
//                  
//                    $('#big_table').find('td:eq(1),th:eq(1)').hide();
//                   $('#big_table').find('td:eq(2),th:eq(2)').show();
//            $('#big_table').find('td:eq(3),th:eq(3)').show();
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


<script>
    $(document).ready(function () {
          $("#define_page").html("Vehicle Model");
          
           $('.vehicle_models').addClass('active');
           $('.vehicle_models').attr('src',"<?php echo base_url();?>/theme/icon/vehicele model_on.png");
//        $('.vehicle_models_thumb').addClass("bg-success");
          
// 
        
        
        $('#btnStickUpSizeToggler').click(function () {
            var size = $('input[name=stickup_toggler]:checked').val()
            var modalElem = $('#myModal');
            if (size == "mini") {
                $('#modalStickUpSmall').modal('show')
            } else {
                $('#myModal').modal('show')
                if (size == "default") {
                    modalElem.children('.modal-dialog').removeClass('modal-lg');
                } else if (size == "full") {
                    modalElem.children('.modal-dialog').addClass('modal-lg');
                }
            }
        });
        
           $('.error-box-class').keypress(function(){
            $('.error-box').text('');
        });

        
        
        
        $('#vehiclemodal_addbutton').click(function () {
            var size = $('input[name=stickup_toggler]:checked').val()
            var modalElem = $('#myModals');
            if (size == "mini") {
                $('#modalStickUpSmall').modal('show')
            } else {
                $('#myModals').modal('show')
                if (size == "default") {
                    modalElem.children('.modal-dialog').removeClass('modal-lg');
                } else if (size == "full") {
                    modalElem.children('.modal-dialog').addClass('modal-lg');
                }
            }
        });
        
        
        $('#searchData').click(function () {


            var dateObject = $("#start").datepicker("getDate"); // get the date object
            var st = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format
            var dateObject = $("#end").datepicker("getDate"); // get the date object
            var end = dateObject.getFullYear() + '-' + (dateObject.getMonth() + 1) + '-' + dateObject.getDate();// Y-n-j in php date() format

            $('#createcontrollerurl').attr('href', '<?php echo base_url() ?>index.php/superadmin/Get_dataformdate/' + st + '/' + end);

        });

        $('#search_by_select').change(function () {


            $('#atag').attr('href', '<?php echo base_url() ?>index.php/superadmin/search_by_select/' + $('#search_by_select').val());

            $("#callone").trigger("click");
        });


        $("#chekdel").click(function () {
            var val = [];
            $('.checkbox:checked').each(function (i) {
                val[i] = $(this).val();
            });

            if (val.length > 0) {
                if (confirm("Are you sure to Delete " + val.length + " Vehicle")) {
                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/deleteVehicles",
                        type: "POST",
                        data: {val: val},
                        dataType: 'json',
                        success: function (result) {
                            alert(result.affectedRows)

                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                            });
                        }
                    });
                }

            } else {
                alert("Please mark any one of options");
            }

        });
        
        $("#delete").click(function(){
        
                     $("#display-data").text("");
         
                    var val = $('.checkbox:checked').map(function () {
                       return this.value;
                   }).get();
            
                        if (val.length > 0) {
                
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
                       $("#errorboxdata").text(<?php echo json_encode(POPUP_VEHICLE_MAKE);?>);

                       $("#confirmed").click(function(){
                          // if (confirm("Are you sure to Delete " + val.length + " Vehicle")) {

                           $.ajax({
                               url: "<?php echo base_url('index.php/superadmin') ?>/deletevehicletypemodel",
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
                       //}

                   });
            }
            else
            {
          //  alert("select atleast one vehicle type");
                 $("#display-data").text(<?php echo json_encode(POPUP_VEHICLETYPE_ATLEAST);?>);
          }
           
        });
        
        
         $("#deletes").click(function(){
         
          $("#display-data").text("");
         
             var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).
                    get();
                    
            if (val.length > 0) {
                
                
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
                  $("#errorboxdatas").text(<?php echo json_encode(POPUP_VEHICLETYPE);?>);

                       $("#confirmeds").click(function(){
                
                //if (confirm("Are you sure to Delete " + val.length + " vehicle make")) 
                {
                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/deletevehiclemodal",
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
//                alert("select atleast one vehicle type");
             $("#display-data").text(<?php echo json_encode(POPUP_VEHICLETYPE_ATLEAST);?>);
            }

        });
        
        
        
        $("#insert").click(function () {
        $("#insert_data").text("");
           var text = /^[a-zA-Z ]*$/;
           var typename = $("#typename").val();

            if (typename == "" || typename == null)
            {
//                alert("please enter the type name");
                $("#insert_data").text(<?php echo json_encode(POPUP_VEHICLEMODEL_TYPENAMES); ?>);
            }
            else if (!text.test(typename))
            {
//                alert("please enter type name as text");
                $("#insert_data").text(<?php echo json_encode(POPUP_VEHICLEMODEL_TEXT); ?>);
            }
           
            else
            {



                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/inserttypename",
                    type: 'POST',
                    data: {
                       typename:typename
                    },
                    dataType: 'JSON',
                    success: function (response)
                    {


//                        alert(response.msg);
                        
                        $("#typename").val('');
                         $(".close").trigger('click');
                         location.reload();
                    }
                   
                    

                });
            }
       });
        
        $("#inserts").click(function () {
        $("#inserts-data").text("");

           var text = /^[a-zA-Z ]*$/;
           var typeid = $("#typeid").val();
           var modal = $("#modalname").val();

            if (typeid == "0")
            {
           //     alert("please select type name");
                 $("#inserts-data").text(<?php echo json_encode(POPUP_VEHICLEMODEL_TYPENAME); ?>);
            }
//            else if(typeid !=="0"){
//                 $("#inserts-data").text("");
//             }
                
            else if (modal =="" || modal== null)
            {
         //       alert("please enter modal name");
                 $("#inserts-data").text(<?php echo json_encode(POPUP_VEHICLEMODEL_MODELNAME); ?>);
            }
           
            else
            {

//                $('#loadingmessage').show();

                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/insertmodal",
                    type: 'POST',
                    data: {
                       typeid:typeid,
                       modal:modal
                    },
                    dataType: 'JSON',
                    success: function (response)
                    {

//                     $('#loadingmessage').hide();
//                        alert(response.msg);
                        
                       $("#typeid").val('');
                        $("#modalname").val('');
                        
                          $(".close").trigger('click');
                         location.reload();
                    }
//                     $(".close").trigger('click');

                });
            }

        });

        
        

    });

</script>

<style>
    .exportOptions{
        display: none;
    }
</style>
<div class="page-content-wrapper"style="padding-top: 20px">
    <!-- START PAGE CONTENT -->
    <div class="content">
        
        
        
        <div class="brand inline" style="  width: auto;
             font-size: 20px;
             color: gray;
             margin-left: 30px;padding-top: 20px;">
           <!--                    <img src="--><?php //echo base_url();  ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();  ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();  ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

           <strong style="color:#0090d9;">VEHICLE MODELS</strong><!-- id="define_page"-->
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
                <!--                    <li><a href="#" class="active">--><?php //echo $vehicle_status;  ?><!--</a>-->
                <!--                    </li>-->
                <!--                </ul>-->
                <!--                <!-- END BREADCRUMB -->
                <!--            </div>-->


<!--                <div id='loadingmessage' style='display:none'>
                    <img src=http://postmenu.cloudapp.net/Taxi/superadmin/dashboard/../../pics/10168386196594.jpg'/>
                    http://postmenu.cloudapp.net/Taxi/superadmin/dashboard/../../pics/user.jpg
                     </div>-->



                <div class="panel panel-transparent ">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-fillup  bg-white whenclicked">
                        <li id="1" class="tabs_active  <?php echo ($status == 1 ? "active" : ""); ?>">
                            <a  class="changeMode"  href="<?php echo base_url(); ?>index.php/superadmin/vehicle_models/1"><span><?php echo LIST_VEHICLEMAKE;?></span></a>
                        </li>
                        <li id="2" class="tabs_active  <?php echo ($status == 2 ? "active" : ""); ?>">
                            <a  class="changeMode"  href="<?php echo base_url(); ?>index.php/superadmin/vehicle_models/2"><span><?php echo LIST_VEHICLEMODELS;?> </span></a>
                        </li>





                        <?php if($status == 1){?>
                             <div class="pull-right m-t-10"><button class="btn btn-primary btn-cons" id="delete"><?php echo BUTTON_DELETE;?></button></div>
                             <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="btnStickUpSizeToggler" ><?php echo BUTTON_ADD;?></button></div>
                           
                        <?php } ?>
                           

                      <?php if($status == 2){?>
                             <div class="pull-right m-t-10"><button class="btn btn-primary btn-cons" id="deletes"><?php echo BUTTON_DELETE;?></button></div>
                              <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="vehiclemodal_addbutton" ><?php echo BUTTON_ADD;?></button></div>
                        

                        <?php } ?>
                       
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

                                                <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="Search "> </div>
                                            </div>
                                            <div class="dltbtn">

                                                
                                            </div>
                                    
                                  




                                </div>
                               &nbsp;
                                <div class="panel-body">
                                     <?php echo $this->table->generate(); ?>
                                    
<!--                                    <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" id="big_table" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">
                                                <thead>

                                                    <tr role="row">
                                                        <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 87px;font-size:15px"> <?php echo VEHICLEMAKE_TABLE_ID;?></th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo VEHICLEMAKE_TABLE_TYPENAME;?></th>
                                               
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo VEHICLEMAKE_TABLE_SELECT;?></th>
                                                    </tr>


                                                </thead>
                                                <tbody>

                                                     <?php
                                                    $i = '1';
                                                    foreach ($vehiclemake as $result) {
                                                        ?>


                                                        <tr role="row"  class="gradeA odd">
                                                            <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->id; ?></p></td>
                                                            <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->vehicletype; ?></p></td>
                                                          
                                                              <td class="v-align-middle">
                                                                    <div class="checkbox check-primary">
                                                                        <input type="checkbox" value="<?php echo $result->id; ?>" id="checkbox<?php echo $i; ?>" class="checkbox">
                                                                        <label for="checkbox<?php echo $i; ?>">Mark</label>
                                                                    </div>
                                                                </td>
                                                            
                                                        </tr>
                                                        <?php
                                                        $i++;
                                                    }
                                                    //                                            
                                                    ?>
                                                </tbody>
                                            </table></div>
                               
                                        
                                        
                                        <?php if($status==2) { ?>               
                                        
                                        
                                        <div class="panel-body">
                                    <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" id="big_table" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">
                                                <thead>

                                                    <tr role="row">
                                                        <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 87px;font-size:15px"> <?php echo VEHICLEMODEL_TABLE_ID;?></th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo VEHICLEMODEL_TABLE_MODELID;?></th>
                                               
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo VEHICLEMODEL_TABLE_MAKE;?></th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo VEHICLEMODEL_TABLE_MODEL;?></th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo VEHICLEMODEL_TABLE_SELECT;?></th>
                                                       
                                                    
                                                    </tr>


                                                </thead>
                                                <tbody>
                                                    
                                                    
                                                    <?php
                                                    $i = '1';
                                                    foreach ($vehiclemodal as $result) {
                                                        ?>


                                                        <tr role="row"  class="gradeA odd">
                                                            <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->id; ?></p></td>
                                                            <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->vehicletypeid; ?></p></td>
                                                              <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->vehicletype; ?></p></td>
                                                            <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->vehiclemodel; ?></p></td>
                                                          
                                                              <td class="v-align-middle">
                                                                    <div class="checkbox check-primary">
                                                                        <input type="checkbox" value="<?php echo $result->id; ?>" id="checkbox<?php echo $i; ?>" class="checkbox">
                                                                        <label for="checkbox<?php echo $i; ?>">Mark</label>
                                                                    </div>
                                                                </td>
                                                            
                                                        </tr>
                                                        <?php
                                                        $i++;
                                                    }
                                                    //                                            
                                                    ?>
                                                </tbody>
                                            </table>
                                        <?php }?>



                                        <div class="row"></div></div>
-->                                </div>
                            </div>
<!--                             END PANEL -->
                        </div>
                    </div>
                </div><!--









-->            </div>


        </div><!--
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
            
 
            
  
<div class="modal fade stick-up" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">

                <div class="modal-header">

                    <div class=" clearfix text-left">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                        </button>

                    </div>
                    <h3><?php echo VEHICLEMAKE_ADDVEHICLE;?></h3>
                </div>
                <br>
                <br>
               

               
                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-3 control-label" style="font-size: medium;margin-top: 7px"><?php echo FIELD_VEHICLEMODEL_TYPENAME;?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="typename" name="typename"  class="form-control error-box-class" placeholder="">
                        </div>
                    </div>

                    <br>
                    <br>

                    


                    <div class="row">
                        <div class="col-sm-4" ></div>
                        <div class="col-sm-4 error-box" id="insert_data" ></div>
                        <div class="col-sm-4" >
                            <button type="button" class="btn btn-primary pull-right" id="insert" ><?php echo BUTTON_ADD;?></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

        
        
        <div class="modal fade stick-up" id="myModals" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">

                <div class="modal-header">

                    <div class=" clearfix text-left">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                        </button>

                    </div>
                    <h3> <?php echo VEHICLEMODEL_ADDVEHICLE;?> </h3>
                </div>
                <br>
                <br>
               
                
                
                 <div class="form-group" class="formex">
                    <label for="fname" class="col-sm-3 control-label" id=""><?php echo FIELD_VEHICLEMODEL_SELECTTYPE;?><span style="color:red;font-size: 18px">*</span></label>
                    <div class="col-sm-6">

                        <select id="typeid" name="country_select"  class="form-control error-box-class" >
                            <option value="0">Select vehicle type</option>
                            <?php
                            foreach ($vehiclemake as $result) {

                                echo "<option value=" . $result->id . ">" . $result->vehicletype . "</option>";
                            }
                            ?>

                        </select>
                    </div>
                </div>
                    
                    <br>
                    <br>
                    
                    
                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLEMODEL_MODAL;?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="modalname" name="typename"  class="form-control" placeholder="">
                        </div>
                    </div>
                    


                    <div class="row">
                        <div class="col-sm-4" ></div>
                        <div class="col-sm-4 error-box" id="inserts-data" ></div>
                        <div class="col-sm-4" >
                            <button type="button" class="btn btn-primary pull-right" id="inserts" ><?php echo BUTTON_ADD;?></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
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
                             
                                <div class="error-box" id="errorboxdata" style="font-size: large;text-align:center"><?php echo VEHICLEMODEL_DELETE;?></div>
                               
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
                             
                                <div class="error-box" id="errorboxdatas" style="font-size: large;text-align:center"><?php echo VEHICLEMODEL_DELETE;?></div>
                               
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
