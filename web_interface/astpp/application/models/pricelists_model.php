<?php

class Pricelists_model extends CI_Model 
{
	
	// ------------------------------------------------------------------------
	/**
	 * initialises the class inheriting the methods of the class Model 
	 *
	 * @return Usermodel
	 */
    function Pricelists_model()
    {     
        parent::__construct();
        
        //FreakAuth_light table prefix
        $this->_prefix = '';     
        $this->_table = $this->_prefix.'pricelists';
    }
	//-------------------------------------------------------------------------
/*	
	sub list_pricelists() {
    my ( $astpp_db, $reseller ) = @_;
    my ( $sql, @pricelistlist, $row, $tmp );
    if ( !$reseller ) {
        $tmp =
"SELECT name FROM pricelists WHERE status < 2 AND reseller IS NULL ORDER BY name";
    }
    else {
        $tmp =
            "SELECT name FROM pricelists WHERE status < 2 AND reseller = "
          . $astpp_db->quote($reseller)
          . " ORDER BY name";
    }
*/    
    function getLists()
    {	
        	//SELECT id, name FROM country
            $this->db->select('name');
            $query = $this->db->get($this->_table);
            
            $options = array();
            
            $options['0']="-----------------";
           	
            foreach ($query->result() as $row)
           	{
                    $options[$row->{'name'}] = $row->{'name'};
           	}
            
           	$query->free_result();
           	   
           	return $options;
               
    }
    // ------------------------------------------------------------------------
    
    /**
     * From the parameter $id from the table user 
     * retrieves the username and returns it as a string
     * 
     * @param integer $id
     * @return string (username)
     */
	function getUserProfileById($id)
	{	
		//SELECT name FROM user WHERE id = $id 
		$this->db->where('id', $id);
        
		//returns the username
		return $this->db->get($this->_table);
	}
	
		
	// ------------------------------------------------------------------------
	
	/**
	 * After pressing the activation link in the e-mail gets the user_temp fields and inserts the values into the user table
	 * (new registered user)
	 *
	 * @param unknown_type $id
	 */
	function insertUserProfile($data)
	{
        $this->db->insert($this->_table, $data);
	}
	
    
    // ------------------------------------------------------------------------
    
    function getTableFields()
    {
    	return $this->db->list_fields($this->_table);
    }
    
    // ------------------------------------------------------------------------
    
    function updateUserProfile($id, $data)
    {	
    	$this->db->where('id', $id);
    	$this->db->update($this->_table, $data);
    }

	    // ------------------------------------------------------------------------
	
	/**
	 * deletes the user by 'id' field
	 *
	 * @param profile id field 
	 */
	function deleteUserProfile($id)
	{
        $this->db->where('id', $id);
        $this->db->delete($this->_table);
	}
	
	
	
}
?>