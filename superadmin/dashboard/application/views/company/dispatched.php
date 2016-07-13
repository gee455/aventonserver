<?php
date_default_timezone_set('UTC');
$rupee = "$";
?>


<style>
    #companyid{
        display: none;
    }
</style>

<script>
    $(document).ready(function () {
        $("#define_page").html("Dispatchers");
        $('.dispatches').addClass('active');
        $('.dispatches').attr('src', "<?php echo base_url(); ?>/theme/icon/dispatcher_on.png");
//        $('.dispatches_thumb').addClass("bg-success");


        var table = $('#tableWithSearch1');
        var settings = {
            "autoWidth": false,
            "sDom": "<'table-responsive't><'row'<p i>>",
            "sPaginationType": "bootstrap",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },
            "iDisplayLength": 20,
            "order": [[0, "desc"]]
        };
        table.dataTable(settings);

        $('#search-table1').keyup(function () {
            table.fnFilter($(this).val());
        });


        $('#btnStickUpSizeToggler').click(function () {

            var size = $('input[name=stickup_toggler]:checked').val()
            var modalElem = $('#myModal');
            if (size == "mini") {
                $('#modalStickUpSmall').modal('show')
            } else {
                $('#myModal').modal('show')
                if (size == "default") {
                    modalElem.children('.modal-dialog').removeClass('modal-lg');
                } else if (size == "full") {
                    modalElem.children('.modal-dialog').addClass('modal-lg');
                }
            }
        });

        $('#reset_password').click(function () {
            
            $("#display-data").text("");

            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();


            if (val.length == 0) {
                //         alert("please select any one dispatcher");
                $("#display-data").text(<?php echo json_encode(POPUP_DISPATCHER_EDIT); ?>);

            } else if (val.length > 1)
            {

                //     alert("please select only one to edit");
                $("#display-data").text(<?php echo json_encode(POPUP_DISPATCHER_ONLYEDIT); ?>);
            }
            else
            {
                var size = $('input[name=stickup_toggler]:checked').val();
                
                var modalElem = $('#myModal1');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#myModal1').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }

            }
        });


        $("#editpass").click(function () {
            $("errorpass").text("");
            
            var newpass = $("#newpass").val();
            var confirmpass = $("#confirmpass").val();
            var reg = /^\S*(?=\S*[a-zA-Z])(?=\S*[0-9])\S*$/;    //^[-]?(?:[.]\d+|\d+(?:[.]\d*)?)$/;



            if (newpass == "" || newpass == null)
            {
//                alert("please enter the new password");
                $("#errorpass").text(<?php echo json_encode(POPUP_PASSENGERS_PASSNEW); ?>);
            }
            else if (!reg.test(newpass))
            {
//                alert("please enter the password with atleast one chareacter and one letter");
                $("#errorpass").text(<?php echo json_encode(POPUP_PASSENGERS_PASSVALID); ?>);
            }
            else if (confirmpass == "" || confirmpass == null)
            {
//                alert("please confirm the password");
                $("#errorpass").text(<?php echo json_encode(POPUP_PASSENGERS_PASSCONFIRM); ?>);
            }
            else if (confirmpass != newpass)
            {
//                alert("please confirm the same password");
                $("#errorpass").text(<?php echo json_encode(POPUP_PASSENGERS_SAMEPASSCONFIRM); ?>);
            }
            else
            {

                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/editpass",
                    type: 'POST',
                    data: {
                        newpass: newpass,
                        val: $('.checkbox:checked').val()
                    },
                    dataType: 'JSON',
                    success: function (response)
                    {

                        $(".close").trigger('click');

                    }

                });
            }

        });


        $("#inactive").click(function () {

            $("#display-data").text("");

            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).
                    get();
            if (val.length > 0) {

                //  if (confirm("Are you sure to inactive " + val.length + " dispatchers"))

                var size = $('input[name=stickup_toggler]:checked').val();
               
                var modalElem = $('#confirmmodel');
                if (size == "mini")
                {
                    $('#modalStickUpSmall').modal('show')
                }
                else
                {
                    $('#confirmmodel').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    }
                    else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
                $("#errorboxdata").text(<?php echo json_encode(POPUP_DISPATCHER_SUREINACTIVE); ?>);

                $("#confirmed").click(function () {


                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/inactivedispatchers",
                        type: "POST",
                        data: {val: val},
                        dataType: 'json',
                        success: function (result)
                        {

                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                            });
                            $(".close").trigger('click');
                        }
                    });
                });

            }
            else
            {
                //   alert("select atleast one dispatcher");
                $("#display-data").text(<?php echo json_encode(POPUP_DISPATCHER_INACTIVE); ?>);
            }

        });


        $("#active").click(function () {
            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).
                    get();
            if (val.length > 0) {

                // if (confirm("Are you sure to active " + val.length + " dispatchers"))
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
                $("#errorboxdatas").text(<?php echo json_encode(POPUP_DISPATCHER_SUREACTIVE); ?>);

                $("#confirmeds").click(function () {

                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/activedispatchers",
                        type: "POST",
                        data: {val: val},
                        dataType: 'json',
                        success: function (result)
                        {

                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                            });

                            $(".close").trigger('click');
                        }
                    });


                });
            }
            else
            {
                //    alert("select atleast one dispatcher");
                $("#display-data").text(<?php echo json_encode(POPUP_DISPATCHER_INACTIVE); ?>);
            }

        });
        
         $("#delete").click(function () {
            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();
            if (val.length > 0) {

                // if (confirm("Are you sure to active " + val.length + " dispatchers"))
                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#conformDelete');
                if (size == "mini")
                {
                    $('#modalStickUpSmall').modal('show')
                }
                else
                {
                    $('#conformDelete').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    }
                    else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
                $("#errorboxdatas").text(<?php echo json_encode(POPUP_DISPATCHER_SUREACTIVE); ?>);

                $("#con_delete").click(function () {

                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/deletedispatchers",
                        type: "POST",
                        data: {val: val},
                        dataType: 'json',
                        success: function (result)
                        {

                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                            });

                            $(".close").trigger('click');
                        }
                    });


                });
            }
            else
            {
                //    alert("select atleast one dispatcher");
                $("#display-data").text(<?php echo json_encode(POPUP_DISPATCHER_INACTIVE); ?>);
            }

        });



        $("#btnStickUpSizeToggl").click(function () {
            $("#display-data").text("");

            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();


            if (val.length == 0) {
                //       alert("please select any one dispatcher");
                $("#display-data").text(<?php echo json_encode(POPUP_DISPATCHER_EDIT); ?>);

            } else if (val.length > 1) {

                //   alert("please select only one to edit");
                $("#display-data").text(<?php echo json_encode(POPUP_DISPATCHER_ONLYEDIT); ?>);

            }
            else {


                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#myModal2');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#myModal2').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
            }
        });



        $("#insert").click(function () {
        
        

            $("#error_data").text("");

            //var coun = $("#countryid").val();
            var city = $("#cityid").val();
            var name = $("#name").val();
            var email = $("#email").val();
            var password = $("#password").val();
            var emails = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
            var text = /^[a-zA-Z ]*$/;
            var pass = /^\S*(?=\S*[a-zA-Z])(?=\S*[0-9])\S*$/;    //^[-]?(?:[.]\d+|\d+(?:[.]\d*)?)$/;

            if (city == "0") {
//                alert("please select city");
                $("#error_data").text(<?php echo json_encode(POPUP_DISPATCHERS_CITY); ?>);
            }
            else if (name == "" || name == null)
            {
//                alert("please enter the name");
                $("#error_data").text(<?php echo json_encode(POPUP_DISPATCHERS_NAME); ?>);
            }
            else if (!text.test(name))
            {
//                alert("please enter name as text");
                $("#error_data").text(<?php echo json_encode(POPUP_DISPATCHERS_NAMETEXT); ?>);
            }
            else if (email == "" || email == null)
            {
//                alert("please enter the email ");
                $("#error_data").text(<?php echo json_encode(POPUP_DISPATCHERS_EMAIL); ?>);
            }
            else if (!emails.test(email))
            {
//                alert("please enter valid email");
                $("#error_data").text(<?php echo json_encode(POPUP_DISPATCHERS_VALIDEMAIL); ?>);
            }
            else if ($("#email").attr('data') == 1)
            {
//                alert("please enter valid email");
                $("#error_data").text(<?php echo json_encode(POPUP_DRIVER_DRIVER_ALLOCATED); ?>);
            }
            else if (password == "" || password == null)
            {
//                alert("please enter the password ");
                $("#error_data").text(<?php echo json_encode(POPUP_DISPATCHERS_PASSWORD); ?>);
            }
            else if (!pass.test(password))
            {
                //  alert("please enter the password with atleast one chareacter and one letter");
                $("#error_data").text(<?php echo json_encode(POPUP_PASSENGERS_PASSVALID); ?>);
            }
            else
            {



                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/insertdispatches",
                    type: 'POST',
                    data: {
                        name: name,
                        city: city,
                        email: email,
                        password: password
                    },
                    dataType: 'JSON',
                    success: function (response)
                    {
                        $(".close").trigger("click");

                        var size = $('input[name=stickup_toggler]:checked').val();
                        var modalElem = $('#confirmmodel');
                        if (size == "mini")
                        {
                            $('#modalStickUpSmall').modal('show')
                        }
                        else
                        {
                            $('#confirmmodel').modal('show')
                            if (size == "default") {
                                modalElem.children('.modal-dialog').removeClass('modal-lg');
                            }
                            else if (size == "full") {
                                modalElem.children('.modal-dialog').addClass('modal-lg');
                            }
                        }

                        if (response.msg == 0) {
                            $("#errorboxdata").text(<?php echo json_encode(POPUP_DISPATCHER_SUCCESS); ?>);
                            $("#confirmed").hide();
                            $("#cityid").val("0");
                            $("#name").val("");
                            $("#email").val("");
                            $("#password").val("");

                        }
                        else if (response.msg == 1) {
                            $("#errorboxdata").text(<?php echo json_encode(POPUP_DISPATCHER_NOTSUCCESS); ?>);
                            $("#confirmed").hide();
                            $("#cityid").val("0");
                            $("#name").val("");
                            $("#email").val("");
                            $("#password").val("");
                        }

                        var table = $('#big_table');
                        
                        var settings = {
                            "autoWidth": false,
                            "sDom": "<'table-responsive't><'row'<p i>>",
                            "destroy": true,
                            "scrollCollapse": true,
                            "iDisplayLength": 20,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/datatable_dispatcher/1',
                            "bJQueryUI": true,
                            "sPaginationType": "full_numbers",
                            "iDisplayStart ": 20,
                            "oLanguage": {
                                "sProcessing": "<img src='http://107.170.66.211/roadyo_live/sadmin/theme/assets/img/ajax-loader_dark.gif'>"
                            },
                            "fnInitComplete": function () {
                                //oTable.fnAdjustColumnSizing();
                                $('#big_table_processing').hide();
                            },
                            'fnServerData': function (sSource, aoData, fnCallback)
                            {
                                $.ajax
                                        ({
                                            'dataType': 'json',
                                            'type': 'POST',
                                            'url': sSource,
                                            'data': aoData,
                                            'success': fnCallback
                                        });
                            }
                        };




                        table.dataTable(settings);

                        // search box for table
                        $('#search-table').keyup(function () {
                            table.fnFilter($(this).val());
                        });



                    }

                });
            }

        });
        $('#editcity').click(function () {
            $("#errormsg").text("");
            var city = $("#cityval").val();
            var val = $('.checkbox:checked').val();


            if (city == 0) {
                //      alert("please select the city");
                $("#errormsg").text(<?php echo json_encode(POPUP_DISPATCHERS_CITY); ?>);

            }
            else {
                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/editdispatchers",
                    type: "POST",
                    data: {cityval: city, val: val},
                    dataType: 'json',
                    success: function (result)
                    {
                        $(".close").trigger("click");
                        var table = $('#big_table');

                        var settings = {
                            "autoWidth": false,
                            "sDom": "<'table-responsive't><'row'<p i>>",
                            "destroy": true,
                            "scrollCollapse": true,
                            "iDisplayLength": 20,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/datatable_dispatcher/1',
                            "bJQueryUI": true,
                            "sPaginationType": "full_numbers",
                            "iDisplayStart ": 20,
                            "oLanguage": {
                                "sProcessing": "<img src='http://107.170.66.211/roadyo_live/sadmin/theme/assets/img/ajax-loader_dark.gif'>"
                            },
                            "fnInitComplete": function () {
                                //oTable.fnAdjustColumnSizing();
                                $('#big_table_processing').hide();
                            },
                            'fnServerData': function (sSource, aoData, fnCallback)
                            {
                                $.ajax
                                        ({
                                            'dataType': 'json',
                                            'type': 'POST',
                                            'url': sSource,
                                            'data': aoData,
                                            'success': fnCallback
                                        });
                            }
                        };




                        table.dataTable(settings);

                        // search box for table
                        $('#search-table').keyup(function () {
                            table.fnFilter($(this).val());
                        });


                    }
                });
            }

        });


    });

</script>


<script type="text/javascript">
    $(document).ready(function () {


//        alert('<?php // echo $status;        ?>');
        var status = '<?php echo $status; ?>';

        $('#big_table_processing').show();

        if (status == 1) {
            $('#btnStickUpSizeToggler').show();
            $('#btnStickUpSizeToggl').show();
            $('#inactive').show();
            $('#active').hide();
            $("#reset_password").show();
        }

        var table = $('#big_table');

        var settings = {
            "autoWidth": false,
            "sDom": "<'table-responsive't><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "iDisplayLength": 20,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/datatable_dispatcher/' + status,
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "iDisplayStart ": 20,
            "oLanguage": {
                "sProcessing": "<img src='http://107.170.66.211/roadyo_live/sadmin/theme/assets/img/ajax-loader_dark.gif'>"
            },
            "fnInitComplete": function () {
                //oTable.fnAdjustColumnSizing();
                $('#big_table_processing').hide();
            },
            'fnServerData': function (sSource, aoData, fnCallback)
            {
                $.ajax
                        ({
                            'dataType': 'json',
                            'type': 'POST',
                            'url': sSource,
                            'data': aoData,
                            'success': fnCallback
                        });
            }
        };




        table.dataTable(settings);

        // search box for table
        $('#search-table').keyup(function () {
            table.fnFilter($(this).val());
        });


        $('.whenclicked li').click(function () {
            // alert($(this).attr('id'));\
            if ($(this).attr('id') == 1) {

                $('#btnStickUpSizeToggler').show();
                $('#btnStickUpSizeToggl').show();
                $('#inactive').show();
                $('#active').hide();
                $("#reset_password").show();

            }

            else if ($(this).attr('id') == 2) {
                $('#btnStickUpSizeToggler').hide();
                $('#btnStickUpSizeToggl').show();
                $('#inactive').hide();
                $('#active').show();
                $("#reset_password").show();


            }
        });

        $('.changeMode').click(function () {

            var table = $('#big_table');
            $('#big_table_processing').show();

            var settings = {
                "autoWidth": false,
                "sDom": "<'table-responsive't><'row'<p i>>",
                "destroy": true,
                "scrollCollapse": true,
                "iDisplayLength": 20,
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": $(this).attr('data'),
                "bJQueryUI": true,
                "sPaginationType": "full_numbers",
                "iDisplayStart ": 20,
                "oLanguage": {
                    "sProcessing": "<img src='http://107.170.66.211/roadyo_live/sadmin/theme/assets/img/ajax-loader_dark.gif'>"
                },
                "fnInitComplete": function () {
                    //oTable.fnAdjustColumnSizing();
                    $('#big_table_processing').hide();

                },
                'fnServerData': function (sSource, aoData, fnCallback)
                {
                    $.ajax
                            ({
                                'dataType': 'json',
                                'type': 'POST',
                                'url': sSource,
                                'data': aoData,
                                'success': fnCallback
                            });
                }
            };

            $('.tabs_active').removeClass('active');

            $(this).parent().addClass('active');



            table.dataTable(settings);

            // search box for table
            $('#search-table').keyup(function () {
                table.fnFilter($(this).val());
            });

        });

    });





    function refreshTableOnActualcitychagne() {

        var table = $('#big_table');

        $('#big_table_processing').show();
        var settings = {
            "autoWidth": false,
            "sDom": "<'table-responsive't><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "iDisplayLength": 20,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $(".whenclicked li.active").children('a').attr('data'),
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "iDisplayStart ": 20,
            "oLanguage": {
                "sProcessing": "<img src='http://107.170.66.211/roadyo_live/sadmin/theme/assets/img/ajax-loader_dark.gif'>"
            },
            "fnInitComplete": function () {
                //oTable.fnAdjustColumnSizing();

                $('#big_table_processing').hide();

            },
            'fnServerData': function (sSource, aoData, fnCallback)
            {
                $.ajax
                        ({
                            'dataType': 'json',
                            'type': 'POST',
                            'url': sSource,
                            'data': aoData,
                            'success': fnCallback
                        });
            }
        };

        table.dataTable(settings);

        // search box for table
        $('#search-table').keyup(function () {

            table.fnFilter($(this).val());
        });
    }

    function ValidateFromDb() {

        $.ajax({
            url: "<?php echo base_url() ?>index.php/superadmin/validatedispatchEmail",
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








</script>

<style>
    .exportOptions{
        display: none;
    }
</style>
<div class="page-content-wrapper"style="padding-top: 20px">
    <!-- START PAGE CONTENT -->
    <div class="content"style="padding-top: 60PX">

        <div class="brand inline" style="  width: auto;
             font-size: 20px;
              color:#0090d9;
             margin-left: 30px;padding-top: 20px;">
           <!--                    <img src="--><?php //echo base_url();    ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();    ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();    ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

            <strong>DISPATCHERS</strong><!-- id="define_page"-->
        </div>
        <!-- START JUMBOTRON -->
        <div class="jumbotron" data-pages="parallax">
            <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">
                <!--            <div class="inner">-->
                <!--                <!-- START BREADCRUMB -->
                <!--                <ul class="breadcrumb">-->
                <!--                    <li>-->
                <!--                        <p>Company</p>-->
                <!--                    </li>-->
                <!--                    <li><a>Vehicles</a>-->
                <!--                    </li>-->
                <!--                    <li><a href="#" class="active">--><?php //echo $vehicle_status;    ?><!--</a>-->
                <!--                    </li>-->
                <!--                </ul>-->
                <!--                <!-- END BREADCRUMB -->
                <!--            </div>-->






                <div class="panel panel-transparent ">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-fillup  bg-white whenclicked">
                        <li id= "1" class="tabs_active <?php echo ($status == 1 ? "active" : ""); ?>" style="cursor:pointer">
                            <a class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_dispatcher/1"><span><?php echo LIST_ACTIVE; ?></span></a>
                        </li>
                        <li id= "2" class="tabs_active <?php echo ($status == 2 ? "active" : ""); ?>" style="cursor:pointer">
                            <a  class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_dispatcher/2"><span><?php echo LIST_INACTIVE; ?> </span></a>
                        </li>

                        <div class="pull-right m-t-10" > <button class="btn btn-primary btn-cons" id="reset_password" ><?php echo BUTTON_RESETPASSWORD; ?></button></div>




                        <div class="pull-right m-t-10" > <button class="btn btn-primary btn-cons" id="inactive" ><?php echo BUTTON_INACTIVE; ?></button></div>


                        <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="active"><?php echo BUTTON_ACTIVE; ?></button></a></div>
                        <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="delete"><?php echo BUTTON_DELETE; ?></button></a></div>

                        <div class="pull-right m-t-10"> <button class="btn btn-primary btn-cons" id="btnStickUpSizeToggl"><?php echo BUTTON_EDIT; ?></button></a></div>


                        <div class="pull-right m-t-10" > <button class="btn btn-primary btn-cons" id="btnStickUpSizeToggler" ><?php echo BUTTON_ADD; ?></button></div>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="container-fluid container-fixed-lg bg-white">
                            <!-- START PANEL -->
                            <div class="panel panel-transparent">

                                <div class="error-box" id="display-data" style="text-align:center"></div>
                                <div id="big_table_processing" class="dataTables_processing" style=""><img src="http://www.ahmed-samy.com/demos/datatables_2/assets/images/ajax-loader_dark.gif"></div>


                                <div class="searchbtn row clearfix pull-right" >

                                    <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="<?php echo SEARCH; ?>"> </div>
                                </div>
                                <div class="dltbtn">

                                    <!--                                    <div class="pull-right"> <a href="--><?php //echo base_url()    ?><!--index.php/superadmin/callExel/--><?php //echo $stdate;    ?><!--/--><?php //echo $enddate    ?><!--"> <button class="btn btn-primary" type="submit">Export</button></a></div>-->
                                    <?php if ($status == '5') { ?>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success" id="chekdel"><i class="fa fa-trash-o"></i>
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>




                            </div>
                            <div class="panel-body">
                                <?php echo $this->table->generate(); ?>
<!--                                <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch1" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">
                                            <thead>

                                                <tr role="row">
                                                    <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 87px;font-size:15px"><?php echo DISPATCHERS_TABLE_DISPATCHERID; ?></th>
                                                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 150px;font-size:15px"><?php echo DISPATCHERS_TABLE_CITY; ?></th>
                                                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 150px;font-size:15px"><?php echo DISPATCHERS_TABLE_EMAIL; ?></th>
                                                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 131px;font-size:15px"><?php echo DISPATCHERS_TABLE_DISPATCHERNAME; ?></th>
                                                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 98px;font-size:15px"><?php echo DISPATCHERS_TABLE_NOOFBOOKINGS; ?></th>

                                                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:75px;font-size:15px"><?php echo DISPATCHERS_TABLE_OPTION; ?></th> 

                                                </tr>


                                            </thead>
                                            <tbody>












                                <?php
                                $i = '1';
                                foreach ($getdata as $result) {
                                    ?>


                                                            <tr role="row"  class="gradeA odd">
                                                                <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result->dis_id; ?></p></td>
                                                                
                                                                <td id = "d_no" class="v-align-middle sorting_1"> <p><?php
                                    foreach ($city as $val) {
                                        if ($result->city == $val->City_Id)
                                            echo $val->City_Name;
                                    }
                                    ?></p></td>
                                                             
                                                                <td class="v-align-middle"><?php echo $result->dis_email; ?></td>
                                                                <td class="v-align-middle"><?php echo $result->dis_name; ?></td>
                                                                <td class="v-align-middle"><?php echo 0; ?></td>
                                                              
                                                                  <td class="v-align-middle">
                                                                        <div class="checkbox check-primary">
                                                                            <input type="checkbox" value="<?php echo $result->dis_id; ?>" id="checkbox<?php echo $i; ?>" class="checkbox">
                                                                            <label for="checkbox<?php echo $i; ?>">Mark</label>
                                                                        </div>
                                                                    </td>
                                                                
                                                            </tr>
                                    <?php
                                    $i++;
                                }
//                                            
                                ?>
                                            </tbody>
                                        </table></div><div class="row"></div></div>-->
                            </div>
                        </div>
                        <!-- END PANEL -->
                    </div>
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
<!-- END FOOTER -->




<div class="modal fade stick-up" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">

                <div class="modal-header">

                    <div class=" clearfix text-left">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                        </button>

                    </div>
                    <h3> <?php echo LIST_ADD_DISPATCHER; ?></h3>
                </div>

                <br>
                <br>

                <div class="modal-body">
                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label" ><?php echo FIELD_SELECTCITY; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">

                            <select id="cityid" name="city_select"  class="form-control" >
                                <option value="0">Select city</option>
                                <?php
                                foreach ($city as $result) {

                                    echo "<option value=" . $result->City_Id . ">" . $result->City_Name . "</option>";
                                }
                                ?>

                            </select>
                        </div>
                    </div>

                    <br>
                    <br>


                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label">  <?php echo FIELD_DISPATCHERS_NAME; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="name" name="name"  class="form-control" placeholder="">
                        </div>
                    </div>

                    <br>
                    <br>

                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_DISPATCHERS_EMAIL; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="email" name="email" class="form-control" data="1" onblur="ValidateFromDb()">
                        </div>
                    </div>

                    <br>
                    <br>


                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label">  <?php echo FIELD_DISPATCHERS_PASSWORD; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="password"  id="password" name="password"  class="form-control" placeholder="">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4" ></div>
                        <div class="col-sm-4 error-box" id="error_data"></div>
                        <div class="col-sm-4" >
                            <button type="button" class="btn btn-primary pull-right" id="insert" ><?php echo BUTTON_ADD; ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
    </button>
</div>

<div class="modal fade stick-up" id="myModal1" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">

                <div class="modal-header">

                    <div class=" clearfix text-left">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                        </button>

                    </div>
                    <h3> <?php echo LIST_RESETPASSWORD_HEAD; ?></h3>
                </div>


                <br>
                <br>

                <div class="modal-body">
                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label"> <?php echo FIELD_NEWPASSWORD; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="newpass" name="newpass"  class="form-control" placeholder="eg:g3Ehadd">
                        </div>
                    </div>

                    <br>
                    <br>

                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_CONFIRMPASWORD; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="confirmpass" name="confirmpass" class="form-control" placeholder="H3dgsk">
                        </div>
                    </div>
                    
                    


                    <div class="row">
                        <div class="col-sm-4" ></div>
                        <div class="col-sm-4 error-box" id="errorpass"></div>
                        <div class="col-sm-4" >
                            <button type="button" class="btn btn-primary pull-right" id="editpass"><?php echo BUTTON_SUBMIT; ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
    </button>
</div>


<div class="modal fade stick-up" id="myModal2" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">

                <div class="modal-header">

                    <div class=" clearfix text-left">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                        </button>

                    </div>
                    <h3> Edit city</h3>
                </div>


                <br>
                <br>

                <div class="modal-body">




                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label" ><?php echo FIELD_SELECTCITY; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">

                            <select id="cityval" name="city_select"  class="form-control" >
                                <option value="0">Select city</option>
                                <?php
                                foreach ($city as $result) {

                                    echo "<option value=" . $result->City_Id . ">" . $result->City_Name . "</option>";
                                }
                                ?>

                            </select>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-4" ></div>
                        <div class="col-sm-4 error-box" id="errormsg"></div>
                        <div class="col-sm-4" >
                            <button type="button" class="btn btn-primary pull-right" id="editcity" ><?php echo BUTTON_SAVE;?></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
    </button>
</div>

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


<div class="modal fade stick-up" id="conformDelete" tabindex="-1" role="dialog" aria-hidden="true">
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

                    <div class="error-box" id="show_deleteMsg" style="font-size: large;text-align:center"><?php echo DISPTACH_DELETE; ?></div>

                </div>
            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4" ></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4" >
                        <button type="button" class="btn btn-primary pull-right" id="con_delete" ><?php echo BUTTON_YES; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


