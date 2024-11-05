<?
include('includes/common.php');
$con = connect();

echo $id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
?>