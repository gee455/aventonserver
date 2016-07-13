<link rel="stylesheet" type="text/css" href="http://datatables.net/release-datatables/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="http://datatables.net/release-datatables/extensions/TableTools/css/dataTables.tableTools.css">

<script>
    function showMsgName() {
    $('#name').css('display', 'block');
    }
    function hideMsgName() {
        $('#name').css('display', 'none');
    }
     function showMsg() {
        $('#rout').css('display', 'block');

    }
    function hideMsg() {
        $('#rout').css('display', 'none');

    }
    
    function showMsgacc() {
        $('#acc').css('display', 'block');

    }
    function hideMsgacc() {
        $('#acc').css('display', 'none');

    }
    var btable;
      $(document).ready(function () {
            $('.banktable').DataTable( {
           "paging":   false,
           "ordering": false,
           "info":     false,
           "columnDefs": [
               {
                   "targets": [0],
                   "visible": false
               }
           ]
         });
          btable = $('.banktable').DataTable();
            $('#add_bank').click(function () {            
                $('#add_bank_window').modal('show');
            });
            
            LoadBank();
    });
    
function MakeDefaultRecipient(bid)
{
        $.ajax({
            url : '<?php echo base_url() ?>index.php/masteradmin/MakeDefaultRecipient',
            type : 'POST',
            data: {bid: bid},
            dataType : 'JSON',
            success : function(result){
                LoadBank();
                alert(result.message);
            },
            error : function(){
                alert("Error in Default Bank");
            }
        });
}
function LoadBank()
{
        $.ajax({
                url : '<?php echo base_url() ?>index.php/masteradmin/Bank_ajax',
                type : 'POST',
                dataType : 'JSON',
                success : function(Bank_Arr){
                        btable = $('.banktable').DataTable();
                        btable.clear().draw();
                        $.each(Bank_Arr, function (index, row) {
                                var d = new Date(row.created * 1000);
                                var cdate = d.getDate() + "-" + parseInt(d.getMonth()+1) + "-" + d.getFullYear();
                                var ctime = d.getHours() + ":" + d.getMinutes();
                                cdate = cdate + " " + ctime;
                                var rad;
                                if(row.default_stripe == 1)
                                    rad = "<input type='radio' onchange='MakeDefaultRecipient(&#39;"+row.bank_id+"&#39;)' name='def_bank' value='&#39;"+row.bank_id+"&#39;' checked='checked'>";
                                else
                                   rad =  "<input type='radio' onchange='MakeDefaultRecipient(&#39;"+row.bank_id+"&#39;)' name='def_bank' value='&#39;"+row.bank_id+"&#39;'>";    
                                
                                btable.row.add([
                                row.bank_id,
                                rad,  
                                row.name,
                                row.email,
                                row.bank_name,
                                row.routing_number,
                                row.country,
                                cdate,
                                row.description,
                                "<button class='btn btn-success btnstyle' type='button' onclick='DeleteBank(&#39;"+row.bank_id+"&#39;)'><i class='fa fa-trash-o'></i></button>"
                             ]).draw();
                        });
                },
                error : function(){
                    alert("Error in Loading Bank");
                }
            });
}
function DeleteBank(bid)
{
        $.ajax({
            url : '<?php echo base_url() ?>index.php/masteradmin/DeleteRecipient',
            type : 'POST',
            data: {bid: bid},
            dataType : 'JSON',
            success : function(result){
                LoadBank();
                alert(result.message);
            },
            error : function(){
                alert("Error in Deleting Bank");
            }
        });
}
function addbank()
{
    var bform = document.getElementById("bform");
    var bformdata = new FormData(bform);
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url() ?>index.php/masteradmin/AddRecipient',
        data: bformdata,
        cache: false,
        processData: false,
        contentType: false,
        async:false,
        success: function(result){
            var res = JSON.parse(result);
            alert(res.message);
            $('#add_bank_window').modal('hide');
            LoadBank();
        },
        error : function(){
            alert("Error in Adding Bank");
        }
        });
}
</script>

<style>
    .exportOptions{
        display: none;
    }
</style>
<div class="page-content-wrapper">
    <div class="content" style="padding-top:0px">
        <div class="jumbotron" data-pages="parallax">
            <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">
                <div class="inner">
                    <ul class="breadcrumb">
                        <li>
                            <p>DOCTOR</p>
                        </li>
                        <li><a href="#" class="active">Banking</a>
                        </li>
                    </ul>
                </div>

                <div class="container-fluid container-fixed-lg bg-white">
                    <div class="panel panel-transparent">
                        <div class="panel-heading">
                            <div class="row clearfix">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-2">
<!--                                    <div class="">
                                        <div class="pull-right"><input type="text" id="search-table" class="form-control pull-right" placeholder="Search by id"> </div>
                                    </div>-->
                                </div>
                                <div class="col-sm-2">
                                    <div class="">
                                        <div class="pull-right"><button id="add_bank" class="btn btn-primary" type="submit">Add Bank</button></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div id="tableWithSearch_wrapper" class="dataTables_wrapper form-inline no-footer">
                                <div class="table-responsive">
<!--                                    <table class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info">
                                        <thead>
                                            <tr role="row">
                                                <th class="sorting_asc" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column ascending" style="width: 68px;">SLNO</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 68px;">Name</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 150px;">email</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 80px;">Bank Name</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 70px;">Routing Number </th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 68px;">Country</th>
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 150px;">Created</th>                                                
                                                <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width:75px;">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $slno = 1;
                                            foreach ($Bank_Arr as $result) {
                                                ?>
                                                    <tr role="row"  class="gradeA odd">
                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $slno; ?></p></td>
                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result['name']; ?></p></td>
                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result['email']; ?></p></td>
                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result['bank_name']; ?></p></td>
                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result['routing_number']; ?></p></td> 
                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result['country']; ?></p></td>
                                                        <td class="v-align-middle"><?php echo date("M-d-Y H:i A",$result['created']); ?></td>
                                                        <td id = "d_no" class="v-align-middle sorting_1"> <p><?php echo $result['description']; ?></p></td>
                                                    </tr>
                                                <?php
                                                $slno++;
                                            }
//                                            
                                            ?>
                                        </tbody>
                                    </table>-->
                                    <table id="bank_table" class="table display banktable" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 275px;">Bank Id</th>
                                                <th style="width: 275px;">Default Bank</th>
                                                <th style="width: 275px;">Name</th>
                                                <th style="width: 275px;">Email</th>
                                                <th style="width: 275px;">Bank Name</th>
                                                <th style="width: 275px;">Routing Number</th>
                                                <th style="width: 275px;">Country</th>
                                                <th style="width: 275px;">Created</th>
                                                <th style="width: 275px;">Description</th>
                                                <th style="width: 275px;">Action</th>
                                             </tr>
                                        </thead>
                                    </table>
                                  <!-- end  -->
                                    
                                    
                                </div>
                                <div class="row">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid container-fixed-lg">
        </div>
    </div>
</div>




<!-- start model add bank-->
<div class="modal fade stick-up in" id="add_bank_window" tabindex="-1" role="dialog" aria-hidden="false" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header clearfix text-left">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                        </button>
                        <h5>Add Bank <span class="semi-bold"></span></h5>
                    </div>
                    <div class="modal-body">
                        <!--<form id="bform" class="" method="post" role="form" autocomplete="off" novalidate="novalidate"  enctype="multipart/form-data" action="AddRecipient">-->
                            <form id="bform" class="" method="post" role="form" autocomplete="off">
                                        <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label">Name</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="fname" placeholder="First Last, First Middle Last, or First M Last (no prefixes or suffixes)"  name="fdata[name]" required="" aria-required="true"  onfocus="showMsgName()"  onblur="hideMsgName()">
                                                <div class="icon" style="float: right;margin-top: -28px;margin-right: 12px;">
                                                    <img src="https://a.stripecdn.com/manage/assets/settings/transfers/account/info-5f252a77a8150ae4389ee5c3e9413c77.png" >
                                                </div>
                                            </div>
                                            <div class="bank-account-popover-view popover-view" style="left: 735px;top: 50px;display: none;"  id="name">
                                                <p class="explanation"><span class="type">name</span> should exactly match the account details with bank.</p>
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="form-group">
                                            <label for="position" class="col-sm-3 control-label">Mobile</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control " id="position" value="<?php echo $userData['mobile']; ?>" placeholder="Mobile" name="fdata[mobile]"  aria-required="true" aria-invalid="true">
                                             </div>
                                        </div>
                                         <br/>               
                                        <div class="form-group">
                                            <label for="name" class="col-sm-3 control-label">Email</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="position" value="<?php echo $userData['email']; ?>" placeholder="Email"  name="fdata[email]"   aria-required="true" aria-invalid="true">
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label">Tax ID</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="fname" placeholder="Tax ID, The Full SSN"  value="<?php echo $userData['tax_id']; ?>" name="fdata[tax_id]" required="" aria-required="true">
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="form-group">
                                            <label for="fname" class="col-sm-3 control-label">Bank Country</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="fdata[county]" >
                                                    <option value="AU">AU</option>
                                                    <option value="CA">CA</option>
                                                    <option value="GB">GB</option>
                                                    <option value="JP">JP</option>
                                                    <option value="US">US</option>

                                                </select>
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="form-group">
                                            <label for="position" class="col-sm-3 control-label">Routing Number</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control " id="position" value="" placeholder="Bank Routing Number" name="fdata[routing_number]"   onfocus="showMsg()"  onblur="hideMsg()" aria-required="true" aria-invalid="true">
                                                <div class="icon" style="float: right;margin-top: -28px;margin-right: 12px;">
                                                    <img src="https://a.stripecdn.com/manage/assets/settings/transfers/account/info-5f252a77a8150ae4389ee5c3e9413c77.png" >
                                                </div>
                                            </div>
                                            <div class="bank-account-popover-view popover-view" style="left: 735px;top: 298px;display: none;"  id="rout">
                                                <div class="check us-routing"></div>
                                                <p class="explanation"><span class="type">routing</span> number is normally found on a check provided by your bank.</p>
                                            </div>
                                        </div>
                                        <br/>
                                        
                                        <div class="form-group">
                                                <label for="name" class="col-sm-3 control-label">Account Number</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="position" value="" placeholder="Acc no"  name="fdata[account_number]"   onfocus="showMsgacc()"  onblur="hideMsgacc()" aria-required="true" aria-invalid="true">
                                                    <div class="icon" style="float: right;margin-top: -28px;margin-right: 12px;">
                                                        <img src="https://a.stripecdn.com/manage/assets/settings/transfers/account/info-5f252a77a8150ae4389ee5c3e9413c77.png" >
                                                    </div>
                                                </div>

                                                <div class="bank-account-popover-view popover-view" style="left: 735px;top: 401px;display: none;" id="acc">
                                                    <div class="check us-routing" style="background-image: url('https://a.stripecdn.com/manage/assets/settings/transfers/checks/us-account-4f0de4f5f3daf7ea0935de52251dc8b6.png')"></div>
                                                    <p class="explanation"><span class="type">account</span> number is normally found on a check provided by your bank.</p>
                                                </div>
                                            </div>
                                            <br/>
                                            <div class="form-group">
                                                <label for="name" class="col-sm-3 control-label">Confirm A/c</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="position" value="" placeholder="Confirm Acc No"  name="fdata[caccount_number]" aria-required="true" aria-invalid="true">
                                                    <div class="icon" style="float: right;margin-top: -28px;margin-right: 12px;">
                                                        <img src="https://a.stripecdn.com/manage/assets/settings/transfers/account/info-5f252a77a8150ae4389ee5c3e9413c77.png" >
                                                    </div>
                                                </div>
                                            </div>
                                          <br/>  
                                    <div class="row">
                                        <div class="col-sm-4 m-t-10 sm-m-t-10">
                                            <span id="error_message1" style="color:red;"></span><br/>
                                            <button type="button" class="btn btn-default btn-clean" data-dismiss="modal" id="close_slots">Close</button>
                                            <button onclick="addbank()" type="button" class="btn btn-success btn-clean" id="add_bank">Add</button>
                                        </div>
                                    </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>