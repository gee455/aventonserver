<?php
date_default_timezone_set('UTC');
$rupee = "$";
//error_reporting(E_ALL);
?>
<style>
    .ui-autocomplete{
        z-index: 5000;
    }
    #selectedcity,#companyid{
        display: none;
    }
    .imageborder{
            border-radius: 50%;
        }

    .ui-menu-item{cursor: pointer;background: black;color:white;border-bottom: 1px solid white;width: 200px;}
</style>
<script>
 var status = '<?php echo $status;?>';
    $(document).ready(function () {
       
       
            if(status == 4)
            {
                $('#inactive').hide();
                 $('#active').show();
                
            }
           


        $("#define_page").html("Passengers");

        $('.passengers').addClass('active');
        $('.passengers').attr('src', "<?php echo base_url(); ?>/theme/icon/passanger_on.png");
//        $('.passengers_thumb').addClass("bg-success");


        var table = $('.tableWithSearch1');
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

//         $('#search-table').keyup(function() {
//                table.fnFilter($(this).val());
//            });


        $('#btnStickUpSizeToggler').click(function () {
            $("#display-data").text("");
            var val = [];
            $('.checkbox:checked').each(function (i) {
                val[i] = $(this).val();
            });



            if (val.length == 0) {
                //     alert("please select any one to reset the password");
                $("#display-data").text(<?php echo json_encode(POPUP_PASSENGERS_ANYONEPASS); ?>);
            }
            else if (val.length > 1) {
                //        alert("please select only one to reset the password");
                $("#display-data").text(<?php echo json_encode(POPUP_PASSENGERS_ONLYONEPASS); ?>);
            }
            else
            {




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

            }
        });

            //Delete a passenger
          $('#delete_passenger').click(function ()
          {
           
              $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();
            if (val.length > 0) {

                //      if (confirm("Are you sure to inactive " + val.length + " passengers"))
                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#delete_pass');
                if (size == "mini")
                {
                    $('#modalStickUpSmall').modal('show')
                }
                else
                {
                    $('#delete_pass').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    }
                    else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
               

                $("#conform_delete").click(function () {
                     $(".close").trigger('click');

                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/deletepassengers",
                        type: "POST",
                        data: {val: val},
                        dataType: 'json',
                        success: function (result)
                        {

                           window.location= '<?php echo base_url() . "index.php/superadmin/passengers/3";?>';
                                  
                        }
                    });

                     
                });
            }
            else
            {
                //      alert("select atleast one passenger");
                $("#display-data").text(<?php echo json_encode(POPUP_PASSENGERS_ATLEAST); ?>);
            } 
              
          });



        $("#insertpass").click(function () {
            $("#errors").text("");
            var newpass = $(".newpass").val();
            var confirmpass = $(".confirmpass").val();
            var reg = /^\S*(?=\S*[a-zA-Z])(?=\S*[0-9])\S*$/;    //^[-]?(?:[.]\d+|\d+(?:[.]\d*)?)$/;



            if (newpass == "" || newpass == null)
            {
//                alert("please enter the new password");
                $("#errorspass").text(<?php echo json_encode(POPUP_PASSENGERS_PASSNEW); ?>);
            }
//            else if (!reg.test(newpass))
//            {
////                alert("please enter the password with atleast one chareacter and one letter");
//                $("#errorspass").text(<?php echo json_encode(POPUP_PASSENGERS_PASSVALID); ?>);
//            }
//            else if (confirmpass == "" || confirmpass == null)
//            {
////                alert("please confirm the password");
//                $("#errorspass").text(<?php echo json_encode(POPUP_PASSENGERS_PASSCONFIRM); ?>);
//            }
            else if (confirmpass != newpass)
            {
                //  alert("please confirm the same password");
                $("#errorspass").text(<?php echo json_encode(POPUP_PASSENGERS_SAMEPASSCONFIRM); ?>);
            }
            else
            {

                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/insertpass",
                    type: 'POST',
                    data: {
                        newpass: newpass,
                        val: $('.checkbox:checked').val()
                    },
                    dataType: 'JSON',
                    success: function (response)
                    {
                        $(".close").trigger("click");

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
                        if(response.flag == 1){
                        $("#errorboxdatas").text(<?php echo json_encode(POPUP_DRIVERS_NEWPASSWORD); ?>);
                         $("#confirmeds").hide();
                         $(".newpass").val("");
                            $(".confirmpass").val("");
                     }

                    }

                });
            }

        });

        $('.error-box-class').keypress(function () {
            $('.error-box').text('');
        });


        $("#inactive").click(function () {
            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();
            if (val.length > 0) {

                //      if (confirm("Are you sure to inactive " + val.length + " passengers"))
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
                $("#errorboxdata").text(<?php echo json_encode(POPUP_PASSENGERS_DEACTIVAT); ?>);

                $("#confirmed").click(function () {

//                    $.ajax({
//                        url: "<?php //echo base_url('index.php/superadmin') ?>/inactivepassengers",
//                        type: "POST",
//                        data: {val: val},
//                        dataType: 'json',
//                        success: function (result)
//                        {
//
//                            $('.checkbox:checked').each(function (i) {
//                                $(this).closest('tr').remove();
//                            });
//                            $(".close").trigger('click');
//                            
//                                  
//                        }
//                    });

                        if (val == '') {
                            alert('Please select  atleast one passenger in the list');
                        } else{
                             $(".close").trigger('click');
                            $.ajax({
                                type: "POST",
                                url: "../../../../../admin/activate_reject.php",
                                data: {item_type: 2, to_do: 4, item_list: val},
                                dataType: "JSON",
                                success: function (result) {
                                    alert(result.message);
                                    if (result.flag == 0) {
                                     window.location = "<?php echo base_url() ?>index.php/superadmin/passengers/3";
//                                        $('.custom_check').each(function () {
////                                            if ($(this).is(':checked') == true) {
////                                                $('#pat_rows' + $(this).attr('dat')).remove();
////                                            }
//                                        });
                                    }
                                }
                            });

                        }
                });
            }
            else
            {
                //      alert("select atleast one passenger");
                $("#display-data").text(<?php echo json_encode(POPUP_PASSENGERS_ATLEAST); ?>);
            }

        });


        $("#active").click(function () {
            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();
            
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
                $("#errorboxdatas").text(<?php echo json_encode(POPUP_PASSENGERS_ACTIVAT); ?>);

                $("#confirmeds").click(function () {

//                    $.ajax({
//                        url: "<?php //echo base_url('index.php/superadmin') ?>/activepassengers",
//                        type: "POST",
//                        data: {val: val},
//                        dataType: 'json',
//                        success: function (result)
//                        {
//
//                            $('.checkbox:checked').each(function (i) {
//                                $(this).closest('tr').remove();
//                            });
//                            $(".close").trigger('click');
//                        }
//                    });
                            if (val == '') {
                            alert('Please select  atleast one passenger in the list');
                        } else{
                             $(".close").trigger('click');
                            $.ajax({
                                type: "POST",
                                url: "../../../../../admin/activate_reject.php",
                                data: {item_type: 2, to_do: 3, item_list: val},
                                dataType: "JSON",
                                success: function (result) {
                                    alert(result.message);
                                    if (result.flag == 0) {
                                     window.location = "<?php echo base_url() ?>index.php/superadmin/passengers/3";
//                                        $('.custom_check').each(function () {
////                                            if ($(this).is(':checked') == true) {
////                                                $('#pat_rows' + $(this).attr('dat')).remove();
////                                            }
//                                        });
                                    }
                                }
                            });

                        }


                });
            }
            else
            {
                // alert("select atleast one passenger");
                $("#display-data").text(<?php echo json_encode(POPUP_PASSENGERS_ATLEAST); ?>);
            }

        });




    });


</script>

<script type="text/javascript">
    $(document).ready(function () {




        var status = '<?php echo $status; ?>';

        if (status == 3) {
            $('#inactive').show();
            $('#active').hide();
            $('#btnStickUpSizeToggler').show();
             $("#display-data").text("");
        }

        $('.whenclicked li').click(function () {
            // alert($(this).attr('id'));
            if ($(this).attr('id') == 3) {
                $('#inactive').show();
                $('#active').hide();
                $('#btnStickUpSizeToggler').show();
                 $("#display-data").text("");

            }

            else if ($(this).attr('id') == 1) {
                $('#inactive').hide();
                $('#active').show();
                $('#btnStickUpSizeToggler').show();
                 $("#display-data").text("");
            }


        });



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
            "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/dt_passenger/' + status,
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
                },
            };

            $('.tabs_active').removeClass('active');

            $(this).parent().addClass('active');
            
            $('#inactive').hide();
            $('#active').show();



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
<div class="page-content-wrapper"style="padding-top: 20px">
    <!-- START PAGE CONTENT -->
    <div class="content" >

        <div class="brand inline" style="  width: auto;
             font-size: 20px;
             color: gray;
             margin-left: 30px;padding-top: 20px;">
           <!--                    <img src="--><?php //echo base_url();     ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();     ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();     ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

            <strong style="color:#0090d9;">PASSENGERS</strong><!-- id="define_page"-->
        </div>
        <div id="test"></div>
        <!-- START JUMBOTRON -->
        <div class="jumbotron" data-pages="parallax">
            <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">

                <div class="panel panel-transparent ">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-fillup  bg-white whenclicked">

                        <li id="3" class="tabs_active <?php echo ($status == 3 ? "active" : ""); ?>" style="cursor:pointer">
                            <a class="changeMode"  data="<?php echo base_url(); ?>index.php/superadmin/dt_passenger/3"><span><?php echo LIST_ACCEPT; ?></span></a>
                        </li>
                        <li id="4" class="tabs_active <?php echo ($status == 4 ? "active" : ""); ?>" style="cursor:pointer">
                            <a  class="changeMode"  data="<?php echo base_url(); ?>index.php/superadmin/dt_passenger/4"><span><?php echo LIST_REJECT; ?> </span></a>
                        </li>

                        <div class="pull-right m-t-10" > <button class="btn btn-primary btn-cons" id="delete_passenger" ><?php echo BUTTON_DELETE_PASSENGER; ?></button></div>
                        
                        <div class="pull-right m-t-10" > <button class="btn btn-primary btn-cons" id="btnStickUpSizeToggler" ><?php echo BUTTON_RESETPASSWORD; ?></button></div>

                        <div class="pull-right m-t-10" > <button class="btn btn-primary btn-cons" id="inactive" ><?php echo BUTTON_DEACTIVE; ?></button></div>


                        <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="active"><?php echo BUTTON_ACTIVE; ?></button></a></div>

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

                                        <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right"  placeholder="<?php echo SEARCH; ?> "> </div>
                                    </div>
                                    <div class="dltbtn">

                                    </div>
<!--                                    <input class="hidden" value="" id="passenger_id"/> -->

                                </div>
                                 &nbsp;
                                <div class="panel-body">


                                    <?php echo $this->table->generate(); ?>
<!--                                    <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" class="tableWithSearch1" id="big_table" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">
            <thead>

                <tr role="row">
                    <th  tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="descending" aria-label="Rendering engine: activate to sort column ascending" style="width: 100px;font-size:15px"><?php echo PASSENGERS_TABLE_PASSENGERID; ?></th>
                    <th  tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 80px;font-size:15px"><?php echo PASSENGERS_TABLE_FIRSTNAME; ?></th>
                    <th tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 85px;font-size:15px"><?php echo PASSENGERS_TABLE_LASTNAME; ?></th>
                    <th  tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo PASSENGERS_TABLE_MOBILE; ?></th>
                    <th  tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 87px;font-size:15px"><?php echo PASSENGERS_TABLE_EMAIL; ?></th>
                    <th  tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 131px;font-size:15px"><?php echo PASSENGERS_TABLE_REGDATE; ?></th>
                    <th tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 98px;font-size:15px"><?php echo PASSENGERS_TABLE_DEVICETYPE; ?></th>

                    <th  tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 87px;font-size:15px"><?php echo PASSENGERS_TABLE_ZIPCODE; ?></th>
                    <th  tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:87px;font-size:15px"><?php echo PASSENGERS_TABLE_PROFILEPIC; ?></th>
                    <th  tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:75px;font-size:15px"><?php echo PASSENGERS_TABLE_STATUS; ?></th>
                    <th  tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 87px;font-size:15px"><?php echo PASSENGERS_TABLE_SELECT; ?></th>

                </tr>


            </thead>
            <tbody>












                                    <?php
//                                                    $i = '1';
//                                                    foreach ($passenger_info as $result) {
//                                                        
                                    ?>
                
                
                                                                        <tr role="row"  class="gradeA odd">
                                                                            <td id="d_no" class="v-align-middle sorting_1"> <p><?php //echo $result->slave_id;    ?></p></td>
                                                                            <td id="d_no" class="v-align-middle sorting_1"> <p><?php //echo $result->first_name;    ?></p></td>
                                                                            <td class="v-align-middle"><?php //echo $result->last_name;    ?></td>
                                                                            <td class="v-align-middle"><?php //echo $result->phone;    ?></td>
                                                                            <td class="v-align-middle"><?php //echo $result->email;    ?></td>
                                                                            <td class="v-align-middle"><?php //echo $result->created_dt;    ?></td>
                
                                                                            <td class="v-align-middle"><?Php
//                                                    if ($result->dev_type == '1')
//                                                        echo "<img src=http://107.170.66.211/roadyo_live/Wko8TuOH/assets/iphone-logo.png' style='width: 35px;' />";
//                                                    else if ($result->dev_type == '2')
//                                                        echo "<img src='http://107.170.66.211/roadyo_live/Wko8TuOH/assets/android_icon.png' style='width: 35px;' />";
//                                                    else
//                                                        echo "Unavailable";
//                                                    
                                    ?></td>
                
                                                                            <td class="v-align-middle"><?php //echo $result->zipcode;   ?></td>
                                                                            <td class="v-align-middle"><?php
//                                                                if ($result->profile_pic == "") {
//
//                                                                    $st1 = "aa_default_profile_pic.gif";
//                                                                } else {
//                                                                    $st1 = $result->profile_pic;
//                                                                }
//                                                                
                                    ?><img src="http://107.170.66.211/roadyo_live/pics/hdpi/<?Php //echo $st1; ?>" height="54" width="55"></td>
                
                                                                            <td class="v-align-middle"><?php
//                                                                if ($result->status == "3") {
//                                                                    echo "active";
//                                                                } else {
//                                                                    echo "inactive";
//                                                                }
//                                                                
                                    ?></td>
                
                                                                            <td class="v-align-middle">
                                                                                <div class="checkbox check-primary">
                                                                                    <input type="checkbox" value="<?php //echo $result->slave_id;    ?>" id="checkbox<?php //echo $i;    ?>" class="checkbox">
                                                                                    <label for="checkbox<?php //echo $i;    ?>">Mark</label>
                                                                                </div>
                                                                            </td>
                
                                                                        </tr>
                                    <?php
//                                                        $i++;
//                                                    }
//                                                    //
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
                    <h3> <?php echo LIST_RESETPASSWORD_HEAD; ?></h3>
                </div>


                <br>
                <br>

                <div class="modal-body">




                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label"> <?php echo FIELD_NEWPASSWORD; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="newpass" name="latitude"  class="newpass form-control error-box-class" placeholder="eg:g3Ehadd">
                        </div>
                    </div>

                    <br>
                    <br>

                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_CONFIRMPASWORD; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="confirmpass" name="longitude" class="confirmpass form-control error-box-class" placeholder="H3dgsk">
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-4" ></div>
                        <div class="col-sm-4 error-box" id="errorspass" ></div>
                        <div class="col-sm-4" >
                            <button type="button" class="btn btn-primary pull-right" id="insertpass" ><?php echo BUTTON_SUBMIT; ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
    </button>
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

<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
</button>


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

<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
</button>


<div class="modal fade stick-up" id="delete_pass" tabindex="-1" role="dialog" aria-hidden="true">
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

                    <div class="error-box" id="errorboxdata" style="font-size: large;text-align:center"><?php echo DELETE_PASSENGER; ?></div>

                </div>
            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4" ></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4" >
                        <button type="button" class="btn btn-primary pull-right" id="conform_delete" ><?php echo BUTTON_YES; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
