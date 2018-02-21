<?php

class Usermodel extends CI_Model 
{
    function Usermodel()
    {     
        parent::__construct();      
    }
	
	function edit_account($data)
	{
           echo "<pre>"; 
//           print_r($data);
                     print_r($data);
//            exit;
            $updatedata=array(
                "language"=>$data['language'],
                "company_name"=>$data['company'],
                "province"=>$data['state'],
                "email"=>$data['email'],
                "first_name"=>$data['name'],
                "city"=>$data['city'],
                 "telephone_1"=>$data['telephone'],
                 "address_1"=>$data['Address'],
                 "country"=>$data['country'],
                 "postal_code"=>$data['code'],
                "tz"=>$data['timezone']
                );
                $this->db->update("accounts",$updatedata);
                return;
                
	}
        function get_account($name)
	{
   
            $this->db->where("number",$name);
            $detail=$this->db->get("accounts");
            return $detail;
        }
}
?>
