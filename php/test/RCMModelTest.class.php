<?php

use	Everyman\Neo4j\Cypher;

class RCMModelTest extends PHPUnit_Framework_TestCase {

	private $rcmm		= null;

	/* Mocked Objects */
	private $propertyContainer	= null;
	private $nodeIndex	= null;
	private $client	= null;
	private $realClient	= null;
	private $node		= null;

	public function setUp() {
		$this->nodeIndex = $this->getMock('NodeIndex', array('add'));
		$this->client = $this->getMock('Client', array('makeNode'));
		$this->node = $this->getMock('Node', array('setProperty'));
		$this->propertyContainer = $this->getMock('PropertyContainer', array('save', 'getProperty'));
		$this->rcmm = new RCMModel();
		$this->realClient = $this->rcmm->getClient();
	}

	public function tearDown() {

		// Clean Up Server
		try {
			$this->realClient->getServerInfo(true);
		} catch( Exception $e ) {
			return;
		}
	}

	/**
	 * @dataProvider nodeProvider
	 */
	public function testServerAddNodeWithProperties( $nodeName, $properties, $expected) {
		$this->rcmm->setClient($this->client);
		$this->rcmm->setNodeIndex($this->nodeIndex);

		$this->propertyContainer->expects($this->atLeastOnce())->method('getProperty')->will($this->returnValue(true));
		$this->propertyContainer->expects($this->atLeastOnce())->method('save')->will($this->returnValue($this->propertyContainer));
		$this->node->expects($this->atLeastOnce())->method('setProperty')->will($this->returnValue($this->propertyContainer));
		$this->client->expects($this->once())->method('makeNode')->will($this->returnValue($this->node));

		$actual = $this->rcmm->addNode( $nodeName);

		$this->assertEquals( $expected, $actual);
	}


	/**
	 * @dataProvider nodeProvider
	 */
	public function testAddNodeNoProperties( $nodeName, $properties, $expected) {
		$this->rcmm->setClient($this->client);
		$this->rcmm->setNodeIndex($this->nodeIndex);

		$this->propertyContainer->expects($this->exactly(2))->method('getProperty')->will($this->returnValue(true));
		$this->propertyContainer->expects($this->once())->method('save')->will($this->returnValue($this->propertyContainer));
		$this->node->expects($this->once())->method('setProperty')->will($this->returnValue($this->propertyContainer));
		$this->client->expects($this->once())->method('makeNode')->will($this->returnValue($this->node));

		$actual = $this->rcmm->addNode( $nodeName);

		$this->assertEquals( $expected, $actual);
	}

	/**
	 * @dataProvider nodeProvider
	 */
	public function testServerAddNodeNoProperties( $nodeName, $properties, $expected) {
		try {
			$this->rcmm->setClient($this->realClient);
			$this->rcmm->getClient()->getServerInfo(true);
		} catch( Exception $e ) {
			$this->markTestSkipped('neo4j Server down');
		}

		$actual = $this->rcmm->addNode( $nodeName);
		$this->assertEquals( $expected, $actual);

		$queryTemplate = "START root=node:NI('name:$nodeName') ".
			"RETURN root";
		$query = new Cypher\Query( $this->rcmm->getClient(), $queryTemplate, array('name' => $nodeName));
		$result = $query->getResultSet();

		foreach($result as $row) {
			$this->rcmm->getClient()->deleteNode($row['root']);
		}

	}


	public function nodeProvider() {
		return array( // elements
				array( // args
					"NodeName1", // var
					array(),
					true // expected
					),
				array( // args
					"NodeName2", // var
					array("propkey2" => "propval2"),
					true // expected
					),
				array( // args
					"NodeName3", // var
					array(
						"propkey31" => "propval31",
						"propkey32" => "propval32",
						"propkey33" => "propval33",
						"propkey34" => "propval34"
						),
					true // expected
					)
				);
	}
}
 
?>
