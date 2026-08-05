<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<style type="text/css">
    @media print {
        #non-printable {
            display: none;
        }

        #printable {
            display: block;
        }
    }
</style>

</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Barcode
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Generate Barcode</a></li>
                    <li class="active">Generate Barcode</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Generate Barcode</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <form class="form-horizontal" method="POST" id="barcodeForm">
                                    <div class="card-body ">

                                        <div class="container-fluid">
                                            <div class="row">

                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Item</label>
                                                        <input type="text" readonly="" class="form-control input-sm"
                                                            name="item_name" id="item_name" value="<?php
                                                                                                    if (isset($inventory) && !empty($inventory)) {
                                                                                                        echo $inventory['code'];
                                                                                                    }
                                                                                                    ?>" required="">
                                                    </div>
                                                </div>


                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Barcode</label>
                                                        <input type="text" class="form-control input-sm" name="barcode" id="barcode"
                                                            value="<?php
                                                                    // Initialize with default value
                                                                    $default_barcode = "1";

                                                                    // Check if $barcode_data exists
                                                                    if (!empty($barcode_data) && isset($barcode_data['barcode'])) {
                                                                        $current_barcode = $barcode_data['barcode'];

                                                                        // Handle numeric barcodes
                                                                        if (is_numeric($current_barcode)) {
                                                                            echo (int)$current_barcode + 1;
                                                                        }
                                                                        // Handle alphanumeric barcodes
                                                                        elseif (is_string($current_barcode) && $current_barcode !== '') {
                                                                            // Extract the last number and increment
                                                                            if (preg_match('/\d+$/', $current_barcode, $matches)) {
                                                                                $number = (int)$matches[0];
                                                                                $prefix = substr($current_barcode, 0, strlen($current_barcode) - strlen($matches[0]));
                                                                                echo $prefix . ($number + 1);
                                                                            } else {
                                                                                // If no number at the end, append "2"
                                                                                echo $current_barcode . '2';
                                                                            }
                                                                        }
                                                                        // Fallback to default
                                                                        else {
                                                                            echo $default_barcode;
                                                                        }
                                                                    } else {
                                                                        // Use default if $barcode_data doesn't exist
                                                                        echo $default_barcode;
                                                                    }
                                                                    ?>"
                                                            required="">
                                                    </div>
                                                </div>



                                            </div>
                                            <div class="row hide">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Code Type</label>
                                                        <select name="type" id="type" class="form-control">
                                                            <option value="codebar">Codebar</option>
                                                            <option value="code128" selected="selected">Code128</option>
                                                            <option value="code2of5">Code2of5</option>
                                                            <option value="code39">Code39</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Orientation</label>
                                                        <select name="orientation" class="form-control" required>
                                                            <option value="horizontal" selected="selected">Horizontal
                                                            </option>
                                                            <option value="vertical">Vertical</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Size</label>
                                                        <input type="number" name="size" id="size" class="form-control"
                                                            min="10" max="400" step="10" value="20" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Print</label>
                                                        <select name="print" id="print" class="form-control" required>
                                                            <option value="true" selected="selected">True</option>
                                                            <option value="false">False</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Qty</label>
                                                        <input type="number" name="qty" id="qty" class="form-control"
                                                            value="10" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row text-center">
                                                <div class="col-md-12">
                                                    <input type="button" name="submit" class="btn btn-success"
                                                        id="generate_barcode" value="Generate Barcode"
                                                        style="font-size: 14px; padding: 5px 15px; margin: 5px; border-radius: 5px;">

                                                    <button class="btn btn-warning"
                                                        onclick="printDiv('append_bar_code')"
                                                        style="font-size: 14px; padding: 5px 15px; margin: 5px; border-radius: 5px; width: auto;">Print
                                                        A4 Barcode</button>

                                                    <button class="btn btn-warning"
                                                        onclick="printThermal('append_bar_code')"
                                                        style="font-size: 14px; padding: 5px 15px; margin: 5px; border-radius: 5px; width: auto;">Print
                                                        Thermal Barcode</button>

                                                    <script>
                                                        function printThermal(divName) {
                                                            var printContents = document.getElementById(divName).innerHTML;
                                                            var originalContents = document.body.innerHTML;

                                                            // Thermal print style (fits 80mm paper size with 72mm width content)
                                                            var thermalStyle = `
        <style>
            @page {
                size: 80mm auto; /* Thermal paper size: 80mm width, auto height */
                margin: 0; /* No extra margin */
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 12px; /* Adjust for better readability */
                margin: 0;
                padding: 0;
                overflow: visible;
                height: auto;
                width: 100%;
                line-height: 1.4;
                writing-mode: vertical-lr; /* Ensure vertical text flow from top to bottom */
                text-align: left; /* Align the content to the left */
                height: 100%;
                overflow-y: auto; /* Allow vertical scrolling */
            }
            .receipt {
                width: 72mm; /* Ensure content fits within the thermal paper (72mm) */
                margin: 0;
                padding: 0;
                overflow: visible;
                white-space: nowrap; /* Prevent wrapping */
                word-break: normal; /* Prevent words from breaking */
                display: block;
                text-align: left;
                font-size: 12px; /* Adjust text size for thermal paper */
            }
            .thermal-print {
                display: block;
                text-align: left;
                padding: 0;
                margin: 0;
                white-space: nowrap; /* Prevent wrapping */
                word-break: normal;
            }
            .thermal-print p, .thermal-print h1, .thermal-print h2, .thermal-print h3, .thermal-print ul, .thermal-print ol {
                margin: 0;
                padding: 0;
                text-align: left;
                display: block;
            }
            .thermal-print ul {
                list-style-type: none;
                padding-left: 0;
            }
            .thermal-print li {
                margin-bottom: 5px; /* Space between list items */
            }
        </style>
    `;

                                                            // Set the body content for thermal printing
                                                            document.body.innerHTML = thermalStyle + `<div class="receipt thermal-print">${printContents}</div>`;

                                                            // Trigger the print
                                                            window.print();

                                                            // Restore the original content after printing
                                                            document.body.innerHTML = originalContents;
                                                        }
                                                    </script>


                                                </div>




                                            </div>
                                        </div>


                                    </div>

                            </div>
                            </form>
                        </div>

                        <div id='append_bar_code'>

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

    <script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>
    <!-- ./wrapper -->
    <?php
    //    if(isset($_POST['submit'])) {
    //       $string = trim($_POST['string']);
    //       $type=$_POST['type'];
    //       $orientation=$_POST['orientation'];
    //       $size=$_POST['size'];
    //       $print=$_POST['print'];
    //      
    //       if($string != '') {
    //          echo '<h5>Generated Barcode</h5>';
    //         
    //         //echo '<img alt="Coding Sips" src="'.$path.'?codetype=Code39&size=40&text=Coding-sips-item-no-786&print=true" />';
    //          echo '<img class="barcode" alt="'.$string.'" src="http://192.168.0.z/scann/barcode.php?text='.$string.'&codetype='.$type.'&orientation='.$orientation.'&size='.$size.'&print='.$print.'"/>';
    //        
    //          
    //       } else {
    //           echo 'Please enter a string!';
    //       }
    //    }
    ?>