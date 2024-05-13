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
		$this->db->select('a.role, b.*');
		$this->db->from('tb_user AS a');
		$this->db->join('tb_profile AS b', 'a.username=b.username');
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
		$this->db->where('a.username != ' . $this->session->user->username);
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

			// update tb_user
			$this->db->set('role', $post['role']);
			$this->db->set('user_input', $this->session->user->username);
			$this->db->where('username', $username);
			$saveUser = $this->db->update('tb_user');

			// update tb_profile
			$this->db->set('name', $post['name']);
			if ($base64_image !== null) {
				$this->db->set('photo', $base64_image);
			}
			$this->db->set('birth_date', $post['birth_date']);
			$this->db->set('born_at', $post['born_at']);
			$this->db->set('name', $post['name']);
			$this->db->set('user_input', $this->session->user->username);
			$this->db->where('username', $username);
			$saveProfile = $this->db->update('tb_profile');

			if ($this->db->trans_status() == false) {
				$this->db->trans_rollback();
				$this->_setFlashdata(false, 'Transaction Error');
				$this->_writeLog('USER_UPDATE', false, $post, $headers);
				redirect('user/edit/' . $username);
				return;
			} else {
				$this->db->trans_commit();
				$this->_setFlashdata(true, 'User berhasil dibuat.');
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

}


?>
