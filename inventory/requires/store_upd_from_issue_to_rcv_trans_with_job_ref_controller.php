<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data)
		{
			$('#txt_job_no').val(data);
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:630px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="txt_job_no" id="txt_job_no" value="" />
                    </th> 					
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'create_job_no_search_list_view', 'search_div', 'store_upd_from_issue_to_rcv_trans_with_job_ref_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:05px" id="search_div"></div>

		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
		
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond order by job_no DESC";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "120,130,60,80","620","270",0, $sql , "js_set_value", "job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no", "",'','0,0,0,0,0','',0) ;
   exit(); 
}

if ($action=="synchronize_stock")
{
	extract($_REQUEST);
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$po_sql= sql_select("select b.id from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_id and a.job_no = '$txt_job_no' and to_char(a.insert_date,'YYYY')=$cbo_year ");

	foreach ($po_sql as $val) 
	{
		$po_no_arr[$val[csf("id")]] = $val[csf("id")];
	}

	if(empty($po_no_arr))
	{
		echo "Order Not Found";
		die;
	}

	$barcode_ref_sql=sql_select("select barcode_no from  pro_roll_details where entry_form in (22,58,82,83,84,110,180,183,61) and status_active =1 and status_active =1 and barcode_no >0 and po_breakdown_id in (".implode(',', $po_no_arr).") ");
	foreach ($barcode_ref_sql as $val) 
	{
		$barcode_ref_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}

	if(empty($barcode_ref_arr))
	{
		echo "Barcode Not Found";
		die;
	}

	$all_barcode_nos = implode(",", $barcode_ref_arr);
	$all_barcode_no_cond=""; $barCond="";
	if($db_type==2 && count($barcode_ref_arr)>999)
	{
		$barcode_ref_arr_chunk=array_chunk($barcode_ref_arr,999) ;
		foreach($barcode_ref_arr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$barCond.=" barcode_no in($chunk_arr_value) or ";
		}

		$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
	}
	else
	{
		$all_barcode_no_cond=" and barcode_no in($all_barcode_nos)";
	}

	$splited_sql="select barcode_no from  pro_roll_details where roll_split_from > 0  and status_active = 1";

	$splited_data = sql_select($splited_sql);
	foreach ($splited_data as $row)
	{
		$splited_data_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
	}

	$barcode_data=sql_select("select id, entry_form, dtls_id, barcode_no from  pro_roll_details where entry_form in (22,58,82,83,84,110,180,183,61) and status_active =1 and status_active =1 and barcode_no >0 $all_barcode_no_cond order by barcode_no, id desc");


	foreach ($barcode_data as $row)
	{
		if($splited_data_arr[$row[csf("barcode_no")]] == "")
		{
			if($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 84 )
			{
				$rcv_n_iss_ret_dtls_arr[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
			}
			else if($row[csf("entry_form")] == 61)
			{
				$issue_dtls_arr[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
			}
			else if($row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183)
			{
				$trans_dtls_arr[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
			}
		}
	}

	//issue transaction ref
	$all_issue_dtls_ids = implode(",", $issue_dtls_arr);
	$all_issue_dtls_id_cond=""; $issueCond="";
	if($db_type==2 && count($issue_dtls_arr)>999)
	{
		$issue_dtls_arr_chunk=array_chunk($issue_dtls_arr,999) ;
		foreach($issue_dtls_arr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$issueCond.="  id in($chunk_arr_value) or ";
		}

		$all_issue_dtls_id_cond.=" and (".chop($issueCond,'or ').")";
	}
	else
	{
		$all_issue_dtls_id_cond=" and id in($all_issue_dtls_ids)";
	}

	$issue_dtls_sql = sql_select("select id, trans_id, store_name from inv_grey_fabric_issue_dtls  where  status_active =1 and is_deleted =0 $all_issue_dtls_id_cond");

	foreach ($issue_dtls_sql as $val)
	{
		$issue_dtls_trans_data[$val[csf("id")]]["trans_id"] 	= $val[csf("trans_id")];
		$issue_dtls_trans_data[$val[csf("id")]]["store_name"] 	= $val[csf("store_name")];
	}

	//rcv and issue return transaction ref
	$all_rcv_n_iss_ret_dtls_ids = implode(",", $rcv_n_iss_ret_dtls_arr);
	$all_rcv_n_iss_ret_dtls_id_cond=""; $rcvIssRetCond="";
	if($db_type==2 && count($rcv_n_iss_ret_dtls_arr)>999)
	{
		$rcv_n_iss_ret_dtls_arr_chunk=array_chunk($rcv_n_iss_ret_dtls_arr,999) ;
		foreach($rcv_n_iss_ret_dtls_arr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$rcvIssRetCond.=" a.id in($chunk_arr_value) or ";
		}

		$all_rcv_n_iss_ret_dtls_id_cond.=" and (".chop($rcvIssRetCond,'or ').")";
	}
	else
	{
		$all_rcv_n_iss_ret_dtls_id_cond=" and a.id in($all_rcv_n_iss_ret_dtls_ids)";
	}

	$rcv_n_iss_ret_sql = sql_select("select a.id, a.trans_id,b.mst_id, b.store_id from pro_grey_prod_entry_dtls a, inv_transaction b  where  a.trans_id = b.id and a.status_active =1 and a.is_deleted =0 $all_rcv_n_iss_ret_dtls_id_cond");

	foreach ($rcv_n_iss_ret_sql as $val)
	{
		$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["from_trans"] 	= $val[csf("trans_id")];
		$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["mst_id"] 	= $val[csf("mst_id")];
		$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["store_id"] 		= $val[csf("store_id")];
	}

	//transfer transaction ref
	$all_trans_dtls_ids = implode(",", $trans_dtls_arr);
	$all_trans_dtls_id_cond=""; $transCond="";
	if($db_type==2 && count($trans_dtls_arr)>999)
	{
		$trans_dtls_arr_chunk=array_chunk($trans_dtls_arr,999) ;
		foreach($trans_dtls_arr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$transCond.="  id in($chunk_arr_value) or ";
		}

		$all_trans_dtls_id_cond.=" and (".chop($transCond,'or ').")";
	}
	else
	{
		$all_trans_dtls_id_cond=" and id in($all_trans_dtls_ids)";
	}

	$transfer_sql_arr = sql_select("select id, trans_id, to_trans_id, from_store, to_store from inv_item_transfer_dtls  where  status_active =1 and is_deleted =0 and item_category = 13 $all_trans_dtls_id_cond");

	foreach ($transfer_sql_arr as $val)
	{
		$transfer_dtls_trans_arr[$val[csf("id")]]["from_trans"] 	= $val[csf("trans_id")];
		$transfer_dtls_trans_arr[$val[csf("id")]]["to_trans"] 		= $val[csf("to_trans_id")];

		$transfer_dtls_trans_arr[$val[csf("id")]]["from_store"] 	= $val[csf("from_store")];
		$transfer_dtls_trans_arr[$val[csf("id")]]["to_store"] 		= $val[csf("to_store")];
	}

	$entry_form_reference = array(58=>"Receive",82=>"Transfer",83=>"Transfer",84=>"Issue Return",110=>"Transfer",180=>"Transfer",183=>"Transfer",61=>"Issue");

	foreach ($barcode_data as $row)
	{
		if($splited_data_arr[$row[csf("barcode_no")]] == "")
		{
			if($barcode_no_chk[$row[csf("barcode_no")]] =="")
			{
				$barcode_no_chk[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
				$pre_store_id="";
			}

			if(($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183) && $source_rcv_arr[$row[csf("barcode_no")]] =="")
			{
				$source_rcv_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];

				$rID=execute_query("update pro_roll_details set re_transfer = 0 where id = ".$row[csf("id")], 0);
				if($rID == 0)
				{
					echo "Failed run script <br>";
					echo "update pro_roll_details set re_transfer = 0 where id = ".$row[csf("id")]."<br>";
					oci_rollback($con);
					disconnect($con);
					die;

				}
			}
			else if(($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183) && $source_rcv_arr[$row[csf("barcode_no")]] !="")
			{

				$rID1=execute_query("update pro_roll_details set re_transfer = 1 where id = ".$row[csf("id")], 0);
				if($rID1 ==0)
				{
					echo "Failed run script <br>";
					echo "update pro_roll_details set re_transfer = 1 where id = ".$row[csf("id")] ."<br>";
					oci_rollback($con);
					disconnect($con);
					die;
				}
			}

			if($row[csf("entry_form")] == 61)
			{
				if($pre_store_id == "")
				{
					$pre_store_id = $issue_dtls_trans_data[$row[csf("dtls_id")]]["store_name"];

					if($pre_store_id == 0 || $pre_store_id =="")
					{
						echo "Failed run script;<br>Store Not Found in ".$entry_form_reference[$row[csf("entry_form")]] ." Page.<br>Barcode No : ".$row[csf("barcode_no")];
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}

				$issue_trans_id = $issue_dtls_trans_data[$row[csf("dtls_id")]]["trans_id"];

				$rID2=execute_query("update inv_grey_fabric_issue_dtls set store_name ='$pre_store_id' where id =".$row[csf("dtls_id")],0);
				$rID3=execute_query("update inv_transaction set store_id = '$pre_store_id' where id = ".$issue_trans_id ,0);

				if($rID2 ==0 && $rID3 ==0)
				{
					echo "Failed run script <br>";
					echo "update inv_grey_fabric_issue_dtls set store_name = '$pre_store_id' where id = ".$row[csf("dtls_id")]."<br>";
					echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$issue_trans_id ."<br><br>";
					oci_rollback($con);
					disconnect($con);
					die;
				}
			}

			if($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 84)
			{
				if($pre_store_id == "")
				{
					$pre_store_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["store_id"];

					if($pre_store_id == 0 || $pre_store_id =="")
					{
						echo "Failed run script;<br>Store Not Found in ".$entry_form_reference[$row[csf("entry_form")]] ." Page.<br>Barcode No : ".$row[csf("barcode_no")];
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}
				$rcv_iss_ret_trans_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["from_trans"];
				$rcv_iss_ret_mst_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["mst_id"];

				if($row[csf("entry_form")] == 58)
				{
					$rID4=execute_query("update inv_receive_master set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_mst_id,0);
					if($rID4 ==0)
					{
						echo "Failed run script <br>";
						echo "update inv_receive_master set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_mst_id ."<br>";
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}

				$rID5=execute_query("update inv_transaction set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_trans_id ,0);
				if($rID5 ==0)
				{
					echo "Failed run script <br>";
					echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_trans_id."<br><br>";
					oci_rollback($con);
					disconnect($con);
					die;
				}

			}

			if($row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183)
			{
				if($pre_store_id == "")
				{
					$pre_store_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_store"];

					if($pre_store_id == 0 || $pre_store_id =="")
					{
						echo "Failed run script;<br>Store Not Found in ".$entry_form_reference[$row[csf("entry_form")]] ." Page.<br>Barcode No : ".$row[csf("barcode_no")];
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}

				$from_trans_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_trans"];
				$to_trans_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_trans"];
				$from_store_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_store"];

				$rID6=execute_query("update inv_item_transfer_dtls set from_store = '$from_store_id', to_store='$pre_store_id' where id = ".$row[csf("dtls_id")],0);
				if($rID6 ==0)
				{
					echo "Failed run script <br>";
					echo "update inv_item_transfer_dtls set from_store = '$from_store_id', to_store='$pre_store_id' where id = ".$row[csf("dtls_id")] ."<br>";

					oci_rollback($con);
					disconnect($con);
					die;
				}
				
				if($row[csf("entry_form")] != 82)
				{
					$rID7=execute_query("update inv_transaction set store_id = '$from_store_id' where id = ".$from_trans_id,0);
					$rID8=execute_query("update inv_transaction set store_id = '$pre_store_id' where id = ".$to_trans_id,0);

					if($rID7 ==0 && $rID8 ==0)
					{
						echo "Failed run script <br>";
						echo "update inv_transaction set store_id = '$from_store_id' where id = ".$from_trans_id."<br>";
						echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$to_trans_id;

						oci_rollback($con);
						disconnect($con);
						die;
					}
				}

				$pre_store_id = $from_store_id;
			}
		}
	}

	oci_commit($con);  
	echo "Data Synchronize is completed successfully";
	disconnect($con);
	die;
}

?>
