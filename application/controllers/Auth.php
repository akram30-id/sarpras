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
		$headers = getallheaders();
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

			$role = $this->db->select('role_name')->from('tb_roles')->where(['id_role' => $user->role])->get()->row();
			$roleName = $role->role_name;
			$user->role_name = $roleName;

			$profile = $this->db->select('name, photo')->from('tb_profile')->where(['username' => $user->username])->get()->row();
			$user->name = $profile->name;
			$user->photo = $profile->photo;

			$this->session->set_userdata('user', $user);
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
