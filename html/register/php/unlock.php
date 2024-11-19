<?php
header('Content-Type: application/json');
$flagFile = 'stop.txt';
file_put_contents($flagFile, 'stop');
$data = [];
echo json_encode($data);