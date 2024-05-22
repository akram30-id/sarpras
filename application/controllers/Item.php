<?php 
defined('BASEPATH') or exit('No direct script is allowed');

class Item extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Item_m');
		$this->load->library('pdfgenerator');
	}

	public function master()
	{
		$area = $this->input->get('area');

		$data['title'] = 'Master Item';
		$data['module'] = 'Item Page';

		if (isset($area) && $area == 1) {
			$data['datatables'] = base_url('item/datatables_master/1');
		} else {
			$data['datatables'] = base_url('item/datatables_master/0');
		}

		$data['content'] = $this->load->view('item/master', $data, true);

		$this->load->view('template', $data);
	}

	public function datatables_master($area)
	{
		$post = $this->input->post();
		$search = $post['search']['value'];
		
		if ($area == 1) {
			$this->db->select('a.*, b.area_name');
			$this->db->from('tb_master_item AS a');
			$this->db->join('tb_master_area AS b', 'a.area_code=b.area_code');
			$this->db->where('a.area_code IS NOT NULL');
			if ($search) {
				$this->db->like('b.area_name', $search);
				$this->db->or_like('a.inventory_name', $search);
			}

			$masterItems = $this->db->get()->result();
		} else {
			$this->db->select('a.*');
			$this->db->from('tb_master_item AS a');
			if ($search) {
				$this->db->or_like('a.inventory_name', $search);
			}

			$masterItems = $this->db->get()->result();
		}

		$data = [];

		if ($masterItems) {
			$no = 1;
			foreach ($masterItems as $key => $value) {
				$data[] = [
					($area == 1) ? $value->area_name : $no++,
					$value->item_code,
					'<img src="' . $value->thumbnail . '" height="32">',
					$value->inventory_name,
					$value->qty . ' ' . $value->unit_qty,
					$value->status,
					$value->description,
					date('d F Y H:i', strtotime($value->created_at)),
					$value->user_input,
					($this->session->user->role == 1 || $this->session->user->username == $value->user_input)
						? '<div class="d-flex align-items-center justify-content-center">
							<a href="' . base_url('item/update_stok/' . $value->item_code) . '" class="btn btn-primary btn-sm rounded-pill">Update</a>
						</div>'
						: '<div class="d-flex align-items-center justify-content-center">
							<a href="' . base_url('item/request/' . $value->item_code) . '" class="btn btn-primary btn-sm rounded-pill">Request</a>
						</div>'
				];
			}

			$output = [
				'draw' => intval($this->input->post('draw')),
				'recordsTotal' => count($masterItems),
				'recordsFiltered' => count($masterItems),
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
		$data['title'] = 'Add Item';
		$data['module'] = 'Item Page';
		$data['findArea'] = base_url('area/find_area');
		$data['content'] = $this->load->view('item/add', $data, true);

		$this->load->view('template', $data);
	}

	public function do_input()
	{
		$post = $this->input->post();
		$headers = $_SERVER;

		try {

			if ($post['qty'] < 1) {
				$this->_setFlashdata(false, 'QTY minimal 1');
				$this->_writeLog('ITEM_ADD', false, $post, $headers);

				return redirect('item/add');
			}

			$thumbnail = $_FILES['thumbnail'];

			if ($thumbnail['size'] > 1048576) {
				$this->_setFlashdata(false, 'Ukuran thumbanil tidak boleh lebih dari 1 MB.');
				return redirect('item/add');
			}

			// ambil image extension
			$extension = explode('.', $thumbnail['name']);
			$extension = $extension[1];

			// Konversi gambar ke base64
			$base64_image = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($thumbnail['tmp_name']));

			// Generate item_code
			$this->db->select_max('id_master_inventory');
			$query = $this->db->get('tb_master_item');
			$row = $query->row();
			$id = (($row->id_master_inventory == null) ? 0 : $row->id_master_inventory) + 1;
			$itemCode = 'INV' . str_pad($id, 6, '0', STR_PAD_LEFT);

			$area = trim($post['area']);
			$explode = explode('-', $area);
			$areaCode = $explode[0];

			$data = [
				'item_code' => $itemCode,
				'area_code' => in_array($post['area'], ["", null]) ? null : $areaCode,
				'inventory_name' => $post['item_name'],
				'qty' => $post['qty'],
				'unit_qty' => $post['unit_qty'],
				'description' => $post['description'],
				'thumbnail' => $base64_image,
				'status' => 'AVAILABLE',
				'user_input' => $this->session->user->username
			];

			$this->db->trans_begin();

			$this->db->insert('tb_master_item', $data);

			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$this->_setFlashdata(false, 'Transaction Failed.');
				$this->_writeLog('ITEM_ADD', false, $post, $headers);
			} else {
				$this->db->trans_commit();
				$this->_setFlashdata(true, 'Item Berhasil Ditambahkan');
				$this->_writeLog('ITEM_ADD', true, $post, $headers);
			}

			return redirect('item/add');
		} catch (\Throwable $th) {
			$this->_setFlashdata(false, 'Internal Server Error');
			$this->_writeLog('ITEM_ADD', false, $post, $headers);

			return redirect('item/add');
		}
	}

	private function _getArea($areaCode)
	{
		$query = $this->db->select('a.area_name')->from('tb_master_area AS a')->where('a.area_code', $areaCode)->get()->row();

		return $query;
	}

	public function update_stok($itemCode)
	{
		$data['title'] = 'Update Item';
		$data['module'] = 'Item Page';
		$data['findArea'] = base_url('area/find_area');
		$data['item'] = $this->repository->findFirst('tb_master_item', ['item_code' => $itemCode]);
		if (in_array($data['item']->area_code, ['', null])) {
			$data['area_name'] = '';
		} else {
			$data['area_name'] = $this->_getArea($data['item']->area_code)->area_name;
		}

		// var_dump($this->db->last_query());
		// var_dump($data['area_name']);
		// return;

		$data['content'] = $this->load->view('item/update', $data, true);

		$this->load->view('template', $data);
	}

	public function do_update($itemCode)
	{
		$post = $this->input->post();
		$headers = $_SERVER;

		try {

			if ($post['qty'] < 1) {
				$this->_setFlashdata(false, 'QTY minimal 1');
				$this->_writeLog('ITEM_UPDATE', false, $post, $headers);
				return redirect('item/update_stok/' . $itemCode);
			}

			$area = trim($post['area']);
			$explode = explode('-', $area);
			$areaCode = $explode[0];

			if (in_array($post['find-area'], [null, ''])) {
				$areaCode = null;
			}

			$data = [
				'area_code' => in_array($post['area'], ["", null]) ? null : $areaCode,
				'inventory_name' => $post['item_name'],
				'qty' => $post['qty'],
				'unit_qty' => $post['unit_qty'],
				'description' => $post['description'],
				'status' => 'AVAILABLE',
				'user_input' => $this->session->user->username
			];

			$thumbnail = $_FILES['thumbnail'];

			if (!in_array($thumbnail["name"], ['', null])) {
				if ($thumbnail['size'] > 1048576) {
					$this->_setFlashdata(false, 'Ukuran thumbanil tidak boleh lebih dari 1 MB.');
					return redirect('item/update_stok/' . $itemCode);
				}
	
				// ambil image extension
				$extension = explode('.', $thumbnail['name']);
				$extension = $extension[1];
	
				// Konversi gambar ke base64
				$base64_image = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($thumbnail['tmp_name']));

				$data['thumbnail'] = $base64_image; // update thumbnail
			}

			$this->db->trans_begin();

			$this->db->update('tb_master_item', $data, ['item_code' => $itemCode]);

			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$this->_setFlashdata(false, 'Transaction Failed.');
				$this->_writeLog('ITEM_UPDATE', false, $post, $headers);
			} else {
				$this->db->trans_commit();
				$this->_setFlashdata(true, 'Item Berhasil Diupdate');
				$this->_writeLog('ITEM_UPDATE', true, $post, $headers);
			}

			return redirect('item/update_stok/' . $itemCode);
		} catch (\Throwable $th) {
			$this->_setFlashdata(false, 'Internal Server Error');
			$this->_writeLog('ITEM_UPDATE', false, $post, $headers);

			return redirect('item/update_stok/' . $itemCode);
		}
	}
	
	public function request()
	{
		$data['title'] = 'Request Item';
		$data['module'] = 'Item Page';
		$data['datatables'] = base_url('item/datatablesRequest');
		$data['content'] = $this->load->view('item/show_submission', $data, true);

		$this->load->view('template', $data);
	}

	public function datatablesRequest()
	{
		$post = $this->input->post();
		$search = $post['search']['value'];

		$this->db->select('a.*, b.inventory_name, b.unit_qty');
		$this->db->from('tb_submission_item AS a');
		$this->db->join('tb_master_item AS b', 'a.item_code=b.item_code');

		if ($this->session->user->role != 1) { // kalo role nya selain admin, cuma boleh akses yg dia submit aja
			$this->db->where('a.user_submit', $this->session->user->username);
		}
		
		if ($search) {
			$this->db->like('b.inventory_name', $search);
			$this->db->or_like('a.item_code', $search);
		}

		$masterItems = $this->db->get()->result();

		$data = [];

		if ($masterItems) {
			$no = 1;
			foreach ($masterItems as $key => $value) {
				$data[] = [
					$no++,
					$value->item_code,
					$value->inventory_name,
					($value->qty == null) ? 0 : $value->qty . ' ' . $value->unit_qty,
					date('d F Y H:i', strtotime($value->start_date)),
					date('d F Y H:i', strtotime($value->end_date)),
					$value->user_notes,
					date('d F Y H:i', strtotime($value->created_at)),
					$value->user_submit,
					(($this->session->user->role == 1 || $this->session->user->username == $value->user_submit) && date('Y-m-d H:i', strtotime($value->end_date)) > date('Y-m-d H:i'))
						? '<div class="d-flex align-items-center justify-content-center">
								<button class="btn btn-sm btn-danger rounded-pill" type="button" style="margin-right: 8px;" data-bs-toggle="collapse" data-bs-target="#cancel' . trim($value->submission_item_code) . '" aria-expanded="false" aria-controls="cancel' . trim($value->submission_item_code) . '">
									Cancel
								</button>
								<a href="' . base_url('item/report/' . $value->submission_item_code) . '" class="btn btn-sm btn-secondary rounded-pill">
									Laporan
								</a>
							</div>
							<div class="flex mt-2">
								<div class="collapse" id="cancel' . trim($value->submission_item_code) . '">
									<div class="card card-body pt-2" style="font-size: 9pt;">
										Apakah Anda yakin?
										<div class="d-flex justify-content-end">
											<a href="' . base_url('item/cancel/' . $value->submission_item_code) . '" style="margin-right: 10px;">Ya</a>
											<a href="' . base_url('item/cancel/' . $value->submission_item_code) . '" data-bs-toggle="collapse" data-bs-target="#cancel' . trim($value->submission_item_code) . '">Tidak</a>
										</div>
									</div>
								</div>
							</div>'
						: '<div class="d-flex align-items-center justify-content-center">
								<a href="' . base_url('item/report/' . $value->submission_item_code) . '" class="btn btn-sm btn-secondary rounded-pill">
									Laporan
								</a>
							</div>
							<div class="flex mt-2">
								<div class="collapse" id="cancel' . trim($value->submission_item_code) . '">
									<div class="card card-body pt-2" style="font-size: 9pt;">
										Apakah Anda yakin?
										<div class="d-flex justify-content-end">
											<a href="' . base_url('item/cancel/' . $value->submission_item_code) . '" style="margin-right: 10px;">Ya</a>
											<a href="' . base_url('item/cancel/' . $value->submission_item_code) . '" data-bs-toggle="collapse" data-bs-target="#cancel' . trim($value->submission_item_code) . '">Tidak</a>
										</div>
									</div>
								</div>
							</div>'
				];
			}

			$output = [
				'draw' => intval($this->input->post('draw')),
				'recordsTotal' => count($masterItems),
				'recordsFiltered' => count($masterItems),
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

	public function form_request()
	{
		$data['title'] = 'Request Item';
		$data['module'] = 'Item Page';
		$data['findItem'] = base_url('item/find_item');
		$data['findItemQty'] = base_url('item/get_qty');
		$data['content'] = $this->load->view('item/request', $data, true);

		$this->load->view('template', $data);
	}

	public function find_item()
	{
		$get = $this->input->get();

		$this->db->select('a.item_code, a.inventory_name');
		$this->db->from('tb_master_item AS a');

		if (!in_array($get['search'], [null, ""])) {
			$this->db->like('a.item_code', $get['search']);
			$this->db->or_like('a.inventory_name', $get['search']);
		}

		$this->db->limit(100);

		$result = $this->db->get()->result();

		$data = [];

		foreach ($result as $key => $value) {
			$data[] = $value->item_code . ' - ' . $value->inventory_name;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function do_request()
	{
		$post = $this->input->post();
		$headers = $_SERVER;

		$getQty = $this->Item_m->search_qty($post);

		if ($post['qty'] > $getQty->qty) {
			$this->_setFlashdata(false, 'QTY request tidak boleh lebih dari ' . $getQty->qty);
			return redirect('item/form_request');
		}

		try {
			$save = $this->Item_m->saveRequest($post);

			if ($save['success'] == false) {
				$this->_setFlashdata(false, $save['message']);
				$post['message'] = $save['message'];
				$this->_writeLog('ITEM_REQ', false, $post, $headers);
				return redirect('item/form_request');
			}

			$this->_setFlashdata(true, 'Request item berhasil.');
			$this->_writeLog('ITEM_REQ', true, $post, $headers);
			return redirect('item/form_request');

		} catch (\Throwable $th) {
			$this->_setFlashdata(false, 'Internal Server Error');
			$post['error_message'] = $th->getMessage();
			$post['error_line'] = $th->getLine();
			$this->_writeLog('ITEM_REQ', false, $post, $headers);

			return redirect('item/form_request');
		}
	}

	public function get_qty()
	{
		$post = $this->input->post();

		$result = $this->Item_m->search_qty($post);

		if (!$result) {
			$result = 0;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}

	public function cancel($requestCode)
	{
		$headers = $_SERVER;

		$getQTY = $this->repository->findFirst('tb_submission_item', ['submission_item_code' => $requestCode], 'qty, item_code');
		$qty = $getQTY->qty;
		$itemCode = $getQTY->item_code;

		$getMasterItemQTY = $this->repository->findFirst('tb_master_item', ['item_code' => $itemCode], 'qty');

		$totalQTY = intval($getMasterItemQTY->qty) + $qty;

		$this->db->trans_begin();
		$updateItemQTY = $this->repository->update('tb_master_item', [
			'qty' => $totalQTY
		], [
			'item_code' => $itemCode
		]);

		$deleteRequest = $this->repository->delete('tb_submission_item', ['submission_item_code' => $requestCode]);

		if ($this->db->trans_status() == false) {
			$this->db->trans_rollback();
			$this->_setFlashdata(false, 'Transaction Failed');
		} else {
			$this->db->trans_commit();
			$this->_setFlashdata(true, 'Cancel sukses.');
			$this->_writeLog('ITEM_REQ_CANCEL', true, $headers, ['submission_item_code' => $requestCode]);
		}

		return redirect('item/request');
	}

	public function report($requestCode)
	{
		$data['title'] = 'Report Item Bermasalah';
		$data['module'] = 'Item Page';
		$data['request_code'] = $requestCode;
		$data['content'] = $this->load->view('item/form_report', $data, true);

		$this->load->view('template', $data);
	}

	public function do_report($requestCode)
	{
		$post = $this->input->post();
		$headers = $_SERVER;

		$evidence = $_FILES['evidence'];

		if (!in_array($evidence['name'], [null, ""])) {
			if ($evidence['size'] > 1048576) {
				$this->_setFlashdata(false, 'Ukuran foto tidak boleh lebih dari 1 MB.');
				return redirect('item/report');
			}
	
			// ambil image extension
			$extension = explode('.', $evidence['name']);
			$extension = $extension[1];
	
			// Konversi gambar ke base64
			$base64_image = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($evidence['tmp_name']));
		} else {
			$base64_image = null;
		}

		$data = [];

		if ($post['type'] == 'broken') {
			// Generate item_code
			$this->db->select_max('id_broken_item');
			$query = $this->db->get('tb_broken_item');

			$row = $query->row();
			$id = (($row->id_broken_item == null) ? 0 : $row->id_broken_item) + 1;
			$reportCode = 'BRKN' . str_pad($id, 6, '0', STR_PAD_LEFT);

			$data = [
				'request_code' => $requestCode,
				'broken_item_code' => $reportCode,
				'user_submit' => $this->session->user->username,
				'reason' => $post['reason'],
				'broken_date' => $post['broken_date'],
				'evidence' => $base64_image
			];
		} else if ($post['type'] == 'lost') {
			$this->db->select_max('id_item_lost');
			$query = $this->db->get('tb_item_lost');
			
			$row = $query->row();
			$id = (($row->id_item_lost == null) ? 0 : $row->id_item_lost) + 1;
			$reportCode = 'LOST' . str_pad($id, 6, '0', STR_PAD_LEFT);

			$data = [
				'request_code' => $requestCode,
				'broken_item_code' => $reportCode,
				'user_submit' => $this->session->user->username,
				'reason' => $post['reason'],
				'broken_date' => $post['broken_date'],
				'evidence' => $base64_image
			];
		} else {
			$this->_setFlashdata(false, 'Invalid report type.');
			return redirect('item/report');
		}

		$this->db->trans_begin();

		if ($post['type'] == 'broken') {
			$this->db->insert('tb_broken_item', $data);
		} else if ($post['type'] == 'lost') {
			$this->db->insert('tb_item_lost', $data);
		} else {
			$this->_setFlashdata(false, 'Invalid Cancel Type.');
			return redirect('item/report/' . $requestCode);
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$this->_setFlashdata(false, 'Transaction Failed.');
			$this->_writeLog('REPORT_ADD', false, $post, $headers);
		} else {
			$this->db->trans_commit();
			$this->_setFlashdata(true, 'Report berhasil dikirim.');
			$this->_writeLog('REPORT_ADD', true, $post, $headers);
		}

		return redirect('item/report/' . $requestCode);
	}

	public function show_report()
	{
		$data['title'] = 'Data Report';
		$data['module'] = 'Item Page';
		$data['datatables'] = base_url('item/datatablesReport');
		$data['content'] = $this->load->view('item/report', $data, true);

		$this->load->view('template', $data);
	}

	private function _getBrokenReport($search = [])
	{
		$this->db->select('a.*, b.item_code, c.inventory_name');
		$this->db->from('tb_broken_item AS a');
		$this->db->join('tb_submission_item AS b', 'a.request_code=b.submission_item_code');
		$this->db->join('tb_master_item AS c', 'b.item_code=c.item_code');

		if ($this->session->user->role != 1) { // kalo role nya bukan admin, cuma boleh akses yg dia submit aja
			$this->db->where('a.user_submit', $this->session->user->username);
		}
		
		if (!empty($search) || !in_array($search, ['', null])) {
			$this->db->like('c.inventory_name', $search);
			$this->db->or_like('b.item_code', $search);
		}

		$result = $this->db->get()->result();

		return $result;
	}

	private function _getLostReport($search)
	{
		$this->db->select('a.*, b.item_code, c.inventory_name');
		$this->db->from('tb_item_lost AS a');
		$this->db->join('tb_submission_item AS b', 'a.request_code=b.submission_item_code');
		$this->db->join('tb_master_item AS c', 'b.item_code=c.item_code');

		if ($this->session->user->role != 1) { // kalo role nya bukan admin, cuma boleh akses yg dia submit aja
			$this->db->where('a.user_submit', $this->session->user->username);
		}
		
		if ($search) {
			$this->db->like('c.inventory_name', $search);
			$this->db->or_like('b.item_code', $search);
		}

		$result = $this->db->get()->result();

		return $result;
	}

	public function datatablesReport()
	{
		$post = $this->input->post();
		$search = $post['search']['value'];

		$getBrokenReport = $this->_getBrokenReport($search);
		$getLostReport = $this->_getLostReport($search);

		$data = [];
		$masterReport = [];

		if ($getBrokenReport) {
			foreach ($getBrokenReport as $key => $value) {
				$value->report_type = 'RUSAK';
				$value->report_code = $value->broken_item_code;
				$value->report_date = $value->broken_date;
				$masterReport[] = $value;
			}
		}

		if ($getLostReport) {
			foreach ($getLostReport as $key => $value) {
				$value->report_type = 'HILANG';
				$value->report_code = $value->item_lost_code;
				$value->report_date = $value->lost_date;
				$masterReport[] = $value;
			}
		}

		if ($masterReport) {
			$no = 1;
			foreach ($masterReport as $key => $value) {
				$data[] = [
					$no++,
					$value->report_code,
					$value->item_code,
					$value->inventory_name,
					$value->report_type,
					$value->reason,
					'<img src="' . $value->evidence . '" height="32"></img>',
					date('d F Y', strtotime($value->report_date)),
					$value->user_submit,
					'<div class="d-flex align-items-center justify-content-center">
						<a href="' . base_url('item/print_report/' . $value->report_type . '/' . $value->report_code) . '" class="btn btn-primary btn-sm rounded-pill" target="_blank">Cetak</a>
					</div>'
				];
			}

			$output = [
				'draw' => intval($this->input->post('draw')),
				'recordsTotal' => count($masterReport),
				'recordsFiltered' => count($masterReport),
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

	public function print_report($type, $reportCode)
	{
		if ($type == 'RUSAK') {
			// title dari pdf
			$this->data['title_pdf'] = 'BERITA ACARA KERUSAKAN BARANG MILIK YAYASAN';
		}

		if ($type == 'HILANG') {
			// title dari pdf
			$this->data['title_pdf'] = 'BERITA ACARA KEHILANGAN BARANG MILIK YAYASAN';
		}
        
        // filename dari pdf ketika didownload
        $file_pdf = $reportCode;
        // setting paper
        $paper = 'A4';
        //orientasi paper potrait / landscape
        $orientation = "potrait";

		$this->db->select('a.*, b.username, b.name, c.role, d.role_name, f.inventory_name, e.item_code, e.qty, f.unit_qty');

        if ($type == 'RUSAK') {
			$this->db->from('tb_broken_item AS a');
		}

		if ($type == 'HILANG') {
			$this->db->from('tb_item_lost AS a');
		}

		$this->db->join('tb_profile AS b', 'a.user_submit=b.username');
		$this->db->join('tb_user AS c', 'b.username=c.username');
		$this->db->join('tb_roles AS d', 'c.role=d.role');
		$this->db->join('tb_submission_item AS e', 'a.request_code=e.submission_item_code');
		$this->db->join('tb_master_item AS f', 'e.item_code=f.item_code');
		$this->db->where('a.broken_item_code', $reportCode);
		
		$report = $this->db->get()->row();

        // echo '<pre>';
        // print_r($report);
        // die();

        $data = [
            'title' => 'BERITA ACARA',
			'subtitle' => $this->data['title_pdf'],
			'type' => $type,
            'report' => $report
        ];

        $html = $this->load->view('item/print_report', $data, true);	    
		// $this->load->view('item/print_report', $data, false);	    
        
        // run dompdf
        $this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
	}

}
 

?>
