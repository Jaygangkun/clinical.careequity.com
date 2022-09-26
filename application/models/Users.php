<?php 

if (!defined('BASEPATH'))
	exit('No direct script access allowed');
	
Class Users extends CI_Model
{
	
	public function exist($email, $password){
		$query = "SELECT * FROM users WHERE email='".$email."' AND password=PASSWORD('".$password."')";
		$query_result = $this->db->query($query)->result_array();
		
		return $query_result;
	}

	public function add($data){
		$query = "INSERT INTO users(`email`, `password`, `username`, `first_name`, `last_name`, `is_active`, `token`, `role`) VALUES('".$data['email']."', PASSWORD('".$data['password']."'), '".$data['username']."', '".$data['first_name']."', '".$data['last_name']."', '1', '".$data['token']."', 'user')";
		$query_result = $this->db->query($query);
		
		return $this->db->insert_id();
	}

	public function getByID($id){
		$query = "SELECT * FROM users WHERE id='".$id."'";
		$query_result = $this->db->query($query)->result_array();
		
		return $query_result;
	}

	public function email_verification($token){
		$this->db->select('email, token, is_active');
		$this->db->from('users');
		$this->db->where('token', $token);
		$query = $this->db->get();
		$result= $query->result_array();
		$match = count($result);
		if($match > 0){
			$this->db->where('token', $token);
			$this->db->update('users', array('is_verify' => 1, 'token'=> ''));
			return true;
		}
		else{
			return false;
		  }
	}

	function check_user_mail($email)
	{
		$result = $this->db->get_where('users', array('email' => $email));

		if($result->num_rows() > 0){
			$result = $result->row_array();
			return $result;
		}
		else {
			return false;
		}
	}

	public function update_reset_code($reset_code, $user_id){
		$data = array('password_reset_code' => $reset_code);
		$this->db->where('id', $user_id);
		$this->db->update('users', $data);
	}

	public function check_password_reset_code($code){

		$result = $this->db->get_where('users',  array('password_reset_code' => $code ));
		if($result->num_rows() > 0){
			return true;
		}
		else{
			return false;
		}
	}

	public function reset_password($pwd_reset_code, $new_password){
		$query = "UPDATE users SET password_reset_code='', password=PASSWORD('".$new_password."') WHERE password_reset_code='".$pwd_reset_code."'";
		$this->db->query($query);
		return true;
	}

	public function updateProfile($user_id, $data){
		$query = "UPDATE users SET username='".$data['username']."', first_name='".$data['first_name']."', last_name='".$data['last_name']."', email='".$data['email']."', password=PASSWORD('".$data['password']."') WHERE id='".$user_id."'";
		if($this->db->query($query)){
			return true;
		}		
		return false;
	}

	public function updateUser($user_id, $data){
		$query = "UPDATE users SET username='".$data['username']."', first_name='".$data['first_name']."', last_name='".$data['last_name']."', email='".$data['email']."', is_verify='".$data['is_verify']."', is_active='".$data['is_active']."' WHERE id='".$user_id."'";
		if($this->db->query($query)){
			return true;
		}		
		return false;
	}

	public function allUsers(){
		$query = "SELECT * FROM users ";
		$query_result = $this->db->query($query)->result_array();
		
		return $query_result;
	}

	public function deleteByID($id){
		$query = "DELETE FROM users WHERE id='".$id."'";
		return $this->db->query($query);
	}

	public function addNew($data){
		$query = "INSERT INTO users(`email`, `password`, `username`, `first_name`, `last_name`, `is_active`, `is_verify`, `role`) VALUES('".$data['email']."', PASSWORD('".$data['password']."'), '".$data['username']."', '".$data['first_name']."', '".$data['last_name']."', '".$data['is_active']."', '".$data['is_verify']."', 'user')";
		$query_result = $this->db->query($query);
		
		return $this->db->insert_id();
	}
}