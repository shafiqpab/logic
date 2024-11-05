<?
include('../includes/common.php');
$con = connect();

function sql_insert2( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
{
	global $con ;
	if($contain_lob=="") $contain_lob=0;
	
	if( $contain_lob==0)
	{
		$count=count($arrValues);
		 //return $count."ss"; 
		if( $count >1 ) // Multirow
		{
			$k=1;	
			foreach( $arrValues as $rows)
			{
				
				if($k==1)
				{
					$strQuery= "INSERT ALL \n";
				}
				$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$rows." \n";
				if( $count==$k )
				{
					$count=$count-$k;
					$strQuery .= "SELECT * FROM dual";
					//return "=".$strQuery; 
					$stid =  oci_parse($con, $strQuery);
					//oci_execute("Character set is AL32UTF8");
					$exestd=oci_execute($stid, OCI_NO_AUTO_COMMIT);
					 if(!$exestd) return 0; //else return $exestd;
					$strQuery="";
					$k=0;
				}
				else if ( $k==50 )
				{
					$count=$count-$k;
					$strQuery .= "SELECT * FROM dual";
					//return $strQuery;
					$stid =  oci_parse($con, $strQuery);
					$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
					if(!$exestd) return 0;
					$strQuery="";
					$k=0;
				}
				$k++;
			}
			return 1;
			 
			//return $strQuery; 
		}
		else // Single Row
		{
			$strQuery= "INSERT  \n";
			foreach( $arrValues as $rows)
			{
				$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$rows." \n";
			}
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			//return $strQuery; 
			 return 1;
		}
	}
	else
	{
		$tmpv=explode(")",$arrValues);
		
		for($i=0; $i<count($tmpv)-1; $i++)
		{
			$strQuery="";
			$strQuery= "INSERT  \n";
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1); 
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
 
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return "0"; 
		}
		return "1";

	}
    return  $strQuery; die;
	//$strQuery .= "SELECT * FROM dual";
	//echo $strQuery;die;
	//$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;



	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	die;
}

function bulk_update_sql_statement2( $table, $id_column, $update_column, $data_values, $id_count )
{
	$field_array=explode("*",$update_column);
	$id_count_arr=array_chunk($id_count,'999');
	
	//echo "<pre>";print_r($id_count_arr);die;
	
	//$id_count=explode("*",$id_count);
	//$data_values=explode("*",$data_values);
	//print_r($data_values);die;
	
	$sql_up.= "UPDATE $table SET ";
	
	 for ($len=0; $len<count($field_array); $len++)
	 {
		 $sql_up.=" ".$field_array[$len]." = CASE $id_column ";
		 for ($id=0; $id<count($id_count); $id++)
		 {
			 if (trim($data_values[$id_count[$id]][$len])=="") $sql_up.=" when ".$id_count[$id]." then  '".$data_values[$id_count[$id]][$len]."'" ;
			 else $sql_up.=" when ".$id_count[$id]." then  ".$data_values[$id_count[$id]][$len]."" ;
		 }
		 if ($len!=(count($field_array)-1)) $sql_up.=" END, "; else $sql_up.=" END ";
	 }
	 if(count($id_count)>999)
	 {
		$sql_up.=" where";
		$p=1;
		foreach($id_count_arr as $id_arr)
		{
			if($p==1) $sql_up .=" $id_column in(".implode(',',$id_arr).")"; else $sql_up .=" or $id_column in(".implode(',',$id_arr).")";
			$p++;
		}
	 }
	 else
	 {
		$sql_up.=" where $id_column in (".implode(",",$id_count).")";
	 }
	 
	 return $sql_up;     
}


$booking_sql="select d.id as con_dtls_id, c.id as work_order_dtls_id, c.booking_no, c.trim_group, d.item_color, d.color_number_id, d.gmts_sizes, d.item_size, d.description 
from wo_booking_mst b, wo_booking_dtls c, wo_trim_book_con_dtls d
where b.booking_no=c.booking_no and c.id=d.wo_trim_booking_dtls_id and b.booking_type=2 and d.CONS>0 and b.status_active=1 and c.status_active=1 and d.status_active=1";
//echo $booking_sql;die;
$booking_result=sql_select($booking_sql);
$booking_data=array();
foreach($booking_result as $row)
{
	$booking_data[$row[csf("work_order_dtls_id")]][$row[csf("trim_group")]][$row[csf("item_color")]][$row[csf("color_number_id")]][$row[csf("gmts_sizes")]][$row[csf("item_size")]][$row[csf("description")]]=$row[csf("con_dtls_id")];
}




$pi_sql="select b.id, b.pi_id, b.work_order_dtls_id, b.work_order_no, b.item_group, b.item_color, b.color_id, b.size_id, b.item_size, b.item_description
from  com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.goods_rcv_status=2 and a.pi_basis_id=1 and b.booking_without_order<>1 and a.status_active=1 and b.status_active=1";

$pi_result=sql_select($pi_sql);
$pi_data=array();
$update_array = "book_con_dtls_id"; $i=1;
foreach($pi_result as $row)
{
	$book_con_dtls_id=0;
	$book_con_dtls_id=$booking_data[$row[csf("work_order_dtls_id")]][$row[csf("item_group")]][$row[csf("item_color")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_size")]][$row[csf("item_description")]];
	if($book_con_dtls_id>0)
	{
		$updateID_array[]=$row[csf("id")];
		$update_data[$row[csf("id")]]=explode("*",("'".$book_con_dtls_id."'"));
		$i++;
	}
	
}

//execute_query
$rID2=(bulk_update_sql_statement2("com_pi_item_details","id",$update_array,$update_data,$updateID_array));

echo $rID2;die;
//echo $rID."<br>".$rID2;die;

if($rID2)
{
	oci_commit($con); 
	echo "Success";
}
else
{
	oci_rollback($con); 
	echo "Failed";
}

?>