<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function index()
	{
		$this->load->view('OK/tester');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */