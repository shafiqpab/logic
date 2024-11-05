<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company in($data) and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
  	exit();	 
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 130, "select id,store_name from lib_store_location where status_active =1 and is_deleted=0 and company_id in($data) order by store_name","id,store_name", 1, "-- Select Store --", $selected, "",0 );     
	exit();	
}

if($action=="job_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_id_arr = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
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
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[0], selected_id_arr ) == -1 ) {
				selected_id_arr.push( str[0] );
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
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
		}
	
    </script>

	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Company Name</th>
	                    <th>Buyer</th>
	                    <th>Style Reference</th>
	                    <th>Job No</th>
	                    <th>PO Number</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"> 
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
								<?
									echo create_drop_down( "cbo_company_id", 100, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company_name, "load_drop_down('requires/style_wise_finish_goods_stock_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
	                        </td>                  
	                        <td align="center" id="buyer_td"> 
	                        	<? 
									echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                   
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_style_ref" id="txt_style_ref" />	
	                        </td> 
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" />	
	                        </td> 
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_po_no" id="txt_po_no" />	
	                        </td> 
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_style_ref').value+'**'+document.getElementById('txt_job_no').value+'**'+document.getElementById('txt_po_no').value, 'search_list_view', 'search_div', 'style_wise_finish_goods_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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

if($action=="search_list_view")
{
	$data=explode('**',$data);
	$company_id=str_replace("'", "", $data[0]);
	$buyer_id=str_replace("'", "", $data[1]);
	$style_ref=str_replace("'", "", $data[2]);
	$job_no=str_replace("'", "", $data[3]);
	$po_no=str_replace("'", "", $data[4]);

	if($style_ref=="" && $job_no=="" && $po_no=="")
	{
		?>
		<div class="alert alert-danger">Please enter value of style, job or po number search field.</div>
		<?
		die;
	}
		
	$search_string='';
	if($company_id!=0)
	{
		$search_string.=" and a.company_name=$company_id ";
	}
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $search_string.=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
		}
	}
	else
	{
		$search_string.=" and a.buyer_name=$buyer_id ";
	}
	if($style_ref!='')
	{
		$search_string.=" and a.style_ref_no like'%".trim($style_ref)."%' ";
	}
	if($job_no!='')
	{
		$search_string.=" and a.job_no like '%".$job_no."%' ";
	}
	if($po_no!='')
	{
		$search_string.=" and b.po_number like '%".trim($po_no)."%' ";
		
	}

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $po_number="group_concat(distinct(b.po_number)) as po_number,"; 
	else if($db_type==2) $po_number="listagg(cast(b.po_number as varchar(4000)),', ') within group(order by b.id) as po_number";
	else  $po_number="";

	$sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 $search_string group by a.id, a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc"; 
    // echo $sql;
	$res= sql_select($sql);

	$job_id_arr = array();
	foreach ($res as $v) 
	{
		$job_id_arr[$v['ID']]=$v['ID'];
	}
	$job_id_cond = where_con_using_array($job_id_arr,0,"job_id");

	$sql_po = "SELECT job_no_mst,po_number from wo_po_break_down where status_active=1 $job_id_cond";
	$po_res = sql_select($sql_po);
	$po_arr = array();
	foreach ($po_res as $v) 
	{
		$po_arr[$v['JOB_NO_MST']] .= ($po_arr[$v['JOB_NO_MST']]=="") ? $v['PO_NUMBER'] : ",".$v['PO_NUMBER'];
	}

	?>
	<div>
		<table class="rpt_table" id="rpt_tabletbl_list_search" rules="all" width="660" cellspacing="0" cellpadding="0" border="0">
			<thead>
				<tr>
					<th width="50">SL No</th>
					<th width="100">Company</th>
					<th width="100">Buyer Name</th>
					<th width="100">Style</th>
					<th width="100">Job No</th>
					<th>Order No</th>
				</tr>
			</thead>
		</table> 
		<div style="max-height:220px; width:658px; overflow-y:auto" id="">
			<table class="rpt_table" id="tbl_list_search" rules="all" width="638" height="" cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<?
					$i=1;
					foreach ($res as $v) 
					{
						?>
						<tr onclick="js_set_value('<?=$i;?>_<?=$v['ID'];?>_<?=$v['JOB_NO'];?>_<?=$v['STYLE_REF_NO'];?>')" style="cursor:pointer" id="tr_<?=$i;?>" height="20" bgcolor="#FFFFFF">
							<td width="50"><?=$i;?></td>
							<td width="100" align="left"><p><?=$company_library[$v['COMPANY_NAME']];?></p></td>
							<td width="100" align="left"><p><?=$buyer_arr[$v['BUYER_NAME']];?></p></td>
							<td width="100" align="left"><p><?=$v['STYLE_REF_NO'];?></p></td>
							<td width="100" align="left"><p><?=$v['JOB_NO'];?></p></td>
							<td align="left"><p><?=$po_arr[$v['JOB_NO']];?></p></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>
		</div>
		<div class="check_all_container">
			<div style="width:100%">
				<div style="width:50%; float:left" align="left">
					<input type="checkbox" name="check_all" id="check_all" onclick="check_all_data()"> Check / Uncheck All
				</div>
				<div style="width:50%; float:left" align="left">
					<input type="button" name="close" id="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
				</div>
			</div>
		</div>
	</div>
	<?
		
	// echo create_list_view("tbl_list_search", "Company,Buyer Name,Style,Job No,Order No", "100,100,100,100","650","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,style_ref_no,job_no,po_number","",'','0,0,0,0,0','',1) ;
   exit(); 
}

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$hidden_job_id=str_replace("'","",$hidden_job_id);
	$start_date=str_replace("'","",$txt_date_from);
	$end_date=str_replace("'","",$txt_date_to);
	$rpt_type=str_replace("'","",$type);
	if($rpt_type==1)
	{
		$company_arr = return_library_array("SELECT id,company_name from lib_company", "id", "company_name");
		$color_arr = return_library_array("SELECT id,color_name from lib_color", "id", "color_name");
		$size_arr = return_library_array("SELECT id,size_name from lib_size", "id", "size_name");
		$store_arr = return_library_array("SELECT id,store_name from lib_store_location", "id", "store_name");
		$country_arr = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
		$buyer_arr = return_library_array("SELECT id,buyer_name from lib_buyer", "id", "buyer_name");
		$room_rack_self_arr = return_library_array("SELECT floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
		$search_cond='';
		if($cbo_company_name!=''){$search_cond.=" and a.company_id in($cbo_company_name) ";}
		if($cbo_store_name!=''){$search_cond.=" and b.store_id in($cbo_store_name) ";}
		if($cbo_year!=0){$search_cond.=" and f.season_year=$cbo_year ";}
		if($cbo_buyer_id!=0){$search_cond.=" and f.buyer_name=$cbo_buyer_id ";}
		if($hidden_job_id!=''){
			$job_id_arr=explode(',',$hidden_job_id);
			$job_id_in=where_con_using_array($job_id_arr,0,'f.id');
		}
		if ($start_date != '' && $end_date != '') 
		{
			if ($db_type == 0) {
				$date_cond= "and a.delivery_date between '" . change_date_format($start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($end_date, 'yyyy-mm-dd') . "'";
				$prvDate=change_date_format($start_date,'yyyy-mm-dd');
			} else if ($db_type == 2) {
				$date_cond= "and a.delivery_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
				$prvDate=change_date_format($start_date,'','',-1);
			}
		}
		
		$sql ="SELECT a.COMPANY_ID, a.DELIVERY_DATE, a.STORE_ID, a.PURPOSE_ID, b.id as MST_ID, b.COUNTRY_ID, b.ITEM_NUMBER_ID, b.PRODUCTION_TYPE, b.CARTON_QTY, b.ROOM_ID, b.RACK_ID, b.SHELF_ID, c.PRODUCTION_QNTY, d.id as PO_ID, d.PO_NUMBER, d.UNIT_PRICE, e.COLOR_NUMBER_ID, e.SIZE_NUMBER_ID, e.ORDER_QUANTITY, f.id as JOB_ID, f.JOB_NO, f.STYLE_REF_NO, f.BUYER_NAME 
		from pro_gmts_delivery_mst a, pro_garments_production_mst b, pro_garments_production_dtls c, wo_po_break_down d,wo_po_color_size_breakdown e, wo_po_details_master f 
		where a.id=b.delivery_mst_id and a.production_type in (81,82,83) and b.production_type in (81,82,83) and b.id=c.mst_id and b.po_break_down_id=d.id and d.id=e.po_break_down_id and c.color_size_break_down_id=e.id and d.job_id=f.id $search_cond $job_id_in $date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0  order by f.id";	
						
		// echo $sql;die;
		$result = sql_select($sql);	
		$resultArr=array();
		$infoArr=array();
		$all_po_arr=array();
		foreach($result as $row)
		{
			$key=$row['COUNTRY_ID'].'*'.$row['ITEM_NUMBER_ID'].'*'.$row['SIZE_NUMBER_ID'];
			$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['buyer_name']=$row['BUYER_NAME'];
			$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['job_no']=$row['JOB_NO'];
			$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['style_ref_no']=$row['STYLE_REF_NO'];
			$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['po_number']=$row['PO_NUMBER'];
			$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['unit_price']=$row['UNIT_PRICE'];
			$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['country_id']=$row['COUNTRY_ID'];
			$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['item_id']=$row['ITEM_NUMBER_ID'];
			$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['color_id']=$row['COLOR_NUMBER_ID'];
			$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['size_id']=$row['SIZE_NUMBER_ID'];
			$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['order_qnty']=$row['ORDER_QUANTITY'];
			$infoArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']]['store_id'].=$row['STORE_ID'].',';
			$infoArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']]['room_id'].=$row['ROOM_ID'].',';
			$infoArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']]['rack_id'].=$row['RACK_ID'].',';
			$infoArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']]['shelf_id'].=$row['SHELF_ID'].',';

			if($row['PRODUCTION_TYPE']==81)
			{
				$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['cur_rcv_qty']+=$row['PRODUCTION_QNTY'];
				$infoArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']]['cur_rcv_carton'][$row['MST_ID']]=$row['CARTON_QTY'];
			}
			if($row['PRODUCTION_TYPE']==82 && $row['PURPOSE_ID']==1)
			{
				$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['del_cur_iss_qty']+=$row['PRODUCTION_QNTY'];
				$infoArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']]['cur_iss_carton'][$row['MST_ID']]=$row['CARTON_QTY'];
			}
			if($row['PRODUCTION_TYPE']==82 && $row['PURPOSE_ID']==2)
			{
				$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['inspc_cur_iss_qty']+=$row['PRODUCTION_QNTY'];
			}
			if($row['PRODUCTION_TYPE']==82 && $row['PURPOSE_ID']==3)
			{
				$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['sales_cur_iss_qty']+=$row['PRODUCTION_QNTY'];
			}
			if($row['PRODUCTION_TYPE']==83)
			{
				$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['cur_rtn_qty']+=$row['PRODUCTION_QNTY'];
			}
			$all_po_arr[$row['PO_ID']]=$row['PO_ID'];
		}
		$po_id_in=where_con_using_array($all_po_arr,0,'d.id');
		if ($start_date != '' && $end_date != '') 
		{
			$prv_sql ="SELECT a.COMPANY_ID, a.DELIVERY_DATE, a.PURPOSE_ID, b.COUNTRY_ID, b.ITEM_NUMBER_ID, b.PRODUCTION_TYPE, c.PRODUCTION_QNTY, d.id as PO_ID, e.COLOR_NUMBER_ID, e.SIZE_NUMBER_ID, f.id as JOB_ID, f.BUYER_NAME 
			from pro_gmts_delivery_mst a, pro_garments_production_mst b, pro_garments_production_dtls c, wo_po_break_down d,wo_po_color_size_breakdown e, wo_po_details_master f 
			where a.id=b.delivery_mst_id and a.production_type in (81,82,83) and b.production_type in (81,82,83) and b.id=c.mst_id and b.po_break_down_id=d.id and d.id=e.po_break_down_id and c.color_size_break_down_id=e.id and d.job_id=f.id $po_id_in $search_cond $job_id_in and a.delivery_date<'$prvDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0";
			// echo $prv_sql;die;
			$prvRsult = sql_select($prv_sql);	
			foreach($prvRsult as $row)
			{
				$key=$row['COUNTRY_ID'].'*'.$row['ITEM_NUMBER_ID'].'*'.$row['SIZE_NUMBER_ID'];
				if($row['PRODUCTION_TYPE']==81)
				{
					$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['prv_rcv_qty']+=$row['PRODUCTION_QNTY'];
				}
				if($row['PRODUCTION_TYPE']==82 && $row['PURPOSE_ID']==1)
				{
					$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['del_prv_iss_qty']+=$row['PRODUCTION_QNTY'];
				}
				if($row['PRODUCTION_TYPE']==82 && $row['PURPOSE_ID']==2)
				{
					$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['inspc_prv_iss_qty']+=$row['PRODUCTION_QNTY'];
				}
				if($row['PRODUCTION_TYPE']==82 && $row['PURPOSE_ID']==3)
				{
					$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['sales_prv_iss_qty']+=$row['PRODUCTION_QNTY'];
				}
				if($row['PRODUCTION_TYPE']==83)
				{
					$resultArr[$row['BUYER_NAME']][$row['JOB_ID']][$row['PO_ID']][$row['COLOR_NUMBER_ID']][$key]['prv_rtn_qty']+=$row['PRODUCTION_QNTY'];
				}
			}
		}
		$tbl_width=3000;
		ob_start();	
		?>
		<style>
			.wrd_brk{word-break: break-all;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>

		<table width="<?=$tbl_width;?>" border="0">
			<tr class="form_caption" style="border:none;">
				<td colspan="35" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Finish GMTS Transaction report</td> 
			</tr>
		</table>
		<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
			<thead>
				<tr>
					<th width="30" >SL</th>
					<th width="100">Buyer</th>
					<th width="100" >Job No</th>
					<th width="100" >Style</th>
					<th width="100" >PO</th>
					<th width="100" >Country</th>
					<th width="100" >Garments Item</th>
					<th width="100" >Color</th>
					<th width="80" >Size</th>
					<th width="80" >Order Total</th>
					<th width="80" >Previous Received Qty.</th>                    
					<th width="80" >Current Received Qty</th>                    
					<th width="80" >Total Received Qty.</th>                    
					<th width="80" >Received Carton Qty.</th>                    
					<th width="80" >Previous issue Return Qty.</th>                    
					<th width="80" >Current issue Return Qty.</th>                    
					<th width="80" >Total issue Return Qty.</th>                    
					<th width="80" >Previous Delivery Qty. </th>                    
					<th width="80" >Current Delivery Qty. </th>                    
					<th width="80" >Total Delivery Qty. </th>                    
					<th width="80" >Delivery CTN QTY</th>                    
					<th width="80" >Previous Send for Inspection</th>                    
					<th width="80" >Current Send for Inspection</th>                    
					<th width="80" >Total Send for Inspection</th>                    
					<th width="80" >Previous Sales QTY</th>                    
					<th width="80" >Current Sales QTY</th>                    
					<th width="80" >Total Sales QTY</th>                    
					<th width="80" >Stock Qty.</th>                    
					<th width="80" >Rate</th>                    
					<th width="80" >Amount</th>                    
					<th width="80" >Stock CTN Qty.</th>                    
					<th width="100" >Store</th>                    
					<th width="100" >Room No</th>                    
					<th width="100" >Rack No</th>                    
					<th >Shelf No</th>                    
				</tr>
			</thead>
		</table>  
		<div style="width:<?=$tbl_width+18;?>px; overflow-y:scroll; max-height:250px" id="scroll_body" > 
			<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"> 
				<tbody>
					<?	
						$grand_tot_grand_qnty=$grand_tot_prv_rcv_qty=$grand_tot_cur_rcv_qty=$grand_tot_rcv_qnty_bal=$grand_tot_prv_rtn_qty=$grand_tot_cur_rtn_qty=$grand_tot_rtn_qty_bal=$grand_tot_del_prv_iss_qty=$grand_tot_del_cur_iss_qty=$grand_tot_del_iss_qty_bal=$grand_tot_inspc_cur_iss_qty=$grand_tot_inspc_prv_iss_qty=$grand_tot_inspc_cur_iss_qty_bal=$grand_tot_sales_prv_iss_qty=$grand_tot_sales_cur_iss_qty=$grand_tot_sales_iss_qty_bal=$grand_tot_stock_bal=$grand_tot_stock_amount=0;
						$grand_tot_carton_rcv=$grand_tot_carton_iss=$grand_tot_carton_bal=0;
						foreach($resultArr as $buyerId=>$buyerVal)
						{		
							$buyer_tot_buyer_qnty=$buyer_tot_prv_rcv_qty=$buyer_tot_cur_rcv_qty=$buyer_tot_rcv_qnty_bal=$buyer_tot_prv_rtn_qty=$buyer_tot_cur_rtn_qty=$buyer_tot_rtn_qty_bal=$buyer_tot_del_prv_iss_qty=$buyer_tot_del_cur_iss_qty=$buyer_tot_del_iss_qty_bal=$buyer_tot_inspc_cur_iss_qty=$buyer_tot_inspc_prv_iss_qty=$buyer_tot_inspc_cur_iss_qty_bal=$buyer_tot_sales_prv_iss_qty=$buyer_tot_sales_cur_iss_qty=$buyer_tot_sales_iss_qty_bal=$buyer_tot_stock_bal=$buyer_tot_stock_amount=0;
							$buyer_tot_carton_rcv=$buyer_tot_carton_iss=$buyer_tot_carton_bal=0;	
							foreach($buyerVal as $jobId=>$jobVal)
							{
								$job_tot_job_qnty=$job_tot_prv_rcv_qty=$job_tot_cur_rcv_qty=$job_tot_rcv_qnty_bal=$job_tot_prv_rtn_qty=$job_tot_cur_rtn_qty=$job_tot_rtn_qty_bal=$job_tot_del_prv_iss_qty=$job_tot_del_cur_iss_qty=$job_tot_del_iss_qty_bal=$job_tot_inspc_cur_iss_qty=$job_tot_inspc_prv_iss_qty=$job_tot_inspc_cur_iss_qty_bal=$job_tot_sales_prv_iss_qty=$job_tot_sales_cur_iss_qty=$job_tot_sales_iss_qty_bal=$job_tot_stock_bal=$job_tot_stock_amount=0;
								$job_tot_carton_rcv=$job_tot_carton_iss=$job_tot_carton_bal=0;
								foreach($jobVal as $poId=>$poVal)
								{
									$po_tot_po_qnty=$po_tot_prv_rcv_qty=$po_tot_cur_rcv_qty=$po_tot_rcv_qnty_bal=$po_tot_prv_rtn_qty=$po_tot_cur_rtn_qty=$po_tot_rtn_qty_bal=$po_tot_del_prv_iss_qty=$po_tot_del_cur_iss_qty=$po_tot_del_iss_qty_bal=$po_tot_inspc_cur_iss_qty=$po_tot_inspc_prv_iss_qty=$po_tot_inspc_cur_iss_qty_bal=$po_tot_sales_prv_iss_qty=$po_tot_sales_cur_iss_qty=$po_tot_sales_iss_qty_bal=$po_tot_stock_bal=$po_tot_stock_amount=0;
									$po_tot_carton_rcv=$po_tot_carton_iss=$po_tot_carton_bal=0;
									foreach($poVal as $colorId=>$colorVal)
									{
										$color_tot_po_qnty=$color_tot_prv_rcv_qty=$color_tot_cur_rcv_qty=$color_tot_rcv_qnty_bal=$color_tot_prv_rtn_qty=$color_tot_cur_rtn_qty=$color_tot_rtn_qty_bal=$color_tot_del_prv_iss_qty=$color_tot_del_cur_iss_qty=$color_tot_del_iss_qty_bal=$color_tot_inspc_cur_iss_qty=$color_tot_inspc_prv_iss_qty=$color_tot_inspc_cur_iss_qty_bal=$color_tot_sales_prv_iss_qty=$color_tot_sales_cur_iss_qty=$color_tot_sales_iss_qty_bal=$color_tot_stock_bal=$color_tot_stock_amount=0;
										$color_tot_carton_rcv=$color_tot_carton_iss=$color_tot_carton_bal=0;
										$i=1;$k=1;
										$rowspan=count($colorVal);
										foreach($colorVal as $key=>$val)
										{
											if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
											?>
												<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
													<td width="30" align="center"><? echo $i; ?></td>
													<td class="wrd_brk" width="100"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
													<td class="wrd_brk" width="100"><? echo $val["job_no"]; ?></td>
													<td class="wrd_brk" width="100"><? echo $val["style_ref_no"]; ?></td>
													<td class="wrd_brk" width="100"><? echo $val["po_number"]; ?></td>
													<td class="wrd_brk" width="100"><? echo $country_arr[$val["country_id"]]; ?></td>
													<td class="wrd_brk" width="100"><? echo $garments_item[$val["item_id"]]; ?></td>
													<td class="wrd_brk" width="100"><? echo $color_arr[$val["color_id"]]; ?></td>
													<td class="wrd_brk" width="80"><? echo $size_arr[$val["size_id"]]; ?></td>
													<td class="wrd_brkk right" width="80"><? echo $val["order_qnty"]; ?></td>
													<td class="wrd_brk right" width="80" ><? echo $val["prv_rcv_qty"]; ?></td>
													<td class="wrd_brk right" width="80" ><? echo $val["cur_rcv_qty"]; ?></td>
													<td class="wrd_brk right" width="80" ><? $total_rcv_qnty=$val["prv_rcv_qty"]+$val["cur_rcv_qty"]; echo $total_rcv_qnty; ?></td>
													<?
														if($k==1)
														{
															$total_carton_rcv=array_sum($infoArr[$buyerId][$jobId][$poId][$colorId]['cur_rcv_carton']);
															?>
																<td class="wrd_brk right" rowspan="<?=$rowspan;?>" width="80" valign="middle"><? echo $total_carton_rcv; ?></td>
															<?
														}
													?>
													<td class="wrd_brk right" width="80" ><? echo $val["prv_rtn_qty"]; ?></td>
													<td class="wrd_brk right" width="80" ><? echo $val["cur_rtn_qty"]; ?></td>
													<td class="wrd_brk right" width="80" ><? $total_rtn_qnty=$val["prv_rtn_qty"]+$val["cur_rtn_qty"]; echo $total_rtn_qnty;?></td>
													<td class="wrd_brk right" width="80" ><? echo $val["del_prv_iss_qty"]; ?></td>
													<td class="wrd_brk right" width="80" ><? echo $val["del_cur_iss_qty"]; ?></td>
													<td class="wrd_brk right" width="80" ><? $total_iss_del_qnty=$val["del_prv_iss_qty"]+$val["del_cur_iss_qty"]; echo $total_iss_del_qnty;?></td>
													<?
														if($k==1)
														{
															$total_carton_iss=array_sum($infoArr[$buyerId][$jobId][$poId][$colorId]['cur_iss_carton']);
															?>
																<td class="wrd_brk right" rowspan="<?=$rowspan;?>" width="80" valign="middle"><? echo $total_carton_iss; ?></td>
															<?
														}
													?>
													<td class="wrd_brk right" width="80" ><? echo $val["inspc_cur_iss_qty"]; ?></td>
													<td class="wrd_brk right" width="80" ><? echo $val["inspc_prv_iss_qty"]; ?></td>
													<td class="wrd_brk right" width="80" ><? $total_iss_inspc_qnty=$val["inspc_prv_iss_qty"]+$val["inspc_cur_iss_qty"]; echo $total_iss_inspc_qnty;?></td>
													<td class="wrd_brk right" width="80" ><? echo $val["sales_prv_iss_qty"]; ?></td>
													<td class="wrd_brk right" width="80" ><? echo $val["sales_cur_iss_qty"]; ?></td>
													<td class="wrd_brk right" width="80" ><? $total_sales_inspc_qnty=$val["sales_prv_iss_qty"]+$val["sales_cur_iss_qty"]; echo $total_sales_inspc_qnty;?></td>
													<td class="wrd_brk right" width="80" >
														<? 
															$balance_qnty=$total_rcv_qnty-$total_iss_del_qnty-$total_iss_inspc_qnty-$total_sales_inspc_qnty+$total_rtn_qnty;
															echo $balance_qnty; 
														?>
													</td>
													<td class="wrd_brk right" width="80" ><? echo number_format($val["unit_price"],2,'.',''); ?></td>
													<td class="wrd_brk right" width="80" ><? echo number_format($balance_qnty*$val["unit_price"],2,'.',''); ?></td>
													<?
														if($k==1)
														{
															?>
																<td class="wrd_brk right" rowspan="<?=$rowspan;?>" width="80" valign="middle"><? echo $total_carton_rcv-$total_carton_iss; ?></td>
																<td class="wrd_brk" rowspan="<?=$rowspan;?>" width="100" valign="middle">
																	<?
																		$store_id_arr=array_unique(explode(",",chop($infoArr[$buyerId][$jobId][$poId][$colorId]['store_id'],',')));
																		$store_name='';
																		foreach($store_id_arr as $row)
																		{
																			$store_name.=$store_arr[$row].', ';
																		}
																		echo rtrim($store_name,', ');
																	?>
																</td>
																<td class="wrd_brk" rowspan="<?=$rowspan;?>" width="100" valign="middle">
																	<?
																		$room_id_arr=array_unique(explode(",",chop($infoArr[$buyerId][$jobId][$poId][$colorId]['room_id'],',')));
																		$room_name='';
																		foreach($room_id_arr as $row)
																		{
																			$room_name.=$room_rack_self_arr[$row].', ';
																		}
																		echo rtrim($room_name,', ');
																	?>
																</td>
																<td class="wrd_brk" rowspan="<?=$rowspan;?>" width="100" valign="middle">
																	<?
																		$rack_id_arr=array_unique(explode(",",chop($infoArr[$buyerId][$jobId][$poId][$colorId]['rack_id'],',')));
																		$rack_name='';
																		foreach($rack_id_arr as $row)
																		{
																			$rack_name.=$room_rack_self_arr[$row].', ';
																		}
																		echo rtrim($rack_name,', ');
																	?>
																</td>
																<td class="wrd_brk" rowspan="<?=$rowspan;?>" valign="middle">
																	<?
																		$shelf_id_arr=array_unique(explode(",",chop($infoArr[$buyerId][$jobId][$poId][$colorId]['shelf_id'],',')));
																		$shelf_name='';
																		foreach($shelf_id_arr as $row)
																		{
																			$shelf_name.=$room_rack_self_arr[$row].', ';
																		}
																		echo rtrim($shelf_name,', ');
																	?>
																</td>
																
															<?
															$color_tot_carton_rcv+=$total_carton_rcv;		
															$color_tot_carton_iss+=$total_carton_iss;		
															$color_tot_carton_bal+=$total_carton_rcv-$total_carton_iss;	

															$po_tot_carton_rcv+=$total_carton_rcv;		
															$po_tot_carton_iss+=$total_carton_iss;		
															$po_tot_carton_bal+=$total_carton_rcv-$total_carton_iss;	

															$job_tot_carton_rcv+=$total_carton_rcv;		
															$job_tot_carton_iss+=$total_carton_iss;		
															$job_tot_carton_bal+=$total_carton_rcv-$total_carton_iss;	

															$buyer_tot_carton_rcv+=$total_carton_rcv;		
															$buyer_tot_carton_iss+=$total_carton_iss;		
															$buyer_tot_carton_bal+=$total_carton_rcv-$total_carton_iss;	

															$grand_tot_carton_rcv+=$total_carton_rcv;		
															$grand_tot_carton_iss+=$total_carton_iss;		
															$grand_tot_carton_bal+=$total_carton_rcv-$total_carton_iss;	
														}
													?>
												</tr>
											<?
											$i++;$k++;	
											$color_tot_po_qnty+=$val["order_qnty"];							
											$color_tot_prv_rcv_qty+=$val["prv_rcv_qty"];							
											$color_tot_cur_rcv_qty+=$val["cur_rcv_qty"];							
											$color_tot_rcv_qnty_bal+=$total_rcv_qnty;	
											$color_tot_prv_rtn_qty+=$val["prv_rtn_qty"];							
											$color_tot_cur_rtn_qty+=$val["cur_rtn_qty"];							
											$color_tot_rtn_qty_bal+=$total_rtn_qnty;						
											$color_tot_del_prv_iss_qty+=$val["del_prv_iss_qty"];							
											$color_tot_del_cur_iss_qty+=$val["del_cur_iss_qty"];							
											$color_tot_del_iss_qty_bal+=$total_iss_del_qnty;							
											$color_tot_inspc_cur_iss_qty+=$val["inspc_cur_iss_qty"];							
											$color_tot_inspc_prv_iss_qty+=$val["inspc_prv_iss_qty"];							
											$color_tot_inspc_cur_iss_qty_bal+=$total_iss_inspc_qnty;							
											$color_tot_sales_prv_iss_qty+=$val["sales_prv_iss_qty"];							
											$color_tot_sales_cur_iss_qty+=$val["sales_cur_iss_qty"];							
											$color_tot_sales_iss_qty_bal+=$total_sales_inspc_qnty;		
											$color_tot_stock_bal+=$balance_qnty;		
											$color_tot_stock_amount+=$balance_qnty*$val["unit_price"];	

											$po_tot_po_qnty+=$val["order_qnty"];							
											$po_tot_prv_rcv_qty+=$val["prv_rcv_qty"];							
											$po_tot_cur_rcv_qty+=$val["cur_rcv_qty"];							
											$po_tot_rcv_qnty_bal+=$total_rcv_qnty;	
											$po_tot_prv_rtn_qty+=$val["prv_rtn_qty"];							
											$po_tot_cur_rtn_qty+=$val["cur_rtn_qty"];							
											$po_tot_rtn_qty_bal+=$total_rtn_qnty;						
											$po_tot_del_prv_iss_qty+=$val["del_prv_iss_qty"];							
											$po_tot_del_cur_iss_qty+=$val["del_cur_iss_qty"];							
											$po_tot_del_iss_qty_bal+=$total_iss_del_qnty;							
											$po_tot_inspc_cur_iss_qty+=$val["inspc_cur_iss_qty"];							
											$po_tot_inspc_prv_iss_qty+=$val["inspc_prv_iss_qty"];							
											$po_tot_inspc_cur_iss_qty_bal+=$total_iss_inspc_qnty;							
											$po_tot_sales_prv_iss_qty+=$val["sales_prv_iss_qty"];							
											$po_tot_sales_cur_iss_qty+=$val["sales_cur_iss_qty"];							
											$po_tot_sales_iss_qty_bal+=$total_sales_inspc_qnty;		
											$po_tot_stock_bal+=$balance_qnty;		
											$po_tot_stock_amount+=$balance_qnty*$val["unit_price"];	

											$job_tot_po_qnty+=$val["order_qnty"];							
											$job_tot_prv_rcv_qty+=$val["prv_rcv_qty"];							
											$job_tot_cur_rcv_qty+=$val["cur_rcv_qty"];							
											$job_tot_rcv_qnty_bal+=$total_rcv_qnty;	
											$job_tot_prv_rtn_qty+=$val["prv_rtn_qty"];							
											$job_tot_cur_rtn_qty+=$val["cur_rtn_qty"];							
											$job_tot_rtn_qty_bal+=$total_rtn_qnty;						
											$job_tot_del_prv_iss_qty+=$val["del_prv_iss_qty"];							
											$job_tot_del_cur_iss_qty+=$val["del_cur_iss_qty"];							
											$job_tot_del_iss_qty_bal+=$total_iss_del_qnty;							
											$job_tot_inspc_cur_iss_qty+=$val["inspc_cur_iss_qty"];							
											$job_tot_inspc_prv_iss_qty+=$val["inspc_prv_iss_qty"];							
											$job_tot_inspc_cur_iss_qty_bal+=$total_iss_inspc_qnty;							
											$job_tot_sales_prv_iss_qty+=$val["sales_prv_iss_qty"];							
											$job_tot_sales_cur_iss_qty+=$val["sales_cur_iss_qty"];							
											$job_tot_sales_iss_qty_bal+=$total_sales_inspc_qnty;		
											$job_tot_stock_bal+=$balance_qnty;		
											$job_tot_stock_amount+=$balance_qnty*$val["unit_price"];		

											$buyer_tot_po_qnty+=$val["order_qnty"];							
											$buyer_tot_prv_rcv_qty+=$val["prv_rcv_qty"];							
											$buyer_tot_cur_rcv_qty+=$val["cur_rcv_qty"];							
											$buyer_tot_rcv_qnty_bal+=$total_rcv_qnty;	
											$buyer_tot_prv_rtn_qty+=$val["prv_rtn_qty"];							
											$buyer_tot_cur_rtn_qty+=$val["cur_rtn_qty"];							
											$buyer_tot_rtn_qty_bal+=$total_rtn_qnty;						
											$buyer_tot_del_prv_iss_qty+=$val["del_prv_iss_qty"];							
											$buyer_tot_del_cur_iss_qty+=$val["del_cur_iss_qty"];							
											$buyer_tot_del_iss_qty_bal+=$total_iss_del_qnty;							
											$buyer_tot_inspc_cur_iss_qty+=$val["inspc_cur_iss_qty"];							
											$buyer_tot_inspc_prv_iss_qty+=$val["inspc_prv_iss_qty"];							
											$buyer_tot_inspc_cur_iss_qty_bal+=$total_iss_inspc_qnty;							
											$buyer_tot_sales_prv_iss_qty+=$val["sales_prv_iss_qty"];							
											$buyer_tot_sales_cur_iss_qty+=$val["sales_cur_iss_qty"];							
											$buyer_tot_sales_iss_qty_bal+=$total_sales_inspc_qnty;		
											$buyer_tot_stock_bal+=$balance_qnty;		
											$buyer_tot_stock_amount+=$balance_qnty*$val["unit_price"];		
			
											$grand_tot_po_qnty+=$val["order_qnty"];							
											$grand_tot_prv_rcv_qty+=$val["prv_rcv_qty"];							
											$grand_tot_cur_rcv_qty+=$val["cur_rcv_qty"];							
											$grand_tot_rcv_qnty_bal+=$total_rcv_qnty;	
											$grand_tot_prv_rtn_qty+=$val["prv_rtn_qty"];							
											$grand_tot_cur_rtn_qty+=$val["cur_rtn_qty"];							
											$grand_tot_rtn_qty_bal+=$total_rtn_qnty;						
											$grand_tot_del_prv_iss_qty+=$val["del_prv_iss_qty"];							
											$grand_tot_del_cur_iss_qty+=$val["del_cur_iss_qty"];							
											$grand_tot_del_iss_qty_bal+=$total_iss_del_qnty;							
											$grand_tot_inspc_cur_iss_qty+=$val["inspc_cur_iss_qty"];							
											$grand_tot_inspc_prv_iss_qty+=$val["inspc_prv_iss_qty"];							
											$grand_tot_inspc_cur_iss_qty_bal+=$total_iss_inspc_qnty;							
											$grand_tot_sales_prv_iss_qty+=$val["sales_prv_iss_qty"];							
											$grand_tot_sales_cur_iss_qty+=$val["sales_cur_iss_qty"];							
											$grand_tot_sales_iss_qty_bal+=$total_sales_inspc_qnty;		
											$grand_tot_stock_bal+=$balance_qnty;		
											$grand_tot_stock_amount+=$balance_qnty*$val["unit_price"];		
			
										}
										?>
											<tr bgcolor="#DDEBF7">
												<td colspan="9" class="right"><b>Color Total</b> </td>
												<td class="wrd_brk right"><?=$color_tot_po_qnty;?></td>
												<td class="wrd_brk right"><?=$color_tot_prv_rcv_qty;?></td>
												<td class="wrd_brk right"><?=$color_tot_cur_rcv_qty;?></td>
												<td class="wrd_brk right"><?=$color_tot_rcv_qnty_bal;?></td>
												<td class="wrd_brk right"><?=$color_tot_carton_rcv;?></td>
												<td class="wrd_brk right"><?=$color_tot_prv_rtn_qty;?></td>
												<td class="wrd_brk right"><?=$color_tot_cur_rtn_qty;?></td>
												<td class="wrd_brk right"><?=$color_tot_rtn_qty_bal;?></td>
												<td class="wrd_brk right"><?=$color_tot_del_prv_iss_qty;?></td>
												<td class="wrd_brk right"><?=$color_tot_del_cur_iss_qty;?></td>
												<td class="wrd_brk right"><?=$color_tot_del_iss_qty_bal;?></td>
												<td class="wrd_brk right"><?=$color_tot_carton_iss;?></td>
												<td class="wrd_brk right"><?=$color_tot_inspc_cur_iss_qty;?></td>
												<td class="wrd_brk right"><?=$color_tot_inspc_prv_iss_qty;?></td>
												<td class="wrd_brk right"><?=$color_tot_inspc_cur_iss_qty_bal;?></td>
												<td class="wrd_brk right"><?=$color_tot_sales_prv_iss_qty;?></td>
												<td class="wrd_brk right"><?=$color_tot_sales_cur_iss_qty;?></td>
												<td class="wrd_brk right"><?=$color_tot_sales_iss_qty_bal;?></td>
												<td class="wrd_brk right"><?=$color_tot_stock_bal;?></td>
												<td class="wrd_brk right"><? ?></td>
												<td class="wrd_brk right"><?=number_format($color_tot_stock_amount,2);?></td>
												<td class="wrd_brk right"><?=$color_tot_carton_bal;?></td>
												<td colspan="4"></td>
											</tr>
										<?
									}
									?>
										<tr bgcolor="#B4C6E7">
											<td colspan="9" class="right"><b>PO Total</b></td>
											<td class="wrd_brk right"><?=$po_tot_po_qnty;?></td>
											<td class="wrd_brk right"><?=$po_tot_prv_rcv_qty;?></td>
											<td class="wrd_brk right"><?=$po_tot_cur_rcv_qty;?></td>
											<td class="wrd_brk right"><?=$po_tot_rcv_qnty_bal;?></td>
											<td class="wrd_brk right"><?=$po_tot_carton_rcv;?></td>
											<td class="wrd_brk right"><?=$po_tot_prv_rtn_qty;?></td>
											<td class="wrd_brk right"><?=$po_tot_cur_rtn_qty;?></td>
											<td class="wrd_brk right"><?=$po_tot_rtn_qty_bal;?></td>
											<td class="wrd_brk right"><?=$po_tot_del_prv_iss_qty;?></td>
											<td class="wrd_brk right"><?=$po_tot_del_cur_iss_qty;?></td>
											<td class="wrd_brk right"><?=$po_tot_del_iss_qty_bal;?></td>
											<td class="wrd_brk right"><?=$po_tot_carton_iss;?></td>
											<td class="wrd_brk right"><?=$po_tot_inspc_cur_iss_qty;?></td>
											<td class="wrd_brk right"><?=$po_tot_inspc_prv_iss_qty;?></td>
											<td class="wrd_brk right"><?=$po_tot_inspc_cur_iss_qty_bal;?></td>
											<td class="wrd_brk right"><?=$po_tot_sales_prv_iss_qty;?></td>
											<td class="wrd_brk right"><?=$po_tot_sales_cur_iss_qty;?></td>
											<td class="wrd_brk right"><?=$po_tot_sales_iss_qty_bal;?></td>
											<td class="wrd_brk right"><?=$po_tot_stock_bal;?></td>
											<td class="wrd_brk right"><? ?></td>
											<td class="wrd_brk right"><?=number_format($po_tot_stock_amount,2);?></td>
											<td class="wrd_brk right"><?=$po_tot_carton_bal;?></td>
											<td colspan="4"></td>
										</tr>
									<?
								}
								?>
									<tr bgcolor="#FFE699">
										<td colspan="9" class="right"><b>Job Total</b> </td>
										<td class="wrd_brk right"><?=$job_tot_po_qnty;?></td>
										<td class="wrd_brk right"><?=$job_tot_prv_rcv_qty;?></td>
										<td class="wrd_brk right"><?=$job_tot_cur_rcv_qty;?></td>
										<td class="wrd_brk right"><?=$job_tot_rcv_qnty_bal;?></td>
										<td class="wrd_brk right"><?=$job_tot_carton_rcv;?></td>
										<td class="wrd_brk right"><?=$job_tot_prv_rtn_qty;?></td>
										<td class="wrd_brk right"><?=$job_tot_cur_rtn_qty;?></td>
										<td class="wrd_brk right"><?=$job_tot_rtn_qty_bal;?></td>
										<td class="wrd_brk right"><?=$job_tot_del_prv_iss_qty;?></td>
										<td class="wrd_brk right"><?=$job_tot_del_cur_iss_qty;?></td>
										<td class="wrd_brk right"><?=$job_tot_del_iss_qty_bal;?></td>
										<td class="wrd_brk right"><?=$job_tot_carton_iss;?></td>
										<td class="wrd_brk right"><?=$job_tot_inspc_cur_iss_qty;?></td>
										<td class="wrd_brk right"><?=$job_tot_inspc_prv_iss_qty;?></td>
										<td class="wrd_brk right"><?=$job_tot_inspc_cur_iss_qty_bal;?></td>
										<td class="wrd_brk right"><?=$job_tot_sales_prv_iss_qty;?></td>
										<td class="wrd_brk right"><?=$job_tot_sales_cur_iss_qty;?></td>
										<td class="wrd_brk right"><?=$job_tot_sales_iss_qty_bal;?></td>
										<td class="wrd_brk right"><?=$job_tot_stock_bal;?></td>
										<td class="wrd_brk right"><? ?></td>
										<td class="wrd_brk right"><?=number_format($job_tot_stock_amount,2);?></td>
										<td class="wrd_brk right"><?=$job_tot_carton_bal;?></td>
										<td colspan="4"></td>
									</tr>
								<?
							}
							?>
								<tr bgcolor="#FFC000">
									<td colspan="9" class="right"><b>Buyer TTL</b> </td>
									<td class="wrd_brk right"><?=$buyer_tot_po_qnty;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_prv_rcv_qty;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_cur_rcv_qty;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_rcv_qnty_bal;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_carton_rcv;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_prv_rtn_qty;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_cur_rtn_qty;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_rtn_qty_bal;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_del_prv_iss_qty;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_del_cur_iss_qty;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_del_iss_qty_bal;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_carton_iss;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_inspc_cur_iss_qty;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_inspc_prv_iss_qty;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_inspc_cur_iss_qty_bal;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_sales_prv_iss_qty;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_sales_cur_iss_qty;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_sales_iss_qty_bal;?></td>
									<td class="wrd_brk right"><?=$buyer_tot_stock_bal;?></td>
									<td class="wrd_brk right"><? ?></td>
									<td class="wrd_brk right"><?=number_format($buyer_tot_stock_amount,2);?></td>
									<td class="wrd_brk right"><?=$buyer_tot_carton_bal;?></td>
									<td colspan="4"></td>
								</tr>
							<?
						} 
					?>
				</tbody>  
				<tfoot>
					<tr bgcolor="#A9D08E">
						<td colspan="9" class="right"> <b>GTTL</b> </td>
						<td class="wrd_brk right"><?=$buyer_tot_po_qnty;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_prv_rcv_qty;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_cur_rcv_qty;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_rcv_qnty_bal;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_carton_rcv;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_prv_rtn_qty;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_cur_rtn_qty;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_rtn_qty_bal;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_del_prv_iss_qty;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_del_cur_iss_qty;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_del_iss_qty_bal;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_carton_iss;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_inspc_cur_iss_qty;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_inspc_prv_iss_qty;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_inspc_cur_iss_qty_bal;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_sales_prv_iss_qty;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_sales_cur_iss_qty;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_sales_iss_qty_bal;?></td>
						<td class="wrd_brk right"><?=$buyer_tot_stock_bal;?></td>
						<td class="wrd_brk right"><? ?></td>
						<td class="wrd_brk right"><?=number_format($buyer_tot_stock_amount,2);?></td>
						<td class="wrd_brk right"><?=$buyer_tot_carton_bal;?></td>
						<td colspan="4"></td>
					</tr>
				</tfoot>
			</table> 
		</div>
			
		<?
	}
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$rpt_type"; 
	exit();
}
?>