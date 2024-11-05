<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action == 'load_drop_down_working_location') {
	echo create_drop_down('cbo_working_location_name', 142, "select id, location_name from lib_location where is_deleted=0 and status_active=1 and company_id in($data) order by location_name", 'id,location_name', 0, '', 0, '');
	exit();
}
if($action == 'load_drop_down_working_floor') {
	echo create_drop_down('cbo_working_floor_id', 142, "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and location_id in($data) and production_process=5 order by floor_name", 'id,floor_name', 0, '', $selected, '');
	exit();	
}

if($action == 'load_drop_down_buyer') {
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name", 'id,buyer_name', 1, "-- All Buyer --", $selected, '' ,0); 
	exit();
}

if($action == 'style_search_popup') {
	extract($_REQUEST);
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
	//echo $style_id;die;

	?>
   <script>
		function js_set_value( str ) {
			$('#txt_selected_no').val(str);
			parent.emailwindow.hide();
		}
    </script>

	<?php
		$buyer=str_replace("'","",$buyer);
		$company=str_replace("'","",$company);

		/*echo $company;
		echo $buyer;*/
		// $job_year=str_replace("'","",$job_year);
		if($buyer!=0) $buyer_cond=" and a.buyer_name in($buyer)"; else $buyer_cond="";
		if($db_type==0) {
			if($job_year!=0) $job_year_cond=" and year(a.insert_date)=$job_year"; else $job_year_cond="";
			$select_date=" year(a.insert_date)";
		}
		else if($db_type==2) {
			if($job_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
			$select_date=" to_char(a.insert_date,'YYYY')";
		}
		
		$sql = "SELECT a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year
			from wo_po_details_master a
			where a.status_active=1  $buyer_cond $job_year_cond and is_deleted=0 order by job_no_prefix_num desc, $select_date";
		// echo $sql;
		echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","410","300",0, $sql , "js_set_value", "id,job_no,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","","") ;	
		//echo "<input type='hidden' id='txt_selected_id' />";
		//echo "<input type='hidden' id='txt_selected' />";
		echo "<input type='hidden' id='txt_selected_no' />";
		
		?>
	    <script language="javascript" type="text/javascript">
		var style_no='<?php echo $txt_ref_no;?>';
		var style_id='<?php echo $txt_style_ref_id;?>';
		var style_des='<?php echo $txt_style_ref_no;?>';
		//alert(style_id);
		if(style_no!="")
		{
			style_no_arr=style_no.split(",");
			style_id_arr=style_id.split(",");
			style_des_arr=style_des.split(",");
			var str_ref="";
			for(var k=0;k<style_no_arr.length; k++)
			{
				str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
				js_set_value(str_ref);
			}
		}
		</script>
    
    <?
	
		exit();
}

if($action=='pono_search_popup')
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
		
		function toggle( x, origColor ) {
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
			
			$('#hdnOrderId').val( id );
			$('#hdnOrderNo').val( name );
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
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hdnOrderNo" id="hdnOrderNo" value="" />
                    <input type="hidden" name="hdnOrderId" id="hdnOrderId" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?php 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?php echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<?php echo $job_no.'**'.$job_year; ?>', 'create_order_no_search_list_view', 'search_div', 'garment_leftover_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><?php echo load_month_buttons(1); ?></td>
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
<?php
	exit(); 
}

if($action == 'create_order_no_search_list_view') {
	$data=explode('**',$data);
	$working_company_id=$data[0];
	$buyer_name = $data[1];
	$job_no=$data[6];
	$job_year=$data[7];
	/*$txt_date_from
	$txt_date_to*/

	if ($txt_date_from == '' && $txt_date_to == '') {
		$txt_date_from = "01-Jan-$cbo_job_year";
		$txt_date_to = "31-Dec-$cbo_job_year";
	}

	if($job_no!='') $job_no_cond="and a.job_no='$job_no'";else $job_no_cond="";

	if($buyer_name != 0)
		$buyer_id_cond=" and a.buyer_name=$buyer_name";
	
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no";
	else 
		$search_field="a.job_no";
		
	$start_date =$data[4];
	$end_date =$data[5];	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later	
	
	$sql = "SELECT b.id, b.po_number, a.company_name, a.buyer_name, $year_field, a.job_no, a.style_ref_no, b.pub_shipment_date
			from wo_po_details_master a, wo_po_break_down b
			where a.status_active=1 and b.status_active=1 and a.company_name in($working_company_id) and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond and a.id = b.job_id";			

	// echo $sql;
		
	echo create_list_view('tbl_list_search', 'Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date', '80,80,50,70,140,170', '760', '220', 0, $sql, 'js_set_value', 'po_break_down_id,po_number', '', 1, 'company_name,buyer_name,0,0,0,0,0', $arr, 'company_name,buyer_name,year,job_no,style_ref_no,po_number,pub_shipment_date', '', '', '0,0,0,0,0,0,3', '', 1);
   exit(); 
}

if($action == 'generate_report_powise') 
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sql_cond = '';
	$orderNos = '';

	$cbo_working_company_name = str_replace("'", '', $cbo_working_company_name);
	$cbo_working_location_name = str_replace("'", '', $cbo_working_location_name);
	$cbo_working_floor_id = str_replace("'", '', $cbo_working_floor_id);
	$cbo_job_year = str_replace("'", '', $cbo_job_year);
	$cbo_buyer_name = str_replace("'", '', $cbo_buyer_name);
	$txt_ref_no = str_replace("'", '', $txt_ref_no);
	$txt_job_no = str_replace("'", '', $txt_job_no);
	$txt_po_no = str_replace("'", '', $txt_po_no);
	$txt_date_from = str_replace("'", '', $txt_date_from);
	$txt_date_to = str_replace("'", '', $txt_date_to);

	if ($txt_date_from == '' && $txt_date_to == '') {
		$txt_date_from = "01-Jan-$cbo_job_year";
		$txt_date_to = "31-Dec-$cbo_job_year";
	}

	// echo "$txt_date_from, $txt_date_to";die;

	$company_sql = 'select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0';
	$company_result = sql_select($company_sql);
	$company_name_library = array();
	$floor_name_library = return_library_array('select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0', 'id', 'floor_name');
	$buyer_library = return_library_array('select id, buyer_name from lib_buyer', 'id', 'buyer_name');
	$season_library = return_library_array('select id, season_name from lib_buyer_season', 'id', 'season_name');
	$gmt_item_library = return_library_array('select id, item_name from lib_garment_item', 'id', 'item_name');

	foreach ($company_result as $row) {
		$company_name_library[$row[csf('id')]]['id'] = $row[csf('id')];
		$company_name_library[$row[csf('id')]]['company_name'] = $row[csf('company_name')];
		$company_name_library[$row[csf('id')]]['company_short_name'] = $row[csf('company_short_name')];
	}

	$sql_cond = "a.working_company_id in($cbo_working_company_name)";

	if($cbo_working_location_name != '') {
		$sql_cond .= " and a.working_location_id in($cbo_working_location_name)";
	}

	if($cbo_working_floor_id != '') {
		$sql_cond .= " and a.working_floor_id in($cbo_working_floor_id)";
	}

	if($cbo_buyer_name != 0) {
		$sql_cond .= " and a.buyer_name in($cbo_buyer_name)";
	}

	if($txt_job_no != '') {
		$sql_cond .= " and b.job_no = '$txt_job_no'";
	}

	if($txt_po_no != '') {
		$poNoArr = explode(',', $txt_po_no);

		foreach ($poNoArr as $poNo) {
			$orderNos .= "'" . $poNo . "',";
		}
		$orderNos = rtrim($orderNos, ',');

		$sql_cond .= " and b.order_no in($orderNos)";
	}

	if($db_type==0)// FOR MYSQL
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.leftover_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.leftover_date <= '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.leftover_date >= '".change_date_format($txt_date_from,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2 || $db_type==1) // FOR ORACLE
	{ 
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.leftover_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.leftover_date <=  '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.leftover_date >= '".date("j-M-Y",strtotime($txt_date_from))."'";
		}		
	}

	$leftover_gmts_rcv_sql = "SELECT a.sys_number, a.company_id, a.working_company_id, a.working_floor_id, a.buyer_name, b.style_ref_no, b.item_number_id, b.job_no, b.order_no, b.po_break_down_id, a.goods_type, b.total_left_over_receive, b.fob_rate
			from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b
			where a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and $sql_cond and a.id=b.mst_id
		group by a.sys_number, a.company_id, a.working_company_id, a.working_floor_id, a.buyer_name, b.style_ref_no, b.item_number_id, b.job_no, b.order_no, b.po_break_down_id, a.goods_type, b.total_left_over_receive, b.fob_rate";

	// echo $leftover_gmts_rcv_sql;

	$leftover_gmts_rcv_result = sql_select($leftover_gmts_rcv_sql);
	$leftover_gmts_rcv_arr = array();
	$poBreakDownIdsArr = array();
	$companyArr = array();

	foreach ($leftover_gmts_rcv_result as $row) 
	{
		$leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['order_no'] = $row[csf('order_no')];
		$leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['company_id'] = $row[csf('company_id')];
		$leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['working_company_id'] = $row[csf('working_company_id')];
		$leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['buyer_name'] = $row[csf('buyer_name')];
		$leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['item_number_id'] = $row[csf('item_number_id')];
		$leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['job_no'] = $row[csf('job_no')];
		$leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['working_floor_id'][] = $row[csf('working_floor_id')];
		// $leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['fob_rate'] = $row[csf('fob_rate')];
		$leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['goods_type'][] = $row[csf('goods_type')];

		if ($row[csf('goods_type')] == 1) {
			$leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['good_qty_rcv'] += $row[csf('total_left_over_receive')];
		} else if($row[csf('goods_type')] == 2) {
			$leftover_gmts_rcv_arr[$row[csf('po_break_down_id')]]['reject_qty_rcv'] += $row[csf('total_left_over_receive')];
		}

		$poBreakDownIdsArr[] = $row[csf('po_break_down_id')];
		$companyArr[] = $row[csf('company_id')];
	}

	unset($leftover_gmts_rcv_result);

	$companyArr = array_unique($companyArr);
	$poBreakDownIdsArr = array_unique($poBreakDownIdsArr);

	$con = connect();
        $user_id = $_SESSION['logic_erp']["user_id"] ;
        if($db_type==0) { mysql_query("BEGIN"); }
        foreach($poBreakDownIdsArr as $po_id) {
            if($po_id!=0) {
                $r_id2=execute_query("insert into tmp_poid(userid, poid, type) values($user_id,$po_id,621)");
            }            
        }
        if($db_type==0) {
            if($r_id2) {
                mysql_query("COMMIT");
            }
        }
        if($db_type==2 || $db_type==1) {
            if($r_id2) {
                oci_commit($con);  
            }
        }

	$wo_sql = "SELECT a.id as po_break_down_id, a.job_id, a.job_no_mst, a.po_number, a.po_quantity, a.pub_shipment_date, c.job_quantity, b.order_quantity, 
       c.buyer_name, c.company_name, c.dealing_marchant, c.gmts_item_id, c.item_number_id, c.style_description, c.style_ref_no, c.season_buyer_wise, c.client_id
  		from wo_po_break_down a, wo_po_color_size_breakdown b, wo_po_details_master c, tmp_poid d
 		where a.status_active = 1 and b.status_active = 1 and c.status_active = 1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and a.id = d.poid and d.type=621 and d.userid=$user_id and a.id = b.po_break_down_id and c.job_no = b.job_no_mst and a.job_id = c.id
		group by a.id, a.job_id, a.job_no_mst, a.po_number, a.po_quantity, a.pub_shipment_date, c.job_quantity, b.order_quantity,
       c.buyer_name, c.company_name, c.dealing_marchant, c.gmts_item_id, c.item_number_id, c.style_description, c.style_ref_no, c.season_buyer_wise, c.client_id";

	// echo $wo_sql;

	$wo_result = sql_select($wo_sql);
	$wo_arr = array();

	foreach ($wo_result as $row) 
	{
		$wo_arr[$row[csf('po_break_down_id')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$wo_arr[$row[csf('po_break_down_id')]]['order_no'] = $row[csf('po_number')];
		$wo_arr[$row[csf('po_break_down_id')]]['job_id'] = $row[csf('job_id')];
		$wo_arr[$row[csf('po_break_down_id')]]['job_no_mst'] = $row[csf('job_no_mst')];
		$wo_arr[$row[csf('po_break_down_id')]]['po_quantity'] = $row[csf('po_quantity')];
		$wo_arr[$row[csf('po_break_down_id')]]['pub_shipment_date'] = $row[csf('pub_shipment_date')];
		$wo_arr[$row[csf('po_break_down_id')]]['order_quantity'] = $row[csf('order_quantity')];
		$wo_arr[$row[csf('po_break_down_id')]]['job_quantity'] = $row[csf('job_quantity')];
		$wo_arr[$row[csf('po_break_down_id')]]['buyer_name'] = $row[csf('buyer_name')];
		$wo_arr[$row[csf('po_break_down_id')]]['company_name'] = $row[csf('company_name')];
		$wo_arr[$row[csf('po_break_down_id')]]['company_name'] = $row[csf('company_name')];
		$wo_arr[$row[csf('po_break_down_id')]]['dealing_marchant'] = $row[csf('dealing_marchant')];
		$wo_arr[$row[csf('po_break_down_id')]]['gmts_item_id'] = $row[csf('gmts_item_id')];
		$wo_arr[$row[csf('po_break_down_id')]]['item_number_id'] = $row[csf('item_number_id')];
		$wo_arr[$row[csf('po_break_down_id')]]['style_description'] = $row[csf('style_description')];
		$wo_arr[$row[csf('po_break_down_id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$wo_arr[$row[csf('po_break_down_id')]]['season_buyer_wise'] = $row[csf('season_buyer_wise')];
		$wo_arr[$row[csf('po_break_down_id')]]['client_id'] = $row[csf('client_id')];
	}


	$sql_cond = " and c.working_company_id in($cbo_working_company_name)";

	if($cbo_working_location_name != '') {
		$sql_cond .= " and c.working_location_id in($cbo_working_location_name)";
	}

	if($cbo_working_floor_id != '') {
		$sql_cond .= " and c.working_floor_id in($cbo_working_floor_id)";
	}

	if($cbo_buyer_name != 0) {
		$sql_cond .= " and d.buyer_id in($cbo_buyer_name)";
	}

	if($txt_po_no != '') {
		$poNoArr = explode(',', $txt_po_no);

		foreach ($poNoArr as $poNo) {
			$orderNos .= "'" . $poNo . "',";
		}
		$orderNos = rtrim($orderNos, ',');

		$sql_cond .= " and d.order_no in($orderNos)";
	}

	if($db_type==0)// FOR MYSQL
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and c.issue_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and c.issue_date <= '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and c.issue_date >= '".change_date_format($txt_date_from,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2 || $db_type==1) // FOR ORACLE
	{ 
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and c.issue_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and c.issue_date <=  '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and c.issue_date >= '".date("j-M-Y",strtotime($txt_date_from))."'";
		}		
	}

	$leftover_sql = "SELECT d.po_break_down_id, c.goods_type, c.order_type, d.total_issue, d.sale_rate, d.style_ref_no,c.currency_id,c.exchange_rate
				from pro_leftover_gmts_issue_mst c, pro_leftover_gmts_issue_dtls d, tmp_poid e
				where d.po_break_down_id=e.poid and d.po_break_down_id = d.po_break_down_id and d.mst_id=c.id and c.issue_purpose=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond
				group by d.po_break_down_id, c.order_type, d.total_issue, c.goods_type, d.sale_rate, d.style_ref_no,c.currency_id,c.exchange_rate";

	// echo $leftover_sql;
	$leftover_result = sql_select($leftover_sql);

	$leftover_arr = array();

	foreach ($leftover_result as $row) 
	{
		$leftover_arr[$row[csf('po_break_down_id')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		if($row[csf('currency_id')]==2) // usd
		{
			$sale_rate = $row[csf('sale_rate')]*$row[csf('exchange_rate')];
		}
		else
		{
			$sale_rate = $row[csf('sale_rate')];
		}
		// echo $sale_rate;
		if ($row[csf('goods_type')] == 1) // good gmts
		{
			$leftover_arr[$row[csf('po_break_down_id')]]['good_qty_sale'] += $row[csf('total_issue')];
			$leftover_arr[$row[csf('po_break_down_id')]]['good_sale_value'] += ($row[csf('total_issue')] * $sale_rate);
			// $leftover_arr[$row[csf('po_break_down_id')]]['good_sale_rate'] = $row[csf('sale_rate')];
		}
		else if($row[csf('goods_type')] == 2) // damage gmts
		{
			$leftover_arr[$row[csf('po_break_down_id')]]['reject_qty_sale'] += $row[csf('total_issue')];
			$leftover_arr[$row[csf('po_break_down_id')]]['reject_sale_value'] += ($row[csf('total_issue')] * $sale_rate);
			// $leftover_arr[$row[csf('po_break_down_id')]]['reject_sale_rate'] = $row[csf('sale_rate')];
		}
	}

	// print_r($leftover_arr);

	$companies = '';

	foreach ($companyArr as $key => $value) {
		$tempCompany = $company_name_library[$value]['company_name'];
		$companies .= $tempCompany . ', ';
	}

	$companies = rtrim($companies, ', ');

	ob_start();
	?>

	<style>
		#powise_report .rpt_table th, #powise_report .rpt_table td {
		    padding: 10px;
		    text-align: center;
		}
	</style>
	<div id="powise_report">
		<h3>Garment Leftover Report (PO Wise)</h3>
    	<p>Company Name : <?php echo $companies; ?></p>

		<table width="100%" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
    				<th style="width: 1%; padding: 2px;">SL</th>
    				<th style="width: 4%;">LC Company</th>
    				<th style="width: 4%;">Working Company</th>
    				<th style="width: 4%;">Working Floor</th>
    				<th style="width: 4%;">Buyer Name</th>
    				<th style="width: 4%;">Buyer Client</th>
    				<th style="width: 4%;">Style Name</th>
    				<th style="width: 4%;">Season</th>
    				<th style="width: 4%;">Job No</th>
    				<th style="width: 4%;">Item Name</th>
    				<th style="width: 4%;">Order Number</th>
    				<th style="width: 4%;">Style Des</th>
    				<th style="width: 4%;">Order Qty. Pcs</th>
    				<th style="width: 4%;">Ship Date</th>
    				<th style="width: 4%;">Reject Qty Rcv</th>
    				<th style="width: 4%;">Reject Sale Qty</th>
    				<th style="width: 4%;">Avg. Rate</th>
    				<th style="width: 4%;">Sale Value</th>
    				<th style="width: 4%;">Stock Inhand (Reject)</th>
    				<th style="width: 4%;">Stock Value (Reject)</th>
    				<th style="width: 4%;">Good Qty Rcv</th>
    				<th style="width: 4%;">Good Qty Sale</th>
    				<th style="width: 4%;">Avg. Rate</th>
    				<th style="width: 4%;">Sale Value</th>
    				<th style="width: 4%;">Stock Inhand (Good)</th>
    				<th>Stock Value (Good)</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$sl = 1;
					$totalRejectRcv = 0;
					$totalRejectSale = 0;
					$totalAvgRateRcv = 0;
					$totalSaleValueReject = 0;
					$totalStockInhandReject = 0;
					$totalStockValueReject = 0;

					$totalGoodRcv = 0;
					$totalGoodSale = 0;
					$totalAvgRateIssue = 0;
					$totalSaleValueGood = 0;
					$totalStockInhandGood = 0;
					$totalStockValueGood = 0;

					foreach ($leftover_gmts_rcv_arr as $row) {
						$workingFloors = '';
						/*foreach ($row['working_floor_id'] as $floorId) {
							$floorIdsArr[] = $floorId;
						}*/

						$floorIdsArr = array_unique($row['working_floor_id']);

						foreach ($floorIdsArr as $floorId) {
							if($floorId) {
								$workingFloors .= $floor_name_library[$floorId] . ', ';
							}							
						}

						$workingFloors = rtrim($workingFloors, ', ');
						$buyerClient = $buyer_library[$wo_arr[$row['po_break_down_id']]['client_id']];
						$styleDesc = $wo_arr[$row['po_break_down_id']]['style_description'];
						
						// $avgRateReject = $leftover_gmts_rcv_arr[$row['po_break_down_id']]['fob_rate'];

						$rejectQtyRcv = $leftover_gmts_rcv_arr[$row['po_break_down_id']]['reject_qty_rcv'];
						$rejectSaleQty = $leftover_arr[$row['po_break_down_id']]['reject_qty_sale'];
						$stockInhandReject = ($rejectQtyRcv-$rejectSaleQty);
						$saleValueReject = $leftover_arr[$row['po_break_down_id']]['reject_sale_value'];
						$avgRateReject = ($saleValueReject/$rejectSaleQty);
						$stockValueReject = ($stockInhandReject*$avgRateReject);
						// $saleValueGood = $leftover_arr[$row['po_break_down_id']]['reject_sale_value'];

						$goodQtyRcv = $leftover_gmts_rcv_arr[$row['po_break_down_id']]['good_qty_rcv'];
						$goodSaleQty = $leftover_arr[$row['po_break_down_id']]['good_qty_sale'];
						$stockInhandGood = ($goodQtyRcv-$goodSaleQty);
						// $saleValueGood = ($goodSaleQty*$avgRateGood);						

						$saleValueGood = $leftover_arr[$row['po_break_down_id']]['good_sale_value'];

						$avgRateGood = ($saleValueGood/$goodSaleQty);
						$stockValueGood = ($stockInhandGood*$avgRateGood);

						$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
				?>
					<tr bgcolor="<?php echo $bgcolor;?>" id="tr_<?php echo $sl;?>" onClick="change_color('tr_<?php echo $sl; ?>','<?php echo $bgcolor; ?>')">
						<td><?php echo $sl; ?></td>
						<td><?php echo $company_name_library[$row['company_id']]['company_name']; ?></td>
						<td><?php echo $company_name_library[$row['working_company_id']]['company_short_name']; ?></td>
						<td><?php echo $workingFloors ? $workingFloors : '-'; ?></td>
						<td><?php echo $buyer_library[$row['buyer_name']]; ?></td>
						<td><?php echo $buyerClient ? $buyerClient : '-'; ?></td>
						<td><?php echo $wo_arr[$row['po_break_down_id']]['style_ref_no']; ?></td>
						<td><?php echo $season_library[$wo_arr[$row['po_break_down_id']]['season_buyer_wise']]; ?></td>
						<td><?php echo $row['job_no']; ?></td>
						<td><?php echo $gmt_item_library[$wo_arr[$row['po_break_down_id']]['gmts_item_id']]; ?></td>
						<td><?php echo $wo_arr[$row['po_break_down_id']]['order_no']; ?></td>
						<td><?php echo $styleDesc ? $styleDesc : '-'; ?></td>
						<td><?php echo $wo_arr[$row['po_break_down_id']]['job_quantity']; ?></td>
						<td><?php echo $wo_arr[$row['po_break_down_id']]['pub_shipment_date']; ?></td>
						<td>
							<a href="##" onclick="fnc_qty_details(<?php echo $row['po_break_down_id']; ?>, <?php echo $row['working_company_id']; ?>, 'rejectQtyRcvPopUp', 'Reject Qty Receive')">
		                		<p><?php echo $rejectQtyRcv; ?></p>
		                	</a>
	                	</td>
						<td>
							<a href="##" onclick="fnc_qty_details(<?php echo $row['po_break_down_id']; ?>, <?php echo $row['working_company_id']; ?>, 'rejectQtySalePopUp', 'Reject Qty Sale')">
		                		<p><?php echo $rejectSaleQty; ?></p>
		                	</a>
						</td>
						<td><?php echo $avgRateReject && !is_nan($avgRateReject) ? number_format($avgRateReject) : '-'; ?></td>
						<td><?php echo $saleValueReject && !is_nan($saleValueReject) ? number_format($saleValueReject) : '-'; ?></td>
						<td><?php echo $stockInhandReject && !is_nan($stockInhandReject) ? $stockInhandReject : '-'; ?></td>
						<td><?php echo $stockValueReject && !is_nan($stockValueReject) ? number_format($stockValueReject) : '-'; ?></td>
						<td>
							<a href="##" onclick="fnc_qty_details(<?php echo $row['po_break_down_id']; ?>, <?php echo $row['working_company_id']; ?>, 'goodQtyRcvPopUp', 'Good Qty Receive')">
		                		<p><?php echo $goodQtyRcv; ?></p>
		                	</a>
						<td>
							<a href="##" onclick="fnc_qty_details(<?php echo $row['po_break_down_id']; ?>, <?php echo $row['working_company_id']; ?>, 'goodQtySalePopUp', 'Good Qty Sale')">
								<p><?php echo $goodSaleQty; ?></p>
							</a>
						</td>
						<td><?php echo $avgRateGood && !is_nan($avgRateGood) ? number_format($avgRateGood) : '-'; ?></td>
						<td><?php echo $saleValueGood ? number_format($saleValueGood) : '-'; ?></td>
						<td><?php echo $stockInhandGood ? $stockInhandGood : '-'; ?></td>
						<td><?php echo $stockValueGood && !is_nan($stockValueGood) ? number_format($stockValueGood) : '-'; ?></td>
					</tr>
					<?php
						$sl++;
						$totalRejectRcv += $rejectQtyRcv;
						$totalRejectSale += $rejectSaleQty;
						$totalAvgRateReject += $avgRateReject && !is_nan($avgRateReject) ? $avgRateReject : 0;
						$totalSaleValueReject += $saleValueReject && !is_nan($saleValueReject) ? $saleValueReject : 0;
						$totalStockInhandReject += $stockInhandReject && !is_nan($stockInhandReject) ? $stockInhandReject : 0;
						$totalStockValueReject += $stockValueReject && !is_nan($stockValueReject) ? $stockValueReject : 0;

						$totalGoodRcv += $goodQtyRcv;
						$totalGoodSale += $goodSaleQty;
						$totalAvgRateIssue +=  $avgRateGood && !is_nan($avgRateGood) ? $avgRateGood : 0;
						$totalSaleValueGood += $saleValueGood && !is_nan($saleValueGood) ? $saleValueGood : 0;
						$totalStockInhandGood += $stockInhandGood && !is_nan($stockInhandGood) ? $stockInhandGood : 0;
						$totalStockValueGood += $stockValueGood && !is_nan($stockValueGood) ? $stockValueGood : 0;
					}
				?>
				
			</tbody>
			<tfoot>
				<tr>
    				<th colspan="14" style="text-align: right;">Total</th>
    				<th><?php echo $totalRejectRcv; ?></th>
    				<th><?php echo $totalRejectSale; ?></th>
    				<th><?php echo $totalAvgRateReject; ?></th>
    				<th><?php echo $totalSaleValueReject; ?></th>
    				<th><?php echo $totalStockInhandReject; ?></th>
    				<th><?php echo $totalStockValueReject; ?></th>
    				<th><?php echo $totalGoodRcv; ?></th>
    				<th><?php echo number_format($totalGoodSale); ?></th>
    				<th><?php echo number_format($totalAvgRateIssue); ?></th>
    				<th><?php echo number_format($totalSaleValueGood) ?></th>
    				<th><?php echo $totalStockInhandGood; ?></th>
    				<th><?php echo number_format($totalStockValueGood); ?></th>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php

	$html=ob_get_contents();
	ob_end_clean();	
			
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html**$filename";

	$r_id3=execute_query("delete from tmp_poid where userid=$user_id and type='621'");
    if($db_type==0) {
        if($r_id3) {
            mysql_query("COMMIT");
        }
    }
    if($db_type==2 || $db_type==1 ) {
        if($r_id3) {
            oci_commit($con);  
        }
    }

    disconnect($con);
    die();
	exit();
}

if($action == 'generate_report_stylewise') {
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sql_cond = '';
	$orderNos = '';

	$cbo_working_company_name = str_replace("'", '', $cbo_working_company_name);
	$cbo_working_location_name = str_replace("'", '', $cbo_working_location_name);
	$cbo_working_floor_id = str_replace("'", '', $cbo_working_floor_id);
	$cbo_job_year = str_replace("'", '', $cbo_job_year);
	$cbo_buyer_name = str_replace("'", '', $cbo_buyer_name);
	$txt_ref_no = str_replace("'", '', $txt_ref_no);
	$txt_job_no = str_replace("'", '', $txt_job_no);
	$txt_po_no = str_replace("'", '', $txt_po_no);
	$txt_date_from = str_replace("'", '', $txt_date_from);
	$txt_date_to = str_replace("'", '', $txt_date_to);

	if ($txt_date_from == '' && $txt_date_to == '') {
		$txt_date_from = "01-Jan-$cbo_job_year";
		$txt_date_to = "31-Dec-$cbo_job_year";
	}

	// echo "$txt_date_from, $txt_date_to";die;

	$company_sql = 'select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0';
	$company_result = sql_select($company_sql);
	$company_name_library = array();
	$floor_name_library = return_library_array('select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0', 'id', 'floor_name');
	$buyer_library = return_library_array('select id, buyer_name from lib_buyer', 'id', 'buyer_name');
	$season_library = return_library_array('select id, season_name from lib_buyer_season', 'id', 'season_name');
	$gmt_item_library = return_library_array('select id, item_name from lib_garment_item', 'id', 'item_name');

	foreach ($company_result as $row) {
		$company_name_library[$row[csf('id')]]['id'] = $row[csf('id')];
		$company_name_library[$row[csf('id')]]['company_name'] = $row[csf('company_name')];
		$company_name_library[$row[csf('id')]]['company_short_name'] = $row[csf('company_short_name')];
	}

	$sql_cond = "a.working_company_id in($cbo_working_company_name)";

	if($cbo_working_location_name != '') {
		$sql_cond .= " and a.working_location_id in($cbo_working_location_name)";
	}

	if($cbo_working_floor_id != '') {
		$sql_cond .= " and a.working_floor_id in($cbo_working_floor_id)";
	}

	if($cbo_buyer_name != 0) {
		$sql_cond .= " and a.buyer_name in($cbo_buyer_name)";
	}

	if($txt_job_no != '') {
		$sql_cond .= " and b.job_no = '$txt_job_no'";
	}

	if($txt_po_no != '') {
		$poNoArr = explode(',', $txt_po_no);

		foreach ($poNoArr as $poNo) {
			$orderNos .= "'" . $poNo . "',";
		}
		$orderNos = rtrim($orderNos, ',');

		$sql_cond .= " and b.order_no in($orderNos)";
	}

	if($db_type==0) // FOR MYSQL
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.leftover_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.leftover_date <= '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.leftover_date >= '".change_date_format($txt_date_from,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2 || $db_type==1) // FOR ORACLE
	{ 
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.leftover_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.leftover_date <=  '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.leftover_date >= '".date("j-M-Y",strtotime($txt_date_from))."'";
		}		
	}

	$leftover_gmts_rcv_sql = "SELECT a.sys_number, a.company_id, a.working_company_id, a.working_floor_id, a.buyer_name, b.style_ref_no, b.item_number_id, b.job_no, b.order_no, b.po_break_down_id, a.goods_type, b.total_left_over_receive, b.fob_rate
			from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b
			where a.status_active=1 and b.status_active=1 and $sql_cond and a.id=b.mst_id
		group by a.sys_number, a.company_id, a.working_company_id, a.working_floor_id, a.buyer_name, b.style_ref_no, b.item_number_id, b.job_no, b.order_no, b.po_break_down_id, a.goods_type, b.total_left_over_receive, b.fob_rate";

	// echo $leftover_gmts_rcv_sql;

	$leftover_gmts_rcv_result = sql_select($leftover_gmts_rcv_sql);
	$leftover_gmts_rcv_arr = array();
	$styleRefsArr = array();
	$companyArr = array();

	foreach ($leftover_gmts_rcv_result as $row) {
		$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['order_no'] .= ($leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['order_no']=="") ? $row[csf('order_no')] : ",".$row[csf('order_no')];
		$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['company_id'] = $row[csf('company_id')];
		$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['working_company_id'] = $row[csf('working_company_id')];
		$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['buyer_name'] = $row[csf('buyer_name')];
		$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['item_number_id'] = $row[csf('item_number_id')];
		$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['job_no'] = $row[csf('job_no')];
		$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['po_break_down_ids'] .= $row[csf('po_break_down_id')] . ',';
		$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['working_floor_id'][] = $row[csf('working_floor_id')];
		$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['fob_rate'] = $row[csf('fob_rate')];
		$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['goods_type'][] = $row[csf('goods_type')];

		if ($row[csf('goods_type')] == 1) {
			$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['good_qty_rcv'] += $row[csf('total_left_over_receive')];
		} else if($row[csf('goods_type')] == 2) {
			$leftover_gmts_rcv_arr[$row[csf('style_ref_no')]]['reject_qty_rcv'] += $row[csf('total_left_over_receive')];
		}

		$styleRefsArr[] = $row[csf('style_ref_no')];
		$companyArr[] = $row[csf('company_id')];
	}

	unset($leftover_gmts_rcv_result);

	$companyArr = array_unique($companyArr);
	$styleRefsArr = array_unique($styleRefsArr);

	/*echo '<pre>';
	print_r($leftover_gmts_rcv_arr);
	echo '</pre>';*/

	$con = connect();
        $user_id = $_SESSION['logic_erp']["user_id"] ;
        if($db_type==0) { mysql_query("BEGIN"); }
        foreach($styleRefsArr as $style) {
            if($style != '') {
                $r_id2=execute_query("insert into tmp_poid(userid, pono, type) values($user_id,'$style',621)");
            }
        }
        if($db_type==0) {
            if($r_id2) {
                mysql_query("COMMIT");
            }
        }
        if($db_type==2 || $db_type==1) {
            if($r_id2) {
                oci_commit($con);
            }
        }

	$wo_sql = "SELECT a.id as po_break_down_id, a.job_id, a.job_no_mst, a.po_number, a.po_quantity, a.pub_shipment_date, c.job_quantity, b.order_quantity, 
       c.buyer_name, c.company_name, c.dealing_marchant, c.gmts_item_id, c.item_number_id, c.style_description, c.style_ref_no, c.season_buyer_wise, c.client_id,a.grouping
  		from wo_po_break_down a, wo_po_color_size_breakdown b, wo_po_details_master c, tmp_poid d
 		where a.status_active = 1 and b.status_active = 1 and c.status_active = 1 and c.style_ref_no = d.pono and d.type=621 and d.userid=$user_id and a.id = b.po_break_down_id and c.job_no = b.job_no_mst and a.job_id = c.id
		group by a.id, a.job_id, a.job_no_mst, a.po_number, a.po_quantity, a.pub_shipment_date, c.job_quantity, b.order_quantity,
       c.buyer_name, c.company_name, c.dealing_marchant, c.gmts_item_id, c.item_number_id, c.style_description, c.style_ref_no, c.season_buyer_wise, c.client_id,a.grouping";

	$wo_result = sql_select($wo_sql);
	$wo_arr = array();

	// echo $wo_sql;

	foreach ($wo_result as $row) {
		$wo_arr[$row[csf('style_ref_no')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$wo_arr[$row[csf('style_ref_no')]]['order_no'] = $row[csf('po_number')];
		$wo_arr[$row[csf('style_ref_no')]]['job_id'] = $row[csf('job_id')];
		$wo_arr[$row[csf('style_ref_no')]]['job_no_mst'] = $row[csf('job_no_mst')];
		$wo_arr[$row[csf('style_ref_no')]]['po_quantity'] = $row[csf('po_quantity')];
		$wo_arr[$row[csf('style_ref_no')]]['pub_shipment_date'] = $row[csf('pub_shipment_date')];
		$wo_arr[$row[csf('style_ref_no')]]['order_quantity'] = $row[csf('order_quantity')];
		$wo_arr[$row[csf('style_ref_no')]]['job_quantity'] = $row[csf('job_quantity')];
		$wo_arr[$row[csf('style_ref_no')]]['buyer_name'] = $row[csf('buyer_name')];
		$wo_arr[$row[csf('style_ref_no')]]['company_name'] = $row[csf('company_name')];
		$wo_arr[$row[csf('style_ref_no')]]['company_name'] = $row[csf('company_name')];
		$wo_arr[$row[csf('style_ref_no')]]['dealing_marchant'] = $row[csf('dealing_marchant')];
		$wo_arr[$row[csf('style_ref_no')]]['gmts_item_id'] = $row[csf('gmts_item_id')];
		$wo_arr[$row[csf('style_ref_no')]]['item_number_id'] = $row[csf('item_number_id')];
		$wo_arr[$row[csf('style_ref_no')]]['style_description'] = $row[csf('style_description')];
		$wo_arr[$row[csf('style_ref_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$wo_arr[$row[csf('style_ref_no')]]['season_buyer_wise'] = $row[csf('season_buyer_wise')];
		$wo_arr[$row[csf('style_ref_no')]]['client_id'] = $row[csf('client_id')];
		$wo_arr[$row[csf('style_ref_no')]]['grouping'] = $row[csf('grouping')];
	}

	$sql_cond = " and c.working_company_id in($cbo_working_company_name)";

	if($cbo_working_location_name != '') {
		$sql_cond .= " and c.working_location_id in($cbo_working_location_name)";
	}

	if($cbo_working_floor_id != '') {
		$sql_cond .= " and c.working_floor_id in($cbo_working_floor_id)";
	}

	if($cbo_buyer_name != 0) {
		$sql_cond .= " and d.buyer_id in($cbo_buyer_name)";
	}

	if($txt_po_no != '') {
		$poNoArr = explode(',', $txt_po_no);

		foreach ($poNoArr as $poNo) {
			$orderNos .= "'" . $poNo . "',";
		}
		$orderNos = rtrim($orderNos, ',');

		$sql_cond .= " and d.order_no in($orderNos)";
	}

	if($db_type==0)// FOR MYSQL
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and c.issue_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and c.issue_date <= '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and c.issue_date >= '".change_date_format($txt_date_from,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2 || $db_type==1) // FOR ORACLE
	{ 
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and c.issue_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and c.issue_date <=  '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and c.issue_date >= '".date("j-M-Y",strtotime($txt_date_from))."'";
		}		
	}

	$leftover_sql = "SELECT d.po_break_down_id, c.goods_type, c.order_type, d.total_issue, d.sale_rate, d.style_ref_no,c.currency_id,c.exchange_rate
				from pro_leftover_gmts_issue_mst c, pro_leftover_gmts_issue_dtls d, tmp_poid e
				where d.style_ref_no=e.poNo and d.mst_id=c.id and c.issue_purpose=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond
				group by d.po_break_down_id, c.order_type, d.total_issue, c.goods_type, d.sale_rate, d.style_ref_no,c.currency_id,c.exchange_rate";


	// echo $leftover_sql;
	$leftover_result = sql_select($leftover_sql);

	$leftover_arr = array();

	foreach ($leftover_result as $row) {
		$leftover_arr[$row[csf('style_ref_no')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$leftover_arr[$row[csf('style_ref_no')]]['sale_rate'] = $row[csf('sale_rate')];

		if($row[csf('currency_id')]==2) // usd
		{
			$sale_rate = $row[csf('sale_rate')]*$row[csf('exchange_rate')];
		}
		else
		{
			$sale_rate = $row[csf('sale_rate')];
		}
		// echo $sale_rate;

		if ($row[csf('goods_type')] == 1) {
			$leftover_arr[$row[csf('style_ref_no')]]['good_qty_sale'] += $row[csf('total_issue')];
			$leftover_arr[$row[csf('style_ref_no')]]['good_sale_value'] += ($row[csf('total_issue')] * $sale_rate);
			// $leftover_arr[$row[csf('style_ref_no')]]['good_sale_rate'] = $row[csf('sale_rate')];
		} else if($row[csf('goods_type')] == 2) {
			$leftover_arr[$row[csf('style_ref_no')]]['reject_qty_sale'] += $row[csf('total_issue')];
			$leftover_arr[$row[csf('style_ref_no')]]['reject_sale_value'] += ($row[csf('total_issue')] * $sale_rate);
			// $leftover_arr[$row[csf('style_ref_no')]]['reject_sale_rate'] = $row[csf('sale_rate')];
		}

		/*if ($row[csf('goods_type')] == 1) {
			$leftover_arr[$row[csf('style_ref_no')]]['good_qty_sale'] += $row[csf('total_issue')];
			$leftover_arr[$row[csf('style_ref_no')]]['good_sale_rate'] = $row[csf('sale_rate')];
		} else if($row[csf('goods_type')] == 2) {
			$leftover_arr[$row[csf('style_ref_no')]]['reject_qty_sale'] += $row[csf('total_issue')];
			$leftover_arr[$row[csf('style_ref_no')]]['reject_sale_rate'] = $row[csf('sale_rate')];
		}*/
	}

	$companies = '';

	foreach ($companyArr as $key => $value) {
		$tempCompany = $company_name_library[$value]['company_name'];
		$companies .= $tempCompany . ', ';
	}

	$companies = rtrim($companies, ', ');

	ob_start();
?>

<style>
	#stylewise_report .rpt_table th, #stylewise_report .rpt_table td {
	    padding: 10px;
	    text-align: center;
	}
</style>

	<div id="stylewise_report">
		<h3>Garment Leftover Report (Style Wise)</h3>
    	<p>Company Name : <?php echo $companies; ?></p>

		<table width="100%" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
    				<th style="width: 1%; padding: 2px;">SL</th>
    				<th style="width: 4%;">LC Company</th>
    				<th style="width: 4%;">Working Company</th>
    				<th style="width: 4%;">Working Floor</th>
    				<th style="width: 4%;">Buyer Name</th>
    				<th style="width: 4%;">Buyer Client</th>
    				<th style="width: 4%;">Style Name</th>
    				<th style="width: 4%;">IR/IB</th>
    				<th style="width: 4%;">Season</th>
    				<th style="width: 4%;">Job No</th>
    				<th style="width: 4%;">Item Name</th>
    				<th style="width: 4%;">Order Number</th>
    				<th style="width: 4%;">Style Des</th>
    				<th style="width: 4%;">Order Qty. Pcs</th>
    				<th style="width: 4%;">Ship Date</th>
    				<th style="width: 4%;">Reject Qty Rcv</th>
    				<th style="width: 4%;">Reject Sale Qty</th>
    				<th style="width: 4%;">Avg. Rate</th>
    				<th style="width: 4%;">Sale Value</th>
    				<th style="width: 4%;">Stock Inhand (Reject)</th>
    				<th style="width: 4%;">Stock Value (Reject)</th>
    				<th style="width: 4%;">Good Qty Rcv</th>
    				<th style="width: 4%;">Good Qty Sale</th>
    				<th style="width: 4%;">Avg. Rate</th>
    				<th style="width: 4%;">Sale Value</th>
    				<th style="width: 4%;">Stock Inhand (Good)</th>
    				<th>Stock Value (Good)</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$sl = 1;
					$totalRejectRcv = 0;
					$totalRejectSale = 0;
					$totalAvgRateRcv = 0;
					$totalSaleValueReject = 0;
					$totalStockInhandReject = 0;
					$totalStockValueReject = 0;

					$totalGoodRcv = 0;
					$totalGoodSale = 0;
					$totalAvgRateIssue = 0;
					$totalSaleValueGood = 0;
					$totalStockInhandGood = 0;
					$totalStockValueGood = 0;

					foreach ($leftover_gmts_rcv_arr as $row) 
					{
						$workingFloors = '';

						$floorIdsArr = array_unique($row['working_floor_id']);
						
						$poidsArr = array_unique(array_filter(explode(',', $row['po_break_down_ids'])));
						$poIds = rtrim(implode(',', $poidsArr), ',');

						foreach ($floorIdsArr as $floorId) {
							if($floorId) {
								$workingFloors .= $floor_name_library[$floorId] . ', ';
							}							
						}

						$workingFloors = rtrim($workingFloors, ', ');
						$buyerClient = $buyer_library[$wo_arr[$row['style_ref_no']]['client_id']];
						$styleDesc = $wo_arr[$row['style_ref_no']]['style_description'];

						$avgRateIssue = $leftover_arr[$row['style_ref_no']]['good_sale_rate'];
						$avgRateReject = $leftover_arr[$row['style_ref_no']]['reject_sale_rate'];						


						// $avgRateReject = $leftover_gmts_rcv_arr[$row['style_ref_no']]['fob_rate'];

						/*$rejectQtyRcv = $leftover_gmts_rcv_arr[$row['style_ref_no']]['reject_qty_rcv'];
						$rejectSaleQty = $leftover_arr[$row['style_ref_no']]['reject_qty_sale'];
						$stockInhandReject = ($rejectQtyRcv-$rejectSaleQty);
						$saleValueReject = ($rejectSaleQty*$avgRateReject);
						$stockValueReject = ($stockInhandReject*$avgRateReject);

						$goodQtyRcv = $leftover_gmts_rcv_arr[$row['style_ref_no']]['good_qty_rcv'];
						$goodSaleQty = $leftover_arr[$row['style_ref_no']]['good_qty_sale'];
						$stockInhandGood = ($goodQtyRcv-$goodSaleQty);
						$saleValueGood = ($goodSaleQty*$avgRateIssue);
						$stockValueGood = ($stockInhandGood*$avgRateIssue);*/

						/*************************************************************/

						$rejectQtyRcv = $leftover_gmts_rcv_arr[$row['style_ref_no']]['reject_qty_rcv'];
						$rejectSaleQty = $leftover_arr[$row['style_ref_no']]['reject_qty_sale'];
						$stockInhandReject = ($rejectQtyRcv-$rejectSaleQty);
						$saleValueReject = $leftover_arr[$row['style_ref_no']]['reject_sale_value'];
						$avgRateReject = ($saleValueReject/$rejectSaleQty);
						$stockValueReject = ($stockInhandReject*$avgRateReject);
						// $saleValueGood = $leftover_arr[$row['style_ref_no']]['reject_sale_value'];

						$goodQtyRcv = $leftover_gmts_rcv_arr[$row['style_ref_no']]['good_qty_rcv'];
						$goodSaleQty = $leftover_arr[$row['style_ref_no']]['good_qty_sale'];
						$stockInhandGood = ($goodQtyRcv-$goodSaleQty);
						// $saleValueGood = ($goodSaleQty*$avgRateGood);						

						$saleValueGood = $leftover_arr[$row['style_ref_no']]['good_sale_value'];

						$avgRateGood = ($saleValueGood/$goodSaleQty);
						$stockValueGood = ($stockInhandGood*$avgRateGood);

						$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
				?>
					<tr bgcolor="<?php echo $bgcolor;?>" id="tr_<?php echo $sl;?>" onClick="change_color('tr_<?php echo $sl; ?>','<?php echo $bgcolor; ?>')">
						<td><?php echo $sl; ?></td>
						<td><?php echo $company_name_library[$row['company_id']]['company_name']; ?></td>
						<td><?php echo $company_name_library[$row['working_company_id']]['company_short_name']; ?></td>
						<td><?php echo $workingFloors ? $workingFloors : '-'; ?></td>
						<td><?php echo $buyer_library[$row['buyer_name']]; ?></td>
						<td><?php echo $buyerClient ? $buyerClient : '-'; ?></td>
						<td><?php echo $wo_arr[$row['style_ref_no']]['style_ref_no']; ?></td>
						<td><?php echo $wo_arr[$row['style_ref_no']]['grouping']; ?></td>
						<td><?php echo $season_library[$wo_arr[$row['style_ref_no']]['season_buyer_wise']]; ?></td>
						<td><?php echo $row['job_no']; ?></td>
						<td><?php echo $gmt_item_library[$wo_arr[$row['style_ref_no']]['gmts_item_id']]; ?></td>
						<td><?php echo $row['order_no']; ?></td>
						<td><?php echo $styleDesc ? $styleDesc : '-'; ?></td>
						<td><?php echo $wo_arr[$row['style_ref_no']]['job_quantity']; ?></td>
						<td><?php echo $wo_arr[$row['style_ref_no']]['pub_shipment_date']; ?></td>
						<td>
							<a href="##" onclick="fnc_qty_details('<?php echo $poIds; ?>', <?php echo $row['working_company_id']; ?>, 'rejectQtyRcvPopUp', 'Reject Qty Receive')">
		                		<p><?php echo $rejectQtyRcv; ?></p>
		                	</a>
	                	</td>
						<td>
							<a href="##" onclick="fnc_qty_details('<?php echo $poIds; ?>', <?php echo $row['working_company_id']; ?>, 'rejectQtySalePopUp', 'Reject Qty Sale')">
		                		<p><?php echo $rejectSaleQty; ?></p>
		                	</a>
						</td>
						<td><?php echo $avgRateReject && !is_nan($avgRateReject) ? number_format($avgRateReject) : '-'; ?></td>
						<td><?php echo $saleValueReject && !is_nan($saleValueReject) ? $saleValueReject : '-'; ?></td>
						<td><?php echo $stockInhandReject && !is_nan($stockInhandReject) ? $stockInhandReject : '-'; ?></td>
						<td><?php echo $stockValueReject && !is_nan($stockValueReject) ? number_format($stockValueReject) : '-'; ?></td>
						<td>
							<a href="##" onclick="fnc_qty_details('<?php echo $poIds; ?>', <?php echo $row['working_company_id']; ?>, 'goodQtyRcvPopUp', 'Good Qty Receive')">
		                		<p><?php echo $goodQtyRcv; ?></p>
		                	</a>
						<td>
							<a href="##" onclick="fnc_qty_details('<?php echo $poIds; ?>', <?php echo $row['working_company_id']; ?>, 'goodQtySalePopUp', 'Good Qty Sale')">
								<p><?php echo $goodSaleQty; ?></p>
							</a>
						</td>
						<td><?php echo $avgRateGood && !is_nan($avgRateGood) ? number_format($avgRateGood) : '-'; ?></td>
						<td><?php echo $saleValueGood ? number_format($saleValueGood) : '-'; ?></td>
						<td><?php echo $stockInhandGood ? $stockInhandGood : '-'; ?></td>
						<td><?php echo $stockValueGood && !is_nan($stockValueGood) ? number_format($stockValueGood) : '-'; ?></td>
					</tr>
					<?php
						$sl++;
						$totalRejectRcv += $rejectQtyRcv;
						$totalRejectSale += $rejectSaleQty;
						$totalAvgRateReject += $avgRateReject && !is_nan($avgRateReject) ? $avgRateReject : 0;
						$totalSaleValueReject += $saleValueReject && !is_nan($saleValueReject) ? $saleValueReject : 0;
						$totalStockInhandReject += $stockInhandReject && !is_nan($stockInhandReject) ? $stockInhandReject : 0;
						$totalStockValueReject += $stockValueReject && !is_nan($stockValueReject) ? $stockValueReject : 0;

						$totalGoodRcv += $goodQtyRcv;
						$totalGoodSale += $goodSaleQty;
						$totalAvgRateIssue +=  $avgRateGood && !is_nan($avgRateGood) ? $avgRateGood : 0;
						$totalSaleValueGood += $saleValueGood && !is_nan($saleValueGood) ? $saleValueGood : 0;
						$totalStockInhandGood += $stockInhandGood && !is_nan($stockInhandGood) ? $stockInhandGood : 0;
						$totalStockValueGood += $stockValueGood && !is_nan($stockValueGood) ? $stockValueGood : 0;
					}
				?>
				
			</tbody>
			<tfoot>
				<tr>
    				<th colspan="15" style="text-align: right;">Total</th>
    				<th><?php echo $totalRejectRcv; ?></th>
    				<th><?php echo $totalRejectSale; ?></th>
    				<th><?php echo number_format($totalAvgRateReject); ?></th>
    				<th><?php echo $totalSaleValueReject; ?></th>
    				<th><?php echo $totalStockInhandReject; ?></th>
    				<th><?php echo number_format($totalStockValueReject); ?></th>
    				<th><?php echo $totalGoodRcv; ?></th>
    				<th><?php echo number_format($totalGoodSale); ?></th>
    				<th><?php echo number_format($totalAvgRateIssue); ?></th>
    				<th><?php echo number_format($totalSaleValueGood) ?></th>
    				<th><?php echo $totalStockInhandGood; ?></th>
    				<th><?php echo number_format($totalStockValueGood); ?></th>
				</tr>
			</tfoot>
		</table>
	</div>

<?php

	$html=ob_get_contents();
	ob_end_clean();	
			
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html**$filename";

	/*$r_id3=execute_query("delete from tmp_poid where userid=$user_id and type='621'");
    if($db_type==0) {
        if($r_id3) {
            mysql_query("COMMIT");  
        }
    }
    if($db_type==2 || $db_type==1 ) {
        if($r_id3) {
            oci_commit($con);  
        }
    }*/

    disconnect($con);
    die();
	exit();
}

if($action == 'rejectQtyRcvPopUp') 
{
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
	extract($_REQUEST);

	$color_name_library = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
	$company_name_library = return_library_array('select id, company_short_name from lib_company where status_active=1 and is_deleted=0', 'id', 'company_short_name');
	$floor_name_library = return_library_array('select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0', 'id', 'floor_name');

	$date_cond = '';

	if($db_type==0) // FOR MYSQL
	{
		if($dateFrom!="" && $dateTo!="")
		{
			$date_cond .= " and a.leftover_date between '".change_date_format($dateFrom,'yyyy-mm-dd')."' and '".change_date_format($dateTo,'yyyy-mm-dd')."'";
		}
		else if($dateFrom=="" && $dateTo!=""){
			$date_cond .= " and a.leftover_date <= '".change_date_format($dateTo,'yyyy-mm-dd')."'";
		}
		else if($dateFrom!="" && $dateTo==""){
			$date_cond .= " and a.leftover_date >= '".change_date_format($dateFrom,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2 || $db_type==1) // FOR ORACLE
	{ 
		if($dateFrom!="" && $dateTo!="")
		{
			$date_cond .= " and a.leftover_date between '".date("j-M-Y",strtotime($dateFrom))."' and '".date("j-M-Y",strtotime($dateTo))."'";
		}
		else if($dateFrom=="" && $dateTo!=""){
			$date_cond .= " and a.leftover_date <=  '".date("j-M-Y",strtotime($dateTo))."'";
		}
		else if($dateFrom!="" && $dateTo==""){
			$date_cond .= " and a.leftover_date >= '".date("j-M-Y",strtotime($dateFrom))."'";
		}		
	}
	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="995" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="100">Working Company</th>
                <th width="100">Working Floor</th>
                <th width="80">Receive Date</th>
                <th width="120">Receive ID</th>
                <th width="50">Color</th>
                <th width="60">Receive Qty</th>
                <th width="100">Grade</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
		<?php
		$categoryArr = array(1 => 'A', 2 => 'B');

		// when lib > Variable Settings > production > production update areas > Left Over: color size level
		$details_sql="SELECT a.working_company_id, a.working_floor_id, a.leftover_date, a.sys_number, sum(c.production_qnty) as production_qnty, b.category_id, a.remarks, d.color_number_id
  				from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d
 				where a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.goods_type = 2 and a.working_company_id=$workingCompanyId and a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and c.color_size_break_down_id = d.id and b.po_break_down_id in ($poBreakDownIds) $date_cond
				group by a.working_company_id, a.working_floor_id, a.leftover_date, a.sys_number, b.category_id, a.remarks, d.color_number_id
				order by a.sys_number";
		// echo $details_sql;

		$sql_result=sql_select($details_sql);

		if( !count($sql_result) ) {
			$details_sql="SELECT a.working_company_id, a.working_floor_id, a.leftover_date, a.sys_number, b.category_id, a.remarks, b.total_left_over_receive as production_qnty
  			from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b
 			where a.status_active = 1 and b.status_active = 1 and a.goods_type = 2 and a.working_company_id=$workingCompanyId and a.id = b.mst_id and b.po_break_down_id in ($poBreakDownIds) $date_cond
 			order by a.sys_number";

			$sql_result=sql_select($details_sql);
		}

		// echo $details_sql;

		$color_arr = sql_select("SELECT a.po_break_down_id, a.color_mst_id, a.color_number_id 
					from wo_po_color_size_breakdown a
					where a.status_active=1 and a.po_break_down_id in($poBreakDownIds_ and rownum=1");

		$sql_result=sql_select($details_sql); $sl=1;
		$totalRejectRcv=0;
		foreach($sql_result as $row) {
			$leftOver = $row[csf('production_qnty')];
			if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			// $delevery_amt=$row[csf("delevery_qty")]*$rate;
		?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $sl; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $sl; ?>">
        		<td><p><?php echo $sl; ?></p></td>
                <td><p><?php echo $company_name_library[$row[csf('working_company_id')]]; ?></p></td>
                <td><p><?php echo $floor_name_library[$row[csf('working_floor_id')]]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('leftover_date')]); ?></p></td>
                <td><p><?php echo $row[csf('sys_number')]; ?></p></td>
                <td><p><?php echo $color_name_library[$row[csf('color_number_id')]]; ?></p></td>
                <td><p><?php echo $leftOver; ?></p></td>
                <td><p><?php echo $categoryArr[$row[csf('category_id')]]; ?></p></td>
                <td><p><?php echo $row[csf('remarks')]; ?></p></td>
            </tr>
        <?php
	            $totalRejectRcv += $leftOver;
				$t++;
			}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th colspan="6" style="text-align: right;">Total</th>
               	<th style="text-align: center;"><?php echo $totalRejectRcv; ?></th>
              	<th style="text-align: center;"><?php // echo number_format($total_prod_amt, 2); ?></th>
              	<th style="text-align: center;"><?php // echo number_format($total_prod_amt, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

if($action == 'goodQtyRcvPopUp') 
{
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
	extract($_REQUEST);

	$color_name_library = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
	$company_name_library = return_library_array('select id, company_short_name from lib_company where status_active=1 and is_deleted=0', 'id', 'company_short_name');
	$floor_name_library = return_library_array('select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0', 'id', 'floor_name');

	$date_cond = '';

	if($db_type==0) // FOR MYSQL
		{
			if($dateFrom!="" && $dateTo!="")
			{
				$date_cond .= " and a.leftover_date between '".change_date_format($dateFrom,'yyyy-mm-dd')."' and '".change_date_format($dateTo,'yyyy-mm-dd')."'";
			}
			else if($dateFrom=="" && $dateTo!=""){
				$date_cond .= " and a.leftover_date <= '".change_date_format($dateTo,'yyyy-mm-dd')."'";
			}
			else if($dateFrom!="" && $dateTo==""){
				$date_cond .= " and a.leftover_date >= '".change_date_format($dateFrom,'yyyy-mm-dd')."'";
			}
		}
		else if($db_type==2 || $db_type==1) // FOR ORACLE
		{ 
			if($dateFrom!="" && $dateTo!="")
			{
				$date_cond .= " and a.leftover_date between '".date("j-M-Y",strtotime($dateFrom))."' and '".date("j-M-Y",strtotime($dateTo))."'";
			}
			else if($dateFrom=="" && $dateTo!=""){
				$date_cond .= " and a.leftover_date <=  '".date("j-M-Y",strtotime($dateTo))."'";
			}
			else if($dateFrom!="" && $dateTo==""){
				$date_cond .= " and a.leftover_date >= '".date("j-M-Y",strtotime($dateFrom))."'";
			}		
		}
   ?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="995" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="80">Working Company</th>
                <th width="80">Working Floor</th>
                <th width="80">Receive Date</th>
                <th width="120">Receive ID</th>
                <th width="50">Color</th>
                <th width="60">Receive Qty</th>
                <th width="80">Grade</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
		<?php
		$categoryArr = array(1 => 'A', 2 => 'B');

		// when lib > Variable Settings > production > production update areas > Left Over: color size level
		$details_sql="SELECT a.working_company_id, a.working_floor_id, a.leftover_date, a.sys_number, sum(c.production_qnty) as production_qnty, b.category_id, a.remarks, d.color_number_id
  				from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d
 				where a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.goods_type = 1 and a.working_company_id=$workingCompanyId and a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and c.color_size_break_down_id = d.id and b.po_break_down_id in ($poBreakDownIds) $date_cond
				group by a.working_company_id, a.working_floor_id, a.leftover_date, a.sys_number, b.category_id, a.remarks, d.color_number_id
				order by a.sys_number";
		// echo $details_sql;

		$sql_result=sql_select($details_sql);

		if( !count($sql_result) ) {
			$details_sql="SELECT a.working_company_id, a.working_floor_id, a.leftover_date, a.sys_number, b.category_id, a.remarks, b.total_left_over_receive as production_qnty
  			from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b
 			where a.status_active = 1 and b.status_active = 1 and a.goods_type = 1 and a.working_company_id=$workingCompanyId and a.id = b.mst_id and b.po_break_down_id in ($poBreakDownIds) $date_cond
 			order by a.sys_number";

			$sql_result=sql_select($details_sql);
		}

		/*echo '<pre>';
		print_r($sql_result);
		echo '</pre>';*/

		$sl=1;
		$totalRejectRcv=0;
		foreach($sql_result as $row) {
			$leftOver = $row[csf('production_qnty')];
			if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			// $delevery_amt=$row[csf("delevery_qty")]*$rate;
		?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $sl; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $sl; ?>">
        		<td><p><?php echo $sl; ?></p></td>
                <td><p><?php echo $company_name_library[$row[csf('working_company_id')]]; ?></p></td>
                <td><p><?php echo $floor_name_library[$row[csf('working_floor_id')]]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('leftover_date')]); ?></p></td>
                <td><p><?php echo $row[csf('sys_number')]; ?></p></td>
                <td><p><?php echo $color_name_library[$row[csf('color_number_id')]]; ?></p></td>
                <td><p><?php echo $leftOver; ?></p></td>
                <td><p><?php echo $categoryArr[$row[csf('category_id')]]; ?></p></td>
                <td><p><?php echo $row[csf('remarks')]; ?></p></td>
            </tr>
        <?php
	            $totalRejectRcv += $leftOver;
				$sl++;
			}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th colspan="6">Total</th>
               	<th style="text-align: center;"><?php echo $totalRejectRcv; ?></th>
              	<th style="text-align: center;"><?php // echo number_format($total_prod_amt, 2); ?></th>
              	<th style="text-align: center;"><?php // echo number_format($total_prod_amt, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}


if($action == 'rejectQtySalePopUp') 
{
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
	extract($_REQUEST);

	$color_name_library = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
	$company_name_library = return_library_array('select id, company_short_name from lib_company where status_active=1 and is_deleted=0', 'id', 'company_short_name');
	$floor_name_library = return_library_array('select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0', 'id', 'floor_name');
	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="995" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="100">Working Company</th>
                <th width="100">Working Floor</th>
                <th width="80">Transection Date</th>
                <th width="120">Transection ID</th>
                <th width="50">Color</th>
                <th width="60">Transection Qty</th>
                <th width="100">Purpose</th>
                <th width="100">Remarks</th>
            </tr>
        </thead>
        <tbody>
		<?php
			$purposeArr = array(1 => 'Sale', 2 => 'Gift');
			$details_sql="SELECT a.company_id, a.issue_date, a.sys_number, sum (c.production_qnty ) as issue_quantity, a.remarks, d.color_number_id, a.issue_purpose,a.working_company_id,a.working_floor_id,a.working_location_id
    				from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b, pro_leftover_gmts_issue_clr_sz c, wo_po_color_size_breakdown d
   					where a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.goods_type=1 and a.id=b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and c.color_size_break_down_id = d.id and b.po_break_down_id in ($poBreakDownIds)
   					group by a.company_id, a.issue_date, a.sys_number, a.remarks, d.color_number_id, a.issue_purpose,a.working_company_id,a.working_floor_id,a.working_location_id
   					order by a.id desc";
		$sql_result=sql_select($details_sql);

		if ( !count($sql_result) ) {
			$details_sql="SELECT a.company_id, a.issue_date, a.sys_number, b.total_issue as issue_quantity, a.issue_purpose, a.remarks,a.working_company_id,a.working_floor_id,a.working_location_id
					from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b
					where a.status_active=1 and b.status_active=1 and a.goods_type=2 and a.id=b.mst_id and b.po_break_down_id in($poBreakDownIds)
					order by a.id desc";
			$sql_result=sql_select($details_sql);
		}
		// echo $details_sql;
		$sl=1;
		$totalIssue=0;
		foreach($sql_result as $row) {
			$issueQty = $row[csf('issue_quantity')];
			if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			// $delevery_amt=$row[csf("delevery_qty")]*$rate;
		?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $sl; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $sl; ?>">
        		<td><p><?php echo $sl; ?></p></td>
                <td><p><?php echo $company_name_library[$row[csf('working_company_id')]]; ?></p></td>
                <td><p><?php echo $floor_name_library[$row[csf('working_floor_id')]]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('issue_date')]); ?></p></td>
                <td><p><?php echo $row[csf('sys_number')]; ?></p></td>
                <td><p><?php echo $color_name_library[$row[csf('color_number_id')]]; ?></p></td>
                <td><p><?php echo $issueQty; ?></p></td>
                <td><p><?php echo $purposeArr[$row[csf('issue_purpose')]]; ?></p></td>
                <td><p><?php echo $row[csf('remarks')]; ?></p></td>
            </tr>
        <?php
	            $totalIssue += $issueQty;
				$sl++;
			}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th colspan="6">Total</th>
               	<th style="text-align: center;"><?php echo $totalIssue; ?></th>
              	<th style="text-align: center;"><?php // echo number_format($total_prod_amt, 2); ?></th>
              	<th style="text-align: center;"><?php // echo number_format($total_prod_amt, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

if($action == 'goodQtySalePopUp') 
{
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
	extract($_REQUEST);

	$color_name_library = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
	$company_name_library = return_library_array('select id, company_short_name from lib_company where status_active=1 and is_deleted=0', 'id', 'company_short_name');
	$floor_name_library = return_library_array('select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0', 'id', 'floor_name');
	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="995" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="100">Working Company</th>
                <th width="100">Working Floor</th>
                <th width="80">Transection Date</th>
                <th width="120">Transection ID</th>
                <th width="50">Color</th>
                <th width="60">Transection Qty</th>
                <th width="100">Purpose</th>
                <th width="100">Remarks</th>
            </tr>
        </thead>
        <tbody>
		<?php
			$purposeArr = array(1 => 'Sale', 2 => 'Gift');

			// when lib > Variable Settings > production > production update areas > Left Over: color size level
			$details_sql="SELECT a.company_id, a.issue_date, a.sys_number, sum(c.production_qnty) as issue_quantity, a.remarks, d.color_number_id, a.issue_purpose,a.working_company_id,a.working_floor_id,a.working_location_id
    				from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b, pro_leftover_gmts_issue_clr_sz c, wo_po_color_size_breakdown d
   					where a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.goods_type = 1 and a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and c.color_size_break_down_id = d.id and b.po_break_down_id in ($poBreakDownIds) and a.issue_purpose=1
   					group by a.company_id, a.issue_date, a.sys_number, a.remarks, d.color_number_id, a.issue_purpose,a.working_company_id,a.working_floor_id,a.working_location_id
   					order by a.sys_number";
		$sql_result=sql_select($details_sql);

   		if ( !count($sql_result) ) {
			$details_sql="SELECT a.company_id, a.issue_date, a.sys_number, b.total_issue as issue_quantity, a.issue_purpose, a.remarks,a.working_company_id,a.working_floor_id,a.working_location_id
					from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b
					where a.status_active=1 and b.status_active=1 and a.goods_type=1 and a.id=b.mst_id and b.po_break_down_id in($poBreakDownIds) and a.issue_purpose=1
					order by a.sys_number";
			$sql_result=sql_select($details_sql);
		}
		
		$sl=1;
		$totalRejectRcv=0;
		foreach($sql_result as $row) {
			$issueQty = $row[csf('issue_quantity')];
			if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			// $delevery_amt=$row[csf("delevery_qty")]*$rate;
		?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $sl; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $sl; ?>">
        		<td><p><?php echo $sl; ?></p></td>
                <td><p><?php echo $company_name_library[$row[csf('working_company_id')]]; ?></p></td>
                <td><p><?php echo $floor_name_library[$row[csf('working_floor_id')]]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('issue_date')]); ?></p></td>
                <td><p><?php echo $row[csf('sys_number')]; ?></p></td>
                <td><p><?php echo $color_name_library[$row[csf('color_number_id')]]; ?></p></td>
                <td><p><?php echo $issueQty; ?></p></td>
                <td><p><?php echo $purposeArr[$row[csf('issue_purpose')]]; ?></p></td>
                <td><p><?php echo $row[csf('remarks')]; ?></p></td>
            </tr>
        <?php
	            $totalIssue += $issueQty;
				$sl++;
			}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th colspan="6">Total</th>
               	<th style="text-align: center;"><?php echo $totalIssue; ?></th>
              	<th style="text-align: center;"><?php // echo number_format($total_prod_amt, 2); ?></th>
              	<th style="text-align: center;"><?php // echo number_format($total_prod_amt, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

?>