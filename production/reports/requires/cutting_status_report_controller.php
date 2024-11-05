<?
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if($action=="style_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
   <script>
		function js_set_value( str )
		{

			$('#txt_selected_no').val(str);
			parent.emailwindow.hide();
		}
    </script>


    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	//$job_year=str_replace("'","",$job_year);
	if($buyer!=0) $buyer_cond=" and a.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(a.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(a.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(a.insert_date,'YYYY')";
	}

	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a where a.company_name=$company $buyer_cond  and is_deleted=0 order by job_no_prefix_num";
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","410","300",0, $sql , "js_set_value", "id,job_no,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","","") ;
	//echo "<input type='hidden' id='txt_selected_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref_no;?>';
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
if($action=="order_no_search_popup")
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>', 'create_order_no_search_list_view', 'search_div', 'cutting_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$job_no=$data[6];
	if($job_no!='') $job_no_cond="and a.job_no='$job_no'";else $job_no_cond="";

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
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond order by b.id, b.pub_shipment_date";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit();
}
if($action=="size_wise_repeat_cut_no")
{
	$size_wise_repeat_cut_no=return_field_value("gmt_num_rep_sty","variable_order_tracking","company_name='$data' and variable_list=28 and is_deleted=0 and status_active=1");
	if($size_wise_repeat_cut_no==1) $size_wise_repeat_cut_no=$size_wise_repeat_cut_no; else $size_wise_repeat_cut_no=0;
	echo "document.getElementById('size_wise_repeat_cut_no').value = '".$size_wise_repeat_cut_no."';\n";
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "onchange_buyer()" );
	exit();
}

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_name", 142, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in ($data) order by location_name","id,location_name", 0, "-- Select  --", $selected, "" );
    exit();
}


if($action=="report_generate")
{
	// var_dump($_REQUEST) ;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name"  );
	$size_library=return_library_array("SELECT id,size_name from lib_size ","id","size_name");
	$season_library=return_library_array("SELECT id,season_name from lib_buyer_season ","id","season_name");
	// echo "<pre>";print_r($color_library);	die;
	// =======================GETTING SEARCH PARAMETER==========================
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_working_company=str_replace("'","",$cbo_working_company_name);
	$cbo_location=str_replace("'","",$cbo_location_name);
	$cbo_buyer=str_replace("'","",$cbo_buyer_name);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
     //$txt_job_no=str_replace("'","",$txt_job_no);
	$hidden_job_id=str_replace("'","",$txt_job_no_hidden);
	$txt_internal_ref_no=trim(str_replace("'","",$txt_internal_ref_no));
	$txt_date = $txt_date;
	$order_no = str_replace("'","",$txt_order_no);
	$hidden_order_id=str_replace("'","",$hide_order_id);
	$style_owner = str_replace("'","",$cbo_style_owner);
	if($type==1)// Today Production
	{
		$company_cond="";
		$company_cond_lay="";
		$company_cond_lay_a="";
		$company_cond_delv="";
		$str_po_cond="";
		$str_po_cond2="";
		if($cbo_working_company!=0) $company_cond.=" and d.serving_company in($cbo_working_company)";
		if($cbo_working_company!=0) $company_cond_lay.=" and d.working_company_id in($cbo_working_company)";
		if($cbo_working_company!=0) $company_cond_lay_a.=" and a.working_company_id in($cbo_working_company)";
		if($cbo_working_company!=0) $company_cond_delv.=" and d.delivery_company_id in($cbo_working_company)";
		if($txt_job_no!= 0) $str_po_cond.=" and a.job_no=$txt_job_no";
		if($txt_job_no!= 0) $str_po_cond2.=" and a.job_no=$txt_job_no";
		if ($txt_internal_ref_no=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping= '$txt_internal_ref_no'";
		if($cbo_buyer!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer";
		if($cbo_buyer!=0) $str_po_cond2.=" and a.buyer_name=$cbo_buyer";
		if($cbo_location) $str_po_cond.=" and d.location_id in($cbo_location)";
		if($cbo_location) $str_po_cond2.=" and d.location in($cbo_location)";
		if($order_no) $str_po_cond.=" and b.po_number in($txt_order_no)";

		$location_name=str_replace("'", "", $cbo_location_name);
		if($location_name) $location_cond.=" and a.location_id in($location_name)";
		if($location_name) $location_cond2.=" and a.location in($location_name)";

		if($hidden_order_id)
		{
			$str_po_cond.=" and b.id in($hidden_order_id)";
			$str_po_cond2.=" and b.id in($hidden_order_id)";
		}
		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);
		$str_com_cond_lay=str_replace("d.working_company_id", "a.working_company_id", $company_cond_lay);

		// =================================== GETTING TODAY CUTTING PO ID =================================
		$today_lay_sql="SELECT b.color_id,c.order_id as po_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
		where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and b.mst_id=c.mst_id  and a.entry_date=$txt_date and company_id in($company_name) $str_com_cond_lay $location_cond and a.status_active in(1) and a.is_deleted=0 and b.status_active in(1) and b.is_deleted=0 and c.status_active in(1) and c.is_deleted=0 " ;
		$today_lay_res = sql_select($today_lay_sql);
		$po_id_arr = [];
		$color_id_arr = [];
		foreach ($today_lay_res as $key => $val) {
			if($val[csf('po_id')] != "")
			{
				$po_id_arr[$val[csf('po_id')]] = $val[csf('po_id')];
				$color_id_arr[$val[csf('color_id')]] = $val[csf('color_id')];
			}

		}

		// ================================ TODAY PRODUCTION PO ID ================================
		$str_com_cond_prod = str_replace("d.serving_company", "a.serving_company", $company_cond);
		$today_pro_sql="SELECT b.color_number_id,a.po_break_down_id as po_id from pro_garments_production_mst a,wo_po_color_size_breakdown b,pro_garments_production_dtls c
		where a.id=c.mst_id and c.color_size_break_down_id=b.id and  a.po_break_down_id=b.po_break_down_id and c.status_active=1 and a.production_date=$txt_date and a.company_id in($company_name) $str_com_cond_prod $location_cond2 and b.status_active in(1) and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.production_type in(1,2,3,4) group by b.color_number_id,a.po_break_down_id" ; // $str_com_cond_prod
		$today_pro_res = sql_select($today_pro_sql);

		foreach ($today_pro_res as $key => $val) {
			if($val[csf('po_id')] != "")
			{
				$po_id_arr[$val[csf('po_id')]] = $val[csf('po_id')];
				$color_id_arr[$val[csf('color_id')]] = $val[csf('color_id')];
			}

		}


		$po_ids =trim(implode(',', $po_id_arr),",");
		$color_ids = trim(implode(',', $color_id_arr),",");
		// ========================= FOR PO ID ARRAY ==========================
		if(count($po_id_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_arr, 999);
	     	$po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="")
	     		{
	     			$po_ids_cond.=" and ( d.po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or   d.po_break_down_id in ($imp_ids) ";
	     		}
	     	}
	     	 $po_ids_cond.=" )";
	    }
	    else
	    {
	     	$po_ids_cond= " and d.po_break_down_id in($po_ids) ";
	    }

	     // ========================= FOR COLOR ID ARRAY ==========================

	    if(count($color_id_arr)>999 && $db_type==2)
	    {
	     	$color_chunk=array_chunk($color_id_arr, 999);
	     	$color_ids_cond= "";
	     	foreach($color_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($color_ids_cond=="")
	     		{
	     			$color_ids_cond.=" and ( e.color_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$color_ids_cond.=" or   e.color_id in ($imp_ids) ";
	     		}
	     	}
	     	 $color_ids_cond.=" )";
	     }
	     else
	     {
	     	$color_ids_cond= " and e.color_id in($color_ids) ";
	     }

	    // ================================== FOR CUT AND LAY ===================================
		$style_owner_cond = $style_owner ?  " and a.style_owner=$style_owner " : "";
	    $prod_po_id_lay = str_replace('d.po_break_down_id', 'f.order_id', $po_ids_cond);
		$lay_sql = "SELECT e.color_id as color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
			where a.job_no=b.job_no_mst  and b.id=c.po_break_down_id and d.job_no=a.job_no and b.id=f.order_id and d.id=e.mst_id  and a.job_no=c.job_no_mst and e.mst_id=f.mst_id and f.order_id=b.id and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1)  and b.is_deleted=0 and c.status_active in(1) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.company_id in($company_name)   $str_po_cond $internal_ref_cond    $prod_po_id_lay $style_owner_cond
			group by e.color_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping "; // $company_cond_lay
			// echo $lay_sql;//die();
			$production_main_array=[];
			foreach(sql_select($lay_sql) as $row)
			{
				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("working_company_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];
				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["internal_ref"]=$row[csf("grouping")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

				$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
				unset($prod_po_id_array[$row[csf("po_id")]]);

			}

			// echo "<pre>";
			// print_r($production_main_array);die();
			//=========================== FOR FINISH FAB QNTY ===========================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'a.po_break_down_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'b.color_number_id', $color_ids_cond);

			$booking_no_fin_qnty_array=array();
			$booking_sql=sql_select("SELECT a.po_break_down_id ,b.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown b  where b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $prod_po_id_lay $prod_color_id_lay ");
			foreach($booking_sql as $vals)
			{
				$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];
			}

			// ==================================== FOR ISSUE ====================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'po_breakdown_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'color_id', $color_ids_cond);

			$issue_sql=sql_select("SELECT po_breakdown_id,color_id,trans_id, sum(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=18 $prod_po_id_lay $prod_color_id_lay group by po_breakdown_id,color_id,trans_id");
			foreach($issue_sql as $values)
			{
			 	$issue_qnty_arr[$values[csf("po_breakdown_id")]][$values[csf("color_id")]]+=$values[csf("qnty")];
			}
			//======================================= FOR CONSUMPTION ===========================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'b.po_break_down_id', $po_ids_cond);
			$result_consumtion=array();
			$sql_consumtiont_qty=sql_select("SELECT a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id $prod_po_id_lay GROUP BY a.job_no, a.body_part_id");

			foreach($sql_consumtiont_qty as $row_consum)
			{
				$result_consumtion[$row_consum[csf('job_no')]]+=($row_consum[csf("requirment")]/$row_consum[csf("pcs")]);
			}

			// ========================================FOR LAY QUANTITY=====================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'c.order_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'b.color_id', $color_ids_cond);

			$lay_sqls="SELECT  a.job_no,c.order_id, b.gmt_item_id,b.color_id,sum( case when a.entry_date=$txt_date then  c.size_qty else 0 end) as today_lay,sum(CASE WHEN a.entry_date <= $txt_date THEN c.size_qty ELSE 0 END) as total_lay from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and b.mst_id=c.mst_id and a.entry_form=99 and company_id in($company_name) $str_com_cond_lay $location_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $prod_po_id_lay  group by a.job_no,c.order_id, b.gmt_item_id,b.color_id ";

			$lay_qnty_array=array();
			foreach(sql_select($lay_sqls) as $vals)
			{
				$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("color_id")]]["today_lay"]+=$vals[csf("today_lay")];
				$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("total_lay")];
			}

			// =================================== FOR ORDER QUANTITY ==================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'po_break_down_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'color_number_id', $color_ids_cond);
			$order_qnty_array=array();
			$order_qnty_sqls="SELECT po_break_down_id,color_number_id,order_quantity,item_number_id from wo_po_color_size_breakdown where status_active in(1) and is_deleted=0 $prod_po_id_lay ";
			 foreach(sql_select($order_qnty_sqls) as $values)
			 {
			 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("color_number_id")]]+=$values[csf("order_quantity")];
			 }
			 // echo "<pre>";
			 // print_r($order_qnty_array);
			 // =================================== FOR PRODUCTION DATA ==================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'b.id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'c.color_number_id', $color_ids_cond);
			$location_cond_prod = str_replace('a.location', 'd.location', $location_cond2);

			$order_sql="SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no  ,   c.color_number_id, sum(c.order_quantity) as order_quantity,

			sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_date then e.production_qnty else 0 end ) as today_cutting ,
			sum(case when d.production_type=1 and e.production_type=1 and d.production_date<=$txt_date then e.production_qnty else 0 end ) as total_cutting ,
			sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_date then e.production_qnty else 0 end ) as today_sewing_input ,
			sum(case when d.production_type=4 and e.production_type=4 and d.production_date<=$txt_date then e.production_qnty else 0 end ) as total_sewing_input ,
			sum(CASE WHEN d.production_type =2 and embel_name in(1,2) and d.production_date=$txt_date THEN e.production_qnty ELSE 0 END) AS today_emb_send_qnty,
			sum(CASE WHEN d.production_type =2 and embel_name in(1,2) and d.production_date<=$txt_date THEN e.production_qnty ELSE 0 END) AS total_emb_send_qnty,
			sum(CASE WHEN d.production_type =3 and embel_name in(1,2) and d.production_date=$txt_date THEN e.production_qnty ELSE 0 END) AS today_emb_rcv_qnty,
			sum(CASE WHEN d.production_type =3 and embel_name in(1,2) and d.production_date<=$txt_date THEN e.production_qnty ELSE 0 END) AS total_emb_rcv_qnty

			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
			where  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $po_ids_cond  $company_cond $location_cond_prod and d.production_type in(1,2,3,4)
			group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number, c.item_number_id, c.color_number_id,e.cut_no ";

			 // echo $order_sql;die;
			$serving_company_check = [];
			$order_sql_res = sql_select($order_sql);
			foreach($order_sql_res as $vals)
			{
				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_cutting"]+=$vals[csf("today_cutting")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_cutting"]+=$vals[csf("total_cutting")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_emb_send_qnty"]+=$vals[csf("today_emb_send_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_emb_send_qnty"]+=$vals[csf("total_emb_send_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_emb_rcv_qnty"]+=$vals[csf("today_emb_rcv_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_emb_rcv_qnty"]+=$vals[csf("total_emb_rcv_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["order_quantity"]+=$vals[csf("order_quantity")];
				$serving_company_check[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]][$vals[csf("serving_company")]]=$vals[csf("serving_company")];

			}
			// echo "<pre>";
			// print_r($serving_company_check);die;


			if(count($production_main_array)==0)
			{
				echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px;text-align:center">No Data Found.</div>'; die;
			}

			// echo "<pre>";
			// print_r($production_main_array);//die;

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
	            hr {
	                border: 0;
	                background-color: #000;
	                height: 1px;
	            }
	            .gd-color
	            {
					background: #f0f9ff; /* Old browsers */
					background: -moz-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f9ff', endColorstr='#a1dbff',GradientType=0 ); /* IE6-9 */
				}
				.gd-color2
				{
					background: rgb(247,251,252); /* Old browsers */
					background: -moz-linear-gradient(top, rgba(247,251,252,1) 0%, rgba(217,237,242,1) 40%, rgba(173,217,228,1) 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7fbfc', endColorstr='#add9e4',GradientType=0 ); /* IE6-9 */
					font-weight: bold;
				}
				.gd-color2 td
				{
					border: 1px solid #777;
					text-align: right;
				}
				.gd-color3
				{
					background: rgb(254,255,255); /* Old browsers */
					background: -moz-linear-gradient(top, rgba(254,255,255,1) 0%, rgba(221,241,249,1) 35%, rgba(160,216,239,1) 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#feffff', endColorstr='#a0d8ef',GradientType=0 ); /* IE6-9 */
					border: 1px solid #dccdcd;
					font-weight: bold;
				}

	        </style>
                <div style="width:3150px; margin: 0 auto">
                    <table width="3140" cellspacing="0" style="margin: 20px 0">
                        <tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td align="right" style="border:none; font-size:14px; font-weight: bold;" width="40%">
                                Working Company Name
                            </td>
                            <td align="center" style="border:none; font-size:14px;font-weight: bold;" width="5%">
                                 :
                            </td>
                            <td align="left" style="border:none; font-size:14px;font-weight: bold;" width="55%">
                                <?
	        					$comp_names="";
	        					foreach(explode(",",$cbo_working_company) as $vals)
	        					{
	        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
	        					}
	        					echo $comp_names;
	        					 ?>
                            </td>
                        </tr>
                        <tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td align="right" style="border:none; font-size:14px;font-weight: bold;" width="40%">
                                Date
                            </td>
                            <td align="center" style="border:none; font-size:14px;font-weight: bold;" width="5%">
                                :
                            </td>
                            <td align="left" style="border:none; font-size:14px;font-weight: bold;" width="55%">
                                <? echo change_date_format(str_replace("'", "", $txt_date)); ?>
                            </td>
                        </tr>
                    </table>



                <div>
                    <table width="3140" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                        <thead>
                            <tr>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="20">Sl.</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="150">Working Company</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="120">Location</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="150">Buyer</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="100">Job No.</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="100">Internal Ref.</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="110">Style ref.</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="110">Ship Status</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="90">Ship Date</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="110">Order No.</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="80">Color Qnty</th>
                                <th style="word-wrap: break-word;word-break: break-all;" colspan="5" width="500">Fabric Status</th>
                                <th style="word-wrap: break-word;word-break: break-all;" colspan="3" width="300">Lay Status</th>
                                <th style="word-wrap: break-word;word-break: break-all;" colspan="3" width="300">Cutting Status</th>
                                <th style="word-wrap: break-word;word-break: break-all;" colspan="5" width="500">Emblishment Status</th>
                                <th style="word-wrap: break-word;word-break: break-all;" colspan="4" width="400">Input Status</th>
                             </tr>
                             <tr>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="160">Color Name</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="90">F.Fab Req.</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="90">Fin. Fab Issue</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="90">F.Issued Balance</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="90">Possible Cut Qty.</th>

                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Today</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Total</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Lay.Balance</th>

                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Today</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Total</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Cut.Balance</th>

                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Today Send</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Total Send</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Today Rcv</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Total Rcv</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Print B/L</th>

                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Today</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Total</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Order-Input Balance</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Inhand Qty</th>
                             </tr>
                        </thead>
                    </table>
                    <div style="max-height:425px; overflow-y:scroll; width:3160px" id="scroll_body">
                        <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="3140" rules="all" id="table_filter" >
                        <?
                        $i=1;
						$jj=1;
						$grand_color_total = 0;
						// fabric status sum
						$grand_fab_req = 0;
						$grand_fin_fab_req = 0;
						$grand_fab_issued_balance = 0;
						$grand_fab_possible_qty = 0;
						// lay status sum
						$grand_today_lay=0;
						$grand_total_lay=0;
						$grand_lay_balance=0;
						// cutting status sum
						$grand_today_cutting=0;
						$grand_total_cutting=0;
						$grand_cut_balance=0;
						// emblishment status sum
						$grand_today_send=0;
						$grand_total_send=0;
						$grand_today_rcv=0;
						$grand_total_rcv=0;
						$grand_embl_balance=0;
						// sewing status sum
						$grand_today_input=0;
						$grand_total_input=0;
						$grand_input_balance=0;
						$grand_inhand_qty=0;
						foreach($production_main_array as $style_id=>$job_data)
						{
							foreach($job_data as $job_id=>$po_data)
							{
								$order_wise_color_total = 0;
								// fabric status sum
								$order_wise_fab_req = 0;
								$order_wise_fin_fab_req = 0;
								$order_wise_fab_issued_balance = 0;
								$order_wise_fab_possible_qty = 0;
								// lay status sum
								$order_wise_today_lay=0;
								$order_wise_total_lay=0;
								$order_wise_lay_balance=0;
								// cutting status sum
								$order_wise_today_cutting=0;
								$order_wise_total_cutting=0;
								$order_wise_cut_balance=0;
								// emblishment status sum
								$order_wise_today_send=0;
								$order_wise_total_send=0;
								$order_wise_today_rcv=0;
								$order_wise_total_rcv=0;
								$order_wise_embl_balance=0;
								// sewing status sum
								$order_wise_today_input=0;
								$order_wise_total_input=0;
								$order_wise_input_balance=0;
								$order_wise_inhand_qty=0;
								foreach($po_data as $po_id=>$color_data)
								{


									//foreach($item_data as $item_id=>$color_data)
									//{
										foreach($color_data as $color_id=>$row)
										{

										 	$today_lay_qnty 				=$lay_qnty_array[$job_id][$po_id][$color_id]["today_lay"];
											$today_cutting_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_cutting"];
											$today_sewing_input 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_sewing_input"];
											$today_emb_send_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_emb_send_qnty"];
											$today_emb_rcv_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_emb_rcv_qnty"];

											if($today_lay_qnty != "" || $today_cutting_qnty != "" || $today_sewing_input != "" || $today_emb_send_qnty != "" || $today_emb_rcv_qnty != "")
											{
											$color_wise_today_cutting		=0;
											$color_wise_total_cutting		=0;
											$color_wise_today_sewing_input 	=0;
											$color_wise_total_sewing_input	=0;
											$color_wise_inh					=0;

											$fin_req 						=0;
											$fin_req 						=$booking_no_fin_qnty_array[$po_id][$color_id]["qnty"];
											$issue_qty 						=$issue_qnty_arr[$po_id][$color_id];
											$req_issue_bal					=($fin_req-$issue_qty);
											$possible_cut_pcs 				=$issue_qty/$result_consumtion[$job_id];
										 	$total_lay_qnty 				=$lay_qnty_array[$job_id][$po_id][$color_id]["total_lay"];
										 	$order_qty 						= $order_qnty_array[$po_id][$color_id];
											$total_cutting_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_cutting"];
											$color_wise_today_cutting    	+= $today_cutting_qnty;
											$color_wise_total_cutting 		+= $total_cutting_qnty;


											$total_sewing_input 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_sewing_input"];

											$total_emb_send_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_emb_send_qnty"];

											$total_emb_rcv_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_emb_rcv_qnty"];

											$color_wise_today_sewing_input 	+= $today_sewing_input;
											$color_wise_total_sewing_input 	+= $total_sewing_input;

											// order wise
											$order_wise_color_total 		+= $order_qty;

											$order_wise_fab_req 			+= $fin_req;
											$order_wise_fin_fab_req 		+= $issue_qty;
											$order_wise_fab_issued_balance 	+= $req_issue_bal;
											$order_wise_fab_possible_qty 	+= $possible_cut_pcs;

											$order_wise_today_cutting 		+=$today_cutting_qnty;
											$order_wise_total_cutting 		+=$total_cutting_qnty;
											$order_wise_cut_balance 		+= ($order_qty-$total_cutting_qnty);

											$order_wise_today_send 			+= $today_emb_send_qnty;
											$order_wise_total_send 			+= $total_emb_send_qnty;
											$order_wise_today_rcv 			+= $today_emb_rcv_qnty;
											$order_wise_total_rcv 			+= $total_emb_rcv_qnty;
											$order_wise_embl_balance 		+= ($total_emb_send_qnty - $total_emb_rcv_qnty);

											$order_wise_today_lay 			+=$today_lay_qnty;
											$order_wise_total_lay 			+=$total_lay_qnty;
											$order_wise_lay_balance 		+= ($order_qty - $total_lay_qnty);

											$order_wise_today_input 		+= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_sewing_input"];
											$order_wise_total_input 		+= $total_sewing_input;
											$order_wise_input_balance 		+= ($order_qty - $total_sewing_input);
											$order_wise_inhand_qty 			+= ($total_cutting_qnty-$total_sewing_input);

											// grand total
											$grand_color_total 				+= $order_qty;

											$grand_fab_req 					+= $fin_req;
											$grand_fin_fab_req 				+= $issue_qty;
											$grand_fab_issued_balance 		+= $req_issue_bal;
											$grand_fab_possible_qty 		+= $possible_cut_pcs;

											$grand_today_cutting 			+=$today_cutting_qnty;
											$grand_total_cutting 			+=$total_cutting_qnty;
											$grand_cut_balance 				+= ($order_qty-$total_cutting_qnty);

											$grand_today_send 				+= $today_emb_send_qnty;
											$grand_total_send 				+= $total_emb_send_qnty;
											$grand_today_rcv 				+= $today_emb_rcv_qnty;
											$grand_total_rcv 				+= $total_emb_rcv_qnty;
											$grand_embl_balance 			+= ($total_emb_send_qnty - $total_emb_rcv_qnty);

											$grand_today_lay 				+=$today_lay_qnty;
											$grand_total_lay 				+=$total_lay_qnty;
											$grand_lay_balance 				+= ($order_qty - $order_wise_total_lay);

											$grand_today_input 				+= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_sewing_input"];
											$grand_total_input 				+= $total_sewing_input;
											$grand_input_balance 			+= ($order_qty - $total_sewing_input);
											$grand_inhand_qty 				+= ($total_cutting_qnty-$total_sewing_input);

											$serving_company = implode(",", $serving_company_check[$style_id][$job_id][$po_id][$color_id]);


												if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
				                            	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="20"><? echo $i;?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="150"><? echo $company_library[$row['working_company_id']]; ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="120"><? echo $location_library[$row['location_id']]; ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="150"><? echo $buyer_library[$row['buyer_name']]; ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $job_id; ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="100"><?php echo $row['internal_ref'];?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="110"><? echo $style_id; ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="110"><? echo $row['shiping_status']; ?></td>
					                            	<td align="center" style="word-wrap: break-word;word-break: break-all;" width="90"><? echo change_date_format($row['pub_shipment_date']); ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="110"><? echo $row['po_number'];?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="80"><? echo number_format($order_qty,0); ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="160"><? echo $color_library[$color_id]; ?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="90"><? echo number_format($fin_req,2);?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="90">
					                            		<a href="javascript:void(0)" onclick="openmypage_fab_issue(<? echo $po_id;?>,<? echo $color_id?>,<? echo 1 ?>, 'fab_issue_popup');">
					                            			<? echo number_format($issue_qty,2);?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="90"><? echo number_format($req_issue_bal,2);?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="90"><? echo number_format($possible_cut_pcs,2);?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',0,'A','production_qnty_popup','Today Lay','600','300');">
					                            			<? echo $today_lay_qnty;?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',0,'B','production_qnty_popup','Total Lay','730','300');">
					                            		<? echo $total_lay_qnty;?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $lay_balance = $order_qty-$total_lay_qnty;?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',1,'A','production_qnty_popup','Today Cutting','600','300');">
					                            			<? echo $today_cutting_qnty;?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',1,'B','production_qnty_popup','Total Cutting','730','300');">
					                            			<? echo $total_cutting_qnty;?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $cut_balance = $order_qty-$total_cutting_qnty;?></td>
					                            	<!-- ============= Emblishment ================== -->
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',2,'A','production_qnty_popup','Today Emblishment Issue','1000','300');"><? echo number_format($today_emb_send_qnty,0); ?>

					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',2,'B','production_qnty_popup','Total Emblishment Issue','730','300');"><? echo number_format($total_emb_send_qnty,0); ?>

					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',3,'A','production_qnty_popup','Today Emblishment Receive','1000','300');"><? echo number_format($today_emb_rcv_qnty,0) ;?>

					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',3,'B','production_qnty_popup','Total Emblishment Receive','730','300');"><? echo number_format($total_emb_rcv_qnty,0); ?>

					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100"><? echo number_format(($total_emb_send_qnty - $total_emb_rcv_qnty),0); ?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',4,'A','production_qnty_popup','Today Sewing Input','800','300');">
					                            			<? echo $today_input = $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_sewing_input"];?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',4,'B','production_qnty_popup','Total Sewing Input','730','300');">
					                            			<? echo $total_sewing_input;?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100"><? echo 	$input_balance = $order_qty-$total_sewing_input;?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $inh_qty= ($total_cutting_qnty-$total_sewing_input);?></td>
				                            	</tr>
									        <?
									        $i++;$jj++;
									    	}
									    }
									//}
								}
								?>
								<tr class="gd-color3">
					            	<td style="word-wrap: break-word;word-break: break-all;" width="960" colspan="10" align="right">Order Wise Sub Total:</td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><b><? echo number_format($order_wise_color_total,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="160">&nbsp;</td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($order_wise_fab_req,2); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($order_wise_fin_fab_req,2); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($order_wise_fab_issued_balance,2); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($order_wise_fab_possible_qty,2); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_today_lay,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_total_lay,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_lay_balance,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_today_cutting,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_total_cutting,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_cut_balance,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_today_send,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_total_send,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_today_rcv,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_total_rcv,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_embl_balance,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_today_input,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_total_input,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_input_balance,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_inhand_qty,0); ?></b></td>
					        	</tr>
								<?
							}
						}
						?>
						</table>
					</div>
						<table  border="1" class=""  width="3140" rules="all" id="" cellpadding="0" cellspacing="0">
							<tr class="gd-color2">
								<td style="word-wrap: break-word;word-break: break-all;" width="20">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="150">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="120">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="150">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="100">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="100">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="110">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="110"> &nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="90"> &nbsp;</td>
				            	<td style="word-wrap: break-word;word-break: break-all;" width="110" align="right">Grand Total:</td>
				            	<td id="grand_color_total" style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><b><? echo number_format($grand_color_total,0); ?></b></td>
				            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="160">&nbsp;</td>
				            	<td id="grand_fab_req" style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($grand_fab_req,2); ?></b></td>
				            	<td id="grand_fin_fab_req" style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($grand_fin_fab_req,2); ?></b></td>
				            	<td id="grand_fab_issued_balance" style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($grand_fab_issued_balance,2); ?></b></td>
				            	<td id="grand_fab_possible_qty" style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($grand_fab_possible_qty,2); ?></b></td>
				            	<td id="grand_today_lay" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_today_lay,0); ?></b></td>
				            	<td id="grand_total_lay" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_total_lay,0); ?></b></td>
				            	<td id="grand_lay_balance" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_lay_balance,0); ?></b></td>
				            	<td id="grand_today_cutting" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_today_cutting,0); ?></b></td>
				            	<td id="grand_total_cutting" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_total_cutting,0); ?></b></td>
				            	<td id="grand_cut_balance" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_cut_balance,0); ?></b></td>
				            	<td id="grand_today_send" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_today_send,0); ?></b></td>
				            	<td id="grand_total_send" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_total_send,0); ?></b></td>
				            	<td id="grand_today_rcv" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_today_rcv,0); ?></b></td>
				            	<td id="grand_total_rcv" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_total_rcv,0); ?></b></td>
				            	<td id="grand_embl_balance" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_embl_balance,0); ?></b></td>
				            	<td id="grand_today_input" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_today_input,0); ?></b></td>
				            	<td id="grand_total_input" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_total_input,0); ?></b></td>
				            	<td id="grand_input_balance" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_input_balance,0); ?></b></td>
				            	<td id="grand_inhand_qty" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_inhand_qty,0); ?></b></td>
				        	</tr>
						</table>
                </div>
                <br />
    		</div><!-- end main div -->
    	<?
    }
	else if($type==3)// Cutting Details
	{
		$company_cond="";
		$company_cond_lay="";
		$company_cond_lay_a="";
		$company_cond_delv="";
		$str_po_cond="";
		$str_po_cond2="";
		if($cbo_working_company!=0) $company_cond.=" and d.serving_company in($cbo_working_company)";
		if($cbo_working_company!=0) $company_cond_lay.=" and d.working_company_id in($cbo_working_company)";
		if($cbo_working_company!=0) $company_cond_lay_a.=" and a.working_company_id in($cbo_working_company)";
		if($cbo_working_company!=0) $company_cond_delv.=" and d.delivery_company_id in($cbo_working_company)";
		if ($txt_internal_ref_no=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping= '$txt_internal_ref_no'";
		if($cbo_buyer!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer";
		if($cbo_buyer!=0) $str_po_cond2.=" and a.buyer_name=$cbo_buyer";
		if($cbo_location) $str_po_cond.=" and d.location_id in($cbo_location)";
		if($cbo_location) $str_po_cond2.=" and d.location in($cbo_location)";
		if($order_no) $str_po_cond.=" and b.po_number in($txt_order_no)";

		if(str_replace("'", "", $txt_job_no)!= "") $job_cond=" and a.job_no=$txt_job_no";
		if(str_replace("'", "", $txt_job_no)!= "") $job_cond2=" and b.job_no_mst=$txt_job_no";
		$location_name=str_replace("'", "", $cbo_location_name);
		if($location_name) $location_cond.=" and a.location_id in($location_name)";
		if($location_name) $location_cond2.=" and a.location in($location_name)";

		if($hidden_order_id)
		{
			$str_po_cond.=" and b.id in($hidden_order_id)";
			$str_po_cond2.=" and b.id in($hidden_order_id)";
		}

        $party_library = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
        $str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);
		$str_com_cond_lay=str_replace("d.working_company_id", "a.working_company_id", $company_cond_lay);

		// =================================== GETTING TODAY CUTTING PO ID =================================
		$today_lay_sql="SELECT b.color_id,c.order_id as po_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
		where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and b.mst_id=c.mst_id  and a.entry_date=$txt_date and company_id in($company_name) $str_com_cond_lay $location_cond $job_cond and a.status_active in(1) and a.is_deleted=0 and b.status_active in(1) and b.is_deleted=0 and c.status_active in(1) and c.is_deleted=0 " ;
		// echo $today_lay_sql;
		$today_lay_res = sql_select($today_lay_sql);
		$po_id_arr = [];
		$color_id_arr = [];
		foreach ($today_lay_res as $key => $val) {
			if($val[csf('po_id')] != "")
			{
				$po_id_arr[$val[csf('po_id')]] = $val[csf('po_id')];
				$color_id_arr[$val[csf('color_id')]] = $val[csf('color_id')];
			}

		}
		// ================================ TODAY PRODUCTION PO ID ================================
		$str_com_cond_prod = str_replace("d.serving_company", "a.serving_company", $company_cond);
		$today_pro_sql="SELECT b.color_number_id,a.po_break_down_id as po_id from pro_garments_production_mst a,wo_po_color_size_breakdown b,pro_garments_production_dtls c
		where a.id=c.mst_id and c.color_size_break_down_id=b.id and  a.po_break_down_id=b.po_break_down_id and c.status_active=1 and a.production_date=$txt_date and a.company_id in($company_name) $str_com_cond_prod $location_cond2 $job_cond2 and b.status_active in(1) and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.production_type in(1,2,3,4) group by b.color_number_id,a.po_break_down_id" ; // $str_com_cond_prod
		// echo $today_pro_sql;
		$today_pro_res = sql_select($today_pro_sql);

		foreach ($today_pro_res as $key => $val) {
			if($val[csf('po_id')] != "")
			{
				$po_id_arr[$val[csf('po_id')]] = $val[csf('po_id')];
				$color_id_arr[$val[csf('color_id')]] = $val[csf('color_id')];
			}

		}
		$po_ids =trim(implode(',', $po_id_arr),",");
		$color_ids = trim(implode(',', $color_id_arr),",");
		// ========================= FOR PO ID ARRAY ==========================
		if(count($po_id_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_arr, 999);
	     	$po_ids_cond= "";
            $po_ids_cond1= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="")
	     		{
	     			$po_ids_cond.=" and ( d.po_break_down_id in ($imp_ids) ";
	     			$po_ids_cond1.=" and (po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or   d.po_break_down_id in ($imp_ids) ";
	     			$po_ids_cond1.=" or po_break_down_id in ($imp_ids) ";
	     		}
	     	}
	     	 $po_ids_cond.=" )";
	     	 $po_ids_cond1.=")";
	    }
	    else
	    {
	     	$po_ids_cond= " and d.po_break_down_id in($po_ids) ";
	     	$po_ids_cond1= " and po_break_down_id in($po_ids) ";
	    }

	     // ========================= FOR COLOR ID ARRAY ==========================

	    if(count($color_id_arr)>999 && $db_type==2)
	    {
	     	$color_chunk=array_chunk($color_id_arr, 999);
	     	$color_ids_cond= "";
	     	foreach($color_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($color_ids_cond=="")
	     		{
	     			$color_ids_cond.=" and ( e.color_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$color_ids_cond.=" or   e.color_id in ($imp_ids) ";
	     		}
	     	}
	     	 $color_ids_cond.=" )";
	     }
	     else
	     {
	     	$color_ids_cond= " and e.color_id in($color_ids) ";
	     }

	    // ================================== FOR CUT AND LAY ===================================
		$style_owner_cond = $style_owner ?  " and a.style_owner=$style_owner " : "";
	    $prod_po_id_lay = str_replace('d.po_break_down_id', 'f.order_id', $po_ids_cond);
		$lay_sql = "SELECT e.color_id as color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
			where a.job_no=b.job_no_mst  and b.id=c.po_break_down_id and d.job_no=a.job_no and b.id=f.order_id and d.id=e.mst_id  and a.job_no=c.job_no_mst and e.mst_id=f.mst_id and f.order_id=b.id and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1)  and b.is_deleted=0 and c.status_active in(1) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.company_id in($company_name)   $str_po_cond $internal_ref_cond    $prod_po_id_lay $style_owner_cond
			group by e.color_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping "; // $company_cond_lay
			// echo $lay_sql;//die();
			$production_main_array=[];
			foreach(sql_select($lay_sql) as $row)
			{
				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("working_company_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];
				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["internal_ref"]=$row[csf("grouping")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

				$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
				unset($prod_po_id_array[$row[csf("po_id")]]);

			}

			// echo "<pre>";
			// print_r($production_main_array);die();
			//=========================== FOR FINISH FAB QNTY ===========================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'a.po_break_down_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'b.color_number_id', $color_ids_cond);

			$booking_no_fin_qnty_array=array();
			$booking_sql=sql_select("SELECT a.po_break_down_id ,b.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown b  where b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $prod_po_id_lay $prod_color_id_lay ");
			foreach($booking_sql as $vals)
			{
				$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];
			}
            $finsish_con_arr = [];
            if($po_ids_cond1 != "") {
                $finish_cons = sql_select("select po_break_down_id, color_number_id, cons from wo_pre_cos_fab_co_avg_con_dtls where status_active = 1 and is_deleted = 0 $po_ids_cond1");
                $finsish_con_arr = [];
                foreach ($finish_cons as $consVal){
                    $finsish_con_arr[$consVal[csf("po_break_down_id")]][$consVal[csf("color_number_id")]] = $consVal[csf("cons")];
                }
            }
			// ==================================== FOR ISSUE ====================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'po_breakdown_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'color_id', $color_ids_cond);

			$issue_sql=sql_select("SELECT po_breakdown_id,color_id,trans_id, sum(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=18 $prod_po_id_lay $prod_color_id_lay group by po_breakdown_id,color_id,trans_id");
			foreach($issue_sql as $values)
			{
			 	$issue_qnty_arr[$values[csf("po_breakdown_id")]][$values[csf("color_id")]]+=$values[csf("qnty")];
			}
			//======================================= FOR CONSUMPTION ===========================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'b.po_break_down_id', $po_ids_cond);
			$result_consumtion=array();
			$sql_consumtiont_qty=sql_select("SELECT a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id $prod_po_id_lay GROUP BY a.job_no, a.body_part_id");

			foreach($sql_consumtiont_qty as $row_consum)
			{
				$result_consumtion[$row_consum[csf('job_no')]]+=($row_consum[csf("requirment")]/$row_consum[csf("pcs")]);
			}

			// ========================================FOR LAY QUANTITY=====================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'c.order_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'b.color_id', $color_ids_cond);

			$lay_sqls="SELECT  a.job_no,c.order_id, b.gmt_item_id,b.color_id,sum( case when a.entry_date=$txt_date then  c.size_qty else 0 end) as today_lay,sum(CASE WHEN a.entry_date <= $txt_date THEN c.size_qty ELSE 0 END) as total_lay from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and b.mst_id=c.mst_id and a.entry_form=99 and company_id in($company_name) $str_com_cond_lay $location_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $prod_po_id_lay  group by a.job_no,c.order_id, b.gmt_item_id,b.color_id ";

			$lay_qnty_array=array();
			foreach(sql_select($lay_sqls) as $vals)
			{
				$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("color_id")]]["today_lay"]+=$vals[csf("today_lay")];
				$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("total_lay")];
			}

			// =================================== FOR ORDER QUANTITY ==================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'po_break_down_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'color_number_id', $color_ids_cond);
			$order_qnty_array=array();
			$plan_qnty_array=array();
			$order_qnty_sqls="SELECT po_break_down_id,color_number_id,order_quantity,item_number_id,excess_cut_perc,plan_cut_qnty from wo_po_color_size_breakdown where status_active in(1) and is_deleted=0 $prod_po_id_lay ";
			 foreach(sql_select($order_qnty_sqls) as $values)
			 {
			 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("color_number_id")]]+=$values[csf("order_quantity")];
				$plan_qnty_array[$values[csf("po_break_down_id")]][$values[csf("color_number_id")]]["excess_cut_perc"]=$values[csf("excess_cut_perc")];
				$plan_qnty_array[$values[csf("po_break_down_id")]][$values[csf("color_number_id")]]["plan_cut_qnty"]+=$values[csf("plan_cut_qnty")];
			 }
			 // =================================== FOR PRODUCTION DATA ==================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'b.id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'c.color_number_id', $color_ids_cond);
			$location_cond_prod = str_replace('a.location', 'd.location', $location_cond2);

			$order_sql="SELECT d.serving_company,d.location,d.production_source, d.production_type, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no  ,   c.color_number_id, sum(c.order_quantity) as order_quantity,

			sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_date then e.production_qnty else 0 end ) as today_cutting ,
			sum(case when d.production_type=1 and e.production_type=1 and d.production_date<=$txt_date then e.production_qnty else 0 end ) as total_cutting ,
			sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_date then e.production_qnty else 0 end ) as today_sewing_input ,
			sum(case when d.production_type=4 and e.production_type=4 and d.production_date<=$txt_date then e.production_qnty else 0 end ) as total_sewing_input ,
			sum(CASE WHEN d.production_type =2 and embel_name in(1,2) and d.production_date=$txt_date THEN e.production_qnty ELSE 0 END) AS today_emb_send_qnty,
			sum(CASE WHEN d.production_type =2 and embel_name in(1,2) and d.production_date<=$txt_date THEN e.production_qnty ELSE 0 END) AS total_emb_send_qnty,
			sum(CASE WHEN d.production_type =3 and embel_name in(1,2) and d.production_date=$txt_date THEN e.production_qnty ELSE 0 END) AS today_emb_rcv_qnty,
			sum(CASE WHEN d.production_type =3 and embel_name in(1,2) and d.production_date<=$txt_date THEN e.production_qnty ELSE 0 END) AS total_emb_rcv_qnty,
			sum(CASE WHEN d.production_type =3 and embel_name in(1,2) and d.production_date<=$txt_date THEN e.reject_qty ELSE 0 END) AS total_reject_qty
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
			where  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $po_ids_cond  $company_cond $location_cond_prod and d.production_type in(1,2,3,4)
			group by d.serving_company,d.location,d.production_source,d.production_type,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number, c.item_number_id, c.color_number_id,e.cut_no ";

            //echo $order_sql; //die;

			$serving_company_check = []; $serving_company_check_inhouse = []; $serving_company_check_outbound = [];
			$order_sql_res = sql_select($order_sql);
			foreach($order_sql_res as $vals)
			{
				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_cutting"]+=$vals[csf("today_cutting")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_cutting"]+=$vals[csf("total_cutting")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_emb_send_qnty"]+=$vals[csf("today_emb_send_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_emb_send_qnty"]+=$vals[csf("total_emb_send_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_emb_rcv_qnty"]+=$vals[csf("today_emb_rcv_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_emb_rcv_qnty"]+=$vals[csf("total_emb_rcv_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_reject_qty"]+=$vals[csf("total_reject_qty")];



				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["order_quantity"]+=$vals[csf("order_quantity")];
				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["production_source"]=$vals[csf("production_source")];

				$serving_company_check[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]][$vals[csf("serving_company")]]=$vals[csf("serving_company")];
				if($vals[csf("production_type")] == 2) {
                    if ($vals[csf("production_source")] == 1) {
                        $serving_company_check_inhouse[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]][$vals[csf("serving_company")]] = $vals[csf("serving_company")];
                    } else {
                        $serving_company_check_outbound[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]][$vals[csf("serving_company")]] = $vals[csf("serving_company")];
                    }
                }
			}

			// echo "<pre>";
			// print_r($serving_company_check);die;


			if(count($production_main_array)==0)
			{
				echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px;text-align:center">No Data Found.</div>'; die;
			}

			// echo "<pre>";
			// print_r($production_main_array);//die;

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
	            hr {
	                border: 0;
	                background-color: #000;
	                height: 1px;
	            }
	            .gd-color
	            {
					background: #f0f9ff; /* Old browsers */
					background: -moz-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f9ff', endColorstr='#a1dbff',GradientType=0 ); /* IE6-9 */
				}
				.gd-color2
				{
					background: rgb(247,251,252); /* Old browsers */
					background: -moz-linear-gradient(top, rgba(247,251,252,1) 0%, rgba(217,237,242,1) 40%, rgba(173,217,228,1) 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7fbfc', endColorstr='#add9e4',GradientType=0 ); /* IE6-9 */
					font-weight: bold;
				}
				.gd-color2 td
				{
					border: 1px solid #777;
					text-align: right;
				}
				.gd-color3
				{
					background: rgb(254,255,255); /* Old browsers */
					background: -moz-linear-gradient(top, rgba(254,255,255,1) 0%, rgba(221,241,249,1) 35%, rgba(160,216,239,1) 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#feffff', endColorstr='#a0d8ef',GradientType=0 ); /* IE6-9 */
					border: 1px solid #dccdcd;
					font-weight: bold;
				}

	        </style>
                <div style="width:3150px; margin: 0 auto">
                    <table width="400" cellspacing="0" style="margin:0 auto;">
					<tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td align="right" style="border:none; font-size:14px;font-weight: bold;">
                                Company Name
                            </td>
                            <td align="center" style="border:none; font-size:14px;font-weight: bold;">
                                :
                            </td>
                            <td align="left" style="border:none; font-size:14px;font-weight: bold;">
                                <?=$company_library[$vals['COMPANY_NAME']]; ?>
                            </td>
                        </tr>
                        <tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td align="right" style="border:none; font-size:14px; font-weight: bold;">
                                Working Company Name
                            </td>
                            <td align="center" style="border:none; font-size:14px;font-weight: bold;">
                                 :
                            </td>
                            <td align="left" style="border:none; font-size:14px;font-weight: bold;">
                                <?
	        					$comp_names="";
	        					foreach(explode(",",$cbo_working_company) as $vals)
	        					{
	        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
	        					}
	        					echo $comp_names;
	        					 ?>
                            </td>
                        </tr>
                        <tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td align="right" style="border:none; font-size:14px;font-weight: bold;">
                                Date
                            </td>
                            <td align="center" style="border:none; font-size:14px;font-weight: bold;">
                                :
                            </td>
                            <td align="left" style="border:none; font-size:14px;font-weight: bold;">
                                <? echo change_date_format(str_replace("'", "", $txt_date)); ?>
                            </td>
                        </tr>
                    </table>
                    <table width="3600" cellspacing="0" border="3" class="rpt_table" rules="all" id="table_header_1">
                        <thead>
                            <tr>
                                <th rowspan="2" width="20">Sl.</th>
                                <th rowspan="2" width="150">Working Company</th>
                                <th rowspan="2" width="120">Location</th>
                                <th rowspan="2" width="150">Buyer</th>
                                <th rowspan="2" width="100">Job No.</th>
                                <th rowspan="2" width="100">Internal Ref.</th>
                                <th rowspan="2" width="110">Style ref.</th>
                                <th rowspan="2" width="110">Ship Status</th>
                                <th rowspan="2" width="90">Ship Date</th>
                                <th rowspan="2" width="130">Order No.</th>
                             	<th rowspan="2" width="160">Color Name</th>
                                <th rowspan="2" width="100">Color Qnty</th>

                                <th colspan="6">Fabric Status</th>
                                <th colspan="3">Lay Status</th>
                                <th colspan="3">Cutting Status</th>
                                <th colspan="7">Embellishment Status</th>
                                <th colspan="4">Input Status</th>
                             </tr>
                             <tr>
								<th width="100">Plan Cut (%)</th>
								<th width="100">Plan Cut Qty</th>
                             	<th width="90">F.Fab Req.</th>
								<th width="90">F.Fab Cons.</th>
                             	<th width="90">Fin. Fab Issue</th>
                             	<th width="90">F.Issued Bal.</th>


                             	<th width="100">Today</th>
                             	<th width="100">Total</th>
                             	<th width="100">Lay.Balance</th>

                             	<th width="100">Today</th>
                             	<th width="100">Total</th>
                             	<th width="100">Cut.Balance</th>

                             	<th width="100">Today Send</th>
                             	<th width="100">Total Send</th>
                             	<th width="100">Today Rcv</th>
                             	<th width="100">Total Rcv</th>
								<th width="100">Reject qty</th>
                             	<th width="100">Print B/L</th>
								<th width="100">EmB Company Name </th>

                             	<th width="100">Today</th>
                             	<th width="100">Total</th>
                             	<th width="100">Order-Input Balance</th>
                             	<th width="100">Inhand Qty</th>
                             </tr>
                        </thead>
                    </table>
                    <div style="max-height:425px; overflow-y:scroll; width:3620px" id="scroll_body">
                        <table border="3" cellpadding="0" cellspacing="0" class="rpt_table"  width="3600" rules="all" id="table_filter_" >
                        <?
                        $i=1;
						$jj=1;
						$grand_color_total = 0;
						// fabric status sum
						$grand_fab_req = 0;
						$grand_fin_fab_req = 0;
						$grand_fab_issued_balance = 0;
						$grand_fab_possible_qty = 0;
						// lay status sum
						$grand_today_lay=0;
						$grand_total_lay=0;
						$grand_lay_balance=0;
						// cutting status sum
						$grand_today_cutting=0;
						$grand_total_cutting=0;
						$grand_cut_balance=0;
						// emblishment status sum
						$grand_today_send=0;
						$grand_total_send=0;
						$grand_today_rcv=0;
						$grand_total_rcv=0;
						$grand_reject_qty=0;

						$grand_embl_balance=0;


						// sewing status sum
						$grand_today_input=0;
						$grand_total_input=0;
						$grand_input_balance=0;
						$grand_inhand_qty=0;
						foreach($production_main_array as $style_id=>$job_data)
						{
							foreach($job_data as $job_id=>$po_data)
							{
								$order_wise_color_total = 0;
								// fabric status sum
								$order_wise_fab_req = 0;
								$order_wise_fin_fab_req = 0;
								$order_wise_fab_issued_balance = 0;
								$order_wise_fab_possible_qty = 0;
								// lay status sum
								$order_wise_today_lay=0;
								$order_wise_total_lay=0;
								$order_wise_lay_balance=0;
								// cutting status sum
								$order_wise_today_cutting=0;
								$order_wise_total_cutting=0;
								$order_wise_cut_balance=0;
								// emblishment status sum
								$order_wise_today_send=0;
								$order_wise_total_send=0;
								$order_wise_today_rcv=0;
								$order_wise_total_rcv=0;
								$order_wise_reject_qty=0;
								$order_wise_embl_balance=0;

								// sewing status sum
								$order_wise_today_input=0;
								$order_wise_total_input=0;
								$order_wise_input_balance=0;
								$order_wise_inhand_qty=0;
								foreach($po_data as $po_id=>$color_data)
								{


									//foreach($item_data as $item_id=>$color_data)
									//{
										foreach($color_data as $color_id=>$row)
										{

										 	$today_lay_qnty 				=$lay_qnty_array[$job_id][$po_id][$color_id]["today_lay"];
											$today_cutting_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_cutting"];
											$today_sewing_input 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_sewing_input"];
											$today_emb_send_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_emb_send_qnty"];
											$today_emb_rcv_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_emb_rcv_qnty"];




											if($today_lay_qnty != "" || $today_cutting_qnty != "" || $today_sewing_input != "" || $today_emb_send_qnty != "" || $today_emb_rcv_qnty != "")
											{
											$color_wise_today_cutting		=0;
											$color_wise_total_cutting		=0;
											$color_wise_today_sewing_input 	=0;
											$color_wise_total_sewing_input	=0;
											$color_wise_inh					=0;

											$fin_req 						=0;
											$fin_req 						=$booking_no_fin_qnty_array[$po_id][$color_id]["qnty"];
											$issue_qty 						=$issue_qnty_arr[$po_id][$color_id];
											$req_issue_bal					=($fin_req-$issue_qty);
											$possible_cut_pcs 				=$issue_qty/$result_consumtion[$job_id];
										 	$total_lay_qnty 				=$lay_qnty_array[$job_id][$po_id][$color_id]["total_lay"];
										 	$order_qty 						= $order_qnty_array[$po_id][$color_id];
                                            $excess_cut_percent             = $plan_qnty_array[$po_id][$color_id]['excess_cut_perc'];
										 	$plan_cut_qnty					= $plan_qnty_array[$po_id][$color_id]['plan_cut_qnty'];
                                            $finish_cons                    = $finsish_con_arr[$po_id][$color_id];
											$total_cutting_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_cutting"];
											$color_wise_today_cutting    	+= $today_cutting_qnty;
											$color_wise_total_cutting 		+= $total_cutting_qnty;


											$total_sewing_input 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_sewing_input"];

											$total_emb_send_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_emb_send_qnty"];

											$total_emb_rcv_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_emb_rcv_qnty"];

											$total_reject_qty			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_reject_qty"];


											$production_source			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["production_source"];







											$color_wise_today_sewing_input 	+= $today_sewing_input;
											$color_wise_total_sewing_input 	+= $total_sewing_input;

											// order wise
											$order_wise_color_total 		+= $order_qty;

											$order_wise_fab_req 			+= $fin_req;
											$order_wise_fin_fab_req 		+= $issue_qty;
											$order_wise_fab_issued_balance 	+= $req_issue_bal;
											$order_wise_fab_possible_qty 	+= $possible_cut_pcs;

											$order_wise_today_cutting 		+=$today_cutting_qnty;
											$order_wise_total_cutting 		+=$total_cutting_qnty;
											$order_wise_cut_balance 		+= ($order_qty-$total_cutting_qnty);

											$order_wise_today_send 			+= $today_emb_send_qnty;
											$order_wise_total_send 			+= $total_emb_send_qnty;
											$order_wise_today_rcv 			+= $today_emb_rcv_qnty;
											$order_wise_total_rcv 			+= $total_emb_rcv_qnty;

											$order_wise_embl_balance 		+= ($total_emb_send_qnty - $total_emb_rcv_qnty);
											$order_wise_reject_qty          +=  $total_reject_qty;

											$order_wise_today_lay 			+=$today_lay_qnty;
											$order_wise_total_lay 			+=$total_lay_qnty;
											$order_wise_lay_balance 		+= ($order_qty - $total_lay_qnty);

											$order_wise_today_input 		+= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_sewing_input"];
											$order_wise_total_input 		+= $total_sewing_input;
											$order_wise_input_balance 		+= ($order_qty - $total_sewing_input);
											$order_wise_inhand_qty 			+= ($total_cutting_qnty-$total_sewing_input);

											// grand total
											$grand_color_total 				+= $order_qty;
											$grand_plan_cut_qnty            += $plan_cut_qnty;

											$grand_fab_req 					+= $fin_req;
											$grand_fin_fab_req 				+= $issue_qty;
											$grand_fab_issued_balance 		+= $req_issue_bal;
											$grand_fab_possible_qty 		+= $possible_cut_pcs;

											$grand_today_cutting 			+=$today_cutting_qnty;
											$grand_total_cutting 			+=$total_cutting_qnty;
											$grand_cut_balance 				+= ($order_qty-$total_cutting_qnty);

											$grand_today_send 				+= $today_emb_send_qnty;
											$grand_total_send 				+= $total_emb_send_qnty;
											$grand_today_rcv 				+= $today_emb_rcv_qnty;
											$grand_total_rcv 				+= $total_emb_rcv_qnty;
											$grand_reject_qty               += $total_reject_qty;

											$grand_embl_balance 			+= ($total_emb_send_qnty - $total_emb_rcv_qnty);



											$grand_today_lay 				+=$today_lay_qnty;
											$grand_total_lay 				+=$total_lay_qnty;
											$grand_lay_balance 				+= ($order_qty - $order_wise_total_lay);

											$grand_today_input 				+= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_sewing_input"];
											$grand_total_input 				+= $total_sewing_input;
											$grand_input_balance 			+= ($order_qty - $total_sewing_input);
											$grand_inhand_qty 				+= ($total_cutting_qnty-$total_sewing_input);

											$serving_company = implode(",", $serving_company_check[$style_id][$job_id][$po_id][$color_id]);
											// echo $serving_company.$production_source;


												if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
				                            	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
					                            	<td valign="middle" width="20" align="center"><? echo $i;?></td>
					                            	<td valign="middle" width="150"><? echo $company_library[$row['working_company_id']]; ?></td>
					                            	<td valign="middle" width="120"><? echo $location_library[$row['location_id']]; ?></td>
					                            	<td valign="middle" width="150"><? echo $buyer_library[$row['buyer_name']]; ?></td>
					                            	<td valign="middle" width="100"><? echo $job_id; ?></td>
					                            	<td valign="middle" width="100"><?php echo $row['internal_ref'];?></td>
					                            	<td valign="middle" width="110"><? echo $style_id; ?></td>
					                            	<td valign="middle" width="110"><? echo $row['shiping_status']; ?></td>
					                            	<td valign="middle" align="center" width="90"><? echo change_date_format($row['pub_shipment_date']); ?></td>
					                            	<td valign="middle" width="130" title="<?=$po_id."--".$color_id?>"><? echo $row['po_number'];?></td>
					                            	<td valign="middle" width="160"><? echo $color_library[$color_id]; ?></td>
					                            	<td valign="middle" align="right" width="100"><? echo number_format($order_qty,0); ?></td>

													<td valign="middle" align="center" width="100"><? echo $excess_cut_percent; ?></td>
													<td valign="middle"  align="right" width="100"><?  echo number_format($plan_cut_qnty); ?></td>
					                            	<td valign="middle" align="right" width="90"><? echo number_format($fin_req,2);?></td>
                                                    <td valign="middle" align="center" width="90"><?=$finish_cons?></td>
					                            	<td valign="middle" align="right" width="90">
					                            		<a href="javascript:void(0)" onclick="openmypage_fab_issue(<? echo $po_id;?>,<? echo $color_id?>,<? echo 1 ?>, 'fab_issue_popup');">
					                            			<? echo number_format($issue_qty,2);?>
					                            		</a>
					                            	</td>
					                            	<td valign="middle" align="right" width="90"><? echo number_format($req_issue_bal,2);?></td>
					                            	<td valign="middle" align="right" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',0,'A','production_qnty_popup','Today Lay','600','300');">
					                            			<? echo $today_lay_qnty;?>
					                            		</a>
					                            	</td>
					                            	<td valign="middle" align="right" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',0,'B','production_qnty_popup','Total Lay','730','300');">
					                            		<? echo $total_lay_qnty;?>
					                            		</a>
					                            	</td>
					                            	<td valign="middle" align="right" width="100"><? echo $lay_balance = $order_qty-$total_lay_qnty;?></td>


					                            	<td valign="middle" align="right" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',1,'A','production_qnty_popup','Today Cutting','600','300');">
					                            			<? echo $today_cutting_qnty;?>
					                            		</a>
					                            	</td>
					                            	<td valign="middle" align="right" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',1,'B','production_qnty_popup','Total Cutting','730','300');">
					                            			<? echo $total_cutting_qnty;?>
					                            		</a>
					                            	</td>
					                            	<td valign="middle" align="right" width="100"><? echo $cut_balance = $order_qty-$total_cutting_qnty;?></td>
					                            	<!-- ============= Emblishment ================== -->
					                            	<td valign="middle" align="right" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',2,'A','production_qnty_popup','Today Emblishment Issue','1000','300');"><? echo number_format($today_emb_send_qnty,0); ?>

					                            		</a>
					                            	</td>
					                            	<td valign="middle" align="right" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',2,'B','production_qnty_popup','Total Emblishment Issue','730','300');"><? echo number_format($total_emb_send_qnty,0); ?>

					                            		</a>
					                            	</td>
					                            	<td valign="middle" align="right" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',3,'A','production_qnty_popup','Today Emblishment Receive','1000','300');"><? echo number_format($today_emb_rcv_qnty,0) ;?>

					                            		</a>
					                            	</td>
					                            	<td valign="middle" align="right" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',3,'B','production_qnty_popup','Total Emblishment Receive','730','300');"><? echo number_format($total_emb_rcv_qnty,0); ?>

					                            		</a>
					                            	</td>
													<td valign="middle" align="right" width="100"><? echo $total_reject_qty ?></td>
					                            	<td valign="middle" align="right" width="100"><? echo number_format(($total_emb_send_qnty - $total_emb_rcv_qnty),0); ?></td>

													<td valign="middle" width="100"><?
                                                        if($production_source==1){
                                                            $serving_company_arr = array_values($serving_company_check_inhouse[$style_id][$job_id][$po_id][$color_id]);
                                                            $company_str = "";
                                                            foreach (array_unique($serving_company_arr) as $key => $val){
                                                                if($key == 0){
                                                                    $company_str .= $company_library[$val];
                                                                }else{
                                                                    $company_str .= ", ".$company_library[$val];
                                                                }
                                                            }
                                                            echo $company_str;
                                                        }else{
                                                            $serving_company_arr = array_values($serving_company_check_outbound[$style_id][$job_id][$po_id][$color_id]);
                                                            $company_str1 = "";
                                                            foreach (array_unique($serving_company_arr) as $key => $val){
                                                                if($key == 0){
                                                                    $company_str1 .= $party_library[$val];
                                                                }else{
                                                                    $company_str1 .= ", ".$party_library[$val];
                                                                }
                                                            }
                                                            echo $company_str1;
                                                        }
                                                        ?>
                                                    </td>
					                            	<td valign="middle" align="right" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',4,'A','production_qnty_popup','Today Sewing Input','800','300');">
					                            			<? echo $today_input = $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_sewing_input"];?>
					                            		</a>
					                            	</td>
					                            	<td valign="middle" align="right" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',4,'B','production_qnty_popup','Total Sewing Input','730','300');">
					                            			<? echo $total_sewing_input;?>
					                            		</a>
					                            	</td>
					                            	<td valign="middle" align="right" width="100"><? echo 	$input_balance = $order_qty-$total_sewing_input;?></td>
					                            	<td valign="middle" align="right" width="100"><? echo $inh_qty= ($total_cutting_qnty-$total_sewing_input);?></td>
				                            	</tr>
									        <?
									        $i++;$jj++;
									    	}
									    }
									//}
								}
								?>
								<tr class="gd-color3">
					            	<td colspan="11" align="right">Order Wise Sub Total:</td>
					            	<td align="right" width="100"><b><? echo number_format($order_wise_color_total,0); ?></b></td>

									<td align="right" width="100"><b></b></td>
									<td align="right" width="100"><b></b></td>
					            	<td align="right" width="90"><b><? echo number_format($order_wise_fab_req,2); ?></b></td>
                                    <td align="right" width="90"><b></b></td>
					            	<td align="right" width="90"><b><? echo number_format($order_wise_fin_fab_req,2); ?></b></td>
					            	<td align="right" width="90"><b><? echo number_format($order_wise_fab_issued_balance,2); ?></b></td>
					            	<td align="right" width="100"><b><? echo number_format($order_wise_today_lay,0); ?></b></td>
					            	<td align="right" width="100"><b><? echo number_format($order_wise_total_lay,0); ?></b></td>
					            	<td align="right" width="100"><b><? echo number_format($order_wise_lay_balance,0); ?></b></td>

					            	<td align="right" width="100"><b><? echo number_format($order_wise_today_cutting,0); ?></b></td>
					            	<td align="right" width="100"><b><? echo number_format($order_wise_total_cutting,0); ?></b></td>
					            	<td align="right" width="100"><b><? echo number_format($order_wise_cut_balance,0); ?></b></td>

					            	<td align="right" width="100"><b><? echo number_format($order_wise_today_send,0); ?></b></td>
					            	<td align="right" width="100"><b><? echo number_format($order_wise_total_send,0); ?></b></td>
					            	<td align="right" width="100"><b><? echo number_format($order_wise_today_rcv,0); ?></b></td>
					            	<td align="right" width="100"><b><? echo number_format($order_wise_total_rcv,0); ?></b></td>
									<td align="right" width="100"><b><? echo number_format($order_wise_reject_qty,0)?></b></td>

					            	<td align="right" width="100"><b><? echo number_format($order_wise_embl_balance,0); ?></b></td>

									<td align="right" width="100"><b></b></td>

					            	<td align="right" width="100"><b><? echo number_format($order_wise_today_input,0); ?></b></td>
					            	<td align="right" width="100"><b><? echo number_format($order_wise_total_input,0); ?></b></td>
					            	<td align="right" width="100"><b><? echo number_format($order_wise_input_balance,0); ?></b></td>
					            	<td align="right" width="100"><b><? echo number_format($order_wise_inhand_qty,0); ?></b></td>
					        	</tr>
								<?
							}
						}
						?>
						</table>
					</div>
						<table  border="3" class=""  width="3600" rules="all" id="" cellpadding="0" cellspacing="0">
							<tr class="gd-color3">
				            	<td colspan="11" align="right">Grand Total:</td>
				            	<td id="grand_color_total" align="right" width="100"><b><? echo number_format($grand_color_total,0); ?></b></td>
				            	<!-- <td align="right" width="160">&nbsp;</td>  -->
								<td align="right" width="100"><b></b></td>
								<td align="right" width="100"><b><?  echo number_format($grand_plan_cut_qnty);  ?></b></td>


				            	<td id="grand_fab_req" align="right" width="90"><b><? echo number_format($grand_fab_req,2); ?></b></td>
                                <td id="grand_fab_possible_qty" align="right" width="90"><b></b></td>
                                <td id="grand_fin_fab_req" align="right" width="90"><b><? echo number_format($grand_fin_fab_req,2); ?></b></td>
				            	<td id="grand_fab_issued_balance" align="right" width="90"><b><? echo number_format($grand_fab_issued_balance,2); ?></b></td>
				            	<td id="grand_today_lay" align="right" width="100"><b><? echo number_format($grand_today_lay,0); ?></b></td>
				            	<td id="grand_total_lay" align="right" width="100"><b><? echo number_format($grand_total_lay,0); ?></b></td>
				            	<td id="grand_lay_balance" align="right" width="100"><b><? echo number_format($grand_lay_balance,0); ?></b></td>
				            	<td id="grand_today_cutting" align="right" width="100"><b><? echo number_format($grand_today_cutting,0); ?></b></td>
				            	<td id="grand_total_cutting" align="right" width="100"><b><? echo number_format($grand_total_cutting,0); ?></b></td>
				            	<td id="grand_cut_balance" align="right" width="100"><b><? echo number_format($grand_cut_balance,0); ?></b></td>
				            	<td id="grand_today_send" align="right" width="100"><b><? echo number_format($grand_today_send,0); ?></b></td>
				            	<td id="grand_total_send" align="right" width="100"><b><? echo number_format($grand_total_send,0); ?></b></td>
				            	<td id="grand_today_rcv" align="right" width="100"><b><? echo number_format($grand_today_rcv,0); ?></b></td>
				            	<td id="grand_total_rcv" align="right" width="100"><b><? echo number_format($grand_total_rcv,0); ?></b></td>
								<td align="right" width="100"><b><? echo number_format($grand_reject_qty,0)?></b></td>

				            	<td id="grand_embl_balance" align="right" width="100"><b><? echo number_format($grand_embl_balance,0); ?></b></td>

								<td align="right" width="100"><b></b></td>
				            	<td id="grand_today_input" align="right" width="100"><b><? echo number_format($grand_today_input,0); ?></b></td>
				            	<td id="grand_total_input" align="right" width="100"><b><? echo number_format($grand_total_input,0); ?></b></td>
				            	<td id="grand_input_balance" align="right" width="100"><b><? echo number_format($grand_input_balance,0); ?></b></td>
				            	<td id="grand_inhand_qty" align="right" width="100"><b><? echo number_format($grand_inhand_qty,0); ?></b></td>
				        	</tr>
						</table>
                </div>
                <br />
    		</div>
    	<?
    }
	else if($type==4)// Cutting wise
	{
		$company_cond="";
		$str_po_cond="";
		$job_no=str_replace("'","",$txt_job_no);
		$order_no = str_replace("'","",$txt_order_no); 
		
		if($cbo_working_company!=0) $company_cond.=" and d.serving_company in($cbo_working_company)";
		if($cbo_location) $str_po_cond.=" and d.location in($cbo_location)";
		if($cbo_buyer!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer";
		if ($txt_internal_ref_no=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping= '$txt_internal_ref_no'";
		$str_po_cond.=($job_no!="") ? " AND a.job_no LIKE '%$job_no'" : "";
		if ($txt_ref_no !='') $str_po_cond.="and a.style_ref_no= '$txt_ref_no'";
	    if ($hidden_job_id !='') $str_po_cond.="and a.job_no = '$hidden_job_id'";
		if ($order_no !='') $str_po_cond.="and b.po_number LIKE '%$order_no'";
		if (str_replace("'","",$txt_date) !='') $date_cond="and d.production_date=$txt_date";

		$style_owner_cond = $style_owner ?  " and a.style_owner=$style_owner " : "";

		$cutting_sql = "SELECT a.buyer_name,a.job_no,a.style_ref_no,a.style_description,a.season_buyer_wise,c.item_number_id,b.id as po_id ,b.po_number,c.color_number_id,c.size_number_id ,c.order_quantity,c.id as col_size_id,e.production_qnty as production_quantity,d.production_date FROM wo_po_details_master a,wo_po_break_down b , wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e where a.id = b.job_id AND b.id=c.po_break_down_id AND b.id = d.po_break_down_id AND d.id=e.mst_id  AND d.production_type=1 AND c.id=e.color_size_break_down_id  AND a.company_name IN ($company_name) $str_po_cond  $company_cond $txt_internal_ref_no $date_cond $style_owner_cond order by c.id";
		// echo $cutting_sql; die;


		$res = sql_select($cutting_sql);
		$data_array = array();
		$data_array2 = array();
		$size_array = array();
		$order_qty_array = array();
		$col_size_chk_array = array();

		foreach ($res as $v)
		{
			// new group
			$data_array[$v['JOB_NO']][$v['COLOR_NUMBER_ID']] ['PO_NUMBER'] [$v['PO_NUMBER']] = $v['PO_NUMBER'];
			$data_array[$v['JOB_NO']][$v['COLOR_NUMBER_ID']] ['ITEM_NUMBER_ID'] [$v['ITEM_NUMBER_ID']] = $v['ITEM_NUMBER_ID'];
			$data_array[$v['JOB_NO']][$v['COLOR_NUMBER_ID']] ['BUYER_NAME'] = $v['BUYER_NAME'];
			$data_array[$v['JOB_NO']][$v['COLOR_NUMBER_ID']] ['STYLE_REF_NO'] = $v['STYLE_REF_NO'];
			$data_array[$v['JOB_NO']][$v['COLOR_NUMBER_ID']] ['STYLE_DESCRIPTION'] = $v['STYLE_DESCRIPTION'];
			$data_array[$v['JOB_NO']][$v['COLOR_NUMBER_ID']] ['SEASON_BUYER_WISE'] = $v['SEASON_BUYER_WISE'];
			$data_array[$v['JOB_NO']][$v['COLOR_NUMBER_ID']] ['SIZE_NUMBER_ID'] [$v['SIZE_NUMBER_ID']]  = $v['SIZE_NUMBER_ID'];
			if($col_size_chk_array[$v['COL_SIZE_ID']]=="")
			{
				$data_array[$v['JOB_NO']][$v['COLOR_NUMBER_ID']]['ORDER_QUANTITY'] [$v['SIZE_NUMBER_ID']] += $v['ORDER_QUANTITY'];
				$col_size_chk_array[$v['COL_SIZE_ID']] = $v['COL_SIZE_ID'];
			}

			$data_array[$v['JOB_NO']][$v['COLOR_NUMBER_ID']]['PRODUCTION_QUANTITY'][$v['SIZE_NUMBER_ID']]   += $v['PRODUCTION_QUANTITY'];
			// $data_array[$v['JOB_NO']][$v['COLOR_NUMBER_ID']] ['VARIANCE'] [$v['SIZE_NUMBER_ID']]   = $v['PRODUCTION_QUANTITY'] -  $v['ORDER_QUANTITY'];

			$size = count($data_array[$v['JOB_NO']][$v['COLOR_NUMBER_ID']]['SIZE_NUMBER_ID']);
			if($size>$arr_size)
			{
				$arr_size = $size ;
			}
		}
		// echo '<pre>';
		// print_r($data_array);
		// die;
		$production_data = $v['PRODUCTION_DATE'];
		$table_width =1040+ ($arr_size * 50)."px" ;
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
	            hr {
	                border: 0;
	                background-color: #000;
	                height: 1px;
	            }
	            .gd-color
	            {
					background: #f0f9ff; /* Old browsers */
					background: -moz-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f9ff', endColorstr='#a1dbff',GradientType=0 ); /* IE6-9 */
				}
								.gd-color2
				{
					background: rgb(247,251,252); /* Old browsers */
					background: -moz-linear-gradient(top, rgba(247,251,252,1) 0%, rgba(217,237,242,1) 40%, rgba(173,217,228,1) 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7fbfc', endColorstr='#add9e4',GradientType=0 ); /* IE6-9 */
					font-weight: bold;
				}
				.gd-color2 td
				{
					border: 1px solid #777;
					text-align: right;
				}
				.gd-color3
				{
					background: rgb(254,255,255); /* Old browsers */
					background: -moz-linear-gradient(top, rgba(254,255,255,1) 0%, rgba(221,241,249,1) 35%, rgba(160,216,239,1) 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#feffff', endColorstr='#a0d8ef',GradientType=0 ); /* IE6-9 */
					border: 1px solid #dccdcd;
					font-weight: bold;
				}
				.t-header{
					border: 1px solid #8DAFDA !important;
					background-image: -webkit-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%) !important;
				}
				.text-center{
					text-align:center;
				}
				.text-left{
					text-align:left !important;
				}
				tr td{
					vertical-align: middle !important;
				}

	        </style>
                <div style="width:<?=$table_width?>; margin: 0 auto">
                    <table style="width:<?=$table_width?>;" cellspacing="0" style="margin:0 auto;">
						<tr style="border:none;">
                        	<td colspan="5">&nbsp;</td>
                            <td align="center" colspan="3" style="border:none; font-size:20px;font-weight: bold;">
							Style Wise Cutting Report
                            </td>
                        </tr>
                        <tr style="border:none;">
                        	<td colspan="5">&nbsp;</td>
                            <td colspan="3" align="center" style="border:none; font-size:17px; font-weight: bold;">
							<?
	        					$comp_names="";
	        					foreach(explode(",",$company_name) as $vals)
	        					{
	        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
	        					}
	        						echo $comp_names;
	        					 ?>
                            </td>
                        </tr>
                        <tr style="border:none;">
                        	<td colspan="6">&nbsp;</td>
                            <td align="center" style="border:none; font-size:14px;font-weight: bold;">
                                From Date :  <?= $production_data  ?>
                            </td>
                        </tr>
                    </table>
                    <div style="max-height:425px; overflow-y:scroll; width:3620px" id="scroll_body">
					<?php
					 $i=1;
					 $jj=1;
					?>
                        <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<?=$table_width?>" rules="all" id="table_filter_" >
						<thead id="table_header_1">
                            <tr>
                                <th width="70">Buyer</th>
                                <th width="70">Job No.</th>
                                <th width="70">Style No</th>
                                <th width="100">Style Description </th>
                                <th width="70">Season</th>
                                <th width="100">Item</th>
                                <th width="150">Po No</th>
                                <th width="150">Colour</th>
                                <th width="100">Description</th>
                             	<th colspan="<?= $arr_size; ?>">Size Wise Break-up</th>
                                <th width="80">Total</th>
                                <th width="80" title="(Total Variance / Total Order Qty)*100">Percentage</th>
                             </tr>
                        </thead>
							<?php
								if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tbody>
								<?php
									$prev_job = '';
									foreach ($data_array as $job_no => $job_array)
									{
										foreach ($job_array as $color_id => $row)
										{
											$variance =[];
											$extra_colspan =  ($arr_size - count($row['SIZE_NUMBER_ID']));
											$total_order_qty = array_sum($row['ORDER_QUANTITY']);
											$total_cutting_qty = array_sum($row['PRODUCTION_QUANTITY']);
											$total_variance = $total_cutting_qty - $total_order_qty;
											$percentage =  ($total_variance / $total_order_qty) * 100;

											?>
											<tr class="text-center"    bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
												<?php
													if($prev_job != $job_no){
												?>
													<td rowspan="<?= ( 4 * count($job_array)) ?>" width="70"><p><?= $buyer_library[$row ['BUYER_NAME']]  ?></p></td>
													<td rowspan="<?= ( 4 * count($job_array)) ?>" width="70"><?= $job_no ?>  </td>
													<td rowspan="<?= ( 4 * count($job_array)) ?>" width="70"><?= $row['STYLE_REF_NO'] ?></td>
													<td rowspan="<?= ( 4 * count($job_array)) ?>" width="100"><p><?= $row['STYLE_DESCRIPTION'] ?></p> </td>
													<td rowspan="<?= ( 4 * count($job_array)) ?>" width="70"><?= $season_library[$row['SEASON_BUYER_WISE']]  ?></td>
													<td rowspan="<?= ( 4 * count($job_array)) ?>" width="100"><p>
														<?php
															$k=0;
															$str_con = '';
															foreach( $row['ITEM_NUMBER_ID'] as $item_id)
															{
																if($k>0) {
																	echo $str_con .= $garments_item[$item_id].',';
																}
																else{
																	echo $str_con .= $garments_item[$item_id];
																}
																$k++;
															}

														?>
														</p>
													</td>
												<?php } ?>
												<td  rowspan="4"  width="150"><p><?= implode(',' , $row['PO_NUMBER'])  ?></p></td>
												<td  rowspan="4"  width="150"><p><?= $color_library[$color_id]  ?></p></td>
												<td  class='t-header'> </td>
												<?php
													foreach ($row['SIZE_NUMBER_ID'] as $key =>  $v)
													{
														$variance[$key] = $row['PRODUCTION_QUANTITY'][$key] - $row['ORDER_QUANTITY'][$key];
													echo "<td class='t-header' > $size_library[$v] </td> ";
													}
													if( $extra_colspan > 0 )
													{
														echo "<td  rowspan='4' colspan= '$extra_colspan'></td>";
													}
												?>
												<td width="70">    </td>
												<td rowspan="4" width="80"><?= number_format($percentage,2) ?>%</td>
											</tr>
											<tr class="text-center" >
												<td  class="text-left">Total Order Qty </td>
												<?php
													foreach ($row['ORDER_QUANTITY'] as   $v)
													{
														echo "<td> $v</td> ";
													}
												?>
												<td><?= $total_order_qty ?></td>
											</tr>
											<tr class="text-center" >
												<td class="text-left"> Total Cutting </td>
												<?php
													foreach ($row['PRODUCTION_QUANTITY'] as   $v)
													{
														echo "<td> $v</td> ";
													}
												?>
												<td><?= $total_cutting_qty ?></td>
											</tr>
											<tr class="text-center" >
												<td class="text-left"  title="(Total Cutting - Total Order Qty)">Variance</td>
												<?php

													foreach ($variance as   $v)
													{
														echo "<td> $v</td> ";
													}
												?>
												<td  ><?=  $total_variance ?> </td>
											</tr>
											<?php
												$jj++;
												$prev_job = $job_no;
										}
									}
								?>
							</tbody>
						</table>
					</div>
                </div>
                <br />
    		</div>
    	<?
    }
    else //Production Wise
    {
		$company_cond="";
		$company_cond_lay="";
		$company_cond_lay_a="";
		$company_cond_delv="";
		$str_po_cond="";
		$str_po_cond2="";
		if($cbo_working_company!=0) $company_cond.=" and d.serving_company in($cbo_working_company)";
		if($cbo_working_company!=0) $company_cond_lay.=" and d.working_company_id in($cbo_working_company)";
		if($cbo_working_company!=0) $company_cond_lay_a.=" and a.working_company_id in($cbo_working_company)";
		if($cbo_working_company!=0) $company_cond_delv.=" and d.delivery_company_id in($cbo_working_company)";
		if($txt_job_no!= 0) $str_po_cond.=" and a.job_no=$txt_job_no";
		if($txt_job_no!= 0) $str_po_cond2.=" and a.job_no=$txt_job_no";
		if($cbo_buyer!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer";
		if($cbo_buyer!=0) $str_po_cond2.=" and a.buyer_name=$cbo_buyer";
		if($cbo_location) $str_po_cond.=" and d.location_id in($cbo_location)";
		if($cbo_location) $str_po_cond2.=" and d.location in($cbo_location)";
		if($order_no) $str_po_cond.=" and b.po_number in($txt_order_no)";

		$location_name=str_replace("'", "", $cbo_location_name);
		if($location_name) $location_cond.=" and a.location_id in($location_name)";
		if($location_name) $location_cond2.=" and a.location in($location_name)";

		if($hidden_order_id)
		{
			$str_po_cond.=" and b.id in($hidden_order_id)";
			$str_po_cond2.=" and b.id in($hidden_order_id)";
		}
		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);
		$str_com_cond_lay=str_replace("d.working_company_id", "a.working_company_id", $company_cond_lay);

		// =================================== GETTING TODAY CUTTING PO ID =================================
		$today_lay_sql="SELECT b.color_id,c.order_id as po_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
		where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and b.mst_id=c.mst_id  and a.entry_date=$txt_date and company_id in($company_name) $str_com_cond_lay $location_cond and a.status_active in(1) and a.is_deleted=0 and b.status_active in(1) and b.is_deleted=0 and c.status_active in(1) and c.is_deleted=0 " ;
		$today_lay_res = sql_select($today_lay_sql);
		$po_id_arr = [];
		$color_id_arr = [];
		foreach ($today_lay_res as $key => $val) {
			if($val[csf('po_id')] != "")
			{
				$po_id_arr[$val[csf('po_id')]] = $val[csf('po_id')];
				$color_id_arr[$val[csf('color_id')]] = $val[csf('color_id')];
			}

		}

		// ================================ TODAY PRODUCTION PO ID ================================
		$str_com_cond_prod = str_replace("d.serving_company", "a.serving_company", $company_cond);
		$today_pro_sql="SELECT b.color_number_id,a.po_break_down_id as po_id from pro_garments_production_mst a,wo_po_color_size_breakdown b,pro_garments_production_dtls c
		where a.id=c.mst_id and c.color_size_break_down_id=b.id and  a.po_break_down_id=b.po_break_down_id and c.status_active=1 and a.production_date=$txt_date and a.company_id in($company_name) $str_com_cond_prod $location_cond2 and b.status_active in(1) and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.production_type in(1,2,3,4) group by b.color_number_id,a.po_break_down_id" ; // $str_com_cond_prod
		$today_pro_res = sql_select($today_pro_sql);

		foreach ($today_pro_res as $key => $val) {
			if($val[csf('po_id')] != "")
			{
				$po_id_arr[$val[csf('po_id')]] = $val[csf('po_id')];
				$color_id_arr[$val[csf('color_id')]] = $val[csf('color_id')];
			}

		}


		$po_ids =trim(implode(',', $po_id_arr),",");
		$color_ids = trim(implode(',', $color_id_arr),",");
		// ========================= FOR PO ID ARRAY ==========================
		if(count($po_id_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_arr, 999);
	     	$po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="")
	     		{
	     			$po_ids_cond.=" and ( d.po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or   d.po_break_down_id in ($imp_ids) ";
	     		}
	     	}
	     	 $po_ids_cond.=" )";
	    }
	    else
	    {
	     	$po_ids_cond= " and d.po_break_down_id in($po_ids) ";
	    }

	     // ========================= FOR COLOR ID ARRAY ==========================

	    if(count($color_id_arr)>999 && $db_type==2)
	    {
	     	$color_chunk=array_chunk($color_id_arr, 999);
	     	$color_ids_cond= "";
	     	foreach($color_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($color_ids_cond=="")
	     		{
	     			$color_ids_cond.=" and ( e.color_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$color_ids_cond.=" or   e.color_id in ($imp_ids) ";
	     		}
	     	}
	     	 $color_ids_cond.=" )";
	     }
	     else
	     {
	     	$color_ids_cond= " and e.color_id in($color_ids) ";
	     }

	    // ================================== FOR CUT AND LAY ===================================
	    $prod_po_id_lay = str_replace('d.po_break_down_id', 'f.order_id', $po_ids_cond);
		$style_owner_cond = $style_owner ?  " and a.style_owner=$style_owner " : "";
		$lay_sql = "SELECT c.color_number_id as color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
			where a.job_no=b.job_no_mst  and b.id=c.po_break_down_id and d.job_no=a.job_no and b.id=f.order_id and d.id=e.mst_id  and a.job_no=c.job_no_mst and e.mst_id=f.mst_id and f.order_id=b.id and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1)  and b.is_deleted=0 and c.status_active in(1) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.company_id in($company_name)   $str_po_cond    $prod_po_id_lay $style_owner_cond
			group by c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status "; // $company_cond_lay
			// echo $lay_sql;die();
			$production_main_array=[];
			foreach(sql_select($lay_sql) as $row)
			{
				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("working_company_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

				$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
				unset($prod_po_id_array[$row[csf("po_id")]]);

			}

			// echo "<pre>";
			// print_r($production_main_array);die();
			if(count($production_main_array)==0)
			{
				echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px;text-align:center">No Data Found.</div>'; die;
			}
			//=========================== FOR FINISH FAB QNTY ===========================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'a.po_break_down_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'b.color_number_id', $color_ids_cond);

			$booking_no_fin_qnty_array=array();
			$booking_sql=sql_select("SELECT a.po_break_down_id ,b.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown b  where b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $prod_po_id_lay $prod_color_id_lay ");
			foreach($booking_sql as $vals)
			{
				$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];
			}

			// ==================================== FOR ISSUE ====================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'po_breakdown_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'color_id', $color_ids_cond);

			$issue_sql=sql_select("SELECT po_breakdown_id,color_id,trans_id, sum(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=18 $prod_po_id_lay $prod_color_id_lay group by po_breakdown_id,color_id,trans_id");
			foreach($issue_sql as $values)
			{
			 	$issue_qnty_arr[$values[csf("po_breakdown_id")]][$values[csf("color_id")]]+=$values[csf("qnty")];
			}
			//======================================= FOR CONSUMPTION ===========================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'b.po_break_down_id', $po_ids_cond);
			$result_consumtion=array();
			$sql_consumtiont_qty=sql_select("SELECT a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id $prod_po_id_lay GROUP BY a.job_no, a.body_part_id");

			foreach($sql_consumtiont_qty as $row_consum)
			{
				$result_consumtion[$row_consum[csf('job_no')]]+=($row_consum[csf("requirment")]/$row_consum[csf("pcs")]);
			}

			// ========================================FOR LAY QUANTITY=====================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'c.order_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'b.color_id', $color_ids_cond);

			$lay_sqls="SELECT  a.job_no,c.order_id, b.gmt_item_id,b.color_id,sum( case when a.entry_date=$txt_date then  c.size_qty else 0 end) as today_lay,sum(CASE WHEN a.entry_date <= $txt_date THEN c.size_qty ELSE 0 END) as total_lay from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and b.mst_id=c.mst_id and a.entry_form=99 and company_id in($company_name) $str_com_cond_lay $location_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $prod_po_id_lay  group by a.job_no,c.order_id, b.gmt_item_id,b.color_id ";

			$lay_qnty_array=array();
			foreach(sql_select($lay_sqls) as $vals)
			{
				$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("color_id")]]["today_lay"]+=$vals[csf("today_lay")];
				$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("total_lay")];
			}

			// =================================== FOR ORDER QUANTITY ==================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'po_break_down_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'color_number_id', $color_ids_cond);
			$order_qnty_array=array();
			$order_qnty_sqls="SELECT po_break_down_id,color_number_id,order_quantity,item_number_id from wo_po_color_size_breakdown where status_active in(1) and is_deleted=0 $prod_po_id_lay ";
			 foreach(sql_select($order_qnty_sqls) as $values)
			 {
			 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("color_number_id")]]+=$values[csf("order_quantity")];
			 }
			 // echo "<pre>";
			 // print_r($order_qnty_array);
			 // =================================== FOR PRODUCTION DATA ==================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'b.id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'c.color_number_id', $color_ids_cond);
			$location_cond_prod = str_replace('a.location', 'd.location', $location_cond2);

			$order_sql="SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no  ,   c.color_number_id, sum(c.order_quantity) as order_quantity,

			sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_date then e.production_qnty else 0 end ) as today_cutting ,
			sum(case when d.production_type=1 and e.production_type=1 and d.production_date<=$txt_date then e.production_qnty else 0 end ) as total_cutting ,
			sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_date then e.production_qnty else 0 end ) as today_sewing_input ,
			sum(case when d.production_type=4 and e.production_type=4 and d.production_date<=$txt_date then e.production_qnty else 0 end ) as total_sewing_input ,
			sum(CASE WHEN d.production_type =2 and embel_name in(1,2) and d.production_date=$txt_date THEN e.production_qnty ELSE 0 END) AS today_emb_send_qnty,
			sum(CASE WHEN d.production_type =2 and embel_name in(1,2) and d.production_date<=$txt_date THEN e.production_qnty ELSE 0 END) AS total_emb_send_qnty,
			sum(CASE WHEN d.production_type =3 and embel_name in(1,2) and d.production_date=$txt_date THEN e.production_qnty ELSE 0 END) AS today_emb_rcv_qnty,
			sum(CASE WHEN d.production_type =3 and embel_name in(1,2) and d.production_date<=$txt_date THEN e.production_qnty ELSE 0 END) AS total_emb_rcv_qnty

			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
			where  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $po_ids_cond  $company_cond $location_cond_prod and d.production_type in(1,2,3,4)
			group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number, c.item_number_id, c.color_number_id,e.cut_no ";

			 // echo $order_sql;die;
			$serving_company_check = [];
			$order_sql_res = sql_select($order_sql);
			foreach($order_sql_res as $vals)
			{
				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_cutting"]+=$vals[csf("today_cutting")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_cutting"]+=$vals[csf("total_cutting")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_emb_send_qnty"]+=$vals[csf("today_emb_send_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_emb_send_qnty"]+=$vals[csf("total_emb_send_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_emb_rcv_qnty"]+=$vals[csf("today_emb_rcv_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_emb_rcv_qnty"]+=$vals[csf("total_emb_rcv_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["order_quantity"]+=$vals[csf("order_quantity")];
				$serving_company_check[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]][$vals[csf("serving_company")]]=$vals[csf("serving_company")];

			}
			// echo "<pre>";
			// print_r($serving_company_check);die;

			$rowspanArr = array();
			foreach ($production_main_array as $style => $styleData)
			{
				foreach ($styleData as $job => $jobDat)
				{
					foreach ($jobDat as $po => $poData)
					{
						foreach ($poData as $color => $colorData)
						{
							$rowspanArr[$style][$job][$po]++;
						}
					}
				}
			}

			// echo "<pre>";
			// print_r($rowspanArr);//die;

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
	            hr {
	                border: 0;
	                background-color: #000;
	                height: 1px;
	            }
	            .gd-color
	            {
					background: #f0f9ff; /* Old browsers */
					background: -moz-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f9ff', endColorstr='#a1dbff',GradientType=0 ); /* IE6-9 */
				}
				.gd-color2
				{
					background: rgb(247,251,252); /* Old browsers */
					background: -moz-linear-gradient(top, rgba(247,251,252,1) 0%, rgba(217,237,242,1) 40%, rgba(173,217,228,1) 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7fbfc', endColorstr='#add9e4',GradientType=0 ); /* IE6-9 */
					font-weight: bold;
				}
				.gd-color2 td
				{
					border: 1px solid #777;
					text-align: right;
				}
				.gd-color3
				{
					background: rgb(254,255,255); /* Old browsers */
					background: -moz-linear-gradient(top, rgba(254,255,255,1) 0%, rgba(221,241,249,1) 35%, rgba(160,216,239,1) 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#feffff', endColorstr='#a0d8ef',GradientType=0 ); /* IE6-9 */
					border: 1px solid #dccdcd;
					font-weight: bold;
				}

	        </style>
                <div style="width:3050px; margin: 0 auto">
                    <table width="3040" cellspacing="0" style="margin: 20px 0">
                        <tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td align="right" style="border:none; font-size:14px; font-weight: bold;" width="40%">
                                Working Company Name
                            </td>
                            <td align="center" style="border:none; font-size:14px;font-weight: bold;" width="5%">
                                 :
                            </td>
                            <td align="left" style="border:none; font-size:14px;font-weight: bold;" width="55%">
                                <?
	        					$comp_names="";
	        					foreach(explode(",",$cbo_working_company) as $vals)
	        					{
	        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
	        					}
	        					echo $comp_names;
	        					 ?>
                            </td>
                        </tr>
                        <tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td align="right" style="border:none; font-size:14px;font-weight: bold;" width="40%">
                                Date
                            </td>
                            <td align="center" style="border:none; font-size:14px;font-weight: bold;" width="5%">
                                :
                            </td>
                            <td align="left" style="border:none; font-size:14px;font-weight: bold;" width="55%">
                                <? echo change_date_format(str_replace("'", "", $txt_date)); ?>
                            </td>
                        </tr>
                    </table>



                <div>
                    <table width="3040" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                        <thead>
                            <tr>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="20">Sl.</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="150">Working Company</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="120">Location</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="150">Buyer</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="100">Job No.</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="110">Style ref.</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="110">Ship Status</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="90">Ship Date</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="110">Order No.</th>
                                <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="80">Color Qnty</th>
                                <th style="word-wrap: break-word;word-break: break-all;" colspan="5" width="500">Fabric Status</th>
                                <th style="word-wrap: break-word;word-break: break-all;" colspan="3" width="300">Lay Status</th>
                                <th style="word-wrap: break-word;word-break: break-all;" colspan="3" width="300">Cutting Status</th>
                                <th style="word-wrap: break-word;word-break: break-all;" colspan="5" width="500">Emblishment Status</th>
                                <th style="word-wrap: break-word;word-break: break-all;" colspan="4" width="400">Input Status</th>
                             </tr>
                             <tr>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="160">Color Name</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="90">F.Fab Req.</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="90">Fin. Fab Issue</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="90">F.Issued Balance</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="90">Possible Cut Qty.</th>

                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Today</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Total</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Lay.Balance</th>

                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Today</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Total</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Cut.Balance</th>

                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Today Send</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Total Send</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Today Rcv</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Total Rcv</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Print B/L</th>

                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Today</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Total</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Order-Input Balance</th>
                             	<th style="word-wrap: break-word;word-break: break-all;" width="100">Inhand Qty</th>
                             </tr>
                        </thead>
                    </table>
                    <div style="max-height:425px; overflow-y:scroll; width:3060px" id="scroll_body">
                        <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="3040" rules="all" id="table_filter" >
                        <?
                        $i=1;
						$jj=1;
						$grand_color_total = 0;
						// fabric status sum
						$grand_fab_req = 0;
						$grand_fin_fab_req = 0;
						$grand_fab_issued_balance = 0;
						$grand_fab_possible_qty = 0;
						// lay status sum
						$grand_today_lay=0;
						$grand_total_lay=0;
						$grand_lay_balance=0;
						// cutting status sum
						$grand_today_cutting=0;
						$grand_total_cutting=0;
						$grand_cut_balance=0;
						// emblishment status sum
						$grand_today_send=0;
						$grand_total_send=0;
						$grand_today_rcv=0;
						$grand_total_rcv=0;
						$grand_embl_balance=0;
						// sewing status sum
						$grand_today_input=0;
						$grand_total_input=0;
						$grand_input_balance=0;
						$grand_inhand_qty=0;
						foreach($production_main_array as $style_id=>$job_data)
						{
							foreach($job_data as $job_id=>$po_data)
							{
								$order_wise_color_total = 0;
								// fabric status sum
								$order_wise_fab_req = 0;
								$order_wise_fin_fab_req = 0;
								$order_wise_fab_issued_balance = 0;
								$order_wise_fab_possible_qty = 0;
								// lay status sum
								$order_wise_today_lay=0;
								$order_wise_total_lay=0;
								$order_wise_lay_balance=0;
								// cutting status sum
								$order_wise_today_cutting=0;
								$order_wise_total_cutting=0;
								$order_wise_cut_balance=0;
								// emblishment status sum
								$order_wise_today_send=0;
								$order_wise_total_send=0;
								$order_wise_today_rcv=0;
								$order_wise_total_rcv=0;
								$order_wise_embl_balance=0;
								// sewing status sum
								$order_wise_today_input=0;
								$order_wise_total_input=0;
								$order_wise_input_balance=0;
								$order_wise_inhand_qty=0;
								$r=0;
								foreach($po_data as $po_id=>$color_data)
								{


									//foreach($item_data as $item_id=>$color_data)
									//{
										foreach($color_data as $color_id=>$row)
										{

										 	$today_lay_qnty 				=$lay_qnty_array[$job_id][$po_id][$color_id]["today_lay"];
											$today_cutting_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_cutting"];
											$today_sewing_input 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_sewing_input"];
											$today_emb_send_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_emb_send_qnty"];
											$today_emb_rcv_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_emb_rcv_qnty"];

											// if($today_lay_qnty != "" || $today_cutting_qnty != "" || $today_sewing_input != "" || $today_emb_send_qnty != "" || $today_emb_rcv_qnty != "")
											// {
											$color_wise_today_cutting		=0;
											$color_wise_total_cutting		=0;
											$color_wise_today_sewing_input 	=0;
											$color_wise_total_sewing_input	=0;
											$color_wise_inh					=0;

											$fin_req 						=0;
											$fin_req 						=$booking_no_fin_qnty_array[$po_id][$color_id]["qnty"];
											$issue_qty 						=$issue_qnty_arr[$po_id][$color_id];
											$req_issue_bal					=($fin_req-$issue_qty);
											$possible_cut_pcs 				=$issue_qty/$result_consumtion[$job_id];
										 	$total_lay_qnty 				=$lay_qnty_array[$job_id][$po_id][$color_id]["total_lay"];
										 	$order_qty 						= $order_qnty_array[$po_id][$color_id];
											$total_cutting_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_cutting"];
											$color_wise_today_cutting    	+= $today_cutting_qnty;
											$color_wise_total_cutting 		+= $total_cutting_qnty;


											$total_sewing_input 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_sewing_input"];

											$total_emb_send_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_emb_send_qnty"];

											$total_emb_rcv_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_emb_rcv_qnty"];

											$color_wise_today_sewing_input 	+= $today_sewing_input;
											$color_wise_total_sewing_input 	+= $total_sewing_input;

											// order wise
											$order_wise_color_total 		+= $order_qty;

											$order_wise_fab_req 			+= $fin_req;
											$order_wise_fin_fab_req 		+= $issue_qty;
											$order_wise_fab_issued_balance 	+= $req_issue_bal;
											$order_wise_fab_possible_qty 	+= $possible_cut_pcs;

											$order_wise_today_cutting 		+=$today_cutting_qnty;
											$order_wise_total_cutting 		+=$total_cutting_qnty;
											$order_wise_cut_balance 		+= ($order_qty-$total_cutting_qnty);

											$order_wise_today_send 			+= $today_emb_send_qnty;
											$order_wise_total_send 			+= $total_emb_send_qnty;
											$order_wise_today_rcv 			+= $today_emb_rcv_qnty;
											$order_wise_total_rcv 			+= $total_emb_rcv_qnty;
											$order_wise_embl_balance 		+= ($total_emb_send_qnty - $total_emb_rcv_qnty);

											$order_wise_today_lay 			+=$today_lay_qnty;
											$order_wise_total_lay 			+=$total_lay_qnty;
											$order_wise_lay_balance 		+= ($order_qty - $total_lay_qnty);

											$order_wise_today_input 		+= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_sewing_input"];
											$order_wise_total_input 		+= $total_sewing_input;
											$order_wise_input_balance 		+= ($order_qty - $total_sewing_input);
											$order_wise_inhand_qty 			+= ($total_cutting_qnty-$total_sewing_input);

											// grand total
											$grand_color_total 				+= $order_qty;

											$grand_fab_req 					+= $fin_req;
											$grand_fin_fab_req 				+= $issue_qty;
											$grand_fab_issued_balance 		+= $req_issue_bal;
											$grand_fab_possible_qty 		+= $possible_cut_pcs;

											$grand_today_cutting 			+=$today_cutting_qnty;
											$grand_total_cutting 			+=$total_cutting_qnty;
											$grand_cut_balance 				+= ($order_qty-$total_cutting_qnty);

											$grand_today_send 				+= $today_emb_send_qnty;
											$grand_total_send 				+= $total_emb_send_qnty;
											$grand_today_rcv 				+= $today_emb_rcv_qnty;
											$grand_total_rcv 				+= $total_emb_rcv_qnty;
											$grand_embl_balance 			+= ($total_emb_send_qnty - $total_emb_rcv_qnty);

											$grand_today_lay 				+=$today_lay_qnty;
											$grand_total_lay 				+=$total_lay_qnty;
											$grand_lay_balance 				+= ($order_qty - $order_wise_total_lay);

											$grand_today_input 				+= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_sewing_input"];
											$grand_total_input 				+= $total_sewing_input;
											$grand_input_balance 			+= ($order_qty - $total_sewing_input);
											$grand_inhand_qty 				+= ($total_cutting_qnty-$total_sewing_input);

											$serving_company = implode(",", $serving_company_check[$style_id][$job_id][$po_id][$color_id]);


												if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
				                            	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="20"><? echo $i;?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="150"><? echo $company_library[$row['working_company_id']]; ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="120"><? echo $location_library[$row['location_id']]; ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="150"><? echo $buyer_library[$row['buyer_name']]; ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $job_id; ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="110"><? echo $style_id; ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="110"><? echo $row['shiping_status']; ?></td>
					                            	<td align="center" style="word-wrap: break-word;word-break: break-all;" width="90"><? echo change_date_format($row['pub_shipment_date']); ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="110"><? echo $row['po_number'];?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="80"><? echo number_format($order_qty,0); ?></td>
					                            	<td style="word-wrap: break-word;word-break: break-all;" width="160"><? echo $color_library[$color_id]; ?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="90"><? echo number_format($fin_req,2);?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="90">
					                            		<a href="javascript:void(0)" onclick="openmypage_fab_issue(<? echo $po_id;?>,<? echo $color_id?>,<? echo 1 ?>, 'fab_issue_popup');">
					                            			<? echo number_format($issue_qty,2);?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="90"><? echo number_format($req_issue_bal,2);?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="90"><? echo number_format($possible_cut_pcs,2);?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',0,'A','production_qnty_popup','Today Lay','600','300');">
					                            			<? echo $today_lay_qnty;?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',0,'B','production_qnty_popup','Total Lay','730','300');">
					                            		<? echo $total_lay_qnty;?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $lay_balance = $order_qty-$total_lay_qnty;?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',1,'A','production_qnty_popup','Today Cutting','600','300');">
					                            			<? echo $today_cutting_qnty;?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',1,'B','production_qnty_popup','Total Cutting','730','300');">
					                            			<? echo $total_cutting_qnty;?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $cut_balance = $order_qty-$total_cutting_qnty;?></td>
					                            	<!-- ============= Emblishment ================== -->
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',2,'A','production_qnty_popup','Today Emblishment Issue','1000','300');"><? echo number_format($today_emb_send_qnty,0); ?>

					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',2,'B','production_qnty_popup','Total Emblishment Issue','730','300');"><? echo number_format($total_emb_send_qnty,0); ?>

					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',3,'A','production_qnty_popup','Today Emblishment Receive','1000','300');"><? echo number_format($today_emb_rcv_qnty,0) ;?>

					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',3,'B','production_qnty_popup','Total Emblishment Receive','730','300');"><? echo number_format($total_emb_rcv_qnty,0); ?>

					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100"><? echo number_format(($total_emb_send_qnty - $total_emb_rcv_qnty),0); ?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',4,'A','production_qnty_popup','Today Sewing Input','800','300');">
					                            			<? echo $today_input = $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_sewing_input"];?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100">
					                            		<a href="javascript:void(0)" onclick="openmypage_production_popup(<? echo $po_id;?>,0,<? echo $color_id;?>,'<? echo $serving_company;?>',4,'B','production_qnty_popup','Total Sewing Input','730','300');">
					                            			<? echo $total_sewing_input;?>
					                            		</a>
					                            	</td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100"><? echo 	$input_balance = $order_qty-$total_sewing_input;?></td>
					                            	<td align="right" style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $inh_qty= ($total_cutting_qnty-$total_sewing_input);?></td>
				                            	</tr>
									        <?
									        $i++;$jj++;
									    	//}
									    }
									//}
								}
								?>
								<tr class="gd-color3">
					            	<td style="word-wrap: break-word;word-break: break-all;" width="960" colspan="9" align="right">Order Wise Sub Total:</td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><b><? echo number_format($order_wise_color_total,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="160">&nbsp;</td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($order_wise_fab_req,2); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($order_wise_fin_fab_req,2); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($order_wise_fab_issued_balance,2); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($order_wise_fab_possible_qty,2); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_today_lay,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_total_lay,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_lay_balance,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_today_cutting,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_total_cutting,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_cut_balance,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_today_send,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_total_send,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_today_rcv,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_total_rcv,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_embl_balance,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_today_input,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_total_input,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_input_balance,0); ?></b></td>
					            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($order_wise_inhand_qty,0); ?></b></td>
					        	</tr>
								<?
							}
						}
						?>
						</table>
					</div>
						<table  border="1" class=""  width="3040" rules="all" id="" cellpadding="0" cellspacing="0">
							<tr class="gd-color2">
								<td style="word-wrap: break-word;word-break: break-all;" width="20">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="150">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="120">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="150">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="100">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="110">&nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="110"> &nbsp;</td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="90"> &nbsp;</td>
				            	<td style="word-wrap: break-word;word-break: break-all;" width="110" align="right">Grand Total:</td>
				            	<td id="grand_color_total" style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><b><? echo number_format($grand_color_total,0); ?></b></td>
				            	<td style="word-wrap: break-word;word-break: break-all;" align="right" width="160">&nbsp;</td>
				            	<td id="grand_fab_req" style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($grand_fab_req,2); ?></b></td>
				            	<td id="grand_fin_fab_req" style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($grand_fin_fab_req,2); ?></b></td>
				            	<td id="grand_fab_issued_balance" style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($grand_fab_issued_balance,2); ?></b></td>
				            	<td id="grand_fab_possible_qty" style="word-wrap: break-word;word-break: break-all;" align="right" width="90"><b><? echo number_format($grand_fab_possible_qty,2); ?></b></td>
				            	<td id="grand_today_lay" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_today_lay,0); ?></b></td>
				            	<td id="grand_total_lay" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_total_lay,0); ?></b></td>
				            	<td id="grand_lay_balance" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_lay_balance,0); ?></b></td>
				            	<td id="grand_today_cutting" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_today_cutting,0); ?></b></td>
				            	<td id="grand_total_cutting" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_total_cutting,0); ?></b></td>
				            	<td id="grand_cut_balance" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_cut_balance,0); ?></b></td>
				            	<td id="grand_today_send" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_today_send,0); ?></b></td>
				            	<td id="grand_total_send" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_total_send,0); ?></b></td>
				            	<td id="grand_today_rcv" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_today_rcv,0); ?></b></td>
				            	<td id="grand_total_rcv" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_total_rcv,0); ?></b></td>
				            	<td id="grand_embl_balance" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_embl_balance,0); ?></b></td>
				            	<td id="grand_today_input" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_today_input,0); ?></b></td>
				            	<td id="grand_total_input" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_total_input,0); ?></b></td>
				            	<td id="grand_input_balance" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_input_balance,0); ?></b></td>
				            	<td id="grand_inhand_qty" style="word-wrap: break-word;word-break: break-all;" align="right" width="100"><b><? echo number_format($grand_inhand_qty,0); ?></b></td>
				        	</tr>
						</table>
                </div>
                <br />
    		</div><!-- end main div -->
    	<?

    }
	$floor_name = implode(',', $floor_arr);
	$floor_wise_total = implode(',', $floor_total_arr);


	foreach (glob($user_id."_*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');
	$is_created = fwrite($create_new_excel,ob_get_contents());
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo "####".$name;
	exit();
}

if($action=="fab_issue_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$data=explode("_", $data);
	if($data[2]==2) $color_cond=" and color_id =$data[1] ";
	?>
    </head>
    <body>
        <div align="center" style="width:100%;" >

             	<table width="660" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
             		<caption> <strong>Issue To Cutting Info</strong></caption>
             		<thead>
             			<tr>
             				<th width="30" >SL</th>
             				<th width="110">Issue No</th>
             				<th width="90">Challan No</th>
             				<th width="90">Issue Date</th>
             				<th width="90">Batch No</th>
             				<th width="90">Issue Qnty</th>
             				<th width="160">Fabric Description</th>
             			</tr>
             		</thead>
             	</table>
             	<div style="">
             	<table  width="660" border="1" rules="all" class="rpt_table">
             	<?
             	$p=1;
             	$sqls=sql_select("SELECT a.issue_number,a.issue_date,a.challan_no,b.trans_id,sum(b.issue_qnty) as qnty,b.batch_id from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.order_id='$data[0]' group by a.issue_number,a.issue_date,a.challan_no,b.trans_id,b.batch_id");

             	$batch_sql="SELECT a.id, a.batch_no,b.item_description from pro_batch_create_mst a,pro_batch_create_dtls b  where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ";
             	foreach(sql_select($batch_sql) as $vals)
             	{
             		$batch_array[$vals[csf("id")]]["batch_no"]=$vals[csf("batch_no")];
             		$batch_array[$vals[csf("id")]]["item_description"]=$vals[csf("item_description")];
             	}
             	$qnty_sql=sql_select("SELECT po_breakdown_id,color_id,trans_id, sum(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=18 and po_breakdown_id=$data[0] $color_cond group by po_breakdown_id,color_id,trans_id  ");
             	foreach($qnty_sql as $vls)
             	{
             		$qnty_array[$vls[csf("po_breakdown_id")]][$vls[csf("trans_id")]][$vls[csf("color_id")]]=$vls[csf("qnty")];
             	}

             	$total=0;
             	foreach($sqls as  $keys=> $rows)
             	{

             		?>

             		<tr>
         				<td align="center" width="30" ><? echo $p++;?></td>
         				<td align="center"  width="110"><? echo $rows[csf("issue_number")];?></td>
         				<td align="center"  width="90"><? echo $rows[csf("challan_no")];?></td>
         				<td align="center"  width="90"><? echo $rows[csf("issue_date")];?></td>
         				<td align="center"  width="90"><? echo $batch_array[$rows[csf("batch_id")]]["batch_no"];?></td>
         				<td align="center"  width="90"><? echo $qntys=$qnty_array[$data[0]][$rows[csf("trans_id")]][$data[1]];?></td>
         				<td align="center"  width="160"><? echo $batch_array[$rows[csf("batch_id")]]["item_description"];?></td>
             			</tr>

					<?
					$total+=$qntys;
				}
				?>
						<tr bgcolor="#E4E4E4">
             				<td colspan="5" align="right">Total</td>
             				<td  align="center"  width="90"><? echo $total;?></td>
             				<td  align="center"  width="160">&nbsp;</td>
             			</tr>
             		</table>
             		</div>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>

    <?
	exit();
}

if($action=="production_qnty_popup")
{
	// var_dump($_REQUEST);
 	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$dates=$txt_date;
	$date_cond="";
	$wo_company_cond = "";
	if($wo_company !=0 || $wo_company !=""){ $wo_company_cond = " and a.serving_company in($wo_company)";}
	?>

	<div id="data_panel" align="center" style="width:100%">
		<script>
			function new_window()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
	</div>

	<?
	if($day=='A')
	{
		$date_cond=" and a.production_date=$dates";
		$date_cond_lay=" and a.entry_date=$dates";
	}
	$companyarr=return_library_array("SELECT id,company_name from lib_company ","id","company_name");
	$floorarr=return_library_array("SELECT id,floor_name from lib_prod_floor ","id","floor_name");
	$resourcearr=return_library_array("SELECT id,line_number from prod_resource_mst ","id","line_number");
	$linearr=return_library_array("SELECT id,line_name from lib_sewing_line ","id","line_name");
	$locationarr=return_library_array("SELECT id,location_name from lib_location ","id","location_name");
	$countryarr=return_library_array("SELECT id,country_name from lib_country ","id","country_name");
	$buyerarr=return_library_array("SELECT id,buyer_name from lib_buyer ","id","buyer_name");
	$sizearr=return_library_array("SELECT id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("SELECT id,color_name from  lib_color ","id","color_name");
	$po_arr=return_library_array("SELECT id,po_number from  wo_po_break_down where id=$po and status_active in(1,2,3) ","id","po_number");
	$po_qnty_arr=return_library_array("SELECT id,po_quantity from  wo_po_break_down where id=$po and status_active in(1,2,3) ","id","po_quantity");
    $party_library = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");

    if( ($type==1 && $day=='A') ||($type==0 && $day=='A'))
	{
		$size_id_array=array();
		$size_wise_qnty_array=array();
		$color_wise_qnty_array=array();
		// echo "Hello";
		$data_array=array();
		if($type==1)
		{
			$production_data="SELECT a.po_break_down_id,a.item_number_id,sum(b.production_qnty) as production_qnty,d.size_number_id,d.color_number_id,d.country_id from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_break_down c,wo_po_color_size_breakdown d where a.id=b.mst_id and b.color_size_break_down_id=d.id and a.po_break_down_id=c.id and d.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id=$po and a.production_type=$type  and d.color_number_id=$color $date_cond group by  a.po_break_down_id,a.item_number_id,d.size_number_id,d.color_number_id,d.country_id ";
		}
		else if($type==0)
		{
			$production_data="SELECT c.order_id as po_break_down_id,b.gmt_item_id as item_number_id,sum(c.size_qty) as production_qnty,c.size_id as size_number_id,b.color_id as color_number_id,c.country_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c  where  a.id=b.mst_id and b.id=c.dtls_id     and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active =1 and c.is_deleted=0  and c.order_id=$po  and b.color_id=$color $date_cond_lay group by c.order_id ,b.gmt_item_id,c.size_id,b.color_id,c.country_id ";
		}

		$production_data=sql_select($production_data);

		foreach($production_data as $vals)
		{
			$data_array[$vals[csf("color_number_id")]][$vals[csf("country_id")]]=$vals[csf("country_id")];
			$size_id_array[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
			$size_wise_qnty_array[$vals[csf("color_number_id")]][$vals[csf("country_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];
			$color_wise_qnty_array[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];
		}
		$counts=count($size_id_array);
		?>

			<div id="details_reports">
				<table width="<? echo 280+($counts*50); ?>" cellspacing="0" cellpadding="0" border="0"   rules="">

					<thead>
						<?
						if(($day=="A" && $type==1) || ($day=="A" && $type==0))
						{
							?>
							<tr>
								<td height="5">&nbsp;</td>
							</tr>
							<tr>
								<td><strong>Date : <? echo change_date_format(str_replace("'", "", $dates)); ?></strong></td>

							</tr>
							<tr>
								<td><strong>Order No: <? echo $po_arr[$po]; ?></strong></td>

							</tr>

							<?
						}
	             			//die;
						?>

					</thead>
				</table>




				<table width="<? echo 280+($counts*50); ?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
					<thead>
						<tr>
							<th width="30" rowspan="2">SI</th>
							<th width="100" rowspan="2">Color</th>
							<th width="100" rowspan="2">Country Name</th>
							<th colspan="<? echo $counts;?>">Size</th>
							<th width="50" rowspan="2">Total</th>
						</tr>
						<tr>
							<?
							foreach ($size_id_array as $value)
							{
								?>
								<th width="50"><? echo $sizearr[$value]; ?></th>

								<?

							}

							?>
						</tr>
					</thead>
				</table>
					<div style="max-height:300px;  ">
						<table  id="table_body"  width="<? echo 280+($counts*50); ?>" border="1" rules="all" class="rpt_table" align="left" >
							<?
							$p=1;
							$gr_total=0;
							foreach($data_array as  $color_id=> $country_val)
							{
								foreach($country_val as  $country_id=> $rows)
								{




									?>
									<tr>
										<td align="center" width="30" ><? echo $p++;?></td>
										<td align="center"  width="100"><? echo $colorarr[$color_id];?></td>
										<td align="center"  width="100"><b><? echo $countryarr[$country_id];?></b></td>
										<?
										$total_qnty=0;
										foreach ($size_id_array as $value)
										{
											?>
											<td align="center" width="50"><?  echo $qntys= $size_wise_qnty_array[$color_id][$country_id][$value];  ?></td>

											<?
											$total_qnty+=$qntys;

										}

										?>

										<td align="center"  width="50"><b><? echo $total_qnty;?></b></td>


									</tr>
									<?
								}
								$gr_total+=$qntys;
								?>

								<tr>

									<td align="right" colspan="3"><b>Color Total: </b></td>
									<?
									$total_qnty=0;
									foreach ($size_id_array as $value)
									{
										?>
										<td align="center" width="50"><?  echo $qntys= $color_wise_qnty_array[$color_id][$value];  ?></td>

										<?
										$total_qnty+=$qntys;

									}

									?>

									<td align="center"  width="50"><b><? echo $total_qnty;?></b></td>


								</tr>
								<?


							}
							?>
							<tr>

								<td align="right" colspan="3"><b>Day Total: </b></td>
								<?
								$total_qnty=0;
								foreach ($size_id_array as $value)
								{
									?>
									<td align="center" width="50"><?  echo $qntys= $color_wise_qnty_array[$color_id][$value];  ?></td>

									<?
									$total_qnty+=$qntys;

								}

								?>

								<td align="center"  width="50"><b><? echo $total_qnty;?></b></td>


							</tr>

						</table>
					</div>


				</div>

			</div>
			<script>   setFilterGrid("table_body",-1);  </script>


		<?

	}
	else if( ($type==1 && $day=='B') || ($type==4 && $day=='B') || ($type==5 && $day=='B') || ($type==8 && $day=='B') || ($type==0 && $day=='B'))
	{
		$order_data="SELECT e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po  and d.color_number_id=$color  group by  e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date ";
		if($type)
		{

			$production_data="SELECT a.floor_id,a.sewing_line, a.production_date,a.serving_company,a.location,sum(case when production_source=1 then b.production_qnty else 0 end ) as inhouse ,sum(case when production_source=3 then b.production_qnty else 0 end ) as outbound   from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where a.id=b.mst_id and b.color_size_break_down_id=d.id and a.po_break_down_id=c.id and d.po_break_down_id=c.id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id=$po and a.production_type=$type  and d.color_number_id=$color  group by   a.floor_id,a.sewing_line,a.production_date,a.serving_company,a.location ";

		}

		else if($type==0)
		{
			$production_data="SELECT a.entry_date as production_date,a.location_id as location,a.working_company_id as serving_company,  sum(c.size_qty) as inhouse from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c  where  a.id=b.mst_id and b.id=c.dtls_id     and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active =1 and c.is_deleted=0  and c.order_id=$po    and b.color_id=$color  group by a.entry_date ,a.location_id ,a.working_company_id ";
		}
		$production_data=sql_select($production_data);


		?>
			<div id="details_reports">
				<table width="700" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
					<thead>
						<tr>
							<th width="100">Buyer</th>
							<th width="100">Job Number</th>
							<th width="100">Style Name</th>
							<th width="100">Order Number</th>
							<th width="100">Ship Date</th>
							<th width="100">Item Name</th>
							<th width="80">Order Qty.</th>
						</tr>
					</thead>
					<tbody>
						<?

						foreach(sql_select($order_data) as $vals)
						{
							?>
							<tr>
								<td align="center" width="100"><?echo $buyerarr[$vals[csf("buyer_name")]]; ?></td>
								<td align="center" width="100"><?echo $vals[csf("job_no")]; ?></td>
								<td align="center" width="100"><?echo $vals[csf("style_ref_no")]; ?></td>
								<td align="center" width="100"><?echo $vals[csf("po_number")]; ?></td>
								<td align="center" width="100"><?echo $vals[csf("pub_shipment_date")]; ?></td>
								<td align="center" width="100"><?echo $garments_item[$vals[csf("item_number_id")]]; ?></td>
								<td align="center" width="80"><?echo $vals[csf("po_qnty")]; ?></td>
							</tr>
							<?


					    }



						//}
						if($type==5 || $type==4)$tbl_wid=700;else $tbl_wid=500;



						?>
					</tbody>
				</table>
				<br>
				<br>


				    <table style="margin-top: 10px;" width="<? echo $tbl_wid;?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
							<thead>
								<tr>
									<th  width="30" rowspan="2">SI</th>
									<th width="100" rowspan="2"><? if($type==1 || $type==0)echo "Cutting Date";else if($type==4 || $type==5 ) echo "Sewing Date"; else if($type==8) echo "Finish Date";?></th>
									<?
										if($type==5 || $type==4)
										{
											?>
											<th rowspan="2" width="100">Floor</th>
											<th rowspan="2" width="100">Sewing Line</th>

											<?
										}
									?>
									<th width="100" colspan="2"><? if($type==1)echo "Cutting Qty";else  if($type==4 || $type==5) echo "Sewing Qty";else if($type==8) echo "Finish Qty";else if($type==0) echo "Lay Qty";?> </th>
									<th width="100" rowspan="2"><? if($type==1)echo "Cutting Company";else  if($type==4 || $type==5) echo "Sewing Comany";else if($type==8) echo "Finish Company";else if($type==0) echo "Lay Company";?></th>

									<th width="100" rowspan="2">Location</th>
								</tr>
								<tr>
									<th width="50">In-house</th>
									<th width="50">Out-bound</th>

								</tr>
							</thead>
					</table>
						<div style="max-height:300px;  ">
							<table id="table_body"  width="<? echo $tbl_wid;?>"  border="1" rules="all" class="rpt_table"  >

								<tbody>
									<?
									$p=1;
									$total_inhouse=0;
									$total_out=0;
									foreach($production_data as $vals)
									{
										?>
										<tr>
											<td align="center" width="30"><?echo $p++; ?></td>
											<td align="center" width="100"><?echo change_date_format($vals[csf("production_date")]); ?></td>

											<?
											if($type==5 || $type==4)
											{
												?>
												<td width="100" align="center"><? echo $floorarr[$vals[csf("floor_id")]]; ?></td>
												<td width="100" align="center"><?  $line= explode(",",  $resourcearr[$vals[csf("sewing_line")]]);
													$lines="";
													foreach($line as $val)
													{

														if($lines=="") $lines=$linearr[$val];
														else  $lines.=','.$linearr[$val];
													}
													echo $lines;
													?></td>

													<?
												}
												$total_inhouse+=$vals[csf("inhouse")];
												$total_out+=$vals[csf("outbound")];
												?>


												<td align="center" width="50"><?echo $vals[csf("inhouse")]; ?></td>
												<td align="center" width="50"><?echo $vals[csf("outbound")]; ?></td>
												<td align="center" width="100"><?echo $companyarr[$vals[csf("serving_company")]]; ?></td>
												<td align="center" width="100"><?echo $locationarr[$vals[csf("location")]]; ?></td>

											</tr>
											<?
									}
										?>




								</tbody>

							</table>
							<div>

					<table   width="<? echo $tbl_wid;?>"  border="1" rules="all" class="rpt_table"  >
						<tfoot>
							<tr>


								<?
								if($type==5 || $type==4)
								{
									?>
									<td width="30">&nbsp;</td>
									<td width="100" >&nbsp;</td>
									<td width="100" >&nbsp;</td>
									<td width="100"   align="right"><strong>Grand Total</strong></td>



									<?
								}
								else
								{
									?>
									<td width="30">&nbsp;</td>
									<td width="100"   align="right"><strong>Grand Total</strong></td>

									<?
								}
								?>


								<td id="ttl_inhouse" align="center" width="50"><?//echo $total_inhouse; ?></td>
								<td id="ttl_outbound" align="center" width="50"><?//echo $total_out; ?></td>
								<td width="100" >&nbsp;</td>
								<td width="100" >&nbsp;</td>

							</tr>

						</tfoot>



					</table>
			</div>

			</div>



					<script type="text/javascript">
 								var tableFilters1 =
								{

									col_operation: {
										id: ["ttl_inhouse","ttl_outbound"],
										col: [2,3],
										operation: ["sum","sum"],
										write_method: ["innerHTML","innerHTML"]
									}
								}

								var tableFilters2 =
								{

									col_operation: {
										id: ["ttl_inhouse","ttl_outbound"],
										col: [4,5],
										operation: ["sum","sum"],
										write_method: ["innerHTML","innerHTML"]
									}
								}
								var type='<? echo $type;?>';
								 if(type==4 || type==5)
								 {
								 	setFilterGrid("table_body",-1,tableFilters2);
								 }
								 else
								 {
								 	setFilterGrid("table_body",-1,tableFilters1);
								 }


					</script>



		<?

	}


	else if( ($type==4 && $day=='A') || ($type==5 && $day=='A') ||   ($type==8 && $day=='A') )
	{
		$order_data="SELECT c.id, e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,d.color_number_id,d.size_number_id, sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po  and d.color_number_id=$color  group by c.id, e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,d.color_number_id,d.size_number_id ";
		$job_array=array();
		foreach(sql_select($order_data) as $vals)
		{
			$job_array[$vals[csf("id")]]["buyer_name"]=$buyerarr[$vals[csf("buyer_name")]];
			$job_array[$vals[csf("id")]]["job_no"]=$vals[csf("job_no")];
			$job_array[$vals[csf("id")]]["style_ref_no"]=$vals[csf("style_ref_no")];
			$job_array[$vals[csf("id")]]["po_number"]=$vals[csf("po_number")];
			$job_array[$vals[csf("id")]]["pub_shipment_date"]=$vals[csf("pub_shipment_date")];
			$job_array[$vals[csf("id")]]["po_qnty"]+=$vals[csf("po_qnty")];
			$job_array[$vals[csf("id")]]["item_number_id"]=$garments_item[$vals[csf("item_number_id")]];
			$col_size_id_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("po_qnty")];
			$col_id_arr[$vals[csf("color_number_id")]]+=$vals[csf("po_qnty")];
			$size_id_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
		}
		$counts=count($size_id_arr);


		$production_data="SELECT a.serving_company, a.country_id,a.production_source,a.challan_no,a.floor_id,a.sewing_line,d.color_number_id,d.size_number_id,sum(b.production_qnty) as qnty   from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where a.id=b.mst_id and b.color_size_break_down_id=d.id and a.po_break_down_id=c.id and d.po_break_down_id=c.id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id=$po and a.production_type=$type  and d.color_number_id=$color $date_cond  group by  a.serving_company, a.country_id,a.production_source,a.challan_no,a.floor_id,a.sewing_line,d.color_number_id,d.size_number_id  ";

		$production_data=sql_select($production_data);
		$main_data_arr=array();
		$size_wise_main_data_arr=array();
		foreach($production_data as $vals)
		{
			$main_data_arr[$vals[csf("country_id")]][$vals[csf("production_source")]][$vals[csf("challan_no")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]] +=$vals[csf("qnty")];
			$size_wise_main_data_arr[$vals[csf("country_id")]][$vals[csf("production_source")]][$vals[csf("challan_no")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("qnty")];

			$main_data_arr_fin[$vals[csf("serving_company")]][$vals[csf("color_number_id")]] +=$vals[csf("qnty")];
			$size_wise_main_data_arr_fin[$vals[csf("serving_company")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]] +=$vals[csf("qnty")];

		}

		?>



		<div id="details_reports">
			<div>
				<strong>Buyer Name : <? echo $job_array[$po]["buyer_name"]; ?>&nbsp;&nbsp;Job No: <? echo $job_array[$po]["job_no"]; ?>&nbsp;&nbsp;Style No : <? echo $job_array[$po]["style_ref_no"]; ?>&nbsp;&nbsp;Garments Item : <? echo $job_array[$po]["item_number_id"]; ?>&nbsp;&nbsp;<br>Order No : <? echo $job_array[$po]["po_number"]; ?>&nbsp;&nbsp;Date: <? echo change_date_format(str_replace("'", "", $dates)); ?>&nbsp;&nbsp;</strong>
			</div>
			<br>
			<table width="<? echo 230+($counts*45);?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
				<caption><strong>Summary</strong></caption>
				<thead>
					<tr>
						<th rowspan="2" width="30">SI</th>
						<th rowspan="2" width="100">Color</th>
						<th colspan="<? echo $counts;?>">Size</th>
						<th rowspan="2" width="100">Total</th>
					</tr>
					<tr>
						<?
						foreach($size_id_arr as $vals)
						{
							?>
							<th width="45"><? echo $sizearr[$vals]; ?></th>


							<?
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?
					$p=1;
					$size_wise_vertical_arr =array();

					foreach($col_id_arr as $col_id=>$size_val)
					{
								//foreach($size_val as $vals)
								//{
									//?>
									<tr>
										<td align="center" width="30"><?echo $p++; ?></td>
										<td align="center" width="100"><?echo $colorarr[$col_id]; ?></td>
										<?
										$total=0;
										foreach($size_id_arr as $vals)
										{
											?>
											<th width="45"><? echo $tot= $col_size_id_arr[$col_id][$vals]; ?></th>


											<?
											$total+=$tot;
											$size_wise_vertical_arr[$vals]+=$tot;
										}
										?>

										<td align="center" width="100"><?echo $total; ?></td>
									</tr>


									<?

								//}

					}
								?>
								<tr>
									<td colspan="2" align="right">&nbsp;</td>
									<?
									$total=0;
									foreach($size_id_arr as $vals)
									{
										?>
										<th width="45"><? echo $tot=$size_wise_vertical_arr[$vals]; ?></th>


										<?
										$total+=$tot;
										$size_wise_vertical_arr[$vals]+=$tot;
									}
									?>

									<td align="center" width="100"><?echo $total; ?></td>
								</tr>




				</tbody>
		    </table>
					<?
					if($type!=8)
					{
						?>
						<div>
							<table width="<? echo 630+($counts*45);?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
								<caption><strong>Details</strong></caption>
								<thead>
									<tr>
										<th width="30" rowspan="2">SI</th>
										<th width="80" rowspan="2">Country Name</th>
										<th width="50" rowspan="2">Source</th>
										<th width="70" rowspan="2">Challan</th>
										<th width="100" rowspan="2">Sewing Unit</th>
										<th width="100" rowspan="2">Sewing Unit</th>
										<th width="100" rowspan="2">Sewing Line</th>
										<th width="100" rowspan="2">Color</th>
										<th colspan="<? echo $counts;?>">Size</th>
										<th width="100" rowspan="2">Total</th>
									</tr>
									<tr>
										<?
										foreach($size_id_arr as $vals)
										{
											?>
											<th width="45"><? echo $sizearr[$vals]; ?></th>


											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="max-height:300px;  ">
								<table id="table_body"   width="<? echo 630+($counts*45);?>" border="1" rules="all" class="rpt_table"   >

									<tbody>
										<?
										$p=1;
										$size_wise_vertical_arr=array();

										foreach($main_data_arr as $c_id=>$source_data)
										{
											foreach($source_data as $s_id=>$challan_data)
											{
												foreach($challan_data as $ch_id=>$floor_data)
												{
													foreach($floor_data as $f_id=>$line_data)
													{
														foreach($line_data as $l_id=>$col_data)
														{
															foreach($col_data as $color_id=>$vals)
															{
																?>
																<tr>
																	<td align="center" width="30"><?echo $p++; ?></td>
																	<td align="center" width="80"><?echo $countryarr[$c_id]; ?></td>
																	<td align="center" width="50"><?echo $knitting_source[$s_id]; ?></td>
																	<td align="center" width="70"><?echo $ch_id; ?></td>
																	<td align="center" width="100"><?echo $floorarr[$f_id]; ?></td>
																	<td align="center" width="100">
																		<?
																		$lines=explode(",", $resourcearr[$l_id]);
																		$line_names="";
																		foreach($lines as $v)
																		{
																			$line_names.=($line_names)? " , $linearr[$v]" : $linearr[$v];
																		}
																		echo $line_names;
																		?>

																	</td>
																	<td align="center" width="100"><?echo $colorarr[$color_id]; ?></td>
																	<?
																	$total=0;
																	foreach($size_id_arr as $vals)
																	{
																		?>
																		<td width="45" align="center"><? echo $tot=$size_wise_main_data_arr[$c_id][$s_id][$ch_id][$f_id][$l_id][$color_id][$vals]; ?></th>


																			<?
																			$total+=$tot;
																			$size_wise_vertical_arr[$vals]+=$tot;
																		}
																		?>


																		<td align="center" width="100"><?echo $total; ?></td>

																	</tr>


																	<?

																}

															}

														}
													}
												}

											}
											?>




										</tbody>


									</table>
									<table  width="<? echo 630+($counts*45);?>" border="1" rules="all" class="rpt_table">
										<tr>
											<td align="center" width="30"> </td>
											<td align="center" width="80"> </td>
											<td align="center" width="50"> </td>
											<td align="center" width="70"> </td>
											<td align="center" width="100"> </td>
											<td align="center" width="100">		</td>
											<td align="center" width="100"><strong>Grand Total</strong></td>
											<?
											$total=0;
											$index=7;
											$id_arr=array();
											$index_array=array();
											$operation=array();
											$write_method=array();
											$kk=0;
											foreach($size_id_arr as $vals)
											{
												$id_arr[$kk]="size".$vals;
												$index_array[$kk]=$index;
												$operation[$kk]="sum";
												$write_method[$kk]="innerHTML";

												?>
												<td align="center" id="<? echo 'size'.$vals;?>" width="45"></td>


												<?
												$total+=$tot;
												$size_wise_vertical_arr[$vals]+=$tot;
												$kk++;
												$index++;
											}
											$id_arr[$kk]="all_total";
											$index_array[$kk]=$index;
											$operation[$kk]="sum";
											$write_method[$kk]="innerHTML";
											$id_arr=json_encode($id_arr);
											$index_array=json_encode($index_array);
											$operation=json_encode($operation);
											$write_method=json_encode($write_method);
											?>

											<td  id="all_total" align="center" width="100"></td>

										</tr>

									</table>
								</div>


								 <script type="text/javascript">
								 	var id_arr='<? echo $id_arr;?>';
								 	var id_arr=JSON.parse(id_arr);

								 	var index_array='<? echo $index_array;?>';
								 	var index_array=JSON.parse(index_array);

								 	var operation='<? echo $operation;?>';
								 	var operation=JSON.parse(operation);

								 	var write_method='<? echo $write_method;?>';
								 	var write_method=JSON.parse(write_method);
								 	//alert(id_arr+index_array+operation);
								 	var tableFilters1 =
								 	{

								 		col_operation: {
								 			id: id_arr ,
								 			col: index_array,
								 			operation: operation,
								 			write_method: write_method
								 		}
								 	}
								 	setFilterGrid("table_body",-1,tableFilters1);
								 </script>


							</div>

						</div>


						<?

					}
					else if($type==8)
					{
						?>

						<div>
							<table    width="<? echo 430+($counts*45);?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"  rules="all">
								<caption><strong>Details</strong></caption>
								<thead>
									<tr>
										<th width="30" rowspan="2">SI</th>
										<th width="100" rowspan="2">Working Company</th>
										<th width="100" rowspan="2">Color</th>
										<th colspan="<? echo $counts;?>">Size</th>
										<th width="100" rowspan="2">Total</th>
									</tr>
									<tr>
										<?
										foreach($size_id_arr as $vals)
										{
											?>
											<th width="45"><? echo $sizearr[$vals]; ?></th>


											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="max-height:300px;  ">
								<table id="table_body"   width="<? echo 430+($counts*45);?>" border="1" rules="all" class="rpt_table" >

									<tbody>
										<?
										$p=1;
										$size_wise_vertical_arr=array();

										foreach($main_data_arr_fin as $c_id=>$color_data)
										{
											foreach($color_data as $col_id=>$vals)
											{
												?>
												<tr>
													<td align="center" width="30"><?echo $p++; ?></td>
													<td align="center" width="100"><?echo $companyarr[$c_id]; ?></td>
													<td align="center" width="100"><?echo $colorarr[$col_id]; ?></td>



													<?
													$total=0;
													foreach($size_id_arr as $vals)
													{
														?>
														<td width="45" align="center"><? echo $tot=$size_wise_main_data_arr_fin[$c_id][$col_id][$vals]; ?></th>


															<?
															$total+=$tot;
															$size_wise_vertical_arr[$vals]+=$tot;
														}
														?>


														<td align="center" width="100"><?echo $total; ?></td>

													</tr>


													<?

												}

											}
											?>




										</tbody>


									</table>
								</div>
								<table   width="<? echo 430+($counts*45);?>" border="1" rules="all" class="rpt_table" >
								 <tfoot>
									<tr>
										<td align="center" width="30"> </td>
										<td align="center" width="100"> </td>
										<td align="center" width="100"><strong>Grand Totals</strong></td>

										<?
										$total=0;
										$index=3;
										$id_arr=array();
										$index_array=array();
										$operation=array();
										$write_method=array();
										$kk=0;
										foreach($size_id_arr as $vals)
										{
											$id_arr[$kk]="size".$vals;
											$index_array[$kk]=$index;
											$operation[$kk]="sum";
											$write_method[$kk]="innerHTML";
											?>
											<td align="center"  id="<? echo 'size'.$vals;?>" width="45"></td>


											<?
											$total+=$tot;
											$size_wise_vertical_arr[$vals]+=$tot;
											$kk++;
											$index++;
										}
										$id_arr[$kk]="all_total";
										$index_array[$kk]=$index;
										$operation[$kk]="sum";
										$write_method[$kk]="innerHTML";
										$id_arr=json_encode($id_arr);
										$index_array=json_encode($index_array);
										$operation=json_encode($operation);
										$write_method=json_encode($write_method);
										?>

										<td  id="all_total" align="center" width="100"> </td>
									</tr>
								 </tfoot>
								</table>
								 <script type="text/javascript">
								 	var id_arr='<? echo $id_arr;?>';
								 	var id_arr=JSON.parse(id_arr);

								 	var index_array='<? echo $index_array;?>';
								 	var index_array=JSON.parse(index_array);

								 	var operation='<? echo $operation;?>';
								 	var operation=JSON.parse(operation);

								 	var write_method='<? echo $write_method;?>';
								 	var write_method=JSON.parse(write_method);
								 	//alert(id_arr+index_array+operation+write_method);
								 	var tableFilters1 =
								 	{

								 		col_operation: {
								 			id: id_arr,
								 			col: index_array,
								 			operation: operation,
								 			write_method: write_method
								 		}
								 	}
								 	setFilterGrid("table_body",-1,tableFilters1);
								 </script>


							</div>

						</div>


						<?

					}
					?>


		</div>
		<script>   //setFilterGrid("table_body",-1);  </script>




				<?

	}
	else if( ($type==2 && $day=='B') || ($type==3 && $day=='B'))
	{
		$order_data="SELECT e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po  and d.color_number_id=$color  group by  e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date ";
		if($type)
		{

			$production_data="SELECT a.floor_id,a.sewing_line, a.production_source, a.production_date,a.serving_company,a.location,sum(case when production_source=1 then b.production_qnty else 0 end ) as inhouse ,sum(case when production_source=3 then b.production_qnty else 0 end ) as outbound   from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where a.id=b.mst_id and b.color_size_break_down_id=d.id and a.po_break_down_id=c.id and d.po_break_down_id=c.id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id=$po and a.production_type=$type  $wo_company_cond and d.color_number_id=$color  group by   a.floor_id,a.sewing_line,a.production_source,a.production_date,a.serving_company,a.location ";

		}

		else if($type==0)
		{
			$production_data="SELECT a.entry_date as production_date,a.location_id as location,a.working_company_id as serving_company,  sum(c.size_qty) as inhouse, 1 as production_source from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c  where  a.id=b.mst_id and b.id=c.dtls_id     and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active =1 and c.is_deleted=0  and c.order_id=$po    and b.color_id=$color $wo_company_cond group by a.entry_date ,a.location_id ,a.working_company_id ";
		}
		$production_data=sql_select($production_data);


		?>
			<div id="details_reports">
				<table width="700" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
					<thead>
						<tr>
							<th width="100">Buyer</th>
							<th width="100">Job Number</th>
							<th width="100">Style Name</th>
							<th width="100">Order Number</th>
							<th width="100">Ship Date</th>
							<th width="100">Item Name</th>
							<th width="80">Order Qty.</th>
						</tr>
					</thead>
					<tbody>
						<?

						foreach(sql_select($order_data) as $vals)
						{
							?>
							<tr>
								<td align="center" width="100"><?echo $buyerarr[$vals[csf("buyer_name")]]; ?></td>
								<td align="center" width="100"><?echo $vals[csf("job_no")]; ?></td>
								<td align="center" width="100"><?echo $vals[csf("style_ref_no")]; ?></td>
								<td align="center" width="100"><?echo $vals[csf("po_number")]; ?></td>
								<td align="center" width="100"><?echo $vals[csf("pub_shipment_date")]; ?></td>
								<td align="center" width="100"><?echo $garments_item[$vals[csf("item_number_id")]]; ?></td>
								<td align="center" width="80"><?echo $vals[csf("po_qnty")]; ?></td>
							</tr>
							<?


					    }



						//}
						if($type==5 || $type==4)$tbl_wid=700;else $tbl_wid=500;



						?>
					</tbody>
				</table>
				<br>
				<br>


				    <table style="margin-top: 10px;" width="<? echo $tbl_wid;?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
							<thead>
								<tr>
									<th  width="30" rowspan="2">SI</th>
									<th width="100" rowspan="2">Date</th>

									<th width="100" colspan="2">Emblishment Qnty</th>
									<th width="100" rowspan="2">Working Company</th>

									<th width="100" rowspan="2">Location</th>
								</tr>
								<tr>
									<th width="50">In-house</th>
									<th width="50">Out-bound</th>

								</tr>
							</thead>
					</table>
						<div style="max-height:300px;  ">
							<table id="table_body"  width="<? echo $tbl_wid;?>"  border="1" rules="all" class="rpt_table"  >

								<tbody>
									<?
									$p=1;
									$total_inhouse=0;
									$total_out=0;
									foreach($production_data as $vals)
									{
										?>
										<tr>
											<td align="center" width="30"><?echo $p++; ?></td>
											<td align="center" width="100"><?echo change_date_format($vals[csf("production_date")]); ?></td>

											<?
											if($type==5 || $type==4)
											{
												?>
												<td width="100" align="center"><? echo $floorarr[$vals[csf("floor_id")]]; ?></td>
												<td width="100" align="center"><?  $line= explode(",",  $resourcearr[$vals[csf("sewing_line")]]);
													$lines="";
													foreach($line as $val)
													{

														if($lines=="") $lines=$linearr[$val];
														else  $lines.=','.$linearr[$val];
													}
													echo $lines;
													?></td>

													<?
												}
												$total_inhouse+=$vals[csf("inhouse")];
												$total_out+=$vals[csf("outbound")];
												?>


												<td align="center" width="50"><?echo $vals[csf("inhouse")]; ?></td>
												<td align="center" width="50"><?echo $vals[csf("outbound")]; ?></td>
                                                <?
                                                if($vals[csf("production_source")] == 1){
                                                ?>
												    <td align="center" width="100"><?echo $companyarr[$vals[csf("serving_company")]]; ?></td>
                                                <?
                                                }else{
                                                ?>
                                                    <td align="center" width="100"><?echo $party_library[$vals[csf("serving_company")]]; ?></td>

                                                <?
                                                }
                                                ?>
                                                <td align="center" width="100"><?echo $locationarr[$vals[csf("location")]]; ?></td>

											</tr>
											<?
									}
										?>




								</tbody>

							</table>
							<div>

					<table   width="<? echo $tbl_wid;?>"  border="1" rules="all" class="rpt_table"  >
						<tfoot>
							<tr>


								<?
								if($type==5 || $type==4)
								{
									?>
									<td width="30">&nbsp;</td>
									<td width="100" >&nbsp;</td>
									<td width="100" >&nbsp;</td>
									<td width="100"   align="right"><strong>Grand Total</strong></td>



									<?
								}
								else
								{
									?>
									<td width="30">&nbsp;</td>
									<td width="100"   align="right"><strong>Grand Total</strong></td>

									<?
								}
								?>


								<td id="ttl_inhouse" align="center" width="50"><?//echo $total_inhouse; ?></td>
								<td id="ttl_outbound" align="center" width="50"><?//echo $total_out; ?></td>
								<td width="100" >&nbsp;</td>
								<td width="100" >&nbsp;</td>

							</tr>

						</tfoot>



					</table>
			</div>

			</div>



					<script type="text/javascript">
 								var tableFilters1 =
								{

									col_operation: {
										id: ["ttl_inhouse","ttl_outbound"],
										col: [2,3],
										operation: ["sum","sum"],
										write_method: ["innerHTML","innerHTML"]
									}
								}

								var tableFilters2 =
								{

									col_operation: {
										id: ["ttl_inhouse","ttl_outbound"],
										col: [4,5],
										operation: ["sum","sum"],
										write_method: ["innerHTML","innerHTML"]
									}
								}
								var type='<? echo $type;?>';
								 if(type==4 || type==5)
								 {
								 	setFilterGrid("table_body",-1,tableFilters2);
								 }
								 else
								 {
								 	setFilterGrid("table_body",-1,tableFilters1);
								 }


					</script>



		<?

	}


	else if(($type==2 && $day=='A') || ($type==3 && $day=='A'))
	{
		$order_data="SELECT c.id, e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,d.color_number_id,d.size_number_id, sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po  and d.color_number_id=$color  group by c.id, e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,d.color_number_id,d.size_number_id ";
		$job_array=array();
		foreach(sql_select($order_data) as $vals)
		{
			$job_array[$vals[csf("id")]]["buyer_name"]=$buyerarr[$vals[csf("buyer_name")]];
			$job_array[$vals[csf("id")]]["job_no"]=$vals[csf("job_no")];
			$job_array[$vals[csf("id")]]["style_ref_no"]=$vals[csf("style_ref_no")];
			$job_array[$vals[csf("id")]]["po_number"]=$vals[csf("po_number")];
			$job_array[$vals[csf("id")]]["pub_shipment_date"]=$vals[csf("pub_shipment_date")];
			$job_array[$vals[csf("id")]]["po_qnty"]+=$vals[csf("po_qnty")];
			$job_array[$vals[csf("id")]]["item_number_id"]=$garments_item[$vals[csf("item_number_id")]];
			$col_size_id_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("po_qnty")];
			$col_id_arr[$vals[csf("color_number_id")]]+=$vals[csf("po_qnty")];
			$size_id_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
		}
		$counts=count($size_id_arr);


		$production_data="SELECT a.serving_company,a.location, a.country_id,a.production_source,a.challan_no,a.floor_id,a.sewing_line,d.color_number_id,d.size_number_id,sum(b.production_qnty) as qnty
		from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e
		where a.id=b.mst_id and b.color_size_break_down_id=d.id and a.po_break_down_id=c.id and d.po_break_down_id=c.id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id=$po and a.production_type=$type  $wo_company_cond and d.color_number_id=$color $date_cond
		group by  a.location,a.serving_company, a.country_id,a.production_source,a.challan_no,a.floor_id,a.sewing_line,d.color_number_id,d.size_number_id  ";

		$production_data=sql_select($production_data);
		$main_data_arr=array();
		$size_wise_main_data_arr=array();
		foreach($production_data as $vals)
		{
			$main_data_arr[$vals[csf("country_id")]][$vals[csf("production_source")]][$vals[csf("challan_no")]][$vals[csf("floor_id")]][$vals[csf("location")]][$vals[csf("color_number_id")]] +=$vals[csf("qnty")];
			$size_wise_main_data_arr[$vals[csf("country_id")]][$vals[csf("production_source")]][$vals[csf("challan_no")]][$vals[csf("floor_id")]][$vals[csf("location")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("qnty")];

			$main_data_arr_fin[$vals[csf("serving_company")]][$vals[csf("color_number_id")]] +=$vals[csf("qnty")];
			$size_wise_main_data_arr_fin[$vals[csf("serving_company")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]] +=$vals[csf("qnty")];

		}

		?>



		<div id="details_reports">
			<div>
				<strong>Buyer Name : <? echo $job_array[$po]["buyer_name"]; ?>&nbsp;&nbsp;Job No: <? echo $job_array[$po]["job_no"]; ?>&nbsp;&nbsp;Style No : <? echo $job_array[$po]["style_ref_no"]; ?>&nbsp;&nbsp;Garments Item : <? echo $job_array[$po]["item_number_id"]; ?>&nbsp;&nbsp;<br>Order No : <? echo $job_array[$po]["po_number"]; ?>&nbsp;&nbsp;Date: <? echo change_date_format(str_replace("'", "", $dates)); ?>&nbsp;&nbsp;</strong>
			</div>
			<br>
			<table width="<? echo 230+($counts*45);?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
				<caption><strong>Summary</strong></caption>
				<thead>
					<tr>
						<th rowspan="2" width="30">SI</th>
						<th rowspan="2" width="100">Color</th>
						<th colspan="<? echo $counts;?>">Size</th>
						<th rowspan="2" width="100">Total</th>
					</tr>
					<tr>
						<?
						foreach($size_id_arr as $vals)
						{
							?>
							<th width="45"><? echo $sizearr[$vals]; ?></th>


							<?
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?
					$p=1;
					$size_wise_vertical_arr =array();

					foreach($col_id_arr as $col_id=>$size_val)
					{
								//foreach($size_val as $vals)
								//{
									//?>
									<tr>
										<td align="center" width="30"><?echo $p++; ?></td>
										<td align="center" width="100"><?echo $colorarr[$col_id]; ?></td>
										<?
										$total=0;
										foreach($size_id_arr as $vals)
										{
											?>
											<th width="45"><? echo $tot= $col_size_id_arr[$col_id][$vals]; ?></th>
											<?
											$total+=$tot;
											$size_wise_vertical_arr[$vals]+=$tot;
										}
										?>

										<td align="center" width="100"><? echo $total; ?></td>
									</tr>


									<?

								//}

					}
					?>
					<tr>
						<td colspan="2" align="right">&nbsp;</td>
						<?
						$total=0;
						foreach($size_id_arr as $vals)
						{
							?>
							<th width="45"><? echo $tot=$size_wise_vertical_arr[$vals]; ?></th>
							<?
							$total+=$tot;
							$size_wise_vertical_arr[$vals]+=$tot;
						}
						?>
						<td align="center" width="100"><?echo $total; ?></td>
					</tr>
				</tbody>
		    </table>

						<div>
							<table width="<? echo 640+($counts*45);?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
								<caption><strong>Details</strong></caption>
								<thead>
									<tr>
										<th style="word-wrap: break-word;word-break: break-all;" width="20" rowspan="2">SI</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="120" rowspan="2">Country Name</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="100" rowspan="2">Source</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="100" rowspan="2">Challan</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="100" rowspan="2">Location</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="100" rowspan="2">Color</th>
										<th style="word-wrap: break-word;word-break: break-all;" colspan="<? echo $counts;?>">Size</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="100" rowspan="2">Total</th>
									</tr>
									<tr>
										<?
										foreach($size_id_arr as $vals)
										{
											?>
											<th width="45"><? echo $sizearr[$vals]; ?></th>


											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="max-height:300px;  ">
								<table id="table_body"   width="<? echo 640+($counts*45);?>" border="1" rules="all" class="rpt_table"   >

									<tbody>
										<?
										$p=1;
										$size_wise_vertical_arr=array();

										foreach($main_data_arr as $c_id=>$source_data)
										{
											foreach($source_data as $s_id=>$challan_data)
											{
												foreach($challan_data as $ch_id=>$floor_data)
												{
													foreach($floor_data as $f_id=>$location_data)
													{
														foreach($location_data as $l_id=>$col_data)
														{
															foreach($col_data as $color_id=>$vals)
															{
																?>
																<tr>
																	<td style="word-wrap: break-word;word-break: break-all;" align="center" width="20"><?echo $p++; ?></td>
																	<td style="word-wrap: break-word;word-break: break-all;" align="center" width="120"><?echo $countryarr[$c_id]; ?></td>
																	<td style="word-wrap: break-word;word-break: break-all;" align="center" width="100"><?echo $knitting_source[$s_id]; ?></td>
																	<td style="word-wrap: break-word;word-break: break-all;" align="center" width="100"><?echo $ch_id; ?></td>
																	<td style="word-wrap: break-word;word-break: break-all;" align="center" width="100"><?echo $locationarr[$l_id]; ?></td>

																	<td style="word-wrap: break-word;word-break: break-all;" align="center" width="100"><?echo $colorarr[$color_id]; ?></td>
																	<?
																	$total=0;
																	foreach($size_id_arr as $key => $value)
																	{
																		?>
																		<td style="word-wrap: break-word;word-break: break-all;" width="45" align="center"><? $tot=$size_wise_main_data_arr[$c_id][$s_id][$ch_id][$f_id][$l_id][$color_id][$value]; echo $tot; ?></th>
																		<?
																		$total+=$tot;
																		$size_wise_vertical_arr[$value]+=$tot;
																	}
																		?>
																		<td style="word-wrap: break-word;word-break: break-all;" align="center" width="100"><? echo $total; ?></td>

																	</tr>
																	<?
																}
															}
														}
													}
												}
											}
											?>
										</tbody>
									</table>
									<table  width="<? echo 640+($counts*45);?>" border="1" rules="all" class="rpt_table">
										<tr>
											<td style="word-wrap: break-word;word-break: break-all;" align="center" width="20"> </td>
											<td style="word-wrap: break-word;word-break: break-all;" align="center" width="120"> </td>
											<td style="word-wrap: break-word;word-break: break-all;" align="center" width="100"> </td>
											<td style="word-wrap: break-word;word-break: break-all;" align="center" width="100"> </td>
											<td style="word-wrap: break-word;word-break: break-all;" align="center" width="100"> </td>
											<td style="word-wrap: break-word;word-break: break-all;" align="center" width="100"><strong>Grand Total</strong></td>
											<?
											$total=0;
											$index=7;
											$id_arr=array();
											$index_array=array();
											$operation=array();
											$write_method=array();
											$kk=0;
											foreach($size_id_arr as $vals)
											{
												$id_arr[$kk]="size".$vals;
												$index_array[$kk]=$index;
												$operation[$kk]="sum";
												$write_method[$kk]="innerHTML";

												?>
												<td style="word-wrap: break-word;word-break: break-all;" align="center" id="<? echo 'size'.$vals;?>s" width="45"><? echo $size_wise_vertical_arr[$vals]; ?></td>


												<?
												$total+=$size_wise_vertical_arr[$vals];
												$size_wise_vertical_arr[$vals]+=$tot;
												$kk++;
												$index++;
											}
											$id_arr[$kk]="all_total";
											$index_array[$kk]=$index;
											$operation[$kk]="sum";
											$write_method[$kk]="innerHTML";
											$id_arr=json_encode($id_arr);
											$index_array=json_encode($index_array);
											$operation=json_encode($operation);
											$write_method=json_encode($write_method);
											?>

											<td style="word-wrap: break-word;word-break: break-all;"  id="all_totals" align="center" width="100"><? echo $total; ?></td>

										</tr>

									</table>
								</div>


								 <script type="text/javascript">
								 	var id_arr='<? echo $id_arr;?>';
								 	var id_arr=JSON.parse(id_arr);

								 	var index_array='<? echo $index_array;?>';
								 	var index_array=JSON.parse(index_array);

								 	var operation='<? echo $operation;?>';
								 	var operation=JSON.parse(operation);

								 	var write_method='<? echo $write_method;?>';
								 	var write_method=JSON.parse(write_method);
								 	//alert(id_arr+index_array+operation);
								 	var tableFilters1 =
								 	{

								 		col_operation: {
								 			id: id_arr ,
								 			col: index_array,
								 			operation: operation,
								 			write_method: write_method
								 		}
								 	}
								 	setFilterGrid("table_body",-1,tableFilters1);
								 </script>


							</div>

						</div>


						<?



					?>


		</div>
		<script>   //setFilterGrid("table_body",-1);  </script>




				<?

	}

 	?>

  	  <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
  	  </body>
   	 </html>

    <?
	exit();
}

?>