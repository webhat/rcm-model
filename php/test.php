<?php

use Everyman\Neo4j\Client,
		Everyman\Neo4j\Index\NodeIndex,
		Everyman\Neo4j\Cypher;

require("phar://libs/neo4jphp.phar");

$client = new Client();
$founders = new NodeIndex($client, 'founders');

$daniel = $client->makeNode()->setProperty('name', 'Daniël W. Crompton')->save();
$karima = $client->makeNode()->setProperty('name', 'Karim A')->save();
$karimm = $client->makeNode()->setProperty('name', 'Karim Maarek')->save();

$founders->add($daniel, 'name', $daniel->getProperty('name'));
$founders->add($karima, 'name', $karima->getProperty('name'));
$founders->add($karimm, 'name', $karimm->getProperty('name'));

$rcm = $client->makeNode()->setProperty('startup', 'rcm')->save();
$wt365 = $client->makeNode()->setProperty('startup', 'wt365')->save();

$daniel->relateTo($rcm, 'IN')->save();
$karima->relateTo($rcm, 'IN')->save();
$daniel->relateTo($wt365, 'IN')->save();
$karimm->relateTo($wt365, 'IN')->save();

$queryTemplate = "START founder=node:founders('name:*') ".
	"MATCH (founder) -[:IN]- (startup)".
	"WHERE startup.startup = {startup}".
	"RETURN founder";
$query = new Cypher\Query($client, $queryTemplate, array('startup'=>'rcm'));
$result = $query->getResultSet();

echo "Found ".count($result)." actors:\n";
foreach($result as $row) {
	echo "  ".$row['founder']->getProperty('name')."\n";
}

?>
