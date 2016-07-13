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

<!--
<script>
    $(document).ready(function () {

    alert = function(){};
        $("#define_page").html("Drivers");

        $('.drivers').addClass('active');
        $('.driver_thumb').addClass("bg-success");

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



        $('#btnStickUpSizeToggle').click(function () {
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


                            $("#newpass").val('');
                            $("#confirmpass").val('');
                        }

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
        
        $("#joblogs").click(function(){
        
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
//        alert('now');
//$('#big_table').find('td:eq(6),th:eq(6)').hide();


        $('#big_table_processing').hide();
//        alert('<?php // echo $status;        ?>');
        var status = '<?php echo $status; ?>';

        if (status == 1) {
            $('#btnStickUpSizeToggle').show();
            $('#chekdel').show();
            $('#reject').show();
            $('#accept').show();
            $('#add').show();

            $('#big_table').find('td:eq(6),th:eq(6)').hide();
            $('#big_table').find('td:eq(7),th:eq(7)').hide();
            $('#big_table').find('td:eq(10),th:eq(10)').hide();

//             $('#big_table').find('td:eq(6)').each(function (){
//                $(this).hide();
//            });
//            $('#big_table').find('td:eq(7)').each(function (){
//                $(this).hide();
//            });
//            $('#big_table').find('td:eq(10)').each(function (){
//                $(this).hide();
//            });
        }

//            $("#tableWithSearch tr:last").hide();

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
                "sProcessing": "<img src='http://107.170.66.211/roadyo_live/sadmin/theme/assets/img/ajax-loader_dark.gif'>"
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


        $('.whenclicked li').click(function () {
            // alert($(this).attr('id'));

            if ($(this).attr('id') == "my1") {
                $('#btnStickUpSizeToggle').show();
                $('#chekdel').show();
                $('#reject').show();
                $('#accept').show();
                $('#add').show();
                $('#big_table').find('td:eq(6),th:eq(6)').hide();
                $('#big_table').find('td:eq(7),th:eq(7)').hide();
                $('#big_table').find('td:eq(10),th:eq(10)').hide();

            }
            else if ($(this).attr('id') == "my3") {
                $('#add').hide();
                $('#accept').hide();
                $('#reject').show();
                $('#big_table').find('td:eq(6),th:eq(6)').show();
                $('#big_table').find('td:eq(7),th:eq(7)').hide();
                $('#big_table').find('td:eq(10),th:eq(10)').hide();
                $('#chekdel').show();
                $('#btnStickUpSizeToggle').show();
            }
            else if ($(this).attr('id') == "my4") {
                $('#add').hide();
                $('#accept').show();
                $('#reject').hide();
                $('#big_table').find('td:eq(6),th:eq(6)').show();
                $('#big_table').find('td:eq(7),th:eq(7)').hide();
                $('#big_table').find('td:eq(10),th:eq(10)').hide();
                $('#chekdel').show();
                $('#btnStickUpSizeToggle').show();
            }
            
              if ($(this).attr('id') == "mo3") {
                $('#btnStickUpSizeToggle').show();
                $('#chekdel').show();
                $('#reject').show();
                $('#accept').show();
                $('#add').show();
                $('#big_table').find('td:eq(6),th:eq(6)').show();
                $('#big_table').find('td:eq(7),th:eq(7)').show();
                $('#big_table').find('td:eq(10),th:eq(10)').show();

            }
            else if ($(this).attr('id') == "mo30") {
                $('#add').hide();
                $('#accept').hide();
                $('#reject').show();
                $('#big_table').find('td:eq(6),th:eq(6)').show();
                $('#big_table').find('td:eq(7),th:eq(7)').show();
                $('#big_table').find('td:eq(10),th:eq(10)').show();
                $('#chekdel').show();
                $('#btnStickUpSizeToggle').show();
            }
            else if ($(this).attr('id') == "mo567") {
                $('#add').hide();
                $('#accept').show();
                $('#reject').hide();
                $('#big_table').find('td:eq(6),th:eq(6)').show();
                $('#big_table').find('td:eq(7),th:eq(7)').show();
                $('#big_table').find('td:eq(10),th:eq(10)').show();
                $('#chekdel').show();
                $('#btnStickUpSizeToggle').show();
            }


        });

        $('.changeMode').click(function () {

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
                    "sProcessing": "<img src='http://107.170.66.211/roadyo_live/sadmin/theme/assets/img/ajax-loader_dark.gif'>"
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
</script>-->


<style>
    .exportOptions{
        display: none;
    }
    .btn-cons {
        margin-right: 5px;
        min-width: 102px;
    }
</style>
<div class="page-content-wrapper"style="padding-top: 20px">
    <!-- START PAGE CONTENT -->
    <div class="content">


        <div class="brand inline" style="  width: auto;
             font-size: 20px;
             color: gray;
             margin-left: 30px;padding-top: 20px;">
           <!--                    <img src="--><?php //echo base_url();         ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();         ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();         ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

            <strong style="color:#0090d9;"><a href="<?php echo base_url()?>index.php/superadmin/Drivers/my/1">JOB LOGS</a></strong><!-- id="define_page"-->
        </div>
        <!-- START JUMBOTRON -->
        <div class="jumbotron" data-pages="parallax">
            <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">




                <div class="panel panel-transparent ">
                    <!-- Nav tabs -->
<!--                    <ul class="nav nav-tabs nav-tabs-fillup  bg-white whenclicked">
                        <li id= "my1" class="tabs_active <?php echo $new ?>" style="cursor:pointer">
                            <a  class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_drivers/my/1"><span><?php echo LIST_NEW; ?></span></a>
                        </li>
                        <li id= "my3" class="tabs_active <?php echo $accept ?>" style="cursor:pointer">
                            <a  class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_drivers/my/3"><span><?php echo LIST_ACCEPTED; ?></span></a>
                        </li>
                        <li id= "my4" class="tabs_active <?php echo $reject ?>" style="cursor:pointer">
                            <a  class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_drivers/my/4"><span><?php echo LIST_REJECTED; ?></span></a>
                        </li>


                        <li id= "mo3" class="tabs_active <?php echo $free ?>" style="cursor:pointer">
                            <a class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_drivers/mo/3"><?php echo LIST_FREEONLINE; ?></a>
                        </li>
                        <li id= "mo30" class="tabs_active <?php echo $offline ?>" style="cursor:pointer">
                            <a class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_drivers/mo/30"><?php echo LIST_OFFLINE; ?></a>
                        </li>
                        <li id= "mo567" class="tabs_active <?php echo $booked ?>" style="cursor:pointer">
                            <a class="changeMode" data="<?php echo base_url(); ?>index.php/superadmin/datatable_drivers/mo/567"><?php echo LIST_BOOKED; ?></a>
                        </li>
                        <?php if ($db != 'mo') { ?>
                                                        <div class="btn-group">

                            <div class=""><button class="btn btn-primary pull-right m-t-10 " id="document_data"  style="margin-left:10px;margin-top: 5px">Document</button></div>
                            <div class=""> <button class="btn btn-primary pull-right m-t-10" id="btnStickUpSizeToggle" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_RESETPASSWORD; ?></button></a></div>

                            <div class=""> <button class="btn btn-primary pull-right m-t-10" id="chekdel" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_DELETE; ?></button></a></div>
                            <div class=""><button class="btn btn-primary pull-right m-t-10 " id="editdriver" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_EDIT; ?></button></div>

                            <div class="">
                                <a> 
                                    <button class="btn btn-primary pull-right m-t-10 " id="joblogs" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_JOBLOGS; ?>
                                    </button></a>
                            </div>



                            <?php if ($status != '4' && $db != 'mo') { ?>
                                <div class=""> <button class="btn btn-primary pull-right m-t-10" id="reject" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_REJECT; ?></button></a></div>
                            <?php } ?>
                            <?php if ($status != '3' && $db != 'mo') { ?>
                                <div class=""> <button class="btn btn-primary pull-right m-t-10" id="accept" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_ACCEPT; ?></button></a></div>
                            <?php } ?>

                            <?php if ($status == '1') { ?>
                                <div class=""><a href="<?php echo base_url() ?>index.php/superadmin/addnewdriver"> <button class="btn btn-primary pull-right m-t-10" id="add" style="margin-left:10px;margin-top: 5px"><?php echo BUTTON_ADD; ?></button></a></div>
                            <?php } ?>

                                                        </div>
                        <?php } ?>




                    </ul>-->
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="container-fluid container-fixed-lg bg-white">
                            <!-- START PANEL -->
                            <div class="panel panel-transparent">
                                <div class="panel-heading">

                                    <div class="error-box" id="display-data" style="text-align:center"></div>
                                    <!--<div id="big_table_processing" class="dataTables_processing" style=""><img src="http://www.ahmed-samy.com/demos/datatables_2/assets/images/ajax-loader_dark.gif"></div>-->

                                    <div class="searchbtn row clearfix pull-right" >
                                        <!--<div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="<?php echo SEARCH; ?>"> </div>-->
<!--                                        <button type="button" class="btn btn-success" id="chekdel"><i class="fa fa-trash-o"></i>-->
                                        <!--                                        </button>-->
                                        <!--                                        <button class="btn btn-green btn-lg pull-right" id="editdriver"   style="line-height: 14px;color: #ffffff !important;margin-right: 2px;background-color: #10cfbd;" class="btn btn-success"  >-->
                                        <!--                                            <i class="fa fa-pencil"></i>-->
                                        <!--                                        </button>-->
                                    </div>




                                </div>
                                <div class="panel-body">

                                   <?php // echo $this->table->generate(); ?>
                                    <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer" id="big_table" role="grid" aria-describedby="tableWithSearch_info">
                                                <thead>

                                                    <tr role="row">
                                                        <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 150px;font-size: 14px;">SL.NO</th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 150px;font-size: 14px;">DATE</th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 150px;font-size: 14px;">NO.OF.SESSIONS</th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 150px;font-size: 14px;">TOTAL DISTANCE</th>
                                                        <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 150px;font-size: 14px;">VIEW LOGS</th>
                                                    </tr>


                                                </thead>
                                                <tbody>

                                                    <?php foreach($joblogs as $row){?>
                                                    <tr role="row"  class="gradeA odd">
                                                        <td id = "d_no" class="v-align-middle sorting_1"><?php $row->sr ?></td>
                                                        <td id = "d_no" class="v-align-middle sorting_1 mname"><?php $row->Date ?></td>
                                                        <td id = "d_no" class="v-align-middle sorting_1 mname"><?php $row->total ?></td>
                                                        <td id = "d_no" class="v-align-middle sorting_1 mname"><?php $row->distance ?></td>
                                                        <td class="v-align-middle"><a href="<?php echo base_url() ?>index.php/superadmin/sessiondetails"><?php $row->view ?></a></td>
                                                    
                                                       

                                                    </tr>
                                                    <?php } ?>

                                                </tbody>
                                            </table></div><div class="row"></div></div>
                                </div>
                            </div>
                            <!-- END PANEL -->
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
</div>


