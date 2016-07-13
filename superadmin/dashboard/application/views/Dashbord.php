<script>
    $(document).ready(function() {
        $('#uimg').on('mouseenter', function() {

            $('#showbtn').show();
        });
           $('#uimg').on('mouseleave', function() {
            $('#showbtn').hide();
        });
    });

</script>
<style>
    #showbtn{
        
    }
</style>
<div class="page-content-wrapper">
    <!-- START PAGE CONTENT -->
    <div class="content">
        <!-- START JUMBOTRON -->
        <div class="jumbotron" data-pages="parallax">
            <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">
                <div class="inner">
                    <!-- START BREADCRUMB -->
                    <ul class="breadcrumb">
                        <li>
                            <p>Profile</p>
                        </li>
                        <li><a href="#" class="active"> </a>
                        </li>
                    </ul>
                    <!-- END BREADCRUMB -->
                </div>


                <div class="panel panel-transparent">
                    <div class="panel-heading ">

                    </div>
                    <div class="panel-body">
                        <div class="col-md-2 sm-no-padding"></div>
                        <div class="col-md-8 sm-no-padding">

                            <div class="panel panel-transparent">
                                <div class="panel-body no-padding">
                                    <div id="portlet-advance" class="panel panel-default">
                                        <div class="panel-heading ">

                                            <div class="panel-controls">
                                                <ul>
                                                    <li>
                                                        <div class="dropdown">
                                                            <a id="portlet-settings" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                                                                <i class="portlet-icon portlet-icon-settings "></i>
                                                            </a>
                                                            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="portlet-settings">
                                                                <li><a href="#">API</a>
                                                                </li>
                                                                <li><a href="#">Preferences</a>
                                                                </li>
                                                                <li><a href="#">About</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                                    <li><a href="#" class="portlet-collapse" data-toggle="collapse"><i class="portlet-icon portlet-icon-collapse"></i></a>
                                                    </li>
                                                    <li><a href="#" class="portlet-refresh" data-toggle="refresh"><i class="portlet-icon portlet-icon-refresh"></i></a>
                                                    </li>
                                                    <li><a href="#" class="portlet-maximize" data-toggle="maximize"><i class="portlet-icon portlet-icon-maximize"></i></a>
                                                    </li>
                                                    <li><a href="#" class="portlet-close" data-toggle="close"><i class="portlet-icon portlet-icon-close"></i></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-4">


                                                    

                                                        <center>
                                                            <div class="col_2">
                                                                <div class="img" id="prof_img">
                                                                    <img style="-webkit-box-shadow: 0 0 8px rgba(0, 0, 0, .8);" class="img-circle" id="uimg" src="http://107.170.66.211/roadyo_live/pics/xxhdpi/<?php echo $userinfo->profile_pic; ?>">

                                                                </div>   
                                                                <div class="img" id="prof_img" style="margin-top: -37px;">

                                                                    <button type="button" id="showbtn" style="display: none;z-index: 1000;position: relative;">Edit</button>
                                                                </div> 
                                                            </div>



                                                        </center>



                                                </div>
                                                <div class="col-sm-6">
                                                    <form id="form-work" class="form-horizontal" method="post" role="form" autocomplete="off" novalidate="novalidate" action="udpadedata/<?php echo $this->session->userdata("PassangeId")?>/slave/slave_id">
                                                        <div class="form-group">
                                                            <label for="fname" class="col-sm-3 control-label">First Name</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control" id="fname" placeholder="Full name"  value="<?php echo $userinfo->first_name; ?>" name="fdata[first_name]" required="" aria-required="true">
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="fname" class="col-sm-3 control-label">Last Name</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control" id="fname" placeholder="Last name" value="<?php echo $userinfo->last_name; ?>" name="fdata[last_name]" required="" aria-required="true">
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="position" class="col-sm-3 control-label">Mobile</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control " id="position" value="<?php echo $userinfo->phone; ?>" placeholder="Designation" name="fdata[phone]"  aria-required="true" aria-invalid="true">
                                                                <!--<label id="position-error" class="error" for="position">This field is required.</label>-->
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="col-sm-3 control-label">Email</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control" id="position" value="<?php echo $userinfo->email; ?>" placeholder="Designation"  name="fdata[email]"   aria-required="true" aria-invalid="true">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="col-sm-3 control-label">Zip Code</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control " id="position" value="<?php echo $userinfo->zipcode; ?>" placeholder="Designation" name="fdata[zipcode]"  aria-required="true" aria-invalid="true">
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <div class="row">
                                                            <div class="col-sm-3">
                                                                <!--<p>I hereby certify that the information above is true and accurate. </p>-->
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <button class="btn btn-success" type="submit">Submit</button>
                                                                <button class="btn btn-default"><i class="pg-close"></i> Clear</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <img src="pages/img/progress/progress-circle-master.svg" style="display:none"></div>
                                </div>
                            </div>


                        </div>
                        <div class="col-md-2 sm-no-padding"></div>
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
                <span class="hint-text">Copyright © 2014</span>
                <span class="font-montserrat">REVOX</span>.
                <span class="hint-text">All rights reserved.</span>
                <span class="sm-block"><a href="#" class="m-l-10 m-r-10">Terms of use</a> | <a href="#" class="m-l-10">Privacy Policy</a>
                </span>
            </p>
            <p class="small no-margin pull-right sm-pull-reset">
                <a href="#">Hand-crafted</a> 
                <span class="hint-text">&amp; Made with Love ®</span>
            </p>
            <div class="clearfix"></div>
        </div>
    </div>
    <!-- END FOOTER -->
</div>