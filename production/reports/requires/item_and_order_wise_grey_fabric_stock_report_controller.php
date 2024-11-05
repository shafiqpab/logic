<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//*--------------------------------------------------------------------------------------------------------------------*//

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );   	 
	exit();
}


if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
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
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'item_and_order_wise_grey_fabric_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
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
	$year_id=$data[3];
	
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
	$search_string=" and job_no_prefix_num = ".trim($data[2])."";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	
	$arr = array(0=>$company_arr,1=>$buyer_arr);
	
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $search_string $buyer_id_cond $year_cond  order by job_no DESC";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "140,130,80,40","600","240",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} 

if($action=="style_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#hide_style_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Year</th>
						<th id="search_by_td_up" width="170">Please Enter Style No</th>
						
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
						
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td>
								<?
								echo create_drop_down("cbo_year", 65, create_year_array(), "", 1, "-- All --",date('Y'), "", 0, "");
								?>
							</td>                 
							<td align="center">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'style_no_search_list_view', 'search_div', 'item_and_order_wise_grey_fabric_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="style_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id = $data[3];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$search_string=" and a.style_ref_no like '%".trim($data[2])."%'";
	
	
	$start_date =trim($data[3]);
	$end_date =trim($data[4]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	
	$arr=array(0=>$company_arr,1=>$buyer_arr);
	
	$sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_string $buyer_id_cond $year_cond  order by a.job_no DESC";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "140,130,80,40","600","240",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit();
	
	exit(); 
}


if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
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
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th id="search_by_td_up" width="170">Please Enter Order No</th>
						<th>Shipment Date</th>
						<th>
							<input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
							<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
							<input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
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
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value, 'create_order_no_search_list_view', 'search_div', 'item_and_order_wise_grey_fabric_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[6];
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$search_string=" and b.po_number like '%".trim($data[2])."%'";
	
	$start_date =trim($data[3]);
	$end_date =trim($data[4]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";
	
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	
	
	$arr=array(0=>$company_arr,1=>$buyer_arr);

	$sql= "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id  $search_string $buyer_id_cond $date_cond $year_cond order by b.id, b.pub_shipment_date";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "150,130,40,40,130,90","760","220",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','',1) ;
	
	exit(); 
}




if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$company_name= str_replace("'","",$cbo_company_name);
	$cbo_year_selection = str_replace("'","",$cbo_year);
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		$buyer_id_cond="";
		$buyer_id_cond_trans="";
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name in (".str_replace("'","",$cbo_buyer_name).")";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") 
	{
		$job_no_cond="";
	}
	else 
	{
		if(str_replace("'","",$txt_job_id) != ''){
			$txt_job_id=str_replace("'","",$txt_job_id);
			$job_no_cond=" and a.id = $txt_job_id ";	
		}else{
			$job_no_cond=" and a.job_no like '%".str_replace("'","",$txt_job_no)."' ";	
		}
		
	}

	$style_no=str_replace("'","",$txt_style_no);
	if ($style_no=="") 
	{
		$style_no_cond=""; 
	}
	else 
	{
		$style_no_cond=" and a.style_ref_no = $txt_style_no ";
	}

	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}else{
			$txt_order_no=str_replace("'","",$txt_order_no);
			$po_cond="and b.po_number like '%".$txt_order_no."%'";
		}
	}

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");

		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);

		}
		$production_date_cond=" and a.receive_date between '$start_date' and '$end_date'";

		$production_sql = "select b.febric_description_id,b.gsm,b.width,sum(b.grey_receive_qnty) as grey_receive_qnty,c.po_breakdown_id from inv_receive_master a,  pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and a.entry_form=2 and a.company_id=$company_name $production_date_cond and b.id=c.dtls_id and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.febric_description_id, b.gsm, b.width,c.po_breakdown_id";
		$production_info = sql_select($production_sql);

		foreach($production_info as $val)
		{
			$productionPoArr[$val[csf('po_breakdown_id')]]=$val[csf('po_breakdown_id')];
		}
	}

	if(str_replace("'","",$txt_booking_date_from)!="" && str_replace("'","",$txt_booking_date_to)!="")
	{
		if($db_type==0)
		{
			$booking_start_date=change_date_format(str_replace("'","",$txt_booking_date_from),"yyyy-mm-dd","");
			$booking_end_date=change_date_format(str_replace("'","",$txt_booking_date_to),"yyyy-mm-dd","");

		}
		else if($db_type==2)
		{
			$booking_start_date=change_date_format(str_replace("'","",$txt_booking_date_from),"","",1);
			$booking_end_date=change_date_format(str_replace("'","",$txt_booking_date_to),"","",1);
		}
		$date_cond=" and b.pub_shipment_date between '$booking_start_date' and '$booking_end_date'";
	}

	$all_po_cond="";
	if(!empty($productionPoArr)){
		if($db_type==2 && count($productionPoArr)>999)
		{
			$all_po_chunk=array_chunk($productionPoArr,999) ;
			foreach($all_po_chunk as $chunk_arr)
			{
				$poCond.=" b.id in(".implode(",",$chunk_arr).") or ";
			}

			$all_po_cond.=" and (".chop($poCond,'or ').")";
		}
		else
		{ 
			$all_po_cond=" and b.id in(".implode(",",$productionPoArr).")";
		}
	}

	if($db_type==2)
	{
		$sql="select b.id,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,b.po_number,d.lib_yarn_count_deter_id,d.fabric_description, listagg(cast(e.booking_no as varchar2(4000)), ',') within group (order by e.booking_no) as booking_no,listagg(d.body_part_id, ',') within group (order by d.body_part_id) as body_part_id,d.gsm_weight,sum(c.grey_fab_qnty) as grey_fab_qnty 
		from wo_po_details_master a,wo_po_break_down b,wo_booking_dtls c,wo_booking_mst e,wo_pre_cost_fabric_cost_dtls d 
		where a.company_name=1 and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no = e.booking_no and c.pre_cost_fabric_cost_dtls_id=d.id and e.fabric_source!=2 and e.item_category=2 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $job_no_cond $style_no_cond $all_po_cond $date_cond group by a.company_name, a.job_no,a.buyer_name, b.id,a.style_ref_no, b.po_number,  d.lib_yarn_count_deter_id,d.fabric_description,d.gsm_weight order by a.job_no,a.buyer_name, b.id,a.style_ref_no, b.po_number,booking_no,body_part_id,d.lib_yarn_count_deter_id,d.fabric_description,d.gsm_weight";
	}else{
		$sql="select b.id,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,b.po_number,d.lib_yarn_count_deter_id,d.fabric_description, group_concat(e.booking_no) booking_no,group_concat(d.body_part_id) body_part_id,d.gsm_weight,sum(c.grey_fab_qnty) as grey_fab_qnty 
		from wo_po_details_master a,wo_po_break_down b,wo_booking_dtls c,wo_booking_mst e,wo_pre_cost_fabric_cost_dtls d 
		where a.company_name=1 and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no = e.booking_no and c.pre_cost_fabric_cost_dtls_id=d.id and e.fabric_source!=2 and e.item_category=2 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $job_no_cond $style_no_cond $all_po_cond $date_cond $po_cond group by a.company_name, a.job_no,a.buyer_name, b.id,a.style_ref_no, b.po_number,  d.lib_yarn_count_deter_id,d.fabric_description,d.gsm_weight order by a.job_no,a.buyer_name, b.id,a.style_ref_no, b.po_number,booking_no,body_part_id,d.lib_yarn_count_deter_id,d.fabric_description,d.gsm_weight";
	}

	$nameArray=sql_select($sql);
	$po_arr=$job_arr=$booking_arr=$po_id_arr=array();
	foreach($nameArray as $row)
	{
		$booking_no = implode(",",array_unique(explode(",",$row[csf('booking_no')])));
		$po_arr[$row[csf('id')]] = $row[csf('id')];
		$job_arr[$row[csf('job_no')]][] = $row[csf('job_no')];
		
		$po_id_arr[$row[csf('job_no')]][$row[csf('id')]][] = $row[csf('lib_yarn_count_deter_id')];

		if(!in_array($row[csf('booking_no')], $booking_arr[$row[csf('job_no')]])){
			$booking_arr[$row[csf('job_no')]][$booking_no][] = $booking_no;
		}
		$order_fabric_total_req_qnty[$row[csf('id')]] += $row[csf('grey_fab_qnty')];
	}
	/*echo "<pre>";
	print_r($booking_arr);
	echo "</pre>";*/
	$productionQtyArr=array();
	if(!empty($po_arr)){
		$gray_production_sql = "select c.po_breakdown_id,b.febric_description_id,sum(c.quantity) as grey_receive_qnty from pro_grey_prod_entry_dtls b,order_wise_pro_details c where b.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in(".implode(",",array_unique($po_arr)).") group by c.po_breakdown_id,b.febric_description_id";
		$gray_production_info = sql_select($gray_production_sql);

		foreach($gray_production_info as $val)
		{
			$productionQtyArr[$val[csf('po_breakdown_id')]][$val[csf('febric_description_id')]] = $val[csf('grey_receive_qnty')];
			$orderWiseProductionQtyArr[$val[csf('po_breakdown_id')]] += $val[csf('grey_receive_qnty')];
		}
	}

	ob_start();
	$tblWidth = "1320";
	?>
	<fieldset style="width:<? echo $tblWidth; ?>px; margin-top:2px;">
		<table width="<? echo $tblWidth; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" >
			<tr class="form_caption">
				<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold">
					<div style="float:left;"> 
						<span style="background-color:red; padding:0 5px;"></span> &nbsp;Production ration is less than 50% from required ratio
					</div>
					<div style="margin:auto; width:50%">
						<? echo $report_title; ?> 
					</div>
				</td>
			</tr>
			<tr class="form_caption">
				<td colspan="15" align="center"><? echo $company_arr[$company_name]; ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tblWidth; ?>" class="rpt_table" >
			<thead>
				<tr>
					<th width="20">SL</th>
					<th width="80">Job No</th>
					<th width="100">Buyer</th>
					<th width="80">Style Ref.</th>
					<th width="80">Order No</th>					
					<th width="100">Work Order (Booking No)</th>
					<th width="140">Body Part(Items)</th>
					<th width="140">Item Wise Fabric Description</th>
					<th width="80">Item Wise Req. Ratio %</th>
					<th width="60">Current Production Ratio %</th>
					<th width="80">Grey Fabric Req. Qty. (Kg)</th>
					<th width="80">Knitting Production Qty. (Kg)</th>
					<th width="60">Balance Qty. (Kg)</th>
					<th width="80">Item Wise Achievement %</th>
					<th width="50">Balance %</th>
					<th width="80">Order Achievement %</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $tblWidth+20; ?>px; overflow-y:scroll; max-height:320px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tblWidth; ?>" class="rpt_table" id="tbl_list_search" style="word-break:break-all">
				<?
				$i=0; 
				if(!empty($nameArray)){
					$jobs_ar=$book_ar=$po_ar=array();
					$j=$k=$m=1;
					$job_rowspan=$po_rowspan=$book_rowspan=0;
					foreach($nameArray as $row)
					{
						$job_rowspan = count($job_arr[$row[csf('job_no')]]);
						if(!in_array($row[csf('job_no')], $jobs_ar)){
							array_push($jobs_ar,$row[csf('job_no')]);
							$j=1;
							$i++;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						}

						$po_no = $row[csf('id')];
						$po_rowspan = count($po_id_arr[$row[csf('job_no')]][$row[csf('id')]]);
						if(!in_array($po_no, $po_ar)){
							array_push($po_ar,$po_no);
							$m=1;
						}

						$booking_no 	= implode(",",array_unique(explode(",",$row[csf('booking_no')])));
						$book_rowspan 	= count($booking_arr[$row[csf('job_no')]][$booking_no]);
						if(!in_array($booking_no, $book_ar)){
							array_push($book_ar,$booking_no);
							$k=1;
						}

						$bodyPartIdArr =  array_unique(explode(",", $row[csf('body_part_id')]));
						$bodyPart = "";
						foreach($bodyPartIdArr as $bodyPartId){
							if($bodyPart!="") $bodyPart .= ", ";
							$bodyPart .= $body_part[$bodyPartId];
						}

						$grey_fab_qnty 			= number_format($row[csf('grey_fab_qnty')],2,".","");
						$total_grey_req_qnty 	= number_format($order_fabric_total_req_qnty[$row[csf('id')]],2,".","");
						$total_knitting_qnty 	= number_format($orderWiseProductionQtyArr[$row[csf('id')]],2,".","");
						$item_req_ratio 		= number_format(($grey_fab_qnty/$total_grey_req_qnty)*100,2,".","");						
						$knittingQty 			= number_format($productionQtyArr[$row[csf('id')]][$row[csf('lib_yarn_count_deter_id')]],2,".","");
						$production_ratio 		= number_format(($knittingQty/$total_knitting_qnty)*100,2,".","");
						$balanceQty 			= number_format(($grey_fab_qnty - $knittingQty),2,".","");
						$production_ratio_color = ((($production_ratio/$item_req_ratio)*100) < ($item_req_ratio/2))?" style='background-color:red;color:#fff;padding-right:2px;'":"";
						?>
						<tr bgcolor="<? echo $bgcolor;?>"  id="tr<? echo $i;?>" >
							
							<? if($j==1){?>
								<td width="20" align="center"  rowspan="<? echo $job_rowspan; ?>"><? echo $i; ?></td>
								<td width="80" align="center" rowspan="<? echo $job_rowspan; ?>"><? echo $row[csf('job_no')];  ?></td>
								<td width="100" align="center" rowspan="<? echo $job_rowspan; ?>"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
								<td width="80" rowspan="<? echo $job_rowspan; ?>"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
								
								<? 
							}
							if($m==1){
								?>
								<td width="80" align="center" rowspan="<? echo $po_rowspan; ?>" title="<? echo $row[csf('id')]; ?>"><? echo $row[csf('po_number')]; ?></td>
								<?
							}
							/*if($k==1){?>
								<td width="100" rowspan="<? echo $book_rowspan; ?>"><p><? echo $booking_no; ?></p></td>
							<? }*/ ?>	
							<td width="100" ><p><? echo $booking_no; ?></p></td>
							<td width="140"><p><? echo $bodyPart; ?></p></td>
							<td width="140" title="<? echo $row[csf('lib_yarn_count_deter_id')]; ?>"><? echo $row[csf('fabric_description')]; ?></td>
							<td width="80" align="right"><? echo $item_req_ratio; ?></td>
							<td width="60" align="right" <? echo $production_ratio_color; ?> title="Production ration is less than 50% from required ratio
"><? echo $production_ratio; ?></td>
							<td width="80" align="right" title="Order Wise Total Grey Fabric Req. Qty. = <? echo $total_grey_req_qnty; ?>"><? echo $grey_fab_qnty; ?></td>
							<td width="80" align="right" title="Order Wise Total Knitting Production Qty. = <? echo $total_knitting_qnty; ?>"><? echo $knittingQty; ?></td>
							<td width="60" align="right"><? echo $balanceQty; ?></td>
							<td width="80" align="right"><? echo number_format(($knittingQty/$grey_fab_qnty)*100,2,".",""); ?></td>
							<td width="50" align="right"><? echo number_format(($balanceQty/$grey_fab_qnty)*100,2,".",""); ?></td>
							<? if($m==1){?>
								<td width="80" align="right" rowspan="<? echo $po_rowspan; ?>" title="Total Knitting=<? echo $total_knitting_qnty; ?>,Total Grey Required=<? echo $total_grey_req_qnty; ?>"><? echo number_format(($total_knitting_qnty/$total_grey_req_qnty)*100,2,".",""); ?></td>
							<? } ?>	
						</tr>
						<?
						$total_ratio += $item_req_ratio;
						$total_production_ratio += $production_ratio;
						$total_grey_fab_qnty += $grey_fab_qnty;
						$total_knittingQty += $knittingQty;
						$total_balanceQty += $balanceQty;

						if(in_array($row[csf('job_no')], $jobs_ar)){
							$j++;

						}else{
							unset($jobs_ar);
							$j==1;
						}

						if(in_array($row[csf('id')], $po_ar)){
							$m++;
						}else{
							unset($po_ar);
							$m==1;
						}

						if(in_array($booking_no, $book_ar)){
							$k++;
						}else{
							unset($book_ar);
							$k==1;
						}
						
					}
				}else{
					?>
					<tr><td colspan="16" align="center"><strong>No Data Found</strong></td></tr>
					<?
				}
				?>
				<tr bgcolor="<? echo $bgcolor;?>"  id="tr<? echo $i;?>" >
					<th colspan="8"></th>
					<th width="80" align="right"></th>
					<th width="60" align="right"></th>
					<th width="80" align="right"><? echo number_format($total_grey_fab_qnty,2,".",""); ?></th>
					<th width="80" align="right"><? echo number_format($total_knittingQty,2,".",""); ?></th>
					<th width="60" align="right"><? echo number_format($total_balanceQty,2,".",""); ?></th>
					<th colspan="3"></th>
				</tr>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}
?>