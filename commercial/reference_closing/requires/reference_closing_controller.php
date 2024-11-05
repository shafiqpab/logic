<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$sample_name_library=return_library_array( "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$item_arrs=return_library_array("select id,item_name from lib_garment_item where status_active=1 and is_deleted=0 order by item_name","id","item_name");
$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

function my_old($dob){
	$now = date('d-m-Y');
	$dob = explode('-', $dob);
	$now = explode('-', $now);
	$mnt = array(1 => 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if (($now[2]%400 == 0) or ($now[2]%4==0 and $now[2]%100!=0)) $mnt[2]=29;
	if($now[0] < $dob[0]){
		$now[0] += $mnt[$now[1]-1];
		$now[1]--;
	}
	if($now[1] < $dob[1]){
		$now[1] += 12;
		$now[2]--;
	}
	if($now[2] < $dob[2]) return false;
	return  array('year' => $now[2] - $dob[2], 'mnt' => $now[1] - $dob[1], 'day' => $now[0] - $dob[0]);
}

if($action=="load_drop_down_buyer")
{
    $data_ar = explode("_", $data);
    if($data_ar[0] == 163 && $data_ar[1] > 0){
        echo create_drop_down( "cbo_buyer", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data_ar[1] $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0);
    }else{
        echo create_drop_down( "cbo_buyer", 130, $blank_array,"", 1, "-- Select Buyer --", "","", 1);
    }
    exit;
}


if($action == 'job_search_popup') 
{
	$data_ar = explode("_", $data);
	//print_r($data_ar);
	if($data_ar[1]==163 || $data_ar[1]==2)
	{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $_SERVER['SERVER_NAME'];
	?>
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value( job_no )
	{
		//alert(job_no);
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table cellspacing="0" width="1020" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
			<thead>
				<tr>
					<th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
				</tr>
				<tr>                	 
					<th width="150" class="must_entry_caption">Company Name</th>
					<th width="130">Buyer Name</th>
					<th width="80">Job No</th>
					<th width="90">Style Ref </th>
					<th width="90">Internal Ref</th>
					<th width="90">File No</th>
					<th width="90">Order No</th>
					<th width="130" colspan="2">Ship Date Range</th>
					<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
				</tr>          
			</thead>
			<tr class="general">
				<td> 
				<input type="hidden" id="selected_job">
				<input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
					<?      
					echo create_drop_down('cbo_company_name', 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $data_ar[0], "", 1);
					?>
				</td>
				<td><? 
					echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data_ar[0]' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0);
				 ?>	</td>
				<td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
				<td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
				<td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:80px"></td>
				<td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:80px"></td>
				<td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
				<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
				<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td> 
				<td align="center">
				<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+<?=$data_ar[1]?>, 'create_po_search_list_view', 'search_div', 'reference_closing_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
			</tr>
			<tr class="general">
				<td align="center" valign="middle" colspan="10">
				<?=load_month_buttons(1);  ?>
				</td>
			</tr>
		</table>    
		<div id="search_div" align="center"></div>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
	}
}

if($action=="create_po_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	// print_r($data);//die();
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0){
		$buyer=" and a.buyer_name='$data[1]'"; 
	}
	else{
		$buyer="";
		$bu_arr=array();
		$pri_buyer=sql_select("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name");
		foreach($pri_buyer as $pri_buyer_row){
			$bu_arr[$pri_buyer_row[csf('id')]]=$pri_buyer_row[csf('id')];
		}
		$bu_arr_str=implode(",",$bu_arr);
		$buyer=" and a.buyer_name in ($bu_arr_str)";
	}//{ echo "Please Select Buyer First."; die; }
	
	$year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";

	$order_cond=""; $job_cond=""; $style_cond="";
	$style_data = strtolower($data[8]);

	if($data[6]==1)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'  $year_cond";
		if (trim($data[7])!="") $order_cond=" and b.po_number='$data[7]'  ";  
		if (trim($data[8])!="") $style_cond=" and lower(a.style_ref_no)='$style_data'"; 
	}
	else if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'  $year_cond"; 
		if (trim($data[7])!="") $order_cond=" and b.po_number like '%$data[7]%'  ";
		if (trim($data[8])!="") $style_cond=" and lower(a.style_ref_no) like '%$style_data%'  ";
	}
	else if($data[6]==2)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'  $year_cond";
		if (trim($data[7])!="") $order_cond=" and b.po_number like '$data[7]%'  ";
		if (trim($data[8])!="") $style_cond=" and lower(a.style_ref_no) like '$style_data%'  ";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'  $year_cond"; 
		if (trim($data[7])!="") $order_cond=" and b.po_number like '%$data[7]'  ";
		if (trim($data[8])!="") $style_cond=" and lower(a.style_ref_no) like '%$style_data'  ";
	}
			
	$internal_ref = str_replace("'","",$data[9]);
	$file_no = str_replace("'","",$data[10]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	if($db_type==0)
	{
		$date_diff_cond="DATEDIFF(pub_shipment_date,po_received_date)";
		$year_select_cond="SUBSTRING_INDEX(a.insert_date, '-', 1)";
	}
	else if($db_type==2)
	{
		$date_diff_cond="(pub_shipment_date - po_received_date)";
		$year_select_cond="to_char(a.insert_date,'YYYY')";
	}
	//if($data_level_secured)
	//echo $data_level_secured.'d';
	if($data_level_secured==1)//Limit Access user // ===Issue Id=135 (2022 yr)======
	{
	$sqlTeam=sql_select("select b.id from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and a.data_level_security=1 and a.user_tag_id='$user_id' and a.status_active =1 and a.is_deleted=0");
	//$mktTeamId="";
	foreach($sqlTeam as $row){
		$mktTeamIdArr[$row[csf('id')]]=$row[csf('id')];
	}
	$mktTeamId=implode(",",$mktTeamIdArr);
	$mktTeamAccess="";
	if(count($mktTeamIdArr)>0) $mktTeamAccess=" and a.team_leader in($mktTeamId)";//Dont hide Issue id ISD-20-31821
	}
	else //All Acces user 
	{
		$mktTeamAccess="";	
	}
	
	if($data[11]==163){
		$sql= "select a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, $date_diff_cond as date_diff, $year_select_cond as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $mktTeamAccess order by a.id DESC";
	}else{		
		$sql= "select a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, $date_diff_cond as date_diff, $year_select_cond as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $mktTeamAccess order by a.id DESC";
	}
	// echo $sql;

	$result=sql_select($sql);
	?>
	<div align="left" style=" margin-left:5px;margin-top:10px"> 
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" align="left" class="rpt_table" >
 			<thead>
 				<th width="30">SL</th>
                <th width="80">Company</th>
 				<th width="50">Year</th>
 				<th width="50">Job No</th>               
 				<th width="100">Buyer Name</th>
                <th width="100">Style Ref. No</th>
                <th width="80">Job Qty.</th>
                <th width="90">PO number</th> 
                <th width="80">PO Qty.</th>
 				<th width="65">Shipment Date</th>
 				<th width="70">Internal Ref</th>
 				<th width="70">File No</th>  
                <th width="85">Gmts Nature</th>             
 				<th>Lead time</th>               
 			</thead>
 		</table>
    	<div style="width:1020px; max-height:270px; overflow-y:scroll" id="container_batch" >	 
 			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="list_view">  
 				<?
 				$i=1;
 				foreach ($result as $row)
 				{  
 					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
 					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('job_no')];?>')"> 
                        <td width="30" align="center"><? echo $i; ?>  </td> 
                        <td width="80" style="word-break:break-all"><? echo $comp[$row[csf('company_name')]]; ?></p></td> 
                        <td width="50" style="word-break:break-all" align="center"><? echo $row[csf('year')]; ?></p></td>
                        <td width="50" style="word-break:break-all" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                        <td width="100" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                        <td width="100" style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="80" style="word-break:break-all" align="right"><? echo $row[csf('job_quantity')]; ?></p></td>
                        <td width="90" style="word-break:break-all"><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="80" style="word-break:break-all" align="right"><? echo $row[csf('po_quantity')]; ?></p></td>
                        <td width="65" style="word-break:break-all"><? echo change_date_format($row[csf('shipment_date')]); ?></p></td>
                        <td width="70" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></p></td>
                        <td width="70" style="word-break:break-all"><? echo $row[csf('file_no')]; ?></p></td>
                        <td width="85" style="word-break:break-all"><? echo $item_category[$row[csf('garments_nature')]]; ?></p></td>
                        <td style="word-break:break-all" align="center"><? echo $row[csf('date_diff')]; ?></p></td>
                    </tr> 
                    <? 
                    $i++;
 				}
 				?> 
 			</table>        
 		</div>
 	</div>
    <?php
	exit();
} 

if($action=="save_update_delete")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$user_id = $_SESSION['logic_erp']['user_id'];
	$unclose_id=str_replace("'","",$unclose_id);
	
		//die();
	
		if($only_full==false)
		{
			//$closing_status=1;
		}
		// $closing_status=$unclose_id;
	//	echo "10**".$unclose_id.'=='.$only_full;die;
	if ($operation==0)
	{
		/*		if (is_duplicate_field( "inv_pur_req_id","inv_reference_closing","inv_pur_req_id=$txt_subsection" )==1)
		{
			echo "11**0"; die;
		}*/
		$con = connect();

		if($db_type==0)
		{

			mysql_query("BEGIN");
		}
		$type=str_replace("'","",$cbo_ref_type);
		
		if($type==69 || $type==70)
		{
		  $db_table='inv_purchase_requisition_mst';
		}
		elseif($type==4)
		{
		  $db_table='inv_receive_master';
		}
		elseif($type==94)
		{
			$db_table='wo_yarn_dyeing_mst';
		}
		elseif($type==144)
		{
			$db_table='wo_non_order_info_mst';
		}
		elseif($type==140)
		{
			$db_table='wo_non_ord_samp_booking_mst';
		}
		elseif($type==117)
		{
			$db_table='sample_development_dtls';
		}
		elseif($type==104)
		{
			$db_table='com_pi_master_details';

		}
		elseif($type==105)
		{
			$db_table='com_btb_lc_master_details';
		}
		elseif($type==106)
		{
			$db_table='com_export_lc';
		}
		elseif($type==2)//Knit Closing
		{
			$db_table='inv_receive_master';
		}
		elseif($type==108)//Booking Closing
		{
			$db_table='wo_booking_mst';
		}
		elseif($type==163)
		{
			$db_table_1='wo_po_break_down';
			$db_table_2='wo_po_color_size_breakdown';
			$db_table_3='pro_ex_factory_mst';
		}
		elseif($type==370)//Sweater
		{
			$db_table_1='wo_po_break_down';
			$db_table_2='wo_po_color_size_breakdown';
			$db_table_3='pro_garments_production_mst';
		}
		/*elseif($type==107)
		{
			$db_table='sample_development_dtls';
		}*/
		else
		{
			$db_table='wo_non_order_info_mst';
			//$db_table_rcv_trns='inv_receive_master';
		}
		//txt_subsection*cbo_section*cbo_status*txt_remark*update_id
		$id=return_next_id( "id", "inv_reference_closing", 1 ) ;//closing_status
		$field_array="id,company_id,closing_date,reference_type,closing_status,inv_pur_req_mst_id,mrr_system_no,inserted_by,insert_date";

		if($type==4)
		{
			$field_array_update="ref_closing_status*updated_by*update_date";
		}
		elseif($type==117)
		{
			$field_array_update="is_complete_prod";
		}
		elseif($type==163 || $type==370)
		{
			$field_array_update="shiping_status*updated_by*update_date";
			$field_array_update3="shiping_status*updated_by*update_date";
			$field_array_update4="ref_closing_status*updated_by*update_date";
		}
		else
		{
			$field_array_update="ref_closing_status";
		}
		//print_r($total_id); die;
		$totid=str_replace("'","",$total_id);
		$all_id= explode("***",$totid);
	//	echo "10**";print_r($all_id); die;
		$totids="";$type_ids="";
		$all_id_arr_1=array();
		$all_id_arr_2=array();$all_id_type_arr=array();
		foreach($all_id as $all_ids)
		{
			list($ids,$mrr_no,$type_id)= explode('**', $all_ids);
			$all_id_arr_1[] =$ids;
			$all_id_arr_2[] =$mrr_no;
			$all_id_type_arr[$ids] =$type_id;
			$totids.=$ids.',';
			$mstIDtotids.=$mrr_no.',';
			$type_ids.=$type_id.',';
		}
		$totidss=chop($totids,",");
		$MIdtotids=chop($mstIDtotids,",");
		$type_IDs=chop($type_ids,",");
		//print_r($all_id_arr_3);
		//echo "10**A";die;
		//echo $all_id_arr[1];
		
		
		//print_r($booking_type_arr);
	//echo "10**=".$totidss.'='.$MIdtotids.'='.$type_idArr; die;


		$data_array="";
		for($j=0;$j<count($all_id_arr_1);$j++)
  		{
			//$data_array.="(".$id.",".$cbo_company_name.",".$txt_ref_cls_date.",".$cbo_ref_type.",'".$all_id_arr_1[$j]."','".$all_id_arr_2[$j]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


			if($data_array==''){
				$data_array.="(".$id.",".$cbo_company_name.",".$txt_ref_cls_date.",".$cbo_ref_type.",".$unclose_id.",'".$all_id_arr_1[$j]."','".$all_id_arr_2[$j]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			else
			{
				$data_array.=",(".$id.",".$cbo_company_name.",".$txt_ref_cls_date.",".$cbo_ref_type.",".$unclose_id.",'".$all_id_arr_1[$j]."','".$all_id_arr_2[$j]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}



			if($type==163)
			{
				$data_array_update="".'3'."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$data_array_update3="".'3'."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			else if($type==370)
			{
				$data_array_update="".'3'."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$data_array_update3="".'3'."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$data_array_update4="".'1'."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			elseif($type==4)
			{
				$data_array_update="".'1'."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			}
			else
			{
				$data_array_update="".'1'."";
			}
			$id=$id+1;
		}
		//print_r($data_array_update);die;
		
		//echo "10**insert into inv_reference_closing (".$field_array.") values".$data_array; die;
		$rID=sql_insert("inv_reference_closing", $field_array, $data_array, 1);
		if($rID)
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}
		//echo "10**".$flag.'='.$type.'='.$only_full; die;
			 
		if($flag)
		{
			if($type==163)
			{
				$poidarr=explode(",",$totidss);
				$po_id_cond=where_con_using_array($poidarr,0,'id');
				//$job_id_condA=where_con_using_array($job_idArr,0,'id');
				$prev_data=sql_select("SELECT id as po_id, is_confirmed ,po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, doc_sheet_qty, no_of_carton, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, matrix_type, round_type, tna_task_from_upto, t_year, t_month, file_no, sc_lc, pack_handover_date,is_deleted, status_active, updated_by, update_date, po_number_prev, pub_shipment_date_prev, file_year FROM wo_po_break_down WHERE status_active=1 and is_deleted=0 $po_id_cond");
				foreach($prev_data as $rows)
				{
					$prev_po_no[$rows[csf('po_id')]]=$rows[csf('po_number')];
					$prev_matrix_type[$rows[csf('po_id')]]=$rows[csf('matrix_type')];
					$prev_round_type[$rows[csf('po_id')]]=$rows[csf('round_type')];
					$prev_doc_sheet_qty[$rows[csf('po_id')]]=$rows[csf('doc_sheet_qty')];
					$prev_no_of_carton[$rows[csf('po_id')]]=$rows[csf('no_of_carton')];
					$prev_order_status[$rows[csf('po_id')]]=$rows[csf('is_confirmed')];
					$prev_po_received_date[$rows[csf('po_id')]]=$rows[csf('po_received_date')];
					$prev_po_qty[$rows[csf('po_id')]]=$rows[csf('po_quantity')];
					$prev_pub_shipment_date[$rows[csf('po_id')]]=$rows[csf('pub_shipment_date')];
					$prev_status[$rows[csf('po_id')]]=$rows[csf('status_active')];
					$prev_org_shipment_date[$rows[csf('po_id')]]=$rows[csf('shipment_date')];
					$prev_factory_rec_date[$rows[csf('po_id')]]=$rows[csf('factory_received_date')];
					$prev_projected_po[$rows[csf('po_id')]]=$rows[csf('projected_po_id')];
					$prev_packing[$rows[csf('po_id')]]=$rows[csf('packing')];
					$prev_grouping[$rows[csf('po_id')]]=$rows[csf('grouping')];
					$prev_details_remark[$rows[csf('po_id')]]=$rows[csf('details_remarks')];
					$prev_file_no[$rows[csf('po_id')]]=$rows[csf('file_no')];
					$prev_avg_price[$rows[csf('po_id')]]=$rows[csf('unit_price')];
					$prev_sc_lc[$rows[csf('po_id')]]=$rows[csf('sc_lc')];
					$prev_phd_date[$rows[csf('po_id')]]=$rows[csf('pack_handover_date')];
					$prev_excess_cut[$rows[csf('po_id')]]=$rows[csf('excess_cut')];
					$prev_plan_cut[$rows[csf('po_id')]]=$rows[csf('plan_cut')];
					$prev_status[$rows[csf('po_id')]]=$rows[csf('status_active')];
					$prev_updated_by[$rows[csf('po_id')]]=$rows[csf('updated_by')];			
					$prev_update_date[$rows[csf('po_id')]]=$rows[csf('update_date')];
					$prev_pono[$rows[csf('po_id')]]=$rows[csf('po_number_prev')];
					$prev_pubship_date[$rows[csf('po_id')]]=$rows[csf('pub_shipment_date_prev')];
					$prev_file_year[$rows[csf('po_id')]]=$rows[csf('file_year')];
				}
				if($only_full=='true')
				{
					$ex_fac_sql="SELECT po_break_down_id  from pro_ex_factory_mst where status_active=1 and po_break_down_id in ($totidss) ";
					$ex_fac_arr=array();
					foreach(sql_select($ex_fac_sql) as $v )
					{
						$ex_fac_arr[$v[csf("po_break_down_id")]]=$v[csf("po_break_down_id")];
					}
					$all_po_arr=explode(",", $totidss);
					//echo "10**".$totidss;print_r($ex_fac_arr);die;
					foreach($all_po_arr as $key)
					{
						
						$po_in_exfac=$ex_fac_arr[$key];
						$sp_status=1;
						if($po_in_exfac)
							$sp_status=2;

						$rID_up_1=execute_query("UPDATE wo_po_break_down SET shiping_status='$sp_status', updated_by='$user_id',update_date='$pc_date_time' where id=$key",1);
						$rID_up_2=execute_query("UPDATE wo_po_color_size_breakdown SET shiping_status='$sp_status', updated_by='$user_id',update_date='$pc_date_time' where po_break_down_id=$key",1);
						$rID_up_3=execute_query("UPDATE pro_ex_factory_mst SET shiping_status='$sp_status', updated_by='$user_id',update_date='$pc_date_time' where po_break_down_id=$key",1);
					}

				}
				else
				{
					$rID_up_1=sql_multirow_update($db_table_1, $field_array_update, $data_array_update,"id",$totidss,1);
					$rID_up_2=sql_multirow_update($db_table_2, $field_array_update, $data_array_update3,"po_break_down_id",$totidss,1);
					$rID_up_3=sql_multirow_update($db_table_3, $field_array_update3, $data_array_update3,"po_break_down_id",$totidss,1);
				}

				if(count($poidarr)>0){				
					$log_id_mst=return_next_id( "id", "wo_po_update_log", 1);
	
					if($db_type==0) $current_date = $pc_date_time;
					else $current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
					$curr_date=date("Y-m-d", strtotime($current_date));
	
					$field_array_history="id, entry_form, matrix_type, round_type, job_no, po_no, po_id, order_status, po_received_date, previous_po_qty, shipment_date, org_ship_date, po_status, t_year, t_month, fac_receive_date, projected_po, packing, remarks, file_no, sc_lc, phd_date, doc_sheet_qty, avg_price, no_of_carton, excess_cut_parcent, plan_cut, status, update_date, update_by";
					$field_array_update="po_no*po_id*matrix_type*round_type*order_status*po_received_date*previous_po_qty*shipment_date*org_ship_date*po_status*fac_receive_date*projected_po*packing*remarks*file_no*sc_lc*phd_date*avg_price*doc_sheet_qty*no_of_carton*excess_cut_parcent*plan_cut*status*update_date*update_by";
	
					foreach($poidarr as $poid){
						$log_update_date=return_field_value("update_date","wo_po_update_log","job_no='".$job_no."' and po_id=".$poid." order by id DESC");
						$log_update='';
						if($log_update_date!=''){
							$log_update=date("Y-m-d", strtotime($log_update_date));
						}		
						
						$flag=1;
						if($log_update=="" || $log_update!=$curr_date)
						{
							$flag=0;
							$data_array_history="(".$log_id_mst.",1,'".$prev_matrix_type[$poid]."','".$prev_round_type[$poid]."','".$job_no."','".$prev_po_no[$poid]."',".$poid.",'".$prev_order_status[$poid]."','".$prev_po_received_date[$poid]."','".$prev_po_qty[$poid]."','".$prev_pub_shipment_date[$poid]."','".$prev_org_shipment_date[$poid]."','".$prev_status[$poid]."','".date("Y",strtotime(str_replace("'","",$prev_org_shipment_date[$poid])))."','".date("m",strtotime(str_replace("'","",$prev_org_shipment_date[$poid])))."','".$prev_factory_rec_date[$poid]."','".$prev_projected_po[$poid]."','".$prev_packing[$poid]."','".$prev_details_remark[$poid]."','".$prev_file_no[$poid]."','".$prev_sc_lc[$poid]."','".$prev_phd_date[$poid]."','".$prev_doc_sheet_qty[$poid]."','".$prev_avg_price[$poid]."','".$prev_no_of_carton[$poid]."','".$prev_excess_cut[$poid]."','".$prev_plan_cut[$poid]."','".$prev_status[$poid]."','".$prev_update_date[$poid]."','".$prev_updated_by[$poid]."')";
							//echo "10**insert into wo_po_update_log ($field_array_history) values $data_array_history"; die;
							$rID3=sql_insert("wo_po_update_log",$field_array_history,$data_array_history,1);
							if($rID3) $flag=1; else $flag=0;
							$log_id_mst++;
						}
						else if($log_update==$curr_date)
						{
							$flag=0;
							$data_array_update="'".$prev_po_no[$poid]."'*".$poid."*'".$prev_matrix_type[$poid]."'*'".$prev_round_type[$poid]."'*'".$prev_order_status[$poid]."'*'".$prev_po_received_date[$poid]."'*'".$prev_po_qty[$poid]."'*'".$prev_pub_shipment_date[$poid]."'*'".$prev_org_shipment_date[$poid]."'*'".$prev_status[$poid]."'*'".$prev_factory_rec_date[$poid]."'*'".$prev_projected_po[$poid]."'*'".$prev_packing[$poid]."'*'".$prev_details_remark[$poid]."'*'".$prev_file_no[$poid]."'*'".$prev_sc_lc[$poid]."'*'".$prev_phd_date[$poid]."'*'".$prev_avg_price[$poid]."'*'".$prev_doc_sheet_qty[$poid]."'*'".$prev_no_of_carton[$poid]."'*'".$prev_excess_cut[$poid]."'*'".$prev_plan_cut[$poid]."'*'".$prev_order_status[$poid]."'*'".$current_date."'*".$_SESSION['logic_erp']['user_id']."";
							$rID3=sql_update("wo_po_update_log",$field_array_update,$data_array_update,"po_id*update_date","".$poid."*'".$log_update_date."'",1);
							if($rID3) $flag=1; else $flag=0;
						}
					}				
	
				}

					
			}
			else if($type==370)//Sweater
			{
				if($only_full=='true')
				{
					$qc_sql="SELECT b.order_id  from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b where a.id=b.mst_id and a.status_active=1 and b.order_id in ($totidss) and a.garments_nature=100";
					$qc_kint_arr=array();
					foreach(sql_select($qc_sql) as $v )
					{
						$qc_kint_arr[$v[csf("order_id")]]=$v[csf("order_id")];
					}
					$all_po_arr=explode(",", $totidss);
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;print_r($qc_kint_arr).'='.$qc_sql;die;
					foreach($all_po_arr as $key)
					{
						
						$po_in_qc_knit=$qc_kint_arr[$key];
						$sp_status=1;
						if($po_in_qc_knit)
							$sp_status=2;

						$rID_up_1=execute_query("UPDATE wo_po_break_down SET shiping_status='$sp_status', updated_by='$user_id',update_date='$pc_date_time' where id=$key",1);
						$rID_up_2=execute_query("UPDATE wo_po_color_size_breakdown SET shiping_status='$sp_status', updated_by='$user_id',update_date='$pc_date_time' where po_break_down_id=$key",1);
						$rID_up_3=execute_query("UPDATE pro_garments_production_mst SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where po_break_down_id=$key and production_type=52",1);
						$rID_up_3=execute_query("UPDATE pro_gmts_cutting_qc_dtls SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where order_id=$key",1);
					}

				}
				else
				{
					$rID_up_1=sql_multirow_update($db_table_1, $field_array_update, $data_array_update,"id",$totidss,1);
					$rID_up_2=sql_multirow_update($db_table_2, $field_array_update, $data_array_update3,"po_break_down_id",$totidss,1);
				//	$rID_up_3=sql_multirow_update($db_table_3, $field_array_update4, $data_array_update4,"po_break_down_id",$totidss,1);
					$rID_up_3=execute_query("UPDATE pro_garments_production_mst SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where po_break_down_id in($totidss) and production_type=52",1);
					$rID_up_3=execute_query("UPDATE pro_gmts_cutting_qc_dtls SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where order_id in($totidss)",1);
				}

					
			}
			else if($type==104)//Com PI com_pi_master_details
			{
				if($only_full=='true')
				{
					
					$all_po_arr=explode(",", $totidss);
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;die;
					foreach($all_po_arr as $key)
					{
						$rID_up=execute_query("UPDATE com_pi_master_details SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key",1);
					}

				}
				else
				{
					$rID_up=execute_query("UPDATE com_pi_master_details SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($totidss)",1);
				}
			}
			else if($type==105)//BTB LC
			{
				if($only_full=='true')
				{
					
					$all_po_arr=explode(",", $totidss);
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;die;
					foreach($all_po_arr as $key)
					{
						$rID_up=execute_query("UPDATE com_btb_lc_master_details SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key",1);
					}

				}
				else
				{
				
					$rID_up=execute_query("UPDATE com_btb_lc_master_details SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($totidss)",1);
				}
					
			}
			else if($type==106)//Export LC
			{
				if($only_full=='true')
				{
					
					$all_po_arr=array_unique(explode(",", $totidss));
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;die;
					foreach($all_po_arr as $key)
					{
						$rID_up=execute_query("UPDATE com_export_lc SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key",1);
					}

				}
				else
				{
				
					$rID_up=execute_query("UPDATE com_export_lc SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($totidss)",1);
				}

					
			}
			else if($type==2)//Kniting Closing...
			{
				if($only_full=='true')
				{
					
					$all_mst_arr=array_unique(explode(",", $MIdtotids));
					$all_progNo_arr=array_unique(explode(",", $totidss));
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;die;
					foreach($all_mst_arr as $key)
					{
						$rID_up=execute_query("UPDATE inv_receive_master SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key and entry_form=2",1);
					}

					if($fso_wise_chk_active=='true')
					{
						foreach($all_progNo_arr as $prog_key)
						{
							$rID_up=execute_query("UPDATE ppl_planning_info_entry_dtls SET ref_closing_status='0',status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$prog_key",1);
						}
					}
					else
					{
						foreach($all_progNo_arr as $prog_key)
						{
							$rID_up=execute_query("UPDATE ppl_planning_info_entry_dtls SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$prog_key",1);
						}
					}

				}
				else
				{
				
					
					$rID_up=execute_query("UPDATE inv_receive_master SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($MIdtotids) and entry_form=2",1);

					if($fso_wise_chk_active=='true')
					{
						$rID_up=execute_query("UPDATE ppl_planning_info_entry_dtls SET ref_closing_status='1',status='4', updated_by='$user_id',update_date='$pc_date_time' where id in($totidss)",1);
					}
					else
					{
						$rID_up=execute_query("UPDATE ppl_planning_info_entry_dtls SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($totidss)",1);
						//echo $rID_up."10**UPDATE inv_receive_master SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($MIdtotids)";die;
					}
				}
					
			}
			else if($type==108)//Fabric Booking Closing...
			{
				//echo "10**".$totidss.'=='.$MIdtotids;die;
				$all_booking_no_arr=array_unique(explode(",", $MIdtotids));
				$all_mst_id_arr=array_unique(explode(",", $totidss));
				if($only_full=='true')
				{
					
					//$all_booking_no_arr=array_unique(explode(",", $MIdtotids));
					//$all_mst_id_arr=array_unique(explode(",", $totidss));
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;die;
					$typeId=0;
					foreach($all_mst_id_arr as $key)
					{
						$typeId=$all_id_type_arr[$key];
						//echo "10**UPDATE wo_booking_mst SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key and booking_type=1";die;
						if($typeId==1)
						{
						$rID_up=execute_query("UPDATE wo_booking_mst SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key and booking_type=$typeId",1);
						}
						else
						{
						$rID_up=execute_query("UPDATE wo_non_ord_samp_booking_mst SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key and booking_type=$typeId",1);
						}
					}
					

				}
				else
				{
				
					$typeId=0;
					foreach($all_mst_id_arr as $key)
					{
						$typeId=$all_id_type_arr[$key];
						if($typeId==1)
						{
						$rID_up=execute_query("UPDATE wo_booking_mst SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($key) and booking_type=$typeId",1);
						}
						else
						{
							$rID_up=execute_query("UPDATE wo_non_ord_samp_booking_mst SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($key) and booking_type=$typeId",1);
						}
					}
					//$rID_up=execute_query("UPDATE ppl_planning_info_entry_dtls SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($totidss)",1);
					//echo $rID_up."10**UPDATE inv_receive_master SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($MIdtotids)";die;
				}

					
			}
			else if($type==94)
			{
				if($only_full=='true')
				{
					
					$all_po_arr=explode(",", $totidss);
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;die;
					foreach($all_po_arr as $key)
					{
						$rID_up=execute_query("UPDATE wo_yarn_dyeing_mst SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key",1);
					}
				}
				else
				{
				
					$rID_up=execute_query("UPDATE wo_yarn_dyeing_mst SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($totidss)",1);
				}

					
			}
			else if($type==144)
			{
				if($only_full=='true')
				{
					
					$all_po_arr=explode(",", $totidss);
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;die;
					foreach($all_po_arr as $key)
					{
						$rID_up=execute_query("UPDATE wo_non_order_info_mst SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key",1);
					}
				}
				else
				{
				
					$rID_up=execute_query("UPDATE wo_non_order_info_mst SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($totidss)",1);
				}		
			}
			else if($type==140)
			{
				if($only_full=='true')
				{
					
					$all_po_arr=explode(",", $totidss);
					//$all_po_ids=implode(",",array_unique(explode(",", $totidss)));
					//echo "10**".$totidss;die;
					foreach($all_po_arr as $key)
					{
						$rID_up=execute_query("UPDATE wo_non_ord_samp_booking_mst SET ref_closing_status='0', updated_by='$user_id',update_date='$pc_date_time' where id=$key",1);
					}
				}
				else
				{
				
					$rID_up=execute_query("UPDATE wo_non_ord_samp_booking_mst SET ref_closing_status='1', updated_by='$user_id',update_date='$pc_date_time' where id in($totidss)",1);
				}		
			}
			else
			{
				$rID_up=sql_multirow_update($db_table, $field_array_update, $data_array_update,"id",$totidss,1);
			}
		}
		//echo $db_table." ".$field_array_update.' '.$data_array_update.' '.$totid.'dd'.$rID_up;die;
		//function sql_multirow_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
		//die;

		//echo "10** $rID && $rID_up";oci._rollback($con);die;
		//echo $db_table." ".$field_array_update.' '.$data_array_update.' '.$totid.'dd'.$rID_up;die;
		//function sql_multirow_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
		//die;

		if($type==163 || $type==370)
		{

			if($db_type==0)
			{
				if($rID && $rID_up_1 && $rID_up_2)
				{
					mysql_query("COMMIT");
					echo "0**".$type;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$type;
				}
			}

			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID_up_1 && $rID_up_2)
				{
					oci_commit($con);
					echo "0**".$type;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$type;
				}
			}

		}
		else
		{
			if($db_type==0)
			{
				if($rID && $rID_up)
				{
					mysql_query("COMMIT");
					echo "0**".$type;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$type;
				}
			}

			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID_up)
				{
					oci_commit($con);
					echo "0**".$type;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$type;
				}
			}
		}
	disconnect($con);
	die;
	}

	elseif ($operation==1)//Update Here but not used
	{
		if (is_duplicate_field( "inv_pur_req_id","inv_reference_closing","inv_pur_req_id=$txt_subsection and id!=$update_id and is_deleted=0") == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$field_array="subsection_name*section_id*status_active*remark*updated_by*update_date";
			$data_array="".$txt_subsection."*".$cbo_section."*".$cbo_status."*".$txt_remark."*".$_SESSION['logic_erp']['user_id']."*".$insert_update_date_time."";
			//echo "update lib_subsection set(".$field_array.")=".$data_array[0]; die;
			$rID=sql_update("lib_subsection", $field_array, $data_array,"id",$update_id,1);
			//echo $rID; die;
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "1**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID)
				{
					oci_commit($con);
					echo "1**".$rID;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==2)   // Delete Here
	{

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*".$insert_update_date_time."*0*1";

		$rID=sql_delete("lib_subsection",$field_array,$data_array,"id",$update_id,1);
		//$rID=sql_delete("tbl_department_test",$field_array,$data_array,"id","$update_id",0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
	          if($rID )
			    {
					oci_commit($con);
					echo "2**".$rID;
				}
			else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		}
		disconnect($con);
		die;
	}
}

if($action=="show_details")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=str_replace("'","",$only_full);
	$job_no_wo=str_replace("'","",$txt_job_no);
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	$merchant_name_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
	if($only_full=='true') $unclose_id=1;else $unclose_id=0;
	if($job_no_wo!='') $job_wo_cond=" and a.job_no_mst = '$job_no_wo' ";else $job_wo_cond="";

	//echo $only_full.'dd'.$unclose_id;
	ob_start();
	$contents='';
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/
		
		
		function toggle( x, origColor ) {
			//alert(x+'_'+origColor)
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
//alert(tbl_row_count);
			
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}
		}
		//this function for only ID
		var selected_id = new Array;var selected_name = new Array;
		function js_set_value(str)
		{
			//alert(str);
			if($("#search"+str).css("display") !='none')
			{
				var select_row=0; var sp=1;
				var select_row= str;
				var tbl_length =$('#tbl_list tbody tr').length-1;
				var select_str=$('#txt_individual_id' + str).val();
			//	alert($('#txt_individual_id' + str).val()+"="+selected_id);
				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) 
				{
					
						
					if($('#tbl_list_head thead tr #all_chk').is(':checked'))
					{
						$( "#chk_id_"+str ).prop( "checked", true );
						//alert(1);
						
					}
					else
					{
						if($('#tbl_list_head thead tr #all_chk').is(':checked'))
						{
							$( "#chk_id_"+str ).prop( "checked", true );
						}
					}		
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push($('#chk_id_' + str).val());
					//alert(1+'='+selected_id);
				}
				else
				{
					$( "#chk_id_"+str ).prop( "checked", false );
					//alert(selected_id.length);
					for( var i = 0; i < selected_id.length; i++ ) {
						//alert(selected_id[i]+'=='+$('#txt_individual_id' + exrow[m]).val());
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
				
				var id = ''; 	var po_id = ''; 
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + '***';
					po_id += selected_name[i] + '***';
				//	alert(id);
					
				}
				id = id.substr( 0, id.length - 1 );
				po_id = po_id.substr( 0, po_id.length - 1 );
				//$('#total_id').val( id );
				$('#total_id').val( po_id );
				
			}
		}



	
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=163)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
       
       <table width="1800" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
			<thead>
				<tr>
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<th colspan="20" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
				</tr>
				<tr>
					<th width="25" valign="middle"> <input style="margin-left:1px;margin-top: 10px;" type="checkbox" id="all_chk" onClick="check_all_data();"/>
                     &nbsp;
                     <?
					 //if($type==163)
					// {
					if($only_full=='true')
						{
							$unclose_id=0;
						}
						else $unclose_id=1;
					 //}
					 ?>
               		 <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $unclose_id;?>"/>
                    </th>
                    <?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<th width="30">SL</th>
					<th width="<? if($type==163) echo "80"; else echo "110";?>">
					<? 
					if($type==69 || $type==70 || $type==117){ echo 'Requisition No';}
					elseif($type==4){echo 'Receive Number';}
					elseif($type==104){ echo 'PI No';}
					elseif($type==105){ echo 'BTB LC No';}
					elseif($type==106){ echo 'Export LC No';}
					elseif($type==163){ echo 'Buyer';}
					else{ echo 'Purchase Order No';} 
					?>
                    </th>
					<th width="<? if($type==117 || $type==163){echo "110" ;} else {echo "65";}?>">
					<? 
					if($type==69 || $type==70 || $type==117){ echo 'Requisition Date';}
					elseif($type==4){echo 'Receive Date';}
					elseif($type==104){ echo 'PI Date';}
					elseif($type==105){ echo 'BTB LC Date';}
					elseif($type==106){ echo 'Export LC Date';}
					elseif($type==163){ echo 'Job Number';}
					else{ echo 'Purchase Order Date';} ?>
                    </th>

					<?php
                    if($type == 163)
                    {
                    	?>
                        <th width="100">Style Ref</th>
                        <?php
						
                    }
					?>

					
                    <?php
                    if($type == 163)
                    {
                    	?>
                        <th width="80">Internal Ref</th>
                        <?php
						
                    }
					?>
					<th width="<? if($type==117){echo "100" ;} else {echo "120";}?>">
					<? 
					if($type==105 ){ echo 'Supplier';}
					elseif($type==106 ){ echo 'Buyer';}
					else if($type==163){ echo "Po No";}
					elseif($type==117){echo 'Sample Name';}
					else{echo 'Source';} ?>
					</th>
					<th width="<? if($type==163){echo "80";}else{echo "110";} ?>">
					<?
					if($type==117){ echo "Item Name";} 
					elseif($type==163){ echo "Shipment Date";} 
					elseif($type==4 ){ echo 'Supplier';}
					else { echo "Age";} 
					?>
					</th>
					<?
                    if($type == 163){
                    ?>
                        <th width="80">Max. Shipdate Act. PO</th>
                    <?
                    }
					?>
					<?
                    if($type == 163){
                    ?>
                        <th width="80">Order Status</th>
                    <?
                    }
					?>
                    <th width="<? if($type==163){echo "80";}else{echo "90";} ?>">
					<? 
					if($type==104 || $type==105){ echo 'Item Category';}
					elseif($type==106){ echo 'Tolerance %';}
					elseif($type==117){ echo 'Color';}
					elseif($type==163){ echo 'Po Qty';}
					else{ echo 'Pay Mode';} 
					?>
                    </th>

                    <?
                    if($type!=163)
                    {
                    	?>
						<th width="<? if($type==117){echo "100" ;} else {echo "80";}?>">
						<? 
						if($type==102 || $type==103){ echo 'PO Value';}
						elseif($type==104){ echo 'PI Value';}
						elseif($type==117){ echo 'Sample Req Qty';}
						elseif($type==105 || $type==106){ echo 'LC Value';}
						elseif($type==4){ echo 'Receive Qty';}
						else { echo 'PO Qty';} 
						?>
	                    </th>
	                    <?
	                    if($type==104)
						{ 
							echo '<th width="80">LC Value</th>';
							echo '<th width="80">Acceptance Value</th>';
						}
					    ?>
						<th width="<? if($type==117){echo "120" ;} else {echo "80";}?>">
						<? 
						if($type==102 || $type==103){ echo 'PI / Receive Value';}
						elseif($type==104 || $type==105){ echo 'Receive Value';}
						elseif($type==106){ echo 'Invoice Value';}
						elseif($type==117){ echo 'Delv Start Date';}
						elseif($type==4){ echo 'Item Category';}
						else { echo 'PI / Receive Qty';} 
						?>
	                    </th>
	                    <?
	                    if($type==105 || $type==106){ echo '<th width="65">LC Exp. Date</th>';}
						elseif($type==4){ echo '<th width="65">Loan Party</th>';}
						if($type==70 || $type==100 || $type==101 || $type==102 || $type==103 || $type==104 || $type==105){ $th_width='width="100"';} 
						elseif($type==4){ $th_width='width="80"';}
						
						if($type==105){ 
							echo '<th width="100">Receive Rtn Value</th>';
							echo '<th width="100">Actual Rcv Value</th>';
						}
						?>
						<th <? echo $th_width;?>>
						<?
                        if($type==102 || $type==103 || $type==104 || $type==105 || $type==106){ echo 'Balance Value';}
                        elseif($type==117){ echo 'Delv End Date';}
						elseif($type==4){ echo 'Receive Basis';}
						else { echo 'Balance Qty';} 
						?>
						</th>
	                   	 <?
	                    if($type==106){ echo '<th width="100">Value %</th>';}
					}
					else
					{
						?>
						<th width="60">Ship Qty</th>
						<th width="110">Ship Bal. Qty</th>
						<th width="110">Shipping Status</th>
						<th width="100">Team Leader</th>
						<th width="100">Dealing Merchandiser</th>
						<th width="100">Factory Merchandiser</th>
						<?
					}
				   	?>
				</tr>
			</thead>
            </table>

            <div style="overflow:scroll; height:350px; width:1800px">
            <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" style="width:1800px;" >
            <tbody>
            	<input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
				<?
				
				$i=1;
				if($type==4) //Dyes and Chemicals Receive
				{
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";


					$rec_sql = "select a.id, a.recv_number, a.receive_date, a.receive_basis, a.receive_purpose, a.item_category, a.buyer_id, a.lc_no, a.loan_party , a.supplier_id,a.pay_mode, a.source, sum(b.cons_quantity) as rcv_qnty
					from inv_receive_master a, inv_transaction b
					where a.id=b.mst_id  and a.item_category in(5,6,7,22,23) and a.entry_form=4 and a.company_id = $company and a.ref_closing_status = 0 and b.transaction_type=1 group by a.id, a.recv_number, a.receive_date, a.receive_basis, a.receive_purpose, a.item_category, a.buyer_id, a.lc_no, a.loan_party,a.supplier_id, a.pay_mode, a.source order by a.id";
					//echo $rec_sql;
					$result = sql_select($rec_sql);
				}
				if($type==69) //Purchase Requisition *PENDING
				{
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
					echo '<br><b>Incompleted<b>';
				}
				if($type==70) //Yarn Purchase Requisition
				{
					$job_sql=sql_select("select a.id, b.po_quantity as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company");
					$job_qnty_arr=array();
					foreach($job_sql as $row)
					{
						$job_qnty_arr[$row[csf("id")]]+=$row[csf("po_quantity")];
					}
					//echo $job_qnty_arr[2762].kayum;
					unset($job_sql);
					
					if($db_type==0) $select_job=" group_concat(b.job_id) as job_id"; else  $select_job=" rtrim(xmlagg(xmlelement(e,b.job_id,',').extract('//text()') order by b.job_id).GetClobVal(),',') AS job_id ";
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.requisition_date between ".$txt_date_from." and ".$txt_date_to."";

					$result= sql_select("select a.id,a.requ_no as requ_and_wo_no,a.requisition_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.quantity) as total_quantity,b.mst_id,  $select_job from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.item_category_id=1 and a.company_id=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond group by a.id,a.requ_no,a.requisition_date,a.source,a.pay_mode,b.mst_id");
				}
				if($type==100) //Yarn Purchase Order
				{
					$job_sql=sql_select("select a.id, b.po_quantity as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company");
					$job_qnty_arr=array();
					foreach($job_sql as $row)
					{
						$job_qnty_arr[$row[csf("id")]]+=$row[csf("po_quantity")];
					}
					//echo $job_qnty_arr[2762].kayum;
					unset($job_sql);
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.wo_date between ".$txt_date_from." and ".$txt_date_to."";
					$pi_qty_arr=return_library_array( "select b.work_order_id, sum(b.quantity) as qty from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company and a.item_category_id=1 and a.pi_basis_id= and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=11 group by b.work_order_id ", "work_order_id", "qty");
					$receive_qty_arr=return_library_array( "select a.pi_wo_batch_no,sum(a.order_qnty) as qty from inv_transaction a,inv_receive_master b
						where a.mst_id=b.id and a.company_id=$company and  a.item_category=1 and a.transaction_type=1 and b.item_category=1
						and b.entry_form=1 and b.receive_basis=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_wo_batch_no ", "pi_wo_batch_no", "qty");
					//print_r($receive_qty_arr);

					 /*$result_aprvl_necty_date_arr= sql_select(" select a.setup_date,b.page_id,b.approval_need from approval_setup_mst a, approval_setup_dtls b  where a.id=b.mst_id and a.company_id=$company and b.page_id=15 and b.approval_need=1 and a.is_deleted=0 and a.status_active=1");
					 $aprvl_necty_date="";
					 foreach($result_aprvl_necty_date_arr as $row)
					 {
						$aprvl_necty_date.="'".$row[csf('setup_date')]."'".',';
					 }
  					$aprvl_necty_date=chop($aprvl_necty_date,",");*/

				  	if($db_type==0)
					{
						$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company')) and page_id=15 and status_active=1 and is_deleted=0";
					}
					else
					{
						$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company')) and page_id=15 and status_active=1 and is_deleted=0";
					}
					$approval_status=sql_select($approval_status);
					if($approval_status[0][csf('approval_need')]==1)
					{
						$approval_status="1";
					}
					else
					{
						$approval_status="0,1";
					}

					$result_wo_date_arr= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date  from wo_non_order_info_mst a,wo_non_order_info_dtls b where b.item_category_id=1 and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond and a.is_approved in ($approval_status) group by a.id,a.wo_number,a.wo_date");

					$wo_approval_by_necty="";
					 foreach($result_wo_date_arr as $row)
					 {
						$wo_approval_by_necty.= $row[csf('id')].",";
					 }
					 $wo_approval_by_necty=chop($wo_approval_by_necty,",");

					if($wo_approval_by_necty!="")
					{
						$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.supplier_order_quantity) as total_quantity,b.mst_id from wo_non_order_info_mst a,wo_non_order_info_dtls b where b.item_category_id=1 and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond and a.is_approved in ($approval_status) and a.id in($wo_approval_by_necty) group by a.id,a.wo_number,a.wo_date,a.source,a.pay_mode,b.mst_id");
					}
					else
					{
						//$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.supplier_order_quantity) as total_quantity,b.mst_id from wo_non_order_info_mst a,wo_non_order_info_dtls b where b.item_category_id=1 and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond group by a.id,a.wo_number,a.wo_date,a.source,a.pay_mode,b.mst_id");

					}

				}
				if($type==101) //Dyes And Chemical Purchase Order
				{
					$job_sql=sql_select("select a.id, b.po_quantity as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company");
					$job_qnty_arr=array();
					foreach($job_sql as $row)
					{
						$job_qnty_arr[$row[csf("id")]]+=$row[csf("po_quantity")];
					}
					//echo $job_qnty_arr[2762].kayum;
					unset($job_sql);
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.wo_date between ".$txt_date_from." and ".$txt_date_to."";
					$pi_qty_arr=return_library_array( "select b.work_order_id, sum(b.quantity) as qty from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company and a.item_category_id in(5,6,7,23) and a.pi_basis_id=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.work_order_id ", "work_order_id", "qty");
					$receive_qty_arr=return_library_array( "select a.pi_wo_batch_no,sum(a.order_qnty) as qty from inv_transaction a,inv_receive_master b
						where a.mst_id=b.id and a.company_id=$company and  a.item_category in(5,6,7,23) and a.transaction_type=1 and b.item_category in(5,6,7,23)
						and b.entry_form=4 and b.receive_basis=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_wo_batch_no ", "pi_wo_batch_no", "qty");
					//print_r($receive_qty_arr);

					$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.supplier_order_quantity) as total_quantity,b.mst_id from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.item_category in(5,6,7,23) and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond group by a.id,a.wo_number,a.wo_date,a.source,a.pay_mode,b.mst_id");
				}
				if($type==102) //Stationary Purchase Order
				{
					$job_sql=sql_select("select a.id, b.po_quantity as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company");
					$job_qnty_arr=array();
					foreach($job_sql as $row)
					{
						$job_qnty_arr[$row[csf("id")]]+=$row[csf("po_quantity")];
					}
					//echo $job_qnty_arr[2762].kayum;
					unset($job_sql);
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.wo_date between ".$txt_date_from." and ".$txt_date_to."";
					$pi_qty_arr=return_library_array( "select b.work_order_id, sum(b.amount) as qty from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company and a.item_category_id in(4,11) and a.pi_basis_id=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.work_order_id ", "work_order_id", "qty");
					$receive_qty_arr=return_library_array( "select a.pi_wo_batch_no,sum(a.order_amount) as qty from inv_transaction a,inv_receive_master b
					where a.mst_id=b.id and a.company_id=$company and  a.item_category in(4,11) and a.transaction_type=1 and b.item_category in(4,11)
					and b.entry_form=20 and b.receive_basis=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_wo_batch_no ", "pi_wo_batch_no", "qty");
					//print_r($receive_qty_arr);

					$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.amount) as total_quantity,b.mst_id from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.item_category in(4,11) and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond group by a.id,a.wo_number,a.wo_date,a.source,a.pay_mode,b.mst_id");
				}
				if($type==103) // Others Purchase Order
				{
					$job_sql=sql_select("select a.id, b.po_quantity as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company");
					$job_qnty_arr=array();
					foreach($job_sql as $row)
					{
						$job_qnty_arr[$row[csf("id")]]+=$row[csf("po_quantity")];
					}
					//echo $job_qnty_arr[2762].kayum;
					unset($job_sql);
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.wo_date between ".$txt_date_from." and ".$txt_date_to."";
					$pi_qty_arr=return_library_array( "select b.work_order_id, sum(b.amount) as qty from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company and a.item_category_id in(8,9,10,15,16,17,18,19,20,21,22,32) and a.pi_basis_id = 1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.work_order_id ", "work_order_id", "qty");
					$receive_qty_arr=return_library_array( "select a.pi_wo_batch_no,sum(a.order_amount) as qty from inv_transaction a,inv_receive_master b
					where a.mst_id=b.id and a.company_id=$company and  a.item_category in(8,9,10,15,16,17,18,19,20,21,22,32) and a.transaction_type=1 and b.item_category in(8,9,10,15,16,17,18,19,20,21,22,32) and b.entry_form=20 and b.receive_basis=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_wo_batch_no ", "pi_wo_batch_no", "qty");
					//print_r($receive_qty_arr);

					$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.amount) as total_quantity,b.mst_id from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.item_category in(8,9,10,15,16,17,18,19,20,21,22,32) and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond group by a.id,a.wo_number,a.wo_date,a.source,a.pay_mode,b.mst_id");
				}
				if($type==104) // Pro Forma Invoice *SYSTEM ID TREAT AS ID
				{
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.pi_date between ".$txt_date_from." and ".$txt_date_to."";
					//$pi_qty_arr=return_library_array( "select b.work_order_id, sum(b.amount) as qty from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company and a.item_category_id in(8,9,10,15,16,17,18,19,20,21,22,32) and a.pi_basis_id = 1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.work_order_id ", "work_order_id", "qty");
					$lc_value_arr=return_library_array("select a.pi_id, sum(b.net_total_amount) as total_lc_value from com_btb_lc_pi a, com_pi_master_details b 
					where a.pi_id=b.id and b.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
					group by a.pi_id", "pi_id", "total_lc_value");
					
					$accep_value_arr=return_library_array("select a.pi_id, sum(a.current_acceptance_value) as accpe_value from com_import_invoice_dtls a, com_pi_master_details b 
					where a.pi_id=b.id and b.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
					group by a.pi_id", "pi_id", "accpe_value");

					$receive_qty_arr=return_library_array( "select a.pi_wo_batch_no, sum(a.order_amount) as qty from inv_transaction a, inv_receive_master b
					where a.mst_id=b.id and a.pi_wo_batch_no=b.booking_id and a.company_id=$company and a.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,34,35,36,37,38) and b.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,34,35,36,37,38) 
					and b.receive_basis=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_wo_batch_no", "pi_wo_batch_no", "qty");
					//print_r($receive_qty_arr);

					//$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,a.pay_mode,SUM(b.amount) as total_quantity,b.mst_id from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.item_category in(8,9,10,15,16,17,18,19,20,21,22,32) and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 group by a.id,a.wo_number,a.wo_date,a.source,a.pay_mode,b.mst_id");
					$result= sql_select("select a.id, a.pi_date as requ_and_wo_date, a.source, a.item_category_id, a.pi_number as requ_and_wo_no, SUM(b.amount) as total_quantity 
					from com_pi_master_details a,com_pi_item_details b 
					where a.id=b.pi_id and a.item_category_id in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,34,35,36,37,38) and a.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond 
					group by a.id,a.pi_date,a.source,a.item_category_id,a.pi_number");

				}
				if($type==105) // BTB/Margin LC
				{
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
					$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
					//$pi_qty_arr=return_library_array( "select b.work_order_id, sum(b.amount) as qty from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.importer_id=$company and a.item_category_id in(8,9,10,15,16,17,18,19,20,21,22,32) and a.pi_basis_id = 1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.work_order_id ", "work_order_id", "qty");
					//$lc_value_arr=return_library_array("select a.pi_id,sum(b.lc_value) as total_lc_value from com_btb_lc_pi a,com_btb_lc_master_details b where a.com_btb_lc_master_details_id=b.id and b.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_id", "pi_id", "total_lc_value");
					$rtn_sql="select a.booking_id, a.exchange_rate, c.id as trans_id, c.cons_amount as rcv_amount 
					from inv_receive_master a, inv_issue_master b, inv_transaction c
					where a.id=b.received_id and b.id=c.mst_id and a.company_id=$company and c.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) and a.receive_basis=1 and c.transaction_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
					$rtn_result=sql_select($rtn_sql);
					foreach($rtn_result as $row)
					{
						if($trans_check[$row[csf("trans_id")]]=="")
						{
							$trans_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
							$receive_rtn_arr[$row[csf("booking_id")]]+=$row[csf("rcv_amount")]/$row[csf("exchange_rate")];
						}
					}
					
					//print_r($receive_qty_arr);
					$receive_qty_arr=return_library_array( "select a.pi_wo_batch_no,sum(a.order_amount) as qty from inv_transaction a, inv_receive_master b
					where a.mst_id=b.id and a.pi_wo_batch_no=b.booking_id and a.company_id=$company and a.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) and b.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) and b.receive_basis=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.pi_wo_batch_no", "pi_wo_batch_no", "qty");
					$btb_sql = "select a.id, a.btb_system_id as requ_and_wo_no_btb, a.lc_date as requ_and_wo_date, a.supplier_id, a.item_category_id, SUM(a.lc_value) as total_quantity, a.lc_number as requ_and_wo_no, a.pi_id as multi_pi_id, a.lc_expiry_date, b.com_btb_lc_master_details_id 
					from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
					where a.id=b.com_btb_lc_master_details_id and b.pi_id = c.pi_id and c.item_category_id in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) and a.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 $wo_date_cond 
					group by a.id, a.btb_system_id, a.lc_date, a.supplier_id, a.item_category_id, a.lc_number, a.pi_id, a.lc_expiry_date, b.com_btb_lc_master_details_id";
					//echo $btb_sql;
				 	$result= sql_select($btb_sql);
				}
				if($type==106) // Export LC Entry
				{
					/*if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
					$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					$invoice_value_arr=return_library_array( "select lc_sc_id,sum(invoice_value) as invoice_value from com_export_invoice_ship_mst where is_deleted=0 and status_active=1 and benificiary_id=$company  group by lc_sc_id","lc_sc_id", "invoice_value");
					*/
				 //	$result= sql_select("select id,lc_date as requ_and_wo_date,export_lc_system_id,a.buyer_name,tolerance,SUM(lc_value) as total_quantity,export_lc_no as requ_and_wo_no ,expiry_date as lc_expiry_date from com_export_lc a where export_item_category in(1,2,3,4,10,11,20,21,22,23,24,30,31,35,36,37,40,45,46,47,48,49,50,51,55,60,65,66) and beneficiary_name=$company and is_deleted=0 and status_active=1 and ref_closing_status=$unclose_id $wo_date_cond group by id,lc_date,export_lc_system_id,buyer_name,tolerance,export_lc_no,expiry_date");
					//echo "select id,lc_date as requ_and_wo_date,export_lc_system_id,a.buyer_name,tolerance,SUM(lc_value) as total_quantity,export_lc_no as requ_and_wo_no ,expiry_date as lc_expiry_date from com_export_lc a where export_item_category in(1,2,3,4,10,11,20,21,22,23,24,30,31,35,36,37,40,45,46,47,48,49,50,51,55,60,65,66) and beneficiary_name=$company and is_deleted=0 and status_active=1 and ref_closing_status=$unclose_id $wo_date_cond group by id,lc_date,export_lc_system_id,buyer_name,tolerance,export_lc_no,expiry_date";
				}
				if($type==107) // Sales Contract Entry *PENDING
				{
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
					if($db_type==0) $select_job=" group_concat(b.job_id) as job_id"; else  $select_job=" rtrim(xmlagg(xmlelement(e,b.job_id,',').extract('//text()') order by b.job_id).GetClobVal(),',') AS job_id ";
					echo '<br><b>Incompleted<b>';
					//$result= sql_select("select a.id,a.wo_number as requ_and_wo_no,a.wo_date as requ_and_wo_date,a.source,SUM(b.supplier_order_quantity) as total_quantity,b.mst_id,$select_job from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.item_category in(4,11) and a.company_name=$company and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and a.ref_closing_status=0 group by a.id,a.wo_number,a.wo_date,a.source,b.mst_id");
				}
				if($type==117) // Sample Requisition
				{
					if($db_type==0)
					{
						$is_complete_prod=" and b.is_complete_prod=0";
					}
					else
					{
						$is_complete_prod=" and b.is_complete_prod is null";
					}
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.requisition_date between ".$txt_date_from." and ".$txt_date_to."";
					$result=sql_select("select a.id as req_id,a.requisition_number  as requ_and_wo_no,b.id,a.requisition_date as requ_and_wo_date ,b.sample_name, b.gmts_item_id, b.smv, b.sample_color, b.sample_prod_qty as total_quantity, b.submission_qty, b.delv_start_date, b.delv_end_date, b.sample_charge, b.sample_curency from sample_development_mst a, sample_development_dtls b where  a.id=b.sample_mst_id  and a.entry_form_id=117 and b.entry_form_id=117 and a.company_id=$company and  a.is_deleted=0  and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $is_complete_prod $wo_date_cond  group by a.id  ,b.id,a.requisition_date   ,b.sample_name, b.gmts_item_id, b.smv, b.sample_color, b.sample_prod_qty, b.submission_qty, b.delv_start_date, b.delv_end_date, b.sample_charge, b.sample_curency,a.requisition_number order by b.id asc");
				}
				
			
				
				
				
				
				if($type==163)// Order Entry
				{
					$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					//$order_qnty=return_library_array( "SELECT a.id, a.po_quantity*b.total_set_qnty as qnty  from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active in(1,2,3) and b.is_deleted=0 and b.status_active=1  ", "id", "qnty");
					//$ex_fac_qnty=return_library_array( "SELECT po_break_down_id,sum(ex_factory_qnty) as qnty from pro_ex_factory_mst where is_deleted=0 and entry_form=0 and  status_active =1 group by po_break_down_id", "po_break_down_id", "qnty");

					//$ex_fac_ret_qnty=return_library_array( "SELECT a.po_break_down_id,sum(b.production_qnty) as qnty from pro_ex_factory_mst a, pro_ex_factory_dtls b where a.is_deleted=0 and a.entry_form=85 and a.id=b.mst_id and a.status_active =1 group by a.po_break_down_id", "po_break_down_id", "qnty");
					
					$ex_fac_sql="SELECT a.po_break_down_id, sum(case when a.entry_form=0 then b.production_qnty else 0 end) as qnty, sum(case when a.entry_form=85 then b.production_qnty else 0 end) as qnty_ret 
					from pro_ex_factory_mst a, pro_ex_factory_dtls b 
					where a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 and b.status_active =1  and a.id=b.mst_id  
					group by a.po_break_down_id";
					$ex_fac_sql_result=sql_select($ex_fac_sql);
					foreach($ex_fac_sql_result as $val)
					{
						$ex_fac_qnty[$val[csf("po_break_down_id")]]+=$val[csf("qnty")];
						$ex_fac_ret_qnty[$val[csf("po_break_down_id")]]+=$val[csf("qnty_ret")];
					}
					

					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.shipment_date between ".$txt_date_from." and ".$txt_date_to."";
					$shipment_cond="";
					if($only_full=='true') $shipment_cond .=" and  a.shiping_status=3";
					else $shipment_cond .=" and  a.shiping_status NOT IN (3) ";
                    $buyer_cond = "";
                    if(str_replace("'", "", $cbo_buyer) > 0){
                        $buyer_cond = " and b.buyer_name = ".str_replace("'", "", $cbo_buyer);
                    }
                
					$sql="SELECT b.team_leader, a.id, a.job_no_mst, a.po_number, a.shiping_status, a.shipment_date, b.buyer_name, b.style_ref_no, b.dealing_marchant, b.factory_marchant, a.grouping, a.is_confirmed, a.po_quantity*b.total_set_qnty as qnty
					from wo_po_details_master b, wo_po_break_down a 
					where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 $wo_date_cond $buyer_cond $job_wo_cond and b.company_name=$company $shipment_cond
					order by a.id asc";
					//echo $sql;die;
					$result=sql_select($sql);
					
					if(empty($result))
					{
						echo get_empty_data_msg();
						die;
					}
					
					$po_all_data=array();
					foreach($result as $row)
					{
						$ex_qty=$ex_fac_qnty[$row[csf("id")]]-$ex_fac_ret_qnty[$row[csf("id")]];
						$bal=$row[csf("qnty")]-$ex_qty; 
						
						
						$po_all_data[$bal][$row[csf("id")]]["id"]=$row[csf("id")];
						$po_all_data[$bal][$row[csf("id")]]["team_leader"]=$row[csf("team_leader")];
						$po_all_data[$bal][$row[csf("id")]]["job_no_mst"]=$row[csf("job_no_mst")];
						$po_all_data[$bal][$row[csf("id")]]["po_number"]=$row[csf("po_number")];
						$po_all_data[$bal][$row[csf("id")]]["shiping_status"]=$row[csf("shiping_status")];
						$po_all_data[$bal][$row[csf("id")]]["shipment_date"]=$row[csf("shipment_date")];
						$po_all_data[$bal][$row[csf("id")]]["buyer_name"]=$row[csf("buyer_name")];
						
						$po_all_data[$bal][$row[csf("id")]]["style_ref_no"]=$row[csf("style_ref_no")];
						$po_all_data[$bal][$row[csf("id")]]["dealing_marchant"]=$row[csf("dealing_marchant")];
						$po_all_data[$bal][$row[csf("id")]]["factory_marchant"]=$row[csf("factory_marchant")];
						$po_all_data[$bal][$row[csf("id")]]["grouping"]=$row[csf("grouping")];
						
						$po_all_data[$bal][$row[csf("id")]]["grouping"]=$row[csf("grouping")];
						$po_all_data[$bal][$row[csf("id")]]["is_confirmed"]=$row[csf("is_confirmed")];
						$po_all_data[$bal][$row[csf("id")]]["qnty"]=$row[csf("qnty")];
						
						$po_all_data[$bal][$row[csf("id")]]["ex_qty"]=$ex_qty;
						$po_all_data[$bal][$row[csf("id")]]["bal"]=$bal;
					}
					ksort($po_all_data);
					//echo "<pre>";print_r($po_all_data);die;
					
					$ship_date_sql=sql_select("select  max(a.acc_ship_date) as acc_ship_date,b.job_no from wo_po_acc_po_info a, wo_po_details_master b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_name=$company group by b.job_no");
					
					foreach($ship_date_sql as $row)
					{
						$ship_date[$row[csf("job_no")]]=$row[csf("acc_ship_date")];
					}
					
					$k=1;
					$tot_po_qty=0;
					$tot_ex_qty=0;
					$tot_bal=0;
					foreach($po_all_data as $bal_qnty=>$bal_value)
					{
						foreach($bal_value as $po_id=>$row)
						{
							if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$requ_and_wo_no=$row["po_number"];
							$contents.= ob_get_flush();
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"  style="text-decoration:none; cursor:pointer" id="search<?=$k;?>"   >
								<td style="word-break:break-all" width="25" align="center">
									 <input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)" value="<? echo $row['id'].'**'.$requ_and_wo_no;  ?>"/>
									  <input type="hidden" id="txt_individual_id<? echo $k;  ?>"  style="margin-left:3px;" value="<? echo $row['id'];?>"/>
								</td>
								<?php
								//$contents.= ob_get_flush();
								ob_start();
								?>
								<td style="word-break:break-all" width="30" align="center"><? echo  $i++; ?></td>
								<td style="word-break:break-all" width="<? if($type==163) echo "80"; else echo "110";?>" title="<? if($type==4) {echo "rcv_id: ";}else {echo "lc_id: ";} echo $row['id']; ?>">
								<p>
								<? 
								echo $lib_buyer_arr[$row["buyer_name"]];
								?> 
								&nbsp;</p>
								</td>
								<td style="word-break:break-all"  width=" <? if($type==117 || $type==163){ echo '110';} else { echo '65';}?> " align="center"><p>
								<? echo $row["job_no_mst"]; ?>&nbsp;</p></td>
								<td style="word-break:break-all"  width="100" align="center"><p><? echo $row["style_ref_no"];   ?></p></td>
								<td style="word-break:break-all;" width="80"><?php echo $row["grouping"]; ?></td> 
		
								<td style="word-break:break-all" width="<? if($type==117 ){echo "100" ;} else {echo "120";}?>"><p>
								<?
								echo $row["po_number"];
								?>
								&nbsp;</p></td>
								<td style="word-break:break-all" width="<? if($type==163){echo "80";}else{echo "110";}?>"><p>
								<?
								echo change_date_format($row["shipment_date"]);
								?>
								&nbsp;</p></td>
								<td width="80"><? echo change_date_format($ship_date[$row["job_no_mst"]]);?></td>
								<td width="80"><?= $order_status[$row["is_confirmed"]]?></td>
								<td style="word-break:break-all" width="<? if($type==163){echo "80";}else{echo "90";} ?>" align="center"><p>
								<? echo $po_qty= $row["qnty"]; ?> &nbsp;</p></td>
								<td style="word-wrap: break-word;word-break: break-all;" align="center" width="60">	<? echo $ex_qty=$row["ex_qty"]; ?></td>
								<td  style="word-wrap: break-word;word-break: break-all;" align="center" width="110">	<? echo  $bal=$po_qty-$ex_qty; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;"  align="center" width="110">	<? echo  $shipment_status[$row["shiping_status"]]; ?></td>
								<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo  $team_leader_arr[$row["team_leader"]]; ?></td>
								<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo  $merchant_name_arr[$row["dealing_marchant"]]; ?></td>
								<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo  $merchant_name_arr[$row["factory_marchant"]]; ?></td>
							</tr>
							<?
							$k++;
		
							$tot_po_qty+=$po_qty;
							$tot_ex_qty+=$ex_qty;
							$tot_bal+=$bal;
						}
					}
				}
				else
				{
					if(empty($result))
					{
						echo get_empty_data_msg();
						die;
					}
					$k=1;
					$tot_po_qty=0;
					$tot_ex_qty=0;
					$tot_bal=0;
					foreach($result as $row)
					{
						if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($db_type==2 && $type==70) $row[csf('job_id')] = $row[csf('job_id')]->load();
						$all_job_id=array_unique(explode(",",$row[csf("job_id")]));
						$job_qnty=0;
						foreach($all_job_id as $job_id)
						{
							$job_qnty+=$job_qnty_arr[$job_id];
						}
						//for type=105
						if($type==105){
							$all_single_pi=array_unique(explode(",",$row[csf("multi_pi_id")]));
							//print_r( $all_single_pi);
							//echo "<br/>";
							//$single_pi_arrr=array();
							$pi_rec_qty=0;$pi_rcv_rtn_amt=0;
							foreach($all_single_pi as $show_single_pi)
							{
								$pi_rec_qty+=$receive_qty_arr[$show_single_pi];
								$pi_rcv_rtn_amt+=$receive_rtn_arr[$show_single_pi];
							}
							//print_r($single_pi_arrr);
							$requ_and_wo_no=$row[csf("requ_and_wo_no")];
						}
						elseif($type==106)
						{
							//$requ_and_wo_no=$row[csf("export_lc_system_id")];
						}
						elseif($type==4)
						{
							$requ_and_wo_no=$row[csf("recv_number")];
						}
						else
						{
							$requ_and_wo_no=$row[csf("requ_and_wo_no")];
						}
	
						?>
						<tr  bgcolor="<? echo $bgcolor; ?>"  style="text-decoration:none; cursor:pointer" id="search<?=$k;?>"   >
							<?php
							$contents.= ob_get_flush();
							//ob_start();
							?>
							<td style="word-break:break-all" width="25" align="center">
								 <input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)"   value="<? echo $row[csf('id')].'**'.$requ_and_wo_no;  ?>"/>
								  <input type="hidden" id="txt_individual_id<? echo $k;  ?>"  style="margin-left:3px;" value="<? echo $row[csf('id')];?>"/>
							</td>
							<?php
							//$contents.= ob_get_flush();
							ob_start();
							?>
							<td style="word-break:break-all" width="30" align="center"><? echo  $i++; ?></td>
							<td style="word-break:break-all" width="<? if($type==163) echo "80"; else echo "110";?>" title="<? if($type==4) {echo "rcv_id: ";}else {echo "lc_id: ";} echo $row[csf("id")]; ?>">
							<p>
							<? 
							if($type==163) { echo $lib_buyer_arr[$row[csf("buyer_name")]];}
							elseif($type == 4){ echo $row[csf("recv_number")];}
							else { echo $requ_and_wo_no;} 
							?> 
							&nbsp;</p>
							</td>
							<td style="word-break:break-all"  width=" <? if($type==117 || $type==163){ echo '110';} else { echo '65';}?> " align="center"><p>
							<? 
							if($type==163) { echo $row[csf("job_no_mst")];}
							
							elseif($type==4){ echo $row[csf("receive_date")];}
							else{ echo  change_date_format($row[csf("requ_and_wo_date")]);} 
							?>
	
							&nbsp;</p></td>
							<? 
							if($type==163){
	
							 ?>
							<td style="word-break:break-all"  width="100" align="center"><p><? echo $row[csf("style_ref_no")];   ?></p></td>
							<? 
							 }
							 ?>
							 
	
							<?php
							if($type == 163)
							{
								?>
								<td style="word-break:break-all;" width="80"><?php echo $row[csf("grouping")]; ?></td>
								<?php
							}
							?>
							<td style="word-break:break-all" width="<? if($type==117 ){echo "100" ;} else {echo "120";}?>"><p>
							<?
							if($type==105){ echo  $lib_supplier_arr[$row[csf("supplier_id")]]; }
							elseif($type==106) { echo $lib_buyer_arr[$row[csf("buyer_name")]];}
							elseif($type==163){ echo $row[csf("requ_and_wo_no")];}
							elseif($type==117){ echo $sample_name_library[$row[csf("sample_name")]];}
							else{ echo  $source[$row[csf("source")]]; }
							?>
							&nbsp;</p></td>
							<td style="word-break:break-all" width="<? if($type==163){echo "80";}else{echo "110";}?>"><p>
							<?
							if($type==117){ echo $item_arrs[$row[csf("gmts_item_id")]];}
							elseif($type==163){ echo change_date_format($row[csf("requ_and_wo_date")]);}
							elseif($type==4){ echo  $lib_supplier_arr[$row[csf("supplier_id")]];}
							else 
							{
								$birth_date=change_date_format($row[csf('requ_and_wo_date')]);
								$age = my_old($birth_date);
								if($age[year]>0 )
								{
									printf("%d years, %d months, %d days\n", $age[year], $age[mnt], $age[day]);
								}
								else
								{
									printf("%d months, %d days\n", $age[mnt], $age[day]);
								}
							}
							?>
							&nbsp;</p></td>
							<?
							if($type == 163){
							?>
								<td width="80"><? echo change_date_format($ship_date[$row[csf("job_no_mst")]]);?></td>
							<?
							}
							?>
							<?
							if($type == 163){
							?>
								<td width="80"><?= $order_status[$row[csf("is_confirmed")]]?></td>
							<?
							} 
							?>
							<td style="word-break:break-all" width="<? if($type==163){echo "80";}else{echo "90";} ?>" align="center"><p>
							<?
							if($type==104 || $type==105){ echo $item_category[$row[csf("item_category_id")]];}
							elseif($type==106){ echo $row[csf("tolerance")]; }
							elseif($type==117){ echo  $color_library[$row[csf("sample_color")]];}
							elseif($type==163){ echo $po_qty= $order_qnty[$row[csf("id")]];}
							else { echo $pay_mode[$row[csf("pay_mode")]];}
							?>
							&nbsp;</p></td>
							<?
							if($type!=163)
							{
								
								?>
								<td  width="<? if($type==117){echo "100" ;} else {echo "80";}?>" align="right">
								<?
								if($type == 4){ echo number_format($row[csf("rcv_qnty")],2);}
								else{ echo  number_format($row[csf("total_quantity")],2);}
								?>
								</td>
								<?
								if($type==104)
								{
									echo '<td width="80" align="right">';
									echo number_format($lc_value_arr[$row[csf("id")]],2);
									?>
									</td>
									<td width="80" align="right"><? echo number_format($accep_value_arr[$row[csf("id")]],2);?></td>
									<?
								}
								?>
								<td width="<? if($type==117){echo "120" ;} else {echo "80";}?>" align="<? if($type==117){echo "center";} else {echo "right";} ?>">
								<?
								if($type==70){
									echo  number_format($job_qnty,2);
								}
								if($type==100 || $type==101 || $type==102 || $type==103)
								{
									if($row[csf("pay_mode")]==2)
									{
										echo number_format($pi_qty_arr[$row[csf("id")]],2);
									}
									else
									{
										echo number_format($receive_qty_arr[$row[csf("id")]],2);
									}
								}
								if(($type==104))
								{
									echo number_format($receive_qty_arr[$row[csf("id")]],2);
								}
								if(($type==117))
								{
									echo change_date_format($row[csf("delv_start_date")]);
								}
								if(($type==105))
								{
									//$resultt = array_intersect($single_pi_arrr, $receive_qty_arr);
									//print_r( $resultt);
								   //$singlePi[$row[csf(628)]][$row[csf("multi_pi_id")]]= $singlePi;
								   // $data[] = $row[csf(628)]][$row[110,122,123] ==
									/*if(in_array($receive_qty_arr[0],$single_pi_arrr))
									{*/
									//$arrLanth=count($single_pi_arrr);
									echo number_format($pi_rec_qty,2);
									//$RecValueTotal="";
									//$increment_index=0;
									//for($g=0;$g <= $arrLanth;$g++){
										//$RecValueTotal+= $single_pi_arrr=$receive_qty_arr[$single_pi_arrr[0]];
										//echo $single_pi_arRr=$receive_qty_arr[$single_pi_arrr];
										//echo $increment_index++;
										//echo $single_pi_arrr=$receive_qty_arr[$single_pi_arrr[2]];
										//echo $RecValueTotal;
										//}
	
									/*}
									print_r( $receive_qty_arr);*/
									//echo number_format($receive_qty_arr[$row[csf("multi_pi_id")]],2);
								  //print_r(  $receive_qty_arr);
								}
								if(($type==106))
								{
									echo number_format($invoice_value_arr[$row[csf("id")]],2);
									$invoce_valuee=$invoice_value_arr[$row[csf("id")]];
								}
								if($type == 4){
									echo $item_category[$row[csf("item_category")]];
								}
								?>
								</td>
								<? if($type==105 || $type==106)
								{
									//------ if expire date is gone td color red
									$now = time(); // or your date as well
									$your_date = strtotime($row[csf("lc_expiry_date")]);
									if($now>=$your_date)
									{
										echo '<td width="65" align="center" style="background-color:red;">';
									}else{echo '<td width="65" align="center" >';}
									echo change_date_format($row[csf("lc_expiry_date")]);
									?>
									</td>
									<?
								}
								if($type==4)
								{
									echo '<td width="65 align="center">';
									echo $lib_supplier_arr[$row[csf("loan_party")]];
									?>
									</td>
									<?
								 }
								 if($type==70 || $type==100 || $type==101 || $type==102 || $type==103 || $type==104 || $type==105)
								 {
									  $th_width='width="100"';
								 }elseif($type == 4){
									  $th_width = 'width="80"';
								 } 
								 if($type==105){
									 ?>
									 <td width="100" align="right"><? echo number_format($pi_rcv_rtn_amt,2); ?></td>
									 <td width="100" align="right"><? $ac_rcv=$pi_rec_qty-$pi_rcv_rtn_amt; echo number_format($ac_rcv,2); ?></td>
									 <? 
								}
								?>
								<td align="<? if($type==117){echo "center";} else {echo "right";} ?>" <? echo $th_width; ?>>
								<?
								if($type==70)
								{
									$balance_req=$row[csf("total_quantity")]-$job_qnty;
									echo  number_format($balance_req,2);
								}
								elseif($type==117)
								{
									echo change_date_format($row[csf("delv_end_date")]);
								}
								elseif($type==100 || $type==101 || $type==102 || $type==103)
								{
									$balance_pi=$row[csf("total_quantity")]-$pi_qty_arr[$row[csf("id")]];
									if($pay_mode[$row[csf("pay_mode")]]==2)
									{
										echo  number_format($balance_pi,2);
									}
									else
									{
										$balance_recv=$row[csf("total_quantity")]-$receive_qty_arr[$row[csf("id")]];
										echo  number_format($balance_recv,2);
									}
								}
								if($type==104)
								{
									 $balance_valuepi= $row[csf("total_quantity")]-$receive_qty_arr[$row[csf("id")]];
									 echo  number_format( $balance_valuepi,2);
								}
								if($type==105)
								{
									 $balance_valuepi= $row[csf("total_quantity")]-$ac_rcv;
									 echo  number_format( $balance_valuepi,2);
								}
								if($type==106)
								{
									 $balance_invoice_value= $row[csf("total_quantity")]-$invoce_valuee;
									 echo  number_format( $balance_invoice_value,2);
								}
								if($type==4)
								{
									 echo $receive_basis_arr[$row[csf("receive_basis")]];
								}
								 //echo  number_format($balance,2); ?>
								</td>
								<? if($type==106){
								echo '<td width="83" align="right">';
								$percent_value=($balance_invoice_value/$row[csf("total_quantity")])*100;
								echo number_format($percent_value,2);
								?>
								</td>
								<?
								}
							}
							else
							{
								?>
								<td style="word-wrap: break-word;word-break: break-all;" align="center" width="60">	<? echo $ex_qty=$ex_fac_qnty[$row[csf("id")]]-$ex_fac_ret_qnty[$row[csf("id")]]; ?></td>
								<td  style="word-wrap: break-word;word-break: break-all;" align="center" width="110">	<? echo  $bal=$po_qty-$ex_qty; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;"  align="center" width="110">	<? echo  $shipment_status[$row[csf("shiping_status")]]; ?></td>
								<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo  $team_leader_arr[$row[csf("team_leader")]]; ?></td>
								<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo  $merchant_name_arr[$row["DEALING_MARCHANT"]]; ?></td>
								<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo  $merchant_name_arr[$row["FACTORY_MARCHANT"]]; ?></td>
								<?
							}
							?>
	
						</tr>
						<?
						$k++;
	
						$tot_po_qty+=$po_qty;
						$tot_ex_qty+=$ex_qty;
						$tot_bal+=$bal;
					}
				}

				

				?>
            </tbody>
		</table>
		<?
		if($type==163)
		{
			?>
			<table id="tbl_list_footer" align="center" class="rpt_table" rules="all" border="1" style="width:1800px;" >
				<tr>
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td width="25">&nbsp;</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td width="30">&nbsp;</td>
					<td width="<? if($type==163) echo "80";else echo "110";?>"></td>
					<?if($type==163)
					{?>
                     <td width="110">&nbsp;</td>
					 <?
					}
					?>
					
					<td width="100">&nbsp;</td>
                    <td width="80">&nbsp;</td>
					<td width="<? if($type==117 ){echo "100" ;} else {echo "120";}?>">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">Grand Total </td>
					<td align="center" id="po" width="80"><? echo $tot_po_qty; ?></td>
					<td  align="center" id='ship' width="60"><? echo $tot_ex_qty; ?></td>
					<td  align="center" id="bal" width="110"><? echo $tot_bal; ?></td>
					<td width="110"></td>
					<td  width="100" ></td>
					<td  width="100" ></td>
					<td  width="100" ></td>
				</tr>

			</table>
			<?
		}
		?>
		</div>
		<script type="text/javascript">
			var type='<? echo $type;?>';
			if(type==163)
			{
				var tableFilters1 =
				{
					col_10: "select",
					col_12: "select",
					display_all_text:'Show All',

					col_operation: {
						id: ["po","ship","bal"],
						col: [10,11,12],
						operation: ["sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML"]
					}
				}
				setFilterGrid("tbl_list",-1,tableFilters1);
			}
		</script>
		<?

		$html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			 @unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php 
}

if($action=="show_details_pi")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	//$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=163)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?
	$lc_value_arr=return_library_array("select a.pi_id, sum(b.net_total_amount) as total_lc_value from com_btb_lc_pi a, com_pi_master_details b 
	where a.pi_id=b.id and b.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
	group by a.pi_id", "pi_id", "total_lc_value");
	
	$accep_value_arr=return_library_array("select a.pi_id, sum(a.current_acceptance_value) as accpe_value from com_import_invoice_dtls a, com_pi_master_details b 
	where a.pi_id=b.id and b.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
	group by a.pi_id", "pi_id", "accpe_value");

	$receive_qty_arr=return_library_array( "select b.booking_id, sum(a.order_amount) as qty 
	from inv_transaction a, inv_receive_master b
	where a.mst_id=b.id and a.transaction_type=1 and b.receive_basis=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_id=$company 
	and a.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) 
	and b.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) 
	group by b.booking_id", "booking_id", "qty");
	
	$wo_date_cond="";
	if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.pi_date between ".$txt_date_from." and ".$txt_date_to."";
	$result= sql_select("select a.id, a.pi_date as requ_and_wo_date, a.source, a.item_category_id, a.internal_file_no, a.pi_number as requ_and_wo_no, SUM(b.amount) as total_quantity 
	from com_pi_master_details a, com_pi_item_details b 
	where a.id=b.pi_id and a.item_category_id in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,34,35,36,37,38) and a.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=$only_full $wo_date_cond 
	group by a.id, a.pi_date, a.source, a.item_category_id, a.internal_file_no, a.pi_number
	order by a.id desc");
	if($check_only_full=='true')
	{
		$closing_status=0;
	}
	else $closing_status=1;

	ob_start();
	$contents='';
	//$contents.= ob_get_flush();
	//ob_start();
	?>
    <div style="width:1200px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1200" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
			<tr>
				<?php
            	$contents.= ob_get_flush();
            	?>
                <th colspan="14" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onClick="check_all_data();"/>
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                </th>
                <?php
            	//$contents.= ob_get_flush();
				ob_start();
            	?>
                <th width="30">SL</th>
                <th width="120">PI No</th>
                <th width="70">PI Date</th>
                <th width="120">Item Category</th>
                <th width="100">File No</th>
                <th width="90">Source</th>
                <th width="135">Age</th>
                <th width="70">Pay Mode</th>
                <th width="80">PI Value</th>
                <th width="80">LC Value</th>
                <th width="80">Acceptance Value</th>
                <th width="80">Receive Value</th>
                <th>Balance Value</th>
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1200px;">
    <table id="tbl_list" class="rpt_table" rules="all" border="1" width="1190" align="left">
    	<tbody>
        	<?
			
			$i=1;$k=1;
			foreach($result as $row)
			{
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $row[csf('id')].'**'.$row[csf("requ_and_wo_no")];  ?>"/>
					</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="120" title="<?=  "PI id :".$row[csf("id")];?>"><p><? echo $row[csf("requ_and_wo_no")]; ?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="70" align="center"><p><? echo  change_date_format($row[csf("requ_and_wo_date")]);?>&nbsp;</p></td>
                    <td style="word-break:break-all" width="120" title="<?= $row[csf("item_category_id")];?>"><p><? echo  $item_category[$row[csf("item_category_id")]];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center"><p><? echo  $row[csf("internal_file_no")];?>&nbsp;</p></td>
                    <td style="word-break:break-all" width="90" align="center" title="<?= $row[csf("source")];?>"><p><? echo  $source[$row[csf("source")]];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="135"><p><?
					$birth_date=change_date_format($row[csf('requ_and_wo_date')]);
					$age = my_old($birth_date);
					if($age[year]>0 )
					{
						printf("%d years, %d months, %d days\n", $age[year], $age[mnt], $age[day]);
					}
					else
					{
						printf("%d months, %d days\n", $age[mnt], $age[day]);
					}
					?>
					&nbsp;</p></td>
					<td style="word-break:break-all" width="70" align="center"><p><? echo $pay_mode[2];?>&nbsp;</p></td>
					<td width="80" align="right"><? echo  number_format($row[csf("total_quantity")],2); ?></td>
                    <td width="80" align="right"><? echo number_format($lc_value_arr[$row[csf("id")]],2);?></td>
                    <td width="80" align="right"><? echo number_format($accep_value_arr[$row[csf("id")]],2);?></td>
					<td width="80" align="right"><? echo number_format($receive_qty_arr[$row[csf("id")]],2);?></td>
                    <td align="right"><? $balance_valuepi= $row[csf("total_quantity")]-$receive_qty_arr[$row[csf("id")]]; echo number_format( $balance_valuepi,2); ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?
    $html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			 @unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php 
}

if($action=="show_details_btb")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=163)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?
	$rtn_sql="select a.booking_id, a.exchange_rate, c.id as trans_id, c.cons_amount as rcv_amount 
	from inv_receive_master a, inv_issue_master b, inv_transaction c
	where a.id=b.received_id and b.id=c.mst_id and a.company_id=$company and c.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) and a.receive_basis=1 and c.transaction_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	$rtn_result=sql_select($rtn_sql);
	foreach($rtn_result as $row)
	{
		if($trans_check[$row[csf("trans_id")]]=="")
		{
			$trans_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
			$receive_rtn_arr[$row[csf("booking_id")]]+=$row[csf("rcv_amount")]/$row[csf("exchange_rate")];
		}
	}

	$receive_qty_arr=return_library_array( "select b.booking_id, sum(a.order_amount) as qty 
	from inv_transaction a, inv_receive_master b
	where a.mst_id=b.id and a.transaction_type=1 and b.receive_basis=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_id=$company 
	and a.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) 
	and b.item_category in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110) 
	group by b.booking_id", "booking_id", "qty");
	// and c.item_category_id in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,28,30,31,32,33,110)
	$wo_date_cond="";
	if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
	$btb_sql = "select a.id, a.btb_system_id, a.lc_date, a.supplier_id, a.lc_value, a.lc_number, a.lc_expiry_date, c.item_category_id, c.id as pi_id
	from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
	where a.id=b.com_btb_lc_master_details_id and b.pi_id = c.pi_id and a.importer_id=$company and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=$only_full $wo_date_cond";
	//echo $btb_sql;
	$result= sql_select($btb_sql);
	$btb_data=array();
	foreach($result as $row)
	{
		$btb_data[$row[csf("id")]]["id"]=$row[csf("id")];
		$btb_data[$row[csf("id")]]["btb_system_id"]=$row[csf("btb_system_id")];
		$btb_data[$row[csf("id")]]["lc_date"]=$row[csf("lc_date")];
		$btb_data[$row[csf("id")]]["supplier_id"]=$row[csf("supplier_id")];
		$btb_data[$row[csf("id")]]["lc_value"]=$row[csf("lc_value")];
		$btb_data[$row[csf("id")]]["lc_number"]=$row[csf("lc_number")];
		$btb_data[$row[csf("id")]]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
		if($pi_check[$row[csf("id")]][$row[csf("pi_id")]]=="")
		{
			$pi_check[$row[csf("id")]][$row[csf("pi_id")]]=$row[csf("pi_id")];
			$btb_data[$row[csf("id")]]["pi_id"].=$row[csf("pi_id")].",";
		}
		if($btb_cat_check[$row[csf("id")]][$row[csf("item_category_id")]]=="")
		{
			$btb_cat_check[$row[csf("id")]][$row[csf("item_category_id")]]=$row[csf("item_category_id")];
			$btb_data[$row[csf("id")]]["item_category_id"].=$row[csf("item_category_id")].",";
		}
	}
	
		if($check_only_full=='true')
		{
			$closing_status=0;
		}
		else $closing_status=1;

	//ob_start(); 
	ob_start();
	$contents='';
	//$contents.= ob_get_flush();
	//ob_start();
	?>
    <div style="width:1150px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1150" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
            	<?php
            	$contents.= ob_get_flush();
				//ob_start();
            	?>
				<th colspan="13" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onClick="check_all_data();"/>
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                </th>
                <?php
            	//$contents.= ob_get_flush();
				ob_start();
            	?>
                <th width="40">SL</th>
                <th width="120">BTB LC No</th>
                <th width="70">BTB LC Date</th>
                <th width="70">LC Exp. Date</th>
                <th width="110">Supplier</th>
                <th width="110">Age</th>
                <th width="110">Item Category</th>
                <th width="90">LC Value </th>
                <th width="90">Receive Value</th>
                <th width="90">Receive Rtn Value</th>
                <th width="90">Actual Rcv Value</th>
                <th>Balance Value</th>
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1150px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1130">
    	<tbody>
        	<?
			$i=1;$k=1;
			foreach($btb_data as $btb_id=>$val)
			{
				$item_cat_arr=explode(",",chop($val["item_category_id"],","));
				$all_cat=$rcv_val=$rcv_rtn_val=$rcv_balance=$balance_val="";
				foreach($item_cat_arr as $cat_id)
				{
					$all_cat.=$item_category[$cat_id].",";
				}
				$pi_id_arr=explode(",",chop($val["pi_id"],","));
				foreach($pi_id_arr as $pi_id)
				{
					$rcv_val+=$receive_qty_arr[$pi_id];
					$rcv_rtn_val+=$receive_rtn_arr[$pi_id];
				}
				$rcv_balance=$rcv_val-$rcv_rtn_val;
				$balance_val=$val["lc_value"]-$rcv_balance;
				
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $btb_id.'**'.$val["lc_number"];  ?>"/>
					</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td style="word-break:break-all" width="40" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="120" title="<?=  "PI id :".$btb_id;?>"><p><? echo $val["lc_number"]; ?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="70" align="center"><p><? echo  change_date_format($val["lc_date"]);?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="70" align="center"><p><? echo  change_date_format($val["lc_expiry_date"]);?>&nbsp;</p></td>
					<td style="word-break:break-all" width="110" align="center" title="<?= $row[csf("source")];?>"><p><? echo  $lib_supplier_arr[$val["supplier_id"]];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="110"><p><?
					$birth_date=change_date_format($val["lc_date"]);
					$age = my_old($birth_date);
					if($age[year]>0 )
					{
						printf("%d years, %d months, %d days\n", $age[year], $age[mnt], $age[day]);
					}
					else
					{
						printf("%d months, %d days\n", $age[mnt], $age[day]);
					}
					?>
					&nbsp;</p></td>
					<td style="word-break:break-all" width="110" align="center"><p><? echo chop($all_cat,",");?>&nbsp;</p></td>
					<td width="90" align="right"><? echo number_format($val["lc_value"],2); ?></td>
                    <td width="90" align="right" title="<?= chop($val["pi_id"],",");?>"><? echo number_format($rcv_val,2);?></td>
                    <td width="90" align="right"><? echo number_format($rcv_rtn_val,2);?></td>
					<td width="90" align="right"><? echo number_format($rcv_balance,2);?></td>
                    <td align="right"><? echo number_format($balance_val,2); ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?
    $html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			 @unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php 
}
if($action=="show_details_export_lc_closing")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=163)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?
		if($check_only_full=='true')
		{
			$closing_status=0;
		}
		else $closing_status=1;

		if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
		$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
		$invoice_value_arr=return_library_array( "select lc_sc_id,sum(invoice_value) as invoice_value from com_export_invoice_ship_mst where is_deleted=0 and status_active=1 and benificiary_id=$company  group by lc_sc_id","lc_sc_id", "invoice_value");

	 	$result= sql_select("select a.id,lc_date as requ_and_wo_date,a.export_lc_system_id,a.buyer_name,a.tolerance,SUM(lc_value) as lc_value,a.export_lc_no as requ_and_wo_no ,a.expiry_date as lc_expiry_date from com_export_lc a where a.export_item_category in(1,2,3,4,10,11,20,21,22,23,24,30,31,35,36,37,40,45,46,47,48,49,50,51,55,60,65,66) and a.beneficiary_name=$company and a.is_deleted=0 and a.status_active=1 and a.ref_closing_status=$only_full $wo_date_cond group by a.id,a.lc_date,a.export_lc_system_id,a.buyer_name,a.tolerance,a.export_lc_no,a.expiry_date");
					
	ob_start(); 
	$contents='';
	?>
    <div style="width:1050px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1050" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
            	<?php
            	$contents.= ob_get_flush();
				//ob_start();
            	?>
				<th colspan="12" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onClick="check_all_data();"/>
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                </th>
                <?php
            	//$contents.= ob_get_flush();
				ob_start();
            	?>
                <th width="40">SL</th>
                <th width="120">Export LC No</th>
                <th width="70">Export LC Date</th>
                <th width="70">Buyer</th>
                <th width="110">Age</th>
                <th width="110">Tolerance % </th>
                <th width="110">LC Value</th>
                <th width="90">Invoice Value</th>
                <th width="90">LC Exp. Date</th>
                <th width="90">Balance Value </th>
                <th width="">Value %</th>
               
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1050px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1030">
    	<tbody>
        	<?
			$i=1;
				$k=1;
				foreach($result as $row)
				{
					if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$all_job_id=array_unique(explode(",",$row[csf("job_id")]));
					$job_qnty=0;
					foreach($all_job_id as $job_id)
					{
						$job_qnty+=$job_qnty_arr[$job_id];
					}
				
					
				
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $row[csf("id")].'**'.$row[csf("export_lc_system_id")];  ?>"/>
					</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td style="word-break:break-all" width="40" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="120" title="<? ?>"><p><? echo $row[csf("export_lc_system_id")]; ?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="70" align="center"><p><? echo  change_date_format($row[csf("requ_and_wo_date")]);?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="70" align="center"><p><?  echo $lib_buyer_arr[$row[csf("buyer_name")]];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="110" align="center"><p><? 
					
					$birth_date=change_date_format($row[csf('requ_and_wo_date')]);
				$age = my_old($birth_date);
				if($age[year]>0 )
				{
					printf("%d years, %d months, %d days\n", $age[year], $age[mnt], $age[day]);
				}
				else
				{
					printf("%d months, %d days\n", $age[mnt], $age[day]);
				};?>&nbsp;</p></td>
					<td style="word-break:break-all" width="110"><p><?
					
					echo $row[csf("tolerance")];
					?>
					&nbsp;</p></td>
					<td style="word-break:break-all" width="110" align="center"><p><? echo number_format($row[csf("lc_value")],2);
						$invoce_valuee=$invoice_value_arr[$row[csf("id")]];;?>&nbsp;</p></td>
					<td width="90" align="right"><? echo  number_format($invoice_value_arr[$row[csf("id")]],2); ?></td>
                    <td width="90" align="right"><? echo change_date_format($row[csf("lc_expiry_date")]);?></td> 
                    <td width="90" align="right"><? echo number_format($row[csf("lc_value")]-$invoice_value_arr[$row[csf("id")]],2);?></td>
					
                    <td align="right"><? $balance_invoice_value=$row[csf("lc_value")]-$invoice_value_arr[$row[csf("id")]];
						$percent_value=($balance_invoice_value/$row[csf("lc_value")])*100;
							echo number_format($percent_value,2);  ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?

    $html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			 @unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php 
	
}
if($action=="show_details_knit_qc_sweater")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	//$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=370)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?

					//$wo_date_cond="";
					//if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.lc_date between ".$txt_date_from." and ".$txt_date_to."";
					$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
					
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.pub_shipment_date between ".$txt_date_from." and ".$txt_date_to."";
					$shipment_cond="";
					if($only_full==1) $shipment_cond .=" and  a.shiping_status=3";
					else $shipment_cond .=" and  a.shiping_status NOT IN (3) "; 


					$result_po_info=sql_select("select b.team_leader,a.id as po_id ,b.style_ref_no,b.order_uom,a.job_no_mst,a.po_number from wo_po_details_master b , wo_po_break_down a where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 and b.garments_nature=100 $wo_date_cond and b.company_name=$company $shipment_cond and a.job_no_mst in('SSL-22-00164','SSL-22-00165','SSL-22-00046') group by b.team_leader,a.id ,b.style_ref_no,b.order_uom,a.job_no_mst,a.po_number");
					$jobNos="";
					$poNos="";
					$jobNoArr=array();
					$poNosArr=array();
					foreach($result_po_info as $val)
					{
						$po_info_arr[$val[csf("po_id")]]['job_no']=$val[csf("job_no_mst")];
						if($jobNoArr[$val[csf("job_no_mst")]]!=$val[csf("job_no_mst")])
						{
							$jobNos.="'".$val[csf("job_no_mst")]."',";
							$jobNoArr[$val[csf("job_no_mst")]]=$val[csf("job_no_mst")];
						}

						if($poNosArr[$val[csf("po_id")]]!=$val[csf("po_id")])
						{
							$poNos.=$val[csf("po_id")].",";
							$poNosArr[$val[csf("po_id")]]=$val[csf("po_id")];
						}
					}
					$jobNos=chop($jobNos,",");
					$poNos=chop($poNos,",");
					unset($result_po_info);
					//print_r($jobNoArr);


					$sql_qc=sql_select("select a.production_quantity,a.po_break_down_id from pro_garments_production_mst a where a.garments_nature=100 and a.production_type=52 and a.status_active=1");
					foreach($sql_qc as $row)
					{
						//$po_qc_qty_arr[$row[csf("po_break_down_id")]]+=$row[csf("production_quantity")];
						$po_qc_qty_arr[$po_info_arr[$row[csf("po_break_down_id")]]['job_no']]+=$row[csf("production_quantity")];
					}
					unset($sql_qc);

					$sql_cut_lay=sql_select("select a.job_no,a.cut_num_prefix_no,sum(b.marker_qty) as marker_qty from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.entry_form=253 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no,a.cut_num_prefix_no");
					$cut_lay_info_arr=array();
					foreach($sql_cut_lay as $row)
					{
						$cut_lay_info_arr[$row[csf("job_no")]]['cut_lay_no'].=$row[csf("cut_num_prefix_no")].',';
						$cut_lay_info_arr[$row[csf("job_no")]]['marker_qty']+=$row[csf("marker_qty")];
					}
					unset($sql_cut_lay);
					//print_r($po_qc_qty_arr);
					
					
					$sql_sweter_issue=sql_select("select a.buyer_job_no,sum(b.cons_quantity) as issue_qnty from INV_ISSUE_MASTER a,inv_transaction b where a.id=b.mst_id and a.entry_form=277 and a.issue_purpose=1 and b.transaction_type=2 and b.item_category=1 and b.receive_basis=6 and a.issue_basis=6 and b.entry_form=277 and a.buyer_job_no in($jobNos) and b.job_no in($jobNos) group by a.buyer_job_no");
					$sweter_issue_info_arr=array();
					foreach($sql_sweter_issue as $row)
					{
						$sweter_issue_info_arr[$row[csf("buyer_job_no")]]['issue_qnty']+=$row[csf("issue_qnty")];
					}
					unset($sql_sweter_issue);
					//print_r($sweter_issue_info_arr);

					$sql_sweter_issue_return=sql_select("select b.job_no,sum(b.cons_quantity) as issue_return_qnty,sum(b.cons_reject_qnty) as reject_qnty,sum(b.weight_editable) as weight_editable from INV_RECEIVE_MASTER a,inv_transaction b where a.id=b.mst_id and a.entry_form=382  and b.transaction_type=4 and b.item_category=1 and a.receive_basis=6 and b.receive_basis=6 and b.entry_form=382 and b.job_no in($jobNos) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.job_no");
					$sweter_issue_rtn_info_arr=array();
					foreach($sql_sweter_issue_return as $row)
					{
						$sweter_issue_rtn_info_arr[$row[csf("job_no")]]['issue_return_qnty']+=$row[csf("issue_return_qnty")];
						$sweter_issue_rtn_info_arr[$row[csf("job_no")]]['reject_qnty']+=$row[csf("reject_qnty")];
						$sweter_issue_rtn_info_arr[$row[csf("job_no")]]['wastage_qnty']+=$row[csf("weight_editable")];
						
					}
					unset($sql_sweter_issue_return);
					//print_r($sweter_issue_rtn_info_arr);

					$sql_bundle_issue=sql_select("select b.po_break_down_id,sum(c.production_qnty) as production_qnty 
					from PRO_GMTS_DELIVERY_MST a, PRO_GARMENTS_PRODUCTION_MST b, PRO_GARMENTS_PRODUCTION_DTLS c 
					where a.id=b.delivery_mst_id and b.id=c.mst_id and a.id=c.delivery_mst_id 
					and a.entry_form=375 and c.production_type=76 and b.po_break_down_id in($poNos) 
					group by b.po_break_down_id");
					$sweter_bundle_issue_info_arr=array();
					foreach($sql_bundle_issue as $row)
					{
						$sweter_bundle_issue_info_arr[$po_info_arr[$row[csf("po_break_down_id")]]['job_no']]['bundle_issue_production']+=$row[csf("production_qnty")];
					}
					unset($sql_bundle_issue);
					//print_r($sweter_bundle_issue_info_arr);


					$result=sql_select("select b.team_leader,LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as po_id ,b.style_ref_no,b.order_uom,a.job_no_mst,LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.po_number) as po_number,sum(a.po_quantity) as po_quantity,a.shiping_status,min(a.pub_shipment_date) as pub_shipment_date,b.buyer_name from wo_po_details_master b , wo_po_break_down a where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 and b.garments_nature=100 $wo_date_cond and b.company_name=$company $shipment_cond and a.job_no_mst in('SSL-22-00164','SSL-22-00165','SSL-22-00046') group by b.team_leader, b.style_ref_no,b.order_uom,a.job_no_mst,a.shiping_status 
					,b.buyer_name ");

					//$result=sql_select("select b.team_leader, a.id as po_id,b.style_ref_no,b.order_uom,a.job_no_mst,a.po_number,a.po_quantity,a.shiping_status,a.pub_shipment_date ,b.buyer_name  from wo_po_details_master b , wo_po_break_down a where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 and b.garments_nature=100 $wo_date_cond and b.company_name=$company $shipment_cond and a.job_no_mst in('SSL-22-00164','SSL-22-00165') order by a.id asc");
					//SSL-22-00164
				
				//echo "select b.team_leader, a.id as po_id,b.style_ref_no,b.order_uom,a.job_no_mst,a.po_number,a.po_quantity,a.shiping_status,a.pub_shipment_date ,b.buyer_name  from wo_po_details_master b , wo_po_break_down a where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 and b.garments_nature=100 $wo_date_cond and b.company_name=$company $shipment_cond  order by a.id asc";
				//$result=sql_select("select b.team_leader, a.id as po_id,b.style_ref_no,b.order_uom,a.job_no_mst,a.po_number,a.po_quantity,a.shiping_status,a.pub_shipment_date ,b.buyer_name  from wo_po_details_master b , wo_po_break_down a,pro_garments_production_mst c where a.job_no_mst=b.job_no  and c.po_break_down_id=a.id and a.is_deleted=0 and a.status_active=1 and b.garments_nature=100 and  c.garments_nature=100 and c.production_type=52 $wo_date_cond and b.company_name=$company and c.ref_closing_status=$only_full   order by a.id asc");
				//echo "select b.team_leader, a.id as po_id,b.style_ref_no,b.order_uom,a.job_no_mst,a.po_number,a.po_quantity,a.shiping_status,a.pub_shipment_date ,b.buyer_name  from wo_po_details_master b , wo_po_break_down a,pro_garments_production_mst c where a.job_no_mst=b.job_no  and c.po_break_down_id=a.id and a.is_deleted=0 and a.status_active=1 and b.garments_nature=100 and  c.garments_nature=100 and c.production_type=52 $wo_date_cond and b.company_name=$company and c.ref_closing_status=$only_full order by a.id asc";
		if($check_only_full=='true')
		{
			$closing_status=0;
		}
		else $closing_status=1;

		ob_start(); 
		$contents='';
	?>
    <div style="width:2120px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="2100" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
            	<?php
            	$contents.= ob_get_flush();
				//ob_start();
            	?>
				<th colspan="13" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onClick="check_all_data();"/>
                &nbsp;
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                
                </th>
                <?php
            	//$contents.= ob_get_flush();
				ob_start();
            	?>
                <th width="40">SL</th>
                <th width="120">Buyer</th>
                <th width="100">Job Number</th>
                <th width="100">Style</th>
                <th width="100">Po No</th>
                <th width="100">Image</th>
                <th width="70">Pub. Ship. Date</th>
                <th width="90">Po Qty </th>

				<th width="100">Yarn Lot Ratio No.</th>
				<th width="100">Lot Ration Qty [Lbs]</th>
				<th width="100">Yarn Issue Qty [Lbs]</th>
				<th width="100">Issue Return Qty [Lbs]</th>
				<th width="100">Kntting Reject Yarn</th>
				<th width="100">Knit.  Qty [Pcs]</th>
				<th width="100">Kntting Reject Panel</th>
				<th width="100">Knit.  Bal. Qty [Pcs]</th>

                <th width="90">Knitting Qty</th>
                <th width="90">UOM</th>
                <th width="90">Knit. Bal. Qty</th>
				<th width="90">Loss %</th>
				<th width="90">Yarn W. Order Locked</th>
                <th>Team Leader</th>
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:2120px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="2100">
    	<tbody>
        	<?
			$imge_arr=return_library_array( "select master_tble_id,image_location from  common_photo_library where file_type=1 and form_name='knit_order_entry' ",'master_tble_id','image_location');
			$i=1;$k=1;
			foreach($result as $val)
			{
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $val[csf('po_id')].'**'.$val[csf("po_number")]; //change_date_format ?>"/>
					</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td style="word-break:break-all" width="40" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="120" title=""><p><? echo $lib_buyer_arr[$val[csf("buyer_name")]]; ?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="100" align="center"><p><? echo  $val[csf("job_no_mst")];?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="100" align="center"><p><? echo  $val[csf("style_ref_no")];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center" title="<?= $val[csf("po_number")];?>"><p><? echo  $val[csf("po_number")];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100"><p><?
					 if($imge_arr[$val[csf("job_no_mst")]]!="")
					{
					?>
					<img  src='../../<? echo $imge_arr[$val[csf("job_no_mst")]]; ?>' height='50' width='98' />
					<?
					}
					else "&nbsp;";
					?>
					&nbsp;</p></td>
					<td style="word-break:break-all" width="70" align="center"><p><? echo change_date_format($val[csf("pub_shipment_date")]);?>&nbsp;</p></td>
					<td width="90" align="right"><? echo number_format($val[csf("po_quantity")],2); ?></td>

					<td style="word-break:break-all;text-align:center;" width="100"><p><? echo chop($cut_lay_info_arr[$val[csf("job_no_mst")]]['cut_lay_no'],','); ?></p></td>
					<td width="100" align="right"><p><? echo chop($cut_lay_info_arr[$val[csf("job_no_mst")]]['marker_qty'],','); ?></p></td>
					<td width="100" align="right"><p><?
					
						echo number_format($sweter_issue_info_arr[$val[csf("job_no_mst")]]['issue_qnty'],2);
					
					
					
					?></p></td>
					<td width="100" align="right"><p><? echo number_format($sweter_issue_rtn_info_arr[$val[csf("job_no_mst")]]['issue_return_qnty'],2); ?></p></td>
					<td width="100" align="right"><p><? echo number_format($sweter_issue_rtn_info_arr[$val[csf("job_no_mst")]]['reject_qnty'],2); ?></p></td>
					
					<td width="100" align="right"><p><?

					echo number_format($sweter_bundle_issue_info_arr[$val[csf("job_no_mst")]]['bundle_issue_production'] ,2); ?></p></td>

					
					<td width="100" align="right"><p><? echo number_format($sweter_issue_rtn_info_arr[$val[csf("job_no_mst")]]['wastage_qnty'],2); ?></p></td>
					
					<td width="100" align="right"><p><? 
						$balanceProductionQty=$sweter_issue_info_arr[$val[csf("job_no_mst")]]['issue_qnty']-($sweter_issue_rtn_info_arr[$val[csf("job_no_mst")]]['issue_return_qnty']+$sweter_issue_rtn_info_arr[$val[csf("job_no_mst")]]['reject_qnty']+$sweter_bundle_issue_info_arr[$val[csf("job_no_mst")]]['bundle_issue_production']+$sweter_issue_rtn_info_arr[$val[csf("job_no_mst")]]['wastage_qnty']); 
						echo number_format($balanceProductionQty,2);
					?></p></td>



                    <td width="90" align="right" title=""><? echo number_format($po_qc_qty_arr[$val[csf("job_no_mst")]],2);?></td>
                    <td width="90" align="center"><? echo $unit_of_measurement[$val[csf("order_uom")]];?></td>
					<td width="90" align="right"><? 
						$knitBlnc= chop($cut_lay_info_arr[$val[csf("job_no_mst")]]['marker_qty'],',')-$sweter_bundle_issue_info_arr[$val[csf("job_no_mst")]]['bundle_issue_production'];
						echo number_format($knitBlnc,2);
						//number_format($val[csf("po_quantity")]-$po_qc_qty_arr[$val[csf("job_no_mst")]],2);
					?></td>
					<td width="90" align="right"><p><? echo number_format($knitBlnc,2); ?></p></td>
					<td width="90" align="right"><p></p></td>
                    <td><? echo  $team_leader_arr[$val[csf("team_leader")]]; ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?

    $html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			 @unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php 

}
if($action=="show_details_knit_closing")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo "test";die;
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	//$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");

	// echo $txt_job_no."__";die;
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=370)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?

		$wo_date_cond="";
		if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";
		$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
		$lib_company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
		$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
		//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
		
		if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";

		if($db_type==0) $year_field="YEAR(d.insert_date) as year"; 
		else if($db_type==2) $year_field="to_char(d.insert_date,'YYYY') as year";

		if($txt_job_no!=""){
			$sqljob=sql_select("SELECT b.id as prog_no  from  ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, wo_po_break_down c,wo_po_details_master d where  b.id=a.dtls_id and c.id=a.po_id and d.job_no=c.job_no_mst and b.status_active =1 and c.status_active =1 and a.status_active =1 and d.COMPANY_NAME=$company and d.job_no='$txt_job_no'");	
			foreach($sqljob as $row){
				$prog_id=$row["PROG_NO"].",";
			}
		    $prog_ids=' and c.id in('. rtrim($prog_id,",").')';
	    }

		$result_prog=sql_select("SELECT a.id as mst_id, a.recv_number, a.receive_date, a.buyer_id, b.id as dtls_id, b.grey_receive_qnty as grey_receive_qnty, b.reject_fabric_receive as reject_fabric_receive, b.order_id, c.id as prog_no, c.program_qnty as program_qnty, c.status, c.program_date, c.knitting_source as KNITTING_SOURCE, c.knitting_party as KNITTING_PARTY
		from inv_receive_master a , pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls c 
		where a.id=b.mst_id and c.id=a.booking_id and a.is_deleted=0 and a.status_active=1 and a.entry_form=2 and a.receive_basis=2 $wo_date_cond and a.company_id=$company and a.ref_closing_status=$only_full $prog_ids
		order by c.id asc");
		
		$prog_no="";
		foreach($result_prog as $row)
		{
			if($dtls_check[$row[csf('prog_no')]][$row[csf('dtls_id')]]=="")
			{
				$dtls_check[$row[csf('prog_no')]][$row[csf('dtls_id')]]=$row[csf('dtls_id')];
				$prog_dataArr[$row[csf('prog_no')]]['grey_receive_qnty']+=$row[csf('grey_receive_qnty')];
				$prog_dataArr[$row[csf('prog_no')]]['reject_fabric_receive']+=$row[csf('reject_fabric_receive')];
			}
			
			$prog_dataArr[$row[csf('prog_no')]]['program_date']=$row[csf('program_date')];
			$prog_dataArr[$row[csf('prog_no')]]['order_id']=$row[csf('order_id')];
			
			$prog_dataArr[$row[csf('prog_no')]]['status']=$row[csf('status')];
			$prog_dataArr[$row[csf('prog_no')]]['buyer_id']=$row[csf('buyer_id')];
			$prog_dataArr[$row[csf('prog_no')]]['recv_number']=$row[csf('recv_number')];
			$prog_dataArr[$row[csf('prog_no')]]['knitting_source']=$row['KNITTING_SOURCE'];
			$prog_dataArr[$row[csf('prog_no')]]['knitting_party']=$row['KNITTING_PARTY'];
			$prog_dataArr[$row[csf('prog_no')]]['mst_id'].=$row[csf('mst_id')].',';
			if($prog_check[$row[csf('prog_no')]]=="")
			{
				$prog_check[$row[csf('prog_no')]]=$row[csf('prog_no')];
				$prog_dataArr[$row[csf('prog_no')]]['program_qnty']=$row[csf('program_qnty')];
				if($prog_no=="") $prog_no=$row[csf('prog_no')];else $prog_no.=",".$row[csf('prog_no')];
			}
			
		}
		
		$poIds=chop($prog_no,','); 
		$po_ids=count(array_unique(explode(",",$prog_no)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			
		}
		else
		{
			$poIds=implode(",",(array_unique(explode(",",$poIds))));
			$po_cond_for_in=" and b.id in($poIds)";
			
		}
		if($db_type==0) $year_field="YEAR(d.insert_date) as year"; 
		else if($db_type==2) $year_field="to_char(d.insert_date,'YYYY') as year";

		$po_result = sql_select("SELECT c.po_number,c.id as po_id,b.id as prog_no,d.style_ref_no,d.job_no,c.file_no,a.po_id as sales_id,c.grouping as ref_no,$year_field from  ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, wo_po_break_down c,wo_po_details_master d where  b.id=a.dtls_id and c.id=a.po_id and d.job_no=c.job_no_mst and b.status_active =1 and c.status_active =1 and a.status_active =1 $po_cond_for_in");
		$sales_id="";
		foreach($po_result as $row)
		{
			$po_arr[$row[csf('prog_no')]]['po_number'].=$row[csf('po_number')].',';
			$po_arr[$row[csf('prog_no')]]['job_no'].=$row[csf('job_no')].',';
			$po_arr[$row[csf('prog_no')]]['style_ref_no'].=$row[csf('style_ref_no')].',';
			$po_arr[$row[csf('prog_no')]]['file_no'].=$row[csf('file_no')].',';
			$po_arr[$row[csf('prog_no')]]['ref_no'].=$row[csf('ref_no')].',';
			$po_arr[$row[csf('prog_no')]]['year'].=$row[csf('year')].',';

			if($sales_check[$row[csf('sales_id')]]=="")
			{
				$sales_check[$row[csf('sales_id')]]=$row[csf('sales_id')];
				if($sales_id=="") $sales_id=$row[csf('sales_id')];else $sales_id.=",".$row[csf('sales_id')];
			}
		}
		$salesIds=chop($sales_id,','); 
		$sales_ids=count(array_unique(explode(",",$sales_id)));
		if($db_type==2 && $sales_ids>1000)
		{
			$sales_cond_for_in=" and (";
			$salesIdsArr=array_chunk(explode(",",$salesIds),999);
			foreach($salesIdsArr as $idss)
			{
				$idss=implode(",",$idss);
				$sales_cond_for_in.=" a.id in($idss) or"; 
			}
			$sales_cond_for_in=chop($sales_cond_for_in,'or ');
			$sales_cond_for_in.=")";
		}
		else
		{
			$salesIds=implode(",",(array_unique(explode(",",$salesIds))));
			$sales_cond_for_in=" and a.id in($salesIds)";
		}


		$req_sql = "SELECT a.booking_no,c.knit_id,c.prod_id,c.requisition_no, c.yarn_qnty
		from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c
		where a.id=b.mst_id and b.id=c.knit_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in";

		$req_result = sql_select($req_sql);

		$req_no="";
		foreach($req_result as $row)
		{
			if($req_no=="") $req_no=$row[csf('requisition_no')];else $req_no.=",".$row[csf('requisition_no')];
		}
		$ReqIds=chop($req_no,','); 
		$req_ids=count(array_unique(explode(",",$ReqIds)));
		if($db_type==2 && $req_ids>1000)
		{
			$req_cond_for_in=" and (";
			//$req_cond_for_in2=" and (";
			$ReqIdsArr=array_chunk(explode(",",$ReqIds),999);
			foreach($ReqIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$req_cond_for_in.=" b.requisition_no in($ids) or"; 
				//$req_cond_for_in2.=" a.booking_id in($ids) or"; 
			}
			$req_cond_for_in=chop($req_cond_for_in,'or ');
			$req_cond_for_in.=")";
			//$req_cond_for_in2=chop($req_cond_for_in2,'or ');
			//$req_cond_for_in2.=")";
		}
		else
		{
			$ReqIds=implode(",",(array_unique(explode(",",$ReqIds))));
			$req_cond_for_in=" and b.requisition_no in($ReqIds)";
			//$req_cond_for_in2=" and a.booking_id in($poIds)";
		}
			
		/*$yarn_issue="SELECT a.id as issue_id, c.knit_id as prog_no, b.requisition_no, b.cons_quantity, b.cons_reject_qnty 
		from inv_issue_master a,inv_transaction b, ppl_yarn_requisition_entry c 
		where a.id=b.mst_id and b.requisition_no=c.requisition_no and a.entry_form=3 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_cond_for_in 
		group by a.id,c.knit_id ,b.requisition_no, b.cons_quantity, b.cons_reject_qnty";
			
		$yarn_result = sql_select($yarn_issue);
		$issue_id="";
		foreach($yarn_result as $row)
		{
			if($issue_id=="") $issue_id=$row[csf('issue_id')];else $issue_id.=",".$row[csf('issue_id')];
			$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue']+=$row[csf('cons_quantity')];
			$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue_rej']+=$row[csf('cons_reject_qnty')];
		}*/
		 $yarn_issue="SELECT a.id as issue_id, c.knit_id as prog_no, b.requisition_no, b.cons_quantity, b.cons_reject_qnty 
		from inv_issue_master a,inv_transaction b, ppl_yarn_requisition_entry c 
		where a.id=b.mst_id and b.requisition_no=c.requisition_no and c.prod_id=b.prod_id and a.entry_form=3  and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_cond_for_in";
			
		$yarn_result = sql_select($yarn_issue);
		$issue_id="";
		foreach($yarn_result as $row)
		{
			if($issue_id=="") $issue_id=$row[csf('issue_id')];else $issue_id.=",".$row[csf('issue_id')];
			$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue']+=$row[csf('cons_quantity')];
			$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue_rej']+=$row[csf('cons_reject_qnty')];
		}
		
		$IssueIds=chop($issue_id,','); 
		$issue_ids=count(array_unique(explode(",",$IssueIds)));
		if($db_type==2 && $issue_ids>1000)
		{
			$issue_cond_for_in=" and (";
			//$req_cond_for_in2=" and (";
			$IssueIdsArr=array_chunk(explode(",",$IssueIds),999);
			foreach($IssueIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$issue_cond_for_in.=" a.issue_id in($ids) or"; 
				//$req_cond_for_in2.=" a.booking_id in($ids) or"; 
			}
			$issue_cond_for_in=chop($issue_cond_for_in,'or ');
			$issue_cond_for_in.=")";
			//$req_cond_for_in2=chop($req_cond_for_in2,'or ');
			//$req_cond_for_in2.=")";
		}
		else
		{
			$IssueIds=implode(",",(array_unique(explode(",",$IssueIds))));
			$issue_cond_for_in=" and a.issue_id in($IssueIds)";
			//$req_cond_for_in2=" and a.booking_id in($poIds)";
		}
			
				
		$yarn_issue_ret="SELECT a.id as id, a.issue_id as issue_id, c.knit_id as prog_no, b.requisition_no as requisition_no, b.cons_quantity as cons_quantity, b.cons_reject_qnty as cons_reject_qnty
		from inv_receive_master a, inv_transaction b, ppl_yarn_requisition_entry c 
		where a.id=b.mst_id and a.booking_id=c.requisition_no  and a.entry_form=9 and b.transaction_type=4 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $issue_cond_for_in 
		group by a.id,a.issue_id,c.knit_id,b.requisition_no,b.cons_quantity,b.cons_reject_qnty
		union all
		SELECT a.id as id, a.issue_id as issue_id, c.knit_id as prog_no, b.requisition_no as requisition_no, b.cons_quantity as cons_quantity, b.cons_reject_qnty as cons_reject_qnty
		from inv_receive_master a, inv_transaction b, ppl_yarn_requisition_entry c 
		where a.id=b.mst_id and a.requisition_no=c.requisition_no  and a.entry_form=9 and b.transaction_type=4 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $issue_cond_for_in 
		group by a.id,a.issue_id,c.knit_id,b.requisition_no,b.cons_quantity,b.cons_reject_qnty";
		$yarn_ret_result = sql_select($yarn_issue_ret);
		foreach($yarn_ret_result as $row)
		{
			//if($req_no=="") $req_no=$row[csf('requisition_no')];else $req_no.=",".$row[csf('requisition_no')];
			$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue_ret']+=$row[csf('cons_quantity')];
			$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue_rej_ret']+=$row[csf('cons_reject_qnty')];
		}
			
			
		if($check_only_full=='true')
		{
			$closing_status=0;
		}
		else $closing_status=1;

	ob_start(); 
	$contents='';
	?>
    <div style="width:1830px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1830" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
            	<?php
            	$contents.= ob_get_flush();
				//ob_start();
            	?>
				<th colspan="22" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onClick="check_all_data();"/>
                &nbsp;
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                
                </th>
                <?php
            	//$contents.= ob_get_flush();
				ob_start();
            	?>
                <th width="20">SL</th>
                <th width="100">Program No</th>
                <th width="100">Program Date</th>
                <th width="100">Knitting Party</th>
                <th width="100">Buyer</th>
                <th width="100">Job Year</th>
               
                <th width="100">Job No</th>
                <th width="100">Style Ref</th>
                <th width="90">Order No</th>
                <th width="90">File No</th>
                <th width="90">Int. Ref.</th>
                <th width="90">Prog. Qty.</th>
                <th width="90">Yarn Issue Qnty</th>
                <th width="90">Issue Return Qnty</th>
                <th width="90">Knitting Qnty</th>
                <th width="90">Reject Fabric Qnty</th>
                <th width="90">Knitting Balance</th>
                <th width="90">Process Loss Qty</th>
                <th width="">Knitting Status</th>
               
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1830px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1810">
    	<tbody>
        	<?
			
			$i=1;$k=1;
			foreach($prog_dataArr as $progNo=>$val)
			{
				
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
					$po_number=rtrim($po_arr[$progNo]['po_number'],',');
					$po_number=implode(',',array_unique(explode(',',$po_number)));
					$style_ref_no=rtrim($po_arr[$progNo]['style_ref_no'],',');
					$style_ref_no=implode(',',array_unique(explode(',',$style_ref_no)));
					$file_no=rtrim($po_arr[$progNo]['file_no'],',');
					$file_no=implode(',',array_unique(explode(',',$file_no)));
					$ref_no=rtrim($po_arr[$progNo]['ref_no'],',');
					$ref_no=implode(',',array_unique(explode(',',$ref_no)));
					$job_year=rtrim($po_arr[$progNo]['year'],',');
					$job_year=implode(',',array_unique(explode(',',$job_year)));
					$job_no=rtrim($po_arr[$progNo]['job_no'],',');
					$job_no=implode(',',array_unique(explode(',',$job_no)));
					
					$mst_ids=rtrim($val[("mst_id")],',');
					$mst_ids=implode(',',array_unique(explode(',',$mst_ids)));
					
					//$yarn_qty_ret=$yarn_qty_arr[$progNo]['yarn_issue_ret'];
					//echo $yarn_qty_ret.'dd';
					$knitting_source=$val[("knitting_source")];
					$knitting_party="";
					if($knitting_source==1){ $knitting_party=$lib_company_arr[$val[("knitting_party")]]; }
					else if($knitting_source==3){ $knitting_party=$lib_supplier_arr[$val[("knitting_party")]]; }
					
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $progNo.'**'.$mst_ids; //change_date_format ?>"/>
					</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td style="word-break:break-all" width="20" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="100" title="<? echo $val[("recv_number")];?>"><p><? echo $progNo;?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="100" align="center"><p><? echo  change_date_format($val[("program_date")]);?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="100"><p><? echo $knitting_party;?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="100" align="center"><p><? echo  $lib_buyer_arr[$val[("buyer_id")]]; ;?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center" title=""><p><? echo  $job_year;?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100"><div style="word-break:break-all"><? echo $job_no;?>	</div></td>
					<td style="word-break:break-all" width="100" align="center"><div style="word-break:break-all"><? echo $style_ref_no;?></div></td>
					<td width="90" align="center"><div style="word-break:break-all"><? echo $po_number; ?></div></td>
                    <td width="90" align="center" title=""><? echo $file_no;?></td>
                    <td width="90" align="center"><? echo $ref_no;?></td>
					<td width="90" align="right"><? echo number_format($val[("program_qnty")],2);
					$total_prog += $val[("program_qnty")];
					?></td>
                    <td width="90" align="right"><? echo number_format($yarn_qty_arr[$progNo]['yarn_issue'],2);
					$total_issue_qty +=$yarn_qty_arr[$progNo]['yarn_issue'];
					?></td>
                    <td width="90" align="right"><? echo number_format($yarn_qty_arr[$progNo]['yarn_issue_ret'],2);
					$total_ret_issue += $yarn_qty_arr[$progNo]['yarn_issue_ret'];
					?></td>
                    <td width="90" align="right" title="Knitting Qty"><? echo number_format($val[("grey_receive_qnty")],2);
					$total_knitting +=$val[("grey_receive_qnty")]; ?></td>
                    <td width="90" align="right"  title="Knitting Reject Qty"><? echo number_format($val[("reject_fabric_receive")],2);
					$total_reject += $val[("reject_fabric_receive")];
					?></td>
                    <td width="90" align="right"><? echo number_format($val[("program_qnty")]-($val[("grey_receive_qnty")]+$val[("reject_fabric_receive")]),2);
					$knitting_balance=$val[("program_qnty")]-($val[("grey_receive_qnty")]+$val[("reject_fabric_receive")]);
					$total_knit_blnc += $knitting_balance;
					?></td>
                    
                    <td width="90" align="right" title="Process Loss Qty Formula: [Yarn Issue qty-(Issue Return Qnty+Knitting Qnty+Reject Fabric Qnty)]"><? echo number_format($yarn_qty_arr[$progNo]['yarn_issue']-($yarn_qty_arr[$progNo]['yarn_issue_ret']+$val[("grey_receive_qnty")]+$val[("reject_fabric_receive")]),2);
					$process_loss = $yarn_qty_arr[$progNo]['yarn_issue']-($yarn_qty_arr[$progNo]['yarn_issue_ret']+$val[("grey_receive_qnty")]+$val[("reject_fabric_receive")]);
					$total_process_loss += $process_loss;
					?></td>

                    <td><? echo  $knitting_program_status[$val[("status")]]; ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
				<tr>
					<td style="word-break:break-all" width="30" align="center"></td>
					<?php
					ob_start();
	            	?>
                    <td width="" colspan="11" align="right"><p><strong>Total:</strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_prog,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_issue_qty,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_ret_issue,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_knitting ,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_reject,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_knit_blnc,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_process_loss,2);?></strong></p></td>
					<td><p></p></td>
				</tr>
        </tbody>
    </table>
    </div>
    <?

    $html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			 @unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php 

}
if($action=="show_details_knit_closing_fso")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo "test";die;
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	//$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=370)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?

		$wo_date_cond="";
		if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";
		$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
		$lib_company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
		$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
		//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
		
		if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";

		$result_prog=sql_select("SELECT a.id as mst_id, a.recv_number, a.receive_date, a.buyer_id, b.id as dtls_id, b.grey_receive_qnty as grey_receive_qnty, b.reject_fabric_receive as reject_fabric_receive, b.order_id, c.id as prog_no, c.program_qnty as program_qnty, c.status, c.program_date, c.knitting_source as KNITTING_SOURCE, c.knitting_party as KNITTING_PARTY
		from inv_receive_master a , pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls c 
		where a.id=b.mst_id and c.id=a.booking_id and a.is_deleted=0 and a.status_active=1 and a.entry_form=2 and a.receive_basis=2 $wo_date_cond and a.company_id=$company and a.ref_closing_status=$only_full  and c.is_sales=1  
		order by c.id asc");
		//and c.id in(4128,3273,1874)
		//and c.id in(305,306)
		
		$prog_no="";
		foreach($result_prog as $row)
		{
			if($dtls_check[$row[csf('prog_no')]][$row[csf('dtls_id')]]=="")
			{
				$dtls_check[$row[csf('prog_no')]][$row[csf('dtls_id')]]=$row[csf('dtls_id')];
				$prog_dataArr[$row[csf('prog_no')]]['grey_receive_qnty']+=$row[csf('grey_receive_qnty')];
				$prog_dataArr[$row[csf('prog_no')]]['reject_fabric_receive']+=$row[csf('reject_fabric_receive')];
			}
			
			$prog_dataArr[$row[csf('prog_no')]]['program_date']=$row[csf('program_date')];
			$prog_dataArr[$row[csf('prog_no')]]['order_id']=$row[csf('order_id')];
			
			$prog_dataArr[$row[csf('prog_no')]]['status']=$row[csf('status')];
			$prog_dataArr[$row[csf('prog_no')]]['buyer_id']=$row[csf('buyer_id')];
			$prog_dataArr[$row[csf('prog_no')]]['recv_number']=$row[csf('recv_number')];
			$prog_dataArr[$row[csf('prog_no')]]['knitting_source']=$row['KNITTING_SOURCE'];
			$prog_dataArr[$row[csf('prog_no')]]['knitting_party']=$row['KNITTING_PARTY'];
			$prog_dataArr[$row[csf('prog_no')]]['mst_id'].=$row[csf('mst_id')].',';
			if($prog_check[$row[csf('prog_no')]]=="")
			{
				$prog_check[$row[csf('prog_no')]]=$row[csf('prog_no')];
				$prog_dataArr[$row[csf('prog_no')]]['program_qnty']=$row[csf('program_qnty')];
				if($prog_no=="") $prog_no=$row[csf('prog_no')];else $prog_no.=",".$row[csf('prog_no')];
			}
			
		}
		unset($result_prog);
		
		$poIds=chop($prog_no,','); 
		$po_ids=count(array_unique(explode(",",$prog_no)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$po_cond_for_in3=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.id in($ids) or"; 
				$po_cond_for_in2.=" b.dtls_id in($ids) or"; 
				$po_cond_for_in3.=" a.booking_no in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in3=chop($po_cond_for_in3,'or ');
			$po_cond_for_in.=")";
			$po_cond_for_in2.=")";
			$po_cond_for_in3.=")";
		}
		else
		{
			$poIds=implode(",",(array_unique(explode(",",$poIds))));
			$po_cond_for_in=" and b.id in($poIds)";
			$po_cond_for_in2=" and b.dtls_id in($poIds)";
			$po_cond_for_in3=" and a.booking_no in($poIds)";
		}
		if($db_type==0) $year_field="YEAR(d.insert_date) as year"; 
		else if($db_type==2) $year_field="to_char(d.insert_date,'YYYY') as year";

		$po_result = sql_select("SELECT b.id as prog_no,a.po_id as sales_id from  ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b where  b.id=a.dtls_id and b.status_active =1  and a.status_active =1 $po_cond_for_in");

		//$po_result = sql_select("SELECT c.po_number,c.id as po_id,b.id as prog_no,d.style_ref_no,d.job_no,c.file_no,a.po_id as sales_id,c.grouping as ref_no,$year_field from  ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, wo_po_break_down c,wo_po_details_master d where  b.id=a.dtls_id and c.id=a.po_id and d.job_no=c.job_no_mst and b.status_active =1 and c.status_active =1 and a.status_active =1 $po_cond_for_in");

		$sales_id="";
		foreach($po_result as $row)
		{
			//$po_arr[$row[csf('prog_no')]]['po_number'].=$row[csf('po_number')].',';
			//$po_arr[$row[csf('prog_no')]]['job_no'].=$row[csf('job_no')].',';
			//$po_arr[$row[csf('prog_no')]]['style_ref_no'].=$row[csf('style_ref_no')].',';
			//$po_arr[$row[csf('prog_no')]]['file_no'].=$row[csf('file_no')].',';
			//$po_arr[$row[csf('prog_no')]]['ref_no'].=$row[csf('ref_no')].',';
			$po_arr[$row[csf('prog_no')]]['year'].=$row[csf('year')].',';

			if($sales_check[$row[csf('sales_id')]]=="")
			{
				$sales_check[$row[csf('sales_id')]]=$row[csf('sales_id')];
				if($sales_id=="") $sales_id=$row[csf('sales_id')];else $sales_id.=",".$row[csf('sales_id')];
			}
		}
		unset($po_result);
		$salesIds=chop($sales_id,','); 
		$sales_ids=count(array_unique(explode(",",$sales_id)));
		if($db_type==2 && $sales_ids>1000)
		{
			$sales_cond_for_in=" and (";
			$salesIdsArr=array_chunk(explode(",",$salesIds),999);
			foreach($salesIdsArr as $idss)
			{
				$idss=implode(",",$idss);
				$sales_cond_for_in.=" a.id in($idss) or"; 
			}
			$sales_cond_for_in=chop($sales_cond_for_in,'or ');
			$sales_cond_for_in.=")";
		}
		else
		{
			$salesIds=implode(",",(array_unique(explode(",",$salesIds))));
			$sales_cond_for_in=" and a.id in($salesIds)";
		}
		if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";


		$sales_sql ="select a.id as sales_id,a.job_no,a.sales_booking_no,b.dtls_id as prog,a.style_ref_no,$year_field,a.customer_buyer from ppl_planning_entry_plan_dtls b, fabric_sales_order_mst a  where b.po_id=a.id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $sales_cond_for_in $po_cond_for_in2 ";
		//and b.dtls_id in(305,306)
		$sales_result = sql_select($sales_sql);
		foreach($sales_result as $row)
		{
			$salesInfoArr[$row[csf('prog')]]["sales_no"]=$row[csf('job_no')];
			$salesInfoArr[$row[csf('prog')]]["sales_booking_no"]=$row[csf('sales_booking_no')];
			$salesInfoArr[$row[csf('prog')]]["year"]=$row[csf('year')];
			$salesInfoArr[$row[csf('prog')]]["style_ref_no"]=$row[csf('style_ref_no')];
			$salesInfoArr[$row[csf('prog')]]["customer_buyer"]=$row[csf('customer_buyer')];
		}
		unset($sales_result);
		//print_r($salesInfoArr);


		//for requisition information
		
		$reqsDatas = sql_select("select c.knit_id, max(c.requisition_no) as reqs_no, LISTAGG(c.prod_id, ',') WITHIN GROUP (ORDER BY c.prod_id) as prod_id from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c
		where a.id=b.mst_id and b.id=c.knit_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in group by c.knit_id,c.requisition_no");
		$reqsDataArr = array();
		foreach ($reqsDatas as $row)
		{
		   $reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
		   $reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
		}
		//print_r($reqsDataArr);
		unset($reqsDatas);

		$deliveryquantityArr = sql_select("SELECT a.booking_no,a.barcode_no,a.qnty as current_delivery 
		from  pro_roll_details a, pro_grey_prod_delivery_dtls b 
		where  a.entry_form in(2,56) and a.booking_without_order=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no=b.barcode_num and b.grey_sys_id=a.mst_id and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3
		group by a.booking_no,a.barcode_no,a.qnty");

		$deliveryStorQtyArr = array();
		foreach ($deliveryquantityArr as $row)
		{
			$deliveryStorQtyArr[$row[csf('booking_no')]] += $row[csf('current_delivery')];
			//$deliveryStorNoOfRollArr[$row[csf('booking_no')]] += $row[csf('roll_no_delv')];
		}
		unset($deliveryquantityArr);
		$req_sql = "SELECT a.booking_no,c.knit_id,c.prod_id,c.requisition_no, c.yarn_qnty
		from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c
		where a.id=b.mst_id and b.id=c.knit_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in";

		$req_result = sql_select($req_sql);

		$req_no="";
		foreach($req_result as $row)
		{
			if($req_no=="") $req_no=$row[csf('requisition_no')];else $req_no.=",".$row[csf('requisition_no')];
		}
		unset($req_result);
		$ReqIds=chop($req_no,','); 
		$req_ids=count(array_unique(explode(",",$ReqIds)));
		if($db_type==2 && $req_ids>1000)
		{
			$req_cond_for_in=" and (";
			//$req_cond_for_in2=" and (";
			$ReqIdsArr=array_chunk(explode(",",$ReqIds),999);
			foreach($ReqIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$req_cond_for_in.=" b.requisition_no in($ids) or"; 
				//$req_cond_for_in2.=" a.booking_id in($ids) or"; 
			}
			$req_cond_for_in=chop($req_cond_for_in,'or ');
			$req_cond_for_in.=")";
			//$req_cond_for_in2=chop($req_cond_for_in2,'or ');
			//$req_cond_for_in2.=")";
		}
		else
		{
			$ReqIds=implode(",",(array_unique(explode(",",$ReqIds))));
			$req_cond_for_in=" and b.requisition_no in($ReqIds)";
			//$req_cond_for_in2=" and a.booking_id in($poIds)";
		}
			
		/*$yarn_issue="SELECT a.id as issue_id, c.knit_id as prog_no, b.requisition_no, b.cons_quantity, b.cons_reject_qnty 
		from inv_issue_master a,inv_transaction b, ppl_yarn_requisition_entry c 
		where a.id=b.mst_id and b.requisition_no=c.requisition_no and a.entry_form=3 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_cond_for_in 
		group by a.id,c.knit_id ,b.requisition_no, b.cons_quantity, b.cons_reject_qnty";
			
		$yarn_result = sql_select($yarn_issue);
		$issue_id="";
		foreach($yarn_result as $row)
		{
			if($issue_id=="") $issue_id=$row[csf('issue_id')];else $issue_id.=",".$row[csf('issue_id')];
			$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue']+=$row[csf('cons_quantity')];
			$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue_rej']+=$row[csf('cons_reject_qnty')];
		}*/
		 $yarn_issue="SELECT a.id as issue_id, c.knit_id as prog_no, b.requisition_no, b.cons_quantity, b.cons_reject_qnty,b.prod_id  
		from inv_issue_master a,inv_transaction b, ppl_yarn_requisition_entry c 
		where a.id=b.mst_id and b.requisition_no=c.requisition_no and c.prod_id=b.prod_id and a.entry_form=3  and b.transaction_type=2 and a.status_active=1 and b.receive_basis in(3,8) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_cond_for_in";
			
		$yarn_result = sql_select($yarn_issue);
		$issue_id="";
		foreach($yarn_result as $row)
		{
			if($issue_id=="") $issue_id=$row[csf('issue_id')];else $issue_id.=",".$row[csf('issue_id')];
			//$yarn_qty_arr1[$row[csf('prog_no')]]['yarn_issue']+=$row[csf('cons_quantity')];
			$yarn_qty_arr1[$row[csf('requisition_no')]][$row[csf('prod_id')]]['yarn_issue']+=$row[csf('cons_quantity')];
			//$yarn_qty_arr1[$row[csf('prog_no')]]['yarn_issue_rej']+=$row[csf('cons_reject_qnty')];
		}
		unset($yarn_result);

		/* $yarnIssueData = sql_select("select a.requisition_no, a.prod_id, sum(a.cons_quantity) as qnty from inv_transaction a,tmp_reqs_no b where a.requisition_no=b.reqs_no and b.userid=$user_name and a.item_category=1 and a.transaction_type=2 and a.receive_basis in(3,8) and a.status_active=1 and a.is_deleted=0  group by a.requisition_no, a.prod_id");
			foreach ($yarnIssueData as $row)
			{
			   $yarn_iss_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]] = $row[csf('qnty')];
			}*/
		
		$IssueIds=chop($issue_id,','); 
		$issue_ids=count(array_unique(explode(",",$IssueIds)));
		if($db_type==2 && $issue_ids>1000)
		{
			$issue_cond_for_in=" and (";
			//$req_cond_for_in2=" and (";
			$IssueIdsArr=array_chunk(explode(",",$IssueIds),999);
			foreach($IssueIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$issue_cond_for_in.=" a.issue_id in($ids) or"; 
				//$req_cond_for_in2.=" a.booking_id in($ids) or"; 
			}
			$issue_cond_for_in=chop($issue_cond_for_in,'or ');
			$issue_cond_for_in.=")";
			//$req_cond_for_in2=chop($req_cond_for_in2,'or ');
			//$req_cond_for_in2.=")";
		}
		else
		{
			$IssueIds=implode(",",(array_unique(explode(",",$IssueIds))));
			$issue_cond_for_in=" and a.issue_id in($IssueIds)";
			//$req_cond_for_in2=" and a.booking_id in($poIds)";
		}
			
				
		$yarn_issue_ret="SELECT a.booking_id AS reqsn_no,b.prod_id,SUM (b.cons_quantity) AS qnty,SUM (b.cons_reject_qnty) AS reject_qnty
		from inv_receive_master a, inv_transaction b 
		where a.id=b.mst_id   and a.entry_form=9 and b.transaction_type=4 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 AND b.receive_basis IN (3) AND b.transaction_type = 4 $issue_cond_for_in 
		group by a.booking_id, b.prod_id
		union all
		SELECT a.requisition_no AS reqsn_no,b.prod_id,SUM (b.cons_quantity) AS qnty,SUM (b.cons_reject_qnty) AS reject_qnty
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id AND b.receive_basis IN (8) and b.transaction_type=4   and a.entry_form=9 and b.transaction_type=4 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $issue_cond_for_in 
		group by a.requisition_no, b.prod_id";



		$yarn_ret_result = sql_select($yarn_issue_ret);
		foreach($yarn_ret_result as $row)
		{
			//if($req_no=="") $req_no=$row[csf('requisition_no')];else $req_no.=",".$row[csf('requisition_no')];
			//$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue_ret']+=$row[csf('cons_quantity')];
			$yarn_qty_arr[$row[csf('reqsn_no')]][$row[csf('prod_id')]]['yarn_issue_ret']=$row[csf('qnty')];
			//$yarn_IssRej_arr[$row[csf('reqsn_no')]][$row[csf('prod_id')]]['yarn_issue_rej_ret']=$row[csf('cons_reject_qnty')];
		}
		unset($yarn_ret_result);
		/*echo "<pre>";
		print_r($yarn_qty_arr);
		echo "</pre>";*/
			
			
		if($check_only_full=='true')
		{
			$closing_status=0;
		}
		else $closing_status=1;

	ob_start(); 
	$contents='';
	?>
    <div style="width:2040px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="2040" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
            	<?php
            	$contents.= ob_get_flush();
				//ob_start();
            	?>
				<th colspan="22" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onClick="check_all_data();"/>
                &nbsp;
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                
                </th>
                <?php
            	//$contents.= ob_get_flush();
				ob_start();
            	?>
                <th width="20">SL</th>
                <th width="100">Program No</th>
                <th width="100">Program Date</th>
                <th width="100">Knitting Party</th>
                <th width="100">Buyer</th>
				<th width="90">Cust. Buyer</th>
                <th width="100">FSO Year</th>
                <th width="150">FSO No</th>
                <th width="150">Booking No</th>
                
                <th width="90">Style Ref.</th>
                <th width="90">Prog. Qty.</th>
                <th width="90">Yarn Issue Qnty</th>
                <th width="90">Issue Return Qnty</th>
                <th width="90">Knitting Qnty</th>
                <th width="90">Reject Fabric Qnty</th>
                <th width="90">Knitting Balance</th>
                <th width="100">Delivery Store</th>
                <th width="90">Process Loss Qty</th>
                <th width="100">Process Loss %</th>
                <th width="">Knitting Status</th>
               
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:2040px;">
    <table id="tbl_list" align="left" class="rpt_table" rules="all" border="1" width="2020">
    	<tbody>
        	<?
			
			$i=1;$k=1;
			foreach($prog_dataArr as $progNo=>$val)
			{
				
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
					$po_number=rtrim($po_arr[$progNo]['po_number'],',');
					$po_number=implode(',',array_unique(explode(',',$po_number)));
					$style_ref_no=rtrim($po_arr[$progNo]['style_ref_no'],',');
					$style_ref_no=implode(',',array_unique(explode(',',$style_ref_no)));
					$file_no=rtrim($po_arr[$progNo]['file_no'],',');
					$file_no=implode(',',array_unique(explode(',',$file_no)));
					$ref_no=rtrim($po_arr[$progNo]['ref_no'],',');
					$ref_no=implode(',',array_unique(explode(',',$ref_no)));
					$job_year=rtrim($po_arr[$progNo]['year'],',');
					$job_year=implode(',',array_unique(explode(',',$job_year)));
					$job_no=rtrim($po_arr[$progNo]['job_no'],',');
					$job_no=implode(',',array_unique(explode(',',$job_no)));
					
					$mst_ids=rtrim($val[("mst_id")],',');
					$mst_ids=implode(',',array_unique(explode(',',$mst_ids)));
					
					//$yarn_qty_ret=$yarn_qty_arr[$progNo]['yarn_issue_ret'];
					//echo $yarn_qty_ret.'dd';
					$knitting_source=$val[("knitting_source")];
					$knitting_party="";
					if($knitting_source==1){ $knitting_party=$lib_company_arr[$val[("knitting_party")]]; }
					else if($knitting_source==3){ $knitting_party=$lib_supplier_arr[$val[("knitting_party")]]; }

					$yarn_issue_qnty =0;
					$prod_id = array_unique(explode(",", $reqsDataArr[$progNo]['prod_id']));
					foreach ($prod_id as $vals) 
                    {
                    	$yarnRtnQty = $yarn_qty_arr[$reqsDataArr[$progNo]['reqs_no']][$vals]['yarn_issue_ret'];
    					//$yarn_issue_reject_qnty += $yarn_IssRej_arr[$reqsDataArr[$progNo]['reqs_no']][$vals]['yarn_issue_rej_ret'];
    					//$yarn_qty_arr[$row[csf('reqsn_no')]][$row[csf('prod_id')]]['yarn_issue_ret'];


    					$yarn_issue_qnty+=$yarn_qty_arr1[$reqsDataArr[$progNo]['reqs_no']][$vals]['yarn_issue']-$yarnRtnQty;

    					//echo $yarn_qty_arr1[$reqsDataArr[$progNo]['reqs_no']][$vals]['yarn_issue']."-".$yarnRtnQty."<br/>";
                    }

					
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $progNo.'**'.$mst_ids; //change_date_format ?>"/>
					</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td style="word-break:break-all" width="20" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="100" title="<? echo $val[("recv_number")];?>"><p><? echo $progNo;?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="100" align="center"><p><? echo  change_date_format($val[("program_date")]);?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="100"><p><? echo $knitting_party;?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="100" align="center"><p><? echo  $lib_buyer_arr[$val[("buyer_id")]]; ;?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="90" align="center"><p><? echo  $lib_buyer_arr[$salesInfoArr[$progNo]["customer_buyer"]]; ;?>&nbsp;</p></td>

					<td style="word-break:break-all" width="100" align="center" title=""><p><? echo  $salesInfoArr[$progNo]["year"];?>&nbsp;</p></td>  
					<td style="word-break:break-all" width="150" align="center" title=""><p><? echo  $salesInfoArr[$progNo]["sales_no"];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="150" align="center" title=""><p><? echo  $salesInfoArr[$progNo]["sales_booking_no"];?>&nbsp;</p></td>
					
                   
                    <td width="90" align="center"><p><? echo $salesInfoArr[$progNo]["style_ref_no"];?></p></td>
					<td width="90" align="right"><? echo number_format($val[("program_qnty")],2);
					$total_prog += $val[("program_qnty")];
					?></td>
                    <td width="90" align="right"><? echo number_format($yarn_issue_qnty,2);
					$total_issue_qty +=$yarn_issue_qnty;
					?></td>
                    <td width="90" align="right"><? echo number_format($yarnRtnQty,2);
					$total_ret_issue += $yarnRtnQty;
					?></td>
                    <td width="90" align="right" title="Knitting Qty"><? echo number_format($val[("grey_receive_qnty")],2);
					$total_knitting +=$val[("grey_receive_qnty")];
					?></td>
                    <td width="90" align="right"  title="Knitting Reject Qty"><? echo number_format($val[("reject_fabric_receive")],2);
					$total_reject += $val[("reject_fabric_receive")];
					?></td>
                    <td width="90" align="right"><? echo number_format($val[("program_qnty")]-($val[("grey_receive_qnty")]+$val[("reject_fabric_receive")]),2);
					$knitting_balance=$val[("program_qnty")]-($val[("grey_receive_qnty")]+$val[("reject_fabric_receive")]);
					$total_knit_blnc += $knitting_balance;
					?></td>
                     <td width="100" align="right"><? echo number_format($deliveryStorQtyArr[$progNo],2);
					 $total_del_store +=$deliveryStorQtyArr[$progNo]; 
					 ?></td>
                 
                    
                    <td width="90" align="right" title="Process Loss Qty Formula: [Yarn Issue qty-(Issue Return Qnty+Delivery Qnty)]"><? echo number_format($yarn_issue_qnty-$deliveryStorQtyArr[$progNo],2);
					$process_loss =$yarn_issue_qnty-$deliveryStorQtyArr[$progNo];
					$total_process_loss += $process_loss;
					?></td>

                    <td width="90" align="right" title="Process Loss Qty % Formula: (Net Issue - Delivery Qty) / Delivery Qty X 100"><? 
                    $yarnIssueProduction=$yarn_issue_qnty-$deliveryStorQtyArr[$progNo];
                    echo  number_format($yarnIssueProduction/$deliveryStorQtyArr[$progNo]*100,2);
					$loss_per = $yarnIssueProduction/$deliveryStorQtyArr[$progNo]*100;
					$total_loss_per += $loss_per; 
					?></td>
                    <td><? echo  $knitting_program_status[$val[("status")]]; ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
			<tr>
					<td style="word-break:break-all" width="30" align="center"></td>
					<?php
					ob_start();
	            	?>
                    <td width="" colspan="10" align="right"><p><strong>Total:</strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_prog,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_issue_qty,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_ret_issue,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_knitting ,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_reject,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_knit_blnc,2);?></strong></p></td>
					<td width="100" align="right"><p><strong><? echo number_format($total_del_store,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_process_loss,2);?></strong></p></td>
					<td width="90" align="right"><p><strong><? echo number_format($total_loss_per,2);?></strong></p></td>
					<td><p></p></td>
				</tr>
        </tbody>
    </table>
    </div>
    <?

		$html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			@unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php 

}
if($action=="show_details_order_closing")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	//$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=370)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?

					$wo_date_cond="";
					if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";
					$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
					
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";
				$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					$order_qnty=return_library_array( "SELECT a.id, a.po_quantity*b.total_set_qnty as qnty  from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active in(1,2,3) and b.is_deleted=0 and b.status_active=1  ", "id", "qnty");
					$ex_fac_qnty=return_library_array( "SELECT po_break_down_id,sum(ex_factory_qnty) as qnty from pro_ex_factory_mst where is_deleted=0 and status_active =1 group by po_break_down_id", "po_break_down_id", "qnty");

					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.shipment_date between ".$txt_date_from." and ".$txt_date_to."";
					$shipment_cond="";
					if($only_full=='true') $shipment_cond .=" and  a.shiping_status=3";
					else $shipment_cond .=" and  a.shiping_status NOT IN (3) "; 

					$result=sql_select("SELECT b.team_leader, a.id,a.po_quantity*b.total_set_qnty as qnty a.job_no_mst, a.po_number as po_number, a.shiping_status, a.shipment_date as requ_and_wo_date, b.buyer_name, a.grouping from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 $wo_date_cond and b.company_name=$company $shipment_cond group by b.team_leader, a.id, a.job_no_mst, a.po_number, a.shiping_status, a.shipment_date, b.buyer_name, a.grouping order by a.id asc");
					//echo "SELECT b.team_leader, a.id, a.job_no_mst, a.po_number as requ_and_wo_no, a.shiping_status, a.shipment_date as requ_and_wo_date, b.buyer_name, a.grouping from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 $wo_date_cond and b.company_name=$company $shipment_cond group by b.team_leader, a.id, a.job_no_mst, a.po_number, a.shiping_status, a.shipment_date, b.buyer_name, a.grouping order by a.id asc";
				$po_id="";
				foreach($result_prog as $row)
				{
				$po_dataArr[$row[csf('id')]]['po_qnty']+=$row[csf('qnty')];
				$po_dataArr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_name')];
				$po_dataArr[$row[csf('id')]]['order_id']=$row[csf('id')];
				$po_dataArr[$row[csf('id')]]['job_no_mst']=$row[csf('job_no_mst')];
				$po_dataArr[$row[csf('id')]]['ref_no']=$row[csf('grouping')];
				$po_dataArr[$row[csf('id')]]['ship_date']=$row[csf('requ_and_wo_date')];
				$po_dataArr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$po_dataArr[$row[csf('id')]]['shiping_status']=$row[csf('shiping_status')];
				$po_dataArr[$row[csf('id')]]['team_leader']=$row[csf('team_leader')];
				if($po_id=="") $po_id=$row[csf('id')];else $po_id.=",".$row[csf('id')];
				}
				
				$poIds=chop($prog_no,','); 
				$po_ids=count(array_unique(explode(",",$prog_no)));
				if($db_type==2 && $po_ids>1000)
				{
				$po_cond_for_in=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
				}
				else
				{
				$po_cond_for_in=" and b.id in($poIds)";
				}
				if($db_type==0) $year_field="YEAR(d.insert_date) as year"; 
				else if($db_type==2) $year_field="to_char(d.insert_date,'YYYY') as year";
	/*
				$po_result = sql_select("select c.po_number,c.id as po_id,b.id as prog_no,d.style_ref_no,d.job_no,c.file_no,c.grouping as ref_no,$year_field from  ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, wo_po_break_down c,wo_po_details_master d where  b.id=a.dtls_id and c.id=a.po_id and d.job_no=c.job_no_mst and b.status_active =1 and c.status_active =1 and a.status_active =1 $po_cond_for_in");
				
				foreach($po_result as $row)
				{
					$po_arr[$row[csf('prog_no')]]['po_number'].=$row[csf('po_number')].',';
					$po_arr[$row[csf('prog_no')]]['job_no'].=$row[csf('job_no')].',';
					$po_arr[$row[csf('prog_no')]]['style_ref_no'].=$row[csf('style_ref_no')].',';
					$po_arr[$row[csf('prog_no')]]['file_no'].=$row[csf('file_no')].',';
					$po_arr[$row[csf('prog_no')]]['ref_no'].=$row[csf('ref_no')].',';
					$po_arr[$row[csf('prog_no')]]['year'].=$row[csf('year')].',';
				}
			*/

			

		if($check_only_full=='true')
		{
			$closing_status=0;
		}
		else $closing_status=1;
	ob_start();
	$contents='';
	?>
    <div style="width:1540px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1540" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
            	<?php
            	$contents.= ob_get_flush();
				//ob_start();
            	?>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onClick="check_all_data();"/>
                &nbsp;
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                
                </th>
                <?php
            	//$contents.= ob_get_flush();
				ob_start();
            	?>
                <th width="20">SL</th>
                <th width="100">Buyer</th>
                <th width="100"> Job No </th>
                <th width="100">Int. Ref no</th>
                <th width="100">PO No </th>
                <th width="100">Shipment Date </th>
                <th width="100">Po Qty </th>
                <th width="90">Ship Qty</th>
                <th width="90">Ship Bal. Qty</th>
                <th width="90">Shipping Status</th>
                <th width="">Team Leader</th>
               
               
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1540px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1520">
    	<tbody>
        	<?
			
			$i=1;$k=1;
			foreach($po_dataArr as $poId=>$val)
			{
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
					$po_number=rtrim($po_arr[$progNo]['po_number'],',');
					$po_number=implode(',',array_unique(explode(',',$po_number)));
					$style_ref_no=rtrim($po_arr[$progNo]['style_ref_no'],',');
					$style_ref_no=implode(',',array_unique(explode(',',$style_ref_no)));
					$file_no=rtrim($po_arr[$progNo]['file_no'],',');
					$file_no=implode(',',array_unique(explode(',',$file_no)));
					$ref_no=rtrim($po_arr[$progNo]['ref_no'],',');
					$ref_no=implode(',',array_unique(explode(',',$ref_no)));
					$job_year=rtrim($po_arr[$progNo]['year'],',');
					$job_year=implode(',',array_unique(explode(',',$job_year)));
					$job_no=rtrim($po_arr[$progNo]['job_no'],',');
					$job_no=implode(',',array_unique(explode(',',$job_no)));
					
					
					
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $poId.'**'.$val[("po_number")]; //change_date_format ?>"/>
					</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td style="word-break:break-all" width="20" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="100" title="<? echo $val[("recv_number")];?>"><p><? echo $lib_buyer_arr[$val[("buyer_id")]];?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="100" align="center"><p><? echo  $val[("job_no_mst")];?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="100" align="center"><p><? echo  $val[("ref_no")]; ;?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center" title=""><p><? echo  $val[("po_number")];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100"><p><? echo change_date_format($val[("program_date")]);?>
					&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center"><p><? echo change_date_format($val[("program_date")]);//$style_ref_no;?>&nbsp;</p></td>
					<td width="90" align="right"><? echo $po_number; ?></td>
                    <td width="90" align="right" title=""><? echo $file_no;?></td>
                    <td width="90" align="center"><? echo $ref_no;?></td>
				
                   
                    <td><? echo  $knitting_program_status[$val[("status")]]; ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?

    $html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			 @unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php 
}
if($action=="show_details_fabric_booking_closing")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	//$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=370)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?

					$wo_date_cond="";
				//	if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.booking_date between ".$txt_date_from." and ".$txt_date_to."";
					$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
					$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
					$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
					
					if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and a.booking_date between ".$txt_date_from." and ".$txt_date_to."";
				//$result_prog=sql_select("select a.id as mst_id,a.recv_number,a.receive_date,c.program_date,sum(b.grey_receive_qnty) as grey_receive_qnty,sum(b.reject_fabric_receive) as reject_fabric_receive,b.order_id,sum(c.program_qnty) as program_qnty,c.status,c.id as prog_no,a.buyer_id  from inv_receive_master a , pro_grey_prod_entry_dtls b,ppl_planning_info_entry_dtls c where a.id=b.mst_id and c.id=a.booking_id   and a.is_deleted=0 and a.status_active=1 and a.entry_form=2 and a.receive_basis=2 $wo_date_cond and a.company_id=$company and a.ref_closing_status=$only_full group by  a.id,a.recv_number,c.id,a.buyer_id,a.receive_date,c.program_date,b.order_id,c.status,c.id   order by c.id asc");
				
				$sql_booking="select a.id as booking_mst_id,a.pay_mode,a.booking_no,a.buyer_id,a.booking_type, a.supplier_id, a.delivery_date, a.booking_date,b.po_break_down_id ,b.job_no,b.amount,b.fin_fab_qnty,d.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and d.job_no=c.job_no_mst and d.job_no=b.job_no and b.po_break_down_id =c.id  and a.company_id=$company and a.ref_closing_status=$only_full and a.booking_type=1  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and b.fin_fab_qnty>0  $booking_no_cond $date_cond $booking_year_cond $wo_date_cond order by a.id ";
				$sql_booking_result=sql_select($sql_booking);
				foreach($sql_booking_result as $row)
				{
				$booking_dataArr[$row[csf('booking_no')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
				$booking_dataArr[$row[csf('booking_no')]]['amount']+=$row[csf('amount')];
				$booking_dataArr[$row[csf('booking_no')]]['booking_date']=$row[csf('booking_date')];

				$booking_dataArr[$row[csf('booking_no')]]['buyer_id']=$lib_buyer_arr[$row[csf('buyer_id')]];
				if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5)
				{
				$booking_dataArr[$row[csf('booking_no')]]['supplier_id']=$company_arr[$row[csf('supplier_id')]];
				}
				else
				{
				$booking_dataArr[$row[csf('booking_no')]]['supplier_id']=$lib_supplier_arr[$row[csf('supplier_id')]];
				}
				$booking_dataArr[$row[csf('booking_no')]]['style_ref_no']=$row[csf('style_ref_no')];
				$booking_dataArr[$row[csf('booking_no')]]['job_no']=$row[csf('job_no')];				
				$booking_dataArr[$row[csf('booking_no')]]['booking_mst_id']=$row[csf('booking_mst_id')];
				$booking_dataArr[$row[csf('booking_no')]]['booking_type']=$row[csf('booking_type')];
				//if($prog_no=="") $prog_no=$row[csf('prog_no')];else $prog_no.=",".$row[csf('prog_no')];
				}
				$samp_ql_booking="select a.id as booking_mst_id,a.pay_mode,a.booking_no,a.buyer_id,a.booking_type, a.supplier_id, a.delivery_date, a.booking_date,b.amount,b.finish_fabric as fin_fab_qnty  from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company and a.ref_closing_status=$only_full and a.booking_type=4  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and b.finish_fabric>0  $booking_no_cond $date_cond $booking_year_cond $wo_date_cond order by a.id";
				$samp_sql_booking_result=sql_select($samp_ql_booking);
				foreach($samp_sql_booking_result as $row)
				{
				$booking_dataArr[$row[csf('booking_no')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
				$booking_dataArr[$row[csf('booking_no')]]['amount']+=$row[csf('amount')];
				$booking_dataArr[$row[csf('booking_no')]]['booking_date']=$row[csf('booking_date')];

				$booking_dataArr[$row[csf('booking_no')]]['buyer_id']=$lib_buyer_arr[$row[csf('buyer_id')]];
				if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5)
				{
				$booking_dataArr[$row[csf('booking_no')]]['supplier_id']=$company_arr[$row[csf('supplier_id')]];
				}
				else
				{
				$booking_dataArr[$row[csf('booking_no')]]['supplier_id']=$lib_supplier_arr[$row[csf('supplier_id')]];

				}
				$booking_dataArr[$row[csf('booking_no')]]['style_ref_no']=$row[csf('style_ref_no')];
				$booking_dataArr[$row[csf('booking_no')]]['job_no']=$row[csf('job_no')];				
				$booking_dataArr[$row[csf('booking_no')]]['booking_mst_id']=$row[csf('booking_mst_id')];
				$booking_dataArr[$row[csf('booking_no')]]['booking_type']=$row[csf('booking_type')];
				//if($prog_no=="") $prog_no=$row[csf('prog_no')];else $prog_no.=",".$row[csf('prog_no')];
				}
				
				
			
		if($check_only_full=='true')
		{
			$closing_status=0;
		}
		else $closing_status=1;
	ob_start();
	$contents='';
	?>
    <div style="width:850px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="850" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
            	<?php
            	$contents.= ob_get_flush();
				//ob_start();
            	?>
				<th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onClick="check_all_data();"/>
                &nbsp;
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                
                </th>
                <?php
            	//$contents.= ob_get_flush();
				ob_start();
            	?>
                <th width="20">SL</th>
                <th width="110">Booking No</th>
                <th width="80">Booking Date</th>
                 <th width="110">Supplier</th>
                <th width="110">Buyer</th>
                <th width="110">Style Ref</th>
                <th width="110">Job No</th>
                <th width="80">Booking Qnty</th>
                <th width="">Booking Amount</th>
               
               
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:870px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="850">
    	<tbody>
        	<?
			
			$i=1;$k=1;
			foreach($booking_dataArr as $bookingNo=>$val)
			{
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
					
					
					$mst_ids=$val[("booking_mst_id")];
					$booking_type_id=$val[("booking_type")];
					//$mst_ids=implode(',',array_unique(explode(',',$mst_ids)));
					
					//$yarn_qty_ret=$yarn_qty_arr[$progNo]['yarn_issue_ret'];
					//echo $yarn_qty_ret.'dd';
					
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $mst_ids.'**'.$bookingNo.'**'.$booking_type_id; //change_date_format ?>"/>
					</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td style="word-break:break-all" width="20" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="110" title=""><p><? echo $bookingNo;?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="80" align="center"><p><? echo  change_date_format($val[("booking_date")]);?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="110" align="center"><p><? echo  $val[("supplier_id")] ;?>&nbsp;</p></td>
					<td style="word-break:break-all" width="110" align="center" title=""><p><? echo  $val[("buyer_id")];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="110"><div style="word-break:break-all"><? echo $val[("style_ref_no")];?>	</div></td>
					<td style="word-break:break-all" width="110" align="center"><div style="word-break:break-all"><? echo $val[("job_no")];;?></div></td>
					<td width="80" align="right"><? echo number_format($val[("fin_fab_qnty")],2);?></td>
                    <td width="" align="right"><? echo number_format($val[("amount")],2);?></td>
                    
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?

    $html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			 @unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php
	exit();
}

if($action=="show_details_yarn_service_closing")
{
	$process = array( &$_POST );
	//var_dump($process);
	extract(check_magic_quote_gpc( $process ));
	//echo "test";die;
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	
 	?>
 	<script>
	 //check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=370)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?
	$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
	$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
	$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$wo_date_cond="";
	if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and c.booking_date between ".$txt_date_from." and ".$txt_date_to."";
			

	$sql_main="SELECT c.id, c.ydw_no,c.booking_without_order,c.is_short,c.service_type,c.booking_date,b.product_id,c.ref_closing_status, sum(b.yarn_wo_qty) as qnty,b.job_no,b.booking_no,b.fab_booking_no from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where b.mst_id=c.id and b.status_active in(1,2) and b.is_deleted=0 and b.entry_form in(41,42,94,114,125,135) and c.status_active in(1,2) and c.is_deleted=0 and c.entry_form in(41,42,94,114,125,135) and c.company_id=$company $wo_date_cond and c.ref_closing_status=$only_full  group by b.product_id,b.job_no,b.booking_no,b.fab_booking_no, c.id, c.ydw_no, c.supplier_id,c.booking_without_order,c.service_type,c.booking_date,c.is_short,c.ref_closing_status order by c.id";


	//echo $sql_main;
	$main_query_result=sql_select($sql_main);

	
	
	if(count($main_query_result)>0)
	{
		$job_no_arr = array();
		$work_order_id_arr = array();
		$grey_product_id_arr = array();
		$wo_arr = array();
		$po_arr = array();
		foreach($main_query_result as $row)
		{
			$wo_arr[$row[csf('id')]][$row[csf('ydw_no')]]['service_type']=$row[csf('service_type')];
			$wo_arr[$row[csf('id')]][$row[csf('ydw_no')]]['booking_date']=$row[csf('booking_date')];
			$wo_arr[$row[csf('id')]][$row[csf('ydw_no')]]['booking_without_order']=$row[csf('booking_without_order')];
			$wo_arr[$row[csf('id')]][$row[csf('ydw_no')]]['is_short']=$row[csf('is_short')];
			$wo_arr[$row[csf('id')]][$row[csf('ydw_no')]]['ref_closing_status']=$row[csf('ref_closing_status')];

			$wo_arr[$row[csf('id')]][$row[csf('ydw_no')]]['job_no'].=$row[csf('job_no')].',';
			$wo_arr[$row[csf('id')]][$row[csf('ydw_no')]]['booking_no'].=$row[csf('booking_no')].',';
			$wo_arr[$row[csf('id')]][$row[csf('ydw_no')]]['fab_booking_no'].=$row[csf('fab_booking_no')].',';
			$wo_arr[$row[csf('id')]][$row[csf('ydw_no')]]['qnty']+=$row[csf('qnty')];

			$job_no_arr[] = "'".$row[csf('job_no')]."'";
			$wo_no_arr[] = "'".$row[csf('ydw_no')]."'";
			
		}

		$job_no_string = implode(',',array_unique($job_no_arr));
		$wo_no_arr_string = implode(',',array_unique($wo_no_arr));
	}
	else
	{
		echo "<br><center><span style='color:red; font-size:20px; font-weight:bolder;'>Data Not Fond.</span></center>";
		die();
	}
	//var_dump($wo_no_arr);

	if($job_no_string!="")
	{
		if($db_type==0)
		{
			$year_select_cond="SUBSTRING_INDEX(a.insert_date, '-', 1)";
			$order_sql = "SELECT b.job_no_mst,a.buyer_name,a.style_ref_no,$year_select_cond as year, group_concat(distinct(b.po_number)) as order_no,group_concat(distinct(b.grouping)) as internal_ref from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.job_no_mst in($job_no_string) group by b.job_no_mst,a.buyer_name,a.style_ref_no,a.insert_date";

			$order_resutl = sql_select($order_sql);
			$order_arr = array();
			foreach ($order_resutl as $row) {
				$order_arr[$row[csf('job_no_mst')]]['buyer_name']   = $row[csf('buyer_name')];
				$order_arr[$row[csf('job_no_mst')]]['style_ref_no'] = $row[csf('style_ref_no')];
				$order_arr[$row[csf('job_no_mst')]]['internal_ref'] = $row[csf('internal_ref')];
			}
		}
		else if($db_type==2)
		{
		
			$year_select_cond="to_char(a.insert_date,'YYYY')";
			$order_sql = "SELECT b.job_no_mst,a.buyer_name,a.style_ref_no,$year_select_cond as year,listagg(b.po_number,',') within group (order by b.po_number) as order_no,listagg(grouping,',') within group (order by b.po_number) as internal_ref from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no_mst in($job_no_string) group by b.job_no_mst,a.buyer_name,a.style_ref_no,a.insert_date";
			//echo $order_sql;
			$order_resutl = sql_select($order_sql);
			$order_arr = array();
			foreach ($order_resutl as $row) {
				$order_arr[$row[csf('job_no_mst')]]['buyer_name']   = $row[csf('buyer_name')];
				$order_arr[$row[csf('job_no_mst')]]['style_ref_no'] = $row[csf('style_ref_no')];
				$order_arr[$row[csf('job_no_mst')]]['internal_ref'] = $row[csf('internal_ref')];
				$order_arr[$row[csf('job_no_mst')]]['order_no']     = $row[csf('order_no')];
				$order_arr[$row[csf('job_no_mst')]]['year']         = $row[csf('year')];
			}				
		}
	}
	//var_dump($order_arr);
	if($wo_no_arr_string!="")
	{
		$issue_sql = "select a.booking_no,sum(b.cons_quantity) issue_quantity from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.booking_no in($wo_no_arr_string)  group by a.booking_no order by a.booking_no desc";
		//echo $issue_sql;

		$issue_sql_resutl = sql_select($issue_sql);
		$issue_arr = array();
		foreach ($issue_sql_resutl as $row) {
			$issue_arr[$row[csf('booking_no')]]['issue_quantity']+= $row[csf('issue_quantity')];
		}
		//var_dump($issue_arr);

		$iss_return_sql = "select a.booking_no, sum(b.cons_quantity) as issue_return,sum(b.cons_reject_qnty) as issue_reject
		from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.item_category=1 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in($wo_no_arr_string) group by a.booking_no order by a.booking_no desc";
		//echo $iss_return_sql;
		$iss_return_resutl = sql_select($iss_return_sql);
		$issue_return_arr = array();
		foreach ($iss_return_resutl as $row) {
			$issue_return_arr[$row[csf('booking_no')]]['issue_return']+= $row[csf('issue_return')]+$row[csf('issue_reject')];
		}
		//var_dump($issue_return_arr);

		$receive_sql = "select b.booking_no,sum(b.order_qnty) as rcvQty
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.booking_no in($wo_no_arr_string) and a.receive_basis=2 and a.receive_purpose in(2,12,15,38,46,50,51) group by b.booking_no order by b.booking_no desc";
		//echo $receive_sql;
		$receive_resutl = sql_select($receive_sql);
		$receive_arr = array();
		foreach ($receive_resutl as $row) {
			$receive_arr[$row[csf('booking_no')]]['rcvQty']+= $row[csf('rcvQty')];
		}
		//var_dump($receive_arr);
		
	}

	if($check_only_full=='true')
	{
		$closing_status=0;
	}
	else $closing_status=1;

	ob_start(); 
	$contents='';
	?>
    <div style="width:1630px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1630" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
            	<?php
            	$contents.= ob_get_flush();
				//ob_start();
            	?>
				<th colspan="19" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onClick="check_all_data();"/>
                &nbsp;
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                
                </th>
                <?php
            	//$contents.= ob_get_flush();
				ob_start();
            	?>
                <th width="20">SL</th>
                <th width="100">Yarn Service WO</th>
                <th width="100">WO Date</th>
                <th width="100">WO Type</th>
                <th width="100">Buyer</th>
                <th width="100">Job Year</th>
                <th width="100">Job No</th>
                <th width="90">Style Ref</th>
                <th width="90">Order No</th>
                <th width="90">Int. Ref.</th>
                <th width="90">FB No/Sam FB No/ FSO No</th>
                <th width="90">WO Qty.</th>
                <th width="90">Yarn Issue Qnty</th>
                <th width="90">Issue Return Qnty</th>
                <th width="90">Service Yarn Rcv Qty</th>
                <th width="90"> Balance</th>
                <th width="90">Process Loss Qty</th>
                <th width="">Dyed Status</th>
               
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1630px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1610">
    	<tbody>
        	<?
			$i=1;$k=1;
			foreach($wo_arr as $wo_id=> $wo_datum)
			{	
				//var_dump($wo_datum);
				foreach ($wo_datum as $key => $wo_data) {
					//var_dump($key);
				
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$job_data=array_unique(explode(",", chop($wo_data['job_no'],",") ));
				$job_no='';
				$buyer_name='';
				$style_ref_no='';
				$internal_ref='';
				$order_no='';
				$year='';
				foreach($job_data as $data)
				{
					$job_no.=$data.',';
					$buyer_name.= $lib_buyer_arr[$order_arr[$data]['buyer_name']].',';
					$style_ref_no= $order_arr[$data]['style_ref_no'];
					$internal_ref= $order_arr[$data]['internal_ref'];
					$order_no.= $order_arr[$data]['order_no'].',';
					$year = $order_arr[$data]['year'];
				} 
				$service_type = '';
				if($wo_data['service_type'])
				{
					$service_type = $yarn_issue_purpose[$wo_data['service_type']];
				}
				else
				{
					$service_type = 'Yarn Dyed';
				}
				$booking_no = '';

				if($wo_data['booking_without_order']==0)
				{					
					$booking_no = $wo_data['booking_no'];
				}
				else
				{
					$booking_no = $wo_data['fab_booking_no'];
				}

				$ref_closing_status =  $wo_data['ref_closing_status'] ? 'Closed' : '';
				
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $wo_id.'**'.$key; //change_date_format ?>"/>
					</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td style="word-break:break-all" width="20" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="100" title="<? echo $key; ?>"><p><? echo $key; ?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="100" align="center"><p><? echo $wo_data['booking_date']; ?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="100" align="center"><p><? echo $service_type; ?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center" title="<? echo rtrim($buyer_name,' ,'); ?>"><p><? echo substr(rtrim($buyer_name,' ,'),0,10); ?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center"><div style="word-break:break-all"><? echo $year; ?>	</div></td>
					<td style="word-break:break-all" width="100" align="center" title="<? echo rtrim($job_no,' ,'); ?>"><div style="word-break:break-all"><? echo substr(rtrim($job_no,' ,'),0,12); ?></div></td>
					<td width="90" align="center"><div style="word-break:break-all"><? echo $style_ref_no; ?></div></td>
                    <td width="90" align="center" title="<? echo rtrim($order_no,' ,'); ?>"><? echo substr(rtrim($order_no,' ,'),0,15); ?></td>
                    <td width="90" align="center"><? echo $internal_ref; ?></td>
					<td width="90" align="right" title="<? echo rtrim($booking_no,' ,');?>"><? echo substr(rtrim($booking_no,' ,'),0,15);?></td>
                    <td width="90" align="right"><? echo $wo_data['qnty']; ?></td>
                    <td width="90" align="right"><? echo number_format($issue_arr[$key]['issue_quantity'],2);?></td>
                    <td width="90" align="right" ><? echo number_format($issue_return_arr[$key]['issue_return'],2);?></td>
                    <td width="90" align="right"><? echo number_format($receive_arr[$key]['rcvQty'],2);?></td>
                    <td width="90" align="right" title="Balance Qty Formula: [Yarn Issue qty-Issue Return Qnty-Service Yarn Rcv Qty]"><? echo number_format($issue_arr[$key]['issue_quantity']-$issue_return_arr[$key]['issue_return']-$receive_arr[$key]['rcvQty'],2);?></td>
                    
                    <td width="90" align="right" title="Process Loss Qty Formula: [Yarn Issue qty-Issue Return Qnty-Service Yarn Rcv Qty]"><? echo number_format($issue_arr[$key]['issue_quantity']-$issue_return_arr[$key]['issue_return']-$receive_arr[$key]['rcvQty'],2);?></td>

                    <td><? echo  $ref_closing_status; ?></td>
                </tr>
				<?
				$k++;
				
				}
			}
			
            ?>
        </tbody>
    </table>
    </div>
    <?

    $html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			 @unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php 

}

if($action=="show_details_yarn_po_closing")
{
	$process = array( &$_POST );
	//var_dump($process);
	extract(check_magic_quote_gpc( $process ));
	//echo "test";die;
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	
 	?>
 	<script>
	 //check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=370)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?
	$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
	$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
	$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$userArr= return_library_array("select id, user_full_name from user_passwd where status_active=1 and is_deleted=0", "id", "user_full_name");
	$wo_date_cond="";
	if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and c.wo_date between ".$txt_date_from." and ".$txt_date_to."";
			
	$sql_main="SELECT c.id,c.wo_number,c.wo_date,c.insert_date,c.inserted_by,c.supplier_id,c.wo_basis_id,c.ref_closing_status, c.delivery_date, c.currency_id, c.pay_mode, c.payterm_id, sum(b.supplier_order_quantity) as qnty from wo_non_order_info_dtls b, wo_non_order_info_mst c where b.mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.entry_form =144 and c.status_active=1 and c.is_deleted=0 and c.company_name=$company $wo_date_cond and c.ref_closing_status=$only_full  group by c.id,c.wo_number,c.wo_date,c.insert_date,c.inserted_by,c.supplier_id,c.wo_basis_id,c.ref_closing_status, c.delivery_date, c.currency_id, c.pay_mode, c.payterm_id order by c.id";


	//echo $sql_main;
	$main_query_result=sql_select($sql_main);

	
	
	if(count($main_query_result)>0)
	{
		// $job_no_arr = array();
		// $work_order_id_arr = array();
		// $grey_product_id_arr = array();
		$wo_arr = array();
		// $po_arr = array();
		foreach($main_query_result as $row)
		{
			$wo_arr[$row[csf('id')]][$row[csf('wo_number')]]['wo_date']=$row[csf('wo_date')];
			$wo_arr[$row[csf('id')]][$row[csf('wo_number')]]['insert_date']=$row[csf('insert_date')];
			$wo_arr[$row[csf('id')]][$row[csf('wo_number')]]['inserted_by']=$row[csf('inserted_by')];
			$wo_arr[$row[csf('id')]][$row[csf('wo_number')]]['supplier_id']=$row[csf('supplier_id')];
			$wo_arr[$row[csf('id')]][$row[csf('wo_number')]]['wo_basis_id']=$row[csf('wo_basis_id')];
			$wo_arr[$row[csf('id')]][$row[csf('wo_number')]]['ref_closing_status']=$row[csf('ref_closing_status')];
			$wo_arr[$row[csf('id')]][$row[csf('wo_number')]]['delivery_date']=$row[csf('delivery_date')];
			$wo_arr[$row[csf('id')]][$row[csf('wo_number')]]['currency_id']=$row[csf('currency_id')];
			$wo_arr[$row[csf('id')]][$row[csf('wo_number')]]['pay_mode']=$row[csf('pay_mode')];
			$wo_arr[$row[csf('id')]][$row[csf('wo_number')]]['payterm_id']=$row[csf('payterm_id')];
			$wo_arr[$row[csf('id')]][$row[csf('wo_number')]]['qnty']+=$row[csf('qnty')];

			//$job_no_arr[] = "'".$row[csf('job_no')]."'";
			$wo_no_arr[] = "'".$row[csf('wo_number')]."'";
			
		}

		//$job_no_string = implode(',',array_unique($job_no_arr));
		$wo_no_arr_string = implode(',',array_unique($wo_no_arr));
	}
	else
	{
		echo "<br><center><span style='color:red; font-size:20px; font-weight:bolder;'>Data Not Fond.</span></center>";
		die();
	}
	//var_dump($wo_no_arr);

	
	if($wo_no_arr_string!="")
	{
		$receive_sql = "select b.booking_no,sum(b.order_qnty) as rcvQty
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.booking_no in($wo_no_arr_string) and a.receive_basis=2 and a.receive_purpose in(16) group by b.booking_no order by b.booking_no desc";
		//echo $receive_sql;
		$receive_resutl = sql_select($receive_sql);
		$receive_arr = array();
		foreach ($receive_resutl as $row) {
			$receive_arr[$row[csf('booking_no')]]['rcvQty']+= $row[csf('rcvQty')];
		}
		//var_dump($receive_arr);
		
	}

	if($check_only_full=='true')
	{
		$closing_status=0;
	}
	else $closing_status=1;

	ob_start(); 
	$contents='';
	?>
    <div style="width:1360px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1360" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
            	<?php
            	$contents.= ob_get_flush();
				//ob_start();
            	?>
				<th colspan="16" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onClick="check_all_data();"/>
                &nbsp;
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                
                </th>
                <?php
            	//$contents.= ob_get_flush();
				ob_start();
            	?>
                <th width="20">SL</th>
                <th width="100">Yarn WO No</th>
                <th width="100">WO Date</th>
                <th width="100">Insert Date</th>
                <th width="100">Insert By</th>
                <th width="100">Supplier Name</th>
                <th width="100">WO Basis</th>
                <th width="90">Currency</th>
                <th width="90">Pay Mode</th>
                <th width="90">Pay Term</th>
                <th width="90">Delivery Date</th>
                <th width="90">WO Qty.</th>
                <th width="90">Rcvd Qty</th>
                <th width="90">Rcv Return </th>              
                <th width=""> Balance</th>               
           
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1360px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1358">
    	<tbody>
        	<?
			$i=1;$k=1;
			foreach($wo_arr as $wo_id=> $wo_datum)
			{

				//var_dump($wo_datum);
				foreach ($wo_datum as $key => $wo_data) {
					//var_dump($key);
				
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				

				$ref_closing_status =  $wo_data['ref_closing_status'] ? 'Closed' : '';
				
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $wo_id.'**'.$key; //change_date_format ?>"/>
					</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td style="word-break:break-all" width="20" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="100" title="<? echo $key; ?>"><p><? echo $key; ?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center"><p><? echo $wo_data['wo_date']; ?>&nbsp;</p></td>
                    <td style="word-break:break-all" width="100" align="center"><p><? echo $wo_data['insert_date']; ?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center"><p><? echo $userArr[$wo_data['inserted_by']]; ?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center"><? echo $lib_supplier_arr[$wo_data['supplier_id']]; ?>	</div></td>
					<td style="word-break:break-all" width="100" align="center"><? echo $wo_basis[$wo_data['wo_basis_id']] ; ?></td>
					<td width="90" align="center"><? echo $currency[$wo_data['currency_id']]; ?></td>
                    <td width="90" align="center"><? echo $pay_mode[$wo_data['pay_mode']]; ?></td>
                    <td width="90" align="center"><? echo $pay_term[$wo_data['payterm_id']]; ?></td>
					<td width="90" align="center"><? echo $wo_data['delivery_date'];?></td>
                    <td width="90" align="right"><? echo $wo_data['qnty']; ?></td>
                    <td width="90" align="right"><? echo number_format($receive_arr[$key]['rcvQty'],2);?></td>
                    <td width="90" align="right" >&nbsp;</td>
                    <td width="" align="right"><? echo number_format(($wo_data['qnty']-$receive_arr[$key]['rcvQty']),2);?></td>
                </tr>
				<?
				$k++;
				
				}
			}
			
            ?>
        </tbody>
    </table>
    </div>
    <?

    $html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			 @unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php 

}

if($action=="show_details_smn_booking_closing")
{
	$process = array( &$_POST );
	//var_dump($process);
	extract(check_magic_quote_gpc( $process ));
	//echo "test";die;
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	
 	?>
 	<script>
	 //check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=370)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?
	$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
	$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
	$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$userArr= return_library_array("select id, user_full_name from user_passwd where status_active=1 and is_deleted=0", "id", "user_full_name");
	$wo_date_cond="";
	if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and c.booking_date between ".$txt_date_from." and ".$txt_date_to."";
			

	$sql_main="SELECT c.id, c.booking_no,c.booking_date, c.delivery_date, c.buyer_id,c.pay_mode, c.inserted_by, c.ref_closing_status, sum(b.grey_fabric) as grey_qty, sum(b.finish_fabric) as fin_qty  from wo_non_ord_samp_booking_dtls b, wo_non_ord_samp_booking_mst c where b.booking_no=c.booking_no and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_category=2 and c.company_id=$company $wo_date_cond and c.ref_closing_status=$only_full  group by c.id, c.booking_no,c.booking_date,c.delivery_date, c.buyer_id,c.pay_mode, c.inserted_by ,c.ref_closing_status order by c.id";


	//echo $sql_main;
	$main_query_result=sql_select($sql_main);

	if(count($main_query_result)>0)
	{
		$wo_arr = array();
		foreach($main_query_result as $row)
		{
			$wo_arr[$row[csf('id')]][$row[csf('booking_no')]]['delivery_date']=$row[csf('delivery_date')];
			$wo_arr[$row[csf('id')]][$row[csf('booking_no')]]['booking_date']=$row[csf('booking_date')];
			$wo_arr[$row[csf('id')]][$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_id')];
			$wo_arr[$row[csf('id')]][$row[csf('booking_no')]]['pay_mode']=$row[csf('pay_mode')];
			$wo_arr[$row[csf('id')]][$row[csf('booking_no')]]['ref_closing_status']=$row[csf('ref_closing_status')];
			$wo_arr[$row[csf('id')]][$row[csf('booking_no')]]['inserted_by']=$row[csf('inserted_by')];
			$wo_arr[$row[csf('id')]][$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_qty')];
			$wo_arr[$row[csf('id')]][$row[csf('booking_no')]]['fin_qty']+=$row[csf('fin_qty')];

			$wo_no_arr[] = "'".$row[csf('booking_no')]."'";
			
		}

		$wo_no_arr_string = implode(',',array_unique($wo_no_arr));
	}
	else
	{
		echo "<br><center><span style='color:red; font-size:20px; font-weight:bolder;'>Data Not Fond.</span></center>";
		die();
	}
	//var_dump($wo_no_arr);
	
	if($wo_no_arr_string!="")
	{		
		$grey_rcv_sql = "select a.booking_no,sum(b.order_qnty) as grey_rcvQty
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in($wo_no_arr_string) group by a.booking_no order by a.booking_no desc";
		//echo $grey_rcv_sql;
		$grey_rcv_resutl = sql_select($grey_rcv_sql);
		$grey_rcv_arr = array();
		foreach ($grey_rcv_resutl as $row) {
			$grey_rcv_arr[$row[csf('booking_no')]]['grey_rcvQty']+= $row[csf('grey_rcvQty')];
		}
		//var_dump($grey_rcv_arr);

		$fin_rcv_sql = "select a.booking_no,sum(b.order_qnty) as fin_rcvQty
		from pro_batch_create_mst a, inv_transaction b
		where a.id=b.pi_wo_batch_no and b.transaction_type=1 and b.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in($wo_no_arr_string) group by a.booking_no order by a.booking_no desc";
		//echo $fin_rcv_sql;
		$fin_rcv_resutl = sql_select($fin_rcv_sql);
		$fin_rcv_arr = array();
		foreach ($fin_rcv_resutl as $row) {
			$fin_rcv_arr[$row[csf('booking_no')]]['fin_rcvQty']+= $row[csf('fin_rcvQty')];
		}
		
	}

	if($check_only_full=='true')
	{
		$closing_status=0;
	}
	else $closing_status=1;

	ob_start(); 
	$contents='';
	?>
    <div style="width:1190px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1190" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
            	<?php
            	$contents.= ob_get_flush();
				//ob_start();
            	?>
				<th colspan="14" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onClick="check_all_data();"/>
                &nbsp;
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                
                </th>
                <?php
            	//$contents.= ob_get_flush();
				ob_start();
            	?>
                <th width="20">SL</th>
                <th width="100">Sample Booking No</th>
                <th width="100">Booking Date</th>
                <th width="100">Booking Delivery Date</th>
                <th width="100">Buyer</th>
                <th width="100">Pay Mode</th>
                <th width="100">Created By</th>
                <th width="90">Grey Req Qty</th>
                <th width="90">Grey Rcv Qty</th>
                <th width="90">Grey Rcv Bal</th>
                <th width="90">Fin. Req Qty</th>
                <th width="90">Fin Rcv Qty</th>
                <th width="">Fin Bal Qty</th>
               
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1190px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1190">
    	<tbody>
        	<?
			$i=1;$k=1;
			foreach($wo_arr as $wo_id=> $wo_datum)
			{	
				//var_dump($wo_datum);
				foreach ($wo_datum as $key => $wo_data) {
					//var_dump($key);
				
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$ref_closing_status =  $wo_data['ref_closing_status'] ? 'Closed' : '';
				
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onClick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $wo_id.'**'.$key; //change_date_format ?>"/>
					</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td style="word-break:break-all" width="20" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="100" title="<? echo $key; ?>"><p><? echo $key; ?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center"><p><? echo $wo_data['booking_date']; ?>&nbsp;</p></td>
                    <td style="word-break:break-all" width="100" align="center"><p><? echo $wo_data['delivery_date']; ?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center"><p><? echo $lib_buyer_arr[$wo_data['buyer_id']];?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center"><? echo $pay_mode[$wo_data['pay_mode']]; ?></td>
					<td style="word-break:break-all" width="100" align="center"><? echo $userArr[$wo_data['inserted_by']]; ?></td>
					<td width="90" align="center"> <? echo $wo_data['grey_qty']; ?></td>
                    <td width="90" align="center" ><? echo number_format($grey_rcv_arr[$key]['grey_rcvQty'],2); ?></td>
                    <td width="90" align="center" ><? echo number_format($wo_data['grey_qty']-$grey_rcv_arr[$key]['grey_rcvQty'],2); ?></td>
                    <td width="90" align="center"><? echo $wo_data['fin_qty']; ?></td>
					<td width="90" align="right" ><? echo number_format($fin_rcv_arr[$key]['fin_rcvQty'],2);?></td>
                    <td width="" align="right"><? echo number_format($wo_data['fin_qty']-$fin_rcv_arr[$key]['fin_rcvQty'],2); ?></td>

                    
                </tr>
				<?
				$k++;
				
				}
			}
			
            ?>
        </tbody>
    </table>
    </div>
    <?

    $html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			 @unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php 

}


?>
