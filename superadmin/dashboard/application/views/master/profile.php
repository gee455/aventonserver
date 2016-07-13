<?php  error_reporting(0)?>


<?php 
        foreach($data['userinfo'] as $row){
            
            
            if($row->doctype == 1){
                 $licencenumber = $row->url;
            $expiredate = $row->expirydate;
            }
            else if($row->doctype == 2){
                 $bankbook = $row->url;
            }
        }


?>





<script>
    $(document).ready(function(){
       $('#upload1').click(function(){

           $('#positionone').trigger('click');


       });

        $('#positionone').change(function(){
            $('#path').html($('#positionone').val());

            var formElement = $('#positionone').prop('files')[0];              //document.getElementById("files_upload_form");
            var form_data = new FormData();

            form_data.append('myfile', formElement);
            form_data.append('uploadType', 'license');
            form_data.append('type', 'master');


            $.ajax({
                url: "<?php echo base_url()?>application/views/master/upload_images_on_local.php",
                type: "POST",
                data: form_data,
                dataType: "JSON",
                mimeType:"multipart/form-data",
                async: false,
                success: function (result) {

                    $('#license_pic').val(result.fileName);



                },
                cache: false,
                contentType: false,
                processData: false
            });




        });

        $('#upload2').click(function(){

            $('#positiontwo').trigger('click');
        });


        $('#profile_img_upload_click').click(function(){

            $('#poponclick').trigger('click');

        });


        $('#poponclick').change(function(){


            var DivId = 0;


            var formElement = $('#poponclick').prop('files')[0];              //document.getElementById("files_upload_form");
            var form_data = new FormData();

            form_data.append('myfile', formElement);
            form_data.append('uploadType', 'profile');
            form_data.append('type', 'master');


            $.ajax({
                url: "<?php echo base_url()?>application/views/master/upload_images_on_local.php",
                type: "POST",
                data: form_data,
                dataType: "JSON",
                mimeType:"multipart/form-data",
                async: false,
                success: function (result) {

                    $('#profile_pic_to_save').val(result.fileName);
                    $('#uimg,#nav_user_img').attr('src','<?php echo base_url()?>../../pics/xxhdpi/'+result.fileName);


                },
                cache: false,
                contentType: false,
                processData: false
            });

        });

    });

</script>

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
                <form enctype="multipart/form-data" id="uploadFile">
                    <input type="file" style="display: none" name="myfile_upload" id="poponclick">
                </form>

                <div class="panel panel-transparent">

                    <div class="panel-body">
                        <div class="col-md-1 sm-no-padding"></div>
                        <div class="col-md-10 sm-no-padding">

                            <div class="panel panel-transparent">
                                <div class="panel-body no-padding">
                                    <div id="portlet-advance" class="panel panel-default">
                                        <div class="panel-heading ">


                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-4">


                                                    

                                                        <center>
                                                            <div class="col_2">
                                                                <div class="img" id="prof_img">
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
                                                    <form id="form-work" class="form-horizontal" method="post" role="form" autocomplete="off" novalidate="novalidate"  enctype="multipart/form-data" action="udpadedataProfile">
                                                        <div class="form-group">
                                                            <label for="fname" class="col-sm-3 control-label">First Name</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control" id="fname" placeholder="Full name"  value="<?php echo $userinfo->first_name; ?>" name="fdata[first_name]" required="" aria-required="true">
                                                            </div>
                                                        </div>
                                                        <input type="hidden" id="profile_pic_to_save" name="fdata[profile_pic]" value="<?php echo $userinfo->profile_pic; ?>">

                                                        <div class="form-group">
                                                            <label for="fname" class="col-sm-3 control-label">Last Name</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control" id="fname" placeholder="Last name" value="<?php echo $userinfo->last_name; ?>" name="fdata[last_name]" required="" aria-required="true">
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="position" class="col-sm-3 control-label">Mobile</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control " id="position" value="<?php echo $userinfo->mobile; ?>" placeholder="Mobile" name="fdata[mobile]"  aria-required="true" aria-invalid="true">
                                                                <!--<label id="position-error" class="error" for="position">This field is required.</label>-->
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="col-sm-3 control-label">Email</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control" id="position" value="<?php echo $userinfo->email; ?>" placeholder="Email"  name="fdata[email]"   aria-required="true" aria-invalid="true" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="col-sm-3 control-label">Zip Code</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control " id="position" value="<?php echo $userinfo->zipcode; ?>" placeholder="Zipcode" name="fdata[zipcode]"  aria-required="true" aria-invalid="true" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="col-sm-3 control-label">COMPANY</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control " id="position" value="<?php echo $userinfo->companyname; ?>" placeholder="Designation" name="fdata[companyname]"  aria-required="true" aria-invalid="true" disabled>
                                                            </div>
                                                        </div>
<!--                                                        <div class="form-group">
                                                            <label for="name" class="col-sm-3 control-label">LICENSE NUMBER</label>
                                                            <div class="col-sm-9">
                                                               
                                                                <input type="text" class="form-control " id="position" value="<?php echo $userinfo->license_num; ?>" placeholder="License number" name="fdata[license_num]"  aria-required="true" aria-invalid="true" disabled>
                                                            </div>
                                                        </div>-->

                                                        <div class="form-group">
                                                            <label for="name" class="col-sm-3 control-label">Upload Driving License</label>
                                                            <div class="col-sm-4">
                                                                <button class="btn btn-success btn-cons m-b-10" type="button" id="upload1"><i class="fa fa-cloud-upload"></i> <span class="bold">Upload</span>
                                                                </button>

                                                                <input type="hidden" id="license_pic" value="<?php echo $userinfo->license_pic;?>" name="fdata[license_pic]">
                                                                <?php if($userinfo->license_pic) {?>
                                                                <a href="<?php echo base_url().'../../pics/'.$userinfo->license_pic;?>" target="_blank"><button class="btn btn-primary btn-cons m-b-10" type="button"><i class="pg-form"></i> <span class="bold">View</span>
                                                                </button></a>
                                                                <?php }?>

                                                            </div>
                                                            <div class="col-sm-5" id="path" style="float: right;"></div>

                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="col-sm-3 control-label">Licence Expiration Date</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control " id="position" value="<?php echo $userinfo->expirydate; ?>" placeholder="Designation" name="fdata[expirydate]"  aria-required="true" aria-invalid="true" disabled>
                                                            </div>
                                                        </div>
<!--                                                        <div class="form-group">-->
<!--                                                            <label for="name" class="col-sm-3 control-label">Upload Bank Passbook Copy</label>-->
<!--                                                            <div class="col-sm-4">-->
<!--                                                                <button class="btn btn-success btn-cons m-b-10" type="button" id="upload2"><i class="fa fa-cloud-upload"></i> <span class="bold">Upload</span>-->
<!--                                                                </button>-->
<!--                                                                <input type="file" class="form-control " id="positiontwo" placeholder="Designation" name="passbook"  style="display: none;" aria-required="true" aria-invalid="true">-->
<!--                                                                <button class="btn btn-primary btn-cons m-b-10" type="button"><i class="pg-form"></i> <span class="bold">View</span>-->
<!--                                                                </button>-->
<!--                                                            </div>-->
<!--                                                            <div class="col-sm-5" id="path" style="float: right;"></div>-->
<!--                                                        </div>-->
                                                        <br>
                                                        <div class="row">
                                                            <div class="col-sm-3">
                                                                <!--<p>I hereby certify that the information above is true and accurate. </p>-->
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <button class="btn btn-success" type="submit">Submit</button>
<!--                                                                <button class="btn btn-default"><i class="pg-close"></i> Clear</button>-->
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="val" value="1">
                                                    </form>


                                                    <input type="file" class="form-control " id="positionone" placeholder="Designation" name="userfile"  style="display: none;" aria-required="true" aria-invalid="true">
                                                </div>
                                            </div>
                                        </div>
                                        <img src="pages/img/progress/progress-circle-master.svg" style="display:none"></div>
                                </div>
                            </div>


                        </div>
                        <div class="col-md-1 sm-no-padding"></div>
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