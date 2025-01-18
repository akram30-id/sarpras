<?php 
defined('BASEPATH') or exit('No direct script is allowed');

class User extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!$this->session->has_userdata('user')) {
			redirect('auth/index');
		}
	}

	public function register()
	{
		$data['title'] = 'Register User';
		$data['module'] = 'User Managemenet';
		$data['roles'] = $this->repository->findMany('tb_roles', [], 'role, role_name');
		$data['content'] = $this->load->view('user/register', $data, true);

		$this->load->view('template', $data);
	}

	public function edit($username)
	{
		$data['title'] = 'Edit User';
		$data['module'] = 'User Managemenet';
		$data['roles'] = $this->repository->findMany('tb_roles', [], 'role, role_name');
		$data['user'] = $this->_getUserByUsername($username);
		$data['content'] = $this->load->view('user/edit', $data, true);

		$this->load->view('template', $data);
	}

	private function _getUserByUsername($username)
	{
		$this->db->select('a.role, a.username as user_username, b.*');
		$this->db->from('tb_user AS a');
		$this->db->join('tb_profile AS b', 'a.username=b.username', 'LEFT');
		$this->db->where('a.username', $username);

		$query = $this->db->get()->row();

		return $query;
	}

	public function do_register()
	{
		try {
			$post = $this->input->post();

			// echo '<pre>';
			// print_r($post);
			// return;

			$headers = $_SERVER;

			if (!in_array($_FILES['avatar']['name'], ['', null])) {
				$avatar = $_FILES['avatar'];
	
				$temp_name = $avatar['tmp_name'];
				$image_name = $avatar['name'];
	
				if ($avatar['size'] > 1048576) {
					$this->_setFlashdata(false, 'Ukuran foto tidak boleh lebih dari 1 MB.');
					redirect('user/register');
					return;
				}
	
				// ambil image extension
				$extension = explode('.', $image_name);
				$extension = $extension[1];
	
				// Konversi gambar ke base64
				$base64_image = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($temp_name));
			} else {
				$base64_image = null;
			}

			$this->db->trans_begin();
			$saveUser = $this->repository->create('tb_user', [
				'username' => $post['user_code'],
				'password' => $this->_encryptLogin('default'),
				'role' => $post['role'],
				'user_input' => $this->session->user->username
			]);

			$saveProfile = $this->repository->create('tb_profile', [
				'name' => $post['name'],
				'username' => $post['user_code'],
				'photo' => $base64_image,
				'birth_date' => $post['birth_date'],
				'born_at' => $post['born_at'],
				'user_input' => $this->session->user->username
			]);

			if ($this->db->trans_status() == false) {
				$this->db->trans_rollback();
				$this->_setFlashdata(false, 'Transaction Error');
				$this->_writeLog('USER_REGISTER', false, $post, $headers);
				redirect('user/register');
				return;
			} else {
				$this->db->trans_commit();
				$this->_setFlashdata(true, 'User berhasil dibuat.');
				$this->_writeLog('USER_REGISTER', true, $post, $headers);
				redirect('user/register');
				return;
			}
		} catch (\Throwable $th) {
			$this->_setFlashdata(false, 'Internal Server Error');
			$this->_writeLog('USER_REGISTER', false, $post, $headers);
			redirect('user/register');
			return;
		}
	}

	public function master()
	{
		$data['title'] = 'Master Data User';
		$data['module'] = 'User Page';
		$data['datatables'] = base_url('user/getUsers');
		$data['content'] = $this->load->view('user/master', $data, true);

		$this->load->view('template', $data);
	}

	public function getUsers()
	{
		$this->db->select('a.username, a.user_input, a.created_at, b.role_name, c.name');
		$this->db->from('tb_user AS a');
		$this->db->join('tb_roles AS b', 'a.role=b.role');
		$this->db->join('tb_profile AS c', 'a.username=c.username');
		$this->db->where('a.username != "' . $this->session->user->username . '"');
		$result = $this->db->get()->result();

		$data = [];
		$no = 1;

		if (!empty($result)) {
			foreach ($result as $key => $value) {
				$data[] = [
					$no++,
					$value->username,
					$value->name,
					$value->role_name,
					$value->user_input,
					date('d F Y', strtotime($value->created_at)),
					'<div class="d-flex align-items-center justify-content-center">
						<a href="' . base_url('user/edit/' . $value->username) . '" class="btn btn-primary btn-sm rounded-pill" style="margin-right: 8px;">Edit</a>
						<button type="button delete-btn" data-username="' . $value->username . '" class="btn btn-danger btn-sm rounded-pill" data-bs-toggle="collapse" data-bs-target="#collapseDelete' . $value->username . '" aria-expanded="false" aria-controls="collapseDelete">Delete</button>
					</div>
					<div class="collapse" id="collapseDelete'. $value->username . '">
						<div class="card card-body">
							<small class="mt-2">Hapus ' . $value->username . '?</small>
							<br>
							<div class="d-flex">
								<a href="' . base_url('user/delete/' . $value->username) . '" style="margin-right:8px;">Ya</a>
								<a href="#" data-bs-toggle="collapse" data-bs-target="#collapseDelete' . $value->username . '">Tidak</a>
							</div>
						</div>
					</div>'
				];
			}

			$output = [
				'draw' => intval($this->input->post('draw')),
				'recordsTotal' => count($result),
				'recordsFiltered' => count($result),
				'data' => $data
			];
		} else {
			$output = [
				'draw' => intval($this->input->post('draw')),
				'recordsTotal' => 0,
				'recordsFiltered' => 0,
				'data' => []
			];
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($output));
	}

	public function do_update($username)
	{
		try {
			$post = $this->input->post();

			// echo '<pre>';
			// print_r($post);
			// print_r($username);
			// return;

			$headers = $_SERVER;
			$avatar = $_FILES['avatar'];

			$temp_name = $avatar['tmp_name'];
			$image_name = $avatar['name'];

			if ($image_name != null) {
				if ($avatar['size'] > 1048576) {
					$this->_setFlashdata(false, 'Ukuran foto tidak boleh lebih dari 1 MB.');
					redirect('user/edit/' . $username);
					return;
				}
	
				// ambil image extension
				$extension = explode('.', $image_name);
				$extension = $extension[1];
	
				// Konversi gambar ke base64
				$base64_image = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($temp_name));
			} else {
				$base64_image = null;
			}

			$this->db->trans_begin();

			$getUserProfile = $this->db->get_where('tb_profile', ['username' => $username])->row();

			// update tb_user
			$this->db->set('user_input', $this->session->user->username);
			$this->db->where('username', $username);
			$saveUser = $this->db->update('tb_user');

			// update tb_profile
			if ($base64_image !== null) {
				$this->db->set('photo', $base64_image);
			}
			$this->db->set('birth_date', $post['birth_date']);
			$this->db->set('born_at', strtoupper($post['born_at']));
			$this->db->set('name', strtoupper($post['name']));
			$this->db->set('user_input', $this->session->user->username);

			// jika belum ada profilenya
			if (!$getUserProfile) {
				$this->db->set('username', $username);
				$saveProfile = $this->db->insert('tb_profile');
			} else { // jika sudah ada
				$this->db->where('username', $username);
				$saveProfile = $this->db->update('tb_profile');
			}

			if ($this->db->trans_status() == false) {
				$this->db->trans_rollback();
				$this->_setFlashdata(false, 'Transaction Error');
				$this->_writeLog('USER_UPDATE', false, $post, $headers);
				redirect('user/edit/' . $username);
				return;
			} else {

				if ($username == $this->session->user->username && $base64_image != null) {
					$this->session->user->photo = $base64_image;
					$this->session->user->name = $post['name'];
				}

				$this->db->trans_commit();
				$this->_setFlashdata(true, 'User berhasil diupdate.');
				$this->_writeLog('USER_UPDATE', true, $post, $headers);
				redirect('user/master');
				return;
			}
		} catch (\Throwable $th) {
			$this->_setFlashdata(false, 'Internal Server Error');
			$this->_writeLog('USER_UPDATE', false, $post, $headers);
			redirect('user/edit/' . $username);
			return;
		}
	}

	public function delete($username)
	{
		$headers = $_SERVER;
		$headers['ip_address'] = $this->input->ip_address();

		$this->db->where('username', $username);
		$delete = $this->db->delete('tb_user');

		if ($delete == false) {
			$this->_setFlashdata(false, 'Gagal hapus user.');
			$this->_writeLog('USER_DELETE', false, ['username' => $username], $headers);
		} else {
			$this->_setFlashdata(true, 'Berhasil hapus user.');
			$this->_writeLog('USER_DELETE', true, ['username' => $username], $headers);
		}
		
		redirect('user/master');
		return;
	}

	public function reset()
	{
		$data['title'] = 'Reset Password';
		$data['module'] = 'User Page';
		$data['findUser'] = base_url('user/find_user');
		$data['content'] = $this->load->view('user/reset', $data, true);

		$this->load->view('template', $data);
	}

	public function find_user()
	{
		$get = $this->input->get();

		$this->db->select('a.username, c.name');
		$this->db->from('tb_user AS a');
		$this->db->join('tb_profile AS c', 'a.username=c.username');

		if (isset($get['search']) || !in_array($get['search'], [null, ""])) {
			$this->db->like('c.name', $get['search']);
		}

		$this->db->limit(100);

		$result = $this->db->get()->result();

		$data = [];

		foreach ($result as $key => $value) {
			$data[] = $value->username . ' - ' . $value->name;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function do_reset()
	{
		$post = $this->input->post();

		$user = $post['user'];
		$explode = explode(' - ', $user);
		$username = $explode[0];

		$getUser = $this->db->get_where('tb_user', ['username' => $username])->row();

		if ($getUser) {
			$this->db->trans_begin();

			$this->db->update('tb_user', ['password' => $this->_encryptLogin('default')], ['username' => $username]);

			if ($this->db->trans_status() === FALSE) {
				$this->_writeLog('PASSWORD_RESET', false, $post, $_SERVER);
				$this->_setFlashdata(false, 'Transaction Failed');

				$this->db->trans_rollback();
			} else {
				$this->_writeLog('PASSWORD_RESET', true, $post, $_SERVER);
				$this->_setFlashdata(true, 'Password berhasil direset');

				$this->db->trans_commit();
			}
		} else {
			$post['message'] = 'User tidak ditemukan.';
			$this->_writeLog('PASSWORD_RESET', false, $post, $_SERVER);
			$this->_setFlashdata(false, $post['message']);
		}

		return redirect('user/reset');

	}

	public function change()
	{
		$data['title'] = 'Change Password';
		$data['module'] = 'User Page';
		$data['user'] = $this->session->user->username;
		$data['content'] = $this->load->view('user/change', $data, true);

		$this->load->view('template', $data);
	}

	public function do_change()
	{
		$post = $this->input->post();

		$cekUser = $this->db->get_where('tb_user', ['username' => $post['user'], 'password' => $this->_encryptLogin($post['old-password'])])->row();

		if (!$cekUser) {
			$post['message'] = 'Password lama salah.';
			$this->_setFlashdata(false, $post['message']);
			$this->_writeLog('CHANGE_PASSWORD', false, $post, $_SERVER);

			return redirect('user/change');
		}

		if ($post['confirm-password'] != $post['new-password']) {
			$post['message'] = 'Password baru dan konfirmas password tidak sama.';
			$this->_setFlashdata(false, $post['message']);
			$this->_writeLog('CHANGE_PASSWORD', false, $post, $_SERVER);

			return redirect('user/change');
		}

		$this->db->trans_begin();

		$this->db->update('tb_user', ['password' => $this->_encryptLogin($post['new-password'])], ['username' => $post['user']]);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();

			$post['message'] = 'Transaction Failed.';
			$this->_setFlashdata(false, $post['message']);
			$this->_writeLog('CHANGE_PASSWORD', false, $post, $_SERVER);
		} else {
			$this->db->trans_commit();

			$post['message'] = 'Password berhasil diubah.';
			$this->_setFlashdata(true, $post['message']);
			$this->_writeLog('CHANGE_PASSWORD', true, $post, $_SERVER);
		}

		return redirect('user/change');


	}

	public function update()
	{
		$username = $this->session->user->username;

		$data['title'] = 'Edit Profile';
		$data['module'] = 'User Page';
		$data['edit_my_self'] = true;
		$data['user'] = $this->_getUserByUsername($username);
		$data['content'] = $this->load->view('user/edit', $data, true);

		$this->load->view('template', $data);
	}
}


?>
