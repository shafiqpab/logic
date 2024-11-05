<?
include('includes/common.php');
$con = connect();

//echo $db_type;die;
//if($con) echo "yes";else echo "no";die;

/*$pageNameBySeq=array("INV_TRIMS_ENTRY_DTLS"=>"INV_TRIMS_ENTRY_DTLS_PK_SEQ","INV_STORE_WISE_QTY_DTLS"=>"INV_STORE_WISE_QTY_DTLS_PK_SEQ","INV_SERIAL_NO_DETAILS"=>"INV_SERIAL_NO_DETAILS_PK_SEQ","INV_MRR_WISE_ISSUE_DETAILS"=>"INV_MRR_WISE_ISSUE_PK_SEQ","INV_TRIMS_ISSUE_DTLS"=>"INV_TRIMS_ISSUE_DTLS_PK_SEQ","INV_GOODS_PLACEMENT"=>"INV_GOODS_PLACEMENT_PK_SEQ","PRO_BATCH_CREATE_DTLS"=>"PRO_BATCH_CREATE_DTLS_PK_SEQ","INV_GREY_FABRIC_ISSUE_DTLS"=>"INV_GREY_FAB_ISS_DTLS_PK_SEQ","PRO_BATCH_CREATE_MST"=>"PRO_BATCH_CREATE_MST_PK_SEQ","DYES_CHEM_ISSUE_DTLS"=>"DYES_CHEM_ISSUE_DTLS_PK_SEQ","PRO_ROLL_SPLIT"=>"PRO_ROLL_SPLIT_PK_SEQ","INV_RECEIVE_MAS_BATCHROLL"=>"INV_RCV_MAS_BATC_PK_SEQ","PRO_GREY_BATCH_DTLS"=>"PRO_GREY_BATCH_DTLS_PK_SEQ","PRO_GREY_PROD_DELIVERY_MST"=>"PRO_GREY_PROD_DELI_MST_PK_SEQ","PRO_GREY_PROD_DELIVERY_DTLS"=>"PRO_GREY_PROD_DELI_DTLS_PK_SEQ","PRO_BATCH_TRIMS_DTLS"=>"PRO_BATCH_TRIMS_DTLS_PK_SEQ","PRO_FINISH_FABRIC_RCV_DTLS"=>"PRO_FIN_FAB_RCV_DTLS_PK_SEQ","INV_FINISH_FABRIC_ISSUE_DTLS"=>"INV_FIN_FAB_ISSUE_DTLS_PK_SEQ","INV_WVN_FINISH_FAB_ISS_DTLS"=>"INV_WV_FIN_FAB_ISS_DTLS_PK_SEQ","FABRIC_SALES_ORDER_MST"=>"FABRIC_SALES_ORDER_MST_PK_SEQ",
"FABRIC_SALES_ORDER_DTLS"=>"FABRIC_SALES_ORDER_DTLS_PK_SEQ");*/
//$entry_form_wise_mrr_tble=array(); 115 => "Roll Receive by Finish Process"


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

$entry_form_wise_mrr_tble=array(1=>"inv_receive_master",2=>"inv_receive_master",3=>"inv_issue_master",4=>"inv_receive_master",5=>"inv_issue_master",7=>"inv_receive_master",8=>'inv_issue_master',9=>'inv_receive_master',10=>'inv_item_transfer_mst',11=>'inv_item_transfer_mst',12=>'inv_item_transfer_mst',13=>'inv_item_transfer_mst',14=>'inv_item_transfer_mst',15=>'inv_item_transfer_mst',16=>'inv_issue_master',17=>'inv_receive_master',18=>'inv_issue_master',19=>'inv_issue_master',20=>'inv_receive_master',21=>'inv_issue_master',22=>'inv_receive_master',23=>'inv_receive_master',24=>'inv_receive_master',25=>'inv_issue_master',26=>'inv_issue_master',27=>'inv_receive_master',28=>'inv_issue_master',29=>'inv_receive_master',37=>"inv_receive_master",45=>"inv_issue_master",46=>"inv_issue_master",49=>"inv_issue_master",50=>"inv_issue_master",51=>"inv_receive_master",52=>"inv_receive_master",53=>"pro_grey_prod_delivery_mst",54=>"pro_grey_prod_delivery_mst",55=>"inv_item_transfer_mst",56=>"pro_grey_prod_delivery_mst",57=>"inv_item_transfer_mst",58=>"inv_receive_master",61=>"inv_issue_master",62=>"inv_receive_mas_batchroll",63=>"inv_receive_mas_batchroll",65=>"inv_receive_mas_batchroll",66=>"inv_receive_master",67=>"pro_grey_prod_delivery_mst",68=>"inv_receive_master",71=>"inv_issue_master",72=>"inv_receive_mas_batchroll",73=>"inv_receive_master",75=>"pro_roll_split",78=>"inv_item_transfer_mst",80=>"inv_item_transfer_mst",81=>"inv_item_transfer_mst",82=>"inv_item_transfer_mst",83=>"inv_item_transfer_mst",84=>"inv_receive_master",91=>"inv_receive_mas_batchroll",109=>"fabric_sales_order_mst",110=>"inv_item_transfer_mst",112=>"inv_item_transfer_mst",113=>"pro_roll_split",126 =>"inv_receive_master",133=>"inv_item_transfer_mst",134=>"inv_item_transfer_mst",141=>"pro_roll_split",502=>"pro_roll_details");

$mrr_table_wise_prefix_num=array("inv_receive_master"=>"recv_number_prefix_num","inv_issue_master"=>"issue_number_prefix_num","inv_item_transfer_mst"=>"transfer_prefix_number","pro_roll_split"=>"system_number_prefix_num","fabric_sales_order_mst"=>"job_no_prefix_num","inv_receive_mas_batchroll"=>"recv_number_prefix_num","pro_grey_prod_delivery_mst"=>"sys_number_prefix_num","pro_roll_details"=>"barcode_suffix_no");

$sql_company=sql_select("select id, company_name from lib_company where status_active=1");
$field_arr="table_name,next_id,company_id,entry_form,year,item_category_id,booking_type,production_type,emblishment_type,transfer_criteria";
if($db_type==0) $data_arr=""; else $data_arr=array();
$i=1;
$year_id=date("Y",time());
$roll_check=array();
if($db_type==0) $year_cond=" and year(insert_date)='$year_id'"; else $year_cond=" and to_char(insert_date,'YYYY')='$year_id'";
foreach($sql_company as $row)
{
	foreach($entry_form_wise_mrr_tble as $entry_form=>$table_name)
	{
		if($entry_form==502)
		{
			
			$mrr_prefix_sql=sql_select("select max(barcode_suffix_no) as max_prefix_num from pro_roll_details where status_active=1  and entry_form=2  $year_cond");
			$max_mrr_prefix_num=$mrr_prefix_sql[0][csf("max_prefix_num")];
			if($max_mrr_prefix_num=="") $max_mrr_prefix_num=0;
			if($roll_check[$table_name]=="")
			{
				$roll_check[$table_name]=$table_name;
				if($db_type==0)
				{
					if($data_arr!="") $data_arr.=",";
					$data_arr.="('".strtoupper($table_name)."','".$max_mrr_prefix_num."',0,2,'".$year_id."',0,0,0,0,0)";
				}
				else
				{
					$data_arr[]="('".strtoupper($table_name)."','".$max_mrr_prefix_num."',0,2,'".$year_id."',0,0,0,0,0)";
				}
			}
			
		}
		else
		{
			if($table_name!="fabric_sales_order_mst") $entry_form_cond=" and entry_form=$entry_form"; else $entry_form_cond=" ";
			$mrr_prefix_sql=sql_select("select max(".$mrr_table_wise_prefix_num[$table_name].") as max_prefix_num from $table_name where status_active=1 and company_id=".$row[csf("id")]." $entry_form_cond $year_cond");
			$max_mrr_prefix_num=$mrr_prefix_sql[0][csf("max_prefix_num")];
			if($max_mrr_prefix_num=="") $max_mrr_prefix_num=0;
			
			if($db_type==0)
			{
				if($data_arr!="") $data_arr.=",";
				$data_arr.="('".strtoupper($table_name)."','".$max_mrr_prefix_num."','".$row[csf("id")]."','".$entry_form."','".$year_id."',0,0,0,0,0)";
			}
			else
			{
				$data_arr[]="('".strtoupper($table_name)."','".$max_mrr_prefix_num."','".$row[csf("id")]."','".$entry_form."','".$year_id."',0,0,0,0,0)";
			}
		}
		
		
		$i++;
	}
}
//die;
//print_r($data_arr);die;

//echo "insert into platform_sequence_pk ($field_arr) values $data_arr";die;



//echo $mrrWiseSeq;die;
if ($db_type == 0)
{
	if ($data_arr == "") 
	{
		echo "No Data Found";die;	
	}
	$mrrWiseSeq= sql_insert("platform_sequence_mrr", $field_arr, $data_arr, 1);
	if ($mrrWiseSeq) 
	{
		mysql_query("COMMIT");
		echo "Data Save Successfully";
	}
	else
	{
		mysql_query("ROLLBACK");
		echo "Data Does Not Save Successfully";
	}
}
else
{
	if (count($data_arr)<1) 
	{
		echo "No Data Found";die;	
	}
	$mrrWiseSeq= sql_insert2("platform_sequence_pk", $field_arr, $data_arr, 1);
	if ($mrrWiseSeq) 
	{
		oci_commit($con);
		echo "Data Save Successfully";
	}
	else
	{
		oci_rollback($con);
		echo "Data Does Not Save Successfully";
	}
}


 
?>