<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package    CodeIgniter - Android Apps
 * @category   AndroidApps
 * @author     Jahid Hasan <jahid@logicsoftbd.com>
 * @copyright  2017 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';

class Apps extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('android_model');
    }

    function login_get() {
        if (!$this->get('user_id')) {
            $this->response('UserID Is Required', 400);
        }
        if (!$this->get('pwd')) {
            $this->response('Password Is Required', 400);
        }

        $userInfo = $this->android_model->login($this->get('user_id'), $this->get('pwd'));
        $status = true;
        if (empty($userInfo)) {
            $status = false;
        }
        $response = array(
            'status' => $status,
            'list' => $userInfo
        );
        if ($response) {
            $this->response($response, 200);
        } else {
            $this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
        }
    }

    function chart_get() {
        $userInfo = $this->android_model->get_chart_by_ordership($this->get('company_id'), $this->get('location'));

        $status = true;
        if (empty($userInfo)) {
            $status = false;
        }
        $response = array(
            'status' => $status,
            'list' => $userInfo
        );
        if ($response) {
            $this->response($response, 200);
        } else {
            $this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
        }
    }

    function ontime_delivery_get() {
        $userInfo = $this->android_model->get_chart_by_ontime_delivery($this->get('company_id'), $this->get('location'));
        $status = true;
        if (empty($userInfo)) {
            $status = false;
        }
        $response = array(
            'status' => $status,
            'list' => $userInfo
        );
        if ($response) {
            $this->response($response, 200);
        } else {
            $this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
        }
    }

}
