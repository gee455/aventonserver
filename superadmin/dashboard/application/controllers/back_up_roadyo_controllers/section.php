<script>

    $(document).ready(function() {

        $('.edit_class').click(function() {
//                alert(1);
            var Section_List = $(this).closest("tr").find(".Section_List").text();
            var Amount = $(this).closest("tr").find(".Amount").text();
            var SectionId = $(this).closest("tr").find(".SectionId").text();

//                alert(SectionId);
            $("#SectionTitle1").val(Section_List);
            $("#Amount1").val(Amount);
            $("#SectionId").val(SectionId);
            $('#modal-container-1866991').modal("show");
            $('#idoftax').val($(this).val());
//                alert(2);

        });
    });

    function delete1(val) {
//    alert("hi"+val.value);
//        alert(1);
        $('#modal-container-18669912').modal("show");
        $('#idoftaxtodelete').val(val.value);
    }

    function addNew() {
        $('#modal-container-186699').modal("show");
    }

    function editSection() {
        $('#modal-container-1866991').modal("show");
    }
</script>



<div class="page-content-wrapper">
    <!-- START PAGE CONTENT -->
    <div class="content">
        <!-- START JUMBOTRON -->


        <div class="panel panel-transparent ">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-tabs-fillup">
                <li class="active">
                    <a data-toggle="tab" href="#slide1"><span>Home</span></a>
                </li>
                <li class="">
                    <a data-toggle="tab" href="#slide2"><span>Profile</span></a>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane slide-left active" id="slide1">
                    <div class="jumbotron bg-white" data-pages="parallax">
                        <div class="inner">
                            <ul class="nav navbar-nav navbar-right" style="margin-right: 1%;">
                                <div class="navbar-form navbar-right">
                                    <input type="button" class="btn btn-success margin" onclick="addNew()" value="Create New">
                                </div>
                            </ul>
                            <!-- END BREADCRUMB -->
                        </div>
                        <form method="post" enctype="multipart/form-data" action="" class="" id="">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-primary">
                                        <div class="box-header">
                                            <h3 class="box-title" style="margin-left:3%"><b>SECTION LIST </b></h3><hr>
                                        </div>
                                        <!-- /.box-header -->

                                        <div class="table-responsive panel-collapse pull out">
                                            <table class="table table-bordered table-hover" id="table1" style="  margin-left: 1%;" align="center">
                                                <thead>
                                                <tr>
                                                    <th>Section List</th>
                                                    <th >Option's</th>
                                                    <th style="display:none;">SectionId</th>
                                                    <!--<th width="20%"></th>-->
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <?php
                                                    foreach($dashborddata as $data){
                                                    ?>
                                                    <td class="Section_List"><?php echo $data->SectionTitle ?></td>
                                                    <td ><button type="button" class="edit_class" value="<?php echo $data->SectionId ?> " onclick="editSection()"> <i class="fa fa-edit"></i>Edit</button>
                                                        <button type="button" value="<?php echo $data->SectionId ?> " onclick="delete1(this)"><i class="fa fa-trash-o"></i>Delete</button></td>
                                                    <td style="display:none;" class="SectionId"><?php echo $data->SectionId ?></td>
                                                    <!--<th width="20%"></th>-->
                                                </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- END PANEL -->
                    </div>




                    <section id="main" role="main">
                        <!-- START CONTAINER FLUID -->
                        <div class="container-fluid container-fixed-lg">
                            <!-- BEGIN PlACE PAGE CONTENT HERE -->
                            <!-- START row -->
                            <div class="row">

                                <div class="modal fade in" id="modal-container-1866991" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="">
                                    <div class="modal-dialog">

                                        <form method="post" enctype="multipart/form-data" action="http://menuse.net/Menuse_Admin/index.php/admin/section/updateSectionDetails">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title">Add Tax</h4>
                                                </div>

                                                <div class="modal-body">

                                                    <input type="hidden" id="idoftax" value="" name="id">

                                                    <div class="form-group" >
                                                        <label>Section Title</label>
                                                        <input type="text" class="form-control" placeholder="Section Title"  name="SectionTitle" id="SectionTitle1">
                                                    </div>
                                                    <div class="form-group" >
                                                        <input type="hidden" class="form-control" placeholder="Section Title"  name="SectionId" id="SectionId">
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <input type="submit" class="btn btn-success" value="value">
                                                </div>
                                                <!--</form>-->
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="modal fade in" id="modal-container-18669912" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="">
                                    <div class="modal-dialog">
                                        <form method="post" enctype="multipart/form-data" action="http://menuse.net/Menuse_Admin/index.php/admin/section/delete">

                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title">Add data</h4>
                                                </div>

                                                <div class="modal-body">
                                                    <label>Are you sure to delete ?</label>

                                                    <input type="hidden" id="idoftaxtodelete" value="" name="SectionId">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                    <input type="submit" class="btn btn-success" value="Yes">

                                                </div>
                                                <!--                            --><?php //echo form_close() ?>
                                                <!--</form>-->
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- END PLACE PAGE CONTENT HERE -->
                        </div>
                        <!-- END CONTAINER FLUID -->
                    </section>
                </div>



                <div class="tab-pane slide-left" id="slide2">
                    <div class="row">

                    </div>
                </div>


            </div>
        </div>









    </div>
    <!-- END JUMBOTRON -->

    <!-- START Template Main -->


</div>
<div class="modal fade in" id="modal-container-186699" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
    <div class="modal-dialog">
        <form method="post" enctype="multipart/form-data" action="http://menuse.net/Menuse_Admin/index.php/admin/section/insertintoSection">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Add Tax</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group" >
                        <label>Section Title: </label>
                        <input type="text" class="form-control" placeholder="Section Title"  name="SectionTitle" id="SectionTitle">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-success" value="Add">
                </div>
                <!--</form>-->
            </div>
        </form>
    </div>
</div>