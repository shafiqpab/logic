<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if (!function_exists('pre')) 
{
	function pre($array){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
}


// if ($action=="load_drop_down_location"){
// 	echo create_drop_down( "cbo_location_name", 120, "SELECT id, location_name from lib_location where company_id=$data and status_active =1 and is_deleted=0 group by id,location_name order by location_name","id,location_name", 1, "-- Select --", $selected, "" );
// }

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 120, "SELECT location_name,id from lib_location where is_deleted=0  and status_active=1 and company_id='$data' order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/leftover_garments_receive_and_issue_report_v2_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_store_name', 'store_name_td');" );
}

if ($action=="load_drop_down_store_name")
{
	$dataEx = explode("_", str_replace("'", "", $data));
	$company_id = $dataEx[0];
	$location_id = $dataEx[1];
	$locationCond = "";
	if($location_id !=0)
	{
		$locationCond = " and a.location_id=$location_id";
	}
	echo create_drop_down( "cbo_store_name", 120, "SELECT a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=30  and a.status_active=1 and a.is_deleted=0 and a.company_id in($company_id) $locationCond group by a.id ,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", "0", "","" );
	//and b.category_type in ($item_cate_credential_cond) $store_location_credential_cond
	exit();
}

if ($action=="load_drop_down_buyer") {
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
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
    
    <?php
	
		exit();
}

if($action=='orderno_search_popup')
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
				name += selected_name[i] + ',';
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
                    <input type="hidden" name="hdnOrderNo" id="hdnOrderNo" />
                    <input type="hidden" name="hdnOrderId" id="hdnOrderId" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?php 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?php
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?php echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<?php echo $job_no.'**'.$job_year; ?>', 'create_order_no_search_list_view', 'search_div', 'leftover_garments_receive_and_issue_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$company_id=$data[0];
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
			where a.status_active=1 and b.status_active=1 and a.company_name in($company_id) and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond and a.id = b.job_id";			

	// echo $sql;
		
	echo create_list_view('tbl_list_search', 'Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date', '80,80,50,70,140,170', '760', '220', 0, $sql, 'js_set_value', 'id,po_number', '', 1, 'company_name,buyer_name,0,0,0,0,0', $arr, 'company_name,buyer_name,year,job_no,style_ref_no,po_number,pub_shipment_date', '', '', '0,0,0,0,0,0,3', '', 1);
   exit(); 
}


if($action=="report_generate") 
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//$report_type=str_replace("'","",$reporttype);
	//cbo_company_name*cbo_location_id*cbo_buyer_name*txt_date_from*txt_date_to*cbo_search_by*txt_search_text
	$report_title=str_replace("'","",$report_title);
	
	$company_id = str_replace("'", '', $cbo_company_name);
	$cbo_location_id = str_replace("'", '', $cbo_location_name); 
	$store_name = str_replace("'", '', $cbo_store_name); 
	$cbo_buyer_name = str_replace("'", '', $cbo_buyer_name);
	$cbo_goods_type = str_replace("'", '', $cbo_goods_type);
	$txt_job_no = str_replace("'", '', $txt_job_no);
	$txt_style_ref = str_replace("'", '', $txt_style_ref);
	$txt_int_ref = str_replace("'", '', $txt_int_ref);
	$txt_order_no = str_replace("'", '', $txt_order_no);
	$date_from=str_replace("'", '', $txt_date_from);
	$date_to=str_replace("'", '', $txt_date_to);
	$dateInfo = '';
	$buyer_id_cond = '';
	$goods_type_cond='';
	$store_cond='';
	$issue_buyer_id_cond = '';
	$job_cond = '';
	$int_ref_cond = '';
	$order_cond = '';
	$date_cond = '';
	$cbo_location="";


	//echo $txt_int_ref; die;

	$company_arr=return_library_array( "select id, company_name from lib_company where id=$company_id", 'id', 'company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$location_arr=return_library_array( "select id, location_name from lib_location", 'id', 'location_name');
	
	if( $cbo_buyer_name !=0 ) {
		$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";
		$issue_buyer_id_cond=" and b.buyer_id in($cbo_buyer_name)";
	}
	if($cbo_goods_type !=0){
		$goods_type_cond =" and a.goods_type=$cbo_goods_type ";
		
	}
	if($store_name)
	{
		$store_cond =" and a.store_name=$store_name "; 
	}
	

	if ($cbo_location_id!=0) $cbo_location.=" and a.location=$cbo_location_id";

	
	if($txt_job_no != '') {
		$job_cond = " and b.job_no like '%$txt_job_no%'";
	}
	
	if(!empty($txt_int_ref)) {
		$int_ref_cond = " and c.grouping like '%$txt_int_ref%'";
	}

	if($txt_style_ref != '') {
		$style_cond = " and b.style_ref_no like '%$txt_style_ref%'";
	}

	if($txt_order_no != '') {
		$orderNosArr = explode(',', $txt_order_no);
		$orderNosStr = implode('\',\'', $orderNosArr);
		$order_cond = " and b.order_no in('$orderNosStr')";
	}
	
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") {
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
		$date_cond=" and a.leftover_date between '$start_date' and '$end_date'";
	}

	if($start_date != '' && $end_date != '') {
		$dateInfo = $start_date.' To '.$end_date;
	}

	//================================== For Receive ================================== 
	$sql="SELECT a.id, a.location, a.order_type, a.buyer_name, a.goods_type, b.style_ref_no, b.po_break_down_id, b.order_no, b.total_left_over_receive, b.leftover_amount, b.job_no, a.company_id, c.grouping,b.category_id
	from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, wo_po_break_down c
	where a.id=b.mst_id and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $cbo_location  
	 
	$goods_type_cond $store_cond $buyer_id_cond $date_cond $job_cond $style_cond $order_cond $int_ref_cond and b.po_break_down_id is not null";

	//   echo $sql;die;

	$sql_result = sql_select($sql);

	if (count($sql_result) == 0 ) {
        echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Leftover Garments Receive Data Not Found ** </h1>" ;
        die();
      }
	
	foreach($sql_result as $row) {
		$po_id_array[$row['PO_BREAK_DOWN_ID']] = $row['PO_BREAK_DOWN_ID']; 
		$leftover_data_arr[$row['PO_BREAK_DOWN_ID']][$row['ORDER_TYPE']][$row['GOODS_TYPE']]['location']=$row['LOCATION'];
		$leftover_data_arr[$row['PO_BREAK_DOWN_ID']][$row['ORDER_TYPE']][$row['GOODS_TYPE']]['buyer_name']=$row['BUYER_NAME'];
		$leftover_data_arr[$row['PO_BREAK_DOWN_ID']][$row['ORDER_TYPE']][$row['GOODS_TYPE']]['style_ref_no']=$row['STYLE_REF_NO'];
		$leftover_data_arr[$row['PO_BREAK_DOWN_ID']][$row['ORDER_TYPE']][$row['GOODS_TYPE']]['order_no']=$row['ORDER_NO'];
		$leftover_data_arr[$row['PO_BREAK_DOWN_ID']][$row['ORDER_TYPE']][$row['GOODS_TYPE']]['job_no']=$row['JOB_NO'];
		$leftover_data_arr[$row['PO_BREAK_DOWN_ID']][$row['ORDER_TYPE']][$row['GOODS_TYPE']]['internal_ref']=$row['GROUPING'];
		$leftover_data_arr[$row['PO_BREAK_DOWN_ID']][$row['ORDER_TYPE']][$row['GOODS_TYPE']]['company_id']=$row['COMPANY_ID'];
		$leftover_data_arr[$row['PO_BREAK_DOWN_ID']][$row['ORDER_TYPE']][$row['GOODS_TYPE']]['total_receive']+=$row['TOTAL_LEFT_OVER_RECEIVE'];
		$leftover_data_arr[$row['PO_BREAK_DOWN_ID']][$row['ORDER_TYPE']][$row['GOODS_TYPE']]['receive'][$row['CATEGORY_ID']]+= $row['TOTAL_LEFT_OVER_RECEIVE'];
	}
	
	unset($sql_result);
	
	// ================================== For Issue ==================================
	$po_id_cond = where_con_using_array($po_id_array,0,'b.po_break_down_id');
	$sql_issue="SELECT a.id, a.goods_type, a.order_type,b.po_break_down_id as po_id, b.order_no, b.style_ref_no, b.total_issue,b.category_id
	from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $po_id_cond";

	// echo $sql_issue;
	$sql_result_issue = sql_select($sql_issue);
	foreach($sql_result_issue as $v) { 
		$leftover_issue_data_arr[$v['PO_ID']][$v['ORDER_TYPE']][$v['GOODS_TYPE']]['total_issue'][$v['CATEGORY_ID']]	+= $v['TOTAL_ISSUE'];
	}
	// pre($leftover_issue_data_arr); die;

	// ================================== SEWING OUT DATA ==================================
	$po_con = where_con_using_array($po_id_array,0,'a.po_break_down_id');
	$prod_sql = "SELECT a.po_break_down_id as po_id, a.production_quantity as prod_qty FROM pro_garments_production_mst a WHERE a.status_active=1 and a.is_deleted=0 and a.production_type=5 $po_con";
	// echo $prod_sql; die;
	$prod_res = sql_select($prod_sql);
	$po_wise_sew_out_array = array();
	foreach ($prod_res as $v) {
		$po_wise_sew_out_array[$v['PO_ID']] += $v['PROD_QTY'];
	}
	// pre($po_wise_sew_out_array); die;

	// ================================== SHIPMENT DATA ==================================
	$po_con = where_con_using_array($po_id_array,0,'a.po_break_down_id');
	$shipment_sql = "SELECT a.po_break_down_id as po_id, a.ex_factory_qnty as ship_qty FROM pro_ex_factory_mst a WHERE a.status_active=1 and a.is_deleted=0 and a.entry_form != 85 $po_con";
	// echo $shipment_sql; die;
	$shipment_res = sql_select($shipment_sql);
	$po_wise_shipment_array = array();
	foreach ($shipment_res as $v) {
		$po_wise_shipment_array[$v['PO_ID']] += $v['SHIP_QTY'];
	}

	
	
	//$leftover_data_arr = $sql_result;
	$issue_purpose_arr = array(1=>"Sell",2=>"Gift",3=>"Others");
	ob_start();
	?>
	<style>
		table tbody tr td{
			word-break: break-all;
		}
	</style>
		<div style="width: 2180px;" align="center">
    		<caption>
        		<strong>
        			<?php echo $company_arr[$company_id].'<br>'.$report_title.'<br>'.$dateInfo;?>
        		</strong>
        	</caption>
	        <table id="table_header_1" width="2420" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <thead>
	                <tr>
						<th rowspan='2' width="30">SL</th>
						<th rowspan='2' width="110">location</th>
						<th rowspan='2' width="100">Buyer</th>
						<th rowspan='2' width="100">Style</th> 
						<th rowspan='2' width="100">Order No</th>
						<th rowspan='2' width="100">Job NO.</th>
						<th rowspan='2' width="100">Internal Ref.</th>
						<th rowspan='2' width="100">Goods Type</th>
						<th rowspan='2' width="80">Order Type</th>
						<th rowspan='2' width="80">Calculating Leftover</th>
						<th rowspan='2' width="80">Leftover Balance</th>
						<th colspan="6" >Rcv Qty</th>
						<th colspan="6" >Issue Qty</th>
						<th colspan="6" >Stock In Hand</th>
	                </tr>
					<tr>
							<th width="80">Goods A Grade</th>
							<th width="80">Goods B Grade</th> 
							<th width="80">Goods C Grade</th>  <!--remove rejection name include C grade--> 
							<th width="80">Goods D Grade</th>
							<th width="80">Sample</th>

							<th width="80">Total Receive </th>
							<th width="80">Goods A Grade</th>
							<th width="80">Goods B Grade</th> 
							<th width="80">Goods C Grade</th> 
							<th width="80">Goods D Grade</th> 
							<th width="80">Sample</th>

							<th width="80">Total Issue </th> 
							<th width="80">Goods A Grade</th>
							<th width="80">Goods B Grade</th> 
							<th width="80">Goods C Grade</th>
							<th width="80">Goods D Grade</th> 
							<th width="80">Sample</th>

							<th width="80">Total  </th> 
						</tr>
	            </thead>
	        </table>
	        <div style="width: 2420px; max-height:400px; overflow-y:scroll" id="scroll_body">
	            <table class="rpt_table" width="2420" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	            	<tbody>
	                	<?php
	                    $goods_type_arr = array(1=>"Good GMT In Hand", 2=>"Damage GMT", 3=>"Leftover Sample");	
						$i=1;
						$total_left_over_blnc = $total_rec_cat_a = $total_rec_cat_b = $total_rec_cat_c =$rec_cat_d=$total_rec_cat_d=$rec_cat_sample= $total_rec_cat_sample=$total_receive_qty = $total_issue_cat_a = $total_issue_cat_b = $total_issue_cat_c = $total_issue_cat_d = $total_issue_cat_sample = $total_issue_qty = $total_stock_cat_a = $total_stock_cat_b = $total_stock_cat_c =$total_stock_cat_d= $total_stock_cat_sample = $total_stock_qty =$issue_cat_d= $total_issue_cat_d=0;
						// $total_rcv_qty=0;
						// $total_issue_qty=0;
						// $total_stock_qty=0;
						// echo "<pre>";
						// print_r($leftover_data_arr);die;
							foreach($leftover_data_arr as $orderId => $ordNoArr) {
								foreach ($ordNoArr as $ordtype => $ordtypeArr) {
									foreach ($ordtypeArr as $goodsType => $row) { 
										$total_rcv_qty+=$row['total_receive'];
										$sew_out  = $po_wise_sew_out_array[$orderId] ? $po_wise_sew_out_array[$orderId] : 0 ;
										$delevary  = $po_wise_shipment_array[$orderId] ? $po_wise_shipment_array[$orderId] : 0 ;
										$cal_left_over =($sew_out>0 && $delevary>0) ? $sew_out - $delevary : 0;

										// ================================== Category wise receive ==================================
										
										$rec_cat_a = $row['receive'][1] ? $row['receive'][1] : 0;
										$rec_cat_b = $row['receive'][2] ? $row['receive'][2] : 0;
										$rec_cat_c = $row['receive'][3] ? $row['receive'][3] : 0; 
										$rec_cat_d = $row['receive'][4] ? $row['receive'][4] : 0;
										$rec_cat_sample = $row['receive'][5] ? $row['receive'][5] : 0;
										$total_rec_cat_a +=  $rec_cat_a;
										$total_rec_cat_b +=  $rec_cat_b;
										$total_rec_cat_c +=  $rec_cat_c;
										$total_rec_cat_d +=  $rec_cat_d;
										$total_rec_cat_sample +=  $rec_cat_sample;
										$receive_qty = $rec_cat_a+ $rec_cat_b + $rec_cat_c + $rec_cat_d + $rec_cat_sample;
										$total_receive_qty += $receive_qty;
										
										

										// ================================== Category wise Issue ==================================
										$issue_arr = $leftover_issue_data_arr[$orderId ][$ordtype][$goodsType];
										$issue_cat_a = $issue_arr['total_issue'][1] ? $issue_arr['total_issue'][1] : 0;
										$issue_cat_b = $issue_arr['total_issue'][2] ? $issue_arr['total_issue'][2] : 0;
										$issue_cat_c = $issue_arr['total_issue'][3] ? $issue_arr['total_issue'][3] : 0; 
										$issue_cat_d = $issue_arr['total_issue'][4] ? $issue_arr['total_issue'][4] : 0;
										$issue_cat_sample = $issue_arr['total_issue'][5] ? $issue_arr['total_issue'][5] : 0;
										$total_issue_cat_a +=  $issue_cat_a;
										$total_issue_cat_b +=  $issue_cat_b;
										$total_issue_cat_c +=  $issue_cat_c;
										$total_issue_cat_d += $issue_cat_d  ;
										$total_issue_cat_sample += $issue_cat_sample  ;
										$issue_qty = $issue_cat_a+ $issue_cat_b + $issue_cat_c+$issue_cat_d+$issue_cat_sample;
										$total_issue_qty += $issue_qty;

										// ================================== Stock in Hand ==================================
										$stock_cat_a = $rec_cat_a - $issue_cat_a;
										$stock_cat_b = $rec_cat_b - $issue_cat_b;
										$stock_cat_c = $rec_cat_c - $issue_cat_c;
										$stock_cat_d = $rec_cat_d - $issue_cat_d;
										$stock_cat_sample = $rec_cat_sample - $issue_cat_sample;
										$total_stock_cat_a +=  $stock_cat_a;
										$total_stock_cat_b +=  $stock_cat_b;
										$total_stock_cat_c +=  $stock_cat_c; 
										$total_stock_cat_d +=  $stock_cat_d; 
										$total_stock_cat_sample +=  $stock_cat_sample; 
										$stock_qty = $receive_qty - $issue_qty; 
										$total_stock_qty += $stock_qty; 

										$left_over_blnc =  $receive_qty - $cal_left_over ; 
										$total_left_over_blnc += $left_over_blnc;
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<?php echo $bgcolor;?>" onClick="change_color('tr_<?php echo $i; ?>','<?php echo $bgcolor;?>')" id="tr_<?php echo $i; ?>">
										<td width="30"><?= $i;?></td>
										<td width="110"><?= $location_arr[$row['location']]; ?></td>
										<td width="100"><?= $buyer_arr[$row['buyer_name']]; ?></td>
										<td width="100"><?= $row['style_ref_no']; ?></td> 
										<td width="100"><?= $row['order_no']; ?></td>
										<td width="100"><?= $row['job_no'];?></td>
										<td width="100"><?= $row['internal_ref'];?></td>
										<td width="100"><?= $goods_type_arr[$goodsType];?></td>
										<td width="80"><?= $order_source[$ordtype];?></td>
										<td width="80" align="right" title="Sew out - Ex-factory = <?=$sew_out .' - '. $delevary?> "><?= $cal_left_over; ?></td>
										<td width="80" align="right" title="Total Receive -Calculating leftover"><?= $left_over_blnc; ?></td>
										<td width="80" align="right"><?= $rec_cat_a ?> </td> 
										<td width="80" align="right"><?= $rec_cat_b ?> </td> 
										<td width="80" align="right"><?= $rec_cat_c       ?> </td>  
										<td width="80" align="right"><?= $rec_cat_d       ?> </td>
										<td width="80" align="right"><?= $rec_cat_sample       ?> </td>
										<td width="80" align="right"><?= $receive_qty ?> </td> 
										<td width="80" align="right"><?= $issue_cat_a ?> </td> 
										<td width="80" align="right"><?= $issue_cat_b ?> </td> 
										<td width="80" align="right"><?= $issue_cat_c ?> </td> 
										<td width="80" align="right"><?= $issue_cat_d ?> </td> 
										<td width="80" align="right"><?= $issue_cat_sample ?> </td> 
										<td width="80" align="right"><?= $issue_qty ?> </td>  
										<td width="80" align="right"><?= $stock_cat_a ?> </td> 
										<td width="80" align="right"><?= $stock_cat_b ?> </td> 
										<td width="80" align="right"><?= $stock_cat_c ?> </td> 
										<td width="80" align="right"><?= $stock_cat_d ?> </td>   
										<td width="80" align="right"><?= $stock_cat_sample ?> </td>   
										<td width="80" align="right"><?= $stock_qty ?> </td>
									</tr>
									<?php
									$i++;
									}
								}
							}
						?>
					</tbody>
					<tfoot>
	                    <th colspan="9">Total :</th>
	                    <th align="right" ><?= ""; ?></th>
	                    <th align="right" ><?= $total_left_over_blnc; ?></th>
	                    <th align="right" ><?= $total_rec_cat_a; ?></th>
	                    <th align="right" ><?= $total_rec_cat_b; ?></th>
	                    <th align="right" ><?= $total_rec_cat_c; ?></th>
						<th align="right" ><?= $total_rec_cat_d; ?></th>
						<th align="right" ><?= $total_rec_cat_sample; ?></th>
	                    <th align="right" ><?= $total_receive_qty; ?></th>
	                    <th align="right" ><?= $total_issue_cat_a; ?></th>
	                    <th align="right" ><?= $total_issue_cat_b; ?></th>
	                    <th align="right" ><?= $total_issue_cat_c; ?></th>
						<th align="right" ><?= $total_issue_cat_d; ?></th>
						<th align="right" ><?= $total_issue_cat_sample; ?></th>
	                    <th align="right" ><?= $total_issue_qty; ?></th>
	                    <th align="right" ><?= $total_stock_cat_a; ?></th>
	                    <th align="right" ><?= $total_stock_cat_b; ?></th>
	                    <th align="right" ><?= $total_stock_cat_c; ?></th>
						<th align="right" ><?= $total_stock_cat_d; ?></th>
						<th align="right" ><?= $total_stock_cat_sample; ?></th>
	                    <th align="right" ><?= $total_stock_qty; ?></th>
	                </tfoot>
	            </table>
	        </div>
		</div>
	<?php
	
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
    echo "$html****$filename****$report_type"; 
	exit();	
}

if($action == 'rcvQtyPopUp') {
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
	extract($_REQUEST);

	$color_name_library = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
	$company_name_library = return_library_array('select id, company_name from lib_company where status_active=1 and is_deleted=0', 'id', 'company_name');
	$size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');

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
    <table width="590" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="100">Company</th>
                <th width="80">Receive Date</th>
                <th width="120">Receive ID</th>
                <th width="50">Color</th>
                <th width="50">Size</th>
                <th width="60">Receive Qty</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
		<?php
			// when lib > Variable Settings > production > production update areas > Left Over: color size level
			$details_sql="SELECT a.company_id, a.working_floor_id, a.leftover_date, a.sys_number, sum(c.production_qnty) as production_qnty, b.category_id, b.remarks, d.color_number_id, d.size_number_id
	  				from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d
	 				where a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.goods_type = $goodsType and a.company_id=$companyId and a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and c.color_size_break_down_id = d.id and b.po_break_down_id in ($poBreakDownIds) $date_cond
					group by a.company_id, a.working_floor_id, a.leftover_date, a.sys_number, b.category_id, b.remarks, d.color_number_id, d.size_number_id
					order by a.sys_number";
			//echo $details_sql;

			$sql_result=sql_select($details_sql);

			if( !count($sql_result) ) {
				$details_sql="SELECT a.company_id, a.working_floor_id, a.leftover_date, a.sys_number, b.category_id, b.remarks, b.total_left_over_receive as production_qnty
	  			from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b
	 			where a.status_active = 1 and b.status_active = 1 and a.goods_type = $goodsType and a.company_id=$companyId and a.id = b.mst_id and b.po_break_down_id in ($poBreakDownIds) $date_cond
	 			order by a.sys_number";

				$sql_result=sql_select($details_sql);
			}

		// echo $details_sql;

		$sl=1;
		$totalRcv=0;
		foreach($sql_result as $row) {
			$leftOver = $row[csf('production_qnty')];
			if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			// $delevery_amt=$row[csf("delevery_qty")]*$rate;
		?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $sl; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $sl; ?>">
        		<td><p><?php echo $sl; ?></p></td>
                <td><p><?php echo $company_name_library[$row[csf('company_id')]]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('leftover_date')]); ?></p></td>
                <td><p><?php echo $row[csf('sys_number')]; ?></p></td>
                <td><p><?php echo $color_name_library[$row[csf('color_number_id')]]; ?></p></td>
                <td><p><?php echo $size_library[$row[csf('size_number_id')]]; ?></p></td>
                <td><p><?php echo $leftOver; ?></p></td>
                <td><p><?php echo $row[csf('remarks')]; ?></p></td>
            </tr>
        <?php
	            $totalRcv += $leftOver;
				$sl++;
			}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th colspan="6">Total</th>
               	<th style="text-align: center;"><?php echo $totalRcv; ?></th>
               	<th></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

if($action == 'issueQtyPopUp') {
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
	extract($_REQUEST);

	$color_name_library = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
	$company_name_library = return_library_array('select id, company_name from lib_company where status_active=1 and is_deleted=0', 'id', 'company_name');
	$size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');

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
    <table width="590" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="100">Company</th>
                <th width="80">Issue Date</th>
                <th width="120">Issue ID</th>
                <th width="50">Color</th>
                <th width="50">Size</th>
                <th width="60">Issue Qty</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
		<?php
			// when lib > Variable Settings > production > production update areas > Left Over: color size level
			$details_sql="SELECT a.company_id, a.issue_date, a.sys_number, sum (c.production_qnty ) as issue_quantity, b.remarks, d.color_number_id, a.issue_purpose,a.working_company_id,a.working_floor_id,a.working_location_id, d.size_number_id
    				from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b, pro_leftover_gmts_issue_clr_sz c, wo_po_color_size_breakdown d
   					where a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.goods_type=$goodsType and a.id=b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and c.color_size_break_down_id = d.id and b.po_break_down_id in ($poBreakDownIds)
   					group by a.company_id, a.issue_date, a.sys_number, b.remarks, d.color_number_id, a.issue_purpose,a.working_company_id,a.working_floor_id,a.working_location_id, d.size_number_id, a.id
   					order by a.id desc";
			$sql_result=sql_select($details_sql);

			if ( !count($sql_result) ) {
				$details_sql="SELECT a.company_id, a.issue_date, a.sys_number, b.total_issue as issue_quantity, a.issue_purpose, b.remarks,a.working_company_id,a.working_floor_id,a.working_location_id
						from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b
						where a.status_active=1 and b.status_active=1 and a.goods_type=$goodsType and a.id=b.mst_id and b.po_break_down_id in($poBreakDownIds)
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
                <td><p><?php echo $company_name_library[$row[csf('company_id')]]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('issue_date')]); ?></p></td>
                <td><p><?php echo $row[csf('sys_number')]; ?></p></td>
                <td><p><?php echo $color_name_library[$row[csf('color_number_id')]]; ?></p></td>
                <td><p><?php echo $size_library[$row[csf('size_number_id')]]; ?></p></td>
                <td><p><?php echo $issueQty; ?></p></td>
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
               	<th></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

?>