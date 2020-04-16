<?php
/**
 * Date: 12.04.20
 * Description: Yandex.Money API for ASTPP Billing
 * Ref Documentation: https://yandex.ru/dev/money/doc/dg/concepts/About-docpage/
 */

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

class Ym_pgateway extends CI_Model {

    private $BASE_URL    = '';
    private $CLIENT_ID   = '';
    private $LOG_FN      = '';
    private $TRANSACTION_INSTANCE = null;
    private $YM_WALLET   = '';
    private $URL_SUCCESS = '';
    private $URL_FAIL    = '';

/*
 * Class constructor
 */
    function __construct() {
        parent::__construct();

        $this->read_config();

        $this->getInstance();
    }

/*
 * Add to log function
 */
    private function Addtolog ($msg) {
        $msg = date("d.m.Y H:i:s")."\t".$msg."\n";
        file_put_contents($this->LOG_FN, $msg, FILE_APPEND);
    }

/*
 *  Read config 
 */
    private function read_config () {
        $config_set = $this->db_model->getSelect("name, value", "ym_config", array());

        if ($config_set->num_rows > 0) {
            $config_set = $config_set->result_array();
            foreach ($config_set as $k => $v){
                if ( $v['name'] == 'base_url' ){
                    $this->BASE_URL = $v['value'];
                }
                if ( $v['name'] == 'client_id' ){
                    $this->CLIENT_ID = $v['value'];
                }
                if ( $v['name'] == 'log_file' ){
                    $this->LOG_FN = $v['value'];
                }
                if ( $v['name'] == 'wallet_id' ){
                    $this->YM_WALLET = $v['value'];
                }
            }
        }
    }

/*
 *  Base HTTP request
 */
    private function baseHttpRequest ( $method, $str_param ) {
        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded'
        );

       $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL,            $this->BASE_URL.$method );
        curl_setopt( $ch, CURLOPT_POST,           true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER,     $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $str_param);

        $result['answer'] = curl_exec($ch);
        $result['info']   = curl_getinfo($ch);

        if ( !in_array($result['info']['http_code'], [200, 302]) ) {
            $this->Addtolog('HTTP request failed with error: '.$result['info']['http_code']);
            $result['answer'] = null;
        }

        curl_close($ch);

        return $result;
    }

/*
 * Check register instance
 */
    private function getInstance () {
        $fun_result = false;
        $req_result = $this->baseHttpRequest('/api/instance-id', 'client_id='.$this->CLIENT_ID);

        if ( null != $req_result['answer'] ) {
            $answer = json_decode($req_result['answer']);

            if ($answer->status == 'success') {
                $this->TRANSACTION_INSTANCE = $answer->instance_id;
                $fun_result = true;
            } else {
                $this->Addtolog('API method instance-id return error: '.$answer->error);
            }
        }

        return $fun_result;
    }

    private function requestExternalPayment ( $psum, $msg = '' ) {
        $req_result = null;

        if ( null !== $this->TRANSACTION_INSTANCE && floatval($psum)>0 ) {
            $req_params = 'pattern_id=p2p&instance_id='.$this->TRANSACTION_INSTANCE.'&to='.$this->YM_WALLET.'&amount='.$psum.'&message='.$msg;

            $req_result = $this->baseHttpRequest('/api/request-external-payment', $req_params);

            if ( null != $req_result['answer'] ) {
                $req_result = json_decode($req_result['answer']);
                
                if ($req_result->status == 'refused') {
                    $this->Addtolog('API method request-external-payment return error: '.$req_result->error);
                }
            } else {
                $this->Addtolog('API method request-external-payment return null answer.');
            }
        } else {
            $this->Addtolog('API method request-external-payment error init params.');
        }

        return $req_result;
    }

    private function processExternalPayment ($rid) {
        $req_result = null;

        if (null !== $rid) {
            $req_params = 'request_id='.$rid.'&instance_id='.$this->TRANSACTION_INSTANCE.'&ext_auth_success_uri='.$this->URL_SUCCESS.'&ext_auth_fail_uri='.$this->URL_FAIL;

            $req_result = $this->baseHttpRequest('/api/process-external-payment', $req_params);

            if ( null != $req_result['answer'] ) {
                $req_result = json_decode($req_result['answer']);

                if ($req_result->status == 'refused') {
                    $this->Addtolog('API method process-external-payment return error: '.$req_result->error);
                }
            } else {
                $this->Addtolog('API method process-external-payment return null answer.');
            }
        } else {
            $this->Addtolog('API method process-external-payment error init params.');
        }

        return $req_result;
    }

    private function db_AddOrder($uid, $psum, $msg, $rid, $status) {
        $order_params = array(
                'uid'           => $uid,
                'psum'          => $psum,
                'pmsg'          => $msg,
                'ym_instance'   => $this->TRANSACTION_INSTANCE,
                'ym_request'    => $rid,
                'ym_status'     => $status
            );

        return $this->db->insert('ym_orders', $order_params);
    }

    private function prepareRedirectURL($base, $params) {
        $url = $base.'?';

        foreach ($params as $k => $v) {
            $url .= $k.'='.$v.'&';
        }

        return substr($url, 0, -1);
    }

    public function placeOrder($uid, $psum, $msg = '') {
        $redirectUrl = null;

        if ( intval($uid)>0 && floatval($psum)>0 ){
            $req_rep = $this->requestExternalPayment($psum, $msg);
            if (property_exists($req_rep, 'status') && $req_rep->status == 'success'){
                $req_pep = $this->processExternalPayment($req_rep->request_id);
                if (property_exists($req_pep, 'status') && ($req_pep->status == 'success' || $req_pep->status == 'ext_auth_required') ){
                    $this->db_AddOrder($uid, $psum, $msg, $req_rep->request_id ,$req_pep->status);
                    $redirectUrl = $this->prepareRedirectURL($req_pep->acs_uri, $req_pep->acs_params);
                }
            }
        } else {
        }

        return $redirectUrl;
    }

    public function ready() {
        return null !== $this->TRANSACTION_INSTANCE;
    }

    public function setCBUrl ($params) {
        $this->URL_SUCCESS = $params['urlSuccess'] ? $params['urlSuccess'] : '';
        $this->URL_FAIL    = $params['urlFail']    ? $params['urlFail']    : '';

        return true;
    }

    public function test () {
        $this->Addtolog('Test on Air.');
    }
}
?>