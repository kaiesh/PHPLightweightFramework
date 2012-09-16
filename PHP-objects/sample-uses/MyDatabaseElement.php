<?
/*************
* A sample class that demonstrates how to extend the DatabaseObject
**************/

require_once("DatabaseObject.php");
class MyDatabaseElement extends DatabaseObject{

	function MyDatabaseElement(&$coreRef, $uniqueRef, $dbObjData=false){
		$this->loadCore($coreRef);
		$this->expoTag = $this->getCore()->dbReadyTxt($uniqueRef);
		$this->loadData("myTableName", "primaryKeyName", $uniqueRef, $dbObjData);
	}

	function getMap(){
		return $this->get("mapURL");
	}

	function showMap(){
		return $this->get("mapShow");
	}

	function showButton(){
		return $this->get("buttonShow");
	}


}

?>