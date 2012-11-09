<?
/****************************************************************************
* DatabaseObject
*
* Not to be confused with the Database file, this abstract class is designed
* to allow for objects to be defined as unique database rows/records and
* then subsequently manipulated within PHP.
*
* This class takes the overhead of retrieving all fields from the database
* row, and providing support to get, set, and save that data, while protecting
* the primary key.
*
* This is v2 of the object that accepts arrays as primary keys. It is backward
* compatible with v1 that takes primitives
****************************************************************************/

abstract class DatabaseObject{
	private $dbArr;
	private $core;
	private $primaryKey;
	private $table;

	protected function loadCore(&$coreRef){
		$this->core = $coreRef;
	}
	protected function getCore(){
		return $this->core;
	}
	protected function loadData($tableName, $primaryKeyName, $primaryKeyValue, $dbRow=false){
		$this->primaryKey = $primaryKeyName;
		$this->table = $tableName;

		if ((is_array($primaryKeyName))&&(is_array($primaryKeyValue))){
			$whereClause = " WHERE ";
			//assumes arrays are of the same length
			$arrSize = sizeof($primaryKeyName);
			for ($i=0; $i< $arrSize; $i++){
				if ($i > 0){
					$whereClause .= " AND";
				}
				$whereClause .= " ".$primaryKeyName[$i]."='".$primaryKeyValue[$i]."'";
			}
			$whereClause .=";";
		}else{
			$whereClause = " WHERE ".$primaryKeyName."='".$primaryKeyValue."';";
		}
		if (!$dbRow){
			$query = "SELECT * FROM ".$tableName." ".$whereClause;
			$res = $this->core->db($query);
			if (mysql_num_rows($res)==1){
				$dbArr = array();
				$dbRow = mysql_fetch_object($res);
			}else{
				throw new Exception ("Unique object with identifier ".$primaryKeyValue." not found");
			}
		}
		$varArr = get_object_vars($dbRow);
		foreach ($varArr as $key=>$value){
			$this->core->getDebugger()->debug("Loading: ".$key." -> ".$value);
			$this->dbArr[$key] = $value;
		}
	}

	protected function get($key){
		return $this->dbArr[$key];
	}

	protected function set($key, $value){
		if ($key != $this->primaryKey){
			$this->dbArr[$key] = $value;
			return true;
		}else{
			return false;
		}
	}

	protected function save(){
		$count = 0;
		$query = "UPDATE ".$this->table." SET ";
		if (is_array($this->primaryKey)){
			$arrayCheck = true;
		}else{
			$arrayCheck = false;
		}

		foreach ($this->dbArr as $key=>$value){
			if ((($arrayCheck)&&(in_array($key, $this->primaryKey)))||((!$arrayCheck)&&($key == $this->primaryKey))){
				//primary keys can not be changed with this method
			}else{
				if ($count > 0){
					$query .=",";
				}
				$query .= $key."='".$value."' ";
				$count++;
			}
		}
		$query .= " WHERE ".$this->primaryKey."='".$this->dbArr[$this->primaryKey]."';";
		//echo $query;
		$this->core->db($query);
	}
}

?>