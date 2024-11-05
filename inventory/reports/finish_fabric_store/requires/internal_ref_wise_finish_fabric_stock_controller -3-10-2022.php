<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.fabrics.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}
if ($action=="load_drop_down_store")
{
	$data=explode('_',$data);
	if($data[1]==1){$knitFinish=2;}else{$knitFinish=3;}
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and  b.category_type=$knitFinish order by a.store_name","id,store_name", 1, "--Select Store--", 1, "",0 );
	exit();
	//select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type=$data[1] order by a.store_name
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];

			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
		}

		/*function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]);
			$("#hide_job_no").val(splitData[1]);
			parent.emailwindow.hide();
		}*/
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
				<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search Job</th>
						<th>Search Style</th>
						<!--<th>Search Order</th>-->
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td align="center">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_job" id="txt_search_job" placeholder="Job No" />
							</td>
							<td align="center">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_style" id="txt_search_style" placeholder="Style Ref." />
							</td>
	                        <!--<td align="center">
	                            <input type="text" style="width:80px" class="text_boxes" name="txt_search_order" id="txt_search_order" placeholder="Order No" />
	                        </td> +'**'+document.getElementById('txt_search_order').value-->
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_job').value+'**'+document.getElementById('txt_search_style').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_finish_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
	                        </td>
	                    </tr>
	                </tbody>
	            </table>
	            <div style="margin-top:15px" id="search_div"></div> 
	        </fieldset>
	    </form>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	//$month_id=$data[5];
	//echo $month_id;

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

	if($data[2]!='') $job_cond=" and job_no_prefix_num=$data[2]"; else $job_cond="";
	if($data[3]!='') $style_cond=" and style_ref_no like '$data[3]'"; else $style_cond="";
	//if($data[4]!='') $order_cond=" and po_number like '$data[4]'"; else $order_cond="";

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date)";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY')";
	else $year_field_by="";

	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";
	//if($month_id!=0) $month_cond="$month_field_by=$month_id"; else $month_cond="";
	$arr=array (0=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, buyer_name, style_ref_no, $year_field_by as year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $buyer_id_cond $job_cond $style_cond $year_cond order by id DESC";

	echo create_list_view("tbl_list_search", "Buyer Name,Job No,Year,Style Ref. No", "170,130,80,60","610","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0','',1) ;
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_report_type 	= str_replace("'","",$cbo_report_type);
	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_comm 	= str_replace("'","",$txt_search_comm);
	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);
	$cbo_store_name = str_replace("'","",$cbo_store_name);
	$cbo_shipment_status = str_replace("'","",$cbo_shipment_status);
	if($cbo_shipment_status>0){$shiping_status_cond=" and b.shiping_status in ($cbo_shipment_status)";}

	//var_dump($cbo_value_range_by);

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '$txt_search_comm'";
	}
	else
	{
		$search_cond.="";
	}

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	if($db_type==0)
	{
		$select_fld= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	ob_start();

	if($cbo_report_type==1)
	{
		// Knit Finish Start
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		//$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2", "id", "product_name_details");
		$product_arr=return_library_array("select a.id, b.construction from product_details_master a, lib_yarn_count_determina_mst b where a.item_category_id=2 and a.detarmination_id=b.id", "id", "construction");

		if($cbo_store_name>0){$storeCond="and c.store_id=$cbo_store_name";}else{$storeCond="";}

		$sql_rcv="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no,b.shiping_status, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id,b.grouping,
		(case when d.entry_form in (7,37,66,68) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
		(case when d.entry_form in (52,126) and $today_receive_date then d.quantity else 0 end) as today_issue_rtn_qnty,
		(case when d.entry_form in (14,15,134,306) and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in_qnty,
		(case when d.entry_form in (7,37,66,68)  then d.quantity else 0 end) as receive_qnty,
		(case when d.entry_form in (52,126) then d.quantity else 0 end) as issue_rtn_qnty,
		(case when d.entry_form in (14,15,134,306) and d.trans_type=5 then d.quantity else 0 end) as trans_in_qnty,

		(case when d.entry_form in (18,71) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty,
		(case when d.entry_form in (46) and $today_receive_date then d.quantity else 0 end) as today_rcv_rtn_qnty,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trns_out_qnty,
		(case when d.entry_form in (46) then d.quantity else 0 end) as issue_rcv_rtn_qnty,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty

		from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
		where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id  and a.company_name=$cbo_company_id and d.entry_form in (7,14,15,18,37,46,52,66,68,71,126,134,306) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $shiping_status_cond $storeCond $receive_date $buyer_id_cond $search_cond $year_cond
		order by a.buyer_name,b.grouping,a.job_no, d.color_id,c.transaction_date";

		$style_wise_arr=$color_id_arr=$prod_id_arr=$po_id_arr=array();

		$sql_rcv_res=sql_select($sql_rcv);
		foreach ($sql_rcv_res as $row)
		{
			$color_id_arr[$row[csf('color_id')]]=$row[csf('color_id')];
			$po_id_shipingStatus_arr[$row[csf('po_id')]]=$row[csf('shiping_status')];
			$prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
			$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$job_no_arr[$row[csf('job_no')]] = "'".$row[csf('job_no')]."'";

			$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
			//$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			//$fab_desc_tmp=$fab_desc_tmp1[0];

			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no']=$row[csf('job_no')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no_pre']=$row[csf('job_no_prefix_num')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['year']=$row[csf('year')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['buyer_name']=$row[csf('buyer_name')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['style_ref_no']=$row[csf('style_ref_no')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['grouping'].=$row[csf('grouping')].",";
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_id'].=$row[csf('po_id')].',';
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_no'].=$row[csf('po_no')].',';
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['receive_qnty']+=$row[csf('receive_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_rtn_qnty']+=$row[csf('issue_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['trans_in_qnty']+=$row[csf('trans_in_qnty')];

			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_rcv_rtn_qnty']+=$row[csf('issue_rcv_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['trns_out_qnty']+=$row[csf('trns_out_qnty')];

			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_rtn_qnty']+=$row[csf('today_issue_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_trans_in_qnty']+=$row[csf('today_trans_in_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_qnty']+=$row[csf('today_issue_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_rcv_rtn_qnty']+=$row[csf('today_rcv_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_trns_out_qnty']+=$row[csf('today_trns_out_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['prod_ids'].=$row[csf('prod_id')].',';

			if($row[csf('store_id')]*1!="" || $row[csf('store_id')]*1>0)
				$store_ids_arr[$row[csf('store_id')]] = $row[csf('store_id')];

			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["receive_qnty"] += $row[csf('receive_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_rtn_qnty"] += $row[csf('issue_rtn_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trans_in_qnty"] += $row[csf('trans_in_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_rcv_rtn_qnty"] += $row[csf('issue_rcv_rtn_qnty')];
		}
		/*echo "<pre>";
		print_r($style_wise_arr);
		die;*/
		$colorIds = implode(",", $color_id_arr);
		$prodIds = implode(",", $prod_id_arr);
		$poIds = implode(",", $po_id_arr);

		$prodIds 	= ($prodIds 	!= "") ? "and c.prod_id in ( $prodIds )" : "";
		$poIds 		= ($poIds 		!= "") ? "and c.po_breakdown_id in($poIds)" : "";

		// ======================================= FOR ISSUE QNTY ==============================================
		$sql_issue="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id,
		(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty
		from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d,inv_finish_fabric_issue_dtls f
		where  a.company_name=$cbo_company_id and a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and c.id=d.trans_id   and f.id=d.dtls_id and   d.entry_form in (7,14,15,18,37,46,52,66,68,71,126,134) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and f.status_active=1 and f.is_deleted=0 $shiping_status_cond $storeCond $receive_date $buyer_id_cond $search_cond $year_cond
		order by a.buyer_name,a.job_no, d.color_id,c.transaction_date";

		$style_wise_arr2=array();
		$sql_issue_res=sql_select($sql_issue);
		foreach ($sql_issue_res as $row)
		{
			//$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			//$fab_desc_tmp=$fab_desc_tmp1[0];

			$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
			$style_wise_arr2[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_qnty']+=$row[csf('issue_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_qnty"] += $row[csf('issue_qnty')];
		}

		// =================================== ISSUE TRANS IN-OUT QNTY ===============================
		$sql_trans="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=5 then d.quantity else 0 end) as trns_in_qnty
		from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details d, inv_transaction c,  inv_item_transfer_dtls e 
		where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and c.mst_id=e.mst_id and d.dtls_id=e.id and a.company_name=$cbo_company_id and d.entry_form in (14,15,134,306) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $shiping_status_cond $storeCond $receive_date $buyer_id_cond $search_cond $year_cond
		order by a.buyer_name,a.job_no, d.color_id,c.transaction_date";
		$sql_trans_res = sql_select($sql_trans);
		$trns_out_qnty_arr = array();
		foreach ($sql_trans_res as $row)
		{
			$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
			//$fab_desc_tmp  = $fab_desc_tmp1[0];
			$trns_out_qnty_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]+=$row[csf('trns_out_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trns_out_qnty"] += $row[csf('trns_out_qnty')];
			$trns_in_qnty_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]+=$row[csf('trns_in_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trns_in_qnty"] += $row[csf('trns_in_qnty')];
		}

		$booking_qnty=array();
		$job_no_arr = array_filter($job_no_arr);
		if(!empty($job_no_arr))
		{
			$job_no_arr = array_filter($job_no_arr);
			if($db_type==2 && count($job_no_arr)>999)
			{
				$job_no_arr_chunk=array_chunk($job_no_arr,999) ;
				foreach($job_no_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$jobCond.="  b.job_no in($chunk_arr_value) or ";
				}

				$all_job_no_cond.=" and (".chop($jobCond,'or ').")";
			}
			else
			{
				$all_job_no_cond=" and b.job_no in(".implode(",",$job_no_arr).")";
			}

			$sql_booking=sql_select("select b.po_break_down_id as po_id,b.fabric_color_id,b.job_no,b.construction,b.fabric_color_id, b.fin_fab_qnty,b.is_short,c.construction short_construction
				from wo_booking_mst a, wo_booking_dtls b
				left join wo_pre_cost_fabric_cost_dtls c on b.pre_cost_fabric_cost_dtls_id=c.id and c.status_active=1 and c.is_deleted=0
				where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $all_job_no_cond ");
			//$all_po_id_cond

			foreach( $sql_booking as $row)
			{
				$construction = ($row[csf('is_short')]==1)?$row[csf('short_construction')]:$row[csf('construction')];
				$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$construction]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
				$job_order_no_arr[$row[csf('job_no')]][] = $row[csf('po_id')];
			}
			unset($sql_booking);
		}

		if($colorIds!=""){
			$color_arr=return_library_array( "select id,color_name from lib_color where id in($colorIds)", "id", "color_name"  );
		}

		$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$store_width    = 120;
		$table_width 	= 2160+count($store_ids_arr)*$store_width;

		//check job wise all po shipment status..............
		$sql_check_shipmentStatus=sql_select("select b.id,b.po_number,b.shiping_status
						from  pro_ex_factory_mst a, wo_po_break_down b
						where b.id=a.po_break_down_id and a.status_active=1 and a.is_deleted=0");
		foreach ($sql_check_shipmentStatus as $row) {
			$shipingStatus_arr[$row[csf('id')]]['shiping_status']=$row[csf('shiping_status')];
		}
		?>
		<style type="text/css">
			.nsbreak{word-break: break-all;}
		</style>

		<fieldset style="width:2180px;">
			<table cellpadding="0" cellspacing="0" width="1960">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="<? echo $table_width; ?>px" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="100" rowspan="2">Buyer</th>
						<th width="60" rowspan="2">Job</th>
						<th width="50" rowspan="2">Year</th>
						<th width="110" rowspan="2">Style</th>
						<th width="100" rowspan="2">Internal Ref.</th>

						<th width="60" rowspan="2">Order Status</th>
						<th width="100" rowspan="2">Shipment Status</th>
						<th width="110" rowspan="2">Fin. Fab. Color</th>
						<th width="120" rowspan="2">Fabric Type</th>

						<th width="80" rowspan="2">Req. Qty</th>
						<th width="240" colspan="3">Today Recv.</th>
						<th width="240" title="Rec.+Issue Ret.+Trans. in" colspan="3">Total Received</th>

						<th width="80" title="Req.-Totat Rec." rowspan="2">Received Balance</th>
						<th width="240" colspan="3">Today Issue</th>
						<th width="240" title="Issue+Rec. Ret.+Trans. out" colspan="3">Total Issued</th>
						<th width="100" title="Total Rec.- Total Issue" rowspan="2">Stock</th>
						<?
						foreach ($store_ids_arr as $store_id) {
							?>
							<th width="<? echo $store_width; ?>" title="Store ID = <? echo $store_id; ?>" rowspan="2"><? echo $store_arr[$store_id]; ?></th>
							<?
						}
						?>
					</tr>
					<tr>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $table_width+20; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table nsbreak" id="table_body" >
					<?
					$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$total_today_issue=0;$total_today_recv=0;
					$fin_color_array=array(); $fin_color_data_arr=array();
					foreach ($style_wise_arr  as $job_key=>$job_val)
					{
						foreach ($job_val  as $color_key=>$color_val)
						{
							foreach ($color_val  as $desc_key=>$val)
							{
								$prod_ids=rtrim($val['prod_ids'],',');
								$prod_ids=implode(",",array_filter(array_unique(explode(",",$prod_ids))));
								$color_id=$row[csf("color_id")];
								$fab_desc_type=$desc_key;
								$po_nos=rtrim($val['po_no'],',');
								$po_nos=implode(",",array_filter(array_unique(explode(",",$po_nos))));
								$grouping=implode(",",array_filter(array_unique(explode(",",chop($val['grouping'],",")))));
								$poids=rtrim($val['po_id'],',');

								$po_ids=array_filter(array_unique(explode(",",$poids)));
								$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
								/*foreach($po_ids as $po_id)
								{
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$desc_key]['fin_fab_qnty'];
								}*/
								$order_no_arr=array_filter(array_unique($job_order_no_arr[$job_key]));
								$order_nos="";
								foreach($order_no_arr as $po_id)
								{
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$desc_key]['fin_fab_qnty'];
								}
								$order_nos = implode(",", $order_no_arr); //N.B. this $order_nos only for showing required quantity

								$po_ids=implode(",",$po_ids);
								$today_recv=$val[("today_receive_qnty")];
								$today_rtn_qnty=$val[("today_issue_rtn_qnty")];
								$today_trans_in_qnty=$val[("today_trans_in_qnty")];


								$today_issue=$val[("today_issue_qnty")];
								$today_issue_rcv_rtn_qnty=$val[("today_rcv_rtn_qnty")];
								$today_issue_trns_out_qnty=$val[("today_trns_out_qnty")];

								$rec_qty = $val[("receive_qnty")];
								$rec_ret_qty = $val[("issue_rtn_qnty")];
								//$rec_trns_qty = $val[("trans_in_qnty")];
								$rec_trns_qty = $trns_in_qnty_arr[$job_key][$color_key][$desc_key];

								$rec_qty_cal=($rec_qty+$rec_ret_qty+$rec_trns_qty);

								$iss_qty = $style_wise_arr2[$job_key][$color_key][$desc_key]['issue_qnty'];
								$iss_ret_qty=$val[("issue_rcv_rtn_qnty")];

								$iss_trns_qty = $trns_out_qnty_arr[$job_key][$color_key][$desc_key];
								$iss_qty_cal=($iss_qty+$iss_ret_qty+$iss_trns_qty);


								$popup_ref_data = $val[("buyer_name")]."_".$val[("job_no_pre")]."_".$val[("year")]."_".$val[("style_ref_no")]."_".$grouping."_".$desc_key;
								$stock_check=$rec_qty_cal-$iss_qty_cal;
								if($cbo_value_range_by==2 &&  number_format($stock_check,2,'.','')>0.00)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
										<td width="100"><p><? echo $grouping; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>">
											<?
											$po_ids_exp=explode(",", $po_ids);
											$poId_count=0; $partial_shipSts_count=0;
											foreach ($po_ids_exp as $row) {
												if($po_id_shipingStatus_arr[$row]==3)
												{
													$partial_shipSts_count++;
												}
												$poId_count++;
											}
											if($partial_shipSts_count==$poId_count){
												?>
												<a style="color:red;" href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											else
											{
												?>
												<a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											?>
										</td>
										<td width="100" title="<? echo $po_ids;?>"><p>
											<? 
												$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
												foreach ($po_ids_exp as $row) {
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$full_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==2){
														$partial_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==1){
														$panding_shipSts_countx++;
													}
													$poId_countx++;
												}
												if($full_shipSts_countx==$poId_countx){
													$ShipingStatus="Full Delivery/Closed";
												}
												else if ($partial_shipSts_countx==$poId_countx) {
													$ShipingStatus="Partial Delivery";
												}
												else if ($panding_shipSts_countx==$poId_countx) {
													$ShipingStatus="Full Pending";
												}
												else
												{
													$ShipingStatus="Partial Delivery";
												}
												echo $ShipingStatus;
											?></p></td>
										<td width="110" title="<? echo $color_key;?>"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="120" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?> </p></td>
										<td width="80" align="right" title="<? echo $job_key."==".$color_key."==".$desc_key."==".$po_ids;?>"><p>
											<a href='#report_details' onClick="openmypage_req_qnty('<? echo $order_nos; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo $job_key; ?>','<? echo $order_nos; ?>','<? echo $desc_key; ?>','<? echo 1; ?>','req_qnty_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</a>
											</p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','today_total_rec_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_recv,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_rtn_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_trans_in_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_trans_in_qnty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','total_receive_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_ret_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_trns_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_trns_qty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty_cal; echo number_format($rec_bal,2,'.','');?></p></td>
										<td width="80" align="right">
											<p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a>
											</p>
										</td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_rcv_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_trns_out_qnty,2,'.',''); ?></a></p>
										</td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_trns_qty,2,'.',''); ?></a></p></td>

										<td width="100" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_rtn_qnty."##".$rec_trns_qty."Issue: ".$val[("issue_qnty")]."##".$issue_rcv_rtn_qnty."##".$val[("trns_out_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','knit_stock_popup','<? echo $popup_ref_data; ?>');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a></p>
										</td>

										<?
										$store_receive=0;
										foreach ($store_ids_arr as $store_id) {
											$store_receive 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["receive_qnty"],2,".","");
											$store_issue_rtn 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rtn_qnty"],2,".","");
											$store_trans_in 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_in_qnty"],2,".","");

											$store_rcv_rtn 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rcv_rtn_qnty"],2,".","");
											$store_trns_out 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_out_qnty"],2,".","");
											$store_issue_qnty    = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_qnty"],2,".","");

											$store_balance = number_format(($store_receive+$store_issue_rtn+$store_trans_in) - ($store_rcv_rtn+$store_trns_out+$store_issue_qnty),2,".","");
											$store_balance_title = "Receive=$store_receive, Transfer In=$store_trans_in, Issue Return=$store_issue_rtn \nIssue=$store_issue_qnty, Transfer Out=$store_trns_out, Receive Return=$store_rcv_rtn";
											?>
											<td width="<? echo $store_width; ?>" title="<? echo $store_balance_title; ?>" align="right"><? echo $store_balance; ?></td>
											<?
											$store_wise_total_stock[$store_id] += $store_balance;
										}
										?>
									</tr>
									<?
									$i++;

									$total_order_qnty+=$val[("po_quantity")];
									$total_req_qty+=$book_qty;

									$total_rec_qty+=$rec_qty;
									$total_rec_ret_qty+=$rec_ret_qty;
									$total_rec_trns_qty+=$rec_trns_qty;

									$total_rec_bal+=$rec_bal;

									$total_issue_qty+=$iss_qty;
									$total_issue_ret_qty+=$iss_ret_qty;
									$total_issue_trns_qty+=$iss_trns_qty;

									$total_stock+=$stock;

									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;

									$total_today_issue+=$today_issue;
									$total_today_issue_rcv_rtn_qnty+=$today_issue_rcv_rtn_qnty;
									$total_today_issue_trns_out_qnty+=$today_issue_trns_out_qnty;

									$total_today_recv+=$today_recv;
									$total_today_rtn_qnty+=$today_rtn_qnty;
									$total_today_trans_in_qnty+=$today_trans_in_qnty;
								}
								else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
										<td width="100"><p><? echo $grouping; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>">
											<?
											$po_ids_exp=explode(",", $po_ids);
											$poId_count=0; $partial_shipSts_count=0;
											foreach ($po_ids_exp as $row) {
												if($po_id_shipingStatus_arr[$row]==3)
												{
													$partial_shipSts_count++;
												}
												$poId_count++;
											}
											if($partial_shipSts_count==$poId_count){
												?>
												<a style="color:red;" href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											else
											{
												?>
												<a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											?>
										</td>
										<td width="100" title="<? echo $po_ids;?>"><p>
											<? 
												$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
												foreach ($po_ids_exp as $row) {
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$full_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==2){
														$partial_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==1){
														$panding_shipSts_countx++;
													}
													$poId_countx++;
												}
												if($full_shipSts_countx==$poId_countx){
													$ShipingStatus="Full Delivery/Closed";
												}
												else if ($partial_shipSts_countx==$poId_countx) {
													$ShipingStatus="Partial Delivery";
												}
												else if ($panding_shipSts_countx==$poId_countx) {
													$ShipingStatus="Full Pending";
												}
												else
												{
													$ShipingStatus="Partial Delivery";
												}
												echo $ShipingStatus;
											?></p></td>
										<td width="110" title="<? echo $color_key;?>"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="120" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?> </p></td>
										<td width="80" align="right" title="<? echo $job_key."==".$color_key."==".$desc_key."==".$po_ids;?>"><p>
												<a href='#report_details' onClick="openmypage_req_qnty('<? echo $order_nos; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo $job_key; ?>','<? echo $order_nos; ?>','<? echo $desc_key; ?>','<? echo 1; ?>','req_qnty_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</a>
										</p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','today_total_rec_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_recv,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_rtn_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_trans_in_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_trans_in_qnty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','total_receive_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_ret_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_trns_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_trns_qty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty_cal; echo number_format($rec_bal,2,'.','');?></p></td>
										<td width="80" align="right">
											<p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a>
											</p>
										</td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_rcv_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_trns_out_qnty,2,'.',''); ?></a></p>
										</td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_trns_qty,2,'.',''); ?></a></p></td>

										<td width="100" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_rtn_qnty."##".$trns_in_qnty."Issue: ".$val[("issue_qnty")]."##".$issue_rcv_rtn_qnty."##".$val[("trns_out_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','knit_stock_popup','<? echo $popup_ref_data; ?>');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a></p>
										</td>

										<?
										$store_receive=0;
										foreach ($store_ids_arr as $store_id) {
											$store_receive 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["receive_qnty"],2,".","");
											$store_issue_rtn 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rtn_qnty"],2,".","");
											$store_trans_in 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_in_qnty"],2,".","");

											$store_rcv_rtn 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rcv_rtn_qnty"],2,".","");
											$store_trns_out 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_out_qnty"],2,".","");
											$store_issue_qnty    = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_qnty"],2,".","");

											$store_balance = number_format(($store_receive+$store_issue_rtn+$store_trans_in) - ($store_rcv_rtn+$store_trns_out+$store_issue_qnty),2,".","");
											$store_balance_title = "Receive=$store_receive, Transfer In=$store_trans_in, Issue Return=$store_issue_rtn \nIssue=$store_issue_qnty, Transfer Out=$store_trns_out, Receive Return=$store_rcv_rtn";
											?>
											<td width="<? echo $store_width; ?>" title="<? echo $store_balance_title; ?>" align="right"><? echo $store_balance; ?></td>
											<?
											$store_wise_total_stock[$store_id] += $store_balance;
										}
										?>
									</tr>
									<?
									$i++;

									$total_order_qnty+=$val[("po_quantity")];
									$total_req_qty+=$book_qty;

									$total_rec_qty+=$rec_qty;
									$total_rec_ret_qty+=$rec_ret_qty;
									$total_rec_trns_qty+=$rec_trns_qty;

									$total_rec_bal+=$rec_bal;

									$total_issue_qty+=$iss_qty;
									$total_issue_ret_qty+=$iss_ret_qty;
									$total_issue_trns_qty+=$iss_trns_qty;

									$total_stock+=$stock;

									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;

									$total_today_issue+=$today_issue;
									$total_today_issue_rcv_rtn_qnty+=$today_issue_rcv_rtn_qnty;
									$total_today_issue_trns_out_qnty+=$today_issue_trns_out_qnty;

									$total_today_recv+=$today_recv;
									$total_today_rtn_qnty+=$today_rtn_qnty;
									$total_today_trans_in_qnty+=$today_trans_in_qnty;
								}
							}
						}
					}
					?>
				</table>
			</div>
			<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<tfoot>
					<th width="30"></th>
					<th width="100"></th>
					<th width="60"></th>
					<th width="50"></th>
					<th width="110">&nbsp;</th>
					<th width="100">&nbsp;</th>

					<th width="60">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="120">Total</th>
					<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?>10</th>
					<th width="80" align="right" id="value_total_today_rtn_qnty"><? echo number_format($total_today_rtn_qnty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_today_trans_in_qnty"><? echo number_format($total_today_trans_in_qnty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_rec_ret_qty"><? echo number_format($total_rec_ret_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_rec_trns_qty"><? echo number_format($total_rec_trns_qty,2,'.',''); ?></th>

					<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>

					<th width="80"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
					<th width="80"  id="value_total_today_issue_rcv_rtn_qty"><? echo number_format($total_today_issue_rcv_rtn_qnty,2,'.',''); ?></th>
					<th width="80"  id="value_recv_total_today_issue_trns_out_qnty"><? echo number_format($total_today_issue_trns_out_qnty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_issue_ret_qty"><? echo number_format($total_issue_ret_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_issue_trns_qty"><? echo number_format($total_issue_trns_qty,2,'.',''); ?></th>

					<th width="100" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
					<?
					foreach ($store_ids_arr as $store_id) {
						?>
						<th width="<? echo $store_width; ?>" id="value_total_store_qty"><? echo number_format($store_wise_total_stock[$store_id],2,".",""); ?></th>
						<?
					}
					?>
				</tfoot>
			</table>
		</fieldset>
		<?
	}
	//Knit end
	else if($cbo_report_type==2) // Woven Finish Start
	{
		$product_array=array();
		$sql_product="select id, color, detarmination_id,dia_width,weight,product_name_details from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0";
		$sql_product_result=sql_select($sql_product);
		foreach( $sql_product_result as $row )
		{
			$product_array[$row[csf('product_name_details')]]['color']=$row[csf('color')];
			$product_array[$row[csf('product_name_details')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('product_name_details')]]['weight']=$row[csf('weight')];
			$product_array[$row[csf('product_name_details')]]['yarn_did']=$row[csf('detarmination_id')];
		}

		$issue_qnty=array();
		$sql_issue=sql_select(" select b.po_breakdown_id,b.color_id, sum(b.quantity) as issue_qnty  from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.issue_purpose in(4,9) and b.entry_form=19 group by b.po_breakdown_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		} //var_dump($issue_qnty);

		$booking_qnty=array();
		//$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		foreach( $sql_booking as $row)
		{
			$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
		}
		unset($sql_booking);

		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

		?>
		<fieldset style="width:1450px;">
			<table cellpadding="0" cellspacing="0" width="1210">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="1420" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Buyer</th>
						<th width="60">Job</th>
						<th width="50">Year</th>
						<th width="110">Style</th>

						<th width="60">Order Status</th>
						<th width="100">Shipment Status</th>
						<th width="110">Fin. Fab. Color</th>
						<th width="220">Fab. Desc.</th>

						<th width="80">Req. Qty</th>
						<th width="80">Today Recv.</th>
						<th width="80" title="Rec.+Issue Ret.+Trans. in">Total Received</th>

						<th width="80" title="Req.-Totat Rec.">Received Balance</th>
						<th width="80">Today Issue</th>
						<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>
						<th width="" title="Total Rec.- Total Issue">Stock</th>
					</tr>
				</thead>
			</table>
			<div style="width:1440px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1420" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					if($db_type==0) $prod_cond="group_concat(e.prod_id) as prod_id";
					else if($db_type==2) $prod_cond="listagg(cast(e.prod_id as varchar2(4000)),',') within group (order by e.prod_id) as prod_id";

					$sql_query="SELECT a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id,c.balance_qnty, $select_fld, e.product_name_details as prod_desc,b.shiping_status,

					(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) as finish_fabric_transfer_in,					
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end) as issue_rtn,

					(case when d.entry_form in (17) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,					
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in,
					(case when d.entry_form in (209)  and c.transaction_type=4 and d.trans_type=4  and $today_receive_date then d.quantity else 0 end) as today_issue_rtn,

					(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty,
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as finish_fabric_transfer_out,
					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end) as recv_rtn,

  					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 and $today_receive_date then d.quantity else 0 end) as today_recv_rtn,
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trans_out,
					(case when d.entry_form in (19) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty

					from  wo_po_details_master a, wo_po_break_down b,inv_transaction c,  order_wise_pro_details d,product_details_master e 
					where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and  d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id and d.entry_form in (17,19,258,209,202) and d.trans_id!=0 and d.entry_form in (17,19,258,209,202)  and c.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$cbo_company_id $receive_date $buyer_id_cond $shiping_status_cond $search_cond $year_cond
					order by a.job_no,d.color_id,c.transaction_date";
					// echo $sql_query;
					$style_wise_arr=array();
					$nameArray=sql_select($sql_query);
					foreach ($nameArray as $row)
					{
						$po_id_shipingStatus_arr[$row[csf('po_id')]]=$row[csf('shiping_status')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no']=$row[csf('job_no')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['year']=$row[csf('year')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['buyer_name']=$row[csf('buyer_name')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['style_ref_no']=$row[csf('style_ref_no')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_id'].=$row[csf('po_id')].',';
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_no'].=$row[csf('po_no')].',';
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_qnty']+=$row[csf('receive_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_qnty']+=$row[csf('issue_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn']+=$row[csf('issue_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn']+=$row[csf('recv_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['balance_qnty']+=$row[csf('balance_qnty')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_in']+=$row[csf('today_trans_in')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_out']+=$row[csf('today_trans_out')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_rtn']+=$row[csf('today_issue_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_recv_rtn']+=$row[csf('today_recv_rtn')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];
					}

					$i=1;
					foreach ($style_wise_arr  as $job_key=>$job_val)
					{
						foreach ($job_val  as $color_key=>$color_val)
						{
							foreach ($color_val  as $desc_key=>$val)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$dzn_qnty=0;
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;

								$color_id=$row[csf("color_id")];
								//$fab_desc_type=$product_arr[$desc_key];
								$fab_desc_type=$desc_key;
								$po_nos=rtrim($val['po_no'],',');
								$po_nos=implode(",",array_unique(explode(",",$po_nos)));
								$poids=rtrim($val['po_id'],',');

								$po_ids=array_unique(explode(",",$poids));
								$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
								foreach($po_ids as $po_id)
								{
									//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."=".$product_array[$desc_key]['dia_width']."=".$product_array[$desc_key]['fin_fab_qnty']."<br>";
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['fin_fab_qnty'];
										//echo $job_key.'ii'.$po_id;
									$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
									$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
								}

									//echo $job_key.'ii';
								$po_ids=implode(",",$po_ids);
								$today_recv=$val[("today_receive_qnty")]+$val[("today_trans_in")]+$val[("today_issue_rtn")];
								$today_issue=$val[("today_issue_qnty")]+$val[("today_trans_out")]+$val[("today_recv_rtn")];
								$rec_qty=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);
								//$rec_qty_cal=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);

								$iss_qty=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
								//$iss_qty_cal=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
								$stock_check=($rec_qty-$iss_qty);

								if($cbo_value_range_by==2 &&  number_format($stock_check,2,'.','')>0.00)
								{

									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
										<td width="100" title="<? echo $po_ids;?>"><p>
											<? 
												$po_ids_exp=explode(",", $po_ids);
												$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
												foreach ($po_ids_exp as $row) 
												{
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$full_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==2){
														$partial_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==1){
														$panding_shipSts_countx++;
													}
													$poId_countx++;
												}
												if($full_shipSts_countx==$poId_countx){
													$ShipingStatus="Full Delivery/Closed";
												}
												else if ($partial_shipSts_countx==$poId_countx) {
													$ShipingStatus="Partial Delivery";
												}
												else if ($panding_shipSts_countx==$poId_countx) {
													$ShipingStatus="Full Pending";
												}
												else
												{
													$ShipingStatus="Partial Delivery";
												}
												echo $ShipingStatus;
											?></p></td>
										<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
										<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>


										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><?
										$rec_bal=$book_qty-$rec_qty;
										//$rec_bal=$val[("balance_qnty")];
										echo number_format($rec_bal,2,'.','');

										?></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_issue_popup');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>

										<td width="" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','woven_knit_stock_popup');"><?
										//$stock=$rec_qty_cal-$iss_qty_cal; old
										$stock=($rec_qty-$iss_qty); //new $rec_qty
										echo number_format($stock,2,'.','');
										?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>

									</tr>
									<?
									$i++;

									$total_req_qty+=$book_qty;
									$total_rec_qty+=$rec_qty;
									$total_rec_bal+=$rec_bal;
									$total_issue_qty+=$iss_qty;
									$total_stock+=$stock;
									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;
									$total_today_issue+=$today_issue;
									$total_today_recv+=$today_recv;
								}
								else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
										<td width="100" title="<? echo $po_ids;?>"><p>
											<? 
												$po_ids_exp=explode(",", $po_ids);
												$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
												foreach ($po_ids_exp as $row) 
												{
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$full_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==2){
														$partial_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==1){
														$panding_shipSts_countx++;
													}
													$poId_countx++;
												}
												if($full_shipSts_countx==$poId_countx){
													$ShipingStatus="Full Delivery/Closed";
												}
												else if ($partial_shipSts_countx==$poId_countx) {
													$ShipingStatus="Partial Delivery";
												}
												else if ($panding_shipSts_countx==$poId_countx) {
													$ShipingStatus="Full Pending";
												}
												else
												{
													$ShipingStatus="Partial Delivery";
												}
												echo $ShipingStatus;
											?></p></td>
										<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
										<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>


										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><?
										$rec_bal=$book_qty-$rec_qty;
										//$rec_bal=$val[("balance_qnty")];
										echo number_format($rec_bal,2,'.','');

										?></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_issue_popup');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>

										<td width="" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','woven_knit_stock_popup');"><?
										//$stock=$rec_qty_cal-$iss_qty_cal; old
										$stock=($rec_qty-$iss_qty); //new $rec_qty
										echo number_format($stock,2,'.','');
										?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>

									</tr>
									<?
									$i++;

									$total_req_qty+=$book_qty;
									$total_rec_qty+=$rec_qty;
									$total_rec_bal+=$rec_bal;
									$total_issue_qty+=$iss_qty;
									$total_stock+=$stock;
									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;
									$total_today_issue+=$today_issue;
									$total_today_recv+=$today_recv;
								}
							}
						}
					}
					?>
				</table>
				<table width="1420" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="50"></th>
						<th width="110">&nbsp;</th>

						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="220">Total</th>
						<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>

						<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
						<th width="80"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th width="" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}

if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_report_type 	= str_replace("'","",$cbo_report_type);
	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_comm 	= str_replace("'","",$txt_search_comm);
	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);
	$cbo_shipment_status = str_replace("'","",$cbo_shipment_status);
	if($cbo_shipment_status>0){$shiping_status_cond=" and b.shiping_status in ($cbo_shipment_status)";}

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '$txt_search_comm'";
	}
	else
	{
		$search_cond.="";
	}

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	if($db_type==0)
	{
		$select_fld= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	ob_start();

	if($cbo_report_type==1)
	{
		// Knit Finish Start
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		//$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2", "id", "product_name_details");
		$product_arr=return_library_array("select a.id, b.construction from product_details_master a, lib_yarn_count_determina_mst b where a.item_category_id=2 and a.detarmination_id=b.id", "id", "construction");

		$sql_rcv="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no,b.shiping_status, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id,b.grouping,
		(case when d.entry_form in (7,37,66,68) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
		(case when d.entry_form in (52,126) and $today_receive_date then d.quantity else 0 end) as today_issue_rtn_qnty,
		(case when d.entry_form in (14,15,134,306) and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in_qnty,
		(case when d.entry_form in (7,37,66,68)  then d.quantity else 0 end) as receive_qnty,
		(case when d.entry_form in (52,126) then d.quantity else 0 end) as issue_rtn_qnty,
		(case when d.entry_form in (14,15,134,306) and d.trans_type=5 then d.quantity else 0 end) as trans_in_qnty,

		(case when d.entry_form in (18,71) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty,
		(case when d.entry_form in (46) and $today_receive_date then d.quantity else 0 end) as today_rcv_rtn_qnty,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trns_out_qnty,
		(case when d.entry_form in (46) then d.quantity else 0 end) as issue_rcv_rtn_qnty,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty

		from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
		where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id  and a.company_name=$cbo_company_id and d.entry_form in (7,14,15,18,37,46,52,66,68,71,126,134,306) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $shiping_status_cond $receive_date $buyer_id_cond $search_cond $year_cond
		order by a.job_no, d.color_id,c.transaction_date,b.grouping";

		$style_wise_arr=$color_id_arr=$prod_id_arr=$po_id_arr=array();

		$sql_rcv_res=sql_select($sql_rcv);
		foreach ($sql_rcv_res as $row)
		{
			$color_id_arr[$row[csf('color_id')]]=$row[csf('color_id')];
			$po_id_shipingStatus_arr[$row[csf('po_id')]]=$row[csf('shiping_status')];
			$prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
			$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$job_no_arr[$row[csf('job_no')]] = "'".$row[csf('job_no')]."'";

			$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
			//$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			//$fab_desc_tmp=$fab_desc_tmp1[0];

			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no']=$row[csf('job_no')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no_pre']=$row[csf('job_no_prefix_num')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['year']=$row[csf('year')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['buyer_name']=$row[csf('buyer_name')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['style_ref_no']=$row[csf('style_ref_no')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['grouping'].=$row[csf('grouping')].",";
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_id'].=$row[csf('po_id')].',';
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_no'].=$row[csf('po_no')].',';
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['receive_qnty']+=$row[csf('receive_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_rtn_qnty']+=$row[csf('issue_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['trans_in_qnty']+=$row[csf('trans_in_qnty')];

			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_rcv_rtn_qnty']+=$row[csf('issue_rcv_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['trns_out_qnty']+=$row[csf('trns_out_qnty')];

			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_rtn_qnty']+=$row[csf('today_issue_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_trans_in_qnty']+=$row[csf('today_trans_in_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_qnty']+=$row[csf('today_issue_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_rcv_rtn_qnty']+=$row[csf('today_rcv_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_trns_out_qnty']+=$row[csf('today_trns_out_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['prod_ids'].=$row[csf('prod_id')].',';

			if($row[csf('store_id')]*1!="" || $row[csf('store_id')]*1>0)
				$store_ids_arr[$row[csf('store_id')]] = $row[csf('store_id')];

			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["receive_qnty"] += $row[csf('receive_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_rtn_qnty"] += $row[csf('issue_rtn_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trans_in_qnty"] += $row[csf('trans_in_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_rcv_rtn_qnty"] += $row[csf('issue_rcv_rtn_qnty')];
		}
		/*echo "<pre>";
		print_r($style_wise_arr);
		die;*/
		$colorIds = implode(",", $color_id_arr);
		$prodIds = implode(",", $prod_id_arr);
		$poIds = implode(",", $po_id_arr);

		$prodIds 	= ($prodIds 	!= "") ? "and c.prod_id in ( $prodIds )" : "";
		$poIds 		= ($poIds 		!= "") ? "and c.po_breakdown_id in($poIds)" : "";

		// ======================================= FOR ISSUE QNTY ==============================================
		$sql_issue="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id,
		(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty
		from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d,inv_finish_fabric_issue_dtls f
		where  a.company_name=$cbo_company_id and a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and c.id=d.trans_id   and f.id=d.dtls_id and   d.entry_form in (7,14,15,18,37,46,52,66,68,71,126,134) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and f.status_active=1 and f.is_deleted=0 $shiping_status_cond $receive_date $buyer_id_cond $search_cond $year_cond
		order by a.job_no, d.color_id,c.transaction_date";

		$style_wise_arr2=array();
		$sql_issue_res=sql_select($sql_issue);
		foreach ($sql_issue_res as $row)
		{
			//$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			//$fab_desc_tmp=$fab_desc_tmp1[0];

			$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
			$style_wise_arr2[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_qnty']+=$row[csf('issue_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_qnty"] += $row[csf('issue_qnty')];
		}

		// =================================== ISSUE TRANS QNTY ===============================
		$sql_trans="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty
		from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
		where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and a.company_name=$cbo_company_id and d.entry_form in (14,15,134,306) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $shiping_status_cond $receive_date $buyer_id_cond $search_cond $year_cond
		order by a.job_no, d.color_id,c.transaction_date";
		$sql_trans_res = sql_select($sql_trans);
		$trns_out_qnty_arr = array();
		foreach ($sql_trans_res as $row)
		{
			//$fab_desc_tmp1 = explode(",",$product_arr[$row[csf('prod_id')]]);
			//$fab_desc_tmp  = $fab_desc_tmp1[0];
			$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
			$trns_out_qnty_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]+=$row[csf('trns_out_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trns_out_qnty"] += $row[csf('trns_out_qnty')];
		}

		$booking_qnty=array();
		$job_no_arr = array_filter($job_no_arr);
		if(!empty($job_no_arr))
		{
			$job_no_arr = array_filter($job_no_arr);
			if($db_type==2 && count($job_no_arr)>999)
			{
				$job_no_arr_chunk=array_chunk($job_no_arr,999) ;
				foreach($job_no_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$jobCond.="  b.job_no in($chunk_arr_value) or ";
				}

				$all_job_no_cond.=" and (".chop($jobCond,'or ').")";
			}
			else
			{
				$all_job_no_cond=" and b.job_no in(".implode(",",$job_no_arr).")";
			}

			$sql_booking=sql_select("select b.po_break_down_id as po_id,b.fabric_color_id,b.job_no,b.construction,b.fabric_color_id, b.fin_fab_qnty,b.is_short,c.construction short_construction
				from wo_booking_mst a, wo_booking_dtls b
				left join wo_pre_cost_fabric_cost_dtls c on b.pre_cost_fabric_cost_dtls_id=c.id and c.status_active=1 and c.is_deleted=0
				where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $all_job_no_cond ");
			//$all_po_id_cond

			foreach( $sql_booking as $row)
			{
				$construction = ($row[csf('is_short')]==1)?$row[csf('short_construction')]:$row[csf('construction')];
				$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$construction]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
				$job_order_no_arr[$row[csf('job_no')]][] = $row[csf('po_id')];
			}
			unset($sql_booking);
		}

		if($colorIds!=""){
			$color_arr=return_library_array( "select id,color_name from lib_color where id in($colorIds)", "id", "color_name"  );
		}

		$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$store_width    = 120;
		$table_width 	= 2160+count($store_ids_arr)*$store_width;

		//check job wise all po shipment status..............
		$sql_check_shipmentStatus=sql_select("select b.id,b.po_number,b.shiping_status
						from  pro_ex_factory_mst a, wo_po_break_down b
						where b.id=a.po_break_down_id and a.status_active=1 and a.is_deleted=0");
		foreach ($sql_check_shipmentStatus as $row) {
			$shipingStatus_arr[$row[csf('id')]]['shiping_status']=$row[csf('shiping_status')];
		}
		?>
		<style type="text/css">
			.nsbreak{word-break: break-all;}
		</style>

		<fieldset style="width:2180px;">
			<table cellpadding="0" cellspacing="0" width="1960">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="<? echo $table_width; ?>px" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="100" rowspan="2">Buyer</th>
						<th width="60" rowspan="2">Job</th>
						<th width="50" rowspan="2">Year</th>
						<th width="110" rowspan="2">Style</th>
						<th width="100" rowspan="2">Internal Ref.</th>

						<th width="60" rowspan="2">Order Status</th>
						<th width="100" rowspan="2">Shipment Status</th>
						<th width="110" rowspan="2">Fin. Fab. Color</th>
						<th width="120" rowspan="2">Fabric Type</th>

						<th width="80" rowspan="2">Req. Qty</th>
						<th width="240" colspan="3">Today Recv.</th>
						<th width="240" title="Rec.+Issue Ret.+Trans. in" colspan="3">Total Received</th>

						<th width="80" title="Req.-Totat Rec." rowspan="2">Received Balance</th>
						<th width="240" colspan="3">Today Issue</th>
						<th width="240" title="Issue+Rec. Ret.+Trans. out" colspan="3">Total Issued</th>
						<th width="100" title="Total Rec.- Total Issue" rowspan="2">Stock</th>
						<?
						foreach ($store_ids_arr as $store_id) {
							?>
							<th width="<? echo $store_width; ?>" title="Store ID = <? echo $store_id; ?>" rowspan="2"><? echo $store_arr[$store_id]; ?></th>
							<?
						}
						?>
					</tr>
					<tr>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $table_width+20; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table nsbreak" id="table_body" >
					<?
					$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$total_today_issue=0;$total_today_recv=0;
					$fin_color_array=array(); $fin_color_data_arr=array();
					foreach ($style_wise_arr  as $job_key=>$job_val)
					{
						foreach ($job_val  as $color_key=>$color_val)
						{
							foreach ($color_val  as $desc_key=>$val)
							{
								$prod_ids=rtrim($val['prod_ids'],',');
								$prod_ids=implode(",",array_filter(array_unique(explode(",",$prod_ids))));
								$color_id=$row[csf("color_id")];
								$fab_desc_type=$desc_key;
								$po_nos=rtrim($val['po_no'],',');
								$po_nos=implode(",",array_filter(array_unique(explode(",",$po_nos))));
								$grouping=implode(",",array_filter(array_unique(explode(",",chop($val['grouping'],",")))));
								$poids=rtrim($val['po_id'],',');

								$po_ids=array_filter(array_unique(explode(",",$poids)));
								$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
								/*foreach($po_ids as $po_id)
								{
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$desc_key]['fin_fab_qnty'];
								}*/
								$order_no_arr=array_filter(array_unique($job_order_no_arr[$job_key]));
								$order_nos="";
								foreach($order_no_arr as $po_id)
								{
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$desc_key]['fin_fab_qnty'];
								}
								$order_nos = implode(",", $order_no_arr); //N.B. this $order_nos only for showing required quantity

								$po_ids=implode(",",$po_ids);
								$today_recv=$val[("today_receive_qnty")];
								$today_rtn_qnty=$val[("today_issue_rtn_qnty")];
								$today_trans_in_qnty=$val[("today_trans_in_qnty")];


								$today_issue=$val[("today_issue_qnty")];
								$today_issue_rcv_rtn_qnty=$val[("today_rcv_rtn_qnty")];
								$today_issue_trns_out_qnty=$val[("today_trns_out_qnty")];

								$rec_qty = $val[("receive_qnty")];
								$rec_ret_qty = $val[("issue_rtn_qnty")];
								$rec_trns_qty = $val[("trans_in_qnty")];

								$rec_qty_cal=($rec_qty+$rec_ret_qty+$rec_trns_qty);

								$iss_qty = $style_wise_arr2[$job_key][$color_key][$desc_key]['issue_qnty'];
								$iss_ret_qty=$val[("issue_rcv_rtn_qnty")];

								$iss_trns_qty = $trns_out_qnty_arr[$job_key][$color_key][$desc_key];
								$iss_qty_cal=($iss_qty+$iss_ret_qty+$iss_trns_qty);


								$popup_ref_data = $val[("buyer_name")]."_".$val[("job_no_pre")]."_".$val[("year")]."_".$val[("style_ref_no")]."_".$grouping."_".$desc_key;
								$stock_check=$rec_qty_cal-$iss_qty_cal;
								if($cbo_value_range_by==2 &&  number_format($stock_check,2,'.','')>0.00)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
										<td width="100"><p><? echo $grouping; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>">
											<?
											$po_ids_exp=explode(",", $po_ids);
											$poId_count=0; $partial_shipSts_count=0;
											foreach ($po_ids_exp as $row) {
												if($po_id_shipingStatus_arr[$row]==3)
												{
													$partial_shipSts_count++;
												}
												$poId_count++;
											}
											if($partial_shipSts_count==$poId_count){
												?>
												<a style="color:red;" href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											else
											{
												?>
												<a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											?>
										</td>
										<td width="100" title="<? echo $po_ids;?>"><p>
											<? 
												$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
												foreach ($po_ids_exp as $row) {
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$full_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==2){
														$partial_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==1){
														$panding_shipSts_countx++;
													}
													$poId_countx++;
												}
												if($full_shipSts_countx==$poId_countx){
													$ShipingStatus="Full Delivery/Closed";
												}
												else if ($partial_shipSts_countx==$poId_countx) {
													$ShipingStatus="Partial Delivery";
												}
												else if ($panding_shipSts_countx==$poId_countx) {
													$ShipingStatus="Full Pending";
												}
												else
												{
													$ShipingStatus="Partial Delivery";
												}
												echo $ShipingStatus;
											?></p></td>
										<td width="110" title="<? echo $color_key;?>"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="120" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?> </p></td>
										<td width="80" align="right" title="<? echo $job_key."==".$color_key."==".$desc_key."==".$po_ids;?>"><p>
											<a href='#report_details' onClick="openmypage_req_qnty('<? echo $order_nos; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo $job_key; ?>','<? echo $order_nos; ?>','<? echo $desc_key; ?>','<? echo 1; ?>','req_qnty_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</a>
											</p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','today_total_rec_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_recv,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_rtn_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_trans_in_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_trans_in_qnty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','total_receive_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_ret_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_trns_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_trns_qty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty_cal; echo number_format($rec_bal,2,'.','');?></p></td>
										<td width="80" align="right">
											<p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a>
											</p>
										</td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_rcv_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_trns_out_qnty,2,'.',''); ?></a></p>
										</td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_trns_qty,2,'.',''); ?></a></p></td>

										<td width="100" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_rtn_qnty."##".$val[("trans_in_qnty")]."Issue: ".$val[("issue_qnty")]."##".$issue_rcv_rtn_qnty."##".$val[("trns_out_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','knit_stock_popup','<? echo $popup_ref_data; ?>');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a></p>
										</td>

										<?
										$store_receive=0;
										foreach ($store_ids_arr as $store_id) {
											$store_receive 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["receive_qnty"],2,".","");
											$store_issue_rtn 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rtn_qnty"],2,".","");
											$store_trans_in 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trans_in_qnty"],2,".","");

											$store_rcv_rtn 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rcv_rtn_qnty"],2,".","");
											$store_trns_out 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_out_qnty"],2,".","");
											$store_issue_qnty    = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_qnty"],2,".","");

											$store_balance = number_format(($store_receive+$store_issue_rtn+$store_trans_in) - ($store_rcv_rtn+$store_trns_out+$store_issue_qnty),2,".","");
											$store_balance_title = "Receive=$store_receive, Transfer In=$store_trans_in, Issue Return=$store_issue_rtn \nIssue=$store_issue_qnty, Transfer Out=$store_trns_out, Receive Return=$store_rcv_rtn";
											?>
											<td width="<? echo $store_width; ?>" title="<? echo $store_balance_title; ?>" align="right"><? echo $store_balance; ?></td>
											<?
											$store_wise_total_stock[$store_id] += $store_balance;
										}
										?>
									</tr>
									<?
									$i++;

									$total_order_qnty+=$val[("po_quantity")];
									$total_req_qty+=$book_qty;

									$total_rec_qty+=$rec_qty;
									$total_rec_ret_qty+=$rec_ret_qty;
									$total_rec_trns_qty+=$rec_trns_qty;

									$total_rec_bal+=$rec_bal;

									$total_issue_qty+=$iss_qty;
									$total_issue_ret_qty+=$iss_ret_qty;
									$total_issue_trns_qty+=$iss_trns_qty;

									$total_stock+=$stock;

									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;

									$total_today_issue+=$today_issue;
									$total_today_issue_rcv_rtn_qnty+=$today_issue_rcv_rtn_qnty;
									$total_today_issue_trns_out_qnty+=$today_issue_trns_out_qnty;

									$total_today_recv+=$today_recv;
									$total_today_rtn_qnty+=$today_rtn_qnty;
									$total_today_trans_in_qnty+=$today_trans_in_qnty;
								}
								else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
										<td width="100"><p><? echo $grouping; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>">
											<?
											$po_ids_exp=explode(",", $po_ids);
											$poId_count=0; $partial_shipSts_count=0;
											foreach ($po_ids_exp as $row) {
												if($po_id_shipingStatus_arr[$row]==3)
												{
													$partial_shipSts_count++;
												}
												$poId_count++;
											}
											if($partial_shipSts_count==$poId_count){
												?>
												<a style="color:red;" href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											else
											{
												?>
												<a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											?>
										</td>
										<td width="100" title="<? echo $po_ids;?>"><p>
											<? 
												$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
												foreach ($po_ids_exp as $row) {
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$full_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==2){
														$partial_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==1){
														$panding_shipSts_countx++;
													}
													$poId_countx++;
												}
												if($full_shipSts_countx==$poId_countx){
													$ShipingStatus="Full Delivery/Closed";
												}
												else if ($partial_shipSts_countx==$poId_countx) {
													$ShipingStatus="Partial Delivery";
												}
												else if ($panding_shipSts_countx==$poId_countx) {
													$ShipingStatus="Full Pending";
												}
												else
												{
													$ShipingStatus="Partial Delivery";
												}
												echo $ShipingStatus;
											?></p></td>
										<td width="110" title="<? echo $color_key;?>"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="120" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?> </p></td>
										<td width="80" align="right" title="<? echo $job_key."==".$color_key."==".$desc_key."==".$po_ids;?>"><p>
												<a href='#report_details' onClick="openmypage_req_qnty('<? echo $order_nos; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo $job_key; ?>','<? echo $order_nos; ?>','<? echo $desc_key; ?>','<? echo 1; ?>','req_qnty_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</a>
										</p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','today_total_rec_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_recv,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_rtn_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_trans_in_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_trans_in_qnty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','total_receive_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_ret_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_trns_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_trns_qty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty_cal; echo number_format($rec_bal,2,'.','');?></p></td>
										<td width="80" align="right">
											<p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a>
											</p>
										</td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_rcv_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_trns_out_qnty,2,'.',''); ?></a></p>
										</td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_trns_qty,2,'.',''); ?></a></p></td>

										<td width="100" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_rtn_qnty."##".$val[("trans_in_qnty")]."Issue: ".$val[("issue_qnty")]."##".$issue_rcv_rtn_qnty."##".$val[("trns_out_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','knit_stock_popup','<? echo $popup_ref_data; ?>');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a></p>
										</td>

										<?
										$store_receive=0;
										foreach ($store_ids_arr as $store_id) {
											$store_receive 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["receive_qnty"],2,".","");
											$store_issue_rtn 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rtn_qnty"],2,".","");
											$store_trans_in 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trans_in_qnty"],2,".","");

											$store_rcv_rtn 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rcv_rtn_qnty"],2,".","");
											$store_trns_out 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_out_qnty"],2,".","");
											$store_issue_qnty    = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_qnty"],2,".","");

											$store_balance = number_format(($store_receive+$store_issue_rtn+$store_trans_in) - ($store_rcv_rtn+$store_trns_out+$store_issue_qnty),2,".","");
											$store_balance_title = "Receive=$store_receive, Transfer In=$store_trans_in, Issue Return=$store_issue_rtn \nIssue=$store_issue_qnty, Transfer Out=$store_trns_out, Receive Return=$store_rcv_rtn";
											?>
											<td width="<? echo $store_width; ?>" title="<? echo $store_balance_title; ?>" align="right"><? echo $store_balance; ?></td>
											<?
											$store_wise_total_stock[$store_id] += $store_balance;
										}
										?>
									</tr>
									<?
									$i++;

									$total_order_qnty+=$val[("po_quantity")];
									$total_req_qty+=$book_qty;

									$total_rec_qty+=$rec_qty;
									$total_rec_ret_qty+=$rec_ret_qty;
									$total_rec_trns_qty+=$rec_trns_qty;

									$total_rec_bal+=$rec_bal;

									$total_issue_qty+=$iss_qty;
									$total_issue_ret_qty+=$iss_ret_qty;
									$total_issue_trns_qty+=$iss_trns_qty;

									$total_stock+=$stock;

									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;

									$total_today_issue+=$today_issue;
									$total_today_issue_rcv_rtn_qnty+=$today_issue_rcv_rtn_qnty;
									$total_today_issue_trns_out_qnty+=$today_issue_trns_out_qnty;

									$total_today_recv+=$today_recv;
									$total_today_rtn_qnty+=$today_rtn_qnty;
									$total_today_trans_in_qnty+=$today_trans_in_qnty;
								}
							}
						}
					}
					?>
				</table>
			</div>
			<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<tfoot>
					<th width="30"></th>
					<th width="100"></th>
					<th width="60"></th>
					<th width="50"></th>
					<th width="110">&nbsp;</th>
					<th width="100">&nbsp;</th>

					<th width="60">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="120">Total</th>
					<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?>10</th>
					<th width="80" align="right" id="value_total_today_rtn_qnty"><? echo number_format($total_today_rtn_qnty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_today_trans_in_qnty"><? echo number_format($total_today_trans_in_qnty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_rec_ret_qty"><? echo number_format($total_rec_ret_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_rec_trns_qty"><? echo number_format($total_rec_trns_qty,2,'.',''); ?></th>

					<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>

					<th width="80"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
					<th width="80"  id="value_total_today_issue_rcv_rtn_qty"><? echo number_format($total_today_issue_rcv_rtn_qnty,2,'.',''); ?></th>
					<th width="80"  id="value_recv_total_today_issue_trns_out_qnty"><? echo number_format($total_today_issue_trns_out_qnty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_issue_ret_qty"><? echo number_format($total_issue_ret_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_issue_trns_qty"><? echo number_format($total_issue_trns_qty,2,'.',''); ?></th>

					<th width="100" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
					<?
					foreach ($store_ids_arr as $store_id) {
						?>
						<th width="<? echo $store_width; ?>" id="value_total_store_qty"><? echo number_format($store_wise_total_stock[$store_id],2,".",""); ?></th>
						<?
					}
					?>
				</tfoot>
			</table>
		</fieldset>
		<?
	}
	//Knit end
	else if($cbo_report_type==2) // Woven Finish Start
	{
		$product_array=array();
		$sql_product="select id, color, detarmination_id,dia_width,weight,product_name_details from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0";
		$sql_product_result=sql_select($sql_product);
		foreach( $sql_product_result as $row )
		{
			$product_array[$row[csf('product_name_details')]]['color']=$row[csf('color')];
			$product_array[$row[csf('product_name_details')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('product_name_details')]]['weight']=$row[csf('weight')];
			$product_array[$row[csf('product_name_details')]]['yarn_did']=$row[csf('detarmination_id')];
		}

		$issue_qnty=array();
		$sql_issue=sql_select(" select b.po_breakdown_id,b.color_id, sum(b.quantity) as issue_qnty  from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.issue_purpose in(4,9) and b.entry_form=19 group by b.po_breakdown_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		} //var_dump($issue_qnty);

		$booking_qnty=array();
		//$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		foreach( $sql_booking as $row)
		{
			$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
		}
		unset($sql_booking);

		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		?>
		<fieldset style="width:1930px;">
			<table cellpadding="0" cellspacing="0" width="1690">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Buyer</th>
						<th width="60">Job</th>
						<th width="50">Year</th>
						<th width="110">Style</th>

						<th width="60">Order Status</th>
						<th width="100">Shipment Status</th>
						<th width="110">Fin. Fab. Color</th>
						<th width="220">Fab. Desc.</th>

						<th width="80">Req. Qty</th>
						<th width="80">Today Recv.</th>
						<th width="80" title="Rec.+Issue Ret.+Trans. in">Total Received</th>

						<th width="80" title="Req.-Totat Rec.">Received Balance</th>
						<th width="80" title="">Avg. Rate</th>
						<th width="80" title="">Received Value</th>

						<th width="80">Today Issue</th>
						<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>
						<th width="80" title="">Avg. Rate</th>
						<th width="80" title="">Issue Value</th>

						<th width="80" title="Total Rec.- Total Issue">Stock</th>
						<th width="80" title="">Avg. Rate</th>
						<th width="" title="">Stock Value</th>
					</tr>
				</thead>
			</table>
			<div style="width:1920px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					if($db_type==0) $prod_cond="group_concat(e.prod_id) as prod_id";
					else if($db_type==2) $prod_cond="listagg(cast(e.prod_id as varchar2(4000)),',') within group (order by e.prod_id) as prod_id";

					$sql_query="SELECT a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id,c.balance_qnty, $select_fld,e.product_name_details as prod_desc,b.shiping_status, 

					(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) as finish_fabric_transfer_in,					
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end) as issue_rtn,

					(case when d.entry_form in (17) and c.transaction_type=1 then c.cons_amount else 0 end) as receive_amount, 
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then c.cons_amount else 0 end) as transfer_in_amount,
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then c.cons_amount else 0 end) as issue_rtn_amount,

					(case when d.entry_form in (17) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,					
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in,
					(case when d.entry_form in (209)  and c.transaction_type=4 and d.trans_type=4  and $today_receive_date then d.quantity else 0 end) as today_issue_rtn,

					(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty,
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as finish_fabric_transfer_out,
					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end) as recv_rtn,

					(case when d.entry_form in (19) and c.transaction_type=2 then c.cons_amount else 0 end) as issue_amount, 
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then c.cons_amount else 0 end) as transfer_out_amount, 
					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then c.cons_amount else 0 end) as recv_rtn_amount, 

  					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 and $today_receive_date then d.quantity else 0 end) as today_recv_rtn,
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trans_out,
					(case when d.entry_form in (19) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty

					from  wo_po_details_master a, wo_po_break_down b,inv_transaction c,  order_wise_pro_details d,product_details_master e 
					where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and  d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id  and d.entry_form in (17,19,258,209,202) and d.trans_id!=0 and d.entry_form in (17,19,258,209,202)  and c.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$cbo_company_id $receive_date $buyer_id_cond $search_cond $shiping_status_cond $year_cond
					order by a.job_no,d.color_id,c.transaction_date";
					// echo $sql_query;
					$style_wise_arr=array();
					$nameArray=sql_select($sql_query);
					foreach ($nameArray as $row)
					{
						$po_id_shipingStatus_arr[$row[csf('po_id')]]=$row[csf('shiping_status')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no']=$row[csf('job_no')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['year']=$row[csf('year')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['buyer_name']=$row[csf('buyer_name')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['style_ref_no']=$row[csf('style_ref_no')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_id'].=$row[csf('po_id')].',';
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_no'].=$row[csf('po_no')].',';
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_qnty']+=$row[csf('receive_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_qnty']+=$row[csf('issue_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn']+=$row[csf('issue_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn']+=$row[csf('recv_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['balance_qnty']+=$row[csf('balance_qnty')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_in']+=$row[csf('today_trans_in')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_out']+=$row[csf('today_trans_out')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_rtn']+=$row[csf('today_issue_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_recv_rtn']+=$row[csf('today_recv_rtn')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];
						
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_amount']+=$row[csf('receive_amount')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['transfer_in_amount']+=$row[csf('transfer_in_amount')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn_amount']+=$row[csf('issue_rtn_amount')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_amount']+=$row[csf('issue_amount')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['transfer_out_amount']+=$row[csf('transfer_out_amount')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn_amount']+=$row[csf('recv_rtn_amount')];
					}
					//print_r($po_id_shipingStatus_arr);

					$i=1;$total_rec_amount=$total_iss_amount=$total_StockValue=0;
					foreach ($style_wise_arr  as $job_key=>$job_val)
					{
						foreach ($job_val  as $color_key=>$color_val)
						{
							foreach ($color_val  as $desc_key=>$val)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$dzn_qnty=0;
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;

								$color_id=$row[csf("color_id")];
								//$fab_desc_type=$product_arr[$desc_key];
								$fab_desc_type=$desc_key;
								$po_nos=rtrim($val['po_no'],',');
								$po_nos=implode(",",array_unique(explode(",",$po_nos)));
								$poids=rtrim($val['po_id'],',');

								$po_ids=array_unique(explode(",",$poids));
								$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
								foreach($po_ids as $po_id)
								{
									//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."<br>";
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['fin_fab_qnty'];
										//echo $job_key.'ii'.$po_id;
									$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
									$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
								}

									//echo $job_key.'ii';
								$po_ids=implode(",",$po_ids);
								$today_recv=$val[("today_receive_qnty")]+$val[("today_trans_in")]+$val[("today_issue_rtn")];
								$today_issue=$val[("today_issue_qnty")]+$val[("today_trans_out")]+$val[("today_recv_rtn")];
								$rec_qty=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);
								$rec_amount=($val[("receive_amount")]+$val[("transfer_in_amount")]+$val[("issue_rtn_amount")]);
								$rec_avg_rate=$rec_amount/$rec_qty;
								// echo $rec_qty.'<br>';
								//$rec_qty_cal=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);

								$iss_qty=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
								$iss_amount=($val[("issue_amount")]+$val[("transfer_out_amount")]+$val[("recv_rtn_amount")]);
								$issue_avg_rate=$iss_amount/$iss_qty;
								$StockValue=$rec_amount-$iss_amount;
								$stock=($rec_qty-$iss_qty);
								$stock_avg_rate=$StockValue/$stock;
								//$iss_qty_cal=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
									<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
									<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
									<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>

									<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
									<td width="100" title="<? echo $po_ids;?>"><p>
										<? 
											$po_ids_exp=explode(",", $po_ids);
											$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
											foreach ($po_ids_exp as $row) 
											{
												if($po_id_shipingStatus_arr[$row]==3)
												{
													$full_shipSts_countx++;
												}
												else if($po_id_shipingStatus_arr[$row]==2){
													$partial_shipSts_countx++;
												}
												else if($po_id_shipingStatus_arr[$row]==1){
													$panding_shipSts_countx++;
												}
												$poId_countx++;
											}
											if($full_shipSts_countx==$poId_countx){
												$ShipingStatus="Full Delivery/Closed";
											}
											else if ($partial_shipSts_countx==$poId_countx) {
												$ShipingStatus="Partial Delivery";
											}
											else if ($panding_shipSts_countx==$poId_countx) {
												$ShipingStatus="Full Pending";
											}
											else
											{
												$ShipingStatus="Partial Delivery";
											}
											echo $ShipingStatus;
										?></p></td>
									<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
									<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>

									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>


									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>

									<td width="80" align="right"><p><?
									$rec_bal=$book_qty-$rec_qty;
									//$rec_bal=$val[("balance_qnty")];
									echo number_format($rec_bal,2,'.','');
									?></p></td>
									<td width="80" align="right"><p><? echo number_format($rec_avg_rate,4,'.',''); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($rec_amount,2,'.',''); ?></p></td>

									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_issue_popup');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_avg_rate,4,'.',''); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($iss_amount,2,'.',''); ?></p></td>

									<td width="80" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','woven_knit_stock_popup');"><?
									//$stock=$rec_qty_cal-$iss_qty_cal; old
									 //new $rec_qty
									echo number_format($stock,2,'.','');
									?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($stock_avg_rate,4,'.',''); ?></p></td>
									<td width="" align="right"><p><? echo number_format($StockValue,2,'.',''); ?></p></td>
								</tr>
								<?
								$i++;

								$total_req_qty+=$book_qty;
								$total_rec_qty+=$rec_qty;
								$total_rec_bal+=$rec_bal;
								$total_issue_qty+=$iss_qty;
								$total_stock+=$stock;
								$total_possible_cut_pcs+=$possible_cut_pcs;
								$total_actual_cut_qty+=$actual_qty;
								$total_rec_return_qnty+=$receive_ret_qnty;
								$total_issue_ret_qnty+=$issue_ret_qnty;
								$total_today_issue+=$today_issue;
								$total_today_recv+=$today_recv;

								$total_rec_amount+=$rec_amount;
								$total_iss_amount+=$iss_amount;
								$total_StockValue+=$StockValue;
							}
						}
					}
					?>
				</table>
				<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="50"></th>
						<th width="110">&nbsp;</th>

						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="220">Total</th>
						<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
						<th width="80" align="right" id=""></th>
						<th width="80" align="right" id="value_total_rec_amount"><? echo number_format($total_rec_amount,2,'.',''); ?></th>

						<th width="80"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th width="80" align="right" id=""></th>
						<th width="80" align="right" id="value_total_iss_amount"><? echo number_format($total_iss_amount,2,'.',''); ?></th>

						<th width="80" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
						<th width="80" align="right" id=""></th>
						<th width="" align="right" id="value_total_StockValue"><? echo number_format($total_StockValue,2,'.',''); ?></th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}

if($action=="report_generate3")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_report_type 	= str_replace("'","",$cbo_report_type);
	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_comm 	= str_replace("'","",$txt_search_comm);
	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '$txt_search_comm'";
	}
	else
	{
		$search_cond.="";
	}

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	if($db_type==0)
	{
		$select_fld= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	ob_start();


	if($cbo_report_type==2) // Woven Finish Start
	{
		$product_array=array();
		$sql_product="select id, color, detarmination_id,dia_width,weight,product_name_details from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0";
		$sql_product_result=sql_select($sql_product);
		foreach( $sql_product_result as $row )
		{
			$product_array[$row[csf('product_name_details')]]['color']=$row[csf('color')];
			$product_array[$row[csf('product_name_details')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('product_name_details')]]['weight']=$row[csf('weight')];
			$product_array[$row[csf('product_name_details')]]['yarn_did']=$row[csf('detarmination_id')];
		}

		$issue_qnty=array();
		$sql_issue=sql_select(" select b.po_breakdown_id,b.color_id, sum(b.quantity) as issue_qnty  from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.issue_purpose in(4,9) and b.entry_form=19 group by b.po_breakdown_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		} //var_dump($issue_qnty);

		$booking_qnty=array();
		//$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		foreach( $sql_booking as $row)
		{
			$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
		}
		unset($sql_booking);

		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		?>
		<fieldset style="width:2730px;">
			<table cellpadding="0" cellspacing="0" width="1990">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="2700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Buyer</th>
						<th width="60">Job</th>
						<th width="50">Year</th>
						<th width="200">Style</th>

						<th width="60">Order Status</th>
						<th width="150">Shipment Status</th>
						<th width="270">Fab. Desc.</th>
						
						<th width="100">RD No</th>
						<th width="100">Fabric Ref No</th>
						<th width="110">Fin. Fab. Color</th>
						

						<th width="120">Req. Qty</th>
						<th width="120">Today Recv.</th>
						<th width="120" title="Rec.+Issue Ret.+Trans. in">Total Received</th>

						<th width="120" title="Req.-Totat Rec.">Received Balance</th>
						<th width="80" title="">Avg. Rate</th>
						<th width="120" title="">Received Value</th>

						<th width="120">Today Issue</th>
						<th width="120" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>
						<th width="80" title="">Avg. Rate</th>
						<th width="120" title="">Issue Value</th>

						<th width="120" title="Total Rec.- Total Issue">Stock</th>
						<th width="80" title="">Avg. Rate</th>
						<th width="" title="">Stock Value</th>
					</tr>
				</thead>
			</table>
			<div style="width:2720px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="2700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					if($db_type==0) $prod_cond="group_concat(e.prod_id) as prod_id";
					else if($db_type==2) $prod_cond="listagg(cast(e.prod_id as varchar2(4000)),',') within group (order by e.prod_id) as prod_id";


					//all amount query
					$sql_queryAmount="SELECT a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,d.color_id,d.prod_id, $select_fld,e.product_name_details as prod_desc,c.rd_no,c.fabric_ref,c.batch_lot,c.transaction_type, c.batch_id,c.pi_wo_batch_no,
					(case when d.entry_form in (17) and c.transaction_type=1 then c.cons_amount else 0 end) as receive_amount, 
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then c.cons_amount else 0 end) as transfer_in_amount,
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then c.cons_amount else 0 end) as issue_rtn_amount,
					(case when d.entry_form in (19) and c.transaction_type=2 then c.cons_amount else 0 end) as issue_amount, 
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then c.cons_amount else 0 end) as transfer_out_amount, 
					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then c.cons_amount else 0 end) as recv_rtn_amount 

					from  wo_po_details_master a, wo_po_break_down b,inv_transaction c,  order_wise_pro_details d,product_details_master e 
					where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and  d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id  and d.entry_form in (17,19,258,209,202) and d.trans_id!=0 and d.entry_form in (17,19,258,209,202)  and c.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$cbo_company_id $receive_date $buyer_id_cond $search_cond $store_id_cond_trans $year_cond group by a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,d.color_id,d.prod_id,  TO_CHAR(a.insert_date,'YYYY') ,e.product_name_details,c.rd_no,c.fabric_ref,c.batch_lot,c.transaction_type, c.batch_id,c.pi_wo_batch_no,c.transaction_date,d.entry_form,d.trans_type,c.cons_amount order by a.job_no,d.color_id,c.transaction_date"; 
					$nameArrayAmount=sql_select($sql_queryAmount);
					$style_wise_arr_amount=array();
					foreach ($nameArrayAmount as $row)
					{

						$style_wise_arr_amount[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('transaction_type')]]['receive_amount']+=$row[csf('receive_amount')];
						$style_wise_arr_amount[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('transaction_type')]]['transfer_in_amount']+=$row[csf('transfer_in_amount')];
						$style_wise_arr_amount[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('transaction_type')]]['issue_rtn_amount']+=$row[csf('issue_rtn_amount')];

						$style_wise_arr_amount[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('transaction_type')]]['issue_amount']+=$row[csf('issue_amount')];
						$style_wise_arr_amount[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('transaction_type')]]['transfer_out_amount']+=$row[csf('transfer_out_amount')];
						$style_wise_arr_amount[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('transaction_type')]]['recv_rtn_amount']+=$row[csf('recv_rtn_amount')];

					}
					/*echo "<pre>";
					print_r($style_wise_arr_amount);
					echo "</pre>";*/

					$sql_query="SELECT a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id,c.balance_qnty, $select_fld,e.product_name_details as prod_desc,c.rd_no,c.fabric_ref,c.batch_lot,c.transaction_type, c.batch_id,c.pi_wo_batch_no, 

					(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) as finish_fabric_transfer_in,					
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end) as issue_rtn,

					(case when d.entry_form in (17) and c.transaction_type=1 then c.cons_amount else 0 end) as receive_amount, 
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then c.cons_amount else 0 end) as transfer_in_amount,
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then c.cons_amount else 0 end) as issue_rtn_amount,

					(case when d.entry_form in (17) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,					
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in,
					(case when d.entry_form in (209)  and c.transaction_type=4 and d.trans_type=4  and $today_receive_date then d.quantity else 0 end) as today_issue_rtn,

					(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty,
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as finish_fabric_transfer_out,
					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end) as recv_rtn,

					(case when d.entry_form in (19) and c.transaction_type=2 then c.cons_amount else 0 end) as issue_amount, 
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then c.cons_amount else 0 end) as transfer_out_amount, 
					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then c.cons_amount else 0 end) as recv_rtn_amount, 

  					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 and $today_receive_date then d.quantity else 0 end) as today_recv_rtn,
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trans_out,
					(case when d.entry_form in (19) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty

					from  wo_po_details_master a, wo_po_break_down b,inv_transaction c,  order_wise_pro_details d,product_details_master e 
					where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and  d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id  and d.entry_form in (17,19,258,209,202) and d.trans_id!=0 and d.entry_form in (17,19,258,209,202)  and c.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$cbo_company_id $receive_date $buyer_id_cond $search_cond $year_cond order by a.job_no,d.color_id,c.transaction_date"; 
					//and c.batch_id in(5063) and e.id in(16068,16069)
					//and   c.id in(121684,121645,121597,121593,121590)
					// and a.style_ref_no='testt' and c.id in(121645,121590,121593,121597,121684,121642,121638,121592,121594) and c.mst_id in(49149,49144) 
					//echo $sql_query;
					$con = connect();
					$color_id_check=array();
					$style_wise_arr=array();
					$style_wise_info_arr=array();
					$nameArray=sql_select($sql_query);
					foreach ($nameArray as $row)
					{
						if(!$color_id_check[$row[csf('color_id')]])
						{
						    $color_id_check[$row[csf('color_id')]]=$row[csf('color_id')];
						    $ColorId = $row[csf('color_id')];
						    $rID=execute_query("insert into tmp_color_id (user_id, color_id) values ($user_id,$ColorId)");
						}

						if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2)
						{
							$recv_issue_batchId=$row[csf("batch_id")];
						}
						else
						{
							$recv_issue_batchId=$row[csf("pi_wo_batch_no")];
						}


						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no']=$row[csf('job_no')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['year']=$row[csf('year')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['buyer_name']=$row[csf('buyer_name')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['style_ref_no']=$row[csf('style_ref_no')];
						
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['prod_id']=$row[csf('prod_id')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['batch_id'].=$recv_issue_batchId.',';
						
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_id'].=$row[csf('po_id')].',';
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_no'].=$row[csf('po_no')].',';
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_qnty']+=$row[csf('receive_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_qnty']+=$row[csf('issue_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn']+=$row[csf('issue_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn']+=$row[csf('recv_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['balance_qnty']+=$row[csf('balance_qnty')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_in']+=$row[csf('today_trans_in')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_out']+=$row[csf('today_trans_out')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_rtn']+=$row[csf('today_issue_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_recv_rtn']+=$row[csf('today_recv_rtn')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];
						
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_amount']=$row[csf('receive_amount')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['transfer_in_amount']+=$row[csf('transfer_in_amount')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn_amount']+=$row[csf('issue_rtn_amount')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_amount']+=$row[csf('issue_amount')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['transfer_out_amount']+=$row[csf('transfer_out_amount')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn_amount']+=$row[csf('recv_rtn_amount')];

						if($row[csf('transaction_type')]==1)
						{
							$style_wise_info_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('prod_id')]]['rd_no']=$row[csf('rd_no')];
							$style_wise_info_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('prod_id')]]['fabric_ref']=$row[csf('fabric_ref')];
							$style_wise_info_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('prod_id')]]['batch_lot']=$row[csf('batch_lot')];
						}
					}
					/*echo "<pre>";
					print_r($style_wise_info_arr);
					echo "</pre>";*/
					if($rID)
					{
					    oci_commit($con);
					}
					$color_arr=return_library_array( "select a.id,a.color_name from lib_color a,tmp_color_id b where a.id=b.color_id and b.user_id=$user_id", "id", "color_name"  );

					$rID=execute_query("delete from tmp_color_id where user_id=$user_id");
					if($rID)
					{
					    oci_commit($con);
					}
									




					$i=1;$total_rec_amount=$total_iss_amount=$total_StockValue=0;$total_stock=0;
					foreach ($style_wise_arr  as $job_key=>$job_val)
					{
						foreach ($job_val  as $color_key=>$color_val)
						{
							foreach ($color_val  as $desc_key=>$val)
							{		
								/*$today_recv=0;
								$today_issue=0;
								$rec_qty=0;
								$rec_amount=0;
								$iss_qty=0;
								$iss_amount=0;*/
								//foreach ($desc_val  as $batch_key=>$val)
								//{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$dzn_qnty=0;
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
									else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
									else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
									else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
									else $dzn_qnty=1;

									$color_id=$row[csf("color_id")];
									//$fab_desc_type=$product_arr[$desc_key];
									$fab_desc_type=$desc_key;

									//----batch---
									$batchids=rtrim($val['batch_id'],',');
									$batch_ids=array_unique(explode(",",$batchids));
									$batch_ids=implode(",",$batch_ids);
									//----------
									$po_nos=rtrim($val['po_no'],',');
									$po_nos=implode(",",array_unique(explode(",",$po_nos)));
									$poids=rtrim($val['po_id'],',');

									$receive_amount=$style_wise_arr_amount[$job_key][$color_key][$desc_key][1]['receive_amount'];
									$transfer_in_amount=$style_wise_arr_amount[$job_key][$color_key][$desc_key][5]['transfer_in_amount'];
									$issue_rtn_amount=$style_wise_arr_amount[$job_key][$color_key][$desc_key][4]['issue_rtn_amount'];

									$issue_amount= $style_wise_arr_amount[$job_key][$color_key][$desc_key][2]['issue_amount'];
									$transfer_out_amount= $style_wise_arr_amount[$job_key][$color_key][$desc_key][6]['transfer_out_amount'];
									$recv_rtn_amount= $style_wise_arr_amount[$job_key][$color_key][$desc_key][3]['recv_rtn_amount'];


									$po_ids=array_unique(explode(",",$poids));
									$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
									foreach($po_ids as $po_id)
									{
										//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."<br>";
										$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['fin_fab_qnty'];
											//echo $job_key.'ii'.$po_id;
										$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
										$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
									}

										//echo $job_key.'ii';
									$po_ids=implode(",",$po_ids);
									$today_recv=$val[("today_receive_qnty")]+$val[("today_trans_in")]+$val[("today_issue_rtn")];
									$today_issue=$val[("today_issue_qnty")]+$val[("today_trans_out")]+$val[("today_recv_rtn")];
									$rec_qty=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);
									$rec_amount=($receive_amount+$transfer_in_amount+$issue_rtn_amount);
									$rec_avg_rate=$rec_amount/$rec_qty;
									// echo $rec_qty.'<br>';
									//$rec_qty_cal=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);
									//echo $val[("issue_qnty")]."+".$val[("finish_fabric_transfer_out")]."+".$val[("recv_rtn")]."<br/>";
									//echo $job_key."+".$color_key."+".$desc_key."+".$batch_key."<br/>";
									$iss_qty=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
									$iss_amount=($issue_amount+$transfer_out_amount+$recv_rtn_amount);
									$issue_avg_rate=$iss_amount/$iss_qty;
									$StockValue=$rec_amount-$iss_amount;
									$stock=($rec_qty-$iss_qty);
									$stock_avg_rate=$StockValue/$stock;
									if(is_nan($issue_avg_rate)){$issue_avg_rate=0.00;}
									if(is_nan($stock_avg_rate)){$stock_avg_rate=0.0000;}
									//$iss_qty_cal=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);

									$rd_no=$style_wise_info_arr[$job_key][$color_key][$desc_key][$val[("prod_id")]]['rd_no'];
									$fabric_ref=$style_wise_info_arr[$job_key][$color_key][$desc_key][$val[("prod_id")]]['fabric_ref'];
									$batch_lot=$style_wise_info_arr[$job_key][$color_key][$desc_key][$val[("prod_id")]]['batch_lot'];

									//($cbo_value_with ==1 && (number_format($opening,2,'.','')!= 0
								
									if($cbo_value_range_by==1 && $stock>=0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
											<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
											<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
											<td width="200"><p><? echo $val[("style_ref_no")]; ?></p></td>

											<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
											<td width="150" title="<? echo $po_ids;?>"><p>
												<? 
													$po_ids_exp=explode(",", $po_ids);
													$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
													foreach ($po_ids_exp as $row) 
													{
														if($po_id_shipingStatus_arr[$row]==3)
														{
															$full_shipSts_countx++;
														}
														else if($po_id_shipingStatus_arr[$row]==2){
															$partial_shipSts_countx++;
														}
														else if($po_id_shipingStatus_arr[$row]==1){
															$panding_shipSts_countx++;
														}
														$poId_countx++;
													}
													if($full_shipSts_countx==$poId_countx){
														$ShipingStatus="Full Delivery/Closed";
													}
													else if ($partial_shipSts_countx==$poId_countx) {
														$ShipingStatus="Partial Delivery";
													}
													else if ($panding_shipSts_countx==$poId_countx) {
														$ShipingStatus="Full Pending";
													}
													else
													{
														$ShipingStatus="Partial Delivery";
													}
													echo $ShipingStatus;
												?></p></td>
											<td width="270" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
											<td width="100" align="center"><p><? echo $rd_no; ?></p></td>
											<td width="100"><p><? echo  $fabric_ref; ?></p></td>
											<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
											
											
											<td width="120" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>

											<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup','','','<? echo "btn3"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>


											<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup','','','<? echo "btn3"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>

											<td width="120" align="right"><p><?
											$rec_bal=$book_qty-$rec_qty;
											//$rec_bal=$val[("balance_qnty")];
											echo number_format($rec_bal,2,'.','');
											?></p></td>
											<td width="80" align="right"><p><? echo number_format($rec_avg_rate,4,'.',''); ?></p></td>
											<td width="120" align="right"><p><? echo number_format($rec_amount,2,'.',''); ?></p></td>

											<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_issue_popup','','','<? echo "btn3"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
											<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup','','','<? echo "btn3"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_avg_rate,4,'.',''); ?></p></td>
											<td width="120" align="right"><p><? echo number_format($iss_amount,2,'.',''); ?></p></td>

											<td width="120" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','woven_knit_stock_popup','','','<? echo "btn3"; ?>','<? echo $batch_ids; ?>');"><?
											//$stock=$rec_qty_cal-$iss_qty_cal; old
											 //new $rec_qty
											echo number_format($stock,2,'.','');
											?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>
											<td width="80" align="right"><p><? echo number_format($stock_avg_rate,4,'.',''); ?></p></td>
											<td width="" align="right"><p><? echo number_format($StockValue,2,'.',''); ?></p></td>
										</tr>
										<?
										$i++;

										$total_req_qty+=$book_qty;
										$total_rec_qty+=$rec_qty;
										$total_rec_bal+=$rec_bal;
										$total_issue_qty+=$iss_qty;
										$total_stock+=$stock;
										$total_possible_cut_pcs+=$possible_cut_pcs;
										$total_actual_cut_qty+=$actual_qty;
										$total_rec_return_qnty+=$receive_ret_qnty;
										$total_issue_ret_qnty+=$issue_ret_qnty;
										$total_today_issue+=$today_issue;
										$total_today_recv+=$today_recv;

										$total_rec_amount+=$rec_amount;
										$total_iss_amount+=$iss_amount;
										$total_StockValue+=$StockValue;
									}
									else if($cbo_value_range_by==2 && $stock>0) 
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
											<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
											<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
											<td width="200"><p><? echo $val[("style_ref_no")]; ?></p></td>

											<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
											<td width="150" title="<? echo $po_ids;?>"><p>
												<? 
													$po_ids_exp=explode(",", $po_ids);
													$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
													foreach ($po_ids_exp as $row) 
													{
														if($po_id_shipingStatus_arr[$row]==3)
														{
															$full_shipSts_countx++;
														}
														else if($po_id_shipingStatus_arr[$row]==2){
															$partial_shipSts_countx++;
														}
														else if($po_id_shipingStatus_arr[$row]==1){
															$panding_shipSts_countx++;
														}
														$poId_countx++;
													}
													if($full_shipSts_countx==$poId_countx){
														$ShipingStatus="Full Delivery/Closed";
													}
													else if ($partial_shipSts_countx==$poId_countx) {
														$ShipingStatus="Partial Delivery";
													}
													else if ($panding_shipSts_countx==$poId_countx) {
														$ShipingStatus="Full Pending";
													}
													else
													{
														$ShipingStatus="Partial Delivery";
													}
													echo $ShipingStatus;
												?></p></td>
											<td width="270" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
											<td width="100" align="center"><p><? echo $rd_no; ?></p></td>
											<td width="100"><p><? echo  $fabric_ref; ?></p></td>
											<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
											
											
											<td width="120" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>

											<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup','','','<? echo "btn3"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>


											<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup','','','<? echo "btn3"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>

											<td width="120" align="right"><p><?
											$rec_bal=$book_qty-$rec_qty;
											//$rec_bal=$val[("balance_qnty")];
											echo number_format($rec_bal,2,'.','');
											?></p></td>
											<td width="80" align="right"><p><? echo number_format($rec_avg_rate,4,'.',''); ?></p></td>
											<td width="120" align="right"><p><? echo number_format($rec_amount,2,'.',''); ?></p></td>

											<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_issue_popup','','','<? echo "btn3"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
											<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup','','','<? echo "btn3"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_avg_rate,4,'.',''); ?></p></td>
											<td width="120" align="right"><p><? echo number_format($iss_amount,2,'.',''); ?></p></td>

											<td width="120" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','woven_knit_stock_popup','','','<? echo "btn3"; ?>','<? echo $batch_ids; ?>');"><?
											//$stock=$rec_qty_cal-$iss_qty_cal; old
											 //new $rec_qty
											echo number_format($stock,2,'.','');
											?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>
											<td width="80" align="right"><p><? echo number_format($stock_avg_rate,4,'.',''); ?></p></td>
											<td width="" align="right"><p><? echo number_format($StockValue,2,'.',''); ?></p></td>
										</tr>
										<?
										$i++;

										$total_req_qty+=$book_qty;
										$total_rec_qty+=$rec_qty;
										$total_rec_bal+=$rec_bal;
										$total_issue_qty+=$iss_qty;
										$total_stock+=$stock;
										$total_possible_cut_pcs+=$possible_cut_pcs;
										$total_actual_cut_qty+=$actual_qty;
										$total_rec_return_qnty+=$receive_ret_qnty;
										$total_issue_ret_qnty+=$issue_ret_qnty;
										$total_today_issue+=$today_issue;
										$total_today_recv+=$today_recv;

										$total_rec_amount+=$rec_amount;
										$total_iss_amount+=$iss_amount;
										$total_StockValue+=$StockValue;


									}

									
								//}
							}
						}
					}
					?>
				</table>
				<table width="2700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="50"></th>
						<th width="200">&nbsp;</th>

						<th width="60">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="270"></th>
						
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="110">Total</th>
						
						<th width="120" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
						<th width="120" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?></th>
						<th width="120" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
						<th width="120" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
						<th width="80" align="right"></th>
						<th width="120" align="right" id="value_total_rec_amount"><? echo number_format($total_rec_amount,2,'.',''); ?></th>
						<th width="120"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
						<th width="120" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th width="80" align="right"></th>
						<th width="120" align="right" id="value_total_iss_amount"><? echo number_format($total_iss_amount,2,'.',''); ?></th>
						<th width="120" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
						<th width="80" align="right" ></th>
						<th width="" align="right" id="value_total_StockValue"><? echo number_format($total_StockValue,2,'.',''); ?></th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}
if($action=="report_generate4")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_report_type 	= str_replace("'","",$cbo_report_type);
	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_comm 	= str_replace("'","",$txt_search_comm);
	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);
	$cbo_shipment_status = str_replace("'","",$cbo_shipment_status);
	if($cbo_shipment_status>0){$shiping_status_cond=" and b.shiping_status in ($cbo_shipment_status)";}
	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '$txt_search_comm'";
	}
	else
	{
		$search_cond.="";
	}

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	if($db_type==0)
	{
		$select_fld= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	ob_start();

	if($cbo_report_type==1)
	{
		echo "<p style='color:red; text-align:center;margin-top:20px;' >"."This report Only for Woven Finish"."</p>";
	}
	//Knit end
	else if($cbo_report_type==2) // Woven Finish Start
	{
		$product_array=array();
		$sql_product="select id, color, detarmination_id,dia_width,weight,product_name_details from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0";
		$sql_product_result=sql_select($sql_product);
		foreach( $sql_product_result as $row )
		{
			$product_array[$row[csf('product_name_details')]]['color']=$row[csf('color')];
			$product_array[$row[csf('product_name_details')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('product_name_details')]]['weight']=$row[csf('weight')];
			$product_array[$row[csf('product_name_details')]]['yarn_did']=$row[csf('detarmination_id')];
		}

		$issue_qnty=array();
		$sql_issue=sql_select(" select b.po_breakdown_id,b.color_id, sum(b.quantity) as issue_qnty  from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.issue_purpose in(4,9) and b.entry_form=19 group by b.po_breakdown_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		} //var_dump($issue_qnty);


		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

		?>
		<fieldset style="width:1840px;">
			<table cellpadding="0" cellspacing="0" width="1210">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Buyer</th>
						<th width="60">Job</th>
						<th width="50">Year</th>
						<th width="110">Style</th>

						<th width="60">Order Status</th>
						<th width="110">Fin. Fab. Color</th>
						<th width="220">Fab. Desc.</th>
						<th width="50">UOM</th>
						<th width="100">Pre-Cost Rate</th>

						<th width="80">Req. Qty</th>
						<th width="80">Total Value</th>
						<th width="80">Today Recv.</th>
						<th width="80">WO Rate</th>
						<th width="80" title="Rec.+Issue Ret.+Trans. in">Total Received</th>
						<th width="80">Total Roll</th>
						<th width="80">Total Value</th>
						<th width="80" title="Req.-Totat Rec.">Received Balance</th>
						<th width="80">Today Issue</th>
						<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>
						<th width="80" title="Total Rec.- Total Issue">Stock</th>
						<th width="" title="">Stock Value</th>
					</tr>
				</thead>
			</table>
			<div style="width:1920px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					if($db_type==0) $prod_cond="group_concat(e.prod_id) as prod_id";
					else if($db_type==2) $prod_cond="listagg(cast(e.prod_id as varchar2(4000)),',') within group (order by e.prod_id) as prod_id";

					$sql_query="SELECT a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id,c.balance_qnty, $select_fld, e.product_name_details as prod_desc, c.transfer_wo_rate,c.cons_uom,c.roll,

					(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) as finish_fabric_transfer_in,					
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end) as issue_rtn,

					(case when d.entry_form in (17) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,					
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in,
					(case when d.entry_form in (209)  and c.transaction_type=4 and d.trans_type=4  and $today_receive_date then d.quantity else 0 end) as today_issue_rtn,

					(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty,
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as finish_fabric_transfer_out,
					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end) as recv_rtn,

  					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 and $today_receive_date then d.quantity else 0 end) as today_recv_rtn,
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trans_out,
					(case when d.entry_form in (19) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty

					from  wo_po_details_master a, wo_po_break_down b,inv_transaction c,  order_wise_pro_details d,product_details_master e 
					where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and  d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id and d.entry_form in (17,19,258,209,202) and d.trans_id!=0 and d.entry_form in (17,19,258,209,202)  and c.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$cbo_company_id $receive_date $buyer_id_cond $search_cond $year_cond $shiping_status_cond 
					order by a.job_no,d.color_id,c.transaction_date";
					//echo $sql_query;
				
					$nameArray=sql_select($sql_query);
					if (!empty($nameArray)) 
					{
						$con = connect();
						$r_id=execute_query("delete from tmp_job_no where userid=$user_id");
						if($r_id)
						{
						    oci_commit($con);
						}
					}

					$style_wise_arr=array();$buyer_wise_summary_arr=array();
					$job_no_check=array();
					$JOB_NOs ="";
					foreach ($nameArray as $row)
					{
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no']=$row[csf('job_no')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['year']=$row[csf('year')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['buyer_name']=$row[csf('buyer_name')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['style_ref_no']=$row[csf('style_ref_no')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_id'].=$row[csf('po_id')].',';
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_no'].=$row[csf('po_no')].',';
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_qnty']+=$row[csf('receive_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_qnty']+=$row[csf('issue_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn']+=$row[csf('issue_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn']+=$row[csf('recv_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['balance_qnty']+=$row[csf('balance_qnty')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_in']+=$row[csf('today_trans_in')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_out']+=$row[csf('today_trans_out')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_rtn']+=$row[csf('today_issue_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_recv_rtn']+=$row[csf('today_recv_rtn')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['prod_id']=$row[csf('prod_id')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['transfer_wo_rate']=$row[csf('transfer_wo_rate')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['cons_uom']=$row[csf('cons_uom')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['roll']+=$row[csf('roll')];

						if(!$job_no_check[$row[csf('job_no')]])
						{
							$job_no_check[$row[csf('job_no')]]=$row[csf('job_no')];
							$JOB_NO = "'".$row[csf('job_no')]."'";
							$JOB_NOs .= "'".$row[csf('job_no')]."',";
							$rID=execute_query("insert into tmp_job_no (userid, job_no) values ($user_id,$JOB_NO)");
						}

						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no']=$row[csf('job_no')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['year']=$row[csf('year')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['buyer_name']=$row[csf('buyer_name')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['style_ref_no']=$row[csf('style_ref_no')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_id'].=$row[csf('po_id')].',';
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_no'].=$row[csf('po_no')].',';
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_qnty']+=$row[csf('receive_qnty')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_qnty']+=$row[csf('issue_qnty')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn']+=$row[csf('issue_rtn')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn']+=$row[csf('recv_rtn')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['balance_qnty']+=$row[csf('balance_qnty')];

						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];

						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_in']+=$row[csf('today_trans_in')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_out']+=$row[csf('today_trans_out')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_rtn']+=$row[csf('today_issue_rtn')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_recv_rtn']+=$row[csf('today_recv_rtn')];

						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
						$buyer_wise_summary_arr[$row[csf('buyer_name')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];


							
					}
					$JOB_NOs =chop($JOB_NOs,",");

					if($rID)
					{
						oci_commit($con);
					}

				 	$condition= new condition();     
				    //$condition->po_id_in($poIds);  
				    $condition->job_no("in($JOB_NOs) ");
					$condition->init();
				    //$costPerArr=$condition->getCostingPerArr();
				    $fabric= new fabric($condition);
				    $fabric_qnty_arr=$fabric->getQtyArray_by_OrderFabColorGsmDeterminIdAndDiaWidth();
				    $fabric_amount_arr=$fabric->getAmountArray_by_OrderFabColorGsmDeterminIdAndDiaWidth();
				    //echo $fabric->getQuery();die;
				    //echo "<pre>";print_r($fabric_qnty_arr);echo "</pre>";

					/*$sql_pre_cost_rate=sql_select("select b.po_break_down_id as po_id,  b.job_no, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width,avg(d.rate) as avg_rate,d.color_number_id from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d,tmp_job_no e where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and c.id=d.pre_cost_fabric_cost_dtls_id and d.job_no=e.job_no and e.userid=$user_id  and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $jobNos_cond  group by b.po_break_down_id, b.job_no, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width ,d.color_number_id"); */
					$sql_pre_cost_rate=sql_select("select b.po_break_down_id as po_id,  b.job_no, c.lib_yarn_count_deter_id,c.gsm_weight,b.dia_width,b.fabric_color_id from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d,tmp_job_no e where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and c.id=d.pre_cost_fabric_cost_dtls_id and d.job_no=e.job_no and e.userid=$user_id  and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $jobNos_cond  group by b.po_break_down_id, b.job_no, c.lib_yarn_count_deter_id,c.gsm_weight,b.dia_width ,b.fabric_color_id"); 
					foreach( $sql_pre_cost_rate as $row)
					{
						//$pre_cost_avg_rate_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['pre_cost_avg']=$row[csf('avg_rate')];

						//echo 'woven'.'='.'finish'.'='.$row[csf('po_id')].'='.$row[csf('fabric_color_id')].'='.$row[csf('gsm_weight')].'='.$row[csf('lib_yarn_count_deter_id')].'='.$row[csf('dia_width')]."<br/>";
			            $pre_cost_qnty_arr[$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('gsm_weight')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]]['fab_woven_finish_qnty']+= array_sum($fabric_qnty_arr['woven']['finish'][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('gsm_weight')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]]);

			            $pre_cost_qnty_arr[$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('gsm_weight')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]]['fab_woven_grey_qnty']+= array_sum($fabric_qnty_arr['woven']['grey'][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('gsm_weight')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]]);


			            $pre_cost_amnt_arr[$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('gsm_weight')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]]['fab_woven_finish_amount']+= array_sum($fabric_amount_arr['woven']['finish'][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('gsm_weight')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]]);

			            $pre_cost_amnt_arr[$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('gsm_weight')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]]['fab_woven_grey_amount']+= array_sum($fabric_amount_arr['woven']['grey'][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('gsm_weight')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]]);


			            //$pre_cost_amount_arr[$val['JOB_NO']][$color_id]['fab_woven_finish']=array_sum($fabric_costing_arr['woven']['finish'][$poIds][$val['COLOR_NUMBER_ID']][$val['CONTRAST_COLOR_ID']][$production_source]);


					}
					//echo "<pre>";print_r($pre_cost_qnty_arr);echo "</pre>";


					$booking_qnty=array();
					$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, sum(b.fin_fab_qnty) as fin_fab_qnty, c.lib_yarn_count_deter_id,c.gsm_weight,b.dia_width,avg(b.rate) as avg_rate from wo_booking_mst a, wo_booking_dtls b,tmp_job_no e, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no=e.job_no and e.job_no=c.job_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and e.userid=$user_id  group by  b.po_break_down_id, b.fabric_color_id, b.job_no, c.lib_yarn_count_deter_id,c.gsm_weight,b.dia_width");
					foreach( $sql_booking as $row)
					{
						$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['fin_fab_qnty']=$row[csf('fin_fab_qnty')];
						$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['avg_rate']=$row[csf('avg_rate')];
					}

					unset($sql_booking);	

					$con = connect();
					$r_id_1=execute_query("delete from tmp_job_no where userid=$user_id");
					if($r_id_1)
					{
					    oci_commit($con);
					}
					
					$i=1;
					foreach ($style_wise_arr  as $job_key=>$job_val)
					{
						foreach ($job_val  as $color_key=>$color_val)
						{
							foreach ($color_val  as $desc_key=>$val)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$dzn_qnty=0;
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;

								$color_id=$row[csf("color_id")];
								//$fab_desc_type=$product_arr[$desc_key];
								$fab_desc_type=$desc_key;
								$po_nos=rtrim($val['po_no'],',');
								$po_nos=implode(",",array_unique(explode(",",$po_nos)));
								$poids=rtrim($val['po_id'],',');

								$po_ids=array_unique(explode(",",$poids));
								$book_qty=0;$book_avg_rate=0;$issue_ret_qnty=0;$receive_ret_qnty=0;$pre_cost_avg_rate=0;
								foreach($po_ids as $po_id)
								{
									//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."=".$product_array[$desc_key]['dia_width']."=".$product_array[$desc_key]['fin_fab_qnty']."<br>";
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['fin_fab_qnty'];
									$book_avg_rate+=$booking_qnty[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['avg_rate'];
									//$pre_cost_avg_rate+=$pre_cost_avg_rate_arr[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['pre_cost_avg'];
										//echo $job_key.'ii'.$po_id;
									
									$pre_cost_qnty=$pre_cost_qnty_arr[$po_id][$color_key][$product_array[$desc_key]['weight']][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']]['fab_woven_finish_qnty'];
									$pre_cost_amount=$pre_cost_amnt_arr[$po_id][$color_key][$product_array[$desc_key]['weight']][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']]['fab_woven_finish_amount'];

									$pre_cost_avg_rate=$pre_cost_amount/$pre_cost_qnty;
									if(is_nan($pre_cost_avg_rate)){$pre_cost_avg_rate=0;}else{$pre_cost_avg_rate=$pre_cost_avg_rate;}



									$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
									$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
								}

									//echo $job_key.'ii';
								$po_ids=implode(",",$po_ids);
								$today_recv=$val[("today_receive_qnty")]+$val[("today_trans_in")]+$val[("today_issue_rtn")];
								$today_issue=$val[("today_issue_qnty")]+$val[("today_trans_out")]+$val[("today_recv_rtn")];
								$rec_qty=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);
								//$rec_qty_cal=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);

								$iss_qty=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
								//$iss_qty_cal=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
								
								$stock_check =($rec_qty-$iss_qty);

								if($cbo_value_range_by==2 &&  number_format($stock_check,2,'.','')>0.00)
								{

									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos ; echo "PO ID= ". $po_ids; ?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
										
										<td width="110" title="<? echo $color_key; ?>"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
										<td width="50" align="center"><p><? echo $unit_of_measurement[$val[("cons_uom")]]; ?></p></td>
										<td width="100" align="right" title="Data comes from pre costing page color wise avg rate"><p><? echo number_format($pre_cost_avg_rate,2,'.',''); ?></p></td>
										<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
										<td width="80" align="right"><p><? $totVal=($pre_cost_avg_rate*$book_qty); echo number_format($totVal,2,'.',''); ?>&nbsp;</p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>
										<td width="80" align="right"><p><? if($val[("finish_fabric_transfer_in")]>0){echo number_format($val[("transfer_wo_rate")],2,'.','');}else{echo number_format($book_avg_rate,2,'.','');} ?>&nbsp;</p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key."__".$val[("prod_id")]; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="center"><p><? echo $val[("roll")]; ?></p></td>
										<td width="80" align="right"><p><? $totValue=($book_avg_rate*$rec_qty); echo number_format($totValue,2,'.',''); ?>&nbsp;</p></td>


										<td width="80" align="right"><p><?
										$rec_bal=$book_qty-$rec_qty;
										//$rec_bal=$val[("balance_qnty")];
										echo number_format($rec_bal,2,'.','');

										?></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_issue_popup');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>

										<td width="80" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','woven_knit_stock_popup');"><?
										//$stock=$rec_qty_cal-$iss_qty_cal; old
										$stock=($rec_qty-$iss_qty); //new $rec_qty
										echo number_format($stock,2,'.','');
										?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>
										<td width="" align="right"><p><? $stockVal=($book_avg_rate*$stock); echo number_format($stockVal,2,'.',''); ?>&nbsp;</p></td>

									</tr>
									<?
									$i++;

									$total_req_qty+=$book_qty;
									$total_rec_qty+=$rec_qty;
									$total_rec_bal+=$rec_bal;
									$total_issue_qty+=$iss_qty;
									$total_stock+=$stock;
									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;
									$total_today_issue+=$today_issue;
									$total_today_recv+=$today_recv;
									

									$total_toVal+=$totVal;
									$total_totValue+=$totValue;
									$total_stockVal+=$stockVal;

								}
								else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos ; echo "PO ID= ". $po_ids; ?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
										
										<td width="110" title="<? echo $color_key; ?>"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
										<td width="50" align="center"><p><? echo $unit_of_measurement[$val[("cons_uom")]]; ?></p></td>
										<td width="100" align="right" title="Data comes from pre costing page color wise avg rate"><p><? echo number_format($pre_cost_avg_rate,2,'.',''); ?></p></td>
										<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
										<td width="80" align="right"><p><? $totVal=($pre_cost_avg_rate*$book_qty); echo number_format($totVal,2,'.',''); ?>&nbsp;</p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>
										<td width="80" align="right"><p><? if($val[("finish_fabric_transfer_in")]>0){echo number_format($val[("transfer_wo_rate")],2,'.','');}else{echo number_format($book_avg_rate,2,'.','');}  ?>&nbsp;</p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key."__".$val[("prod_id")]; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>

										<td width="80" align="center"><p><? echo $val[("roll")]; ?></p></td>
										<td width="80" align="right"><p><? $totValue=($book_avg_rate*$rec_qty); echo number_format($totValue,2,'.',''); ?>&nbsp;</p></td>


										<td width="80" align="right"><p><?
										$rec_bal=$book_qty-$rec_qty;
										//$rec_bal=$val[("balance_qnty")];
										echo number_format($rec_bal,2,'.','');

										?></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_issue_popup');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>

										<td width="80" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','woven_knit_stock_popup');"><?
										//$stock=$rec_qty_cal-$iss_qty_cal; old
										$stock=($rec_qty-$iss_qty); //new $rec_qty
										echo number_format($stock,2,'.','');
										?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>
										<td width="" align="right"><p><? $stockVal=($book_avg_rate*$stock); echo number_format($stockVal,2,'.',''); ?>&nbsp;</p></td>

									</tr>
									<?
									$i++;

									$total_req_qty+=$book_qty;
									$total_rec_qty+=$rec_qty;
									$total_rec_bal+=$rec_bal;
									$total_issue_qty+=$iss_qty;
									$total_stock+=$stock;
									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;
									$total_today_issue+=$today_issue;
									$total_today_recv+=$today_recv;
									

									$total_toVal+=$totVal;
									$total_totValue+=$totValue;
									$total_stockVal+=$stockVal;
								}
								
							}
						}
					}
					?>
				</table>
				<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="50"></th>
						<th width="110">&nbsp;</th>

						<th width="60">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="220"></th>
						<th width="50"></th>
						<th width="100">Total</th>
						<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_issue_tot_val"><? echo number_format($total_toVal,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?></th>
						<th width="80" align="right" id=""><? //echo number_format($total_toVal,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
						<th width="80" align="right"></th>
						<th width="80" align="right" id="value_total_recv_val"><? echo number_format($total_totValue,2,'.',''); ?></th>

						<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
						<th width="80"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
						<th width="" align="right" id="value_total_stock_value"><? echo number_format($total_stockVal,2,'.',''); ?></th>
					</tfoot>
				</table>
			</div>

			<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Buyer</th>
						<th width="110">Fin. Fab. Color</th>
						<th width="220">Fab. Desc.</th>
						<th title="Total Rec.- Total Issue">Stock</th>
					</tr>
				</thead>
			</table>
			<div style="width:620px; max-height:350px; overflow-y:scroll;" id="scroll_bodys">
				<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_bodys" >
					<?
					//summary -------------------------
					$j=1;
					foreach ($buyer_wise_summary_arr  as $buyer_key=>$buyer_val)
					{
						foreach ($buyer_val  as $color_key=>$color_val)
						{
							foreach ($color_val  as $desc_key=>$val)
							{
								if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$dzn_qnty=0;
								if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;

								$color_id=$row[csf("color_id")];
								//$fab_desc_type=$product_arr[$desc_key];
								$fab_desc_type=$desc_key;
								$po_nos=rtrim($val['po_no'],',');
								$po_nos=implode(",",array_unique(explode(",",$po_nos)));
								$poids=rtrim($val['po_id'],',');

								$po_ids=array_unique(explode(",",$poids));
								$book_qty=0;$book_avg_rate=0;$issue_ret_qnty=0;$receive_ret_qnty=0;$pre_cost_avg_rate=0;
								foreach($po_ids as $po_id)
								{
									//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."=".$product_array[$desc_key]['dia_width']."=".$product_array[$desc_key]['fin_fab_qnty']."<br>";
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['fin_fab_qnty'];
									$book_avg_rate+=$booking_qnty[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['avg_rate'];
									//$pre_cost_avg_rate+=$pre_cost_avg_rate_arr[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['pre_cost_avg'];
										//echo $job_key.'ii'.$po_id;
									
									$pre_cost_qnty=$pre_cost_qnty_arr[$po_id][$color_key][$product_array[$desc_key]['weight']][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']]['fab_woven_finish_qnty'];
									$pre_cost_amount=$pre_cost_amnt_arr[$po_id][$color_key][$product_array[$desc_key]['weight']][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']]['fab_woven_finish_amount'];

									$pre_cost_avg_rate=$pre_cost_amount/$pre_cost_qnty;
									if(is_nan($pre_cost_avg_rate)){$pre_cost_avg_rate=0;}else{$pre_cost_avg_rate=$pre_cost_avg_rate;}



									$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
									$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
								}

									//echo $job_key.'ii';
								$po_ids=implode(",",$po_ids);
								$today_recv=$val[("today_receive_qnty")]+$val[("today_trans_in")]+$val[("today_issue_rtn")];
								$today_issue=$val[("today_issue_qnty")]+$val[("today_trans_out")]+$val[("today_recv_rtn")];
								$rec_qty=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);
								//$rec_qty_cal=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);

								$iss_qty=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
								//$iss_qty_cal=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
								
								$stock_check =($rec_qty-$iss_qty);

								if($cbo_value_range_by==2 &&  number_format($stock_check,2,'.','')>0.00)
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $j;?>','<? echo $bgcolor;?>')" id="tr<? echo $j;?>">
										<td width="30"><? echo $j; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										
										
										<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
										<td align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><? $stock=($rec_qty-$iss_qty); echo number_format($stock,2,'.','');
										?></p></td>
									
									</tr>
									<?
									$j++;

									$total_stock_summary+=$stock;

								}
								else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $j;?>','<? echo $bgcolor;?>')" id="tr<? echo $j;?>">
										<td width="30"><? echo $j; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										
										
										<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
										<td align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><? $stock=($rec_qty-$iss_qty); echo number_format($stock,2,'.','');
										?></p></td>
									
									</tr>
									<?
									$j++;

									$total_stock_summary+=$stock;
								}
								
							}
						}
					}
					
					//summary end----------------------


					?>
					
				</table>
				<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30"></th>
						<th width="100"></th>
						<th width="110">&nbsp;</th>
						<th width="220">Total</th>
						<th align="right" id="value_total_stock"><? echo number_format($total_stock_summary,2,'.',''); ?></th>
					</tfoot>
				</table>
			</div>


		</fieldset>
		<?
	}

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}
if($action=="report_generate5")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_report_type 	= str_replace("'","",$cbo_report_type);
	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_comm 	= str_replace("'","",$txt_search_comm);
	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '$txt_search_comm'";
	}
	else
	{
		$search_cond.="";
	}

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	if($db_type==0)
	{
		$select_fld= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	ob_start();

	if($cbo_report_type==1)
	{
		// Knit Finish Start
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		//$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2", "id", "product_name_details");
		$product_arr=return_library_array("select a.id, b.construction from product_details_master a, lib_yarn_count_determina_mst b where a.item_category_id=2 and a.detarmination_id=b.id", "id", "construction");

		$sql_rcv="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no,b.shiping_status, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id,b.grouping,
		(case when d.entry_form in (7,37,66,68) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
		(case when d.entry_form in (52,126) and $today_receive_date then d.quantity else 0 end) as today_issue_rtn_qnty,
		(case when d.entry_form in (14,15,134,306) and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in_qnty,
		(case when d.entry_form in (7,37,66,68)  then d.quantity else 0 end) as receive_qnty,
		(case when d.entry_form in (52,126) then d.quantity else 0 end) as issue_rtn_qnty,
		(case when d.entry_form in (14,15,134,306) and d.trans_type=5 then d.quantity else 0 end) as trans_in_qnty,

		(case when d.entry_form in (18,71) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty,
		(case when d.entry_form in (46) and $today_receive_date then d.quantity else 0 end) as today_rcv_rtn_qnty,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trns_out_qnty,
		(case when d.entry_form in (46) then d.quantity else 0 end) as issue_rcv_rtn_qnty,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty

		from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
		where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id  and a.company_name=$cbo_company_id and d.entry_form in (7,14,15,18,37,46,52,66,68,71,126,134,306) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $receive_date $buyer_id_cond $search_cond $year_cond
		order by a.job_no, d.color_id,c.transaction_date,b.grouping";

		$style_wise_arr=$color_id_arr=$prod_id_arr=$po_id_arr=array();

		$sql_rcv_res=sql_select($sql_rcv);
		foreach ($sql_rcv_res as $row)
		{
			$color_id_arr[$row[csf('color_id')]]=$row[csf('color_id')];
			$po_id_shipingStatus_arr[$row[csf('po_id')]]=$row[csf('shiping_status')];
			$prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
			$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$job_no_arr[$row[csf('job_no')]] = "'".$row[csf('job_no')]."'";

			$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
			//$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			//$fab_desc_tmp=$fab_desc_tmp1[0];

			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no']=$row[csf('job_no')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no_pre']=$row[csf('job_no_prefix_num')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['year']=$row[csf('year')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['buyer_name']=$row[csf('buyer_name')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['style_ref_no']=$row[csf('style_ref_no')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['grouping'].=$row[csf('grouping')].",";
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_id'].=$row[csf('po_id')].',';
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_no'].=$row[csf('po_no')].',';
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['receive_qnty']+=$row[csf('receive_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_rtn_qnty']+=$row[csf('issue_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['trans_in_qnty']+=$row[csf('trans_in_qnty')];

			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_rcv_rtn_qnty']+=$row[csf('issue_rcv_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['trns_out_qnty']+=$row[csf('trns_out_qnty')];

			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_rtn_qnty']+=$row[csf('today_issue_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_trans_in_qnty']+=$row[csf('today_trans_in_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_qnty']+=$row[csf('today_issue_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_rcv_rtn_qnty']+=$row[csf('today_rcv_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_trns_out_qnty']+=$row[csf('today_trns_out_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['prod_ids'].=$row[csf('prod_id')].',';

			if($row[csf('store_id')]*1!="" || $row[csf('store_id')]*1>0)
				$store_ids_arr[$row[csf('store_id')]] = $row[csf('store_id')];

			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["receive_qnty"] += $row[csf('receive_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_rtn_qnty"] += $row[csf('issue_rtn_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trans_in_qnty"] += $row[csf('trans_in_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_rcv_rtn_qnty"] += $row[csf('issue_rcv_rtn_qnty')];
		}
		/*echo "<pre>";
		print_r($style_wise_arr);
		die;*/
		$colorIds = implode(",", $color_id_arr);
		$prodIds = implode(",", $prod_id_arr);
		$poIds = implode(",", $po_id_arr);

		$prodIds 	= ($prodIds 	!= "") ? "and c.prod_id in ( $prodIds )" : "";
		$poIds 		= ($poIds 		!= "") ? "and c.po_breakdown_id in($poIds)" : "";

		// ======================================= FOR ISSUE QNTY ==============================================
		$sql_issue="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id,
		(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty
		from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d,inv_finish_fabric_issue_dtls f
		where  a.company_name=$cbo_company_id and a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and c.id=d.trans_id   and f.id=d.dtls_id and   d.entry_form in (7,14,15,18,37,46,52,66,68,71,126,134) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and f.status_active=1 and f.is_deleted=0 $receive_date $buyer_id_cond $search_cond $year_cond
		order by a.job_no, d.color_id,c.transaction_date";

		$style_wise_arr2=array();
		$sql_issue_res=sql_select($sql_issue);
		foreach ($sql_issue_res as $row)
		{
			//$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			//$fab_desc_tmp=$fab_desc_tmp1[0];

			$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
			$style_wise_arr2[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_qnty']+=$row[csf('issue_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_qnty"] += $row[csf('issue_qnty')];
		}

		// =================================== ISSUE TRANS IN-OUT QNTY ===============================
		$sql_trans="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=5 then d.quantity else 0 end) as trns_in_qnty
		from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details d, inv_transaction c,  inv_item_transfer_dtls e 
		where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and c.mst_id=e.mst_id and d.dtls_id=e.id and a.company_name=$cbo_company_id and d.entry_form in (14,15,134,306) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $receive_date $buyer_id_cond $search_cond $year_cond
		order by a.job_no, d.color_id,c.transaction_date";
		$sql_trans_res = sql_select($sql_trans);
		$trns_out_qnty_arr = array();
		foreach ($sql_trans_res as $row)
		{
			$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
			//$fab_desc_tmp  = $fab_desc_tmp1[0];
			$trns_out_qnty_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]+=$row[csf('trns_out_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trns_out_qnty"] += $row[csf('trns_out_qnty')];
			$trns_in_qnty_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]+=$row[csf('trns_in_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trns_in_qnty"] += $row[csf('trns_in_qnty')];
		}

		$booking_qnty=array();
		$job_no_arr = array_filter($job_no_arr);
		if(!empty($job_no_arr))
		{
			$job_no_arr = array_filter($job_no_arr);
			if($db_type==2 && count($job_no_arr)>999)
			{
				$job_no_arr_chunk=array_chunk($job_no_arr,999) ;
				foreach($job_no_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$jobCond.="  b.job_no in($chunk_arr_value) or ";
				}

				$all_job_no_cond.=" and (".chop($jobCond,'or ').")";
			}
			else
			{
				$all_job_no_cond=" and b.job_no in(".implode(",",$job_no_arr).")";
			}

			$sql_booking=sql_select("select b.po_break_down_id as po_id,b.fabric_color_id,b.job_no,b.construction,b.fabric_color_id, b.fin_fab_qnty,b.is_short,c.construction short_construction
				from wo_booking_mst a, wo_booking_dtls b
				left join wo_pre_cost_fabric_cost_dtls c on b.pre_cost_fabric_cost_dtls_id=c.id and c.status_active=1 and c.is_deleted=0
				where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $all_job_no_cond ");
			//$all_po_id_cond

			foreach( $sql_booking as $row)
			{
				$construction = ($row[csf('is_short')]==1)?$row[csf('short_construction')]:$row[csf('construction')];
				$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$construction]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
				$job_order_no_arr[$row[csf('job_no')]][] = $row[csf('po_id')];
			}
			unset($sql_booking);
		}

		if($colorIds!=""){
			$color_arr=return_library_array( "select id,color_name from lib_color where id in($colorIds)", "id", "color_name"  );
		}

		$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$store_width    = 120;
		$table_width 	= 2160+count($store_ids_arr)*$store_width;

		//check job wise all po shipment status..............
		$sql_check_shipmentStatus=sql_select("select b.id,b.po_number,b.shiping_status
						from  pro_ex_factory_mst a, wo_po_break_down b
						where b.id=a.po_break_down_id and a.status_active=1 and a.is_deleted=0");
		foreach ($sql_check_shipmentStatus as $row) {
			$shipingStatus_arr[$row[csf('id')]]['shiping_status']=$row[csf('shiping_status')];
		}
		?>
		<style type="text/css">
			.nsbreak{word-break: break-all;}
		</style>

		<fieldset style="width:2180px;">
			<table cellpadding="0" cellspacing="0" width="1960">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="<? echo $table_width; ?>px" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="100" rowspan="2">Buyer</th>
						<th width="60" rowspan="2">Job</th>
						<th width="50" rowspan="2">Year</th>
						<th width="110" rowspan="2">Style</th>
						<th width="100" rowspan="2">Internal Ref.</th>

						<th width="60" rowspan="2">Order Status</th>
						<th width="100" rowspan="2">Shipment Status</th>
						<th width="110" rowspan="2">Fin. Fab. Color</th>
						<th width="120" rowspan="2">Fabric Type</th>

						<th width="80" rowspan="2">Req. Qty</th>
						<th width="240" colspan="3">Today Recv.</th>
						<th width="240" title="Rec.+Issue Ret.+Trans. in" colspan="3">Total Received</th>

						<th width="80" title="Req.-Totat Rec." rowspan="2">Received Balance</th>
						<th width="240" colspan="3">Today Issue</th>
						<th width="240" title="Issue+Rec. Ret.+Trans. out" colspan="3">Total Issued</th>
						<th width="100" title="Total Rec.- Total Issue" rowspan="2">Stock</th>
						<?
						foreach ($store_ids_arr as $store_id) {
							?>
							<th width="<? echo $store_width; ?>" title="Store ID = <? echo $store_id; ?>" rowspan="2"><? echo $store_arr[$store_id]; ?></th>
							<?
						}
						?>
					</tr>
					<tr>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $table_width+20; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table nsbreak" id="table_body" >
					<?
					$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$total_today_issue=0;$total_today_recv=0;
					$fin_color_array=array(); $fin_color_data_arr=array();
					foreach ($style_wise_arr  as $job_key=>$job_val)
					{
						foreach ($job_val  as $color_key=>$color_val)
						{
							foreach ($color_val  as $desc_key=>$val)
							{
								$prod_ids=rtrim($val['prod_ids'],',');
								$prod_ids=implode(",",array_filter(array_unique(explode(",",$prod_ids))));
								$color_id=$row[csf("color_id")];
								$fab_desc_type=$desc_key;
								$po_nos=rtrim($val['po_no'],',');
								$po_nos=implode(",",array_filter(array_unique(explode(",",$po_nos))));
								$grouping=implode(",",array_filter(array_unique(explode(",",chop($val['grouping'],",")))));
								$poids=rtrim($val['po_id'],',');

								$po_ids=array_filter(array_unique(explode(",",$poids)));
								$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
								/*foreach($po_ids as $po_id)
								{
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$desc_key]['fin_fab_qnty'];
								}*/
								$order_no_arr=array_filter(array_unique($job_order_no_arr[$job_key]));
								$order_nos="";
								foreach($order_no_arr as $po_id)
								{
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$desc_key]['fin_fab_qnty'];
								}
								$order_nos = implode(",", $order_no_arr); //N.B. this $order_nos only for showing required quantity

								$po_ids=implode(",",$po_ids);
								$today_recv=$val[("today_receive_qnty")];
								$today_rtn_qnty=$val[("today_issue_rtn_qnty")];
								$today_trans_in_qnty=$val[("today_trans_in_qnty")];


								$today_issue=$val[("today_issue_qnty")];
								$today_issue_rcv_rtn_qnty=$val[("today_rcv_rtn_qnty")];
								$today_issue_trns_out_qnty=$val[("today_trns_out_qnty")];

								$rec_qty = $val[("receive_qnty")];
								$rec_ret_qty = $val[("issue_rtn_qnty")];
								//$rec_trns_qty = $val[("trans_in_qnty")];
								$rec_trns_qty = $trns_in_qnty_arr[$job_key][$color_key][$desc_key];

								$rec_qty_cal=($rec_qty+$rec_ret_qty+$rec_trns_qty);

								$iss_qty = $style_wise_arr2[$job_key][$color_key][$desc_key]['issue_qnty'];
								$iss_ret_qty=$val[("issue_rcv_rtn_qnty")];

								$iss_trns_qty = $trns_out_qnty_arr[$job_key][$color_key][$desc_key];
								$iss_qty_cal=($iss_qty+$iss_ret_qty+$iss_trns_qty);


								$popup_ref_data = $val[("buyer_name")]."_".$val[("job_no_pre")]."_".$val[("year")]."_".$val[("style_ref_no")]."_".$grouping."_".$desc_key;
								$stock_check=$rec_qty_cal-$iss_qty_cal;
								if($cbo_value_range_by==2 &&  number_format($stock_check,2,'.','')>0.00)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
										<td width="100"><p><? echo $grouping; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>">
											<?
											$po_ids_exp=explode(",", $po_ids);
											$poId_count=0; $partial_shipSts_count=0;
											foreach ($po_ids_exp as $row) {
												if($po_id_shipingStatus_arr[$row]==3)
												{
													$partial_shipSts_count++;
												}
												$poId_count++;
											}
											if($partial_shipSts_count==$poId_count){
												?>
												<a style="color:red;" href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											else
											{
												?>
												<a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											?>
										</td>
										<td width="100" title="<? echo $po_ids;?>"><p>
											<? 
												$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
												foreach ($po_ids_exp as $row) {
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$full_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==2){
														$partial_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==1){
														$panding_shipSts_countx++;
													}
													$poId_countx++;
												}
												if($full_shipSts_countx==$poId_countx){
													$ShipingStatus="Full Delivery/Closed";
												}
												else if ($partial_shipSts_countx==$poId_countx) {
													$ShipingStatus="Partial Delivery";
												}
												else if ($panding_shipSts_countx==$poId_countx) {
													$ShipingStatus="Full Pending";
												}
												else
												{
													$ShipingStatus="Partial Delivery";
												}
												echo $ShipingStatus;
											?></p></td>
										<td width="110" title="<? echo $color_key;?>"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="120" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?> </p></td>
										<td width="80" align="right" title="<? echo $job_key."==".$color_key."==".$desc_key."==".$po_ids;?>"><p>
											<a href='#report_details' onClick="openmypage_req_qnty('<? echo $order_nos; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo $job_key; ?>','<? echo $order_nos; ?>','<? echo $desc_key; ?>','<? echo 1; ?>','req_qnty_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</a>
											</p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','today_total_rec_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_recv,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_rtn_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_trans_in_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_trans_in_qnty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','total_receive_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_ret_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_trns_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_trns_qty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty_cal; echo number_format($rec_bal,2,'.','');?></p></td>
										<td width="80" align="right">
											<p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a>
											</p>
										</td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_rcv_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_trns_out_qnty,2,'.',''); ?></a></p>
										</td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_trns_qty,2,'.',''); ?></a></p></td>

										<td width="100" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_rtn_qnty."##".$rec_trns_qty."Issue: ".$val[("issue_qnty")]."##".$issue_rcv_rtn_qnty."##".$val[("trns_out_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','knit_stock_popup','<? echo $popup_ref_data; ?>');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a></p>
										</td>

										<?
										$store_receive=0;
										foreach ($store_ids_arr as $store_id) {
											$store_receive 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["receive_qnty"],2,".","");
											$store_issue_rtn 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rtn_qnty"],2,".","");
											$store_trans_in 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_in_qnty"],2,".","");

											$store_rcv_rtn 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rcv_rtn_qnty"],2,".","");
											$store_trns_out 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_out_qnty"],2,".","");
											$store_issue_qnty    = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_qnty"],2,".","");

											$store_balance = number_format(($store_receive+$store_issue_rtn+$store_trans_in) - ($store_rcv_rtn+$store_trns_out+$store_issue_qnty),2,".","");
											$store_balance_title = "Receive=$store_receive, Transfer In=$store_trans_in, Issue Return=$store_issue_rtn \nIssue=$store_issue_qnty, Transfer Out=$store_trns_out, Receive Return=$store_rcv_rtn";
											?>
											<td width="<? echo $store_width; ?>" title="<? echo $store_balance_title; ?>" align="right"><? echo $store_balance; ?></td>
											<?
											$store_wise_total_stock[$store_id] += $store_balance;
										}
										?>
									</tr>
									<?
									$i++;

									$total_order_qnty+=$val[("po_quantity")];
									$total_req_qty+=$book_qty;

									$total_rec_qty+=$rec_qty;
									$total_rec_ret_qty+=$rec_ret_qty;
									$total_rec_trns_qty+=$rec_trns_qty;

									$total_rec_bal+=$rec_bal;

									$total_issue_qty+=$iss_qty;
									$total_issue_ret_qty+=$iss_ret_qty;
									$total_issue_trns_qty+=$iss_trns_qty;

									$total_stock+=$stock;

									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;

									$total_today_issue+=$today_issue;
									$total_today_issue_rcv_rtn_qnty+=$today_issue_rcv_rtn_qnty;
									$total_today_issue_trns_out_qnty+=$today_issue_trns_out_qnty;

									$total_today_recv+=$today_recv;
									$total_today_rtn_qnty+=$today_rtn_qnty;
									$total_today_trans_in_qnty+=$today_trans_in_qnty;
								}
								else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
										<td width="100"><p><? echo $grouping; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>">
											<?
											$po_ids_exp=explode(",", $po_ids);
											$poId_count=0; $partial_shipSts_count=0;
											foreach ($po_ids_exp as $row) {
												if($po_id_shipingStatus_arr[$row]==3)
												{
													$partial_shipSts_count++;
												}
												$poId_count++;
											}
											if($partial_shipSts_count==$poId_count){
												?>
												<a style="color:red;" href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											else
											{
												?>
												<a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											?>
										</td>
										<td width="100" title="<? echo $po_ids;?>"><p>
											<? 
												$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
												foreach ($po_ids_exp as $row) {
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$full_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==2){
														$partial_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==1){
														$panding_shipSts_countx++;
													}
													$poId_countx++;
												}
												if($full_shipSts_countx==$poId_countx){
													$ShipingStatus="Full Delivery/Closed";
												}
												else if ($partial_shipSts_countx==$poId_countx) {
													$ShipingStatus="Partial Delivery";
												}
												else if ($panding_shipSts_countx==$poId_countx) {
													$ShipingStatus="Full Pending";
												}
												else
												{
													$ShipingStatus="Partial Delivery";
												}
												echo $ShipingStatus;
											?></p></td>
										<td width="110" title="<? echo $color_key;?>"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="120" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?> </p></td>
										<td width="80" align="right" title="<? echo $job_key."==".$color_key."==".$desc_key."==".$po_ids;?>"><p>
												<a href='#report_details' onClick="openmypage_req_qnty('<? echo $order_nos; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo $job_key; ?>','<? echo $order_nos; ?>','<? echo $desc_key; ?>','<? echo 1; ?>','req_qnty_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</a>
										</p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','today_total_rec_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_recv,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_rtn_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_trans_in_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_trans_in_qnty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','total_receive_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_ret_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_trns_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_trns_qty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty_cal; echo number_format($rec_bal,2,'.','');?></p></td>
										<td width="80" align="right">
											<p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a>
											</p>
										</td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_rcv_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_trns_out_qnty,2,'.',''); ?></a></p>
										</td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_trns_qty,2,'.',''); ?></a></p></td>

										<td width="100" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_rtn_qnty."##".$trns_in_qnty."Issue: ".$val[("issue_qnty")]."##".$issue_rcv_rtn_qnty."##".$val[("trns_out_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','knit_stock_popup','<? echo $popup_ref_data; ?>');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a></p>
										</td>

										<?
										$store_receive=0;
										foreach ($store_ids_arr as $store_id) {
											$store_receive 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["receive_qnty"],2,".","");
											$store_issue_rtn 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rtn_qnty"],2,".","");
											$store_trans_in 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_in_qnty"],2,".","");

											$store_rcv_rtn 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rcv_rtn_qnty"],2,".","");
											$store_trns_out 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_out_qnty"],2,".","");
											$store_issue_qnty    = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_qnty"],2,".","");

											$store_balance = number_format(($store_receive+$store_issue_rtn+$store_trans_in) - ($store_rcv_rtn+$store_trns_out+$store_issue_qnty),2,".","");
											$store_balance_title = "Receive=$store_receive, Transfer In=$store_trans_in, Issue Return=$store_issue_rtn \nIssue=$store_issue_qnty, Transfer Out=$store_trns_out, Receive Return=$store_rcv_rtn";
											?>
											<td width="<? echo $store_width; ?>" title="<? echo $store_balance_title; ?>" align="right"><? echo $store_balance; ?></td>
											<?
											$store_wise_total_stock[$store_id] += $store_balance;
										}
										?>
									</tr>
									<?
									$i++;

									$total_order_qnty+=$val[("po_quantity")];
									$total_req_qty+=$book_qty;

									$total_rec_qty+=$rec_qty;
									$total_rec_ret_qty+=$rec_ret_qty;
									$total_rec_trns_qty+=$rec_trns_qty;

									$total_rec_bal+=$rec_bal;

									$total_issue_qty+=$iss_qty;
									$total_issue_ret_qty+=$iss_ret_qty;
									$total_issue_trns_qty+=$iss_trns_qty;

									$total_stock+=$stock;

									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;

									$total_today_issue+=$today_issue;
									$total_today_issue_rcv_rtn_qnty+=$today_issue_rcv_rtn_qnty;
									$total_today_issue_trns_out_qnty+=$today_issue_trns_out_qnty;

									$total_today_recv+=$today_recv;
									$total_today_rtn_qnty+=$today_rtn_qnty;
									$total_today_trans_in_qnty+=$today_trans_in_qnty;
								}
							}
						}
					}
					?>
				</table>
			</div>
			<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<tfoot>
					<th width="30"></th>
					<th width="100"></th>
					<th width="60"></th>
					<th width="50"></th>
					<th width="110">&nbsp;</th>
					<th width="100">&nbsp;</th>

					<th width="60">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="120">Total</th>
					<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?>10</th>
					<th width="80" align="right" id="value_total_today_rtn_qnty"><? echo number_format($total_today_rtn_qnty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_today_trans_in_qnty"><? echo number_format($total_today_trans_in_qnty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_rec_ret_qty"><? echo number_format($total_rec_ret_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_rec_trns_qty"><? echo number_format($total_rec_trns_qty,2,'.',''); ?></th>

					<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>

					<th width="80"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
					<th width="80"  id="value_total_today_issue_rcv_rtn_qty"><? echo number_format($total_today_issue_rcv_rtn_qnty,2,'.',''); ?></th>
					<th width="80"  id="value_recv_total_today_issue_trns_out_qnty"><? echo number_format($total_today_issue_trns_out_qnty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_issue_ret_qty"><? echo number_format($total_issue_ret_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_issue_trns_qty"><? echo number_format($total_issue_trns_qty,2,'.',''); ?></th>

					<th width="100" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
					<?
					foreach ($store_ids_arr as $store_id) {
						?>
						<th width="<? echo $store_width; ?>" id="value_total_store_qty"><? echo number_format($store_wise_total_stock[$store_id],2,".",""); ?></th>
						<?
					}
					?>
				</tfoot>
			</table>
		</fieldset>
		<?
	}
	//Knit end
	else if($cbo_report_type==2) // Woven Finish Start
	{
		$product_array=array();
		$sql_product="select id, color, detarmination_id,dia_width,weight,product_name_details from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0";
		$sql_product_result=sql_select($sql_product);
		foreach( $sql_product_result as $row )
		{
			$product_array[$row[csf('product_name_details')]]['color']=$row[csf('color')];
			$product_array[$row[csf('product_name_details')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('product_name_details')]]['weight']=$row[csf('weight')];
			$product_array[$row[csf('product_name_details')]]['yarn_did']=$row[csf('detarmination_id')];
		}

		$issue_qnty=array();
		$sql_issue=sql_select(" select b.po_breakdown_id,b.color_id, sum(b.quantity) as issue_qnty  from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.issue_purpose in(4,9) and b.entry_form=19 group by b.po_breakdown_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		} //var_dump($issue_qnty);

		$booking_qnty=array();
		//$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		foreach( $sql_booking as $row)
		{
			$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
		}
		unset($sql_booking);

		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

		?>
		<fieldset style="width:2150px;">
			<table cellpadding="0" cellspacing="0" width="1910">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="2120" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<!-- <tr>
						<th width="30">SL</th>
						<th width="100">Buyer</th>
						<th width="60">Job</th>
						<th width="50">Year</th>
						<th width="110">Style</th>

						<th width="60">Order Status</th>
						<th width="100">Shipment Status</th>
						<th width="110">Fin. Fab. Color</th>
						<th width="220">Fab. Desc.</th>

						<th width="80">Req. Qty</th>
						<th width="80">Today Recv.</th>
						<th width="80" title="Rec.+Issue Ret.+Trans. in">Total Received</th>

						<th width="80" title="Req.-Totat Rec.">Received Balance</th>
						<th width="80">Today Issue</th>
						<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>
						<th width="" title="Total Rec.- Total Issue">Stock</th>
					</tr>
 					-->
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="100" rowspan="2">Buyer</th>
						<th width="60" rowspan="2">Job</th>
						<th width="50" rowspan="2">Year</th>
						<th width="110" rowspan="2">Style</th>

						<th width="60" rowspan="2">Order Status</th>
						<th width="100" rowspan="2">Shipment Status</th>
						<th width="110" rowspan="2">Fin. Fab. Color</th>
						<th width="220" rowspan="2">Fab. Desc.</th>

						<th width="80" rowspan="2">Req. Qty</th>
						<th width="240" colspan="3">Today Recv.</th>
						<th width="240" title="Rec.+Issue Ret.+Trans. in" colspan="3">Total Received</th>

						<th width="80" title="Req.Qnty - (Receive + Transfer In)" rowspan="2">Received Balance</th>
						<th width="240" colspan="3">Today Issue</th>
						<th width="240" title="Issue+Rec. Ret.+Trans. out" colspan="3">Total Issued</th>
						<th  title="Total Rec.- Total Issue" rowspan="2">Stock</th>
					</tr>
					<tr>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
					</tr>



				</thead>
			</table>
			<div style="width:2140px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="2120" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					if($db_type==0) $prod_cond="group_concat(e.prod_id) as prod_id";
					else if($db_type==2) $prod_cond="listagg(cast(e.prod_id as varchar2(4000)),',') within group (order by e.prod_id) as prod_id";

					$sql_query="SELECT a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id,c.balance_qnty, $select_fld, e.product_name_details as prod_desc,

					(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) as finish_fabric_transfer_in,					
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end) as issue_rtn,

					(case when d.entry_form in (17) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,					
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in,
					(case when d.entry_form in (209)  and c.transaction_type=4 and d.trans_type=4  and $today_receive_date then d.quantity else 0 end) as today_issue_rtn,

					(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty,
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as finish_fabric_transfer_out,
					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end) as recv_rtn,

  					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 and $today_receive_date then d.quantity else 0 end) as today_recv_rtn,
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trans_out,
					(case when d.entry_form in (19) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty

					from  wo_po_details_master a, wo_po_break_down b,inv_transaction c,  order_wise_pro_details d,product_details_master e 
					where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and  d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id and d.entry_form in (17,19,258,209,202) and d.trans_id!=0 and d.entry_form in (17,19,258,209,202)  and c.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$cbo_company_id $receive_date $buyer_id_cond $search_cond $year_cond
					order by a.job_no,d.color_id,c.transaction_date";
					// echo $sql_query;
					$style_wise_arr=array();
					$nameArray=sql_select($sql_query);
					foreach ($nameArray as $row)
					{
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no']=$row[csf('job_no')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['year']=$row[csf('year')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['buyer_name']=$row[csf('buyer_name')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['style_ref_no']=$row[csf('style_ref_no')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_id'].=$row[csf('po_id')].',';
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_no'].=$row[csf('po_no')].',';
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_qnty']+=$row[csf('receive_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_qnty']+=$row[csf('issue_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn']+=$row[csf('issue_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn']+=$row[csf('recv_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['balance_qnty']+=$row[csf('balance_qnty')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_in']+=$row[csf('today_trans_in')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_out']+=$row[csf('today_trans_out')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_rtn']+=$row[csf('today_issue_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_recv_rtn']+=$row[csf('today_recv_rtn')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];
					}

					$i=1;
					foreach ($style_wise_arr  as $job_key=>$job_val)
					{
						foreach ($job_val  as $color_key=>$color_val)
						{
							foreach ($color_val  as $desc_key=>$val)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$dzn_qnty=0;
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;

								$color_id=$row[csf("color_id")];
								//$fab_desc_type=$product_arr[$desc_key];
								$fab_desc_type=$desc_key;
								$po_nos=rtrim($val['po_no'],',');
								$po_nos=implode(",",array_unique(explode(",",$po_nos)));
								$poids=rtrim($val['po_id'],',');

								$po_ids=array_unique(explode(",",$poids));
								$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
								foreach($po_ids as $po_id)
								{
									//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."=".$product_array[$desc_key]['dia_width']."=".$product_array[$desc_key]['fin_fab_qnty']."<br>";
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['fin_fab_qnty'];
										//echo $job_key.'ii'.$po_id;
									$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
									$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
								}

									//echo $job_key.'ii';
								$po_ids=implode(",",$po_ids);
								$today_recv=$val[("today_receive_qnty")];
								$today_transIn=$val[("today_trans_in")];
								$today_issueRtn=$val[("today_issue_rtn")];

								$today_issue=$val[("today_issue_qnty")];
								$today_transOut=$val[("today_trans_out")];
								$today_recvRtn=$val[("today_recv_rtn")];

								$rec_qty=$val[("receive_qnty")];
								$rec_tranfIn=$val[("finish_fabric_transfer_in")];
								$rec_issueRtn=$val[("issue_rtn")];
								//$rec_qty_cal=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);

								$iss_qty=$val[("issue_qnty")];
								$iss_trnsOut=$val[("finish_fabric_transfer_out")];
								$iss_recvRtn=$val[("recv_rtn")];


								$total_recQnty=$rec_qty+$rec_tranfIn+$rec_issueRtn;

								$total_issQnty=$iss_qty+$iss_trnsOut+$iss_recvRtn;

								//$iss_qty_cal=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
									<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
									<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
									<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>

									<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
									<td width="100" title="<? echo $po_ids;?>"><p>
										<? 
											$po_ids_exp=explode(",", $po_ids);
											$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
											foreach ($po_ids_exp as $row) 
											{
												if($po_id_shipingStatus_arr[$row]==3)
												{
													$full_shipSts_countx++;
												}
												else if($po_id_shipingStatus_arr[$row]==2){
													$partial_shipSts_countx++;
												}
												else if($po_id_shipingStatus_arr[$row]==1){
													$panding_shipSts_countx++;
												}
												$poId_countx++;
											}
											if($full_shipSts_countx==$poId_countx){
												$ShipingStatus="Full Delivery/Closed";
											}
											else if ($partial_shipSts_countx==$poId_countx) {
												$ShipingStatus="Partial Delivery";
											}
											else if ($panding_shipSts_countx==$poId_countx) {
												$ShipingStatus="Full Pending";
											}
											else
											{
												$ShipingStatus="Partial Delivery";
											}
											echo $ShipingStatus;
										?></p></td>
									<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
									<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>



									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_receive_popup');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_issue_rtn_popup');"><? echo number_format($today_issueRtn,2,'.',''); ?>&nbsp;</p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_transf_in_popup');"><? echo number_format($today_transIn,2,'.',''); ?>&nbsp;</p></td>


									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_issue_rtn_popup');"><? echo number_format($rec_issueRtn,2,'.',''); ?></a></p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_transf_in_popup');"><? echo number_format($rec_tranfIn,2,'.',''); ?></a></p></td>

									<td width="80" align="right"><p><?
									$rec_bal=$book_qty-($total_recQnty-$rec_issueRtn);
									//$rec_bal=$val[("balance_qnty")];
									echo number_format($rec_bal,2,'.','');

									?></p></td>


									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_issue_popup');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_recv_rtn_popup');"><? echo number_format($today_recvRtn,2,'.',''); ?></a></p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_transf_out_popup');"><? echo number_format($today_transOut,2,'.',''); ?></a></p></td>




									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_issue_popup');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_recv_rtn_popup');"><? echo number_format($iss_recvRtn,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_transf_out_popup');"><? echo number_format($iss_trnsOut,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>

									<td width="" align="right" title="<? echo "(Receive= ".$rec_qty."+ "."Issue Return=". $rec_issueRtn."+  Transfer In=".$rec_tranfIn.") - ("."Issue= ".$iss_qty."+ Receive Return=".$iss_recvRtn."+ Transfer Out=".$iss_trnsOut; ?>)" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','woven_knit_stock_popup');"><?
									//$stock=$rec_qty_cal-$iss_qty_cal; old
									$stock=($total_recQnty-$total_issQnty); //new $rec_qty
									echo number_format($stock,2,'.','');
									?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>

								</tr>
								<?
								$i++;

								$total_req_qty+=$book_qty;

								$total_rec_qty+=$rec_qty;
								$total_rec_issueRtn_qty+=$rec_issueRtn;
								$total_rec_tranfIn_qty+=$rec_tranfIn;



								$total_rec_bal+=$rec_bal;

								$total_issue_qty+=$iss_qty;
								$total_issue_recvRtn_qty+=$iss_recvRtn;
								$total_issue_trnsOut_qty+=$iss_trnsOut;

								$total_stock+=$stock;

								$total_today_issue+=$today_issue;
								$total_today_recvRtn+=$today_recvRtn;
								$total_today_transOut+=$today_transOut;

								$total_today_recv+=$today_recv;
								$total_today_issueRtn+=$today_issueRtn;
								$total_today_transIn+=$today_transIn;
							}
						}
					}
					?>
				</table>
				<table width="2120" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="50"></th>
						<th width="110">&nbsp;</th>

						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="220">Total</th>

						<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
						
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rtn_qnty"><? echo number_format($total_today_issueRtn,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_trans_in_qnty"><? echo number_format($total_today_transIn,2,'.',''); ?></th>
						

						<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_rec_ret_qty"><? echo number_format($total_rec_issueRtn_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_rec_trns_qty"><? echo number_format($total_rec_tranfIn_qty,2,'.',''); ?></th>

						<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>

						<th width="80"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
						<th width="80"  id="value_total_today_issue_rcv_rtn_qty"><? echo number_format($total_today_recvRtn,2,'.',''); ?></th>
						<th width="80"  id="value_recv_total_today_issue_trns_out_qnty"><? echo number_format($total_today_transOut,2,'.',''); ?></th>
						
						<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_issue_ret_qty"><? echo number_format($total_issue_recvRtn_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_issue_trns_qty"><? echo number_format($total_issue_trnsOut_qty,2,'.',''); ?></th>
						

						<th width="" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}
if($action=="report_generate6")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name 	=$cbo_company_id;
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_comm 	= str_replace("'","",$txt_search_comm);
	$cbo_report_type 	= str_replace("'","",$cbo_report_type);
	$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);
	$company_name=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_id);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	$txt_inter_ref=str_replace("'","",$txt_inter_ref);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_styleref_no=str_replace("'","",$txt_styleref_no);
	//$txt_season="%".trim(str_replace("'","",$txt_season))."%";
	//if($txt_batch_no!="")
	$company_arr=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
  

	//FAL-17-00138
	//if($txt_file_no!="") $file_cond=" and e.file_no='$txt_file_no'";else $file_cond="";
	//if($txt_batch_no!="") $batch_cond=" and a.batch_no='$txt_batch_no'";else $batch_cond="";
	//if($txt_inter_ref!="") $ref_cond=" and e.grouping='$txt_inter_ref'";else $ref_cond="";
	//if($txt_inter_ref!="") $inter_ref_con=" and c.grouping='$txt_inter_ref'";else $inter_ref_con="";
	
	if($db_type==2)
	{
		$group_con="LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id";
	}
	else
	{
		$group_con="group_concat(distinct(b.po_id)) as po_id";
	}
	if($cbo_buyer_name>0) $buyer_cond=" and d.buyer_name=$cbo_buyer_name"; else  $buyer_cond="";





	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if($txt_search_comm!="") $job_cond=" and d.job_no='$txt_search_comm'";else $job_cond="";
		//if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and d.job_no in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if($txt_search_comm!="") $styleRef_cond=" and d.style_ref_no='$txt_search_comm'";else $styleRef_cond="";
		//if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and d.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if($txt_search_comm!="") $po_cond=" and e.po_number='$txt_search_comm'";else $po_cond="";
		//if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if($txt_search_comm!="") $file_cond=" and e.file_no='$txt_search_comm'";else $file_cond="";
		//if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and e.file_no='$txt_search_comm'";
	}
	else if($cbo_search_by==5)
	{
		if($txt_search_comm!="") $ref_cond=" and e.grouping='$txt_search_comm'";else $ref_cond="";
		if($txt_search_comm!="") $inter_ref_con=" and c.grouping='$txt_search_comm'";else $inter_ref_con="";
		//if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '$txt_search_comm'";
	}
	else
	{
		$search_cond.="";
	}

	///$date_from=str_replace("'","",$txt_date_from);
	//if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	//if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	//$date_from=str_replace("'","",$txt_date_from);
	//if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		//$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		//$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	if($db_type==0)
	{
		$select_fld= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	ob_start();

	if($cbo_report_type==1)
	{
		
		$constructtion_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
		foreach( $data_array as $row )
		{
			$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		}
	 	/*$sql_res="SELECT a.batch_no,a.id,d.company_name,d.buyer_name,d.style_ref_no,e.file_no,e.grouping,a.color_id,$group_con,sum(b.batch_qnty) as batch_qnty,e.job_no_mst, b.item_description, f.detarmination_id,LISTAGG(cast(f.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY f.id) as prod_id 
	 	from pro_batch_create_mst a,pro_batch_create_dtls b , wo_po_details_master d,wo_po_break_down e, product_details_master f 
	 	where b.po_id=e.id and d.id=e.job_id and a.id=b.mst_id and b.prod_id=f.id and a.entry_form in(0,37)  and a.status_active=1 and b.status_active=1  and d.company_name=$cbo_company_name and a.batch_against != 2 $buyer_cond  $batch_cond $file_cond $job_cond $ref_cond $styleRef_cond $po_cond 
	 	group by a.batch_no,a.id,d.buyer_name,a.color_id,d.company_name,e.file_no,e.grouping,e.job_no_mst,d.style_ref_no, b.item_description, f.detarmination_id,f.id order by a.color_id, e.job_no_mst";*/


	echo $sql_res=	"SELECT a.batch_no,a.id,d.company_name,d.buyer_name,d.style_ref_no,e.file_no,e.grouping,a.color_id,LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id


		,sum(b.batch_qnty) as batch_qnty,
		e.job_no_mst, b.item_description, f.detarmination_id,LISTAGG(cast(f.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY f.id) as prod_id 

		,g.body_part_id,g.item_number_id
		 from pro_batch_create_mst a,pro_batch_create_dtls b,wo_booking_dtls c,WO_PRE_COST_FABRIC_COST_DTLS g, wo_po_details_master d,wo_po_break_down e, product_details_master f 
		 where b.po_id=e.id and d.id=e.job_id
		 
		  and e.id=c.po_break_down_id and b.po_id=c.po_break_down_id  and c.job_no=d.job_no 
		  and c.pre_cost_fabric_cost_dtls_id=g.id and d.job_no=g.job_no 
		  
		  and a.id=b.mst_id  and b.prod_id=f.id and a.entry_form in(0,37) and a.status_active=1 and b.status_active=1
		  and d.company_name='9' and a.batch_against != 2 and e.grouping='reportdyeingaop99' 
		group by a.batch_no,a.id,d.buyer_name,a.color_id,d.company_name,e.file_no,e.grouping,e.job_no_mst,d.style_ref_no, b.item_description, f.detarmination_id,f.id

		,g.body_part_id,g.item_number_id
		order by a.color_id, e.job_no_mst ";




		//echo $sql_res; die;
		// and A.BATCH_NO='B400' 
		$header=sql_select($sql_res);
		$batchreport = array();
		$batchIdArray = array();
		$poIdArray = array();
		$contru_arr = array();
		$all_po_id="";
		foreach($header as $row)
		{
			if ($sub_group_arr[$row[csf('color_id')]]=='')
			{
				$i=0;
				$sub_group_arr[$row[csf('color_id')]]=$row[csf('color_id')];
			}
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['color_id']=$row[csf('color_id')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['po_id']=$row[csf('po_id')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['batch_no']=$row[csf('batch_no')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['body_part_id']=$row[csf('body_part_id')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['item_description']=$row[csf('item_description')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['job_no_mst']=$row[csf('job_no_mst')];
			// $batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['production_date']=$row[csf('production_date')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['company_name']=$row[csf('company_name')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['id']=$row[csf('id')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['buyer_name']=$row[csf('buyer_name')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['grouping']=$row[csf('grouping')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['style_ref_no']=$row[csf('style_ref_no')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['file_no']=$row[csf('file_no')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['item_number_id']=$row[csf('item_number_id')];

			$itemNumberArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('item_description')]]['item_number_id']=$row[csf('item_number_id')];
			$batchIdArray[$row[csf('id')]] = $row[csf('id')];
			$poIdArray[$row[csf('po_id')]] = $row[csf('po_id')];
			$fabricSlicing=explode(",", $row[csf('item_description')]);
			$fabricDes=$fabricSlicing[0];
			$gsm=$fabricSlicing[2];
			$dia=$fabricSlicing[3];

			$prodIdArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['prod_id']=$row[csf('prod_id')];
			$prodIdArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
			$prodIdArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['item_description']=$fabricDes;
			$prodIdArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['gsm']=$gsm;
			$prodIdArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['dia']=$dia;

			// $constructionArray[$row[csf('item_description')]] = $row[csf('item_description')];
			$construction_name=$constructtion_arr[$row[csf('detarmination_id')]];
			//$contru_arr[$constructtion_arr[$row[csf('detarmination_id')]]]=$construction_name;
			$batch_qty_arr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$construction_name]+=$row[csf('batch_qnty')];

			$prodIdArr2[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$construction_name]['prod_id']=$row[csf('prod_id')];
			
			if($all_po_id=="") $all_po_id=$row[csf('po_id')]; else $all_po_id.=",".$row[csf('po_id')]; //echo $all_po_id;
			$i++;
		}		
		// echo "<pre>"; print_r($contru_arr);die;
		//echo "<pre>"; print_r($prodIdArr2);//die;

		/*$contru_arr=array();
		foreach ($constructionArray as $key => $value) 
		{
			$contru=explode(",", $value);
			$contru_arr[$contru[0]]=$contru[0];
		}
		$count_constr=count($contru_arr);*/
		//$count_constr=count($contru_arr);

		if(count($batchreport)==0)
		{
			?>
			<div style="font-weight: bold;color: red;font-size: 20px;text-align: center;">Data not found! Please try again.</div>
			<?
			die();
		}
		//===============================================================
		$batchIds = implode(",", $batchIdArray);
		if($db_type==2 && count($batchIdArray)>1000)
		{
			$batchIds_cond=" and (";
			$batchIdsArr=array_chunk($batchIdArray,990);
			foreach($batchIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$batchIds_cond.=" a.batch_id  in ($ids) or ";
			}
			$batchIds_cond=chop($batchIds_cond,'or ');
			$batchIds_cond.=")";
		}
		else
		{
			$batchIds_cond=" and  a.batch_id  in ($batchIds)";
		}
		//===========================================================

		$poIds=chop($all_po_id,','); 
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		$poIds_cond="";$poIds_cond2="";$poIds_cond3="";
		if($db_type==2 && $po_ids>1000)
		{
			$poIds_cond=" and (";
			//$poIdsArr=array_chunk($poIds_Arr,990);
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" b.po_break_down_id  in ($ids) or ";
				$poIds_cond2.=" a.po_break_down_id  in ($ids) or ";
				$poIds_cond3.=" b.to_order_id  in ($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
			$poIds_cond2=chop($poIds_cond2,'or ');
			$poIds_cond2.=")";
			$poIds_cond3=chop($poIds_cond3,'or ');
			$poIds_cond3.=")";
		}
		else
		{
			$poIdsx=implode(",",array_unique(explode(",",$all_po_id)));
			$poIds_cond=" and  b.po_break_down_id  in ($poIdsx)";
			$poIds_cond2=" and  a.po_break_down_id  in ($poIdsx)";
			$poIds_cond3=" and  b.to_order_id  in ($poIdsx)";
		}

		// ====================== Req. Qty As Per Booking Color Wise ========================
		$grey_fabric_qnty=sql_select("SELECT a.job_no,a.fabric_color_id, d.construction, (a.grey_fab_qnty) as grey_fab_qnty,(a.fin_fab_qnty) as fin_fab_qnty
		from wo_booking_dtls a, wo_po_break_down c, wo_pre_cost_fabric_cost_dtls d
		where a.po_break_down_id=c.id and a.pre_cost_fabric_cost_dtls_id=d.id $inter_ref_con $poIds_cond2
		and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ");
		//group by a.job_no,a.fabric_color_id, d.construction
		 
		$grey_fabric_qnty_array=array();
		foreach($grey_fabric_qnty as $row)
		{
			$grey_fabric_qnty_array[$row[csf("job_no")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['grey_fab_qnty']+=$row[csf("grey_fab_qnty")];
			$grey_fabric_qnty_array[$row[csf("job_no")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['fin_fab_qnty']+=$row[csf("fin_fab_qnty")];
		}

		// ==================== QC Pass Qty, Received qty and Issue Return qty and Transfer In and Out ==================
		$batchIds_cond_fin = str_replace("a.batch_id", "b.batch_id", $batchIds_cond); 
		$batchIds_cond_fin_out = str_replace("a.batch_id", "b.batch_id", $batchIds_cond); 
		$batchIds_cond_fin_in = str_replace("a.batch_id", "b.to_batch_id", $batchIds_cond); 
		
		$data_array_finish_qnty_transfer_in=sql_select("select a.entry_form,b.color_id, b.to_batch_id as batch_id, e.detarmination_id as fabric_description_id, sum(d.quantity) as trans_in_qnty,b.to_prod_id as prod_id,f.grouping as internal_ref,g.style_ref_no,g.buyer_name,g.job_no,e.item_description,b.to_order_id ,b.to_body_part  from inv_item_transfer_mst a,inv_item_transfer_dtls b,inv_transaction c, order_wise_pro_details d,product_details_master e,wo_po_break_down f,wo_po_details_master g where a.id=b.mst_id and b.to_trans_id=c.id and c.id=d.trans_id and b.to_prod_id=e.id and b.to_order_id=f.id and f.job_id=g.id and  d.entry_form in(14) and a.entry_form in(14) and c.item_category=2 and c.transaction_type=5 and d.trans_type=5 and a.to_company=$company_name $poIds_cond3 group by a.entry_form,b.color_id, b.to_batch_id, e.detarmination_id,b.to_prod_id,f.grouping,g.style_ref_no,g.buyer_name,g.job_no,e.item_description,b.to_order_id ,b.to_body_part  ");


	
		$data_array_finish_qnty_transfer_out=sql_select("select a.entry_form,b.color_id, b.batch_id, e.detarmination_id as fabric_description_id, sum(d.quantity) as trans_out_qnty,b.from_prod_id as prod_id,e.item_description,b.body_part_id  from inv_item_transfer_mst a,inv_item_transfer_dtls b,inv_transaction c, order_wise_pro_details d,product_details_master e where a.id=b.mst_id and b.trans_id=c.id and c.id=d.trans_id  and b.from_prod_id=e.id and  d.entry_form in(14) and a.entry_form in(14) and c.item_category=2 and c.transaction_type=6 and d.trans_type=6 and a.company_id=$company_name $batchIds_cond_fin_out group by a.entry_form,b.color_id, b.batch_id, e.detarmination_id,b.from_prod_id,e.item_description,b.body_part_id ");
		$finishing_trans_in_qty_array=array();$finishing_trans_out_qty_array=array();$trnsfInArr=array();$contru_arrx=array();$batchIDS="";
		foreach($data_array_finish_qnty_transfer_in as $row)
		{
			$finishing_trans_in_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("to_body_part")]]+=$row[csf("trans_in_qnty")];
			$prodIdArrsx[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("to_body_part")]]['prod_id']=$row[csf("prod_id")];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('item_description')]][$row[csf('to_body_part')]]['color_id']=$row[csf('color_id')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('item_description')]][$row[csf('to_body_part')]]['job_no']=$row[csf('job_no')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('item_description')]][$row[csf('to_body_part')]]['batch_id']=$row[csf('batch_id')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('item_description')]][$row[csf('to_body_part')]]['buyer_name']=$row[csf('buyer_name')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('item_description')]][$row[csf('to_body_part')]]['internal_ref']=$row[csf('internal_ref')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('item_description')]][$row[csf('to_body_part')]]['style_ref_no']=$row[csf('style_ref_no')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('item_description')]][$row[csf('to_body_part')]]['to_order_id']=$row[csf('to_order_id')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('item_description')]][$row[csf('to_body_part')]]['item_number_id']=$itemNumberArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('to_body_part')]][$row[csf('item_description')]]['item_number_id'];

			$fabricSlicing=explode(",", $row[csf('item_description')]);
			$fabricDes=$fabricSlicing[0];
			$gsm=$fabricSlicing[2];
			$dia=$fabricSlicing[3];

			$construction_namex=$constructtion_arr[$row[csf('fabric_description_id')]];
			$contru_arrx[$constructtion_arr[$row[csf('fabric_description_id')]]]=$construction_namex;
			$batchIDS.=$row[csf('batch_id')].",";
			//echo $row[csf('batch_id')].",";
		}
		//print_r($contru_arrx);
		$batchIDS=chop($batchIDS,",");
		$batch_library=return_library_array("select id, batch_no from pro_batch_create_mst where id in($batchIDS)", "id", "batch_no");

		$data_array_transfer_in_issue=sql_select("SELECT c.color_id, c.id as batch_id, d.detarmination_id, (b.cons_quantity) as issue_qnty,b.prod_id,d.item_description,b.body_part_id 
		 from inv_issue_master a, inv_transaction b, pro_batch_create_mst c, product_details_master d
		where a.id=b.mst_id and b.pi_wo_batch_no=c.id  and b.prod_id=d.id and a.item_category=2 and a.entry_form in (71,18) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.pi_wo_batch_no in ($batchIDS) ");//group by c.color_id, c.id, d.detarmination_id
		$issue_transIn_array=array();
		foreach($data_array_transfer_in_issue as $row)
		{
			$issue_transIn_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("body_part_id")]]+=$row[csf("issue_qnty")];
			$prodIdArrsTransIn[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("body_part_id")]]['prod_id']=$row[csf("prod_id")];
		}

		//print_r($issue_transIn_array);

		foreach($data_array_finish_qnty_transfer_out as $row)
		{
			$finishing_trans_out_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("body_part_id")]]+=$row[csf("trans_out_qnty")];
			$prodIdArrs[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("body_part_id")]]['prod_id']=$row[csf("prod_id")];
		}

		$data_array_finsing_qty=sql_select("SELECT a.entry_form, c.color_id, b.batch_id, b.fabric_description_id, sum(b.receive_qnty) as receive_qnty,b.prod_id,d.item_description,b.body_part_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c ,product_details_master d  
		where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.id=b.mst_id  and b.batch_id=c.id and b.prod_id=d.id and  a.entry_form in(37,7,52) and a.company_id=$company_name $batchIds_cond_fin group by a.entry_form,c.color_id, b.batch_id, b.fabric_description_id,b.prod_id,d.item_description,b.body_part_id");

		$qc_pass_qnty_array=array();$finishing_rec_qty_array=array();
		foreach($data_array_finsing_qty as $row)
		{
			if($row[csf("entry_form")]==7) // finish fabric production entry page
			{
				//$qc_pass_qnty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("receive_qnty")];
				$qc_pass_qnty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("body_part_id")]]+=$row[csf("receive_qnty")];
			}
			else if($row[csf("entry_form")]==37) // Knit Finish Fabric Receive By Garments
			{
				//$finishing_rec_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("receive_qnty")];
				//$prodIdArrs[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]['prod_id']=$row[csf("prod_id")];
				$finishing_rec_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("body_part_id")]]+=$row[csf("receive_qnty")];
				$prodIdArrs[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("body_part_id")]]['prod_id']=$row[csf("prod_id")];
			}
			else // 52 Knit Finish Fabric Issue Return
			{
				//$finish_issue_rtn_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("receive_qnty")];
				$finish_issue_rtn_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("body_part_id")]]+=$row[csf("receive_qnty")];
			}
		}
		//echo "<pre>";print_r($finishing_rec_qty_array);die;

		// =============================== del. to store ============================
		$batchIds_cond_fin = str_replace("a.batch_id", "b.batch_id", $batchIds_cond); 
		$sql_del_store=sql_select("SELECT b.batch_id,a.color_id, b.determination_id,sum(b.current_delivery) as delivery from pro_grey_prod_delivery_dtls b,pro_batch_create_mst a where b.batch_id=a.id and  b.status_active=1 and b.is_deleted=0 and b.entry_form=54 $batchIds_cond_fin group by b.batch_id,a.color_id, b.determination_id");
		$delivery=array();
		foreach($sql_del_store as $row)
		{
			$delivery[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("determination_id")]]]+=$row[csf("delivery")];
			
		}
		// echo "<pre>";print_r($delivery);die;

		// =================================== issue =======================================
		$batchIds_cond_issue = str_replace("a.batch_id", "b.pi_wo_batch_no", $batchIds_cond);
		$data_array_issue=sql_select("SELECT c.color_id, c.id as batch_id, d.detarmination_id, (b.cons_quantity) as issue_qnty,b.prod_id,b.body_part_id,d.item_description,a.issue_purpose  
		 from inv_issue_master a, inv_transaction b, pro_batch_create_mst c, product_details_master d
		where a.id=b.mst_id and b.pi_wo_batch_no=c.id  and b.prod_id=d.id and a.item_category=2 and a.entry_form in (71,18) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 $batchIds_cond_issue ");//group by c.color_id, c.id, d.detarmination_id
		$issue_array=array();
		foreach($data_array_issue as $row)
		{
			$issue_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("body_part_id")]]+=$row[csf("issue_qnty")];
			if($row[csf("issue_purpose")]==9)
			{
				$issue_array_swing[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("body_part_id")]]+=$row[csf("issue_qnty")];

			}
			else
			{
				$issue_array_others[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("body_part_id")]]+=$row[csf("issue_qnty")];

			}
			$prodIdArrs[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("item_description")]][$row[csf("body_part_id")]]['prod_id']=$row[csf("prod_id")];
		}
		// echo "<pre>";print_r($issue_array);die;

		//$table_width=2430;
		// $table_width=1030;
		$table_width=(6*330)+100;

		ob_start();
		?>
		
		<style type="text/css">
			/*#td_idss{border:none !important;}
			#td_color_idsss{border:none !important;}*/
			#change_size{font-size:12px;}
		</style>
		<fieldset style="width:<? echo $table_width;?>px" >
		    <table cellpadding="0" align="center" cellspacing="0" width="<? echo $table_width-20; ?>">
				<tr>
				   <td  width="100%" colspan="24" class="form_caption"><? echo $report_title; ?></td>
				   <!-- "<div style='color:red'> Ext. Batch not allowed.</div>". -->
				</tr>
			</table>
			<table width="915" align="center">
	            <tr>
		            <td id="change_size"><strong>Buyer Name:</strong>
	                <?php
					$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
					$all_buyer='';
					foreach($batchreport as $job_no=>$job_no_arr)
					{ 
	                    foreach ($job_no_arr as $color_id=>$colorId_arr)
	                    {
	                    	foreach ($colorId_arr as $batch_no=>$batchData)
	                    	{ 
	                    		foreach ($batchData as $body_partID=>$bodyPartData)
	                    		{
	                    			foreach ($bodyPartData as $productDescription=>$row)
		                    		{
							  			if($all_buyer=='' ) $all_buyer= $row['buyer_name']; else $all_buyer.=",".$row['buyer_name'];
							  		}
						  		}
						  	}
						}
					}
				   	$all_buyer_ids='';
				   	$buyer_ids=array_unique(explode(",",$all_buyer));
				   	foreach($buyer_ids as $bid)
				   	{
					 	if($all_buyer_ids=='' ) $all_buyer_ids=$buyer_arr[$bid]; else $all_buyer_ids.=",".$buyer_arr[$bid];  
				   	}
					echo $all_buyer_ids;//implode(",",array_unique(explode(",",$buyer_arr[$all_buyer])));
					?>
	                </td>
		            <td width="100"></td>
		            <td id="change_size"><strong>Style Ref. No:</strong>
	                <?php
						$all_style_ref='';
						foreach($batchreport as $job_no=>$job_no_arr)
						{ 
		                    foreach ($job_no_arr as $color_id=>$colorId_arr)
		                    {
		                    	foreach ($colorId_arr as $batch_no=>$batchData)
		                    	{
		                    		foreach ($batchData as $body_partID=>$bodyPartData)
	                    			{
	                    				foreach ($bodyPartData as $productDescription=>$row)
		                    			{
							  				if($all_style_ref=='' ) $all_style_ref= $row['style_ref_no']; else $all_style_ref.=",".$row['style_ref_no'];
							  			}
							  		}
							  	}
							}
						}
						echo implode(",",array_unique(explode(",",$all_style_ref)));
					?>
	                </td>
	                <td width="100"></td>
	                <td id="change_size"><strong>Internal Ref:</strong>
					<?php
					$all_in_ref='';
					foreach($batchreport as $job_no=>$job_no_arr)
					{ 
	                    foreach ($job_no_arr as $color_id=>$colorId_arr)
	                    {
	                    	foreach ($colorId_arr as $batch_no=>$batchData)
	                    	{ 
	                    		foreach ($batchData as $body_partID=>$bodyPartData)
	                    		{
	                    			foreach ($bodyPartData as $productDescription=>$row)
		                    		{
										if($all_in_ref=='' ) $all_in_ref= $row['grouping']; else $all_in_ref.=",".$row['grouping'];
									}
								}
							}
						}
					}
					echo implode(",",array_unique(explode(",",$all_in_ref)));
					?> 
	                </td>
	                <td width="100"></td>
	                <td><strong>Job No:</strong><?php
					$all_job='';
					foreach($batchreport as $job_no=>$job_no_arr)
					{ 
	                    foreach ($job_no_arr as $color_id=>$colorId_arr)
	                    {
	                    	foreach ($colorId_arr as $batch_no=>$batchData)
	                    	{
	                    		foreach ($batchData as $body_partID=>$bodyPartData)
	                    		{
	                    			foreach ($bodyPartData as $productDescription=>$row)
		                    		{
	                    				if($all_job=='' ) $all_job= $row['job_no_mst']; else $all_job.=",".$row['job_no_mst'];
	                    			}
	                    		}
	                    	}						  	
						}
					}
					echo implode(",",array_unique(explode(",",$all_job)));
					?></td>
					<td width="100"></td>
		        </tr>
	        </table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="<? echo $table_width-20; ?>">
				<thead>
					<tr>
	                	<th width="35">SL.</th>
						<th width="100">Buyer</th>
						<th width="100">Style</th>
						<th width="100">Internal Ref</th>
						<!-- <th width="100">Job No</th> -->
						<th width="100">Gmt Item</th>
						<th width="100">Color Name</th>
						<!-- <th width="100">Batch No</th> -->
						<th width="100">Body Part</th>
						<th width="200">Fabrication</th>
						<th width="50">GSM</th>

						<th width="50">Unit</th>
						<th width="50">Booking</th>
						<th width="50">Body to Trim Ratio</th>
						<th width="50">Additionl Booking</th>
						<th width="50">Total Booking</th>

	                  				
						<th width="80">Received</th>
						<th width="80">Transfer In</th>
						<th width="80">Transfer Out</th>
						<th width="80">Total Receive</th>
						<th width="80">Receive Balance</th>

						<th width="80">Issue Return</th>
						

						<th width="80">Issue to Cutting</th>
						
						<th width="80">Stock In Hand</th>
						
						<th >Comments</th>
	                </tr>
	                
				</thead>
	        </table> 
	        <div style="width:<? echo $table_width;?>px; overflow-y:scroll;  max-height:400px;" id="scroll_body">
	            <table id="tbl_list_search" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="<? echo $table_width-20; ?>">
	            	
		            <?
					$i=1;
					foreach($batchreport as $job_no=>$job_no_arr)
					{						
						foreach ($job_no_arr as $color_id=>$colorId_arr)
	                    {
	                    	$fab_finQty_color_bal_arr=array();

	                    	$color_total_recv_arr=array();
	                    	$color_total_issue_rtn_arr=array();
	                    	$color_total_trans_in_arr=array();

	                    	$color_balance_recv_qty_arr=array();
	                    	$color_balance_issue_rtn_qty_arr=array();
	                    	$color_balance_trans_in_qty_arr=array();

	                    	$color_total_issue_arr=array();
	                    	$color_total_trans_out_arr=array();
	                    	$color_balance_issue_qty_arr=array();
	                    	$color_balance_trans_out_qty_arr=array();

	                    	$color_total_inHand_arr=array();
	                    	$color_total_inHand_transIn_arr=array();
	                    	$color_balance_inHand_qty_arr=array();

	                    	
		                    foreach ($colorId_arr as $batch_no=>$batchData)
		                    {
		                    	foreach ($batchData as $body_partID=>$bodyPartData)
	                    		{
	                    			foreach ($bodyPartData as $productDescription=>$row)
		                    		{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$po_ids=array_unique(explode(",",$row[("po_id")]));
				                       	?>
				                        
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				    						<td width="35" align="left" id="td_id" valign="middle"><? echo $i; ?></td>
				                            
				                            <td width="100"><p><? echo $buyer_arr[$row[("buyer_name")]]; ?></p></td>
				                            <td width="100"><p><? echo $row[("style_ref_no")]; ?></p></td>
				                            <td width="100"><p><? echo $row[("grouping")]; ?></p></td>
				                            <td width="100"><p><? echo $garments_item[$row[("item_number_id")]]; ?></p></td>

				                           <!--  <td width="100"><p><? //echo $job_no; ?></p></td> -->

				                            <td width="100" id="td_color_id" title="ColorId=<? echo $color_id;?>" valign="middle"><div style="word-break:break-all"><? echo $color_library[$row["color_id"]]; ?></div></td>
				                           
				                            <!-- <td width="100" title="Batch ID:<? //echo $row[("id")]; ?>"><div style="word-break:break-all"><? //echo $row[("batch_no")];?></div></td> -->

				                            <td width="100" title="<? echo $row[("body_part_id")];?>"><p><? echo $body_part[$row[("body_part_id")]]; ?></p></td>
				                            <td width="200"><p><? echo $productDescription; ?></p></td>
				                            <td width="50" align="center"><p><? $gsmArr= explode(",",$productDescription); echo $gsmArr[2]; ?></p></td>
				                            <td width="50" align="center"><p><?  ?></p></td>
				                            <td width="50" align="right"><p><? ?></p></td>
				                            <td width="50" align="right"><p><? ?></p></td>
				                            <td width="50" align="right"><p><? ?></p></td>
				                            <td width="50" align="right"><p><? ?></p></td>
				                            
				                            <?

						                	//fin recv qty
						                		//$fin_recv_qnty=$finishing_rec_qty_array[$color_id][$row["id"]][$contruction];
						                		//$pordIDS=$prodIdArrs[$color_id][$row["id"]][$contruction]['prod_id'];
						                		$fin_recv_qnty=$finishing_rec_qty_array[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]];
						                		$pordIDS=$prodIdArrs[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]]['prod_id'];
						                		
						                		?>
						                		<td width="80" align="right"><p><a href='#report_details' onClick="openmypageShow6('<? echo $row["po_id"]; ?>','<? echo $pordIDS; ?>','<? echo $color_id; ?>','<? echo 1; ?>','total_receive_popup_show6','<? echo $row[("id")]; ?>','<? echo $job_no; ?>','<? echo $row[("buyer_name")]; ?>','<? echo $row[("style_ref_no")]; ?>','<? echo $row[("grouping")]; ?>','<? echo $prodIdArr[$job_no][$color_id][$row[("id")]]['detarmination_id']; ?>','<? echo $productDescription; ?>','<? echo $gsm; ?>','<? echo $dia; ?>');"><? echo number_format($fin_recv_qnty,2,'.',''); ?></a></p></td>
						                		<?
						                		$color_total_recv_arr[$productDescription]+=$fin_recv_qnty;
						                	

						                	//transfer In row
						                		$fin_recv_qnty=$finishing_rec_qty_array[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]];
						                		$fin_issue_rtn_qnty=$finish_issue_rtn_qty_array[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]];

						                		$fin_trans_in_qnty=$finishing_trans_in_qty_array[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]];
						                		$pordIDS=$prodIdArrs[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]]['prod_id'];

						                		$recv_qnty_for_inHand_arr[$productDescription]=$fin_recv_qnty+$fin_issue_rtn_qnty+$fin_trans_in_qnty;
						                		?>
						                		<td width="80" align="right"><p><a href='#report_details' onClick="openmypageShow6('<? echo $row["po_id"]; ?>','<? echo $pordIDS; ?>','<? echo $color_id; ?>','<? echo 1; ?>','total_trans_in_popup_show6','<? echo $row[("id")]; ?>','<? echo $job_no; ?>','<? echo $row[("buyer_name")]; ?>','<? echo $row[("style_ref_no")]; ?>','<? echo $row[("grouping")]; ?>','<? echo $prodIdArr[$job_no][$color_id][$row[("id")]]['detarmination_id']; ?>','<? echo $productDescription; ?>','<? echo $gsm; ?>','<? echo $dia; ?>');"><? echo number_format($fin_trans_in_qnty,2,'.',''); ?></a></p></td>
						                		<?
						                		$color_total_trans_in_arr[$productDescription]+=$fin_trans_in_qnty;

						                		
						                	//transfer Out
						                		$pordIDS_issue=$prodIdArrs[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]]['prod_id'];
						                		$fin_issue_qnty=$issue_array[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]];
						                		$fin_trans_out_qnty=$finishing_trans_out_qty_array[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]];
						                		
						                		$issue_qty_for_inHand_arr[$productDescription]=$fin_issue_qnty+$fin_trans_out_qnty;
						                		?>
						                		<td width="80" align="right" title="fin ret=<? //echo $fin_issue_rtn_qnty;?>">
						                		<p><a href='#report_details' onClick="openmypageShow6('<? echo $row["po_id"]; ?>','<? echo $pordIDS_issue; ?>','<? echo $color_id; ?>','<? echo 1; ?>','total_trans_out_popup_show6','<? echo $row[("id")]; ?>','<? echo $job_no; ?>','<? echo $row[("buyer_name")]; ?>','<? echo $row[("style_ref_no")]; ?>','<? echo $row[("grouping")]; ?>','<? echo $prodIdArr[$job_no][$color_id][$row[("id")]]['detarmination_id']; ?>','<? echo $productDescription; ?>','<? echo $gsm; ?>','<? echo $dia; ?>');"><? echo "-".number_format($fin_trans_out_qnty,2,'.',''); ?></a></p>
						                		</td> 
						                		<?
						                		$color_total_trans_out_arr[$contruction]+=$fin_trans_out_qnty;



						                	//Total Recv
						                		?>
						                		<td width="80" align="right"><p><? 
						                		$totalRecv=($fin_recv_qnty+$fin_trans_in_qnty)-$fin_trans_out_qnty;

						                		echo number_format($totalRecv,2,'.',''); ?></p></td>

						                		<?

						                	//Total Recv Balance
						                		?>
						                		<td width="80" align="right"><p><? 
						                		//$totalRecv=($fin_recv_qnty+$fin_trans_in_qnty)-$fin_trans_out_qnty;

						                		//echo number_format($totalRecv,2,'.',''); ?></p></td>

						                		<?


						                	//issue return row
						                		//$fin_recv_qnty=$finishing_rec_qty_array[$color_id][$row["id"]][$contruction];
						                		$fin_issue_rtn_qnty=$finish_issue_rtn_qty_array[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]];
						                		$pordIDS=$prodIdArrs[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]]['prod_id'];
						                		

						                		
						                		?>
						                		<td width="80" align="right"><p><a href='#report_details' onClick="openmypageShow6('<? echo $row["po_id"]; ?>','<? echo $pordIDS; ?>','<? echo $color_id; ?>','<? echo 1; ?>','total_issue_rtn_popup_show6','<? echo $row[("id")]; ?>','<? echo $job_no; ?>','<? echo $row[("buyer_name")]; ?>','<? echo $row[("style_ref_no")]; ?>','<? echo $row[("grouping")]; ?>','<? echo $prodIdArr[$job_no][$color_id][$row[("id")]]['detarmination_id']; ?>','<? echo $productDescription; ?>','<? echo $gsm; ?>','<? echo $dia; ?>');"><? echo number_format($fin_issue_rtn_qnty,2,'.',''); ?></a></p></td>
						                		<?
						                		$color_total_issue_rtn_arr[$productDescription]+=$fin_issue_rtn_qnty;



						                	// net issue qty
						                		$fin_issue_qnty=$issue_array[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]];
						                		$pordIDS_issue=$prodIdArrs[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]]['prod_id'];
						                		$fin_issue_rtn_qnty=$finish_issue_rtn_qty_array[$color_id][$row["id"]][$productDescription][$row[("body_part_id")]];
						                		$net_issued_qty=$fin_issue_qnty;
						                		
						                		?>
						                		<td width="80" align="right" title="fin ret=<? echo $fin_issue_rtn_qnty;?>">
						                		<p><a href='#report_details' onClick="openmypageShow6('<? echo $row["po_id"]; ?>','<? echo $pordIDS_issue; ?>','<? echo $color_id; ?>','<? echo 1; ?>','total_issue_popup_show6','<? echo $row[("id")]; ?>','<? echo $job_no; ?>','<? echo $row[("buyer_name")]; ?>','<? echo $row[("style_ref_no")]; ?>','<? echo $row[("grouping")]; ?>','<? echo $prodIdArr[$job_no][$color_id][$row[("id")]]['detarmination_id']; ?>','<? echo $productDescription; ?>','<? echo $gsm; ?>','<? echo $dia; ?>');"><? echo number_format($net_issued_qty,2,'.',''); ?></a></p>
						                		</td> 
						                		<?
						                		$color_total_issue_arr[$contruction]+=$net_issued_qty;
						                	

						                	
						                	


						                	 // Stock In Hand
						                		$recv_qty=$recv_qnty_for_inHand_arr[$contruction];
						                		$net_issue=$issue_qty_for_inHand_arr[$contruction];
						                		$stock_in_hand=$recv_qty-$net_issue;
						                		//echo $recv_qty.'-'.$net_issue;
						                		?>
						                		<td width="80" align="right"><? echo number_format($stock_in_hand,2,'.',''); ?></td>
						                		<?
						                		$color_total_inHand_arr[$contruction]+=$stock_in_hand;
						                	
						                	?>
						                	<td ></td>
										</tr>
							 			<?
			                            $i++;
			                        }
		                        }
		                    } 

		                    //$finishing_trans_in_qty_array[$row[csf("job_no")]][$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]][$row[csf("internal_ref")]][$row[csf("style_ref_no")]][$row[csf("buyer_name")]]

		                    //================
		                    	//transfer  in part in this block
		                    foreach($trnsfInArr as $job_nox=>$job_no_arrx)
							{						
								foreach ($job_no_arrx as $color_idx=>$colorId_arrx)
			                    {
			                    	foreach ($colorId_arrx as $batch_idx=>$batchData)
		                    		{
		                    			foreach ($batchData as $fabricDescName=>$fabricDescData)
			                    		{
			                    			foreach ($fabricDescData as $bodyPartId=>$row)
				                    		{
			                    				if($color_idx==$color_id)
				                    			{

				                    			
					                    			?>

								                    	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								    						<td width="35" align="left" id="td_id" valign="middle"><? echo $i; ?></td>
								                           
								                            <td width="100"><p><? echo $buyer_arr[$row[("buyer_name")]]; ?></p></td>
								                            <td width="100"><p><? echo $row[("style_ref_no")]; ?></p></td>
								                            <td width="100"><p><? echo $row[("internal_ref")]; ?></p></td>
								                            <td width="100"><p><? echo $garments_item[$row[("item_number_id")]]; ?></p></td>
								                            <!-- <td width="100"><p><? //echo $job_nox; ?></p></td> -->
								                            <td width="100" id="td_color_id" title="ColorId=<? echo $color_idx;?>" valign="middle"><div style="word-break:break-all"><? echo $color_library[$color_idx]; ?></div></td>
								                           <!--  <td width="100" title="Batch ID:<? //echo $batch_idx; ?>"><div style="word-break:break-all"><?  //echo $batch_library[$batch_idx];?></div></td> -->
								                            <td width="100" title="<? echo $bodyPartId; ?>"><p><? echo $body_part[$bodyPartId]; ?></p></td>
				                            				<td width="200"><p><? echo $fabricDescName; ?></p></td>
   															<td width="50" align="center"><p><? $gsmArrs= explode(",",$fabricDescName); echo $gsmArrs[2];  ?></p></td>

   															<td width="50" align="center"><p><?  ?></p></td>
								                            <td width="50" align="right"><p><? ?></p></td>
								                            <td width="50" align="right"><p><? ?></p></td>
								                            <td width="50" align="right"><p><? ?></p></td>
								                            <td width="50" align="right"><p><? ?></p></td>
								                            <?

										                	//fin recv qty
										                		?>
										                		<td width="80" align="right"></td>
										                		<?
										                	//foreach ($contru_arrx as $key => $contruction)
										                	//transfer In row

										                		$fin_trans_in_qnty=$finishing_trans_in_qty_array[$color_idx][$batch_idx][$fabricDescName][$bodyPartId];
										                		$pordIDS=$prodIdArrsx[$color_idx][$batch_idx][$fabricDescName][$bodyPartId]['prod_id'];

										                		$trans_in_qnty_for_inHand_arr[$fabricDescName][$bodyPartId]=$fin_trans_in_qnty;

										                		//$recv_qnty_for_inHand_arr[$contruction]=$fin_recv_qnty+$fin_issue_rtn_qnty+$fin_trans_in_qnty;
										                		?>
										                		<td width="80" align="right"><p><a href='#report_details' onClick="openmypageShow6('<? echo $row["to_order_id"]; ?>','<? echo $pordIDS; ?>','<? echo $color_idx; ?>','<? echo 1; ?>','total_trans_in_popup_show6','<? echo $batch_idx; ?>','<? echo $job_nox; ?>','<? echo $row[("buyer_name")]; ?>','<? echo $row[("style_ref_no")]; ?>','<? echo $row[("internal_ref")]; ?>','<? echo $prodIdArr[$job_nox][$color_idx][$batch_idx]['detarmination_id']; ?>','<? echo $fabricDescName; ?>','<? echo $gsm; ?>','<? echo $dia; ?>');"><? echo number_format($fin_trans_in_qnty,2,'.',''); ?></a></p></td>
										                		<?
										                		$color_total_trans_in_arr[$fabricDescName]+=$fin_trans_in_qnty;

										                	//transfer Out
										                		?>
										                		<td width="80" align="right"></td> 
										                		<?

										                	//Total recv
										                		?>
										                		<td width="80" align="right"></td> 
										                		<?
										                	//Total recv Balance
										                		?>
										                		<td width="80" align="right"></td> 
										                		<?

										                	//issue return row
										                		?>
										                		<td width="80" align="right"></td>
										                		<?						                
										                	
										                	// net issue qty
										                		$fin_trans_in_issue_qnty=$issue_transIn_array[$color_idx][$batch_idx][$fabricDescName][$bodyPartId];
										                		$pordIDSTransIn=$prodIdArrsTransIn[$color_idx][$batch_idx][$fabricDescName][$bodyPartId]['prod_id'];

										                		$trans_in_issue_qty_for_inHand_arr[$fabricDescName]=$fin_trans_in_issue_qnty;

										                		/*$fin_issue_qnty=$issue_array[$color_id][$row["id"]][$contruction];
										                		$pordIDS_issue=$prodIdArrs[$color_id][$row["id"]][$contruction]['prod_id'];
										                		$fin_issue_rtn_qnty=$finish_issue_rtn_qty_array[$color_id][$row["id"]][$contruction];
										                		$net_issued_qty=$fin_issue_qnty;*/
										                		?>
										                		<td width="80" align="right">
										                		<p><a href='#report_details' onClick="openmypageShow6('<? echo $row["to_order_id"]; ?>','<? echo $pordIDSTransIn; ?>','<? echo $color_idx; ?>','<? echo 1; ?>','total_issue_popup_show6','<? echo $batch_idx; ?>','<? echo $job_nox; ?>','<? echo $row[("buyer_name")]; ?>','<? echo $row[("style_ref_no")]; ?>','<? echo $row[("internal_ref")]; ?>','<? echo $prodIdArr[$job_nox][$color_idx][$batch_idx]['detarmination_id']; ?>','<? echo $fabricDescName; ?>','<? echo $gsm; ?>','<? echo $dia; ?>');"><? echo number_format($fin_trans_in_issue_qnty,2,'.',''); ?></a></p>
										                		</td> 
										                		<?
										                		$color_total_issue_arr[$fabricDescName]+=$fin_trans_in_issue_qnty;
										                	
										                	 // Stock In Hand
										                		?>
										                		<td width="80" align="right"><? 
										                		//$fin_trans_in_qnty=$finishing_trans_in_qty_array[$color_idx][$batch_idx][$contruction];
										                		//$fin_trans_in_stock_qnty=$fin_trans_in_qnty-$fin_trans_in_issue_qnty;

										                		$transIn_recv_qty=$trans_in_qnty_for_inHand_arr[$fabricDescName];
										                		$transIn_net_issue=$trans_in_issue_qty_for_inHand_arr[$fabricDescName];
										                		$transIn_stock_in_hand=$transIn_recv_qty-$transIn_net_issue;


										                		echo $transIn_stock_in_hand;
										                		 ?></td>
										                		<?

										                		$color_total_inHand_transIn_arr[$fabricDescName]+=$transIn_stock_in_hand;
										                	
										                	?>
										                	<td></td>
														</tr>
								                    	<?
								                }
								            }
								        }
		                    		}

			                    }
			                }
		                    //================

		                    // color total below
		                    ?>
		                    <tr class="tbl_bottom">
		                        <td colspan="8"><strong>Color Total</strong></td>
		                        <?
		                       
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color total Finish Req. & Received
			                		$color_total_recv_qty = $color_total_recv_arr[$contruction];
			                		?>
			                		<td  align="right"><? echo number_format($color_total_recv_qty,2,'.',''); ?></td>
			                		<?
			                		$color_balance_recv_qty_arr[$contruction]+=$color_total_recv_qty;
			                		$job_total_recv_qty_arr[$contruction]+=$color_total_recv_qty;
			                	}

			                	foreach ($contru_arr as $key => $contruction)
			                	{ //color total issue return row
			                		$color_total_issue_rtn_qty = $color_total_issue_rtn_arr[$contruction];
			                		?>
			                		<td  align="right"><? echo number_format($color_total_issue_rtn_qty,2,'.',''); ?></td>
			                		<?
			                		$color_balance_issue_rtn_qty_arr[$contruction]+=$color_total_issue_rtn_qty;
			                		$job_total_issue_rtn_qty_arr[$contruction]+=$color_total_issue_rtn_qty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color total transfer In row
			                		$color_total_trans_in_qty = $color_total_trans_in_arr[$contruction];
			                		?>
			                		<td  align="right"><? echo number_format($color_total_trans_in_qty,2,'.',''); ?></td>
			                		<?
			                		$color_balance_trans_in_qty_arr[$contruction]+=$color_total_trans_in_qty;
			                		$job_total_trans_in_qty_arr[$contruction]+=$color_total_trans_in_qty;
			                	}


			                	foreach ($contru_arr as $key => $contruction)
			                	{//color total Finish Req. & Net. Issued qty
			                		$color_total_issue_qty = $color_total_issue_arr[$contruction];
			                		?>
			                		<td  align="right"><? echo number_format($color_total_issue_qty,2,'.',''); ?></td>
			                		<?
			                		$color_balance_issue_qty_arr[$contruction]+=$color_total_issue_qty;
			                		$job_total_issue_qty_arr[$contruction]+=$color_total_issue_qty;
			                	}


			                	foreach ($contru_arr as $key => $contruction)
			                	{//color total transfer Out
			                		$color_total_trans_out_qty = $color_total_trans_out_arr[$contruction];
			                		?>
			                		<td  align="right"><? echo number_format($color_total_trans_out_qty,2,'.',''); ?></td>
			                		<?
			                		$color_balance_trans_out_qty_arr[$contruction]+=$color_total_trans_out_qty;
			                		$job_total_trans_out_qty_arr[$contruction]+=$color_total_trans_out_qty;
			                	}



			                	foreach ($contru_arr as $key => $contruction)
			                	{
			                	//color total Stock In Hand
				                	$color_total_inHand_qtyx = $color_total_inHand_transIn_arr[$contruction];
			                		$color_total_inHand_qty = $color_total_inHand_arr[$contruction]+$color_total_inHand_qtyx;
			                		?>
			                		<td  align="right"><? echo number_format($color_total_inHand_qty,2,'.',''); ?></td>
			                		<?
			                		$color_balance_inHand_qty_arr[$contruction]+=$color_total_inHand_qty;
			                		$job_total_inHand_qty_arr[$contruction]+=$color_total_inHand_qty;
			                	}
			                	?>
			                	<td ></td>
		                    </tr>

							<tr class="tbl_bottom">
								<td colspan="8"><strong>Color Total Balance</strong></td>
		                     	<?

			                	foreach ($contru_arr as $key => $contruction)
			                	{//color Balance Finish Req. & Received
			                		//$grey_fabric_qnty_array[$job_no][$color_id][$contruction]['fin_fab_qnty']


			                		$color_recv_qty=$color_balance_recv_qty_arr[$contruction];
			                		$color_fab_finQty_qty=$fab_finQty_color_bal_arr[$contruction];

			                		$color_total_balance_recv_qty=$color_fab_finQty_qty-$color_recv_qty;
			                		?>
			                		<td  align="right"><? echo number_format($color_total_balance_recv_qty,2,'.',''); ?></td>
			                		<?
			                		$job_total_bal_recv_qty_arr[$contruction]+=$color_total_balance_recv_qty;
			                	}


			                	foreach ($contru_arr as $key => $contruction)
			                	{//color Balance issue return row
			                		//$grey_fabric_qnty_array[$job_no][$color_id][$contruction]['fin_fab_qnty']


			                		$color_issue_rtn_qty=$color_balance_issue_rtn_qty_arr[$contruction];
			                		$color_fab_finQty_qty=$fab_finQty_color_bal_arr[$contruction];

			                		$color_total_balance_issue_rtn_qty=$color_fab_finQty_qty-$color_issue_rtn_qty;
			                		?>
			                		<td  align="right"><? //echo number_format($color_total_balance_issue_rtn_qty,2,'.',''); ?></td>
			                		<?
			                		$job_total_bal_issue_rtn_qty_arr[$contruction]+=$color_total_balance_issue_rtn_qty;
			                	}
			                	foreach ($contru_arr as $key => $contruction)
			                	{//color Balance transfer In row
			                		//$grey_fabric_qnty_array[$job_no][$color_id][$contruction]['fin_fab_qnty']


			                		$color_trans_in_qty=$color_balance_trans_in_qty_arr[$contruction];
			                		$color_fab_finQty_qty=$fab_finQty_color_bal_arr[$contruction];

			                		$color_total_balance_trans_in_qty=$color_fab_finQty_qty-$color_trans_in_qty;
			                		?>
			                		<td  align="right"><? //echo number_format($color_total_balance_trans_in_qty,2,'.',''); ?></td>
			                		<?
			                		$job_total_bal_trans_in_qty_arr[$contruction]+=$color_total_balance_trans_in_qty;
			                	}



			                	foreach ($contru_arr as $key => $contruction)
			                	{//color Balance Finish Req. & Net. Issued qty
			                		$color_issue_qty=$color_balance_issue_qty_arr[$contruction];
			                		$color_fab_finQty_qty=$fab_finQty_color_bal_arr[$contruction];
			                		$color_total_balance_issue_qty=$color_fab_finQty_qty-$color_issue_qty;
			                		?>
			                		<td  align="right"><? echo number_format($color_total_balance_issue_qty,2,'.',''); ?></td>
			                		<?
			                		$job_total_bal_issue_qty_arr[$contruction]+=$color_total_balance_issue_qty;
			                	}

			                	foreach ($contru_arr as $key => $contruction)
			                	{ //color balance transfer Out
			                		$color_trans_out_qty=$color_balance_trans_out_qty_arr[$contruction];
			                		$color_fab_finQty_qty=$fab_finQty_color_bal_arr[$contruction];
			                		$color_total_balance_trans_out_qty=$color_fab_finQty_qty-$color_trans_out_qty;
			                		?>
			                		<td  align="right"><? //echo number_format($color_total_balance_trans_out_qty,2,'.',''); ?></td>
			                		<?
			                		$job_total_bal_trans_out_qty_arr[$contruction]+=$color_total_balance_trans_out_qty;
			                	}


			                	foreach ($contru_arr as $key => $contruction)
			                	{//color Balance Stock In Hand
			                		$color_inHand_qty=$color_balance_inHand_qty_arr[$contruction];
			                		$color_fab_finQty_qty=$fab_finQty_color_bal_arr[$contruction];
			                		$color_total_balance_inHand_qty=$color_fab_finQty_qty-$color_inHand_qty;
			                		?>
			                		<td  align="right"><? //echo number_format($color_total_balance_inHand_qty,2,'.',''); ?></td>
			                		<?
			                		$job_total_bal_inHand_qty_arr[$contruction]+=$color_total_balance_inHand_qty;
			                	}
			                	?>
			                	<td ></td>
					        </tr> 
		                    <?
		                }// job total below
		                ?>
				        <tr class="tbl_bottom">
	                        <td colspan="8"><strong>Job Total</strong></td>
	                        <?
		                	
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Total Finish Req. & Received
		                		$job_total_recv_qty=$job_total_recv_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_total_recv_qty,2,'.',''); ?></td>
		                		<?
		                	}

		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Total issue return row
		                		$job_total_issue_rtn_qty=$job_total_issue_rtn_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_total_issue_rtn_qty,2,'.',''); ?></td>
		                		<?
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Total transfer In row
		                		$job_total_trans_in_qty=$job_total_trans_in_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_total_trans_in_qty,2,'.',''); ?></td>
		                		<?
		                	}


		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Total Finish Req. & Net. Issued qty
		                		$job_total_issue_qty=$job_total_issue_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_total_issue_qty,2,'.',''); ?></td>
		                		<?
		                	}


		                	foreach ($contru_arr as $key => $contruction)
		                	{ //Job Total transfer Out
		                		$job_total_trans_out_qty=$job_total_trans_out_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_total_trans_out_qty,2,'.',''); ?></td>
		                		<?
		                	}



		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Total Stock In Hand
		                		$job_total_inHand_qty=$job_total_inHand_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_total_inHand_qty,2,'.',''); ?></td>
		                		<?
		                	}
		                	?>
		                	<td ></td>
	                    </tr>
	                    <tr class="tbl_bottom">
	                        <td colspan="7"><strong>Job Total Balance</strong></td>
	                        <?
	                       
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance Finish Req. & Received
		                		$job_balance_total_recv_qty=$job_total_bal_recv_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_balance_total_recv_qty,2,'.',''); ?></td>
		                		<?
		                	}

		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance issue return row
		                		//$job_balance_total_issue_rtn_qty=$job_total_bal_issue_rtn_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? //echo number_format($job_balance_total_issue_rtn_qty,2,'.',''); ?></td>
		                		<?
		                	}
		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance transfer In row
		                		//$job_balance_total_trans_in_qty=$job_total_bal_trans_in_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? //echo number_format($job_balance_total_trans_in_qty,2,'.',''); ?></td>
		                		<?
		                	}



		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance Finish Req. & Net. Issued qty
		                		$job_balance_total_issue_qty=$job_total_bal_issue_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? echo number_format($job_balance_total_issue_qty,2,'.',''); ?></td>
		                		<?
		                	}

		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance transfer Out
		                		//$job_balance_total_trans_out_qty=$job_total_bal_trans_out_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? //echo number_format($job_balance_total_trans_out_qty,2,'.',''); ?></td>
		                		<?
		                	}


		                	foreach ($contru_arr as $key => $contruction)
		                	{//Job Balance Stock In Hand
		                		$job_balance_total_inHand_qty=$job_total_bal_inHand_qty_arr[$contruction];
		                		?>
		                		<td  align="right"><? //echo number_format($job_balance_total_inHand_qty,2,'.',''); ?></td>
		                		<?
		                	}
		                	?>
		                	<td ></td>
	                    </tr>
		                <?
					}
					?>
				</table> 
			</div>
		</fieldset>
		<?
	}
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}
if($action=="report_generate7")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_report_type 	= str_replace("'","",$cbo_report_type);
	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_comm 	= str_replace("'","",$txt_search_comm);
	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);

	$cbo_shipment_status = str_replace("'","",$cbo_shipment_status);
	if($cbo_shipment_status>0){$shiping_status_cond=" and b.shiping_status in ($cbo_shipment_status)";}

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '$txt_search_comm'";
	}
	else
	{
		$search_cond.="";
	}

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	if($db_type==0)
	{
		$select_fld= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	ob_start();

	if($cbo_report_type==1)
	{
		// Knit Finish Start
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		//$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2", "id", "product_name_details");
		$product_arr=return_library_array("select a.id, b.construction from product_details_master a, lib_yarn_count_determina_mst b where a.item_category_id=2 and a.detarmination_id=b.id", "id", "construction");

		$sql_rcv="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no,b.shiping_status, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id,b.grouping,c.cons_uom,
		(case when d.entry_form in (7,37,66,68) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
		(case when d.entry_form in (52,126) and $today_receive_date then d.quantity else 0 end) as today_issue_rtn_qnty,
		(case when d.entry_form in (14,15,134,306) and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in_qnty,
		(case when d.entry_form in (7,37,66,68)  then d.quantity else 0 end) as receive_qnty,
		(case when d.entry_form in (52,126) then d.quantity else 0 end) as issue_rtn_qnty,
		(case when d.entry_form in (14,15,134,306) and d.trans_type=5 then d.quantity else 0 end) as trans_in_qnty,

		(case when d.entry_form in (18,71) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty,
		(case when d.entry_form in (46) and $today_receive_date then d.quantity else 0 end) as today_rcv_rtn_qnty,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trns_out_qnty,
		(case when d.entry_form in (46) then d.quantity else 0 end) as issue_rcv_rtn_qnty,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty

		from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
		where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id  and a.company_name=$cbo_company_id and d.entry_form in (7,14,15,18,37,46,52,66,68,71,126,134,306) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $receive_date $buyer_id_cond $search_cond $year_cond $shiping_status_cond
		order by a.job_no, d.color_id,c.transaction_date,b.grouping";

		$style_wise_arr=$color_id_arr=$prod_id_arr=$po_id_arr=array();

		$sql_rcv_res=sql_select($sql_rcv);
		foreach ($sql_rcv_res as $row)
		{
			$color_id_arr[$row[csf('color_id')]]=$row[csf('color_id')];
			$po_id_shipingStatus_arr[$row[csf('po_id')]]=$row[csf('shiping_status')];
			$prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
			$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$job_no_arr[$row[csf('job_no')]] = "'".$row[csf('job_no')]."'";

			$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
			//$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			//$fab_desc_tmp=$fab_desc_tmp1[0];

			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no']=$row[csf('job_no')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no_pre']=$row[csf('job_no_prefix_num')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['year']=$row[csf('year')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['buyer_name']=$row[csf('buyer_name')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['style_ref_no']=$row[csf('style_ref_no')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['grouping'].=$row[csf('grouping')].",";
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_id'].=$row[csf('po_id')].',';
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_no'].=$row[csf('po_no')].',';
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['receive_qnty']+=$row[csf('receive_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_rtn_qnty']+=$row[csf('issue_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['trans_in_qnty']+=$row[csf('trans_in_qnty')];

			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_rcv_rtn_qnty']+=$row[csf('issue_rcv_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['trns_out_qnty']+=$row[csf('trns_out_qnty')];

			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_rtn_qnty']+=$row[csf('today_issue_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_trans_in_qnty']+=$row[csf('today_trans_in_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_qnty']+=$row[csf('today_issue_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_rcv_rtn_qnty']+=$row[csf('today_rcv_rtn_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_trns_out_qnty']+=$row[csf('today_trns_out_qnty')];
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['prod_ids'].=$row[csf('prod_id')].',';
			$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['cons_uom']=$row[csf('cons_uom')];

			if($row[csf('store_id')]*1!="" || $row[csf('store_id')]*1>0)
				$store_ids_arr[$row[csf('store_id')]] = $row[csf('store_id')];

			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["receive_qnty"] += $row[csf('receive_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_rtn_qnty"] += $row[csf('issue_rtn_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trans_in_qnty"] += $row[csf('trans_in_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_rcv_rtn_qnty"] += $row[csf('issue_rcv_rtn_qnty')];
		}
		/*echo "<pre>";
		print_r($style_wise_arr);
		die;*/
		$colorIds = implode(",", $color_id_arr);
		$prodIds = implode(",", $prod_id_arr);
		$poIds = implode(",", $po_id_arr);

		$prodIds 	= ($prodIds 	!= "") ? "and c.prod_id in ( $prodIds )" : "";
		$poIds 		= ($poIds 		!= "") ? "and c.po_breakdown_id in($poIds)" : "";

		// ======================================= FOR ISSUE QNTY ==============================================
		$sql_issue="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id,
		(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty
		from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d,inv_finish_fabric_issue_dtls f
		where  a.company_name=$cbo_company_id and a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and c.id=d.trans_id   and f.id=d.dtls_id and   d.entry_form in (7,14,15,18,37,46,52,66,68,71,126,134) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and f.status_active=1 and f.is_deleted=0 $receive_date $buyer_id_cond $search_cond $year_cond $shiping_status_cond
		order by a.job_no, d.color_id,c.transaction_date";

		$style_wise_arr2=array();
		$sql_issue_res=sql_select($sql_issue);
		foreach ($sql_issue_res as $row)
		{
			//$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			//$fab_desc_tmp=$fab_desc_tmp1[0];

			$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
			$style_wise_arr2[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_qnty']+=$row[csf('issue_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_qnty"] += $row[csf('issue_qnty')];
		}

		// =================================== ISSUE TRANS QNTY ===============================
		$sql_trans="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id,
		(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty
		from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
		where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and a.company_name=$cbo_company_id and d.entry_form in (14,15,134,306) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $receive_date $buyer_id_cond $search_cond $year_cond $shiping_status_cond
		order by a.job_no, d.color_id,c.transaction_date";
		$sql_trans_res = sql_select($sql_trans);
		$trns_out_qnty_arr = array();
		foreach ($sql_trans_res as $row)
		{
			//$fab_desc_tmp1 = explode(",",$product_arr[$row[csf('prod_id')]]);
			//$fab_desc_tmp  = $fab_desc_tmp1[0];
			$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
			$trns_out_qnty_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]+=$row[csf('trns_out_qnty')];
			$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trns_out_qnty"] += $row[csf('trns_out_qnty')];
		}

		$booking_qnty=array();
		$job_no_arr = array_filter($job_no_arr);
		if(!empty($job_no_arr))
		{
			$job_no_arr = array_filter($job_no_arr);
			if($db_type==2 && count($job_no_arr)>999)
			{
				$job_no_arr_chunk=array_chunk($job_no_arr,999) ;
				foreach($job_no_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$jobCond.="  b.job_no in($chunk_arr_value) or ";
				}

				$all_job_no_cond.=" and (".chop($jobCond,'or ').")";
			}
			else
			{
				$all_job_no_cond=" and b.job_no in(".implode(",",$job_no_arr).")";
			}

			$sql_booking=sql_select("select b.po_break_down_id as po_id,b.fabric_color_id,b.job_no,b.construction,b.fabric_color_id, b.fin_fab_qnty,b.is_short,c.construction short_construction
				from wo_booking_mst a, wo_booking_dtls b
				left join wo_pre_cost_fabric_cost_dtls c on b.pre_cost_fabric_cost_dtls_id=c.id and c.status_active=1 and c.is_deleted=0
				where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $all_job_no_cond ");
			//$all_po_id_cond

			foreach( $sql_booking as $row)
			{
				$construction = ($row[csf('is_short')]==1)?$row[csf('short_construction')]:$row[csf('construction')];
				$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$construction]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
				$job_order_no_arr[$row[csf('job_no')]][] = $row[csf('po_id')];
			}
			unset($sql_booking);
		}

		if($colorIds!=""){
			$color_arr=return_library_array( "select id,color_name from lib_color where id in($colorIds)", "id", "color_name"  );
		}

		$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$store_width    = 120;
		$table_width 	= 2260+count($store_ids_arr)*$store_width;

		//check job wise all po shipment status..............
		$sql_check_shipmentStatus=sql_select("select b.id,b.po_number,b.shiping_status
						from  pro_ex_factory_mst a, wo_po_break_down b
						where b.id=a.po_break_down_id and a.status_active=1 and a.is_deleted=0");
		foreach ($sql_check_shipmentStatus as $row) {
			$shipingStatus_arr[$row[csf('id')]]['shiping_status']=$row[csf('shiping_status')];
		}
		?>
		<style type="text/css">
			.nsbreak{word-break: break-all;}
		</style>

		<fieldset style="width:2280px;">
			<table cellpadding="0" cellspacing="0" width="2060">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="<? echo $table_width; ?>px" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="100" rowspan="2">Buyer</th>
						<th width="60" rowspan="2">Job</th>
						<th width="50" rowspan="2">Year</th>
						<th width="110" rowspan="2">Style</th>
						<th width="100" rowspan="2">Internal Ref.</th>

						<th width="60" rowspan="2">Order Status</th>
						<th width="100" rowspan="2">Shipment Status</th>
						<th width="110" rowspan="2">Fin. Fab. Color</th>
						<th width="120" rowspan="2">Fabric Type</th>
						<th width="100" rowspan="2">UOM</th>

						<th width="80" rowspan="2">Req. Qty</th>
						<th width="240" colspan="3">Today Recv.</th>
						<th width="240" title="Rec.+Issue Ret.+Trans. in" colspan="3">Total Received</th>

						<th width="80" title="Req.-Totat Rec." rowspan="2">Received Balance</th>
						<th width="240" colspan="3">Today Issue</th>
						<th width="240" title="Issue+Rec. Ret.+Trans. out" colspan="3">Total Issued</th>
						<th width="100" title="Total Rec.- Total Issue" rowspan="2">Stock</th>
						<?
						foreach ($store_ids_arr as $store_id) {
							?>
							<th width="<? echo $store_width; ?>" title="Store ID = <? echo $store_id; ?>" rowspan="2"><? echo $store_arr[$store_id]; ?></th>
							<?
						}
						?>
					</tr>
					<tr>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $table_width+20; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table nsbreak" id="table_body" >
					<?
					$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$total_today_issue=0;$total_today_recv=0;
					$fin_color_array=array(); $fin_color_data_arr=array();
					foreach ($style_wise_arr  as $job_key=>$job_val)
					{
						foreach ($job_val  as $color_key=>$color_val)
						{
							foreach ($color_val  as $desc_key=>$val)
							{
								$prod_ids=rtrim($val['prod_ids'],',');
								$prod_ids=implode(",",array_filter(array_unique(explode(",",$prod_ids))));
								$color_id=$row[csf("color_id")];
								$fab_desc_type=$desc_key;
								$po_nos=rtrim($val['po_no'],',');
								$po_nos=implode(",",array_filter(array_unique(explode(",",$po_nos))));
								$grouping=implode(",",array_filter(array_unique(explode(",",chop($val['grouping'],",")))));
								$poids=rtrim($val['po_id'],',');

								$po_ids=array_filter(array_unique(explode(",",$poids)));
								$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
								/*foreach($po_ids as $po_id)
								{
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$desc_key]['fin_fab_qnty'];
								}*/
								$order_no_arr=array_filter(array_unique($job_order_no_arr[$job_key]));
								$order_nos="";
								foreach($order_no_arr as $po_id)
								{
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$desc_key]['fin_fab_qnty'];
								}
								$order_nos = implode(",", $order_no_arr); //N.B. this $order_nos only for showing required quantity

								$po_ids=implode(",",$po_ids);
								$today_recv=$val[("today_receive_qnty")];
								$today_rtn_qnty=$val[("today_issue_rtn_qnty")];
								$today_trans_in_qnty=$val[("today_trans_in_qnty")];


								$today_issue=$val[("today_issue_qnty")];
								$today_issue_rcv_rtn_qnty=$val[("today_rcv_rtn_qnty")];
								$today_issue_trns_out_qnty=$val[("today_trns_out_qnty")];

								$rec_qty = $val[("receive_qnty")];
								$rec_ret_qty = $val[("issue_rtn_qnty")];
								$rec_trns_qty = $val[("trans_in_qnty")];

								$rec_qty_cal=($rec_qty+$rec_ret_qty+$rec_trns_qty);

								$iss_qty = $style_wise_arr2[$job_key][$color_key][$desc_key]['issue_qnty'];
								$iss_ret_qty=$val[("issue_rcv_rtn_qnty")];

								$iss_trns_qty = $trns_out_qnty_arr[$job_key][$color_key][$desc_key];
								$iss_qty_cal=($iss_qty+$iss_ret_qty+$iss_trns_qty);


								$popup_ref_data = $val[("buyer_name")]."_".$val[("job_no_pre")]."_".$val[("year")]."_".$val[("style_ref_no")]."_".$grouping."_".$desc_key;
								$stock_check=$rec_qty_cal-$iss_qty_cal;
								if($cbo_value_range_by==2 &&  number_format($stock_check,2,'.','')>0.00)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
										<td width="100"><p><? echo $grouping; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>">
											<?
											$po_ids_exp=explode(",", $po_ids);
											$poId_count=0; $partial_shipSts_count=0;
											foreach ($po_ids_exp as $row) {
												if($po_id_shipingStatus_arr[$row]==3)
												{
													$partial_shipSts_count++;
												}
												$poId_count++;
											}
											if($partial_shipSts_count==$poId_count){
												?>
												<a style="color:red;" href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											else
											{
												?>
												<a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											?>
										</td>
										<td width="100" title="<? echo $po_ids;?>"><p>
											<? 
												$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
												foreach ($po_ids_exp as $row) {
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$full_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==2){
														$partial_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==1){
														$panding_shipSts_countx++;
													}
													$poId_countx++;
												}
												if($full_shipSts_countx==$poId_countx){
													$ShipingStatus="Full Delivery/Closed";
												}
												else if ($partial_shipSts_countx==$poId_countx) {
													$ShipingStatus="Partial Delivery";
												}
												else if ($panding_shipSts_countx==$poId_countx) {
													$ShipingStatus="Full Pending";
												}
												else
												{
													$ShipingStatus="Partial Delivery";
												}
												echo $ShipingStatus;
											?></p></td>
										<td width="110" title="<? echo $color_key;?>"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="120" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?> </p></td>
										<td width="100" title="" align="center"><p><? echo $unit_of_measurement[$val[("cons_uom")]]; ?> </p></td>
										<td width="80" align="right" title="<? echo $job_key."==".$color_key."==".$desc_key."==".$po_ids;?>"><p>
											<a href='#report_details' onClick="openmypage_req_qnty('<? echo $order_nos; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo $job_key; ?>','<? echo $order_nos; ?>','<? echo $desc_key; ?>','<? echo 1; ?>','req_qnty_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</a>
											</p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','today_total_rec_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_recv,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_rtn_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_trans_in_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_trans_in_qnty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','total_receive_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_ret_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_trns_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_trns_qty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty_cal; echo number_format($rec_bal,2,'.','');?></p></td>
										<td width="80" align="right">
											<p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a>
											</p>
										</td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_rcv_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_trns_out_qnty,2,'.',''); ?></a></p>
										</td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_trns_qty,2,'.',''); ?></a></p></td>

										<td width="100" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_rtn_qnty."##".$val[("trans_in_qnty")]."Issue: ".$val[("issue_qnty")]."##".$issue_rcv_rtn_qnty."##".$val[("trns_out_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','knit_stock_popup','<? echo $popup_ref_data; ?>');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a></p>
										</td>

										<?
										$store_receive=0;
										foreach ($store_ids_arr as $store_id) {
											$store_receive 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["receive_qnty"],2,".","");
											$store_issue_rtn 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rtn_qnty"],2,".","");
											$store_trans_in 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trans_in_qnty"],2,".","");

											$store_rcv_rtn 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rcv_rtn_qnty"],2,".","");
											$store_trns_out 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_out_qnty"],2,".","");
											$store_issue_qnty    = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_qnty"],2,".","");

											$store_balance = number_format(($store_receive+$store_issue_rtn+$store_trans_in) - ($store_rcv_rtn+$store_trns_out+$store_issue_qnty),2,".","");
											$store_balance_title = "Receive=$store_receive, Transfer In=$store_trans_in, Issue Return=$store_issue_rtn \nIssue=$store_issue_qnty, Transfer Out=$store_trns_out, Receive Return=$store_rcv_rtn";
											?>
											<td width="<? echo $store_width; ?>" title="<? echo $store_balance_title; ?>" align="right"><? echo $store_balance; ?></td>
											<?
											$store_wise_total_stock[$store_id] += $store_balance;
										}
										?>
									</tr>
									<?
									$i++;

									$total_order_qnty+=$val[("po_quantity")];
									$total_req_qty+=$book_qty;

									$total_rec_qty+=$rec_qty;
									$total_rec_ret_qty+=$rec_ret_qty;
									$total_rec_trns_qty+=$rec_trns_qty;

									$total_rec_bal+=$rec_bal;

									$total_issue_qty+=$iss_qty;
									$total_issue_ret_qty+=$iss_ret_qty;
									$total_issue_trns_qty+=$iss_trns_qty;

									$total_stock+=$stock;

									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;

									$total_today_issue+=$today_issue;
									$total_today_issue_rcv_rtn_qnty+=$today_issue_rcv_rtn_qnty;
									$total_today_issue_trns_out_qnty+=$today_issue_trns_out_qnty;

									$total_today_recv+=$today_recv;
									$total_today_rtn_qnty+=$today_rtn_qnty;
									$total_today_trans_in_qnty+=$today_trans_in_qnty;
								}
								else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
										<td width="100"><p><? echo $grouping; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>">
											<?
											$po_ids_exp=explode(",", $po_ids);
											$poId_count=0; $partial_shipSts_count=0;
											foreach ($po_ids_exp as $row) {
												if($po_id_shipingStatus_arr[$row]==3)
												{
													$partial_shipSts_count++;
												}
												$poId_count++;
											}
											if($partial_shipSts_count==$poId_count){
												?>
												<a style="color:red;" href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											else
											{
												?>
												<a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
												<?
											}
											?>
										</td>
										<td width="100" title="<? echo $po_ids;?>"><p>
											<? 
												$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
												foreach ($po_ids_exp as $row) {
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$full_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==2){
														$partial_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==1){
														$panding_shipSts_countx++;
													}
													$poId_countx++;
												}
												if($full_shipSts_countx==$poId_countx){
													$ShipingStatus="Full Delivery/Closed";
												}
												else if ($partial_shipSts_countx==$poId_countx) {
													$ShipingStatus="Partial Delivery";
												}
												else if ($panding_shipSts_countx==$poId_countx) {
													$ShipingStatus="Full Pending";
												}
												else
												{
													$ShipingStatus="Partial Delivery";
												}
												echo $ShipingStatus;
											?></p></td>
										<td width="110" title="<? echo $color_key;?>"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="120" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?> </p></td>
										<td width="100" title="" align="center"><p><? echo $unit_of_measurement[$val[("cons_uom")]]; ?> </p></td>
										<td width="80" align="right" title="<? echo $job_key."==".$color_key."==".$desc_key."==".$po_ids;?>"><p>
												<a href='#report_details' onClick="openmypage_req_qnty('<? echo $order_nos; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo $job_key; ?>','<? echo $order_nos; ?>','<? echo $desc_key; ?>','<? echo 1; ?>','req_qnty_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</a>
										</p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','today_total_rec_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_recv,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_rtn_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_trans_in_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_trans_in_qnty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','total_receive_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_ret_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_trns_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_trns_qty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty_cal; echo number_format($rec_bal,2,'.','');?></p></td>
										<td width="80" align="right">
											<p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a>
											</p>
										</td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_rcv_rtn_qnty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_trns_out_qnty,2,'.',''); ?></a></p>
										</td>

										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_ret_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_trns_qty,2,'.',''); ?></a></p></td>

										<td width="100" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_rtn_qnty."##".$val[("trans_in_qnty")]."Issue: ".$val[("issue_qnty")]."##".$issue_rcv_rtn_qnty."##".$val[("trns_out_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','knit_stock_popup','<? echo $popup_ref_data; ?>');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a></p>
										</td>

										<?
										$store_receive=0;
										foreach ($store_ids_arr as $store_id) {
											$store_receive 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["receive_qnty"],2,".","");
											$store_issue_rtn 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rtn_qnty"],2,".","");
											$store_trans_in 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trans_in_qnty"],2,".","");

											$store_rcv_rtn 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rcv_rtn_qnty"],2,".","");
											$store_trns_out 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_out_qnty"],2,".","");
											$store_issue_qnty    = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_qnty"],2,".","");

											$store_balance = number_format(($store_receive+$store_issue_rtn+$store_trans_in) - ($store_rcv_rtn+$store_trns_out+$store_issue_qnty),2,".","");
											$store_balance_title = "Receive=$store_receive, Transfer In=$store_trans_in, Issue Return=$store_issue_rtn \nIssue=$store_issue_qnty, Transfer Out=$store_trns_out, Receive Return=$store_rcv_rtn";
											?>
											<td width="<? echo $store_width; ?>" title="<? echo $store_balance_title; ?>" align="right"><? echo $store_balance; ?></td>
											<?
											$store_wise_total_stock[$store_id] += $store_balance;
										}
										?>
									</tr>
									<?
									$i++;

									$total_order_qnty+=$val[("po_quantity")];
									$total_req_qty+=$book_qty;

									$total_rec_qty+=$rec_qty;
									$total_rec_ret_qty+=$rec_ret_qty;
									$total_rec_trns_qty+=$rec_trns_qty;

									$total_rec_bal+=$rec_bal;

									$total_issue_qty+=$iss_qty;
									$total_issue_ret_qty+=$iss_ret_qty;
									$total_issue_trns_qty+=$iss_trns_qty;

									$total_stock+=$stock;

									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;

									$total_today_issue+=$today_issue;
									$total_today_issue_rcv_rtn_qnty+=$today_issue_rcv_rtn_qnty;
									$total_today_issue_trns_out_qnty+=$today_issue_trns_out_qnty;

									$total_today_recv+=$today_recv;
									$total_today_rtn_qnty+=$today_rtn_qnty;
									$total_today_trans_in_qnty+=$today_trans_in_qnty;
								}
							}
						}
					}
					?>
				</table>
			</div>
			<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<tfoot>
					<th width="30"></th>
					<th width="100"></th>
					<th width="60"></th>
					<th width="50"></th>
					<th width="110">&nbsp;</th>
					<th width="100">&nbsp;</th>

					<th width="60">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="120"></th>
					<th width="100">Total</th>
					<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?>10</th>
					<th width="80" align="right" id="value_total_today_rtn_qnty"><? echo number_format($total_today_rtn_qnty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_today_trans_in_qnty"><? echo number_format($total_today_trans_in_qnty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_rec_ret_qty"><? echo number_format($total_rec_ret_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_rec_trns_qty"><? echo number_format($total_rec_trns_qty,2,'.',''); ?></th>

					<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>

					<th width="80"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
					<th width="80"  id="value_total_today_issue_rcv_rtn_qty"><? echo number_format($total_today_issue_rcv_rtn_qnty,2,'.',''); ?></th>
					<th width="80"  id="value_recv_total_today_issue_trns_out_qnty"><? echo number_format($total_today_issue_trns_out_qnty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_issue_ret_qty"><? echo number_format($total_issue_ret_qty,2,'.',''); ?></th>
					<th width="80" align="right" id="value_total_issue_trns_qty"><? echo number_format($total_issue_trns_qty,2,'.',''); ?></th>

					<th width="100" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
					<?
					foreach ($store_ids_arr as $store_id) {
						?>
						<th width="<? echo $store_width; ?>" id="value_total_store_qty"><? echo number_format($store_wise_total_stock[$store_id],2,".",""); ?></th>
						<?
					}
					?>
				</tfoot>
			</table>
		</fieldset>
		<?
	}
	//Knit end
	else if($cbo_report_type==2) // Woven Finish Start
	{
		$product_array=array();
		$sql_product="select id, color, detarmination_id,dia_width,weight,product_name_details from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0";
		$sql_product_result=sql_select($sql_product);
		foreach( $sql_product_result as $row )
		{
			$product_array[$row[csf('product_name_details')]]['color']=$row[csf('color')];
			$product_array[$row[csf('product_name_details')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('product_name_details')]]['weight']=$row[csf('weight')];
			$product_array[$row[csf('product_name_details')]]['yarn_did']=$row[csf('detarmination_id')];
		}

		$issue_qnty=array();
		$sql_issue=sql_select(" select b.po_breakdown_id,b.color_id, sum(b.quantity) as issue_qnty  from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.issue_purpose in(4,9) and b.entry_form=19 group by b.po_breakdown_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		} //var_dump($issue_qnty);

		$booking_qnty=array();
		//$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		foreach( $sql_booking as $row)
		{
			$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
		}
		unset($sql_booking);

		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		?>
		<fieldset style="width:1930px;">
			<table cellpadding="0" cellspacing="0" width="1690">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Buyer</th>
						<th width="60">Job</th>
						<th width="50">Year</th>
						<th width="110">Style</th>

						<th width="60">Order Status</th>
						<th width="100">Shipment Status</th>
						<th width="110">Fin. Fab. Color</th>
						<th width="220">Fab. Desc.</th>

						<th width="80">Req. Qty</th>
						<th width="80">Today Recv.</th>
						<th width="80" title="Rec.+Issue Ret.+Trans. in">Total Received</th>

						<th width="80" title="Req.-Totat Rec.">Received Balance</th>
						<th width="80" title="">Avg. Rate</th>
						<th width="80" title="">Received Value</th>

						<th width="80">Today Issue</th>
						<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>
						<th width="80" title="">Avg. Rate</th>
						<th width="80" title="">Issue Value</th>

						<th width="80" title="Total Rec.- Total Issue">Stock</th>
						<th width="80" title="">Avg. Rate</th>
						<th width="" title="">Stock Value</th>
					</tr>
				</thead>
			</table>
			<div style="width:1920px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					if($db_type==0) $prod_cond="group_concat(e.prod_id) as prod_id";
					else if($db_type==2) $prod_cond="listagg(cast(e.prod_id as varchar2(4000)),',') within group (order by e.prod_id) as prod_id";

					$sql_query="SELECT a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id,c.balance_qnty, $select_fld,e.product_name_details as prod_desc,b.shiping_status, 

					(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) as finish_fabric_transfer_in,					
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end) as issue_rtn,

					(case when d.entry_form in (17) and c.transaction_type=1 then c.cons_amount else 0 end) as receive_amount, 
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then c.cons_amount else 0 end) as transfer_in_amount,
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then c.cons_amount else 0 end) as issue_rtn_amount,

					(case when d.entry_form in (17) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,					
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in,
					(case when d.entry_form in (209)  and c.transaction_type=4 and d.trans_type=4  and $today_receive_date then d.quantity else 0 end) as today_issue_rtn,

					(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty,
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as finish_fabric_transfer_out,
					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end) as recv_rtn,

					(case when d.entry_form in (19) and c.transaction_type=2 then c.cons_amount else 0 end) as issue_amount, 
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then c.cons_amount else 0 end) as transfer_out_amount, 
					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then c.cons_amount else 0 end) as recv_rtn_amount, 

  					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 and $today_receive_date then d.quantity else 0 end) as today_recv_rtn,
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trans_out,
					(case when d.entry_form in (19) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty

					from  wo_po_details_master a, wo_po_break_down b,inv_transaction c,  order_wise_pro_details d,product_details_master e 
					where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and  d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id  and d.entry_form in (17,19,258,209,202) and d.trans_id!=0 and d.entry_form in (17,19,258,209,202)  and c.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$cbo_company_id $receive_date $buyer_id_cond $search_cond $year_cond
					order by a.job_no,d.color_id,c.transaction_date";
					// echo $sql_query;
					$style_wise_arr=array();
					$nameArray=sql_select($sql_query);
					foreach ($nameArray as $row)
					{
						$po_id_shipingStatus_arr[$row[csf('po_id')]]=$row[csf('shiping_status')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no']=$row[csf('job_no')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['year']=$row[csf('year')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['buyer_name']=$row[csf('buyer_name')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['style_ref_no']=$row[csf('style_ref_no')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_id'].=$row[csf('po_id')].',';
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_no'].=$row[csf('po_no')].',';
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_qnty']+=$row[csf('receive_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_qnty']+=$row[csf('issue_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn']+=$row[csf('issue_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn']+=$row[csf('recv_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['balance_qnty']+=$row[csf('balance_qnty')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_in']+=$row[csf('today_trans_in')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_out']+=$row[csf('today_trans_out')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_rtn']+=$row[csf('today_issue_rtn')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_recv_rtn']+=$row[csf('today_recv_rtn')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];
						
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_amount']+=$row[csf('receive_amount')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['transfer_in_amount']+=$row[csf('transfer_in_amount')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn_amount']+=$row[csf('issue_rtn_amount')];

						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_amount']+=$row[csf('issue_amount')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['transfer_out_amount']+=$row[csf('transfer_out_amount')];
						$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn_amount']+=$row[csf('recv_rtn_amount')];
					}
					//print_r($po_id_shipingStatus_arr);

					$i=1;$total_rec_amount=$total_iss_amount=$total_StockValue=0;
					foreach ($style_wise_arr  as $job_key=>$job_val)
					{
						foreach ($job_val  as $color_key=>$color_val)
						{
							foreach ($color_val  as $desc_key=>$val)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$dzn_qnty=0;
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;

								$color_id=$row[csf("color_id")];
								//$fab_desc_type=$product_arr[$desc_key];
								$fab_desc_type=$desc_key;
								$po_nos=rtrim($val['po_no'],',');
								$po_nos=implode(",",array_unique(explode(",",$po_nos)));
								$poids=rtrim($val['po_id'],',');

								$po_ids=array_unique(explode(",",$poids));
								$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
								foreach($po_ids as $po_id)
								{
									//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."<br>";
									$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['fin_fab_qnty'];
										//echo $job_key.'ii'.$po_id;
									$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
									$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
								}

									//echo $job_key.'ii';
								$po_ids=implode(",",$po_ids);
								$today_recv=$val[("today_receive_qnty")]+$val[("today_trans_in")]+$val[("today_issue_rtn")];
								$today_issue=$val[("today_issue_qnty")]+$val[("today_trans_out")]+$val[("today_recv_rtn")];
								$rec_qty=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);
								$rec_amount=($val[("receive_amount")]+$val[("transfer_in_amount")]+$val[("issue_rtn_amount")]);
								$rec_avg_rate=$rec_amount/$rec_qty;
								// echo $rec_qty.'<br>';
								//$rec_qty_cal=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);

								$iss_qty=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
								$iss_amount=($val[("issue_amount")]+$val[("transfer_out_amount")]+$val[("recv_rtn_amount")]);
								$issue_avg_rate=$iss_amount/$iss_qty;
								$StockValue=$rec_amount-$iss_amount;
								$stock=($rec_qty-$iss_qty);
								$stock_avg_rate=$StockValue/$stock;
								//$iss_qty_cal=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
									<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
									<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
									<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>

									<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
									<td width="100" title="<? echo $po_ids;?>"><p>
										<? 
											$po_ids_exp=explode(",", $po_ids);
											$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
											foreach ($po_ids_exp as $row) 
											{
												if($po_id_shipingStatus_arr[$row]==3)
												{
													$full_shipSts_countx++;
												}
												else if($po_id_shipingStatus_arr[$row]==2){
													$partial_shipSts_countx++;
												}
												else if($po_id_shipingStatus_arr[$row]==1){
													$panding_shipSts_countx++;
												}
												$poId_countx++;
											}
											if($full_shipSts_countx==$poId_countx){
												$ShipingStatus="Full Delivery/Closed";
											}
											else if ($partial_shipSts_countx==$poId_countx) {
												$ShipingStatus="Partial Delivery";
											}
											else if ($panding_shipSts_countx==$poId_countx) {
												$ShipingStatus="Full Pending";
											}
											else
											{
												$ShipingStatus="Partial Delivery";
											}
											echo $ShipingStatus;
										?></p></td>
									<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
									<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>

									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>


									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>

									<td width="80" align="right"><p><?
									$rec_bal=$book_qty-$rec_qty;
									//$rec_bal=$val[("balance_qnty")];
									echo number_format($rec_bal,2,'.','');
									?></p></td>
									<td width="80" align="right"><p><? echo number_format($rec_avg_rate,4,'.',''); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($rec_amount,2,'.',''); ?></p></td>

									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_issue_popup');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_avg_rate,4,'.',''); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($iss_amount,2,'.',''); ?></p></td>

									<td width="80" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','woven_knit_stock_popup');"><?
									//$stock=$rec_qty_cal-$iss_qty_cal; old
									 //new $rec_qty
									echo number_format($stock,2,'.','');
									?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($stock_avg_rate,4,'.',''); ?></p></td>
									<td width="" align="right"><p><? echo number_format($StockValue,2,'.',''); ?></p></td>
								</tr>
								<?
								$i++;

								$total_req_qty+=$book_qty;
								$total_rec_qty+=$rec_qty;
								$total_rec_bal+=$rec_bal;
								$total_issue_qty+=$iss_qty;
								$total_stock+=$stock;
								$total_possible_cut_pcs+=$possible_cut_pcs;
								$total_actual_cut_qty+=$actual_qty;
								$total_rec_return_qnty+=$receive_ret_qnty;
								$total_issue_ret_qnty+=$issue_ret_qnty;
								$total_today_issue+=$today_issue;
								$total_today_recv+=$today_recv;

								$total_rec_amount+=$rec_amount;
								$total_iss_amount+=$iss_amount;
								$total_StockValue+=$StockValue;
							}
						}
					}
					?>
				</table>
				<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="50"></th>
						<th width="110">&nbsp;</th>

						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="220">Total</th>
						<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
						<th width="80" align="right" id=""></th>
						<th width="80" align="right" id="value_total_rec_amount"><? echo number_format($total_rec_amount,2,'.',''); ?></th>

						<th width="80"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th width="80" align="right" id=""></th>
						<th width="80" align="right" id="value_total_iss_amount"><? echo number_format($total_iss_amount,2,'.',''); ?></th>

						<th width="80" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
						<th width="80" align="right" id=""></th>
						<th width="" align="right" id="value_total_StockValue"><? echo number_format($total_StockValue,2,'.',''); ?></th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}
if($action=="report_generate_uom")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	//$cbo_presentation=str_replace("'","",$cbo_presentation);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);
	$cbo_value_range_by=str_replace("'","",$cbo_value_range_by);
	//$cbo_uom=str_replace("'","",$cbo_uom);
	//if($cbo_uom) $uom_cond = " and c.cons_uom in ($cbo_uom) "; else $uom_cond = "";
	//echo $cbo_report_type;die;

	if($cbo_store_name>0){$storeCond="and c.store_id=$cbo_store_name";}else{$storeCond="";}
	if($cbo_shipment_status>0){$shiping_status_cond=" and b.shiping_status in ($cbo_shipment_status)";}

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '%$txt_search_comm%'";
	}
	else
	{
		$search_cond.="";
	}

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	$product_array=array(); $allProductIdsArr=array();

	$cbo_uom=str_replace("'","",$cbo_uom);
	if($cbo_uom) $uom_cond_prod = " and unit_of_measure in ($cbo_uom) "; else $uom_cond_prod = "";
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$product_sql = sql_select("select id, product_name_details,unit_of_measure from product_details_master where status_active = 1 and is_deleted = 0 $uom_cond_prod");
	foreach ($product_sql as $prow)
	{
		$product_arr[$prow[csf("id")]] = $prow[csf("product_name_details")];
		$uomFromProductArr[$prow[csf("id")]] = $prow[csf("unit_of_measure")];
		$productIds .= $prow[csf("id")].",";
	}


	$allProductIdsArr = array_filter(array_unique(explode(",",chop($productIds,","))));
	if(count($allProductIdsArr) > 999 && $db_type == 2 )
	{
		$allProductIdsChunkArr=array_chunk($allProductIdsArr,999) ;
		foreach($allProductIdsChunkArr as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$pid_cond.="  d.prod_id in($chunk_arr_value) or ";
		}

		$productId_cond.=" and (".chop($pid_cond,'or ').")";
	}else{

		$productId_cond.=" and d.prod_id in (".implode(",",$allProductIdsArr).")";
	}
	if($cbo_uom) $productId_cond=$productId_cond;else $productId_cond='';
	if($db_type==0)
	{
		$select_fld= "year(a.insert_date) as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}

	ob_start();
	if($cbo_report_type==1) // Knit Finish Start
	{
		//echo $productId_cond;die;

		unset($sql_product_result);

		$transfer_arr=array(); $all_data_arr=array();
		$iss_return_qnty=array();
		$sql_issue_ret=sql_select("select a.job_no,d.po_breakdown_id as po_id, d.color_id,d.prod_id, (d.quantity) as issue_ret_qnty  from wo_po_details_master a, wo_po_break_down b,order_wise_pro_details d where a.job_no=b.job_no_mst and b.id=d.po_breakdown_id and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=52  and
			a.company_name=$cbo_company_id  $buyer_id_cond $search_cond $year_cond");
		foreach( $sql_issue_ret as $row )
		{
			$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			$fab_desc_tmp=$fab_desc_tmp1[0];

			$iss_return_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_ret_qnty']+=$row[csf('issue_ret_qnty')];
		}
		unset($sql_issue_ret);

		$rec_return_qnty=array();

		$sql_rec_ret=sql_select("select a.job_no,d.po_breakdown_id as po_id,d.color_id, d.prod_id, (d.quantity) as rec_ret_qnty  from wo_po_details_master a, wo_po_break_down b,order_wise_pro_details d where a.job_no=b.job_no_mst and b.id=d.po_breakdown_id and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=46  and
			a.company_name=$cbo_company_id  $buyer_id_cond $search_cond $year_cond");
		foreach( $sql_rec_ret as $row )
		{
			$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			$fab_desc_tmp=$fab_desc_tmp1[0];
			$rec_return_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('color_id')]][$fab_desc_tmp]['rec_ret_qnty']+=$row[csf('rec_ret_qnty')];
		}
		unset($sql_rec_ret);
		//print_r($rec_return_qnty);
		$booking_qnty=array(); $booking_qnty_chk = array();
		/*$sql_booking=sql_select("select b.po_break_down_id as po_id,b.fabric_color_id,b.job_no,b.construction, (b.fin_fab_qnty ) as fin_fab_qnty,c.uom,b.id as dtls_id from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and a.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0");*/

		$sql_booking=sql_select("select b.po_break_down_id as po_id,b.fabric_color_id,b.job_no,b.construction, (b.fin_fab_qnty ) as fin_fab_qnty,c.uom,b.id as dtls_id from wo_booking_mst a, wo_booking_dtls b left join wo_pre_cost_fabric_cost_dtls c on b.pre_cost_fabric_cost_dtls_id=c.id and c.status_active=1 and c.is_deleted=0 where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0");

		foreach( $sql_booking as $row)
		{
			if($booking_qnty_chk[$row[csf('dtls_id')]] == "")
			{
				$booking_qnty_chk[$row[csf('dtls_id')]]=$row[csf('dtls_id')];
				$booking_qnty[$row[csf('uom')]][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			}
		}
		/*echo "<pre>";
		print_r($booking_qnty[27]["D n C-17-01245"]);
		die;*/
		unset($sql_booking);

		?>
		<fieldset style="width:1390px;">
			<table cellpadding="0" cellspacing="0" width="1390">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="15" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="15" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="15" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="1370" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="60">Job</th>
					<th width="50">Year</th>
					<th width="110">Style</th>

					<th width="60">Order Status</th>
					<th width="100">Shiping Status</th>
					<th width="110">Fin. Fab. Color</th>
					<th width="120">Fabric Type</th>
					<th width="50">UOM</th>
					<th width="80">Req. Qty</th>
					<th width="80">Today Recv.</th>
					<th width="80" title="Rec.+Issue Ret.+Trans. in">Total Received</th>

					<th width="80" title="Req.-Totat Rec.">Received Balance</th>
					<th width="80">Today Issue</th>
					<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>
					<th width="" title="Total Rec.- Total Issue">Stock</th>

				</thead>
			</table>
			<div style="width:1390px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1370" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					$sql_query="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id,$select_fld,d.color_id,d.prod_id,
					(case when d.entry_form in (7,37,66,68) then d.quantity else 0 end) as receive_qnty,
					(case when d.entry_form in (7,37,66,68) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
					(case when d.entry_form in(14,15) and d.trans_type=5 then d.quantity else 0 end) as rec_trns_qnty,
					(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty,
					(case when d.entry_form in (18,71) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty,
					(case when d.entry_form in(14,15) and d.trans_type=6 then d.quantity else 0 end) as issue_trns_qnty
					from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d, pro_batch_create_mst e
					where  a.company_name=$cbo_company_id and a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.pi_wo_batch_no=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted = 0 and e.status_active = 1 and e.is_deleted=0
					and d.entry_form in (7,37,66,68,14,15,18,71) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and c.id=d.trans_id and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0  $receive_date $buyer_id_cond $search_cond $year_cond $productId_cond
					order by a.job_no,d.color_id";
					$style_wise_arr=array();
					$nameArray=sql_select($sql_query);
					foreach ($nameArray as $row)
					{
						$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
						$fab_desc_tmp=$fab_desc_tmp1[0];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no']=$row[csf('job_no')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no_pre']=$row[csf('job_no_prefix_num')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['year']=$row[csf('year')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['buyer_name']=$row[csf('buyer_name')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['style_ref_no']=$row[csf('style_ref_no')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_id'].=$row[csf('po_id')].',';
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_no'].=$row[csf('po_no')].',';
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['receive_qnty']+=$row[csf('receive_qnty')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_qnty']+=$row[csf('issue_qnty')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_qnty']+=$row[csf('today_issue_qnty')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['prod_ids'].=$row[csf('prod_id')].',';
					}

					$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$total_today_issue=0;$total_today_recv=0;
					$fin_color_array=array(); $fin_color_data_arr=array();
					foreach ($style_wise_arr as $cons_uom => $cons_uom_data)
					{
						$sub_req_qty=0;$sub_today_recv=$sub_rec_qty=$sub_rec_bal=$sub_today_issue=$sub_issue_qty=$sub_stock=0;
						foreach ($cons_uom_data  as $job_key=>$job_val)
						{
							foreach ($job_val  as $color_key=>$color_val)
							{
								foreach ($color_val  as $desc_key=>$val)
								{

									$dzn_qnty=0;
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
									else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
									else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
									else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
									else $dzn_qnty=1;

									$prod_ids=rtrim($val['prod_ids'],',');
									$color_id=$row[csf("color_id")];
									$fab_desc_type=$desc_key;
									$po_nos=rtrim($val['po_no'],',');
									$po_nos=implode(",",array_filter(array_unique(explode(",",$po_nos))));
									$poids=rtrim($val['po_id'],',');

									$po_ids=array_filter(array_unique(explode(",",$poids)));
									$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
									foreach($po_ids as $po_id)
									{
										$book_qty+=$booking_qnty[$cons_uom][$job_key][$po_id][$color_key][$desc_key]['fin_fab_qnty'];
										$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
										$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
									}

									$po_ids=implode(",",$po_ids);
									$today_recv=$val[("today_receive_qnty")];
									$today_issue=$val[("today_issue_qnty")];
									$rec_qty=($val[("receive_qnty")]+$val[("rec_trns_qnty")]+$issue_ret_qnty);
									$rec_qty_cal=($val[("receive_qnty")]+$issue_ret_qnty+$val[("rec_trns_qnty")]);

									$iss_qty=($val[("issue_qnty")]+$val[("issue_trns_qnty")]+$receive_ret_qnty);
									$iss_qty_cal=($val[("issue_qnty")]+$receive_ret_qnty+$val[("issue_trns_qnty")]);
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
										<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
										<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>

										<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
										<td width="100" title="<? echo $po_ids;?>"><p>
											<? 
												$po_ids_exp=explode(",", $po_ids);
												$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
												foreach ($po_ids_exp as $row) {
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$full_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==2){
														$partial_shipSts_countx++;
													}
													else if($po_id_shipingStatus_arr[$row]==1){
														$panding_shipSts_countx++;
													}
													$poId_countx++;
												}
												if($full_shipSts_countx==$poId_countx){
													$ShipingStatus="Full Delivery/Closed";
												}
												else if ($partial_shipSts_countx==$poId_countx) {
													$ShipingStatus="Partial Delivery";
												}
												else if ($panding_shipSts_countx==$poId_countx) {
													$ShipingStatus="Full Pending";
												}
												else
												{
													$ShipingStatus="Partial Delivery";
												}
												echo $ShipingStatus;
											?></p></td>
										<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
										<td width="120" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?></p></td>
										<td width="50" title="uom"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
										<td width="80" align="right" title="<? echo "[$cons_uom][$job_key][$po_id][$color_key][$desc_key]";?>"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','today_receive_popup');"><? echo number_format($today_recv,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty; echo number_format($rec_bal,2,'.',''); ?></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 3; ?>','today_issue_popup');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','issue_popup');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>

										<td width="" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','knit_stock_popup');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a></p></td>

									</tr>
									<?
									$i++;
									$total_order_qnty+=$val[("po_quantity")];
									$total_req_qty+=$book_qty;
									$total_rec_qty+=$rec_qty;
									$total_rec_bal+=$rec_bal;
									$total_issue_qty+=$iss_qty;
									$total_stock+=$stock;
									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;
									$total_today_issue+=$today_issue;
									$total_today_recv+=$today_recv;

									$sub_req_qty+=$book_qty;
									$sub_today_recv+=$today_recv;
									$sub_rec_qty+=$rec_qty;
									$sub_rec_bal+=$rec_bal;
									$sub_today_issue+=$today_issue;
									$sub_issue_qty+=$iss_qty;
									$sub_stock+=$stock;

								}
							}
						}

						?>
						<tr style="font-weight:bold;background-color: #e0e0e0">
							<td colspan="9" align="right">UOM Total</td>
							<td align="right"><? echo number_format($sub_req_qty,2,'.','') ?></td>
							<td align="right"><? echo number_format($sub_today_recv,2,'.','');?></td>
							<td align="right"><? echo number_format($sub_rec_qty,2,'.','');?></td>
							<td align="right"><? echo number_format($sub_rec_bal,2,'.','');?></td>
							<td align="right"><? echo number_format($sub_today_issue,2,'.','');?></td>
							<td align="right"><? echo number_format($sub_issue_qty,2,'.','');?></td>
							<td align="right"><? echo number_format($sub_stock,2,'.','');?></td>
						</tr>
						<?
					}
					?>
				</table>
			</div>
			<table width="1370" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<tfoot>
					<th width="30"></th>
					<th width="100"></th>
					<th width="60"></th>
					<th width="50"></th>
					<th width="110">&nbsp;</th>

					<th width="60">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="120">Total</th>
					<th width="50">&nbsp;</th>
					<th width="80" align="right"><? echo number_format($total_req_qty,2,'.',''); ?></th>
					<th width="80" align="right"><? echo number_format($total_today_recv,2,'.',''); ?></th>
					<th width="80" align="right"><? echo number_format($total_rec_qty,2,'.',''); ?></th>

					<th width="80" align="right" ><? echo number_format($total_rec_bal,2,'.',''); ?></th>
					<th width="80" align="right"><? echo number_format($total_today_issue,2,'.',''); ?></th>
					<th width="80" align="right"><? echo number_format($total_issue_qty,2,'.',''); ?></th>

					<th width="" align="right"><? echo number_format($total_stock,2,'.',''); ?></th>

				</tfoot>
			</table>
		</fieldset>
		<?
	}//Knit end
	else if($cbo_report_type==2) // Woven Finish Start
	{
		$product_array=array();
		$sql_product="select id, color, detarmination_id,dia_width,weight,product_name_details from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0";
		$sql_product_result=sql_select($sql_product);
		foreach( $sql_product_result as $row )
		{
			$product_array[$row[csf('product_name_details')]]['color']=$row[csf('color')];
			$product_array[$row[csf('product_name_details')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('product_name_details')]]['weight']=$row[csf('weight')];
			$product_array[$row[csf('product_name_details')]]['yarn_did']=$row[csf('detarmination_id')];
		}
		$issue_qnty=array();
		$sql_issue=sql_select(" select b.po_breakdown_id,b.color_id, sum(b.quantity) as issue_qnty  from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.issue_purpose in(4,9) and b.entry_form=19 $storeCond group by b.po_breakdown_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		} //var_dump($issue_qnty);

		$booking_qnty=array(); $booking_qnty_chk= array();
		$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty ) as fin_fab_qnty, b.id as dtls_id, c.uom, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b , wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		/*$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");*/
		foreach( $sql_booking as $row)
		{
			if($booking_qnty_chk[$row[csf('dtls_id')]] == "")
			{
				$booking_qnty_chk[$row[csf('dtls_id')]]=$row[csf('dtls_id')];
				$booking_qnty[$row[csf('uom')]][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			}
		}

		/*echo "<pre>";
		print_r($booking_qnty);*/

		unset($sql_booking);

		$iss_return_qnty=array();
		$sql_issue_ret=sql_select("select a.job_no,d.po_breakdown_id as po_id, d.color_id,d.prod_id, (d.quantity) as issue_ret_qnty  from wo_po_details_master a, wo_po_break_down b,order_wise_pro_details d,inv_transaction c where a.job_no=b.job_no_mst and b.id=d.po_breakdown_id and d.trans_id=c.id and  d.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=209  and
			a.company_name=$cbo_company_id  $buyer_id_cond $search_cond $year_cond $storeCond $shiping_status_cond");
		foreach( $sql_issue_ret as $row )
		{
			$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			$fab_desc_tmp=$fab_desc_tmp1[0];

			$iss_return_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('prod_id')]]['issue_ret_qnty']+=$row[csf('issue_ret_qnty')];
		}
		unset($sql_issue_ret);
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$fabric_type_arr=return_library_array( "select id, type from lib_yarn_count_determina_mst where fab_nature_id=3 and entry_form=426", "id", "type"  );

		ob_start();
		?>
		<fieldset style="width:1840px;">
			<table cellpadding="0" cellspacing="0" width="1810">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="15" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="15" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="15" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="1810" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="60">Job</th>
					<th width="50">Year</th>
					<th width="110">Style</th>

					<th width="60">Order Status</th>
					<th width="100">Shiping Status</th>
					<th width="110">Fin. Fab. Color</th>
					<th width="100">Fab. Type</th>
					<th width="220">Fab. Desc.</th>
					<th width="50">UOM</th>

					<th width="80">Req. Qty</th>
					<th width="80">Today Recv.</th>
					<th width="80" title="Rec.+Issue Ret.+Trans. in">Total Received</th>
					<th width="80" title="">Received Amount</th>

					<th width="80" title="Req.-Totat Rec.">Received Balance</th>
					<th width="80">Today Issue</th>
					<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>
					<th width="80" title="">Issued Amount</th>
					<th width="80" title="Total Rec.- Total Issue">Stock</th>
					<th width="" title="">Stock Value</th>
				</thead>
			</table>
			<div style="width:1830px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1810" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					if($db_type==0) $prod_cond="group_concat(e.prod_id) as prod_id";
					else if($db_type==2) $prod_cond="listagg(cast(e.prod_id as varchar2(4000)),',') within group (order by e.prod_id) as prod_id";

					$sql_query="
						Select
							a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id, c.order_rate, $select_fld,e.product_name_details as prod_desc,e.detarmination_id , 
							(case when d.entry_form in (17,209) or (d.trans_type=5 and d.entry_form=258) then d.quantity else 0 end) as receive_qnty,
							(case when (d.entry_form in (17,209) or (d.trans_type=5 and d.entry_form=258)) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
							(case when (d.entry_form in (209) or (d.trans_type=4 and d.entry_form=209)) and $today_receive_date then d.quantity else 0 end) as today_issue_rtn_qnty,
							(case when d.entry_form in (19,202) or (d.trans_type=6 and d.entry_form=258) then d.quantity else 0 end) as issue_qnty,
							(case when (d.entry_form in (19,202) or (d.trans_type=6 and d.entry_form=258)) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty
						from
							wo_po_details_master a,
							wo_po_break_down b,
							inv_transaction c,
							order_wise_pro_details d,product_details_master e 
						where
							a.job_no=b.job_no_mst 
							and d.po_breakdown_id=b.id 
							and d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id 
							and d.trans_id!=0
							and d.entry_form in (17,19,202,209,258) 
							and c.item_category=3 
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
							and a.company_name = ".$cbo_company_id." $receive_date $buyer_id_cond $search_cond $year_cond $storeCond $shiping_status_cond order by a.job_no,d.color_id";

					//echo $sql_query; die;
					$style_wise_arr=array();
					$nameArray=sql_select($sql_query);
					foreach ($nameArray as $row)
					{
						if($uomFromProductArr[$row[csf('prod_id')]] == "") continue;
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no']=$row[csf('job_no')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['year']=$row[csf('year')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['buyer_name']=$row[csf('buyer_name')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['style_ref_no']=$row[csf('style_ref_no')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_id'].=$row[csf('po_id')].',';
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_no'].=$row[csf('po_no')].',';
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_qnty']+=$row[csf('receive_qnty')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_qnty']+=$row[csf('issue_qnty')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_rtn_qnty']+=$row[csf('today_issue_rtn_qnty')];
						
						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_amount']+=($row[csf('receive_qnty')]*$row[csf('order_rate')]);

						$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['detarmination_id']=$row[csf('detarmination_id')];

						
						//$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['stock_value']+=(($row[csf('receive_qnty')]*1 - $row[csf('issue_qnty')]*1)*$row[csf('order_rate')]);
						
						//echo $row[csf('receive_qnty')]."=".$row[csf('issue_qnty')]."=".(($row[csf('receive_qnty')]*1-$row[csf('issue_qnty')]*1)."=".$row[csf('order_rate')])."<br>";
					}
					//echo "<pre>";
					//print_r($style_wise_arr); die;

					$i=1;
					foreach ($style_wise_arr as $cons_uom => $cons_uom_data)
					{
						$sub_req_qty=0;
						$sub_today_recv = 0;
						$sub_rec_qty = 0;
						$sub_rec_bal = 0;
						$sub_today_issue = 0;
						$sub_issue_qty = 0;
						$sub_stock=0;
						foreach ($cons_uom_data  as $job_key=>$job_val)
						{
							foreach ($job_val  as $color_key=>$color_val)
							{
								foreach ($color_val  as $desc_key=>$val)
								{
									//if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$dzn_qnty=0;
									
									if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
									else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
									else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
									else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
									else $dzn_qnty=1;

									$color_id=$row[csf("color_id")];
									//$fab_desc_type=$product_arr[$desc_key];
									$fab_desc_type=$desc_key;
									$po_nos=rtrim($val['po_no'],',');
									$po_nos=implode(",",array_unique(explode(",",$po_nos)));
									$poids=rtrim($val['po_id'],',');

									$po_ids=array_unique(explode(",",$poids));
									$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
									foreach($po_ids as $po_id)
									{
										//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."=".$product_array[$desc_key]['dia_width']."=".$product_array[$desc_key]['fin_fab_qnty']."<br>";
										$book_qty+=$booking_qnty[$cons_uom][$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['fin_fab_qnty'];
										//echo $job_key.'ii'.$po_id;
										$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
										//echo $job_key."=".$po_id."=".$color_key."=".$desc_key."<br/>";
										//echo $issue_ret_qnty.",";
										$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
									}

									//echo $job_key.'ii';
									$po_ids=implode(",",$po_ids);
									$today_recv=$val[("today_receive_qnty")];
									$today_issue=$val[("today_issue_qnty")];
									$rec_qty=($val[("receive_qnty")]);
									$receive_amount=number_format($val[("receive_amount")],2,'.','');
									$rec_qty_cal=($val[("receive_qnty")]);
									$iss_qty=($val[("issue_qnty")]);
									$iss_qty_cal=($val[("issue_qnty")]);
									$stock=$rec_qty_cal-$iss_qty_cal;
									
									//rate calculating
									$rate = $receive_amount/$rec_qty;
									$issue_amount = number_format(($val[("issue_qnty")]*$rate),2,'.','');
									$stock_amount = number_format(($stock*$rate),2,'.','');

									if($cbo_value_range_by==2 &&  number_format($stock,2,'.','')>0.00)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
											<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
											<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
											<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
											<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
											<td width="100" title="<? echo $po_ids;?>"><p>
												<? 
													$po_ids_exp=explode(",", $po_ids);
													$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
													foreach ($po_ids_exp as $row) {
														if($po_id_shipingStatus_arr[$row]==3)
														{
															$full_shipSts_countx++;
														}
														else if($po_id_shipingStatus_arr[$row]==2){
															$partial_shipSts_countx++;
														}
														else if($po_id_shipingStatus_arr[$row]==1){
															$panding_shipSts_countx++;
														}
														$poId_countx++;
													}
													if($full_shipSts_countx==$poId_countx){
														$ShipingStatus="Full Delivery/Closed";
													}
													else if ($partial_shipSts_countx==$poId_countx) {
														$ShipingStatus="Partial Delivery";
													}
													else if ($panding_shipSts_countx==$poId_countx) {
														$ShipingStatus="Full Pending";
													}
													else
													{
														$ShipingStatus="Partial Delivery";
													}
													echo $ShipingStatus;
												?></p></td>
											<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
											<td width="100" align="center"><p><? echo $fabric_type_arr[$val[("detarmination_id")]]; ?></p></td>
											<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $fab_desc_type; ?></p></td>
											<td width="50"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
											<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo '0'; ?>','woven_today_receive_popup');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</a></p></td>

											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><? echo $receive_amount; ?></p></td>
											<td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty; echo number_format($rec_bal,2,'.',''); ?></p></td>
											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 8; ?>','woven_today_issue_popup','','<?php echo $rate;?>','<? echo "btnUOM"; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup','','<?php echo $rate;?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><? echo $issue_amount; ?></p></td>
											<td width="80" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','woven_knit_stock_popup');"><? echo number_format($stock,2,'.',''); ?></a></p></td>
											<td width="" align="right" title=""><p><? echo $stock_amount; ?></p></td>
										</tr>
											<?
											$i++;

											$sub_req_qty+=$book_qty;
											$sub_today_recv+=$today_recv;
											$sub_rec_qty+=$rec_qty;
											$sub_rec_bal+=$rec_bal;
											$sub_today_issue+=$today_issue;
											$sub_issue_qty+=$iss_qty;
											$sub_stock+=$stock;
											
											$sub_receive_amount+=$receive_amount;
											$sub_issue_amount+=$issue_amount;
											$sub_stock_value+=$stock_amount;
											
											$total_req_qty+=$book_qty;
											$total_rec_qty+=$rec_qty;
											$total_rec_bal+=$rec_bal;
											$total_issue_qty+=$iss_qty;
											$total_stock+=$stock;
											$total_possible_cut_pcs+=$possible_cut_pcs;
											$total_actual_cut_qty+=$actual_qty;
											$total_rec_return_qnty+=$receive_ret_qnty;
											$total_issue_ret_qnty+=$issue_ret_qnty;
											$total_today_issue+=$today_issue;
											$total_today_recv+=$today_recv;
											
											$total_receive_amount+=$receive_amount;
											$total_issue_amount+=$issue_amount;
											$total_stock_value+=$stock_amount;
									}
									else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
											<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
											<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
											<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
											<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
											<td width="100" title="<? echo $po_ids;?>"><p>
												<? 
													$po_ids_exp=explode(",", $po_ids);
													$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
													foreach ($po_ids_exp as $row) {
														if($po_id_shipingStatus_arr[$row]==3)
														{
															$full_shipSts_countx++;
														}
														else if($po_id_shipingStatus_arr[$row]==2){
															$partial_shipSts_countx++;
														}
														else if($po_id_shipingStatus_arr[$row]==1){
															$panding_shipSts_countx++;
														}
														$poId_countx++;
													}
													if($full_shipSts_countx==$poId_countx){
														$ShipingStatus="Full Delivery/Closed";
													}
													else if ($partial_shipSts_countx==$poId_countx) {
														$ShipingStatus="Partial Delivery";
													}
													else if ($panding_shipSts_countx==$poId_countx) {
														$ShipingStatus="Full Pending";
													}
													else
													{
														$ShipingStatus="Partial Delivery";
													}
													echo $ShipingStatus;
												?></p></td>
											<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
											<td width="100" align="center"><p><? echo $fabric_type_arr[$val[("detarmination_id")]]; ?></p></td>
											<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $fab_desc_type; ?></p></td>
											<td width="50"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
											<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo '0'; ?>','woven_today_receive_popup');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</a></p></td>

											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><? echo $receive_amount; ?></p></td>
											<td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty; echo number_format($rec_bal,2,'.',''); ?></p></td>
											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 8; ?>','woven_today_issue_popup','','<?php echo $rate;?>','<? echo "btnUOM"; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup','','<?php echo $rate;?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><? echo $issue_amount; ?></p></td>
											<td width="80" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','woven_knit_stock_popup');"><? echo number_format($stock,2,'.',''); ?></a></p></td>
											<td width="" align="right" title=""><p><? echo $stock_amount; ?></p></td>
										</tr>
											<?
											$i++;

											$sub_req_qty+=$book_qty;
											$sub_today_recv+=$today_recv;
											$sub_rec_qty+=$rec_qty;
											$sub_rec_bal+=$rec_bal;
											$sub_today_issue+=$today_issue;
											$sub_issue_qty+=$iss_qty;
											$sub_stock+=$stock;
											
											$sub_receive_amount+=$receive_amount;
											$sub_issue_amount+=$issue_amount;
											$sub_stock_value+=$stock_amount;
											
											$total_req_qty+=$book_qty;
											$total_rec_qty+=$rec_qty;
											$total_rec_bal+=$rec_bal;
											$total_issue_qty+=$iss_qty;
											$total_stock+=$stock;
											$total_possible_cut_pcs+=$possible_cut_pcs;
											$total_actual_cut_qty+=$actual_qty;
											$total_rec_return_qnty+=$receive_ret_qnty;
											$total_issue_ret_qnty+=$issue_ret_qnty;
											$total_today_issue+=$today_issue;
											$total_today_recv+=$today_recv;
											
											$total_receive_amount+=$receive_amount;
											$total_issue_amount+=$issue_amount;
											$total_stock_value+=$stock_amount;
									}

								}
							}
						}
							?>
							<tr style="font-weight:bold;background-color:#e0e0e0;">
								<td colspan="11" align="right">UOM Total</td>
								<td align="right"><? echo number_format($sub_req_qty,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_today_recv,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_rec_qty,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_receive_amount,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_rec_bal,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_today_issue,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_issue_qty,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_issue_amount,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_stock,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_stock_value,2,'.','') ?></td>
							</tr>
							<?
					}
						?>
					</table>
                	<table width="1810" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
                    <tfoot>
                        <th width="30"></th>
                        <th width="100"></th>
                        <th width="60"></th>
                        <th width="50"></th>
                        <th width="110">&nbsp;</th>

                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="220">&nbsp;</th>
                        <th width="50">Total</th>
                        <th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
                        <th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?></th>
                        <th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
                        <th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_receive_amount,2,'.',''); ?></th>

                        <th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
                        <th width="80"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
                        <th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
                        <th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_amount,2,'.',''); ?></th>

                        <th width="80" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
                        <th width="" align="right" id=""><? echo number_format($total_stock_value,2,'.',''); ?></th>
                    </tfoot>
                </table>
            </div>
        </fieldset>
        <?
    }

	$html = ob_get_contents();
	ob_clean();

	foreach (glob("*.xls") as $filename) {

		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**".$cbo_report_type;
	exit();
}

if($action=="open_exfactory")
{
	echo load_html_head_contents("Ex-factory Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption><strong> Order Status</strong></caption>
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="100">Order No</th>
						<th width="100">Ex-factory Date</th>
						<th width="80">Ex-factory Qty</th>
						<th>Order Status</th>
					</tr>
				</thead>
				<tbody>
					<?

					$sql="select a.id, a.ex_factory_date, a.ex_factory_qnty,b.po_number,b.shiping_status
					from  pro_ex_factory_mst a, wo_po_break_down b
					where b.id=a.po_break_down_id and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id in($po_id)";
					//echo $mrr_sql;

					$dtlsArray=sql_select($sql);
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="50" align="center"><p><? echo $i; ?></p></td>
							<td width="100" align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="100" align="center"><p><? if($row[csf('ex_factory_date')]!="" &&  $row[csf('ex_factory_date')]!="0000-00-00") echo change_date_format($row[csf('ex_factory_date')]); ?></p></td>
							<td width="80" align="right"><? echo number_format($row[csf('ex_factory_qnty')],2); ?></td>
							<td width="" align="center"><p><? echo  $shipment_status[$row[csf('shiping_status')]]; ?></p></td>
						</tr>
						<?
						$tot_exfact_qty+=$row[csf('ex_factory_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="3" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_exfact_qty,2); ?>&nbsp;</td>
						<td>&nbsp; </td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="open_order_exfactory")
{
	echo load_html_head_contents("Ex-factory Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:480px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="120">Order No</th>
						<th width="80">Ex-factory Date</th>
						<th width="100">Ex-factory Qty</th>
						<th>Order Status</th>
					</tr>
				</thead>
				<tbody>
					<?

					$sql="select a.id as order_id, a.po_number, a.shiping_status, b.ex_factory_date, b.ex_factory_qnty
					from wo_po_break_down a, pro_ex_factory_mst b
					where a.id=b.po_break_down_id and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($po_id)";
				//echo $sql;

					$dtlsArray=sql_select($sql);
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="50" align="center"><p><? echo $i; ?></p></td>
							<td width="120"><? echo $row[csf('po_number')]; ?></td>
							<td width="80" align="center"><p><? if($row[csf('ex_factory_date')]!="" &&  $row[csf('ex_factory_date')]!="0000-00-00") echo change_date_format($row[csf('ex_factory_date')]); ?></p></td>
							<td align="right" width="100"><? echo number_format($row[csf('ex_factory_qnty')],2); ?></td>
							<td><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
						</tr>
						<?
						$tot_exfact_qty+=$row[csf('ex_factory_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<th width="50">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="80">Total</th>
						<th width="100"><? echo number_format($tot_exfact_qty,2); ?></th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');

	extract($_REQUEST);
	?>

	<fieldset style="width:1550px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		?>
		<div style="width:870px;" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="1545" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="18">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="80">Batch No</th>
						<th width="60">Rack No</th>
						<th width="80">Grey Used</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="70">Process Loss.</th>
						<th width="60">QC ID</th>
						<th width="80">QC Name</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th>Collar/Cuff Pcs</th>
					</tr>
				</thead>
				<tbody>
					<?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");

					$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}
				//print_r($grey_used_arr);
					$i=1;

					$mrr_sql="select a.id,a.recv_number, a.booking_no,a.receive_date,a.knitting_source,a.knitting_company,a.challan_no,a.emp_id,a.qc_name,b.rack_no as rack_no, b.prod_id,b.batch_id,b.body_part_id,b.gsm,b.width,c.dtls_id, sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty,c.color_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
					where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id = d.id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and d.status_active = 1 and d.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id in ( $prod_id ) and c.color_id='$color' and c.trans_id!=0 and c.trans_type=1 group by a.id,a.recv_number, a.receive_date,a.booking_no, a.emp_id,b.rack_no,b.prod_id,b.body_part_id,c.dtls_id,c.color_id,a.knitting_source,a.knitting_company,a.challan_no,a.qc_name,b.batch_id,b.gsm,b.width";

				//echo $mrr_sql;

					$dtlsArray=sql_select($mrr_sql);
					$tot_grey_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
						$process_loss=100-($row[csf('quantity')]/$grey_used_qty)*100;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="60"><p><? echo $row[csf('rack_no')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? echo number_format($process_loss,2); ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('emp_id')]; ?></p></td>
							<td width="80" align="center"><p><? echo $row[csf('qc_name')]; ?></p></td>

							<td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $row[csf('width')]; ?></p></td>
							<td><p><? //echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_grey_qty+=$grey_used_qty;
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_grey_qty,2); ?> </td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td colspan="5"> </td>
						<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Issue Return Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="80">Ret. Date</th>
						<th width="100">Dyeing Source</th>
						<th width="120">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
						<th width="80">Rack</th>
						<th width="80">Ret. Qty</th>
						<th width="">Fabric Des.</th>

					</tr>
				</thead>
				<tbody>
					<?
					$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
					}
					//print_r($issue_arr);
					$i=1;
				//and a.entry_form in (7,37,66,68)
					$ret_sql="select a.recv_number,a.challan_no,a.issue_id, a.receive_date,b.prod_id,b.pi_wo_batch_no, sum(c.quantity) as quantity,sum(c.returnable_qnty) as returnable_qnty,c.color_id from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (52) and c.entry_form in (52)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.prod_id in ( $prod_id ) and c.trans_id!=0 group by a.recv_number,a.challan_no, a.receive_date,b.prod_id,a.issue_id,b.pi_wo_batch_no,c.color_id";
				//echo $ret_sql;

					$retDataArray=sql_select($ret_sql);

					foreach($retDataArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
					//echo $row[csf('pi_wo_batch_no')].'='.$batch_no_arr[$row[csf('pi_wo_batch_no')]];
						$knit_dye_source=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
						$knit_dye_company=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];

						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
							//$knitting_company=$knit_dye_company;
						}
						else
						{
							$knitting_company=$supplier_name_arr[$knit_dye_company];
							//$knitting_company=$knit_dye_company;
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="80"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="100"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
							<td width="120" ><p><? echo $knitting_company; ?></p></td>
							<td width="100" ><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
							<td  width="100" align="right"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td  width="100" align="right"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
							<td  width="80" align="right"><p><? echo $row[csf('Rack')]; ?></p></td>
							<td  width="80" align="right"><p><? echo $row[csf('quantity')]; ?></p></td>

							<td align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						</tr>
						<?
						$tot_issue_return_qty+=$row[csf('quantity')];
					//$tot_returnable_qnty+=$row[csf('returnable_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="1060" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="130">Transfer ID</th>
						<th width="80">Transfer Date</th>
						<th width="80">Trans. From Order</th>
						<th width="80">Trans. To Order</th>
						<th width="100">Challan</th>
						<th width="100">Color</th>
						<th width="100">Batch</th>
						<th width="70">Rack</th>
						<th width="70">Qty</th>
						<th width="">Fabric Des.</th>
					</tr>
				</thead>
				<tbody>
					<?

				//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
				//$sql_transfer_out="select a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,a.from_order_id,a.to_order_id,b.rack,b.batch_id,c.color_id,sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=15 and c.trans_type=5 and c.color_id='$color' and a.transfer_criteria=4 and a.to_order_id in($po_id) and c.prod_id in ( $prod_id )  and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by  a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,b.from_order_id,b.to_order_id,b.rack,b.batch_id,c.color_id";

					$sql_transfer_out=" select a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,a.from_order_id,a.to_order_id,b.rack,b.batch_id,c.color_id,sum(b.transfer_qnty) as transfer_out_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=3 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15) and c.trans_type=5 and c.color_id='2' and a.transfer_criteria=4 and a.to_order_id in($po_id) and c.prod_id in ( $prod_id ) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.transfer_system_id, a.challan_no,a.transfer_date,a.from_order_id, b.uom, b.from_prod_id,b.from_order_id,b.to_order_id,b.rack,b.batch_id,c.color_id,a.to_order_id";

					$transfer_out=sql_select($sql_transfer_out);

					foreach($transfer_out as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="80"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td width="80"><p><? echo $po_number_no_arr[$row[csf('from_order_id')]]; ?></p></td>
							<td width="80"><p><? echo $po_number_no_arr[$row[csf('to_order_id')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100" ><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="70" ><p><? echo $row[csf('rack')]; ?> &nbsp;</p></td>
							<td width="70" align="right" ><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?> &nbsp;</p></td>
							<td ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total Receive Balance</td>
						<td align="right">&nbsp;<? $tot_balance=$tot_qty+$tot_issue_return_qty+$tot_trans_qty; echo number_format($tot_balance,2); ?>&nbsp;</td>
						<td>&nbsp; </td>
					</tr>
				</tfoot>
			</table>
		</div>

		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />


		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}

/*if($action=="total_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');

	extract($_REQUEST);
	?>

	<fieldset style="width:1720px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		ob_start();
		?>
		<div style="width:870px;" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="1720" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="18">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Receive Company</th>
						<th width="100">Challan No</th>

						<th width="80">Style</th>
						<th width="80">Po No</th>
						<th width="80">Buyer</th>

						<th width="80">Color</th>
						<th width="80">Batch No</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

						<th width="80">Fin. Rcv. Qty.</th>
						<th width="70">Process Loss.</th>
						<th width="60">QC ID</th>
						<th>QC Name</th>
					</thead>
					<tbody>
						<?
						$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
						$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
						$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
						$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");

						$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
						$dtlsgrey=sql_select($grey_sql);
						$grey_used_arr=array();
						foreach($dtlsgrey as $row)
						{
							$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
						}
					//print_r($grey_used_arr);
						$i=1;

						$mrr_sql="select a.id,a.recv_number, a.booking_no,a.receive_date,a.knitting_source,a.knitting_company,a.challan_no,a.emp_id,a.qc_name,b.rack_no as rack_no, b.prod_id,b.batch_id,b.body_part_id,b.gsm,b.width,b.order_id,b.buyer_id,c.dtls_id, sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty,c.color_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
						where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id = d.id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and d.status_active = 1 and d.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id in ( $prod_id ) and c.color_id='$color' and c.trans_id!=0 and c.trans_type=1 group by a.id,a.recv_number, a.receive_date,a.booking_no, a.emp_id,b.rack_no,b.prod_id,b.body_part_id,c.dtls_id,c.color_id,a.knitting_source,a.knitting_company,a.challan_no,a.qc_name,b.batch_id,b.gsm,b.width,b.order_id,b.buyer_id";

					//echo $mrr_sql;

						$dtlsArray=sql_select($mrr_sql);
						$tot_grey_qty=0;
						foreach($dtlsArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$tot_reject=$row[csf('returnable_qnty')];
							if($row[csf('knitting_source')]==1)
							{
								$knitting_company=$company_arr[$row[csf('knitting_company')]];
							}
							else
							{
								$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
							}
							$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
							$process_loss=100-($row[csf('quantity')]/$grey_used_qty)*100;
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
								<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
								<td width="110"><p><? echo $knitting_company; ?></p></td>
								<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>

								<td width="80"><p><? echo $style_ref_no; ?></p></td>
								<td width="80"><p><? echo $po_number_no_arr[$row[csf('order_id')]]; ?></p></td>
								<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>

								<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
								<td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
								<td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo $row[csf('width')]; ?></p></td>

								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? echo number_format($process_loss,2); ?></p></td>
								<td width="60" align="center"><p><? echo $row[csf('emp_id')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('qc_name')]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
						//$tot_grey_qty+=$grey_used_qty;
						//$tot_reject_qty+=$row[csf('returnable_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="14" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty,2); ?> </td>
							<td colspan="5"> </td>

						</tr>
					</tfoot>
				</table>

			</table>
		</div>

		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />


		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}*/
if($action=="total_receive_popup_show6")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	/*po_id
	prod_id
	job_no
	color
	type
	action
	style_ref_no 
	internalref
	buyer
	batchId
	determination
	itemdesc
	gsm
	dia
	*/
	//list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	$mrr_sql="SELECT a.id,a.recv_number, a.receive_date,a.knitting_source,a.knitting_company,a.location_id, b.prod_id, b.batch_id,b.body_part_id, b.gsm,b.width, e.store_id, sum(c.quantity) as quantity, d.color_id,b.remarks,a.challan_no
	from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d, inv_transaction e 
	where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id = d.id and c.trans_id = e.id and a.entry_form in (37) and c.entry_form in (37)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and d.status_active = 1 and d.is_deleted=0 and a.company_id='$companyID'  and c.color_id='$color' and c.prod_id in ( $prod_id )   and c.trans_id!=0 and c.trans_type=1 and d.id=$batchId
	group by a.id, a.recv_number,a.knitting_source,a.knitting_company,a.location_id, a.receive_date, b.prod_id, b.body_part_id, d.color_id, b.batch_id,b.gsm, b.width, e.store_id,b.remarks,a.challan_no";
	//and c.prod_id in ( $prod_id )  and c.po_breakdown_id in($po_id)
	//and d.id=$batchId and b.width='$dia'  and b.gsm='$gsm'

	$dtlsArray=sql_select($mrr_sql);
	foreach($dtlsArray as $row)
	{
		$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		$knitting_company_arr[$row[csf('knitting_company')]] = $row[csf('knitting_company')];
		$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
	}

	if(!empty($product_id_arr))
		$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

	if(!empty($knitting_company_arr))
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier where id in(".implode(",",$knitting_company_arr).")", "id", "supplier_name");

	if(!empty($batch_id_arr))
		$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
	if(!empty($color_id_arr))
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".implode(",",$color_id_arr).")", "id", "color_name"  );
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");

		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

		ob_start();
		?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Receive Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer];?></td>
						<td><? echo $job_no;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $internalref;?></td>
						<td><? echo $color_arr[$color];?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<table border="1" class="rpt_table" rules="all" width="1380" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="70">Challan</th>
						<th width="80">Batch No</th>
						<th width="110">Service Company</th>
						<th width="110">Service Location</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="80">Store Name</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$tot_grey_qty=0;$i=1;
					foreach($dtlsArray as $row)
					{
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $knitting_company; ?></p></td>
							<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('width')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							<td align="right" style="word-wrap: break-word; word-break: break-all;"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="12" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
		</table>
	</div>
	<?

	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});
	</script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="total_issue_rtn_popup_show6")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	//list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	$mrr_sql="SELECT a.id,a.recv_number, a.receive_date,a.knitting_source,a.knitting_company,a.location_id, b.prod_id, b.batch_id,b.body_part_id, b.gsm,b.width, e.store_id, sum(c.quantity) as quantity, d.color_id,b.remarks,a.challan_no
	from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d, inv_transaction e 
	where a.id=b.mst_id and b.trans_id=c.trans_id and b.batch_id = d.id and c.trans_id = e.id and a.entry_form in (52) and c.entry_form in (52)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and d.status_active = 1 and d.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID'  and c.color_id='$color' and c.prod_id in ( $prod_id )   and c.trans_id!=0 and c.trans_type=4
	group by a.id, a.recv_number,a.knitting_source,a.knitting_company,a.location_id, a.receive_date, b.prod_id, b.body_part_id, d.color_id, b.batch_id,b.gsm, b.width, e.store_id,b.remarks,a.challan_no";
	//and c.prod_id in ( $prod_id )
	//and d.id=$batchId and b.width='$dia'  and b.gsm='$gsm'

	$dtlsArray=sql_select($mrr_sql);
	foreach($dtlsArray as $row)
	{
		$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		$knitting_company_arr[$row[csf('knitting_company')]] = $row[csf('knitting_company')];
		$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
	}

	if(!empty($product_id_arr))
		$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

	if(!empty($knitting_company_arr))
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier where id in(".implode(",",$knitting_company_arr).")", "id", "supplier_name");

	if(!empty($batch_id_arr))
		$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
	if(!empty($color_id_arr))
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".implode(",",$color_id_arr).")", "id", "color_name"  );
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");

		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

		ob_start();
		?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Issue Return Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer];?></td>
						<td><? echo $job_no;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $internalref;?></td>
						<td><? echo $color_arr[$color];?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<table border="1" class="rpt_table" rules="all" width="1080" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Issue Return Qty.</th>
						<th width="80">Store Name</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$tot_grey_qty=0;$i=1;
					foreach($dtlsArray as $row)
					{
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('width')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							<td align="right" style="word-wrap: break-word; word-break: break-all;"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
		</table>
	</div>
	<?

	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});
	</script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="total_trans_in_popup_show6")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	//list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	$mrr_sql="select a.id,a.transfer_system_id as recv_number, a.transfer_date as receive_date, b.to_prod_id as prod_id, b.to_batch_id as batch_id,b.body_part_id, e.gsm,e.dia_width as width, b.to_store as store_id, sum(b.transfer_qnty) as quantity, b.color_id,b.remarks from inv_item_transfer_mst a,inv_item_transfer_dtls b,inv_transaction c, order_wise_pro_details d,product_details_master e where a.id=b.mst_id and b.to_trans_id=c.id and c.id=d.trans_id and b.to_prod_id=e.id and  d.entry_form in(14) and a.entry_form in(14) and c.item_category=2 and c.transaction_type=5 and d.trans_type=5 and a.to_company=$companyID and b.color_id='$color' and b.to_batch_id= $batchId and b.to_order_id in($po_id) and b.to_prod_id in ( $prod_id ) group by a.id,a.transfer_system_id, a.transfer_date, b.to_prod_id, b.to_batch_id,b.body_part_id, e.gsm,e.dia_width, b.to_store, b.color_id,b.remarks";

	
		/*$finishing_trans_in_qty_array=array();
		foreach($data_array_finish_qnty_transfer_in as $row)
		{
			$finishing_trans_in_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("trans_in_qnty")];
				$prodIdArrs[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]['prod_id']=$row[csf("prod_id")];
		}*/
		

	//and c.prod_id in ( $prod_id )
	//and d.id=$batchId and b.width='$dia'  and b.gsm='$gsm'

	$dtlsArray=sql_select($mrr_sql);
	foreach($dtlsArray as $row)
	{
		$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		$knitting_company_arr[$row[csf('knitting_company')]] = $row[csf('knitting_company')];
		$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
	}

	if(!empty($product_id_arr))
		$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

	if(!empty($knitting_company_arr))
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier where id in(".implode(",",$knitting_company_arr).")", "id", "supplier_name");

	if(!empty($batch_id_arr))
		$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
	if(!empty($color_id_arr))
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".implode(",",$color_id_arr).")", "id", "color_name"  );
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");

		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

		ob_start();
		?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Transfer In Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer];?></td>
						<td><? echo $job_no;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $internalref;?></td>
						<td><? echo $color_arr[$color];?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<table border="1" class="rpt_table" rules="all" width="1380" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Transfer In Qty.</th>
						<th width="80">Store Name</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$tot_grey_qty=0;$i=1;
					foreach($dtlsArray as $row)
					{
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('width')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							<td align="right" style="word-wrap: break-word; word-break: break-all;"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
		</table>
	</div>
	<?

	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});
	</script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="total_issue_popup_show6")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	//list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	/*$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
	$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	//$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	//$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );*/



	/*$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
	$dtlsgrey=sql_select($grey_sql);
	$grey_used_arr=array();
	foreach($dtlsgrey as $row)
	{
		$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
	}*/
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		ob_start();
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
		?>
		<div style="width:1000px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Issue Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer];?></td>
						<td><? echo $job_no;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $internalref;?></td>
						<td><? echo $color_arr[$color];?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="1400" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="17">Issue Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="70">Challan No</th>
						<th width="80">Batch No</th>
						<th width="110">Service Company</th>
						<th width="110">Service Location</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="80">Store Name</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$mrr_sql="SELECT a.id,a.company_id, a.issue_number, a.issue_date,a.challan_no,a.knit_dye_source,a.knit_dye_company,a.location_id, b.prod_id,b.batch_id,b.rack_no, b.cutting_unit, c.quantity as quantity,c.color_id,c.po_breakdown_id,a.knit_dye_source,a.knit_dye_company,b.store_id, b.remarks,a.challan_no
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(18,71)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id='$companyID'  and c.prod_id in( $prod_id ) and c.color_id='$color' and b.batch_id=$batchId"; // and c.po_breakdown_id in ($po_id)
					//and a.issue_date <='$from_date'
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					foreach($dtlsArray as $row)
					{
						$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
						$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
						$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
						$order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}

					if(!empty($product_id_arr))
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

					if(!empty($batch_id_arr))
					{
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
					}

					if(!empty($batch_id_arr))
					{
						$batch_id_cond = " and b.batch_id in(".implode(",",$batch_id_arr).")";
					}

					$sql_issue="SELECT a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68) $batch_id_cond";
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						$issue_arr[$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
					}

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$rack=$issue_arr[$row[csf('batch_id')]]['rack'];
						$knit_dye_source=$row[csf('knit_dye_source')];
						$knit_dye_company=$row[csf('knit_dye_company')];
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];

						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knit_dye_company')]];
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td align="center"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td align="center"><p><? echo $knitting_company; ?></p></td>
							<td align="center"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
							<td align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td align="left"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $gsm; ?></p></td>
							<td align="center"><p><? echo $width; ?></p></td>
							<td align="right"><p><? echo $row[csf('quantity')]; ?></p></td>
							<td align="left"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_issue_return_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="12" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
		</table>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>
	</fieldset>
	<?
	exit();
} 
if($action=="total_trans_out_popup_show6")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	//list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	$poSql=sql_select("select b.id,b.grouping,c.mst_id from wo_po_details_master a,wo_po_break_down b,pro_batch_create_dtls c where a.id=b.job_id and b.id=c.po_id and b.id in($po_id) and c.mst_id=$batchId and b.grouping='$internalref' group by  b.id,b.grouping,c.mst_id");
	foreach($poSql as $row)
	{
		$po_info_arr[$row[csf("mst_id")]]['grouping']=$row[csf("grouping")];
	}

	$mrr_sql="select a.id,a.transfer_system_id as recv_number, a.transfer_date as receive_date, b.from_prod_id as prod_id, b.batch_id,b.body_part_id, e.gsm,e.dia_width as width, b.from_store as store_id, sum(b.transfer_qnty) as quantity, b.color_id,b.remarks from inv_item_transfer_mst a,inv_item_transfer_dtls b,inv_transaction c, order_wise_pro_details d,product_details_master e where a.id=b.mst_id and b.trans_id=c.id and c.id=d.trans_id  and b.from_prod_id=e.id and  d.entry_form in(14) and a.entry_form in(14) and c.item_category=2 and c.transaction_type=6 and d.trans_type=6 and a.company_id='$companyID' and b.color_id='$color' and b.from_order_id in($po_id) and b.from_prod_id in ( $prod_id ) group by a.id,a.transfer_system_id, a.transfer_date, b.from_prod_id, b.batch_id,b.body_part_id, e.gsm,e.dia_width, b.from_store, b.color_id,b.remarks";
		/*$finishing_trans_out_qty_array=array();
		
		foreach($data_array_finish_qnty_transfer_out as $row)
		{
			$finishing_trans_out_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("trans_out_qnty")];
				$prodIdArrs[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]['prod_id']=$row[csf("prod_id")];
		}*/


	//and c.prod_id in ( $prod_id )
	//and d.id=$batchId and b.width='$dia'  and b.gsm='$gsm'

	$dtlsArray=sql_select($mrr_sql);
	foreach($dtlsArray as $row)
	{
		$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		$knitting_company_arr[$row[csf('knitting_company')]] = $row[csf('knitting_company')];
		$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
	}

	if(!empty($product_id_arr))
		$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

	if(!empty($knitting_company_arr))
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier where id in(".implode(",",$knitting_company_arr).")", "id", "supplier_name");

	if(!empty($batch_id_arr))
		$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
	if(!empty($color_id_arr))
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".implode(",",$color_id_arr).")", "id", "color_name"  );
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");

		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

		ob_start();
		?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer];?></td>
						<td><? echo $job_no;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $internalref;?></td>
						<td><? echo $color_arr[$color];?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<table border="1" class="rpt_table" rules="all" width="1180" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="100">From Int. Ref.</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Transfer Out Qty.</th>
						<th width="80">Store Name</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$tot_grey_qty=0;$i=1;
					foreach($dtlsArray as $row)
					{
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td><p><? echo $po_info_arr[$row[csf('batch_id')]]['grouping']; ?></p></td> 
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('width')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							<td align="right" style="word-wrap: break-word; word-break: break-all;"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="10" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td colspan="3"></td>
					</tr>
				</tfoot>
			</table>
		</table>
	</div>
	<?

	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});
	</script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="total_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	$mrr_sql="SELECT a.id,a.recv_number, a.receive_date,a.knitting_source,a.knitting_company,a.location_id, b.prod_id, b.batch_id,b.body_part_id, b.gsm,b.width, e.store_id, sum(c.quantity) as quantity, d.color_id,b.remarks,a.challan_no
	from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d, inv_transaction e
	where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id = d.id and c.trans_id = e.id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and d.status_active = 1 and d.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id in ( $prod_id ) and c.color_id='$color' and c.trans_id!=0 and c.trans_type=1
	group by a.id, a.recv_number,a.knitting_source,a.knitting_company,a.location_id, a.receive_date, b.prod_id, b.body_part_id, d.color_id, b.batch_id,b.gsm, b.width, e.store_id,b.remarks,a.challan_no";

	$dtlsArray=sql_select($mrr_sql);
	foreach($dtlsArray as $row)
	{
		$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		$knitting_company_arr[$row[csf('knitting_company')]] = $row[csf('knitting_company')];
		$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
	}

	if(!empty($product_id_arr))
		$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

	if(!empty($knitting_company_arr))
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier where id in(".implode(",",$knitting_company_arr).")", "id", "supplier_name");

	if(!empty($batch_id_arr))
		$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
	if(!empty($color_id_arr))
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".implode(",",$color_id_arr).")", "id", "color_name"  );
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");

		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

		ob_start();
		?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Receive Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<table border="1" class="rpt_table" rules="all" width="1380" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="70">Challan</th>
						<th width="80">Batch No</th>
						<th width="110">Service Company</th>
						<th width="110">Service Location</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="80">Store Name</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$tot_grey_qty=0;$i=1;
					foreach($dtlsArray as $row)
					{
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $knitting_company; ?></p></td>
							<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('width')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							<td align="right" style="word-wrap: break-word; word-break: break-all;"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="12" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
		</table>
	</div>
	<?

	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});
	</script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</fieldset>
<?
exit();
}

if($action=="receive_ret_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');

	extract($_REQUEST);
	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);
	?>

	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
		?>
		<div style="width:1000px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Receive Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Stock Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="">Store Name</th>
					</tr>
				</thead>
				<tbody>
					<?
					$ret_sql="SELECT a.recv_number,a.challan_no,a.issue_id, a.receive_date,b.prod_id,b.pi_wo_batch_no, sum(c.quantity) as quantity,sum(c.returnable_qnty) as returnable_qnty,c.color_id,c.po_breakdown_id,b.store_id
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (52) and c.entry_form in (52)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.prod_id in ( $prod_id ) and c.trans_id!=0 group by a.recv_number,a.challan_no, a.receive_date,b.prod_id,a.issue_id,b.pi_wo_batch_no,c.color_id,c.po_breakdown_id,b.store_id";
					//echo $ret_sql;
					$retDataArray=sql_select($ret_sql);
					foreach($retDataArray as $row)
					{
						$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
						$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
						$batch_id_arr[$row[csf('pi_wo_batch_no')]] = $row[csf('pi_wo_batch_no')];
						$order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}
					// print_r($batch_id_arr);
					if(!empty($product_id_arr))
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

					if(!empty($batch_id_arr))
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");


					if(!empty($batch_id_arr))
					{
						$batch_id_cond = " and b.batch_id in(".implode(",",$batch_id_arr).")";
					}
					$sql_issue="SELECT a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68) $batch_id_cond";
					// echo $sql_issue;die();
					$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						$issue_arr[$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
					}
						//print_r($issue_arr);

					/*$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}*/
						//print_r($grey_used_arr);

					$i=1;
						//and a.entry_form in (7,37,66,68)




					foreach($retDataArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
						//echo $row[csf('pi_wo_batch_no')].'='.$batch_no_arr[$row[csf('pi_wo_batch_no')]];
						$knit_dye_source=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
						$knit_dye_company=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];
						$gsm=$issue_arr[$row[csf('pi_wo_batch_no')]]['gsm'];
						$width=$issue_arr[$row[csf('pi_wo_batch_no')]]['width'];
						// $grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
						//echo $gsm;
						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
								//$knitting_company=$knit_dye_company;
						}
						else
						{
							$knitting_company=$supplier_name_arr[$knit_dye_company];
								//$knitting_company=$knit_dye_company;
						}

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td align="center"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
							<td align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td align="center"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $gsm; ?></p></td>
							<td align="center"><p><? echo $width; ?></p></td>
							<td align="right"><p><? echo $row[csf('quantity')]; ?></p></td>
							<td align="left"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
						</tr>
						<?
						$tot_issue_return_qty+=$row[csf('quantity')];
						// $tot_grey_qty+=$grey_used_qty;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>

		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />


		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}//Knit Finish end

if($action=="receive_trns_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');

	extract($_REQUEST);
	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);
	?>

	<fieldset style="width:1000px; margin:0 auto">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
		?>
		<div style="width:1000px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">

			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Receive Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="1080" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="12">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="100">From Int. Ref.</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="">Store Name</th>
					</tr>
				</thead>
				<tbody>
					<?

					$sql_transfer_in=" SELECT a.company_id, a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,b.from_order_id,b.to_order_id,b.rack,b.batch_id,c.color_id,sum(b.transfer_qnty) as transfer_out_qnty,c.prod_id,b.to_store as store_id
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134,306) and c.trans_type=5 and c.color_id=$color and a.entry_form in(14,15,134,306) and b.to_order_id in($po_id) and c.prod_id in ($prod_id ) and a.transfer_date <='$from_date' and a.item_category=2 and b.status_active=1 and b.is_deleted=0
					group by a.company_id, a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,b.from_order_id,b.to_order_id,b.rack,b.batch_id,c.color_id,c.prod_id,b.to_store";
					//echo $sql_transfer_in;
					$transfer_in=sql_select($sql_transfer_in);
					foreach($transfer_in as $row)
					{
						$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
						$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
						$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
						$from_order_id_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
						$order_id_arr[$row[csf('to_order_id')]] = $row[csf('to_order_id')];
					}

					if(!empty($product_id_arr))
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

					if(!empty($batch_id_arr))
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
					$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
					if(!empty($batch_id_arr))
					{
						$batch_id_cond = " and b.batch_id in(".implode(",",$batch_id_arr).")";
					}

					$sql_issue="SELECT a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68) $batch_id_cond";
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						$issue_arr[$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
					}

					if(!empty($from_order_id_arr))
					{
						$order_id_cond = " b.id in(".implode(",",$from_order_id_arr).")";
						$sql = "SELECT b.grouping, b.po_number, b.id as from_order_id from wo_po_break_down b where $order_id_cond";
						$from_sty=sql_select($sql);
						$internal_ref_arr = array();
						foreach ($from_sty as $key => $row)
						{
							$internal_ref_arr[$row[csf('from_order_id')]]=$row[csf('grouping')];
						}
					}

					$i=1;
					foreach($transfer_in as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knit_dye_source=$issue_arr[$row[csf('batch_id')]]['knit_dye_source'];
						$knit_dye_company=$issue_arr[$row[csf('batch_id')]]['knit_dye_company'];
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];
						$buyer_id=$issue_arr[$row[csf('batch_id')]]['buyer_id'];

						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$knit_dye_company];
						}

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td><p><? echo $internal_ref_arr[$row[csf('from_order_id')]]; ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td align="right"><p><? echo $gsm; ?></p></td>
							<td align="right"><p><? echo $width; ?></p></td>

							<td align="right" ><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?> &nbsp;</p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
						</tr>
						<?
						$tot_issue_return_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="11" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>

		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

	//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />


		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}//Knit Finish end

if($action=="today_receive_popup")//today_receive_popup
{
	echo load_html_head_contents("Today Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1550px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		?>
		<div style="width:870px;" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="left">
			<table border="1" class="rpt_table" rules="all" width="1000" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Today Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="80">Batch No</th>
						<th width="60">Rack No</th>
						<th width="80">Rcv. Qty.</th>
						<th width="">Fabric Desc.</th>

					</tr>
				</thead>
				<tbody>
					<?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");

					//$sql="select a.id as order_id, a.po_number, a.shiping_status from wo_po_break_down a where b.status_active=1 and b.is_deleted=0";

					$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
					$dtlsbook=sql_select($book_sql);
					$booking_arr=array();
					foreach($dtlsbook as $row)
					{
						$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
					}
					$i=1;
					//;  	d.transaction_date='$from_date'  inv_transaction
					$mrr_sql="select a.recv_number, a.booking_no,a.receive_date,a.knitting_source,a.knitting_company,a.challan_no, b.prod_id,b.batch_id,sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty,c.color_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c,inv_transaction d
					where a.id=b.mst_id and b.id=c.dtls_id and d.id=c.trans_id and  a.id=d.mst_id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id in( $prod_id ) and c.color_id='$color' and c.trans_id!=0 and c.trans_type=1 and d.transaction_date='$from_date' group by a.recv_number, a.receive_date,a.booking_no,b.prod_id,c.color_id,a.knitting_source,a.knitting_company,a.challan_no,b.batch_id";

					//echo $mrr_sql;

					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="60"><p><? echo $row[csf('rack_no')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>

						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_booking_qty+=$booking_qty;
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>

						<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}//Today Recv end

if($action=="req_qnty_popup")
{
	echo load_html_head_contents("Today Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$department_name_arr=return_library_array( "select id, department_name from  lib_department", "id", "department_name");
	$internal_ref_arr=return_library_array( "select job_no_mst, grouping from  wo_po_break_down where id in($po_id)", "job_no_mst", "grouping");
	//echo $job_key."==",$poID."==".$desc_key;
	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	$main_sql="SELECT a.booking_no, b.booking_type,b.job_no,b.is_short,a.booking_year,a.internal_ref_no,b.responsible_person,b.responsible_dept,b.reason,sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and  b.po_break_down_id in($po_id) and a.company_id=$companyID and b.construction= '$fabric_type' and b.fabric_color_id in($color) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, b.booking_type,b.job_no,b.is_short,a.booking_year,a.internal_ref_no,b.responsible_person,b.responsible_dept,b.reason";

	$dtlsArray=sql_select($main_sql);
	
	?>
	<fieldset style="width:1000px; margin:0 auto">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="990" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Stock Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Internal Ref.</th>
						<th width="110">Booking Year</th>
						<th width="110">Booking No</th>
						<th width="110">Booking Type</th>
						<th width="80">Grey Qty Kg.</th>
						<th width="80">Finish Qty Kg.</th>
						<th width="100">Responsible Dept.</th>
						<th width="100"> Responsible person</th>
						<th>Reason</th>
					</thead>
					<tbody>
						<?
						$i=1;
						$tot_grey_qty=0;
						foreach($dtlsArray as $row)
						{
							if($row[csf('booking_type')]==3)
							{
								$booking_type = "Service";
							}
							else if($row[csf('booking_type')]==4)
							{
								$booking_type = "Sample";
							}
							else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
							{
								$booking_type = "Main";
							}
							else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
							{
								$booking_type = "Short";
							}

							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td align="center"><p><? echo $internal_ref_arr[$row[csf('job_no')]]; ?></p></td>
								<td align="center"><p><? echo $row[csf('booking_year')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td align="center"><p><? echo $booking_type; ?></p></td>
								<td align="right"><p><? echo  number_format($row[csf('grey_fab_qnty')],2); ?></p></td>
								<td align="right"><p><? echo  number_format($row[csf('fin_fab_qnty')],2); ?></p></td>
								<td align="center"><p><? echo $department_name_arr[$row[csf('responsible_dept')]]; ?></p></td>
								<td align="center"><p><? echo $row[csf('responsible_person')]; ?></p></td>
								<td><p>&nbsp;<? echo $row[csf('reason')]; ?></p></td>
							</tr>
							<?
							$tot_qty_grey+=$row[csf('grey_fab_qnty')];
							$tot_qty_fin+=$row[csf('fin_fab_qnty')];
							$i++;
						}
						$short_booking_sql="select a.booking_no,b.booking_type,b.job_no,b.is_short,a.internal_ref_no,a.booking_year,b.responsible_person,b.responsible_dept,b.po_break_down_id,b.reason, b.construction,b.fabric_color_id,sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty 
					 from wo_booking_mst a, wo_booking_dtls b
					  left join wo_pre_cost_fabric_cost_dtls c on b.pre_cost_fabric_cost_dtls_id=c.id and c.status_active=1 and c.is_deleted=0 and b.fabric_color_id in($color) 
					   where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  b.po_break_down_id in($po_id)
					   and c.construction ='$fabric_type' and b.is_short=1 group by a.booking_no,b.booking_type,b.job_no,b.is_short,a.internal_ref_no,a.booking_year,b.responsible_person,b.responsible_dept,b.po_break_down_id,b.reason, b.construction,b.fabric_color_id";

						$shortBookingArray=sql_select($short_booking_sql);
						foreach($shortBookingArray as $row)

						{
							if($row[csf('booking_type')]==3)
							{
								$booking_type = "Service";
							}
							else if($row[csf('booking_type')]==4)
							{
								$booking_type = "Sample";
							}
							else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
							{
								$booking_type = "Main";
							}
							else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
							{
								$booking_type = "Short";
							}
							$responsible_dept=explode(",", $row[csf('responsible_dept')]);
							$responsibleDept="";
							foreach ($responsible_dept as $value) {
								$responsibleDept.=$department_name_arr[$value].",";
							}
							$responsibleDept=chop($responsibleDept,",");

							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td align="center"><p><? echo $internal_ref_arr[$row[csf('job_no')]]; ?></p></td>
								<td align="center"><p><? echo $row[csf('booking_year')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td align="center"><p><? echo $booking_type; ?></p></td>
								<td align="right"><p><? echo  number_format($row[csf('grey_fab_qnty')],2); ?></p></td>
								<td align="right"><p><? echo  number_format($row[csf('fin_fab_qnty')],2); ?></p></td>
								<td align="center"><p><? echo $responsibleDept;; ?></p></td>
								<td align="center"><p><? echo $row[csf('responsible_person')]; ?></p></td>
								<td><p>&nbsp;<? echo $row[csf('reason')]; ?></p></td>
							</tr>
							<?
							$tot_qty_grey+=$row[csf('grey_fab_qnty')];
							$tot_qty_fin+=$row[csf('fin_fab_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="5" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty_grey,2); ?> </td>
							<td align="right"><? echo number_format($tot_qty_fin,2); ?> </td>
							<td colspan="3"></td>
						</tr>
					</tfoot>
				</table>

			</table>
		</div>

		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}

if($action=="today_total_rec_popup")
{
	echo load_html_head_contents("Today Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	$mrr_sql="SELECT a.recv_number, a.booking_no,a.receive_date,a.knitting_source,a.knitting_company,a.location_id,a.challan_no, b.prod_id,b.batch_id,b.order_id,b.buyer_id,b.gsm,b.width,sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty,c.color_id,d.store_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c,inv_transaction d
	where a.id=b.mst_id and b.id=c.dtls_id and d.id=c.trans_id and  a.id=d.mst_id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id in( $prod_id ) and c.color_id='$color' and c.trans_id!=0 and c.trans_type=1 and d.transaction_date='$from_date' group by a.recv_number, a.receive_date,a.booking_no,b.prod_id,b.order_id,b.buyer_id,b.gsm,b.width,c.color_id,a.knitting_source,a.knitting_company,a.location_id,a.challan_no,b.batch_id,d.store_id";

	$dtlsArray=sql_select($mrr_sql);
	foreach($dtlsArray as $row)
	{
		$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		$knitting_company_arr[$row[csf('knitting_company')]] = $row[csf('knitting_company')];
		$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
		$order_id_arr[$row[csf('order_id')]] = $row[csf('order_id')];
	}

	if(!empty($product_id_arr))
		$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

	if(!empty($knitting_company_arr))
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier where id in(".implode(",",$knitting_company_arr).")", "id", "supplier_name");

	if(!empty($batch_id_arr))
		$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");

	$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	?>
	<fieldset style="width:1000px; margin:0 auto">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="7">Style Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="1210" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="13">Stock Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="110">Transection Date</th>
						<th width="110">Batch No</th>
						<th width="110">Service Company</th>
						<th width="110">Service Location</th>
						<th width="100">Batch Color</th>
						<th width="120">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Quantity</th>
						<th>Store</th>
					</thead>
					<tbody>
						<?
						$i=1;
						$tot_grey_qty=0;
						foreach($dtlsArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if($row[csf('knitting_source')]==1)
							{
								$knitting_company=$company_arr[$row[csf('knitting_company')]];
							}
							else
							{
								$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
							}
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
								<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
								<td><p><? echo $knitting_company; ?></p></td>
								<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
								<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
								<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
								<td align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('width')]; ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?>&nbsp;</p></td>
								<td><p>&nbsp;<? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="11" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty,2); ?> </td>
							<td></td>
						</tr>
					</tfoot>
				</table>

			</table>
		</div>

		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}//Today total_rec_popup end

if($action=="today_total_rtn_popup")
{
	echo load_html_head_contents("Today Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
		ob_start();
		?>
		<div style="width:1000px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Receive Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Issue Return Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="">Store Name</th>
					</tr>
				</thead>
				<tbody>
					<?
					$mrr_sql="SELECT a.recv_number,a.challan_no,a.issue_id, a.receive_date,b.prod_id,b.pi_wo_batch_no, sum(c.quantity) as quantity,sum(c.returnable_qnty) as returnable_qnty,c.color_id,c.po_breakdown_id,b.store_id,a.knitting_company
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (52,126) and c.entry_form in (52,126) and a.item_category=2 and a.receive_date='$from_date' and
					a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id in ( $prod_id ) and c.trans_id!=0
					group by a.recv_number,a.challan_no, a.receive_date,b.prod_id,a.issue_id,b.pi_wo_batch_no,c.color_id,c.po_breakdown_id,b.store_id,a.knitting_company";
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					foreach($dtlsArray as $row)
					{
						$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
						$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
						$knitting_company_arr[$row[csf('knitting_company')]] = $row[csf('knitting_company')];
						$batch_id_arr[$row[csf('pi_wo_batch_no')]] = $row[csf('pi_wo_batch_no')];
						$order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}

					if(!empty($product_id_arr))
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

					if(!empty($knitting_company_arr))
						$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier where id in(".implode(",",$knitting_company_arr).")", "id", "supplier_name");

					if(!empty($batch_id_arr))
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");

					$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
					if(!empty($batch_id_arr))
					{
						$batch_id_cond = " and b.batch_id in(".implode(",",$batch_id_arr).")";
					}

					$sql_issue="SELECT a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68) $batch_id_cond";

					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						$issue_arr[$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
					}
						//print_r($issue_arr);

					$grey_sql="SELECT a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}
						//print_r($grey_used_arr);
					$i=1;
					//;  	d.transaction_date='$from_date'  inv_transaction
					// $mrr_sql="select a.recv_number, a.booking_no,a.receive_date,a.knitting_source,a.knitting_company,a.challan_no, b.prod_id,b.batch_id,sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty,c.color_id
					// from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c,inv_transaction d
					// where a.id=b.mst_id and d.id=c.trans_id and a.entry_form in (52,126) and c.entry_form in (52,126)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id in( $prod_id ) and c.color_id='$color' and c.trans_id!=0 and c.trans_type=4 and d.transaction_date='$from_date'
					// group by a.recv_number, a.receive_date,a.booking_no,b.prod_id,c.color_id,a.knitting_source,a.knitting_company,a.challan_no,b.batch_id";


					// $mrr_sql="select a.recv_number,a.challan_no,a.issue_id, a.receive_date,b.prod_id,b.pi_wo_batch_no, sum(c.quantity) as quantity,sum(c.returnable_qnty) as returnable_qnty,c.color_id
					// from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					// where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (52,126) and c.entry_form in (52,126)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and a.receive_date='05-Mar-2015' and and c.prod_id in ( $prod_id ) and c.trans_id!=0 group by a.recv_number,a.challan_no, a.receive_date,b.prod_id,a.issue_id,b.pi_wo_batch_no,c.color_id";




					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$rack = $issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
						$gsm = $issue_arr[$row[csf('pi_wo_batch_no')]]['gsm'];
						$width = $issue_arr[$row[csf('pi_wo_batch_no')]]['width'];
						$knitting_source_id = $issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
						$knit_dye_company_id = $issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];
						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$knit_dye_company_id];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$knit_dye_company_id];
						}
						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td align="right"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
							<td align="right"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="right"><p><? echo $gsm; ?></p></td>
							<td align="right"><p><? echo $width; ?></p></td>
							<td align="right"><p><? echo $row[csf('quantity')]; ?></p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
						</tr>
						<?
						$tot_issue_return_qty+=$row[csf('quantity')];
						$tot_grey_qty+=$grey_used_qty;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}//Today total_rec_popup end

if($action=="today_total_trans_in_popup")
{
	echo load_html_head_contents("Today Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);
	?>
	<fieldset style="width:1000px; margin:0 auto">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
		?>
		<div style="width:1000px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Receive Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="1080" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="12">Today Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="100">From Int. Ref.</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Qty.</th>
						<th width="">Store Name</th>
					</tr>
				</thead>
				<tbody>
					<?
					$sql_transfer_in=" SELECT a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,a.from_order_id,a.to_order_id,b.rack,b.batch_id,c.color_id, c.prod_id,sum(b.transfer_qnty) as transfer_out_qnty,b.to_store as store_id
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134,306) and c.trans_type=5 and c.color_id=$color and a.entry_form in(14,15,134,306) and b.to_order_id in($po_id) and c.prod_id in ($prod_id ) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 and a.transfer_date='$from_date'
					group by a.transfer_system_id, a.challan_no,a.transfer_date,a.from_order_id, b.uom, b.from_prod_id,b.from_order_id,b.to_order_id,b.rack,b.batch_id,c.color_id,a.to_order_id, c.prod_id,b.to_store";
					//echo $sql_transfer_in;
					$transfer_in=sql_select($sql_transfer_in);
					foreach($transfer_in as $row)
					{
						$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
						$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
						$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
						$order_id_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
					}

					if(!empty($product_id_arr))
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

					if(!empty($batch_id_arr))
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");


					if(!empty($batch_id_arr))
					{
						$batch_id_cond = " and b.batch_id in(".implode(",",$batch_id_arr).")";
					}
					$sql_issue="SELECT a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.order_id,b.buyer_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68) $batch_id_cond";
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						$issue_arr[$row[csf('batch_id')]]['order_id']=$row[csf('order_id')];
						$issue_arr[$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
					}

					if(!empty($order_id_arr))
					{
						$order_id_cond = " b.id in(".implode(",",$order_id_arr).")";
						$sql = "SELECT b.grouping, b.po_number, b.id as from_order_id from wo_po_break_down b where $order_id_cond";
						$from_sty=sql_select($sql);
						$internal_ref_arr = array();
						foreach ($from_sty as $key => $row)
						{
							$internal_ref_arr[$row[csf('from_order_id')]]=$row[csf('grouping')];
						}
					}

					$i=1;
					foreach($transfer_in as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knit_dye_source=$issue_arr[$row[csf('batch_id')]]['knit_dye_source'];
						$knit_dye_company=$issue_arr[$row[csf('batch_id')]]['knit_dye_company'];

						$buyer_id=$issue_arr[$row[csf('batch_id')]]['buyer_id'];
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];

						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$knit_dye_company];
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td><p><? echo $internal_ref_arr[$row[csf('from_order_id')]]; ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td align="right"><p><? echo $gsm; ?></p></td>
							<td align="right"><p><? echo $width; ?></p></td>
							<td align="right" ><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?> &nbsp;</p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
						</tr>
						<?
						$tot_issue_return_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="10" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}

//Today total_rec_popup end
if($action=="woven_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$prod_id_arr=explode("__", $prod_id);
	$prod_id=$prod_id_arr[0];
	$product_id=$prod_id_arr[1];
	?>
	<fieldset style="width:1195px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		$batch_no_arr=return_library_array( "select id,batch_no from pro_batch_create_mst where id in(".$batchId.")", "id", "batch_no"  );

		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1275" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="19">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="100">Batch No</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th>Collar/Cuff Pcs</th>
					</tr>
				</thead>
				<tbody>
					<?
					$gsm_arr=return_library_array( "select id, gsm_weight from lib_yarn_count_determina_mst", "id", "gsm_weight");
					$prodData=sql_select("select id, item_description, unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$gsm_arr[$row[csf('detarmination_id')]];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
					$dtlsbook=sql_select($book_sql);
					$booking_arr=array();
					foreach($dtlsbook as $row)
					{
						$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
					}

					$i=1;
					if ($rpt_btn=='btn3') 
					{
							$mrr_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity,b.batch_id 
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.batch_id in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.order_rate,b.batch_id order by a.receive_date";
					}
					else
					{
							$mrr_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity,b.batch_id 
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.order_rate,b.batch_id order by a.receive_date";
					}
					
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1) $knitting_company=$company_arr[$row[csf('knitting_company')]];
						else $knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];

						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
							<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_amount+=number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2,'.','');
						$tot_booking_qty+=$booking_qty;
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Total<? //echo number_format($tot_booking_qty,2); ?> </td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount,2); ?> </td>
						<td colspan="2"> </td>
						<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
            <br/>
			<table border="1" class="rpt_table" rules="all" width="1275" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="19">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
                        <!-- <th width="80">Dyeing Source</th>
                        <th width="110">Dyeing Company</th> -->
                        <th width="100">Challan No</th>
                        <th width="80">Color</th>
                        <th width="100">Batch No</th>
                        <!-- <th width="60">Rack No</th>
                        <th width="80">Grey Qty.</th> -->
                        <th width="80">Fin. Transfer. Qty.</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <!-- <th width="70">Process Loss Qty.</th>
                        <th width="60">QC ID</th>
                        <th width="80">QC Name</th> -->
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th>Collar/Cuff Pcs</th>
                    </tr>
                </thead>
                <tbody>
                	<?
                	$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
                	$prod_array=array();
                	foreach($prodData as $row)
                	{
                		$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
                		$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
                		$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
                	}

                	$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
                	$dtlsbook=sql_select($book_sql);

                	$booking_arr=array();
                	foreach($dtlsbook as $row)
                	{
                		$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
                	}

                	$i=1;

			/*	echo	$mrr_sql_trnsf="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, sum(c.quantity) as quantity from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id=$prod_id and c.color_id='$color' group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no";*/

					if ($rpt_btn=='btn3')
					{
						$mrr_sql_trnsf="select a.transfer_system_id, a.challan_no,a.transfer_date,c.color_id, b.prod_id,b.rack,d.feb_description_id,d.gsm,d.dia_width,
						sum(c.quantity) as quantity,b.pi_wo_batch_no 
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=d.mst_id and d.to_trans_id=b.id
						and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and b.transaction_type=5 and c. trans_type=5 and a.entry_form in (258) and c.entry_form in (258) and a.item_category=3 
						and a.is_deleted=0 and a.status_active=1 
						and b.status_active=1 and b.is_deleted=0
						and c.po_breakdown_id in($po_id) and a.to_company='$companyID' and b.pi_wo_batch_no in($batchId) and e.id=$product_id  and c.color_id='$color'
						group by a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id, b.prod_id,b.rack,d.feb_description_id,d.gsm,d.dia_width,b.pi_wo_batch_no order by a.transfer_date";
						//and e.product_name_details='$prod_id'
					}
					else
					{
						$mrr_sql_trnsf="select a.transfer_system_id, a.challan_no,a.transfer_date,c.color_id, b.prod_id,b.rack,d.feb_description_id,d.gsm,d.dia_width,
						sum(c.quantity) as quantity,b.pi_wo_batch_no 
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=d.mst_id and d.to_trans_id=b.id
						and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and b.transaction_type=5 and c. trans_type=5 and a.entry_form in (258) and c.entry_form in (258) and a.item_category=3 
						and a.is_deleted=0 and a.status_active=1 
						and b.status_active=1 and b.is_deleted=0
						and c.po_breakdown_id in($po_id) and a.to_company='$companyID' and e.id=$product_id  and c.color_id='$color'
						group by a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id, b.prod_id,b.rack,d.feb_description_id,d.gsm,d.dia_width,b.pi_wo_batch_no  order by a.transfer_date";
						//and e.product_name_details='$prod_id'
					}

					$dtlsArray=sql_select($mrr_sql_trnsf);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <!--  <td width="80"><p><? //echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                            <td width="110"><p><? //echo $knitting_company; ?></p></td> -->
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                             <td width="100"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
                            <!--<td width="60"><p><? //echo $row[csf('rack')]; ?></p></td>
                            <td width="80" align="right"><p><? //echo number_format($booking_qty,2); ?></p></td>-->
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2); ?></p></td>
                            <!-- <td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? //echo number_format($process_loss); ?></p></td>
                            <td width="60" align="center"><p><? //echo $row[csf('emp_id')]; ?></p></td>
                            <td width="80" align="center"><p><? //echo $row[csf('qc_name')]; ?></p></td> -->
                            
                            <td width="200" ><p><? echo trim($description," , "); ?></p></td>
                            <td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $dia; ?></p></td>
                            <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                         </tr>
                         <?
                         $tot_qty_trns+=$row[csf('quantity')];
						 $tot_amount_trns+=number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2,'.','');
                         $tot_booking_qty+=$booking_qty;
                         $tot_reject_qty+=$row[csf('returnable_qnty')];
                         $i++;

                     }
                     ?>
                 </tbody>
                 <tfoot>
                 	<tr class="tbl_bottom">
                 		<td colspan="5" align="right"></td>
                 		<td align="right">Total<? //echo number_format($tot_booking_qty,2); ?> </td>
                 		<td align="right"><? echo number_format($tot_qty_trns,2); ?> </td>
                 		<td align="right"></td>
                 		<td align="right"><? echo number_format($tot_amount_trns,2); ?> </td>
                 		<td colspan="2"> </td>
                 		<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
                 		<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                 	</tr>
                 </tfoot>
             </table>
            <br/>
             <table border="1" class="rpt_table" rules="all" width="1275" cellpadding="0" cellspacing="0" align="center">
             	<thead>
             		<tr>
             			<th colspan="18">Issue Return Details</th>
             		</tr>
             		<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th>
                        <th width="70">Return Date</th>
                        <!-- <th width="80">Dyeing Source</th>
                        <th width="110">Dyeing Company</th> -->
                        <th width="100">Challan No</th>
                        <th width="80">Color</th>
                         <th width="100">Batch No</th>
                        <!--<th width="60">Rack No</th>
                        <th width="80">Grey Qty.</th> -->
                        <th width="80">Issue Return Qty.</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <!-- <th width="70">Process Loss Qty.</th>
                        <th width="60">QC ID</th>
                        <th width="80">QC Name</th> -->
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th>Collar/Cuff Pcs</th>
                    </tr>
                </thead>
                <tbody>
                	<?
                	$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
                	$prod_array=array();
                	foreach($prodData as $row)
                	{
                		$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
                		$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
                		$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
                	}

                	$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
                	$dtlsbook=sql_select($book_sql);

                	$booking_arr=array();
                	foreach($dtlsbook as $row)
                	{
                		$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
                	}

                	$i=1;

					if ($rpt_btn=='btn3')
					{
						$mrr_sql_issue_rtn="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, sum(c.quantity) as quantity,b.pi_wo_batch_no 
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.pi_wo_batch_no in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no,b.pi_wo_batch_no order by a.receive_date";
					}
					else
					{
						$mrr_sql_issue_rtn="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, sum(c.quantity) as quantity,b.pi_wo_batch_no
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no,b.pi_wo_batch_no order by a.receive_date";
					}

					$dtlsArray=sql_select($mrr_sql_issue_rtn);
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <!--  <td width="80"><p><? //echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                            <td width="110"><p><? //echo $knitting_company; ?></p></td> -->
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                             <td width="100"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
                            <!--<td width="60"><p><? //echo $row[csf('rack')]; ?></p></td>
                            <td width="80" align="right"><p><? //echo number_format($booking_qty,2); ?></p></td>-->
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2); ?></p></td>
                            <!-- <td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? //echo number_format($process_loss); ?></p></td>
                            <td width="60" align="center"><p><? //echo $row[csf('emp_id')]; ?></p></td>
                            <td width="80" align="center"><p><? //echo $row[csf('qc_name')]; ?></p></td> -->
                            <td width="200" ><p><? echo trim($description," , "); ?></p></td>
                            <td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $dia; ?></p></td>
                            <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                        </tr>
                         <?
                         $tot_issueRtn_qty+=$row[csf('quantity')];
						 $tot_issueRtn_amount+=number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2,'.','');
                         $tot_booking_qty+=$booking_qty;
                         $tot_reject_qty+=$row[csf('returnable_qnty')];
                         $i++;
                     }
                     ?>
                 </tbody>
                 <tfoot>
                 	<tr class="tbl_bottom">
                 		<td colspan="5" align="right"></td>
                 		<td align="right">Total<? //echo number_format($tot_booking_qty,2); ?> </td>
                 		<td align="right"><? echo number_format($tot_issueRtn_qty,2); ?> </td>
                 		<td align="right"></td>
                 		<td align="right"><? //echo number_format($tot_issueRtn_qty,2); ?> </td>
                 		<td align="right"></td>
                 		<td align="right"><? //echo number_format($tot_issueRtn_amount,2); ?> </td>
                 		
                 		<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
                 		<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                 	</tr>
                 </tfoot>
             </table>
         </div>
     </fieldset>
     <?
     $html=ob_get_contents();
     ob_flush();

     foreach (glob(""."*.xls") as $filename)
     {
     	@unlink($filename);
     }

			//html to xls convert
     $name=time();
     $name=$user_id."_".$name.".xls";
     $create_new_excel = fopen(''.$name, 'w');
     $is_created = fwrite($create_new_excel,$html);

     ?>
     <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
     <script>
     	$(document).ready(function(e) {
     		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
     	});

     </script>
     <?
     exit();
}
if($action=="woven_only_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1195px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1175" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="18">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th>Collar/Cuff Pcs</th>
					</tr>
				</thead>
				<tbody>
					<?
					$gsm_arr=return_library_array( "select id, gsm_weight from lib_yarn_count_determina_mst", "id", "gsm_weight");
					$prodData=sql_select("select id, item_description, unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$gsm_arr[$row[csf('detarmination_id')]];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
					$dtlsbook=sql_select($book_sql);
					$booking_arr=array();
					foreach($dtlsbook as $row)
					{
						$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
					}

					$i=1;
					
					$mrr_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity
				from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
				where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.order_rate";
					
					
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1) $knitting_company=$company_arr[$row[csf('knitting_company')]];
						else $knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];

						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
							<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_amount+=number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2,'.','');
						$tot_booking_qty+=$booking_qty;
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right"><? //echo number_format($tot_booking_qty,2); ?> </td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount,2); ?> </td>
						<td colspan="2"> </td>
						<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table> 
         </div>
     </fieldset>
     <?
     $html=ob_get_contents();
     ob_flush();

     foreach (glob(""."*.xls") as $filename)
     {
     	@unlink($filename);
     }

			//html to xls convert
     $name=time();
     $name=$user_id."_".$name.".xls";
     $create_new_excel = fopen(''.$name, 'w');
     $is_created = fwrite($create_new_excel,$html);

     ?>
     <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
     <script>
     	$(document).ready(function(e) {
     		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
     	});

     </script>
     <?
     exit();
}
if($action=="woven_only_issue_rtn_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1195px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			
		
             <table border="1" class="rpt_table" rules="all" width="1175" cellpadding="0" cellspacing="0" align="center">
             	<thead>
             		<tr>
             			<th colspan="18">Issue Return Details</th>
             		</tr>
             		<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th>
                        <th width="70">Return Date</th>
                        <!-- <th width="80">Dyeing Source</th>
                        <th width="110">Dyeing Company</th> -->
                        <th width="100">Challan No</th>
                        <th width="80">Color</th>
                        <!-- <th width="80">Batch No</th>
                        <th width="60">Rack No</th>
                        <th width="80">Grey Qty.</th> -->
                        <th width="80">Issue Return Qty.</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <!-- <th width="70">Process Loss Qty.</th>
                        <th width="60">QC ID</th>
                        <th width="80">QC Name</th> -->
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th>Collar/Cuff Pcs</th>
                    </tr>
                </thead>
                <tbody>
                	<?
                	$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
                	$prod_array=array();
                	foreach($prodData as $row)
                	{
                		$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
                		$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
                		$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
                	}

                	$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
                	$dtlsbook=sql_select($book_sql);

                	$booking_arr=array();
                	foreach($dtlsbook as $row)
                	{
                		$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
                	}

                	$i=1;

				
					$mrr_sql_issue_rtn="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, sum(c.quantity) as quantity,b.cons_rate,b.cons_amount
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no,b.cons_rate,b.cons_amount";
				

					$dtlsArray=sql_select($mrr_sql_issue_rtn);
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <!--  <td width="80"><p><? //echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                            <td width="110"><p><? //echo $knitting_company; ?></p></td> -->
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <!-- <td width="80"><p><? //echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60"><p><? //echo $row[csf('rack')]; ?></p></td>
                            <td width="80" align="right"><p><? //echo number_format($booking_qty,2); ?></p></td>-->
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('cons_rate')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format(($row[csf('quantity')]*$row[csf('cons_rate')]),2); ?></p></td>
                            <!-- <td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? //echo number_format($process_loss); ?></p></td>
                            <td width="60" align="center"><p><? //echo $row[csf('emp_id')]; ?></p></td>
                            <td width="80" align="center"><p><? //echo $row[csf('qc_name')]; ?></p></td> -->
                            <td width="200" ><p><? echo trim($description," , "); ?></p></td>
                            <td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $dia; ?></p></td>
                            <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                        </tr>
                         <?
                         $tot_issueRtn_qty+=$row[csf('quantity')];
						 $tot_issueRtn_amount+=number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2,'.','');
                         $tot_booking_qty+=$booking_qty;
                         $tot_reject_qty+=$row[csf('returnable_qnty')];
                         $i++;
                     }
                     ?>
                 </tbody>
                 <tfoot>
                 	<tr class="tbl_bottom">
                 		<td colspan="4" align="right">Total</td>
                 		<td align="right"><? //echo number_format($tot_booking_qty,2); ?> </td>
                 		<td align="right"><? echo number_format($tot_issueRtn_qty,2); ?> </td>
                 		<td align="right"></td>
                 		<td align="right"><? //echo number_format($tot_issueRtn_qty,2); ?> </td>
                 		<td align="right"></td>
                 		<td align="right"><? //echo number_format($tot_issueRtn_amount,2); ?> </td>
                 		
                 		<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
                 		<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                 	</tr>
                 </tfoot>
             </table>
         </div>
     </fieldset>
     <?
     $html=ob_get_contents();
     ob_flush();

     foreach (glob(""."*.xls") as $filename)
     {
     	@unlink($filename);
     }

			//html to xls convert
     $name=time();
     $name=$user_id."_".$name.".xls";
     $create_new_excel = fopen(''.$name, 'w');
     $is_created = fwrite($create_new_excel,$html);

     ?>
     <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
     <script>
     	$(document).ready(function(e) {
     		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
     	});

     </script>
     <?
     exit();
}
if($action=="woven_only_transf_in_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1195px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			
			<table border="1" class="rpt_table" rules="all" width="1175" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="18">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
                        <!-- <th width="80">Dyeing Source</th>
                        <th width="110">Dyeing Company</th> -->
                        <th width="100">Challan No</th>
                        <th width="80">Color</th>
                        <!-- <th width="80">Batch No</th>
                        <th width="60">Rack No</th>
                        <th width="80">Grey Qty.</th> -->
                        <th width="80">Fin. Transfer. Qty.</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <!-- <th width="70">Process Loss Qty.</th>
                        <th width="60">QC ID</th>
                        <th width="80">QC Name</th> -->
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th>Collar/Cuff Pcs</th>
                    </tr>
                </thead>
                <tbody>
                	<?
                	$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
                	$prod_array=array();
                	foreach($prodData as $row)
                	{
                		$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
                		$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
                		$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
                	}

                	$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
                	$dtlsbook=sql_select($book_sql);

                	$booking_arr=array();
                	foreach($dtlsbook as $row)
                	{
                		$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
                	}

                	$i=1;
					$mrr_sql_trnsf="select a.transfer_system_id, a.challan_no,a.transfer_date,c.color_id, b.prod_id,b.rack,d.feb_description_id,d.gsm,d.dia_width,
					sum(c.quantity) as quantity,b.cons_rate 
					from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e 
					where a.id=d.mst_id and d.to_trans_id=b.id
					and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and b.transaction_type=5 and c. trans_type=5 and a.entry_form in (258) and c.entry_form in (258) and a.item_category=3 
					and a.is_deleted=0 and a.status_active=1 
					and b.status_active=1 and b.is_deleted=0
					and c.po_breakdown_id in($po_id) and a.to_company='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color'
					group by a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id, b.prod_id,b.rack,d.feb_description_id,d.gsm,d.dia_width,b.cons_rate";
					

					$dtlsArray=sql_select($mrr_sql_trnsf);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <!--  <td width="80"><p><? //echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                            <td width="110"><p><? //echo $knitting_company; ?></p></td> -->
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <!-- <td width="80"><p><? //echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60"><p><? //echo $row[csf('rack')]; ?></p></td>
                            <td width="80" align="right"><p><? //echo number_format($booking_qty,2); ?></p></td>-->
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('cons_rate')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format(($row[csf('quantity')]*$row[csf('cons_rate')]),2); ?></p></td>
                            <!-- <td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? //echo number_format($process_loss); ?></p></td>
                            <td width="60" align="center"><p><? //echo $row[csf('emp_id')]; ?></p></td>
                            <td width="80" align="center"><p><? //echo $row[csf('qc_name')]; ?></p></td> -->
                            
                            <td width="200" ><p><? echo trim($description," , "); ?></p></td>
                            <td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $dia; ?></p></td>
                            <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                         </tr>
                         <?
                         $tot_qty_trns+=$row[csf('quantity')];
						 $tot_amount_trns+=number_format(($row[csf('quantity')]*$row[csf('cons_rate')]),2,'.','');
                         $tot_booking_qty+=$booking_qty;
                         $tot_reject_qty+=$row[csf('returnable_qnty')];
                         $i++;

                     }
                     ?>
                 </tbody>
                 <tfoot>
                 	<tr class="tbl_bottom">
                 		<td colspan="4" align="right">Total</td>
                 		<td align="right"><? //echo number_format($tot_booking_qty,2); ?> </td>
                 		<td align="right"><? echo number_format($tot_qty_trns,2); ?> </td>
                 		<td align="right"></td>
                 		<td align="right"><? echo number_format($tot_amount_trns,2); ?> </td>
                 		<td colspan="2"> </td>
                 		<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
                 		<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                 	</tr>
                 </tfoot>
             </table>
         </div>
     </fieldset>
     <?
     $html=ob_get_contents();
     ob_flush();

     foreach (glob(""."*.xls") as $filename)
     {
     	@unlink($filename);
     }

			//html to xls convert
     $name=time();
     $name=$user_id."_".$name.".xls";
     $create_new_excel = fopen(''.$name, 'w');
     $is_created = fwrite($create_new_excel,$html);

     ?>
     <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
     <script>
     	$(document).ready(function(e) {
     		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
     	});

     </script>
     <?
     exit();
}
if($action=="woven_only_today_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1250px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
				<?
					$gsm_arr=return_library_array( "select id, gsm_weight from lib_yarn_count_determina_mst", "id", "gsm_weight");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");

					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$gsm_arr[$row[csf('detarmination_id')]];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_receive_date=""; else $today_receive_date= "and b.transaction_date='".$date_from."'";

				$mrr_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity
				from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
				where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_receive_date group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.order_rate";
		
				
					

				$dtlsArray=sql_select($mrr_sql);
				
			?>
			<table border="1" class="rpt_table" rules="all" width="1235" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="13">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('order_rate')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($amount,2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_amount+=number_format($amount,2,'.','');
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount,2); ?> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}

			//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);

	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}
if($action=="woven_only_today_issue_rtn_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1250px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
				<?
					$gsm_arr=return_library_array( "select id, gsm_weight from lib_yarn_count_determina_mst", "id", "gsm_weight");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");

					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$gsm_arr[$row[csf('detarmination_id')]];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_receive_date=""; else $today_receive_date= "and b.transaction_date='".$date_from."'";
		
					$mrr_issue_rtn_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.cons_rate, sum(c.quantity) as quantity
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_receive_date group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.cons_rate";

					$dtlsArray_issue_rtn=sql_select($mrr_issue_rtn_sql);
					
				?>	
			<table border="1" class="rpt_table" rules="all" width="1235" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="13">Issue Return Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtlsArray_issue_rtn as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('cons_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($amount,2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
						</tr>
						<?
						$tot_qty3+=$row[csf('quantity')];
						$tot_amount3+=number_format($amount,2,'.','');
						$tot_reject_qty3+=$row[csf('returnable_qnty')];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty3,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount3,2); ?> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}

			//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);

	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}
if($action=="woven_only_today_transf_in_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1250px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
				<?
					$gsm_arr=return_library_array( "select id, gsm_weight from lib_yarn_count_determina_mst", "id", "gsm_weight");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");

					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$gsm_arr[$row[csf('detarmination_id')]];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_receive_date=""; else $today_receive_date= "and b.transaction_date='".$date_from."'";

					$mrr_trns_in_sql="select a.transfer_system_id as  recv_number,null as knitting_source,null as booking_no, null as knitting_company, a.challan_no,a.transfer_date as receive_date,c.color_id, b.prod_id,b.rack, b.cons_rate, sum(c.quantity) as quantity
					from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
					where a.id=d.mst_id and d.to_trans_id=b.id
					and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and b.transaction_type=5 and c. trans_type=5 and a.entry_form in (258) and c.entry_form in (258) and a.item_category=3 
					and a.is_deleted=0 and a.status_active=1 
					and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.to_company='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_receive_date  group by a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id, b.prod_id,b.rack, b.cons_rate";

					$dtlsArray_trns_in=sql_select($mrr_trns_in_sql);
				?>

			<table border="1" class="rpt_table" rules="all" width="1235" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="13">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtlsArray_trns_in as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('cons_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($amount,2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
						</tr>
						<?
						$tot_qty2+=$row[csf('quantity')];
						$tot_amount2+=number_format($amount,2,'.','');
						$tot_reject_qty2+=$row[csf('returnable_qnty')];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty2,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount2,2); ?> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}

			//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);

	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}
if($action=="woven_today_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1250px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		$batch_no_arr=return_library_array( "select id,batch_no from pro_batch_create_mst where id in(".$batchId.")", "id", "batch_no"  );

		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
				<?
					$gsm_arr=return_library_array( "select id, gsm_weight from lib_yarn_count_determina_mst", "id", "gsm_weight");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");

					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$gsm_arr[$row[csf('detarmination_id')]];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_receive_date=""; else $today_receive_date= "and b.transaction_date='".$date_from."'";

					if ($rpt_btn=='btn3')
					{
						$mrr_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity,b.batch_id 
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.batch_id in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $today_receive_date group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.order_rate,b.batch_id";
				
						$mrr_issue_rtn_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity,b.pi_wo_batch_no as batch_id
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.pi_wo_batch_no in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $today_receive_date group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.order_rate,b.pi_wo_batch_no";

						$mrr_trns_in_sql="select a.transfer_system_id as  recv_number,null as knitting_source,null as booking_no, null as knitting_company, a.challan_no,a.transfer_date as receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity,b.pi_wo_batch_no as batch_id
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.to_trans_id=b.id
						and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and b.transaction_type=5 and c. trans_type=5 and a.entry_form in (258) and c.entry_form in (258) and a.item_category=3 
						and a.is_deleted=0 and a.status_active=1 
						and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.to_company='$companyID' and b.pi_wo_batch_no in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $today_receive_date  group by a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id, b.prod_id,b.rack, b.order_rate,b.pi_wo_batch_no";
					}
					else
					{
						$mrr_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity,b.batch_id
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_receive_date group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.order_rate,b.batch_id";
				
						$mrr_issue_rtn_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity,b.pi_wo_batch_no as batch_id 
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_receive_date group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.order_rate,b.pi_wo_batch_no";

						$mrr_trns_in_sql="select a.transfer_system_id as  recv_number,null as knitting_source,null as booking_no, null as knitting_company, a.challan_no,a.transfer_date as receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity,b.pi_wo_batch_no as batch_id
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.to_trans_id=b.id
						and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and b.transaction_type=5 and c. trans_type=5 and a.entry_form in (258) and c.entry_form in (258) and a.item_category=3 
						and a.is_deleted=0 and a.status_active=1 
						and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.to_company='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_receive_date  group by a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id, b.prod_id,b.rack, b.order_rate,b.pi_wo_batch_no";
					}

					

					$dtlsArray=sql_select($mrr_sql);
					$dtlsArray_trns_in=sql_select($mrr_trns_in_sql);
					$dtlsArray_issue_rtn=sql_select($mrr_issue_rtn_sql);
				?>
			<table border="1" class="rpt_table" rules="all" width="1335" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="14">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="100">Batch No</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('order_rate')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($amount,2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_amount+=number_format($amount,2,'.','');
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount,2); ?> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1335" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="14">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="100">Batch No</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtlsArray_trns_in as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('order_rate')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($amount,2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
						</tr>
						<?
						$tot_qty2+=$row[csf('quantity')];
						$tot_amount2+=number_format($amount,2,'.','');
						$tot_reject_qty2+=$row[csf('returnable_qnty')];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty2,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount2,2); ?> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1335" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="14">Issue Return Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="100">Batch No</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtlsArray_issue_rtn as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('order_rate')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($amount,2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
						</tr>
						<?
						$tot_qty3+=$row[csf('quantity')];
						$tot_amount3+=number_format($amount,2,'.','');
						$tot_reject_qty3+=$row[csf('returnable_qnty')];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty3,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount3,2); ?> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}

			//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);

	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}

if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	?>

	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}

	</script>

	<?
	ob_start();
	?>
	<div id="report_id" align="center" style="width:960px">
		<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
				<td> <div id="report_container"> </div> </td>
			</tr>
		</table>
		<table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0" align="center">
			<thead>
				<tr>
					<th colspan="10">Issue To Cutting Info</th>
				</tr>
				<tr>
					<th width="30">Sl</th>
					<th width="110">Issue No</th>
					<th width="120">Issue to Company</th>
					<th width="100">Challan No</th>
					<th width="100">Issue Date</th>
					<th width="100">Color</th>
					<th width="100">Batch No</th>
					<th width="60">Rack No</th>
					<th width="80">Qty</th>
					<th width="">Fabric Des.</th>
				</tr>
			</thead>
			<tbody>
				<?
				$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
				$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
				$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
				$i=1;
				$mrr_sql="select a.id,a.company_id, a.issue_number, a.issue_date,a.challan_no, b.prod_id,b.batch_id,b.rack_no, b.cutting_unit, c.quantity,c.color_id
				from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(18,71)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID'  and c.prod_id in( $prod_id ) and c.color_id='$color'";
					//echo $mrr_sql;

				$dtlsArray=sql_select($mrr_sql);

				foreach($dtlsArray as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";



					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
						<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
						<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
						<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
						<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
						<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
						<td width="60" ><p><? echo $row[csf('rack_no')]; ?></p></td>
						<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
						<td  align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
					</tr>
					<?
					$tot_qty+=$row[csf('quantity')];
					$i++;
				}
				?>
			</tbody>
			<tfoot>
				<tr class="tbl_bottom">
					<td colspan="8" align="right">Total</td>
					<td align="right"><? echo number_format($tot_qty,2); ?></td>
					<td>&nbsp; </td>
				</tr>
			</tfoot>
		</table>
		<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0" align="center">
			<thead>
				<tr>
					<th colspan="10">Receive Return Details</th>
				</tr>
				<tr>
					<th width="30">Sl</th>
					<th width="110">Recv.Ret.ID</th>
					<th width="120">Recv.Ret.Company</th>
					<th width="100">Challan No</th>
					<th width="70">Return Date</th>
					<th width="100">Color</th>
					<th width="100">Batch No</th>
					<th width="70">Rack No</th>
					<th  width="70">Return Qty</th>
					<th width="">Fabric Des.</th>
				</tr>
			</thead>
			<tbody>
				<?
				$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
				$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";
				$result_issue=sql_select($sql_issue);
				$issue_arr=array();
				foreach($result_issue as $row)
				{
					$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
					$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
					$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
				}
				$i=1;
				$ret_sql="select a.issue_number,a.company_id,a.challan_no,a.issue_date,b.pi_wo_batch_no,b.rack,c.color_id, b.prod_id, sum(c.quantity) as quantity
				from inv_issue_master a, inv_transaction b, order_wise_pro_details c
				where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (46) and c.entry_form in (46)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.prod_id in( $prod_id ) and c.trans_id!=0 group by a.issue_number,a.company_id,a.challan_no,a.issue_date,b.pi_wo_batch_no,c.color_id,b.rack, b.prod_id";
				$retDataArray=sql_select($ret_sql);

				foreach($retDataArray as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$knit_dye_source=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
					$knit_dye_company=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];
							//$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
					if($knit_dye_source==1)
					{
						$knitting_company=$company_arr[$knit_dye_company];
									//$knitting_company=$knit_dye_company;
					}
					else
					{
						$knitting_company=$supplier_name_arr[$knit_dye_company];
									//$knitting_company=$knit_dye_company;
					}
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
						<td width="130"><p><? echo $knitting_company; ?></p></td>
						<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
						<td width="70"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
						<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
						<td width="100"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
						<td width="70"><p><? echo $row[csf('rack')]; ?></p></td>

						<td  align="right" width="70"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
						<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
					</tr>
					<?
					$tot_ret_qty+=$row[csf('quantity')];
					$i++;
				}
				?>
			</tbody>
			<tfoot>
				<tr class="tbl_bottom">
					<td colspan="8" align="right">Total</td>
					<td align="right"><? echo number_format($tot_ret_qty,2); ?>&nbsp;</td>
					<td> </td>
				</tr>

			</tfoot>
		</table>
		<br>
		<table border="1" class="rpt_table" rules="all" width="880" cellpadding="0" cellspacing="0" align="center">
			<thead>
				<tr>
					<th colspan="10">Transfer Out Details</th>
				</tr>
				<tr>
					<th width="30">Sl</th>
					<th width="130">Transfer ID</th>
					<th width="70">Transfer Date</th>
					<th width="80">Trans. From Order</th>
					<th width="80">Trans. To Order</th>
					<th width="100">Color</th>
					<th width="100">Batch</th>
					<th width="70">Rack</th>
					<th width="70">Qty</th>
					<th>Fabric Des.</th>
				</tr>
			</thead>
			<tbody>
				<?
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
				$i=1;
					//$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$companyID and a.id=b.mst_id and a.transfer_criteria=4 and a.from_order_id in($po_id) and b.from_prod_id in ($prod_id) and a.item_category=2 group by a.from_order_id, b.from_prod_id, a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.from_prod_id, b.uom";

				$sql_transfer_out="select a.transfer_system_id, a.transfer_date,a.from_order_id,a.to_order_id,b.batch_id,b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty,c.color_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and c.prod_id in( $prod_id ) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by  a.transfer_system_id, a.transfer_date,b.from_prod_id,a.from_order_id,a.to_order_id,c.color_id,b.batch_id";
				$transfer_out=sql_select($sql_transfer_out);
				foreach($transfer_out as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
						<td width="70"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
						<td width="80"><p><? echo $row[csf('from_order_id')]; ?></p></td>
						<td width="80"><p><? echo $row[csf('to_order_id')]; ?></p></td>
						<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
						<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
						<td width="70" ><p><? echo $product_arr[$row[csf('rack')]]; ?></p></td>
						<td width="70" align="right" ><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?> &nbsp;</p></td>
						<td  align="right"><p><? echo  $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
					</tr>
					<?
					$tot_trans_qty+=$row[csf('transfer_out_qnty')];
					$i++;
				}
				?>
			</tbody>
			<tfoot>
				<tr class="tbl_bottom">
					<td colspan="8" align="right">Total</td>
					<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
					<td> </td>
				</tr>
				<tr class="tbl_bottom">
					<td colspan="8" align="right">Total Issue Balance</td>
					<td align="right"><? $tot_iss_bal=$tot_qty+$tot_trans_qty+$tot_ret_qty; echo number_format($tot_iss_bal,2); ?>&nbsp;</td>
					<td> </td>
				</tr>
			</tfoot>
		</table>
	</table>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}
			//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>

	<?
	exit();
} // Issue End

if($action=="total_issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	/*$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
	$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	//$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	//$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );*/



	/*$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
	$dtlsgrey=sql_select($grey_sql);
	$grey_used_arr=array();
	foreach($dtlsgrey as $row)
	{
		$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
	}*/
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		ob_start();
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
		?>
		<div style="width:1000px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Issue Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="1400" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="17">Issue Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="70">Challan No</th>
						<th width="80">Batch No</th>
						<th width="110">Service Company</th>
						<th width="110">Service Location</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="80">Store Name</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$mrr_sql="SELECT a.id,a.company_id, a.issue_number, a.issue_date,a.challan_no,a.knit_dye_source,a.knit_dye_company,a.location_id, b.prod_id,b.batch_id,b.rack_no, b.cutting_unit, c.quantity as quantity,c.color_id,c.po_breakdown_id,a.knit_dye_source,a.knit_dye_company,b.store_id, b.remarks,a.challan_no
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(18,71)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID'  and c.prod_id in( $prod_id ) and c.color_id='$color' and a.issue_date <='$from_date'";
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					foreach($dtlsArray as $row)
					{
						$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
						$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
						$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
						$order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}

					if(!empty($product_id_arr))
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

					if(!empty($batch_id_arr))
					{
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
					}

					if(!empty($batch_id_arr))
					{
						$batch_id_cond = " and b.batch_id in(".implode(",",$batch_id_arr).")";
					}

					$sql_issue="SELECT a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68) $batch_id_cond";
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						$issue_arr[$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
					}

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$rack=$issue_arr[$row[csf('batch_id')]]['rack'];
						$knit_dye_source=$row[csf('knit_dye_source')];
						$knit_dye_company=$row[csf('knit_dye_company')];
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];

						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knit_dye_company')]];
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td align="center"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td align="center"><p><? echo $knitting_company; ?></p></td>
							<td align="center"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
							<td align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td align="left"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $gsm; ?></p></td>
							<td align="center"><p><? echo $width; ?></p></td>
							<td align="right"><p><? echo $row[csf('quantity')]; ?></p></td>
							<td align="left"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_issue_return_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="12" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
		</table>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>
	</fieldset>
	<?
	exit();
} // Issue End

if($action=="issue_popup_receive_return")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	?>
	<fieldset style="width:1000px; margin:0 auto">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>

		<?
		ob_start();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
		?>
		<div style="width:1000px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Receive Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="15">Receive Return Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="">Store Name</th>
					</tr>
				</thead>
				<tbody>
					<?
					$ret_sql="SELECT a.issue_number,a.company_id,a.challan_no,a.issue_date,b.pi_wo_batch_no,b.rack,c.color_id,c.po_breakdown_id, b.prod_id, sum(c.quantity) as quantity,b.store_id
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (46) and c.entry_form in (46)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.prod_id in( $prod_id ) and c.trans_id!=0 group by a.issue_number,a.company_id,a.challan_no,a.issue_date,b.pi_wo_batch_no,c.color_id,c.po_breakdown_id,b.rack, b.prod_id,b.store_id";
					$retDataArray=sql_select($ret_sql);
					foreach($retDataArray as $row)
					{
						$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
						$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
						$batch_id_arr[$row[csf('pi_wo_batch_no')]] = $row[csf('pi_wo_batch_no')];
						$order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}

					if(!empty($product_id_arr))
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

					if(!empty($batch_id_arr))
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");

					if(!empty($batch_id_arr))
					{
						$batch_id_cond = " and b.batch_id in(".implode(",",$batch_id_arr).")";
					}
					$sql_issue="SELECT a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68) $batch_id_cond";

					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						$issue_arr[$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
					}
					//print_r($issue_arr);

					/*$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}*/
					//print_r($grey_used_arr);

					$i=1;

					foreach($retDataArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						$knit_dye_source=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
						$knit_dye_company=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];
							//$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
						$gsm=$issue_arr[$row[csf('pi_wo_batch_no')]]['gsm'];
						$width=$issue_arr[$row[csf('pi_wo_batch_no')]]['width'];
						// $grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
									//$knitting_company=$knit_dye_company;
						}
						else
						{
							$knitting_company=$supplier_name_arr[$knit_dye_company];
									//$knitting_company=$knit_dye_company;
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="right"><p><? echo $gsm; ?></p></td>
							<td align="right"><p><? echo $width; ?></p></td>
							<td  align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>

						</tr>
						<?
						$tot_ret_qty+=$row[csf('quantity')];
						// $tot_grey_qty+=$grey_used_qty;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<!-- <td align="right">&nbsp;<? // echo number_format($tot_grey_qty,2); ?>&nbsp;</td> -->
						<td align="right">&nbsp;<? echo number_format($tot_ret_qty,2); ?>&nbsp;</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
			<?
			$html=ob_get_contents();
			ob_flush();

			foreach (glob(""."*.xls") as $filename)
			{
				@unlink($filename);
			}
			//html to xls convert
			$name=time();
			$name=$user_id."_".$name.".xls";
			$create_new_excel = fopen(''.$name, 'w');
			$is_created = fwrite($create_new_excel,$html);
			?>
			<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
			<script>
				$(document).ready(function(e) {
					document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
				});

			</script>
			<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
		</div>
	</fieldset>
	<?
	exit();
} // issue_popup_receive_return End

if($action=="issue_popup_transfer_out")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	?>
	<fieldset style="width:1000px; margin:0 auto">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>

		<?
		ob_start();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
		?>
		<div style="width:1000px;padding: 10px;" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>

		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Receive Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="1080" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="12">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="100">To Int. Ref.</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="">Store Name</th>
					</tr>
				</thead>
				<tbody>

					<?
					$sql_transfer_out="SELECT a.transfer_system_id, a.transfer_date,b.from_order_id,b.to_order_id,b.batch_id,b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty,c.color_id,c.prod_id,b.from_store as store_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,306) and c.trans_type=6 and c.color_id='$color' and b.from_order_id in($po_id) and c.prod_id in( $prod_id ) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 and a.transfer_date <='$from_date' group by a.transfer_system_id, a.transfer_date,b.from_prod_id,b.from_order_id,b.to_order_id,c.color_id,b.batch_id,c.prod_id,b.from_store";
					//and a.transfer_criteria=4
					$transfer_out=sql_select($sql_transfer_out);
					foreach($transfer_out as $row)
					{
						$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
						$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
						$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
						$order_id_arr[$row[csf('to_order_id')]] = $row[csf('to_order_id')];
					}

					if(!empty($product_id_arr))
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

					if(!empty($batch_id_arr))
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
					if(!empty($batch_id_arr))
					{
						$batch_id_cond = " and b.batch_id in(".implode(",",$batch_id_arr).")";
					}

					$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68) $batch_id_cond";
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						$issue_arr[$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
					}

					if(!empty($order_id_arr))
					{
						$order_id_cond = " b.id in(".implode(",",$order_id_arr).")";
						$sql = "SELECT b.grouping, b.po_number, b.id as to_order_id from wo_po_break_down b where $order_id_cond";
						$from_sty=sql_select($sql);
						$internal_ref_arr = array();
						foreach ($from_sty as $key => $row)
						{
							$internal_ref_arr[$row[csf('to_order_id')]]=$row[csf('grouping')];
						}
					}

					$i=1;
					foreach($transfer_out as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knit_dye_source=$issue_arr[$row[csf('batch_id')]]['knit_dye_source'];
						$knit_dye_company=$issue_arr[$row[csf('batch_id')]]['knit_dye_company'];
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];
						$buyer_id=$issue_arr[$row[csf('batch_id')]]['buyer_id'];

						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$knit_dye_company];
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td><p><? echo $internal_ref_arr[$row[csf('to_order_id')]]; ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td align="center"><p><? echo  $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $gsm; ?></p></td>
							<td align="center"><p><? echo $width; ?></p></td>
							<td align="right" ><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?> &nbsp;</p></td>
							<td align="left"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="10" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</table>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>
	</fieldset>
	<?
	exit();
} // issue_popup_transfer_out End

if($action=="today_issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );

	?>

	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?
	ob_start();
	?>
	<div id="report_id" align="center" style="width:960px">
		<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
				<td> <div id="report_container"> </div> </td>
			</tr>
		</table>
		<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0" align="center">
			<thead>
				<tr>
					<th colspan="10">Today Issue To Cutting Info</th>
				</tr>
				<tr>
					<th width="30">Sl</th>
					<th width="110">Issue No</th>
					<th width="120">Issue to Company</th>
					<th width="100">Challan No</th>
					<th width="100">Issue Date</th>
					<th width="100">Color</th>
					<th width="100">Batch No</th>
					<th width="60">Rack No</th>
					<th width="80">Issue Qnty</th>
					<th width="">Fabric Des.</th>


				</tr>
			</thead>
			<tbody>
				<?
				$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
				$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
				$i=1;
					//echo $from_date;//d.transaction_date='$from_date'
				$mrr_sql="select a.company_id, a.issue_number, a.issue_date,a.challan_no, b.prod_id,b.batch_id,b.rack_no,c.quantity,c.color_id
				from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c ,inv_transaction d
				where a.id=b.mst_id and a.id=d.mst_id and b.id=c.dtls_id and d.id=c.trans_id and a.entry_form in(18,71) and d.transaction_type in(2)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and d.transaction_date='$from_date' and a.company_id='$companyID'  and c.prod_id in( $prod_id ) and c.color_id='$color'";
					//echo $mrr_sql;

				$dtlsArray=sql_select($mrr_sql);

				foreach($dtlsArray as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
						<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
						<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
						<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
						<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
						<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
						<td width="60" ><p><? echo $row[csf('rack_no')]; ?></p></td>
						<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
						<td  align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
					</tr>
					<?
					$tot_qty+=$row[csf('quantity')];
					$i++;
				}
				?>
			</tbody>
			<tfoot>
				<tr class="tbl_bottom">
					<td colspan="8" align="right">Total</td>
					<td align="right"><? echo number_format($tot_qty,2); ?></td>
					<td>&nbsp; </td>
				</tr>
			</tfoot>
		</table>
	</table>

	<?

	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}

			//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);

	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>

	<?
	exit();
} // Today Issue End

if($action=="today_total_issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name");
		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
		?>
		<div style="width:1000px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Receive Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="1200" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="13">Issue Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						

						<th width="80">Batch No</th>
						<th width="110">Service Company</th>
						<th width="110">Service Location</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="">Store Name</th>
					</tr>
				</thead>
				<tbody>
					<?
					$mrr_sql="SELECT a.company_id, a.issue_number, a.issue_date,a.knit_dye_source,a.knit_dye_company,a.location_id,a.challan_no, b.prod_id,b.batch_id,b.rack_no,c.quantity,c.color_id,c.po_breakdown_id,d.store_id,d.issue_challan_no
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c ,inv_transaction d
					where a.id=b.mst_id and a.id=d.mst_id and b.id=c.dtls_id and d.id=c.trans_id and a.entry_form in(18,71) and d.transaction_type in(2)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and d.transaction_date='$from_date' and a.company_id='$companyID'  and c.prod_id in( $prod_id ) and c.color_id='$color'";
					$dtlsArray=sql_select($mrr_sql);
					foreach($dtlsArray as $row)
					{
						$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
						$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
						$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
						$order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}

					if(!empty($product_id_arr))
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

					if(!empty($batch_id_arr))
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
					$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");

					if(!empty($batch_id_arr))
					{
						$batch_id_cond = " and b.batch_id in(".implode(",",$batch_id_arr).")";
					}
					$sql_issue="SELECT a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68) $batch_id_cond";
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						$issue_arr[$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
					}

					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$rack=$issue_arr[$row[csf('batch_id')]]['rack'];

						$knit_dye_source=$row[csf('knit_dye_source')];
						$knit_dye_company=$row[csf('knit_dye_company')];
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];

						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$knit_dye_company];
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $knitting_company; ?></p></td>
							<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td align="center"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>

							<td align="center"><p><? echo $gsm; ?></p></td>
							<td align="center"><p><? echo $width; ?></p></td>
							<td align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
							<td align="left"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="11" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</table>
		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>
	</fieldset>
	<?
	exit();
} // Today Issue End

if($action=="today_issue_popup_receive_return")
{
	//echo "Test";
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);
	//$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );

	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
		?>
		<div style="width:1000px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Receive Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Receive Return Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="">Store Name</th>
					</tr>
				</thead>
				<tbody>
					<?
					$mrr_sql="SELECT a.issue_number,a.company_id,a.challan_no,a.issue_date,b.pi_wo_batch_no,b.rack,c.color_id, b.prod_id, sum(c.quantity) as quantity,c.po_breakdown_id,b.store_id
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (46) and c.entry_form in (46)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and b.transaction_date='$from_date' and c.prod_id in( $prod_id ) and c.trans_id!=0 group by a.issue_number,a.company_id,a.challan_no,a.issue_date,b.pi_wo_batch_no,c.color_id,b.rack, b.prod_id,c.po_breakdown_id,b.store_id";
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					foreach($dtlsArray as $row)
					{
						$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
						$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
						$batch_id_arr[$row[csf('pi_wo_batch_no')]] = $row[csf('pi_wo_batch_no')];
						$order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}

					if(!empty($product_id_arr))
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

					if(!empty($batch_id_arr))
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
					if(!empty($batch_id_arr))
					{
						$batch_id_cond = " and b.batch_id in(".implode(",",$batch_id_arr).")";
					}
					$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68) $batch_id_cond";

					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						$issue_arr[$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
					}
					//print_r($issue_arr);

					/*$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}*/
					//print_r($grey_used_arr);

					$i=1;

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knit_dye_source=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
						$knit_dye_company=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];
						$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
						$gsm=$issue_arr[$row[csf('pi_wo_batch_no')]]['gsm'];
						$width=$issue_arr[$row[csf('pi_wo_batch_no')]]['width'];
						// $grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
							//$knitting_company=$knit_dye_company;
						}
						else
						{
							$knitting_company=$supplier_name_arr[$knit_dye_company];
							//$knitting_company=$knit_dye_company;
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="right"><p><? echo $gsm; ?></p></td>
							<td align="right"><p><? echo $width; ?></p></td>
							<td align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						// $tot_grey_qty+=$grey_used_qty;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<!-- <td align="right">&nbsp;<? // echo number_format($tot_grey_qty,2); ?>&nbsp;</td> -->
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</table>

		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>
	</fieldset>
	<?
	exit();
} // Today Receive Return End

if($action=="today_issue_popup_transfer_out")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		ob_start();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
		?>
		<div style="width:1000px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Receive Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="1080" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="12">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="100">To Int. Ref.</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="">Store Name</th>
					</tr>
				</thead>
				<tbody>
					<?
					$mrr_sql="SELECT a.transfer_system_id, a.transfer_date,a.from_order_id,a.to_order_id,b.batch_id,b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty,c.color_id,c.po_breakdown_id,c.prod_id,b.from_store as store_id
					from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, inv_transaction d
					where a.company_id=$companyID and d.transaction_date='$from_date' and d.id=c.trans_id and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,306) and c.trans_type=6 and c.color_id='$color' and b.from_order_id in($po_id) and c.prod_id in( $prod_id ) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by  a.transfer_system_id, a.transfer_date,b.from_prod_id,a.from_order_id,a.to_order_id,c.color_id,c.po_breakdown_id,b.batch_id,c.prod_id,b.from_store"; //and a.transfer_criteria in (4)

					$dtlsArray=sql_select($mrr_sql);
					foreach($dtlsArray as $row)
					{
						$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
						$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
						$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
						$order_id_arr[$row[csf('to_order_id')]] = $row[csf('to_order_id')];
					}

					if(!empty($product_id_arr))
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

					if(!empty($batch_id_arr))
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
					if(!empty($batch_id_arr))
					{
						$batch_id_cond = " and b.batch_id in(".implode(",",$batch_id_arr).")";
					}


					$sql_issue="SELECT a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68) $batch_id_cond";

					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						$issue_arr[$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
					}

					if(!empty($order_id_arr))
					{
						$order_id_cond = " b.id in(".implode(",",$order_id_arr).")";
						$sql = "SELECT b.grouping, b.po_number, b.id as from_order_id from wo_po_break_down b where $order_id_cond";
						$from_sty=sql_select($sql);
						$internal_ref_arr = array();
						foreach ($from_sty as $key => $row)
						{
							$internal_ref_arr[$row[csf('from_order_id')]]=$row[csf('grouping')];
						}
					}

					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knit_dye_source=$issue_arr[$row[csf('batch_id')]]['knit_dye_source'];
						$knit_dye_company=$issue_arr[$row[csf('batch_id')]]['knit_dye_company'];
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];
						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$knit_dye_company];
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td><p><? echo $internal_ref_arr[$row[csf('to_order_id')]]; ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td align="right"><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td align="right"><p><? echo $gsm; ?></p></td>
							<td align="right"><p><? echo $width; ?></p></td>
							<td align="right" ><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?></p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="10" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</table>

		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>
	</fieldset>
	<?
	exit();
} // Today Transfer Out End

if($action=="woven_issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?
	ob_start();
	$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
	$batch_no_arr=return_library_array( "select id,batch_no from pro_batch_create_mst where id in(".$batchId.")", "id", "batch_no"  );

	?>
	<fieldset style="width:1130px; margin-left:3px">
		<div id="report_id" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1210" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th colspan="11">Issue To Cutting Info</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Issue No</th>
						<th width="120">Issue to Company</th>
						<th width="100">Challan No</th>
						<th width="100">Issue Date</th>
						<th width="100">Color</th>
                        <th width="100">Batch No</th>
                        <!-- <th width="60">Rack No</th> -->
                        <th width="80">Qty</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <th width="">Fabric Des.</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1;
					if($rpt_btn=='btn3')
					{
						$mrr_sql="select x.company_id, x.issue_number, x.challan_no,x.issue_date, sum(x.quantity) as quantity,x.color_id,x.prod_id,x.rack.x.batch_id from(select a.company_id, a.issue_number, a.challan_no,a.issue_date, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.batch_id
	                    from  inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
	                    where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and b.batch_id in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' and b.transaction_date <='$from_date'  group by a.company_id, a.issue_number, a.challan_no,a.issue_date,c.color_id,c.prod_id,b.rack,b.batch_id 
						union all  
						
						select a.company_id, a.transfer_system_id as issue_number, a.challan_no,a. transfer_date as issue_date, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no as batch_id  
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and b.transaction_type=6 and c. trans_type=6 and a.entry_form in (258) and c.entry_form in (258) 
						and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) 
						and a.company_id='$companyID' and b.pi_wo_batch_no in($batchId) and e.product_name_details='$prod_id'  and c.color_id='$color' and b.transaction_date<='$from_date'  
						group by a.company_id, a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no )x group by  x.company_id, x.issue_number, x.challan_no,x.issue_date,x.color_id,x.prod_id,x.rack,x.batch_id order by x.issue_date";
					}
					else
					{
	                    $mrr_sql="select x.company_id, x.issue_number, x.challan_no,x.issue_date, sum(x.quantity) as quantity,x.color_id,x.prod_id,x.rack from(select a.company_id, a.issue_number, a.challan_no,a.issue_date, c.quantity,c.color_id,c.prod_id,b.rack
	                    from  inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
	                    where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.transaction_date <='$from_date'  
						union all  
						
						select a.company_id, a.transfer_system_id as issue_number, a.challan_no,a. transfer_date as issue_date, c.quantity,c.color_id,c.prod_id,b.rack 
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and b.transaction_type=6 and c. trans_type=6 and a.entry_form in (258) and c.entry_form in (258) 
						and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) 
						and a.company_id='$companyID' and e.product_name_details='$prod_id'  and c.color_id='$color' and b.transaction_date<='$from_date'  
						group by a.company_id, a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id,c.prod_id,b.rack , c.quantity)x group by  x.company_id, x.issue_number, x.challan_no,x.issue_date,x.color_id,x.prod_id,x.rack order by x.issue_date";
					}
                    $dtlsArray=sql_select($mrr_sql);
					
					$poIdArr = array();
					foreach($dtlsArray as $row)
                    {
						$poIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
					}
                    //$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$poIdArr).")", "id", "product_name_details"  );

                    $prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where id in(".implode(",",$poIdArr).") and item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}



                    foreach($dtlsArray as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
						//amount calculating
						$amount = ($row[csf('quantity')]*$rate);
                        ?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td align="center" width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <!--<td width="60" ><p><? //echo $row[csf('rack')]; ?></p></td> -->
                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                            <td width="50" align="right" ><p><? echo number_format($rate,2); ?> &nbsp;</p></td>
                            <td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
                            <td><p><? echo $prod_array[$row[csf('prod_id')]]['name_details'];//$product_arr[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $tot_qty+=$row[csf('quantity')];
                        $tot_amount+=number_format($amount,2,'.','');
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="7" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                        <td align="right"></td>
                        <td align="right"><? echo number_format($tot_amount,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                    </tr>
                </tfoot>
            </table>
            
            <table border="1" class="rpt_table" rules="all" width="1210" cellpadding="0" cellspacing="0" align="left">
                <thead>
                    <tr>
                        <th colspan="11">Receive Return To Supplier</th>
                    </tr>
                    <tr>
                        <th width="30">Sl</th>
                        <th width="110">Issue No</th>
                        <th width="120">Issue to Company</th>
                        <th width="100">Challan No</th>
                        <th width="100">Issue Date</th>
                        <th width="100">Color</th>
                    	 <th width="100">Batch No</th>
                        <!--<th width="60">Rack No</th> -->
                        <th width="80">Qty</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <th width="">Fabric Des.</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1;
                    if($rpt_btn=='btn3')
					{
						$mrr_sql_recv_rtrn="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no as batch_id
	                    from  inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
	                    where a.id=b.mst_id and b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and a.entry_form=202 and c.entry_form=202 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and b.pi_wo_batch_no='".$batchId."' and e.product_name_details='$prod_id' and c.color_id='$color' group by a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no order by a.issue_date";
               	 	}
	                else
	                {
	                    $mrr_sql_recv_rtrn="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
	                    from  inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
	                    where a.id=b.mst_id and b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and a.entry_form=202 and c.entry_form=202 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' order by a.issue_date";
	                }
                    $dtlsArray=sql_select($mrr_sql_recv_rtrn);
					$poIdArr = array();
					foreach($dtlsArray as $row)
                    {
						$poIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
					}
                    $product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$poIdArr).")", "id", "product_name_details"  );

                    foreach($dtlsArray as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
						//amount calculating
						$amount = ($row[csf('quantity')]*$rate);
                        ?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td align="center" width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <!--<td width="60" ><p><? //echo $row[csf('rack')]; ?></p></td> -->
                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                            <td width="50" align="right" ><p><? echo number_format($rate,2); ?> &nbsp;</p></td>
                            <td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
                            <td><p><? echo $prod_array[$row[csf('prod_id')]]['name_details'];//$product_arr[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $tot_qty2+=$row[csf('quantity')];
                        $tot_amount2+=number_format($amount,2,'.','');
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="7" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty2,2); ?>&nbsp;</td>
                        <td align="right"></td>
                        <td align="right"><? echo number_format($tot_amount2,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
	<?
    $html=ob_get_contents();
    ob_flush();

    foreach (glob(""."*.xls") as $filename)
    {
        @unlink($filename);
    }
        //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        });

    </script>
    <?
    exit();
}
if($action=="woven_only_issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?
	ob_start();
	$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
	?>
	<fieldset style="width:1130px; margin-left:3px">
		<div id="report_id" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1110" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th colspan="10">Issue To Cutting Info</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Issue No</th>
						<th width="120">Issue to Company</th>
						<th width="100">Challan No</th>
						<th width="100">Issue Date</th>
						<th width="100">Color</th>
                        <!-- <th width="100">Batch No</th>
                        <th width="60">Rack No</th> -->
                        <th width="80">Qty</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <th width="">Fabric Des.</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1;
					
                    $mrr_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.cons_rate
                    from  inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
                    where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.transaction_date <='$from_date' group by a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id,c.color_id,c.prod_id,b.rack,b.cons_rate";
					
                    $dtlsArray=sql_select($mrr_sql);
					
					$poIdArr = array();
					foreach($dtlsArray as $row)
                    {
						$poIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
					}
                    $product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$poIdArr).")", "id", "product_name_details"  );

                    foreach($dtlsArray as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
                        ?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td align="center" width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <!--<td width="100"><p><? //echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60" ><p><? //echo $row[csf('rack')]; ?></p></td> -->
                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                            <td width="50" align="right" ><p><? echo number_format($row[csf('cons_rate')],2); ?> &nbsp;</p></td>
                            <td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
                            <td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $tot_qty+=$row[csf('quantity')];
                        $tot_amount+=number_format($amount,2,'.','');
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="6" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                        <td align="right"></td>
                        <td align="right"><? echo number_format($tot_amount,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
	<?
    $html=ob_get_contents();
    ob_flush();

    foreach (glob(""."*.xls") as $filename)
    {
        @unlink($filename);
    }
        //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        });

    </script>
    <?
    exit();
}
if($action=="woven_only_recv_rtn_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?
	ob_start();
	$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
	?>
	<fieldset style="width:1130px; margin-left:3px">
		<div id="report_id" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		
            <table border="1" class="rpt_table" rules="all" width="1110" cellpadding="0" cellspacing="0" align="left">
                <thead>
                    <tr>
                        <th colspan="10">Receive Return To Supplier</th>
                    </tr>
                    <tr>
                        <th width="30">Sl</th>
                        <th width="110">Issue No</th>
                        <th width="120">Issue to Company</th>
                        <th width="100">Challan No</th>
                        <th width="100">Issue Date</th>
                        <th width="100">Color</th>
                    	<!-- <th width="100">Batch No</th>
                        <th width="60">Rack No</th> -->
                        <th width="80">Qty</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <th width="">Fabric Des.</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1;
                
                    $mrr_sql_recv_rtrn="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
                    from  inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
                    where a.id=b.mst_id and b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and a.entry_form=202 and c.entry_form=202 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color'";
	               
                    $dtlsArray=sql_select($mrr_sql_recv_rtrn);
					$poIdArr = array();
					foreach($dtlsArray as $row)
                    {
						$poIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
					}
                    $product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$poIdArr).")", "id", "product_name_details"  );

                    foreach($dtlsArray as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
						//amount calculating
						$amount = ($row[csf('quantity')]*$rate);
                        ?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td align="center" width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <!--<td width="100"><p><? //echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60" ><p><? //echo $row[csf('rack')]; ?></p></td> -->
                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                            <td width="50" align="right" ><p><? echo number_format($rate,2); ?> &nbsp;</p></td>
                            <td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
                            <td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $tot_qty2+=$row[csf('quantity')];
                        $tot_amount2+=number_format($amount,2,'.','');
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="6" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty2,2); ?>&nbsp;</td>
                        <td align="right"></td>
                        <td align="right"><? echo number_format($tot_amount2,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
	<?
    $html=ob_get_contents();
    ob_flush();

    foreach (glob(""."*.xls") as $filename)
    {
        @unlink($filename);
    }
        //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        });

    </script>
    <?
    exit();
}
if($action=="woven_only_transf_out_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?
	ob_start();
	$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
	?>
	<fieldset style="width:1130px; margin-left:3px">
		<div id="report_id" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1110" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th colspan="10">Transfer Out</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Issue No</th>
						<th width="120">Issue to Company</th>
						<th width="100">Challan No</th>
						<th width="100">Issue Date</th>
						<th width="100">Color</th>
                        <!-- <th width="100">Batch No</th>
                        <th width="60">Rack No</th> -->
                        <th width="80">Qty</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <th width="">Fabric Des.</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1;
					
	                    $mrr_sql="select a.company_id, a.transfer_system_id as issue_number, a.challan_no,a. transfer_date as issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.cons_rate  
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and b.transaction_type=6 and c. trans_type=6 and a.entry_form in (258) and c.entry_form in (258) 
						and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) 
						and a.company_id='$companyID' and e.product_name_details='$prod_id'  and c.color_id='$color' and b.transaction_date<='$from_date'  
						group by a.company_id, a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id,c.prod_id,b.rack,b.prod_id,b.cons_rate";
					
                    $dtlsArray=sql_select($mrr_sql);
					
					$poIdArr = array();
					foreach($dtlsArray as $row)
                    {
						$poIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
					}
                    $product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$poIdArr).")", "id", "product_name_details"  );

                    foreach($dtlsArray as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
                        ?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td align="center" width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <!--<td width="100"><p><? //echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60" ><p><? //echo $row[csf('rack')]; ?></p></td> -->
                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                            <td width="50" align="right" ><p><? echo number_format($row[csf('cons_rate')],2); ?> &nbsp;</p></td>
                            <td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
                            <td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $tot_qty+=$row[csf('quantity')];
                        $tot_amount+=number_format($amount,2,'.','');
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="6" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                        <td align="right"></td>
                        <td align="right"><? echo number_format($tot_amount,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
	<?
    $html=ob_get_contents();
    ob_flush();

    foreach (glob(""."*.xls") as $filename)
    {
        @unlink($filename);
    }
        //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        });

    </script>
    <?
    exit();
}
if($action=="woven_today_issue_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1150px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		$batch_no_arr=return_library_array( "select id,batch_no from pro_batch_create_mst where id in(".$batchId.")", "id", "batch_no"  );

		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>

			
					<?
					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_issue_date=""; else $today_issue_date= "and b.transaction_date='".$from_date."'";
					/*select a.company_id,a.recv_number as issue_number, a.challan_no,a.receive_date as issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id=$prod_id and c.color_id='$color' $today_issue_date */
					if($rpt_btn=='btn3')
					{
						$mrr_issue_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.batch_id 
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id  and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and b.batch_id in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						  group by  a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id,c.color_id,c.prod_id,b.rack,b.batch_id"; 
						
						$mrr_recvRtn_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no as batch_id
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and  a.entry_form=202 and c.entry_form=202 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and b.pi_wo_batch_no in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						group by  a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no"; 
						
						$mrr_transOut_sql="select a.company_id, a.transfer_system_id as issue_number, a.challan_no,a. transfer_date as issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no as batch_id
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and b.transaction_type=6 and c. trans_type=6 and a.entry_form in (258) and c.entry_form in (258) 
						and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) 
						and a.company_id='$companyID' and b.pi_wo_batch_no in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						  group by a.company_id, a.transfer_system_id, a.challan_no,a. transfer_date, b.prod_id,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no";
					}
					else if($rpt_btn=='btnUOM')
					{
						$mrr_issue_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack 
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id  and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						  group by  a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id,c.color_id,c.prod_id,b.rack"; 
						
						$mrr_recvRtn_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and  a.entry_form=202 and c.entry_form=202 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						group by  a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id,c.color_id,c.prod_id,b.rack"; 
						
						$mrr_transOut_sql="select a.company_id, a.transfer_system_id as issue_number, a.challan_no,a. transfer_date as issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack 
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and b.transaction_type=6 and c. trans_type=6 and a.entry_form in (258) and c.entry_form in (258) 
						and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) 
						and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						  group by a.company_id, a.transfer_system_id, a.challan_no,a. transfer_date, b.prod_id,c.color_id,c.prod_id,b.rack";
					}
					else
					{
						$mrr_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id  and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						union all 
						select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and  a.entry_form=202 and c.entry_form=202 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						union all 
						select a.company_id, a.transfer_system_id as issue_number, a.challan_no,a. transfer_date as issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack 
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and b.transaction_type=6 and c. trans_type=6 and a.entry_form in (258) and c.entry_form in (258) 
						and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) 
						and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						group by a.company_id, a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id,c.prod_id,b.rack , c.quantity,b.prod_id";
					}

					if($rpt_btn=='btn3' || $rpt_btn=='btnUOM')
					{

						
						if($rpt_btn=='btn3'){$colspan=7;$colspan2=11;}else{$colspan=6;$colspan2=10;}
									


						?>
						<table border="1" class="rpt_table" rules="all" width="1230" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="<? echo $colspan2; ?>">Issue To Cutting Info</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<?
										if($rpt_btn=='btn3')
										{
											?>
											<th width="100">Batch No</th>
											<?
										}
									?>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray_issue=sql_select($mrr_issue_sql);

								$i=1;
								foreach($dtlsArray_issue as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount = ($row[csf('quantity')]*$rate);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<?
											if($rpt_btn=='btn3')
											{
												?>
													<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
												<?
											}
										?>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($rate); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty_issue+=$row[csf('quantity')];
									$tot_amount_issue+=number_format($amount,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="<? echo $colspan; ?>" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty_issue,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount_issue,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						<table border="1" class="rpt_table" rules="all" width="1230" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="<? echo $colspan2; ?>">Receive Return To Supplier</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<?
										if($rpt_btn=='btn3')
										{
											?>
											<th width="100">Batch No</th>
											<?
										}
									?>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray_recv_rtn=sql_select($mrr_recvRtn_sql);

								$i=1;
								foreach($dtlsArray_recv_rtn as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description_rcvrtn = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount_rcvrtn = ($row[csf('quantity')]*$rate);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<?
											if($rpt_btn=='btn3')
											{
												?>
													<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
												<?
											}
										?>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($rate); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount_rcvrtn,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description_rcvrtn, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty_rcvrtn+=$row[csf('quantity')];
									$tot_amount_rcvrtn+=number_format($amount_rcvrtn,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="<? echo $colspan; ?>" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty_rcvrtn,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount_rcvrtn,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						<table border="1" class="rpt_table" rules="all" width="1230" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="<? echo $colspan2; ?>">Transfer To Supplier</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<?
										if($rpt_btn=='btn3')
										{
											?>
											<th width="100">Batch No</th>
											<?
										}
									?>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray_transOut=sql_select($mrr_transOut_sql);

								$i=1;
								foreach($dtlsArray_transOut as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description_transOut = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount_transOut = ($row[csf('quantity')]*$rate);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<?
											if($rpt_btn=='btn3')
											{
												?>
													<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
												<?
											}
										?>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($rate); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount_transOut,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description_transOut, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty_transOut+=$row[csf('quantity')];
									$tot_amount_transOut+=number_format($amount_transOut,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="<? echo $colspan; ?>" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty_transOut,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount_transOut,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						<?
					}
					else
					{
						?>
						<table border="1" class="rpt_table" rules="all" width="1130" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="10">Issue To Cutting Info</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray=sql_select($mrr_sql);

								$i=1;
								foreach($dtlsArray as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount = ($row[csf('quantity')]*$rate);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($rate); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty+=$row[csf('quantity')];
									$tot_amount+=number_format($amount,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="6" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						<?
					}
					?>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}

	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);

	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}
if($action=="woven_only_today_issue_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1150px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>

			
					<?
					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_issue_date=""; else $today_issue_date= "and b.transaction_date='".$from_date."'";
					/*select a.company_id,a.recv_number as issue_number, a.challan_no,a.receive_date as issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id=$prod_id and c.color_id='$color' $today_issue_date */
					
					$mrr_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.cons_rate 
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
					where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id  and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date group by a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id,c.color_id,c.prod_id,b.rack,b.cons_rate ";					
						?>
						<table border="1" class="rpt_table" rules="all" width="1130" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="10">Issue To Cutting Info</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray=sql_select($mrr_sql);

								$i=1;
								foreach($dtlsArray as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($row[csf('cons_rate')],2); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty+=$row[csf('quantity')];
									$tot_amount+=number_format($amount,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="6" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						<?
					
					?>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}

	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);

	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}
if($action=="woven_only_today_recv_rtn_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1150px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>

			
					<?
					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_issue_date=""; else $today_issue_date= "and b.transaction_date='".$from_date."'";
					/*select a.company_id,a.recv_number as issue_number, a.challan_no,a.receive_date as issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id=$prod_id and c.color_id='$color' $today_issue_date */
					
					$mrr_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack,b.cons_rate 
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
					where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and  a.entry_form=202 and c.entry_form=202 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date ";
						?>
						<table border="1" class="rpt_table" rules="all" width="1130" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="10">Today Receive Return</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray=sql_select($mrr_sql);

								$i=1;
								foreach($dtlsArray as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($row[csf('cons_rate')],2); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty+=$row[csf('quantity')];
									$tot_amount+=number_format($amount,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="6" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						<?
					
					?>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}

	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);

	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}
if($action=="woven_only_today_transf_out_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1150px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>

			
					<?
					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_issue_date=""; else $today_issue_date= "and b.transaction_date='".$from_date."'";
					/*select a.company_id,a.recv_number as issue_number, a.challan_no,a.receive_date as issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id=$prod_id and c.color_id='$color' $today_issue_date */
					
						$mrr_sql="select a.company_id, a.transfer_system_id as issue_number, a.challan_no,a. transfer_date as issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack,b.cons_rate  
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and b.transaction_type=6 and c. trans_type=6 and a.entry_form in (258) and c.entry_form in (258) 
						and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) 
						and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						group by a.company_id, a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id,c.prod_id,b.rack , c.quantity,b.prod_id,b.cons_rate";
					

					
						?>
						<table border="1" class="rpt_table" rules="all" width="1130" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="10">Today Transfer Out</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray=sql_select($mrr_sql);

								$i=1;
								foreach($dtlsArray as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($row[csf('cons_rate')],2); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty+=$row[csf('quantity')];
									$tot_amount+=number_format($amount,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="6" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						<?
					
					?>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}

	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);

	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}
if($action=="knit_stock_popup") //Stock
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);
	//echo $po_id."**".$color;die;
	?>
	<fieldset style="width:570px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$product_arr=return_library_array( "select id, product_name_details from product_details_master where in($prod_id)", "id", "product_name_details");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
		?>

		<div style="width:570px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="100">Year</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
						<th width="110">Fabric Type</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td><? echo $job_no_pre;?></td>
						<td><? echo $job_year;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $grouping;?></td>
						<td><? echo $color_arr[$color];?></td>
						<td><? echo $fabric_type;?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="5"> Stock Details</th>
					</tr>
					<tr>
						<th width="50">Sl</th>
						<th width="80">Product ID</th>
						<th width="150">Batch No</th>
						<th width="80">Qty</th>
						<th>Store Name</th>
					</tr>
				</thead>
				<tbody>
					<?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$iss_return_qnty=array();
					$sql_issue_ret=sql_select("select c.batch_id_from_fissuertn as batch_id,c.rack , d.color_id,d.prod_id, (d.quantity) as issue_ret_qnty  from inv_transaction  c,order_wise_pro_details d where c.id=d.trans_id  and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=52  and  d.po_breakdown_id in($po_id) and d.color_id='$color' and d.prod_id in( $prod_id )");
					foreach( $sql_issue_ret as $row )
					{
						if($row[csf('rack')]=='') $row[csf('rack')]=0;else $row[csf('rack')]=$row[csf('rack')];
						$iss_return_qnty[$row[csf('batch_id')]][$row[csf('rack')]][$row[csf('prod_id')]]['issue_ret_qnty']+=$row[csf('issue_ret_qnty')];
					}
					unset($sql_issue_ret);

					$rec_return_qnty=array();
					$sql_rec_ret=sql_select("select c.batch_id_from_fissuertn as batch_id,c.rack, d.color_id,d.prod_id, (d.quantity) as rec_ret_qnty  from inv_transaction c,order_wise_pro_details d where c.id=d.trans_id  and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=46  and d.po_breakdown_id in($po_id) and d.color_id='$color' and d.prod_id in( $prod_id )");
					foreach( $sql_rec_ret as $row )
					{
						if($row[csf('rack')]=='') $row[csf('rack')]=0;else $row[csf('rack')]=$row[csf('rack')];
						$rec_return_qnty[$row[csf('batch_id')]][$row[csf('rack')]][$row[csf('prod_id')]]['rec_ret_qnty']+=$row[csf('rec_ret_qnty')];
					}
					unset($sql_rec_ret);
					$mrr_sql="SELECT e.id as batch_id, e.batch_no, c.prod_id, c.rack,c.store_id,a.job_no,d.color_id,
					sum(case when d.entry_form in (7,37,66,68) then d.quantity else 0 end) as receive_qnty,
					sum(case when d.entry_form in(14,15,306) and d.trans_type=5 then d.quantity else 0 end) as rec_trns_qnty,
					sum(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty,
					sum(case when d.entry_form in(14,15,306) and d.trans_type=6 then d.quantity else 0 end) as issue_trns_qnty

					from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d, pro_batch_create_mst e
					where  a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.pi_wo_batch_no=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted = 0 and e.status_active = 1 and e.is_deleted=0
					and d.entry_form in (7,37,66,68,14,15,18,71,306) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and c.id=d.trans_id and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id in($po_id) and d.color_id='$color' and d.prod_id in( $prod_id ) and c.transaction_date <='$from_date'
					group by e.id, e.batch_no, c.prod_id,c.rack,c.store_id,a.job_no,d.color_id";

					//echo $mrr_sql;//die;
					$dtlsArray=sql_select($mrr_sql);
					// ===================================  TRANS IN-OUT QNTY ===============================
					$sql_trans="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, d.color_id, d.prod_id,c.store_id,c.pi_wo_batch_no as batch_id,
					(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty,
					   (case when d.entry_form in(14,15,134,306) and d.trans_type=5 then d.quantity else 0 end) as trns_in_qnty 
					from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details d, inv_transaction c,  inv_item_transfer_dtls e 
					where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and c.mst_id=e.mst_id and d.dtls_id=e.id and a.company_name=$companyID and d.entry_form in (14,15,134,306) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and c.transaction_date <='$from_date' and d.po_breakdown_id in($po_id) and d.color_id='$color' and d.prod_id in( $prod_id )
					order by a.job_no, d.color_id,c.transaction_date";
					$sql_trans_res = sql_select($sql_trans);
					$trns_out_qnty_arr = array();
					foreach ($sql_trans_res as $row)
					{
						$trns_out_qnty_arr[$row[csf('batch_id')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]+=$row[csf('trns_out_qnty')];
						$trns_in_qnty_arr[$row[csf('batch_id')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]+=$row[csf('trns_in_qnty')];
					}
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						$rack=$row[csf('rack')];
						$transInQnty=$trns_in_qnty_arr[$row[csf('batch_id')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]];
						$transOutQnty=$trns_out_qnty_arr[$row[csf('batch_id')]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]];
						if($row[csf('rack')]=='') $row[csf('rack')]=0;else $row[csf('rack')]=$row[csf('rack')];
						$issue_ret_qty=$iss_return_qnty[$row[csf('batch_id')]][$row[csf('rack')]][$row[csf('prod_id')]]['issue_ret_qnty'];
						$recv_ret_qty=$rec_return_qnty[$row[csf('batch_id')]][$row[csf('rack')]][$row[csf('prod_id')]]['rec_ret_qnty'];
						$tot_recv_bal=$row[csf('receive_qnty')]+$transInQnty+$issue_ret_qty;
						$tot_issue_bal=$row[csf('issue_qnty')]+$transOutQnty+$recv_ret_qty;
							//echo $tot_recv_bal.'-'.$tot_issue_bal.',';
						$tot_balance=$tot_recv_bal-$tot_issue_bal;
							//if($tot_balance>0)
							//{
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
							<td align="center" title="<? echo $product_arr[$row[csf('prod_id')]];?>"><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('batch_no')]; ?></p></td>

							<td align="right" title="<? echo 'Recv :'.$tot_recv_bal.', '.'Issue :'.$tot_issue_bal;?>"><p><? echo number_format($tot_balance,2); ?></p></td>
							<td align="left"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
						</tr>
						<?

						$tot_qty+=$tot_balance;
						$tot_recv_qty+=$tot_recv_bal;
						$tot_issue_qty+=$tot_issue_bal;
						$i++;
						//}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="3" align="right">Total</td>
						<td align="right" title="<? echo $tot_recv_qty.'='.$tot_issue_qty;?>"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
	</fieldset>
	<?
	exit();
}
if($action=="woven_knit_stock_popup") //Stock
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id."**".$color;die;
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?
	ob_start();
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="report_id" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="8"> Woven Stock Details</th>
					</tr>
					<tr>
						<th width="50">Sl</th>
						<th width="80">Product ID</th>
						<th width="200">Batch No</th>
						<th width="100">Floor</th>
						<th width="100">Room</th>
						<th width="100">Rack</th>
						<th width="100">Shelf</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?

					if($rpt_btn=='btn3')
					{
						$mrr_sql="
						select c.prod_id, b.floor_id, b.room, b.rack, b.self,
							sum(case when c.entry_form in (17) then c.quantity else 0 end) as recv_qnty,
							sum(case when c.entry_form in (19) then c.quantity else 0 end) as issue_qnty,
							 0  as issue_retn_qnty, 
							 0 as recv_rtn_qnty ,0 as finish_fabric_transfer_in,
							 0 as finish_fabric_transfer_out 
						from
							inv_transaction b,
							order_wise_pro_details c,product_details_master e 
						where
							b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and c.entry_form in(17,19,258,209,202) and b.item_category=3 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in (".$po_id.") and c.color_id='".$color."' and b.batch_id in($batchId) and e.product_name_details='".$prod_id."'  and b.transaction_date <='$from_date' 
						group by
							c.prod_id, b.floor_id, b.room, b.rack, b.self 

						union all

						select c.prod_id, b.floor_id, b.room, b.rack, b.self,0 as recv_qnty, 0 as issue_qnty,
							 sum(case when c.entry_form in (209) and b.transaction_type=4 then c.quantity else 0 end) as issue_retn_qnty, 
							 sum(case when c.entry_form in (202) and b.transaction_type=3 then c.quantity else 0 end) as recv_rtn_qnty,
							 sum (case when c.entry_form in (258) and b.transaction_type=5 then c.quantity else 0 end) as finish_fabric_transfer_in, 
							 sum (case when c.entry_form in (258) and b.transaction_type=6 then c.quantity else 0 end) as finish_fabric_transfer_out 
						from
							inv_transaction b,
							order_wise_pro_details c,product_details_master e 
						where
							b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and c.entry_form in(17,19,258,209,202) and b.item_category=3 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in (".$po_id.") and c.color_id='".$color."' and b.pi_wo_batch_no in($batchId) and e.product_name_details='".$prod_id."'  and b.transaction_date <='$from_date' 
						group by
							c.prod_id, b.floor_id, b.room, b.rack, b.self 

							";
					}
					else
					{
						$mrr_sql="
						select c.prod_id, b.batch_id, b.floor_id, b.room, b.rack, b.self,
							sum(case when c.entry_form in (17) then c.quantity else 0 end) as recv_qnty,
							sum(case when c.entry_form in (19) then c.quantity else 0 end) as issue_qnty,
							sum(case when c.entry_form in (209) and b.transaction_type=4 then c.quantity else 0 end) as issue_retn_qnty,
							sum(case when c.entry_form in (202) and b.transaction_type=3 then c.quantity else 0 end) as recv_rtn_qnty,
							sum  (case when c.entry_form in (258) and b.transaction_type=5 then c.quantity else 0 end) as finish_fabric_transfer_in,
							sum (case when c.entry_form in (258) and b.transaction_type=6 then c.quantity else 0 end) as finish_fabric_transfer_out 
						from
							inv_transaction b,
							order_wise_pro_details c,product_details_master e 
						where
							b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and c.entry_form in(17,19,258,209,202) and b.item_category=3 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in (".$po_id.") and c.color_id='".$color."' and e.product_name_details='".$prod_id."'  and b.transaction_date <='$from_date' 
						group by
							c.prod_id, b.batch_id, b.floor_id, b.room, b.rack, b.self";
					}
					
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					if(empty($dtlsArray))
					{
						echo get_empty_data_msg();
						die;
					}
					
					$batchIdArr = array();
					$floorIdArr = array();
					$roomIdArr = array();
					$rackIdArr = array();
					$shelfIdArr = array();
					foreach($dtlsArray as $row)
					{
						if($rpt_btn=='btn3')
						{
							$batchIdArr[$batchId] = $batchId;
						}
						else
						{
							$batchIdArr[$row[csf('batch_id')]] = $row[csf('batch_id')];
						}
						
						$row[csf('floor_id')] = ($row[csf('floor_id')]*1);
						$row[csf('room')] = ($row[csf('room')]*1);
						$row[csf('rack')] = ($row[csf('rack')]*1);
						$row[csf('self')] = ($row[csf('self')]*1);
						
						if($row[csf('floor_id')] != 0)
						{
							$floorIdArr[$row[csf('floor_id')]] = $row[csf('floor_id')];
						}
						
						if($row[csf('room')] != 0)
						{
							$roomIdArr[$row[csf('room')]] = $row[csf('room')];
						}
						
						if($row[csf('rack')] != 0)
						{
							$rackIdArr[$row[csf('rack')]] = $row[csf('rack')];
						}
						
						if($row[csf('self')] != 0)
						{
							$shelfIdArr[$row[csf('self')]] = $row[csf('self')];
						}
					}
					
					//product_name_details
					$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in (".$po_id.") and status_active = 1 and is_deleted = 0", "id", "product_name_details"  );
					
					//pro_batch_create_mst
					$batchCondition = '';
					if(!empty($batchIdArr))
					{
						$batchCondition = " and id in(".implode(",", $batchIdArr).")";
					}
					$batch_no_arr=return_library_array( "select id, batch_no from pro_batch_create_mst where status_active = 1 and is_deleted = 0 ".$batchCondition."",'id','batch_no');

					if($rpt_btn=='btn3')
					{
						$batchName="";
						$batchNameArr=explode(',', $batchId);
						foreach ($batchNameArr as $vall) {
						 	$batchName.=$batch_no_arr[$vall].",";
						 } 
						 $batchName=chop($batchName,",");
					}
					else
					{
						$batchName=$batch_no_arr[$batchId];
					}
					
					//floorSql
					$floorSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					//echo $floorSql;
					$floorDetails = return_library_array( $floorSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					//roomSql
					$roomSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					$roomDetails = return_library_array( $roomSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					//rackSql
					$rackSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.serial_no
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					$rackDetails = return_library_array( $rackSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					/*
					$rackSerialNoSql = "
						SELECT b.floor_room_rack_dtls_id, b.serial_no
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
						GROUP BY b.floor_room_rack_dtls_id, b.serial_no
						ORDER BY b.serial_no ASC
					";
					$rackSerialNoResult = sql_select($rackSerialNoSql);
					foreach($rackSerialNoResult as $row)
					{
						$rackSerialNoArr[$row[csf('floor_room_rack_dtls_id')]] = $row[csf('serial_no')];
					}
					*/
				
					//selfSql
					
					$shelfSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					$shelfDetails = return_library_array( $shelfSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					//binSql
					/*
					$binSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.bin_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
					";
					$binDetails = return_library_array( $binSql, 'floor_room_rack_id', 'floor_room_rack_name');					
					*/
					
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						
						$row[csf('floor_id')] = (($row[csf('floor_id')]*1) == 0 ? '' : $floorDetails[$row[csf('floor_id')]]);
						$row[csf('room')] = (($row[csf('room')]*1) == 0 ? '' : $roomDetails[$row[csf('room')]]);
						$row[csf('rack')] = (($row[csf('rack')]*1) == 0 ? '' : $rackDetails[$row[csf('rack')]]);
						$row[csf('self')] = (($row[csf('self')]*1) == 0 ? '' : $shelfDetails[$row[csf('self')]]);

						$tot_balance=($row[csf('recv_qnty')]+$row[csf('finish_fabric_transfer_in')]+$row[csf('issue_retn_qnty')])-($row[csf('issue_qnty')]+$row[csf('finish_fabric_transfer_out')]+$row[csf('recv_rtn_qnty')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
							<td align="center" title="<? echo $product_arr[$row[csf('prod_id')]];?>"><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $batchName; ?></p></td>
							<td align="center"><p><? echo $row[csf('floor_id')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('room')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('rack')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('self')]; ?></p></td>
							<td align="right"><p><? echo number_format($tot_balance,2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$tot_balance;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();
		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
	</fieldset>
	<?
	exit();
}

if($action=="actual_cut_popup")
{
	echo load_html_head_contents("Actual Cut Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<!--<th width="100">Issue ID</th>-->
					<th width="75">Production Date</th>
					<th width="200">Item Name</th>
					<th width="80">Qty</th>
				</thead>
				<tbody>
					<?
					if($db_type==0) $select_grpby_actual="group by a.id";
					if($db_type==2) $select_grpby_actual=" group by a.id,a.production_date, a.item_number_id,b.color_size_break_down_id, c.color_mst_id";
					else $select_grpby_actual="";
					//$color_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$mrr_sql="select a.id, a.production_date, a.item_number_id, sum(b.production_qnty) as production_qnty, b.color_size_break_down_id, c.color_mst_id
					from  pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.color_number_id='$color' and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and a.po_break_down_id in ($po_id) $select_grpby_actual";
					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<!--  <td width="100"><p><? //echo $row[csf('issue_number')]; ?></p></td>-->
							<td width="75"><p><? echo change_date_format($row[csf('production_date')]); ?></p></td>
							<td width="200" ><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('production_qnty')],0); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('production_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="3" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,0); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
} //actual end

if($action=="woven_actual_cut_popup")
{
	echo load_html_head_contents("Actual Cut Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">

		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<!--<th width="100">Issue ID</th>-->
					<th width="75">Production Date</th>
					<th width="200">Item Name</th>
					<th width="80">Qty</th>
				</thead>
				<tbody>
					<?
					if($db_type==0) $select_grpby_actual="group by a.id";
					if($db_type==2) $select_grpby_actual=" group by a.id,a.production_date, a.item_number_id,b.color_size_break_down_id, c.color_mst_id";
					else $select_grpby_actual="";
					//$color_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$mrr_sql="select a.id, a.production_date, a.item_number_id, sum(b.production_qnty) as production_qnty, b.color_size_break_down_id, c.color_mst_id
					from  pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.color_number_id='$color' and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and a.po_break_down_id in ($po_id) $select_grpby_actual";
					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<!-- <td width="100"><p><? //echo $row[csf('issue_number')]; ?></p></td>-->
							<td width="75"><p><? echo change_date_format($row[csf('production_date')]); ?></p></td>
							<td width="200" ><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('production_qnty')],0); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('production_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="3" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,0); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=166 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id){

		if($id==178)$buttonHtml.='<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />';
		if($id==195)$buttonHtml.='<input type="button" name="search" id="search" value="Show 2" onClick="generate_report(3)" style="width:70px" class="formbutton" />';
		if($id==242)$buttonHtml.='<input type="button" name="search" id="search" value="Show 3" onClick="generate_report(4)" style="width:70px" class="formbutton" />';
		if($id==359)$buttonHtml.='<input title="Similar Show button and Only for WOVEN FABRIC" type="button" name="search" id="search" value="Show 4" onClick="generate_report(5)" style="width:70px" class="formbutton" />';
		if($id==712)$buttonHtml.='<input title="Similar Show button and Only for WOVEN FABRIC and KNIT" type="button" name="search" id="search" value="Show 5" onClick="generate_report(6)" style="width:70px" class="formbutton" />';
		if($id==389)$buttonHtml.='<input title="Only for Knit" type="button" name="search" id="search" value="Show 6" onClick="generate_report(7)" style="width:70px" class="formbutton" />';
		if($id==758)$buttonHtml.='<input title="Only for Knit Mondol Group" type="button" name="search" id="search" value="Show 7" onClick="generate_report(8)" style="width:70px" class="formbutton" />';
		if($id==352)$buttonHtml.='<input type="button" name="search" id="search" value="UOM Wise" onClick="generate_report(2)" style="width:70px" class="formbutton" />';
	}

   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}
?>