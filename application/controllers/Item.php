<?php 
defined('BASEPATH') or exit('No direct script is allowed');

class Item extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Item_m');
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
			return redirect('item/request');
		}

		try {
			$save = $this->Item_m->saveRequest($post);

			if ($save['success'] == false) {
				$this->_setFlashdata(false, $save['message']);
				$post['message'] = $save['message'];
				$this->_writeLog('ITEM_REQ', false, $post, $headers);
				return redirect('item/request');
			}

			$this->_setFlashdata(true, 'Request item berhasil.');
			$this->_writeLog('ITEM_REQ', true, $post, $headers);
			return redirect('item/request');

		} catch (\Throwable $th) {
			$this->_setFlashdata(false, 'Internal Server Error');
			$post['error_message'] = $th->getMessage();
			$post['error_line'] = $th->getLine();
			$this->_writeLog('ITEM_REQ', false, $post, $headers);

			return redirect('item/request');
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

}
 

?>
