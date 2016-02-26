<?php

class Babau{
	public function test(){
		echo "test";
	}
}

$bb = new Babau();
$bb->test();

$test = 'test';
$bb->$test();//ok
$bb->{'test'}();//ok
$bb->{$test}();//ok

?>