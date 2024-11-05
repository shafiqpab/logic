<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.yarns.php');


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
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
		
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
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

	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
				<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th>
							<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						</th> 					
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'fabric_and_yarn_issue_delivery_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();  
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_array=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];

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

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit(); 
} 

if($action=="style_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array; var selected_name = new Array;

		function check_all_datas()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value2( str ) {

			if (str!="") str=str.split("_");

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );


			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );

			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';

			}

			id = id.substr( 0, id.length - 1 );
			$('#hidden_style_no').val( id );
		}
	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:680px;">
					<table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th width="170">Please Enter Style No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />              
							<input type="hidden" name="hidden_style_no" id="hidden_style_no" value="" />              
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>   

								<td align="center">				
									<input type="text" style="width:130px" class="text_boxes" name="txt_style" id="txt_style" />	
								</td> 	
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_style').value+'**'+'<? echo $cbo_year_id; ?>', 'create_style_no_search_list_view', 'search_div', 'fabric_and_yarn_issue_delivery_status_report_controller', 'setFilterGrid(\'tbl_list_div\',-1)');" style="width:100px;" />
								</td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:15px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}
if($action=="create_style_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[3];
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{ 
				$buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else 
			{ 
				$buyer_id_cond="";
			}
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
	
	if(trim($data[2])) 
	{
		$style_ref_cond=" and style_ref_no = ".trim($data[2]);
	}
	

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
	

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $style_ref_cond $buyer_id_cond $year_search_cond order by style_ref_no";
	
	
	$sqlResult=sql_select($sql);
	?>
	<div align="center">
		<fieldset style="width:650px;margin-left:10px">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="130">Company</th> 
						<th width="110">Buyer</th>
						<th width="110">Job No</th>
						<th width="120">Style Ref.</th>
					</thead>
				</table>
				<div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_div">
						<?
						$i=1;
						foreach($sqlResult as $row )
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							$data=$i.'_'.$row[csf('style_ref_no')];
						//echo $data;
							?>
							<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
								<td width="30" align="center"><?php echo $i; ?>
									<td width="130"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
									<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
									<td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
									<td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
					<table width="650" cellspacing="0" cellpadding="0" style="border:none" align="center">
						<tr>
							<td align="center" height="30" valign="bottom">
								<div style="width:100%">
									<div style="width:50%; float:left" align="left">
										<input type="checkbox" name="check_all" id="check_all" onClick="check_all_datas()"/>
										Check / Uncheck All
									</div>
									<div style="width:50%; float:left" align="left">
										<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
									</div>
								</div>
							</td>
						</tr>
					</table>
			</form>
		</fieldset>
	</div>
	<?
	exit(); 
} 

if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#order_no_id").val(splitData[0]); 
			$("#order_no_val").val(splitData[1]); 
			parent.emailwindow.hide();
		}

	</script>
	<input type="hidden" id="order_no_id" />
	<input type="hidden" id="order_no_val" />
	<?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";
	if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	$job_no=str_replace("'","",$txt_job_id);
	if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and b.job_no_prefix_num in (".$data[2].")";

	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no DESC";

	$arr=array(1=>$buyer_arr);

	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "fabric_and_yarn_issue_delivery_status_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$rpt_type=str_replace("'","",$rpt_type);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);


	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";
	}


	if( $txt_date_from!="" && $txt_date_to!="" ) $date_cond=" and a.pub_shipment_date between '".$txt_date_from."' and '".$txt_date_to."'";

	$job_no=trim(str_replace("'","",$txt_job_no));
	if ($job_no) $job_no_cond=" and b.job_no_prefix_num in ($job_no) "; else $job_no_cond="";
	$year_id=str_replace("'","",$cbo_year);

	$txt_job_id=str_replace("'","",$txt_job_id);
	if ($txt_job_id) $job_no_cond .=" and b.id in ($txt_job_id) ";

	$txt_style_no= trim(str_replace("'","",$txt_style_no));

	$style_no_cond=""; 
	$style_class_cond = "";
	if($txt_style_no)
	{
		$style_arr = array_unique(array_filter(explode(",", $txt_style_no)));
		foreach ($style_arr as $val) 
		{
			if($style_class_cond =="") $style_class_cond .= "'".$val."'"; else $style_class_cond .= ",'".$val."'";
		}
		if($style_class_cond!="") $style_no_cond = " and b.style_ref_no in (".$style_class_cond.")";
	}


	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(b.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}

	$order_id=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond=" and a.id in ($order_id)";

	$order_no= trim(str_replace("'","",$txt_order_no));
	$order_id_cond="";$order_ids="";
	if($order_no) 
	{
		$order_arr = array_unique(array_filter(explode(",", $order_no)));
		foreach ($order_arr as $val) 
		{
			if($order_ids =="") $order_ids .= "'".$val."'"; else $order_ids .= ",'".$val."'";
		}

		$order_id_cond .=" and a.po_number in ($order_ids)";
	}

	$sql="select b.job_no, b.buyer_name, b.style_ref_no, a.id as po_id, a.po_number, a.pub_shipment_date from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $style_no_cond $date_cond $order_id_cond order by a.id, a.pub_shipment_date";
	$result=sql_select( $sql );

	foreach ($result as $val) 
	{
		$po_id_arr[$val[csf("po_id")]] =$val[csf("po_id")];
	}

	$po_ids = implode(",", array_filter(array_unique($po_id_arr)));
	$poCond = $all_po_id_cond = $poCond2 = $all_po_id_cond2 = ""; 
	$all_po_id_arr=explode(",",$po_ids);
	if($po_ids)
	{
		if($db_type==2 && count($all_po_id_arr)>999)
		{
			$all_po_id_chunk=array_chunk($all_po_id_arr,999) ;
			foreach($all_po_id_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$poCond.=" b.po_break_down_id in($chunk_arr_value) or ";
				$poCond2.=" b.po_breakdown_id in($chunk_arr_value) or ";
			}

			$all_po_id_cond.=" and (".chop($poCond,'or ').")";	
			$all_po_id_cond2.=" and (".chop($poCond,'or ').")";	

		}
		else
		{ 
			$all_po_id_cond=" and b.po_break_down_id in($po_ids)"; 
			$all_po_id_cond2=" and b.po_breakdown_id in($po_ids)"; 
		}
	}
	else
	{
		echo "<span><b>Data Not Found...</b></span>";die;
	}

	$booking_sql = sql_select("select a.booking_date, b.update_date,b.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no = b.booking_no and b.status_active = 1 and b.is_deleted = 0 $all_po_id_cond and a.booking_type = 1 group by a.booking_date, b.update_date,b.po_break_down_id order by a.booking_date desc");

	foreach ($booking_sql as $val) 
	{
		$booking_data[$val[csf("po_break_down_id")]]["booking_date"] = $val[csf("booking_date")];
		$booking_data[$val[csf("po_break_down_id")]]["update_date"] = $val[csf("update_date")];
	}
	if($db_type == 0){
		$is_sales_cond = " b.is_sales =''";
	}else{
		$is_sales_cond = " b.is_sales is  null";
	}
	
	$issue_sql = sql_select("select a.transaction_type, b.po_breakdown_id,a.item_category, sum(b.quantity) cons_quantity, min(a.transaction_date) min_date, max(a.transaction_date) max_date from  inv_transaction a, order_wise_pro_details b where a.id = b.trans_id and a.transaction_type in (2,4) and b.trans_type in (2,4) and a.item_category in (1,13) and (b.is_sales =0 or $is_sales_cond)  and a.status_active = 1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_po_id_cond2 group by b.po_breakdown_id,a.item_category,a.transaction_type order by b.po_breakdown_id desc");

	foreach ($issue_sql as $val) 
	{
		if($val[csf("transaction_type")] ==2)
		{
			if($val[csf("item_category")]==1)
			{
				$issue_data[$val[csf("po_breakdown_id")]]["yarn"] += $val[csf("cons_quantity")];
				$issue_data[$val[csf("po_breakdown_id")]]["yarn_iss_min"] = $val[csf("min_date")];
				$issue_data[$val[csf("po_breakdown_id")]]["yarn_iss_max"] = $val[csf("max_date")];

			}
			else
			{
				$issue_data[$val[csf("po_breakdown_id")]]["grey_fab"] += $val[csf("cons_quantity")];
				$issue_data[$val[csf("po_breakdown_id")]]["grey_fab_iss_min"] = $val[csf("min_date")];
				$issue_data[$val[csf("po_breakdown_id")]]["grey_fab_iss_max"] = $val[csf("max_date")];
			}
		}
		else
		{
			if($val[csf("item_category")]==1)
			{
				$return_data[$val[csf("po_breakdown_id")]]["yarn"] += $val[csf("cons_quantity")];
			}
			else
			{
				$return_data[$val[csf("po_breakdown_id")]]["grey_fab"] += $val[csf("cons_quantity")];
			}
		}
	}

	$grey_prod_data = return_library_array("select b.po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details b where entry_form=2 and status_active=1 and is_deleted = 0 and (b.is_sales =0 or $is_sales_cond)  $all_po_id_cond2 group by po_breakdown_id", 'po_breakdown_id', 'quantity');

	$finish_sql = sql_select("select b.po_breakdown_id, sum(b.quantity) quantity, min(a.transaction_date) min_date, max(a.transaction_date) max_date
	from inv_transaction a, order_wise_pro_details b
	where a.id = b.trans_id and a.transaction_type=1 and a.item_category = 2  and b.entry_form = 37 $all_po_id_cond2 and b.trans_type=1 and (b.is_sales =0 or $is_sales_cond)  
	and a.status_active = 1 and a.is_deleted= 0 and b.status_active = 1 and b.is_deleted= 0
	group by b.po_breakdown_id
	order by b.po_breakdown_id desc");

	foreach ($finish_sql as $val) 
	{
		$finish_data[$val[csf("po_breakdown_id")]]["finish_fab"] += $val[csf("quantity")];
		$finish_data[$val[csf("po_breakdown_id")]]["finish_fab_iss_min"] = $val[csf("min_date")];
		$finish_data[$val[csf("po_breakdown_id")]]["finish_fab_iss_max"] = $val[csf("max_date")];
	}

	$condition= new condition();
	if(str_replace("'","",$cbo_company_id)>0){
	$condition->company_name("=$cbo_company_id");
	}

	if(str_replace("'","",$cbo_buyer_id)>0){
	  	$condition->buyer_name("=$cbo_buyer_id");
	}

	if(str_replace("'","",$job_no) !=''){
	  	$condition->job_no_prefix_num(" in ($job_no)");
	}

	if( str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
	  	$condition->pub_shipment_date(" between '$txt_date_from' and '$txt_date_to'");
	}

	if(str_replace("'","",$order_ids)!='')
	{
		$condition->po_number("in ($order_ids)"); 
	}

	if(str_replace("'","",$style_ref_no)!='')
	{
		$condition->style_ref_no(" in ($style_class_cond)"); 
	}
	
	$condition->init();

	$yarn= new yarn($condition);
	$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();

	ob_start();
	?>
	<style type="text/css">
		.word_break {
			word-break: break-all;
		}
	</style>
	<fieldset style="width:1830px">
		<table cellpadding="0" cellspacing="0" width="1400">
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if( $txt_date_from!="" && $txt_date_to!="" ) echo change_date_format(str_replace("'","",$txt_date_from)) ." To ". change_date_format(str_replace("'","",$txt_date_to)) ;?></strong></td>
			</tr>
		</table>
		<table width="1810" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
			<thead>
				<tr>
					<th width="40">Sl</th>
					<th width="100">Buyer</th>
					<th width="90">Job</th>
					<th width="100">Style</th>
					<th width="100">Order</th>
					<th width="90">Ship Date</th>
					<th width="120">Booking Rlsd DT</th>
					<th width="100">Booking Last Update DT</th>
					<th width="100">Yarn Rqrd Kg</th>
					<th width="120">Yarn Issue DT</th>
					<th width="100">Yarn Issue Kg</th>
					<th width="100">Grey Prod Kg</th>
					<th width="120">Grey Fab Issue DT</th>
					<th width="100">Grey Fab Issue Kg</th>
					<th width="100">Grey Fab Issue Balance</th>
					<th width="100">Finish Fabric Rcvd Actual DT</th>
					<th width="100">Finish Fabric Rcvd KG</th>
					<th width="110">REMARKS</th>
				</tr>
			</thead>
		</table>
		<div style="width:1830px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="1810" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left"> 
				<?
				$i=1;
				foreach ($result as $row) 
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="40" align="center"><? echo $i;?></td>
						<td width="100"><p><? echo $buyer_array[$row[csf("buyer_name")]];?></p></td>
						<td width="90" align="center"><p><? echo $row[csf("job_no")];?></p></td>
						<td width="100" align="center" class="word_break"><p><? echo $row[csf("style_ref_no")];?></p></td>
						<td width="100" align="center" class="word_break"><p><? echo $row[csf("po_number")];?></p></td>
						<td width="90" align="center"><? echo change_date_format($row[csf("pub_shipment_date")]);?></td>
						<td width="120" align="center"><p>
							<? 
							$booking_date="";$booking_date_arr=array();
							$booking_date_arr = array_filter(array_unique(explode(",",chop($booking_data[$row[csf("po_id")]]["booking_date"],","))));
								foreach ($booking_date_arr as $val) 
								{
									$booking_date = change_date_format($val);
								}
								echo $booking_date;
							?></p>
						</td>
						<td width="100" align="center">
							<p><? 
								if($booking_data[$row[csf("po_id")]]["update_date"] != "")
								{
									echo change_date_format($booking_data[$row[csf("po_id")]]["update_date"]);
								}
								
							?></p>
						</td>
						<td width="100" align="right"><p><? echo number_format($yarn_req_qty_arr[$row[csf("po_id")]],2);?></p></td>
						<td width="120" align="center" class="word_break">
							<p><?
							if($issue_data[$row[csf("po_id")]]["yarn_iss_min"] != "")
							{
								if($issue_data[$row[csf("po_id")]]["yarn_iss_min"] != $issue_data[$row[csf("po_id")]]["yarn_iss_max"]){
									echo change_date_format($issue_data[$row[csf("po_id")]]["yarn_iss_min"]) . " To ". change_date_format($issue_data[$row[csf("po_id")]]["yarn_iss_max"]);
								}else{
									echo change_date_format($issue_data[$row[csf("po_id")]]["yarn_iss_min"]);
								}
							}
							?></p>						
						</td>
						<td width="100" align="right" class="word_break">
							<p><? 
								$yarn_issue = $issue_data[$row[csf("po_id")]]["yarn"] - $return_data[$row[csf("po_id")]]["yarn"];
								echo number_format($yarn_issue,2);
							?></p>
						</td>
						<td width="100" align="right" class="word_break"><? echo number_format($grey_prod_data[$row[csf("po_id")]],2);?></td>
						<td width="120" align="center" class="word_break">
							<p><? 
							if($issue_data[$row[csf("po_id")]]["grey_fab_iss_min"] != "") 
							{
								if($issue_data[$row[csf("po_id")]]["grey_fab_iss_min"] != $issue_data[$row[csf("po_id")]]["grey_fab_iss_max"]) {
									echo change_date_format($issue_data[$row[csf("po_id")]]["grey_fab_iss_min"]) . " To ". change_date_format($issue_data[$row[csf("po_id")]]["grey_fab_iss_max"]); 
								}
								else 
								{
									echo change_date_format($issue_data[$row[csf("po_id")]]["grey_fab_iss_min"]);
								}
							}
							?></p>
						</td>
						<td width="100" align="right" class="word_break">
							<p><? 
								$grey_fab_issue = $issue_data[$row[csf("po_id")]]["grey_fab"] - $return_data[$row[csf("po_id")]]["grey_fab"];
								echo number_format($grey_fab_issue,2);
							?></p>
						</td>
						<td width="100" align="right" class="word_break">
							<p><? 
								$grey_balance = $yarn_req_qty_arr[$row[csf("po_id")]] - $grey_fab_issue;
								echo number_format($grey_balance,2);
							?></p>
						</td>
						<td width="100" align="center">
							<p><? 
							if($finish_data[$row[csf("po_id")]]["finish_fab_iss_min"] != "") 
							{
								if($finish_data[$row[csf("po_id")]]["finish_fab_iss_min"] != $finish_data[$row[csf("po_id")]]["finish_fab_iss_max"]) {
									echo change_date_format($finish_data[$row[csf("po_id")]]["finish_fab_iss_min"]) . " To ". change_date_format($finish_data[$row[csf("po_id")]]["finish_fab_iss_max"]); 
								}
								else 
								{
									echo change_date_format($finish_data[$row[csf("po_id")]]["finish_fab_iss_min"]);
								}
							}
							?></p>
						</td>
						<td width="100" align="right"><p><? echo number_format($finish_data[$row[csf("po_id")]]["finish_fab"],2) ;?></p></td>
						<td width="110">&nbsp;</td>
					</tr>
					<?
					$i++;
				}

				?>
			</table>
		</div>
		<table width="1810" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
			<tfoot>
				<tr>
					<th width="40">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="90">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="90">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100" id="value_yarn_req_qnty">Yarn Rqrd Kg</th>
					<th width="120">&nbsp;</th>
					<th width="100" id="value_yarn_issue_qnty">Yarn Issue Kg</th>
					<th width="100" id="value_grey_prod_qnty">Grey Prod Kg</th>
					<th width="120">&nbsp;</th>
					<th width="100" id="value_grey_issue_qnty">Grey Fab Issue Kg</th>
					<th width="100" id="value_grey_balance_qnty">Grey Fab Issue Balance</th>
					<th width="100">&nbsp;</th>
					<th width="100" id="value_finish_receive_qnty">Finish Fabric Rcvd KG</th>
					<th width="110">&nbsp;</th>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	<?

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename####$rpt_type"; 
	exit();
}

