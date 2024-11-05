<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
    if($data != 0){
		echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sales_contract_controller', this.value, 'load_drop_down_issue_bank', 'issue_bank_td' );" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sales_contract_controller', this.value, 'load_drop_down_issue_bank', 'issue_bank_td' );" );
		exit();
	}
    exit();
}

if($action == "sc_no_popup")
{
    echo load_html_head_contents("SC Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	//echo $buyer_name;
	if($buyer_name!=0){
		$buyer_sql_con = " and c.buyer_name = '$buyer_name' ";
	}
    ?>
    <script>
	var selected_id = new Array, selected_name = new Array();

       $(document).ready(function(e) {
            setFilterGrid('list_view',-1);
	});
	
	function toggle( x, origColor )
	{
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	function js_set_value_sc(id)
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
		//alert(id)
		$('#hide_sc_id').val( id );
		$('#hide_sc_no').val( ddd );
		
	}
    </script>
 
		<input type="hidden" name="hide_sc_id" id="hide_sc_id" value="" />
		<input type="hidden" name="hide_sc_no" id="hide_sc_no" value="" />
    
    <?
  
	//  $sql = "SELECT a.id,a.po_number,a.sc_lc,c.lc_year from wo_po_break_down a , com_export_lc_order_info b,com_export_lc c where a.id=b.wo_po_break_down_id and b.com_export_lc_id = c.id $buyer_sql_con and c.lc_year ='$cbo_year_id' and a.sc_lc is not null and a.is_deleted = 0 and a.status_active = 1";
	 
	 $sql = "SELECT a.id,a.po_number,c.CONTRACT_NO,c.sc_year FROM wo_po_break_down a, COM_SALES_CONTRACT_ORDER_INFO b, COM_SALES_CONTRACT c WHERE  a.id = b.wo_po_break_down_id
	 AND b.COM_SALES_CONTRACT_ID = c.id
	 AND a.is_deleted = 0 $buyer_sql_con and c.sc_year ='$cbo_year_id' and a.is_deleted = 0 and a.status_active = 1";


	 echo create_list_view("list_view", "Year,ID,PO Number,SC", "100,100,150,100","650","270",0, $sql , "js_set_value_sc", "id,CONTRACT_NO", "", 1, "id,CONTRACT_NO", $arr , "sc_year,id,po_number,CONTRACT_NO", "",'','0,0,0,0,0','',1) ;
	 exit();
}

if($action == "lc_no_popup")
{
    echo load_html_head_contents("LC Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	if($buyer_name!=0){
		$buyer_sql_con = " and c.buyer_name = '$buyer_name' ";
	}
    ?>
    <script>
	var selected_id = new Array, selected_name = new Array();

       $(document).ready(function(e) {
            setFilterGrid('list_view',-1);
	});
	
	function toggle( x, origColor )
	{
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	function js_set_value_lc(id)
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
		//alert(id)
		$('#hide_lc_id').val( id );
		$('#hide_lc_no').val( ddd );
		
	}
    </script>
 
		<input type="hidden" name="hide_lc_id" id="hide_lc_id" value="" />
		<input type="hidden" name="hide_lc_no" id="hide_lc_no" value="" />
    
    <?
  
	 $sql = "SELECT a.id,a.po_number,c.lc_year,c.export_lc_no from wo_po_break_down a , com_export_lc_order_info b,com_export_lc c where a.id=b.wo_po_break_down_id and b.com_export_lc_id = c.id $buyer_sql_con and c.lc_year ='$cbo_year_id' and a.is_deleted = 0 and a.status_active = 1";
 	
	 echo create_list_view("list_view", "Year,ID,PO Number,LC", "100,100,150,100","650","270",0, $sql , "js_set_value_lc", "id,export_lc_no", "", 1, "id,export_lc_no", $arr , "lc_year,id,po_number,export_lc_no", "",'','0,0,0,0,0','',1) ;
	 exit();
}

if($action == "order_no_popup")
{
    echo load_html_head_contents("Order Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	if($buyer_name!=0){
		$buyer_sql_con = " and b.buyer_name = '$buyer_name' ";
	}
    ?>
    <script>
	var selected_id = new Array, selected_name = new Array();

       $(document).ready(function(e) {
            setFilterGrid('list_view',-1);
	});
	
	function toggle( x, origColor )
	{
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	function js_set_value_lc(id)
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
		//alert(id)
		$('#hide_order_id').val( id );
		$('#hide_order_no').val( ddd );
		
	}
    </script>
 
		<input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
		<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
    
    <?
  
	//  $sql = "SELECT a.id,a.po_number,c.lc_year,c.export_lc_no from wo_po_break_down a , com_export_lc_order_info b,com_export_lc c where a.id=b.wo_po_break_down_id and b.com_export_lc_id = c.id and c.lc_year ='$cbo_year_id' and a.sc_lc is not null and a.is_deleted = 0 and a.status_active = 1"; 
	 
	 $sql = "SELECT a.id,a.po_number,b.style_ref_no ,TO_CHAR(b.insert_date,'YYYY') as year from wo_po_break_down a,wo_po_details_master b
	 where a.job_no_mst=b.job_no $buyer_sql_con and TO_CHAR(b.insert_date,'YYYY') ='$cbo_year_id' and a.is_deleted = 0 and a.status_active = 1 and b.status_active = 1";

	 echo create_list_view("list_view", "Year,ID,Style Ref.,PO Number", "100,100,150,100","650","270",0, $sql , "js_set_value_lc", "id,po_number", "", 1, "id,po_number", $arr , "year,id,style_ref_no,po_number", "",'','0,0,0,0,0','',1) ;

	 exit();
}

if($action == "style_no_popup")
{
    echo load_html_head_contents("Style Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	if($buyer_name!=0){
		$buyer_sql_con = " and b.buyer_name = '$buyer_name' ";
	}
    ?>
    <script>
	var selected_id = new Array, selected_name = new Array();

       $(document).ready(function(e) {
            setFilterGrid('list_view',-1);
	});
	
	function toggle( x, origColor )
	{
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	function js_set_value_lc(id)
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
		//alert(id)
		$('#style_ref_id').val( id );
		$('#style_ref_no').val( ddd );
		
	}
    </script>
 
		<input type="hidden" name="style_ref_id" id="style_ref_id" value="" />
		<input type="hidden" name="style_ref_no" id="style_ref_no" value="" />
    
    <?
  
	 $sql = "SELECT a.id,a.po_number,b.style_ref_no ,TO_CHAR(b.insert_date,'YYYY') as year from wo_po_break_down a,wo_po_details_master b
	 where a.job_no_mst=b.job_no $buyer_sql_con and TO_CHAR(b.insert_date,'YYYY') ='$cbo_year_id' and a.is_deleted = 0 and a.status_active = 1 and b.status_active = 1";

	 echo create_list_view("list_view", "Year,ID,PO Number,Style Ref.", "100,100,150,100","650","270",0, $sql , "js_set_value_lc", "id,style_ref_no", "", 1, "id,style_ref_no", $arr , "year,id,po_number,style_ref_no", "",'','0,0,0,0,0','',1) ;

	 exit();
}

if($action == 'report_generate')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_id);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$report_type=str_replace("'","",$report_type);
	$year=str_replace("'","",$cbo_year);

	$sc_no=str_replace("'","",$txt_sc_no);
	$sc_no_arr=array_unique(explode(",",$sc_no));
	$all_sc="";
	foreach($sc_no_arr as $sc_no)
		{
			$all_sc.="'".$sc_no."'"." , ";
		}
	$all_sc_no=chop($all_sc, " , ");

	$lc_no=str_replace("'","",$txt_lc_no);
	$lc_no_arr=array_unique(explode(",",$lc_no));
	$all_lc="";
	foreach($lc_no_arr as $lc_id)
		{
			$all_lc.="'".$lc_id."'"." , ";
		}
	$all_lc_no=chop($all_lc, " , ");
	
	$order_no=str_replace("'","",$txt_order_no);
	$order_no_arr=array_unique(explode(",",$order_no));
	$all_order="";
	foreach($order_no_arr as $order_id)
		{
			$all_order.="'".$order_id."'"." , ";
		}
	$all_order_no=chop($all_order, " , ");

	$style_ref_no=str_replace("'","",$style_ref_no);
	$style_no_arr=array_unique(explode(",",$style_ref_no));
	$all_order="";
	foreach($style_no_arr as $style_id)
		{
			$all_style.="'".$style_id."'"." , ";
		}
	$all_style_no=chop($all_style, " , ");
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	$sqlCond = "";
	$sqlCond .= ($sc_no!='') ? " AND  G.CONTRACT_NO IN ($all_sc_no)" : "";
	$sqlCond .= ($lc_no!='') ? " AND  E.EXPORT_LC_NO IN ($all_lc_no)" : "";
	$sqlCond .= ($order_no!='') ? " AND  A.PO_NUMBER IN ($all_order_no)" : "";
	$sqlCond .= ($style_ref_no!='') ? " AND  B.STYLE_REF_NO IN ($all_style_no)" : "";
	$sqlCond .= ($buyer_name>0) ? "AND B.BUYER_NAME=$buyer_name" : "";
	$sqlCond .= ($company_name) ? "AND B.COMPANY_NAME in($company_name)" : "";

	if($date_from!='' && $date_to!=''){ 
		if($db_type==0)
		{
			$start_date=change_date_format($date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		}
		else
		{
			$start_date=date("j-M-Y",strtotime($date_from));
			$end_date=date("j-M-Y",strtotime($date_to));
		}   
		$sqlCond .= " and A.SHIPMENT_DATE between '$start_date' and '$end_date'";     
	}
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"); 

	// echo $report_type;die;
	if($report_type==1){

		if($lc_no!=''){
		$sql = "SELECT A.ID as PO_ID, A.PO_NUMBER,A.SC_LC,A.PO_QUANTITY,A.SHIPMENT_DATE,B.BUYER_NAME,B.STYLE_REF_NO,B.ORDER_UOM,B.AVG_UNIT_PRICE,B.STYLE_DESCRIPTION,
		E.EXPORT_LC_NO, g.CONTRACT_NO 
		FROM WO_PO_DETAILS_MASTER B, WO_PO_BREAK_DOWN A , COM_SALES_CONTRACT_ORDER_INFO f, COM_SALES_CONTRACT g ,
		COM_EXPORT_LC_ATCH_SC_INFO c , COM_EXPORT_LC E 
		WHERE B.id = A.JOB_id AND A.ID = F.WO_PO_BREAK_DOWN_ID AND f.COM_SALES_CONTRACT_ID= c.COM_SALES_CONTRACT_ID AND 
		c.COM_SALES_CONTRACT_ID = g.id AND c.COM_EXPORT_LC_ID = e.id $sqlCond
		AND A.STATUS_ACTIVE = 1 AND B.STATUS_ACTIVE = 1  AND e.STATUS_ACTIVE = 1
		AND g.STATUS_ACTIVE = 1 AND c.STATUS_ACTIVE = 1  GROUP BY A.ID, A.PO_NUMBER,A.SC_LC,A.PO_QUANTITY,A.SHIPMENT_DATE,
		B.BUYER_NAME,B.STYLE_REF_NO,B.ORDER_UOM,B.AVG_UNIT_PRICE,B.STYLE_DESCRIPTION,E.EXPORT_LC_NO, g.CONTRACT_NO  ";

		}
		else{
		$sql = "SELECT A.ID as PO_ID, A.PO_NUMBER,A.SC_LC,A.PO_QUANTITY,A.SHIPMENT_DATE,B.BUYER_NAME,B.STYLE_REF_NO,B.ORDER_UOM,B.AVG_UNIT_PRICE,B.STYLE_DESCRIPTION, c.COLOR_NUMBER_ID as COLOR_ID, g.CONTRACT_NO 
		FROM WO_PO_DETAILS_MASTER B, WO_PO_BREAK_DOWN A , WO_PO_COLOR_SIZE_BREAKDOWN C, COM_SALES_CONTRACT_ORDER_INFO f, COM_SALES_CONTRACT g 
		WHERE B.id = A.JOB_id AND A.ID = F.WO_PO_BREAK_DOWN_ID AND f.COM_SALES_CONTRACT_ID = g.id 
		AND C.PO_BREAK_DOWN_ID=A.ID
		AND A.STATUS_ACTIVE = 1 AND B.STATUS_ACTIVE = 1 AND C.STATUS_ACTIVE = 1  AND f.STATUS_ACTIVE = 1 AND  g.STATUS_ACTIVE = 1 $sqlCond GROUP BY A.ID, A.PO_NUMBER,A.SC_LC,A.PO_QUANTITY,A.SHIPMENT_DATE,B.BUYER_NAME,B.STYLE_REF_NO,B.ORDER_UOM,B.AVG_UNIT_PRICE,B.STYLE_DESCRIPTION, c.COLOR_NUMBER_ID, g.CONTRACT_NO ";
		}
		
		// echo $sql;
		$dataArray=sql_select( $sql );
		$main_data_arr=array();
		foreach ($dataArray as $row)
		{
			if ($check_arr[$row['PO_ID']]=='')
			{
				$main_data_arr[$row['PO_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
				$main_data_arr[$row['PO_ID']]['SC_LC']=$row['SC_LC'];
				$main_data_arr[$row['PO_ID']]['CONTRACT_NO']=$row['CONTRACT_NO'];
				$main_data_arr[$row['PO_ID']]['EXPORT_LC_NO']=$row['EXPORT_LC_NO'];
				$main_data_arr[$row['PO_ID']]['PO_NUMBER']=$row['PO_NUMBER'];
				$main_data_arr[$row['PO_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
				$main_data_arr[$row['PO_ID']]['STYLE_DESCRIPTION']=$row['STYLE_DESCRIPTION'];
				$main_data_arr[$row['PO_ID']]['PO_QUANTITY']=$row['PO_QUANTITY'];
				$main_data_arr[$row['PO_ID']]['ORDER_UOM']=$row['ORDER_UOM'];
				$main_data_arr[$row['PO_ID']]['AVG_UNIT_PRICE']=$row['AVG_UNIT_PRICE'];
				$main_data_arr[$row['PO_ID']]['SHIPMENT_DATE']=$row['SHIPMENT_DATE'];

				$check_arr[$row['PO_ID']]=$row['PO_ID'];
			}
			$main_data_arr[$row['PO_ID']]['COLOR_ID'].=$row['COLOR_ID'].',';
		}
		//echo count($main_data_arr);
		$width=1190;
		ob_start();
			?>
		<div class="main_container" style="width:1190px;">
			<table width="<?=$width;?>" cellspacing="0" align="center">
				<!-- <tr>
					<td align="center" colspan="17" class="form_caption">
						<strong style="font-size:16px;">Company Name: <?// echo $company_library[str_replace("'", "", $company_name)]; ?></strong>
					</td>
				</tr> -->
				<tr class="form_caption">
					<td colspan="18" align="center" class="form_caption"> <strong style="font-size:16px;">SC/LC Wise Order Summary Report</strong></td>
				</tr>
			</table>
			<table class="rpt_table" rules="all" width="1190" align="left" border="1">
				<thead>
					<tr>
						<th width="30" align="center">Sl</th>
						<th width="110" align="center">Buyer</th>	
						<th width="120" align="center">Cont Ref</th>	
						<th width="100" align="center">LC</th>	
						<th width="80" align="center">Order No</th>	
						<th width="120" align="center">Style No</th>	
						<th width="120" align="center">Description</th>	
						<th width="130" align="center">Color</th>	
						<th width="100" align="center">Order Qty</th>	
						<th width="60" align="center">Uom</th>	
						<th width="100" align="center">Unit Price</th>	
						<th width="100" align="center">Total value</th>	
						<th width="110" align="center">Shipment date</th>	
					</tr>
				</thead>
				<div class="body_part" style="width:1190px;max-height:300px;overflow:auto" align="left" id="scroll_body">
					<table class="rpt_table" rules="all" width="1190" id="table_body" border="1">
						<tbody>
							<?
							$i=1;
							foreach ($main_data_arr as $po_id=>$row) 
							{
								$color_idArr=array_unique(explode(',',rtrim($row['COLOR_ID'],',')));
								// echo "<pre>";
								// print_r($color_idArr);
								$color_names="";
								foreach($color_idArr as $color_id)
								{
									$color_names.=$color_library[$color_id].', ';
								}
								$bgcolor=($i%2==0)? "#E9F3FF":"#FFFFFF";
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
									<td width="30"  align="center"><?=$i; ?></td>
									<td width="110"  align="center"style="word-break:break-all"><? echo $buyer_arr[$row['BUYER_NAME']]; ?></td>
									<td width="120"  align="center"><?=$row['CONTRACT_NO']; ?></td>
									<td width="100" align="center"><?=$row['EXPORT_LC_NO']; ?></td>
									<td width="80"  align="center"><?=$row['PO_NUMBER']; ?></td>
									<td width="120" align="center"><?=$row['STYLE_REF_NO']; ?></td>
									<td width="120" align="center"><?= $row['STYLE_DESCRIPTION']; ?></td>
									<td width="130" align="center"><? echo rtrim($color_names,', '); ?></td>
									<td width="100" align="right"><?=$row['PO_QUANTITY']; ?></td>
									<td width="60"  align="center"><?($row['ORDER_UOM']==1) ? $uom =  "Pcs" : $uom = "Set"; echo $uom; ?></td>
									<td width="100" align="right"><?=number_format($row['AVG_UNIT_PRICE'],2); ?></td>
									<td width="100" align="right"><?
									$total_val = $row['PO_QUANTITY']*$row['AVG_UNIT_PRICE'];
									echo  number_format($total_val,2); ?></td>
									<td width="110" align="center"><? echo date('d-m-Y',strtotime($row['SHIPMENT_DATE'])); ?></td>

								</tr>
								<?
								$totalPoQty+=$row['PO_QUANTITY'];
								$totalValue+=$total_val;
								$i++;
							}
							?>
							<tr>
								<td colspan='8' align="right"><strong>Total</strong></td>
								<td align="right"><strong><? echo $totalPoQty;?></strong></td>
								<td></td>
								<td></td>
								<td align="right"><strong><? echo number_format($totalValue,2);?></strong></td>
							</tr>
						</tbody>
					</table>
				</div>
			</table>
		</div>
	   <?
	}
	else
	{
		if($lc_no!=''){
		$sql = "SELECT A.ID as PO_ID, A.PO_NUMBER, A.SC_LC, A.PO_QUANTITY, A.SHIPMENT_DATE, B.BUYER_NAME, B.STYLE_REF_NO, B.ORDER_UOM,B.AVG_UNIT_PRICE, B.STYLE_DESCRIPTION, A.SHIPING_STATUS, E.EXPORT_LC_NO, g.CONTRACT_NO 
		FROM WO_PO_DETAILS_MASTER B, WO_PO_BREAK_DOWN A , COM_SALES_CONTRACT_ORDER_INFO f, COM_SALES_CONTRACT g ,
		COM_EXPORT_LC_ATCH_SC_INFO c , COM_EXPORT_LC E 
		WHERE B.id = A.JOB_id AND A.ID = F.WO_PO_BREAK_DOWN_ID AND f.COM_SALES_CONTRACT_ID= c.COM_SALES_CONTRACT_ID AND 
		c.COM_SALES_CONTRACT_ID = g.id AND c.COM_EXPORT_LC_ID = e.id $sqlCond
		AND A.STATUS_ACTIVE = 1 AND B.STATUS_ACTIVE = 1  AND e.STATUS_ACTIVE = 1
		AND g.STATUS_ACTIVE = 1 AND c.STATUS_ACTIVE = 1  GROUP BY A.ID, A.PO_NUMBER,A.SC_LC,A.PO_QUANTITY,A.SHIPMENT_DATE,
		B.BUYER_NAME, B.STYLE_REF_NO, B.ORDER_UOM, B.AVG_UNIT_PRICE, B.STYLE_DESCRIPTION, A.SHIPING_STATUS, E.EXPORT_LC_NO, g.CONTRACT_NO";
		}
		else{
		$sql = "SELECT A.ID as PO_ID, A.PO_NUMBER,A.SC_LC,A.PO_QUANTITY,A.SHIPMENT_DATE,B.BUYER_NAME,B.STYLE_REF_NO,B.ORDER_UOM,B.AVG_UNIT_PRICE,B.STYLE_DESCRIPTION, c.COLOR_NUMBER_ID as COLOR_ID, g.CONTRACT_NO, A.SHIPING_STATUS
		FROM WO_PO_DETAILS_MASTER B, WO_PO_BREAK_DOWN A , WO_PO_COLOR_SIZE_BREAKDOWN C, COM_SALES_CONTRACT_ORDER_INFO f, COM_SALES_CONTRACT g 
		WHERE B.id = A.JOB_id AND A.ID = F.WO_PO_BREAK_DOWN_ID AND f.COM_SALES_CONTRACT_ID = g.id 
		AND C.PO_BREAK_DOWN_ID=A.ID
		AND A.STATUS_ACTIVE = 1 AND B.STATUS_ACTIVE = 1 AND C.STATUS_ACTIVE = 1  AND f.STATUS_ACTIVE = 1 AND  g.STATUS_ACTIVE = 1 $sqlCond GROUP BY A.ID, A.PO_NUMBER,A.SC_LC,A.PO_QUANTITY,A.SHIPMENT_DATE,B.BUYER_NAME,B.STYLE_REF_NO,B.ORDER_UOM,B.AVG_UNIT_PRICE,B.STYLE_DESCRIPTION, c.COLOR_NUMBER_ID, g.CONTRACT_NO, A.SHIPING_STATUS";
		}
			
		//echo $sql;
		$dataArray=sql_select( $sql );
		$main_data_arr=array();
		foreach ($dataArray as $row)
		{
			if ($check_arr[$row['PO_ID']]=='')
			{
				$main_data_arr[$row['PO_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
				$main_data_arr[$row['PO_ID']]['SC_LC']=$row['SC_LC'];
				$main_data_arr[$row['PO_ID']]['CONTRACT_NO']=$row['CONTRACT_NO'];
				$main_data_arr[$row['PO_ID']]['EXPORT_LC_NO']=$row['EXPORT_LC_NO'];
				$main_data_arr[$row['PO_ID']]['PO_NUMBER']=$row['PO_NUMBER'];
				$main_data_arr[$row['PO_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
				$main_data_arr[$row['PO_ID']]['STYLE_DESCRIPTION']=$row['STYLE_DESCRIPTION'];
				$main_data_arr[$row['PO_ID']]['PO_QUANTITY']=$row['PO_QUANTITY'];
				$main_data_arr[$row['PO_ID']]['ORDER_UOM']=$row['ORDER_UOM'];
				$main_data_arr[$row['PO_ID']]['AVG_UNIT_PRICE']=$row['AVG_UNIT_PRICE'];
				$main_data_arr[$row['PO_ID']]['SHIPMENT_DATE']=$row['SHIPMENT_DATE'];
				$main_data_arr[$row['PO_ID']]['SHIPING_STATUS']=$row['SHIPING_STATUS'];

				$check_arr[$row['PO_ID']]=$row['PO_ID'];
			}
			$main_data_arr[$row['PO_ID']]['COLOR_ID'].=$row['COLOR_ID'].',';
		}
		//echo count($main_data_arr);
		$width=1290;
		ob_start();
			?>
		<div class="main_container" style="width:1290px;">
			<table width="<?=$width;?>" cellspacing="0" align="center">
				<tr class="form_caption">
					<td colspan="18" align="center" class="form_caption"> <strong style="font-size:16px;">SC/LC Wise Order Summary Report</strong></td>
				</tr>
			</table>
			<table class="rpt_table" rules="all" width="1290" align="left" border="1">
				<thead>
					<tr>
						<th width="30" align="center">Sl</th>
						<th width="110" align="center">Buyer</th>	
						<th width="120" align="center">Cont Ref</th>	
						<th width="100" align="center">LC</th>	
						<th width="80" align="center">Order No</th>	
						<th width="120" align="center">Style No</th>	
						<th width="120" align="center">Description</th>								
						<th width="100" align="center">Order Qty</th>	
						<th width="60" align="center">Uom</th>	
						<th width="100" align="center">Unit Price</th>	
						<th width="100" align="center">Total value</th>	
						<th width="110" align="center">Shipment date</th>	
						<th  align="center">Shipping Status</th>
					</tr>
				</thead>
				<div class="body_part" style="width:1290px;max-height:300px;overflow:auto" align="left" id="scroll_body">
					<table class="rpt_table" rules="all" width="1290" id="table_body" border="1">
						<tbody>
							<?
							$i=1;
							foreach ($main_data_arr as $po_id=>$row) 
							{
								$color_idArr=array_unique(explode(',',rtrim($row['COLOR_ID'],',')));
								// echo "<pre>";
								// print_r($color_idArr);
								$color_names="";
								foreach($color_idArr as $color_id)
								{
									$color_names.=$color_library[$color_id].', ';
								}
								$bgcolor=($i%2==0)? "#E9F3FF":"#FFFFFF";
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
									<td width="30"  align="center"><?=$i; ?></td>
									<td width="110"  align="center"style="word-break:break-all"><? echo $buyer_arr[$row['BUYER_NAME']]; ?></td>
									<td width="120"  align="center"><?=$row['CONTRACT_NO']; ?></td>
									<td width="100" align="center"><?=$row['EXPORT_LC_NO']; ?></td>
									<td width="80"  align="center" style="word-break: break-all;"><?=$row['PO_NUMBER']; ?></td>
									<td width="120" align="center" style="word-break: break-all;"><?=$row['STYLE_REF_NO']; ?></td>
									<td width="120" align="center" style="word-break: break-all;" ><?= $row['STYLE_DESCRIPTION']; ?></td>									
									<td width="100" align="right"><?=$row['PO_QUANTITY']; ?></td>
									<td width="60"  align="center"><?($row['ORDER_UOM']==1) ? $uom =  "Pcs" : $uom = "Set"; echo $uom; ?></td>
									<td width="100" align="right"><?=number_format($row['AVG_UNIT_PRICE'],2); ?></td>
									<td width="100" align="right"><?
									$total_val = $row['PO_QUANTITY']*$row['AVG_UNIT_PRICE'];
									echo  number_format($total_val,2); ?></td>
									<td width="110" align="center"><? echo date('d-m-Y',strtotime($row['SHIPMENT_DATE'])); ?></td>
									<td  align="center"><? echo $shipment_status[$row["SHIPING_STATUS"]]; ?></td>
								</tr>
								<?
								$totalPoQty+=$row['PO_QUANTITY'];
								$totalValue+=$total_val;
								$i++;
							}
							?>
							<tr>
								<td colspan='7' align="right"><strong>Total</strong></td>
								<td align="right"><strong><? echo $totalPoQty;?></strong></td>
								<td></td>
								<td></td>
								<td align="right"><strong><? echo number_format($totalValue,2);?></strong></td>
								<td></td>
							</tr>
						</tbody>
					</table>
				</div>
			</table>
		</div>
 <? }

}
?>