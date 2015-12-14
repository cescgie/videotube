<?php
require_once('../libs/db.php');

//$key = "hamburg";
$key = strtolower($_GET['name']);

$query="SELECT lists FROM playlist WHERE LOWER(name) LIKE LOWER('$key') ";

$result = $mysqli->query($query) or die($mysqli->error.__LINE__);

$arr = array();
if($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$arr[] = $row;
	}
}
print_r($arr[0]['lists']);
?>
