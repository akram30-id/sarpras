<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Area_m extends CI_Model
{

	public function create_area($area_name, $open_hours, $close_hours)
	{
		$user_input = $this->session->user->username;
		$data = array(
			'area_name' => $area_name,
			'open_hours' => $open_hours,
			'close_hours' => $close_hours,
			'status' => 'ACTIVE',
			'user_input' => $user_input
		);

        // Generate area_code
		$this->db->select_max('id_master_area');
		$query = $this->db->get('tb_master_area');
		$row = $query->row();
		$id = (($row->id_master_area == null) ? 0 : $row->id_master_area) + 1;
		$area_code = 'HFOA' . str_pad($id, 6, '0', STR_PAD_LEFT);

		$data['area_code'] = $area_code;

		$this->db->trans_begin();
		$this->db->insert('tb_master_area', $data);
		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return ['status' => false, 'message' => 'Transaction Failed'];
		} else {
			$this->db->trans_commit();
			return ['status' => true, 'area_code' => $area_code];
		}
	}

	public function update_area($area_code, $area_name, $open_hours, $close_hours, $status)
	{
		$user_input = $this->session->user->username;
		$data = array(
			'area_name' => $area_name,
			'open_hours' => $open_hours,
			'close_hours' => $close_hours,
			'status' => $status,
			'user_input' => $user_input,
			'updated_at' => date('Y-m-d H:i:s')
		);

		$this->db->trans_begin();

		$this->db->where('area_code', $area_code);
		$this->db->update('tb_master_area', $data);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return ['status' => false, 'message' => 'Transaction Error'];
		} else {
			$this->db->trans_commit();
			return ['status' => true];
		}
	}

	public function delete_area($area_code)
	{
		$this->db->where('area_code', $area_code);
		$delete = $this->db->delete('tb_master_area');

		if ($delete == false) {
			return ['status' => false, 'messag' => 'Internal Server Error'];
		}

		return ['status' => true, 'area_code' => $area_code];
	}

	public function save_image($area_code, $base64_image)
	{
		$data = [];

		foreach ($base64_image as $key => $value) {
			$data[] = [
				'photo_url' => $value,
				'area_code' => $area_code,
				'user_input' => $this->session->user->username
			];
		}

		$this->db->trans_begin();
		
		$this->db->insert_batch('tb_photo_area', $data);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return ['status' => false, 'message' => 'Transaction Error'];
		} else {
			$this->db->trans_commit();
			return ['status' => true, 'area_code' => $area_code];
		}
	}

	function saveBooking($post)
	{
		// Generate area_code
		$this->db->select_max('id_submission_area');
		$query = $this->db->get('tb_submission_area');
		$row = $query->row();
		$id = (($row->id_submission_area == null) ? 0 : $row->id_submission_area) + 1;
		$submissionAreaCode = 'BOOK' . str_pad($id, 6, '0', STR_PAD_LEFT);

		$area = trim($post['area']);
		$explode = explode('-', $area);
		$areaCode = $explode[0];

		$data = [
			'submission_area_code' => $submissionAreaCode,
			'area_code' => $areaCode,
			'user_submit' => $this->session->user->username,
			'start_date' => date('Y-m-d H:i:s', strtotime($post['start_date'] . ' ' . $post['start_clock'])),
			'end_date' => date('Y-m-d H:i:s', strtotime($post['end_date'] . ' ' . $post['end_clock'])),
			'user_notes' => $post['user_notes']
		];
		$this->db->trans_begin();
		$this->db->insert('tb_submission_area', $data);
		
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return ['success' => false, 'message' => 'Transaction Failed'];
		}

		$this->db->trans_commit();
		return ['success' => true];
	}

	function getBookingApproval($pic)
	{
		$this->db->select('a.*, b.area_name, b.pic_area, c.name AS submitter_name');
		$this->db->from('tb_submission_area AS a');
		$this->db->join('tb_master_area AS b', 'a.area_code=b.area_code');
		$this->db->join('tb_profile AS c', 'a.user_submit=c.username');
		if ($this->session->user->role != 1) {
			$this->db->where('b.pic_area', $pic);
		}
		$query = $this->db->get()->result();

		return $query;
	}
}
