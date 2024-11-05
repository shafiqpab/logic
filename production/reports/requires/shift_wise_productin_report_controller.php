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


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
  exit();	 
}

if ($action=="load_drop_down_location")
{	
	$data=str_replace("'", "", $data);
	echo create_drop_down( "cbo_location_name", 120, "SELECT id,location_name from lib_location where company_id in($data) and  status_active =1 and is_deleted=0  group by id,location_name  order by location_name","id,location_name", 1, "-- Select Floor --", $selected, "" );  
	
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
	// echo "<pre>";
	// print_r($process);
	ob_start();
    extract(check_magic_quote_gpc( $process ));
	$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
	$production_floor =return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0",'id','floor_name');
	$garments_item = return_library_array("select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name", "id", "item_name");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	
	//$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$sew_group_Arr = return_library_array("select id,sewing_group from lib_sewing_line order by id","id","sewing_group"); 
	
	$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial"); 
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}
	// ================================= GETTING FORM DATA ====================================
	$rpt_type=str_replace("'","",$type);
	$job_cond_id=""; 
	$style_cond="";
	$order_cond="";

   	if(str_replace("'","",$cbo_company_name)==0) $company_cond=""; else $company_cond=" and a.company_name=".str_replace("'","",$cbo_company_name)."";

	if(str_replace("'","",$cbo_buyer_name)==0)  $buyer_cond=""; else $buyer_cond="and a.buyer_name=".str_replace("'","",$cbo_buyer_name)."";
	if(str_replace("'","",$cbo_location_name)==0)  $location_cond=""; else $location_cond="and c.location=".str_replace("'","",$cbo_location_name)."";

	if(str_replace("'","",$cbo_shift_name)==0)  $shift_cond=""; else $shift_cond="and c.shift_name=".str_replace("'","",$cbo_shift_name)."";
	$job_year_cond="";

	if(str_replace("'","",$cbo_year)!=0) 
	{
		if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
    	if($db_type==2) $job_year_cond=" and extract( year from a.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	else 
	{
		if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)>=2018";
    	if($db_type==2) $job_year_cond=" and extract( year from a.insert_date)>=2018";
	}

	// $txt_date_from = str_replace("'", "", $txt_date_from);
	// $txt_date_to = str_replace("'", "", $txt_date_to);
	$company_id = str_replace("'", "", $cbo_company_name);

	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $production_date="";
	else $production_date=" and c.production_date between $txt_date_from and $txt_date_to";
    
	$order_cond="";
	if (str_replace("'","",$hidden_order_id)!=""){ $order_cond="and b.id in (".str_replace("'","",$hidden_order_id).")";}
	else if (str_replace("'","",$txt_order_no)=="") $order_cond=""; else $order_cond="and b.po_number='".str_replace("'","",$txt_order_no)."'"; 
	
	if($rpt_type==1) // SHOW
	{
        // ================================= Main Query ===============================================
		$sql="SELECT 
		a.job_no,
		a.job_no_prefix_num,
		a.style_ref_no,
		a.buyer_name,
		a.gmts_item_id,
		b.id as po_id,
		b.po_number,
		b.shipment_date,
		c.production_date,
		c.floor_id,
		c.sewing_line,
		c.production_type,
		c.prod_reso_allo,
		d.floor_serial_no,
		sum(case when e.production_type = 4 then e.production_qnty else 0 end) as sewing_input,
		sum(case when e.production_type = 5 and shift_name = 1 then e.production_qnty else 0 end) as shift_a,
		sum(case when e.production_type = 5 and shift_name = 2 then e.production_qnty else 0 end) as shift_b,
		sum(case when e.production_type = 5 and shift_name = 3 then e.production_qnty else 0 end) as shift_c,

		sum(case when e.production_type = 8 and shift_name = 1 then e.production_qnty else 0 end) as finishing_shift_a,
		sum(case when e.production_type = 8 and shift_name = 2 then e.production_qnty else 0 end) as finishing_shift_b,
		sum(case when e.production_type = 8 and shift_name = 3 then e.production_qnty else 0 end) as finishing_shift_c,
		sum(case when e.production_type = 8 then e.production_qnty else 0 end) as finishing_qnty
        FROM wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c,  pro_garments_production_dtls e, lib_prod_floor d
		WHERE 
		a.id = b.job_id
		and b.id = c.po_break_down_id
		and c.id = e.mst_id
		and c.production_type in (4,5,8)
		and c.floor_id = d.id
		and a.status_active='1'
		and a.is_deleted='0'
		and b.status_active='1'
		and b.is_deleted='0'
		and c.status_active='1'
		and c.is_deleted='0'
		and e.status_active=1
		and e.is_deleted=0
		
		$production_date $company_cond $buyer_cond $location_cond $shift_cond $job_year_cond $order_cond  
        group by 
		a.job_no,
		a.job_no_prefix_num,
		a.style_ref_no,
		a.buyer_name,
		a.gmts_item_id,
		b.id,
		b.po_number,
		b.shipment_date,
		d.floor_serial_no,
		c.production_date,c.floor_id,c.sewing_line,c.production_type,c.prod_reso_allo order by d.floor_serial_no";
			
		//echo $sql;die;
		$sql_result = sql_select($sql);

		$data_array = array();
		$floor_data_array = array();
		//$prod_reso_arr =array();
		$prod_type="";

		
		
		foreach($sql_result as $row)
		{
			if($row['PRODUCTION_TYPE']==4)
		   {
				$prod_type=5;
		   }
		   else
		   {
				$prod_type=$row['PRODUCTION_TYPE'];
			} 

			if($row[csf('prod_reso_allo')]==1)
			{
				
				$sewing_line_id=$prod_reso_arr[$row[csf('sewing_line')]];
				// $sl_ids_arr = explode(",", $sewing_line_ids);
				// $sewing_line_id = $sl_ids_arr[0];
			}
			else
			{
				$sewing_line_id=$row[csf('sewing_line')];
			}
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];

           $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['JOB_NO'] = $row['JOB_NO'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['JOB_NO_PREFIX_NUM'] = $row['JOB_NO_PREFIX_NUM'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['prod_reso_allo'] = $row['PROD_RESO_ALLO'];
           $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['PO_NUMBER'] = $row['PO_NUMBER'];
           $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['SEWING_LINE'] = $row['SEWING_LINE'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['BUYER_NAME'] = $row['BUYER_NAME'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['GMTS_ITEM_ID'] = $row['GMTS_ITEM_ID'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['PRODUCTION_DATE'] = $row['PRODUCTION_DATE'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['SEWING_INPUT'] += $row['SEWING_INPUT'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['SHIFT_A'] += $row['SHIFT_A'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['SHIFT_B'] += $row['SHIFT_B'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['SHIFT_C'] += $row['SHIFT_C'];

		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['PO_ID'] = $row['PO_ID'];

		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['FINISHING_SHIFT_A'] += $row['FINISHING_SHIFT_A'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['FINISHING_SHIFT_B'] += $row['FINISHING_SHIFT_B'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['FINISHING_SHIFT_C'] += $row['FINISHING_SHIFT_C'];
		   $data_array[$prod_type][$row['FLOOR_ID']][$slNo][$sewing_line_id][$row['JOB_NO']][$row['PO_ID']][$row['PRODUCTION_DATE']]['FINISHING_QNTY'] += $row['FINISHING_QNTY'];
		   
		   
		   $floor_data_array[$prod_type][$row['FLOOR_ID']]['FLOOR_ID'] = $row['FLOOR_ID'];
		   $floor_data_array[$prod_type][$row['FLOOR_ID']]['SEWING_INPUT'] += $row['SEWING_INPUT'];
		   $floor_data_array[$prod_type][$row['FLOOR_ID']]['SHIFT_A'] += $row['SHIFT_A'];
		   $floor_data_array[$prod_type][$row['FLOOR_ID']]['SHIFT_B'] += $row['SHIFT_B'];
		   $floor_data_array[$prod_type][$row['FLOOR_ID']]['SHIFT_C'] += $row['SHIFT_C'];

		   $floor_data_array[$prod_type][$row['FLOOR_ID']]['FINISHING_QNTY'] += $row['FINISHING_QNTY'];

		}
		ksort($floor_data_array);
		ksort($data_array);

		//  echo "<pre>";
		// print_r($data_array); die;


		?>
			<fieldset style="width:1570px">
				<table width="1570" cellpadding="0" cellspacing="0"> 
					<tr class="form_caption">
						<td align="center"><p style="font-size:25px; font-weight:bold;"><? echo $company_library[$company_id]; ?><p></td> 
					</tr>
					<tr class="form_caption">
						<td align="center"><p style="font-size:21px; font-weight:bold;">Shift Wise Productin Report</p></td> 
					</tr>
					<tr class="form_caption">
						<td align="center"><p style="font-size:18px; font-weight:bold;"><? echo "From:".change_date_format( str_replace("'","",trim($txt_date_from)) )." To ".change_date_format( str_replace("'","",trim($txt_date_to)) ); ?></p></td> 
					</tr>
				</table>
				<br />
				<!-- ============================ Summery Part ============================ -->
				<table id="report_container2" class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all" style="float: left;">
					<thead>
					    <P style="font-weight:bold; text-align:left;">Summary</p>
						<tr>
						<th colspan="3" align="right"></th>
						<th colspan="4" align="center">Sew Output</th>
						<th colspan="1" align="center"></th>
						</tr>
						<tr height="50">
							<th width="40">Sl.</th>
							<th width="100">Floor/Unit Name</th>
							<th width="100">Sewing Input</th>
							<th width="70">Shift-A</th>
							<th width="70">Shift-B</th>
							<th width="70">Shift-C</th>
							<th width="100">Total Sewing Output</th>
							<th width="100">Total Finish Qty</th>
						</tr>
					</thead>
				</table>
				<table class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" style="float: left;">
					<tbody>
						<?
                        $k=1;
						$gr_total_floor_sewing_input = 0;
						$gr_total_floor_shift_a      = 0;
						$gr_total_floor_shift_b      = 0;
						$gr_total_floor_shift_c      = 0;
						$gr_total_floor_sewing_output = 0;
						$gr_total_floor_finishing_qnty = 0;

						foreach($floor_data_array as $prod_key=>$prod_value)
						{   
							$total_floor_sewing_input = 0;
							$total_floor_shift_a      = 0;
							$total_floor_shift_b      = 0;
							$total_floor_shift_c      = 0;
							$total_floor_sewing_output = 0;
							$total_floor_finishing_qnty = 0;
							
							foreach($prod_value as $floor_key=>$floor_value)
							{        
									if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
								?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
										<td width="40" align="center"><? echo $k; ?></td>
										<td width="100" align="left"><? echo  $production_floor[$floor_key]; ?></td>
										<td width="100" align="right"><? echo number_format($floor_value['SEWING_INPUT'],0); ?></td>
										<td width="70" align="right"><? echo number_format($floor_value['SHIFT_A'],0); ?></td>
										<td width="70" align="right"><? echo number_format($floor_value['SHIFT_B'],0); ?></td>
										<td width="70" align="right"><? echo number_format($floor_value['SHIFT_C'],0); ?></td>
										<td width="100" align="right">
											<?
												$floor_sewing_output= $floor_value['SHIFT_A']+$floor_value['SHIFT_B']+$floor_value['SHIFT_C'];
												echo number_format($floor_sewing_output,0);
											?>
										</td>
										<td width="100" align="right"><? echo number_format($floor_value['FINISHING_QNTY'],0); ?></td>
									</tr>
								<?
								

								     $k++;
										$total_floor_sewing_input  += $floor_value['SEWING_INPUT'];
										$total_floor_shift_a       += $floor_value['SHIFT_A'];
										$total_floor_shift_b       += $floor_value['SHIFT_B'];
										$total_floor_shift_c       += $floor_value['SHIFT_C'];
										$total_floor_sewing_output += $floor_sewing_output;

										$total_floor_finishing_qnty += $floor_value['FINISHING_QNTY'];
					        }
								
							?>
							<tr>
									<td colspan="2" align="center">Total</td>
									<td width="100" align="right" style="font-weight:bold;"><? echo number_format($total_floor_sewing_input,0);?></td>
									<td width="70" align="right" style="font-weight:bold;"><? echo number_format($total_floor_shift_a,0);?></td>
									<td width="70" align="right" style="font-weight:bold;"><? echo number_format($total_floor_shift_b,0);?></td>
									<td width="70" align="right" style="font-weight:bold;"><? echo number_format($total_floor_shift_c,0);?></td>
									<td width="100" align="right" style="font-weight:bold;"><? echo number_format($total_floor_sewing_output,0);?></td>
									<td width="100" align="right" style="font-weight:bold;"><? echo number_format($total_floor_finishing_qnty,0);?></td>
								</tr> 
								<?
								    $gr_total_floor_sewing_input += $total_floor_sewing_input;
									$gr_total_floor_shift_a      += $total_floor_shift_a;
									$gr_total_floor_shift_b      += $total_floor_shift_b;
									$gr_total_floor_shift_c      += $total_floor_shift_c;
									$gr_total_floor_sewing_output += $total_floor_sewing_output;
									$gr_total_floor_finishing_qnty += $total_floor_finishing_qnty;
                           
						}
						
								?>	
								<tr >
									<td colspan="2" align="center"> Grand Total</td>
									<td width="100" align="right" style="font-weight:bold;"><? echo number_format($gr_total_floor_sewing_input,0);?></td>
									<td width="70" align="right" style="font-weight:bold;"><? echo number_format($gr_total_floor_shift_a,0);?></td>
									<td width="70" align="right" style="font-weight:bold;"><? echo number_format($gr_total_floor_shift_b,0);?></td>
									<td width="70" align="right" style="font-weight:bold;"><? echo number_format($gr_total_floor_shift_c,0);?></td>
									<td width="100" align="right" style="font-weight:bold;"><? echo number_format($gr_total_floor_sewing_output,0);?></td>
									<td width="100" align="right" style="font-weight:bold;"><? echo number_format($gr_total_floor_finishing_qnty,0);?></td>
								</tr> 
							</tbody>                   
				</table>
				<br>
		
				 <!-- ============================ Details Part ============================ -->
				<table id="report_container3" class="rpt_table" width="1570" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<tr>
							<td colspan="16"> 
							<p align="left" style="font-weight:bold;">Production Details</p>
							</td>
					    </tr>
						<tr>
						<th colspan="11" align="right"></th>
						<th colspan="4" align="center">Sew Output</th>
						<th colspan="1" align="center"></th>
						<th width="100"></th>
						</tr>
						<tr height="50">
							<th width="40">Sl.</th>
							<th width="100">Floor/Unit Name</th>
							<th width="100">Group Name</th>
							<th width="100">Line No</th>
							<th width="100">Job No</th>
							<th width="100">Order Number</th>					
							<th width="100">Buyer Name</th>
							<th width="100">Style Name</th>
							<th width="100">Item Name</th>
							<th width="100">Production Date</th>
							<th width="100">Sewing Input</th>
							<th width="70">Shift-A</th>
							<th width="70">Shift-B</th>
							<th width="70">Shift-C</th>
							<th width="100">Total Sewing Output</th>	
							<th width="100">Total Finish Qty</th>
							<th width="100">Remarks</th>
						</tr>
					</thead>
				</table>
				<table class="rpt_table" width="1570" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_details">
					<tbody id='scroll_body'>
						<?
						$i=1;
						    $gr_total_sewing_input = 0;
							$gr_total_shift_a      = 0;
							$gr_total_shift_b      = 0;
							$gr_total_shift_c       = 0;
							$gr_total_sewing_output = 0;
                            $gr_total_finishing_qnty= 0;
					foreach($data_array as $prod_key=>$prod_value)
					{   		
						foreach($prod_value as $floor_key=>$floor_value)
						{    
								$total_sewing_input =0;
								$total_shift_a  =0;
								$total_shift_b            = 0;
								$total_shift_c            =0;
								$total_sewing_output      =0;
								$total_finishing_qnty     = 0;
								ksort($floor_value);
							foreach($floor_value as $slNo_key=>$slNo_value)
							{	
										foreach($slNo_value as $sewing_line_id_key=>$sewing_line_value)
										{     
											
											foreach($sewing_line_value as $job_key=>$job_value)
											{
												
												foreach($job_value as $po_key=>$po_value)
												{
													foreach($po_value as $date_key=>$date_value)
													{
														$line_name = '';
														if($date_value['prod_reso_allo']==1)
														{
															
															//$sewing_line_ids=$sewing_line_id_key;
															$sl_ids_arr = explode(",", $sewing_line_id_key);
															foreach($sl_ids_arr as $line_id)
															{
																$line_name .= $lineArr[$line_id].",";
															}
															
														}
														else
														{
															$line_name=$lineArr[$sewing_line_id_key];
														}
														if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
														?>
																	<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_2nd<? echo $i; ?>">
																		<td width="40" align="center"><? echo $i; ?></td>
																		<td width="100" align="left"><? echo $production_floor[$floor_key];?></td>
																		<td width="100" align="left"><?=$sew_group_Arr[$sewing_line_id_key];?></td>
																		<td width="100" align="left"><? echo rtrim($line_name,",") ;?></td>
																		<td width="100" align="left"><? echo $date_value['JOB_NO_PREFIX_NUM']; ?></td>
																		<td width="100" align="left" style="word-wrap: break-word;word-break: break-all;"><? echo $date_value['PO_NUMBER']; ?></td>
																		
																		<td width="100" align="left" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_library[$date_value['BUYER_NAME']]; ?></td>
																		<td width="100" align="left" style="word-wrap: break-word;word-break: break-all;"><? echo $date_value['STYLE_REF_NO']; ?></td>
																		<td width="100" align="left" style="word-wrap: break-word;word-break: break-all;"><? echo $garments_item[$date_value['GMTS_ITEM_ID']]; ?></td>
																		<td width="100" align="left"><? echo change_date_format($date_value['PRODUCTION_DATE']); ?></td>
																		<td width="100" align="right"><? echo number_format($date_value['SEWING_INPUT'],0); ?></td>
																		<td width="70" align="right"><? echo number_format($date_value['SHIFT_A'],0); ?></td>
																		<td width="70" align="right"><? echo number_format($date_value['SHIFT_B'],0); ?></td>
																		<td width="70" align="right"><? echo number_format($date_value['SHIFT_C'],0); ?></td>
																		<td width="100" align="right">
																			<?
																			$sewing_output= $date_value['SHIFT_A']+$date_value['SHIFT_B']+$date_value['SHIFT_C'];
																			echo number_format($sewing_output,0);
																			?>
																		</td>
																		<td width="100" align="right"><? echo number_format($date_value['FINISHING_QNTY'],0); ?></td>
																		
																		<td style="word-wrap: break-word;word-break: break-all;" width="100">
																			<? $po_id = $date_value['PO_ID'];?>
																			<a href="##"  onclick="openmypage_remark(<? echo $po_id;?>,'remark_popup');" > Veiw  </a>
																		</td>
																	</tr>
																<?
																$i++;
																$total_sewing_input       += $date_value['SEWING_INPUT'];
																$total_shift_a            += $date_value['SHIFT_A'];
																$total_shift_b            += $date_value['SHIFT_B'];
																$total_shift_c            += $date_value['SHIFT_C'];
																$total_sewing_output      += $sewing_output;
																$total_finishing_qnty     += $date_value['FINISHING_QNTY'];
													
													
								}	
							
							
						
								
							}	
							
							
					
						}
											
						
					  }
					 
				    }	
					$gr_total_sewing_input += $total_sewing_input;
							$gr_total_shift_a     += $total_shift_a;
							$gr_total_shift_b      += $total_shift_b;
							$gr_total_shift_c       += $total_shift_c;
							$gr_total_sewing_output += $total_sewing_output;
									$gr_total_finishing_qnty += $total_finishing_qnty;
						?>
						<tr style="background:3371ff;"> 
							<td colspan="10" align="center" style="font-weight:bold;">Unit Total</td>
						
							<td width="100" align="right" style="font-weight:bold;"><? echo number_format($total_sewing_input,0);?></td>
							<td width="70" align="right" style="font-weight:bold;"><? echo number_format($total_shift_a,0);?></td>
							<td width="70" align="right" style="font-weight:bold;"><? echo number_format($total_shift_b,0);?></td>
							<td width="70" align="right" style="font-weight:bold;"><? echo number_format($total_shift_c,0);?></td>
							<td width="100" align="right" style="font-weight:bold;"><? echo number_format($total_sewing_output,0);?></td>
						
							<td width="100" align="right" style="font-weight:bold;"><? echo number_format($total_finishing_qnty ,0);?></td>
						
					</tr> 
					<?
				}
			}		
						
						?>
							
							<tr style="background:#dfdfdf;"> 
								<td colspan="10" align="center" style="font-weight:bold;">Grand Total</td>
							
								<td width="100" align="right" style="font-weight:bold;"><? echo number_format($gr_total_sewing_input,0);?></td>
								<td width="70" align="right" style="font-weight:bold;"><? echo number_format($gr_total_shift_a,0);?></td>
								<td width="70" align="right" style="font-weight:bold;"><? echo number_format($gr_total_shift_b,0);?></td>
								<td width="70" align="right" style="font-weight:bold;"><? echo number_format($gr_total_shift_c,0);?></td>
								<td width="100" align="right" style="font-weight:bold;"><? echo number_format($gr_total_sewing_output,0);?></td>
								
								<td width="100" align="right" style="font-weight:bold;"><? echo number_format($gr_total_finishing_qnty ,0);?></td>
								
							</tr> 
					</tbody>                   
				</table>
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

if($action=="remark_popup")
{	
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');

	 $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	 $buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

 ?>
 <div id="view_part" class="view_part">

	<table>
	<? 
    		
			 $sql= "SELECT a.buyer_name,b.id as po_id,b.po_number from wo_po_details_master a, wo_po_break_down b where  a.id = b.job_id and b.is_deleted=0 and b.status_active=1  and a.is_deleted=0 and a.status_active=1";
			// echo $sql ;die;
			 $result=sql_select($sql);
			 $data_array = array();
		   
		foreach($result as $row)
		{
           $data_array[$row['PO_ID']]['buyer_name'] = $row['BUYER_NAME'];
		   $data_array[$row['PO_ID']]['po_number'] = $row['PO_NUMBER'];
		  

		}
		// echo "<pre>";
		// print_r($data_array); die;
 			

	?>
		<tr>

			<th style="font-size: 15px">
			Buyer :<?= $buyer_library[$row['BUYER_NAME']];?> &nbsp;&nbsp; Po No.<?= $row['PO_NUMBER'];?> 
			</th>
		</tr>
	</table>

    <fieldset style="width:505px">
	
      <legend>Color wise Sewing Production</legend>
		<? 
    		$i=1;
    		 $sql= "SELECT c.color_number_id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b, wo_po_color_size_breakdown c where  a.id=b.mst_id and c.id=b.color_size_break_down_id and b.is_deleted=0 and b.status_active=1  and b.production_type='5' and a.po_break_down_id='$po_break_down_id'  and a.production_type='5' and a.is_deleted=0 and a.status_active=1 group by c.color_number_id,a.production_date,a.remarks";
			 //echo $sql;die;

			 $result=sql_select($sql);
 			 $avg_prod_qty="";

			?>
		<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<th>SL No</th>
				<th>Color</th>
				<th>Production Date</th>
				<th>Production Qnty</th>
				<th>Remarks</th>
			</thead>
			<?
			foreach($result as $row)
			{
				?>
				<tr>
					<td width="50"><? echo $i;?></td>
					<td width="100"><? echo $color_library[$row['COLOR_NUMBER_ID']] ;?></td>
				
					<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
					<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
					<td>
					<? echo $row[csf('remarks')];
						$avg_prod_qty+=$row[csf('production_quantity')];
					?>
					</td>
				</tr>
				<?
				$i++;
			}
			?>
			<tfoot>
				<th align="right" colspan="3">Total</th>
				<th align=""><? echo $avg_prod_qty; ?></th>
			</tfoot>
		</table>
    </fieldset>
	<br>
	<fieldset style="width:505px">
    <legend>Color wise Finishing Production</legend>
    	<? 
    		$i=1;
			 $sql= "SELECT  c.color_number_id,a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b,  wo_po_color_size_breakdown c where   a.id=b.mst_id  and c.id=b.color_size_break_down_id and b.is_deleted=0 and b.status_active=1 and b.production_type='8' and   a.po_break_down_id='$po_break_down_id' and a.production_type='8'  and a.is_deleted=0 and a.status_active=1 group by c.color_number_id, a.id,a.production_date,a.remarks";
			  //echo $sql;die;
			 $result=sql_select($sql);
 			 $avg_prod_qty="";

			?>
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Color</th>
			<th>Production Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
		{
			?>
			<tr>
				<td width="50"><? echo $i;?></td>
				<td width="100"><? echo $color_library[$row['COLOR_NUMBER_ID']] ;?></td>
				<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
				<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
				<td>
				<? echo $row[csf('remarks')];
					$avg_prod_qty+=$row[csf('production_quantity')];
				?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
		<tfoot>
			<th align="right" colspan="3">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>
 </div>

 <div id="view_part2"></div>


 	<script type="text/javascript">
		//var contents=contents.trim();
	    document.getElementById('view_part2').innerHTML='<input type="button" onclick="new_window()" value="Print" name="Print" class="formbutton" style="width:100px;margin-left:200px;"/>';


		function new_window()
	    {
	        
	        var w = window.open("Surprise", "#");
	        var d = w.document.open();
	        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body style="font-size:12px; font-family:Arial Narrow">'+document.getElementById('view_part').innerHTML+'</body</html>');
	        d.close();
	    }

 	</script>
	<?
}

