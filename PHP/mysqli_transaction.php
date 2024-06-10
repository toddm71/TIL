<?php
$id = filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
$updcount = filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
$sales = filter_input(INPUT_POST,'sales');


$mysqli = new mysqli('localhost', 'username', 'password', 'database_name');

if ($mysqli->connect_error) {
    die("Connection failed: ". $mysqli->connect_error);
}

try {
    $isFailed = False;
    $mysqli->begin_transaction();

    $query1="SELECT PIC, UPDCOUNT FROM LOCK_MONTHLY_SALES WHERE ID = ?";
    $stmt = mysqli->prepare($query1);
    $stmt->bind_param('i',$id);
    if(!$stmt->execute()){
        throw new Exception("Error executing query 1: ". $mysqli->error);
    }
    $stmt->store_result();
    $stmt->bind_result($pic,$upd_from_select);
    $stmt->fetch();
    $stmt->close();

    if($updcount <> $upd_from_select){
        throw new Exception("Error $pic updated data, please try again");
    }

    $query2 = "UPDATE MONTHLY_SALES SET SALES = ?,UPDCOUNT = UPDCOUNT+1 WHERE ID = ?";
    $stmt=$mysqli->prepare($query2);
    $stmt->bind_param('ii',$sales,$id);
    if (!$stmt->execute()) {
        throw new Exception("Error executing query 2: ". $mysqli->error);
    }
    $stmt->close();

    $query3 = "UPDATE LOCK_MONTHLY_SALES SET UPDCOUNT = UPDCOUNT + 1, PIC = NULL";
    $stmt = $mysqli->prepare($query3);
    $stmt->execute();
    $stmt->close();       

    $mysqli->commit();
} catch (Exception $e) {
    $isFailed = True;
    echo "Transaction failed: ". $e->getMessage();
    $mysqli->rollback();
} finally {
    $mysqli->close();
}
if(!$isFailed){
    echo 'Transaction successed';
}

?>