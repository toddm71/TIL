<?php
$mysqli = new mysqli('localhost', 'username', 'password', 'database_name');
if ($mysqli->connect_error) {
    die("Connection failed: ". $mysqli->connect_error);
}

$contry = filter_input(INPUT_POST,'contry');

// pattern A : using placeholder
$patternAqry = "SELECT USERID, SCORE FROM USERS WHERE COUNTRYCD =?";
$stmt = $mysqli->prepare($patternAqry);
$stmt->bind_param('s',$contry);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($USERID, $SCORE);
$arr = [];
while($stmt->fetch()){
    $arr[] = [
        'userid' => $USERID,
        'score' => $SCORE
    ];
}
$stmt->close();

// pattern B : using real_escape_string
$patternBqry = sprintf("SELECT USERID, SCORE FROM USERS WHERE COUNTRYCD = '%s'", $mysqli->real_escape_string($contry));
$stmt = $mysqli->prepare($patternBqry);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($USERID, $SCORE);
while($stmt->fetch()){
    $arr[] = [
        'userid' => $USERID,
        'score' => $SCORE
    ];
}
$stmt->close();

$result = json_encode($arr);
?>
