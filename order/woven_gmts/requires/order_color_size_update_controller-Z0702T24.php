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
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name","id,location_name", 1, "-Select Location-", $selected, "" );
	exit();
}
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $selected, "" );
		exit();
}
if ($action=="load_drop_down_buyer_pop")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $selected, "load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $selected, "load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
}

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( job_data )
		{
			var all_data=job_data.split("_");
			document.getElementById('job_id').value=all_data[0];
			document.getElementById('job_no').value=all_data[1];
			parent.emailwindow.hide();
		}
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
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-Select Company-", $cbo_company_name,"load_drop_down( 'order_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name","id,buyer_name",'', 1, "-Select Buyer-","load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" ); ?></td>
        		<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 70, $blank_array,'', 1, "-Brand-",$selected, "" ); ?>
        		<td id="season_td"><? echo create_drop_down( "cbo_season_id", 70, $blank_array,'', 1, "-Season-",$selected, "" ); ?></td>
        		<td><?=create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date"></td>
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_season_year').value, 'create_po_search_list_view', 'search_div', 'order_color_size_update_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
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
		load_drop_down('order_color_size_update_controller', <?=$cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
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
		$sql= "select a.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id, a.job_quantity, a.order_uom, a.order_repeat_no, a.brand_id, a.season_year,a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, DATEDIFF(pub_shipment_date,po_received_date) as date_diff, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC";
	}
	else if($db_type==2)
	{
		$sql= "select a.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id,a.job_quantity,a.order_uom, a.order_repeat_no, a.brand_id, a.season_year, a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC";
	}
	//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Buyer,Brand,Season,Season Year,Style Ref.,B/W Color,Quo. ID,Job Qty.,Repeat No,PO No.,PO Qty.,Shipment Date,Ref no, File No,Lead time", "40,40,100,70,75,50,100,70,50,70,50,90,70,60,50,50,50","1170","300",0, $sql , "js_set_value", "id,job_no", "", 1, "0,0,buyer_name,brand_id,season_buyer_wise,0,0,body_wash_color,0,0,0,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,brand_id,season_buyer_wise,season_year,style_ref_no,body_wash_color,quotation_id,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,grouping,file_no,date_diff", "",'','0,0,0,0,0,0,0,0,1,0,0,0,0,3,0,0,0');
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
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_season_year').value, 'create_po_search_list_view2', 'search_div', 'order_color_size_update_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
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
		load_drop_down('order_color_size_update_controller', <?=$cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
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
		$sql= "select b.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id, a.job_quantity, a.order_uom, a.order_repeat_no, a.brand_id, a.season_year,a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, DATEDIFF(pub_shipment_date,po_received_date) as date_diff, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer  $order_cond  $file_no_cond  $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC";
	}
	else if($db_type==2)
	{
		$sql= "select b.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id,a.job_quantity,a.order_uom, a.order_repeat_no, a.brand_id, a.season_year, a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer $order_cond  $file_no_cond  $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC";
	}
	echo  create_list_view("list_view", "Job No,Year,Buyer,Brand,Season,Season Year,Style Ref.,B/W Color,Quo. ID,Job Qty.,Repeat No,PO No.,PO Qty.,Shipment Date,Ref no, File No,Lead time", "40,40,100,70,75,50,100,70,50,70,50,90,70,60,50,50,50","1170","300",0, $sql , "js_set_value", "id,po_number", "", 1, "0,0,buyer_name,brand_id,season_buyer_wise,0,0,body_wash_color,0,0,0,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,brand_id,season_buyer_wise,season_year,style_ref_no,body_wash_color,quotation_id,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,grouping,file_no,date_diff", "",'','0,0,0,0,0,0,0,0,1,0,0,0,0,3,0,0,0');
	exit();
}

if($action=='report_generate'){
	//$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	
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
	
	//echo $operation;
	if($operation==1)//Show
	{
		$color_size_data=sql_select("SELECT a.id as job_id, a.company_name, a.buyer_name, a.job_no_prefix_num, a.insert_date, a.style_ref_no, b.id as po_id, b.po_number, b.file_year, b.file_no, b.sc_lc, b.matrix_type,  c.id as color_size_id, c.country_id, c.color_number_id, c.size_number_id, c.order_quantity, c.order_rate, c.order_total, c.item_number_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$hidden_job_id $order_cond");
		
		foreach ($color_size_data as $row) {
			$company_id=$row[csf('company_name')];
		}
		
		$wo_po_ratio_sql=sql_select("SELECT c.id as ratio_breakdown_id, c.country_id, c.gmts_item_id, c.color_id, c.size_id, c.po_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_ratio_breakdown c on b.id=c.po_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$hidden_job_id $order_cond");
		if(count($wo_po_ratio_sql)>0){
			foreach ($wo_po_ratio_sql as $row) {
				$key=$row[csf('po_id')].'*'.$row[csf('country_id')].'*'.$row[csf('gmts_item_id')].'*'.$row[csf('color_id')].'*'.$row[csf('size_id')];
				$ratio_id_arr[$key]=$row[csf('ratio_breakdown_id')];
			}
		}
		
		$file_year_sql="SELECT distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=$company_id and status_active=1 and is_deleted=0 union select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=$company_id and status_active=1 and is_deleted=0";
		?>
		<table width="1200" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<td colspan="7" align="right">Rate Copy Level</td> 
                    <td width="80" align="center">Job</td>
                    <td width="60" align="center">PO</td>
                    <td width="40" align="center">Country</td>
                    <td width="40" align="center">Color</td>
                    <td width="40" align="center">Size</td>
                    <td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<th colspan="7">&nbsp;</th> 
                    <th width="80"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(1);" id="chk_job"></th>
                    <th width="60"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(2);" id="chk_po"></th>
                    <th width="40"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(3);" id="chk_country"></th>
                    <th width="40"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(4);" id="chk_color"></th>
                    <th width="40"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(5);" id="chk_size"></th>
                    <th colspan="4">&nbsp;</th>
				</tr>
                <tr>
                    <th width="30">SL</th>   
                    <th width="80">Buyer</th>
                    <th width="60">Job NO</th>
                    <th width="40">Job Year</th>
                    <th width="80">Style No</th>
                    <th width="80">PO No</th>
                    <th width="80">Country</th>
                    <th width="60">Color</th>
                    <th width="40">Size</th>
                    <th width="40">Qty.</th>
                    <th width="40">Rate</th>
                    <th width="60">Amount</th>
                    <th width="40">File Year</th>
                    <th width="60">File No</th>
                    <th width="60">SC/LC No</th>
                </tr>
            </thead>
            <tbody id="color_size_data">
            	<? $i++;
            	$disabled="";
            	foreach ($color_size_data as $row) {
            		$gmts_ratio_id=0;
            		if($budget_approved==1 && $isRateEditableAfterBomApp==1){
            			$disabled="disabled";
            		}
            		if($row[csf('matrix_type')]==3){
            			$datakey=$row[csf('po_id')].'*'.$row[csf('country_id')].'*'.$row[csf('item_number_id')].'*'.$row[csf('color_number_id')].'*'.$row[csf('size_number_id')];
            			$gmts_ratio_id=$ratio_id_arr[$datakey];
            		}
					$cellcolor=""; $distex=""; $disdrop=0;
					if($shipstatus==3) { $cellcolor="red"; $distex="disabled"; $disdrop="1";}
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            	 ?>
            		<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>">
            			<td align="center"><?=$i;?></td>
            			<td style="word-break:break-all"><?=$buyerArr[$row[csf('buyer_name')]];?></td>
            			<td style="word-break:break-all"><?=$row[csf('job_no_prefix_num')]; ?></td>
            			<td style="word-break:break-all"><?=date("Y", strtotime($row[csf('insert_date')]));?></td>
            			<td style="word-break:break-all"><?=$row[csf('style_ref_no')];?></td>
            			<td style="word-break:break-all"><?=$row[csf('po_number')];?>
            				<input type="hidden" id="poid_<?=$i ?>" value="<?= $row[csf('po_id')]?>">
            				<input type="hidden" id="jobid_<?=$i ?>" value="<?= $row[csf('job_id')]?>">
            				<input type="hidden" id="colorsizeid_<?=$i ?>" value="<?= $row[csf('color_size_id')]?>">
            				<input type="hidden" id="ratioid_<?=$i ?>" value="<?= $gmts_ratio_id?>">
            				<input type="hidden" id="approved_<?=$i ?>" value="<?= $budget_approved?>">
            			</td>
            			<td style="word-break:break-all"><?=$country_arr[$row[csf('country_id')]];?>
            				<input type="hidden" id="countryid_<?=$i ?>" value="<?= $row[csf('country_id')]?>">
            			</td>
            			<td style="word-break:break-all"><?=$colorArr[$row[csf('color_number_id')]];?>
            				<input type="hidden" id="gmtscolorid_<?=$i ?>" value="<?= $row[csf('color_number_id')]?>">
            			</td>
            			<td style="word-break:break-all" align="center"><?=$itemSizeArr[$row[csf('size_number_id')]];?>
            				<input type="hidden" id="gmtssizesid_<?=$i ?>" value="<?= $row[csf('size_number_id')]?>">
            			</td>
            			<td style="word-break:break-all" align="right"><?=$row[csf('order_quantity')];?>
            				<input type="hidden" id="gmtsqty_<?=$i ?>" value="<?= $row[csf('order_quantity')]?>">
            			</td>
            			<td><input type="text" class="text_boxes_numeric" id="orderrate_<?= $i;?>" value="<?= $row[csf('order_rate')];?>" onChange="copy_value(this.value,'orderrate_',<?= $i ?>)" style="width:60px;" <? echo $disabled ?>></td>
            			<td><input type="text" id="ordeamount_<?= $i;?>" class="text_boxes_numeric" style="width:60px;" value="<?= $row[csf('order_total')];?>" readonly></td>
            			<td><? echo create_drop_down( "fileyear_".$i,80,$file_year_sql,"lc_sc_year,lc_sc_year", 1, "-- Select --",$row[csf('file_year')],"copy_value(this.value,'fileyear_',$i)"); ?>
            			</td>
            			<td><input type="text" class="text_boxes" id="fileno_<?= $i;?>" value="<?= $row[csf('file_no')];?>" style="width:60px;" onChange="copy_value(this.value,'fileno_',<?= $i ?>)"></td>
            			<td><input type="text" class="text_boxes" id="sclcno_<?= $i;?>" value="<?= $row[csf('sc_lc')];?>" style="width:60px;" onChange="copy_value(this.value,'sclcno_',<?= $i ?>)"></td>
            		</tr>
            	<? 
            		$total_qty+=$row[csf('order_quantity')];
            		$total_amount+=$row[csf('order_total')];
            		$i++;
            	} 
            	?>
            </tbody>
            <tfoot>
            	<tr bgcolor="#CCCCCC">
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>
                    	<input type="text" class="text_boxes_numeric" id="total_qty" value="<?=$total_qty;?>" style="width:60px;" readonly>
                    	<input type="hidden" id="hiddreportlevel" value="<?=$operation; ?>">
                    </td>
            		<td>&nbsp;</td>
            		<td><input type="text" class="text_boxes_numeric" id="total_amount" value="<?= $total_amount;?>" style="width:60px;" readonly></td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            	</tr>
            </tfoot>
		</table>
		<br>
    <? } 
    else if($operation==2)//Po Wise
	{
		$color_size_data=sql_select("SELECT a.id as job_id, a.company_name, a.buyer_name, a.job_no_prefix_num, a.insert_date, a.style_ref_no, b.id as po_id, b.po_number, b.file_year, b.file_no, b.matrix_type, b.po_received_date, b.pub_shipment_date, b.shipment_date, b.po_quantity as order_quantity, b.po_total_price as order_total, b.shiping_status, b.pack_handover_date from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$hidden_job_id $order_cond");
		$shipdateArr=array();
		foreach ($color_size_data as $row) {
			$company_id=$row[csf('company_name')];
			$str="";
			$str=$row[csf('buyer_name')].'__'.$row[csf('job_no_prefix_num')].'__'.$row[csf('job_id')].'__'.$row[csf('insert_date')].'__'.$row[csf('style_ref_no')].'__'.$row[csf('po_number')].'__'.$row[csf('file_year')].'__'.$row[csf('file_no')].'__'.$row[csf('matrix_type')].'__'.$row[csf('po_received_date')].'__'.$row[csf('pub_shipment_date')].'__'.$row[csf('shipment_date')].'__'.$row[csf('shiping_status')].'__'.$row[csf('pack_handover_date')];
			$shipdateArr[$row[csf('po_id')]][$str]['qty']+=$row[csf('order_quantity')];
			$shipdateArr[$row[csf('po_id')]][$str]['val']+=$row[csf('order_total')];
			/*if($shipdateArr[$row[csf('po_id')]][$str]['cid']=="") $shipdateArr[$row[csf('po_id')]][$str]['cid']=$row[csf('color_size_id')];
			else $shipdateArr[$row[csf('po_id')]][$str]['cid'].=','.$row[csf('color_size_id')];*/
		}
		
		$wo_po_ratio_sql=sql_select("SELECT c.id as ratio_breakdown_id, c.country_id, c.gmts_item_id, c.color_id, c.size_id, c.po_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_ratio_breakdown c on b.id=c.po_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$hidden_job_id $order_cond");
		if(count($wo_po_ratio_sql)>0){
			foreach ($wo_po_ratio_sql as $row) {
				$key=$row[csf('po_id')].'*'.$row[csf('country_id')].'*'.$row[csf('gmts_item_id')].'*'.$row[csf('color_id')].'*'.$row[csf('size_id')];
				$ratio_id_arr[$key]=$row[csf('ratio_breakdown_id')];
			}
		}
		$file_year_sql="SELECT distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=$company_id and status_active=1 and is_deleted=0 union select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=$company_id and status_active=1 and is_deleted=0";
		?>
        <br>
		<table width="1160" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
            	<tr>
					<td colspan="9" align="right">Ship Date Copy Level</td> 
                    <td align="center">Pub. Ship Date</td>
                    <td align="center">Org. Ship Date</td>
                    <td align="center">PHD/PCD</td>
                    <td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<th colspan="9">&nbsp;</th> 
                    <th><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(1);" id="chk_pubshipdate"></th>
                    <th><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(2);" id="chk_orgshipdate"></th>
                    <th><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(3);" id="chk_phddate"></th>
                    
                    <th colspan="4">&nbsp;</th>
				</tr>
                <tr>
                    <th width="30">SL</th>   
                    <th width="80">Buyer</th>
                    <th width="60">Job NO</th>
                    <th width="40">Job Year</th>
                    <th width="130">Style No</th>
                    <th width="130">PO No</th>
                    <th width="100">PO No [Edit]</th>
                    <th width="60">Qty.</th>
                    <th width="60">PO Received Date</th>
                    <th width="60">Publish Shipdate</th>
                    <th width="60">Original Ship Date</th>
                    <th width="60">PHD/PCD</th>
                    
                    <th width="40">Rate</th>
                    <th width="60">Amount</th>
                    <th width="40">File Year</th>
                    <th>File No</th>
                    
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
						$shipstatus=$exstr[12];
						$phddate=$exstr[13];
						
						$gmts_ratio_id=0;
						if($budget_approved==1) $disabled="disabled";
						
						$cellcolor=""; $distex=""; $disdrop=0;
						if($shipstatus==3) { $cellcolor="red"; $distex="disabled"; $disdrop="1";}
						$ordrate=$strval['val']/$strval['qty'];
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					 ?>
						<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>">
							<td align="center"><?=$i; ?></td>
							<td style="word-break:break-all"><?=$buyerArr[$buyer]; ?></td>
							<td style="word-break:break-all"><?=$jobno; ?></td>
							<td style="word-break:break-all"><?=date("Y", strtotime($insertdate)); ?></td>
							<td style="word-break:break-all"><?=$styleref; ?></td>
							<td style="word-break:break-all"><?=$pono; ?>
								<input type="hidden" id="poid_<?=$i; ?>" value="<?=$poid; ?>">
								<input type="hidden" id="jobid_<?=$i; ?>" value="<?=$job_id; ?>">
								<input type="hidden" id="ratioid_<?=$i; ?>" value="<?=$gmts_ratio_id; ?>">
								<input type="hidden" id="approved_<?=$i; ?>" value="<?=$budget_approved; ?>">
							</td>
							<td style="word-break:break-all"><input type="text" class="text_boxes" id="txtpono_<?=$i; ?>" value="<?=$pono; ?>" style="width:87px; background-color:<?=$cellcolor; ?>" <?=$distex; ?> placeholder="<?=$pono; ?>"></td>
							<td style="word-break:break-all" align="right"><?=$strval['qty']; ?>
								<input type="hidden" id="gmtsqty_<?=$i; ?>" value="<?=$strval['qty']; ?>">
							</td>
							<td><input type="text" class="datepicker" id="txtporecdate_<?=$i; ?>" value="<?=change_date_format($po_received_date); ?>" style="width:60px; background-color:<?=$cellcolor; ?>" <?=$distex; ?> placeholder="<?=change_date_format($po_received_date); ?>" readonly disabled></td>
							<td><input type="text" class="datepicker" id="txtpubshipdate_<?=$i; ?>" value="<?=change_date_format($pubshipdate); ?>" onChange="set_tna_task(<?=$i; ?>); copy_value(this.value,'txtpubshipdate_',<?=$i; ?>);" placeholder="<?=change_date_format($pubshipdate); ?>" style="width:60px; background-color:<?=$cellcolor; ?>" <?=$distex; ?> ></td>
							<td><input type="text" class="datepicker" id="txtposhipdate_<?=$i; ?>" value="<?=change_date_format($shipdate); ?>" onChange="set_tna_task(<?=$i; ?>); copy_value(this.value,'txtposhipdate_',<?=$i; ?>);" placeholder="<?=change_date_format($shipdate); ?>" style="width:60px; background-color:<?=$cellcolor; ?>" <?=$distex; ?> ></td>
							<td><input type="text" class="datepicker" id="txtpophddate_<?=$i; ?>" value="<?=change_date_format($phddate); ?>" onChange="set_tna_task(<?=$i; ?>); copy_value(this.value,'txtpophddate_',<?=$i; ?>);" placeholder="<?=change_date_format($phddate); ?>" style="width:60px; background-color:<?=$cellcolor; ?>" <?=$distex; ?> ></td>
							<td><input type="text" class="text_boxes_numeric" id="orderrate_<?=$i; ?>" value="<?=number_format($ordrate,4,"","."); ?>" style="width:60px; background-color:<?=$cellcolor; ?>" <?=$distex; ?> disabled></td>
							<td><input type="text" class="text_boxes_numeric" id="ordeamount_<?=$i; ?>" value="<?=$strval['val']; ?>" style="width:60px; background-color:<?=$cellcolor; ?>" <?=$distex; ?> disabled></td>
							<td><?=create_drop_down( "fileyear_".$i, 80,$file_year_sql, "lc_sc_year,lc_sc_year", 1, "-- Select --",$fileyear,"copy_value(this.value,'fileyear_',$i)",$disdrop); ?>
							</td>
							<td><input type="text" class="text_boxes" id="fileno_<?=$i; ?>" value="<?=$fileno; ?>" style="width:60px; background-color:<?=$cellcolor; ?>" <?=$distex; ?> onChange="copy_value(this.value,'fileno_',<?=$i; ?>);"></td>
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
            	<tr bgcolor="#CCCCCC">
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
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            	</tr>
            </tfoot>
		</table>
        <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		<br>
     <? } 
    else if($operation==3)//Country Wise
	{
		$color_size_data=sql_select("SELECT a.id as job_id, a.company_name, a.buyer_name, a.job_no_prefix_num, a.insert_date, a.style_ref_no, b.id as po_id, b.po_number, b.file_year, b.file_no, b.matrix_type, b.po_received_date, b.pub_shipment_date, b.shipment_date, b.shiping_status, c.id as color_size_id, c.country_id, c.order_quantity, c.order_rate, c.order_total, c.item_number_id, c.country_ship_date from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$hidden_job_id $order_cond");
		$shipdateArr=array();
		foreach ($color_size_data as $row) {
			$company_id=$row[csf('company_name')];
			$str="";
			$str=$row[csf('buyer_name')].'__'.$row[csf('job_no_prefix_num')].'__'.$row[csf('job_id')].'__'.$row[csf('insert_date')].'__'.$row[csf('style_ref_no')].'__'.$row[csf('po_number')].'__'.$row[csf('file_year')].'__'.$row[csf('file_no')].'__'.$row[csf('matrix_type')].'__'.$row[csf('po_received_date')].'__'.$row[csf('pub_shipment_date')].'__'.$row[csf('shipment_date')].'__'.$row[csf('shiping_status')].'__'.$row[csf('country_ship_date')].'__'.$row[csf('country_id')].'__'.$row[csf('item_number_id')];
			$shipdateArr[$row[csf('po_id')]][$str]['qty']+=$row[csf('order_quantity')];
			$shipdateArr[$row[csf('po_id')]][$str]['val']+=$row[csf('order_total')];
			if($shipdateArr[$row[csf('po_id')]][$str]['cid']=="") $shipdateArr[$row[csf('po_id')]][$str]['cid']=$row[csf('color_size_id')];
			else $shipdateArr[$row[csf('po_id')]][$str]['cid'].=','.$row[csf('color_size_id')];
		}
		
		$wo_po_ratio_sql=sql_select("SELECT c.id as ratio_breakdown_id, c.country_id, c.gmts_item_id, c.color_id, c.size_id, c.po_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_ratio_breakdown c on b.id=c.po_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$hidden_job_id $order_cond");
		//echo "SELECT c.id as ratio_breakdown_id, c.country_id, c.gmts_item_id, c.color_id, c.size_id, c.po_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_ratio_breakdown c on b.id=c.po_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$hidden_job_id $order_cond";
		if(count($wo_po_ratio_sql)>0){
			foreach ($wo_po_ratio_sql as $row) {
				$key=$row[csf('po_id')].'*'.$row[csf('country_id')].'*'.$row[csf('gmts_item_id')];
				if($ratio_id_arr[$key]=="") $ratio_id_arr[$key]=$row[csf('ratio_breakdown_id')];
				else $ratio_id_arr[$key].=','.$row[csf('ratio_breakdown_id')];
			}
		}
		$file_year_sql="SELECT distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=$company_id and status_active=1 and is_deleted=0 union select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=$company_id and status_active=1 and is_deleted=0";
		?>
        <br>
		<table width="1060" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
            	<tr>
					<td colspan="10" align="right">Ship Date Copy Level</td> 
                    <td align="center" colspan="2">Country Ship Date</td>
                    <td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<th colspan="9">&nbsp;</th> 
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(3);" id="chk_country"></th>
                    <th colspan="4">&nbsp;</th>
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
                    <th width="60">Original Ship Date</th>
                    <th width="60">Country Ship Date</th>
                    
                    <th width="40">Rate</th>
                    <th width="60">Amount</th>
                    <th width="40">File Year</th>
                    <th>File No</th>
                    
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
						$shipstatus=$exstr[12];
						$countryshipdate=$exstr[13];
						$country_id=$exstr[14];
						$gmtsitem=$exstr[15];
						
						$gmts_ratio_id=0;
						if($budget_approved==1){
							$disabled="disabled";
						}
						if($matrixtype==3){
							$datakey=$poid.'*'.$country_id.'*'.$gmtsitem;
							$gmts_ratio_id=$ratio_id_arr[$datakey];
						}
						$cellcolor=""; $distex=""; $disdrop=0;
						if($shipstatus==3) { $cellcolor="red"; $distex="disabled"; $disdrop="1";}
						$ordrate=$strval['val']/$strval['qty'];
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					 ?>
						<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>">
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
							<td><?=change_date_format($po_received_date); ?></td>
							<td><?=change_date_format($pubshipdate); ?></td>
							<td><?=change_date_format($shipdate); ?></td>
							<td><input type="text" class="datepicker" id="txtcountryshipdate_<?=$i; ?>" value="<?=change_date_format($countryshipdate); ?>" onChange="copy_value(this.value,'txtcountryshipdate_',<?=$i; ?>);" placeholder="<?=change_date_format($countryshipdate); ?>" style="width:60px; background-color:<?=$cellcolor; ?>" <?=$distex; ?> ></td>
							<td><input type="text" class="text_boxes_numeric" id="orderrate_<?=$i; ?>" value="<?=number_format($ordrate,4,"","."); ?>" style="width:60px;" disabled></td>
							<td><input type="text" class="text_boxes_numeric" id="ordeamount_<?=$i; ?>" value="<?=$strval['val']; ?>" style="width:60px;" disabled></td>
							<td><?=create_drop_down( "fileyear_".$i, 80,$file_year_sql, "lc_sc_year,lc_sc_year", 1, "-- Select --",$fileyear,"copy_value(this.value,'fileyear_',$i)",$disdrop); ?>
							</td>
							<td><input type="text" class="text_boxes" id="fileno_<?=$i; ?>" value="<?=$fileno; ?>" style="width:60px;" onChange="copy_value(this.value,'fileno_',<?=$i; ?>);" <?=$distex; ?> ></td>
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
            	<tr bgcolor="#CCCCCC">
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
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            	</tr>
            </tfoot>
		</table>
        <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		<br>
     <? } ?>
		<? echo load_submit_buttons( $permission, "fnc_order_entry_details", 1,0 ,"",2); ?>
	<?
	exit();
}

if($action=='save_update_delete_dtls'){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	if ($operation==1)
	{
		if ($reporttype==1)//Show
		{
			$field_array_up="order_rate*order_total*updated_by*update_date";
			$field_ratio_up="ratio_rate*updated_by*update_date";
			$po_field_array_up="file_year*file_no*sc_lc*updated_by*update_date";
			$pofield_array_up="unit_price*po_total_price";
			$counter=0; $rcounter=0; $rID1=1;
			for($m=1; $m<=$row_table; $m++)
			{
				$orderrate="orderrate_".$m;
				$ordeamount="ordeamount_".$m;
				$fileyear="fileyear_".$m;
				$fileno="fileno_".$m;
				$sclcno="sclcno_".$m;
				$poid="poid_".$m;
				$colorsizeid="colorsizeid_".$m;
				$gmtsqty="gmtsqty_".$m;
				$ratioid="ratioid_".$m;
				$approved="approved_".$m;
				$ratio_id=str_replace("'",'',$$ratioid);
				$approved_id=str_replace("'",'',$$approved);
				$poidarr[str_replace("'",'',$$poid)]=str_replace("'",'',$$poid);
				$id_arr[]=str_replace("'",'',$$colorsizeid);
				$po_wise_rate[str_replace("'",'',$$poid)]['rate']+=str_replace("'",'',$$orderrate)*1;
				$po_wise_rate[str_replace("'",'',$$poid)]['counter']+=1;
				$po_wise_rate[str_replace("'",'',$$poid)]['qty']+=str_replace("'",'',$$gmtsqty)*1;
				$data_array_up[str_replace("'",'',$$colorsizeid)] =explode("*",("".$$orderrate."*".$$ordeamount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				if($ratio_id!=0){
					$id_ratioarr[]=$ratio_id;
					$data_ratio_up[$ratio_id] =explode("*",("".$$orderrate."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$rcounter++;
				}
				$counter++;
				if(str_replace("'",'',$$approved) !=1){
					if($data_array_up!="" && $counter==100){
						$rID1=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ));
						$counter=0;
						$id_arr=array();
						$data_array_up=array();
					}
					if( $data_ratio_up!="" && $rcounter==100){
						$rID1=execute_query(bulk_update_sql_statement("wo_po_ratio_breakdown", "id",$field_ratio_up,$data_ratio_up,$id_ratioarr ));
						$rcounter=0;
						$id_ratioarr=array();
						$data_ratio_up=array();
					}
				}
				
				$po_data_array_up="".$$fileyear."*".$$fileno."*".$$sclcno."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			if($data_array_up!="" && $counter!=100 && $approved_id != 1){
				$rID1=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ));
			}
			if( $data_ratio_up!="" && $rcounter!=100 && $approved_id != 1){
				$rID1=execute_query(bulk_update_sql_statement("wo_po_ratio_breakdown", "id",$field_ratio_up,$data_ratio_up,$id_ratioarr ));
			}
			foreach ($po_wise_rate as $po_id => $data) {
				$avg_rate=number_format($data['rate']/$data['counter'],4);
				$total_price=$avg_rate*$data['qty'];
				$poid_arr[]=$po_id;
				$podata_array_up[$po_id] =explode("*",("".$avg_rate."*".$total_price.""));
			}
			if($podata_array_up!=""){
				$rID3=execute_query(bulk_update_sql_statement("wo_po_break_down", "id",$pofield_array_up,$podata_array_up,$poid_arr ));
			}	
			if($po_data_array_up!=''){
				$rID2=sql_update("wo_po_break_down",$po_field_array_up,$po_data_array_up,"job_id","".$hidden_job_id."",1);
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID1){
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
		else if ($reporttype==2)//Po Wise
		{
			$job_id=str_replace("'",'',$hidden_job_id);
			$job_table_data=sql_select("SELECT job_no from wo_po_details_master where status_active=1 and is_deleted=0 and id=$job_id");
			//echo "10**SELECT job_no from wo_po_details_master where status_active=1 and is_deleted=0 and id=$job_id"; die;
			foreach($job_table_data as $row){
				$job_no=$row[csf('job_no')];
			}
			$field_array_up="country_ship_date*updated_by*update_date";
			$pofield_array_up="po_number*pub_shipment_date*shipment_date*file_year*file_no*pack_handover_date*updated_by*update_date";

			/* $sql_con="po_number =$txt_po_no and job_id=$job_id and pub_shipment_date=$txt_pub_shipment_date and shipment_date=$txt_pub_shipment_date and file_no=$txt_file_no and id=$update_id_details and is_deleted=0";
			$sql_con=str_replace("=''"," IS NULL ",$sql_con);
			$is_duplicate=is_duplicate_field( "po_number", "wo_po_break_down", $sql_con ); */

			$data_shipDate_vari="";
			$sql_shipDate_vari=sql_select("select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29");
			$data_shipDate_vari=$sql_shipDate_vari[0][csf("duplicate_ship_date")];
			for($m=1; $m<=$row_table; $m++)
			{
				$poid="poid_".$m;
				$poid_arr[]=str_replace("'",'',$$poid);
			}
			$job_id_cond=where_con_using_array($poid_arr,0,'id');
			//$job_id_condA=where_con_using_array($job_idArr,0,'a.job_id');
			$prev_data=sql_select("SELECT id as po_id, is_confirmed ,po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, doc_sheet_qty, no_of_carton, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, matrix_type, round_type, tna_task_from_upto, t_year, t_month, file_no, sc_lc, pack_handover_date,is_deleted, status_active, updated_by, update_date, po_number_prev, pub_shipment_date_prev, file_year FROM wo_po_break_down WHERE status_active=1 and is_deleted=0 $job_id_cond");
			foreach($prev_data as $rows)
			{
				$prev_po_no[$rows[csf('po_id')]]=$rows[csf('po_number')];
				$prev_matrix_type[$rows[csf('po_id')]]=$rows[csf('matrix_type')];
				$prev_round_type[$rows[csf('po_id')]]=$rows[csf('round_type')];
				$prev_doc_sheet_qty[$rows[csf('po_id')]]=$rows[csf('doc_sheet_qty')];
				$prev_no_of_carton[$rows[csf('po_id')]]=$rows[csf('no_of_carton')];
				$prev_order_status[$rows[csf('po_id')]]=$rows[csf('is_confirmed')];
				$prev_po_received_date[$rows[csf('po_id')]]=$rows[csf('po_received_date')];
				$prev_po_qty[$rows[csf('po_id')]]=$rows[csf('po_quantity')];
				$prev_pub_shipment_date[$rows[csf('po_id')]]=$rows[csf('pub_shipment_date')];
				$prev_status[$rows[csf('po_id')]]=$rows[csf('status_active')];
				$prev_org_shipment_date[$rows[csf('po_id')]]=$rows[csf('shipment_date')];
				$prev_factory_rec_date[$rows[csf('po_id')]]=$rows[csf('factory_received_date')];
				$prev_projected_po[$rows[csf('po_id')]]=$rows[csf('projected_po_id')];
				$prev_packing[$rows[csf('po_id')]]=$rows[csf('packing')];
				$prev_grouping[$rows[csf('po_id')]]=$rows[csf('grouping')];
				$prev_details_remark[$rows[csf('po_id')]]=$rows[csf('details_remarks')];
				$prev_file_no[$rows[csf('po_id')]]=$rows[csf('file_no')];
				$prev_avg_price[$rows[csf('po_id')]]=$rows[csf('unit_price')];
				$prev_sc_lc[$rows[csf('po_id')]]=$rows[csf('sc_lc')];
				$prev_phd_date[$rows[csf('po_id')]]=$rows[csf('pack_handover_date')];
				$prev_excess_cut[$rows[csf('po_id')]]=$rows[csf('excess_cut')];
				$prev_plan_cut[$rows[csf('po_id')]]=$rows[csf('plan_cut')];
				$prev_status[$rows[csf('po_id')]]=$rows[csf('status_active')];
				$prev_updated_by[$rows[csf('po_id')]]=$rows[csf('updated_by')];			
				$prev_update_date[$rows[csf('po_id')]]=$rows[csf('update_date')];
				$prev_pono[$rows[csf('po_id')]]=$rows[csf('po_number_prev')];
				$prev_pubship_date[$rows[csf('po_id')]]=$rows[csf('pub_shipment_date_prev')];
				$prev_file_year[$rows[csf('po_id')]]=$rows[csf('file_year')];
			}			

			$counter=0; $rcounter=0; $rID1=1;
			for($m=1; $m<=$row_table; $m++)
			{
				$txtpono="txtpono_".$m;
				$txtpubshipdate="txtpubshipdate_".$m;
				$txtposhipdate="txtposhipdate_".$m;
				$phddate="phddate_".$m;
				$fileyear="fileyear_".$m;
				$fileno="fileno_".$m;
				$poid="poid_".$m;				
				
				$pubshipdate=date("d-M-Y",strtotime(str_replace("'",'',$$txtpubshipdate)));
				$poshipdate=date("d-M-Y",strtotime(str_replace("'",'',$$txtposhipdate)));
				$phddate=date("d-M-Y",strtotime(str_replace("'",'',$$phddate)));
				
				$approved_id=str_replace("'",'',$$approved);				
				
				$po_id_arr[$job_id][str_replace("'",'',$$poid)]=str_replace("'",'',$$poid);

				$po_id=str_replace("'",'',$$poid);
				
				if($data_shipDate_vari==1) $txt_pub_shipment_date_cond="and pub_shipment_date=$pubshipdate";
				else $txt_pub_shipment_date_cond="";
				$po_no=$$txtpono;
				
				if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$po_no and job_id=$job_id  $txt_pub_shipment_date_cond and id!=$po_id and is_deleted=0" ) == 1)
				{
					echo "11**0";
					disconnect($con);die;
				}
				//echo "10**".__LINE__; die;
				
				$prepubshipdate=date("d-M-Y",strtotime(str_replace("'",'',$prev_pub_shipment_date[$po_id])));
				$prevphddate=date("d-M-Y",strtotime(str_replace("'",'',$prev_phd_date[$po_id])));
				$prevorgshipmentdate=date("d-M-Y",strtotime(str_replace("'",'',$prev_org_shipment_date[$po_id])));
				if((str_replace("'",'',$$txtpono) != $prev_po_no[$po_id]) || ($pubshipdate != $prepubshipdate) || ($poshipdate != $prevorgshipmentdate) || (str_replace("'",'',$$fileyear)*1 != $prev_file_year[$po_id]) || (str_replace("'",'',$$fileno) !="" && ($$fileno != $prev_file_no[$po_id])) || ($prevphddate != $phddate)){
					$current_po_data[str_replace("'",'',$$poid)]['po_no']=$$txtpono;
					$current_po_data[str_replace("'",'',$$poid)]['pubshipdate']=$pubshipdate;
					$current_po_data[str_replace("'",'',$$poid)]['pobshipdate']=$poshipdate;
					$poidarr[]=str_replace("'",'',$$poid);
					$podata_array_up[str_replace("'",'',$$poid)] =explode("*",("".$$txtpono."*'".$pubshipdate."'*'".$poshipdate."'*".$$fileyear."*".$$fileno."*'".$phddate."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				}
				
				//$podata_array_up="'".$pubshipdate."'*'".$poshipdate."'*".$$fileyear."*".$$fileno."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			//echo "10**".__LINE__; die;
			if(count($poidarr)>0){				
				$log_id_mst=return_next_id( "id", "wo_po_update_log", 1);

				if($db_type==0) $current_date = $pc_date_time;
				else $current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
				$curr_date=date("Y-m-d", strtotime($current_date));

				$field_array_history="id, entry_form, matrix_type, round_type, job_no, po_no, po_id, order_status, po_received_date, previous_po_qty, shipment_date, org_ship_date, po_status, t_year, t_month, fac_receive_date, projected_po, packing, remarks, file_no, sc_lc, phd_date, doc_sheet_qty, avg_price, no_of_carton, excess_cut_parcent, plan_cut, status, update_date, update_by";
				$field_array_update="po_no*po_id*matrix_type*round_type*order_status*po_received_date*previous_po_qty*shipment_date*org_ship_date*po_status*fac_receive_date*projected_po*packing*remarks*file_no*sc_lc*phd_date*avg_price*doc_sheet_qty*no_of_carton*excess_cut_parcent*plan_cut*status*update_date*update_by";

				foreach($poidarr as $poid){
					$log_update_date=return_field_value("update_date","wo_po_update_log","job_no='".$job_no."' and po_id=".$poid." order by id DESC");
					$log_update='';
					if($log_update_date!=''){
						$log_update=date("Y-m-d", strtotime($log_update_date));
					}		
					
					$flag=1;
					if($log_update=="" || $log_update!=$curr_date)
					{
						$flag=0;
						$data_array_history="(".$log_id_mst.",1,'".$prev_matrix_type[$poid]."','".$prev_round_type[$poid]."','".$job_no."','".$prev_po_no[$poid]."',".$poid.",'".$prev_order_status[$poid]."','".$prev_po_received_date[$poid]."','".$prev_po_qty[$poid]."','".$prev_pub_shipment_date[$poid]."','".$prev_org_shipment_date[$poid]."','".$prev_status[$poid]."','".date("Y",strtotime(str_replace("'","",$prev_org_shipment_date[$poid])))."','".date("m",strtotime(str_replace("'","",$prev_org_shipment_date[$poid])))."','".$prev_factory_rec_date[$poid]."','".$prev_projected_po[$poid]."','".$prev_packing[$poid]."','".$prev_details_remark[$poid]."','".$prev_file_no[$poid]."','".$prev_sc_lc[$poid]."','".$prev_phd_date[$poid]."','".$prev_doc_sheet_qty[$poid]."','".$prev_avg_price[$poid]."','".$prev_no_of_carton[$poid]."','".$prev_excess_cut[$poid]."','".$prev_plan_cut[$poid]."','".$prev_status[$poid]."','".$prev_update_date[$poid]."','".$prev_updated_by[$poid]."')";
						//echo "10**insert into wo_po_update_log ($field_array_history) values $data_array_history"; die;
						$rID3=sql_insert("wo_po_update_log",$field_array_history,$data_array_history,1);
						if($rID3) $flag=1; else $flag=0;
						$log_id_mst++;
					}
					else if($log_update==$curr_date)
					{
						$flag=0;
						$data_array_update="'".$prev_po_no[$poid]."'*".$poid."*'".$prev_matrix_type[$poid]."'*'".$prev_round_type[$poid]."'*'".$prev_order_status[$poid]."'*'".$prev_po_received_date[$poid]."'*'".$prev_po_qty[$poid]."'*'".$prev_pub_shipment_date[$poid]."'*'".$prev_org_shipment_date[$poid]."'*'".$prev_status[$poid]."'*'".$prev_factory_rec_date[$poid]."'*'".$prev_projected_po[$poid]."'*'".$prev_packing[$poid]."'*'".$prev_details_remark[$poid]."'*'".$prev_file_no[$poid]."'*'".$prev_sc_lc[$poid]."'*'".$prev_phd_date[$poid]."'*'".$prev_avg_price[$poid]."'*'".$prev_doc_sheet_qty[$poid]."'*'".$prev_no_of_carton[$poid]."'*'".$prev_excess_cut[$poid]."'*'".$prev_plan_cut[$poid]."'*'".$prev_order_status[$poid]."'*'".$current_date."'*".$_SESSION['logic_erp']['user_id']."";
						$rID3=sql_update("wo_po_update_log",$field_array_update,$data_array_update,"po_id*update_date","".$poid."*'".$log_update_date."'",1);
						if($rID3) $flag=1; else $flag=0;
					}
				}				

			}
			if($podata_array_up!="" && $flag==1){
				$rID3=execute_query(bulk_update_sql_statement("wo_po_break_down", "id",$pofield_array_up,$podata_array_up,$poidarr ));
				//echo "10**".bulk_update_sql_statement("wo_po_break_down", "id",$pofield_array_up,$podata_array_up,$poidarr ); die;
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID3){
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
		else if ($reporttype==3)//Country Wise
		{
			$field_array_up="country_ship_date*updated_by*update_date";
			$counter=0; $rcounter=0; $rID1=$rID2=1;
			for($m=1; $m<=$row_table; $m++)
			{
				$poid="poid_".$m;
				
				$colorsizeid="colorsizeid_".$m;
				$ratioid="ratioid_".$m;
				$txtcountryshipdate="txtcountryshipdate_".$m;
				$approved="approved_".$m;
				
				$countryshipdate=date("d-M-Y",strtotime(str_replace("'",'',$$txtcountryshipdate)));
				
				$approved_id=str_replace("'",'',$$approved);
				$poidarr[]=str_replace("'",'',$$poid);
				
				$excolorsizeid=explode(",",str_replace("'",'',$$colorsizeid));
				foreach($excolorsizeid as $exczid)
				{
					$data_array_up[$exczid] =explode("*",("'".$countryshipdate."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$counter++;
					$id_arr[]=$exczid;
					if(str_replace("'",'',$$approved) !=1){
						if($data_array_up!="" && $counter==100){
							$rID1=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ));
							$counter=0;
							$id_arr=array();
							$data_array_up=array();
						}
					}
				}
				
				$exratioid=explode(",",str_replace("'",'',$$ratioid));
				foreach($exratioid as $exrid)
				{
					$data_array_ratioup[$exrid] =explode("*",("'".$countryshipdate."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$rcounter++;
					$idratio_arr[]=$exrid;
					if(str_replace("'",'',$$approved) !=1){
						if($data_array_ratioup!="" && $rcounter==100){
							$rID1=execute_query(bulk_update_sql_statement("wo_po_ratio_breakdown", "id",$field_array_up,$data_array_ratioup,$idratio_arr ));
							$rcounter=0;
							$idratio_arr=array();
							$data_array_ratioup=array();
						}
					}
				}
				//$podata_array_up[str_replace("'",'',$$poid)] =explode("*",("'".$pubshipdate."'*'".$poshipdate."'*".$$fileyear."*".$$fileno."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				//$podata_array_up="'".$pubshipdate."'*'".$poshipdate."'*".$$fileyear."*".$$fileno."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			if($data_array_up!="" && $counter!=100){
				$rID1=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ));
				//echo "10**".bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ); die;
			}
			if($data_array_ratioup!="" && $rcounter!=100){
				$rID2=execute_query(bulk_update_sql_statement("wo_po_ratio_breakdown", "id",$field_array_up,$data_array_ratioup,$idratio_arr ));
				//echo "10**".bulk_update_sql_statement("wo_po_ratio_breakdown", "id",$field_array_up,$data_array_up,$id_arr ); die;
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID1==1 && $rID2==1){
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
}