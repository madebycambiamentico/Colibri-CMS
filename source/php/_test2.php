<?php

class xPARENT{
	
	static function first(){
		return 'babau<br>';
	}
	
	static function xprint($func,$param=[]){
		if (!is_callable("self::{$func}"))
			return false;
		$query = call_user_func_array("self::{$func}",$param);
		echo $query;
		return $query;
	}
}


class xCHILD extends xPARENT{
	
	static function second(){
		return 'ciao<br>';
	}
	
	static function xprint($func,$param=[]){
		if (!is_callable("self::{$func}")){
			//if we there isn't self::function then try with parent::function
			return parent::xprint($func,$param=[]);
		}
		$query = call_user_func_array("self::{$func}",$param);
		echo $query;
		return $query;
	}
}

xCHILD::xprint('first');
xCHILD::xprint('second');
xCHILD::xprint('second');
xCHILD::xprint('first');

?>