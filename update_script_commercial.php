<?
include('includes/common.php');

//$size_country_array[3][5]=10;
//$size_country_array[3][1]=15;
//$keys=array_keys($size_country_array[3]);

//echo $keys[0];
//print_r($keys);die;




if($db_type==0) $year_cond="YEAR(insert_date)"; 
else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";

$company_arr=return_library_array( "select id, importer_id from com_btb_lc_master_details",'id','importer_id'); 
$sql=sql_select("select * from com_import_payment where  status_active=1 and  is_deleted=0 ");
$payment_arr=array();
$payment_mst_id_arr=array();
$field_array="id,system_number_prefix,system_number_prefix_num,system_number,company_id,invoice_id,lc_id,payment_date,inserted_by,insert_date";

foreach( $sql as $row ) {
	$payment_arr[$row[csf('invoice_id')]][$row[csf('lc_id')]][$row[csf('payment_date')]][$row[csf('id')]]=$row[csf('id')];
}

$id=return_next_id( "id", "com_import_payment_mst", 1 ) ;
//$id=6;
$mrr_check_array=array();
foreach($payment_arr as $invoice_id=>$invoice_data) {
	foreach($invoice_data as $lc=>$lc_data) {
		foreach($lc_data as $paydate=>$paydata_data) {
			if( empty( $mrr_check_array[$company_arr[$lc]] ))
			{
				$new_return_number=explode("*",return_mrr_number( $company_arr[$lc], '', 'IMP', date("Y",time()), 5, "select system_number_prefix,system_number_prefix_num from com_import_payment_mst where company_id=".$company_arr[$lc]." and $year_cond=".date('Y',time())." order by id DESC", "system_number_prefix", "system_number_prefix_num" ));
			
			}
			else {
				$new_return_number[1]=$mrr_check_array[$company_arr[$lc]][1];
				$new_return_number[2]=$mrr_check_array[$company_arr[$lc]][2]+1;
				$new_return_number[0]=$mrr_check_array[$company_arr[$lc]][1]."".str_pad($new_return_number[2],5,"0",STR_PAD_LEFT);
				
			}
			
			if( !empty( $data_array ) ) $data_array .=",";
			$data_array.="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',".$company_arr[$lc].",".$invoice_id.",".$lc.",'".$paydate."',2,'".$pc_date_time."')";
			
			$mrr_check_array[$company_arr[$lc]][1]=$new_return_number[1];
			$mrr_check_array[$company_arr[$lc]][2]=$new_return_number[2];
			
			foreach($paydata_data as $pamyment_id=>$payment_id_data) {
				$payment_mst_id_arr[$pamyment_id]=$id;
				$updateID_array[]=$pamyment_id;
				$update_data[$pamyment_id][0]=$id;
			}
		
			$id++;
		
		}
	}
}

$con = connect();
$update_array ="mst_id";
$rID = sql_insert("com_import_payment_mst",$field_array,$data_array,1);
$rID2=execute_query(bulk_update_sql_statement("com_import_payment","id",$update_array,$update_data,$updateID_array),1);


if($db_type==0)
{
	if($rID && $rID2){
		mysql_query("COMMIT");  
		echo "Success";
	}
	else{
		mysql_query("ROLLBACK"); 
		echo "Faill";
	}
}
else if($db_type==2 || $db_type==1 )
{
	if($rID && $rID2){
		oci_commit($con);   
		echo "Success";
	}
	else{
		oci_rollback($con); 
		echo "Faill";
	}
}
disconnect($con);
die;



die;








//echo bulk_update_sql_statement_a("com_import_payment","id",$update_array,$update_data,$updateID_array);







$sql_invoice=sql_select("select b.id,a.exchange_rate,b.current_acceptance_value  from com_import_invoice_mst a,com_import_invoice_dtls b where a.id=b.import_invoice_id and a.exchange_rate is not null order by id desc");
//$i=1;
foreach( $sql_invoice as $row)
{
		$domestic_acceptance_value=$row[csf('current_acceptance_value')]*$row[csf('exchange_rate')];
		$update_id_arr[]=$row[csf('id')];
		$update_data_arr[$row[csf('id')]]=explode("*",("".$domestic_acceptance_value.""));
		//$i++;
}
$update_field="domestic_acceptance_value";
$upsubDtlsID=bulk_update_sql_statement("com_import_invoice_dtls","id",$update_field,$update_data_arr,$update_id_arr);
echo $upsubDtlsID."<br/><br/>";die;


$sql_invoice=sql_select("select a.invoice_date,b.currency_id,a.id from  com_import_invoice_mst a, com_btb_lc_master_details b where a.is_posted_account=0  and  a.btb_lc_id=to_char(b.id) group by a.invoice_date,b.currency_id,a.id order by id desc");
//print_r($sql_invoice);
foreach( $sql_invoice as $row)
{
	if( !empty($row[csf('invoice_date')])) {
		$sql_currency=sql_select("select currency,conversion_rate,con_date  from currency_conversion_rate  where con_date <='".$row[csf('invoice_date')]."' and currency=".$row[csf('currency_id')]." order by con_date desc");
		
		$update_id_arr[]=$row[csf('id')];
		$update_data_arr[$row[csf('id')]]=explode("*",("".$sql_currency[0][csf('conversion_rate')].""));
	}
}

$update_field="exchange_rate";
$upsubDtlsID=bulk_update_sql_statement("com_import_invoice_mst","id",$update_field,$update_data_arr,$update_id_arr);
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













function bulk_update_sql_statement_a( $table, $id_column, $update_column, $data_values, $id_count )
{
	$field_array=explode("*",$update_column);
	//print_r($field_array);die;
	//$id_count=explode("*",$id_count);
	//$data_values=explode("*",$data_values);
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
	 $sql_up.=" where $id_column in (".implode(",",$id_count).")";
	 return $sql_up;     
} 



















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
   // return  $strQuery; die;
	//$strQuery .= "SELECT * FROM dual";
	echo $strQuery;die;
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
	print_r($data_values);die;
	
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


$rcv_data_arr=array(); $issue_data_arr=array();

$sql_issue="select b.prod_id, b.id as trans_id, b.cons_quantity, b.transaction_type  from inv_transaction b where b.item_category in(1) and company_id=5 and b.transaction_type in(2,3,6) and b.status_active=1 and b.is_deleted=0 order by b.id";
$result_issue=sql_select($sql_issue);
foreach($result_issue as $row)
{
	$issue_data_arr[$row[csf('prod_id')]][$row[csf('trans_id')]]["cons_quantity"]=$row[csf('cons_quantity')];
	$issue_data_arr[$row[csf('prod_id')]][$row[csf('trans_id')]]["transaction_type"]=$row[csf('transaction_type')];
}


$sql_receive="select b.prod_id, b.id as trans_id, b.cons_quantity, b.cons_rate,b.transaction_type  from inv_transaction b where b.item_category in(1) and company_id=5 and b.transaction_type in(1,4,5) and b.status_active=1 and b.is_deleted=0 order by b.id";
$result_receive=sql_select($sql_receive);
foreach($result_receive as $row)
{
	$rcv_data_arr[$row[csf('prod_id')]][$row[csf('trans_id')]]["cons_quantity"]=$row[csf('cons_quantity')];
	$rcv_data_arr[$row[csf('prod_id')]][$row[csf('trans_id')]]["cons_rate"]=$row[csf('cons_rate')];
	$rcv_data_arr[$row[csf('prod_id')]][$row[csf('trans_id')]]["transaction_type"]=$row[csf('transaction_type')];
}


//echo count($issue_data_arr)."<br>";
//echo count($issue_data_arr)."<pre>";print_r($issue_data_arr);die;
//echo "<pre>";print_r($rcv_data_arr);die;

$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
$update_array = "balance_qnty*balance_amount*updated_by*update_date";
$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";


$i=1;$k=1;
$receive_balance_check=array();
foreach($issue_data_arr as $prod_id=>$prod_data)
{
	ksort($prod_data);
	//echo "<pre>";print_r($prod_data);die;
	foreach($prod_data as $issue_trans_id=>$val)
	{
		
		/*if($prod_check[$prod_id]=="")
		{
			$prod_check[]=$prod_id;
			$sql_receive=sql_select("select b.prod_id, b.id as rcv_trans_id, b.cons_quantity, b.cons_rate  from inv_transaction b where b.item_category=1 and b.transaction_type in(1,4,5) and b.status_active=1 and b.is_deleted=0 and b.prod_id=$prod_id order by b.id ASC");
		}*/
		
		$issue_qnty=$val["cons_quantity"];
		$issue_trans_type=$val["transaction_type"];
		
		foreach($rcv_data_arr[$prod_id] as $receive_trans_id=>$row)
		{
			
			$cons_rate = $row[("cons_rate")];
			if($issue_trans_type==2) $entry_form=3;
			else if($issue_trans_type==3) $entry_form=8;
			else $entry_form=10;
			//$k++; 
			if($receive_omite_check[$receive_trans_id]=="")
			{
				
				//$i++;
				
				if($receive_balance_check[$receive_trans_id]=="")
				{
					$balance_qnty = $row[("cons_quantity")];
				}
				else
				{
					
					$balance_qnty = number_format($receive_balance_check[$receive_trans_id],4,'.','')*1;
					
				}
				
				
				
				$transQntyBalance = number_format(($balance_qnty*1)-(number_format($issue_qnty,4,'.','')*1),4,'.','');
				
				if($transQntyBalance>number_format(0,4,'.',''))
				{
					$receive_balance_check[$receive_trans_id]=$transQntyBalance;
				}
				else
				{
					$receive_omite_check[$receive_trans_id]=$receive_trans_id;
				}
				
				//echo $i."<br>";
				if($transQntyBalance>=0)
				{
					
					//echo $receive_trans_id."**".$transQntyBalance."**".$balance_qnty."**".$issue_qnty."**".$row[("cons_quantity")];print_r($receive_balance_check);echo"<br>";
					if($transQntyBalance==0.0000)
					{
						$receive_balance_check[$receive_trans_id]="";
					}
					
					$transAmtBalance = $transQntyBalance*$cons_rate;
					$mrr_issue_qnty=$issue_qnty;
					$mrr_issue_amt=$mrr_issue_qnty*$cons_rate;
					$test_issue_qnty+=$mrr_issue_qnty;
					//if($data_array_mrr!="") $data_array_mrr .= ",";
					//$data_array[]  
					
					//echo $mrr_issue_qnty."**".$receive_balance_check[$receive_trans_id];echo"<br>";
					//echo $mrr_issue_qnty."**".$transQntyBalance;print_r($receive_balance_check);echo"<br>";
					
					$data_array_mrr[]= "(".$mrrWiseIsID.",".$receive_trans_id.",".$issue_trans_id.",".$entry_form.",".$prod_id.",".$mrr_issue_qnty.",".$cons_rate.",".$mrr_issue_amt.",'1','".$pc_date_time."')";
					
					
					
					if($rcv_trans_check[$receive_trans_id]=="")
					{
						$rcv_trans_check[$receive_trans_id]=$receive_trans_id;
						$updateID_array[]=$receive_trans_id;
					}
					 
					$update_data[$receive_trans_id]=explode("*",("".$transQntyBalance."*".$transAmtBalance."*'1'*'".$pc_date_time."'"));
					$mrrWiseIsID=$mrrWiseIsID+1;
					
					//echo "<pre>";print_r($data_array_mrr);
					//echo "<pre>";print_r($update_data);
					break;
					
					
				}
				else
				{
					//$balance_qnty = $issue_qnty-$receive_balance_check[$receive_trans_id];
					//echo $issue_qnty."=".$receive_balance_check[$receive_trans_id]."=".$balance_qnty."<br>";
					//$mrr_issue_qnty=$receive_balance_check[$receive_trans_id];
					
					
					/*if($issue_qnty>$receive_balance_check[$receive_trans_id] && $receive_balance_check[$receive_trans_id]>0)
					{
						$mrr_issue_qnty=$receive_balance_check[$receive_trans_id];
					}
					else
					{
						$mrr_issue_qnty=$balance_qnty;
					}*/
					//$mrr_issue_qnty=$issue_qnty-$balance_qnty;
					
					if($receive_balance_check[$receive_trans_id]!="")
					{
						$transferQntyBalance = $issue_qnty-$receive_balance_check[$receive_trans_id];
						$mrr_issue_qnty=$receive_balance_check[$receive_trans_id];
					}
					else
					{
						$transferQntyBalance = $issue_qnty-$balance_qnty;
						$mrr_issue_qnty=$balance_qnty;
					}
					
					
					
					$mrr_issue_amt=$mrr_issue_qnty*$cons_rate;
					
					/*if($transferQntyBalance>0 && $transferQntyBalance)
					{
						$receive_balance_check[$receive_trans_id]=$transferQntyBalance;
					}
					else
					{
						$receive_balance_check[$receive_trans_id]="";
					}*/
					
					$receive_balance_check[$receive_trans_id]="";
					
					//echo $mrr_issue_qnty."+**".$transferQntyBalance;print_r($receive_balance_check);echo"<br>";
					
					$test_issue_qnty+=$mrr_issue_qnty;
					
					//echo $mrr_issue_qnty."+".$transferQntyBalance."+".$receive_trans_id;print_r($receive_balance_check);echo"<br>";
					
					
					//$receive_balance_check[$receive_trans_id]="";
					
					//echo $receive_trans_id."**".$transQntyBalance."**".$balance_qnty."**".$issue_qnty."**".$row[("cons_quantity")];print_r($receive_balance_check);echo"<br>";
					//echo $mrr_issue_qnty;print_r($receive_balance_check);echo"<br>";
					
					
					
					$data_array_mrr[]= "(".$mrrWiseIsID.",".$receive_trans_id.",".$issue_trans_id.",".$entry_form.",".$prod_id.",'".$mrr_issue_qnty."',".$cons_rate.",".$mrr_issue_amt.",'1','".$pc_date_time."')";
					//$updateID_array[]=$receive_trans_id; 
					if($rcv_trans_check[$receive_trans_id]=="")
					{
						$rcv_trans_check[$receive_trans_id]=$receive_trans_id;
						$updateID_array[]=$receive_trans_id;
					}
					$update_data[$receive_trans_id]=explode("*",("0*0*'1'*'".$pc_date_time."'"));
					$mrrWiseIsID=$mrrWiseIsID+1;
					$issue_qnty = $transferQntyBalance;
					//$i++;
				}
				
			}
			
			
			
			
			
		}
	}
}
//echo "<br>".$test_issue_qnty;
//die;
//echo count($data_array_mrr);die;
//echo "<pre>";print_r($data_array_mrr);die;
//echo $i."==".$k;die;
//echo count($updateID_array);echo "<pre>";print_r($updateID_array);die;execute_query

//echo count($data_array_mrr);die;

$rID = sql_insert2("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
$rID2=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_array,$update_data,$updateID_array),1);
//echo $rID;die;
//echo $rID."<br>".$rID2;die;

if($rID && $rID2)
{
	oci_commit($con); 
	echo "Success";
}
else
{
	oci_rollback($con); 
	echo "Failed";
}






/*$sql="select b.prod_id from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by prod_id order by prod_id";
$result=sql_select($sql);
foreach($result as $row)
{
	if(in_array($row[csf('prod_id')],$only_trims_arr))
	{
		$both_arr[$id]=$row[csf('prod_id')];
		$id++;
	}
	else
	{
		$only_gi_arr[]=$row[csf('prod_id')];
	}
}*/

//echo implode(",",$both_arr)."<br>";
//echo count($both_arr)."<br>";
//var_dump($both_arr);
//var_dump($only_gi_arr);
/*if(count($both_arr)>0)
{
	$id_string=""; $previds=''; $newids='';
	foreach($both_arr as $newId=>$previd)
	{
		$id_string.=" WHEN $previd THEN $newId";
		$previds.=$previd.",";
		$newids.=$newId.",";
	}
	
	$id_string_prod="CASE id ".$id_string." END";
	$id_string_trans="CASE b.prod_id ".$id_string." END";
	$id_string_nonOrder="CASE item_id ".$id_string." END";
	$id_string_mrr="CASE prod_id ".$id_string." END";
	
	$sql_insert="insert into product_details_master(id, company_id,supplier_id,store_id, item_category_id,entry_form, detarmination_id,sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, re_order_label,minimum_label,maximum_label, item_account,packing_type,avg_rate_per_unit, last_purchased_qnty, current_stock, last_issued_qnty, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color,gmts_size,gsm,brand, brand_supplier, dia_width, item_size, weight,allocated_qnty,available_qnty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
				select	
					$id_string_prod, company_id,supplier_id,store_id, item_category_id,20,detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code,unit_of_measure, re_order_label,minimum_label,maximum_label, item_account,packing_type,avg_rate_per_unit, last_purchased_qnty, current_stock, last_issued_qnty, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color,gmts_size,gsm,brand, brand_supplier, dia_width, item_size, weight,allocated_qnty,available_qnty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from product_details_master where id in (".substr($previds,0,-1).")";
	
	$trans_sql="update inv_transaction b set b.prod_id=".$id_string_trans." where b.id in(select b.id from inv_receive_master a where a.id=b.mst_id and a.entry_form=20 
and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id in(".substr($previds,0,-1).") and b.transaction_type=1 and b.item_category=4)";

	$trans_sql2="update inv_transaction b set b.prod_id=".$id_string_trans." where b.id in(select b.id from inv_issue_master a where a.id=b.mst_id and a.entry_form in (21,26)
and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id in(".substr($previds,0,-1).") and b.transaction_type in (2,3) and b.item_category=4)";

	$purc_sql="update inv_purchase_requisition_dtls set product_id=".$id_string_prod." where product_id in(".substr($previds,0,-1).")";
	$nonOrder_sql="update wo_non_order_info_dtls set item_id=".$id_string_nonOrder." where item_id in(".substr($previds,0,-1).")";
	$mrr_sql="update inv_mrr_wise_issue_details set prod_id=".$id_string_mrr." where prod_id in(".substr($previds,0,-1).") and entry_form=21";
	//$serial_sql="update inv_serial_no_details set product_id=".$id_string_prod." where product_id in(".substr($previds,0,-1).")";$serial=execute_query($serial_sql,0);

	$both_ids=$previds.substr($newids,0,-1);
	
	$sql_trans=sql_select("SELECT prod_id, (sum(case when transaction_type in(1,4) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3) then cons_quantity else 0 end)) as current_stock from inv_transaction where status_active=1 and is_deleted=0 and prod_id in(".$both_ids.") and item_category=4 group by prod_id");
	$update_field="current_stock";
	foreach($sql_trans as $row)
	{
		$update_id_arr[]=$row[csf('prod_id')];
		$update_data_arr[$row[csf('prod_id')]]=explode("*",($row[csf('current_stock')]));
	}
	
	
	
	$upsubDtlsID=bulk_update_sql_statement("product_details_master","id",$update_field,$update_data_arr,$update_id_arr);
	$rID=sql_multirow_update("product_details_master","entry_form","24","id",implode(",",$only_trims_arr),0);
	$rID2=sql_multirow_update("product_details_master","entry_form","20","id",implode(",",$only_gi_arr),0);
	$rID3=execute_query($sql_insert,0);
	
	$inv_trans=execute_query($trans_sql,0);
	$inv_trans2=execute_query($trans_sql2,0);
	$inv_purc=execute_query($purc_sql,0);
	$non_order=execute_query($nonOrder_sql,0);
	$mrr=execute_query($mrr_sql,0);
	$rID4=execute_query($upsubDtlsID);
	
	if($rID && $rID2 && $rID3 && $inv_trans && $inv_trans2 && $inv_purc && $non_order && $mrr && $rID4)
	{
		oci_commit($con); 
		echo "Success";
	}
	else
	{
		oci_rollback($con); 
		echo "Failed";
	}
}*/


?>