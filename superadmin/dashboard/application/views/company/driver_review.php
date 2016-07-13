<?php
date_default_timezone_set('UTC');
$rupee = "$";
//error_reporting(0);


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
          $("#define_page").html("Driver Review");
            $('.driver_review').addClass('active');
            $('.driver_review').attr('src',"<?php echo base_url();?>/theme/icon/driver review_on.png");
//        $('.driver_review_thumb').addClass("bg-success");
          
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

        table.dataTable(settings);

             $('#search-table1').keyup(function () {
            table.fnFilter($(this).val());
        });


        
        $("#inactive").click(function () {
             $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).
              get();
//              var appid=$('#appid').val();
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
                  $("#errorboxdata").text(<?php echo json_encode(POPUP_PASSENGERS_DEACTIVATE);?>);

                       $("#confirmed").click(function(){
            
                    
                   
                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/inactivedriver_review",
                        type: "POST",
                        data: {val: val },
                        dataType: 'json',
                        success: function (result)
                        {

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
                  $("#display-data").text(<?php echo json_encode(POPUP_DRIVERREVIEW_ATLEAST);?>);
            }

        });


        $("#active").click(function () {
            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }). get();
            
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
                  $("#errorboxdatas").text(<?php echo json_encode(POPUP_PASSENGERS_ACTIVATE);?>);

                       $("#confirmeds").click(function(){
                    {
                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/activedriver_review",
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
                  $("#display-data").text(<?php echo json_encode(POPUP_DRIVERREVIEW_ATLEAST);?>);
            }

        });



    });

</script>


<script type="text/javascript">
    $(document).ready(function () {

       
//        alert('<?php // echo $status;      ?>');
        var status = '<?php echo $status; ?>';
        
         if (status == 1) {
                $('#inactive').show();
                $('#active').hide();
                  $("#display-data").text("");  
//               $('#big_table').find('td,th').first().remove();
               
             }
             
           
               $('#big_table_processing').show();
   
        var table = $('#big_table');

        var settings = {
            "autoWidth": false,
            "sDom": "<'table-responsive't><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "iDisplayLength": 20,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/datatable_driverreview/' + status,
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




        table.dataTable(settings);

        // search box for table
        $('#search-table').keyup(function () {
            table.fnFilter($(this).val());
        });


        $('.whenclicked li').click(function () {
            // alert($(this).attr('id'));\
             if  ($(this).attr('id') == 1) {
                $('#inactive').show();
                $('#active').hide();
                 $("#display-data").text("");
//                 $('#big_table').find('td:eq(6),th:eq(6)').hide();
             }

         else if ($(this).attr('id') == 2) {
            $('#active').show();     
            $('#inactive').hide();
             $("#display-data").text("");
//             $('#big_table').find('td:eq(6),th:eq(6)').show();
       
                
            }
           });

        $('.changeMode').click(function () {

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

        });

    });

    function refreshTableOnCityChange(){

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
<div class="page-content-wrapper"style="padding-top: 20px;">
    <!-- START PAGE CONTENT -->
    <div class="content">
        
        <div class="brand inline" style="  width: auto;
             font-size: 20px;
             color:#0090d9;
             margin-left: 30px;padding-top: 20px;">
           <!--                    <img src="--><?php //echo base_url();  ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();  ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();  ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

            <strong>DRIVER REVIEW</strong><!-- id="define_page"-->
        </div>
        <!-- START JUMBOTRON -->
        <div class="jumbotron" data-pages="parallax">
            <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">
               



                <div class="panel panel-transparent ">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-fillup  bg-white whenclicked">
                        <li id= "1" class="tabs_active <?php echo ($status == 1 ? "active" : ""); ?>" style="cursor:pointer">
                            <a  class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_driverreview/1"><span><?php echo LIST_ACTIVE;?></span></a>
                        </li>
                        <li id= "2" class="tabs_active <?php echo ($status == 2 ? "active" : ""); ?>" style="cursor:pointer">
                            <a   class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_driverreview/2"><span><?php echo LIST_INACTIVE;?> </span></a>
                        </li>
                               
                            <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="inactive"><?php echo BUTTON_INACTIVE;?></button></div>
                      
                            <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="active"><?php echo BUTTON_ACTIVE;?></button></div>
                       
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

                                                <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="<?php echo SEARCH;?>"> </div>
                                            </div>
                                            <div class="dltbtn">

                                        
                                    </div>




                                </div>
                                 &nbsp;
                                <div class="panel-body">
                                     <?php echo $this->table->generate(); ?>
                                    
<!--                                    <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch1" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">
                                                <thead>

                                                    <tr role="row">
                                                        <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 87px;font-size:15px"><?php echo DRIVERREVIEW_TABLE_SLNO;?></th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo DRIVERREVIEW_TABLE_DRIVERID;?></th>
                                                     
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo DRIVERREVIEW_TABLE_REVIEWDATE;?></th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 150px;font-size:15px"><?php echo DRIVERREVIEW_TABLE_DRIVERNAME;?></th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 131px;font-size:15px"><?php echo DRIVERREVIEW_TABLE_PASSENGERNAME;?></th>
                                                           <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo DRIVERREVIEW_TABLE_REVIEW;?></th>
                                                              <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo DRIVERREVIEW_TABLE_RATING;?></th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 98px;font-size:15px"><?php echo DRIVERREVIEW_TABLE_STATUS;?></th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 153px;font-size:15px"><?php echo DRIVERREVIEW_TABLE_SELECT;?></th>
                                                         </tr>


                                                </thead>
                                                <tbody>












                                                    <?php
                                                    $i = '1';
                                                    foreach ($driver_review as $result) {
                                                        ?>


                                                        <tr role="row"  class="gradeA odd">
                                                            <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $i; ?></p></td>
                                                            <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->appointment_id; ?></p></td>
                                                            
                                                             <td class="v-align-middle"><?php echo $result->appointment_dt; ?></td>
                                                            <td class="v-align-middle"><?php echo $result->mastername; ?></td>
                                                           
                                                           
                                                            <td class="v-align-middle"><?php echo $result->slave_id; ?></td>
                                                            <td class="v-align-middle"><?php echo $result->review; ?></td>
                                                            <td class="v-align-middle"><?php echo $result->star_rating; ?></td>
                                                            <td class="v-align-middle"><?php if($result->status==1) echo active; else echo inactive ; ?></td>
                                                              <td class="v-align-middle">
                                                                    <div class="checkbox check-primary">
                                                                        <input type="checkbox" value="<?php echo $result-> mas_id.','.$result->appointment_id; ?>" id="checkbox<?php echo $i; ?>" class="checkbox">
                                                                        <input type="hidden" value="<?php echo $result->appointment_id?>" id="appid"/>
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
