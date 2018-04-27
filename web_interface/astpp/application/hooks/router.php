<?php
if(!defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Router {
      public static $_domain    = '';
      public function route() {
        if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] != ''){
          require_once(BASEPATH.'database/DB.php');
          $db = DB(); // getting hold of a DAO instance
          $db->select("domain");
          $db->where(array("domain"=>$_SERVER["HTTP_HOST"]));
          $res = $db->get("invoice_conf");
          $domain = $res->result();
          if(!empty($domain) && $domain[0]->domain != ""){
            // Do not specify a protocol. This makes the User Agent use the
            // current protocol (http or https)
            self::$_domain = "//".$domain[0]->domain."/";
          }
        }
      }

      public function config() {
          if(self::$_domain != ""){
            $RTR = load_class('Router','core');
            $RTR->config->config['base_url'] = self::$_domain;
            return true;
          }
      }
}

