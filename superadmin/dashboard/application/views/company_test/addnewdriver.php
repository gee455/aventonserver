<?php
$this->load->database();
?>
<style>
    .form-horizontal .form-group
    {
        margin-left: 13px;
    }

    .ui-autocomplete{
        z-index: 5000;
    }
    #selectedcity,#companyid{
        display: none;
    }
    
    .ui-menu-item{cursor: pointer;background: black;color:white;border-bottom: 1px solid white;width: 200px;}
</style>
<script>

    function isNumberKey(evt)
    {
        $("#mobify").text("");
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 45 || charCode > 57)) {
            //    alert("Only numbers are allowed");
            $("#mobify").text(<?php echo json_encode(LIST_COMPANY_MOBIFY); ?>);
            return false;
        }
        return true;
    }


    $(document).ready(function () {



        $('.datepicker-component').on('changeDate', function () {
            $(this).datepicker('hide');
        });



//        $("#datepicker1").datepicker({ minDate: 0});
        var date = new Date();
        $('.datepicker-component').datepicker({
            startDate: date
        });




        $("#firstname").keypress(function (event) {
            var inputValue = event.which;
            //if digits or not a space then don't let keypress work.
            if ((inputValue > 64 && inputValue < 91) // uppercase
                    || (inputValue > 96 && inputValue < 123) // lowercase
                    || inputValue == 32) { // space
                return;
            }
            event.preventDefault();
        });

        $("#lastname").keypress(function (event) {
            var inputValue = event.which;
            //if digits or not a space then don't let keypress work.
            if ((inputValue > 64 && inputValue < 91) // uppercase
                    || (inputValue > 96 && inputValue < 123) // lowercase
                    || inputValue == 32) { // space
                return;
            }
            event.preventDefault();
        });


 $("#file_upload").change(function ()

        {
           var iSize = ($("#file_upload")[0].files[0].size / 1024);

            if (iSize / 1024 > 1)

            {
                $("#file_driver_photo").html("your file is too large");
            }
            else
            {
                iSize = (Math.round(iSize * 100) / 100)
                $("#file_driver_photo").html(iSize + "kb");

            }



        });
        
         $("#file_upload_l").change(function ()

        {

            var iSize = ($("#file_upload_l")[0].files[0].size / 1024);

            if (iSize / 1024 > 1)

            {
                $("#file_driver_license").html("your file is too large");
            }
            else
            {
                iSize = (Math.round(iSize * 100) / 100)
                $("#file_driver_license").html(iSize + "kb");

            }



        });
        
         $("#file_upload_p").change(function ()

        {

            var iSize = ($("#file_upload_p")[0].files[0].size / 1024);

            if (iSize / 1024 > 1)

            {
                $("#file_passbook").html("your file is too large");
            }
            else
            {
                iSize = (Math.round(iSize * 100) / 100)
                $("#file_passbook").html(iSize + "kb");

            }



        });
        
        
      
        $('.drivers').addClass('active');
        $('.Drivers').attr('src',"<?php echo base_url();?>/theme/icon/drivers_on.png");

        $('#city_select').change(function () {
            $('#getvechiletype').load('<?php echo base_url() ?>index.php/superadmin/ajax_call_to_get_types/vtype', {city: $('#city_select').val()});
        });


        $('#title').change(function () {
            $('#vehiclemodel').load('<?php echo base_url() ?>index.php/superadmin/ajax_call_to_get_types/vmodel', {adv: $('#title').val()});
        });
    });

//validations for each previous tab before proceeding to the next tab
    function managebuttonstate()
    {
        $("#prevbutton").addClass("hidden");
    }

    function profiletab(litabtoremove, divtabtoremove)
    {
        var pstatus = true;

        $("#error-box").text("");

        $("#text_firstname").text("");
        $("#text_lastnmae").text("");
        $("#driver_mobile").text("");
        $("#file_driver_photo").text("");

        var firstname = $("#firstname").val();
        var lastname = $("#lastname").val();
        var mobile = $('#mobile').val();
        var driverphoto = $('#file_upload').val();



        var password = /^(?=.*\d)(?=.*[a-zA-Z])(?!.*[\W_\x7B-\xFF]).{6,15}$/;

        var number = /^[0-9-+]+$/;

//                            var phone = /^\d{10}$/;
//                            var company = /^[-\w\s]+$/;
//                            var re = /[a-zA-Z0-9\-\_]$/;

        var email = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/; //^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        var text = /^[a-zA-Z ]*$/;
        var alphabit = /^[a-zA-Z]+$/;


        if (firstname == "" || firstname == null)
        {
            $("#text_firstname").text(<?php echo json_encode(POPUP_DRIVER_FIRSTNAME); ?>);
            pstatus = false;
        }


        else if (lastname == "" || lastname == null)
        {

            $("#text_lastnmae").text(<?php echo json_encode(POPUP_DRIVER_LASTNAME); ?>);
            pstatus = false;
        }


        else if (mobile == "" || mobile == null)
        {
            $("#driver_mobile").text(<?php echo json_encode(POPUP_DRIVER_MOBILE); ?>);
            pstatus = false;
        }


        else if (driverphoto == "" || driverphoto == null)
        {
            $("#file_driver_photo").text(<?php echo json_encode(POPUP_DRIVER_DRIVERPHOTO); ?>);
            pstatus = false;
        }


        if (pstatus === false)
        {
            setTimeout(function ()
            {
                proceed(litabtoremove, divtabtoremove, 'firstlitab', 'tab1');
            }, 300);


            $("#tab1icon").removeClass("fs-14 fa fa-check");
            return false;
        }
        $("#tab1icon").addClass("fs-14 fa fa-check");
        $("#prevbutton").removeClass("hidden");
        $("#nextbutton").removeClass("hidden");
        $("#finishbutton").addClass("hidden");
        return true;
    }

    function addresstab(litabtoremove, divtabtoremove)
    {
        var astatus = true;


//
//                            var number = /^[0-9-+]+$/;
//
////                            var phone = /^\d{10}$/;
////                            var company = /^[-\w\s]+$/;
////                            var re = /[a-zA-Z0-9\-\_]$/;
//
//                            var email = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/; //^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
//                            var text = /^[a-zA-Z ]*$/;
//                            var alphabit = /^[a-zA-Z]+$/;





        if (profiletab(litabtoremove, divtabtoremove))
        {
            astatus == true;

            $("#text_password").text("");
            $("#text_zip").text("");

            var email = $("#email").val();
            var password = $("#password").val();
            var zipcode = $('#zipcode').val();
//         var driverphoto = $('#file_upload').val();



            var pass = /^(?=.*\d)(?=.*[a-zA-Z])(?!.*[\W_\x7B-\xFF]).{6,15}$/;

            if (email == "" || email == null)
            {
                $("#editerrorbox").html(<?php echo json_encode(POPUP_DRIVER_DRIVER_EMAIL); ?>);
                astatus = false;
            }
//                            

            else if (validateEmail($("#email").val()) !== 2)
            {
                $("#editerrorbox").html(<?php echo json_encode(POPUP_DRIVER_DRIVER_YOUREMAIL); ?>);
                astatus = false;


            }
            else if ($("#email").attr('data') == 1)
            {
                $("#editerrorbox").html(<?php echo json_encode(POPUP_DRIVER_DRIVER_ALLOCATED); ?>);
                astatus = false;


            }
//            $("text_email").text("");

//                             

            else if (password == "" || password == null)
            {
                $("#text_email").text("");
                $("#text_password").text(<?php echo json_encode(POPUP_DRIVER_DRIVER_PASSWORD); ?>);
                astatus = false;
            }
            else if ((!pass.test(password)))
            {
                $("#text_email").text("");
                $("#text_password").text(<?php echo json_encode(POPUP_DRIVER_DRIVER_PASSWORD_VALID); ?>);
                astatus = false;
            }


            else if (zipcode == "" || zipcode == null)
            {
                $("#text_password").text("");
                $("#text_zip").text(<?php echo json_encode(POPUP_DRIVER_DRIVER_ZIPCODE); ?>);
                astatus = false;
            }

            if (astatus === false)
            {
                setTimeout(function ()
                {
                    proceed(litabtoremove, divtabtoremove, 'secondlitab', 'tab2');

                }, 100);

//                alert("complete Login Details tab properly")
                $("#tab2icon").removeClass("fs-14 fa fa-check");
                return false;
            }

            $("#tab2icon").addClass("fs-14 fa fa-check");
            $("#finishbutton").removeClass("hidden");
            $("#nextbutton").addClass("hidden");

            return astatus;
        }
    }

    function bonafidetab(litabtoremove, divtabtoremove)
    {
        var bstatus = true;
        if (addresstab(litabtoremove, divtabtoremove))
        {
            if (isBlank($("#file_upload_l").val()) || isBlank($("#expirationrc").val()) || isBlank($("#file_upload_p").val()))
            {
                bstatus = false;
            }

            if (bstatus === false)
            {
                setTimeout(function ()
                {
                    proceed(litabtoremove, divtabtoremove, 'thirdlitab', 'tab3');

                }, 100);

                alert("complete Driving Licence tab properly");
                $("#tab3icon").removeClass("fs-14 fa fa-check");
                return false;
            }

            $("#tab3icon").addClass("fs-14 fa fa-check");
            $("#nextbutton").addClass("hidden");
            $("#finishbutton").removeClass("hidden");

            return bstatus;

        }
    }

    function signatorytab(litabtoremove, divtabtoremove)
    {
        var bstatus = true;
        if (bonafidetab(litabtoremove, divtabtoremove))
        {
            if (isBlank($("#entitypersonname").val()) || isBlank($("#entitysignatorymobileno").val()) || isBlank($("#entitysignatoryimagefile").val()) || $("#entitydegination").val() === "null")
            {
                bstatus = false;
            }

            if (validateEmail($("#entityemail").val()) !== 2)
            {
                bstatus = false;
            }

            if (bstatus === false)
            {
                setTimeout(function ()
                {
                    proceed(litabtoremove, divtabtoremove, 'fourthlitab', 'tab4');

                }, 100);

                alert("complete Other Document tab properly");
                $("#tab4icon").removeClass("fs-14 fa fa-check");
                return false;
            }

            $("#tab4icon").addClass("fs-14 fa fa-check");
            $("#nextbutton").addClass("hidden");
            $("#finishbutton").removeClass("hidden");

            return bstatus;
        }

    }


    function proceed(litabtoremove, divtabtoremove, litabtoadd, divtabtoadd)
    {
        $("#" + litabtoremove).removeClass("active");
        $("#" + divtabtoremove).removeClass("active");

        $("#" + litabtoadd).addClass("active");
        $("#" + divtabtoadd).addClass("active");
    }

    /*-----managing direct click on tab is over -----*/

//manage next next and finish button
    function movetonext()
    {
        var currenttabstatus = $("li.active").attr('id');
        if (currenttabstatus === "firstlitab")
        {
            profiletab('secondlitab', 'tab2');
            proceed('firstlitab', 'tab1', 'secondlitab', 'tab2');
        }
        else if (currenttabstatus === "secondlitab")
        {
            addresstab('thirdlitab', 'tab3');
            proceed('secondlitab', 'tab2', 'thirdlitab', 'tab3');

        }
        else if (currenttabstatus === "thirdlitab")
        {
            bonafidetab('fourthlitab', 'tab4');
            proceed('thirdlitab', 'tab3', 'fourthlitab', 'tab4');

            $("#finishbutton").removeClass("hidden");
            $("#nextbutton").addClass("hidden");
        }
    }

    function movetoprevious()
    {
        var currenttabstatus = $("li.active").attr('id');
        if (currenttabstatus === "secondlitab")
        {
            profiletab('secondlitab', 'tab2');
            proceed('secondlitab', 'tab2', 'firstlitab', 'tab1');
            $("#prevbutton").addClass("hidden");
        }
        else if (currenttabstatus === "thirdlitab")
        {
            addresstab('thirdlitab', 'tab3');
            proceed('thirdlitab', 'tab3', 'secondlitab', 'tab2');
            $("#nextbutton").removeClass("hidden");
            $("#finishbutton").addClass("hidden");
            $("#prevbutton").addClass("hidden");
        }
//    else if(currenttabstatus === "fourthlitab")
//    {
//        bonafidetab('fourthlitab','tab4');
//        proceed('fourthlitab','tab4','thirdlitab','tab3');
//        $("#nextbutton").removeClass("hidden");
//        $("#finishbutton").addClass("hidden");
//    }
    }

//here this function validates all the field of form while adding new subadmin you can find all related functions in RylandInsurence.js file

    function validate() {

        if (!isBlank($("#Firstname").val()))
        {
            if (!isAlphabet($("#Firstname").val()))
            {
                $("#errorbox").html("Enter only character in First name");
                return false;
            }
        }
        else
        {
            $("#errorbox").html("First name is blank");
            return false;
        }
    }
    function validateForm()
    {
        if (!isBlank($("#Firstname").val()))
        {
            if (!isAlphabet($("#Firstname").val()))
            {
                $("#errorbox").html("Enter only character in First name");
                return false;
            }
        }
        else
        {
            $("#errorbox").html("First name is blank");
            return false;
        }

        if (!isBlank($("#Lastname").val()))
        {
            if (!isAlphabet($("#Lastname").val()))
            {
                $("#errorbox").html("Enter only character in Last name");
                return false;
            }
        }
        else
        {
            $("#errorbox").html("Last name is blank");
            return false;
        }

        if (validateEmail($("#Email").val()) == 1)
        {

            $("#errorbox").html("Enter valid email");
            return false;
        }

        if (isBlank($("#Password").val()))
        {
            $("#errorbox").html("Password is Blank");
            return false;
        }

        if (!MatchPassword($("#Password").val(), $("#Cpassword").val()))
        {
            $("#errorbox").html("Password not matching");
            return false;
        }
        // return true;
    }

    function ValidateFromDb() {

        $.ajax({
            url: "validateEmail",
            type: "POST",
            data: {email: $('#email').val()},
            dataType: "JSON",
            success: function (result) {

                $('#email').attr('data', result.msg);

                if (result.msg == 1) {

                    $("#editerrorbox").html("Email is already allocated !");
                    $('#email').focus();
                    return false;
                } else if (result.msg == 0) {
                    $("#editerrorbox").html("");

                }
            }
        });

    }

  
    function submitform()
    {


        $("#file_driver_license").val('');
        $("#file_driver_exdate").val('');
        $("#file_passbook").val('');
       

        var uploaddrivinglicence = $("#file_upload_l").val();
        var expiredatelicence = $("#expirationrc").val();
        var bankpassbook = $("#file_upload_p").val();
       
     
        if (uploaddrivinglicence == "" || uploaddrivinglicence == null)
        {
           $("#file_driver_license").text(<?php echo json_encode(POPUP_DRIVER_FILE_POPUP_DRIVING); ?>);
        }


        else if (expiredatelicence == "" || expiredatelicence == null)
        {

             $("#file_driver_license").text("");
            $("#file_driver_exdate").text(<?php echo json_encode(POPUP_DRIVER_FILE_POPUP_EXPERIENCE); ?>);

        }


        else if (bankpassbook == "" || bankpassbook == null)
        {
           $("#file_driver_exdate").text("");
             $("#file_passbook").text(<?php echo json_encode(POPUP_DRIVER_FILE_POPUP_BANKPASSBOOK); ?>);

        }

        else {
            $('#addentity').submit();
        }

    }
    
</script>




<div class="page-content-wrapper">
    <!-- START PAGE CONTENT -->
    <div class="content">
        <!-- START JUMBOTRON -->
        <div class="jumbotron bg-white" data-pages="parallax">
            <div class="inner">
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb" style="margin-left: 20px;">
                    <li><a href="Drivers/my/1" class=""><?php echo LIST_DRIVER; ?></a>
                    </li>

                    <li style="width: 100px"><a href="#" class="active"><?php echo LIST_DRIVER_ADDNEW; ?></a>
                    </li>
                </ul>
                <!-- END BREADCRUMB -->
            </div>



            <div class="container-fluid container-fixed-lg bg-white">

                <div id="rootwizard" class="m-t-50">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" id="mytabs">
                        <li class="active" id="firstlitab" onclick="managebuttonstate()">
                            <a data-toggle="tab" href="#tab1" id="tb1"><i id="tab1icon" class=""></i> <span><?php echo LIST_DRIVER_PESIONALDETAILS; ?></span></a>
                        </li>
                        <li class="" id="secondlitab">
                            <a data-toggle="tab" href="#tab2" onclick="profiletab('secondlitab', 'tab2')" id="mtab2"><i id="tab2icon" class=""></i> <span><?php echo LIST_DRIVER_LOGINDETAILS; ?></span></a>
                        </li>
                        <li class="" id="thirdlitab">
                            <a data-toggle="tab" href="#tab3" onclick="addresstab('thirdlitab', 'tab3')"><i id="tab3icon" class=""></i> <span><?php echo LIST_DRIVER_DRIVINGLICENCE; ?></span></a>
                        </li>
                        <!--    <li class="" id="fourthlitab">-->
                        <!--        <a data-toggle="tab" href="#tab4" onclick="bonafidetab('fourthlitab','tab4')"><i id="tab4icon" class=""></i> <span>Other Documents</span></a>-->
                        <!--    </li>-->
                    </ul>
                    <!-- Tab panes -->
                    <form id="addentity" class="form-horizontal" role="form" action="<?php echo base_url(); ?>index.php/companyadmin/AddNewDriverData" method="post" enctype="multipart/form-data">
                        <div class="tab-content">
                            <div class="tab-pane padding-20 slide-left active" id="tab1">
                                <div class="row row-same-height">


                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_DRIVERS_FIRSTNAME; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">

                                            <input type="text"  id="firstname" name="firstname" required="required"class="form-control">

                                        </div>
                                        <div class="col-sm-3 error-box" id="text_firstname"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_DRIVERS_LASTNAME; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">

                                            <input type="text" id="lastname" name="lastname" required="required"class="form-control">

                                        </div>
                                        <div class="col-sm-3 error-box" id="text_lastnmae"></div>
                                    </div>



                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_DRIVERS_MOBILE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">

                                            <input type="text"  id="mobile" name="mobile" required="required"class="form-control" onkeypress="return isNumberKey(event)">

                                        </div>
                                        <div class="col-sm-3 error-box" id="driver_mobile"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?php echo FIELD_DRIVERS_UPLOADPHOTO; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <!--                <input type="text" class="form-control" name="entitydocname" id="entitydocname">-->
                                            <input type="file" class="form-control" style="height: 37px;" name="photos" id="file_upload">
                                        </div>
                                        <div class="col-sm-3 error-box" id="file_driver_photo"></div>
                                    </div>







                                </div>
                            </div>
                            <div class="tab-pane slide-left padding-20" id="tab2">
                                <div class="row row-same-height">

                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_DRIVERS_EMAIL; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">

                                            <input type="email" data="1" id="email" name="email" required="required" onblur="ValidateFromDb()" class="form-control">


                                        </div>
                                        <span id="editerrorbox" class="col-sm-3 control-label" style="color: #ff0000"></span>
                                        <div class="col-sm-3 error-box" id="text_email"></div>
                                    </div>




                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_DRIVERS_PASSWORD; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">

                                            <input type="text"  id="password" name="password" required="required" class="form-control">

                                        </div>
                                        <div class="col-sm-3 error-box" id="text_password"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_DRIVERS_ZIPCODE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">

                                            <input type="text"  id="zipcode" name="zipcode" required="required" class="form-control" onkeypress="return isNumberKey(event)">

                                        </div>
                                        <div class="col-sm-3 error-box" id="text_zip"></div>
                                    </div>

                                </div>
                            </div>




                            <div class="tab-pane slide-left padding-20" id="tab3">
                                <div class="row row-same-height">
                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_DRIVERS_UPLOADDRIVERLICENSE; ?><span style="color:red;font-size: 18px">*</span></label>

                                        <div class="col-sm-6">
                                            <input type="file" class="form-control" style="height: 37px;" name="certificate" id="file_upload_l" >
                                        </div>
                                        <div class="col-sm-3 error-box" id="file_driver_license"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_DRIVERS_EXDATE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input id="expirationrc" name="expirationrc" required="required"  type="" class="form-control datepicker-component"  >
                                        </div>
                                        <div class="col-sm-3 error-box" id="file_driver_exdate"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_DRIVERS_UPLOADPASSBOOK; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="file" class="form-control" style="height: 37px;" name="passbook"  id="file_upload_p">
                                        </div>
                                        <div class="col-sm-3 error-box" id=file_passbook></div>
                                    </div>

                                </div>
                            </div>

                            <div class="padding-20 bg-white">
                                <ul class="pager wizard">
                                    <li class="next" id="nextbutton">
                                        <button class="btn btn-primary btn-cons btn-animated from-left  pull-right" type="button" onclick="movetonext()">
                                            <span><?php echo BUTTON_NEXT; ?></span>
                                        </button>
                                    </li>
                                    <li class="hidden" id="finishbutton">
                                        <button class="btn btn-primary btn-cons btn-animated from-left fa fa-cog pull-right" type="button" onclick="submitform()">
                                            <span><?php echo BUTTON_FINISH; ?></span>
                                        </button>
                                    </li>

                                    <li class="previous hidden" id="prevbutton">
                                        <button class="btn btn-default btn-cons pull-right" type="button" onclick="movetoprevious()">
                                            <span><?php echo BUTTON_PREVIOUS; ?></span>
                                        </button>
                                    </li>
                                </ul>
                            </div>

                        </div>

                    </form>

                </div>


            </div>
            <!-- END PANEL -->
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


<script src="http://107.170.66.211/apps/RylandInsurence/RylandInsurence/javascript/RylandInsurence.js" type="text/javascript"></script>