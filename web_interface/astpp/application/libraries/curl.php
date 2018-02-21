<?php

/**
* OO cURL Class
* Object oriented wrapper for the cURL library.
* @author David Hopkins (semlabs.co.uk)
* @version 0.3
*/
class Curl
{	
	public $sessions=array();
	public $retry=0;	
	public $opts = array(
					CURLOPT_USERAGENT=>"Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1",
					CURLOPT_FOLLOWLOCATION=>1,
					CURLOPT_RETURNTRANSFER=>1,
					CURLOPT_AUTOREFERER=> true );	
	
	//public $perl_base_url = "http://vm8.pssvoip.co.za/cgi-bin/astpp-admin/";	
	//$this->config->base_url();
	public $perl_base_url = "cgi-bin/astpp-admin/";
   
	/**
	* Adds a cURL session to stack
	* @param $url string, session's URL
	* @param $opts array, optional array of cURL options and values
	*/
	
	public function addSession($url,$method='GET',$encoding=false,$ssl=false,$post_data="",$opts=false)
	{		
		if(is_array($url))
		{
			foreach($url as $uri)
			{
				$this->sessions[] = curl_init($uri);
			}
		}
		else
		{
			$this->sessions[] = curl_init($url);
		}
		
		
		if( $opts == false)
		$opts = $this->opts;
		
		if($method == 'POST')
		{
			$opts[CURLOPT_POST] = 1;
        	$opts[CURLOPT_POSTFIELDS]=$post_data;
		}
		
		if($ssl == true)
		{
			 $opts[CURLOPT_SSL_VERIFYPEER] = false;
		}
		
		if($encoding)
		{
			$opts[CURLOPT_ENCODING]= $encoding;
		}
		
		$this->setOpts($opts);
	}
	
	/**
	* Sets an option to a cURL session
	* @param $option constant, cURL option
	* @param $value mixed, value of option
	* @param $key int, session key to set option for
	*/
	public function setOpt( $option, $value, $key = 0 )
	{
		curl_setopt( $this->sessions[$key], $option, $value );
	}
	
	/**
	* Sets an array of options to a cURL session
	* @param $options array, array of cURL options and values
	* @param $key int, session key to set option for
	*/
	public function setOpts($options)
	{
		foreach($this->sessions as $key=>$session)
		{
			curl_setopt_array( $this->sessions[$key], $options );
		}
	}
	
	/**
	* Executes as cURL session
	* @param $key int, optional argument if you only want to execute one session
	*/
	public function exec( $key = false )
	{
		$no = count( $this->sessions );
		
		if( $no == 1 )
			$res = $this->execSingle();
		elseif( $no > 1 ) {
			if( $key === false )
				$res = $this->execMulti();	
			else
				$res = $this->execSingle( $key );
		}
		
		if( $res )
			return $res;
	}
	
	/**
	* Executes a single cURL session
	* @param $key int, id of session to execute
	* @return array of content if CURLOPT_RETURNTRANSFER is set
	*/
	public function execSingle( $key = 0 )
	{
		if( $this->retry > 0 )
		{
			$retry = $this->retry;
			$code = 0;
			while( $retry >= 0 && ( $code[0] == 0 || $code[0] >= 400 ) )
			{
				$res = curl_exec( $this->sessions[$key] );
				$code = $this->info( $key, CURLINFO_HTTP_CODE );
				
				$retry--;
			}
		}
		else
			$res = curl_exec( $this->sessions[$key] );
		
		return $res;
	}
	
	/**
	* Executes a stack of sessions
	* @return array of content if CURLOPT_RETURNTRANSFER is set
	*/
	public function execMulti()
	{
		$mh = curl_multi_init();
		
		#Add all sessions to multi handle
		foreach ( $this->sessions as $i => $url )
			curl_multi_add_handle( $mh, $this->sessions[$i] );
		
		do
			$mrc = curl_multi_exec( $mh, $active );
		while ( $mrc == CURLM_CALL_MULTI_PERFORM );
		
		while ( $active && $mrc == CURLM_OK )
		{
			if ( curl_multi_select( $mh ) != -1 )
			{
				do
					$mrc = curl_multi_exec( $mh, $active );
				while ( $mrc == CURLM_CALL_MULTI_PERFORM );
			}
		}

		if ( $mrc != CURLM_OK )
			echo "Curl multi read error $mrc\n";
		
		#Get content foreach session, retry if applied
		foreach ( $this->sessions as $i => $url )
		{
			$code = $this->info( $i, CURLINFO_HTTP_CODE );
			if( $code[0] > 0 && $code[0] < 400 )
				$res[] = curl_multi_getcontent( $this->sessions[$i] );
			else
			{
				if( $this->retry > 0 )
				{
					$retry = $this->retry;
					$this->retry -= 1;
					$eRes = $this->execSingle( $i );
					
					if( $eRes )
						$res[] = $eRes;
					else
						$res[] = false;
						
					$this->retry = $retry;
					echo '1';
				}
				else
					$res[] = false;
			}

			curl_multi_remove_handle( $mh, $this->sessions[$i] );
		}

		curl_multi_close( $mh );
		
		return $res;
	}
	
	/**
	* Closes cURL sessions
	* @param $key int, optional session to close
	*/
	public function close( $key = false )
	{
		if( $key === false )
		{
			foreach( $this->sessions as $session )
				curl_close( $session );
		}
		else
			curl_close( $this->sessions[$key] );
	}
	
	/**
	* Remove all cURL sessions
	*/
	public function clear()
	{
		foreach( $this->sessions as $session )
			curl_close( $session );
		unset( $this->sessions );
	}
	
	/**
	* Returns an array of session information
	* @param $key int, optional session key to return info on
	* @param $opt constant, optional option to return
	*/
	public function info( $key = false, $opt = false )
	{
		if( $key === false )
		{
			foreach( $this->sessions as $key => $session )
			{
				if( $opt )
					$info[] = curl_getinfo( $this->sessions[$key], $opt );
				else
					$info[] = curl_getinfo( $this->sessions[$key] );
			}
		}
		else
		{
			if( $opt )
				$info[] = curl_getinfo( $this->sessions[$key], $opt );
			else
				$info[] = curl_getinfo( $this->sessions[$key] );
		}
		
		return $info;
	}
	
	/**
	* Returns an array of errors
	* @param $key int, optional session key to retun error on
	* @return array of error messages
	*/
	public function error( $key = false )
	{
		if( $key === false )
		{
			foreach( $this->sessions as $session )
				$errors[] = curl_error( $session );
		}
		else
			$errors[] = curl_error( $this->sessions[$key] );
			
		return $errors;
	}
	
	/**
	* Returns an array of session error numbers
	* @param $key int, optional session key to retun error on
	* @return array of error codes
	*/
	public function errorNo( $key = false )
	{
		if( $key === false )
		{
			foreach( $this->sessions as $session )
				$errors[] = curl_errno( $session );
		}
		else
			$errors[] = curl_errno( $this->sessions[$key] );
			
		return $errors;
	}
	
	function sendRequestToPerlScript($function,$data)
	{
		//$this->ci =& get_instance();
		//$this->ci->load->config('perl_auth', TRUE);
		$this->ci =& get_instance();
    	$this->ci->config->load('config');
	    $this->perl_url = $this->ci->config->item('perl_url');		
		//echo $perl_base_url = $this->config->item('perl_url', 'perl_auth');
		//exit;
		//$url = base_url().$this->perl_base_url . $function;	
			
		$url = $this->perl_url . $function;
		
		$post_data = "";
		foreach($data as $filed=>$value)
		{
			$post_data .= "&".$filed."=".$value;
		}

		$post_data = substr($post_data,1);
		$this->addSession($url,'POST',false,false,$post_data);
	   	$result = $this->exec();
		$this->clear();
		return strip_tags($result);
	}
}

?>
