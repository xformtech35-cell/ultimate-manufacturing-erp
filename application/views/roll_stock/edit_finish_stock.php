<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Finish Goods
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Edit Finish Goods</a></li>
                    <li class="active">Edit Finish Goods Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Finish Goods Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>RollController/edit_finish_goods" enctype="multipart/form-data">
                                    <div class="card-body ">
                                        <!-- form start -->

                                        <div class="form-group row">

                                            <input type="hidden" class="form-control input-sm" name="id" id="id" value="<?php
                                            if (isset($finish) && !empty($finish)) {
                                                echo $finish['id'];
                                            }
                                            ?>">

                                            <label for="inputEmail3" class="col-sm-3 control-label">Item<span style="color: red;">*</span></label>
                                            <div class="col-sm-9" id="append_to_dropdown">
                                                <select class="form-control" name="code" id="code" required id="">
                                                        <option value="">Select Code</option>
                                                        <?php
                                                        $code = $finish['code'];
                                                        foreach ($codes1 as $row) {
                                                            ?>
                                                            <option value="<?php echo $row->code ?>"  
                                                            <?php
                                                            if ($code == $row->code) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?> ><?php echo $row->code . " - " . $row->item_name; ?></option>
                                                                <?php }
                                                                ?>
                                                    </select>  
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Roll Weight In KG<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control input-sm"  name="roll_weight" id="roll_weight" value="<?php
                                                if (isset($finish) && !empty($finish)) {
                                                    echo $finish['roll_weight'];
                                                }
                                                ?>" required="">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Roll Color</label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm" name="roll_color" id="roll_color">
                                                    <?php if ($finish['roll_color'] == Red) { ?>
                                                        <option value="Red" selected="">Red</option>
                                                        <option value="RGreen">R Green</option>
                                                        <option value="LYellow">L Yellow</option>
                                                        <option value="GYellow">G Yellow</option> 
                                                        <option value="PGreen">P Green</option>
                                                        <option value="White">White</option>
                                                        <option value="Ivory">Ivory</option>
                                                        <option value="Beige">Beige</option>
                                                        <option value="Orange">Orange</option> 
                                                        <option value="MedicalBlue">Medical Blue</option>
                                                        <option value="Violet">Violet</option>
                                                        <option value="Blue">Blue</option>
                                                        <option value="Black">Black</option>
                                                    <?php } ?>

                                                    <?php if ($finish['roll_color'] == RGreen) { ?>
                                                        <option value="Red">Red</option>
                                                        <option value="RGreen" selected="">R Green</option>
                                                        <option value="LYellow">L Yellow</option>
                                                        <option value="GYellow">G Yellow</option> 
                                                        <option value="PGreen">P Green</option>
                                                        <option value="White">White</option>
                                                        <option value="Ivory">Ivory</option>
                                                        <option value="Beige">Beige</option>
                                                        <option value="Orange">Orange</option> 
                                                        <option value="MedicalBlue">Medical Blue</option>
                                                        <option value="Violet">Violet</option>
                                                        <option value="Blue">Blue</option>
                                                        <option value="Black">Black</option>
                                                    <?php } ?>

                                                    <?php if ($finish['roll_color'] == LYellow) { ?>
                                                        <option value="Red">Red</option>
                                                        <option value="RGreen" >R Green</option>
                                                        <option value="LYellow" selected="">L Yellow</option>
                                                        <option value="GYellow">G Yellow</option> 
                                                        <option value="PGreen">P Green</option>
                                                        <option value="White">White</option>
                                                        <option value="Ivory">Ivory</option>
                                                        <option value="Beige">Beige</option>
                                                        <option value="Orange">Orange</option> 
                                                        <option value="MedicalBlue">Medical Blue</option>
                                                        <option value="Violet">Violet</option>
                                                        <option value="Blue">Blue</option>
                                                        <option value="Black">Black</option>
                                                    <?php } ?>

                                                    <?php if ($finish['roll_color'] == GYellow) { ?>
                                                        <option value="Red">Red</option>
                                                        <option value="RGreen">R Green</option>
                                                        <option value="LYellow">L Yellow</option>
                                                        <option value="GYellow"  selected="">G Yellow</option> 
                                                        <option value="PGreen">P Green</option>
                                                        <option value="White">White</option>
                                                        <option value="Ivory">Ivory</option>
                                                        <option value="Beige">Beige</option>
                                                        <option value="Orange">Orange</option> 
                                                        <option value="MedicalBlue">Medical Blue</option>
                                                        <option value="Violet">Violet</option>
                                                        <option value="Blue">Blue</option>
                                                        <option value="Black">Black</option>
                                                    <?php } ?>

                                                    <?php if ($finish['roll_color'] == PGreen) { ?>
                                                        <option value="Red">Red</option>
                                                        <option value="RGreen">R Green</option>
                                                        <option value="LYellow">L Yellow</option>
                                                        <option value="GYellow">G Yellow</option> 
                                                        <option value="PGreen"  selected="">P Green</option>
                                                        <option value="White">White</option>
                                                        <option value="Ivory">Ivory</option>
                                                        <option value="Beige">Beige</option>
                                                        <option value="Orange">Orange</option> 
                                                        <option value="MedicalBlue">Medical Blue</option>
                                                        <option value="Violet">Violet</option>
                                                        <option value="Blue">Blue</option>
                                                        <option value="Black">Black</option>
                                                    <?php } ?>

                                                    <?php if ($finish['roll_color'] == White) { ?>
                                                        <option value="Red">Red</option>
                                                        <option value="RGreen">R Green</option>
                                                        <option value="LYellow">L Yellow</option>
                                                        <option value="GYellow">G Yellow</option> 
                                                        <option value="PGreen">P Green</option>
                                                        <option value="White" selected="">White</option>
                                                        <option value="Ivory">Ivory</option>
                                                        <option value="Beige">Beige</option>
                                                        <option value="Orange">Orange</option> 
                                                        <option value="MedicalBlue">Medical Blue</option>
                                                        <option value="Violet">Violet</option>
                                                        <option value="Blue">Blue</option>
                                                        <option value="Black">Black</option>
                                                    <?php } ?>

                                                    <?php if ($finish['roll_color'] == Ivory) { ?>
                                                        <option value="Red">Red</option>
                                                        <option value="RGreen">R Green</option>
                                                        <option value="LYellow">L Yellow</option>
                                                        <option value="GYellow">G Yellow</option> 
                                                        <option value="PGreen">P Green</option>
                                                        <option value="White">White</option>
                                                        <option value="Ivory" selected="">Ivory</option>
                                                        <option value="Beige">Beige</option>
                                                        <option value="Orange">Orange</option> 
                                                        <option value="MedicalBlue">Medical Blue</option>
                                                        <option value="Violet">Violet</option>
                                                        <option value="Blue">Blue</option>
                                                        <option value="Black">Black</option>
                                                    <?php } ?>

                                                    <?php if ($finish['roll_color'] == Beige) { ?>
                                                        <option value="Red">Red</option>
                                                        <option value="RGreen">R Green</option>
                                                        <option value="LYellow">L Yellow</option>
                                                        <option value="GYellow">G Yellow</option> 
                                                        <option value="PGreen">P Green</option>
                                                        <option value="White">White</option>
                                                        <option value="Ivory">Ivory</option>
                                                        <option value="Beige" selected="">Beige</option>
                                                        <option value="Orange">Orange</option> 
                                                        <option value="MedicalBlue">Medical Blue</option>
                                                        <option value="Violet">Violet</option>
                                                        <option value="Blue">Blue</option>
                                                        <option value="Black">Black</option>
                                                    <?php } ?>

                                                    <?php if ($finish['roll_color'] == Orange) { ?>
                                                        <option value="Red">Red</option>
                                                        <option value="RGreen">R Green</option>
                                                        <option value="LYellow">L Yellow</option>
                                                        <option value="GYellow">G Yellow</option> 
                                                        <option value="PGreen">P Green</option>
                                                        <option value="White">White</option>
                                                        <option value="Ivory">Ivory</option>
                                                        <option value="Beige">Beige</option>
                                                        <option value="Orange" selected="">Orange</option> 
                                                        <option value="MedicalBlue">Medical Blue</option>
                                                        <option value="Violet">Violet</option>
                                                        <option value="Blue">Blue</option>
                                                        <option value="Black">Black</option>
                                                    <?php } ?>

                                                    <?php if ($finish['roll_color'] == MedicalsBlue) { ?>
                                                        <option value="Red">Red</option>
                                                        <option value="RGreen">R Green</option>
                                                        <option value="LYellow">L Yellow</option>
                                                        <option value="GYellow">G Yellow</option> 
                                                        <option value="PGreen">P Green</option>
                                                        <option value="White">White</option>
                                                        <option value="Ivory">Ivory</option>
                                                        <option value="Beige">Beige</option>
                                                        <option value="Orange">Orange</option> 
                                                        <option value="MedicalBlue" selected="">Medical Blue</option>
                                                        <option value="Violet">Violet</option>
                                                        <option value="Blue">Blue</option>
                                                        <option value="Black">Black</option>
                                                    <?php } ?>

                                                    <?php if ($finish['roll_color'] == Violet) { ?>
                                                        <option value="Red">Red</option>
                                                        <option value="RGreen">R Green</option>
                                                        <option value="LYellow">L Yellow</option>
                                                        <option value="GYellow">G Yellow</option> 
                                                        <option value="PGreen">P Green</option>
                                                        <option value="White">White</option>
                                                        <option value="Ivory">Ivory</option>
                                                        <option value="Beige">Beige</option>
                                                        <option value="Orange">Orange</option> 
                                                        <option value="MedicalBlue">Medical Blue</option>
                                                        <option value="Violet"  selected="">Violet</option>
                                                        <option value="Blue">Blue</option>
                                                        <option value="Black">Black</option>
                                                    <?php } ?>

                                                    <?php if ($finish['roll_color'] == Blue) { ?>
                                                        <option value="Red">Red</option>
                                                        <option value="RGreen">R Green</option>
                                                        <option value="LYellow">L Yellow</option>
                                                        <option value="GYellow">G Yellow</option> 
                                                        <option value="PGreen">P Green</option>
                                                        <option value="White">White</option>
                                                        <option value="Ivory">Ivory</option>
                                                        <option value="Beige">Beige</option>
                                                        <option value="Orange">Orange</option> 
                                                        <option value="MedicalBlue">Medical Blue</option>
                                                        <option value="Violet" >Violet</option>
                                                        <option value="Blue"  selected="">Blue</option>
                                                        <option value="Black">Black</option>
                                                    <?php } ?>

                                                    <?php if ($finish['roll_color'] == Black) { ?>
                                                        <option value="Red">Red</option>
                                                        <option value="RGreen">R Green</option>
                                                        <option value="LYellow">L Yellow</option>
                                                        <option value="GYellow">G Yellow</option> 
                                                        <option value="PGreen">P Green</option>
                                                        <option value="White">White</option>
                                                        <option value="Ivory">Ivory</option>
                                                        <option value="Beige">Beige</option>
                                                        <option value="Orange">Orange</option> 
                                                        <option value="MedicalBlue">Medical Blue</option>
                                                        <option value="Violet" >Violet</option>
                                                        <option value="Blue">Blue</option>
                                                        <option value="Black"  selected="">Black</option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">GSM<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm" name="gsm" id="gsm">
                                                    <?php if ($finish['gsm'] == 60) { ?>
                                                        <option value="60" selected="">60</option>
                                                        <option value="80">80</option>
                                                        <option value="100">100</option>
                                                        <option value="120">120</option> 
                                                    <?php } ?>
                                                    <?php if ($finish['gsm'] == 80) { ?>
                                                        <option value="60">60</option>
                                                        <option value="80" selected="">80</option>
                                                        <option value="100">100</option>
                                                        <option value="120">120</option> 
                                                    <?php } ?>
                                                    <?php if ($finish['gsm'] == 100) { ?>
                                                        <option value="60">60</option>
                                                        <option value="80">80</option>
                                                        <option value="100" selected="">100</option>
                                                        <option value="120">120</option> 
                                                    <?php } ?>
                                                    <?php if ($finish['gsm'] == 120) { ?>
                                                        <option value="60">60</option>
                                                        <option value="80">80</option>
                                                        <option value="100">100</option>
                                                        <option value="120" selected="">120</option> 
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Roll Size (Inch)<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm" name="roll_size" id="roll_size">
                                                    <?php if ($finish['roll_size'] == '25"') { ?>
                                                        <option value='25"' selected="">25"</option>
                                                        <option value='32"'>32"</option>
                                                        <option value='37"'>37"</option>
                                                        <option value='43"'>43"</option> 
                                                        <option value='47"'>47"</option>
                                                        <option value='63"'>63"</option>
                                                    <?php } ?>
                                                    <?php if ($finish['roll_size'] == '32"') { ?>
                                                        <option value='25"'>25"</option>
                                                        <option value='32"' selected="">32"</option>
                                                        <option value='37"'>37"</option>
                                                        <option value='43"'>43"</option> 
                                                        <option value='47"'>47"</option>
                                                        <option value='63"'>63"</option>
                                                    <?php } ?>
                                                    <?php if ($finish['roll_size'] == '37"') { ?>
                                                        <option value='25"'>25"</option>
                                                        <option value='32"'>32"</option>
                                                        <option value='37"' selected="">37"</option>
                                                        <option value='43"'>43"</option> 
                                                        <option value='47"'>47"</option>
                                                        <option value='63"'>63"</option>
                                                    <?php } ?>
                                                    <?php if ($finish['roll_size'] == '43"') { ?>
                                                        <option value='25"'>25"</option>
                                                        <option value='32"'>32"</option>
                                                        <option value='37"'>37"</option>
                                                        <option value='43"' selected="">43"</option> 
                                                        <option value='47"'>47"</option>
                                                        <option value='63"'>63"</option>
                                                    <?php } ?>
                                                    <?php if ($finish['roll_size'] == '47"') { ?>
                                                        <option value='25"'>25"</option>
                                                        <option value='32"'>32"</option>
                                                        <option value='37"'>37"</option>
                                                        <option value='43"'>43"</option> 
                                                        <option value='47"' selected="">47"</option>
                                                        <option value='63"'>63"</option>
                                                    <?php } ?>
                                                    <?php if ($finish['roll_size'] == '63"') { ?>
                                                        <option value='25"'>25"</option>
                                                        <option value='32"'>32"</option>
                                                        <option value='37"'>37"</option>
                                                        <option value='43"'>43"</option> 
                                                        <option value='47"'>47"</option>
                                                        <option value='63"' selected="">63"</option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Bags Created Qty (Numbers)<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="number" min="0" class="form-control input-sm number-only-validation"  name="bags_created" id="bags_created" value="<?php
                                                if (isset($finish) && !empty($finish)) {
                                                    echo $finish['bags_created'];
                                                }
                                                ?>" required="">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Bag Type<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm" name="bag_size" id="bag_size" required="">
                                                        <option value="Standard-D-Cut" <?php if ($finish['bag_type'] == 'Standard-D-Cut') { ?> selected="" <?php } ?>>Standard D-Cut</option>
                                                        <option value="Deluxe-D-Cut"<?php if ($finish['bag_type'] == 'Deluxe-D-Cut') { ?> selected="" <?php } ?>>Deluxe D-Cut</option>
                                                        <option value="Rice-Bags"<?php if ($finish['bag_type'] == 'Rice-Bags') { ?> selected="" <?php } ?>>Rice Bags</option>
                                                        <option value="Printing-Bags"<?php if ($finish['bag_type'] == 'Printing-Bags') { ?> selected="" <?php } ?>>Printing Bags</option>
                                                        <option value="Shopping Bags"<?php if ($finish['bag_type'] == 'Shopping Bags') { ?> selected="" <?php } ?>>Shopping Bags</option>
                                                        <option value="W-Cut Bags"<?php if ($finish['bag_type'] == 'W-Cut Bags') { ?> selected="" <?php } ?>>W-Cut Bags</option>
                                                        <option value="U-Cut Bags"<?php if ($finish['bag_type'] == 'U-Cut Bags') { ?> selected="" <?php } ?>>U-Cut Bags</option>
                                                        <option value="Wedding Bags"<?php if ($finish['bag_type'] == 'Wedding Bags') { ?> selected="" <?php } ?>>Wedding Bags</option>
                                                        <option value="Other"<?php if ($finish['bag_type'] == 'Other') { ?> selected="" <?php } ?>>Other</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Bag Size<span style="color: red;">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm" name="bag_size" id="bag_size">
                                                    <?php if ($finish['bag_size'] == '8x10') { ?>
                                                        <option value="8x10" selected="">8x10</option>
                                                        <option value="10x14">10x14</option>
                                                        <option value="12x16">12x16</option>
                                                        <option value="14x19">14x19</option> 
                                                        <option value="16x21">16x21</option>
                                                    <?php } ?>
                                                    <?php if ($finish['bag_size'] == '10x14') { ?>
                                                        <option value="8x10">8x10</option>
                                                        <option value="10x14" selected="">10x14</option>
                                                        <option value="12x16">12x16</option>
                                                        <option value="14x19">14x19</option> 
                                                        <option value="16x21">16x21</option>
                                                    <?php } ?>
                                                    <?php if ($finish['bag_size'] == '12x16') { ?>
                                                        <option value="8x10">8x10</option>
                                                        <option value="10x14">10x14</option>
                                                        <option value="12x16"  selected="">12x16</option>
                                                        <option value="14x19">14x19</option> 
                                                        <option value="16x21">16x21</option>
                                                    <?php } ?>
                                                    <?php if ($finish['bag_size'] == '14x19') { ?>
                                                        <option value="8x10">8x10</option>
                                                        <option value="10x14">10x14</option>
                                                        <option value="12x16">12x16</option>
                                                        <option value="14x19" selected="">14x19</option> 
                                                        <option value="16x21">16x21</option>
                                                    <?php } ?>
                                                    <?php if ($finish['bag_size'] == '16x21') { ?>
                                                        <option value="8x10">8x10</option>
                                                        <option value="10x14">10x14</option>
                                                        <option value="12x16">12x16</option>
                                                        <option value="14x19">14x19</option> 
                                                        <option value="16x21" selected="">16x21</option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-footer small text-muted">
                                        <button type="button" id="back" class="btn btn-default">Back</button>
                                        <button type="submit" class="btn btn-success pull-right">Submit</button>
                                    </div>
                                </form>


                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->
