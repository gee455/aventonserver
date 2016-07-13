<?php
date_default_timezone_set('UTC');
$rupee = "$";
//error_reporting(0);


if ($status == 1) {
    $vehicle_status = 'New';
    $new = "active";
} else if ($status == 3 && $db == 'my') {
    $vehicle_status = 'Accepted';
    $accept = "active";
} else if ($status == 3 && $db == 'mo') {
    $vehicle_status = 'Online&Free';
    $free = "active";
} else if ($status == 4) {
    $vehicle_status = 'Rejected';
    $reject = 'active';
}
//else if($status == 2) {
//    $vehicle_status = 'Online&Free';
//    $free = 'active';
//  
//}
else if ($status == 30) {
    $$vehicle_status = 'Offile';
    $offline = 'active';
} else if ($status == 567) {
    $$vehicle_status = 'Booked';
    $booked = 'active';
}
?>
<style>

    .imageborder{
        border-radius: 50%;
    }

</style>

<script>
    $(document).ready(function () {

//        alert = function () {
//        };
        $("#define_page").html("Drivers");

        $('.drivers').addClass('active');
        $('.Drivers').attr('src', "<?php echo base_url(); ?>/theme/icon/drivers_on.png");
//        $('.driver_thumb').addClass("bg-success");

        $("#document_data").click(function () {


            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();
            if (val.length == 0) {
                //         alert("please select any one dispatcher");
                $("#display-data").text(<?php echo json_encode(POPUP_DRIVER_EDIT); ?>);
            } else if (val.length > 1)
            {

                //     alert("please select only one to edit");
                $("#display-data").text(<?php echo json_encode(POPUP_DRIVER_ONLYEDIT); ?>);
            }
            else
            {
                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#myModaldocument');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#myModaldocument').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }

            }

//            $("#errorboxdatas").text(<?php echo json_encode(POPUP_DRIVERS_REJECT); ?>);

//            $("#confirmeds").click(function () {
            $('#doc_body').html('');

            $.ajax({
                url: "<?php echo base_url('index.php/superadmin') ?>/documentgetdata",
                type: "POST",
                data: {val: val},
                dataType: 'json',
                success: function (result)
                {


                    $.each(result, function (index, doc) {

                        var html = "<tr><td>";

                        if (doc.doctype == '1')
                            html += "License</td><td>" + doc.expirydate + "</td>";
                        else
                            html += "Bank Passbook</td><td>-</td>";

                        html += "<td>" + "<a target=__blank href=" + '<?php echo base_url() ?>' + "../../pics/" + doc.url + "><button>view</button></a>\n\
                            <a target=__blank href=" + '<?php echo base_url() ?>' + "../../pics/" + doc.url + " download= " + doc.url + "><button>download</button></a>" + "</td>";

                        html += "</tr>";

                        $('#doc_body').append(html);

                    });
                    $("#documentok").click(function () {
                        $('.close').trigger('click');
                    });

                }
            });
//            });

            $("#editdriver").click(function () {


                $("#display-data").text("");
                var val = $('.checkbox:checked').map(function () {
                    return this.value;
                }).
                        get();
                if (val.length == 0) {
                    //         alert("please select any one dispatcher");
                    $("#display-data").text(<?php echo json_encode(POPUP_DRIVER_EDIT); ?>);
                } else if (val.length > 1)
                {

                    //     alert("please select only one to edit");
                    $("#display-data").text(<?php echo json_encode(POPUP_DRIVER_ONLYEDIT); ?>);
                }
                else
                {

//               window.locaton = "<?php echo base_url() ?>index.php/superadmin/editdriver" + val;
                    window.location = "<?php echo base_url('index.php/superadmin') ?>/editdriver/" + val;
                }
            });


        });




        $("#chekdel").click(function () {

            $("#display-data").text("");
            var val = [];
            $('.checkbox:checked').each(function (i) {
                val[i] = $(this).val();
            });

            if (val.length > 0) {

                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#deletedriver');
                if (size == "mini")
                {
                    $('#modalStickUpSmall').modal('show')
                }
                else
                {
                    $('#deletedriver').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    }
                    else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
                $("#errorbox").text(<?php echo json_encode(POPUP_DRIVERS_DELETE); ?>);

                $("#yesdelete").click(function () {

//            if(confirm("Are you sure to Delete " +val.length + " Drivers")){
                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/deleteDrivers",
                        type: "POST",
                        data: {masterid: val},
                        success: function (result) {

                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                            });

                            $(".close").trigger('click');
                        }
                    });
                });
            }



            else {
//                alert("Please mark any one of options");
                $("#display-data").text(<?php echo json_encode(POPUP_DRIVERS_ATLEAST); ?>);
            }

        });

        $("#accept").click(function () {

            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).
                    get();
            if (val.length > 0) {

                //      if (confirm("Are you sure to inactive " + val.length + " passengers"))
                   
                var size = $('input[name=stickup_toggler]:checked').val()
//                   $.ajax({
//                        url: "<?php echo base_url('index.php/superadmin') ?>/getdrivervehicle",
//                        type: "POST",
//                        data: {masterid: val},
//                        success: function (result) {
//                           alert("hi");
//                           alert(result.flag);
//                           
//                           
//                                          var modalElem = $('#confirmmodel');
//                if (size == "mini")
//                {
//                    $('#modalStickUpSmall').modal('show')
//                }
//                else
//                {
//                    $('#confirmmodel').modal('show')
//                    if (size == "default") {
//                        modalElem.children('.modal-dialog').removeClass('modal-lg');
//                    }
//                    else if (size == "full") {
//                        modalElem.children('.modal-dialog').addClass('modal-lg');
//                    }
//                }
//                
//                $("#ve_compan").text("");
//                           
//                           
//                           
//                        }
//                    });
           
               
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
                
                $("#ve_compan").text("");

                $("#confirmed").click(function () {

                    var val = $('.checkbox:checked').map(function () {
                        return this.value;
                    }).
                            get();


//                      $("#error-box").text("");

                    $("#ve_compan").val('');

                    var company = $("#company_select").val();

                    if (company == "" || company == null || company == "0")
                    {
                        $("#ve_compan").text(<?php echo json_encode(POPUP_ADDCOMPANY_NAME); ?>);
//                                 $("#errorboxdata").text("please select the company");
                    }
                    else {

                        $.ajax({
                            url: "<?php echo base_url('index.php/superadmin') ?>/acceptdrivers",
                            type: "POST",
                            data: {val: val, company_id: company},
                            dataType: 'json',
                            success: function (response)
                            {
                                var size = $('input[name=stickup_toggler]:checked').val()
                                var modalElem = $('#acceptdrivermsg');
                                if (size == "mini")
                                {
                                    $('#modalStickUpSmall').modal('show')
                                }
                                else
                                {
                                    $('#acceptdrivermsg').modal('show')
                                    if (size == "default") {
                                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                                    }
                                    else if (size == "full") {
                                        modalElem.children('.modal-dialog').addClass('modal-lg');
                                    }
                                }
                                $("#errorbox_accept").text(<?php echo json_encode(POPUP_MSG_ACCEPTED); ?>);

                                $("#accepted_msg").click(function(){
                                    $('.close').trigger('click');
                                });


                                $('.checkbox:checked').each(function (i) {
                                    $(this).closest('tr').remove();
                                });

//                            location.reload();
                            }


                        });

                        $(".close").trigger('click');

                        $("#ve_compan").val('');
                        $("#company_select").val('');

                    }
                });
            }
            else
            {
                //      alert("select atleast one passenger");
                $("#display-data").text(<?php echo json_encode(POPUP_DRIVERS_ATLEAST); ?>);

            }

        });



        $('#driverresetpassword').click(function () {
            $("#display-data").text("");

            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();


            if (val.length == 0) {
                //         alert("please select any one dispatcher");
                $("#display-data").text(<?php echo json_encode(POPUP_DRIVER_EDIT); ?>);

            } else if (val.length > 1)
            {

                //     alert("please select only one to edit");
                $("#display-data").text(<?php echo json_encode(POPUP_DRIVER_ONLYEDIT); ?>);
            }
            else
            {
                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#myModal1_driverpass');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#myModal1_driverpass').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }

            }
        });



        $("#editpass_msg").click(function () {
            $("errorpass").text("");
            
            var newpass = $(".newpass").val();
            var confirmpass = $(".confirmpass").val();
            var reg = /^\S*(?=\S*[a-zA-Z])(?=\S*[0-9])\S*$/;    //^[-]?(?:[.]\d+|\d+(?:[.]\d*)?)$/;


            if (newpass == "" || newpass == null)
            {
//                alert("please enter the new password");
                $("#errorpass_driversmsg").text(<?php echo json_encode(POPUP_PASSENGERS_PASSNEW); ?>);
            }
//            else if (!reg.test(newpass))
//            {
////                alert("please enter the password with atleast one chareacter and one letter");
//                $("#errorpass_driversmsg").text(<?php echo json_encode(POPUP_PASSENGERS_PASSVALID); ?>);
//            }
//            else if (confirmpass == "" || confirmpass == null)
//            {
////                alert("please confirm the password");
//                $("#errorpass_driversmsg").text(<?php echo json_encode(POPUP_PASSENGERS_PASSCONFIRM); ?>);
//            }
//            else if (confirmpass != newpass)
//            {
////                alert("please confirm the same password");
//                $("#errorpass_driversmsg").text(<?php echo json_encode(POPUP_PASSENGERS_SAMEPASSCONFIRM); ?>);
//            }
            else
            {
//                alert('here');

                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/editdriverpassword",
                    type: 'POST',
                    data: {
                        newpass: newpass,
                        val: $('.checkbox:checked').val()
                    },
                    dataType: 'JSON',
                    success: function (response)
                    {
                        if (response.flag != 1) {

                            $(".close").trigger('click');

                            var size = $('input[name=stickup_toggler]:checked').val()
                            var modalElem = $('#confirmmodelss');
                            if (size == "mini")
                            {
                                $('#modalStickUpSmall').modal('show')
                            }
                            else
                            {
                                $('#confirmmodelss').modal('show')
                                if (size == "default") {
                                    modalElem.children('.modal-dialog').removeClass('modal-lg');
                                }
                                else if (size == "full") {
                                    modalElem.children('.modal-dialog').addClass('modal-lg');
                                }
                            }

                            $("#errorboxdatass").text(<?php echo json_encode(POPUP_DRIVERS_NEWPASSWORD); ?>);
                            $("#confirmedss").hide();


                            $(".newpass").val('');
                            $(".confirmpass").val('');
                        }
                        else if(response.flag == 1)
                             $("#errorboxdatass").text(<?php echo json_encode(POPUP_DRIVERS_NEWPASSWORD_FAILED); ?>);
                            $("#confirmedss").hide();
//                        location.reload();

                    }

                });
            }

        });





        $("#reject").click(function () {
            $("#display-data").text("");
            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).
                    get();
            if (val.length > 0) {

                //      if (confirm("Are you sure to inactive " + val.length + " passengers"))

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
                $("#errorboxdatas").text(<?php echo json_encode(POPUP_DRIVERS_DEACTIVAT); ?>);

                $("#confirmeds").click(function () {

                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/rejectdrivers",
                        type: "POST",
                        data: {val: val},
                        dataType: 'json',
                        success: function (result)
                        {
                            $('#confirmmodels').modal('hide');

                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                            });

                        }
                    });


                });
            }
            else
            {
                //      alert("select atleast one passenger");
                $("#display-data").text(<?php echo json_encode(POPUP_DRIVERS_ATLEAST); ?>);
            }

        });


        $("#editdriver").click(function () {
            $("#display-data").text("");

            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();


            if (val.length == 0) {
                //         alert("please select any one dispatcher");
                $("#display-data").text(<?php echo json_encode(POPUP_DRIVER_EDIT); ?>);

            } else if (val.length > 1)
            {

                //     alert("please select only one to edit");
                $("#display-data").text(<?php echo json_encode(POPUP_DRIVER_ONLYEDIT); ?>);
            }
            else
            {
                window.location = "<?php echo base_url() ?>index.php/superadmin/editdriver/" + val;

                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/editdriver",
                    type: "POST",
                    data: {val: val},
                    dataType: 'json',
                    success: function (result)
                    {
//                            $('#confirmmodels').modal('hide');
//
//                            $('.checkbox:checked').each(function (i) {
//                                $(this).closest('tr').remove();
//                            });

                    }
                });

            }
        });

        $("#joblogs").click(function () {

            $("#display-data").text("");

            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).get();


            if (val.length == 0) {
                //         alert("please select any one dispatcher");
                $("#display-data").text(<?php echo json_encode(POPUP_DRIVER_EDIT); ?>);

            } else if (val.length > 1)
            {

                //     alert("please select only one to edit");
                $("#display-data").text(<?php echo json_encode(POPUP_DRIVER_ONLYEDIT); ?>);
            }
            else
            {
                window.location = "<?php echo base_url() ?>index.php/superadmin/joblogs/" + val;

            }
        });




    });

</script>


<script type="text/javascript">
    $(document).ready(function () {


        $('#big_table_processing').hide();
//        alert('<?php // echo $status;              ?>');
        var status = '<?php echo $status; ?>';
       

        if (status == 1) {

            $("#display-data").text("");
//            alert('asdf');
            $('#driverresetpassword').show();
            $('#chekdel').show();
            $('#reject').show();
            $('#accept').show();
            $('#add').show();
            $('#selectedcity').hide();
            $('#companyid').hide();

        }
//        else if(status != 1)
//        {
//            alert('hai');
//             $('#selectedcity').show();
//            $('#companyid').show();
//        }


        $('#my1').click(function () {
            $("#display-data").text("");

            $('#driverresetpassword').show();
            $('#chekdel').show();
            $('#reject').show();
            $('#accept').show();
            $('#add').show();
            $('#selectedcity').hide();
            $('#companyid').hide();

        });
        $('#my3').click(function () {
            $("#display-data").text("");

            $('#driverresetpassword').show();
            $('#chekdel').show();
            $('#reject').show();
            $('#accept').hide();
            $('#add').show();
            $('#selectedcity').show();
            $('#companyid').show();

        });

        $('#my4').click(function () {
            $("#display-data").text("");

            $('#driverresetpassword').show();
            $('#chekdel').show();
            $('#reject').hide();
            $('#accept').show();
            $('#add').show();
            $('#selectedcity').show();
            $('#companyid').show();

        });


        $('#mo3').click(function () {

            $("#display-data").text("");

            $('#driverresetpassword').hide();
            $('#chekdel').hide();

            $('#reject').hide();
            $('#accept').hide();

            $('#editdriver').hide();
            $('#joblogs').hide();
            $('#driverresetpassword').hide();
            $('#document_data').hide();

            $('#add').hide();
            $('#selectedcity').show();
            $('#companyid').show();

        });
        $('#mo30').click(function () {
            $("#display-data").text("");

            $('#driverresetpassword').hide();
            $('#chekdel').hide();
            $('#reject').hide();
            $('#accept').hide();
            $('#editdriver').hide();
            $('#joblogs').hide();
            $('#driverresetpassword').hide();
            $('#document_data').hide();
            $('#add').hide();
            $('#selectedcity').show();
            $('#companyid').show();

        });
        $('#mo567').click(function () {
            $("#display-data").text("");

            $('#driverresetpassword').hide();
            $('#chekdel').hide();
            $('#editdriver').hide();
            $('#joblogs').hide();
            $('#driverresetpassword').hide();
            $('#document_data').hide();
            $('#reject').hide();
            $('#accept').hide();
            $('#add').hide();
            $('#selectedcity').show();
            $('#companyid').show();

        });




        var table = $('#big_table');

        var settings = {
            "autoWidth": false,
            "sDom": "<'table-responsive't><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "iDisplayLength": 20,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/datatable_drivers/my/' + status,
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "iDisplayStart ": 20,
            "oLanguage": {
                "sProcessing": "<img src='<?php echo base_url()?>theme/assets/img/ajax-loader_dark.gif'>"
            },
            "fnInitComplete": function () {
                //oTable.fnAdjustColumnSizing();
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

        $('#big_table').on('init.dt', function () {
          

            var urlChunks = $("li.active").find('.changeMode').attr('data').split('/');
            var status = urlChunks[urlChunks.length - 1];
            var forwhat = urlChunks[urlChunks.length - 2];
            if (forwhat == 'my') {
                if (status == 3 || status == 4 || status==1 ) {
                    $('#big_table').dataTable().fnSetColumnVis([], false);
                } else {
                    $('#big_table').dataTable().fnSetColumnVis([6], false);
                }
            }

        });

        $('.changeMode').click(function () {
            $("#display-data").text("");

//            var status = '<?php echo $status; ?>';
//
            if (status == "my1") {
                $('#selectedcity').hide();
                $('#companyid').hide();
            }
            else if (status == "my3") {
                $('#selectedcity').show();
                $('#companyid').show();
            }

                 if ($(this).data('id') == 1) {
               $("#display-data").text("");
//            alert('asdf');
            $('#driverresetpassword').show();
            $('#chekdel').show();
            $('#reject').show();
            $('#accept').show();
            $('#add').show();
            $('#document_data').show();
            $('#joblogs').show();
            $('#editdriver').show();


            }
            else if ($(this).data('id') == 2) {
               $("#display-data").text("");
//            alert('asdf');
            $('#driverresetpassword').show();
            $('#chekdel').show();
            $('#reject').show();
//            $('#accept').show();
//            $('#add').show();
            $('#document_data').show();
            $('#joblogs').show();
            $('#editdriver').show();


            }
            else if ($(this).data('id') == 3) {
               $("#display-data").text("");
//            alert('asdf');
            $('#driverresetpassword').show();
            $('#chekdel').show();
            $('#reject').show();
//            $('#accept').show();
//            $('#add').show();
            $('#document_data').show();
            $('#joblogs').show();
            $('#editdriver').show();


            }



            $('#big_table_processing').toggle();

            var table = $('#big_table');

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
                    "sProcessing": "<img src='<?php echo base_url()?>theme/assets/img/ajax-loader_dark.gif'>"
                },
                "fnInitComplete": function () {
                    //oTable.fnAdjustColumnSizing();
                    $('#big_table_processing').toggle();
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

    function refreshTableOnCityChange() {

        var table = $('#big_table');
        $("#display-data").text("");

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
                "sProcessing": "<img src='<?php echo base_url()?>theme/assets/img/ajax-loader_dark.gif'>"
            },
            "fnInitComplete": function () {
                //oTable.fnAdjustColumnSizing();


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
         alert("here");

            table.fnFilter($(this).val());
        });
    }
</script>


<style>
    .exportOptions{
        display: none;
    }
    .btn-cons {
        margin-right: 5px;
        min-width: 102px;
    }
    .btn{
        font-size: 13px;
    }
</style>
<div class="page-content-wrapper"style="padding-top: 20px">
    <!-- START PAGE CONTENT -->
    <div class="content">


        <div class="brand inline" style="  width: auto;
             font-size: 20px;
             color: gray;
             margin-left: 30px;padding-top: 20px;">
           <!--                    <img src="--><?php //echo base_url();               ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();               ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();               ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

            <strong style="color:#0090d9;">DRIVERS</strong><!-- id="define_page"-->
        </div>
        <!-- START JUMBOTRON -->
        <div class="jumbotron" data-pages="parallax">
            <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">




                <div class="panel panel-transparent ">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-fillup  bg-white whenclicked">
                        <li id= "my1" class="tabs_active <?php echo $new ?>" style="cursor:pointer">
                            <a  class="changeMode New_" data="<?php echo base_url(); ?>index.php/superadmin/datatable_drivers/my/1" data-id="1"><span><?php echo LIST_NEW; ?></span></a>
                        </li>
                        <li id= "my3" class="tabs_active <?php echo $accept ?>" style="cursor:pointer">
                            <a  class="changeMode accepted_" data="<?php echo base_url(); ?>index.php/superadmin/datatable_drivers/my/3" data-id="2"><span><?php echo LIST_ACCEPTED; ?></span></a>
                        </li>
                        <li id= "my4" class="tabs_active <?php echo $reject ?>" style="cursor:pointer">
                            <a  class="changeMode rejected_" data="<?php echo base_url(); ?>index.php/superadmin/datatable_drivers/my/4" data-id="3"><span><?php echo LIST_REJECTED; ?></span></a>
                        </li>


                        <li id= "mo3" class="tabs_active <?php echo $free ?>" style="cursor:pointer">
                            <a class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_drivers/mo/3" data-id="4"><?php echo LIST_FREEONLINE; ?></a>
                        </li>
                        <li id= "mo30" class="tabs_active <?php echo $offline ?>" style="cursor:pointer">
                            <a class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_drivers/mo/30" data-id="5"><?php echo LIST_OFFLINE; ?></a>
                        </li>
                        <li id= "mo567" class="tabs_active <?php echo $booked ?>" style="cursor:pointer">
                            <a class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_drivers/mo/567" data-id="6"><?php echo LIST_BOOKED; ?></a>
                        </li>

                        <!--                            <div class="btn-group">-->

                        <div class=""><button class="btn btn-primary pull-right m-t-10 " id="document_data"  style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_DOCUMENT ?></button></div>
                        <div class=""> <button class="btn btn-primary pull-right m-t-10" id="driverresetpassword" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_RESETPASSWORD; ?></button></a></div>

                        <div class=""> <button class="btn btn-primary pull-right m-t-10" id="chekdel" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_DELETE; ?></button></a></div>
                        <div class=""><button class="btn btn-primary pull-right m-t-10 " id="editdriver" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_EDIT; ?></button></div>

                        <div class="">
<!--                            <a> 
                                <button class="btn btn-primary pull-right m-t-10 " id="joblogs" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_JOBLOGS; ?>
                                </button></a>-->
                        </div>




                        <div class=""> <button class="btn btn-primary pull-right m-t-10" id="reject" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_REJECT; ?></button></a></div>
                        <div class=""> <button class="btn btn-primary pull-right m-t-10" id="accept" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_ACCEPT; ?></button></a></div>

                        <div class=""><a href="<?php echo base_url() ?>index.php/superadmin/addnewdriver"> <button class="btn btn-primary pull-right m-t-10" id="add" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_ADD; ?></button></a></div>


                        <!--                            </div>-->





                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="container-fluid container-fixed-lg bg-white">
                            <!-- START PANEL -->
                            <div class="panel panel-transparent">
                                <div class="panel-heading">

                                    <div class="error-box" id="display-data" style="text-align:center"></div>
                                    <div id="big_table_processing" class="dataTables_processing" style=""><img src="<?php echo base_url()?>theme/assets/img/ajax-loader_dark.gif"></div>

                                    <div class="searchbtn row clearfix pull-right" >
                                        <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="<?php echo SEARCH; ?>"> </div>

                                    </div>




                                </div>
                                &nbsp;
                                <div class="panel-body">

                                    <?php

                                    echo $this->table->generate();
                                    ?>

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

                <div class="form-group">
                    <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_VEHICLE_SELECTCOMPANY; ?><span style="color:red;font-size: 18px">*</span></label>
                    <div class="col-sm-6 error-box" >
                        <select class="form-control" id="company_select">
                            <option value="0">Select Company</option>
                            <?php
                            $this->load->database();
                            $query = $this->db->query('select * from  company_info WHERE  status = 3')->result();
                            foreach ($query as $result) {


                                echo '<option value="' . $result->company_id . '">' . $result->companyname . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                </div>
                <br>

            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-2" ></div>
                    <div class="col-sm-6 " id="ve_compan"></div>
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

<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
</button>






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

<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
</button>



<div class="modal fade stick-up" id="myModal1_driverpass" tabindex="-1" role="dialog" aria-hidden="true">
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
                            <input type="text"  id="newpass" name="latitude"  class="newpass form-control" placeholder="eg:g3Ehadd">
                        </div>
                    </div>

                    <br>
                    <br>

                    <div class="form-group" class="formex">
                        <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_CONFIRMPASWORD; ?><span style="color:red;font-size: 18px">*</span></label>
                        <div class="col-sm-6">
                            <input type="text"  id="confirmpass" name="longitude" class="confirmpass form-control" placeholder="H3dgsk">
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-4" ></div>
                        <div class="col-sm-4 error-box" id="errorpass_driversmsg"></div>
                        <div class="col-sm-4" >
                            <button type="button" class="btn btn-primary pull-right" id="editpass_msg" ><?php echo BUTTON_SUBMIT; ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

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
                        <button type="button" class="btn btn-primary pull-right" id="confirmedss" ><?php echo BUTTON_OK; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<div class="modal fade stick-up" id="deletedriver" tabindex="-1" role="dialog" aria-hidden="true">
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

                    <div class="error-box" id="errorbox" style="font-size: large;text-align:center"><?php echo VEHICLEMODEL_DELETE; ?></div>

                </div>
            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4" ></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4" >
                        <button type="button" class="btn btn-primary pull-right" id="yesdelete" ><?php echo BUTTON_YES; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade stick-up" id="acceptdrivermsg" tabindex="-1" role="dialog" aria-hidden="true">
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

                    <div class="error-box" id="errorbox_accept" style="font-size: large;text-align:center"><?php echo VEHICLEMODEL_DELETE; ?></div>

                </div>
            </div>

            <br>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4" ></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4" >
                        <button type="button" class="btn btn-primary pull-right" id="accepted_msg" ><?php echo BUTTON_OK; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<div class="modal fade stick-up" id="myModaldocument" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">

                <div class="modal-header">

                    <div class=" clearfix text-left">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                        </button>

                    </div>
                    <h3> <?php echo LIST_RESETPASSWORD_DRIVERDOCUMENTS; ?></h3>
                </div>


                <br>
                <br>

                <div class="modal-body">

                    <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" id="big_table" role="grid" aria-describedby="tableWithSearch_info">


                                <thead>

                                    <tr role="row">
                                        <th  rowspan="1" colspan="1" aria-sort="ascending"  style="width: 100PX;font-size: 14px"><?php echo DRIVERS_TABLE_DRIVER_DOCUMENT; ?></th>
                                        <th  rowspan="1" colspan="1" aria-sort="ascending"  style="width: 100PX;font-size: 14px"><?php echo DRIVERS_TABLE_DRIVER_EXPIREDATE; ?></th>
                                        <th  rowspan="1" colspan="1" aria-sort="ascending"  style="width: 100PX;font-size: 14px"><?php echo DRIVERS_TABLE_DRIVER_VIEW; ?></th>

                                    </tr>


                                </thead>
                                <tbody id="doc_body">

                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-sm-4" ></div>
                                <div class="col-sm-4 error-box" id="errorpass"></div>
                                <div class="col-sm-4" >
                                    <button type="button" class="btn btn-primary pull-right" id="documentok" ><?php echo BUTTON_OK; ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>


        </div>
    </div> </div>