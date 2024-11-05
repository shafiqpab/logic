<?
/*-------------------------------------------- Comments
Version          :  V1
Purpose			 : This form will create print Booking
Functionality	 :
JS Functions	 :
Created by		 : MONZU
Creation date 	 :
Requirment Client:
Requirment By    :
Requirment type  :
Requirment       :
Affected page    :
Affected Code    :
DB Script        :
Updated by 		 :
Update date		 :
QC Performed BY	 :
QC Date			 :
Comments		 : From this version oracle conversion is start
Entry From 		 : 403
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
//---------------------------------------------------- Start---------------------------------------------------------------------------
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$country_name_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );


if ($action=="load_drop_down_buyer"){
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );//load_drop_down( 'print_booking_multijob_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'print_booking_multijob_controller', this.value, 'load_drop_down_season', 'season_td');
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );//load_drop_down( 'print_booking_multijob_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'print_booking_multijob_controller', this.value, 'load_drop_down_season', 'season_td');
		exit();
	}
}
if ($action == "load_button") {
	echo "$('#print_booking').hide();";
	echo "$('#print_booking1').hide();";
	// echo "$('#print_booking3').hide();";
	// echo "$('#print_booking4').hide();";
	// echo "$('#print_booking5').hide();";

	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $data . "' and module_id=2 and report_id=244 and is_deleted=0 and status_active=1");
	foreach (explode(',', $print_report_format) as $button_id) {
		if ($button_id == 13) {
			echo "$('#print_booking').show();";
		}
		if ($button_id == 14) {
			echo "$('#print_booking1').show();";
		}
		// if ($button_id == 16) {
		// 	echo "$('#print_booking3').show();";
		// }
		// if ($button_id == 177) {
		// 	echo "$('#print_booking4').show();";
		// }
		// if ($button_id == 175) {
		// 	echo "$('#print_booking5').show();";
		// }
	}
	exit();
}

if ($action=="load_drop_down_floor")
{
	$exdata=explode("__",$data);
	if($exdata[0]==3 || $exdata[0]==5)
	{
		echo create_drop_down( "cbo_floor_id", 130, "select id, floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id='$exdata[1]' and location_id='$exdata[2]' and production_process in (7) order by floor_name","id,floor_name", 1, "--Select--", $selected, "",0 );
	}
	else
	{
		echo create_drop_down("cbo_floor_id", 130, $blank_array, "", 1, "--Select--", 0, "", 0);
	}
	exit();   	 
}

if ($action=="load_drop_down_party_location")
{
	$sql="select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name";
	//echo $sql;
	
	echo create_drop_down( "cbo_party_location", 130, $sql,"id,location_name", 1, "-- Select Location --", $selected,  "fnc_floorload();" );		
	exit(); 

}
if ($action=="load_drop_down_season")
{
	//echo "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC";
	echo create_drop_down( "cbo_season_id", 100, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	 //echo "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_id_cond order by brand_name ASC";
	echo create_drop_down( "cbo_brand_id", 100, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}
if ($action == "supplier_company_action")
{
	$data=explode("_",$data);
	$company=$data[0];
	$pay_mode_id=$data[1];
	if($pay_mode_id==3 || $pay_mode_id==5)
	{
		$sql = "select c.id, c.company_name as label from lib_company c where c.status_active=1 and c.is_deleted=0 group by c.id, c.company_name order by company_name";
	}
	else
	{
		 $sql = "select c.id, c.supplier_name as label from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$company and b.party_type =23 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";
	}

	/*if($pay_mode!=3 || $pay_mode!=5)
	{
		$sql = "select c.id, c.supplier_name as label from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$company and b.party_type =23 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";
	}
	else
	{
		$sql = "select c.id, c.company_name as label from lib_company c where c.status_active=1 and c.is_deleted=0 group by c.id, c.company_name order by company_name";
	}*/
	//echo $sql;
	$result = sql_select($sql);
	$supplierArr = array();
	foreach($result as $key => $val){
		$supplierArr[$key]["id"]=$val[csf("id")];
		$supplierArr[$key]["label"]=$val[csf("label")];
	}
	echo json_encode($supplierArr);
    exit();
}

if ($action=="fabric_emb_item_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>

	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}

		var selected_id = new Array();
		var selected_name = new Array();
		var selected_item=new Array();
		var selected_po=new Array();

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push($('#txt_job_no' + str).val());
					selected_item.push($('#precost_emb_id' + str).val());
					selected_po.push($('#txt_po_id' + str).val());
				}
				else{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i,1 );
					selected_item.splice( i,1 );
					selected_po.splice( i,1 );
				}
			}
			var id = '';
			var job = '';
			var precost_emb_id='';
			var txt_po_id='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				job += selected_name[i] + ',';
				precost_emb_id+=selected_item[i]+ ',';
				txt_po_id+=selected_po[i]+ ',';
			}
			id = id.substr( 0, id.length - 1 );
			job = job.substr( 0, job.length - 1 );
			precost_emb_id = precost_emb_id.substr( 0, precost_emb_id.length - 1 );
			txt_po_id = txt_po_id.substr( 0, txt_po_id.length - 1 );
			$('#txt_selected_id').val( id );
			$('#txt_job_id').val( job );
			$('#emb_id').val( precost_emb_id );
			$('#txt_selected_po').val( txt_po_id );
		}

	function check(){
		var cbo_company_name= document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		var cbo_year_selection=document.getElementById('cbo_year_selection').value;

		var cbo_currency= document.getElementById('cbo_currency').value;
		var txt_style=document.getElementById('txt_style').value;
		var txt_order_search=document.getElementById('txt_order_search').value;
		var txt_job=document.getElementById('txt_job').value;

		var cbo_item=document.getElementById('cbo_item').value;
		var txt_ref_no=document.getElementById('txt_ref_no').value;
		//show_list_view ( cbo_company_name+'_'+cbo_buyer_name+'_'+cbo_supplier_name+'_'+cbo_year_selection+'_'+cbo_currency+'_'+txt_style+'_'+txt_order_search+'_'+txt_job+'_'+cbo_item+'_'+txt_ref_no+'_'+'<?// echo $txt_booking_no; ?>'+'_'+'<?// echo $cbo_level; ?>', 'create_fnc_process_data', 'search_div', 'print_booking_multijob_controller',setFilterGrid('tbl_list_search',-1))

		show_list_view (cbo_company_name+'_'+cbo_buyer_name+'_'+cbo_supplier_name+'_'+cbo_year_selection+'_'+cbo_currency+'_'+txt_style+'_'+txt_order_search+'_'+txt_job+'_'+cbo_item+'_'+txt_ref_no+'_'+'<?=$txt_booking_no; ?>'+'_'+'<?=$cbo_level; ?>', 'create_fnc_process_data', 'search_div', 'print_booking_multijob_controller','setFilterGrid(\'tbl_list_search\',-1)');

	}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th width="100">Style Ref </th>
                <th width="100">Job No </th>
                <th width="60">Year</th>
                <th width="100">M.Style / Int. Ref. No</th>
                <th width="100">Order No</th>
                <th width="120">Item Name</th>
                <th>&nbsp;
                    <input type="hidden"  style="width:20px" name="txt_garments_nature" id="txt_garments_nature" value="<? echo $garments_nature;?>" />
                    <input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $company_id;?>" />
                    <input type="hidden" style="width:20px" name="cbo_buyer_name" id="cbo_buyer_name" value="<? echo $cbo_buyer_name;?>" />
                    <input type="hidden" name="cbo_currency" id="cbo_currency" value="<? echo $cbo_currency;?>" />
                    <input type="hidden" style="width:20px" name="cbo_supplier_name" id="cbo_supplier_name" value="<? echo $cbo_supplier_name;?>" />
                </th>
            </tr>
        </thead>
        <tr>
            <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
            <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:90px"></td>
            <td><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 );	?></td>
            <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px"></td>
            <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
            <td><? echo create_drop_down( "cbo_item", 120, $emblishment_name_array,"", 1, "-- Select Emb Name --", $selected, "",0 ); ?></td>
            <td align="center">
            <input type="button" name="button2" class="formbutton" value="Show" onClick="check()" style="width:60px;" />
            </td>
        </tr>
    </table>
    </form>
    <div id="search_div"></div>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="create_fnc_process_data")
{
	$data=explode('_',$data);
	$company_id=$data[0];
	$cbo_buyer_name=$data[1];
	$cbo_supplier_name=$data[2];
	$cbo_year_selection=$data[3];
	$cbo_currency=$data[4];

	$txt_style=$data[5];
	$txt_order_search=$data[6];
	$txt_job=$data[7];
	$cbo_item=$data[8];
	$ref_no=$data[9];
	$booking_no=$data[10];
	$cbo_level=$data[11];


	if ($txt_style!="") $style_cond=" and a.style_ref_no='$txt_style'"; else $style_cond=$txt_style;
	if ($txt_order_search!="") $order_cond=" and d.po_number='$txt_order_search'"; else $order_cond="";
	if ($ref_no!="") $ref_cond=" and d.grouping='$ref_no'"; else $ref_cond="";
	if ($txt_job!="") $job_cond=" and a.job_no_prefix_num='$txt_job'"; else $job_cond ="";
	if ($cbo_item!=0) $itemgroup_cond=" and c.emb_name=$cbo_item"; else $itemgroup_cond ="";
	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");


	?>
	</head>
	<body>
	<div style="width:1280px;">
	<?
	extract($_REQUEST);
	?>
	<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
	<input type="hidden" name="emb_id" id="emb_id" value="" />
	<input type="hidden" name="txt_job_id" id="txt_job_id" value="" />
	<input type="hidden" name="txt_selected_po" id="txt_selected_po" value="" />
	<table cellspacing="0" cellpadding="0"  rules="all" width="1280" class="rpt_table"  >
        <thead>
            <th width="20">SL</th>
            <th width="50">Buyer</th>
            <th width="55">Brand</th>
            <th width="55">Season</th>
            <th width="40">Season Year</th>
            <th width="40">Year</th>
            <th width="50">Job No</th>
            <th width="60">File No</th>
            <th width="60">M.Style / Int. Ref. No</th>
            <th width="80">Style No</th>
            <th width="80">Po No</th>
            <th width="80">Garmentes Item</th>
            <th width="70">Embl. Name</th>
            <th width="70">Embl. Type</th>
            <th width="70">Body Part</th>
            <th width="60">Req. Qty</th>
            <th width="40">UOM</th>
            <th width="60">CU WOQ</th>
            <th width="60">Bal WOQ</th>
            <th width="40">Exch. Rate</th>
            <th width="40">Rate</th>
            <th>Amount</th>
        </thead>
	</table>
	<div style="width:1280px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1260" class="rpt_table" id="tbl_list_search" >
        <?
		if($db_type==0) $year_field="YEAR(a.insert_date) as year";
        else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
        
        $condition= new condition();
        if(str_replace("'","",$company_id) !=''){
            $condition->company_name("=$company_id");
        }
        if(str_replace("'","",$cbo_buyer_name) !=''){
            $condition->buyer_name("=$cbo_buyer_name");
        }
        if(str_replace("'","",$txt_job) !=''){
            $condition->job_no_prefix_num("=$txt_job");
        }
        if(str_replace("'","",$txt_order_search)!='')
        {
            $condition->po_number("=$txt_order_search");
        }
		if(str_replace("'","",$ref_no)!='')
		{
			$condition->grouping("='$ref_no'");
		}

        $condition->init();
        $emblishment= new emblishment($condition);
        $req_qty_arr=$emblishment->getQtyArray_by_orderEmblishmentidAndGmtsitem();
		//echo "vvv";
		//print_r($req_qty_arr)."====";
        $req_amount_arr=$emblishment->getAmountArray_by_orderEmblishmentidAndGmtsitem();
		$wash= new wash($condition);
		$req_qty_arr_wash=$wash->getQtyArray_by_orderEmblishmentidAndGmtsitem();
        $req_amount_arr_wash=$wash->getAmountArray_by_orderEmblishmentidAndGmtsitem();
		//print_r($req_qty_arr_wash);

        $cu_booking_arr=array();
		$sql_cu_booking=sql_select("select c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d, wo_booking_mst b,wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and b.booking_no=c.booking_no and  d.id=c.po_break_down_id and a.company_name=$company_id and a.buyer_name=$cbo_buyer_name  and b.booking_type=6  and c.booking_type=6 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $job_cond $order_cond $ref_cond $style_cond group by a.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item");
        foreach($sql_cu_booking as $row_cu_booking){
            $cu_booking_arr[$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('po_break_down_id')]][$row_cu_booking[csf('gmt_item')]]['cu_wo_qnty']=$row_cu_booking[csf('cu_wo_qnty')];
            $cu_booking_arr[$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('po_break_down_id')]][$row_cu_booking[csf('gmt_item')]]['cu_amount']=$row_cu_booking[csf('cu_amount')];
        }
        unset($sql_cu_booking);
		
		$sql=sql_select("select b.approval_need, b.validate_page, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$company_id' and b.page_id=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
		$app_nessity=2; $validate_page=0; $allow_partial=2;
		foreach($sql as $row){
			$app_nessity=$row[csf('approval_need')];
			$validate_page=$row[csf('validate_page')];
			$allow_partial=$row[csf('allow_partial')];
		}
		
		$sourcingAppCond="";//Dont HIde Issue id ISD-21-04464
		if($app_nessity==1)
		{
			 if($allow_partial==1) $sourcingAppCond=" and b.sourcing_approved in (1,3)";
			 else $sourcingAppCond=" and b.sourcing_approved=1";
		}

         $sql="select a.job_no_prefix_num, $year_field,a.job_no, a.company_name, a.buyer_name, a.brand_id, a.season_buyer_wise, a.season_year, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as precost_emb_id, c.emb_name, c.emb_type,c.body_part_id, c.rate, d.id as po_id, d.po_number, d.file_no, d.grouping, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id,e.item_number_id, avg(e.requirment) AS cons
        from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_pre_cos_emb_co_avg_con_dtls e

        where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and c.id=e.pre_cost_emb_cost_dtls_id and d.id=e.po_break_down_id and a.company_name=$company_id and
        a.buyer_name=$cbo_buyer_name and (c.supplier_id = $cbo_supplier_name or c.supplier_id=0) and d.shiping_status!=3 and d.is_deleted=0 and d.status_active=1
        ".$buyer_cond_test." $itemgroup_cond $job_cond $order_cond $ref_cond $style_cond $sourcingAppCond
        group by
        a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.brand_id, a.season_buyer_wise, a.season_year, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.emb_name,c.emb_type,c.body_part_id, c.rate, a.insert_date, d.id, d.po_number, d.file_no, d.grouping, d.po_quantity, e.po_break_down_id, e.item_number_id order by d.id, c.id";

      // echo $sql; //die;
        $i=1; $req_qty=0; $req_amount=0; $rate=0; $total_req=0; $total_amount=0;
        $nameArray=sql_select( $sql );

        foreach ($nameArray as $selectResult)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$cbo_currency_job=$selectResult[csf('currency_id')];
			$exchange_rate=$selectResult[csf('exchange_rate')];
			$costing_per=$selectResult[csf('costing_per')];
			//echo $costing_per.'D';
			
			if($costing_per==2) $costing_per_dzn="Pcs";
			else $costing_per_dzn="Dzn";
			
			$emb_type="";
			if($selectResult[csf('emb_name')]==1) $emb_type=$emblishment_print_type[$selectResult[csf('emb_type')]];
			if($selectResult[csf('emb_name')]==2) $emb_type=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
			if($selectResult[csf('emb_name')]==3) $emb_type=$emblishment_wash_type[$selectResult[csf('emb_type')]];
			if($selectResult[csf('emb_name')]==4) $emb_type=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
			if($selectResult[csf('emb_name')]==5) $emb_type=$emblishment_gmts_type[$selectResult[csf('emb_type')]];
			if($selectResult[csf('emb_name')]==99) $emb_type=$emblishment_other_type_arr[$selectResult[csf('emb_type')]];
			
			if($cbo_currency==$cbo_currency_job) $exchange_rate=1;
			
			if($selectResult[csf('emb_name')]==3){
				$req_qty=$req_qty_arr_wash[$selectResult[csf('po_id')]][$selectResult[csf('precost_emb_id')]][$selectResult[csf('item_number_id')]];
				$req_amount=$req_amount_arr_wash[$selectResult[csf('po_id')]][$selectResult[csf('precost_emb_id')]][$selectResult[csf('item_number_id')]];
				//echo $req_qty."<br/>" ;
			}else{
				$req_qty=$req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('precost_emb_id')]][$selectResult[csf('item_number_id')]];
				$req_amount=$req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('precost_emb_id')]][$selectResult[csf('item_number_id')]];
				//echo $req_qty."<br/>" ;
			}
			$rate=$req_amount/$req_qty;

			$cu_wo_qnty=$cu_booking_arr[$selectResult[csf('precost_emb_id')]][$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]]['cu_wo_qnty'];
			$cu_wo_amnt=$cu_booking_arr[$selectResult[csf('precost_emb_id')]][$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]]['cu_amount'];
			$bal_woq=def_number_format($req_qty-$cu_wo_qnty,5,"");
			$bal_wom=def_number_format($req_amount-$cu_wo_amnt,5,"");

			$total_req+=$req_qnty;
			$total_req_amount+=$req_amount;
			$total_cu_amount+=$selectResult[csf('cu_amount')];
			$amount=def_number_format($rate*$bal_woq,4,"");
			//echo $selectResult[csf('emb_name')]."==".$bal_woq."==".$cu_wo_qnty."<br/>" ;
			if($bal_woq>0 && ($cu_wo_qnty=="" || $cu_wo_qnty==0))
			{
				?>
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);">
					<td width="20"><?=$i;?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
						<input type="hidden" name="precost_emb_id" id="precost_emb_id<?php echo $i ?>" value="<? echo $selectResult[csf('precost_emb_id')]; ?>"/>
						<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
						<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
						<input type="hidden" name="hiddemb_name" id="hiddemb_name<?php echo $i ?>" value="<? echo $selectResult[csf('emb_name')]; ?>"/>
					</td>
					<td width="50" style="word-break:break-all"><? echo $buyer_arr[$selectResult[csf('buyer_name')]];?></td>
					<td width="55" style="word-break:break-all"><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></td>
					<td width="55" style="word-break:break-all"><? echo $season_arr[$selectResult[csf('season_buyer_wise')]]; ?></td>
					<td width="40" style="word-break:break-all"><? if ($selectResult[csf('season_year')] != 0) echo $selectResult[csf('season_year')]; else echo ''; ?></td>
					<td width="40" style="word-break:break-all"><? echo $selectResult[csf('year')];?></td>
					<td width="50" style="word-break:break-all"><? echo $selectResult[csf('job_no_prefix_num')];?></td>
					<td width="60" style="word-break:break-all"><? echo $selectResult[csf('file_no')];?></td>
					<td width="60" style="word-break:break-all"><? echo $selectResult[csf('grouping')];?></td>
					<td width="80" style="word-break:break-all"><?=$selectResult[csf('style_ref_no')];?></td>
					<td width="80" style="word-break:break-all"><? echo $selectResult[csf('po_number')];?></td>
					<td width="80" style="word-break:break-all"><? echo $garments_item[$selectResult[csf('item_number_id')]];?></td>
					<td width="70" style="word-break:break-all"><? echo $emblishment_name_array[$selectResult[csf('emb_name')]];?></td>
					<td width="70" style="word-break:break-all" id="td_item_des<?=$i; ?>" style="word-break:break-all"><?=$emb_type; ?></td>
					<td width="70" style="word-break:break-all"><? echo $body_part[$selectResult[csf('body_part_id')]];?></td>
					<td width="60" style="word-break:break-all" align="right"><? echo number_format($req_qty,4); ?></td>
					<td width="40" style="word-break:break-all"><? echo $costing_per_dzn;//$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom]];?></td>
					<td width="60" style="word-break:break-all" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
					<td width="60" style="word-break:break-all" align="right"><? echo number_format($bal_woq,4); ?></td>
					<td width="40" style="word-break:break-all" align="right"><? echo number_format($exchange_rate,2); ?></td>
					<td width="40" style="word-break:break-all" align="right"><? echo number_format($rate,4); ?></td>
					<td style="word-break:break-all" align="right"><? echo number_format($amount,2); ?></td>
				</tr>
				<?
				$i++;
				$total_amount+=$amount;
			}
			elseif($bal_woq>=1 && $cu_wo_qnty>0)
			{
				?>
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);">
					<td width="20"><? echo $i;?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
						<input type="hidden" name="precost_emb_id" id="precost_emb_id<?php echo $i ?>" value="<? echo $selectResult[csf('precost_emb_id')]; ?>"/>
						<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
						<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
						<input type="hidden" name="hiddemb_name" id="hiddemb_name<?php echo $i ?>" value="<? echo $selectResult[csf('emb_name')]; ?>"/>
					</td>
					<td width="50" style="word-break:break-all"><? echo $buyer_arr [$selectResult[csf('buyer_name')]]; ?></td>
					<td width="55" style="word-break:break-all"><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></td>
					<td width="55" style="word-break:break-all"><? echo $season_arr[$selectResult[csf('season_buyer_wise')]]; ?></td>
					<td width="40" style="word-break:break-all"><? if ($selectResult[csf('season_year')] != 0) echo $selectResult[csf('season_year')]; else echo ''; ?></td>
					<td width="40" style="word-break:break-all"><? echo $selectResult[csf('year')];?></td>
					<td width="50" style="word-break:break-all"><? echo $selectResult[csf('job_no_prefix_num')];?></td>
					<td width="60" style="word-break:break-all"><? echo $selectResult[csf('file_no')];?></td>
					<td width="60" style="word-break:break-all"><? echo $selectResult[csf('grouping')];?></td>
					<td width="80" style="word-break:break-all"><? echo $selectResult[csf('style_ref_no')];?></td>
					<td width="80" style="word-break:break-all"><? echo $selectResult[csf('po_number')];?></td>
					<td width="80" style="word-break:break-all"><? echo $garments_item[$selectResult[csf('item_number_id')]];?></td>
					<td width="70" style="word-break:break-all"><? echo $emblishment_name_array[$selectResult[csf('emb_name')]];?></td>
					<td width="70" style="word-break:break-all" id="td_item_des<?=$i; ?>"><?=$emb_type; ?></td>
					<td width="70" style="word-break:break-all"><? echo $body_part[$selectResult[csf('body_part_id')]];?></td>
					<td width="60" style="word-break:break-all" align="right"><? echo number_format($req_qty,4); ?></td>
					<td width="40" style="word-break:break-all"><? echo $costing_per_dzn;//$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom]];?></td>
					<td width="60" style="word-break:break-all" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
					<td width="60" style="word-break:break-all" align="right"><? echo number_format($bal_woq,4); ?></td>
					<td width="40" style="word-break:break-all" align="right"><? echo number_format($exchange_rate,2); ?></td>
					<td width="40" style="word-break:break-all" align="right"><? echo number_format($rate,4); ?></td>
					<td style="word-break:break-all" align="right"><? echo number_format($amount,2); ?></td>
				</tr>
				<?
				$i++;
				$total_amount+=$amount;
			}
        }
        ?>
        </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1280" class="rpt_table">
        	<tfoot>
                <th width="20">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="55">&nbsp;</th>
                <th width="55">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="60" id="value_total_req"></th>
                <th width="40"><input type="hidden" style="width:30px"  id="txt_tot_req_amount" value="<? echo number_format($total_req_amount,2); ?>" /></th>
                <th width="60"><input type="hidden" style="width:50px" id="txt_tot_cu_amount" value="<? echo number_format($total_cu_amount,2); ?>" /></th>
                <th width="60">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th id="value_total_amount"><? echo number_format($total_amount,2); ?></th>
            </tfoot>
        </table>

	<table width="790" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                    	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
	</table>
	<script>
		var tableFilters = {
			col_operation: {
				id: ["value_total_amount"],
				col: [21],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		setFilterGrid('tbl_list_search',-1,tableFilters)
	</script>
	</div>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="generate_fabric_booking")
{
	extract($_REQUEST);
	if($garments_nature==0) $garment_nature_cond="";
	else $garment_nature_cond=" and a.garments_nature=$garments_nature";
	
	$param=implode(",",array_unique(explode(",",str_replace("'","",$param))));
	$data=implode(",",array_unique(explode(",",str_replace("'","",$data))));
	$pre_cost_id=implode(",",array_unique(explode(",",str_replace("'","",$pre_cost_id))));
	$sql_vari_lib="select item_category_id,variable_list,excut_source  from variable_order_tracking where company_name=".$cbo_company_name." and item_category_id=12  and variable_list=72 and status_active=1"; 
	$result_vari_lib=sql_select($sql_vari_lib);
	$source_from=1;//$woven_category_id=0;
	foreach($result_vari_lib as $row)
	{
		$source_from=$row[csf('excut_source')];
	}
	
	$condition= new condition();
	if(str_replace("'","",$data) !=''){
		$condition->po_id("in($data)");
	}
	$condition->init();
	//$costPerArr=$condition->getCostingPerArr();

	$emblishment= new emblishment($condition);
    $req_qty_arr=$emblishment->getQtyArray_by_orderEmblishmentidAndGmtsitem();
	if($source_from==2) //Sourcing Budget
	{
    	$req_amount_arr=$emblishment->getAmountArray_by_orderEmblishmentidAndGmtsitemSourcing();
	}
	else
	{
		$req_amount_arr=$emblishment->getAmountArray_by_orderEmblishmentidAndGmtsitem();
	}

	$wash= new wash($condition);
	$req_qty_arr_wash=$wash->getQtyArray_by_orderEmblishmentidAndGmtsitem();
	if($source_from==2) //Sourcing Budget
	{
    	$req_amount_arr_wash=$wash->getAmountArray_by_orderEmblishmentidAndGmtsitemSourcing();
	}
	else
	{
		$req_amount_arr_wash=$wash->getAmountArray_by_orderEmblishmentidAndGmtsitem();
	}
	
	$cu_booking_arr=array();

	$sql_cu_booking=sql_select("SELECT c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item, d.requirment as cu_wo_qnty, d.amount as cu_amount,d.item_color,d.gmts_sizes from wo_po_details_master a, wo_emb_book_con_dtls d , wo_booking_dtls c where a.job_no=d.job_no and c.booking_no=d.booking_no and d.po_break_down_id=c.po_break_down_id and a.job_no=c.job_no and d.wo_booking_dtls_id=c.id   and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_type=6 and d.requirment<>0 and c.po_break_down_id in($data) and  c.pre_cost_fabric_cost_dtls_id in ($pre_cost_id)  and d.color_size_table_id>0");

	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('gmt_item')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]] += $row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('gmt_item')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]] += $row_cu_booking[csf('cu_amount')];
		$cu_booking_colorarr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('gmt_item')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]][$row_cu_booking[csf('item_color')]] += $row_cu_booking[csf('cu_wo_qnty')];
	}
	unset($sql_cu_booking);
	/*echo '<pre>';
	print_r($cu_booking_arr); die;*/

	$sql="SELECT a.job_no_prefix_num, a.order_uom, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as bom_emb_dtls_id, c.emb_name, c.emb_type, c.body_part_id, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, e.item_number_id, avg(e.requirment) as cons from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and c.id=e.pre_cost_emb_cost_dtls_id and d.id=e.po_break_down_id and a.company_name=$cbo_company_name $garment_nature_cond and e.po_break_down_id in($data) and c.id in ($pre_cost_id) and d.is_deleted=0 and d.status_active=1 group by a.job_no_prefix_num, a.order_uom, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.emb_name, c.emb_type, c.body_part_id, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, e.item_number_id order by d.id, c.id";
	//e.id in($param) and e.color_number_id
	//echo $sql; die;
	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );

	foreach ($nameArray as $selectResult)
	{
		$cbo_currency_job=$selectResult[csf('currency_id')];
		$exchange_rate=$selectResult[csf('exchange_rate')];
		$bom_id=$selectResult[csf('bom_emb_dtls_id')];
		$jobNo=$selectResult[csf('job_no')];
		$po_id=$selectResult[csf('po_id')];
		$job_uom_id_arr[$selectResult[csf('job_no')]]=$selectResult[csf('order_uom')];
		if($cbo_currency==$cbo_currency_job){
			$exchange_rate=1;
		}
		if($selectResult[csf('emb_name')]==3)
		{
			$req_qnty_cons_uom=$req_qty_arr_wash[$po_id][$bom_id][$selectResult[csf('item_number_id')]];
			$req_amount_cons_uom=$req_amount_arr_wash[$po_id][$bom_id][$selectResult[csf('item_number_id')]];
		}else{
			$req_qnty_cons_uom=$req_qty_arr[$po_id][$bom_id][$selectResult[csf('item_number_id')]];
			$req_amount_cons_uom=$req_amount_arr[$po_id][$bom_id][$selectResult[csf('item_number_id')]];
		}
		$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

		$cu_woq=$cu_booking_arr[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['cu_woq'][$po_id];
		$cu_amount=$cu_booking_arr[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['cu_amount'][$po_id];

		//$bal_woq=def_number_format($req_qnty_cons_uom-$cu_woq,5,"");
		//$amount=def_number_format($rate_cons_uom*$bal_woq,5,"");
		$bal_woq=$req_qnty_cons_uom-$cu_woq;
		$amount=$rate_cons_uom*$bal_woq;

		if($selectResult[csf('emb_name')]==1) $emb_type_name=$emblishment_print_type[$selectResult[csf('emb_type')]];
		else if($selectResult[csf('emb_name')]==2) $emb_type_name=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
		else if($selectResult[csf('emb_name')]==3) $emb_type_name=$emblishment_wash_type[$selectResult[csf('emb_type')]];
		else if($selectResult[csf('emb_name')]==4) $emb_type_name=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
		else if($selectResult[csf('emb_name')]==5) $emb_type_name=$emblishment_gmts_type[$selectResult[csf('emb_type')]];
		else if($selectResult[csf('emb_name')]==99) $emb_type_name=$emblishment_other_type_arr[$selectResult[csf('emb_type')]];

		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['job_no'][$po_id]=$jobNo;
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['costing_per'][$po_id]=$selectResult[csf('costing_per')];
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['po_id'][$po_id]=$po_id;
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['po_number'][$po_id]=$selectResult[csf('po_number')];

		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['item_number_id'][$po_id]=$selectResult[csf('item_number_id')];

		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['country'][$po_id]=$selectResult[csf('country')];
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['body_part_id'][$po_id]=$selectResult[csf('body_part_id')];
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['body_part'][$po_id]=$body_part[$selectResult[csf('body_part_id')]];
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['emb_type'][$po_id]=$selectResult[csf('emb_type')];
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['emb_type_name'][$po_id]=$emb_type_name;
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['emb_name'][$po_id]=$selectResult[csf('emb_name')];

		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['emb_name_name'][$po_id]=$emblishment_name_array[$selectResult[csf('emb_name')]];
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['pre_cost_emb_cost_dtls_id'][$po_id]=$bom_id;
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['req_qnty'][$po_id]=$req_qnty_cons_uom;
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['req_amount'][$po_id]=$req_amount_cons_uom;
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['cu_woq'][$po_id]=$cu_woq;
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['cu_amount'][$po_id]=$cu_amount;
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['bal_woq'][$po_id]=$bal_woq;
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['exchange_rate'][$po_id]=$exchange_rate;
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['rate'][$po_id]=$rate_cons_uom;
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['amount'][$po_id]=$amount;
		$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['txt_delivery_date'][$po_id]=$txt_delivery_date;
		//$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['color_req_woq'][$po_id][$selectResult[csf('color_number_id')]]=$txt_delivery_date;
		//$job_and_trimgroup_level[$jobNo][$bom_id][$selectResult[csf('item_number_id')]]['costing_per'][$po_id]=$costPerArr[$jobNo];
	}
	// print_r($job_and_trimgroup_level);
	?>
	<input type="hidden" id="strdata" value='<?=json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="80">Job No</th>
            <th width="100">Ord. No</th>
            <th width="100">Garmentes Item</th>
            <th width="100">Emb. Name</th>
            <th width="150">Body Part</th>
            <th width="150">Emb. Type</th>
            <th width="70">Req. Qnty</th>
            <th width="50">UOM</th>
            <th width="80">CU WOQ</th>
            <th width="80">Bal WOQ</th>
            <th width="100">Sensitivity</th>
            <th width="80">WOQ</th>
            <th width="55">Exch.Rate</th>
            <th width="80">Rate</th>
            <th width="80">Amount</th>
            <th>Delv. Date</th>
        </thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" id="tbl_list_search" >
        <tbody>
			<?
            if($cbo_level==1)
			{
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
					$cbo_currency_job=$selectResult[csf('currency_id')];
					$exchange_rate=$selectResult[csf('exchange_rate')];
					$costing_per=$selectResult[csf('costing_per')];
					$bom_id=$selectResult[csf('bom_emb_dtls_id')];
					
					/*if($costing_per==1) $costing_per_dzn="Dzn";
					else if($costing_per==2) $costing_per_dzn="Pcs";
					else if($costing_per==3) $costing_per_dzn="Dzn";
					else if($costing_per==4) $costing_per_dzn="Dzn";
					else if($costing_per==5) $costing_per_dzn="Dzn";*/
					$uom_id=0;
					$costing_per_dzn='';
					if($costing_per==2)
					{
						$costing_per_dzn='Pcs';
						$uom_id=1;
					}
					else{
						$costing_per_dzn='Dzn';
						$uom_id=2;
					}
					
					if($cbo_currency == $cbo_currency_job){
						$exchange_rate=1;
					}
				//$uom_id=$job_uom_id_arr[$selectResult[csf('job_no')]];
				
					if($selectResult[csf('emb_name')]==3)
					{
						$req_qnty_cons_uom=$req_qty_arr_wash[$selectResult[csf('po_id')]][$bom_id][$selectResult[csf('item_number_id')]];
						$req_amount_cons_uom=$req_amount_arr_wash[$selectResult[csf('po_id')]][$bom_id][$selectResult[csf('item_number_id')]];
					}else{
						$req_qnty_cons_uom=$req_qty_arr[$selectResult[csf('po_id')]][$bom_id][$selectResult[csf('item_number_id')]];
						$req_amount_cons_uom=$req_amount_arr[$selectResult[csf('po_id')]][$bom_id][$selectResult[csf('item_number_id')]];
					}
					$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;
			
					$cu_woq=$cu_booking_arr[$selectResult[csf('job_no')]][$bom_id][$selectResult[csf('item_number_id')]]['cu_woq'][$selectResult[csf('po_id')]];
					$cu_amount=$cu_booking_arr[$selectResult[csf('job_no')]][$bom_id][$selectResult[csf('item_number_id')]]['cu_amount'][$selectResult[csf('po_id')]];
				
					//$bal_woq=def_number_format($req_qnty_cons_uom-$cu_woq,5,"");
					//$amount=def_number_format($rate_cons_uom*$bal_woq,5,"");
					$bal_woq=$req_qnty_cons_uom-$cu_woq;
					$amount=$rate_cons_uom*$bal_woq;
				
					if($selectResult[csf('emb_name')]==1) $emb_type_name=$emblishment_print_type[$selectResult[csf('emb_type')]];
					else if($selectResult[csf('emb_name')]==2) $emb_type_name=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
					else if($selectResult[csf('emb_name')]==3) $emb_type_name=$emblishment_wash_type[$selectResult[csf('emb_type')]];
					else if($selectResult[csf('emb_name')]==4) $emb_type_name=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
					else if($selectResult[csf('emb_name')]==5) $emb_type_name=$emblishment_gmts_type[$selectResult[csf('emb_type')]];
					else if($selectResult[csf('emb_name')]==99) $emb_type_name=$emblishment_other_type_arr[$selectResult[csf('emb_type')]];
					?>
					<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
                        <td width="40" align="center"><?=$i; ?></td>
                        <td width="80">
							<?=$jobNo;?>
                            <input type="hidden" id="txtjob_<?=$i; ?>" value="<?=$selectResult[csf('job_no')]; ?>" style="width:30px" class="text_boxes" readonly/>
                        </td>
                        <td width="100" style="word-break:break-all"><?=$selectResult[csf('po_number')]; ?>
                            <input type="hidden" id="txtbookingid_<?=$i; ?>" value="" readonly/>
                            <input type="hidden" id="txtpoid_<?=$i; ?>" value="<?=$selectResult[csf('po_id')]; ?>" readonly/>
                            <input type="hidden" id="txtcountry_<?=$i; ?>" value="<?=$selectResult[csf('country')]; ?>" readonly />
                        </td>
                        <td width="100" style="word-break:break-all">
							<?=$garments_item[$selectResult[csf('item_number_id')]];?>
                            <input type="hidden" id="txtgmtitemid_<?=$i; ?>" value="<?=$selectResult[csf('item_number_id')]; ?>" readonly/>
                        </td>
                        <td width="100" style="word-break:break-all">
							<?=$emblishment_name_array[$selectResult[csf('emb_name')]];?>
                            <input type="hidden" id="txtembcostid_<?=$i; ?>" value="<?=$bom_id; ?>" readonly/>
                            <input type="hidden" id="emb_name_<?=$i; ?>" value="<?=$selectResult[csf('emb_name')]; ?>" readonly/>
                        </td>
                        <td width="150" style="word-break:break-all">
							<?=$body_part[$selectResult[csf('body_part_id')]]; ?>
                            <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="body_part_id_<?=$i;?>" value="<?=$selectResult[csf('body_part_id')]; ?>"  />
                        </td>
                        <td width="150" style="word-break:break-all">
							<?=$emb_type_name; ?>
                            <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="emb_type_<?=$i; ?>"  value="<?=$selectResult[csf('emb_type')]; ?>"  />
                        </td>
                        <td width="70" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtreqqnty_<?=$i; ?>" value="<?=number_format($req_qnty_cons_uom,4,'.',''); ?>" readonly />
                            <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtreqamount_<?=$i; ?>" value="<?=number_format($req_amount_cons_uom,4,'.',''); ?>" readonly />
                        </td>
                        <td width="50"><?=$unit_of_measurement[$uom_id]; ?><input type="hidden" id="txtuom_<?=$i; ?>" value="<?= $uom_id ?>" readonly /></td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuwoq_<?=$i; ?>" value="<?=number_format($selectResult[csf('cu_woq')],4,'.',''); ?>"  readonly  />
                            <input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuamount_<?=$i; ?>" value="<?=number_format($selectResult[csf('cu_amount')],4,'.',''); ?>" readonly  />
                        </td>
                        <td width="80" align="right">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalwoq_<?=$i; ?>" value="<?=number_format($bal_woq,4,'.',''); ?>" readonly />
                        </td>
                        <td width="100" align="right"><?=create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "1", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,3,4" ); ?></td>
                        <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<?=$i; ?>" value="<?=number_format($bal_woq,4,'.',''); ?>" onClick="open_consumption_popup('requires/print_booking_multijob_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<?=$i; ?>',<?=$i; ?>);" readonly /></td>
                        <td width="55" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtexchrate_<?=$i; ?>" value="<?=$exchange_rate; ?>" readonly /></td>
                        <td width="80" align="right">
							<?
                            $ratetexcolor="#000000";
                            $decimal=explode(".",$rate_cons_uom);
                            if(strlen($decimal[1]>6)) $ratetexcolor="#F00";
                            ?>
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   text-align:right; color:<?=$ratetexcolor; ?>; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i; ?>" value="<?=number_format($rate_cons_uom,4,'.',''); ?>" onChange="calculate_amount(<?=$i; ?>);" readonly />
                            <input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_precost_<?=$i; ?>" value="<?=$rate_cons_uom; ?>" readonly />
                        </td>
                        <td width="80" align="right"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i; ?>" value="<?=number_format($amount,4,'.',''); ?>" readonly /></td>
                        <td align="right">
                            <input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtddate_<?=$i; ?>" class="datepicker" value="<?=$txt_delivery_date; ?>" readonly />
                            <input type="hidden" id="consbreckdown_<?=$i; ?>" value=""/>
                            <input type="hidden" id="jsondata_<?=$i; ?>" value=""/>
                        </td>
					</tr>
					<?
					$i++;
				}
            }
            else if($cbo_level==2)
			{
				$i=1;
				foreach ($job_and_trimgroup_level as $job_no)
				{
					foreach ($job_no as $wo_pre_cost_trim_cost_dtlsArr)
					{
						foreach ($wo_pre_cost_trim_cost_dtlsArr as $wo_pre_cost_trim_cost_dtls)
						{
							$job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
							$uom_name=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
							$costing_per=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['costing_per']));
							$po_number=implode(",",$wo_pre_cost_trim_cost_dtls['po_number']);
							$po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
							$item_number_id=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['item_number_id']))));
						
							$country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
							$body_part_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['body_part_id']));
							$body_part=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['body_part']));
							$emb_type=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['emb_type']));
							$emb_type_name=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['emb_type_name']));
						
							$pre_cost_emb_cost_dtls_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['pre_cost_emb_cost_dtls_id']));
							$emb_name = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['emb_name']));
							$emb_name_name = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['emb_name_name']));
							$uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
						
							$req_qnty_cons_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
							$rate_cons_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount'])/array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
							$req_amount_cons_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount']);
						
							$bal_woq=array_sum($wo_pre_cost_trim_cost_dtls['bal_woq']);
							$amount=array_sum($wo_pre_cost_trim_cost_dtls['amount']);
						
							$cu_woq=array_sum($wo_pre_cost_trim_cost_dtls['cu_woq']);
							$cu_amount=array_sum($wo_pre_cost_trim_cost_dtls['cu_amount']);
							//$reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$job_no];
							$uom_id=0;
							$costing_per_dzn='';
							if($costing_per==2)
							{
								$costing_per_dzn='Pcs';
								$uom_id=1;
							}
							else{
								$costing_per_dzn='Dzn';
								$uom_id=2;
							}
							//$uom_id=$job_uom_id_arr[$job_no];
							?>
							<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
                                <td width="40" align="center"><?=$i; ?></td>
                                <td width="80"><?=$job_no; ?> <input type="hidden" id="txtjob_<?=$i; ?>" value="<?=$job_no; ?>" style="width:30px" class="text_boxes" readonly/></td>
                                <td width="100" style="word-wrap:break-word;word-break: break-all">
                                    <?=$po_number; ?>
                                    <input type="hidden" id="txtbookingid_<?=$i; ?>" value="" readonly/>
                                    <input type="hidden" id="txtpoid_<?=$i;?>" value="<?=$po_id; ?>" readonly/>
                                    <input type="hidden" id="txtcountry_<?=$i; ?>" value="<?=$country; ?>" readonly />
                                </td>
                                <td width="100"><?=$garments_item[$item_number_id]; ?><input type="hidden" id="txtgmtitemid_<?=$i; ?>" value="<?=$item_number_id; ?>" readonly/></td>
                                <td width="100"><?=$emb_name_name; ?>
                                    <input type="hidden" id="txtembcostid_<?=$i; ?>" value="<?=$pre_cost_emb_cost_dtls_id; ?>" readonly/>
                                    <input type="hidden" id="emb_name_<?=$i; ?>" value="<?=$emb_name; ?>" readonly/>
                                </td>
                                <td width="150">
                                    <?=$body_part; ?>
                                    <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<?=$bgcolor; ?>" id="body_part_id_<?=$i; ?>" value="<?=$body_part_id; ?>" />
                                </td>
                                <td width="150">
                                    <?=$emb_type_name;?>
                                    <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="emb_type_<?=$i; ?>" value="<?=$emb_type; ?>" />
                                </td>
                                <td width="70" align="right">
                                    <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtreqqnty_<?=$i; ?>" value="<?=number_format($req_qnty_cons_uom,4,'.',''); ?>" readonly />
                                    <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtreqamount_<?=$i; ?>" value="<?=number_format($req_amount_cons_uom,4,'.',''); ?>" readonly />
                                </td>
                                <td width="50">
                                    <?=$unit_of_measurement[$uom_id]; ?>
                                    <input type="hidden" id="txtuom_<?=$i; ?>" value="<?=$uom_id; ?>" readonly />
                                </td>
                                <td width="80" align="right">
                                    <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuwoq_<?=$i; ?>" value="<?=number_format($cu_woq,4,'.',''); ?>" readonly />
                                    <input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuamount_<?=$i; ?>" value="<?=number_format($cu_amount,4,'.',''); ?>" readonly />
                                </td>
                                <td width="80" align="right">
                                    <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalwoq_<?=$i; ?>" value="<?=number_format($bal_woq,4,'.',''); ?>" readonly />
                                </td>
                                <td width="100" align="right"><?=create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "1", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,3,4" ); ?></td>
                                <td width="80" align="right">
                                    <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtwoq_<?=$i; ?>" value="<?=number_format($bal_woq,4,'.',''); ?>" onClick="open_consumption_popup('requires/print_booking_multijob_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<?=$i; ?>',<?=$i; ?>);" readonly/>
                                </td>
                                <td width="55" align="right">
                                    <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtexchrate_<?=$i; ?>" value="<?=$exchange_rate; ?>" readonly />
                                </td>
                                <td width="80" align="right">
                                    <?
                                    $ratetexcolor="#000000";
                                    $decimal=explode(".",$rate_cons_uom);
                                    if(strlen($decimal[1])>6) $ratetexcolor="#F00";
                                    ?>
                                    <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; color:<?=$ratetexcolor; ?>; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i; ?>" value="<?=number_format($rate_cons_uom,4,'.',''); ?>" onChange="calculate_amount(<?=$i; ?>);" readonly />
                                    <input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_precost_<?=$i; ?>" value="<?=$rate_cons_uom; ?>" readonly />
                                </td>
                                <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i; ?>" value="<?=number_format($amount,4,'.',''); ?>" readonly /></td>
                                <td align="right">
                                    <input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtddate_<?=$i; ?>" class="datepicker" value="<?=$txt_delivery_date; ?>" readonly />
                                    <input type="hidden" id="consbreckdown_<?=$i; ?>" value=""/>
                                    <input type="hidden" id="jsondata_<?=$i; ?>" value=""/>
                                </td>
							</tr>
							<?
							$i++;
						}
					}
				}
            }
            ?>
        </tbody>
	</table>
	<table width="1500" class="rpt_table" border="0" rules="all">
        <tfoot>
            <tr>
                <th width="40">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="70"><?=$tot_req_qty; ?></th>
                <th width="50">&nbsp;</th>
                <th width="80"><?=$tot_cu_woq; ?></th>
                <th width="80"><?=$tot_bal_woq; ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="55">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><input type="hidden" id="tot_amount" value="<?=$total_amount; ?>" style="width:80px" readonly /></th>
                <th><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
            </tr>
        </tfoot>
	</table>
    <table width="1100" colspan="14" cellspacing="0" class="" border="0">
        <tr>
            <td align="center"class="button_container">
            <?=load_submit_buttons( $permission, "fnc_trims_booking_dtls", 0,0,"reset_form('','booking_list_view','','','')",2); ?>
            </td>
        </tr>
    </table>
	<?
	exit();
}

if ($action == "consumption_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from lib_size", "id", "size_name");
	?>
	<script>
		var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size", "size_name"  ), 0, -1); ?>];
		var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color", "color_name"  ), 0, -1); ?>];
		function poportionate_qty(qty)
		{
			/*var txtwoq=document.getElementById('txtwoq').value;
			var txtwoq_qty=document.getElementById('txtwoq_qty').value*1;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			for(var i=1; i<=rowCount; i++){
				var poreqqty=$('#poreqqty_'+i).val();
				var txtwoq_cal =number_format_common((txtwoq_qty/txtwoq) * (poreqqty),5,0);
				//alert(txtwoq_cal);
				$('#qty_'+i).val(txtwoq_cal);
				calculate_requirement(i)
			}*/
			var po_qty=document.getElementById('po_qty').value*1;;
			//var pcs_sum=document.getElementById('pcs_sum').value*1;
			/*if(pcs_sum == '')
			{
				pcs_sum=qty;
			}*/
			
			var txtwoq_qty=document.getElementById('txtwoq_qty').value*1;;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			var pcs_sum=0;
			for(var i=1; i<=rowCount; i++){
				var qty = $('#reqqty_'+i).val()*1;
				pcs_sum =pcs_sum+qty;
			}
			console.log("sum:"+pcs_sum);
			var txtwoq_cal=0;
			for(var i=1; i<=rowCount; i++){
				var pcs=$('#pcsset_'+i).val()*1;
				var reqqty=$('#reqqty_'+i).val()*1;
				console.log('req'+reqqty+'txtwoq'+txtwoq_qty+'sum'+pcs_sum);
				txtwoq_cal =number_format_common((reqqty/pcs_sum) * (txtwoq_qty),5,0);
				
				if(txtwoq_cal*1 > number_format_common(reqqty,0,0)*1)
				{
					if(number_format_common(txtwoq_cal,0,0)*1>number_format_common(reqqty,0,0)*1)
					{
						console.log(txtwoq_cal+'=='+number_format_common(reqqty,5,0) + ' test ');
						$('#qty_'+i).val(0);
						calculate_requirement(i)
					}
					else
					{
						console.log(txtwoq_cal+'=='+number_format_common(reqqty,5,0));
						$('#qty_'+i).val(reqqty);
						calculate_requirement(i)
					}
					
				}
				else
				{
					console.log(txtwoq_cal+'=='+number_format_common(reqqty,5,0));
					$('#qty_'+i).val(txtwoq_cal);
					calculate_requirement(i)
				}	
				/*$('#qty_'+i).val(txtwoq_cal);
				calculate_requirement(i)	*/		
			}
			set_sum_value( 'qty_sum', 'qty_')
			var j=i-1;
			var qty_sum=document.getElementById('qty_sum').value*1;
			/*if(qty_sum >txtwoq_qty ){
				$('#qty_'+j).val(number_format_common(txtwoq_cal*1-(qty_sum-txtwoq_qty),5,0))
			}
			else if(qty_sum < txtwoq_qty ){
				$('#qty_'+j).val(number_format_common((txtwoq_cal*1) +(txtwoq_qty - qty_sum),5,0))
			}
			else{
				$('#qty_'+j).val(number_format_common(txtwoq_cal,5,0));
			}*/
			//set_sum_value( 'qty_sum', 'qty_');
			calculate_requirement(j)
		}

		function calculate_requirement(i){
			var process_loss_method_id=document.getElementById('process_loss_method_id').value;
			var cons=(document.getElementById('qty_'+i).value)*1;
			var processloss=(document.getElementById('excess_'+i).value)*1;
			var WastageQty='';
			if(process_loss_method_id==1){
				WastageQty=cons+cons*(processloss/100);
			}
			else if(process_loss_method_id==2){
				var devided_val = 1-(processloss/100);
				var WastageQty=parseFloat(cons/devided_val);
			}
			else{
				WastageQty=0;
			}
			var reqqty=$('#reqqty_'+i).val()*1;
			var qty=$('#qty_'+i).val()*1;

			// if(qty>reqqty)
			// {
			// 	$('#qty_'+i).val(0);
			// }
			if(number_format_common(qty,0,0)*1>number_format_common(reqqty,0,0)*1)
			{
				console.log(txtwoq_cal+'=='+number_format_common(reqqty,5,0) + ' cal ');
				$('#qty_'+i).val(0);
				
			}
			WastageQty= number_format_common( WastageQty, 5, 0) ;
			document.getElementById('woqny_'+i).value= WastageQty;
			set_sum_value( 'woqty_sum', 'woqny_' )
			calculate_amount(i);
		}

		function set_sum_value(des_fil_id,field_id)
		{
			if(des_fil_id=='qty_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='excess_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='woqty_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='amount_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='pcs_sum') var ddd={dec_type:6,comma:0};
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}

		function copy_value(value,field_id,i)
		{
			var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
			var pocolorid=document.getElementById('pocolorid_'+i).value;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			var copy_basis=$('input[name="copy_basis"]:checked').val()

			for(var j=i; j<=rowCount; j++)
			{
				if(field_id=='des_'){
					if(copy_basis==0) document.getElementById(field_id+j).value=value;
					if(copy_basis==1)
					{
						if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
							document.getElementById(field_id+j).value=value;
						}
					}
					if(copy_basis==2){
						if( pocolorid==document.getElementById('pocolorid_'+j).value){
							document.getElementById(field_id+j).value=value;
						}
					}
				}
				if(field_id=='itemcolor_'){
					if(copy_basis==0){
						document.getElementById(field_id+j).value=value;
					}
					if(copy_basis==1){
						if( pocolorid==document.getElementById('pocolorid_'+j).value){
							document.getElementById(field_id+j).value=value;
						}
					}
					if(copy_basis==2){
						if( pocolorid==document.getElementById('pocolorid_'+j).value){
							document.getElementById(field_id+j).value=value;
						}
					}
				}

				if(field_id=='itemsizes_'){
					if(copy_basis==0){
						document.getElementById(field_id+j).value=value;
					}
					if(copy_basis==1)
					{
						if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
							document.getElementById(field_id+j).value=value;
						}
					}
					if(copy_basis==2)
					{
						if( pocolorid==document.getElementById('pocolorid_'+j).value){
							document.getElementById(field_id+j).value=value;
						}
					}
				}
				if(field_id=='qty_'){
					if(copy_basis==0){
						document.getElementById(field_id+j).value=value;
						calculate_requirement(j)
						set_sum_value( 'qty_sum', 'qty_'  );
					}
					if(copy_basis==1){
						if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_requirement(j)
							set_sum_value( 'qty_sum', 'qty_'  );
						}
					}
					if(copy_basis==2){
						if( pocolorid==document.getElementById('pocolorid_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_requirement(j)
							set_sum_value( 'qty_sum', 'qty_'  );
						}
					}
				}
				if(field_id=='excess_'){
					if(copy_basis==0){
						document.getElementById(field_id+j).value=value;
						calculate_requirement(j)
					}
					if(copy_basis==1){
						if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_requirement(j)
						}
					}
					if(copy_basis==2){
						if( pocolorid==document.getElementById('pocolorid_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_requirement(j)
						}
					}
				}
				if(field_id=='rate_'){
					if(copy_basis==0){
						document.getElementById(field_id+j).value=value;
						calculate_amount(j)
					}
					if(copy_basis==1){
						if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_amount(j)
						}
					}
					if(copy_basis==2){
						if( pocolorid==document.getElementById('pocolorid_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_amount(j)
						}
					}
				}
			}
		}

		function calculate_amount(i){
			var rate=(document.getElementById('rate_'+i).value)*1;
			var woqny=(document.getElementById('woqny_'+i).value)*1;
			var amount=number_format_common((rate*woqny),5,0);
			document.getElementById('amount_'+i).value=amount;
			set_sum_value( 'amount_sum', 'amount_' );
			calculate_avg_rate()
		}

		function calculate_avg_rate(){
			var woqty_sum=document.getElementById('woqty_sum').value;
			var amount_sum=document.getElementById('amount_sum').value;
			//var avg_rate=number_format_common((amount_sum/woqty_sum),5,0);
				var avg_rate=number_format((amount_sum/woqty_sum),5,'.','');
			document.getElementById('rate_sum').value=avg_rate;
		}

		function js_set_value(){
			var reg=/[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;
			var row_num=$('#tbl_consmption_cost tbody tr').length;
			var cons_breck_down="";
			for(var i=1; i<=row_num; i++){
				var txtdescription=$('#des_'+i).val();
				//alert(txtdescription.match(reg))
				if(txtdescription.match(reg)){
					alert("Your Description Can not Have any thing other than a-zA-Z0-9!@#$%^,;.:<>{}?+|[]/- ");
					//release_freezing();
					$('#des_'+i).css('background-color', 'red');
					return;
				}
				var pocolorid=$('#pocolorid_'+i).val()
				if(pocolorid=='') pocolorid=0;

				var gmtssizesid=$('#gmtssizesid_'+i).val()
				if(gmtssizesid=='') gmtssizesid=0;

				var des=trim($('#des_'+i).val())
				if(des=='') des=0;

				var itemcolor=$('#itemcolor_'+i).val()
				if(itemcolor=='') itemcolor=0;

				var itemsizes=$('#itemsizes_'+i).val()
				if(itemsizes=='') itemsizes=0;

				var qty=$('#qty_'+i).val()
				if(qty=='') qty=0;

				var excess=$('#excess_'+i).val()
				if(excess=='') excess=0;

				var woqny=$('#woqny_'+i).val()
				if(woqny=='') woqny=0;

				var rate=$('#rate_'+i).val()
				if(rate=='') rate=0;

				var amount=$('#amount_'+i).val()
				if(amount=='') amount=0;

				var pcs=$('#pcs_'+i).val()
				if(pcs=='') pcs=0;

				var colorsizetableid=$('#colorsizetableid_'+i).val()
				if(colorsizetableid=='')colorsizetableid=0;

				var updateid=$('#updateid_'+i).val()
				if(updateid=='') updateid=0;

				var reqqty=$('#reqqty_'+i).val()
				if(reqqty=='') reqqty=0;

				var poarticle=$('#poarticle_'+i).val()
				if(poarticle=='') poarticle='no article';

				var pocolorqty=$('#pocolorqty_'+i).val()
				if(pocolorqty=='') pocolorqty=0;

				if(cons_breck_down==""){
					cons_breck_down+=pocolorid+'_'+gmtssizesid+'_'+des+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty+'_'+poarticle+'_'+pocolorqty;
				}
				else{
					cons_breck_down+="__"+pocolorid+'_'+gmtssizesid+'_'+des+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty+'_'+poarticle+'_'+pocolorqty;
				}
				//alert(cons_breck_down);
			}
			document.getElementById('cons_breck_down').value=cons_breck_down;
			var wo_qty=$('#txtwoq_qty').val()*1;
			var txt_req_quantity=$('#txt_req_quantity').val()*1;
			var txtcuwoq=$('#txtcuwoq').val()*1;

		//	alert(txt_req_quantity+'='+wo_qty+'='+txtcuwoq);
			if(txt_req_quantity<wo_qty+txtcuwoq)
			{
				
				alert("Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.");
				return;
				
			}
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<?
        extract($_REQUEST);
        if($txt_job_no==""){
			$txt_job_no_cond="";
			$txt_job_no_cond1="";
        }
        else{
			$txt_job_no_cond ="and a.job_no='$txt_job_no'";
			$txt_job_no_cond1 ="and job_no='$txt_job_no'";
        }
        if($txt_country==""){
			$txt_country_cond="";
        }
        else{
			$txt_country_cond ="and c.country_id in ($txt_country)";
        }
        $process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
        $tot_po_qty=0;
        $sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id) and c.item_number_id=$txtgmtitemid  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
        foreach($sql_po_qty as$sql_po_qty_row){
			$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
			$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
        }
        ?>
        <div align="center" style="width:1150px;" >
            <fieldset>
                <form id="consumptionform_1" autocomplete="off">
                    <table width="1150" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                        <thead>
                        	<tr>
                                <th colspan="14"  style="color:#FF0000">
                                	<p id="td_sync_msg"></p>
                                	<p id="td_po_qnt_chagne_msg"></p>
                                </th>
                                
                            </tr>
                           
                            <tr>
                                <th colspan="14">
                                    <input type="hidden" id="cons_breck_down" name="cons_breck_down" value="" />
                                    <input type="hidden" id="txtwoq" value="<? echo $txt_req_quantity;?>"/>
                                    <input type="hidden" id="txt_req_quantity" value="<? echo $txt_req_quantity;?>"/>
                                    <input type="hidden" id="txtcuwoq" value="<? echo $txtcuwoq;?>"/>
                                    Wo Qty::<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value)" value="<? echo $txtwoq; ?>"/>
                                    <input type="radio" name="copy_basis" value="0" <? if(!$txt_update_dtls_id) { echo "checked";} ?>>Copy to All
                                    <input type="radio" name="copy_basis" value="1">Gmts Size Wise
                                    <input type="radio" name="copy_basis" value="2">Gmts Color Wise
                                    <input type="radio" name="copy_basis" value="10" <? if($txt_update_dtls_id) { echo "checked";} ?>>No Copy
                                    <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>"/>
                                    <input type="hidden" id="po_qty" name="po_qty" value="<? echo $tot_po_qty; ?>"/>
                                </th>
                            </tr>
                            <tr>
                                <th width="40">SL</th><th  width="100">Article No</th><th  width="100">Gmts. Color</th><th  width="70">Gmts. sizes</th><th  width="100">Description</th><th  width="100">Item Color</th><th width="80">Item Sizes</th><th width="70"> Wo Qty</th><th width="40">Excess %</th><th width="70">WO Qty.</th><th width="120">Rate</th><th width="100">Amount</th><th width="">RMG Qnty</th>

                            </tr>
                        </thead>
                        <tbody>
                        <?

                        $booking_data_arr=array();
						if($txt_update_dtls_id==""){
							$txt_update_dtls_id=0;
						}

                        $booking_data=sql_select("select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id  from wo_emb_book_con_dtls where wo_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
                        foreach($booking_data as $row){
							$booking_data_arr[$row[csf('color_size_table_id')]][id]=$row[csf('id')];
							$booking_data_arr[$row[csf('color_size_table_id')]][description]=$row[csf('description')];
							$booking_data_arr[$row[csf('color_size_table_id')]][item_color]=$row[csf('item_color')];
							$booking_data_arr[$row[csf('color_size_table_id')]][item_size]=$row[csf('item_size')];

							$booking_data_arr[$row[csf('color_size_table_id')]][cons]+=$row[csf('cons')];
							$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]=$row[csf('process_loss_percent')];
							$booking_data_arr[$row[csf('color_size_table_id')]][requirment]+=$row[csf('requirment')];
							$booking_data_arr[$row[csf('color_size_table_id')]][rate]=$row[csf('rate')];
							$booking_data_arr[$row[csf('color_size_table_id')]][amount]+=$row[csf('amount')];
                        }
                        if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3)
                        {
                        	if($txt_update_dtls_id == '')
	                        {
	                        	$previous_booking = sql_select("SELECT b.requirment, b.po_break_down_id, b.color_size_table_id, b.color_number_id, a.pre_cost_fabric_cost_dtls_id from wo_booking_dtls a join wo_emb_book_con_dtls b on a.id=b.wo_booking_dtls_id  where b.job_no='$txt_job_no'and b.po_break_down_id in ($txt_po_id) and a.pre_cost_fabric_cost_dtls_id=$txtembcostid and b.requirment<>0 and  a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0");
		                        foreach ($previous_booking as $row) {
		                        	$previous_booking_arr[$txtembcostid][$row[csf('color_number_id')]] += $row[csf('requirment')];
		                        	$previous_booking_po_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('po_break_down_id')]] += $row[csf('requirment')];
		                        }
	                        }
	                        else
	                        {
	                        	$previous_booking = sql_select("SELECT b.requirment, b.po_break_down_id, b.color_size_table_id, b.color_number_id, a.pre_cost_fabric_cost_dtls_id from wo_booking_dtls a join wo_emb_book_con_dtls b on a.id=b.wo_booking_dtls_id  where b.job_no='$txt_job_no'and b.po_break_down_id in ($txt_po_id) and a.pre_cost_fabric_cost_dtls_id=$txtembcostid and b.requirment<>0 and  a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.id not in ($txt_update_dtls_id)");
		                        foreach ($previous_booking as $row) {
		                        	$previous_booking_arr[$txtembcostid][$row[csf('color_number_id')]] += $row[csf('requirment')];
		                        	$previous_booking_po_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('po_break_down_id')]] += $row[csf('requirment')];
		                        }
	                        }
                        }
                        if($cbo_colorsizesensitive==4)
                        {
                        	if($txt_update_dtls_id == '')
	                        {
	                        	//echo "SELECT a.gmt_item, b.requirment, b.po_break_down_id, b.color_size_table_id, b.color_number_id, a.pre_cost_fabric_cost_dtls_id,b.gmts_sizes,b.article_number from wo_booking_dtls a join wo_emb_book_con_dtls b on a.id=b.wo_booking_dtls_id  where b.job_no='$txt_job_no'and b.po_break_down_id in ($txt_po_id) and a.pre_cost_fabric_cost_dtls_id=$txtembcostid and b.requirment<>0 and  a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0"; die;
	                        	$previous_booking = sql_select("SELECT a.gmt_item, b.requirment, b.po_break_down_id, b.color_size_table_id, b.color_number_id, a.pre_cost_fabric_cost_dtls_id,b.gmts_sizes,b.article_number from wo_booking_dtls a join wo_emb_book_con_dtls b on a.id=b.wo_booking_dtls_id  where b.job_no='$txt_job_no'and b.po_break_down_id in ($txt_po_id) and a.pre_cost_fabric_cost_dtls_id=$txtembcostid and b.requirment<>0 and  a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by a.gmt_item, b.requirment, b.po_break_down_id, b.color_size_table_id, b.color_number_id, a.pre_cost_fabric_cost_dtls_id,b.gmts_sizes,b.article_number");

							    foreach ($previous_booking as $row) {
							    	$previous_booking_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('gmts_sizes')]][$row[csf('article_number')]] += $row[csf('requirment')];
							    	$previous_booking_po_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('gmts_sizes')]][$row[csf('article_number')]][$row[csf('po_break_down_id')]] += $row[csf('requirment')];
							    }
							}
							else
							{
								$previous_booking = sql_select("SELECT a.gmt_item, b.requirment, b.po_break_down_id, b.color_size_table_id, b.color_number_id, a.pre_cost_fabric_cost_dtls_id,b.gmts_sizes,b.article_number from wo_booking_dtls a join wo_emb_book_con_dtls b on a.id=b.wo_booking_dtls_id  where b.job_no='$txt_job_no'and b.po_break_down_id in ($txt_po_id) and a.pre_cost_fabric_cost_dtls_id=$txtembcostid and b.requirment<>0 and  a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.id not in ($txt_update_dtls_id)");

							    foreach ($previous_booking as $row) {
							    	$previous_booking_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('gmts_sizes')]][$row[csf('article_number')]] += $row[csf('requirment')];
							    	$previous_booking_po_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('gmts_sizes')]][$row[csf('article_number')]][$row[csf('po_break_down_id')]] += $row[csf('requirment')];
							    }
							}
                        }
                        
                        
                        /*echo '<pre>';
                        print_r($previous_booking_po_arr); die;*/

                        $condition= new condition();
                        if(str_replace("'","",$txt_po_id) !=''){
							$condition->po_id("in($txt_po_id)");
                        }

                        $condition->init();
                        $emblishment= new emblishment($condition);
						$wash= new wash($condition);

                        $gmt_color_edb="";
                        $item_color_edb="";
                        $gmt_size_edb="";
                        $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							$req_qty_arr=$emblishment->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
							$req_amount_arr=$emblishment->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();

							$req_qty_arr_wash=$wash->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
							$req_amount_arr_wash=$wash->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();

							$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.item_number_id order by b.id, color_order";
							$gmt_size_edb=1;
							$item_size_edb=1;
							/*echo '<pre>';
							print_r($req_qty_arr_wash); ;die;*/
                        }
                        else if($cbo_colorsizesensitive==2){
							$req_qty_arr=$emblishment->getQtyArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();
							$req_amount_arr=$emblishment->getAmountArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();

							$req_qty_arr_wash=$wash->getQtyArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();
							$req_amount_arr_wash=$wash->getAmountArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();

							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,c.article_number,c.item_number_id,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)  $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id,c.article_number,c.item_number_id order by b.id,size_order";
							$gmt_color_edb=1;
							$item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							$req_qty_arr=$emblishment->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
							$req_amount_arr=$emblishment->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();

							$req_qty_arr_wash=$wash->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
							$req_amount_arr_wash=$wash->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();


							 $sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)  $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.item_number_id order by b.id, color_order";
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
							$req_qty_arr=$emblishment->getQtyArray_by_OrderEmblishmentidAndGmtscolorGmtssizeArticleAndGmtsitem();
							$req_amount_arr=$emblishment->getAmountArray_by_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem();

							$req_qty_arr_wash=$wash->getQtyArray_by_OrderEmblishmentidAndGmtscolorGmtssizeArticleAndGmtsitem();
							$req_amount_arr_wash=$wash->getAmountArray_by_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem();

						 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,c.item_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)  $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number,c.item_number_id  order by b.id, color_order,size_order";
                        }
                        else{
							$req_qty_arr=$emblishment->getQtyArray_by_orderEmblishmentidAndGmtsitem();
							$req_amount_arr=$emblishment->getAmountArray_by_orderEmblishmentidAndGmtsitem();

							$req_qty_arr_wash=$wash->getQtyArray_by_orderEmblishmentidAndGmtsitem();

							$req_amount_arr_wash=$wash->getAmountArray_by_orderEmblishmentidAndGmtsitem();

							 $sql="select b.id, b.po_number,b.po_quantity,c.item_number_id,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)  $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,c.item_number_id,a.total_set_qnty order by b.id";
                        }
                        //echo $sql; die;
                        $po_color_level_data_arr=array();
                        $po_size_level_data_arr=array();

                        $po_no_sen_level_data_arr=array();
                        $po_color_size_level_data_arr=array();
                        $data_array=sql_select($sql);
                        if ( count($data_array)>0){
							$i=0;
							foreach( $data_array as $row ){
								$data=explode('_',$data_array_cons[$i]);
								$i++;
								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == "") $item_size=$row[csf('item_size')];

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="")$rate=$txt_avg_price;

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="") $brand_supplier=trim($txt_pre_brand_sup);

								if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 )
								{
									if($emb_name==3){
										$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
									}else{
										$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
									}
									$txtwoq_cal =def_number_format($txt_req_quantity,5,"");

									$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

									$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];
								}
								else if($cbo_colorsizesensitive==2)
								{
									if($emb_name==3){
									$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
									}else{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
									}
									$txtwoq_cal =def_number_format($txt_req_quantity,5,"");
									$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];

									$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['article_number'][$row[csf('id')]]=$row[csf('article_number')];

									$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];

								}
								else if($cbo_colorsizesensitive==4)
								{
									if($emb_name==3){
									$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
									}else{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
									}
									$txtwoq_cal =def_number_format($txt_req_quantity,5,"");

									$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['article_number'][$row[csf('id')]]=$row[csf('article_number')];

									$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];
								}
								else if($cbo_colorsizesensitive==0)
								{
									if($emb_name==3){

										$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
									}else{
										$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
									}
									$txtwoq_cal =def_number_format($txt_req_quantity,5,"");
									$po_no_sen_level_data_arr[$txtembcostid]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_no_sen_level_data_arr[$txtembcostid]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_no_sen_level_data_arr[$txtembcostid]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_no_sen_level_data_arr[$txtembcostid]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_no_sen_level_data_arr[$txtembcostid]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_no_sen_level_data_arr[$txtembcostid]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

									$po_no_sen_level_data_arr[$txtembcostid]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_no_sen_level_data_arr[$txtembcostid]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_no_sen_level_data_arr[$txtembcostid]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];
								}
							}
                        }

						//print_r($po_no_sen_level_data_arr);

						$piNumber=0;
						$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no='$txt_booking_no' and b.item_group='".$txt_gmt_item_id."' and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
						if($pi_number){
							$piNumber=1;
						}
						$recvNumber=0;
						$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and b.item_group_id='".$txt_gmt_item_id."' and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
						if($recv_number){
							$recvNumber=1;
						}
						//echo $piNumber."PI8888888";

                        if ( count($data_array)>0 && $cbo_level==1){
							$i=0;
							foreach( $data_array as $row ){
								$data=explode('_',$data_array_cons[$i]);

								if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
									if($emb_name==3){
									$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
									}else{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
									}
									$txtwoq_cal = def_number_format($txt_req_quantity,5,"");
								}

								else if($cbo_colorsizesensitive==2){
									if($emb_name==3){
									$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
									}else{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
									}
									$txtwoq_cal = def_number_format($txt_req_quantity,5,"");
								}
								else if($cbo_colorsizesensitive==4){
									if($emb_name==3){
									$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
									}else{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
									}
									$txtwoq_cal = def_number_format($txt_req_quantity,5,"");
								}
								else if($cbo_colorsizesensitive==0){
									if($emb_name==3){
										$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
									}else{
										$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
									}
									$txtwoq_cal = def_number_format($txt_req_quantity,5,"");
								}


								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="") $rate=$txt_avg_price;

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="")$brand_supplier=trim($txt_pre_brand_sup);

								if($txtwoq_cal>0){
									$i++;
								?>
									<tr id="break_1" align="center">
                                        <td><? echo $i;?></td>
                                        <td><input type="text" id="poarticle_<? echo $i;?>"  name="poarticle_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('article_number')]; ?>"  readonly /></td>
                                        <td>
                                            <input type="text" id="pocolor_<? echo $i;?>"  name="pocolor_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="pocolorid_<? echo $i;?>"  name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<? echo $i;?>"  name="poid_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('id')]; ?>" />
                                            <input type="hidden" id="poqty_<? echo $i;?>"  name="poqty_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $po_qty_arr[$row[csf('id')]]; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<? echo $i;?>"  name="poreqqty_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>

                                        <td><input type="text" id="itemcolor_<? echo $i;?>"  value="<? echo $color_library[$item_color]; ?>"  name="itemcolor_<? echo $i;?>"  class="text_boxes" style="width:100px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)" <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:80px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $item_size; ?>" <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="hidden" id="reqqty_<? echo $i;?>"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"    value="<? echo $txtwoq_cal ?>" readonly/>
                                        	<input type="text" id="qty_<? echo $i;?>"  onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/>
                                        </td>
                                        <td>
                                        	<input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/>
                                        </td>
                                        <td>
                                        	<input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' )" onChange="set_sum_value( 'woqty_sum', 'woqny_' )" name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly />
                                        </td>
                                        <td>
                                        	<input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:120px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td>
                                        	<input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:100px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<? echo $i;?>"  name="pcsset_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity_set')]; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
                                            <input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
									</tr>
								<?
								}
							}
                        }

                        $level_arr=array();
                        $gmt_color_edb="";
                        $item_color_edb="";
                        $gmt_size_edb="";
                        $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							 $sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  c.color_number_id,c.item_number_id order by color_order";
							 	//echo $sql; die;
								$level_arr=$po_color_level_data_arr;
								$gmt_size_edb=1;
								$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,c.article_number,c.item_number_id,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  c.size_number_id,c.article_number,c.item_number_id order by size_order";
							$level_arr=$po_size_level_data_arr;
							$gmt_color_edb=1;
							$item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							 $sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)    $txt_country_cond and d.id=$txtembcostid group by  c.color_number_id,c.item_number_id order by color_order";
								$level_arr=$po_color_level_data_arr;
								$gmt_size_edb=1;
								$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
						 $sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,c.item_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  c.color_number_id,c.size_number_id, c.article_number,c.item_number_id order by  color_order,size_order,c.article_number";
							$level_arr=$po_color_size_level_data_arr;
                        }
                        else{
							  $sql="select b.job_no_mst,c.item_number_id,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  b.job_no_mst,c.item_number_id";
								$level_arr=$po_no_sen_level_data_arr;
                        }
                        //echo $sql; die;
                        /*echo '<pre>';
                        print_r($level_arr); die;*/
                        $data_array=sql_select($sql);
                        if ( count($data_array)>0 && $cbo_level==2){
							$i=0;
							$total_order=0;
							foreach( $data_array as $row ){

								if($cbo_colorsizesensitive==1){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty']),5,"");
									$previous_booking_woq = def_number_format($previous_booking_arr[$txtembcostid][$row[csf('color_number_id')]],5,"");
									$po_qty=array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_amt']),5,"");
									$color_wise_po_qty='';
									foreach ($level_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty'] as $poid => $qty) {
										$balance_qty1=$qty-$previous_booking_po_arr[$txtembcostid][$row[csf('color_number_id')]][$poid];
										$color_wise_po_qty .= $poid.'-'.$balance_qty1.',';
									}
									$color_wise_po_qty = substr($color_wise_po_qty, 0, -1);

								}
								if($cbo_colorsizesensitive==2){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']),5,"");
									$previous_booking_woq=0;
									$po_qty=array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt']),5,"");
								}
								if($cbo_colorsizesensitive==3){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty']),5,"");
									$previous_booking_woq = def_number_format($previous_booking_arr[$txtembcostid][$row[csf('color_number_id')]],5,"");
									$po_qty=array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_amt']),5,"");
									$color_wise_po_qty='';
									foreach ($level_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty'] as $poid => $qty) {
										$balance_qty1=$qty-$previous_booking_po_arr[$txtembcostid][$row[csf('color_number_id')]][$poid];
										$color_wise_po_qty .= $poid.'-'.$balance_qty1.',';
									}
									$color_wise_po_qty = substr($color_wise_po_qty, 0, -1);
								}
								if($cbo_colorsizesensitive==4){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']),5,"");
									$previous_booking_woq = def_number_format($previous_booking_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]],5,"");
									$po_qty=array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt']),5,"");
									$color_wise_po_qty='';
									foreach ($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'] as $poid => $qty) {									
										$balance_qty1=$qty-$previous_booking_po_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$poid];
										$color_wise_po_qty .= $poid.'-'.$balance_qty1.',';
									}
									$color_wise_po_qty = substr($color_wise_po_qty, 0, -1);
								}
								if($cbo_colorsizesensitive==0){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$txtembcostid]['req_qty']),5,"");
									$previous_booking_woq=0;
									$po_qty=array_sum($level_arr[$txtembcostid]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$txtembcostid]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$txtembcostid]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$txtembcostid]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$txtembcostid]['booking_amt']),5,"");
								}

								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];

								//$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($booking_amt>0)
								{
									$rate=$booking_amt/$booking_qty;
								}
								else
								{
									 $rate=$txt_avg_price;
								}


								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);
								//echo $description.'='.$txt_pre_des.'<br/>';
								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="") $brand_supplier=trim($txt_pre_brand_sup);
								//echo $color_wise_po_qty;
								//echo __LINE__.'qty: '.$txtwoq_cal.'==='.$previous_booking_woq.'<br/>';
								if($txtwoq_cal-$previous_booking_woq>0){
									$i++;
								?>
									<tr id="break_1" align="center">
                                        <td><? echo $i;?></td>
                                        <td><input type="text" id="poarticle_<? echo $i;?>"  name="poarticle_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('article_number')]; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="pocolor_<? echo $i;?>"  name="pocolor_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly />
                                            <input type="hidden" id="pocolorid_<? echo $i;?>"  name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<? echo $i;?>"  name="poid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('id')]; ?>" readonly />
                                            <input type="hidden" id="poqty_<? echo $i;?>"  name="poqty_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $po_qty; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<? echo $i;?>"  name="poreqqty_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $txtwoq_cal; ?>" readonly />
                                            <input type="hidden" name="pocolorqty_<? echo $i ?>" id="pocolorqty_<? echo $i ?>" value="<? echo $color_wise_po_qty ?>" >
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>

                                        <td><input type="text" id="itemcolor_<? echo $i;?>"  value="<? echo $color_library[$item_color]; ?>" name="itemcolor_<? echo $i;?>" class="text_boxes" style="width:100px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)"   <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>" class="text_boxes" style="width:70px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $item_size; ?>"  <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/>
                                        </td>
                                        <td><input type="hidden" id="reqqty_<? echo $i;?>"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"    value="<? echo $txtwoq_cal-$previous_booking_woq //$txtwoq_cal ?>" readonly/>

                                        	<input type="text" id="qty_<? echo $i;?>" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px"   placeholder="<? echo $txtwoq_cal-$previous_booking_woq ?>" value="<? if($booking_qty>0){echo $booking_qty;} ?>"/>
                                        </td>
                                        <td>
                                        	<input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/>
                                        </td>
                                        <td><input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' ) " onChange="set_sum_value( 'woqty_sum', 'woqny_' )"  name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<?  if($booking_qty){echo $booking_qty;} ?>" readonly />
                                        </td>
                                        <td><input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:120px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:100px"  value="<? echo $booking_amt; //$booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly>
                                        </td>

                                        <td><input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<? echo $i;?>"  name="pcsset_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $order_quantity_set; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
                                            <input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
									</tr>
								<?
									if($booking_data_arr[$row[csf('color_size_table_id')]][id] == '')
									{
										$total_order += $row[csf('order_quantity')];
									}
									else
									{
										$total_order = $txtwoq;
									}
									
								}
							}
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                               <th width="40">&nbsp;</th><th width="100">&nbsp;</th><th width="100">&nbsp;</th><th width="70">&nbsp;</th><th width="100">&nbsp;</th><th width="100">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:70px"  readonly></th>
                                <th width="40"><input type="text" id="excess_sum"  name="excess_sum" class="text_boxes_numeric" style="width:40px" readonly></th>
                                <th width="70"><input type="text" id="woqty_sum"  name="woqty_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
                                <th width="40"><input type="text" id="rate_sum"  name="rate_sum" class="text_boxes_numeric" style="width:120px" readonly></th>
                                <th width="50"><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:100px" readonly></th>
                                <th><input type="hidden" id="json_data" name="json_data" class="text_boxes_numeric" style="width:50px" value='<? echo json_encode($level_arr); ?>' readonly>
                                	<input type="text" id="pcs_sum" name="pcs_sum" class="text_boxes_numeric" style="width:50px" readonly>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <table width="1150" cellspacing="0" class="" border="0" rules="all">
                        <tr>
                            <td align="center" width="100%"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()"/> </td>
                        </tr>
                    </table>
                </form>
            </fieldset>
        </div>
	</body>
	<script>
		$("input[type=text]").focus(function() {
		   $(this).select();
		});
		<?
		if($txt_update_dtls_id==""){
			?>
			poportionate_qty(<? echo $total_order; ?>);
			<?
		}
		?>
		set_sum_value( 'qty_sum', 'qty_' );
		set_sum_value( 'woqty_sum', 'woqny_' );
		set_sum_value( 'amount_sum', 'amount_' );
		set_sum_value( 'pcs_sum', 'pcs_' );
		calculate_avg_rate();
		var wo_qty=$('#txtwoq_qty').val()*1;
		var txt_req_quantity=$('#txt_req_quantity').val()*1;
		var txtcuwoq=$('#txtcuwoq').val()*1;

		var wo_qty_sum=$('#qty_sum').val()*1;

		if(wo_qty!=wo_qty_sum)
		{
			//$('#td_sync_msg').html("Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.");
		}
		//if(txt_req_quantity<wo_qty+txtcuwoq)
		//alert(wo_qty+'='+txt_req_quantity);
		if(wo_qty!=wo_qty_sum)
		{
			$('#td_po_qnt_chagne_msg').html("Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.");

		}
		


	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
    exit();
}

if ($action=="set_cons_break_down")
{
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$data=explode("_",$data);
/*	echo '<pre>';
	print_r($data); die;*/
	$garments_nature=$data[0];
	$cbo_company_name=$data[1];
	$txt_job_no=$data[2];
	$txt_po_id=$data[3];
	$txtembcostid=trim($data[4]);
	$txtgmtitemid=$data[5];
	$txt_update_dtls_id=trim($data[6]);
	$cbo_colorsizesensitive=$data[7];
	$txt_req_quantity=$data[8];
	$txt_avg_price=$data[9];
	$txt_country=$data[10];
	$emb_name=$data[11];
	$emb_type=$data[12];
	$cbo_level=$data[13];

	if($txt_job_no==""){
		$txt_job_no_cond="";
		$txt_job_no_cond1="";
	}
	else{
		$txt_job_no_cond ="and a.job_no='$txt_job_no'";
		$txt_job_no_cond1 ="and job_no='$txt_job_no'";
	}

	if($txt_country=="") $txt_country_cond=""; else $txt_country_cond ="and c.country_id in ($txt_country)";

	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
	$sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id) c.item_number_id=$txtgmtitemid   $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
	$tot_po_qty=0;
	foreach($sql_po_qty as $sql_po_qty_row){
		$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
		$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
	}


	$booking_data_arr=array();
	if($txt_update_dtls_id=="" || $txt_update_dtls_id==0) $txt_update_dtls_id=0;else $txt_update_dtls_id=$txt_update_dtls_id;
	$booking_data=sql_select("select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id  from wo_emb_book_con_dtls where wo_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
	foreach($booking_data as $booking_data_row){
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][id]=$booking_data_row[csf('id')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][description]=$booking_data_row[csf('description')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_color]=$booking_data_row[csf('item_color')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_size]=$booking_data_row[csf('item_size')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][cons]+=$booking_data_row[csf('cons')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][process_loss_percent]=$booking_data_row[csf('process_loss_percent')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][requirment]+=$booking_data_row[csf('requirment')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][rate]=$booking_data_row[csf('rate')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][amount]+=$booking_data_row[csf('amount')];
	}

	if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3){
		$previous_booking = sql_select("SELECT b.requirment, b.po_break_down_id, b.color_size_table_id, b.color_number_id, a.pre_cost_fabric_cost_dtls_id from wo_booking_dtls a join wo_emb_book_con_dtls b on a.id=b.wo_booking_dtls_id  where b.job_no='$txt_job_no'and b.po_break_down_id in ($txt_po_id) and a.pre_cost_fabric_cost_dtls_id=$txtembcostid and b.requirment<>0 and  a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0");

	    foreach ($previous_booking as $row) {
	    	$previous_booking_arr[$row[csf('po_break_down_id')]][$txtembcostid][$row[csf('color_number_id')]] += $row[csf('requirment')];
	    	$previous_booking_po_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('po_break_down_id')]] += $row[csf('requirment')];
	    }
	}
	if($cbo_colorsizesensitive==4){
		$previous_booking = sql_select("SELECT a.gmt_item, b.requirment, b.po_break_down_id, b.color_size_table_id, b.color_number_id, a.pre_cost_fabric_cost_dtls_id,b.gmts_sizes,b.article_number from wo_booking_dtls a join wo_emb_book_con_dtls b on a.id=b.wo_booking_dtls_id  where b.job_no='$txt_job_no'and b.po_break_down_id in ($txt_po_id) and a.pre_cost_fabric_cost_dtls_id=$txtembcostid and b.requirment<>0 and  a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0");

	    foreach ($previous_booking as $row) {
	    	$previous_booking_arr[$row[csf('po_break_down_id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('gmts_sizes')]][$row[csf('article_number')]][$row[csf('gmt_item')]] += $row[csf('requirment')];
	    }
	}
    /*echo '<pre>';
    print_r($previous_booking_arr); die;*/

	$condition= new condition();
	if(str_replace("'","",$txt_po_id) !=''){
			$condition->po_id("in($txt_po_id)");
	}

	$condition->init();
    $emblishment= new emblishment($condition);
	$wash= new wash($condition);

	$gmt_color_edb="";
	$item_color_edb="";
	$gmt_size_edb="";
	$item_size_edb="";
	if($cbo_colorsizesensitive==1){

		$req_qty_arr=$emblishment->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
		$req_amount_arr=$emblishment->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
//print_r($req_qty_arr);die;
		$req_qty_arr_wash=$wash->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
		$req_amount_arr_wash=$wash->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();

		 $sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.item_number_id order by b.id, color_order";
		$gmt_size_edb=1;
		$item_size_edb=1;
	}
	else if($cbo_colorsizesensitive==2){
		$req_qty_arr=$emblishment->getQtyArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();
		$req_amount_arr=$emblishment->getAmountArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();

		$req_qty_arr_wash=$wash->getQtyArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();
		$req_amount_arr_wash=$wash->getAmountArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();

		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,c.article_number,c.item_number_id,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id,c.article_number,c.item_number_id order by b.id,size_order";
		$gmt_color_edb=1;
		$item_color_edb=1;
	}
	else if($cbo_colorsizesensitive==3){
		$req_qty_arr=$emblishment->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
		$req_amount_arr=$emblishment->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
		$req_qty_arr_wash=$wash->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
		$req_amount_arr_wash=$wash->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
		$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)  $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.item_number_id order by b.id, color_order";
		$gmt_size_edb=1;
		$item_size_edb=1;
	}
	else if($cbo_colorsizesensitive==4){

		$req_qty_arr=$emblishment->getQtyArray_by_OrderEmblishmentidAndGmtscolorGmtssizeArticleAndGmtsitem();
		$req_amount_arr=$emblishment->getAmountArray_by_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem();

		$req_qty_arr_wash=$wash->getQtyArray_by_OrderEmblishmentidAndGmtscolorGmtssizeArticleAndGmtsitem();
		$req_amount_arr_wash=$wash->getAmountArray_by_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem();

		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,c.item_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number,c.item_number_id  order by b.id, color_order,size_order";
	}
	else{
		$req_qty_arr=$emblishment->getQtyArray_by_orderEmblishmentidAndGmtsitem();
		$req_amount_arr=$emblishment->getAmountArray_by_orderEmblishmentidAndGmtsitem();

		$req_qty_arr_wash=$wash->getQtyArray_by_orderEmblishmentidAndGmtsitem();
		$req_amount_arr_wash=$wash->getAmountArray_by_orderEmblishmentidAndGmtsitem();

		$sql="select b.id, b.po_number,b.po_quantity,c.item_number_id,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,c.item_number_id,a.total_set_qnty order by b.id";
	}
/*	echo '<pre>';
	print_r($req_qty_arr_wash); die;*/
	$data_array=sql_select($sql);
	if ( count($data_array)>0)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			$color_number_id=$row[csf('color_number_id')];
			if($color_number_id=="") $color_number_id=0;

			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="") $size_number_id=0;

			$description=$txt_pre_des;
			if($description=="") $description=0;



			$item_color=$color_library[$row[csf('color_number_id')]];
			if($item_color=="") $item_color=0;

			$item_size=$row[csf('item_size')];
			if($item_size=="") $item_size=0;
			$excess=0;
			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="") $pcs=0;

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="") $colorsizetableid=0;

			$articleNumber=$row[csf('article_number')];
			if($articleNumber=="") $articleNumber='no article';

			if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
				if($emb_name==3){
				$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
				}else{
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
				}

				//$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$txt_req_quantity),5,"");
				$previous_booking_woq= $previous_booking_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]];
				$balance_woq=$txt_req_quantity-$previous_booking_woq;
				//echo $row[csf('color_number_id')].'--'.$balance_woq.'<br>';
				if($balance_woq>0)
				{
					$req_qnty_ordUom = def_number_format((($balance_woq/$data[14])*$data[14]),5,"");
					$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
					$amount = def_number_format($txtwoq_cal*$txt_avg_price,5,"");

					$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
					$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['balance_qty'][$row[csf('id')]]=$txtwoq_cal;
					$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
					$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
					$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
					$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
					$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
					$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['amount'][$row[csf('id')]]=$amount;
				}
				
			}
			else if($cbo_colorsizesensitive==2){
				if($emb_name==3){
				$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}else{
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$txt_req_quantity),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");

				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['amount'][$row[csf('id')]]=$amount;
			}
			else if($cbo_colorsizesensitive==4){
				if($emb_name==3){
				$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}else{
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}
				//$previous_booking_woq= $previous_booking_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]];
				$previous_booking_woq=$previous_booking_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				$balance_woq=$txt_req_quantity-$previous_booking_woq;

				if($balance_woq>0)
				{
					//$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$txt_req_quantity),5,"");
					$req_qnty_ordUom = def_number_format((($balance_woq/$data[14])*$data[14]),5,"");
					$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
					$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");

					$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
					$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
					$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
					$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
					$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];

					$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

					$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['amount'][$row[csf('id')]]=$amount;
					}
				
			}
			else if($cbo_colorsizesensitive==0){
				if($emb_name==3){
					$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
				}else{
					$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$txt_req_quantity),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");

				$po_no_sen_level_data_arr[$txtembcostid]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_no_sen_level_data_arr[$txtembcostid]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_no_sen_level_data_arr[$txtembcostid]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_no_sen_level_data_arr[$txtembcostid]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_no_sen_level_data_arr[$txtembcostid]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
				$po_no_sen_level_data_arr[$txtembcostid]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
				$po_no_sen_level_data_arr[$txtembcostid]['amount'][$row[csf('id')]]=$amount;
			}
		}
	}

	$cons_breck_down="";
	if ( count($data_array)>0 && $cbo_level==1)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			$color_number_id=$row[csf('color_number_id')];
			if($color_number_id=="") $color_number_id=0;

			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="") $size_number_id=0;

			$description=$txt_pre_des;
			if($description=="") $description=0;

			$brand_supplier=$txt_pre_brand_sup;
			if($brand_supplier=="") $brand_supplier=0;

			$item_color=$color_library[$row[csf('color_number_id')]];
			if($item_color=="") $item_color=0;

			//$item_size=$row[csf('item_size')];
			$item_size=$size_library[$row[csf('size_number_id')]];
			if($item_size=="") $item_size=0;
			$excess=0;

			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="") $pcs=0;

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="") $colorsizetableid=0;

			$articleNumber=$row[csf('article_number')];
			if($articleNumber=="") $articleNumber='no article';

			if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
                if($emb_name==3){
				$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
				}else{
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
				}				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$txt_req_quantity),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}

			else if($cbo_colorsizesensitive==2){
                if($emb_name==3){
				$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}else{
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$txt_req_quantity),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			else if($cbo_colorsizesensitive==4){
				if($emb_name==3){
				$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}else{
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$txt_req_quantity),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			else if($cbo_colorsizesensitive==0){

				if($emb_name==3){
				$txt_req_quantity=$req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
				}else{
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$txt_req_quantity),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($txtwoq_cal>0){
				if($cons_breck_down=="")
				{
					$cons_breck_down.=$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber;
				}
				else
				{
					$cons_breck_down.="__".$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber;
				}
			}
		}
		echo $cons_breck_down;
	}	

	$level_arr=array();
	$gmt_color_edb="";
	$item_color_edb="";
	$gmt_size_edb="";
	$item_size_edb="";
	if($cbo_colorsizesensitive==1){
		 $sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)    $txt_country_cond and d.id=$txtembcostid group by  c.color_number_id,c.item_number_id order by color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb=1;
		$item_size_edb=1;
	}
	else if($cbo_colorsizesensitive==2){
		$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,c.article_number,c.item_number_id,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  c.size_number_id,c.article_number,c.item_number_id order by size_order";
		$level_arr=$po_size_level_data_arr;
		$gmt_color_edb=1;
		$item_color_edb=1;
	}
	else if($cbo_colorsizesensitive==3){
		$sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  c.color_number_id,c.item_number_id order by color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb=1;
		$item_size_edb=1;
	}
	else if($cbo_colorsizesensitive==4){
		$sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,c.item_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  c.color_number_id,c.size_number_id, c.article_number,c.item_number_id order by  color_order,size_order,c.article_number";
		$level_arr=$po_color_size_level_data_arr;
	}
	else{
		$sql="select b.job_no_mst,c.item_number_id,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)  $txt_country_cond and d.id=$txtembcostid group by  b.job_no_mst,c.item_number_id";
		$level_arr=$po_no_sen_level_data_arr;
	}
	$data_array=sql_select($sql);

	$cons_breck_down="";
	if ( count($data_array)>0 && $cbo_level==2)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			if($cbo_colorsizesensitive==1){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set']);
				$booking_qty=def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_qty']),5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
				$color_wise_po_qty='';
				foreach ($level_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty'] as $poid => $qty) {
					$color_wise_po_qty .= $poid.'-'.$qty.',';
				}
				$color_wise_po_qty = substr($color_wise_po_qty, 0, -1);
				//echo $txtwoq_cal.'=='.$color_wise_po_qty;die;

			}
			if($cbo_colorsizesensitive==2){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==3){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
				$color_wise_po_qty='';
				foreach ($level_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty'] as $poid => $qty) {
					$color_wise_po_qty .= $poid.'-'.$qty.',';
				}
				$color_wise_po_qty = substr($color_wise_po_qty, 0, -1);
			}
			if($cbo_colorsizesensitive==4){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
				$color_wise_po_qty='';
				foreach ($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'] as $poid => $qty) {
					$color_wise_po_qty .= $poid.'-'.$qty.',';
				}
				$color_wise_po_qty = substr($color_wise_po_qty, 0, -1);
			}
			if($cbo_colorsizesensitive==0){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$txtembcostid]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$txtembcostid]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$txtembcostid]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			$color_number_id=$row[csf('color_number_id')];
			if($color_number_id=="") $color_number_id=0;

			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="") $size_number_id=0;

			$description=$txt_pre_des;
			if($description=="") $description=0;



			$item_color=$color_library[$row[csf('color_number_id')]];
			if($item_color=="") $item_color=0;

			//$item_size=$row[csf('item_size')];
		    $item_size=$size_library[$row[csf('size_number_id')]];
			if($item_size=="") $item_size=0;
			$excess=0;

			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="") $pcs=0;

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="") $colorsizetableid=0;

			$articleNumber=$row[csf('article_number')];
			if($articleNumber=="") $articleNumber='no article';

			if($txtwoq_cal>0){
				if($cons_breck_down==""){
					$cons_breck_down.=trim($color_number_id).'_'.$size_number_id.'_'.$description.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber."_".$color_wise_po_qty;
				}
				else{
					$cons_breck_down.="__".trim($color_number_id).'_'.$size_number_id.'_'.$description.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber."_".$color_wise_po_qty;
				}
			}
		}
		echo $cons_breck_down."**".json_encode($level_arr);
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$lock_another_process='';
	//echo '10**';//.$txt_booking_no;die;
	if(str_replace("'","",$txt_booking_no)!='')
	{
		$sql=sql_select("SELECT b.job_no_mst from subcon_ord_mst a join subcon_ord_dtls b on a.id=b.mst_id where b.order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.job_no_mst");
		foreach($sql as $row){
			$lock_another_process=$row[csf('job_no_mst')];
		}
		if($lock_another_process!=''){
			echo "lockAnotherProcess**".$lock_another_process;
			 disconnect($con);die;
		}
	}
	//echo $lock_another_process; die;

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'ESB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and entry_form=574 and booking_type=6 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));

		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, supplier_location_id, floor_id, currency_id, item_category, pay_mode, source, booking_date, delivery_date, supplier_id, attention, ready_to_approved, entry_form, inserted_by, insert_date, cbo_level, remarks";

		 $data_array ="(".$id.",6,".$cbo_isshort.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_party_location.",".$cbo_floor_id.",".$cbo_currency.",25,".$cbo_pay_mode.",".$cbo_source.",".$txt_booking_date.",".$txt_delivery_date.",".$hidden_supplier_id.",".$txt_attention.",".$cbo_ready_to_approved.",574,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_level.",".$remarks.")";
		 //echo "10** insert into wo_booking_mst (".$field_array.") values ".$data_array;die;
		 $rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0]."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0]."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			 disconnect($con);die;
		}
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				 disconnect($con);die;
			}
		}
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="buyer_id*currency_id*item_category*pay_mode*source*booking_date*delivery_date*supplier_id*supplier_location_id*floor_id*attention*ready_to_approved*is_short*updated_by*update_date*cbo_level*remarks";
		 $data_array ="".$cbo_buyer_name."*".$cbo_currency."*25*".$cbo_pay_mode."*".$cbo_source."*".$txt_booking_date."*".$txt_delivery_date."*".$hidden_supplier_id."*".$cbo_party_location."*".$cbo_floor_id."*".$txt_attention."*".$cbo_ready_to_approved."*".$cbo_isshort."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_level."*".$remarks."";
		$rID=sql_update("wo_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);
		$rID=execute_query( "update wo_booking_mst set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where  booking_no=$txt_booking_no",0);
		$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where  booking_no=$txt_booking_no",0);
		if($db_type==0)
		{
			if($rID && $rID1){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="save_update_delete_dtls")
{
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_booking_no);
		 disconnect($con);die;
	}
	$lock_another_process='';
	if(str_replace("'","",$txt_booking_no)!='')
	{
		$sql=sql_select("SELECT b.job_no_mst from subcon_ord_mst a join subcon_ord_dtls b on a.id=b.mst_id where b.order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.job_no_mst");
		foreach($sql as $row){
			$lock_another_process=$row[csf('job_no_mst')];
		}
		if($lock_another_process!=''){
			echo "lockAnotherProcess**".$lock_another_process;
			 disconnect($con);die;
		}
	}
	if ($operation==0){
		$gmtArr=array();
		$poArr=array();
		$pre_emb_id_arr=array();
		for ($i=1;$i<=$total_row;$i++){
			$txtembcostid="txtembcostid_".$i;
			$txtpoid="txtpoid_".$i;
		    $txtgmtitemid='txtgmtitemid_'.$i;
			$txtembcostid=str_replace("'","",$$txtembcostid);
			$poid=str_replace("'","",$$txtpoid);
			$gmtItem=str_replace("'","",$$txtgmtitemid);
			$pre_emb_id_arr[$pretrimcostid]=$txtembcostid;
			$poArr[$poid]=$poid;
			$gmtArr[$gmtItem]=$gmtItem;
		}
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**0";
			 disconnect($con);die;
		}

	    if (is_duplicate_field( "booking_no", "wo_booking_dtls", "gmt_item in(".implode(",",$gmtArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_emb_id_arr).") and booking_type=6 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0") == 1)
		{
			check_table_status( $_SESSION['menu_id'],0);
			echo "11**0";
		 disconnect($con);	die;
		}

		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;

		$field_array1="id,booking_mst_id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no,gmt_item, booking_no, booking_type, is_short, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date,country_id_string, inserted_by, insert_date";
		$field_array2="id,wo_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, pcs,color_size_table_id,article_number";

		$add_comma=0;
		$id1=return_next_id( "id", "wo_emb_book_con_dtls", 1 );
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++){


			//===============
			$txtembcostid="txtembcostid_".$i;
			$txtgmtitemid='txtgmtitemid_'.$i;
			$txtpoid="txtpoid_".$i;
			$txtuom="txtuom_".$i;
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtwoq="txtwoq_".$i;
			$txtexchrate="txtexchrate_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtddate="txtddate_".$i;
			$consbreckdown="consbreckdown_".$i;
			$txtbookingid="txtbookingid_".$i;
			$txtcountry="txtcountry_".$i;
			$txtjob_id="txtjob_".$i;
			$txtreqqnty="txtreqqnty_".$i;
			$jsondata="jsondata_".$i;
			$txtreqamount="txtreqamount_".$i;

			$uom_id=str_replace("'","",$$txtuom);
			$job=str_replace("'","",$$txtjob_id);
			$embcostid=str_replace("'","",$$txtembcostid);
			$gmtitemid=str_replace("'","",$$txtgmtitemid);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);
			$amt=str_replace("'","",$$txtamount);
			$exRate=str_replace("'","",$$txtexchrate);
			$update_id=str_replace("'","",$update_id);
			//==============

			$data_array1 ="(".$id_dtls.",".$update_id.",".$$txtembcostid.",".$$txtpoid.",".$$txtjob_id.",".$$txtgmtitemid.",".$txt_booking_no.",6,2,".$$txtuom.",".$$cbocolorsizesensitive.",".$$txtwoq.",".$$txtexchrate.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//	CONS break down===============================================================================================
			if(str_replace("'",'',$$consbreckdown) !=''){
				$data_array2="";
				$rID_de1=execute_query( "delete from wo_emb_book_con_dtls where  wo_booking_dtls_id =".$$txtbookingid."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					if(str_replace("'","",$consbreckdownarr[3])!="")
					{
						if (!in_array(str_replace("'","",$consbreckdownarr[3]),$new_array_color)){
							$color_id = return_id( str_replace("'","",$consbreckdownarr[3]), $color_library, "lib_color", "id,color_name","403");
							$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[3]);
						}
						else $color_id =  array_search(str_replace("'","",$consbreckdownarr[3]), $new_array_color);
					}
					else $color_id =0;

					if ($c!=0) $data_array2 .=",";
					$data_array2 .="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$color_id."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[13]."')";
					$id1=$id1+1;
					$add_comma++;
					//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
				}
			}
			//CONS break down end===============================================================================================
			// echo "10** insert into wo_booking_dtls (".$field_array1.") values ".$data_array1;die;
			$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
			$rID2=1;
			if($data_array2 !=""){
				$rID2=sql_insert("wo_emb_book_con_dtls",$field_array2,$data_array2,1);
			}
			$id_dtls=$id_dtls+1;
		}

		check_table_status( $_SESSION['menu_id'],0);
		//echo "10**".$rID1." &&". $rID2;die;
		if($db_type==0){
			if($rID1 && $rID2){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}
		if($db_type==2 || $db_type==1 ){
			if($rID1 && $rID2){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)
	{
	    $gmtArr=array();
		$poArr=array();
		$pre_emb_id_arr=array();
		$booking_dtls_id_arr=array();
		for ($i=1;$i<=$total_row;$i++){
			$txtembcostid="txtembcostid_".$i;
			$txtpoid="txtpoid_".$i;
		    $txtgmtitemid='txtgmtitemid_'.$i;
			$txtbookingid="txtbookingid_".$i;

			$txtembcostid=str_replace("'","",$$txtembcostid);
			$poid=str_replace("'","",$$txtpoid);
			$gmtItem=str_replace("'","",$$txtgmtitemid);
			$bookingdtlsid=str_replace("'","",$$txtbookingid);

			$pre_emb_id_arr[$pretrimcostid]=$txtembcostid;
			$poArr[$poid]=$poid;
			$gmtArr[$gmtItem]=$gmtItem;
			$booking_dtls_id_arr[$bookingdtlsid]=$bookingdtlsid;

		}


	$con = connect();
	if($db_type==0){
	mysql_query("BEGIN");
	}
	if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
		echo "15**1";
		 disconnect($con);die;
	}

	if (is_duplicate_field( "booking_no", "wo_booking_dtls", "gmt_item in(".implode(",",$gmtArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_emb_id_arr).") and id not in (".implode(",",$booking_dtls_id_arr).") and booking_type=6 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0") == 1)
	{
	check_table_status( $_SESSION['menu_id'],0);
	echo "11**0";
	 disconnect($con);die;
	}

	$field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*gmt_item*booking_no*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*country_id_string*updated_by*update_date";
	$field_array_up2="id,wo_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id,article_number";

	$add_comma=0;
	$id1=return_next_id( "id", "wo_emb_book_con_dtls", 1 );
	$new_array_color=array();
	for ($i=1;$i<=$total_row;$i++){
		    $txtembcostid="txtembcostid_".$i;
			$txtgmtitemid='txtgmtitemid_'.$i;
			$txtpoid="txtpoid_".$i;
			$txtuom="txtuom_".$i;
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtwoq="txtwoq_".$i;
			$txtexchrate="txtexchrate_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtddate="txtddate_".$i;
			$consbreckdown="consbreckdown_".$i;
			$txtbookingid="txtbookingid_".$i;
			$txtcountry="txtcountry_".$i;
			$txtjob_id="txtjob_".$i;
			$txtreqqnty="txtreqqnty_".$i;
			$jsondata="jsondata_".$i;
			$txtreqamount="txtreqamount_".$i;

			$uom_id=str_replace("'","",$$txtuom);
			$job=str_replace("'","",$$txtjob_id);
			$embcostid=str_replace("'","",$$txtembcostid);
			$gmtitemid=str_replace("'","",$$txtgmtitemid);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);
			$amt=str_replace("'","",$$txtamount);
			$exRate=str_replace("'","",$$txtexchrate);

		if(str_replace("'",'',$$txtbookingid)!=""){
			$id_arr=array();
			$data_array_up1=array();
			$id_arr[]=str_replace("'",'',$$txtbookingid);
			$data_array_up1[str_replace("'",'',$$txtbookingid)] =explode("*",("".$$txtembcostid."*".$$txtpoid."*".$$txtjob_id."*".$$txtgmtitemid."*".$txt_booking_no."*".$$txtuom."*".$$cbocolorsizesensitive."*".$$txtwoq."*".$$txtexchrate."*".$$txtrate."*".$$txtamount."*".$$txtddate."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			//	CONS break down===============================================================================================
			if(str_replace("'",'',$$consbreckdown) !=''){
				$data_array_up2="";
				$rID_de1=execute_query( "delete from wo_emb_book_con_dtls where  wo_booking_dtls_id =".$$txtbookingid."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					if(str_replace("'","",$consbreckdownarr[3])!="")
					{
						if (!in_array(str_replace("'","",$consbreckdownarr[3]),$new_array_color)){
							$color_id = return_id( str_replace("'","",$consbreckdownarr[3]), $color_library, "lib_color", "id,color_name","403");
							$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[3]);
						}
						else $color_id =  array_search(str_replace("'","",$consbreckdownarr[3]), $new_array_color);
					}
					else $color_id =0;


					if ($c!=0) $data_array_up2 .=",";
					$data_array_up2 .="(".$id1.",".$$txtbookingid.",".$txt_booking_no.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$color_id."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[13]."')";
					$id1=$id1+1;
					$add_comma++;
				}
			}
			//CONS break down end===============================================================================================
			if($data_array_up1 !="")
			{
			$rID1=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
			}
		}
		$rID2=1;
		if($data_array_up2 !="")
		{
		$rID2=sql_insert("wo_emb_book_con_dtls",$field_array_up2,$data_array_up2,1);
		}
	}
	$rID=execute_query( "update wo_booking_mst set revised_no=revised_no+1 where  booking_no=$txt_booking_no",0);
	check_table_status( $_SESSION['menu_id'],0);
	if($db_type==0){
		if($rID1 &&  $rID2){
			mysql_query("COMMIT");
			echo "1**".str_replace("'","",$txt_booking_no);
		}
		else{
			mysql_query("ROLLBACK");
			echo "10**".str_replace("'","",$txt_booking_no);
		}
	}

	if($db_type==2 || $db_type==1 ){
		if($rID1 &&  $rID2){
			oci_commit($con);
			echo "1**".str_replace("'","",$txt_booking_no);
		}
		else{
			oci_rollback($con);
			echo "10**".str_replace("'","",$txt_booking_no);
		}
	}
	disconnect($con);
	die;
	}

	else if ($operation==2){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		for ($i=1;$i<=$total_row;$i++){
			$txtpoid="txtpoid_".$i;
			$txtbookingid="txtbookingid_".$i;
			$delete_cause=str_replace("'","",$delete_cause);
			$delete_cause=str_replace('"','',$delete_cause);
			$delete_cause=str_replace('(','',$delete_cause);
			$delete_cause=str_replace(')','',$delete_cause);

			$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  id in (".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
		    $rID2=execute_query( "update wo_emb_book_con_dtls set status_active=0,is_deleted=1 where  wo_booking_dtls_id in(".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
		}
		if($db_type==0){
			if($rID1 &&  $rID2){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}

		if($db_type==2 || $db_type==1 ){
			if($rID1 &&  $rID2){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="save_update_delete_dtls_job_level")
{

	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_booking_no);
		 disconnect($con);die;
	}
	$lock_another_process='';
	if(str_replace("'","",$txt_booking_no)!='')
	{
		$sql=sql_select("SELECT b.job_no_mst from subcon_ord_mst a join subcon_ord_dtls b on a.id=b.mst_id where b.order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.job_no_mst");
		foreach($sql as $row){
			$lock_another_process=$row[csf('job_no_mst')];
		}
		if($lock_another_process!=''){
			echo "lockAnotherProcess**".$lock_another_process;
			 disconnect($con);die;
		}
	}
	$strdata=json_decode(str_replace("'","",$strdata));
	/*echo '10**<pre>';
	print_r($strdata); die;*/
	if ($operation==0){
		$gmtArr=array();
		$poArr=array();
		$pre_emb_id_arr=array();
		for ($i=1;$i<=$total_row;$i++){
			$txtembcostid="txtembcostid_".$i;
			$txtpoid="txtpoid_".$i;
		    $txtgmtitemid='txtgmtitemid_'.$i;
		    $cbocolorsizesensitive="cbocolorsizesensitive_".$i;

			$txtembcostid=str_replace("'","",$$txtembcostid);
			$poid=str_replace("'","",$$txtpoid);
			$gmtItem=str_replace("'","",$$txtgmtitemid);
			$colorsizesensitive=str_replace("'","",$$cbocolorsizesensitive);
			$pre_emb_id_arr[$pretrimcostid]=$txtembcostid;
			$poArr[$poid]=$poid;					
			$gmtArr[$gmtItem]=$gmtItem;
			$poid_Arr = explode(',', $poid);
			foreach ($poid_Arr as $id) {
				$poidArr[$id] = $id;
				$senArr[$id]=$colorsizesensitive;	
			}
		}

		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "gmt_item in(".implode(",",$gmtArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_emb_id_arr).") and booking_type=6 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0") == 1)
		{

			echo "11**0";
		 	disconnect($con);die;
		}
		$sensitivity_check=sql_select("SELECT sensitivity,po_break_down_id from wo_booking_dtls where po_break_down_id in (".implode(",",$poArr).") and status_active=1 and is_deleted=0 and booking_type=6 and is_short=2 group by sensitivity,po_break_down_id");
		foreach ($sensitivity_check as $row) {
			$sen_chk_arr[$row[csf('po_break_down_id')]] = $row[csf('sensitivity')];
		}
		if(count($sen_chk_arr)>0)
		{
			foreach ($poidArr as $poId) {
				if($sen_chk_arr[$poId] != $senArr[$poId]){
					echo "samesen";
					 disconnect($con);die;
				}
			}
		}
		
		//echo "10**".__LINE__; die;

		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;

		$field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no,gmt_item, booking_no, booking_type, is_short, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date,country_id_string, inserted_by, insert_date";

		$field_array2="id,wo_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, pcs,color_size_table_id,article_number";

		$add_comma=0;
		$id1=return_next_id( "id", "wo_emb_book_con_dtls", 1 );
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++){
			$txtembcostid="txtembcostid_".$i;
			$txtgmtitemid='txtgmtitemid_'.$i;
			$txtpoid="txtpoid_".$i;
			$txtuom="txtuom_".$i;
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtwoq="txtwoq_".$i;
			$txtexchrate="txtexchrate_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtddate="txtddate_".$i;
			$consbreckdown="consbreckdown_".$i;
			$txtbookingid="txtbookingid_".$i;
			$txtcountry="txtcountry_".$i;
			$txtjob_id="txtjob_".$i;
			$txtreqqnty="txtreqqnty_".$i;
			$jsondata="jsondata_".$i;
			$txtreqamount="txtreqamount_".$i;

			$jsonarr=json_decode(str_replace("'","",$$jsondata));
			$uom_id=str_replace("'","",$$txtuom);
			$job=str_replace("'","",$$txtjob_id);
			$embcostid=str_replace("'","",$$txtembcostid);
			$gmtitemid=str_replace("'","",$$txtgmtitemid);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);
			$amt=str_replace("'","",$$txtamount); 
			$exRate=str_replace("'","",$$txtexchrate);
			foreach($strdata->$job->$embcostid->$gmtitemid->po_id as $poId){
				$wqQty=($strdata->$job->$embcostid->$gmtitemid->req_qnty->$poId/$reqqnty)*$woq;
				$amount=$wqQty*$rate;
				$amount=number_format($amount,4,'.','');
				$data_array1 ="(".$id_dtls.",".$$txtembcostid.",".$poId.",".$$txtjob_id.",".$$txtgmtitemid.",".$txt_booking_no.",6,2,".$$txtuom.",".$$cbocolorsizesensitive.",".$wqQty.",".$$txtexchrate.",".$$txtrate.",".$amount.",".$$txtddate.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//echo "10** insert into wo_booking_dtls (".$field_array1.") values ".$data_array1; die;
				$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
				//	CONS break down===============================================================================================
				if(str_replace("'",'',$$consbreckdown) !=''){
					$rID_de1=execute_query( "delete from wo_emb_book_con_dtls where  wo_booking_dtls_id =".$$txtbookingid."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					/*echo '10**<pre>';
					print_r($consbreckdown_array); die;*/
					$d=0;
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						if(str_replace("'","",$consbreckdownarr[3])!="")
						{
							if (!in_array(str_replace("'","",$consbreckdownarr[3]),$new_array_color)){
								$color_id = return_id( str_replace("'","",$consbreckdownarr[3]), $color_library, "lib_color", "id,color_name","403");
								$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[3]);
							}
							else $color_id =  array_search(str_replace("'","",$consbreckdownarr[3]), $new_array_color);
						}
						else $color_id =0;

						$gmc=$consbreckdownarr[0];
						$gms=$consbreckdownarr[1];
						$art=$consbreckdownarr[13];
						$bwqQty=0;
						if(str_replace("'","",$$cbocolorsizesensitive)==1 || str_replace("'","",$$cbocolorsizesensitive)==3){
							//echo "10**".$jsonarr->$embcostid->$gmc->req_qty->$poId.'--'.$consbreckdownarr[12].'--'.$consbreckdownarr[5]; die;
							$total_balance=0;
							$po_wise_balance_qty_arr = explode(',', $consbreckdownarr[14]);
							foreach ($po_wise_balance_qty_arr as $data) {
								$po_colow_qty_arr = explode('-', $data);
								$poidcolorqtyarr[$po_colow_qty_arr[0]]=$po_colow_qty_arr[1];
								$total_balance += $po_colow_qty_arr[1];
							}
							$bQty=($jsonarr->$embcostid->$gmc->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[5];
							//$bwqQty=($jsonarr->$embcostid->$gmc->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[7];
							$bwqQty=($poidcolorqtyarr[$poId]/$total_balance)*$consbreckdownarr[7];
							$order_qty=$jsonarr->$embcostid->$gmc->order_quantity->$poId;
							$colorSizeTableId=$jsonarr->$embcostid->$gmc->color_size_table_id->$poId;
							
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==2){
							$bQty=($jsonarr->$embcostid->$gms->$art->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[5];
							$bwqQty=($jsonarr->$embcostid->$gms->$art->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[7];
							$order_qty=$jsonarr->$embcostid->$gms->$art->order_quantity->$poId;
							$colorSizeTableId=$jsonarr->$embcostid->$gms->$art->color_size_table_id->$poId;
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==4){
							//echo "10**".__LINE__; die;
							$total_balance=0;
							$po_wise_balance_qty_arr = explode(',', $consbreckdownarr[14]);
							foreach ($po_wise_balance_qty_arr as $data) {
								$po_colow_qty_arr = explode('-', $data);
								$poidcolorqtyarr[$po_colow_qty_arr[0]]=$po_colow_qty_arr[1];
								$total_balance += $po_colow_qty_arr[1];
							}
							$bwqQty=($poidcolorqtyarr[$poId]/$total_balance)*$consbreckdownarr[7];
							$bQty=($jsonarr->$embcostid->$gmc->$gms->$art->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[5];
							//$bwqQty=($jsonarr->$embcostid->$gmc->$gms->$art->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[7];
							$order_qty=$jsonarr->$embcostid->$gmc->$gms->$art->order_quantity->$poId;
							$colorSizeTableId=$jsonarr->$embcostid->$gmc->$gms->$art->color_size_table_id->$poId;
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==0){
							$bQty=($jsonarr->$embcostid->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[5];
							$bwqQty=($jsonarr->$embcostid->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[7];
							$order_qty=$jsonarr->$embcostid->order_quantity->$poId;
							$colorSizeTableId=$jsonarr->$embcostid->color_size_table_id->$poId;
						}
						$bamount=$bwqQty*$consbreckdownarr[8];
						if($colorSizeTableId != '')
						{
							//echo "10**".__LINE__; die;
							if ($d!=0){
								$data_array2 .=",";
							}
							$bamount=number_format($bamount,4,'.','');
							$data_array2 ="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$$txtjob_id.",".$poId.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$color_id."','".$consbreckdownarr[4]."','".$bQty."','".$consbreckdownarr[6]."','".$bwqQty."','".$consbreckdownarr[8]."','".$bamount."','".$order_qty."','".$colorSizeTableId."','".$consbreckdownarr[13]."')";
							$data_insert[$id1] = $data_array2;
							$id1=$id1+1;
							$add_comma++;
							$d++;
							$rID2=sql_insert("wo_emb_book_con_dtls",$field_array2,$data_array2,0);	
						}
											
					}
				}//CONS break down end				
				$id_dtls=$id_dtls+1;
			}			
		}
		if($db_type==0)
		{
			if($rID1 && $rID2){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "**10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}

	else if ($operation==1){
		$gmtArr=array();
		$poArr=array();
		$pre_emb_id_arr=array();
		$booking_dtls_id_arr=array();
		for ($i=1;$i<=$total_row;$i++){
			$txtembcostid="txtembcostid_".$i;
			$txtpoid="txtpoid_".$i;
		    $txtgmtitemid='txtgmtitemid_'.$i;
			$txtbookingid="txtbookingid_".$i;

			$txtembcostid=str_replace("'","",$$txtembcostid);
			$poid=str_replace("'","",$$txtpoid);
			$gmtItem=str_replace("'","",$$txtgmtitemid);
			$bookingdtlsid=str_replace("'","",$$txtbookingid);

			$pre_emb_id_arr[$pretrimcostid]=$txtembcostid;
			$poArr[$poid]=$poid;
			$gmtArr[$gmtItem]=$gmtItem;
			$booking_dtls_id_arr[$bookingdtlsid]=$bookingdtlsid;

		}

		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**1";
			 disconnect($con);die;
		}
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "gmt_item in(".implode(",",$gmtArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_emb_id_arr).") and id not in (".implode(",",$booking_dtls_id_arr).") and booking_type=6 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0") == 1)
		{
			check_table_status( $_SESSION['menu_id'],0);
			echo "11**0";
			 disconnect($con);
			 die;
		}
		 $field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*gmt_item*booking_no*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*country_id_string*updated_by*update_date";

		$field_array_up2="id,wo_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id,article_number";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_emb_book_con_dtls", 1 );
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++){
		    $txtembcostid="txtembcostid_".$i;
			$txtgmtitemid='txtgmtitemid_'.$i;
			$txtpoid="txtpoid_".$i;
			$txtuom="txtuom_".$i;
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtwoq="txtwoq_".$i;
			$txtexchrate="txtexchrate_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtddate="txtddate_".$i;
			$consbreckdown="consbreckdown_".$i;
			$txtbookingid="txtbookingid_".$i;
			$txtcountry="txtcountry_".$i;
			$txtjob_id="txtjob_".$i;
			$txtreqqnty="txtreqqnty_".$i;
			$jsondata="jsondata_".$i;
			$txtreqamount="txtreqamount_".$i;

			$jsonarr=json_decode(str_replace("'","",$$jsondata));
			$uom_id=str_replace("'","",$$txtuom);
			$job=str_replace("'","",$$txtjob_id);
			$embcostid=str_replace("'","",$$txtembcostid);
			$gmtitemid=str_replace("'","",$$txtgmtitemid);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);
			$amt=str_replace("'","",$$txtamount);
			$exRate=str_replace("'","",$$txtexchrate);

			if(str_replace("'",'',$$txtbookingid)!=""){
				foreach($strdata->$job->$embcostid->$gmtitemid->po_id as $poId){
					$wqQty=($strdata->$job->$embcostid->$gmtitemid->req_qnty->$poId/$reqqnty)*$woq;
					$amount=$wqQty*$rate;
					$amount=number_format($amount,4,'.','');
					//check_table_status( $_SESSION['menu_id'],0);
					//echo "10**=".$wqQty.'='.$rate;die;
					$id_arr=array();
					$data_array_up1=array();
					$id_arr[]=str_replace("'",'',$strdata->$job->$embcostid->$gmtitemid->booking_id->$poId);
					$data_array_up1[str_replace("'",'',$strdata->$job->$embcostid->$gmtitemid->booking_id->$poId)] =explode("*",("".$$txtembcostid."*".$poId."*".$$txtjob_id."*".$gmtitemid."*".$txt_booking_no."*".$$txtuom."*".$$cbocolorsizesensitive."*".$wqQty."*".$$txtexchrate."*".$$txtrate."*".$amount."*".$$txtddate."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					//echo "10**".bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr );die;
					if($data_array_up1 !=""){
						$rID1=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
					}
					//	CONS break down===============================================================================================
					$rID2=1;
					if(str_replace("'",'',$$consbreckdown) !=''){
						$rID_de1=execute_query( "delete from wo_emb_book_con_dtls where  wo_booking_dtls_id =".$strdata->$job->$embcostid->$gmtitemid->booking_id->$poId."",0);
						$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
						$d=0;
						for($c=0;$c < count($consbreckdown_array);$c++){
							$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
							if(str_replace("'","",$consbreckdownarr[3])!="")
							{
								if (!in_array(str_replace("'","",$consbreckdownarr[3]),$new_array_color)){
									$color_id = return_id( str_replace("'","",$consbreckdownarr[3]), $color_library, "lib_color", "id,color_name","403");
									$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[3]);
								}
								else $color_id =  array_search(str_replace("'","",$consbreckdownarr[3]), $new_array_color);
							}
							else $color_id =0;
							$gmc=$consbreckdownarr[0];
							$gms=$consbreckdownarr[1];
							$art=$consbreckdownarr[13];

							if(str_replace("'","",$$cbocolorsizesensitive)==1 || str_replace("'","",$$cbocolorsizesensitive)==3){
								$total_balance=0;
								$po_wise_balance_qty_arr = explode(',', $consbreckdownarr[14]);
								foreach ($po_wise_balance_qty_arr as $data) {
									$po_colow_qty_arr = explode('-', $data);
									$poidcolorqtyarr[$po_colow_qty_arr[0]]=$po_colow_qty_arr[1];
									$total_balance += $po_colow_qty_arr[1];
								}
								$bQty=($jsonarr->$embcostid->$gmc->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[5];
								$bwqQty=($poidcolorqtyarr[$poId]/$total_balance)*$consbreckdownarr[7];
								$order_qty=$jsonarr->$embcostid->$gmc->order_quantity->$poId;
								$colorSizeTableId=$jsonarr->$embcostid->$gmc->color_size_table_id->$poId;
								
								//echo "10**=".$bwqQty."=".$consbreckdownarr[14];die;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==2){
								$bQty=($jsonarr->$embcostid->$gms->$art->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[5];
								$bwqQty=($jsonarr->$embcostid->$gms->$art->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[7];
								$order_qty=$jsonarr->$embcostid->$gms->$art->order_quantity->$poId;
								$colorSizeTableId=$jsonarr->$embcostid->$gms->$art->color_size_table_id->$poId;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==4){
								$total_balance=0;
								$po_wise_balance_qty_arr = explode(',', $consbreckdownarr[14]);
								foreach ($po_wise_balance_qty_arr as $data) {
									$po_colow_qty_arr = explode('-', $data);
									$poidcolorqtyarr[$po_colow_qty_arr[0]]=$po_colow_qty_arr[1];
									$total_balance += $po_colow_qty_arr[1];
								}
								$bwqQty=($poidcolorqtyarr[$poId]/$total_balance)*$consbreckdownarr[7];
								$bQty=($jsonarr->$embcostid->$gmc->$gms->$art->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[5];
								//$bwqQty=($jsonarr->$embcostid->$gmc->$gms->$art->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[7];
								$order_qty=$jsonarr->$embcostid->$gmc->$gms->$art->order_quantity->$poId;
								$colorSizeTableId=$jsonarr->$embcostid->$gmc->$gms->$art->color_size_table_id->$poId;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==0){
								$bQty=($jsonarr->$embcostid->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[5];
								$bwqQty=($jsonarr->$embcostid->req_qty->$poId/$consbreckdownarr[12])*$consbreckdownarr[7];
								$order_qty=$jsonarr->$embcostid->order_quantity->$poId;
								$colorSizeTableId=$jsonarr->$embcostid->color_size_table_id->$poId;
							}

							$bamount=$bwqQty*$consbreckdownarr[8];
							$bamount=number_format($bamount,4,'.','');
							if ($d!=0) $data_array2 .=",";
							$data_array2 ="(".$id1.",".$strdata->$job->$embcostid->$gmtitemid->booking_id->$poId.",".$txt_booking_no.",".$$txtjob_id.",".$poId.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$color_id."','".$consbreckdownarr[4]."','".$bQty."','".$consbreckdownarr[6]."','".$bwqQty."','".$consbreckdownarr[8]."','".$bamount."','".$order_qty."','".$colorSizeTableId."','".$consbreckdownarr[13]."')";
							$id1=$id1+1;
							$add_comma++;
							$d++;
									check_table_status( $_SESSION['menu_id'],0);

							//echo "10**insert into wo_emb_book_con_dtls (".$field_array_up2.") values ".$data_array2;die;
							$rID2=sql_insert("wo_emb_book_con_dtls",$field_array_up2,$data_array2,0);
						}
					}//CONS break down end==============================================================================================
				}
			}
		}
		$rID=execute_query( "update wo_booking_mst set revised_no=revised_no+1 where  booking_no=$txt_booking_no",0);

		check_table_status( $_SESSION['menu_id'],0);
		//echo "10**".$rID1 ."&&". $rID2;
		if($db_type==0){
			if($rID1 && $rID2){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}

		if($db_type==2 || $db_type==1 ){
			if($rID1 && $rID2){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}


	else if ($operation==2){

		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		for ($i=1;$i<=$total_row;$i++){
			$txtpoid="txtpoid_".$i;
			$txtbookingid="txtbookingid_".$i;
			$delete_cause=str_replace("'","",$delete_cause);
			$delete_cause=str_replace('"','',$delete_cause);
			$delete_cause=str_replace('(','',$delete_cause);
			$delete_cause=str_replace(')','',$delete_cause);
			$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  id in (".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
		    $rID2=execute_query( "update wo_emb_book_con_dtls set status_active=0,is_deleted=1 where  wo_booking_dtls_id in(".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
		}

		if($db_type==0){
			if($rID1 &&  $rID2){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}

		if($db_type==2 || $db_type==1 ){
			if($rID1 &&  $rID2){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="show_trim_booking")
{
	extract($_REQUEST);

	if($garments_nature==0){
		$garment_nature_cond="";
	}
	else{
		$garment_nature_cond=" and a.garments_nature=$garments_nature";
	}



	$condition= new condition();
	if(str_replace("'","",$job_no) !=''){
		$condition->job_no("in('$job_no')");
	}
	$condition->init();
	$emblishment= new emblishment($condition);

    $req_qty_arr=$emblishment->getQtyArray_by_orderEmblishmentidAndGmtsitem();
    $req_amount_arr=$emblishment->getAmountArray_by_orderEmblishmentidAndGmtsitem();

	$wash= new wash($condition);
	$req_qty_arr_wash=$wash->getQtyArray_by_orderEmblishmentidAndGmtsitem();
    $req_amount_arr_wash=$wash->getAmountArray_by_orderEmblishmentidAndGmtsitem();
	//$costPerArr=$condition->getCostingPerArr();
		


	$cu_booking_arr=array();

	$sql_cu_booking=sql_select("select a.order_uom,c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id and c.booking_type=6 and c.status_active=1 and c.is_deleted=0 and c.id not in($booking_id) group by c.job_no,a.order_uom, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item");
	
	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('gmt_item')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('gmt_item')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_amount')];
		$job_uom_arr[csf('job_no')]=$row_cu_booking[csf('order_uom')];
	}
	//print_r($job_uom_arr);
	unset($sql_cu_booking);

	$sql="select a.job_no_prefix_num,a.order_uom, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as pre_cost_emb_cost_dtls_id, c.emb_name, c.emb_type, c.body_part_id, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id,e.item_number_id, avg(e.requirment) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date, f.gmt_item

	from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_pre_cos_emb_co_avg_con_dtls e, wo_booking_dtls f

	where
	a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.pre_cost_emb_cost_dtls_id and d.id=e.po_break_down_id and e.pre_cost_emb_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and e.item_number_id=f.gmt_item and f.booking_type=6 and f.booking_no=$txt_booking_no and f.id in($booking_id) and a.company_name=$cbo_company_name $garment_nature_cond and e.pre_cost_emb_cost_dtls_id=$pre_cost_id and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by
	a.job_no_prefix_num, a.order_uom,a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.emb_name, c.emb_type, c.body_part_id, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id,e.item_number_id, f.id, f.sensitivity, f.delivery_date, f.gmt_item
	order by d.id,c.id";
	//echo $sql; die;
	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $infr)
	{
		$cbo_currency_job=$infr[csf('currency_id')];
		$exchange_rate=$infr[csf('exchange_rate')];
		$job_uom_arr[$infr[csf('job_no')]]=$infr[csf('costing_per')];
		$job_uom_id_arr[$infr[csf('job_no')]]=$infr[csf('order_uom')];
		
		if($cbo_currency==$cbo_currency_job){
			$exchange_rate=1;
		}

		$pre_cost_emb_id=$infr[csf('pre_cost_emb_cost_dtls_id')];

		if($infr[csf('emb_name')]==3)
		{
			$req_qnty_cons_uom=$req_qty_arr_wash[$infr[csf('po_id')]][$infr[csf('pre_cost_emb_cost_dtls_id')]][$infr[csf('item_number_id')]];
			$req_amount_cons_uom=$req_amount_arr_wash[$infr[csf('po_id')]][$infr[csf('pre_cost_emb_cost_dtls_id')]][$infr[csf('item_number_id')]];
		}else{
			$req_qnty_cons_uom=$req_qty_arr[$infr[csf('po_id')]][$infr[csf('pre_cost_emb_cost_dtls_id')]][$infr[csf('item_number_id')]];
			$req_amount_cons_uom=$req_amount_arr[$infr[csf('po_id')]][$infr[csf('pre_cost_emb_cost_dtls_id')]][$infr[csf('item_number_id')]];
		}
		$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

		$cu_woq=$cu_booking_arr[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['cu_woq'][$infr[csf('po_id')]];
		$cu_amount=$cu_booking_arr[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['cu_amount'][$infr[csf('po_id')]];

		$bal_woq=def_number_format($req_qnty_cons_uom-$cu_woq,5,"");
		$amount=def_number_format($rate_cons_uom*$bal_woq,5,"");

		if($infr[csf('emb_name')]==1)
		{
			$emb_type_name=$emblishment_print_type[$infr[csf('emb_type')]];
		}
		if($infr[csf('emb_name')]==2)
		{
			$emb_type_name=$emblishment_embroy_type[$infr[csf('emb_type')]];
		}
		if($infr[csf('emb_name')]==3)
		{
			$emb_type_name=$emblishment_wash_type[$infr[csf('emb_type')]];
		}
		if($infr[csf('emb_name')]==4)
		{
			$emb_type_name=$emblishment_spwork_type[$infr[csf('emb_type')]];
		}
		if($infr[csf('emb_name')]==5)
		{
			$emb_type_name=$emblishment_gmts_type[$infr[csf('emb_type')]];
		}


		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['job_no'][$infr[csf('po_id')]]=$infr[csf('job_no')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['po_id'][$infr[csf('po_id')]]=$infr[csf('po_id')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['po_number'][$infr[csf('po_id')]]=$infr[csf('po_number')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['item_number_id'][$infr[csf('po_id')]]=$infr[csf('item_number_id')];

		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['country'][$infr[csf('po_id')]]=$infr[csf('country')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['body_part_id'][$infr[csf('po_id')]]=$infr[csf('body_part_id')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['body_part'][$infr[csf('po_id')]]=$body_part[$infr[csf('body_part_id')]];

		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]][$infr[csf('item_number_id')]]['emb_type'][$infr[csf('po_id')]]=$infr[csf('emb_type')];
        $job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['emb_type_name'][$infr[csf('po_id')]]=$emb_type_name;

		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['emb_name'][$infr[csf('po_id')]]=$infr[csf('emb_name')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['emb_name_name'][$infr[csf('po_id')]]=$emblishment_name_array[$infr[csf('emb_name')]];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['pre_cost_emb_cost_dtls_id'][$infr[csf('po_id')]]=$pre_cost_emb_id;


		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['req_qnty'][$infr[csf('po_id')]]=$req_qnty_cons_uom;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['req_amount'][$infr[csf('po_id')]]=$req_amount_cons_uom;



		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['cu_woq'][$infr[csf('po_id')]]=$cu_woq;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['cu_amount'][$infr[csf('po_id')]]=$cu_amount;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['bal_woq'][$infr[csf('po_id')]]=$bal_woq;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['exchange_rate'][$infr[csf('po_id')]]=$exchange_rate;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['rate'][$infr[csf('po_id')]]=$rate_cons_uom;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['amount'][$infr[csf('po_id')]]=$amount;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['txt_delivery_date'][$infr[csf('po_id')]]=$infr[csf('delivery_date')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['booking_id'][$infr[csf('po_id')]]=$infr[csf('booking_id')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['sensitivity'][$infr[csf('po_id')]]=$infr[csf('sensitivity')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['uom'][$infr[csf('po_id')]]=$infr[csf('costing_per')];
	}
	$sql_booking=sql_select("select c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id,c.gmt_item, sum(c.wo_qnty) as wo_qnty, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id  and c.id in($booking_id) and c.booking_type=6 and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id,c.gmt_item");
	foreach($sql_booking as $row_booking){
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('gmt_item')]]['woq'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('gmt_item')]]['amount'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('amount')];
	}
	?>

    <input type="hidden" id="strdata" value='<? echo json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="80">Job No</th>
            <th width="100">Ord. No</th>
             <th width="100">Gmt. Item</th>
            <th width="100">Emb Name</th>
            <th width="150">Body Part</th>
            <th width="150">Emb Type</th>
            <th width="70">Req. Qnty</th>
            <th width="50">UOM</th>
            <th width="80">CU WOQ</th>
            <th width="80">Bal WOQ</th>
            <th width="100">Sensitivity</th>
            <th width="80">WOQ</th>
            <th width="55">Exch.Rate</th>
            <th width="80">Rate</th>
            <th width="80">Amount</th>
            <th>Delv. Date</th>
        </thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" id="tbl_list_search" >
        <tbody>
        <?
        if($cbo_level==1)
        {
            foreach ($nameArray as $selectResult)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                $cbo_currency_job=$selectResult[csf('currency_id')];
                $exchange_rate=$selectResult[csf('exchange_rate')];
                if($cbo_currency==$cbo_currency_job){
                    $exchange_rate=1;
                }

				$pre_cost_emb_id=$selectResult[csf('pre_cost_emb_cost_dtls_id')];
				if($selectResult[csf('emb_name')]==3)
				{
					$req_qnty_cons_uom=$req_qty_arr_wash[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
					$req_amount_cons_uom=$req_amount_arr_wash[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
				}else{
					$req_qnty_cons_uom=$req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
					$req_amount_cons_uom=$req_amount_arr[$selectResult[csf('po_id')]][$infr[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
				}
				$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;



                $cu_woq=$cu_booking_arr[$selectResult[csf('job_no')]][$pre_cost_emb_id][$selectResult[csf('item_number_id')]]['cu_woq'][$selectResult[csf('po_id')]];
                $cu_amount=$cu_booking_arr[$selectResult[csf('job_no')]][$pre_cost_emb_id][$selectResult[csf('item_number_id')]]['cu_amount'][$selectResult[csf('po_id')]];

                $bal_woq=def_number_format($req_qnty_cons_uom-$cu_woq,5,"");

                $woq=$job_and_trimgroup_level[$selectResult[csf('job_no')]][$pre_cost_emb_id][$selectResult[csf('item_number_id')]]['woq'][$selectResult[csf('po_id')]];
                $amount=$job_and_trimgroup_level[$selectResult[csf('job_no')]][$pre_cost_emb_id][$selectResult[csf('item_number_id')]]['amount'][$selectResult[csf('po_id')]];
                $rate=$amount/$woq;
                $total_amount+=$amount;
				if($selectResult[csf('emb_name')]==1)
				{
				$emb_type_name=$emblishment_print_type[$selectResult[csf('emb_type')]];
				}
				if($selectResult[csf('emb_name')]==2)
				{
				$emb_type_name=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
				}
				if($selectResult[csf('emb_name')]==3)
				{
				$emb_type_name=$emblishment_wash_type[$selectResult[csf('emb_type')]];
				}
				if($selectResult[csf('emb_name')]==4)
				{
				$emb_type_name=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
				}
				if($selectResult[csf('emb_name')]==5)
				{
				$emb_type_name=$emblishment_gmts_type[$selectResult[csf('emb_type')]];
				}
				if($selectResult[csf('emb_name')]==99)
				{
				$emb_type_name=$emblishment_other_type_arr[$selectResult[csf('emb_type')]];
				}
				//$costPerQty=$costPerArr[$selectResult[csf('job_no')]];

                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="80"><? echo $selectResult[csf('job_no')];?>
                        <input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $selectResult[csf('job_no')];?>" style="width:30px" class="text_boxes" readonly/>
                    </td>
                    <td width="100"><? echo $selectResult[csf('po_number')];?>
                        <input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $selectResult[csf('booking_id')];?>" readonly/>
                        <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
                        <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $selectResult[csf('country')] ?>" readonly />
                    </td>
                    <td width="100">
					<? echo $garments_item[$selectResult[csf('item_number_id')]];?>
                    <input type="hidden" id="txtgmtitemid_<? echo $i;?>" value="<? echo $selectResult[csf('item_number_id')];?>" readonly/>
                    </td>
                    <td width="100">
					<? echo $emblishment_name_array[$selectResult[csf('emb_name')]];?>
                    <input type="hidden" id="txtembcostid_<? echo $i;?>" value="<? echo $selectResult[csf('pre_cost_emb_cost_dtls_id')];?>" readonly/>
                    <input type="hidden" id="emb_name_<? echo $i;?>" value="<? echo $selectResult[csf('emb_name')];?>" readonly/>
                    </td>
                    <td width="150">
                    <? echo $body_part[$selectResult[csf('body_part_id')]]; ?>
                    <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="body_part_id_<? echo $i;?>"  value="<? echo $selectResult[csf('body_part_id')]; ?>"  />
                    </td>
                    <td width="150">
                    <?  echo $emb_type_name;?>
                    <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="emb_type_<? echo $i;?>"  value="<? echo $selectResult[csf('emb_type')];?>"  />
                    </td>
                    <td width="70" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_cons_uom,4,'.','');?>"  readonly  />

                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                    </td>
                    <td width="50">
                    
                        <? 
						$uom_id=0;
						if($costing_per==2)
						{
							$costing_per_dzn='Pcs';
							$uom_id=1;
						}
						else{
							$costing_per_dzn='Dzn';
							$uom_id=2;
						}
						//$uom_id=$job_uom_id_arr[$selectResult[csf('job_no')]];
						echo $unit_of_measurement[$uom_id];?>
                        <input type="hidden" id="txtuom_<? echo $i;?>" value="<?= $uom_id ?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($cu_amount,4,'.','');?>"  readonly  />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.',''); ?>"  readonly  />
                    </td>
                    <td width="100" align="right"><? echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $selectResult[csf("sensitivity")], "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)",1,"1,3,4" ); ?>
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($woq,4,'.','');?>" onClick="open_consumption_popup('requires/print_booking_multijob_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly />
                    </td>
                    <td width="55" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_cons_uom;?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
                    </td>
                    <td width="" align="right">
                        <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?>"  readonly <? if($disAbled){echo "disabled";}else{ echo "";}?>  />
                        <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                        <input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
                    </td>
                </tr>
            <?
            $i++;
            }
        }
        if($cbo_level==2)
        {
            $i=1;
            foreach ($job_and_trimgroup_level as $job_no)
            {
				foreach ($job_no as $wo_pre_cost_trim_cost_dtlsArr)
                {
                foreach ($wo_pre_cost_trim_cost_dtlsArr as $gmtItem=>$wo_pre_cost_trim_cost_dtls)
                {
                    $job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
					$costing_per=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
					$po_number=implode(",",$wo_pre_cost_trim_cost_dtls['po_number']);
                    $po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
					$item_number_id=$gmtItem;//implode(",",$wo_pre_cost_trim_cost_dtls['item_number_id']);
                    $country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));

                    $body_part_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['body_part_id']));
					$body_part=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['body_part']));
					$emb_type=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['emb_type']));
					$emb_type_name=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['emb_type_name']));

                    $pre_cost_emb_cost_dtls_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['pre_cost_emb_cost_dtls_id']));
					$emb_name = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['emb_name']));
					$emb_name_name = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['emb_name_name']));

                    //$uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
                    $uom=$job_uom_arr[$job_no];
                    $booking_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['booking_id']));
                    $sensitivity=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['sensitivity']));
                    $delivery_date=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['txt_delivery_date']));

                    $req_qnty_cons_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
					$rate_cons_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount'])/array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
					$req_amount_cons_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount']);


                    $bal_woq=array_sum($wo_pre_cost_trim_cost_dtls['bal_woq']);
                    $cu_woq=array_sum($wo_pre_cost_trim_cost_dtls['cu_woq']);
                    $cu_amount=array_sum($wo_pre_cost_trim_cost_dtls['cu_amount']);

                    $woq=array_sum($wo_pre_cost_trim_cost_dtls['woq']);
                    $amount=array_sum($wo_pre_cost_trim_cost_dtls['amount']);
                    $rate=$amount/$woq;
                    $total_amount+=$amount;


                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                        <td width="40"><? echo $i;?></td>
                        <td width="80"><? echo $job_no?><input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>" style="width:30px" class="text_boxes" readonly/></td>
                        <td width="100" style="word-wrap:break-word;word-break: break-all"><? echo $po_number; ?>
                            <input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $booking_id; ?>" readonly/>
                            <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_id; ?>" readonly/>
                            <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $country; ?>" readonly />
                        </td>
                         <td width="100">
						<? echo $garments_item[$item_number_id];?>
                        <input type="hidden" id="txtgmtitemid_<? echo $i;?>" value="<? echo $item_number_id;?>" readonly/>
                        </td>
                        <td width="100">
                        <? echo $emb_name_name;?>
                        <input type="hidden" id="txtembcostid_<? echo $i;?>" value="<? echo $pre_cost_emb_cost_dtls_id;?>" readonly/>
                        <input type="hidden" id="emb_name_<? echo $i;?>" value="<? echo $emb_name;?>" readonly/>
                        </td>
                        <td width="150">
                         <? echo $body_part; ?>
                        <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="body_part_id_<? echo $i;?>"  value="<? echo $body_part_id; ?>"  />
                        </td>
                        <td width="150">
                        <? echo $emb_type_name;?>
                        <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="emb_type_<? echo $i;?>"  value="<? echo $emb_type;?>"  />
                        </td>
                        <td width="70" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_cons_uom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                        </td>
                        <td width="50">
                        
						<?
						$uom_id=0;
						$costing_per_dzn='';
						if($costing_per==2)
						{
							$costing_per_dzn='Pcs';
							$uom_id=1;
						}
						else{
							$costing_per_dzn='Dzn';
							$uom_id=2;
						}
						//$uom_id=$job_uom_id_arr[$job_no];
						//echo $costing_per_dzn;
						echo $unit_of_measurement[$uom_id];
						?>
                        <input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $uom_id;?>" readonly /></td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%; height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.',''); ?>"  readonly  />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo $cu_amount;?>"  readonly  />
                        </td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.','');?>"  readonly  />
                        </td>
                        <td width="100" align="right"><?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $sensitivity, "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)",1,"1,3,4" ); ?>
                        </td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($woq,4,'.','');?>" onClick="open_consumption_popup('requires/print_booking_multijob_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly />
                        </td>
                        <td width="55" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />
                        </td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_cons_uom;?>" readonly />
                        </td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
                        </td>
                        <td width="" align="right">
                            <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>"  readonly <? if($disAbled){echo "disabled";}else{ echo "";}?>   />
                            <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                            <input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
                        </td>
                    </tr>
                    <?
                    $i++;
                }
			    }
            }
        }
        ?>
        </tbody>
	</table>
	<table width="1500" class="rpt_table" border="0" rules="all">
        <tfoot>
            <tr>
                <th width="40">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="70"><? echo $tot_req_qty; ?></th>
                <th width="50">&nbsp;</th>
                <th width="80"><? echo $tot_cu_woq; ?></th>
                <th width="80"><? echo $tot_bal_woq; ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="55">&nbsp;</th>
                <th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/></th>
                <th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/></th>
                <th><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
            </tr>
        </tfoot>
	</table>
    <table width="1100" colspan="14" cellspacing="0" class="" border="0">
        <tr>
            <td align="center"class="button_container">
            	<? echo load_submit_buttons( $permission, "fnc_trims_booking_dtls", 1,0,"reset_form('','booking_list_view','','','')",2); ?>
            </td>
        </tr>
    </table>
	<?
	exit();
}

if ($action=="show_trim_booking_list")
{
	extract($_REQUEST);

	if($garments_nature==0){
		$garment_nature_cond="";
	}
	else{
		$garment_nature_cond=" and a.garments_nature=$garments_nature";
	}

	$sql="select a.job_no_prefix_num, a.order_uom,a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_embe_cost_dtls, c.emb_name, c.body_part_id , c.emb_type, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.requirment) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity,f.gmt_item, f.delivery_date, f.description as description
	from
	wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_pre_cos_emb_co_avg_con_dtls e, wo_booking_dtls f

	where
	a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.pre_cost_emb_cost_dtls_id and d.id=e.po_break_down_id and e.pre_cost_emb_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=6 and f.booking_no=$txt_booking_no and a.company_name=$cbo_company_name   $garment_nature_cond and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by a.job_no_prefix_num,a.order_uom, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.emb_name, c.body_part_id, c.emb_type, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity,f.gmt_item, f.delivery_date, f.description
	order by d.id, c.id";
	// echo $sql; die;
	
	$job_and_trimgroup_level=array();
	$i=1;
	
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $selectResult){
	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	$job_uom_id_arr[$selectResult[csf('job_no')]]=$selectResult[csf('order_uom')];
	if($cbo_currency==$cbo_currency_job){
		$exchange_rate=1;
	}
		if($selectResult[csf('emb_name')]==1)
		{
			$emb_type_name=$emblishment_print_type[$selectResult[csf('emb_type')]];
		}
		if($selectResult[csf('emb_name')]==2)
		{
			$emb_type_name=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
		}
		if($selectResult[csf('emb_name')]==3)
		{
			$emb_type_name=$emblishment_wash_type[$selectResult[csf('emb_type')]];
		}
		if($selectResult[csf('emb_name')]==4)
		{
			$emb_type_name=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
		}
		if($selectResult[csf('emb_name')]==5)
		{
			$emb_type_name=$emblishment_gmts_type[$selectResult[csf('emb_type')]];
		}
		if($selectResult[csf('emb_name')]==99)
		{
			$emb_type_name=$emblishment_other_type_arr[$selectResult[csf('emb_type')]];
		}
	$job_costing_per[$selectResult[csf('job_no')]]=$selectResult[csf('costing_per')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['job_no'][$selectResult[csf('po_id')]]=$selectResult[csf('job_no')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['costing_per'][$selectResult[csf('po_id')]]=$selectResult[csf('costing_per')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['order_uom'][$selectResult[csf('po_id')]]=$unit_of_measurement[$selectResult[csf('order_uom')]];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['costing_per'][$selectResult[csf('po_id')]]=$costPerArr[$selectResult[csf('job_no')]];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['po_id'][$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['po_number'][$selectResult[csf('po_id')]]=$selectResult[csf('po_number')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['country'][$selectResult[csf('po_id')]]=$selectResult[csf('country')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['gmt_item'][$selectResult[csf('po_id')]]=$selectResult[csf('gmt_item')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['gmt_item_name'][$selectResult[csf('po_id')]]=$garments_item[$selectResult[csf('gmt_item')]];


	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['emb_name'][$selectResult[csf('po_id')]]=$selectResult[csf('emb_name')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['emb_name_name'][$selectResult[csf('po_id')]]=$emblishment_name_array[$selectResult[csf('emb_name')]];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['emb_type'][$selectResult[csf('po_id')]]=$selectResult[csf('emb_type')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['emb_type_name'][$selectResult[csf('po_id')]]=$emb_type_name;

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['body_part_id'][$selectResult[csf('po_id')]]=$selectResult[csf('body_part_id')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['body_part_name'][$selectResult[csf('po_id')]]=$body_part[$selectResult[csf('body_part_id')]];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['wo_pre_cost_embe_cost_dtls'][$selectResult[csf('po_id')]]=$selectResult[csf('wo_pre_cost_embe_cost_dtls')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['cu_woq'][$selectResult[csf('po_id')]]=$cu_woq;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['cu_amount'][$selectResult[csf('po_id')]]=$cu_amount;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['bal_woq'][$selectResult[csf('po_id')]]=$bal_woq;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['exchange_rate'][$selectResult[csf('po_id')]]=$exchange_rate;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['rate'][$selectResult[csf('po_id')]]=$rate_ord_uom;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['txt_delivery_date'][$selectResult[csf('po_id')]]=$selectResult[csf('delivery_date')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['booking_id'][$selectResult[csf('po_id')]]=$selectResult[csf('booking_id')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['sensitivity'][$selectResult[csf('po_id')]]=$selectResult[csf('sensitivity')];
	}

	$sql_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.sensitivity, c.gmt_item,c.brand_supplier,c.wo_qnty as wo_qnty, c.amount as amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.booking_type=6 and c.status_active=1 and c.is_deleted=0");
	foreach($sql_booking as $row_booking){
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('gmt_item')]]['woq'][$row_booking[csf('po_break_down_id')]]+=$row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('gmt_item')]]['amount'][$row_booking[csf('po_break_down_id')]]+=$row_booking[csf('amount')];

		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('gmt_item')]]['gmt_item'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('gmt_item')];
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table">
	<thead>
	<th width="40">SL</th>
	<th width="100">Job No</th>
	<th width="100">Ord. No</th>
    <th width="100">Gmts. Item</th>
	<th width="100">Emb Name</th>
    <th width="150">Emb Type</th>
    <th width="150">Body Part</th>
	<th width="80">UOM</th>
	<th width="100">Sensitivity</th>
	<th width="80">WOQ</th>
	<th width="80">Exch.Rate</th>
	<th width="80">Rate</th>
	<th width="80">Amount</th>
	<th width="">Delv. Date</th>
	</thead>
	<tbody id="save_list">
	<?
if($cbo_level==1){
	foreach ($nameArray as $selectResult){
	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	$costing_per=$selectResult[csf('costing_per')];
	if($cbo_currency==$cbo_currency_job){
		$exchange_rate=1;
	}
	if($selectResult[csf('emb_name')]==1)
	{
		$emb_type_name=$emblishment_print_type[$selectResult[csf('emb_type')]];
	}
	if($selectResult[csf('emb_name')]==2)
	{
		$emb_type_name=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
	}
	if($selectResult[csf('emb_name')]==3)
	{
		$emb_type_name=$emblishment_wash_type[$selectResult[csf('emb_type')]];
	}
	if($selectResult[csf('emb_name')]==4)
	{
		$emb_type_name=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
	}
	if($selectResult[csf('emb_name')]==5)
	{
		$emb_type_name=$emblishment_gmts_type[$selectResult[csf('emb_type')]];
	}
	if($selectResult[csf('emb_name')]==99)
	{
		$emb_type_name=$emblishment_other_type_arr[$selectResult[csf('emb_type')]];
	}
	$woq=def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['woq'][$selectResult[csf('po_id')]],5,"");
	$amount=def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['amount'][$selectResult[csf('po_id')]],5,"");
	$rate=def_number_format($amount/$woq,5,"");
	$total_amount+=$amount;

	//$costing_per_dzn=$unit_of_measurement[$costing_per];
	if($costing_per==1)
	{
		$costing_per_dzn="Dzn";
	}
	else if($costing_per==2)
	{
		$costing_per_dzn="Pcs";
	}
	else if($costing_per==3)
	{
		$costing_per_dzn="Dzn";
	}
	else if($costing_per==4)
	{
		$costing_per_dzn="Dzn";
	}
	else if($costing_per==5)
	{
		$costing_per_dzn="Dzn";
	}
	 
						 
						if($costing_per==2)
						{
							$costing_per_dzn='Pcs';
							$uom_id=1;
						}
						else{
							$costing_per_dzn='Dzn';
							$uom_id=2;
						}
						
//$uom_id=$job_uom_id_arr[$selectResult[csf('job_no')]];
	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="fnc_show_booking(<? echo $selectResult[csf('wo_pre_cost_embe_cost_dtls')];?>,'<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('booking_id')];?>','<? echo $selectResult[csf('job_no')];?>')">
	<td width="40"><? echo $i;?></td>
	<td width="100">
	<? echo $selectResult[csf('job_no')];?>
	</td>
	<td width="100">
	<? echo $selectResult[csf('po_number')];?>
	</td>
    <td width="100">
	<? echo $garments_item[$selectResult[csf('gmt_item')]];?>
	</td>
	<td width="100">
	<? echo $emblishment_name_array[$selectResult[csf('emb_name')]];?>
	</td>
	<td width="150" >
	<? echo $emb_type_name;?>
	</td>
    <td width="150" >
	<? echo $body_part[$selectResult[csf('body_part_id')]];?>
	</td>
	<td width="80">
   
	<? 
	//echo $costing_per_dzn;
	 echo $unit_of_measurement[$uom_id];?>
	</td>
	<td width="100" align="right">
    <? echo $size_color_sensitive[$selectResult[csf("sensitivity")]];?>
	</td>
	<td width="80" align="right">
	<? echo number_format($woq,0,'.','');$tot_woq+=$woq;?>
	</td>
	<td width="80" align="right">
    <? echo $exchange_rate;?>
	</td>
	<td width="80" align="right">
    <? echo number_format($rate,4,'.','');?>
	</td>
	<td width="80" align="right">
    <? echo number_format($amount,4,'.','');$tot_amount+=$amount;?>
	</td>
	<td width="" align="right">
    <? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?>
	</td>
	</tr>
	<?
	$i++;
	}
}

if($cbo_level==2){
	$i=1;
	foreach ($job_and_trimgroup_level as $job_no){
	foreach ($job_no as $sen){
	foreach ($sen as $gmtItemId){
	//foreach ($desc as $brandsup){
	foreach ($gmtItemId as $wo_pre_cost_trim_cost_dtls){
	$job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
	$order_uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['order_uom']));
	//$costing_per=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['costing_per']));
	$costing_per=$job_costing_per[$job_no];
	//echo $costing_per.'d';
	$po_number=implode(",",$wo_pre_cost_trim_cost_dtls['po_number']);
	$po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
	//$gmtItemName=$garments_item[implode(",",$wo_pre_cost_trim_cost_dtls['gmt_item'])];
	$gmtItemName=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['gmt_item_name']))));

	$country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
	$wo_pre_cost_emb_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_embe_cost_dtls']));
	$emb_name_id = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['emb_name']));
	$emb_name_name = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['emb_name_name']));
	$emb_type_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['emb_type']));
	$embTypeName=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['emb_type_name']));
	$body_part_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['body_part_id']));
	$body_part_name=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['body_part_name']));


	//$uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
	$booking_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['booking_id']));
	$sensitivity=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['sensitivity']));
	$delivery_date=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['txt_delivery_date']));
	$woq=def_number_format(array_sum($wo_pre_cost_trim_cost_dtls['woq']),5,"");
	$amount=def_number_format(array_sum($wo_pre_cost_trim_cost_dtls['amount']),5,"");
	$rate=def_number_format($amount/$woq,5,"");
	$total_amount+=$amount;
	if($costing_per==1)
	{
		$costing_per_dzn="Dzn";
	}
	else if($costing_per==2)
	{
		$costing_per_dzn="Pcs";
	}
	else if($costing_per==3)
	{
		$costing_per_dzn="Dzn";
	}
	else if($costing_per==4)
	{
		$costing_per_dzn="Dzn";
	}
	else if($costing_per==5)
	{
		$costing_per_dzn="Dzn";
	}
	//$costing_per_dzn=$costing_per[$costing_per];
$uom_id=$job_uom_id_arr[$job_no];
	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="fnc_show_booking(<? echo $wo_pre_cost_emb_id;?>,'<? echo $po_id; ?>','<? echo $booking_id; ?>','<? echo $job_no; ?>')">
	<td width="40"><? echo $i;?></td>
	<td width="100">
	<? echo $job_no?>
	</td>
	<td width="100" style="word-wrap:break-word;word-break: break-all">
	<? echo $po_number; ?>
	</td>
     <td width="100">
	<? echo $gmtItemName;?>
	</td>
	<td width="100">
	<? echo $emb_name_name;?>
	</td>
	<td width="150" >
	<? echo $embTypeName;?>
	</td>
    <td width="150" >
	<? echo $body_part_name;?>
	</td>
	<td width="80">
    
	<?  echo $unit_of_measurement[$uom_id];?>
	</td>
	<td width="100" align="right">
    <? echo $size_color_sensitive[$sensitivity];?>
	</td>
	<td width="80" align="right">
	<? echo number_format($woq,0,'.','');$tot_woq+=$woq;?>
	</td>
	<td width="80" align="right">
    <? echo $exchange_rate;?>
	</td>
	<td width="80" align="right">
    <? echo number_format($rate,4,'.','');?>
	</td>
	<td width="80" align="right">
    <? echo number_format($amount,4,'.','');$tot_amount+=$amount;?>
	</td>
	<td width="" align="right">
    <? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>
	</td>
	</tr>
	<?
	$i++;
	}
	//}
	}
	}
	}
}
	?>
	</tbody>
	<tfoot>
			<tr>
				<td colspan="9" align="right"><b>Grand Total</b></td>
				<td width="80" align="right"><? echo number_format($tot_woq, 4, '.', ''); ?></td>
				<td width="80" align="right">&nbsp;</td>
				<td width="80" align="right">&nbsp;</td>
				<td width="80" align="right"><? echo number_format($tot_amount, 4, '.', ''); ?></td>
				<td width="" align="right">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<?
	exit();
}

if ($action=="fabric_booking_popup") 
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>

	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
	function check_orphan( str )
	{
		if($("#chk_orphan").prop('checked')==true)
			$('#chk_orphan').val(1);
		else
			$('#chk_orphan').val(0);
	}
    </script>
</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="1060"cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th colspan="12"><?=create_drop_down( "cbo_search_category", 110, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
            </thead>
            <thead>
                <th width="150" class="must_entry_caption">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="100">Brand</th>
                <th width="100">Season</th>
                <th width="60">Season Year</th>
                
                <th width="80">Booking No</th>
                <th width="80">Job No</th>
                <th width="90">Style Ref</th>
                <th width="80">M.Style / Int. Ref. No</th>
                <th width="130" colspan="2">Date Range</th>
                <th> <input type="checkbox" id="chk_orphan" onClick="check_orphan(this.value)" value="0">Orphan WO</th>
            </thead>
            <tr class="general">
                <td> <input type="hidden" id="selected_booking">
                <?=create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name, "load_drop_down( 'print_booking_multijob_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?></td>
                <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 140, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $cbo_buyer_name, "load_drop_down( 'print_booking_multijob_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'print_booking_multijob_controller', this.value, 'load_drop_down_season', 'season_td');" ); ?></td>
                 <td id="brand_td"><?=create_drop_down( "cbo_brand_id", 100, $blank_array,"", 1, "-- All Brand --", $selected, "",0,"" ); ?></td>
                 <td id="season_td"><?=create_drop_down( "cbo_season_id", 100, $blank_array,"", 1,"-Season-", 1, "",0,"" );?></td>
                 <td><?=create_drop_down( "cbo_season_year", 60, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:70px"></td>

                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"></td>
                <td>
                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('chk_orphan').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_season_year').value+'_'+document.getElementById('txt_style').value, 'create_booking_search_list_view', 'search_div', 'print_booking_multijob_controller','setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                </td>
            </tr>
            <tr>
                <td colspan="11" align="center" valign="middle"><?=load_month_buttons(1); ?></td>
            </tr>
     	</table>
     <div id="search_div"></div>
    </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="create_booking_search_list_view")
{
	//echo $data;
	$data=explode('_',$data);
	//print_r($data);
	if($data[0]==0 && $data[1]==0)
    {
        echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
        die;
    }
    $style_cond=" ";
    if (trim($data[13])!="") $style_cond=" and e.style_ref_no like '%$data[13]%'";
	if ($data[0]!=0) $company="  and a.company_id='$data[0]'"; else $company="";
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0)
	{
		$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($data[6]==1)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]' "; else $booking_cond="";
	}
	else if($data[6]==2)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
	}
	$brand_id=$data[10];
	$season_id=$data[11];
	$season_year_id=$data[12];
	if($brand_id>0) $brand_cond="and e.brand_id in($brand_id)";else  $brand_cond="";
	if($season_id>0) $season_cond="and e.season_buyer_wise in($season_id)"; else  $season_cond="";
	//echo $season_cond.'system'.$data[7];die;
	if($season_year_id>0) $season_year_cond="and e.season_year=$season_year_id";else  $season_year_cond="";
	
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$po_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
	$po_array=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
    $season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");

	if(trim($data[9])!="") $ref_cond=" and d.grouping like '%$data[9]'"; else $ref_cond="";
	if(trim($data[8])!="") $job_cond=" and b.job_no like '%$data[8]%'"; else $job_cond="";
	$year_cond='';
	if($db_type==0) $year_cond=" and YEAR(a.insert_date)=".$data[4];
	else if($db_type==2 || $db_type==0) $year_cond=" and to_char(a.insert_date,'YYYY')=".$data[4];
	
	//$arr=array (2=>$buyer_arr,3=>$brand_arr,4=>$season_arr,5=>$po_num,7=>$po_array,8=>$garments_item,9=>$emblishment_name_array,10=>$suplier,11=>$approved,12=>$is_ready);
	//$arr=array (2=>$buyer_arr,3=>$brand_arr,4=>$season_arr,5=>$year,6=>$po_num,8=>$po_array,9=>$garments_item,10=>$emblishment_name_array,11=>$suplier,12=>$approved,13=>$is_ready);
	$arr=array (2=>$buyer_arr,3=>$brand_arr,4=>$season_arr,5=>$year,6=>$po_num,9=>$po_array,10=>$garments_item,11=>$emblishment_name_array,12=>$suplier,13=>$approved,14=>$is_ready);
	if($data[7]==0)
		$sql= "SELECT a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,d.grouping,e.brand_id,e.season_buyer_wise,e.season_year,a.pay_mode, e.style_ref_no, 
			CASE
	            WHEN a.pay_mode not IN(3,5) 
	            then (select  p.short_name from lib_supplier p where A.SUPPLIER_ID=p.id )
	            else  (select q.company_short_name from lib_company q where A.SUPPLIER_ID=q.id )
	        end as supplier 
        from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c,wo_po_break_down d,wo_po_details_master e where  e.id=d.job_id and b.job_no = e.job_no and  b.is_deleted=0 $company $buyer $booking_date $booking_cond and d.job_no_mst=b.job_no and d.id=b.po_break_down_id  and  b.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.booking_type=6 and a.entry_form=574 and  a.status_active=1  and 	a.is_deleted=0 and  b.status_active=1  $job_cond $ref_cond $brand_cond $season_cond $season_year_cond $year_cond $style_cond group by a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,d.grouping,e.brand_id,e.season_buyer_wise,e.season_year,a.pay_mode, e.style_ref_no order by a.id DESC";
	else
		$sql= "SELECT a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.supplier_id,a.is_approved,a.ready_to_approved,'' as po_break_down_id,'' as gmt_item,'' as grouping ,a.pay_mode, '' as style_ref_no, 
				CASE
		            WHEN a.pay_mode not IN(3,5) 
		            then (select  p.short_name from lib_supplier p where A.SUPPLIER_ID=p.id )
		            else  (select q.company_short_name from lib_company q where A.SUPPLIER_ID=q.id )
		        end as supplier
			 from wo_booking_mst a where a.is_deleted=0  and  a.booking_no not in ( select a.booking_no from  wo_booking_mst a join wo_booking_dtls b on a.booking_no=b.booking_no where a.booking_type=6 and a.entry_form=574 and  a.status_active =1 and a.is_deleted=0  and  b.status_active =1 and b.is_deleted=0 $company $buyer $booking_date $booking_cond $job_cond group by a.booking_no)  $company $buyer $booking_date $booking_cond  $year_cond  and a.booking_type=6 and a.entry_form=574  and  a.status_active=1  $job_cond group by a.id,a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode order by a.id DESC";
	 //echo $sql;
	 echo  create_list_view("list_view", "Booking No,Booking Date,Buyer,Brand,Season,Season Year,Job No.,Style Ref,M.Style / Int. Ref. No,PO No.,Gmts Item,Embl Name,Supplier,Approved,Is-Ready", "50,65,70,70,60,50,50,100,100,100,80,100,50","1110","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,buyer_id,brand_id,season_buyer_wise,season_year,job_no,0,0,po_break_down_id,gmt_item,emb_name,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,buyer_id,brand_id,season_buyer_wise,season_year,job_no,style_ref_no,grouping,po_break_down_id,gmt_item,emb_name,supplier,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0,0,0,0,0,0','','');
	 exit();
}

if($action=="check_pi_exist_or_not")
{
	$data=explode("_",$data);

	$result=sql_select("select c.pi_number from wo_booking_mst a, com_pi_item_details b ,com_pi_master_details c where a.status_active=1 and b.status_active=1 and c.status_active=1 and a.id=b.work_order_id and c.id=b.pi_id and a.booking_no='$data[0]'");
	
	if(count($result))
	{
		echo $result[0][csf('pi_number')];
		exit();
	}
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	 $sql= "select id,booking_no, booking_date, company_id, buyer_id, currency_id, exchange_rate, pay_mode, booking_month, is_short, supplier_id, attention, delivery_date, source, booking_year, is_approved, ready_to_approved, cbo_level, is_short, remarks, supplier_location_id, floor_id from wo_booking_mst where booking_no='$data' and  status_active=1 and is_deleted=0";

	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );

		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_isshort').value = '".$row[csf("is_short")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('hidden_supplier_id').value = '".$row[csf("supplier_id")]."';\n";
		
		if($row[csf("pay_mode")]!=3 && $row[csf("pay_mode")]!=5)
		{
			echo "document.getElementById('cbo_supplier_name').value = '".$supplier_library[$row[csf("supplier_id")]]."';\n";
		}
		else
		{
			echo "document.getElementById('cbo_supplier_name').value = '".$company_library[$row[csf("supplier_id")]]."';\n";
		}

		echo "load_drop_down( 'requires/print_booking_multijob_controller', document.getElementById('hidden_supplier_id').value, 'load_drop_down_party_location', 'location_td' );\n";
		echo "load_drop_down( 'requires/print_booking_multijob_controller', '".$row[csf("pay_mode")].'__'.$row[csf("supplier_id")].'__'.$row[csf("supplier_location_id")]."', 'load_drop_down_floor', 'floor_td' );\n";

		echo "document.getElementById('cbo_party_location').value = '".$row[csf("supplier_location_id")]."';\n";
		echo "document.getElementById('cbo_floor_id').value = '".$row[csf("floor_id")]."';\n";

		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";
		echo "document.getElementById('remarks').value = '".$row[csf("remarks")]."';\n";

		echo " $('#cbo_company_name').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		echo " $('#cbo_currency').attr('disabled',true);\n";
		echo " $('#cbo_level').attr('disabled',true);\n";
		echo " $('#cbo_buyer_name').attr('disabled',true);\n";
		echo "fnc_show_booking_list();\n";
		if($row[csf("is_approved")]==1)
		{
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		}
		else
		{
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
	 }
}

if($action=="show_trim_booking_report2")
{
	extract($_REQUEST);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$id_approved_id=str_replace("'","",$id_approved_id);
	$report_type=str_replace("'","",$report_type);
	$show_comment=str_replace("'","",$show_comment);
	$cbo_template_id=str_replace("'","",$cbo_template_id);

	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$floorArr=return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;
	$booking_grand_total=0;
	$currency_id="";
	$buyer_string=array();
	$style_owner=array();
	$job_no=array();
	$style_ref=array();
	$all_dealing_marcent=array();
	$season=array();
	$order_repeat_no=array();
	$po_id_arr=array();
	$file_no_arr=array();

	$nameArray_buyer=sql_select( "select  a.style_ref_no, a.order_uom,a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant, a.season, a.season_matrix, a.season_buyer_wise, a.order_repeat_no, b.po_break_down_id,c.costing_per, d.file_no from wo_po_details_master a, wo_booking_dtls b,wo_pre_cost_mst c,wo_po_break_down d where a.job_no=b.job_no and a.job_no=c.job_no and b.job_no=c.job_no and a.job_no=d.job_no_mst and  b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");
	foreach ($nameArray_buyer as $result_buy){
		$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
		$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
		$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
		$job_costing_per_arr[$result_buy[csf('job_no')]]=$result_buy[csf('costing_per')];
		$job_uom_arr[$result_buy[csf('job_no')]]=$unit_of_measurement[$result_buy[csf('order_uom')]];
		$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];

		$file_no_arr[$result_buy[csf('job_no')]]=$result_buy[csf('file_no')];

		$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
		$season_matrix=$result_buy[csf('season_matrix')];
		$season_buyer_wise=$result_buy[csf('season_buyer_wise')];
		if($season_matrix!=0 && $season_buyer_wise==0 )
		{
			$season_matrix_con=$season_matrix;
		}
		else if($season_buyer_wise!=0 && $season_matrix==0)
		{
			$season_matrix_con=$season_buyer_wise;
		}
		$seasons_name.=$season_arr[$season_matrix_con].',';
		$order_rept_no.=$result_buy[csf('order_repeat_no')].',';
		$order_repeat_no[$result_buy[csf('order_repeat_no')]]=$result_buy[csf('order_repeat_no')];

		$po_id_arr[$result_buy[csf('po_break_down_id')]]=$result_buy[csf('po_break_down_id')];
	}
	$style_sting=implode(",",array_unique($style_ref));
	$job_no=implode(",",$job_no);
	$seasons_names=rtrim($seasons_name,',');

	$seasons_names=implode(",",array_unique(explode(",",$seasons_names)));
	$poid_arr=array_unique($po_id_arr);

	$order_rept_no=rtrim($order_rept_no,',');
	$order_rept_no=implode(",",array_unique(explode(",",$order_rept_no)));

	$po_no=array();
	$file_no=array();
	$ref_no=array();
	$po_quantity=array();
	$pub_shipment_date='';
	$int_ref_no='';
	$tot_po_quantity=0;
	$po_idss='';
	$nameArray_job=sql_select( "select b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
	foreach ($nameArray_job as $result_job){
		$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
		$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
		$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
		$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		$job_ref_no[$result_job[csf('job_no_mst')]].=$result_job[csf('grouping')].',';
		$po_no_arr[$result_job[csf('job_no_mst')]]['po_id'].=$result_job[csf('id')].',';
		$pub_shipment_date.=$result_job[csf('pub_shipment_date')].',';
		$int_ref_no.=$result_job[csf('grouping')].',';
		if($po_idss=='') $po_idss=$result_job[csf('id')];else $po_idss.=",".$result_job[csf('id')];

	}
	$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
	foreach ($sql_job as $row)
	{
		$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]]+=$row[csf('po_quantity')];
		$tot_po_quantity+=$row[csf('po_quantity')];
	}

	$nameArray=sql_select( "select a.booking_no, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no, a.floor_id from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
	foreach( $nameArray as $row)
	{
		$varcode_booking_no=$row[csf('booking_no')];
		$booking_date=$row[csf('booking_date')];
		$delivery_date=$row[csf('delivery_date')];
		$pay_mode_id=$row[csf('pay_mode')];
		$supplier_id=$row[csf('supplier_id')];
		$currency_id=$row[csf('currency_id')];
		$buyer_id=$row[csf('buyer_id')];
		$exchange_rate=$row[csf('exchange_rate')];
		$attention=$row[csf('attention')];
		$remarks=$row[csf('remarks')];
		$revised_no=$row[csf('revised_no')];
		$source_id=$row[csf('source')];
		if($pay_mode_id==3 || $pay_mode_id==5) $floor_id=$row[csf('floor_id')]; else $floor_id=0;
	}
	?>
    <html>
    <div style="width:1333px" align="center">
    <table width="1333px" cellpadding="0" cellspacing="0" style="border:0px solid black" >
        <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
            <tr>
                <td width="20px">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
                        <tr>
                            <td width="50" >
                            <?
							if($report_type==1)
                            {
								if($link == 1)
								{
								?>
                                    <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
								<?
								}
								else
								{
								?>
                                    <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
								<?
								}
                            }
                            else
                            {
							?>
                                <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
                            <?
							}
                            ?>
                            </td>
                            <td width="40px" align="center">
                                &nbsp;  &nbsp;  &nbsp;
                            </td>
                            <td width="30px"   align="center">
                            <b style="font-size:25px;"> <?
                            echo $company_library[$cbo_company_name]; ?>
                            </b>
                            <br>
                            <label>
                            <?
                            $nameArray=sql_select( "select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result){
                            ?>
                            <?  if($result[csf('plot_no')]!='') echo $result[csf('plot_no')]; else echo '';?> &nbsp;
                            <? echo $result[csf('level_no')];?> &nbsp;
                            <? echo $result[csf('road_no')]; ?>  &nbsp;
                            <? echo $result[csf('block_no')];?>  &nbsp;
                            <? echo $result[csf('city')];?>  &nbsp;
                            <? echo $result[csf('zip_code')]; ?>  &nbsp;
                            <?php echo $result[csf('province')]; ?>  &nbsp;
                            <? echo $country_arr[$result[csf('country_id')]]; ?> &nbsp;<br/>
                            <? echo $result[csf('email')];?>  &nbsp;
                            <? echo $result[csf('website')];
                            if($result[csf('plot_no')]!='')
                            {
                            $plot_no=$result[csf('plot_no')];
                            }
                            if($result[csf('level_no')]!='')
                            {
                            $level_no=$result[csf('level_no')];
                            }
                            if($result[csf('road_no')]!='')
                            {
                            $road_no=$result[csf('road_no')];
                            }
                            if($result[csf('block_no')]!='')
                            {
                            $block_no=$result[csf('block_no')];
                            }
                            if($result[csf('city')]!='')
                            {
                            $city=$result[csf('city')];
                            }
                            //$company_address[$result[csf('id')]]=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
                            }
                            ?>
                            </label>
                            <br/>
                            <b style="font-size:20px;">
                            <?php echo $report_title; ?>
                            </b>
                            </td>
                            <td width="10px" align="center" style="font-size:20px;">
                                <table width="80%" align="right" cellpadding="0" cellspacing="0" style="border:0px solid black">
                                    <tr>
                                        <td width="80">  Booking No:&nbsp; <?php echo $varcode_booking_no; ?>  </td>
                                    </tr>
                                    <tr>
                                        <td>  Booking Date:&nbsp; <?php echo change_date_format($booking_date); ?>  </td>
                                    </tr>
                                    <?
                                    if($revised_no>0)
                                    {
                                    ?>
                                    <tr>
                                        <td>  Revised No:&nbsp; <?php echo $revised_no; ?>  </td>
                                    </tr>
                                    <?
                                    }
                                    if(str_replace("'","",$id_approved_id) ==1)
                                    {
                                    ?>
                                    <tr>
                                        <td>Approved Status :&nbsp;  <? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </td>
                                    </tr>
                                    <?
                                    }
                                    ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?
        $nameArray=sql_select( "select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$supplier_id");
        foreach ($nameArray as $result){
            if($result[csf('plot_no')]!='')
            {
            $plot_no=$result[csf('plot_no')];
            }
            if($result[csf('level_no')]!='')
            {
            $level_no=$result[csf('level_no')];
            }
            if($result[csf('road_no')]!='')
            {
            $road_no=$result[csf('road_no')];
            }
            if($result[csf('block_no')]!='')
            {
            $block_no=$result[csf('block_no')];
            }
            if($result[csf('city')]!='')
            {
            $city=$result[csf('city')];
            }
        	$company_address[$result[csf('id')]]=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
        }
        ?>

        <table width="100%" style="border:0px solid black;table-layout: fixed;">
            <tr>
            <td colspan="6" valign="top"></td>
            </tr>
            <tr>
            <td width="100" style="font-size:18px"><span><b>To, </b></span>  </td>
            <td width="110" colspan="5" style="font-size:18px">&nbsp;<span></span></td>
            </tr>
            <tr>
            <td width="210" colspan="2" style="font-size:18px">&nbsp; <b>
            <?
            if($pay_mode_id==5 || $pay_mode_id==3){
            echo $company_library[$supplier_id];
            }
            else{
            echo $supplier_name_arr[$supplier_id];
            }
            ?></b>
            </td>
            <td  width="100" style="font-size:12px"><b>Buyer.</b></td>
            <td  width="110" >:&nbsp;<? echo $buyer_name_arr[$buyer_id]; ?></td>
            <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
            <td width="110">:&nbsp;<?  echo change_date_format($delivery_date); ?></td>
            </tr>
            <tr>
            <td width="110" colspan="2" rowspan="2" style="font-size:18px">Address :&nbsp;
            <?
            if($pay_mode_id==5 || $pay_mode_id==3){
            $address=$company_address[$supplier_id];
            }
            else{
            $address=$supplier_address_arr[$supplier_id];
            }
            echo $address;
            ?>
            </td>
            <td width="100" style="font-size:12px"><b>PO Qty.</b>   </td>
            <td width="110">:&nbsp;<? echo $tot_po_quantity; ?></td>
            <td width="100" style="font-size:12px"><b>Season</b> </td>
            <td width="110">:&nbsp;<? echo $seasons_names; ?></td>
            </td>
            </tr>
            <tr>
            <td width="100" style="font-size:12px"><b>Currency</b></td>
            <td width="110">:&nbsp;<?  echo $currency[$currency_id]; ?></td>
            <td width="100" style="font-size:12px"><b>Order Repeat </b> </td>
            <td width="110">:&nbsp;<? echo $order_rept_no; ?></td>
            </tr>
            <tr>
            <td style="font-size:12px" ><b>Attention </b>   </td>
            <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
            <?
            echo $attention;
            ?>
            </td>
            <td style="font-size:12px"><b>Dealing Merchant</b></td>
            <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
            <?
            echo implode(",",array_unique($all_dealing_marcent));
            ?>
            </td>
            <td  style="font-size:12px"><b>Pay mode</b></td>
            <td>:&nbsp;<? echo $pay_mode[$pay_mode_id];?></td>
            </tr>
            <tr>
            	<td style="font-size:12px"><b>Floor/Unit</b></td>
                <td>:&nbsp;<?=$floorArr[$floor_id];?></td>
                <td style="font-size:12px"><b>Source</b></td>
                <td>:&nbsp;<? echo $source[$source_id];?></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Remarks</b>  </td>
                <td width="110" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="5">:&nbsp;<? echo $remarks; ?></td>
            </tr>
        </table>
    <!--==============================================AS PER GMTS COLOR START=========================================  -->
    <?
   /* $precost_arr=array();
    $trims_qtyPerUnit_arr=array();
    $precost_sql=sql_select("select a.id, a.job_no,a.gmt_item,a.calculatorstring,a.remark, c.cal_parameter from wo_pre_cost_trim_cost_dtls a,wo_booking_dtls b, lib_item_group c where a.job_no=b.job_no and a.gmt_item=b.gmt_item and a.gmt_item=c.id and b.booking_no='$txt_booking_no' and a.id=b.pre_cost_fabric_cost_dtls_id and  b.status_active =1 and b.is_deleted=0");
    $calUom="";
    foreach($precost_sql as $precost_row){
    if($precost_row[csf('cal_parameter')]==1){
    $calUom="Mtr";
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
    }
    else if($precost_row[csf('cal_parameter')]==2){
    $calUom="Pcs";
    }
    else if($precost_row[csf('cal_parameter')]==3){
    $calUom="Pcs";
    }
    else if($precost_row[csf('cal_parameter')]==4){
    $calUom="Pcs";
    }
    else if($precost_row[csf('cal_parameter')]==5){
    $calUom="Yds";
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
    }
    else if($precost_row[csf('cal_parameter')]==6){
    $calUom="Yds";
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
    }
    else if($precost_row[csf('cal_parameter')]==7){
    $calUom="Pcs";
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
    }
    else if($precost_row[csf('cal_parameter')]==8){
    $calUom="Yds";
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
    }
    else{
    $calUom=0;
    }
    $trims_remark_arr[$precost_row[csf('id')]]['remark']=$precost_row[csf('remark')];
    }*/

    $booking_country_arr=array();
    $nameArray_booking_country=sql_select( "select pre_cost_fabric_cost_dtls_id,sensitivity,country_id_string from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0");
    foreach($nameArray_booking_country as $nameArray_booking_country_row){
		$country_id_string=explode(",",$nameArray_booking_country_row[csf('country_id_string')]);
		$tocu=count($country_id_string);
		for($cu=0;$cu<$tocu;$cu++){
			$booking_country_arr[$nameArray_booking_country_row[csf('pre_cost_fabric_cost_dtls_id')]][$nameArray_booking_country_row[csf('sensitivity')]][$country_id_string[$cu]]=$country_arr[$country_id_string[$cu]];
		}
    }

    $nameArray_job_po=sql_select( "select job_no from wo_booking_dtls  where booking_no='$txt_booking_no' and status_active =1 and is_deleted=0 group by job_no order by job_no ");
    foreach($nameArray_job_po as $nameArray_job_po_row){
		$nameArray_item=sql_select( "select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'   and a.sensitivity=1 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
		if(count($nameArray_item)>0){
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
			
			$costing_per=$job_costing_per_arr[$nameArray_job_po_row[csf('job_no')]];
			if($costing_per==1)
			{
			$costing_per_dzn="Dzn";
			}
			else if($costing_per==2)
			{
			$costing_per_dzn="Pcs";
			}
			else if($costing_per==3)
			{
			$costing_per_dzn="Dzn";
			}
			else if($costing_per==4)
			{
			$costing_per_dzn="Dzn";
			}
			else if($costing_per==5)
			{
			$costing_per_dzn="Dzn";
			}
    ?>
    &nbsp;
    <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
    <tr>
    <td colspan="10" align="">
    <table width="100%" style="table-layout: fixed;">
    <tr>
    <td width="60%" align="left"><strong>As Per Garments Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " "; echo "&nbsp; File NO:".$file_no_arr[$nameArray_job_po_row[csf('job_no')]]." &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
    <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
    </tr>
    </table>
    </td>
    </tr>
    <tr>
    <td style="border:1px solid black"><strong>Sl</strong> </td>
    <td style="border:1px solid black"><strong>Name/Country</strong> </td>
    <td style="border:1px solid black"><strong>Embl. Type</strong> </td>
    <td style="border:1px solid black"><strong>Gmts Item</strong> </td>
    <td style="border:1px solid black"><strong>Body Part</strong> </td>
     <td style="border:1px solid black"><strong>Description</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
    <td style="border:1px solid black" align="center"><strong>WO Qty(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Rate(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
    </tr>
    <?
    $i=0;
    $grand_total_as_per_gmts_color=0;
    foreach($nameArray_item as $result_item){
    $i++;
    $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and c.emb_name=".$result_item[csf('emb_name')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.color_size_table_id>0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_color,c.emb_name,c.emb_type,c.body_part_id order by bid ");

    ?>
    <tr>
    <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <? echo $i; ?>
    </td>
    <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <?
    echo $emblishment_name_array[$result_item[csf('emb_name')]]."<br/>";
    echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][1]);
    ?>
    </td>
    <?
    $item_desctiption_total=0;
    $total_amount_as_per_gmts_color=0;
    foreach($nameArray_item_description as $result_itemdescription){
	if($result_item[csf('emb_name')]==1)
	{
		$emb_type_name=$emblishment_print_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==2)
	{
		$emb_type_name=$emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==3)
	{
		$emb_type_name=$emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==4)
	{
		$emb_type_name=$emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==5)
	{
		$emb_type_name=$emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==99)
	{
		$emb_type_name=$emblishment_other_type_arr[$result_itemdescription[csf('emb_type')]];
	}
    ?>
    <td style="border:1px solid black">
	<? echo $emb_type_name; ?>
    </td>
    <td style="border:1px solid black">
	<? echo $garments_item[$result_itemdescription[csf('gmt_item')]];  ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <? echo $body_part[$result_itemdescription[csf('body_part_id')]] // ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <?
	if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
	echo $color_library[$result_itemdescription[csf('item_color')]];
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($result_itemdescription[csf('cons')],4);
    $item_desctiption_total += $result_itemdescription[csf('cons')] ;
    ?>
    </td>
    <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('amount')]/$result_itemdescription[csf('cons')],4); ?> </td>
    <td style="border:1px solid black; text-align:right">
    <?
    $amount_as_per_gmts_color = $result_itemdescription[csf('amount')];
    echo number_format($amount_as_per_gmts_color,4);
    $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
    <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
    echo number_format($item_desctiption_total ,4);
    ?></td>
    <td style="border:1px solid black; text-align:right"></td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($total_amount_as_per_gmts_color,2);
    $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
    <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
    </tr>
    </table>

    <?
    }
    ?>
    <!--==============================================AS PER GMTS COLOR END=========================================  -->


    <!--==============================================Size Sensitive START=========================================  -->
    <?
		$nameArray_item=sql_select( "select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'   and a.sensitivity=2 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
    if(count($nameArray_item)>0)
    {
		$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
		$po_ids=array_unique(explode(",",$po_ids));
		$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
		$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
		$po_no_qty=0;
		$job_no=$nameArray_job_po_row[csf('job_no')];
		foreach($po_ids as $poid)
		{
			$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
		}
		$costing_per=$job_costing_per_arr[$nameArray_job_po_row[csf('job_no')]];
		if($costing_per==1)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==2)
		{
		$costing_per_dzn="Pcs";
		}
		else if($costing_per==3)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==4)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==5)
		{
		$costing_per_dzn="Dzn";
		}
    ?>
    <br/>
    <table border="1"align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
    <tr>
    <td colspan="10" align="">
    <table width="100%" style="table-layout: fixed;">
    <tr>
    	
    <td width="60%"><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; echo "&nbsp;&nbsp;Int Ref.:".$ref_nos; echo "&nbsp; File NO:".$file_no_arr[$nameArray_job_po_row[csf('job_no')]]."&nbsp;&nbsp; Po Qty..:".$po_no_qty; ?></strong></td>
    <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;margin-left:210px; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
    </tr>
    </table>
    </td>
    </tr>
    <tr>
    <td style="border:1px solid black"><strong>Sl</strong> </td>
    <td style="border:1px solid black"><strong>Name/Country</strong> </td>
    <td style="border:1px solid black"><strong>Embl. Type</strong> </td>
    <td style="border:1px solid black"><strong>Gmts Item</strong> </td>
    <td style="border:1px solid black"><strong>Body Part</strong> </td>
     <td style="border:1px solid black"><strong>Description</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
    <td style="border:1px solid black" align="center"><strong>WO Qty(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Rate(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
    </tr>
    <?
    $i=0;
    $grand_total_as_per_gmts_color=0;
    foreach($nameArray_item as $result_item)
    {
    $i++;
   // $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,b.article_number,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_emb_book_con_dtls b where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.gmt_item=".$result_item[csf('gmt_item')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_size,b.article_number order by bid");
       $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=2  and c.emb_name=".$result_item[csf('emb_name')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_size,c.emb_name,c.emb_type,c.body_part_id order by bid ");

    ?>
    <tr>
    <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <? echo $i; ?>
    </td>
    <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <?
    echo $emblishment_name_array[$result_item[csf('emb_name')]]."<br/>";
    echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][2]);
    ?>
    </td>
    <?
    $item_desctiption_total=0;
    $total_amount_as_per_gmts_size=0;
    foreach($nameArray_item_description as $result_itemdescription){
	if($result_item[csf('emb_name')]==1)
	{
		$emb_type_name=$emblishment_print_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==2)
	{
		$emb_type_name=$emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==3)
	{
		$emb_type_name=$emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==4)
	{
		$emb_type_name=$emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==5)
	{
		$emb_type_name=$emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==99)
	{
		$emb_type_name=$emblishment_other_type_arr[$result_itemdescription[csf('emb_type')]];
	}
    ?>
    <td style="border:1px solid black">
	<? echo $emb_type_name; ?>
    </td>
    <td style="border:1px solid black">
	<? echo $garments_item[$result_itemdescription[csf('gmt_item')]];  ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <? echo $body_part[$result_itemdescription[csf('body_part_id')]] // ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <?
	if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
	echo $result_itemdescription[csf('item_size')];
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($result_itemdescription[csf('cons')],4);
    $item_desctiption_total += $result_itemdescription[csf('cons')] ;
    ?>
    </td>
    <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
    <td style="border:1px solid black; text-align:right">
    <?
    $amount_as_per_gmts_size = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
    echo number_format($amount_as_per_gmts_size,4);
    $total_amount_as_per_gmts_size+=$amount_as_per_gmts_size;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
    <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
    echo number_format($item_desctiption_total ,4);
    ?>ccc</td>
    <td style="border:1px solid black; text-align:right"></td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($total_amount_as_per_gmts_size,2);
    $grand_total_as_per_gmts_size+=$total_amount_as_per_gmts_size;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
    <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_size,2); $booking_grand_total+=$grand_total_as_per_gmts_size; ?></td>
    </tr>
    </table>
    <br/>
    <?
    }
    ?>

    <!--==============================================Size Sensitive END=========================================  -->

    <!--==============================================AS PER CONTRAST COLOR START=========================================  -->


    <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
    <?
    $nameArray_item=sql_select( "select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'   and a.sensitivity=3 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
		if(count($nameArray_item)>0){
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
		$costing_per=$job_costing_per_arr[$nameArray_job_po_row[csf('job_no')]];
		if($costing_per==1)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==2)
		{
		$costing_per_dzn="Pcs";
		}
		else if($costing_per==3)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==4)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==5)
		{
		$costing_per_dzn="Dzn";
		}
    ?>
    &nbsp;
    <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
    <tr>
    <td colspan="11" align="">
    <table width="100%" style="table-layout: fixed;">
    <tr>
    <td width="60%" align="left"><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " "; echo "&nbsp; File NO:".$file_no_arr[$nameArray_job_po_row[csf('job_no')]]." &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
    <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
    </tr>
    </table>
    </td>
    </tr>
    <tr>
    <td style="border:1px solid black"><strong>Sl</strong> </td>
    <td style="border:1px solid black"><strong>Name/Country</strong> </td>
    <td style="border:1px solid black"><strong>Embl. Type</strong> </td>
    <td style="border:1px solid black"><strong>Gmts Item</strong> </td>
    <td style="border:1px solid black"><strong>Body Part</strong> </td>
    <td style="border:1px solid black"><strong>Description</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
     <td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
    <td style="border:1px solid black" align="center"><strong>WO Qty(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Rate(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
    </tr>
    <?
    $i=0;
    $grand_total_as_per_gmts_color=0;
    foreach($nameArray_item as $result_item){
    $i++;
    $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=3  and c.emb_name=".$result_item[csf('emb_name')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_color,b.color_number_id,c.emb_name,c.emb_type,c.body_part_id order by bid ");
	

    ?>
    <tr>
    <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <? echo $i; ?>
    </td>
    <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <?
    echo $emblishment_name_array[$result_item[csf('emb_name')]]."<br/>";
    echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][3]);
    ?>
    </td>
    <?
    $item_desctiption_total=0;
    $total_amount_as_per_gmts_color=0;
    foreach($nameArray_item_description as $result_itemdescription){
	if($result_item[csf('emb_name')]==1)
	{
		$emb_type_name=$emblishment_print_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==2)
	{
		$emb_type_name=$emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==3)
	{
		$emb_type_name=$emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==4)
	{
		$emb_type_name=$emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==5)
	{
		$emb_type_name=$emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
	}if($result_item[csf('emb_name')]==99)
	{
		$emb_type_name=$emblishment_other_type_arr[$result_itemdescription[csf('emb_type')]];
	}
    ?>
    <td style="border:1px solid black">
	<? echo $emb_type_name; ?>
    </td>
    <td style="border:1px solid black">
	<? echo $garments_item[$result_itemdescription[csf('gmt_item')]];  ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <? echo $body_part[$result_itemdescription[csf('body_part_id')]] // ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <?
	if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
	echo $color_library[$result_itemdescription[csf('item_color')]];
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
	echo $color_library[$result_itemdescription[csf('color_number_id')]];
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($result_itemdescription[csf('cons')],4);
    $item_desctiption_total += $result_itemdescription[csf('cons')] ;
    ?>
    </td>
    <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
    <td style="border:1px solid black; text-align:right">
    <?
    $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
    echo number_format($amount_as_per_gmts_color,4);
    $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
    <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
    echo number_format($item_desctiption_total ,4);
    ?></td>
    <td style="border:1px solid black; text-align:right"></td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($total_amount_as_per_gmts_color,2);
    $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td align="right" style="border:1px solid black"  colspan="10"><strong>Total</strong></td>
    <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
    </tr>
    </table>

    <?
    }
    ?>
    <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
     <?
    $nameArray_item=sql_select( "select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'   and a.sensitivity=4 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
		if(count($nameArray_item)>0){
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
		$costing_per=$job_costing_per_arr[$nameArray_job_po_row[csf('job_no')]];
		if($costing_per==1)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==2)
		{
		$costing_per_dzn="Pcs";
		}
		else if($costing_per==3)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==4)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==5)
		{
		$costing_per_dzn="Dzn";
		}
    ?>
    &nbsp;
    <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
    <tr>
    <td colspan="13" align="">
    <table width="100%" style="table-layout: fixed;">
    <tr>
    <td width="60%" align="left"><strong>Color & Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " "; echo "&nbsp; File NO:".$file_no_arr[$nameArray_job_po_row[csf('job_no')]]." &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
    <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
    </tr>
    </table>
    </td>
    </tr>
    <tr>
    <td style="border:1px solid black"><strong>Sl</strong> </td>
    <td style="border:1px solid black"><strong>Name/Country</strong> </td>
    <td style="border:1px solid black"><strong>Embl. Type</strong> </td>
    <td style="border:1px solid black"><strong>Gmts Item</strong> </td>
    <td style="border:1px solid black"><strong>Body Part</strong> </td>
    <td style="border:1px solid black"><strong>Description</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
    <td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
    <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
    <td align="center" style="border:1px solid black"><strong>Gmts Size</strong></td>
    <td style="border:1px solid black" align="center"><strong>WO Qty(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Rate(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
    </tr>
    <?
    $i=0;
    $grand_total_as_per_gmts_color=0;
    foreach($nameArray_item as $result_item){
    $i++;
    $sqlBomEmb="select id, emb_name, emb_type, body_part_id from wo_pre_cost_embe_cost_dtls where id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and status_active=1 and is_deleted=0 ";
		$sqlBomEmbRes=sql_select($sqlBomEmb);
		$bomEmbArr=array();
		foreach($sqlBomEmbRes as $row)
		{
			$bomEmbArr[$row[csf('id')]]['emb']=$row[csf('emb_name')];
			$bomEmbArr[$row[csf('id')]]['embtype']=$row[csf('emb_type')];
			$bomEmbArr[$row[csf('id')]]['bodypart']=$row[csf('body_part_id')];
		}
		unset($sqlBomEmbRes);
		
		$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, a.gmt_item, min(b.id) as bid, b.description, b.item_color, b.color_number_id, b.item_size, b.gmts_sizes, sum(b.cons) as cons, avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_emb_book_con_dtls b where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=4 and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment!=0 and a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, a.gmt_item, b.description, b.item_color, b.color_number_id, b.item_size, b.gmts_sizes order by bid ");

		?>
		<tr>
            <td style="border:1px solid black" rowspan="<?=count($nameArray_item_description)+1; ?>"><?=$i; ?></td>
            <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
            <?
            echo $emblishment_name_array[$result_item[csf('pre_cost_fabric_cost_dtls_id')]['emb']]."<br/>";
            echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][4]);
            ?>
            </td>
            <?
            $item_desctiption_total=0;
            $total_amount_as_per_gmts_color=0;
				foreach($nameArray_item_description as $result_itemdescription){
					if($result_item[csf('pre_cost_fabric_cost_dtls_id')]['emb']==1)
					{
						$emb_type_name=$emblishment_print_type[$result_item[csf('pre_cost_fabric_cost_dtls_id')]['embtype']];
					}
					if($result_item[csf('pre_cost_fabric_cost_dtls_id')]['emb']==2)
					{
						$emb_type_name=$emblishment_embroy_type[$result_item[csf('pre_cost_fabric_cost_dtls_id')]['embtype']];
					}
					if($result_item[csf('pre_cost_fabric_cost_dtls_id')]['emb']==3)
					{
						$emb_type_name=$emblishment_wash_type[$result_item[csf('pre_cost_fabric_cost_dtls_id')]['embtype']];
					}
					if($result_item[csf('pre_cost_fabric_cost_dtls_id')]['emb']==4)
					{
						$emb_type_name=$emblishment_spwork_type[$result_item[csf('pre_cost_fabric_cost_dtls_id')]['embtype']];
					}
					if($result_item[csf('pre_cost_fabric_cost_dtls_id')]['emb']==5)
					{
						$emb_type_name=$emblishment_gmts_type[$result_item[csf('pre_cost_fabric_cost_dtls_id')]['embtype']];
					}
					if($result_item[csf('pre_cost_fabric_cost_dtls_id')]['emb']==99)
					{
						$emb_type_name=$emblishment_other_type_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]['embtype']];
					}
					?>
					<td style="border:1px solid black"><? echo $emb_type_name; ?></td>
					<td style="border:1px solid black"><? echo $garments_item[$result_itemdescription[csf('gmt_item')]];  ?></td>
					<td style="border:1px solid black; text-align:left"><? echo $body_part[$result_item[csf('pre_cost_fabric_cost_dtls_id')]['bodypart']]; ?></td>
                    <td style="border:1px solid black; text-align:left"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];} ?></td>
                    <td style="border:1px solid black; text-align:right"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?></td>
                    <td style="border:1px solid black; text-align:right"><? echo $color_library[$result_itemdescription[csf('color_number_id')]]; ?></td>
                    <td style="border:1px solid black; text-align:right"> <? echo $result_itemdescription[csf('item_size')]; ?></td>
                    <td style="border:1px solid black; text-align:right"><? echo $size_library[$result_itemdescription[csf('gmts_sizes')]]; ?></td>
                    <td style="border:1px solid black; text-align:right">
						<?
                        echo number_format($result_itemdescription[csf('cons')],4);
                        $item_desctiption_total += $result_itemdescription[csf('cons')] ;
                        ?>
                    </td>
                    <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                    <td style="border:1px solid black; text-align:right">
						<?
                        $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
                        echo number_format($amount_as_per_gmts_color,4);
                        $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                        ?>
                    </td>
                </tr>
			<?
            }
		?>
		<tr>
            <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
            <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total ,4); ?></td>
            <td style="border:1px solid black; text-align:right"></td>
            <td style="border:1px solid black; text-align:right">
				<?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
            </td>
		</tr>
		<?
		}
		?>
		<tr>
			<td align="right" style="border:1px solid black"  colspan="12"><strong>Total</strong></td>
			<td style="border:1px solid black;  text-align:right"><? echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
		</tr>
    </table>
    <?
    }
    ?>

    <!--==============================================AS PER Color & SIZE  END=========================================  -->

    <!--==============================================NO NENSITIBITY START=========================================  -->
    <?
    $nameArray_item=sql_select( "select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'   and a.sensitivity=0 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
		if(count($nameArray_item)>0){
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
		$costing_per=$job_costing_per_arr[$nameArray_job_po_row[csf('job_no')]];
		if($costing_per==1)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==2)
		{
		$costing_per_dzn="Pcs";
		}
		else if($costing_per==3)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==4)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==5)
		{
		$costing_per_dzn="Dzn";
		}
    ?>
    &nbsp;
    <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
    <tr>
    <td colspan="10" align="">
    <table width="100%" style="table-layout: fixed;">
    <tr>
    <td width="60%" align="left"><strong>NO sensitive  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " "; echo "&nbsp; File NO:".$file_no_arr[$nameArray_job_po_row[csf('job_no')]]." &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
    <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
    </tr>
    </table>
    </td>
    </tr>
    <tr>
    <td style="border:1px solid black"><strong>Sl</strong> </td>
    <td style="border:1px solid black"><strong>Name/Country</strong> </td>
    <td style="border:1px solid black"><strong>Embl. Type</strong> </td>
    <td style="border:1px solid black"><strong>Gmts Item</strong> </td>
    <td style="border:1px solid black"><strong>Body Part</strong> </td>
     <td style="border:1px solid black"><strong>Description</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
    <td style="border:1px solid black" align="center"><strong>WO Qty (<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Rate(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
    </tr>
    <?
    $i=0;
    $grand_total_as_per_gmts_color=0;
    foreach($nameArray_item as $result_item){
    $i++;
    $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0  and c.emb_name=".$result_item[csf('emb_name')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_color,c.emb_name,c.emb_type,c.body_part_id order by bid ");

    ?>
    <tr>
    <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <? echo $i; ?>
    </td>
    <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <?
    echo $emblishment_name_array[$result_item[csf('emb_name')]]."<br/>";
    echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][0]);
    ?>
    </td>
    <?
    $item_desctiption_total=0;
    $total_amount_as_per_gmts_color=0;
    foreach($nameArray_item_description as $result_itemdescription){
	if($result_item[csf('emb_name')]==1)
	{
		$emb_type_name=$emblishment_print_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==2)
	{
		$emb_type_name=$emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==3)
	{
		$emb_type_name=$emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==4)
	{
		$emb_type_name=$emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==5)
	{
		$emb_type_name=$emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
	}if($result_item[csf('emb_name')]==99)
	{
		$emb_type_name=$emblishment_other_type_arr[$result_itemdescription[csf('emb_type')]];
	}
    ?>
    <td style="border:1px solid black">
	<? echo $emb_type_name; ?>
    </td>
    <td style="border:1px solid black">
	<? echo $garments_item[$result_itemdescription[csf('gmt_item')]];  ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <? echo $body_part[$result_itemdescription[csf('body_part_id')]] // ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <?
	if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
	echo $color_library[$result_itemdescription[csf('item_color')]];
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($result_itemdescription[csf('cons')],4);
    $item_desctiption_total += $result_itemdescription[csf('cons')] ;
    ?>
    </td>
    <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
    <td style="border:1px solid black; text-align:right">
    <?
    $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
    echo number_format($amount_as_per_gmts_color,4);
    $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
    <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
    echo number_format($item_desctiption_total ,4);
    ?></td>
    <td style="border:1px solid black; text-align:right"></td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($total_amount_as_per_gmts_color,2);
    $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
    <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
    </tr>
    </table>

    <?
    }
    ?>

    <!--==============================================NO NENSITIBITY END=========================================  -->

	<?
    }
    ?>

    <?
    $mcurrency="";
    $dcurrency="";
    if($currency_id==1)
    {
    $mcurrency='Taka';
    $dcurrency='Paisa';
    }
    if($currency_id==2)
    {
    $mcurrency='USD';
    $dcurrency='CENTS';
    }
    if($currency_id==3)
    {
    $mcurrency='EURO';
    $dcurrency='CENTS';
    }
    ?>
    <br/>
    <table width="100%" style="margin-top:1px">
    <tr>
    <td>
    <table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
    <tr style="border:1px solid black;">
    <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
    <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total,2);?></td>
    </tr>
    <tr style="border:1px solid black;">
    <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
    <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    <br/>
    <table width="100%">
    <tr>
    <td width="49%">
    <?
    echo get_spacial_instruction($txt_booking_no);
    ?>
    </td>
    <td width="2%"></td>
    <?
    $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
    ?>
    <td width="49%" valign="top">
    <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">

    <tr style="border:1px solid black;">
    <td colspan="3" style="border:1px solid black;">Approval Status</td>
    </tr>
    <tr style="border:1px solid black;">
    <td width="3%" style="border:1px solid black;">Sl</td><td width="50%" style="border:1px solid black;">Name</td><td width="27%" style="border:1px solid black;">Approval Date</td><td width="20%" style="border:1px solid black;">Approval No</td>
    </tr>


    <?
    $i;
    foreach($data_array as $row){
    ?>
    <tr style="border:1px solid black;">
    <td width="3%" style="border:1px solid black;"><? echo $i;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')];?></td><td width="27%" style="border:1px solid black;"><? echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
    </tr>
    <?
    $i++;
    }
    ?>

    </table>
    </td>
    </tr>
    </table>
    </table>

    </div> <!--class="footer_signature"-->
    <div  style="margin-top:-5px;">
    <?
    echo signature_table(133, $cbo_company_name, "1300px",$cbo_template_id);
    ?>
    </div>
    <br>
    <div id="page_break_div">
    </div>
    <br>
    <div>

    <?
    //echo signature_table(2, $cbo_company_name, "1330px");
    echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
    ?>
    </div>

    <?
    if($link == 1){
    ?>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <?
    }else {
    ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <?
    }
    ?>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');

    </script>
    </html>
<?
exit();
}
if($action=="show_trim_booking_report3")
{
	extract($_REQUEST);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$id_approved_id=str_replace("'","",$id_approved_id);
	$report_type=str_replace("'","",$report_type);
	$show_comment=str_replace("'","",$show_comment);
	$cbo_template_id=str_replace("'","",$cbo_template_id);

	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$floorArr=return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;
	$booking_grand_total=0;
	$currency_id="";
	$buyer_string=array();
	$style_owner=array();
	$job_no=array();
	$style_ref=array();
	$all_dealing_marcent=array();
	$season=array();
	$order_repeat_no=array();
	$po_id_arr=array();
	$file_no_arr=array();

	$nameArray_buyer=sql_select( "select  a.style_ref_no, a.order_uom,a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant, a.season, a.season_matrix, a.season_buyer_wise, a.order_repeat_no, b.po_break_down_id,c.costing_per, d.file_no from wo_po_details_master a, wo_booking_dtls b,wo_pre_cost_mst c,wo_po_break_down d where a.job_no=b.job_no and a.job_no=c.job_no and b.job_no=c.job_no and a.job_no=d.job_no_mst and  b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");
	foreach ($nameArray_buyer as $result_buy){
		$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
		$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
		$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
		$job_no_array[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
		$job_costing_per_arr[$result_buy[csf('job_no')]]=$result_buy[csf('costing_per')];
		$job_uom_arr[$result_buy[csf('job_no')]]=$unit_of_measurement[$result_buy[csf('order_uom')]];
		$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];

		$file_no_arr[$result_buy[csf('job_no')]]=$result_buy[csf('file_no')];

		$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
		$season_matrix=$result_buy[csf('season_matrix')];
		$season_buyer_wise=$result_buy[csf('season_buyer_wise')];
		if($season_matrix!=0 && $season_buyer_wise==0 )
		{
			$season_matrix_con=$season_matrix;
		}
		else if($season_buyer_wise!=0 && $season_matrix==0)
		{
			$season_matrix_con=$season_buyer_wise;
		}
		$seasons_name.=$season_arr[$season_matrix_con].',';
		$order_rept_no.=$result_buy[csf('order_repeat_no')].',';
		$order_repeat_no[$result_buy[csf('order_repeat_no')]]=$result_buy[csf('order_repeat_no')];

		$po_id_arr[$result_buy[csf('po_break_down_id')]]=$result_buy[csf('po_break_down_id')];
	}
	$style_sting=implode(",",array_unique($style_ref));
	$job_no=implode(",",$job_no);
	$seasons_names=rtrim($seasons_name,',');

	$seasons_names=implode(",",array_unique(explode(",",$seasons_names)));
	$poid_arr=array_unique($po_id_arr);

	$order_rept_no=rtrim($order_rept_no,',');
	$order_rept_no=implode(",",array_unique(explode(",",$order_rept_no)));

	$po_no=array();
	$file_no=array();
	$ref_no=array();
	$po_quantity=array();
	$pub_shipment_date='';
	$int_ref_no='';
	$tot_po_quantity=0;
	$po_idss='';
	$nameArray_job=sql_select( "select a.pre_cost_fabric_cost_dtls_id,b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, (b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0  ");
	foreach ($nameArray_job as $result_job){
		$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
		$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
		$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
		$po_quantity[$result_job[csf('id')]]+=$result_job[csf('po_quantity')];
		$job_ref_no[$result_job[csf('job_no_mst')]].=$result_job[csf('grouping')].',';
		$po_no_arr[$result_job[csf('job_no_mst')]]['po_id'].=$result_job[csf('id')].',';
		$pub_shipment_date.=$result_job[csf('pub_shipment_date')].',';
		$int_ref_no.=$result_job[csf('grouping')].',';
		if($po_idss=='') $po_idss=$result_job[csf('id')];else $po_idss.=",".$result_job[csf('id')];

	}
	$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
	foreach ($sql_job as $row)
	{
		$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]]+=$row[csf('po_quantity')];
		$tot_po_quantity+=$row[csf('po_quantity')];
	}

	$nameArray=sql_select( "select a.booking_no, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no, a.floor_id from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
	foreach( $nameArray as $row)
	{
		$varcode_booking_no=$row[csf('booking_no')];
		$booking_date=$row[csf('booking_date')];
		$delivery_date=$row[csf('delivery_date')];
		$pay_mode_id=$row[csf('pay_mode')];
		$supplier_id=$row[csf('supplier_id')];
		$currency_id=$row[csf('currency_id')];
		$buyer_id=$row[csf('buyer_id')];
		$exchange_rate=$row[csf('exchange_rate')];
		$attention=$row[csf('attention')];
		$remarks=$row[csf('remarks')];
		$revised_no=$row[csf('revised_no')];
		$source_id=$row[csf('source')];
		if($pay_mode_id==3 || $pay_mode_id==5) $floor_id=$row[csf('floor_id')]; else $floor_id=0;
	}
	
	$nameArray_embl=sql_select( "select  c.emb_name,a.pre_cost_fabric_cost_dtls_id from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0  ");
	foreach( $nameArray_embl as $row)
	{
		$msg_ttl_change1=$emblishment_name_array[$row[csf('emb_name')]];
		$emb_count_arr[$row[csf('emb_name')]]=$row[csf('emb_name')];
	}
		
	if(count($emb_count_arr)==1)
	{
		
		$msg_ttl_change=$msg_ttl_change1." Work Order";
	}
	else  $msg_ttl_change="Embellishment Work Order";
	?>
    <html>
    <div style="width:1333px" align="center">
    <table width="1333px" cellpadding="0" cellspacing="0" style="border:0px solid black" >
        <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
            <tr>
                <td width="20px">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
                        <tr>
                            <td width="50" >
                            <?
							if($report_type==1)
                            {
								if($link == 1)
								{
								?>
                                    <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
								<?
								}
								else
								{
								?>
                                    <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
								<?
								}
                            }
                            else
                            {
							?>
                                <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
                            <?
							}
                            ?>
                            </td>
                            <td width="40px" align="center">
                                &nbsp;  &nbsp;  &nbsp;
                            </td>
                            <td width="30px"   align="center">
                            <b style="font-size:25px;"> <?
                            echo $company_library[$cbo_company_name]; ?>
                            </b>
                            <br>
                            <label>
                            <?
                            $nameArray=sql_select( "select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result){
                            ?>
                            <?  if($result[csf('plot_no')]!='') echo $result[csf('plot_no')].","; else echo '';?>
                            <? echo $result[csf('level_no')];?>
                            <? echo $result[csf('road_no')]; ?>
                            <? echo $result[csf('block_no')];?>
                            <? echo $result[csf('city')];?>
                            <? echo $result[csf('zip_code')]; ?> 
                            <? echo $result[csf('province')].","; ?> 
                            <? echo $country_arr[$result[csf('country_id')]]; ?> &nbsp;<br/>
                            <? echo $result[csf('email')];?>  &nbsp;
                            <? echo $result[csf('website')];
                            if($result[csf('plot_no')]!='')
                            {
                            $plot_no=$result[csf('plot_no')];
                            }
                            if($result[csf('level_no')]!='')
                            {
                            $level_no=$result[csf('level_no')];
                            }
                            if($result[csf('road_no')]!='')
                            {
                            $road_no=$result[csf('road_no')];
                            }
                            if($result[csf('block_no')]!='')
                            {
                            $block_no=$result[csf('block_no')];
                            }
                            if($result[csf('city')]!='')
                            {
                            $city=$result[csf('city')];
                            }
                            //$company_address[$result[csf('id')]]=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
                            }
                            ?>
                            </label>
                            <br/>
                            <b style="font-size:20px;">
                            <?php echo $msg_ttl_change;//$report_title; ?>
                            </b>
                            </td>
                            <td width="10px" align="center" style="font-size:20px;">
                                <table width="80%" align="right" cellpadding="0" cellspacing="0" style="border:0px solid black">
                                    <tr>
                                        <td width="80">  Booking No:&nbsp; <?php echo $varcode_booking_no; ?>  </td>
                                    </tr>
                                    <tr>
                                        <td>  Booking Date:&nbsp; <?php echo change_date_format($booking_date); ?>  </td>
                                    </tr>
                                    <?
                                    if($revised_no>0)
                                    {
                                    ?>
                                    <tr>
                                        <td>  Revised No:&nbsp; <?php echo $revised_no; ?>  </td>
                                    </tr>
                                    <?
                                    }
                                    if(str_replace("'","",$id_approved_id) ==1)
                                    {
                                    ?>
                                    <tr>
                                        <td>Approved Status :&nbsp;  <? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </td>
                                    </tr>
                                    <?
                                    }
                                    ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?
        $nameArray=sql_select( "select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$supplier_id");
        foreach ($nameArray as $result){
            if($result[csf('plot_no')]!='')
            {
            $plot_no=$result[csf('plot_no')];
            }
            if($result[csf('level_no')]!='')
            {
            $level_no=$result[csf('level_no')];
            }
            if($result[csf('road_no')]!='')
            {
            $road_no=$result[csf('road_no')];
            }
            if($result[csf('block_no')]!='')
            {
            $block_no=$result[csf('block_no')];
            }
            if($result[csf('city')]!='')
            {
            $city=$result[csf('city')];
            }
        	$company_address[$result[csf('id')]]=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
        }
        ?>

        <table width="900" align="left" style="border:0px solid black;">
            <tr>
            <td colspan="6" valign="top"></td>
            </tr>
            <tr>
            <td width="100" style="font-size:18px"><span><b>To, </b></span>  </td>
            <td width="110" colspan="5" style="font-size:18px">&nbsp;<span></span></td>
            </tr>
            <tr>
            <td width="210" colspan="2" style="font-size:18px">&nbsp; <b>
            <?
            if($pay_mode_id==5 || $pay_mode_id==3){
            echo $company_library[$supplier_id];
            }
            else{
            echo $supplier_name_arr[$supplier_id];
            }
            ?></b>
            </td>
            <td  width="100" style="font-size:12px"><b>Buyer.</b></td>
            <td  width="110" >:&nbsp;<? echo $buyer_name_arr[$buyer_id]; ?></td>
            <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
            <td width="110">:&nbsp;<?  echo change_date_format($delivery_date); ?></td>
            </tr>
            <tr>
            <td width="110" colspan="2" rowspan="2" style="font-size:18px">Address :&nbsp;
            <?
            if($pay_mode_id==5 || $pay_mode_id==3){
            $address=$company_address[$supplier_id];
            }
            else{
            $address=$supplier_address_arr[$supplier_id];
            }
            echo $address;
            ?>
            </td>
            <td width="100" style="font-size:12px"><b>PO Qty.</b>   </td>
            <td width="110">:&nbsp;<? echo $tot_po_quantity; ?></td>
            <td width="100" style="font-size:12px"><b>Season</b> </td>
            <td width="110">:&nbsp;<? echo $seasons_names; ?></td>
            <tr>
            <td width="100" style="font-size:12px"><b>Currency</b></td>
            <td width="110">:&nbsp;<?  echo $currency[$currency_id]; ?></td>
            <td width="100" style="font-size:12px"><b>Order Repeat </b> </td>
            <td width="110">:&nbsp;<? echo $order_rept_no; ?></td>
            </tr>
            <tr>
            <td width="100" style="font-size:12px" ><b>Attention </b>   </td>
            <td width="110">:&nbsp;<? echo $attention; ?> </td>
            <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
            <td width="110">:&nbsp;<? echo implode(",",array_unique($all_dealing_marcent))?></td>
            <td width="100" style="font-size:12px"><b>Pay mode</b></td>
            <td width="110">:&nbsp;<? echo $pay_mode[$pay_mode_id];?></td>
            </tr>
            <tr>
            	<td width="100" style="font-size:12px"><b>Floor/Unit</b></td>
                <td width="110">:&nbsp;<?=$floorArr[$floor_id];?></td>
                <td width="100" style="font-size:12px"><b>Source</b></td>
                <td width="110">:&nbsp;<? echo $source[$source_id];?></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Remarks</b>  </td>
                <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="5">:&nbsp;<? echo $remarks; ?></td>
            </tr>
        </table>
    <!--==============================================AS PER GMTS COLOR START=========================================  -->
    <?
   /* $precost_arr=array();
    $trims_qtyPerUnit_arr=array();
    $precost_sql=sql_select("select a.id, a.job_no,a.gmt_item,a.calculatorstring,a.remark, c.cal_parameter from wo_pre_cost_trim_cost_dtls a,wo_booking_dtls b, lib_item_group c where a.job_no=b.job_no and a.gmt_item=b.gmt_item and a.gmt_item=c.id and b.booking_no='$txt_booking_no' and a.id=b.pre_cost_fabric_cost_dtls_id and  b.status_active =1 and b.is_deleted=0");
    $calUom="";
    foreach($precost_sql as $precost_row){
    if($precost_row[csf('cal_parameter')]==1){
    $calUom="Mtr";
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
    }
    else if($precost_row[csf('cal_parameter')]==2){
    $calUom="Pcs";
    }
    else if($precost_row[csf('cal_parameter')]==3){
    $calUom="Pcs";
    }
    else if($precost_row[csf('cal_parameter')]==4){
    $calUom="Pcs";
    }
    else if($precost_row[csf('cal_parameter')]==5){
    $calUom="Yds";
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
    }
    else if($precost_row[csf('cal_parameter')]==6){
    $calUom="Yds";
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
    }
    else if($precost_row[csf('cal_parameter')]==7){
    $calUom="Pcs";
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
    }
    else if($precost_row[csf('cal_parameter')]==8){
    $calUom="Yds";
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
    $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
    }
    else{
    $calUom=0;
    }
    $trims_remark_arr[$precost_row[csf('id')]]['remark']=$precost_row[csf('remark')];
    }*/

    $booking_country_arr=array();
    $nameArray_booking_country=sql_select( "select pre_cost_fabric_cost_dtls_id,sensitivity,country_id_string from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0");
    foreach($nameArray_booking_country as $nameArray_booking_country_row){
		$country_id_string=explode(",",$nameArray_booking_country_row[csf('country_id_string')]);
		$tocu=count($country_id_string);
		for($cu=0;$cu<$tocu;$cu++){
			$booking_country_arr[$nameArray_booking_country_row[csf('pre_cost_fabric_cost_dtls_id')]][$nameArray_booking_country_row[csf('sensitivity')]][$country_id_string[$cu]]=$country_arr[$country_id_string[$cu]];
		}
    }

    $nameArray_job_po=sql_select( "select job_no from wo_booking_dtls  where booking_no='$txt_booking_no' and status_active =1 and is_deleted=0 group by job_no order by job_no ");
    foreach($nameArray_job_po as $nameArray_job_po_row){
		$nameArray_item=sql_select( "select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'   and a.sensitivity=1 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
		if(count($nameArray_item)>0){
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
			
			$costing_per=$job_costing_per_arr[$nameArray_job_po_row[csf('job_no')]];
			if($costing_per==1)
			{
			$costing_per_dzn="Dzn";
			}
			else if($costing_per==2)
			{
			$costing_per_dzn="Pcs";
			}
			else if($costing_per==3)
			{
			$costing_per_dzn="Dzn";
			}
			else if($costing_per==4)
			{
			$costing_per_dzn="Dzn";
			}
			else if($costing_per==5)
			{
			$costing_per_dzn="Dzn";
			}
    ?>
    &nbsp;
    <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
    <tr>
    <td colspan="10" align="">
    <table width="100%" style="table-layout: fixed;">
    <tr>
    <td width="60%" align="left"><strong>As Per Garments Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " "; echo "&nbsp; File NO:".$file_no_arr[$nameArray_job_po_row[csf('job_no')]]." &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
    <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
    </tr>
    </table>
    </td>
    </tr>
    <tr>
    <td style="border:1px solid black"><strong>Sl</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Name</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Type</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Gmts Item</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Body Part</strong> </td>
     <td align="center" style="border:1px solid black"><strong>Description</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
    <td style="border:1px solid black" align="center"><strong>WO Qty(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Rate(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
    </tr>
    <?
    $i=0;
    $grand_total_as_per_gmts_color=0;
	$currency_sign_arr = array(1 => "৳", 2 => "$", 3 => "€", 4 => "CHF", 5 => "S$", 6 => "£", 7 => "¥");
    foreach($nameArray_item as $result_item){
    $i++;
    $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and c.emb_name=".$result_item[csf('emb_name')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.color_size_table_id>0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_color,c.emb_name,c.emb_type,c.body_part_id order by bid ");

    ?>
    <tr>
    <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <? echo $i; ?>
    </td>
    <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <?
    echo $emblishment_name_array[$result_item[csf('emb_name')]]."<br/>";
    echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][1]);
    ?>
    </td>
    <?
    $item_desctiption_total=0;
    $total_amount_as_per_gmts_color=0;
    foreach($nameArray_item_description as $result_itemdescription){
	if($result_item[csf('emb_name')]==1)
	{
		$emb_type_name=$emblishment_print_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==2)
	{
		$emb_type_name=$emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==3)
	{
		$emb_type_name=$emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==4)
	{
		$emb_type_name=$emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==5)
	{
		$emb_type_name=$emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==99)
	{
		$emb_type_name=$emblishment_other_type_arr[$result_itemdescription[csf('emb_type')]];
	}
    ?>
    <td style="border:1px solid black">
	<? echo $emb_type_name; ?>
    </td>
    <td style="border:1px solid black">
	<? echo $garments_item[$result_itemdescription[csf('gmt_item')]];  ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <? echo $body_part[$result_itemdescription[csf('body_part_id')]] // ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <?
	if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
	echo $color_library[$result_itemdescription[csf('item_color')]];
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($result_itemdescription[csf('cons')],4);
    $item_desctiption_total += $result_itemdescription[csf('cons')] ;
    ?>
    </td>
    <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('amount')]/$result_itemdescription[csf('cons')],4); ?> </td>
    <td style="border:1px solid black; text-align:right">
    <?
    $amount_as_per_gmts_color = $result_itemdescription[csf('amount')];
    echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($amount_as_per_gmts_color,4);
    $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
    <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
    echo number_format($item_desctiption_total ,4);
    ?></td>
    <td style="border:1px solid black; text-align:right"></td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo $currency_sign_arr[$currency_id].'&nbsp;'.number_format($total_amount_as_per_gmts_color,2);
    $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
    <td  style="border:1px solid black;  text-align:right"><?  echo $currency_sign_arr[$currency_id].'&nbsp;'.number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
    </tr>
    </table>

    <?
    }
    ?>
    <!--==============================================AS PER GMTS COLOR END=========================================  -->


    <!--==============================================Size Sensitive START=========================================  -->
    <?
		$nameArray_item=sql_select( "select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'   and a.sensitivity=2 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
    if(count($nameArray_item)>0)
    {
		$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
		$po_ids=array_unique(explode(",",$po_ids));
		$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
		$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
		$po_no_qty=0;
		$job_no=$nameArray_job_po_row[csf('job_no')];
		foreach($po_ids as $poid)
		{
			$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
		}
		$costing_per=$job_costing_per_arr[$nameArray_job_po_row[csf('job_no')]];
		if($costing_per==1)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==2)
		{
		$costing_per_dzn="Pcs";
		}
		else if($costing_per==3)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==4)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==5)
		{
		$costing_per_dzn="Dzn";
		}
    ?>
    <br/>
    <table border="1"align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
    <tr>
    <td colspan="10" align="">
    <table width="100%" style="table-layout: fixed;">
    <tr>
    	
    <td width="60%"><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; echo "&nbsp;&nbsp;Int Ref.:".$ref_nos; echo "&nbsp; File NO:".$file_no_arr[$nameArray_job_po_row[csf('job_no')]]."&nbsp;&nbsp; Po Qty..:".$po_no_qty; ?></strong></td>
    <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;margin-left:210px; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
    </tr>
    </table>
    </td>
    </tr>
    <tr>
    <td style="border:1px solid black"><strong>Sl</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Name</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Type</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Gmts Item</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Body Part</strong> </td>
     <td align="center" style="border:1px solid black"><strong>Description</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
    <td style="border:1px solid black" align="center"><strong>WO Qty(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Rate(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
    </tr>
    <?
    $i=0;
    $grand_total_as_per_gmts_color=0;
	$currency_sign_arr = array(1 => "৳", 2 => "$", 3 => "€", 4 => "CHF", 5 => "S$", 6 => "£", 7 => "¥");
    foreach($nameArray_item as $result_item)
    {
    $i++;
   // $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,b.article_number,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_emb_book_con_dtls b where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.gmt_item=".$result_item[csf('gmt_item')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_size,b.article_number order by bid");
       $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=2  and c.emb_name=".$result_item[csf('emb_name')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_size,c.emb_name,c.emb_type,c.body_part_id order by bid ");

    ?>
    <tr>
    <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <? echo $i; ?>
    </td>
    <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <?
    echo $emblishment_name_array[$result_item[csf('emb_name')]]."<br/>";
    echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][2]);
    ?>
    </td>
    <?
    $item_desctiption_total=0;
    $total_amount_as_per_gmts_size=0;
    foreach($nameArray_item_description as $result_itemdescription){
	if($result_item[csf('emb_name')]==1)
	{
		$emb_type_name=$emblishment_print_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==2)
	{
		$emb_type_name=$emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==3)
	{
		$emb_type_name=$emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==4)
	{
		$emb_type_name=$emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==5)
	{
		$emb_type_name=$emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==99)
	{
		$emb_type_name=$emblishment_other_type_arr[$result_itemdescription[csf('emb_type')]];
	}
    ?>
    <td style="border:1px solid black">
	<? echo $emb_type_name; ?>
    </td>
    <td style="border:1px solid black">
	<? echo $garments_item[$result_itemdescription[csf('gmt_item')]];  ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <? echo $body_part[$result_itemdescription[csf('body_part_id')]] // ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <?
	if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
	echo $result_itemdescription[csf('item_size')];
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($result_itemdescription[csf('cons')],4);
    $item_desctiption_total += $result_itemdescription[csf('cons')] ;
    ?>
    </td>
    <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
    <td style="border:1px solid black; text-align:right">
    <?
    $amount_as_per_gmts_size = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
    echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($amount_as_per_gmts_size,4);
    $total_amount_as_per_gmts_size+=$amount_as_per_gmts_size;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
    <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
    echo number_format($item_desctiption_total ,4);
    ?>ccc</td>
    <td style="border:1px solid black; text-align:right"></td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($total_amount_as_per_gmts_size,2);
    $grand_total_as_per_gmts_size+=$total_amount_as_per_gmts_size;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
    <td  style="border:1px solid black;  text-align:right"><?  echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($grand_total_as_per_gmts_size,2); $booking_grand_total+=$grand_total_as_per_gmts_size; ?></td>
    </tr>
    </table>
    <br/>
    <?
    }
    ?>

    <!--==============================================Size Sensitive END=========================================  -->

    <!--==============================================AS PER CONTRAST COLOR START=========================================  -->


    <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
    <?
    $nameArray_item=sql_select( "select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'   and a.sensitivity=3 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
		if(count($nameArray_item)>0){
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
		$costing_per=$job_costing_per_arr[$nameArray_job_po_row[csf('job_no')]];
		if($costing_per==1)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==2)
		{
		$costing_per_dzn="Pcs";
		}
		else if($costing_per==3)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==4)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==5)
		{
		$costing_per_dzn="Dzn";
		}
    ?>
    &nbsp;
    <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
    <tr>
    <td colspan="11" align="">
    <table width="100%" style="table-layout: fixed;">
    <tr>
    <td width="60%" align="left"><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " "; echo "&nbsp; File NO:".$file_no_arr[$nameArray_job_po_row[csf('job_no')]]." &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
    <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
    </tr>
    </table>
    </td>
    </tr>
    <tr>
    <td style="border:1px solid black"><strong>Sl</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Name</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Type</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Gmts Item</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Body Part</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Description</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
     <td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
    <td style="border:1px solid black" align="center"><strong>WO Qty(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Rate(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
    </tr>
    <?
    $i=0;
    $grand_total_as_per_gmts_color=0;
	$currency_sign_arr = array(1 => "৳", 2 => "$", 3 => "€", 4 => "CHF", 5 => "S$", 6 => "£", 7 => "¥");
    foreach($nameArray_item as $result_item){
    $i++;
    $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=3  and c.emb_name=".$result_item[csf('emb_name')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_color,b.color_number_id,c.emb_name,c.emb_type,c.body_part_id order by bid ");
	

    ?>
    <tr>
    <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <? echo $i; ?>
    </td>
    <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <?
    echo $emblishment_name_array[$result_item[csf('emb_name')]]."<br/>";
    echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][3]);
    ?>
    </td>
    <?
    $item_desctiption_total=0;
    $total_amount_as_per_gmts_color=0;
    foreach($nameArray_item_description as $result_itemdescription){
	if($result_item[csf('emb_name')]==1)
	{
		$emb_type_name=$emblishment_print_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==2)
	{
		$emb_type_name=$emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==3)
	{
		$emb_type_name=$emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==4)
	{
		$emb_type_name=$emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==5)
	{
		$emb_type_name=$emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
	}if($result_item[csf('emb_name')]==99)
	{
		$emb_type_name=$emblishment_other_type_arr[$result_itemdescription[csf('emb_type')]];
	}
    ?>
    <td style="border:1px solid black">
	<? echo $emb_type_name; ?>
    </td>
    <td style="border:1px solid black">
	<? echo $garments_item[$result_itemdescription[csf('gmt_item')]];  ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <? echo $body_part[$result_itemdescription[csf('body_part_id')]] // ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <?
	if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
	echo $color_library[$result_itemdescription[csf('item_color')]];
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
	echo $color_library[$result_itemdescription[csf('color_number_id')]];
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($result_itemdescription[csf('cons')],4);
    $item_desctiption_total += $result_itemdescription[csf('cons')] ;
    ?>
    </td>
    <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
    <td style="border:1px solid black; text-align:right">
    <?
    $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
    echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($amount_as_per_gmts_color,4);
    $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
    <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
    echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($item_desctiption_total ,4);
    ?></td>
    <td style="border:1px solid black; text-align:right"></td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($total_amount_as_per_gmts_color,2);
    $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td align="right" style="border:1px solid black"  colspan="10"><strong>Total</strong></td>
    <td  style="border:1px solid black;  text-align:right"><?  echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
    </tr>
    </table>

    <?
    }
    ?>
    <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
    <?
				$nameArray_item = sql_select("select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='" . $nameArray_job_po_row[csf('job_no')] . "'   and a.sensitivity=4 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
				if (count($nameArray_item) > 0) {
					$po_ids = rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
					$po_ids = array_unique(explode(",", $po_ids));
					$ref_nos = rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]], ',');
					$ref_nos = implode(",", array_unique(explode(",", $ref_nos)));
					$po_no_qty = 0;
					$job_no = $nameArray_job_po_row[csf('job_no')];
					foreach ($po_ids as $poid) {
						$po_no_qty += $job_po_qty_arr[$job_no][$poid];
					}
				?>
					&nbsp;
					<table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all">
						<tr>
							<td colspan="13" align="">
								<table width="100%" style="table-layout: fixed;">
									<tr>
										<td width="60%" align="left"><strong>Color & Size Sensitive (<? echo "Job NO:" . $nameArray_job_po_row[csf('job_no')]; ?>) <? echo "Style NO:" . $style_ref[$nameArray_job_po_row[csf('job_no')]];
																																								if ($ref_nos != '') echo " &nbsp;Int Ref.:&nbsp;" . $ref_nos;
																																								else " ";
																																								echo " &nbsp;  Po Qty.:&nbsp;" . $po_no_qty; ?></strong></td>
										<td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",", $po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black"><strong>Sl</strong> </td>
							<td align="center" style="border:1px solid black"><strong>Name</strong> </td>
							<td align="center" style="border:1px solid black"><strong>Type</strong> </td>
							<td align="center" style="border:1px solid black"><strong>Gmts Item</strong> </td>
							<td align="center" style="border:1px solid black"><strong>Body Part</strong> </td>
							<td align="center" style="border:1px solid black"><strong>Description</strong> </td>
							<td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
							<td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
							<td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
							<td align="center" style="border:1px solid black"><strong>Gmts Size</strong></td>
							<td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
							<td style="border:1px solid black" align="center"><strong>Rate</strong></td>
							<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
						</tr>
						<?
						$i = 0;
						$grand_total_as_per_gmts_color = 0;
						$currency_sign_arr = array(1 => "৳", 2 => "$", 3 => "€", 4 => "CHF", 5 => "S$", 6 => "£", 7 => "¥");
						foreach ($nameArray_item as $result_item) {
							$i++;
							$nameArray_item_description = sql_select("select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_color,b.color_number_id,b.item_size,b.gmts_sizes,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id,a.uom as uom from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='" . $nameArray_job_po_row[csf('job_no')] . "'  and a.sensitivity=4  and c.emb_name=" . $result_item[csf('emb_name')] . " and a.pre_cost_fabric_cost_dtls_id=" . $result_item[csf('pre_cost_fabric_cost_dtls_id')] . " and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_color,b.color_number_id,b.item_size,b.gmts_sizes,c.emb_name,c.emb_type,c.body_part_id,a.uom order by bid ");

						?>
							<tr>
								<td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<? echo $i; ?>
								</td>
								<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<?
									echo $emblishment_name_array[$result_item[csf('emb_name')]] . "<br/>";
									echo implode(", ", $booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][4]);
									?>
								</td>
								<?
								$item_desctiption_total = 0;
								$total_amount_as_per_gmts_color = 0;
								foreach ($nameArray_item_description as $result_itemdescription) {
									if ($result_item[csf('emb_name')] == 1) {
										$emb_type_name = $emblishment_print_type[$result_itemdescription[csf('emb_type')]];
									}
									if ($result_item[csf('emb_name')] == 2) {
										$emb_type_name = $emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
									}
									if ($result_item[csf('emb_name')] == 3) {
										$emb_type_name = $emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
									}
									if ($result_item[csf('emb_name')] == 4) {
										$emb_type_name = $emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
									}
									if ($result_item[csf('emb_name')] == 5) {
										$emb_type_name = $emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
									}
								?>
									<td style="border:1px solid black">
										<? echo $emb_type_name; ?>
									</td>
									<td style="border:1px solid black">
										<? echo $garments_item[$result_itemdescription[csf('gmt_item')]];  ?>
									</td>
									<td style="border:1px solid black; text-align:left">
										<? echo $body_part[$result_itemdescription[csf('body_part_id')]] // 
										?>
									</td>
									<td style="border:1px solid black; text-align:left">
										<?
										if ($result_itemdescription[csf('description')]) {
											echo $result_itemdescription[csf('description')];
										}
										?>
									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo $color_library[$result_itemdescription[csf('item_color')]];
										?>
									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo $color_library[$result_itemdescription[csf('color_number_id')]];
										?>
									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo $result_itemdescription[csf('item_size')];
										?>
									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo $size_library[$result_itemdescription[csf('gmts_sizes')]];
										?>
									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo number_format($result_itemdescription[csf('cons')], 4);
										$item_desctiption_total += $result_itemdescription[csf('cons')];
										?>
									</td>
									<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')], 4); ?> </td>
									<td style="border:1px solid black; text-align:right">
										<?
										$amount_as_per_gmts_color = $result_itemdescription[csf('cons')] * $result_itemdescription[csf('rate')];
										echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($amount_as_per_gmts_color, 4);
										$total_amount_as_per_gmts_color += $amount_as_per_gmts_color;
										?>
									</td>
							</tr>
						<?
								}
						?>
						<tr>
							<td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
							<td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
																										echo number_format($item_desctiption_total, 4);
																										?></td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right">
								<?
								echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($total_amount_as_per_gmts_color, 2);
								$grand_total_as_per_gmts_color += $total_amount_as_per_gmts_color;
								$booking_grand_total += $total_amount_as_per_gmts_color;
								$total_amount_as_per_gmts_color = 0;
								?>
							</td>
						</tr>
					<?
						}
					?>
					<tr>
						<td align="right" style="border:1px solid black" colspan="12"><strong>Total</strong></td>
						<td style="border:1px solid black;  text-align:right"><? echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($grand_total_as_per_gmts_color, 2);  ?></td>
					</tr>
					</table>

				<?
				}
				?>

    <!--==============================================AS PER Color & SIZE  END=========================================  -->

    <!--==============================================NO NENSITIBITY START=========================================  -->
    <?
    $nameArray_item=sql_select( "select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'   and a.sensitivity=0 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
		if(count($nameArray_item)>0){
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
		$costing_per=$job_costing_per_arr[$nameArray_job_po_row[csf('job_no')]];
		if($costing_per==1)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==2)
		{
		$costing_per_dzn="Pcs";
		}
		else if($costing_per==3)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==4)
		{
		$costing_per_dzn="Dzn";
		}
		else if($costing_per==5)
		{
		$costing_per_dzn="Dzn";
		}
    ?>
    &nbsp;
    <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
    <tr>
    <td colspan="10" align="">
    <table width="100%" style="table-layout: fixed;">
    <tr>
    <td width="60%" align="left"><strong>NO sensitive  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " "; echo "&nbsp; File NO:".$file_no_arr[$nameArray_job_po_row[csf('job_no')]]." &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
    <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
    </tr>
    </table>
    </td>
    </tr>
    <tr>
    <td style="border:1px solid black"><strong>Sl</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Name</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Type</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Gmts Item</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Body Part</strong> </td>
     <td align="center" style="border:1px solid black"><strong>Description</strong> </td>
    <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
    <td style="border:1px solid black" align="center"><strong>WO Qty (<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Rate(<? echo $costing_per_dzn;?>)</strong></td>
    <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
    </tr>
    <?
    $i=0;
    $grand_total_as_per_gmts_color=0;
	$currency_sign_arr = array(1 => "৳", 2 => "$", 3 => "€", 4 => "CHF", 5 => "S$", 6 => "£", 7 => "¥");
    foreach($nameArray_item as $result_item){
    $i++;
    $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0  and c.emb_name=".$result_item[csf('emb_name')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_color,c.emb_name,c.emb_type,c.body_part_id order by bid ");

    ?>
    <tr>
    <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <? echo $i; ?>
    </td>
    <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
    <?
    echo $emblishment_name_array[$result_item[csf('emb_name')]]."<br/>";
    echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][0]);
    ?>
    </td>
    <?
    $item_desctiption_total=0;
    $total_amount_as_per_gmts_color=0;
    foreach($nameArray_item_description as $result_itemdescription){
	if($result_item[csf('emb_name')]==1)
	{
		$emb_type_name=$emblishment_print_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==2)
	{
		$emb_type_name=$emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==3)
	{
		$emb_type_name=$emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==4)
	{
		$emb_type_name=$emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
	}
	if($result_item[csf('emb_name')]==5)
	{
		$emb_type_name=$emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
	}if($result_item[csf('emb_name')]==99)
	{
		$emb_type_name=$emblishment_other_type_arr[$result_itemdescription[csf('emb_type')]];
	}
    ?>
    <td style="border:1px solid black">
	<? echo $emb_type_name; ?>
    </td>
    <td style="border:1px solid black">
	<? echo $garments_item[$result_itemdescription[csf('gmt_item')]];  ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <? echo $body_part[$result_itemdescription[csf('body_part_id')]] // ?>
    </td>
    <td style="border:1px solid black; text-align:left">
    <?
	if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
	echo $color_library[$result_itemdescription[csf('item_color')]];
    ?>
    </td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo number_format($result_itemdescription[csf('cons')],4);
    $item_desctiption_total += $result_itemdescription[csf('cons')] ;
    ?>
    </td>
    <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
    <td style="border:1px solid black; text-align:right">
    <?
    $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
    echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($amount_as_per_gmts_color,4);
    $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
    <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
    echo number_format($item_desctiption_total ,4);
    ?></td>
    <td style="border:1px solid black; text-align:right"></td>
    <td style="border:1px solid black; text-align:right">
    <?
    echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($total_amount_as_per_gmts_color,2);
    $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
    ?>
    </td>
    </tr>
    <?
    }
    ?>
    <tr>
    <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
    <td  style="border:1px solid black;  text-align:right"><?  echo  $currency_sign_arr[$currency_id].'&nbsp;'.number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
    </tr>
    </table>

    <?
    }
    ?>

    <!--==============================================NO NENSITIBITY END=========================================  -->

	<?
    }
    ?>

    <?
    $mcurrency="";
    $dcurrency="";
    if($currency_id==1)
    {
    $mcurrency='Taka';
    $dcurrency='Paisa';
    }
    if($currency_id==2)
    {
    $mcurrency='USD';
    $dcurrency='CENTS';
    }
    if($currency_id==3)
    {
    $mcurrency='EURO';
    $dcurrency='CENTS';
    }
    ?>
    <br/>
    <table width="100%" style="margin-top:1px">
    <tr>
    <td>
    <table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
    <tr style="border:1px solid black;">
    <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
    <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total,2);?></td>
    </tr>
    <tr style="border:1px solid black;">
    <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
    <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    <br/>
    <table width="100%">
    <tr>
    <td width="49%">
    <?
    echo get_spacial_instruction($txt_booking_no);
    ?>
    </td>
    <td width="2%"></td>
    <?
    $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
    ?>
    <td width="49%" valign="top">
    <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">

    <tr style="border:1px solid black;">
    <td colspan="3" style="border:1px solid black;">Approval Status</td>
    </tr>
    <tr style="border:1px solid black;">
    <td width="3%" style="border:1px solid black;">Sl</td><td width="50%" style="border:1px solid black;">Name</td><td width="27%" style="border:1px solid black;">Approval Date</td><td width="20%" style="border:1px solid black;">Approval No</td>
    </tr>


    <?
    $i;
    foreach($data_array as $row){
    ?>
    <tr style="border:1px solid black;">
    <td width="3%" style="border:1px solid black;"><? echo $i;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')];?></td><td width="27%" style="border:1px solid black;"><? echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
    </tr>
    <?
    $i++;
    }
    ?>

    </table>
    </td>
    </tr>
    </table>
    </table>

    </div> <!--class="footer_signature"-->
    <div  style="margin-top:-5px;">
    <?
    echo signature_table(133, $cbo_company_name, "1300px",$cbo_template_id);
    ?>
    </div>
    <br>
    <div id="page_break_div">
    </div>
    <br>
    <div>

    <?
    //echo signature_table(2, $cbo_company_name, "1330px");
    echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
    ?>
    </div>

    <?
    if($link == 1){
    ?>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <?
    }else {
    ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <?
    }
    ?>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');

    </script>
    </html>
<?
exit();
}
?>