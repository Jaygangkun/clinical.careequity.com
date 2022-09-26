<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link rel="icon" type="image/png" href="<?= base_url() ?>assets/img/favicon.png">
		<title>Profile - Care Equity Clinical Tool</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/materialize.css">
		<link rel="stylesheet" href="<?= base_url() ?>assets/css/dataTables.bootstrap.min.css">
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
	<div class="users-area">
		<div class="container">
			<div class="card">
				<div class="header">
					<a class="btn btn-success" href="<?php echo base_url('/user-new')?>">Create User</a>
				</div>
				<div class="body">
					<div class="table-responsive">
						<table id="users_table" class="table table-bordered table-striped table-hover dataTable">
							<thead>
							<tr>
								<th>#</th>
								<th>Username</th>
								<th>Email</th>
								<th>Name</th> 
								<th>Verify</th> 
								<th>Active</th> 
								<th>Role</th> 
								<th>Action</th> 
							</tr>
							</thead>
							<tbody>
								<?php
								$index = 1;
								foreach($users as $user){
									?>
									<tr>
										<td><?php echo $index?></td>
										<td><?php echo $user['username']?></td>
										<td><?php echo $user['email']?></td>
										<td><?php echo $user['first_name'].' '.$user['last_name']?></td>
										<td><?php echo $user['is_verify'] == '1' ? '<span class="btn bg-green">Yes</span>' : '<span class="btn bg-red">No</span>'?></td>
										<td><?php echo $user['is_active'] == '1' ? '<span class="btn bg-green">Yes</span>' : '<span class="btn bg-red">No</span>'?></td>
										<td><?php echo $user['role'] == 'admin' ? 'Admin' : 'User'?></td>
										<td>
											<a class="btn btn-success btn-user-edit" href="<?php echo base_url('/user-edit/').$user['id']?>" user-id="<?php echo $user['id']?>">Edit</a>
											<span class="btn btn-danger btn-user-delete" user-id="<?php echo $user['id']?>">Delete</span>
										</td>
									</tr>
									<?php
									$index++;
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>#</th>
									<th>Username</th>
									<th>Email</th>
									<th>Name</th> 
									<th>Verify</th> 
									<th>Active</th> 
									<th>Role</th> 
									<th>Action</th> 
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

    </body>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.dataTables.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>assets/js/dataTables.bootstrap.min.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>assets/js/sweetalert.min.js"></script>
    <script src="<?= base_url() ?>assets/js/app.js?v=<?php echo time()?>"></script>
	<script>
		var users_table = $('#users_table').DataTable({
			responsive: true
		});
	</script>
</html>