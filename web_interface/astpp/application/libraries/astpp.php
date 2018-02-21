<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Astpp {

	public $CI;
	public $form;
	/**
	 * Constructor
	 *
	 * Loads the astpp file and sets the default reference
	 */
	public function __construct($config = array())
	{
		$this->CI =& get_instance();
		
		if (is_null($this->form))
		{
			$this->CI->load->library('form');
			$this->form = new Form();	

			$this->CI->load->database();
			
		}
		
		if (count($config) > 0)
		{
			$this->initialize($config);
		}

		log_message('debug', "Astpp Class Initialized");
	}
	// --------------------------------------------------------------------

	/**
	 * Initialize the user preferences
	 *
	 * Accepts an associative array as input, containing display preferences
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */
	function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
	}
	// --------------------------------------------------------------------
	
	
}


?>