<?php

/**
 * @cont:	Run_CRON
 * 
 * @desc:	non-web controller
 * 
 * @site:	www.example.com
 * @author:	Michael Pope
 * 			nyndesigns.com
 *
 * @file:	run_cron.php
 * @date:	10.03.2011 - 11:53:06  [Michael Pope]
 */


class Run_CRON extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		if(!defined( 'CRON' ) )  
		  exit();
// 		$this->load->model('Default/cron');
	}


	function __destruct()
	{
	}
		
	
	/**
	 * Index
	 * 
	 * @desc not used
	 */
	function index()
	{	
echo "hiiii in index fuinction"; exit;
	}
	
	
	/**
	 * All
	 */
	function all()
	{
echo "hhhhhh"; exit;
		$this->generate_sitemap();
	}
	

	/**
	 * Generate Sitemap XML
	 */		
	function generate_sitemap()
	{	
		// Live Mode:
		if( ! CRON_BETA_MODE )
			$cron_id = $this->cron->create('Sitemap (Google|Bing|Ask|Yahoo!)');
		
		// Example Code
		
		$this->load->library('sitemap');
		$this->load->config('sitemap');

		// ...
		// ...
		// ...

		// Sandbox Mode:
		if( CRON_BETA_MODE )
			$this->sitemap->generate_xml(null, false);
			
		// Live Mode:
		else {
			echo 'live';
		
			$this->sitemap->generate_xml();
			$this->cron->update( $cron_id );
		}
		
	}
}
/* End of file run_cron.php */
/* Location: ./application/controllers/run_cron.php */
