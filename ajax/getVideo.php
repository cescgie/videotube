<?php
require_once('../libs/db.php');

$key = $_GET['q'];
$limit = $_GET['maxResults'];

$query="SELECT * FROM yapi WHERE title LIKE '%$key%' LIMIT $limit ";

$result = $mysqli->query($query) or die($mysqli->error.__LINE__);

$arr = array();
if($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$arr[] = $row;
	}
}

# JSON-encode the response
echo $json_response = json_encode($arr);

?>
