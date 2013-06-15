<?php

class Lib_DataType{

	const VARCHAR	=	1;
	const TEXT		=	2;
	const INTEGER	=	3;
	const DECIMAL	=	4;
	const DATE		=	5;
	const DATETIME	=	6;
	const BOOLEAN	=	7;
	const ENUM		=	8; 

	static function getTypes(){
		return array(
			self::VARCHAR 	=> 'VARCHAR',
			self::TEXT 		=> 'TEXT',
			self::INTEGER 	=> 'INTEGER',
			self::DECIMAL 	=> 'DECIMAL',
			self::DATE 		=> 'DATE',
			self::DATETIME 	=> 'DATETIME',
			self::BOOLEAN 	=> 'BOOLEAN',
			self::ENUM 		=> 'ENUM', 
		);
	}

	static function getTypeName($id){
		if($id==0)return self::VARCHAR;
		$names = self::getTypes();
		return $names[$id];
	}

}

?>