<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2009, EllisLab, Inc.
 * @license		http://expressionengine.com/docs/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * ExpressionEngine Core Email Class
 *
 * @package		ExpressionEngine
 * @subpackage	Core
 * @category	Core
 * @author		ExpressionEngine Dev Team
 * @link		http://expressionengine.com
 */
class EE_Email extends CI_Email {


	/**
	 * Constructor
	 */	
	function EE_Email($init = TRUE)
	{
		parent::CI_Email();

		if ($init != TRUE)
			return;

		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
			
		$this->EE_initialize();		
	}	

	// --------------------------------------------------------------------

	/**
	 * Set config values
	 *
	 * @access	private
	 * @return	void
	 */
	function EE_initialize()
	{	
		$config = array(
						'protocol'		=> ( ! in_array( $this->EE->config->item('mail_protocol'), $this->_protocols)) ? 'mail' : $this->EE->config->item('mail_protocol'),
						'charset'		=> ($this->EE->config->item('email_charset') == '') ? 'utf-8' : $this->EE->config->item('email_charset'),
						'smtp_host'		=> $this->EE->config->item('smtp_server'),
						'smtp_user'		=> $this->EE->config->item('smtp_username'),
						'smtp_pass'		=> $this->EE->config->item('smtp_password')
						);
		
		/* -------------------------------------------
		/*	Hidden Configuration Variables
		/*	- email_newline => Default newline.
		/*  - email_crlf => CRLF used in quoted-printable encoding
        /* -------------------------------------------*/
		
		if ($this->EE->config->item('email_newline') !== FALSE)
		{
			$config['newline'] = $this->EE->config->item('email_newline');
		}
		
		if ($this->EE->config->item('email_crlf') !== FALSE)
		{
			$config['crlf'] = $this->EE->config->item('email_crlf');
		}
		
		$this->initialize($config);
						
		$this->useragent = APP_NAME.' '.APP_VER;						
	}

	// --------------------------------------------------------------------

	/**
	 * Set the email message
	 *
	 * EE uses action ID's so we override the messsage() function 	 
	 *
	 * @access	public
	 * @return	void
	 */	 
	function message($body, $alt = '')
	{
		$body = $this->EE->functions->insert_action_ids($body);
	
		if ($alt != '')
		{
			$this->set_alt_message($this->EE->functions->insert_action_ids($alt));
		}
				
		$this->_body = stripslashes(rtrim(str_replace("\r", "", $body)));
	}
	

}
// END CLASS

/* End of file EE_Email.php */
/* Location: ./system/expressionengine/libraries/EE_Email.php */