<?php
$this->load->database();
$activetab1 = $activetab2 = '';
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


<script>

    $(document).ready(function () {
        $('.vehicle_type').addClass('active');
        $('.vehicletype_thumb').addClass("bg-success");

        $("#cancel").click(function () {

            //        if (confirm("Cancel the data you have entered?")) {

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
            $("#errorboxdatas").text(<?php echo json_encode(POPUP_CANCEL); ?>);

            $("#confirmeds").click(function () {
                $(".close").trigger("click");
                $("#vehicletypename").val('');
                $("#seating").val('');
                $("#minimumfare").val('');
                $("#basefare").val('');
                $("#priceperminute").val('');
                $("#priceperkm").val('');
                $("#discrption").val('');
                $("#citys").val('');

            });
        });




        $("#cancel_s").click(function () {

            window.location = "<?php echo base_url('index.php/superadmin') ?>/vehicle_type";
        });





        $("#type_on_image").change(function ()

        {
            var iSize = ($("#type_on_image")[0].files[0].size / 1024);

            if (iSize / 1024 > 1)

            {
                $("#type_on_imageErr").html("your file is too large");
            }
            else
            {
                iSize = (Math.round(iSize * 100) / 100)
                $("#type_on_imageErr").html(iSize + "kb");

            }
        });

        $("#type_off_image").change(function ()

        {
            var iSize = ($("#type_off_image")[0].files[0].size / 1024);

            if (iSize / 1024 > 1)

            {
                $("#type_off_imageErr").html("your file is too large");
            }
            else
            {
                iSize = (Math.round(iSize * 100) / 100)
                $("#type_off_imageErr").html(iSize + "kb");

            }
        });

        $("#type_map_image").change(function ()

        {
            var iSize = ($("#type_map_image")[0].files[0].size / 1024);

            if (iSize / 1024 > 1)

            {
                $("#type_map_imageErr").html("your file is too large");
            }
            else
            {
                iSize = (Math.round(iSize * 100) / 100)
                $("#type_map_imageErr").html(iSize + "kb");

            }
        });






        $("#addvehicle").click(function () {

            $("#vehicletype").text("");
            $("#seat").text("");
            $("#minimum").text("");
            $("#base").text("");
            $("#pricepermin").text("");
            $("#pricekm").text("");
            $("#disc").text("");
            $("#cities").text("");
            $("#cancilationf").text("");



            var vtype = $("#vehicletypename").val();
            var seating = $("#seating").val();
            var mfare = $("#minimumfare").val();
            var bfare = $("#basefare").val();
            var ppmnt = $("#priceperminute").val();
            var ppkm = $("#priceperkm").val();
            var cancilationfee = $("#cancilationfee").val();
            var discription_s = $("#discrption").val();
            var city = $("#city").val();

            var type_on_image = $("#type_on_image").val();
            var type_off_image = $("#type_off_image").val();
            var type_map_image = $("#type_map_image").val();
            var waiting_charge_per_min = $("#waiting_charge").val();


//             //bb
//              var type_on_image = $('#type_on_image').prop('files')[0];  
//              var type_off_image = $('#type_off_image').prop('files')[0];  
//              var type_map_image = $('#type_map_image').prop('files')[0];  
//               var image_data = new FormData();  
////               var image_data2 = new FormData();  
////               var image_data3 = new FormData();  
//               image_data.append('image_data1', type_on_image);
//               image_data.append('image_data2', type_off_image);
//               image_data.append('image_data3', type_map_image);
            //aa


            //  var password = /^(?=.*\d)(?=.*[a-zA-Z])(?!.*[\W_\x7B-\xFF]).{6,15}$/;

            var number = /^[0-9-+]+$/;

            //  var phone = /^\d{10}$/;
            // var company = /^[-\w\s]+$/;
            var alphanumeric = /[a-zA-Z0-9\-\_]$/;

            // var email = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/; //^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            var text = /^[a-zA-Z ]*$/;
            //var alphabit = /^[a-zA-Z]+$/;

            if (city == "0")
            {
//                alert("select the city");
                $("#cities").text(<?php echo json_encode(POPUP_VEHICLETYPE_SELECTCITY); ?>);
            }
            else if (vtype == "" || vtype == null)
            {
                //    alert("please enter  the vehicle type");
                $("#vehicletype").text(<?php echo json_encode(POPUP_VEHICLETYPE_ENTER); ?>);

            }
            else if (!text.test(vtype))
            {
//                alert("enter the  vehicle type as  text");
                $("#vehicletype").text(<?php echo json_encode(POPUP_VEHICLETYPE_ENTERTEXT); ?>);
            }

            else if (seating == "" || seating == null || seating == "0")
            {
//                alert("please enter number of seatings");
                $("#seat").text(<?php echo json_encode(POPUP_VEHICLETYPE_NOSEATINGS); ?>);
            }
            else if (!number.test(seating))
            {
//                alert("enter the seating as number");
                $("#seat").text(<?php echo json_encode(POPUP_VEHICLETYPE_NUMSEATINGS); ?>);
            }
            else if (mfare == "" || mfare == null)
            {
                $("#minimum").text(<?php echo json_encode(POPUP_VEHICLETYPE_MINMUM); ?>);
            }
//            else if (!number.test(mfare))
//            {
//                $("#minimum").text(<?php echo json_encode(POPUP_VEHICLETYPE_MINMUM_NUMBER); ?>);
//            }
            else if (bfare == "" || bfare == null)
            {
//                alert("enter the Base Fare");
                $("#base").text(<?php echo json_encode(POPUP_VEHICLETYPE_BASEFARE); ?>);
            }
//            else if (!number.test(bfare))
//            {
////                alert("enter the Base Fare as number only");
//                $("#base").text(<?php echo json_encode(POPUP_VEHICLETYPE_BASEFARENUM); ?>);
//            }

            else if (ppmnt == "" || ppmnt == null)
            {
//                alert("enter the cost per minute");
                $("#pricepermin").text(<?php echo json_encode(POPUP_VEHICLETYPE_MINUTE); ?>);
            }
//            else if (!number.test(ppmnt))
//            {
////                alert("enetr price per minute as a number");
//                $("#pricepermin").text(<?php echo json_encode(POPUP_VEHICLETYPE_NUMMINUTE); ?>);
//            }

            else if (ppkm == "" || ppkm == null)
            {
//                alert("enter the cost per kilometer");
                $("#pricekm").text(<?php echo json_encode(POPUP_VEHICLETYPE_KM); ?>);
            }
             else if (type_on_image == "" || type_on_image == null)
            {
               
                $("#type_on_imageErr").text(<?php echo json_encode(POPUP_VEHICLETYPE_ON_IMAGE); ?>);
            } 
            else if (type_off_image == "" || type_off_image == null)
            {
//                alert("enter the cost per kilometer");
                $("#type_off_imageErr").text(<?php echo json_encode(POPUP_VEHICLETYPE_OFF_IMAGE); ?>);
            }
//             else if (type_map_image == "" || type_map_image == null)
//            {
////                alert("enter the cost per kilometer");
//                $("#type_map_imageErr").text(<?php echo json_encode(POPUP_VEHICLETYPE_MAP_IMAGE); ?>);
//            }
//            else if (waiting_charge_per_min == "" || waiting_charge_per_min == null)
//            {
////                alert("enter the cost per kilometer");
//                $("#waiting_chargeErr").text(<?php echo json_encode(POPUP_VEHICLETYPE_WAITING_CHARGE_ERR); ?>);
//            }
           

//            else if (!number.test(ppkm))
//            {
////                alert("enetr price per kilometer as number");
//                $("#pricekm").text(<?php echo json_encode(POPUP_VEHICLETYPE_KMNUM); ?>);
//            }
//            else if (cancilationfee != "" || ppkm != null)
//            {
////                alert("enter the cost per kilometer");
//                $("#cancilationf").text(<?php echo json_encode(POPUP_VEHICLETYPE_KM); ?>);
//            }
//            else if (!number.test(ppkm))
//            {
////                alert("enetr price per kilometer as number");
//                $("#pricekm").text(<?php echo json_encode(POPUP_VEHICLETYPE_KMNUM); ?>);
//            }


//
//            else if (discription_s == "" || discription_s == null)
//            {
//                alert("enter the type of vehicle");
//            }
//            else if (!text.test(discription_s))
//            {
//                alert("enetr vehicle type as text");
//            }




            else
            {
                $('#addentity').submit();
//                
//                    $.ajax({
//                    url: "<?php echo base_url('index.php/superadmin') ?>/insert_vehicletype",
//                    type: 'POST',
//                    data: {
//                        vehicletype: vtype,
//                        seating: seating,
//                        minimumfare: mfare,
//                        basefare: bfare,
//                        priceperminute: ppmnt,
//                        priceperkm: ppkm,
//                        discription: discription_s,
//                        city: city,
//                        cancilationfee:cancilationfee,
//                        type_on_image:type_on_image,
//                        type_off_image:type_off_image,
//                        type_map_image:type_map_image,
//                        waiting_charge:waiting_charge_per_min
//
//                    },
//                    dataType: 'JSON',
//                    success: function (response)
//                    {
//                        
//                           var size = $('input[name=stickup_toggler]:checked').val()
//                        var modalElem = $('#confirmmodelsa');
//                        if (size == "mini")
//                        {
//                            $('#modalStickUpSmall').modal('show')
//                        }
//                        else
//                        {
//                            $('#confirmmodelsa').modal('show')
//                            if (size == "default") {
//                                modalElem.children('.modal-dialog').removeClass('modal-lg');
//                            }
//                            else if (size == "full") {
//                                modalElem.children('.modal-dialog').addClass('modal-lg');
//                            }
//                        }
//                          if(response.flag == 1){
//                               $("#errorboxdatasa").text(response.msg);
//                          }
//                          else
//                               $("#errorboxdatasa").text(<?php echo json_encode(POPUP_COUNTRY_ADDED); ?>);
////                           $("#confirmedsa").hide();
////                        alert(response.msg);
//                        $("#vehicletypename").val('');
//                        $("#seating").val('');
//                        $("#minimumfare").val('');
//                        $("#basefare").val('');
//                        $("#priceperminute").val('');
//                        $("#priceperkm").val('');
//                        $("#discrption").val('');
//                        $("#city").val('');
//                        
//                        $("#confirmedsa").click(function(){
//                           window.location =  "<?php echo base_url('index.php/superadmin') ?>/vehicle_type";
//                        });
////                         window.location =  "<?php echo base_url('index.php/superadmin') ?>/vehicle_type";
//                    }
//                    
//                });
//

            }


        });




        $("#exx").click(function () {


            $("#vehicle_type_name").text("");
            $("#vehicle_seating").text("");
            $("#vehicle_minimumfare").text("");
            $("#vehicletype_basefare").text("");
            $("#vehicletype_pricepermin").text("");
            $("#vehicletype_priceperkm").text("");
            $("#vehicletype_description").text("");
            $("#vehicletype_cities").text("");


            var vtype = $("#vehicletypename_s").val();
            var seating = $("#seating_s").val();
            var mfare = $("#minimumfare_s").val();
            var bfare = $("#basefare_s").val();
            var ppmnt = $("#priceperminute_s").val();
            var ppkm = $("#priceperkm_s").val();
            var discription_s = $("#discrption_s").val();
            var city = $("#city_s").val();
            
            var waiting_charge_edit = $("#waiting_charge_edit").val();
            var type_on_image_edit = $("#type_on_image_edit").val();
            var type_off_image_edit = $("#type_off_image_edit").val();
            var type_map_image_edit = $("#type_map_image_edit").val();



            //  var password = /^(?=.*\d)(?=.*[a-zA-Z])(?!.*[\W_\x7B-\xFF]).{6,15}$/;

            var number = /^[0-9-+]+$/;

            //  var phone = /^\d{10}$/;
            // var company = /^[-\w\s]+$/;
            var alphanumeric = /[a-zA-Z0-9\-\_]$/;

            // var email = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/; //^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            var text = /^[a-zA-Z ]*$/;



            if (vtype == "" || vtype == null)
            {
//                alert("please enter  the vehicle type");
                $("#vehicle_type_name").text(<?php echo json_encode(POPUP_VEHICLETYPE_ENTER); ?>);
            }
            else if (!text.test(vtype))
            {
//               
                $("#vehicle_type_name").text(<?php echo json_encode(POPUP_VEHICLETYPE_ENTERTEXT); ?>);
            }

            else if (seating == "" || seating == null || seating == "0")
            {
//                
                $("#vehicle_seating").text(<?php echo json_encode(POPUP_VEHICLETYPE_NOSEATINGS); ?>);
            }
            else if (!number.test(seating))
            {
//               
                $("#vehicle_seating").text(<?php echo json_encode(POPUP_VEHICLETYPE_NUMSEATINGS); ?>);
            }
            else if (mfare == "" || mfare == null)
            {
                $("#vehicle_minimumfare").text(<?php echo json_encode(POPUP_VEHICLETYPE_MINMUM); ?>);

            }
//            else if (!number.test(mfare))
//            {
//               $("#vehicle_minimumfare").text(<?php echo json_encode(POPUP_VEHICLETYPE_MINMUM_NUMBER); ?>);
//               
//            }
            else if (bfare == "" || bfare == null)
            {
//                alert("enter the base fare");
                $("#vehicletype_basefare").text(<?php echo json_encode(POPUP_VEHICLETYPE_BASEFARE); ?>);
            }
//            else if (!number.test(bfare))
//            {
////                alert("enter the base fare as number only");
//                  $("#vehicletype_basefare").text(<?php echo json_encode(POPUP_VEHICLETYPE_BASEFARENUM); ?>);
//            }

            else if (ppmnt == "" || ppmnt == null)
            {
//                alert("enter the cost per minute");
                $("#vehicletype_pricepermin").text(<?php echo json_encode(POPUP_VEHICLETYPE_MINUTE); ?>);
            }
//            else if (!number.test(ppmnt))
//            {
////                alert("enetr price per minute as a number");
//                   $("#vehicletype_pricepermin").text(<?php echo json_encode(POPUP_VEHICLETYPE_NUMMINUTE); ?>);
//            }

            else if (ppkm == "" || ppkm == null)
            {
//                alert("enter the price per kilometer");
                $("#vehicletype_priceperkm").text(<?php echo json_encode(POPUP_VEHICLETYPE_KM); ?>);
            }
//            else if (!number.test(ppkm))
//            {
////                alert("enetr price per kilometer as number");
//                 $("#vehicletype_priceperkm").text(<?php echo json_encode(POPUP_VEHICLETYPE_KMNUM); ?>);
//            }


            else if (city == "0")
            {
                //alert("select the city ");
                $("#vehicletype_cities").text(<?php echo json_encode(POPUP_VEHICLETYPE_SELECTCITY); ?>);
            }
//             else if (type_on_image_edit == "" || type_on_image_edit == null)
//            {
//                
//                $("#type_on_image_editErr").text(<?php echo json_encode(POPUP_VEHICLETYPE_ON_IMAGE); ?>);
//            } 
//            else if (type_off_image_edit == "" || type_off_image_edit == null)
//            {
////                alert("enter the cost per kilometer");
//                $("#type_off_image_editErr").text(<?php echo json_encode(POPUP_VEHICLETYPE_OFF_IMAGE); ?>);
//            }
//             else if (type_map_image_edit == "" || type_map_image_edit == null)
//            {
////                alert("enter the cost per kilometer");
//                $("#type_map_image_editErr").text(<?php echo json_encode(POPUP_VEHICLETYPE_MAP_IMAGE); ?>);
//            }
//            else if (waiting_charge_edit == "" || waiting_charge_edit == null)
//            {
////                alert("enter the cost per kilometer");
//                $("#waiting_charge_editErr").text(<?php echo json_encode(POPUP_VEHICLETYPE_WAITING_CHARGE_ERR); ?>);
//            }
            else
            {
                $('#updateentity').submit();
                
//                var size = $('input[name=stickup_toggler]:checked').val()
//                var modalElem = $('#confirmmodels');
//                if (size == "mini")
//                {
//                    $('#modalStickUpSmall').modal('show')
//                }
//                else
//                {
//                    $('#confirmmodels').modal('show')
//                    if (size == "default") {
//                        modalElem.children('.modal-dialog').removeClass('modal-lg');
//                    }
//                    else if (size == "full") {
//                        modalElem.children('.modal-dialog').addClass('modal-lg');
//                    }
//                }
//                $("#errorboxdatas").text(<?php echo json_encode(POPUP_COMPANY_EDITED_D); ?>);
            }
        });




        $(ok).click(function () {
//             alert("hai");

            var vtype = $("#vehicletypename_s").val();
            var seating = $("#seating_s").val();
            var mfare = $("#minimumfare_s").val();
            var bfare = $("#basefare_s").val();
            var ppmnt = $("#priceperminute_s").val();
            var ppkm = $("#priceperkm_s").val();
            var discription_s = $("#discrption_s").val();
            var city = $("#city_s").val();
            var cancilationfee = $("#cancilationfee_s").val();
            
       


            $.ajax({
                url: "<?php echo base_url('index.php/superadmin') ?>/update_vehicletype/<?php echo $param; ?>",
                                type: 'POST',
                                data: {
                                    vehicletype: vtype,
                                    seating: seating,
                                    minimumfare: mfare,
                                    basefare: bfare,
                                    priceperminute: ppmnt,
                                    priceperkm: ppkm,
                                    discription: discription_s,
                                    city: city
//                                    cancilationfee: cancilationfee

                                },
                                dataType: 'JSON',
                                success: function (response)
                                {

                                    window.location = "<?php echo base_url('index.php/superadmin') ?>/vehicle_type";

//                                            
                                }
                            });
//
                        });

                        $(no).click(function () {
//                            window.location = "<?php echo base_url('index.php/superadmin') ?>/company_s/1";
                            $('.close').trigger('click');
                        });



                        $('#discrption_s').keypress(function (e) {
                            var key = e.which;
                            if (key == 13)  // the enter key code
                            {
                                $("#exx").trigger('click');
                            }
                        });



                        $('.number').keypress(function (event) {
                            if (event.which < 46
                                    || event.which > 59) {
                                event.preventDefault();
                            } // prevent if not number/dot

                            if (event.which == 46
                                    && $(this).val().indexOf('.') != -1) {
                                event.preventDefault();
                            } // prevent if already dot
                        });


                    });




                    function isNumber(evt) {
                        evt = (evt) ? evt : window.event;
                        var charCode = (evt.which) ? evt.which : evt.keyCode;
                        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                            return false;
                        }
                        return true;
                    }


</script>
<style>
    .col-sm-4 {
        width: 67px; 
    }
</style>


<div class="page-content-wrapper">
    <!-- START PAGE CONTENT -->
    <div class="content">
        <!-- START JUMBOTRON -->
        <div class="jumbotron bg-white" data-pages="parallax">
            <div class="inner">
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb" style="margin-left: 20px;">
                    <li><a href="<?php echo base_url('index.php/superadmin') ?>/vehicle_type" class=""><?PHP ECHO LIST_VEHICLETYPE; ?></a>
                    </li>

                    <li ><a href="#" class="active">ADD VEHICLE TYPE</a>
                    </li>

                </ul>
                <!-- END BREADCRUMB -->
            </div>



            <div class="container-fluid container-fixed-lg bg-white">

                <div id="rootwizard" class="m-t-50">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" id="mytabs">
                        <?php if ($param == '') {
                            $activetab1 = "active";
                            ?>

                            <li class="active" id="firstlitab" onclick="managebuttonstate()">
                                <a data-toggle="tab" href="#tab1" id="tb1"><i id="tab1icon" class=""></i> <span> <?PHP ECHO LIST_ADD_VEHICLETYPE_DETAILS; ?></span></a>
                            </li>
                            <?php
                        } else {
                            $activetab2 = "active";
                            ?>
                            <li class="active" id="secondlitab">
                                <a data-toggle="tab" href="#tab2" onclick="profiletab('secondlitab', 'tab2')" id="mtab2"><i id="tab2icon" class=""></i> <span><?PHP ECHO LIST_EDIT_VEHICLETYPE_DETAILS; ?></span></a>
                            </li>
<?php } ?>

                    </ul>
                    <!-- Tab panes -->
                         <div class="tab-content">
                            <div class="tab-pane padding-20 slide-left <?php echo $activetab1 ?>" id="tab1">

                                 <form id="addentity" method="post" class="form-horizontal" role="form" action="<?php echo base_url(); ?>index.php/superadmin/insert_vehicletype"  enctype="multipart/form-data">

                  
                                <div class="row row-same-height">


                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_CITY; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">


                                            <select id="city" name="country_select"  class="form-control" >
                                                <option value="0">Select city</option>
                                                <?php
                                                foreach ($city as $result) {

                                                    echo "<option value=" . $result->City_Id . ">" . $result->City_Name . "</option>";
                                                }
                                                ?>

                                            </select>

                                        </div>
                                        <div class="col-sm-3 error-box" id="cities"></div>

                                    </div>


                                    <div class="form-group" class="formexx">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_NAME; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" id="vehicletypename" name="vehicletypename" class="form-control">

                                        </div>
                                        <div class="col-sm-3 error-box" id="vehicletype"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_SEATINGCAPACITY; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" id="seating" name="seating" class="form-control"  onkeypress="return isNumber(event)">

                                        </div>
                                        <div class="col-sm-3 error-box" id="seat"></div>

                                    </div>

                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_MINIMUMFARE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" id="minimumfare" name="minimumfare" class="form-control number" >

                                        </div>
                                        <div class="col-sm-3 error-box" id="minimum"></div>
                                    </div>


                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_BASEFARE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" id="basefare" name="basefare" class="form-control number"  >

                                        </div>
                                        <div class="col-sm-3 error-box" id="base"></div>
                                    </div>



                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_PRICEMINUTE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" id="priceperminute" name="priceperminute" class="form-control number">

                                        </div>
                                        <div class="col-sm-3 error-box" id="pricepermin"></div>

                                    </div>

                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_WAITING_CHARGE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" id="waiting_charge" name="waiting_charge" class="form-control number">

                                        </div>
                                        <div class="col-sm-3 error-box" id="waiting_chargeErr"></div>

                                    </div>



                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_PRICEKM; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" id="priceperkm" name="priceperkm" class="form-control number">

                                        </div>
                                        <div class="col-sm-3 error-box" id="pricekm"></div>

                                    </div>
                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_CANCILATIONFEE; ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" id="cancilationfee" name="cancilationfee" class="form-control number">

                                        </div>
                                        <div class="col-sm-3 error-box" id="cancilationf"></div>

                                    </div>

                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_ONIMAGE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">

                                            <input type="file" id="type_on_image" name="type_on_image" class="form-control">

                                        </div>
                  
                                        <div class="col-sm-3 error-box" id="type_on_imageErr"></div>

                                    </div>

                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_OFFIMAGE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="file" id="type_off_image" name="type_off_image" class="form-control">

                                        </div>
                                        <div class="col-sm-3 error-box" id="type_off_imageErr"></div>

                                    </div>

                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_MAPIMAGE; ?></label>
                                        <div class="col-sm-6">
                                            <input type="file" id="type_map_image" name="type_map_image" class="form-control">

                                        </div>
                                        <div class="col-sm-3 error-box" id="type_map_imageErr"></div>

                                    </div>


                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_DESCRIPTION; ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" id="discrption" name="descrption" class="form-control">

                                        </div>
                                        <div class="col-sm-3 error-box" id="disc"></div>

                                    </div>




                                    <div>

                                        <div class="pull-right m-t-10"> <button type="button"  id="addvehicle" class="btn btn-primary btn-cons"><?php echo BUTTON_ADD_VEHICLETYPE; ?></button></div>
                                        <div class="pull-right m-t-10"> <button  type="button" class="btn btn-primary btn-cons" id="cancel"><?php echo BUTTON_CANCEL; ?></button></div>


                                    </div>   
                                </div>
                                 </form>
                            </div>



                            <div class="tab-pane slide-left padding-20 <?php echo $activetab2 ?>" id="tab2">
                                 <form id="updateentity" method="post" class="form-horizontal" role="form" action="<?php echo base_url('index.php/superadmin') ?>/update_vehicletype/<?php echo $param;?>"  enctype="multipart/form-data">

                  
                                <div class="row row-same-height">
                                    <?php
                                    foreach ($editvehicletype as $value) {
                                        ?>
                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_CITY; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">


                                                <select id="city_s" name="city_select_s"  class="form-control" style="-webkit-appearance: none;" >
                                                    <!--<option value="0"><?php // echo $value->city_id;?></option>-->
                                                    <?php
                                                    foreach ($city as $result) {

                                                        $selected = "";
                                                        if ($result->City_Id == $value->city_id)
                                                            $selected = "selected";

                                                        echo "<option value='" . $result->City_Id . "'" . $selected . ">" . $result->City_Name . "</option>";
                                                    }
                                                    ?>

                                                </select>

                                            </div>
                                            <div class="col-sm-3 error-box" id="vehicletype_cities"></div>

                                        </div>

                                        <div class="form-group" class="formexx">
                                            <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_NAME; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" id="vehicletypename_s" name="vehicletypename_s" class="form-control" value="<?php echo $value->type_name; ?>">

                                            </div>
                                            <div class="col-sm-3 error-box" id="vehicle_type_name"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_SEATINGCAPACITY; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" id="seating_s" name="seating_s" class="form-control" value="<?php echo $value->max_size; ?>" onkeypress="return isNumber(event)">

                                            </div>
                                            <div class="col-sm-3 error-box" id="vehicle_seating"></div>
                                        </div>

                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_MINIMUMFARE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" id="minimumfare_s" name="minimumfare_s" class="form-control number" value="<?php echo $value->min_fare; ?>">

                                            </div>
                                            <div class="col-sm-3 error-box" id="vehicle_minimumfare"></div>

                                        </div>


                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_BASEFARE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" id="basefare_s" name="basefare_s" class="form-control number" value="<?php echo $value->basefare; ?>">

                                            </div>
                                            <div class="col-sm-3 error-box" id="vehicletype_basefare"></div>

                                        </div>


                                     <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_WAITING_CHARGE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" id="waiting_charge_edit" name="waiting_charge_edit" class="form-control number" value="<?php echo $value->waiting_charge_per_min;?>">

                                        </div>
                                        <div class="col-sm-3 error-box" id="waiting_charge_editErr"></div>

                                    </div>

                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_PRICEMINUTE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" id="priceperminute_s" name="priceperminute_s" class="form-control number" value="<?php echo $value->price_per_min; ?>">

                                            </div>
                                            <div class="col-sm-3 error-box" id="vehicletype_pricepermin"></div>

                                        </div>



                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_PRICEKM; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" id="priceperkm_s" name="priceperkm_s" class="form-control number" value="<?php echo $value->price_per_km; ?>">

                                            </div>
                                            <div class="col-sm-3 error-box" id="vehicletype_priceperkm"></div>
                                        </div>

                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_CANCILATIONFEE; ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" id="cancilationfee_s" name="cancilationfee_s" class="form-control number" value="<?php echo $value->cancilation_fee; ?>">

                                            </div>
                                            <div class="col-sm-3 error-box" id="cancilationf_s"></div>

                                        </div>
                                    
                                        
                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_ONIMAGE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">

                                            <input type="file" id="type_on_image_edit" name="type_on_image_edit" class="form-control">
                                            
                                         <input type="hidden" value="<?php echo $value->type_on_image; ?>" id='viewimage_on_image'>
                                                <?php
                                                if ($value->vehicle_img != '') {
                                                    ?>
                                                    <a target="_blank" href="<?php echo base_url()?>../../pics/<?php echo $value->vehicle_img; ?>">view</a> 

                                                <?php }
                                                ?>

                                        </div>
                                        <div class="col-sm-3 error-box" id="type_on_image_editErr"></div>

                                    </div>

                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_OFFIMAGE; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="file" id="type_off_image_edit" name="type_off_image_edit" class="form-control">

                                             <input type="hidden" value="<?php echo $value->type_off_image; ?>" id='viewimage_off_image'>
                                                <?php
                                                if ($value->vehicle_img_off != '') {
                                                    ?>
                                                    <a target="_blank" href="<?php echo base_url()?>../../pics/<?php echo $value->vehicle_img_off; ?>">view</a> 

                                                <?php }
                                                ?>
                                        </div>
                                        <div class="col-sm-3 error-box" id="type_off_image_editErr"></div>

                                    </div>

                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_MAPIMAGE; ?></label>
                                        <div class="col-sm-6">
                                            <input type="file" id="type_map_image_edit" name="type_map_image_edit" class="form-control">

                                            <input type="hidden" value="<?php echo $value->map_icon; ?>" id='viewimage_map_image'>
                                                <?php
                                                if ($value->MapIcon != '') {
                                                    ?>
                                                    <a target="_blank" href="<?php echo base_url()?>../../pics/<?php echo $value->MapIcon; ?>">view</a> 

                                                <?php }
                                                ?>
                                        </div>
                                        <div class="col-sm-3 error-box" id="type_map_image_editErr"></div>

                                    </div>
                                    


                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label"><?PHP ECHO FIELD_VEHICLETYPE_DESCRIPTION; ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" id="discrption_s" name="discrption_s" class="form-control" value="<?php echo $value->type_desc; ?>">

                                            </div>
                                            <div class="col-sm-3 error-box" id="vehicletype_description"></div>
                                        </div>








                                        <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="exx" type="button"><?php echo BUTTON_CHANGES_COMPANY; ?></button></div>
                                        <div class="pull-right m-t-10"> <button type="button" class="btn btn-primary btn-cons" id="cancel_s"><?php echo BUTTON_CANCEL ?></button></div>

                                    <?php }
                                    ?>

                                </div>
                                      </form>
                            </div>
                             



                        </div>
                   

                </div>


            </div>



        </div>


    </div>
    <!-- END PANEL -->
</div>


<!-- END JUMBOTRON -->

<!-- START CONTAINER FLUID -->
<div class="container-fluid container-fixed-lg">
    <!-- BEGIN PlACE PAGE CONTENT HERE -->

    <!-- END PLACE PAGE CONTENT HERE -->
</div>
<!-- END CONTAINER FLUID -->


<!-- END PAGE CONTENT -->



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



<div class="modal fade stick-up" id="confirmmodelsa" tabindex="-1" role="dialog" aria-hidden="true">
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

                    <div class="error-box" id="errorboxdatasa" style="font-size: large;text-align:center"><?php echo VEHICLEMODEL_DELETE; ?></div>

                </div>
            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4" ></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-12" >
                        <button type="button" class="btn btn-primary pull-right" id="confirmedsa" ><?php echo BUTTON_OK; ?></button>
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
                    <div class="col-sm-4 pull-right"> <button type="button" class="btn btn-primary pull-right" id="no"><?php echo BUTTON_NO; ?></button></div>
                    <div class="col-sm-4 pull-right">
                        <button type="button" class="btn btn-primary pull-right" id="ok" ><?php echo BUTTON_YES; ?></button>
                    </div>
                </div>
            </div>


        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>