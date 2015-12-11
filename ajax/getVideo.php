<?php
require_once('../libs/db.php');

$limit = $_GET['maxResults'];

if(isset($_GET['q'])){
	$key = strtolower($_GET['q']);
	$query="SELECT * FROM yapi WHERE LOWER(title) LIKE LOWER('%$key%') AND title IS NOT NULL OR title != '' LIMIT $limit ";
}else{
	$query="SELECT * FROM yapi WHERE title IS NOT NULL OR title != '' LIMIT $limit ";
}
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
