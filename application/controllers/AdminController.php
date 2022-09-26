<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminController extends CI_Controller {

	public function __construct(){
		
        parent::__construct();

		$this->load->model("Reports");
		$this->load->model("Users");
		$this->load->library('mailer');
	}

	public function index(){
		if(!isset($_SESSION['user_id'])){
			redirect('/login');
		}
		else{
			redirect('/dashboard');
		}
	}

	public function login(){
		if($this->input->post('submit')){
		    
		    // for google recaptcha
    		$this->form_validation->set_rules('email', 'Email', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$this->load->view('auth/login');
			}
			else {
				$results = $this->Users->exist($this->input->post('email'), $this->input->post('password'));
				if(count($results)){
					$result = $results[0];
					if($result['is_verify'] == 0){
			    		$this->session->set_flashdata('warning', 'Please verify your email address!');
						redirect(base_url('/login'));
						exit;
			    	}
					if($result['is_active'] == 0){
			    		$this->session->set_flashdata('warning', 'Your account has been deactivated!');
						redirect(base_url('/login'));
						exit;
			    	}
				
					$_SESSION['user_id'] = $result['id'];
					$_SESSION['role'] = $result['role'];
					$_SESSION['username'] = $result['username'];

					redirect(base_url('/dashboard'), 'refresh');
				
				}
				else{
					$data['msg'] = 'Invalid Email or Password!';
					$this->load->view('auth/login', $data);
				}
			}
		}
		else{
			unset($_SESSION['user_id']);
			$this->load->view('auth/login');
		}
	}

	public function register(){
		if($this->input->post('submit')){
		                
			$this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[users.username]');
			$this->form_validation->set_rules('first_name', 'Firstname', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Lastname', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|is_unique[users.email]|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]');
			$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'trim|required|matches[password]');

			if ($this->form_validation->run() == FALSE) {
				$this->load->view('auth/register', array(
					'username' => $this->input->post('username'),
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'email' => $this->input->post('email')
				));
			}
			else{
				$data = array(
					'username' => $this->input->post('username'),
					'firstname' => $this->input->post('firstname'),
					'lastname' => $this->input->post('lastname'),
					'email' => $this->input->post('email'),
					'password' =>  password_hash($this->input->post('password'), PASSWORD_BCRYPT),
					'is_active' => 1,
					'is_verify' => 0,
					'token' => md5(rand(0,1000)),    
					'last_ip' => '',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				);
				$data = $this->security->xss_clean($data);
				// $result = $this->auth_model->register($data);
				$token = md5(rand(0,1000));
				$added_user_id = $this->Users->add(array(
					'username' => $this->input->post('username'), 
					'email' => $this->input->post('email'), 
					'password' => $this->input->post('password'),
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'token' => $token
				));

				if($added_user_id){
					//sending welcome email to user
					$result = $this->mailer->sendRegistrationMail(array(
						'username' => strtoupper($this->input->post('first_name').' '.$this->input->post('last_name')),
						'email_verification_link' => base_url('/verify/').$token,
						'to' => $this->input->post('email')
					));

					if($result['success']){
						$this->session->set_flashdata('success', 'Your Account has been made, please verify it by clicking the activation link that has been send to your email.');	
						redirect(base_url('/login'));
					}
					else{

					}

					// $name = $data['firstname'].' '.$data['lastname'];
					// $email_verification_link = base_url('auth/verify/').'/'.$data['token'];
					// $body = $this->mailer->Tpl_Registration($name, $email_verification_link);
					// $this->load->helper('email_helper');
					// $to = $data['email'];
					// $subject = 'Activate your account';
					// $message =  $body ;
					// $email = sendEmail($to, $subject, $message, $file = '' , $cc = '');
					// $email = true;

					// if($email){
					// 	$this->session->set_flashdata('success', 'Your Account has been made, please verify it by clicking the activation link that has been send to your email.');	
					// 	redirect(base_url('/login'));
					// }	
					// else{
					// 	echo 'Email Error';
					// }
				}
			}
		}
		else{
			$this->load->view('auth/register');
		}
	}

	public function forgotPassword(){
		
		if($this->input->post('submit')){
		                
			//checking server side validation
			$this->form_validation->set_rules('email', 'Email', 'valid_email|trim|required');
			if ($this->form_validation->run() === FALSE) {
				$this->load->view('auth/forgot-password');
				return;
			}
			$email = $this->input->post('email');
			$response = $this->Users->check_user_mail($email);
			if($response){
				$rand_no = rand(0,1000);
				$pwd_reset_code = md5($rand_no.$response['id']);
				$this->Users->update_reset_code($pwd_reset_code, $response['id']);

				$result = $this->mailer->sendResetPasswordMail(array(
					'username' => strtoupper($response['first_name'].' '.$response['last_name']),
					'reset_link' => base_url('/reset-password/').$pwd_reset_code,
					'to' => $response['email']
				));

				if($result['success']){
					$this->session->set_flashdata('success', 'We have sent instructions for resetting your password to your email');
					redirect(base_url('/forgot-password'));
				}
				else{
					$this->session->set_flashdata('error', 'There is the problem on your email');
					redirect(base_url('/forgot-password'));
				}
			}
			else{
				$this->session->set_flashdata('error', 'The Email that you provided are invalid');
				redirect(base_url('/forgot-password'));
			}
		}
		else{
			$this->load->view('auth/forgot-password');	
		}
	}

	public function resetPassword($pwd_reset_code){
		if($this->input->post('submit')){
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]');
			$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'trim|required|matches[password]');

			if ($this->form_validation->run() == FALSE) {
				$result = false;
				$data['reset_code'] = $pwd_reset_code;
				$this->load->view('auth/reset-password',$data);
			}   
			else{
				$this->Users->reset_password($pwd_reset_code, $this->input->post('password'));
				$this->session->set_flashdata('success','New password has been Updated successfully.Please login below');
				redirect(base_url('/login'));
			}
		}
		else{
			$result = $this->Users->check_password_reset_code($pwd_reset_code);
			if($result){
				$data['reset_code'] = $pwd_reset_code;
				$this->load->view('auth/reset-password',$data);
			}
			else{
				$this->session->set_flashdata('error','Password Reset Code is either invalid or expired.');
				redirect(base_url('/forgot-password'));
			}
		}
	}

	public function dashboard(){
		if(!isset($_SESSION['user_id'])){
			redirect(base_url('/login'));
		}

		$data = array();
		$data['studies'] = getAllStudies();
		$data['countries'] = getAllCountries();
		$data['reports'] = $this->Reports->load($_SESSION['user_id']);

		$this->load->view('admin/dashboard', $data);
	}

	public function verify($token){
		$result = $this->Users->email_verification($token);
		if($result){
			$this->session->set_flashdata('success', 'Your email has been verified, you can now login.');	
			redirect(base_url('/login'));
		}
		else{
			$this->session->set_flashdata('error', 'The url is either invalid or you already have activated your account.');	
			redirect(base_url('/login'));
		}	
	}

	public function profile(){
		if($this->input->post('submit')){
		                
			$this->form_validation->set_rules('username', 'Username', 'trim|required');
			$this->form_validation->set_rules('first_name', 'Firstname', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Lastname', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]');
			$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'trim|required|matches[password]');

			if ($this->form_validation->run() == FALSE) {
				$this->load->view('admin/profile', array(
					'username' => $this->input->post('username'),
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'email' => $this->input->post('email')
				));
			}
			else{
				$update_result = $this->Users->updateProfile(
					isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '', array(
					'username' => $this->input->post('username'), 
					'email' => $this->input->post('email'), 
					'password' => $this->input->post('password'),
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name')
				));

				if($update_result){
					$this->session->set_flashdata('success', 'Profile updated successfully.');	
					redirect(base_url('/profile'));
				}
				else{
					$this->session->set_flashdata('error', 'Profile updating failed.');	
					redirect(base_url('/profile'));
				}
			}
		}
		else{
			
			$users = $this->Users->getByID(isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '');

			if(count($users) > 0){
				$user = $users[0];
				$this->load->view('admin/profile', array(
					'username' => $user['username'],
					'first_name' => $user['first_name'],
					'last_name' => $user['last_name'],
					'email' => $user['email']
				));
			}
			else{
				redirect(base_url('/login'));
			}
		}
	}

	public function users(){

		$users = $this->Users->allUsers();

		$this->load->view('admin/users', array(
			'users' => $users
		));
	}

	public function userEdit($user_id){
		if(isset($_SESSION['role']) && $_SESSION['role'] != 'admin'){
			redirect(base_url('/'));
		}

		if($this->input->post('submit')){
			$this->form_validation->set_rules('username', 'Username', 'trim|required');
			$this->form_validation->set_rules('first_name', 'Firstname', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Lastname', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');

			if ($this->form_validation->run() == FALSE) {
				$this->load->view('admin/user-edit', array(
					'user_id' => $user_id,
					'username' => $this->input->post('username'),
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'email' => $this->input->post('email'),
					'is_verify' => $this->input->post('is_verify') ? '1' : '',
					'is_active' => $this->input->post('is_active') ? '1' : ''
				));
			}
			else{
				$update_result = $this->Users->updateUser(
					$user_id, array(
					'username' => $this->input->post('username'), 
					'email' => $this->input->post('email'), 
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'is_verify' => $this->input->post('is_verify') ? '1' : '',
					'is_active' => $this->input->post('is_active') ? '1' : ''
				));

				if($update_result){
					$this->session->set_flashdata('success', 'Updated successfully.');	
					redirect(base_url('/user-edit/'.$user_id));
				}
				else{
					$this->session->set_flashdata('error', 'Updated failed.');	
					redirect(base_url('/user-edit/'.$user_id));
				}
			}
		}
		else{
			$users = $this->Users->getByID($user_id);

			if(count($users) > 0){
				$user = $users[0];
				$this->load->view('admin/user-edit', array(
					'user_id' => $user_id,
					'username' => $user['username'],
					'first_name' => $user['first_name'],
					'last_name' => $user['last_name'],
					'email' => $user['email'],
					'is_verify' => $user['is_verify'],
					'is_active' => $user['is_active']
				));
			}
		}
	}

	public function userNew(){
		if($this->input->post('submit')){
			$this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[users.username]');
			$this->form_validation->set_rules('first_name', 'Firstname', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Lastname', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|is_unique[users.email]|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]');
			$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'trim|required|matches[password]');

			if ($this->form_validation->run() == FALSE) {
				$this->load->view('admin/user-new', array(
					'username' => $this->input->post('username'),
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'email' => $this->input->post('email'),
					'is_verify' => $this->input->post('is_verify') ? '1' : '',
					'is_active' => $this->input->post('is_active') ? '1' : ''
				));
			}
			else{
				
				$added_user_id = $this->Users->addNew(array(
					'username' => $this->input->post('username'), 
					'email' => $this->input->post('email'), 
					'password' => $this->input->post('password'),
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'is_verify' => $this->input->post('is_verify') ? '1' : '',
					'is_active' => $this->input->post('is_active') ? '1' : ''
				));

				if($added_user_id){
					$this->session->set_flashdata('success', 'Your Account has been made, please verify it by clicking the activation link that has been send to your email.');	
					redirect(base_url('/user-new'));
				}
			}
		}
		else{
			$this->load->view('admin/user-new');
		}
	}
}
