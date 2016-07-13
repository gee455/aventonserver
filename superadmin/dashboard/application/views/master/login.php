<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta charset="utf-8" />
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    

     <link href="<?php echo base_url();?>theme/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>theme/assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>theme/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>theme/assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="<?php echo base_url(); ?>theme/assets/plugins/bootstrap-select2/select2.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="<?php echo base_url(); ?>theme/assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="<?php echo base_url(); ?>theme/pages/css/pages-icons.css" rel="stylesheet" type="text/css">
    <link class="main-stylesheet" href="<?php echo base_url(); ?>theme/pages/css/pages.css" rel="stylesheet" type="text/css" />
        
        <script type="text/javascript">
            window.onload = function()
            {
                // fix for windows 8
                if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
                    document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="pages/css/windows.chrome.fix.css" />'
            }

            function validateForm()
            {
                if (!validateEmail($("#email").val()))
                {
                    $("#errorbox").html("Enter valid email id");
                    return false;
                }

                if (isBlank($("#password").val()))
                {
                    $("#errorbox").html("Enter password");
                    return false;
                }

                return true;

            }

            function VerifyNsendLink()
            {
              var  resetemail = $("#txtforgotpassword").val();
                if (!validateEmail(resetemail))
                {
                    $("#forgoterrorbox").html("Enter valid email id");
                    return false;
                }
                else
                {
                    $("#forgoterrorbox").html("Verifying Email");
                    $.ajax({
                        url: '<?php echo base_url() ?>index.php/masteradmin/ForgotPassword',
                        type: 'POST',
                        data: {"resetemail": resetemail},
                        datatype: "JSON",
                        success: function(data) {
                            $('#forgoterrorbox').html(data);
                        }
                    });
                }

            }

        </script>


    </head>
    <body class="fixed-header   ">
         
        <div class="login-wrapper ">
             
            <div class="bg-pic">
                 <!--START Background Pic-->
                <img src="<?php echo base_url() ?>theme/assets/img/demo/new-york-city-buildings-sunrise-morning-hd-wallpaper.jpg" data-src="<?php echo base_url() ?>theme/assets/img/demo/new-york-city-buildings-sunrise-morning-hd-wallpaper.jpg" data-src-retina="<?php echo base_url() ?>theme/assets/img/demo/new-york-city-buildings-sunrise-morning-hd-wallpaper.jpg" alt="" class="lazy">
       
                <div class="bg-caption pull-bottom sm-pull-bottom text-white p-l-20 m-b-20">
                    <h2 class="semi-bold text-white">
                        <?php echo Appname;?> make it easy to enjoy what matters the most in the life</h2>
                    <p class="small">
                        images Displayed are solely for representation purposes only, All work copyright of respective owner, otherwise © 2013-2014 REVOX.<br>                        

                    </p>
                </div>
                 <!--END Background Caption-->
            </div>
             
            <div class="login-container bg-white">
                <div class="p-l-50 m-l-20 p-r-50 m-r-20 p-t-50 m-t-30 sm-p-l-15 sm-p-r-15 sm-p-t-40">
                    <!--<img src="<?php echo base_url() ?>theme/assets/img/logo.png" alt="logo" data-src="<?php echo base_url() ?>RylandInsurence/images/logo.png" data-src-retina="<?php echo base_url() ?>theme/assets/img/logo.png" width="78" height="22">-->
                    <p class="p-t-35">Sign into your <?php echo Appname;?> account </p>
                     
                    <form id="form-login" class="p-t-15" role="form" action ="<?php echo base_url() ?>index.php/masteradmin/AuthenticateUser" onsubmit= "return validateForm();" method="post">

                         <!--START Form Control-->
                        <div class="form-group form-group-default">
                            <label>Login</label>
                            <div class="controls">
                                
                                <input type="text" id="email" name="email" class="form-control" placeholder="User Name" />
                            </div>
                        </div>
                         
                         <!--START Form Control-->
                        <div class="form-group form-group-default">
                            <label>Password</label>
                            <div class="controls">

                                <input type="password" name="password" id="password" class="form-control" placeholder="Password" />
                            </div>
                        </div>
                        <div class="form-group ">
                            <span id = "errorbox" style=" color: red"><?php if (!is_null($loginerrormsg)) echo $loginerrormsg; ?></span>
                        </div>
<!--                         START Form Control

                         END Form Control-->

                        <div class="row">
                            <div class="col-md-6">


                                <div class="form-group " style="border:none">
                                    <button class="btn btn-primary btn-cons m-t-10" type="submit">Sign in</button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group " style="border:none">
                                    <a id = "forgotpassword" onclick="showOrHideElement('forgotpasswordgroup', 1)" class="text-info small" style="cursor: pointer;">Forgot Password ?</a>
                                </div>

                            </div>


                        </div>
                        <div class="row">
                            <div class="col-md-12">

                                <div id = "forgotpasswordgroup" style="display: none">
                                    <br>
                                    <label> Enter email to reset password</label>
                                    <img src = "<?php echo base_url(); ?>theme/assets/img/uparrow.ico" class ="img-rounded" height ="20px" width ="20px" style="float: right; cursor: pointer" onclick="showOrHideElement('forgotpasswordgroup', 0)"/>

                                    <input type = "text" id ="txtforgotpassword" name = "txtforgotpassword" class = "form-control" />



                                    <label id="forgoterrorbox" style="color: red"></label>
                                    <br>
                                    <button type="button" class="btn bg-olive btn-block" onclick="VerifyNsendLink();">Send</button> 
                                </div>

                            </div>



                        </div>

                    </form>
                </div>
            </div>
             END Login Right Container
        </div>

      
      <script src="<?php echo base_url();?>theme/assets/js/universal.js" type="text/javascript"></script>
           <script src="<?php echo base_url(); ?>theme/assets/plugins/pace/pace.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery/jquery-1.8.3.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>theme/assets/plugins/modernizr.custom.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>theme/assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery-bez/jquery.bez.min.js"></script>
    <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery-actual/jquery.actual.min.js"></script>
    <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>theme/assets/plugins/bootstrap-select2/select2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>theme/assets/plugins/classie/classie.js"></script>
    <script src="<?php echo base_url(); ?>theme/assets/plugins/switchery/js/switchery.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>theme/assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <!-- END VENDOR JS -->
    <!-- BEGIN CORE TEMPLATE JS -->
    <script src="<?php echo base_url(); ?>theme/pages/js/pages.min.js"></script>
    <!-- END CORE TEMPLATE JS -->
    <!-- BEGIN PAGE LEVEL JS -->
    <script src="<?php echo base_url(); ?>theme/assets/js/scripts.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL JS -->
    <script>
    $(function()
    {
      $('#form-login').validate()
    })
    </script>
    </body>
</html>


    