<?php


defined('BASEPATH') or exit('No direct script is allowed');

class Item_m extends CI_Model
{

	function saveRequest($post)
	{
		// Generate item_code
		$this->db->select_max('id_submission_item');
		$query = $this->db->get('tb_submission_item');
		$row = $query->row();
		$id = (($row->id_submission_item == null) ? 0 : $row->id_submission_item) + 1;
		$submissionItemCode = 'RQI' . str_pad($id, 6, '0', STR_PAD_LEFT);

		$item = trim($post['item']);
		$explode = explode('-', $item);
		$itemCode = $explode[0];

		$cekItem = $this->db->select('item_code')
							->from('tb_master_item')
							->where('item_code', $itemCode)
							->get()->row();

		if (!$cekItem) {
			return ['success' => false, 'message' => 'Item is not found.'];
		}

		$data = [
			'submission_item_code' => $submissionItemCode,
			'item_code' => $itemCode,
			'user_submit' => $this->session->user->username,
			'start_date' => date('Y-m-d H:i:s', strtotime($post['start_date'] . ' ' . $post['start_clock'])),
			'end_date' => date('Y-m-d H:i:s', strtotime($post['end_date'] . ' ' . $post['end_clock'])),
			'user_notes' => $post['user_notes'],
			'qty' => $post['qty']
		];

		if ($data['start_date'] < date('Y-m-d H:i')) {
			return ['success' => false, 'message' => 'Tgl Mulai tidak boleh kurang dari waktu sekarang.'];
		}

		if ($data['end_date'] <= date('Y-m-d H:i')) {
			return ['success' => false, 'message' => 'Tgl Selesai tidak boleh kurang dari waktu sekarang.'];
		}

		if ($data['end_date'] < $data['start_date']) {
			return ['success' => false, 'message' => 'Tgl Selesai tidak boleh kurang dari waktu mulai.'];
		}
		
		$this->db->trans_begin();
		$this->db->insert('tb_submission_item', $data); // insert ke tb_submission_item

		$this->db->insert('tb_approval_item', [
			'status_approval' => 'PENDING',
			'approval_item_flag' => 1,
			'submission_item_code' => $submissionItemCode
		]); // insert ke tb_approval_item

		$this->updateStock($post['item'], $post['qty']); // update stok
		
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return ['success' => false, 'message' => 'Transaction Failed'];
		}

		$this->db->trans_commit();
		return ['success' => true];
	}

	function search_qty($post)
	{
		$this->db->select('a.qty');
		$this->db->from('tb_master_item AS a');

		if (isset($post['search'])) {
			$this->db->where('a.item_code', trim($post['search']));
		}

		if (isset($post['item'])) {
			$item = $post['item'];
			$explode = explode(' - ', $item);
			$itemCode = $explode[0];

			$this->db->where('a.item_code', trim($itemCode));
		}

		$result = $this->db->get()->row();

		return $result;
	}

	function updateStock($item, $reqQty)
	{
		$post = [];
		$post['item'] = $item;

		$getQTY = $this->search_qty($post);
		$qty = $getQTY->qty;

		$currentQTY = intval($qty) - intval($reqQty);

		$this->db->set('qty', $currentQTY);

		$item = $post['item'];
		$explode = explode(' - ', $item);
		$itemCode = $explode[0];

		$this->db->where('item_code', $itemCode);
		$this->db->update('tb_master_item');
	}

	public function showRequestItem($post = null)
	{
		if ($post != null) {
			$offset = $post['start'];
			$limit = $post['length'];
			$search = $post['search']['value'];
		} else {
			$offset = 0;
			$limit = 10;
			$search = null;
		}

		$this->db->select('a.*, b.inventory_name, b.unit_qty, c.status_approval, d.name');
		$this->db->select('(
							CASE
								WHEN c.status_approval = "PENDING"
									THEN "1"
								ELSE "0"
							END) AS cancel_available');
		$this->db->select('(
							CASE
								WHEN c.status_approval = "APPROVE"
									THEN "1"
								ELSE "0"
							END) AS report_available');
		$this->db->from('tb_submission_item AS a');
		$this->db->join('tb_master_item AS b', 'a.item_code=b.item_code');
		$this->db->join('tb_approval_item AS c', 'a.submission_item_code=c.submission_item_code');
		$this->db->join('tb_profile AS d', 'a.user_submit=d.username');

		$this->db->where('a.user_submit', $this->session->user->username);
		$this->db->where('c.status_approval != "REJECT"');

		if ($search) {
			$this->db->where('
			(a.submission_item_code LIKE "%' . $search . '%" 
			OR b.inventory_name LIKE "%' . $search . '%"
			OR a.user_notes LIKE "%' . $search . '%")');
		}

		$this->db->order_by('a.id_submission_item', 'DESC');
		
		$this->db->limit($limit, $offset);

		$result = $this->db->get()->result();

		return $result;
	}

	public function showRequestApproval($post = null, $flag = 1)
	{
		if ($post != null) {
			$offset = $post['start'];
			$limit = $post['length'];
			$search = $post['search']['value'];
		} else {
			$offset = 0;
			$limit = 10;
			$search = null;
		}

		$this->db->select('a.*, b.inventory_name, b.unit_qty, c.status_approval, c.approval_item_flag, c.signature, c.user_input, c.id_approval_item');
		$this->db->select('(CASE
								WHEN c.status_approval = "PENDING"
									THEN "1"
								ELSE "0"
							END) AS need_approval');
		$this->db->select('(CASE
								WHEN d.name IS NULL
									THEN ""
								ELSE d.name
							END) AS user_request');
		$this->db->select('(CASE
								WHEN e.name IS NULL
									THEN ""
								ELSE e.name
							END) AS user_approval');
		// jika flag nya return
		if ($flag == 2) {
			$this->db->select('f.return_item_code');
		}
		$this->db->from('tb_submission_item AS a');
		$this->db->join('tb_master_item AS b', 'a.item_code=b.item_code');

		// jika flag nya return
		if ($flag == 2) {
			$this->db->join('tb_return_item AS f', 'a.submission_item_code=f.submission_item_code');
			$this->db->join('tb_approval_item AS c', 'f.id_return_item=c.id_return_item');
		} else {
			$this->db->join('tb_approval_item AS c', 'a.submission_item_code=c.submission_item_code');
		}

		$this->db->join('tb_profile AS d', 'a.user_submit=d.username', 'left');
		$this->db->join('tb_profile AS e', 'c.user_input=e.username', 'left');

		if (in_array($this->session->user->role, [2])) {
			$this->db->join('tb_master_area AS g', 'b.area_code=g.area_code');
			$this->db->where('g.pic_area', trim($this->session->user->username));
		}

		if ($search) {
			if ($flag == 2) {
				$this->db->where('
					(f.return_item_code LIKE "%' . $search . '%" 
					OR b.inventory_name LIKE "%' . $search . '%"
					OR a.user_notes LIKE "%' . $search . '%")'
				);
			} else {
				$this->db->where('
					(a.submission_item_code LIKE "%' . $search . '%" 
					OR b.inventory_name LIKE "%' . $search . '%"
					OR a.user_notes LIKE "%' . $search . '%")'
				);
			}
		}

		$this->db->where('c.approval_item_flag', $flag);
		
		$this->db->limit($limit, $offset);

		$result = $this->db->get()->result();

		return $result;
	}

	public function showRequestReturn($post = null)
	{
		if ($post != null) {
			$offset = $post['start'];
			$limit = $post['length'];
			$search = $post['search']['value'];
		} else {
			$offset = 0;
			$limit = 10;
			$search = null;
		}

		$this->db->select('a.*, b.submission_item_code, b.qty, f.inventory_name, f.unit_qty, c.status_approval, c.approval_item_flag, a.signature, c.user_input, c.id_approval_item');
		$this->db->select('(CASE
								WHEN c.status_approval = "PENDING"
									THEN "1"
								ELSE "0"
							END) AS need_approval');
		$this->db->select('(CASE
								WHEN d.name IS NULL
									THEN ""
								ELSE d.name
							END) AS user_request');
		$this->db->select('(CASE
								WHEN e.name IS NULL
									THEN ""
								ELSE e.name
							END) AS user_approval');
		$this->db->from('tb_return_item AS a');
		$this->db->join('tb_submission_item AS b', 'a.submission_item_code=b.submission_item_code');
		$this->db->join('tb_approval_item AS c', 'a.id_return_item=c.id_return_item');
		$this->db->join('tb_profile AS d', 'a.user_submit=d.username', 'left');
		$this->db->join('tb_profile AS e', 'c.user_input=e.username', 'left');
		$this->db->join('tb_master_item AS f', 'b.item_code=f.item_code');

		$this->db->where('a.user_submit', trim($this->session->user->username));

		if ($search) {
			$this->db->where('
				(a.submission_item_code LIKE "%' . $search . '%" 
				OR f.inventory_name LIKE "%' . $search . '%"
				OR a.user_notes LIKE "%' . $search . '%")');
		}

		$this->db->order_by('a.id_return_item', 'DESC');
		
		$this->db->limit($limit, $offset);

		$result = $this->db->get()->result();

		return $result;
	}

	public function getDataApprove($idApproval, $flag = 1)
	{
		$this->db->select('a.*, b.inventory_name, b.unit_qty, c.status_approval, c.approval_item_flag, c.signature, c.user_input, c.id_approval_item');
		$this->db->select('(CASE
								WHEN c.status_approval = "PENDING"
									THEN "1"
								ELSE "0"
							END) AS need_approval');
		$this->db->select('(CASE
								WHEN d.name IS NULL
									THEN ""
								ELSE d.name
							END) AS user_request');
		$this->db->select('(CASE
								WHEN e.name IS NULL
									THEN ""
								ELSE e.name
							END) AS user_approval');
		// jika flag nya return
		if ($flag == 2) {
			$this->db->select('f.return_item_code');
		}
		$this->db->from('tb_submission_item AS a');
		$this->db->join('tb_master_item AS b', 'a.item_code=b.item_code');

		// jika flag nya return
		if ($flag == 2) {
			$this->db->join('tb_return_item AS f', 'a.submission_item_code=f.submission_item_code');
			$this->db->join('tb_approval_item AS c', 'f.id_return_item=c.id_return_item');
		} else {
			$this->db->join('tb_approval_item AS c', 'a.submission_item_code=c.submission_item_code');
		}

		$this->db->join('tb_profile AS d', 'a.user_submit=d.username', 'left');
		$this->db->join('tb_profile AS e', 'c.user_input=e.username', 'left');

		$this->db->where('c.approval_item_flag', $flag);

		$this->db->where('c.id_approval_item', $idApproval);

		$result = $this->db->get()->row();

		return $result;
	}

}
