<?php 

defined('BASEPATH') or exit('No direct script is allowed');

class Area extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Area_m');

		if (!$this->session->has_userdata('user')) {
			redirect('auth/index');
		}
	}

	public function master()
	{
		$data['title'] = 'Master Data Area';
		$data['module'] = 'Area Page';
		$data['areas'] = $this->_getAreaMaster();
		$data['content'] = $this->load->view('area/show', $data, true);

		$this->load->view('template', $data);
	}

	private function _getAreaMaster()
	{
		$areaMaster = $this->repository->findMany('tb_master_area', []);

		if ($areaMaster) {
			foreach ($areaMaster as $key => $value) {
				$images = $this->repository->findMany('tb_photo_area', ['area_code' => $value->area_code], 'photo_url');
				$value->photos = [];
				if ($images) {
					foreach ($images as $keyImg => $valImg) {
						$value->photos[] = $valImg->photo_url;
					}
				}
			}
		}

		return $areaMaster;
	}

	public function add()
	{
		$data['title'] = 'Buat Area';
		$data['module'] = 'Area Page';
		$data['content'] = $this->load->view('area/add', null, true);

		$this->load->view('template', $data);
	}

	public function addPhotos($areaCode)
	{
		$data['title'] = 'Foto Area';
		$data['module'] = 'Area Page';
		$data['areaCode'] = $areaCode;
		$data['content'] = $this->load->view('area/add_photo', $data, true);

		$this->load->view('template', $data);
	}

	public function editPhotos($areaCode)
	{
		$data['title'] = 'Foto Area';
		$data['module'] = 'Area Page';
		$data['areaCode'] = $areaCode;
		$data['photos'] = $this->repository->findMany('tb_photo_area', ['area_code' => $areaCode], 'photo_url, id_photo_area');
		$data['content'] = $this->load->view('area/edit_photo', $data, true);

		$this->load->view('template', $data);
	}

	public function create()
	{
		try {
			$post = $this->input->post();

			$headers = $_SERVER;
			$headers['ip_address'] = $this->input->ip_address();

			$save = $this->Area_m->create_area($post['area_name'], $post['open_hours'], $post['close_hours']);

			if ($save['status'] == true) {
				$this->_setFlashdata(true, 'Berhasil Menyimpan Area'); // set flashdata
				$this->_writeLog('AREA_CREATE', true, $post, $headers); // tulis log
				redirect('area/add/photos/' . $save['area_code']);
			}
		} catch (\Throwable $th) {
			$this->_setFlashdata(false, 'Internal Server Error'); // set flashdata

			$post['error'] = $th->getMessage();
			$post['error_line'] = $th->getLine();
			$post['error_file'] = $th->getFile();

			$this->_writeLog('AREA_CREATE', false, $post, $headers); // tulis log
			redirect('area/add');
		}

	}

	public function upload_photos($areaCode)
	{
		try {
			// Proses unggah gambar
			$images = $_FILES['images'];

			$headers = $_SERVER;
			$headers['ip_address'] = $this->input->ip_address();

			// echo '<pre>';
			// print_r($images);
			// return;

			// Validasi jumlah gambar maksimal
			if (count($images['name']) > 5) {
				$this->_setFlashdata(false, 'Anda hanya dapat mengunggah maksimal 5 gambar.');
				redirect('area/add/photos/' . $areaCode);
				return;
			}

			$imagesVessel = []; // vessel untuk nampung base64 imagenya
		
			// Looping untuk setiap gambar yang diunggah
			foreach ($images['name'] as $key => $image_name) {
				if ($image_name == null) {
					$this->_setFlashdata(true, 'Tambah Area Berhasil.');
					redirect('area/master');
					return;
				}

				$temp_name = $images['tmp_name'][$key];

				if (intval($images['size'][$key]) > 1048576) { // kalau image size nya lebih dari 1 MB
					$this->_setFlashdata(false, 'Ukuran foto tidak boleh lebih dari 1 MB.');
					redirect('area/add/photos/' . $areaCode);
					return;
				}
		
				// ambil image extension
				$extension = explode('.', $image_name);
				$extension = $extension[1];

				// Konversi gambar ke base64
				$base64_image = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($temp_name));

				$imagesVessel[] = $base64_image;

				// Simpan base64 image ke database
			}

			$save = $this->Area_m->save_image($areaCode, $imagesVessel); // insert batch
			$post = [];
			if ($save['status'] == true) {
				$this->_setFlashdata(true, 'Berhasil Upload Foto Area.');
				$post['images'] = $images;
				$this->_writeLog('AREA_PHOTO_CREATE', true, $post, $headers);
				redirect('area/add');
				return;
			} else {

			}
		} catch (\Throwable $th) {
			$this->_setFlashdata(false, 'Internal Server Error'); // set flashdata

			$post = [];
			$post['error'] = $th->getMessage();
			$post['error_line'] = $th->getLine();
			$post['error_file'] = $th->getFile();

			$this->_writeLog('AREA_CREATE', false, $post, $headers); // tulis log
			redirect('area/add/photos/' . $areaCode);
		}

	}

	public function update_photos($areaCode)
	{
		try {
			$post = $this->input->post();

			$headers = $_SERVER;
			$headers['ip_address'] = $this->input->ip_address();

			if (isset($post['delete_images'])) {
				foreach ($post['delete_images'] as $key => $value) {
					$delete = $this->_deletePhotos($value);
					if ($delete) {
						$statusLog = true;
						$post['user_input'] = $this->session->user->username;
						$this->_writeLog('DELETE_PHOTO_AREA', $statusLog, $post, $headers);
					} else {
						$statusLog = false;
						$post['user_input'] = $this->session->user->username;
						$this->_writeLog('DELETE_PHOTO_AREA', $statusLog, $post, $headers);

						$this->_setFlashdata(false, 'Delete photo area gagal.');
						redirect('area/edit/photos/' . $areaCode);
					}
				}
			}

			if (!isset($post['images'])) {
				redirect('area/edit/photos/' . $areaCode);
				return;
			}

			// Proses unggah gambar
			$images = $_FILES['images'];

			// echo '<pre>';
			// print_r(count($images['name']));
			// return;

			// Validasi jumlah gambar maksimal
			if (count($images['name']) > 5) {
				$this->_setFlashdata(false, 'Anda hanya dapat mengunggah maksimal 5 gambar.');
				redirect('area/edit/photos/' . $areaCode);
				return;
			}

			$imagesVessel = []; // vessel untuk nampung base64 imagenya
		
			// Looping untuk setiap gambar yang diunggah
			foreach ($images['name'] as $key => $image_name) {
				$temp_name = $images['tmp_name'][$key];

				if (intval($images['size'][$key]) > 1048576) { // kalau image size nya lebih dari 1 MB
					$this->_setFlashdata(false, 'Ukuran foto tidak boleh lebih dari 1 MB.');
					redirect('area/edit/photos/' . $areaCode);
					return;
				}
		
				// ambil image extension
				$extension = explode('.', $image_name);
				$extension = $extension[1];

				// Konversi gambar ke base64
				$base64_image = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($temp_name));

				$imagesVessel[] = $base64_image;

				// Simpan base64 image ke database
			}

			$save = $this->Area_m->save_image($areaCode, $imagesVessel); // insert batch
			if ($save['status'] == true) {
				$this->_setFlashdata(true, 'Berhasil Upload Foto Area.');
				$post['images'] = $images;
				$this->_writeLog('AREA_PHOTO_UPDATE', true, $post, $headers);
				redirect('area/edit/photos/' . $areaCode);
				return;
			} else {

			}
		} catch (\Throwable $th) {
			$this->_setFlashdata(false, 'Internal Server Error'); // set flashdata

			$post['error'] = $th->getMessage();
			$post['error_line'] = $th->getLine();
			$post['error_file'] = $th->getFile();

			$this->_writeLog('AREA_PHOTO_UPDATE', false, $post, $headers); // tulis log
			redirect('area/edit/photos/' . $areaCode);
		}

	}

	private function _deletePhotos($idPhoto)
	{
		$delete = $this->repository->delete('tb_photo_area', ['id_photo_area' => $idPhoto]);

		if ($delete == false) {
			return false;
		} else {
			return true;
		}
	}

	public function edit($areaCode)
	{
		$data['title'] = 'Buat Area';
		$data['module'] = 'Area Page';

		$data['area_edit'] = $this->repository->findFirst('tb_master_area', ['area_code' => $areaCode]);

		$data['content'] = $this->load->view('area/edit', $data, true);

		$this->load->view('template', $data);
	}

	public function update($areaCode)
	{
		$post = $this->input->post();
		$headers = $_SERVER;
		$headers['ip_address'] = $this->input->ip_address();

		try {
			$save = $this->Area_m->update_area($areaCode, $post['area_name'], $post['open_hours'], $post['close_hours'], $post['status']);

			if ($save['status'] == true) {
				$this->_setFlashdata(true, 'Berhasil Menyimpan Area'); // set flashdata
				$this->_writeLog('AREA_UPDATE', true, $post, $headers); // tulis log
			}
		} catch (\Throwable $th) {
			$this->_setFlashdata(false, 'Internal Server Error'); // set flashdata
			$this->_writeLog('AREA_UPDATE', false, $post, $headers); // tulis log
		}

		redirect('area/add/photos/' . $areaCode);
	}

	public function delete($areaCode)
	{
		$logContent = [
			'user_input' => $this->session->user->username,
			'area_code' => $areaCode
		];

		try {
			$delete = $this->Area_m->delete_area($areaCode);
			$headers = $_SERVER;
			$headers['ip_address'] = $this->input->ip_address();

			if ($delete['status'] == false) {
				$this->_setFlashdata(false, $delete['message']);

				$statusLog = false;
			} else {
				$this->_setFlashdata(true, $delete['message']);
				$statusLog = true;
			}
		} catch (\Throwable $th) {
			$this->_setFlashdata(false, 'Internal Server Error'); // set flashdata
		}

		$this->_writeLog('AREA_DELETE', $statusLog, $logContent, $headers); // tulis log

		redirect('area/master');
	}

	public function assign()
	{
		$data['title'] = 'Assign PIC';
		$data['module'] = 'Area Page';
		$data['areas'] = $this->_getAreaMaster();
		$data['findUser'] = base_url('area/find_user');
		$data['findArea'] = base_url('area/find_area');
		$data['content'] = $this->load->view('area/assign_pic', $data, true);

		$this->load->view('template', $data);	
	}

	public function find_user()
	{
		$get = $this->input->get();

		$this->db->select('a.username, c.name');
		$this->db->from('tb_user AS a');
		$this->db->join('tb_profile AS c', 'a.username=c.username');
		$this->db->where('a.role < 3');

		if (isset($get['search']) || !in_array($get['search'], [null, ""])) {
			$this->db->like('a.username', $get['search']);
			$this->db->or_like('c.name', $get['search']);
		}

		$this->db->limit(100);

		$result = $this->db->get()->result();

		$data = [];

		foreach ($result as $key => $value) {
			$data[] = $value->username . ' - ' . $value->name;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function find_area()
	{
		$get = $this->input->get();

		$this->db->select('a.area_code, a.area_name');
		$this->db->from('tb_master_area AS a');

		if (!in_array($get['search'], [null, ""])) {
			$this->db->like('a.area_code', $get['search']);
			$this->db->or_like('a.area_name', $get['search']);
		}

		$this->db->limit(100);

		$result = $this->db->get()->result();

		$data = [];

		foreach ($result as $key => $value) {
			$data[] = $value->area_code . ' - ' . $value->area_name;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function update_pic()
	{
		$post = $this->input->post();
		$headers = $_SERVER;
		$headers['ip_address'] = $this->input->ip_address();

		$area = $post['area'];
		$explodedArea = explode('-', trim($area));
		$area = $explodedArea[0];

		$user = $post['user'];
		$explodedUser = explode('-', trim($user));
		$user = $explodedUser[0];

		$this->db->trans_begin();
		$this->db->update('tb_master_area', ['pic_area' => $user], ['area_code' => $area]);
		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$this->_setFlashdata(false, 'Transaction Failed.');
			$this->_writeLog('AREA_PIC_ASSIGN', false, $post, $headers);
		} else {
			$this->db->trans_commit();
			$this->_setFlashdata(true, 'Assign PIC sukses.');
			$this->_writeLog('AREA_PIC_ASSIGN', true, $post, $headers);
		}

		redirect('area/master');
	}

	public function book()
	{
		$data['title'] = 'Booking Area';
		$data['module'] = 'Area Page';
		$data['areas'] = $this->_getAreaMaster();
		$data['findArea'] = base_url('area/find_area');
		$data['content'] = $this->load->view('area/book', $data, true);

		$this->load->view('template', $data);		
	}

	public function do_booking()
	{
		$post = $this->input->post();
		$headers = $_SERVER;
		try {
			$save = $this->Area_m->saveBooking($post);

			if ($save['success'] == false) {
				$this->_setFlashdata(false, $save['message']);
				$post['message'] = $save['message'];
				$this->_writeLog('AREA_BOOK', false, $post, $headers);
				return redirect('area/book');
			}

			$this->_setFlashdata(true, 'Booking Berhasil Dikirim.');
			$this->_writeLog('AREA_BOOK', true, $post, $headers);
			return redirect('area/book');

		} catch (\Throwable $th) {
			$this->_setFlashdata(false, 'Internal Server Error');
			$post['error_message'] = $th->getMessage();
			$post['error_line'] = $th->getLine();
			$this->_writeLog('AREA_BOOK', false, $post, $headers);

			return redirect('area/book');
		}
	}

}

?>
