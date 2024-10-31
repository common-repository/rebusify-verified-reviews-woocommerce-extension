<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
include_once('rvrw_xss_clean.php');

function rvrw_sanitize_data($var, $type)
{
	$obj_input=new rvrw_sanitize_input();       
      switch ( $type ) {
      					
						case 'xss_clean':
						//echo '<br> i am case xss'.$var.'<br>';
					    $var= $obj_input->xss_clean($var);	 
					    return $var;
					    break;
						
                        case 'int': // integer
                        $var = (int) $var;
                        break;

                        case 'str': // trim string
                        $var = trim ( $var );
                        break;

                        case 'nohtml': // trim string, no HTML allowed
                        $var = htmlentities ( trim ( $var ), ENT_QUOTES );
                        break;
                     
						case 'plain': // trim string, no HTML allowed, plain text
                        $var =  htmlentities ( trim ( $var ) , ENT_NOQUOTES )  ;
                        break;

                        case 'upper_word': // trim string, upper case words
                        $var = ucwords ( strtolower ( trim ( $var ) ) );
                        break;

                        case 'ucfirst': // trim string, upper case first word
                        $var = ucfirst ( strtolower ( trim ( $var ) ) );
                        break;

                        case 'lower': // trim string, lower case words
                        $var = strtolower ( trim ( $var ) );
                        break;

                        case 'urle': // trim string, url encoded
                        $var = urlencode ( trim ( $var ) );
                        break;

                        case 'trim_urle': // trim string, url decoded
                        $var = urldecode ( trim ( $var ) );
                        break;

                        case 'telephone': // True/False for a telephone number
                        $size = strlen ($var) ;
                        for ($x=0;$x<$size;$x++)
                        {
                                if ( ! ( ( ctype_digit($var[$x] ) || ($var[$x]=='+') || ($var[$x]=='*') || ($var[$x]=='p')) ) )
                                {
                                        return false;
                                }
                        }
                        return true;
                        break;

                        case 'pin': // True/False for a PIN
                        if ( (strlen($var) != 13) || (ctype_digit($var)!=true) )
                        {
                                return false;
                        }
                        return true;
                        break;

                        case 'id_card': // True/False for an ID CARD
						if ( (ctype_alpha( substr( $var , 0 , 2) ) != true ) || (ctype_digit( substr( $var , 2 , 6) ) != true ) || ( strlen($var) != 8))
                        {
                                return false;
                        }
                        return true;
                        break;
					
					   case 'sql': // True/False if the given string is SQL injection safe
                        //  insert code here, I usually use ADODB -> qstr() but depending on your needs you can use mysql_real_escape();
                       // return mysql_real_escape_string($var);
						if (!is_numeric($var))
						  {
						  $var = "'" . mysql_real_escape_string($var) . "'";
						  
						  }
						  return $var;
                        break;
						
					   case 'encode_php_tag':
						{
						$var = str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $var);
						return $var;
					    }
						 break;
						 
						case 'natural_non_zero':
						$var=rvrw_is_natural_no_zero($var);
						return $var;
						
							
		            }       
        return $var;
}

/**
	 * Is a Natural number, but not a zero  (1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function rvrw_is_natural_no_zero($str)
	{
		//echo 'val= '.$str.'<br>';   
		if ( ! preg_match( '/^[0-9]+$/', $str))
		{
			return FALSE;
		}
	
		if ($str == 0)
		{
			return FALSE;
		}

		return TRUE;
	}
 
	// --------------------------------------------------------------------
	
/*	/**
	 * Required
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function rvrw_required($str)
	{
		if ( ! is_array($str))
		{
			return (trim($str) == '') ? FALSE : TRUE;
		}
		else
		{
			return ( ! empty($str));
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	function rvrw_matches($str, $field)
	{
		if ( ! isset($_POST[$field]))
		{
			return FALSE;
		}
		
		return ($str !== $_POST[$field]) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Minimum Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */	
	function rvrw_min_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) < $val) ? FALSE : TRUE;		
		}

		return (strlen($str) < $val) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Max Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */	
	function rvrw_max_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}
		
		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) > $val) ? FALSE : TRUE;		
		}

		return (strlen($str) > $val) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	/**
	 * Exact Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */	
	function rvrw_exact_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}
	
		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) != $val) ? FALSE : TRUE;		
		}

		return (strlen($str) != $val) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	/**
	 * Valid Email
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function rvrw_valid_email($str)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
                                                
	// --------------------------------------------------------------------
	/**
	 * Validate IP Address
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function rvrw_valid_ip($ip)
	{
		return $this->CI->input->valid_ip($ip);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */		
	function rvrw_alpha($str)
	{
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Alpha-numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function rvrw_alpha_numeric($str)
	{
		return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function rvrw_alpha_dash($str)
	{
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function rvrw_numeric($str)
	{
		return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

	}

	// --------------------------------------------------------------------
	/**
	 * Is Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool

	// --------------------------------------------------------------------*/
	function rvrw_integer($str)
	{
		return (bool)preg_match( '/^[\-+]?[0-9]+$/', $str);
	}
	
	/**
	 * Is a Natural number  (0,1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function rvrw_is_natural($str)
	{   
   		return (bool)preg_match( '/^[0-9]+$/', $str);
	}

	
	
	
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Prep data for form
	 *
	 * This function allows HTML to be safely shown in a form.
	 * Special characters are converted.
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function rvrw_prep_for_form($data = '')
	{
		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				$data[$key] = $this->rvrw_prep_for_form($val);
			}
			
			return $data;
		}
		
		if ($this->_safe_form_data == FALSE OR $data == '')
		{
			return $data;
		}

		return str_replace(array("'", '"', '<', '>'), array("&#39;", "&quot;", '&lt;', '&gt;'), stripslashes($data));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Prep URL
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
	function rvrw_prep_url($str = '')
	{
		if ($str == 'http://' OR $str == '')
		{
			//$_POST[$this->_current_field] = '';
			return;
		}
		
		if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://')
		{
			$str = 'http://'.$str;
		}
		
		return $str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Strip Image Tags
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
	function rvrw_strip_image_tags($str)
	{
		$obj_input_strip=new CI_Input_t();   
		$var = $obj_input_strip->rvrw_strip_image_tags($str);
		return $var;
	}
	
	
	/**
	 * Convert PHP tags to entities
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
	function rvrw_encode_php_tags($str)
	{
		$_POST[$this->_current_field] = str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
	}
 
 // this function will protect from sql injections
 function rvrw_escape_str($str, $like = FALSE)	
	{	
		if (is_array($str))
		{
			foreach($str as $key => $val)
	   		{
				$str[$key] = $this->rvrw_escape_str($val, $like);
	   		}
   		
	   		return $str;
	   	}

		
		if (function_exists('mysql_real_escape_string'))
		{
		    $str =	mysql_real_escape_string($str);
		}
		else
		{
			$str = addslashes($str);
		}
		
		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			$str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
		}
		
		return $str;
	}
 
	
?>