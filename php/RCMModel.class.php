<?php

use Everyman\Neo4j\Client,
		Everyman\Neo4j\Index\NodeIndex,
		Everyman\Neo4j\Cypher;

class RCMModel {

	private $config			= null;
	private $client			= null;
	private $nodeIndex	= null;

	public function __construct() {
		$this->setConfig( new RCMConfig());
		$this->setClient( new Client());
		$this->setNodeIndex( new NodeIndex($this->getClient(), 'NI'));
	}

	public function addNode( $name, $properties = array()) {
		$ret = $this->client->makeNode()->setProperty( 'name', $name);

		if(is_array($properties) && !empty($properties)) {
			foreach( $properties as $key => $value)
				$ret->setProperty($key, $value);
		}

		$ret = $ret->save();
		$this->nodeIndex->add($ret, 'name', $ret->getProperty('name'));

		return ($ret->getProperty('name') == $name);
	}

	public function removeNodeByName( $nodeName) {
		return $this->removeNodeByProperty( 'name', $nodeName);
	}

	public function removeNodeByProperty( $key, $value) {
		$queryTemplate = "START root=node:NI('$key:$value') ".
			"RETURN root";
		$query = new Cypher\Query( $this->getClient(), $queryTemplate, array($key => $value));
		$result = $query->getResultSet();

		if(($cnt = count($result)) > 1) {
			throw new RCMModelException( "Too many nodes match the query. $cnt nodes found");
		}

		foreach($result as $row) {
			if(!$this->getClient()->deleteNode($row['root'])) {
				throw new RCMModelException( "Unabse to delete node, reason unknown.");
			}
		}

		return true;
	}

	public function setNodeIndex($nodeIndex) {
		return $this->nodeIndex = $nodeIndex;
	}

	public function getNodeIndex() {
		return $this->nodeIndex;
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
