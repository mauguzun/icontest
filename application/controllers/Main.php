<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends Logined_Controller {




	private $_url = "https://www.ss.com/lv/real-estate/flats/riga/centre/rss/";
	
	public function __construct(){
		
		parent::__construct();
	}

	public function index()
	{
		$this->load->view('parts/header',['user'=> $this->ion_auth->user()->row()]);
		$feed = @simplexml_load_file($this->_url);
	    $this->load->view('parts/page',['feed'=>$feed]);
	}
}
