<?php
// ##############################################################################
// ASTPP - Open Source VoIP Billing Solution
//
// Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
// Samir Doshi <samir.doshi@inextrix.com>
// ASTPP Version 3.0 and above
// License https://www.gnu.org/licenses/agpl-3.0.html
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>.
// ##############################################################################
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

class Activity_report_form extends common
{

    function __construct()
    {
        $this->CI = & get_instance();
    }

    function build_activity_report_list_for_admin()
    {
        $grid_field_arr = json_encode(array(
            array(
                gettext("Account"),
                "200",
                "accountid",
                "first_name,last_name,number",
                "accounts",
                "build_concat_string"
            ),
            array(
                gettext("Debit"),
                "180",
                "total1",
                "total1",
                "total1",
                "convert_to_currency"
            ),
            array(
                gettext("Cost"),
                "180",
                "total",
                "total",
                "total",
                "convert_to_currency"
            ),
            array(
                gettext("Last DID Call date"),
                "250",
                "callstart",
                "callstart",
                "callstart",
                "convert_GMT_to"
            ),
            array(
                gettext("Last Outgoing Call date"),
                "250",
                "callstart",
                "callstart",
                "callstart",
                "convert_GMT_to"
            ),
            array(
                gettext("Last Call Date"),
                "180",
                "callstart",
                "callstart",
                "callstart",
                "convert_GMT_to"
            ),
            array(
                gettext("Balance"),
                "130",
                "callstart",
                "callstart",
                "callstart",
                "convert_GMT_to"
            ),
            array(
                gettext("Credit limit"),
                "130",
                "callstart",
                "callstart",
                "callstart",
                "convert_GMT_to"
            )
        ));
        return $grid_field_arr;
    }

    function build_grid_activity_report_buttons()
    {
        $buttons_json = json_encode(array(
            array(
                gettext("Export"),
                "btn btn-xing",
                " fa fa-download fa-lg",
                "button_action",
                "/activity_report/activity_report_export_cdr_xls",
                'single'
            )
        ));
        return $buttons_json;
    }

    function get_activity_report_form()
    {
        $form['forms'] = array(
            "",
            array(
                'id' => "activity_search"
            )
        );
        $form['Search'] = array(
            array(
                gettext('From Date'),
                'INPUT',
                array(
                    'name' => 'callstart[]',
                    'id' => 'customer_from_date',
                    'size' => '20',
                    'class' => "text field "
                ),
                '',
                'tOOL TIP',
                '',
                'start_date[start_date-date]'
            ),
            array(
                gettext('To Date'),
                'INPUT',
                array(
                    'name' => 'callstart[]',
                    'id' => 'customer_to_date',
                    'size' => '20',
                    'class' => "text field "
                ),
                '',
                'tOOL TIP',
                '',
                'end_date[end_date-date]'
            ),
            array(
                gettext('Account'),
                array(
                    'name' => 'reseller_id'
                ),
                'SELECT',
                '',
                'Account Search',
                'tOOL TIP',
                'Please Enter account number',
                'id',
                'first_name,last_name,number',
                'accounts',
                'build_concat_dropdown_reseller',
                'where_arr',
                ''
            ),
            array(
                '',
                'HIDDEN',
                'ajax_search',
                '1',
                '',
                '',
                ''
            ),
            array(
                '',
                'HIDDEN',
                'advance_search',
                '1',
                '',
                '',
                ''
            )
        );
        $form['button_search'] = array(
            'name' => 'action',
            'id' => "activity_search_btn",
            'content' => 'Search',
            'value' => gettext('save'),
            'type' => 'button',
            'class' => 'btn btn-line-parrot pull-right'
        );
        $form['button_reset'] = array(
            'name' => 'action',
            'id' => "id_reset",
            'content' => 'Clear',
            'value' => gettext('cancel'),
            'type' => 'reset',
            'class' => 'btn btn-line-sky pull-right margin-x-10'
        );

        return $form;
    }
}

?>
