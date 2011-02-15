<?php

/**
 * Simple DataObject for wrapping data up
 * Provides setFoo and getFoo functionality
 * dynamically.
 * 
 * @author Paul Lewis
 * @version 1.1
 * @date 2010/05/05
 *
 */
class DataObject
{
	private $_data;
	
	/**
	 * Contructor - initializes internal array
	 */
	public function __construct()
	{
		$this->_data = array();
	}
	
	/**
	 * Generic call handler to deal with setFoo and getFoo
	 * type function calls
	 * 
	 * @param {string} The function name, e.g. setFoo
	 * @param {Array} The array of parameters
	 * @return {Mixed} If a get, either NULL or the value. If set, NULL
	 */
	public function __call($strFunctionName, $arrArgs=NULL)
	{
		// assume we have nothing to return
		$mxRetVal 	= NULL;
		
		// work out what we're looking for
		$strValName = preg_replace("@(^get|^set)@", "", $strFunctionName);
		
		// if the function begins with a get
		// and we have the value, assign it
		if(preg_match("@^get@", $strFunctionName) &&
		   array_key_exists($strValName, $this->_data))
		   $mxRetVal = $this->_data[$strValName];

		// if it's a set make sure we have sufficient
		// parameters then populate the internal array
		if(preg_match("@^set@", $strFunctionName) &&
		   is_array($arrArgs) &&
		   count($arrArgs))
		   $this->_data[$strValName] = $arrArgs[0];
		
		// return whatever we have
		return $mxRetVal;
	}
}

?>