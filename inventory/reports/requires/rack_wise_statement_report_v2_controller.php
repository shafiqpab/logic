<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.yarns.php');

include ("../../../ext_resource/excel/excel/vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

$user_id=$_SESSION['logic_erp']['user_id'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

	$select_year="to_char";
    $year_con=",'YYYY'";
	
	
	

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 80, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}
if($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 80, "select id, location_name from lib_location where status_active =1 and is_deleted=0 and company_id=$data order by location_name","id,location_name", 1, "--Select Location--", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 70, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type in(4) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"load_drop_down('requires/rack_wise_statement_report_v2_controller', this.value+'_'+$data, 'load_drop_floor','floor_td');");
	exit();
}

if($action == "load_drop_floor")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_floor", 80, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "load_drop_down('requires/rack_wise_statement_report_v2_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_room','room_td');",0 );
	exit();
}

if($action == "load_drop_room")
{
	$data = explode("_", $data);
	$floor_id=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$room_id = $data[3];

	echo create_drop_down( "cbo_room", 80, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.floor_id='$floor_id' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", $room_id, "load_drop_down('requires/rack_wise_statement_report_v2_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_rack','rack_td');",0 );
	exit();
}

if($action == "load_drop_rack")
{
	$data = explode("_", $data);
	$room_id=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$rack_id = $data[3];
	echo create_drop_down( "txt_rack", 80, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.room_id='$room_id' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", $rack_id, "load_drop_down('requires/rack_wise_statement_report_v2_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_shelf','shelf_td');",0 );
	exit();
}

if($action == "load_drop_shelf")
{
	$data = explode("_", $data);
	$rack=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$shelf_id = $data[3];
	echo create_drop_down( "txt_shelf", 80, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.rack_id='$rack' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", $shelf_id, "load_drop_down('requires/rack_wise_statement_report_v2_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_bin','bin_td');storeUpdateUptoDisable();",0 );
	exit();
}

if($action == "load_drop_bin")
{
	$data = explode("_", $data);
	$shelf=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$bin_id = $data[3];
	echo create_drop_down( "cbo_bin", 80, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.shelf_id=$shelf and b.store_id='$store_id' and a.company_id='$company_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select Bin--", $bin_id, "",0 );
	exit();
}

if ($action=="load_drop_down_supplier")
{
    echo create_drop_down( "cbo_suppler_name", 100, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b  where a.id=b.supplier_id and b.tag_company='$data' and a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", 0, "" );
    exit();
}

if($action == "item_group_popup")
{
	echo load_html_head_contents("Composition Info","../../../", 1, 1, '','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
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

		function set_all()
		{
			var old=document.getElementById('selected_ids').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
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

			$('#selected_id').val(id);
			$('#selected_name').val(name);
		}
	</script>
	</head>
	<fieldset style="width:390px">
		<legend>Item Details</legend>
		<input type="hidden" name="selected_name" id="selected_name" value="">
		<input type="hidden" name="selected_id" id="selected_id" value="">
		<table width="390" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="150">Item Category</th>
					<th width="">Item Group Name</th>
				</tr>
			</thead>
		</table>
		<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
				$i = 1;
				$cbo_item_group=str_replace("'","",$cbo_item_group);
				$cbo_year_selection=str_replace("'","",$cbo_year_selection);

				if($db_type==0) { $year_cond=" and YEAR(insert_date)=$cbo_year_selection";   }
				if($db_type==2) {$year_cond=" and to_char(insert_date,'YYYY')=$cbo_year_selection";}
				
				$sql="select id, item_category, item_name from lib_item_group where status_active=1 and item_category=4 and is_deleted=0";
				// echo $sql;
				$result=sql_select($sql);
				$selected_id_arr=explode(",",$cbo_item_group);
				foreach ($result as $row)
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					if(in_array($row[csf("id")],$selected_id_arr))
					{
						if($selected_ids=="") $selected_ids=$i; else $selected_ids.=",".$i;
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
						<td width="30" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
							<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf("item_name")]; ?>"/>
						</td>
						<td width="150"><p><? echo $item_category[$row[csf("item_category")]]; ?></p></td>
						<td width=""><p><? echo $row[csf("item_name")]; ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
				<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
			</table>
		</div>
		<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	</fieldset>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
		set_all();
	</script>
	<?
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'rack_wise_statement_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_array=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_arr,1=>$buyer_array);

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

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id 	= str_replace("'","",$cbo_company_id);
	$buyer_id 		= str_replace("'","",$cbo_buyer_id);
	$ship_status 	= str_replace("'","",$cbo_shipment_status);
    $job_no 		= str_replace("'","",$txt_job_no);
	$job_id 		= str_replace("'","",$txt_job_id);
	$style_id 		= str_replace("'","",$txt_style_ref_id);
	// $txt_style_ref 		= str_replace("'","",$txt_style_ref);
	$order_no 		= str_replace("'","",$txt_order_no);
	$order_id 		= str_replace("'","",$txt_order_id);
	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);
	$cbo_location_id = str_replace("'","",$cbo_location_id);
	$txt_item_group = str_replace("'","",$txt_item_group);
	$cbo_item_group = str_replace("'","",$cbo_item_group);
	$txt_order 		= str_replace("'","",$txt_order);
	$txt_order_id_no = str_replace("'","",$txt_order_id_no);
	$cbo_store_name = str_replace("'","",$cbo_store_name);
	$cbo_floor 		= str_replace("'","",$cbo_floor);
	$cbo_room 		= str_replace("'","",$cbo_room);
	$txt_rack 		= str_replace("'","",$txt_rack);
	$txt_shelf 		= str_replace("'","",$txt_shelf);
	$cbo_bin 		= str_replace("'","",$cbo_bin);
	$cbo_suppler_name 		= str_replace("'","",$cbo_suppler_name);
	$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);

	$sql_cond = "";$date_cond="";
	if( trim($cbo_store_name)>0 ) $sql_cond .= " and a.store_id='$cbo_store_name'";
	$sql_cond .= ($buyer_id!=0) ? " and d.BUYER_NAME=$buyer_id" : "";
	$sql_cond .= ($ship_status!=0) ? " and c.shiping_status=$ship_status" : "";
	$sql_cond .= ($cbo_store_name!=0) ? " and a.store_id=$cbo_store_name" : "";
	$sql_cond .= ($cbo_floor!=0) ? " and a.floor_id=$cbo_floor" : "";
	$sql_cond .= ($cbo_room!=0) ? " and a.room=$cbo_room" : "";
	$sql_cond .= ($txt_rack!=0) ? " and a.rack=$txt_rack" : "";
	$sql_cond .= ($txt_shelf!=0) ? " and a.self=$txt_shelf" : ""; 
	$sql_cond .= ($cbo_bin!=0) ? " and a.bin_box=$cbo_bin" : "";
	$sql_cond .= ($cbo_location_id!=0) ? " and d.location_name=$cbo_location_id" : "";
	$sql_cond .= ($job_id!="") ? " and d.id in($job_id)" : "";
	$sql_cond .= ($style_id!="") ? " and d.id in($style_id)" : "";
	$sql_cond .= ($order_id!="") ? " and c.id in($order_id)" : "";
	$sql_cond .= ($job_no!="") ? " and d.job_no_prefix_num in($job_no)" : "";
	$sql_cond .= ($cbo_item_group!="") ? " and p.item_group_id in($cbo_item_group)" : "";
	// $sql_cond .= ($order_no!="") ? " and c.po_number in('$order_no')" : "";
	$sql_cond .= ($order_no != "") ? " AND c.po_number LIKE '%$order_no%'" : "";
	$sql_cond .= ($txt_order != "") ? " AND c.GROUPING IN ('" . str_replace(",", "','", $txt_order) . "')" : "";
	if(str_replace("'","",$txt_style_ref)){
		$sql_cond .= ($txt_style_ref!="") ? " and d.style_ref_no in($txt_style_ref)" : "";
	}
	
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$lib_buyer_brand_arr = return_library_array("select id, brand_name from lib_buyer_brand","id","brand_name");
	$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_array=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$lib_floor=return_library_array("select b.floor_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0","floor_id","floor_room_rack_name");

	$lib_room=return_library_array("SELECT b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name","room_id","floor_room_rack_name");

	$lib_rack=return_library_array("SELECT b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc","rack_id","floor_room_rack_name");
	
	$lib_self_no=return_library_array("SELECT b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc","shelf_id","floor_room_rack_name");

	$lib_bin_box=return_library_array("SELECT b.bin_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc","bin_id","floor_room_rack_name");
	
	$item_group_sql=sql_select("select ID, ITEM_NAME, CONVERSION_FACTOR from lib_item_group where status_active=1 and is_deleted=0 and ITEM_CATEGORY=4");
	foreach($item_group_sql as $val)
	{
		$trim_group_library[$val["ID"]]=$val["ITEM_NAME"];
		$conversion_factor_arr[$val["ID"]]=$val["CONVERSION_FACTOR"];
	}
	unset($item_group_sql);
	
	$sql="SELECT A.ID AS TRANS_ID, B.PO_BREAKDOWN_ID AS ORDER_ID, A.TRANSACTION_DATE, A.CONS_UOM, A.STORE_ID, a.FLOOR_ID, a.ROOM, a.RACK, a.SELF, a.BIN_BOX, A.COMPANY_ID, D.JOB_NO, D.JOB_NO_PREFIX_NUM, D.STYLE_REF_NO, d.BUYER_NAME, C.PO_NUMBER, C.SHIPMENT_DATE, B.PROD_ID, A.TRANSACTION_TYPE, A.MST_ID, A.CONS_RATE, A.ORDER_RATE, A.INSERTED_BY, A.RECEIVE_BASIS, a.PI_WO_BATCH_NO, A.ORDER_QNTY, P.ITEM_DESCRIPTION as PRODUCT_NAME_DETAILS, P.COLOR, P.ITEM_COLOR, P.GMTS_SIZE, P.ITEM_SIZE, P.ITEM_GROUP_ID, p.UNIT_OF_MEASURE as UOM, c.GROUPING, d.BRAND_ID, d.season_buyer_wise as SEASON, d.SEASON_YEAR, c.SHIPING_STATUS, c.ID as BREAK_DOWN_ID, b.QUANTITY as CONS_QUANTITY
	from inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d, product_details_master p
	where a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_id=d.id and p.id=a.prod_id AND a.transaction_date <='" . $date_to. "' and a.item_category=4 and A.COMPANY_ID=$company_id  $sql_cond and b.trans_type in(1,2,3,4,5,6) and b.entry_form in(24,25,49,73,78,112) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0 and p.is_deleted=0
	order by P.ITEM_SIZE desc";

	// echo $sql;die;
	$res= sql_select($sql);

	$fromDate=strtotime($date_from);
	$toDate=strtotime($date_to);
	    
	$job_no_array=$pi_id_arr=$wo_id_arr=$summary_data_arr=array();$trims_wo_id_arr=array();
	foreach($res as $row){

		$transaction_date = strtotime($row["TRANSACTION_DATE"]);
		$transaction_type = $row["TRANSACTION_TYPE"];
		$consQuantity = $row["CONS_QUANTITY"];

		$receive_qty = ( $transaction_type==1 && $transaction_date>=$fromDate  && $transaction_date<=$toDate ) ? $consQuantity : 0;
		$issue_qty = ( $transaction_type==2 && $transaction_date>=$fromDate  && $transaction_date<=$toDate ) ? $consQuantity : 0;
		$receive_ret_qty = ( $transaction_type==3 && $transaction_date>=$fromDate  && $transaction_date<=$toDate  ) ? $consQuantity : 0;
		$issue_ret_qty = ( $transaction_type==4 && $transaction_date>=$fromDate  && $transaction_date<=$toDate ) ? $consQuantity : 0;
		$trans_in = ($transaction_type==5 && $transaction_date>=$fromDate  && $transaction_date<=$toDate  ) ? $consQuantity : 0;
		$trans_out = ($transaction_type==6 && $transaction_date>=$fromDate  && $transaction_date<=$toDate   ) ? $consQuantity : 0;

		$opening_total_receive = 0;
		if (in_array($transaction_type, [1, 4, 5]) && $transaction_date < $fromDate) {
			$opening_total_receive = $consQuantity;
		}

		$opening_total_issue = 0; 
		if (in_array($transaction_type, [2, 3, 6]) && $transaction_date < $fromDate) {
			$opening_total_issue = $consQuantity;
		}

		$keys=$row['JOB_NO']."##".$row['PO_NUMBER']."##".$row['BUYER_NAME']."##".$row['STYLE_REF_NO']."##".$row['GROUPING']."##".$row['ITEM_GROUP_ID']."##".$row['COLOR']."##".$row['ITEM_SIZE']."##".$row['UOM']."##".$row['STORE_ID']."##".$row['FLOOR_ID']."##".$row['ROOM']."##".$row['RACK']."##".$row['SELF']."##".$row['BIN_BOX']."##".$row['BREAK_DOWN_ID']."##".$row['PRODUCT_NAME_DETAILS']."##".$row['GMTS_SIZE'];
		$summary_key=$row["ITEM_GROUP_ID"]."##".$row["CONS_UOM"];

		$job_no_array[$keys]["JOB_NO"]=$row["JOB_NO"];
		$job_no_array[$keys]["PO_NUMBER"]=$row["PO_NUMBER"];
		$job_no_array[$keys]["STYLE_REF_NO"]=$row["STYLE_REF_NO"];
		$job_no_array[$keys]["BUYER_NAME"]=$row["BUYER_NAME"];
		$job_no_array[$keys]["GROUPING"]=$row["GROUPING"];
		$job_no_array[$keys]["ITEM_COLOR"]=$row["ITEM_COLOR"];
		$job_no_array[$keys]["BRAND_ID"]=$row["BRAND_ID"];
		$job_no_array[$keys]["SEASON"]=$row["SEASON"];
		$job_no_array[$keys]["SEASON_YEAR"]=$row["SEASON_YEAR"];
		$job_no_array[$keys]["ITEM_GROUP_ID"]=$row["ITEM_GROUP_ID"];
		$job_no_array[$keys]["COLOR"]=$row["COLOR"];
		$job_no_array[$keys]["PRODUCT_NAME_DETAILS"]=$row["PRODUCT_NAME_DETAILS"];
		$job_no_array[$keys]["ITEM_SIZE"]=$row["ITEM_SIZE"];
		$job_no_array[$keys]["GMTS_SIZE"]=$row["GMTS_SIZE"];
		$job_no_array[$keys]["RECEIVE_QTY"]+=$receive_qty;
		$job_no_array[$keys]["RECEIVE_RET_QTY"]+=$receive_ret_qty;
		$job_no_array[$keys]["ISSUE_QTY"]+=$issue_qty;
		$job_no_array[$keys]["ISSUE_RET_QTY"]+=$issue_ret_qty;
		$job_no_array[$keys]["TRANS_IN"]+=$trans_in;
		$job_no_array[$keys]["TRANS_OUT"]+=$trans_out;
		$job_no_array[$keys]["OPENING_TOTAL_RECEIVE"]+=$opening_total_receive;
		$job_no_array[$keys]["OPENING_TOTAL_ISSUE"]+=$opening_total_issue;
		$job_no_array[$keys]["ORDER_RATE"]=$row["ORDER_RATE"];
		$job_no_array[$keys]["CONS_RATE"]=$row["CONS_RATE"];
		$job_no_array[$keys]["SHIPING_STATUS"]=$row["SHIPING_STATUS"];
		$job_no_array[$keys]["CONS_UOM"]=$row["CONS_UOM"];
		$job_no_array[$keys]["STORE_ID"]=$row["STORE_ID"];
		$job_no_array[$keys]["FLOOR_ID"]=$row["FLOOR_ID"];
		$job_no_array[$keys]["ROOM"]=$row["ROOM"];
		$job_no_array[$keys]["RACK"]=$row["RACK"];
		$job_no_array[$keys]["SELF"]=$row["SELF"];
		$job_no_array[$keys]["BIN_BOX"]=$row["BIN_BOX"];
		$job_no_array[$keys]["BREAK_DOWN_ID"]=$row["BREAK_DOWN_ID"];
		$job_no_array[$keys]["ORDER_ID"]=$row["ORDER_ID"];
		$trims_wo_id_arr[$row["BREAK_DOWN_ID"]]=$row["BREAK_DOWN_ID"];
		if($row["RECEIVE_BASIS"]==1)
		{
			$pi_id_arr[$row["PI_WO_BATCH_NO"]]=$row["PI_WO_BATCH_NO"];
		}
		elseif($row["RECEIVE_BASIS"]==2)
		{
			$wo_id_arr[$row["PI_WO_BATCH_NO"]]=$row["PI_WO_BATCH_NO"];
		}
		//for summary part
		$summary_data_arr[$summary_key]["ITEM_GROUP_ID"]=$row["ITEM_GROUP_ID"];
		$summary_data_arr[$summary_key]["CONS_UOM"]=$row["CONS_UOM"];
		$summary_data_arr[$summary_key]["CONS_RATE"]=$row["CONS_RATE"];
		$summary_data_arr[$summary_key]["RECEIVE_QTY"]+=$receive_qty;
		$summary_data_arr[$summary_key]["RECEIVE_RET_QTY"]+=$receive_ret_qty;
		$summary_data_arr[$summary_key]["ISSUE_QTY"]+=$issue_qty;
		$summary_data_arr[$summary_key]["ISSUE_RET_QTY"]+=$issue_ret_qty;
		$summary_data_arr[$summary_key]["TRANS_IN"]+=$trans_in;
		$summary_data_arr[$summary_key]["TRANS_OUT"]+=$trans_out;
		$summary_data_arr[$summary_key]["OPENING_TOTAL_RECEIVE"]+=$opening_total_receive;
		$summary_data_arr[$summary_key]["OPENING_TOTAL_ISSUE"]+=$opening_total_issue;
	}
	unset($res);

	$con = connect();
	$rid=execute_query("delete from GBL_TEMP_ENGINE where entry_form=96 and user_id=$user_id");
	if($rid) oci_commit($con);
	$wo_order_arr=array();$wo_pi_arr=array();
	if(!empty($wo_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 96, 1, $wo_id_arr,$empty_arr);
		$sql_result =sql_select("SELECT a.PAY_MODE, b.BOOKING_NO, b.PO_BREAK_DOWN_ID, b.ID as WO_ID, a.SUPPLIER_ID, b.RATE, b.TRIM_GROUP as ITEM_GROUP_ID  
		from  wo_booking_mst a, wo_booking_dtls b, gbl_temp_engine c 
		where a.id=b.BOOKING_MST_ID and b.BOOKING_MST_ID=c.ref_val and a.entry_form=87 and c.entry_form=96 and c.ref_from=1 and c.user_id= $user_id and a.COMPANY_ID=$company_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0");

		foreach($sql_result as $row){
			$wo_order_arr[$row["PO_BREAK_DOWN_ID"]][$row["ITEM_GROUP_ID"]]["RATE"]=$row["RATE"];	
			$wo_order_arr[$row["PO_BREAK_DOWN_ID"]][$row["ITEM_GROUP_ID"]]["WORK_ORDER_NO"]=$row["BOOKING_NO"];	
			$wo_order_arr[$row["PO_BREAK_DOWN_ID"]][$row["ITEM_GROUP_ID"]]["PAY_MODE"]=$row["PAY_MODE"];	
			$wo_order_arr[$row["PO_BREAK_DOWN_ID"]][$row["ITEM_GROUP_ID"]]["SUPPLIER_ID"]=$row["SUPPLIER_ID"];	
		}
		
		$sql_result =sql_select("SELECT a.PI_NUMBER, a.SUPPLIER_ID, b.ITEM_GROUP, b.ORDER_ID , e.LC_NUMBER 
		from  COM_PI_ITEM_DETAILS b, gbl_temp_engine c, 
		COM_PI_MASTER_DETAILS a left join com_btb_lc_pi d on a.id=d.pi_id
		left join com_btb_lc_master_details e on d.com_btb_lc_master_details_id=e.id 
		where a.id=b.PI_ID and b.WORK_ORDER_ID=c.ref_val and a.ITEM_CATEGORY_ID=4 and c.entry_form=96 and c.ref_from=1 and c.user_id= $user_id and a.IMPORTER_ID=$company_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0");

		foreach($sql_result as $row){
			$wo_pi_arr[$row["ORDER_ID"]][$row["ITEM_GROUP"]]["PI_NUMBER"]=$row["PI_NUMBER"];	
			$wo_pi_arr[$row["ORDER_ID"]][$row["ITEM_GROUP"]]["LC_NUMBER"]=$row["LC_NUMBER"];
		}
	}
	
	if(!empty($pi_id_arr))
	{ 
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 96, 2, $pi_id_arr,$empty_arr);
		$sql_result =sql_select("SELECT a.PI_NUMBER, a.SUPPLIER_ID, b.ITEM_GROUP, b.ORDER_ID, e.LC_NUMBER, b.WORK_ORDER_ID
		from  COM_PI_ITEM_DETAILS b, gbl_temp_engine c, 
		COM_PI_MASTER_DETAILS a left join com_btb_lc_pi d on a.id=d.pi_id
		left join com_btb_lc_master_details e on d.com_btb_lc_master_details_id=e.id 
		where a.id=b.PI_ID and b.PI_ID=c.ref_val and a.ITEM_CATEGORY_ID=4 and c.entry_form=96 and c.ref_from=2 and c.user_id= $user_id and a.IMPORTER_ID=$company_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0");

		$wo_id_from_pi_arr=array();
		foreach($sql_result as $row){
			$wo_pi_arr[$row["ORDER_ID"]][$row["ITEM_GROUP"]]["PI_NUMBER"]=$row["PI_NUMBER"];	
			$wo_pi_arr[$row["ORDER_ID"]][$row["ITEM_GROUP"]]["LC_NUMBER"]=$row["LC_NUMBER"];	
			$wo_id_from_pi_arr[$row["WORK_ORDER_ID"]]=$row["WORK_ORDER_ID"];
		}

		if(!empty($wo_id_from_pi_arr)){

			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 96, 3, $wo_id_from_pi_arr,$empty_arr);
			$sql_result =sql_select("SELECT a.PAY_MODE, b.BOOKING_NO, b.PO_BREAK_DOWN_ID, b.ID as WO_ID, a.SUPPLIER_ID, b.RATE, b.TRIM_GROUP as ITEM_GROUP_ID  
			from  wo_booking_mst a, wo_booking_dtls b, gbl_temp_engine c 
			where a.id=b.BOOKING_MST_ID and a.ID=c.REF_VAL and a.entry_form=87 and c.entry_form=96 and c.ref_from=3 and c.user_id= $user_id and a.COMPANY_ID=$company_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0");

			// $wo_order_arr=array();
			foreach($sql_result as $row){
				$wo_order_arr[$row["PO_BREAK_DOWN_ID"]][$row["ITEM_GROUP_ID"]]["RATE"]=$row["RATE"];	
				$wo_order_arr[$row["PO_BREAK_DOWN_ID"]][$row["ITEM_GROUP_ID"]]["WORK_ORDER_NO"]=$row["BOOKING_NO"];	
				$wo_order_arr[$row["PO_BREAK_DOWN_ID"]][$row["ITEM_GROUP_ID"]]["PAY_MODE"]=$row["PAY_MODE"];	
				$wo_order_arr[$row["PO_BREAK_DOWN_ID"]][$row["ITEM_GROUP_ID"]]["SUPPLIER_ID"]=$row["SUPPLIER_ID"];	
			}	
		}
	}
	// print_r($trims_wo_id_arr);
	if(!empty($trims_wo_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 96, 4, $trims_wo_id_arr,$empty_arr);
		 $sql_result_data =sql_select("SELECT c.item_ref as ARTICLE_NUMBER, c.PO_BREAK_DOWN_ID,c.ITEM_COLOR, c.DESCRIPTION, c.GMTS_SIZES, c.ITEM_SIZE  from  wo_trim_book_con_dtls c, gbl_temp_engine d where c.po_break_down_id=d.ref_val and d.user_id= $user_id and d.entry_form=96 and d.REF_FROM=4 group by c.item_ref, c.PO_BREAK_DOWN_ID, c.ITEM_COLOR, c.DESCRIPTION, c.GMTS_SIZES, c.ITEM_SIZE");

		$article_number_arr=array();
		foreach($sql_result_data as $row){
			$article_number_arr[$row["PO_BREAK_DOWN_ID"]][$row["ITEM_COLOR"]][$row["DESCRIPTION"]][$row["GMTS_SIZES"]][$row["ITEM_SIZE"]]["ARTICLE_NUMBER"]=$row["ARTICLE_NUMBER"];	
		}
	 }

	$tbl_width = 3550;
	ob_start();
	if($rpt_type==1)
	{
		?>
		<fieldset style="width:<?=$tbl_width+20;?>px">
			<table cellpadding="0" cellspacing="0" width="1400">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if(str_replace("'","",$date_from)!="" && str_replace("'","",$date_to) ) echo change_date_format(str_replace("'","",$date_from)) ." To ". change_date_format(str_replace("'","",$date_to)) ;?></strong></td>
				</tr>
			</table>
			<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
				<thead>
					<tr>
						<th width="40"><p>Sl</p></th>
						<th width="100"><p>Job</p></th>
						<th width="100"><p>Internal Ref</p></th>
						<th width="90"><p>Order</p></th>
						<th width="100"><p>Style Ref.</p></th>
						<th width="100"><p>Buyer</p></th>
						<th width="70"><p>Brand</p></th>
						<th width="70"><p>Season</p></th>
						<th width="70"><p>Season Year</p></th>
						<th width="80"><p>Work Order</p></th>
						<th width="80"><p>Paymode</p></th>
						<th width="80"><p>PI NO</p></th>
						<th width="80"><p>LC/SC</p></th>
						<th width="80"><p>Supplier Name</p></th>  
						<th width="80"><p>Item Group</p></th>
						<th width="80"><p>Item Description</p></th>
						<th width="80"><p>Gmts Color</p></th>
						<th width="80"><p>Gmts Size</p></th>
						<th width="80"><p>Item Color</p></th>
						<th width="80"><p>Item Size</p></th>
						<th width="80"><p>Article No</p></th>
						<th width="80"><p>UOM</p></th>

						<th width="90"><p>Opening Stock</p></th>
						<th width="80"><p>Receive Qnty</p></th>
						<th width="80"><p>Issue Return</p></th>
						<th width="80"><p>Transfer in</p></th>
						<th width="110"><p>Total Receive Qnty</p></th>
						<th width="80"><p>Issue Qnty</p></th>
						<th width="80"><p>Rcv Return</p></th>
						<th width="80"><p>Transfer out</p></th>
						<th width="110"><p>Total Issue Qnty</p></th>
						<th width="80"><p>Closing Stock</p></th>
						<th width="80"><p>Rate ($)</p></th>
						<th width="80"><p>Rate (Tk)</p></th>
						<th width="100"><p>Amount (Tk)</p></th>
						<th width="80"><p>Store</p></th>
						<th width="80"><p>Floor</p></th>
						<th width="80"><p>Room</p></th>
						<th width="80"><p>Rack No</p></th>
						<th width="80"><p>Self No</p></th>
						<th width="80"><p>Bin/Box</p></th>
						<th width="100"><p>Shipment Status</p></th>
					</tr>
				</thead>
			</table>
			<div style="width:<?=$tbl_width+20;?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left"> 
					<tbody>
						<?			
						$i=1;$closeingStockQty=0;$openingStockQty=0;$totalReceive=0;$totalIssue=0;$total_rec_qty=$total_issue_qty=0;
						$summary_arr_forqmmount=array();
						foreach ($job_no_array as $key => $val) 
						{
							$openingStockQty = (($val["OPENING_TOTAL_RECEIVE"]-$val["OPENING_TOTAL_ISSUE"])*$conversion_factor_arr[$val['ITEM_GROUP_ID']]);

							$totalReceive = (($val["RECEIVE_QTY"]+$val["ISSUE_RET_QTY"]+$val["TRANS_IN"])*$conversion_factor_arr[$val['ITEM_GROUP_ID']]);

							$totalIssue = (($val["ISSUE_QTY"]+$val["RECEIVE_RET_QTY"]+$val["TRANS_OUT"])*$conversion_factor_arr[$val['ITEM_GROUP_ID']]);
							$closeingStockQty = ($openingStockQty+$totalReceive-$totalIssue);
							$arctical_no=$article_number_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_COLOR"]][$val["PRODUCT_NAME_DETAILS"]][$val["GMTS_SIZE"]][$val["ITEM_SIZE"]]["ARTICLE_NUMBER"];
							// $arctical_no=($arctical!=0)? $arctical: 0;

							$total_rec_qty=$val["RECEIVE_QTY"]+$val["ISSUE_RET_QTY"]+$val["TRANS_IN"];
							$total_issue_qty=$val["ISSUE_QTY"]+$val["RECEIVE_RET_QTY"]+$val["TRANS_OUT"];

							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							if($cbo_value_range_by==2 &&  number_format($closeingStockQty,2,'.','')>0.00)
							{
								$summary_arr_forqmmount[$val["ITEM_GROUP_ID"]][$val["CONS_UOM"]]["CONS_AMMOUNTS"]+=$closeingStockQty*$val["CONS_RATE"];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<?  echo $i; ?>">
									<td width="40"><p><?=$i;?></p></td>
									<td width="100"><p><?=$val["JOB_NO"];?></p></td>
									<td width="100"><p><?=$val['GROUPING'];?></p></td>
									<td width="90"><p><?=$val['PO_NUMBER'];?></p></td>
									<td width="100"><p>&nbsp;<?=$val['STYLE_REF_NO'];?></p></td>
									<td width="100"><p>&nbsp;<?=$buyer_array[$val['BUYER_NAME']];?></p></td>
									<td width="70" align="center"><p><?=$lib_buyer_brand_arr[$val['BRAND_ID']];?></p></td>
									<td width="70" align="center"><p><?=$season_arr[$val['SEASON']];?></p></td>
									<td width="70" align="center"><p><?=$val['SEASON_YEAR'];?></p></td>
									<td width="80" align="center"><p><?=$wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["WORK_ORDER_NO"];?></p></td>
									<td width="80" align="center"><p><?=$pay_mode[$wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["PAY_MODE"]];?></p></td>
									<td width="80" align="center"><p><?=$wo_pi_arr[$val["ORDER_ID"]][$val["ITEM_GROUP_ID"]]["PI_NUMBER"];?></p></td>
									<td width="80" align="center"><p><?=$wo_pi_arr[$val["ORDER_ID"]][$val["ITEM_GROUP_ID"]]["LC_NUMBER"];?></p></td>
									<td width="80" align="right"><p><?=$supplier_name_arr[$wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["SUPPLIER_ID"]];?></p></td>
									<td width="80" align="right"><p><?=$trim_group_library[$val["ITEM_GROUP_ID"]];?></p></td>
									<td width="80" align="center"><p><?=$val["PRODUCT_NAME_DETAILS"];?></p></td>
									<td width="80" align="center"><p><?=$color_library[$val["COLOR"]];?></p></td>
									<td width="80" align="center"><p><?=$size_library[$val["GMTS_SIZE"]];?></p></td>
									<td width="80" align="center"><p><?=$color_library[$val["ITEM_COLOR"]];//$size_library[$val["GMTS_SIZE"]];?></p></td>
									<td width="80" align="center"><p><?=$val["ITEM_SIZE"];//$size_library[$val["GMTS_SIZE"]];?></p></td>
									<td width="80" align="center"><p><?=$arctical_no?></p></td>
									<td width="80" align="center"><p><?=$unit_of_measurement[$val["CONS_UOM"]]?></p></td>
									<td width="90" align="right"><p><?= number_format($openingStockQty,2);?></p></td>
									<td width="80" align="right"><p><?=number_format($val["RECEIVE_QTY"],2);?></p></td>
									<td width="80" align="right"><p><?=number_format($val["ISSUE_RET_QTY"],2);?></p></td>
									<td width="80" align="right"><p><?= number_format($val["TRANS_IN"],2);?></p></td>
									<td width="110" align="right" title="<?='Receive Qnty+Issue Return+Transfer in'?>"><p><?= number_format($total_rec_qty,2);?></p></td>
									<td width="80" align="right"><p><?=number_format($val["ISSUE_QTY"],2);?></p></td>
									<td width="80" align="right"><p><?=number_format($val["RECEIVE_RET_QTY"],2);?></p></td>
									<td width="80" align="right"><p><?=number_format($val["TRANS_OUT"],0);?></p></td>
									<td width="110" align="right" title="<?='Issue Qnty+Rcv Return+Transfer out'?>"><p><?= number_format($total_issue_qty,2);?></p></td>
									<td width="80" align="right"><p><?=number_format($closeingStockQty,2);?></p></td>
									<td width="80" align="right"><p><?=number_format($wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["RATE"],4);?></p></td>
									<td width="80" align="right"><p><?=number_format($val["CONS_RATE"],2);?></p></td>
									<td width="100" align="right"><p><?= number_format($closeingStockQty*$val["CONS_RATE"],4);?></p></td>
									<td width="80" align="center"><p><?=$store_arr[$val["STORE_ID"]];?></p></td>
									<td width="80" align="center"><p><?=$lib_floor[$val["FLOOR_ID"]];?></p></td>
									<td width="80" align="center"><p><?=$lib_room[$val["ROOM"]];?></p></td>
									<td width="80" align="center"><p><?=$lib_rack[$val["RACK"]];?></p></td>
									<td width="80" align="center"><p><?=$lib_self_no[$val["SELF"]];?></p></td>
									<td width="80" align="center"><p><?=$lib_bin_box[$val["BIN_BOX"]];?></p></td>
									<td width="100" align="center"><p><?=$shipment_status[$val['SHIPING_STATUS']];?></p></td>
								</tr>
								<?
								$i++;		
								$total_opening_stock += $openingStockQty;
								$total_rcv_qty += $val["RECEIVE_QTY"];
								$total_issue_rate += $val["ISSUE_RET_QTY"];
								$total_trans_in_qty += $val["TRANS_IN"];
								$total_rec_rate += $total_rec_qty;
								$total_issue += $val["ISSUE_QTY"];
								$total_rcv_ret_qty += $val["RECEIVE_RET_QTY"];
								$total_trns_out += $val["TRANS_OUT"];
								$total_iss_qty += $total_issue_qty;
								$total_clos_qty += $closeingStockQty;
								$total_rate_dlr += $wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["RATE"];
								$total_rate_tk += $val["CONS_RATE"];
								$total_amnt +=$closeingStockQty*$val["CONS_RATE"];
							}
							else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
							{
								$summary_arr_forqmmount[$val["ITEM_GROUP_ID"]][$val["CONS_UOM"]]["CONS_AMMOUNTS"]+=$closeingStockQty*$val["CONS_RATE"];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<?  echo $i; ?>">
									<td width="40"><p><?=$i;?></p></td>
									<td width="100"><p><?=$val["JOB_NO"];?></p></td>
									<td width="100"><p><?=$val['GROUPING'];?></p></td>
									<td width="90"><p><?=$val['PO_NUMBER'];?></p></td>
									<td width="100"><p>&nbsp;<?=$val['STYLE_REF_NO'];?></p></td>
									<td width="100"><p>&nbsp;<?=$buyer_array[$val['BUYER_NAME']];?></p></td>
									<td width="70" align="center"><p><?=$lib_buyer_brand_arr[$val['BRAND_ID']];?></p></td>
									<td width="70" align="center"><p><?=$season_arr[$val['SEASON']];?></p></td>
									<td width="70" align="center"><p><?=$val['SEASON_YEAR'];?></p></td>
									<td width="80" align="center"><p><?=$wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["WORK_ORDER_NO"];?></p></td>
									<td width="80" align="center"><p><?=$pay_mode[$wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["PAY_MODE"]];?></p></td>
									<td width="80" align="center"><p><?=$wo_pi_arr[$val["ORDER_ID"]][$val["ITEM_GROUP_ID"]]["PI_NUMBER"];?></p></td>
									<td width="80" align="center"><p><?=$wo_pi_arr[$val["ORDER_ID"]][$val["ITEM_GROUP_ID"]]["LC_NUMBER"];?></p></td>
									<td width="80" align="right"><p><?=$supplier_name_arr[$wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["SUPPLIER_ID"]];?></p></td>
									<td width="80" align="right"><p><?=$trim_group_library[$val["ITEM_GROUP_ID"]];?></p></td>
									<td width="80" align="center"><p><?=$val["PRODUCT_NAME_DETAILS"];?></p></td>
									<td width="80" align="center"><p><?=$color_library[$val["COLOR"]];?></p></td>
									<td width="80" align="center"><p><?=$size_library[$val["GMTS_SIZE"]];?></p></td>
									<td width="80" align="center"><p><?=$color_library[$val["ITEM_COLOR"]];//$size_library[$val["GMTS_SIZE"]];?></p></td>
									<td width="80" align="center"><p><?=$val["ITEM_SIZE"];//$size_library[$val["GMTS_SIZE"]];?></p></td>
									<td width="80" align="center"><p><?=$arctical_no?></p></td>
									<td width="80" align="center"><p><?=$unit_of_measurement[$val["CONS_UOM"]]?></p></td>
									<td width="90" align="right"><p><?= number_format($openingStockQty,2);?></p></td>
									<td width="80" align="right"><p><?=number_format($val["RECEIVE_QTY"],2);?></p></td>
									<td width="80" align="right"><p><?=number_format($val["ISSUE_RET_QTY"],2);?></p></td>
									<td width="80" align="right"><p><?= number_format($val["TRANS_IN"],2);?></p></td>
									<td width="110" align="right" title="<?='Receive Qnty+Issue Return+Transfer in'?>"><p><?= number_format($total_rec_qty,2);?></p></td>
									<td width="80" align="right"><p><?=number_format($val["ISSUE_QTY"],2);?></p></td>
									<td width="80" align="right"><p><?=number_format($val["RECEIVE_RET_QTY"],2);?></p></td>
									<td width="80" align="right"><p><?=number_format($val["TRANS_OUT"],0);?></p></td>
									<td width="110" align="right" title="<?='Issue Qnty+Rcv Return+Transfer out'?>"><p><?= number_format($total_issue_qty,2);?></p></td>
									<td width="80" align="right"><p><?=number_format($closeingStockQty,2);?></p></td>
									<td width="80" align="right"><p><?=number_format($wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["RATE"],4);?></p></td>
									<td width="80" align="right"><p><?=number_format($val["CONS_RATE"],2);?></p></td>
									<td width="100" align="right"><p><?= number_format($closeingStockQty*$val["CONS_RATE"],4);?></p></td>
									<td width="80" align="center"><p><?=$store_arr[$val["STORE_ID"]];?></p></td>
									<td width="80" align="center"><p><?=$lib_floor[$val["FLOOR_ID"]];?></p></td>
									<td width="80" align="center"><p><?=$lib_room[$val["ROOM"]];?></p></td>
									<td width="80" align="center"><p><?=$lib_rack[$val["RACK"]];?></p></td>
									<td width="80" align="center"><p><?=$lib_self_no[$val["SELF"]];?></p></td>
									<td width="80" align="center"><p><?=$lib_bin_box[$val["BIN_BOX"]];?></p></td>
									<td width="100" align="center"><p><?=$shipment_status[$val['SHIPING_STATUS']];?></p></td>
								</tr>
								<?
								$i++;		
								$total_opening_stock += $openingStockQty;
								$total_rcv_qty += $val["RECEIVE_QTY"];
								$total_issue_rate += $val["ISSUE_RET_QTY"];
								$total_trans_in_qty += $val["TRANS_IN"];
								$total_rec_rate += $total_rec_qty;
								$total_issue += $val["ISSUE_QTY"];
								$total_rcv_ret_qty += $val["RECEIVE_RET_QTY"];
								$total_trns_out += $val["TRANS_OUT"];
								$total_iss_qty += $total_issue_qty;
								$total_clos_qty += $closeingStockQty;
								$total_rate_dlr += $wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["RATE"];
								$total_rate_tk += $val["CONS_RATE"];
								$total_amnt +=$closeingStockQty*$val["CONS_RATE"];
							}
						}
						?>
					</tbody>
				</table>
				</div>	
				<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
					<tfoot>
						<tr>
							<th width="40"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>  
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80">Total</th>
							<th width="90" id="value_yarn_req_qnty"><? echo number_format($total_opening_stock,2,".","")?></th>
							<th width="80" id="value_total_rcv_qty"><? echo number_format($total_rcv_qty,2,".","")?></th>
							<th width="80" id="value_total_issue_rate"><? echo number_format($total_issue_rate,2,".","")?></th>
							<th width="80" id="value_total_trans_in_qty"><? echo number_format($total_trans_in_qty,2,".","")?></th>
							<th width="110" id="value_total_rec_rate"><? echo number_format($total_rec_rate,2,".","")?></th>
							<th width="80" id="value_total_issue"><? echo number_format($total_issue,2,".","")?></th>
							<th width="80" id="value_total_rcv_ret_qty"><? echo number_format($total_rcv_ret_qty,2,".","")?></th>
							<th width="80" id="value_total_trns_out"><? echo number_format($total_trns_out,2,".","")?></th>
							<th width="110" id="value_total_iss_qty"><? echo number_format($total_iss_qty,2,".","")?></th>
							<th width="80" id="value_total_clos_qty"><? echo number_format($total_clos_qty,2,".","")?></th>
							<th width="80" id="value_total_rate_dlrs"><?// echo number_format($total_rate_dlr,2,".","")?></th>
							<th width="80" id="value_total_rate_tkss"><?// echo number_format($total_rate_tk,2,".","")?></th>
							<th width="100" id="value_total_amnt"><? echo number_format($total_amnt,2,".","")?></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="100"></th>
						</tr>
					</tfoot>
				</table>
			<br><br><br>
			<div style="width:1480px; overflow-y: scroll; max-height:380px;" >
				<table width="1480" cellpadding="0" cellspacing="0"  rules="all" border="1" align="left" class="rpt_table"> 
					<thead>
						<tr>
							<th colspan="14" style="font-size: 17px;">Trims Summary Stock Report</th>
						</tr>
						<tr>
							<th width="30">Id</th>
							<th width="100">Item Group Name</th>
							<th width="100">UOM</th>
							<th width="100">Opening Stock</th>
							<th width="100">Receive Qnty</th>
							<th width="100">Issue Return</th>
							<th width="100">Transfer in</th>
							<th width="100">Total Receive Qnty</th>
							<th width="100">Issue Qnty</th>
							<th width="100">Rcv Return</th>
							<th width="100">Transfer out</th>
							<th width="100">Total Issue Qnty</th>
							<th width="100">Closing Stock</th>
							<th width="100">Closing Stock Amount (Tk)</th>
						</tr>
					</thead>	
					<tbody>
					<?$i=1;$total_amnt_tk=0;
					foreach($summary_data_arr as $row)
					{
						$openingStockQty = (($row["OPENING_TOTAL_RECEIVE"]-$row["OPENING_TOTAL_ISSUE"])*$conversion_factor_arr[$row['ITEM_GROUP_ID']]);
						$totalReceive = (($row["RECEIVE_QTY"]+$row["ISSUE_RET_QTY"]+$row["TRANS_IN"])*$conversion_factor_arr[$row['ITEM_GROUP_ID']]);
						$totalIssue = (($row["ISSUE_QTY"]+$row["RECEIVE_RET_QTY"]+$row["TRANS_OUT"])*$conversion_factor_arr[$row['ITEM_GROUP_ID']]);

						$closeingStockQty = ($openingStockQty+$totalReceive-$totalIssue);
						// $total_rec_qty=$row["RECEIVE_QTY"]+$row["TRANS_IN"];
						$total_rec_qty=$row["RECEIVE_QTY"]+$row["ISSUE_RET_QTY"]+$row["TRANS_IN"];
						$total_issue_qty=$row["ISSUE_QTY"]+$row["RECEIVE_RET_QTY"]+$row["TRANS_OUT"];
						// $total_issue_qty=$row["ISSUE_QTY"]+$row["TRANS_OUT"];

						//echo $summary_arr_forqmmount[$row["ITEM_GROUP_ID"]][$row["CONS_UOM"]]["CONS_AMMOUNTS"];

						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
						
							<tr bgcolor="<?=$bgcolor?>">
								<td><?=$i?></td>
								<td><?= $trim_group_library[$row["ITEM_GROUP_ID"]]?></td>
								<td><?= $unit_of_measurement[$row["CONS_UOM"]]?></td>
								<td align="right"><?=number_format($openingStockQty,2)?></td>
								<td align="right"><?=number_format($row["RECEIVE_QTY"],2)?></td>
								<td align="right"><?= number_format($row["ISSUE_RET_QTY"],2)?></td>
								<td align="right"><?= number_format($row["TRANS_IN"],2)?></td>
								<td align="right"><?=number_format($total_rec_qty,2)?></td>
								<td align="right"><?=number_format($row["ISSUE_QTY"],2)?></td>
								<td align="right"><?=number_format($row["RECEIVE_RET_QTY"],2)?></td>
								<td align="right"><?=number_format($row["TRANS_OUT"],2)?></td>
								<td align="right"><?=number_format($total_issue_qty,2)?></td>
								<td align="right"><?=number_format($closeingStockQty,2)?></td>
								<td align="right"><? 
								echo number_format($summary_arr_forqmmount[$row["ITEM_GROUP_ID"]][$row["CONS_UOM"]]["CONS_AMMOUNTS"],4);?></td>
							</tr>
							<?
							$i++;
							$total_opening_stock2 += $openingStockQty;
							$total_rcv_qty2 += $row["RECEIVE_QTY"];
							$total_issue_rate2 += $row["ISSUE_RET_QTY"];
							$total_trans_in_qty2 += $row["TRANS_IN"];
							$total_rec_rate2 += $total_rec_qty;
							$total_issue2 += $row["ISSUE_QTY"];
							$total_rcv_ret_qty2 += $row["RECEIVE_RET_QTY"];
							$total_trns_out2 += $row["TRANS_OUT"];
							$total_iss_qty2 += $total_issue_qty;
							$total_clos_qty2 += $closeingStockQty;
							$total_amnt_tk += $summary_arr_forqmmount[$row["ITEM_GROUP_ID"]][$row["CONS_UOM"]]["CONS_AMMOUNTS"];
					}?>
				</tbody>
				<tfoot>
					<tr align="right">
						<th colspan="3"> Total:</th>
						<th width="100"><p><? echo number_format($total_opening_stock2,2)?></p></th>
						<th width="100"><p><? echo number_format($total_rcv_qty2,2)?></p></th>
						<th width="100"><p><? echo number_format($total_issue_rate2,2)?></p></th>
						<th width="100"><p><? echo number_format($total_trans_in_qty2,2)?></p></th>
						<th width="100"><p><? echo number_format($total_rec_rate2,2)?></p></th>
						<th width="100"><p><? echo number_format($total_issue2,2)?></p></th>
						<th width="100"><p><? echo number_format($total_rcv_ret_qty2,2)?></p></th>
						<th width="100"><p><? echo number_format($total_trns_out2,2)?></p></th>
						<th width="100"><p><? echo number_format($total_iss_qty2,2)?></p></th>
						<th width="100"><p><? echo number_format($total_clos_qty2,2)?></p></th>
						<th width="100"><p><? echo number_format($total_amnt_tk,2)?></p></th>		
					</tr>
				</tfoot>
				</table>
			</div>
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
	else
	{

			$html .='
			<fieldset style="width:'.($tbl_width+20).'px>
			<div style="width:'.($tbl_width+20).'px; >
			<table width="'.$tbl_width.'"> 
				<thead>
					<tr>
						<td>Report Title : '.$report_title.' </td>
					</tr>
					<tr>
						<td>'.$company_arr[str_replace("'","",$cbo_company_id)].'</td>
					</tr>
					<tr>
						<td>'.change_date_format(str_replace("'","",$date_from)) ." To ". change_date_format(str_replace("'","",$date_to)).' </td>
					</tr>
					
				</thead>
			</table>

			</div>
			<div style="width:'.($tbl_width+20).'px; >
			<table width="<'.$tbl_width.'>">
        	<thead>
               <tr>
					<th>Sl</th>
					<th>Job</th>
					<th>Internal Ref</th>
					<th>Order</th>
					<th>Style Ref.</th>
					<th>Buyer</th>
					<th>Brand</th>
					<th>Season</th>
					<th>Season Year</th>
					<th>Work Order</th>
					<th>Paymode</th>
					<th>PI NO</th>
					<th>LC/SC</th>
					<th>Supplier Name</th>
					<th>Item Group</th>
					<th>Item Description</th>
					<th>Gmts Color</th>
					<th>Gmts Size</th>
					<th>Item Color</th>
					<th>Item Size</th>
					<th>Article No</th>
					<th>UOM</th>
					<th>Opening Stock</th>
					<th>Receive Qnty</th>
					<th>Issue Return</th>
					<th>Transfer in</th>
					<th>Total Receive Qnty</th>
					<th>Issue Qnty</th>
					<th>Rcv Return</th>
					<th>Transfer out</th>
					<th>Total Issue Qnty</th>
					<th>Closing Stock</th>
					<th>Rate ($)</th>
					<th>Rate (Tk)</th>
					<th>Amount (Tk)</th>
					<th>Store</th>
					<th>Floor</th>
					<th>Room</th>
					<th>Rack No</th>
					<th>Self No</th>
					<th>Bin/Box</th>
					<th>Shipment Status</th>
               </tr> 
            </thead>
		</div>
           
			<div style="width:'.($tbl_width+20).'px;  " id="">
				<table  width="'.$tbl_width.'" > 
					<tbody>';
					$n=1;$closeingStockQty=0;$openingStockQty=0;$totalReceive=0;$totalIssue=0;$total_rec_qty=$total_issue_qty=0;
					foreach ($job_no_array as $key => $val) 
					{
						$openingStockQty = (($val["OPENING_TOTAL_RECEIVE"]-$val["OPENING_TOTAL_ISSUE"])*$conversion_factor_arr[$val['ITEM_GROUP_ID']]);

						$totalReceive = (($val["RECEIVE_QTY"]+$val["ISSUE_RET_QTY"]+$val["TRANS_IN"])*$conversion_factor_arr[$val['ITEM_GROUP_ID']]);

						$totalIssue = (($val["ISSUE_QTY"]+$val["RECEIVE_RET_QTY"]+$val["TRANS_OUT"])*$conversion_factor_arr[$val['ITEM_GROUP_ID']]);
						$closeingStockQty = ($openingStockQty+$totalReceive-$totalIssue);
						$arctical_no=$article_number_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_COLOR"]][$val["PRODUCT_NAME_DETAILS"]][$val["GMTS_SIZE"]][$val["ITEM_SIZE"]]["ARTICLE_NUMBER"];
						// $arctical_no=($arctical!=0)? $arctical: 0;

						$total_rec_qty=$val["RECEIVE_QTY"]+$val["ISSUE_RET_QTY"]+$val["TRANS_IN"];
						$total_issue_qty=$val["ISSUE_QTY"]+$val["RECEIVE_RET_QTY"]+$val["TRANS_OUT"];

						$amnt = ($closeingStockQty * $val["CONS_RATE"]);

						$bgcolor=($n%2==0)?"#E9F3FF":"#FFFFFF";
						if($cbo_value_range_by==2 &&  round($closeingStockQty,2,'.','')>0.00)
						{
							$summary_arr_forqmmount[$val["ITEM_GROUP_ID"]][$val["CONS_UOM"]]["CONS_AMMOUNTS"]+=$closeingStockQty*$val["CONS_RATE"];
							$html .='<tr>
							<td>'.$n.'</td>	
                            <td>' . $val["JOB_NO"] . '</td>
							<td>' . $val['GROUPING'] . '</td>
							<td>' . $val['PO_NUMBER'] . '</td>
							<td>' . $val['STYLE_REF_NO'] . '</td>
							<td>' . $buyer_array[$val['BUYER_NAME']] . '</td>
							<td>' . $lib_buyer_brand_arr[$val['BRAND_ID']] . '</td>
							<td>' . $season_arr[$val['SEASON']] . '</td>
							<td>' . $val['SEASON_YEAR'] . '</td>
							<td>' . $wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["WORK_ORDER_NO"] . '</td>
							<td>' . $pay_mode[$wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["PAY_MODE"]] . '</td>
							<td>' . $wo_pi_arr[$val["ORDER_ID"]][$val["ITEM_GROUP_ID"]]["PI_NUMBER"] . '&nbsp;</td>
							<td>' . $wo_pi_arr[$val["ORDER_ID"]][$val["ITEM_GROUP_ID"]]["LC_NUMBER"] . '&nbsp;</td>
							<td>' . $supplier_name_arr[$wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["SUPPLIER_ID"]] . '</td>
							<td>' . $trim_group_library[$val["ITEM_GROUP_ID"]] . '</td>
							<td>' . $val["PRODUCT_NAME_DETAILS"] . '</td>
							<td>' . $color_library[$val["COLOR"]] . '</td>
							<td>' . $size_library[$val["GMTS_SIZE"]] . '</td>
							<td>' . $color_library[$val["ITEM_COLOR"]] . '</td>
							<td>' . $size_library[$val["ITEM_SIZE"]] . '</td>
							<td>' . $arctical_no . '</td>
							<td>' . $unit_of_measurement[$val["CONS_UOM"]] . '</td>
							<td>' . round($openingStockQty, 2) . '</td>
							<td>' . round($val["RECEIVE_QTY"], 2) . '</td>
							<td>' . round($val["ISSUE_RET_QTY"], 2) . '</td>
							<td>' . round($val["TRANS_IN"], 2) . '</td>
							<td title="' . 'Receive Qnty+Issue Return+Transfer in' . '">' . round($total_rec_qty, 2) . '</td>
							<td>' . round($val["ISSUE_QTY"], 2) . '</td>
							<td>' . round($val["RECEIVE_RET_QTY"], 2) . '</td>
							<td>' . round($val["TRANS_OUT"], 0) . '</td>
							<td title="' . 'Issue Qnty+Rcv Return+Transfer out' . '">' . round($total_issue_qty, 2) . '</td>
							<td>' . round($closeingStockQty, 2) . '</td>
							<td>' . round($wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["RATE"], 4) . '</td>
							<td>' . round($val["CONS_RATE"], 2) . '</td>
							<td>' . round($amnt, 2) . '</td>
							<td>' . $store_arr[$val["STORE_ID"]] . '</td>
							<td>' . $lib_floor[$val["FLOOR_ID"]] . '</td>
							<td>' . $lib_room[$val["ROOM"]] . '</td>
							<td>' . $lib_rack[$val["RACK"]] . '</td>
							<td>' . $lib_self_no[$val["SELF"]] . '</td>
							<td>' . $lib_bin_box[$val["BIN_BOX"]] . '</td>
							<td>' . $shipment_status[$val['SHIPING_STATUS']] . '</td>
							</tr>';
							$n++;		
							$total_opening_stock += $openingStockQty;
							$total_rcv_qty += $val["RECEIVE_QTY"];
							$total_issue_rate += $val["ISSUE_RET_QTY"];
							$total_trans_in_qty += $val["TRANS_IN"];
							$total_rec_rate += $total_rec_qty;
							$total_issue += $val["ISSUE_QTY"];
							$total_rcv_ret_qty += $val["RECEIVE_RET_QTY"];
							$total_trns_out += $val["TRANS_OUT"];
							$total_iss_qty += $total_issue_qty;
							$total_clos_qty += $closeingStockQty;
							//$total_rate_dlr += $wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["RATE"];
							//$total_rate_tk += $val["CONS_RATE"];
							$total_amnt += $amnt;
						}
						else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
						{
							$summary_arr_forqmmount[$val["ITEM_GROUP_ID"]][$val["CONS_UOM"]]["CONS_AMMOUNTS"]+=$closeingStockQty*$val["CONS_RATE"];
							$html .='<tr>
							<td>'.$n.'</td>	
                            <td>' . $val["JOB_NO"] . '</td>
							<td>' . $val['GROUPING'] . '</td>
							<td>' . $val['PO_NUMBER'] . '</td>
							<td>' . $val['STYLE_REF_NO'] . '</td>
							<td>' . $buyer_array[$val['BUYER_NAME']] . '</td>
							<td>' . $lib_buyer_brand_arr[$val['BRAND_ID']] . '</td>
							<td>' . $season_arr[$val['SEASON']] . '</td>
							<td>' . $val['SEASON_YEAR'] . '</td>
							<td>' . $wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["WORK_ORDER_NO"] . '</td>
							<td>' . $pay_mode[$wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["PAY_MODE"]] . '</td>
							<td>' . $wo_pi_arr[$val["ORDER_ID"]][$val["ITEM_GROUP_ID"]]["PI_NUMBER"] . '&nbsp;</td>
							<td>' . $wo_pi_arr[$val["ORDER_ID"]][$val["ITEM_GROUP_ID"]]["LC_NUMBER"] . '&nbsp;</td>
							<td>' . $supplier_name_arr[$wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["SUPPLIER_ID"]] . '</td>
							<td>' . $trim_group_library[$val["ITEM_GROUP_ID"]] . '</td>
							<td>' . $val["PRODUCT_NAME_DETAILS"] . '</td>
							<td>' . $color_library[$val["COLOR"]] . '</td>
							<td>' . $size_library[$val["GMTS_SIZE"]] . '</td>
							<td>' . $color_library[$val["ITEM_COLOR"]] . '</td>
							<td>' . $val["ITEM_SIZE"]. '</td>
							<td>' . $arctical_no . '</td>
							<td>' . $unit_of_measurement[$val["CONS_UOM"]] . '</td>
							<td>' . round($openingStockQty, 2) . '</td>
							<td>' . round($val["RECEIVE_QTY"], 2) . '</td>
							<td>' . round($val["ISSUE_RET_QTY"], 2) . '</td>
							<td>' . round($val["TRANS_IN"], 2) . '</td>
							<td title="' . 'Receive Qnty+Issue Return+Transfer in' . '">' . round($total_rec_qty, 2) . '</td>
							<td>' . round($val["ISSUE_QTY"], 2) . '</td>
							<td>' . round($val["RECEIVE_RET_QTY"], 2) . '</td>
							<td>' . round($val["TRANS_OUT"], 0) . '</td>
							<td title="' . 'Issue Qnty+Rcv Return+Transfer out' . '">' . round($total_issue_qty, 2) . '</td>
							<td>' . round($closeingStockQty, 2) . '</td>
							<td>' . round($wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["RATE"], 4) . '</td>
							<td>' . round($val["CONS_RATE"], 2) . '</td>
							<td>' . round($amnt, 2) . '</td>
							<td>' . $store_arr[$val["STORE_ID"]] . '</td>
							<td>' . $lib_floor[$val["FLOOR_ID"]] . '</td>
							<td>' . $lib_room[$val["ROOM"]] . '</td>
							<td>' . $lib_rack[$val["RACK"]] . '</td>
							<td>' . $lib_self_no[$val["SELF"]] . '</td>
							<td>' . $lib_bin_box[$val["BIN_BOX"]] . '</td>
							<td>' . $shipment_status[$val['SHIPING_STATUS']] . '</td>
							</tr>';
							$n++;		
							$total_opening_stock += $openingStockQty;
							$total_rcv_qty += $val["RECEIVE_QTY"];
							$total_issue_rate += $val["ISSUE_RET_QTY"];
							$total_trans_in_qty += $val["TRANS_IN"];
							$total_rec_rate += $total_rec_qty;
							$total_issue += $val["ISSUE_QTY"];
							$total_rcv_ret_qty += $val["RECEIVE_RET_QTY"];
							$total_trns_out += $val["TRANS_OUT"];
							$total_iss_qty += $total_issue_qty;
							$total_clos_qty += $closeingStockQty;
							//$total_rate_dlr += $wo_order_arr[$val["BREAK_DOWN_ID"]][$val["ITEM_GROUP_ID"]]["RATE"];
							//$total_rate_tk += $val["CONS_RATE"];
							$total_amnt += $amnt;
						}
					}
					$html .='</tbody> 
				
				<tfoot> 
			   		<tr align="right">
					<th colspan="22"> Total:</th>
					<th>' . round($total_opening_stock, 2) . '</th>
					<th>' . round($total_rcv_qty, 2) . '</th>
					<th>' . round($total_issue_rate, 2) . '</th>
					<th>' . round($total_trans_in_qty, 2) . '</th>
					<th>' . round($total_rec_rate, 2) . '</th>
					<th>' . round($total_issue, 2) . '</th>
					<th>' . round($total_rcv_ret_qty, 2) . '</th>
					<th>' . round($total_trns_out, 2) . '</th>
					<th>' . round($total_iss_qty, 2) . '</th>
					<th>' . round($total_clos_qty, 2) . '</th>
					<th>' . round($total_rate_dlr, 2) . '</th>
					<th>' . round($total_rate_tk, 2) . '</th>
					<th>' . round($total_amnt, 2) . '</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					</tr>
				</tfoot>
			</table>
			</div>

		
			<div>
				<table> 
					<thead>
						<tr>
							<td>Trims Summary Stock Report</td>
						</tr>
					</thead>
				</table>
				<table> 
				  <thead>
					<tr>
					<th>Id</th>
					<th>Item Group Name</th>
					<th>UOM</th>
					<th>Opening Stock</th>
					<th>Receive Qnty</th>
					<th>Issue Return</th>
					<th>Transfer in</th>
					<th>Total Receive Qnty</th>
					<th>Issue Qnty</th>
					<th>Rcv Return</th>
					<th>Transfer out</th>
					<th>Total Issue Qnty</th>
					<th>Closing Stock</th>
					<th>Closing Stock Amount (Tk)</th>
					</tr>
				  </thead>
				
					<tbody>';
					$m=1;
					foreach($summary_data_arr as $row)
					{
						$openingStockQty = (($row["OPENING_TOTAL_RECEIVE"]-$row["OPENING_TOTAL_ISSUE"])*$conversion_factor_arr[$row['ITEM_GROUP_ID']]);
						$totalReceive = (($row["RECEIVE_QTY"]+$row["ISSUE_RET_QTY"]+$row["TRANS_IN"])*$conversion_factor_arr[$row['ITEM_GROUP_ID']]);
						$totalIssue = (($row["ISSUE_QTY"]+$row["RECEIVE_RET_QTY"]+$row["TRANS_OUT"])*$conversion_factor_arr[$row['ITEM_GROUP_ID']]);
	
						$closeingStockQty = ($openingStockQty+$totalReceive-$totalIssue);
						$total_rec_qty=$row["RECEIVE_QTY"]+$row["TRANS_IN"];
						$total_issue_qty=$row["ISSUE_QTY"]+$row["TRANS_OUT"];
						// $amnt_tk1 = ($closeingStockQty*$row["CONS_RATE"]);
	
						$bgcolor=($m%2==0)?"#E9F3FF":"#FFFFFF";
						$html .='<tr>
						<td>'.$m.'</td>	
						<td>' . $trim_group_library[$row["ITEM_GROUP_ID"]] . '</td>
						<td>' . $unit_of_measurement[$row["CONS_UOM"]] . '</td>
						<td>' . round($openingStockQty, 2) . '</td>
						<td>' . round($row["RECEIVE_QTY"], 2) . '</td>
						<td>' . round($row["ISSUE_RET_QTY"], 2) . '</td>
						<td>' . round($row["TRANS_IN"], 2) . '</td>
						<td>' . round($total_rec_qty, 2) . '</td>
						<td>' . round($row["ISSUE_QTY"], 2) . '</td>
						<td>' . round($row["RECEIVE_RET_QTY"], 2) . '</td>
						<td>' . round($row["TRANS_OUT"], 2) . '</td>
						<td>' . round($total_issue_qty, 2) . '</td>
						<td>' . round($closeingStockQty, 2) . '</td>
						<td>' . round($summary_arr_forqmmount[$row["ITEM_GROUP_ID"]][$row["CONS_UOM"]]["CONS_AMMOUNTS"], 2) . '</td>';
						$m++;
						$total_opening_stock2 += $openingStockQty;
						$total_rcv_qty2 += $row["RECEIVE_QTY"];
						$total_issue_rate2 += $row["ISSUE_RET_QTY"];
						$total_trans_in_qty2 += $row["TRANS_IN"];
						$total_rec_rate2 += $total_rec_qty;
						$total_issue2 += $row["ISSUE_QTY"];
						$total_rcv_ret_qty2 += $row["RECEIVE_RET_QTY"];
						$total_trns_out2 += $row["TRANS_OUT"];
						$total_iss_qty2 += $total_issue_qty;
						$total_clos_qty2 += $closeingStockQty;
						$total_amnt_tk += $summary_arr_forqmmount[$row["ITEM_GROUP_ID"]][$row["CONS_UOM"]]["CONS_AMMOUNTS"];
					}
					$html .='</tbody>
					<tfoot>
					<tr align="right">
					
						<th colspan="3"> Total:</th>
						<th>' . round($total_opening_stock2, 2) . '</th>
						<th>' . round($total_rcv_qty2, 2) . '</th>
						<th>' . round($total_issue_rate2, 2) . '</th>
						<th>' . round($total_trans_in_qty2, 2) . '</th>
						<th>' . round($total_rec_rate2, 2) . '</th>
						<th>' . round($total_issue2, 2) . '</th>
						<th>' . round($total_rcv_ret_qty2, 2) . '</th>
						<th>' . round($total_trns_out2, 2) . '</th>
						<th>' . round($total_iss_qty2, 2) . '</th>
						<th>' . round($total_clos_qty2, 2) . '</th>
						<th>' . round($total_amnt_tk, 2) . '</th>
					</tr>
					</tfoot>
				</table>
			</div>
		<fieldset>';	
		
		foreach (glob("rack_wise_statement_report_v2_$user_id*.xlsx") as $filename){
			@unlink($filename);
		}
		$name=time();
		$filename='rack_wise_statement_report_v2_'.$user_id."_".$name.".xlsx";
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
		$spreadsheet = $reader->loadFromString($html);
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$writer->save($filename);
		echo "####$filename####$rpt_type";  
		exit();

	}
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=96");
	oci_commit($con);
	disconnect($con);

	// if($rpt_type == 1){
		
	// }
	// else
	// {
		
	// }

}

if($action=="style_reference_search")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	    <script>
            var selected_id = new Array;
            var selected_name = new Array;
            var selected_no = new Array;
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

                    toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

                    if( jQuery.inArray( selectID, selected_id ) == -1 ) {
                        selected_id.push( selectID );
                        selected_name.push( selectDESC );
                        selected_no.push( str );
                    }
                    else {
                        for( var i = 0; i < selected_id.length; i++ ) {
                            if( selected_id[i] == selectID ) break;
                        }
                        selected_id.splice( i, 1 );
                        selected_name.splice( i, 1 );
                        selected_no.splice( i, 1 );
                    }
                    var id = ''; var name = ''; var job = ''; var num='';
                    for( var i = 0; i < selected_id.length; i++ ) {
                        id += selected_id[i] + ',';
                        name += selected_name[i] + ',';
                        num += selected_no[i] + ',';
                    }
                    id 		= id.substr( 0, id.length - 1 );
                    name 	= name.substr( 0, name.length - 1 );
                    num 	= num.substr( 0, num.length - 1 );
                    //alert(num);
                    $('#txt_selected_id').val( id );
                    $('#txt_selected').val( name );
                    $('#txt_selected_no').val( num );
            }

            function fn_selected()
            {
                var style_no='<? echo $txt_style_ref_no;?>';
                var style_id='<? echo $txt_style_ref_id;?>';
                var style_des='<? echo $txt_style_ref;?>';

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
            }
        </script>
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset>
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                            <tr>
                                <th>Style Ref No</th>
                                <th>Job No</th>
                                <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_style_ref_no" id="txt_style_ref_no" />
                                </td>
                                <td align="center">
                                     <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                                </td>
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?=$company;?>'+'**'+'<?=$buyer;?>'+'**'+document.getElementById('txt_style_ref_no').value+'**'+document.getElementById('txt_job_no').value+'**'+'<?=$cbo_year;?>', 'style_reference_search_list_view', 'search_div', 'rack_wise_statement_report_v2_controller', 'setFilterGrid(\'list_view\',-1)');fn_selected();" style="width:100px;" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
                <div style="margin-top:15px" id="search_div"></div>
            </form>
        </div>
    </body>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="style_reference_search_list_view")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($company,$buyer,$style_ref_no,$job_no,$cbo_year)=explode('**',$data);

	if($style_ref_no!=""){$search_con=" and style_ref_no like('%$style_ref_no%')";}
	if($job_no!=""){$search_con .=" and job_no like('%$job_no')";}

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if($buyer > 0)
        $buyer_cond=" and buyer_name=$buyer";
    else
        $buyer_cond="";

	if($cbo_year > 0){
        if($db_type==0)
            $year_cond=" and year(insert_date)='$cbo_year'";
        else
            $year_cond=" and to_char(insert_date,'YYYY')='$cbo_year'";
    }else{
        $year_cond="";
    }

	$sql = "select id,style_ref_no,job_no,job_no_prefix_num,$select_year(insert_date $year_con) as year from wo_po_details_master where company_name=$company $buyer_cond $year_cond  $search_con and is_deleted=0 order by job_no_prefix_num";

	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","235",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	exit();
}

if($action=="order_search")
{

	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
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
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str_or );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			$('#txt_selected_no').val( num );
		}
    </script>

    </head>

    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                    <thead>
                        <th>Buyer</th>
                        <th>Search By</th>
                        <th id="search_by_td_up">Please Enter In Ref</th>
                        <th>Shipment Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"></th>
                        <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                        <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                    </thead>
                    <tbody>
                        <tr>
                            <td align="center">
                                <?
                                    echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                                ?>
                            </td>
                            <td align="center">
                            <?
                                $search_by_arr=array(1=>"In Ref");
                                $dd="change_search_event(this.value, '0', '0*', '../../../') ";
                                echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                            </td>
                            <td align="center" id="search_by_td" width="130">
                                <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                            </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                            </td>
                            <td align="center">
                                <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value, 'order_search_list_view', 'search_div', 'rack_wise_statement_report_v2_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                                <!-- +'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $style_ref_id; ?>' -->
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

if ($action == "order_search_list_view") {
    extract($_REQUEST);
   
	// print_r($data)
    list($company, $buyer, $search_type, $search_value, $start_date, $end_date, $cbo_year_selection) = explode('**', $data);

    $buyer = str_replace("'", "", $buyer);
    $company = str_replace("'", "", $company);

    $cbo_year = str_replace("'", "", $cbo_year_selection);
    if (trim($cbo_year) != 0) {
        if ($db_type == 0) {
            $year_cond = " and YEAR(b.insert_date)=$cbo_year";
        } else {
            $year_cond = " and to_char(b.insert_date,'YYYY')=$cbo_year";
        }
    }

    if ($search_type == 1 && $search_value != '') {
        $search_con = " and a.grouping like('%$search_value')";
    } 

    if ($start_date != "" && $end_date != "") {
        if ($db_type == 0) {
            $date_cond = "and a.pub_shipment_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
        } else {
            $date_cond = "and a.pub_shipment_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
        }
    } 
	else{
        $date_cond = "";
    }
    $job_style_cond = "";
    if ($buyer != 0) $buyer_cond = "and b.buyer_name=$buyer"; else $buyer_cond = "";
	
     $sql = "select a.id,a.po_number,a.grouping as int_ref,a.job_no_mst,b.style_ref_no,b.quotation_id,b.JOB_NO as job_no_prefix_num,$cbo_year_selection as job_year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name in($company)   $search_con $date_cond $buyer_cond $year_cond and a.status_active=1";
    // echo $sql;
    echo create_list_view("list_view", "Order NO,int.Ref No,Job No,QuotationId,Year,Style Ref No", "150,100,80,70,70,150", "670", "150", 0, $sql, "js_set_value", "id,int_ref", "", 1, "0", $arr, "po_number,int_ref,job_no_prefix_num,quotation_id,job_year,style_ref_no", "", "setFilterGrid('list_view',-1)", "0", "", 1);
    echo "<input type='hidden' id='txt_selected_id' />";
    echo "<input type='hidden' id='txt_selected' />";
    echo "<input type='hidden' id='txt_selected_no' />";
    exit();
}

