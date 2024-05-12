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
}
