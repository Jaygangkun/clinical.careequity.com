<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>ViewMyLicense Dashboard</title>
		<!-- Tell the browser to be responsive to screen width -->
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<!-- Bootstrap 3.3.6 -->
		<link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap/bootstrap.min.css">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
		<!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- DataTables -->
		<link rel="stylesheet" href="<?= base_url() ?>assets/css/datatables/dataTables.bootstrap.css">
		<!-- bootstrap datepicker -->
		<link rel="stylesheet" href="<?= base_url() ?>assets/css/datepicker/datepicker3.css">

		<!-- Theme style -->
		<link rel="stylesheet" href="<?= base_url() ?>assets/css/AdminLTE.min.css">
		<!-- AdminLTE Skins. Choose a skin from the css/skins
			folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/_all-skins.min.css">
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/vml.css">
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->


        <!-- jQuery 2.2.3 -->
		<script src="<?= base_url() ?>assets/js/jQuery/jquery-2.2.3.min.js"></script>
		<!-- Bootstrap 3.3.6 -->
        <script src="<?= base_url() ?>assets/js/bootstrap/bootstrap.min.js"></script>
        <!-- DataTables -->
        <script src="<?= base_url() ?>assets/js/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= base_url() ?>assets/js/datatables/dataTables.bootstrap.min.js"></script>

		<!-- bootstrap datepicker -->
		<script src="<?= base_url() ?>assets/js/datepicker/bootstrap-datepicker.js"></script>

		<!-- FastClick -->
		<!-- <script src="../../plugins/fastclick/fastclick.js"></script> -->
		<!-- AdminLTE App -->
		<script src="<?= base_url() ?>assets/js/app.min.js"></script>
		<!-- AdminLTE for demo purposes -->
		<script src="<?= base_url() ?>assets/js/demo.js"></script>

		<script src="<?= base_url() ?>assets/js/vml.js"></script>
	</head>
	<body>
        <div class="container mt-20">
            <div class="box box-success customer-info-box">
                <div class="customer-logo-wrap">
                    <?php
                    if($customer['logo'] != ''){
                        ?>
                        <img class="customer-logo-img" src="<?php echo $customer['logo']?>">
                        <?php
                    }
                    else{
                        ?>
                        <span>No Logo</span>
                        <?php
                    }
                    ?>
                </div>
                <div class="customer-info-box">
                    <div class="customer-info-row">
                        <div class="customer-info-title">Customer Business Name:</div>
                        <div class="customer-info-value"><?php echo $customer['business_name']?></div>
                    </div>
                    <div class="customer-info-row">
                        <div class="customer-info-title">Customer Business Address:</div>
                        <div class="customer-info-value"><?php echo $customer['business_address']?></div>
                    </div>
                    <div class="customer-info-row">
                        <div class="customer-info-title">Customer Business City:</div>
                        <div class="customer-info-value"><?php echo $customer['business_city']?></div>
                    </div>
                    <div class="customer-info-row">
                        <div class="customer-info-title">Customer Business State:</div>
                        <div class="customer-info-value"><?php echo $customer['business_state']?></div>
                    </div>
                    <div class="customer-info-row">
                        <div class="customer-info-title">Customer Business Zip Code:</div>
                        <div class="customer-info-value"><?php echo $customer['business_zip']?></div>
                    </div>
                    <div class="customer-info-row">
                        <div class="customer-info-title">Customer Business Phone:</div>
                        <div class="customer-info-value"><?php echo $customer['business_phone']?></div>
                    </div>
                    <div class="customer-info-row">
                        <div class="customer-info-title">Customer Business Email:</div>
                        <div class="customer-info-value"><?php echo $customer['business_email']?></div>
                    </div>
                    <div class="customer-info-row">
                        <div class="customer-info-title">Customer Website:</div>
                        <div class="customer-info-value"><?php echo $customer['website']?></div>
                    </div>
                    <div class="customer-info-row">
                        <div class="customer-info-title">Contact Name:</div>
                        <div class="customer-info-value"><?php echo $customer['contact_name']?></div>
                    </div>
                    <div class="customer-info-row">
                        <div class="customer-info-title">Contact Email:</div>
                        <div class="customer-info-value"><?php echo $customer['contact_email']?></div>
                    </div>
                    <div class="customer-info-row">
                        <div class="customer-info-title">Contact Phone:</div>
                        <div class="customer-info-value"><?php echo $customer['contact_phone']?></div>
                    </div>
                    <div class="customer-info-row">
                        <div class="customer-info-title">Start Date:</div>
                        <div class="customer-info-value"><?php echo $customer['start_date']?></div>
                    </div>
                    <div class="customer-info-row">
                        <div class="customer-info-title">End Date:</div>
                        <div class="customer-info-value"><?php echo $customer['end_date']?></div>
                    </div>
                </div>

                <div class="customer-documents-list">
                    <?php
                    $doc_files = json_decode($customer['documents'], true);
                    if(count($doc_files) > 0){
                        for($index = 0; $index < count($doc_files); $index++){
                            $doc_file = $doc_files[$index];
                            ?>
                            <div class="customer-document-wrap">
                                <a class="customer-document-link" href="<?php echo $doc_file['path']?>" target="_blank"><?php echo $doc_file['label']?></a>
                            </div>
                            <?php
                        }
                    }
                    else{
                        ?>
                        <span class="">No Documents</span>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
	</body>
</html>