<style>
    .page-sidebar .sidebar-menu .menu-items > li > a {
        width: 189px !important;
    }
</style>
<script>

    $(document).ready(function () {

        $('#selectedcity').change(function () {

            $.ajax({
                url: "<?php echo base_url('index.php/superadmin') ?>/showcompanys",
                type: "POST",
                data: {city: $(this).val(), vt: '1'},
//                dataType: 'JSON',
                success: function (response)
                {
//                    $(this).val()
                    $("#companyid").html(response);
                    refreshTableOnActualcitychagne();

//                    $("#companyid").val("<?php //$this->session->userdata('company_id') ?>//");
                }
            });

        });
        $('#companyid').change(function () {

            $.ajax({
                url: "<?php echo base_url('index.php/superadmin') ?>/setcity_session",
                type: "POST",
                data: {company: $(this).val(), city: $('#selectedcity').val()},
//                dataType: 'JSON',
                success: function (response)
                {
                    refreshTableOnCityChange();
//                   alert('sessionset');
                }
            });

        });

        if ("<?php echo $this->session->userdata('city_id') ?>" != '0' || "<?php echo $this->session->userdata('company_id') ?>" != '0') {
//alert("<?php //echo  $this->session->userdata('city_id') ?>//");
//alert("<?php //echo  $this->session->userdata('company_id') ?>//");
            $('#selectedcity').val("<?php echo $this->session->userdata('city_id') ?>");
            $.ajax({
                url: "<?php echo base_url('index.php/superadmin') ?>/showcompanys",
                type: "POST",
                data: {city: "<?php echo $this->session->userdata('city_id') ?>"},
//                dataType: 'JSON',
                success: function (response)
                {
                    $("#companyid").html(response);
                    $("#companyid").val("<?php echo $this->session->userdata('company_id') ?>");
                }
            });


        }
        
         $('#btnStickUpSizeToggle').click(function () {
         
                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#myModal1');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#myModal1').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
         });
        
        
        
        
        $("#superpass").click(function () {
            $("errorpass").text("");

            var newpass = $("#newpass").val();
            var confirmpass = $("#confirmpass").val();
            var currentpassword = $("#currentpassword").val();
            
            var reg = /^\S*(?=\S*[a-zA-Z])(?=\S*[0-9])\S*$/;    //^[-]?(?:[.]\d+|\d+(?:[.]\d*)?)$/;
             
             
             
              if (currentpassword == "" || currentpassword == null)
            {
//                alert("please enter the new password");
                $("#errorpass").text(<?php echo json_encode(POPUP_PASSENGERS_CURRENTPASSWORD); ?>);
                
            }

            else if (newpass == "" || newpass == null)
            {
//                alert("please enter the new password");
                $("#errorpass").text(<?php echo json_encode(POPUP_PASSENGERS_PASSNEW); ?>);
            }
            else if (!reg.test(newpass))
            {
//                alert("please enter the password with atleast one chareacter and one letter");
                $("#errorpass").text(<?php echo json_encode(POPUP_PASSENGERS_PASSVALID); ?>);
            }
            else if (confirmpass == "" || confirmpass == null)
            {
//                alert("please confirm the password");
                $("#errorpass").text(<?php echo json_encode(POPUP_PASSENGERS_PASSCONFIRM); ?>);
            }
            else if (confirmpass != newpass)
            {
//                alert("please confirm the same password");
                $("#errorpass").text(<?php echo json_encode(POPUP_PASSENGERS_SAMEPASSCONFIRM); ?>);
            }
            else
            {

                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/editsuperpassword",
                    type: 'POST',
                    data: {
                        newpass: newpass,
                        val: $('.checkbox:checked').val(),
                        currentpassword:currentpassword
                    },
                    dataType: 'JSON',
                    success: function (response)
                    {
                       
                        if (response.flag == 2) {

                            $(".close").trigger('click');

                            var size = $('input[name=stickup_toggler]:checked').val()
                            var modalElem = $('#confirmmodelss');
                            if (size == "mini")
                            {
                                $('#modalStickUpSmall').modal('show')
                            }
                            else
                            {
                                $('#confirmmodelss').modal('show')
                                if (size == "default") {
                                    modalElem.children('.modal-dialog').removeClass('modal-lg');
                                }
                                else if (size == "full") {
                                    modalElem.children('.modal-dialog').addClass('modal-lg');
                                }
                            }

                            $("#errorboxdatass").text(<?php echo json_encode(POPUP_DRIVERS_ERRCURRENTPASSWORD); ?>);
                            $("#confirmedss").hide();


                            $("#newpass").val('');
                            $("#confirmpass").val('');
                            $("#currentpassword").val('');
                        }
                        
                        else if(response.flag == 0){
                             $(".close").trigger('click');

                            var size = $('input[name=stickup_toggler]:checked').val()
                            var modalElem = $('#confirmmodelss');
                            if (size == "mini")
                            {
                                $('#modalStickUpSmall').modal('show')
                            }
                            else
                            {
                                $('#confirmmodelss').modal('show')
                                if (size == "default") {
                                    modalElem.children('.modal-dialog').removeClass('modal-lg');
                                }
                                else if (size == "full") {
                                    modalElem.children('.modal-dialog').addClass('modal-lg');
                                }
                            }

                            $("#errorboxdatass").text(<?php echo json_encode(POPUP_DRIVERS_NEWPASSWORD); ?>);
                            $("#confirmedss").hide();


                            $("#newpass").val('');
                            $("#confirmpass").val('');
                            $("#currentpassword").val('');
                        }


//                        location.reload();

                    }

                });
            }

        });


        
        
        
        
    });
    
    
</script>





<div class="page-container" xmlns="https://www.w3.org/1999/html">
    <!-- START PAGE HEADER WRAPPER -->
    <!-- START HEADER -->
    <div class="header  nav nav-tabs nav-tabs-fillup  bg-white" style="
         height: 82px">
        <!-- START MOBILE CONTROLS -->
        <!-- LEFT SIDE -->
        <div class="pull-left full-height visible-sm visible-xs">
            <!-- START ACTION BAR -->
            <div class="sm-action-bar">
                <a href="#" class="btn-link toggle-sidebar" data-toggle="sidebar">
                    <span class="icon-set menu-hambuger"></span>
                </a>
            </div>
            <!-- END ACTION BAR -->
        </div>
        <!-- RIGHT SIDE -->
        <div class="pull-right full-height visible-sm visible-xs">
            <!-- START ACTION BAR -->
            <div class="sm-action-bar">
                <a href="#" class="btn-link" data-toggle="quickview" data-toggle-element="#quickview">
                    <span class="icon-set menu-hambuger-plus"></span>
                </a>
            </div>
            <!-- END ACTION BAR -->
        </div>
        <!-- END MOBILE CONTROLS -->
        <div class=" pull-left sm-table">
            <div class="header-inner">
                <div class="brand inline" style="  width: auto;
                     font-size: 27px;
                     color: gray;
                     margin-left: 100px;margin-right: 20px;margin-bottom: 12px; margin-top: 10px;" >
<!--                    <img src="--><?php //echo base_url();              ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();              ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();              ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

                    <strong ><?php echo Appname; ?> Super Admin Console</strong><!-- id="define_page"-->

                </div>

                <div class="brand inline" style="width:auto">
<!--                    <img src="--><?php //echo base_url();              ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();              ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();              ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->
                    <div class="form-group " >
                        <!--<label for="fname" class="col-sm-6 control-label" style="margin-top: 10px;font-size: 13px;padding:0px">SELECT CITY</label>-->
                        <div class="col-sm-8" style="width: auto;
                             paddingng: 0px;
                             margin-bottom: 10px;" >

                            <select id="selectedcity" name="company_select" class="form-control"  onchange="loadcompay()">
                                <!--<option value="0">Select city</option>-->
                                <?php $city = $this->db->query("select * from city_available ORDER BY City_Name ASC")->result(); ?>
                                <option value="0">All</option>
                                <?php
                                foreach ($city as $result) {

                                    echo '<option value="' . $result->City_Id . '">' . $result->City_Name . '</option>';
                                }
                                ?>   
                            </select>

                        </div>


                    </div>
<!--                   <strong>Roadyo Super Admin Console</strong> id="define_page"-->
                </div>

                <div class="brand inline"  style="width:auto" >
<!--                    <img src="--><?php //echo base_url();              ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();              ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();              ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->
                    <div class="form-group" >
                        <!--<label for="fname" class="col-sm-6 control-label" style="margin-top: 10px;font-size: 13PX;padding:0px">SELECT COMPANY</label>-->
                        <div class="col-sm-8" style="width: auto;
                             padding: 0px;
                             margin-bottom: 10px;" >

                            <select id="companyid" name="company_select" class="form-control"  >
                                <option value="0">Select company</option>
                            </select>

                        </div>
                    </div>
<!--                   <strong>Roadyo Super Admin Console</strong> id="define_page"-->
                </div>

            </div>


        </div>
        <div class=" pull-right">
            <div class="header-inner">
                <!--<a href="#" class="btn-link icon-set menu-hambuger-plus m-l-20 sm-no-margin hidden-sm hidden-xs" data-toggle="quickview" data-toggle-element="#quickview"></a>-->
            </div>
        </div>
        <div class=" pull-right">
            <!-- START User Info-->
            <div class="visible-lg visible-md m-t-10" id="caldw">
                <div class="pull-left p-r-10 p-t-10 fs-16 font-heading">
                    <span class="semi-bold"><?php echo $this->session->userdata("first_name"); ?></span>
                    <span class="text-master"><?php echo $this->session->userdata("last_name"); ?></span>
                </div>

                <div class="btn-group">
                    
                    <!--<h3>superadmin</h3>-->
                                <p data-toggle="dropdown" style="
                font-size: 20px;
                margin-top: 8%;
                cursor: pointer;
                color: #10CFBD; font-weight: bolder;
            ">SUPERADMIN </p>
                    <!--<p  data-toggle="dropdown" style="font-size:">superadmin </p>-->
<!--                        style="border-radius: 28px;margin-top: 4px;margin-right: 7px;cursor: pointer;" data-hover="dropdown" 
                         src="<?php echo PIC_PATH .'hdpi/'. $this->session->userdata("profile_pic"); ?>" alt="" data-src="http://107.170.66.211/roadyo_live/pics/hdpi/<?php echo $this->session->userdata("profile_pic"); ?>" 
                         data-src-retina="<?php echo PIC_PATH .'hdpi/'. $this->session->userdata("profile_pic"); ?>" width="32" height="32">-->
                        <ul class="dropdown-menu" style="margin-left: -135px;margin-top: 14px;background: #ffffff;width: 171px;">
                            <li>
<!--                                <div class="row center-margin m-b-10">
                                    <div class="col-xs-2 text-center">
                                        <i class="fs-14 sl-user-follow"></i>
                                    </div>
                                    <div class="col-xs-8 text-center">
                                        <a tabindex="-1" href="<?php echo base_url(); ?>index.php/superadmin/profile">My Profile</a>
                                    </div>
                                </div>-->
                                <div class="row center-margin m-b-10">
<!--                                    <div class="col-xs-2 text-center">
                                        <i class="fs-14 sl-user-follow"></i>
                                    </div>-->
                                    <!--<div class="col-xs-8 text-center">-->
                                        <center style="cursor:pointer;" id="btnStickUpSizeToggle">Change password</center>
                                    <!--</div>-->
                                </div>

                            </li>
                            <li class="divider"></li>

                            <li>

                                <center><a tabindex="-1" href="<?php echo base_url(); ?>index.php/superadmin/Logout">Logout</a></center>
                            </li>

                        </ul>
                </div>

            </div>
            <!-- END User Info-->
        </div>
    </div>
    
    
<div class="modal fade stick-up" id="myModal1" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <br>
                      <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label"> <?php echo FIELD_CURRENTPASSWORD; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="currentpassword" name="currentpassword"  class="form-control" placeholder="">
                        </div>
                    </div>

                        <br><br>
                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label"> <?php echo FIELD_NEWPASSWORD; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="newpass" name="latitude"  class="form-control" placeholder="eg:g3Ehadd">
                        </div>
                    </div>

                    <br>
                    <br>

                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_CONFIRMPASWORD; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="confirmpass" name="longitude" class="form-control" placeholder="H3dgsk">
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-4" ></div>
                        <div class="col-sm-4 error-box" id="errorpass"></div>
                        <div class="col-sm-4" >
                            <button type="button" class="btn btn-primary pull-right" id="superpass" ><?php echo BUTTON_SUBMIT; ?></button>
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

                    <div class="error-box" id="errorboxdatass" style="font-size: large;text-align:center"><?php echo VEHICLEMODEL_DELETE; ?></div>

                </div>
            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4" ></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4" >
                        <button type="button" class="btn btn-primary pull-right" id="confirmedss" ><?php echo BUTTON_OK; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
