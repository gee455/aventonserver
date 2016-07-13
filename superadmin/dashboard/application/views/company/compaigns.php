<?php
date_default_timezone_set('UTC');
$rupee = "$";
error_reporting(0);

if ($status == 1) {
    $passenger_status = 'active';
    $active = "active";
    echo '<style> .searchbtn{float: left;  margin-right: 63px;}.dltbtn{float: right;}</style>';
} else if ($status == 2) {
    $passenger_status = 'deactive';
    $deactive = "active";
}
?> 

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

    function isNumberKey(evt)
    {
        $("#pcode").text("");
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 45 || charCode > 57)) {
            //    alert("Only numbers are allowed");
            $("#pcode").text(<?php echo json_encode(LIST_COMPANY_MOBIFY); ?>);
            return false;
        }
        return true;
    }





    $(document).ready(function () {


        $('.datepicker-component').on('changeDate', function () {
            $(this).datepicker('hide');
        });

        var date = new Date();
        $('.datepicker-component').datepicker({
            startDate: date
        });

        $("#define_page").html("Compaigns");


        $('.compaigns').addClass('active');
        $('.compaigns').attr('src', "<?php echo base_url(); ?>/theme/icon/campaigns_on.png");
        $('.compaigns_thumb').addClass("bg-success");

//
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

//
        $('#secondadd').click(function () {
            var size = $('input[name=stickup_toggler]:checked').val()
            var modalElem = $('#myModals');
            if (size == "mini") {
                $('#modalStickUpSmall').modal('show')
            } else {
                $('#myModals').modal('show')
                if (size == "default") {
                    modalElem.children('.modal-dialog').removeClass('modal-lg');
                } else if (size == "full") {
                    modalElem.children('.modal-dialog').addClass('modal-lg');
                }
            }
        });
//
//
        $("#insert").click(function () {

            $('#errorBox_myModal').text('');

            var city = $("#selectcity").val();
            var title = $("#title").val();

            var discount = $("#discount").val();
            var message = $("#message").val();
            var referaldiscount = $("#referaldiscount").val();
            var title = $("#title").val();
            var alphabit = /^[a-zA-Z ]*$/;

            var discountradio = $('.optionyes1:checked').val();

            var refferalradio = $('.optionyes:checked').val();

            var re = /[a-zA-Z0-9\-\_]$/;
            var reg = /^[0-9]+$/;     //^[-]?(?:[.]\d+|\d+(?:[.]\d*)?)$/;



            if (title == '') {
                $('#errorBox_myModal').text(<?php echo json_encode(POPUP_COMPAIGNS_TITLE); ?>);
            }
            else if (city == "0") {
//                alert("please select city");
                $('#errorBox_myModal').text(<?php echo json_encode(POPUP_DISPATCHERS_CITY); ?>);
            }
            else if (discount == "" || discount == null)
            {
//                alert("please enter the discount");
                $('#errorBox_myModal').text(<?php echo json_encode(POPUP_COMPAIGNS_DISCOUNT); ?>);
            }
//            else if (!reg.test(discount))
//            {
//                //      alert("please enter  numbers only");
//                $('#errorBox_myModal').text(<?php echo json_encode(POPUP_COMPAIGNS_NUMBERS); ?>);
//            }
            else if (referaldiscount == "" || referaldiscount == null)
            {
                //      alert("please enter the referaldiscount");
                $('#errorBox_myModal').text(<?php echo json_encode(POPUP_COMPAIGNS_REFERALDISCOUNT); ?>);
            }
//            else if (!reg.test(referaldiscount))
//            {
//                //    alert("please enter numbers only");
//                $('#errorBox_myModal').text(<?php echo json_encode(POPUP_COMPAIGNS_NUMBERS); ?>);
//            }
            else if (message == "" || message == null)
            {
                //  alert("please enter the message");
                $('#errorBox_myModal').text(<?php echo json_encode(POPUP_COMPAIGNS_MESSAGE); ?>);
            }
//            else if (!re.test(message))
//            {
//                //            alert("please enter message as text only");
//                $('#errorBox_myModal').text(<?php echo json_encode(POPUP_COMPAIGNS_TEXT); ?>);
//            }
            else
            {

                $('.close').trigger('click');

                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/insertcompaigns",
                    type: 'POST',
                    data: {
                        coupon_type: 1,
                        city: city,
                        discount: discount,
                        message: message,
                        referaldiscount: referaldiscount,
                        discountradio: discountradio,
                        refferalradio: refferalradio,
                        title: title
                    },
                    dataType: 'JSON',
                    success: function (response)
                    {
                        if (response.flag == 1) {

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



                            $("#errorboxdatass").text(response.msg);
                            $("#confirmedss").click(function () {
//                                 window.location = "<?php echo base_url(); ?>/index.php/supersuperadmin/compaigns/1";
                                $('.close').trigger('click');
                            });


                        }
                        else if (response.flag == 0) {

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



                            $("#errorboxdatass").text(response.msg);
                            $("#confirmedss").click(function () {
                                window.location = "<?php echo base_url(); ?>/index.php/superadmin/compaigns/1";
                            });


                        }

                    }

                });
            }

        });

        $("#firsteditsubmit").click(function () {

            $('#errorBox_myModalfirst').text('');
            var val = $('.checkbox:checked').val();

            var title = $("#titlefirst").val();

            var discount = $("#discountfirst").val();
            var message = $("#messagefirst").val();
            var referaldiscount = $("#referaldiscountfirst").val();

            var alphabit = /^[a-zA-Z ]*$/;

            var discountradio = $('.optionyesfirst:checked').val();

            var refferalradio = $('.optionyesfirstreferal:checked').val();

            var re = /[a-zA-Z0-9\-\_]$/;
            var reg = /^[0-9]+$/;     //^[-]?(?:[.]\d+|\d+(?:[.]\d*)?)$/;



            if (title == '') {
                $('#errorBox_myModalfirst').text(<?php echo json_encode(POPUP_COMPAIGNS_TITLE); ?>);
            }

            else if (discount == "" || discount == null)
            {

                $('#errorBox_myModalfirst').text(<?php echo json_encode(POPUP_COMPAIGNS_DISCOUNT); ?>);
            }
//            else if (!reg.test(discount))
//            {
//
//                $('#errorBox_myModalfirst').text(<?php echo json_encode(POPUP_COMPAIGNS_NUMBERS); ?>);
//            }
            else if (referaldiscount == "" || referaldiscount == null)
            {

                $('#errorBox_myModalfirst').text(<?php echo json_encode(POPUP_COMPAIGNS_REFERALDISCOUNT); ?>);
            }
//            else if (!reg.test(referaldiscount))
//            {
//
//                $('#errorBox_myModalfirst').text(<?php echo json_encode(POPUP_COMPAIGNS_NUMBERS); ?>);
//            }
            else if (message == "" || message == null)
            {

                $('#errorBox_myModalfirst').text(<?php echo json_encode(POPUP_COMPAIGNS_MESSAGE); ?>);
            }

            else
            {

                $('.close').trigger('click');

                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/updatecompaigns",
                    type: 'POST',
                    data: {
                        coupon_type: 1,
                        val: val,
                        discount: discount,
                        message: message,
                        referaldiscount: referaldiscount,
                        discountradio: discountradio,
                        refferalradio: refferalradio,
                        title: title
                    },
                    dataType: 'JSON',
                    success: function (response)
                    {
                        window.location = "<?php echo base_url(); ?>/index.php/superadmin/compaigns/1";
                    }

                });
            }

        });
//        
        $("#deactive").click(function () {

            $("#display-data").text("");

            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).
                    get();
            if (val.length == 0) {
                //    alert("select atleast one compaign");
                $("#display-data").text(<?php echo json_encode(POPUP_COMPAIGN_ATLEAST); ?>);
            }
            if (val.length > 0) {

                //  $('#btnStickUpSizeToggle').click(function () {
                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#confirmmodel');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#confirmmodel').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }
                $("#errorboxdata").text(<?php echo json_encode(COMPAIGNS_DISPLAY); ?>);

                $("#confirmed").click(function () {

                    $('.close').trigger('click');

                    $.ajax({
                        url: "<?php echo base_url('index.php/superadmin') ?>/deactivecompaigns",
                        type: "POST",
                        data: {val: val},
                        dataType: 'json',
                        success: function (response)
                        {
//                            if (response.flag == 1)
                            $('.checkbox:checked').each(function (i) {
                                $(this).closest('tr').remove();
                            });
//                             window.location = "<?php echo base_url(); ?>/index.php/superadmin/compaigns/";
                            //    $("#errorboxdata").css("color":"blue");
//                            $("#errorboxdata").text(response.msg);
//                            $("#confirmed").hide();
                        }
                    });
                    //}

                });


            }

        });


        $('#firstedit').click(function () {

            $("#display-data").text("");

            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).
                    get();

            if (val.length == 0) {
                //    alert("select atleast one compaign");
                $("#display-data").text(<?php echo json_encode(POPUP_COMPAIGN_ONETOEDIT); ?>);
            }
            else if (val.length == 1) {



                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#myModalsfirstedit');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#myModalsfirstedit').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }

                $('#yesreferralfirst').removeAttr('checked');
                $('#noreferralfirst').removeAttr('checked');
                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/editcompaigns",
                    type: "POST",
                    data: {val: $('.checkbox:checked').val()},
                    dataType: 'json',
                    success: function (row)
                    {
//                            alert(JSON.stringify(response));

//                        $.each(response, function (index, row) {
                        $('#titlefirst').val(row.title);
                        $('#selectcityfirst').val(row.city_id);
                        var dis = row.discount_type;
                        if (dis == 1)
                            $("#yesfirst").attr('checked', 'checked');
//                                      
                        else if (dis == 2)
                            $("#nofirst").attr('checked', 'checked');
//                                  
                        $('#discountfirst').val(row.discount);
                        var refferal = row.referral_discount_type;

                        if (refferal == 1)
                            $("#yesreferralfirst").attr('checked', 'checked');
                        else if (refferal == 2)
                            $("#noreferralfirst").attr('checked', 'checked');
                        $('#referaldiscountfirst').val(row.referral_discount);
                        $('#messagefirst').val(row.message);
//                        });

                    }
                });
            }
            else if (val.length > 1) {
                $("#display-data").text(<?php echo json_encode(POPUP_COMPAIGN_ONLYONE); ?>);
            }

        });




        $('#secondedit').click(function () {

            $("#display-data").text("");

            var val = $('.checkbox:checked').map(function () {
                return this.value;
            }).
                    get();

            if (val.length == 0) {
                //    alert("select atleast one compaign");
                $("#display-data").text(<?php echo json_encode(POPUP_COMPAIGN_ONETOEDIT); ?>);
            }
            else if (val.length == 1) {


                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/editcompaigns",
                    type: "POST",
                    data: {val: $('.checkbox:checked').val()},
                    dataType: 'json',
                    success: function (row)
                    {
//                        alert(JSON.stringify(response));

//                        $.each(response, function (index, row) {
                        $('#titlesecond').val(row.title);
                        $('#selectcitysecond').val(row.city_id);
                        $('#codesecond').val(row.coupon_code);
                        $('#sdatesecond').val(row.start_date);
                        $('#edatesecond').val(row.expiry_date);
                        var dis = row.discount_type;
                        if (dis == 1)
                            $("#yessecondedit").attr('checked', 'checked');
//                                     
                        else if (dis == 2)
                            $("#nosecondedit").attr('checked', 'checked');
//                                   
                        $('#discountsecond').val(row.discount);
                        $('#messagesecond').val(row.message);
//                        });
                    }

                });


                var size = $('input[name=stickup_toggler]:checked').val()
                var modalElem = $('#myModalssecondedit');
                if (size == "mini") {
                    $('#modalStickUpSmall').modal('show')
                } else {
                    $('#myModalssecondedit').modal('show')
                    if (size == "default") {
                        modalElem.children('.modal-dialog').removeClass('modal-lg');
                    } else if (size == "full") {
                        modalElem.children('.modal-dialog').addClass('modal-lg');
                    }
                }


            }
            else if (val.length > 1) {
                $("#display-data").text(<?php echo json_encode(POPUP_COMPAIGN_ONLYONE); ?>);
            }

        });






        $("#inserts").click(function () {

            $('#errorbox_myModals').text('');

            var city = $("#selectcitys").val();


            var discount = $("#discounts").val();
            var message = $("#messages").val();
            var code = $("#code").val();
            var sdate = $("#sdate").val();
            var edate = $("#edate").val();
            var title = $("#title1").val();

            var alphabit = /^[a-zA-Z ]*$/;
            var alphanumeric = /[a-zA-Z0-9\-\_]$/;
            var reg = /^[0-9]+$/;     //^[-]?(?:[.]\d+|\d+(?:[.]\d*)?)$/;
            var discounttypes = $('.discount_types:checked').val();


            if (title == '') {
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_TITLE); ?>);
            }
            else if (city == "0")
            {
                //   alert("please select city");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_DISPATCHERS_CITY); ?>);
            }
            else if (code == "" || code == null)
            {
                //     alert("please enter the code");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_CODE); ?>);
            }
            else if (!alphanumeric.test(code))
            {
                //  alert("please enter code as text or number");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_TEXTNUMBER); ?>);
            }

            else if (sdate == "" || sdate == null)
            {
                //    alert("please select  the start date");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_STARTDATE); ?>);
            }
            else if (edate == "" || edate == null)
            {
                //      alert("please select  the expire date");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_EXPIREDATE); ?>);
            }



            else if (discount == "" || discount == null)
            {
                //      alert("please enter the discount");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_PROMOTION); ?>);
            }
//            else if (!reg.test(discount))
//            {
//                //     alert("please enter  numbers only");
//                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_NUMBERS); ?>);
//            }

            else if (message == "" || message == null)
            {
                //       alert("please enter the message");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_MESSAGE); ?>);
            }
//            else if (!alphabit.test(message))
//            {
//                //         alert("please enter message as text only");
//                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_TEXT); ?>);
//            }


            else
            {
                $('.close').trigger('click');


                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/insertcompaigns",
                    type: 'POST',
                    data: {
                        coupon_type: 2,
                        citys: city,
                        discounts: discount,
                        messages: message,
                        codes: code,
                        sdate: sdate,
                        edate: edate,
                        discounttypes: discounttypes,
                        title: title

                    },
                    dataType: 'JSON',
                    success: function (response)
                    {
                        if (response.flag == 0) {

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
                            $("#errorboxdatass").text(<?php echo json_encode(POPUP_COMPAIGNS_TEXT_PROMOTIONS_S); ?>);
                            $("#confirmedss").click(function () {
                                window.location = "<?php echo base_url(); ?>/index.php/superadmin/compaigns/2";
                            });


                        }

                        else if (response.flag == 1) {

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
                            $("#errorboxdatass").text(response.msg);
                            $("#confirmedss").click(function () {
                                window.location = "<?php echo base_url(); ?>/index.php/superadmin/compaigns/2";
                            });
                        }
                    }

                });
            }

        });

        $("#secondeditsubmit").click(function () {

            $('#errorbox_myModals').text('');

            var val = $('.checkbox:checked').val();
            var discount = $("#discountsecond").val();
            var message = $("#messagesecond").val();
            var code = $("#codesecond").val();
            var sdate = $("#sdatesecond").val();
            var edate = $("#edatesecond").val();
            var title = $("#titlesecond").val();

            var alphabit = /^[a-zA-Z ]*$/;
            var alphanumeric = /[a-zA-Z0-9\-\_]$/;
            var reg = /^[0-9]+$/;     //^[-]?(?:[.]\d+|\d+(?:[.]\d*)?)$/;
            var discounttypes = $('.discounttypesecondedit:checked').val();


            if (title == '') {
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_TITLE); ?>);
            }

            else if (code == "" || code == null)
            {
                //     alert("please enter the code");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_CODE); ?>);
            }
            else if (!alphanumeric.test(code))
            {
                //  alert("please enter code as text or number");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_TEXTNUMBER); ?>);
            }

            else if (sdate == "" || sdate == null)
            {
                //    alert("please select  the start date");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_STARTDATE); ?>);
            }
            else if (edate == "" || edate == null)
            {
                //      alert("please select  the expire date");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_EXPIREDATE); ?>);
            }



            else if (discount == "" || discount == null)
            {
                //      alert("please enter the discount");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_PROMOTION); ?>);
            }
//            else if (!reg.test(discount))
//            {
//                //     alert("please enter  numbers only");
//                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_NUMBERS); ?>);
//            }

            else if (message == "" || message == null)
            {
                //       alert("please enter the message");
                $('#errorbox_myModals').text(<?php echo json_encode(POPUP_COMPAIGNS_MESSAGE); ?>);
            }

            else
            {
                $('.close').trigger('click');


                $.ajax({
                    url: "<?php echo base_url('index.php/superadmin') ?>/updatecompaigns",
                    type: 'POST',
                    data: {
                        coupon_type: 2,
                        val2: val,
                        discounts: discount,
                        messages: message,
                        codes: code,
                        sdate: sdate,
                        edate: edate,
                        discounttypes: discounttypes,
                        title: title

                    },
                    dataType: 'JSON',
                    success: function (response)
                    {
//                        alert(response.msg);
//                        location.reload();
                        window.location = "<?php echo base_url(); ?>/index.php/superadmin/compaigns/2";
                    }

                });
            }

        });

        $('#selectOPT').change(function () {


            if ($(this).val() == 0) {
                $('#deactive').show();
                $('#secondadd').show();
            }
            else if ($(this).val() == 10) {

                $('#secondadd').hide();
                $('#deactive').hide();

            }
            else if ($(this).val() == 1) {
                $('#deactive').hide();


            }

            $.ajax({
                url: "<?php echo base_url('index.php/superadmin') ?>/compaigns_ajax/<?php echo $status ?>",
                                type: 'POST',
                                data: {value: $(this).val()},
                                dataType: 'JSON',
                                success: function (response)
                                {
                                    var table = $('.tableWithSearch_referels').DataTable();
                                    table.clear().draw();
                                    
                                    var t = $('.tableWithSearch_promotions').DataTable();
                                    t.clear().draw();

                                    if(response.data[0].coupon_type == 1)
                                    {
                                      
//                                    
                                    $.each(response.data, function (index, row) {
                                        var disc = '--';
                                        var other = '--';
                                        var referr = '--';
                                        var others = '--';
                                        if (row.discount_type == 1) {
                                            disc = row.discount;

                                        }
                                        else if (row.discount_type == 2) {
                                            other = row.discount;
                                        }

                                        if (row.referral_discount_type == 1) {
                                            referr = row.referral_discount;
                                        }
                                        else if (row.referral_discount_type == 2) {
                                            others = row.referral_discount;
                                        }

                                        table.row.add([
                                            row._id.$id,
                                            row.title,
                                            disc,
                                            other,
                                            referr,
                                            others,
                                            row.city_name,
                                            row.currency,
                                            '<a href="<?php echo base_url(); ?>index.php/superadmin/referral_details/' + row._id.$id + '">\n\
                                <button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a><input type="checkbox" value="' + row._id.$id + '"  class="checkbox check-primary">'
//                                            
                                        ]).draw();

                                    });
                                    }
                                    else
                                    {
                                          
                                    $.each(response.data, function (index, row) {
                                        var disc = '--';
                                        var other = '--';
//                                        var referr= '--';
//                                        var others = '--';
                                        if (row.discount_type == 1) {
                                            disc = row.discount;

                                        }
                                        else if (row.discount_type == 2) {
                                            other = row.discount;
                                        }

//                                        if(row.referral_discount_type == 1){
//                                       referr = row.referral_discount;
//                                       }
//                                       else if(row.referral_discount_type == 2){
//                                       others = row.referral_discount;
//                                     }

                                        t.row.add([
                                            row._id.$id,
                                            row.coupon_code,
                                            row.title,
                                            row.start_date,
                                            row.expiry_date,
                                            disc,
                                            other,
                                            row.city_name,
                                            row.currency,
                                           '<a href="<?php echo base_url(); ?>index.php/superadmin/promo_details/' + row._id.$id + '">\n\
                                <button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a><input type="checkbox" value="' + row._id.$id+ '" class="checkbox check-primary">'
////                                            
                                        ]).draw();
                                        });
                                  
                                    }
                                }

                            });

                        });



//        var table = $('#big_table');
//
//        var settings = {
//            "autoWidth": false,
//            "sDom": "<'table-responsive't><'row'<p i>>",
//            "destroy": true,
//            "scrollCollapse": true,
//            "iDisplayLength": 20,
//            "bProcessing": true,
//            "bServerSide": true,
//            "sAjaxSource": '<?php echo base_url() ?>index.php/superadmin/datatable_compaigns/3',
//            "bJQueryUI": true,
//            "sPaginationType": "full_numbers",
//            "iDisplayStart ": 20,
//            "oLanguage": {
//                "sProcessing": "<img src='http://107.170.66.211/roadyo_live/ssuperadmin/theme/assets/img/ajax-loader_dark.gif'>"
//            },
//            "fnInitComplete": function () {
//                //oTable.fnAdjustColumnSizing();
//            },
//            'fnServerData': function (sSource, aoData, fnCallback)
//            {
//                $.ajax
//                        ({
//                            'dataType': 'json',
//                            'type': 'POST',
//                            'url': sSource,
//                            'data': aoData,
//                            'success': fnCallback
//                        });
//            }
//        };
//
//        table.dataTable(settings);
//
//        // search box for table
//        $('#search-table').keyup(function () {
//            table.fnFilter($(this).val());
//        });






                    });

</script>

<style>
    .exportOptions{
        display: none;
    }

    .datepicker{z-index:1151 !important;}
</style>
<div class="page-content-wrapper"style="padding-top: 20px">
    <!-- START PAGE CONTENT -->
    <div class="content">

        <div class="brand inline" style="  width: auto;
             font-size: 20px;
             color:#0090d9;
             margin-left: 30px;padding-top: 20px;">
           <!--                    <img src="--><?php //echo base_url();                 ?><!--theme/assets/img/Rlogo.png" alt="logo" data-src="--><?php //echo base_url();                 ?><!--theme/assets/img/Rlogo.png" data-src-retina="--><?php //echo base_url();                 ?><!--theme/assets/img/logo_2x.png" width="93" height="25">-->

            <strong>CAMPAIGNS</strong><!-- id="define_page"-->
        </div>
        <!-- START JUMBOTRON -->
        <div class="jumbotron" data-pages="parallax">
            <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">




                <div class="panel panel-transparent ">

                    <!-- Nav tabs -->

                    <div>
                        <ul class="nav nav-tabs nav-tabs-fillup  bg-white">
                            <li class="<?php echo ($status == 1 ? "active" : ""); ?>">
                                <a  href="<?php echo base_url(); ?>index.php/superadmin/compaigns/1"><span><?php echo LIST_REFERRALS; ?></span></a>
                            </li>
                            <li class="<?php echo ($status == 2 ? "active" : ""); ?>">
                                <a  href="<?php echo base_url(); ?>index.php/superadmin/compaigns/2"><span><?php echo LIST_PROMOTIONS; ?> </span></a>
                            </li>
                            <li class="<?php echo ($status == 3 ? "active" : ""); ?>">
                                <a  href="<?php echo base_url(); ?>index.php/superadmin/compaigns/3"><span><?php echo LIST_REFFERED_PROMOS; ?> </span></a>
                            </li>

                            <?php if ($status != 3) { ?>
                                <div class="pull-right m-t-10" > <button class="btn btn-primary btn-cons" id="deactive" ><?php echo BUTTON_DEACTIVE; ?></button></div>
                            <?php } ?>
                            <?php if ($status == 1) { ?>
                                <div class="pull-right m-t-10" > <button class="btn btn-primary btn-cons" id="firstedit" ><?php echo BUTTON_EDIT; ?></button></div>

                                <div class="pull-right m-t-10" > <button class="btn btn-primary btn-cons" id="btnStickUpSizeToggler" ><?php echo BUTTON_ADD; ?></button></div>
                            <?php } ?>

                            <?php if ($status == 2) { ?>
                                <div class="pull-right m-t-10" > <button class="btn btn-primary btn-cons" id="secondedit" ><?php echo BUTTON_EDIT; ?></button></div>

                                <div class="pull-right m-t-10" > <button class="btn btn-primary btn-cons" id="secondadd" ><?php echo BUTTON_ADD; ?></button></div>
                            <?php } ?> 

                        </ul>


                        <div class="container-fluid container-fixed-lg bg-white">
                            <!-- START PANEL -->
                            <div class="panel panel-transparent">
                                <div class="panel-heading">

                                    <div class="error-box" id="display-data" style="text-align:center">

                                    </div>
                                    <div class="pull-left">
                                        <select class="form-control col-md-3" id="selectOPT">

                                            <?php if ($status == 1) { ?>
                                                <option value="0" id="act">ACTIVE</option>
                                                <option value="1" id="inact">INACTIVE</option>
                                            <?php } else if ($status == 3) { ?>
                                                <option value="31" id='expire'> USED</option>
                                                <option value="32" id='expire'> UNUSED</option>
                                                <option value="33" id='expire'> EXPIRED</option>
                                            <?php } else { ?>
                                                <option value="0" id="act">ACTIVE</option>
                                                <option value="10" id='expire'> EXPIRED</option>
                                            <?php } ?>
                                        </select>
                                    </div>





                                    <div class="row searchbtn  clearfix pull-right" >

                                        <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="<?php echo SEARCH; ?> "> </div>
                                    </div>






                                </div>


                                <div class="panel-body">

                                    <?php if ($status == 1) { ?>                                
                                        <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer tableWithSearch_referels" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">
                                                    <thead>

                                                        <tr role="row">
                                                            <th  rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 87px;font-size:15px"> CAMPAIGN ID</th>
                                                            <th  rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 87px;font-size:15px"> TITLE</th>
                                                            <th colspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px"> NEW USER <?php echo COMPAIGNS_TABLEL_DISCOUNT; ?></th>
                                                            <th colspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px"> REFERRAL BONUS</th>
    <!--                                                            <th rowspan="2" class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px">--><?php //echo COMPAIGNS_TABLE_REFERRALDISCOUNT;                ?><!--</th>-->
    <!--                                                            <th rowspan="2" class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px">--><?php //echo COMPAIGNS_TABLE_MESSAGE;                ?><!--</th>-->
                                                            <th rowspan="2" class="sorting " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 87px;font-size:15px"><?php echo COMPAIGNS_TABLE_CITY; ?></th>
                                                            <th rowspan="2" class="sorting " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 87px;font-size:15px"><?php echo COMPAIGNS_TABLE_CURRENCY; ?></th>
                                                            <th rowspan="2" class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 87px;font-size:15px"><?php echo COMPAIGNS_TABLE_SELECT; ?></th>
                                                        </tr>

                                                        <tr>

                                                            <th width="50PX" class="discountpercent"> PERCENT (%)</th>
                                                            <th width="50PX" class="discountfixed">  FIXED  </th>
                                                            <th width="50PX"  class="referralpercent"> PERCENT (%)</th>
                                                            <th width="50PX"  class="referralfixed">  FIXED  </th>

                                                        </tr>


                                                    </thead>
                                                    <tbody>












                                                        <?php
                                                        $i = '1';
                                                        foreach ($compaign as $result) {
                                                            ?>


                                                            <tr role="row"  class="gradeA odd">
                                                                <td id = "" class="v-align-middle "><?php echo (string) $result['_id']; ?></td>
                                                                <td id = "" class="v-align-middle "> <p><?php echo $result['title']; ?></p></td>
                                                                <td id = "" class="v-align-middle "> <p><?php echo ($result['discount_type'] == 1 ? ($result['discount']) : '--'); ?></p></td>
                                                                <td id = "" class="v-align-middle "> <p><?php echo ($result['discount_type'] == 2 ? ($result['discount']) : '--'); ?></p></td>
                                                                <td id = "" class="v-align-middle "> <p><?php echo ($result['referral_discount_type'] == 1 ? ($result['referral_discount']) : '--'); ?></p></td>
                                                                <td id = "" class="v-align-middle "> <p><?php echo ($result['referral_discount_type'] == 2 ? ($result['referral_discount']) : '--'); ?></p></td>
                                                <!--                                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p>--><?php //echo ($result->discount_type == 1 ? ($result->discount . "%") : $result->discount);                ?><!--</p></td>-->
                                                <!--                                                                        <td class="v-align-middle">--><?php //echo $result->message;                ?><!--</td>-->
                                                                <td class="v-align-middle"> <?php echo $result['city_name']; ?> </td>
                                                                <td class="v-align-middle"> <?php echo $result['currency']; ?> </td>



                                                                <td class="v-align-middle">
                                                                    <a  href="<?php echo base_url(); ?>index.php/superadmin/referral_details/<?php echo (string) $result['_id']; ?>"><button class="btn btn-success btn-cons" style="min-width: 83px !important;" value="<?php echo (string) $result['_id']; ?>">DETAILS</button></a>
                                                                    <div class="checkbox check-primary">
                                                                        <input type="checkbox" value="<?php echo (string) $result['_id']; ?>" id="checkbox<?php echo $i; ?>" class="checkbox">
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
                                                </table>

                                            <?php } ?>
                                            <div class="row"></div>


                                        </div>

                                        <?php if ($status == 2) { ?>                               
                                            <div class="panel-body">
                                                <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive"><table class="table table-hover demo-table-search dataTable no-footer tableWithSearch_promotions" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">
                                                            <thead>

                                                                <tr role="row">
                                                                    <th  rowspan="2" class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 87px;font-size:15px"> PROMOTION ID</th>
                                                                    <th  rowspan="2" class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 87px;font-size:15px"> <?php echo COMPAIGNS_TABLE_CODE; ?></th>
                                                                    <th rowspan="2"  class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 87px;font-size:15px"> TITLE</th>
                                                                    <th rowspan="2"  class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo COMPAIGNS_TABLE_STARTDATE; ?></th>

                                                                    <th rowspan="2"  class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px"><?php echo COMPAIGNS_TABLE_ENDDATE; ?></th>
                                                                    <th  colspan="2" class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 300px;font-size:15px"> PROMOTION <?php echo COMPAIGNS_TABLE_DISCOUNT; ?></th>
    <!--                                                                    <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size:15px">--><?php //echo COMPAIGNS_TABLE_MESSAGE;                ?><!--</th>-->
                                                                    <th rowspan="2"  class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 87px;font-size:15px"><?php echo COMPAIGNS_TABLE_PROMOT_CITY; ?></th>
                                                                    <th rowspan="2"  class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 87px;font-size:15px"><?php echo COMPAIGNS_TABLE_CURRENCY; ?></th>
                                                                    <th rowspan="2"  class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 140px;font-size:15px"><?php echo COMPAIGNS_TABLE_SELECT; ?></th>


                                                                </tr>

                                                                <tr>

                                                                    <th width="10px"> PERCENT (%)</th>
                                                                    <th width="10px">  FIXED </th>
                                                                </tr>

                                                            </thead>
                                                            <tbody>

                                                                <?php
                                                                $i = '1';
                                                                foreach ($compaign as $result) {
                                                                    ?>

                                                                    <tr role="row"  class="gradeA odd">
                                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo (string) $result['_id']; ?></p></td>
                                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result['coupon_code']; ?></p></td>
                                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result['title']; ?></p></td>
                                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo date('Y-m-d H:i:s', $result['start_date'])?></p></td>
                                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo date('Y-m-d H:i:s', $result['expiry_date']); ?></p></td>
                                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo ($result['discount_type'] == 1 ? ($result['discount']) : '--'); ?></p></td>
                                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo ($result['discount_type'] == 2 ? ($result['discount']) : '--'); ?></p></td>
                                                        <!--                                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p>--><?php //echo ($result->discount_type == 1 ? ($result->discount . "%") : $result->discount);                ?><!--</p></td>-->
                                                        <!--                                                                        <td class="v-align-middle">--><?php //echo $result->message;                ?><!--</td>-->
                                                                        <td class="v-align-middle"> <?php echo $result['city_name']; ?> </td>
                                                                        <td class="v-align-middle"> <?php echo $result['currency']; ?> </td>



                                                                        <td class="v-align-middle">
                                                                            <a  href="<?php echo base_url(); ?>index.php/superadmin/promo_details/<?php echo (string) $result['_id']; ?>"><button class="btn btn-success btn-cons" style="min-width: 83px !important;" value="<?php echo (string) $result['_id']; ?>">DETAILS</button></a>
                                                                            <div class="checkbox check-primary">
                                                                                <input type="checkbox" value="<?php echo (string) $result['_id']; ?>" id="checkbox<?php echo $i; ?>" class="checkbox">
                                                                                <label for="checkbox<?php echo $i; ?>">Mark</label>
                                                                            </div>
                                                                        </td>

                                                                    </tr>

                                                                    <?php
                                                                    $i++;
                                                                }
                                                                ?> 


                                                            </tbody>
                                                        </table>
                                                    <?php } ?>


                                                    <?php if ($status == 3) { ?>  

                                                        <?php // print_r($compaign[0]['coupon_code']); ?>
                                                        <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer"><div class="table-responsive">
                                                                <table class="table table-hover demo-table-search dataTable no-footer tableWithSearch_referels" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">
                                                                    <thead>

                                                                        <tr role="row">
                                                                            <!--<th  rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px"> SLNO</th>-->
                                                                            <th  rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px">  CUSTOMER ID</th>
                                                                            <th  rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px">  CUSTOMER EMAIL</th>
                                                                            <!--<th  rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px"> INVOICE VALUE</th>-->
                                                                            <th  rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px"> PROMO CODE</th>
                                                                            <th  rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px"> DISCOUNT</th>
                                                                            <th  rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px"> EXPIRY DATE</th>
                                                                            <!--<th  rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px"> BOOKING ID</th>-->
                                                                            <th  rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px"> BOOKING ID</th>
                                                                            <th  rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px"> INVOICE VALUE</th>
                                                                            <!--<th rowspan="2" class="sorting_asc " tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 120px;font-size:15px"> CUSTOMER EMAIL</th>-->




                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($compaign as $data) { ?>
                                                                            <tr role="row"  class="gradeA odd">
                                                                                <td id = "d_no" class="v-align-middle sorting_1 "><?php echo $data['user_id']; ?> </td>
                                                                                <td id = "d_no" class="v-align-middle sorting_1 "> <?php echo $data['email']; ?></td>
                                                                                <td id = "d_no" class="v-align-middle sorting_1 "> <p><?php echo $data['coupon_code']; ?></p></td>
                                                                                <td id = "d_no" class="v-align-middle sorting_1 "> <p><?php
                                                                                        echo $data['discount'];
                                                                                        echo ($data['discount_type'] == '2' ? "" : "%");
                                                                                        ?></p></td>
                                                                                <td id = "d_no" class="v-align-middle sorting_1 "> <p><?php echo date('Y-m-d H:i:s', $data['expiry_date']); ?></p></td>
                                                                                <?php
                                                                                $flag = 1;
                                                                                foreach ($data['bookings'] as $booking) {
                                                                                    if ($booking['status'] == '2') {
                                                                                        $flag = 0;
                                                                                        ?>
                                                                                        <td id = "d_no" class="v-align-middle sorting_1 "> <p><?php echo $booking['booking_id']; ?></p></td>
                                                                                        <td id = "d_no" class="v-align-middle sorting_1 "> <p><?php echo $booking['sub_total']; ?></p></td>
                                                                                    <?php }
                                                                                } if($flag == 1){
                                                                                    ?>
                                                                                        
                                                                                <td id = "d_no" class="v-align-middle sorting_1 "> <p><?php echo "-"; ?></p></td>
                                                                                <td id = "d_no" class="v-align-middle sorting_1 "> <p><?php echo "-"; ?></p></td>
                                                                                        <?php
                                                                                }
?>


                                                                            </tr>
    <?php } ?>
                                                                    </tbody>
                                                                </table>
<?php } ?>



                                                            <div class="row"></div></div>
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
                    </div>


                    <div class="modal fade stick-up" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-body">

                                    <div class="modal-header">

                                        <div class=" clearfix text-left">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                                            </button>

                                        </div>
                                        <h3> <?php echo LIST_REFFERAL_HEAD; ?></h3>
                                    </div>

                                    <br>

                                    <div class="modal-body">
                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_TITLE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="title" name="title" class="form-control" placeholder="Title">
                                            </div>
                                        </div>

                                        <br>
                                        <br>

                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label" id=""><?php echo FIELD_VEHICLE_SELECTCITY; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">

                                                <select id="selectcity" name="city_select"  class="form-control">
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
                                            <label for="fname" class="col-sm-4 control-label" ><?php echo FIELD_COMPAIGNS_DISCOUNTTYPE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <div class="radio radio-success" >
                                                    <input type="radio" value="1" name="optionyes1" class="optionyes1" id="yes">
                                                    <label for="yes"><?php echo FIELD_COMPAIGNS_PERCENTAGE; ?></label>
                                                    <input type="radio"  value="2"  checked="checked" name="optionyes1" class="optionyes1"  id="no" class="formex">
                                                    <label for="no"><?php echo FIELD_COMPAIGNS_FIXED; ?></label>
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <br>


                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"> <?php echo FIELD_COMPAIGNS_DISCOUNT; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="discount" name="latitude"  class="form-control" placeholder="" onkeypress="return isNumberKey(event)">
                                            </div>
                                        </div>


                                        <br>
                                        <br>




                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_REFERRALDISCOUNTTYPE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <div class="radio radio-success" class="formex">
                                                    <input type="radio" value="1" name="optionyes" class="optionyes" id="yes1">
                                                    <label for="yes1"><?php echo FIELD_COMPAIGNS_PERCENTAGE; ?></label>
                                                    <input type="radio" checked="checked" value="2" name="optionyes"  class="optionyes" id="no1" >
                                                    <label for="no1"><?php echo FIELD_COMPAIGNS_FIXED; ?></label>
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <br>

                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_REFERRALDISCOUNT; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="referaldiscount" name="longitude" class="form-control" placeholder="" onkeypress="return isNumberKey(event)">
                                            </div>
                                        </div>

                                        <br>
                                        <br>
                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_MESSAGE; ?></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="message" name="longitude" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                        <br>

                                        <div class="row">
                                            <div class="col-sm-4" ></div>
                                            <div class="col-sm-4 error-box" id="errorBox_myModal"></div>
                                            <div class="col-sm-4" >
                                                <button type="button" class="btn btn-primary pull-right" id="insert" ><?php echo BUTTON_SUBMIT; ?></button>
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
                    <div class="modal fade stick-up" id="myModalsfirstedit" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-body">

                                    <div class="modal-header">

                                        <div class=" clearfix text-left">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                                            </button>

                                        </div>
                                        <h3> <?php echo LIST_REFFERAL_HEAD; ?></h3>
                                    </div>

                                    <br>

                                    <div class="modal-body">
                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_TITLE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="titlefirst" name="title" class="form-control" placeholder="Title" value="">
                                            </div>
                                        </div>

                                        <br>
                                        <br>

                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label" id=""><?php echo FIELD_VEHICLE_SELECTCITY; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">

                                                <select id="selectcityfirst" name="city_select"  class="form-control" disabled="disabled" value="">
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
                                            <label for="fname" class="col-sm-4 control-label" ><?php echo FIELD_COMPAIGNS_DISCOUNTTYPE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <div class="radio radio-success" >
                                                    <input type="radio" value="1" name="optionyesfirst" class="optionyesfirst" id="yesfirst">
                                                    <label for="yes"><?php echo FIELD_COMPAIGNS_PERCENTAGE; ?></label>
                                                    <input type="radio"  value="2" name="optionyesfirst" class="optionyesfirst"  id="nofirst" class="formex">
                                                    <label for="no"><?php echo FIELD_COMPAIGNS_FIXED; ?></label>
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <br>


                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"> <?php echo FIELD_COMPAIGNS_DISCOUNT; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="discountfirst" name="latitude"  class="form-control" value="" onkeypress="return isNumberKey(event)">
                                            </div>
                                        </div>


                                        <br>
                                        <br>




                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_REFERRALDISCOUNTTYPE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <div class="radio radio-success" class="formex">
                                                    <input type="radio" value="1" name="optionyesfirstreferal" class="optionyesfirstreferal" id="yesreferralfirst">
                                                    <label for="yes1"><?php echo FIELD_COMPAIGNS_PERCENTAGE; ?></label>
                                                    <input type="radio"  value="2" name="optionyesfirstreferal" checked="checked" class="optionyesfirstreferal" id="noreferralfirst" >
                                                    <label for="no1"><?php echo FIELD_COMPAIGNS_FIXED; ?></label>
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <br>

                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_REFERRALDISCOUNT; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="referaldiscountfirst" name="longitude" class="form-control" placeholder="" onkeypress="return isNumberKey(event)">
                                            </div>
                                        </div>

                                        <br>
                                        <br>
                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_MESSAGE; ?></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="messagefirst" name="longitude" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                        <br>

                                        <div class="row">
                                            <div class="col-sm-4" ></div>
                                            <div class="col-sm-4 error-box" id="errorBox_myModalfirst"></div>
                                            <div class="col-sm-4" >
                                                <button type="button" class="btn btn-primary pull-right" id="firsteditsubmit" ><?php echo BUTTON_SUBMIT; ?></button>
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





                    <div class="modal fade stick-up" id="myModals" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-body">

                                    <div class="modal-header">

                                        <div class=" clearfix text-left">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                                            </button>

                                        </div>
                                        <h3> <?php echo LIST_PRAMOTION_HEAD; ?></h3>
                                    </div>

                                    <br>

                                    <div class="modal-body">

                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_TITLE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="title1" name="title1" class="form-control" placeholder="Title">
                                            </div>
                                        </div>

                                        <br>
                                        <br>
                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label" id=""><?php echo POPUP_DISPATCHERS_CITY; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">

                                                <select id="selectcitys" name="city_select"  class="form-control">
                                                    <option value="0">Select city</option>
                                                    <?php
                                                    foreach ($city as $result) {

                                                        echo "<option value=" . $result->City_Id . ">" . $result->City_Name . "</option>";
                                                    }
                                                    ?>

                                                </select>
                                            </div>
                                        </div>

                                        <BR>
                                        <BR>

                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_CODE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="code" name="longitude" class="form-control" placeholder="">
                                            </div>
                                        </div>


                                        <br>
                                        <br>

                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label" id="strdate"><?php echo FIELD_COMPAIGNS_STARTDATE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <div  class="input-group date ">
                                                    <input type="text" class="form-control datepicker-component"  id="sdate"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                </div>

                                            </div>
                                        </div>


                                        <br>
                                        <br>


                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_EXPIREDATE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <div  class="input-group date">
                                                    <input type="text" class="form-control datepicker-component" id="edate"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                </div>






                                            </div>
                                        </div>


                                        <br>
                                        <br>


                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label" id="discounttype"><?php echo FIELD_COMPAIGNS_DISCOUNTTYPE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <div class="radio radio-success" >
                                                    <input type="radio" value="1" name="discount_types" class="discount_types" id="yes2" >
                                                    <label for="yes2"><?php echo FIELD_COMPAIGNS_PERCENTAGE; ?></label>
                                                    <input type="radio" checked="checked" value="2" name="discount_types" class="discount_types" id="no2" class="formex" >
                                                    <label for="no2"><?php echo FIELD_COMPAIGNS_FIXED; ?></label>
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <br>


                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"> <?php echo FIELD_PROMOTION_DISCOUNT; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="discounts" name="discount"  class="form-control" placeholder="" onkeypress="return isNumberKey(event)">
                                            </div>
                                        </div>



                                        <br>
                                        <br>

                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_MESSAGE; ?></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="messages" name="message" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                        <br>

                                        <div class="row">
                                            <div class="col-sm-4" ></div>
                                            <div class="col-sm-4 error-box" id="errorbox_myModals"></div>
                                            <div class="col-sm-4" >
                                                <button type="button" class="btn btn-primary pull-right" id="inserts" ><?php echo BUTTON_SUBMIT; ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>


                    </div>
                    <div class="modal fade stick-up" id="myModalssecondedit" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-body">

                                    <div class="modal-header">

                                        <div class=" clearfix text-left">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                                            </button>

                                        </div>
                                        <h3> <?php echo LIST_PRAMOTION_HEAD; ?></h3>
                                    </div>

                                    <br>

                                    <div class="modal-body">

                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_TITLE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="titlesecond" name="title1" class="form-control" placeholder="Title" value="">
                                            </div>
                                        </div>

                                        <br>
                                        <br>
                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label" id=""><?php echo POPUP_DISPATCHERS_CITY; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">

                                                <select id="selectcitysecond" name="city_select"  class="form-control" disabled="disabled">
                                                    <option value="0">Select city</option>
                                                    <?php
                                                    foreach ($city as $result) {

                                                        echo "<option value=" . $result->City_Id . ">" . $result->City_Name . "</option>";
                                                    }
                                                    ?>

                                                </select>
                                            </div>
                                        </div>

                                        <BR>
                                        <BR>

                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_CODE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="codesecond" name="longitude" class="form-control" value="">
                                            </div>
                                        </div>


                                        <br>
                                        <br>

                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label" id="strdate"><?php echo FIELD_COMPAIGNS_STARTDATE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <div  class="input-group date ">
                                                    <input type="text" class="form-control datepicker-component"  id="sdatesecond" value="" ><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                </div>

                                            </div>
                                        </div>


                                        <br>
                                        <br>


                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_EXPIREDATE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <div  class="input-group date">
                                                    <input type="text" class="form-control datepicker-component" id="edatesecond" value=""><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>


                                        <br>
                                        <br>


                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label" id="discounttype"><?php echo FIELD_COMPAIGNS_DISCOUNTTYPE; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <div class="radio radio-success" >
                                                    <input type="radio" value="1" name="discount_type" class="discounttypesecondedit" id="yessecondedit" >
                                                    <label for="yes2"><?php echo FIELD_COMPAIGNS_PERCENTAGE; ?></label>
                                                    <input type="radio" checked="checked" value="2" name="discount_type" class="discounttypesecondedit" id="nosecondedit" class="formex" >
                                                    <label for="no2"><?php echo FIELD_COMPAIGNS_FIXED; ?></label>
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <br>


                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"> <?php echo FIELD_PROMOTION_DISCOUNT; ?><span style="color:red;font-size: 18px">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="discountsecond" name="discount"  class="form-control" value="" onkeypress="return isNumberKey(event)">
                                            </div>
                                        </div>



                                        <br>
                                        <br>

                                        <div class="form-group" class="formex">
                                            <label for="fname" class="col-sm-4 control-label"><?php echo FIELD_COMPAIGNS_MESSAGE; ?></label>
                                            <div class="col-sm-6">
                                                <input type="text"  id="messagesecond" name="message" class="form-control" value="">
                                            </div>
                                        </div>
                                        <br>

                                        <div class="row">
                                            <div class="col-sm-4" ></div>
                                            <div class="col-sm-4 error-box" id="errorbox_myModals"></div>
                                            <div class="col-sm-4" >
                                                <button type="button" class="btn btn-primary pull-right" id="secondeditsubmit" ><?php echo BUTTON_SUBMIT; ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
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
                                    <div class="row">

                                        <div class="error-box" id="errorboxdata" style="font-size: large;text-align:center"><?php echo COMPAIGNS_DISPLAY; ?></div>

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
