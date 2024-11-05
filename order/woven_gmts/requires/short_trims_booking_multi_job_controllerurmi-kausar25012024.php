<?
//error_reporting(-1);
//ini_set('display_errors',1);
/*-------------------------------------------- Comments
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  Aziz
Purpose			         :  This form will create Trims Booking Multi Job Wise
Functionality	         :
JS Functions	         :
Created by		         :  Aziz
Creation date 	         :  17-1-2016
Requirment Client        :  Peak Apperels
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.trims.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//---------------------------------------------------- Start---------------------------------------------------------------------------
function load_drop_down_supplier($data){
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/short_trims_booking_multi_job_controllerurmi');",0,"" );
	}
	else
	{
	$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/short_trims_booking_multi_job_controllerurmi');","");
	}
	return $cbo_supplier_name;
	exit();
}

if ($action=="populate_variable_setting_data"){
	$data_array=sql_select("select exeed_budge_qty,exeed_budge_amount,amount_exceed_level from variable_order_tracking where company_name='$data' and item_category_id=4 and variable_list=26 and status_active=1 and is_deleted=0");
	foreach ($data_array as $row){
		echo "document.getElementById('exeed_budge_qty').value = '".$row[csf("exeed_budge_qty")]."';\n";
		echo "document.getElementById('exeed_budge_amount').value = '".$row[csf("exeed_budge_amount")]."';\n";
		echo "document.getElementById('amount_exceed_level').value = '".$row[csf("amount_exceed_level")]."';\n";
	}
	$cbo_supplier_name     = create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, \'load_drop_down_attention\', \'requires/short_trims_booking_multi_job_controllerurmi\');","");
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 172, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"","");

	echo "document.getElementById('supplier_td').innerHTML = '".$cbo_supplier_name."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=57 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();
}


if ($action=="load_drop_down_buyer"){
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		exit();
	}
}

if ($action=="load_drop_down_supplier"){
	echo $action($data);
	exit();
}
if($action=="load_drop_down_attention"){
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();
}

if ($action=="fnc_process_data"){
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	?>
  <script>
	var cbo_level='<? echo $cbo_level; ?>';
	var po_job_level=cbo_level;
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			//alert(tbl_row_count)
			if(document.getElementById('check_all').checked==true)
			{
				po_job_level=1;
			}
			else if(document.getElementById('check_all').checked==false)
			{
				po_job_level=cbo_level;
			}
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			//alert(x+'_'+origColor)
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
				var select_row=0; var sp=1;
				if(po_job_level==1)
				{
					var select_row= str;
					sp=1;
				}
				else if(po_job_level==2)
				{
					var tbl_length =$('#tbl_list_search tr').length-1;
					var select_str=$('#txt_job_no' + str).val()+'_'+$('#hiddtrim_group' + str).val()+'_'+$('#td_item_des' + str).text();
					
				// alert($('#hiddtrim_group' + str).val());
					for(var i=1; i<=tbl_length; i++)
					{
						var string=$('#txt_job_no' + i).val()+'_'+$('#hiddtrim_group' + i).val()+'_'+$('#td_item_des' + i).text();
						if(select_str==string)
						{
							// alert(select_str+'='+string);
							if(select_row==0)
							{
								select_row=i; sp=1;
							}
							else
							{
								select_row+=','+i; sp=2;
							}
						}
					}
				}
				var exrow = new Array();
				if(sp==2) { exrow=select_row.split(','); var countrow=exrow.length; }
				else countrow=1;
				//alert(select_row)

				//alert(exrow)
				for(var m=0; m<countrow; m++)
				{
					if(sp==2) exrow[m]=exrow[m];
					else exrow[m]=select_row;
					//alert(exrow[m])
					toggle( document.getElementById( 'search' + exrow[m] ), '#FFFFCC' );
					if( jQuery.inArray( $('#txt_individual_id' + exrow[m]).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + exrow[m]).val() );
						selected_name.push($('#txt_job_no' + exrow[m]).val());
						selected_item.push($('#txt_trim_group_id' + exrow[m]).val());
						selected_po.push($('#txt_po_id' + exrow[m]).val());
					}
					else{
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == $('#txt_individual_id' + exrow[m]).val() ) break;
						}
						selected_id.splice( i, 1 );
						selected_name.splice( i,1 );
						selected_item.splice( i,1 );
						selected_po.splice( i,1 );
					}
				}
				var id = ''; var job = ''; var txt_trim_group_id=''; var txt_po_id='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					job += selected_name[i] + ',';
					txt_trim_group_id+=selected_item[i]+ ',';
					txt_po_id+=selected_po[i]+ ',';
				}
				id = id.substr( 0, id.length - 1 );
				job = job.substr( 0, job.length - 1 );
				txt_trim_group_id = txt_trim_group_id.substr( 0, txt_trim_group_id.length - 1 );
				txt_po_id = txt_po_id.substr( 0, txt_po_id.length - 1 );
				$('#txt_selected_id').val( id );
				$('#txt_job_id').val( job );
				$('#itemGroup').val( txt_trim_group_id );
				$('#txt_selected_po').val( txt_po_id );
			}
		}
	</script>
	<div align="center" style="width:100%;" >
    <input type="hidden" id="txt_booking" value="" />
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                    <th width="120">Style Ref </th>
                    <th width="120">Job No </th>
                    <th width="100">Int. Ref. No </th>
                    <th width="100">Order No</th>
                    <th width="150">Item Name</th>
                    <th>
                    <input type="hidden" style="width:20px" name="txt_garments_nature" id="txt_garments_nature" value="<?=$garments_nature;?>" />
                    <input type="hidden" name="cbo_booking_month" id="cbo_booking_month" value="<? echo $cbo_booking_month;?>" />
                    <input type="hidden" name="cbo_booking_year" id="cbo_booking_year" value="<? echo $cbo_booking_year;?>" />
                    <input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $company_id;?>" />
                    <input type="hidden" style="width:20px" name="cbo_buyer_name" id="cbo_buyer_name" value="<? echo $cbo_buyer_name;?>" />
                    <input type="hidden" name="cbo_currency" id="cbo_currency" value="<? echo $cbo_currency;?>" />
                    <input type="hidden" name="cbo_currency_job" id="cbo_currency_job" value="<? echo $cbo_currency_job;?>" />
                    <input type="hidden" style="width:20px" name="cbo_supplier_name" id="cbo_supplier_name" value="<? echo $cbo_supplier_name;?>" />
                    </th>
                </tr>
            </thead>
            <tr class="general">
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:110px"></td>
                <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:110px"></td>
                <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:140px"></td>
                <td><?=create_drop_down( "cbo_item", 150, "select a.id,a.item_name from  lib_item_group a where a.item_category=4 and  a.status_active =1 and a.is_deleted=0 order by a.item_name","id,item_name", 1, "-- Select Item Name --", $selected, "",0 );		
				?></td>
                <td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_booking_month').value+'_'+document.getElementById('cbo_booking_year').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_currency_job').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('cbo_item').value+'_'+document.getElementById('txt_ref_no').value, 'create_fnc_process_data', 'search_div', 'short_trims_booking_multi_job_controllerurmi','setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" valign="middle">
                    <?=create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );// echo load_month_buttons(); ?>
                </td>
            </tr>
        </table>
        </form>
        </div>
        <div align="center" valign="top" id="search_div"></div>
	   </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_fnc_process_data"){
	?>
	<div style="width:1295px;" align="center" >
	<?
	extract($_REQUEST);

	$data=explode('_',$data);
	$company_id=$data[0];
	$cbo_buyer_name=$data[1];
	$cbo_supplier_name=$data[2];
	$cbo_booking_month=$data[3];

	$cbo_booking_year=$data[4];
	$cbo_year_selection=$data[5];
	$cbo_currency=$data[6];
	$cbo_currency_job=$data[7];
	//echo $company_id.'='.$cbo_buyer_name;
	$txt_style=$data[8];
	$txt_order_search=$data[9];
	 $txt_job=$data[10];
	$cbo_item=$data[11];
	$ref_no=$data[12];

	if ($txt_style!="") $style_cond=" and a.style_ref_no='$txt_style'"; else $style_cond=$txt_style;
	if ($txt_order_search!="") $order_cond=" and d.po_number='$txt_order_search'"; else $order_cond="";
	if ($ref_no!="") $ref_cond=" and d.grouping='$ref_no'"; else $ref_cond="";
	if ($txt_job!="") $job_cond=" and a.job_no_prefix_num='$txt_job'"; else $job_cond ="";
	if ($cbo_item!=0) $itemgroup_cond=" and c.trim_group=$cbo_item"; else $itemgroup_cond ="";
	$booking_month=0;
	if($cbo_booking_month<10){
		$booking_month.=$cbo_booking_month;
	}
	else{
		$booking_month=$cbo_booking_month;
	}

	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);

	if($db_type==0){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	?>
	<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
	<input type="hidden" name="itemGroup" id="itemGroup" value="" />
	<input type="hidden" name="txt_job_id" id="txt_job_id" value="" />
	<input type="hidden" name="txt_selected_po" id="txt_selected_po" value="" />
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1295" class="rpt_table"  >
        <thead>
            <th width="30">SL</th>
            <th width="50">Buyer</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="60">File No</th>
            <th width="80">Ref. No</th>
            <th width="100">Style No</th>
            <th width="100">Ord. No</th>
            <th width="100">Trim Group</th>
            <th width="">Desc.</th>
            <th width="100">Brand/Sup.Ref</th>
            <th width="80">Req. Qnty</th>
            <th width="45">UOM</th>
            <th width="80">CU WOQ</th>
            <th width="80">Bal WOQ</th>
            <th width="45">Exch. Rate</th>
            <th width="40">Rate</th>
            <th width="70">Amount</th>
        </thead>
	</table>
	<div style="width:1315px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" >
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1295" class="rpt_table" id="tbl_list_search" >
		<?
        if($db_type==0){
            if ($start_date!="" &&  $end_date!="") $class_datecond = "between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $class_datecond ="";
        }
        else if($db_type==2){
            if ($start_date!="" &&  $end_date!="") $class_datecond = "between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $class_datecond ="";
        }
    
        $trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
        $sql_lib_item_group_array=array();
        $sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
        foreach($sql_lib_item_group as $row_sql_lib_item_group){
            $sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
            $sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
            $sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
        }
        $sql_vari_lib="select item_category_id,variable_list,excut_source  from variable_order_tracking where company_name=".$company_id." and item_category_id=4  and variable_list=72 and status_active=1"; 
        //echo "select item_category_id,variable_list,excut_source  from variable_order_tracking where company_name=".$cbo_company_name." and item_category_id=4  and variable_list=72 and status_active=1";
        $result_vari_lib=sql_select($sql_vari_lib);
        $source_from=1;//$woven_category_id=0;
        foreach($result_vari_lib as $row)
        {
            //$woven_category_id=$row[csf('item_category_id')];
            if($row[csf('excut_source')]>0)
            {
                $source_from=$row[csf('excut_source')];
            }
        }
        
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
        // echo $source_from;
        $condition->init();
        $trims= new trims($condition);
        $req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
        if($source_from==2) //Sourcing Budget pAGE	
        {
            $req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidSourcing();
            //print_r($req_amount_arr);
        }
        else
        {
            $req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
        }
        //echo $cbo_buyer_name;die;
        $cu_booking_arr=array();
        $sql_cu_booking=sql_select("select c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$company_id and a.buyer_name=$cbo_buyer_name  and c.status_active=1 and c.is_deleted=0 $job_cond $order_cond $ref_cond $style_cond group by a.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");
        foreach($sql_cu_booking as $row_cu_booking){
            $cu_booking_arr[$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('po_break_down_id')]]['cu_wo_qnty']=$row_cu_booking[csf('cu_wo_qnty')];
            $cu_booking_arr[$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('po_break_down_id')]]['cu_amount']=$row_cu_booking[csf('cu_amount')];
        }
        
        $sql=sql_select("select b.approval_need, b.validate_page, b.allow_partial, b.page_id from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and b.page_id in (36,37) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
        $app_nessity=2; $validate_page=0; $allow_partial=2; $woven_app_nessity=1;
        foreach($sql as $row){
        	if($row[csf('page_id')]==36)
        	{
        		$app_nessity=$row[csf('approval_need')];
	            $validate_page=$row[csf('validate_page')];
	            $allow_partial=$row[csf('allow_partial')];
        	}
        	if($row[csf('page_id')]==37)
        	{
        		$woven_app_nessity=$row[csf('approval_need')];
        	}
            
        }
        $sourcingAppCond="";//Dont HIde Issue id ISD-21-04463
        if($app_nessity==1)
        {
             if($allow_partial==1) $sourcingAppCond=" and b.sourcing_approved in (1,3)";
             else $sourcingAppCond=" and b.sourcing_approved=1";
        }
        $pre_cost_approval="";
        if($woven_app_nessity==1)
        {
        	$pre_cost_approval= " and b.approved=1";
        }
    
        $sql="SELECT a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, c.description, c.brand_sup_ref, c.rate, d.id as po_id, d.po_number, d.file_no, d.grouping, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) AS cons from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and a.company_name=$company_id  and a.buyer_name=$cbo_buyer_name  and (c.nominated_supp = $cbo_supplier_name or c.nominated_supp= 0)  and d.is_deleted=0 and d.status_active=1 ".$buyer_cond_test."$itemgroup_cond $job_cond $order_cond $ref_cond $style_cond $sourcingAppCond $pre_cost_approval group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.rate, a.insert_date, d.id, d.po_number, d.file_no, d.grouping, d.po_quantity, e.po_break_down_id order by d.id,c.id";
    
        //echo $sql;
        $i=1;
        $total_req=0;
        $total_amount=0;
        $nameArray=sql_select( $sql );
        foreach ($nameArray as $selectResult)
        {
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            $cbo_currency_job=$selectResult[csf('currency_id')];
            $exchange_rate=$selectResult[csf('exchange_rate')];
            if($cbo_currency==$cbo_currency_job) $exchange_rate=1;
            
            $req_qnty_cons_uom=$req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
            $req_amount_cons_uom=$req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
            $rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;
        
            $req_qnty=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
            $cu_wo_qnty=$cu_booking_arr[$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('po_id')]]['cu_wo_qnty'];
            $bal_woq=def_number_format($req_qnty-$cu_wo_qnty,5,"");
        
            $rate=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
            $req_amount=def_number_format($req_qnty*$rate,5,"");
        
            $total_req_amount+=$req_amount;
            $total_cu_amount+=$selectResult[csf('cu_amount')];
        
            $total_req+=$req_qnty;
            $amount=def_number_format($rate*$bal_woq,4,"");
            //  echo $req_qnty.'='.$req_amount_cons_uom.'='.$rate_cons_uom.'<br>';;
           
            $ig=1;
            //echo $bal_woq.'='.$cu_wo_qnty;
            if($bal_woq <= 0 && ($cu_wo_qnty !="" || $cu_wo_qnty !=0) && $ig=0)
            {
                ?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                    <td width="30"><?=$i;?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                    <input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')]; ?>"/>
                    <input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
                    <input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
					<input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<?=$selectResult[csf('trim_group')];?>"/>
                    </td>
                    <td width="50"><p><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></p></td>
                    <td width="50"><p><? echo $selectResult[csf('year')];?></p></td>
                    <td width="50"><p><? echo $selectResult[csf('job_no_prefix_num')];?></p></td>
                    <td width="60"><p><? echo $selectResult[csf('file_no')];?></p></td>
                    <td width="80"><p><? echo $selectResult[csf('grouping')];?></p></td>
                    <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')];?></div></td>
                    <td width="100"><p><? echo $selectResult[csf('po_number')];?></p></td>
                
                    <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $trim_group_library[$selectResult[csf('trim_group')]];?></div></td>
                    <td width=""><div style="width:120px; word-wrap:break-word;"><? echo $selectResult[csf('description')];?></div></td>
                    <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('brand_sup_ref')];?></div></td>
                    <td width="80" align="right"><?=number_format($req_qnty,4); ?></td>
                    <td width="45"><? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?></td>
                    <td width="80" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
                    <td width="80" align="right"><?=number_format($bal_woq,4); ?></td>
                    <td width="45" align="right"><p><?=number_format($exchange_rate,2); ?></p></td>
                    <td width="40" align="right"><p><?=number_format($rate,4); ?></p></td>
                    <td width="70" align="right"><?=number_format($amount,2); ?></td>
                </tr>
            <?
            $i++;
			$total_amount+=$amount;
            }
            if($bal_woq <=1 && $cu_wo_qnty>0)
            {
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                    <td width="30"><? echo $i;?>
                        <input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i; ?>" value="<?=$selectResult[csf('id')]; ?>"/>
                        <input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?=$i; ?>" value="<?=$selectResult[csf('wo_pre_cost_trim_cost_dtls')]; ?>"/>
                        <input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
                        <input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
						<input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<?=$selectResult[csf('trim_group')];?>"/>
                    </td>
                    <td width="50"><p><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></p></td>
                    <td width="50"><p><? echo $selectResult[csf('year')];?></p></td>
                    <td width="50"><p><? echo $selectResult[csf('job_no_prefix_num')];?></p></td>
                    <td width="60"><p><? echo $selectResult[csf('file_no')];?></p></td>
                    <td width="80"><p><? echo $selectResult[csf('grouping')];?></p></td>
                    <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')];?></div></td>
                    <td width="100"><p><? echo $selectResult[csf('po_number')];?></p></td>
                
                    <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $trim_group_library[$selectResult[csf('trim_group')]];?></div></td>
                    <td><div style="width:120px; word-wrap:break-word;"><? echo $selectResult[csf('description')];?></div></td>
                    <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('brand_sup_ref')];?></div></td>
                    <td width="80" align="right"><?=number_format($req_qnty,4); ?></td>
                    <td width="45"><? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?></td>
                    <td width="80" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
                    <td width="80" align="right"><?=number_format($bal_woq,4); ?></td>
                    <td width="45" align="right"><p><?=number_format($exchange_rate,2); ?></p></td>
                    <td width="40" align="right"><p><?=number_format($rate,4); ?></p></td>
                    <td width="70" align="right"><?=number_format($amount,2); ?></td>
                </tr>
                <?
                $i++;
				$total_amount+=$amount;
            }
        }
        ?>
		<tfoot>
            <th width="30">&nbsp;</th>
            <th width="50">&nbsp;</th>
            <th width="50">&nbsp;</th>
            <th width="50">&nbsp;</th>
            <th width="60">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="80" id="value_total_req">&nbsp;</th>
            <th width="45"><input type="hidden" style="width:40px"  id="txt_tot_req_amount" value="<? echo number_format($total_req_amount,2); ?>" /></th>
            <th width="80"><input type="hidden" style="width:40px" id="txt_tot_cu_amount" value="<? echo number_format($total_cu_amount,2); ?>" /></th>
            <th width="80">&nbsp;</th>
            <th width="45">&nbsp;</th>
            <th width="40">&nbsp;</th>
            <th width="70" id="value_total_amount"><? echo number_format($total_amount,2); ?></th>
        </tfoot>
	</table>
	</div>
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
			id: ["value_total_req","value_total_amount"],
			col: [11,17],
			operation: ["sum","sum"],
			write_method: ["innerHTML","innerHTML"]
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

if ($action=="generate_fabric_booking"){
	extract($_REQUEST);
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$booking_month=0;
	if($cbo_booking_month<10){
		$booking_month.=$cbo_booking_month;
	}
	else{
		$booking_month=$cbo_booking_month;
	}
	if($garments_nature==0){
		$garment_nature_cond="";
	}
	else{
		$garment_nature_cond=" and a.garments_nature=$garments_nature";
	}

	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
	$param=implode(",",array_unique(explode(",",str_replace("'","",$param))));
	$data=implode(",",array_unique(explode(",",str_replace("'","",$data))));
	$pre_cost_id=implode(",",array_unique(explode(",",str_replace("'","",$pre_cost_id))));

	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}
$sql_vari_lib="select item_category_id,variable_list,excut_source  from variable_order_tracking where company_name=".$cbo_company_name." and item_category_id=4  and variable_list=72 and status_active=1"; 
	//echo "select item_category_id,variable_list,excut_source  from variable_order_tracking where company_name=".$cbo_company_name." and item_category_id=4  and variable_list=72 and status_active=1";
	$result_vari_lib=sql_select($sql_vari_lib);
	$source_from=1;//$woven_category_id=0;
	foreach($result_vari_lib as $row)
	{
		//$woven_category_id=$row[csf('item_category_id')];
		if($row[csf('excut_source')]>0)
		{
		$source_from=$row[csf('excut_source')];
		}
	}

	$condition= new condition();
	if(str_replace("'","",$data) !=''){
		$condition->po_id("in($data)");
	}

	$condition->init();
	$trims= new trims($condition);
	$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	if($source_from==2) //Sourcing Budget pAGE	
	{
		
	$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidSourcing();
	//print_r($req_amount_arr);
	}
	else
	{
	$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
	}

	$cu_booking_arr=array();
	$sql_cu_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name $shipment_date and c.status_active=1 and c.is_deleted=0   group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");
	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_amount')];
	}

	$sql="SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) as cons from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and a.company_name=$cbo_company_name   $garment_nature_cond and e.id in($param) and e.po_break_down_id in($data) and c.id in($pre_cost_id) and d.is_deleted=0 and d.status_active=1 group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id order by d.id,c.id";
	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );

	foreach ($nameArray as $selectResult){
		$cbo_currency_job=$selectResult[csf('currency_id')];
		$exchange_rate=$selectResult[csf('exchange_rate')];
		if($cbo_currency==$cbo_currency_job){
			$exchange_rate=1;
		}

		$req_qnty_cons_uom=$req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
		$req_amount_cons_uom=$req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
		$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

		$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
		$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
		$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");

		$cu_woq=$cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_woq'][$selectResult[csf('po_id')]];
		$cu_amount=$cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_amount'][$selectResult[csf('po_id')]];

		$bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
		$amount=def_number_format($rate_ord_uom*$bal_woq,5,"");



		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['job_no'][$selectResult[csf('po_id')]]=$selectResult[csf('job_no')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['po_id'][$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['po_number'][$selectResult[csf('po_id')]]=$selectResult[csf('po_number')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['country'][$selectResult[csf('po_id')]]=$selectResult[csf('country')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['description'][$selectResult[csf('po_id')]]=$selectResult[csf('description')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['brand_sup_ref'][$selectResult[csf('po_id')]]=$selectResult[csf('brand_sup_ref')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['trim_group'][$selectResult[csf('po_id')]]=$selectResult[csf('trim_group')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['trim_group_name'][$selectResult[csf('po_id')]]=$trim_group_library[$selectResult[csf('trim_group')]];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['wo_pre_cost_trim_cost_dtls'][$selectResult[csf('po_id')]]=$selectResult[csf('wo_pre_cost_trim_cost_dtls')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['req_qnty'][$selectResult[csf('po_id')]]=$req_qnty_ord_uom;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['uom'][$selectResult[csf('po_id')]]=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['uom_name'][$selectResult[csf('po_id')]]=$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['req_amount'][$selectResult[csf('po_id')]]=$req_amount_ord_uom;

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_woq'][$selectResult[csf('po_id')]]=$cu_woq;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_amount'][$selectResult[csf('po_id')]]=$cu_amount;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['bal_woq'][$selectResult[csf('po_id')]]=$bal_woq;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['exchange_rate'][$selectResult[csf('po_id')]]=$exchange_rate;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['rate'][$selectResult[csf('po_id')]]=$rate_ord_uom;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['amount'][$selectResult[csf('po_id')]]=$amount;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['txt_delivery_date'][$selectResult[csf('po_id')]]=$txt_delivery_date;
	}
	?>
	<input type="hidden" id="strdata" value='<? echo json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" >
	<thead>
	<th width="40">SL</th>
	<th width="80">Job No</th>
	<th width="100">Ord. No</th>
	<th width="100">Trims Group</th>
	<th width="70">Req. Qnty</th>
	<th width="50">UOM</th>
	<th width="80">CU WOQ</th>
	<th width="80">Bal WOQ</th>
	<th width="100">Sensitivity</th>
	<th width="80">WOQ</th>
	<th width="55">Exch.Rate</th>
	<th width="80">Rate</th>
	<th width="80">Amount</th>
	<th width="">Delv. Date</th>
	</thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="tbl_list_search" >
	<tbody>
	<?
	if($cbo_level==1){
	foreach ($nameArray as $selectResult){
	if ($i%2==0)
	$bgcolor="#E9F3FF";
	else
	$bgcolor="#FFFFFF";

	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	if($cbo_currency == $cbo_currency_job){
		$exchange_rate=1;
	}

	$req_qnty_cons_uom = $req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
	$req_amount_cons_uom = $req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
	$rate_cons_uom = $req_amount_cons_uom/$req_qnty_cons_uom;

	$req_qnty_ord_uom = def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
	$rate_ord_uom = def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
	$req_amount_ord_uom = def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");

	$cu_woq = $cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_woq'][$selectResult[csf('po_id')]];
	$cu_amount = $cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_amount'][$selectResult[csf('po_id')]];
	$bal_woq = def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
	$amount = def_number_format($bal_woq*$rate_ord_uom,5,"");
	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
	<td width="40"><? echo $i;?></td>
	<td width="80">
	<? echo $selectResult[csf('job_no')];?>
	<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $selectResult[csf('job_no')];?>" style="width:30px" class="text_boxes" readonly/>
	</td>
	<td width="100">
	<? echo $selectResult[csf('po_number')];?>
	<input type="hidden" id="txtbookingid_<? echo $i;?>" value="" readonly/>
	<input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
	<input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $selectResult[csf('country')] ?>" readonly />
	<input type="hidden" id="txtdesc_<? echo $i;?>"  value="<? echo $selectResult[csf('description')];?>" readonly />
	<input type="hidden" id="txtbrandsup_<? echo $i;?>"  value="<? echo $selectResult[csf('brand_sup_ref')];?>" readonly />
	</td>
	<td width="100"  title="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor];  ?>">
	<? echo $trim_group_library[$selectResult[csf('trim_group')]];?>
	<input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>" readonly/>
	<input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $selectResult[csf('trim_group')];?>" readonly/>
	</td>
	<td width="70" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
	<input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
	</td>
	<td width="50">
	<? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?>
	<input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];?>" readonly />
	</td>
	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($selectResult[csf('cu_woq')],4,'.','');?>"  readonly  />
	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($selectResult[csf('cu_amount')],4,'.','');?>"  readonly  />
	</td>
	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.',''); ?>"  readonly  />
	</td>
	<td width="100" align="right">
	<?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?>
	</td>
	<td width="80" align="right">

	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? //echo number_format($bal_woq,4,'.','');?>" onClick="open_consumption_popup('requires/short_trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly />
	</td>
	<td width="55" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />

	</td>
	<td width="80" align="right">
	<?
	$ratetexcolor="#000000";
	$decimal=explode(".",$rate_ord_uom);
	if(strlen($decimal[1]>6)){
	$ratetexcolor="#F00";
	}
	?>
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   text-align:right; color:<? echo $ratetexcolor; ?>; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate_ord_uom ;?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />

	</td>
	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
	</td>
	<td width="" align="right">
	<input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo $txt_delivery_date; ?>"  readonly  />
	<input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
	<input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
	</td>
	</tr>
	<?
	$i++;
	$total_amount += $amount;
	}
	}
	if($cbo_level==2){
	?>
	<?
	$total_amount=0;
	$i=1;
	foreach ($job_and_trimgroup_level as $job_no){
	foreach ($job_no as $wo_pre_cost_trim_cost_dtls){
	$job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
	$po_number=implode(",",$wo_pre_cost_trim_cost_dtls['po_number']);
	$po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
	$country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
	$description=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['description']));
	$brand_sup_ref=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['brand_sup_ref']));
	$wo_pre_cost_trim_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_trim_cost_dtls']));
	$trim_group = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['trim_group']));
	$uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));



	$req_qnty_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
	$rate_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount'])/array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
	$req_amount_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount']);

	$bal_woq=array_sum($wo_pre_cost_trim_cost_dtls['bal_woq']);
	$amount=array_sum($wo_pre_cost_trim_cost_dtls['amount']);

	$cu_woq=array_sum($wo_pre_cost_trim_cost_dtls['cu_woq']);
	$cu_amount=array_sum($wo_pre_cost_trim_cost_dtls['cu_amount']);

	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
	<td width="40"><? echo $i;?></td>
	<td width="80">
	<? echo $job_no?>
	<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>" style="width:30px" class="text_boxes" readonly/>
	</td>
	<td width="100" style="word-wrap:break-word;word-break: break-all">
	<? echo $po_number; ?>
	<input type="hidden" id="txtbookingid_<? echo $i;?>" value="" readonly/>
	<input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_id; ?>" readonly/>
	<input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $country; ?>" readonly />
	<input type="hidden" id="txtdesc_<? echo $i;?>"  value="<? echo $description; ?>" readonly />
	<input type="hidden" id="txtbrandsup_<? echo $i;?>"  value="<? echo $brand_sup_ref;?>" readonly />
	</td>
	<td width="100"  title="<? echo $sql_lib_item_group_array[$trim_group][conversion_factor];  ?>">
	<? echo $trim_group_library[$trim_group];?>
	<input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $wo_pre_cost_trim_id;?>" readonly/>
	<input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $trim_group;?>" readonly/>
	</td>
	<td width="70" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
	<input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
	</td>
	<td width="50">
	<?  echo $unit_of_measurement[$uom];?>
	<input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $uom;?>" readonly />
	</td>
	<td width="80" align="right">

	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.',''); ?>"  readonly  />
	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($cu_amount,4,'.','');?>"  readonly  />
	</td>
	<td width="80" align="right">
	<?
	?>
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.','');?>"  readonly  />
	</td>
	<td width="100" align="right">
	<?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?>
	</td>
	<td width="80" align="right">

	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? //echo number_format($bal_woq,4,'.','');?>" onClick="open_consumption_popup('requires/short_trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly/>
	</td>
	<td width="55" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />

	</td>
	<td width="80" align="right">
	<?
	$ratetexcolor="#000000";
	$decimal=explode(".",$rate_ord_uom);

	if(strlen($decimal[1])>6){
	$ratetexcolor="#F00";
	}
	//echo strlen($decimal[1]);
	?>
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; color:<? echo $ratetexcolor;  ?>;  background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate_ord_uom ;?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />

	</td>

	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
	</td>
	<td width="" align="right">
	<input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo $txt_delivery_date; ?>"  readonly  />
	<input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
	<input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
	</td>
	</tr>
	<?
	$i++;
	$total_amount += $amount;
	}
	}
	?>
	<?
	}
	?>
	</tbody>
	</table>
	<table width="1100" class="rpt_table" border="0" rules="all">
	<tfoot>
	<tr>
	<th width="40">&nbsp;</th>
	<th width="80"></th>
	<th width="100"></th>
	<th width="100"></th>
	<th width="70"><? echo $tot_req_qty; ?></th>
	<th width="50"></th>
	<th width="80"><? echo $tot_cu_woq; ?></th>
	<th width="80"><? echo $tot_bal_woq; ?></th>
	<th width="100"></th>
	<th width="80"></th>
	<th width="55"></th>
	<th width="80">Total</th>
	<th width="80" id="total_amount"><input type="text" id="tot_amount" value="<? echo  $total_amount; ?>" style="width:80px; text-align:right " readonly /></th>
	<th width=""><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
	</tr>
	</tfoot>
	</table>
    <table width="1100" colspan="14" cellspacing="0" class="" border="0">
    <tr>
    <td align="center"class="button_container">
    <?
    echo load_submit_buttons( $permission, "fnc_trims_booking_dtls", 0,0,"reset_form('','booking_list_view','','','')",2) ;
    ?>
    </td>
    </tr>
    </table>
	<?
	exit();
}

if ($action=="show_trim_booking"){
	extract($_REQUEST);
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$booking_month=0;
	if($cbo_booking_month<10){
		$booking_month.=$cbo_booking_month;
	}
	else{
		$booking_month=$cbo_booking_month;
	}
	if($garments_nature==0){
		$garment_nature_cond="";
	}
	else{
		$garment_nature_cond=" and a.garments_nature=$garments_nature";
	}

	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
	if($db_type==0){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}

	if($db_type==2){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}

	$condition= new condition();
	if(str_replace("'","",$data) !=''){
		$condition->po_id("in($data)");
	}
	$condition->init();
	$trims= new trims($condition);
	$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();

	$cu_booking_arr=array();
	$sql_cu_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");
	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_amount')];
	}

	$sql="select
	a.job_no_prefix_num,
	a.job_no,
	a.company_name,
	a.buyer_name,
	a.currency_id,
	a.style_ref_no,
	b.costing_per,
	b.exchange_rate,
	c.id as wo_pre_cost_trim_cost_dtls,
	c.trim_group,
	g.description,
	c.brand_sup_ref,
	c.country,
	c.rate,
	d.id as po_id,
	d.po_number,
	d.po_quantity as plan_cut,
	min(e.id) as id,
	e.po_break_down_id,
	avg(e.cons) as cons,
	sum(f.wo_qnty) as cu_woq,
	sum(f.amount) as cu_amount,
	f.id as booking_id,
	f.sensitivity,
	f.delivery_date

	from
	wo_po_details_master a,
	wo_pre_cost_mst b,
	wo_pre_cost_trim_cost_dtls c,
	wo_po_break_down d,
	wo_pre_cost_trim_co_cons_dtls e,
	wo_booking_dtls f
	left join wo_trim_book_con_dtls g on f.id=g.wo_trim_booking_dtls_id
	where
	a.job_no=b.job_no and
	a.job_no=c.job_no and
	a.job_no=d.job_no_mst and
	a.job_no=e.job_no and
	a.job_no=f.job_no and
	c.id=e.wo_pre_cost_trim_cost_dtls_id and
	d.id=e.po_break_down_id and
	e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and
	e.po_break_down_id=f.po_break_down_id and
	f.booking_type=2 and
	f.booking_no=$txt_booking_no and
	f.id in($booking_id) and
	a.company_name=$cbo_company_name   $garment_nature_cond and
	e.wo_pre_cost_trim_cost_dtls_id=$pre_cost_id and
	d.is_deleted=0 and
	d.status_active=1 and
	f.status_active=1 and
	f.is_deleted=0

	group by
	a.job_no_prefix_num,
	a.job_no,
	a.company_name,
	a.buyer_name,
	a.currency_id,
	a.style_ref_no,
	b.costing_per,
	b.exchange_rate,
	c.id,
	c.trim_group,
	g.description,
	c.brand_sup_ref,
	c.country,
	c.rate,
	d.id,
	d.po_number,
	d.po_quantity,
	e.po_break_down_id,
	f.id,
	f.sensitivity,
	f.delivery_date
	order by d.id,c.id";

	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $selectResult){
	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	if($cbo_currency==$cbo_currency_job){
		$exchange_rate=1;
	}

	$req_qnty_cons_uom=$req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
	$req_amount_cons_uom=$req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
	$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

	$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
	$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
	$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");

	$cu_woq=$cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_woq'][$selectResult[csf('po_id')]];
	$cu_amount=$cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_amount'][$selectResult[csf('po_id')]];

	$bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
	$amount=def_number_format($rate_ord_uom*$bal_woq,5,"");

	$total_req_amount+=$req_amount;
	$total_cu_amount+=$selectResult[csf('cu_amount')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['job_no'][$selectResult[csf('po_id')]]=$selectResult[csf('job_no')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['po_id'][$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['po_number'][$selectResult[csf('po_id')]]=$selectResult[csf('po_number')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['country'][$selectResult[csf('po_id')]]=$selectResult[csf('country')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['description'][$selectResult[csf('po_id')]]=$selectResult[csf('description')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['brand_sup_ref'][$selectResult[csf('po_id')]]=$selectResult[csf('brand_sup_ref')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['trim_group'][$selectResult[csf('po_id')]]=$selectResult[csf('trim_group')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['trim_group_name'][$selectResult[csf('po_id')]]=$trim_group_library[$selectResult[csf('trim_group')]];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['wo_pre_cost_trim_cost_dtls'][$selectResult[csf('po_id')]]=$selectResult[csf('wo_pre_cost_trim_cost_dtls')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['req_qnty'][$selectResult[csf('po_id')]]=$req_qnty_ord_uom;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['uom'][$selectResult[csf('po_id')]]=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['uom_name'][$selectResult[csf('po_id')]]=$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['req_amount'][$selectResult[csf('po_id')]]=$req_amount_ord_uom;

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_woq'][$selectResult[csf('po_id')]]=$cu_woq;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_amount'][$selectResult[csf('po_id')]]=$cu_amount;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['bal_woq'][$selectResult[csf('po_id')]]=$bal_woq;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['exchange_rate'][$selectResult[csf('po_id')]]=$exchange_rate;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['rate'][$selectResult[csf('po_id')]]=$rate_ord_uom;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['amount'][$selectResult[csf('po_id')]]=$amount;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['txt_delivery_date'][$selectResult[csf('po_id')]]=$selectResult[csf('delivery_date')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['booking_id'][$selectResult[csf('po_id')]]=$selectResult[csf('booking_id')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['sensitivity'][$selectResult[csf('po_id')]]=$selectResult[csf('sensitivity')];
	}

	$sql_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as wo_qnty, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id  and c.id in($booking_id) and c.booking_type=2 and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");
	foreach($sql_booking as $row_booking){
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]]['woq'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]]['amount'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('amount')];
	}
	?>

    <input type="hidden" id="strdata" value='<? echo json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
	<thead>
	<th width="40">SL</th>
	<th width="80">Job No</th>
	<th width="100">Ord. No</th>
	<th width="100">Trims Group</th>
	<th width="150">Description</th>
	<th width="70">Req. Qnty</th>
	<th width="50">UOM</th>
	<th width="80">CU WOQ</th>
	<th width="80">Bal WOQ</th>
	<th width="100">Sensitivity</th>
	<th width="80">WOQ</th>
	<th width="55">Exch.Rate</th>
	<th width="80">Rate</th>
	<th width="80">Amount</th>
	<th width="">Delv. Date</th>
	</thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" id="tbl_list_search" >
	<tbody>
	<?
	if($cbo_level==1){
	foreach ($nameArray as $selectResult){
	if ($i%2==0)
	$bgcolor="#E9F3FF";
	else
	$bgcolor="#FFFFFF";

	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	if($cbo_currency==$cbo_currency_job){
		$exchange_rate=1;
	}

	$req_qnty_cons_uom=$req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
	$req_amount_cons_uom=$req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
	$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

	$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
	$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
	$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");

	$cu_woq=$cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_woq'][$selectResult[csf('po_id')]];
	$cu_amount=$cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_amount'][$selectResult[csf('po_id')]];

	$bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,5,"");


	$woq=$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['woq'][$selectResult[csf('po_id')]];
	$amount=$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['amount'][$selectResult[csf('po_id')]];
	$rate=$amount/$woq;
	$total_amount+=$amount;



	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
	<td width="40"><? echo $i;?></td>
	<td width="80">
	<? echo $selectResult[csf('job_no')];?>
	<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $selectResult[csf('job_no')];?>" style="width:30px" class="text_boxes" readonly/>
	</td>
	<td width="100">
	<? echo $selectResult[csf('po_number')];?>
	<input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $selectResult[csf('booking_id')];?>" readonly/>
	<input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
	<input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $selectResult[csf('country')] ?>" readonly />
	<input type="hidden" id="txtdesc_<? echo $i;?>"  value="<? echo $selectResult[csf('description')];?>" readonly />
	<input type="hidden" id="txtbrandsup_<? echo $i;?>"  value="<? echo $selectResult[csf('brand_sup_ref')];?>" readonly />
	</td>
	<td width="100"  title="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor];  ?>">
	<? echo $trim_group_library[$selectResult[csf('trim_group')]];?>
	<input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>" readonly/>
	<input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $selectResult[csf('trim_group')];?>" readonly/>
	</td>
	<td width="150"><? echo $selectResult[csf('description')];?></td>

	<td width="70" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
	</td>
	<td width="50">
	<? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?>
	<input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];?>" readonly />
	</td>
	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.','');?>"  readonly  />
	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($cu_amount,4,'.','');?>"  readonly  />
	</td>
	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.',''); ?>"  readonly  />
	</td>
	<td width="100" align="right">
	<?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $selectResult[csf("sensitivity")], "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?>
	</td>
	<td width="80" align="right">

	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($woq,4,'.','');?>" onClick="open_consumption_popup('requires/short_trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly />
	</td>
	<td width="55" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />

	</td>
	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />

	</td>
	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
	</td>
	<td width="" align="right">
	<input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?>"  readonly  />
	<input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
	<input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
	</td>
	</tr>
	<?
	$i++;
	}
	}

	if($cbo_level==2){
	$i=1;
	foreach ($job_and_trimgroup_level as $job_no){
	foreach ($job_no as $wo_pre_cost_trim_cost_dtls){
	$job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
	$po_number=implode(",",$wo_pre_cost_trim_cost_dtls['po_number']);
	$po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
	$country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
	$description=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['description']));
	$brand_sup_ref=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['brand_sup_ref']));
	$wo_pre_cost_trim_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_trim_cost_dtls']));
	$trim_group = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['trim_group']));
	$uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
	$booking_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['booking_id']));
	$sensitivity=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['sensitivity']));
	$delivery_date=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['txt_delivery_date']));

	$req_qnty_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
	$rate_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount'])/array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
	$req_amount_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount']);

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
	<td width="80">
	<? echo $job_no?>
	<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>" style="width:30px" class="text_boxes" readonly/>
	</td>
	<td width="100" style="word-wrap:break-word;word-break: break-all">
	<? echo $po_number; ?>
	<input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $booking_id; ?>" readonly/>
	<input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_id; ?>" readonly/>
	<input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $country; ?>" readonly />
	<input type="hidden" id="txtdesc_<? echo $i;?>"  value="<? echo $description; ?>" readonly />
	<input type="hidden" id="txtbrandsup_<? echo $i;?>"  value="<? echo $brand_sup_ref;?>" readonly />
	</td>
	<td width="100"  title="<? echo $sql_lib_item_group_array[$trim_group][conversion_factor];  ?>">
	<? echo $trim_group_library[$trim_group];?>
	<input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $wo_pre_cost_trim_id;?>" readonly/>
	<input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $trim_group;?>" readonly/>
	</td>
	<td width="150"><? echo $description;?></td>
	<td width="70" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
	</td>
	<td width="50">
	<?  echo $unit_of_measurement[$uom];?>
	<input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $uom;?>" readonly />
	</td>
	<td width="80" align="right">

	<input type="text"  style="width:100%; height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.',''); ?>"  readonly  />
	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo $cu_amount;?>"  readonly  />
	</td>
	<td width="80" align="right">
	<?
	?>
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.','');?>"  readonly  />
	</td>
	<td width="100" align="right">
	<?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $sensitivity, "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?>
	</td>
	<td width="80" align="right">

	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($woq,4,'.','');?>" onClick="open_consumption_popup('requires/short_trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly />
	</td>
	<td width="55" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />
	</td>
	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>" onChange="calculate_amount(<? echo $i; ?>)" readonly/>
	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />
	</td>
	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
	</td>
	<td width="" align="right">
	<input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>"  readonly  />
	<input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
	<input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
	</td>
	</tr>
	<?
	$i++;
	}
	}
	}
	?>
	</tbody>
	</table>
	<table width="1250" class="rpt_table" border="0" rules="all">
	<tfoot>
	<tr>
	<th width="40">&nbsp;</th>
	<th width="80"></th>
	<th width="100"></th>
	<th width="100"></th>
	<th width="150"></th>
	<th width="70"><? echo $tot_req_qty; ?></th>
	<th width="50"></th>
	<th width="80"><? echo $tot_cu_woq; ?></th>
	<th width="80"><? echo $tot_bal_woq; ?></th>
	<th width="100"></th>
	<th width="80"></th>
	<th width="55"></th>
	<th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/><? //echo  $total_amount; ?></th>
    <th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/><? //echo  $total_amount; ?></th>
	<th width=""><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
	</tr>
	</tfoot>
	</table>
     <table width="1100" colspan="14" cellspacing="0" class="" border="0">
    <tr>
    <td align="center"class="button_container">
    <?
    echo load_submit_buttons( $permission, "fnc_trims_booking_dtls", 1,0,"reset_form('','booking_list_view','','','')",2) ;
    ?>
    </td>
    </tr>
    </table>
	<?
}


if ($action=="show_trim_booking_list"){
	extract($_REQUEST);
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$booking_month=0;
	if($cbo_booking_month<10){
		$booking_month.=$cbo_booking_month;
	}
	else{
		$booking_month=$cbo_booking_month;
	}
	if($garments_nature==0){
		$garment_nature_cond="";
	}
	else{
		$garment_nature_cond=" and a.garments_nature=$garments_nature";
	}

	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
	if($db_type==0){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}

	if($db_type==2){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}



	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}

	//  $sql="SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, f.description, c.brand_sup_ref, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=2 and f.booking_no=$txt_booking_no and a.company_name=$cbo_company_name   $garment_nature_cond and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date order by d.id,c.id";
	 
	 $sql="SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, g.description, c.brand_sup_ref, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f left join wo_trim_book_con_dtls g on f.id=g.wo_trim_booking_dtls_id 
	 where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=2 and f.booking_no=$txt_booking_no and a.company_name=$cbo_company_name   $garment_nature_cond and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, g.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date order by d.id,c.id";
	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $selectResult){
	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	if($cbo_currency==$cbo_currency_job){
		$exchange_rate=1;
	}
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['job_no'][$selectResult[csf('po_id')]]=$selectResult[csf('job_no')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['po_id'][$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['po_number'][$selectResult[csf('po_id')]]=$selectResult[csf('po_number')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['country'][$selectResult[csf('po_id')]]=$selectResult[csf('country')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['description'][$selectResult[csf('po_id')]]=$selectResult[csf('description')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['brand_sup_ref'][$selectResult[csf('po_id')]]=$selectResult[csf('brand_sup_ref')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['trim_group'][$selectResult[csf('po_id')]]=$selectResult[csf('trim_group')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['trim_group_name'][$selectResult[csf('po_id')]]=$trim_group_library[$selectResult[csf('trim_group')]];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['wo_pre_cost_trim_cost_dtls'][$selectResult[csf('po_id')]]=$selectResult[csf('wo_pre_cost_trim_cost_dtls')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['uom'][$selectResult[csf('po_id')]]=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['uom_name'][$selectResult[csf('po_id')]]=$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];


	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['cu_woq'][$selectResult[csf('po_id')]]=$cu_woq;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['cu_amount'][$selectResult[csf('po_id')]]=$cu_amount;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['bal_woq'][$selectResult[csf('po_id')]]=$bal_woq;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['exchange_rate'][$selectResult[csf('po_id')]]=$exchange_rate;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['rate'][$selectResult[csf('po_id')]]=$rate_ord_uom;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['amount'][$selectResult[csf('po_id')]]=$amount;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['txt_delivery_date'][$selectResult[csf('po_id')]]=$selectResult[csf('delivery_date')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['booking_id'][$selectResult[csf('po_id')]]=$selectResult[csf('booking_id')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['sensitivity'][$selectResult[csf('po_id')]]=$selectResult[csf('sensitivity')];
	}

	$sql_booking=sql_select("SELECT c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.sensitivity, sum(c.wo_qnty) as wo_qnty, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.booking_type=2 and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,sensitivity");
	foreach($sql_booking as $row_booking){
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]]['woq'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]]['amount'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('amount')];
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" >
	<thead>
	<th width="40">SL</th>
	<th width="100">Job No</th>
	<th width="100">Ord. No</th>
	<th width="100">Trims Group</th>
	<th width="100">Description</th>
	<th width="80">UOM</th>
	<th width="100">Sensitivity</th>
	<th width="80">WOQ</th>
	<th width="80">Exch.Rate</th>
	<th width="80">Rate</th>
	<th width="80">Amount</th>
	<th width="">Delv. Date</th>
	</thead>
	<tbody>
	<?
	if($cbo_level==1){
	foreach ($nameArray as $selectResult){
	if ($i%2==0)
	$bgcolor="#E9F3FF";
	else
	$bgcolor="#FFFFFF";

	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	if($cbo_currency==$cbo_currency_job){
		$exchange_rate=1;
	}
	$woq=def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['woq'][$selectResult[csf('po_id')]],5,"");
	$amount=def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]]['amount'][$selectResult[csf('po_id')]],5,"");
	$rate=def_number_format($amount/$woq,5,"");
	//$total_amount+=$amount;
	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="fnc_show_booking(<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>,'<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('booking_id')];?>')">
	<td width="40"><? echo $i;?></td>
	<td width="100">
	<? echo $selectResult[csf('job_no')];?>
	</td>
	<td width="100">
	<? echo $selectResult[csf('po_number')];?>
	</td>
	<td width="100"  title="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor];  ?>">
	<? echo $trim_group_library[$selectResult[csf('trim_group')]];?>
	</td>
	<td width="100"><? echo $selectResult[csf('description')];?></td>

	<td width="80">
	<? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?>
	</td>
	<td width="100" align="right">
    <? echo $size_color_sensitive[$selectResult[csf("sensitivity")]];?>
	</td>
	<td width="80" align="right">
	<? echo number_format($woq,4,'.','');?>
	</td>
	<td width="80" align="right">
    <? echo $exchange_rate;?>
	</td>
	<td width="80" align="right">
    <? echo number_format($rate,4,'.','');?>
	</td>
	<td width="80" align="right">
    <? echo number_format($amount,4,'.','');?>
	</td>
	<td width="" align="right">
    <? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?>
	</td>
	</tr>
	<?
	$total_amount += $amount;
	$i++;
	}
	}

	if($cbo_level==2){
	$i=1;
	foreach ($job_and_trimgroup_level as $job_no){
	foreach ($job_no as $sen){
	foreach ($sen as $wo_pre_cost_trim_cost_dtls){
	$job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
	$po_number=implode(",",$wo_pre_cost_trim_cost_dtls['po_number']);
	$po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
	$country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
	$description=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['description']));
	$brand_sup_ref=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['brand_sup_ref']));
	$wo_pre_cost_trim_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_trim_cost_dtls']));
	$trim_group = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['trim_group']));
	$uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
	$booking_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['booking_id']));
	$sensitivity=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['sensitivity']));
	$delivery_date=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['txt_delivery_date']));
	$woq=def_number_format(array_sum($wo_pre_cost_trim_cost_dtls['woq']),5,"");
	$amount=def_number_format(array_sum($wo_pre_cost_trim_cost_dtls['amount']),5,"");
	$rate=def_number_format($amount/$woq,5,"");
	//$total_amount+=$amount;
	//echo $sensitivity."<br/>";
	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="fnc_show_booking(<? echo $wo_pre_cost_trim_id;?>,'<? echo $po_id; ?>','<? echo $booking_id; ?>')">
	<td width="40"><? echo $i;?></td>
	<td width="100">
	<? echo $job_no?>
	</td>
	<td width="100" style="word-wrap:break-word;word-break: break-all">
	<? echo $po_number; ?>
	</td>
	<td width="100"  title="<? echo $sql_lib_item_group_array[$trim_group][conversion_factor];  ?>">
	<? echo $trim_group_library[$trim_group];?>
	</td>
	<td width="80">
	<?  echo $description;?>
	</td>
	<td width="80">
	<?  echo $unit_of_measurement[$uom];?>
	</td>
	<td width="100" align="right">
    <? echo $size_color_sensitive[$sensitivity];?>
	</td>
	<td width="80" align="right">
	<? echo number_format($woq,4,'.','');?>
	</td>
	<td width="80" align="right">
    <? echo $exchange_rate;?>
	</td>
	<td width="80" align="right">
    <? echo number_format($rate,4,'.','');?>
	</td>
	<td width="80" align="right">
    <? echo number_format($amount,4,'.','');?>
	</td>
	<td width="" align="right">
    <? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>
	</td>
	</tr>
	<?
	$i++;
	$total_amount += $amount;
	}
	}
	}
	}
	?>
	</tbody>
	<tfoot>
		<th colspan="9" align="right">Total</th>
		<th align="right"><? echo number_format($total_amount,4,'.','');?></th>
		<th></th>
	</tfoot>
	</table>
	<?
}
if ($action == "consumption_popup"){
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from lib_size", "id", "size_name");
	?>
	<script>
	var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size", "size_name"  ), 0, -1); ?>];
	var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color", "color_name"  ), 0, -1); ?>];
	function poportionate_qty_old(qty){
		var po_qty=document.getElementById('po_qty').value;
		var txtwoq_qty=document.getElementById('txtwoq_qty').value;
		var rowCount = $('#tbl_consmption_cost tbody tr').length;
		for(var i=1; i<=rowCount; i++){
			var pcs=$('#pcsset_'+i).val();
			var txtwoq_cal =number_format_common((txtwoq_qty/po_qty) * (pcs),5,0);
			$('#qty_'+i).val(txtwoq_cal);
			calculate_requirement(i)
		}
		set_sum_value( 'qty_sum', 'qty_' )
	}

	function poportionate_qty(qty){
		var txtwoq=document.getElementById('txtwoq').value;
		var txtwoq_qty=document.getElementById('txtwoq_qty').value*1;
		var rowCount = $('#tbl_consmption_cost tbody tr').length;
		for(var i=1; i<=rowCount; i++){
			var poreqqty=$('#poreqqty_'+i).val();
			var txtwoq_cal =number_format_common((txtwoq_qty/txtwoq) * (poreqqty),5,0);
			$('#qty_'+i).val(txtwoq_cal);
			calculate_requirement(i)
		}
		set_sum_value( 'qty_sum', 'qty_')
		var j=i-1;
		var qty_sum=document.getElementById('qty_sum').value*1;
		if(qty_sum >txtwoq_qty ){
			$('#qty_'+j).val(number_format_common(txtwoq_cal*1-(qty_sum-txtwoq_qty),5,0))
		}
		else if(qty_sum < txtwoq_qty ){
			$('#qty_'+j).val(number_format_common((txtwoq_cal*1) +(txtwoq_qty - qty_sum),5,0))
		}
		else{
			$('#qty_'+j).val(number_format_common(txtwoq_cal,5,0));
		}
		set_sum_value( 'qty_sum', 'qty_');
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
		WastageQty= number_format_common( WastageQty, 5, 0) ;
		document.getElementById('woqny_'+i).value= WastageQty;
		set_sum_value( 'woqty_sum', 'woqny_' )
		calculate_amount(i);
	}

	function set_sum_value(des_fil_id,field_id){
		if(des_fil_id=='qty_sum'){
			var ddd={dec_type:5,comma:0,currency:0};
		}

		if(des_fil_id=='excess_sum'){
			var ddd={dec_type:5,comma:0,currency:0};
		}

		if(des_fil_id=='woqty_sum'){
			var ddd={dec_type:5,comma:0,currency:0};
		}

		if(des_fil_id=='amount_sum'){
			var ddd={dec_type:5,comma:0,currency:0};
		}

		if(des_fil_id=='pcs_sum'){
			var ddd={dec_type:6,comma:0};
		}
		var rowCount = $('#tbl_consmption_cost tbody tr').length;
		math_operation( des_fil_id, field_id, '+', rowCount,ddd );
	}

	function copy_value(value,field_id,i){
		var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
		var pocolorid=document.getElementById('pocolorid_'+i).value;
		var rowCount = $('#tbl_consmption_cost tbody tr').length;
		var copy_basis=$('input[name="copy_basis"]:checked').val()

		for(var j=i; j<=rowCount; j++){
			if(field_id=='des_'){
				if(copy_basis==0){
					document.getElementById(field_id+j).value=value;
				}
				if(copy_basis==1){
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
			if(field_id=='brndsup_'){
				if(copy_basis==0){
					document.getElementById(field_id+j).value=value;
				}
				if(copy_basis==1){
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
		var avg_rate=number_format_common((amount_sum/woqty_sum),5,0);
		document.getElementById('rate_sum').value=avg_rate;
	}

	function js_set_value(){
		var row_num=$('#tbl_consmption_cost tbody tr').length;
		var cons_breck_down="";
		for(var i=1; i<=row_num; i++){

			var pocolorid=$('#pocolorid_'+i).val()
			if(pocolorid==''){
				pocolorid=0;
			}

			var gmtssizesid=$('#gmtssizesid_'+i).val()
			if(gmtssizesid==''){
				gmtssizesid=0;
			}

			var des=$('#des_'+i).val()
			if(des==''){
				des=0;
			}

			var brndsup=$('#brndsup_'+i).val();
			if(brndsup==''){
				brndsup=0;
			}

			var itemcolor=$('#itemcolor_'+i).val()
			if(itemcolor==''){
				itemcolor=0;
			}

			var itemsizes=$('#itemsizes_'+i).val()
			if(itemsizes==''){
				itemsizes=0;
			}

			var qty=$('#qty_'+i).val()
			if(qty==''){
				qty=0;
			}

			var excess=$('#excess_'+i).val()
			if(excess==''){
				excess=0;
			}

			var woqny=$('#woqny_'+i).val()
			if(woqny==''){
				woqny=0;
			}

			var rate=$('#rate_'+i).val()
			if(rate==''){
				rate=0;
			}

			var amount=$('#amount_'+i).val()
			if(amount==''){
				amount=0;
			}

			var pcs=$('#pcs_'+i).val()
			if(pcs==''){
				pcs=0;
			}

			var colorsizetableid=$('#colorsizetableid_'+i).val()
			if(colorsizetableid==''){
				colorsizetableid=0;
			}

			var updateid=$('#updateid_'+i).val()
			if(updateid==''){
				updateid=0;
			}

			var reqqty=$('#reqqty_'+i).val()
			if(reqqty==''){
				reqqty=0;
			}


			if(cons_breck_down==""){
				cons_breck_down+=pocolorid+'_'+gmtssizesid+'_'+des+'_'+brndsup+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty;
			}
			else{
				cons_breck_down+="__"+pocolorid+'_'+gmtssizesid+'_'+des+'_'+brndsup+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty;
			}
		}
		document.getElementById('cons_breck_down').value=cons_breck_down;
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
        $sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
        foreach($sql_po_qty as$sql_po_qty_row){
			$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
			$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
        }
        ?>
        <div align="center" style="width:1050px;" >
            <fieldset>
                <form id="consumptionform_1" autocomplete="off">
                    <table width="1050" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                        <thead>
                        	<tr>
                                <th colspan="14" id="td_sync_msg" style="color:#FF0000"></th>
                            </tr>
                            <tr>
                                <th width="40" colspan="14">
                                <input type="hidden" id="cons_breck_down" name="cons_breck_down" value="" />
                                <input type="hidden" id="txtwoq" value="<? echo $txt_req_quantity;?>"/>
                                Wo Qty:<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value)" value="<? echo $txtwoq; ?>"/>
                                <input type="radio" name="copy_basis" value="0" checked>Copy to All
                                <input type="radio" name="copy_basis" value="1">Gmts Size Wise
                                <input type="radio" name="copy_basis" value="2">Gmts Color Wise
                                <input type="radio" name="copy_basis" value="10">No Copy
                                <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>"/>
                                <input type="hidden" id="po_qty" name="po_qty" value="<? echo $tot_po_qty; ?>"/>
                                </th>
                            </tr>
                            <tr>
                                <th width="40">SL</th><th  width="100">Gmts. Color</th><th  width="70">Gmts. sizes</th><th  width="100">Description</th><th  width="100">Brand/Sup Ref</th><th  width="100">Item Color</th><th width="80">Item Sizes</th><th width="70"> Wo Qty</th><th width="40">Excess %</th><th width="70">WO Qty.</th><th width="120">Rate</th><th width="100">Amount</th><th width="">RMG Qnty</th>

                            </tr>
                        </thead>
                        <tbody>
                        <?
                        $sql_lib_item_group_array=array();
                        $sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
                        foreach($sql_lib_item_group as $row_sql_lib_item_group){
							$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
							$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
							$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
                        }

                        $booking_data_arr=array();
                        $booking_data=sql_select("select id,wo_trim_booking_dtls_id,description,brand_supplier,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id  from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
                        foreach($booking_data as $booking_data_row){
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][id]=$booking_data_row[csf('id')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][description]=$booking_data_row[csf('description')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][brand_supplier]=$booking_data_row[csf('brand_supplier')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_color]=$booking_data_row[csf('item_color')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_size]=$booking_data_row[csf('item_size')];

							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][cons]+=$booking_data_row[csf('cons')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][process_loss_percent]=$booking_data_row[csf('process_loss_percent')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][requirment]+=$booking_data_row[csf('requirment')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][rate]=$booking_data_row[csf('rate')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][amount]+=$booking_data_row[csf('amount')];
                        }
                        $condition= new condition();
                        if(str_replace("'","",$txt_po_id) !=''){
							$condition->po_id("in($txt_po_id)");
                        }

                        $condition->init();
                        $trims= new trims($condition);

                        $gmt_color_edb="";
                        $item_color_edb="";
                        $gmt_size_edb="";
                        $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtssize();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtssize();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id order by b.id,size_order";
							$gmt_color_edb=1;
							$item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
							$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
							$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id  order by b.id, color_order,size_order";
                        }
                        else{
							$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
                        }

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
								if($item_color==0 || $item_color=="" ){
									$item_color = $row[csf('color_number_id')];
								}
								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == ""){
									$item_size=$row[csf('item_size')];
								}

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate==""){
									$rate=$txt_avg_price;
								}

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description==""){
									$description=$txt_pre_des;
								}

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier==""){
									$brand_supplier=$txt_pre_brand_sup;
								}
								if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){

									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");

									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
								}


								else if($cbo_colorsizesensitive==2){
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
								}
								else if($cbo_colorsizesensitive==4){
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");

									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
								}
								else if($cbo_colorsizesensitive==0){
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");

									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
								}
							}
                        }

						$piNumber=0;
						$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no='$txt_booking_no' and b.item_group='".$txt_trim_group_id."' and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
						if($pi_number){
						$piNumber=1;
						}
						$recvNumber=0;
						$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and b.item_group_id='".$txt_trim_group_id."' and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
						if($recv_number){
						$recvNumber=1;
						}

                        if ( count($data_array)>0 && $cbo_level==1){
							$i=0;
							foreach( $data_array as $row ){
								$data=explode('_',$data_array_cons[$i]);

								if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");
								}

								else if($cbo_colorsizesensitive==2){
									$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]];
									$req_qnty_ord_uom = def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");
								}
								else if($cbo_colorsizesensitive==4){
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");
								}
								else if($cbo_colorsizesensitive==0){
									$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
									$req_qnty_ord_uom = def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");
								}


								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ){
									$item_color = $row[csf('color_number_id')];
								}

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == ""){
									$item_size=$size_library[$row[csf('size_number_id')]];
								}
								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate==""){
									$rate=$txt_avg_price;
								}
								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description==""){
									$description=$txt_pre_des;
								}

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier==""){
									$brand_supplier=$txt_pre_brand_sup;
								}
								if($txtwoq_cal>0){
									$i++;
								?>
									<tr id="break_1" align="center">
									<td>
									<? echo $i;?>
									</td>

									<td>
									<input type="text" id="pocolor_<? echo $i;?>"  name="pocolor_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
									<input type="hidden" id="pocolorid_<? echo $i;?>"  name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
									<input type="hidden" id="poid_<? echo $i;?>"  name="poid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('id')]; ?>" />
									<input type="hidden" id="poqty_<? echo $i;?>"  name="poqty_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $po_qty_arr[$row[csf('id')]]; ?>" readonly />
									<input type="hidden" id="poreqqty_<? echo $i;?>"  name="poreqqty_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $txtwoq_cal; ?>" readonly />

									</td>
									<td>
									<input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly />
									<input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>" readonly/>
									</td>
									<td>
									<input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>  />
									</td>
									<td>
									<input type="text" id="brndsup_<? echo $i;?>"  name="brndsup_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>  />

									</td>
									<td>
									<input type="text" id="itemcolor_<? echo $i;?>"  value="<? echo $color_library[$item_color]; ?>"  name="itemcolor_<? echo $i;?>"  class="text_boxes" style="width:95px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)"  <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
									</td>
									<td>
									<input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:70px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $item_size; ?>" <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
									</td>
									<td>

									<input type="hidden" id="reqqty_<? echo $i;?>"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"    value="<? echo $txtwoq_cal ?>" readonly/>

									<input type="text" id="qty_<? echo $i;?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/>
									</td>
									<td>
									<input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/>
									</td>
									<td>
									<input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' ) " onChange="set_sum_value( 'woqty_sum', 'woqny_' )"  name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly />
									</td>


									<td>
									<input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:120px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>  />
									</td>
									<td>
									<input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:100px"  value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly />
									</td>
									<td>
									<input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity')]; ?>" readonly>
									<input type="hidden" id="pcsset_<? echo $i;?>"  name="pcsset_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity_set')]; ?>" readonly>
                                    <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_size_table_id')]; ?>" readonly />
									<input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
									</td>

									</tr>
								<?
								}
							}
                        }
                        ?>

                        <?
                        $level_arr=array();
                        $gmt_color_edb="";
                        $item_color_edb="";
                        $gmt_size_edb="";
                        $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.size_number_id order by size_order";
							$level_arr=$po_size_level_data_arr;
							$gmt_color_edb=1;
							$item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							$sql="select min(b.id) as id, min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
							$sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.size_number_id  order by  color_order,size_order";
							$level_arr=$po_color_size_level_data_arr;
                        }
                        else{
							$sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
							$level_arr=$po_no_sen_level_data_arr;
                        }
                        $data_array=sql_select($sql);
                        if ( count($data_array)>0 && $cbo_level==2){
							$i=0;
							foreach( $data_array as $row ){

								if($cbo_colorsizesensitive==1){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
								}
								if($cbo_colorsizesensitive==2){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['booking_qty']),5,"");
								}
								if($cbo_colorsizesensitive==3){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
								}
								if($cbo_colorsizesensitive==4){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['booking_qty']),5,"");
								}
								if($cbo_colorsizesensitive==0){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_qty']),5,"");
								}

								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ){
									$item_color = $row[csf('color_number_id')];
								}

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == ""){
									$item_size=$size_library[$row[csf('size_number_id')]];
								}

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate==""){
									$rate=$txt_avg_price;
								}

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description==""){
									$description=$txt_pre_des;
								}

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier==""){
									$brand_supplier=$txt_pre_brand_sup;
								}
								if($txtwoq_cal>0){
									$i++;
								?>
									<tr id="break_1" align="center">
									<td>
									<? echo $i;?>
									</td>
									<td>
									<input type="text" id="pocolor_<? echo $i;?>"  name="pocolor_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>"  <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
									<input type="hidden" id="pocolorid_<? echo $i;?>"  name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
									<input type="hidden" id="poid_<? echo $i;?>"  name="poid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('id')]; ?>" readonly />
									<input type="hidden" id="poqty_<? echo $i;?>"  name="poqty_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $po_qty; ?>" />
									<input type="hidden" id="poreqqty_<? echo $i;?>"  name="poreqqty_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $txtwoq_cal; ?>" readonly />

									</td>
									<td>
									<input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/>
									<input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>" readonly />
									</td>
									<td>
									<input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
									</td>
									<td>
									<input type="text" id="brndsup_<? echo $i;?>"  name="brndsup_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />

									</td>
									<td>
									<input type="text" id="itemcolor_<? echo $i;?>"  value="<? echo $color_library[$item_color]; ?>" name="itemcolor_<? echo $i;?>" class="text_boxes" style="width:95px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)" <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
									</td>
									<td>
									<input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>" class="text_boxes" style="width:70px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $item_size; ?>"  <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
									</td>
									<td>
									<input type="hidden" id="reqqty_<? echo $i;?>"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"    value="<? echo $txtwoq_cal ?>" readonly/>

									<input type="text" id="qty_<? echo $i;?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? if($booking_cons>0){echo $booking_cons;} ?>"/>
									</td>
									<td>
									<input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/>
									</td>
									<td>
									<input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' ) " onChange="set_sum_value( 'woqty_sum', 'woqny_' )"  name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<?  if($booking_qty){echo $booking_qty;} ?>" readonly />
									</td>


									<td>
									<input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:120px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>"  <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
									</td>
									<td>
									<input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:100px"  value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly />
									</td>
									<td>
									<input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity')]; ?>" readonly>
									<input type="hidden" id="pcsset_<? echo $i;?>"  name="pcsset_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $order_quantity_set; ?>" readonly>
                                    <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_size_table_id')]; ?>" readonly />
									<input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
									</td>

									</tr>
								<?
								}
							}
                        }
                        ?>
                        </tbody>
                         <tfoot>
                            <tr>
                               <th width="40"></th><th  width="100"></th><th  width="70"></th><th  width="100"></th><th  width="100"></th><th  width="100"></th><th width="80"></th>
                                <th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:70px"  readonly></th>
                                <th width="40"><input type="text" id="excess_sum"  name="excess_sum" class="text_boxes_numeric" style="width:40px" readonly></th>
                                <th width="70"><input type="text" id="woqty_sum"  name="woqty_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
                                <th width="40"><input type="text" id="rate_sum"  name="rate_sum" class="text_boxes_numeric" style="width:120px" readonly></th>
                                <th width="50"><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:100px" readonly></th>
                                <th width="">
                                <input type="hidden" id="json_data" name="json_data" class="text_boxes_numeric" style="width:50px" value='<? echo json_encode($level_arr); ?>' readonly>
                                <input type="text" id="pcs_sum" name="pcs_sum" class="text_boxes_numeric" style="width:50px" readonly>
                                </th>
                            </tr>
                        </tfoot>
                    </table>

                    <table width="810" cellspacing="0" class="" border="0" rules="all">
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
		poportionate_qty(<? echo $txtwoq; ?>);
		<?
	}
	?>
	set_sum_value( 'qty_sum', 'qty_' );
	set_sum_value( 'woqty_sum', 'woqny_' );
	set_sum_value( 'amount_sum', 'amount_' );
	set_sum_value( 'pcs_sum', 'pcs_' );
	calculate_avg_rate();

	var wo_qty=$('#txtwoq_qty').val()*1;

	var wo_qty_sum=$('#qty_sum').val()*1;

	if(wo_qty!=wo_qty_sum)
	{
		$('#td_sync_msg').html("Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.");
	}
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
<?
}

if ($action=="set_cons_break_down"){
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$data=explode("_",$data);
	$garments_nature=$data[0];
	$cbo_company_name=$data[1];
	$txt_job_no=$data[2];
	$txt_po_id=$data[3];
	$cbo_trim_precost_id=$data[4];
	$txt_trim_group_id=$data[5];
	$txt_update_dtls_id=$data[6];
	$cbo_colorsizesensitive=$data[7];
	$txt_req_quantity=$data[8];
	$txt_avg_price=$data[9];
	$txt_country=$data[10];
	$txt_pre_des=$data[11];
	$txt_pre_brand_sup=$data[12];
	$cbo_level=$data[13];

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
	$sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
	$tot_po_qty=0;
	foreach($sql_po_qty as$sql_po_qty_row){
		$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
		$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
	}
	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}

	$booking_data_arr=array();
	$booking_data=sql_select("select id,wo_trim_booking_dtls_id,description,brand_supplier,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id  from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
	foreach($booking_data as $booking_data_row){
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][id]=$booking_data_row[csf('id')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][description]=$booking_data_row[csf('description')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][brand_supplier]=$booking_data_row[csf('brand_supplier')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_color]=$booking_data_row[csf('item_color')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_size]=$booking_data_row[csf('item_size')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][cons]+=$booking_data_row[csf('cons')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][process_loss_percent]=$booking_data_row[csf('process_loss_percent')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][requirment]+=$booking_data_row[csf('requirment')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][rate]=$booking_data_row[csf('rate')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][amount]+=$booking_data_row[csf('amount')];
	}
		$cu_booking_data_arr=array();
	$cu_booking_data=sql_select("select a.pre_cost_fabric_cost_dtls_id,b.id,b.wo_trim_booking_dtls_id,b.po_break_down_id,b.color_number_id,b.gmts_sizes,b.requirment  from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and b.po_break_down_id in($txt_po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
	foreach($cu_booking_data as $cu_booking_data_row){
		if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==2 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('gmts_sizes')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==4 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]][$cu_booking_data_row[csf('gmts_sizes')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==0 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
	}

	$condition= new condition();
	if(str_replace("'","",$txt_po_id) !=''){
			$condition->po_id("in($txt_po_id)");
	}

	$condition->init();
	$trims= new trims($condition);
	$gmt_color_edb="";
	$item_color_edb="";
	$gmt_size_edb="";
	$item_size_edb="";
	if($cbo_colorsizesensitive==1){
		$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
		 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==2){
	    $req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtssize();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtssize();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id order by b.id,size_order";
		$gmt_color_edb="disabled";
		$item_color_edb="disabled";
	}
	else if($cbo_colorsizesensitive==3){
		$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4){

		$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
		$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();

		 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id  order by b.id, color_order,size_order";
	}
	else{
		$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	    $req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
	}

	$data_array=sql_select($sql);
	if ( count($data_array)>0)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			$color_number_id=$row[csf('color_number_id')];
			if($color_number_id=="")
			{
				$color_number_id=0;
			}

			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="")
			{
				$size_number_id=0;
			}

			$description=$txt_pre_des;
			if($description=="")
			{
				$description=0;
			}

			$brand_supplier=$txt_pre_brand_sup;
			if($brand_supplier=="")
			{
				$brand_supplier=0;
			}

			$item_color=$color_library[$row[csf('color_number_id')]];
			if($item_color=="")
			{
				$item_color=0;
			}

			$item_size=$row[csf('item_size')];
			if($item_size=="")
			{
				$item_size=0;
			}
			$excess=0;
			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="")
			{
				$pcs=0;
			}

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="")
			{
				$colorsizetableid=0;
			}
			if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
			$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
			$req_qnty_ord_uom = def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
			$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]],5,"");
			$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
			$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");
			$amount = def_number_format($txtwoq_cal*$txt_avg_price,5,"");

			$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
			$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
			$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
			$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
			$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
			$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['amount'][$row[csf('id')]]=$amount;
			}

			else if($cbo_colorsizesensitive==2){

			$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]];
			$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
			$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]],5,"");
			$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
			$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
			$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");

			$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
			$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
			$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
			$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
			$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
			$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['amount'][$row[csf('id')]]=$amount;
			}
			else if($cbo_colorsizesensitive==4){
			$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]];
			$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
			$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]],5,"");
			$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
			$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
			$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");

			$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
			$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
			$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
			$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
			$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
			$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['amount'][$row[csf('id')]]=$amount;
			}
			else if($cbo_colorsizesensitive==0){

			$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
			$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
			$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id],5,"");
			$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
			$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
			$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");

			$po_no_sen_level_data_arr[$cbo_trim_precost_id]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
			$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_qty'][$row[csf('id')]]=$po_qty;
			$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
			$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_id'][$row[csf('id')]]=$row[csf('id')];
			$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
			$po_no_sen_level_data_arr[$cbo_trim_precost_id]['amount'][$row[csf('id')]]=$amount;
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
			if($color_number_id=="")
			{
				$color_number_id=0;
			}

			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="")
			{
				$size_number_id=0;
			}

			$description=$txt_pre_des;
			if($description=="")
			{
				$description=0;
			}

			$brand_supplier=$txt_pre_brand_sup;
			if($brand_supplier=="")
			{
				$brand_supplier=0;
			}

			$item_color=$color_library[$row[csf('color_number_id')]];
			if($item_color=="")
			{
				$item_color=0;
			}

			$item_size=$row[csf('item_size')];
			if($item_size=="")
			{
				$item_size=0;
			}
			$excess=0;

			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="")
			{
				$pcs=0;
			}

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="")
			{
				$colorsizetableid=0;
			}
			if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}

			else if($cbo_colorsizesensitive==2){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			else if($cbo_colorsizesensitive==4){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			else if($cbo_colorsizesensitive==0){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($txtwoq_cal>0){
				if($cons_breck_down=="")
				{
					$cons_breck_down.=$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal;
				}
				else
				{
					$cons_breck_down.="__".$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal;
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
	 $sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
	 $level_arr=$po_color_level_data_arr;
	 $gmt_size_edb="disabled";
	 $item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==2){
		$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.size_number_id order by size_order";
	 $level_arr=$po_size_level_data_arr;
	 $gmt_color_edb="disabled";
	 $item_color_edb="disabled";
	}
	else if($cbo_colorsizesensitive==3){
		$sql="select min(b.id) as id, min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
	$level_arr=$po_color_level_data_arr;
	 $gmt_size_edb="disabled";
	 $item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4){
		 $sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.size_number_id  order by  color_order,size_order";
		$level_arr=$po_color_size_level_data_arr;
	}
	else{
		 $sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
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
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
				$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}

			if($cbo_colorsizesensitive==2){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==3){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==4){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==0){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			$color_number_id=$row[csf('color_number_id')];
			if($color_number_id=="")
			{
				$color_number_id=0;
			}

			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="")
			{
				$size_number_id=0;
			}

			$description=$txt_pre_des;
			if($description=="")
			{
				$description=0;
			}

			$brand_supplier=$txt_pre_brand_sup;
			if($brand_supplier=="")
			{
				$brand_supplier=0;
			}

			$item_color=$color_library[$row[csf('color_number_id')]];
			if($item_color=="")
			{
				$item_color=0;
			}

			$item_size=$row[csf('item_size')];
			if($item_size=="")
			{
				$item_size=0;
			}
			$excess=0;

			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="")
			{
				$pcs=0;
			}

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="")
			{
				$colorsizetableid=0;
			}

			if($txtwoq_cal>0){
				if($cons_breck_down==""){
					$cons_breck_down.=$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal;
				}
				else{
					$cons_breck_down.="__".$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal;
				}
				}
			}
		echo $cons_breck_down."**".json_encode($level_arr);
	}
}

if ($action=="save_update_delete"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0){
		$con = connect();
		if($db_type==0){
		mysql_query("BEGIN");
		}
		if($db_type==0){
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=2 and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		if($db_type==2){
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=2 and to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id,booking_type,is_short,booking_month,booking_year,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id, 	item_category,supplier_id,currency_id,booking_date,delivery_date,pay_mode,source,attention,remarks,item_from_precost,entry_form,cbo_level,ready_to_approved,responsible_dept,responsible_person,reason,inserted_by,insert_date";
		$data_array ="(".$id.",2,1,".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",4,".$cbo_supplier_name.",".$cbo_currency.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$txt_remarks.",1,273,".$cbo_level.",".$cbo_ready_to_approved.",".$cbo_responsible_dept.",".$cbo_responsible_person.",".$txt_reason.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		if($db_type==0){
		if($rID){
			mysql_query("COMMIT");
			echo "0**".$new_booking_no[0]."**".$id;
		}
		else{
			mysql_query("ROLLBACK");
			echo "10**".$new_booking_no[0]."**".$id;
		}
		}

		if($db_type==2 || $db_type==1 ){
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

	else if ($operation==1){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		$booking_mst_id=str_replace("'","",$booking_mst_id);
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		//if(str_replace("'","",$cbo_pay_mode)==2){
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				 disconnect($con);die;
			}
		//}else{
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				 disconnect($con);die;
			}
		//}
		$field_array_up="booking_month*booking_year*supplier_id*currency_id*booking_date*delivery_date*pay_mode*source*attention*remarks*item_from_precost*cbo_level*ready_to_approved*responsible_dept*responsible_person*reason*updated_by*update_date";

		$data_array_up ="".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_supplier_name."*".$cbo_currency."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$txt_remarks."*1*".$cbo_level."*".$cbo_ready_to_approved."*".$cbo_responsible_dept."*".$cbo_responsible_person."*".$txt_reason."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);

		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no)."**".$booking_mst_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no)."**".$booking_mst_id;
			}
		}

		if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".$booking_mst_id;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no)."**".$booking_mst_id;
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
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		//if(str_replace("'","",$cbo_pay_mode)==2){
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				 disconnect($con);die;
			}
		//}else{
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			 disconnect($con);	die;
			}
		//}

		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

		//$rID=execute_query( "delete from wo_booking_mst where  booking_no =".$txt_booking_no."",0);
		//$rID1=execute_query( "delete from wo_booking_dtls where  booking_no =".$txt_booking_no."",0);
		//$rID2=execute_query( "delete from wo_trim_book_con_dtls where  booking_no =".$txt_booking_no."",0);
		$rID=execute_query( "update wo_booking_mst set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no=$txt_booking_no",0);
		$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no=$txt_booking_no",0);
		$rID1=execute_query( "update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where  booking_no=$txt_booking_no",0);
		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}

		if($db_type==2 || $db_type==1 ){
			if($rID){
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

if ($action=="save_update_delete_dtls"){
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$booking_mst_id=str_replace("'","",$booking_mst_id);
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_booking_no);
		 disconnect($con);die;
	}

	if ($operation==0){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**0";
			 disconnect($con);die;
		}
		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		$field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_mst_id, booking_type, is_short, trim_group, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date, country_id_string, inserted_by, insert_date";

		$field_array2="id,wo_trim_booking_dtls_id,booking_no, booking_mst_id, job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id";

		$add_comma=0;
		$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
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

			$data_array1 ="(".$id_dtls.",".$$txttrimcostid.",".$$txtpoid.",".$$txtjob_id.",".$txt_booking_no.",".$booking_mst_id.",2,1,".$$txttrimgroup.",".$$txtuom.",".$$cbocolorsizesensitive.",".$$txtwoq.",".$$txtexchrate.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//	CONS break down===============================================================================================
			if(str_replace("'",'',$$consbreckdown) !=''){
				$data_array2="";
				$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					if(str_replace("'","",$consbreckdownarr[4]) !="")
					{
					    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
					    {
					        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","273");
					        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
					    }
					    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
					}
					else
					{
					    $color_id=0;
					}

					if ($c!=0) $data_array2 .=",";
					$data_array2 .="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."')";
					$id1=$id1+1;
					$add_comma++;
				}
			}
			//CONS break down end===============================================================================================
			$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
			$rID2=1;
			if($data_array2 !=""){
				$rID2=sql_insert("wo_trim_book_con_dtls",$field_array2,$data_array2,1);
			}
			$id_dtls=$id_dtls+1;
		}
		check_table_status( $_SESSION['menu_id'],0);
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
	else if ($operation==1){
	$con = connect();
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_booking_no);
		 disconnect($con);die;
	}

	if($db_type==0){
	mysql_query("BEGIN");
	}
	if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
		echo "15**1";
		 disconnect($con);die;
	}
	$field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*trim_group*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*country_id_string*updated_by*update_date";
	$field_array_up2="id,wo_trim_booking_dtls_id,booking_no, booking_mst_id,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id";

	$add_comma=0;
	$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
	$new_array_color=array();
	for ($i=1;$i<=$total_row;$i++){
		$txttrimcostid="txttrimcostid_".$i;
		$txtpoid="txtpoid_".$i;
		$txttrimgroup="txttrimgroup_".$i;
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

		$pi_number=array();
		$piquantity=0;
		$sqlPi=sql_select("select a.pi_number, b.quantity from  com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		foreach($sqlPi as $rowPi){
			$pi_number[$rowPi[csf('pi_number')]]=$rowPi[csf('pi_number')];
			$piquantity+=$rowPi[csf('quantity')];
		}

		if($piquantity && str_replace("'","",$$txtwoq) < $piquantity){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".implode(",",$pi_number)."**".$piquantity;
			check_table_status( $_SESSION['menu_id'],0);
			 disconnect($con);die;
		}

		$recv_number=array();
		$recvquantity=0;
		$sqlRecv=sql_select("select a.recv_number, b.receive_qnty from  inv_receive_master a,inv_trims_entry_dtls b where a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		foreach($sqlRecv as $rowRecv){
			$recv_number[$rowRecv[csf('recv_number')]]=$rowRecv[csf('recv_number')];
			$recvquantity+=$rowRecv[csf('receive_qnty')];
		}
		if($recvquantity && str_replace("'","",$$txtwoq) < $recvquantity){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".implode(",",$recv_number)."**".$recvquantity;
			check_table_status( $_SESSION['menu_id'],0);
			 disconnect($con);die;
		}

		if(str_replace("'",'',$$txtbookingid)!=""){
			$id_arr=array();
			$data_array_up1=array();
			$id_arr[]=str_replace("'",'',$$txtbookingid);
			$data_array_up1[str_replace("'",'',$$txtbookingid)] =explode("*",("".$$txttrimcostid."*".$$txtpoid."*".$$txtjob_id."*".$txt_booking_no."*".$$txttrimgroup."*".$$txtuom."*".$$cbocolorsizesensitive."*".$$txtwoq."*".$$txtexchrate."*".$$txtrate."*".$$txtamount."*".$$txtddate."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			//	CONS break down===============================================================================================
			if(str_replace("'",'',$$consbreckdown) !=''){
				$data_array_up2="";
				$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					if(str_replace("'","",$consbreckdownarr[4]) !="")
					{
					    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
					    {
					        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","273");
					        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
					    }
					    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
					}
					else
					{
					    $color_id=0;
					}
					if ($c!=0) $data_array_up2 .=",";
					$data_array_up2 .="(".$id1.",".$$txtbookingid.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."')";
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
		$rID2=sql_insert("wo_trim_book_con_dtls",$field_array_up2,$data_array_up2,1);
		}
	}
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
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		if($db_type==0){
			mysql_query("BEGIN");
		}
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$txtpoid="txtpoid_".$i;
			$txtbookingid="txtbookingid_".$i;
			//if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			    if($pi_number){
				    echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				   disconnect($con);  die;
			    }
			//}else{
				$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			    if($recv_number){
				    echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				    disconnect($con); die;
			    }
			//}
			$delete_cause=str_replace("'","",$delete_cause);
			$delete_cause=str_replace('"','',$delete_cause);
			$delete_cause=str_replace('(','',$delete_cause);
			$delete_cause=str_replace(')','',$delete_cause);

			//$rID1=execute_query( "delete from wo_booking_dtls where  id in (".str_replace("'","",$$txtbookingid).")",0);
			//$rID2=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).")",0);
			$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  id in (".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
		    $rID2=execute_query( "update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where  wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
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




if ($action=="save_update_delete_dtls_job_level"){
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$booking_mst_id= str_replace("'","",$booking_mst_id);
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
	}
	$strdata=json_decode(str_replace("'","",$strdata));
	if ($operation==0){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**0";
		 disconnect($con);	die;
		}
		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		$field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_mst_id,  booking_type, is_short, trim_group, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date, country_id_string, inserted_by, insert_date";
		$field_array2="id,wo_trim_booking_dtls_id,booking_no,booking_mst_id,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, pcs,color_size_table_id";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
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
			$jsonarr=json_decode(str_replace("'","",$$jsondata));

			$job=str_replace("'","",$$txtjob_id);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);

			foreach($strdata->$job->$trimcostid->po_id as $poId){
				$wqQty=($strdata->$job->$trimcostid->req_qnty->$poId/$reqqnty)*$woq;
				$amount=$wqQty*$rate;
				$data_array1 ="(".$id_dtls.",".$$txttrimcostid.",".$poId.",".$$txtjob_id.",".$txt_booking_no.",".$booking_mst_id.",2,1,".$$txttrimgroup.",".$$txtuom.",".$$cbocolorsizesensitive.",".$wqQty.",".$$txtexchrate.",".$$txtrate.",".$amount.",".$$txtddate.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);

				//	CONS break down===============================================================================================
				if(str_replace("'",'',$$consbreckdown) !=''){
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					$d=0;
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						if(str_replace("'","",$consbreckdownarr[4]) !="")
						{
						    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
						    {
						        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","273");
						        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						    }
						    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						}
						else
						{
						    $color_id=0;
						}

						$gmc=$consbreckdownarr[0];
						$gms=$consbreckdownarr[1];
						if(str_replace("'","",$$cbocolorsizesensitive)==1 || str_replace("'","",$$cbocolorsizesensitive)==3){
							$bQty=($jsonarr->$trimcostid->$gmc->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->$gmc->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$order_qty=$jsonarr->$trimcostid->$gmc->order_quantity->$poId;
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==2){
							$bQty=($jsonarr->$trimcostid->$gms->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->$gms->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$order_qty=$jsonarr->$trimcostid->$gms->order_quantity->$poId;
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==4){
							$bQty=($jsonarr->$trimcostid->$gmc->$gms->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->$gmc->$gms->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$order_qty=$jsonarr->$trimcostid->$gmc->$gms->order_quantity->$poId;
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==0){
							$bQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$order_qty=$jsonarr->$trimcostid->order_quantity->$poId;
						}
						$bamount=$bwqQty*$consbreckdownarr[9];
						if ($d!=0){
							$data_array2 .=",";
						}
						$data_array2 ="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$poId.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$bQty."','".$consbreckdownarr[7]."','".$bwqQty."','".$consbreckdownarr[9]."','".$bamount."','".$order_qty."','".$consbreckdownarr[12]."')";
						$id1=$id1+1;
						$add_comma++;
						$d++;
						$rID2=sql_insert("wo_trim_book_con_dtls",$field_array2,$data_array2,0);
					}
				}//CONS break down end==============================================================================================
				$id_dtls=$id_dtls+1;
			}
		}
		check_table_status( $_SESSION['menu_id'],0);
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
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}

	else if ($operation==1){
		$con = connect();
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
				echo "app1**".str_replace("'","",$txt_booking_no);
			 disconnect($con);	die;
		}
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**1";
			 disconnect($con);die;
		}
		$field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*trim_group*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*country_id_string*updated_by*update_date";
		$field_array_up2="id,wo_trim_booking_dtls_id,booking_no,booking_mst_id,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
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
			$jsonarr=json_decode(str_replace("'","",$$jsondata));
			$pi_number=array();
			$piquantity=0;
			$sqlPi=sql_select("select a.pi_number, b.quantity from  com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			foreach($sqlPi as $rowPi){
				$pi_number[$rowPi[csf('pi_number')]]=$rowPi[csf('pi_number')];
				$piquantity+=$rowPi[csf('quantity')];
			}

			if($piquantity && str_replace("'","",$$txtwoq) < $piquantity){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".implode(",",$pi_number)."**".$piquantity;
				check_table_status( $_SESSION['menu_id'],0);
				 disconnect($con);die;
			}

			$recv_number=array();
			$recvquantity=0;
			$sqlRecv=sql_select("select a.recv_number, b.receive_qnty from  inv_receive_master a,inv_trims_entry_dtls b where a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			foreach($sqlRecv as $rowRecv){
				$recv_number[$rowRecv[csf('recv_number')]]=$rowRecv[csf('recv_number')];
				$recvquantity+=$rowRecv[csf('receive_qnty')];
			}
			if($recvquantity && str_replace("'","",$$txtwoq) < $recvquantity){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".implode(",",$recv_number)."**".$recvquantity;
				check_table_status( $_SESSION['menu_id'],0);
				 disconnect($con);die;
			}

			$job=str_replace("'","",$$txtjob_id);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);

			if(str_replace("'",'',$$txtbookingid)!=""){
				foreach($strdata->$job->$trimcostid->po_id as $poId){
					$wqQty=($strdata->$job->$trimcostid->req_qnty->$poId/$reqqnty)*$woq;
					$amount=$wqQty*$rate;
					$id_arr=array();
					$data_array_up1=array();
					$id_arr[]=str_replace("'",'',$strdata->$job->$trimcostid->booking_id->$poId);
					$data_array_up1[str_replace("'",'',$strdata->$job->$trimcostid->booking_id->$poId)] =explode("*",("".$$txttrimcostid."*".$poId."*".$$txtjob_id."*".$txt_booking_no."*".$$txttrimgroup."*".$$txtuom."*".$$cbocolorsizesensitive."*".$wqQty."*".$$txtexchrate."*".$$txtrate."*".$amount."*".$$txtddate."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					if($data_array_up1 !=""){
						$rID1=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
					}
					//	CONS break down===============================================================================================
					$rID2=1;
					if(str_replace("'",'',$$consbreckdown) !=''){
						$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$strdata->$job->$trimcostid->booking_id->$poId."",0);
						$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
						$d=0;
						for($c=0;$c < count($consbreckdown_array);$c++){
							$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
							if(str_replace("'","",$consbreckdownarr[4]) !="")
							{
							    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
							    {
							        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","273");
							        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
							    }
							    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
							}
							else
							{
							    $color_id=0;
							}
							$gmc=$consbreckdownarr[0];
							$gms=$consbreckdownarr[1];
							if(str_replace("'","",$$cbocolorsizesensitive)==1 || str_replace("'","",$$cbocolorsizesensitive)==3){
								$bQty=($jsonarr->$trimcostid->$gmc->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->$gmc->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->$gmc->order_quantity->$poId;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==2){
								$bQty=($jsonarr->$trimcostid->$gms->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->$gms->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->$gms->order_quantity->$poId;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==4){
								$bQty=($jsonarr->$trimcostid->$gmc->$gms->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->$gmc->$gms->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->$gmc->$gms->order_quantity->$poId;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==0){
								$bQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->order_quantity->$poId;
							}

							$bamount=$bwqQty*$consbreckdownarr[9];
							if ($d!=0) $data_array2 .=",";
							$data_array2 ="(".$id1.",".$strdata->$job->$trimcostid->booking_id->$poId.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$poId.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$bQty."','".$consbreckdownarr[7]."','".$bwqQty."','".$consbreckdownarr[9]."','".$bamount."','".$order_qty."','".$consbreckdownarr[12]."')";
							$id1=$id1+1;
							$add_comma++;
							$d++;
							$rID2=sql_insert("wo_trim_book_con_dtls",$field_array_up2,$data_array2,0);
						}
					}//CONS break down end==============================================================================================
				}
			}
		}
		check_table_status( $_SESSION['menu_id'],0);
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
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
				echo "app1**".str_replace("'","",$txt_booking_no);
				 disconnect($con);die;
		}
		if($db_type==0){
			mysql_query("BEGIN");
		}
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$txtpoid="txtpoid_".$i;
			$txtbookingid="txtbookingid_".$i;

			//if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			    if($pi_number){
				    echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				   disconnect($con);  die;
			    }
			//}else{
				$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			    if($recv_number){
				    echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				    disconnect($con); die;
			    }
			//}
			$delete_cause=str_replace("'","",$delete_cause);
			$delete_cause=str_replace('"','',$delete_cause);
			$delete_cause=str_replace('(','',$delete_cause);
			$delete_cause=str_replace(')','',$delete_cause);
			//$rID1=execute_query( "delete from wo_booking_dtls where  id in (".str_replace("'","",$$txtbookingid).")",0);
			//$rID2=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).")",0);
			$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  id in (".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
		    $rID2=execute_query( "update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where  wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
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

if ($action=="trims_booking_popup"){
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function set_checkvalue(){
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
	}
	function js_set_value( str_data ){
		document.getElementById('txt_booking').value=str_data;
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <input type="hidden" id="txt_booking" value="" />
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                    <table  width="930" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                        <thead>
                            <tr>
                                <th colspan="10">
                                    <? echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                                </th>
                            </tr>
                            <tr>
                                <th width="140" class="must_entry_caption">Company Name</th>
                                <th width="150" class="must_entry_caption">Buyer Name</th>
                                <th width="80">Style Ref </th>
                                <th width="80">Job No </th>
                                <th width="80">Order No</th>
                                <th width="140">Supplier Name</th>
                                <th width="80">Booking No</th>
                                <th width="130" colspan="2"> Booking Date Range</th>
                                <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without PO</th> </th>
                            </tr>
                        </thead>
                        <tr class="general">
                            <td>
                            <?
                            echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "load_drop_down( 'short_trims_booking_multi_job_controllerurmi', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                            ?>
                            </td>
                            <td id="buyer_td">
                            <?
                            echo create_drop_down( "cbo_buyer_name", 130, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_id, "" );
                            ?>
                            </td>
                            <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:70px"></td>
                             <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:70px"></td>
                            <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
                            <td>
                            <?
                            echo create_drop_down( "cbo_supplier_name", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=4 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
                            ?>	</td>
                            <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px"></td>
                            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
                            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
                            <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_booking_search_list_view', 'search_div', 'short_trims_booking_multi_job_controllerurmi','setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else $company = "";
	if ($data[1]!=0) $buyer=" and c.buyer_name='$data[1]'"; else $buyer=set_user_lavel_filtering(' and c.buyer_name','buyer_id');
	if ($data[2]!=0) $supplier_id=" and a.supplier_id='$data[2]'"; else $supplier_id ="";
	if($db_type==0){
	$booking_year_cond=" and SUBSTRING_INDEX(a.booking_date, '-', 1)=$data[5]";
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2){
	$booking_year_cond=" and to_char(a.booking_date,'YYYY')=$data[5]";
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	if($data[7]==1){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[6]'   "; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no ='$data[8]'";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number = '$data[9]'  "; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num = '$data[10]'  "; //else  $order_cond="";
	}
	if($data[7]==2){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no like '$data[8]%'  "; //else  $style_cond="";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number like '$data[9]%'  "; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num like '$data[10]%'  "; //else  $order_cond="";
	}
	if($data[7]==3){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]'"; //else  $style_cond="";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number like '%$data[9]'  "; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num like '%$data[10]'  "; //else  $order_cond="";
	}
	if($data[7]==4 || $data[7]==0){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]%'"; //else  $style_cond="";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number like '%$data[9]%'  "; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num like '%$data[10]%'  "; //else  $order_cond="";
	}
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (1=>$comp,2=>$suplier);
	if($data[11]==0)
	{
		$sql="SELECT min(a.id) as id, a.booking_no_prefix_num, a.booking_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,c.style_ref_no,d.po_number from wo_booking_mst a,wo_booking_dtls b, wo_po_details_master c,wo_po_break_down d  where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type=2 and a.entry_form=273 and a.is_short=1
		and  a.status_active =1 and a.is_deleted=0  and  b.status_active =1 and b.is_deleted=0 $company  $buyer  $supplier_id $booking_date $booking_cond $style_cond $order_cond $job_cond $booking_year_cond group by a.booking_no_prefix_num, a.booking_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,c.style_ref_no,d.po_number  order by id DESC";

		echo  create_list_view("list_view", "Booking No,Company,Supplier,Booking Date,Delivery Date,Style Ref No,Po Number", "120,100,100,100,150,150","900","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,company_id,supplier_id,0,0,0,0", $arr , "booking_no_prefix_num,company_id,supplier_id,booking_date,delivery_date,style_ref_no,po_number", '','','0,0,0,3,3,0,0','','');
	}
	else
	{
		$sql="SELECT min(a.id) as id,a.job_no,a.booking_no_prefix_num, a.booking_no,company_id,a.supplier_id,a.booking_date,a.delivery_date from wo_booking_mst a  where  a.booking_no not in ( select a.booking_no from  wo_booking_mst a , wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no  and b.po_break_down_id=c.id and  a.booking_type=2 and a.entry_form=273 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   $company  ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')."  $supplier_id $booking_date $booking_cond $job_cond $file_cond $ref_cond group by a.booking_no_prefix_num, a.booking_no,company_id,a.supplier_id,a.booking_date,a.delivery_date  ) and a.booking_type=2 and a.entry_form=273 and a.status_active=1 and a.is_deleted=0 $company ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')."  $supplier_id $booking_date $booking_cond $booking_year_cond and a.is_short=1  group by a.booking_no_prefix_num, a.booking_no,a.job_no,company_id,a.supplier_id,a.booking_date,a.delivery_date order by id DESC";

		echo  create_list_view("list_view", "Booking No,Company,Supplier,Booking Date,Delivery Date", "120,100,100,100","700","420",0, $sql , "js_set_value", "booking_no", "", 1, "0,company_id,supplier_id,0,0", $arr , "booking_no_prefix_num,company_id,supplier_id,booking_date,delivery_date", '','','0,0,0,3,3','','');
	}
	exit();
}

if($action=="terms_condition_popup"){
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
?>
	<script>
	var permission='<? echo $permission; ?>';
function add_break_down_tr(i)
 {
	var row_num=$('#tbl_termcondi_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;

		 $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }
			});
		  }).end().appendTo("#tbl_termcondi_details");
		 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		  $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
		  $('#termscondition_'+i).val("");
		  $('#sltd_'+i).val(i);
		  //$('#sl_td').i
		  //alert(i)
		  //document.getElementById('sltd_'+i).innerHTML=i;
	}

}

function fn_deletebreak_down_tr(rowNo)
{


		var numRow = $('table#tbl_termcondi_details tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_termcondi_details tbody tr:last').remove();
		}

}

function fnc_fabric_booking_terms_condition( operation )
{
	    var row_num=$('#tbl_termcondi_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{

			if (form_validation('termscondition_'+i,'Term Condition')==false)
			{
				return;
			}

			data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"");
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","short_trims_booking_multi_job_controllerurmi.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
}

function fnc_fabric_booking_terms_condition_reponse()
{

	if(http.readyState == 4)
	{
	    var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
	}
}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
<input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
        	<form id="termscondi_1" autocomplete="off">



            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$i++;
							?>
                            	<tr id="settr_1" align="center" bgcolor="<? echo $bgcolor;  ?>">
                                    <td >
                                    <? //echo $i;?>
                                    <input type="text" id="sltd_<? echo $i;?>"   name="sltd_<? echo $i;?>" style="width:100%;background-color:<? echo $bgcolor;  ?>"  value="<? echo $i; ?>"  readonly />
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$i++;
					?>
                    <tr id="settr_1" align="center" bgcolor="<? echo $bgcolor;  ?>">
                                    <td >
                                    <? // echo $i;?>
                                    <input type="text" id="sltd_<? echo $i;?>"   name="sltd_<? echo $i;?>" style="width:100%; background-color:<? echo $bgcolor;  ?>"  value="<? echo $i; ?>"  readonly />
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
                                    </td>
                                </tr>
                    <?
						}
					}
					?>
                </tbody>
                </table>

                <table width="650" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
						        <?
									echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
									?>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="save_update_delete_fabric_booking_terms_condition")
{
$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
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
			if($rID ){
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
}

if ($action=="populate_data_from_search_popup_booking"){
	$job_no="";
	 $sql= "select id, booking_no,booking_date,company_id,buyer_id,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,remarks,item_from_precost,delivery_date,source,booking_year,is_approved,cbo_level,ready_to_approved, responsible_dept, responsible_person, reason from wo_booking_mst  where booking_no='$data' and  status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach ($data_array as $row){
		echo "get_php_form_data(".$row[csf("company_id")].", 'populate_variable_setting_data', 'requires/short_trims_booking_multi_job_controllerurmi' );\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('booking_mst_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "load_drop_down( 'requires/short_trims_booking_multi_job_controllerurmi', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "set_multiselect('cbo_responsible_dept','0','1','".$row[csf("responsible_dept")]."','0');\n";
		echo "document.getElementById('txt_reason').value = '".$row[csf("reason")]."';\n";
		echo "document.getElementById('cbo_responsible_person').value = '".$row[csf("responsible_person")]."';\n";
		echo " $('#cbo_company_name').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		echo " $('#cbo_level').attr('disabled',true);\n";
		echo " $('#cbo_buyer_name').attr('disabled',true);\n";
		echo "fnc_show_booking_list();\n";

		if($row[csf("is_approved")]==1){
			//echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		}
		else if($row[csf("is_approved")]==3){
			//echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is partial approved';\n";
		}
		else{
			//echo "document.getElementById('app_sms').innerHTML = '';\n";
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
	}
}

//================================================report Start=====================================================
if($action=="show_trim_booking_report2"){
	extract($_REQUEST);
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$report_type=str_replace("'","",$report_type);
	//echo $report_type.'dfdfdfd';
	 $nameArray=sql_select( "select a.booking_no, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no from wo_booking_mst a where a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0");
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
		}


		$buyer_string=array();
		$style_owner=array();
		$job_no=array();
		$style_ref=array();
		$all_dealing_marcent=array();
		$season=array();

		$nameArray_buyer=sql_select( "select  a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant,a.season,a.season_matrix   from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0");
        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
			$season[$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
		}
		$style_sting=implode(",",array_unique($style_ref));
		$job_no=implode(",",$job_no);

		$po_no=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();

		$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		}

	?>
	<div style="width:1333px" align="center">

        <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
            <tr>
            <td width="20px">
            <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
            <tr>
            <td width="50" >
            <? if($report_type==1)
            {
            ?>
            <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%'/>

            <?
            }
            else
            { ?>
            <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
            <? }
            ?>
            </td>
            <td width="40px" align="center">
            &nbsp;  &nbsp;  &nbsp;
            </td>
            <td width="30px"   align="center">

            <b style="font-size:25px;"> <?
            echo $company_library[$cbo_company_name];
            ?>
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
            $company_address[$result[csf('id')]]=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
            }
            ?>
            </label>
            <br/>
            <b style="font-size:20px;">
            <?
            if(str_replace("'","",$cbo_isshort)==2){
            $isshort="";
            }
            if(str_replace("'","",$cbo_isshort)==1){
            $isshort="[Short]";
            }
            if ($report_title !=""){
            echo $report_title." ".$isshort;
            }
            else{
            echo "Main Trims Booking ".$isshort;
            }

            ?>
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
            <td  width="110" >:&nbsp;<? echo implode(",",array_unique($buyer_string));; ?></td>
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
            ?></b>
            </td>
            <td width="100" style="font-size:12px"><b>PO Qty.</b>   </td>
            <td width="110">:&nbsp;<? echo  array_sum($po_quantity); ?></td>
            <td style="font-size:12px" ><b>Delivery To </b>   </td>
            <td style="">:&nbsp;
            <?
            //echo $attention;
            ?>
            </td>

            </tr>
            <tr>

            <td width="100" style="font-size:12px"><b>Season</b> </td>
            <td width="110">:&nbsp;<? echo implode(",",array_unique($season)); ?></td>

            <td width="100" style="font-size:12px"><b>Currency</b></td>
            <td width="110">:&nbsp;<?  echo $currency[$currency_id]; ?></td>

            </tr>
            <tr>
            <td style="font-size:12px" ><b>Attention </b>   </td>
            <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
            <?
            echo $attention;
            ?>
            </td>
            <td width="100" style="font-size:12px"><b>Order Repeat </b> </td>
            <td width="110">:&nbsp;<? echo $order_rept_no; ?></td>
              <td style="font-size:12px"><b>Conversion Rate</b></td>
            <td>:&nbsp;
            <?
            echo $exchange_rate;
            ?>
            </td>



            </tr>

            <tr>
            <td style="font-size:12px"><b>Source</b></td>
            <td>:&nbsp;<? echo $source[$source_id];?></td>
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
            <td width="100" style="font-size:12px"><b>Remarks</b>  </td>
            <td width="110" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="5">:&nbsp;<? echo $remarks; ?></td>

            </tr>
        </table>


		<?

		$booking_grand_total=0;

        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		$precost_arr=array();
		$trims_qtyPerUnit_arr=array();
		$precost_sql=sql_select("select a.id, a.job_no,a.trim_group,a.calculatorstring, c.cal_parameter from wo_pre_cost_trim_cost_dtls a,wo_booking_dtls b, lib_item_group c where a.job_no=b.job_no and a.trim_group=b.trim_group and a.trim_group=c.id and b.booking_no=$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.status_active=1 and b.is_deleted=0");
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
			//$precost_arr[$precost_row[csf('job_no')]][$precost_row[csf('trim_group')]]['calculatorstring']=$precost_row[csf('calculatorstring')];
			//$precost_arr[$precost_row[csf('job_no')]][$precost_row[csf('trim_group')]]['cal_parameter_uom']=$calUom;

			//$trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			//$trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
		}
		$booking_country_arr=array();
		$nameArray_booking_country=sql_select( "select pre_cost_fabric_cost_dtls_id,sensitivity,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($nameArray_booking_country as $nameArray_booking_country_row){
			$country_id_string=explode(",",$nameArray_booking_country_row[csf('country_id_string')]);
			$tocu=count($country_id_string);
			for($cu=0;$cu<$tocu;$cu++){
				$booking_country_arr[$nameArray_booking_country_row[csf('pre_cost_fabric_cost_dtls_id')]][$nameArray_booking_country_row[csf('sensitivity')]][$country_id_string[$cu]]=$country_arr[$country_id_string[$cu]];
			}
		}

		$nameArray_job_po=sql_select( "select job_no from wo_booking_dtls  where booking_no=$txt_booking_no and status_active=1 and is_deleted=0 group by job_no order by job_no ");
		foreach($nameArray_job_po as $nameArray_job_po_row){
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'   and sensitivity=1 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
	    if(count($nameArray_item)>0){
        ?>
        &nbsp;
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >

            <tr>
                <td colspan="9" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td ><strong>As Per Garments Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]] ?></strong></td><td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item){
			$i++;
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color order by bid ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][1]);
				?>
                </td>
                <?
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription){
                ?>
                <td style="border:1px solid black"><?  if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];} ?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black; text-align:left">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>
                <td style="border:1px solid black; text-align:right">
				<?
				//echo number_format($result_itemdescription[csf('cons')],4);
				echo $result_itemdescription[csf('cons')];
				$item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
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
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo $item_desctiption_total;  ?></td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
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
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->


        <!--==============================================Size Sensitive START=========================================  -->
		<?
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=2 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
		if(count($nameArray_item)>0)
		{
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                 <table width="100%" style="table-layout: fixed;">
                <tr>
                <td ><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]] ?></strong></td><td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_size order by bid");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][2]);
				?>
                </td>
                <?
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black; text-align:left">
              <? echo $result_itemdescription[csf('item_size')];?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>


                <td style="border:1px solid black; text-align:right">
				 <?
				 echo $result_itemdescription[csf('cons')];
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
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
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold"><? echo $item_desctiption_total;  ?></td>
                 <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
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
        <br/>
        <?
		}
		?>

        <!--==============================================Size Sensitive END=========================================  -->

         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		 $nameArray_item=sql_select( "select pre_cost_fabric_cost_dtls_id, trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=3  and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="10" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td ><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]] ?></strong></td><td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid,b.description, b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,b.color_number_id order by bid ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][3]);
				?>
                </td>
                <?
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black; text-align:right">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   echo $color_library[$result_itemdescription[csf('color_number_id')]];
			   ?>
                </td>
               <td style="border:1px solid black; text-align:left">
               <?
			  // $calUom=$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['cal_parameter_uom'];
			   //$calQty=explode("_",$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['calculatorstring']);

			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			  // print_r($calQty);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>

                <td style="border:1px solid black; text-align:right">
				<?
				echo $result_itemdescription[csf('cons')];
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo $item_desctiption_total;  ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
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
        <br/>
        <?
		}
		?>

        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->

        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group");
	   if(count($nameArray_item)>0)
		{
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="12" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td ><strong>Color & size sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]] ?></strong></td><td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>


                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black; width:170px;"><strong>Gmts Color</strong> </td>

                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			 //$nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.description, b.brand_supplier order by bid ");
			 $nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.description, b.brand_supplier order by color_order,size_order"); //and  c.id=b.color_size_table_id

            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." and pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and status_active=1 and is_deleted=0 order by trim_group ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][4]);
				?>
                </td>
                <?



				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
				foreach($nameArray_color as $result_color)
                {

					?>

					<td style="border:1px solid black" ><? if($result_color[csf('description')]){echo $result_color[csf('description')];} ?> </td>
					<td style="border:1px solid black" ><? if($result_color[csf('brand_supplier')]){echo $result_color[csf('brand_supplier')]; }?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('gmt_color')]]; //echo $color_library[$gmtcolor_library[$result_color[csf('color_number_id')]]]; ?> </td>
					<td style="border:1px solid black; text-align:left">
					<? echo $result_color[csf('item_size')]; ?>
					</td>
                    <td style="border:1px solid black; text-align:left">
				   <?
                   //$calUom=$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['cal_parameter_uom'];
				   //$calQty=explode("_",$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['calculatorstring']);

				   $calUom=$trims_qtyPerUnit_arr[$result_color[csf('pre_cost_fabric_cost_dtls_id')]][2];
			       $calQty=explode("_",$trims_qtyPerUnit_arr[$result_color[csf('pre_cost_fabric_cost_dtls_id')]][1]);
				   if($calUom && end($calQty)){
					   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
				   }
                   ?>
                    </td>

					<td style="border:1px solid black; text-align:right">
					<?
					echo $result_color[csf('cons')];
					$item_desctiption_total += $result_color[csf('cons')] ;
					//echo number_format($item_desctiption_total,2);
					?>
                    </td>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]];  ?></td>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('rate')],4);
					/*$rate =$result_color[csf('amount')]/$result_color[csf('cons')];
					echo number_format($rate,4); */
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<?
					//$amount_as_per_gmts_color = $item_desctiption_total*  $result_color[csf('rate')];
					$amount_as_per_gmts_color =$result_color[csf('amount')];
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
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo $item_desctiption_total;  ?></td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }

            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="11"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
       <?
		}
	   ?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->



         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
       //$nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=0 order by trim_group ");
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=0 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1");
		//$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="8" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td ><strong>NO sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]] ?></strong></td><td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                 </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                 <td style="border:1px solid black"><strong>Item Color</strong> </td>
                 <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;

				$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color");

            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and trim_group=".$result_item[csf('trim_group')]." order by trim_group ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">

                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][0]);

				?>
                </td>
                <?
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];} ?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   //$calUom=$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['cal_parameter_uom'];
			   //$calQty=explode("_",$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['calculatorstring']);

			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>
                <?
				if($db_type==0)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");

						}

                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                 <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo $result_color_size_qnty[csf('cons')];
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?
                }
                ?>

                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;">
                <?
                if($color_tatal !='')
                {
                echo $color_tatal;
                }
                ?>
                </td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
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
		//print_r($color_tatal);
		}
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
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
       <table  width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
       <tr style="border:1px solid black;">
                <th width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</th><td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</th><td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
         <br/> <br/>
        <table width="100%">
        <tr>

        <td width="49%">
	        <?
	        	echo get_spacial_instruction($txt_booking_no);
	        ?>
    	</td>

    <td width="2%"></td>
      <?
	 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_booking_no and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	?>
    <td width="49%">
     <br/> <br/>
  <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="3" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="50%" style="border:1px solid black;">Name</th><th width="27%" style="border:1px solid black;">Approval Date</th><th width="20%" style="border:1px solid black;">Approval No</th>
                </tr>
            </thead>
            <tbody>
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
            </tbody>
        </table>
    <?
	/*if($show_comment==1)
	{}*/
	?>
    </td>
    </tr>
    </table>

    </div>
     <br/>
    <div>
		<?
        	echo signature_table(2, $cbo_company_name, "1330px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>

     <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');

    </script>
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
	$cbo_level=str_replace("'","",$cbo_level);
	$report_title=str_replace("'","",$report_title);
	$level_arr = array(1=>"PO Level",2=>"Job Level");
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size", "id", "size_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$marchentr_email = return_library_array("select id,team_member_email from lib_mkt_team_member_info ","id","team_member_email");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'","image_location");
	$address = "";
	$add_info = "";
	$nameArray=sql_select("select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name");
	if($nameArray[0][csf('plot_no')] != ''){
		$address.=$nameArray[0][csf('plot_no')].',';
	}
	if($nameArray[0][csf('level_no')] != ''){
		$address.=$nameArray[0][csf('level_no')].',';
	}
	if($nameArray[0][csf('road_no')] != ''){
		$address.=$nameArray[0][csf('road_no')].',';
	}
	if($nameArray[0][csf('block_no')] != ''){
		$address.=$nameArray[0][csf('block_no')].'<br>';
	}
	if($nameArray[0][csf('city')] != ''){
		$address.=$nameArray[0][csf('city')].',';
	}
	if($nameArray[0][csf('zip_code')] != 0 && $nameArray[0][csf('zip_code')] != ''){
		$address.='-'.$nameArray[0][csf('zip_code')].',';
	}
	if($nameArray[0][csf('province')] != ''){
		$address.=$nameArray[0][csf('province')].','.$country_arr[$nameArray[0][csf('country_id')]];
	}
	if($nameArray[0][csf('email')] != ''){
		$add_info.='Email: '.$nameArray[0][csf('email')];
	}
	if($nameArray[0][csf('website')] != ''){
		$add_info.=', Website: '.$nameArray[0][csf('website')];
	}
	/* Second Table Data */
	$po_booking_info=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");
	$job_data_arr=array();
	foreach ($po_booking_info as $result_buy){
		$job_data_attr = array('job_no','total_set_qnty','product_dept','product_code','pro_sub_dep','gmts_item_id','style_ref_no','style_description','dealing_marchant','season_matrix','order_repeat_no','qlty_label','client_id');
		foreach ($job_data_attr as $attr) {
			$job_data_arr[$result_buy[csf('job_no')]][$attr] = $result_buy[csf($attr)];
			if($attr == 'product_dept'){
				$job_data_arr[$result_buy[csf('job_no')]][$attr]=$product_dept[$result_buy[csf($attr)]];
			}
			if($attr == 'pro_sub_dep'){
				$job_data_arr[$result_buy[csf('job_no')]][$attr]=$pro_sub_dept_array[$result_buy[csf($attr)]];
			}
			if($attr == 'dealing_marchant'){
				$job_data_arr[$attr][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf($attr)]];
			}
			if($attr == 'season_matrix'){
				$job_data_arr[$result_buy[csf('job_no')]][$attr]=$season_arr[$result_buy[csf($attr)]];
			}
			if($attr == 'qlty_label'){
				$job_data_arr[$result_buy[csf('job_no')]][$attr]=$quality_label[$result_buy[csf($attr)]];
			}
		}
		$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
		$job_data_arr['dealing_marchant_email'][$result_buy[csf('job_no')]]=$marchentr_email[$result_buy[csf('dealing_marchant')]];
		$job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
	}
	$job_no= implode(", ",array_unique($job_data_arr['job_no']));
	$job_no_in= implode(", ",array_unique($job_data_arr['job_no_in']));
	$product_depertment=implode(", ",array_unique($job_data_arr['product_dept']));
	$product_code=implode(", ",array_unique($job_data_arr['product_code']));
	$pro_sub_dep=implode(", ",array_unique($job_data_arr['pro_sub_dep']));
	$gmts_item_id=implode(", ",array_unique($job_data_arr['gmts_item_id']));
	$style_sting=implode(", ",array_unique($job_data_arr['style_ref_no']));
	$style_description=implode(", ",array_unique($job_data_arr['style_description']));
	$dealing_marchant=implode(", ",array_unique($job_data_arr['dealing_marchant']));
	$dealing_marchant_email=implode(", ",array_unique($job_data_arr['dealing_marchant_email']));
	$season_matrix=implode(", ",array_unique($job_data_arr['season_matrix']));
	$order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
	$qlty_label= implode(", ",array_unique($job_data_arr['qlty_label']));
	$client_id= implode(", ",array_unique($job_data_arr['client_id']));

	$booking_master_info=sql_select( "select a.buyer_id, a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.rmg_process_breakdown, a.insert_date, a.update_date, a.uom, a.remarks, a.pay_mode, a.fabric_composition,a.source from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0 ");
	$booking_grand_total=0;
	$currency_id="";
	?>
	<html>
	<head>
	</head>

	<table style="border:1px solid black;table-layout: fixed; " width="100%">
				<tr>
					<td rowspan="6"><?php if ($image_location != '') {?> <img src="../../<? echo $image_location; ?>" height="70" width="100"></td><?php }?>
				</tr>
				<tr>
					<td style="font-size:20px; text-align: center;"><strong><? echo $company_library[$cbo_company_name]; ?></strong></td>
					<td rowspan="4" style="font-size:16px; text-align: right;"><b> Booking No:<? echo $booking_master_info[0][csf('booking_no')];?></b></td>
				</tr>
				<tr>
					<td style="font-size:16px; text-align: center;"><?php echo $address; ?>
					</td>
				</tr>
				<tr>
					<td style="font-size:16px; text-align: center;"> <label style="text-align: center;"><?php echo $add_info ?></label></td>
				</tr>
				<tr>
					<td style="text-align: center; font-size:16px; text-decoration: underline; font-weight: bold;"><? echo $report_title ?></td>
				</tr>
	</table>
	<?
	foreach ($booking_master_info as $result) {
		$currency_id=$result[csf('currency_id')];
		$booking_date=$result[csf('update_date')];
		$pay_mode_id=$pay_mode[$result[csf('pay_mode')]];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			$booking_date=$result[csf('insert_date')];
			}
	 	?>
		<table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-top: 5px; margin-bottom: 5px" >
	        <tr>
	        	<th width="175" style="text-align: left">Supplier Name </th>
	            <td width="175" ><?
					if($result[csf('pay_mode')]==5){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				?>
				</td>
				<th width="175" style="text-align: left">Dealing Merchant </th>
	            <td width="175" ><? echo $dealing_marchant; ?></td>

	            <th width="175" style="text-align: left">Buyer/Agent Name</th>
	            <td width="175"><? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></td>
	        </tr>
	        <tr>
	        	<th style="text-align: left">Attention </th>
	            <td><? echo $result[csf('attention')]; ?></td>
	            <th style="text-align: left">Merchant E-Mail id </th>
	            <td><? echo $dealing_marchant_email ?></td>
	            <th style="text-align: left">Garments Item </th>
	            <td><?
		            $gmts_item_name="";
					$gmts_item=explode(',',$gmts_item_id);
					for($g=0;$g<=count($gmts_item); $g++)
					{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
					}
					echo rtrim($gmts_item_name,',');
				?>
				</td>
	        </tr>
	        <tr>
	            <th style="text-align: left">Booking Date </th>
	            <td><? echo change_date_format($booking_date,'dd-mm-yyyy','-','');?></td>
	            <th style="text-align: left">Trims ETD </th>
	            <td><? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
	            <th style="text-align: left">Pay mode </th>
	            <td><? echo $pay_mode_id;//$grouping ?></td>
	        </tr>
	        <tr>
	        	<th style="text-align: left">Remarks </th>
	            <td colspan="5"><? echo $result[csf('remarks')]?></td>
	        </tr>
	    </table>
		<?
	}?>
	<?
	$booking_country_arr=array();
	$nameArray_booking_country=sql_select( "select pre_cost_fabric_cost_dtls_id,sensitivity,country_id_string from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0");
	foreach($nameArray_booking_country as $nameArray_booking_country_row){
		$country_id_string=explode(",",$nameArray_booking_country_row[csf('country_id_string')]);
		$tocu=count($country_id_string);
		for($cu=0;$cu<$tocu;$cu++){
			$booking_country_arr[$nameArray_booking_country_row[csf('pre_cost_fabric_cost_dtls_id')]][$nameArray_booking_country_row[csf('sensitivity')]][$country_id_string[$cu]]=$country_arr[$country_id_string[$cu]];
		}
	}

	$no_sensitive_arr = array();
	$color_size_arr = array();
	$contrast_color_arr = array();
	$size_sensitive_arr = array();
	$as_per_gmts_color_arr = array();
	?>
	<!--===================================AS PER GMTS COLOR START===========================  -->
	<?
		$as_per_gmts_color = sql_select("SELECT b.job_no_mst, a.pre_cost_fabric_cost_dtls_id, e.id, b.pub_shipment_date, b.po_number, b.grouping, b.file_no, sum(b.po_quantity) as po_quantity, a.trim_group, c.cons_uom, c.brand_sup_ref, c.description, avg(e.rate) as rate, c.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark, f.cal_parameter,sum(e.requirment) as booking_cons ,e.color_number_id ,e.item_size, e.gmts_sizes ,e.article_number from wo_booking_dtls a, wo_po_break_down b , wo_pre_cost_trim_cost_dtls c , wo_po_details_master d, wo_trim_book_con_dtls e, lib_item_group f where a.id= e.wo_trim_booking_dtls_id and a.booking_no=e.booking_no and a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.trim_group = f.id and a.pre_cost_fabric_cost_dtls_id = c.id and a.job_no=d.job_no  and a.sensitivity=1 and e.requirment !=0 and e.status_active=1 and  e.is_deleted=0 group by b.job_no_mst, e.id, b.pub_shipment_date, b.po_number,b.grouping, b.file_no ,a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.cons_uom, c.brand_sup_ref, c.description, e.rate, c.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark,f.cal_parameter ,e.color_number_id,e.item_size,e.gmts_sizes,e.article_number order by b.po_number");
		if(count($as_per_gmts_color)>0){
			foreach ($as_per_gmts_color as $key => $value) {
				$gmts_color_attr =array('grouping','style_ref_no');
				foreach ($gmts_color_attr as $attr) {
					$as_per_gmts_color_arr[$value[csf('job_no_mst')]][$attr] = $value[csf($attr)];
				}
				$sub_gmts_color_attr = array('id', 'pub_shipment_date', 'grouping', 'file_no', 'po_quantity', 'trim_group', 'cons_uom', 'brand_sup_ref', 'description', 'rate', 'amount','item_color','calculatorstring','remark','cal_parameter','booking_cons','color_number_id','gmts_sizes','item_size','article_number','po_number');
				foreach ($sub_gmts_color_attr as $attr) {
					$as_per_gmts_color_arr[$value[csf('job_no_mst')]]['trim_groups'][$value[csf('trim_group')]][$value[csf('id')]][$attr] = $value[csf($attr)];
				}
			}
		}


	?>
    <!--===================================AS PER GMTS COLOR END=============================  -->
    <!--==================================Size Sensitive START===============================  -->
	<?
		$size_sensitive = sql_select("SELECT b.job_no_mst, a.pre_cost_fabric_cost_dtls_id, e.id, b.pub_shipment_date, b.po_number, b.grouping, b.file_no, sum(b.po_quantity) as po_quantity, a.trim_group, c.cons_uom, c.brand_sup_ref, c.description, avg(e.rate) as rate , c.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark, f.cal_parameter,sum(e.requirment) as booking_cons ,e.color_number_id ,e.item_size, e.gmts_sizes ,e.article_number from wo_booking_dtls a, wo_po_break_down b , wo_pre_cost_trim_cost_dtls c , wo_po_details_master d, wo_trim_book_con_dtls e, lib_item_group f where a.id= e.wo_trim_booking_dtls_id and a.booking_no=e.booking_no and a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.trim_group = f.id and a.pre_cost_fabric_cost_dtls_id = c.id and a.job_no=d.job_no  and a.sensitivity=2 and e.requirment !=0 and e.status_active=1 and  e.is_deleted=0 group by b.job_no_mst, e.id, b.pub_shipment_date, b.po_number,b.grouping, b.file_no ,a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.cons_uom, c.brand_sup_ref, c.description, e.rate, c.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark,f.cal_parameter ,e.color_number_id,e.item_size,e.gmts_sizes,e.article_number order by b.po_number");

		if(count($size_sensitive)>0){
			foreach ($size_sensitive as $key => $value) {
				$size_sensitive_attr =array('grouping','style_ref_no');
				foreach ($size_sensitive_attr as $attr) {
					$size_sensitive_arr[$value[csf('job_no_mst')]][$attr] = $value[csf($attr)];
				}
				$sub_size_sensitive_attr = array('id', 'pub_shipment_date', 'grouping', 'file_no', 'po_quantity', 'trim_group', 'cons_uom', 'brand_sup_ref', 'description', 'rate', 'amount','item_color','calculatorstring','remark','cal_parameter','booking_cons','color_number_id','gmts_sizes','item_size','article_number','po_number');
				foreach ($sub_size_sensitive_attr as $attr) {
					$size_sensitive_arr[$value[csf('job_no_mst')]]['trim_groups'][$value[csf('trim_group')]][$value[csf('id')]][$attr] = $value[csf($attr)];
				}
			}
		}

	?>
    <!--==============================Size Sensitive END====================================  -->
    <!--==============================AS PER CONTRAST COLOR START===========================  -->
	<?
		$contrast_color = sql_select("SELECT b.job_no_mst, a.pre_cost_fabric_cost_dtls_id, e.id, b.pub_shipment_date, b.po_number, b.grouping, b.file_no, sum(b.po_quantity) as po_quantity, a.trim_group, c.cons_uom, c.brand_sup_ref, c.description, avg(e.rate) as rate, c.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark, f.cal_parameter,sum(e.requirment) as booking_cons ,e.color_number_id ,e.item_size, e.gmts_sizes from wo_booking_dtls a, wo_po_break_down b , wo_pre_cost_trim_cost_dtls c , wo_po_details_master d, wo_trim_book_con_dtls e, lib_item_group f where a.id= e.wo_trim_booking_dtls_id and a.booking_no=e.booking_no and a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.trim_group = f.id and a.pre_cost_fabric_cost_dtls_id = c.id and a.job_no=d.job_no  and a.sensitivity=3 and e.requirment !=0 and e.status_active=1 and  e.is_deleted=0 group by b.job_no_mst, e.id, b.pub_shipment_date, b.po_number,b.grouping, b.file_no ,a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.cons_uom, c.brand_sup_ref, c.description, e.rate, c.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark,f.cal_parameter ,e.color_number_id,e.item_size,e.gmts_sizes order by b.po_number");

		if(count($contrast_color)>0){
			foreach ($contrast_color as $key => $value) {
				$contrast_color_attr =array('grouping','style_ref_no');
				foreach ($contrast_color_attr as $attr) {
					$contrast_color_arr[$value[csf('job_no_mst')]][$attr] = $value[csf($attr)];
				}
				$sub_contrast_color_attr = array('id', 'pub_shipment_date', 'grouping', 'file_no', 'po_quantity', 'trim_group', 'cons_uom', 'brand_sup_ref', 'description', 'rate', 'amount','item_color','calculatorstring','remark','cal_parameter','booking_cons','color_number_id','gmts_sizes','item_size','po_number');
				foreach ($sub_contrast_color_attr as $attr) {
					$contrast_color_arr[$value[csf('job_no_mst')]]['trim_groups'][$value[csf('trim_group')]][$value[csf('id')]][$attr] = $value[csf($attr)];
				}
			}
		}
		//echo "<pre>".print_r($size_sensitive_arr, true); die;
	?>
    <!--=============================AS PER CONTRAST COLOR ==================================  -->
    <!--=============================AS PER GMTS Color & SIZE START==========================  -->
	<?
		$color_size=sql_select("SELECT b.job_no_mst, a.pre_cost_fabric_cost_dtls_id, e.id, b.pub_shipment_date, b.po_number, b.grouping, b.file_no, sum(b.po_quantity) as po_quantity, a.trim_group, c.cons_uom, c.brand_sup_ref, c.description, avg(e.rate) as rate , c.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark, f.cal_parameter,sum(e.requirment) as booking_cons ,min(g.color_order) as color_order, min(g.size_order) as size_order, g.article_number, e.color_number_id ,e.item_size, e.gmts_sizes from wo_booking_dtls a, wo_po_break_down b , wo_pre_cost_trim_cost_dtls c , wo_po_details_master d, wo_trim_book_con_dtls e, lib_item_group f ,wo_po_color_size_breakdown g where a.id= e.wo_trim_booking_dtls_id and a.booking_no=e.booking_no and a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.trim_group = f.id and a.pre_cost_fabric_cost_dtls_id = c.id and a.job_no=d.job_no and  e.po_break_down_id=g.po_break_down_id and  e.color_number_id=g.color_number_id and e.gmts_sizes=g.size_number_id and  g.id=e.color_size_table_id and a.sensitivity=4 and e.requirment !=0 and e.status_active=1 and  e.is_deleted=0 group by b.job_no_mst, e.id, b.pub_shipment_date, b.po_number,b.grouping, b.file_no ,a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.cons_uom, c.brand_sup_ref, c.description, e.rate, c.amount, d.style_ref_no,e.item_color,c.calculatorstring,c.remark,f.cal_parameter ,g.article_number,e.color_number_id,e.item_size,e.gmts_sizes order by b.po_number");

		if(count($color_size)>0){
			foreach ($color_size as $key => $value) {
				$color_size_attr =array('grouping','style_ref_no');
				foreach ($color_size_attr as $attr) {
					$color_size_arr[$value[csf('job_no_mst')]][$attr] = $value[csf($attr)];
				}
				$sub_color_size_attr = array('id', 'pub_shipment_date', 'grouping', 'file_no', 'po_quantity', 'trim_group', 'cons_uom', 'brand_sup_ref', 'description', 'rate', 'amount','item_color','calculatorstring','remark','cal_parameter','booking_cons','article_number','color_number_id','gmts_sizes','item_size','po_number');
				foreach ($sub_color_size_attr as $attr) {
					$color_size_arr[$value[csf('job_no_mst')]]['trim_groups'][$value[csf('trim_group')]][$value[csf('id')]][$attr] = $value[csf($attr)];
				}
			}
		}
   	?>
    <!--===================================AS PER Color & SIZE ==============================  -->
    <!--===================================NO SENSITIBITY START===============================  -->
	<?
		$no_sensitive_item=sql_select("SELECT a.pre_cost_fabric_cost_dtls_id, a.trim_group, b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity,  c.cons_uom, c.brand_sup_ref, c.description, c.calculatorstring, c.remark, d.style_ref_no ,e.item_color, sum(e.requirment) as booking_cons, avg(e.rate) as rate, e.amount, f.cal_parameter from wo_booking_dtls a, wo_po_break_down b , wo_pre_cost_trim_cost_dtls c , wo_po_details_master d,wo_trim_book_con_dtls e,lib_item_group f where a.id= e.wo_trim_booking_dtls_id and a.booking_no=e.booking_no and a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.trim_group = f.id and a.pre_cost_fabric_cost_dtls_id = c.id and a.job_no=d.job_no and a.sensitivity=0 group by b.job_no_mst, b.id, b.pub_shipment_date, b.po_number,b.grouping, b.file_no ,a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.cons_uom, c.brand_sup_ref, c.description, e.rate, e.amount, d.style_ref_no,e.item_color,c.calculatorstring,c.remark,f.cal_parameter order by b.po_number");

		if(count($no_sensitive_item)>0)
		{
			foreach ($no_sensitive_item as $key => $value) {
				$sensitive_attr =array('grouping','style_ref_no');
				foreach ($sensitive_attr as $attr) {
					$no_sensitive_arr[$value[csf('job_no_mst')]][$attr] = $value[csf($attr)];
				}
				$trim_cost_attr = array('id', 'pub_shipment_date', 'grouping', 'file_no', 'po_quantity', 'trim_group', 'cons_uom', 'brand_sup_ref', 'description', 'rate', 'amount','item_color','calculatorstring','remark','cal_parameter','booking_cons','po_number');
				foreach ($trim_cost_attr as $attr) {
					$no_sensitive_arr[$value[csf('job_no_mst')]]['trim_cost_dtls'][$value[csf('pre_cost_fabric_cost_dtls_id')]][$value[csf('po_number')]][$attr] = $value[csf($attr)];
				}
			}
		}
		/*echo '<pre>';
		print_r($no_sensitive_arr); die;*/

	$cal_parameter_arr = array(1=>"Mtr",2=>"Pcs",3=>"Pcs",4=>"Pcs",5=>"Yds",6=>"Yds",7=>"Pcs",8=>"Yds");
	if(count($as_per_gmts_color_arr) > 0){
		foreach ($as_per_gmts_color_arr as $job_no => $data_arr) { ?>
			<table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-bottom: 10px" >
				<tr>
				<? $header ='As Per Garments Color (Job No: '.$job_no.') Style NO: '.$data_arr['style_ref_no'].' Int. Ref. NO:'.$data_arr['grouping']; ?>
                <td colspan="<? echo ($show_comment==1 ? '11':'10') ?>"><strong><? echo $header ?></strong></td>
            	</tr>
            	<tr>
                <th>Sl</th>
                <th>Item Group</th>
                <th>Item Description</th>
                <th>Brand/Supplier Ref.</th>
                <th>Item Color</th>
                <th>Po. No.</th>
                <th>Po. Qty</th>
                <th>Qty per Unit</th>
                <th>WO Qty.</th>
                <th>UOM</th>
                <th>Rate</th>
                <th>Amount</th>
                <? if($show_comment==1) {?>
                <th>Remarks</th>
                <? } ?>
            </tr><?
            $i=1;
            $po_qty = '';
            $po_amount = '';
            foreach ($data_arr['trim_groups'] as $group_id=>$group_data) { ?>
            	<tr>
        			<td rowspan="<? echo count($group_data) ?>"><? echo $i; ?></td>
        			<td rowspan="<? echo count($group_data) ?>"><? echo $trim_group_library[$group_id];?> </td>
            	<?
            	$group_qty='';
            	$group_amount = '';
            	foreach ($group_data as $data) {
            		$calQty=explode("_",$data['calculatorstring']);
				   	if($data['cal_parameter'] && end($calQty)){
					   $per_unit= "1".$unit_of_measurement[$order_uom_arr[$data['trim_group']]]."=".$calQty[2]." ".$cal_parameter_arr[$data['cal_parameter']];
				    }
				    else{
				    	$per_unit = '';
				    }
					$amount = $data['rate']*$data['booking_cons'];
					?>
        			<td><? echo $data['description']; ?></td>
        			<td><? echo $data['brand_sup_ref']; ?></td>
        			<td><? echo $color_library[$data['item_color']]; ?></td>
        			<td><? echo $data['po_number']; ?></td>
        			<td><? echo $data['po_quantity']; ?></td>
        			<td><? echo $per_unit; ?></td>
					<td><? echo number_format($data['booking_cons'],4); ?></td>
        			<td><? echo $unit_of_measurement[$order_uom_arr[$data['trim_group']]] ?></td>
        			<td><? echo number_format($data['rate'],4); ?></td>
        			<td><? echo number_format($amount,4); ?></td>
        			<? if($show_comment==1) {?>
        			<td><? echo $data['remark'] ?></td>
        			<? } ?>
            	</tr>
					<?
					$group_qty += $data['booking_cons'];
            		$group_amount += $amount;
            	}
            	$i++; ?>
            	<tr>
		         	<th colspan="8" style="text-align: right;">Item Qty.</th>
		         	<th><? echo number_format($group_qty,4); $po_qty += $group_qty; ?></th>
		         	<th colspan="2">Item Amount</th>
		         	<th><? echo number_format($group_amount,4); $po_amount += $group_amount; ?></th>
		         	<? if($show_comment==1) {?>
        			<td>&nbsp;</td>
        			<? } ?>
		        </tr>
            	<?
            }
            ?>
            <tr>
	         	<th colspan="8" style="text-align: right;">Total Qty.</th>
	         	<th><? echo number_format($po_qty,4) ?></th>
	         	<th colspan="2">Total Amount</th>
	         	<th><? echo number_format($po_amount,4); $booking_grand_total+=$po_amount;?></th>
	         	<? if($show_comment==1) {?>
        			<td>&nbsp;</td>
        		<? } ?>
	        </tr>
			</table><?
		}
	}
	if(count($size_sensitive_arr) > 0){
		foreach ($size_sensitive_arr as $job_no => $data_arr) {
			//foreach ($po_no_arr as $po_number => $data_arr) { ?>
				<table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-bottom: 10px" >
					<tr>
					<? $header ='Size Sensitive (Job No: '.$job_no.') Style NO: '.$data_arr['style_ref_no'].',Int. Ref. NO:'.$data_arr['grouping']; ?>
	                <td colspan="<? echo ($show_comment==1 ? '12':'11') ?>"><strong><? echo $header ?></strong></td>
	            	</tr>
	            	<tr>
	                <th>Sl</th>
	                <th>Item Group</th>
	                <th>Item Description</th>
	                <th>Brand/Supplier Ref.</th>
	                <th>Article No</th>
	                <th>Item Size</th>
	                <th>Po. No.</th>
	                <th>Po. Qty.</th>
	                <th>Qty per Unit</th>
	                <th>WO Qty.</th>
	                <th>UOM</th>
	                <th>Rate</th>
	                <th>Amount</th>
	                <? if($show_comment==1) {?>
	                <th>Remarks</th>
	                <? } ?>
	            </tr><?
	            $i=1;
	            $po_qty = '';
	            $po_amount = '';
	            foreach ($data_arr['trim_groups'] as $group_id=>$group_data) { ?>
	            	<tr>
            			<td rowspan="<? echo count($group_data) ?>"><? echo $i; ?></td>
            			<td rowspan="<? echo count($group_data) ?>"><? echo $trim_group_library[$group_id];?> </td>
	            	<?
	            	$group_qty='';
	            	$group_amount = '';
	            	foreach ($group_data as $data) {
	            		$calQty=explode("_",$data['calculatorstring']);
					   	if($data['cal_parameter'] && end($calQty)){
						   $per_unit= "1".$unit_of_measurement[$order_uom_arr[$data['trim_group']]]."=".$calQty[2]." ".$cal_parameter_arr[$data['cal_parameter']];
					    }
					    else{
					    	$per_unit = '';
					    }
						$amount = $data['rate']*$data['booking_cons'];
						?>
            			<td><? echo $data['description']; ?></td>
            			<td><? echo $data['brand_sup_ref']; ?></td>
            			<td><? echo ($result_color[csf('article_number')]!="no article" ? ' - ':$result_color[csf('article_number')]); ?></td>
            			<td><? echo $data['item_size']; ?></td>
            			<td><? echo $data['po_number']; ?></td>
            			<td><? echo $data['po_quantity']; ?></td>
            			<td><? echo $per_unit; ?></td>
						<td><? echo number_format($data['booking_cons'],4); ?></td>
            			<td><? echo $unit_of_measurement[$order_uom_arr[$data['trim_group']]] ?></td>
            			<td><? echo number_format($data['rate'],4); ?></td>
            			<td><? echo number_format($amount,4); ?></td>
            			<? if($show_comment==1) {?>
            			<td><? echo $data['remark'] ?></td>
            			<? } ?>
	            	</tr>
						<?
						$group_qty += $data['booking_cons'];
	            		$group_amount += $amount;
	            	}
	            	$i++; ?>
	            	<tr>
			         	<th colspan="9" style="text-align: right;">Item Qty.</th>
			         	<th><? echo number_format($group_qty,4); $po_qty += $group_qty; ?></th>
			         	<th colspan="2">Item Amount</th>
			         	<th><? echo number_format($group_amount,4); $po_amount += $group_amount; ?></th>
			         	<? if($show_comment==1) {?>
            			<td>&nbsp;</td>
            			<? } ?>
			        </tr>
	            	<?
	            }
	            ?>
	            <tr>
		         	<th colspan="9" style="text-align: right;">Total Qty.</th>
		         	<th><? echo number_format($po_qty,4) ?></th>
		         	<th colspan="2">Total Amount</th>
		         	<th><? echo number_format($po_amount,4); $booking_grand_total+=$po_amount;?></th>
		         	<? if($show_comment==1) {?>
            			<td>&nbsp;</td>
            		<? } ?>
		        </tr>
				</table><?
			//}
		}
	}
	if(count($contrast_color_arr) > 0){
		foreach ($contrast_color_arr as $job_no => $data_arr) {
			//foreach ($po_no_arr as $po_number => $data_arr) { ?>
				<table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-bottom: 10px" >
					<tr>
					<? $header ='Contrast Color (Job No: '.$job_no.') Style NO: '.$data_arr['style_ref_no'].',Int. Ref. NO:'.$data_arr['grouping']; ?>
	                <td colspan="<? echo ($show_comment==1 ? '12':'11') ?>"><strong><? echo $header ?></strong></td>
	            	</tr>
	            	<tr>
	                <th>Sl</th>
	                <th>Item Group</th>
	                <th>Item Description</th>
	                <th>Brand/Supplier Ref.</th>
	                <th>Item Color</th>
	                <th>Gmts Color</th>
	                <th>Po. No.</th>
	                <th>Po. Qty</th>
	                <th>Qty per Unit</th>
	                <th>WO Qty.</th>
	                <th>UOM</th>
	                <th>Rate</th>
	                <th>Amount</th>
	                <? if($show_comment==1) {?>
	                <th>Remarks</th>
	                <? } ?>
	            </tr><?
	            $i=1;
	            $po_qty = '';
	            $po_amount = '';
	            foreach ($data_arr['trim_groups'] as $group_id=>$group_data) { ?>
	            	<tr>
            			<td rowspan="<? echo count($group_data) ?>"><? echo $i; ?></td>
            			<td rowspan="<? echo count($group_data) ?>"><? echo $trim_group_library[$group_id];?> </td>
	            	<?
	            	$group_qty='';
	            	$group_amount = '';
	            	foreach ($group_data as $data) {
	            		$calQty=explode("_",$data['calculatorstring']);
					   	if($data['cal_parameter'] && end($calQty)){
						   $per_unit= "1".$unit_of_measurement[$order_uom_arr[$data['trim_group']]]."=".$calQty[2]." ".$cal_parameter_arr[$data['cal_parameter']];
					    }
					    else{
					    	$per_unit = '';
					    }
						$amount = $data['rate']*$data['booking_cons'];
						?>
            			<td><? echo $data['description']; ?></td>
            			<td><? echo $data['brand_sup_ref']; ?></td>
            			<td><? echo $color_library[$data['color_number_id']]; ?></td>
            			<td><? echo $color_library[$data['item_color']]; ?></td>
            			<td><? echo $data['po_number'] ?></td>
            			<td><? echo $data['po_quantity'] ?></td>
            			<td><? echo $per_unit; ?></td>
						<td><? echo number_format($data['booking_cons'],4); ?></td>
            			<td><? echo $unit_of_measurement[$order_uom_arr[$data['trim_group']]] ?></td>
            			<td><? echo number_format($data['rate'],4); ?></td>
            			<td><? echo number_format($amount,4); ?></td>
            			<? if($show_comment==1) {?>
            			<td><? echo $data['remark'] ?></td>
            			<? } ?>
	            	</tr>
						<?
						$group_qty += $data['booking_cons'];
	            		$group_amount += $amount;
	            	}
	            	$i++; ?>
	            	<tr>
			         	<th colspan="9" style="text-align: right;">Item Qty.</th>
			         	<th><? echo number_format($group_qty,4); $po_qty += $group_qty; ?></th>
			         	<th colspan="2">Item Amount</th>
			         	<th><? echo number_format($group_amount,4); $po_amount += $group_amount; ?></th>
			         	<? if($show_comment==1) {?>
            			<td>&nbsp;</td>
            			<? } ?>
			        </tr>
	            	<?
	            }
	            ?>
	            <tr>
		         	<th colspan="9" style="text-align: right;">Total Qty.</th>
		         	<th><? echo number_format($po_qty,4) ?></th>
		         	<th colspan="2">Total Amount</th>
		         	<th><? echo number_format($po_amount,4); $booking_grand_total+=$po_amount;?></th>
		         	<? if($show_comment==1) {?>
            			<td>&nbsp;</td>
            		<? } ?>
		        </tr>
				</table><?
			//}
		}
	}
	if(count($color_size_arr) > 0){
		foreach ($color_size_arr as $job_no => $data_arr) {
			//foreach ($po_no_arr as $po_number => $data_arr) { ?>
				<table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-bottom: 10px" >
					<tr>
					<? $header ='Color & size sensitive (Job No: '.$job_no.') Style NO: '.$data_arr['style_ref_no'].',Int. Ref. NO:'.$data_arr['grouping']; ?>
	                <td colspan="<? echo ($show_comment==1 ? '15':'14') ?>"><strong><? echo $header ?></strong></td>
	            	</tr>
	            	<tr>
	                <th>Sl</th>
	                <th>Item Group</th>
	                <th>Item Description</th>
	                <th>Brand/Supplier Ref.</th>
	                <th>Article No.</th>
	                <th>Item Color</th>
	                <th>Gmts Color</th>
	                <th>Gmts Size</th>
	                <th>Item Size</th>
	                <th>Po. No.</th>
	                <th>Po. Qty</th>
	                <th>Qty per Unit</th>
	                <th>WO Qty.</th>
	                <th>UOM</th>
	                <th>Rate</th>
	                <th>Amount</th>
	                <? if($show_comment==1) {?>
	                <th>Remarks</th>
	                <? } ?>
	            </tr><?
	            $i=1;
	            $po_qty = '';
	            $po_amount = '';
	            foreach ($data_arr['trim_groups'] as $group_id=>$group_data) { ?>
	            	<tr>
            			<td rowspan="<? echo count($group_data) ?>"><? echo $i; ?></td>
            			<td rowspan="<? echo count($group_data) ?>"><? echo $trim_group_library[$group_id];?> </td>
	            	<?
	            	$group_qty='';
	            	$group_amount = '';
	            	foreach ($group_data as $data) {
	            		$calQty=explode("_",$data['calculatorstring']);
					   	if($data['cal_parameter'] && end($calQty)){
						   $per_unit= "1".$unit_of_measurement[$order_uom_arr[$data['trim_group']]]."=".$calQty[2]." ".$cal_parameter_arr[$data['cal_parameter']];
					    }
					    else{
					    	$per_unit = '';
					    }
						$amount = $data['rate']*$data['booking_cons'];
						?>
            			<td><? echo $data['description']; ?></td>
            			<td><? echo $data['brand_sup_ref']; ?></td>
            			<td><? echo ($result_color[csf('article_number')]!="no article" ? '-':$result_color[csf('article_number')]); ?></td>
            			<td><? echo $color_library[$data['color_number_id']]; ?></td>
            			<td><? echo $color_library[$data['item_color']]; ?></td>
            			<td><? echo $data['gmts_sizes'] ?></td>
            			<td><? echo $data['item_size'] ?></td>
            			<td><? echo $data['po_number'] ?></td>
            			<td><? echo $data['po_quantity'] ?></td>
            			<td><? echo $per_unit; ?></td>
						<td><? echo number_format($data['booking_cons'],4); ?></td>
            			<td><? echo $unit_of_measurement[$order_uom_arr[$data['trim_group']]] ?></td>
            			<td><? echo number_format($data['rate'],4); ?></td>
            			<td><? echo number_format($amount,4); ?></td>
            			<? if($show_comment==1) {?>
            			<td><? echo $data['remark'] ?></td>
            			<? } ?>
	            	</tr>
						<?
						$group_qty += $data['booking_cons'];
	            		$group_amount += $amount;
	            	}
	            	$i++; ?>
	            	<tr>
			         	<th colspan="12" style="text-align: right;">Item Qty.</th>
			         	<th><? echo number_format($group_qty,4); $po_qty += $group_qty; ?></th>
			         	<th colspan="2">Item Amount</th>
			         	<th><? echo number_format($group_amount,4); $po_amount += $group_amount; ?></th>
			         	<? if($show_comment==1) {?>
            			<td>&nbsp;</td>
            			<? } ?>
			        </tr>
	            	<?
	            }
	            ?>
	            <tr>
		         	<th colspan="12" style="text-align: right;">Total Qty.</th>
		         	<th><? echo number_format($po_qty,4) ?></th>
		         	<th colspan="2">Total Amount</th>
		         	<th><? echo number_format($po_amount,4); $booking_grand_total+=$po_amount;?></th>
		         	<? if($show_comment==1) {?>
            			<td>&nbsp;</td>
            		<? } ?>
		        </tr>
				</table><?
			//}
		}
	}
	if(count($no_sensitive_arr) > 0){
		foreach ($no_sensitive_arr as $job_no => $data_arr) {
		//foreach ($po_no_arr as $po_number => $data_arr) { ?>
			<table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-bottom: 10px" >
				<tr>
					<? $header ='NO sensitive (Job No: '.$job_no.') Style NO: '.$data_arr['style_ref_no'].',Int. Ref. NO:'.$data_arr['grouping']; ?>
	                <td colspan="11"><strong><? echo $header ?></strong></td>
	            </tr>
	            <tr>
	                <th>Sl</th>
	                <th>Item Group</th>
	                <th>Item Description</th>
	                <th>Brand/Supplier Ref.</th>
	                <th>Item Color</th>
	                <th>Po. No.</th>
	                <th>Po. Qty</th>
	                <th>Qty per Unit</th>
	                <th>Qnty</th>
	                <th>UOM</th>
	                <th>Rate</th>
	                <th>Amount</th>
	                <? if($show_comment==1) {?>
	                <th>Remarks</th>
	                <? } ?>
	            </tr>
	            <? $i=1;
	            $po_total_amount='';
	    		$po_total_qty = '';
	            foreach ($data_arr['trim_cost_dtls'] as $pre_cost_data) {
	            	foreach ($pre_cost_data as $po_number => $data) {
            			$calQty=explode("_",$data['calculatorstring']);
	                   	if($data['cal_parameter'] && end($calQty)){
	                       $per_unit= "1".$unit_of_measurement[$order_uom_arr[$data['trim_group']]]."=".$calQty[2]." ".$cal_parameter_arr[$data['cal_parameter']];
	                    }
	                    else{
	                        $per_unit = '';
	                    }
	                    $amount = $data['rate']*$data['booking_cons'];
	                    ?>
	                    <tr>
	                        <td><? echo $i; ?></td>
	                        <td><? echo $trim_group_library[$data['trim_group']];?> </td>
	                        <td><? echo $data['description']; ?></td>
	                        <td><? echo $data['brand_sup_ref']; ?></td>
	                        <td><? echo $color_library[$data['item_color']]; ?></td>
	                        <td><? echo $data['po_number'] ?></td>
	                        <td><? echo $data['po_quantity'] ?></td>
	                        <td><? echo $per_unit; ?></td>
							<td><? echo number_format($data['booking_cons'],4); ?></td>
	                        <td><? echo $unit_of_measurement[$order_uom_arr[$data['trim_group']]] ?></td>
	                        <td><? echo number_format($data['rate'],4); ?></td>
	                        <td><? echo number_format($amount,4); ?></td>
	                        <? if($show_comment==1) {?>
	                        <td><? echo $data['remark'] ?></td>
	                        <? } ?>
	                    </tr>
		                <? $i++;
		                $po_total_amount += $amount;
		                $po_total_qty += $data['booking_cons'];
	            	}
	          	}
	         ?>
	         <tr>
	         	<th colspan="8" style="text-align: right;">Total Qty.</th>
	         	<th><? echo number_format($po_total_qty,4) ?></th>
	         	<th colspan="2">Total Amount</th>
	         	<th><? echo number_format($po_total_amount,4); $booking_grand_total+=$po_total_amount; ?></th>
	         </tr>
			</table>
		 <?
		 //}
		}
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
     $final_approved_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$txt_booking_no' and b.entry_form=8 and a.status_active =1 and a.is_deleted=0 and is_approved=1");
    ?>
        <td width="49%" valign="top">
            <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">

                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;">Approval Status</td>
                    </tr>
                    <tr style="border:1px solid black;">
                    <td width="3%" style="border:1px solid black;">Sl</td>
                    <td width="50%" style="border:1px solid black;">Name</td>
                    <td width="27%" style="border:1px solid black;">Approval Date</td>
                    </tr>


                <?

                if(count($final_approved_array)>0){
                	$i=1;
	                foreach($data_array as $row){
	                ?>
	                <tr style="border:1px solid black;">
	                    <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
	                    <td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')];?></td>
	                    <td width="27%" style="border:1px solid black;"><? echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td>
	                    </tr>
	                    <?
	                    $i++;
	                }
                }
                else{?>
                   <tr style="border:1px solid black;">
                   	<td colspan="3" Style="font-weight:bold; text-align:center; font-size:24px;">Draft</td>
                   </tr>
                <? }
                ?>

            </table>
    </td>
    </tr>
	</table>
   	 </tbody>
     </table>
     <!--class="footer_signature"-->
    <div  style="margin-top:-50px;">
         <?
          echo signature_table(2, $cbo_company_name, "1333px");
		 ?>
   </div>
      <div id="page_break_div">
   	 </div>
    <div>
		<?
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>
  </html>
	<?
	exit();
}
?>
