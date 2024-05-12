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
		$data['content'] = $this->load->view('home', '', true);
		$this->load->view('template', $data);
	}

}


?>
