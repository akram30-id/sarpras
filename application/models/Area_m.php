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
		$area = trim($post['area']);
		$explode = explode(' - ', $area);
		$areaCode = $explode[0];

		$startInput = date('Y-m-d H:i', strtotime($post['start_date'] . ' ' . $post['start_clock']));
		$endInput = date('Y-m-d H:i', strtotime($post['end_date'] . ' ' . $post['end_clock']));

		// cek dulu apakah udah ada yg booking atau belum
		$getBookingExist = $this->db->select('start_date, end_date')
									->from('tb_submission_area')
									->where('area_code', $areaCode)
									->get()->result();

		foreach ($getBookingExist as $booking) {
			if (
				($startInput >= date('Y-m-d H:i', strtotime($booking->start_date)) && $startInput <= date('Y-m-d H:i', strtotime($booking->end_date))) 
				|| 
				($endInput >= date('Y-m-d H:i', strtotime($booking->start_date)) && $endInput <= date('Y-m-d H:i', strtotime($booking->end_date)))
			) {
				return ['success' => false, 'message' => 'Jadwal sudah ada yang booking.'];
			}

			if ($startInput <= date('Y-m-d H:i', strtotime($booking->start_date)) && $endInput >= date('Y-m-d H:i', strtotime($booking->start_date))) {
				return ['success' => false, 'message' => 'Jadwal sudah ada yang booking.'];
			}
		}

		// Generate area_code
		$this->db->select_max('id_submission_area');
		$query = $this->db->get('tb_submission_area');
		$row = $query->row();
		$id = (($row->id_submission_area == null) ? 0 : $row->id_submission_area) + 1;
		$submissionAreaCode = 'BOOK' . str_pad($id, 6, '0', STR_PAD_LEFT);

		$data = [
			'submission_area_code' => $submissionAreaCode,
			'area_code' => $areaCode,
			'user_submit' => $this->session->user->username,
			'start_date' => $startInput,
			'end_date' => $endInput,
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
		$post = $this->input->post();

		$search = $post['search']['value'];

		$this->db->select('a.*, b.area_name, b.pic_area, c.name AS submitter_name');
		$this->db->from('tb_submission_area AS a');
		$this->db->join('tb_master_area AS b', 'a.area_code=b.area_code');
		$this->db->join('tb_profile AS c', 'a.user_submit=c.username');
		if ($this->session->user->role == 2) {
			$this->db->where('b.pic_area', $pic);
		} else if ($this->session->user->role == 3) {
			$this->db->where('a.user_submit', $this->session->user->username);
		}

		if (!in_array($search, ['', null])) {
			$this->db->like('b.area_name', $search);
		}

		$this->db->order_by('a.created_at', 'DESC');

		$query = $this->db->get()->result();

		return $query;
	}

	function getSchedule($start, $end)
	{
		$this->db->select('a.id_submission_area AS id, a.start_date AS start, a.end_date AS end, a.user_notes AS description, CONCAT(b.area_name, "[", c.name, "]") AS title');
		$this->db->from('tb_submission_area AS a');
		$this->db->join('tb_master_area AS b', 'a.area_code=b.area_code');
		$this->db->join('tb_profile AS c', 'a.user_submit=c.username');
		$this->db->where('a.status_approval', 'APPROVED');
		$this->db->where('a.start_date >= "' . date('Y-m-d H:i:s', strtotime($start)) . '"', null);
		$this->db->where('a.end_date < "' . date('Y-m-d H:i:s', strtotime($end)) . '"', null);
		$this->db->limit(100);

		$query = $this->db->get()->result();

		return $query;
	}

	function getBookingByUserSubmit($user, $search = null)
	{
		$this->db->select('a.*, b.area_name, b.pic_area, c.name AS submitter_name');
		$this->db->from('tb_submission_area AS a');
		$this->db->join('tb_master_area AS b', 'a.area_code=b.area_code');
		$this->db->join('tb_profile AS c', 'a.user_submit=c.username');
		if (!in_array($search, ['', null])) {
			$this->db->like('b.area_name', $search);
		}

		$this->db->where('a.user_submit', $user);

		if (!in_array($search, ['', null])) {
			$this->db->or_like('c.name', $search);
		}

		$this->db->order_by('a.created_at', 'DESC');

		$query = $this->db->get()->result();

		return $query;
	}

}
