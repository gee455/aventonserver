<script>
    $(document).ready(function () {
        $('#uimg').on('mouseenter', function () {

            $('#showbtn').show();
        });
        $('#uimg').on('mouseleave', function () {
            $('#showbtn').hide();
        });

        $('#profile_img_upload_click').click(function () {

            $('#poponclick').trigger('click');

        });
        $('#poponclick').change(function () {


            var DivId = 0;


            var formElement = $('#poponclick').prop('files')[0];              //document.getElementById("files_upload_form");
            var form_data = new FormData();

            form_data.append('myfile', formElement);
            form_data.append('uploadType', 'profile');
            form_data.append('type', 'slave');


            $.ajax({
                url: "<?php echo base_url(); ?>application/views/passenger/upload_images_on_local.php",
                type: "POST",
                data: form_data,
                dataType: "JSON",
                async: false,
                success: function (result) {

                    $('#profile_pic_to_save').val(result.fileName);
                    $('#uimg,#nimg').attr('src','<?php echo base_url()?>../../pics/xxhdpi/'+result.fileName);
                },
                cache: false,
                contentType: false,
                processData: false
            });





        });
    });

</script>
<style>
    #showbtn{

    }
    .img-circle {
        border-radius: 50%;
        width: 100px;
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
                            <p><b>Profile</b></p>
                        </li>
                       
                    </ul>
                    <!-- END BREADCRUMB -->
                </div>

                <form enctype="multipart/form-data" id="uploadFile">
                    <input type="file" style="display: none" name="myfile_upload" id="poponclick">
                </form>
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

<!--                                            <div class="panel-controls">
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
                                            </div>-->
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-4">




                                                    <center>
                                                        <div class="col_2">
                                                            <div class="img" id="prof_img">
<!--                                                                <img style="-webkit-box-shadow: 0 0 8px rgba(0, 0, 0, .8);" class="img-circle" id="uimg" 
                                                                     src="<?php echo PIC_PATH . $userinfo->profile_pic; ?>">-->
                                                                 <img style="-webkit-box-shadow: 0 0 8px rgba(0, 0, 0, .8);width: 123px;height: 132px" 
                                                                         class="img-circle" id="uimg" src="<?php echo base_url().'../../pics/'.$userinfo->profile_pic; ?>" 
                                                                         enctype="multipart/form-data">
                                                            </div>
                                                            <div class="img" id="prof_img" style="margin-top: 28px;">

                                                                <button class="btn btn-success btn-cons m-b-10" type="button" id="profile_img_upload_click"><i class="fa fa-cloud-upload"></i> <span class="bold">Upload</span>
                                                                </button>
                                                            </div>
                                                        </div>



                                                    </center>



                                                </div>
                                                <div class="col-sm-6">
                                                    <form id="form-work" class="form-horizontal" method="post" role="form" autocomplete="off" novalidate="novalidate" action="udpadedata/<?php echo $this->session->userdata("LoginId") ?>/slave/slave_id">
                                                        <div class="form-group">
                                                            <label for="fname" class="col-sm-3 control-label">First Name</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control" id="fname" placeholder="Full name"  value="<?php echo $userinfo->first_name; ?>" name="fdata[first_name]" required="" aria-required="true">
                                                            </div>
                                                        </div>
                                                        <input type="hidden" value="<?php echo $userinfo->profile_pic; ?>" id="profile_pic_to_save" name="fdata[profile_pic]">

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
                                                                <input type="text" class="form-control" id="position" value="<?php echo $userinfo->email; ?>" placeholder="Designation"  name="fdata[email]"   aria-required="true" aria-invalid="true" disabled="disabled">
                                                            </div>
                                                        </div>
                                                        <!--                                                        <div class="form-group">
                                                                                                                    <label for="name" class="col-sm-3 control-label">Zip Code</label>
                                                                                                                    <div class="col-sm-9">
                                                                                                                        <input type="text" class="form-control " id="position" value="<?php echo $userinfo->zipcode; ?>" placeholder="Designation" name="fdata[zipcode]"  aria-required="true" aria-invalid="true">
                                                                                                                    </div>
                                                                                                                </div>-->
                                                        <br>
                                                        <div class="row">
                                                            <div class="col-sm-3">
                                                                <!--<p>I hereby certify that the information above is true and accurate. </p>-->
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <button class="btn btn-success" type="submit">Submit</button>
                                                                <!--<button class="btn btn-default"><i class="pg-close"></i> Clear</button>-->
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
                <span class="hint-text">Copyright @ 3Embed software technologies, All rights reserved</span>

            </p>

            <div class="clearfix"></div>
        </div>
    </div>
<!--            <p class="small no-margin pull-right sm-pull-reset">
                <a href="#">Hand-crafted</a> 
                <span class="hint-text">&amp; Made with Love ®</span>
            </p>-->
            <div class="clearfix"></div>

   
    <!-- END FOOTER -->
</div>