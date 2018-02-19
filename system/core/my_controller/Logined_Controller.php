<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Application Controller Class
*
* This class object is the super class that every library in
* CodeIgniter will be assigned to.
*
* @package        CodeIgniter
* @subpackage    Libraries
* @category    Libraries
* @author        EllisLab Dev Team
* @link        https://codeigniter.com/user_guide/general/controllers.html
*/
class Logined_Controller  extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in())
       		 redirect(base_url('auth'));
    }

}
