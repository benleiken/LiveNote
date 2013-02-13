<?php
	include("db.inc.php");
	$content = $_POST['content']; //get posted data
	$content = mysql_real_escape_string($content);	//escape string	
	
	$sql = "UPDATE content SET text = '$content' WHERE element_id = '1' ";
	
	if (mysql_query($sql))
	{
		echo 1;
	}
?>