<?php

class RCMModelTest extends PHPUnit_Framework_TestCase {

	private $rcmm		= null;

	/* Mocked Objects */
	private $client	= null;
	private $node		= null;

	public function setUp() {
		$this->client = $this->getMock('Client', array('makeNode'));
		$this->node = $this->getMock('Node', array('setProperty'));
		$this->propertycontainer = $this->getMock('PropertyContainer', array('save', 'getProperty'));
		$this->rcmm = new RCMModel();
		$this->rcmm->setClient($this->client);
	}

	public function tearDown() {
	}

	/**
	 * @dataProvider nodeProvider
	 */
	public function testAddNodeNoProperties( $nodeName, $expected) {
		$this->propertycontainer->expects($this->once())->method('getProperty');
		$this->propertycontainer->expects($this->once())->method('save')->will($this->returnValue($this->propertycontainer));
		$this->node->expects($this->once())->method('setProperty')->will($this->returnValue($this->propertycontainer));
		$this->client->expects($this->once())->method('makeNode')->will($this->returnValue($this->node));
		$actual = $this->rcmm->addNode( $nodeName);

		$this->assertEquals( $expected, $actual);
	}

	public function nodeProvider() {
		return array( // elements
				array( // args
					"NodeName1", // var
					false // expected
					)
				);
	}
}
 
?>
