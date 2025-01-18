<?php 
defined('BASEPATH') or exit('No direct script is allowed.');

class Ekskul extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->_cekLogin();

		$this->load->model('Area_m');
		$this->load->library('pdfgenerator');
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

		if ($masterEkskul) {
			$no = 1;
			foreach ($masterEkskul as $key => $value) {
				
				$getSchedule = $this->db->select('ekskul_code')->from('tb_ekskul_schedule')->where('ekskul_code', $value->ekskul_code)->get()->row();

				if (!$getSchedule) {
					$additionalButton = '<a href="' . base_url('ekskul/set_schedule/' . $value->ekskul_code) . '" class="btn btn-warning btn-sm rounded-pill" style="margin-right: 8px;">+Jadwal</a>';
				} else {
					$additionalButton = '';
				}

				if ($this->session->user->role == 1) {
					$button = '<div class="d-flex align-items-center justify-content-center">
									<a href="' . base_url('ekskul/detail/' . $value->ekskul_code) . '" class="btn btn-primary btn-sm rounded-pill" style="margin-right: 8px;">Detail</a>
									' . $additionalButton . '
									<button class="btn btn-danger btn-sm rounded-pill" data-bs-toggle="collapse" data-bs-target="#collapseDelete' . $value->ekskul_code . '" aria-expanded="false" aria-controls="collapseDelete">Hapus</button>
								</div>
								<div class="collapse" id="collapseDelete'. $value->ekskul_code . '">
									<div class="card card-body">
										<small class="mt-2">Hapus ' . $value->ekskul_code . '?</small>
										<br>
										<div class="d-flex">
											<a href="' . base_url('ekskul/delete/' . $value->ekskul_code) . '" style="margin-right:8px;">Ya</a>
											<a href="#" data-bs-toggle="collapse" data-bs-target="#collapseDelete' . $value->ekskul_code . '">Tidak</a>
										</div>
									</div>
								</div>';
				} else {
					if ($this->session->user->username == $value->pic) {
						$button = '<div class="d-flex align-items-center justify-content-center">
										<a href="' . base_url('ekskul/detail/' . $value->ekskul_code) . '" class="btn btn-primary btn-sm rounded-pill" style="margin-right: 8px;">Detail</a>
										' . $additionalButton . '
									</div>';
					} else {
						$button = '<div class="d-flex align-items-center justify-content-center">
										<a href="' . base_url('ekskul/detail/' . $value->ekskul_code) . '" class="btn btn-primary btn-sm rounded-pill" style="margin-right: 8px;">Detail</a>
									</div>';
					}
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
		$this->db->where_in('a.role', [1,2]);

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

	public function delete($ekskulCode)
	{
		$headers = $_SERVER;

		$this->db->trans_begin();
		// hapus master ekskul
		$this->db->delete('tb_master_ekskul', ['ekskul_code' => $ekskulCode]);

		// hapus jadwal booking
		$this->db->delete('tb_submission_area', ['user_notes' => 'KEGIATAN EKSKUL #' . $ekskulCode]);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();

			$this->_setFlashdata(false, 'Transaction failed.');
			$this->_writeLog('DELETE_EKSKUL', false, ['ekskul_code' => $ekskulCode, 'success' => false, 'message' => 'Transaction failed.'], $headers);
		} else {
			$this->db->trans_commit();

			$this->_setFlashdata(true, 'Berhasil menghapus ekskul.');
			$this->_writeLog('DELETE_EKSKUL', true, ['ekskul_code' => $ekskulCode, 'success' => true, 'message' => 'Berhasil menghapus ekskul.'], $headers);
		}

		return redirect('ekskul/master');
	}

	public function detail($ekskulCode)
	{
		$detailEkskul = $this->db->select('a.*, c.name')
									->from('tb_master_ekskul AS a')
									->join('tb_profile AS c', 'a.pic=c.username', 'left')
									->where('a.ekskul_code', $ekskulCode)
									->get()->row();

		$detailSchedule = $this->db->select('a.*, b.area_name')
									->from('tb_ekskul_schedule AS a')
									->join('tb_master_area AS b', 'a.area_code=b.area_code')
									->where('a.ekskul_code', $ekskulCode)
									->get()->row();

		$data['title'] = 'Master Ekskul';
		$data['module'] = 'Ekskul Page';
		$data['ekskul_code'] = $ekskulCode;
		$data['ajax'] = base_url('ekskul/pic/' . $ekskulCode);
		$data['ekskul'] = $detailEkskul;
		$data['schedule'] = $detailSchedule;
		$data['findArea'] = base_url('area/find_area');
		$data['findUser'] = base_url('ekskul/find_user');

		$data['content'] = $this->load->view('ekskul/detail', $data, true);

		$this->load->view('template', $data);
	}

	public function getPic($ekskulCode)
	{
		$ekskul = $this->db->select('pic')
							->from('tb_master_ekskul')
							->where('ekskul_code', $ekskulCode)
							->get()->row();

		$pic = $ekskul->pic;

		$getPic = $this->db->select('*')
							->from('tb_profile')
							->where('username', $pic)
							->get()->row();

		$getPic->created_at = date('d F Y', strtotime($getPic->created_at));
		
		$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => true,
					'data' => $getPic
				]));
	}

	public function set_schedule($ekskulCode)
	{
		$this->db->select('a.ekskul_code, a.ekskul_name, a.pic, b.name');
		$this->db->from('tb_master_ekskul AS a');
		$this->db->join('tb_profile AS b', 'a.pic=b.username');
		$this->db->where('ekskul_code', $ekskulCode);
		$ekskul = $this->db->get()->row();

		$data['title'] = 'Set Jadwal Ekskul';
		$data['module'] = 'Ekskul Page';
		$data['ekskul'] = $ekskul;
		$data['findArea'] = base_url('ekskul/find_area');
		$data['ajax'] = base_url('ekskul/pic/' . $ekskulCode);
		$data['content'] = $this->load->view('ekskul/form_schedule', $data, true);

		$this->load->view('template', $data);
	}

	public function find_area()
	{
		$get = $this->input->get();

		$this->db->select('a.area_code, a.area_name');
		$this->db->from('tb_master_area AS a');

		if (!in_array($get['search'], [null, ""])) {
			$this->db->like('a.area_name', $get['search']);
		}

		$this->db->limit(100);

		$result = $this->db->get()->result();

		$data = [];

		foreach ($result as $key => $value) {
			$data[] = $value->area_code . ' - ' . $value->area_name;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function do_insert_schedule($ekskulCode)
	{
		$post = $this->input->post();

		$this->db->trans_begin();

		$post['ekskul'] = $ekskulCode; // tambahin index "ekskul"

		$explode = explode(' - ', $post['area']);
		$areaCode = $explode[0];

		// masuk ke master schedule
		$this->db->insert('tb_ekskul_schedule', [
			'ekskul_code' => $ekskulCode,
			'area_code' => $areaCode,
			'day' => $post['day'],
			'start_clock' => $post['start_clock'],
			'end_clock' => $post['end_clock'],
			'user_input' => $this->session->user->username
		]);

		$scheduleId = $this->db->insert_id();
		$scheduleCode = 'SCHE' . str_pad($scheduleId, 6, '0', STR_PAD_LEFT);
		// update schedule code
		$this->db->update('tb_ekskul_schedule', ['schedule_code' => $scheduleCode], ['id_ekskul_schedule' => $scheduleId]);

		$this->_setAutoSchedule($post);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			
			$this->_writeLog('ADD_SCHEDUL_EKS', false, $post, $_SERVER);

			$this->_setFlashdata(false, 'Transaciton Failed.');
		} else {
			$this->db->trans_commit();

			$this->_writeLog('ADD_SCHEDUL_EKS', true, $post, $_SERVER);

			$this->_setFlashdata(true, 'Behasil menambahkan jadwal.');
		}

		return redirect('ekskul/master');
	}

	private function _setAutoSchedule($post)
	{
		// initialize days
		$days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
		$selectedDay = $days[intval($post['day'])];

		// Start date
		$startDate = new DateTime();
		$startDate->modify('next ' . $selectedDay); // Start from the next Thursday

		// End date (6 months ahead)
		$endDate = new DateTime();
		$endDate->modify('+6 months');

		// Array to store all Thursdays
		$dates = [];

		// Iterate through each week to get Thursdays
		while ($startDate <= $endDate) {
			// Add the date to the array
			$dates[] = $startDate->format('Y-m-d');
			// Move to the next Thursday
			$startDate->modify('+1 week');
		}

		// Set all dates
		foreach ($dates as $date) {
			// echo $date . PHP_EOL;
			
			// booking otomatis
			$post['start_date'] = $date;
			$post['end_date'] = $date;
			$post['user_notes'] = 'KEGIATAN EKSKUL #' . $post['ekskul'];
			$booking = $this->Area_m->saveBooking($post);

			// approve otomatis
			if ($booking['success']) {
				$this->db->update('tb_submission_area', [
					'status_approval' => 'APPROVED',
					'user_update' => $this->session->user->username,
				], ['submission_area_code' => $booking['submission_area_code']]);
			} else {
				$this->_writeLog('ADD_SCHEDUL_EKS', false, ['post' => $post, 'message' => $booking['message'] ?? ''], $_SERVER);
				continue;
			}
		}
	}

	public function schedule()
	{
		$schedule = $this->get_schedule();

		$data['title'] = 'Jadwal Ekskul Semester Ini';
		$data['module'] = 'Ekskul Page';
		$data['schedule'] = $schedule;
		$data['content'] = $this->load->view('ekskul/schedule', $data, true);

		$this->load->view('template', $data);
	}

	public function get_schedule()
	{
		$this->db->select('a.*, b.ekskul_name');
		$this->db->from('tb_ekskul_schedule AS a');
		$this->db->join('tb_master_ekskul AS b', 'a.ekskul_code=b.ekskul_code');
		$query = $this->db->get()->result();

		return $query;
	}

	public function do_update($ekskulCode)
	{
		$ekskul = $this->db->get_where('tb_master_ekskul', ['ekskul_code' => $ekskulCode])->row();

		// cuma boleh diupdate oleh admin atau user PIC ekskul
		if($this->session->user->role != 1) { // kalo bukan admin
			if ($this->session->user->username != $ekskul->pic) { // bukan admin, bukan PIC juga
				$this->_setFlashdata(false, 'Akses ditolak.');
				return redirect('ekskul/master');
			}
		}

		$post = $this->input->post();

		$explode = explode(' - ', $post['pic']);
		$pic = $explode[0];

		$this->db->trans_begin();
		$this->repository->update('tb_master_ekskul', [
			'ekskul_name' => $post['ekskul_name'],
			'pic' => $pic,
			'updated_at' => date('Y-m-d H:i:s'),
			'user_input' => $this->session->user->username
		]);

		if ($this->db->trans_status() === FALSE) {
			$this->_writeLog('EKSKUL_UPDATE', false, $post, $_SERVER);
			$this->_setFlashdata(false, 'Transaction Failed.');

			$this->db->trans_rollback();
		} else {
			$this->_writeLog('EKSKUL_UPDATE', true, $post, $_SERVER);
			$this->_setFlashdata(true, 'Berhasil Update Master Ekskul');

			$this->db->trans_commit();
		}

		return redirect('ekskul/detail/' . $ekskulCode);
	}

	public function do_update_schedule($scheduleCode)
	{
		$post = $this->input->post();

		$ekskul = $this->db->get_where('tb_ekskul_schedule', ['schedule_code' => $scheduleCode])->row();

		$this->db->trans_begin();

		$post['ekskul'] = $ekskul->ekskul_code; // tambahin index "ekskul"

		$explode = explode(' - ', $post['area']);
		$areaCode = $explode[0];

		// delete booking
		$this->db->delete('tb_submission_area', ['user_notes' => 'KEGIATAN EKSKUL #' . $ekskul->ekskul_code]);

		// update ke master schedule
		$this->db->update('tb_ekskul_schedule', [
			'area_code' => $areaCode,
			'day' => $post['day'],
			'start_clock' => $post['start_clock'],
			'end_clock' => $post['end_clock'],
			'user_input' => $this->session->user->username,
			'updated_at' => date('Y-m-d H:i:s')
		], ['schedule_code' => $scheduleCode]);

		$this->_setAutoSchedule($post);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			
			$this->_writeLog('UPDATE_SCHEDULE_EKS', false, $post, $_SERVER);

			$this->_setFlashdata(false, 'Transaciton Failed.');
		} else {
			$this->db->trans_commit();

			$this->_writeLog('UPDATE_SCHEDULE_EKS', true, $post, $_SERVER);

			$this->_setFlashdata(true, 'Update jadwal berhasil.');
		}

		return redirect('ekskul/detail/' . $ekskul->ekskul_code);
	}

	public function print_ekskul()
	{
		$post = $this->input->post();

		$start = $post['start'];
		$end = $post['end'];

		$this->data['title_pdf'] = 'LAPORAN EKSTRAKURIKULER PERIODE ' . date('d/m/Y', strtotime($start)) . ' - ' . date('d/m/Y', strtotime($end));
        
        // filename dari pdf ketika didownload
        $file_pdf = 'EKSKUL_REPORT_' . date('dmY', strtotime($start)) . '_' . date('dmY', strtotime($end));
        // setting paper
        $paper = 'A4';
        //orientasi paper potrait / landscape
        $orientation = "potrait";

		$this->db->select('a.*, b.name, c.*');

		$this->db->from('tb_master_ekskul AS a');

		$this->db->join('tb_profile AS b', 'a.pic=b.username');

		$this->db->join('tb_ekskul_schedule AS c', 'a.ekskul_code=c.ekskul_code');

		$this->db->where('(a.created_at BETWEEN "' . $start . ' 00:00:00' . '" AND "' . $end . ' 23:59:00' . '")');

		$this->db->order_by('a.id_master_ekskul', 'ASC');
		
		$report = $this->db->get()->result();

		// echo '<pre>';
		// print_r($report);
		// return;

        $data = [
            'title' => 'EKSTRAKURIKULER REPORT',
			'subtitle' => $this->data['title_pdf'],
            'report' => $report
        ];

        $html = $this->load->view('ekskul/print_report', $data, true);
        
        // run dompdf
        $this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
	}

}


?>
