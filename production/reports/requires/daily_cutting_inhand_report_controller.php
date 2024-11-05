<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$colorname_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from   lib_country", "id", "country_name");
$country_code_arr=return_library_array( "select id, short_name from   lib_country", "id", "short_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

// ================================Print button ==============================

if($action=="print_button_variable_setting")
{

    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=134 and is_deleted=0 and status_active=1");
	$printButton=explode(',',$print_report_format);

	 foreach($printButton as $id){
		if($id==260)$buttonHtml.='<input type="button" id="show_button_po" class="formbutton" style="width:60px" value="PO Wise" onClick="generate_report(1)" />';
		if($id==243)$buttonHtml.='<input type="button" id="show_button_Item_Wise" class="formbutton" style="width:60px" value="Item Wise" onClick="generate_report(2)" />';
		if($id==708)$buttonHtml.='<input type="button" id="show_button_Item_Wise2" class="formbutton" style="width:70px" value="Item Wise2" onClick="generate_report(3)" />';
		if($id==709)$buttonHtml.='<input type="button" id="show_buttonItem_Wise3" class="formbutton" style="width:70px" value="Item Wise3" onClick="generate_report(4)" />';
	 }
	 echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";

    exit();
}
// ======================= End Print button =================================================

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();
}

if ($action=="load_drop_down_location")
{
	$data=str_replace("'", "", $data);
	echo create_drop_down( "cbo_location_name", 120, "SELECT id,location_name from lib_location where company_id in($data) and  status_active =1 and is_deleted=0  group by id,location_name  order by location_name","id,location_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/daily_cutting_inhand_report_controller',this.value, 'load_drop_down_floor', 'floor_td' )" );

	exit();
}

if ($action=="load_drop_down_floor")  //document.getElementById('cbo_floor').value
{
	echo create_drop_down( "cbo_floor_name", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 );
	exit();
}

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";

if($action=="job_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
	// $report_type=$data[3];
	// print_r($data);
	//echo $batch_type."AAZZZ";
	?>
	<script type="text/javascript">
	  function js_set_value(id)
		  {
			//alert(id);
			document.getElementById('selected_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
	<input type="hidden" id="selected_id" name="selected_id" />
	<?

	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and b.job_no_mst='".$job_no."'";
	if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
	$job_year_cond="";
	if($cbo_year!=0)
	{
	if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
    if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	if($db_type==0) $year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";

	if($db_type==2) $group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
	else if($db_type==0) $group_field="group_concat(distinct b.po_number ) as po_number";

	$sql="select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num as job_prefix,$year_field,$group_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company $buyer_name $year_cond $job_cond and a.is_deleted=0 group by  a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,a.insert_date ";


	//$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	?>
	<table width="500" border="1" rules="all" class="rpt_table">
		<thead>
	        <tr>
	            <th width="30">SL</th>
	             <th width="40">Year</th>
	             <th width="50">Job no</th>
	            <th width="100">Style</th>
	            <th width="">Po number</th>

	        </tr>
	   </thead>
	</table>
	<div style="max-height:300px; overflow:auto;">
	<table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
	 <? $rows=sql_select($sql);
		 $i=1;
		 foreach($rows as $data)
		 {
			 	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
	  ?>
		<tr bgcolor="<? echo  $bgcolor;?>" onclick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
			<td width="30"><? echo $i; ?></td>
	        <td align="center" width="40"><p><? echo $data[csf('year')]; ?></p></td>
			<td align="center"  width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
			<td width="100"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
	        <td width=""><p><? echo $po_num; ?></p></td>

		</tr>
	    <? $i++; } ?>
	</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	disconnect($con);
	exit();
}//JobNumberShow


if($action=="style_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
	// $report_type=$data[3];
	// print_r($data);
	//echo $batch_type."AAZZZ";
	?>
	<script type="text/javascript">
	  function js_set_value(id)
		  {
			//alert(id);
			document.getElementById('selected_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
	<input type="hidden" id="selected_id" name="selected_id" />
	<?
	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
	    else  if (str_replace("'","",$job_no)!="") $job_cond="and b.job_no_mst='".$job_no."'";
		if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
		$job_year_cond="";
		if($cbo_year!=0)
		{
		if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
	    if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
		}
		if($db_type==0) $year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";

		if($db_type==2) $group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
		else if($db_type==0) $group_field="group_concat(distinct b.po_number ) as po_number";

		$sql="select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num as job_prefix,$year_field,$group_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company $buyer_name $year_cond $job_cond and a.is_deleted=0 group by  a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,a.insert_date ";


	//$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	?>
	<table width="500" border="1" rules="all" class="rpt_table">
		<thead>
	        <tr>
	            <th width="30">SL</th>
	             <th width="40">Year</th>
	             <th width="50">Job no</th>
	            <th width="100">Style</th>
	            <th width="">Po number</th>

	        </tr>
	   </thead>
	</table>
	<div style="max-height:300px; overflow:auto;">
	<table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
	 <? $rows=sql_select($sql);
		 $i=1;
		 foreach($rows as $data)
		 {
			 	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
	  ?>
		<tr bgcolor="<? echo  $bgcolor;?>" onclick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('style_ref_no')]; ?>')" style="cursor:pointer;">
			<td width="30"><? echo $i; ?></td>
	        <td align="center" width="40"><p><? echo $data[csf('year')]; ?></p></td>
			<td align="center"  width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
			<td width="100"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
	        <td width=""><p><? echo $po_num; ?></p></td>

		</tr>
	    <? $i++; } ?>
	</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	disconnect($con);
	exit();
}//JobNumberShow



//order wise browse------------------------------//
if($action=="order_wise_search")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>

		var selected_id = new Array;
		var selected_name = new Array;

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
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );

				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
		}
    </script>
	<?
	extract($_REQUEST);
	//echo $job_no;die;
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$job_cond='';
	if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and a.job_no_mst='".$job_no."'";
	else if($cbo_year!=0)
	{
	if($db_type==0) $job_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
    if($db_type==2) $job_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}

	if(str_replace("'","",$style_id)!="")  $style_cond="and b.id in(".str_replace("'","",$style_id).")";
    else  if (str_replace("'","",$style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no='".$style_no."'";
	$sql = "select distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,$insert_year as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name $job_cond  $buyer_name $style_cond";
	//echo $sql;die;
	echo create_list_view("list_view", "Year,Job No,Style Ref,Order Number","50,100,120,150,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}


if($action=="generate_report")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
	$rpt_type=str_replace("'","",$type);
	$job_cond_id="";
	$style_cond="";
	$order_cond="";
   	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=".str_replace("'","",$cbo_company_name)."";
	if(str_replace("'","",$cbo_buyer_name)==0)  $buyer_name=""; else $buyer_name="and b.buyer_name=".str_replace("'","",$cbo_buyer_name)."";
	if(str_replace("'","",$cbo_location_name)==0)  $location_name=""; else $location_name="and e.location=".str_replace("'","",$cbo_location_name)."";
	if(str_replace("'","",$cbo_floor_name)==0)  $floor_name=""; else $floor_name="and e.floor_id=".str_replace("'","",$cbo_floor_name)."";
	$job_year_cond="";
	if(str_replace("'","",$cbo_year)!=0)
	{
		if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
    	if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	else // this rules applied for only libas. update date 27-04-2019
	{
		if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)>=2018";
    	if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)>=2018";
	}

	if(str_replace("'","",$hidden_job_id)!="") { $job_cond_id="and b.id in(".str_replace("'","",$hidden_job_id).")";}
	else if (str_replace("'","",$txt_job_no)!="") { $job_cond_id=" and b.job_no_prefix_num=".str_replace("'","",$txt_job_no)." $job_year_cond  "; }
	else  $job_cond_id=" $job_year_cond  ";
	if (str_replace("'", "", $hidden_style_id) != "")  $style_cond = "and a.id in(" . str_replace("'", "", $hidden_style_id) . ")";
	else  if (str_replace("'", "", $txt_style_no) == "") $style_cond = "";
	else $style_cond = "and b.style_ref_no like '%" . str_replace("'", "", $txt_style_no) . "%' ";
	if (str_replace("'","",$hidden_order_id)!=""){ $order_cond="and a.id in (".str_replace("'","",$hidden_order_id).")";$job_cond=""; }
	else if (str_replace("'","",$txt_order_no)=="") $order_cond=""; else $order_cond="and a.po_number='".str_replace("'","",$txt_order_no)."'";
	$sql_cond="";$txt_file_no=str_replace("'","",$txt_file_no);$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if ($txt_file_no!="") $sql_cond=" and a.file_no=$txt_file_no";
	if ($txt_int_ref_no!="") $sql_cond.=" and a.grouping='$txt_int_ref_no'";

	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
 	$po_number_data=array();
	$production_data_arr=array();
	$po_number_id=array();

	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $country_ship_date="";
	else $country_ship_date=" and d.country_ship_date between $txt_date_from and $txt_date_to";
	if($db_type==0) { $group_cond="group by d.po_break_down_id,d.color_number_id"; }
	if($db_type==2) { $group_cond="group by a.id,a.job_no_mst,a.po_number, d.po_break_down_id,d.color_number_id,b.buyer_name,b.style_ref_no,
	b.job_no_prefix_num,b.insert_date"; }
	$shipping_cond="";
	if(str_replace("'","",$txt_inhand_type)==2) $shipping_cond=" and a.shiping_status!=3";
	$prod_date_cond = "";
	if(str_replace("'","",$txt_production_date) !=""){$prod_date_cond=" and d.production_date=$txt_production_date";}

	if($rpt_type==1) // PO WISE
	{

		if(str_replace("'","",$txt_production_date) !="")
		{
			$prod_po_arr = array();
			$sql=sql_select( "SELECT po_break_down_id from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date and company_id=$cbo_company_name");
			foreach ($sql as $val)
			{
				$prod_po_arr[$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];
			}

			$sql=sql_select( "SELECT a.po_break_down_id from PRO_EX_FACTORY_MST a, PRO_EX_FACTORY_DELIVERY_MST b where b.id=a.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and a.ex_factory_date=$txt_production_date and b.company_id=$cbo_company_name");
			foreach ($sql as $val)
			{
				$prod_po_arr[$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];
			}

			$sql=sql_select( "SELECT a.po_breakdown_id from order_wise_pro_details a,inv_transaction b where a.trans_id = b.id
			and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0 and b.transaction_date=$txt_production_date and b.company_id=$cbo_company_name");
			foreach ($sql as $val)
			{
				$prod_po_arr[$val['PO_BREAKDOWN_ID']] = $val['PO_BREAKDOWN_ID'];
			}
			// print_r($prod_po_arr);
			if(count($prod_po_arr)>0)
			{
				$prod_po_cond = where_con_using_array($prod_po_arr,0,"a.id");
			}
			else
			{
				echo "<div style='color:red;text-align:center;font-size:18px;'>Data Not Found!</div>";die;
			}
		}


	      $pro_date_sql=sql_select ("SELECT  a.id,a.job_no_mst,a.po_number,
		  d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,d.item_number_id as gmt_id,b.buyer_name,b.style_ref_no as style,b.job_no_prefix_num,$insert_year
		  as year ,d.color_number_id, a.file_no, a.grouping as int_ref_no, e.location,e.floor_id
		  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d,pro_garments_production_mst e, pro_garments_production_dtls f
		  where a.job_id=b.id and a.id=d.po_break_down_id and  a.job_id=d.job_id and a.id=e.po_break_down_id and d.po_break_down_id=e.po_break_down_id and e.id=f.mst_id and a.is_deleted=0 and a.status_active=1 and
		  b.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and
		  b.status_active=1 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $company_name $location_name $floor_name $buyer_name $style_cond $order_cond $sql_cond $job_cond_id $country_ship_date $shipping_cond $prod_po_cond
		  order by  b.buyer_name,a.job_no_mst");

		  foreach($pro_date_sql as $row)
		  {
			  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['id']=$row[csf('id')];
			  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no_mst')];
			  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
			  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['gmt_id']=$row[csf('gmt_id')];
			  // $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['po_quantity']+=$row[csf('order_qty')];
			  // $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['plan_qty']+=$row[csf('plan_qty')];
			  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['buyer_name']=$row[csf('buyer_name')];
			  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['style']=$row[csf('style')];
			  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['job_prifix']=$row[csf('job_no_prefix_num')];
			  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['year']=$row[csf('year')];
			  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['color_id']=$row[csf('color_number_id')];
			  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['file_no']=$row[csf('file_no')];
			  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['int_ref_no']=$row[csf('int_ref_no')];
			  $po_number_id[]=$row[csf('id')];
		  }
		  unset($pro_date_sql);
		//   print_r($po_number_data);
		  // ======================== FOR ORDER QUANTITY ====================================
		  $pro_qty_sql=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,
		  d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,d.item_number_id as gmt_id,b.buyer_name,b.style_ref_no as style,b.job_no_prefix_num,$insert_year
		  as year ,d.color_number_id, a.file_no, a.grouping as int_ref_no
		  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
		  where a.job_id=b.id and a.id=d.po_break_down_id and  a.job_id=d.job_id and a.is_deleted=0 and a.status_active=1 and
		  b.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and
		  b.status_active=1 $company_name $buyer_name $style_cond $order_cond $sql_cond $job_cond_id $country_ship_date $shipping_cond
		  order by  b.buyer_name,a.job_no_mst");
		  $job_qty_data = [];
		  foreach($pro_qty_sql as $row)
		  {
			  $job_qty_data[$row[csf('id')]][$row[csf('color_number_id')]]['po_quantity']+=$row[csf('order_qty')];
			  $job_qty_data[$row[csf('id')]][$row[csf('color_number_id')]]['plan_qty']+=$row[csf('plan_qty')];
		  }

		  //print_r($po_number_id);die;
		  $po_number_id=implode(",",array_unique($po_number_id));
		  if($po_number_id=="") $po_number_id=0;
		  $allqty_sql=sql_select ("SELECT  d.po_break_down_id,sum(d.order_quantity) as order_qty,sum(d.plan_cut_qnty) as plan_qty,
		  min(d.country_ship_date) as minimum_shipdate,d.color_number_id from  wo_po_color_size_breakdown d
		  where   d.status_active=1 and d.is_deleted=0  in (".str_replace("'","",$po_number_id).") group by d.po_break_down_id,d.color_number_id");


		  $po_min_shipdate_data=array();

		  foreach($allqty_sql as $inv)
		  {
			// echo "abc";
			 // $po_number_data[$inv[csf('po_break_down_id')]][$inv[csf('color_number_id')]]['po_quantity']+=$inv[csf('order_qty')];
			 // $po_number_data[$inv[csf('po_break_down_id')]][$inv[csf('color_number_id')]]['plan_qty']+=$inv[csf('plan_qty')];
			  $po_min_shipdate_data[$inv[csf('po_break_down_id')]][$inv[csf('color_number_id')]]['minimum_shipdate']=$inv[csf('minimum_shipdate')];

		  }



		  unset($allqty_sql);
		  //print_r($po_number_data);
		   $sew_line_arr=array();
		  /*  if($db_type==0)
		   {
		   		$sql_line=sql_select("select group_concat(distinct a.sewing_line) as line_id,a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id");
		   }
		   if($db_type==2)
		   {
		   		$sql_line=sql_select("select listagg(cast(a.sewing_line as varchar2(2000)),',') within group (order by a.sewing_line) as line_id,a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 and a.po_break_down_id in (".str_replace("'","",$po_number_id).") group by a.po_break_down_id");
		   }
		   foreach($sql_line as $row_sew)
		   {
		   		$sew_line_arr[$row_sew[csf('po_break_down_id')]]['line']= implode(',',array_unique(explode(',',$row_sew[csf('line_id')])));
		   }

		   unset($sql_line); */




		  $production_mst_sql=sql_select("select a.po_break_down_id,c.color_number_id, a.sewing_line,b.production_type,
		   sum(CASE WHEN b.production_type =1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cutting_qnty,
		   sum(CASE WHEN b.production_type =1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cutting_qnty_pre,
		   sum(CASE WHEN b.production_type =2  and a.embel_name=4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS sp_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printing_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printing_qnty_pre ,
		   sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printreceived_qnty,
		   sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printreceived_qnty_pre,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS embl_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS embl_qnty_pre ,
		   sum(CASE WHEN b.production_type =3  and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS emblreceived_qnty,
		   sum(CASE WHEN b.production_type =3  and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS emblreceived_qnty_pre,
		   min(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<=".$txt_production_date." THEN a.production_date end)
		   AS min_printin_date,
		   min(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<=".$txt_production_date." THEN a.production_date end)
		   as min_embl_date,
		   sum(CASE WHEN b.production_type =4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty,
		   sum(CASE WHEN b.production_type =4 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty_pre,
		   sum(CASE WHEN b.production_type =9 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty,
		   sum(CASE WHEN b.production_type =9 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty_pre
		   from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c where a.id=b.mst_id
		   and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0
		   and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in (".str_replace("'","",$po_number_id).")  group by a.po_break_down_id,c.color_number_id,a.sewing_line,b.production_type");
		//    echo $production_mst_sql; die;



			   //and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
	       foreach($production_mst_sql as $val)
		   {
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['cutting_qnty']+=$val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['cutting_qnty_pre']+=$val[csf('cutting_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['printing_qnty']+=$val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['printreceived_qnty']+=$val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['printing_qnty_pre']+=$val[csf('printing_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['printreceived_qnty_pre']+=$val[csf('printreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['emblreceived_qnty_pre']+=$val[csf('emblreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['emblreceived_qnty']+=$val[csf('emblreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['embl_qnty_pre']+=$val[csf('embl_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['embl_qnty']+=$val[csf('embl_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['min_printin_date']+=$val[csf('min_printin_date')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['min_embl_date']+=$val[csf('min_embl_date')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['sewingin_qnty']+=$val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['sewingin_qnty_pre']+=$val[csf('sewingin_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['cut_delivery_qnty']+=$val[csf('cut_delivery_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['cut_delivery_qnty_pre']+=$val[csf('cut_delivery_qnty_pre')];
				$po_number_gmt[]=$val[csf('po_break_down_id')];

				$total_sewing = $val[csf('sewingin_qnty_pre')]+ $val[csf('sewingin_qnty')];
				if ($val['PRODUCTION_TYPE']==4 && $total_sewing>0) 
				{
					foreach (explode(',',$val[csf('sewing_line')]) as   $line) 
					{ 
						$sew_line_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['line'] [$line]=  $line;  
					}
				}
		    }
			// echo "<pre>";
			// print_r($sew_line_arr);die;
			// unset($production_mst_sql);

			$sql_cutting_delevery=sql_select("select a.po_break_down_id,c.color_number_id,
			sum(CASE WHEN  a.cut_delivery_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty,
		    sum(CASE WHEN  a.cut_delivery_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty_pre
			from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c  where a.id=b.mst_id
		    and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id  and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
		    and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		    group by a.po_break_down_id,c.color_number_id");
		   //and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
			foreach( $sql_cutting_delevery as $inf)
			{
				$production_data_arr[$inf[csf('po_break_down_id')]][$inf[csf('color_number_id')]]['cut_delivery_qnty']=$inf[csf('cut_delivery_qnty')];

				$production_data_arr[$inf[csf('po_break_down_id')]][$inf[csf('color_number_id')]]['cut_delivery_qnty_pre']=$inf[csf('cut_delivery_qnty_pre')];
			}
			unset($sql_cutting_delevery);

	        $sql_fabric_qty=sql_select("SELECT a.po_breakdown_id,a.color_id,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
		    ELSE 0 END ) AS grey_fabric_issue,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =4  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
		    ELSE 0 END ) AS grey_fabric_issue_return,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS finish_fabric_rece,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS finish_fabric_rece_return,
			/* 	sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS fabric_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =2 and b.item_category=2  AND a.entry_form =18 THEN a.quantity
			ELSE 0 END ) AS fabric_qty_pre, */
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS fabric_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =1 and b.item_category=2  AND a.entry_form =37 THEN a.quantity
			ELSE 0 END ) AS fabric_qty_pre,
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END )
			AS trans_in_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_pre,
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END )
			AS trans_out_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_pre
			FROM order_wise_pro_details a,inv_transaction b
		    WHERE a.trans_id = b.id
			and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0  AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).") group by a.po_breakdown_id,a.color_id");
			//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
			$fabric_pre_qty=array();
			$fabric_today_qty=array();
			$total_fabric=array();
			$fabric_balance=array();
			$fabric_wip=array();
			foreach($sql_fabric_qty as $value)
			{
				$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
				$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];

				$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
				-$value[csf("trans_out_pre")];
				$fabric_today_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty")]+$value[csf("trans_in_qty")]
				-$value[csf("trans_out_qty")];

				$po_id_fab[]=$value[csf("po_breakdown_id")];
			}


			unset($sql_fabric_qty);


	 		//*********************************************************************************************************************

		  $color_size_qty_arr=array();
		  $color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty
		  from wo_po_color_size_breakdown
		  where  is_deleted=0  and  status_active=1 and  po_break_down_id in (".str_replace("'","",$po_number_id).")
		  group by po_break_down_id,item_number_id,size_number_id,color_number_id");
		  foreach($color_size_sql as $s_id)
		  {
			$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')];
		  }

		  unset($color_size_sql);


	  	   $sql_sewing=sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum(b.cons) AS conjumction
		   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
		   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".str_replace("'","",$po_number_id).") and b.cons!=0
		   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
		   $con_per_dzn=array();
		   $po_item_qty_arr=array();
		   $color_size_conjumtion=array();
	       foreach($sql_sewing as $row_sew)
	       {
		    $color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);

			$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
			$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
	       }

		   unset($sql_sewing);

		  foreach($color_size_conjumtion as $p_id=>$p_value)
		  {
			 foreach($p_value as $i_id=>$i_value)
			 {
				foreach($i_value as $c_id=>$c_value)
				 {
					 foreach($c_value as $s_id=>$s_value)
					 {
						 foreach($s_value as $b_id=>$b_value)
						 {
						   $order_color_size_qty=$b_value['plan_cut_qty'];
						   $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
						   $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
						   $conjunction_per= ($b_value['conjum']*$order_color_size_qty_per/100);
						   $con_per_dzn[$p_id][$c_id]+=$conjunction_per;

						  // echo $p_id."**".$order_color_size_qty."**".$order_qty."**".$order_color_size_qty_per."**".$b_value['conjum']."<br>";
						 }
					 }
				 }
			 }
		  }
			//print_r($con_per_dzn);die;
		 	//**********************************************************************************************************************


	     $sql_tna_date=sql_select("select a.po_number_id, a.task_start_date,a.task_finish_date	from  tna_process_mst a, lib_tna_task b
		 where b.task_name=a.task_number  and task_name=84");
		 $tna_date_arr=array();
		 foreach($sql_tna_date as $tna_val)
		 {
		 $tna_date_arr[$tna_val[csf('po_number_id')]]['tna_start']= $tna_val[csf('task_start_date')];
		 $tna_date_arr[$tna_val[csf('po_number_id')]]['tna_end']= $tna_val[csf('task_finish_date')];
		 }


		 unset($sql_tna_date);


		 $costing_per_sql=sql_select("select job_no,costing_per from wo_pre_cost_mst");
		 $costing_per_arr=array();
		 foreach($costing_per_sql as $cost_val)
		 {
			$costing_per_arr[$cost_val[csf('job_no')]]=$cost_val[csf('costing_per')];
		 }

		 unset($costing_per_sql);

		 $ready_to_sewing_sql=sql_select("select po_break_down_id, color_id, sewing_qnty as sewing_qnty from ready_to_sewing_dtls");
		 $ready_to_sewing_arr=array();
		 foreach($ready_to_sewing_sql as $row)
		 {
			$ready_to_sewing_arr[$row[csf('po_break_down_id')]][$row[csf('color_id')]]+=$row[csf('sewing_qnty')];

		 }
		 unset($ready_to_sewing_sql);
		 ob_start();
	 		//and po_number_id in (".str_replace("'","",$po_number_id).")
	 		?>
	  		<fieldset style="width:3860px;">
	        	   <table width="1880"  cellspacing="0"   >
	                    <tr class="form_caption" style="border:none;">
	                           <td colspan="30" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report</td>
	                     </tr>
	                    <tr style="border:none;">
	                            <td colspan="30" align="center" style="border:none; font-size:16px; font-weight:bold">
	                            Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
	                            </td>
	                      </tr>
	                      <tr style="border:none;">
	                            <td colspan="30" align="center" style="border:none;font-size:12px; font-weight:bold">
	                            <? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
	                            </td>
	                      </tr>
	                </table>
	             <br />
	             <table cellspacing="0"  border="1" rules="all"  width="3860" class="rpt_table">
	                <thead>
	                	<tr >
	                        <th width="40" rowspan="2">SL</th>
	                        <th width="80" rowspan="2">Buyer</th>
	                        <th width="60" rowspan="2">Job No</th>
	                        <th width="50" rowspan="2">Year</th>
	                        <th width="100" rowspan="2">Order No</th>
	                        <th width="80" rowspan="2">File No</th>
	                        <th width="80" rowspan="2">Int. Ref. No</th>
	                        <th width="80" rowspan="2">TNA Start Date</th>
	                        <th width="80" rowspan="2">TNA End Date</th>
	                        <th width="80" rowspan="2">First Shipment Date</th>
	                        <th width="100" rowspan="2">Style</th>
	                        <th width="120" rowspan="2">Item</th>
	                        <th width="100" rowspan="2">Color</th>
	                        <th width="70" rowspan="2">Order Qty.</th>
	                        <th width="70" rowspan="2">Plan Cut Qty.</th>
	                        <th width="70" rowspan="2">Fin Fab Cons/Dzn.</th>
	                        <th width="70" rowspan="2">Fabric Required Qty</th>
	                        <th width="240" colspan="4">Fabric Receive Qty.</th>
	                        <th width="70" rowspan="2">Possible Cut Qty</th>
	                        <th width="240" colspan="4">Cutting</th>
	                        <th width="60" rowspan="2"> Cutting WIP</th>
	                        <th width="240" colspan="4">Cutting Delivery To Input</th>
	                        <th width="180" colspan="3">Delivery to Print</th>
	                        <th width="180" colspan="3">Receive from Print</th>
	                        <th width="60" rowspan="2">Print WIP</th>
	                        <th width="180" colspan="3">Delivery to Emb.</th>
	                        <th width="180" colspan="3">Receive from Emb.</th>
	                        <th width="60" rowspan="2">Emb. WIP</th>

	                        <th width="300" colspan="5"> Sewing Input</th>
	                        <th width="70" rowspan="2">Cutting Inhand</th>
	                        <th width="70" rowspan="2">Input Inhand</th>
	                        <th width="100" rowspan="2">Ready To Sewing</th>
	                        <th width="100" rowspan="2">Line No</th>
	                        <th  rowspan="2">Remarks</th>
	                    </tr>
	                    <tr>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Bal.</th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Bal.</th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Bal.</th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today</th>
	                        <th width="60" rowspan="2">Total</th>
	                        <th width="60" rowspan="2">%</th>
	                        <th width="60" rowspan="2">Balance</th>


	                    </tr>
	                </thead>
	            </table>
	             <div style="max-height:425px; overflow-y:scroll; width:3860px;" id="scroll_body">
	                    <table  border="1" class="rpt_table"  width="3842" rules="all" id="table_body" >
	                    <?
	                      $total_cut=0;
	                      $total_print_iss=0;
						  $total_embl_iss=0;
						  $total_wash_iss=0;
						  $total_sp_iss=0;
	                      $total_print_receive=0;
						  $total_sp_rec=0;
						  $total_embl_rec=0;
						  $total_wash_receive=0;
						  $total_sp_rec=0;
	                      $total_sew_input=0;
	                      $total_sew_out=0;
						  $total_delivery_cut=0;
	                      $cutting_balance=0;
						  $print_issue_balance=0;
						  $print_rec_balance=0;
					  	  $deliv_cut_bal=0;
						  $total_sew_input_balance=0;
						  $input_percentage=0;
						  $inhand=0;
						  $buyer_total_order=0;
						  $buyer_total_plan=0;
						  $buyer_total_fabric_qty=0;
						  $buyer_total_fabric_pre=0;
						  $buyer_fabric_total=0;
						  $buyer_fabric_today_total=0;
						  $buyer_fabric_bal=0;
						  $buyer_pre_cut=0;
						  $buyer_today_cut=0;
						  $buyer_total_cut=0;
						  $buyer_cutting_balance=0;
						  $buyer_priv_print_iss=0;
						  $buyer_today_print_iss=0;
						  $buyer_print_issue_balance=0;
						  $buyer_priv_print_rec=0;
						  $buyer_today_print_rec=0;
						  $buyer_total_print_rec=0;
						  $buyer_print_rec_balance=0;
						  $buyer_priv_deliv_cut=0;
						  $buyer_today_deliv_cut=0;
						  $buyer_total_delivery_cut=0;
						  $buyer_deliv_cut_bal=0;
						  $buyer_priv_sew=0;
						  $buyer_today_sew=0;
						  $buyer_total_sew_=0;
						  $buyer_total_sew__bal=0;
						  $buyer_inhand=0;
						  $buyer_arr=array();
						  $job_arr=array();
	                      $i=1;$k=1;


		  //echo "jahid";die;

	  	foreach($po_number_data as $po_id=>$po_arr)
		{
			 foreach($po_arr as $color_id=>$color_arr)
		     {
				if($i==1)
				{
					$job_no = $po_number_data[$po_id][$color_id]['job_no'];
					// echo $job_no;
				}

	 				if($i!=1)
					  {
						// echo $job_no;
						 if(!in_array($po_number_data[$po_id][$color_id]['job_no'],$job_arr))
						 {
									// echo "abc";
								?>
								   <tr bgcolor="#CCCCCC" id="">
										<td width="40"><? // echo $i;?></td>
										<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
										<td width="60"></td>
										<td width="50"></td>
										<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
										<td width="80"></td>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
										<td width="80"></td>
										<td width="80"></td>
	                                    <td width="100"><strong></strong></td>
										<td width="120"><strong></strong></td>
										<td width="100" align="right"><strong>Job Total:</strong></td>
										<td width="70" align="right"><? echo $job_total_order; ?></td>
										<td width="70" align="right"><?  echo $job_total_plan; ?></td>
										<td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
										<td width="70" align="right"><?  echo number_format($job_total_fabric_qty,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_total_fabric_pre,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_today_total,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_total,2);?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_bal,2); ?></td>

										<td width="70" align="right"><?  echo number_format($job_possible_cut_qty,0); ?></td>
										<td width="60" align="right"><?  echo $job_pre_cut; ?></td>
										<td width="60" align="right"><a href="##" onclick="openmypage_job_total('<? echo  $job_no; ?>',<? echo $txt_production_date; ?>, 'cutting_popup_one',850,350)"> <?  echo $job_today_cut; ?></td>
										<td width="60" align="right"><a href="##" onclick="openmypage_job_wise_total('<? echo  $job_no; ?>', 'job_total_cutting_popup_one',850,350)"> <?  echo $job_total_cut; ?></a></td>
										<td width="60" align="right"><?  echo $job_cutting_balance; ?></td>
										<td width="60" align="right"><?  echo number_format($job_possible_cut_qty,0)-$job_total_cut; ?></td>
										<td width="60" align="right"><?  echo $job_priv_deliv_cut; ?></td>
										<td width="60" align="right"><?  echo $job_today_deliv_cut; ?></td>
										<td width="60" align="right"><?  echo $job_total_delivery_cut; ?></td>
										<td width="60" align="right"><?  echo $job_deliv_cut_bal; ?></td>
										<td width="60" align="right"><?  echo $job_priv_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_today_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_priv_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_today_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_iss-$job_total_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_priv_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_today_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_priv_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_today_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_iss-$job_total_embl_rec; ?></td>

										<td width="60" align="right"><?  echo $job_priv_sew; ?></td>
										<td width="60" align="right"><?  echo $job_today_sew; ?></td>
										<td width="60" align="right"> <? echo $job_total_sew_input; ?></td>
										<td width="60" align="right"><? //echo $input_percentage; ?></td>
										<td width="60" align="right"><?  echo $job_total_sew__bal; ?></td>
										<td width="70" align="right"><? echo $job_cutinhand; ?></td>
										<td width="70" align="right"><? echo $job_inhand; ?></td>
										<td width="100" align="right"><? echo $job_ready_to_sewing; ?></td>
										<td width="100" align="right"><? //echo  $sewing_line ?></td>
										<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
								  </tr>
								<?
								$job_no = $po_number_data[$po_id][$color_id]['job_no'];
								  $job_inhand=0;
								  $job_cutinhand=0;
								  $job_possible_cut_qty=0;
								  $job_total_order=0;
								  $job_total_plan=0;
								  $job_total_fabric_qty=0;
								  $job_total_fabric_pre=0;
								  $job_fabric_total=0;
								  $job_fabric_today_total=0;
								  $job_fabric_bal=0;
								  $job_pre_cut=0;
								  $job_today_cut=0;
								  $job_total_cut=0;
								  $job_cutting_balance=0;
								  $job_priv_print_iss=0;
								  $job_today_print_iss=0;
								  $job_total_print_iss=0;
								  $job_print_issue_balance=0;
								  $job_priv_print_rec=0;
								  $job_today_print_rec=0;
								  $job_total_print_rec=0;
								  $job_print_rec_balance=0;
								  $job_priv_deliv_cut=0;
								  $job_today_deliv_cut=0;
								  $job_total_delivery_cut=0;
								  $job_deliv_cut_bal=0;
								  $job_priv_sew=0;
								  $job_today_sew=0;
								  $job_total_sew_input=0;
								  $job_total_sew__bal=0;
								  $job_priv_print_iss=0;
								  $job_today_print_iss=0;
								  $job_total_print_iss=0;
								  $job_priv_embl_iss=0;
								  $job_priv_print_rec=0;
								  $job_today_embl_iss=0;
								  $job_total_embl_iss=0;
								  $job_today_wash_iss=0;
								  $job_priv_wash_iss=0;
								  $job_today_sp_iss=0;
								  $job_total_wash_iss=0;
								  $job_total_sp_iss=0;
								  $job_priv_sp_iss=0;
								  $job_priv_print_rec=0;
								  $job_today_print_rec=0;
								  $job_total_print_rec=0;
								  $job_priv_wash_rec=0;
								  $job_today_wash_rec=0;
								  $job_total_wash_rec=0;
								  $job_priv_embl_rec=0;
								  $job_today_embl_rec=0;
								  $job_total_embl_rec=0;
								  $job_priv_sp_rec=0;
								  $job_today_sp_rec=0;
								  $job_total_sp_rec=0;
								  $job_ready_to_sewing=0;
						 }
					  }


					 if($i!=1)
					 {
					 if( !in_array($po_number_data[$po_id][$color_id]['buyer_name'],$buyer_arr))
							{

							?>
							<tr bgcolor="#999999" style=" height:15px">
							<td width="40"><? // echo $i;?></td>
							<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
							<td width="60"></td>
							<td width="50"></td>
							<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
							<td width="80"></td>
	                        <td width="80"></td>
	                        <td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="100"><strong> </strong></td>
	                        <td width="120"><strong> </strong></td>
							<td width="100" align="right"><strong>Buyer Total:</strong></td>
							<td width="70" align="right"><? echo $buyer_total_order; ?></td>
							<td width="70" align="right"><?  echo $buyer_total_plan; ?></td>
							<td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
							<td width="70" align="right"><?  echo number_format($buyer_total_fabric_qty,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_total_fabric_pre,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_today_total,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_total,2);?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_bal,2); ?></td>

							<td width="70" align="right"><?  echo number_format($buyer_possible_cut_qty,0); ?></td>
							<td width="60" align="right"><?  echo $buyer_pre_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_cutting_balance; ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_possible_cut_qty,0)-$buyer_total_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_deliv_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_deliv_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_delivery_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_deliv_cut_bal; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_print_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_print_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_print_rec; ?></td>
						   <td width="60" align="right"><?   echo $buyer_total_print_iss-$buyer_total_print_rec; ?></td>

							<td width="60" align="right"><?  echo $buyer_priv_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_iss-$buyer_total_embl_rec; ?></td>


							<td width="60" align="right"><?  echo $buyer_priv_sew; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_sew; ?></td>
							<td width="60" align="right"> <? echo $buyer_total_sew_; ?></td>
							<td width="60" align="right"><? //echo $input_percentage; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_sew_bal; ?></td>
							<td width="70" align="right"><? echo $buyer_cutinhand; ?></td>
							<td width="70" align="right"><? echo $buyer_inhand; ?></td>
							<td width="100" align="right"><? echo $buyer_ready_to_sewing; ?></td>

							<td width="100" align="right"><? //echo  $sewing_line ?></td>
							<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
					  </tr>
								<?
								  $buyer_cutinhand=$buyer_ready_to_sewing=0;
								  $buyer_possible_cut_qty=0;
								  $buyer_total_order=0;
								  $buyer_total_plan=0;
								  $buyer_total_fabric_qty=0;
								  $buyer_total_fabric_pre=0;
								  $buyer_fabric_total=0;
								  $buyer_fabric_today_total=0;
								  $buyer_fabric_bal=0;
								  $buyer_pre_cut=0;
								  $buyer_today_cut=0;
								  $buyer_total_cut=0;
								  $buyer_cutting_balance=0;
								  $buyer_priv_print_iss=0;
								  $buyer_today_print_iss=0;
								  $buyer_total_print_iss=0;
								  $buyer_print_issue_balance=0;
								  $buyer_priv_print_rec=0;
								  $buyer_today_print_rec=0;
								  $buyer_total_print_rec=0;
								  $buyer_print_rec_balance=0;
								  $buyer_priv_deliv_cut=0;
								  $buyer_today_deliv_cut=0;
								  $buyer_total_delivery_cut=0;
								  $buyer_deliv_cut_bal=0;
								  $buyer_priv_sew=0;
								  $buyer_today_sew=0;
								  $buyer_total_sew_=0;
								  $buyer_total_sew__bal=0;
								  $buyer_inhand=0;
								  $buyer_priv_print_iss=0;
								  $buyer_today_print_iss=0;
								  $buyer_total_print_iss=0;
								  $buyer_priv_embl_iss=0;
								  $buyer_priv_print_rec=0;
								  $buyer_today_embl_iss=0;
								  $buyer_total_embl_iss=0;
								  $buyer_today_wash_iss=0;
								  $buyer_priv_wash_iss=0;
								  $buyer_today_sp_iss=0;
								  $buyer_total_wash_iss=0;
								  $buyer_total_sp_iss=0;
								  $buyer_priv_sp_iss=0;
								  $buyer_priv_print_rec=0;
								  $buyer_today_print_rec=0;
								  $buyer_total_print_rec=0;
								  $buyer_priv_wash_rec=0;
								  $buyer_today_wash_rec=0;
								  $buyer_total_wash_rec=0;
								  $buyer_priv_embl_rec=0;
								  $buyer_today_embl_rec=0;
								  $buyer_total_embl_rec=0;
								  $buyer_priv_sp_rec=0;
								  $buyer_today_sp_rec=0;
								  $buyer_total_sp_rec=0;
							}
					  }
					//***********************for line****************************************************************************************
					$line_id_all=$sew_line_arr[$po_id][$color_id]['line'];
					$line_name="";
					foreach($line_id_all as $l_id)
					{
						if($line_name!="") $line_name.=",";
						if($prod_reso_allo==1)
						{
						$line_name.= $lineArr[$prod_reso_arr[$l_id]];
						}
						else
						{
						$line_name.= $lineArr[$l_id];
						}
					}

					$costing_per=$costing_per_arr[$po_number_data[$po_id][$color_id]['job_no']];
					if($costing_per==1)
					{
						$costing_per_qty=12;
					}
					else if($costing_per==2)
					{
						$costing_per_qty=1;
					}
					else if($costing_per==3)
					{
						$costing_per_qty=24;
					}
					else if($costing_per==4)
					{
						$costing_per_qty=36;
					}
					else if($costing_per==5)
					{
						$costing_per_qty=48;
					}

					//$fabric_wip=$fabric_wip[$po_id][$color_id]['issue']-$fabric_wip[$po_id][$color_id]['receive'];
					$ready_to_sewing=$ready_to_sewing_arr[$po_id][$color_id];
				    $fabric_pre=$fabric_pre_qty[$po_id][$color_id];
					$fabric_today=$fabric_today_qty[$po_id][$color_id];
					$total_fabric=$fabric_pre+$fabric_today;
					$cons_per_pcs = $con_per_dzn[$po_id][$color_id]/$costing_per_qty;
					$possible_cut_qty=($total_fabric/($con_per_dzn[$po_id][$color_id]/$costing_per_qty));
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$plan_cut_qty = $job_qty_data[$po_id][$color_id]['plan_qty'];
					$fabric_qty = $plan_cut_qty * $cons_per_pcs;
				    $fabric_balance=$fabric_qty-$total_fabric;
					$total_cut=$production_data_arr[$po_id][$color_id]['cutting_qnty']+$production_data_arr[$po_id][$color_id]['cutting_qnty_pre'];
					$cutting_balance= $plan_cut_qty - $total_cut;
					$cutting_balance_title =" Plan Cut Qty($plan_cut_qty) - Total Cutting($total_cut)";

					$total_print_iss=$production_data_arr[$po_id][$color_id]['printing_qnty_pre']+$production_data_arr[$po_id][$color_id]['printing_qnty'];
					$total_embl_iss=$production_data_arr[$po_id][$color_id]['embl_qnty_pre']+$production_data_arr[$po_id][$color_id]['embl_qnty'];
					$total_embl_rec=$production_data_arr[$po_id][$color_id]['emblreceived_qnty_pre']+$production_data_arr[$po_id][$color_id]['emblreceived_qnty'];
					$total_print_receive=$production_data_arr[$po_id][$color_id]['printreceived_qnty']+$production_data_arr[$po_id][$color_id]['printreceived_qnty_pre'];

					//echo $inhand."***";die;
					$print_balance=$total_print_iss-$total_print_receive;
					$embl_balance=$total_embl_iss-$total_embl_rec;
					$wash_balance=$total_wash_iss-$total_wash_receive;
					$sp_balance=$total_sp_iss-$total_sp_rec;
					$total_emblishment_iss=$total_embl_iss+$total_wash_iss+$total_print_iss+$total_sp_iss;
					//$total_emblishment_rec=$total_wash_receive+$total_embl_rec+$total_sp_rec+$total_print_rec;
					$print_issue_balance=$total_emblishment_iss-$total_cut;
					$print_rec_balance=$total_emblishment_rec-$total_emblishment_iss;
					$total_delivery_cut=$production_data_arr[$po_id][$color_id]['cut_delivery_qnty']+$production_data_arr[$po_id][$color_id]['cut_delivery_qnty_pre'];
					$deliv_cut_bal=($total_cut)-$total_delivery_cut;
					$total_sew_input=$production_data_arr[$po_id][$color_id]['sewingin_qnty']+$production_data_arr[$po_id][$color_id]['sewingin_qnty_pre'];
					$total_sew_input_balance=$job_qty_data[$po_id][$color_id]['plan_qty']-$total_sew_input;
					$input_percentage=($total_sew_input/$job_qty_data[$po_id][$color_id]['po_quantity'])*100;
					// $input_percentage plan cut qty change to po qty by saeed vie;

					$cutting_inhand=0;
					$cutting_inhand=$total_cut-$total_delivery_cut;
					$inhand=0;
					if($total_print_iss!=0 && $total_embl_iss!=0)
					{
						if(date("Y-m-d",strtotime($production_data_arr[$po_id][$color_id]['min_printin_date']))<=date("Y-m-d",strtotime($production_data_arr[$po_id][$color_id]['min_embl_date'])))
					    {
				        $inhand=($total_delivery_cut+$total_embl_rec)-($total_sew_input+$total_print_iss);
						}
						else
						{
						$inhand=($total_delivery_cut+$total_embl_iss)-($total_sew_input+$total_print_receive);
						}
					}
					else if ($total_print_iss!=0) { $inhand=(($total_delivery_cut+$total_print_receive)-($total_print_iss+$total_sew_input));}
					else if ($total_embl_iss!=0) { $inhand=($total_delivery_cut+$total_embl_rec)-($total_embl_iss+$total_sew_input);}
					else if($total_delivery_cut!=0)$inhand=$total_delivery_cut-$total_sew_input;
					else $inhand=0;  // $inhand=$total_cut-$total_sew_input;
					//if($inhand<0) $inhand=0;


					//for job total *******************************************************************************************************
					$job_possible_cut_qty+=$possible_cut_qty;
					$job_total_order+=$job_qty_data[$po_id][$color_id]['po_quantity'];
					$job_total_plan+=$job_qty_data[$po_id][$color_id]['plan_qty'];
					$job_total_fabric_qty+=$fabric_qty;
					$job_total_fabric_pre+=$fabric_pre;
					$job_fabric_today_total+=$fabric_today;
					$job_fabric_total+=$total_fabric;
					$job_fabric_bal+=$fabric_balance;
					$job_pre_cut+=$production_data_arr[$po_id][$color_id]['cutting_qnty_pre'];
					$job_today_cut+=$production_data_arr[$po_id][$color_id]['cutting_qnty'];
					$job_total_cut+=$total_cut;
					$job_cutting_balance+=$cutting_balance;
					$job_priv_print_iss+=$production_data_arr[$po_id][$color_id]['printing_qnty_pre'];
					$job_today_print_iss+=$production_data_arr[$po_id][$color_id]['printing_qnty'];
					$job_total_print_iss+=$total_print_iss;
					$job_priv_embl_iss+=$production_data_arr[$po_id][$color_id]['embl_qnty_pre'];
					$job_today_embl_iss+=$production_data_arr[$po_id][$color_id]['embl_qnty'];
					$job_total_embl_iss+=$total_embl_iss;
					$job_today_wash_iss+=$production_data_arr[$po_id][$color_id]['wash_qnty'];
					$job_priv_wash_iss+=$production_data_arr[$po_id][$color_id]['wash_qnty_pre'];
					$job_total_wash_iss+=$total_wash_iss;
					$job_priv_sp_iss+=$production_data_arr[$po_id][$color_id]['sp_qnty_pre'];
					$job_today_sp_iss+=$production_data_arr[$po_id][$color_id]['sp_qnty'];
					$job_total_sp_iss+=$total_sp_iss;
					$job_priv_print_rec+=$production_data_arr[$po_id][$color_id]['printreceived_qnty_pre'];
					$job_today_print_rec+=$production_data_arr[$po_id][$color_id]['printreceived_qnty'];
					$job_total_print_rec+=$total_print_receive;
					$job_print_issue_balance=$print_issue_balance;
					$job_priv_wash_rec+=$production_data_arr[$po_id][$color_id]['washreceived_qnty_pre'];
					$job_today_wash_rec+=$production_data_arr[$po_id][$color_id]['washreceived_qnty'];
					$job_total_wash_rec+=$total_wash_receive;
					$job_priv_sp_rec+=$production_data_arr[$po_id][$color_id]['sp_received_qnty_pre'];
					$job_today_sp_rec+=$production_data_arr[$po_id][$color_id]['sp_received_qnty'];
					$job_total_embl_rec+=$total_embl_rec;
					$job_priv_embl_rec+=$production_data_arr[$po_id][$color_id]['emblreceived_qnty_pre'];
					$job_today_embl_rec+=$production_data_arr[$po_id][$color_id]['emblreceived_qnty'];
					$job_total_sp_rec+=$total_sp_rec;
					$job_print_rec_balance+=$print_rec_balance;
					$job_priv_deliv_cut+=$production_data_arr[$po_id][$color_id]['cut_delivery_qnty_pre'];
					$job_today_deliv_cut+=$production_data_arr[$po_id][$color_id]['cut_delivery_qnty'];
					$job_total_delivery_cut+=$total_delivery_cut;
					$job_deliv_cut_bal+=$deliv_cut_bal;
					$job_priv_sew+=$production_data_arr[$po_id][$color_id]['sewingin_qnty_pre'];
					$job_today_sew+=$production_data_arr[$po_id][$color_id]['sewingin_qnty'];
					$job_total_sew_input+=$total_sew_input;
					$job_total_sew_bal+=$total_sew_input_balance;
					$job_inhand+=$inhand;
					$job_cutinhand+=$cutting_inhand;
					$job_ready_to_sewing+=$ready_to_sewing;
					//buyer sub total **************************************************************************************************
					$buyer_possible_cut_qty+=$possible_cut_qty;
					$buyer_total_order+=$job_qty_data[$po_id][$color_id]['po_quantity'];
					$buyer_total_plan+=$job_qty_data[$po_id][$color_id]['plan_qty'];
					$buyer_total_fabric_qty+=$fabric_qty;
					$buyer_total_fabric_pre+=$fabric_pre;
					$buyer_fabric_today_total+=$fabric_today;
					$buyer_fabric_total+=$total_fabric;
					$buyer_fabric_bal+=$fabric_balance;
					$buyer_pre_cut+=$production_data_arr[$po_id][$color_id]['cutting_qnty_pre'];
					$buyer_today_cut+=$production_data_arr[$po_id][$color_id]['cutting_qnty'];
					$buyer_total_cut+=$total_cut;
					$buyer_cutting_balance+=$cutting_balance;
					$buyer_priv_print_iss+=$production_data_arr[$po_id][$color_id]['printing_qnty_pre'];
					$buyer_today_print_iss+=$production_data_arr[$po_id][$color_id]['printing_qnty'];
					$buyer_total_print_iss+=$total_print_iss;
					$buyer_priv_embl_iss+=$production_data_arr[$po_id][$color_id]['embl_qnty_pre'];
					$buyer_today_embl_iss+=$production_data_arr[$po_id][$color_id]['embl_qnty'];
					$buyer_total_embl_iss+=$total_embl_iss;
					$buyer_today_wash_iss+=$production_data_arr[$po_id][$color_id]['wash_qnty'];
					$buyer_priv_wash_iss+=$production_data_arr[$po_id][$color_id]['wash_qnty_pre'];
					$buyer_total_wash_iss+=$total_wash_iss;
					$buyer_priv_sp_iss+=$production_data_arr[$po_id][$color_id]['sp_qnty_pre'];
					$buyer_today_sp_iss+=$production_data_arr[$po_id][$color_id]['sp_qnty'];
					$buyer_total_sp_iss+=$total_sp_iss;
					$buyer_priv_print_rec+=$production_data_arr[$po_id][$color_id]['printreceived_qnty_pre'];
					$buyer_today_print_rec+=$production_data_arr[$po_id][$color_id]['printreceived_qnty'];
					$buyer_total_print_rec+=$total_print_receive;
					$buyer_print_issue_balance=$print_issue_balance;
					$buyer_priv_wash_rec+=$production_data_arr[$po_id][$color_id]['washreceived_qnty_pre'];
					$buyer_today_wash_rec+=$production_data_arr[$po_id][$color_id]['washreceived_qnty'];
					$buyer_total_wash_rec+=$total_wash_receive;
					$buyer_priv_sp_rec+=$production_data_arr[$po_id][$color_id]['sp_received_qnty_pre'];
					$buyer_today_sp_rec+=$production_data_arr[$po_id][$color_id]['sp_received_qnty'];
					$buyer_total_embl_rec+=$total_embl_rec;
					$buyer_priv_embl_rec+=$production_data_arr[$po_id][$color_id]['emblreceived_qnty_pre'];
					$buyer_today_embl_rec+=$production_data_arr[$po_id][$color_id]['emblreceived_qnty'];
					$buyer_total_sp_rec+=$total_sp_rec;
					$buyer_priv_deliv_cut+=$production_data_arr[$po_id][$color_id]['cut_delivery_qnty_pre'];
					$buyer_today_deliv_cut+=$production_data_arr[$po_id][$color_id]['cut_delivery_qnty'];
					$buyer_total_delivery_cut+=$total_delivery_cut;
					$buyer_deliv_cut_bal+=$deliv_cut_bal;
					$buyer_priv_sew+=$production_data_arr[$po_id][$color_id]['sewingin_qnty_pre'];
					$buyer_today_sew+=$production_data_arr[$po_id][$color_id]['sewingin_qnty'];
					$buyer_total_sew_+=$total_sew_input;
					$buyer_total_sew_bal+=$total_sew_input_balance;
					$buyer_inhand+=$inhand;
					$buyer_cutinhand+=$cutting_inhand;
					$buyer_ready_to_sewing+=$ready_to_sewing;
					// for grand total ********************************************************************************************************************
					$grand_possible_cut_qty+=$possible_cut_qty;
					$grand_total_order+=$job_qty_data[$po_id][$color_id]['po_quantity'];
					$grand_total_plan+=$job_qty_data[$po_id][$color_id]['plan_qty'];
					$grand_total_fabric_qty+=$fabric_qty;
					$grand_total_fabric_pre+=$fabric_pre;
					$grand_fabric_today_total+=$fabric_today;
					$grand_fabric_total+=$total_fabric;
					$grand_fabric_bal+=$fabric_balance;
					$grand_pre_cut+=$production_data_arr[$po_id][$color_id]['cutting_qnty_pre'];
					$grand_today_cut+=$production_data_arr[$po_id][$color_id]['cutting_qnty'];
					$grand_total_cut+=$total_cut;
					$grand_cutting_balance+=$cutting_balance;
					$grand_priv_print_iss+=$production_data_arr[$po_id][$color_id]['printing_qnty_pre'];
					$grand_today_print_iss+=$production_data_arr[$po_id][$color_id]['printing_qnty'];
					$grand_total_print_iss+=$total_print_iss;
					$grand_priv_embl_iss+=$production_data_arr[$po_id][$color_id]['embl_qnty_pre'];
					$grand_today_embl_iss+=$production_data_arr[$po_id][$color_id]['embl_qnty'];
					$grand_total_embl_iss+=$total_embl_iss;
					$grand_today_wash_iss+=$production_data_arr[$po_id][$color_id]['wash_qnty'];
					$grand_priv_wash_iss+=$production_data_arr[$po_id][$color_id]['wash_qnty_pre'];
					$grand_total_wash_iss+=$total_wash_iss;
					$grand_priv_sp_iss+=$production_data_arr[$po_id][$color_id]['sp_qnty_pre'];
					$grand_today_sp_iss+=$production_data_arr[$po_id][$color_id]['sp_qnty'];
					$grand_total_sp_iss+=$total_sp_iss;
					$grand_priv_print_rec+=$production_data_arr[$po_id][$color_id]['printreceived_qnty_pre'];
					$grand_today_print_rec+=$production_data_arr[$po_id][$color_id]['printreceived_qnty'];
					$grand_total_print_rec+=$total_print_receive;
					$grand_print_issue_balance=$print_issue_balance;
					$grand_priv_wash_rec+=$production_data_arr[$po_id][$color_id]['washreceived_qnty_pre'];
					$grand_today_wash_rec+=$production_data_arr[$po_id][$color_id]['washreceived_qnty'];
					$grand_total_wash_rec+=$total_wash_receive;
					$grand_priv_sp_rec+=$production_data_arr[$po_id][$color_id]['sp_received_qnty_pre'];
					$grand_today_sp_rec+=$production_data_arr[$po_id][$color_id]['sp_received_qnty'];
					$grand_total_embl_rec+=$total_embl_rec;
					$grand_priv_embl_rec+=$production_data_arr[$po_id][$color_id]['emblreceived_qnty_pre'];
					$grand_today_embl_rec+=$production_data_arr[$po_id][$color_id]['emblreceived_qnty'];
					$grand_total_sp_rec+=$total_sp_rec;
					$grand_priv_deliv_cut+=$production_data_arr[$po_id][$color_id]['cut_delivery_qnty_pre'];
					$grand_today_deliv_cut+=$production_data_arr[$po_id][$color_id]['cut_delivery_qnty'];
					$grand_total_delivery_cut+=$total_delivery_cut;
					$grand_deliv_cut_bal+=$deliv_cut_bal;
					$grand_priv_sew+=$production_data_arr[$po_id][$color_id]['sewingin_qnty_pre'];
					$grand_today_sew+=$production_data_arr[$po_id][$color_id]['sewingin_qnty'];
					$grand_total_sew_+=$total_sew_input;
					$grand_total_sew_bal+=$total_sew_input_balance;
					$grand_inhand+=$inhand;
					$grand_cutinhand+=$cutting_inhand;
					$grand_ready_to_sewing+=$ready_to_sewing;

					$sewing = $production_data_arr[$po_id][$color_id]['sewingin_qnty'];
					$embRcv = $production_data_arr[$po_id][$color_id]['emblreceived_qnty'];
					$embQty = $production_data_arr[$po_id][$color_id]['embl_qnty'];
					$printRcv = $production_data_arr[$po_id][$color_id]['printreceived_qnty'];
					$print = $production_data_arr[$po_id][$color_id]['printing_qnty'];
					$cutDel = $production_data_arr[$po_id][$color_id]['cut_delivery_qnty'];
					$cut = $production_data_arr[$po_id][$color_id]['cutting_qnty'];
					// if( $cut !=0 || $cutDel !=0 || $print !=0 || $printRcv !=0 || $embQty !=0 || $embRcv !=0 || $sewing !=0 ){}

	                    ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
	                    <td width="40"><? echo $i; ?></td>
	                    <td width="80" style="word-break:break-all;"><p><? echo $buyer_short_library[$po_number_data[$po_id][$color_id]['buyer_name']]; ?></p></td>
	                    <td width="60" align="center"><? echo $po_number_data[$po_id][$color_id]['job_prifix'];?></td>
	                    <td width="50" align="right"><? echo $po_number_data[$po_id][$color_id]['year'];?></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$color_id]['po_number'];?></p></td>
	                    <td width="80" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$color_id]['file_no'];?></p></td>
	                    <td width="80" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$color_id]['int_ref_no'];?></p></td>
	                    <td width="80" align="center"><?  echo  change_date_format($tna_date_arr[$po_id]['tna_start']);  ?></td>
	                    <td width="80" align="center"><?  echo  change_date_format($tna_date_arr[$po_id]['tna_end']);  ?></td>
	                    <td width="80" align="center"><?  echo  change_date_format($po_min_shipdate_data[$po_id][$color_id]['minimum_shipdate']);  ?></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$color_id]['style']; ?></p></td>
	                    <td width="120" align="center" style="word-break:break-all;"><p><? echo $garments_item[$po_number_data[$po_id][$color_id]['gmt_id']]; ?></p></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $colorname_arr[$po_number_data[$po_id][$color_id]['color_id']]; ?></p></td>
	                    <td width="70" align="right"><?  echo $job_qty_data[$po_id][$color_id]['po_quantity']; ?></td>
	                    <td width="70" align="right"><?  echo $plan_cut_qty; ?></td>
	                    <td width="70" align="right"><?  echo number_format(($con_per_dzn[$po_id][$color_id]*12/$costing_per_qty),3); ?></td>
	                    <td width="70" align="right"  title="<?= "consumption per pcs($cons_per_pcs) * Plan Cut Qty ($plan_cut_qty)" ?>"><?  echo number_format($fabric_qty,2); ?></td>
	                    <td width="60" align="right"><?  echo number_format($fabric_pre,2); ?></td>
	                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,1)"><?  echo number_format($fabric_today,2); ?></a></td>
	                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,2)"><?  echo number_format($total_fabric,2); ?></a><?  //echo number_format($total_fabric,2);?></td>
	                    <td width="60" align="right"><?  echo number_format($fabric_balance,2); ?></td>

	                    <td width="70" align="right" title="Possible Cut Qty. =Total Fabric Receive/ Consumption Per Pcs."><?  echo  number_format($possible_cut_qty,0); ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$color_id]['cutting_qnty_pre']; //$production_data_arr[$row_fab[csf('po_breakdown_id')]][$row_fab[csf('color_id')]]['cutting_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',850,350,'','<? echo $color_id; ?>','',1)"> <?  echo $production_data_arr[$po_id][$color_id]['cutting_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',950,450,'','<? echo $color_id; ?>','',2)"><?  echo $total_cut; ?></a></td>
	                    <td width="60" align="right" title="<?= $cutting_balance_title ?>" ><?  echo $cutting_balance; ?></td>
	                    <td width="60" align="right" title="Cutting WIP=Possible Cut Qty - Total Cutting Qty"><?  echo number_format($possible_cut_qty-$total_cut,0); ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$color_id]['cut_delivery_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','',1)"><?  echo $production_data_arr[$po_id][$color_id]['cut_delivery_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','',2)"><?  echo $total_delivery_cut; ?></a></td>
	                    <td width="60" align="right"><?  echo $deliv_cut_bal; ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$color_id]['printing_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','',1)"><?  echo $production_data_arr[$po_id][$color_id]['printing_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','',2)"><?  echo $total_print_iss; ?></a></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$color_id]['printreceived_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','',1)"><?  echo $production_data_arr[$po_id][$color_id]['printreceived_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','',2)"><?  echo $total_print_receive; ?></a></td>
	                    <td width="60" align="right" title="Print WIP=Delivery to Print-Receive from Print"><?  echo $print_balance; ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$color_id]['embl_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','',1)"><?  echo $production_data_arr[$po_id][$color_id]['embl_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','',2)"><?  echo $total_embl_iss; ?></a></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$color_id]['emblreceived_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','',1)"><?  echo $production_data_arr[$po_id][$color_id]['emblreceived_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','',2)"><?  echo $total_embl_rec; ?></a></td>
	                    <td width="60" align="right" title="Embl. WIP=Delivery to Embl.-Receive from Embl."><?  echo $embl_balance; ?></td>

	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$color_id]['sewingin_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','',1)"><?  echo $production_data_arr[$po_id][$color_id]['sewingin_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','',2)"> <? echo $total_sew_input; ?></a></td>
	                    <td width="60" align="right" title="(Total Sewing Input*100)/Order Qty"><?  echo number_format($input_percentage,2); ?></td>
	                    <td width="60" align="right"><?  echo $total_sew_input_balance; ?></td>
	                    <td width="70" align="right" title="Inhand=Total Cut - Delivery to Input" ><?  echo $cutting_inhand; ?></td>
	                     <td width="70" align="right" title="Inhand= Cutting Delivery To Input  - Delivery to  Embellishment &#13; + Receive from Embellishment -Sewing Input" ><?  echo $inhand; ?></td>
	                    <td width="100" align="right"><p><?  echo  $ready_to_sewing; ?></p></td>
	                    <td width="100" align="center"><p><?  echo  $line_name; ?></p></td>
	                    <td  align="center"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_remarks',650,350,'','<? echo $color_id; ?>','','')">
						<?
						   if($total_delivery_cut-$plan_cut_qty>0) echo "Receive Ok";
						   else   if($total_sew_input_balance-$plan_cut_qty>0) echo "Input Ok";
						   else echo "Remarks";

						?></a></td>
	        	  </tr>
							<?
					 $job_arr[]=$po_number_data[$po_id][$color_id]['job_no'];
					 $buyer_arr[]=$po_number_data[$po_id][$color_id]['buyer_name'];
					 $i++;

	                } //end foreach 2nd

	 		 }

				?>
	                         <tr bgcolor="#CCCCCC" id="">
	                                <td width="40"><? // echo $i;?></td>
	                                <td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
	                                <td width="60"></td>
	                                <td width="50"></td>
	                                <td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="100"><strong></strong></td>
	                                <td width="120"><strong></strong></td>
	                                <td width="100" align="right"><strong>Job Total:</strong></td>
	                                <td width="70" align="right"><? echo $job_total_order; ?></td>
	                                <td width="70" align="right"><?  echo $job_total_plan; ?></td>
	                                <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
	                                <td width="70" align="right"><?  echo number_format($job_total_fabric_qty,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_total_fabric_pre,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_today_total,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_total,2);?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_bal,2); ?></td>
	                                <td width="70" align="right"><?  echo number_format($job_possible_cut_qty,0); ?></td>
	                                <td width="60" align="right"><?  echo $job_pre_cut; ?></td>
	                                <td width="60" align="right"><a href="##" onclick="openmypage_job_total('<? echo  $job_no; ?>',<? echo $txt_production_date; ?>, 'cutting_popup_one',850,350)"> <?  echo $job_today_cut; ?></a></td>
	                                <td width="60" align="right"><a href="##" onclick="openmypage_job_wise_total('<? echo  $job_no; ?>', 'job_total_cutting_popup_one',850,350)"> <?  echo $job_total_cut; ?></a></td>
	                                <td width="60" align="right"><?  echo $job_cutting_balance; ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_possible_cut_qty-$job_total_cut,0); ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_deliv_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_deliv_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_delivery_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_deliv_cut_bal; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_iss-$job_total_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_iss-$job_total_embl_rec; ?></td>

	                                <td width="60" align="right"><?  echo $job_priv_sew; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_sew; ?></td>
	                                <td width="60" align="right"> <? echo $job_total_sew_input; ?></td>
	                                <td width="60" align="right"><? //echo $input_percentage; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_sew__bal; ?></td>
	                                <td width="70" align="right"><? echo $job_cutinhand; ?></td>
	                                <td width="70" align="right"><? echo $job_inhand; ?></td>
	                                <td width="100" align="right"><? echo $job_ready_to_sewing; ?></td>

	                                <td width="100" align="right"><? //echo  $sewing_line ?></td>
	                                <td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
	                          </tr>
	                           <tr bgcolor="#999999" style=" height:15px">
										<td width="40"><? // echo $i;?></td>
										<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
										<td width="60"></td>
										<td width="50"></td>
										<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
										<td width="80"></td>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
										<td width="80"></td>
	                                    <td width="80"></td>
										<td width="100"><strong> </strong></td>
	                                    <td width="120"><strong> </strong></td>
										<td width="100" align="right"><strong>Buyer Total:</strong></td>
										<td width="70" align="right"><? echo $buyer_total_order; ?></td>
										<td width="70" align="right"><?  echo $buyer_total_plan; ?></td>
	                                    <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
										<td width="70" align="right"><?  echo number_format($buyer_total_fabric_qty,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_total_fabric_pre,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_today_total,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_total,2);?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_bal,2); ?></td>

	                                    <td width="70" align="right"><?  echo number_format($buyer_possible_cut_qty,0); ?></td>
										<td width="60" align="right"><?  echo $buyer_pre_cut; ?></td>
										<td width="60" align="right"><?  echo $buyer_today_cut; ?></td>
										<td width="60" align="right"><?  echo $buyer_total_cut; ?></td>
										<td width="60" align="right"><?  echo $buyer_cutting_balance; ?></td>
	                                    <td width="60" align="right"><?  echo number_format($buyer_possible_cut_qty-$buyer_total_cut,0); ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_deliv_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_deliv_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_delivery_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_deliv_cut_bal; ?></td>

	                                    <td width="60" align="right"><?  echo $buyer_priv_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_iss-$buyer_total_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_iss-$buyer_total_embl_rec; ?></td>


	                                    <td width="60" align="right"><?  echo $buyer_priv_sew; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_sew; ?></td>
	                                    <td width="60" align="right"> <? echo $buyer_total_sew_; ?></td>
	                                    <td width="60" align="right"><? //echo $input_percentage; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_sew_bal; ?></td>
	                                    <td width="70" align="right"><? echo $buyer_cutinhand; ?></td>
	                                    <td width="70" align="right"><? echo $buyer_inhand; ?></td>
	                                    <td width="100" align="right"><? echo $buyer_ready_to_sewing; ?></td>

	                                    <td width="100" align="right"><? //echo  $sewing_line ?></td>
										<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
								  </tr>
	                            <tfoot>
	                                 <tr>
	                                    <th width="40"><? // echo $i;?></th>
	                                    <th width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
	                                    <th width="60"></td>
	                                    <th width="50"></td>
	                                    <th width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
	                                    <th width="80"></th>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
	                                    <th width="80"></th>
	                                    <th width="80"></th>
	                                    <th width="100"> <strong></strong></th>
	                                    <th width="120"> <strong></strong></th>
	                                    <th width="100" align="right"><strong>Grand Total:</strong></th>
	                                    <th width="70" align="right"><? echo $grand_total_order; ?></th>
	                                    <th width="70" align="right"><?  echo $grand_total_plan; ?></th>
	                                    <th width="70" align="right"><? // echo $grand_total_plan; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_total_fabric_qty,2); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_total_fabric_pre,2); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_fabric_today_total,2); ?></th>
	                                    <th width="60" align="right"><?  echo number_format($grand_fabric_total,2);?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_fabric_bal,2); ?></th>

	                                    <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_possible_cut_qty,0); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_pre_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_cutting_balance; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_possible_cut_qty-$grand_total_cut,0); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_deliv_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_deliv_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_delivery_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_deliv_cut_bal; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_iss-$grand_total_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_iss-$grand_total_embl_rec; ?></th>


	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_sew; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_sew; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><? echo $grand_total_sew_; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><? //echo $input_percentage; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_sew_bal; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><? echo $grand_cutinhand; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><? echo $grand_inhand; ?></th>
	                                    <th width="100" align="right" style="word-break:break-all;"><? echo $grand_ready_to_sewing; ?></th>

	                                    <th width="100" align="right"><? //echo  $sewing_line ?></th>
	                                    <th  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></th>
	                             </tr>
	  						</tfoot>

	                </table>
	           </div>
	  	</div>

	  	</fieldset>
	 	<?
	}
	elseif ($rpt_type==2) //ITEM WISE
	{
		if(str_replace("'","",$txt_production_date) !="")
		{
			$prod_po_arr = array();
			$sql=sql_select( "SELECT po_break_down_id from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date and company_id=$cbo_company_name");
			foreach ($sql as $val)
			{
				$prod_po_arr[$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];
			}

			$sql=sql_select( "SELECT a.po_break_down_id from PRO_EX_FACTORY_MST a, PRO_EX_FACTORY_DELIVERY_MST b where b.id=a.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and a.ex_factory_date=$txt_production_date and b.company_id=$cbo_company_name");
			foreach ($sql as $val)
			{
				$prod_po_arr[$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];
			}

			$sql=sql_select( "SELECT a.po_breakdown_id from order_wise_pro_details a,inv_transaction b where a.trans_id = b.id
			and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0 and b.transaction_date=$txt_production_date and b.company_id=$cbo_company_name");
			foreach ($sql as $val)
			{
				$prod_po_arr[$val['PO_BREAKDOWN_ID']] = $val['PO_BREAKDOWN_ID'];
			}
			// print_r($prod_po_arr);
			if(count($prod_po_arr)>0)
			{
				$prod_po_cond = where_con_using_array($prod_po_arr,0,"a.id");
			}
			else
			{
				echo "<div style='color:red;text-align:center;font-size:18px;'>Data Not Found!</div>";die;
			}
		}

		$floor_id = str_replace("'","",$cbo_floor_name);
		$floor_cond = $floor_id ? " and e.floor_id in ($floor_id) and production_type=4": "";

		// die;
		// ==================================== MAIN QUERY ==========================================
	 	$pro_date_sql="SELECT  a.id,a.job_no_mst,a.job_id,a.po_number,
 		d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,d.item_number_id as gmt_id,b.buyer_name,b.style_ref_no as style,b.job_no_prefix_num,$insert_year
 		as year ,d.color_number_id, a.file_no, a.grouping as int_ref_no ,
 		min(d.country_ship_date) as minimum_shipdate
 		from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d, pro_garments_production_mst e
 		where a.job_id=b.id and a.id=d.po_break_down_id and  a.job_id=d.job_id and a.id=e.po_break_down_id and  a.is_deleted=0 and a.status_active=1 and b.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and a.is_confirmed=1 $company_name $buyer_name $style_cond $order_cond $sql_cond $job_cond_id $country_ship_date $shipping_cond  $prod_po_cond $floor_cond
 		group by a.id,a.job_no_mst, a.job_id,a.po_number,
 		d.order_quantity,d.plan_cut_qnty ,d.item_number_id,b.buyer_name,b.style_ref_no ,b.job_no_prefix_num,d.color_number_id, a.file_no, a.grouping,b.insert_date
 		order by  b.buyer_name,a.job_no_mst";
 		// echo $pro_date_sql;die();
 		$pro_date_sql_res = sql_select($pro_date_sql);

		if (count($pro_date_sql_res) == 0 )
		{
			echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
			die();
		}

	  	$po_min_shipdate_data=array();
	  	$po_number_id = array();
	  	$job_id_arr = array();
	  	foreach($pro_date_sql_res as $row)
	  	{
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['id']=$row[csf('id')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no_mst')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['gmt_id']=$row[csf('gmt_id')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['po_quantity']+=$row[csf('order_qty')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['plan_qty']+=$row[csf('plan_qty')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['buyer_name']=$row[csf('buyer_name')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['style']=$row[csf('style')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['job_prifix']=$row[csf('job_no_prefix_num')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['year']=$row[csf('year')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['color_id']=$row[csf('color_number_id')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['file_no']=$row[csf('file_no')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['int_ref_no']=$row[csf('int_ref_no')];
		  $po_number_id[$row[csf('id')]]=$row[csf('id')];
		  $job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];

		  $po_min_shipdate_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['minimum_shipdate']=$row[csf('minimum_shipdate')];
	  	}
	  	unset($pro_date_sql);
 		// echo "<pre>";
 		// print_r($po_min_shipdate_data);
 		// echo "</pre>";

 		$po_number_id=implode(",",array_unique($po_number_id));

		$po_condition = $order_cond_lay="";
		$po_number_id_arr=explode(",", trim(str_replace("'","",$po_number_id)));
		if($db_type==2 && count($po_number_id_arr)>999)
		{
	  		$chunk_arr=array_chunk($po_number_id_arr, 999);
	  		foreach($chunk_arr as $keys=>$vals)
	  		{
	  			$po_ids=implode(",", $vals);
	  			if($po_condition=="")
	  			{
	  				$po_condition.=" and ( a.po_break_down_id in ($po_ids) ";
					$order_cond_lay.=" and ( c.order_id in($po_ids)";
	  			}
	  			else
	  			{
	  				$po_condition.=" or a.po_break_down_id in ($po_ids) ";
					$order_cond_lay.=" or c.order_id in($po_ids)";
	  			}
	  		}
	  		$po_condition.=" ) ";
	  		$order_cond_lay.=" ) ";
		}
		else
		{
			$po_condition=" and a.po_break_down_id in (".str_replace("'","",$po_number_id).")";
			$order_cond_lay=" and c.order_id in (".str_replace("'","",$po_number_id).")";
		}

		//
		/*$date_wise_po="SELECT po_break_down_id from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date";
	 	$date_wise_po_arr=array();
	 	foreach(sql_select($date_wise_po) as $vals)
	 	{
	 		$date_wise_po_arr[$vals[csf("po_break_down_id")]]=$vals[csf("po_break_down_id")];
	 	}
	 	$date_wise_po_ids=implode(",", $date_wise_po_arr);*/
	 	// GETTING ORDER QUANTITY  ADDED IN 13/10/2018

	 	// ===================================== GETTING ORDER QTY =============================================
	 	$order_qnty_array=array();
	 	$plan_cut_qnty_array=array();
		$order_qnty_sqls="SELECT a.po_break_down_id,a.color_number_id,a.order_quantity,a.item_number_id,a.plan_cut_qnty from wo_po_color_size_breakdown a where a.status_active in(1,2,3) and a.is_deleted=0 $po_condition";
		// echo $order_qnty_sqls;
		foreach(sql_select($order_qnty_sqls) as $values)
		{
		 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]['po_quantity']+=$values[csf("order_quantity")];
		 	$plan_cut_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]['plan_cut_qnty']+=$values[csf("plan_cut_qnty")];
		}
		// ===================================== CUT LAY QTY =============================================
		$sql_lay=" SELECT  b.gmt_item_id, c.order_id,  b.color_id, c.size_qty as production_qnty,entry_date from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id   and a.status_active=1 and b.status_active=1 and c.status_active=1 $order_cond_lay "; //
		// echo $sql_lay; die;
		$cut_lay_res = sql_select($sql_lay);
		$lay_data_arr = array();
		foreach ($cut_lay_res as $v)
		{

			$today = strtotime(str_replace("'","",$txt_production_date)) ;
			$prod_date = strtotime($v['ENTRY_DATE']);
			if($today == $prod_date )
			{
				$lay_data_arr[$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']]['TODAY'] +=$v['PRODUCTION_QNTY'] ;
			}
			elseif ($today > $prod_date )
			{
				$lay_data_arr[$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']]['PREV'] +=$v['PRODUCTION_QNTY'] ;
			}
		}
		/* echo "<pre>";
		print_r($lay_data_arr);
		die; */
 		// ============================== for exfactory rowspan ===========================
 		$rowspan_arr = array();
 		foreach ($po_number_data as $po_key => $po_arr)
 		{
 			foreach ($po_arr as $item_key => $item_arr)
 			{
 				foreach ($item_arr as $color_key => $color_arr)
 				{
 					$rowspan_arr[$po_key][$item_key]++;
 				}
 			}
 		}
	  	// echo "<pre>";
 		// print_r($rowspan_arr);
 		// echo "</pre>";

	   	$sew_line_arr=array();
	   	if($db_type==0)
	   	{
	   		$sql_line=sql_select("SELECT group_concat(distinct a.sewing_line) as line_id, group_concat(distinct a.floor_id) as floor_id, a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 $po_condition group by a.po_break_down_id");
	   	}
	   	if($db_type==2)
	   	{
			$floor_cond = $floor_id ? " and a.floor_id in ($floor_id) ": "";
	   		$sql_line=sql_select("SELECT listagg(cast(a.sewing_line as varchar2(2000)),',') within group (order by a.sewing_line) as line_id, listagg(cast(a.floor_id as varchar2(2000)),',') within group (order by a.floor_id) as floor_id, a.po_break_down_id from pro_garments_production_mst a where  a.production_type=4 and a.is_deleted=0 and a.status_active=1 $po_condition $floor_cond group by a.po_break_down_id");
	   	}

	   	foreach($sql_line as $row_sew)
	   	{
	   		$sew_line_arr[$row_sew[csf('po_break_down_id')]]['line']= implode(',',array_unique(explode(',',$row_sew[csf('line_id')])));
			$sew_line_arr[$row_sew[csf('po_break_down_id')]]['floor_id']= implode(',',array_unique(explode(',',$row_sew[csf('floor_id')])));
	   	}
	   	unset($sql_line);


	  	if($po_number_id=="") $po_number_id=0;
	  	$company_id = str_replace("'","",$cbo_company_name);
	  	$variable_sql=sql_select("SELECT ex_factory from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
		$ex_factory_level=$variable_sql[0][csf("ex_factory")];

	  	if($ex_factory_level==1) // for gross level
	  	{
	  		$ex_factory_sql="SELECT a.po_break_down_id as order_id, a.item_number_id as item_id,
		    sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date=$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS exf_qnty_today,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date=$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS ret_exf_qnty_today,
			sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date<$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS exf_qnty_pre,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date<$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS ret_exf_qnty_pre
			from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a
			where m.id=a.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and m.status_active=1 and m.is_deleted=0   $po_condition
			group by  a.po_break_down_id, a.item_number_id";
	  	}
	  	else // for color or color and size level
	  	{
	  		$ex_factory_sql="SELECT a.po_break_down_id as order_id, a.item_number_id as item_id, c.color_number_id as color_id,
		    sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END) AS exf_qnty_today,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_today,
			sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date<$txt_production_date THEN b.production_qnty ELSE 0 END) AS exf_qnty_pre,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date<$txt_production_date THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_pre
			from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
			where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0   $po_condition
			group by  a.po_break_down_id, a.item_number_id, c.color_number_id";
	  	}


		 	// echo $ex_factory_sql;die;
			$ex_factory_sql_result=sql_select($ex_factory_sql);
			$exFactPoQtyArray = array();
			foreach($ex_factory_sql_result as $row)
			{
				if($ex_factory_level==1)
				{
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]]['exf_qnty_today']=$row[csf('exf_qnty_today')]-$row[csf('ret_exf_qnty_today')];
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]]['exf_qnty_pre']=$row[csf('exf_qnty_pre')]-$row[csf('ret_exf_qnty_pre')];
				}
				else
				{
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['exf_qnty_today']=$row[csf('exf_qnty_today')]-$row[csf('ret_exf_qnty_today')];
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['exf_qnty_pre']=$row[csf('exf_qnty_pre')]-$row[csf('ret_exf_qnty_pre')];
				}
				$exFactPoQtyArray[$row[csf('order_id')]]['exf_qnty_today'] 	+= $row[csf('exf_qnty_today')]-$row[csf('ret_exf_qnty_today')];
				$exFactPoQtyArray[$row[csf('order_id')]]['exf_qnty_pre'] 	+= $row[csf('exf_qnty_pre')]-$row[csf('ret_exf_qnty_pre')];
			}

		  $floor_cond = $floor_id ? " and a.floor_id in ($floor_id) ": "";
		  $production_sql="SELECT a.po_break_down_id,c.color_number_id,c.item_number_id as gmt_id,
		   sum(CASE WHEN b.production_type =1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cutting_qnty,
		   sum(CASE WHEN b.production_type =1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cutting_qnty_pre,
		   sum(CASE WHEN b.production_type =2  and a.embel_name=4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS sp_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printing_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printing_qnty_pre ,
		   sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printreceived_qnty,
		   sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printreceived_qnty_pre,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS embl_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS embl_qnty_pre ,
		   sum(CASE WHEN b.production_type =3  and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS emblreceived_qnty,
		   sum(CASE WHEN b.production_type =3  and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS emblreceived_qnty_pre,
		   min(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<=".$txt_production_date." THEN a.production_date end)
		   AS min_printin_date,
		   min(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<=".$txt_production_date." THEN a.production_date end)
		   as min_embl_date,
		   sum(CASE WHEN b.production_type =4 $floor_cond and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty,
		   sum(CASE WHEN b.production_type =4 $floor_cond and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty_pre,
		   sum(CASE WHEN b.production_type =5 $floor_cond and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingout_qnty_pre,
		   sum(CASE WHEN b.production_type =5 $floor_cond and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingout_qnty_today,
		   sum(CASE WHEN b.production_type =5 $floor_cond and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sewout_rej_qty_today,
		   sum(CASE WHEN b.production_type =5 $floor_cond and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sewout_rej_qty_pre,

		   sum(CASE WHEN b.production_type =7 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty_pre,
		   sum(CASE WHEN b.production_type =7 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty_today,
		   sum(CASE WHEN b.production_type =7 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS ploy_rej_qty_pre,
		   sum(CASE WHEN b.production_type =7 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS ploy_rej_qty_today,

		    sum(CASE WHEN b.production_type =8 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS fin_qty_pre,
			sum(CASE WHEN b.production_type =8 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS fin_qty_today,
			sum(CASE WHEN b.production_type =8 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS fin_rej_qty_today,
			sum(CASE WHEN b.production_type =8 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS fin_rej_qty_pre,
			sum(CASE WHEN b.production_type =9 and a.production_date=".$txt_production_date." THEN b.PRODUCTION_QNTY ELSE 0 END) AS cut_delivery_qnty,
			sum(CASE WHEN b.production_type =9 and a.production_date<".$txt_production_date." THEN b.PRODUCTION_QNTY ELSE 0 END) AS cut_delivery_qnty_pre


		   from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c where a.id=b.mst_id
		   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		   and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id $po_condition  group by a.po_break_down_id,c.color_number_id,c.item_number_id";
		   //echo "$production_sql";die;
			//and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
			$production_mst_sql=sql_select($production_sql);
			$order_wise_fin_qty_array = array();
	       foreach($production_mst_sql as $val)
		   {
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printing_qnty']=$val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['emblreceived_qnty_pre']=$val[csf('emblreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['emblreceived_qnty']=$val[csf('emblreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['embl_qnty_pre']=$val[csf('embl_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['embl_qnty']=$val[csf('embl_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['min_printin_date']=$val[csf('min_printin_date')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['min_embl_date']=$val[csf('min_embl_date')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingout_qnty_pre']=$val[csf('sewingout_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingout_qnty_today']=$val[csf('sewingout_qnty_today')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewout_rej_qty_today']=$val[csf('sewout_rej_qty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewout_rej_qty_pre']=$val[csf('sewout_rej_qty_pre')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['poly_qnty_pre']=$val[csf('poly_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['poly_qnty_today']=$val[csf('poly_qnty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['ploy_rej_qty_pre']=$val[csf('ploy_rej_qty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['ploy_rej_qty_today']=$val[csf('ploy_rej_qty_today')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_qty_pre']=$val[csf('fin_qty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_qty_today']=$val[csf('fin_qty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_rej_qty_today']=$val[csf('fin_rej_qty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_rej_qty_pre']=$val[csf('fin_rej_qty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cut_delivery_qnty']=$val[csf('cut_delivery_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cut_delivery_qnty_pre']=$val[csf('cut_delivery_qnty_pre')];

				$order_wise_fin_qty_array[$val[csf('po_break_down_id')]]['fin_qty_pre'] 	+= $val[csf('fin_qty_pre')];
				$order_wise_fin_qty_array[$val[csf('po_break_down_id')]]['fin_qty_today'] 	+= $val[csf('fin_qty_today')];

				$po_number_gmt[]=$val[csf('po_break_down_id')];
		   }
		    /*  echo "<pre>";
			print_r($production_data_arr);
			echo "</pre>"; die(); */

			unset($production_mst_sql);

			$sql_cutting_delevery=sql_select("select a.po_break_down_id,c.color_number_id,c.item_number_id as gmt_id,
			sum(CASE WHEN  a.cut_delivery_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty,
		    sum(CASE WHEN  a.cut_delivery_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty_pre
			from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c  where a.id=b.mst_id
		    and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id   $po_condition
		    and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		    group by a.po_break_down_id,c.color_number_id,c.item_number_id");
			// echo $sql_cutting_delevery; die;
		   //and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
			foreach( $sql_cutting_delevery as $inf)
			{
				$production_data_arr[$inf[csf('po_break_down_id')]][$inf[csf('gmt_id')]][$inf[csf('color_number_id')]]['cut_delivery_qnty']=$inf[csf('cut_delivery_qnty')];

				$production_data_arr[$inf[csf('po_break_down_id')]][$inf[csf('gmt_id')]][$inf[csf('color_number_id')]]['cut_delivery_qnty_pre']=$inf[csf('cut_delivery_qnty_pre')];
			}
			unset($sql_cutting_delevery);
			$po_condition_fab=str_replace("po_break_down_id", "po_breakdown_id", $po_condition);

	        $sql_fabric_qty=sql_select("SELECT a.po_breakdown_id,a.color_id,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
		    ELSE 0 END ) AS grey_fabric_issue,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =4  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
		    ELSE 0 END ) AS grey_fabric_issue_return,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS finish_fabric_rece,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS finish_fabric_rece_return,
			/* sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS fabric_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =2 and b.item_category=2  AND a.entry_form =18 THEN a.quantity
			ELSE 0 END ) AS fabric_qty_pre, */
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS fabric_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =1 and b.item_category=2  AND a.entry_form =37 THEN a.quantity
			ELSE 0 END ) AS fabric_qty_pre,
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END )
			AS trans_in_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_pre,
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END )
			AS trans_out_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_pre
			FROM order_wise_pro_details a,inv_transaction b
		    WHERE a.trans_id = b.id
			and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0  $po_condition_fab group by a.po_breakdown_id,a.color_id");
			//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
			$fabric_pre_qty=array();
			$fabric_today_qty=array();
			$total_fabric=array();
			$fabric_balance=array();
			$fabric_wip=array();
			foreach($sql_fabric_qty as $value)
			{
				$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
				$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];

				$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
				-$value[csf("trans_out_pre")];
				$fabric_today_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty")]+$value[csf("trans_in_qty")]
				-$value[csf("trans_out_qty")];

				$po_id_fab[]=$value[csf("po_breakdown_id")];
			}


			unset($sql_fabric_qty);


	 		//*********************************************************************************************************************

		  $color_size_qty_arr=array();
		  $po_condition2=str_replace("a.po_break_down_id", "po_break_down_id", $po_condition);
		  $po_condition3=str_replace("a.po_break_down_id", "b.po_break_down_id", $po_condition);
		  $color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty
		  from wo_po_color_size_breakdown
		  where  is_deleted=0  and  status_active=1  $po_condition2
		  group by po_break_down_id,item_number_id,size_number_id,color_number_id");
		  foreach($color_size_sql as $s_id)
		  {
			$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')];
		  }

		  unset($color_size_sql);


	  	   $sql_sewing=sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum(b.cons) AS conjumction
		   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
		   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  $po_condition3 and b.cons!=0
		   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
		   //echo $sql_sewing; die();
		   $con_per_dzn=array();
		   $po_item_qty_arr=array();
		   $color_size_conjumtion=array();
	       foreach($sql_sewing as $row_sew)
	       {
		    	$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);

				$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
				$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
	       }

		   unset($sql_sewing);

		  foreach($color_size_conjumtion as $p_id=>$p_value)
		  {
			 foreach($p_value as $i_id=>$i_value)
			 {
				foreach($i_value as $c_id=>$c_value)
				 {
					 foreach($c_value as $s_id=>$s_value)
					 {
						 foreach($s_value as $b_id=>$b_value)
						 {
						   $order_color_size_qty=$b_value['plan_cut_qty'];
						   $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
						   $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
						   $conjunction_per= (($b_value['conjum']*$order_color_size_qty_per)/100);
						   $con_per_dzn[$p_id][$c_id]+=$conjunction_per;

						  // echo $p_id."**".$order_color_size_qty."**".$order_qty."**".$order_color_size_qty_per."**".$b_value['conjum']."<br>";
						 }
					 }
				 }
			 }
		  }
			//print_r($con_per_dzn);die;
		 	//**********************************************************************************************************************

		$po_condition_tna=str_replace("a.po_break_down_id", "PO_NUMBER_ID", $po_condition);
	     $sql_tna_date=sql_select("select a.po_number_id, a.task_start_date,a.task_finish_date	from  tna_process_mst a, lib_tna_task b
		 where b.task_name=a.task_number  and task_name=84 $po_condition_tna");
		 $tna_date_arr=array();
		 foreach($sql_tna_date as $tna_val)
		 {
		 $tna_date_arr[$tna_val[csf('po_number_id')]]['tna_start']= $tna_val[csf('task_start_date')];
		 $tna_date_arr[$tna_val[csf('po_number_id')]]['tna_end']= $tna_val[csf('task_finish_date')];
		 }


		 unset($sql_tna_date);

		 $job_no_cond = where_con_using_array($job_id_arr,0,"job_id");
		 $costing_per_sql=sql_select("select job_no,costing_per from wo_pre_cost_mst where status_active=1 $job_no_cond");
		 $costing_per_arr=array();
		 foreach($costing_per_sql as $cost_val)
		 {
			$costing_per_arr[$cost_val[csf('job_no')]]=$cost_val[csf('costing_per')];
		 }

		 unset($costing_per_sql);
		 $po_condition_sew_ready=str_replace("a.po_break_down_id", "PO_BREAK_DOWN_ID", $po_condition);
		 $ready_to_sewing_sql=sql_select("select po_break_down_id, color_id, sewing_qnty as sewing_qnty from ready_to_sewing_dtls where status_active=1 $po_condition_sew_ready");
		 $ready_to_sewing_arr=array();
		 foreach($ready_to_sewing_sql as $row)
		 {
			$ready_to_sewing_arr[$row[csf('po_break_down_id')]][$row[csf('color_id')]]+=$row[csf('sewing_qnty')];

		 }
		 unset($ready_to_sewing_sql);
		 $width = 5890;
		 ob_start();

			 ?>
	         <fieldset style="width:<?=  $width+20 ?>px;">
	        	   <table width="1880"  cellspacing="0"   >
	                    <tr class="form_caption" style="border:none;">
	                           <td colspan="57" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report(Item Wise)</td>
	                     </tr>
	                    <tr style="border:none;">
	                            <td colspan="57" align="center" style="border:none; font-size:16px; font-weight:bold">
	                            Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
	                            </td>
	                      </tr>
	                      <tr style="border:none;">
	                            <td colspan="57" align="center" style="border:none;font-size:12px; font-weight:bold">
	                            <? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
	                            </td>
	                      </tr>
	                </table>
	             <br />
	            <table cellspacing="0"  border="1" rules="all"  width="<?=  $width ?>" class="rpt_table">
	                <thead>
	                	<tr >
	                        <th width="40" rowspan="2">SL</th>
	                        <th width="80" rowspan="2">Buyer</th>
	                        <th width="60" rowspan="2">Job No</th>
	                        <th width="50" rowspan="2">Year</th>
	                        <th width="100" rowspan="2">Order No</th>
	                        <th width="80" rowspan="2">File No</th>
	                        <th width="80" rowspan="2">Int. Ref. No</th>
	                        <th width="80" rowspan="2">TNA Start Date</th>
	                        <th width="80" rowspan="2">TNA End Date</th>
	                        <th width="80" rowspan="2">First Shipment Date</th>
	                        <th width="100" rowspan="2">Style</th>
	                        <th width="120" rowspan="2">Item</th>
	                        <th width="100" rowspan="2">Color</th>
	                        <th width="70" rowspan="2">Order Qty.</th>
	                        <th width="70" rowspan="2">Plan Cut Qty.</th>
	                        <th width="70" rowspan="2">Fin Fab Cons/Dzn.</th>
	                        <th width="70" rowspan="2">Fabric Required Qty</th>
	                        <th width="240" colspan="4">Fabric Receive Qty.</th>
	                        <th width="70" rowspan="2">Possible Cut Qty</th>
							<th width="210" colspan="3">Lay Quantity</th>
	                        <th width="300" colspan="5">Cutting</th>
	                        <th width="60" rowspan="2"> Cutting WIP</th>
	                        <th width="240" colspan="4">Cutting Delivery To Input</th>
	                        <th width="180" colspan="3">Delivery to Print</th>
	                        <th width="180" colspan="3">Receive from Print</th>
	                        <th width="60" rowspan="2">Print WIP</th>
	                        <th width="180" colspan="3">Delivery to Emb.</th>
	                        <th width="180" colspan="3">Receive from Emb.</th>
	                        <th width="60" rowspan="2">Emb. WIP</th>

	                        <th width="300" colspan="5"> Sewing Input</th>
	                        <th width="70" rowspan="2">Cutting Inhand</th>
	                        <th width="70" rowspan="2">Input Inhand</th>
	                        <th width="100" rowspan="2">Ready To Sewing</th>
	                        <th width="100" rowspan="2">Line No</th>

	                        <th width="60" rowspan="2">Unit(Floor)</th>
	                        <th width="80" rowspan="2">Cutable Pcs as per Fabric Received</th>
	                        <th width="80" rowspan="2">Cutting KG</th>
	                        <th width="80" rowspan="2">Cutting Balance Fabric</th>

	                        <th width="210" colspan="3">Sewing Output</th>
	                        <th width="70" rowspan="2">Today Sewing Reject</th>
	                        <th width="70" rowspan="2">Sewing Reject Total</th>
	                        <th width="70" rowspan="2">Sewing WIP</th>

	                        <th width="210" colspan="3">Iron Entry</th>

	                        <th width="70" rowspan="2">Today Iron Reject</th>
	                        <th width="70" rowspan="2">Iron Reject Total</th>
	                        <th width="70" rowspan="2">Iron WIP</th>

	                        <th width="210" colspan="3">Packing & Finishing</th>

	                        <th width="70" rowspan="2">Today Finishing Reject</th>
	                        <th width="70" rowspan="2">Finishing Reject Total</th>
	                        <th width="70" rowspan="2">Pac &Fin. WIP</th>
	                        <th width="210" colspan="3">Ex-Factory</th>
	                        <th width="70" rowspan="2">Ex-Fac. WIP</th>
	                        <th rowspan="2">Remarks</th>
	                    </tr>
	                    <tr>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Bal.</th>

							<th width="60" rowspan="2">Prev</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Cutting Persent </th>
	                        <th width="60" rowspan="2" title="Plancut - Cutting Qty">Bal.</th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Bal.</th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today</th>
	                        <th width="60" rowspan="2">Total</th>
	                        <th width="60" rowspan="2">%</th>
	                        <th width="60" rowspan="2" title="Plancut - Input">Balance</th>

	                         <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>

	                        <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>

	                        <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>

	                        <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>
	                    </tr>
	                </thead>
	            </table>

	             <div style="max-height:425px; overflow-y:scroll; width:<?=  $width+20 ?>px;" id="scroll_body">
	                    <table  border="1" class="rpt_table"  width="<?=  $width ?>" rules="all" id="table_body" >
	                    <?
	                      $total_cut = $total_print_iss = $total_embl_iss = $total_wash_iss = $total_sp_iss = $total_print_receive = $total_sp_rec = $total_embl_rec = $total_wash_receive = $total_sp_rec = $total_sew_input = $total_sew_out = $total_delivery_cut = $cutting_balance = $print_issue_balance = $print_rec_balance = $deliv_cut_bal = $total_sew_input_balance = $input_percentage = $inhand = $buyer_total_order = $buyer_total_plan = $buyer_total_fabric_qty = $buyer_total_fabric_pre = $buyer_fabric_total = $buyer_fabric_today_total = $buyer_fabric_bal = $buyer_pre_cut = $buyer_today_cut = $buyer_total_cut = $buyer_cutting_balance = $buyer_priv_print_iss = $buyer_today_print_iss = $buyer_print_issue_balance = $buyer_priv_print_rec = $buyer_today_print_rec = $buyer_total_print_rec = $buyer_print_rec_balance = $buyer_priv_deliv_cut = $buyer_today_deliv_cut = $buyer_total_delivery_cut = $buyer_deliv_cut_bal = $buyer_priv_sew = $buyer_today_sew = $buyer_total_sew_ = $buyer_total_sew__bal = $buyer_inhand = 0;
						  $buyer_arr=array();
						  $job_arr=array();
	                      $i=1;$k=1;


		  //echo "jahid";die;

	  	foreach($po_number_data as $po_id=>$po_arr)
		{
			$po_wise_total_order = $po_wise_total_plan = $po_wise_total_fabric_qty = $po_wise_total_fabric_pre = $po_wise_fabric_total = $po_wise_fabric_today_total = $po_wise_fabric_bal = $po_wise_pre_cut = $po_wise_today_cut = $po_wise_total_cut = $po_wise_cutting_balance = $po_wise_priv_print_iss = $po_wise_today_print_iss = $po_wise_print_issue_balance = $po_wise_priv_print_rec = $po_wise_today_print_rec = $po_wise_total_print_rec = $po_wise_print_rec_balance = $po_wise_priv_deliv_cut = $po_wise_today_deliv_cut = $po_wise_total_delivery_cut = $po_wise_deliv_cut_bal = $po_wise_priv_sew = $po_wise_today_sew = $po_wise_total_sew_ = $po_wise_total_sew__bal = $po_wise_inhand = $po_prev_lay_qty=$po_today_lay_qty=$po_total_lay_qty= $po_wise_prev_lay_qty = $po_wise_today_lay_qty = $po_wise_total_lay_qty = 0;

		foreach($po_arr as $item_id=>$item_arr)
		   {
		   	$r=0;
			 foreach($item_arr as $color_id=>$color_arr)
		     {
	 				if($i!=1)
					{
						if(!in_array($po_number_data[$po_id][$item_id][$color_id]['job_no'],$job_arr))
						{
								?>
								   <tr bgcolor="#CCCCCC" id="">
										<td width="40"><? // echo $i;?></td>
										<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
										<td width="60"></td>
										<td width="50"></td>
										<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
										<td width="80"></td>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
										<td width="80"></td>
										<td width="80"></td>
	                                    <td width="100"><strong></strong></td>
										<td width="120"><strong></strong></td>
										<td width="100" align="right"><strong>Job Total:</strong></td>
										<td width="70" align="right"><? echo $job_total_order; ?></td>
										<td width="70" align="right"><?  echo $job_total_plan; ?></td>
										<td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
										<td width="70" align="right"><?  echo number_format($job_total_fabric_qty,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_total_fabric_pre,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_today_total,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_total,2);?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_bal,2); ?></td>

										<td width="70" align="right"><?  echo number_format($job_possible_cut_qty,0); ?></td>
										<td width="70" align="right"><?= $job_prev_lay_qty; ?></td>
										<td width="70" align="right"><?= $job_today_lay_qty; ?></td>
										<td width="70" align="right"><?= $job_total_lay_qty; ?></td>
										<td width="60" align="right"><?  echo $job_pre_cut; ?></td>
										<td width="60" align="right"><?  echo $job_today_cut; ?></td>
										<td width="60" align="right"><?  echo $job_total_cut; ?></td>
										<td width="60" align="right"><?  //echo $job_total_cut; ?></td>
										<td width="60" align="right"><?  echo $job_cutting_balance; ?></td>
										<td width="60" align="right"><?  echo number_format($job_possible_cut_qty,0)-$job_total_cut; ?></td>
										<td width="60" align="right"><?  echo $job_priv_deliv_cut; ?></td>
										<td width="60" align="right"><?  echo $job_today_deliv_cut; ?></td>
										<td width="60" align="right"><?  echo $job_total_delivery_cut; ?></td>
										<td width="60" align="right"><?  echo $job_deliv_cut_bal; ?></td>
										<td width="60" align="right"><?  echo $job_priv_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_today_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_priv_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_today_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_iss-$job_total_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_priv_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_today_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_priv_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_today_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_iss-$job_total_embl_rec; ?></td>

										<td width="60" align="right"><?  echo $job_priv_sew; ?></td>
										<td width="60" align="right"><?  echo $job_today_sew; ?></td>
										<td width="60" align="right"> <? echo $job_total_sew_input; ?></td>
										<td width="60" align="right"><? //echo $input_percentage; ?></td>
										<td width="60" align="right"><?  echo $job_total_sew__bal; ?></td>
										<td width="70" align="right"><? echo $job_cutinhand; ?></td>
										<td width="70" align="right"><? echo $job_inhand; ?></td>
										<td width="100" align="right"><? echo $job_ready_to_sewing; ?></td>
										<td width="100" align="right"><? //echo  $sewing_line ?></td>

	                                    <td width="60" align="right"><? //echo  $unit ?></td>
	                                    <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_bal ?></td>


	                                   	 <td width="70" align="right"><? echo  $job_priv_sewout; ?></td>
	                                     <td width="70" align="right"><? echo  $job_today_sewout ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sew_out_bal; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sew_out_reject_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sew_out_reject_bal; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sewingout_wip_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_poly_qnty_pre; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_poly_qnty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_poly_qnty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_ploy_rej_qty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_ploy_rej_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_tot_poly_wip; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_qnty_pre; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_qnty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_qnty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_rej_qty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_rej_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_tot_fin_wip; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_exf_qty_pre; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_exf_qty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_exf_tot_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_tot_exf_wip; ?></td>


										<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
								  </tr>
								<?
								  $job_inhand = $job_cutinhand = $job_possible_cut_qty = $job_total_order = $job_total_plan = $job_total_fabric_qty = $job_total_fabric_pre = $job_fabric_total = $job_fabric_today_total = $job_fabric_bal = $job_pre_cut = $job_today_cut = $job_total_cut = $job_cutting_balance = $job_priv_print_iss = $job_today_print_iss = $job_total_print_iss = $job_print_issue_balance = $job_priv_print_rec = $job_today_print_rec = $job_total_print_rec = $job_print_rec_balance = $job_priv_deliv_cut = $job_today_deliv_cut = $job_total_delivery_cut = $job_deliv_cut_bal = $job_priv_sew = $job_today_sew = $job_total_sew_input=$job_today_sewout=$job_total_sew_out_bal = $job_total_sew_out_reject_today = $job_total_sew_out_reject_bal = $job_total_sewingout_wip_qty = $job_total_poly_qnty_pre=$job_total_poly_qnty_today=$job_total_poly_qnty=$job_total_ploy_rej_qty_pre=$job_total_ploy_rej_qty_pre=$job_total_ploy_rej_qty_today=$job_total_ploy_rej_qty=$job_total_tot_poly_wip = $job_total_fin_qnty_pre=$job_total_fin_qnty_today=$job_total_fin_qnty=$job_total_fin_rej_qty_pre=$job_total_fin_rej_qty_today=$job_total_fin_rej_qty=$job_total_tot_fin_wip = $job_total_exf_qty_pre=$job_total_exf_qty_today=$job_total_exf_tot_qty=$job_total_tot_exf_wip = $job_total_sew__bal = $job_priv_print_iss = $job_today_print_iss = $job_total_print_iss = $job_priv_embl_iss = $job_priv_print_rec = $job_today_embl_iss = $job_total_embl_iss = $job_today_wash_iss = $job_priv_wash_iss = $job_today_sp_iss = $job_total_wash_iss = $job_total_sp_iss = $job_priv_sp_iss = $job_priv_print_rec = $job_today_print_rec = $job_total_print_rec = $job_priv_wash_rec = $job_today_wash_rec = $job_total_wash_rec = $job_priv_embl_rec = $job_today_embl_rec = $job_total_embl_rec = $job_priv_sp_rec = $job_today_sp_rec = $job_total_sp_rec = $job_ready_to_sewing = $job_priv_sewout=$job_prev_lay_qty=$job_today_lay_qty=$job_total_lay_qty=0;
						  }
					}


					if($i!=1)
					{
					 	if( !in_array($po_number_data[$po_id][$item_id][$color_id]['buyer_name'],$buyer_arr))
						{

							?>
							<tr bgcolor="#999999" style="height:15px">
							<td width="40"><? // echo $i;?></td>
							<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
							<td width="60"></td>
							<td width="50"></td>
							<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
							<td width="80"></td>
	                        <td width="80"></td>
	                        <td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="100"><strong> </strong></td>
	                        <td width="120"><strong> </strong></td>
							<td width="100" align="right"><strong>Buyer Total:</strong></td>
							<td width="70" align="right"><? echo $buyer_total_order; ?></td>
							<td width="70" align="right"><?  echo $buyer_total_plan; ?></td>
							<td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
							<td width="70" align="right"><?  echo number_format($buyer_total_fabric_qty,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_total_fabric_pre,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_today_total,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_total,2);?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_bal,2); ?></td>

							<td width="70" align="right"><?  echo number_format($buyer_possible_cut_qty,0); ?></td>
							<td width="70" align="right" > <?= $buyer_prev_lay_qty; ?> </td>
							<td width="70" align="right" > <?= $buyer_today_lay_qty; ?> </td>
							<td width="70" align="right" > <?= $buyer_total_lay_qty; ?> </td>

							<td width="60" align="right"><?  echo $buyer_pre_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_cut; ?></td>
							<td width="60" align="right"><?  //echo $buyer_total_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_cutting_balance; ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_possible_cut_qty,0)-$buyer_total_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_deliv_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_deliv_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_delivery_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_deliv_cut_bal; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_print_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_print_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_print_rec; ?></td>
						   <td width="60" align="right"><?   echo $buyer_total_print_iss-$buyer_total_print_rec; ?></td>

							<td width="60" align="right"><?  echo $buyer_priv_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_iss-$buyer_total_embl_rec; ?></td>


							<td width="60" align="right"><?  echo $buyer_priv_sew; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_sew; ?></td>
							<td width="60" align="right"> <? echo $buyer_total_sew_; ?></td>
							<td width="60" align="right"><? //echo $input_percentage; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_sew_bal; ?></td>
							<td width="70" align="right"><? echo $buyer_cutinhand; ?></td>
							<td width="70" align="right"><? echo $buyer_inhand; ?></td>
							<td width="100" align="right"><? echo $buyer_ready_to_sewing; ?></td>

							<td width="100" align="right"><? //echo  $sewing_line ?></td>
	                        <td width="60" align="right"><? //echo  $unit ?></td>
	                        <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                        <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                        <td width="80" align="right"><? //echo  $cutting_bal ?></td>

	                        <td width="70" align="right"><? echo  $buyer_priv_sew_out ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_today ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_bal; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_bal; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sewingout_wip_qty ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_poly_qnty_pre; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_poly_qnty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_poly_qnty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_tot_poly_wip; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_qnty_pre; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_qnty_today;?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_qnty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_tot_fin_wip; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_exf_qty_pre; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_exf__qty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_exf_tot_qty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_tot_exf_wip ;?></td>


							<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
					  	</tr>
								<?
								  $buyer_cutinhand=$buyer_ready_to_sewing = $buyer_possible_cut_qty = $buyer_total_order = $buyer_total_plan = $buyer_total_fabric_qty = $buyer_total_fabric_pre = $buyer_fabric_total = $buyer_fabric_today_total = $buyer_fabric_bal = $buyer_pre_cut = $buyer_today_cut = $buyer_total_cut = $buyer_cutting_balance = $buyer_priv_print_iss = $buyer_today_print_iss = $buyer_total_print_iss = $buyer_print_issue_balance = $buyer_priv_print_rec = $buyer_today_print_rec = $buyer_total_print_rec = $buyer_print_rec_balance = $buyer_priv_deliv_cut = $buyer_today_deliv_cut = $buyer_total_delivery_cut = $buyer_deliv_cut_bal = $buyer_priv_sew = $buyer_today_sew=$buyer_priv_sew_out = $buyer_total_sew_=$buyer_total_sew_out_today = $buyer_total_sew_out_reject_today=$buyer_total_sew_out_reject_bal=$buyer_total_sewingout_wip_qty = $buyer_total_poly_qnty_pre=$buyer_total_poly_qnty_today=$buyer_total_poly_qnty=$buyer_total_ploy_rej_qty_pre=$buyer_total_ploy_rej_qty_today=$buyer_total_ploy_rej_qty=$buyer_total_tot_poly_wip = $buyer_total_fin_qnty_pre=$buyer_total_fin_qnty_today=$buyer_total_fin_qnty=$buyer_total_fin_rej_qty_pre=$buyer_total_fin_rej_qty_today=$buyer_total_fin_rej_qty=$buyer_total_tot_fin_wip = $buyer_total_exf_qty_pre=$buyer_total_exf__qty_today=$buyer_total_exf_tot_qty=$buyer_total_tot_exf_wip = $buyer_total_sew__bal = $buyer_inhand = $buyer_priv_print_iss = $buyer_today_print_iss = $buyer_total_print_iss = $buyer_priv_embl_iss = $buyer_priv_print_rec = $buyer_today_embl_iss = $buyer_total_embl_iss = $buyer_today_wash_iss = $buyer_priv_wash_iss = $buyer_today_sp_iss = $buyer_total_wash_iss = $buyer_total_sp_iss = $buyer_priv_sp_iss = $buyer_priv_print_rec = $buyer_today_print_rec = $buyer_total_print_rec = $buyer_priv_wash_rec = $buyer_today_wash_rec = $buyer_total_wash_rec = $buyer_priv_embl_rec = $buyer_today_embl_rec = $buyer_total_embl_rec = $buyer_priv_sp_rec = $buyer_today_sp_rec = $buyer_total_sp_rec=$buyer_prev_lay_qty=$buyer_today_lay_qty=$buyer_total_lay_qty=0;
							}
					  }
					//***********************for line****************************************************************************************
					$line_id_all=$sew_line_arr[$po_id]['line'];
					$line_name="";
					foreach(array_unique(explode(",",$line_id_all)) as $l_id)
					{
						if($line_name!="") $line_name.=",";
						if($prod_reso_allo==1)
						{
						$line_name.= $lineArr[$prod_reso_arr[$l_id]];
						}
						else
						{
						$line_name.= $lineArr[$l_id];
						}
					}

					$floor_id_all=$sew_line_arr[$po_id]['floor_id'];
					$floor_name="";
					foreach(array_unique(explode(",",$floor_id_all)) as $f_id)
					{
						if($floor_name!="") $floor_name.=",";
						$floor_name.= $floor_arr[$f_id];
					}

					$costing_per=$costing_per_arr[$po_number_data[$po_id][$item_id][$color_id]['job_no']];
					if($costing_per==1)
					{
						$costing_per_qty=12;
					}
					else if($costing_per==2)
					{
						$costing_per_qty=1;
					}
					else if($costing_per==3)
					{
						$costing_per_qty=24;
					}
					else if($costing_per==4)
					{
						$costing_per_qty=36;
					}
					else if($costing_per==5)
					{
						$costing_per_qty=48;
					}

					//$fabric_wip=$fabric_wip[$po_id][$color_id]['issue']-$fabric_wip[$po_id][$color_id]['receive'];
					$ready_to_sewing=$ready_to_sewing_arr[$po_id][$color_id];
				    $fabric_pre=$fabric_pre_qty[$po_id][$color_id];
					$fabric_today=$fabric_today_qty[$po_id][$color_id];
					$total_fabric=$fabric_pre+$fabric_today;
					$possible_cut_qty=($total_fabric/($con_per_dzn[$po_id][$color_id]/$costing_per_qty));
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					// $con = $con_per_dzn[$po_id][$color_id]*12/$costing_per_qty;
					$fabric_qty=($plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty']*$con_per_dzn[$po_id][$color_id])/$costing_per_qty;

					//echo $po_number_data[$po_id][$item_id][$color_id]['plan_qty'].'=='.$con_per_dzn[$po_id][$color_id].'=='.$costing_per_qty.', ';
				    $fabric_balance=$fabric_qty-$total_fabric;
					$total_cut=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$cutting_balance=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'] - $total_cut;
					// echo $po_number_data[$po_id][$item_id][$color_id]['plan_qty'].'-'.$total_cut;die('go to hell');
					$total_print_iss=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre']+$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$total_embl_iss=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre']+$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$total_embl_rec=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre']+$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$total_print_receive=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];

					//echo $inhand."***";die;
					$print_balance=$total_print_iss-$total_print_receive;
					$embl_balance=$total_embl_iss-$total_embl_rec;
					$wash_balance=$total_wash_iss-$total_wash_receive;
					$sp_balance=$total_sp_iss-$total_sp_rec;
					$total_emblishment_iss=$total_embl_iss+$total_wash_iss+$total_print_iss+$total_sp_iss;
					//$total_emblishment_rec=$total_wash_receive+$total_embl_rec+$total_sp_rec+$total_print_rec;
					$print_issue_balance=$total_emblishment_iss-$total_cut;
					$print_rec_balance=$total_emblishment_rec-$total_emblishment_iss;

					// CUT LAY DATA
					$lay_data 		= $lay_data_arr[$po_id][$item_id][$color_id];
					$prev_lay_qty 	= $lay_data['PREV'];
					$today_lay_qty 	= $lay_data['TODAY']  ;
					$total_lay_qty 	= $prev_lay_qty + $today_lay_qty;


					$total_delivery_cut=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$deliv_cut_bal=($total_cut)-$total_delivery_cut;
					$total_sew_input=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$total_sew_input_balance=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'] - $total_sew_input;
					$input_percentage=($total_sew_input/$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'])*100;
					// $input_percentage plan cut qty change to po qty by saeed vie;
					$sewingout_qnty_pre=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_pre'];
					$sewingout_qnty_today=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_today'];

					$sewout_rej_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['sewout_rej_qty_today'];
					$sewout_rej_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['sewout_rej_qty_pre'];

					$fin_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['fin_qty_pre'];
					$fin_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['fin_qty_today'];
					$fin_rej_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['fin_rej_qty_pre'];
					$fin_rej_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['fin_rej_qty_today'];

					$poly_qnty_pre=$production_data_arr[$po_id][$item_id][$color_id]['poly_qnty_pre'];
					$poly_qnty_today=$production_data_arr[$po_id][$item_id][$color_id]['poly_qnty_today'];
					$ploy_rej_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['ploy_rej_qty_pre'];
					$ploy_rej_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['ploy_rej_qty_today'];

					$total_poly_qnty=$poly_qnty_pre+$poly_qnty_today;
					$total_ploy_rej_qty=$ploy_rej_qty_pre+$ploy_rej_qty_today;


					$total_sewingout_qnty_bal=$sewingout_qnty_pre+$sewingout_qnty_today;
					$total_sewingout_reject_qty_bal=$sewout_rej_qty_today+$sewout_rej_qty_pre;
					$total_sewingout_wip_qty=$total_sewingout_qnty_bal-$total_sew_input;
					$tot_poly_wip=(($total_poly_qnty+$total_ploy_rej_qty)-$total_sewingout_qnty_bal);

					$fin_qty_pre=$fin_qty_pre;
					$fin_qty_today=$fin_qty_today;
					$fin_rej_qty_today=$fin_rej_qty_today;
					$fin_rej_qty_pre=$fin_rej_qty_pre;

					$tot_fin_qnty=$fin_qty_pre+$fin_qty_today;
					$tot_fin_reject_qty=$fin_rej_qty_today+$fin_rej_qty_pre;
					// $tot_fin_wip=(($tot_fin_qnty+$tot_fin_reject_qty)-$total_poly_qnty);
					$tot_fin_wip=$total_sewingout_qnty_bal-$tot_fin_qnty;
					if($ex_factory_level==1) // when gross level
					{
						$exf_qnty_today=$production_data[$po_id][$item_id]['exf_qnty_today'];
						$exf_qnty_pre=$production_data[$po_id][$item_id]['exf_qnty_pre'];
					}
					else
					{
						$exf_qnty_today=$production_data[$po_id][$item_id][$color_id]['exf_qnty_today'];
						$exf_qnty_pre=$production_data[$po_id][$item_id][$color_id]['exf_qnty_pre'];
					}

					//echo $exf_qnty_today.'='.$exf_qnty_pre;
					$tot_exf_qnty=$exf_qnty_today+$exf_qnty_pre;
					$ex_fact_wip=($tot_fin_qnty-$tot_exf_qnty);



					$cutting_inhand=0;
					$cutting_inhand=$total_cut-$total_delivery_cut;
					$inhand=0;
					if($total_print_iss!=0 && $total_embl_iss!=0)
					{
						if(date("Y-m-d",strtotime($production_data_arr[$po_id][$item_id][$color_id]['min_printin_date']))<=date("Y-m-d",strtotime($production_data_arr[$po_id][$item_id][$color_id]['min_embl_date'])))
					    {
				        $inhand=($total_delivery_cut+$total_embl_rec)-($total_sew_input+$total_print_iss);
						}
						else
						{
						$inhand=($total_delivery_cut+$total_embl_iss)-($total_sew_input+$total_print_receive);
						}
					}
					else if ($total_print_iss!=0) { $inhand=(($total_delivery_cut+$total_print_receive)-($total_print_iss+$total_sew_input));}
					else if ($total_embl_iss!=0) { $inhand=($total_delivery_cut+$total_embl_rec)-($total_embl_iss+$total_sew_input);}
					else if($total_delivery_cut!=0)$inhand=$total_delivery_cut-$total_sew_input;
					else $inhand=0;  // $inhand=$total_cut-$total_sew_input;
					//if($inhand<0) $inhand=0;


					//for job total *******************************************************************************************************
					$job_possible_cut_qty+=$possible_cut_qty;
					$job_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$job_total_plan+=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];
					$job_total_fabric_qty+=$fabric_qty;
					$job_total_fabric_pre+=$fabric_pre;
					$job_fabric_today_total+=$fabric_today;
					$job_fabric_total+=$total_fabric;
					$job_fabric_bal+=$fabric_balance;
					$job_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$job_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$job_total_cut+=$total_cut;
					$job_cutting_balance+=$cutting_balance;
					$job_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$job_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$job_total_print_iss+=$total_print_iss;
					$job_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$job_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$job_total_embl_iss+=$total_embl_iss;
					$job_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$job_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$job_total_wash_iss+=$total_wash_iss;
					$job_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$job_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$job_total_sp_iss+=$total_sp_iss;
					$job_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$job_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$job_total_print_rec+=$total_print_receive;
					$job_print_issue_balance=$print_issue_balance;
					$job_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$job_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$job_total_wash_rec+=$total_wash_receive;
					$job_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$job_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$job_total_embl_rec+=$total_embl_rec;
					$job_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$job_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$job_total_sp_rec+=$total_sp_rec;
					$job_print_rec_balance+=$print_rec_balance;
					$job_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$job_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$job_total_delivery_cut+=$total_delivery_cut;
					$job_deliv_cut_bal+=$deliv_cut_bal;
					$job_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$job_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$job_priv_sewout+=$sewingout_qnty_pre;
					$job_today_sewout+=$sewingout_qnty_today;
					$job_priv_sew_input+=$total_sew_input;
					$job_total_sew_bal+=$total_sew_input_balance;
					$job_total_sew_out_bal+=$total_sewingout_qnty_bal;
					$job_total_sew_out_reject_bal+=$total_sewingout_reject_qty_bal;
					$job_total_sew_out_reject_today+=$sewout_rej_qty_today;
					$job_total_sewingout_wip_qty+=$total_sewingout_wip_qty;


					$job_prev_lay_qty 	+= $prev_lay_qty;
					$job_today_lay_qty 	+= $today_lay_qty;
					$job_total_lay_qty 	+= $total_lay_qty;

					$job_total_poly_qnty_pre+=$poly_qnty_pre;
					$job_total_poly_qnty_today+=$poly_qnty_today;
					$job_total_poly_qnty+=$total_poly_qnty;

					$job_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;
					$job_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$job_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$job_total_tot_poly_wip+=$tot_poly_wip;
					//Fin and Pack
					$job_total_fin_qnty_pre+=$fin_qty_pre;
					$job_total_fin_qnty_today+=$fin_qty_today;
					$job_total_fin_qnty+=$tot_fin_qnty;

					$job_total_fin_rej_qty_pre+=$fin_rej_qty_pre;
					$job_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$job_total_fin_rej_qty+=$tot_fin_reject_qty;
					$job_total_tot_fin_wip+=$tot_fin_wip;

					$job_total_exf_qty_pre+=$exf_qnty_pre;
					$job_total_exf_qty_today+=$exf_qnty_today;
					$job_total_exf_tot_qty+=$tot_exf_qnty;
					$job_total_tot_exf_wip+=$ex_fact_wip;


					$job_inhand+=$inhand;
					$job_cutinhand+=$cutting_inhand;
					$job_ready_to_sewing+=$ready_to_sewing;
					//buyer sub total **************************************************************************************************
					$buyer_possible_cut_qty+=$possible_cut_qty;
					$buyer_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$buyer_total_plan+=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];
					$buyer_total_fabric_qty+=$fabric_qty;
					$buyer_total_fabric_pre+=$fabric_pre;
					$buyer_fabric_today_total+=$fabric_today;
					$buyer_fabric_total+=$total_fabric;
					$buyer_fabric_bal+=$fabric_balance;
					$buyer_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$buyer_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$buyer_total_cut+=$total_cut;
					$buyer_cutting_balance+=$cutting_balance;
					$buyer_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$buyer_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$buyer_total_print_iss+=$total_print_iss;
					$buyer_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$buyer_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$buyer_total_embl_iss+=$total_embl_iss;
					$buyer_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$buyer_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$buyer_total_wash_iss+=$total_wash_iss;
					$buyer_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$buyer_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$buyer_total_sp_iss+=$total_sp_iss;
					$buyer_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$buyer_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$buyer_total_print_rec+=$total_print_receive;
					$buyer_print_issue_balance=$print_issue_balance;
					$buyer_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$buyer_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$buyer_total_wash_rec+=$total_wash_receive;
					$buyer_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$buyer_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$buyer_total_embl_rec+=$total_embl_rec;
					$buyer_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$buyer_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$buyer_total_sp_rec+=$total_sp_rec;
					$buyer_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$buyer_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$buyer_total_delivery_cut+=$total_delivery_cut;
					$buyer_deliv_cut_bal+=$deliv_cut_bal;
					$buyer_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$buyer_priv_sew_out+=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_pre'];
					$buyer_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$buyer_total_sew_+=$total_sew_input;
					$buyer_total_sew_bal+=$total_sew_input_balance;
					$buyer_total_sew_out_pre+=$sewingout_qnty_pre;
					$buyer_total_sew_out_today+=$sewingout_qnty_today;
					$buyer_total_sew_out_bal+=$total_sewingout_qnty_bal;
					$buyer_total_sew_out_reject_today+=$sewout_rej_qty_today;
					$buyer_total_sew_out_reject_bal+=$total_sewingout_reject_qty_bal;
					$buyer_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$buyer_total_poly_qnty_pre+=$poly_qnty_pre;
					$buyer_total_poly_qnty_today+=$poly_qnty_today;
					$buyer_total_poly_qnty+=$total_poly_qnty;
					$buyer_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;

					$buyer_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$buyer_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$buyer_total_tot_poly_wip+=$tot_poly_wip;

					$buyer_prev_lay_qty 	+= $prev_lay_qty;
					$buyer_today_lay_qty 	+= $today_lay_qty;
					$buyer_total_lay_qty 	+= $total_lay_qty;


					// Fin & Pack
					$buyer_total_fin_qnty_pre+=$fin_qty_pre;
					$buyer_total_fin_qnty_today+=$fin_qty_today;
					$buyer_total_fin_qnty+=$tot_fin_qnty;
					$buyer_total_fin_rej_qty_pre+=$fin_rej_qty_pre;

					$buyer_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$buyer_total_fin_rej_qty+=$tot_fin_reject_qty;
					$buyer_total_tot_fin_wip+=$tot_fin_wip;

					$buyer_total_exf_qty_pre+=$exf_qnty_pre;
					$buyer_total_exf__qty_today+=$exf_qnty_today;
					$buyer_total_exf_tot_qty+=$tot_exf_qnty;
					$buyer_total_tot_exf_wip+=$ex_fact_wip;

					$buyer_inhand+=$inhand;
					$buyer_cutinhand+=$cutting_inhand;
					$buyer_ready_to_sewing+=$ready_to_sewing;

					//buyer sub total **************************************************************************************************
					$po_wise_possible_cut_qty+=$possible_cut_qty;
					$po_wise_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$po_wise_total_plan+=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];
					$po_wise_total_fabric_qty+=$fabric_qty;
					$po_wise_total_fabric_pre+=$fabric_pre;
					$po_wise_fabric_today_total+=$fabric_today;
					$po_wise_fabric_total+=$total_fabric;
					$po_wise_fabric_bal+=$fabric_balance;
					$po_wise_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$po_wise_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$po_wise_total_cut+=$total_cut;
					$po_wise_cutting_balance+=$cutting_balance;
					$po_wise_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$po_wise_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$po_wise_total_print_iss+=$total_print_iss;
					$po_wise_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$po_wise_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$po_wise_total_embl_iss+=$total_embl_iss;
					$po_wise_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$po_wise_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$po_wise_total_wash_iss+=$total_wash_iss;
					$po_wise_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$po_wise_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$po_wise_total_sp_iss+=$total_sp_iss;
					$po_wise_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$po_wise_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$po_wise_total_print_rec+=$total_print_receive;
					$po_wise_print_issue_balance=$print_issue_balance;
					$po_wise_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$po_wise_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$po_wise_total_wash_rec+=$total_wash_receive;
					$po_wise_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$po_wise_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$po_wise_total_embl_rec+=$total_embl_rec;
					$po_wise_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$po_wise_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$po_wise_total_sp_rec+=$total_sp_rec;
					$po_wise_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$po_wise_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$po_wise_total_delivery_cut+=$total_delivery_cut;
					$po_wise_deliv_cut_bal+=$deliv_cut_bal;
					$po_wise_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$po_wise_priv_sew_out+=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_pre'];
					$po_wise_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$po_wise_total_sew_+=$total_sew_input;
					$po_wise_total_sew_bal+=$total_sew_input_balance;
					$po_wise_total_sew_out_pre+=$sewingout_qnty_pre;
					$po_wise_total_sew_out_today+=$sewingout_qnty_today;
					$po_wise_total_sew_out_bal+=$total_sewingout_qnty_bal;
					$po_wise_total_sew_out_reject_today+=$sewout_rej_qty_today;
					$po_wise_total_sew_out_reject_bal+=$total_sewingout_reject_qty_bal;
					$po_wise_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$po_wise_total_poly_qnty_pre+=$poly_qnty_pre;
					$po_wise_total_poly_qnty_today+=$poly_qnty_today;
					$po_wise_total_poly_qnty+=$total_poly_qnty;
					$po_wise_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;

					$po_wise_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$po_wise_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$po_wise_total_tot_poly_wip+=$tot_poly_wip;


					$po_wise_prev_lay_qty 	+= $prev_lay_qty;
					$po_wise_today_lay_qty 	+= $today_lay_qty;
					$po_wise_total_lay_qty 	+= $total_lay_qty;

					// Fin & Pack
					$po_wise_total_fin_qnty_pre+=$fin_qty_pre;
					$po_wise_total_fin_qnty_today+=$fin_qty_today;
					$po_wise_total_fin_qnty+=$tot_fin_qnty;
					$po_wise_total_fin_rej_qty_pre+=$fin_rej_qty_pre;

					$po_wise_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$po_wise_total_fin_rej_qty+=$tot_fin_reject_qty;
					$po_wise_total_tot_fin_wip+=$tot_fin_wip;

					$po_wise_total_exf_qty_pre+=$exf_qnty_pre;
					$po_wise_total_exf__qty_today+=$exf_qnty_today;
					$po_wise_total_exf_tot_qty+=$tot_exf_qnty;
					$po_wise_total_tot_exf_wip+=$ex_fact_wip;

					$po_wise_inhand+=$inhand;
					$po_wise_cutinhand+=$cutting_inhand;
					$po_wise_ready_to_sewing+=$ready_to_sewing;

					// for grand total ********************************************************************************************************************
					$grand_possible_cut_qty+=$possible_cut_qty;
					$grand_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$grand_total_plan+=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];
					$grand_total_fabric_qty+=$fabric_qty;
					$grand_prev_lay_qty 	+= $prev_lay_qty;
					$grand_today_lay_qty 	+= $today_lay_qty;
					$grand_total_lay_qty 	+= $total_lay_qty;
					$grand_total_fabric_pre+=$fabric_pre;
					$grand_fabric_today_total+=$fabric_today;
					$grand_fabric_total+=$total_fabric;
					$grand_fabric_bal+=$fabric_balance;
					$grand_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$grand_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$grand_total_cut+=$total_cut;
					$grand_cutting_balance+=$cutting_balance;
					$grand_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$grand_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$grand_total_print_iss+=$total_print_iss;
					$grand_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$grand_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$grand_total_embl_iss+=$total_embl_iss;
					$grand_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$grand_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$grand_total_wash_iss+=$total_wash_iss;
					$grand_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$grand_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$grand_total_sp_iss+=$total_sp_iss;
					$grand_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$grand_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$grand_total_print_rec+=$total_print_receive;
					$grand_print_issue_balance=$print_issue_balance;
					$grand_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$grand_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$grand_total_wash_rec+=$total_wash_receive;
					$grand_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$grand_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$grand_total_embl_rec+=$total_embl_rec;
					$grand_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$grand_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$grand_total_sp_rec+=$total_sp_rec;
					$grand_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$grand_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$grand_total_delivery_cut+=$total_delivery_cut;
					$grand_deliv_cut_bal+=$deliv_cut_bal;
					$grand_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$grand_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$grand_total_sew_+=$total_sew_input;
					$grand_total_sewingout_qnty_pre+=$sewingout_qnty_pre;
					$grand_total_sewingout_qnty_today+=$sewingout_qnty_today;
					$grand_total_sewingout_qnty_bal+=$total_sewingout_qnty_bal;
					$grand_total_sewingout_qnty_reject_today+=$sewout_rej_qty_today;
					$grand_total_sewingout_qnty_reject_bal+=$total_sewingout_reject_qty_bal;
					$grand_total_sewingout_qnty_reject_today+=$sewout_rej_qty_today;
					$grand_total_sew_bal+=$total_sew_input_balance;
					$grand_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$grand_total_poly_qnty_pre+=$poly_qnty_pre;
					$grand_total_poly_qnty_today+=$poly_qnty_today;
					$grand_total_poly_qnty+=$total_poly_qnty;
					$grand_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;
					$grand_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$grand_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$grand_total_tot_poly_wip+=$tot_poly_wip;

					//Fin & Pack

					$grand_total_fin_qnty_pre+=$fin_qty_pre;
					$grand_total_fin_qnty_today+=$fin_qty_today;
					$grand_total_fin_qnty+=$tot_fin_qnty;
					$grand_total_fin_rej_qty_pre+=$fin_rej_qty_pre;
					$grand_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$grand_total_fin_rej_qty+=$tot_fin_reject_qty;
					$grand_total_tot_fin_wip+=$tot_fin_wip;

					$grand_total_exf_qty_pre+=$exf_qnty_pre;
					$grand_total_exf_qty_today+=$exf_qnty_today;
					$grand_total_exf_qty+=$tot_exf_qnty;
					$grand_total_tot_exf_wip+=$ex_fact_wip;

					$grand_inhand+=$inhand;
					$grand_cutinhand+=$cutting_inhand;
					$grand_ready_to_sewing+=$ready_to_sewing;

	                    ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
	                    <td width="40"><? echo $i; ?></td>
	                    <td width="80" style="word-break:break-all;"><p><? echo $buyer_short_library[$po_number_data[$po_id][$item_id][$color_id]['buyer_name']]; ?></p></td>
	                    <td width="60" align="center"><? echo $po_number_data[$po_id][$item_id][$color_id]['job_prifix'];?></td>
	                    <td width="50" align="right"><? echo $po_number_data[$po_id][$item_id][$color_id]['year'];?></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['po_number'];?></p></td>
	                    <td width="80" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['file_no'];?></p></td>
	                    <td width="80" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['int_ref_no'];?></p></td>
	                    <td width="80" align="center"><?  echo  change_date_format($tna_date_arr[$po_id]['tna_start']);  ?></td>
	                    <td width="80" align="center"><?  echo  change_date_format($tna_date_arr[$po_id]['tna_end']);  ?></td>
	                    <td width="80" align="center"><?  echo  change_date_format($po_min_shipdate_data[$po_id][$item_id][$color_id]['minimum_shipdate']);  ?></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['style']; ?></p></td>
	                    <td width="120" align="center" title="Item Id=<? echo $item_id;?>" style="word-break:break-all;"><p><? echo $garments_item[$item_id]; ?></p></td>
	                    <td width="100" align="center" title="color_id=<? echo $color_id;?>" style="word-break:break-all;"><p><? echo $colorname_arr[$po_number_data[$po_id][$item_id][$color_id]['color_id']]; ?></p></td>
	                    <td width="70" align="right"><?  echo $order_qnty=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity']; ?></td>
	                    <td width="70" align="right"><?  echo $plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty']; ?></td>
	                    <td width="70" align="right"><?  echo number_format(($con_per_dzn[$po_id][$color_id]*12/$costing_per_qty),3); ?></td>
	                    <td width="70" align="right"  title="consumption per pcs * Plan Cut Qty"><?  echo number_format($fabric_qty,2); ?></td>
	                    <td width="60" align="right"><?  echo number_format($fabric_pre,2); ?></td>
	                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,1)"><?  echo number_format($fabric_today,2); ?></a></td>
	                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,2)"><?  echo number_format($total_fabric,2); ?></a><?  //echo number_format($total_fabric,2);?></td>
	                    <td width="60" align="right"><?  echo number_format($fabric_balance,2); ?></td>

	                    <td width="70" align="right" title="Possible Cut Qty. =Total Fabric Receive/ Consumption Per Pcs."><?  echo  number_format($possible_cut_qty,0); ?></td>
						<td width="70" align="right" > <?= $prev_lay_qty ?? 0 ?> </td>
						<td width="70" align="right" > <?= $today_lay_qty ?? 0 ?> </td>
						<td width="70" align="right" > <?= $total_lay_qty ?? 0 ?> </td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre']; //$production_data_arr[$row_fab[csf('po_breakdown_id')]][$row_fab[csf('color_id')]]['cutting_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',950,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"> <?  echo $production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',950,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_cut; ?></a></td>
	                    <td width="60" align="right" title="(Total Cut - Order qty)*100/Total Cut"><? $cut_persent=(($total_cut - $order_qnty)*100) / $total_cut; echo number_format($cut_persent,2); ?></td>
	                    <td width="60" align="right"><?  echo $cutting_balance; ?></td>
	                    <td width="60" align="right" title="Cutting WIP=Possible Cut Qty - Total Cutting Qty"><?  echo number_format($possible_cut_qty-$total_cut,0); ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_delivery_cut; ?></a></td>
	                    <td width="60" align="right"><?  echo $deliv_cut_bal; ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printing_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_print_iss; ?></a></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty']??0; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_print_receive; ?></a></td>
	                    <td width="60" align="right" title="Print WIP=Delivery to Print-Receive from Print"><?  echo $print_balance; ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['embl_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_embl_iss; ?></a></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_embl_rec; ?></a></td>
	                    <td width="60" align="right" title="Embl. WIP=Delivery to Embl.-Receive from Embl."><?  echo $embl_balance; ?></td>

	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"> <? echo $total_sew_input; ?></a></td>
	                    <td width="60" align="right" title="(Total Sewing Input*100)/Order Qty"><?  echo number_format($input_percentage,2); ?></td>
	                    <td width="60" align="right"><?  echo $total_sew_input_balance; ?></td>
	                    <td width="70" align="right" title="Inhand=Total Cut - Delivery to Input" ><?  echo $cutting_inhand; ?></td>
	                     <td width="70" align="right" title="Inhand= Cutting Delivery To Input  - Delivery to  Embellishment &#13; + Receive from Embellishment -Sewing Input" ><?  echo $inhand; ?></td>
	                    <td width="100" align="right"><p><? echo $ready_to_sewing; ?></p></td>
	                    <td width="100" align="center"><p><? echo $line_name; ?> </p></td>

	                    <td width="60" align="center"><p><? echo $floor_name; ?></p></td>
	                    <td width="80" align="right" title="cutable_pcs= (total_fabric / fin_cons)*12"><p><? $fin_cons=0; $cutable_pcs=0; $fin_cons=number_format(($con_per_dzn[$po_id][$color_id]*12/$costing_per_qty),3); $cutable_pcs=($total_fabric/$fin_cons)*12; echo number_format($cutable_pcs,2); ?></p></td>
	                    <td width="80" align="right" title="Cutting_kg= (fin_cons/12) * cutting_qnty"><p><? $cutting_kg=0; $cutting_kg=($fin_cons/12)*$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']; echo number_format($cutting_kg,2); ?></p></td>
	                    <td width="80" align="right" title="cutting_bal= total_fabric- cutting_kg"><p><? $cutting_bal=0; $cutting_bal=$total_fabric-$cutting_kg; echo number_format($cutting_bal,2); ?></p></td>

	                     <td width="70" align="right"><p><? echo number_format($sewingout_qnty_pre,2); ?></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,5,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><? echo number_format($sewingout_qnty_today,2); ?></a></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,5,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($total_sewingout_qnty_bal,2); ?></a></p></td>
	                     <td width="70" align="right"><p><?  echo number_format($sewout_rej_qty_today,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($total_sewingout_reject_qty_bal,2); ?></p></td>
	                     <td width="70" align="right"><p><?  echo number_format($total_sewingout_wip_qty,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($poly_qnty_pre,2); ?></p></td>

	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,7,'iron_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo number_format($poly_qnty_today,2); ?></a></p></td>

	                      <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,7,'iron_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($total_poly_qnty,2); ?></a></p></td>

	                     <td width="70" align="right"><p><?  echo number_format($ploy_rej_qty_today,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($total_ploy_rej_qty,2); ?></p></td>
	                     <td width="70" align="right"><p><?  echo number_format($tot_poly_wip,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($fin_qty_pre,2); ?></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,8,'packing_and_finishing_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><? echo number_format($fin_qty_today,2); ?></a></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,8,'packing_and_finishing_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($tot_fin_qnty,2); ?></a></p></td>
	                     <td width="70" align="right"><p><? echo number_format($fin_rej_qty_today,2); ?></p></td>
	                      <td width="70" align="right"><p><?  echo number_format($tot_fin_reject_qty,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($tot_fin_wip,2); ?></p></td>


	                    <?
	                    if($ex_factory_level==1)
	                    {
	                     	if($r==0)
	                     	{
		                     	?>

	                     		<td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><?  echo number_format($exf_qnty_pre,2); ?></p></td>
			                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><a href="##" onclick="javascript:alert('It is gross level production, so popup not found.');"><?  echo number_format($exf_qnty_today,2); ?></a></p></td>
			                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><a href="##" onclick="javascript:alert('It is gross level production, so popup not found.');"><? echo number_format($tot_exf_qnty,2); ?></a></p></td>

			                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><?  echo number_format($ex_fact_wip,2); ?></p></td>
		                     	<?
		                     	$r++;
		                     	// $po_wise_total_tot_exf_wip = $ex_fact_wip;
	                     	}
	                 	}
	                    else
	                    {
	                     	?>
		                    <td valign="middle" width="70" align="right"><p><?  echo number_format($exf_qnty_pre,2); ?></p></td>
		                    <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,'','ex_factory_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo number_format($exf_qnty_today,2); ?></a></p></td>
		                    <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,'','ex_factory_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($tot_exf_qnty,2); ?></a></p></td>

		                    <td width="70" align="right" title=" ex_fact_wip = (tot_fin_qnty - tot_exf_qnty);"> <p><?  echo number_format($ex_fact_wip,2); ?></p></td>

	                    	<?
	                    	// $po_wise_total_tot_exf_wip = $ex_fact_wip;
		                }
		                ?>




	                    <td  align="center"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_remarks',650,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>','')">
						<?
						   if($total_delivery_cut-$po_number_data[$po_id][$color_id][$item_id]['plan_qty']>0) echo "Receive Ok";
						   else  if($total_sew_input_balance-$po_number_data[$po_id][$item_id][$color_id]['plan_qty']>0) echo "Input Ok";
						   else echo "Remarks";

						?></a></td>
	        	  </tr>
							<?
					 $job_arr[]=$po_number_data[$po_id][$item_id][$color_id]['job_no'];
					 $buyer_arr[]=$po_number_data[$po_id][$item_id][$color_id]['buyer_name'];
					 $i++;
	                } //end foreach 2nd
				}
				?>
					<tr bgcolor="#999999" style=" height:15px">
						<td width="40"></td>
						<td width="80"></td>
						<td width="60"></td>
						<td width="50"></td>
						<td width="100"></td>
						<td width="80"></td>
                        <td width="80"></td>
                        <td width="80"></td>
						<td width="80"></td>
                        <td width="80"></td>
						<td width="100"><strong> </strong></td>
                        <td width="120"><strong> </strong></td>
						<td width="100" align="right"><strong>PO Total:</strong></td>
						<td width="70" align="right"><? echo $po_wise_total_order; ?></td>
						<td width="70" align="right"><?  echo $po_wise_total_plan; ?></td>
                        <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
						<td width="70" align="right"><?  echo number_format($po_wise_total_fabric_qty,2); ?></td>
						<td width="60" align="right"><?  echo number_format($po_wise_total_fabric_pre,2); ?></td>
						<td width="60" align="right"><?  echo number_format($po_wise_fabric_today_total,2); ?></td>
						<td width="60" align="right"><?  echo number_format($po_wise_fabric_total,2);?></td>
						<td width="60" align="right"><?  echo number_format($po_wise_fabric_bal,2); ?></td>

                        <td width="70" align="right"><?  echo number_format($po_wise_possible_cut_qty,0); ?></td>
						<td width="70" align="right" > <?= $po_wise_prev_lay_qty; ?></td>
						<td width="70" align="right" > <?= $po_wise_today_lay_qty; ?> </td>
						<td width="70" align="right" > <?= $po_wise_total_lay_qty; ?> </td>
						<td width="60" align="right"><?  echo $po_wise_pre_cut; ?></td>
						<td width="60" align="right"><?  echo $po_wise_today_cut; ?></td>
						<td width="60" align="right"><?  echo $po_wise_total_cut; ?></td>
						<td width="60" align="right"></td>
						<td width="60" align="right"><?  echo $po_wise_cutting_balance; ?></td>
                        <td width="60" align="right"><?  echo number_format($po_wise_possible_cut_qty-$po_wise_total_cut,0); ?></td>
                        <td width="60" align="right"><?  echo $po_wise_priv_deliv_cut; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_deliv_cut; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_delivery_cut; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_deliv_cut_bal; ?></td>

                        <td width="60" align="right"><?  echo $po_wise_priv_print_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_print_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_print_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_priv_print_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_print_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_print_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_print_iss-$po_wise_total_print_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_priv_embl_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_embl_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_embl_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_priv_embl_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_embl_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_embl_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_embl_iss-$po_wise_total_embl_rec; ?></td>


                        <td width="60" align="right"><?  echo $po_wise_priv_sew; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_sew; ?></td>
                        <td width="60" align="right"> <? echo $po_wise_total_sew_; ?></td>
                        <td width="60" align="right"><? //echo $input_percentage; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_sew_bal; ?></td>
                        <td width="70" align="right"><? echo $po_wise_cutinhand; ?></td>
                        <td width="70" align="right"><? echo $po_wise_inhand; ?></td>
                        <td width="100" align="right"><? echo $po_wise_ready_to_sewing; ?></td>

                        <td width="100" align="right"><? //echo  $sewing_line ?></td>
                        <td width="60" align="right"><? //echo  $unit ?></td>
                        <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
                        <td width="80" align="right"><? //echo  $cutting_kg ?></td>
                        <td width="80" align="right"><? //echo  $cutting_bal ?></td>

                        <td width="70" align="right"><? echo  $po_wise_priv_sew_out; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sew_out_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sew_out_bal; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sew_out_reject_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sew_out_reject_bal; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sewingout_wip_qty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_poly_qnty_pre; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_poly_qnty_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_poly_qnty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_ploy_rej_qty_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_ploy_rej_qty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_tot_poly_wip; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_qnty_pre; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_qnty_today;?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_qnty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_rej_qty_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_rej_qty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_tot_fin_wip; ?></td>
                        <td width="70" align="right"><? echo  $exFactPoQtyArray[$po_id]['exf_qnty_pre']; ?></td>
                        <td width="70" align="right"><? echo  $exFactPoQtyArray[$po_id]['exf_qnty_today']; ?></td>
                        <td width="70" align="right"><? echo  $totEx = $exFactPoQtyArray[$po_id]['exf_qnty_pre']+$exFactPoQtyArray[$po_id]['exf_qnty_today'];; ?></td>
                        <td width="70" align="right"><? echo $po_wise_total_tot_exf_wip; ?></td>

						<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
				  </tr>
				<?

	 		 }

				?>
	                         <tr bgcolor="#CCCCCC" id="">
	                                <td width="40"><? // echo $i;?></td>
	                                <td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
	                                <td width="60"></td>
	                                <td width="50"></td>
	                                <td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="100"><strong></strong></td>
	                                <td width="120"><strong></strong></td>
	                                <td width="100" align="right"><strong>Job Total:</strong></td>
	                                <td width="70" align="right"><? echo $job_total_order; ?></td>
	                                <td width="70" align="right"><?  echo $job_total_plan; ?></td>
	                                <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
	                                <td width="70" align="right"><?  echo number_format($job_total_fabric_qty,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_total_fabric_pre,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_today_total,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_total,2);?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_bal,2); ?></td>
	                                <td width="70" align="right"><?  echo number_format($job_possible_cut_qty,0); ?></td>
									<td width="70" align="right" > <?= $job_prev_lay_qty ?> </td>
									<td width="70" align="right" > <?= $job_today_lay_qty ?> </td>
									<td width="70" align="right" > <?= $job_total_lay_qty ?> </td>
	                                <td width="60" align="right"><?  echo $job_pre_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_cut; ?></td>
	                                <td width="60" align="right"></td>
	                                <td width="60" align="right"><?  echo $job_cutting_balance; ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_possible_cut_qty-$job_total_cut,0); ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_deliv_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_deliv_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_delivery_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_deliv_cut_bal; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_iss-$job_total_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_iss-$job_total_embl_rec; ?></td>

	                                <td width="60" align="right"><?  echo $job_priv_sew; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_sew; ?></td>
	                                <td width="60" align="right"> <? echo $job_total_sew_input; ?></td>
	                                <td width="60" align="right"><? //echo $input_percentage; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_sew__bal; ?></td>
	                                <td width="70" align="right"><? echo $job_cutinhand; ?></td>
	                                <td width="70" align="right"><? echo $job_inhand; ?></td>
	                                <td width="100" align="right"><? echo $job_ready_to_sewing; ?></td>

	                                <td width="100" align="right"><? //echo  $sewing_line ?></td>

	                                <td width="60" align="right"><? //echo  $unit ?></td>
	                                <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                                <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                                <td width="80" align="right"><? //echo  $cutting_bal ?></td>

	                                <td width="70" align="right"><? echo  $job_priv_sewout; ?></td>
	                                <td width="70" align="right"><? echo  $job_today_sewout; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sew_out_bal; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sew_out_reject_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sew_out_reject_bal; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sewingout_wip_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_poly_qnty_pre; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_poly_qnty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_poly_qnty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_ploy_rej_qty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_ploy_rej_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_tot_poly_wip; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_qnty_pre; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_qnty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_qnty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_rej_qty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_rej_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_tot_fin_wip; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_exf_qty_pre; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_exf_qty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_exf_tot_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_tot_exf_wip; ?></td>
	                                <!-- <td width="70" align="right"><? //echo  $cutting_bal; ?></td> -->



	                                <td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
	                          </tr>
	                           <tr bgcolor="#999999" style=" height:15px">
										<td width="40"><? // echo $i;?></td>
										<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
										<td width="60"></td>
										<td width="50"></td>
										<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
										<td width="80"></td>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
										<td width="80"></td>
	                                    <td width="80"></td>
										<td width="100"><strong> </strong></td>
	                                    <td width="120"><strong> </strong></td>
										<td width="100" align="right"><strong>Buyer Total:</strong></td>
										<td width="70" align="right"><? echo $buyer_total_order; ?></td>
										<td width="70" align="right"><?  echo $buyer_total_plan; ?></td>
	                                    <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
										<td width="70" align="right"><?  echo number_format($buyer_total_fabric_qty,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_total_fabric_pre,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_today_total,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_total,2);?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_bal,2); ?></td>

	                                    <td width="70" align="right"><?  echo number_format($buyer_possible_cut_qty,0); ?></td>
										<td width="70" align="right" > <?= $buyer_prev_lay_qty ?> </td>
										<td width="70" align="right" > <?= $buyer_today_lay_qty ?> </td>
										<td width="70" align="right" > <?= $buyer_total_lay_qty ?> </td>
										<td width="60" align="right"><?  echo $buyer_pre_cut; ?></td>
										<td width="60" align="right"><?  echo $buyer_today_cut; ?></td>
										<td width="60" align="right"><?  echo $buyer_total_cut; ?></td>
										<td width="60" align="right"></td>
										<td width="60" align="right"><?  echo $buyer_cutting_balance; ?></td>
	                                    <td width="60" align="right"><?  echo number_format($buyer_possible_cut_qty-$buyer_total_cut,0); ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_deliv_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_deliv_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_delivery_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_deliv_cut_bal; ?></td>

	                                    <td width="60" align="right"><?  echo $buyer_priv_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_iss-$buyer_total_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_iss-$buyer_total_embl_rec; ?></td>


	                                    <td width="60" align="right"><?  echo $buyer_priv_sew; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_sew; ?></td>
	                                    <td width="60" align="right"> <? echo $buyer_total_sew_; ?></td>
	                                    <td width="60" align="right"><? //echo $input_percentage; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_sew_bal; ?></td>
	                                    <td width="70" align="right"><? echo $buyer_cutinhand; ?></td>
	                                    <td width="70" align="right"><? echo $buyer_inhand; ?></td>
	                                    <td width="100" align="right"><? echo $buyer_ready_to_sewing; ?></td>

	                                    <td width="100" align="right"><? //echo  $sewing_line ?></td>
	                                    <td width="60" align="right"><? //echo  $unit ?></td>
	                                    <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_bal ?></td>

	                                    <td width="70" align="right"><? echo  $buyer_priv_sew_out; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_bal; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_bal; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sewingout_wip_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_poly_qnty_pre; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_poly_qnty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_poly_qnty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_tot_poly_wip; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_qnty_pre; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_qnty_today;?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_qnty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_tot_fin_wip; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_exf_qty_pre; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_exf__qty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_exf_tot_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_tot_exf_wip; ?></td>

										<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
								  </tr>
	                            <tfoot>
	                                 <tr>
	                                    <th width="40"><? // echo $i;?></th>
	                                    <th width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
	                                    <th width="60"></td>
	                                    <th width="50"></td>
	                                    <th width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
	                                    <th width="80"></th>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
	                                    <th width="80"></th>
	                                    <th width="80"></th>
	                                    <th width="100"> <strong></strong></th>
	                                    <th width="120"> <strong></strong></th>
	                                    <th width="100" align="right"><strong>Grand Total:</strong></th>
	                                    <th width="70" align="right"><? echo $grand_total_order; ?></th>
	                                    <th width="70" align="right"><?  echo $grand_total_plan; ?></th>
	                                    <th width="70" align="right"><? // echo $grand_total_plan; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_total_fabric_qty,2); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_total_fabric_pre,2); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_fabric_today_total,2); ?></th>
	                                    <th width="60" align="right"><?  echo number_format($grand_fabric_total,2);?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_fabric_bal,2); ?></th>

	                                    <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_possible_cut_qty,0); ?></th>
										<td width="70" align="right" > <?= $grand_prev_lay_qty ?> </td>
										<td width="70" align="right" > <?= $grand_today_lay_qty ?> </td>
										<td width="70" align="right" > <?= $grand_total_lay_qty ?> </td>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_pre_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_cutting_balance; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_possible_cut_qty-$grand_total_cut,0); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_deliv_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_deliv_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_delivery_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_deliv_cut_bal; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_iss-$grand_total_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_iss-$grand_total_embl_rec; ?></th>


	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_sew; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_sew; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><? echo $grand_total_sew_; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><? //echo $input_percentage; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_sew_bal; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><? echo $grand_cutinhand; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><? echo $grand_inhand; ?></th>
	                                    <th width="100" align="right" style="word-break:break-all;"><? echo $grand_ready_to_sewing; ?></th>

	                                    <th width="100" align="right"><? //echo  $sewing_line ?></th>

	                                    <th width="60" align="right"><? //echo  $unit ?></th>
	                                    <th width="80" align="right"><? //echo  $cutble_pcs ?></th>
	                                    <th width="80" align="right"><? //echo  $cutting_kg ?></th>
	                                    <th width="80" align="right"><? //echo  $cutting_bal ?></th>

	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_today ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_reject_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_reject_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_reject_bal; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_wip_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_poly_qnty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_poly_qnty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_poly_qnty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_ploy_rej_qty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_ploy_rej_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_poly_wip; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_qnty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_qnty_today ;?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_qnty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_rej_qty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_rej_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_fin_wip; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_exf_qty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_exf_qty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_exf_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_exf_wip; ?></th>

	                                    <th  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></th>
	                             </tr>
	  						</tfoot>

	                </table>
	           </div>
	  	</div>

	  	</fieldset>

		<?
	}
	elseif ($rpt_type_bk==2) //ITEM WISE Backup 28-08-2022
	{
		if(str_replace("'","",$txt_production_date) !="")
		{

			$prod_po_arr=return_library_array( "select po_break_down_id, po_break_down_id as po_id from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date",'po_break_down_id','po_id');
			if(count($prod_po_arr)>0)
			{
				// $prod_po_cond = where_con_using_array($prod_po_arr,0,"a.id");
			}
		}
		// ==================================== MAIN QUERY ==========================================
	 	$pro_date_sql="SELECT  a.id,a.job_no_mst,a.po_number,
 		d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,d.item_number_id as gmt_id,b.buyer_name,b.style_ref_no as style,b.job_no_prefix_num,$insert_year
 		as year ,d.color_number_id, a.file_no, a.grouping as int_ref_no ,
 		min(d.country_ship_date) as minimum_shipdate
 		from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
 		where a.job_id=b.id and a.id=d.po_break_down_id and  a.job_id=d.job_id and  a.is_deleted=0 and a.status_active=1 and
 		b.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and
 		b.status_active=1 and a.is_confirmed=1 $company_name $buyer_name $style_cond $order_cond $sql_cond $job_cond_id $country_ship_date $shipping_cond  $prod_po_cond
 		group by a.id,a.job_no_mst,a.po_number,
 		d.order_quantity,d.plan_cut_qnty ,d.item_number_id,b.buyer_name,b.style_ref_no ,b.job_no_prefix_num,d.color_number_id, a.file_no, a.grouping,b.insert_date
 		order by  b.buyer_name,a.job_no_mst";
 		//echo $pro_date_sql;die();
 		$pro_date_sql_res = sql_select($pro_date_sql);
	  	$po_min_shipdate_data=array();
	  	$po_number_id = array();
	  	foreach($pro_date_sql_res as $row)
	  	{
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['id']=$row[csf('id')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no_mst')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['gmt_id']=$row[csf('gmt_id')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['po_quantity']+=$row[csf('order_qty')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['plan_qty']+=$row[csf('plan_qty')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['buyer_name']=$row[csf('buyer_name')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['style']=$row[csf('style')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['job_prifix']=$row[csf('job_no_prefix_num')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['year']=$row[csf('year')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['color_id']=$row[csf('color_number_id')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['file_no']=$row[csf('file_no')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['int_ref_no']=$row[csf('int_ref_no')];
		  $po_number_id[$row[csf('id')]]=$row[csf('id')];

		  $po_min_shipdate_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['minimum_shipdate']=$row[csf('minimum_shipdate')];
	  	}
	  	unset($pro_date_sql);
 		// echo "<pre>";
 		// print_r($po_min_shipdate_data);
 		// echo "</pre>";

 		$po_number_id=implode(",",array_unique($po_number_id));

		$po_condition="";
		$po_number_id_arr=explode(",", trim(str_replace("'","",$po_number_id)));
		if($db_type==2 && count($po_number_id_arr)>999)
		{
	  		$chunk_arr=array_chunk($po_number_id_arr, 999);
	  		foreach($chunk_arr as $keys=>$vals)
	  		{
	  			$po_ids=implode(",", $vals);
	  			if($po_condition=="")
	  			{
	  				$po_condition.=" and ( a.po_break_down_id in ($po_ids) ";
	  			}
	  			else
	  			{
	  				$po_condition.=" or a.po_break_down_id in ($po_ids) ";
	  			}
	  		}
	  		$po_condition.=" ) ";
		}
		else
		{
			$po_condition=" and a.po_break_down_id in (".str_replace("'","",$po_number_id).")";
		}

		//
		/*$date_wise_po="SELECT po_break_down_id from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date";
	 	$date_wise_po_arr=array();
	 	foreach(sql_select($date_wise_po) as $vals)
	 	{
	 		$date_wise_po_arr[$vals[csf("po_break_down_id")]]=$vals[csf("po_break_down_id")];
	 	}
	 	$date_wise_po_ids=implode(",", $date_wise_po_arr);*/
	 	// GETTING ORDER QUANTITY  ADDED IN 13/10/2018

	 	// ===================================== GETTING ORDER QTY =============================================
	 	$order_qnty_array=array();
	 	$plan_cut_qnty_array=array();
		$order_qnty_sqls="SELECT a.po_break_down_id,a.color_number_id,a.order_quantity,a.item_number_id,a.plan_cut_qnty from wo_po_color_size_breakdown a where a.status_active in(1,2,3) and a.is_deleted=0 $po_condition";
		// echo $order_qnty_sqls;
		foreach(sql_select($order_qnty_sqls) as $values)
		{
		 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]['po_quantity']+=$values[csf("order_quantity")];
		 	$plan_cut_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]['plan_cut_qnty']+=$values[csf("plan_cut_qnty")];
		}

 		// ============================== for exfactory rowspan ===========================
 		$rowspan_arr = array();
 		foreach ($po_number_data as $po_key => $po_arr)
 		{
 			foreach ($po_arr as $item_key => $item_arr)
 			{
 				foreach ($item_arr as $color_key => $color_arr)
 				{
 					$rowspan_arr[$po_key][$item_key]++;
 				}
 			}
 		}
	  	// echo "<pre>";
 		// print_r($rowspan_arr);
 		// echo "</pre>";

	   	$sew_line_arr=array();
	   	if($db_type==0)
	   	{
	   		$sql_line=sql_select("SELECT group_concat(distinct a.sewing_line) as line_id, group_concat(distinct a.floor_id) as floor_id, a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 $po_condition group by a.po_break_down_id");
	   	}
	   	if($db_type==2)
	   	{
	   		$sql_line=sql_select("SELECT listagg(cast(a.sewing_line as varchar2(2000)),',') within group (order by a.sewing_line) as line_id, listagg(cast(a.floor_id as varchar2(2000)),',') within group (order by a.floor_id) as floor_id, a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 $po_condition group by a.po_break_down_id");
	   	}

	   	foreach($sql_line as $row_sew)
	   	{
	   		$sew_line_arr[$row_sew[csf('po_break_down_id')]]['line']= implode(',',array_unique(explode(',',$row_sew[csf('line_id')])));
			$sew_line_arr[$row_sew[csf('po_break_down_id')]]['floor_id']= implode(',',array_unique(explode(',',$row_sew[csf('floor_id')])));
	   	}
	   	unset($sql_line);


	  	if($po_number_id=="") $po_number_id=0;
	  	$company_id = str_replace("'","",$cbo_company_name);
	  	$variable_sql=sql_select("SELECT ex_factory from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
		$ex_factory_level=$variable_sql[0][csf("ex_factory")];

	  	if($ex_factory_level==1) // for gross level
	  	{
	  		$ex_factory_sql="SELECT a.po_break_down_id as order_id, a.item_number_id as item_id,
		    sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date=$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS exf_qnty_today,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date=$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS ret_exf_qnty_today,
			sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date<$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS exf_qnty_pre,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date<$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS ret_exf_qnty_pre
			from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a
			where m.id=a.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and m.status_active=1 and m.is_deleted=0   $po_condition
			group by  a.po_break_down_id, a.item_number_id";
	  	}
	  	else // for color or color and size level
	  	{
	  		$ex_factory_sql="SELECT a.po_break_down_id as order_id, a.item_number_id as item_id, c.color_number_id as color_id,
		    sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END) AS exf_qnty_today,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_today,
			sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date<$txt_production_date THEN b.production_qnty ELSE 0 END) AS exf_qnty_pre,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date<$txt_production_date THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_pre
			from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
			where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0   $po_condition
			group by  a.po_break_down_id, a.item_number_id, c.color_number_id";
	  	}


		 	// echo $ex_factory_sql;die;
			$ex_factory_sql_result=sql_select($ex_factory_sql);
			$exFactPoQtyArray = array();
			foreach($ex_factory_sql_result as $row)
			{
				if($ex_factory_level==1)
				{
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]]['exf_qnty_today']=$row[csf('exf_qnty_today')]-$row[csf('ret_exf_qnty_today')];
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]]['exf_qnty_pre']=$row[csf('exf_qnty_pre')]-$row[csf('ret_exf_qnty_pre')];
				}
				else
				{
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['exf_qnty_today']=$row[csf('exf_qnty_today')]-$row[csf('ret_exf_qnty_today')];
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['exf_qnty_pre']=$row[csf('exf_qnty_pre')]-$row[csf('ret_exf_qnty_pre')];
				}
				$exFactPoQtyArray[$row[csf('order_id')]]['exf_qnty_today'] 	+= $row[csf('exf_qnty_today')]-$row[csf('ret_exf_qnty_today')];
				$exFactPoQtyArray[$row[csf('order_id')]]['exf_qnty_pre'] 	+= $row[csf('exf_qnty_pre')]-$row[csf('ret_exf_qnty_pre')];
			}


		  $production_sql="SELECT a.po_break_down_id,c.color_number_id,c.item_number_id as gmt_id,
		   sum(CASE WHEN b.production_type =1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cutting_qnty,
		   sum(CASE WHEN b.production_type =1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cutting_qnty_pre,
		   sum(CASE WHEN b.production_type =2  and a.embel_name=4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS sp_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printing_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printing_qnty_pre ,
		   sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printreceived_qnty,
		   sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printreceived_qnty_pre,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS embl_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS embl_qnty_pre ,
		   sum(CASE WHEN b.production_type =3  and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS emblreceived_qnty,
		   sum(CASE WHEN b.production_type =3  and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS emblreceived_qnty_pre,
		   min(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<=".$txt_production_date." THEN a.production_date end)
		   AS min_printin_date,
		   min(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<=".$txt_production_date." THEN a.production_date end)
		   as min_embl_date,
		   sum(CASE WHEN b.production_type =4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty,
		   sum(CASE WHEN b.production_type =4 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty_pre,
		   sum(CASE WHEN b.production_type =5 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingout_qnty_pre,
		   sum(CASE WHEN b.production_type =5 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingout_qnty_today,
		   sum(CASE WHEN b.production_type =5 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sewout_rej_qty_today,
		   sum(CASE WHEN b.production_type =5 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sewout_rej_qty_pre,

		   sum(CASE WHEN b.production_type =7 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty_pre,
		   sum(CASE WHEN b.production_type =7 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty_today,
		   sum(CASE WHEN b.production_type =7 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS ploy_rej_qty_pre,
		   sum(CASE WHEN b.production_type =7 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS ploy_rej_qty_today,

		    sum(CASE WHEN b.production_type =8 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS fin_qty_pre,
			sum(CASE WHEN b.production_type =8 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS fin_qty_today,
			sum(CASE WHEN b.production_type =8 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS fin_rej_qty_today,
			sum(CASE WHEN b.production_type =8 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS fin_rej_qty_pre,
			sum(CASE WHEN b.production_type =9 and a.production_date=".$txt_production_date." THEN b.PRODUCTION_QNTY ELSE 0 END) AS cut_delivery_qnty,
			sum(CASE WHEN b.production_type =9 and a.production_date<".$txt_production_date." THEN b.PRODUCTION_QNTY ELSE 0 END) AS cut_delivery_qnty_pre


		   from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c where a.id=b.mst_id
		   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		   and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id $po_condition  group by a.po_break_down_id,c.color_number_id,c.item_number_id";
		   //echo "$production_sql";die;
			//and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
			$production_mst_sql=sql_select($production_sql);
			$order_wise_fin_qty_array = array();
	       foreach($production_mst_sql as $val)
		   {
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printing_qnty']=$val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['emblreceived_qnty_pre']=$val[csf('emblreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['emblreceived_qnty']=$val[csf('emblreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['embl_qnty_pre']=$val[csf('embl_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['embl_qnty']=$val[csf('embl_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['min_printin_date']=$val[csf('min_printin_date')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['min_embl_date']=$val[csf('min_embl_date')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingout_qnty_pre']=$val[csf('sewingout_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingout_qnty_today']=$val[csf('sewingout_qnty_today')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewout_rej_qty_today']=$val[csf('sewout_rej_qty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewout_rej_qty_pre']=$val[csf('sewout_rej_qty_pre')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['poly_qnty_pre']=$val[csf('poly_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['poly_qnty_today']=$val[csf('poly_qnty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['ploy_rej_qty_pre']=$val[csf('ploy_rej_qty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['ploy_rej_qty_today']=$val[csf('ploy_rej_qty_today')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_qty_pre']=$val[csf('fin_qty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_qty_today']=$val[csf('fin_qty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_rej_qty_today']=$val[csf('fin_rej_qty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_rej_qty_pre']=$val[csf('fin_rej_qty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cut_delivery_qnty']=$val[csf('cut_delivery_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cut_delivery_qnty_pre']=$val[csf('cut_delivery_qnty_pre')];

				$order_wise_fin_qty_array[$val[csf('po_break_down_id')]]['fin_qty_pre'] 	+= $val[csf('fin_qty_pre')];
				$order_wise_fin_qty_array[$val[csf('po_break_down_id')]]['fin_qty_today'] 	+= $val[csf('fin_qty_today')];

				$po_number_gmt[]=$val[csf('po_break_down_id')];
		   }
		    //  echo "<pre>";
			// print_r($production_data_arr);
			// echo "</pre>"; die();

			unset($production_mst_sql);

			$sql_cutting_delevery=("select a.po_break_down_id,c.color_number_id,c.item_number_id as gmt_id,
			sum(CASE WHEN  a.cut_delivery_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty,
		    sum(CASE WHEN  a.cut_delivery_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty_pre
			from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c  where a.id=b.mst_id
		    and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id   $po_condition
		    and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		    group by a.po_break_down_id,c.color_number_id,c.item_number_id");
			// echo $sql_cutting_delevery; die;
		   //and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
			foreach( $sql_cutting_delevery as $inf)
			{
				$production_data_arr[$inf[csf('po_break_down_id')]][$inf[csf('gmt_id')]][$inf[csf('color_number_id')]]['cut_delivery_qnty']=$inf[csf('cut_delivery_qnty')];

				$production_data_arr[$inf[csf('po_break_down_id')]][$inf[csf('gmt_id')]][$inf[csf('color_number_id')]]['cut_delivery_qnty_pre']=$inf[csf('cut_delivery_qnty_pre')];
			}
			unset($sql_cutting_delevery);
			$po_condition_fab=str_replace("po_break_down_id", "po_breakdown_id", $po_condition);

	        $sql_fabric_qty=sql_select("SELECT a.po_breakdown_id,a.color_id,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
		    ELSE 0 END ) AS grey_fabric_issue,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =4  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
		    ELSE 0 END ) AS grey_fabric_issue_return,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS finish_fabric_rece,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS finish_fabric_rece_return,
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS fabric_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =2 and b.item_category=2  AND a.entry_form =18 THEN a.quantity
			ELSE 0 END ) AS fabric_qty_pre,
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END )
			AS trans_in_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_pre,
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END )
			AS trans_out_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_pre
			FROM order_wise_pro_details a,inv_transaction b
		    WHERE a.trans_id = b.id
			and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0  $po_condition_fab group by a.po_breakdown_id,a.color_id");
			//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
			$fabric_pre_qty=array();
			$fabric_today_qty=array();
			$total_fabric=array();
			$fabric_balance=array();
			$fabric_wip=array();
			foreach($sql_fabric_qty as $value)
			{
				$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
				$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];

				$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
				-$value[csf("trans_out_pre")];
				$fabric_today_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty")]+$value[csf("trans_in_qty")]
				-$value[csf("trans_out_qty")];

				$po_id_fab[]=$value[csf("po_breakdown_id")];
			}


			unset($sql_fabric_qty);


	 		//*********************************************************************************************************************

		  $color_size_qty_arr=array();
		  $po_condition2=str_replace("a.po_break_down_id", "po_break_down_id", $po_condition);
		  $po_condition3=str_replace("a.po_break_down_id", "b.po_break_down_id", $po_condition);
		  $color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty
		  from wo_po_color_size_breakdown
		  where  is_deleted=0  and  status_active=1  $po_condition2
		  group by po_break_down_id,item_number_id,size_number_id,color_number_id");
		  foreach($color_size_sql as $s_id)
		  {
			$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')];
		  }

		  unset($color_size_sql);


	  	   $sql_sewing=sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum(b.cons) AS conjumction
		   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
		   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  $po_condition3 and b.cons!=0
		   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
		   //echo $sql_sewing; die();
		   $con_per_dzn=array();
		   $po_item_qty_arr=array();
		   $color_size_conjumtion=array();
	       foreach($sql_sewing as $row_sew)
	       {
		    	$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);

				$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
				$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
	       }

		   unset($sql_sewing);

		  foreach($color_size_conjumtion as $p_id=>$p_value)
		  {
			 foreach($p_value as $i_id=>$i_value)
			 {
				foreach($i_value as $c_id=>$c_value)
				 {
					 foreach($c_value as $s_id=>$s_value)
					 {
						 foreach($s_value as $b_id=>$b_value)
						 {
						   $order_color_size_qty=$b_value['plan_cut_qty'];
						   $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
						   $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
						   $conjunction_per= (($b_value['conjum']*$order_color_size_qty_per)/100);
						   $con_per_dzn[$p_id][$c_id]+=$conjunction_per;

						  // echo $p_id."**".$order_color_size_qty."**".$order_qty."**".$order_color_size_qty_per."**".$b_value['conjum']."<br>";
						 }
					 }
				 }
			 }
		  }
			//print_r($con_per_dzn);die;
		 	//**********************************************************************************************************************


	     $sql_tna_date=sql_select("select a.po_number_id, a.task_start_date,a.task_finish_date	from  tna_process_mst a, lib_tna_task b
		 where b.task_name=a.task_number  and task_name=84");
		 $tna_date_arr=array();
		 foreach($sql_tna_date as $tna_val)
		 {
		 $tna_date_arr[$tna_val[csf('po_number_id')]]['tna_start']= $tna_val[csf('task_start_date')];
		 $tna_date_arr[$tna_val[csf('po_number_id')]]['tna_end']= $tna_val[csf('task_finish_date')];
		 }


		 unset($sql_tna_date);


		 $costing_per_sql=sql_select("select job_no,costing_per from wo_pre_cost_mst");
		 $costing_per_arr=array();
		 foreach($costing_per_sql as $cost_val)
		 {
			$costing_per_arr[$cost_val[csf('job_no')]]=$cost_val[csf('costing_per')];
		 }

		 unset($costing_per_sql);

		 $ready_to_sewing_sql=sql_select("select po_break_down_id, color_id, sewing_qnty as sewing_qnty from ready_to_sewing_dtls");
		 $ready_to_sewing_arr=array();
		 foreach($ready_to_sewing_sql as $row)
		 {
			$ready_to_sewing_arr[$row[csf('po_break_down_id')]][$row[csf('color_id')]]+=$row[csf('sewing_qnty')];

		 }
		 unset($ready_to_sewing_sql);
		 ob_start();
			 ?>
	         <fieldset style="width:5760px;">
	        	   <table width="1880"  cellspacing="0"   >
	                    <tr class="form_caption" style="border:none;">
	                           <td colspan="57" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report(Item Wise)</td>
	                     </tr>
	                    <tr style="border:none;">
	                            <td colspan="57" align="center" style="border:none; font-size:16px; font-weight:bold">
	                            Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
	                            </td>
	                      </tr>
	                      <tr style="border:none;">
	                            <td colspan="57" align="center" style="border:none;font-size:12px; font-weight:bold">
	                            <? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
	                            </td>
	                      </tr>
	                </table>
	             <br />
	             <table cellspacing="0"  border="1" rules="all"  width="5760" class="rpt_table">
	                <thead>
	                	<tr >
	                        <th width="40" rowspan="2">SL</th>
	                        <th width="80" rowspan="2">Buyer</th>
	                        <th width="60" rowspan="2">Job No</th>
	                        <th width="50" rowspan="2">Year</th>
	                        <th width="100" rowspan="2">Order No</th>
	                        <th width="80" rowspan="2">File No</th>
	                        <th width="80" rowspan="2">Int. Ref. No</th>
	                        <th width="80" rowspan="2">TNA Start Date</th>
	                        <th width="80" rowspan="2">TNA End Date</th>
	                        <th width="80" rowspan="2">First Shipment Date</th>
	                        <th width="100" rowspan="2">Style</th>
	                        <th width="120" rowspan="2">Item</th>
	                        <th width="100" rowspan="2">Color</th>
	                        <th width="70" rowspan="2">Order Qty.</th>
	                        <th width="70" rowspan="2">Plan Cut Qty.</th>
	                        <th width="70" rowspan="2">Fin Fab Cons/Dzn.</th>
	                        <th width="70" rowspan="2">Fabric Required Qty</th>
	                        <th width="240" colspan="4">Fabric Receive Qty.</th>
	                        <th width="70" rowspan="2">Possible Cut Qty</th>
	                        <th width="300" colspan="5">Cutting</th>
	                        <th width="60" rowspan="2"> Cutting WIP</th>
	                        <th width="240" colspan="4">Cutting Delivery To Input</th>
	                        <th width="180" colspan="3">Delivery to Print</th>
	                        <th width="180" colspan="3">Receive from Print</th>
	                        <th width="60" rowspan="2">Print WIP</th>
	                        <th width="180" colspan="3">Delivery to Emb.</th>
	                        <th width="180" colspan="3">Receive from Emb.</th>
	                        <th width="60" rowspan="2">Emb. WIP</th>

	                        <th width="300" colspan="5"> Sewing Input</th>
	                        <th width="70" rowspan="2">Cutting Inhand</th>
	                        <th width="70" rowspan="2">Input Inhand</th>
	                        <th width="100" rowspan="2">Ready To Sewing</th>
	                        <th width="100" rowspan="2">Line No</th>

	                        <th width="60" rowspan="2">Unit(Floor)</th>
	                        <th width="80" rowspan="2">Cutable Pcs as per Fabric Received</th>
	                        <th width="80" rowspan="2">Cutting KG</th>
	                        <th width="80" rowspan="2">Cutting Balance Fabric</th>

	                        <th width="210" colspan="3">Sewing Output</th>
	                        <th width="70" rowspan="2">Today Sewing Reject</th>
	                        <th width="70" rowspan="2">Sewing Reject Total</th>
	                        <th width="70" rowspan="2">Sewing WIP</th>

	                        <th width="210" colspan="3">Iron Entry</th>

	                        <th width="70" rowspan="2">Today Iron Reject</th>
	                        <th width="70" rowspan="2">Iron Reject Total</th>
	                        <th width="70" rowspan="2">Iron WIP</th>

	                        <th width="210" colspan="3">Packing & Finishing</th>

	                        <th width="70" rowspan="2">Today Finishing Reject</th>
	                        <th width="70" rowspan="2">Finishing Reject Total</th>
	                        <th width="70" rowspan="2">Pac &Fin. WIP</th>
	                        <th width="210" colspan="3">Ex-Factory</th>
	                        <th width="70" rowspan="2">Ex-Fac. WIP</th>
	                        <th rowspan="2">Remarks</th>
	                    </tr>
	                    <tr>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Bal.</th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Cutting Persent </th>
	                        <th width="60" rowspan="2" title="Plancut - Cutting Qty">Bal.</th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Bal.</th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today</th>
	                        <th width="60" rowspan="2">Total</th>
	                        <th width="60" rowspan="2">%</th>
	                        <th width="60" rowspan="2" title="Plancut - Input">Balance</th>

	                         <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>

	                        <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>

	                        <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>

	                        <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>
	                    </tr>
	                </thead>
	            </table>

	             <div style="max-height:425px; overflow-y:scroll; width:5760px;" id="scroll_body">
	                    <table  border="1" class="rpt_table"  width="5742" rules="all" id="table_body" >
	                    <?
	                      $total_cut=0;
	                      $total_print_iss=0;
						  $total_embl_iss=0;
						  $total_wash_iss=0;
						  $total_sp_iss=0;
	                      $total_print_receive=0;
						  $total_sp_rec=0;
						  $total_embl_rec=0;
						  $total_wash_receive=0;
						  $total_sp_rec=0;
	                      $total_sew_input=0;
	                      $total_sew_out=0;
						  $total_delivery_cut=0;
	                      $cutting_balance=0;
						  $print_issue_balance=0;
						  $print_rec_balance=0;
					  	  $deliv_cut_bal=0;
						  $total_sew_input_balance=0;
						  $input_percentage=0;
						  $inhand=0;
						  $buyer_total_order=0;
						  $buyer_total_plan=0;
						  $buyer_total_fabric_qty=0;
						  $buyer_total_fabric_pre=0;
						  $buyer_fabric_total=0;
						  $buyer_fabric_today_total=0;
						  $buyer_fabric_bal=0;
						  $buyer_pre_cut=0;
						  $buyer_today_cut=0;
						  $buyer_total_cut=0;
						  $buyer_cutting_balance=0;
						  $buyer_priv_print_iss=0;
						  $buyer_today_print_iss=0;
						  $buyer_print_issue_balance=0;
						  $buyer_priv_print_rec=0;
						  $buyer_today_print_rec=0;
						  $buyer_total_print_rec=0;
						  $buyer_print_rec_balance=0;
						  $buyer_priv_deliv_cut=0;
						  $buyer_today_deliv_cut=0;
						  $buyer_total_delivery_cut=0;
						  $buyer_deliv_cut_bal=0;
						  $buyer_priv_sew=0;
						  $buyer_today_sew=0;
						  $buyer_total_sew_=0;
						  $buyer_total_sew__bal=0;
						  $buyer_inhand=0;
						  $buyer_arr=array();
						  $job_arr=array();
	                      $i=1;$k=1;


		  //echo "jahid";die;

	  	foreach($po_number_data as $po_id=>$po_arr)
		{
			$po_wise_total_order=0;
		  	$po_wise_total_plan=0;
		  	$po_wise_total_fabric_qty=0;
		  	$po_wise_total_fabric_pre=0;
		  	$po_wise_fabric_total=0;
		  	$po_wise_fabric_today_total=0;
		  	$po_wise_fabric_bal=0;
		  	$po_wise_pre_cut=0;
		  	$po_wise_today_cut=0;
		  	$po_wise_total_cut=0;
		  	$po_wise_cutting_balance=0;
		  	$po_wise_priv_print_iss=0;
		  	$po_wise_today_print_iss=0;
		  	$po_wise_print_issue_balance=0;
		  	$po_wise_priv_print_rec=0;
		  	$po_wise_today_print_rec=0;
		  	$po_wise_total_print_rec=0;
		  	$po_wise_print_rec_balance=0;
		  	$po_wise_priv_deliv_cut=0;
		  	$po_wise_today_deliv_cut=0;
		  	$po_wise_total_delivery_cut=0;
		  	$po_wise_deliv_cut_bal=0;
		  	$po_wise_priv_sew=0;
		  	$po_wise_today_sew=0;
		  	$po_wise_total_sew_=0;
		  	$po_wise_total_sew__bal=0;
		  	$po_wise_inhand=0;

		foreach($po_arr as $item_id=>$item_arr)
		   {
		   	$r=0;
			 foreach($item_arr as $color_id=>$color_arr)
		     {
	 				if($i!=1)
					{
						if(!in_array($po_number_data[$po_id][$item_id][$color_id]['job_no'],$job_arr))
						{
								?>
								   <tr bgcolor="#CCCCCC" id="">
										<td width="40"><? // echo $i;?></td>
										<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
										<td width="60"></td>
										<td width="50"></td>
										<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
										<td width="80"></td>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
										<td width="80"></td>
										<td width="80"></td>
	                                    <td width="100"><strong></strong></td>
										<td width="120"><strong></strong></td>
										<td width="100" align="right"><strong>Job Total:</strong></td>
										<td width="70" align="right"><? echo $job_total_order; ?></td>
										<td width="70" align="right"><?  echo $job_total_plan; ?></td>
										<td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
										<td width="70" align="right"><?  echo number_format($job_total_fabric_qty,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_total_fabric_pre,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_today_total,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_total,2);?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_bal,2); ?></td>

										<td width="70" align="right"><?  echo number_format($job_possible_cut_qty,0); ?></td>
										<td width="60" align="right"><?  echo $job_pre_cut; ?></td>
										<td width="60" align="right"><?  echo $job_today_cut; ?></td>
										<td width="60" align="right"><?  echo $job_total_cut; ?></td>
										<td width="60" align="right"><?  //echo $job_total_cut; ?></td>
										<td width="60" align="right"><?  echo $job_cutting_balance; ?></td>
										<td width="60" align="right"><?  echo number_format($job_possible_cut_qty,0)-$job_total_cut; ?></td>
										<td width="60" align="right"><?  echo $job_priv_deliv_cut; ?></td>
										<td width="60" align="right"><?  echo $job_today_deliv_cut; ?></td>
										<td width="60" align="right"><?  echo $job_total_delivery_cut; ?></td>
										<td width="60" align="right"><?  echo $job_deliv_cut_bal; ?></td>
										<td width="60" align="right"><?  echo $job_priv_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_today_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_priv_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_today_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_iss-$job_total_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_priv_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_today_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_priv_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_today_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_iss-$job_total_embl_rec; ?></td>

										<td width="60" align="right"><?  echo $job_priv_sew; ?></td>
										<td width="60" align="right"><?  echo $job_today_sew; ?></td>
										<td width="60" align="right"> <? echo $job_total_sew_input; ?></td>
										<td width="60" align="right"><? //echo $input_percentage; ?></td>
										<td width="60" align="right"><?  echo $job_total_sew__bal; ?></td>
										<td width="70" align="right"><? echo $job_cutinhand; ?></td>
										<td width="70" align="right"><? echo $job_inhand; ?></td>
										<td width="100" align="right"><? echo $job_ready_to_sewing; ?></td>
										<td width="100" align="right"><? //echo  $sewing_line ?></td>

	                                    <td width="60" align="right"><? //echo  $unit ?></td>
	                                    <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_bal ?></td>


	                                   	 <td width="70" align="right"><? echo  $job_priv_sewout; ?></td>
	                                     <td width="70" align="right"><? echo  $job_today_sewout ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sew_out_bal; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sew_out_reject_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sew_out_reject_bal; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sewingout_wip_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_poly_qnty_pre; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_poly_qnty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_poly_qnty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_ploy_rej_qty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_ploy_rej_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_tot_poly_wip; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_qnty_pre; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_qnty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_qnty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_rej_qty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_rej_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_tot_fin_wip; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_exf_qty_pre; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_exf_qty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_exf_tot_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_tot_exf_wip; ?></td>


										<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
								  </tr>
								<?
								  $job_inhand=0;
								  $job_cutinhand=0;
								  $job_possible_cut_qty=0;
								  $job_total_order=0;
								  $job_total_plan=0;
								  $job_total_fabric_qty=0;
								  $job_total_fabric_pre=0;
								  $job_fabric_total=0;
								  $job_fabric_today_total=0;
								  $job_fabric_bal=0;
								  $job_pre_cut=0;
								  $job_today_cut=0;
								  $job_total_cut=0;
								  $job_cutting_balance=0;
								  $job_priv_print_iss=0;
								  $job_today_print_iss=0;
								  $job_total_print_iss=0;
								  $job_print_issue_balance=0;
								  $job_priv_print_rec=0;
								  $job_today_print_rec=0;
								  $job_total_print_rec=0;
								  $job_print_rec_balance=0;
								  $job_priv_deliv_cut=0;
								  $job_today_deliv_cut=0;
								  $job_total_delivery_cut=0;
								  $job_deliv_cut_bal=0;
								  $job_priv_sew=0;
								  $job_today_sew=0;
								  $job_total_sew_input=$job_today_sewout=$job_total_sew_out_bal=0;
								  $job_total_sew_out_reject_today=$job_total_sew_out_reject_bal=0;$job_total_sewingout_wip_qty=0;
								  $job_total_poly_qnty_pre=$job_total_poly_qnty_today=$job_total_poly_qnty=$job_total_ploy_rej_qty_pre=$job_total_ploy_rej_qty_pre=$job_total_ploy_rej_qty_today=$job_total_ploy_rej_qty=$job_total_tot_poly_wip=0;
								  $job_total_fin_qnty_pre=$job_total_fin_qnty_today=$job_total_fin_qnty=$job_total_fin_rej_qty_pre=$job_total_fin_rej_qty_today=$job_total_fin_rej_qty=$job_total_tot_fin_wip=0;
								  $job_total_exf_qty_pre=$job_total_exf_qty_today=$job_total_exf_tot_qty=$job_total_tot_exf_wip=0;
								  $job_total_sew__bal=0;
								  $job_priv_print_iss=0;
								  $job_today_print_iss=0;
								  $job_total_print_iss=0;
								  $job_priv_embl_iss=0;
								  $job_priv_print_rec=0;
								  $job_today_embl_iss=0;
								  $job_total_embl_iss=0;
								  $job_today_wash_iss=0;
								  $job_priv_wash_iss=0;
								  $job_today_sp_iss=0;
								  $job_total_wash_iss=0;
								  $job_total_sp_iss=0;
								  $job_priv_sp_iss=0;
								  $job_priv_print_rec=0;
								  $job_today_print_rec=0;
								  $job_total_print_rec=0;
								  $job_priv_wash_rec=0;
								  $job_today_wash_rec=0;
								  $job_total_wash_rec=0;
								  $job_priv_embl_rec=0;
								  $job_today_embl_rec=0;
								  $job_total_embl_rec=0;
								  $job_priv_sp_rec=0;
								  $job_today_sp_rec=0;
								  $job_total_sp_rec=0;
								  $job_ready_to_sewing=$job_priv_sewout=0;
						  }
					}


					if($i!=1)
					{
					 	if( !in_array($po_number_data[$po_id][$item_id][$color_id]['buyer_name'],$buyer_arr))
						{

							?>
							<tr bgcolor="#999999" style="height:15px">
							<td width="40"><? // echo $i;?></td>
							<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
							<td width="60"></td>
							<td width="50"></td>
							<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
							<td width="80"></td>
	                        <td width="80"></td>
	                        <td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="100"><strong> </strong></td>
	                        <td width="120"><strong> </strong></td>
							<td width="100" align="right"><strong>Buyer Total:</strong></td>
							<td width="70" align="right"><? echo $buyer_total_order; ?></td>
							<td width="70" align="right"><?  echo $buyer_total_plan; ?></td>
							<td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
							<td width="70" align="right"><?  echo number_format($buyer_total_fabric_qty,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_total_fabric_pre,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_today_total,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_total,2);?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_bal,2); ?></td>

							<td width="70" align="right"><?  echo number_format($buyer_possible_cut_qty,0); ?></td>
							<td width="60" align="right"><?  echo $buyer_pre_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_cut; ?></td>
							<td width="60" align="right"><?  //echo $buyer_total_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_cutting_balance; ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_possible_cut_qty,0)-$buyer_total_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_deliv_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_deliv_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_delivery_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_deliv_cut_bal; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_print_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_print_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_print_rec; ?></td>
						   <td width="60" align="right"><?   echo $buyer_total_print_iss-$buyer_total_print_rec; ?></td>

							<td width="60" align="right"><?  echo $buyer_priv_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_iss-$buyer_total_embl_rec; ?></td>


							<td width="60" align="right"><?  echo $buyer_priv_sew; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_sew; ?></td>
							<td width="60" align="right"> <? echo $buyer_total_sew_; ?></td>
							<td width="60" align="right"><? //echo $input_percentage; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_sew_bal; ?></td>
							<td width="70" align="right"><? echo $buyer_cutinhand; ?></td>
							<td width="70" align="right"><? echo $buyer_inhand; ?></td>
							<td width="100" align="right"><? echo $buyer_ready_to_sewing; ?></td>

							<td width="100" align="right"><? //echo  $sewing_line ?></td>
	                        <td width="60" align="right"><? //echo  $unit ?></td>
	                        <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                        <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                        <td width="80" align="right"><? //echo  $cutting_bal ?></td>

	                        <td width="70" align="right"><? echo  $buyer_priv_sew_out ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_today ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_bal; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_bal; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sewingout_wip_qty ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_poly_qnty_pre; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_poly_qnty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_poly_qnty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_tot_poly_wip; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_qnty_pre; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_qnty_today;?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_qnty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_tot_fin_wip; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_exf_qty_pre; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_exf__qty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_exf_tot_qty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_tot_exf_wip ;?></td>


							<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
					  	</tr>
								<?
								  $buyer_cutinhand=$buyer_ready_to_sewing=0;
								  $buyer_possible_cut_qty=0;
								  $buyer_total_order=0;
								  $buyer_total_plan=0;
								  $buyer_total_fabric_qty=0;
								  $buyer_total_fabric_pre=0;
								  $buyer_fabric_total=0;
								  $buyer_fabric_today_total=0;
								  $buyer_fabric_bal=0;
								  $buyer_pre_cut=0;
								  $buyer_today_cut=0;
								  $buyer_total_cut=0;
								  $buyer_cutting_balance=0;
								  $buyer_priv_print_iss=0;
								  $buyer_today_print_iss=0;
								  $buyer_total_print_iss=0;
								  $buyer_print_issue_balance=0;
								  $buyer_priv_print_rec=0;
								  $buyer_today_print_rec=0;
								  $buyer_total_print_rec=0;
								  $buyer_print_rec_balance=0;
								  $buyer_priv_deliv_cut=0;
								  $buyer_today_deliv_cut=0;
								  $buyer_total_delivery_cut=0;
								  $buyer_deliv_cut_bal=0;
								  $buyer_priv_sew=0;
								  $buyer_today_sew=$buyer_priv_sew_out=0;
								  $buyer_total_sew_=$buyer_total_sew_out_today=0;
								  $buyer_total_sew_out_reject_today=$buyer_total_sew_out_reject_bal=$buyer_total_sewingout_wip_qty=0;
								  $buyer_total_poly_qnty_pre=$buyer_total_poly_qnty_today=$buyer_total_poly_qnty=$buyer_total_ploy_rej_qty_pre=$buyer_total_ploy_rej_qty_today=$buyer_total_ploy_rej_qty=$buyer_total_tot_poly_wip=0;
								  $buyer_total_fin_qnty_pre=$buyer_total_fin_qnty_today=$buyer_total_fin_qnty=$buyer_total_fin_rej_qty_pre=$buyer_total_fin_rej_qty_today=$buyer_total_fin_rej_qty=$buyer_total_tot_fin_wip=0;
								  $buyer_total_exf_qty_pre=$buyer_total_exf__qty_today=$buyer_total_exf_tot_qty=$buyer_total_tot_exf_wip=0;
								  $buyer_total_sew__bal=0;
								  $buyer_inhand=0;
								  $buyer_priv_print_iss=0;
								  $buyer_today_print_iss=0;
								  $buyer_total_print_iss=0;
								  $buyer_priv_embl_iss=0;
								  $buyer_priv_print_rec=0;
								  $buyer_today_embl_iss=0;
								  $buyer_total_embl_iss=0;
								  $buyer_today_wash_iss=0;
								  $buyer_priv_wash_iss=0;
								  $buyer_today_sp_iss=0;
								  $buyer_total_wash_iss=0;
								  $buyer_total_sp_iss=0;
								  $buyer_priv_sp_iss=0;
								  $buyer_priv_print_rec=0;
								  $buyer_today_print_rec=0;
								  $buyer_total_print_rec=0;
								  $buyer_priv_wash_rec=0;
								  $buyer_today_wash_rec=0;
								  $buyer_total_wash_rec=0;
								  $buyer_priv_embl_rec=0;
								  $buyer_today_embl_rec=0;
								  $buyer_total_embl_rec=0;
								  $buyer_priv_sp_rec=0;
								  $buyer_today_sp_rec=0;
								  $buyer_total_sp_rec=0;
							}
					  }
					//***********************for line****************************************************************************************
					$line_id_all=$sew_line_arr[$po_id]['line'];
					$line_name="";
					foreach(array_unique(explode(",",$line_id_all)) as $l_id)
					{
						if($line_name!="") $line_name.=",";
						if($prod_reso_allo==1)
						{
						$line_name.= $lineArr[$prod_reso_arr[$l_id]];
						}
						else
						{
						$line_name.= $lineArr[$l_id];
						}
					}

					$floor_id_all=$sew_line_arr[$po_id]['floor_id'];
					$floor_name="";
					foreach(array_unique(explode(",",$floor_id_all)) as $f_id)
					{
						if($floor_name!="") $floor_name.=",";
						$floor_name.= $floor_arr[$f_id];
					}

					$costing_per=$costing_per_arr[$po_number_data[$po_id][$item_id][$color_id]['job_no']];
					if($costing_per==1)
					{
						$costing_per_qty=12;
					}
					else if($costing_per==2)
					{
						$costing_per_qty=1;
					}
					else if($costing_per==3)
					{
						$costing_per_qty=24;
					}
					else if($costing_per==4)
					{
						$costing_per_qty=36;
					}
					else if($costing_per==5)
					{
						$costing_per_qty=48;
					}

					//$fabric_wip=$fabric_wip[$po_id][$color_id]['issue']-$fabric_wip[$po_id][$color_id]['receive'];
					$ready_to_sewing=$ready_to_sewing_arr[$po_id][$item_id][$color_id];
				    $fabric_pre=$fabric_pre_qty[$po_id][$color_id];
					$fabric_today=$fabric_today_qty[$po_id][$color_id];
					$total_fabric=$fabric_pre+$fabric_today;
					$possible_cut_qty=($total_fabric/($con_per_dzn[$po_id][$color_id]/$costing_per_qty));
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					// $con = $con_per_dzn[$po_id][$color_id]*12/$costing_per_qty;
					$fabric_qty=($plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty']*$con_per_dzn[$po_id][$color_id])/$costing_per_qty;

					//echo $po_number_data[$po_id][$item_id][$color_id]['plan_qty'].'=='.$con_per_dzn[$po_id][$color_id].'=='.$costing_per_qty.', ';
				    $fabric_balance=$fabric_qty-$total_fabric;
					$total_cut=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$cutting_balance=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'] - $total_cut;
					// echo $po_number_data[$po_id][$item_id][$color_id]['plan_qty'].'-'.$total_cut;die('go to hell');
					$total_print_iss=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre']+$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$total_embl_iss=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre']+$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$total_embl_rec=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre']+$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$total_print_receive=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];

					//echo $inhand."***";die;
					$print_balance=$total_print_iss-$total_print_receive;
					$embl_balance=$total_embl_iss-$total_embl_rec;
					$wash_balance=$total_wash_iss-$total_wash_receive;
					$sp_balance=$total_sp_iss-$total_sp_rec;
					$total_emblishment_iss=$total_embl_iss+$total_wash_iss+$total_print_iss+$total_sp_iss;
					//$total_emblishment_rec=$total_wash_receive+$total_embl_rec+$total_sp_rec+$total_print_rec;
					$print_issue_balance=$total_emblishment_iss-$total_cut;
					$print_rec_balance=$total_emblishment_rec-$total_emblishment_iss;
					$total_delivery_cut=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$deliv_cut_bal=($total_cut)-$total_delivery_cut;
					$total_sew_input=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$total_sew_input_balance=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'] - $total_sew_input;
					$input_percentage=($total_sew_input/$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'])*100;
					// $input_percentage plan cut qty change to po qty by saeed vie;
					$sewingout_qnty_pre=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_pre'];
					$sewingout_qnty_today=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_today'];

					$sewout_rej_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['sewout_rej_qty_today'];
					$sewout_rej_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['sewout_rej_qty_pre'];

					$fin_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['fin_qty_pre'];
					$fin_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['fin_qty_today'];
					$fin_rej_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['fin_rej_qty_pre'];
					$fin_rej_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['fin_rej_qty_today'];

					$poly_qnty_pre=$production_data_arr[$po_id][$item_id][$color_id]['poly_qnty_pre'];
					$poly_qnty_today=$production_data_arr[$po_id][$item_id][$color_id]['poly_qnty_today'];
					$ploy_rej_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['ploy_rej_qty_pre'];
					$ploy_rej_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['ploy_rej_qty_today'];

					$total_poly_qnty=$poly_qnty_pre+$poly_qnty_today;
					$total_ploy_rej_qty=$ploy_rej_qty_pre+$ploy_rej_qty_today;


					$total_sewingout_qnty_bal=$sewingout_qnty_pre+$sewingout_qnty_today;
					$total_sewingout_reject_qty_bal=$sewout_rej_qty_today+$sewout_rej_qty_pre;
					$total_sewingout_wip_qty=$total_sewingout_qnty_bal-$total_sew_input;
					$tot_poly_wip=(($total_poly_qnty+$total_ploy_rej_qty)-$total_sewingout_qnty_bal);

					$fin_qty_pre=$fin_qty_pre;
					$fin_qty_today=$fin_qty_today;
					$fin_rej_qty_today=$fin_rej_qty_today;
					$fin_rej_qty_pre=$fin_rej_qty_pre;

					$tot_fin_qnty=$fin_qty_pre+$fin_qty_today;
					$tot_fin_reject_qty=$fin_rej_qty_today+$fin_rej_qty_pre;
				    $tot_fin_wip=(($tot_fin_qnty+$tot_fin_reject_qty)-$total_poly_qnty);
					// $tot_fin_wip=$total_sewingout_qnty_bal-$tot_fin_qnty;
					if($ex_factory_level==1) // when gross level
					{
						$exf_qnty_today=$production_data[$po_id][$item_id]['exf_qnty_today'];
						$exf_qnty_pre=$production_data[$po_id][$item_id]['exf_qnty_pre'];
					}
					else
					{
						$exf_qnty_today=$production_data[$po_id][$item_id][$color_id]['exf_qnty_today'];
						$exf_qnty_pre=$production_data[$po_id][$item_id][$color_id]['exf_qnty_pre'];
					}

					//echo $exf_qnty_today.'='.$exf_qnty_pre;
					$tot_exf_qnty=$exf_qnty_today+$exf_qnty_pre;
					$ex_fact_wip=($tot_exf_qnty-$tot_fin_qnty);



					$cutting_inhand=0;
					$cutting_inhand=$total_cut-$total_delivery_cut;
					$inhand=0;
					if($total_print_iss!=0 && $total_embl_iss!=0)
					{
						if(date("Y-m-d",strtotime($production_data_arr[$po_id][$item_id][$color_id]['min_printin_date']))<=date("Y-m-d",strtotime($production_data_arr[$po_id][$item_id][$color_id]['min_embl_date'])))
					    {
				        $inhand=($total_delivery_cut+$total_embl_rec)-($total_sew_input+$total_print_iss);
						}
						else
						{
						$inhand=($total_delivery_cut+$total_embl_iss)-($total_sew_input+$total_print_receive);
						}
					}
					else if ($total_print_iss!=0) { $inhand=(($total_delivery_cut+$total_print_receive)-($total_print_iss+$total_sew_input));}
					else if ($total_embl_iss!=0) { $inhand=($total_delivery_cut+$total_embl_rec)-($total_embl_iss+$total_sew_input);}
					else if($total_delivery_cut!=0)$inhand=$total_delivery_cut-$total_sew_input;
					else $inhand=0;  // $inhand=$total_cut-$total_sew_input;
					//if($inhand<0) $inhand=0;


					//for job total *******************************************************************************************************
					$job_possible_cut_qty+=$possible_cut_qty;
					$job_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$job_total_plan+=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];
					$job_total_fabric_qty+=$fabric_qty;
					$job_total_fabric_pre+=$fabric_pre;
					$job_fabric_today_total+=$fabric_today;
					$job_fabric_total+=$total_fabric;
					$job_fabric_bal+=$fabric_balance;
					$job_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$job_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$job_total_cut+=$total_cut;
					$job_cutting_balance+=$cutting_balance;
					$job_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$job_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$job_total_print_iss+=$total_print_iss;
					$job_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$job_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$job_total_embl_iss+=$total_embl_iss;
					$job_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$job_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$job_total_wash_iss+=$total_wash_iss;
					$job_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$job_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$job_total_sp_iss+=$total_sp_iss;
					$job_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$job_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$job_total_print_rec+=$total_print_receive;
					$job_print_issue_balance=$print_issue_balance;
					$job_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$job_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$job_total_wash_rec+=$total_wash_receive;
					$job_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$job_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$job_total_embl_rec+=$total_embl_rec;
					$job_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$job_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$job_total_sp_rec+=$total_sp_rec;
					$job_print_rec_balance+=$print_rec_balance;
					$job_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$job_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$job_total_delivery_cut+=$total_delivery_cut;
					$job_deliv_cut_bal+=$deliv_cut_bal;
					$job_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$job_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$job_priv_sewout+=$sewingout_qnty_pre;
					$job_today_sewout+=$sewingout_qnty_today;
					$job_priv_sew_input+=$total_sew_input;
					$job_total_sew_bal+=$total_sew_input_balance;
					$job_total_sew_out_bal+=$total_sewingout_qnty_bal;
					$job_total_sew_out_reject_bal+=$total_sewingout_reject_qty_bal;
					$job_total_sew_out_reject_today+=$sewout_rej_qty_today;
					$job_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$job_total_poly_qnty_pre+=$poly_qnty_pre;
					$job_total_poly_qnty_today+=$poly_qnty_today;
					$job_total_poly_qnty+=$total_poly_qnty;

					$job_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;
					$job_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$job_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$job_total_tot_poly_wip+=$tot_poly_wip;
					//Fin and Pack
					$job_total_fin_qnty_pre+=$fin_qty_pre;
					$job_total_fin_qnty_today+=$fin_qty_today;
					$job_total_fin_qnty+=$tot_fin_qnty;

					$job_total_fin_rej_qty_pre+=$fin_rej_qty_pre;
					$job_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$job_total_fin_rej_qty+=$tot_fin_reject_qty;
					$job_total_tot_fin_wip+=$tot_fin_wip;

					$job_total_exf_qty_pre+=$exf_qnty_pre;
					$job_total_exf_qty_today+=$exf_qnty_today;
					$job_total_exf_tot_qty+=$tot_exf_qnty;
					$job_total_tot_exf_wip+=$ex_fact_wip;


					$job_inhand+=$inhand;
					$job_cutinhand+=$cutting_inhand;
					$job_ready_to_sewing+=$ready_to_sewing;
					//buyer sub total **************************************************************************************************
					$buyer_possible_cut_qty+=$possible_cut_qty;
					$buyer_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$buyer_total_plan+=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];
					$buyer_total_fabric_qty+=$fabric_qty;
					$buyer_total_fabric_pre+=$fabric_pre;
					$buyer_fabric_today_total+=$fabric_today;
					$buyer_fabric_total+=$total_fabric;
					$buyer_fabric_bal+=$fabric_balance;
					$buyer_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$buyer_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$buyer_total_cut+=$total_cut;
					$buyer_cutting_balance+=$cutting_balance;
					$buyer_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$buyer_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$buyer_total_print_iss+=$total_print_iss;
					$buyer_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$buyer_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$buyer_total_embl_iss+=$total_embl_iss;
					$buyer_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$buyer_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$buyer_total_wash_iss+=$total_wash_iss;
					$buyer_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$buyer_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$buyer_total_sp_iss+=$total_sp_iss;
					$buyer_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$buyer_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$buyer_total_print_rec+=$total_print_receive;
					$buyer_print_issue_balance=$print_issue_balance;
					$buyer_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$buyer_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$buyer_total_wash_rec+=$total_wash_receive;
					$buyer_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$buyer_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$buyer_total_embl_rec+=$total_embl_rec;
					$buyer_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$buyer_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$buyer_total_sp_rec+=$total_sp_rec;
					$buyer_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$buyer_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$buyer_total_delivery_cut+=$total_delivery_cut;
					$buyer_deliv_cut_bal+=$deliv_cut_bal;
					$buyer_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$buyer_priv_sew_out+=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_pre'];
					$buyer_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$buyer_total_sew_+=$total_sew_input;
					$buyer_total_sew_bal+=$total_sew_input_balance;
					$buyer_total_sew_out_pre+=$sewingout_qnty_pre;
					$buyer_total_sew_out_today+=$sewingout_qnty_today;
					$buyer_total_sew_out_bal+=$total_sewingout_qnty_bal;
					$buyer_total_sew_out_reject_today+=$sewout_rej_qty_today;
					$buyer_total_sew_out_reject_bal+=$total_sewingout_reject_qty_bal;
					$buyer_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$buyer_total_poly_qnty_pre+=$poly_qnty_pre;
					$buyer_total_poly_qnty_today+=$poly_qnty_today;
					$buyer_total_poly_qnty+=$total_poly_qnty;
					$buyer_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;

					$buyer_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$buyer_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$buyer_total_tot_poly_wip+=$tot_poly_wip;

					// Fin & Pack
					$buyer_total_fin_qnty_pre+=$fin_qty_pre;
					$buyer_total_fin_qnty_today+=$fin_qty_today;
					$buyer_total_fin_qnty+=$tot_fin_qnty;
					$buyer_total_fin_rej_qty_pre+=$fin_rej_qty_pre;

					$buyer_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$buyer_total_fin_rej_qty+=$tot_fin_reject_qty;
					$buyer_total_tot_fin_wip+=$tot_fin_wip;

					$buyer_total_exf_qty_pre+=$exf_qnty_pre;
					$buyer_total_exf__qty_today+=$exf_qnty_today;
					$buyer_total_exf_tot_qty+=$tot_exf_qnty;
					$buyer_total_tot_exf_wip+=$ex_fact_wip;

					$buyer_inhand+=$inhand;
					$buyer_cutinhand+=$cutting_inhand;
					$buyer_ready_to_sewing+=$ready_to_sewing;

					//buyer sub total **************************************************************************************************
					$po_wise_possible_cut_qty+=$possible_cut_qty;
					$po_wise_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$po_wise_total_plan+=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];
					$po_wise_total_fabric_qty+=$fabric_qty;
					$po_wise_total_fabric_pre+=$fabric_pre;
					$po_wise_fabric_today_total+=$fabric_today;
					$po_wise_fabric_total+=$total_fabric;
					$po_wise_fabric_bal+=$fabric_balance;
					$po_wise_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$po_wise_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$po_wise_total_cut+=$total_cut;
					$po_wise_cutting_balance+=$cutting_balance;
					$po_wise_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$po_wise_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$po_wise_total_print_iss+=$total_print_iss;
					$po_wise_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$po_wise_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$po_wise_total_embl_iss+=$total_embl_iss;
					$po_wise_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$po_wise_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$po_wise_total_wash_iss+=$total_wash_iss;
					$po_wise_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$po_wise_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$po_wise_total_sp_iss+=$total_sp_iss;
					$po_wise_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$po_wise_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$po_wise_total_print_rec+=$total_print_receive;
					$po_wise_print_issue_balance=$print_issue_balance;
					$po_wise_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$po_wise_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$po_wise_total_wash_rec+=$total_wash_receive;
					$po_wise_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$po_wise_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$po_wise_total_embl_rec+=$total_embl_rec;
					$po_wise_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$po_wise_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$po_wise_total_sp_rec+=$total_sp_rec;
					$po_wise_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$po_wise_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$po_wise_total_delivery_cut+=$total_delivery_cut;
					$po_wise_deliv_cut_bal+=$deliv_cut_bal;
					$po_wise_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$po_wise_priv_sew_out+=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_pre'];
					$po_wise_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$po_wise_total_sew_+=$total_sew_input;
					$po_wise_total_sew_bal+=$total_sew_input_balance;
					$po_wise_total_sew_out_pre+=$sewingout_qnty_pre;
					$po_wise_total_sew_out_today+=$sewingout_qnty_today;
					$po_wise_total_sew_out_bal+=$total_sewingout_qnty_bal;
					$po_wise_total_sew_out_reject_today+=$sewout_rej_qty_today;
					$po_wise_total_sew_out_reject_bal+=$total_sewingout_reject_qty_bal;
					$po_wise_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$po_wise_total_poly_qnty_pre+=$poly_qnty_pre;
					$po_wise_total_poly_qnty_today+=$poly_qnty_today;
					$po_wise_total_poly_qnty+=$total_poly_qnty;
					$po_wise_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;

					$po_wise_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$po_wise_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$po_wise_total_tot_poly_wip+=$tot_poly_wip;

					// Fin & Pack
					$po_wise_total_fin_qnty_pre+=$fin_qty_pre;
					$po_wise_total_fin_qnty_today+=$fin_qty_today;
					$po_wise_total_fin_qnty+=$tot_fin_qnty;
					$po_wise_total_fin_rej_qty_pre+=$fin_rej_qty_pre;

					$po_wise_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$po_wise_total_fin_rej_qty+=$tot_fin_reject_qty;
					$po_wise_total_tot_fin_wip+=$tot_fin_wip;

					$po_wise_total_exf_qty_pre+=$exf_qnty_pre;
					$po_wise_total_exf__qty_today+=$exf_qnty_today;
					$po_wise_total_exf_tot_qty+=$tot_exf_qnty;
					$po_wise_total_tot_exf_wip+=$ex_fact_wip;

					$po_wise_inhand+=$inhand;
					$po_wise_cutinhand+=$cutting_inhand;
					$po_wise_ready_to_sewing+=$ready_to_sewing;

					// for grand total ********************************************************************************************************************
					$grand_possible_cut_qty+=$possible_cut_qty;
					$grand_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$grand_total_plan+=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];
					$grand_total_fabric_qty+=$fabric_qty;
					$grand_total_fabric_pre+=$fabric_pre;
					$grand_fabric_today_total+=$fabric_today;
					$grand_fabric_total+=$total_fabric;
					$grand_fabric_bal+=$fabric_balance;
					$grand_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$grand_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$grand_total_cut+=$total_cut;
					$grand_cutting_balance+=$cutting_balance;
					$grand_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$grand_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$grand_total_print_iss+=$total_print_iss;
					$grand_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$grand_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$grand_total_embl_iss+=$total_embl_iss;
					$grand_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$grand_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$grand_total_wash_iss+=$total_wash_iss;
					$grand_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$grand_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$grand_total_sp_iss+=$total_sp_iss;
					$grand_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$grand_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$grand_total_print_rec+=$total_print_receive;
					$grand_print_issue_balance=$print_issue_balance;
					$grand_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$grand_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$grand_total_wash_rec+=$total_wash_receive;
					$grand_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$grand_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$grand_total_embl_rec+=$total_embl_rec;
					$grand_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$grand_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$grand_total_sp_rec+=$total_sp_rec;
					$grand_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$grand_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$grand_total_delivery_cut+=$total_delivery_cut;
					$grand_deliv_cut_bal+=$deliv_cut_bal;
					$grand_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$grand_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$grand_total_sew_+=$total_sew_input;
					$grand_total_sewingout_qnty_pre+=$sewingout_qnty_pre;
					$grand_total_sewingout_qnty_today+=$sewingout_qnty_today;
					$grand_total_sewingout_qnty_bal+=$total_sewingout_qnty_bal;
					$grand_total_sewingout_qnty_reject_today+=$sewout_rej_qty_today;
					$grand_total_sewingout_qnty_reject_bal+=$total_sewingout_reject_qty_bal;
					$grand_total_sewingout_qnty_reject_today+=$sewout_rej_qty_today;
					$grand_total_sew_bal+=$total_sew_input_balance;
					$grand_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$grand_total_poly_qnty_pre+=$poly_qnty_pre;
					$grand_total_poly_qnty_today+=$poly_qnty_today;
					$grand_total_poly_qnty+=$total_poly_qnty;
					$grand_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;
					$grand_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$grand_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$grand_total_tot_poly_wip+=$tot_poly_wip;

					//Fin & Pack

					$grand_total_fin_qnty_pre+=$fin_qty_pre;
					$grand_total_fin_qnty_today+=$fin_qty_today;
					$grand_total_fin_qnty+=$tot_fin_qnty;
					$grand_total_fin_rej_qty_pre+=$fin_rej_qty_pre;
					$grand_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$grand_total_fin_rej_qty+=$tot_fin_reject_qty;
				    $grand_total_tot_fin_wip+=$tot_fin_wip;
					// $grand_total_tot_fin_wip+=$total_sewingout_qnty_bal-$tot_fin_qnty;

					$grand_total_exf_qty_pre+=$exf_qnty_pre;
					$grand_total_exf_qty_today+=$exf_qnty_today;
					$grand_total_exf_qty+=$tot_exf_qnty;
					$grand_total_tot_exf_wip+=$ex_fact_wip;

					$grand_inhand+=$inhand;
					$grand_cutinhand+=$cutting_inhand;
					$grand_ready_to_sewing+=$ready_to_sewing;

	                    ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
	                    <td width="40"><? echo $i; ?></td>
	                    <td width="80" style="word-break:break-all;"><p><? echo $buyer_short_library[$po_number_data[$po_id][$item_id][$color_id]['buyer_name']]; ?></p></td>
	                    <td width="60" align="center"><? echo $po_number_data[$po_id][$item_id][$color_id]['job_prifix'];?></td>
	                    <td width="50" align="right"><? echo $po_number_data[$po_id][$item_id][$color_id]['year'];?></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['po_number'];?></p></td>
	                    <td width="80" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['file_no'];?></p></td>
	                    <td width="80" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['int_ref_no'];?></p></td>
	                    <td width="80" align="center"><?  echo  change_date_format($tna_date_arr[$po_id]['tna_start']);  ?></td>
	                    <td width="80" align="center"><?  echo  change_date_format($tna_date_arr[$po_id]['tna_end']);  ?></td>
	                    <td width="80" align="center"><?  echo  change_date_format($po_min_shipdate_data[$po_id][$item_id][$color_id]['minimum_shipdate']);  ?></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['style']; ?></p></td>
	                    <td width="120" align="center" title="Item Id=<? echo $item_id;?>" style="word-break:break-all;"><p><? echo $garments_item[$item_id]; ?></p></td>
	                    <td width="100" align="center" title="color_id=<? echo $color_id;?>" style="word-break:break-all;"><p><? echo $colorname_arr[$po_number_data[$po_id][$item_id][$color_id]['color_id']]; ?></p></td>
	                    <td width="70" align="right"><?  echo $order_qnty=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity']; ?></td>
	                    <td width="70" align="right"><?  echo $plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty']; ?></td>
	                    <td width="70" align="right"><?  echo number_format(($con_per_dzn[$po_id][$color_id]*12/$costing_per_qty),3); ?></td>
	                    <td width="70" align="right"  title="consumption per pcs * Plan Cut Qty"><?  echo number_format($fabric_qty,2); ?></td>
	                    <td width="60" align="right"><?  echo number_format($fabric_pre,2); ?></td>
	                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,1)"><?  echo number_format($fabric_today,2); ?></a></td>
	                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,2)"><?  echo number_format($total_fabric,2); ?></a><?  //echo number_format($total_fabric,2);?></td>
	                    <td width="60" align="right"><?  echo number_format($fabric_balance,2); ?></td>

	                    <td width="70" align="right" title="Possible Cut Qty. =Total Fabric Receive/ Consumption Per Pcs."><?  echo  number_format($possible_cut_qty,0); ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre']; //$production_data_arr[$row_fab[csf('po_breakdown_id')]][$row_fab[csf('color_id')]]['cutting_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',950,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"> <?  echo $production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',950,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_cut; ?></a></td>
	                    <td width="60" align="right" title="(Total Cut - Order qty)*100/Total Cut"><? $cut_persent=(($total_cut - $order_qnty)*100) / $total_cut; echo number_format($cut_persent,2); ?></td>
	                    <td width="60" align="right"><?  echo $cutting_balance; ?></td>
	                    <td width="60" align="right" title="Cutting WIP=Possible Cut Qty - Total Cutting Qty"><?  echo number_format($possible_cut_qty-$total_cut,0); ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_delivery_cut; ?></a></td>
	                    <td width="60" align="right"><?  echo $deliv_cut_bal; ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printing_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_print_iss; ?></a></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$color_id]['printreceived_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_print_receive; ?></a></td>
	                    <td width="60" align="right" title="Print WIP=Delivery to Print-Receive from Print"><?  echo $print_balance; ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$color_id]['embl_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['embl_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_embl_iss; ?></a></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_embl_rec; ?></a></td>
	                    <td width="60" align="right" title="Embl. WIP=Delivery to Embl.-Receive from Embl."><?  echo $embl_balance; ?></td>

	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"> <? echo $total_sew_input; ?></a></td>
	                    <td width="60" align="right" title="(Total Sewing Input*100)/Order Qty"><?  echo number_format($input_percentage,2); ?></td>
	                    <td width="60" align="right"><?  echo $total_sew_input_balance; ?></td>
	                    <td width="70" align="right" title="Inhand=Total Cut - Delivery to Input" ><?  echo $cutting_inhand; ?></td>
	                     <td width="70" align="right" title="Inhand= Cutting Delivery To Input  - Delivery to  Embellishment &#13; + Receive from Embellishment -Sewing Input" ><?  echo $inhand; ?></td>
	                    <td width="100" align="right"><p><? echo $ready_to_sewing; ?></p></td>
	                    <td width="100" align="center"><p><? echo $line_name; ?> </p></td>

	                    <td width="60" align="center"><p><? echo $floor_name; ?></p></td>
	                    <td width="80" align="right"><p><? $fin_cons=0; $cutable_pcs=0; $fin_cons=number_format(($con_per_dzn[$po_id][$color_id]*12/$costing_per_qty),3); $cutable_pcs=($total_fabric/$fin_cons)*12; echo number_format($cutable_pcs,2); ?></p></td>
	                    <td width="80" align="right"><p><? $cutting_kg=0; $cutting_kg=($fin_cons/12)*$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']; echo number_format($cutting_kg,2); ?></p></td>
	                    <td width="80" align="right"><p><? $cutting_bal=0; $cutting_bal=$total_fabric-$cutting_kg; echo number_format($cutting_bal,2); ?></p></td>

	                     <td width="70" align="right"><p><? echo number_format($sewingout_qnty_pre,2); ?></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,5,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><? echo number_format($sewingout_qnty_today,2); ?></a></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,5,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($total_sewingout_qnty_bal,2); ?></a></p></td>
	                     <td width="70" align="right"><p><?  echo number_format($sewout_rej_qty_today,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($total_sewingout_reject_qty_bal,2); ?></p></td>
	                     <td width="70" align="right"><p><?  echo number_format($total_sewingout_wip_qty,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($poly_qnty_pre,2); ?></p></td>

	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,7,'iron_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo number_format($poly_qnty_today,2); ?></a></p></td>

	                      <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,7,'iron_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($total_poly_qnty,2); ?></a></p></td>

	                     <td width="70" align="right"><p><?  echo number_format($ploy_rej_qty_today,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($total_ploy_rej_qty,2); ?></p></td>
	                     <td width="70" align="right"><p><?  echo number_format($tot_poly_wip,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($fin_qty_pre,2); ?></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,8,'packing_and_finishing_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><? echo number_format($fin_qty_today,2); ?></a></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,8,'packing_and_finishing_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($tot_fin_qnty,2); ?></a></p></td>
	                     <td width="70" align="right"><p><? echo number_format($fin_rej_qty_today,2); ?></p></td>
	                      <td width="70" align="right"><p><?  echo number_format($tot_fin_reject_qty,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($tot_fin_wip,2); ?></p></td>


	                    <?
	                    if($ex_factory_level==1)
	                    {
	                     	if($r==0)
	                     	{
		                     	?>

	                     		<td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><?  echo number_format($exf_qnty_pre,2); ?></p></td>
			                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><a href="##" onclick="javascript:alert('It is gross level production, so popup not found.');"><?  echo number_format($exf_qnty_today,2); ?></a></p></td>
			                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><a href="##" onclick="javascript:alert('It is gross level production, so popup not found.');"><? echo number_format($tot_exf_qnty,2); ?></a></p></td>

			                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><?  echo number_format($ex_fact_wip,2); ?></p></td>
		                     	<?
		                     	$r++;
		                     	// $po_wise_total_tot_exf_wip = $ex_fact_wip;
	                     	}
	                 	}
	                    else
	                    {
	                     	?>
		                    <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,'','ex_factory_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo number_format($exf_qnty_today,2); ?></a></p></td>
		                    <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,'','ex_factory_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($tot_exf_qnty,2); ?></a></p></td>

		                    <td width="70" align="right"><p><?  echo number_format($ex_fact_wip,2); ?></p></td>

	                    	<?
	                    	// $po_wise_total_tot_exf_wip = $ex_fact_wip;
		                }
		                ?>




	                    <td  align="center"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_remarks',650,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>','')">
						<?
						   if($total_delivery_cut-$po_number_data[$po_id][$color_id][$item_id]['plan_qty']>0) echo "Receive Ok";
						   else  if($total_sew_input_balance-$po_number_data[$po_id][$item_id][$color_id]['plan_qty']>0) echo "Input Ok";
						   else echo "Remarks";

						?></a></td>
	        	  </tr>
							<?
					 $job_arr[]=$po_number_data[$po_id][$item_id][$color_id]['job_no'];
					 $buyer_arr[]=$po_number_data[$po_id][$item_id][$color_id]['buyer_name'];
					 $i++;
	                } //end foreach 2nd
				}
				?>
					<tr bgcolor="#999999" style=" height:15px">
						<td width="40"></td>
						<td width="80"></td>
						<td width="60"></td>
						<td width="50"></td>
						<td width="100"></td>
						<td width="80"></td>
                        <td width="80"></td>
                        <td width="80"></td>
						<td width="80"></td>
                        <td width="80"></td>
						<td width="100"><strong> </strong></td>
                        <td width="120"><strong> </strong></td>
						<td width="100" align="right"><strong>PO Total:</strong></td>
						<td width="70" align="right"><? echo $po_wise_total_order; ?></td>
						<td width="70" align="right"><?  echo $po_wise_total_plan; ?></td>
                        <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
						<td width="70" align="right"><?  echo number_format($po_wise_total_fabric_qty,2); ?></td>
						<td width="60" align="right"><?  echo number_format($po_wise_total_fabric_pre,2); ?></td>
						<td width="60" align="right"><?  echo number_format($po_wise_fabric_today_total,2); ?></td>
						<td width="60" align="right"><?  echo number_format($po_wise_fabric_total,2);?></td>
						<td width="60" align="right"><?  echo number_format($po_wise_fabric_bal,2); ?></td>

                        <td width="70" align="right"><?  echo number_format($po_wise_possible_cut_qty,0); ?></td>
						<td width="60" align="right"><?  echo $po_wise_pre_cut; ?></td>
						<td width="60" align="right"><?  echo $po_wise_today_cut; ?></td>
						<td width="60" align="right"><?  echo $po_wise_total_cut; ?></td>
						<td width="60" align="right"></td>
						<td width="60" align="right"><?  echo $po_wise_cutting_balance; ?></td>
                        <td width="60" align="right"><?  echo number_format($po_wise_possible_cut_qty-$po_wise_total_cut,0); ?></td>
                        <td width="60" align="right"><?  echo $po_wise_priv_deliv_cut; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_deliv_cut; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_delivery_cut; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_deliv_cut_bal; ?></td>

                        <td width="60" align="right"><?  echo $po_wise_priv_print_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_print_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_print_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_priv_print_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_print_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_print_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_print_iss-$po_wise_total_print_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_priv_embl_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_embl_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_embl_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_priv_embl_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_embl_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_embl_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_embl_iss-$po_wise_total_embl_rec; ?></td>


                        <td width="60" align="right"><?  echo $po_wise_priv_sew; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_sew; ?></td>
                        <td width="60" align="right"> <? echo $po_wise_total_sew_; ?></td>
                        <td width="60" align="right"><? //echo $input_percentage; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_sew_bal; ?></td>
                        <td width="70" align="right"><? echo $po_wise_cutinhand; ?></td>
                        <td width="70" align="right"><? echo $po_wise_inhand; ?></td>
                        <td width="100" align="right"><? echo $po_wise_ready_to_sewing; ?></td>

                        <td width="100" align="right"><? //echo  $sewing_line ?></td>
                        <td width="60" align="right"><? //echo  $unit ?></td>
                        <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
                        <td width="80" align="right"><? //echo  $cutting_kg ?></td>
                        <td width="80" align="right"><? //echo  $cutting_bal ?></td>

                        <td width="70" align="right"><? echo  $po_wise_priv_sew_out; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sew_out_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sew_out_bal; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sew_out_reject_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sew_out_reject_bal; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sewingout_wip_qty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_poly_qnty_pre; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_poly_qnty_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_poly_qnty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_ploy_rej_qty_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_ploy_rej_qty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_tot_poly_wip; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_qnty_pre; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_qnty_today;?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_qnty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_rej_qty_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_rej_qty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_tot_fin_wip; ?></td>
                        <td width="70" align="right"><? echo  $exFactPoQtyArray[$po_id]['exf_qnty_pre']; ?></td>
                        <td width="70" align="right"><? echo  $exFactPoQtyArray[$po_id]['exf_qnty_today']; ?></td>
                        <td width="70" align="right"><? echo  $totEx = $exFactPoQtyArray[$po_id]['exf_qnty_pre']+$exFactPoQtyArray[$po_id]['exf_qnty_today'];; ?></td>
                        <td width="70" align="right"><? //echo $ex_fact_wip; ?></td>

						<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
				  </tr>
				<?

	 		 }

				?>
	                         <tr bgcolor="#CCCCCC" id="">
	                                <td width="40"><? // echo $i;?></td>
	                                <td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
	                                <td width="60"></td>
	                                <td width="50"></td>
	                                <td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="100"><strong></strong></td>
	                                <td width="120"><strong></strong></td>
	                                <td width="100" align="right"><strong>Job Total:</strong></td>
	                                <td width="70" align="right"><? echo $job_total_order; ?></td>
	                                <td width="70" align="right"><?  echo $job_total_plan; ?></td>
	                                <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
	                                <td width="70" align="right"><?  echo number_format($job_total_fabric_qty,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_total_fabric_pre,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_today_total,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_total,2);?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_bal,2); ?></td>
	                                <td width="70" align="right"><?  echo number_format($job_possible_cut_qty,0); ?></td>
	                                <td width="60" align="right"><?  echo $job_pre_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_cut; ?></td>
	                                <td width="60" align="right"></td>
	                                <td width="60" align="right"><?  echo $job_cutting_balance; ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_possible_cut_qty-$job_total_cut,0); ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_deliv_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_deliv_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_delivery_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_deliv_cut_bal; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_iss-$job_total_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_iss-$job_total_embl_rec; ?></td>

	                                <td width="60" align="right"><?  echo $job_priv_sew; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_sew; ?></td>
	                                <td width="60" align="right"> <? echo $job_total_sew_input; ?></td>
	                                <td width="60" align="right"><? //echo $input_percentage; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_sew__bal; ?></td>
	                                <td width="70" align="right"><? echo $job_cutinhand; ?></td>
	                                <td width="70" align="right"><? echo $job_inhand; ?></td>
	                                <td width="100" align="right"><? echo $job_ready_to_sewing; ?></td>

	                                <td width="100" align="right"><? //echo  $sewing_line ?></td>

	                                <td width="60" align="right"><? //echo  $unit ?></td>
	                                <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                                <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                                <td width="80" align="right"><? //echo  $cutting_bal ?></td>

	                                <td width="70" align="right"><? echo  $job_priv_sewout; ?></td>
	                                <td width="70" align="right"><? echo  $job_today_sewout; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sew_out_bal; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sew_out_reject_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sew_out_reject_bal; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sewingout_wip_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_poly_qnty_pre; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_poly_qnty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_poly_qnty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_ploy_rej_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_tot_poly_wip; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_qnty_pre; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_qnty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_qnty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_rej_qty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_rej_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_tot_fin_wip; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_exf_qty_pre; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_exf_qty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_exf_tot_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_tot_exf_wip; ?></td>
	                                <td width="70" align="right"><? //echo  $cutting_bal; ?></td>



	                                <td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
	                          </tr>
	                           <tr bgcolor="#999999" style=" height:15px">
										<td width="40"><? // echo $i;?></td>
										<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
										<td width="60"></td>
										<td width="50"></td>
										<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
										<td width="80"></td>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
										<td width="80"></td>
	                                    <td width="80"></td>
										<td width="100"><strong> </strong></td>
	                                    <td width="120"><strong> </strong></td>
										<td width="100" align="right"><strong>Buyer Total:</strong></td>
										<td width="70" align="right"><? echo $buyer_total_order; ?></td>
										<td width="70" align="right"><?  echo $buyer_total_plan; ?></td>
	                                    <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
										<td width="70" align="right"><?  echo number_format($buyer_total_fabric_qty,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_total_fabric_pre,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_today_total,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_total,2);?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_bal,2); ?></td>

	                                    <td width="70" align="right"><?  echo number_format($buyer_possible_cut_qty,0); ?></td>
										<td width="60" align="right"><?  echo $buyer_pre_cut; ?></td>
										<td width="60" align="right"><?  echo $buyer_today_cut; ?></td>
										<td width="60" align="right"><?  echo $buyer_total_cut; ?></td>
										<td width="60" align="right"></td>
										<td width="60" align="right"><?  echo $buyer_cutting_balance; ?></td>
	                                    <td width="60" align="right"><?  echo number_format($buyer_possible_cut_qty-$buyer_total_cut,0); ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_deliv_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_deliv_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_delivery_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_deliv_cut_bal; ?></td>

	                                    <td width="60" align="right"><?  echo $buyer_priv_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_iss-$buyer_total_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_iss-$buyer_total_embl_rec; ?></td>


	                                    <td width="60" align="right"><?  echo $buyer_priv_sew; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_sew; ?></td>
	                                    <td width="60" align="right"> <? echo $buyer_total_sew_; ?></td>
	                                    <td width="60" align="right"><? //echo $input_percentage; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_sew_bal; ?></td>
	                                    <td width="70" align="right"><? echo $buyer_cutinhand; ?></td>
	                                    <td width="70" align="right"><? echo $buyer_inhand; ?></td>
	                                    <td width="100" align="right"><? echo $buyer_ready_to_sewing; ?></td>

	                                    <td width="100" align="right"><? //echo  $sewing_line ?></td>
	                                    <td width="60" align="right"><? //echo  $unit ?></td>
	                                    <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_bal ?></td>

	                                    <td width="70" align="right"><? echo  $buyer_priv_sew_out; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_bal; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_bal; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sewingout_wip_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_poly_qnty_pre; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_poly_qnty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_poly_qnty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_tot_poly_wip; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_qnty_pre; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_qnty_today;?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_qnty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_tot_fin_wip; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_exf_qty_pre; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_exf__qty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_exf_tot_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_tot_exf_wip; ?></td>

										<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
								  </tr>
	                            <tfoot>
	                                 <tr>
	                                    <th width="40"><? // echo $i;?></th>
	                                    <th width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
	                                    <th width="60"></td>
	                                    <th width="50"></td>
	                                    <th width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
	                                    <th width="80"></th>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
	                                    <th width="80"></th>
	                                    <th width="80"></th>
	                                    <th width="100"> <strong></strong></th>
	                                    <th width="120"> <strong></strong></th>
	                                    <th width="100" align="right"><strong>Grand Total:</strong></th>
	                                    <th width="70" align="right"><? echo $grand_total_order; ?></th>
	                                    <th width="70" align="right"><?  echo $grand_total_plan; ?></th>
	                                    <th width="70" align="right"><? // echo $grand_total_plan; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_total_fabric_qty,2); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_total_fabric_pre,2); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_fabric_today_total,2); ?></th>
	                                    <th width="60" align="right"><?  echo number_format($grand_fabric_total,2);?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_fabric_bal,2); ?></th>

	                                    <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_possible_cut_qty,0); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_pre_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_cutting_balance; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_possible_cut_qty-$grand_total_cut,0); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_deliv_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_deliv_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_delivery_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_deliv_cut_bal; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_iss-$grand_total_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_iss-$grand_total_embl_rec; ?></th>


	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_sew; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_sew; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><? echo $grand_total_sew_; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><? //echo $input_percentage; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_sew_bal; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><? echo $grand_cutinhand; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><? echo $grand_inhand; ?></th>
	                                    <th width="100" align="right" style="word-break:break-all;"><? echo $grand_ready_to_sewing; ?></th>

	                                    <th width="100" align="right"><? //echo  $sewing_line ?></th>

	                                    <th width="60" align="right"><? //echo  $unit ?></th>
	                                    <th width="80" align="right"><? //echo  $cutble_pcs ?></th>
	                                    <th width="80" align="right"><? //echo  $cutting_kg ?></th>
	                                    <th width="80" align="right"><? //echo  $cutting_bal ?></th>

	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_today ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_reject_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_reject_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_reject_bal; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_wip_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_poly_qnty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_poly_qnty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_poly_qnty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_ploy_rej_qty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_ploy_rej_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_poly_wip; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_qnty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_qnty_today ;?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_qnty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_rej_qty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_rej_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_fin_wip; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_exf_qty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_exf_qty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_exf_wip; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_exf_wip; ?></th>

	                                    <th  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></th>
	                             </tr>
	  						</tfoot>

	                </table>
	           </div>
	  	</div>

	  	</fieldset>

		<?
	}
	// start Item Wise3
	elseif ($rpt_type==4) //ITEM WISE3
	{
		// ==================================== MAIN QUERY ==========================================
	 	$pro_date_sql="SELECT  a.id,a.job_no_mst,a.po_number,
 		d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,d.item_number_id as gmt_id,b.buyer_name,b.style_ref_no as style,b.job_no_prefix_num,$insert_year
 		as year ,d.color_number_id, a.file_no, a.grouping as int_ref_no ,
 		min(d.country_ship_date) as minimum_shipdate
 		from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
 		where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and  a.job_no_mst=d.job_no_mst and  a.is_deleted=0 and a.status_active=1 and
 		b.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and
 		b.status_active=1 and a.is_confirmed=1 $company_name $buyer_name $style_cond $order_cond $sql_cond $job_cond_id $country_ship_date $shipping_cond
 		group by a.id,a.job_no_mst,a.po_number,
 		d.order_quantity,d.plan_cut_qnty ,d.item_number_id,b.buyer_name,b.style_ref_no ,b.job_no_prefix_num,d.color_number_id, a.file_no, a.grouping,b.insert_date
 		order by  b.buyer_name,a.job_no_mst";
 		// echo $pro_date_sql;die();
 		$pro_date_sql_res = sql_select($pro_date_sql);
	  	$po_min_shipdate_data=array();
	  	$po_number_id = array();
	  	foreach($pro_date_sql_res as $row)
	  	{
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['id']=$row[csf('id')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no_mst')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['gmt_id']=$row[csf('gmt_id')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['po_quantity']+=$row[csf('order_qty')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['plan_qty']+=$row[csf('plan_qty')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['buyer_name']=$row[csf('buyer_name')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['style']=$row[csf('style')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['job_prifix']=$row[csf('job_no_prefix_num')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['year']=$row[csf('year')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['color_id']=$row[csf('color_number_id')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['file_no']=$row[csf('file_no')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['int_ref_no']=$row[csf('int_ref_no')];
		  $po_number_id[$row[csf('id')]]=$row[csf('id')];

		  $po_min_shipdate_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['minimum_shipdate']=$row[csf('minimum_shipdate')];
	  	}
	  	unset($pro_date_sql);
 		// echo "<pre>";
 		// print_r($po_min_shipdate_data);
 		// echo "</pre>";

 		$po_number_id=implode(",",array_unique($po_number_id));

		$po_condition="";
		$po_number_id_arr=explode(",", trim(str_replace("'","",$po_number_id)));
		if($db_type==2 && count($po_number_id_arr)>999)
		{
	  		$chunk_arr=array_chunk($po_number_id_arr, 999);
	  		foreach($chunk_arr as $keys=>$vals)
	  		{
	  			$po_ids=implode(",", $vals);
	  			if($po_condition=="")
	  			{
	  				$po_condition.=" and ( a.po_break_down_id in ($po_ids) ";
	  			}
	  			else
	  			{
	  				$po_condition.=" or a.po_break_down_id in ($po_ids) ";
	  			}
	  		}
	  		$po_condition.=" ) ";
		}
		else
		{
			$po_condition=" and a.po_break_down_id in (".str_replace("'","",$po_number_id).")";
		}

		//
		/*$date_wise_po="SELECT po_break_down_id from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date";
	 	$date_wise_po_arr=array();
	 	foreach(sql_select($date_wise_po) as $vals)
	 	{
	 		$date_wise_po_arr[$vals[csf("po_break_down_id")]]=$vals[csf("po_break_down_id")];
	 	}
	 	$date_wise_po_ids=implode(",", $date_wise_po_arr);*/
	 	// GETTING ORDER QUANTITY  ADDED IN 13/10/2018

	 	// ===================================== GETTING ORDER QTY =============================================
	 	$order_qnty_array=array();
	 	$plan_cut_qnty_array=array();
		$order_qnty_sqls="SELECT a.po_break_down_id,a.color_number_id,a.order_quantity,a.item_number_id,a.plan_cut_qnty from wo_po_color_size_breakdown a where a.status_active in(1,2,3) and a.is_deleted=0 $po_condition";
		// echo $order_qnty_sqls;
		foreach(sql_select($order_qnty_sqls) as $values)
		{
		 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]['po_quantity']+=$values[csf("order_quantity")];
		 	$plan_cut_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]['plan_cut_qnty']+=$values[csf("plan_cut_qnty")];
		}

 		// ============================== for exfactory rowspan ===========================
 		$rowspan_arr = array();
 		foreach ($po_number_data as $po_key => $po_arr)
 		{
 			foreach ($po_arr as $item_key => $item_arr)
 			{
 				foreach ($item_arr as $color_key => $color_arr)
 				{
 					$rowspan_arr[$po_key][$item_key]++;
 				}
 			}
 		}
	  	// echo "<pre>";
 		// print_r($rowspan_arr);
 		// echo "</pre>";

	   	$sew_line_arr=array();
	   	if($db_type==0)
	   	{
	   		$sql_line=sql_select("SELECT group_concat(distinct a.sewing_line) as line_id, group_concat(distinct a.floor_id) as floor_id, a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id");
	   	}
	   	if($db_type==2)
	   	{
	   		$sql_line=sql_select("SELECT listagg(cast(a.sewing_line as varchar2(2000)),',') within group (order by a.sewing_line) as line_id, listagg(cast(a.floor_id as varchar2(2000)),',') within group (order by a.floor_id) as floor_id, a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id");
	   	}

	   	foreach($sql_line as $row_sew)
	   	{
	   		$sew_line_arr[$row_sew[csf('po_break_down_id')]]['line']= implode(',',array_unique(explode(',',$row_sew[csf('line_id')])));
			$sew_line_arr[$row_sew[csf('po_break_down_id')]]['floor_id']= implode(',',array_unique(explode(',',$row_sew[csf('floor_id')])));
	   	}
	   	unset($sql_line);


	  	if($po_number_id=="") $po_number_id=0;
	  	$company_id = str_replace("'","",$cbo_company_name);
	  	$variable_sql=sql_select("SELECT ex_factory from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
		$ex_factory_level=$variable_sql[0][csf("ex_factory")];

	  	if($ex_factory_level==1) // for gross level
	  	{
	  		$ex_factory_sql="SELECT a.po_break_down_id as order_id, a.item_number_id as item_id,
		    sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date=$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS exf_qnty_today,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date=$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS ret_exf_qnty_today,
			sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date<$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS exf_qnty_pre,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date<$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS ret_exf_qnty_pre
			from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a
			where m.id=a.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and m.status_active=1 and m.is_deleted=0   $po_condition
			group by  a.po_break_down_id, a.item_number_id";
	  	}
	  	else // for color or color and size level
	  	{
	  		$ex_factory_sql="SELECT a.po_break_down_id as order_id, a.item_number_id as item_id, c.color_number_id as color_id,
		    sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END) AS exf_qnty_today,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_today,
			sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date<$txt_production_date THEN b.production_qnty ELSE 0 END) AS exf_qnty_pre,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date<$txt_production_date THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_pre
			from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
			where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0   $po_condition
			group by  a.po_break_down_id, a.item_number_id, c.color_number_id";
	  	}


		 	// echo $ex_factory_sql;die;
			$ex_factory_sql_result=sql_select($ex_factory_sql);
			$exFactPoQtyArray = array();
			foreach($ex_factory_sql_result as $row)
			{
				if($ex_factory_level==1)
				{
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]]['exf_qnty_today']=$row[csf('exf_qnty_today')]-$row[csf('ret_exf_qnty_today')];
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]]['exf_qnty_pre']=$row[csf('exf_qnty_pre')]-$row[csf('ret_exf_qnty_pre')];
				}
				else
				{
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['exf_qnty_today']=$row[csf('exf_qnty_today')]-$row[csf('ret_exf_qnty_today')];
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['exf_qnty_pre']=$row[csf('exf_qnty_pre')]-$row[csf('ret_exf_qnty_pre')];
				}
				$exFactPoQtyArray[$row[csf('order_id')]]['exf_qnty_today'] 	+= $row[csf('exf_qnty_today')]-$row[csf('ret_exf_qnty_today')];
				$exFactPoQtyArray[$row[csf('order_id')]]['exf_qnty_pre'] 	+= $row[csf('exf_qnty_pre')]-$row[csf('ret_exf_qnty_pre')];
			}


		  $production_sql="SELECT a.po_break_down_id,c.color_number_id,c.item_number_id as gmt_id,
		   sum(CASE WHEN b.production_type =1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cutting_qnty,
		   sum(CASE WHEN b.production_type =1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cutting_qnty_pre,
		   sum(CASE WHEN b.production_type =2  and a.embel_name=4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS sp_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printing_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printing_qnty_pre ,
		   sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printreceived_qnty,
		   sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printreceived_qnty_pre,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS embl_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS embl_qnty_pre ,
		   sum(CASE WHEN b.production_type =3  and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS emblreceived_qnty,
		   sum(CASE WHEN b.production_type =3  and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS emblreceived_qnty_pre,
		   min(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<=".$txt_production_date." THEN a.production_date end)
		   AS min_printin_date,
		   min(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<=".$txt_production_date." THEN a.production_date end)
		   as min_embl_date,
		   sum(CASE WHEN b.production_type =4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty,
		   sum(CASE WHEN b.production_type =4 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty_pre,
		   sum(CASE WHEN b.production_type =5 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingout_qnty_pre,
		   sum(CASE WHEN b.production_type =5 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingout_qnty_today,
		   sum(CASE WHEN b.production_type =5 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sewout_rej_qty_today,
		   sum(CASE WHEN b.production_type =5 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sewout_rej_qty_pre,

		   sum(CASE WHEN b.production_type =7 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty_pre,
		   sum(CASE WHEN b.production_type =7 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty_today,
		   sum(CASE WHEN b.production_type =7 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS ploy_rej_qty_pre,
		   sum(CASE WHEN b.production_type =7 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS ploy_rej_qty_today,

		    sum(CASE WHEN b.production_type =8 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS fin_qty_pre,
			sum(CASE WHEN b.production_type =8 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS fin_qty_today,
			sum(CASE WHEN b.production_type =8 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS fin_rej_qty_today,
			sum(CASE WHEN b.production_type =8 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS fin_rej_qty_pre,
			sum(CASE WHEN b.production_type =9 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS cut_delivery_qnty,
			sum(CASE WHEN b.production_type =9 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS cut_delivery_qnty_pre


		   from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c where a.id=b.mst_id
		   and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		   and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id $po_condition  group by a.po_break_down_id,c.color_number_id,c.item_number_id";
		  // echo "$production_sql";die;
			//and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
			$production_mst_sql=sql_select($production_sql);
			$order_wise_fin_qty_array = array();
	       foreach($production_mst_sql as $val)
		   {
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printing_qnty']=$val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['emblreceived_qnty_pre']=$val[csf('emblreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['emblreceived_qnty']=$val[csf('emblreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['embl_qnty_pre']=$val[csf('embl_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['embl_qnty']=$val[csf('embl_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['min_printin_date']=$val[csf('min_printin_date')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['min_embl_date']=$val[csf('min_embl_date')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingout_qnty_pre']=$val[csf('sewingout_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingout_qnty_today']=$val[csf('sewingout_qnty_today')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewout_rej_qty_today']=$val[csf('sewout_rej_qty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewout_rej_qty_pre']=$val[csf('sewout_rej_qty_pre')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['poly_qnty_pre']=$val[csf('poly_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['poly_qnty_today']=$val[csf('poly_qnty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['ploy_rej_qty_pre']=$val[csf('ploy_rej_qty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['ploy_rej_qty_today']=$val[csf('ploy_rej_qty_today')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_qty_pre']=$val[csf('fin_qty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_qty_today']=$val[csf('fin_qty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_rej_qty_today']=$val[csf('fin_rej_qty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_rej_qty_pre']=$val[csf('fin_rej_qty_pre')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cut_delivery_qnty']=$val[csf('cut_delivery_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cut_delivery_qnty_pre']=$val[csf('cut_delivery_qnty_pre')];

				$order_wise_fin_qty_array[$val[csf('po_break_down_id')]]['fin_qty_pre'] 	+= $val[csf('fin_qty_pre')];
				$order_wise_fin_qty_array[$val[csf('po_break_down_id')]]['fin_qty_today'] 	+= $val[csf('fin_qty_today')];

				$po_number_gmt[]=$val[csf('po_break_down_id')];
		   }

			unset($production_mst_sql);

			$sql_cutting_delevery=sql_select("select a.po_break_down_id,c.color_number_id,c.item_number_id as gmt_id
			sum(CASE WHEN  a.cut_delivery_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty,
		    sum(CASE WHEN  a.cut_delivery_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty_pre
			from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c  where a.id=b.mst_id
		    and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id   $po_condition
		    and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		    group by a.po_break_down_id,c.color_number_id,c.item_number_id");
		   //and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
			foreach( $sql_cutting_delevery as $inf)
			{
				$production_data_arr[$inf[csf('po_break_down_id')]][$inf[csf('gmt_id')]][$inf[csf('color_number_id')]]['cut_delivery_qnty']=$inf[csf('cut_delivery_qnty')];

				$production_data_arr[$inf[csf('po_break_down_id')]][$inf[csf('gmt_id')]][$inf[csf('color_number_id')]]['cut_delivery_qnty_pre']=$inf[csf('cut_delivery_qnty_pre')];
			}
			unset($sql_cutting_delevery);
			$po_condition_fab=str_replace("po_break_down_id", "po_breakdown_id", $po_condition);

	        $sql_fabric_qty=sql_select("SELECT a.po_breakdown_id,a.color_id,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
		    ELSE 0 END ) AS grey_fabric_issue,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =4  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
		    ELSE 0 END ) AS grey_fabric_issue_return,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS finish_fabric_rece,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS finish_fabric_rece_return,
			/* sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS fabric_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =2 and b.item_category=2  AND a.entry_form =18 THEN a.quantity
			ELSE 0 END ) AS fabric_qty_pre, */
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS fabric_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =1 and b.item_category=2  AND a.entry_form =37 THEN a.quantity
			ELSE 0 END ) AS fabric_qty_pre,
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END )
			AS trans_in_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_pre,
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END )
			AS trans_out_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_pre
			FROM order_wise_pro_details a,inv_transaction b
		    WHERE a.trans_id = b.id
			and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0  $po_condition_fab group by a.po_breakdown_id,a.color_id");
			//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
			$fabric_pre_qty=array();
			$fabric_today_qty=array();
			$total_fabric=array();
			$fabric_balance=array();
			$fabric_wip=array();
			foreach($sql_fabric_qty as $value)
			{
				$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
				$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];

				$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
				-$value[csf("trans_out_pre")];
				$fabric_today_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty")]+$value[csf("trans_in_qty")]
				-$value[csf("trans_out_qty")];

				$po_id_fab[]=$value[csf("po_breakdown_id")];
			}


			unset($sql_fabric_qty);


	 		//*********************************************************************************************************************

		  $color_size_qty_arr=array();
		  $po_condition2=str_replace("a.po_break_down_id", "po_break_down_id", $po_condition);
		  $po_condition3=str_replace("a.po_break_down_id", "b.po_break_down_id", $po_condition);
		  $color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty
		  from wo_po_color_size_breakdown
		  where  is_deleted=0  and  status_active=1  $po_condition2
		  group by po_break_down_id,item_number_id,size_number_id,color_number_id");
		  foreach($color_size_sql as $s_id)
		  {
			$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')];
		  }

		  unset($color_size_sql);


	  	   $sql_sewing=sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum(b.cons) AS conjumction
		   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
		   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  $po_condition3 and b.cons!=0
		   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
		   $con_per_dzn=array();
		   $po_item_qty_arr=array();
		   $color_size_conjumtion=array();
	       foreach($sql_sewing as $row_sew)
	       {
		    	$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);

				$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
				$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
	       }
		   // echo "<pre>"; print_r($color_size_conjumtion);die;
		   unset($sql_sewing);

		  foreach($color_size_conjumtion as $p_id=>$p_value)
		  {
			 foreach($p_value as $i_id=>$i_value)
			 {
				foreach($i_value as $c_id=>$c_value)
				 {
					 foreach($c_value as $s_id=>$s_value)
					 {
						 foreach($s_value as $b_id=>$b_value)
						 {
						   $order_color_size_qty=$b_value['plan_cut_qty'];
						   $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
						   $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
						   $conjunction_per= (($b_value['conjum']*$order_color_size_qty_per)/100);
						   $con_per_dzn[$p_id][$c_id]+=$conjunction_per;

						  // echo $p_id."**".$order_color_size_qty."**".$order_qty."**".$order_color_size_qty_per."**".$b_value['conjum']."<br>";
						 }
					 }
				 }
			 }
		  }
			// echo "<pre>"; print_r($con_per_dzn);die;
		 	//**********************************************************************************************************************


	     $sql_tna_date=sql_select("select a.po_number_id, a.task_start_date,a.task_finish_date	from  tna_process_mst a, lib_tna_task b
		 where b.task_name=a.task_number  and task_name=84");
		 $tna_date_arr=array();
		 foreach($sql_tna_date as $tna_val)
		 {
		 $tna_date_arr[$tna_val[csf('po_number_id')]]['tna_start']= $tna_val[csf('task_start_date')];
		 $tna_date_arr[$tna_val[csf('po_number_id')]]['tna_end']= $tna_val[csf('task_finish_date')];
		 }


		 unset($sql_tna_date);


		 $costing_per_sql=sql_select("select job_no,costing_per from wo_pre_cost_mst");
		 $costing_per_arr=array();
		 foreach($costing_per_sql as $cost_val)
		 {
			$costing_per_arr[$cost_val[csf('job_no')]]=$cost_val[csf('costing_per')];
		 }

		 unset($costing_per_sql);

		 $ready_to_sewing_sql=sql_select("select po_break_down_id, color_id, sewing_qnty as sewing_qnty from ready_to_sewing_dtls");
		 $ready_to_sewing_arr=array();
		 foreach($ready_to_sewing_sql as $row)
		 {
			$ready_to_sewing_arr[$row[csf('po_break_down_id')]][$row[csf('color_id')]]+=$row[csf('sewing_qnty')];

		 }
		 unset($ready_to_sewing_sql);
		 ob_start();
			 ?>
	         <fieldset style="width:5760px;">
	        	   <table width="1880"  cellspacing="0"   >
	                    <tr class="form_caption" style="border:none;">
	                           <td colspan="57" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report(Item Wise)</td>
	                     </tr>
	                    <tr style="border:none;">
	                            <td colspan="57" align="center" style="border:none; font-size:16px; font-weight:bold">
	                            Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
	                            </td>
	                      </tr>
	                      <tr style="border:none;">
	                            <td colspan="57" align="center" style="border:none;font-size:12px; font-weight:bold">
	                            <? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
	                            </td>
	                      </tr>
	                </table>
	             <br />
	             <table cellspacing="0"  border="1" rules="all"  width="5760" class="rpt_table">
	                <thead>
	                	<tr >
	                        <th width="40" rowspan="2">SL</th>
	                        <th width="80" rowspan="2">Buyer</th>
	                        <th width="60" rowspan="2">Job No</th>
	                        <th width="50" rowspan="2">Year</th>
	                        <th width="100" rowspan="2">Order No</th>
	                        <th width="80" rowspan="2">File No</th>
	                        <th width="80" rowspan="2">Int. Ref. No</th>
	                        <th width="80" rowspan="2">TNA Start Date</th>
	                        <th width="80" rowspan="2">TNA End Date</th>
	                        <th width="80" rowspan="2">First Shipment Date</th>
	                        <th width="100" rowspan="2">Style</th>
	                        <th width="120" rowspan="2">Item</th>
	                        <th width="100" rowspan="2">Color</th>
	                        <th width="70" rowspan="2">Order Qty.</th>
	                        <th width="70" rowspan="2">Plan Cut Qty.</th>
	                        <th width="70" rowspan="2">Fin Fab Cons/Dzn.</th>
	                        <th width="70" rowspan="2">Fabric Required Qty</th>
	                        <th width="240" colspan="4">Fabric Receive Qty.</th>
	                        <th width="70" rowspan="2">Possible Cut Qty</th>
	                        <th width="300" colspan="5">Cutting</th>
	                        <th width="60" rowspan="2"> Cutting WIP</th>
	                        <th width="240" colspan="4">Cutting Delivery To Input</th>
	                        <th width="180" colspan="3">Delivery to Print</th>
	                        <th width="180" colspan="3">Receive from Print</th>
	                        <th width="60" rowspan="2">Print WIP</th>
	                        <th width="180" colspan="3">Delivery to Emb.</th>
	                        <th width="180" colspan="3">Receive from Emb.</th>
	                        <th width="60" rowspan="2">Emb. WIP</th>

	                        <th width="300" colspan="5"> Sewing Input</th>
	                        <th width="70" rowspan="2">Cutting Inhand</th>
	                        <th width="70" rowspan="2">Input Inhand</th>
	                        <th width="100" rowspan="2">Ready To Sewing</th>
	                        <th width="100" rowspan="2">Line No</th>

	                        <th width="60" rowspan="2">Unit(Floor)</th>
	                        <th width="80" rowspan="2">Cutable Pcs as per Fabric Received</th>
	                        <th width="80" rowspan="2">Cutting KG</th>
	                        <th width="80" rowspan="2">Cutting Balance Fabric</th>

	                        <th width="210" colspan="3">Sewing Output</th>
	                        <th width="70" rowspan="2">Today Sewing Reject</th>
	                        <th width="70" rowspan="2">Sewing Reject Total</th>
	                        <th width="70" rowspan="2">Sewing WIP</th>

	                        <th width="210" colspan="3">Iron Entry</th>

	                        <th width="70" rowspan="2">Today Iron Reject</th>
	                        <th width="70" rowspan="2">Iron Reject Total</th>
	                        <th width="70" rowspan="2">Iron WIP</th>

	                        <th width="210" colspan="3">Packing & Finishing</th>

	                        <th width="70" rowspan="2">Today Finishing Reject</th>
	                        <th width="70" rowspan="2">Finishing Reject Total</th>
	                        <th width="70" rowspan="2">Pac &Fin. WIP</th>
	                        <th width="210" colspan="3">Ex-Factory</th>
	                        <th width="70" rowspan="2">Ex-Fac. WIP</th>
	                        <th rowspan="2">Remarks</th>
	                    </tr>
	                    <tr>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Bal.</th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Cutting Persent </th>
	                        <th width="60" rowspan="2" title="Plancut - Cutting Qty">Bal.</th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Bal.</th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today</th>
	                        <th width="60" rowspan="2">Total</th>
	                        <th width="60" rowspan="2">%</th>
	                        <th width="60" rowspan="2" title="Plancut - Input">Balance</th>

	                         <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>

	                        <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>

	                        <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>

	                        <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>
	                    </tr>
	                </thead>
	            </table>

	             <div style="max-height:425px; overflow-y:scroll; width:5760px;" id="scroll_body">
	                    <table  border="1" class="rpt_table"  width="5742" rules="all" id="table_body" >
	                    <?
	                      $total_cut=0;
	                      $total_print_iss=0;
						  $total_embl_iss=0;
						  $total_wash_iss=0;
						  $total_sp_iss=0;
	                      $total_print_receive=0;
						  $total_sp_rec=0;
						  $total_embl_rec=0;
						  $total_wash_receive=0;
						  $total_sp_rec=0;
	                      $total_sew_input=0;
	                      $total_sew_out=0;
						  $total_delivery_cut=0;
	                      $cutting_balance=0;
						  $print_issue_balance=0;
						  $print_rec_balance=0;
					  	  $deliv_cut_bal=0;
						  $total_sew_input_balance=0;
						  $input_percentage=0;
						  $inhand=0;
						  $buyer_total_order=0;
						  $buyer_total_plan=0;
						  $buyer_total_fabric_qty=0;
						  $buyer_total_fabric_pre=0;
						  $buyer_fabric_total=0;
						  $buyer_fabric_today_total=0;
						  $buyer_fabric_bal=0;
						  $buyer_pre_cut=0;
						  $buyer_today_cut=0;
						  $buyer_total_cut=0;
						  $buyer_cutting_balance=0;
						  $buyer_priv_print_iss=0;
						  $buyer_today_print_iss=0;
						  $buyer_print_issue_balance=0;
						  $buyer_priv_print_rec=0;
						  $buyer_today_print_rec=0;
						  $buyer_total_print_rec=0;
						  $buyer_print_rec_balance=0;
						  $buyer_priv_deliv_cut=0;
						  $buyer_today_deliv_cut=0;
						  $buyer_total_delivery_cut=0;
						  $buyer_deliv_cut_bal=0;
						  $buyer_priv_sew=0;
						  $buyer_today_sew=0;
						  $buyer_total_sew_=0;
						  $buyer_total_sew__bal=0;
						  $buyer_inhand=0;
						  $buyer_arr=array();
						  $job_arr=array();
	                      $i=1;$k=1;


		  //echo "jahid";die;

	  	foreach($po_number_data as $po_id=>$po_arr)
		{
			$po_wise_total_order=0;
		  	$po_wise_total_plan=0;
		  	$po_wise_total_fabric_qty=0;
		  	$po_wise_total_fabric_pre=0;
		  	$po_wise_fabric_total=0;
		  	$po_wise_fabric_today_total=0;
		  	$po_wise_fabric_bal=0;
		  	$po_wise_pre_cut=0;
		  	$po_wise_today_cut=0;
		  	$po_wise_total_cut=0;
		  	$po_wise_cutting_balance=0;
		  	$po_wise_priv_print_iss=0;
		  	$po_wise_today_print_iss=0;
		  	$po_wise_print_issue_balance=0;
		  	$po_wise_priv_print_rec=0;
		  	$po_wise_today_print_rec=0;
		  	$po_wise_total_print_rec=0;
		  	$po_wise_print_rec_balance=0;
		  	$po_wise_priv_deliv_cut=0;
		  	$po_wise_today_deliv_cut=0;
		  	$po_wise_total_delivery_cut=0;
		  	$po_wise_deliv_cut_bal=0;
		  	$po_wise_priv_sew=0;

			$po_wise_priv_sew_out=0;
			$po_wise_total_sew_out_bal=0;
			$po_wise_total_fin_qnty_pre=0;
			$po_wise_total_fin_qnty=0;
			$po_wise_total_poly_qnty_pre=0;
			$po_wise_total_poly_qnty=0;

		  	$po_wise_today_sew=0;
		  	$po_wise_total_sew_=0;
		  	$po_wise_total_sew__bal=0;
		  	$po_wise_inhand=0;

		foreach($po_arr as $item_id=>$item_arr)
		   {
		   	$r=0;
			 foreach($item_arr as $color_id=>$color_arr)
		     {
	 				if($i!=1)
					{
						if(!in_array($po_number_data[$po_id][$item_id][$color_id]['job_no'],$job_arr))
						{
								?>
								   <tr bgcolor="#CCCCCC" id="">
										<td width="40"><? // echo $i;?></td>
										<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
										<td width="60"></td>
										<td width="50"></td>
										<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
										<td width="80"></td>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
										<td width="80"></td>
										<td width="80"></td>
	                                    <td width="100"><strong></strong></td>
										<td width="120"><strong></strong></td>
										<td width="100" align="right"><strong>Job Total:</strong></td>
										<td width="70" align="right"><? echo $job_total_order; ?></td>
										<td width="70" align="right"><?  echo $job_total_plan; ?></td>
										<td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
										<td width="70" align="right"><?  echo number_format($job_total_fabric_qty,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_total_fabric_pre,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_today_total,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_total,2);?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_bal,2); ?></td>

										<td width="70" align="right"><?  echo number_format($job_possible_cut_qty,0); ?></td>
										<td width="60" align="right"><?  echo $job_pre_cut; ?></td>
										<td width="60" align="right"><?  echo $job_today_cut; ?></td>
										<td width="60" align="right"><?  echo $job_total_cut; ?></td>
										<td width="60" align="right"><?  //echo $job_total_cut; ?></td>
										<td width="60" align="right"><?  echo $job_cutting_balance; ?></td>
										<td width="60" align="right"><?  echo number_format($job_possible_cut_qty,0)-$job_total_cut; ?></td>
										<td width="60" align="right"><?  echo $job_priv_deliv_cut; ?></td>
										<td width="60" align="right"><?  echo $job_today_deliv_cut; ?></td>
										<td width="60" align="right"><?  echo $job_total_delivery_cut; ?></td>
										<td width="60" align="right"><?  echo $job_deliv_cut_bal; ?></td>
										<td width="60" align="right"><?  echo $job_priv_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_today_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_priv_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_today_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_iss-$job_total_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_priv_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_today_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_priv_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_today_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_iss-$job_total_embl_rec; ?></td>

										<td width="60" align="right"><?  echo $job_priv_sew; ?></td>
										<td width="60" align="right"><?  echo $job_today_sew; ?></td>
										<td width="60" align="right"> <? echo $job_total_sew_input; ?></td>
										<td width="60" align="right"><? //echo $input_percentage; ?></td>
										<td width="60" align="right"><?  echo $job_total_sew__bal; ?></td>
										<td width="70" align="right"><? echo $job_cutinhand; ?></td>
										<td width="70" align="right"><? echo $job_inhand; ?></td>
										<td width="100" align="right"><? echo $job_ready_to_sewing; ?></td>
										<td width="100" align="right"><? //echo  $sewing_line ?></td>

	                                    <td width="60" align="right"><? //echo  $unit ?></td>
	                                    <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_bal ?></td>


	                                   	 <td width="70" align="right"><? echo  $job_priv_sewout; ?></td>
	                                     <td width="70" align="right"><? echo  $job_today_sewout ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sew_out_bal; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sew_out_reject_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sew_out_reject_bal; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sewingout_wip_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_poly_qnty_pre; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_poly_qnty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_poly_qnty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_ploy_rej_qty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_ploy_rej_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_tot_poly_wip; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_qnty_pre; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_qnty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_qnty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_rej_qty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_rej_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_tot_fin_wip; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_exf_qty_pre; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_exf_qty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_exf_tot_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_tot_exf_wip; ?></td>


										<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
								  </tr>
								<?
								  $job_inhand=0;
								  $job_cutinhand=0;
								  $job_possible_cut_qty=0;
								  $job_total_order=0;
								  $job_total_plan=0;
								  $job_total_fabric_qty=0;
								  $job_total_fabric_pre=0;
								  $job_fabric_total=0;
								  $job_fabric_today_total=0;
								  $job_fabric_bal=0;
								  $job_pre_cut=0;
								  $job_today_cut=0;
								  $job_total_cut=0;
								  $job_cutting_balance=0;
								  $job_priv_print_iss=0;
								  $job_today_print_iss=0;
								  $job_total_print_iss=0;
								  $job_print_issue_balance=0;
								  $job_priv_print_rec=0;
								  $job_today_print_rec=0;
								  $job_total_print_rec=0;
								  $job_print_rec_balance=0;
								  $job_priv_deliv_cut=0;
								  $job_today_deliv_cut=0;
								  $job_total_delivery_cut=0;
								  $job_deliv_cut_bal=0;
								  $job_priv_sew=0;
								  $job_today_sew=0;
								  $job_total_sew_input=$job_today_sewout=$job_total_sew_out_bal=0;
								  $job_total_sew_out_reject_today=$job_total_sew_out_reject_bal=0;$job_total_sewingout_wip_qty=0;
								  $job_total_poly_qnty_pre=$job_total_poly_qnty_today=$job_total_poly_qnty=$job_total_ploy_rej_qty_pre=$job_total_ploy_rej_qty_pre=$job_total_ploy_rej_qty_today=$job_total_ploy_rej_qty=$job_total_tot_poly_wip=0;
								  $job_total_fin_qnty_pre=$job_total_fin_qnty_today=$job_total_fin_qnty=$job_total_fin_rej_qty_pre=$job_total_fin_rej_qty_today=$job_total_fin_rej_qty=$job_total_tot_fin_wip=0;
								  $job_total_exf_qty_pre=$job_total_exf_qty_today=$job_total_exf_tot_qty=$job_total_tot_exf_wip=0;
								  $job_total_sew__bal=0;
								  $job_priv_print_iss=0;
								  $job_today_print_iss=0;
								  $job_total_print_iss=0;
								  $job_priv_embl_iss=0;
								  $job_priv_print_rec=0;
								  $job_today_embl_iss=0;
								  $job_total_embl_iss=0;
								  $job_today_wash_iss=0;
								  $job_priv_wash_iss=0;
								  $job_today_sp_iss=0;
								  $job_total_wash_iss=0;
								  $job_total_sp_iss=0;
								  $job_priv_sp_iss=0;
								  $job_priv_print_rec=0;
								  $job_today_print_rec=0;
								  $job_total_print_rec=0;
								  $job_priv_wash_rec=0;
								  $job_today_wash_rec=0;
								  $job_total_wash_rec=0;
								  $job_priv_embl_rec=0;
								  $job_today_embl_rec=0;
								  $job_total_embl_rec=0;
								  $job_priv_sp_rec=0;
								  $job_today_sp_rec=0;
								  $job_total_sp_rec=0;
								  $job_ready_to_sewing=$job_priv_sewout=0;
						  }
					}


					if($i!=1)
					{
					 	if( !in_array($po_number_data[$po_id][$item_id][$color_id]['buyer_name'],$buyer_arr))
						{

							?>
							<tr bgcolor="#999999" style="height:15px">
							<td width="40"><? // echo $i;?></td>
							<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
							<td width="60"></td>
							<td width="50"></td>
							<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
							<td width="80"></td>
	                        <td width="80"></td>
	                        <td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="100"><strong> </strong></td>
	                        <td width="120"><strong> </strong></td>
							<td width="100" align="right"><strong>Buyer Total:</strong></td>
							<td width="70" align="right"><? echo $buyer_total_order; ?></td>
							<td width="70" align="right"><?  echo $buyer_total_plan; ?></td>
							<td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
							<td width="70" align="right"><?  echo number_format($buyer_total_fabric_qty,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_total_fabric_pre,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_today_total,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_total,2);?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_bal,2); ?></td>

							<td width="70" align="right"><?  echo number_format($buyer_possible_cut_qty,0); ?></td>
							<td width="60" align="right"><?  echo $buyer_pre_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_cut; ?></td>
							<td width="60" align="right"><?  //echo $buyer_total_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_cutting_balance; ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_possible_cut_qty,0)-$buyer_total_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_deliv_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_deliv_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_delivery_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_deliv_cut_bal; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_print_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_print_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_print_rec; ?></td>
						   <td width="60" align="right"><?   echo $buyer_total_print_iss-$buyer_total_print_rec; ?></td>

							<td width="60" align="right"><?  echo $buyer_priv_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_iss-$buyer_total_embl_rec; ?></td>


							<td width="60" align="right"><?  echo $buyer_priv_sew; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_sew; ?></td>
							<td width="60" align="right"> <? echo $buyer_total_sew_; ?></td>
							<td width="60" align="right"><? //echo $input_percentage; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_sew_bal; ?></td>
							<td width="70" align="right"><? echo $buyer_cutinhand; ?></td>
							<td width="70" align="right"><? echo $buyer_inhand; ?></td>
							<td width="100" align="right"><? echo $buyer_ready_to_sewing; ?></td>

							<td width="100" align="right"><? //echo  $sewing_line ?></td>
	                        <td width="60" align="right"><? //echo  $unit ?></td>
	                        <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                        <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                        <td width="80" align="right"><? //echo  $cutting_bal ?></td>

	                        <td width="70" align="right"><? echo  $buyer_priv_sew_out ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_today ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_bal; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_bal; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sewingout_wip_qty ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_poly_qnty_pre; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_poly_qnty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_poly_qnty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_tot_poly_wip; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_qnty_pre; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_qnty_today;?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_qnty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_tot_fin_wip; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_exf_qty_pre; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_exf__qty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_exf_tot_qty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_tot_exf_wip ;?></td>


							<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
					  	</tr>
								<?
								  $buyer_cutinhand=$buyer_ready_to_sewing=0;
								  $buyer_possible_cut_qty=0;
								  $buyer_total_order=0;
								  $buyer_total_plan=0;
								  $buyer_total_fabric_qty=0;
								  $buyer_total_fabric_pre=0;
								  $buyer_fabric_total=0;
								  $buyer_fabric_today_total=0;
								  $buyer_fabric_bal=0;
								  $buyer_pre_cut=0;
								  $buyer_today_cut=0;
								  $buyer_total_cut=0;
								  $buyer_cutting_balance=0;
								  $buyer_priv_print_iss=0;
								  $buyer_today_print_iss=0;
								  $buyer_total_print_iss=0;
								  $buyer_print_issue_balance=0;
								  $buyer_priv_print_rec=0;
								  $buyer_today_print_rec=0;
								  $buyer_total_print_rec=0;
								  $buyer_print_rec_balance=0;
								  $buyer_priv_deliv_cut=0;
								  $buyer_today_deliv_cut=0;
								  $buyer_total_delivery_cut=0;
								  $buyer_deliv_cut_bal=0;
								  $buyer_priv_sew=0;
								  $buyer_today_sew=$buyer_priv_sew_out=0;
								  $buyer_total_sew_=$buyer_total_sew_out_today=0;
								  $buyer_total_sew_out_reject_today=$buyer_total_sew_out_reject_bal=$buyer_total_sewingout_wip_qty=0;
								  $buyer_total_poly_qnty_pre=$buyer_total_poly_qnty_today=$buyer_total_poly_qnty=$buyer_total_ploy_rej_qty_pre=$buyer_total_ploy_rej_qty_today=$buyer_total_ploy_rej_qty=$buyer_total_tot_poly_wip=0;
								  $buyer_total_fin_qnty_pre=$buyer_total_fin_qnty_today=$buyer_total_fin_qnty=$buyer_total_fin_rej_qty_pre=$buyer_total_fin_rej_qty_today=$buyer_total_fin_rej_qty=$buyer_total_tot_fin_wip=0;
								  $buyer_total_exf_qty_pre=$buyer_total_exf__qty_today=$buyer_total_exf_tot_qty=$buyer_total_tot_exf_wip=0;
								  $buyer_total_sew__bal=0;
								  $buyer_inhand=0;
								  $buyer_priv_print_iss=0;
								  $buyer_today_print_iss=0;
								  $buyer_total_print_iss=0;
								  $buyer_priv_embl_iss=0;
								  $buyer_priv_print_rec=0;
								  $buyer_today_embl_iss=0;
								  $buyer_total_embl_iss=0;
								  $buyer_today_wash_iss=0;
								  $buyer_priv_wash_iss=0;
								  $buyer_today_sp_iss=0;
								  $buyer_total_wash_iss=0;
								  $buyer_total_sp_iss=0;
								  $buyer_priv_sp_iss=0;
								  $buyer_priv_print_rec=0;
								  $buyer_today_print_rec=0;
								  $buyer_total_print_rec=0;
								  $buyer_priv_wash_rec=0;
								  $buyer_today_wash_rec=0;
								  $buyer_total_wash_rec=0;
								  $buyer_priv_embl_rec=0;
								  $buyer_today_embl_rec=0;
								  $buyer_total_embl_rec=0;
								  $buyer_priv_sp_rec=0;
								  $buyer_today_sp_rec=0;
								  $buyer_total_sp_rec=0;
							}
					  }
					//***********************for line****************************************************************************************
					$line_id_all=$sew_line_arr[$po_id]['line'];
					$line_name="";
					foreach(array_unique(explode(",",$line_id_all)) as $l_id)
					{
						if($line_name!="") $line_name.=",";
						if($prod_reso_allo==1)
						{
						$line_name.= $lineArr[$prod_reso_arr[$l_id]];
						}
						else
						{
						$line_name.= $lineArr[$l_id];
						}
					}

					$floor_id_all=$sew_line_arr[$po_id]['floor_id'];
					$floor_name="";
					foreach(array_unique(explode(",",$floor_id_all)) as $f_id)
					{
						if($floor_name!="") $floor_name.=",";
						$floor_name.= $floor_arr[$f_id];
					}

					$costing_per=$costing_per_arr[$po_number_data[$po_id][$item_id][$color_id]['job_no']];
					if($costing_per==1)
					{
						$costing_per_qty=12;
					}
					else if($costing_per==2)
					{
						$costing_per_qty=1;
					}
					else if($costing_per==3)
					{
						$costing_per_qty=24;
					}
					else if($costing_per==4)
					{
						$costing_per_qty=36;
					}
					else if($costing_per==5)
					{
						$costing_per_qty=48;
					}

					//$fabric_wip=$fabric_wip[$po_id][$color_id]['issue']-$fabric_wip[$po_id][$color_id]['receive'];
					$ready_to_sewing=$ready_to_sewing_arr[$po_id][$item_id][$color_id];
				    $fabric_pre=$fabric_pre_qty[$po_id][$color_id];
					$fabric_today=$fabric_today_qty[$po_id][$color_id];
					$total_fabric=$fabric_pre+$fabric_today;
					$possible_cut_qty=($total_fabric/($con_per_dzn[$po_id][$color_id]/$costing_per_qty));
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					// $con = $con_per_dzn[$po_id][$color_id]*12/$costing_per_qty;
					$fabric_qty=($plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty']*$con_per_dzn[$po_id][$color_id])/$costing_per_qty;

					// echo $plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'].'=='.$con_per_dzn[$po_id][$color_id].'=='.$costing_per_qty.'<br>';
				    $fabric_balance=$fabric_qty-$total_fabric;
					$total_cut=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$cutting_balance=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'] - $total_cut;
					// echo $po_number_data[$po_id][$item_id][$color_id]['plan_qty'].'-'.$total_cut;die('go to hell');
					$total_print_iss=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre']+$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$total_embl_iss=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre']+$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$total_embl_rec=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre']+$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$total_print_receive=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];

					//echo $inhand."***";die;
					$print_balance=$total_print_iss-$total_print_receive;
					$embl_balance=$total_embl_iss-$total_embl_rec;
					$wash_balance=$total_wash_iss-$total_wash_receive;
					$sp_balance=$total_sp_iss-$total_sp_rec;
					$total_emblishment_iss=$total_embl_iss+$total_wash_iss+$total_print_iss+$total_sp_iss;
					//$total_emblishment_rec=$total_wash_receive+$total_embl_rec+$total_sp_rec+$total_print_rec;
					$print_issue_balance=$total_emblishment_iss-$total_cut;
					$print_rec_balance=$total_emblishment_rec-$total_emblishment_iss;
					$total_delivery_cut=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$deliv_cut_bal=($total_cut)-$total_delivery_cut;
					$total_sew_input=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$total_sew_input_balance=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'] - $total_sew_input;
					$input_percentage=($total_sew_input/$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'])*100;
					// $input_percentage plan cut qty change to po qty by saeed vie;
					$sewingout_qnty_pre=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_pre'];
					$sewingout_qnty_today=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_today'];

					$sewout_rej_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['sewout_rej_qty_today'];
					$sewout_rej_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['sewout_rej_qty_pre'];

					$fin_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['fin_qty_pre'];
					$fin_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['fin_qty_today'];
					$fin_rej_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['fin_rej_qty_pre'];
					$fin_rej_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['fin_rej_qty_today'];

					$poly_qnty_pre=$production_data_arr[$po_id][$item_id][$color_id]['poly_qnty_pre'];
					$poly_qnty_today=$production_data_arr[$po_id][$item_id][$color_id]['poly_qnty_today'];
					$ploy_rej_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['ploy_rej_qty_pre'];
					$ploy_rej_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['ploy_rej_qty_today'];

					$total_poly_qnty=$poly_qnty_pre+$poly_qnty_today;
					$total_ploy_rej_qty=$ploy_rej_qty_pre+$ploy_rej_qty_today;


					$total_sewingout_qnty_bal=$sewingout_qnty_pre+$sewingout_qnty_today;
					$total_sewingout_reject_qty_bal=$sewout_rej_qty_today+$sewout_rej_qty_pre;
					$total_sewingout_wip_qty=$total_sewingout_qnty_bal-$total_sew_input;
					$tot_poly_wip=(($total_poly_qnty+$total_ploy_rej_qty)-$total_sewingout_qnty_bal);

					$fin_qty_pre=$fin_qty_pre;
					$fin_qty_today=$fin_qty_today;
					$fin_rej_qty_today=$fin_rej_qty_today;
					$fin_rej_qty_pre=$fin_rej_qty_pre;

					$tot_fin_qnty=$fin_qty_pre+$fin_qty_today;
					$tot_fin_reject_qty=$fin_rej_qty_today+$fin_rej_qty_pre;
					$tot_fin_wip=(($tot_fin_qnty+$tot_fin_reject_qty)-$total_poly_qnty);
					if($ex_factory_level==1) // when gross level
					{
						$exf_qnty_today=$production_data[$po_id][$item_id]['exf_qnty_today'];
						$exf_qnty_pre=$production_data[$po_id][$item_id]['exf_qnty_pre'];
					}
					else
					{
						$exf_qnty_today=$production_data[$po_id][$item_id][$color_id]['exf_qnty_today'];
						$exf_qnty_pre=$production_data[$po_id][$item_id][$color_id]['exf_qnty_pre'];
					}

					//echo $exf_qnty_today.'='.$exf_qnty_pre;
					$tot_exf_qnty=$exf_qnty_today+$exf_qnty_pre;
					$ex_fact_wip=($tot_exf_qnty-$tot_fin_qnty);



					$cutting_inhand=0;
					$cutting_inhand=$total_cut-$total_sew_input;
					$inhand=0;
					if($total_print_iss!=0 && $total_embl_iss!=0)
					{
						if(date("Y-m-d",strtotime($production_data_arr[$po_id][$item_id][$color_id]['min_printin_date']))<=date("Y-m-d",strtotime($production_data_arr[$po_id][$item_id][$color_id]['min_embl_date'])))
					    {
				        $inhand=($total_delivery_cut+$total_embl_rec)-($total_sew_input+$total_print_iss);
						}
						else
						{
						$inhand=($total_delivery_cut+$total_embl_iss)-($total_sew_input+$total_print_receive);
						}
					}
					else if ($total_print_iss!=0) { $inhand=(($total_delivery_cut+$total_print_receive)-($total_print_iss+$total_sew_input));}
					else if ($total_embl_iss!=0) { $inhand=($total_delivery_cut+$total_embl_rec)-($total_embl_iss+$total_sew_input);}
					else if($total_delivery_cut!=0)$inhand=$total_delivery_cut-$total_sew_input;
					else $inhand=0;  // $inhand=$total_cut-$total_sew_input;
					//if($inhand<0) $inhand=0;


					//for job total *******************************************************************************************************
					$job_possible_cut_qty+=$possible_cut_qty;
					$job_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$job_total_plan+=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];
					$job_total_fabric_qty+=$fabric_qty;
					$job_total_fabric_pre+=$fabric_pre;
					$job_fabric_today_total+=$fabric_today;
					$job_fabric_total+=$total_fabric;
					$job_fabric_bal+=$fabric_balance;
					$job_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$job_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$job_total_cut+=$total_cut;
					$job_cutting_balance+=$cutting_balance;
					$job_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$job_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$job_total_print_iss+=$total_print_iss;
					$job_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$job_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$job_total_embl_iss+=$total_embl_iss;
					$job_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$job_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$job_total_wash_iss+=$total_wash_iss;
					$job_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$job_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$job_total_sp_iss+=$total_sp_iss;
					$job_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$job_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$job_total_print_rec+=$total_print_receive;
					$job_print_issue_balance=$print_issue_balance;
					$job_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$job_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$job_total_wash_rec+=$total_wash_receive;
					$job_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$job_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$job_total_embl_rec+=$total_embl_rec;
					$job_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$job_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$job_total_sp_rec+=$total_sp_rec;
					$job_print_rec_balance+=$print_rec_balance;
					$job_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$job_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$job_total_delivery_cut+=$total_delivery_cut;
					$job_deliv_cut_bal+=$deliv_cut_bal;
					$job_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$job_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$job_priv_sewout+=$sewingout_qnty_pre;
					$job_today_sewout+=$sewingout_qnty_today;
					$job_priv_sew_input+=$total_sew_input;
					$job_total_sew_bal+=$total_sew_input_balance;
					$job_total_sew_out_bal+=$total_sewingout_qnty_bal;
					$job_total_sew_out_reject_bal+=$total_sewingout_reject_qty_bal;
					$job_total_sew_out_reject_today+=$sewout_rej_qty_today;
					$job_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$job_total_poly_qnty_pre+=$poly_qnty_pre;
					$job_total_poly_qnty_today+=$poly_qnty_today;
					$job_total_poly_qnty+=$total_poly_qnty;

					$job_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;
					$job_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$job_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$job_total_tot_poly_wip+=$tot_poly_wip;
					//Fin and Pack
					$job_total_fin_qnty_pre+=$fin_qty_pre;
					$job_total_fin_qnty_today+=$fin_qty_today;
					$job_total_fin_qnty+=$tot_fin_qnty;

					$job_total_fin_rej_qty_pre+=$fin_rej_qty_pre;
					$job_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$job_total_fin_rej_qty+=$tot_fin_reject_qty;
					$job_total_tot_fin_wip+=$tot_fin_wip;

					$job_total_exf_qty_pre+=$exf_qnty_pre;
					$job_total_exf_qty_today+=$exf_qnty_today;
					$job_total_exf_tot_qty+=$tot_exf_qnty;
					$job_total_tot_exf_wip+=$ex_fact_wip;


					$job_inhand+=$inhand;
					$job_cutinhand+=$cutting_inhand;
					$job_ready_to_sewing+=$ready_to_sewing;
					//buyer sub total **************************************************************************************************
					$buyer_possible_cut_qty+=$possible_cut_qty;
					$buyer_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$buyer_total_plan+=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];
					$buyer_total_fabric_qty+=$fabric_qty;
					$buyer_total_fabric_pre+=$fabric_pre;
					$buyer_fabric_today_total+=$fabric_today;
					$buyer_fabric_total+=$total_fabric;
					$buyer_fabric_bal+=$fabric_balance;
					$buyer_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$buyer_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$buyer_total_cut+=$total_cut;
					$buyer_cutting_balance+=$cutting_balance;
					$buyer_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$buyer_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$buyer_total_print_iss+=$total_print_iss;
					$buyer_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$buyer_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$buyer_total_embl_iss+=$total_embl_iss;
					$buyer_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$buyer_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$buyer_total_wash_iss+=$total_wash_iss;
					$buyer_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$buyer_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$buyer_total_sp_iss+=$total_sp_iss;
					$buyer_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$buyer_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$buyer_total_print_rec+=$total_print_receive;
					$buyer_print_issue_balance=$print_issue_balance;
					$buyer_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$buyer_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$buyer_total_wash_rec+=$total_wash_receive;
					$buyer_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$buyer_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$buyer_total_embl_rec+=$total_embl_rec;
					$buyer_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$buyer_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$buyer_total_sp_rec+=$total_sp_rec;
					$buyer_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$buyer_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$buyer_total_delivery_cut+=$total_delivery_cut;
					$buyer_deliv_cut_bal+=$deliv_cut_bal;
					$buyer_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$buyer_priv_sew_out+=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_pre'];
					$buyer_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$buyer_total_sew_+=$total_sew_input;
					$buyer_total_sew_bal+=$total_sew_input_balance;
					$buyer_total_sew_out_pre+=$sewingout_qnty_pre;
					$buyer_total_sew_out_today+=$sewingout_qnty_today;
					$buyer_total_sew_out_bal+=$total_sewingout_qnty_bal;
					$buyer_total_sew_out_reject_today+=$sewout_rej_qty_today;
					$buyer_total_sew_out_reject_bal+=$total_sewingout_reject_qty_bal;
					$buyer_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$buyer_total_poly_qnty_pre+=$poly_qnty_pre;
					$buyer_total_poly_qnty_today+=$poly_qnty_today;
					$buyer_total_poly_qnty+=$total_poly_qnty;
					$buyer_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;

					$buyer_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$buyer_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$buyer_total_tot_poly_wip+=$tot_poly_wip;

					// Fin & Pack
					$buyer_total_fin_qnty_pre+=$fin_qty_pre;
					$buyer_total_fin_qnty_today+=$fin_qty_today;
					$buyer_total_fin_qnty+=$tot_fin_qnty;
					$buyer_total_fin_rej_qty_pre+=$fin_rej_qty_pre;

					$buyer_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$buyer_total_fin_rej_qty+=$tot_fin_reject_qty;
					$buyer_total_tot_fin_wip+=$tot_fin_wip;

					$buyer_total_exf_qty_pre+=$exf_qnty_pre;
					$buyer_total_exf__qty_today+=$exf_qnty_today;
					$buyer_total_exf_tot_qty+=$tot_exf_qnty;
					$buyer_total_tot_exf_wip+=$ex_fact_wip;

					$buyer_inhand+=$inhand;
					$buyer_cutinhand+=$cutting_inhand;
					$buyer_ready_to_sewing+=$ready_to_sewing;

					//buyer sub total **************************************************************************************************
					$po_wise_possible_cut_qty+=$possible_cut_qty;
					$po_wise_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$po_wise_total_plan+=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];
					$po_wise_total_fabric_qty+=$fabric_qty;
					$po_wise_total_fabric_pre+=$fabric_pre;
					$po_wise_fabric_today_total+=$fabric_today;
					$po_wise_fabric_total+=$total_fabric;
					$po_wise_fabric_bal+=$fabric_balance;
					$po_wise_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$po_wise_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$po_wise_total_cut+=$total_cut;
					$po_wise_cutting_balance+=$cutting_balance;
					$po_wise_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$po_wise_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$po_wise_total_print_iss+=$total_print_iss;
					$po_wise_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$po_wise_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$po_wise_total_embl_iss+=$total_embl_iss;
					$po_wise_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$po_wise_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$po_wise_total_wash_iss+=$total_wash_iss;
					$po_wise_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$po_wise_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$po_wise_total_sp_iss+=$total_sp_iss;
					$po_wise_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$po_wise_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$po_wise_total_print_rec+=$total_print_receive;
					$po_wise_print_issue_balance=$print_issue_balance;
					$po_wise_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$po_wise_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$po_wise_total_wash_rec+=$total_wash_receive;
					$po_wise_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$po_wise_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$po_wise_total_embl_rec+=$total_embl_rec;
					$po_wise_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$po_wise_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$po_wise_total_sp_rec+=$total_sp_rec;
					$po_wise_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$po_wise_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$po_wise_total_delivery_cut+=$total_delivery_cut;
					$po_wise_deliv_cut_bal+=$deliv_cut_bal;
					$po_wise_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$po_wise_priv_sew_out+=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_pre'];
					$po_wise_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$po_wise_total_sew_+=$total_sew_input;
					$po_wise_total_sew_bal+=$total_sew_input_balance;
					$po_wise_total_sew_out_pre+=$sewingout_qnty_pre;
					$po_wise_total_sew_out_today+=$sewingout_qnty_today;
					$po_wise_total_sew_out_bal+=$total_sewingout_qnty_bal;
					$po_wise_total_sew_out_reject_today+=$sewout_rej_qty_today;
					$po_wise_total_sew_out_reject_bal+=$total_sewingout_reject_qty_bal;
					$po_wise_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$po_wise_total_poly_qnty_pre+=$poly_qnty_pre;
					$po_wise_total_poly_qnty_today+=$poly_qnty_today;
					$po_wise_total_poly_qnty+=$total_poly_qnty;
					$po_wise_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;

					$po_wise_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$po_wise_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$po_wise_total_tot_poly_wip+=$tot_poly_wip;

					// Fin & Pack
					$po_wise_total_fin_qnty_pre+=$fin_qty_pre;
					$po_wise_total_fin_qnty_today+=$fin_qty_today;
					$po_wise_total_fin_qnty+=$tot_fin_qnty;
					$po_wise_total_fin_rej_qty_pre+=$fin_rej_qty_pre;

					$po_wise_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$po_wise_total_fin_rej_qty+=$tot_fin_reject_qty;
					$po_wise_total_tot_fin_wip+=$tot_fin_wip;

					$po_wise_total_exf_qty_pre+=$exf_qnty_pre;
					$po_wise_total_exf__qty_today+=$exf_qnty_today;
					$po_wise_total_exf_tot_qty+=$tot_exf_qnty;
					$po_wise_total_tot_exf_wip+=$ex_fact_wip;

					$po_wise_inhand+=$inhand;
					$po_wise_cutinhand+=$cutting_inhand;
					$po_wise_ready_to_sewing+=$ready_to_sewing;

					// for grand total ********************************************************************************************************************
					$grand_possible_cut_qty+=$possible_cut_qty;
					$grand_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$grand_total_plan+=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];
					$grand_total_fabric_qty+=$fabric_qty;
					$grand_total_fabric_pre+=$fabric_pre;
					$grand_fabric_today_total+=$fabric_today;
					$grand_fabric_total+=$total_fabric;
					$grand_fabric_bal+=$fabric_balance;
					$grand_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$grand_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$grand_total_cut+=$total_cut;
					$grand_cutting_balance+=$cutting_balance;
					$grand_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$grand_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$grand_total_print_iss+=$total_print_iss;
					$grand_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$grand_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$grand_total_embl_iss+=$total_embl_iss;
					$grand_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$grand_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$grand_total_wash_iss+=$total_wash_iss;
					$grand_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$grand_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$grand_total_sp_iss+=$total_sp_iss;
					$grand_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$grand_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$grand_total_print_rec+=$total_print_receive;
					$grand_print_issue_balance=$print_issue_balance;
					$grand_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$grand_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$grand_total_wash_rec+=$total_wash_receive;
					$grand_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$grand_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$grand_total_embl_rec+=$total_embl_rec;
					$grand_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$grand_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$grand_total_sp_rec+=$total_sp_rec;
					$grand_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$grand_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$grand_total_delivery_cut+=$total_delivery_cut;
					$grand_deliv_cut_bal+=$deliv_cut_bal;
					$grand_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$grand_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$grand_total_sew_+=$total_sew_input;
					$grand_total_sewingout_qnty_pre+=$sewingout_qnty_pre;
					$grand_total_sewingout_qnty_today+=$sewingout_qnty_today;
					$grand_total_sewingout_qnty_bal+=$total_sewingout_qnty_bal;
					$grand_total_sewingout_qnty_reject_today+=$sewout_rej_qty_today;
					$grand_total_sewingout_qnty_reject_bal+=$total_sewingout_reject_qty_bal;
					$grand_total_sewingout_qnty_reject_today+=$sewout_rej_qty_today;
					$grand_total_sew_bal+=$total_sew_input_balance;
					$grand_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$grand_total_poly_qnty_pre+=$poly_qnty_pre;
					$grand_total_poly_qnty_today+=$poly_qnty_today;
					$grand_total_poly_qnty+=$total_poly_qnty;
					$grand_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;
					$grand_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$grand_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$grand_total_tot_poly_wip+=$tot_poly_wip;

					//Fin & Pack

					$grand_total_fin_qnty_pre+=$fin_qty_pre;
					$grand_total_fin_qnty_today+=$fin_qty_today;
					$grand_total_fin_qnty+=$tot_fin_qnty;
					$grand_total_fin_rej_qty_pre+=$fin_rej_qty_pre;
					$grand_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$grand_total_fin_rej_qty+=$tot_fin_reject_qty;
					$grand_total_tot_fin_wip+=$tot_fin_wip;

					$grand_total_exf_qty_pre+=$exf_qnty_pre;
					$grand_total_exf_qty_today+=$exf_qnty_today;
					$grand_total_exf_qty+=$tot_exf_qnty;
					$grand_total_tot_exf_wip+=$ex_fact_wip;

					$grand_inhand+=$inhand;
					$grand_cutinhand+=$cutting_inhand;
					$grand_ready_to_sewing+=$ready_to_sewing;

	                    ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
	                    <td width="40"><? echo $i; ?></td>
	                    <td width="80" style="word-break:break-all;"><p><? echo $buyer_short_library[$po_number_data[$po_id][$item_id][$color_id]['buyer_name']]; ?></p></td>
	                    <td width="60" align="center"><? echo $po_number_data[$po_id][$item_id][$color_id]['job_prifix'];?></td>
	                    <td width="50" align="right"><? echo $po_number_data[$po_id][$item_id][$color_id]['year'];?></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['po_number'];?></p></td>
	                    <td width="80" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['file_no'];?></p></td>
	                    <td width="80" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['int_ref_no'];?></p></td>
	                    <td width="80" align="center"><?  echo  change_date_format($tna_date_arr[$po_id]['tna_start']);  ?></td>
	                    <td width="80" align="center"><?  echo  change_date_format($tna_date_arr[$po_id]['tna_end']);  ?></td>
	                    <td width="80" align="center"><?  echo  change_date_format($po_min_shipdate_data[$po_id][$item_id][$color_id]['minimum_shipdate']);  ?></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['style']; ?></p></td>
	                    <td width="120" align="center" title="Item Id=<? echo $item_id;?>" style="word-break:break-all;"><p><? echo $garments_item[$item_id]; ?></p></td>
	                    <td width="100" align="center" title="color_id=<? echo $color_id;?>" style="word-break:break-all;"><p><? echo $colorname_arr[$po_number_data[$po_id][$item_id][$color_id]['color_id']]; ?></p></td>
	                    <td width="70" align="right"><?  echo $order_qnty=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity']; ?></td>
	                    <td width="70" align="right"><?  echo $plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty']; ?></td>
	                    <td width="70" align="right"><?  echo number_format(($con_per_dzn[$po_id][$color_id]*12/$costing_per_qty),3); ?></td>
	                    <td width="70" align="right"  title="consumption per pcs * Plan Cut Qty"><?  echo number_format($fabric_qty,2); ?></td>
	                    <td width="60" align="right"><?  echo number_format($fabric_pre,2); ?></td>
	                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,1)"><?  echo number_format($fabric_today,2); ?></a></td>
	                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,2)"><?  echo number_format($total_fabric,2); ?></a><?  //echo number_format($total_fabric,2);?></td>
	                    <td width="60" align="right"><?  echo number_format($fabric_balance,2); ?></td>

	                    <td width="70" align="right" title="Possible Cut Qty. =Total Fabric Receive/ Consumption Per Pcs."><?  echo  number_format($possible_cut_qty,0); ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre']; //$production_data_arr[$row_fab[csf('po_breakdown_id')]][$row_fab[csf('color_id')]]['cutting_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',950,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"> <?  echo $production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',950,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_cut; ?></a></td>
	                    <td width="60" align="right" title="(Total Cut - Order qty)*100/Total Cut"><? $cut_persent=(($total_cut - $order_qnty)*100) / $total_cut; echo number_format($cut_persent,2); ?></td>
	                    <td width="60" align="right"><?  echo $cutting_balance; ?></td>
	                    <td width="60" align="right" title="Cutting WIP=Possible Cut Qty - Total Cutting Qty"><?  echo number_format($possible_cut_qty-$total_cut,0); ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_delivery_cut; ?></a></td>
	                    <td width="60" align="right"><?  echo $deliv_cut_bal; ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printing_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_print_iss; ?></a></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$color_id]['printreceived_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_print_receive; ?></a></td>
	                    <td width="60" align="right" title="Print WIP=Delivery to Print-Receive from Print"><?  echo $print_balance; ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$color_id]['embl_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['embl_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_embl_iss; ?></a></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_embl_rec; ?></a></td>
	                    <td width="60" align="right" title="Embl. WIP=Delivery to Embl.-Receive from Embl."><?  echo $embl_balance; ?></td>

	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"> <? echo $total_sew_input; ?></a></td>
	                    <td width="60" align="right" title="(Total Sewing Input*100)/Order Qty"><?  echo number_format($input_percentage,2); ?></td>
	                    <td width="60" align="right"><?  echo $total_sew_input_balance; ?></td>
	                    <td width="70" align="right" title="Inhand=Total Cut - total sewing input" ><?  echo $cutting_inhand; ?></td>
	                     <td width="70" align="right" title="Inhand= Cutting Delivery To Input  - Delivery to  Embellishment &#13; + Receive from Embellishment -Sewing Input" ><?  echo $inhand; ?></td>
	                    <td width="100" align="right"><p><? echo $ready_to_sewing; ?></p></td>
	                    <td width="100" align="center"><p><? echo $line_name; ?> </p></td>

	                    <td width="60" align="center"><p><? echo $floor_name; ?></p></td>
	                    <td width="80" align="right"><p><? $fin_cons=0; $cutable_pcs=0; $fin_cons=number_format(($con_per_dzn[$po_id][$color_id]*12/$costing_per_qty),3); $cutable_pcs=($total_fabric/$fin_cons)*12; echo number_format($cutable_pcs,2); ?></p></td>
	                    <td width="80" align="right"><p><? $cutting_kg=0; $cutting_kg=($fin_cons/12)*$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']; echo number_format($cutting_kg,2); ?></p></td>
	                    <td width="80" align="right"><p><? $cutting_bal=0; $cutting_bal=$total_fabric-$cutting_kg; echo number_format($cutting_bal,2); ?></p></td>

	                     <td width="70" align="right"><p><? echo number_format($sewingout_qnty_pre,2); ?></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,5,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><? echo number_format($sewingout_qnty_today,2); ?></a></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,5,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($total_sewingout_qnty_bal,2); ?></a></p></td>
	                     <td width="70" align="right"><p><?  echo number_format($sewout_rej_qty_today,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($total_sewingout_reject_qty_bal,2); ?></p></td>
	                     <td width="70" align="right"><p><?  echo number_format($total_sewingout_wip_qty,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($poly_qnty_pre,2); ?></p></td>

	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,7,'iron_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo number_format($poly_qnty_today,2); ?></a></p></td>

	                      <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,7,'iron_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($total_poly_qnty,2); ?></a></p></td>

	                     <td width="70" align="right"><p><?  echo number_format($ploy_rej_qty_today,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($total_ploy_rej_qty,2); ?></p></td>
	                     <td width="70" align="right"><p><?  echo number_format($tot_poly_wip,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($fin_qty_pre,2); ?></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,8,'packing_and_finishing_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><? echo number_format($fin_qty_today,2); ?></a></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,8,'packing_and_finishing_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($tot_fin_qnty,2); ?></a></p></td>
	                     <td width="70" align="right"><p><? echo number_format($fin_rej_qty_today,2); ?></p></td>
	                      <td width="70" align="right"><p><?  echo number_format($tot_fin_reject_qty,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($tot_fin_wip,2); ?></p></td>


	                    <?
	                    if($ex_factory_level==1)
	                    {
	                     	if($r==0)
	                     	{
		                     	?>

	                     		<td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><?  echo number_format($exf_qnty_pre,2); ?></p></td>
			                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><a href="##" onclick="javascript:alert('It is gross level production, so popup not found.');"><?  echo number_format($exf_qnty_today,2); ?></a></p></td>
			                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><a href="##" onclick="javascript:alert('It is gross level production, so popup not found.');"><? echo number_format($tot_exf_qnty,2); ?></a></p></td>

			                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><?  echo number_format($ex_fact_wip,2); ?></p></td>
		                     	<?
		                     	$r++;
		                     	// $po_wise_total_tot_exf_wip = $ex_fact_wip;
	                     	}
	                 	}
	                    else
	                    {
	                     	?>
		                    <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,'','ex_factory_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo number_format($exf_qnty_today,2); ?></a></p></td>
		                    <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,'','ex_factory_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($tot_exf_qnty,2); ?></a></p></td>

		                    <td width="70" align="right"><p><?  echo number_format($ex_fact_wip,2); ?></p></td>

	                    	<?
	                    	// $po_wise_total_tot_exf_wip = $ex_fact_wip;
		                }
		                ?>




	                    <td  align="center"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_remarks',650,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>','')">
						<?
						   if($total_delivery_cut-$po_number_data[$po_id][$color_id][$item_id]['plan_qty']>0) echo "Receive Ok";
						   else  if($total_sew_input_balance-$po_number_data[$po_id][$item_id][$color_id]['plan_qty']>0) echo "Input Ok";
						   else echo "Remarks";

						?></a></td>
	        	  </tr>
							<?
					 $job_arr[]=$po_number_data[$po_id][$item_id][$color_id]['job_no'];
					 $buyer_arr[]=$po_number_data[$po_id][$item_id][$color_id]['buyer_name'];
					 $i++;
	                } //end foreach 2nd
				}
				?>
					<tr bgcolor="#999999" style=" height:15px">
						<td width="40"></td>
						<td width="80"></td>
						<td width="60"></td>
						<td width="50"></td>
						<td width="100"></td>
						<td width="80"></td>
                        <td width="80"></td>
                        <td width="80"></td>
						<td width="80"></td>
                        <td width="80"></td>
						<td width="100"><strong> </strong></td>
                        <td width="120"><strong> </strong></td>
						<td width="100" align="right"><strong>PO Total:</strong></td>
						<td width="70" align="right"><? echo $po_wise_total_order; ?></td>
						<td width="70" align="right"><?  echo $po_wise_total_plan; ?></td>
                        <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
						<td width="70" align="right"><?  echo number_format($po_wise_total_fabric_qty,2); ?></td>
						<td width="60" align="right"><?  echo number_format($po_wise_total_fabric_pre,2); ?></td>
						<td width="60" align="right"><?  echo number_format($po_wise_fabric_today_total,2); ?></td>
						<td width="60" align="right"><?  echo number_format($po_wise_fabric_total,2);?></td>
						<td width="60" align="right"><?  echo number_format($po_wise_fabric_bal,2); ?></td>

                        <td width="70" align="right"><?  echo number_format($po_wise_possible_cut_qty,0); ?></td>
						<td width="60" align="right"><?  echo $po_wise_pre_cut; ?></td>
						<td width="60" align="right"><?  echo $po_wise_today_cut; ?></td>
						<td width="60" align="right"><?  echo $po_wise_total_cut; ?></td>
						<td width="60" align="right"></td>
						<td width="60" align="right"><?  echo $po_wise_cutting_balance; ?></td>
                        <td width="60" align="right"><?  echo number_format($po_wise_possible_cut_qty-$po_wise_total_cut,0); ?></td>
                        <td width="60" align="right"><?  echo $po_wise_priv_deliv_cut; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_deliv_cut; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_delivery_cut; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_deliv_cut_bal; ?></td>

                        <td width="60" align="right"><?  echo $po_wise_priv_print_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_print_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_print_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_priv_print_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_print_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_print_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_print_iss-$po_wise_total_print_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_priv_embl_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_embl_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_embl_iss; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_priv_embl_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_embl_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_embl_rec; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_embl_iss-$po_wise_total_embl_rec; ?></td>


                        <td width="60" align="right"><?  echo $po_wise_priv_sew; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_today_sew; ?></td>
                        <td width="60" align="right"> <? echo $po_wise_total_sew_; ?></td>
                        <td width="60" align="right"><? //echo $input_percentage; ?></td>
                        <td width="60" align="right"><?  echo $po_wise_total_sew_bal; ?></td>
                        <td width="70" align="right"><? echo $po_wise_cutinhand; ?></td>
                        <td width="70" align="right"><? echo $po_wise_inhand; ?></td>
                        <td width="100" align="right"><? echo $po_wise_ready_to_sewing; ?></td>

                        <td width="100" align="right"><? //echo  $sewing_line ?></td>
                        <td width="60" align="right"><? //echo  $unit ?></td>
                        <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
                        <td width="80" align="right"><? //echo  $cutting_kg ?></td>
                        <td width="80" align="right"><? //echo  $cutting_bal ?></td>

                        <td width="70" align="right"><? echo  $po_wise_priv_sew_out; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sew_out_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sew_out_bal; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sew_out_reject_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sew_out_reject_bal; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_sewingout_wip_qty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_poly_qnty_pre; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_poly_qnty_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_poly_qnty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_ploy_rej_qty_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_ploy_rej_qty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_tot_poly_wip; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_qnty_pre; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_qnty_today;?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_qnty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_rej_qty_today; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_fin_rej_qty; ?></td>
                        <td width="70" align="right"><? echo  $po_wise_total_tot_fin_wip; ?></td>
                        <td width="70" align="right"><? echo  $exFactPoQtyArray[$po_id]['exf_qnty_pre']; ?></td>
                        <td width="70" align="right"><? echo  $exFactPoQtyArray[$po_id]['exf_qnty_today']; ?></td>
                        <td width="70" align="right"><? echo  $totEx = $exFactPoQtyArray[$po_id]['exf_qnty_pre']+$exFactPoQtyArray[$po_id]['exf_qnty_today'];; ?></td>
                        <td width="70" align="right"><? //echo $ex_fact_wip; ?></td>

						<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
				  </tr>
				<?

	 		 }

				?>
	                         <tr bgcolor="#CCCCCC" id="">
	                                <td width="40"><? // echo $i;?></td>
	                                <td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
	                                <td width="60"></td>
	                                <td width="50"></td>
	                                <td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="100"><strong></strong></td>
	                                <td width="120"><strong></strong></td>
	                                <td width="100" align="right"><strong>Job Total:</strong></td>
	                                <td width="70" align="right"><? echo $job_total_order; ?></td>
	                                <td width="70" align="right"><?  echo $job_total_plan; ?></td>
	                                <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
	                                <td width="70" align="right"><?  echo number_format($job_total_fabric_qty,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_total_fabric_pre,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_today_total,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_total,2);?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_bal,2); ?></td>
	                                <td width="70" align="right"><?  echo number_format($job_possible_cut_qty,0); ?></td>
	                                <td width="60" align="right"><?  echo $job_pre_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_cut; ?></td>
	                                <td width="60" align="right"></td>
	                                <td width="60" align="right"><?  echo $job_cutting_balance; ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_possible_cut_qty-$job_total_cut,0); ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_deliv_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_deliv_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_delivery_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_deliv_cut_bal; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_iss-$job_total_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_iss-$job_total_embl_rec; ?></td>

	                                <td width="60" align="right"><?  echo $job_priv_sew; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_sew; ?></td>
	                                <td width="60" align="right"> <? echo $job_total_sew_input; ?></td>
	                                <td width="60" align="right"><? //echo $input_percentage; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_sew__bal; ?></td>
	                                <td width="70" align="right"><? echo $job_cutinhand; ?></td>
	                                <td width="70" align="right"><? echo $job_inhand; ?></td>
	                                <td width="100" align="right"><? echo $job_ready_to_sewing; ?></td>

	                                <td width="100" align="right"><? //echo  $sewing_line ?></td>

	                                <td width="60" align="right"><? //echo  $unit ?></td>
	                                <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                                <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                                <td width="80" align="right"><? //echo  $cutting_bal ?></td>

	                                <td width="70" align="right"><? echo  $job_priv_sewout; ?></td>
	                                <td width="70" align="right"><? echo  $job_today_sewout; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sew_out_bal; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sew_out_reject_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sew_out_reject_bal; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sewingout_wip_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_poly_qnty_pre; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_poly_qnty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_poly_qnty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_ploy_rej_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_tot_poly_wip; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_qnty_pre; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_qnty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_qnty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_rej_qty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_rej_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_tot_fin_wip; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_exf_qty_pre; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_exf_qty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_exf_tot_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_tot_exf_wip; ?></td>
	                                <td width="70" align="right"><? //echo  $cutting_bal; ?></td>



	                                <td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
	                          </tr>
	                           <tr bgcolor="#999999" style=" height:15px">
										<td width="40"><? // echo $i;?></td>
										<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
										<td width="60"></td>
										<td width="50"></td>
										<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
										<td width="80"></td>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
										<td width="80"></td>
	                                    <td width="80"></td>
										<td width="100"><strong> </strong></td>
	                                    <td width="120"><strong> </strong></td>
										<td width="100" align="right"><strong>Buyer Total:</strong></td>
										<td width="70" align="right"><? echo $buyer_total_order; ?></td>
										<td width="70" align="right"><?  echo $buyer_total_plan; ?></td>
	                                    <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
										<td width="70" align="right"><?  echo number_format($buyer_total_fabric_qty,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_total_fabric_pre,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_today_total,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_total,2);?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_bal,2); ?></td>

	                                    <td width="70" align="right"><?  echo number_format($buyer_possible_cut_qty,0); ?></td>
										<td width="60" align="right"><?  echo $buyer_pre_cut; ?></td>
										<td width="60" align="right"><?  echo $buyer_today_cut; ?></td>
										<td width="60" align="right"><?  echo $buyer_total_cut; ?></td>
										<td width="60" align="right"></td>
										<td width="60" align="right"><?  echo $buyer_cutting_balance; ?></td>
	                                    <td width="60" align="right"><?  echo number_format($buyer_possible_cut_qty-$buyer_total_cut,0); ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_deliv_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_deliv_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_delivery_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_deliv_cut_bal; ?></td>

	                                    <td width="60" align="right"><?  echo $buyer_priv_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_iss-$buyer_total_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_iss-$buyer_total_embl_rec; ?></td>


	                                    <td width="60" align="right"><?  echo $buyer_priv_sew; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_sew; ?></td>
	                                    <td width="60" align="right"> <? echo $buyer_total_sew_; ?></td>
	                                    <td width="60" align="right"><? //echo $input_percentage; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_sew_bal; ?></td>
	                                    <td width="70" align="right"><? echo $buyer_cutinhand; ?></td>
	                                    <td width="70" align="right"><? echo $buyer_inhand; ?></td>
	                                    <td width="100" align="right"><? echo $buyer_ready_to_sewing; ?></td>

	                                    <td width="100" align="right"><? //echo  $sewing_line ?></td>
	                                    <td width="60" align="right"><? //echo  $unit ?></td>
	                                    <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_bal ?></td>

	                                    <td width="70" align="right"><? echo  $buyer_priv_sew_out; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_bal; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_bal; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sewingout_wip_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_poly_qnty_pre; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_poly_qnty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_poly_qnty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_tot_poly_wip; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_qnty_pre; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_qnty_today;?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_qnty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_tot_fin_wip; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_exf_qty_pre; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_exf__qty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_exf_tot_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_tot_exf_wip; ?></td>

										<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
								  </tr>
	                            <tfoot>
	                                 <tr>
	                                    <th width="40"><? // echo $i;?></th>
	                                    <th width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
	                                    <th width="60"></td>
	                                    <th width="50"></td>
	                                    <th width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
	                                    <th width="80"></th>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
	                                    <th width="80"></th>
	                                    <th width="80"></th>
	                                    <th width="100"> <strong></strong></th>
	                                    <th width="120"> <strong></strong></th>
	                                    <th width="100" align="right"><strong>Grand Total:</strong></th>
	                                    <th width="70" align="right"><? echo $grand_total_order; ?></th>
	                                    <th width="70" align="right"><?  echo $grand_total_plan; ?></th>
	                                    <th width="70" align="right"><? // echo $grand_total_plan; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_total_fabric_qty,2); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_total_fabric_pre,2); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_fabric_today_total,2); ?></th>
	                                    <th width="60" align="right"><?  echo number_format($grand_fabric_total,2);?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_fabric_bal,2); ?></th>

	                                    <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_possible_cut_qty,0); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_pre_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_cutting_balance; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_possible_cut_qty-$grand_total_cut,0); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_deliv_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_deliv_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_delivery_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_deliv_cut_bal; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_iss-$grand_total_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_iss-$grand_total_embl_rec; ?></th>


	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_sew; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_sew; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><? echo $grand_total_sew_; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><? //echo $input_percentage; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_sew_bal; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><? echo $grand_cutinhand; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><? echo $grand_inhand; ?></th>
	                                    <th width="100" align="right" style="word-break:break-all;"><? echo $grand_ready_to_sewing; ?></th>

	                                    <th width="100" align="right"><? //echo  $sewing_line ?></th>

	                                    <th width="60" align="right"><? //echo  $unit ?></th>
	                                    <th width="80" align="right"><? //echo  $cutble_pcs ?></th>
	                                    <th width="80" align="right"><? //echo  $cutting_kg ?></th>
	                                    <th width="80" align="right"><? //echo  $cutting_bal ?></th>

	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_today ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_reject_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_reject_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_reject_bal; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_wip_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_poly_qnty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_poly_qnty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_poly_qnty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_ploy_rej_qty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_ploy_rej_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_poly_wip; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_qnty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_qnty_today ;?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_qnty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_rej_qty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_rej_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_fin_wip; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_exf_qty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_exf_qty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_exf_wip; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_exf_wip; ?></th>

	                                    <th  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></th>
	                             </tr>
	  						</tfoot>

	                </table>
	           </div>
	  	</div>

	  	</fieldset>

		<?
	}
	// End Item Wise3
	elseif ($rpt_type==22) //ITEM WISE
	{
		// ==================================== MAIN QUERY ==========================================
	 	$pro_date_sql="SELECT  a.id,a.job_no_mst,a.po_number,
 		d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,d.item_number_id as gmt_id,b.buyer_name,b.style_ref_no as style,b.job_no_prefix_num,$insert_year
 		as year ,d.color_number_id, a.file_no, a.grouping as int_ref_no ,
 		min(d.country_ship_date) as minimum_shipdate
 		from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
 		where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and  a.job_no_mst=d.job_no_mst and  a.is_deleted=0 and a.status_active=1 and
 		b.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and
 		b.status_active=1 and a.is_confirmed=1 $company_name $buyer_name $style_cond $order_cond $sql_cond $job_cond_id $country_ship_date $shipping_cond
 		group by a.id,a.job_no_mst,a.po_number,
 		d.order_quantity,d.plan_cut_qnty ,d.item_number_id,b.buyer_name,b.style_ref_no ,b.job_no_prefix_num,d.color_number_id, a.file_no, a.grouping,b.insert_date
 		order by  b.buyer_name,a.job_no_mst";
 		// echo $pro_date_sql;die();
 		$pro_date_sql_res = sql_select($pro_date_sql);
	  	$po_min_shipdate_data=array();
	  	$po_number_id = array();
	  	foreach($pro_date_sql_res as $row)
	  	{
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['id']=$row[csf('id')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no_mst')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['gmt_id']=$row[csf('gmt_id')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['po_quantity']+=$row[csf('order_qty')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['plan_qty']+=$row[csf('plan_qty')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['buyer_name']=$row[csf('buyer_name')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['style']=$row[csf('style')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['job_prifix']=$row[csf('job_no_prefix_num')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['year']=$row[csf('year')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['color_id']=$row[csf('color_number_id')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['file_no']=$row[csf('file_no')];
		  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['int_ref_no']=$row[csf('int_ref_no')];
		  $po_number_id[$row[csf('id')]]=$row[csf('id')];

		  $po_min_shipdate_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['minimum_shipdate']=$row[csf('minimum_shipdate')];
	  	}
	  	unset($pro_date_sql);
 		// echo "<pre>";
 		// print_r($po_min_shipdate_data);
 		// echo "</pre>";

 		$po_number_id=implode(",",array_unique($po_number_id));

		$po_condition="";
		$po_number_id_arr=explode(",", trim(str_replace("'","",$po_number_id)));
		if($db_type==2 && count($po_number_id_arr)>999)
		{
	  		$chunk_arr=array_chunk($po_number_id_arr, 999);
	  		foreach($chunk_arr as $keys=>$vals)
	  		{
	  			$po_ids=implode(",", $vals);
	  			if($po_condition=="")
	  			{
	  				$po_condition.=" and ( a.po_break_down_id in ($po_ids) ";
	  			}
	  			else
	  			{
	  				$po_condition.=" or a.po_break_down_id in ($po_ids) ";
	  			}
	  		}
	  		$po_condition.=" ) ";
		}
		else
		{
			$po_condition=" and a.po_break_down_id in (".str_replace("'","",$po_number_id).")";
		}

		//
		/*$date_wise_po="SELECT po_break_down_id from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date";
	 	$date_wise_po_arr=array();
	 	foreach(sql_select($date_wise_po) as $vals)
	 	{
	 		$date_wise_po_arr[$vals[csf("po_break_down_id")]]=$vals[csf("po_break_down_id")];
	 	}
	 	$date_wise_po_ids=implode(",", $date_wise_po_arr);*/
	 	// GETTING ORDER QUANTITY  ADDED IN 13/10/2018

	 	// ===================================== GETTING ORDER QTY =============================================
	 	$order_qnty_array=array();
	 	$plan_cut_qnty_array=array();
		$order_qnty_sqls="SELECT a.po_break_down_id,a.color_number_id,a.order_quantity,a.item_number_id,a.plan_cut_qnty from wo_po_color_size_breakdown a where a.status_active in(1,2,3) and a.is_deleted=0 $po_condition";

		foreach(sql_select($order_qnty_sqls) as $values)
		{
		 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]['po_quantity']+=$values[csf("order_quantity")];
		 	$plan_cut_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]['plan_cut_qnty']+=$values[csf("plan_cut_qnty")];
		}

 		// ============================== for exfactory rowspan ===========================
 		$rowspan_arr = array();
 		foreach ($po_number_data as $po_key => $po_arr)
 		{
 			foreach ($po_arr as $item_key => $item_arr)
 			{
 				foreach ($item_arr as $color_key => $color_arr)
 				{
 					$rowspan_arr[$po_key][$item_key]++;
 				}
 			}
 		}
	  	// echo "<pre>";
 		// print_r($rowspan_arr);
 		// echo "</pre>";

	   	$sew_line_arr=array();
	   	if($db_type==0)
	   	{
	   		$sql_line=sql_select("SELECT group_concat(distinct a.sewing_line) as line_id, group_concat(distinct a.floor_id) as floor_id, a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id");
	   	}
	   	if($db_type==2)
	   	{
	   		$sql_line=sql_select("SELECT listagg(cast(a.sewing_line as varchar2(2000)),',') within group (order by a.sewing_line) as line_id, listagg(cast(a.floor_id as varchar2(2000)),',') within group (order by a.floor_id) as floor_id, a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id");
	   	}

	   	foreach($sql_line as $row_sew)
	   	{
	   		$sew_line_arr[$row_sew[csf('po_break_down_id')]]['line']= implode(',',array_unique(explode(',',$row_sew[csf('line_id')])));
			$sew_line_arr[$row_sew[csf('po_break_down_id')]]['floor_id']= implode(',',array_unique(explode(',',$row_sew[csf('floor_id')])));
	   	}
	   	unset($sql_line);


	  	if($po_number_id=="") $po_number_id=0;
	  	$company_id = str_replace("'","",$cbo_company_name);
	  	$variable_sql=sql_select("SELECT ex_factory from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
		$ex_factory_level=$variable_sql[0][csf("ex_factory")];

	  	if($ex_factory_level==1) // for gross level
	  	{
	  		$ex_factory_sql="SELECT a.po_break_down_id as order_id, a.item_number_id as item_id,
		    sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date=$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS exf_qnty_today,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date=$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS ret_exf_qnty_today,
			sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date<$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS exf_qnty_pre,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date<$txt_production_date THEN a.ex_factory_qnty ELSE 0 END) AS ret_exf_qnty_pre
			from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a
			where m.id=a.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and m.status_active=1 and m.is_deleted=0   $po_condition
			group by  a.po_break_down_id, a.item_number_id";
	  	}
	  	else // for color or color and size level
	  	{
	  		$ex_factory_sql="SELECT a.po_break_down_id as order_id, a.item_number_id as item_id, c.color_number_id as color_id,
		    sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END) AS exf_qnty_today,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_today,
			sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date<$txt_production_date THEN b.production_qnty ELSE 0 END) AS exf_qnty_pre,
			sum(CASE WHEN m.entry_form=85 and a.ex_factory_date<$txt_production_date THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_pre
			from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
			where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0   $po_condition
			group by  a.po_break_down_id, a.item_number_id, c.color_number_id";
	  	}


		 	// echo $ex_factory_sql;die;
			$ex_factory_sql_result=sql_select($ex_factory_sql);
			foreach($ex_factory_sql_result as $row)
			{
				if($ex_factory_level==1)
				{
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]]['exf_qnty_today']=$row[csf('exf_qnty_today')]-$row[csf('ret_exf_qnty_today')];
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]]['exf_qnty_pre']=$row[csf('exf_qnty_pre')]-$row[csf('ret_exf_qnty_pre')];
				}
				else
				{
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['exf_qnty_today']=$row[csf('exf_qnty_today')]-$row[csf('ret_exf_qnty_today')];
					$production_data[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['exf_qnty_pre']=$row[csf('exf_qnty_pre')]-$row[csf('ret_exf_qnty_pre')];
				}
			}


		  $production_sql="SELECT a.po_break_down_id,c.color_number_id,c.item_number_id as gmt_id,
		   sum(CASE WHEN b.production_type =1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cutting_qnty,
		   sum(CASE WHEN b.production_type =1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cutting_qnty_pre,
		   sum(CASE WHEN b.production_type =2  and a.embel_name=4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS sp_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printing_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printing_qnty_pre ,
		   sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printreceived_qnty,
		   sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS printreceived_qnty_pre,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS embl_qnty,
		   sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS embl_qnty_pre ,
		   sum(CASE WHEN b.production_type =3  and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS emblreceived_qnty,
		   sum(CASE WHEN b.production_type =3  and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END)
		   AS emblreceived_qnty_pre,
		   min(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<=".$txt_production_date." THEN a.production_date end)
		   AS min_printin_date,
		   min(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<=".$txt_production_date." THEN a.production_date end)
		   as min_embl_date,
		   sum(CASE WHEN b.production_type =4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty,
		   sum(CASE WHEN b.production_type =4 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty_pre,
		   sum(CASE WHEN b.production_type =5 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingout_qnty_pre,
		   sum(CASE WHEN b.production_type =5 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingout_qnty_today,
		   sum(CASE WHEN b.production_type =5 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sewout_rej_qty_today,
		   sum(CASE WHEN b.production_type =5 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sewout_rej_qty_pre,

		   sum(CASE WHEN b.production_type =7 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty_pre,
		   sum(CASE WHEN b.production_type =7 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty_today,
		   sum(CASE WHEN b.production_type =7 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS ploy_rej_qty_pre,
		   sum(CASE WHEN b.production_type =7 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS ploy_rej_qty_today,

		    sum(CASE WHEN b.production_type =8 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS fin_qty_pre,
			sum(CASE WHEN b.production_type =8 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS fin_qty_today,
			sum(CASE WHEN b.production_type =8 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS fin_rej_qty_today,
			sum(CASE WHEN b.production_type =8 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS fin_rej_qty_pre,

		    sum(CASE WHEN b.production_type =9 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty_pre,
			sum(CASE WHEN b.production_type =9 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty


		   from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c where a.id=b.mst_id
		   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		   and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id $po_condition  group by a.po_break_down_id,c.color_number_id,c.item_number_id";
		//    echo "$production_sql";die;
			//and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
			 $production_mst_sql=sql_select($production_sql);
	       foreach($production_mst_sql as $val)
		   {
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printing_qnty']=$val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['emblreceived_qnty_pre']=$val[csf('emblreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['emblreceived_qnty']=$val[csf('emblreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['embl_qnty_pre']=$val[csf('embl_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['embl_qnty']=$val[csf('embl_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['min_printin_date']=$val[csf('min_printin_date')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['min_embl_date']=$val[csf('min_embl_date')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingout_qnty_pre']=$val[csf('sewingout_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewingout_qnty_today']=$val[csf('sewingout_qnty_today')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewout_rej_qty_today']=$val[csf('sewout_rej_qty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['sewout_rej_qty_pre']=$val[csf('sewout_rej_qty_pre')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['poly_qnty_pre']=$val[csf('poly_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['poly_qnty_today']=$val[csf('poly_qnty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['ploy_rej_qty_pre']=$val[csf('ploy_rej_qty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['ploy_rej_qty_today']=$val[csf('ploy_rej_qty_today')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_qty_pre']=$val[csf('fin_qty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_qty_today']=$val[csf('fin_qty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_rej_qty_today']=$val[csf('fin_rej_qty_today')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['fin_rej_qty_pre']=$val[csf('fin_rej_qty_pre')];

				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cut_delivery_qnty']=$val[csf('cut_delivery_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('gmt_id')]][$val[csf('color_number_id')]]['cut_delivery_qnty_pre']=$val[csf('cut_delivery_qnty_pre')];


				$po_number_gmt[]=$val[csf('po_break_down_id')];
		    }

			unset($production_mst_sql);

			$sql_cutting_delevery=sql_select("select a.po_break_down_id,c.color_number_id,c.item_number_id as gmt_id
			sum(CASE WHEN  a.cut_delivery_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty,
		    sum(CASE WHEN  a.cut_delivery_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty_pre
			from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c  where a.id=b.mst_id
		    and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id   $po_condition
		    and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		    group by a.po_break_down_id,c.color_number_id,c.item_number_id");
		   //and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
			foreach( $sql_cutting_delevery as $inf)
			{
				$production_data_arr[$inf[csf('po_break_down_id')]][$inf[csf('gmt_id')]][$inf[csf('color_number_id')]]['cut_delivery_qnty']=$inf[csf('cut_delivery_qnty')];

				$production_data_arr[$inf[csf('po_break_down_id')]][$inf[csf('gmt_id')]][$inf[csf('color_number_id')]]['cut_delivery_qnty_pre']=$inf[csf('cut_delivery_qnty_pre')];
			}
			unset($sql_cutting_delevery);
			$po_condition_fab=str_replace("po_break_down_id", "po_breakdown_id", $po_condition);

	        $sql_fabric_qty=sql_select("SELECT a.po_breakdown_id,a.color_id,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
		    ELSE 0 END ) AS grey_fabric_issue,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =4  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
		    ELSE 0 END ) AS grey_fabric_issue_return,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS finish_fabric_rece,
			sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS finish_fabric_rece_return,
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
		    ELSE 0 END ) AS fabric_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =2 and b.item_category=2  AND a.entry_form =18 THEN a.quantity
			ELSE 0 END ) AS fabric_qty_pre,
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END )
			AS trans_in_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_pre,
			sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END )
			AS trans_out_qty,
			sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_pre
			FROM order_wise_pro_details a,inv_transaction b
		    WHERE a.trans_id = b.id
			and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0  $po_condition_fab group by a.po_breakdown_id,a.color_id");
			//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
			$fabric_pre_qty=array();
			$fabric_today_qty=array();
			$total_fabric=array();
			$fabric_balance=array();
			$fabric_wip=array();
			foreach($sql_fabric_qty as $value)
			{
				$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
				$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];

				$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
				-$value[csf("trans_out_pre")];
				$fabric_today_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty")]+$value[csf("trans_in_qty")]
				-$value[csf("trans_out_qty")];

				$po_id_fab[]=$value[csf("po_breakdown_id")];
			}


			unset($sql_fabric_qty);


	 		//*********************************************************************************************************************

		  $color_size_qty_arr=array();
		  $po_condition2=str_replace("a.po_break_down_id", "po_break_down_id", $po_condition);
		  $po_condition3=str_replace("a.po_break_down_id", "b.po_break_down_id", $po_condition);
		  $color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty
		  from wo_po_color_size_breakdown
		  where  is_deleted=0  and  status_active=1  $po_condition2
		  group by po_break_down_id,item_number_id,size_number_id,color_number_id");
		  foreach($color_size_sql as $s_id)
		  {
			$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')];
		  }

		  unset($color_size_sql);


	  	   $sql_sewing=sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum(b.cons) AS conjumction
		   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
		   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  $po_condition3 and b.cons!=0
		   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
		   $con_per_dzn=array();
		   $po_item_qty_arr=array();
		   $color_size_conjumtion=array();
	       foreach($sql_sewing as $row_sew)
	       {
		    $color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);

			$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
			$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
	       }

		   unset($sql_sewing);

		  foreach($color_size_conjumtion as $p_id=>$p_value)
		  {
			 foreach($p_value as $i_id=>$i_value)
			 {
				foreach($i_value as $c_id=>$c_value)
				 {
					 foreach($c_value as $s_id=>$s_value)
					 {
						 foreach($s_value as $b_id=>$b_value)
						 {
						   $order_color_size_qty=$b_value['plan_cut_qty'];
						   $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
						   $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
						   $conjunction_per= ($b_value['conjum']*$order_color_size_qty_per/100);
						   $con_per_dzn[$p_id][$c_id]+=$conjunction_per;

						  // echo $p_id."**".$order_color_size_qty."**".$order_qty."**".$order_color_size_qty_per."**".$b_value['conjum']."<br>";
						 }
					 }
				 }
			 }
		  }
			//print_r($con_per_dzn);die;
		 	//**********************************************************************************************************************


	     $sql_tna_date=sql_select("select a.po_number_id, a.task_start_date,a.task_finish_date	from  tna_process_mst a, lib_tna_task b
		 where b.task_name=a.task_number  and task_name=84");
		 $tna_date_arr=array();
		 foreach($sql_tna_date as $tna_val)
		 {
		 $tna_date_arr[$tna_val[csf('po_number_id')]]['tna_start']= $tna_val[csf('task_start_date')];
		 $tna_date_arr[$tna_val[csf('po_number_id')]]['tna_end']= $tna_val[csf('task_finish_date')];
		 }


		 unset($sql_tna_date);


		 $costing_per_sql=sql_select("select job_no,costing_per from wo_pre_cost_mst");
		 $costing_per_arr=array();
		 foreach($costing_per_sql as $cost_val)
		 {
			$costing_per_arr[$cost_val[csf('job_no')]]=$cost_val[csf('costing_per')];
		 }

		 unset($costing_per_sql);

		 $ready_to_sewing_sql=sql_select("select po_break_down_id, color_id, sewing_qnty as sewing_qnty from ready_to_sewing_dtls");
		 $ready_to_sewing_arr=array();
		 foreach($ready_to_sewing_sql as $row)
		 {
			$ready_to_sewing_arr[$row[csf('po_break_down_id')]][$row[csf('color_id')]]+=$row[csf('sewing_qnty')];

		 }
		 unset($ready_to_sewing_sql);
		 ob_start();
			 ?>
	         <fieldset style="width:5760px;">
	        	   <table width="1880"  cellspacing="0"   >
	                    <tr class="form_caption" style="border:none;">
	                           <td colspan="57" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report(Item Wise)</td>
	                     </tr>
	                    <tr style="border:none;">
	                            <td colspan="57" align="center" style="border:none; font-size:16px; font-weight:bold">
	                            Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
	                            </td>
	                      </tr>
	                      <tr style="border:none;">
	                            <td colspan="57" align="center" style="border:none;font-size:12px; font-weight:bold">
	                            <? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
	                            </td>
	                      </tr>
	                </table>
	             <br />
	             <table cellspacing="0"  border="1" rules="all"  width="5760" class="rpt_table">
	                <thead>
	                	<tr >
	                        <th width="40" rowspan="2">SL</th>
	                        <th width="80" rowspan="2">Buyer</th>
	                        <th width="60" rowspan="2">Job No</th>
	                        <th width="50" rowspan="2">Year</th>
	                        <th width="100" rowspan="2">Order No</th>
	                        <th width="80" rowspan="2">File No</th>
	                        <th width="80" rowspan="2">Int. Ref. No</th>
	                        <th width="80" rowspan="2">TNA Start Date</th>
	                        <th width="80" rowspan="2">TNA End Date</th>
	                        <th width="80" rowspan="2">First Shipment Date</th>
	                        <th width="100" rowspan="2">Style</th>
	                        <th width="120" rowspan="2">Item</th>
	                        <th width="100" rowspan="2">Color</th>
	                        <th width="70" rowspan="2">Order Qty.</th>
	                        <th width="70" rowspan="2">Plan Cut Qty.</th>
	                        <th width="70" rowspan="2">Fin Fab Cons/Dzn.</th>
	                        <th width="70" rowspan="2">Fabric Required Qty</th>
	                        <th width="240" colspan="4">Fabric Receive Qty.</th>
	                        <th width="70" rowspan="2">Possible Cut Qty</th>
	                        <th width="300" colspan="5">Cutting</th>
	                        <th width="60" rowspan="2"> Cutting WIP</th>
	                        <th width="240" colspan="4">Cutting Delivery To Input</th>
	                        <th width="180" colspan="3">Delivery to Print</th>
	                        <th width="180" colspan="3">Receive from Print</th>
	                        <th width="60" rowspan="2">Print WIP</th>
	                        <th width="180" colspan="3">Delivery to Emb.</th>
	                        <th width="180" colspan="3">Receive from Emb.</th>
	                        <th width="60" rowspan="2">Emb. WIP</th>

	                        <th width="300" colspan="5"> Sewing Input</th>
	                        <th width="70" rowspan="2">Cutting Inhand</th>
	                        <th width="70" rowspan="2">Input Inhand</th>
	                        <th width="100" rowspan="2">Ready To Sewing</th>
	                        <th width="100" rowspan="2">Line No</th>

	                        <th width="60" rowspan="2">Unit(Floor)</th>
	                        <th width="80" rowspan="2">Cutable Pcs as per Fabric Received</th>
	                        <th width="80" rowspan="2">Cutting KG</th>
	                        <th width="80" rowspan="2">Cutting Balance Fabric</th>

	                        <th width="210" colspan="3">Sewing Output</th>
	                        <th width="70" rowspan="2">Today Sewing Reject</th>
	                        <th width="70" rowspan="2">Sewing Reject Total</th>
	                        <th width="70" rowspan="2">Sewing WIP</th>

	                        <th width="210" colspan="3">Iron Entry</th>

	                        <th width="70" rowspan="2">Today Iron Reject</th>
	                        <th width="70" rowspan="2">Iron Reject Total</th>
	                        <th width="70" rowspan="2">Iron WIP</th>

	                        <th width="210" colspan="3">Packing & Finishing</th>

	                        <th width="70" rowspan="2">Today Finishing Reject</th>
	                        <th width="70" rowspan="2">Finishing Reject Total</th>
	                        <th width="70" rowspan="2">Pac &Fin. WIP</th>
	                        <th width="210" colspan="3">Ex-Factory</th>
	                        <th width="70" rowspan="2">Ex-Fac. WIP</th>
	                        <th rowspan="2">Remarks</th>
	                    </tr>
	                    <tr>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Bal.</th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Cutting Persent </th>
	                        <th width="60" rowspan="2" title="Plancut - Cutting Qty">Bal.</th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Bal.</th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>
	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today </th>
	                        <th width="60" rowspan="2">Total </th>

	                        <th width="60" rowspan="2">Prev.</th>
	                        <th width="60" rowspan="2">Today</th>
	                        <th width="60" rowspan="2">Total</th>
	                        <th width="60" rowspan="2">%</th>
	                        <th width="60" rowspan="2" title="Plancut - Input">Balance</th>

	                         <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>

	                        <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>

	                        <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>

	                        <th width="70">Prev.</th>
	                        <th width="70">Today </th>
	                        <th width="70">Total </th>
	                    </tr>
	                </thead>
	            </table>

	             <div style="max-height:425px; overflow-y:scroll; width:5760px;" id="scroll_body">
	                    <table  border="1" class="rpt_table"  width="5742" rules="all" id="table_body" >
	                    <?
	                      $total_cut=0;
	                      $total_print_iss=0;
						  $total_embl_iss=0;
						  $total_wash_iss=0;
						  $total_sp_iss=0;
	                      $total_print_receive=0;
						  $total_sp_rec=0;
						  $total_embl_rec=0;
						  $total_wash_receive=0;
						  $total_sp_rec=0;
	                      $total_sew_input=0;
	                      $total_sew_out=0;
						  $total_delivery_cut=0;
	                      $cutting_balance=0;
						  $print_issue_balance=0;
						  $print_rec_balance=0;
					  	  $deliv_cut_bal=0;
						  $total_sew_input_balance=0;
						  $input_percentage=0;
						  $inhand=0;
						  $buyer_total_order=0;
						  $buyer_total_plan=0;
						  $buyer_total_fabric_qty=0;
						  $buyer_total_fabric_pre=0;
						  $buyer_fabric_total=0;
						  $buyer_fabric_today_total=0;
						  $buyer_fabric_bal=0;
						  $buyer_pre_cut=0;
						  $buyer_today_cut=0;
						  $buyer_total_cut=0;
						  $buyer_cutting_balance=0;
						  $buyer_priv_print_iss=0;
						  $buyer_today_print_iss=0;
						  $buyer_print_issue_balance=0;
						  $buyer_priv_print_rec=0;
						  $buyer_today_print_rec=0;
						  $buyer_total_print_rec=0;
						  $buyer_print_rec_balance=0;
						  $buyer_priv_deliv_cut=0;
						  $buyer_today_deliv_cut=0;
						  $buyer_total_delivery_cut=0;
						  $buyer_deliv_cut_bal=0;
						  $buyer_priv_sew=0;
						  $buyer_today_sew=0;
						  $buyer_total_sew_=0;
						  $buyer_total_sew__bal=0;
						  $buyer_inhand=0;
						  $buyer_arr=array();
						  $job_arr=array();
	                      $i=1;$k=1;


		  //echo "jahid";die;

	  	foreach($po_number_data as $po_id=>$po_arr)
		{
		foreach($po_arr as $item_id=>$item_arr)
		   {
		   	$r=0;
			 foreach($item_arr as $color_id=>$color_arr)
		     {
	 				if($i!=1)
					  {
						if(!in_array($po_number_data[$po_id][$item_id][$color_id]['job_no'],$job_arr))
						{
								?>
								   <tr bgcolor="#CCCCCC" id="">
										<td width="40"><? // echo $i;?></td>
										<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
										<td width="60"></td>
										<td width="50"></td>
										<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
										<td width="80"></td>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
										<td width="80"></td>
										<td width="80"></td>
	                                    <td width="100"><strong></strong></td>
										<td width="120"><strong></strong></td>
										<td width="100" align="right"><strong>Job Total:</strong></td>
										<td width="70" align="right"><? echo $job_total_order; ?></td>
										<td width="70" align="right"><?  echo $job_total_plan; ?></td>
										<td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
										<td width="70" align="right"><?  echo number_format($job_total_fabric_qty,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_total_fabric_pre,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_today_total,2); ?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_total,2);?></td>
										<td width="60" align="right"><?  echo number_format($job_fabric_bal,2); ?></td>

										<td width="70" align="right"><?  echo number_format($job_possible_cut_qty,0); ?></td>
										<td width="60" align="right"><?  echo $job_pre_cut; ?></td>
										<td width="60" align="right"><?  echo $job_today_cut; ?></td>
										<td width="60" align="right"><?  echo $job_total_cut; ?></td>
										<td width="60" align="right"><?  //echo $job_total_cut; ?></td>
										<td width="60" align="right"><?  echo $job_cutting_balance; ?></td>
										<td width="60" align="right"><?  echo number_format($job_possible_cut_qty,0)-$job_total_cut; ?></td>
										<td width="60" align="right"><?  echo $job_priv_deliv_cut; ?></td>
										<td width="60" align="right"><?  echo $job_today_deliv_cut; ?></td>
										<td width="60" align="right"><?  echo $job_total_delivery_cut; ?></td>
										<td width="60" align="right"><?  echo $job_deliv_cut_bal; ?></td>
										<td width="60" align="right"><?  echo $job_priv_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_today_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_iss; ?></td>
										<td width="60" align="right"><?  echo $job_priv_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_today_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_print_iss-$job_total_print_rec; ?></td>
										<td width="60" align="right"><?  echo $job_priv_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_today_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_iss; ?></td>
										<td width="60" align="right"><?  echo $job_priv_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_today_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_rec; ?></td>
										<td width="60" align="right"><?  echo $job_total_embl_iss-$job_total_embl_rec; ?></td>

										<td width="60" align="right"><?  echo $job_priv_sew; ?></td>
										<td width="60" align="right"><?  echo $job_today_sew; ?></td>
										<td width="60" align="right"> <? echo $job_total_sew_input; ?></td>
										<td width="60" align="right"><? //echo $input_percentage; ?></td>
										<td width="60" align="right"><?  echo $job_total_sew__bal; ?></td>
										<td width="70" align="right"><? echo $job_cutinhand; ?></td>
										<td width="70" align="right"><? echo $job_inhand; ?></td>
										<td width="100" align="right"><? echo $job_ready_to_sewing; ?></td>
										<td width="100" align="right"><? //echo  $sewing_line ?></td>

	                                    <td width="60" align="right"><? //echo  $unit ?></td>
	                                    <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_bal ?></td>


	                                   	 <td width="70" align="right"><? echo  $job_priv_sewout; ?></td>
	                                     <td width="70" align="right"><? echo  $job_today_sewout ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sew_out_bal; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sew_out_reject_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sew_out_reject_bal; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_sewingout_wip_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_poly_qnty_pre; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_poly_qnty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_poly_qnty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_ploy_rej_qty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_ploy_rej_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_tot_poly_wip; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_qnty_pre; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_qnty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_qnty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_rej_qty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_fin_rej_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_tot_fin_wip; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_exf_qty_pre; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_exf_qty_today; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_exf_tot_qty; ?></td>
	                                     <td width="70" align="right"><? echo  $job_total_tot_exf_wip; ?></td>


										<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
								  </tr>
								<?
								  $job_inhand=0;
								  $job_cutinhand=0;
								  $job_possible_cut_qty=0;
								  $job_total_order=0;
								  $job_total_plan=0;
								  $job_total_fabric_qty=0;
								  $job_total_fabric_pre=0;
								  $job_fabric_total=0;
								  $job_fabric_today_total=0;
								  $job_fabric_bal=0;
								  $job_pre_cut=0;
								  $job_today_cut=0;
								  $job_total_cut=0;
								  $job_cutting_balance=0;
								  $job_priv_print_iss=0;
								  $job_today_print_iss=0;
								  $job_total_print_iss=0;
								  $job_print_issue_balance=0;
								  $job_priv_print_rec=0;
								  $job_today_print_rec=0;
								  $job_total_print_rec=0;
								  $job_print_rec_balance=0;
								  $job_priv_deliv_cut=0;
								  $job_today_deliv_cut=0;
								  $job_total_delivery_cut=0;
								  $job_deliv_cut_bal=0;
								  $job_priv_sew=0;
								  $job_today_sew=0;
								  $job_total_sew_input=$job_today_sewout=$job_total_sew_out_bal=0;
								  $job_total_sew_out_reject_today=$job_total_sew_out_reject_bal=0;$job_total_sewingout_wip_qty=0;
								  $job_total_poly_qnty_pre=$job_total_poly_qnty_today=$job_total_poly_qnty=$job_total_ploy_rej_qty_pre=$job_total_ploy_rej_qty_pre=$job_total_ploy_rej_qty_today=$job_total_ploy_rej_qty=$job_total_tot_poly_wip=0;
								  $job_total_fin_qnty_pre=$job_total_fin_qnty_today=$job_total_fin_qnty=$job_total_fin_rej_qty_pre=$job_total_fin_rej_qty_today=$job_total_fin_rej_qty=$job_total_tot_fin_wip=0;
								  $job_total_exf_qty_pre=$job_total_exf_qty_today=$job_total_exf_tot_qty=$job_total_tot_exf_wip=0;
								  $job_total_sew__bal=0;
								  $job_priv_print_iss=0;
								  $job_today_print_iss=0;
								  $job_total_print_iss=0;
								  $job_priv_embl_iss=0;
								  $job_priv_print_rec=0;
								  $job_today_embl_iss=0;
								  $job_total_embl_iss=0;
								  $job_today_wash_iss=0;
								  $job_priv_wash_iss=0;
								  $job_today_sp_iss=0;
								  $job_total_wash_iss=0;
								  $job_total_sp_iss=0;
								  $job_priv_sp_iss=0;
								  $job_priv_print_rec=0;
								  $job_today_print_rec=0;
								  $job_total_print_rec=0;
								  $job_priv_wash_rec=0;
								  $job_today_wash_rec=0;
								  $job_total_wash_rec=0;
								  $job_priv_embl_rec=0;
								  $job_today_embl_rec=0;
								  $job_total_embl_rec=0;
								  $job_priv_sp_rec=0;
								  $job_today_sp_rec=0;
								  $job_total_sp_rec=0;
								  $job_ready_to_sewing=$job_priv_sewout=0;
						  }
					  }


					 if($i!=1)
					 {
					 if( !in_array($po_number_data[$po_id][$item_id][$color_id]['buyer_name'],$buyer_arr))
							{

							?>
							<tr bgcolor="#999999" style="height:15px">
							<td width="40"><? // echo $i;?></td>
							<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
							<td width="60"></td>
							<td width="50"></td>
							<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
							<td width="80"></td>
	                        <td width="80"></td>
	                        <td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="100"><strong> </strong></td>
	                        <td width="120"><strong> </strong></td>
							<td width="100" align="right"><strong>Buyer Total:</strong></td>
							<td width="70" align="right"><? echo $buyer_total_order; ?></td>
							<td width="70" align="right"><?  echo $buyer_total_plan; ?></td>
							<td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
							<td width="70" align="right"><?  echo number_format($buyer_total_fabric_qty,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_total_fabric_pre,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_today_total,2); ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_total,2);?></td>
							<td width="60" align="right"><?  echo number_format($buyer_fabric_bal,2); ?></td>

							<td width="70" align="right"><?  echo number_format($buyer_possible_cut_qty,0); ?></td>
							<td width="60" align="right"><?  echo $buyer_pre_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_cut; ?></td>
							<td width="60" align="right"><?  //echo $buyer_total_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_cutting_balance; ?></td>
							<td width="60" align="right"><?  echo number_format($buyer_possible_cut_qty,0)-$buyer_total_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_deliv_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_deliv_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_delivery_cut; ?></td>
							<td width="60" align="right"><?  echo $buyer_deliv_cut_bal; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_print_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_print_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_print_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_print_rec; ?></td>
						   <td width="60" align="right"><?   echo $buyer_total_print_iss-$buyer_total_print_rec; ?></td>

							<td width="60" align="right"><?  echo $buyer_priv_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_iss; ?></td>
							<td width="60" align="right"><?  echo $buyer_priv_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_rec; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_embl_iss-$buyer_total_embl_rec; ?></td>


							<td width="60" align="right"><?  echo $buyer_priv_sew; ?></td>
							<td width="60" align="right"><?  echo $buyer_today_sew; ?></td>
							<td width="60" align="right"> <? echo $buyer_total_sew_; ?></td>
							<td width="60" align="right"><? //echo $input_percentage; ?></td>
							<td width="60" align="right"><?  echo $buyer_total_sew_bal; ?></td>
							<td width="70" align="right"><? echo $buyer_cutinhand; ?></td>
							<td width="70" align="right"><? echo $buyer_inhand; ?></td>
							<td width="100" align="right"><? echo $buyer_ready_to_sewing; ?></td>

							<td width="100" align="right"><? //echo  $sewing_line ?></td>
	                        <td width="60" align="right"><? //echo  $unit ?></td>
	                        <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                        <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                        <td width="80" align="right"><? //echo  $cutting_bal ?></td>

	                        <td width="70" align="right"><? echo  $buyer_priv_sew_out ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_today ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_bal; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_bal; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_sewingout_wip_qty ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_poly_qnty_pre; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_poly_qnty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_poly_qnty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_tot_poly_wip; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_qnty_pre; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_qnty_today;?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_qnty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_tot_fin_wip; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_exf_qty_pre; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_exf__qty_today; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_exf_tot_qty; ?></td>
	                         <td width="70" align="right"><? echo  $buyer_total_tot_exf_wip ;?></td>


							<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
					  </tr>
								<?
								  $buyer_cutinhand=$buyer_ready_to_sewing=0;
								  $buyer_possible_cut_qty=0;
								  $buyer_total_order=0;
								  $buyer_total_plan=0;
								  $buyer_total_fabric_qty=0;
								  $buyer_total_fabric_pre=0;
								  $buyer_fabric_total=0;
								  $buyer_fabric_today_total=0;
								  $buyer_fabric_bal=0;
								  $buyer_pre_cut=0;
								  $buyer_today_cut=0;
								  $buyer_total_cut=0;
								  $buyer_cutting_balance=0;
								  $buyer_priv_print_iss=0;
								  $buyer_today_print_iss=0;
								  $buyer_total_print_iss=0;
								  $buyer_print_issue_balance=0;
								  $buyer_priv_print_rec=0;
								  $buyer_today_print_rec=0;
								  $buyer_total_print_rec=0;
								  $buyer_print_rec_balance=0;
								  $buyer_priv_deliv_cut=0;
								  $buyer_today_deliv_cut=0;
								  $buyer_total_delivery_cut=0;
								  $buyer_deliv_cut_bal=0;
								  $buyer_priv_sew=0;
								  $buyer_today_sew=$buyer_priv_sew_out=0;
								  $buyer_total_sew_=$buyer_total_sew_out_today=0;
								  $buyer_total_sew_out_reject_today=$buyer_total_sew_out_reject_bal=$buyer_total_sewingout_wip_qty=0;
								  $buyer_total_poly_qnty_pre=$buyer_total_poly_qnty_today=$buyer_total_poly_qnty=$buyer_total_ploy_rej_qty_pre=$buyer_total_ploy_rej_qty_today=$buyer_total_ploy_rej_qty=$buyer_total_tot_poly_wip=0;
								  $buyer_total_fin_qnty_pre=$buyer_total_fin_qnty_today=$buyer_total_fin_qnty=$buyer_total_fin_rej_qty_pre=$buyer_total_fin_rej_qty_today=$buyer_total_fin_rej_qty=$buyer_total_tot_fin_wip=0;
								  $buyer_total_exf_qty_pre=$buyer_total_exf__qty_today=$buyer_total_exf_tot_qty=$buyer_total_tot_exf_wip=0;
								  $buyer_total_sew__bal=0;
								  $buyer_inhand=0;
								  $buyer_priv_print_iss=0;
								  $buyer_today_print_iss=0;
								  $buyer_total_print_iss=0;
								  $buyer_priv_embl_iss=0;
								  $buyer_priv_print_rec=0;
								  $buyer_today_embl_iss=0;
								  $buyer_total_embl_iss=0;
								  $buyer_today_wash_iss=0;
								  $buyer_priv_wash_iss=0;
								  $buyer_today_sp_iss=0;
								  $buyer_total_wash_iss=0;
								  $buyer_total_sp_iss=0;
								  $buyer_priv_sp_iss=0;
								  $buyer_priv_print_rec=0;
								  $buyer_today_print_rec=0;
								  $buyer_total_print_rec=0;
								  $buyer_priv_wash_rec=0;
								  $buyer_today_wash_rec=0;
								  $buyer_total_wash_rec=0;
								  $buyer_priv_embl_rec=0;
								  $buyer_today_embl_rec=0;
								  $buyer_total_embl_rec=0;
								  $buyer_priv_sp_rec=0;
								  $buyer_today_sp_rec=0;
								  $buyer_total_sp_rec=0;
							}
					  }
					//***********************for line****************************************************************************************
					$line_id_all=$sew_line_arr[$po_id]['line'];
					$line_name="";
					foreach(array_unique(explode(",",$line_id_all)) as $l_id)
					{
						if($line_name!="") $line_name.=",";
						if($prod_reso_allo==1)
						{
						$line_name.= $lineArr[$prod_reso_arr[$l_id]];
						}
						else
						{
						$line_name.= $lineArr[$l_id];
						}
					}

					$floor_id_all=$sew_line_arr[$po_id]['floor_id'];
					$floor_name="";
					foreach(array_unique(explode(",",$floor_id_all)) as $f_id)
					{
						if($floor_name!="") $floor_name.=",";
						$floor_name.= $floor_arr[$f_id];
					}

					$costing_per=$costing_per_arr[$po_number_data[$po_id][$item_id][$color_id]['job_no']];
					if($costing_per==1)
					{
						$costing_per_qty=12;
					}
					else if($costing_per==2)
					{
						$costing_per_qty=1;
					}
					else if($costing_per==3)
					{
						$costing_per_qty=24;
					}
					else if($costing_per==4)
					{
						$costing_per_qty=36;
					}
					else if($costing_per==5)
					{
						$costing_per_qty=48;
					}

					//$fabric_wip=$fabric_wip[$po_id][$color_id]['issue']-$fabric_wip[$po_id][$color_id]['receive'];
					$ready_to_sewing=$ready_to_sewing_arr[$po_id][$item_id][$color_id];
				    $fabric_pre=$fabric_pre_qty[$po_id][$color_id];
					$fabric_today=$fabric_today_qty[$po_id][$color_id];
					$total_fabric=$fabric_pre+$fabric_today;
					$possible_cut_qty=($total_fabric/($con_per_dzn[$po_id][$color_id]/$costing_per_qty));
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$fabric_qty=($plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty']*$con_per_dzn[$po_id][$color_id])/$costing_per_qty;
					// $fabric_qty=$po_number_data[$po_id][$item_id][$color_id]['plan_qty']*($con_per_dzn[$po_id][$color_id]/$costing_per_qty);
					//echo $po_number_data[$po_id][$item_id][$color_id]['plan_qty'].'=='.$con_per_dzn[$po_id][$color_id].'=='.$costing_per_qty.', ';
				    $fabric_balance=$fabric_qty-$total_fabric;
					$total_cut=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$cutting_balance=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'] - $total_cut;
					$total_print_iss=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre']+$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$total_embl_iss=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre']+$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$total_embl_rec=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre']+$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$total_print_receive=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];

					//echo $inhand."***";die;
					$print_balance=$total_print_iss-$total_print_receive;
					$embl_balance=$total_embl_iss-$total_embl_rec;
					$wash_balance=$total_wash_iss-$total_wash_receive;
					$sp_balance=$total_sp_iss-$total_sp_rec;
					$total_emblishment_iss=$total_embl_iss+$total_wash_iss+$total_print_iss+$total_sp_iss;
					//$total_emblishment_rec=$total_wash_receive+$total_embl_rec+$total_sp_rec+$total_print_rec;
					$print_issue_balance=$total_emblishment_iss-$total_cut;
					$print_rec_balance=$total_emblishment_rec-$total_emblishment_iss;
					$total_delivery_cut=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$deliv_cut_bal=($total_cut)-$total_delivery_cut;
					$total_sew_input=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$total_sew_input_balance=$plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty']-$total_sew_input;
					$input_percentage=($total_sew_input/$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'])*100;
					// $input_percentage plan cut qty change to po qty by saeed vie;
					$sewingout_qnty_pre=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_pre'];
					$sewingout_qnty_today=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_today'];

					$sewout_rej_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['sewout_rej_qty_today'];
					$sewout_rej_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['sewout_rej_qty_pre'];

					$fin_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['fin_qty_pre'];
					$fin_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['fin_qty_today'];
					$fin_rej_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['fin_rej_qty_pre'];
					$fin_rej_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['fin_rej_qty_today'];

					$poly_qnty_pre=$production_data_arr[$po_id][$item_id][$color_id]['poly_qnty_pre'];
					$poly_qnty_today=$production_data_arr[$po_id][$item_id][$color_id]['poly_qnty_today'];
					$ploy_rej_qty_pre=$production_data_arr[$po_id][$item_id][$color_id]['ploy_rej_qty_pre'];
					$ploy_rej_qty_today=$production_data_arr[$po_id][$item_id][$color_id]['ploy_rej_qty_today'];

					$total_poly_qnty=$poly_qnty_pre+$poly_qnty_today;
					$total_ploy_rej_qty=$ploy_rej_qty_pre+$ploy_rej_qty_today;


					$total_sewingout_qnty_bal=$sewingout_qnty_pre+$sewingout_qnty_today;
					$total_sewingout_reject_qty_bal=$sewout_rej_qty_today+$sewout_rej_qty_pre;
					$total_sewingout_wip_qty=$total_sewingout_qnty_bal-$total_sew_input;
					$tot_poly_wip=(($total_poly_qnty+$total_ploy_rej_qty)-$total_sewingout_qnty_bal);

					$fin_qty_pre=$fin_qty_pre;
					$fin_qty_today=$fin_qty_today;
					$fin_rej_qty_today=$fin_rej_qty_today;
					$fin_rej_qty_pre=$fin_rej_qty_pre;

					$tot_fin_qnty=$fin_qty_pre+$fin_qty_today;
					$tot_fin_reject_qty=$fin_rej_qty_today+$fin_rej_qty_pre;
					$tot_fin_wip=(($tot_fin_qnty+$tot_fin_reject_qty)-$total_poly_qnty);
					if($ex_factory_level==1) // when gross level
					{
						$exf_qnty_today=$production_data[$po_id][$item_id]['exf_qnty_today'];
						$exf_qnty_pre=$production_data[$po_id][$item_id]['exf_qnty_pre'];
					}
					else
					{
						$exf_qnty_today=$production_data[$po_id][$item_id][$color_id]['exf_qnty_today'];
						$exf_qnty_pre=$production_data[$po_id][$item_id][$color_id]['exf_qnty_pre'];
					}

					//echo $exf_qnty_today.'='.$exf_qnty_pre;
					$tot_exf_qnty=$exf_qnty_today+$exf_qnty_pre;
					$ex_fact_wip=($tot_exf_qnty-$tot_fin_qnty);



					$cutting_inhand=0;
					$cutting_inhand=$total_cut-$total_delivery_cut;
					$inhand=0;
					if($total_print_iss!=0 && $total_embl_iss!=0)
					{
						if(date("Y-m-d",strtotime($production_data_arr[$po_id][$item_id][$color_id]['min_printin_date']))<=date("Y-m-d",strtotime($production_data_arr[$po_id][$item_id][$color_id]['min_embl_date'])))
					    {
				        $inhand=($total_delivery_cut+$total_embl_rec)-($total_sew_input+$total_print_iss);
						}
						else
						{
						$inhand=($total_delivery_cut+$total_embl_iss)-($total_sew_input+$total_print_receive);
						}
					}
					else if ($total_print_iss!=0) { $inhand=(($total_delivery_cut+$total_print_receive)-($total_print_iss+$total_sew_input));}
					else if ($total_embl_iss!=0) { $inhand=($total_delivery_cut+$total_embl_rec)-($total_embl_iss+$total_sew_input);}
					else if($total_delivery_cut!=0)$inhand=$total_delivery_cut-$total_sew_input;
					else $inhand=0;  // $inhand=$total_cut-$total_sew_input;
					//if($inhand<0) $inhand=0;


					//for job total *******************************************************************************************************
					$job_possible_cut_qty+=$possible_cut_qty;
					$job_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$job_total_plan+=$po_number_data[$po_id][$item_id][$color_id]['plan_qty'];
					$job_total_fabric_qty+=$fabric_qty;
					$job_total_fabric_pre+=$fabric_pre;
					$job_fabric_today_total+=$fabric_today;
					$job_fabric_total+=$total_fabric;
					$job_fabric_bal+=$fabric_balance;
					$job_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$job_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$job_total_cut+=$total_cut;
					$job_cutting_balance+=$cutting_balance;
					$job_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$job_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$job_total_print_iss+=$total_print_iss;
					$job_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$job_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$job_total_embl_iss+=$total_embl_iss;
					$job_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$job_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$job_total_wash_iss+=$total_wash_iss;
					$job_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$job_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$job_total_sp_iss+=$total_sp_iss;
					$job_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$job_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$job_total_print_rec+=$total_print_receive;
					$job_print_issue_balance=$print_issue_balance;
					$job_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$job_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$job_total_wash_rec+=$total_wash_receive;
					$job_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$job_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$job_total_embl_rec+=$total_embl_rec;
					$job_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$job_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$job_total_sp_rec+=$total_sp_rec;
					$job_print_rec_balance+=$print_rec_balance;
					$job_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$job_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$job_total_delivery_cut+=$total_delivery_cut;
					$job_deliv_cut_bal+=$deliv_cut_bal;
					$job_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$job_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$job_priv_sewout+=$sewingout_qnty_pre;
					$job_today_sewout+=$sewingout_qnty_today;
					$job_priv_sew_input+=$total_sew_input;
					$job_total_sew_bal+=$total_sew_input_balance;
					$job_total_sew_out_bal+=$total_sewingout_qnty_bal;
					$job_total_sew_out_reject_bal+=$total_sewingout_reject_qty_bal;
					$job_total_sew_out_reject_today+=$sewout_rej_qty_today;
					$job_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$job_total_poly_qnty_pre+=$poly_qnty_pre;
					$job_total_poly_qnty_today+=$poly_qnty_today;
					$job_total_poly_qnty+=$total_poly_qnty;

					$job_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;
					$job_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$job_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$job_total_tot_poly_wip+=$tot_poly_wip;
					//Fin and Pack
					$job_total_fin_qnty_pre+=$fin_qty_pre;
					$job_total_fin_qnty_today+=$fin_qty_today;
					$job_total_fin_qnty+=$tot_fin_qnty;

					$job_total_fin_rej_qty_pre+=$fin_rej_qty_pre;
					$job_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$job_total_fin_rej_qty+=$tot_fin_reject_qty;
					$job_total_tot_fin_wip+=$tot_fin_wip;

					$job_total_exf_qty_pre+=$exf_qnty_pre;
					$job_total_exf_qty_today+=$exf_qnty_today;
					$job_total_exf_tot_qty+=$tot_exf_qnty;
					$job_total_tot_exf_wip+=$ex_fact_wip;


					$job_inhand+=$inhand;
					$job_cutinhand+=$cutting_inhand;
					$job_ready_to_sewing+=$ready_to_sewing;
					//buyer sub total **************************************************************************************************
					$buyer_possible_cut_qty+=$possible_cut_qty;
					$buyer_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$buyer_total_plan+=$po_number_data[$po_id][$item_id][$color_id]['plan_qty'];
					$buyer_total_fabric_qty+=$fabric_qty;
					$buyer_total_fabric_pre+=$fabric_pre;
					$buyer_fabric_today_total+=$fabric_today;
					$buyer_fabric_total+=$total_fabric;
					$buyer_fabric_bal+=$fabric_balance;
					$buyer_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$buyer_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$buyer_total_cut+=$total_cut;
					$buyer_cutting_balance+=$cutting_balance;
					$buyer_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$buyer_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$buyer_total_print_iss+=$total_print_iss;
					$buyer_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$buyer_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$buyer_total_embl_iss+=$total_embl_iss;
					$buyer_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$buyer_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$buyer_total_wash_iss+=$total_wash_iss;
					$buyer_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$buyer_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$buyer_total_sp_iss+=$total_sp_iss;
					$buyer_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$buyer_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$buyer_total_print_rec+=$total_print_receive;
					$buyer_print_issue_balance=$print_issue_balance;
					$buyer_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$buyer_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$buyer_total_wash_rec+=$total_wash_receive;
					$buyer_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$buyer_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$buyer_total_embl_rec+=$total_embl_rec;
					$buyer_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$buyer_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$buyer_total_sp_rec+=$total_sp_rec;
					$buyer_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$buyer_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$buyer_total_delivery_cut+=$total_delivery_cut;
					$buyer_deliv_cut_bal+=$deliv_cut_bal;
					$buyer_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$buyer_priv_sew_out+=$production_data_arr[$po_id][$item_id][$color_id]['sewingout_qnty_pre'];
					$buyer_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$buyer_total_sew_+=$total_sew_input;
					$buyer_total_sew_bal+=$total_sew_input_balance;
					$buyer_total_sew_out_pre+=$sewingout_qnty_pre;
					$buyer_total_sew_out_today+=$sewingout_qnty_today;
					$buyer_total_sew_out_bal+=$total_sewingout_qnty_bal;
					$buyer_total_sew_out_reject_today+=$sewout_rej_qty_today;
					$buyer_total_sew_out_reject_bal+=$total_sewingout_reject_qty_bal;
					$buyer_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$buyer_total_poly_qnty_pre+=$poly_qnty_pre;
					$buyer_total_poly_qnty_today+=$poly_qnty_today;
					$buyer_total_poly_qnty+=$total_poly_qnty;
					$buyer_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;

					$buyer_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$buyer_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$buyer_total_tot_poly_wip+=$tot_poly_wip;

					// Fin & Pack
					$buyer_total_fin_qnty_pre+=$fin_qty_pre;
					$buyer_total_fin_qnty_today+=$fin_qty_today;
					$buyer_total_fin_qnty+=$tot_fin_qnty;
					$buyer_total_fin_rej_qty_pre+=$fin_rej_qty_pre;

					$buyer_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$buyer_total_fin_rej_qty+=$tot_fin_reject_qty;
					$buyer_total_tot_fin_wip+=$tot_fin_wip;

					$buyer_total_exf_qty_pre+=$exf_qnty_pre;
					$buyer_total_exf__qty_today+=$exf_qnty_today;
					$buyer_total_exf_tot_qty+=$tot_exf_qnty;
					$buyer_total_tot_exf_wip+=$ex_fact_wip;

					$buyer_inhand+=$inhand;
					$buyer_cutinhand+=$cutting_inhand;
					$buyer_ready_to_sewing+=$ready_to_sewing;
					// for grand total ********************************************************************************************************************
					$grand_possible_cut_qty+=$possible_cut_qty;
					$grand_total_order+=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity'];
					$grand_total_plan+=$po_number_data[$po_id][$item_id][$color_id]['plan_qty'];
					$grand_total_fabric_qty+=$fabric_qty;
					$grand_total_fabric_pre+=$fabric_pre;
					$grand_fabric_today_total+=$fabric_today;
					$grand_fabric_total+=$total_fabric;
					$grand_fabric_bal+=$fabric_balance;
					$grand_pre_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
					$grand_today_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty'];
					$grand_total_cut+=$total_cut;
					$grand_cutting_balance+=$cutting_balance;
					$grand_priv_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre'];
					$grand_today_print_iss+=$production_data_arr[$po_id][$item_id][$color_id]['printing_qnty'];
					$grand_total_print_iss+=$total_print_iss;
					$grand_priv_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty_pre'];
					$grand_today_embl_iss+=$production_data_arr[$po_id][$item_id][$color_id]['embl_qnty'];
					$grand_total_embl_iss+=$total_embl_iss;
					$grand_today_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty'];
					$grand_priv_wash_iss+=$production_data_arr[$po_id][$item_id][$color_id]['wash_qnty_pre'];
					$grand_total_wash_iss+=$total_wash_iss;
					$grand_priv_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty_pre'];
					$grand_today_sp_iss+=$production_data_arr[$po_id][$item_id][$color_id]['sp_qnty'];
					$grand_total_sp_iss+=$total_sp_iss;
					$grand_priv_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre'];
					$grand_today_print_rec+=$production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty'];
					$grand_total_print_rec+=$total_print_receive;
					$grand_print_issue_balance=$print_issue_balance;
					$grand_priv_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty_pre'];
					$grand_today_wash_rec+=$production_data_arr[$po_id][$item_id][$color_id]['washreceived_qnty'];
					$grand_total_wash_rec+=$total_wash_receive;
					$grand_priv_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty_pre'];
					$grand_today_sp_rec+=$production_data_arr[$po_id][$item_id][$color_id]['sp_received_qnty'];
					$grand_total_embl_rec+=$total_embl_rec;
					$grand_priv_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre'];
					$grand_today_embl_rec+=$production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty'];
					$grand_total_sp_rec+=$total_sp_rec;
					$grand_priv_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre'];
					$grand_today_deliv_cut+=$production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty'];
					$grand_total_delivery_cut+=$total_delivery_cut;
					$grand_deliv_cut_bal+=$deliv_cut_bal;
					$grand_priv_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre'];
					$grand_today_sew+=$production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty'];
					$grand_total_sew_+=$total_sew_input;
					$grand_total_sewingout_qnty_pre+=$sewingout_qnty_pre;
					$grand_total_sewingout_qnty_today+=$sewingout_qnty_today;
					$grand_total_sewingout_qnty_bal+=$total_sewingout_qnty_bal;
					$grand_total_sewingout_qnty_reject_today+=$sewout_rej_qty_today;
					$grand_total_sewingout_qnty_reject_bal+=$total_sewingout_reject_qty_bal;
					$grand_total_sewingout_qnty_reject_today+=$sewout_rej_qty_today;
					$grand_total_sew_bal+=$total_sew_input_balance;
					$grand_total_sewingout_wip_qty+=$total_sewingout_wip_qty;

					$grand_total_poly_qnty_pre+=$poly_qnty_pre;
					$grand_total_poly_qnty_today+=$poly_qnty_today;
					$grand_total_poly_qnty+=$total_poly_qnty;
					$grand_total_ploy_rej_qty_pre+=$ploy_rej_qty_pre;
					$grand_total_ploy_rej_qty_today+=$ploy_rej_qty_today;
					$grand_total_ploy_rej_qty+=$total_ploy_rej_qty;
					$grand_total_tot_poly_wip+=$tot_poly_wip;

					//Fin & Pack

					$grand_total_fin_qnty_pre+=$fin_qty_pre;
					$grand_total_fin_qnty_today+=$fin_qty_today;
					$grand_total_fin_qnty+=$tot_fin_qnty;
					$grand_total_fin_rej_qty_pre+=$fin_rej_qty_pre;
					$grand_total_fin_rej_qty_today+=$fin_rej_qty_today;
					$grand_total_fin_rej_qty+=$tot_fin_reject_qty;
					$grand_total_tot_fin_wip+=$tot_fin_wip;

					$grand_total_exf_qty_pre+=$exf_qnty_pre;
					$grand_total_exf_qty_today+=$exf_qnty_today;
					$grand_total_exf_qty+=$tot_exf_qnty;
					$grand_total_tot_exf_wip+=$ex_fact_wip;

					$grand_inhand+=$inhand;
					$grand_cutinhand+=$cutting_inhand;
					$grand_ready_to_sewing+=$ready_to_sewing;

	                    ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
	                    <td width="40"><? echo $i; ?></td>
	                    <td width="80" style="word-break:break-all;"><p><? echo $buyer_short_library[$po_number_data[$po_id][$item_id][$color_id]['buyer_name']]; ?></p></td>
	                    <td width="60" align="center"><? echo $po_number_data[$po_id][$item_id][$color_id]['job_prifix'];?></td>
	                    <td width="50" align="right"><? echo $po_number_data[$po_id][$item_id][$color_id]['year'];?></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['po_number'];?></p></td>
	                    <td width="80" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['file_no'];?></p></td>
	                    <td width="80" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['int_ref_no'];?></p></td>
	                    <td width="80" align="center"><?  echo  change_date_format($tna_date_arr[$po_id]['tna_start']);  ?></td>
	                    <td width="80" align="center"><?  echo  change_date_format($tna_date_arr[$po_id]['tna_end']);  ?></td>
	                    <td width="80" align="center"><?  echo  change_date_format($po_min_shipdate_data[$po_id][$item_id][$color_id]['minimum_shipdate']);  ?></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $po_number_data[$po_id][$item_id][$color_id]['style']; ?></p></td>
	                    <td width="120" align="center" style="word-break:break-all;"><p><? echo $garments_item[$item_id]; ?></p></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $colorname_arr[$po_number_data[$po_id][$item_id][$color_id]['color_id']]; ?></p></td>
	                    <td width="70" align="right"><?  echo $order_qnty=$order_qnty_array[$po_id][$item_id][$color_id]['po_quantity']; ?></td>
	                    <td width="70" align="right"><?  echo $plan_cut_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty']; ?></td>
	                    <td width="70" align="right"><?  echo number_format(($con_per_dzn[$po_id][$color_id]*12/$costing_per_qty),3); ?></td>
	                    <td width="70" align="right"  title="consumption per pcs * Plan Cut Qty"><?  echo number_format($fabric_qty,2); ?></td>
	                    <td width="60" align="right"><?  echo number_format($fabric_pre,2); ?></td>
	                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,1)"><?  echo number_format($fabric_today,2); ?></a></td>
	                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,2)"><?  echo number_format($total_fabric,2); ?></a><?  //echo number_format($total_fabric,2);?></td>
	                    <td width="60" align="right"><?  echo number_format($fabric_balance,2); ?></td>

	                    <td width="70" align="right" title="Possible Cut Qty. =Total Fabric Receive/ Consumption Per Pcs."><?  echo  number_format($possible_cut_qty,0); ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre']; //$production_data_arr[$row_fab[csf('po_breakdown_id')]][$row_fab[csf('color_id')]]['cutting_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',950,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"> <?  echo $production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',950,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_cut; ?></a></td>
	                    <td width="60" align="right" title="(Total Cut - Order qty)*100/Total Cut"><? $cut_persent=(($total_cut - $order_qnty)*100) / $total_cut; echo number_format($cut_persent,2); ?></td>
	                    <td width="60" align="right"><?  echo $cutting_balance; ?></td>
	                    <td width="60" align="right" title="Cutting WIP=Possible Cut Qty - Total Cutting Qty"><?  echo number_format($possible_cut_qty-$total_cut,0); ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_delivery_cut; ?></a></td>
	                    <td width="60" align="right"><?  echo $deliv_cut_bal; ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printing_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printing_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_print_iss; ?></a></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['printreceived_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$color_id]['printreceived_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_print_receive; ?></a></td>
	                    <td width="60" align="right" title="Print WIP=Delivery to Print-Receive from Print"><?  echo $print_balance; ?></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$color_id]['embl_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['embl_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_embl_iss; ?></a></td>
	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['emblreceived_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,3,'emblishment_popup',850,350,2,'<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_embl_rec; ?></a></td>
	                    <td width="60" align="right" title="Embl. WIP=Delivery to Embl.-Receive from Embl."><?  echo $embl_balance; ?></td>

	                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty_pre']; ?></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty']; ?></a></td>
	                    <td width="60" align="right"><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"> <? echo $total_sew_input; ?></a></td>
	                    <td width="60" align="right" title="(Total Sewing Input*100)/Order Qty"><?  echo number_format($input_percentage,2); ?></td>
	                    <td width="60" align="right"><?  echo $total_sew_input_balance; ?></td>
	                    <td width="70" align="right" title="Inhand=Total Cut - Delivery to Input" ><?  echo $cutting_inhand; ?></td>
	                     <td width="70" align="right" title="Inhand= Cutting Delivery To Input  - Delivery to  Embellishment &#13; + Receive from Embellishment -Sewing Input" ><?  echo $inhand; ?></td>
	                    <td width="100" align="right"><p><? echo $ready_to_sewing; ?></p></td>
	                    <td width="100" align="center"><p><? echo $line_name; ?> </p></td>

	                    <td width="60" align="center"><p><? echo $floor_name; ?></p></td>
	                    <td width="80" align="right"><p><? $fin_cons=0; $cutable_pcs=0; $fin_cons=number_format(($con_per_dzn[$po_id][$color_id]*12/$costing_per_qty),3); $cutable_pcs=($total_fabric/$fin_cons)*12; echo number_format($cutable_pcs,2); ?></p></td>
	                    <td width="80" align="right"><p><? $cutting_kg=0; $cutting_kg=($fin_cons/12)*$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']; echo number_format($cutting_kg,2); ?></p></td>
	                    <td width="80" align="right"><p><? $cutting_bal=0; $cutting_bal=$total_fabric-$cutting_kg; echo number_format($cutting_bal,2); ?></p></td>

	                     <td width="70" align="right"><p><? echo number_format($sewingout_qnty_pre,2); ?></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,5,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><? echo number_format($sewingout_qnty_today,2); ?></a></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,5,'cutting_and_sewing_popup',1000,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($total_sewingout_qnty_bal,2); ?></a></p></td>
	                     <td width="70" align="right"><p><?  echo number_format($sewout_rej_qty_today,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($total_sewingout_reject_qty_bal,2); ?></p></td>
	                     <td width="70" align="right"><p><?  echo number_format($total_sewingout_wip_qty,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($poly_qnty_pre,2); ?></p></td>

	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,7,'iron_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo number_format($poly_qnty_today,2); ?></a></p></td>

	                      <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,7,'iron_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($total_poly_qnty,2); ?></a></p></td>

	                     <td width="70" align="right"><p><?  echo number_format($ploy_rej_qty_today,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($total_ploy_rej_qty,2); ?></p></td>
	                     <td width="70" align="right"><p><?  echo number_format($tot_poly_wip,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($fin_qty_pre,2); ?></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,8,'packing_and_finishing_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><? echo number_format($fin_qty_today,2); ?></a></p></td>
	                     <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,8,'packing_and_finishing_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($tot_fin_qnty,2); ?></a></p></td>
	                     <td width="70" align="right"><p><? echo number_format($fin_rej_qty_today,2); ?></p></td>
	                      <td width="70" align="right"><p><?  echo number_format($tot_fin_reject_qty,2); ?></p></td>
	                     <td width="70" align="right"><p><? echo number_format($tot_fin_wip,2); ?></p></td>


	                    <?
	                    if($ex_factory_level==1)
	                    {
	                     	if($r==0)
	                     	{
		                     	?>

	                     		<td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><?  echo number_format($exf_qnty_pre,2); ?></p></td>
			                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><a href="##" onclick="javascript:alert('It is gross level production, so popup not found.');"><?  echo number_format($exf_qnty_today,2); ?></a></p></td>
			                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><a href="##" onclick="javascript:alert('It is gross level production, so popup not found.');"><? echo number_format($tot_exf_qnty,2); ?></a></p></td>

			                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id][$item_id]; ?>" width="70" align="right"><p><?  echo number_format($ex_fact_wip,2); ?></p></td>
		                     	<?
		                     	$r++;
	                     	}
	                 	}
	                    else
	                    {
	                     	?>
		                    <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,'','ex_factory_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo number_format($exf_qnty_today,2); ?></a></p></td>
		                    <td width="70" align="right"><p><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,'','ex_factory_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><? echo number_format($tot_exf_qnty,2); ?></a></p></td>

		                    <td width="70" align="right"><p><?  echo number_format($ex_fact_wip,2); ?></p></td>

	                    	<?
		                }
		                ?>




	                    <td  align="center"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_remarks',650,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>','')">
						<?
						   if($total_delivery_cut-$po_number_data[$po_id][$color_id][$item_id]['plan_qty']>0) echo "Receive Ok";
						   else  if($total_sew_input_balance-$po_number_data[$po_id][$item_id][$color_id]['plan_qty']>0) echo "Input Ok";
						   else echo "Remarks";

						?></a></td>
	        	  </tr>
							<?
					 $job_arr[]=$po_number_data[$po_id][$item_id][$color_id]['job_no'];
					 $buyer_arr[]=$po_number_data[$po_id][$item_id][$color_id]['buyer_name'];
					 $i++;
	                } //end foreach 2nd
				}

	 		 }

				?>
	                         <tr bgcolor="#CCCCCC" id="">
	                                <td width="40"><? // echo $i;?></td>
	                                <td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
	                                <td width="60"></td>
	                                <td width="50"></td>
	                                <td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="80"></td>
	                                <td width="100"><strong></strong></td>
	                                <td width="120"><strong></strong></td>
	                                <td width="100" align="right"><strong>Job Total:</strong></td>
	                                <td width="70" align="right"><? echo $job_total_order; ?></td>
	                                <td width="70" align="right"><?  echo $job_total_plan; ?></td>
	                                <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
	                                <td width="70" align="right"><?  echo number_format($job_total_fabric_qty,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_total_fabric_pre,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_today_total,2); ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_total,2);?></td>
	                                <td width="60" align="right"><?  echo number_format($job_fabric_bal,2); ?></td>
	                                <td width="70" align="right"><?  echo number_format($job_possible_cut_qty,0); ?></td>
	                                <td width="60" align="right"><?  echo $job_pre_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_cut; ?></td>
	                                <td width="60" align="right"></td>
	                                <td width="60" align="right"><?  echo $job_cutting_balance; ?></td>
	                                <td width="60" align="right"><?  echo number_format($job_possible_cut_qty-$job_total_cut,0); ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_deliv_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_deliv_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_delivery_cut; ?></td>
	                                <td width="60" align="right"><?  echo $job_deliv_cut_bal; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_print_iss-$job_total_print_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_iss; ?></td>
	                                <td width="60" align="right"><?  echo $job_priv_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_rec; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_embl_iss-$job_total_embl_rec; ?></td>

	                                <td width="60" align="right"><?  echo $job_priv_sew; ?></td>
	                                <td width="60" align="right"><?  echo $job_today_sew; ?></td>
	                                <td width="60" align="right"> <? echo $job_total_sew_input; ?></td>
	                                <td width="60" align="right"><? //echo $input_percentage; ?></td>
	                                <td width="60" align="right"><?  echo $job_total_sew__bal; ?></td>
	                                <td width="70" align="right"><? echo $job_cutinhand; ?></td>
	                                <td width="70" align="right"><? echo $job_inhand; ?></td>
	                                <td width="100" align="right"><? echo $job_ready_to_sewing; ?></td>

	                                <td width="100" align="right"><? //echo  $sewing_line ?></td>

	                                <td width="60" align="right"><? //echo  $unit ?></td>
	                                <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                                <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                                <td width="80" align="right"><? //echo  $cutting_bal ?></td>

	                                <td width="70" align="right"><? echo  $job_priv_sewout; ?></td>
	                                <td width="70" align="right"><? echo  $job_today_sewout; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sew_out_bal; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sew_out_reject_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sew_out_reject_bal; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_sewingout_wip_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_poly_qnty_pre; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_poly_qnty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_poly_qnty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_ploy_rej_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_tot_poly_wip; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_qnty_pre; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_qnty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_qnty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_rej_qty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_fin_rej_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_tot_fin_wip; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_exf_qty_pre; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_exf_qty_today; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_exf_tot_qty; ?></td>
	                                <td width="70" align="right"><? echo  $job_total_tot_exf_wip; ?></td>
	                                <td width="70" align="right"><? //echo  $cutting_bal; ?></td>



	                                <td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
	                          </tr>
	                           <tr bgcolor="#999999" style=" height:15px">
										<td width="40"><? // echo $i;?></td>
										<td width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
										<td width="60"></td>
										<td width="50"></td>
										<td width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></td>
										<td width="80"></td>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
										<td width="80"></td>
	                                    <td width="80"></td>
										<td width="100"><strong> </strong></td>
	                                    <td width="120"><strong> </strong></td>
										<td width="100" align="right"><strong>Buyer Total:</strong></td>
										<td width="70" align="right"><? echo $buyer_total_order; ?></td>
										<td width="70" align="right"><?  echo $buyer_total_plan; ?></td>
	                                    <td width="70" align="right"><? // echo number_format($fabric_qty,2); ?></td>
										<td width="70" align="right"><?  echo number_format($buyer_total_fabric_qty,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_total_fabric_pre,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_today_total,2); ?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_total,2);?></td>
										<td width="60" align="right"><?  echo number_format($buyer_fabric_bal,2); ?></td>

	                                    <td width="70" align="right"><?  echo number_format($buyer_possible_cut_qty,0); ?></td>
										<td width="60" align="right"><?  echo $buyer_pre_cut; ?></td>
										<td width="60" align="right"><?  echo $buyer_today_cut; ?></td>
										<td width="60" align="right"><?  echo $buyer_total_cut; ?></td>
										<td width="60" align="right"></td>
										<td width="60" align="right"><?  echo $buyer_cutting_balance; ?></td>
	                                    <td width="60" align="right"><?  echo number_format($buyer_possible_cut_qty-$buyer_total_cut,0); ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_deliv_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_deliv_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_delivery_cut; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_deliv_cut_bal; ?></td>

	                                    <td width="60" align="right"><?  echo $buyer_priv_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_print_iss-$buyer_total_print_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_iss; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_priv_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_rec; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_embl_iss-$buyer_total_embl_rec; ?></td>


	                                    <td width="60" align="right"><?  echo $buyer_priv_sew; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_today_sew; ?></td>
	                                    <td width="60" align="right"> <? echo $buyer_total_sew_; ?></td>
	                                    <td width="60" align="right"><? //echo $input_percentage; ?></td>
	                                    <td width="60" align="right"><?  echo $buyer_total_sew_bal; ?></td>
	                                    <td width="70" align="right"><? echo $buyer_cutinhand; ?></td>
	                                    <td width="70" align="right"><? echo $buyer_inhand; ?></td>
	                                    <td width="100" align="right"><? echo $buyer_ready_to_sewing; ?></td>

	                                    <td width="100" align="right"><? //echo  $sewing_line ?></td>
	                                    <td width="60" align="right"><? //echo  $unit ?></td>
	                                    <td width="80" align="right"><? //echo  $cutble_pcs ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_kg ?></td>
	                                    <td width="80" align="right"><? //echo  $cutting_bal ?></td>

	                                    <td width="70" align="right"><? echo  $buyer_priv_sew_out; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_bal; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sew_out_reject_bal; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_sewingout_wip_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_poly_qnty_pre; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_poly_qnty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_poly_qnty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_ploy_rej_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_tot_poly_wip; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_qnty_pre; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_qnty_today;?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_qnty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_fin_rej_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_tot_fin_wip; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_exf_qty_pre; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_exf__qty_today; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_exf_tot_qty; ?></td>
	                                    <td width="70" align="right"><? echo  $buyer_total_tot_exf_wip; ?></td>

										<td  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></td>
								  </tr>
	                            <tfoot>
	                                 <tr>
	                                    <th width="40"><? // echo $i;?></th>
	                                    <th width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
	                                    <th width="60"></td>
	                                    <th width="50"></td>
	                                    <th width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
	                                    <th width="80"></th>
	                                    <td width="80"></td>
	                                    <td width="80"></td>
	                                    <th width="80"></th>
	                                    <th width="80"></th>
	                                    <th width="100"> <strong></strong></th>
	                                    <th width="120"> <strong></strong></th>
	                                    <th width="100" align="right"><strong>Grand Total:</strong></th>
	                                    <th width="70" align="right"><? echo $grand_total_order; ?></th>
	                                    <th width="70" align="right"><?  echo $grand_total_plan; ?></th>
	                                    <th width="70" align="right"><? // echo $grand_total_plan; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_total_fabric_qty,2); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_total_fabric_pre,2); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_fabric_today_total,2); ?></th>
	                                    <th width="60" align="right"><?  echo number_format($grand_fabric_total,2);?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_fabric_bal,2); ?></th>

	                                    <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_possible_cut_qty,0); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_pre_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_cutting_balance; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo number_format($grand_possible_cut_qty-$grand_total_cut,0); ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_deliv_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_deliv_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_delivery_cut; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_deliv_cut_bal; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_print_iss-$grand_total_print_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_iss; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_rec; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_embl_iss-$grand_total_embl_rec; ?></th>


	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_priv_sew; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_today_sew; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><? echo $grand_total_sew_; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><? //echo $input_percentage; ?></th>
	                                    <th width="60" align="right" style="word-break:break-all;"><?  echo $grand_total_sew_bal; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><? echo $grand_cutinhand; ?></th>
	                                    <th width="70" align="right" style="word-break:break-all;"><? echo $grand_inhand; ?></th>
	                                    <th width="100" align="right" style="word-break:break-all;"><? echo $grand_ready_to_sewing; ?></th>

	                                    <th width="100" align="right"><? //echo  $sewing_line ?></th>

	                                    <th width="60" align="right"><? //echo  $unit ?></th>
	                                    <th width="80" align="right"><? //echo  $cutble_pcs ?></th>
	                                    <th width="80" align="right"><? //echo  $cutting_kg ?></th>
	                                    <th width="80" align="right"><? //echo  $cutting_bal ?></th>

	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_today ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_reject_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_reject_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_qnty_reject_bal; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_sewingout_wip_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_poly_qnty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_poly_qnty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_poly_qnty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_ploy_rej_qty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_ploy_rej_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_poly_wip; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_qnty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_qnty_today ;?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_qnty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_rej_qty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_fin_rej_qty; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_fin_wip; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_exf_qty_pre; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_exf_qty_today; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_exf_wip; ?></th>
	                                    <th width="70" align="right"><? echo  $grand_total_tot_exf_wip; ?></th>

	                                    <th  align="right"><? //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></th>
	                             </tr>
	  						</tfoot>

	                </table>
	           </div>
	  	</div>

	  	</fieldset>

		<?
	}
	else // ITEM WISE 2 (shafiq)
	{
		$country_ship_date = str_replace("d.country_ship_date", "c.country_ship_date", $country_ship_date);
		$location_name = str_replace("e.location", "d.location", $location_name);
		$floor_name = str_replace("e.floor_id", "d.floor_id", $floor_name);
		// ==================================== MAIN QUERY ==========================================
	 	$sql="SELECT  a.id,a.po_number,b.buyer_name,b.style_ref_no as style,c.color_number_id,d.floor_id from wo_po_break_down a,wo_po_details_master b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e where a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.id=d.po_break_down_id and d.id=e.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and b.status_active=1 and a.is_confirmed=1 and c.status_active=1 and e.status_active=1 and d.production_type in(5,8) and c.id=e.color_size_break_down_id $company_name $buyer_name $style_cond $order_cond $sql_cond $job_cond_id $country_ship_date $shipping_cond $location_name $floor_name $prod_date_cond";
	 	// echo $sql;die();
	 	$sql_res = sql_select($sql);
	 	if(count($sql_res)==0){echo "Data not available";die();}
	 	$main_array = array();
	 	$po_id_array = array();
	 	foreach ($sql_res as $val)
	 	{
	 		$po_id_array[$val[csf('id')]] = $val[csf('id')];
	 	}

	 	// =================== grouping po id ======================
	 	$all_po_id = implode(",", $po_id_array);
	 	if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( a.id in ($ids) ";
				else
					$po_cond.=" or a.id in ($ids) ";
			}
			$po_cond.=") ";
		}
		else
		{
			$po_cond.=" and a.id in ($all_po_id) ";
		}
		// echo $po_cond;

		ob_start();
		?>
		<style type="text/css">
			table tr th,table tr td{word-break: break-all;word-wrap: break-word;}
			table caption{ font-size: 14px;font-weight: bold; }
			.summary,.details{padding-bottom: 10px;}
		</style>
	    <fieldset style="width:1230px;">
	    	<div class="heading">
	    	    <table width="1210"  cellspacing="0">
	                <tr class="form_caption" style="border:none;">
	                    <td colspan="15" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report(Item Wise 2)</td>
	                </tr>
	                <tr style="border:none;">
	                    <td colspan="15" align="center" style="border:none; font-size:16px; font-weight:bold">
	                    Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
	                    </td>
	                </tr>
	                <tr style="border:none;">
	                    <td colspan="15" align="center" style="border:none;font-size:12px; font-weight:bold">
	                    <? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
	                    </td>
	                </tr>
	            </table>
        	</div>
        	<div class="summary">
        		<table cellspacing="0"  border="1" rules="all"  width="400" class="rpt_table">
        			<caption>Unit Wise Summary</caption>
        			<thead>
        				<tr>
        					<th width="30">Sl</th>
        					<th width="100">Unit</th>
        					<th width="90">Sewing Output</th>
        					<th width="90">Finishing</th>
        					<th width="90">Exfactory</th>
        				</tr>
        			</thead>
        			<tbody>
        				<tr>
        					<td align="left"></td>
        					<td align="left"></td>
        					<td align="right"></td>
        					<td align="right"></td>
        					<td align="right"></td>
        				</tr>
        			</tbody>
        			<tfoot>
        				<tr>
        					<th align="right" colspan="2">Total</th>
        					<th align="right"></th>
        					<th align="right"></th>
        					<th align="right"></th>
        				</tr>
        			</tfoot>
        		</table>
        	</div>
        	<div class="details">
	            <table cellspacing="0"  border="1" rules="all"  width="1210" class="rpt_table">
	            	<caption>Unit Wise Details</caption>
	                <thead>
	                	<tr>
	                        <th width="30" rowspan="2">SL</th>
	                        <th width="120" rowspan="2">Buyer</th>
	                        <th width="120" rowspan="2">Order No</th>
	                        <th width="120" rowspan="2">Style</th>
	                        <th width="120" rowspan="2">Color</th>
	                        <th width="70" rowspan="2">Order Qty.</th>

	                        <th width="210" colspan="3">Sewing Output</th>
	                        <th width="210" colspan="3">Packing & Finishing</th>
	                        <th width="210" colspan="3">Ex-Factory</th>
	                    </tr>
	                    <tr>
	                        <th width="70" rowspan="2">Today </th>
	                        <th width="70" rowspan="2">Total </th>
	                        <th width="70" rowspan="2">Balance</th>

	                        <th width="70" rowspan="2">Today </th>
	                        <th width="70" rowspan="2">Total </th>
	                        <th width="70" rowspan="2">Balance</th>

	                        <th width="70" rowspan="2">Today </th>
	                        <th width="70" rowspan="2">Total </th>
	                        <th width="70" rowspan="2">Balance</th>
	                    </tr>
	                </thead>
	            </table>
		        <div style="max-height:425px; overflow-y:scroll; width:1230px;" id="scroll_body">
		            <table  border="1" class="rpt_table"  width="1210" rules="all" id="table_body" >
						<tbody>
							<tr>
								<td width="30" align="left"><? echo $a;?></td>
								<td width="120" align="left"><? echo $a;?></td>
								<td width="120" align="left"><? echo $a;?></td>
								<td width="120" align="left"><? echo $a;?></td>
								<td width="120" align="left"><? echo $a;?></td>
								<td width="70" align="right"><? echo $a;?></td>
								<td width="70" align="right"><? echo $a;?></td>
								<td width="70" align="right"><? echo $a;?></td>
								<td width="70" align="right"><? echo $a;?></td>
								<td width="70" align="right"><? echo $a;?></td>
								<td width="70" align="right"><? echo $a;?></td>
								<td width="70" align="right"><? echo $a;?></td>
								<td width="70" align="right"><? echo $a;?></td>
								<td width="70" align="right"><? echo $a;?></td>
								<td width="70" align="right"><? echo $a;?></td>
							</tr>
						</tbody>
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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}


if($action=="finish_fabric")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);


	  $insert_cond="   and  d.production_date='$insert_date'";
    // if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?

		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>
               <th><? echo $total_qty; ?></th>
               </tr>
          </tfoot>
       </table>
      </fieldset>
       <br />
    <?

	$sql_fabric="SELECT a.po_breakdown_id,a.color_id,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
	    ELSE 0 END ) AS grey_fabric_issue,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =4  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
	    ELSE 0 END ) AS grey_fabric_issue_return,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS finish_fabric_rece,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS finish_fabric_rece_return,
		sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS fabric_qty,
		sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =2 and b.item_category=2  AND a.entry_form =18 THEN a.quantity
		ELSE 0 END ) AS fabric_qty_pre,
		sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
		sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_pre,
		sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty,
		sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_pre
		FROM order_wise_pro_details a,inv_transaction b
	    WHERE a.trans_id = b.id
		and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0 AND a.po_breakdown_id
		in (".str_replace("'","",$po_number_id).") group by a.po_breakdown_id,a.color_id";



		//echo $sql_cutting_delevery;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql_cutting_delevery);
		$production_details_arr=array();
		$production_size_details_arr=array();
		foreach( $sql_data as $row)
		{
		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array['color_total']+=$row[csf('production_qnty')];
		//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];

		$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
		$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
		$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('cut_delivery_date')];
		$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
		$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('production_qnty')];
		//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
		$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('production_qnty')];
		}
		// print_r($production_size_details_arr);die;
		 $job_color_tot=0;
		 ?>
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">ID</th>
                        <th width="70">Date</th>
                        <th width="70">Fabric Qty.</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($production_details_arr as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//if($value_c != "")
				//{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
                 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
				 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{

						?>
						<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
						<?

						}
				 ?>
				 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				//}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th></th>
                 <th></th>
                 <th></th>
				 <th>Total</th>

				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
						<?
							}
						}
				?>
                 <th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
				 </tr>
			  </tfoot>
		</table>
	    <br />
     </fieldset>
 </div>
 <?
}


if($action=="cutting_delivery_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and c.item_number_id=$item_id";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_id=b.id and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");

	// ================= gross qty ==================
	$sql_delivery_qnty=sql_select("SELECT  d.country_id,d.color_number_id,
	e.production_qnty AS delivery_qnty
	from  wo_po_color_size_breakdown d,pro_cut_delivery_color_dtls e
	where  e.color_size_break_down_id=d.id and  d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and d.color_number_id=$color_id $item_cond  group by d.country_id,d.color_number_id,e.production_qnty");

	$delivery_qty_arr=array();
	foreach($sql_delivery_qnty as $key=>$value)
	{
		$delivery_qty_arr[$value[csf("country_id")]][$value[csf("color_number_id")]]["delivery_qnty"] +=$value[csf("delivery_qnty")];

	}
	// ======================== bundle qty =======================$job_size_array=array();
	$main_sewing_source=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$production_details_arr=array();
	$production_size_details_arr=array();
	$prod_date = str_replace("d.production_date","a.production_date",$insert_cond);
	$bundle_sql = "SELECT a.id,a.challan_no,a.PRODUCTION_DATE,c.color_number_id as color_id,c.country_id,c.size_number_id,b.production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown  c where a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and b.status_active=1 and c.status_active=1 and a.status_active=1 and b.production_type=9 and a.PRODUCTION_SOURCE=1 and  c.po_break_down_id='$order_id' $prod_date and c.color_number_id=$color_id $item_cond2";
	// echo $bundle_sql;
	$res = sql_select($bundle_sql);
	foreach($res as $v)
	{
		$delivery_qty_arr[$v[csf("country_id")]][$v[csf("color_id")]]["delivery_qnty"] +=$v[csf("production_qnty")];
		// =================================================
		$job_size_array[$order_number][$v[csf('size_number_id')]]=$v[csf('size_number_id')];
		$job_size_qnty_array[$v[csf('size_number_id')]]+=$v[csf('production_qnty')];


		$job_color_array[$order_number][$v[csf('color_id')]]=$v[csf('color_id')];
		$job_color_qnty_array['color_total']+=$v[csf('production_qnty')];
		//$job_color_size_qnty_array[$order_number][$v[csf('color_id')]][$v[csf('size_number_id')]]+=$v[csf('product_qty')];

		$production_details_arr[$v[csf('id')]]['country']=$v[csf('country_id')];
		$production_details_arr[$v[csf('id')]]['color']=$v[csf('color_id')];
		$production_details_arr[$v[csf('id')]]['production_date']=$v[csf('production_date')];
		$production_details_arr[$v[csf('id')]]['challan_no']=$v[csf('challan_no')];
		$production_details_arr[$v[csf('id')]]['product_qty']+=$v[csf('production_qnty')];
		//$production_details_arr[$v[csf('id')]]['size']=$v[csf('size_number_id')];
		$production_size_details_arr[$v[csf('id')]][$v[csf('size_number_id')]]['product_qty']+=$v[csf('production_qnty')];

	}

	$sql_color_size="SELECT d.country_id, d.color_number_id,d.size_number_id,sum(d.plan_cut_qnty) as plan_cut, sum(d.order_quantity) as order_qty  from wo_po_break_down a, wo_po_color_size_breakdown d where a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1  and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and d.color_number_id=$color_id  group by d.country_id,d.color_number_id,d.size_number_id";
	//  echo $sql_color_size;
	$sql_color_size_data=sql_select($sql_color_size);
	$country_size_arr=array();
	$country_color_size_arr=array();
	foreach($sql_color_size_data as $key=>$value)
	{
		$country_size_arr[$value[csf("country_id")]][$value[csf("color_number_id")]][$value[csf("size_number_id")]]["order_qty"] +=$value[csf("order_qty")];
		$country_size_arr[$value[csf("country_id")]][$value[csf("color_number_id")]][$value[csf("size_number_id")]]["plan_cut"] +=$value[csf("plan_cut")];
		$country_color_size_arr[$value[csf("country_id")]]["order_qty_color_total"] +=$value[csf("order_qty")];
		$country_color_size_arr[$value[csf("country_id")]]["plan_qty_color_total"] +=$value[csf("order_qty")];

	}
	?>
    <div id="data_panel" align="center" style="width:100%">
       <div  style="width:830px">
	    <div id="data_panel" align="" style="width:100%">
    	<script>
			function new_window()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports_all').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
	 </div>
	 <hr>
	 <div id="details_reports_all">
	 <!-- ======================================== 1st Print Start ================================== -->
	  <div id="details_reports">
         <table width="810px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="150">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="80">Order Qty</th>
			  <th width="80">Delivery Qty</th>
              </tr>
          </thead>
          <tbody>
          <?

		    foreach($sql_job as $row)
			{
				// if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>">
					<td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
					<td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
					<td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
					<td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
					<td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
					<td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>

					<td align="right">
					<?
						echo $delivery_qty_arr[$value[csf("country_id")]][$value[csf("color_number_id")]]["delivery_qnty"]+=$value[csf("delivery_qnty")];
						$total_delivery_qty+=$delivery_qty_arr[$value[csf("country_id")]][$value[csf("color_number_id")]]["delivery_qnty"] ;
					?>
					</td>

				</tr>
				<?
			}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>

               <th><? echo $total_qty; ?></th>
			   <th align="right"><? echo $total_delivery_qty;?></th>
               </tr>
          </tfoot>
       </table>
			<!-- </div>
			</div> -->
      </div>
       <br />
    	<?
		$sql_cutting_delevery="SELECT a.id,a.cut_delivery_date,a.challan_no ,b.production_qnty,c.size_number_id,c.color_number_id,c.country_id,d.sewing_source
		from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c,pro_cut_delivery_mst d
	    where a.id=b.mst_id
	    and b.color_size_break_down_id=c.id
		and a.po_break_down_id=c.po_break_down_id
		and d.id=a.delivery_mst_id

		and d.sewing_source=1
		and a.po_break_down_id=$order_id
		and c.color_number_id=$color_id
		and a.status_active=1 and a.is_deleted=0
		and  b.status_active=1  and b.is_deleted=0
		and c.status_active=1 $item_cond2";
		// echo $sql_cutting_delevery;
		$sql_data = sql_select($sql_cutting_delevery);

		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");


		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];


			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array['color_total']+=$row[csf('production_qnty')];
			//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];

			$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
			$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
			$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('cut_delivery_date')];
			$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
			$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('production_qnty')];
			//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
			$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('production_qnty')];

		}

		// print_r($production_size_details_arr);die;
		 $job_color_tot=0;
		 ?>
		 <!-- 2nd print Start here -->
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
	    <script>
			function new_window2()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('InhouseOutBound_details').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window2()" />
	  <div id="InhouseOutBound_details">
		<div style="text-align: center;"> <strong>In House </strong></div>
		<div style="text-align: center;"> <strong>Style No : <? echo $sql_job[0][csf('style_ref_no')]; ?> </strong></div>

			<div style="text-align:center"> <strong>Order Number: <? echo $order_number; ?><strong></div>
			<table width="390" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($production_details_arr as $key_c=>$value_c)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					?>
					<tr bgcolor="<? echo $bgcolor;?>">
						<td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
						<td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
						<td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
						<td align="right"><?  echo  $value_c['challan_no']; ?></td>
						<?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{

							?>
							<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
							<?

						}
						?>
						<td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

					</tr>
					<?
					$i++;

				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th></th>
                 <th></th>
                 <th></th>
				 <th>Total</th>

				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
						<?
							}
						}
				?>
                 <th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
				 </tr>
			  </tfoot>
		</table>
				  <br />

			<!-- ==============================================================================================
			/											Outbound Part											/
			/ =============================================================================================== -->
			<?
		    $sql_outbound="SELECT a.id,a.cut_delivery_date,a.challan_no ,b.production_qnty,c.size_number_id,c.color_number_id,c.country_id,d.sewing_source,d.sewing_company
			from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c,pro_cut_delivery_mst d
			where a.id=b.mst_id
			and b.color_size_break_down_id=c.id
			and a.po_break_down_id=c.po_break_down_id
			and d.id=a.delivery_mst_id

			and d.sewing_source=3
			and a.po_break_down_id=$order_id
			and c.color_number_id=$color_id
			and a.status_active=1 and a.is_deleted=0
			and  b.status_active=1  and b.is_deleted=0
			and c.status_active=1 $item_cond2";
			// echo $sql_outbound;


		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql_outbound);
		$production_details_arr=array();
		$production_size_details_arr=array();
		$country_cutting_qty_arr=array();
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];


			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array['color_total']+=$row[csf('production_qnty')];
			//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];

			$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
			$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
			$production_details_arr[$row[csf('id')]]['sewing_company']=$row[csf('sewing_company')];


			$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('cut_delivery_date')];
			$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
			$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('production_qnty')];
			//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
			$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('production_qnty')];

		}

		//  ============================ bundle level data =========================
		$bundle_sql = "SELECT a.id,a.challan_no,a.production_date,a.serving_company,c.color_number_id,c.country_id,c.size_number_id,b.production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown  c where a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and b.status_active=1 and c.status_active=1 and a.status_active=1 and b.production_type=9 and a.PRODUCTION_SOURCE=3 and  c.po_break_down_id='$order_id' $prod_date and c.color_number_id=$color_id $item_cond2";
		// echo $bundle_sql;die;
		$sql_data = sql_select($bundle_sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];


			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array['color_total']+=$row[csf('production_qnty')];
			//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];

			$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
			$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
			$production_details_arr[$row[csf('id')]]['sewing_company']=$row[csf('serving_company')];


			$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('production_date')];
			$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
			$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('production_qnty')];
			//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
			$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('production_qnty')];

		}

		$sql_output_qty="SELECT  d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id, d.production_date,f.country_id,d.production_source FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f WHERE d.po_break_down_id=$order_id  and d.id=e.mst_id and e.color_size_break_down_id=f.id  and f.po_break_down_id=$order_id   and f.color_number_id=$color_id and e.is_deleted =0 and e.status_active =1 and f.is_deleted =0 and f.status_active =1 and d.is_deleted =0 and d.status_active =1 $insert_cond  order by d.production_date";
	  	// echo $sql_output_qty;
		$sql_output_data=sql_select($sql_output_qty);
		// $job_size_array=array();
		$country_color_size_arr=array();
		 $country_cutting_qty_arr=array();
		foreach($sql_output_data as $row){
			$country_color_wise_arr[$row[csf('country_id')]][$row[csf('color_number_id')]]['country_qty']+=$row[csf('product_qty')];
			$country_cutting_qty_arr[$row[csf('production_source')]][$row[csf('country_id')]][$row[csf('size_number_id')]]=$row[csf('product_qty')];
			// $grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			// $grand_color_qty+=$row[csf('product_qty')];
			// $country_cutting_qty_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			// $country_cutting_qty_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
			// $country_cutting_qty_arr[$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];


		}
		//  print_r($country_cutting_qty_arr);

		// echo "<pre>";print_r($production_details_arr);die;
		 $job_color_tot=0;
		 ?>
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
	    <script>
			function new_window2()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('InhouseOutBound_details').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window2()" />
	  <div id="InhouseOutBound_details">
		<div style="text-align: center;"> <strong>Out Bound </strong></div>
		<div style="text-align: center;"> <strong>Style No : <? echo $sql_job[0][csf('style_ref_no')]; ?> </strong></div>

			<div style="text-align:center"> <strong>Order Number: <? echo $order_number; ?><strong></div>
			<table width="490" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="100">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						// print_r($itemSizeArr[$value]);
						?>

                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($production_details_arr as $key_c=>$value_c)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//if($value_c != "")
					//{
					?>
					<tr bgcolor="<? echo $bgcolor;?>">
					<td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
					<td align="center"><?  echo  $supplier_arr [$value_c['sewing_company']]; ?>
					<td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
					<td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
					<td align="right"><?  echo  $value_c['challan_no']; ?></td>
					<?
							foreach($job_size_array[$order_number] as $key_s=>$value_s)
							{

							?>
							<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
							<?

							}
					?>
					<td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

					</tr>
					<?
					$i++;
					//}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th></th>
				 <th></th>
                 <th></th>
                 <th></th>
				 <th>Total</th>

				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
						<?
							}
						}
				?>
                 <th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
				 </tr>
			  </tfoot>
		</table>
				  <br />
				        <!-- 2nd Print End  -->
						<!-- 3rd Print Start from here  -->

		<?
		$tbl_width = 370+(count($job_size_array[$order_number])*60);
		?>
		<!-- ==============================================================================================
		/												Last Part											/
		/ =============================================================================================== -->
		<div id="data_panel" align="center" style="width:100%">
        	<script>
				function new_window3()
				{
					$(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('country_wise_breakdown_details').innerHTML);
					d.close();
					$(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window3()" />
	       	<div id="country_wise_breakdown_details" style="text-align: center;margin: 0 auto;width: <? echo $tbl_width;?>px">
				<div style="text-align: center;"> <strong>Style : <? echo $sql_job[0][csf('style_ref_no')]; ?></strong>, &nbsp;
				<strong>Po Number: <? echo $order_number; ?></strong></div>


				<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2">

					<?

					$i=1;

					foreach($country_color_wise_arr as $country_key=>$country_data)
					{

						?>
						<thead>
							<tr>
		                        <th width="100">Country</th>
		                        <th width="100">Color</th>
		                        <th width="100">Size</th>
								<?
								foreach($job_size_array[$order_number] as $key=>$value)
								{
									if($value !="")
									{
										?>
										<th width="60"><? echo $itemSizeArr[$value];?></th>
										<?
									}
								}
								?>
		                        <th width="70">Total Qty.</th>
							</tr>
						</thead>


						<?
						foreach($country_data as $color_key=>$val)
						{


							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//if($value_c != "")
							//{
							?>
							 <tr bgcolor="<? echo $bgcolor;?>">
			                 <td width="100" rowspan="4" valign="middle" align="left" style="word-break: break-all;word-wrap: break-word;">
			                 <? echo  $country_arr[$country_key]."/".$country_code_arr[$country_key]; ?>
			                 </td>
							 <td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 <td width="100" align="left">Order Qnty</td>
							 <?
							 	$totalQty = 0;
								foreach($job_size_array[$order_number] as $key_s=>$value_s)
								{

									?>
									<td width="60" align="right"><? echo $country_size_arr[$country_key][$color_key][$key_s]['order_qty'];?></td>
									<?
									$totalQty += $country_size_arr[$country_key][$color_key][$key_s]['order_qty'];
								}
								// echo '<pre>';
								//  print_r($country_size_arr);
							 ?>

							 <td width="70" align="right"><? echo  $totalQty; ?></td>

							 </tr>

							<tr>
							  	<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
								<td align="left">Plan To <? echo ($type==4) ? "Input" : "Output";?></td>
									 <?
									 $pc_total = 0;
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_size_arr[$country_key][$color_key][$key_s]["plan_cut"];?></td>
												<?
												$pc_total += $country_size_arr[$country_key][$color_key][$key_s]["plan_cut"];
											}
										}
									?>
				                <td width="70" align="right"><? echo $pc_total; ?></td>
							</tr>
						  	<tr>
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 	<td  align="left"><? echo ($type==4) ? "Input" : "Output";?> Qty</td>
									<?
									// echo $country_key."==".$key_s."<br>";
										$cuttingTotal = 0;
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_cutting_qty_arr[$country_key][$color_key][$key_s];?></td>
												<?
												$cuttingTotal += $country_cutting_qty_arr[$country_key][$color_key][$key_s];
											}
										}
									?>
			                 	<td width="70" align="right"><? echo  $cuttingTotal; ?></td>
						 	</tr>

						  	<tr>
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 	<td  align="left"><? echo ($type==4) ? "Input" : "Output";?> Balance</td>
								 <?
									foreach($job_size_array[$order_number] as $key_s=>$value_s)
									{
										if($value_s !="")
										{
											?>
											<td width="60" align="right"><? echo $bal = $country_size_arr[$country_key][$color_key][$key_s]["plan_cut"] - $country_cutting_qty_arr[$country_key][$key_s];?></td>
											<?
											$balance += $bal;
										}
									}
								?>
			                 	<td  align="right"><? echo  $balance; ?></td>
						 	</tr>
						 	<?
							$i++;
							//}
						}
					}
					?>
				</table>


				<br />

 </div>
 <?
}

if($action=="cutting_and_sewing_remarks")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and c.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";
	?>
    <div align="center">
        <fieldset style="width:480px">
        <legend>Cutting</legend>
		<?
        $sql="SELECT  d.id,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=1  and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1 and
		d.is_deleted =0 and
		d.status_active =1 and
		f.is_deleted =0 and
		f.status_active =1   $insert_cond  $item_cond2 group by d.id,f.color_number_id,d.remarks,d.production_date order by d.id";
        //echo $sql;
        echo  create_list_view ( "list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280","600","220",1, $sql, "", "","", 1, '0,0,0,0', $arr, "id,production_date,product_qty,remarks", "../requires/daily_cutting_inhand_report_controller", '','0,3,1,0','0,0,0,product_qty,0');
        ?>
        </fieldset>
        <br/>
        <fieldset style="width:480px">
        <legend>Cutting Delivery to Input</legend>
		<?
	    $sql_cutting_delevery="select a.id,a.cut_delivery_date ,a.remarks,
		sum(b.production_qnty) AS cut_delivery_qnty
		from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c
	    where a.id=b.mst_id
	    and b.color_size_break_down_id=c.id
		and a.po_break_down_id=c.po_break_down_id
		and a.po_break_down_id=$order_id
		and c.color_number_id=$color_id  $item_cond
	    group by a.id,a.cut_delivery_date ,a.remarks";
       // echo $sql_cutting_delevery;
        echo  create_list_view ( "list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280","600","220",1, $sql_cutting_delevery, "", "","", 1, '0,0,0,0', $arr, "id,cut_delivery_date,cut_delivery_qnty,remarks", "../requires/daily_cutting_inhand_report_controller", '','0,3,1,0','0,0,0,cut_delivery_qnty,0');

        ?>
        </fieldset>
        <br/>
        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
		<?
        $sql="SELECT  d.id,d.embel_name,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=2 and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1 and
		d.is_deleted =0 and
		d.status_active =1 and
		f.is_deleted =0 and
		f.status_active =1   $insert_cond $item_cond2 group by d.id,d.embel_name,f.color_number_id,d.remarks,d.production_date order by d.embel_name";
        $arr=array(1=>$emblishment_name_array);
        echo  create_list_view ( "list_view_1", "ID,Embel. Name,Date,Production Qnty,Remarks", "80,100,70,70,180","600","220",1, $sql, "", "","", 1, '0,embel_name,0,0,0', $arr, "id,embel_name,production_date,product_qty,remarks", "../requires/daily_cutting_inhand_report_controller", '','0,0,3,1,0','0,0,0,0,product_qty,0');
        ?>
        </fieldset>
        <br/>
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
		<?
        $sql="SELECT  d.id,d.embel_name,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=3 and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1  and
		f.is_deleted =0 and
		f.status_active =1 and
		d.is_deleted =0 and
		d.status_active =1

		 $insert_cond $item_cond2 group by d.id,d.embel_name,f.color_number_id,d.remarks,d.production_date order by d.embel_name";
        $arr=array(1=>$emblishment_name_array);
        echo  create_list_view ( "list_view_1", "ID,Embel. Name,Date,Production Qnty,Remarks", "80,100,70,70,180","600","220",1, $sql, "", "","", 1, '0,embel_name,0,0,0', $arr, "id,embel_name,production_date,product_qty,remarks", "../requires/daily_cutting_inhand_report_controller", '','0,0,3,1,0','0,0,0,0,product_qty,0');
        ?>
        </fieldset>
        <br/>

        <fieldset style="width:480px">
        <legend>Sewing Input</legend>
        <?
        $sql="SELECT  d.id,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=4  and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1  and
		d.is_deleted =0 and
		d.status_active =1 and
		f.is_deleted =0 and
		f.status_active =1
		 $insert_cond $item_cond2 group by d.id,f.color_number_id,d.remarks,d.production_date order by d.id";
        //echo $sql;
        echo  create_list_view ( "list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280","600","220",1, $sql, "", "","", 1, '0,0,0,0', $arr, "id,production_date,product_qty,remarks", "../requires/daily_cutting_inhand_report_controller", '','0,3,1,0','0,0,0,product_qty,0');
		?>
        </fieldset>
	</div>
	<?
	exit();
}



if($action=="emblishment_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";
	//echo $item_con.'aaaaaa';
	$sql_job=("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:810px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="150">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?

		    foreach( sql_select($sql_job) as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>
               <th><? echo $total_qty; ?></th>
               </tr>
          </tfoot>
       </table>
      </fieldset>
       <br />
    <?
        $delivery_sql="SELECT id ,sys_number_prefix_num from pro_gmts_delivery_mst where status_active=1 and is_deleted=0 and company_id=$company_id and production_type=$type and embel_name=$embl_type";
    	foreach( sql_select($delivery_sql) as $key=>$vals)
    	{
    		$challan_no_arr[$vals[csf("id")]]=$vals[csf("sys_number_prefix_num")];
    	}
 		$sql="SELECT  d.id,d.floor_id,d.production_source,d.serving_company,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,
		    d.challan_no,d.production_date,f.country_id,d.delivery_mst_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=$type  and
		    f.color_number_id=$color_id and
			d.embel_name=$embl_type  and
		    e.is_deleted =0 and
			e.status_active =1 and
		    d.is_deleted =0 and
			d.status_active =1 and
		    f.is_deleted =0 and
			f.status_active =1   $insert_cond  $item_cond2 order by d.production_date,f.id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		$production_details_arr=array();
		$production_size_details_arr=array();
		$floor_qty_arr=array();
		$grand_size_qty=array();
		//$grand_color_qty=array();
		foreach( $sql_data as $row)
		{
			if($row[csf('production_source')]==1)
			{
				$job_size_array[$row[csf('production_source')]][$ocrder_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$floor_qty_arr[$row[csf('production_source')]][$row[csf('floor_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
				if($row[csf('challan_no')])
					{
						$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
					}
					else
					{
						$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$challan_no_arr[$row[csf('delivery_mst_id')]];
					}
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
			}
			else
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				if($row[csf('challan_no')])
				{
					$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				}
				else
				{
					$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$challan_no_arr[$row[csf('delivery_mst_id')]];
				}

				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
			}
			$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$grand_color_qty+=$row[csf('product_qty')];
		}
		//print_r($job_size_array);die;
		 $job_color_tot=0;
		 ?>
        <div id="data_panel" align="" style="width:100%">
			<label> <strong>In House <strong><label/>

			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="150">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Unit Name</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[1][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?

				$i=1;
				$inhouse_floor=array();
				foreach($production_details_arr[1] as $key_c=>$value_c)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($i!=1)
						{

							if(!in_array($value_c['floor_id'],$inhouse_floor))
								{
								?>
								 <tr bgcolor="#FFFFE8">
									 <td colspan="5" align="right"> Floor Total</td>

									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>
								<?
								}
							}

							?>



							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  $floor_arr[$value_c['floor_id']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{

									?>
									<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['product_qty'] ;?></td>
									<?

									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>

							 </tr>
							<?

							$i++;
							$inhouse_floor[]=$value_c['floor_id'];
							$floor_id=$value_c['floor_id'];;
					}
					?>


                    		 <tr bgcolor="#FFFFE8">
									 <td colspan="5" align="right"> Floor Total</td>

									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1][$floor_id]['color_total']; ?></td>
								 </tr>


                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>

                             <th></th>
                             <th>Total</th>

                             <?
                                    foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
                                    <?
                                        }
                                    }
                            ?>
                             <th align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                             </tr>
                          </tfoot>
					</table>

                <label > <strong>Out Bound:<strong><label/>
                <table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="120">Color</th>
                        <th width="130">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[3][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?

				$j=1;
				$inhouse_floor=array();
				foreach($production_details_arr[3] as $key_c=>$value_c)
					{
					if($prod_reso_allo==1)
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>



							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                             <td align="center"><? echo  $supplier_arr[$value_c['serving_company']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
									{

									?>
									<td width="60" align="right"><? echo $production_size_details_arr[3][$key_c][$key_s]['product_qty'] ;?></td>
									<?

									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[3][$value_po][$value_c]; ?></td>

							 </tr>
							<?
							$j++;

					}
					?>

                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                              <th></th>
                             <th>Total</th>

                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[3][$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $job_color_qnty_array[3]['color_total']; ?></th>
                             </tr>
                             <tr bgcolor="<? // echo $bgcolor;?>">

                             <th colspan="5"> Grand Total</th>

                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $grand_size_qty[$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $grand_color_qty; ?></th>
                             </tr>

                          </tfoot>

					</table>
	 </div>
 <?
}


if($action=="cutting_and_sewing_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";

	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");
	//   $sql= "SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	//   sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	//   from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	//   where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	//   b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id";
	//   echo $sql;

	$sql_color_size=sql_select("SELECT d.country_id, d.color_number_id,d.size_number_id, sum(d.plan_cut_qnty) as plan_cut, sum(d.order_quantity) as order_qty  from wo_po_break_down a, wo_po_color_size_breakdown d where a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1  and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and d.color_number_id=$color_id $item_cond group by d.country_id,d.color_number_id,d.size_number_id ");
	$country_size_qty_arr = array();
	$country_color_total_arr = array();
	foreach($sql_color_size as $key=>$vals)
	{
		$country_size_qty_arr[$vals[csf("country_id")]][$vals[csf("size_number_id")]]["order_qty"] +=$vals[csf("order_qty")];
		$country_size_qty_arr[$vals[csf("country_id")]][$vals[csf("size_number_id")]]["plan_cut"] +=$vals[csf("plan_cut")];
		$country_color_total_arr[$vals[csf("country_id")]]["order_qty_color_total"] +=$vals[csf("order_qty")];
		$country_color_total_arr[$vals[csf("country_id")]]["plan_cut_color_total"] +=$vals[csf("plan_cut")];
 	}
	?>
 <div id="data_panel" align="center" style="width:100%">

    <?
	$sql="SELECT  d.production_source,d.serving_company,d.floor_id,d.sewing_line,d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no, d.production_date,f.country_id,d.challan_no FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f WHERE d.po_break_down_id=$order_id  and d.id=e.mst_id and e.color_size_break_down_id=f.id and f.po_break_down_id=$order_id and e.production_type='$type'  and f.color_number_id=$color_id and e.is_deleted =0 and e.status_active =1 and f.is_deleted =0 and f.status_active =1 and d.is_deleted =0 and d.status_active =1 $insert_cond  $item_cond2 order by  d.floor_id,d.production_date,f.size_order";
	//echo $sql;die;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$production_details_arr=array();
	$production_size_details_arr=array();
	$job_floor_qnty_array=array();
	$floor_qty_arr=array();
	$grand_size_qty=array();
	$sewing_qty=array();
	//$grand_color_qty=array();

	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		// if($row[csf('production_source')]==1)
		// {
			$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$floor_qty_arr[$row[csf('production_source')]][$row[csf('floor_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
			$job_floor_qnty_array[$row[csf('production_source')]][$row[csf('floor_id')]]+=$row[csf('product_qty')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
			$sewing_qty[$row[csf('country_id')]]+=$row[csf('product_qty')];
			$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];

			$country_color_wise_arr[$row[csf('country_id')]][$row[csf('color_number_id')]]['country_qty']+=$row[csf('product_qty')];
			$country_cutting_qty_arr[$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		/*}
		else
		{
			$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
			$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
		}*/
		$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$grand_color_qty+=$row[csf('product_qty')];
	}
	//print_r($job_floor_qnty_array);die;
	$inhouse_size_total = count($job_size_array[1][$order_number]);
	$inhouse_tbl_width = 600+($inhouse_size_total*60);

	$outbound_size_total = count($job_size_array[3][$order_number]);
	$outbound_tbl_width = 530+($outbound_size_total*60);

	$job_color_tot=0;
	?>
     <!-- ======================================== All Print Start ================================== -->
     <div id="data_panel" align="" style="width:100%">
    	<script>
			function new_window()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports_all').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
	 </div>
	 <hr>
	<div id="details_reports_all">
	 <!-- ======================================== 1st Print Start ================================== -->
	  <div id="details_reports">
		<div  style="width:920px;margin: 0 auto;">
	       	<table width="900px" align="center" border="1" rules="all" class="rpt_table" >
		        <thead>
		              <tr>
		              <th width="200">Buyer Name</th>
		              <th width="100">Job No </th>
		              <th width="100">Style Reff.</th>
		              <th width="100">Country</th>
		              <th width="100">Order No</th>
		              <th width="100">Ship Date</th>
		              <th width="100">Order Qty</th>
		              <th width="100">Sewing Qty</th>
		              </tr>
		        </thead>
	         	<tbody>
	          <?
			    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
	                <tr bgcolor="<? echo $bgcolor;?>">
	                   <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
	                   <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
	                   <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
	                   <td align="left"><p><? echo $country_arr[$row[csf('country_id')]]."/".$country_code_arr[$row[csf('country_id')]]; ?></p></td>
	                   <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
	                   <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
	                    <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')]; ?></td>
	                    <td align="right"><? echo $sewing_qty[$row[csf('country_id')]]; $total_sewing_qty+=$sewing_qty[$row[csf('country_id')]]; ?></td>
	                </tr>
	                <?
				}
			  ?>
	          </tbody>
	            <tfoot>
	               <tr>
		               <th colspan="6">Total</th>
		               <th><? echo $total_qty; ?></th>
		               <th><? echo $total_sewing_qty; ?></th>
	               </tr>
	            </tfoot>
	       </table>
		</div>
	 </div>
	 <!-- ======================================== 1st Print End ================================== -->
	 <!-- ======================================== 2nd Print Start ================================== -->
	 <div id="data_panel" align="center" style="width:100%">
	    <script>
			function new_window2()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('InhouseOutBound_details').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window2()" />
	  <div id="InhouseOutBound_details">
		<div style="text-align: center;"> <strong>In House </strong></div>
		<div style="text-align: center;"> <strong>Style No : <? echo $sql_job[0][csf('style_ref_no')]; ?> </strong></div>
		<div style="text-align: center;"> <strong>Order No : <? echo $order_number; ?> </strong></div>

		<table width="<? echo $inhouse_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" >
			<thead>
				<tr>
					<th width="180">Color</th>
                    <th width="70">Country</th>
                    <th width="70">Unit Name</th>
                    <th width="70">Line No</th>
                    <th width="70">Date</th>
                    <th width="70">Challan</th>
					<?
					foreach($job_size_array[1][$order_number] as $key=>$value)
					{
						if($value !="")
						{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
						}
					}
					?>
                    <th width="70">Color Total</th>
				</tr>
			</thead>
		</table>

		<table align="center" width="<? echo $inhouse_tbl_width;?>" border="1" rules="all" class="rpt_table" id="html_search_1" >
			<?

			$i=1;
			$inhouse_floor=array();
			foreach($production_details_arr[1] as $key_c=>$value_c)
			{
				if($prod_reso_allo==1)
				{
					$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
			    }
				else
				{
					$line_name= $lineArr[$value_c['sewing_line']];
				}
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($i!=1)
				{
					if(!in_array($value_c['floor_id'],$inhouse_floor))
						{
						?>
						 <tr bgcolor="#FFFFE8">
							 <td width="530" colspan="6" align="right"> Floor Total</td>

							 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{
										if($value_s !="")
										{
									?>
									<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
									<?
										}
									}
							?>
							 <td width="70" align="right"><? echo  $job_floor_qnty_array[1][$floor_id]; ?></td>
						 </tr>
						<?
						}
					}
					?>
					<tr bgcolor="<? echo $bgcolor;?>">
						 <td width="180" align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
						 <td width="70" align="center"><? echo  $country_arr[$value_c['country']]."/".$country_code_arr[$value_c['country']]; ?></td>
						 <td width="70" align="center"><? echo  $floor_arr[$value_c['floor_id']]; ?></td>
						 <td width="70" align="center"><? echo  $line_name; ?></td>

						 <td width="70" align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
						 <td width="70" align="right"><?  echo  $value_c['challan_no']; ?></td>
						 <?
								foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
								{

								?>
								<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['product_qty'] ;?></td>
								<?

								}
						 ?>
						 <td width="70" align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>

					</tr>
					<?

					$i++;
					$inhouse_floor[]=$value_c['floor_id'];
					$floor_id=$value_c['floor_id'];
				}
				?>
        		<tr bgcolor="#FFFFE8">
						 <td colspan="6" align="right"> Floor Total</td>

						 <?
							foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
							{
								if($value_s !="")
								{
									?>
									<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
									<?
								}
							}
					?>
					 <td align="right"><? echo  $job_floor_qnty_array[1][$floor_id];;//$job_color_qnty_array[1][$floor_id]['color_total']; ?></td>
				</tr>
                <tfoot>
                 <tr bgcolor="<? // echo $bgcolor;?>">
                     <th></th>
                     <th></th>
                     <th></th>
                     <th></th>
                     <th></th>
                     <th>Total</th>

                     <?
                        foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                                ?>
                                <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
                                <?
                            }
                        }
                    ?>
                     <th align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                 </tr>
              </tfoot>
		</table>

        <!-- ======================================== OUT BOUND ================================== -->
        <div  style="text-align: center;"> <strong>Out Bound:</strong></div>
        <table width="<? echo $outbound_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" >
			<thead>
				<tr>
					<th width="180">Color</th>
                    <th width="70">Company</th>
                    <th width="70">Country</th>
                    <th width="70">Date</th>
                    <th width="70">Challan</th>
					<?
					foreach($job_size_array[3][$order_number] as $key=>$value)
					{
						if($value !="")
						{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
						}
					}
					?>
                    <th width="70">Color Total</th>
				</tr>
			</thead>
		</table>
		<!-- ============================= for Outbound order =========================== -->

		<table width="<? echo $outbound_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2" >
			<?
			$j=1;
			$inhouse_floor=array();
			foreach($production_details_arr[3] as $key_c=>$value_c)
			{
				if($prod_reso_allo==1)
				{
				$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
			    }
				else
				{
					$line_name= $lineArr[$value_c['sewing_line']];
				}
				if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				?>
				<tr bgcolor="<? echo $bgcolor;?>">
					 <td width="180" align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
	                 <td width="70" align="center"><? echo  $supplier_arr[$value_c['serving_company']]; ?></td>
					 <td width="70" align="center"><? echo  $country_arr[$value_c['country']]."/".$country_code_arr[$value_c['country']]; ?></td>
					 <td width="70" align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
					 <td width="70" align="right"><?  echo  $value_c['challan_no']; ?></td>
					 <?
							foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
							{

							?>
							<td width="60" align="right"><? echo $production_size_details_arr[3][$key_c][$key_s]['product_qty'] ;?></td>
							<?

							}
					 ?>
					 <td width="70" align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[3][$value_po][$value_c]; ?></td>

				</tr>
				<?
				$j++;

				}
				?>
                <tfoot>
	                 <tr bgcolor="<? // echo $bgcolor;?>">
		                 <th width="180"></th>
		                 <th width="70"></th>
		                 <th width="70"></th>
		                 <th width="70"></th>
		                 <th width="70">Total</th>

		                		 <?
		                        foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
		                        {
		                            if($value_s !="")
		                            {
		                                ?>
		                                <th width="60" align="right"><? echo $job_size_qnty_array[3][$key_s];?></th>
		                                <?
		                            }
		                        }
		                		?>
		                 <th width="70" align="right"><? echo  $job_color_qnty_array[3]['color_total']; ?></th>
		                 </tr>
		                 <tr bgcolor="<? // echo $bgcolor;?>">

		                 <th colspan="5"> Grand Total</th>

		                		 <?
		                		 $grand_color_qty=0;
		                        foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
		                        {
		                            if($value_s !="")
		                            {
		                                ?>
		                                <th width="60" align="right"><? $grand_color_qty+=$job_color_qnty_array[3]['color_total']; echo $job_color_qnty_array[3]['color_total'];?></th>
		                                <?
		                            }
		                        }
		                		?>
		                 <th align="right"><? echo  $grand_color_qty; ?></th>
	                 </tr>

              </tfoot>

		</table>
	  </div>
	 </div>
     <!-- ======================= 2nd Print End ================= -->
		<?
		$tbl_width = 370+(count($job_size_array[1][$order_number])*60);
		?>
		<div id="data_panel" align="center" style="width:100%">
        	<script>
				function new_window3()
				{
					$(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('country_wise_breakdown_details').innerHTML);
					d.close();
					$(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window3()" />
	       	<div id="country_wise_breakdown_details" style="text-align: center;margin: 0 auto;width: <? echo $tbl_width;?>px">
				<div style="text-align: center;"> <strong>Style : <? echo $sql_job[0][csf('style_ref_no')]; ?></strong>, &nbsp;
				<strong>Po Number: <? echo $order_number; ?></strong></div>


				<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2">

					<?

					$i=1;

					foreach($country_color_wise_arr as $country_key=>$country_data)
					{

						?>
						<thead>
							<tr>
		                        <th width="100">Country</th>
		                        <th width="100">Color</th>
		                        <th width="100">Size</th>
								<?
								foreach($job_size_array[1][$order_number] as $key=>$value)
								{
									if($value !="")
									{
										?>
										<th width="60"><? echo $itemSizeArr[$value];?></th>
										<?
									}
								}
								?>
		                        <th width="70">Total Qty.</th>
							</tr>
						</thead>
						<?
						foreach($country_data as $color_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//if($value_c != "")
							//{
							?>
							 <tr bgcolor="<? echo $bgcolor;?>">
			                 <td width="100" rowspan="4" valign="middle" align="left" style="word-break: break-all;word-wrap: break-word;">
			                 <? echo  $country_arr[$country_key]."/".$country_code_arr[$country_key]; ?>
			                 </td>
							 <td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 <td width="100" align="left">Order Qnty</td>
							 <?
							 	$totalQty = 0;
								foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
								{

									?>
									<td width="60" align="right"><? echo $country_size_qty_arr[$country_key][$key_s]['order_qty'];?></td>
									<?
									$totalQty += $country_size_qty_arr[$country_key][$key_s]['order_qty'];
								}
							 ?>
							 <td width="70" align="right"><? echo  $totalQty; ?></td>

							 </tr>

							<tr>
							  	<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
								<td align="left">Plan To <? echo ($type==4) ? "Input" : "Output";?></td>
									 <?
									 $pc_total = 0;
										foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_size_qty_arr[$country_key][$key_s]["plan_cut"];?></td>
												<?
												$pc_total += $country_size_qty_arr[$country_key][$key_s]["plan_cut"];
											}
										}
									?>
				                <td width="70" align="right"><? echo $pc_total; ?></td>
							</tr>
						  	<tr>
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 	<td  align="left"><? echo ($type==4) ? "Input" : "Output";?> Qty</td>
									<?
										$cuttingTotal = 0;
										foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_cutting_qty_arr[$country_key][$key_s];?></td>
												<?
												$cuttingTotal += $country_cutting_qty_arr[$country_key][$key_s];
											}
										}
									?>
			                 	<td width="70" align="right"><? echo  $cuttingTotal; ?></td>
						 	</tr>

						  	<tr>
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 	<td  align="left"><? echo ($type==4) ? "Input" : "Output";?> Balance</td>
								 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{
										if($value_s !="")
										{
											?>
											<td width="60" align="right"><? echo $bal = $country_size_qty_arr[$country_key][$key_s]["plan_cut"] - $country_cutting_qty_arr[$country_key][$key_s];?></td>
											<?
											$balance += $bal;
										}
									}
								?>
			                 	<td  align="right"><? echo  $balance; ?></td>
						 	</tr>
						 	<?
							$i++;
							//}
						}
					}
					?>
				</table>
				<br />
	     	</div>
		</div>
	 <!-- ======================= 3rd Print End ================= -->
	</div>
     <!-- ======================================== All Print End ================================== -->
 </div>
	<script>
	 	setFilterGrid("html_search_1",-1);
	 	setFilterGrid("html_search_2",-1);
	</script>
	<?
}



if($action=="iron_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";

	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");
	?>
    <div id="data_panel" align="center" style="width:100%">

    <?
		 $sql="SELECT  d.production_source,d.serving_company,d.floor_id,d.sewing_line,d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,
		    d.production_date,f.country_id,d.challan_no
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type='$type'  and
			f.color_number_id=$color_id and
		    e.is_deleted =0 and
			e.status_active =1 and
		    f.is_deleted =0 and
			f.status_active =1 and
		    d.is_deleted =0 and
			d.status_active =1
			 $insert_cond  $item_cond2 order by d.production_date,f.size_order";
		//echo $sql;die;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		$production_details_arr=array();
		$production_size_details_arr=array();
		$floor_qty_arr=array();
		$grand_size_qty=array();
		//$grand_color_qty=array();
		foreach( $sql_data as $row)
		{
			if($row[csf('production_source')]==1)
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$floor_qty_arr[$row[csf('production_source')]][$row[csf('floor_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
			}
			else
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
			}
			$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$grand_color_qty+=$row[csf('product_qty')];
		}
		//print_r($job_size_array);die;
		 $job_color_tot=0;
		 ?>
        <div id="data_panel" align="" style="width:100%">
			<label> <strong>In House </strong><label/>

			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Unit Name</th>
                        <th width="70">Line No</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[1][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?

				$i=1;
				$inhouse_floor=array();
				foreach($production_details_arr[1] as $key_c=>$value_c)
					{

					if($prod_reso_allo==1)
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($i!=1)
						{

							if(!in_array($value_c['floor_id'],$inhouse_floor))
								{
								?>
								 <tr bgcolor="#FFFFE8">
									 <td colspan="6" align="right"> Floor Total</td>

									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>
								<?
								}
							}

							?>

							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  $floor_arr[$value_c['floor_id']]; ?></td>
							 <td align="center"><? echo  $line_name; ?></td>

							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{

									?>
									<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['product_qty'] ;?></td>
									<?

									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>

							 </tr>
							<?

							$i++;
							$inhouse_floor[]=$value_c['floor_id'];
							$floor_id=$value_c['floor_id'];;
					}
					?>

                    		 <tr bgcolor="#FFFFE8">
									 <td colspan="6" align="right"> Floor Total</td>

									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1][$floor_id]['color_total']; ?></td>
								 </tr>


                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th>Total</th>

                             <?
                                    foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
                                    <?
                                        }
                                    }
                            ?>
                             <th align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                             </tr>
                          </tfoot>
					</table>

                <label > <strong>Out Bound:<strong><label/>
                <table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[3][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?

				$j=1;
				$inhouse_floor=array();
				foreach($production_details_arr[3] as $key_c=>$value_c)
					{
					if($prod_reso_allo==1)
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>

							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                             <td align="center"><? echo  $supplier_arr[$value_c['serving_company']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
									{

									?>
									<td width="60" align="right"><? echo $production_size_details_arr[3][$key_c][$key_s]['product_qty'] ;?></td>
									<?

									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[3][$value_po][$value_c]; ?></td>

							 </tr>
							<?
							$j++;

					}
					?>

                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                              <th></th>
                             <th>Total</th>

                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[3][$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $job_color_qnty_array[3]['color_total']; ?></th>
                             </tr>
                             <tr bgcolor="<? // echo $bgcolor;?>">

                             <th colspan="5"> Grand Total</th>

                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $grand_size_qty[$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $grand_color_qty; ?></th>
                             </tr>

                          </tfoot>

					</table>
	 </div>
	 <?
}



if($action=="packing_and_finishing_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";

	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");
	$sql_color_size="SELECT d.country_id, d.color_number_id,d.size_number_id,sum(d.plan_cut_qnty) as plan_cut, sum(d.order_quantity) as order_qty  from wo_po_break_down a, wo_po_color_size_breakdown d where a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1  and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and d.color_number_id=$color_id  group by d.country_id,d.color_number_id,d.size_number_id";
	//  echo $sql_color_size;
	$sql_color_size_data=sql_select($sql_color_size);
	$country_size_arr=array();
	$country_color_size_arr=array();
	foreach($sql_color_size_data as $key=>$vals){
		// $country_size_arr[$value[csf("country_id")]][$value[csf("color_number_id")]][$value[csf("size_number_id")]]["order_qty"] +=$value[csf("order_qty")];
		// $country_size_arr[$value[csf("country_id")]][$value[csf("color_number_id")]][$value[csf("size_number_id")]]["plan_cut"] +=$value[csf("plan_cut")];
		// $country_color_size_arr[$value[csf("country_id")]]["order_qty_color_total"] +=$value[csf("order_qty")];
		// $country_color_size_arr[$value[csf("country_id")]]["plan_qty_color_total"] +=$value[csf("order_qty")];
		$color_size_wise_arr[$vals[csf("size_number_id")]]["order_qty"] +=$vals[csf("order_qty")];
		$color_size_wise_arr[$vals[csf("size_number_id")]]["plan_cut"] +=$vals[csf("plan_cut")];
		$color_total_arr["order_qty_color_total"] +=$vals[csf("order_qty")];
		$color_total_arr["plan_cut_color_total"] +=$vals[csf("plan_cut")];
		//==============================================
		$country_size_qty_arr[$vals[csf("country_id")]][$vals[csf("size_number_id")]]["order_qty"] +=$vals[csf("order_qty")];
		$country_size_qty_arr[$vals[csf("country_id")]][$vals[csf("size_number_id")]]["plan_cut"] +=$vals[csf("plan_cut")];
		$country_color_total_arr[$vals[csf("country_id")]]["order_qty_color_total"] +=$vals[csf("order_qty")];
		$country_color_total_arr[$vals[csf("country_id")]]["plan_cut_color_total"] +=$vals[csf("plan_cut")];

	}
	//   $sql="SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	//   sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no,f.production_quantity as product_qty
	//   from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d,pro_garments_production_mst f
	//   where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and f.po_break_down_id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	//   b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id,f.production_quantity";
	//   echo $sql;

	//   $sql="SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	//   sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	//   from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	//   where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	//   b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id";
	//   echo $sql;
	?>
    <div id="data_panel" align="center" style="width:100%">

    <?



		 $sql="SELECT  d.production_source,d.serving_company,d.floor_id,d.sewing_line,d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,
		    d.production_date,f.country_id,d.challan_no
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type='$type'  and
			f.color_number_id=$color_id and
		    e.is_deleted =0 and
			e.status_active =1 and
		    f.is_deleted =0 and
			f.status_active =1 and
		    d.is_deleted =0 and
			d.status_active =1
			 $insert_cond  $item_cond2 order by d.production_date,f.size_order";
		// echo $sql;die;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		$production_details_arr=array();
		$production_size_details_arr=array();
		$packing_and_finshing_arr=array();
		$country_color_wise_arr=array();
	    $country_cutting_qty_arr=array();
		$floor_qty_arr=array();
		$grand_size_qty=array();
		//$grand_color_qty=array();
		foreach( $sql_data as $row)
		{
			if($row[csf('production_source')]==1)
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_array2[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$floor_qty_arr[$row[csf('production_source')]][$row[csf('floor_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];

				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
				$packing_and_finshing_arr[$row[csf('country_id')]]+=$row[csf('product_qty')];
				$packing_and_finshing_arr2[$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];

			}
			else
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_array2[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
				$packing_and_finshing_arr[$row[csf('country_id')]]+=$row[csf('product_qty')];
				$packing_and_finshing_arr2[$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];


			}

			$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$grand_color_qty+=$row[csf('product_qty')];
		}

		$sql_output_qty="SELECT  d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id, d.production_date,f.country_id,g.production_source FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f, pro_garments_production_mst g,
		pro_garments_production_dtls i WHERE d.po_break_down_id=$order_id  and d.id=e.mst_id and e.color_size_break_down_id=f.id and  g.id=i.mst_id and i.color_size_break_down_id=f.id and f.po_break_down_id=$order_id   and f.color_number_id=$color_id and e.is_deleted =0 and e.status_active =1 and f.is_deleted =0 and f.status_active =1 and d.is_deleted =0 and d.status_active =1 $insert_cond  order by d.production_date";
		//   echo $sql_output_qty;
		$sql_output_data=sql_select($sql_output_qty);
		// $job_size_array=array();
		$country_color_size_arr=array();
		 $country_cutting_qty_arr=array();
		foreach($sql_output_data as $row){
			$country_color_wise_arr[$row[csf('country_id')]][$row[csf('color_number_id')]]['country_qty']+=$row[csf('product_qty')];
			$country_cutting_qty_arr[$row[csf('country_id')]][$row[csf('size_number_id')]]=$row[csf('product_qty')];
			// $grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			// $grand_color_qty+=$row[csf('product_qty')];
			// $country_cutting_qty_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			// $country_cutting_qty_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
			// $country_cutting_qty_arr[$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];


		}
		/* echo "<pre>";
		print_r($packing_and_finshing_arr2); die; */

		 $job_color_tot=0;
		$inhouse_size_total = count($job_size_array[1][$order_number]);
		$inhouse_tbl_width = 600+($inhouse_size_total*60);

		$outbound_size_total = count($job_size_array[3][$order_number]);
		$outbound_tbl_width = 530+($outbound_size_total*60);
		 ?>
		 <!-- 1st Print Start Here -->
		 <div id="details_reports_all">
	 <!-- ======================================== 1st Print Start ================================== -->
	  <div id="details_reports">
         <table width="810px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="150">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="80">Order Qty</th>
			  <th width="80">Packing & Finishing Qty</th>

              </tr>
          </thead>
          <tbody>
          <?

		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>

						<td align="right"><? echo  $packing_and_finshing_arr[$row[csf('country_id')]] ; $total_packing_qty+= $packing_and_finshing_arr[$row[csf('country_id')]];?></td>

                    </tr>
                    <?
				}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>

               <th><? echo $total_qty; ?></th>
			   <th align="right"><? echo $total_packing_qty;?></th>
               </tr>
          </tfoot>
       </table>
			<!-- </div>
			</div> -->
      </div>
       <br />


		 <!-- 1st print End -->
		  <!--2nd Print Start Here  -->
        <div id="data_panel" align="" style="width:100%">
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
			<hr>
			<div id="details_reports">
			<div style="text-align: center;"> <strong>In House </strong></div>
            <div style="text-align: center;"> <strong>Order No : <? echo $order_number;?> </strong></div>
			<table width="<? echo $inhouse_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Unit Name</th>
                        <th width="70">Line No</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[1][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
			</table>
			<table width="<? echo $inhouse_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_1">
				<?

				$i=1;
				$inhouse_floor=array();
				foreach($production_details_arr[1] as $key_c=>$value_c)
					{

					if($prod_reso_allo==1)
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($i!=1)
						{

							if(!in_array($value_c['floor_id'],$inhouse_floor))
								{
								?>
								 <tr bgcolor="#FFFFE8">
									 <td width="530" colspan="6" align="right"> Floor Total</td>

									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td width="70" align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>
								<?
								}
							}

							?>



							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td width="180" align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
							 <td width="70" align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td width="70" align="center"><? echo  $floor_arr[$value_c['floor_id']]; ?></td>
							 <td width="70" align="center"><? echo  $line_name; ?></td>

							 <td width="70" align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td width="70" align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{

									?>
									<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['product_qty'] ;?></td>
									<?

									}
							 ?>
							 <td width="70" align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>

							 </tr>
							<?

							$i++;
							$inhouse_floor[]=$value_c['floor_id'];
							$floor_id=$value_c['floor_id'];;
					}
					?>


                    		 <tr bgcolor="#FFFFE8">
									 <td width="530" colspan="6" align="right"> Floor Total</td>

									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td width="70" align="right"><? echo  $job_color_qnty_array[1][$floor_id]['color_total']; ?></td>
								 </tr>


                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th width="180"></th>
                             <th width="70"></th>
                             <th width="70"></th>
                             <th width="70"></th>
                             <th width="70"></th>
                             <th width="70">Total</th>

                             <?
                                    foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
		                                    ?>
		                                    <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
		                                    <?
                                        }
                                    }
                            ?>
                             <th width="70" align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                             </tr>
                          </tfoot>
					</table>
                <!-- ===================================== OUT BOUND ================================= -->
                <div style="text-align: center;"> <strong>Out Bound</strong></div>
                <table width="<? echo $outbound_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[3][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
			</table>
			<table width="<? echo $outbound_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2">
				<?

				$j=1;
				$inhouse_floor=array();
				foreach($production_details_arr[3] as $key_c=>$value_c)
					{
					if($prod_reso_allo==1)
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>



							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td width="180" align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                             <td width="70" align="center"><? echo  $supplier_arr[$value_c['serving_company']]; ?></td>
							 <td width="70" align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td width="70" align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td width="70" align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
									{

									?>
									<td width="60" align="right"><? echo $production_size_details_arr[3][$key_c][$key_s]['product_qty'] ;?></td>
									<?

									}
							 ?>
							 <td width="70" align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[3][$value_po][$value_c]; ?></td>

							 </tr>
							<?
							$j++;

					}
					?>

                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th width="180"></th>
                             <th width="70"></th>
                             <th width="70"></th>
                             <th width="70"></th>
                             <th width="70">Total</th>

                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[3][$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th width="70" align="right"><? echo  $job_color_qnty_array[3]['color_total']; ?></th>
                             </tr>
                             <tr bgcolor="<? // echo $bgcolor;?>">

                             <th colspan="5"> Grand Total</th>

                            		 <?
                            		 $grand_color_qty=0;
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
		                                    ?>
		                                    <th width="60" align="right"><? $grand_color_qty+=$grand_size_qty[$key_s]; echo $grand_size_qty[$key_s];?></th>
		                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $grand_color_qty; ?></th>
                             </tr>

                          </tfoot>

					</table>
				</div>
			</div>
			<!-- 3rd Print -->

			<?
			$tbl_width = 370+($size_total*60);

		?>
		<div id="data_panel" align="center" style="width:100%">
        	<script>
				function new_window3()
				{
					$(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('country_wise_breakdown_details').innerHTML);
					d.close();
					$(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window3()" />
	       	<div id="country_wise_breakdown_details" style="text-align: center;">
				<div style="text-align: center;"> <strong>Style : <? echo $sql_job[0][csf('style_ref_no')]; ?></strong>, &nbsp;
				<strong>Po Number: <? echo $order_number; ?></strong></div>
				<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2">
					<?
					$i=1;
					foreach($country_color_wise_arr as $country_key=>$country_data)
					{
						?>
						<thead>
							<tr>
		                        <th width="100">Country</th>
		                        <th width="100">Color</th>
		                        <th width="100">Size</th>
								<?
								foreach($job_size_array2[$order_number] as $key=>$value)
								{
									if($value !="")
									{
										?>
										<th width="60"><? echo $itemSizeArr[$value];?></th>
										<?
									}
								}
								?>
		                        <th width="70">Total Qty.</th>
							</tr>
						</thead>
						<?

						foreach($country_data as $color_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//if($value_c != "")
							//{
							?>
							 <tr bgcolor="<? echo $bgcolor;?>">
			                 <td width="100" rowspan="4" valign="middle" align="left" style="word-break: break-all;word-wrap: break-word;">
			                 <? echo  $country_arr[$country_key]."/".$country_code_arr[$country_key]; ?>
			                 </td>
							 <td width="100" align="center"><? echo $colorname_arr [$color_key]; ?></td>
							 <td width="100" align="left">Order Qnty</td>
							 <?
							 	$totalQty = 0;

								foreach($job_size_array2[$order_number] as $key_s=>$value_s)
								{

									?>
									<td width="60" align="right"><? echo $country_size_qty_arr[$country_key][$key_s]['order_qty'];?></td>
									<?
									$totalQty += $country_size_qty_arr[$country_key][$key_s]['order_qty'];
								}
							 ?>
							 <td width="70" align="right"><? echo  $totalQty; ?></td>

							 </tr>

							<tr>
							  	<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
								<td align="left">Plan To Cut</td>
									 <?
									 $pc_total = 0;
										foreach($job_size_array2[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_size_qty_arr[$country_key][$key_s]["plan_cut"];?></td>
												<?
												$pc_total += $country_size_qty_arr[$country_key][$key_s]["plan_cut"];
											}
										}
									?>
				                <td width="70" align="right"><? echo $pc_total; ?></td>
							</tr>
						  	<tr>
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 	<td  align="left">Finishing Qty</td>
									<?
										$cuttingTotal = 0;
										foreach($job_size_array2[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $packing_and_finshing_arr2[$country_key][$color_key];?></td>
												<?
												$cuttingTotal += $packing_and_finshing_arr2[$country_key][$key_s];
											}
										}
									?>
			                 	<td width="70" align="right"><? echo  $cuttingTotal; ?></td>
						 	</tr>

						  	<tr>
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 	<td  align="left">Cutting Balance</td>
								 <?
									foreach($job_size_array2[$order_number] as $key_s=>$value_s)
									{
										if($value_s !="")
										{
											?>
											<td width="60" align="right" title="Plan To Cut - Finishing Qty"><? echo $bal = $country_size_qty_arr[$country_key][$key_s]["plan_cut"] - $packing_and_finshing_arr2[$country_key][$key_s];?></td>
											<?
											$balance += $bal;
										}
									}
								?>
			                 	<td  align="right"><? echo  $balance; ?></td>
						 	</tr>
						 	<?
							$i++;
							//}
						}
					}
					?>
				</table>
				<br />


				<!-- 3rd print End -->

			<script type="text/javascript">
				setFilterGrid("html_search_1",-1);
				setFilterGrid("html_search_2",-1);
			</script>
	 </div>
	 <?
}



if($action=="ex_factory_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from  lib_company", "id", "company_name"  );
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";


	?>
    <div id="data_panel" align="center" style="width:100%">

    <?
		$ex_fac_date_con = ($today_total ==1) ? "and a.ex_factory_date='$insert_date'" : "";
		$ex_factory_sql="SELECT a.id,a.country_id,a.challan_no,a.po_break_down_id as order_id, a.item_number_id as item_id,a.ex_factory_date, c.color_number_id as color_id,m.source,c.size_number_id, c.color_number_id,m.delivery_floor_id,
			m.company_id,
	    sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date<='".$insert_date."' THEN b.production_qnty ELSE 0 END) AS exf_qnty_today,
		sum(CASE WHEN m.entry_form=85 and a.ex_factory_date='".$insert_date."' THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_today,
		sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date<'".$insert_date."' THEN b.production_qnty ELSE 0 END) AS exf_qnty_pre,
		sum(CASE WHEN m.entry_form=85 and a.ex_factory_date<'".$insert_date."' THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_pre
		from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
		where  a.po_break_down_id = $order_id and a.item_number_id=$item_id and c.color_number_id=$color_id $ex_fac_date_con and  m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0
		group by  a.id,a.country_id,a.po_break_down_id ,a.challan_no, a.item_number_id,a.ex_factory_date, c.color_number_id ,m.source,c.size_number_id, c.color_number_id,m.delivery_floor_id,m.company_id";
	// echo $ex_factory_sql;
	$ex_factory_sql_result=sql_select($ex_factory_sql);
	// echo "<pre>";
	// print_r($ex_factory_sql_result);
	$production_data = array();
		//echo $sql;die;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$production_details_arr=array();
		$production_size_details_arr=array();
		$floor_qty_arr=array();
		$grand_size_qty=array();
		//$grand_color_qty=array();
		foreach( $ex_factory_sql_result as $row)
		{
			if($row[csf('source')]==1)
			{
				$job_size_array[$row[csf('source')]][$row[csf('order_id')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('source')]][$row[csf('size_number_id')]]+=$row[csf('exf_qnty_today')];
				$floor_qty_arr[$row[csf('source')]][$row[csf('size_number_id')]]+=$row[csf('exf_qnty_today')];
				$job_color_array[$row[csf('source')]][$row[csf('order_id')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('source')]]['color_total']+=$row[csf('exf_qnty_today')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['delivery_floor_id']=$row[csf('delivery_floor_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['ex_factory_date']=$row[csf('ex_factory_date')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['exf_qnty_today']+=$row[csf('exf_qnty_today')];
				$production_size_details_arr[$row[csf('source')]][$row[csf('id')]][$row[csf('size_number_id')]]['exf_qnty_today']+=$row[csf('exf_qnty_today')];
			}
			else
			{
				$job_size_array[$row[csf('source')]][$row[csf('order_id')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('source')]][$row[csf('size_number_id')]]+=$row[csf('exf_qnty_today')];
				$job_color_array[$row[csf('source')]][$row[csf('order_id')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('source')]]['color_total']+=$row[csf('exf_qnty_today')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['company_id']=$row[csf('company_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['delivery_floor_id']=$row[csf('delivery_floor_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['ex_factory_date']=$row[csf('ex_factory_date')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['exf_qnty_today']+=$row[csf('exf_qnty_today')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
				$production_size_details_arr[$row[csf('source')]][$row[csf('id')]][$row[csf('size_number_id')]]['exf_qnty_today']+=$row[csf('exf_qnty_today')];
			}
			$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('exf_qnty_today')];
			$grand_color_qty+=$row[csf('exf_qnty_today')];
		}
		// print_r($production_size_details_arr);
		// print_r($job_size_array[1]);
		 $job_color_tot=0;
		 ?>
        <div id="data_panel" align="" style="width:100%">
			<label> <strong>In House </strong><label/>

			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Unit Name</th>
                        <th width="70">Line No</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[1][$order_id] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?

				$i=1;
				$inhouse_floor=array();
				// echo "<pre>";
				// print_r($production_details_arr);
				foreach($production_details_arr[1] as $key_c=>$value_c)
					{

					if($prod_reso_allo==1)
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($i!=1)
						{

							if(!in_array($value_c['delivery_floor_id'],$inhouse_floor))
								{
								?>
								 <tr bgcolor="#FFFFE8">
									 <td colspan="6" align="right"> Floor Total</td>

									 <? //print_r($job_size_array[1][$order_id]);
											foreach($job_size_array[1][$order_id] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>
								<?
								}
							}

							?>



							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  $floor_arr[$value_c['delivery_floor_id']]; ?></td>
							 <td align="center"><? echo  $line_name; ?></td>

							 <td align="center"><? echo  change_date_format($value_c['ex_factory_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[1][$order_id] as $key_s=>$value_s)
									{

									?>
									<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['exf_qnty_today'] ;?></td>
									<?

									}
							 ?>
							 <td align="right"><? echo  $value_c['exf_qnty_today']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>

							 </tr>
							<?

							$i++;
							$inhouse_floor[]=$value_c['delivery_floor_id'];
							$floor_id=$value_c['delivery_floor_id'];;
					}
					?>


                    		 <tr bgcolor="#FFFFE8">
									 <td colspan="6" align="right"> Floor Total</td>

									 <?
											foreach($job_size_array[1][$order_id] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>


                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th>Total</th>

                             <?
                                    foreach($job_size_array[1][$order_id] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
                                    <?
                                        }
                                    }
                            ?>
                             <th align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                             </tr>
                          </tfoot>
					</table>
                   <!-- =================================== Out Bound Start ==========================================-->
                <label > <strong>Out Bound:<strong><label/>
                <table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						// echo "<pre>";
						// print_r($job_size_array[2]);
						foreach($job_size_array[2][$order_id] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?

				$j=1;
				$inhouse_floor=array();
				foreach($production_details_arr[2] as $key_c=>$value_c)
					{
					if($prod_reso_allo==1)
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>



							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                             <td align="center"><? echo  $company_arr[$value_c['company_id']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['ex_factory_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[2][$order_id] as $key_s=>$value_s)
									{
									// echo $key_c;
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[2][$key_c][$key_s]['exf_qnty_today'] ;?></td>
									<?

									}
							 ?>
							 <td align="right"><? echo  $value_c['exf_qnty_today']; $job_color_tot+=$job_color_qnty_array[2][$value_po][$value_c]; ?></td>

							 </tr>
							<?
							$j++;

					}
					?>

                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                              <th></th>
                             <th>Total</th>

                            		 <?
                                    foreach($job_size_array[2][$order_id] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[2][$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $job_color_qnty_array[2]['color_total']; ?></th>
                             </tr>
                             <tr bgcolor="<? // echo $bgcolor;?>">

                             <th colspan="5"> Grand Total</th>

                            		 <?
                                    foreach($job_size_array[2][$order_id] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $grand_size_qty[$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $grand_color_qty; ?></th>
                             </tr>

                          </tfoot>

					</table>
	 </div>
	 <?
}


if($action=="cutting_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=='') $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=='') $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";

	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id, sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no ,a.excess_cut from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id,a.excess_cut");

	$sql_color_size=sql_select("SELECT d.country_id, d.color_number_id,d.size_number_id, sum(d.plan_cut_qnty) as plan_cut, sum(d.order_quantity) as order_qty  from wo_po_break_down a, wo_po_color_size_breakdown d where a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1  and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and d.color_number_id=$color_id  group by d.country_id,d.color_number_id,d.size_number_id ");
	$country_size_qty_arr = array();
	$country_color_total_arr = array();
	foreach($sql_color_size as $key=>$vals)
	{
		$color_size_wise_arr[$vals[csf("size_number_id")]]["order_qty"] +=$vals[csf("order_qty")];
		$color_size_wise_arr[$vals[csf("size_number_id")]]["plan_cut"] +=$vals[csf("plan_cut")];
		$color_total_arr["order_qty_color_total"] +=$vals[csf("order_qty")];
		$color_total_arr["plan_cut_color_total"] +=$vals[csf("plan_cut")];
		//==============================================
		$country_size_qty_arr[$vals[csf("country_id")]][$vals[csf("size_number_id")]]["order_qty"] +=$vals[csf("order_qty")];
		$country_size_qty_arr[$vals[csf("country_id")]][$vals[csf("size_number_id")]]["plan_cut"] +=$vals[csf("plan_cut")];
		$country_color_total_arr[$vals[csf("country_id")]]["order_qty_color_total"] +=$vals[csf("order_qty")];
		$country_color_total_arr[$vals[csf("country_id")]]["plan_cut_color_total"] +=$vals[csf("plan_cut")];
 	}


	$sql="SELECT  d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,d.table_no,
	d.floor_id,d.production_date,f.country_id,d.serving_company,d.cut_no, d.po_break_down_id, e.bundle_no FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f WHERE d.po_break_down_id=$order_id  and d.id=e.mst_id and d.po_break_down_id=f.po_break_down_id and e.color_size_break_down_id=f.id and f.po_break_down_id=$order_id and d.production_type=$type and e.production_type=$type and f.color_number_id=$color_id and e.is_deleted =0 and e.status_active =1 and f.is_deleted =0 and f.status_active =1 and d.is_deleted =0 and d.status_active=1 $insert_cond $item_cond2 order by f.size_order,d.production_date";
	//  echo $sql;die;
	$sql_order_cut="SELECT a.cutting_no,b.order_cut_no,b.plies,b.remarks,c.order_id,b.gmt_item_id as item_id,b.color_id,c.bundle_no  FROM ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c WHERE  a.id=b.mst_id and b.id=c.dtls_id  and a.id=c.mst_id and b.color_id=$color_id and c.order_id=$order_id and a.is_deleted =0 and a.status_active =1 and c.is_deleted =0 and c.status_active =1 and b.is_deleted =0 and b.status_active=1 order by b.order_cut_no ASC";
	// echo $sql_order_cut;

	$sql_order_cut_one=sql_select($sql_order_cut);
	$order_cut_array=array();
	foreach($sql_order_cut_one as $value){
		$bundle_order_cut_array [$value['BUNDLE_NO']] = $value['ORDER_CUT_NO'];
		$order_cut_array[$value[csf('order_id')]][$value[csf('color_id')]][$value[csf("cutting_no")]][$value[csf("order_cut_no")]]["plies"]=$value[csf("plies")];
		$order_cut_array[$value[csf('order_id')]][$value[csf('color_id')]][$value[csf("cutting_no")]][$value[csf("order_cut_no")]]["remarks"]=$value[csf("remarks")];


	}
    //  echo "<pre>";
	//  print_r($order_cut_array);
	//  die;
	// echo $sql_order_cut;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	$production_details_arr=array();
	$production_size_details_arr=array();
	$cutting_qty_arr=array();
	$country_color_wise_arr=array();
	$country_cutting_qty_arr=array();
	foreach( $sql_data as $row)
	{
		$order_cut  = $bundle_order_cut_array[$row['BUNDLE_NO']];

		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array['color_total']+=$row[csf('product_qty')];
		//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$production_details_arr[$row['CUT_NO']][$order_cut]['country']=$row[csf('country_id')];
		$production_details_arr[$row['CUT_NO']][$order_cut]['color']=$row[csf('color_number_id')];
		$production_details_arr[$row['CUT_NO']][$order_cut]['order_id']=$row[csf('po_break_down_id')];
		$production_details_arr[$row['CUT_NO']][$order_cut]['production_date']=$row[csf('production_date')];
		$production_details_arr[$row['CUT_NO']][$order_cut]['challan_no']=$row[csf('challan_no')];
		$production_details_arr[$row['CUT_NO']][$order_cut]['serving_company']=$row[csf('serving_company')];
		$production_details_arr[$row['CUT_NO']][$order_cut]['cut_no']=$row['CUT_NO'];
		$production_details_arr[$row['CUT_NO']][$order_cut]['floor_id']=$row[csf('floor_id')];
		$production_details_arr[$row['CUT_NO']][$order_cut]['table_no']=$row[csf('table_no')];
		$production_details_arr[$row['CUT_NO']][$order_cut]['product_qty']+=$row[csf('product_qty')];
		$cutting_qty_arr[$row[csf('country_id')]]+=$row[csf('product_qty')];
		$production_size_details_arr[$row['CUT_NO']][$order_cut][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
		//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];

		$country_color_wise_arr[$row[csf('country_id')]][$row[csf('color_number_id')]]['country_qty']+=$row[csf('product_qty')];
		$country_cutting_qty_arr[$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
	}
	// echo '<pre>';
	// print_r($job_size_qnty_array);
	// echo '</pre>';
	?>
    <div id="data_panel" align="center" style="width:920px">
    	<script>
    		function new_window_all()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				// d.write(document.getElementById('details_reports').innerHTML);

				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
                '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;position: !important;-webkit-transform: rotate(-90deg);} </style><body>'+document.getElementById('details_reports').innerHTML+'</body</html>');
				d.close();
				$(".flt").css("display","block");
			}
			function new_window()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('order_details').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<!-- <input type="button" value="Print All" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window_all()" /> -->
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window_all()" />
	</div>
	<hr>
	<div id="details_reports">
		<div id="order_details">
		    <div style="width:920px; margin: 0 auto;">
		    	 <table width="920px" align="center" border="" rules="all" class="" >
			          <thead>
				              <tr>
					              <th colspan="9" align="center"><? echo $company_arr[$company_id]; ?></th>

				              </tr>
				              <tr>
					              <th colspan="9" align="center">Cutting report job card wise</th>

				              </tr>
			          </thead>
		          </table>
		    </div>
		    <br>
	       	<div  style="width:920px;margin: 0 auto;">
	         	<table width="900px" align="center" border="1" rules="all" class="rpt_table" >
		          	<thead>
		              	<tr>
			              	<th width="200">Buyer Name</th>
			              	<th width="100">Job No </th>
			              	<th width="100">Style Reff.</th>
			              	<th width="100">Country</th>
			              	<th width="100">Order No</th>
			              	<th width="100">Ship Date</th>
			             	<th width="100">Order Qty</th>
			              	<th width="100">Cutting Qty</th>
		             	</tr>
		          	</thead>
	          		<tbody>
			          	<?
					    foreach($sql_job as $row)
						{
						 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
			                <tr bgcolor="<? echo $bgcolor;?>">
			                   <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
			                   <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
			                   <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
			                   <td align="left"><p><? echo $country_arr[$row[csf('country_id')]]."/".$country_code_arr[$row[csf('country_id')]]; ?></p></td>
			                   <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
			                   <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
			                    <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')]; ?></td>
			                    <td align="right"><? echo $cutting_qty_arr[$row[csf('country_id')]]; $total_cutting_qty+=$cutting_qty_arr[$row[csf('country_id')]]; ?></td>
			                </tr>
			                <?
						}
					  	?>
	          		</tbody>
	           		<tfoot>
		               	<tr>
			               <th colspan="6">Total</th>
			               <th><? echo $total_qty; ?></th>
			               <th><? echo $total_cutting_qty; ?></th>
		               	</tr>
	            	</tfoot>
	       		</table>
	      	</div>
	    </div>
       	<br />
	    <?
			// print_r($production_size_details_arr);die;
			$size_total = count($job_size_array[$order_number]);
			$tbl_width = 950+($size_total*60);

			 $job_color_tot=0;
			 ?>
	        <div id="data_panel" align="center" style="width:100%">
	        	<script>
					function new_window2()
					{
						$(".flt").css("display","none");
						var w = window.open("Surprise", "#");
						var d = w.document.open();
						d.write(document.getElementById('breakdown_details').innerHTML);
						d.close();
						$(".flt").css("display","block");
					}
				</script>
				<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window2()" />
		       	<div id="breakdown_details" style="text-align: center;">
					<div style="text-align: center;"> <strong>Style : <? echo $sql_job[0][csf('style_ref_no')]; ?></strong>, &nbsp;
					 <strong>Po Number: <? echo $order_number; ?></strong></div>
					<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" >
						<thead>
							<tr>
								<th width="120">Cutting Company</th>
								<th width="100">Cutting No</th>
								<th width="100">Color</th>
		                        <th width="70">Country</th>
								<th width="70">Floor</th>
								<th width="70">Table</th>
								<th width="70">No of Pile</th>
		                        <th width="70">Date</th>
								<th width="70">Order Cut No</th>
		                        <th width="70">Challan</th>
								<?
								foreach($job_size_array[$order_number] as $key=>$value)
								{
									if($value !="")
									{
										?>
										<th width="60"><? echo $itemSizeArr[$value];?></th>
										<?
									}
								}
								?>
		                        <th width="70">Color Total</th>
								<th width="70">Remarks</th>
							</tr>
						</thead>
					</table>
					<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_1">
						<?
						$i=1;


						foreach($production_details_arr as $key_c=>$cut_arr)
						{
							ksort($cut_arr);
							foreach ($cut_arr as $order_cut => $value_c)
							{
								$odr_cut_arr = $order_cut_array[$value_c['order_id']][$value_c['color']][$value_c['cut_no']][$order_cut];
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>">
									<td width="120" align="center"><? echo $company_arr[$value_c['serving_company']]; ?></td>
									<td width="100" align="center"><? echo  $value_c['cut_no']; ?></td>
									<td width="100" align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
									<td width="70" align="center"><? echo  $country_arr[$value_c['country']]."/".$country_code_arr[$value_c['country']]; ?></td>
										<?php
											$floorn_name= return_field_value("floor_name","lib_prod_floor","id='".$value_c['floor_id']."'");
											$table_name= return_field_value("table_name","lib_table_entry","id='".$value_c['table_no']."'");
										?>
									<td width="70" align="center"><? echo $floorn_name; ?></td>
									<td width="70" align="center"><? echo $table_name; ?></td>
									<td width="70" align="center"><?= $odr_cut_arr['plies'];?></td>
									<td width="70" align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
									<td width="70" align="center"><?= $order_cut;  ?></td>
									<td width="70" align="right"></td>
									<?
											foreach($job_size_array[$order_number] as $key_s=>$value_s)
											{

												?>
												<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$order_cut][$key_s]['product_qty']??0 ;?></td>
												<?

											}
									?>
									<td width="70" align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
									<td width="70" align="right"><?= $odr_cut_arr['remarks']; ?></td>

								</tr>
								<?
								$i++;
							}
						}
						?>
						<tfoot>
							 <tr>
								 <th colspan="10" width="530" align="left">Total</th>
									 <?
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
												<?
											}
										}
									?>
				                 <th width="70" align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
								 <th width="70" align="right"><? ?></th>
							 </tr>

							  <tr>
								 <th width="530" colspan="10" align="left">Plan To Cut (AVG <?  echo $sql_job[0][csf("excess_cut")];?>)%</th>
									 <?
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
										?>
										<th width="60" align="right"><? echo $color_size_wise_arr[$key_s]["plan_cut"];?></th>
										<?
											}
										}
									?>
				                 <th width="70" align="right"><? echo   $color_total_arr["plan_cut_color_total"]; ?></th>
								 <th width="70" align="right"><? ?></th>
							 </tr>

						  	<tr>
							 <th width="530" colspan="10" align="left">Cutting Balance</th>
								 <?
									foreach($job_size_array[$order_number] as $key_s=>$value_s)
									{
										if($value_s !="")
										{
											$size_bal=$color_size_wise_arr[$key_s]["plan_cut"]- $job_size_qnty_array[$key_s];
											$color_cond=($size_bal>=0)? " color:black; " : " color:crimson;";
											$color_bal_tot=$color_total_arr["plan_cut_color_total"]- $job_color_qnty_array['color_total'];
											$color_bal_cond=($color_bal_tot>=0)? " color:black; " : "color:crimson;";
											?>
											<th style="<? echo $color_cond;?>" width="60" align="right"><? echo $size_bal;?></th>
											<?
										}
									}
								?>
			                 <th width="70" style="<? echo $color_bal_cond;?>" align="right"><? echo  $color_bal_tot; ?></th>
							 <th width="70" align="right"><? ?></th>
						 	</tr>
						  	<tr>
							 	<th width="530" colspan="10" align="left">Order Qty</th>
									<?
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
										?>
										<th width="60" align="right"><? echo $color_size_wise_arr[$key_s]["order_qty"];?></th>
										<?
											}
										}
									?>
			                 	<th width="70" align="right"><? echo  $color_total_arr["order_qty_color_total"]; ?></th>
							    <th width="70" align="right"><? ?></th>
						 	</tr>

					  	</tfoot>
					</table>
					<br />
		     	</div>
			</div>
		<script type="text/javascript">
		 	setFilterGrid("html_search_1",-1);
		</script>
		<!-- ======================================== -->
		<?
			$tbl_width = 370+($size_total*60);
		?>
		<div id="data_panel" align="center" style="width:100%">
        	<script>
				function new_window3()
				{
					$(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('country_wise_breakdown_details').innerHTML);
					d.close();
					$(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window3()" />
	       	<div id="country_wise_breakdown_details" style="text-align: center;">
				<div style="text-align: center;"> <strong>Style : <? echo $sql_job[0][csf('style_ref_no')]; ?></strong>, &nbsp;
				<strong>Po Number: <? echo $order_number; ?></strong></div>
				<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2">
					<?
					$i=1;
					foreach($country_color_wise_arr as $country_key=>$country_data)
					{
						?>
						<thead>
							<tr>
		                        <th width="100">Country</th>
		                        <th width="100">Color</th>
		                        <th width="100">Size</th>
								<?
								foreach($job_size_array[$order_number] as $key=>$value)
								{
									if($value !="")
									{
										?>
										<th width="60"><? echo $itemSizeArr[$value];?></th>
										<?
									}
								}
								?>
		                        <th width="70">Total Qty.</th>
							</tr>
						</thead>
						<?
						foreach($country_data as $color_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//if($value_c != "")
							//{
							?>
							 <tr bgcolor="<? echo $bgcolor;?>">
			                 <td width="100" rowspan="4" valign="middle" align="left" style="word-break: break-all;word-wrap: break-word;">
			                 <? echo  $country_arr[$country_key]."/".$country_code_arr[$country_key]; ?>
			                 </td>
							 <td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 <td width="100" align="left">Order Qnty</td>
							 <?
							 	$totalQty = 0;
								foreach($job_size_array[$order_number] as $key_s=>$value_s)
								{

									?>
									<td width="60" align="right"><? echo $country_size_qty_arr[$country_key][$key_s]['order_qty'];?></td>
									<?
									$totalQty += $country_size_qty_arr[$country_key][$key_s]['order_qty'];
								}
							 ?>
							 <td width="70" align="right"><? echo  $totalQty; ?></td>

							 </tr>

							<tr>
							  	<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
								<td align="left">Plan To Cut</td>
									 <?
									 $pc_total = 0;
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_size_qty_arr[$country_key][$key_s]["plan_cut"];?></td>
												<?
												$pc_total += $country_size_qty_arr[$country_key][$key_s]["plan_cut"];
											}
										}
									?>
				                <td width="70" align="right"><? echo $pc_total; ?></td>
							</tr>
						  	<tr>
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 	<td  align="left">Cutting Qty</td>
									<?
										$cuttingTotal = 0;
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_cutting_qty_arr[$country_key][$key_s];?></td>
												<?
												$cuttingTotal += $country_cutting_qty_arr[$country_key][$key_s];
											}
										}
									?>
			                 	<td width="70" align="right"><? echo  $cuttingTotal; ?></td>
						 	</tr>

						  	<tr>
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 	<td  align="left">Cutting Balance</td>
								 <?
									foreach($job_size_array[$order_number] as $key_s=>$value_s)
									{
										if($value_s !="")
										{
											?>
											<td width="60" align="right"><? echo $bal = $country_size_qty_arr[$country_key][$key_s]["plan_cut"] - $country_cutting_qty_arr[$country_key][$key_s];?></td>
											<?
											$balance += $bal;
										}
									}
								?>
			                 	<td  align="right"><? echo  $balance; ?></td>
						 	</tr>
						 	<?
							$i++;
							//}
						}
					}
					?>
				</table>
				<br />
	     	</div>
		</div>
	</div>
	<?
}
if($action=="cutting_popup_one")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	?>
	<?
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	if (str_replace("'","",$job_no)!="") $job_cond="and b.job_no_mst='".$job_no."'";
     $insert_cond=" and d.production_date='$insert_date'";



	$sql="SELECT d.serving_company,d.challan_no,d.cut_no,c.production_qnty,b.color_number_id,b.size_number_id,a.po_number,d.production_date from wo_po_break_down a,wo_po_color_size_breakdown b, pro_garments_production_dtls c,pro_garments_production_mst d where a.id=b.po_break_down_id and c.mst_id=d.id and b.po_break_down_id=d.po_break_down_id and
	c.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=1 and d.production_type=1  $job_cond $insert_cond";
	//   echo $sql;


	$cutting_sql=sql_select($sql);
	if(count($cutting_sql)==0)
	{
		echo "No data found";
		die();
	}
	$cutting_arr=array();
	$cutting_size_arr=array();
	$color_span_arr=array();
	$color_total_arr=array();
	$po_total_arr=array();



	foreach($cutting_sql as $val)
	{
		$cutting_size_arr[$val[csf('size_number_id')]]=$val[csf('size_number_id')];
		$cutting_arr[$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('challan_no')]]['serving_company']=$val[csf('serving_company')];
		$cutting_arr[$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('challan_no')]]['cut_no']=$val[csf('cut_no')];
		$cutting_arr[$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('challan_no')]]['po_number']=$val[csf('po_number')];
		$cutting_arr[$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('challan_no')]][$val[csf('size_number_id')]]['qty']+=$val[csf('production_qnty')];
		$color_total_arr[$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('production_qnty')];
		$po_total_arr[$val[csf('size_number_id')]]+=$val[csf('production_qnty')];
		// $cutting_arr[$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('challan_no')]]['rowspan']+=1;

	}

	// echo '<pre>';
	// print_r($cutting_arr);
	// echo '</pre>';
	// echo '<pre>';
	// print_r($grand_total_arr);
	// echo '</pre>';
	// echo '<pre>';
	// print_r($color_po_arr);
	// echo '</pre>';
	// echo '<pre>';
	// print_r($color_qnty_array);
	// echo '</pre>';
	// echo '<pre>';
	// print_r($po_total_arr);
	// echo '</pre>';
	$rowspan_arr = array();
	$po_arr=array();
	foreach($cutting_arr as $color_id=>$color_data)
	{

		foreach($color_data as $prod_date=>$prod_data)
		{
			foreach($prod_data as $challan_no=>$row)
			{
				$rowspan_arr[$color_id]++;
				$po_arr[$color_id][$prod_date][$challan_no]['po_number']++;
				$company_rowspan=count($row);



			}
		}
	}
	$company_rowspan+=count($rowspan_arr);
	// echo $rowCount;
	// die();
	// echo count($rowspan_arr);

	  $tbl_width=700+(count($cutting_size_arr)*60);

    $sql_total="SELECT a.production_qnty,b.color_number_id,b.size_number_id from pro_garments_production_dtls a,wo_po_color_size_breakdown b,pro_garments_production_mst c where a.color_size_break_down_id=b.id and b.po_break_down_id=c.po_break_down_id and a.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $job_cond";
	// echo $sql_total;

	$sql_job_wise=sql_select($sql_total);
	$job_total_arr=array();
	$job_size_arr=array();
	$color_size_arr=array();
	$size_total_arr=array();

	foreach($sql_job_wise as $row)
	{
		$job_size_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
        $color_size_arr[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_total_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
        $size_total_arr[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
	}
	// echo '<pre>';
	// print_r($job_total_arr);
	// echo '</pre>';
	// echo '<pre>';
	// print_r($color_size_arr);
	// echo '</pre>';
	$sql_order_size=sql_select("SELECT a.id, b.color_number_id,b.size_number_id, b.order_quantity as order_qty from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0   $job_cond   ");
	//  echo $sql_order="SELECT a.po_number, b.color_number_id,b.size_number_id, b.order_quantity as order_qty from wo_po_break_down a, wo_po_color_size_breakdown b,pro_garments_production_mst d where a.id=b.po_break_down_id and b.po_break_down_id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $job_cond $insert_cond ";
	// echo $sql_order="SELECT a.id, b.color_number_id,b.size_number_id, b.order_quantity as order_qty from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0  $job_cond ";
	$size_order_quantity=array();
	$size_total_qty_arr=array();
	// $order_size_quantity_arr=array();

	foreach($sql_order_size as $row)
	{
		// $order_size_quantity_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$size_order_quantity[$row[csf('size_number_id')]]['order_qty']+=$row[csf('order_qty')];
		$size_total_qty_arr['color_total']+=$row[csf('order_qty')];
	}
	// echo '<pre>';
	// print_r($order_size_quantity_arr);
	// echo '</pre>';
	$buyer_sql="SELECT a.buyer_name,a.style_ref_no from wo_po_details_master a,wo_po_color_size_breakdown b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond";

	// echo $buyer_sql;
	$buyer_arr=array();
	$buyer_data=sql_select($buyer_sql);

	foreach($buyer_data as $val)
	{
		$buyer_arr[$val[csf('buyer_name')]]=$val[csf('buyer_name')];
		$buyer_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
	}



  ?>

	       <div id="data_panel" align="center" style="width:100%">
        	<script>
				function new_window6()
				{
					$(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('job_today').innerHTML);
					d.close();
					$(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window6()" />
			<div id="job_today" style="text-align: center;">
			<div style="text-align: center;"> <strong>Buyer : <? echo $buyer_short_library[$buyer_arr[$val[csf('buyer_name')]]];  ?></strong>, &nbsp;
				<strong>Style: <? echo $buyer_arr[$val[csf('style_ref_no')]]  ?></strong></div>
				<div style="width:<?=$tbl_width+20;?>px;" align="center">
				<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2">
					<thead>
						<tr>
							<th width="100">Cutting Company</th>
							<th width="100">Date</th>
							<th width="100">Cutting No</th>
							<th width="100">Challan</th>
							<th width="100">Color</th>
							<th width="100">Po No</th>
							<?
								foreach($cutting_size_arr as $size_id=>$size_data)
								{
							?>
								<th width="60"><?
								echo $size_library[$size_id];
								?></th>
							<?
							}
							?>
							<th width="100">Total</th>
					    </tr>
					</thead>

				   <tbody>
					<?
					 $i=1;

                      foreach($cutting_arr as $color_id=>$color_data)
					  {
						 $clr = 0;
						 foreach($color_data as $prod_date=>$prod_data)
						 {
                           foreach($prod_data as $challan_no=>$row)
						   {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


								?>
								<tr bgcolor="<? echo $bgcolor;?>">
								<?
								if($i==1)
								{
								?>
						    	<td width="100" valign="middle" rowspan="<?=$company_rowspan;?>"><? echo $company_arr[$row['serving_company']];?></td>
								<?

								}
                            	?>
								<td width="100"><? echo $prod_date;?></td>
								<td width="100"><? echo $row['cut_no'];?></td>
								<td width="100"><? echo $challan_no;?></td>
								<?if($clr==0){?>
								<td rowspan="<?=$rowspan_arr[$color_id];?>" width="100" align="center"><? echo $color_library[$color_id];?></td>
								<?$clr++;}
								?>


								<td width="100"><? echo $row['po_number'];?></td>
								<?
								$total_product_qty=0;
								foreach($cutting_size_arr as $size_id=>$size_data)
								{
								?>
									<td width="60" align="right">
										<?
										echo $row[$size_id]['qty'];
										$total_product_qty+=$row[$size_id]['qty'];
										?>
									</td>
								<?
								}
								?>
									<td width="100" align="right"><?  echo $total_product_qty; ?></td>
									</tr>
								<?
								$i++;
    						}
				        }
					   ?>
							<tr>
								<th colspan="5" align="right">Total</th>

								<?
								$grand_color_qty=0;
								foreach($cutting_size_arr as $size_id=>$val)
								{
									if($val !="")
									{
									?>
									<th width="60" align="right"><? echo $color_total_arr[$color_id][$size_id];$grand_color_qty+=$color_total_arr[$color_id][$size_id];?></th>
									<?
									}
								}
							?>
								<th  align="right"><? echo $grand_color_qty;?></th>

							</tr>
							<?
					}
					?>

				  </tbody>
				 <tfoot>
				    <tr>
					<th colspan="6"  width="700" align="left">Today PO Total</th>
					<?
					$po_wise_total=0;
					foreach($cutting_size_arr as $size_id=>$val)
					{
						if($val !="")
						{
							?>
							<th width="60" align="right"><? echo $po_total_arr[$size_id]; $po_wise_total+=$po_total_arr[$size_id]; ?></th>
							<?
						}
					}
					?>
					 <th align="right"><? echo $po_wise_total;?></th>
				     </tr>
					 <?
						foreach($color_size_arr as $color_id=>$color_data)
						{
							?>
							<tr>
							<th colspan="6"  width="700" align="left"><?  echo $color_library[$color_id];?></th>
							<?
							$job_wise_total=0;
							foreach($job_size_arr as $size_id=>$val)
							{
								if($val !="")
								{
									?>
									<th width="60" align="right"><? echo $job_total_arr[$color_id][$size_id]; $job_wise_total+=$job_total_arr[$color_id][$size_id]; ?></th>
									<?
								}
							}
							?>
							 <th align="right"><? echo $job_wise_total;?></th>

							</tr>
							<?
						}

					?>
					<tr>
					<th colspan="6"  width="700" align="left">Grand Total</th>
						<?
							$size_wise_total=0;
							foreach($cutting_size_arr as $size_id=>$val)
							{
								if($val !="")
								{
									?>
									<th width="60" align="right"><? echo $size_total_arr[$size_id];$size_wise_total+=$size_total_arr[$size_id]?></th>
									<?
								}
							}
						?>
					  <th align="right"><? echo $size_wise_total;?></th>
					</tr>
						<tr>
						<th colspan="6"  width="700" align="left" >Plan to Cut(AVG 2%)</th>
						<?
						   $total_plan_cut=0;
							foreach($cutting_size_arr as $size_id=>$val)
							{
								if($val !="")
								{
									?>
									<th width="60" align="right"><? $plan_cut=($size_order_quantity[$size_id]['order_qty']*0.02)+$size_order_quantity[$size_id]['order_qty']; echo $plan_cut;$total_plan_cut+=$plan_cut;?></th>
									<?
								}
							}
						?>
						 <th align="right"><? echo $total_plan_cut;?></th>
						</tr>
						<tr>
						<th colspan="6"  width="700" align="left" >Order Qty</th>
						<?
						$total_size_order_quantity=0;
						foreach($cutting_size_arr as $size_id=>$val)
						{

							if($val !="")
							{
								?>
								<th width="60" align="right"><? echo $size_order_quantity[$size_id]['order_qty'];$total_size_order_quantity+=$size_order_quantity[$size_id]['order_qty'];?></th>
								<?
							}
						}
						?>
						 <th align="right"><? echo $total_size_order_quantity;?></th>
						</tr>
						<tr>
						<th colspan="6"  width="700" align="left" title="Grand Total-Order Qty">Cutting Balance</th>
						<?
							$total_balance=0;
							foreach($cutting_size_arr as $size_id=>$val)
							{
								if($val !="")
								{
									?>
									<th width="60" align="right"><?  $balance=$size_total_arr[$size_id]-$size_order_quantity[$size_id]['order_qty']; echo $balance; $total_balance+=$balance; ?></th>
									<?
								}
							}
						?>
						 <th align="right"><? echo $total_balance;?></th>

						</tr>

				</tfoot>


			</table>
			</div>




	<?
}

if($action=="job_total_cutting_popup_one")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	// $data=explode('*',$data);
	// print_r($data);
	?>
	<?

	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$country_arr=return_library_array( "select id, country_name from   lib_country", "id", "country_name");
	$country_code_arr=return_library_array( "select id, short_name from   lib_country", "id", "short_name");
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );


	if (str_replace("'","",$job_no)!="") $job_cond="and c.job_no_mst='".$job_no."'";


	$sql="SELECT a.serving_company,a.cut_no,c.color_number_id,c.country_id,a.production_date,b.production_qnty as product_qty,a.challan_no,c.size_number_id,d.buyer_name,d.style_ref_no,e.po_number from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c,wo_po_details_master d,wo_po_break_down e where a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and c.job_id=d.id and d.id=e.job_id and e.id=c.po_break_down_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.production_type=1 and b.production_type=1  $job_cond order by a.production_date desc,a.cut_no,a.challan_no";

    // echo $sql;
	$sql_color_size=sql_select("SELECT  c.color_number_id,c.size_number_id, c.order_quantity as order_qty from wo_po_break_down a, wo_po_color_size_breakdown c where a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1  and c.status_active=1 and c.is_deleted=0  $job_cond  ");
	// $sql_color="SELECT  c.color_number_id,c.size_number_id, sum(c.plan_cut_qnty) as plan_cut, c.order_quantity as order_qty  from wo_po_break_down a, wo_po_color_size_breakdown c where a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1  and c.status_active=1 and c.is_deleted=0  $job_cond  group by c.country_id,c.color_number_id,c.size_number_id, c.order_quantity ";
	//  echo $sql_color;

	  	$sql_main=sql_select($sql);
		if(count($sql_main)==0)
		{
			echo "No Data found";
			die();
		}
       $job_wise_cutting_arr=array();
	   $job_size_arr=array();
	//    $total_product_qty=array();
	   $job_wise_qnty_arr=array();
	   $buyer_arr=array();
	   $style_ref_arr=array();
	   $job_wise_color_qnty_arr=array();
	   $po_number_array=array();

	   foreach($sql_main as $val)
	   {
		  $job_size_arr[$val[csf('size_number_id')]]=$val[csf('size_number_id')];
		  $job_wise_cutting_arr[$val[csf('color_number_id')]][$val[csf('production_date')]]['serving_company']=$val[csf('serving_company')];
		  $job_wise_cutting_arr[$val[csf('color_number_id')]][$val[csf('production_date')]]['cut_no'].=$val[csf('cut_no')].",";
		  $job_wise_cutting_arr[$val[csf('color_number_id')]][$val[csf('production_date')]]['country'].=$country_arr[$val[csf('country_id')]].",";
		  $job_wise_cutting_arr[$val[csf('color_number_id')]][$val[csf('production_date')]]['challan_no'].=$val[csf('challan_no')].",";
		  $job_wise_cutting_arr[$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('size_number_id')]]['product_qty']+=$val[csf('product_qty')];
          $job_wise_qnty_arr[$val[csf('size_number_id')]]+=$val[csf('product_qty')];
		  $buyer_arr[$val[csf('buyer_name')]]=$val[csf('buyer_name')];
		  $style_ref_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
		  $po_number_array[$val[csf('po_number')]]=$val[csf('po_number')];
		  $job_wise_color_qnty_arr['color_total']+=$val[csf('product_qty')];
		  $job_wise_cutting_arr[$val[csf('color_number_id')]][$val[csf('production_date')]]['rowspan']+=1;
	   }
	    //  echo "<pre>";
	    // print_r($job_wise_cutting_arr);
	    // echo "</pre>";
	    // echo "<pre>";
	    // print_r($po_number_array);
	    // echo "</pre>";
	    // foreach($po_number_array as $color_id=>$color_val)
		//    foreach($color_val as $prod_date=>$v)
		// {
		//   {
		// 	$po_number=implode(",",array_unique(array_filter(explode(",", $v['po_number']))));
		//   }
		// }
		// echo $po_number;


	    $size_wise_order_quantity=array();
		$size_wise_total_qty_arr=array();

	    foreach($sql_color_size as $row)
		{
			$size_wise_order_quantity[$row[csf('size_number_id')]]['order_qty']+=$row[csf('order_qty')];
			$size_wise_total_qty_arr['color_total']+=$row[csf('order_qty')];
		}
		// echo "<pre>";
	    // print_r($size_wise_order_quantity);
	    // echo "</pre>";
        $po_array=implode(",",$po_number_array);
		$tbl_width=700+(count($job_size_arr)*60);

	  ?>

	       <div id="data_panel" align="center" style="width:100%">
        	<script>
				function new_window5()
				{
					$(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('job_wise_popup').innerHTML);
					d.close();
					$(".flt").css("display","block");
				}
			</script>
		   </div>
			<div align="center">
				<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin:5px;" align="center" onclick="new_window5()" />
			</div>
			<div id="job_wise_popup" style="text-align: center;">
			<div style="text-align: center;"> <strong>Buyer: <? echo $buyer_short_library[$buyer_arr[$val[csf('buyer_name')]]]; ?></strong>
				<strong>Style: <? echo $val[csf('style_ref_no')]; ?>,</strong>
                <strong>Po Number: <?  echo $po_array;?></strong>

			</div>

                <div style="width:<?=$tbl_width+20;?>px;" align="center">
				<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2">
				<thead>
					<tr>
						<th width="100">Cutting Company</th>
						<th width="100">Cutting No</th>
						<th width="100">Color</th>
						<th width="100">Country</th>
						<th width="100">Date</th>
						<th width="100">Challan</th>
						<?
							foreach($job_size_arr as $size_id=>$size_data)
							{
								?>
								<th width="60"><?
								echo $itemSizeArr[$size_id];
								?></th>
						<?
						}
						?>
						<th width="100">Color Total</th>
			       </tr>
				</thead>
                <tbody>
					<?
					  $i=1;

					    foreach($job_wise_cutting_arr as $color_id =>$color_data)
						{
                           foreach($color_data as $prod_date=>$val)
                           {

								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$cut_no = implode(",",array_unique(array_filter(explode(",", $val['cut_no']))));
							    $country = implode(",",array_unique(array_filter(explode(",", $val['country']))));
								// echo $country;
								$challan_no=implode(",",array_unique(array_filter(explode(",", $val['challan_no']))));
                                $total_product_qty=0;

								?>
                                <tr bgcolor="<? echo $bgcolor; ?>">
								<?
                                 if($i==1)
								 {
									?>
									<td width="100" valign="middle" rowspan="<? echo $val['rowspan'];?>"><p><? echo $company_arr[$val['serving_company']]; ?></p></td>
								<?
								 }

                                ?>
								<td width="100"><? echo $cut_no; ?></td>
								<td  width="100"><? echo  $colorArr[$color_id]; ?></td>
								<?
                                 if($i==1)
								 {
									?>
									<td width="100"  rowspan="<? echo $val['rowspan'];?>" align="center"><? echo $country;?></td>
								<?
								 }

                                ?>
								<td width="100"><? echo $prod_date;?></td>
								<td width="100"><? echo $challan_no;?></td>
								<?
									foreach($job_size_arr as $size_id=>$size_data)
									{

								?>
								   <td align="right"><? echo $val[$size_id]['product_qty']; $total_product_qty+=$val[$size_id]['product_qty'];?></td>
								<?
									}
					  			?>
                                <td width="100" align="right"><?  echo $total_product_qty; ?></td>


							    </tr>
								<?
                                  $i++;
							  }
						   }
					?>
				</tbody>

			   <tfoot>
				    <tr>
					<th colspan="6" width="700" align="left">Grand Total</th>
					<?
					foreach($job_size_arr as $size_id=>$val)
					{
						if($val !="")
						{
							?>
							<th width="60" align="right"><? echo $job_wise_qnty_arr[$size_id];?></th>
							<?
						}
					}
					?>

					<th width="100" align="right"><?  echo $job_wise_color_qnty_arr['color_total']; ?></th>
					</tr>
					<tr>
					<th colspan="6" width="700" align="left">Plan To Cut(AVG 2)% </th>
					<?
					foreach($job_size_arr as $size_id=>$val)
					{
						if($val !="")
						{
							?>
							<th width="60" align="right"><? $plan_cut=($size_wise_order_quantity[$size_id]['order_qty']*0.02)+$size_wise_order_quantity[$size_id]['order_qty']; echo $plan_cut;$total_plan_cut+=$plan_cut;?></th>
							<?
						}
					}
					?>

					<th width="100" align="right"><? echo $total_plan_cut;  ?></th>
					</tr>
					<tr>
					<th colspan="6" width="700" align="left">Order Qty</th>

					<?
					foreach($job_size_arr as $size_id=>$val)
					{
						if($val !="")
						{
							?>
							<th width="60" align="right"><? echo $size_wise_order_quantity[$size_id]['order_qty'];?></th>
							<?
						}
					}
					?>

					<th width="100" align="right"><? echo $size_wise_total_qty_arr['color_total']; ?></th>
					</tr>
					<tr>
					<th colspan="6" width="700" align="left"  title="Grand Total-Order Qty">Cutting Balance</th>
					<?
					foreach($job_size_arr as $size_id=>$val)
					{
						if($val !="")
						{
							?>
							<th width="60" align="right"><?

							$total_qty=$job_wise_qnty_arr[$size_id];
							$tot_orderqty=$size_wise_order_quantity[$size_id]['order_qty'];
                            $cutting_balance=$job_wise_qnty_arr[$size_id]-$size_wise_order_quantity[$size_id]['order_qty'];
							echo $cutting_balance;$tot_cutting_balance+=$cutting_balance;

							?></th>
							<?
						}
					}
					?>

					<th width="100" align="right"><? echo $tot_cutting_balance; ?></th>
					</tr>
			   </tfoot>

			</table>
			</div>



	<?
}

if($action=="total_fabric_recv_qty")//total_fabric_recv_qty
{
	echo load_html_head_contents("Today Fabric Recv Qty","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	/*	echo $prod_date.'_';
	echo $order_id.'_';
	echo $color_id.'_';*/

	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$batch_noArr = return_library_array("select id,batch_no from pro_batch_create_mst","id","batch_no");
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	//$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
	//echo $prod_date;
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";

	  $sql_fabric_qty=("SELECT a.po_breakdown_id,a.color_id,c.recv_number as issue_number,c.receive_date as issue_date,b.pi_wo_batch_no, a.quantity AS fabric_qty
		FROM order_wise_pro_details a,inv_transaction b,inv_receive_master c
	    WHERE a.trans_id = b.id
		and b.status_active=1 and a.entry_form in(18,15,16,37) and b.transaction_date <= '".$prod_date."' AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 and c.id=b.mst_id and a.quantity!=0 and  b.is_deleted=0  and a.color_id=$color_id AND a.po_breakdown_id in (".str_replace("'","",$order_id).") order by c.recv_number ");
		//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
		//echo $sql_fabric_qty;
		$result=sql_select($sql_fabric_qty);
		$fabric_pre_qty=array();
		$fabric_today_qty=array();
		$total_fabric=array();
		$fabric_balance=array();
		$fabric_wip=array();
		/*foreach($result as $value)
		{
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];

		//	$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
		//	-$value[csf("trans_out_pre")];

			$fabric_pre_qty[$value[csf("color_id")]]['fab_qty']+=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]-$value[csf("trans_out_pre")];
			//-$value[csf("trans_out_pre")];
			$fabric_pre_qty[$value[csf("color_id")]]['fabric_qty']+=$value[csf("fabric_qty")];
			$fabric_pre_qty[$value[csf("color_id")]]['issue_id']=$value[csf("issue_number")];
			$fabric_pre_qty[$value[csf("color_id")]]['issue_date']=$value[csf("issue_date")];
			$fabric_pre_qty[$value[csf("color_id")]]['batch_no']=$value[csf("pi_wo_batch_no")];

		}*/
	//	print_r($fabric_pre_qty);
	?>


     <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:540px">
		<table width="540" align="center" border="1" rules="all" class="rpt_table"   >
		<thead>
			<tr>
                <th width="30">SL</th>
                <th width="130">Received ID</th>
                <th width="80">Received Date</th>
                <th width="100">BATCH NO</th>
                <th width="100">COLOR NAME</th>
                <th width="80">Recv. Qty</th>
            </tr>
         </thead>
         <?
		 $total_fab_qty=0;
		 $k=1;
        // foreach($fabric_today_qty as $order_id=>$order_data)
		 //{
			 //foreach($fabric_pre_qty as $color_key=>$color_val)

			 foreach($result as $value)
			 {
				  if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 ?>
         <tr style="font:'Arial Narrow'" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
         	<td width="30"><? echo  $k;?> </td>
            <td width="130"><? echo  $value[csf("issue_number")];//$color_val['issue_id'];?>  </td>
            <td width="80"><? echo   change_date_format($value[csf("issue_date")]);//change_date_format($color_val['issue_date']);?> </td>
            <td width="100"> <? echo  $batch_noArr[$value[csf("pi_wo_batch_no")]];//$batch_noArr[$color_val['batch_no']];?></td>
            <td width="100"> <? echo  $color_name_arr[$value[csf("color_id")]];//$color_name_arr[$color_key];?></td>
            <td width="80" align="right"><? echo  number_format($value[csf("fabric_qty")],2);//number_format($color_val['fab_qty']+$color_val['fabric_qty'],2);?> </td>

         </tr>
         <?
		 $total_fab_qty+=$value[csf("fabric_qty")];//$color_val['fab_qty']+$color_val['fabric_qty'];
		 $k++;
			 }
		 //}
		 ?>
         <tr>
         <tfoot>
         <th align="right" colspan="5">Total</th><th align="right"> <? echo number_format($total_fab_qty,2);?></th>
         </tr>
         </table>

  </fieldset>
  </div>
	<?
	//exit();

}
if($action=="today_fabric_recv_qty")//
{
	echo load_html_head_contents("Today Fabric Recv Qty","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$batch_noArr = return_library_array("select id,batch_no from pro_batch_create_mst","id","batch_no");
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	//$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
	//echo $prod_date;
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";

	   $sql_fabric_qty=("SELECT a.po_breakdown_id,a.color_id,c.recv_number AS issue_number,c.receive_date as issue_date,b.pi_wo_batch_no, a.quantity AS fabric_qty
		FROM order_wise_pro_details a,inv_transaction b,inv_receive_master c
	    WHERE a.trans_id = b.id
		and b.status_active=1 and a.entry_form in(18,15,16,37) and b.transaction_date = '".$prod_date."' AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 and c.id=b.mst_id and a.quantity>0 and  b.is_deleted=0 and a.color_id=$color_id  AND a.po_breakdown_id in (".str_replace("'","",$order_id).") ");
		//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
		//echo  $sql_fabric_qty;
		$result=sql_select($sql_fabric_qty);
		$fabric_pre_qty=array();
		$fabric_today_qty=array();
		$total_fabric=array();
		$fabric_balance=array();
		$fabric_wip=array();
		/*foreach($result as $value)
		{
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];

			//$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
			//-$value[csf("trans_out_pre")];
			$fabric_today_qty[$value[csf("color_id")]]['fabric_qty']+=$value[csf("fabric_qty")]+$value[csf("trans_in_qty")]
			-$value[csf("trans_out_qty")];
			//-$value[csf("trans_out_pre")];
			$fabric_today_qty[$value[csf("color_id")]]['issue_id']=$value[csf("issue_number")];
			$fabric_today_qty[$value[csf("color_id")]]['issue_date']=$value[csf("issue_date")];
			$fabric_today_qty[$value[csf("color_id")]]['batch_no']=$value[csf("pi_wo_batch_no")];

		}*/

	?>


     <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:550px">
		<table width="520" align="center" border="1" rules="all" class="rpt_table"   >
		<thead>
			<tr>
                <th width="30">SL</th>
                <th width="130">Received ID</th>
                <th width="80">Received Date</th>
                <th width="100">BATCH NO</th>
                <th width="100">COLOR NAME</th>
                <th width="80">Recv. Qty</th>
            </tr>
         </thead>
         <?
		 $total_fab_qty=0;
		 $k=1;

			 //foreach($fabric_today_qty as $color_key=>$color_val)
			 foreach($result as $value)
			 {
				 if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				 if($value[csf("fabric_qty")]>0)
				 {

		 ?>
        <tr style="font:'Arial Narrow'" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
         	<td width="30"><?   echo  	$k;?> </td>
            <td width="130"><? 	echo    $value[csf("issue_number")];//$color_val['issue_id'];?>  </td>
            <td width="80"><? 	echo   change_date_format($value[csf("issue_date")]);//change_date_format($color_val['issue_date']);?> </td>
            <td width="100"><? 	echo   $batch_noArr[$value[csf("pi_wo_batch_no")]];//$batch_noArr[$color_val['batch_no']];?></td>
            <td width="100"><? 	echo   $color_name_arr[$value[csf("color_id")]];//$color_name_arr[$color_key];?></td>
            <td width="80" align="right"><? echo  number_format($value[csf("fabric_qty")],2);// number_format($color_val['fabric_qty'],2);?> </td>

         </tr>
         <?
		  $total_fab_qty+=$value[csf("fabric_qty")]; //new
		  //$total_fab_qty+=$color_val['fabric_qty'];//old
		 $k++;
			 	}
			  }
		 ?>
         <tr>
         <tfoot>
         <th align="right" colspan="5">Total</th><th align="right"> <? echo number_format($total_fab_qty,2);?></th>
         </tr>
         </table>

  </fieldset>
  </div>
	<?
	//	exit();

}