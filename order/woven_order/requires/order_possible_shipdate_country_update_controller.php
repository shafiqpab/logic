<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
$isRateEditableAfterBomApp=0;

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name","id,location_name", 1, "-Select-", $selected, "" );
	exit();
}
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $selected, "" );
		exit();
}
if ($action=="load_drop_down_buyer_pop")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
}

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		
		var selected_name = new Array;var selected_id = new Array;
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
			//alert(str);
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_name ) == -1 ) {
				selected_name.push( str[1] );
				selected_id.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_name.length; i++ ) {
					if( selected_name[i] == str[1] ) break;
				}
				selected_name.splice( i, 1 );
				selected_id.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_name.length; i++ ) {
				name += selected_name[i] + ',';
				id += selected_id[i] + ',';
			}
			
			name = name.substr( 0, name.length - 1 );
			id = id.substr( 0, id.length - 1 );
			
			$('#job_id').val( name );
			$('#job_no').val( id );
		}
		// function js_set_value( job_data )
		// {
		// 	var all_data=job_data.split("_");
		// 	document.getElementById('job_id').value=all_data[0];
		// 	document.getElementById('job_no').value=all_data[1];
		// 	parent.emailwindow.hide();
		// }
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="1080" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="13" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="140" class="must_entry_caption">Buyer Name</th>
                    <th width="70">Brand</th>
                    <th width="70">Season</th>
                    <th width="60">Season Year</th>
                    <th width="60">Job No</th>
                    <th width="90">Style Ref.</th>
                    <th width="80">M.Style/Internal Ref</th>
                    <th width="80">File No</th>
                    <th width="90">Order No</th>
                    <th width="130" colspan="2">Ship Date Range</th>
                    <th><input type="hidden" value="0" id="chk_job_wo_po"></th>
                </tr>
            </thead>
            <tr class="general">
                <td>
                    <input type="hidden" id="job_id">
                    <input type="hidden" id="job_no">
                    <input type="hidden" id="garments_nature" value="<?=$garments_nature; ?>">
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'order_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name","id,buyer_name",'', 1, "-- Select Buyer --","load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" ); ?></td>
        		<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 70, $blank_array,'', 1, "Brand",$selected, "" ); ?>
        		<td id="season_td"><? echo create_drop_down( "cbo_season_id", 70, $blank_array,'', 1, "Season",$selected, "" ); ?></td>
        		<td><?=create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date"></td>
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_season_year').value, 'create_po_search_list_view', 'search_div', 'order_possible_shipdate_country_update_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="13"><?=load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script>
		load_drop_down('order_possible_shipdate_country_update_controller', <?=$cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<?=$cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'";
	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else $buyer="";
	}
	else $buyer=" and a.buyer_name='$data[1]'";
	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	$order_cond=""; $job_cond=""; $style_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond="";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no='$data[10]'  "; //else  $style_cond="";
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]%'  "; //else  $style_cond="";
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '$data[10]%'  "; //else  $style_cond="";
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]'  "; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[11]);
	$file_no = str_replace("'","",$data[12]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

	if($data[13] !=0) $brand_cond = " and a.brand_id='$data[13]'"; else $brand_cond="";
	if($data[14] !=0) $season_cond = " and a.season_buyer_wise='$data[14]'"; else $season_cond="";
	if($data[15] !=0) $season_year_cond = " and a.season_year='$data[15]'"; else $season_year_cond="";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$company_arr=return_library_array( "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name",'id','company_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');

	$arr=array(2=>$buyer_arr,3=>$brand_arr,4=>$season_arr, 7=>$color_library,13=>$item_category);
	if($db_type==0)
	{
		$sql= "select a.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id, a.job_quantity, a.order_uom, a.order_repeat_no, a.brand_id, a.season_year,a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, DATEDIFF(pub_shipment_date,po_received_date) as date_diff, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id  and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC";
	}
	else if($db_type==2)
	{
		$sql= "select a.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id,a.job_quantity,a.order_uom, a.order_repeat_no, a.brand_id, a.season_year, a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id  and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC";
	}
	//echo $sql;
	echo  create_list_view("tbl_list_search", "Job No,Year,Buyer,Brand,Season,Season Year,Style Ref.,B/W Color,Quo. ID,Job Qty.,Repeat No,PO No.,PO Qty.,Shipment Date,Ref no, File No,Lead time", "40,40,100,70,75,50,100,70,50,70,50,90,70,60,50,50,50","1170","300",0, $sql , "js_set_value", "id,job_no", "", 1, "0,0,buyer_name,brand_id,season_buyer_wise,0,0,body_wash_color,0,0,0,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,brand_id,season_buyer_wise,season_year,style_ref_no,body_wash_color,quotation_id,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,grouping,file_no,date_diff", "",'','0,0,0,0,0,0,0,0,1,0,0,0,0,3,0,0,0','',1);
	exit();
}

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( order_data )
		{
			
			var all_data=order_data.split("_");
			document.getElementById('job_id').value=all_data[0];
			document.getElementById('job_no').value=all_data[1];
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="13" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="140" class="must_entry_caption">Buyer Name</th>
                    <th width="70">Brand</th>
                    <th width="70">Season</th>
                    <th width="60">Season Year</th>
                  
                    <th width="80">File No</th>
                    <th width="90">Order No</th>
                    <th width="130" colspan="2">Ship Date Range</th>
                    <th><input type="hidden" value="0" id="chk_job_wo_po"></th>
                </tr>
            </thead>
            <tr class="general">
                <td>
                    <input type="hidden" id="job_id">
                    <input type="hidden" id="job_no">
                    <input type="hidden" id="garments_nature" value="<?=$garments_nature; ?>">
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'order_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name","id,buyer_name",'', 1, "-- Select Buyer --","load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" ); ?></td>
        		<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 70, $blank_array,'', 1, "Brand",$selected, "" ); ?>
        		<td id="season_td"><? echo create_drop_down( "cbo_season_id", 70, $blank_array,'', 1, "Season",$selected, "" ); ?></td>
        		<td><?=create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>

                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date"></td>
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_season_year').value, 'create_po_search_list_view2', 'search_div', 'order_possible_shipdate_country_update_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="13"><?=load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script>
		load_drop_down('order_possible_shipdate_country_update_controller', <?=$cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<?=$cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_po_search_list_view2")
{
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'";
	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else $buyer="";
	}
	else $buyer=" and a.buyer_name='$data[1]'";
	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[6]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	$order_cond=""; $job_cond=""; $style_cond="";
	
	if($data[7]==1)
	{
	
		if (trim($data[8])!="") $order_cond=" and b.po_number='$data[8]'  "; //else  $order_cond="";
	
	}else if($data[7]==4 || $data[8]==0)
	{
		
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]%'  ";
	
	}
	else if($data[7]==2)
	{
		
		if (trim($data[8])!="") $order_cond=" and b.po_number like '$data[8]%'  ";
		
	}
	else if($data[7]==3)
	{
		
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]'  ";
		 //else  $style_cond="";
	}


	$file_no = str_replace("'","",$data[9]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

	if($data[10] !=0) $brand_cond = " and a.brand_id='$data[10]'"; else $brand_cond="";
	if($data[11] !=0) $season_cond = " and a.season_buyer_wise='$data[11]'"; else $season_cond="";
	if($data[12] !=0) $season_year_cond = " and a.season_year='$data[12]'"; else $season_year_cond="";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$company_arr=return_library_array( "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name",'id','company_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');

	$arr=array(2=>$buyer_arr,3=>$brand_arr,4=>$season_arr, 7=>$color_library,13=>$item_category);
	if($db_type==0)
	{
		$sql= "select b.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id, a.job_quantity, a.order_uom, a.order_repeat_no, a.brand_id, a.season_year,a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, DATEDIFF(pub_shipment_date,po_received_date) as date_diff, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id  and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer  $order_cond  $file_no_cond  $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC";
	}
	else if($db_type==2)
	{
		$sql= "select b.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id,a.job_quantity,a.order_uom, a.order_repeat_no, a.brand_id, a.season_year, a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id  and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer $order_cond  $file_no_cond  $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC";
	}
	echo  create_list_view("list_view", "Job No,Year,Buyer,Brand,Season,Season Year,Style Ref.,B/W Color,Quo. ID,Job Qty.,Repeat No,PO No.,PO Qty.,Shipment Date,Ref no, File No,Lead time", "40,40,100,70,75,50,100,70,50,70,50,90,70,60,50,50,50","1170","300",0, $sql , "js_set_value", "id,po_number", "", 1, "0,0,buyer_name,brand_id,season_buyer_wise,0,0,body_wash_color,0,0,0,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,brand_id,season_buyer_wise,season_year,style_ref_no,body_wash_color,quotation_id,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,grouping,file_no,date_diff", "",'','0,0,0,0,0,0,0,0,1,0,0,0,0,3,0,0,0');
	exit();
}

if($action=='report_generate'){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$hidden_job_id=str_replace("'","",$hidden_job_id);
	$job_cond=""; $job_cond2=""; $job_cond3="";

	if($hidden_job_id !==''){
		$job_cond="and job_id=$hidden_job_id";
		$job_cond2="and a.id in(".str_replace("'","",$hidden_job_id).")";
		$job_cond3="and a.id=$hidden_job_id";
	}
	
	$approved_arr=sql_select("SELECT approved, id from WO_PRE_COST_MST where   status_active=1 and is_deleted=0 $job_cond");
	$budget_approved=0;
	foreach ($approved_arr as $row) {
		if($row[csf('approved')]==1 || $row[csf('approved')]==3){
			$budget_approved=1;
		}
	}
	//echo $budget_approved;
	$hidden_order_id = str_replace("'", "", $hidden_order_id);
	$order_cond="";
		if($hidden_order_id>0){
			$order_cond=" and b.id=$hidden_order_id";

		}
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	//echo $operation;
	$date_cond='';
	if($start_date!="" && $end_date!="") $date_cond="and c.country_ship_date between '$start_date' and '$end_date'"; else $date_cond="";
	 
	// $job_no=str_replace("'","",$txt_job_no);
	// if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no in ('$job_no') ";

	if($operation==2)
	{
		$color_size_data=sql_select("SELECT a.id as job_id, a.company_name, a.buyer_name, a.job_no_prefix_num,a.job_no, a.insert_date, a.style_ref_no, b.id as po_id, b.po_number, b.file_year, b.file_no, b.matrix_type, b.po_received_date, b.pub_shipment_date, b.shipment_date, c.id as color_size_id, c.country_id, c.order_quantity, c.order_rate, c.order_total, c.item_number_id, c.country_ship_date,c.possible_shipment_date from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $job_cond2 $order_cond $date_cond");
	
		// echo "SELECT a.id as job_id, a.company_name, a.buyer_name, a.job_no_prefix_num,a.job_no, a.insert_date, a.style_ref_no, b.id as po_id, b.po_number, b.file_year, b.file_no, b.matrix_type, b.po_received_date, b.pub_shipment_date, b.shipment_date, c.id as color_size_id, c.country_id, c.order_quantity, c.order_rate, c.order_total, c.item_number_id, c.country_ship_date,c.possible_shipment_date from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in(".str_replace("'","",$hidden_job_id).") $order_cond $date_cond";
		
		
		$shipdateArr=array();
		foreach ($color_size_data as $row) {
			$company_id=$row[csf('company_name')];
			$str="";
			$str=$row[csf('buyer_name')].'__'.$row[csf('job_no_prefix_num')].'__'.$row[csf('job_id')].'__'.$row[csf('insert_date')].'__'.$row[csf('style_ref_no')].'__'.$row[csf('po_number')].'__'.$row[csf('file_year')].'__'.$row[csf('file_no')].'__'.$row[csf('matrix_type')].'__'.$row[csf('po_received_date')].'__'.$row[csf('pub_shipment_date')].'__'.$row[csf('shipment_date')].'__'.$row[csf('country_ship_date')].'__'.$row[csf('country_id')].'__'.$row[csf('item_number_id')].'__'.$row[csf('possible_shipment_date')];
			$shipdateArr[$row[csf('po_id')]][$str]['qty']+=$row[csf('order_quantity')];
			$shipdateArr[$row[csf('po_id')]][$str]['val']+=$row[csf('order_total')];
			if($shipdateArr[$row[csf('po_id')]][$str]['cid']=="") $shipdateArr[$row[csf('po_id')]][$str]['cid']=$row[csf('color_size_id')];
			else $shipdateArr[$row[csf('po_id')]][$str]['cid'].=','.$row[csf('color_size_id')];
		}
		
		$wo_po_ratio_sql=sql_select("SELECT c.id as ratio_breakdown_id, c.country_id, c.gmts_item_id, c.color_id, c.size_id, c.po_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_ratio_breakdown c on b.id=c.po_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $job_cond3 $order_cond");
		if(count($wo_po_ratio_sql)>0){
			foreach ($wo_po_ratio_sql as $row) {
				$key=$row[csf('po_id')].'*'.$row[csf('country_id')].'*'.$row[csf('gmts_item_id')].'*'.$row[csf('color_id')].'*'.$row[csf('size_id')];
				$ratio_id_arr[$key]=$row[csf('ratio_breakdown_id')];
			}
		}
		 
		?>
		<table width="1000" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
            	<tr>
					<td colspan="8" align="right">Possible  Date Copy Level</td> 
                    
                    <td align="center" colspan="3"><b style="float:right; padding-right:1px;">Job | PO | Country</b></td>
                    <td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<th colspan="8">&nbsp;</th> 
                    <th colspan="3" ><b style="float:right; padding-right:20px;"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(1);" id="chk_job"> 
                    <input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(2);" id="chk_po"> 
                     <input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(3);" id="chk_country"></b></th>
                    <th colspan="3">&nbsp;</th>
				</tr>
                <tr>
                    <th width="30">SL</th>   
                    <th width="80">Buyer</th>
                    <th width="60">Job NO</th>
                    <th width="40">Job Year</th>
                    <th width="100">Style No</th>
                    <th width="100">PO No</th>
                    <th width="100">Country</th>
                    <th width="60">Qty.</th>
                    <th width="60">PO Received Date</th>
                    <th width="60">Publish Shipdate</th>
                    <th width="60">Possible Date</th>
                    <th width="60">Country Ship Date</th>
                    
                    <th width="40">Rate</th>
                    <th width="">Amount</th>
                   
                    
                </tr>
            </thead>
            <tbody id="color_size_data">
            	<? $i++;
            	$disabled="";
            	foreach ($shipdateArr as $poid=>$podata)
				{
					foreach ($podata as $strdata=>$strval)
					{
						$exstr=explode("__",$strdata);
						
						$buyer=$jobno=$job_id=$insertdate=$styleref=$pono=$fileyear=$fileno=$matrixtype=$po_received_date=$pubshipdate=$shipdate=$countryshipdate=$country_id="";
						$buyer=$exstr[0];
						$jobno=$exstr[1];
						$job_id=$exstr[2];
						$insertdate=$exstr[3];
						$styleref=$exstr[4];
						$pono=$exstr[5];
						$fileyear=$exstr[6];
						$fileno=$exstr[7];
						$matrixtype=$exstr[8];
						$po_received_date=$exstr[9];
						$pubshipdate=$exstr[10];
						$shipdate=$exstr[11];
						$countryshipdate=$exstr[12];
						$country_id=$exstr[13];
						$possible_ship_date=$exstr[15];
						
						$gmts_ratio_id=0;
						if($budget_approved==1){
							$disabled="disabled";
						}
						/*if($matrixtype==3){
							$datakey=$poid.'*'.$row[csf('country_id')].'*'.$row[csf('item_number_id')];
							$gmts_ratio_id=$ratio_id_arr[$datakey];
						}*/
						$ordrate=$strval['val']/$strval['qty'];
						$ord_rate=$strval['val']/$strval['qty'];
						//echo $strval['val']/$strval['qty'];
					 ?>
						<tr>
							<td align="center"><?=$i; ?></td>
							<td style="word-break:break-all"><?=$buyerArr[$buyer]; ?></td>
							<td style="word-break:break-all"><?=$jobno; ?></td>
							<td style="word-break:break-all"><?=date("Y", strtotime($insertdate)); ?></td>
							<td style="word-break:break-all"><?=$styleref; ?></td>
							<td style="word-break:break-all"><?=$pono; ?>
								<input type="hidden" id="poid_<?=$i; ?>" value="<?=$poid; ?>">
								<input type="hidden" id="jobid_<?=$i; ?>" value="<?=$job_id; ?>">
								<input type="hidden" id="colorsizeid_<?=$i; ?>" value="<?=$strval['cid']; ?>">
								<input type="hidden" id="ratioid_<?=$i; ?>" value="<?=$gmts_ratio_id; ?>">
								<input type="hidden" id="approved_<?=$i; ?>" value="<?=$budget_approved; ?>">
							</td>
							<td style="word-break:break-all"><?=$country_arr[$country_id]; ?>
								<input type="hidden" id="countryid_<?=$i; ?>" value="<?=$country_id; ?>">
							</td>
							<td style="word-break:break-all" align="right"><?=$strval['qty']; ?>
								<input type="hidden" id="gmtsqty_<?=$i; ?>" value="<?=$strval['qty']; ?>">
							</td>
							<td><input type="text" class="datepicker" id="txtporecdate_<?=$i; ?>" value="<?=change_date_format($po_received_date); ?>" style="width:60px;" readonly disabled></td>
							<td><input type="text" class="datepicker" id="txtpubshipdate_<?=$i; ?>" value="<?=change_date_format($pubshipdate); ?>" onChange="set_tna_task(<?=$i; ?>); copy_value(this.value,'txtpubshipdate_',<?=$i; ?>);" style="width:60px;" disabled ></td>
							<td><input type="text" class="datepicker" id="txtpossibleshipdate_<?=$i; ?>" value="<?=change_date_format($possible_ship_date); ?>" onChange="set_tna_task(<?=$i; ?>); copy_value(this.value,'txtpossibleshipdate_',<?=$i; ?>);" style="width:60px;"  ></td>
							<td><input type="text" class="datepicker" id="txtcountryshipdate_<?=$i; ?>" value="<?=change_date_format($countryshipdate); ?>" onChange="copy_value(this.value,'txtcountryshipdate_',<?=$i; ?>);" style="width:60px;" disabled></td>
							<td title="<?=number_format($ord_rate,4,'.',''); ?>"><input type="text" class="text_boxes_numeric" id="orderrate_<?=$i; ?>" value="<?=number_format($ord_rate,4,'.',''); ?>" style="width:60px;" disabled></td>
							<td><input type="text" class="text_boxes_numeric" id="ordeamount_<?=$i; ?>" value="<?=$strval['val']; ?>" style="width:60px;" disabled></td>
							 
						</tr>
					<? 
						$total_qty+=$strval['qty'];
						$total_amount+=$strval['val'];
						$i++;
					}
            	} 
            	?>
            </tbody>
            <tfoot>
            	<tr>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>
                    	<input type="text" class="text_boxes_numeric" id="total_qty" value="<?=$total_qty; ?>" style="width:60px;" readonly>
                    	<input type="hidden" id="hiddreportlevel" value="<?=$operation; ?>">
                    </td>
                    <td>&nbsp;</td>
            		<td>&nbsp;</td>
                    <td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td><input type="text" class="text_boxes_numeric" id="total_amount" value="<?=$total_amount; ?>" style="width:60px;" readonly></td>
            		 
            	</tr>
            </tfoot>
		</table>
        <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		<br>
     <? } ?>
		<? echo load_submit_buttons( $permission, "fnc_order_entry_details", 1,0 ,"",2); ?>
        <div> <input type="button" id="show_button" class="formbutton" style="width:70px" value="Print" onClick="fn_report_print('po_print');" /> </div>
	<?
}
//
if($action=='save_update_delete_dtls'){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	if ($operation==1)
	{
		if ($reporttype==2)
		{
			$field_array_up="possible_shipment_date*updated_by*update_date";
			$pofield_array_up="possible_shipment_date*updated_by*update_date";
			$counter=0; $rcounter=0; $rID1=1;
			for($m=1; $m<=$row_table; $m++)
			{
				//$txtpubshipdate="txtpubshipdate_".$m;
				$txtpossibleshipdate="txtpossibleshipdate_".$m;
				$fileyear="fileyear_".$m;
				$fileno="fileno_".$m;
				$poid="poid_".$m;
				
				$colorsizeid="colorsizeid_".$m;
				//$txtcountryshipdate="txtpossibleshipdate_".$m;
				$approved="approved_".$m;
				
				//$pubshipdate=date("d-M-Y",strtotime(str_replace("'",'',$$txtpossibleshipdate)));
				$poshipdate=date("d-M-Y",strtotime(str_replace("'",'',$$txtpossibleshipdate)));
				$countryshipdate=date("d-M-Y",strtotime(str_replace("'",'',$$txtpossibleshipdate)));
				
				$approved_id=str_replace("'",'',$$approved);
				$poidarr[]=str_replace("'",'',$$poid);
				
				$excolorsizeid=explode(",",str_replace("'",'',$$colorsizeid));
				foreach($excolorsizeid as $exczid)
				{
					$data_array_up[$exczid] =explode("*",("'".$countryshipdate."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				//	echo "10**=".$countryshipdate;die;
					$counter++;
					$id_arr[]=$exczid;
					if(str_replace("'",'',$$approved) !=1){
						if($data_array_up!="" && $counter==100){
							$rID1=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ));
							//echo "10**=".bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr );die;
							$counter=0;
							$id_arr=array();
							$data_array_up=array();
						}
					}
				}
				$podata_array_up[str_replace("'",'',$$poid)] =explode("*",("'".$poshipdate."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				//$podata_array_up="'".$pubshipdate."'*'".$poshipdate."'*".$$fileyear."*".$$fileno."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			 
			if(count($data_array_up)>0 && $counter!=100){
				$rID1=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ));
				//echo "10**==".$counter; die;
			}
			//echo "10**".print_r($poidarr); die;
			if($podata_array_up!=""){
				$rID3=execute_query(bulk_update_sql_statement("wo_po_break_down", "id",$pofield_array_up,$podata_array_up,$poidarr ));
				//echo "10**".bulk_update_sql_statement("wo_po_break_down", "id",$pofield_array_up,$podata_array_up,$poidarr ); die;
			}
			//echo "10**".$rID1.'='.$rID3; die;
		}
		
		if($db_type==0)
		{
			if($rID1==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$hidden_job_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$hidden_job_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1==1){
				oci_commit($con);
				echo "1**".str_replace("'",'',$hidden_job_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$hidden_job_id);
			}
		}
		disconnect($con);
		die;
	}
}
 if($action=='po_print_action')
 {
	 extract($_REQUEST);
	$data=explode('*',$data);
	//echo "<pre>";
	//print_r($data);
	$cbo_company_name=$data[0];
	$txt_job_no=$data[1];
	$report_title=$data[2];
	$hidden_job_id=$data[3];
	$hidden_order_id=$data[4];
	$txt_date_from=$data[5];
	$txt_date_to=$data[6];
	
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$CompArr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$brand_name_arr=return_library_array( "select id,brand_name from lib_brand", "id", "brand_name");
	$FactoryMarArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$dealing_marArr = return_library_array("select id,team_member_name from  lib_mkt_team_member_info ","id","team_member_name");
	
	$approved_arr=sql_select("SELECT approved, id from WO_PRE_COST_MST where job_id=$hidden_job_id and status_active=1 and is_deleted=0");
	$budget_approved=0;
	foreach ($approved_arr as $row) {
		if($row[csf('approved')]==1 || $row[csf('approved')]==3){
			$budget_approved=1;
		}
	}
	$hidden_order_id = str_replace("'", "", $hidden_order_id);
	$order_cond="";
		if($hidden_order_id>0){
			$order_cond=" and b.id=$hidden_order_id";

		}
	$start_date1=str_replace("'","",trim($txt_date_from));
	$start_date=date('d-M-Y',strtotime($start_date1));
	$end_date2=str_replace("'","",trim($txt_date_to));
	$end_date=date('d-M-Y',strtotime($end_date2));
	//echo $operation;
	$date_cond='';
	if(strtotime($start_date1)!="" && strtotime($end_date2)!="") $date_cond="and c.country_ship_date between '$start_date' and '$end_date'"; else $date_cond="";
	
	$color_size_data=sql_select("SELECT a.id as job_id, a.job_no,a.company_name, a.style_description,a.buyer_name,a.dealing_marchant,a.brand_id,a.factory_marchant, a.job_no_prefix_num, a.insert_date, a.style_ref_no, b.id as po_id, b.po_number, b.possible_shipment_date as possible_shipment_date_po,b.po_received_date, b.pub_shipment_date, b.shipment_date, c.id as color_size_id, c.country_id, c.order_quantity, c.order_rate, c.order_total, c.item_number_id, c.country_ship_date,c.possible_shipment_date from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$hidden_job_id $order_cond $date_cond");
	 

		$shipdateArr=array();
		foreach ($color_size_data as $row) {
			$company_id=$row[csf('company_name')];
			
			$poWiseArr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
			$poWiseArr[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$poWiseArr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
			$poWiseArr[$row[csf('po_id')]]['possible_shipment_date']=$row[csf('possible_shipment_date')];
			$poWiseArr[$row[csf('po_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
			$poWiseArr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
			$poWiseArr[$row[csf('po_id')]]['possible_date_po']=$row[csf('possible_shipment_date_po')];
			$poWiseArr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
			$poWiseArr[$row[csf('po_id')]]['brand_id']=$brand_name_arr[$row[csf('brand_id')]];
			$poWiseArr[$row[csf('po_id')]]['dealing_marchant']=$dealing_marArr[$row[csf('dealing_marchant')]];
			$poWiseArr[$row[csf('po_id')]]['factory_marchant']=$FactoryMarArr[$row[csf('factory_marchant')]];
			$poWiseArr[$row[csf('po_id')]]['style_description']=$row[csf('style_description')];
			$poWiseArr[$row[csf('po_id')]]['order_quantity']+=$row[csf('order_quantity')];
			$poWiseArr[$row[csf('po_id')]]['rate']=$row[csf('order_total')]/$row[csf('order_quantity')];
			$poWiseArr[$row[csf('po_id')]]['po_value']+=$row[csf('order_total')];
		}
		$path ="../../../";
		?>
         <table style="width:1050px" ><tr>
         <td colspan="13" align="center">
                   <?
				   	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='".$company_id."'","image_location");
					?>
                     <img  src='<?=$path.$image_location; ?>' height='50' align="left" />
                    <b style="font-size:25px;"><?=$CompArr[str_replace("'","",$company_id)]; ?></b><br>
                    <b style="font-size:14px;"> <? echo $report_title;?></b>
             </td></tr></table>
        <table width="1050"  cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
        <caption>  &nbsp;<b>  <?   if(strtotime($start_date)>0) echo $start_date;else echo " ";?> <i style="float:right"> <? echo date('d-m-Y');?></i></b></caption>
			<thead>
                <tr>
                    <th width="20">SL</th>   
                    <th width="100">D. MARCHAIN NAME </th>
                    <th width="100">FAC. MARCHAIN NAME </th>
                    <th width="100">BUYER NAME </th>
                    <th width="100">BRAND </th>
                    <th width="100">INDEX NO/ STYNE NO</th>
                    <th width="100">ORDER NUMBER</th>
                    <th width="100">GOODS DESCRIPTION</th>
                    <th width="70">ORDER QTY</th>
                    <th width="50">UNIT PRICE </th>
                    <th width="100">Total Value </th>
                    <th width="70">Pub.Shipdate</th>
                    <th width="">Possible Date</th>
                    
                </tr>
            </thead>
            <tbody>
            	<? $i++;
            	$disabled="";$total_qty=$total_amount=0;
            	foreach ($poWiseArr as $poid=>$row)
				{
					 ?>
						<tr>
							<td align="center"><?=$i; ?></td>
							<td style="word-break:break-all"><?=$row['dealing_marchant']; ?></td>
							<td style="word-break:break-all"><?=$row['factory_marchant']; ?></td>
							<td style="word-break:break-all"><?=$buyerArr[$row['buyer_name']]; ?></td>
							<td style="word-break:break-all"><?=$row['brand_id']; ?></td>
							<td style="word-break:break-all"><?=$row['style_ref_no']; ?>
							</td>
							<td style="word-break:break-all"><?=$row['po_number']; ?>
							</td>
							<td style="word-break:break-all" align="right"><?=$row['style_description']; ?>
							</td>
							<td align="right"><?=$row['order_quantity']; ?></td>
							<td align="right"><?=number_format($row['po_value']/$row['order_quantity'],2); ?></td>
							<td align="right"><?=number_format($row['po_value'],2); ?></td>
							<td><?=change_date_format($row['pub_shipment_date']); ?></td>
							<td><?=change_date_format($row['possible_shipment_date']); ?></td>
							 
						</tr>
					<? 
						$total_qty+=$row['order_quantity'];
						$total_amount+=$row['po_value'];
						$i++;
				 
            	} 
            	?>
            </tbody>
            <tfoot>
            	<tr>
            		<th colspan="8" align="right">GRAND TOTAL:</th>
                    <th align="right"><?=number_format($total_qty,0); ?></th>
            		<th>&nbsp;</td>
                    <th align="right"><?=number_format($total_amount,2); ?></th>
            		<th>&nbsp;</th>
            		 
            		<th> </th>
            		 
            	</tr>
            </tfoot>
		</table>
        
        <?
			
 exit();
 }