<div class="page-container" xmlns="http://www.w3.org/1999/html">
    <!-- START PAGE HEADER WRAPPER -->
    <!-- START HEADER -->

    <script>

        $(document).ready(function () {

            $('#selectedcity').change(function () {

                $.ajax({
                    url: "<?php echo base_url('index.php/companyadmin') ?>/showcompanys",
                    type: "POST",
                    data: {city: $(this).val(), vt: '1'},
//                dataType: 'JSON',
                    success: function (response)
                    {
//                    $(this).val()
                        $("#companyid").html(response);
                        refreshTableOnActualcitychagne();

//                    $("#companyid").val("<?php //$this->session->userdata('company_id')  ?>//");
                    }
                });

            });
            $('#companyid').change(function () {

                $.ajax({
                    url: "<?php echo base_url('index.php/companyadmin') ?>/setcity_session",
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
//alert("<?php //echo  $this->session->userdata('city_id')  ?>//");
//alert("<?php //echo  $this->session->userdata('company_id')  ?>//");
                $('#selectedcity').val("<?php echo $this->session->userdata('city_id') ?>");
                $.ajax({
                    url: "<?php echo base_url('index.php/companyadmin') ?>/showcompanys",
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
        });
    </script>





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
<!--                    <img src="--><?php //echo base_url();  ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();  ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();  ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

                    <strong> COMPANY MANAGEMENT CONSOLE </strong>
                </div>

                <div class="brand inline" style="width:auto">
<!--                    <img src="--><?php //echo base_url();               ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();               ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();               ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->
                    <div class="form-group " >
                        <!--<label for="fname" class="col-sm-6 control-label" style="margin-top: 10px;font-size: 13px;padding:0px">SELECT CITY</label>-->
                        <div class="col-sm-8" style="width: auto;
                             paddingng: 0px;
                             margin-bottom: 10px;" >

                            <select id="selectedcity" name="company_select" class="form-control"  onchange="loadcompay()">
                                <!--<option value="0">Select city</option>-->
                                <?php $city = $this->db->query("select * from city_available ")->result(); ?>
                                <option value="0">None</option>
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
<!--                    <img src="--><?php //echo base_url();               ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();               ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();               ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->
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
                    <img id="nav_user_img" data-toggle="dropdown" style="border-radius: 28px;margin-top: 4px;margin-right: 7px;cursor: pointer;" data-hover="dropdown" src="http://107.170.66.211/roadyo_live/pics/hdpi/<?php echo $this->session->userdata("profile_pic"); ?>" alt="" data-src="http://107.170.66.211/roadyo_live/pics/hdpi/<?php echo $this->session->userdata("profile_pic"); ?>" data-src-retina="http://107.170.66.211/roadyo_live/pics/hdpi/<?php echo $this->session->userdata("profile_pic"); ?>" width="32" height="32">
                        <ul class="dropdown-menu" style="margin-left: -135px;margin-top: 14px;background: #ffffff;width: 171px;">
                            <li>
                                <div class="row center-margin m-b-10">
                                    <div class="col-xs-2 text-center">
                                        <i class="fs-14 sl-user-follow"></i>
                                    </div>
                                    <div class="col-xs-8 text-center">
                                        <a tabindex="-1" href="<?php echo base_url(); ?>index.php/companyadmin/profile">My Profile</a>
                                    </div>
                                </div>

                            </li>
                            <li class="divider"></li>

                            <li>

                                <center><a tabindex="-1" href="<?php echo base_url(); ?>index.php/companyadmin/Logout">Logout</a></center>
                            </li>

                        </ul>
                </div>

            </div>
            <!-- END User Info-->
        </div>
    </div>

