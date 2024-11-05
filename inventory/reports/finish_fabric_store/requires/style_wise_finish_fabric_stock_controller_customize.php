<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

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
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
$consumtion_library=return_library_array( "select job_no, avg_finish_cons from wo_pre_cost_fabric_cost_dtls", "job_no", "avg_finish_cons");


if($action=="report_generate")
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
	//echo $cbo_report_type;die;
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
		$item_category_cond = "and item_category_id=2"; // Knit Finish Start
		$booking_qnty=array();

		$sql_booking=sql_select("select b.po_break_down_id as po_id,b.fabric_color_id,b.job_no,b.construction,b.fabric_color_id, (b.fin_fab_qnty ) as fin_fab_qnty  
		from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		foreach( $sql_booking as $row)
		{
			$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
		}
		unset($sql_booking);

	} else {
		$item_category_cond = "and item_category_id=3"; // Woven Finish Start

		$booking_qnty=array(); 
		$booking_qnty_chk= array();
		$sql_booking=sql_select("select b.po_break_down_id as po_id,b.fabric_color_id,b.job_no, (b.fin_fab_qnty ) as fin_fab_qnty, b.id as dtls_id, c.uom from wo_booking_mst a, wo_booking_dtls b , wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		foreach( $sql_booking as $row)
		{
			if($booking_qnty_chk[$row[csf('dtls_id')]] == "")
			{
				$booking_qnty_chk[$row[csf('dtls_id')]]=$row[csf('dtls_id')];
				$booking_qnty[$row[csf('uom')]][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			}
		}
	}

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$product_array=array();
	$sql_product="select id, color, product_name_details from product_details_master where status_active=1 and is_deleted=0 $item_category_cond";
	$sql_product_result=sql_select($sql_product);
	foreach( $sql_product_result as $row )
	{
		$product_array[$row[csf('id')]]['color'] = $row[csf('color')];
		$product_array[$row[csf('id')]]['description'] = $row[csf('product_name_details')];
	}

	if($cbo_report_type==1) 
	{
		$sql_query ="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,
		(case when d.entry_form in (7,37,66,68) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
		(case when d.entry_form in (52,126) and $today_receive_date then d.quantity else 0 end) as today_issue_rtn_qnty,
		(case when d.entry_form in (15,134) and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in_qnty,
		(case when d.entry_form in (7,37,66,68) then d.quantity else 0 end) as receive_qnty,
		(case when d.entry_form in (52,126) then d.quantity else 0 end) as issue_rtn_qnty,
		(case when d.entry_form in (15,134) and d.trans_type=5 then d.quantity else 0 end) as trans_in_qnty,
		
		(case when d.entry_form in (18,71) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty,
		(case when d.entry_form in (46) and $today_receive_date then d.quantity else 0 end) as today_rcv_rtn_qnty,
		(case when d.entry_form in(15,134) and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trns_out_qnty,
		(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty,
		(case when d.entry_form in (46) then d.quantity else 0 end) as issue_rcv_rtn_qnty,
		(case when d.entry_form in(15,134) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty

		from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
		where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and a.company_name=$cbo_company_id and d.entry_form in (7,15,18,37,46,52,66,68,71,126,134) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $receive_date $buyer_id_cond $search_cond $year_cond
		order by a.job_no, d.color_id";
	}
	else 
	{
        $sql_query="Select a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id, $select_fld,
		(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
		(case when d.entry_form in (17) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
		(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty,
		(case when d.entry_form in (19) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty
			
        from  wo_po_details_master a, wo_po_break_down b,inv_transaction c,  order_wise_pro_details d
        where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and  d.trans_id=c.id and d.entry_form in (17,19) and d.trans_id!=0  
        and d.entry_form in (17,19)  and c.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and
        a.company_name=$cbo_company_id $receive_date $buyer_id_cond $search_cond $year_cond
		order by a.job_no,d.color_id";
	}
	
	//echo $sql_query; die;
	$style_wise_arr=array();
	$nameArray=sql_select($sql_query);
	foreach ($nameArray as $row)
	{
		$fab_desc_tmp1=explode(",",$product_array[$row[csf('prod_id')]]['description']);
		$fab_desc_tmp=$fab_desc_tmp1[0];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no']=$row[csf('job_no')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no_pre']=$row[csf('job_no_prefix_num')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['year']=$row[csf('year')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['buyer_name']=$row[csf('buyer_name')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['style_ref_no']=$row[csf('style_ref_no')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_id'].=$row[csf('po_id')].',';
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_no'].=$row[csf('po_no')].',';
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['receive_qnty']+=$row[csf('receive_qnty')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_rtn_qnty']+=$row[csf('issue_rtn_qnty')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['trans_in_qnty']+=$row[csf('trans_in_qnty')];

		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_qnty']+=$row[csf('issue_qnty')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_rcv_rtn_qnty']+=$row[csf('issue_rcv_rtn_qnty')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['trns_out_qnty']+=$row[csf('trns_out_qnty')];

		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
		//$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_rtn_qnty']+=$row[csf('today_issue_rtn_qnty')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_trans_in_qnty']+=$row[csf('today_trans_in_qnty')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_qnty']+=$row[csf('today_issue_qnty')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_rcv_rtn_qnty']+=$row[csf('today_rcv_rtn_qnty')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_trns_out_qnty']+=$row[csf('today_trns_out_qnty')];
		$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['prod_ids'].=$row[csf('prod_id')].',';
	}
	?>
	
	<style type="text/css">
		.nsbreak{word-break: break-all;}
	</style>

	<fieldset style="width:1880px;">
		<table cellpadding="0" cellspacing="0" width="1860">
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
		<table width="1860" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
			<thead>
				<tr>
					<th width="30" rowspan="2">SL</th>
					<th width="100" rowspan="2">Buyer</th>
					<th width="60" rowspan="2">Job</th>
					<th width="50" rowspan="2">Year</th>
					<th width="110" rowspan="2">Style</th> 
				   
					<th width="60" rowspan="2">Order Status</th>
					<th width="110" rowspan="2">Fin. Fab. Color</th>
					<th width="120" rowspan="2">Fabric Type</th>
					
					<th width="80" rowspan="2">Req. Qty</th>
					<th width="240" colspan="3">Today Recv.</th>
					<th width="240" title="Rec.+Issue Ret.+Trans. in" colspan="3">Total Received</th>
				  
					<th width="80" title="Req.-Totat Rec." rowspan="2">Received Balance</th>
					<th width="240" colspan="3">Today Issue</th>
					<th width="240" title="Issue+Rec. Ret.+Trans. out" colspan="3">Total Issued</th>
					<th width="" title="Total Rec.- Total Issue" rowspan="2">Stock</th>
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
		<div style="width:1880px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="1860" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table nsbreak" id="table_body" > 
			<?
		    //  print_r($style_wise_arr); die;
			//echo $sql_query;
			$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$total_today_issue=0;$total_today_recv=0;
		    $fin_color_array=array(); $fin_color_data_arr=array();
			foreach ($style_wise_arr  as $job_key=>$job_val)
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
						//$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['prod_ids']
						$prod_ids=rtrim($val['prod_ids'],',');
						$prod_ids=implode(",",array_filter(array_unique(explode(",",$prod_ids))));
						$color_id=$row[csf("color_id")];
						$fab_desc_type=$desc_key;//$product_arr[$desc_key];
						$po_nos=rtrim($val['po_no'],',');
						$po_nos=implode(",",array_filter(array_unique(explode(",",$po_nos))));
						$poids=rtrim($val['po_id'],',');
						
						$po_ids=array_filter(array_unique(explode(",",$poids)));
						$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
						foreach($po_ids as $po_id)
						{
							$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$desc_key]['fin_fab_qnty']; 
							//$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
							//$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_rcv_rtn_qnty'];
						}
						
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

						//$rec_qty_cal=($val[("receive_qnty")]+$issue_ret_qnty+$val[("rec_trns_qnty")]);
						$rec_qty_cal=($rec_qty+$rec_ret_qty+$rec_trns_qty);

						//$iss_qty=($val[("issue_qnty")]+$val[("issue_trns_qnty")]+$receive_ret_qnty);
						$iss_qty = $val[("issue_qnty")];
						$iss_ret_qty=$val[("issue_rcv_rtn_qnty")];
						$iss_trns_qty=$val[("trns_out_qnty")];
						$iss_qty_cal=($iss_qty+$iss_ret_qty+$iss_trns_qty);	

				?>
				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
					<td width="30"><? echo $i; ?></td>
					<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
					<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
					<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
					<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
				  
					<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
					<td width="110" title="<? echo $color_key;?>"><p><? echo $color_arr[$color_key]; ?></p></td>
					<td width="120" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?></p></td>
					<td width="80" align="right" title="<? echo $job_key."==".$color_key."==".$desc_key."==".$po_ids;?>"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>

					<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','today_total_rec_popup');"><? echo number_format($today_recv,2,'.',''); ?></a><? //echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>
					<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_rtn_popup');"><? echo number_format($today_rtn_qnty,2,'.',''); ?></a><? //echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>
					<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_trans_in_popup');"><? echo number_format($today_trans_in_qnty,2,'.',''); ?></a><? //echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>

					
					<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','total_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
					<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_ret_popup');"><? echo number_format($rec_ret_qty,2,'.',''); ?></a></p></td>
					<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_trns_popup');"><? echo number_format($rec_trns_qty,2,'.',''); ?></a></p></td>
					
					<td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty_cal; echo number_format($rec_bal,2,'.',''); ?></p></td> 
					
					<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_issue_popup');"><? echo number_format($today_issue,2,'.',''); ?></a><? //echo number_format($today_issue,2,'.',''); ?></p></td>
					<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_receive_return');"><? echo number_format($today_issue_rcv_rtn_qnty,2,'.',''); ?></a><? //echo number_format($today_issue,2,'.',''); ?></p></td>
					<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_transfer_out');"><? echo number_format($today_issue_trns_out_qnty,2,'.',''); ?></a><? //echo number_format($today_issue,2,'.',''); ?></p></td>

					
					<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','total_issue_popup');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>
					<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_receive_return');"><? echo number_format($iss_ret_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>
					<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_transfer_out');"><? echo number_format($iss_trns_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>
					
					<td width="" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_rtn_qnty."##".$val[("trans_in_qnty")]."Issue: ".$val[("issue_qnty")]."##".$issue_rcv_rtn_qnty."##".$val[("trns_out_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','knit_stock_popup');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>
					
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
			//echo $total_rec_qty; die;
			
			?>
			</table>
		</div>
		<table width="1860" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
			<tfoot>
				<th width="30"></th>
				<th width="100"></th>
				<th width="60"></th>
				<th width="50"></th>
				<th width="110">&nbsp;</th>
			   
				<th width="60">&nbsp;</th>
				<th width="110">&nbsp;</th>
				<th width="120">Total</th>
				<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
				<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?></th>
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
			  
				<th width="" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
			   
			</tfoot>
		</table>   
	</fieldset>         
	<?
	

	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
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
	//$cbo_uom=str_replace("'","",$cbo_uom);
	//if($cbo_uom) $uom_cond = " and c.cons_uom in ($cbo_uom) "; else $uom_cond = "";
	//echo $cbo_report_type;die;
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
		$sql_booking=sql_select("select b.po_break_down_id as po_id,b.fabric_color_id,b.job_no,b.construction, (b.fin_fab_qnty ) as fin_fab_qnty,c.uom,b.id as dtls_id from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and a.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
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
            <fieldset style="width:1290px;">
                <table cellpadding="0" cellspacing="0" width="1290">
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
                <table width="1270" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
                    <thead>
                        <th width="30">SL</th>
                        <th width="100">Buyer</th>
                        <th width="60">Job</th>
                        <th width="50">Year</th>
                        <th width="110">Style</th> 
                       
                        <th width="60">Order Status</th>
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
                <div style="width:1290px; max-height:350px; overflow-y:scroll;" id="scroll_body">
                    <table width="1270" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
                    <?
					
                    
						$sql_query="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id,$select_fld,d.color_id,d.prod_id,
                        (case when d.entry_form in (7,37,66,68) then d.quantity else 0 end) as receive_qnty,
						(case when d.entry_form in (7,37,66,68) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
                        (case when d.entry_form=15 and d.trans_type=5 then d.quantity else 0 end) as rec_trns_qnty,
						(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty,
						(case when d.entry_form in (18,71) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty,
                        (case when d.entry_form=15 and d.trans_type=6 then d.quantity else 0 end) as issue_trns_qnty
                        from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d, pro_batch_create_mst e
                        where  a.company_name=$cbo_company_id and a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.pi_wo_batch_no=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted = 0 and e.status_active = 1 and e.is_deleted=0
                        and d.entry_form in (7,37,66,68,15,18,71) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and c.id=d.trans_id and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0  $receive_date $buyer_id_cond $search_cond $year_cond $productId_cond
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
							//$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_qnty']+=$row[csf('today_issue_qnty')];
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['prod_ids'].=$row[csf('prod_id')].',';
						}
                  //echo "<pre>";  print_r($style_wise_arr); die;
                    //echo $sql_query;die;
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
									//$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['prod_ids']
									$prod_ids=rtrim($val['prod_ids'],',');
									$color_id=$row[csf("color_id")];
									$fab_desc_type=$desc_key;//$product_arr[$desc_key];
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
									
									//echo "=".$cons_uom. "* $booking_qnty[$cons_uom][$job_key][$po_id][$color_key][$desc_key]";die;
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
			                            <td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
			                            <td width="120" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?></p></td>
			                            <td width="50" title="uom"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
			                            <td width="80" align="right" title="<? echo "[$cons_uom][$job_key][$po_id][$color_key][$desc_key]";?>"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
			                            <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','today_receive_popup');"><? echo number_format($today_recv,2,'.',''); ?></a><? //echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>
			                            <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
			                            
			                            <td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty; echo number_format($rec_bal,2,'.',''); ?></p></td> 
			                             <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 3; ?>','today_issue_popup');"><? echo number_format($today_issue,2,'.',''); ?></a><? //echo number_format($today_issue,2,'.',''); ?></p></td>
			                            <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','issue_popup');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>
			                            
			                            <td width="" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','knit_stock_popup');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>
			                            
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
                <table width="1270" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
                    <tfoot>
                        <th width="30"></th>
                        <th width="100"></th>
                        <th width="60"></th>
                        <th width="50"></th>
                        <th width="110">&nbsp;</th>
                       
                        <th width="60">&nbsp;</th>
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
		$sql_product="select id, color from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0";
		$sql_product_result=sql_select($sql_product);
		foreach( $sql_product_result as $row )
		{
			$product_array[$row[csf('id')]]=$row[csf('color')];
		}

		$issue_qnty=array();
		$sql_issue=sql_select(" select b.po_breakdown_id,b.color_id, sum(b.quantity) as issue_qnty  from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.issue_purpose in(4,9) and b.entry_form=19 group by b.po_breakdown_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		} //var_dump($issue_qnty);
		
		$booking_qnty=array(); $booking_qnty_chk= array();
		$sql_booking=sql_select("select b.po_break_down_id as po_id,b.fabric_color_id,b.job_no, (b.fin_fab_qnty ) as fin_fab_qnty, b.id as dtls_id, c.uom from wo_booking_mst a, wo_booking_dtls b , wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		foreach( $sql_booking as $row)
		{
			if($booking_qnty_chk[$row[csf('dtls_id')]] == "")
			{
				$booking_qnty_chk[$row[csf('dtls_id')]]=$row[csf('dtls_id')];
				$booking_qnty[$row[csf('uom')]][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			}
		}

		/*echo "<pre>";
		print_r($booking_qnty);*/

		unset($sql_booking);
		
			ob_start();
			$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		?>
            <fieldset style="width:1400px;">
                <table cellpadding="0" cellspacing="0" width="1260">
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
                  <table width="1370" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
                  <thead>              	
                    <th width="30">SL</th>
                        <th width="100">Buyer</th>
                        <th width="60">Job</th>
                        <th width="50">Year</th>
                        <th width="110">Style</th> 
                       
                        <th width="60">Order Status</th>
                        <th width="110">Fin. Fab. Color</th>
                        <th width="220">Fab. Desc.</th>
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
                    if($db_type==0) $prod_cond="group_concat(e.prod_id) as prod_id"; 
                    else if($db_type==2) $prod_cond="listagg(cast(e.prod_id as varchar2(4000)),',') within group (order by e.prod_id) as prod_id";
                    
                    $sql_query="Select a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id, $select_fld, 
					(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
					(case when d.entry_form in (17) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
					(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty,
					(case when d.entry_form in (19) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty
						
                    from  wo_po_details_master a, wo_po_break_down b,inv_transaction c,  order_wise_pro_details d
                    where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and  d.trans_id=c.id and d.entry_form in (17,19) and d.trans_id!=0  
                    and d.entry_form in (17,19)  and c.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and
                    a.company_name=$cbo_company_id $receive_date $buyer_id_cond $search_cond   $year_cond
					order by a.job_no,d.color_id";
					
					$style_wise_arr=array();
					$nameArray=sql_select($sql_query);
					  foreach ($nameArray as $row)
                   	 	{
                   	 		if($uomFromProductArr[$row[csf('prod_id')]] == "") continue;
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['job_no']=$row[csf('job_no')];
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['year']=$row[csf('year')];
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['buyer_name']=$row[csf('buyer_name')];
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['style_ref_no']=$row[csf('style_ref_no')];
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['po_id'].=$row[csf('po_id')].',';
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['po_no'].=$row[csf('po_no')].',';
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['receive_qnty']+=$row[csf('receive_qnty')];
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['issue_qnty']+=$row[csf('issue_qnty')];
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
							$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];
						}
                  
                    $i=1; 

                    foreach ($style_wise_arr as $cons_uom => $cons_uom_data) 
                   	{
                   		$sub_req_qty=0;$sub_today_recv=$sub_rec_qty=$sub_rec_bal=$sub_today_issue=$sub_issue_qty=$sub_stock=0;
	                    foreach ($cons_uom_data  as $job_key=>$job_val)
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
									$fab_desc_type=$product_arr[$desc_key];
									$po_nos=rtrim($val['po_no'],',');
									$po_nos=implode(",",array_unique(explode(",",$po_nos)));
									$poids=rtrim($val['po_id'],',');
									
									$po_ids=array_unique(explode(",",$poids));
									$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
									foreach($po_ids as $po_id)
									{
										 $book_qty+=$booking_qnty[$cons_uom][$job_key][$po_id][$color_key]['fin_fab_qnty']; 
										//echo $job_key.'ii'.$po_id;
										$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
										$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
									}
									
									//echo $job_key.'ii';
									$po_ids=implode(",",$po_ids);
									$today_recv=$val[("today_receive_qnty")];
									$today_issue=$val[("today_issue_qnty")];
									$rec_qty=($val[("receive_qnty")]);
									$rec_qty_cal=($val[("receive_qnty")]);
									
									$iss_qty=($val[("issue_qnty")]);
									$iss_qty_cal=($val[("issue_qnty")]);
		                        	?>
			                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
			                            <td width="30"><? echo $i; ?></td>
			                            <td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
			                            <td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
			                            <td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
			                            <td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
			                          
			                            <td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
			                            <td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
			                            <td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $fab_desc_type; ?></p></td>
			                            <td width="50"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
			                            <td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
			                            <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup');"><? //echo number_format($today_recv,2,'.',''); ?></a><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>
			                            <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
			                            
			                            <td width="80" align="right"><p><? $rec_bal=$book_qty-$rec_qty; echo number_format($rec_bal,2,'.',''); ?></p></td> 
			                             <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 8; ?>','woven_today_issue_popup');"><? //echo number_format($today_issue,2,'.',''); ?></a><? echo number_format($today_issue,2,'.',''); ?></p></td>
			                            <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>
			                            
			                            <td width="" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 5; ?>','woven_knit_stock_popup');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>
			                            
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
						?>
							<tr style="font-weight:bold;background-color: #e0e0e0">
								<td colspan="9" align="right">UOM Total</td>
								<td align="right"><? echo number_format($sub_req_qty,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_today_recv,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_rec_qty,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_rec_bal,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_today_issue,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_issue_qty,2,'.','') ?></td>
								<td align="right"><? echo number_format($sub_stock,2,'.','') ?></td>
							</tr>
						<?
					}
                	?>
                    </table>
                    <table width="1370" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
                        <tfoot>
                        <th width="30"></th>
                        <th width="100"></th>
                        <th width="60"></th>
                        <th width="50"></th>
                        <th width="110">&nbsp;</th>
                       
                        <th width="60">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="220">&nbsp;</th>
                        <th width="50">Total</th>
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
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
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
                            <td width="" align="center"><p><? echo $shipment_status[$row[csf('shiping_status')]]; ?></p></td>
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

					$sql_transfer_out=" select a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,a.from_order_id,a.to_order_id,b.rack,b.batch_id,c.color_id,sum(b.transfer_qnty) as transfer_out_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=3 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=15 and c.trans_type=5 and c.color_id='2' and a.transfer_criteria=4 and a.to_order_id in($po_id) and c.prod_id in ( $prod_id ) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.transfer_system_id, a.challan_no,a.transfer_date,a.from_order_id, b.uom, b.from_prod_id,b.from_order_id,b.to_order_id,b.rack,b.batch_id,c.color_id,a.to_order_id";

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
}//Knit Finish end
if($action=="total_receive_popup")
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
                    	<th colspan="20">Receive Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th>
                        <th width="70">Receive Date</th>
                        <th width="80">Dyeing Source</th>
                        <th width="110">Dyeing Company</th>
                        <th width="100">Challan No</th>

                        <th width="80">Order No</th>
	                    <th width="80">Style</th>
	                    <th width="80">Buyer</th>

                        <th width="80">Color</th>
                        <th width="80">Batch No</th>
                        <th width="60">Rack No</th> 
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>

                        <th width="80">Grey Qty.</th>
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

                            <td width="80"><p><? echo $row[csf('order_id')]; ?></p></td>
                            <td width="80"><p><? echo $po_number_no_arr[$row[csf('order_id')]]; ?></p></td>
                            <td width="80"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>

                            <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60"><p><? echo $row[csf('rack_no')]; ?></p></td>
                            <td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $row[csf('width')]; ?></p></td>

                            <td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? echo number_format($process_loss,2); ?></p></td>
                            <td width="60" align="center"><p><? echo $row[csf('emp_id')]; ?></p></td>
                            <td align="center"><p><? echo $row[csf('qc_name')]; ?></p></td>
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
                    	<td colspan="12" align="right">Total</td>
                         <td align="right"><? echo number_format($tot_grey_qty,2); ?> </td>
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
}//Knit Finish end
if($action=="receive_ret_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');
	
	extract($_REQUEST);
	?>
     
	<fieldset style="width:1100px; margin-left:3px">
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
     <div style="width:1100px;" align="center">
    		<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
            <tr>
            <td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
             <td> <div id="report_container"> </div> </td>
            </tr>
             </table>
        </div>
		<div id="scroll_body" align="center">			
            <table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="14">Issue Return Details</th>
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
                        <th width="80">Fabric Des.</th>

                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th width="80">Grey Qty.</th>
                        <th width="80">Ret. Qty</th>
                       
                    </tr>
				</thead>
                <tbody>
                <?
                		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
						$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
						$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");

						$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";	
						$result_issue=sql_select($sql_issue);
						$issue_arr=array();
						foreach($result_issue as $row)
						{
							$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
							$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
							$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
							$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
							$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						}
						//print_r($issue_arr);

						$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
						$dtlsgrey=sql_select($grey_sql);
						$grey_used_arr=array();
						foreach($dtlsgrey as $row)
						{
							$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
						}
						//print_r($grey_used_arr);

						$i=1;
						//and a.entry_form in (7,37,66,68)
						$ret_sql="select a.recv_number,a.challan_no,a.issue_id, a.receive_date,b.prod_id,b.pi_wo_batch_no, sum(c.quantity) as quantity,sum(c.returnable_qnty) as returnable_qnty,c.color_id 
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c 
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
						$gsm=$issue_arr[$row[csf('pi_wo_batch_no')]]['gsm'];
						$width=$issue_arr[$row[csf('pi_wo_batch_no')]]['width'];
						$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
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
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td> 
                            <td width="80"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="100"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
                            <td width="120" ><p><? echo $knitting_company; ?></p></td>
                            <td width="100" ><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td width="100" align="right"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100" align="right"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo $row[csf('Rack')]; ?></p></td>                             
                            <td width="80" align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo $gsm; ?></p></td>
							<td width="80" align="right"><p><? echo $width; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>

                            <td width="80" align="right"><p><? echo $row[csf('quantity')]; ?></p></td>
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
                    	<td colspan="12" align="right">Total</td>
                        <td align="right">&nbsp;<? echo number_format($tot_grey_qty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
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
	?>
     
	<fieldset style="width:1300px; margin-left:3px">
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
     <div style="width:1300px;" align="center">
    		<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
            <tr>
            <td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
             <td> <div id="report_container"> </div> </td>
            </tr>
             </table>
        </div>
		<div id="scroll_body" align="center">
            
                <table border="1" class="rpt_table" rules="all" width="1300" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="17">Transfer In Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th> 
                        <th width="80">Receive Date</th>
                        <th width="100">Dyeing Source</th>
                        <th width="120">Dyeing Company</th>
                        <th width="100">From Order</th>
                        <th width="100">Color</th> 
                        <th width="100">Buyer</th> 

                        <th width="100">Trns. From Style</th> 
                        <th width="100">Trns. To Style</th> 

                        <th width="100">Batch No</th>
                        <th width="80">Rack</th>
                        <th width="80">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th width="80">Grey Qty.</th>
                        <th width="80">Fin. Rcv. Qty.</th>

                    </tr>
				</thead>
                <tbody>
                <?

                	    $product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
						$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
						$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
						$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");

						$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";	
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

						$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
						$dtlsgrey=sql_select($grey_sql);
						$grey_used_arr=array();
						foreach($dtlsgrey as $row)
						{
							$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
						}
						//print_r($grey_used_arr);
				
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					//$sql_transfer_out="select a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,a.from_order_id,a.to_order_id,b.rack,b.batch_id,c.color_id,sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=15 and c.trans_type=5 and c.color_id='$color' and a.transfer_criteria=4 and a.to_order_id in($po_id) and c.prod_id in ( $prod_id )  and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by  a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,b.from_order_id,b.to_order_id,b.rack,b.batch_id,c.color_id";

					$sql_transfer_out=" select a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,a.from_order_id,a.to_order_id,b.rack,b.batch_id,c.color_id,sum(b.transfer_qnty) as transfer_out_qnty 
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c 
					where a.company_id='$companyID' and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(15,134) and c.trans_type=5 and c.color_id=$color and a.entry_form in(15,134) and a.to_order_id in($po_id) and c.prod_id in ($prod_id ) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 
					group by a.transfer_system_id, a.challan_no,a.transfer_date,a.from_order_id, b.uom, b.from_prod_id,b.from_order_id,b.to_order_id,b.rack,b.batch_id,c.color_id,a.to_order_id";

					$transfer_out=sql_select($sql_transfer_out);
					
					foreach($transfer_out as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						//$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];	
						$knit_dye_source=$issue_arr[$row[csf('batch_id')]]['knit_dye_source'];	
						$knit_dye_company=$issue_arr[$row[csf('batch_id')]]['knit_dye_company'];	
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];
						$buyer_id=$issue_arr[$row[csf('batch_id')]]['buyer_id'];
						$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
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
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="80"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td width="120"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
                            <td width="100" ><p><? echo $knitting_company; ?></p></td>
                            <td width="100"><p><? echo $row[csf('from_order_id')]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>

                            <td width="100"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
                            <td width="100"><p><? echo $po_number_no_arr[$row[csf('from_order_id')]]; ?></p></td>
                            <td width="100"><p><? echo $po_number_no_arr[$row[csf('to_order_id')]]; ?></p></td>

                            <td width="100" ><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="80" ><p><? echo $row[csf('rack')]; ?> &nbsp;</p></td>                            
                            <td width="50" ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
                            <td width="50" align="right"><p><? echo $gsm; ?></p></td>
							<td width="80" align="right"><p><? echo $width; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>

                            <td width="80" align="right" ><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?> &nbsp;</p></td>
                        </tr>
						<?
						$tot_issue_return_qty+=$row[csf('transfer_out_qnty')];
						$tot_grey_qty+=$grey_used_qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="15" align="right">Total</td>
                        <td align="right">&nbsp;<? echo number_format($tot_grey_qty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
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

if($action=="today_total_rec_popup")
{
	echo load_html_head_contents("Today Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1725px; margin-left:3px">
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
                    	<th colspan="20">Receive Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th>
                        <th width="70">Receive Date</th>
                        <th width="80">Dyeing Source</th>
                        <th width="110">Dyeing Company</th>
                        <th width="100">Challan No</th>
                        <th width="80">Order No</th>
                        <th width="80">Style</th>
                        <th width="80">Buyer</th>
                        <th width="80">Color</th>
                        <th width="80">Batch No</th>
                        <th width="60">Rack No</th> 
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>

                        <th width="80">Grey Qty.</th>
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
					//;  	d.transaction_date='$from_date'  inv_transaction 
						$mrr_sql="select a.recv_number, a.booking_no,a.receive_date,a.knitting_source,a.knitting_company,a.challan_no, b.prod_id,b.batch_id,b.order_id,b.buyer_id,b.gsm,b.width,sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty,c.color_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c,inv_transaction d
						 where a.id=b.mst_id and b.id=c.dtls_id and d.id=c.trans_id and  a.id=d.mst_id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id in( $prod_id ) and c.color_id='$color' and c.trans_id!=0 and c.trans_type=1 and d.transaction_date='$from_date' group by a.recv_number, a.receive_date,a.booking_no,b.prod_id,b.order_id,b.buyer_id,b.gsm,b.width,c.color_id,a.knitting_source,a.knitting_company,a.challan_no,b.batch_id";
					
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

                            <td width="80"><p><? echo $row[csf('order_id')]; ?></p></td>
                            <td width="80"><p><? echo $po_number_no_arr[$row[csf('order_id')]]; ?></p></td>
                            <td width="80"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>

                            <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60"><p><? echo $row[csf('rack_no')]; ?></p></td>
                            <td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $row[csf('width')]; ?></p></td>

                            <td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? echo number_format($process_loss,2); ?></p></td>
                            <td width="60" align="center"><p><? echo $row[csf('emp_id')]; ?></p></td>
                            <td align="center"><p><? echo $row[csf('qc_name')]; ?></p></td>
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
                    	<td colspan="15" align="right">Total</td>
                         <td align="right"><? echo number_format($tot_grey_qty,2); ?> </td>
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
}//Today total_rec_popup end
if($action=="today_total_rtn_popup")
{
	echo load_html_head_contents("Today Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1100px; margin-left:3px">
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
     <div style="width:1100px;" align="center">
    		<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
            <tr>
            <td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
             <td> <div id="report_container"> </div> </td>
            </tr>
             </table>
        </div>
		<div id="scroll_body" align="center">			
            <table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="14">Issue Return Details</th>
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
                        <th width="80">Fabric Des.</th>

                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th width="80">Grey Qty.</th>
                        <th width="80">Ret. Qty</th>
                       
                    </tr>
				</thead>
                <tbody>
                <?
                		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
						$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
						$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");
                		
						$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";

						$result_issue=sql_select($sql_issue);
						$issue_arr=array();
						foreach($result_issue as $row)
						{
							$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
							$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
							$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
							$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
							$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						}
						//print_r($issue_arr);

						$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
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


					$mrr_sql="select a.recv_number,a.challan_no,a.issue_id, a.receive_date,b.prod_id,b.pi_wo_batch_no, sum(c.quantity) as quantity,sum(c.returnable_qnty) as returnable_qnty,c.color_id 
					 from inv_receive_master a, inv_transaction b, order_wise_pro_details c
					 where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (52,126) and c.entry_form in (52,126) and a.item_category=2 and a.receive_date='$from_date' and
					 a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id in ( $prod_id ) and c.trans_id!=0 
					 group by a.recv_number,a.challan_no, a.receive_date,b.prod_id,a.issue_id,b.pi_wo_batch_no,c.color_id";

					//echo $mrr_sql;
					
					$dtlsArray=sql_select($mrr_sql);
					
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
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td> 
                             <td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80"><p><? echo  $knitting_source[$knitting_source_id]; ?></p></td>
                            <td width="110"><p><? echo $knitting_company; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                             <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                             <td width="80"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
                             <td width="60"><p><? echo $rack; ?></p></td>
                             
                            <td width="" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo $gsm; ?></p></td>
							<td width="80" align="right"><p><? echo $width; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                           
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
                    <td colspan="12" align="right">Total</td>
                    <td align="right">&nbsp;<? echo number_format($tot_grey_qty,2); ?>&nbsp;</td>
                    <td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
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
	?>
	<fieldset style="width:1100px; margin-left:3px">
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
     	<div style="width:1200px;" align="center">
    		<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
            <tr>
            <td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
             <td> <div id="report_container"> </div> </td>
            </tr>
             </table>
        </div>
		<div id="scroll_body" align="center">            
                <table border="1" class="rpt_table" rules="all" width="1200" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="15">Today Transfer In Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th> 
                        <th width="80">Receive Date</th>
                        <th width="100">Dyeing Source</th>
                        <th width="120">Dyeing Company</th>
                        <th width="100">From Order</th>
                        <th width="100">Buyer</th> 
                        <th width="100">Color</th> 
                        <th width="100">Batch No</th>
                        <th width="80">Rack</th>
                        <th width="80">Fabric Des.</th>

                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th width="80">Grey Qty.</th>
                        <th width="80">Fin. Rcv. Qty.</th>
                    </tr>
				</thead>
                <tbody>
                <?

                	$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");

					$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");

					$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.order_id,b.buyer_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";	
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
					//print_r($issue_arr);

					$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}
					//print_r($grey_used_arr);
				
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					//$sql_transfer_out="select a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,a.from_order_id,a.to_order_id,b.rack,b.batch_id,c.color_id,sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=15 and c.trans_type=5 and c.color_id='$color' and a.transfer_criteria=4 and a.to_order_id in($po_id) and c.prod_id in ( $prod_id )  and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by  a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,b.from_order_id,b.to_order_id,b.rack,b.batch_id,c.color_id";

					$sql_transfer_out=" select a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,a.from_order_id,a.to_order_id,b.rack,b.batch_id,c.color_id,sum(b.transfer_qnty) as transfer_out_qnty 
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c 
					where a.company_id='$companyID' and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(15,134) and c.trans_type=5 and c.color_id=$color and a.entry_form in(15,134) and a.to_order_id in($po_id) and c.prod_id in ($prod_id ) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 and a.transfer_date='$from_date'
					group by a.transfer_system_id, a.challan_no,a.transfer_date,a.from_order_id, b.uom, b.from_prod_id,b.from_order_id,b.to_order_id,b.rack,b.batch_id,c.color_id,a.to_order_id";

					$transfer_out=sql_select($sql_transfer_out);
					
					foreach($transfer_out as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];	
						$knit_dye_source=$issue_arr[$row[csf('batch_id')]]['knit_dye_source'];	
						$knit_dye_company=$issue_arr[$row[csf('batch_id')]]['knit_dye_company'];	
						

						$buyer_id=$issue_arr[$row[csf('batch_id')]]['buyer_id'];
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];
						$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
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
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">							
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="80"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
                            
                            <td width="120"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
                            <td width="100" ><p><? echo $knitting_company; ?></p></td>   
                            <td width="100"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100" ><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="80" ><p><? echo $row[csf('rack')]; ?> &nbsp;</p></td>                            
							<td width="100"><p><? echo $po_number_no_arr[$row[csf('from_order_id')]]; ?></p></td>
                            <td width="50" ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>

                            <td width="50" align="right"><p><? echo $gsm; ?></p></td>
							<td width="80" align="right"><p><? echo $width; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>

                            <td width="80" align="right" ><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?> &nbsp;</p></td>
                        </tr>
						<?
						$tot_issue_return_qty+=$row[csf('transfer_out_qnty')];
						$tot_grey_qty+=$grey_used_qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="13" align="right">Total</td>
                        <td align="right">&nbsp;<? echo number_format($tot_grey_qty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
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
if($action=="woven_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1565px; margin-left:3px">
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
		<div id="scroll_body" align="center">
        <table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
                <tr>
                    <td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
                     <td> <div id="report_container"> </div> </td>
                </tr>
             </table>
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
                        <th width="80">Grey Qty.</th>
                        <th width="80">Fin. Rcv. Qty.</th>
                        <th width="70">Process Loss Qty.</th>
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
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
					$dtlsbook=sql_select($book_sql);
					$booking_arr=array();
					foreach($dtlsbook as $row)
					{
						$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
					}
					
					$i=1;
					
					$mrr_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, sum(c.quantity) as quantity
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c 
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id=$prod_id and c.color_id='$color' group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no";
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
						$prod_desc=explode(",",$product_arr[$row[csf('prod_id')]]);
						$gsm=$prod_desc[4];
						$dia=$prod_desc[5];
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
                             <td width="60"><p><? echo $row[csf('rack')]; ?></p></td>
                             <td width="80" align="right"><p><? echo number_format($booking_qty,2); ?></p></td>
                             <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                             <td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? echo number_format($process_loss); ?></p></td>
                             <td width="60" align="center"><p><? echo $row[csf('emp_id')]; ?></p></td>
                              <td width="80" align="center"><p><? echo $row[csf('qc_name')]; ?></p></td>
                           
                            <td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $dia; ?></p></td>
                            <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
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
                         <td align="right"><? echo number_format($tot_booking_qty,2); ?> </td>
                          <td align="right"><? echo number_format($tot_qty,2); ?> </td>
                        <td colspan="5"> </td>
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
		
					$sql_transfer_out="select a.transfer_system_id, a.transfer_date,a.from_order_id,a.to_order_id,b.batch_id,b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty,c.color_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=15 and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and c.prod_id in( $prod_id ) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by  a.transfer_system_id, a.transfer_date,b.from_prod_id,a.from_order_id,a.to_order_id,c.color_id,b.batch_id";
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
	?>
    <fieldset style="width:1100px; margin-left:3px">
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
		<div style="width:1100px;" align="center">
    		<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
            <tr>
            <td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
             <td> <div id="report_container"> </div> </td>
            </tr>
             </table>
        </div>
		<div id="scroll_body" align="center">			
            <table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="12">Issue Details</th>
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
                        <!-- <th width="80">Rack</th> -->
                        <th width="80">Fabric Des.</th>

                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <!--<th width="80">Grey Qty.</th>-->
                        <th width="80">Ret. Qty</th>
                       
                    </tr>
				</thead>
               <tbody>
                <?
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");

					$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";	
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
					}
					//print_r($issue_arr);

					$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}
					//print_r($grey_used_arr);
					$i=1;
					$mrr_sql="select a.id,a.company_id, a.issue_number, a.issue_date,a.challan_no, b.prod_id,b.batch_id,b.rack_no, b.cutting_unit, c.quantity,c.color_id
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(18,71)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID'  and c.prod_id in( $prod_id ) and c.color_id='$color'";
					//echo $mrr_sql;
					
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$rack=$issue_arr[$row[csf('batch_id')]]['rack'];	
						//echo $row[csf('batch_id')].'='.$batch_no_arr[$row[csf('batch_id')]];
						$knit_dye_source=$issue_arr[$row[csf('batch_id')]]['knit_dye_source'];	
						$knit_dye_company=$issue_arr[$row[csf('batch_id')]]['knit_dye_company'];	
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];
						$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
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
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td> 
                            <td width="80"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="100"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
                            <td width="120" ><p><? echo $knitting_company; ?></p></td>									
                            <td width="100" ><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td width="100" align="right"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100" align="right"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <!-- <td width="80" align="right"><p><? // echo $row[csf('rack_no')]; ?></p></td> -->                            
                            <td width="80" align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>

							<td width="80" align="right"><p><? echo $gsm; ?></p></td>
							<td width="80" align="right"><p><? echo $width; ?></p></td>
							<!-- <td width="80" align="right"><p><? // echo number_format($grey_used_qty,2); ?></p></td> -->

                            <td width="80" align="right"><p><? echo $row[csf('quantity')]; ?></p></td>

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
                    	<td colspan="11" align="right">Total</td>
                        <!-- <td align="right">&nbsp;<? // echo number_format($tot_grey_qty,2); ?>&nbsp;</td> -->
                        <td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
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
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	?>
    <fieldset style="width:1100px; margin-left:3px">
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
			<div style="width:1100px;" align="center">
	    		<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
					<tr>
						<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
						<td> <div id="report_container"> </div> </td>
					</tr>
	             </table>
        	</div>
            <div id="scroll_body" align="center">			
            <table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="14">Receive Return Details</th>
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
                        <th width="80">Fabric Des.</th>

                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th width="80">Grey Qty.</th>
                        <th width="80">Ret. Qty</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");				
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");
					$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";	
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
					}
					//print_r($issue_arr);

					$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}
					//print_r($grey_used_arr);	

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
							$gsm=$issue_arr[$row[csf('pi_wo_batch_no')]]['gsm'];
							$width=$issue_arr[$row[csf('pi_wo_batch_no')]]['width'];
							$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
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
                            <td width="70"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="100"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
                            <td width="130"><p><? echo $knitting_company; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>                            
                            <td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
                            <td width="70"><p><? echo $row[csf('rack')]; ?></p></td>
                            <td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>

                            <td width="80" align="right"><p><? echo $gsm; ?></p></td>
							<td width="80" align="right"><p><? echo $width; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
                           
                            <td  align="right" width="70"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            
                        </tr>
						<?
						$tot_ret_qty+=$row[csf('quantity')];
						$tot_grey_qty+=$grey_used_qty;
						$i++;
					}
				?>
                </tbody>
                 <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="12" align="right">Total</td>
                        <td align="right">&nbsp;<? echo number_format($tot_grey_qty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_ret_qty,2); ?>&nbsp;</td>
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
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	?>
    <fieldset style="width:1300px; margin-left:3px">
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
		<div style="width:1300px;" align="center">
    		<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
            <tr>
            <td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
             <td> <div id="report_container"> </div> </td>
            </tr>
             </table>
        </div>

	        <div id="scroll_body" align="center">			
            <table border="1" class="rpt_table" rules="all" width="1300" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="17">Transfer Out Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th> 
                        <th width="80">Ret. Date</th>
                        <th width="100">Dyeing Source</th>
                        <th width="120">Dyeing Company</th>
						<th width="100">In Order</th>
                        <th width="100">Color</th> 
                        <th width="100">Buyer</th> 
                        <th width="100">Trns. From Style</th> 
                        <th width="100">Trns. To Style</th> 
                        <th width="100">Batch No</th>
                        <th width="80">Rack</th>
                        <th width="80">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th width="80">Grey Qty.</th>
                        <th width="80">Ret. Qty</th>
                    </tr>
				</thead>
                <tbody>
                    
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");	
					$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');			
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");
					$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";	
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

					$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}
					//print_r($grey_used_arr);
					$i=1;
					//$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$companyID and a.id=b.mst_id and a.transfer_criteria=4 and a.from_order_id in($po_id) and b.from_prod_id in ($prod_id) and a.item_category=2 group by a.from_order_id, b.from_prod_id, a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.from_prod_id, b.uom";
		
					$sql_transfer_out="select a.transfer_system_id, a.transfer_date,a.from_order_id,a.to_order_id,b.batch_id,b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty,c.color_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=15 and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and c.prod_id in( $prod_id ) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by  a.transfer_system_id, a.transfer_date,b.from_prod_id,a.from_order_id,a.to_order_id,c.color_id,b.batch_id";
					$transfer_out=sql_select($sql_transfer_out);
					foreach($transfer_out as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						$knit_dye_source=$issue_arr[$row[csf('batch_id')]]['knit_dye_source'];
						$knit_dye_company=$issue_arr[$row[csf('batch_id')]]['knit_dye_company'];
						//$rack=$issue_arr[$row[csf('batch_id')]]['rack'];
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];
						$buyer_id=$issue_arr[$row[csf('batch_id')]]['buyer_id'];


						$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
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
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td width="100"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
                            <td width="130"><p><? echo $knitting_company; ?></p></td>
                            <td width="80"><p><? echo $row[csf('to_order_id')]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
                            <td width="100"><p><? echo $po_number_no_arr[$row[csf('from_order_id')]]; ?></p></td>
                            <td width="100"><p><? echo $po_number_no_arr[$row[csf('to_order_id')]]; ?></p></td>
                            <td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="70" ><p><? echo $product_arr[$row[csf('rack')]]; ?></p></td>                            
                            <td  align="right"><p><? echo  $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo $gsm; ?></p></td>
							<td width="80" align="right"><p><? echo $width; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
                            <td width="70" align="right" ><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?> &nbsp;</p></td>
                        </tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$tot_grey_qty+=$grey_used_qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="15" align="right">Total</td>
                        <td align="right">&nbsp;<? echo number_format($tot_grey_qty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
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
	//$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	
	?>
    <fieldset style="width:1100px; margin-left:3px">
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
			<div style="width:1100px;" align="center">
	    		<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
	            <tr>
	            <td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
	             <td> <div id="report_container"> </div> </td>
	            </tr>
	             </table>
	        </div>
				<div id="scroll_body" align="center">			
				<table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<tr>
						<th colspan="12">Issue Details</th>
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
						<!--<th width="80">Rack</th>-->
						<th width="80">Fabric Des.</th>

						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<!--<th width="80">Grey Qty.</th>-->
						<th width="80">Ret. Qty</th>

						</tr>
					</thead>
				<tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");				
					$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");

					$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";	
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
					}
					//print_r($issue_arr);

					$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}
					//print_r($grey_used_arr);

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
						$rack=$issue_arr[$row[csf('batch_id')]]['rack'];	
						//echo $row[csf('batch_id')].'='.$batch_no_arr[$row[csf('batch_id')]];
						$knit_dye_source=$issue_arr[$row[csf('batch_id')]]['knit_dye_source'];	
						$knit_dye_company=$issue_arr[$row[csf('batch_id')]]['knit_dye_company'];	
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];
						$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
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
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="80"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>

                            <td width="100"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>	

                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>                                                      
                            <td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <!-- <td width="80"><p><? // echo $row[csf('rack_no')]; ?></p></td> -->
                            <td width="80" align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>

                            <td width="80" align="right"><p><? echo $gsm; ?></p></td>
							<td width="80" align="right"><p><? echo $width; ?></p></td>
							<!--<td width="80" align="right"><p><? // echo number_format($grey_used_qty,2); ?></p></td>-->

                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>

                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_grey_qty+=$grey_used_qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="11" align="right">Total</td>
                        <!-- <td align="right">&nbsp;<? // echo number_format($tot_grey_qty,2); ?>&nbsp;</td>-->
                        <td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
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
	//$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	
	?>
    <fieldset style="width:1100px; margin-left:3px">
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
			<div style="width:1100px;" align="center">
				<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
					<tr>
						<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
						<td> <div id="report_container"> </div> </td>
					</tr>
				</table>
			</div>
			<div id="scroll_body" align="center">			
            <table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="14">Receive Return Details</th>
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
                        <th width="80">Fabric Des.</th>

                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th width="80">Grey Qty.</th>
                        <th width="80">Ret. Qty</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");	
					$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");
					$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";	
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
					}
					//print_r($issue_arr);

					$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}
					//print_r($grey_used_arr);	

					$i=1;
					$mrr_sql="select a.issue_number,a.company_id,a.challan_no,a.issue_date,b.pi_wo_batch_no,b.rack,c.color_id, b.prod_id, sum(c.quantity) as quantity
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c 
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (46) and c.entry_form in (46)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and b.transaction_date='$from_date' and c.prod_id in( $prod_id ) and c.trans_id!=0 group by a.issue_number,a.company_id,a.challan_no,a.issue_date,b.pi_wo_batch_no,c.color_id,b.rack, b.prod_id";		
					//echo $mrr_sql; 		
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						$knit_dye_source=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
						$knit_dye_company=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];
						$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
						$gsm=$issue_arr[$row[csf('pi_wo_batch_no')]]['gsm'];
						$width=$issue_arr[$row[csf('pi_wo_batch_no')]]['width'];
						$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
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
                            <td width="70"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="100"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
                            <td width="130"><p><? echo $knitting_company; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
                            <td width="70" ><p><? echo $rack; ?></p></td>
                            <td  align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>

                            <td width="80" align="right"><p><? echo $gsm; ?></p></td>
							<td width="80" align="right"><p><? echo $width; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
                            <td width="70" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>

                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_grey_qty+=$grey_used_qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="12" align="right">Total</td>
                        <td align="right">&nbsp;<? echo number_format($tot_grey_qty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
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
	//$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	
	?>
    <fieldset style="width:1100px; margin-left:3px">
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
			<div style="width:1100px;" align="center">
	    		<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
		            <tr>
		            	<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
		             	<td> <div id="report_container"> </div> </td>
		            </tr>
	             </table>
        	</div>
			<div id="scroll_body" align="center">			
            <table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="14">Transfer Out Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th> 
                        <th width="80">Ret. Date</th>
                        <th width="100">Dyeing Source</th>
                        <th width="120">Dyeing Company</th>
						<th width="100">In Order</th>
                        <th width="100">Color</th> 
                        <th width="100">Batch No</th>
                        <th width="80">Rack</th>
                        <th width="80">Fabric Des.</th>

                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th width="80">Grey Qty.</th>
                        <th width="80">Ret. Qty</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");	
					$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");
					$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";	
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
					}
					//print_r($issue_arr);

					$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}
					//print_r($grey_used_arr);
					$i=1;
					//echo $from_date;//d.transaction_date='$from_date'
					// $mrr_sql="select a.company_id, a.issue_number, a.issue_date,a.challan_no, b.prod_id,b.batch_id,b.rack_no,c.quantity,c.color_id
					// from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c ,inv_transaction d
					// where a.id=b.mst_id and a.id=d.mst_id and b.id=c.dtls_id and d.id=c.trans_id and a.entry_form in(18,71) and d.transaction_type in(2)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and d.transaction_date='$from_date' and a.company_id='$companyID'  and c.prod_id in( $prod_id ) and c.color_id='$color'";
					$mrr_sql="select a.transfer_system_id, a.transfer_date,a.from_order_id,a.to_order_id,b.batch_id,b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty,c.color_id 
					from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, inv_transaction d
					where a.company_id=$companyID and d.transaction_date='$from_date' and d.id=c.trans_id and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=15 and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and c.prod_id in( $prod_id ) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by  a.transfer_system_id, a.transfer_date,b.from_prod_id,a.from_order_id,a.to_order_id,c.color_id,b.batch_id";
					//echo $mrr_sql;
					
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knit_dye_source=$issue_arr[$row[csf('batch_id')]]['knit_dye_source'];
						$knit_dye_company=$issue_arr[$row[csf('batch_id')]]['knit_dye_company'];
						//$rack=$issue_arr[$row[csf('batch_id')]]['rack'];
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];
						$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
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
                            <td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td width="100"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
                            <td width="130"><p><? echo $knitting_company; ?></p></td>
                            <td width="80"><p><? echo $row[csf('to_order_id')]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60" ><p><? echo $row[csf('rack_no')]; ?></p></td>
                            <td  align="right"><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>

                            <td width="80" align="right"><p><? echo $gsm; ?></p></td>
							<td width="80" align="right"><p><? echo $width; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>

                            <td width="70" align="right" ><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?> &nbsp;</p></td>

                        </tr>
						<?
						$tot_qty+=$row[csf('transfer_out_qnty')];
						$tot_grey_qty+=$grey_used_qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="12" align="right">Total</td>
                        <td align="right">&nbsp;<? echo number_format($tot_grey_qty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
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
		?>    
	<fieldset style="width:1020px; margin-left:3px">
    
		<div id="report_id" align="center">
         <table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0"> 
                <tr>
                    <td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
                     <td> <div id="report_container"> </div> </td>
                </tr>
             </table>
			<table border="1" class="rpt_table" rules="all" width="1000" cellpadding="0" cellspacing="0" align="center">
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
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
										
					$mrr_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
					from  inv_issue_master a, inv_transaction b, order_wise_pro_details c 
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and c.prod_id=$prod_id and c.color_id='$color'";
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
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60" ><p><? echo $row[csf('rack')]; ?></p></td>
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
		?>    
	
		<div style="width:570px;" align="center">
    		<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
            <tr>
            <td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
             <td> <div id="report_container"> </div> </td>
            </tr>
             </table>
        </div>
        <div id="scroll_body" align="center">
             
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
                <tr>
                	<th colspan="5"> Stock Details</th>
                </tr>
                	<tr>
                        <th width="50">Sl</th>
                        <th width="80">Product ID</th>
                        <th width="200">Batch No</th>
                        <th width="100">Rack</th>
                        <th>Qty</th>
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
								
					$mrr_sql="select a.id as batch_id, a.batch_no, b.prod_id, b.rack,
					    sum(case when c.entry_form in (7,37,66,68) then c.quantity else 0 end) as receive_qnty,
					    sum(case when c.entry_form=15 and c.trans_type=5 then c.quantity else 0 end) as rec_trns_qnty,
						sum(case when c.entry_form in (18,71) then c.quantity else 0 end) as issue_qnty,
						sum(case when c.entry_form=15 and c.trans_type=6 then c.quantity else 0 end) as issue_trns_qnty
					
					from  pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c 
					where a.id=b.pi_wo_batch_no and b.id=c.trans_id  and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.prod_id in( $prod_id )  and c.entry_form in (7,37,66,68,15,18,71) 
					group by a.id, a.batch_no, b.prod_id,b.rack";
					//echo $mrr_sql;//die;
					$dtlsArray=sql_select($mrr_sql);
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							$rack=$row[csf('rack')];
							if($row[csf('rack')]=='') $row[csf('rack')]=0;else $row[csf('rack')]=$row[csf('rack')];
							$issue_ret_qty=$iss_return_qnty[$row[csf('batch_id')]][$row[csf('rack')]][$row[csf('prod_id')]]['issue_ret_qnty'];
							$recv_ret_qty=$rec_return_qnty[$row[csf('batch_id')]][$row[csf('rack')]][$row[csf('prod_id')]]['rec_ret_qnty'];
							$tot_recv_bal=$row[csf('receive_qnty')]+$row[csf('rec_trns_qnty')]+$issue_ret_qty;
							$tot_issue_bal=$row[csf('issue_qnty')]+$row[csf('issue_trns_qnty')]+$recv_ret_qty;
							//echo $tot_recv_bal.'-'.$tot_issue_bal.',';
							$tot_balance=$tot_recv_bal-$tot_issue_bal;
							//if($tot_balance>0)
							//{
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
                            <td align="center" title="<? echo $product_arr[$row[csf('prod_id')]];?>"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td><p><? echo $row[csf('batch_no')]; ?></p></td>
                            <td align="center"><p><? echo $rack; //if($row[csf('rack')]==0) echo ' ' ;else echo $row[csf('rack')]; ?></p></td>
                            <td align="right" title="<? echo 'Recv :'.$tot_recv_bal.', '.'Issue :'.$tot_issue_bal;?>"><p><? echo number_format($tot_balance,2); ?></p></td>
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
                    	<td colspan="4" align="right">Total</td>
                        <td align="right" title="<? echo $tot_recv_qty.'='.$tot_issue_qty;?>"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
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
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
                <tr>
                	<th colspan="5"> Woven Stock Details</th>
                </tr>
                	<tr>
                        <th width="50">Sl</th>
                        <th width="80">Product ID</th>
                        <th width="200">Batch No</th>
                        <th width="100">Rack</th>
                        <th>Qty</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$mrr_sql="select c.prod_id, b.rack,
					 sum(case when c.entry_form in (17) then c.quantity else 0 end) as recv_qnty,
					  sum(case when c.entry_form in (19) then c.quantity else 0 end) as issue_qnty
					from  inv_transaction b, order_wise_pro_details c 
					where  b.id=c.trans_id and c.entry_form in(17,19) and b.item_category=3 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and c.color_id='$color' and c.prod_id=$prod_id group by  c.prod_id, b.rack ";
					//$dtlsArray=sql_select($mrr_sql);
					$dtlsArray=sql_select($mrr_sql);
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
							$tot_balance=$row[csf('recv_qnty')]-$row[csf('issue_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
                            <td align="center" title="<? echo $product_arr[$row[csf('prod_id')]];?>"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td><p><? echo $row[csf('batch_no')]; ?></p></td>
                            <td align="center"><p><? if($row[csf('rack')]==0) echo ' ' ;else echo $row[csf('rack')]; ?></p></td>
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
                    	<td colspan="4" align="right">Total</td>
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
							<!--<td width="100"><p><? //echo $row[csf('issue_number')]; ?></p></td>--> 
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
?>