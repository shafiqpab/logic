<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$type=$_REQUEST['type'];

//working 
if($action=="print_button_variable_setting")
{
	$print_report_format = return_field_value("format_id","lib_report_template","template_name in($data) and module_id=4 and report_id=294 and is_deleted=0 and status_active=1");
	$buttonHtml ='';
	$printButton = explode(",", $print_report_format);
	foreach($printButton as $id){
		if($id==108)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generate(1);"/>';	
		if($id==195)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show 2" onClick="fn_report_generate(2);"/>';
		if($id==242)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show 3" onClick="fn_report_generate(3);"/>';	
	}
    echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}
//Get Buyers List Show
if($action=="load_drop_down_buyers")
{
    echo create_drop_down("cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data)  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, "");
    exit();
}
// Get Brand List Show
if($action=="load_drop_down_brands")
{
	$data_arr = explode("_", $data);
	$company_id = $data_arr[0];
	$buyer_id = $data_arr[1]; 
    echo create_drop_down("cbo_brand_name", 100, "select id, brand_name from lib_buyer_brand where buyer_id in ($buyer_id) and status_active =1 and is_deleted=0 order by brand_name ASC","id,brand_name", 1, "--Select--", "", "" );
	exit();
}
// Job No Popup Show
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');

	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			$("#hide_job_no").val(str);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" >
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="470" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
					<th>Company Name</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
                    </th>
                </thead>
                <tbody>
                	<tr>

					<td>
                        <?= create_drop_down( "cbo_working_company_name", 142, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select  --", $selected, "" );?>
                        </td>

                        <td align="center">
                        	<?= create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );?>
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $sytle_ref_no; ?>'+'**'+'<? echo $cbo_year; ?>', 'create_job_no_search_list_view', 'search_div', 'plan_wise_shipment_schedule_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}
// Job Show
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";
	$company_id = $data[0];
	$year_id = $data[5];
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

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
	$year="year(a.insert_date)";
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

	 
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	 
	$sql= "SELECT a.id,a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a where  a.status_active=1 and a.is_deleted=0 and company_name=$company_id and  $search_field  like '$search_string' $buyer_id_cond $year_cond order by a.insert_date DESC";
    // echo $sql;die();  // order by a.insert_date DESC

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no,year,style_ref_no", "",'','','') ;
	exit();
} 
// Order No Popup Show
if($action=="orderno_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	?>
	<script>
		
		var selected_id = new Array();
		var selected_name = new Array();
		
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
			}
		</script>
	<?
	extract($_REQUEST);
	//echo $job_no;die;
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$job_cond='';
	if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and a.job_no_mst='".$job_no."'";
	else if($cbo_year!=0)
	{
		if($db_type==0) $job_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
		if($db_type==2) $job_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	
	$sql = "SELECT distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,b.insert_date from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active in(1,2,3) and company_name in ($companyID) $job_cond  $buyer_name $style_cond";
	// echo $sql;die;
	echo create_list_view("list_view", "Year,Job No,Style Ref,Order Number","50,100,120,150,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

// report generate
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id = str_replace("'", "", $cbo_company_id);
	$cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
	$cbo_brand_id = str_replace("'", "",$cbo_brand_name);
    $txt_job_no = str_replace("'", "", $txt_job_no);
    $txt_order_no = trim(str_replace("'", "", $txt_order_no));
    $cbo_ship_status = str_replace("'", "", $cbo_ship_status);
    $cbo_date_category = str_replace("'", "", $cbo_date_category);
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);

    $company_arr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$brand_arr = return_library_array("select id, brand_name from lib_buyer_brand",'id','brand_name');
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	 
	$company_id = str_replace('"', '', $company_id);

    if($cbo_company_id){$company_con = " and a.company_name in($company_id)";}
	else{$company_con="";}

    if($cbo_buyer_id){$buyer_con = " and a.buyer_name in($cbo_buyer_id)";}
	else{$buyer_con="";}
 
    if($cbo_brand_id){$brand_con = " and a.brand_id in($cbo_brand_id)";}
	else{$brand_con="";} 

	if($txt_job_no){$job_con = " and a.job_no_prefix_num = '$txt_job_no'";}
	else{$job_con="";}

  

	$txt_order_number = trim($txt_order_no);
	if($txt_order_id == "")
	{
		$txt_order_number_expl = explode(",",$txt_order_number);
		$poDatas = "";
		foreach($txt_order_number_expl as $poData)
		{
			$poDatas.="'".$poData."'".",";
		}
		$poNumbers=chop($poDatas,",");
		
		if($txt_order_number != "")
		{
			$order_con = " and b.po_number in($poNumbers)";
		}
		else
		{
			$order_con = "";
		}
	}

	// echo $order_con;die;
	 
    if($cbo_ship_status){$ship_status_con = " and b.shiping_status in($cbo_ship_status)";}
	else{$ship_status_con="";}
  
	$date_category_status_date = '';
	if($date_from && $date_to){
		// date category
		if($cbo_date_category==1) // Pub Ship Date
		{
			$date_category_status_date = " AND b.pub_shipment_date between '$date_from' and '$date_to'";
		}
		else if($cbo_date_category == 2) // Org. Ship Date
		{
			$date_category_status_date = " AND b.shipment_date between '$date_from' and '$date_to'";
		}
		else if($cbo_date_category == 3) // Country Ship Date
		{
			$date_category_status_date = " AND c.country_ship_date between '$date_from' and '$date_to'";
		} 
		else if($cbo_date_category == 4) // PHD/PCD Ship Date
		{
			$date_category_status_date = " AND b.pack_handover_date between '$date_from' and '$date_to'";
		}
		else if($cbo_date_category == 5) // Plan Date
		{
			$date_category_status_date=" AND e.START_DATE between '$date_from' and '$date_to' and e.END_DATE between '$date_from' and '$date_to'";
		}
    }

	$sql ="SELECT a.id, a.garments_nature, a.order_uom, a.company_name, a.style_owner, a.working_company_id, a.working_location_id,a.dealing_marchant, a.location_name, a.job_no, a.style_ref_no, a.gmts_item_id, a.season_buyer_wise AS season_id, a.set_smv, a.set_break_down, a.buyer_name, a.total_set_qnty, a.team_leader, a.product_dept, a.season_year, a.brand_id, b.pub_shipment_date, b.pub_shipment_date AS pub_ship,b.pack_handover_date,b.shipment_date AS actual_shipment_date, b.po_received_date, b.id AS po_id, b.po_number, b.shiping_status, b.is_confirmed, b.shipment_date, b.unit_price, b.insert_date, b.GROUPING, d.color_number_id, c.PLAN_CUT_QNTY, d.PLAN_QNTY, d.PUB_SHIPMENT_DATE,e.START_DATE,e.END_DATE,e.plan_id FROM wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c, ppl_sewing_plan_board_powise  d, PPL_SEWING_PLAN_BOARD e WHERE a.garments_nature = 3 AND a.job_no = b.job_no_mst AND b.id = c.po_break_down_id AND c.po_break_down_id= d.po_break_down_id and d.plan_id=e.plan_id $company_con $buyer_con $brand_con $job_con $order_con $ship_status_con $date_category_status_date AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 AND c.STATUS_ACTIVE=1 AND c.IS_DELETED=0";
    // echo $sql;die; 
  
    $sql_data = sql_select($sql);
    $order_wise_array2 = array();
    $order_wise_array = array();
	$all_po_id_arr    = array();
	$all_color_id_arr = array();
	$po_id_arr = array();
    foreach( $sql_data as $row)
	{
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['po_id']        = $row[csf('po_id')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['company_name'] = $row[csf('company_name')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['working_company_id'] = $row[csf('working_company_id')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['buyer_name']   = $row[csf('buyer_name')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['brand_id']     = $row[csf('brand_id')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['season_year']  = $row[csf('season_year')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['job_no']       = $row[csf('job_no')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['style_ref_no'] = $row[csf('style_ref_no')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['po_id']        = $row[csf('po_id')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['gmts_item_id'] = $row[csf('gmts_item_id')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['color_number_id']      = $row[csf('color_number_id')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['style_owner']          = $row[csf('style_owner')]; 
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['pack_handover_date']   = $row[csf('pack_handover_date')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['pub_ship']             = $row[csf('pub_ship')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['actual_shipment_date'] = $row[csf('actual_shipment_date')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['shiping_status'] = $row[csf('shiping_status')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['is_confirmed']   = $row[csf('is_confirmed')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['po_number']      = $row[csf('PO_NUMBER')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['plan_cut_qnty']  = $row[csf('PLAN_CUT_QNTY')];
        $order_wise_array[$row[csf('po_id')]][$row[csf('color_number_id')]]['plan_qnty']      = $row[csf('PLAN_QNTY')];
		
		$all_po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		$all_color_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		$po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
    } 

	// echo "<pre>";
    // print_r($order_wise_array);
    // die;
 
	$color_sql = "select id,job_no_mst, item_number_id, color_number_id, po_break_down_id, color_order from wo_po_color_size_breakdown where is_deleted=0 and status_active in (1,2,3) ".where_con_using_array($all_po_id_arr,1,'po_break_down_id')." order by color_order";
	// echo $color_sql; die;
	$color_data = sql_select($color_sql);
	$po_color_arr = array();
	foreach($color_data as $row){
		$po_color_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]] = $color_library[$row[csf('color_number_id')]];
	}


	// plan cutiing qnty 
    $cut_qty_sql = "SELECT id, marker_qty, order_ids, COLOR_ID FROM ppl_cut_lay_dtls WHERE ORDER_IDS in('".implode("','", $all_po_id_arr)."') and  status_active=1 and is_deleted=0";
	$cut_qty_sql_res = sql_select($cut_qty_sql);
	$order_cutting_arr = array();
    foreach($cut_qty_sql_res as $rows)
	{  
		$order_cutting_arr[$rows['ORDER_IDS']][$rows['COLOR_ID']] = $rows['MARKER_QTY'];
	}
	 
    // sewing input 
	$sewing_input_sql = "SELECT c.id as prdid, d.id as colorsizeid, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number, e.id as po_id from pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e WHERE  c.color_size_break_down_id=d.id  and d.po_break_down_id=e.id  and c.production_type=4 and c.status_active=1 and c.is_deleted=0  AND e.ID IN('".implode("','", $all_po_id_arr)."')";
	 
	//".where_con_using_array($all_color_id_arr,0,'color_size_break_down_id')."";
	//echo $sewing_input_sql ;die;
 
	$sewing_input_sql_res = sql_select($sewing_input_sql);
	$sewing_input_arr = array();
	foreach($sewing_input_sql_res as $rows)
	{  
		$sewing_input_arr[$rows['PO_ID']][$rows['COLOR_NUMBER_ID']]['sewing_input'] += $rows['PRODUCTION_QNTY'];
	}

	//print_r($sewing_input_arr);die;
	  
	// sewing output 
	$sewing_output_sql = "SELECT c.id as prdid, d.id as colorsizeid, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number, e.id as po_id from pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e WHERE  c.color_size_break_down_id=d.id  and d.po_break_down_id=e.id  and c.production_type=5 and c.status_active=1 and c.is_deleted=0  AND e.ID IN('".implode("','", $all_po_id_arr)."')";
    // echo $sql ;die;
	$sewing_output_sql_res = sql_select($sewing_output_sql);
	$sewing_output_arr = array();
	foreach($sewing_output_sql_res as $rows)
	{  
		$sewing_output_arr[$rows['PO_ID']][$rows['COLOR_NUMBER_ID']]['sewing_output'] += $rows['PRODUCTION_QNTY'];
	}
 
	$wash_send_sql = "SELECT a.color_size_break_down_id,b.PO_BREAK_DOWN_ID,c.COLOR_NUMBER_ID,
			SUM (CASE WHEN a.production_type = 5 THEN a.production_qnty ELSE 0 END) AS production_qnty,
			SUM (CASE WHEN     a.production_type = 2 AND b.embel_name = 3 AND b.entry_form = 645 
			THEN
					a.production_qnty
				ELSE
					0
			END) AS cur_production_qnty FROM pro_garments_production_dtls a, pro_garments_production_mst b, WO_PO_COLOR_SIZE_BREAKDOWN c WHERE a.mst_id = b.id and a.color_size_break_down_id=c.id AND b.po_break_down_id IN('".implode("','", $all_po_id_arr)."') AND a.entry_form = 645 AND a.production_type IN (2) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY a.color_size_break_down_id,b.PO_BREAK_DOWN_ID,c.COLOR_NUMBER_ID";

   
	$wash_sql_res = sql_select($wash_send_sql);
	$wash_send_arr = array();
	foreach($wash_sql_res as $row)
	{
		$wash_send_arr[$row['PO_BREAK_DOWN_ID']][$row['COLOR_NUMBER_ID']]['wash_send'] += $row['CUR_PRODUCTION_QNTY'];
	}
	//echo "<pre>";
	//print_r($wash_send_arr);die;

	$wash_receive_sql = "SELECT a.color_size_break_down_id,b.PO_BREAK_DOWN_ID,c.COLOR_NUMBER_ID,
			SUM (CASE WHEN a.production_type = 5 THEN a.production_qnty ELSE 0 END) AS production_qnty,
			SUM (CASE WHEN  a.production_type = 3 AND b.embel_name = 3 AND b.entry_form = 648 
			THEN
					a.production_qnty
				ELSE
					0
			END) AS cur_production_qnty FROM pro_garments_production_dtls a, pro_garments_production_mst b, WO_PO_COLOR_SIZE_BREAKDOWN c WHERE a.mst_id = b.id and a.color_size_break_down_id=c.id AND b.po_break_down_id IN('".implode("','", $all_po_id_arr)."') AND a.entry_form = 648 AND a.production_type =3 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY a.color_size_break_down_id,b.PO_BREAK_DOWN_ID,c.COLOR_NUMBER_ID";

	//echo $wash_receive_sql;die;
	$wash_sql_res = sql_select($wash_receive_sql);
	$wash_received_arr = array();
	foreach($wash_sql_res as $row)
	{
		$wash_received_arr[$row['PO_BREAK_DOWN_ID']][$row['COLOR_NUMBER_ID']]['wash_received'] += $row['CUR_PRODUCTION_QNTY'];
	}
 

	$dataSql = "SELECT SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalsewing, po_break_down_id, production_type from pro_garments_production_mst WHERE po_break_down_id in (".implode(",",$po_id_arr).") and status_active=1 and is_deleted=0 group by production_type,po_break_down_id";
	$packing_sql_res = sql_select($dataSql);
	$packing_arr = array();
	foreach($packing_sql_res as $rows)
	{
		$packing_arr[$rows['PO_BREAK_DOWN_ID']]['packing_carton'] += $rows['TOTALSEWING'];
	}

	$factory_sql = "SELECT ex_factory_date,ex_factory_qnty,po_break_down_id from pro_ex_factory_mst WHERE po_break_down_id in (".implode(",",$po_id_arr).") and status_active=1 and is_deleted=0";
	$factory_sql_res = sql_select($factory_sql);
	$factory_arr = array();
	foreach($factory_sql_res as $rows)
	{  
		$factory_arr[$rows['PO_BREAK_DOWN_ID']]['factory_qnty'] += $rows['EX_FACTORY_QNTY'];
		$factory_arr[$rows['PO_BREAK_DOWN_ID']]['factory_date'] = $rows['EX_FACTORY_DATE'];
	} 
 
	//echo $finishingSql;die;

	$finishingSql="SELECT b.color_size_break_down_id, b.production_qnty,b.alter_qty, b.spot_qty, b.reject_qty, b.re_production_qty, c.po_break_down_id AS po_id, c.color_number_id, a.production_type FROM pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE  a.id = b.mst_id and b.COLOR_SIZE_BREAK_DOWN_ID = c.id and a.po_break_down_id in (".implode(",",$po_id_arr).") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0 AND a.production_type = 7";

	//echo $finishingSql;die;
 
	$finishing_res = sql_select($finishingSql);
	$finishing_arr = array();
	foreach($finishing_res as $row)
	{ 
		$finishing_arr[$row['PO_ID']][$row['COLOR_NUMBER_ID']]['finishin_qnty'] += $row['PRODUCTION_QNTY'];
	}

	// echo "<pre>";
    // print_r($finishing_arr);
    // die;


	 // 713 7

	//  $sql_dtls;die;
	ob_start();
  
    ?>
    <br>
    <br>
    <div style="width:2820px;">
		<div align="center">
			<table width="300" border="0" cellpadding="2" cellspacing="0"> 
				<thead>
					<tr class="form_caption">
						<td colspan="30" align="center" style="font-size:16px; font-weight:bold">Plan Wise Shipment Schedule Report</td> 
					</tr> 
					<tr class="form_caption">
						<td colspan="30" align="center"><?= $date_category_arr[$cbo_date_cat_id].' ('. change_date_format($date_from).' To '.change_date_format($date_to); ?>)</td> 
					</tr>
				</thead>
			</table>
		<div>
		<table width="2820" id="table_header_1" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Plan Company</th>
					<th width="100">Lc Company</th>
					<th width="100">Buyer</th>
					<th width="100">Brand</th>
					<th width="100">Year</th>
					<th width="100">Job No</th>
					<th width="120">Style Ref</th>
					<th width="100">PO NO</th>
					<th width="100">Garment Item</th>
					<th width="200">Color</th>
					<th width="80">Color Qty (Pcs)</th>
					<th width="80">Cutting Qty</th>
					<th width="80">Sewing Input</th>
					<th width="80">Sewing Output</th>
					<th width="120">Sewing Balance</th>
					<th width="80">Wash Send</th>
					<th width="80">Wash Rcvd</th>
					<th width="80">Wash Balance</th>
					<th width="80">Finishing (Getup Pass)</th>
					<th width="80">Finishing (Getup Pass) Balance</th>
					<th width="80">Packing/Carton</th>
					<th width="50">Packing / Carton WIP</th>
					<th width="100">PHD Date</th>
					<th width="100" title="Order Qty (Pcs)*SMV">Pub. Shipment</th>
					<th width="70">PO Shipment Date</th>
					<th width="100">Actual Ex-Factory Date</th>
					<th width="90">Actual Ex-Factory Qty (Pcs)</th>
					<th width="90">Shipping Status</th>
					<th width="100">Order Status</th>
				</tr>
			</thead>
		</table>	
        <div style="max-height:400px; overflow-y:scroll; width:2820px" align="left" id="scroll_body">
			<table width="2820" border="1" class="rpt_table" rules="all" id="table-body">
				<?php
				$i = 1;
				$total_color_qnty = 0;$total_plan_cut_qnty =0;$total_sewing_input=0;$total_sewing_output=0;$total_sewing_balance=0;$total_wash_send=0;$total_wash_received=0;$total_wash_balance=0;$total_packing=0;$total_factory= 0;$total_finishing=0;$total_finishing_balance=0;
				foreach($order_wise_array as $color_number_id){
					foreach($color_number_id as $row){
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>"> 
					<td width="30" align="center" bgcolor="<?= $color; ?>"><?= $i;?></td>
					<td width="100" align="center"><?= $company_arr[$row['working_company_id']];?></td>
					<td width="100" align="center"><?= $company_arr[$row['company_name']];?></td>
					<td width="100" align="center"><?= $buyer_arr[$row['buyer_name']];?></td>
					<td width="100" align="center"><?= $brand_arr[$row['brand_id']];?></td>
					<td width="100" align="center"><?= $row['season_year'];?></td>
					<td width="100" align="center"><?= $row['job_no'];?></td>
					<td width="120" align="center"><p><?= $row['style_ref_no'];?></p></td>
					<td width="100" align="center"><p><?= $row['po_number'];?></p></td>
					<td width="100" align="center">
					<?
					$gmts_item_name="";
					$gmts_item_id=explode(',',$row[('gmts_item_id')]);
					for($j=0; $j<count($gmts_item_id); $j++)
					{
						$gmts_item_name.= $garments_item[$gmts_item_id[$j]].",";
					}
					echo rtrim($gmts_item_name,",");
					?>
					</td>
					<td width="200" align="center"><?= $color_library[$row['color_number_id']] // implode(',',$po_color_arr[$row['po_id']][$row['gmts_item_id']]);?></td>
					<td width="80" align="right"><p><?= $row['plan_qnty'];// $order_qty_dtls_arr[$row['plan_qnty']]['order_qnty'];?></p></td>
					<td width="80" align="right"><?= $order_cutting_arr[$row['po_id']][$row['color_number_id']];?></p></td>
					<td width="80" align="right"><?= $sewing_input_arr[$row['po_id']][$row['color_number_id']]['sewing_input'];?></p></td>
					<td width="80" align="right"><?= $sewing_output_arr[$row['po_id']][$row['color_number_id']]['sewing_output'];?></p></td>
					<td width="120" align="right"><?= $sewing_input_arr[$row['po_id']][$row['color_number_id']]['sewing_input']-$sewing_output_arr[$row['po_id']][$row['color_number_id']]['sewing_output'];?></td>
					<td width="80" align="right"><?= $wash_send_arr[$row['po_id']][$row['color_number_id']]['wash_send'];?></td>
					<td width="80" align="right"><?= $wash_received_arr[$row['po_id']][$row['color_number_id']]['wash_received'];?></td>
					<td width="80" align="right"><?= $wash_send_arr[$row['po_id']][$row['color_number_id']]['wash_send']-$wash_received_arr[$row['po_id']][$row['color_number_id']]['wash_received'];?></td>
					<td width="80" align="right"><?= $finishing_arr[$row['po_id']][$row['color_number_id']]['finishin_qnty'];?></td>
					<td width="80" align="right"><?= $sewing_output_arr[$row['po_id']][$row['color_number_id']]['sewing_output']-$finishing_arr[$row['po_id']][$row['color_number_id']]['finishin_qnty'];?></td>
					<td width="80" align="right"><?= $packing_arr[$row['po_id']]['packing_carton'];?></td> 
					<td width="50" align="center"><?= $finishing_arr[$row['po_id']][$row['color_number_id']]['finishin_qnty']-$packing_arr[$row['po_id']]['packing_carton'];?></td>
					<td width="100" align="right"><?= change_date_format($row['pack_handover_date']);?></td>
					<td width="100" align="right"><?= change_date_format($row['pub_ship']); ?></td>
					<td width="70" align="right"><?= change_date_format($row['actual_shipment_date']); ?></td>
					<td width="100" align="right"><?= change_date_format($factory_arr[$row['po_id']]['factory_date']);?></td>
					<td width="90" align="right"><?= $factory_arr[$row['po_id']]['factory_qnty'];?></td>
					<td width="90" align="center"><?= $shipment_status[$row[('shiping_status')]];?></td>
					<td width="100" align="center"><?=$order_status[$row[('is_confirmed')]];?></td>
				</tr>
				<?php
					++$i;
					$total_color_qnty += $row['plan_qnty'];
					$total_plan_cut_qnty += $order_cutting_arr[$row['po_id']][$row['color_number_id']];
					$total_sewing_input += $sewing_input_arr[$row['po_id']][$row['color_number_id']]['sewing_input'];
					$total_sewing_output += $sewing_output_arr[$row['po_id']][$row['color_number_id']]['sewing_output'];
					$total_sewing_balance += $sewing_input_arr[$row['po_id']][$row['color_number_id']]['sewing_input']-$sewing_output_arr[$row['po_id']][$row['color_number_id']]['sewing_output'];
					$total_wash_send += $wash_send_arr[$row['po_id']][$row['color_number_id']]['wash_send'];
					$total_wash_received += $wash_received_arr[$row['po_id']]['wash_received'];
					$total_wash_balance += $wash_send_arr[$row['po_id']][$row['color_number_id']]['wash_send']-$wash_received_arr[$row['po_id']][$row['color_number_id']]['wash_received'];
					$total_packing += $packing_arr[$row['po_id']]['packing_carton'];
					$total_factory += $factory_arr[$row['po_id']]['factory_qnty'];

					$total_finishing +=$finishing_arr[$row['po_id']][$row['color_number_id']]['finishin_qnty'];

					$total_finishing_balance +=$sewing_output_arr[$row['po_id']][$row['color_number_id']]['sewing_output']-$finishing_arr[$row['po_id']][$row['color_number_id']]['finishin_qnty'];

					$total_carton_wip +=$finishing_arr[$row['po_id']][$row['color_number_id']]['finishin_qnty']-$packing_arr[$row['po_id']]['packing_carton'];
				 
				}}
				?>
			</table>
		</div>
		<table width="2820" rules="all" cellpadding="0" cellspacing="0"  border="1" class="tbl_bottom" >
			<thead>
				<tr>
					<td width="30">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="120">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="200">Total</td>
					<td width="80"><?= $total_color_qnty;?></td>
					<td width="80"><?= $total_plan_cut_qnty;?></td>
					<td width="80"><?= $total_sewing_input;?></td>
					<td width="80"><?= $total_sewing_output;?></td>
					<td width="120"><?= $total_sewing_balance;?></td>
					<td width="80"><?= $total_wash_send;?></td>
					<td width="80"><?= $total_wash_received;?></td>
					<td width="80"><?= $total_wash_balance;?></td>
					<td width="80"><?= $total_finishing;?></td>
					<td width="80"><?= $total_finishing_balance;?></td>
					<td width="80"><?= $total_packing;?></td>
					<td width="50"><?= $total_carton_wip;?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="90"><?= $total_factory;?></td>
					<td width="90">&nbsp;</td>
					<td width="100">&nbsp;</td>
				</tr>
		    </thead>
		</table>		
	</div>
	<br>
	<br>
    <?php
    $user_id = $_SESSION['logic_erp']['user_id'];
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name = time();
    $filename = $user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$report_type";
    exit();
}
// drop down brand
if ($action=="load_drop_down_brand")
{ 
	echo create_drop_down( "cbo_brand_name", 100, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "--Select--", "", "" );
	exit();
}
//  drop down buyer season
if ($action=="load_drop_down_buyer_season")
{ 
	echo create_drop_down( "cbo_buyer_season_name", 100, "select season_name,id from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "--Select--", "", "" );
	exit();
}

if($action=="create_style_no_search_list_view")
{
	$data=explode('**',$data);
	// echo"<pre>";
	// print_r($data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	$brand_id=$data[6];
	$buyer_season_name_id=$data[7];
	$season_year=$data[8];
	//echo $month_id;
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
	
	$search_by=$data[2];
	$search_string=trim($data[3]);
	
	if($search_string!=''){
		if($search_by==1){$search_con=" and a.job_no like('%$search_string')";}
		else if($search_by==2){$search_con=" and a.style_ref_no like('%$search_string')";}
		else if($search_by==3){ $search_con=" and b.po_number like('%$search_string')";}
	}
 	
	//$year="year(insert_date)";
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

	if(str_replace("'","",$brand_id)==0) $brand_name_cond=""; else $brand_name_cond="and a.brand_id=".str_replace("'","",$brand_id)."";
	if(str_replace("'","",$buyer_season_name_id)==0) $season_name_cond=""; else $season_name_cond="and a.season_buyer_wise=".str_replace("'","",$buyer_season_name_id)."";
	if(str_replace("'","",$season_year)==0) $season_year_cond=""; else $season_year_cond="and a.season_year=".str_replace("'","",$season_year)."";
	 
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "SELECT b.po_number, a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,a.brand_id,a.season_buyer_wise,a.season_year, $year_field from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.company_name in($company_id) $search_con  $buyer_id_cond $year_cond  
	$brand_name_cond $season_name_cond $season_year_cond  order by job_no";
	// echo $sql;//die;
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,PO", "120,130,80,60,80","600","240",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0','') ;
	exit(); 
}  
?>