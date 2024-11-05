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

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $selected, "" );
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
			var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
			
			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}

			function check_all_data()
			{
				var row_num=$('#tbl_list_search tr').length-1;
				for(var i=1;  i<=row_num;  i++)
				{
					if($("#tr_"+i).css("display") != "none")
					{
						$("#tr_"+i).click();
					}
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
						// alert(selected_name)
						
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == str  ) break;
						}
						// selected_id.splice( i, 1 );
						// selected_name.splice( i,1 );
					}
					var id = ''; var name = '';
					for( var i = 0; i < selected_id.length; i++ ) {
						name += selected_id[i] + ',';
						id += selected_name[i] + ',';
					}
					
					name = name.substr( 0, name.length - 1 );
					id = id.substr( 0, id.length - 1 );
					$('#job_id').val( name );
					$('#job_no').val( id );
					
			}
			//function js_set_value( job_data )
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
        <table width="880" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="9" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="140" class="must_entry_caption">Buyer Name</th>
                    <th width="60">Job No</th>
                    <th width="90">Style Ref.</th>
                    <th width="80">IR/IB No.</th>
                    <th width="90">Order No</th>
                    <th width="100" colspan="2">Ship Date Range</th>
                    <th><input type="hidden" value="0" id="chk_job_wo_po"></th>
                </tr>
            </thead>
            <tr class="general">
                <td>
                    <input type="hidden" id="job_id">
                    <input type="hidden" id="job_no">
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'trims_group_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name","id,buyer_name",'', 1, "-- Select Buyer --","" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date"></td>
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value, 'create_job_search_list_view', 'search_div', 'trims_group_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="10"><?=load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script>
		load_drop_down('trims_group_entry_controller', <?=$cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<?=$cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_job_search_list_view")
{
	$data=explode('_',$data);
    //cbo_company_mst*cbo_buyer_id*chk_job_wo_po*txt_date_from*txt_date_to*txt_booking_prifix*txt_job_prifix*cbo_year_selection*cbo_string_search_type*txt_order_search*txt_style*txt_internal_ref
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
		if($db_type==2)
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";
			if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
		}
	$order_cond=""; $job_cond=""; $style_cond="";
	if($data[7]==1)
	{

		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]'  $year_cond";
		if (trim($data[8])!="") $order_cond=" and b.po_number='$data[8]'  "; //else  $order_cond="";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no='$data[9]'  "; //else  $style_cond="";
	}
	else if($data[7]==4 || $data[7]==0)
	{	
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]%'  $year_cond";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]%'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]%'  "; //else  $style_cond="";
	}
	else if($data[7]==2)
	{
		
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '$data[5]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '$data[8]%'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '$data[9]%'  "; //else  $style_cond="";
	}
	else if($data[7]==3)
	{
		
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]'  $year_cond"; //else  $job_cond="";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]'  "; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[10]);
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name",'id','company_name');

	$arr=array(2=>$buyer_arr, 7=>$color_library,13=>$item_category);
    if($db_type==2)
	{
		$sql= "select a.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id,a.job_quantity,a.order_uom, a.order_repeat_no, a.brand_id, a.season_year, a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, a.insert_date from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer $job_cond $order_cond $style_cond  $internal_ref_cond $year_cond order by a.job_no DESC";

	}
	//echo $sql;die;
	echo  create_list_view("tbl_list_search", "Job No,Year,Buyer,Style Ref.,B/W Color,Quo. ID,Job Qty.,Repeat No,PO No.,PO Qty.,Shipment Date,Ref no, File No,Lead time", "40,40,100,100,70,50,70,50,90,70,60,50,50,50","990","300",0, $sql , "js_set_value", "id,job_no", "", 1, "0,0,buyer_name,0,body_wash_color,0,0,0,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,style_ref_no,body_wash_color,quotation_id,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,grouping,file_no,date_diff", "",'','0,0,0,0,0,1,0,0,0,0,3,0,0,0','',1);
	exit();
}
if ($action=="style_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
			var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
			
			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}

			function check_all_data()
			{
				var row_num=$('#tbl_list_search tr').length-1;
				for(var i=1;  i<=row_num;  i++)
				{
					if($("#tr_"+i).css("display") != "none")
					{
						$("#tr_"+i).click();
					}
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
						// alert(selected_name)
						
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == str  ) break;
						}
						// selected_id.splice( i, 1 );
						// selected_name.splice( i,1 );
					}
					var id = ''; var name = '';
					for( var i = 0; i < selected_id.length; i++ ) {
						name += selected_id[i] + ',';
						id += selected_name[i] + ',';
					}
					
					name = name.substr( 0, name.length - 1 );
					id = id.substr( 0, id.length - 1 );
					$('#job_no').val( name );
					$('#style_no').val( id );
					
			}
			//function js_set_value( job_data )
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
	<table width="880" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="9" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="140" class="must_entry_caption">Buyer Name</th>
                    <th width="60">Job No</th>
                    <th width="90">Style Ref.</th>
                    <th width="80">IR/IB No.</th>
                    <th width="90">Order No</th>
                    <th width="100" colspan="2">Ship Date Range</th>
                    <th><input type="hidden" value="0" id="chk_job_wo_po"></th>
                </tr>
            </thead>
            <tr class="general">
                <td>
                    <input type="hidden" id="job_no">
                    <input type="hidden" id="style_no">
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'trims_group_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name","id,buyer_name",'', 1, "-- Select Buyer --","" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date"></td>
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value, 'create_style_search_list_view', 'search_div', 'trims_group_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="10"><?=load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script>
		load_drop_down('trims_group_entry_controller', <?=$cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<?=$cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_style_search_list_view")
{
	$data=explode('_',$data);
    //cbo_company_mst*cbo_buyer_id*chk_job_wo_po*txt_date_from*txt_date_to*txt_booking_prifix*txt_job_prifix*cbo_year_selection*cbo_string_search_type*txt_order_search*txt_style*txt_internal_ref
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
    $year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";
		if ($data[3]!="" &&  $data[4]!="") $booking_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	

	$order_cond=""; $job_cond=""; $style_cond="";
	if($data[7]==1)
	{

		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]'  $year_cond";
		if (trim($data[8])!="") $order_cond=" and b.po_number='$data[8]'  "; //else  $order_cond="";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no='$data[9]'  "; //else  $style_cond="";
	}
	else if($data[7]==4 || $data[7]==0)
	{	
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]%'  $year_cond";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]%'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]%'  "; //else  $style_cond="";
	}
	else if($data[7]==2)
	{
		
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '$data[5]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '$data[8]%'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '$data[9]%'  "; //else  $style_cond="";
	}
	else if($data[7]==3)
	{
		
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]'  $year_cond"; //else  $job_cond="";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]'  "; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[10]);
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name",'id','company_name');
	$arr=array(2=>$buyer_arr, 7=>$color_library,13=>$item_category);
    if($db_type==2)
	{
		$sql= "select a.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id,a.job_quantity,a.order_uom, a.order_repeat_no, a.brand_id, a.season_year, a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, to_char(a.insert_date,'yyyy') as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $booking_date $company $buyer $job_cond $order_cond $style_cond  $internal_ref_cond $year_cond order by a.job_no DESC";

	}
	//echo $sql;die;
	echo  create_list_view("tbl_list_search", "Job No,Year,Buyer,Style Ref.,B/W Color,Quo. ID,Job Qty.,Repeat No,PO No.,PO Qty.,Shipment Date,Ref no, File No,Lead time", "40,40,100,100,70,50,70,50,90,70,60,50,50,50","990","300",0, $sql , "js_set_value", "job_no,style_ref_no", "", 1, "0,0,buyer_name,0,body_wash_color,0,0,0,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,style_ref_no,body_wash_color,quotation_id,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,grouping,file_no,date_diff", "",'','0,0,0,0,0,1,0,0,0,0,3,0,0,0','',1);
	exit();
}
if ($action=="intrnal_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
			var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
			
			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}

			function check_all_data()
			{
				var row_num=$('#tbl_list_search tr').length-1;
				for(var i=1;  i<=row_num;  i++)
				{
					if($("#tr_"+i).css("display") != "none")
					{
						$("#tr_"+i).click();
					}
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
						// alert(selected_name)
						
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == str  ) break;
						}
						// selected_id.splice( i, 1 );
						// selected_name.splice( i,1 );
					}
					var id = ''; var name = '';
					for( var i = 0; i < selected_id.length; i++ ) {
						name += selected_id[i] + ',';
						id += selected_name[i] + ',';
					}
					
					name = name.substr( 0, name.length - 1 );
					id = id.substr( 0, id.length - 1 );
					$('#job_no').val( name );
					$('#grouping_no').val( id );
					
			}
			//function js_set_value( job_data )
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
	<table width="880" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="9" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="140" class="must_entry_caption">Buyer Name</th>
                    <th width="60">Job No</th>
                    <th width="90">Style Ref.</th>
                    <th width="80">IR/IB No.</th>
                    <th width="90">Order No</th>
                    <th width="100" colspan="2">Ship Date Range</th>
                    <th><input type="hidden" value="0" id="chk_job_wo_po"></th>
                </tr>
            </thead>
            <tr class="general">
                <td>
                    <input type="hidden" id="job_no">
                    <input type="hidden" id="grouping_no">
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'trims_group_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name","id,buyer_name",'', 1, "-- Select Buyer --","" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date"></td>
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value, 'create_itrnal_search_list_view', 'search_div', 'trims_group_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="10"><?=load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script>
		load_drop_down('trims_group_entry_controller', <?=$cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<?=$cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_itrnal_search_list_view")
{
	$data=explode('_',$data);
    //cbo_company_mst*cbo_buyer_id*chk_job_wo_po*txt_date_from*txt_date_to*txt_booking_prifix*txt_job_prifix*cbo_year_selection*cbo_string_search_type*txt_order_search*txt_style*txt_internal_ref
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
    $year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";
		if ($data[3]!="" &&  $data[4]!="") $booking_date = "and b.pub_shipment_date '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	

	$order_cond=""; $job_cond=""; $style_cond="";
	if($data[7]==1)
	{

		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]'  $year_cond";
		if (trim($data[8])!="") $order_cond=" and b.po_number='$data[8]'  "; //else  $order_cond="";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no='$data[9]'  "; //else  $style_cond="";
	}
	else if($data[7]==4 || $data[7]==0)
	{	
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]%'  $year_cond";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]%'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]%'  "; //else  $style_cond="";
	}
	else if($data[7]==2)
	{
		
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '$data[5]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '$data[8]%'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '$data[9]%'  "; //else  $style_cond="";
	}
	else if($data[7]==3)
	{
		
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]'  $year_cond"; //else  $job_cond="";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]'  "; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[10]);
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name",'id','company_name');
	$arr=array(2=>$buyer_arr, 7=>$color_library,13=>$item_category);
    if($db_type==2)
	{
		$sql= "select a.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id,a.job_quantity,a.order_uom, a.order_repeat_no, a.brand_id, a.season_year, a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, to_char(a.insert_date,'yyyy') as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $booking_date $company $buyer $job_cond $order_cond $style_cond  $internal_ref_cond $year_cond order by a.job_no DESC";

	}
	//echo $sql;die;
	echo  create_list_view("tbl_list_search", "Job No,Year,Buyer,Style Ref.,B/W Color,Quo. ID,Job Qty.,Repeat No,PO No.,PO Qty.,Shipment Date,Ref no, File No,Lead time", "40,40,100,100,70,50,70,50,90,70,60,50,50,50","990","300",0, $sql , "js_set_value", "job_no,grouping", "", 1, "0,0,buyer_name,0,body_wash_color,0,0,0,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,style_ref_no,body_wash_color,quotation_id,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,grouping,file_no,date_diff", "",'','0,0,0,0,0,1,0,0,0,0,3,0,0,0','',1);
	exit();
}

if ($action=="process_data"){
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $str_data;
	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$from_date = '';
	$to_date = '';
	$date_disabled ='';
	if($cbo_booking_month != 0 && $cbo_booking_year !=0)
	{
		$booking_month=0;
		if($cbo_booking_month<10) $booking_month.=$cbo_booking_month; else $booking_month=$cbo_booking_month;
		$start_date="01-".$booking_month."-".$cbo_booking_year;
		$end_date=cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year)."-".$booking_month.'-'.$cbo_booking_year;
		if($start_date != '' && $end_date != '' )
		{
			$from_date = $start_date;
			$to_date = $end_date;
			$date_disabled = 'disabled';
		}
	}

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
			///alert(x+'_'+origColor)
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
		//	alert(str);
			if($("#search"+str).css("display") !='none'){
				var select_row=0; var sp=1;
				
				if(po_job_level==2)
				{
					var select_row= str;
					sp=1;
				}
				else if(po_job_level==0 || po_job_level==1 )
				{
					var tbl_length =$('#tbl_list_search tr').length-1;
					var select_str=$('#txt_job_no' + str).val()+'_'+$('#hiddtrim_group' + str).val()+'_'+$('#td_item_des' + str).text();
					for(var i=1; i<=tbl_length; i++)
					{
						var string=$('#txt_job_no' + i).val()+'_'+$('#hiddtrim_group' + i).val()+'_'+$('#td_item_des' + i).text();
						if(select_str==string)
						{
							//alert(select_str+'='+string);
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
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <input type="hidden" id="txt_booking" value="" />
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
                            <th width="100">Style Ref</th>
                            <th width="80">Job No</th>
                            <th width="100">Dealing Merchant</th>
                            <th width="80">Int. Ref. No</th>
                            <th width="100">Order No</th>
                            <th width="100">Item Name</th>
                            <th width="130" colspan="2">Pub. Ship Date Range</th>
                            <th>&nbsp;
                                <input type="hidden"  style="width:20px" name="txt_garments_nature" id="txt_garments_nature" value="<? echo $garments_nature;?>" />
                                <input type="hidden" name="cbo_booking_month" id="cbo_booking_month" value="<? echo $cbo_booking_month;?>" />
                                <input type="hidden" name="cbo_booking_year" id="cbo_booking_year" value="<? echo $cbo_booking_year;?>" />
                                <input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $company_id;?>" />
                                <input type="hidden" style="width:20px" name="cbo_buyer_name" id="cbo_buyer_name" value="<? echo $cbo_buyer_name;?>" />
                                <input type="hidden" name="cbo_currency" id="cbo_currency" value="<? echo $cbo_currency;?>" />
                                <input type="hidden" name="txt_booking_date" id="txt_booking_date" value="<? //echo $txt_booking_date;?>" />
                                <input type="hidden" name="cbo_currency_job" id="cbo_currency_job" value="<? echo $cbo_currency_job;?>" />
                                <input type="hidden" style="width:20px" name="cbo_supplier_name" id="cbo_supplier_name" value="<? echo $cbo_supplier_name;?>" /> 
                                <input type="hidden" name="cbo_trim_type" id="cbo_trim_type" value="<? echo $cbo_trim_type;?>" />
                                <input type="hidden" name="cbo_item_from" id="cbo_item_from" value="<? echo $cbo_item_from;?>" />
                            </th>
                        </tr>
                    </thead>
                    <?
					//echo $cbo_trim_type.'DDDD';
                    if($cbo_trim_type==0) $trim_cond="";else  $trim_cond="and a.trim_type in($cbo_trim_type)";
					?>
                    <tr class="general">
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
                        <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:70px"></td>
                        <td><? echo create_drop_down( "cbo_dealing_merchant", 100, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 group by id, team_member_name order by team_member_name ASC","id,team_member_name", 1, "-Deal. Merchant-", $selected, "" ); ?></td>
                        <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:70px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
                        <td><? echo create_drop_down( "cbo_item", 100, "select a.id,a.item_name from  lib_item_group a where  a.status_active =1 and a.is_deleted=0 and a.item_category=4 $trim_cond order by a.item_name","id,item_name", 1, "-- Select Item Name --", $selected, "",0 ); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" value="<? echo $from_date; ?>" <? //echo $date_disabled; ?>></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" value="<? echo $to_date; ?>" <? //echo $date_disabled; ?>></td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_item_from').value+'_'+document.getElementById('cbo_item_from').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_currency_job').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('cbo_item').value+'_'+document.getElementById('txt_ref_no').value+'_'+'<? echo $txt_booking_no; ?>'+'_'+'<? echo $cbo_level; ?>'+'_'+'<? echo $cbo_material_source; ?>'+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_dealing_merchant').value+'_'+'<? echo $txt_booking_date; ?>'+'_'+'<? echo $cbo_trim_type; ?>'+'_'+'<? echo $cbo_source; ?>'+'_'+'<? echo $cbo_pay_mode; ?>'+'_'+'<? echo $garments_nature; ?>', 'create_fnc_process_data', 'search_div', 'trims_group_entry_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:60px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                    <tr>
                </table>
            </form>
        </div>
        <div id="search_div"></div>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_fnc_process_data")
{
	//echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	//echo $data;
	//extract($_REQUEST);
	$data=explode('_',$data);
	$company_id=$data[0];
	$cbo_buyer_name=$data[1];
	$cbo_supplier_name=$data[2];
	//$cbo_booking_month=$data[3];
	$cbo_item_from=$data[3];
	$cbo_year_selection=$data[5];
	$cbo_currency=$data[6];
	$cbo_currency_job=$data[7];
	$txt_style=$data[8];
	$txt_order_search=$data[9];
	$txt_job=$data[10];
	$cbo_item=$data[11];
	$ref_no=$data[12];
	$booking_no=$data[13];
	$cbo_level=$data[14];
	$cbo_material_source=$data[15];
	$fromDate=$data[16];
	$toDate=$data[17];
	//echo $fromDate.'='.$toDate;
	$dealing_merchant=$data[18];
	$booking_date=$data[19];
	$trim_type=$data[20];
	$cbo_source=$data[21];
	$cbo_pay_mode=$data[22];
	$garments_nature=$data[23];
	//echo $cbo_item_from;

	if($txt_style == '' && $txt_job == '' && $ref_no == '' && $txt_order_search == '' && $cbo_item ==0 && $dealing_merchant==0 && $fromDate == '' && $toDate =='')
	{
		echo "<div align='center'><span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select any search data.</span></div> ";
		die;
	}

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";

	if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_year_selection"; else if($db_type==2) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";

	if ($txt_style!="") $style_cond=" and a.style_ref_no='$txt_style'"; else $style_cond=$txt_style;
	if ($txt_order_search!="") $order_cond=" and b.po_number='$txt_order_search'"; else $order_cond="";
	if ($ref_no!="") $ref_cond=" and b.grouping='$ref_no'"; else $ref_cond="";
	if ($txt_job!="") $job_cond=" and a.job_no_prefix_num='$txt_job'"; else $job_cond ="";
	if ($cbo_item!=0) $itemgroup_cond=" and b.trim_group=$cbo_item"; else $itemgroup_cond ="";
	if ($dealing_merchant!=0) $dealing_merchant_cond=" and a.dealing_marchant='$dealing_merchant'"; else $dealing_merchant_cond ="";

	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');

	extract(check_magic_quote_gpc($_REQUEST));

	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY')";
	}

	$shipment_date ="";
	$class_datecond ="";
	if ($fromDate!="" &&  $toDate!="")
	{
		if($db_type==0)
		{
			if ($fromDate!="" &&  $toDate!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($fromDate, "yyyy-mm-dd", "-")."' and '".change_date_format($toDate, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";

			if ($fromDate!="" &&  $toDate!="") $class_datecond = "between '".change_date_format($fromDate, "yyyy-mm-dd", "-")."' and '".change_date_format($toDate, "yyyy-mm-dd", "-")."'"; else $class_datecond ="";

		}
		else if($db_type==2)
		{
			if ($fromDate!="" &&  $toDate!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($fromDate, "yyyy-mm-dd", "-",1)."' and '".change_date_format($toDate, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";

			if ($fromDate!="" &&  $toDate!="") $class_datecond = "between '".change_date_format($fromDate, "yyyy-mm-dd", "-",1)."' and '".change_date_format($toDate, "yyyy-mm-dd", "-",1)."'"; else $class_datecond ="";
			//$year_field="to_char(a.insert_date,'YYYY')";
		}
	}
	else
	{
		//echo $start_date.'--'.$end_date; die;
		if($start_date!="" &&  $end_date!="")
		{
			if($db_type==0)
			{
				if ($start_date!="" &&  $end_date!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";

				if ($start_date!="" &&  $end_date!="") $class_datecond = "between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $class_datecond ="";
				 //$year_field="YEAR(a.insert_date)";
			}
			else if($db_type==2)
			{
				if ($start_date!="" &&  $end_date!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";

				if ($start_date!="" &&  $end_date!="") $class_datecond = "between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $class_datecond ="";
				//$year_field="to_char(a.insert_date,'YYYY')";
			}
		}

	}
	?>
	<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
	<input type="hidden" name="itemGroup" id="itemGroup" value="" />
	<input type="hidden" name="txt_job_id" id="txt_job_id" value="" />
	<input type="hidden" name="txt_selected_po" id="txt_selected_po" value="" />
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1205" class="rpt_table"  >
        <thead>
            <th width="25">SL</th>
            <th width="50">Buyer</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="60">File No</th>
            <th width="60">Ref. No</th>
            <th width="100">Style No</th>
            <th width="100">Ord. No</th>
            <th width="100">Trim Group</th>
            <th width="130">Desc.</th>
            <th width="70">Brand/ Sup.Ref</th>
            <th width="70"><? if($cbo_item_from==2) echo "PO Qty";else echo "Req. Qty"; ?></th>
            <th width="45">UOM</th>
            <th width="70">CU WOQ</th>
            <th width="70">Bal WOQ</th>
            <th width="45">Exch. Rate</th>
            <th width="40"><? if($cbo_item_from==2) echo "Unit Rate";else echo "Rate"; ?></th>
            <th><? if($cbo_item_from==2) echo "PO Amount";else echo "Amount"; ?> </th>
        </thead>
	</table>
	<div style="width:1225px; overflow-y:scroll; max-height:340px;" id="buyer_list_view" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1205" class="rpt_table" id="tbl_list_search" >
        <?
       // echo $cbo_item_from.'='.$end_date;
		if($trim_type==0) $trimCond="";else $trimCond="and trim_type=$trim_type";
		//echo $trimCond;
        $lib_item_group_arr=array();
        $sql_lib_item_group=sql_select("select id, item_name,trim_type, conversion_factor, order_uom as cons_uom from lib_item_group where status_active=1 $trimCond");
		//echo "select id, item_name,trim_type, conversion_factor, order_uom as cons_uom from lib_item_group where status_active=1 $trimCond";
        foreach($sql_lib_item_group as $rowitem){
            $lib_item_group_arr[$rowitem[csf('id')]][item_name]=$rowitem[csf('item_name')];
            $lib_item_group_arr[$rowitem[csf('id')]][conversion_factor]=$rowitem[csf('conversion_factor')];
            $lib_item_group_arr[$rowitem[csf('id')]][cons_uom]=$rowitem[csf('cons_uom')];
			$trim_type_arr[$rowitem[csf('id')]]=$rowitem[csf('trim_type')];
			$lib_item_group_id_arr[$rowitem[csf('id')]]=$rowitem[csf('id')];
        }
        unset($sql_lib_item_group);
		if($trim_type)
		{
		$item_id=implode(",",$lib_item_group_id_arr);
		$item_id_cond="and b.trim_group in($item_id)";
		} else  $item_id_cond="";
		//echo $item_id.'SSS';
		

	    $exceed_qty_level=return_field_value("exceed_qty_level", "variable_order_tracking", "company_name=$company_id  and variable_list=26 and status_active=1 and is_deleted=0");
		if( $exceed_qty_level==0 || $exceed_qty_level==2 || $exceed_qty_level=="") $exceed_qty_level=2;else $exceed_qty_level=$exceed_qty_level;
		//echo $exceed_qty_level.'DDD';;die;
       
		
		$cbo_item_from=str_replace("'","",$cbo_item_from);
		
     if($cbo_item_from==1) //Item From Pre Costing....
	 {
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
            $condition->po_number("='$txt_order_search'");
        }
		if(str_replace("'","",$fromDate)!='' && str_replace("'","",$toDate)!=''){
			   $condition->pub_shipment_date($class_datecond);
		 }
         if(str_replace("'","",$ref_no)!='')
         {
            $condition->grouping("='$ref_no'");
         }

        $condition->init();
        $trims= new trims($condition);
        //echo $trims->getQuery(); die;
        $req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
        //$trims= new trims($condition);
        $req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();

		  $sql_job="select a.job_no_prefix_num, a.job_no, $year_field  as year, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.id, b.po_number, b.file_no, b.grouping, b.po_quantity as plan_cut from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.company_name=$company_id and b.shiping_status not in(3) $buyer_id_cond $job_cond $dealing_merchant_cond $order_cond $ref_cond $style_cond $job_year_cond $shipment_date ";
		//echo $sql_job; die;
		$sql_jobRes=sql_select($sql_job); $jobData_arr=array(); $tot_rows=0; $poIds=''; $jobNo='';
		foreach($sql_jobRes as $jrow)
		{
			$tot_rows++;
			$poIds.=$jrow[csf('id')].",";
			$jobNo.="'".$jrow[csf('job_no')]."',";
			$jobData_arr[$jrow[csf('id')]]['jobPre']=$jrow[csf('job_no_prefix_num')];
			$jobData_arr[$jrow[csf('id')]]['job_no']=$jrow[csf('job_no')];
			$jobData_arr[$jrow[csf('id')]]['year']=$jrow[csf('year')];
			$jobData_arr[$jrow[csf('id')]]['company_name']=$jrow[csf('company_name')];
			$jobData_arr[$jrow[csf('id')]]['buyer_name']=$jrow[csf('buyer_name')];
			$jobData_arr[$jrow[csf('id')]]['currency_id']=$jrow[csf('currency_id')];
			$jobData_arr[$jrow[csf('id')]]['style_ref_no']=$jrow[csf('style_ref_no')];
			$jobData_arr[$jrow[csf('id')]]['po_number']=$jrow[csf('po_number')];
			$jobData_arr[$jrow[csf('id')]]['file_no']=$jrow[csf('file_no')];
			$jobData_arr[$jrow[csf('id')]]['grouping']=$jrow[csf('grouping')];
			$jobData_arr[$jrow[csf('id')]]['plan_cut']=$jrow[csf('plan_cut')];
		}
		unset($sql_jobRes);

		$poIds=chop($poIds,','); $poIds_bom_cond=""; $poIds_booking_cond=""; $poIds_tna_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_bom_cond=" and (";
			$poIds_booking_cond=" and (";
			$poIds_tna_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_bom_cond.=" c.po_break_down_id in($ids) or ";
				$poIds_booking_cond.=" b.po_break_down_id in($ids) or ";
				$poIds_tna_cond.=" po_number_id in($ids) or ";
			}

			$poIds_bom_cond=chop($poIds_bom_cond,'or ');
			$poIds_bom_cond.=")";

			$poIds_booking_cond=chop($poIds_booking_cond,'or ');
			$poIds_booking_cond.=")";
			$poIds_tna_cond=chop($poIds_tna_cond,'or ');
			$poIds_tna_cond.=")";
		}
		else
		{
			$poIds_bom_cond=" and c.po_break_down_id in ($poIds)";
			$poIds_booking_cond=" and b.po_break_down_id in ($poIds)";
			$poIds_tna_cond=" and po_number_id in ($poIds)";
		}

		$jobNos=implode(",",array_filter(array_unique(explode(",",$jobNo))));

		$cu_booking_arr=array();
		$sql_cu_booking=sql_select("select b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.wo_qnty as cu_wo_qnty, b.amount as cu_amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=2 and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $poIds_booking_cond");
        foreach($sql_cu_booking as $rowcu){
            $cu_booking_arr[$rowcu[csf('pre_cost_fabric_cost_dtls_id')]][$rowcu[csf('po_break_down_id')]]['cu_wo_qnty']+=$rowcu[csf('cu_wo_qnty')];
            $cu_booking_arr[$rowcu[csf('pre_cost_fabric_cost_dtls_id')]][$rowcu[csf('po_break_down_id')]]['cu_amount']+=$rowcu[csf('cu_amount')];
			$trimpreIdArr[$rowcu[csf('pre_cost_fabric_cost_dtls_id')]]=$rowcu[csf('pre_cost_fabric_cost_dtls_id')];
        }
        unset($sql_cu_booking);
	//	echo $previouse_pre_id=implode(",",$trimpreIdArr);
        

		$sql_supp="select trimid from wo_pre_cost_trim_supplier where job_no in ($jobNos) and supplier_id in($cbo_supplier_name) and is_deleted=0 and status_active=1";
		$sql_suppRes=sql_select( $sql_supp ); $trim_id="";
		foreach($sql_suppRes as $row)
		{
			$trim_id.=$row[csf('trimid')].",";
		}
		unset($sql_suppRes);
		$trim_ids=chop($trim_id,',');
		if($db_type==2)
		{
		if($trim_ids!="") $trim_idCond="and (b.id in ($trim_ids) or b.nominated_supp_multi is null)"; else $trim_idCond=" and (b.nominated_supp_multi is null or b.nominated_supp_multi=0)";
		}
		else
		{
			if($trim_ids!="") $trim_idCond="and (b.id in ($trim_ids) or b.nominated_supp_multi='')"; else $trim_idCond=" and b.nominated_supp_multi=''";
		}
		$tnasql=sql_select("select po_number_id,task_finish_date,task_number from tna_process_mst where    is_deleted= 0 and status_active=1 $poIds_tna_cond");
		//echo "select po_number_id,task_finish_date,task_number from tna_process_mst where    is_deleted= 0 and status_active=1 $poIds_tna_cond"; 

		foreach($tnasql as $tnarow){
			$task_finish_date_arr[$tnarow[csf('po_number_id')]][$tnarow[csf('task_number')]]=$tnarow[csf('task_finish_date')];
		}
		unset($tnasql);
			
		$tna_integrated=return_field_value("tna_integrated","variable_order_tracking","company_name='$company_id' and variable_list=14 and status_active=1 and is_deleted=0","tna_integrated");
		//echo $tna_integrated;

		$approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and b.page_id=25 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
		if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==1){
			$approval_cond="and a.approved in (1,2,3)";
		}else{
			
			if($approval_allow[0][csf("approval_need")]==2) // Issue Id=26656 for Libas
			{
				$approval_cond="";
			}
			else
			{
			$approval_cond="and a.approved in (1)";
			}
		}
		$source_cond='';
		if(!empty($cbo_source))
		{
			if($cbo_source*1==1)
			{
				$source_cond=" and b.source_id in (1,0)";
			}
			else{
				$source_cond=" and b.source_id in (2,0)";
			}
		}

		$sql="SELECT a.costing_per, a.exchange_rate, b.id as wo_pre_cost_trim_cost_dtls, b.trim_group, b.description, b.brand_sup_ref, b.rate, min(c.id) as id, b.nominated_supp_multi, c.po_break_down_id, avg(c.cons) AS cons from wo_pre_cost_mst a,  wo_pre_cost_trim_co_cons_dtls c,wo_pre_cost_trim_cost_dtls b where a.job_no=b.job_no and a.job_no=c.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id   $approval_cond and c.cons>0 and b.is_deleted=0 and b.status_active=1 $itemgroup_cond $poIds_bom_cond $item_id_cond $source_cond group by a.costing_per, a.exchange_rate, b.id, b.trim_group, b.description, b.brand_sup_ref, b.rate, b.nominated_supp_multi,c.po_break_down_id order by c.po_break_down_id, b.id asc"; //$trim_idCond

        $i=1; $total_req=0; $total_amount=0;
		//echo $sql;
		//if($poIds_bom_cond!='') //Check Need for Shipment Status
		$nameArray=sql_select( $sql );

        foreach ($nameArray as $row)
        {
        	
			if($trimpreIdArr[$row[csf('wo_pre_cost_trim_cost_dtls')]]=='')
			{
				
			$supplier_arr = explode("_", $row[csf('nominated_supp_multi')]);
			 
        	$supplier_arr_data=array();
        	if(count($supplier_arr) >0)
        	{
				if($cbo_pay_mode==3 || $cbo_pay_mode==5){
					$comsupplierdata_arr = explode(",", $supplier_arr[1]);
					if(count($comsupplierdata_arr) >0)
					{
						foreach ($comsupplierdata_arr as $value) {
							$supplier_arr_data[$value]=$value;
						}
					}
				}
				else{
					$supplierdata_arr = explode(",", $supplier_arr[0]);
					if(count($supplierdata_arr) >0)
					{
						foreach ($supplierdata_arr as $value) {
							$supplier_arr_data[$value]=$value;
						}
					}
				}				
        	}
			/* echo "<pre>";
			print_r($supplier_arr_data); die; */    	
			//echo $row[csf('nominated_supp_multi')].'SDD';
        	if(array_key_exists($cbo_supplier_name, $supplier_arr_data) || $row[csf('nominated_supp_multi')] =='' || $row[csf('nominated_supp_multi')] ==0)
        	{
        		//echo "joy".__LINE__; die;
        		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=$row[csf('po_break_down_id')];
				//else echo "B";
				// echo "B,";
				$cbo_currency_job=$jobData_arr[$poid]['currency_id'];
				$exchange_rate=$row[csf('exchange_rate')];
				if($cbo_currency==$cbo_currency_job){
					$exchange_rate=1;
				}
				$req_qnty_cons_uom=$req_qty_arr[$poid][$row[csf('wo_pre_cost_trim_cost_dtls')]];
				$req_amount_cons_uom=$req_amount_arr[$poid][$row[csf('wo_pre_cost_trim_cost_dtls')]];
				$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

				$req_qnty=def_number_format($req_qnty_cons_uom/$lib_item_group_arr[$row[csf('trim_group')]][conversion_factor],5,"");
				$cu_wo_qnty=def_number_format($cu_booking_arr[$row[csf('wo_pre_cost_trim_cost_dtls')]][$poid]['cu_wo_qnty'],5,"");
				$cu_wo_amnt=def_number_format($cu_booking_arr[$row[csf('wo_pre_cost_trim_cost_dtls')]][$poid]['cu_amount'],5,"");
				$bal_woq=$req_qnty;

				$rate=def_number_format(($rate_cons_uom*$lib_item_group_arr[$row[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
				$req_amount=def_number_format($req_qnty*$rate,5,"");

				$bal_wom=$req_amount-$cu_wo_amnt;

				$total_req_amount+=$req_amount;
				$total_cu_amount+=$row[csf('cu_amount')];

				$total_req+=$req_qnty;
				$amount=def_number_format($rate*$bal_woq,4,"");
				//-----------------------------------------TNA Check----------------------
			 
				
				if($bal_woq>0 && ($cu_wo_qnty=="" || $cu_wo_qnty==0) && $exceed_qty_level==2)
				{
					?>
					<tr bgcolor="<?=$td_row_color;?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i;?>)">
						<td width="25"><?=$i;?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<?=$row[csf('id')];?>"/>
							<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<?=$row[csf('wo_pre_cost_trim_cost_dtls')];?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<?=$jobData_arr[$poid]['job_no'];?>"/>
							<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<?=$jobData_arr[$poid]['po_number'];?>"/>
							<input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<?=$row[csf('trim_group')];?>"/>
						</td>
						<td width="50" style="word-break:break-all"><?=$buyer_arr[$jobData_arr[$poid]['buyer_name']];?></td>
						<td width="50" style="word-break:break-all"><?=$jobData_arr[$poid]['year'];?></td>
						<td width="50" style="word-break:break-all"><?=$jobData_arr[$poid]['jobPre'];?></td>
						<td width="60" style="word-break:break-all"><?=$jobData_arr[$poid]['file_no'];?></td>
						<td width="60" style="word-break:break-all"><?=$jobData_arr[$poid]['grouping'];?></td>
						<td width="100" style="word-break:break-all"><?=$jobData_arr[$poid]['style_ref_no'];?></td>
						<td width="100" title="<? echo $tna_found;?>" style="word-break:break-all"><?=$jobData_arr[$poid]['po_number'];?></td>
						<td width="100" style="word-break:break-all"><?=$lib_item_group_arr[$row[csf('trim_group')]][item_name];?></td>
						<td width="130" style="word-break:break-all" id="td_item_des<?php echo $i; ?>"><?=$row[csf('description')];?></td>
						<td width="70" style="word-break:break-all"><?=$row[csf('brand_sup_ref')];?></td>
						<td width="70" style="word-break:break-all" align="right"><?=number_format($req_qnty, 4);?></td>
						<td width="45" style="word-break:break-all"><?=$unit_of_measurement[$lib_item_group_arr[$row[csf('trim_group')]][cons_uom]];?></td>
						<td width="70" style="word-break:break-all" align="right"><?=def_number_format($cu_wo_qnty, 5, "");?></td>
						<td width="70" style="word-break:break-all" align="right"><?=number_format($bal_woq, 4);?></td>
						<td width="45" style="word-break:break-all" align="right"><?=number_format($exchange_rate, 2);?></td>
						<td width="40" style="word-break:break-all" align="right"><?=number_format($rate, 4);?></td>
						<td style="word-break:break-all" align="right"><?=number_format($amount, 2);?></td>
					</tr>
					<?
					$i++;
					$total_amount+=$amount;
				}
				elseif($bal_woq>0 && $cu_wo_qnty>0) //>=1
				{
					?>
					<tr bgcolor="<?=$td_row_color;?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i;?>)">
						<td width="25"><?=$i;?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<?=$row[csf('id')];?>"/>
							<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<?=$row[csf('wo_pre_cost_trim_cost_dtls')];?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<?=$jobData_arr[$poid]['job_no'];?>"/>
							<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<?=$jobData_arr[$poid]['po_number'];?>"/>
							<input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<?=$row[csf('trim_group')];?>"/>
						</td>
						<td width="50" style="word-break:break-all"><?=$buyer_arr[$jobData_arr[$poid]['buyer_name']];?></td>
						<td width="50" style="word-break:break-all"><?=$jobData_arr[$poid]['year'];?></td>
						<td width="50" style="word-break:break-all"><?=$jobData_arr[$poid]['jobPre'];?></td>
						<td width="60" style="word-break:break-all"><?=$jobData_arr[$poid]['file_no'];?></td>
						<td width="60" style="word-break:break-all"><?=$jobData_arr[$poid]['grouping'];?></td>
						<td width="100" style="word-break:break-all"><?=$jobData_arr[$poid]['style_ref_no'];?></td>
						<td width="100" style="word-break:break-all"><?=$jobData_arr[$poid]['po_number'];?></td>
						<td width="100" style="word-break:break-all"><?=$lib_item_group_arr[$row[csf('trim_group')]][item_name];?></td>
						<td width="130" style="word-break:break-all" id="td_item_des<?php echo $i; ?>"><?=$row[csf('description')];?></td>
						<td width="70" style="word-break:break-all"><?=$row[csf('brand_sup_ref')];?></td>
						<td width="70" style="word-break:break-all" align="right"><?=number_format($req_qnty, 4);?></td>
						<td width="45" style="word-break:break-all"><?=$unit_of_measurement[$lib_item_group_arr[$row[csf('trim_group')]][cons_uom]];?></td>
						<td width="70" style="word-break:break-all" align="right"><?=def_number_format($cu_wo_qnty, 5, "");?></td>
						<td width="70" style="word-break:break-all" align="right"><?=number_format($bal_woq, 4);?></td>
						<td width="45" style="word-break:break-all" align="right"><?=number_format($exchange_rate, 2);?></td>
						<td width="40" style="word-break:break-all" align="right"><?=number_format($rate, 4);?></td>
						<td style="word-break:break-all" align="right"><?=number_format($amount, 2);?></td>
					</tr>
					<?
					$i++;
					$total_amount+=$amount;
				}
				elseif($bal_wom>0  && $exceed_qty_level==1)
				{
					//echo $bal_wom.'='.$exceed_qty_level;die;
					?>
					<tr bgcolor="<?=$td_row_color;?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i;?>)">
						<td width="25"><?=$i;?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<?=$row[csf('id')];?>"/>
							<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<?=$row[csf('wo_pre_cost_trim_cost_dtls')];?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<?=$jobData_arr[$poid]['job_no'];?>"/>
							<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<?=$jobData_arr[$poid]['po_number'];?>"/>
							<input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<?=$row[csf('trim_group')];?>"/>
						</td>
						<td width="50" style="word-break:break-all"><?=$buyer_arr[$jobData_arr[$poid]['buyer_name']];?></td>
						<td width="50" style="word-break:break-all"><?=$jobData_arr[$poid]['year'];?></td>
						<td width="50" style="word-break:break-all"><?=$jobData_arr[$poid]['jobPre'];?></td>
						<td width="60" style="word-break:break-all"><?=$jobData_arr[$poid]['file_no'];?></td>
						<td width="60" style="word-break:break-all"><?=$jobData_arr[$poid]['grouping'];?></td>
						<td width="100" style="word-break:break-all"><?=$jobData_arr[$poid]['style_ref_no'];?></td>
						<td width="100" style="word-break:break-all"><?=$jobData_arr[$poid]['po_number'];?></td>
						<td width="100" style="word-break:break-all"><?=$lib_item_group_arr[$row[csf('trim_group')]][item_name];?></td>
						<td width="130" style="word-break:break-all" id="td_item_des<?php echo $i; ?>"><?=$row[csf('description')];?></td>
						<td width="70" style="word-break:break-all"><?=$row[csf('brand_sup_ref')];?></td>
						<td width="70" style="word-break:break-all" align="right"><?=number_format($req_qnty, 4);?></td>
						<td width="45" style="word-break:break-all"><?=$unit_of_measurement[$lib_item_group_arr[$row[csf('trim_group')]][cons_uom]];?></td>
						<td width="70" style="word-break:break-all" align="right"><?=def_number_format($cu_wo_qnty, 5, "");?></td>
						<td width="70" style="word-break:break-all" align="right"><?=number_format($bal_woq, 4);?></td>
						<td width="45" style="word-break:break-all" align="right"><?=number_format($exchange_rate, 2);?></td>
						<td width="40" style="word-break:break-all" align="right"><?=number_format($rate, 4);?></td>
						<td style="word-break:break-all" align="right"><?=number_format($amount, 2);?></td>
					</tr>
					<?
					$i++;
					$total_amount+=$amount;
				}
				elseif($bal_woq>0  && $cbo_material_source==3)
				{
					//echo $bal_wom.'='.$exceed_qty_level;die;
					?>
					<tr bgcolor="<?=$td_row_color;?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i;?>)">
						<td width="25"><?=$i;?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<?=$row[csf('id')];?>"/>
							<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<?=$row[csf('wo_pre_cost_trim_cost_dtls')];?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<?=$jobData_arr[$poid]['job_no'];?>"/>
							<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<?=$jobData_arr[$poid]['po_number'];?>"/>
							<input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<?=$row[csf('trim_group')];?>"/>
						</td>
						<td width="50" style="word-break:break-all"><?=$buyer_arr[$jobData_arr[$poid]['buyer_name']];?></td>
						<td width="50" style="word-break:break-all"><?=$jobData_arr[$poid]['year'];?></td>
						<td width="50" style="word-break:break-all"><?=$jobData_arr[$poid]['jobPre'];?></td>
						<td width="60" style="word-break:break-all"><?=$jobData_arr[$poid]['file_no'];?></td>
						<td width="60" style="word-break:break-all"><?=$jobData_arr[$poid]['grouping'];?></td>
						<td width="100" style="word-break:break-all"><?=$jobData_arr[$poid]['style_ref_no'];?></td>
						<td width="100" style="word-break:break-all"><?=$jobData_arr[$poid]['po_number'];?></td>
						<td width="100" style="word-break:break-all"><?=$lib_item_group_arr[$row[csf('trim_group')]][item_name];?></td>
						<td width="130" style="word-break:break-all" id="td_item_des<?php echo $i; ?>"><?=$row[csf('description')];?></td>
						<td width="70" style="word-break:break-all"><?=$row[csf('brand_sup_ref')];?></td>
						<td width="70" style="word-break:break-all" align="right"><?=number_format($req_qnty, 4);?></td>
						<td width="45" style="word-break:break-all"><?=$unit_of_measurement[$lib_item_group_arr[$row[csf('trim_group')]][cons_uom]];?></td>
						<td width="70" style="word-break:break-all" align="right"><?=def_number_format($cu_wo_qnty, 5, "");?></td>
						<td width="70" style="word-break:break-all" align="right"><?=number_format($bal_woq, 4);?></td>
						<td width="45" style="word-break:break-all" align="right"><?=number_format($exchange_rate, 2);?></td>
						<td width="40" style="word-break:break-all" align="right"><?=number_format($rate, 4);?></td>
						<td style="word-break:break-all" align="right"><?=number_format($amount, 2);?></td>
					</tr>
					<?
					$i++;
					$total_amount+=$amount;
				}
			}
         }
		} //Previous Pre cost Id Check End
		
		
	   } //Item From *****************End
	   else
	   {
		   
		     $sql_job="select a.job_no_prefix_num, a.job_no, $year_field  as year, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.id, b.po_number, b.file_no, b.grouping, b.po_quantity as plan_cut from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.company_name=$company_id and b.shiping_status not in(3) $buyer_id_cond $job_cond $dealing_merchant_cond $order_cond $ref_cond $style_cond $job_year_cond $shipment_date ";
		//echo $sql_job; 
		$sql_jobRes=sql_select($sql_job); $jobData_arr=array(); $tot_rows=0; $poIds=''; $jobNo='';
		foreach($sql_jobRes as $jrow)
		{
			$tot_rows++;
			$poIds.=$jrow[csf('id')].",";
			$jobNo.="'".$jrow[csf('job_no')]."',";
			$jobData_arr[$jrow[csf('id')]]['jobPre']=$jrow[csf('job_no_prefix_num')];
			$jobData_arr[$jrow[csf('id')]]['job_no']=$jrow[csf('job_no')];
			$jobData_arr[$jrow[csf('id')]]['year']=$jrow[csf('year')];
			$jobData_arr[$jrow[csf('id')]]['company_name']=$jrow[csf('company_name')];
			$jobData_arr[$jrow[csf('id')]]['buyer_name']=$jrow[csf('buyer_name')];
			$jobData_arr[$jrow[csf('id')]]['currency_id']=$jrow[csf('currency_id')];
			$jobData_arr[$jrow[csf('id')]]['style_ref_no']=$jrow[csf('style_ref_no')];
			$jobData_arr[$jrow[csf('id')]]['po_number']=$jrow[csf('po_number')];
			$jobData_arr[$jrow[csf('id')]]['file_no']=$jrow[csf('file_no')];
			$jobData_arr[$jrow[csf('id')]]['grouping']=$jrow[csf('grouping')];
			$jobData_arr[$jrow[csf('id')]]['plan_cut']=$jrow[csf('plan_cut')];
		}
		unset($sql_jobRes);

		$poIds=chop($poIds,','); $poIds_bom_cond=""; $poIds_booking_cond=""; $poIds_tna_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_bom_cond=" and (";
			$poIds_booking_cond=" and (";
			$poIds_tna_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_bom_cond.=" c.po_break_down_id in($ids) or ";
				$poIds_booking_cond.=" b.po_break_down_id in($ids) or ";
				$poIds_tna_cond.=" po_number_id in($ids) or ";
			}

			$poIds_bom_cond=chop($poIds_bom_cond,'or ');
			$poIds_bom_cond.=")";

			$poIds_booking_cond=chop($poIds_booking_cond,'or ');
			$poIds_booking_cond.=")";
			$poIds_tna_cond=chop($poIds_tna_cond,'or ');
			$poIds_tna_cond.=")";
		}
		else
		{
			$poIds_bom_cond=" and c.po_break_down_id in ($poIds)";
			$poIds_booking_cond=" and b.po_break_down_id in ($poIds)";
			$poIds_tna_cond=" and po_number_id in ($poIds)";
		}

		$jobNos=implode(",",array_filter(array_unique(explode(",",$jobNo))));

		$cu_booking_arr=array();
		$sql_cu_booking=sql_select("select b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.wo_qnty as cu_wo_qnty, b.amount as cu_amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=2 and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $poIds_booking_cond");
        foreach($sql_cu_booking as $rowcu){
            $cu_booking_arr[$rowcu[csf('pre_cost_fabric_cost_dtls_id')]][$rowcu[csf('po_break_down_id')]]['cu_wo_qnty']+=$rowcu[csf('cu_wo_qnty')];
            $cu_booking_arr[$rowcu[csf('pre_cost_fabric_cost_dtls_id')]][$rowcu[csf('po_break_down_id')]]['cu_amount']+=$rowcu[csf('cu_amount')];
			$trimpreIdArr[$rowcu[csf('pre_cost_fabric_cost_dtls_id')]]=$rowcu[csf('pre_cost_fabric_cost_dtls_id')];
        }
        unset($sql_cu_booking);
	//	echo $previouse_pre_id=implode(",",$trimpreIdArr);
        

		$sql_supp="select trimid from wo_pre_cost_trim_supplier where job_no in ($jobNos) and supplier_id in($cbo_supplier_name) and is_deleted=0 and status_active=1";
		$sql_suppRes=sql_select( $sql_supp ); $trim_id="";
		foreach($sql_suppRes as $row)
		{
			$trim_id.=$row[csf('trimid')].",";
		}
		unset($sql_suppRes);
		$trim_ids=chop($trim_id,',');
		 
			
		$tna_integrated=return_field_value("tna_integrated","variable_order_tracking","company_name='$company_id' and variable_list=14 and status_active=1 and is_deleted=0","tna_integrated");
		//echo $tna_integrated;

		$approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and b.page_id=25 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
		if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==1){
			$approval_cond="and a.approved in (1,2,3)";
		}else{
			
			if($approval_allow[0][csf("approval_need")]==2) // Issue Id=26656 for Libas
			{
				$approval_cond="";
			}
			else
			{
			$approval_cond="and a.approved in (1)";
			}
		}
		$source_cond='';
		if(!empty($cbo_source))
		{
			if($cbo_source*1==1)
			{
				$source_cond=" and b.source_id in (1,0)";
			}
			else{
				$source_cond=" and b.source_id in (2,0)";
			}
		}
			

		/* $sql="SELECT a.costing_per, a.exchange_rate, b.id as wo_pre_cost_trim_cost_dtls, b.trim_group, b.description, b.brand_sup_ref, b.rate, min(c.id) as id, b.nominated_supp_multi, c.po_break_down_id, avg(c.cons) AS cons from wo_pre_cost_mst a,  wo_pre_cost_trim_co_cons_dtls c,wo_pre_cost_trim_cost_dtls b where a.job_no=b.job_no and a.job_no=c.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id   $approval_cond and c.cons>0 and b.is_deleted=0 and b.status_active=1 $itemgroup_cond $poIds_bom_cond $item_id_cond $source_cond group by a.costing_per, a.exchange_rate, b.id, b.trim_group, b.description, b.brand_sup_ref, b.rate, b.nominated_supp_multi,c.po_break_down_id order by c.po_break_down_id, b.id asc"; *///$trim_idCond
		 
		 if($garments_nature==0) $garment_nature_cond=""; else $garment_nature_cond=" and a.garments_nature=$garments_nature";
		   $sql_job_color="select a.job_no_prefix_num, a.job_no, $year_field  as year, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, b.file_no, b.grouping, sum(c.order_quantity) as plan_cut,sum(c.order_total) as amount,min(c.id) as color_size_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_mst d where a.id=b.job_id and  a.id=c.job_id and  a.id=d.job_id and  c.job_id=d.job_id and b.id=c.po_break_down_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_id and b.shiping_status not in(3) $buyer_id_cond $job_cond $dealing_merchant_cond $order_cond $ref_cond $style_cond  $job_year_cond $shipment_date group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.id, b.po_number, b.file_no, b.grouping,a.insert_date order by a.job_no,b.id";
		   //$garment_nature_cond
			 

        $i=1; $total_req=0; $total_amount=0;
		//echo $sql;
		//if($poIds_bom_cond!='') //Check Need for Shipment Status
		$nameArray=sql_select( $sql_job_color );

        foreach ($nameArray as $row)
        {
        	
			if($trimpreIdArr[$row[csf('wo_pre_cost_trim_cost_dtls')]]=='')
			{
				
			
			/* echo "<pre>";
			print_r($supplier_arr_data); die; */    	
			//echo $row[csf('nominated_supp_multi')].'SDD';
        	
        		//echo "joy".__LINE__; die;
        		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=$row[csf('po_break_down_id')];
				//else echo "B";
				// echo "B,";
				$cbo_currency_job=$jobData_arr[$poid]['currency_id'];
				$exchange_rate=$row[csf('exchange_rate')];
				if($cbo_currency==$cbo_currency_job){
					$exchange_rate=1;
				}
				/*$req_qnty_cons_uom=$req_qty_arr[$poid][$row[csf('wo_pre_cost_trim_cost_dtls')]];
				$req_amount_cons_uom=$req_amount_arr[$poid][$row[csf('wo_pre_cost_trim_cost_dtls')]];
				$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

				$req_qnty=def_number_format($req_qnty_cons_uom/$lib_item_group_arr[$row[csf('trim_group')]][conversion_factor],5,"");
				$cu_wo_qnty=def_number_format($cu_booking_arr[$row[csf('wo_pre_cost_trim_cost_dtls')]][$poid]['cu_wo_qnty'],5,"");
				$cu_wo_amnt=def_number_format($cu_booking_arr[$row[csf('wo_pre_cost_trim_cost_dtls')]][$poid]['cu_amount'],5,"");
				$bal_woq=$req_qnty;

				$rate=def_number_format(($rate_cons_uom*$lib_item_group_arr[$row[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
				$req_amount=def_number_format($req_qnty*$rate,5,"");

				$bal_wom=$req_amount-$cu_wo_amnt;

				$total_req_amount+=$req_amount;
				$total_cu_amount+=$row[csf('cu_amount')];

				$total_req+=$req_qnty;
				$amount=def_number_format($rate*$bal_woq,4,"");*/
				//-----------------------------------------TNA Check----------------------
			 
				
				$req_qnty=$row[csf('plan_cut')];
				$amount=$row[csf('amount')];
				
					?>
					<tr bgcolor="<?=$td_row_color;?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i;?>)">
						<td width="25"><?=$i;?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<?=$poid;?>"/>
							<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<?=$row[csf('color_size_id')];?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<?=$jobData_arr[$poid]['job_no'];?>"/>
							<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<?=$jobData_arr[$poid]['po_number'];?>"/>
							<input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<?=$poid;?>"/>
						</td>
						<td width="50" style="word-break:break-all"><?=$buyer_arr[$jobData_arr[$poid]['buyer_name']];?></td>
						<td width="50" style="word-break:break-all"><?=$jobData_arr[$poid]['year'];?></td>
						<td width="50" style="word-break:break-all"><?=$jobData_arr[$poid]['jobPre'];?></td>
						<td width="60" style="word-break:break-all"><?=$jobData_arr[$poid]['file_no'];?></td>
						<td width="60" style="word-break:break-all"><?=$jobData_arr[$poid]['grouping'];?></td>
						<td width="100" style="word-break:break-all"><?=$jobData_arr[$poid]['style_ref_no'];?></td>
						<td width="100" title="<? echo $tna_found;?>" style="word-break:break-all"><?=$jobData_arr[$poid]['po_number'];?></td>
						<td width="100" style="word-break:break-all"><?=$lib_item_group_arr[$row[csf('trim_group')]][item_name];?></td>
						<td width="130" style="word-break:break-all" id="td_item_des<?php echo $i; ?>"><?=$row[csf('description')];?></td>
						<td width="70" style="word-break:break-all"><?=$row[csf('brand_sup_ref')];?></td>
						<td width="70" style="word-break:break-all" title="PO Qty" align="right"><?=number_format($req_qnty, 4);?></td>
						<td width="45" style="word-break:break-all"><?=$unit_of_measurement[$lib_item_group_arr[$row[csf('trim_group')]][cons_uom]];?></td>
						<td width="70" style="word-break:break-all" align="right"><?=def_number_format($cu_wo_qnty, 5, "");?></td>
						<td width="70" style="word-break:break-all" align="right"><?=number_format($bal_woq, 4);?></td>
						<td width="45" style="word-break:break-all" align="right"><?=number_format($exchange_rate, 2);?></td>
						<td width="40" style="word-break:break-all" align="right"><?=number_format($rate, 4);?></td>
						<td style="word-break:break-all" title="PO Amount" align="right"><?=number_format($amount, 2);?></td>
					</tr>
					<?
					$i++;
					$total_amount+=$amount;
				
				
			
         }//Previous Pre cost Id Check End
		} 
	  } //*******Item From Library End**************
	   
        ?>
        </table>

        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1205" class="rpt_table">
        	<tfoot>
                <th width="25">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="130">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70" id="value_total_req"></th>
                <th width="45"><input type="hidden" style="width:40px"  id="txt_tot_req_amount" value="<?=number_format($total_req_amount, 2);?>" /></th>
                <th width="70"><input type="hidden" style="width:40px" id="txt_tot_cu_amount" value="<?=number_format($total_cu_amount, 2);?>" /></th>
                <th width="70">&nbsp;</th>
                <th width="45">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th id="value_total_amount"><?=number_format($total_amount, 2);?></th>
            </tfoot>
        </table>
	</div>
	<table width="1205" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
if($action=='report_generate'){
	$process = array( &$_POST );
	//cbo_company_name*cbo_buyer_name*txt_job_no*hidden_job_id*txt_style_no*txt_inter_ref*hidden_item_id*txt_item_no*cbo_level
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_style_no=str_replace("'","",$txt_style_no);
	$txt_inter_ref=str_replace("'","",$txt_inter_ref);
	$hidden_item_id=str_replace("'","",$hidden_item_id);
	$txt_item_no=str_replace("'","",$txt_item_no);
	$cbo_level=str_replace("'","",$cbo_level);
	
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 order by item_name", "id", "item_name");

	$hidden_job_id=str_replace("'","",$hidden_job_id);
	$job_cond=""; $job_cond2=""; $job_cond3="";
	if($hidden_job_id !==''){
		$job_cond="and job_id=$hidden_job_id";
		$job_cond2="and a.id in(".str_replace("'","",$hidden_job_id).")";
		$job_cond3="and a.id=$hidden_job_id";
	}
	$hidden_item_id = str_replace("'", "", $hidden_item_id);
	$order_cond="";
		if($hidden_item_id>0){
			$order_cond=" and b.id in ($hidden_item_id)";

		}
	 if ($cbo_company_name==0 || $cbo_company_name=="") $company_name_cond=""; else $company_name_cond=" and a.company_name in ('$cbo_company_name') ";
	 if ($txt_job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no in ('$txt_job_no') ";
	 if ($txt_style_no=="") $style_no_cond=""; else $style_no_cond=" and a.style_ref_no in ('$txt_style_no') ";
	 if ($txt_inter_ref=="") $inter_ref_cond=""; else $inter_ref_cond=" and b.grouping in ('$txt_inter_ref') ";
	// if ($txt_item_no=="") $item_no_cond=""; else $item_no_cond=" and a.po_id in ($txt_item_no) ";

		$color_size_data_sql=sql_select("SELECT a.id as job_id, a.job_no, a.style_ref_no, b.id as po_id,b.po_number, b.grouping, d.trim_group, e.size_number_id, e.item_size, e.color_number_id, e.item_number_id,c.id as color_size_id,e.kimble_no,e.sku,e.barcode_code_no,e.fabrication,d.id as dtls_id FROM wo_po_details_master a JOIN wo_po_break_down b ON a.id = b.job_id JOIN wo_po_color_size_breakdown c ON a.id = c.job_id AND b.id = c.po_break_down_id JOIN wo_pre_cost_trim_cost_dtls d ON a.id = d.job_id AND b.job_id = d.job_id AND c.job_id = d.job_id JOIN wo_pre_cost_trim_co_cons_dtls e on d.id=e.WO_PRE_COST_TRIM_COST_DTLS_ID and e.COLOR_SIZE_TABLE_ID=c.id and a.id = e.job_id and b.job_id=e.job_id WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 $company_name_cond $job_cond2 $order_cond $job_no_cond $style_no_cond $inter_ref_cond $item_no_cond");
		
		$jobdateArr=array();
		foreach ($color_size_data_sql as $row) {
			
			//$colorWiseImageArr[$row[csf('job_id')]][$row[csf('po_id')]]['color']=$row[csf('color_number_id')];
			//$image_reference=$row[csf('job_id')]."_".$row[csf('po_id')]."_".$row[csf('trim_group')]."_".$row[csf('color_number_id')];
			//$image_reference_con=$row[csf('job_id')].$row[csf('po_id')].$row[csf('trim_group')].$row[csf('color_number_id')];
		}
			
		 //echo "<pre>";print_r($colorWiseImageArr);die;
		?>
		<table width="1250" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr> <?if($cbo_level==0){$colspan=13;}else $colspan=11;?>
                    <th colspan="<?=$colspan?>" ><b style="float:left; padding-right:20px;">Job No Generated Based on <?$level_arr= array(0=>"Color & Size Level",1=>"Color Level"); echo $level_arr[$cbo_level];?> </th>
				</tr>
				<tr>
					<?if($cbo_level==0)
					{?>
                    <th colspan="9"></th>
					<?}else{?>
					<th colspan="7"></th> 
					<?}?>
                    <th width="60"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(1);" id="chk_kimble"></th>
                    <th width="40"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(2);" id="chk_sku"></th>
                    <th width="40"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(3);" id="chk_barcode"></th>
                    <th width="40"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(4);" id="chk_fabrication"></th>
                    
                    </th>
				</tr>
                <tr>
                    <th width="40">SL</th>   
                    <th width="80">Job NO</th>
                    <th width="120">Style Ref</th>
                    <th width="120">IR/IB No</th>
                    <th width="100">Color</th>
					<?if($cbo_level==0)
					{?>
                    <th width="80">Gmt. Size </th>
                    <th width="80">Item Size</th>
					<?}?>
                    <th width="80">Item Group</th>
                    <th width="80">Add Image</th>
                    <th width="120">Kimble No.</th>
                    <th width="120">SKU</th>
                    <th width="120">Barcode No.</th>
					<th width="120">Fabrication</th>
                </tr>
            </thead>
            <tbody id="color_size_data">
            	<? $i++;
						foreach ($color_size_data_sql as $row)
						{	

											?>
											<tr>
												
												
												<td align="center"><?=$i; ?></td>
												<td style="word-break:break-all"><?=$row[csf('job_no')]; ?></td>
												<td style="word-break:break-all"><?=$row[csf('style_ref_no')]; ?></td>
												<td style="word-break:break-all"><?="IR: ".$row[csf('grouping')]."<br>"."PO: ".$row[csf('po_number')]; ?></td>
												<td style="word-break:break-all"><?= $colorArr[$row[csf('color_number_id')]]; ?></td>
												<?if($cbo_level==0){?>
												<td style="word-break:break-all"><?=$itemSizeArr[$row[csf('size_number_id')]]; ?>
													<input type="hidden" id="jobid_<?=$i; ?>" value="<?=$row[csf('job_id')]; ?>">
													<input type="hidden" id="jobno_<?=$i; ?>" value="<?=$row[csf('job_no')]; ?>">
													<input type="hidden" id="poid_<?=$i; ?>" value="<?=$row[csf('po_id')]; ?>">
													<input type="hidden" id="colorsizeid_<?=$i; ?>" value="<?=$row[csf('color_size_id')]; ?>">

													
													<input type="hidden" id="styleref_<?=$i; ?>" value="<?=$row[csf('style_ref_no')]; ?>">
													<input type="hidden" id="grouping_<?=$i; ?>" value="<?=$row[csf('grouping')]; ?>">
													<input type="hidden" id="gmstcolorid_<?=$i; ?>" value="<?=$row[csf('color_number_id')]; ?>">
													<input type="hidden" id="gmtssizeid_<?=$i; ?>" value="<?=$row[csf('size_number_id')]; ?>">
													<input type="hidden" id="itemnumberid_<?=$i; ?>" value="<?=$row[csf('trim_group')]; ?>">
												</td>
												<td>
													<input type="text" class="text_boxes" id="txtitemsizeid_<?=$i; ?>" value="<?=$row[csf('item_size')]; ?>" style="width:108px;" >
												</td>
												<?}?>
												<td style="word-break:break-all"><?=$lib_item_group_arr[$row[csf('trim_group')]]; ?></td>
												<td>
													<? $image_reference_con=$row[csf('job_id')].$row[csf('po_id')].$row[csf('dtls_id')].$row[csf('color_number_id')];?>
													<input type="button" class="image_uploader" id="image_button_front_<?=$i; ?>" value="Attach" style="width:90px;" onClick="file_uploader ( '../../', <?=$image_reference_con?>,'', 'trim_group_entry', 0 ,1)">
												</td>
												<td>
													<input type="text" class="text_boxes" id="txtkimbleno_<?=$i; ?>" value="<?=$row[csf('kimble_no')]; ?>" onChange="copy_value(this.value,'txtkimbleno_',<?=$i; ?>);" style="width:108px;" >
												</td>
												<td>
													<input type="text" class="text_boxes" id="txtsku_<?=$i; ?>" value="<?=$row[csf('sku')]; ?>" onChange=" copy_value(this.value,'txtsku_',<?=$i; ?>);" style="width:108px;"  >
												</td>
												<td>
													<input type="text" class="text_boxes" id="txtbarcodeno_<?=$i; ?>" value="<?=$row[csf('barcode_code_no')]; ?>" onChange="copy_value(this.value,'txtbarcodeno_',<?=$i; ?>);" style="width:108px;">
												</td>
												<td>
													<input type="text" class="text_boxes" id="txtfabrication_<?=$i; ?>" value="<?=$row[csf('fabrication')]; ?>" onChange="copy_value(this.value,'txtfabrication_',<?=$i; ?>);" style="width:108px;">
												</td>
											</tr>
										<?
											$i++;
		
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
					<?if($cbo_level==0)
					{?>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
					<?}?>
            		<td>
                    	<input type="hidden" class="text_boxes_numeric" id="total_qty" value="<?=$total_qty; ?>" style="width:60px;" readonly>
                    	<input type="hidden" id="hiddreportlevel" value="<?=$operation; ?>">
                    </td>
                    <td>&nbsp;</td>
            		<td>&nbsp;</td>
                    <td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		 
            	</tr>
            </tfoot>
		</table>
        <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		<br>
     <?  ?>
		<? echo load_submit_buttons( $permission, "fnc_order_entry_details", 1,0 ,"",2); ?>
	<?
}
//WO_TRIMS_GROUP_MST
/*ID*SYSTEM_NO_PREFIX*SYSTEM_NO_PREFIX_NUM*SYSTEM_NO*JOB_NO*GMTS_COLOR_ID*ITEM_NUMBER_ID*GMTS_SIZE*ITEM_SIZE*STYLE_REF_NO*GROUPING*JOB_ID*ENTRY_FORM_ID*KIMBLE_NO*SKU*BARCODE_NO*FABRICATION*INSERTED_BY*INSERT_DATE*UPDATED_BY*UPDATE_DATE*STATUS_ACTIVE*IS_DELETED
jobid_*jobno_*poid_*styleref_*grouping_*gmstcolorid_*gmtssizeid_*itemsizeid_*itemnumberid_*txtitemsizeid_*image_button_front_*txtkimbleno_*txtsku_*txtbarcodeno_*txtfabrication_*
 */
if($action=='save_update_delete'){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	if ($operation==1)
	{
		if ($reporttype==2)
		{ 
			$budget_field_array_up="kimble_no*sku*barcode_code_no*fabrication*updated_by*update_date";
			$counter=0; $rID1=1;
			for($m=1; $m<=$row_table; $m++)
			{
				$jobid="jobid_".$m;
				$jobno="jobno_".$m;
				$poid="poid_".$m;
				$styleref="styleref_".$m;
				$grouping="grouping_".$m;
				$gmstcolorid="gmstcolorid_".$m;
				$gmtssizeid="gmtssizeid_".$m;
				$itemsizeid="itemsizeid_".$m;
				$itemnumberid="itemnumberid_".$m;
				$txtitemsizeid="txtitemsizeid_".$m;
				$image_buttonfront_="image_button_front_".$m;
				$txtkimbleno="txtkimbleno_".$m;
				$txtsku="txtsku_".$m;
				$txtbarcodeno_="txtbarcodeno_".$m;
				$txtfabrication="txtfabrication_".$m;
				$colorsizeid="colorsizeid_".$m;

				$poidarr[]=str_replace("'",'',$$poid);
				$jobidarr[]=str_replace("'",'',$$jobid);
				$sizeid=explode(",",str_replace("'",'',$$txtitemsizeid));
				$id_arr[]=str_replace("'",'',$$colorsizeid);


				$exjobid=explode(",",str_replace("'",'',$$jobidarr));
				$excolorsizeid=explode(",",str_replace("'",'',$$colorsizeid));
				$budget_data_array_up[str_replace("'",'',$$colorsizeid)] =explode("*",("".$$txtkimbleno."*".$$txtsku."*".$$txtbarcodeno_."*".$$txtfabrication."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));

				$counter++;
				if($budget_data_array_up!="" && $counter==100){
					$rID1=execute_query(bulk_update_sql_statement("wo_pre_cost_trim_co_cons_dtls", "color_size_table_id",$budget_field_array_up,$budget_data_array_up,$id_arr ));
					//echo "10**=".bulk_update_sql_statement("wo_pre_cost_trim_co_cons_dtls", "color_size_table_id",$budget_field_array_up,$budget_data_array_up,$id_arr);die;
					$counter=0;
					$id_arr=array();
					$budget_data_array_up=array();
				}
				/*foreach($excolorsizeid as $exczid)
				{
					$budget_data_array_up[$exczid] =explode("*",("".$$txtkimbleno."*".$$txtsku."*".$$txtbarcodeno_."*".$$txtfabrication."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				
					$counter++;
					$id_arr[]=$exczid;
						if($budget_data_array_up!=""){
							$rID1=execute_query(bulk_update_sql_statement("wo_pre_cost_trim_co_cons_dtls", "color_size_table_id",$budget_field_array_up,$budget_data_array_up,$id_arr ));
							//echo "10**=".bulk_update_sql_statement("wo_pre_cost_trim_co_cons_dtls", "color_size_table_id",$budget_field_array_up,$budget_data_array_up,$id_arr);die;
							$counter=0;
							$id_arr=array();
							$budget_data_array_up=array();
						}
					
				}*/
				

			}
			//echo "10**==".$counter; die;
			//echo "10**".count($budget_data_array_up);die;
			if(count($budget_data_array_up)>0 && $counter!=100){
				$rID1=execute_query(bulk_update_sql_statement("wo_pre_cost_trim_co_cons_dtls", "color_size_table_id",$budget_field_array_up,$budget_data_array_up,$id_arr ));
			//echo "10**=".bulk_update_sql_statement("wo_pre_cost_trim_co_cons_dtls", "color_size_table_id",$budget_field_array_up,$budget_data_array_up,$id_arr);die;
			}
			
		}
		
	 if($db_type==2 || $db_type==1 )
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