<?php

// ##############################################################################
// ASTPP - Open Source VoIP Billing Solution
// ##############################################################################

class Payments_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_pay_attrs($inn) {
        $res = array(
                'error' => 0,
                'error_str' => '',
              );

        $q_pay_attrs = $this->db_model->getSelect("id, company_name, inn, bank_rs, bank_name, bank_kpp, bank_bik", "accounts", array('inn' => $inn));

        if ($q_pay_attrs->num_rows() == 1) {
            $res = ($q_pay_attrs->result_array())[0];
        } else if ($q_pay_attrs->num_rows() > 1) {
            $res['error'] = 150;
            $res['error_str'] = gettext('Too many users found');
        } else {
            $res['error'] = 100;
            $res['error_str'] = gettext('User not found');
        }

        return $res;
    }
}
?>
