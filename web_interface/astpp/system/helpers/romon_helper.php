<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------
	function variable_value($field_name)
	{
		$field_value = '';
    	if( $_SERVER['REQUEST_METHOD'] == 'GET')
    	{
        	if(isset($_GET[$field_name]))
        	{
            	$field_value = $_GET[$field_name];
        	}
    	}
    	if( $_SERVER['REQUEST_METHOD'] == 'POST')
    	{
        	if(isset($_POST[$field_name]) )
        	{
            	$field_value = $_POST[$field_name];
        	}
    	}
    	
    	return $field_value;
	}

if ( ! function_exists('romon_form_render'))
{
	function romon_form_render($label,$data,$order,$legend='',$closetable=0,$addtable=0)
	{
		$retStr = '';
		if($closetable)
		{
			$retStr .= "</table></fieldset>\n";
		}
		if($legend){
			$retStr .= '<fieldset><legend>'.$legend.'</legend>';
			$retStr .= "<table align=center>\n";
		}		
		else 
		{
			if($addtable)
			{
				$retStr .= "<table align=center>\n";
			}
		}
		foreach ($order as $k)
		{
			//$retStr .= $k;
			//$k = (is_int($k)) ? '' : $k;
			//form_hidden($name.'['.$k.']', $v, TRUE);
			$retStr .= romon_form_field($k, $label[$k], $data[$k])."\n";
		}
		return $retStr;
	}
}		
	
if ( ! function_exists('romon_form_field'))
{
	function romon_form_field($id,$label,$field,$helptext='')
	{
		$retStr = '<tr><th><label for="'.$id.'">'.$label.'</label></th><td>';
		$retStr .= $field;
		if($helptext)
		{
			$retStr .= '<br /><span class="helptext">'.$helptext.'</span>';
		}
		$retStr .= "</td></tr>\n";
		return $retStr;
	}
}

// ------------------------------------------------------------------------

/**
 * Form Declaration
 *
 * Creates the opening portion of the form.
 *
 * @access	public
 * @param	string	the URI segments of the form destination
 * @param	array	a key/value pair of attributes
 * @param	array	a key/value pair hidden data
 * @return	string
 */
if ( ! function_exists('form_open_romon'))
{
	function form_open_romon($action = '', $attributes = '',$legend='', $hidden = array())
	{
		$CI =& get_instance();

//		if ($attributes == '')
//		{
//			$attributes = 'method="post"';
//		}

		if (is_string($attributes))
		{
			$attributes .= ' enctype="multipart/form-data"';
			$attributes .= ' method="post"';
		}
		else
		{
			$attributes['enctype'] = 'multipart/form-data';
			$attributes['method'] = 'post';
		}
		
		// If an action is not a full URL then turn it into one
		if ($action && strpos($action, '://') === FALSE)
		{
			$action = $CI->config->site_url($action);
		}

		// If no action is provided then set to the current url
		$action OR $action = $CI->config->site_url($CI->uri->uri_string());

		$form = '<form action="'.$action.'"';
		$form .= _attributes_to_string($attributes, TRUE);
		$form .= '>'."\n";
		if($legend){
			$form .= '<fieldset>';
			$form .= '<legend>'.$legend.'</legend>';	
			$form .= "<table align=center>\n";		
		}
		
		// Add CSRF field if enabled, but leave it out for GET requests and requests to external websites	
		if ($CI->config->item('csrf_protection') === TRUE AND ! (strpos($action, $CI->config->site_url()) === FALSE OR strpos($form, 'method="get"')))	
		{
			$hidden[$CI->security->get_csrf_token_name()] = $CI->security->get_csrf_hash();
		}

		if (is_array($hidden) AND count($hidden) > 0)
		{
			$form .= sprintf("<div style=\"display:none\">%s</div>", form_hidden($hidden));
		}

		return $form;
	}
}
// ------------------------------------------------------------------------

/**
 * Form Close Tag
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_close_romon'))
{
	function form_close_romon($extra = '</table>')
	{
		return $extra."</fieldset></form>";
	}
}
// ------------------------------------------------------------------------

/**
 * Attributes To String
 *
 * Helper function used by some of the form helpers
 *
 * @access	private
 * @param	mixed
 * @param	bool
 * @return	string
 */
if ( ! function_exists('_attributes_to_string'))
{
	function _attributes_to_string($attributes, $formtag = FALSE)
	{
		if (is_string($attributes) AND strlen($attributes) > 0)
		{
			if ($formtag == TRUE AND strpos($attributes, 'method=') === FALSE)
			{
				$attributes .= ' method="post"';
			}

			if ($formtag == TRUE AND strpos($attributes, 'accept-charset=') === FALSE)
			{
				$attributes .= ' accept-charset="'.strtolower(config_item('charset')).'"';
			}

		return ' '.$attributes;
		}

		if (is_object($attributes) AND count($attributes) > 0)
		{
			$attributes = (array)$attributes;
		}

		if (is_array($attributes) AND count($attributes) > 0)
		{
			$atts = '';

			if ( ! isset($attributes['method']) AND $formtag === TRUE)
			{
				$atts .= ' method="post"';
			}

			if ( ! isset($attributes['accept-charset']) AND $formtag === TRUE)
			{
				$atts .= ' accept-charset="'.strtolower(config_item('charset')).'"';
			}

			foreach ($attributes as $key => $val)
			{
				$atts .= ' '.$key.'="'.$val.'"';
			}

			return $atts;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * Hidden Input Field
 *
 * Generates hidden fields.  You can pass a simple key/value string or an associative
 * array with multiple values.
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_hidden'))
{
	function form_hidden($name, $value = '', $recursing = FALSE)
	{
		static $form;

		if ($recursing === FALSE)
		{
			$form = "\n";
		}

		if (is_array($name))
		{
			foreach ($name as $key => $val)
			{
				form_hidden($key, $val, TRUE);
			}
			return $form;
		}

		if ( ! is_array($value))
		{
			$form .= '<input type="hidden" name="'.$name.'" value="'.form_prep($value, $name).'" />'."\n";
		}
		else
		{
			foreach ($value as $k => $v)
			{
				$k = (is_int($k)) ? '' : $k;
				form_hidden($name.'['.$k.']', $v, TRUE);
			}
		}

		return $form;
	}
}




?>