<?php
include 'class.php';
?>
<html>
<head>
	<link rel="stylesheet" href="style/common.css" />
	<link rel="stylesheet" href="style/tooltip.css" />
	<link rel="stylesheet" href="style/ui.css" />
	
	<script src="js/jquery-3.1.1.min.js"></script>
	<script src="js/jquery-pub-sub.js"></script>
	<script src="js/SuperScript.js"></script>
	<script src="js/common.js"></script>
	<script src="js/popup.js"></script>
	<script src="js/ui.js"></script>
</head>
<body>
<script>$.publish('bodyShow')</script>
<script>
(function(){

var ui = new UI.Manager({
	el: document.body,
	dir_data: <?php

include 'get_dir_data.php';
$a = array();
foreach ($DIR_DATA as $key => $arr){
	$a[ $key ] = array(
		'name' => $arr['NAME'],
		'files' => array_keys($arr['FILES'])
	);
}
echo json_encode($a, JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

?>
})

})()
</script>
</body>
</html>