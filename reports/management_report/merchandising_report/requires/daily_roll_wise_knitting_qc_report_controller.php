<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

include ("../../../../ext_resource/excel/excel/vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action == "load_drop_down_knitting_com") {
	$data = explode("_", $data);
	//print_r($data);
	$company_id = $data[1];
	//$company_id
	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_company", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", "", "load_location();load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_controller', this.value, 'load_drop_down_floor', 'cbo_del_floor' );", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_knitting_company", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
	} else {
		echo create_drop_down("cbo_knitting_company", 120, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
	}
	exit();
}

if ($action=="load_drop_down_buyer")
{
	
	if($data!=0)
	{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0  $buyer_cond  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0,"" );
	}
	exit();
}
if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location_name", 120, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "load_floor();");
exit();
}

if ($action == "load_drop_down_floor") {
	$data = explode("_", $data);
	$company_id = $data[0];
	$location_id = $data[1];
	if ($location_id == 0 || $location_id == "") $location_cond = ""; else $location_cond = " and b.location_id=$location_id";

	echo create_drop_down("cbo_del_floor", 120, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "", "");
	exit();
}

/*if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_controller', $data+'**'+this.value, 'load_drop_down_del_floor', 'del_floor_td' );" );		 
}
if ($action=="load_drop_down_del_floor")
{ 
	$data=explode('**',$data);
	echo create_drop_down( "cbo_del_floor", 120, "select id,floor_name from lib_prod_floor where company_id='$data[0]' and location_id='$data[1]' and status_active =1 and is_deleted=0 and production_process=11 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "" );
	exit();	 
}*/

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
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
		
		function js_set_value( str ) 
		{
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
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'daily_roll_wise_knitting_qc_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}
if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];

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
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "70,70,50,70,150,180","760","220",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}

//$job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master",'job_no','set_smv');

//return_field_value("sum(a.ex_factory_qnty) as po_quantity"," pro_ex_factory_mst a, wo_po_break_down b","a.po_break_down_id=b.id and b.id='".$row[csf("po_id")]."' and a.is_deleted=0 and a.status_active=1","po_quantity");
//$lc_sc=return_field_value("b.contract_no as export_lc_no"," com_sales_contract b"," b.id in($sc_lc_id)' ","export_lc_no");
//$lc_sc=return_field_value("b.export_lc_no as export_lc_no","com_export_lc b"," b.id in($sc_lc_id) ","export_lc_no");
//$lc_type=return_field_value("is_lc","com_export_invoice_ship_mst","id in(".$row[csf('invoice_no')].")","is_lc");
//$last_ex_factory_date=return_field_value(" max(ex_factory_date) as ex_factory_date","pro_ex_factory_mst","po_break_down_id in(".$row[csf('po_id')].")","ex_factory_date");

if($action=="report_generate")
{ 
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_knitting_company=str_replace("'","",$cbo_knitting_company);
	$cbo_location_name=str_replace("'","",$cbo_location_name);
	$cbo_delivery_floor=str_replace("'","",$cbo_del_floor);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$hide_order_id=str_replace("'","",$hide_order_id);
	$txt_barcode_no=str_replace("'","",$txt_barcode_no);
	$txt_program_no=str_replace("'","",$txt_program_no);
	$txt_date_from_qc=str_replace("'","",$txt_date_from_qc);
	$txt_date_to_qc=str_replace("'","",$txt_date_to_qc);
	$cbo_roll_status=str_replace("'","",$cbo_roll_status);
	$cbo_date_range_type=str_replace("'","",$cbo_date_range_type);
	
	if($cbo_knitting_source>0){$knitting_source_cond="and a.knitting_source=$cbo_knitting_source";}else{$knitting_source_cond="";}
	if($cbo_delivery_floor>0){$del_floor_cond="and b.floor_id=$cbo_delivery_floor";}else{$del_floor_cond="";}
	if($hide_order_id!=""){$hide_order_cond="and c.po_breakdown_id in($hide_order_id)";}else{$hide_order_cond="";}
	if($cbo_buyer_name>0){$buyer_name_cond="and a.buyer_id =$cbo_buyer_name";}else{$buyer_name_cond="";}
	if($cbo_company_name>0){$company_name_cond="and a.company_id =$cbo_company_name";}else{$company_name_cond="";}

	$job_no_cond="and c.po_breakdown_id="."'".$orderID."'";
	//if($cbo_knitting_source>0){$knitting_source_cond="and a.knitting_source=$cbo_knitting_source";}else{$knitting_source_cond="";}

	$user_arr = return_library_array("select user_name, id  from user_passwd where is_deleted=0 and status_active=1", 'id', 'user_name');

	if($txt_job_no!="")
	{
		$jobs=trim($txt_job_no);
		$txt_jobNo="'".$jobs."'";
		// change this query
		//$po_id_arr = return_library_array("select job_no_mst, id from wo_po_break_down where job_no_mst=$txt_jobNo", 'job_no_mst', 'id');
		
		
		$sqls=sql_select("select job_no_mst, id from wo_po_break_down where job_no_mst=$txt_jobNo");
		//$orderID="";
		foreach($sqls as $k=>$val)
		{
			//$j=$val[csf("job_no_mst")];
			//$job="'".$j."'";
			//$orderID=$po_id_arr[$job].=$val[csf("id")].'';
			$poId_arr[$val[csf("id")]]=$val[csf("id")];
		}
		//$job_no_cond="and c.po_breakdown_id="."'".$orderID."'";
		$all_po_id=implode(",", $poId_arr);
		$job_no_cond="and c.po_breakdown_id in($all_po_id)";
		//$job_no_cond=$orderID;
	}
	else
	{
		$job_no_cond="";
	}

  	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	
	//if(trim($txt_job_no)!="") $job_no_cond="%".trim($txt_job_no); else $job_no_cond="%%";//."%"

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$str_cond_date="and a.receive_date between '$txt_date_from' and  '$txt_date_to ' ";
	}
	else
	{
		$str_cond_date="";
	}
	if($txt_date_from_qc!="" && $txt_date_to_qc!="")
	{
		$qc_date_cond="and d.qc_date between '$txt_date_from_qc' and  '$txt_date_to_qc ' ";
	}
	else
	{
		$qc_date_cond="";
	}
	if($cbo_knitting_company>0)
	{
		 $knit_comp_cond="and a.knitting_company='$cbo_knitting_company' ";
	}
	else
	{
		 $knit_comp_cond="";
	}
	if($cbo_location_name>0)
	{
		 $del_location_cond="and a.location_id='$cbo_location_name' ";
	}
	else
	{
		 $del_location_cond="";
	} 
	
	if($cbo_delivery_floor>0)
	{
		 $del_floor_cond="and b.FLOOR_ID='$cbo_delivery_floor' ";
	}
	else
	{
		 $del_floor_cond="";
	} 
	
	if($txt_barcode_no!="")
	{
		$barcode_cond="and c.barcode_no='$txt_barcode_no'";
	}
	else
	{
		$barcode_cond="";
	}
	if($txt_program_no!="")
	{
		$program_cond="and a.booking_no='$txt_program_no'";
	}
	else
	{
		$program_cond="";
	}
	
	if($txt_booking_no!="")
	{
		$booking_cond=" and a.booking_without_order=1 and a.booking_no like '%$txt_booking_no%'";
	}
	else
	{
		$booking_cond="";
	}
	
	$details_report="";
	$master_data=array();
	$current_date=date("Y-m-d");
	$date=date("Y-m-d");$break_id=0;$sc_lc_id=0;
	$sy = date('Y',strtotime($txt_date_from));
	//$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst where year=$sy",'comapny_id','basic_smv');
	
	//$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
	//$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
	
	ob_start();
	?>
    <style type="text/css">
        .block_div { 
			width:auto;
			height:auto;
			text-wrap:normal;
			vertical-align:bottom;
			display: block;
			position: !important; 
			-webkit-transform: rotate(-90deg);
			-moz-transform: rotate(-90deg);					
        }
        .breakAll{
				word-break:break-all;
				word-wrap: break-word;
			}
    </style> 
        
  
	<? 	
	
	
	if($report_format==1) // Show
	{
		?>
		<fieldset style="width:6345px;">
			<table width="1260">
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption" style="font-size:18px;">Daily Roll wise Knitting QC Report</td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr>
				<tr>
				   <td align="center" width="100%" colspan="12" class="form_caption" style="font-size:12px;"><? echo   show_company($cbo_knitting_company,'',''); ?></td>
				</tr>
			</table>
			  <div style="width:6295px; float:left;">
				<table width="6290" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left;">
					<thead>
						<tr height="100">
							<th width="40">SL</th>
							<th width="70">Date</th>
							<th width="110">Knitting Source</th>
							<th width="150">Knitting Company</th>
							<th width="100">Production No</th>
							<th width="100">Barcode</th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">MC NO#</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">Roll No</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">SHIFT</div></th>
							<th width="100">BUYER</th>
							<th width="100">Job No</th>
							<th width="200">ORDER NO</th>
							<th width="100">Style Ref.</th>
							<th width="120">Booking Type</th>
							<th width="100">Booking No</th>
							<th width="60" style="vertical-align:middle"><div class="block_div" >ROLL WEIGHT</div></th>
							<th width="100">YARN COUNT</th>
							<th width="100">Fabric Color</th>
							<th width="100">Yarn Type</th>
							<th width="100">SUPPLIER</th>
							<th width="100">YARN LOT</th>
							<th width="100">Yarn Composition</th>
							<th width="100">M/C DIA</th>
							<th width="100">REQ DIA</th>
							<th width="100">ACTUAL DIA</th>
							<th width="100">Variance</th>
							<th width="100">Stitch Length</th>
							<th width="100">GAUGE</th>
							<th width="100">FABRIC TYPE</th>
							<th width="100">REQ.GSM</th>
							<th width="100">ACTUAL.GSM</th>
							<th width="100">Variance</th>
							<th width="70">Qc Pass Qty</th>
							<th width="70">Roll Status</th>
							<th width="100">Rate/ kg</th>
							<th width="100">Total TK</th>
							<th width="130">Delivery Challan No</th>
							<th width="100">Delivery Date</th>
							
                            
                            <th width="60" style="vertical-align:middle"><div class="rotate_90_deg">HOLE</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">LOOP</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">PRESS OFF</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">LYCRA OUT</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">LYCRA DROP</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">DUST</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">OIL SPOT</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">FLY CONTA</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">SLUB</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">PATTA</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">NEEDLE BREAK</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">SINKER MARK</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">WHEEL FREE</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">COUNT MIX</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">YARN CONTRA</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">NEPS</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">BLACK SPOT</div></th>
							<th width="100" style="vertical-align:middle"><div class="rotate_90_deg">OIL/INK MARK</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">SET UP</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">PIN HOLE</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">SLUB HOLE</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">NEEDLE MARK</div></th>
							
                            <th width="60" style="vertical-align:middle" class="breakAll"><div class="rotate_90_deg">Contamination</div></th>
                            <th width="60" style="vertical-align:middle"><div class="rotate_90_deg">Thick and Thin</div></th>
                            
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">MISS YARN</div></th>
							
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">TTL POINTS</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">GRADE</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">DEFECT %</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">LENGTH YDS</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">Reject Qty</div></th>
							<th width="100" style="vertical-align:middle"><div class="rotate_90_deg">RESPONSIBLE</div></th>
							<th width="100" style="vertical-align:middle"><div class="rotate_90_deg">DESIGNATION</div></th>
							<th width="100" style="vertical-align:middle"><div class="rotate_90_deg">REASON FOR REJECTION</div></th>
							<th width="100">Operator Name</th>
							<th width="100">Supervisor</th>
							<th width="100">Defective Length</th>
							<th style="vertical-align:middle"><div class="rotate_90_deg">REMARKS</div></th>
						</tr>
					</thead>
				</table>

				<div style="width:6295px; float:left; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table width="6277" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body" style="float:left;">
					<tbody>
					<?

					$con = connect();
					execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=174");
					execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=174");
					oci_commit($con);

					$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
					$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
					$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");				
					$yarn_count_lib_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
					$operator_lib_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
					$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
					
					//$non_booking_arr=return_library_array( "select  id,booking_no  from  wo_non_ord_samp_booking_mst ", "id", "booking_no"  );
					
					$knitting_company_library = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
					$add_table_cond=$add_where_cond="";
					if ($cbo_roll_status!=0) 
					{
						$add_table_cond= ", pro_qc_result_mst e";
						$add_where_cond = " and c.barcode_no=e.barcode_no and e.roll_status=$cbo_roll_status and e.entry_form is null ";
					}
					if($cbo_date_range_type==2)
					{
						$sql_qc_dtls_date_wise="SELECT d.barcode_no 
						from pro_qc_result_mst d,pro_qc_result_dtls e  
						where d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0 $qc_date_cond order by d.barcode_no";


						$sql_qc_dtls_date=sql_select($sql_qc_dtls_date_wise); 
						foreach($sql_qc_dtls_date as $row)
						{
							$pro_barCode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
						}
						// $qcBarcodeNos= array_chunk($pro_barCode_arr, 999);
						/*$qcBarcodeNos_cond=" and(";
						foreach($qcBarcodeNos as $qcBarcodeNoss)
						{
							if($qcBarcodeNos_cond==" and(") $qcBarcodeNos_cond.=" c.barcode_no in(". implode(',', $qcBarcodeNoss).")"; else $qcBarcodeNos_cond.="  or c.barcode_no in(". implode(',', $qcBarcodeNoss).")";
						}
						$qcBarcodeNos_cond.=")";*/

						if(!empty($pro_barCode_arr))
						{
							foreach($pro_barCode_arr as $barcodeno)
							{
								execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$barcodeno,174)");
							}
							oci_commit($con);
						}

						$sql_grey_prod_entry="SELECT a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group, b.id as pro_dtls_id,b.machine_no_id,b.no_of_roll,b.shift_name,b.yarn_lot,b.yarn_count,b.color_id,b.color_range_id,b.machine_dia,b.stitch_length,b.machine_gg,b.gsm,c.po_breakdown_id as order_id,b.prod_id,b.width,b.operator_name,b.febric_description_id,b.rate,b.yarn_prod_id, c.barcode_no, c.id as roll_id, c.roll_no, sum(c.qnty) as roll_weight, sum(c.qnty) as qc_pass_qnty
						from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_barcode_no g $add_table_cond
						where  a.id=b.mst_id and b.id=c.dtls_id and c.barcode_no = g.barcode_no and g.userid=$user_id and g.entry_form=174 and a.entry_form in(2) and c.entry_form in(2) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $knit_comp_cond $company_name_cond $del_floor_cond $job_no_cond $hide_order_cond $knitting_source_cond $year_cond $del_location_cond $buyer_name_cond $str_cond_date  $barcode_cond $booking_cond $program_cond $add_where_cond
						group by a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group, b.id,b.machine_no_id,b.no_of_roll,b.shift_name,b.yarn_lot,b.yarn_count,b.color_id,b.color_range_id,b.machine_dia, b.stitch_length,b.machine_gg,b.gsm,c.po_breakdown_id,b.prod_id,b.width,b.operator_name,b.febric_description_id,b.rate,b.yarn_prod_id, c.barcode_no, c.id, c.roll_no order by c.barcode_no";
					}
					else
					{
						$sql_grey_prod_entry="SELECT a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group, b.id as pro_dtls_id,b.machine_no_id,b.no_of_roll,b.shift_name,b.yarn_lot,b.yarn_count,b.color_id,b.color_range_id,b.machine_dia,b.stitch_length,b.machine_gg,b.gsm,c.po_breakdown_id as order_id,b.prod_id,b.width,b.operator_name,b.febric_description_id,b.rate,b.yarn_prod_id, c.barcode_no, c.id as roll_id, c.roll_no, sum(c.qnty) as roll_weight, sum(c.qnty) as qc_pass_qnty
						from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c $add_table_cond
						where  a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $knit_comp_cond $company_name_cond $del_floor_cond $job_no_cond $hide_order_cond $knitting_source_cond $year_cond $del_location_cond $buyer_name_cond $str_cond_date  $barcode_cond $booking_cond $program_cond $add_where_cond
						group by a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group, b.id,b.machine_no_id,b.no_of_roll,b.shift_name,b.yarn_lot,b.yarn_count,b.color_id,b.color_range_id,b.machine_dia, b.stitch_length,b.machine_gg,b.gsm,c.po_breakdown_id,b.prod_id,b.width,b.operator_name,b.febric_description_id,b.rate,b.yarn_prod_id, c.barcode_no, c.id, c.roll_no order by c.barcode_no";
					}

					
					// echo $sql_grey_prod_entry;die;
					$sql_qry_prod_entry=sql_select($sql_grey_prod_entry);					
					
					$i=1;
					foreach($sql_qry_prod_entry as $row)
					{
						$pro_dtls_id_arr[$row[csf("pro_dtls_id")]]=$row[csf("pro_dtls_id")];
						$order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
						/*if ($row[csf("receive_basis")]==2) 
						{
							$booking_no = return_field_value("booking_no as booking_no", " ppl_planning_entry_plan_dtls ", "dtls_id='" . $row[csf("booking_id")] . "' group by booking_no", "booking_no");
						}
						else
						{							
							$booking_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
						}*/
						$booking_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
						if(trim($row[csf("color_id")])){
						$color_id_arr[$row[csf("color_id")]]=chop($row[csf("color_id")],",");
						}
						$prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
						$febric_description_id_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
					}

					$all_po_ids= array_chunk($order_id_arr, 999);
					$all_booking_ids= array_chunk($booking_id_arr, 999);
					$pro_dtls_ids= array_chunk($pro_dtls_id_arr, 999);
					$color_Ids= array_chunk($color_id_arr, 800);
					$all_prod_ids= array_chunk($prod_id_arr, 999);
					$all_febric_description_ids= array_chunk($febric_description_id_arr, 999);

					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 174, 1,$order_id_arr, $empty_arr);
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 174, 2,$booking_id_arr, $empty_arr);
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 174, 3,$pro_dtls_id_arr, $empty_arr);
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 174, 4,$color_id_arr, $empty_arr);
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 174, 5,$prod_id_arr, $empty_arr);
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 174, 6,$febric_description_id_arr, $empty_arr);

					/*$color_Ids_cond=" and(";
					foreach($color_Ids as $colorIds)
					{
						if($color_Ids_cond==" and(") $color_Ids_cond.=" id in(". implode(',', $colorIds).")"; else $color_Ids_cond.="  or id in(". implode(',', $colorIds).")";
					}
					$color_Ids_cond.=")";

					$all_prod_ids_cond=" and(";
					foreach($all_prod_ids as $prod_ids)
					{
						if($all_prod_ids_cond==" and(") $all_prod_ids_cond.=" a.id in(". implode(',', $prod_ids).")"; else $all_prod_ids_cond.="  or a.id in(". implode(',', $prod_ids).")";
					}
					$all_prod_ids_cond.=")";

					$all_febric_description_cond=" and(";
					foreach($all_prod_ids as $description_ids)
					{
						if($all_febric_description_cond==" and(") $all_febric_description_cond.=" a.id in(". implode(',', $description_ids).")"; else $all_febric_description_cond.="  or a.id in(". implode(',', $description_ids).")";
					}
					$all_febric_description_cond.=")";


					$po_ids_cond=" and("; $order_ids_cond=" and("; $po_breakdown_ids_cond=" and("; 
					foreach($all_po_ids as $po_ids)
					{
						if($po_ids_cond==" and(") $po_ids_cond.=" a.id in(". implode(',', $po_ids).")"; else $po_ids_cond.="  or a.id in(". implode(',', $po_ids).")";


						if($order_ids_cond==" and(") $order_ids_cond.=" b.order_id in(". implode(',', $po_ids).")"; else $order_ids_cond.="  or b.order_id in(". implode(',', $po_ids).")";

						if($po_breakdown_ids_cond==" and(") $po_breakdown_ids_cond.=" b.po_break_down_id in(". implode(',', $po_ids).")"; else $po_breakdown_ids_cond.="  or b.po_break_down_id in(". implode(',', $po_ids).")";
					}
					$po_ids_cond.=")";
					$order_ids_cond.=")";
					$po_breakdown_ids_cond.=")";

					$booking_ids_cond=" and(";$booking_ids_cond_2=" and(";
					foreach($all_booking_ids as $booking_ids)
					{
						if($booking_ids_cond==" and(") $booking_ids_cond.=" a.id in(". implode(',', $booking_ids).")"; else $booking_ids_cond.="  or a.id in(". implode(',', $booking_ids).")";
						if($booking_ids_cond_2==" and(") $booking_ids_cond_2.=" a.dtls_id in(". implode(',', $booking_ids).")"; else $booking_ids_cond_2.="  or a.dtls_id in(". implode(',', $booking_ids).")";
					}
					$booking_ids_cond.=")";
					$booking_ids_cond_2.=")";

					$pro_dtls_ids_cond=" and(";
					foreach($pro_dtls_ids as $pro_dtls_ids)
					{
						if($pro_dtls_ids_cond==" and(") $pro_dtls_ids_cond.=" d.pro_dtls_id in(". implode(',', $pro_dtls_ids).")"; else $pro_dtls_ids_cond.="  or d.pro_dtls_id in(". implode(',', $pro_dtls_ids).")";
					}
					$pro_dtls_ids_cond.=")";*/

					//echo $pro_dtls_ids_cond;die;
					if(!empty($color_Ids)){
						$color_arr = return_library_array("SELECT a.id, a.color_name from GBL_TEMP_ENGINE g, lib_color a where g.ref_val=a.id and g.user_id=$user_id and g.entry_form=174 and g.ref_from=4 and a.status_active=1", 'id', 'color_name');// $color_Ids_cond
					}
					if(!empty($all_prod_ids)){
						$fabric_desc_arr = return_library_array("SELECT a.id, a.item_description 
						from  GBL_TEMP_ENGINE g, product_details_master a 
						where g.ref_val=a.id and g.user_id=$user_id and g.entry_form=174 and g.ref_from=5 and a.item_category_id=13", "id", "item_description");// $all_prod_ids_cond
					}

					$sql_pro_grey_prod_del = sql_select("SELECT a.id, a.sys_number, a.delevery_date,b.product_id,b.order_id,b.barcode_num 
					from GBL_TEMP_ENGINE g, pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b
					where g.ref_val=b.order_id and g.user_id=$user_id and g.entry_form=174 and g.ref_from=1 and a.id=b.mst_id and a.entry_form=56 and a.status_active=1 and a.is_deleted=0 $knit_comp_cond $knitting_source_cond $buyer_name_cond $del_location_cond order by id");// $order_ids_cond
					$pro_grey_arr_data=array();
					foreach($sql_pro_grey_prod_del as $row_prod)
					{
						$pro_grey_arr_data[$row_prod[csf("order_id")]][$row_prod[csf("product_id")]][$row_prod[csf("barcode_num")]]["sys_number"]=$row_prod[csf("sys_number")];
						$pro_grey_arr_data[$row_prod[csf("order_id")]][$row_prod[csf("product_id")]][$row_prod[csf("barcode_num")]]["delevery_date"]=$row_prod[csf("delevery_date")];
					}
					

					$composition_arr = array();
					$composition_arr_new = array();
					if(!empty($all_prod_ids)){
						$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					}
					$data_array = sql_select($sql_deter);
					if (count($data_array) > 0) {
						foreach ($data_array as $row) {
							if (array_key_exists($row[csf('id')], $composition_arr)) {
								$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
								$composition_arr_new[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
	
							} else {
								//$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
								$composition_arr[$row[csf('id')]] = $row[csf('construction')];
								$composition_arr_new[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
	
							}
						}
					}
					if(!empty($all_prod_ids)){
						$sql_supplier = sql_select("select a.id as product_id,a.supplier_id,a.lot,a.yarn_type,a.yarn_comp_type1st,a.yarn_comp_percent1st,yarn_count_id from product_details_master a where item_category_id=1 $company_name_cond ");
					}
					//$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%";
					$supplier_arr_data=array();
					$yarn_composition_arr_data=array();
					foreach($sql_supplier as $row_spl)
					{
						$supplier_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["supplier_id"]=$row_spl[csf("supplier_id")];
						$supplier_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["yarn_type"]=$row_spl[csf("yarn_type")];
	
						$yarn_composition_arr_data[$row_spl[csf("yarn_count_id")]][$row_spl[csf("product_id")]]["yarn_conmposition"]=$composition[$row_spl[csf('yarn_comp_type1st')]] . " " . $row_spl[csf('yarn_comp_percent1st')] . "%";
	
						$yarn_composition_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["yarn_conmposition_lot"]=$composition[$row_spl[csf('yarn_comp_type1st')]] . " " . $row_spl[csf('yarn_comp_percent1st')] . "%";
					}
					//print_r($yarn_composition_arr_data);die;
					

					$style_ref_arr=array();
					if(!empty($all_po_ids)){
						$style_ref_query = "SELECT a.id,b.style_ref_no,a.job_no_mst,a.po_number 
						from GBL_TEMP_ENGINE g, wo_po_break_down a, wo_po_details_master b 
						where g.ref_val=a.id and g.user_id=$user_id and g.entry_form=174 and g.ref_from=1 and a.job_id=b.id and a.is_deleted=0 and a.status_active=1";//$po_ids_cond
					}
					$sql_style_ref=sql_select($style_ref_query);
					foreach($sql_style_ref as $rows)
					{
						$style_ref_arr[$rows[csf("id")]]["style_ref_no"]=$rows[csf("style_ref_no")];
						$job_no_arr[$rows[csf("id")]]["job_no_mst"]=$rows[csf("job_no_mst")];
						$po_number_arr[$rows[csf("id")]]=$rows[csf("po_number")];
					}

					$act_dia_gsm_arr=array();
					if(!empty($all_po_ids)){
						$act_dia_gsm_query = "SELECT a.gsm_weight,b.dia_width,b.po_break_down_id 
						from GBL_TEMP_ENGINE g, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a  
						where g.ref_val=b.po_break_down_id and g.user_id=$user_id and g.entry_form=174 and g.ref_from=1 and b.pre_cost_fabric_cost_dtls_id = a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.gsm_weight,b.dia_width,b.po_break_down_id";// $po_breakdown_ids_cond
					}
					//echo $act_dia_gsm_query;
					$act_dia_gsm_result=sql_select($act_dia_gsm_query);
					foreach($act_dia_gsm_result as $rows)
					{
						$act_dia_gsm_arr[$rows[csf("po_break_down_id")]]["req_dia"]=$rows[csf("dia_width")];
						$act_dia_gsm_arr[$rows[csf("po_break_down_id")]]["req_gsm"]=$rows[csf("gsm_weight")];
					}
					//var_dump($act_dia_gsm_arr);
			
					/*$sql_po_number_query = "select a.id, a.po_number from wo_po_break_down a where a.status_active=1 $po_ids_cond";//die;
					$po_number_arr_result=sql_select($sql_po_number_query);
					foreach($po_number_arr_result as $rows)
					{
						$po_number_arr[$rows[csf("id")]]=$rows[csf("po_number")];
						//$po_number_arr[$rows[csf("id")]]["job_no_mst"]=$rows[csf("job_no_mst")];
						//$booking_type_arr[$rows[csf("booking_no")]]=$rows[csf("booking_type")];
					}
					*/
					// mark
					if(!empty($all_booking_ids))
					{ // without program
						$sql_booking_query = "SELECT a.booking_type, a.booking_no, a.is_short 
						from GBL_TEMP_ENGINE g, wo_booking_mst a 
						where g.ref_val=a.id and g.user_id=$user_id and g.entry_form=174 and g.ref_from=2 and status_active=1";//die; $booking_ids_cond
					}
					$booking_type_result=sql_select($sql_booking_query);
					foreach($booking_type_result as $rows) 
					{
						$booking_type_arr[$rows[csf("booking_no")]]=$rows[csf("booking_type")];
						$booking_is_short_arr[$rows[csf("booking_no")]]=$rows[csf("is_short")];
					}
					if(!empty($all_booking_ids)){ // for program
						$prog_booking_id_sql="SELECT b.id, a.booking_no, b.booking_type, b.is_short 
						from GBL_TEMP_ENGINE g, ppl_planning_entry_plan_dtls a, wo_booking_mst b 
						where g.ref_val=a.dtls_id and g.user_id=$user_id and g.entry_form=174 and g.ref_from=2 and a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 
						group by b.id, a.booking_no, b.booking_type, b.is_short";// $booking_ids_cond_2
					}
					$prog_booking_type_result=sql_select($prog_booking_id_sql);
					foreach($prog_booking_type_result as $row)
					{
						$prog_booking_type_arr[$row[csf("booking_no")]]=$row[csf("booking_type")];
						$prog_booking_is_short_arr[$row[csf("booking_no")]]=$row[csf("is_short")];
					}
					// echo "<pre>"; print_r($prog_booking_type_arr);die;

					//$non_order_style_id = return_field_value("b.style_id as style_id", "wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a", "a.booking_no=b.booking_no  $booking_ids_cond","style_id");				

					if(!empty($all_booking_ids)){
						$non_order_style_id = return_library_array("SELECT a.id,b.style_id 
						from GBL_TEMP_ENGINE g, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
						where g.ref_val=a.id and g.user_id=$user_id and g.entry_form=174 and g.ref_from=2 and a.status_active=1 and a.id=b.booking_mst_id", 'id', 'style_id');// $booking_ids_cond
					}

					//$booking_no = return_field_value("booking_no as booking_no", " ppl_planning_entry_plan_dtls ", "dtls_id='" . $row[csf("booking_id")] . "' group by booking_no", "booking_no");
					if(!empty($all_booking_ids)){
						$booking_no_disp_arr = return_library_array("SELECT a.dtls_id,a.booking_no from GBL_TEMP_ENGINE g, ppl_planning_entry_plan_dtls a 
						where g.ref_val=a.dtls_id and g.user_id=$user_id and g.entry_form=174 and g.ref_from=2 and a.status_active=1 ", 'dtls_id', 'booking_no');// $booking_ids_cond_2
					}

					// echo "<pre>"; print_r($booking_is_short_arr);die;
					//a.recv_number='D n C-GPE-17-00549' and 
					$sql_qc_dtls="SELECT d.id, d.pro_dtls_id, d.roll_maintain, d.barcode_no, d.roll_id, d.roll_no, d.qc_name, d.roll_status, d.roll_width, d.roll_weight, d.roll_length, d.reject_qnty, d.qc_date, d.total_penalty_point, d.total_point,d.fabric_grade, d.comments,e.defect_name,e.defect_count,e.found_in_inch,e.penalty_point,
					case when e.defect_name ='1'  then e.defect_count else 0 end as hole_defect_count,
					case when e.defect_name ='5'  then e.defect_count else 0 end as loop_defect_count ,
					case when e.defect_name ='10'  then e.defect_count else 0 end as press_defect_count ,
					case when e.defect_name ='15'  then e.defect_count else 0 end as lycraout_defect_count ,
					case when e.defect_name ='20'  then e.defect_count else 0 end as lycradrop_defect_count ,
					case when e.defect_name ='25'  then e.defect_count else 0 end as dust_defect_count ,
					case when e.defect_name ='30'  then e.defect_count else 0 end as oilspot_defect_count ,
					case when e.defect_name ='35'  then e.defect_count else 0 end as flyconta_defect_count ,
					case when e.defect_name ='40'  then e.defect_count else 0 end as slub_defect_count ,
					case when e.defect_name ='45'  then e.defect_count else 0 end as patta_defect_count ,
					case when e.defect_name ='50'  then e.defect_count else 0 end as neddle_defect_count ,
					case when e.defect_name ='55'  then e.defect_count else 0 end as sinker_defect_count ,
					case when e.defect_name ='60'  then e.defect_count else 0 end as wheel_defect_count ,
					case when e.defect_name ='65'  then e.defect_count else 0 end as count_defect_count ,
					case when e.defect_name ='70'  then e.defect_count else 0 end as yarn_defect_count ,
					case when e.defect_name ='75'  then e.defect_count else 0 end as neps_defect_count ,
					case when e.defect_name ='80'  then e.defect_count else 0 end as black_defect_count ,
					case when e.defect_name ='85'  then e.defect_count else 0 end as oilink_defect_count ,
					case when e.defect_name ='90'  then e.defect_count else 0 end as setup_defect_count,
					case when e.defect_name ='95'  then e.defect_count else 0 end as pin_hole_defect_count,
					case when e.defect_name ='100'  then e.defect_count else 0 end as slub_hole_defect_count,
					case when e.defect_name ='105'  then e.defect_count else 0 end as needle_mark_defect_count,
					case when e.defect_name ='110'  then e.defect_count else 0 end as miss_yarn_defect_count,
					case when e.defect_name ='168'  then e.defect_count else 0 end as cont_mark_defect_count ,
					case when e.defect_name ='169'  then e.defect_count else 0 end as thin_mark_defect_count 
					from GBL_TEMP_ENGINE g, pro_qc_result_mst d,pro_qc_result_dtls e  
					where g.ref_val=d.pro_dtls_id and g.user_id=$user_id and g.entry_form=174 and g.ref_from=3 and d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0 order by d.barcode_no";//$pro_dtls_ids_cond
					//d.pro_dtls_id=24893 and 
					//echo $sql_qc_dtls;//die; 

					$sql_qc_dtls_data=sql_select($sql_qc_dtls); 
					//print_r($sql_qc_dtls_data);die("with jj");
					$roll_status_arr = array(1 => 'QC Pass', 2 => 'Held Up', 3 => 'Reject');
					$sql_qc_data_arr=array(); 
					foreach($sql_qc_dtls_data as $dataRow)
					{
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["hole_defect_count"]+=$dataRow[csf("hole_defect_count")];
						//print_r($sql_qc_data_arr);die("with sumon");
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["loop_defect_count"]+=$dataRow[csf("loop_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["press_defect_count"]+=$dataRow[csf("press_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["lycraout_defect_count"]+=$dataRow[csf("lycraout_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["lycradrop_defect_count"]+=$dataRow[csf("lycradrop_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["dust_defect_count"]+=$dataRow[csf("dust_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["oilspot_defect_count"]+=$dataRow[csf("oilspot_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["flyconta_defect_count"]+=$dataRow[csf("flyconta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["slub_defect_count"]+=$dataRow[csf("slub_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["patta_defect_count"]+=$dataRow[csf("patta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["neddle_defect_count"]+=$dataRow[csf("neddle_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["sinker_defect_count"]+=$dataRow[csf("sinker_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["wheel_defect_count"]+=$dataRow[csf("wheel_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["count_defect_count"]+=$dataRow[csf("count_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["yarn_defect_count"]+=$dataRow[csf("yarn_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["neps_defect_count"]+=$dataRow[csf("neps_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["black_defect_count"]+=$dataRow[csf("black_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["oilink_defect_count"]+=$dataRow[csf("oilink_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["setup_defect_count"]+=$dataRow[csf("setup_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["pin_hole_defect_count"]+=$dataRow[csf("pin_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["slub_hole_defect_count"]+=$dataRow[csf("slub_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["needle_mark_defect_count"]+=$dataRow[csf("needle_mark_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["cont_mark_defect_count"]+=$dataRow[csf("cont_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["thin_mark_defect_count"]+=$dataRow[csf("thin_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["miss_yarn_defect_count"]+=$dataRow[csf("miss_yarn_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["comments"]=$dataRow[csf("comments")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["reject_qnty"]=$dataRow[csf("reject_qnty")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["fabric_grade"]=$dataRow[csf("fabric_grade")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["roll_length"]=$dataRow[csf("roll_length")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["total_point"]=$dataRow[csf("total_point")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["tr_color"]=$dataRow[csf("barcode_no")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["act_dia"]=$dataRow[csf("roll_width")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["roll_status"]=$roll_status_arr[$dataRow[csf("roll_status")]];

						$pro_dtls_id_arr[$dataRow[csf("pro_dtls_id")]]=$dataRow[csf("pro_dtls_id")];
					}

					$pro_dtls_ids= array_chunk($pro_dtls_id_arr, 999);

					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 174, 4,$pro_dtls_id_arr, $empty_arr);

					/*$pro_dtls_ids_cond=" and(";
					foreach($pro_dtls_ids as $pro_dtls_ids)
					{
						if($pro_dtls_ids_cond==" and(") $pro_dtls_ids_cond.=" id in(". implode(',', $pro_dtls_ids).")"; else $pro_dtls_ids_cond.="  or id in(". implode(',', $pro_dtls_ids).")";
					}
					$pro_dtls_ids_cond.=")";*/
					
					$gsm_data_array = sql_select("SELECT a.id as ID, a.gsm as a.GSM 
					FROM GBL_TEMP_ENGINE g, pro_grey_prod_entry_dtls a 
					WHERE g.ref_val=a.id and g.user_id=$user_id and g.entry_form=174 and g.ref_from=4 and a.status_active=1 and a.is_deleted=0");// $pro_dtls_ids_cond

					$act_gsm=array();
					foreach ($gsm_data_array as $row) 
					{
						$act_gsm[$row["ID"]]["gsm"]=$row["GSM"];
					}

					$i=1;
					foreach($sql_qry_prod_entry as $row)
					{
						//echo $bgcolor;die("sumon");	break;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "")
						$fabric_desc = $fabric_desc_arr[$row[csf('prod_id')]];
						else
						$fabric_desc = $composition_arr[$row[csf('febric_description_id')]];
						$booking_type_string='';$is_short=0;$booking_type_id=0;
						if ($row[csf("receive_basis")] == 1 && $row[csf("booking_without_order")] != 1) 
						{
							$booking_no = $row[csf("booking_no")] ;
							$booking_type_id=$booking_type_arr[$booking_no];
							if($booking_type_id==1)
							{
								$is_short=$booking_is_short_arr[$booking_no];
								if($is_short==1)  $booking_type_string="Short Fabric";
								else if($is_short==2)  $booking_type_string="Main Fabric";
							}
							
							else if($booking_type_id==4) $booking_type_string="Sample Booking";
							//echo "set_auto_complete(2);\n";
						}
						else if ($row[csf("receive_basis")] == 1 && $row[csf("booking_without_order")] == 1) 
						{
							$booking_type_string="Sample Without Order";
							$booking_no = $row[csf("booking_no")] ;
						}
						else if ($row[csf("receive_basis")] == 2) 
						{
							//$booking_no = return_field_value("booking_no as booking_no", " ppl_planning_entry_plan_dtls ", "dtls_id='" . $row[csf("booking_id")] . "' group by booking_no", "booking_no");

							$booking_no=$booking_no_disp_arr[$row[csf("booking_id")]];
							$booking_type_id=$prog_booking_type_arr[$booking_no];
							if($booking_type_id==1)
							{
								$is_short=$prog_booking_is_short_arr[$booking_no];
								if($is_short==1)  $booking_type_string="Short Fabric";
								else if($is_short==2)  $booking_type_string="Main Fabric";
							}
							else if($booking_type_id==4) $booking_type_string="Sample Booking";
							else $booking_type_string="Sample Without Order";
							
							//echo "set_auto_complete(2);\n";
						} 
						else {
							//echo "set_auto_complete(1);\n";
							$booking_no = '';
						}
		
						if($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["tr_color"]!="")
						{
							$bgcolor='#3CB371';
						}
							$lot_arr = array_unique(explode(",", $row[csf("yarn_lot")]));
							$yarn_prod_arr = explode(',',$row[csf("yarn_prod_id")]);
						?>
						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
							<td width="40"><p><? echo $i; ?></p></td>
							<td width="70" align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
							<td width="110" align="center"><p><? echo $knitting_source[$row[csf("knitting_source")]]; ?></p></td> 
							<td width="150" align="center" ><p><? 
							if($row[csf("knitting_source")]==1){echo $company_library[$row[csf("knitting_company")]]; }else{ echo $knitting_company_library[$row[csf("knitting_company")]]; }
							?></p></td>
							<td width="100"><p><? echo $row[csf("recv_number")]; ?></p></td>
							<td width="100" align="center"><p><? echo $row[csf("barcode_no")]//$row[csf("challan_no")]; ?></p></td>
							<td width="60" align="center"><p><? echo $machine_arr[$row[csf("machine_no_id")]]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf("roll_no")]; ?></p></td>
							<td width="60" align="center"><p><? echo $shift_name[$row[csf("shift_name")]]; ?></p></td>
							<td width="100" align="center" class="breakAll"><p><? echo $buyer_library[$row[csf("buyer_id")]]; ?></p></td>
							<?php if($row[csf("booking_without_order")]==1)
							{
								//$non_order_style_ids = return_field_value("b.style_id as style_id", "wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a", "a.booking_no=b.booking_no and a.id='" . $row[csf("booking_id")] . "'","style_id");

								?>
								<td width="100" align="center"><p><? //echo $row[csf("booking_without_order")]; ?></p></td>
								<td width="100" align="center" ><p><? //echo $po_number_arr[$row[csf("order_id")]]; ?></p></td>
								<td width="100" align="center" class="breakAll"><p><? echo $style_library[$non_order_style_id[$row[csf("booking_id")]]]; ?></p></td>
								<?php
							}
							else
							{
								?>
								<td width="100" align="center" ><p><? echo $job_no_arr[$row[csf("order_id")]]["job_no_mst"]; ?></p></td>
								<td width="200" align="center" class="breakAll"><p><? echo $po_number_arr[$row[csf("order_id")]]; ?></p></td>
								<td width="100" align="center" class="breakAll"><p><? echo $style_ref_arr[$row[csf("order_id")]]["style_ref_no"]; ?></p></td>
								<?php 
							}
							?>
							
							<td width="120" align="center" title="receive_basis: <? echo $row[csf("receive_basis")]; ?>"><p><? echo $booking_type_string; ?></p></td>
							<td width="100" align="center"><p><? echo $booking_no; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf("roll_weight")]; ?></p></td>
							<td width="100" align="center" class="breakAll"><p><? 
							$yarnCount="";
							$yarnCountID= array_unique(explode(",", $row[csf("yarn_count")]));
							foreach ($yarnCountID as $yarnCountID) 
							{
								$yarnCount .= $yarn_count_lib_arr[$yarnCountID]. ",";
							}
							echo chop($yarnCount,",");
							
							//echo $yarn_count_lib_arr[$row[csf("yarn_count")]]; 
							
							?></p></td>
							<td width="100" align="center" class="breakAll"><p><? 
							$colorName="";
							$colorID_arr = array_unique(explode(",", $row[csf("color_id")]));
							foreach ($colorID_arr as $colID) 
							{
								$colorName .= $color_arr[$colID] . ",";
							}
							echo chop($colorName,",");
							//echo $row[csf("color_id")];//echo $color_arr[$row[csf("color_id")]]; 
							
							
							?></p></td>
							<td width="100" align="center" class="breakAll"><p><? 
							$yarnTypeName="";
							
							foreach ($lot_arr as $lotNo) 
							{
								foreach($yarn_prod_arr as $yearProdId)
								{
									if($yarn_type[$supplier_arr_data[$lotNo][$yearProdId]["yarn_type"]]!="")
									{
										if($yarnTypeName!="")
										{
											$yarnTypeName .= ",".$yarn_type[$supplier_arr_data[$lotNo][$yearProdId]["yarn_type"]];
										}else{
											$yarnTypeName = $yarn_type[$supplier_arr_data[$lotNo][$yearProdId]["yarn_type"]];
										}
									}
								}
							}
							echo $yarnTypeName;
							
							?></p> 
							</td>
							<td width="100" class="breakAll"><p><? 
							$supplierName="";
							
							foreach ($lot_arr as $lotNo) 
							{
								foreach($yarn_prod_arr as $yearProdId)
								{
									if($supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]]!="")
									{
										if($supplierName!="")
										{
											$supplierName .= ",".$supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]];
										}else{
											$supplierName = $supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]];
										}
									}
								}
							}
							echo $supplierName;
							?></p></td>
							<td width="100" align="center" class="breakAll"><p><?
								$yarnLot="";
								
								foreach ($lot_arr as $yarnLotID) {
									$yarnLot .= $yarnLotID. ", ";
								}
									echo chop($yarnLot,', '); 
								?></p></td>
							<td width="100" align="center" class="breakAll"><p><? 
							$yarnCountName="";

							foreach ($lot_arr as $lotNo) 
							{
								
								foreach($yarn_prod_arr as $yearProdId)
								{
									if($yarn_composition_arr_data[$lotNo][$yearProdId]["yarn_conmposition_lot"]!="")
									{
										if($yarnCountName!="")
										{
											$yarnCountName .=",". $yarn_composition_arr_data[$lotNo][$yearProdId]["yarn_conmposition_lot"];
											
										}else {
											$yarnCountName = $yarn_composition_arr_data[$lotNo][$yearProdId]["yarn_conmposition_lot"];
										}
									}
									
								}
								
							}
							echo $yarnCountName;
							
							?></p></td>
							<td width="100" align="center"><p><? echo $row[csf("machine_dia")]; ?></p></td>
							<td width="100" align="center"><p><? echo $req_dia = $act_dia_gsm_arr[$row[csf("order_id")]]["req_dia"]; ?></p></td>
							<td width="100" align="center"><p><? echo $act_dia = $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["act_dia"]; ?></p></td>
							<td width="100" align="center"><p><? echo ($req_dia-$act_dia); ?></p></td>
							<td width="100" align="center"><p><? echo $row[csf("stitch_length")]; ?></p></td>
							<td width="100" align="center"><p><? echo $row[csf("machine_gg")]; ?></p></td>
							<td width="100" align="center" class="breakAll"><p><? echo $fabric_desc; ?></p></td>
							<td width="100" align="center"><p><? echo $req_gsm = $act_dia_gsm_arr[$row[csf("order_id")]]["req_gsm"]; ?></p></td>
							<td width="100" align="center"><p><? echo $act_gsm = $act_gsm[$row[csf("pro_dtls_id")]]["gsm"]; ?></p></td>
							<td width="100" align="center"><p><? echo ($req_gsm-$act_gsm); ?></p></td>

							<td width="70" align="right"><p><? if ($row[csf("qc_pass_qnty")]>0) echo $row[csf("qc_pass_qnty")]-$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["reject_qnty"]; else echo 0; ?></p></td>

							<td width="70" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_status"];; ?></p></td>
							<td width="100" align="center"><p><? echo $row[csf("rate")]; ?></p></td>
							<td width="100" align="right"><p><? echo $row[csf("qc_pass_qnty")]*$row[csf("rate")]." tk"; ?></p></td>
							<td width="130"><p><? echo $pro_grey_arr_data[$row[csf("order_id")]][$row[csf("prod_id")]][$row[csf("barcode_no")]]["sys_number"]; ?></p></td>
							<td width="100" align="center"><p><? echo change_date_format($pro_grey_arr_data[$row[csf("order_id")]][$row[csf("prod_id")]][$row[csf("barcode_no")]]["delevery_date"]); ?></p></td>
							
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["hole_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["loop_defect_count"]; ?></p></td>
							<td width="60" align="center" title="<? echo $row[csf("barcode_no")]; ?>"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["press_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycraout_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycradrop_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["dust_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilspot_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["flyconta_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["patta_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neddle_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["sinker_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["wheel_defect_count"]; ?></p></td>
							<td width="60" align="center" ><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["count_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["yarn_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neps_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["black_defect_count"]; ?></p></td>
							<td width="100" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilink_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["setup_defect_count"]; ?></p></td>
							
							
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["pin_hole_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_hole_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["needle_mark_defect_count"]; ?></p></td>
							
                            <td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["cont_mark_defect_count"]; ?></p></td>
                            <td width="60" align="center" ><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["thin_mark_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["miss_yarn_defect_count"]; ?></p></td>
							<td width="60" align="center"><p><? echo number_format($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"],2); ?></p></td>
						
                            

							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["fabric_grade"]; ?></p></td>
							<td width="60" align="center"><p><? $defect_percent=($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"]*36*100)/($row[csf("width")]*$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"]); echo number_format($defect_percent,2).'%'; ?></p></td>
							<td width="60" align="center"><p><? echo number_format($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"],2); ?></p></td>
							<td width="60" align="center"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["reject_qnty"]; ?></p></td>
							<td width="100"></td>
							<td width="100">&nbsp;</td>
							<td width="100"></td>
							
							<td width="100" class="breakAll"><p><? echo $operator_lib_arr[$row[csf("operator_name")]]; ?></p></td>
							<td width="100"></td>
							<td width="100" align="center"><p><? echo number_format($defect_percent*$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"],2); ?></p></td>
							<td class="breakAll"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["comments"]; ?></p></td>
						</tr>
						<?
						$total_qc_pass_qty+=$row[csf("qc_pass_qnty")]-$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["reject_qnty"];
						$total_hole_defect+=			$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["hole_defect_count"];
						
						$total_loop_defect+=			$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["loop_defect_count"];
						
						$total_press_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["press_defect_count"];
						$total_lycraout_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycraout_defect_count"];
						$total_lycradrop_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycradrop_defect_count"];
						$total_dust_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["dust_defect_count"];
						$total_oilspot_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilspot_defect_count"];
						$total_flyconta_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["flyconta_defect_count"];
						$total_slub_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_defect_count"];
						$total_patta_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["patta_defect_count"];
						$total_neddle_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neddle_defect_count"];
						$total_sinker_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["sinker_defect_count"];
						$total_wheel_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["wheel_defect_count"];
						$total_count_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["count_defect_count"];
						$total_yarn_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["yarn_defect_count"];
						$total_neps_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neps_defect_count"];
						$total_black_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["black_defect_count"];
						$total_oilink_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilink_defect_count"];
						$total_setup_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["setup_defect_count"];
						
						$total_pin_hole_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["pin_hole_defect_count"];
						$total_slub_hole_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_hole_defect_count"];
						$total_needle_mark_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["needle_mark_defect_count"];
						
						$total_cont_mark_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["cont_mark_defect_count"];
						$total_thin_mark_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["thin_mark_defect_count"];
						$total_miss_yarn_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["miss_yarn_defect_count"];
						
						
						$total_totalDefect_point+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"];
						$total_reject_qty+=				$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["reject_qnty"];
						$total_taka+=					$row[csf("qc_pass_qnty")]*$row[csf("rate")];

						$i++;
					}
					?>
					</tbody>
				   	<tfoot>
						<tr style="background-color:#CCCCCC;">
							<td colspan="32" align="right"><strong>Total</strong></td>
							<td align="right" id="td_total_qc_pass_qty"><strong><? echo $total_qc_pass_qty; ?></strong></td>
							<td align="right"><strong></strong></td>
							<td align="right"><strong></strong></td>
							<td align="right" id="td_total_taka"><strong><? echo $total_taka; ?></strong></td>
							<td align="right"><strong></strong></td>
							<td align="right"><strong></strong></td>
							<td align="right" id="td_total_hole_defect"><strong><? echo $total_hole_defect; ?></strong></td>
							
							<td align="right" id="td_total_loop_defect"><strong><? echo $total_loop_defect; ?></strong></td>
							
							<td align="right" id="td_total_press_defect_count"><strong><? echo $total_press_defect_count; ?></strong></td>
							<td align="right" id="td_total_lycraout_defect_count"><strong><? echo $total_lycraout_defect_count; ?></strong></td>
							<td align="right" id="td_total_lycradrop_defect_count"><strong><? echo $total_lycradrop_defect_count; ?></strong></td>
							<td align="right" id="td_total_dust_defect_count"><strong><? echo $total_dust_defect_count; ?></strong></td>
							<td align="right" id="td_total_oilspot_defect_count"><strong><? echo $total_oilspot_defect_count; ?></strong></td>
							<td align="right" id="td_total_flyconta_defect_count"><strong><? echo $total_flyconta_defect_count; ?></strong></td>
							<td align="right" id="td_total_slub_defect_count"><strong><? echo $total_slub_defect_count; ?></strong></td>
							<td align="right" id="td_total_patta_defect_count"><strong><? echo $total_patta_defect_count; ?></strong></td>
							<td align="right" id="td_total_neddle_defect_count"><strong><? echo $total_neddle_defect_count; ?></strong></td>
							<td align="right" id="td_total_sinker_defect_count"><strong><? echo $total_sinker_defect_count; ?></strong></td>
							<td align="right" id="td_total_wheel_defect_count"><strong><? echo $total_wheel_defect_count; ?></strong></td>
							<td align="right" id="td_total_count_defect_count"><strong><? echo $total_count_defect_count; ?></strong></td>
							<td align="right" id="td_total_yarn_defect_count"><strong><? echo $total_yarn_defect_count; ?></strong></td>
							<td align="right" id="td_total_neps_defect_count"><strong><? echo $total_neps_defect_count; ?></strong></td>
							<td align="right" id="td_total_black_defect_count"><strong><? echo $total_black_defect_count; ?></strong></td>
							<td align="right" id="td_total_oilink_defect_count"><strong><? echo $total_oilink_defect_count; ?></strong></td>
							<td align="right" id="td_total_setup_defect_count"><strong><? echo $total_setup_defect_count; ?></strong></td>
							
							<td align="right" id="td_total_pin_hole_defect"><strong><? echo $total_pin_hole_defect_count; ?></strong></td>
							<td align="right" id="td_total_slub_hole_defect"><strong><? echo $total_slub_hole_defect_count; ?></strong></td>
							<td align="right" id="td_total_needle_mark_defect"><strong><? echo $total_needle_mark_defect_count; ?></strong></td>
							
                            <td align="right" id="td_total_cont_mark_defect"><strong><? echo $total_cont_mark_defect_count; ?></strong></td> 
                            <td align="right" id="td_total_thin_mark_defect"><strong><? echo $total_thin_mark_defect_count; ?></strong></td>
							<td align="right" id="td_total_miss_yarn_qty"><strong><? echo $total_miss_yarn_defect_count; ?></strong></td>
							<td align="right" id="td_total_totalDefect_point"><strong><? echo number_format($total_totalDefect_point,2); ?></strong></td>
						
							<td align="right"><strong></strong></td>
							<td align="right"><strong></strong></td>
							<td align="right"><strong></strong></td>
							<td align="right" id="td_total_reject_qty"><strong><? echo $total_reject_qty; ?></strong></td>

							<td colspan="8"></td>
						</tr>
					</tfoot>
				</table>
				</div>
			</div>
		</fieldset>
		<?
		execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form =174");
		execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=174");
		oci_commit($con);
	}
	if($report_format==2) // Formate-2 for barnali
	{

		?>
		<!-- Barnali - -->
		<fieldset style="width:4515px;">
	        <table width="1200">
	            <tr>
	                <td align="center" width="100%" colspan="12" class="form_caption" style="font-size:18px;">Daily Roll wise Knitting QC Report</td>
	            </tr>
	            <tr>
	                <td align="center" width="100%" colspan="12" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_knitting_company)]; ?></td>
	            </tr>
	            <tr>
	               <td align="center" width="100%" colspan="12" class="form_caption" style="font-size:12px;"><? echo   show_company($cbo_knitting_company,'',''); ?></td>
	            </tr>
	        </table>
				<style>
					.breakAll{
						word-break:break-all;
						word-wrap: break-word;
					}
				</style>
	          <div style="width:6065px; float:left;">
	            <table width="6060" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left;">
	                <thead>
	                	<tr height="100">
		                    <th width="50">SL</th>
							<th width="100">Date</th>
	                        <th width="100">Company Name</th>
							<th width="100">Knitting Source</th>
							<th width="100">Knitting Company</th>
							<th width="100">Production No</th>
							<th width="100">Out Bond Chalan No.</th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">MC NO#</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Roll No</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">SHIFT</div></th>
							<th width="100">BUYER</th>
	                        <th width="100">Job No</th>
							<th width="200">ORDER NO</th>
							<th width="100">Style Ref.</th>
							<th width="120">Booking Type</th>
							<th width="100">Booking No</th>
							<th width="80"  style="vertical-align:middle"><div class="rotate_90_deg">ROLL WEIGHT</div></th>
							<th width="100">YARN COUNT</th>
							<th width="100">Fabric Color</th>
							<th width="100">Yarn Type</th>
							<th width="100">SUPPLIER</th>
							<th width="100">YARN LOT</th>
	                        <th width="100">Yarn Composition</th>
							<th width="100">M/C DIA</th>
							<th width="100">Finish Dia</th>
							<th width="100">Stitch Length</th>
							<th width="100">GAUGE</th>
							
							<th width="100">FABRIC TYPE</th>
							<th width="100">REQ.GSM</th>
							<th width="100">Qc Pass Qty</th>
							<th width="100">Rate/ kg</th>
							<th width="100">Total TK</th>
							<th width="100">Delivery Challan No</th>
							<th width="100">Delivery Date</th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">HOLE</div></th>
	                        <th width="50" style="vertical-align:middle"><div class="rotate_90_deg">LOOP</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">PRESS OFF</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">LYCRA OUT</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">LYCRA DROP</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">DUST</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">OIL SPOT</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">FLY CONTA</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">SLUB</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">PATTA</div></th>
							<th width="70" style="vertical-align:middle"><div class="rotate_90_deg">NEEDLE BREAK</div></th>
							<th width="70" style="vertical-align:middle"><div class="rotate_90_deg">SINKER MARK</div></th>
							<th width="70" style="vertical-align:middle"><div class="rotate_90_deg">WHEEL FREE</div></th>
							<th width="70" style="vertical-align:middle"><div class="rotate_90_deg">COUNT MIX</div></th>
							<th width="80" style="vertical-align:middle"><div class="rotate_90_deg">YARN CONTRA</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">NEPS</div></th>
							<th width="80" style="vertical-align:middle"><div class="rotate_90_deg">BLACK SPOT</div></th>
							<th width="100" style="vertical-align:middle"><div class="rotate_90_deg">OIL/INK MARK</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">SET UP</div></th>
	                        <th width="60" style="vertical-align:middle"><div class="rotate_90_deg">PIN HOLE</div></th>
	                        <th width="60" style="vertical-align:middle"><div class="rotate_90_deg">SLUB HOLE</div></th>
	                        <th width="60" style="vertical-align:middle"><div class="rotate_90_deg">NEEDLE MARK</div></th>
                            
	                        
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">TTL POINTS</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">GRADE</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">DEFECT %</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">LENGTH YDS</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">Reject Qty</div></th>
							<th width="150" style="vertical-align:middle"><div class="rotate_90_deg">RESPONSIBLE</div></th>
							<th width="150" style="vertical-align:middle"><div class="rotate_90_deg">DESIGNATION</div></th>
							<th width="150" style="vertical-align:middle"><div class="rotate_90_deg">REASON FOR REJECTION</div></th>
							<th width="150">Operator Name</th>
							<th width="150">Supervisor</th>
							<th width="150">Defective Length</th>
							<th style="vertical-align:middle"><div class="rotate_90_deg">REMARKS</div></th>
	                    </tr>
	                </thead>
	            </table>
	            <div style="width:6065px; float:left; max-height:400px; overflow-y:scroll" id="scroll_body">
	            <table width="6047" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body" style="float:left;">

	                <?
	                //$color_arr = return_library_array("select id, color_name from lib_color where status_active=1", 'id', 'color_name');
					$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
					$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
					$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
					$yarn_count_lib_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
					$operator_lib_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
					
					//$fabric_desc_arr = return_library_array("select id, item_description from product_details_master where item_category_id=13", "id", "item_description");
					$non_order_style_arr = return_library_array("select id, style_ref_no from sample_development_mst where entry_form_id=203", "id", "style_ref_no");
					$non_order_color_arr = return_library_array("select id, sample_color from sample_development_mst where entry_form_id=203", "id", "style_ref_no");
					$non_ord_col = sql_select("select sample_mst_id, sample_color from sample_development_dtls where entry_form_id=203 and status_active=1");
					foreach ($non_ord_col as $val) 
					{
						if(isset($nonOrdColIdsArr[$val[csf('sample_mst_id')]]))
						{
							$nonOrdColIdsArr[$val[csf('sample_mst_id')]] .= ",".$val[csf('sample_color')];
						}
						else
						{
							$nonOrdColIdsArr[$val[csf('sample_mst_id')]] = $val[csf('sample_color')];
						}
					}
					// print_r($nonOrdColIdsArr);
	  				$knitting_company_library = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
					
					$composition_arr = array();
					$composition_arr_new = array();
					$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					$data_array = sql_select($sql_deter);
					if (count($data_array) > 0) {
						foreach ($data_array as $row) {
							if (array_key_exists($row[csf('id')], $composition_arr)) {
								$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
								$composition_arr_new[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";

							} else {
								//$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
								$composition_arr[$row[csf('id')]] = $row[csf('construction')];
								$composition_arr_new[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";

							}
						}
					}
					

					$sql_grey_prod_entry="SELECT a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group, b.id as pro_dtls_id,b.machine_no_id,b.no_of_roll,b.shift_name,b.yarn_lot,b.yarn_count,b.color_id,b.color_range_id,b.machine_dia,b.stitch_length,b.machine_gg,b.gsm,c.po_breakdown_id as order_id,b.prod_id,b.width,b.operator_name,b.febric_description_id,b.rate,b.yarn_prod_id, c.barcode_no, c.id as roll_id, c.roll_no, sum(c.qnty) as roll_weight,c.qc_pass_qnty
					from inv_receive_master a,pro_grey_prod_entry_dtls b,pro_roll_details c  
					where  a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $knit_comp_cond $company_name_cond $del_floor_cond $job_no_cond $hide_order_cond $knitting_source_cond $year_cond $del_location_cond $buyer_name_cond $str_cond_date $program_cond 
					group by a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group, b.id,b.machine_no_id,b.no_of_roll,b.shift_name,b.yarn_lot,b.yarn_count,b.color_id,b.color_range_id,b.machine_dia, b.stitch_length,b.machine_gg,b.gsm,c.po_breakdown_id,b.prod_id,b.width,b.operator_name,b.febric_description_id,b.rate,b.yarn_prod_id, c.barcode_no, c.id, c.roll_no,c.qc_pass_qnty order by c.barcode_no";
					//echo $sql_grey_prod_entry;//die;
					$sql_qry_prod_entry=sql_select($sql_grey_prod_entry);						
					
					$con = connect();
					execute_query("delete from tmp_poid where userid=".$_SESSION['logic_erp']['user_id']."");
					oci_commit($con); 
					foreach($sql_qry_prod_entry as $row)
					{
						
						$r_id1=execute_query("insert into tmp_poid (userid, poid,type) values (".$_SESSION['logic_erp']['user_id'].",".$row[csf("pro_dtls_id")].",1)");
						$r_id2=execute_query("insert into tmp_poid (userid, poid,type) values (".$_SESSION['logic_erp']['user_id'].",".$row[csf("order_id")].",2)");
						$r_id3=execute_query("insert into tmp_poid (userid, poid,type) values (".$_SESSION['logic_erp']['user_id'].",".$row[csf("booking_id")].",3)");
						$r_id4=execute_query("insert into tmp_poid (userid, poid,type) values (".$_SESSION['logic_erp']['user_id'].",".$row[csf("color_id")].",4)");
						$r_id5=execute_query("insert into tmp_poid (userid, poid,type) values (".$_SESSION['logic_erp']['user_id'].",".$row[csf("prod_id")].",5)");
					}
					oci_commit($con); 
					//disconnect($con); 
					
				
					
					$sql_booking_query = "select a.booking_type, a.booking_no, a.is_short from wo_booking_mst a,tmp_poid tmp where a.id=tmp.poid and tmp.userid=".$_SESSION['logic_erp']['user_id']." and tmp.type=3 and status_active=1 ";
					$booking_type_result=sql_select($sql_booking_query);
					foreach($booking_type_result as $rows)
					{
						$booking_type_arr[$rows[csf("booking_no")]]=$rows[csf("booking_type")];
						$booking_is_short_arr[$rows[csf("booking_no")]]=$rows[csf("is_short")];
					}
					
					$non_order_style_id = return_library_array("select a.id,b.style_id from wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a,tmp_poid tmp where  a.id=tmp.poid and tmp.userid=".$_SESSION['logic_erp']['user_id']." and tmp.type=3 and a.status_active=1 and a.booking_no=b.booking_no ", 'id', 'style_id');
					
					$booking_no_disp_arr = return_library_array("select a.dtls_id,a.booking_no from ppl_planning_entry_plan_dtls a,tmp_poid tmp where a.dtls_id=tmp.poid and tmp.userid=".$_SESSION['logic_erp']['user_id']." and tmp.type=3 and  a.status_active=1", 'dtls_id', 'booking_no');
					
					
					$sql_po_number_query = "select a.id, a.po_number from wo_po_break_down a,tmp_poid tmp where a.id=tmp.poid and tmp.userid=".$_SESSION['logic_erp']['user_id']." and tmp.type=2 and a.status_active=1";//die;
					$po_number_arr_result=sql_select($sql_po_number_query);
					foreach($po_number_arr_result as $rows)
					{
						$po_number_arr[$rows[csf("id")]]=$rows[csf("po_number")];
					}
					
					$sql_pro_grey_prod_del = sql_select("select a.id, a.sys_number, a.delevery_date,b.product_id,b.order_id,b.barcode_num from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,tmp_poid tmp where b.order_id=tmp.poid and tmp.userid=".$_SESSION['logic_erp']['user_id']." and tmp.type=2 and a.id=b.mst_id and a.entry_form=56 and a.status_active=1 and a.is_deleted=0 $knit_comp_cond $knitting_source_cond $buyer_name_cond $del_location_cond order by id");
					$pro_grey_arr_data=array();
					foreach($sql_pro_grey_prod_del as $row_prod)
					{
						$pro_grey_arr_data[$row_prod[csf("order_id")]][$row_prod[csf("product_id")]][$row_prod[csf("barcode_num")]]["sys_number"]=$row_prod[csf("sys_number")];
						$pro_grey_arr_data[$row_prod[csf("order_id")]][$row_prod[csf("product_id")]][$row_prod[csf("barcode_num")]]["delevery_date"]=$row_prod[csf("delevery_date")];
					}
					
					$style_ref_arr=array();
					$style_ref_query = "select a.id,b.style_ref_no,a.job_no_mst from wo_po_break_down a,wo_po_details_master b,tmp_poid tmp where a.id=tmp.poid and tmp.userid=".$_SESSION['logic_erp']['user_id']." and tmp.type=2 and a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1";//die;
					$sql_style_ref=sql_select($style_ref_query);
					foreach($sql_style_ref as $rows)
					{
						$style_ref_arr[$rows[csf("id")]]["style_ref_no"]=$rows[csf("style_ref_no")];
						$job_no_arr[$rows[csf("id")]]["job_no_mst"]=$rows[csf("job_no_mst")];
					}
					
					
					$sql_color_query = "select a.id, a.color_name from lib_color a,tmp_poid tmp where a.id=tmp.poid and tmp.userid=".$_SESSION['logic_erp']['user_id']." and tmp.type=4 and  a.status_active=1 ";//die;
					$color_result=sql_select($sql_color_query);
					foreach ($color_result as $row) {
						$color_arr[$row[csf("id")]]=$row[csf("color_name")];
					}


					$sql_supplier = sql_select("select a.item_category_id,a.item_description,a.id as product_id,a.supplier_id,a.lot,a.yarn_type,a.yarn_comp_type1st,a.yarn_comp_percent1st,yarn_count_id from product_details_master a where item_category_id in(1,13) $company_name_cond ");
					$supplier_arr_data=array();
					$yarn_composition_arr_data=array();
					foreach($sql_supplier as $row_spl)
					{
						if($row_spl[csf("item_category_id")]==13){
							$fabric_desc_arr[$row_spl[csf("product_id")]]=$row_spl[csf("item_description")];
						}
						else{
						$supplier_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["supplier_id"]=$row_spl[csf("supplier_id")];
						$supplier_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["yarn_type"]=$row_spl[csf("yarn_type")];
						$yarn_composition_arr_data[$row_spl[csf("yarn_count_id")]][$row_spl[csf("product_id")]]["yarn_conmposition"]=$composition[$row_spl[csf('yarn_comp_type1st')]] . " " . $row_spl[csf('yarn_comp_percent1st')] . "%";
						$yarn_composition_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["yarn_conmposition_lot"]=$composition[$row_spl[csf('yarn_comp_type1st')]] . " " . $row_spl[csf('yarn_comp_percent1st')] . "%";
						}
					}

					
		  		 	$sql_qc_dtls="SELECT d.id, d.pro_dtls_id, d.roll_maintain, d.barcode_no, d.roll_id, d.roll_no, d.qc_name, d.roll_status, d.roll_width, d.roll_weight, d.roll_length, d.reject_qnty, d.qc_date, d.total_penalty_point, d.total_point,d.fabric_grade, d.comments, e.defect_name, e.defect_count, e.found_in_inch, e.penalty_point,
					case when e.defect_name ='1'  then e.defect_count else 0 end as hole_defect_count,
					case when e.defect_name ='5'  then e.defect_count else 0 end as loop_defect_count ,
					case when e.defect_name ='10'  then e.defect_count else 0 end as press_defect_count ,
					case when e.defect_name ='15'  then e.defect_count else 0 end as lycraout_defect_count ,
					case when e.defect_name ='20'  then e.defect_count else 0 end as lycradrop_defect_count ,
					case when e.defect_name ='25'  then e.defect_count else 0 end as dust_defect_count ,
					case when e.defect_name ='30'  then e.defect_count else 0 end as oilspot_defect_count ,
					case when e.defect_name ='35'  then e.defect_count else 0 end as flyconta_defect_count ,
					case when e.defect_name ='40'  then e.defect_count else 0 end as slub_defect_count ,
					case when e.defect_name ='45'  then e.defect_count else 0 end as patta_defect_count ,
					case when e.defect_name ='50'  then e.defect_count else 0 end as neddle_defect_count ,
					case when e.defect_name ='55'  then e.defect_count else 0 end as sinker_defect_count ,
					case when e.defect_name ='60'  then e.defect_count else 0 end as wheel_defect_count ,
					case when e.defect_name ='65'  then e.defect_count else 0 end as count_defect_count ,
					case when e.defect_name ='70'  then e.defect_count else 0 end as yarn_defect_count ,
					case when e.defect_name ='75'  then e.defect_count else 0 end as neps_defect_count ,
					case when e.defect_name ='80'  then e.defect_count else 0 end as black_defect_count ,
					case when e.defect_name ='85'  then e.defect_count else 0 end as oilink_defect_count ,
					case when e.defect_name ='90'  then e.defect_count else 0 end as setup_defect_count,
					case when e.defect_name ='95'  then e.defect_count else 0 end as pin_hole_defect_count,
					case when e.defect_name ='100'  then e.defect_count else 0 end as slub_hole_defect_count,
					case when e.defect_name ='105'  then e.defect_count else 0 end as needle_mark_defect_count 
					from pro_qc_result_mst d,pro_qc_result_dtls e,tmp_poid tmp  
					where  d.pro_dtls_id=tmp.poid and tmp.userid=".$_SESSION['logic_erp']['user_id']." and tmp.type=1 and d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0 order by d.barcode_no ";
					// echo $sql_qc_dtls;die;
					$sql_qc_dtls_data=sql_select($sql_qc_dtls);
					$sql_qc_data_arr=array();
					foreach($sql_qc_dtls_data as $dataRow)
					{
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["hole_defect_count"]+=$dataRow[csf("hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["loop_defect_count"]+=$dataRow[csf("loop_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["press_defect_count"]+=$dataRow[csf("press_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["lycraout_defect_count"]+=$dataRow[csf("lycraout_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["lycradrop_defect_count"]+=$dataRow[csf("lycradrop_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["dust_defect_count"]+=$dataRow[csf("dust_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["oilspot_defect_count"]+=$dataRow[csf("oilspot_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["flyconta_defect_count"]+=$dataRow[csf("flyconta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["slub_defect_count"]+=$dataRow[csf("slub_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["patta_defect_count"]+=$dataRow[csf("patta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["neddle_defect_count"]+=$dataRow[csf("neddle_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["sinker_defect_count"]+=$dataRow[csf("sinker_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["wheel_defect_count"]+=$dataRow[csf("wheel_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["count_defect_count"]+=$dataRow[csf("count_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["yarn_defect_count"]+=$dataRow[csf("yarn_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["neps_defect_count"]+=$dataRow[csf("neps_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["black_defect_count"]+=$dataRow[csf("black_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["oilink_defect_count"]+=$dataRow[csf("oilink_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["setup_defect_count"]+=$dataRow[csf("setup_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["pin_hole_defect_count"]+=$dataRow[csf("pin_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["slub_hole_defect_count"]+=$dataRow[csf("slub_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["needle_mark_defect_count"]+=$dataRow[csf("needle_mark_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["comments"]=$dataRow[csf("comments")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["reject_qnty"]=$dataRow[csf("reject_qnty")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["fabric_grade"]=$dataRow[csf("fabric_grade")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["roll_length"]=$dataRow[csf("roll_length")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["total_point"]=$dataRow[csf("total_point")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["tr_color"]=$dataRow[csf("barcode_no")];

					
						$qc_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["tr_color"]=$dataRow[csf("barcode_no")];
					}

					execute_query("delete from tmp_poid where userid=".$_SESSION['logic_erp']['user_id']."");
					oci_commit($con);
					disconnect($con);

					$i=1;
					foreach($sql_qry_prod_entry as $row) // main loop start
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "")
						$fabric_desc = $fabric_desc_arr[$row[csf('prod_id')]];
						else
						$fabric_desc = $composition_arr[$row[csf('febric_description_id')]];
						// ========================================
						$booking_type_string='';	
						if ($row[csf("receive_basis")] == 1 && $row[csf("booking_without_order")] != 1) 
						{
							$booking_no = $row[csf("booking_no")] ;
							$booking_type_id=$booking_type_arr[$booking_no];
							if($booking_type_id==1)
							{
								$is_short=$booking_is_short_arr[$booking_no];
								if($is_short==1)  $booking_type_string="Short Fabric";
								else if($is_short==2)  $booking_type_string="Main Fabric";
							}
							
							else if($booking_type_id==4) $booking_type_string="Sample Booking";
							//echo "set_auto_complete(2);\n";
						}
						else if ($row[csf("receive_basis")] == 1 && $row[csf("booking_without_order")] == 1) 
						{
							$booking_type_string="Sample Without Order";
							$booking_no = $row[csf("booking_no")] ;
						}
						else if ($row[csf("receive_basis")] == 2) 
						{
							$booking_no=$booking_no_disp_arr[$row[csf("booking_id")]];
							if($booking_no=="") $booking_no = $row[csf("booking_no")];
							$booking_type_id=$booking_type_arr[$booking_no];
							if($booking_type_id==1)
							{
								$is_short=$booking_is_short_arr[$booking_no];
								if($is_short==1)  $booking_type_string="Short Fabric";
								else if($is_short==2)  $booking_type_string="Main Fabric";
							}
							else if($booking_type_id==4) $booking_type_string="Sample Booking";
							else $booking_type_string="Sample Without Order";
							
						} 
						else 
						{
							$booking_no = '';
						}
						//===================================	
						if ($row[csf("receive_basis")] == 1 && $row[csf("booking_without_order")] != 1) {
						} else if ($row[csf("receive_basis")] == 2) {
							if ($row[csf("within_group")] == 1) {
								$job_no = '';
							} else {
							}
						} else {
							$job_no = '';
						}
					
					
					
						if($qc_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["tr_color"]!="")
						{
							$bgcolor='#3CB371';
						}
						
						$lot_arr = array_unique(explode(",", $row[csf("yarn_lot")]));
						$yarn_prod_arr = explode(',',$row[csf("yarn_prod_id")]);
								
	                	?>
	                	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
	                    	<td width="50"><? echo $i; ?></td>
							<td width="100" align="center"><? echo $row[csf("receive_date")]; ?></td>
	                        <td width="100" align="center"><p><? echo $company_library[$row[csf("company_id")]]; ?></p></td>
							<td width="100" align="center"><? echo $knitting_source[$row[csf("knitting_source")]]; ?></td> 
							<td width="100" align="center"><p><? 
							if($row[csf("knitting_source")]==1){echo $company_library[$row[csf("knitting_company")]]; }else{ echo $knitting_company_library[$row[csf("knitting_company")]]; }
							?></p></td>
							<td width="100"><p><? echo $row[csf("recv_number")]; ?></p></td>
							<td width="100"><? echo $row[csf("challan_no")]; ?></td>
							<td width="50" align="center"><? echo $machine_arr[$row[csf("machine_no_id")]]; ?></td>
							<td width="50" align="center"><? echo $row[csf("roll_no")]; ?></td>
							<td width="50" align="center"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
							<td width="100" align="center" class="breakAll"><p><? echo $buyer_library[$row[csf("buyer_id")]]; ?></p></td>
	                        <td width="100" align="center">
		                        <? 
		                        if($row[csf("booking_without_order")] != 1)
		                        {
		                        	echo $job_no_arr[$row[csf("order_id")]]["job_no_mst"]; 
		                        }
		                        ?>	                        	
	                        </td>
							<td width="200" align="center" class="breakAll">
								<? 
									if($row[csf("booking_without_order")] != 1)
									{
										echo $po_number_arr[$row[csf("order_id")]]; 
									}
								?>								
							</td>
	                        <td width="100" align="center">
		                        <? 
		                        if($row[csf("booking_without_order")] == 1)
		                        {
		                        	echo $non_order_style_arr[$non_order_style_id[$row[csf("booking_id")]]];
		                        }
		                        else
		                        {
		                        	echo $style_ref_arr[$row[csf("order_id")]]["style_ref_no"]; 
		                        }
		                        ?>	                        	
	                        </td>
	                        <td width="120" align="center" ><? echo $booking_type_string; ?></td>
							<td width="100" align="center"><? echo $booking_no; ?></td>
							<td width="80" align="center"><? echo $row[csf("roll_weight")]; ?></td>
							<td width="100" align="center"><p><? 
							$yarnCount="";
							$yarnCountID= array_unique(explode(",", $row[csf("yarn_count")]));
							foreach ($yarnCountID as $yarnCountID) {
	                                    $yarnCount .= $yarn_count_lib_arr[$yarnCountID]. ",";
	                                }
									echo chop($yarnCount,",");
							?></p></td>
							<td width="100" align="center"><p><? 
							if($row[csf("booking_without_order")] != 1)
							{
								$colorName="";
								$colorID_arr = array_unique(explode(",", $row[csf("color_id")]));
								foreach ($colorID_arr as $colID) 
								{
	                                $colorName .= $color_arr[$colID] . ",";
	                            }
								echo chop($colorName,",");
							}
							else
							{
								$colorName="";
								$fab_col = $nonOrdColIdsArr[$non_order_style_id[$row[csf("booking_id")]]];
								$colorID_arr = explode(",", $fab_col);
								foreach ($colorID_arr as $colID) 
								{
	                                $colorName .= $color_arr[trim($colID)] . ",";
	                            }
								echo chop($colorName,",");

							}
							
							
							?></p></td>
							<td width="100" align="center"><p><? 
							$yarnTypeName="";
							
							foreach ($lot_arr as $lotNo) {
								foreach($yarn_prod_arr as $yearProdId)
								{
									if($yarn_type[$supplier_arr_data[$lotNo][$yearProdId]["yarn_type"]]!="")
									{
										if($yarnTypeName!="")
										{
											$yarnTypeName .= ",".$yarn_type[$supplier_arr_data[$lotNo][$yearProdId]["yarn_type"]];
										}else{
											$yarnTypeName = $yarn_type[$supplier_arr_data[$lotNo][$yearProdId]["yarn_type"]];
										}
									}
								}
							}
							echo $yarnTypeName;
							
							?></p></td>
							<td width="100"><p><? 
							$supplierName="";
							
							foreach ($lot_arr as $lotNo) {
								foreach($yarn_prod_arr as $yearProdId)
								{
									if($supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]]!="")
									{
										if($supplierName!="")
										{
											$supplierName .= ",".$supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]];
										}else{
											$supplierName = $supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]];
										}
									}
								}
							}
							echo $supplierName;
							?></p></td>
							<td width="100" align="center"><p><?
								$yarnLot="";
								foreach ($lot_arr as $yarnLotID) {
									$yarnLot .= $yarnLotID. ", ";
								}
								 echo chop($yarnLot,', '); 
							 ?></p></td>
	                        <td width="100" align="center"><p><? 
							$yarnCountName="";

							foreach ($lot_arr as $lotNo) {
								
								foreach($yarn_prod_arr as $yearProdId)
								{
									if($yarn_composition_arr_data[$lotNo][$yearProdId]["yarn_conmposition_lot"]!="")
									{
										if($yarnCountName!="")
										{
											$yarnCountName .=",". $yarn_composition_arr_data[$lotNo][$yearProdId]["yarn_conmposition_lot"];
											
										}else {
											$yarnCountName = $yarn_composition_arr_data[$lotNo][$yearProdId]["yarn_conmposition_lot"];
										}
									}
									
								}
								
							}
							echo $yarnCountName;
							?></p></td>
							<td width="100" align="center"><? echo $row[csf("machine_dia")]; ?></td>
							<td width="100" align="center"><? echo $row[csf("width")]; ?></td>
							<td width="100" align="center"><? echo $row[csf("stitch_length")]; ?></td>
	                        <td width="100" align="center"><? echo $row[csf("machine_gg")]; ?></td>
							<td width="100" align="center"><p><? echo $fabric_desc; ?></p></td>
							<td width="100" align="center"><? echo $row[csf("gsm")]; ?></td>
							<td width="100" align="center"><? echo $row[csf("qc_pass_qnty")]; ?></td>
							<td width="100" align="center"><? echo $row[csf("rate")]; ?></td>
							<td width="100" align="center"><? echo $row[csf("qc_pass_qnty")]*$row[csf("rate")]." tk"; ?></td>
							<td width="100"><p><? echo $pro_grey_arr_data[$row[csf("order_id")]][$row[csf("prod_id")]][$row[csf("barcode_no")]]["sys_number"]; ?></p></td>
							<td width="100" align="center"><? echo $pro_grey_arr_data[$row[csf("order_id")]][$row[csf("prod_id")]][$row[csf("barcode_no")]]["delevery_date"]; ?></td> 					
							<td width="50" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["hole_defect_count"]; ?></td>
	                        <td width="50" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["loop_defect_count"]; ?></td>
							<td width="50" align="center" title="<? echo $row[csf("barcode_no")]; ?>"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["press_defect_count"]; ?></td>
							<td width="50" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycraout_defect_count"]; ?></td>
							<td width="50" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycradrop_defect_count"]; ?></td>
							<td width="50" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["dust_defect_count"]; ?></td>
							<td width="50" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilspot_defect_count"]; ?></td>
							<td width="50" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["flyconta_defect_count"]; ?></td>
							<td width="50" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_defect_count"]; ?></td>
							<td width="50" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["patta_defect_count"]; ?></td>
							<td width="70" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neddle_defect_count"]; ?></td>
							<td width="70" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["sinker_defect_count"]; ?></td>
	                        <td width="70" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["wheel_defect_count"]; ?></td>
							<td width="70" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["count_defect_count"]; ?></td>
							<td width="80" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["yarn_defect_count"]; ?></td>
							<td width="50" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neps_defect_count"]; ?></td>
							<td width="80" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["black_defect_count"]; ?></td>
							<td width="100" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilink_defect_count"]; ?></td>
							<td width="60" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["setup_defect_count"]; ?></td>
	                        <td width="60" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["pin_hole_defect_count"]; ?></td>
	                        <td width="60" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_hole_defect_count"]; ?></td>
	                        <td width="60" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["needle_mark_defect_count"]; ?></td>
							<td width="60" align="center"><? echo number_format($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"],2); ?></td>
							<td width="60" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["fabric_grade"]; ?></td>
							<td width="60" align="center"><? $defect_percent=($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"]*36*100)/($row[csf("width")]*$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"]); echo number_format($defect_percent,2).'%'; ?></td>
							<td width="60" align="center"><? echo number_format($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"],2); ?></td>
	                        <td width="60" align="center"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["reject_qnty"]; ?></td>
							<td width="150"></td>
							<td width="150"></td>
							<td width="150"></td>
							<td width="150"><p><? echo $operator_lib_arr[$row[csf("operator_name")]]; ?></p></td>
							<td width="150"></td>
							<td width="150" align="center"><? echo number_format($defect_percent*$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"],2); ?></td>

							<td>
								<p>
									<? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["comments"]; ?>
								</p>
							</td>

	                    </tr>
	                    <?
						$total_qc_pass_qty+=$row[csf("qc_pass_qnty")];
						$total_hole_defect+=			$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["hole_defect_count"];
						$total_loop_defect+=			$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["loop_defect_count"];
						
						$total_press_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["press_defect_count"];
						$total_lycraout_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycraout_defect_count"];
						$total_lycradrop_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycradrop_defect_count"];
						$total_dust_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["dust_defect_count"];
						$total_oilspot_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilspot_defect_count"];
						$total_flyconta_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["flyconta_defect_count"];
						$total_slub_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_defect_count"];
						$total_patta_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["patta_defect_count"];
						$total_neddle_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neddle_defect_count"];
						$total_sinker_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["sinker_defect_count"];
						$total_wheel_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["wheel_defect_count"];
						$total_count_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["count_defect_count"];
						$total_yarn_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["yarn_defect_count"];
						$total_neps_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neps_defect_count"];
						$total_black_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["black_defect_count"];
						$total_oilink_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilink_defect_count"];
						$total_setup_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["setup_defect_count"];
						
						$total_pin_hole_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["pin_hole_defect_count"];
						$total_slub_hole_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_hole_defect_count"];
						$total_needle_mark_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["needle_mark_defect_count"];
						
						
						$total_totalDefect_point+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"];
						$total_reject_qty+=				$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["reject_qnty"];
						$total_taka+=					$row[csf("qc_pass_qnty")]*$row[csf("rate")];
						
						

						
						$i++;
					}
					?>
                    <tfoot>
             			<tr style="background-color:#CCCCCC;">
			              	<td colspan="29" align="right"><strong>Total</strong></td>
			                <td align="right" id="td_total_qc_pass_qty"><strong><? echo $total_qc_pass_qty; ?></strong></td>
			                <td align="right"><strong></strong></td>
			                <td align="right" id="td_total_taka"><strong><? echo $total_taka; ?></strong></td>
			                <td align="right"><strong></strong></td>
			                <td align="right"><strong></strong></td>
			                <td align="right" id="td_total_hole_defect"><strong><? echo $total_hole_defect; ?></strong></td>
                            
                            <td align="right" id="td_total_loop_defect"><strong><? echo $total_loop_defect; ?></strong></td>
                            
			                <td align="right" id="td_total_press_defect_count"><strong><? echo $total_press_defect_count; ?></strong></td>
			                <td align="right" id="td_total_lycraout_defect_count"><strong><? echo $total_lycraout_defect_count; ?></strong></td>
			                <td align="right" id="td_total_lycradrop_defect_count"><strong><? echo $total_lycradrop_defect_count; ?></strong></td>
			                <td align="right" id="td_total_dust_defect_count"><strong><? echo $total_dust_defect_count; ?></strong></td>
                            <td align="right" id="td_total_oilspot_defect_count"><strong><? echo $total_oilspot_defect_count; ?></strong></td>
			                <td align="right" id="td_total_flyconta_defect_count"><strong><? echo $total_flyconta_defect_count; ?></strong></td>
			                <td align="right" id="td_total_slub_defect_count"><strong><? echo $total_slub_defect_count; ?></strong></td>
			                <td align="right" id="td_total_patta_defect_count"><strong><? echo $total_patta_defect_count; ?></strong></td>
			                <td align="right" id="td_total_neddle_defect_count"><strong><? echo $total_neddle_defect_count; ?></strong></td>
			                <td align="right" id="td_total_sinker_defect_count"><strong><? echo $total_sinker_defect_count; ?></strong></td>
			                <td align="right" id="td_total_wheel_defect_count"><strong><? echo $total_wheel_defect_count; ?></strong></td>
			                <td align="right" id="td_total_count_defect_count"><strong><? echo $total_count_defect_count; ?></strong></td>
			                <td align="right" id="td_total_yarn_defect_count"><strong><? echo $total_yarn_defect_count; ?></strong></td>
			                <td align="right" id="td_total_neps_defect_count"><strong><? echo $total_neps_defect_count; ?></strong></td>
                            <td align="right" id="td_total_black_defect_count"><strong><? echo $total_black_defect_count; ?></strong></td>
			                <td align="right" id="td_total_oilink_defect_count"><strong><? echo $total_oilink_defect_count; ?></strong></td>
			                <td align="right" id="td_total_setup_defect_count"><strong><? echo $total_setup_defect_count; ?></strong></td>
                            
                            <td align="right" id="td_total_pin_hole_defect"><strong><? echo $total_pin_hole_defect_count; ?></strong></td>
                            <td align="right" id="td_total_slub_hole_defect"><strong><? echo $total_slub_hole_defect_count; ?></strong></td>
                            <td align="right" id="td_total_needle_mark_defect"><strong><? echo $total_needle_mark_defect_count; ?></strong></td>
                            
			                <td align="right" id="td_total_totalDefect_point"><strong><? echo number_format($total_totalDefect_point,2); ?></strong></td>
			                <td align="right"><strong></strong></td>
			                <td align="right"><strong></strong></td>
			                <td align="right"><strong></strong></td>
			                <td align="right" id="td_total_reject_qty"><strong><? echo $total_reject_qty; ?></strong></td>
			                <td colspan="7"><strong></strong></td>
                		</tr>
              		</tfoot>
	            </table>
	            </div>
	        </div>
		</fieldset>

		<?
	}
	if($report_format==3) //show 2
	{
		?>
		<fieldset style="width:3715px;">
			<table width="1200">
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption" style="font-size:18px;">Daily Roll wise Knitting QC Report</td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr>
				<tr>
				   <td align="center" width="100%" colspan="12" class="form_caption" style="font-size:12px;"><? echo   show_company($cbo_knitting_company,'',''); ?></td>
				</tr>
			</table>
			  <div style="width:3745px; float:left;">
				<table width="3740" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left;">
					<thead>
						<tr height="100">
							<th width="40">SL</th>
							<th width="70">Date</th>
							<th width="150">Knitting Company</th>
							<th width="100">Barcode</th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">MC NO#</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">SHIFT</div></th>
							<th width="100">BUYER</th>
							<th width="200">ORDER NO</th>
							<th width="100">PROGRAM NO</th>
							<th width="60" style="vertical-align:middle"><div class="block_div" >ROLL WEIGHT</div></th>
							<th width="100">YARN COUNT</th>
							<th width="100">Fabric Color</th>
							<th width="100">SUPPLIER</th>
							<th width="100">YARN LOT</th>
							<th width="100">M/C DIA</th>
							<th width="100">Finish Dia</th>
							<th width="100">Stitch Length</th>
							<th width="100">GAUGE</th>
							<th width="100">FABRIC TYPE</th>
							<th width="100">REQ.GSM</th>
							<th width="70">Qc Pass Qty</th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">HOLE</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">LOOP</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">MISS YARN</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">LYCRA OUT</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">LYCRA DROP</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">OIL SPOT</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">FLY CONTA</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">SLUB</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">NEEDLE BREAK</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">WHEEL FREE</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">COUNT MIX</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">YARN CONTRA</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">SET UP</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">PIN HOLE</div></th>
							
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">TTL POINTS</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">GRADE</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">DEFECT %</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">LENGTH YDS</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">Reject Qty</div></th>
							<th width="100">Operator Name</th>
							<th width="100">QC Name</th>
							<th style="vertical-align:middle"><div class="rotate_90_deg">REMARKS</div></th>
						</tr>
					</thead>
				</table>
				  	<style>
						.breakAll{
							word-break:break-all;
							word-wrap: break-word;
						}
					</style>
				  <div style="width:3745px; float:left; max-height:400px; overflow-y:scroll" id="scroll_body">
				  <table width="3727" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show2" style="float:left;">
					<tbody>
					<?
					$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
					$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
					$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");				
					$yarn_count_lib_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
					$operator_lib_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
					$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
					
					//$non_booking_arr=return_library_array( "select  id,booking_no  from  wo_non_ord_samp_booking_mst ", "id", "booking_no"  );
					
					$knitting_company_library = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");

					$sql_grey_prod_entry="SELECT a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group, b.id as pro_dtls_id,b.machine_no_id,b.no_of_roll,b.shift_name,b.yarn_lot,b.yarn_count,b.color_id,b.color_range_id,b.machine_dia,b.stitch_length,b.machine_gg,b.gsm,c.po_breakdown_id as order_id,b.prod_id,b.width,b.operator_name,b.febric_description_id,b.rate,b.yarn_prod_id, c.barcode_no, c.id as roll_id, c.roll_no, sum(c.qnty) as roll_weight, sum(c.qnty) as qc_pass_qnty 
					from inv_receive_master a,pro_grey_prod_entry_dtls b,pro_roll_details c
					where  a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $knit_comp_cond $company_name_cond $del_floor_cond $job_no_cond $hide_order_cond $knitting_source_cond $year_cond $del_location_cond $buyer_name_cond $str_cond_date  $barcode_cond $booking_cond $program_cond
					group by a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group, b.id,b.machine_no_id,b.no_of_roll,b.shift_name,b.yarn_lot,b.yarn_count,b.color_id,b.color_range_id,b.machine_dia, b.stitch_length,b.machine_gg,b.gsm,c.po_breakdown_id,b.prod_id,b.width,b.operator_name,b.febric_description_id,b.rate,b.yarn_prod_id, c.barcode_no, c.id, c.roll_no order by c.barcode_no";
					// echo $sql_grey_prod_entry;
					$sql_qry_prod_entry=sql_select($sql_grey_prod_entry);					
					
					$i=1;
					foreach($sql_qry_prod_entry as $row)
					{
						$pro_dtls_id_arr[$row[csf("pro_dtls_id")]]=$row[csf("pro_dtls_id")];
						$order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
						$booking_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
						if(trim($row[csf("color_id")])){
						$color_id_arr[$row[csf("color_id")]]=chop($row[csf("color_id")],",");
						}
						$prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
						$febric_description_id_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
					}

					$all_po_ids= array_chunk($order_id_arr, 999);
					$all_booking_ids= array_chunk($booking_id_arr, 999);
					$pro_dtls_ids= array_chunk($pro_dtls_id_arr, 999);
					$color_Ids= array_chunk($color_id_arr, 800);
					$all_prod_ids= array_chunk($prod_id_arr, 999);
					$all_febric_description_ids= array_chunk($febric_description_id_arr, 999);


					$color_Ids_cond=" and(";
					foreach($color_Ids as $colorIds)
					{
						if($color_Ids_cond==" and(") $color_Ids_cond.=" id in(". implode(',', $colorIds).")"; else $color_Ids_cond.="  or id in(". implode(',', $colorIds).")";
					}
					$color_Ids_cond.=")";

					$all_prod_ids_cond=" and(";
					foreach($all_prod_ids as $prod_ids)
					{
						if($all_prod_ids_cond==" and(") $all_prod_ids_cond.=" a.id in(". implode(',', $prod_ids).")"; else $all_prod_ids_cond.="  or a.id in(". implode(',', $prod_ids).")";
					}
					$all_prod_ids_cond.=")";

					$all_febric_description_cond=" and(";
					foreach($all_prod_ids as $description_ids)
					{
						if($all_febric_description_cond==" and(") $all_febric_description_cond.=" a.id in(". implode(',', $description_ids).")"; else $all_febric_description_cond.="  or a.id in(". implode(',', $description_ids).")";
					}
					$all_febric_description_cond.=")";


					$po_ids_cond=" and("; $order_ids_cond=" and(";
					foreach($all_po_ids as $po_ids)
					{
						if($po_ids_cond==" and(") $po_ids_cond.=" a.id in(". implode(',', $po_ids).")"; else $po_ids_cond.="  or a.id in(". implode(',', $po_ids).")";


						if($order_ids_cond==" and(") $order_ids_cond.=" b.order_id in(". implode(',', $po_ids).")"; else $order_ids_cond.="  or b.order_id in(". implode(',', $po_ids).")";

					}
					$po_ids_cond.=")";
					$order_ids_cond.=")";

					$booking_ids_cond=" and(";$booking_ids_cond_2=" and(";
					foreach($all_booking_ids as $booking_ids)
					{
						if($booking_ids_cond==" and(") $booking_ids_cond.=" a.id in(". implode(',', $booking_ids).")"; else $booking_ids_cond.="  or a.id in(". implode(',', $booking_ids).")";
						if($booking_ids_cond_2==" and(") $booking_ids_cond_2.=" a.dtls_id in(". implode(',', $booking_ids).")"; else $booking_ids_cond_2.="  or a.dtls_id in(". implode(',', $booking_ids).")";


					}
					$booking_ids_cond.=")";
					$booking_ids_cond_2.=")";

					$pro_dtls_ids_cond=" and(";
					foreach($pro_dtls_ids as $pro_dtls_ids)
					{
						if($pro_dtls_ids_cond==" and(") $pro_dtls_ids_cond.=" d.pro_dtls_id in(". implode(',', $pro_dtls_ids).")"; else $pro_dtls_ids_cond.="  or d.pro_dtls_id in(". implode(',', $pro_dtls_ids).")";
					}
					$pro_dtls_ids_cond.=")";

					//echo $pro_dtls_ids_cond;die;
					if(!empty($color_Ids)){
						$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 $color_Ids_cond", 'id', 'color_name');
					}
					if(!empty($all_prod_ids)){
						$fabric_desc_arr = return_library_array("select a.id, a.item_description from product_details_master a where a.item_category_id=13 $all_prod_ids_cond", "id", "item_description");
					}

					$sql_pro_grey_prod_del = sql_select("select a.id, a.sys_number, a.delevery_date,b.product_id,b.order_id,b.barcode_num from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.entry_form=56 and a.status_active=1 and a.is_deleted=0 $knit_comp_cond $knitting_source_cond $buyer_name_cond $del_location_cond $order_ids_cond order by id");
					$pro_grey_arr_data=array();
					foreach($sql_pro_grey_prod_del as $row_prod)
					{
						$pro_grey_arr_data[$row_prod[csf("order_id")]][$row_prod[csf("product_id")]][$row_prod[csf("barcode_num")]]["sys_number"]=$row_prod[csf("sys_number")];
						$pro_grey_arr_data[$row_prod[csf("order_id")]][$row_prod[csf("product_id")]][$row_prod[csf("barcode_num")]]["delevery_date"]=$row_prod[csf("delevery_date")];
					}
					

					$composition_arr = array();
					$composition_arr_new = array();
					if(!empty($all_prod_ids)){
						$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					}
					$data_array = sql_select($sql_deter);
					if (count($data_array) > 0) {
						foreach ($data_array as $row) {
							if (array_key_exists($row[csf('id')], $composition_arr)) {
								$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
								$composition_arr_new[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
	
							} else {
								//$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
								$composition_arr[$row[csf('id')]] = $row[csf('construction')];
								$composition_arr_new[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
	
							}
						}
					}
					if(!empty($all_prod_ids)){
						$sql_supplier = sql_select("select a.id as product_id,a.supplier_id,a.lot,a.yarn_type,a.yarn_comp_type1st,a.yarn_comp_percent1st,yarn_count_id from product_details_master a where item_category_id=1 $company_name_cond ");
					}
					//$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%";
					$supplier_arr_data=array();
					$yarn_composition_arr_data=array();
					foreach($sql_supplier as $row_spl)
					{
						$supplier_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["supplier_id"]=$row_spl[csf("supplier_id")];
						$supplier_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["yarn_type"]=$row_spl[csf("yarn_type")];
	
						$yarn_composition_arr_data[$row_spl[csf("yarn_count_id")]][$row_spl[csf("product_id")]]["yarn_conmposition"]=$composition[$row_spl[csf('yarn_comp_type1st')]] . " " . $row_spl[csf('yarn_comp_percent1st')] . "%";
	
						$yarn_composition_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["yarn_conmposition_lot"]=$composition[$row_spl[csf('yarn_comp_type1st')]] . " " . $row_spl[csf('yarn_comp_percent1st')] . "%";
					}
					//print_r($yarn_composition_arr_data);die;
					

					$style_ref_arr=array();
					if(!empty($all_po_ids)){
						$style_ref_query = "select a.id,b.style_ref_no,a.job_no_mst,a.po_number from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $po_ids_cond  and a.is_deleted=0 and a.status_active=1";//die;
					}
					$sql_style_ref=sql_select($style_ref_query);
					foreach($sql_style_ref as $rows)
					{
						$style_ref_arr[$rows[csf("id")]]["style_ref_no"]=$rows[csf("style_ref_no")];
						$job_no_arr[$rows[csf("id")]]["job_no_mst"]=$rows[csf("job_no_mst")];
						$po_number_arr[$rows[csf("id")]]=$rows[csf("po_number")];
					}
			
					/*$sql_po_number_query = "select a.id, a.po_number from wo_po_break_down a where a.status_active=1 $po_ids_cond";//die;
					$po_number_arr_result=sql_select($sql_po_number_query);
					foreach($po_number_arr_result as $rows)
					{
						$po_number_arr[$rows[csf("id")]]=$rows[csf("po_number")];
						//$po_number_arr[$rows[csf("id")]]["job_no_mst"]=$rows[csf("job_no_mst")];
						//$booking_type_arr[$rows[csf("booking_no")]]=$rows[csf("booking_type")];
					}
					*/
					// mark
					if(!empty($all_booking_ids)){
						$sql_booking_query = "select a.booking_type, a.booking_no, a.is_short from wo_booking_mst a where status_active=1 $booking_ids_cond ";//die;
					}
					$booking_type_result=sql_select($sql_booking_query);
					foreach($booking_type_result as $rows)
					{
						$booking_type_arr[$rows[csf("booking_no")]]=$rows[csf("booking_type")];
						$booking_is_short_arr[$rows[csf("booking_no")]]=$rows[csf("is_short")];
						
					}

					//$non_order_style_id = return_field_value("b.style_id as style_id", "wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a", "a.booking_no=b.booking_no  $booking_ids_cond","style_id");
					if(!empty($all_booking_ids)){
						$non_order_style_id = return_library_array("select a.id,b.style_id from wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a where a.status_active=1 and a.booking_no=b.booking_no  $booking_ids_cond", 'id', 'style_id');
					}

					//$booking_no = return_field_value("booking_no as booking_no", " ppl_planning_entry_plan_dtls ", "dtls_id='" . $row[csf("booking_id")] . "' group by booking_no", "booking_no");
					if(!empty($all_booking_ids)){
						$booking_no_disp_arr = return_library_array("select a.dtls_id,a.booking_no from ppl_planning_entry_plan_dtls a where a.status_active=1  $booking_ids_cond_2", 'dtls_id', 'booking_no');
					}

					//print_r($booking_is_short_arr);die;
					//a.recv_number='D n C-GPE-17-00549' and 
					$sql_qc_dtls="SELECT d.id, d.pro_dtls_id, d.roll_maintain, d.barcode_no, d.roll_id, d.roll_no, d.qc_name, d.roll_status, d.roll_width, d.roll_weight, d.roll_length, d.reject_qnty, d.qc_date, d.total_penalty_point, d.total_point,d.fabric_grade, d.comments,e.defect_name,e.defect_count,e.found_in_inch,e.penalty_point,
					case when e.defect_name ='1'  then e.defect_count else 0 end as hole_defect_count,
					case when e.defect_name ='5'  then e.defect_count else 0 end as loop_defect_count ,
					case when e.defect_name ='10'  then e.defect_count else 0 end as press_defect_count ,
					case when e.defect_name ='15'  then e.defect_count else 0 end as lycraout_defect_count ,
					case when e.defect_name ='20'  then e.defect_count else 0 end as lycradrop_defect_count ,
					case when e.defect_name ='25'  then e.defect_count else 0 end as dust_defect_count ,
					case when e.defect_name ='30'  then e.defect_count else 0 end as oilspot_defect_count ,
					case when e.defect_name ='35'  then e.defect_count else 0 end as flyconta_defect_count ,
					case when e.defect_name ='40'  then e.defect_count else 0 end as slub_defect_count ,
					case when e.defect_name ='45'  then e.defect_count else 0 end as patta_defect_count ,
					case when e.defect_name ='50'  then e.defect_count else 0 end as neddle_defect_count ,
					case when e.defect_name ='55'  then e.defect_count else 0 end as sinker_defect_count ,
					case when e.defect_name ='60'  then e.defect_count else 0 end as wheel_defect_count ,
					case when e.defect_name ='65'  then e.defect_count else 0 end as count_defect_count ,
					case when e.defect_name ='70'  then e.defect_count else 0 end as yarn_defect_count ,
					case when e.defect_name ='75'  then e.defect_count else 0 end as neps_defect_count ,
					case when e.defect_name ='80'  then e.defect_count else 0 end as black_defect_count ,
					case when e.defect_name ='85'  then e.defect_count else 0 end as oilink_defect_count ,
					case when e.defect_name ='90'  then e.defect_count else 0 end as setup_defect_count,
					case when e.defect_name ='95'  then e.defect_count else 0 end as pin_hole_defect_count,
					case when e.defect_name ='100'  then e.defect_count else 0 end as slub_hole_defect_count,
					case when e.defect_name ='105'  then e.defect_count else 0 end as needle_mark_defect_count, 
					case when e.defect_name ='110'  then e.defect_count else 0 end as miss_yarn_defect_count 
					from pro_qc_result_mst d,pro_qc_result_dtls e  
					where d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0 $pro_dtls_ids_cond order by d.barcode_no";
					//d.pro_dtls_id=24893 and 
					//echo $sql_qc_dtls;die; 

					$sql_qc_dtls_data=sql_select($sql_qc_dtls); 
					//print_r($sql_qc_dtls_data);die("with jj");
					$sql_qc_data_arr=array(); 
					foreach($sql_qc_dtls_data as $dataRow)
					{
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["hole_defect_count"]+=$dataRow[csf("hole_defect_count")];
						//print_r($sql_qc_data_arr);die("with sumon");
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["loop_defect_count"]+=$dataRow[csf("loop_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["press_defect_count"]+=$dataRow[csf("press_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["lycraout_defect_count"]+=$dataRow[csf("lycraout_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["lycradrop_defect_count"]+=$dataRow[csf("lycradrop_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["dust_defect_count"]+=$dataRow[csf("dust_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["oilspot_defect_count"]+=$dataRow[csf("oilspot_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["flyconta_defect_count"]+=$dataRow[csf("flyconta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["slub_defect_count"]+=$dataRow[csf("slub_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["patta_defect_count"]+=$dataRow[csf("patta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["neddle_defect_count"]+=$dataRow[csf("neddle_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["sinker_defect_count"]+=$dataRow[csf("sinker_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["wheel_defect_count"]+=$dataRow[csf("wheel_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["count_defect_count"]+=$dataRow[csf("count_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["yarn_defect_count"]+=$dataRow[csf("yarn_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["neps_defect_count"]+=$dataRow[csf("neps_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["black_defect_count"]+=$dataRow[csf("black_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["oilink_defect_count"]+=$dataRow[csf("oilink_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["setup_defect_count"]+=$dataRow[csf("setup_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["pin_hole_defect_count"]+=$dataRow[csf("pin_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["slub_hole_defect_count"]+=$dataRow[csf("slub_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["needle_mark_defect_count"]+=$dataRow[csf("needle_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["miss_yarn_defect_count"]+=$dataRow[csf("miss_yarn_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["comments"]=$dataRow[csf("comments")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["reject_qnty"]=$dataRow[csf("reject_qnty")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["qc_name"]=$dataRow[csf("qc_name")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["fabric_grade"]=$dataRow[csf("fabric_grade")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["roll_length"]=$dataRow[csf("roll_length")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["total_point"]=$dataRow[csf("total_point")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["tr_color"]=$dataRow[csf("barcode_no")];
					}

					$i=1;
					foreach($sql_qry_prod_entry as $row)
					{
						//echo $bgcolor;die("sumon");	break;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "")
						$fabric_desc = $fabric_desc_arr[$row[csf('prod_id')]];
						else
						$fabric_desc = $composition_arr[$row[csf('febric_description_id')]];
						$booking_type_string='';	
						if ($row[csf("receive_basis")] == 1 && $row[csf("booking_without_order")] != 1) {
							$booking_no = $row[csf("booking_no")] ;
							$booking_type_id=$booking_type_arr[$booking_no];
							if($booking_type_id==1)
							{
								$is_short=$booking_is_short_arr[$booking_no];
								if($is_short==1)  $booking_type_string="Short Fabric";
								else if($is_short==2)  $booking_type_string="Main Fabric";
							}
							
							else if($booking_type_id==4) $booking_type_string="Sample Booking";
							//echo "set_auto_complete(2);\n";
						}
						else if ($row[csf("receive_basis")] == 1 && $row[csf("booking_without_order")] == 1) 
						{
							$booking_type_string="Sample Without Order";
							$booking_no = $row[csf("booking_no")] ;
						}
						else if ($row[csf("receive_basis")] == 2) {
							//$booking_no = return_field_value("booking_no as booking_no", " ppl_planning_entry_plan_dtls ", "dtls_id='" . $row[csf("booking_id")] . "' group by booking_no", "booking_no");

						$booking_no=$booking_no_disp_arr[$row[csf("booking_id")]];
						$booking_type_id=$booking_type_arr[$booking_no];
						if($booking_type_id==1)
						{
							$is_short=$booking_is_short_arr[$booking_no];
							if($is_short==1)  $booking_type_string="Short Fabric";
							else if($is_short==2)  $booking_type_string="Main Fabric";
						}
						else if($booking_type_id==4) $booking_type_string="Sample Booking";
						else $booking_type_string="Sample Without Order";
						
						//echo "set_auto_complete(2);\n";
						} else {
							//echo "set_auto_complete(1);\n";
							$booking_no = '';
						}
		
						if($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["tr_color"]!="")
						{
							$bgcolor='#3CB371';
						}
							$lot_arr = array_unique(explode(",", $row[csf("yarn_lot")]));
							$yarn_prod_arr = explode(',',$row[csf("yarn_prod_id")]);
						?>
						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="70" align="center"><? echo change_date_format($row[csf("receive_date")]); ?></td>
							<td width="150" align="center" ><p><? 
							if($row[csf("knitting_source")]==1){echo $company_library[$row[csf("knitting_company")]]; }else{ echo $knitting_company_library[$row[csf("knitting_company")]]; }
							?></p></td>
							<td width="100" align="center"><? echo $row[csf("barcode_no")]//$row[csf("challan_no")]; ?></td>
							<td width="60" align="center"><? echo $machine_arr[$row[csf("machine_no_id")]]; ?></td>
							<td width="60" align="center"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
							<td width="100" align="center" class="breakAll"><p><? echo $buyer_library[$row[csf("buyer_id")]]; ?></p></td>
							<?php if($row[csf("booking_without_order")]==1)
							{
								//$non_order_style_ids = return_field_value("b.style_id as style_id", "wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a", "a.booking_no=b.booking_no and a.id='" . $row[csf("booking_id")] . "'","style_id");

								?>
								<td width="100" align="center" ><? //echo $po_number_arr[$row[csf("order_id")]]; ?></td>
								<?php
							}
							else
							{
								?>
								<td width="200" align="center" class="breakAll"><? echo $po_number_arr[$row[csf("order_id")]]; ?></td>
								<?php 
							
							}
							?>
							<td width="100" align="center"> <? if($row[csf("receive_basis")]==2) {echo $row[csf("booking_no")];} ?></td>
							<td width="60" align="center"><? echo $row[csf("roll_weight")]; ?></td>
							<td width="100" align="center" class="breakAll"><p><? 
							$yarnCount="";
							$yarnCountID= array_unique(explode(",", $row[csf("yarn_count")]));
							foreach ($yarnCountID as $yarnCountID) {
										$yarnCount .= $yarn_count_lib_arr[$yarnCountID]. ",";
									}
									echo chop($yarnCount,",");
							
							//echo $yarn_count_lib_arr[$row[csf("yarn_count")]]; 
							
							?></p></td>
							<td width="100" align="center" class="breakAll"><p><? 
							$colorName="";
							$colorID_arr = array_unique(explode(",", $row[csf("color_id")]));
							foreach ($colorID_arr as $colID) {
										$colorName .= $color_arr[$colID] . ",";
									}
									echo chop($colorName,",");
							//echo $row[csf("color_id")];//echo $color_arr[$row[csf("color_id")]]; 
							
							
							?></p></td>
							
							<td width="100" class="breakAll"><p><? 
							$supplierName="";
							
							foreach ($lot_arr as $lotNo) {
								foreach($yarn_prod_arr as $yearProdId)
								{
									if($supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]]!="")
									{
										if($supplierName!="")
										{
											$supplierName .= ",".$supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]];
										}else{
											$supplierName = $supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]];
										}
									}
								}
							}
							echo $supplierName;
							?></p></td>
							<td width="100" align="center" class="breakAll"><p><?
								$yarnLot="";
								
								foreach ($lot_arr as $yarnLotID) {
									$yarnLot .= $yarnLotID. ", ";
								}
									echo chop($yarnLot,', '); 
								?></p>
							</td>							
							<td width="100" align="center" ><? echo $row[csf("machine_dia")]; ?></td>
							<td width="100" align="center" ><? echo $row[csf("width")]; ?></td>
							<td width="100" align="center" ><? echo $row[csf("stitch_length")]; ?></td>
							<td width="100" align="center" ><? echo $row[csf("machine_gg")]; ?></td>
							<td width="100" align="center" class="breakAll"><p><? echo $fabric_desc; ?></p></td>
							<td width="100" align="center" ><? echo $row[csf("gsm")]; ?></td>
							<td width="70" align="center" ><? echo $row[csf("qc_pass_qnty")]; ?></td>
							
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["hole_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["loop_defect_count"]; ?></td>
							<td width="60" align="center" > <? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["miss_yarn_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycraout_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycradrop_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilspot_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["flyconta_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neddle_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["wheel_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["count_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["yarn_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["setup_defect_count"]; ?></td>
							
							
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["pin_hole_defect_count"]; ?></td>

							<td width="60" align="center" ><? echo number_format($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"],2); ?></td>

							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["fabric_grade"]; ?></td>
							<td width="60" align="center" ><? $defect_percent=($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"]*36*100)/($row[csf("width")]*$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"]); echo number_format($defect_percent,2).'%'; ?></td>
							<td width="60" align="center" ><? echo number_format($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"],2); ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["reject_qnty"]; ?></td>
							
							<td width="100" class="breakAll"><p><? echo $operator_lib_arr[$row[csf("operator_name")]]; ?></p></td>
							<td width="100" class="breakAll"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["qc_name"]; ?></p></td>
							<td class="breakAll"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["comments"]; ?></p></td>
						</tr>
						<?
						$total_qc_pass_qty+=$row[csf("qc_pass_qnty")];
						$total_hole_defect+=			$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["hole_defect_count"];
						
						$total_loop_defect+=			$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["loop_defect_count"];
						$total_miss_yarn_defect+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["miss_yarn_defect_count"];
						
						$total_press_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["press_defect_count"];
						$total_lycraout_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycraout_defect_count"];
						$total_lycradrop_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycradrop_defect_count"];
						$total_dust_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["dust_defect_count"];
						$total_oilspot_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilspot_defect_count"];
						$total_flyconta_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["flyconta_defect_count"];
						$total_slub_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_defect_count"];
						$total_patta_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["patta_defect_count"];
						$total_neddle_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neddle_defect_count"];
						$total_sinker_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["sinker_defect_count"];
						$total_wheel_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["wheel_defect_count"];
						$total_count_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["count_defect_count"];
						$total_yarn_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["yarn_defect_count"];
						$total_neps_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neps_defect_count"];
						$total_black_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["black_defect_count"];
						$total_oilink_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilink_defect_count"];
						$total_setup_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["setup_defect_count"];
						
						$total_pin_hole_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["pin_hole_defect_count"];
						$total_slub_hole_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_hole_defect_count"];
						$total_needle_mark_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["needle_mark_defect_count"];
						
						
						$total_totalDefect_point+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"];
						$total_reject_qty+=				$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["reject_qnty"];
						$total_taka+=					$row[csf("qc_pass_qnty")]*$row[csf("rate")];

						$i++;
					}
					?>
					</tbody>
				   	<tfoot>
						<tr style="background-color:#CCCCCC;">
							<td colspan="20" align="right"><strong>Total</strong></td>
							<td align="right" id="td_total_qc_pass_qty"><strong><? echo $total_qc_pass_qty; ?></strong></td>
							<td align="right" id="td_total_hole_defect"><strong><? echo $total_hole_defect; ?></strong></td>
							
							<td align="right" id="td_total_loop_defect"><strong><? echo $total_loop_defect; ?></strong></td>
							<td align="right" id="td_total_missyarn_defect"><strong><? echo $total_miss_yarn_defect; ?></strong></td>
							
							<td align="right" id="td_total_lycraout_defect_count"><strong><? echo $total_lycraout_defect_count; ?></strong></td>
							<td align="right" id="td_total_lycradrop_defect_count"><strong><? echo $total_lycradrop_defect_count; ?></strong></td>
							<td align="right" id="td_total_oilspot_defect_count"><strong><? echo $total_oilspot_defect_count; ?></strong></td>
							<td align="right" id="td_total_flyconta_defect_count"><strong><? echo $total_flyconta_defect_count; ?></strong></td>
							<td align="right" id="td_total_slub_defect_count"><strong><? echo $total_slub_defect_count; ?></strong></td>
							<td align="right" id="td_total_neddle_defect_count"><strong><? echo $total_neddle_defect_count; ?></strong></td>
							<td align="right" id="td_total_wheel_defect_count"><strong><? echo $total_wheel_defect_count; ?></strong></td>
							<td align="right" id="td_total_count_defect_count"><strong><? echo $total_count_defect_count; ?></strong></td>
							<td align="right" id="td_total_yarn_defect_count"><strong><? echo $total_yarn_defect_count; ?></strong></td>
							<td align="right" id="td_total_setup_defect_count"><strong><? echo $total_setup_defect_count; ?></strong></td>
							
							<td align="right" id="td_total_pin_hole_defect"><strong><? echo $total_pin_hole_defect_count; ?></strong></td>
							
							<td align="right" id="td_total_totalDefect_point"><strong><? echo number_format($total_totalDefect_point,2); ?></strong></td>
							<td align="right"><strong></strong></td>
							<td align="right"><strong></strong></td>
							<td align="right"><strong></strong></td>
							<td align="right" id="td_total_reject_qty"><strong><? echo $total_reject_qty; ?></strong></td>
							<td colspan="3"><strong></strong></td>
						</tr>
					</tfoot>
				</table>
				</div>
			</div>
		</fieldset>
		<?
	}
	if($report_format==4) // Show 3 for ISLAM GROUP 21-12-2020 Create by Tipu
	{
		?>
		<fieldset style="width:5415px;">
			<table width="1200">
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption" style="font-size:18px;">Daily Roll wise Knitting QC Report</td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr>
				<tr>
				   <td align="center" width="100%" colspan="12" class="form_caption" style="font-size:12px;"><? echo   show_company($cbo_knitting_company,'',''); ?></td>
				</tr>
			</table>
			<?
				$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
				$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
				$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");				
				$yarn_count_lib_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
				$operator_lib_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
				$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
				$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
				
				//$non_booking_arr=return_library_array( "select  id,booking_no  from  wo_non_ord_samp_booking_mst ", "id", "booking_no"  );
				
				$knitting_company_library = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");

				if ($qc_date_cond!="") 
				{
					$sql_qc_dtls="SELECT d.id, d.pro_dtls_id, d.roll_maintain, d.barcode_no, d.roll_id, d.roll_no, d.qc_name, d.roll_status, d.roll_width, d.roll_weight, d.roll_length, d.reject_qnty, d.qc_date, d.total_penalty_point, d.total_point,d.fabric_grade, d.comments,e.defect_name,e.defect_count,e.found_in_inch,e.penalty_point, d.inserted_by,
					case when e.defect_name ='1'  then e.defect_count else 0 end as hole_defect_count,
					case when e.defect_name ='5'  then e.defect_count else 0 end as loop_defect_count ,
					case when e.defect_name ='10'  then e.defect_count else 0 end as press_defect_count ,
					case when e.defect_name ='15'  then e.defect_count else 0 end as lycraout_defect_count ,
					case when e.defect_name ='20'  then e.defect_count else 0 end as lycradrop_defect_count ,
					case when e.defect_name ='25'  then e.defect_count else 0 end as dust_defect_count ,
					case when e.defect_name ='30'  then e.defect_count else 0 end as oilspot_defect_count ,
					case when e.defect_name ='35'  then e.defect_count else 0 end as flyconta_defect_count ,
					case when e.defect_name ='40'  then e.defect_count else 0 end as slub_defect_count ,
					case when e.defect_name ='45'  then e.defect_count else 0 end as patta_defect_count ,
					case when e.defect_name ='50'  then e.defect_count else 0 end as neddle_defect_count ,
					case when e.defect_name ='55'  then e.defect_count else 0 end as sinker_defect_count ,
					case when e.defect_name ='60'  then e.defect_count else 0 end as wheel_defect_count ,
					case when e.defect_name ='65'  then e.defect_count else 0 end as count_defect_count ,
					case when e.defect_name ='70'  then e.defect_count else 0 end as yarn_defect_count ,
					case when e.defect_name ='75'  then e.defect_count else 0 end as neps_defect_count ,
					case when e.defect_name ='80'  then e.defect_count else 0 end as black_defect_count ,
					case when e.defect_name ='85'  then e.defect_count else 0 end as oilink_defect_count ,
					case when e.defect_name ='90'  then e.defect_count else 0 end as setup_defect_count,
					case when e.defect_name ='95'  then e.defect_count else 0 end as pin_hole_defect_count,
					case when e.defect_name ='100' then e.defect_count else 0 end as slub_hole_defect_count,
					case when e.defect_name ='105' then e.defect_count else 0 end as needle_mark_defect_count, 
					case when e.defect_name ='167' then e.defect_count else 0 end as tara_defect_count 
					from pro_qc_result_mst d,pro_qc_result_dtls e  
					where d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0 $qc_date_cond order by d.barcode_no";
					//echo $sql_qc_dtls;//die; 

					$sql_qc_dtls_data=sql_select($sql_qc_dtls); 
					//print_r($sql_qc_dtls_data);die("with jj");
					$sql_qc_data_arr=array();
					foreach($sql_qc_dtls_data as $dataRow)
					{
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["hole_defect_count"]+=$dataRow[csf("hole_defect_count")];
						//print_r($sql_qc_data_arr);die("with sumon");
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["loop_defect_count"]+=$dataRow[csf("loop_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["press_defect_count"]+=$dataRow[csf("press_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["lycraout_defect_count"]+=$dataRow[csf("lycraout_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["lycradrop_defect_count"]+=$dataRow[csf("lycradrop_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["dust_defect_count"]+=$dataRow[csf("dust_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["oilspot_defect_count"]+=$dataRow[csf("oilspot_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["flyconta_defect_count"]+=$dataRow[csf("flyconta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["slub_defect_count"]+=$dataRow[csf("slub_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["patta_defect_count"]+=$dataRow[csf("patta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["neddle_defect_count"]+=$dataRow[csf("neddle_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["sinker_defect_count"]+=$dataRow[csf("sinker_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["wheel_defect_count"]+=$dataRow[csf("wheel_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["count_defect_count"]+=$dataRow[csf("count_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["yarn_defect_count"]+=$dataRow[csf("yarn_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["neps_defect_count"]+=$dataRow[csf("neps_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["black_defect_count"]+=$dataRow[csf("black_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["oilink_defect_count"]+=$dataRow[csf("oilink_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["setup_defect_count"]+=$dataRow[csf("setup_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["pin_hole_defect_count"]+=$dataRow[csf("pin_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["slub_hole_defect_count"]+=$dataRow[csf("slub_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["needle_mark_defect_count"]+=$dataRow[csf("needle_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["tara_defect_count"]+=$dataRow[csf("tara_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["comments"]=$dataRow[csf("comments")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["reject_qnty"]=$dataRow[csf("reject_qnty")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["fabric_grade"]=$dataRow[csf("fabric_grade")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["roll_length"]=$dataRow[csf("roll_length")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["total_point"]=$dataRow[csf("total_point")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["qc_date"]=$dataRow[csf("qc_date")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["qc_name"]=$dataRow[csf("qc_name")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["roll_status"]=$dataRow[csf("roll_status")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["roll_width"]=$dataRow[csf("roll_width")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["tr_color"]=$dataRow[csf("barcode_no")];

						$all_barcode_arr[$dataRow[csf("barcode_no")]] = $dataRow[csf("barcode_no")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["inserted_by"]=$dataRow[csf("inserted_by")];
		
					}

					$all_barcode_nos = implode(",", array_filter(array_unique($all_barcode_arr)));
					if($all_barcode_nos=="") $all_barcode_nos=0;
					$barCond = $qc_barcode_cond = ""; 
					$all_barcode_arr=explode(",",$all_barcode_nos);
					if($db_type==2 && count($all_barcode_arr)>999)
					{
						$all_barcode_chunk=array_chunk($all_barcode_arr,999) ;
						foreach($all_barcode_chunk as $chunk_arr)
						{
							$barCond.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";	
						}								
						$qc_barcode_cond.=" and (".chop($barCond,'or ').")";
					}
					else
					{ 
						$qc_barcode_cond=" and c.barcode_no in($all_barcode_nos)";
					}
				}
				// echo $qc_barcode_cond;die;
				$sql_grey_prod_entry="SELECT a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group, b.id as pro_dtls_id,b.machine_no_id,b.no_of_roll,b.shift_name,b.yarn_lot,b.yarn_count,b.color_id,b.color_range_id,b.machine_dia,b.stitch_length,b.machine_gg,b.gsm,c.po_breakdown_id as order_id,b.prod_id,b.width,b.operator_name,b.febric_description_id,b.rate,b.yarn_prod_id, c.barcode_no, c.id as roll_id, c.roll_no, sum(c.qnty) as roll_weight, sum(c.qnty) as qc_pass_qnty
				from inv_receive_master a,pro_grey_prod_entry_dtls b,pro_roll_details c
				where  a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $knit_comp_cond $company_name_cond $del_floor_cond $job_no_cond $hide_order_cond $knitting_source_cond $year_cond $del_location_cond $buyer_name_cond $str_cond_date  $barcode_cond $booking_cond $program_cond $qc_barcode_cond
				group by a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group, b.id,b.machine_no_id,b.no_of_roll,b.shift_name,b.yarn_lot,b.yarn_count,b.color_id,b.color_range_id,b.machine_dia, b.stitch_length,b.machine_gg,b.gsm,c.po_breakdown_id,b.prod_id,b.width,b.operator_name,b.febric_description_id,b.rate,b.yarn_prod_id, c.barcode_no, c.id, c.roll_no order by c.barcode_no";
				//echo $sql_grey_prod_entry;
				$sql_qry_prod_entry=sql_select($sql_grey_prod_entry);					
				
				$i=1;
				foreach($sql_qry_prod_entry as $row)
				{
					$pro_dtls_id_arr[$row[csf("pro_dtls_id")]]=$row[csf("pro_dtls_id")];
					$order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
					$booking_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
					if(trim($row[csf("color_id")])){
					$color_id_arr[$row[csf("color_id")]]=chop($row[csf("color_id")],",");
					}
					$prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
					$febric_description_id_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
				}
				$all_po_ids= array_chunk($order_id_arr, 999);
				$all_booking_ids= array_chunk($booking_id_arr, 999);
				$pro_dtls_ids= array_chunk($pro_dtls_id_arr, 999);
				$color_Ids= array_chunk($color_id_arr, 800);
				$all_prod_ids= array_chunk($prod_id_arr, 999);
				$all_febric_description_ids= array_chunk($febric_description_id_arr, 999);


				$color_Ids_cond=" and(";
				foreach($color_Ids as $colorIds)
				{
					if($color_Ids_cond==" and(") $color_Ids_cond.=" id in(". implode(',', $colorIds).")"; else $color_Ids_cond.="  or id in(". implode(',', $colorIds).")";
				}
				$color_Ids_cond.=")";

				$all_prod_ids_cond=" and(";
				foreach($all_prod_ids as $prod_ids)
				{
					if($all_prod_ids_cond==" and(") $all_prod_ids_cond.=" a.id in(". implode(',', $prod_ids).")"; else $all_prod_ids_cond.="  or a.id in(". implode(',', $prod_ids).")";
				}
				$all_prod_ids_cond.=")";

				$all_febric_description_cond=" and(";
				foreach($all_prod_ids as $description_ids)
				{
					if($all_febric_description_cond==" and(") $all_febric_description_cond.=" a.id in(". implode(',', $description_ids).")"; else $all_febric_description_cond.="  or a.id in(". implode(',', $description_ids).")";
				}
				$all_febric_description_cond.=")";


				$po_ids_cond=" and("; $order_ids_cond=" and(";
				foreach($all_po_ids as $po_ids)
				{
					if($po_ids_cond==" and(") $po_ids_cond.=" a.id in(". implode(',', $po_ids).")"; else $po_ids_cond.="  or a.id in(". implode(',', $po_ids).")";


					if($order_ids_cond==" and(") $order_ids_cond.=" b.order_id in(". implode(',', $po_ids).")"; else $order_ids_cond.="  or b.order_id in(". implode(',', $po_ids).")";

				}
				$po_ids_cond.=")";
				$order_ids_cond.=")";

				$booking_ids_cond=" and(";$booking_ids_cond_2=" and(";
				foreach($all_booking_ids as $booking_ids)
				{
					if($booking_ids_cond==" and(") $booking_ids_cond.=" a.id in(". implode(',', $booking_ids).")"; else $booking_ids_cond.="  or a.id in(". implode(',', $booking_ids).")";
					if($booking_ids_cond_2==" and(") $booking_ids_cond_2.=" a.dtls_id in(". implode(',', $booking_ids).")"; else $booking_ids_cond_2.="  or a.dtls_id in(". implode(',', $booking_ids).")";
				}
				$booking_ids_cond.=")";
				$booking_ids_cond_2.=")";

				$pro_dtls_ids_cond=" and(";
				foreach($pro_dtls_ids as $pro_dtls_ids)
				{
					if($pro_dtls_ids_cond==" and(") $pro_dtls_ids_cond.=" d.pro_dtls_id in(". implode(',', $pro_dtls_ids).")"; else $pro_dtls_ids_cond.="  or d.pro_dtls_id in(". implode(',', $pro_dtls_ids).")";
				}
				$pro_dtls_ids_cond.=")";

				//echo $pro_dtls_ids_cond;die;
				if(!empty($color_Ids)){
					$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 $color_Ids_cond", 'id', 'color_name');
				}
				if(!empty($all_prod_ids)){
					$fabric_desc_arr = return_library_array("select a.id, a.item_description from product_details_master a where a.item_category_id=13 $all_prod_ids_cond", "id", "item_description");
				}

				$sql_pro_grey_prod_del = sql_select("select a.id, a.sys_number, a.delevery_date,b.product_id,b.order_id,b.barcode_num from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.entry_form=56 and a.status_active=1 and a.is_deleted=0 $knit_comp_cond $knitting_source_cond $buyer_name_cond $del_location_cond $order_ids_cond order by id");
				$pro_grey_arr_data=array();
				foreach($sql_pro_grey_prod_del as $row_prod)
				{
					$pro_grey_arr_data[$row_prod[csf("order_id")]][$row_prod[csf("product_id")]][$row_prod[csf("barcode_num")]]["sys_number"]=$row_prod[csf("sys_number")];
					$pro_grey_arr_data[$row_prod[csf("order_id")]][$row_prod[csf("product_id")]][$row_prod[csf("barcode_num")]]["delevery_date"]=$row_prod[csf("delevery_date")];
				}

				$composition_arr = array();
				$composition_arr_new = array();
				if(!empty($all_prod_ids)){
					$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
				}
				$data_array = sql_select($sql_deter);
				if (count($data_array) > 0) 
				{
					foreach ($data_array as $row) {
						if (array_key_exists($row[csf('id')], $composition_arr)) {
							$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
							$composition_arr_new[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";

						} else {
							//$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
							$composition_arr[$row[csf('id')]] = $row[csf('construction')];
							$composition_arr_new[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";

						}
					}
				}

				if(!empty($all_prod_ids)){
					$sql_supplier = sql_select("select a.id as product_id,a.supplier_id,a.lot,a.yarn_type,a.yarn_comp_type1st,a.yarn_comp_percent1st,yarn_count_id from product_details_master a where item_category_id=1 $company_name_cond ");
				}
				//$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%";
				$supplier_arr_data=array();
				$yarn_composition_arr_data=array();
				foreach($sql_supplier as $row_spl)
				{
					$supplier_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["supplier_id"]=$row_spl[csf("supplier_id")];
					$supplier_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["yarn_type"]=$row_spl[csf("yarn_type")];

					$yarn_composition_arr_data[$row_spl[csf("yarn_count_id")]][$row_spl[csf("product_id")]]["yarn_conmposition"]=$composition[$row_spl[csf('yarn_comp_type1st')]] . " " . $row_spl[csf('yarn_comp_percent1st')] . "%";

					$yarn_composition_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["yarn_conmposition_lot"]=$composition[$row_spl[csf('yarn_comp_type1st')]] . " " . $row_spl[csf('yarn_comp_percent1st')] . "%";
				}
				//print_r($yarn_composition_arr_data);die;

				$style_ref_arr=array();
				if(!empty($all_po_ids)){
					$style_ref_query = "select a.id,b.style_ref_no,a.job_no_mst,a.po_number from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $po_ids_cond  and a.is_deleted=0 and a.status_active=1";//die;
				}
				$sql_style_ref=sql_select($style_ref_query);
				foreach($sql_style_ref as $rows)
				{
					$style_ref_arr[$rows[csf("id")]]["style_ref_no"]=$rows[csf("style_ref_no")];
					$job_no_arr[$rows[csf("id")]]["job_no_mst"]=$rows[csf("job_no_mst")];
					$po_number_arr[$rows[csf("id")]]=$rows[csf("po_number")];
				}

				

				if(!empty($all_booking_ids))
				{
					$non_order_style_id = return_library_array("select a.id,b.style_id from wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a where a.status_active=1 and a.booking_no=b.booking_no  $booking_ids_cond", 'id', 'style_id');
				}

				if(!empty($all_booking_ids)){
					$booking_no_disp_arr = return_library_array("select a.dtls_id,a.booking_no from ppl_planning_entry_plan_dtls a where a.status_active=1  $booking_ids_cond_2", 'dtls_id', 'booking_no');
				}
				// echo "<pre>";print_r($booking_no_disp_arr);die;
				/*$vari="";
				foreach ($booking_no_disp_arr as $key => $value) 
				{
					if ($vari=="") 
					{
						$vari=$value;
					}
					else{
						$vari.=','.$value;
					}
				}*/
				// echo $vari.'<br>';die;
				/*$prog_booking_ids_cond = implode("','", array_unique($booking_no_disp_arr));
				//echo "'".$prog_booking_ids_cond."'";die;
				if (!empty($booking_no_disp_arr)) 
				{
					$sql_booking_query = "select a.booking_type, a.booking_no, a.is_short from wo_booking_mst a where status_active=1 and a.booking_no in('$prog_booking_ids_cond')";
				}*/
				if(!empty($all_booking_ids))
				{
					$sql_booking_query = "select a.booking_type, a.booking_no, a.is_short from wo_booking_mst a where status_active=1";//die;
					//echo "select a.booking_type, a.booking_no, a.is_short from wo_booking_mst a where status_active=1 $booking_ids_cond ";
				}

				$booking_type_result=sql_select($sql_booking_query);
				foreach($booking_type_result as $rows)
				{
					$booking_type_arr[$rows[csf("booking_no")]]=$rows[csf("booking_type")];
					$booking_is_short_arr[$rows[csf("booking_no")]]=$rows[csf("is_short")];
					
				}
				if ($qc_date_cond=="") 
				{
					$sql_qc_dtls="SELECT d.id, d.pro_dtls_id, d.roll_maintain, d.barcode_no, d.roll_id, d.roll_no, d.qc_name, d.roll_status, d.roll_width, d.roll_weight, d.roll_length, d.reject_qnty, d.qc_date, d.total_penalty_point, d.total_point,d.fabric_grade, d.comments,e.defect_name,e.defect_count,e.found_in_inch,e.penalty_point, d.inserted_by,
					case when e.defect_name ='1'  then e.defect_count else 0 end as hole_defect_count,
					case when e.defect_name ='5'  then e.defect_count else 0 end as loop_defect_count ,
					case when e.defect_name ='10'  then e.defect_count else 0 end as press_defect_count ,
					case when e.defect_name ='15'  then e.defect_count else 0 end as lycraout_defect_count ,
					case when e.defect_name ='20'  then e.defect_count else 0 end as lycradrop_defect_count ,
					case when e.defect_name ='25'  then e.defect_count else 0 end as dust_defect_count ,
					case when e.defect_name ='30'  then e.defect_count else 0 end as oilspot_defect_count ,
					case when e.defect_name ='35'  then e.defect_count else 0 end as flyconta_defect_count ,
					case when e.defect_name ='40'  then e.defect_count else 0 end as slub_defect_count ,
					case when e.defect_name ='45'  then e.defect_count else 0 end as patta_defect_count ,
					case when e.defect_name ='50'  then e.defect_count else 0 end as neddle_defect_count ,
					case when e.defect_name ='55'  then e.defect_count else 0 end as sinker_defect_count ,
					case when e.defect_name ='60'  then e.defect_count else 0 end as wheel_defect_count ,
					case when e.defect_name ='65'  then e.defect_count else 0 end as count_defect_count ,
					case when e.defect_name ='70'  then e.defect_count else 0 end as yarn_defect_count ,
					case when e.defect_name ='75'  then e.defect_count else 0 end as neps_defect_count ,
					case when e.defect_name ='80'  then e.defect_count else 0 end as black_defect_count ,
					case when e.defect_name ='85'  then e.defect_count else 0 end as oilink_defect_count ,
					case when e.defect_name ='90'  then e.defect_count else 0 end as setup_defect_count,
					case when e.defect_name ='95'  then e.defect_count else 0 end as pin_hole_defect_count,
					case when e.defect_name ='100' then e.defect_count else 0 end as slub_hole_defect_count,
					case when e.defect_name ='105' then e.defect_count else 0 end as needle_mark_defect_count, 
					case when e.defect_name ='167' then e.defect_count else 0 end as tara_defect_count 
					from pro_qc_result_mst d,pro_qc_result_dtls e  
					where d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0 $pro_dtls_ids_cond order by d.barcode_no";
					//echo $sql_qc_dtls;//die; 

					$sql_qc_dtls_data=sql_select($sql_qc_dtls); 
					//print_r($sql_qc_dtls_data);die("with jj");
					$sql_qc_data_arr=array();
					foreach($sql_qc_dtls_data as $dataRow)
					{
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["hole_defect_count"]+=$dataRow[csf("hole_defect_count")];
						//print_r($sql_qc_data_arr);die("with sumon");
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["loop_defect_count"]+=$dataRow[csf("loop_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["press_defect_count"]+=$dataRow[csf("press_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["lycraout_defect_count"]+=$dataRow[csf("lycraout_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["lycradrop_defect_count"]+=$dataRow[csf("lycradrop_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["dust_defect_count"]+=$dataRow[csf("dust_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["oilspot_defect_count"]+=$dataRow[csf("oilspot_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["flyconta_defect_count"]+=$dataRow[csf("flyconta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["slub_defect_count"]+=$dataRow[csf("slub_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["patta_defect_count"]+=$dataRow[csf("patta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["neddle_defect_count"]+=$dataRow[csf("neddle_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["sinker_defect_count"]+=$dataRow[csf("sinker_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["wheel_defect_count"]+=$dataRow[csf("wheel_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["count_defect_count"]+=$dataRow[csf("count_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["yarn_defect_count"]+=$dataRow[csf("yarn_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["neps_defect_count"]+=$dataRow[csf("neps_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["black_defect_count"]+=$dataRow[csf("black_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["oilink_defect_count"]+=$dataRow[csf("oilink_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["setup_defect_count"]+=$dataRow[csf("setup_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["pin_hole_defect_count"]+=$dataRow[csf("pin_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["slub_hole_defect_count"]+=$dataRow[csf("slub_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["needle_mark_defect_count"]+=$dataRow[csf("needle_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["tara_defect_count"]+=$dataRow[csf("tara_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["comments"]=$dataRow[csf("comments")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["reject_qnty"]=$dataRow[csf("reject_qnty")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["fabric_grade"]=$dataRow[csf("fabric_grade")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["roll_length"]=$dataRow[csf("roll_length")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["total_point"]=$dataRow[csf("total_point")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["qc_date"]=$dataRow[csf("qc_date")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["qc_name"]=$dataRow[csf("qc_name")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["roll_status"]=$dataRow[csf("roll_status")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["roll_width"]=$dataRow[csf("roll_width")];
						
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["tr_color"]=$dataRow[csf("barcode_no")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["inserted_by"]=$dataRow[csf("inserted_by")];
					}
				}				

				// Summary Start =============================================
				$qc_summary_qty=0; $qc_summary_roll=0; $summary_arr=array();
				foreach($sql_qry_prod_entry as $row)
				{
					$summary_arr[$row[csf("knitting_source")]]['qc_pass_qnty']+=$row[csf("qc_pass_qnty")];
					$summary_arr[$row[csf("knitting_source")]]['roll_no']++;
					if($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["tr_color"]!="")
					{
						$qc_summary_qty+=$row[csf("qc_pass_qnty")];
						$qc_summary_roll++;
					}
				}
				// Summary End ===============================================
			?>
			<style>
				.breakAll{
					word-break:break-all;
					word-wrap: break-word;
				}
			</style>
			<div style="width:6425px; float:left;">
				<!-- Summary Start -->				
				<table width="300"  style=" margin-bottom:5px;"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
					<thead>
						<tr>
							<th colspan="3" style="text-align: center;"><strong>Summary</strong></th>
						</tr>
						<tr>
							<th><strong>SL</strong></th>
							<th><strong>Qnty.</strong></th>
							<th><strong>Roll</strong></th>
						</tr>
						<tr>
							<td style="background: #FFFFFF; width: 150px;"><strong>Production (In-house)</strong></td>
							<td align="right" style="background: #FFFFFF;"><strong><? 
							$production_in_house_qty=$summary_arr[1]['qc_pass_qnty']; 
							echo number_format($production_in_house_qty,2); ?></strong></td>
							<td align="right" style="background: #FFFFFF;"><strong><? 
							$production_in_house_roll=$summary_arr[1]['roll_no']; 
							echo number_format($production_in_house_roll,2); ?></strong></td>
						</tr>
						<tr>
							<td style="background: #E9F3FF;"><strong>Production (Out -Bound)</strong></td>
							<td align="right" style="background: #E9F3FF;"><strong><? 
							$production_out_bound_qty=$summary_arr[3]['qc_pass_qnty']; 
							echo number_format($production_out_bound_qty,2); ?></strong></td>
							<td align="right" style="background: #E9F3FF;"><strong><? 
							$production_out_bound_roll=$summary_arr[3]['roll_no']; 
							echo number_format($production_out_bound_roll,2); ?></strong></td>
						</tr>
						<tr>
							<td style="background: #FFFFFF;"><strong>Total Production</strong></td>
							<td align="right" style="background: #FFFFFF;"><strong><? 
							$total_production_qty=$production_in_house_qty+$production_out_bound_qty;
							echo number_format($total_production_qty,2);
							?></strong></td>
							<td align="right" style="background: #FFFFFF;"><strong><? 
							$total_production_roll=$production_in_house_roll+$production_out_bound_roll; 
							echo number_format($total_production_roll,2); ?></strong></td>
						</tr>
						<tr>
							<td style="background: #E9F3FF;"><strong>Grey Fabric QC Qty.</strong></td>
							<td align="right" style="background: #E9F3FF;"><strong><? 
							echo number_format($qc_summary_qty,2); ?></strong></td>
							<td align="right" style="background: #E9F3FF;"><strong><? 
							echo number_format($qc_summary_roll,2); ?></strong></td>
						</tr>
						<tr>
							<td style="background: #FFFFFF;"><strong>Balance </strong></td>
							<td align="right" style="background: #FFFFFF;"><strong><? 
							echo number_format($total_production_qty-$qc_summary_qty,2); ?></strong></td>
							<td align="right" style="background: #FFFFFF;"><strong><? 
							echo number_format($total_production_roll-$qc_summary_roll,2); ?></strong></td>
						</tr>
					</thead>
				</table>
				<br>
				<br>
				<br>
				<!-- Summary End -->

				<table width="6420" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left;">
					<thead>
						<tr>
							<th width="40">SL</th>
							<th width="70">QC Date</th>
							<th width="70">Knitting Date</th>
							<th width="150">Company Name</th>
							<th width="110">Knitting Source</th>
							<th width="150">Knitting Company</th>
							<th width="100">Program No</th>
							<th width="100">Production No</th>
							<th width="100">Barcode</th>
							<th width="100">Out Bond Chalan No.</th>
							<th width="60">MC No</th>
							<th width="60">Roll No</th>
							<th width="60">Shift</th>
							<th width="100">Buyer</th>
							<th width="100">Job No</th>
							<th width="200">Order No</th>
							<th width="100">Style Ref.</th>
							<th width="120">Booking Type</th>
							<th width="100">Booking No</th>
							<th width="60">Roll Weight</th>
							<th width="100">Yarn Count</th>
							<th width="100">Fabric Color</th>
							<th width="100">Yarn Type</th>
							<th width="100">Supplier</th>
							<th width="100">Yarn Lot</th>
							<th width="100">Yarn Composition</th>
							<th width="100">M/C DIA</th>
							<th width="100">Finish Dia</th>
							<th width="100">Stitch Length</th>
							<th width="100">Gauge</th>
							<th width="100">Roll Width (inch)</th>
							<th width="100">Fabric Type</th>
							<th width="100">Req.GSM</th>
							<th width="70">Qc Pass Qty</th>
							<th width="100">Rate/ kg</th>
							<th width="100">Total TK</th>
							<th width="130">Delivery Challan No</th>
							<th width="100">Delivery Date</th>

							<th width="60">Hole</th>
							<th width="60">Loop</th>
							<th width="60">Press Off</th>
							<th width="60">Lycra Out</th>
							<th width="60">Lycra Drop</th>
							<th width="60">Dust</th>
							<th width="60">Oil Spot</th>
							<th width="60">Fly Conta</th>
							<th width="60">Slub</th>
							<th width="60">Patta</th>
							<th width="60">Needle Break</th>
							<th width="60">Sinker Mark</th>
							<th width="60">Wheel Free</th>
							<th width="60">Count Mix</th>
							<th width="60">Yarn Contra</th>
							<th width="60">Neps</th>
							<th width="60">Black Spot</th>
							<th width="100">Oil/ink Mark</th>
							<th width="60">Set Up</th>
							<th width="60">Pin Hole</th>
							<th width="60">Slub Hole</th>
							<th width="60">Needle Mark</th>

							<th width="60">TTL Points</th>
							<th width="60">Grade</th>
							<th width="60">Defect</th>
							<th width="60">Length Yds</th>
							<th width="60">Reject Qty</th>
							<th width="60">Roll Status</th>
							<th width="60">QC Name</th>
							<th width="100">Insert User</th>
							<th width="100">Responsible</th>
							<th width="100">Designation</th>
							<th width="100">Reason for Rejection</th>
							<th width="100">Operator Name</th>
							<th width="100">Supervisor</th>
							<th width="100">Defective Length</th>
							<th>Remarks</th>
						</tr>
					</thead>
				</table>			  	
				<div style="width:6425px; float:left; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table width="6407" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show4" style="float:left;">
					<tbody>
					<?
					

					$i=1;
					foreach($sql_qry_prod_entry as $row)
					{
						//echo $bgcolor;die("sumon");	break;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "")
						$fabric_desc = $fabric_desc_arr[$row[csf('prod_id')]];
						else
						$fabric_desc = $composition_arr[$row[csf('febric_description_id')]];
						$booking_type_string='';	
						if ($row[csf("receive_basis")]==1 && $row[csf("booking_without_order")]!= 1) 
						{
							$booking_no = $row[csf("booking_no")] ;
							$booking_type_id=$booking_type_arr[$booking_no];
							if($booking_type_id==1)
							{
								$is_short=$booking_is_short_arr[$booking_no];
								if($is_short==1)  $booking_type_string="Short Fabric";
								else if($is_short==2)  $booking_type_string="Main Fabric";
							}							
							else if($booking_type_id==4) $booking_type_string="Sample Booking";
							//echo "set_auto_complete(2);\n";
						}
						else if ($row[csf("receive_basis")]==1 && $row[csf("booking_without_order")]==1) 
						{
							$booking_type_string="Sample Without Order";
							$booking_no = $row[csf("booking_no")] ;
						}
						else if ($row[csf("receive_basis")] == 2) 
						{
							//$booking_no = return_field_value("booking_no as booking_no", " ppl_planning_entry_plan_dtls ", "dtls_id='" . $row[csf("booking_id")] . "' group by booking_no", "booking_no");
							// echo "string<br>";
							$booking_no=$booking_no_disp_arr[$row[csf("booking_id")]];
							$booking_type_id=$booking_type_arr[$booking_no];
							if($booking_type_id==1)
							{
								$is_short=$booking_is_short_arr[$booking_no];
								if($is_short==1)  $booking_type_string="Short Fabric";
								else if($is_short==2)  $booking_type_string="Main Fabric";
							}
							else if($booking_type_id==4) $booking_type_string="Sample Booking";
							else $booking_type_string="Sample Without Order";
							//echo $booking_type_id.'='.$booking_no."TTT<br>";
						} 
						else 
						{
							//echo "set_auto_complete(1);\n";
							$booking_no = '';
						}
		
						if($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["tr_color"]!="")
						{
							$bgcolor='#3CB371';
						}
							$lot_arr = array_unique(explode(",", $row[csf("yarn_lot")]));
							$yarn_prod_arr = explode(',',$row[csf("yarn_prod_id")]);
						?>
						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="70" align="center"><p><? echo change_date_format($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["qc_date"]); ?></p></td>
							<td width="70" align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
							<td width="150" align="center" ><p><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></p></td>
							<td width="110" align="center"><p><? echo $knitting_source[$row[csf("knitting_source")]]; ?></p></td> 
							<td width="150" align="center" ><p><? 
							if($row[csf("knitting_source")]==1){echo $company_library[$row[csf("knitting_company")]]; }else{ echo $knitting_company_library[$row[csf("knitting_company")]]; }
							?></p></td>
							<td width="100" align="center"><p><? if($row[csf("receive_basis")]==2) {echo $row[csf("booking_no")];} ?></p></td>
							<td width="100"><p><? echo $row[csf("recv_number")]; ?></p></td>
							<td width="100" align="center"><p><? echo $row[csf("barcode_no")]//$row[csf("challan_no")]; ?></p></td>
							<td width="100"><p><? echo $row[csf("challan_no")]; ?></p></td>
							<td width="60" align="center"><p><? echo $machine_arr[$row[csf("machine_no_id")]]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf("roll_no")]; ?></p></td>
							<td width="60" align="center"><p><? echo $shift_name[$row[csf("shift_name")]]; ?></p></td>
							<td width="100" align="center" class="breakAll"><p><? echo $buyer_library[$row[csf("buyer_id")]]; ?></p></td>
							<?php if($row[csf("booking_without_order")]==1) //3
							{
								//$non_order_style_ids = return_field_value("b.style_id as style_id", "wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a", "a.booking_no=b.booking_no and a.id='" . $row[csf("booking_id")] . "'","style_id");

								?>
								<td width="100" align="center"><? //echo $row[csf("booking_without_order")]; ?></td>
								<td width="200" align="center" ><? //echo $po_number_arr[$row[csf("order_id")]]; ?></td>
								<td width="100" align="center" class="breakAll"><p><? echo $style_library[$non_order_style_id[$row[csf("booking_id")]]]; ?></p></td>
								<?php
							}
							else
							{
								?>
								<td width="100" align="center" ><p><? echo $job_no_arr[$row[csf("order_id")]]["job_no_mst"]; ?></p></td>
								<td width="200" align="center" class="breakAll"><p><? echo $po_number_arr[$row[csf("order_id")]]; ?></p></td>
								<td width="100" align="center" class="breakAll"><? echo $style_ref_arr[$row[csf("order_id")]]["style_ref_no"]; ?></td>
								<?php 
							}
							?>
							
							<td width="120" align="center" class="breakAll"><? echo $booking_type_string; ?></td>
							<td width="100" align="center" class="breakAll"><? echo $booking_no; ?></td>
							<td width="60" align="center" class="breakAll"><? echo $row[csf("roll_weight")]; ?></td>
							<td width="100" align="center" class="breakAll"><p><? 
							$yarnCount="";
							$yarnCountID= array_unique(explode(",", $row[csf("yarn_count")]));
							foreach ($yarnCountID as $yarnCountID) 
							{
								$yarnCount .= $yarn_count_lib_arr[$yarnCountID]. ",";
							}
							echo chop($yarnCount,",");
							
							//echo $yarn_count_lib_arr[$row[csf("yarn_count")]]; 
							
							?></p></td>
							<td width="100" align="center" class="breakAll"><p><? 
							$colorName="";
							$colorID_arr = array_unique(explode(",", $row[csf("color_id")]));
							foreach ($colorID_arr as $colID) 
							{
								$colorName .= $color_arr[$colID] . ",";
							}
							echo chop($colorName,",");
							//echo $row[csf("color_id")];//echo $color_arr[$row[csf("color_id")]]; 
							
							
							?></p></td>
							<td width="100" align="center" class="breakAll"><p><? 
							$yarnTypeName="";
							
							foreach ($lot_arr as $lotNo) 
							{
								foreach($yarn_prod_arr as $yearProdId)
								{
									if($yarn_type[$supplier_arr_data[$lotNo][$yearProdId]["yarn_type"]]!="")
									{
										if($yarnTypeName!="")
										{
											$yarnTypeName .= ",".$yarn_type[$supplier_arr_data[$lotNo][$yearProdId]["yarn_type"]];
										}else{
											$yarnTypeName = $yarn_type[$supplier_arr_data[$lotNo][$yearProdId]["yarn_type"]];
										}
									}
								}
							}
							echo $yarnTypeName;
							
							?></p> 
							</td>
							<td width="100" class="breakAll"><p><? 
							$supplierName="";
							
							foreach ($lot_arr as $lotNo) 
							{
								foreach($yarn_prod_arr as $yearProdId)
								{
									if($supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]]!="")
									{
										if($supplierName!="")
										{
											$supplierName .= ",".$supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]];
										}else{
											$supplierName = $supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]];
										}
									}
								}
							}
							echo $supplierName;
							?></p></td>
							<td width="100" align="center" class="breakAll"><p><?
								$yarnLot="";
								
								foreach ($lot_arr as $yarnLotID) {
									$yarnLot .= $yarnLotID. ", ";
								}
									echo chop($yarnLot,', '); 
								?></p>
							</td>
							<td width="100" align="center" class="breakAll"><p><? 
							$yarnCountName="";

							foreach ($lot_arr as $lotNo) 
							{
								
								foreach($yarn_prod_arr as $yearProdId)
								{
									if($yarn_composition_arr_data[$lotNo][$yearProdId]["yarn_conmposition_lot"]!="")
									{
										if($yarnCountName!="")
										{
											$yarnCountName .=",". $yarn_composition_arr_data[$lotNo][$yearProdId]["yarn_conmposition_lot"];
											
										}else {
											$yarnCountName = $yarn_composition_arr_data[$lotNo][$yearProdId]["yarn_conmposition_lot"];
										}
									}
									
								}
							}
							echo $yarnCountName;
							
							?></p></td>
							<td width="100" align="center" class="breakAll"><? echo $row[csf("machine_dia")]; ?></td>
							<td width="100" align="center" class="breakAll"><? echo $row[csf("width")]; ?></td>
							<td width="100" align="center" class="breakAll"><? echo $row[csf("stitch_length")]; ?></td>
							<td width="100" align="center" class="breakAll"><? echo $row[csf("machine_gg")]; ?></td>
							<td width="100" align="center" class="breakAll"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_width"]; ?></td>
							<td width="100" align="center" class="breakAll"><p><? echo $fabric_desc; ?></p></td>
							<td width="100" align="center" class="breakAll"><? echo $row[csf("gsm")]; ?></td>
							<td width="70" align="center" class="breakAll"><? echo $row[csf("qc_pass_qnty")]; ?></td>
							<td width="100" align="center" class="breakAll"><? echo $row[csf("rate")]; ?></td>
							<td width="100" align="center" class="breakAll"><? echo $row[csf("qc_pass_qnty")]*$row[csf("rate")]." tk"; ?></td>
							<td width="130" class="breakAll"><p><? echo $pro_grey_arr_data[$row[csf("order_id")]][$row[csf("prod_id")]][$row[csf("barcode_no")]]["sys_number"]; ?></p></td>
							<td width="100" align="center" class="breakAll"><? echo change_date_format($pro_grey_arr_data[$row[csf("order_id")]][$row[csf("prod_id")]][$row[csf("barcode_no")]]["delevery_date"]); ?></td>

							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["hole_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["loop_defect_count"]; ?></td>
							<td width="60" align="center"  title="<? echo $row[csf("barcode_no")]; ?>"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["press_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycraout_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycradrop_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["dust_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilspot_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["flyconta_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["patta_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neddle_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["sinker_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["wheel_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["count_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["yarn_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neps_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["black_defect_count"]; ?></td>
							<td width="100" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilink_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["setup_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["pin_hole_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_hole_defect_count"]; ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["needle_mark_defect_count"]; ?></td>

							<td width="60" align="center" ><? echo number_format($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"],2); ?></td>
							<td width="60" align="center" class="breakAll"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["fabric_grade"]; ?></td>
							<td width="60" align="center" ><? $defect_percent=($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"]*36*100)/($row[csf("width")]*$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"]); echo number_format($defect_percent,2).'%'; ?></td>
							<td width="60" align="center" ><? echo number_format($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"],2); ?></td>
							<td width="60" align="center" ><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["reject_qnty"]; ?></td>
							<td width="60" align="center" class="breakAll"><? 
							$roll_status = array(1 => 'QC Pass', 2 => 'Held Up', 3 => 'Reject');
							echo $roll_status[$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_status"]]; ?></td>
							<td width="60" class="breakAll" align="center" title="<? echo 'qc_name'; ?>"><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["qc_name"]; ?></td>
							<td align="center" width="100"><? echo $user_arr[$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["inserted_by"]]; ?></td>
							<td width="100"></td>
							<td width="100">&nbsp;</td>
							<td width="100"></td>
							<td width="100" class="breakAll"><p><? echo $operator_lib_arr[$row[csf("operator_name")]]; ?></p></td>
							<td width="100"></td>
							<td width="100" align="center" ><? echo number_format($defect_percent*$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"],2); ?></td>
							<td class="breakAll"><p><? echo $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["comments"]; ?></p></td>
						</tr>
						<?
						$total_qc_pass_qty+=$row[csf("qc_pass_qnty")];
						$total_hole_defect+=			$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["hole_defect_count"];						
						$total_loop_defect+=			$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["loop_defect_count"];						
						$total_press_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["press_defect_count"];
						$total_lycraout_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycraout_defect_count"];
						$total_lycradrop_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycradrop_defect_count"];
						$total_dust_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["dust_defect_count"];
						$total_oilspot_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilspot_defect_count"];
						$total_flyconta_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["flyconta_defect_count"];
						$total_slub_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_defect_count"];
						$total_patta_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["patta_defect_count"];
						$total_neddle_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neddle_defect_count"];
						$total_sinker_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["sinker_defect_count"];
						$total_wheel_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["wheel_defect_count"];
						$total_count_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["count_defect_count"];
						$total_yarn_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["yarn_defect_count"];
						$total_neps_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neps_defect_count"];
						$total_black_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["black_defect_count"];
						$total_oilink_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilink_defect_count"];
						$total_setup_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["setup_defect_count"];						
						$total_pin_hole_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["pin_hole_defect_count"];
						$total_slub_hole_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_hole_defect_count"];
						$total_needle_mark_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["needle_mark_defect_count"];
						$total_totalDefect_point+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"];
						$total_reject_qty+=				$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["reject_qnty"];
						$total_taka+=					$row[csf("qc_pass_qnty")]*$row[csf("rate")];

						$i++;
					}
					?>
					</tbody>
				</table>
				<table width="6407" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="40"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="150"></th>
							<th width="110"></th>
							<th width="150"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="200"></th>
							<th width="100"></th>
							<th width="120"></th>
							<th width="100"></th>
							<th width="60"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100" align="right"><strong>Total</strong></th>
							<th width="70" align="right" id="td_total_qc_pass_qty"><strong><? echo $total_qc_pass_qty; ?></strong></th>
							<th width="100" align="right"><strong></strong></th>
							<th width="100" align="right" id="td_total_taka"><strong><? echo $total_taka; ?></strong></th>
							<th width="130" align="right"><strong></strong></th>
							<th width="100" align="right"><strong></strong></th>

							<th width="60" align="right" id="td_total_hole_defect"><strong><? echo $total_hole_defect; ?></strong></th>							
							<th width="60" align="right" id="td_total_loop_defect"><strong><? echo $total_loop_defect; ?></strong></th>							
							<th width="60" align="right" id="td_total_press_defect_count"><strong><? echo $total_press_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_lycraout_defect_count"><strong><? echo $total_lycraout_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_lycradrop_defect_count"><strong><? echo $total_lycradrop_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_dust_defect_count"><strong><? echo $total_dust_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_oilspot_defect_count"><strong><? echo $total_oilspot_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_flyconta_defect_count"><strong><? echo $total_flyconta_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_slub_defect_count"><strong><? echo $total_slub_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_patta_defect_count"><strong><? echo $total_patta_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_neddle_defect_count"><strong><? echo $total_neddle_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_sinker_defect_count"><strong><? echo $total_sinker_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_wheel_defect_count"><strong><? echo $total_wheel_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_count_defect_count"><strong><? echo $total_count_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_yarn_defect_count"><strong><? echo $total_yarn_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_neps_defect_count"><strong><? echo $total_neps_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_black_defect_count"><strong><? echo $total_black_defect_count; ?></strong></th>
							<th width="100" align="right" id="td_total_oilink_defect_count"><strong><? echo $total_oilink_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_setup_defect_count"><strong><? echo $total_setup_defect_count; ?></strong></th>							
							<th width="60" align="right" id="td_total_pin_hole_defect"><strong><? echo $total_pin_hole_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_slub_hole_defect"><strong><? echo $total_slub_hole_defect_count; ?></strong></th>
							<th width="60" align="right" id="td_total_needle_mark_defect"><strong><? echo $total_needle_mark_defect_count; ?></strong></th>

							<th width="60" align="right" id="td_total_totalDefect_point"><strong><? echo number_format($total_totalDefect_point,2); ?></strong></th>
							<th width="60" align="right"><strong></strong></th>
							<th width="60" align="right"><strong></strong></th>
							<th width="60" align="right"><strong></strong></th>
							<th width="60" align="right" id="td_total_reject_qty"><strong><? echo $total_reject_qty; ?></strong></th>
							<th width="60"></th>							
							<th width="60"></th>							
							<th width="100"></th>							
							<th width="100"></th>							
							<th width="100"></th>							
							<th width="100"></th>							
							<th width="100"></th>							
							<th width="100"></th>						
							<th></th>						
						</tr>
					</tfoot>
				</table>	
				</div>
			</div>
		</fieldset>
		<?
	}
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$reportType";
	exit();
}

if($action=="report_generate_excel_only")
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_knitting_company=str_replace("'","",$cbo_knitting_company);
	$cbo_location_name=str_replace("'","",$cbo_location_name);
	$cbo_delivery_floor=str_replace("'","",$cbo_del_floor);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$hide_order_id=str_replace("'","",$hide_order_id);
	$txt_barcode_no=str_replace("'","",$txt_barcode_no);
	$txt_program_no=str_replace("'","",$txt_program_no);
	$txt_date_from_qc=str_replace("'","",$txt_date_from_qc);
	$txt_date_to_qc=str_replace("'","",$txt_date_to_qc);
	$cbo_roll_status=str_replace("'","",$cbo_roll_status);
	$cbo_date_range_type=str_replace("'","",$cbo_date_range_type);
	
	if($cbo_knitting_source>0){$knitting_source_cond="and a.knitting_source=$cbo_knitting_source";}else{$knitting_source_cond="";}
	if($cbo_delivery_floor>0){$del_floor_cond="and b.floor_id=$cbo_delivery_floor";}else{$del_floor_cond="";}
	if($hide_order_id!=""){$hide_order_cond="and c.po_breakdown_id in($hide_order_id)";}else{$hide_order_cond="";}
	if($cbo_buyer_name>0){$buyer_name_cond="and a.buyer_id =$cbo_buyer_name";}else{$buyer_name_cond="";}
	if($cbo_company_name>0){$company_name_cond="and a.company_id =$cbo_company_name";}else{$company_name_cond="";}

	$job_no_cond="and c.po_breakdown_id="."'".$orderID."'";

	$user_arr = return_library_array("select user_name, id  from user_passwd where is_deleted=0 and status_active=1", 'id', 'user_name');

	if($txt_job_no!="")
	{
		$jobs=trim($txt_job_no);
		$txt_jobNo="'".$jobs."'";
		// change this query
		//$po_id_arr = return_library_array("select job_no_mst, id from wo_po_break_down where job_no_mst=$txt_jobNo", 'job_no_mst', 'id');
		
		
		$sqls=sql_select("select job_no_mst, id from wo_po_break_down where job_no_mst=$txt_jobNo");
		//$orderID="";
		foreach($sqls as $k=>$val)
		{
			//$j=$val[csf("job_no_mst")];
			//$job="'".$j."'";
			//$orderID=$po_id_arr[$job].=$val[csf("id")].'';
			$poId_arr[$val[csf("id")]]=$val[csf("id")];
		}
		//$job_no_cond="and c.po_breakdown_id="."'".$orderID."'";
		$all_po_id=implode(",", $poId_arr);
		$job_no_cond="and c.po_breakdown_id in($all_po_id)";
		//$job_no_cond=$orderID;
	}
	else
	{
		$job_no_cond="";
	}

  	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$str_cond_date="and a.receive_date between '$txt_date_from' and  '$txt_date_to ' ";
	}
	else
	{
		$str_cond_date="";
	}
	if($txt_date_from_qc!="" && $txt_date_to_qc!="")
	{
		$qc_date_cond="and d.qc_date between '$txt_date_from_qc' and  '$txt_date_to_qc ' ";
	}
	else
	{
		$qc_date_cond="";
	}
	if($cbo_knitting_company>0)
	{
		 $knit_comp_cond="and a.knitting_company='$cbo_knitting_company' ";
	}
	else
	{
		 $knit_comp_cond="";
	}
	if($cbo_location_name>0)
	{
		 $del_location_cond="and a.location_id='$cbo_location_name' ";
	}
	else
	{
		 $del_location_cond="";
	} 
	
	if($cbo_delivery_floor>0)
	{
		 $del_floor_cond="and b.delivery_floor_id='$cbo_delivery_floor' ";
	}
	else
	{
		 $del_floor_cond="";
	} 
	
	if($txt_barcode_no!="")
	{
		$barcode_cond="and c.barcode_no='$txt_barcode_no'";
	}
	else
	{
		$barcode_cond="";
	}
	if($txt_program_no!="")
	{
		$program_cond="and a.booking_no='$txt_program_no'";
	}
	else
	{
		$program_cond="";
	}
	
	if($txt_booking_no!="")
	{
		$booking_cond=" and a.booking_without_order=1 and a.booking_no like '%$txt_booking_no%'";
	}
	else
	{
		$booking_cond="";
	}
	
	$details_report="";
	$master_data=array();
	$current_date=date("Y-m-d");
	$date=date("Y-m-d");$break_id=0;$sc_lc_id=0;
	$sy = date('Y',strtotime($txt_date_from));
	
	//ob_start();
	
		$html .= '<table style="width:1200px" border="0">
	        <tr class="form_caption" style="border:none;"> 
	            <td colspan="12" align="center" style="font-size:16px; font-weight:bold">Daily Roll wise Knitting QC Report</td>
	        </tr>
	        
	        <tr>
	            <td colspan="12" align="center" font-size:14px;">
	               <b>'.$company_library[str_replace("'","",$cbo_company_name)].'</b>                    
	            </td>
	        </tr>
	        <tr>
	            <td colspan="12" align="center" font-size:14px;">
	               <b>'.show_company($cbo_knitting_company,'','').'</b>                    
	            </td>
	        </tr>
    	</table>';

		$html .='<table width="1000" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left;">
			<thead>
				<tr>
					<th width="12">SL</th>
					<th width="12">Date</th>
					<th width="12">Knitting Company</th>
					<th width="12">Barcode</th>
					<th width="12">MC NO</th>
					<th width="12">SHIFT</th>
					<th width="12">BUYER</th>
					<th width="12">ORDER NO</th>
					<th width="12">PROGRAM NO</th>
					<th width="12">ROLL WEIGHT</th>
					<th width="12">YARN COUNT</th>
					<th width="12">Fabric Color</th>
					<th width="12">SUPPLIER</th>
					<th width="12">YARN LOT</th>
					<th width="12">M/C DIA</th>
					<th width="12">Finish Dia</th>
					<th width="12">Stitch Length</th>
					<th width="12">GAUGE</th>
					<th width="12">FABRIC TYPE</th>
					<th width="12">REQ.GSM</th>
					<th width="12">Qc Pass Qty</th>
					<th width="12">HOLE</th>
					<th width="12">LOOP</th>
					<th width="12">MISS YARN</th>
					<th width="12">LYCRA OUT</th>
					<th width="12">LYCRA DROP</th>
					<th width="12">OIL SPOT</th>
					<th width="12">FLY CONTA</th>
					<th width="12">SLUB</th>
					<th width="12">NEEDLE BREAK</th>
					<th width="12">WHEEL FREE</th>
					<th width="12">COUNT MIX</th>
					<th width="12">YARN CONTRA</th>
					<th width="12">SET UP</th>
					<th width="12">PIN HOLE</th>
					<th width="12">TTL POINTS</th>
					<th width="12">GRADE</th>
					<th width="12">DEFECT %</th>
					<th width="12">LENGTH YDS</th>
					<th width="12">Reject Qty</th>
					<th width="12">Operator Name</th>
					<th width="12">QC Name</th>
					<th>REMARKS</th>
				</tr>
			</thead>
		</table>';

		$html .='<table width="3727" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show2" style="float:left;">
			<tbody>';
				$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
				$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
				$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");				
				$yarn_count_lib_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
				$operator_lib_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
				$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
				$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
				
				//$non_booking_arr=return_library_array( "select  id,booking_no  from  wo_non_ord_samp_booking_mst ", "id", "booking_no"  );
				
				$knitting_company_library = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");

				$sql_grey_prod_entry="SELECT a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group, b.id as pro_dtls_id,b.machine_no_id,b.no_of_roll,b.shift_name,b.yarn_lot,b.yarn_count,b.color_id,b.color_range_id,b.machine_dia,b.stitch_length,b.machine_gg,b.gsm,c.po_breakdown_id as order_id,b.prod_id,b.width,b.operator_name,b.febric_description_id,b.rate,b.yarn_prod_id, c.barcode_no, c.id as roll_id, c.roll_no, sum(c.qnty) as roll_weight, sum(c.qnty) as qc_pass_qnty 
				from inv_receive_master a,pro_grey_prod_entry_dtls b,pro_roll_details c
				where  a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $knit_comp_cond $company_name_cond $del_floor_cond $job_no_cond $hide_order_cond $knitting_source_cond $year_cond $del_location_cond $buyer_name_cond $str_cond_date  $barcode_cond $booking_cond $program_cond
				group by a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group, b.id,b.machine_no_id,b.no_of_roll,b.shift_name,b.yarn_lot,b.yarn_count,b.color_id,b.color_range_id,b.machine_dia, b.stitch_length,b.machine_gg,b.gsm,c.po_breakdown_id,b.prod_id,b.width,b.operator_name,b.febric_description_id,b.rate,b.yarn_prod_id, c.barcode_no, c.id, c.roll_no order by c.barcode_no";
				// echo $sql_grey_prod_entry;
				$sql_qry_prod_entry=sql_select($sql_grey_prod_entry);					
				
				$i=1;
				foreach($sql_qry_prod_entry as $row)
				{
					$pro_dtls_id_arr[$row[csf("pro_dtls_id")]]=$row[csf("pro_dtls_id")];
					$order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
					$booking_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
					if(trim($row[csf("color_id")])){
					$color_id_arr[$row[csf("color_id")]]=chop($row[csf("color_id")],",");
					}
					$prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
					$febric_description_id_arr[$row[csf("febric_description_id")]]=$row[csf("febric_description_id")];
				}

				$all_po_ids= array_chunk($order_id_arr, 999);
				$all_booking_ids= array_chunk($booking_id_arr, 999);
				$pro_dtls_ids= array_chunk($pro_dtls_id_arr, 999);
				$color_Ids= array_chunk($color_id_arr, 800);
				$all_prod_ids= array_chunk($prod_id_arr, 999);
				$all_febric_description_ids= array_chunk($febric_description_id_arr, 999);


				$color_Ids_cond=" and(";
				foreach($color_Ids as $colorIds)
				{
					if($color_Ids_cond==" and(") $color_Ids_cond.=" id in(". implode(',', $colorIds).")"; else $color_Ids_cond.="  or id in(". implode(',', $colorIds).")";
				}
				$color_Ids_cond.=")";

				$all_prod_ids_cond=" and(";
				foreach($all_prod_ids as $prod_ids)
				{
					if($all_prod_ids_cond==" and(") $all_prod_ids_cond.=" a.id in(". implode(',', $prod_ids).")"; else $all_prod_ids_cond.="  or a.id in(". implode(',', $prod_ids).")";
				}
				$all_prod_ids_cond.=")";

				$all_febric_description_cond=" and(";
				foreach($all_prod_ids as $description_ids)
				{
					if($all_febric_description_cond==" and(") $all_febric_description_cond.=" a.id in(". implode(',', $description_ids).")"; else $all_febric_description_cond.="  or a.id in(". implode(',', $description_ids).")";
				}
				$all_febric_description_cond.=")";


				$po_ids_cond=" and("; $order_ids_cond=" and(";
				foreach($all_po_ids as $po_ids)
				{
					if($po_ids_cond==" and(") $po_ids_cond.=" a.id in(". implode(',', $po_ids).")"; else $po_ids_cond.="  or a.id in(". implode(',', $po_ids).")";


					if($order_ids_cond==" and(") $order_ids_cond.=" b.order_id in(". implode(',', $po_ids).")"; else $order_ids_cond.="  or b.order_id in(". implode(',', $po_ids).")";

				}
				$po_ids_cond.=")";
				$order_ids_cond.=")";

				$booking_ids_cond=" and(";$booking_ids_cond_2=" and(";
				foreach($all_booking_ids as $booking_ids)
				{
					if($booking_ids_cond==" and(") $booking_ids_cond.=" a.id in(". implode(',', $booking_ids).")"; else $booking_ids_cond.="  or a.id in(". implode(',', $booking_ids).")";
					if($booking_ids_cond_2==" and(") $booking_ids_cond_2.=" a.dtls_id in(". implode(',', $booking_ids).")"; else $booking_ids_cond_2.="  or a.dtls_id in(". implode(',', $booking_ids).")";


				}
				$booking_ids_cond.=")";
				$booking_ids_cond_2.=")";

				$pro_dtls_ids_cond=" and(";
				foreach($pro_dtls_ids as $pro_dtls_ids)
				{
					if($pro_dtls_ids_cond==" and(") $pro_dtls_ids_cond.=" d.pro_dtls_id in(". implode(',', $pro_dtls_ids).")"; else $pro_dtls_ids_cond.="  or d.pro_dtls_id in(". implode(',', $pro_dtls_ids).")";
				}
				$pro_dtls_ids_cond.=")";

				//echo $pro_dtls_ids_cond;die;
				if(!empty($color_Ids)){
					$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 $color_Ids_cond", 'id', 'color_name');
				}
				if(!empty($all_prod_ids)){
					$fabric_desc_arr = return_library_array("select a.id, a.item_description from product_details_master a where a.item_category_id=13 $all_prod_ids_cond", "id", "item_description");
				}

				$sql_pro_grey_prod_del = sql_select("select a.id, a.sys_number, a.delevery_date,b.product_id,b.order_id,b.barcode_num from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.entry_form=56 and a.status_active=1 and a.is_deleted=0 $knit_comp_cond $knitting_source_cond $buyer_name_cond $del_location_cond $order_ids_cond order by id");
				$pro_grey_arr_data=array();
				foreach($sql_pro_grey_prod_del as $row_prod)
				{
					$pro_grey_arr_data[$row_prod[csf("order_id")]][$row_prod[csf("product_id")]][$row_prod[csf("barcode_num")]]["sys_number"]=$row_prod[csf("sys_number")];
					$pro_grey_arr_data[$row_prod[csf("order_id")]][$row_prod[csf("product_id")]][$row_prod[csf("barcode_num")]]["delevery_date"]=$row_prod[csf("delevery_date")];
				}
				

				$composition_arr = array();
				$composition_arr_new = array();
				if(!empty($all_prod_ids)){
					$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
				}
				$data_array = sql_select($sql_deter);
				if (count($data_array) > 0) {
					foreach ($data_array as $row) {
						if (array_key_exists($row[csf('id')], $composition_arr)) {
							$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
							$composition_arr_new[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";

						} else {
							//$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
							$composition_arr[$row[csf('id')]] = $row[csf('construction')];
							$composition_arr_new[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";

						}
					}
				}
				if(!empty($all_prod_ids)){
					$sql_supplier = sql_select("select a.id as product_id,a.supplier_id,a.lot,a.yarn_type,a.yarn_comp_type1st,a.yarn_comp_percent1st,yarn_count_id from product_details_master a where item_category_id=1 $company_name_cond ");
				}
				//$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%";
				$supplier_arr_data=array();
				$yarn_composition_arr_data=array();
				foreach($sql_supplier as $row_spl)
				{
					$supplier_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["supplier_id"]=$row_spl[csf("supplier_id")];
					$supplier_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["yarn_type"]=$row_spl[csf("yarn_type")];

					$yarn_composition_arr_data[$row_spl[csf("yarn_count_id")]][$row_spl[csf("product_id")]]["yarn_conmposition"]=$composition[$row_spl[csf('yarn_comp_type1st')]] . " " . $row_spl[csf('yarn_comp_percent1st')] . "%";

					$yarn_composition_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["yarn_conmposition_lot"]=$composition[$row_spl[csf('yarn_comp_type1st')]] . " " . $row_spl[csf('yarn_comp_percent1st')] . "%";
				}
				//print_r($yarn_composition_arr_data);die;
				

				$style_ref_arr=array();
				if(!empty($all_po_ids)){
					$style_ref_query = "select a.id,b.style_ref_no,a.job_no_mst,a.po_number from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $po_ids_cond  and a.is_deleted=0 and a.status_active=1";//die;
				}
				$sql_style_ref=sql_select($style_ref_query);
				foreach($sql_style_ref as $rows)
				{
					$style_ref_arr[$rows[csf("id")]]["style_ref_no"]=$rows[csf("style_ref_no")];
					$job_no_arr[$rows[csf("id")]]["job_no_mst"]=$rows[csf("job_no_mst")];
					$po_number_arr[$rows[csf("id")]]=$rows[csf("po_number")];
				}
		
				// mark
				if(!empty($all_booking_ids)){
					$sql_booking_query = "select a.booking_type, a.booking_no, a.is_short from wo_booking_mst a where status_active=1 $booking_ids_cond ";//die;
				}
				$booking_type_result=sql_select($sql_booking_query);
				foreach($booking_type_result as $rows)
				{
					$booking_type_arr[$rows[csf("booking_no")]]=$rows[csf("booking_type")];
					$booking_is_short_arr[$rows[csf("booking_no")]]=$rows[csf("is_short")];
					
				}

				if(!empty($all_booking_ids)){
					$non_order_style_id = return_library_array("select a.id,b.style_id from wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a where a.status_active=1 and a.booking_no=b.booking_no  $booking_ids_cond", 'id', 'style_id');
				}

				if(!empty($all_booking_ids)){
					$booking_no_disp_arr = return_library_array("select a.dtls_id,a.booking_no from ppl_planning_entry_plan_dtls a where a.status_active=1  $booking_ids_cond_2", 'dtls_id', 'booking_no');
				}

				//print_r($booking_is_short_arr);die;
				$sql_qc_dtls="SELECT d.id, d.pro_dtls_id, d.roll_maintain, d.barcode_no, d.roll_id, d.roll_no, d.qc_name, d.roll_status, d.roll_width, d.roll_weight, d.roll_length, d.reject_qnty, d.qc_date, d.total_penalty_point, d.total_point,d.fabric_grade, d.comments,e.defect_name,e.defect_count,e.found_in_inch,e.penalty_point,
				case when e.defect_name ='1'  then e.defect_count else 0 end as hole_defect_count,
				case when e.defect_name ='5'  then e.defect_count else 0 end as loop_defect_count ,
				case when e.defect_name ='10'  then e.defect_count else 0 end as press_defect_count ,
				case when e.defect_name ='15'  then e.defect_count else 0 end as lycraout_defect_count ,
				case when e.defect_name ='20'  then e.defect_count else 0 end as lycradrop_defect_count ,
				case when e.defect_name ='25'  then e.defect_count else 0 end as dust_defect_count ,
				case when e.defect_name ='30'  then e.defect_count else 0 end as oilspot_defect_count ,
				case when e.defect_name ='35'  then e.defect_count else 0 end as flyconta_defect_count ,
				case when e.defect_name ='40'  then e.defect_count else 0 end as slub_defect_count ,
				case when e.defect_name ='45'  then e.defect_count else 0 end as patta_defect_count ,
				case when e.defect_name ='50'  then e.defect_count else 0 end as neddle_defect_count ,
				case when e.defect_name ='55'  then e.defect_count else 0 end as sinker_defect_count ,
				case when e.defect_name ='60'  then e.defect_count else 0 end as wheel_defect_count ,
				case when e.defect_name ='65'  then e.defect_count else 0 end as count_defect_count ,
				case when e.defect_name ='70'  then e.defect_count else 0 end as yarn_defect_count ,
				case when e.defect_name ='75'  then e.defect_count else 0 end as neps_defect_count ,
				case when e.defect_name ='80'  then e.defect_count else 0 end as black_defect_count ,
				case when e.defect_name ='85'  then e.defect_count else 0 end as oilink_defect_count ,
				case when e.defect_name ='90'  then e.defect_count else 0 end as setup_defect_count,
				case when e.defect_name ='95'  then e.defect_count else 0 end as pin_hole_defect_count,
				case when e.defect_name ='100'  then e.defect_count else 0 end as slub_hole_defect_count,
				case when e.defect_name ='105'  then e.defect_count else 0 end as needle_mark_defect_count, 
				case when e.defect_name ='110'  then e.defect_count else 0 end as miss_yarn_defect_count 
				from pro_qc_result_mst d,pro_qc_result_dtls e  
				where d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0 $pro_dtls_ids_cond order by d.barcode_no";
				//echo $sql_qc_dtls;die; 

				$sql_qc_dtls_data=sql_select($sql_qc_dtls); 
				//print_r($sql_qc_dtls_data);die("with jj");
				$sql_qc_data_arr=array(); 
				foreach($sql_qc_dtls_data as $dataRow)
				{
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["hole_defect_count"]+=$dataRow[csf("hole_defect_count")];
					//print_r($sql_qc_data_arr);die("with sumon");
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["loop_defect_count"]+=$dataRow[csf("loop_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["press_defect_count"]+=$dataRow[csf("press_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["lycraout_defect_count"]+=$dataRow[csf("lycraout_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["lycradrop_defect_count"]+=$dataRow[csf("lycradrop_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["dust_defect_count"]+=$dataRow[csf("dust_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["oilspot_defect_count"]+=$dataRow[csf("oilspot_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["flyconta_defect_count"]+=$dataRow[csf("flyconta_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["slub_defect_count"]+=$dataRow[csf("slub_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["patta_defect_count"]+=$dataRow[csf("patta_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["neddle_defect_count"]+=$dataRow[csf("neddle_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["sinker_defect_count"]+=$dataRow[csf("sinker_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["wheel_defect_count"]+=$dataRow[csf("wheel_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["count_defect_count"]+=$dataRow[csf("count_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["yarn_defect_count"]+=$dataRow[csf("yarn_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["neps_defect_count"]+=$dataRow[csf("neps_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["black_defect_count"]+=$dataRow[csf("black_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["oilink_defect_count"]+=$dataRow[csf("oilink_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["setup_defect_count"]+=$dataRow[csf("setup_defect_count")];
					
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["pin_hole_defect_count"]+=$dataRow[csf("pin_hole_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["slub_hole_defect_count"]+=$dataRow[csf("slub_hole_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["needle_mark_defect_count"]+=$dataRow[csf("needle_mark_defect_count")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["miss_yarn_defect_count"]+=$dataRow[csf("miss_yarn_defect_count")];
					
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["comments"]=$dataRow[csf("comments")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["reject_qnty"]=$dataRow[csf("reject_qnty")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["qc_name"]=$dataRow[csf("qc_name")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["fabric_grade"]=$dataRow[csf("fabric_grade")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["roll_length"]=$dataRow[csf("roll_length")];
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["total_point"]=$dataRow[csf("total_point")];
					
					$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["tr_color"]=$dataRow[csf("barcode_no")];
				}

				$i=1;
				foreach($sql_qry_prod_entry as $row)
				{
					//echo $bgcolor;die("sumon");	break;
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "")
					$fabric_desc = $fabric_desc_arr[$row[csf('prod_id')]];
					else
					$fabric_desc = $composition_arr[$row[csf('febric_description_id')]];
					$booking_type_string='';	
					if ($row[csf("receive_basis")] == 1 && $row[csf("booking_without_order")] != 1) {
						$booking_no = $row[csf("booking_no")] ;
						$booking_type_id=$booking_type_arr[$booking_no];
						if($booking_type_id==1)
						{
							$is_short=$booking_is_short_arr[$booking_no];
							if($is_short==1)  $booking_type_string="Short Fabric";
							else if($is_short==2)  $booking_type_string="Main Fabric";
						}
						
						else if($booking_type_id==4) $booking_type_string="Sample Booking";
						//echo "set_auto_complete(2);\n";
					}
					else if ($row[csf("receive_basis")] == 1 && $row[csf("booking_without_order")] == 1) 
					{
						$booking_type_string="Sample Without Order";
						$booking_no = $row[csf("booking_no")] ;
					}
					else if ($row[csf("receive_basis")] == 2) {

					$booking_no=$booking_no_disp_arr[$row[csf("booking_id")]];
					$booking_type_id=$booking_type_arr[$booking_no];
					if($booking_type_id==1)
					{
						$is_short=$booking_is_short_arr[$booking_no];
						if($is_short==1)  $booking_type_string="Short Fabric";
						else if($is_short==2)  $booking_type_string="Main Fabric";
					}
					else if($booking_type_id==4) $booking_type_string="Sample Booking";
					else $booking_type_string="Sample Without Order";
					
					//echo "set_auto_complete(2);\n";
					} else {
						//echo "set_auto_complete(1);\n";
						$booking_no = '';
					}
	
					if($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["tr_color"]!="")
					{
						$bgcolor='#3CB371';
					}
						$lot_arr = array_unique(explode(",", $row[csf("yarn_lot")]));
						$yarn_prod_arr = explode(',',$row[csf("yarn_prod_id")]);
					$html .='<tr id="tr_'. $i.'">
						<td>'.$i.'</td>
						<td>'.change_date_format($row[csf("receive_date")]).'</td>						
						<td>';
						if($row[csf("knitting_source")]==1){ $html .= $company_library[$row[csf("knitting_company")]]; }else{ $html .= $knitting_company_library[$row[csf("knitting_company")]]; }
					$html .= '</td>
						<td>'.$row[csf("barcode_no")].'</td>
						<td>'.$machine_arr[$row[csf("machine_no_id")]].'</td>
						<td>'.$shift_name[$row[csf("shift_name")]].'</td>
						<td>'.$buyer_library[$row[csf("buyer_id")]].'</td>';

						if($row[csf("booking_without_order")]==1)
						{
							$html .= '<td></td>';
						}
						else
						{
							$html .= '<td>'.$po_number_arr[$row[csf("order_id")]].'</td>';
						}

						$html .= '<td>';
						if($row[csf("receive_basis")]==2) 
						{
							$html .= $row[csf("booking_no")];
						}
						$html .= '</td>';
						$html .= '<td>'.$row[csf("roll_weight")].'</td>
						<td>';
						$yarnCount="";
						$yarnCountID= array_unique(explode(",", $row[csf("yarn_count")]));
						foreach ($yarnCountID as $yarnCountID) 
						{
							$yarnCount .= $yarn_count_lib_arr[$yarnCountID]. ",";
						}
						$html .= chop($yarnCount,",")
						//echo $yarn_count_lib_arr[$row[csf("yarn_count")]]; 
						.'</td>
						<td>';
						$colorName="";
						$colorID_arr = array_unique(explode(",", $row[csf("color_id")]));
						foreach ($colorID_arr as $colID) 
						{
							$colorName .= $color_arr[$colID] . ",";
						}
						$html .= chop($colorName,",")
						//echo $row[csf("color_id")];//echo $color_arr[$row[csf("color_id")]];
						.'</td>
						<td>';
						$supplierName="";
						foreach ($lot_arr as $lotNo) {
							foreach($yarn_prod_arr as $yearProdId)
							{
								if($supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]]!="")
								{
									if($supplierName!="")
									{
										$supplierName .= ",".$supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]];
									}else{
										$supplierName = $supplier_arr[$supplier_arr_data[$lotNo][$yearProdId]["supplier_id"]];
									}
								}
							}
						}
						$html .= $supplierName
						.'</td>
						<td>';
							$yarnLot="";
							
							foreach ($lot_arr as $yarnLotID) {
								$yarnLot .= $yarnLotID. ", ";
							}
								$html .= chop($yarnLot,', ')
						.'</td>						
						<td>'.$row[csf("machine_dia")].'</td>
						<td>'.$row[csf("width")].'</td>
						<td>'.$row[csf("stitch_length")].'</td>
						<td>'.$row[csf("machine_gg")].'</td>
						<td>'.$fabric_desc.'</td>
						<td>'.$row[csf("gsm")].'</td>
						<td>'.$row[csf("qc_pass_qnty")].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["hole_defect_count"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["loop_defect_count"].'</td>
						<td>'. $sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["miss_yarn_defect_count"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycraout_defect_count"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycradrop_defect_count"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilspot_defect_count"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["flyconta_defect_count"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_defect_count"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neddle_defect_count"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["wheel_defect_count"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["count_defect_count"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["yarn_defect_count"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["setup_defect_count"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["pin_hole_defect_count"].'</td>
						<td>'.number_format($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"],2).'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["fabric_grade"].'</td>';
						$defect_percent=($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"]*36*100)/($row[csf("width")]*$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"]);
						$html .='<td>'.number_format($defect_percent,2).'%</td>
						<td>'.number_format($sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["roll_length"],2).'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["reject_qnty"].'</td>
						<td>'.$operator_lib_arr[$row[csf("operator_name")]].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["qc_name"].'</td>
						<td>'.$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["comments"].'</td>
					</tr>';
					
					$total_qc_pass_qty+=$row[csf("qc_pass_qnty")];
					$total_hole_defect+=			$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["hole_defect_count"];					
					$total_loop_defect+=			$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["loop_defect_count"];
					$total_miss_yarn_defect+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["miss_yarn_defect_count"];					
					$total_press_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["press_defect_count"];
					$total_lycraout_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycraout_defect_count"];
					$total_lycradrop_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["lycradrop_defect_count"];
					$total_dust_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["dust_defect_count"];
					$total_oilspot_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilspot_defect_count"];
					$total_flyconta_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["flyconta_defect_count"];
					$total_slub_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_defect_count"];
					$total_patta_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["patta_defect_count"];
					$total_neddle_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neddle_defect_count"];
					$total_sinker_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["sinker_defect_count"];
					$total_wheel_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["wheel_defect_count"];
					$total_count_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["count_defect_count"];
					$total_yarn_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["yarn_defect_count"];
					$total_neps_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["neps_defect_count"];
					$total_black_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["black_defect_count"];
					$total_oilink_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["oilink_defect_count"];
					$total_setup_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["setup_defect_count"];					
					$total_pin_hole_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["pin_hole_defect_count"];
					$total_slub_hole_defect_count+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["slub_hole_defect_count"];
					$total_needle_mark_defect_count+=	$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["needle_mark_defect_count"];					
					$total_totalDefect_point+=		$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["total_point"];
					$total_reject_qty+=				$sql_qc_data_arr[$row[csf("pro_dtls_id")]][$row[csf("barcode_no")]]["reject_qnty"];
					$total_taka+=					$row[csf("qc_pass_qnty")]*$row[csf("rate")];
					$i++;
				}
			$html .='</tbody>
		   	<tfoot>
				<tr>
					<td colspan="20" align="right"><strong>Total</strong></td>
					<td>'.$total_qc_pass_qty.'</td>
					<td>'.$total_hole_defect.'</td>					
					<td>'.$total_loop_defect.'</td>
					<td>'.$total_miss_yarn_defect.'</td>					
					<td>'.$total_lycraout_defect_count.'</td>
					<td>'.$total_lycradrop_defect_count.'</td>
					<td>'.$total_oilspot_defect_count.'</td>
					<td>'.$total_flyconta_defect_count.'</td>
					<td>'.$total_slub_defect_count.'</td>
					<td>'.$total_neddle_defect_count.'</td>
					<td>'.$total_wheel_defect_count.'</td>
					<td>'.$total_count_defect_count.'</td>
					<td>'.$total_yarn_defect_count.'</td>
					<td>'.$total_setup_defect_count.'</td>					
					<td>'.$total_pin_hole_defect_count.'</td>					
					<td>'.number_format($total_totalDefect_point,2).'</td>
					<td></td>
					<td></td>
					<td></td>
					<td>'.$total_reject_qty.'</td>
					<td colspan="3"></td>
				</tr>
			</tfoot>
		</table>';

	foreach (glob("QCR_$user_id*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename='QCR_'.$user_id."_".$name.".xls";
	//echo "$html####$filename"; die;
	//echo $filename;die;
	
	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
	$spreadsheet = $reader->loadFromString($html);

	$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
	$writer->save($filename);
	header('Content-Type: application/x-www-form-urlencoded');
	header('Content-Transfer-Encoding: Binary');
	header("Content-disposition: attachment; filename=\"".$filename."\"");
	echo "$filename####$filename"; 
	exit();
	// ======================================================
	/*foreach (glob("GFSR_*.xls") as $filename) {
        @unlink($filename);
    }
    $name=time();
    $filename="QCR_".$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$filename####$filename";
    exit();*/
}

if($action=="booking_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
	function js_set_value(str)
	{
		$("#hide_booking_no").val(str);
		parent.emailwindow.hide(); 
	}
	</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:980px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th width="100">Booking No</th>
                    <th width="80">Style Desc.</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 				
                    <input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_style_desc" id="txt_style_desc" class="text_boxes" style="width:80px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                          <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                         </td> 	
                         <td align="center">
                 			<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_style_desc').value, 'create_booking_search_list_view', 'search_div', 'daily_roll_wise_knitting_qc_report_controller','setFilterGrid(\'table_body_booking\',1)')" style="width:100px;" />              
                        </td>
                    </tr>
                    <tr>
                        <td  align="center" height="40" valign="middle" colspan="6">
                        <? 
                        echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                        ?>
                        <? echo load_month_buttons();  ?>
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

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$style_desc=$data[7];
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if ($data[1]!=0){$buyer=" and a.buyer_id='$data[1]'";}
	else{$buyer="";}
	
	if($db_type==0)
	 {
		  // $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		  $booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' 
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date =""; 
     }
	if($db_type==2)
	 {
		  $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	 }
	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]%' "; else $style_des_cond="";
	}
 
	
	

	/*$po_array=array();
	$sql_po= sql_select("select a.booking_no_prefix_num, a.booking_no,a.po_break_down_id from wo_non_ord_samp_booking_mst a  where $company $buyer $booking_date and booking_type=4  and   status_active=1  and 	is_deleted=0 order by booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$po_number[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}*/
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
    $approved=array(0=>"No",1=>"Yes");
    $is_ready=array(0=>"No",1=>"Yes",2=>"No"); 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$item_category,5=>$fabric_source,6=>$suplier,7=>$style_library,9=>$approved,10=>$is_ready);
	 $sql= "select a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode,b.style_id,b.style_des from wo_non_ord_samp_booking_mst  a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no  and b.status_active=1 and b.is_deleted=0  where   $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond $style_des_cond and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0 group by a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode,b.style_id,b.style_des order by booking_no"; 
	//echo $sql;
	//echo create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Fabric Nature,Fabric Source,Supplier,Style,Style Desc.,Approved,Is-Ready", "100,80,100,100,80,80,80,50,80,50","950","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,item_category,fabric_source,supplier_id,style_id,0,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,item_category,fabric_source,supplier_id,style_id,style_des,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0,0,0','','');
	?>
   <table class="rpt_table scroll" width="970" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
       <thead>
            <th width="40">Sl</th> 
            <th width="80">Booking No</th>  
            <th width="80">Booking Date</th>           	 
            <th width="100">Buyer</th>
            <th width="120">Fabric Nature</th>
            <th width="80">Fabric Source</th>
            <th width="80">Pay Mode</th>
            <th width="100">Supplier</th>
            <th width="80">Style</th>
            <th width="200">Style Desc.</th>
        </thead>
     </table>
		<div style="width:970px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="952" class="rpt_table" id="table_body_booking">
                <tbody>
                    <? 
                    $i=1;
                    $sql_data=sql_select($sql);
                    foreach($sql_data as $row){
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";    
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('booking_no')]  ?>')" style="cursor:pointer">
                        <td width="40"><? echo $i;?></td> 
                        <td width="80"><? echo $row[csf('booking_no_prefix_num')];?></td>  
                        <td width="80"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>           	 
                        <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
                        <td width="120"><? echo $item_category[$row[csf('item_category')]];?></td>
                        <td width="80"><? echo $fabric_source[$row[csf('fabric_source')]];?></td>
                        <td width="80">
                        <? echo $pay_mode[$row[csf('pay_mode')]];?>
                        </td>
                        <td width="100">
                        <? 
                        if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){
                            echo $comp[$row[csf('supplier_id')]];
                        }
                        else{
                            echo $suplier[$row[csf('supplier_id')]];
                        }
                        ?>
                        </td>
                        <td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $style_library[$row[csf('style_id')]];?></td>
                        <td width="" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('style_des')];?></td>

                    </tr>
                    <?
                    $i++;
                     }
                    ?>
                </tbody>
            </table>
        </div>
    <?
}
if($action=="job_popup")
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
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'daily_roll_wise_knitting_qc_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
if ($action=="job_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);

	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)=$cbo_year";
			$year_field="YEAR(insert_date)";
		}
		else
		{
			$year_cond=" and to_char(insert_date,'YYYY')=$cbo_year";	
			$year_field="to_char(insert_date,'YYYY')";
		}
	}
	else $year_cond="";

	$arr=array (2=>$company_library,3=>$buyer_arr);
	$sql= "select a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year from wo_po_details_master a where a.company_name=$company_id $buyer_cond $year_cond $search_con order by a.id DESC";
	//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","230",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}
disconnect($con);
?>
