<?php
$this->load->database();

//foreach ($vehicleedit as $result)
//    $cityid = $result->city_id;
//    $vehiclemake = $result->;
?>
<style>
    .form-horizontal .form-group
    {
        margin-left: 13px;
    }
</style>
<style>
    .ui-autocomplete{
        z-index: 5000;
    }
    #selectedcity,#companyid{
        display: none;
    }
    
    .ui-menu-item{cursor: pointer;background: black;color:white;border-bottom: 1px solid white;width: 200px;}
</style>
<?php
$motor_cert = $reg_cert = $car_permit = $motor_exp = $reg_exp = $car_permit_exp = "";

foreach ($data['vehicleDoc'] as $value) {

    if ($value->doctype == '2') {
        $motor_cert = $value->url;
        $motor_exp = $value->expirydate;
    } else if ($value->doctype == '1') {
        $reg_cert = $value->url;
        $reg_exp = $value->expirydate;
    } else if ($value->doctype == '3') {
        $car_permit = $value->url;
        $car_permit_exp = $value->expirydate;
    }
}
?>



<script>

    $(document).ready(function () {


        $('.datepicker-component').on('changeDate', function () {
            $(this).datepicker('hide');
        });



//        $("#datepicker1").datepicker({ minDate: 0});
        var date = new Date();
        $('.datepicker-component').datepicker({
            startDate: date
        });





        $("#vechilecolor").on("input", function () {
            var regexp = /[^a-zA-Z/ ]/g;
            if ($(this).val().match(regexp)) {
                $(this).val($(this).val().replace(regexp, ''));
            }

        });

//        $('#city_select').val('<?php echo $cityid ?>');

        $('.vehicles').addClass('active');
        $('.vehicles_thumb').addClass("bg-success");

        $('#city_select').change(function () {
            $('#getvechiletype').load('<?php echo base_url() ?>index.php/superadmin/ajax_call_to_get_types/vtype', {city: $('#city_select').val()});
        });


        $('#vehiclemake').change(function () {
            $('#vehiclemodel').load('<?php echo base_url() ?>index.php/superadmin/ajax_call_to_get_types/vmodel', {adv: $('#vehiclemake').val()});
        });

        $('.error-box-class').keypress(function () {
            $('.error-box').text('');
        });
    });




    function managebuttonstate()
    {
        $("#prevbutton").addClass("hidden");
       
         $("#cancelbutton").removeClass("hidden");
        
    }

    function profiletab(litabtoremove, divtabtoremove)
    {
//        alert('in profiletab');
        var pstatus = true;

        $("#error-box").text("");

        $("#ve_compan").val('');
        $("#ve_city").val('');
        $("#ve_type").val('');
        $("#ve_make").val('');
        $("#v_modal").val('');
        $("#v_image").val('');

        var company = $("#company_select").val();
        var cityselect = $("#city_select").val();
        var vtype = $("#getvechiletype").val();
        var vmake = $('#vehiclemake').val();
        var vmodal = $('#vehiclemodel').val();
        var viewimage = $("#imagefiles").val();


        if (company == "" || company == null)
        {
            $("#ve_compan").text(<?php echo json_encode(POPUP_DRIVER_FIRSTNAME); ?>);
            pstatus = false;
        }


        else if (cityselect == "" || cityselect == null)
        {
            $("#ve_compan").text("");

            $("#ve_city").text(<?php echo json_encode(POPUP_DRIVER_LASTNAME); ?>);
            pstatus = false;
        }


        else if (vtype == "" || vtype == null)
        {
            $("#ve_city").text("");
            $("#ve_type").text(<?php echo json_encode(POPUP_DRIVER_MOBILE); ?>);
            pstatus = false;
        }


        else if (vmake == "" || vmake == null)
        {
            $("#ve_type").text("");
            $("#ve_make").text(<?php echo json_encode(POPUP_DRIVER_DRIVERPHOTO); ?>);
            pstatus = false;
        }

        else if (vmodal == "" || vmodal == null)
        {
            $("#ve_make").text("");
            $("#v_modal").text(<?php echo json_encode(POPUP_DRIVER_MOBILE); ?>);
            pstatus = false;
        }


        else if ((viewimage == "" || viewimage == null) && $('#viewimage_hidden').val() == '')
        {
            $("#ve_make").text("");
            $("#v_modal").text("");
            $("#v_image").text(<?php echo json_encode(POPUP_DRIVER_DRIVERPHOTO); ?>);
            pstatus = false;
        }


        if (pstatus === false)
        {
            setTimeout(function ()
            {
                proceed(litabtoremove, divtabtoremove, 'firstlitab', 'tab1');
            }, 300);

            $("#tab1icon").removeClass("fs-14 fa fa-check");
            $("#cancelbutton").removeClass("hidden");
            return false;
        }
        $("#tab1icon").addClass("fs-14 fa fa-check");
         $("#cancelbutton").removeClass("hidden");
        $("#prevbutton").removeClass("hidden");
        $("#nextbutton").removeClass("hidden");
        $("#finishbutton").addClass("hidden");
        return true;
    }

    function addresstab(litabtoremove, divtabtoremove)
    {
        var astatus = true;
//        alert('in address tab');

        if (profiletab(litabtoremove, divtabtoremove))
        {

            $("#error-box").text("");

            $("#vehi_reg").val('');
            $("#vehicl_plate").val('');
            $("#ve_insurence").val('');
            $("#v_color").val('');


            var regno = $("#vechileregno").val();
            var licenseno = $("#licenceplaetno").val();
            var insurenceno = $("#Vehicle_Insurance_No").val();
            var vcolor = $('#vechilecolor').val();

            if (regno == "" || regno == null)
            {
                $("#vehi_reg").text(<?php echo json_encode(POPUP_DRIVER_FIRSTNAME); ?>);
                astatus = false;
            }


            else if (licenseno == "" || licenseno == null)
            {
                $("#vehi_reg").text("");

                $("#vehicl_plate").text(<?php echo json_encode(POPUP_DRIVER_LASTNAME); ?>);
                astatus = false;
            }


            else if (insurenceno == "" || insurenceno == null)
            {
                $("#vehicl_plate").text("");
                $("#ve_insurence").text(<?php echo json_encode(POPUP_DRIVER_MOBILE); ?>);
                astatus = false;
            }


            else if (vcolor == "" || vcolor == null)
            {
                $("#ve_insurence").text("");
                $("#v_color").text(<?php echo json_encode(POPUP_DRIVER_DRIVERPHOTO); ?>);
                astatus = false;
            }




            //alert(profiletab());



            if (astatus === false)
            {
                setTimeout(function ()
                {
                    proceed(litabtoremove, divtabtoremove, 'secondlitab', 'tab2');

                }, 100);

                $("#tab2icon").removeClass("fs-14 fa fa-check");
                return false;

            }

            $("#tab3icon").addClass("fs-14 fa fa-check");
             $("#cancelbutton").removeClass("hidden");
            $("#finishbutton").removeClass("hidden");
            $("#nextbutton").addClass("hidden");

            return astatus;
        }
        alert('after address tab');
    }




    function bonafidetab(litabtoremove, divtabtoremove)
    {
        var bstatus = true;
        if (addresstab(litabtoremove, divtabtoremove))
        {
//            alert('in bonafied');
//            if (isBlank($("#expirationrc").val()) || isBlank($("#expirationinsurance").val()) || isBlank($("#regcertificate").val()) || isBlank($("#motorcertificate").val()) || isBlank($("#contractpermit").val()) || isBlank($("#edate").val()))
//            {
//                bstatus = false;
//            }
//alert('after empty check');
            if (bstatus === false)
            {
                setTimeout(function ()
                {
                    proceed(litabtoremove, divtabtoremove, 'thirdlitab', 'tab3');

                }, 100);

//                alert("complete Document tab properly");
                $("#tab3icon").removeClass("fs-14 fa fa-check");
                return false;
            }
//alert('after time out');
            $("#tab2icon").addClass("fs-14 fa fa-check");
            $("#cancelbutton").removeClass("hidden");
            $("#nextbutton").addClass("hidden");
            $("#finishbutton").removeClass("hidden");
//alert('after active chages');
            return bstatus;

        }
    }

    function signatorytab(litabtoremove, divtabtoremove)
    {
        var bstatus = true;
        if (bonafidetab(litabtoremove, divtabtoremove))
        {
            if (isBlank($("#regcertificate").val()) || isBlank($("#motorcertificate").val()) || isBlank($("#contractpermit").val()) || $("#entitydegination").val() === "null")
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

                alert("complete 4 tab properly");
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

            bonafidetab('thirdlitab', 'tab3');

//            alert('after bonafied');
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

//            $("#nextbutton").removeClass("hidden");
//            $("#finishbutton").addClass("hidden");
        }
////    else if(currenttabstatus === "fourthlitab")
////    {
////        bonafidetab('fourthlitab','tab4');
////        proceed('fourthlitab','tab4','thirdlitab','tab3');
////        $("#nextbutton").removeClass("hidden");
////        $("#finishbutton").addClass("hidden");
////    }
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



    function submitform()
    {


        $("#error-box").text("");

        $("#v_upload_cr").val('');
        $("#ve_expire").val('');
        $("#vehicle_uploadmotor").val('');
        $("#expire_insurence_date").val('');
        $("#vehicle_up_carrp").val('');
        $("#vehicle_expire_date").val('');

        var regcertificate = $("#regcertificate").val();
        var expirerc = $("#expirationrc").val();
        var motorcertificate = $("#motorcertificate").val();
        var expiremotor = $("#expirationinsurance").val();

        var carriagepermit = $("#contractpermit").val();
        var date = $("#date").val();
        var entitydegnation = $("#entitydegination").val();
        var edate = $("#edate").val();


//
        if ((regcertificate == "" || regcertificate == null) && $('#regcertificate_hidden').val() == '')
        {
            $("#v_upload_cr").text(<?php echo json_encode(POPUP_SELECT_VEHICLEUPLOADREGNO); ?>);
        }


        if (expirerc == "" || expirerc == null)
        {

            $("#v_upload_cr").text("");
            $("#ve_expire").text(<?php echo json_encode(POPUP_SELECT_VEHICLE_DATE); ?>);

        }


        else if ((motorcertificate == "" || motorcertificate == null) && $('#motorcertificate_hidden').val() == '')
        {
            $("#ve_expire").text("");
            $("#vehicle_uploadmotor").text(<?php echo json_encode(POPUP_SELECT_VINSURENCENUMBER_INSURENCE); ?>);

        }


        else if (expiremotor == "" || expiremotor == null)
        {
            $("#vehicle_uploadmotor").text("");
            $("#expire_insurence_date").text(<?php echo json_encode(POPUP_SELECT_VEHICLE_DATE); ?>);

        }

        else if ((carriagepermit == "" || carriagepermit == null) && $('#carriagepermit_hidden').val() == '')
        {
            $("#expire_insurence_date").text("");
            $("#vehicle_up_carrp").text(<?php echo json_encode(POPUP_SELECT_VEHICLECOLOR_CARRIAGE_PERMIT); ?>);

        }


        else if (edate == "" || edate == null)
        {
            $("#vehicle_up_carrp").text("");
            $("#vehicle_expire_date").text(<?php echo json_encode(POPUP_SELECT_VEHICLE_DATE); ?>);
            return false;
        }

        else {
            $('#addentity').submit();
        }

    }
    
    function cancel(){
    
            window.location="<?php echo base_url('index.php/superadmin') ?>/Vehicles/5";
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
                    <li><a href="<?php echo base_url('index.php/superadmin') ?>/Vehicles/5" class=""><?php echo LIST_VEHICLE; ?></a>
                    </li>

                    <li style="width: 100px"><a href="#" class="active"><?php echo LIST_VEHICLE_ADD; ?></a>
                    </li>
                </ul>
                <!-- END BREADCRUMB -->
            </div>



            <div class="container-fluid container-fixed-lg bg-white">

                <div id="rootwizard" class="m-t-50">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" id="mytabs">
                        <li class="active" id="firstlitab" onclick="managebuttonstate()">
                            <a data-toggle="tab" href="#tab1" id="tb1"><i id="tab1icon" class=""></i> <span><?php echo LIST_VEHICLE_VEHICLESETUP; ?></span></a>
                        </li>
                        <li class="" id="secondlitab">
                            <a data-toggle="tab" href="#tab2" onclick="profiletab('secondlitab', 'tab2')" id="mtab2"><i id="tab2icon" class=""></i> <span><?php echo LIST_VEHICLE_DETAILS; ?></span></a>
                        </li>
                        <li class="" id="thirdlitab">
                            <a data-toggle="tab" href="#tab3" onclick="addresstab('thirdlitab', 'tab3')"><i id="tab3icon" class=""></i> <span><?php echo LIST_VEHICLE_DOCUMETS; ?></span></a>
                        </li>
                        <!--    <li class="" id="fourthlitab">-->
                        <!--        <a data-toggle="tab" href="#tab4" onclick="bonafidetab('fourthlitab','tab4')"><i id="tab4icon" class=""></i> <span>4</span></a>-->
                        <!--    </li>-->
                    </ul>
                    <!-- Tab panes -->
                    <form id="addentity" class="form-horizontal" role="form" action="<?php echo base_url(); ?>index.php/superadmin/editNewVehicleData" method="post" enctype="multipart/form-data">
                        <div class="tab-content">
                            <input type="hidden" value="<?php echo $vehId; ?>" name="vehicle_id"/>
                            <?php
//                            print_r($data['cityList']);
                            foreach ($data['vehicle'] as $value) {
                                ?>
                                <div class="tab-pane padding-20 slide-left active" id="tab1">
                                    <div class="row row-same-height">

                                       

                                        <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_SELECTCOMPANY; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                <!--                <input type="text" class="form-control" id="entityname" placeholder="Name" name="entityname"  aria-required="true">-->

                                                <select id="company_select" name="company_id"  class="form-control error-box-class" >
                                                    <option value="">Select a Company  </option>


                                                    <?php
                                                    foreach ($data['company'] as $typelist) {
                                                        ?>
                                                    <option value="<?php echo $typelist->company_id;?>" <?php if($value->company == $typelist->company_id)echo 'selected';?>><?php echo $typelist->companyname;?></option>;
                                                      <?php
                                                    }
                                                    ?>


                                                </select>
                                                 <!--<input type="text" id="ve_compan" name="fname" class="form-control error-box-class">-->

                                            </div>
                                            <div class="col-sm-3 error-box" id="ve_compan"></div>

                                        </div>
                                        
                                         <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_SELECTCITY; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                <!--                <input type="text" class="form-control" id="entityname" placeholder="Name" name="entityname"  aria-required="true">-->

                                                <select id="city_select" name="city_select"  class="form-control error-box-class" >
                                                    <option value="">Select a City  </option>
                                                    <?php
                                                    foreach ($data['cityList'] as $typelist) {
                                                        $selected = "";
                                                        if ($typelist->City_Id == $value->city_id)
                                                            $selected = "selected";
                                                        echo "<option value='" . $typelist->City_Id . "' " . $selected . ">" . $typelist->City_Name . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                 <!--<input type="text" id="ve_city" name="fname" class="form-control error-box-class">-->

                                            </div>
                                            <div class="col-sm-3 error-box" id="ve_city"></div>
                                        </div>




                                        <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_VEHICLETYPE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
            <!--                                    <input type="text" class="form-control" id="entityemail" placeholder="Email" name="entityemail"  aria-required="true">-->
                                                <select id="getvechiletype" name="getvechiletype" class="form-control error-box-class">
                                                    <option value="">Select a vehicle type</option>
                                                    <?php
                                                   
                                                    foreach ($data['workplaceTypes'] as $typelist) {
                                                        $selected = "";
                                                        if ($typelist->type_id == $value->type_id)
                                                            $selected = "selected";
                                                        ?>
//                                                        echo <option value="<?php echo $typelist->type_id?>" <?php echo $selected;?>><?php echo $typelist->type_name;?></option>;
                                                <?php    
                                                }
                                                    ?>

                                                </select>
                                            </div>
                                            <!--<input type="text" id="ve_type" name="fname" class="form-control error-box-class">-->
                                            <div class="col-sm-3 error-box" id="ve_type"></div>
                                        </div>



                                        <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_VEHICLEMAKE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">

                                                <select id="vehiclemake" name="title" class="form-control error-box-class">

                                                    <option value="">Select a vehicle make</option>
                                                    <?php
                                                    foreach ($data['vehicleTypes'] as $adv_sql_row) {
                                                        $selected = "";
                                                        if ($adv_sql_row->id == $value->Title)
                                                            $selected = "selected";
                                                        echo "<option value='" . $adv_sql_row->id . "' " . $selected . ">" . $adv_sql_row->vehicletype . "</option>";
                                                    }
                                                    ?>


                                                </select>

                                            </div>
                                            <!--<input type="text" id="ve_make" name="fname" class="form-control error-box-class">-->
                                            <div class="col-sm-3 error-box" id="ve_make"></div>
                                        </div>



                                        <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_VEHICLEMODEL; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
            <!--                                    <input type="text" class="form-control" id="entityregno" placeholder="Registration no" name="entityregno"  aria-required="true">-->
                                                <select id="vehiclemodel" name="vehiclemodel" class="form-control error-box-class">

                                                    <option value="">Select a vehicle Model:</option>
                                                    <?php
                                                    echo "<option value='" . $value->id . "' selected>" . $value->vehiclemodel . "</option>";
                                                    ?>
                                                </select>
                                            </div>
                                            <!--<input type="text" id="v_modal" name="fname" class="form-control error-box-class">-->
                                            <div class="col-sm-3 error-box" id="v_modal"></div>
                                        </div>


                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_IMAGE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                <!--                <input type="text" class="form-control" name="entitydocname" id="entitydocname">-->
                                                <input type="file" class="form-control error-box-class" style="height: 37px;" name="imagefile" id="imagefiles">
                                                <input type="hidden" value="<?php echo $value->Vehicle_Image; ?>" id='viewimage_hidden'/>
                                                <?php
                                                if ($value->Vehicle_Image != '') {
                                                    ?>
                                                    <a target="_blank" href="<?php echo base_url()?>../../pics/<?php echo $value->Vehicle_Image; ?>">view</a> 

                                                <?php }
                                                ?>
                                            </div>
                                       <!--<input type="text" id="v_image" name="fname" class="form-control error-box-class">-->
                                            <div class="col-sm-3 error-box" id="v_image"></div>
                                        </div>
                                       
                                    </div>
                                </div>

                                <div class="tab-pane slide-left padding-20" id="tab2">
                                    <div class="row row-same-height">

                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_REGNO; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" id="vechileregno" name="vechileregno" required="required"class="form-control" value="<?php echo $value->Vehicle_Reg_No; ?>">

                                            </div>
                                            <div class="col-sm-3 error-box" id="vehi_reg"></div>
                                        </div>




                                        <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_PLATENO; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="licenceplaetno" name="licenceplaetno" required="required" class="form-control" placeholder="eg. KA-05/1800" value="<?php echo $value->License_Plate_No; ?>">
                                            </div>
                                            <div class="col-sm-3 error-box" id="vehicl_plate"></div>
                                        </div>

                                        <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_INSURENCE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="Vehicle_Insurance_No" name="Vehicle_Insurance_No" required="required" placeholder="eg. PL-23111441" value="<?php echo $value->Vehicle_Insurance_No; ?>">
                                            </div>
                                            <div class="col-sm-3 error-box" id="ve_insurence"></div>
                                        </div>

                                        <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_COLOR; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">

                                                <input type="text" class="form-control" id="vechilecolor" name="vechilecolor" required="required"  placeholder="eg. blue" value="<?php echo $value->Vehicle_Color; ?>">
                                            </div>
                                            <div class="col-sm-3 error-box" id="v_color"></div>
                                        </div>

                                       

                                    </div>
                                </div>



                                <div class="tab-pane slide-left padding-20" id="tab3">
                                    <div class="row row-same-height">




                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_UPLOADCR; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                <!--                <input type="text" class="form-control" name="entitydocname" id="entitydocname">-->
                                                <input type="file" class="form-control error-box-class" style="height: 37px;" name="certificate" id="regcertificate">
                                                <input type="hidden" value="<?php echo $reg_cert; ?>" id='regcertificate_hidden'/>
                                                <?php
                                                if ($reg_cert != "") {
                                                    ?>
                                                    <a target='_blank' href="<?php echo base_url()?>../../pics/<?php echo $reg_cert; ?>">view</a> 

                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <!--<input type="text" id="v_upload_cr" name="fname" class="form-control error-box-class">-->
                                            <div class="col-sm-3 error-box" id="v_upload_cr"></div>
                                        </div>



                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_EXPIREDATE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">

                                                <input id="expirationrc" name="expirationrc" required="required"  type="" class="form-control error-box-class datepicker-component" value="<?php echo $reg_exp; ?>">
                                            </div>
                                            <div class="col-sm-3 error-box" id="ve_expire"></div>
                                        </div>

                                        <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_UPLOADMOTOR; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="file" class="form-control error-box-class" style="height: 37px;" name="insurcertificate" id="motorcertificate">

                                                <input type="hidden" value="<?php echo $motor_cert; ?>" id='motorcertificate_hidden'/>
                                                <?php
                                                if ($motor_cert != "") {
                                                    ?>
                                                    <a target='_blank' href="<?php echo base_url()?>../../pics/<?php echo $motor_cert; ?>">view</a> 

                                                    <?php
                                                }
                                                ?>

                                            </div>
                                            <!--<input type="text" id="vehicle_uploadmotor" name="fname" class="form-control error-box-class">-->
                                            <div class="col-sm-3 error-box" id="vehicle_uploadmotor"></div>
                                        </div>

                                        <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_EXPIREDATE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">

                                                <input id="expirationinsurance" name="expirationinsurance" required="required"  type=""class="form-control error-box-class datepicker-component" value="<?php echo $motor_exp; ?>" >
                                            </div>
                                     <!--<input type="text" id="expire_insurence_date" name="fname" class="form-control error-box-class">-->
                                            <div class="col-sm-3 error-box" id="expire_insurence_date"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_UPLOADCP; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="file" class="form-control error-box-class" name="carriagecertificate" id="contractpermit">
                                                <input type="hidden" value="<?php echo $car_permit; ?>" id='carriagecertificate_hidden'/>
                                                <?php
                                                if ($car_permit != "") {
                                                    ?>
                                                    <a target='_blank' href="<?php echo base_url()?>../../pics/<?php echo $car_permit; ?>">View</a>

                                                    <?php
                                                }
                                                ?>

                                            </div>
                                            <!--<input type="text" id="vehicle_up_carrp" name="fname" class="form-control error-box-class">-->
                                            <div class="col-sm-3 error-box" id="vehicle_up_carrp"></div>
                                        </div>

                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_EXPIREDATE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">

                                                <input type="" class="form-control error-box-class datepicker-component" style="height: 37px;" name="expirationpermit" id="edate" value="<?php echo $car_permit_exp; ?>">
                                            </div>
                                 <!--<input type="text" id="vehicle_expire_date" name="fname" class="form-control error-box-class">-->
                                            <div class="col-sm-3 error-box" id="vehicle_expire_date"></div>
                                        </div>
                                        
                                    <?php } ?>
                                        
                                       
                                </div>
                            </div>

                            <div class="padding-20 bg-white">
                                <ul class="pager wizard">
                                    <li class="next" id="nextbutton">
                                        <button class="btn btn-primary btn-cons btn-animated from-left  pull-right" type="button" onclick="movetonext()">
                                            <span>Next</span>
                                        </button>
                                    </li>
                                    <li class="hidden" id="finishbutton">
                                        <button class="btn btn-primary btn-cons btn-animated from-left fa fa-cog pull-right" type="button" onclick="submitform()" >
                                            <span>Finish</span>
                                        </button>
                                    </li>

                                    <li class="previous hidden" id="prevbutton">
                                        <button class="btn btn-default btn-cons pull-right" type="button" onclick="movetoprevious()">
                                            <span>Previous</span>
                                        </button>
                                    </li>
                                     <li class="" id="cancelbutton">
                                    
                                         <button  type="button" class="btn btn-default btn-cons pull-right" onclick = "cancel()" >
                                             <?php echo BUTTON_CANCEL; ?>
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


<!--<script src="http://107.170.66.211/apps/RylandInsurence/RylandInsurence/javascript/RylandInsurence.js" type="text/javascript"></script>-->