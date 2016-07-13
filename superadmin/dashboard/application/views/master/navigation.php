


<script>
$(document).ready(function () {
$('#changemasterpassword').click(function () {
            $("#display-data").text("");
            var size = $('input[name=stickup_toggler]:checked').val()
            var modalElem = $('#mastermodal');
            if (size == "mini") {
                $('#modalStickUpSmall').modal('show')
            } else {
                $('#mastermodal').modal('show')
                if (size == "default") {
                    modalElem.children('.modal-dialog').removeClass('modal-lg');
                } else if (size == "full") {
                    modalElem.children('.modal-dialog').addClass('modal-lg');
                }
            }
        });


        $("#changemasterpass").click(function () {
            $("errormasterpass").text("");

            var newpass = $("#masternewpass").val();
            var confirmpass = $("#masterconfirmpass").val();
            var reg = /^\S*(?=\S*[a-zA-Z])(?=\S*[0-9])\S*$/;    //^[-]?(?:[.]\d+|\d+(?:[.]\d*)?)$/;



            if (newpass == "" || newpass == null)
            {
//                alert("please enter the new password");
                $("#errormasterpass").text(<?php echo json_encode(POPUP_PASSENGERS_PASSNEW); ?>);
            }
            else if (!reg.test(newpass))
            {
//                alert("please enter the password with atleast one chareacter and one letter");
                $("#errormasterpass").text(<?php echo json_encode(POPUP_PASSENGERS_PASSVALID); ?>);
            }
            else if (confirmpass == "" || confirmpass == null)
            {
//                alert("please confirm the password");
                $("#errormasterpass").text(<?php echo json_encode(POPUP_PASSENGERS_PASSCONFIRM); ?>);
            }
            else if (confirmpass != newpass)
            {
//                alert("please confirm the same password");
                $("#errormasterpass").text(<?php echo json_encode(POPUP_PASSENGERS_SAMEPASSCONFIRM); ?>);
            }
            else
            {

                $.ajax({
                    url: "<?php echo base_url('index.php/masteradmin') ?>/changemasterpassword",
                    type: 'POST',
                    data: {
                        newpass: newpass
//                        val: $('.checkbox:checked').val()
                    },
                    dataType: 'JSON',
                    success: function (response)
                    {
                       
                      

                            $(".close").trigger('click');

                            var size = $('input[name=stickup_toggler]:checked').val()
                            var modalElem = $('#mastermsgpassword');
                            if (size == "mini")
                            {
                                $('#modalStickUpSmall').modal('show')
                            }
                            else
                            {
                                $('#mastermsgpassword').modal('show')
                                if (size == "default") {
                                    modalElem.children('.modal-dialog').removeClass('modal-lg');
                                }
                                else if (size == "full") {
                                    modalElem.children('.modal-dialog').addClass('modal-lg');
                                }
                            }
                            
                              if (response.flag == 0) {
                                  
                            $("#errorboxdatass").text(<?php echo json_encode(POPUP_DRIVERS_NEWPASSWORD); ?>);
                            $("#confirmedss").hide();
                             $("#masternewpass").val('');
                            $("#masterconfirmpass").val('');
                        }
                           else if (response.flag == 1) {
                                  
                            $("#errorboxdatass").text(<?php echo json_encode(POPUP_DRIVERS_ERRPASSWORD); ?>);
                            $("#confirmedss").hide();
                             $("#masternewpass").val('');
                            $("#masterconfirmpass").val('');
                        }


                    }

                });
            }

        });


});








</script>


<div class="page-container" xmlns="http://www.w3.org/1999/html">
    <!-- START PAGE HEADER WRAPPER -->
    <!-- START HEADER -->
    <div class="header ">
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
                <div class="brand inline" style="width: 671px;font-size: 27px;color: gray;">
<!--                    <img src="--><?php //echo base_url(); ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url(); ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url(); ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

                   <strong> DRIVER MANAGEMENT CONSOLE </strong>
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
                   
                    <img id="nav_user_img" data-toggle="dropdown" 
                         style="border-radius: 28px;margin-top: 4px;margin-right: 7px;cursor: pointer;"
                         data-hover="dropdown"
                         src="<?php echo base_url().'../pics/hdpi/'.$this->session->userdata("profile_pic"); ?>" 
                         alt="" data-src="<?php echo base_url().'../../pics/'.$this->session->userdata("profile_pic"); ?>" 
                         data-src-retina="<?php echo base_url().'../../pics/'.$this->session->userdata("profile_pic"); ?>"
                         width="32" height="32">
                    
                        
                        <ul class="dropdown-menu" style="margin-left: -135px;margin-top: 14px;background: #ffffff;width: 171px;">
                        <li>
                            <div class="row center-margin m-b-10">
                                <div class="col-xs-2 text-center">
                                    <i class="fs-14 sl-user-follow"></i>
                                </div>
                                <div class="col-xs-8 text-center">
                                    <a tabindex="-1" href="<?php echo base_url(); ?>index.php/masteradmin/profile">My Profile</a>
                                </div>
                            </div>

                        </li>
                        <li class="divider"></li>

                         
                        <li>

                            <center><a id="changemasterpassword">Change password</a></center>
                        </li>
                         
                         <li class="divider"></li>
                        <li>

                        <center><a tabindex="-1" href="<?php echo base_url(); ?>index.php/masteradmin/Logout">Logout</a></center>
                        </li>

                    </ul>
                </div>

            </div>
            <!-- END User Info-->
        </div>
    </div>
    



<div class="modal fade stick-up" id="mastermodal" tabindex="-1" role="dialog" aria-hidden="true">

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
                            <input type="text"  id="masternewpass" name="latitude"  class="form-control" placeholder="eg:g3Ehadd">
                        </div>
                    </div>

                    <br>
                    <br>

                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_CONFIRMPASWORD; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="masterconfirmpass" name="longitude" class="form-control" placeholder="H3dgsk">
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-4" ></div>
                        <div class="col-sm-4 error-box" id="errormasterpass"></div>
                        <div class="col-sm-4" >
                            <button type="button" class="btn btn-primary pull-right" id="changemasterpass" ><?php echo BUTTON_SUBMIT; ?></button>
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
    
    
<div class="modal fade stick-up" id="mastermsgpassword" tabindex="-1" role="dialog" aria-hidden="true">
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

