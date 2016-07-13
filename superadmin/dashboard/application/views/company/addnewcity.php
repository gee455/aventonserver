<?php
$this->load->database();
?>
<style>
    .form-horizontal .form-group
    {
        margin-left: 13px;
    }
    #selectedcity,#companyid{
        display: none;
    }

</style>




<script>


    $(document).ready(function () {


        $('.cities').addClass('active');
        $('.cities_thumb').addClass("bg-success");

        $("#ex").click(function () {

            $("#field_countries").text("");
            var data2 = $("#two").val();

            if (data2 == "" || data2 == null)
            {
                //       alert("please enter the country name");
                $("#field_countries").text(<?php echo json_encode(POPUP_CITIES_ENTER_COUNTRY_NAME); ?>);
            }
            else {



                $.ajax({
                    type: 'post',
                    url: "<?php echo base_url('index.php/superadmin') ?>/addingcountry",
                    data: {
                        data2: data2
                    },
                    dataType: "json",
                    success: function (response) {
                        $("#two").val('');



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

                        if (response.flag == 0)
                            $("#errorboxdatas").text(response.msg);
                        else
                            $("#errorboxdatas").text(<?php echo json_encode(POPUP_COUNTRY_ADDED); ?>);

                        $("#confirmeds").hide();

                        $('#countryid').append("<option value='" + response.id + "'>" + data2.toUpperCase() + "</option>");
                    },
                });

            }

        });


        $('.error-box-class').keypress(function () {
            $('.error-box').text('');
        });



        $("#two").on("input", function () {
            var regexp = /[^a-zA-Z/ ]/g;
            if ($(this).val().match(regexp)) {
                $(this).val($(this).val().replace(regexp, ''));
            }
        });



        $("#three").on("input", function () {
            var regexp = /[^a-zA-Z/ ]/g;
            if ($(this).val().match(regexp)) {
                $(this).val($(this).val().replace(regexp, ''));
            }
        });


        $("#one").on("input", function () {
            var regexp = /[^a-zA-Z/ ]/g;
            if ($(this).val().match(regexp)) {
                $(this).val($(this).val().replace(regexp, ''));
            }
        });


        $("#exx").click(function () {

            $("#field_countries").text("");
            $("#field_cities").text("");
            $("#field_currency").text("");

            var data3 = $("#three").val();
            var data = $("#one").val();
            var filter = /^[a-zA-Z ]/g;
            var countryid = $("#countryid").val();

            if (countryid == "0")
            {
          
                $("#field_countries_second").text(<?php echo json_encode(POPUP_CITIES_CITY_ENTER_COUNTRY); ?>);
            }
             else if (data3 == "" || data3 == null)
            {
                $("#field_countries_second").text("");
                $("#field_cities").text(<?php echo json_encode(POPUP_CITIES_CITY_ENTER); ?>);

            }
            else if (!filter.test(data3)) {
                $("#field_cities").text("");
                $("#field_cities").text(<?php echo json_encode(POPUP_CITIES_CITY_ENTERALPHA); ?>);
            }

            else if (data == "" || data == null || data.length > 3)

            {

                $("#field_currency").text(<?php echo json_encode(POPUP_CITIES_CITY_CURENCY); ?>);
            }

            else
            {


                $.ajax({
                    type: 'post',
                    url: "<?php echo base_url('index.php/superadmin') ?>/addingcity",
                    dataType: 'JSON',
                    data: {
                        countryid: countryid,
                        data3: data3,
                        data: data


                    },
                    success: function (response) {

                        $("#three").val('');
                        $("#one").val('');
                        $("#countryid").val('0');

                        var size = $('input[name=stickup_toggler]:checked').val()
                        var modalElem = $('#addcitymodal');
                        if (size == "mini")
                        {
                            $('#modalStickUpSmall').modal('show')
                        }
                        else
                        {
                            $('#addcitymodal').modal('show')
                            if (size == "default") {
                                modalElem.children('.modal-dialog').removeClass('modal-lg');
                            }
                            else if (size == "full") {
                                modalElem.children('.modal-dialog').addClass('modal-lg');
                            }
                        }

                        if (response.flag == 1)
                            $("#cityerror").text(response.msg);
                        else
                            $("#cityerror").text('city added successfully');

                        $("#cityok").hide();
//                         

                        //      alert("city aded successfully");
//                           

                    }


                });
            }


        });

    });

//      $(".close").click(function(){ 
//      window.location="<?php echo base_url('index.php/superadmin') ?>/cities";});







</script>




<div class="page-content-wrapper">
    <!-- START PAGE CONTENT -->
    <div class="content">
        <!-- START JUMBOTRON -->
        <div class="jumbotron bg-white" data-pages="parallax">
            <div class="inner">
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb" style="margin-left: 20px;">
                    <li><a href="cities" class=""><?php echo LIST_CITIES; ?></a>
                    </li>

                    <li style="width: 100px"><a href="#" class="active"> <?php echo LIST_ADDCITIES; ?></a>
                    </li>
                </ul>


                <!-- END BREADCRUMB -->
            </div>



            <div class="container-fluid container-fixed-lg bg-white">

                <div id="rootwizard" class="m-t-50">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" id="mytabs">
                        <li class="active" id="firstlitab" onclick="managebuttonstate()">
                            <a data-toggle="tab" href="#tab1" id="tb1"><i id="tab1icon" class=""></i> <span> <?php echo LIST_ADD_COUNTRY_DETAILS; ?></span></a>
                        </li>
                        <li class="" id="secondlitab">
                            <a data-toggle="tab" href="#tab2" onclick="profiletab('secondlitab', 'tab2')" id="mtab2"><i id="tab2icon" class=""></i> <span><?php echo LIST_ADD_CITY_DETAILS; ?></span></a>
                        </li>

                    </ul>
                    <!-- Tab panes -->

                </div>
                <div>
                    <form id="addentity" class="form-horizontal" role="form"   enctype="multipart/form-data">

                        <div class="tab-content">
                            <div class="tab-pane padding-20 slide-left active" id="tab1">

                                <div class="row row-same-height">


                                    <div class="form-group">
                                        <label for="address" class="col-sm-3 control-label"><?php echo FIELD_ENTER_COUNTRY_NAME; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" id="two" name="countryname" class="form-control error-box-class">

                                        </div><div class="col-sm-3 error-box" id="field_countries"></div>


                                    </div>


                                    <div>

                                        <div class="pull-right m-t-10"> <button id="ex" type="button" class="btn btn-primary btn-cons"><?php echo BUTTON_ADDCOUNTRY; ?></button></div>
                                    </div>






                                </div>
                            </div>
                            <div class="tab-pane slide-left padding-20" id="tab2">
                                <div class="row row-same-height">


                                    <div class="form-group" class="formex">
                                        <label for="address" class="col-sm-3 control-label"><?php echo FIELD_CITIES_COUNTRY; ?><span style="color:red;font-size: 18px">*</span></label>
                                        <div class="col-sm-6">

                                            <select id="countryid" name="country_select"  class="form-control error-box-class">
                                                <option value="0">Select Country</option>
                                                <?php
                                                foreach ($country as $result) {

                                                    echo '<option value="' . $result->Country_Id . '">' . ucwords(strtolower($result->Country_Name)) . '  </option>';
                                                }
                                                ?>

                                            </select>
                                        </div>
                                   
                                        <div class="col-sm-3 error-box" id="field_countries_second">
                                            
                                        </div>
                                </div>

                                <div class="form-group" class="formex">
                                    <label for="address" class="col-sm-3 control-label"><?php echo FIELD_CITIES_CITYNAME_NAME; ?><span style="color:red;font-size: 18px">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="text" id="three" name="cityname" class="form-control error-box-class">

                                    </div>
                                    <div class="col-sm-3 error-box" id="field_cities">
                                        
                                    </div>
                                        
                                </div>



                                <div class="form-group" class="formexx">
                                    <label for="address" class="col-sm-3 control-label"><?php echo FIELD_CITIES_CURRENCY_NAME; ?><span style="color:red;font-size: 18px">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="text" id="one" name="currency" class="form-control error-box-class">

                                    </div><div class="col-sm-3 error-box" id="field_currency"></div></div>





                                <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="exx" type="button"><?php echo BUTTON_ADDCITY; ?></button></div>


                            </div>
                        </div>
                </div>

                <div>


                </div>
            </div>

        </div>

    </div>


</div>





<!-- END PANEL -->









<!-- END JUMBOTRON --
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
                        <button type="button" class="btn btn-primary pull-right" id="confirmeds" ><?php echo BUTTON_YES; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



<div class="modal fade stick-up" id="addcitymodal" tabindex="-1" role="dialog" aria-hidden="true">
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

                    <div class="error-box" id="cityerror" style="font-size: large;text-align:center"><?php echo VEHICLEMODEL_DELETE; ?></div>

                </div>
            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4" ></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4" >
                        <button type="button" class="btn btn-primary pull-right" id="cityok" ><?php echo BUTTON_YES; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



<!--<script src="http://107.170.66.211/apps/RylandInsurence/RylandInsurence/javascript/RylandInsurence.js" type="text/javascript"></script>-->




