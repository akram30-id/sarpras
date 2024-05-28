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

		$data = [
			'submission_item_code' => $submissionItemCode,
			'item_code' => $itemCode,
			'user_submit' => $this->session->user->username,
			'start_date' => date('Y-m-d H:i:s', strtotime($post['start_date'] . ' ' . $post['start_clock'])),
			'end_date' => date('Y-m-d H:i:s', strtotime($post['end_date'] . ' ' . $post['end_clock'])),
			'user_notes' => $post['user_notes'],
			'qty' => $post['qty']
		];
		
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

}
