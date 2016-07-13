<?php
$this->load->database();
?>
<style>
    .form-horizontal .form-group
    {
        margin-left: 13px;
    }
</style>




<script>
    $(document).ready(function () {

        $('#vehicleid').blur(function () {
            
            $.ajax({
                type: "post",
                url: "<?php echo base_url() ?>index.php/superadmin/uniq_val",
                data: {uniq_id: $(this).val()},
                dataType: "json",
                success: function (result) {
                    if(result.flag ==  1){
                     $('#vehicleid').val('');
                     $('#ve_id').html('The Vehicle Id Is Already Allocated');
                    }
                   
                }
            });
        });
        
        
        $('.datepicker-component').on('changeDate', function () {
            $(this).datepicker('hide');
        });



//        $("#datepicker1").datepicker({ minDate: 0});
        var date = new Date();
        $('.datepicker-component').datepicker({
            startDate: date
        });


        $("#vechilecolor").on("input", function () {
//                  alert("hai");
            var regexp = /[^a-zA-Z/ ]/g;
            if ($(this).val().match(regexp)) {
                $(this).val($(this).val().replace(regexp, ''));
            }
        });



        $('.vehicles').addClass('active');
        $('.vehicles_thumb').addClass("bg-success");

        $('#city_select').change(function () {
            $('#getvechiletype').load('<?php echo base_url() ?>index.php/superadmin/ajax_call_to_get_types/vtype', {city: $('#city_select').val()});
        });

//        $('#city_select').change(function () {
//            $('#company_select').load('<?php echo base_url() ?>index.php/superadmin/ajax_call_to_get_types/companyselect', {company: $('#city_select').val()});
//        });


        $('#vehiclemake').change(function () {
            $('#vehiclemodel').load('<?php echo base_url() ?>index.php/superadmin/ajax_call_to_get_types/vmodel', {adv: $('#vehiclemake').val()});
        });

        $('.error-box-class').keypress(function () {
            $('.error-box').text('');
        });


        $("#entitystatus").onchange(function () {
            var checkedval = $("input[@name='entitystatus']:checked").val();
            alert(checkedval);
        });
//    if ($("input[@name='entitystatus']:checked").val()) {
//       alert('one checkbox is checked!');
//        return false;
//    }
//    else {
//      alert('One of the radio buttons is checked!');
//    }







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

        $("#ve_compan").val('');
        $("#ve_city").val('');
        $("#ve_type").val('');
        $("#ve_make").val('');
        $("#v_modal").val('');
        $("#v_image").val('');
        $("#ve_id").val('');

        var company = $("#company_select").val();
        var cityselect = $("#city_select").val();
        var vtype = $("#getvechiletype").val();
        var vmake = $('#vehiclemake').val();
        var vmodal = $('#vehiclemodel').val();
        var vimage = $('#imagefiles').val();
        var vehicleid = $("#vehicleid").val();
        var manual = $("input[name = entitystatus]:checked").val()

        if (cityselect == "" || cityselect == null)
        {
            $("#ve_city").text(<?php echo json_encode(POPUP_ADDCITY__NAME); ?>);
            pstatus = false;
        }

        else if (company == "" || company == null)
        {
            $("#ve_city").text("");
            $("#ve_compan").text(<?php echo json_encode(POPUP_ADDCOMPANY_NAME); ?>);
            pstatus = false;
        }


        else if (vtype == "" || vtype == null)
        {
            $("#ve_compan").text("");

            $("#ve_type").text(<?php echo json_encode(POPUP_SELECT_TYPE); ?>);
            pstatus = false;
        }

        else if (manual == 2 && (vehicleid == "" || vehicleid == null))
        {
            $("#ve_type").text("");
            $("#ve_id").text(<?php echo json_encode(POPUP_SELECT_VEHICLEID); ?>);
            pstatus = false;
        }
        else if (vmake == "" || vmake == null)
        {
            $("#ve_type").text("");
            $("#ve_id").text("");
            $("#ve_make").text(<?php echo json_encode(POPUP_SELECT_VEHICLEMAKE); ?>);
            pstatus = false;
        }

        else if (vmodal == "" || vmodal == null)
        {
            $("#ve_make").text("");
            $("#v_modal").text(<?php echo json_encode(POPUP_SELECT_VEHICLEMODAL); ?>);
            pstatus = false;
        }


        else if (vimage == "" || vimage == null)
        {
            $("#ve_make").text("");
            $("#v_modal").text("");
            $("#v_image").text(<?php echo json_encode(POPUP_SELECT_VEHICLEIMAGE); ?>);
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
                $("#vehi_reg").text(<?php echo json_encode(POPUP_SELECT_VEHICLEREGNO); ?>);
                astatus = false;
            }


            else if (licenseno == "" || licenseno == null)
            {
                $("#vehi_reg").text("");

                $("#vehicl_plate").text(<?php echo json_encode(POPUP_SELECT_VEHICLEPLATENO); ?>);
                astatus = false;
            }


            else if (insurenceno == "" || insurenceno == null)
            {
                $("#vehicl_plate").text("");
                $("#ve_insurence").text(<?php echo json_encode(POPUP_SELECT_VINSURENCENUMBER); ?>);
                astatus = false;
            }


            else if (vcolor == "" || vcolor == null)
            {
                $("#ve_insurence").text("");
                $("#v_color").text(<?php echo json_encode(POPUP_SELECT_VEHICLECOLOR); ?>);
                astatus = false;
            }


            if (astatus === false)
            {
                setTimeout(function ()
                {
                    proceed(litabtoremove, divtabtoremove, 'secondlitab', 'tab2');

                }, 100);

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
//            if (isBlank($("#expirationrc").val()) || isBlank($("#expirationinsurance").val()) || isBlank($("#regcertificate").val()) || isBlank($("#motorcertificate").val()) || isBlank($("#contractpermit").val()) || isBlank($("#edate").val()))
//            {
//                bstatus = false;
//            }

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

//                alert("complete 4 tab properly");
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
            
            bonafidetab('thirdlitab', 'tab3')


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
//
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



        if (regcertificate == "" || regcertificate == null)
        {
            
            $("#v_upload_cr").text(<?php echo json_encode(POPUP_SELECT_VEHICLEUPLOADREGNO); ?>);

        }


        else if (expirerc == "" || expirerc == null)
        {
            
            $("#v_upload_cr").text("");
            $("#ve_expire").text(<?php echo json_encode(POPUP_SELECT_VEHICLE_DATE); ?>);

        }


        else if (motorcertificate == "" || motorcertificate == null)
        {
            $("#ve_expire").text("");
            
            $("#vehicle_uploadmotor").text(<?php echo json_encode(POPUP_SELECT_VINSURENCENUMBER_INSURENCE); ?>);

        }


        else if (expiremotor == "" || expiremotor == null)
        {
            $("#vehicle_uploadmotor").text("");
           
            $("#expire_insurence_date").text(<?php echo json_encode(POPUP_SELECT_VEHICLE_DATE); ?>);

        }

        else if (carriagepermit == "" || carriagepermit == null)
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


    function changeType(dis) {
        if (dis.value == 1){
            $('#vehicleid').hide();
            $('#ve_id').html('');
        }

        else
            $('#vehicleid').show();
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
                    <form id="addentity" class="form-horizontal" role="form" action="<?php echo base_url(); ?>index.php/superadmin/AddNewVehicleData" method="post" enctype="multipart/form-data">
                        <div class="tab-content">
                            <div class="tab-pane padding-20 slide-left active" id="tab1">
                                <div class="row row-same-height">





                                    


                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_SELECTCOMPANY; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                            <!--                <input type="text" class="form-control" id="entityname" placeholder="Name" name="entityname"  aria-required="true">-->

                                            <select id="company_select" name="company_select"  class="form-control error-box-class" >
                                               <option value="">Select a Company  </option>
                                                <?php
                                                    foreach ($company as $typelist) {
                                                        echo "<option value='" . $typelist->company_id . "'>" . $typelist->companyname . "</option>";
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
                                                $cityname = $this->db->query("SELECT * FROM city_available order by City_Name ASC")->result();

                                                foreach ($cityname as $typelist) {
                                                    echo "<option value='" . $typelist->City_Id . "'>" . $typelist->City_Name . "</option>";
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
                                                <option value="">Select.. </option>

                                            </select>
                                        </div>
                                        <!--<input type="text" id="ve_type" name="fname" class="form-control error-box-class">-->
                                        <div class="col-sm-3 error-box" id="ve_type"></div>
                                    </div>


                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_VEHICLEID; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6  radio-success">
                                            <input type="radio" value="1"  checked="true" name="entitystatus" onclick="changeType(this)" >&nbsp;&nbsp;&nbsp;<?php echo BUTTON_AUTOMATIC; ?>&nbsp;&nbsp;&nbsp;
                                            <input type="radio" id="manual" value="2" name="entitystatus" onclick="changeType(this)">&nbsp;&nbsp;&nbsp;<?php echo BUTTON_MANUAL; ?>&nbsp;&nbsp;&nbsp;
                                            <input type="text" value="" name="vehicleid" id="vehicleid" class="form-control error-box-class" style="width: 99%;display: none;  margin-top: 13px;">
                                        </div>
                                        <div id="ve_id" class="col-sm-3 error-box"></div>

                                    </div>


                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_VEHICLEMAKE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
<!--                                            <input type="radio" value="active" checked="true" name="entitystatus" id="entitystatus">&nbsp;&nbsp;&nbsp;automatic&nbsp;&nbsp;&nbsp;
                                            <input type="radio" value="deactive" name="entitystatus" id="entitystatus">&nbsp;&nbsp;&nbsp;manual&nbsp;&nbsp;&nbsp;-->

                                            <select id="vehiclemake" name="title" class="form-control error-box-class">

                                                <option value="">Select a vehicle:</option>
                                                <?php
                                                $adv_sql = $this->db->query("SELECT * FROM vehicleType")->result();
                                                foreach ($adv_sql as $adv_sql_row) {
                                                    echo "<option value='" . $adv_sql_row->id . "' id='" . $adv_sql_row->id . "'>" . $adv_sql_row->vehicletype . "</option>";
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
                                            <input type="text" id="vechileregno" name="vechileregno" required="required"class="form-control error-box-class">

                                        </div>
                                        <!--<input type="text" id="vehi_reg" name="fname" class="form-control error-box-class">-->
                                        <div class="col-sm-3 error-box" id="vehi_reg"></div>
                                    </div>




                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_PLATENO; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text"  id="licenceplaetno" name="licenceplaetno" required="required" class="form-control error-box-class" placeholder="eg. KA-05/1800">
                                        </div>
                                        <!--<input type="text" id="vehicl_plate" name="fname" class="form-control error-box-class">-->
                                        <div class="col-sm-3 error-box" id="vehicl_plate"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_INSURENCE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control error-box-class" id="Vehicle_Insurance_No" name="Vehicle_Insurance_No" required="required" placeholder="eg. PL-23111441">
                                        </div>
                                        <!--<input type="text" id="ve_insurence" name="fname" class="form-control error-box-class">-->
                                        <div class="col-sm-3 error-box" id="ve_insurence"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_COLOR; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">

                                            <input type="text" class="form-control error-box-class" id="vechilecolor" name="vechilecolor" required="required"  placeholder="eg. blue">
                                        </div>
                                        <!--<input type="text" id="v_color" name="fname" class="form-control error-box-class">-->
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
                                        </div>
                                        <!--<input type="text" id="v_upload_cr" name="fname" class="form-control error-box-class">-->
                                        <div class="col-sm-3 error-box" id="v_upload_cr"></div>
                                    </div>



                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_EXPIREDATE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                            <!--                <textarea class="form-control" name="entitydescription" id="entitydescription" rows="3">-->
                                            <!--                </textarea>-->
                                            <input id="expirationrc" name="expirationrc" required="required"  type="" class="form-control error-box-class datepicker-component">
                                        </div>
                                        <!--<input type="text" id="ve_expire" name="fname" class="form-control error-box-class">-->
                                        <div class="col-sm-3 error-box" id="ve_expire"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_UPLOADMOTOR; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="file" class="form-control error-box-class" style="height: 37px;" name="insurcertificate" id="motorcertificate">
                                        </div>
                                        <!--<input type="text" id="vehicle_uploadmotor" name="fname" class="form-control error-box-class">-->
                                        <div class="col-sm-3 error-box" id="vehicle_uploadmotor"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="fname" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_EXPIREDATE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input id="expirationinsurance" name="expirationinsurance" required="required"  type="" class="form-control error-box-class datepicker-component" >
                                        </div>
                                        <!--<input type="text" id="expire_insurence_date" name="fname" class="form-control error-box-class">-->
                                        <div class="col-sm-3 error-box" id="expire_insurence_date"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_UPLOADCP; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="file" class="form-control error-box-class" name="carriagecertificate" id="contractpermit">
                                        </div>
                                        <!--<input type="text" id="vehicle_up_carrp" name="fname" class="form-control error-box-class">-->
                                        <div class="col-sm-3 error-box" id="vehicle_up_carrp"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?php echo FIELD_VEHICLE_EXPIREDATE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">

                                            <input type="" class="form-control error-box-class datepicker-component" style="height: 37px;"  name="expirationpermit" id="edate">
                                        </div>
                                        <!--<input type="text" id="vehicle_expire_date" name="fname" class="form-control error-box-class">-->
                                        <div class="col-sm-3 error-box" id="vehicle_expire_date"></div>
                                    </div>

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
                        <button type="button" class="btn btn-primary pull-right" id="confirmeds"><?php echo BUTTON_OK; ?></button>
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

                    <div class="error-box" id="errorboxdatass" style="font-size: large;text-align:center"><?php echo VEHICLEMODEL_DELETE; ?></div>

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



<!--<script src="http://107.170.66.211/apps/RylandInsurence/RylandInsurence/javascript/RylandInsurence.js" type="text/javascript"></script>-->