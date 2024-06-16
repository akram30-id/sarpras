<?php 
defined('BASEPATH') or exit('No direct script is allowed');

class Home extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		if (!$this->session->has_userdata('user')) {
			redirect('auth/index');
		}
	}

	public function index()
	{
		$data['title'] = 'Home Page';
		$data['module'] = 'Home';
		$data['total_user'] = $this->_getTotalUser();
		$data['total_ekskul'] = $this->_getTotalEkskul();
		$data['total_pending_area'] = $this->_getTotalSubmissionArea();
		$data['total_pending_item'] = $this->_getTotalSubmissionItem();
		$data['statistic'] = base_url('home/get_annual_statistic');
		$data['content'] = $this->load->view('home', $data, true);
		$this->load->view('template', $data);
	}

	private function _getTotalUser()
	{
		$total = $this->db->select('id_user')->from('tb_user')->limit(1000)->get()->result();

		$totalUser = count($total);
		if ($totalUser == 1000) {
			$totalUser = '1000+';
		}

		return $totalUser;
	}

	private function _getTotalEkskul()
	{
		$total = $this->db->select('id_master_ekskul')->from('tb_master_ekskul')->limit(1000)->get()->result();

		$totalEkskul = count($total);
		if ($totalEkskul == 1000) {
			$totalEkskul = '1000+';
		}

		return $totalEkskul;
	}

	private function _getTotalSubmissionArea()
	{
		$getPICs = $this->repository->findMany('tb_master_area', ['pic_area' => $this->session->user->username], 'area_code');
		
		$this->db->select('a.id_submission_area');
		$this->db->from('tb_submission_area AS a');

		// jika rolenya admin
		if ($this->session->user->role == 1) {
			$this->db->where('a.status_approval', 'PENDING');
		} else if ($this->session->user->role == 2) { // jika rolenya guru
			// cek apakah usernya salah satu dari pic area
			if ($getPICs) { // kalau usernya salah satu dari PIC area
				$this->db->join('tb_master_area AS b', 'a.area_code=b.area_code');
				$this->db->where('a.status_approval', 'PENDING');
				$this->db->where('b.pic_area', $this->session->user->username);
			} else {
				$this->db->where('a.status_approval', 'PENDING');
				$this->db->where('a.user_submit', $this->session->user->username);	
			}
		} else {
			$this->db->where('a.status_approval', 'PENDING');
			$this->db->where('a.user_submit', $this->session->user->username);
		}

		$this->db->limit(1000);
		$total = $this->db->get()->result();

		$totalSubmission = count($total);
		if ($totalSubmission == 1000) {
			$totalSubmission = '1000+';
		}

		return $totalSubmission;
	}

	private function _getTotalSubmissionItem()
	{		
		$this->db->select('a.id_submission_item');
		$this->db->from('tb_submission_item AS a');
		$this->db->join('tb_approval_item AS b', 'a.submission_item_code=b.submission_item_code');

		// jika rolenya admin
		if ($this->session->user->role == 1) {
			$this->db->where('b.status_approval', 'PENDING');
		} else {
			$this->db->where('b.status_approval', 'PENDING');
			$this->db->where('a.user_submit', $this->session->user->username);
		}

		$this->db->limit(1000);
		$total = $this->db->get()->result();

		$totalSubmission = count($total);
		if ($totalSubmission == 1000) {
			$totalSubmission = '1000+';
		}

		return $totalSubmission;
	}

	public function getMonths()
	{
		$months = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
		
		return $months;
	}

	private function _getDataAnnual($month)
	{
		$year = date('Y');
		$data = [];

		$getUserAnnual = $this->db->select('COUNT(a.username) AS total_user')
									->from('tb_user AS a')
									->where('YEAR(a.created_at) = "' . $year . '"')
									->where('MONTH(a.created_at) = "' . $month . '"')
									->get()->row();

		$data['user'] = $getUserAnnual->total_user;

		$getEkskulAnnual = $this->db->select('COUNT(a.ekskul_code) AS total_ekskul')
									->from('tb_master_ekskul AS a')
									->where('YEAR(a.created_at) = "' . $year . '"')
									->where('MONTH(a.created_at) = "' . $month . '"')
									->get()->row();

		$data['ekskul'] = $getEkskulAnnual->total_ekskul;

		$getItemAnnual = $this->db->select('COUNT(a.submission_item_code) AS total_submission_item')
									->from('tb_submission_item AS a')
									->where('YEAR(a.created_at) = "' . $year . '"')
									->where('MONTH(a.created_at) = "' . $month . '"')
									->get()->row();

		$data['submission_item'] = $getItemAnnual->total_submission_item;

		$getItemAnnual = $this->db->select('COUNT(a.submission_area_code) AS total_submission_area')
									->from('tb_submission_area AS a')
									->where('YEAR(a.created_at) = "' . $year . '"')
									->where('MONTH(a.created_at) = "' . $month . '"')
									->get()->row();

		$data['submission_area'] = $getItemAnnual->total_submission_area;

		return $data;
	}

	public function get_annual_statistic()
	{
		$months = $this->getMonths();

		$result = [];
		foreach ($months as $idxMonth => $month) {
			$result['user'][] = $this->_getDataAnnual(intval($idxMonth) + 1)['user'];
			$result['ekskul'][] = $this->_getDataAnnual(intval($idxMonth) + 1)['ekskul'];
			$result['submission_item'][] = $this->_getDataAnnual(intval($idxMonth) + 1)['submission_item'];
			$result['submission_area'][] = $this->_getDataAnnual(intval($idxMonth) + 1)['submission_area'];
		}

		echo json_encode($result);
	}

}


?>
