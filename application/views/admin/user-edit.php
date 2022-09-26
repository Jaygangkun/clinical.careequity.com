<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link rel="icon" type="image/png" href="<?= base_url() ?>assets/img/favicon.png">
		<title>Edit User</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/materialize.css">
		<link rel="stylesheet" href="<?= base_url() ?>assets/css/sweetalert.css">
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/style.css?v=<?php echo time() ?>">
        
		<script type="text/javascript">
        var base_url = "<?php echo base_url()?>";
        </script>
	</head>
	<body>
	<?php 
	if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){
		include('nav-admin.php');
	}
	else{
		include('nav-user.php');
	}
	?>
	<div class="profile-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 col-lg-offset-2">
					<?php if(isset($msg) || validation_errors() !== ''): ?>
						<div class="alert alert-warning alert-dismissible">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4><i class="icon fa fa-warning"></i> Alert!</h4>
							<?= validation_errors();?>
							<?= isset($msg)? $msg: ''; ?>
						</div>
					<?php endif; ?> 
					<?php if($this->session->flashdata('success')): ?>
						<div class="alert alert-success">
							<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
							<?=$this->session->flashdata('success')?>
						</div>
					<?php endif; ?>
					<?php if($this->session->flashdata('warning')): ?>
						<div class="alert alert-warning">
						<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
						<?=$this->session->flashdata('warning')?>
						</div>
					<?php endif; ?>
					<?php if($this->session->flashdata('error')): ?>
						<div class="alert alert-danger">
						<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
						<?=$this->session->flashdata('error')?>
						</div>
					<?php endif; ?>
					<div class="card">
						<div class="body">
							<?php echo form_open(base_url('/user-edit/'.$user_id), 'class="profile-form" '); ?>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">person</i>
									</span>
									<div class="form-line">
										<input type="text" class="form-control" name="username" placeholder="Username" required autofocus value="<?php echo isset($username) ? $username: "" ?>">
									</div>
								</div>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">person</i>
									</span>
									<div class="form-line">
										<input type="text" class="form-control" name="first_name" placeholder="First Name" required autofocus value="<?php echo isset($first_name) ? $first_name: "" ?>">
									</div>
								</div>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">person</i>
									</span>
									<div class="form-line">
										<input type="text" class="form-control" name="last_name" placeholder="Last Name" required autofocus value="<?php echo isset($last_name) ? $last_name: "" ?>">
									</div>
								</div>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">email</i>
									</span>
									<div class="form-line">
										<input type="text" class="form-control" name="email" placeholder="email" required autofocus value="<?php echo isset($email) ? $email: "" ?>">
									</div>
								</div>
								<div class="input-group">
									<input type="checkbox" id="is_verify" name="is_verify" class="filled-in chk-col-green" <?php echo isset($is_verify) && $is_verify == '1' ? "checked" : "" ?> />
									<label for="is_verify">Verify</label>
								</div>
								<div class="input-group">
									<input type="checkbox" id="is_active" name="is_active" class="filled-in chk-col-green" <?php echo isset($is_active) && $is_active == '1' ? "checked" : "" ?> />
									<label for="is_active">Active</label>
								</div>
								<div class="row">
									<div class="col-xs-12 login-wrap">
										<input type="submit" name="submit" class="btn btn-block btn-success waves-effect" value="Update">
									</div>
								</div>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

    </body>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>assets/js/sweetalert.min.js"></script>
    <script src="<?= base_url() ?>assets/js/app.js?v=<?php echo time()?>"></script>
</html>