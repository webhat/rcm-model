<?php

require("phar://libs/neo4jphp.phar");

function loader($class) {
	$file = "". $class . '.class.php';
	if (file_exists($file)) {
		require $file;
	}	else if (file_exists("php/". $file)) {
		require "php/". $file;
	} else if(file_exists("php/test/" . $file)) {
		require "php/test/". $file;
	} else if(file_exists("../" . $file)) {
		require "../". $file;
	}
}

spl_autoload_register('loader');

