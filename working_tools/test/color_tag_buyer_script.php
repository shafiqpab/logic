<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$color_sql="select id, tag_buyer from lib_color where status_active=1 and is_deleted=0 ";

$color_sql_res=sql_select($color_sql); $i=0;
$field_array="id,color_id,buyer_id";
$buyer_id=4;
$i=0;
$id = return_next_id( "id", "lib_color_tag_buyer", 1 );
foreach($color_sql_res as $row)
{
    $i++; 
    $tag_buyer="";$tag_buyer_arr=array(); 
    $tag_buyer=implode(",",array_unique(explode(",",$row[csf('tag_buyer')]))); 
    // if($row[csf('tag_buyer')]!=''){        
    //     $tag_buyer=$row[csf('tag_buyer')].','.$buyer_id;
    // }
    $color_id=$row[csf('id')];
	$up=execute_query("update lib_color set tag_buyer='$tag_buyer' where id=$color_id");
    if($up==false){ echo "update lib_color set tag_buyer='$tag_buyer' where id=$color_id";oci_rollback($con);die;}
    $tag_buyer_arr=explode(",",$tag_buyer);
    foreach($tag_buyer_arr as $buyerid){
        $rID=execute_query("insert into lib_color_tag_buyer (id,color_id,buyer_id) values ($id,$color_id,$buyerid)");
        if($rID==false){echo "insert into lib_color_tag_buyer (id,color_id,buyer_id) values ($id,$color_id,$buyerid)";oci_rollback($con);die;}       
        $id++;
    }
    
    
	
}

if($rID && $up)
{
    oci_commit($con); 
    echo "Success=".$i;
}
else
{
    oci_rollback($con);
    echo "Not Success=".$i;
}

