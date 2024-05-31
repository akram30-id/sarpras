<?php 
defined('BASEPATH') or exit('No direct script is allowed.');

class Ekskul extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->_cekLogin();
	}

	public function master()
	{
		$data['title'] = 'Master Ekskul';
		$data['module'] = 'Ekskul Page';
		$data['datatables'] = base_url('ekskul/datatablesEkskul');
		$data['content'] = $this->load->view('ekskul/master', $data, true);

		$this->load->view('template', $data);
	}

	public function datatablesEkskul()
	{
		$post = $this->input->post();
		$search = $post['search']['value'];
		
		$this->db->select('a.*');
		$this->db->from('tb_master_ekskul AS a');

		if ($search) {
			$this->db->like('a.pic', $search);
		}

		$masterEkskul = $this->db->get()->result();

		$data = [];

		$button = '<div class="d-flex align-items-center justify-content-center">';
		if ($masterEkskul) {
			$no = 1;
			foreach ($masterEkskul as $key => $value) {
				if ($this->session->user->role == 1) {
					$button .= '<a href="' . base_url('ekskul/detail/' . $value->ekskul_code) . '" class="btn btn-primary btn-sm rounded-pill" style="margin-right: 8px;">Detail</a>
								<a href="' . base_url('ekskul/delete/' . $value->ekskul_code) . '" class="btn btn-danger btn-sm rounded-pill">Hapus</a>';
				} else {
					$button .= '<a href="' . base_url('ekskul/detail/' . $value->ekskul_code) . '" class="btn btn-primary btn-sm rounded-pill" style="margin-right: 8px;">Detail</a>';
				}

				$data[] = [
					$no++,
					$value->ekskul_code,
					$value->ekskul_name,
					$value->pic,
					$value->user_input,
					date('d F Y', strtotime($value->created_at)),
					$button
				];
			}

			$output = [
				'draw' => intval($this->input->post('draw')),
				'recordsTotal' => count($masterEkskul),
				'recordsFiltered' => count($masterEkskul),
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
		$button .= '</div>';

		$this->output->set_content_type('application/json')->set_output(json_encode($output));
	}

	public function add()
	{
		$data['title'] = 'Tambah Ekskul';
		$data['module'] = 'Ekskul Page';
		$data['findUser'] = base_url('ekskul/find_user');
		$data['content'] = $this->load->view('ekskul/add', $data, true);

		$this->load->view('template', $data);
	}


	public function find_user()
	{
		$get = $this->input->get();

		$this->db->select('a.username, b.name');
		$this->db->from('tb_user AS a');
		$this->db->join('tb_profile AS b', 'a.username=b.username');
		$this->db->where('a.role', 3);

		if (!in_array($get['search'], [null, ""])) {
			$this->db->like('b.name', $get['search']);
		}

		$this->db->order_by('a.id_user', 'DESC');

		$this->db->limit(100);

		$result = $this->db->get()->result();

		$data = [];

		foreach ($result as $key => $value) {
			$data[] = $value->username . ' - ' . $value->name;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function do_add()
	{
		$post = $this->input->post();
		$headers = $_SERVER;

		$user = $post['pic'];
		$explodeUser = explode(' - ', $user);
		$username = $explodeUser[0];

		// Generate ekskul code
		$this->db->select_max('id_master_ekskul');
		$query = $this->db->get('tb_master_ekskul');
		$row = $query->row();
		$id = (($row->id_master_ekskul == null) ? 0 : $row->id_master_ekskul) + 1;
		$ekskulCode = 'EKSK' . str_pad($id, 6, '0', STR_PAD_LEFT);

		$this->db->trans_begin();
		$this->db->insert('tb_master_ekskul', [
			'ekskul_code' => $ekskulCode,
			'ekskul_name' => $post['ekskul_name'],
			'pic' => $username,
			'user_input' => $this->session->user->username
		]);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$this->_setFlashdata(false, 'Transaction Failed.');
			$this->_writeLog('EKSKUL_ADD', false, $post, $headers);

			return redirect('ekskul/add');
		} else {
			$this->db->trans_commit();
			$this->_setFlashdata(true, 'Ekskul berhasil ditambahkan.');
			$this->_writeLog('EKSKUL_ADD', true, $post, $headers);

			return redirect('ekskul/master');
		}
	}

}


?>
