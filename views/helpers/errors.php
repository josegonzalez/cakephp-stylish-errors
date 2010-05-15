<?php
/**
 * ErrorsHelper prints out some awesome looking error messages, rails style
 *
 * Just include the following css:
 * 
 * 		#error{background-color:#ddd;border:1px solid #900;padding:0 0 .5em 0;}
 * 		#error span{background-color:#900;color:#fff;display:block;margin:0 0 1em 0;padding:1em;}
 * 		#error ul{display:block;list-style-type:disc;margin:0;padding:0;}
 * 		#error ul li{display:list-item;list-style-type: square;margin:0 0 0 2em;}
 * 
 * @package app.views.helpers
 * @author Jose Diaz-Gonzalez
 */
class ErrorsHelper extends AppHelper {
/**
 * Call this helper automatically, with no help from the user
 *
 * @var string
 **/
	var $auto = true;

/**
 * Automatically embeds session messages, regardless of whether this helper is called manually or automatically
 *
 * @var string
 **/
	var $useSession = true;

/**
 * Object that contains a View
 *
 * @var string
 **/
	var $view = null;

/**
 * Object that contains the session helper
 *
 * @var string
 **/
	var $session = null;

/**
* Prints out a jQuery enhanced list of validation errors
* 
* @param array $data Array of all possible errors for all models being validated
* @param integer $type Integer indicating the css id of the error
* @param boolean $flash Boolean indicating whether this is a flash message
* @access public
*/
	function beforeRender(){
		if ($this->auto) {
			$this->for_layout();
		}
	}
	
/**
 * Renders the errors for the layout
 *
 * @return string
 * @author Jose Diaz-Gonzalez
 **/
	function for_layout() {
		$errors = (isset($this->validationErrors)) ? $this->_getArray($this->validationErrors) : null;
		ob_start();
		$this->doSession();
		if ($errors != array()){
			echo $this->assembleMessage($errors);
		}
		if (!$this->view) {
			$this->view = ClassRegistry::getObject('view');
		}
		$this->view->set("errors_for_layout", ob_get_clean());
	}

/**
 * Renders the session flash messages, if any
 *
 * @return void
 * @author savant
 **/
	function doSession() {
		if ($this->useSession) {
			if (!$this->session) {
				App::import('Helper', 'Session');
				$this->session = new SessionHelper();
			}
			echo $this->session->flash();
			echo $this->session->flash('auth');
		}
	}

/**
* Returns an Array of items that are nested within some other array
* 
* @param array $data Array of items which may be arrays themselves
* @return array Array of items which remove one layer of nesting
* @access public
*/
	function _getArray($data){
		$arrayValues = array_values($data);
		$output = array();
		foreach ($arrayValues as $value){
			$output += $value;
		}
		return $output;
	}

/**
* Prints out a jQuery enhanced list of validation errors
* 
* @param array $data Array of all possible errors
* @return string String of Javascript/HTML containing list of errors
* @access public
*/
	function assembleMessage($data) {
		$message = $this->getList($data);
		$reasonCount = count($data);
		$countMessage = null;
		if ($reasonCount > 1) {
			$countMessage = sprintf(__("%s errors prohibited this record from being saved", true), $reasonCount);
		} else {
			$countMessage = __("1 error prohibited this record from being saved", true);
		}
		$output = "<div id='error' class='flash' style='display: none'>
						<span>{$countMessage}</span>
						{$message}</div>
						<script type='text/javascript'>
							jQuery(document).ready(function() {
								$ ('#error').fadeIn('slow');
							});
						</script>";
		return $output;
	}

/**
* Returns a list of items, each wrapped in an <li></li>
* 
* @param array $data Array of all possible items
* @return string String containing a <li></li> wrapped list of items
* @access public
*/
	function getList($data){
		$output = '';
		if (is_array($data)) {
			foreach($data as $key => $value){
				$key = Inflector::humanize($key);
				$output .= "<li>{$key} {$value}</li>";
			}
		} else {
			$output .= "<li>{$data}</li>";
		}
		return "<ul>{$output}</ul>";
	}
}
?>