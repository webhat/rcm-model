<?php

use Everyman\Neo4j\Client,
		Everyman\Neo4j\Index\NodeIndex,
		Everyman\Neo4j\Cypher;

class RCMModel {

	private $config = null;
	private $client = null;

	public function __construct() {
		$this->setConfig( new RCMConfig());
		$this->setClient( new Client());
	}

	public function addNode( $name, $properties = array()) {
		$ret = $this->client->makeNode()->setProperty( 'name', $name)->save();

		if($ret->getProperty('name') == $name);
	}

	public function setConfig($config) {
		return $this->config = $config;
	}

	public function getConfig() {
		return $this->config;
	}

	public function setClient($client) {
		return $this->client = $client;
	}

	public function getClient() {
		return $this->client;
	}
}

?>
