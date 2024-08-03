<?php 

defined('BASEPATH') or exit('No direct script is allowed');

class Auth extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
	}


	public function index()
	{
		$this->load->view('auth/login');
	}

	public function login()
	{
		$post = $this->input->post();

		$ipAddress = $this->input->ip_address();
		$headers = $_SERVER;
		$headers['ip_address'] = $ipAddress;

		$this->form_validation->set_data($post);
		$this->form_validation->set_rules('username', 'Username', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required|trim');

		if ($this->form_validation->run() === false) {
			$status = 'failed';
			$this->_writeLog('AUTH_LOGIN', $status, $post, $headers);

			unset($post);
			$this->session->set_flashdata('failed', 'Username dan password wajib diisi!');
		}

		$username = $post['username'];
		$password = $post['password'];

		$this->db->where('username', $username);
		$this->db->where('password', $this->_encryptLogin($password));
		$user = $this->db->get('tb_user')->row();

		if ($user) {
			unset($user->password);
			unset($user->id_user);

			// var_dump($user);
			// return;

			$role = $this->db->select('role_name')->from('tb_roles')->where(['id_role' => $user->role])->get()->row();

			$roleName = $role->role_name;
			$user->role_name = $roleName;

			$profile = $this->db->select('name, photo')->from('tb_profile')->where(['username' => $user->username])->get()->row();
			$user->name = $profile->name;
			$user->photo = $profile->photo;

			if (isset($post['remember']) && $post['remember'] == true) {
				$this->session->set_tempdata('user', $user, 604800);
			} else {
				$this->session->set_userdata('user', $user);
			}

			if ($this->_isPIC(trim($user->username))) {
				$user->is_pic = true;
			} else {
				$user->is_pic = false;
			}

			$this->session->set_flashdata('success', 'Login Berhasil');

			// success login logging
			$status = 'success';
			$this->_writeLog('AUTH_LOGIN', $status, $post, $headers);

			redirect('home');
		} else {
			// failed login logging
			$status = 'failed';
			$this->_writeLog('AUTH_LOGIN', $status, $post, $headers);

			unset($post);
			$this->session->set_flashdata('failed', 'Login Gagal. Username atau password salah!');

			redirect('auth/index');
		}
	}

	private function _isPIC($username)
	{
		$area = $this->db->get_where('tb_master_area', ['pic_area' => $username])->row();

		if ($this->session->user->role == 1) {
			return true;
		}

		if ($area) {
			return true;
		}

		return false;
	}

	public function logout($username)
	{
		$cekSession = $this->session->user->username;
		if ($username == $cekSession) {
			$this->session->sess_destroy();
			$this->session->set_flashdata('success', 'Logout Berhasil');
			redirect('auth/index');
		} else {
			$this->session->set_flashdata('failed', 'Logout Gagal');
			redirect('auth/index');
		}
	}

}

?>
