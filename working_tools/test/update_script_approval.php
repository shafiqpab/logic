<?
include('../includes/common.php');
// ===========================for fabric booking approval===========================================

/*BEGIN
        FOR i IN (select a.id, a.is_approved, b.sequence_no,B.APPROVED_BY,b.id as approve_id
                        from wo_booking_mst a, approval_history b
                        where a.id=b.mst_id   and b.entry_form=7 and a.company_id=2 and a.is_short in(2,3) and a.booking_type=1
                            and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0  and 
                            b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved  in (1,3) and B.APPROVED_BY=146
                        group by a.id, a.is_approved, b.sequence_no,B.APPROVED_BY,b.id )
        LOOP
            update APPROVAL_HISTORY set SEQUENCE_NO=6  where ENTRY_FORM =7 and  id=i.approve_id and APPROVED_BY=146;
        END LOOP;
    END;*/
/*   ===========================for precosting approval===========================================   
    BEGIN
        FOR i IN (select b.id, c.id as approval_id, c.sequence_no,c.ENTRY_FORM
                  from wo_pre_cost_mst b,  wo_po_details_master a,approval_history c
                  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=2  and a.is_deleted=0 and
                  a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and
                  b.is_deleted=0 and b.approved in (1,3) and C.APPROVED_BY=146
                   group by b.id, c.id, c.sequence_no,c.ENTRY_FORM  )
        LOOP
            update APPROVAL_HISTORY set SEQUENCE_NO=6  where ENTRY_FORM =15 and  id=i.approval_id and APPROVED_BY=146;
        END LOOP;
    END;*/
/*  ===========================for precosting approval===========================================   
    BEGIN
        FOR i IN ( SELECT a.id, b.id as approval_id, b.sequence_no, b.approved_by
                    from wo_booking_mst a, approval_history b
                   where a.id=b.mst_id  and b.entry_form=12 and a.is_short=1 and a.booking_type=1 and a.company_id=2 and 
                   a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and
                    b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in(1,3) and b.approved_by=148  )
        LOOP
            update APPROVAL_HISTORY set SEQUENCE_NO=4  where ENTRY_FORM =12 and  id=i.approval_id and APPROVED_BY=148;
        END LOOP;
    END;

      ===========================for trims booking approval===========================================
      with order     
    BEGIN
        FOR i IN (select a.id,  d.id as approval_id,D.SEQUENCE_NO,D.APPROVED_BY
          from wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c, approval_history d 
          where a.id=d.mst_id and a.booking_no=b.booking_no and b.job_no=c.job_no and d.entry_form=8 and a.company_id=2 and a.item_category in(4) and
           a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and a.is_approved in(1,3)  and a.ready_to_approved=1 and D.APPROVED_BY=140
            group by a.id,  d.id,D.SEQUENCE_NO,D.APPROVED_BY  )
        LOOP
            update APPROVAL_HISTORY set SEQUENCE_NO=5  where ENTRY_FORM =8 and  id=i.approval_id and APPROVED_BY=140;
        END LOOP;
    END;
	booking without order
	 BEGIN
        FOR i IN ( select a.id, d.id as approval_id,a.is_approved,D.SEQUENCE_NO,D.APPROVED_BY from wo_non_ord_samp_booking_mst a, approval_history d where a.id=d.mst_id and d.entry_form=8 and
          a.company_id=2 and a.item_category in(4) and a.booking_type=5 and a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and
           a.is_approved in(1,3) and a.ready_to_approved=1 and D.APPROVED_BY=148 )
        LOOP
            update APPROVAL_HISTORY set SEQUENCE_NO=4  where ENTRY_FORM =8 and  id=i.approval_id and APPROVED_BY=148;
        END LOOP;
    END;
	//



			====================Sample Fabric Booking Aproval-With order=====================================
     BEGIN
        FOR i IN ( select a.id, 
      b.id as approval_id, b.sequence_no, b.approved_by, 
      a.is_apply_last_update,c.grouping, c.file_no from wo_booking_mst a, approval_history b,wo_po_break_down c where a.id=b.mst_id and a.job_no=c.job_no_mst
       and b.entry_form=13 and a.company_id=2 and a.is_short=2 and a.booking_type=4 and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and 
       b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in(1,3) and B.APPROVED_BY=146 )
        LOOP
            update APPROVAL_HISTORY set SEQUENCE_NO=6  where ENTRY_FORM =13 and  id=i.approval_id and APPROVED_BY=146;
        END LOOP;
    END;

    */

$sql=sql_select("select b.id, c.id as approval_id, c.sequence_no,c.entry_form
                  from wo_pre_cost_mst b,  wo_po_details_master a,approval_history c
                  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=2  and a.is_deleted=0 and
                  a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and
                  b.is_deleted=0 and b.approved in (1,3) and c.entry_form=15 
                   group by b.id, c.id, c.sequence_no,c.entry_form ");

$update_field="sequence_no*approved_by";
foreach($sql as $row)
{
	$update_id_arr[]=$row[csf('approval_id')];
	$update_data_arr[$row[csf('approval_id')]]=explode("*",("1*94"));
	
}

//echo implode(",",$update_id_arr);die;
$upsubDtlsID=bulk_update_sql_statement("approval_history","id",$update_field,$update_data_arr,$update_id_arr);
echo $upsubDtlsID."<br/><br/>";die;




function sql_insert1( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
{
	global $con ;
	if($contain_lob=="") $contain_lob=0;
	if( $contain_lob==0)
	{
		$tmpv=explode(")",$arrValues);
		if(count($tmpv)>2)
			$strQuery= "INSERT ALL \n";
		else
			$strQuery= "INSERT  \n";
			
		for($i=0; $i<count($tmpv)-1; $i++)
		{
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1); 
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
		}
		
	   if(count($tmpv)>2) $strQuery .= "SELECT * FROM dual";
	 //return $strQuery ;
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
			//return $strQuery ;
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return "0"; 
		}
		return "1";
	    
	}
 //  return  $strQuery; die;
	echo $strQuery;die;
	//$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;



	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	die;
	
	if ( $commit==1 )
	{
		if (!oci_error($exestd))
		{
			$pc_time= add_time(date("H:i:s",time()),360); 
			$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
	        $pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));
			
			$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_date_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')"; 
			$resultss=oci_parse($con, $strQuery);
			oci_execute($resultss);
			$_SESSION['last_query']="";
			//oci_commit($con); 
			return "0";
		}
		else
		{
			//oci_rollback($con);
			return "10";
		}
	}
	else return 1;
	//else
		//return 0;
		
	die;
}

	$id=return_next_id("id","product_details_master",1);
	$field_array="id,company_id,item_category_id,entry_form,item_group_id,sub_group_code,sub_group_name,item_code, item_description, product_name_details,item_size,re_order_label,minimum_label,maximum_label,unit_of_measure,item_account,inserted_by,insert_date,status_active,is_deleted";
	
	$nameArray=sql_select("select * from product_details_master where company_id=3 and item_category_id in(4,8,9,10,11,15,16,17,18,19,20,21,22) and id<4000");
	
	foreach($nameArray as $row)
	{
		if( !empty( $data_array ) )	$data_array.=",";
		$data_array.="(".$id.",1,".$row[csf('item_category_id')].",'".$row[csf('entry_form')]."','".$row[csf('item_group_id')]."','".$row[csf('sub_group_code')]."','".$row[csf('sub_group_name')]."','".$row[csf('item_code')]."','".$row[csf('item_description')]."','".$row[csf('product_name_details')]."','".$row[csf('item_size')]."','".$row[csf('re_order_label')]."','".$row[csf('minimum_label')]."','".$row[csf('maximum_label')]."','".$row[csf('unit_of_measure')]."','".$row[csf('item_account')]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',".$row[csf('status_active')].",0)";
		$id++;
	}
	$rID2=sql_insert1("PRODUCT_DETAILS_MASTER_asrotex",$field_array,$data_array,0);
	if($rID2==1){ echo "data insert successfully";die;}
	else { echo "Problem in data inserting";die; }
echo $data_array;die;



$sql_trans=sql_select("select id, supplier_id from inv_receive_master where  receive_purpose=5 and loan_party=0 and status_active=1 and is_deleted=0 and entry_form=4 order by id");

$lib_up_data=array();
foreach($sql_trans as $row)
{
	$lib_up_data[$row[csf('other_id')]]=$row[csf('sup_id')];
}

//var_dump($lib_up_data);die;

$sql_rcv=sql_select("select id, loan_party  from   INV_RECEIVE_MASTER  where loan_party in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15) and loan_party>0");//
$update_field="loan_party";
foreach($sql_rcv as $row)
{
	$update_id_arr[]=$row[csf('id')];
	$update_data_arr[$row[csf('id')]]=explode("*",("".$lib_up_data[$row[csf('loan_party')]].""));
}
$upsubDtlsID=bulk_update_sql_statement("INV_RECEIVE_MASTER","id",$update_field,$update_data_arr,$update_id_arr);
echo $upsubDtlsID."<br/><br/>";die;
































$sql=sql_select("select id,barcode_no,entry_form,roll_id from pro_roll_details where entry_form  in(66,67,68) order by barcode_no,entry_form");
foreach($sql as $row)
{
	if($row[csf('entry_form')]==66) 
	{
		$roll_id=$row[csf('roll_id')];
	}
	if($row[csf('entry_form')]>66) 
	{
		$update_data_arr[$row[csf('id')]]=explode("*",("".$roll_id.""));	
		$update_id_arr[]=$row[csf('id')];
	} 
}
//echo count($update_id_arr)."<br>";
//echo count($sql)."<br>";
$update_field="roll_id";
echo $upsubDtlsID=bulk_update_sql_statement("pro_roll_details","id",$update_field,$update_data_arr,$update_id_arr);die;















$sql=sql_select("select prod_id, sum(cons_amount)/sum(cons_quantity) as rate, b.avg_rate_per_unit, b.current_stock from inv_transaction a, product_details_master b where a.prod_id=b.id  and a.status_active=1 and a.is_deleted=0 AND transaction_type IN (1,4,5) and item_category=4 and item_category_id=4 and entry_form=24 group by prod_id order by prod_id");
foreach($sql as $row)
{
	if(number_format($row[csf('rate')],4,'','.')!=number_format($row[csf('avg_rate_per_unit')],4,'','.')) 
	{
		$avg_rate_per_unit=$row[csf('rate')];
		$stock_value=$row[csf('current_stock')]*$avg_rate_per_unit;
		$update_data_arr[$row[csf('prod_id')]]=explode("*",("".$avg_rate_per_unit."*".$stock_value.""));	
		$update_id_arr[]=$row[csf('prod_id')];
	} 
}
//echo count($update_id_arr)."<br>";
//echo count($sql)."<br>";
$update_field="avg_rate_per_unit*stock_value";
echo $upsubDtlsID=bulk_update_sql_statement("product_details_master","id",$update_field,$update_data_arr,$update_id_arr);die;
//echo $upsubDtlsID."<br/><br/>";die;

/*update inv_issue_master set batch_no=( select batch_id from dyes_chem_issue_requ_mst a,inv_issue_master b where b.req_no=a.id) where id in (SELECT id
FROM inv_issue_master
WHERE `issue_basis` =7
AND `entry_form` =5
AND `batch_no` = '0'
)*/
/*$sql=sql_select("select b.id,a.batch_id from dyes_chem_issue_requ_mst a,inv_issue_master b where b.req_no=a.id and b.issue_basis =7
AND b.entry_form=5 AND b.batch_no= '0' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 ");
foreach($sql as $row)
{
    $update_data_arr[$row[csf('id')]]=explode("*",("".$row[csf('batch_id')].""));	
   	$update_id_arr[]=$row[csf('id')];
	
}


$update_field="batch_no";
$upsubDtlsID=bulk_update_sql_statement("inv_issue_master","id",$update_field,$update_data_arr,$update_id_arr);
echo $upsubDtlsID."<br/><br/>";die;
*/





/*
die;
$sql=sql_select("select a.id,b.avg_rate_per_unit from inv_transaction a,product_details_master b where a.prod_id=b.id and  a.transaction_type in(2,6)and a.status_active=1 and a.is_deleted=0 and a.item_category in (5,6,7) and a.prod_id>0 and a.cons_rate=0  and b.status_active=1 and b.is_deleted=0 ");
foreach($sql as $row)
{
    $update_data_arr[$row[csf('id')]]=explode("*",("".$row[csf('avg_rate_per_unit')].""));	
   	$update_id_arr[]=$row[csf('id')];
	
}


$update_field="cons_rate";
$upsubDtlsID=bulk_update_sql_statement("inv_transaction","id",$update_field,$update_data_arr,$update_id_arr);
echo $upsubDtlsID."<br/><br/>";die;*/
//print_r($prod_id_arr);die;

$id="1442,1562,807,685,686,803,804,805,808,809,810,27124,27120,577,757,758,759,760,762,828,829,830,32806,31221,812,814,815,816,72,566,550,551,554,555,918,528,529,952,899,833,1427";



/* *current_stock*stock_value
$update_case="update  inv_transaction set cons_rate= case prod_id ";
$update_field="avg_rate_per_unit";
foreach($sql_trans as $row)
{
	$update_id_arr[]=$row[csf('prod_id')];
	$update_data_arr[$row[csf('prod_id')]]=explode("*",(number_format($row[csf('order_rate')],8)));
	$update_case.="     when ".$row[csf('prod_id')]." then ".number_format($row[csf('order_rate')],8)."";
}
$update_case.=" end ";
$update_case.="  where transaction_type=2 and item_category in(5,6,7) and status_active=1 and is_deleted=0 and prod_id in (".implode(",",$update_id_arr).")";

echo $update_case;

$con = connect();
// ==============================for production hour ===========================================================================================
  // for hour test=============================================================================================================================
  	$i=0; $flag=1;
	//$sql_time=sql_select("select id,production_hour,production_date from pro_gar_prod_gross_mst");
	$sql_time=sql_select("select id,production_hour_prev as production_hour,production_date from pro_garments_production_mst where production_type in(5)");//1,5,7,8,9
	$update_field="production_hour";
	foreach($sql_time as $time_r)
	{
		$i++;
		$time_hour=explode(".",$time_r[csf("production_hour")]) ;
		$time_date=$time_r[csf("production_date")] ;
		if($time_hour[1]=="") $time_min="00";  
		else 
		{
			$time_min=".".$time_hour[1];
			$time_min=($time_min*60);
		}
		
		if($time_hour[0]>23)
		{
			$actual_time="23:".$time_min.":00";
		}
		else
		{
			$actual_time=$time_hour[0].":".$time_min.":00";
		}
		
		$update_id_arr[]=$time_r[csf("id")];
		$update_data_arr[$time_r[csf("id")]]=explode("*",("'".$actual_time."'"));
		
		if($i>9999)
		{
			$upsubDtlsID=bulk_update_sql_statement("pro_garments_production_mst","id",$update_field,$update_data_arr,$update_id_arr);
			//echo $upsubDtlsID;
			$rID=execute_query($upsubDtlsID);

			if($flag==1)
			{
				if($rID) {$flag=1;} else {$flag=0;}
			}
			if($flag==0) {echo $upsubDtlsID;die;}
			unset($update_id_arr);
			unset($update_data_arr);
			$i=0;
		}
		
	}
	
	$upsubDtlsID=bulk_update_sql_statement("pro_garments_production_mst","id",$update_field,$update_data_arr,$update_id_arr);
	
	$rID=execute_query($upsubDtlsID);
	/*if($flag==1)
	{
		if($rID) $flag=1; else $flag=0;
	}
	
	if($flag==1)
	{
		oci_commit($con); 
		echo "Success";
	}
	else
	{
		oci_rollback($con);
		echo "Failed";
	}*/
	
	//if($rID) echo "Success"; else {echo "Failed";echo $upsubDtlsID;die;}
	
	//die;
	/*$upsubDtlsID=bulk_update_sql_statement("pro_garments_production_mst","id",$update_field,$update_data_arr,$update_id_arr);
	echo $upsubDtlsID."<br/><br/>";	*/
	
	/*$sql_time=sql_select("select id,hour,production_date from subcon_gmts_prod_dtls");
	$update_field="hour2";
	foreach($sql_time as $time_r)
	{
		$time_hour=explode(".",$time_r[csf("hour")]) ;
		$time_date=$time_r[csf("production_date")] ;
		if($time_hour[1]=="") $time_min="00";  
		else 
		{
			$time_min=".".$time_hour[1];
			$time_min=($time_min*60);
		}
		
		if($db_type==0)
		{
			$actual_time=$time_hour[0].":".$time_min.":00";
			$update_id_arr[]=$time_r[csf("id")];
			$update_data_arr[$time_r[csf("id")]]=explode("*",("'".$actual_time."'"));
		}
		else
		{
			$actual_time=$time_date." ".$time_hour[0].":".$time_min.":00";  
			$update_id_arr[]=$time_r[csf("id")];  
			$update_data_arr[$time_r[csf("id")]]=explode("*",("to_date('".$actual_time."','DD MONTH YYYY HH24:MI:SS')"));
		}
		
		//$update_data_arr[$time_r[csf("id")]]=explode("*",("to_date('".$actual_time."','DD MONTH YYYY HH24:MI:SS')"));
	}

	$upsubDtlsID=bulk_update_sql_statement("subcon_gmts_prod_dtls","id",$update_field,$update_data_arr,$update_id_arr);
	echo $upsubDtlsID."<br/><br/>";	
*/