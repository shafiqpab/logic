<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="supplier_category_multi_select")
{
	echo "set_multiselect('cbo_supplier','0','0','','0');\n";
	echo "set_multiselect('cbo_item_category_id','0','0','','0');\n";
	exit();
}
if ($action=="load_drop_down_supplier")
{
	if ($data!=0) {
		$company_cond = " and c.tag_company in($data) ";
	}
	echo create_drop_down( "cbo_supplier", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  $company_cond and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", $selected, "",0 );
}

if($action=="load_drop_down_category")
{
	$supplier_res = array();
	if ($data>0) 
	{
		$supplier_res = sql_select("select c.supplier_name,c.id, b.party_type from  lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id  and c.status_active=1 and c.is_deleted=0 and c.id in($data) order by c.supplier_name");
	}
	
	foreach($supplier_res as $val) 
	{
		$party_types[$val[csf("party_type")]] = $val[csf("party_type")];
	}
	//print_r($party_types);
	$category="";
	if(empty($party_types)) 
	{
		//echo create_drop_down( "cbo_item_category_id", 160, $blank_array,"", 1,"-- Select --",0,"" );
		echo create_drop_down( "cbo_item_category_id", 160, $item_category,'', 1, '-- Select --',0,"",0,'','','','1,2,6,12,13,14,23,24,25,28,30');
	}
	else 
	{
		if($party_types["7"])
		{			  
			$item_category_all =  $item_category;
			unset($item_category_all["1"]);
			unset($item_category_all["2"]);
			unset($item_category_all["3"]);
			unset($item_category_all["4"]);
			unset($item_category_all["5"]);
			unset($item_category_all["6"]);
			unset($item_category_all["7"]);
			unset($item_category_all["9"]);
			unset($item_category_all["10"]);
			unset($item_category_all["11"]);
			unset($item_category_all["13"]);
			unset($item_category_all["14"]);
			unset($item_category_all["31"]);
			unset($item_category_all["32"]);
			$category .= implode(",", array_keys($item_category_all));
		} 

		if($party_types["91"])
		{			  
			$item_category_all =  $item_category;
			unset($item_category_all["1"]);
			$category .= implode(",", array_keys($item_category_all));
		}

		if( $party_types["2"])
		{
			//if($category)  $category .= ",1"; else $category ="1";
		}
		if($party_types["9"])
		{
			if($category)  $category .=",2,3,13,14"; else $category ="2,3,13,14";
		}
		if($party_types["4"] || $party_types["5"])
		{
			if($category)  $category .=",4"; else $category ="4";
			
		}
		if($party_types["3"])
		{
			if($category) $category .=",5,6,7"; else $category ="5,6,7";
		}
		if($party_types["6"])
		{
			if($category) $category .=",9,10"; else $category ="9,10"; 
		}
		if($party_types["8"])
		{
			if($category) $category .=",11"; else $category ="11";  
		}
		if($party_types["20"] || $party_types["21"] ||$party_types["22"] ||$party_types["23"] ||$party_types["24"] ||$party_types["30"] ||$party_types["31"] ||$party_types["32"] ||$party_types["35"] ||$party_types["36"] ||$party_types["37"] ||$party_types["38"] ||$party_types["39"])
		{
			//if($category) $category .=",12,24,25"; else $category ="12,24,25";
		}
		if($party_types["26"])
		{
			//if($category) $category .= ",31"; else $category ="31";
		}
		if($party_types["92"])
		{
			if($category) $category .= ",32"; else $category ="32";
		}
		
		$category_arr1 = explode(",", $category); 
		$category_arr2 = explode(",", "1,2,3,6,12,13,14,24,23,25,28,30");
		$show_category = array_diff($category_arr1, $category_arr2);
		//print_r($category);die;
		echo create_drop_down( "cbo_item_category_id", 160, $item_category,'', 1, '-- Select --',0,"",0,implode(",",$show_category),'','','');
	}
	//========================================
}

if($action=="create_wo_no_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_year,$item_category_id)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0){
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}else{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	$sql_cond =" and a.company_id=$company";	
	$sql_cond .=" and a.item_category=$item_category_id";	
	if($search_type==3 && $search_value!=''){
		$sql_cond .=" and a.booking_no_prefix_num='$search_value'";	
	}

	/*else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no='$search_value'";	
	}*/

	
	if($db_type==0) $year_field="YEAR(a.insert_date) as job_year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as job_year";
	else $year_field="";

	$sql_wo="select a.id, a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.buyer_id
	from wo_booking_mst a, wo_booking_dtls b
	where a.booking_no=b.booking_no and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond group by a.id, a.booking_no, a.booking_no_prefix_num, a.booking_date, a.buyer_id";

	//and a.booking_type=2 and b.booking_type=2 
	//if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	//$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$year_field from wo_po_details_master a where a.company_name in($company) $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by job_no_prefix_num"; 
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$arr=array(2=>$buyer_arr);
	echo create_list_view("list_view", "WO No,WO Date,Buyer","100,90,160","400","200",0, $sql_wo , "js_set_value", "id,wo_number_prefix_num", "", 1, "0,0,buyer_id", $arr, "wo_number,wo_date,buyer_id", "","setFilterGrid('list_view',-1)","0","",1) ;

	echo "<input type='hidden' id='hide_wo_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='hide_wo_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	//var style_des='<? echo $txt_style_ref;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_supplier=str_replace("'","",$cbo_supplier);	
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$cbo_insert_by=str_replace("'","",$cbo_insert_by);
	$txt_req_no=trim(str_replace("'","",$txt_req_no));
	$txt_wo_no=trim(str_replace("'","",$txt_wo_no));
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	if($cbo_company_name>0) $company_cond =" and a.company_name=$cbo_company_name ";else $company_cond= "";
	if($cbo_item_category_id>0) $item_category_cond =" and b.item_category_id in($cbo_item_category_id) ";else $item_category_cond= "";
	if($cbo_supplier>0) $supplier_cond =" and a.supplier_id in($cbo_supplier) "; else $supplier_cond= "";
	if($cbo_insert_by>0) $insert_by_cond=" and a.inserted_by=$cbo_insert_by "; else $insert_by_cond="";
	if($txt_req_no>0) $requisition_no_cond =" and a.requ_prefix_num=$txt_req_no "; else $requisition_no_cond= "";
	if($txt_wo_no>0) $wo_no_cond =" and a.wo_number_prefix_num='$txt_wo_no' ";else $wo_no_cond= "";
	//echo $company_cond.'<br>'.$item_category_cond.'<br>'.$supplier_cond.'<br>'.$insert_by_cond.'<br>'.$wo_no_cond; die;

	if($cbo_company_name>0) $requ_company_cond =" and a.company_id=$cbo_company_name ";else $requ_company_cond= "";
	$sql_requ_no="SELECT A.ID AS REQU_ID, A.REQU_NO, A.REQU_PREFIX_NUM, A.REQUISITION_DATE, B.ID AS DTLS_ID, B.QUANTITY AS REQU_QTY, B.CONS_UOM
	FROM INV_PURCHASE_REQUISITION_MST A, INV_PURCHASE_REQUISITION_DTLS B
	WHERE A.ID=B.MST_ID AND A.ENTRY_FORM=69 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 $requisition_no_cond $requ_company_cond ORDER BY A.ID DESC";
	// echo $sql_requ_no;die;
	$requ_data_arr = sql_select($sql_requ_no);
	$requ_data = array();
	foreach($requ_data_arr as $row)
	{
		$all_requ_id.=$row['DTLS_ID'].',';
		$requ_data[$row['DTLS_ID']]['REQU_NO']=$row['REQU_NO'];
		$requ_data[$row['DTLS_ID']]['REQUISITION_DATE']=$row['REQUISITION_DATE'];
		$requ_data[$row['DTLS_ID']]['REQU_QTY']=$row['REQU_QTY'];
		$requ_data[$row['DTLS_ID']]['CONS_UOM']=$row['CONS_UOM'];
	}
	/*echo "<pre>";
	print_r($requ_data);die;*/

	function quote($str)
    {
        return sprintf("'%s'", $str);
    }

	$all_requ_id=chop($all_requ_id,",");

	$all_requ_id_arr = explode(",",$all_requ_id);
    $all_requ_id_string = implode(',', array_map('quote', $all_requ_id_arr));
    //echo $all_requ_id_string;die;

	$p=1;
	if($all_requ_id!="" && $txt_req_no!="")
	{
		$all_requ_id_arr=array_chunk(array_unique(explode(",",$all_requ_id_string)),999);
		foreach($all_requ_id_arr as $requ_id)
		{
			// if($p==1) $requ_id_cond .=" and (a.requisition_no in(".implode(",",$requ_id).")"; else $requ_id_cond .=" or a.requisition_no in(".implode(",",$requ_id).")";
			if($p==1) $requ_id_cond .=" and (b.requisition_dtls_id in(".implode(",",$requ_id).")"; else $requ_id_cond .=" or a.requisition_no in(".implode(",",$requ_id).")";
			$p++;
		}
		$requ_id_cond .=" )";
	}
	//echo $requ_id_cond.Tipu;die;

	if ($txt_date_from!="" && $txt_date_to!="") 
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',-1);
			$date_to=change_date_format($txt_date_to,'','',-1);
		}
		$date_cond=" and a.wo_date between '$date_from' and '$date_to'";
	}
	
	// echo $date_cond;die;	
	
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$company_full_name=return_library_array("select id, company_name from lib_company",'id','company_name');
	$item_group_array = return_library_array("select id,item_name from  lib_item_group ","id","item_name");
	$suplier_array = return_library_array("select id,supplier_name from  lib_supplier ","id","supplier_name");
	$item_group_array = return_library_array("select id,item_name from lib_item_group","id","item_name");
	$user_name_array = return_library_array("select id, user_name from user_passwd","id","user_name");
	$user_full_name_array = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");

	$sql_data = "SELECT A.ID, A.COMPANY_NAME, A.WO_NUMBER, A.INSERTED_BY, A.REQUISITION_NO AS REQU_ID, A.WO_DATE, A.SUPPLIER_ID, B.ID AS DTLS_ID, B.ITEM_CATEGORY_ID, B.ITEM_ID, B.REQ_QUANTITY, B.REQUISITION_DTLS_ID AS REQU_DTLS_ID, B.SUPPLIER_ORDER_QUANTITY AS WO_QTY, B.RATE, B.AMOUNT, B.UOM, C.ITEM_GROUP_ID, C.ITEM_DESCRIPTION
	FROM WO_NON_ORDER_INFO_MST A, WO_NON_ORDER_INFO_DTLS B, PRODUCT_DETAILS_MASTER C 
	WHERE A.ID=B.MST_ID AND B.ITEM_ID=C.ID AND A.ENTRY_FORM IN(146,147) $company_cond  $item_category_cond  $supplier_cond  $insert_by_cond  $wo_no_cond  $requ_id_cond  $date_cond AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 ORDER BY A.ID";
	// echo $sql_data;
	$data_arr = sql_select($sql_data);

	ob_start();
	?>
	<style type="text/css">
		#textAlign{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>
	<div>	
		<table width="1600" cellspacing="0" cellpadding="0" border="0" rules="all" >
			<br>
		    <tr class="form_caption">
		        <td colspan="30" align="center"><? echo (str_replace("'","",$cbo_company_name)==0) ? 'All Company' : $company_arr[str_replace("'","",$cbo_company_name)]; ?><br>
		        </b>
		    </tr>
		    <tr class="form_caption">
		        <td colspan="30" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
		        </td>
		    </tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1600" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="110">Company</th>
				<th width="80">Category</th>
				<th width="100">WO No</th>	
				<th width="110">Insert By</th>
				<th width="120">Full User Name</th>
				<th width="100">Req. No</th>
				<th width="60">Req. Date</th>
				<th width="70">Req Qty.</th>
				<th width="40">UOM</th>				
				<th width="70">Item Group</th>
				<th width="150">Item Description</th>
				<th width="70">WO Qnty</th>
				<th width="50"><p>Wo Rate<p></th>
				<th width="90"><p>WO Amount</p></th>
				<th width="60"><p>WO Date<p></th>
				<th width="80"><p>WO Balance</p></th>
				<th width="">Supplier</th>
			</thead>
		</table>

		<div style="width:1620px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1600" class="rpt_table" id="table_body">
				<?
			    $i=1;
			    $total_requ_qty=$total_wo_amount=$total_wo_qty=$total_wo_balance=0;
				foreach($data_arr as $key=>$rows)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					// $requ_no 	= $requ_data[$rows['REQU_ID']][$rows['REQU_DTLS_ID']]['REQU_NO'];
					// $requ_date 	= $requ_data[$rows['REQU_ID']][$rows['REQU_DTLS_ID']]['REQUISITION_DATE'];
					// $requ_qty 	= $requ_data[$rows['REQU_ID']][$rows['REQU_DTLS_ID']]['REQU_QTY'];
					// $requ_uom 	= $requ_data[$rows['REQU_ID']][$rows['REQU_DTLS_ID']]['CONS_UOM'];
					$requ_no 	= $requ_data[$rows['REQU_DTLS_ID']]['REQU_NO'];
					$requ_date 	= $requ_data[$rows['REQU_DTLS_ID']]['REQUISITION_DATE'];
					$requ_qty 	= $requ_data[$rows['REQU_DTLS_ID']]['REQU_QTY'];
					$requ_uom 	= $requ_data[$rows['REQU_DTLS_ID']]['CONS_UOM'];
					$wo_balance = ($requ_qty>0) ? $requ_qty-$rows['WO_QTY'] : 0 ;
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
						<td valign="middle" id="textAlign" width="30"><? echo $i; ?></td>
						<td valign="middle" id="textAlign" width="110"><p><? echo $company_full_name[$rows['COMPANY_NAME']]; ?></p></td>
						<td valign="middle" id="textAlign" width="80"><p><? echo $item_category[$rows['ITEM_CATEGORY_ID']]; ?></p></td>
						<td valign="middle" id="textAlign" width="100"><p><? echo $rows['WO_NUMBER']; ?></p></td>
						<td valign="middle" id="textAlign" width="110"><p><? echo $user_name_array[$rows['INSERTED_BY']]; ?></p></td>
						<td valign="middle" id="textAlign" width="120"><p><? echo $user_full_name_array[$rows['INSERTED_BY']]; ?></p></td>
						<td valign="middle" id="textAlign" width="100"><p><? echo $requ_no; ?></p></td>
						<td valign="middle" id="textAlign" width="60"><p><? echo change_date_format($requ_date); ?></p></td>
						<td valign="middle" id="textAlign" width="70" align="right"><p><? echo number_format($requ_qty,2,".",""); ?></p></td>
						<td valign="middle" id="textAlign" width="40"><p><? echo $unit_of_measurement[$rows['UOM']]; ?></p></td>
						<td valign="middle" id="textAlign" width="70"><p><? echo $item_group_array[$rows['ITEM_GROUP_ID']]; ?></p></td>
						<td valign="middle" id="textAlign" width="150"><p><? echo $rows['ITEM_DESCRIPTION']; ?></p></td>
						<td valign="middle" id="textAlign" width="70" align="right"><p><? echo number_format($rows['WO_QTY'],2,".","") ; ?></p></td>
						<td valign="middle" id="textAlign" width="50" align="right"><p><? echo number_format($rows['RATE'],2,".",""); ?></p></td>
						<td valign="middle" id="textAlign" width="90" align="right"><p><? echo number_format($rows['AMOUNT'],2,".",""); ?></p></td>
						<td valign="middle" id="textAlign" width="60"><p><? echo change_date_format($rows['WO_DATE']); ?></p></td>
						<td valign="middle" id="textAlign" width="80" align="right"><p><? echo number_format($wo_balance,2,".",""); ?></p></td>
						<td valign="middle" id="textAlign"><p><? echo $suplier_array[$rows['SUPPLIER_ID']]; ?></p></td>
				    </tr>
				    <?								
				    $i++;
				    $total_requ_qty+=$requ_qty;
				    $total_wo_qty+=$rows['WO_QTY'];
				    $total_wo_amount+=$rows['AMOUNT'];
				    $total_wo_balance+=$wo_balance;
				} 
			    ?>
			</table>

			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1600" class="rpt_table" id="report_table_footer">
				<tfoot>
					<th width="30" title="Sl"></th>
					<th width="110" title="Company"></th>
					<th width="80" title="Category"></th>
					<th width="100" title="WO No"></th>
					<th width="110" title="Insert By"></th>
					<th width="120" title="Full User Name"></th>
					<th width="100" title="Req. No"></th>
					<th width="60" title="Req. Date"><strong>Total: </strong></th>
					<th width="70" align="right" id="value_total_req_qnty"><strong><? echo number_format($total_requ_qty,2,".",""); ?></strong></th>  
					<th width="40" title="UOM"></th>
					<th width="70" title="Item Group"></th>    
					<th width="150" title="Item Description"></th>
					<th width="70" align="right" id="value_total_wo_qnty"><strong><? echo number_format($total_wo_qty,2,".",""); ?></strong></th>
					<th width="50" title="Rate"></th>
					<th width="90" align="right" id="value_total_wo_amt"><strong><? echo number_format($total_wo_amount,2,".",""); ?></strong></th>
					<th width="60" title="WO Date"></th>       
					<th width="80" align="right" id="value_total_wo_balance"><strong><? echo number_format($total_wo_balance,2,".",""); ?></strong></th>
					<th width="" title="Supplier"></th>
				</tfoot>
			</table>
		</div>	
	</div>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

disconnect($con);
?>
