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
* Version 1 of the object only holds support for simple objects with one field
* as the unique key. Future versions can be developed to support multiple
* fields through the use of arrays.
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

		if ((!$dbRow)||($dbRow->$primaryKeyName!=$primaryKeyValue)) {
			$query = "SELECT * FROM ".$tableName." WHERE ".$primaryKeyName."='".$primaryKeyValue."';";
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
		$query = "UPDATE ".$this->table." SET ";
		foreach ($this->dbArr as $key->$value){
			if ($key != $this->primaryKey){
				$query .= $key."='".$value."' ";
			}
		}
		$query .= " WHERE ".$this->primaryKey."='".$this->dbArr[$this->primaryKey]."';";
		$this->core->db($query);
	}
}

?>