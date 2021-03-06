<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Class Auth
* @property Ion_auth|Ion_auth_model $ion_auth        The ION Auth spark
* @property CI_Form_validation      $form_validation The form validation library
*/
class Auth extends CI_Controller
{


    public function __construct()
    {
    	
    	
        parent::__construct();
        $this->load->database();

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));


       
    }

    /**
    * Redirect if needed, otherwise display the user list
    */
    public function index()
    {

        if (!$this->ion_auth->logged_in()) {
            // redirect them to the login page
            redirect('auth/login', 'refresh');
        }
        else
        if (!$this->ion_auth->is_admin()) // remove this elseif if you want to enable this for non - admins
        {
            // redirect them to the home page because they must be an administrator to view this
            return show_error('You must be an administrator to view this page.');
        }
        else {
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            //list the users
            $this->data['users'] = $this->ion_auth->users()->result();
            foreach ($this->data['users'] as $k => $user) {
                $this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
            }

            $this->_render_page('auth/index', $this->data);
        }
    }

    /**
    * Log the user in
    */
    public function login()
    {
        $this->data['title'] = $this->lang->line('login_heading');

        // validate form input
        $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
        $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');

        if ($this->form_validation->run() === TRUE) {
            // check to see if the user is logging in
            // check for "remember me"
            $remember = (bool)$this->input->post('remember');

            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {
                //if the login is successful
                //redirect them back to the home page
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect('/', 'refresh');
            }
            else {
                // if the login was un - successful
                // redirect them back to the login page
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect('auth/login', 'refresh'); // use redirects instead of loading views for compatibility with MY_Controller libraries
            }
        }
        else {
            // the user is not logging in so display the login page
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['identity'] = array('name'    => 'identity',
                'id'      => 'identity',
                'type'    => 'email',
                'class'   =>'form-control',
                'required'=>'require',
                'value'   => $this->form_validation->set_value('identity'),
            );
            $this->data['password'] = array('name' => 'password',
                'id'   => 'password',
                'class'=>'form-control',
                'type' => 'password',
            );

            $this->_render_page('auth/login', $this->data);
        }
    }

    /**
    * Log the user out
    */
    public function logout()
    {
        $this->data['title'] = "Logout";

        // log the user out
        $logout = $this->ion_auth->logout();

        // redirect them to the login page
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        redirect('auth/login', 'refresh');
    }

    /**
    * Change password
    */
    public function change_password()
    {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $user = $this->ion_auth->user()->row();

        if ($this->form_validation->run() === FALSE) {
            // display the form
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
            $this->data['old_password'] = array(
                'name'=> 'old',
                'id'  => 'old',
                'class'=>'form-control',
                'type'=> 'password',
            );
            $this->data['new_password'] = array(
                'name'   => 'new',
                'id'     => 'new', 'class'=>'form-control',
                'type'   => 'password',
                'pattern'=> '^.{' . $this->data['min_password_length'] . '}.*$',
            );
            $this->data['new_password_confirm'] = array(
                'name'   => 'new_confirm',
                'id'     => 'new_confirm',
                'type'   => 'password',
                'pattern'=> '^.{' . $this->data['min_password_length'] . '}.*$',
            );
            $this->data['user_id'] = array(
                'name' => 'user_id',
                'id'   => 'user_id',
                'type' => 'hidden',
                'value'=> $user->id,
            );

            // render
            $this->_render_page('auth/change_password', $this->data);
        }
        else {
            $identity = $this->session->userdata('identity');

            $change   = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

            if ($change) {
                //if the password was successfully changed
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->logout();
            }
            else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect('auth/change_password', 'refresh');
            }
        }
    }

    /**
    * Forgot password
    */
    public function forgot_password()
    {
        // setting validation rules by checking whether identity is username or email
        if ($this->config->item('identity', 'ion_auth') != 'email') {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
        }
        else {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
        }


        if ($this->form_validation->run() === FALSE) {
            $this->data['type'] = $this->config->item('identity', 'ion_auth');
            // setup the input
            $this->data['identity'] = array('name'    => 'identity',
                'id'      => 'identity','type'    => 'email',
                'class'   =>'form-control',
                'required'=>'require',
            );

            if ($this->config->item('identity', 'ion_auth') != 'email') {
                $this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
            }
            else {
                $this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
            }

            // set any errors and display the form
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->_render_page('auth/forgot_password', $this->data);
        }
        else {
            $identity_column = $this->config->item('identity', 'ion_auth');
            $identity        = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();

            if (empty($identity)) {

                if ($this->config->item('identity', 'ion_auth') != 'email') {
                    $this->ion_auth->set_error('forgot_password_identity_not_found');
                }
                else {
                    $this->ion_auth->set_error('forgot_password_email_not_found');
                }

                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect("auth/forgot_password", 'refresh');
            }

            // run the forgotten password method to email an activation code to the user
            $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

            if ($forgotten) {
                // if there were no errors
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
            }
            else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect("auth/forgot_password", 'refresh');
            }
        }
    }

    /**
    * Reset password - final step for forgotten password
    *
    * @param string|null $code The reset code
    */
    public function reset_password($code = NULL)
    {
        if (!$code) {
            show_404();
        }

        $user = $this->ion_auth->forgotten_password_check($code);

        if ($user) {
            // if the code is valid then display the password reset form

            $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

            if ($this->form_validation->run() === FALSE) {
                // display the form

                // set the flash data error message if there is one
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

                $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
                $this->data['new_password'] = array(
                    'name'   => 'new',
                    'id'     => 'new','class'=>'form-control',
                    'type'   => 'password',
                    'pattern'=> '^.{' . $this->data['min_password_length'] . '}.*$',
                );
                $this->data['new_password_confirm'] = array(
                    'name'   => 'new_confirm',
                    'id'     => 'new_confirm',
                    'type'   => 'password','class'=>'form-control',
                    'pattern'=> '^.{' . $this->data['min_password_length'] . '}.*$',
                );
                $this->data['user_id'] = array(
                    'name' => 'user_id',
                    'id'   => 'user_id',
                    'type' => 'hidden',
                    'value'=> $user->id,
                );
                $this->data['csrf'] = $this->_get_csrf_nonce();
                $this->data['code'] = $code;

                // render
                $this->_render_page('auth/reset_password', $this->data);
            }
            else {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id')) {

                    // something fishy might be up
                    $this->ion_auth->clear_forgotten_password_code($code);

                    show_error($this->lang->line('error_csrf'));

                }
                else {
                    // finally change the password
                    $identity = $user->{
                        $this->config->item('identity', 'ion_auth')
                    };

                    $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

                    if ($change) {
                        // if the password was successfully changed
                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        redirect("auth/login", 'refresh');
                    }
                    else {
                        $this->session->set_flashdata('message', $this->ion_auth->errors());
                        redirect('auth/reset_password/' . $code, 'refresh');
                    }
                }
            }
        }
        else {
            // if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }

    /**
    * Activate the user
    *
    * @param int         $id   The user ID
    * @param string|bool $code The activation code
    */
    public function activate($id, $code = FALSE)
    {
        if ($code !== FALSE) {
            $activation = $this->ion_auth->activate($id, $code);
        }
        else
        if ($this->ion_auth->is_admin()) {
            $activation = $this->ion_auth->activate($id);
        }

        if ($activation) {
            // redirect them to the auth page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("auth", 'refresh');
        }
        else {
            // redirect them to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }

    /**
    * Deactivate the user
    *
    * @param int|string|null $id The user ID
    */
    public function deactivate($id = NULL)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            // redirect them to the home page because they must be an administrator to view this
            return show_error('You must be an administrator to view this page.');
        }

        $id = (int)$id;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
        $this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

        if ($this->form_validation->run() === FALSE) {
            // insert csrf check
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->user($id)->row();

            $this->_render_page('auth/deactivate_user', $this->data);
        }
        else {
            // do we really want to deactivate?
            if ($this->input->post('confirm') == 'yes') {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
                    return show_error($this->lang->line('error_csrf'));
                }

                // do we have the right userlevel?
                if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                    $this->ion_auth->deactivate($id);
                }
            }

            // redirect them back to the auth page
            redirect('auth', 'refresh');
        }
    }

    /**
    * Create a new user
    */
    public function create_user()
    {

        $this->data['title'] = $this->lang->line('create_user_heading');
        /*
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
        {
        redirect('auth', 'refresh');
        }*/

        $tables          = $this->config->item('tables', 'ion_auth');
        $identity_column = $this->config->item('identity', 'ion_auth');
        $this->data['identity_column'] = $identity_column;

        if ($identity_column !== 'email') {
            $this->form_validation->set_rules('identity', $this->lang->line('create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identity_column . ']');
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email');
        }
        else {
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]');
        }


        $this->_setFormValidation();
        $this->_setPasswordValidation();

        if ($this->form_validation->run() === TRUE) {
            $email    = strtolower($this->input->post('email'));
            $identity = ($identity_column === 'email') ? $email : $this->input->post('identity');
            $password = $this->input->post('password');

        }
        if ($this->form_validation->run() === TRUE && $this->ion_auth->register($identity, $password, $email, $this->_additionalData())) {
            // check to see if we are creating the user
            // redirect them back to the admin page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("auth", 'refresh');
        }
        else {
            // display the create user form
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));


            $this->_setInputs(null);
            $this->_render_page('auth/create_user', $this->data);
        }
    }

    /**
    * Edit a user
    *
    * @param int|string $id
    */
    public function edit_user()
    {

        if (!$this->ion_auth->logged_in()) {
            redirect(base_url());
        }
        
         if ($this->input->post('password')) {
                $this->_setPasswordValidation();
        } 
        
        $row = $this->ion_auth->user()->row();
        $id  = $row->id;

        $this->data['title'] = $this->lang->line('edit_user_heading');

        if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id))) {
            redirect('auth', 'refresh');
        }

        $user          = $this->ion_auth->user($id)->row();
        $groups        = $this->ion_auth->groups()->result_array();
        $currentGroups = $this->ion_auth->get_users_groups($id)->result();

        $this->_setFormValidation();




        if (isset($_POST) && !empty($_POST)) {
            // do we have a valid request?
            if ($this->_valid_csrf_nonce() === FALSE ) {
            	$this->ion_auth->set_error($this->lang->line('error_csrf'));
               
            }

            // update the password if it was posted
           

            if ($this->form_validation->run() === TRUE) {
                $data = $this->_additionalData();

                // update the password if it was posted
                if ($this->input->post('password')) {
                    $data['password'] = $this->input->post('password');
                }
                
                $data['email'] = $this->input->post('email');

                // Only allow updating groups if user is admin
                if ($this->ion_auth->is_admin()) {
                    // Update the groups user belongs to
                    $groupData = $this->input->post('groups');

                    if (isset($groupData) && !empty($groupData)) {

                        $this->ion_auth->remove_from_group('', $id);

                        foreach ($groupData as $grp) {
                            $this->ion_auth->add_to_group($grp, $id);
                        }

                    }
                }

                // check to see if we are updating the user
                if ($this->ion_auth->update($user->id, $data)) {
                    // redirect them back to the admin page if admin, or to the base url if non admin
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    redirect(base_url('auth/edit_user'), 'refresh');
                }
                else {
                    // redirect them back to the admin page if admin, or to the base url if non admin
                    $this->session->set_flashdata('message', $this->ion_auth->errors());
                    
                }

            }
        }

        // display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        // set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

        // pass the user to the view
        $this->data['user'] = $user;
        $this->data['groups'] = $groups;
        $this->data['currentGroups'] = $currentGroups;

        $this->_setInputs((array)$this->data['user']);
        $this->_render_page('auth/edit_user', $this->data);
    }



    /**
    * @return array A CSRF key-value pair
    */
    public function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key   = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key=> $value);
    }

    /**
    * @return bool Whether the posted CSRF token matches
    */
    public function _valid_csrf_nonce()
    {
        $csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
        if ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue')) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    /**
    * @param string     $view
    * @param array|null $data
    * @param bool       $returnhtml
    *
    * @return mixed
    */
    public function _render_page($view, $data = NULL, $returnhtml = FALSE)//I think this makes more sense
    {

        $this->viewdata = (empty($data)) ? $this->data : $data;

        $view_html = $this->load->view($view, $this->viewdata, $returnhtml);

        // This will return html on 3rd argument being true
        if ($returnhtml) {
            return $view_html;
        }
    }

    protected function _setPasswordValidation()
    {

        $this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
    }

    protected function _setFormValidation()
    {

        $this->form_validation->set_rules('first_name', lang('create_user_validation_fname_label'), 'trim|required|min_length[5]|max_length[50]');
        $this->form_validation->set_rules('last_name', lang('create_user_validation_lname_label'), 'trim|required|min_length[5]|max_length[50]');
        $this->form_validation->set_rules('about', 'max 500', 'trim|max_length[500]');
   		$this->form_validation->set_rules('img',null, 'trim');
   		$this->form_validation->set_rules('birthday', null, 'trim');
    }

    protected function _setInputs($user = NULL)
    {

        $this->data['first_name'] = array(
            'name'    => 'first_name',
            'id'      => 'first_name',
            'class'   =>'form-control',
            'type'    => 'text',
            'required'=>'require',
            'value'   => $this->form_validation->set_value('first_name',$user['first_name']),
        );
        $this->data['last_name'] = array(
            'name'    => 'last_name',
            'id'      => 'last_name',
            'class'   =>'form-control',
            'type'    => 'text',
            'required'=>'require',
            'value'   => $this->form_validation->set_value('last_name',$user['last_name']),
        );

        $this->data['email'] = array(
            'name'    => 'email',
            'id'      => 'email',
            'type'    => 'email',
            'required'=>'require','class'   =>'form-control',
            'value'   => $this->form_validation->set_value('email',$user['email']),
        );


        $this->data['birthday'] = array(
            'name' => 'birthday',
            'id'   => 'birthday',
            'type' => 'date',
            'class'=>'form-control',
            'value'=> $this->form_validation->set_value('birthday',$user['birthday']),
        );

        $this->data['about'] = array(
            'name' => 'about','id'   => 'about','class'=>'form-control',
            'value'=> $this->form_validation->set_value('about',$user['about']),
        );

        $this->data['password'] = array(
            'name' => 'password',
            'id'   => 'password',
            'type' => 'password','class'=>'form-control',"autocomplete"=>"off",
            'value'=> $this->form_validation->set_value('password'),
        );
        $this->data['password_confirm'] = array(
            'name' => 'password_confirm',
            'id'   => 'password_confirm',"autocomplete"=>"off",
            'type' => 'password','class'=>'form-control',
            'value'=> $this->form_validation->set_value('password_confirm'),
        );
        
        $this->data['img'] = array(
            'name' => 'img',
            'id'   => 'img',
            'type' => 'hidden',
            'class'=>'form-control',"autocomplete"=>"off",
            'value'=> $this->form_validation->set_value('img',$user['img']),
        );
        
         $this->data['img_upload'] = array(
            'id'   => 'img_upload',
            'type' => 'file',
            'class'=>'form-control',"autocomplete"=>"off",
            
        );
        
        $this->data['src'] = $user['img'];
    }


    protected function _additionalData() : array
    {

        return [
            'first_name'=> $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'about'     =>$this->input->post('about'),
            'birthday'  =>$this->input->post('birthday'),
            'img' =>$this->input->post('img'),
        ];
    }
}
