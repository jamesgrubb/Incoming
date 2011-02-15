<?php
/**
 * Database wrapper class
 * 
 * @author Paul Lewis
 * @version 1.1
 * @date 2010/05/06
 */
class Database
{
	private static $__conn;
	
	/**
	 * Gets a result set from the database
	 * 
	 * @param {string} The SQL to run against the database
	 * @param {bool} Whether or not to bounce a single row down to a single object
	 * @return {Array} The result set
	 */
	public static function get($strSQL, $bBounceSingleRow=false)
	{
		// always connect
		self::_connect();
		
		// assume an empty array
		// is the response
		$mxResponse 	= array();
		
		// now attempt to run the query
		$objResultSet 	= mysql_query($strSQL, self::$__conn) or die("Get query fail: ".$strSQL.": ".mysql_error(self::$__conn));
	
		// get the row
		while($objResultRow = mysql_fetch_object($objResultSet))
		{
			// create a new DataObject
			$objDataRow = new DataObject();
			
			// go through each column in the row
			foreach($objResultRow as $strLabel => $mxValue)
			{
				// work out what the setter should look like
				$strDataRowLabel 	= preg_replace("@^f@", "", $strLabel);
				
				// now set it
				$objDataRow->{"set".$strDataRowLabel}($mxValue);
			}
			
			// add it to the result set
			$mxResponse[] = $objDataRow;
		}
		
		// if we need to, bounce the single row
		// down. That's supposing there is only a
		// single row that has been returned
		if(count($mxResponse) == 1 && $bBounceSingleRow)
			$mxResponse = $mxResponse[0];
		
		// send back the result set
		return $mxResponse;
	}
	
	/**
	 * Inserts something into the database
	 * @param $strSQL
	 * @return unknown_type
	 */
	public static function set($strSQL, $arrBind=NULL)
	{
		// assume we don't know what
		// to return
		$iReturnValue 	= NULL;
		
		// always connect
		self::_connect();
		
		// if we have things to bind on,
		// do that now
		if($arrBind)
			$strSQL		= self::_bind($strSQL, $arrBind);
		
		// now do the query
		mysql_query($strSQL) or die("Set query fail: ".$strSQL.": ".mysql_error(self::$__conn));
		
		// now work out what info we need to
		// return back to the user
		if(preg_match("@^INSERT@is", $strSQL))
			$iReturnValue = mysql_insert_id(self::$__conn);
		else
			$iReturnValue = mysql_affected_rows(self::$__conn);
		
		// get the inserted ID
		return $iReturnValue;
	}
	
	/**
	 * 
	 * @param $mxValues
	 * @return unknown_type
	 */
	public static function escape($mxValues)
	{
		// always connect
		self::_connect();
		
		// assume we don't need to bounce down
		$bBounceDown = false;
		
		// if the value is a string
		// wrap it in an array
		if(is_string($mxValues))
		{
			$mxValues 		= array($mxValues);
			$bBounceDown 	= true;
		}
		
		// escape the string
		for($i = 0; $i < count($mxValues); $i++)
		{
			// if this is definitely a string
			if(is_string($mxValues[$i]))
				$mxValues[$i] = "'".mysql_real_escape_string($mxValues[$i])."'";
			
		}
		
		// now bounce down if needed
		if($bBounceDown && count($mxValues))
			$mxValues = $mxValues[0];
		
		// send it back
		return $mxValues;
	}
	
	/**
	 * Shuts down the database connection
	 */
	public static function shutdown()
	{
		// if we have a valid connection
		// shut it down
		if(self::$__conn)
			mysql_close(self::$__conn);
	}
	
	private static function _bind($strSQL, $arrBind)
	{
		$strNewSQL 		= "";
		$arrSQLToBind 	= explode("?", $strSQL);
		
		$iBindParts		= count($arrBind);
		
		while($iBindParts--)
			$strNewSQL = $arrSQLToBind[$iBindParts].self::escape($arrBind[$iBindParts]).$strNewSQL;
		
		return $strNewSQL;
	}
	
	/**
	 * Connects to the database
	 * 
	 * @return {Object} A connection
	 */
	private static function _connect()
	{
		// if we don't have a connection
		if(!self::$__conn)
		{
			// create a connection
			self::$__conn = mysql_connect(DATABASE_HOST,
										  DATABASE_USERNAME,
										  DATABASE_PASSWORD) or die("Unable to connect to database: ".mysql_connect_error());

			// register a shutdown function
			register_shutdown_function("Database::shutdown");
							  
			// if we know which database
			// we need to be talking to
			// then go ahead and set that
			if(defined('DATABASE_TARGET'))
				mysql_select_db(DATABASE_TARGET, self::$__conn);
		}
		
		// in any case return the connection
		return self::$__conn;
	}
}
?>