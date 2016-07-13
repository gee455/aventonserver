<?php

class Upload extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
    }

    function index()
    {
        $this->load->view('master/upload_form', array('error' => ' ' ));
    }

    function do_upload()
    {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png';
//        $config['max_size']	= '1000';
//        $config['max_width']  = '1024';
//        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        $count = count($_FILES['userfile']['size']);

        foreach($_FILES as $key=>$value)
            for($s=0; $s<=$count-1; $s++) {
                $_FILES['userfile']['name'] = $value['name'][$s];
                $_FILES['userfile']['type'] = $value['type'][$s];
                $_FILES['userfile']['tmp_name'] = $value['tmp_name'][$s];
                $_FILES['userfile']['error'] = $value['error'][$s];
                $_FILES['userfile']['size'] = $value['size'][$s];

                if ( ! $this->upload->do_upload())
                {
                    $error = array('error' => $this->upload->display_errors());

                    $this->load->view('master/upload_form', $error);
                }
                else
                {
                    $data = array('upload_data' => $this->upload->data());

                    $this->load->view('master/upload_success', $data);
                }
            }



    }
}
?>