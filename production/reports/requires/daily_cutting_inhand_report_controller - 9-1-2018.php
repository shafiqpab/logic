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
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();	 
}
if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//
/*if($action=="style_wise_search")
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
	if($company==0) $company_name=""; else $company_name="and a.company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and b.job_no_mst='".$job_no."'";
	else if($cbo_year!=0)
	{
	if($db_type==0) $job_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
    if($db_type==2) $job_cond=" and extract( year from a.insert_date)=".str_replace("'","",$cbo_year)."";
	}

	echo $sql = "select a.id,a.style_ref_no,a.job_no_prefix_num,$insert_year as year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst $company_name $buyer_name $job_cond "; 
	
	echo create_list_view("list_view", "Style Refference,Job no,Year","190,100,100","440","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}
*/
//order wise browse------------------------------//
/*if($action=="job_wise_search")
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
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";//job_no
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$job_year_cond="";
	if($cbo_year!=0)
	{
	if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
    if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	$sql = "select distinct b.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$insert_year as year
	from wo_po_break_down a,  wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	b.is_deleted=0 and b.is_deleted=0 $company_name  $buyer_name $job_year_cond";
	//echo $sql;
	echo create_list_view("list_view", "Order Number,Job No,Year,Style Ref","150,100,100,100","500","310",0, $sql , "js_set_value", "id,job_no_mst", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}*/
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
	$rpt_type=str_replace("'","",$type);
	$job_cond_id=""; 
	$style_cond="";
	$order_cond="";
   	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=".str_replace("'","",$cbo_company_name)."";
	if(str_replace("'","",$cbo_buyer_name)==0)  $buyer_name=""; else $buyer_name="and b.buyer_name=".str_replace("'","",$cbo_buyer_name)."";
	$job_year_cond="";
	if(str_replace("'","",$cbo_year)!=0) 
	{
	if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
    if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	if(str_replace("'","",$hidden_job_id)!="") { $job_cond_id="and b.id in(".str_replace("'","",$hidden_job_id).")";}
	else  if (str_replace("'","",$txt_job_no)!="") { $job_cond_id=" and b.job_no_prefix_num=".str_replace("'","",$txt_job_no)." $job_year_cond  "; }
	else  $job_cond_id=" $job_year_cond  ";
	if(str_replace("'","",$hidden_style_id)!="")  $style_cond="and b.id in(".str_replace("'","",$hidden_style_id).")";
	else  if (str_replace("'","",$txt_style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no='".$txt_style_no."'";
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
	 if($rpt_type==1)
	 {
	 
      $pro_date_sql=sql_select ("SELECT  a.id,a.job_no_mst,a.po_number,
	  d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,d.item_number_id as gmt_id,b.buyer_name,b.style_ref_no as style,b.job_no_prefix_num,$insert_year 
	  as year ,d.color_number_id, a.file_no, a.grouping as int_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and  a.job_no_mst=d.job_no_mst and  a.is_deleted=0 and a.status_active=1 and 
	  b.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and 
	  b.status_active=1 $company_name $buyer_name $style_cond $order_cond $sql_cond $job_cond_id $country_ship_date $shipping_cond 
	  order by  b.buyer_name,a.job_no_mst");
	  foreach($pro_date_sql as $row)
	  {
		  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['id']=$row[csf('id')];
		  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no_mst')];
		  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
		  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['gmt_id']=$row[csf('gmt_id')];
		  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['po_quantity']+=$row[csf('order_qty')];
		  $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['plan_qty']+=$row[csf('plan_qty')];
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
//print_r($po_number_id);die;
	  $allqty_sql=sql_select ("SELECT  d.po_break_down_id,sum(d.order_quantity) as order_qty,sum(d.plan_cut_qnty) as plan_qty,
	  min(d.country_ship_date) as minimum_shipdate,d.color_number_id from  wo_po_color_size_breakdown d
	  where   d.status_active=1 and d.is_deleted=0 group by d.po_break_down_id,d.color_number_id");
	  $po_min_shipdate_data=array();
	  foreach($allqty_sql as $inv)
	  {
		 // $po_number_data[$inv[csf('po_break_down_id')]][$inv[csf('color_number_id')]]['po_quantity']+=$inv[csf('order_qty')];
		 // $po_number_data[$inv[csf('po_break_down_id')]][$inv[csf('color_number_id')]]['plan_qty']+=$inv[csf('plan_qty')];
		  $po_min_shipdate_data[$inv[csf('po_break_down_id')]][$inv[csf('color_number_id')]]['minimum_shipdate']=$inv[csf('minimum_shipdate')];
	  }
	  
	  unset($allqty_sql);
	  //print_r($po_number_data);
	   $sew_line_arr=array();
	   if($db_type==0)
	   {
	   		$sql_line=sql_select("select group_concat(distinct a.sewing_line) as line_id,a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id");
	   }
	   if($db_type==2)
	   {
	   		$sql_line=sql_select("select listagg(cast(a.sewing_line as varchar2(2000)),',') within group (order by a.sewing_line) as line_id,a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id");
	   }
	   foreach($sql_line as $row_sew)
	   {
	   		$sew_line_arr[$row_sew[csf('po_break_down_id')]]['line']= implode(',',array_unique(explode(',',$row_sew[csf('line_id')])));
	   }
	   
	   unset($sql_line);
	   
	  $po_number_id=implode(",",array_unique($po_number_id));
	  if($po_number_id=="") $po_number_id=0;
	
	
	  $production_mst_sql=sql_select("select a.po_break_down_id,c.color_number_id,
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
	   sum(CASE WHEN b.production_type =4 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty_pre 
	   from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c where a.id=b.mst_id 
	   and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0
	   and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in (".str_replace("'","",$po_number_id).")  group by a.po_break_down_id,c.color_number_id");
	   
	   
	  
	   
		   //and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
       foreach($production_mst_sql as $val)
	   {
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['printing_qnty']=$val[csf('printing_qnty')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['emblreceived_qnty_pre']=$val[csf('emblreceived_qnty_pre')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['emblreceived_qnty']=$val[csf('emblreceived_qnty')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['embl_qnty_pre']=$val[csf('embl_qnty_pre')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['embl_qnty']=$val[csf('embl_qnty')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['min_printin_date']=$val[csf('min_printin_date')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['min_embl_date']=$val[csf('min_embl_date')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
			$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
			$po_number_gmt[]=$val[csf('po_break_down_id')];		
	    }
		
		unset($production_mst_sql);

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
 				if($i!=1)
				  {
					 if(!in_array($po_number_data[$po_id][$color_id]['job_no'],$job_arr))
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
				$possible_cut_qty=($total_fabric/($con_per_dzn[$po_id][$color_id]/$costing_per_qty));		
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$fabric_qty=$po_number_data[$po_id][$color_id]['plan_qty']*($con_per_dzn[$po_id][$color_id]/$costing_per_qty);
			    $fabric_balance=$fabric_qty-$total_fabric;	
				$total_cut=$production_data_arr[$po_id][$color_id]['cutting_qnty']+$production_data_arr[$po_id][$color_id]['cutting_qnty_pre'];
				$cutting_balance=$po_number_data[$po_id][$color_id]['plan_qty']-$total_cut;
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
				$total_sew_input_balance=$po_number_data[$po_id][$color_id]['plan_qty']-$total_sew_input;
				$input_percentage=($total_sew_input/$po_number_data[$po_id][$color_id]['po_quantity'])*100;
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
				$job_total_order+=$po_number_data[$po_id][$color_id]['po_quantity'];
				$job_total_plan+=$po_number_data[$po_id][$color_id]['plan_qty'];
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
				$buyer_total_order+=$po_number_data[$po_id][$color_id]['po_quantity'];
				$buyer_total_plan+=$po_number_data[$po_id][$color_id]['plan_qty'];
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
				$grand_total_order+=$po_number_data[$po_id][$color_id]['po_quantity'];
				$grand_total_plan+=$po_number_data[$po_id][$color_id]['plan_qty'];
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
                    <td width="70" align="right"><?  echo $po_number_data[$po_id][$color_id]['po_quantity']; ?></td>
                    <td width="70" align="right"><?  echo $po_number_data[$po_id][$color_id]['plan_qty']; ?></td>
                    <td width="70" align="right"><?  echo number_format(($con_per_dzn[$po_id][$color_id]*12/$costing_per_qty),3); ?></td>
                    <td width="70" align="right"  title="consumption per pcs * Plan Cut Qty"><?  echo number_format($fabric_qty,2); ?></td>
                    <td width="60" align="right"><?  echo number_format($fabric_pre,2); ?></td>
                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,1)"><?  echo number_format($fabric_today,2); ?></a></td>
                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,2)"><?  echo number_format($total_fabric,2); ?></a><?  //echo number_format($total_fabric,2);?></td>
                    <td width="60" align="right"><?  echo number_format($fabric_balance,2); ?></td>
               
                    <td width="70" align="right" title="Possible Cut Qty. =Total Fabric Receive/ Consumption Per Pcs."><?  echo  number_format($possible_cut_qty,0); ?></td>
                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$color_id]['cutting_qnty_pre']; //$production_data_arr[$row_fab[csf('po_breakdown_id')]][$row_fab[csf('color_id')]]['cutting_qnty_pre']; ?></td>
                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',850,350,'','<? echo $color_id; ?>','',1)"> <?  echo $production_data_arr[$po_id][$color_id]['cutting_qnty']; ?></a></td>
                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',850,350,'','<? echo $color_id; ?>','',2)"><?  echo $total_cut; ?></a></td>
                    <td width="60" align="right"><?  echo $cutting_balance; ?></td>
                    <td width="60" align="right" title="Cutting WIP=Possible Cut Qty - Total Cutting Qty"><?  echo number_format($possible_cut_qty-$total_cut,0); ?></td>
                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$color_id]['cut_delivery_qnty_pre']; ?></td>
                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','',1)"><?  echo $production_data_arr[$po_id][$color_id]['cut_delivery_qnty']; ?></a></td>
                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','',1)"><?  echo $total_delivery_cut; ?></a></td>
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
					   if($total_delivery_cut-$po_number_data[$po_id][$color_id]['plan_qty']>0) echo "Receive Ok"; 
					   else   if($total_sew_input_balance-$po_number_data[$po_id][$color_id]['plan_qty']>0) echo "Input Ok"; 
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
                                <td width="60" align="right"><?  echo $job_today_cut; ?></td>
                                <td width="60" align="right"><?  echo $job_total_cut; ?></td>
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
	 else
	 {
		 
		  $pro_date_sql=sql_select ("SELECT  a.id,a.job_no_mst,a.po_number,
	  d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,d.item_number_id as gmt_id,b.buyer_name,b.style_ref_no as style,b.job_no_prefix_num,$insert_year 
	  as year ,d.color_number_id, a.file_no, a.grouping as int_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and  a.job_no_mst=d.job_no_mst and  a.is_deleted=0 and a.status_active=1 and 
	  b.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and 
	  b.status_active=1 $company_name $buyer_name $style_cond $order_cond $sql_cond $job_cond_id $country_ship_date $shipping_cond 
	  order by  b.buyer_name,a.job_no_mst");
	  foreach($pro_date_sql as $row)
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
		  $po_number_id[]=$row[csf('id')];	
	  }
	  unset($pro_date_sql);
//print_r($po_number_id);die;
	  $allqty_sql=sql_select ("SELECT  d.po_break_down_id,d.item_number_id as gmt_id,sum(d.order_quantity) as order_qty,sum(d.plan_cut_qnty) as plan_qty,
	  min(d.country_ship_date) as minimum_shipdate,d.color_number_id from  wo_po_color_size_breakdown d
	  where   d.status_active=1 and d.is_deleted=0 group by d.po_break_down_id,d.color_number_id,d.item_number_id");
	  $po_min_shipdate_data=array();
	  foreach($allqty_sql as $inv)
	  {
		 // $po_number_data[$inv[csf('po_break_down_id')]][$inv[csf('color_number_id')]]['po_quantity']+=$inv[csf('order_qty')];
		 // $po_number_data[$inv[csf('po_break_down_id')]][$inv[csf('color_number_id')]]['plan_qty']+=$inv[csf('plan_qty')];
		  $po_min_shipdate_data[$inv[csf('po_break_down_id')]][$inv[csf('gmt_id')]][$inv[csf('color_number_id')]]['minimum_shipdate']=$inv[csf('minimum_shipdate')];
	  }
	  
	  unset($allqty_sql);
	  //print_r($po_number_data);
	   $sew_line_arr=array();
	   if($db_type==0)
	   {
	   		$sql_line=sql_select("select group_concat(distinct a.sewing_line) as line_id, group_concat(distinct a.floor_id) as floor_id, a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id");
	   }
	   if($db_type==2)
	   {
	   		$sql_line=sql_select("select listagg(cast(a.sewing_line as varchar2(2000)),',') within group (order by a.sewing_line) as line_id, listagg(cast(a.floor_id as varchar2(2000)),',') within group (order by a.floor_id) as floor_id, a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id");
	   }
	   foreach($sql_line as $row_sew)
	   {
	   		$sew_line_arr[$row_sew[csf('po_break_down_id')]]['line']= implode(',',array_unique(explode(',',$row_sew[csf('line_id')])));
			$sew_line_arr[$row_sew[csf('po_break_down_id')]]['floor_id']= implode(',',array_unique(explode(',',$row_sew[csf('floor_id')])));
	   }
	   
	   unset($sql_line);
	   
	 
	 
	
	   
	  $po_number_id=implode(",",array_unique($po_number_id));
	  if($po_number_id=="") $po_number_id=0;
	  
	   $ex_factory_sql="select a.po_break_down_id as order_id, a.item_number_id as item_id, c.color_number_id as color_id, 
	    sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS exf_qnty_today,
		sum(CASE WHEN m.entry_form=85 and a.ex_factory_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_today,
		sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS exf_qnty_pre,
		sum(CASE WHEN m.entry_form=85 and a.ex_factory_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_pre
	from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
	where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0  and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
	group by  a.po_break_down_id, a.item_number_id, c.color_number_id";
	//echo $ex_factory_sql;//die;
	$ex_factory_sql_result=sql_select($ex_factory_sql);
	foreach($ex_factory_sql_result as $row)
	{
		//if($row[csf("ex_fact_qnty")]>0)
		//{
			$production_data[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['exf_qnty_today']=$row[csf('exf_qnty_today')]-$row[csf('ret_exf_qnty_today')];
			$production_data[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['exf_qnty_pre']=$row[csf('exf_qnty_pre')]-$row[csf('ret_exf_qnty_pre')];
		//}
	}
	
	  $production_sql="select a.po_break_down_id,c.color_number_id,c.item_number_id as gmt_id,
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
	   
	   sum(CASE WHEN b.production_type =11 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty_pre,
	   sum(CASE WHEN b.production_type =11 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty_today,
	   sum(CASE WHEN b.production_type =11 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS ploy_rej_qty_pre,
	   sum(CASE WHEN b.production_type =11 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS ploy_rej_qty_today,
	   
	    sum(CASE WHEN b.production_type =8 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS fin_qty_pre,
		sum(CASE WHEN b.production_type =8 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS fin_qty_today,
		sum(CASE WHEN b.production_type =8 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS fin_rej_qty_today,
		sum(CASE WHEN b.production_type =8 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS fin_rej_qty_pre

	    
	   from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c where a.id=b.mst_id 
	   and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	   and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in (".str_replace("'","",$po_number_id).")  group by a.po_break_down_id,c.color_number_id,c.item_number_id";
	   
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
			
			
			$po_number_gmt[]=$val[csf('po_break_down_id')];		
	    }
		
		unset($production_mst_sql);

		$sql_cutting_delevery=sql_select("select a.po_break_down_id,c.color_number_id,c.item_number_id as gmt_id
		sum(CASE WHEN  a.cut_delivery_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty,
	    sum(CASE WHEN  a.cut_delivery_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS cut_delivery_qnty_pre
		from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c  where a.id=b.mst_id 
	    and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id  and a.po_break_down_id in (".str_replace("'","",$po_number_id).")  
	    and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	    group by a.po_break_down_id,c.color_number_id,c.item_number_id");
	   //and a.po_break_down_id in (".str_replace("'","",$po_number_id).")
		foreach( $sql_cutting_delevery as $inf)
		{
			$production_data_arr[$inf[csf('po_break_down_id')]][$inf[csf('gmt_id')]][$inf[csf('color_number_id')]]['cut_delivery_qnty']=$inf[csf('cut_delivery_qnty')];
		
			$production_data_arr[$inf[csf('po_break_down_id')]][$inf[csf('gmt_id')]][$inf[csf('color_number_id')]]['cut_delivery_qnty_pre']=$inf[csf('cut_delivery_qnty_pre')]; 	
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
		 ?>
         <fieldset style="width:5700px;">
        	   <table width="1880"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                           <td colspan="56" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report(Item Wise)</td>
                     </tr>
                    <tr style="border:none;">
                            <td colspan="56" align="center" style="border:none; font-size:16px; font-weight:bold">
                            Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                            </td>
                      </tr>
                      <tr style="border:none;">
                            <td colspan="56" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
                            </td>
                      </tr>
                </table>
             <br />	
             <table cellspacing="0"  border="1" rules="all"  width="5700" class="rpt_table">
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
                        
                        <th width="60" rowspan="2">Unit(Floor)</th>
                        <th width="80" rowspan="2">Cutable Pcs as per Fabric Received</th>
                        <th width="80" rowspan="2">Cutting KG</th>
                        <th width="80" rowspan="2">Cutting Balance Fabric</th>
                        
                        <th width="210" colspan="3">Sewing Output</th>
                        <th width="70" rowspan="2">Today Sewing Reject</th>
                        <th width="70" rowspan="2">Sewing Reject Total</th>
                        <th width="70" rowspan="2">Sewing WIP</th>
                        
                        <th width="210" colspan="3">Poly Entry</th>
                        
                        <th width="70" rowspan="2">Today Poly Reject</th>
                        <th width="70" rowspan="2">Poly Reject Total</th>
                        <th width="70" rowspan="2">Poly WIP</th>
                        
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
            
             <div style="max-height:425px; overflow-y:scroll; width:5700px;" id="scroll_body">
                    <table  border="1" class="rpt_table"  width="5682" rules="all" id="table_body" >
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
				$fabric_qty=$po_number_data[$po_id][$item_id][$color_id]['plan_qty']*($con_per_dzn[$po_id][$color_id]/$costing_per_qty);
				//echo $po_number_data[$po_id][$item_id][$color_id]['plan_qty'].'=='.$con_per_dzn[$po_id][$color_id].'=='.$costing_per_qty.', ';
			    $fabric_balance=$fabric_qty-$total_fabric;	
				$total_cut=$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']+$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre'];
				$cutting_balance=$po_number_data[$po_id][$item_id][$color_id]['plan_qty']-$total_cut;
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
				$total_sew_input_balance=$po_number_data[$po_id][$item_id][$color_id]['plan_qty']-$total_sew_input;
				$input_percentage=($total_sew_input/$po_number_data[$po_id][$item_id][$color_id]['po_quantity'])*100;
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
				
				$exf_qnty_today=$production_data[$po_id][$item_id][$color_id]['exf_qnty_today'];
				$exf_qnty_pre=$production_data[$po_id][$item_id][$color_id]['exf_qnty_pre'];
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
				$job_total_order+=$po_number_data[$po_id][$item_id][$color_id]['po_quantity'];
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
				$buyer_total_order+=$po_number_data[$po_id][$item_id][$color_id]['po_quantity'];
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
				$grand_total_order+=$po_number_data[$po_id][$item_id][$color_id]['po_quantity'];
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
                    <td width="70" align="right"><?  echo $po_number_data[$po_id][$item_id][$color_id]['po_quantity']; ?></td>
                    <td width="70" align="right"><?  echo $po_number_data[$po_id][$item_id][$color_id]['plan_qty']; ?></td>
                    <td width="70" align="right"><?  echo number_format(($con_per_dzn[$po_id][$color_id]*12/$costing_per_qty),3); ?></td>
                    <td width="70" align="right"  title="consumption per pcs * Plan Cut Qty"><?  echo number_format($fabric_qty,2); ?></td>
                    <td width="60" align="right"><?  echo number_format($fabric_pre,2); ?></td>
                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,1)"><?  echo number_format($fabric_today,2); ?></a></td>
                    <td width="60" align="right"><a href="##" onClick="today_fab_recv_po(<? echo $cbo_company_name;?>,<? echo $po_id;?>,'<? echo $color_id;?>',<? echo $txt_production_date;?>,2)"><?  echo number_format($total_fabric,2); ?></a><?  //echo number_format($total_fabric,2);?></td>
                    <td width="60" align="right"><?  echo number_format($fabric_balance,2); ?></td>
               
                    <td width="70" align="right" title="Possible Cut Qty. =Total Fabric Receive/ Consumption Per Pcs."><?  echo  number_format($possible_cut_qty,0); ?></td>
                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty_pre']; //$production_data_arr[$row_fab[csf('po_breakdown_id')]][$row_fab[csf('color_id')]]['cutting_qnty_pre']; ?></td>
                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',850,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"> <?  echo $production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']; ?></a></td>
                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,1,'cutting_popup',850,350,'','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"><?  echo $total_cut; ?></a></td>
                    <td width="60" align="right"><?  echo $cutting_balance; ?></td>
                    <td width="60" align="right" title="Cutting WIP=Possible Cut Qty - Total Cutting Qty"><?  echo number_format($possible_cut_qty-$total_cut,0); ?></td>
                    <td width="60" align="right"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty_pre']; ?></td>
                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['cut_delivery_qnty']; ?></a></td>
                    <td width="60" align="right"><a href="##" onclick="openmypage_embl(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,2,'cutting_delivery_popup',850,250,1,'<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $total_delivery_cut; ?></a></td>
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
                    <td width="60" align="right"><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',1)"><?  echo $production_data_arr[$po_id][$item_id][$color_id]['sewingin_qnty']; ?></a></td>
                    <td width="60" align="right"><a href="##" onclick="openmypage_swing(<? echo $cbo_company_name; ?> ,'<? echo  $po_id; ?>','<? echo  $po_number_data[$po_id][$item_id][$color_id]['po_number']; ?>', <? echo $txt_production_date; ?>,4,'cutting_and_sewing_popup',900,350,'<? echo $prod_reso_allo; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>',2)"> <? echo $total_sew_input; ?></a></td>
                    <td width="60" align="right" title="(Total Sewing Input*100)/Order Qty"><?  echo number_format($input_percentage,2); ?></td>
                    <td width="60" align="right"><?  echo $total_sew_input_balance; ?></td>
                    <td width="70" align="right" title="Inhand=Total Cut - Delivery to Input" ><?  echo $cutting_inhand; ?></td>
                     <td width="70" align="right" title="Inhand= Cutting Delivery To Input  - Delivery to  Embellishment &#13; + Receive from Embellishment -Sewing Input" ><?  echo $inhand; ?></td>
                    <td width="100" align="right"><p><? echo $ready_to_sewing; ?></p></td>
                    <td width="100" align="center"><p><? echo $line_name; ?></p></td>
                    
                    <td width="60" align="center"><p><? echo $floor_name; ?></p></td>
                    <td width="80" align="right"><p><? $fin_cons=0; $cutable_pcs=0; $fin_cons=number_format(($con_per_dzn[$po_id][$color_id]*12/$costing_per_qty),3); $cutable_pcs=($total_fabric/$fin_cons)*12; echo number_format($cutable_pcs,2); ?></p></td>
                    <td width="80" align="right"><p><? $cutting_kg=0; $cutting_kg=($fin_cons/12)*$production_data_arr[$po_id][$item_id][$color_id]['cutting_qnty']; echo number_format($cutting_kg,2); ?></p></td>
                    <td width="80" align="right"><p><? $cutting_bal=0; $cutting_bal=$total_fabric-$cutting_kg; echo number_format($cutting_bal,2); ?></p></td>
                    
                     <td width="70" align="right"><p><? echo number_format($sewingout_qnty_pre,2); ?></p></td>
                     <td width="70" align="right"><p><? echo number_format($sewingout_qnty_today,2); ?></p></td>
                     <td width="70" align="right"><p><? echo number_format($total_sewingout_qnty_bal,2); ?></p></td>
                     <td width="70" align="right"><p><?  echo number_format($sewout_rej_qty_today,2); ?></p></td>
                     <td width="70" align="right"><p><? echo number_format($total_sewingout_reject_qty_bal,2); ?></p></td>
                     <td width="70" align="right"><p><?  echo number_format($total_sewingout_wip_qty,2); ?></p></td>
                     <td width="70" align="right"><p><? echo number_format($poly_qnty_pre,2); ?></p></td>
                     <td width="70" align="right"><p><?  echo number_format($poly_qnty_today,2); ?></p></td>
                      <td width="70" align="right"><p><? echo number_format($total_poly_qnty,2); ?></p></td>
                     <td width="70" align="right"><p><?  echo number_format($ploy_rej_qty_today,2); ?></p></td>
                     <td width="70" align="right"><p><? echo number_format($total_ploy_rej_qty,2); ?></p></td>
                     <td width="70" align="right"><p><?  echo number_format($tot_poly_wip,2); ?></p></td>
                     <td width="70" align="right"><p><? echo number_format($fin_qty_pre,2); ?></p></td>
                     <td width="70" align="right"><p><? echo number_format($fin_qty_today,2); ?></p></td>
                     <td width="70" align="right"><p><? echo number_format($tot_fin_qnty,2); ?></p></td>
                     <td width="70" align="right"><p><? echo number_format($fin_rej_qty_today,2); ?></p></td>
                      <td width="70" align="right"><p><?  echo number_format($tot_fin_reject_qty,2); ?></p></td>
                     <td width="70" align="right"><p><? echo number_format($tot_fin_wip,2); ?></p></td>
                     <td width="70" align="right"><p><?  echo number_format($exf_qnty_pre,2); ?></p></td>
                     <td width="70" align="right"><p><?  echo number_format($exf_qnty_today,2); ?></p></td>
                     <td width="70" align="right"><p><? echo number_format($tot_exf_qnty,2); ?></p></td>
                     <td width="70" align="right"><p><?  echo number_format($ex_fact_wip,2); ?></p></td>
                     
                    
                    
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
		 
	<? }
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
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
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
		$sql_cutting_delevery="select a.id,a.cut_delivery_date,a.challan_no ,b.production_qnty,c.size_number_id,c.color_number_id,c.country_id
		from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c 
	    where a.id=b.mst_id 
	    and b.color_size_break_down_id=c.id 
		and a.po_break_down_id=c.po_break_down_id  
		and a.po_break_down_id=$order_id
		and c.color_number_id=$color_id 
		and a.status_active=1 and a.is_deleted=0
		and  b.status_active=1  and b.is_deleted=0
		and c.status_active=1 $item_cond2";
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
		$sql="SELECT  d.id,d.floor_id,d.production_source,d.serving_company,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,
		    d.challan_no,d.production_date,f.country_id
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
			e.production_type=4  and
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



if($action=="cutting_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=='') $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=='') $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
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
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')]; ?></td>
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
	 
	

		$sql="SELECT  d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,d.production_date,f.country_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=$type  and
			f.color_number_id=$color_id and
		    e.is_deleted =0 and
			e.status_active =1 and
			f.is_deleted =0 and
			f.status_active =1 and
			d.is_deleted =0 and
			d.status_active=1  
			  $insert_cond $item_cond2
			order by f.size_order,d.production_date";
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
		foreach( $sql_data as $row)
		{
		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array['color_total']+=$row[csf('product_qty')];
		//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		
		$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
		$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
		$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('production_date')];
		$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
		$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
		//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
		$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
		}
		// print_r($production_size_details_arr);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
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
	
	  $sql_fabric_qty=("SELECT a.po_breakdown_id,a.color_id,c.issue_number,c.issue_date,b.pi_wo_batch_no,
	
		CASE WHEN b.transaction_date <= '".$prod_date."' AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END  AS fabric_qty
		
		FROM order_wise_pro_details a,inv_transaction b,inv_issue_master c
	    WHERE a.trans_id = b.id 
		and b.status_active=1 and a.entry_form in(18,15,16,37) and c.id=b.mst_id and a.quantity!=0 and  b.is_deleted=0  and a.color_id=$color_id AND a.po_breakdown_id in (".str_replace("'","",$order_id).") order by c.issue_number ");
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
                <th width="130">ISSUE ID</th>
                <th width="80">Issue Date</th>
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
	
	   $sql_fabric_qty=("SELECT a.po_breakdown_id,a.color_id,c.issue_number,c.issue_date,b.pi_wo_batch_no,
	
		CASE WHEN b.transaction_date = '".$prod_date."' AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END  AS fabric_qty
		
		FROM order_wise_pro_details a,inv_transaction b,inv_issue_master c
	    WHERE a.trans_id = b.id 
		and b.status_active=1 and a.entry_form in(18,15,16,37) and c.id=b.mst_id and a.quantity>0 and  b.is_deleted=0 and a.color_id=$color_id  AND a.po_breakdown_id in (".str_replace("'","",$order_id).") ");
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
                <th width="130">ISSUE ID</th>
                <th width="80">Issue Date</th>
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