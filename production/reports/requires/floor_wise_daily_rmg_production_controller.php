<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.commisions.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.conversions.php');
require_once('../../../includes/class4/class.others.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.commercials.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/cm_gmt_class.php');


$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
$floor_group_library=return_library_array( "select id,group_name from lib_prod_floor", "id", "group_name"  );

if ($action=="load_drop_down_buyer")
{
	// echo create_drop_down( "cbo_buyer_name", 100, "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.party_type='1' order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "" );     	 
	exit();
  exit();	 
}

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if ($action=="load_drop_down_location")
{
    extract($_REQUEST);
    $choosenCompany = $choosenCompany;  
	echo create_drop_down( "cbo_location_name", 100, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_floor")
{
    extract($_REQUEST);
    $choosenLocation = $choosenLocation;  
	echo create_drop_down( "cbo_floor_name", 100, "SELECT id,floor_name from lib_prod_floor where location_id in( $choosenLocation ) and status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
	exit();
}

if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $buyer;die;
	?>
	
	<script>
    function js_set_value(id)
    {
		//alert(id);
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
    }
    </script>
    </head>
    <body>
    <div align="center" style="width:820px;">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:800px;">
            <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" id="selected_id" name="selected_id" />
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center"> 
							<?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td align="center">
                        	 <? 
								//echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								if($buyer>0) $buy_cond=" and a.id=$buyer";
								echo create_drop_down( "cbo_buyer_name", 140, "select a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 $buy_cond order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'floor_wise_daily_rmg_production_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
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

if ($action=="job_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);
	if($company_id==0)
	{
		echo "Please Select Company Name";
		die;
	}
	//echo $company_id."==".$buyer_id."==".$search_type."==".$search_value."==".$cbo_year;die;
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
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	else $year_cond="";
	
	if($db_type==2)
	{
		$group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
		$year_field="to_char(a.insert_date,'YYYY')";
	} 
	else if($db_type==0) 
	{
		$group_field="group_concat(distinct b.po_number ) as po_number";
		$year_field="YEAR(a.insert_date)";
	}

	$arr=array (2=>$company_arr,3=>$buyer_arr);
	$sql= "SELECT a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year , $group_field
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and b.status_active in(1,2,3) and a.company_name=$company_id $buyer_cond $year_cond $search_con 
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date
	order by a.id";
	//echo $sql;//die;
	$rows=sql_select($sql);
	?>
    <table width="800" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Company</th>
                <th width="120">Buyer</th>
                <th width="50">Year</th>
                <th width="120">Job no</th>
                <th width="120">Style</th>
                <th>Po number</th>
                
            </tr>
       </thead>
    </table>
    <div style="max-height:820px; overflow:auto;">
    <table id="table_body2" width="800" border="1" rules="all" class="rpt_table">
     <? $rows=sql_select($sql);
         $i=1;
         foreach($rows as $data)
         {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
			?>
			<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="120"><p><? echo $company_arr[$data[csf('company_name')]]; ?></p></td>
                <td width="120"><p><? echo $buyer_short_library[$data[csf('buyer_name')]]; ?></p></td>
                <td align="center" width="50"><p><? echo $data[csf('year')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('job_no')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
                <td><p><? echo $po_num; ?></p></td>
			</tr>
			<? 
			$i++; 
		} 
		?>
    </table>
    </div>
    <?
	
	//echo $sql;
	//echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","230",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	//echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}

if($action=="job_wise_search")
{
	/*
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
	*/
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
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$job_cond='';
	if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and a.job_no_mst='".$job_no."'";
	else if($cbo_year!=0)
	{
		if($db_type==0) $job_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
		if($db_type==2) $job_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	
	$sql = "SELECT distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,$insert_year as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active in(1,2,3)  $company_name $job_cond  $buyer_name $style_cond";
	//echo $sql;//die;
	echo create_list_view("list_view", "Year,Job No,Style Ref,Order Number","50,100,120,150,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

//int ref wise browse------------------------------//
if($action=="int_ref_wise_search")
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
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$job_cond='';
	if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and a.job_no_mst='".$job_no."'";
	else if($cbo_year!=0)
	{
		if($db_type==0) $job_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
		if($db_type==2) $job_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	
	$sql = "SELECT distinct a.id,a.po_number,a.grouping,b.style_ref_no,b.job_no_prefix_num,$insert_year as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active in(1,2,3)  $company_name $job_cond  $buyer_name $style_cond order by a.id desc";
	//echo $sql;//die;
	echo create_list_view("list_view", "Year,Job No,Style Ref,Int Ref,Order Number","50,100,130,130,130,","550","310",0, $sql , "js_set_value", "id,grouping", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,grouping,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

$colorname_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from   lib_country", "id", "country_name");
$floor_arr=return_library_array( "select id, floor_name from   lib_prod_floor", "id", "floor_name");
$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1 and form_name='knit_order_entry'",'master_tble_id','image_location');

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$_SESSION['txt_production_date'] = $txt_production_date;
	$_SESSION['working_company_id'] = str_replace("'", "", $cbo_work_company_name);
	$_SESSION['location_id'] = str_replace("'", "", $cbo_location_name);
	
	if(str_replace("'","",$type)==1) // show
	{
		// ============================= getting form value =============================
		$working_company_id = str_replace("'", "", $cbo_work_company_name);
		$location_id 		= str_replace("'", "", $cbo_location_name);
		$floor_id 			= str_replace("'", "", $cbo_floor_name);
		$group_name 		= str_replace("'", "", $cbo_group_name);
		$buyer_id 			= str_replace("'", "", $cbo_buyer_name);
		$year 				= str_replace("'", "", $cbo_year);
		$job_id 			= str_replace("'", "", $txt_job_no);
		$hidden_job_id 		= str_replace("'", "", $hidden_job_id);
		$txt_int_ref 		= str_replace("'", "", $txt_int_ref);
		$order_id 			= str_replace("'", "", $txt_order_no);
		$hidden_order_id 	= str_replace("'", "", $hidden_order_id);
		$shiping_status 	= str_replace("'", "", $cbo_shipping_status);
		// echo $location_id."_".$floor_id."_".$group_name;

		$sql_cond_lay 		= "";
		$sql_cond_qc 		= "";
		$sql_cond_prod 		= "";
		$sql_cond_exfact 	= "";

		$lay_cond 			= "";
		$qc_cond 			= "";
		$exfact_cond 		= "";

		if($working_company_id !="")
		{
			$sql_cond_lay 	= " and d.working_company_id in($working_company_id)";
			$sql_cond_qc 	= " and d.serving_company in($working_company_id)";
			$sql_cond_prod 	= " and d.serving_company in($working_company_id)";
			$sql_cond_exfact= " and f.delivery_company_id in($working_company_id)";

			$lay_cond 		.= " and a.working_company_id in($working_company_id)";
			$qc_cond 		.= " and a.serving_company in($working_company_id)";
			$exfact_cond	.= " and d.delivery_company_id in($working_company_id)";
		}

		if($location_id !="")
		{
			$sql_cond_lay 	.= " and d.location_id in($location_id)";
			$sql_cond_qc 	.= " and d.location_id in($location_id)";
			$sql_cond_prod 	.= " and d.location in($location_id)";
			$sql_cond_exfact.= " and f.delivery_location_id in($location_id)";

			$lay_cond 		.= " and a.location_id in($location_id)";
			$qc_cond 		.= " and a.location_id in($location_id)";
			$exfact_cond	.= " and d.delivery_location_id in($location_id)";
		}

		if($shiping_status !="")
		{
			$sql_cond_lay 	.= " and b.shiping_status in($shiping_status)";
			$sql_cond_qc 	.= " and b.shiping_status in($shiping_status)";
			$sql_cond_prod 	.= " and b.shiping_status in($shiping_status)";
			$sql_cond_exfact.= " and b.shiping_status in($shiping_status)";
		}

		if($group_name)
		{
			$group_cond="";
			$group_sql = sql_select("SELECT a.id from lib_prod_floor a where a.status_active=1 and a.group_name=$cbo_group_name order by a.id");
			foreach ($group_sql as $value) 
			{
				if($group_cond=="")
				{
					$group_cond = $value[csf('id')];
				}
				else
				{
					$group_cond .= ",".$value[csf('id')];
				}
			}
			$sql_cond_lay.=" and d.floor_id in($group_cond)";
			$sql_cond_qc.=" and d.floor_id in($group_cond)";
			$sql_cond_prod.=" and d.floor_id in($group_cond)";
			$sql_cond_exfact.=" and f.delivery_floor_id in($group_cond)";

			$lay_cond 		.= " and a.floor_id in($group_cond)";
			$qc_cond 		.= " and a.floor_id in($group_cond)";
			$exfact_cond	.= " and d.delivery_floor_id in($group_cond)";
		}
		else if($floor_id !="")
		{
			$sql_cond_lay 	.= " and d.floor_id in($floor_id)";
			$sql_cond_qc 	.= " and d.floor_id in($floor_id)";
			$sql_cond_prod 	.= " and d.floor_id in($floor_id)";
			$sql_cond_exfact.= " and f.delivery_floor_id in($floor_id)";

			$lay_cond 		.= " and a.floor_id in($floor_id)";
			$qc_cond 		.= " and a.floor_id in($floor_id)";
			$exfact_cond	.= " and d.delivery_floor_id in($floor_id)";
		}

		if($buyer_id !=0)
		{
			$sql_cond_lay 	.= " and a.buyer_name = $buyer_id";
			$sql_cond_qc 	.= " and a.buyer_name = $buyer_id";
			$sql_cond_prod 	.= " and a.buyer_name = $buyer_id";
			$sql_cond_exfact.= " and a.buyer_name = $buyer_id";
		}

		if($year !=0)
		{
			if($db_type==0)
			{
				$sql_cond_lay .=" and year(a.insert_date)=$year";
				$sql_cond_qc .=" and year(a.insert_date)=$year";
				$sql_cond_prod .=" and year(a.insert_date)=$year";
				$sql_cond_exfact .=" and year(a.insert_date)=$year";
			}
			else
			{
				$sql_cond_lay .=" and to_char(a.insert_date,'YYYY')=$year";
				$sql_cond_qc .=" and to_char(a.insert_date,'YYYY')=$year";
				$sql_cond_prod .=" and to_char(a.insert_date,'YYYY')=$year";
				$sql_cond_exfact .=" and to_char(a.insert_date,'YYYY')=$year";
			}
		}

		if($hidden_job_id!="")
		{
			$sql_cond_lay.=" and a.id in($hidden_job_id)";
			$sql_cond_qc.=" and a.id in($hidden_job_id)";
			$sql_cond_prod.=" and a.id in($hidden_job_id)";
			$sql_cond_exfact.=" and a.id in($hidden_job_id)";
		}
		
		if($hidden_order_id !="")
		{
			$sql_cond_lay.=" and b.id in($hidden_order_id)";
			$sql_cond_qc.=" and b.id in($hidden_order_id)";
			$sql_cond_prod.=" and b.id in($hidden_order_id)";
			$sql_cond_exfact.=" and b.id in($hidden_order_id)";
		} 

		/*if($txt_int_ref !="")
		{
			$sql_cond_lay.=" and b.grouping like '%$txt_int_ref%'";
			$sql_cond_qc.=" and b.grouping like '%$txt_int_ref%'";
			$sql_cond_prod.=" and b.grouping like '%$txt_int_ref%'";
			$sql_cond_exfact.=" and b.grouping like '%$txt_int_ref%'";
		}*/ 

		// ======================================== MAIN QUERY FOR LAY =========================================
		$today_lay_sql="SELECT a.company_name, c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
		where a.id=b.job_id and b.id=c.po_break_down_id and d.job_no=a.job_no and d.id=e.mst_id  and a.id=c.job_id and e.mst_id=f.mst_id and f.order_id=b.id and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_confirmed=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.entry_date=$txt_production_date $sql_cond_lay
		group by a.company_name,c.color_number_id,b.id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty"; 
		// echo $today_lay_sql;die();
		

		$po_id_array=array();
		$col_id_array=array();
		$production_main_array=array();
		$buyer_wise_summary_array=array();
		$buyer_wise_all_info=array();
		$lc_company_array = array();
		$all_job_array = array();
		foreach(sql_select($today_lay_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_job_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("working_company_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_set_qnty"]=$row[csf("total_set_qnty")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

			$buyer_wise_summary_array[$row[csf("buyer_name")]]['unit_price'] = $row[csf("unit_price")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['total_set_qnty'] = $row[csf("total_set_qnty")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['job_no'] = $row[csf("job_no")];

			$lc_company_array[$row[csf("company_name")]] = $row[csf("company_name")];

		}

		// ======================================== MAIN QUERY FOR CUTTING QC =========================================
		$today_qc_sql="SELECT a.company_name, c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.serving_company,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_gmts_cutting_qc_mst d,pro_gmts_cutting_qc_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.job_no=a.job_no and d.id=e.mst_id  and a.id=c.job_id and b.id=e.order_id  and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_confirmed=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.entry_date=$txt_production_date $sql_cond_qc
		group by a.company_name, c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.serving_company,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty"; //and c.id=e.color_size_id
		// echo $today_qc_sql;die();		
		
		
		foreach(sql_select($today_qc_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_job_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("serving_company")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_set_qnty"]=$row[csf("total_set_qnty")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

			$buyer_wise_summary_array[$row[csf("buyer_name")]]['unit_price'] = $row[csf("unit_price")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['total_set_qnty'] = $row[csf("total_set_qnty")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['job_no'] = $row[csf("job_no")];

			$lc_company_array[$row[csf("company_name")]] = $row[csf("company_name")];

		}

		// ======================================== MAIN QUERY FOR PRODUCTION =========================================
		$today_prod_sql="SELECT a.company_name, c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cut_no,b.po_number,d.serving_company,d.location,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty,d.production_type  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.id=e.mst_id  and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_confirmed=1  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.production_date=$txt_production_date $sql_cond_prod
		group by a.company_name, c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,d.cut_no,b.po_number,d.serving_company,d.location,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty,d.production_type  "; //and c.id=e.color_size_break_down_id
		// echo $today_prod_sql;die();		
		
		$sewing_floor_arr = array();
		foreach(sql_select($today_prod_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_job_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
			if($row[csf("production_type")]==4)
			{
				$sewing_floor_arr[$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("floor_id")]] = $row[csf("floor_id")];
			}
			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("serving_company")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_set_qnty"]=$row[csf("total_set_qnty")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

			$buyer_wise_summary_array[$row[csf("buyer_name")]]['unit_price'] = $row[csf("unit_price")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['total_set_qnty'] = $row[csf("total_set_qnty")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['job_no'] = $row[csf("job_no")];

			$lc_company_array[$row[csf("company_name")]] = $row[csf("company_name")];

		}

		// ======================================== MAIN QUERY FOR EX-FACTORY =========================================
		$today_exf_sql="SELECT a.company_name, c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,b.po_number,f.delivery_company_id,f.delivery_location_id,f.delivery_floor_id, a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_ex_factory_mst d,pro_ex_factory_dtls e, pro_ex_factory_delivery_mst f
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.id=e.mst_id and f.id=d.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_confirmed=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.ex_factory_date=$txt_production_date $sql_cond_exfact
		group by a.company_name, c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,b.po_number,f.delivery_company_id,f.delivery_location_id,f.delivery_floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty"; //and c.id=e.color_size_break_down_id
		// echo $today_exf_sql;die();		
		
		
		foreach(sql_select($today_exf_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_job_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("delivery_company_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("delivery_location_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("delivery_floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_set_qnty"]=$row[csf("total_set_qnty")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

			$buyer_wise_summary_array[$row[csf("buyer_name")]]['unit_price'] = $row[csf("unit_price")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['total_set_qnty'] = $row[csf("total_set_qnty")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['job_no'] = $row[csf("job_no")];

			$lc_company_array[$row[csf("company_name")]] = $row[csf("company_name")];

		}
		// echo "<pre>";
		// print_r($production_main_array);die;
		if(count($production_main_array)==0)
		{
			?>
			<div style="margin:20px auto; width: 90%">
				<div class="alert alert-error">
				  <strong>Data not found!</strong> Change a few things up and try submitting again.
				</div>
			</div>
			<?
			die();
		} 

		$prod_po_ids=implode(",", $prod_po_id_array);
		if($prod_po_ids)
		{
			$po_conds2=" and b.id in($prod_po_ids)";
		}

		$po_ids=implode(",", $po_id_array);
		$color_ids=implode(",", $col_id_array);
		if(!$po_ids) $po_ids=0;
		if(!$color_ids) $color_ids=0;

		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( po_id_string in ($ids) ";
				else
					$po_cond.=" or   po_id_string in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and po_id_string in ($po_ids) ";
		}
		//echo $po_cond;die;
		// ===================================== FOR COLOR WISE ORDER QTY ========================================	
		$poIDs=implode(",", $po_id_array);
		// if($poIDs!="")
		// {
		// 	$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($working_company_id,$poIDs); 
		// 	//print_r($cm_gmt_cost_dzn_arr);
		// }
		$lc_company = implode(",", $lc_company_array);
		$cm_gmt_cost_dzn_arr=array();
		$cm_gmt_cost_dzn_arr_new=array();				 
		$new_arr=array_unique(explode(",", $poIDs));
		$chnk_arr=array_chunk($new_arr,50);
		foreach($chnk_arr as $vals )
		{
			$p_ids=implode(",", $vals);
			$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($lc_company,$p_ids); 
			 foreach($cm_gmt_cost_dzn_arr as $po_id=>$vv)
			 {
			 	$cm_gmt_cost_dzn_arr_new[$po_id]["dzn"]=$vv["dzn"] ;
			 }
		}

		$order_qnty_array=array();
		$buyer_wise_order_qnty_array=array();
		$po_cond1=str_replace("po_id_string", "po_break_down_id", $po_cond);
		$order_qnty_sqls="SELECT b.po_break_down_id,b.color_number_id,b.item_number_id,sum(b.order_quantity) as order_quantity,a.buyer_name from wo_po_details_master a, wo_po_color_size_breakdown b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $po_cond1 group by b.po_break_down_id,b.color_number_id,b.item_number_id,a.buyer_name";
		// echo $order_qnty_sqls;die();
		foreach(sql_select($order_qnty_sqls) as $values)
		{
		 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]=$values[csf("order_quantity")];

		 	$buyer_wise_order_qnty_array[$values[csf("buyer_name")]] += $values[csf("order_quantity")];
		}
		
		// echo "<pre>";
		// print_r($buyer_wise_summary_array);
		// echo "</pre>";
		// die();
		// ============================================ FOR PRODUCTION ================================================
		$po_cond2=str_replace("po_id_string", "b.id", $po_cond);
		$order_sql="SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no,c.color_number_id, 
		sum(c.order_quantity) as order_quantity, 
		sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_cutting ,
		sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_issue_to_print ,
		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=1  then e.production_qnty else 0 end ) as total_issue_to_print ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_rcv_frm_print ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=1  then e.production_qnty else 0 end ) as total_rcv_frm_print ,
		sum(case when d.production_type in(2,3) and e.production_type in(2,3) and d.embel_name=1  then e.reject_qty else 0 end ) as print_reject ,


		sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_input ,
		sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input ,

		sum(case when d.production_type=5 and e.production_type=5 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_output ,
		sum(case when d.production_type=5 and e.production_type=5   then e.production_qnty else 0 end ) as total_sewing_output ,

		sum(case when d.production_type=8 and e.production_type=8 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_finishing ,
		sum(case when d.production_type=8 and e.production_type=8   then e.production_qnty else 0 end ) as total_finishing,

		sum(case when d.production_type=8 and e.production_type=8 and d.production_date=$txt_production_date then d.carton_qty else 0 end ) as today_carton_qty,
		sum(case when d.production_type=8 and e.production_type=8  then d.carton_qty else 0 end ) as total_carton_qty

		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $sql_cond_prod $po_cond2 and d.production_type in(1,2,3,4,5,8,11) 
		group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  ,  c.color_number_id,e.cut_no ";

		// echo $order_sql;die;
		$order_sql_res = sql_select($order_sql);
		$cutting_sewing_data = array();
		$buyer_wise_cutting_sewing_data = array();
		foreach($order_sql_res as $vals)
		{
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_cutting"]+=$vals[csf("today_cutting")];			 

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_cutting"]+=$vals[csf("total_cutting")];
			 

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_issue_to_print"]+=$vals[csf("today_issue_to_print")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_issue_to_print"]+=$vals[csf("total_issue_to_print")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_rcv_frm_print"]+=$vals[csf("today_rcv_frm_print")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_rcv_frm_print"]+=$vals[csf("total_rcv_frm_print")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["print_reject"]+=$vals[csf("print_reject")];


			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

			 
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_output"]+=$vals[csf("today_sewing_output")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_output"]+=$vals[csf("total_sewing_output")];


			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][0]["today_finishing"]+=$vals[csf("today_finishing")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][0]["total_finishing"]+=$vals[csf("total_finishing")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_carton_qty"]+=$vals[csf("today_carton_qty")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_carton_qty"]+=$vals[csf("total_carton_qty")];
			// ======================================== BUYER WISE SUM ======================================================
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_cutting"]			+= $vals[csf("today_cutting")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_cutting"]			+= $vals[csf("total_cutting")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_issue_to_print"] 	+= $vals[csf("today_issue_to_print")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_issue_to_print"] 	+= $vals[csf("total_issue_to_print")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_rcv_frm_print"] 	+= $vals[csf("today_rcv_frm_print")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_rcv_frm_print"] 	+= $vals[csf("total_rcv_frm_print")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["print_reject"]	 		+= $vals[csf("print_reject")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_sewing_input"] 	+= $vals[csf("today_sewing_input")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_sewing_input"] 	+= $vals[csf("total_sewing_input")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_sewing_output"] 	+= $vals[csf("today_sewing_output")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_sewing_output"] 	+= $vals[csf("total_sewing_output")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_finishing"]		+= $vals[csf("today_finishing")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_finishing"] 		+= $vals[csf("total_finishing")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_carton_qty"] 		+= $vals[csf("today_carton_qty")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_carton_qty"] 		+= $vals[csf("total_carton_qty")];
		}
		//echo "<pre>";
		//print_r($cutting_sewing_data);die;

		// =========================================== FOR CUTTING LAY QTY ==========================================
	  	$po_cond3=str_replace("po_id_string", "c.order_id", $po_cond); 	
	  	$lay_sqls="SELECT  a.job_no,c.order_id, b.gmt_item_id,b.color_id,sum( case when a.entry_date=$txt_production_date then  c.size_qty else 0 end) as today_lay,sum(c.size_qty) as total_lay,d.buyer_name from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c,wo_po_details_master d where a.job_no=d.job_no and a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond3  $lay_cond  group by a.job_no,c.order_id, b.gmt_item_id,b.color_id,d.buyer_name ";
	  	// echo $lay_sqls;die();
		$lay_qnty_array=array();
		$buyer_wise_lay_qnty_array=array();
		foreach(sql_select($lay_sqls) as $vals)
		{
			$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["today_lay"]+=$vals[csf("today_lay")];
			$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("total_lay")];
			$buyer_wise_lay_qnty_array[$vals[csf("buyer_name")]]["today_lay"]+=$vals[csf("today_lay")];
			$buyer_wise_lay_qnty_array[$vals[csf("buyer_name")]]["total_lay"]+=$vals[csf("total_lay")];
		}
		// echo "<pre>";
		// print_r($buyer_wise_lay_qnty_array);
		// echo "</pre>";
		// =========================================== FOR CUTTING QC ==========================================
	  	$po_cond_qc=str_replace("po_id_string", "b.order_id", $po_cond); 	
	  	$qc_sqls="SELECT  a.job_no,b.order_id, c.item_number_id,b.color_id,d.buyer_name,
	  	sum( case when a.entry_date=$txt_production_date then  b.qc_pass_qty else 0 end) as today_qc,
	  	sum(b.qc_pass_qty) as total_qc,
	  	sum(b.reject_qty) as total_rej 
	  	from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b,wo_po_color_size_breakdown c,wo_po_details_master d
	  	where d.job_no=a.job_no and a.id=b.mst_id and b.color_size_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 $po_cond_qc $qc_cond
	  	group by a.job_no,b.order_id, c.item_number_id,b.color_id,d.buyer_name";
	  	// echo $qc_sqls;die();
		$qc_qnty_array=array();
		$buyer_wise_qc_qnty_array=array();
		foreach(sql_select($qc_sqls) as $vals)
		{
			$qc_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("item_number_id")]][$vals[csf("color_id")]]["today_qc"]+=$vals[csf("today_qc")];
			$qc_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("item_number_id")]][$vals[csf("color_id")]]["total_qc"]+=$vals[csf("total_qc")];
			$qc_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("item_number_id")]][$vals[csf("color_id")]]["total_rej"]+=$vals[csf("total_rej")];
			$buyer_wise_qc_qnty_array[$vals[csf('buyer_name')]]["today_qc"]+=$vals[csf("today_qc")];
			$buyer_wise_qc_qnty_array[$vals[csf('buyer_name')]]["total_qc"]+=$vals[csf("total_qc")];
			$buyer_wise_qc_qnty_array[$vals[csf('buyer_name')]]["total_rej"]+=$vals[csf("total_rej")];
		}

		// ==================================================== FOR EX-FACTORY QTY ==========================================
		$po_cond4=str_replace("po_id_string", "a.po_break_down_id", $po_cond); 	
		$ex_factory_arr=array();
		$buyer_wise_ex_factory_arr=array();
		$ex_factory_data="SELECT a.po_break_down_id, a.item_number_id,c.color_number_id,d.buyer_id, sum(CASE WHEN a.entry_form!=85 and ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS today_ex_fac , sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c where d.id=a.delivery_mst_id  and  a.id=b.mst_id and b.color_size_break_down_id=c.id   and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $exfact_cond  $po_cond4  group by a.po_break_down_id, a.item_number_id,c.color_number_id,d.buyer_id";
		// echo $ex_factory_data;
		$ex_factory_data_res = sql_select($ex_factory_data);
		foreach($ex_factory_data_res as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['today_ex_fac']+=$exRow[csf('today_ex_fac')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['total_ex_fac']=+$exRow[csf('total_ex_fac')];
			$buyer_wise_ex_factory_arr[$exRow[csf('buyer_id')]]['today_ex_fac']+=$exRow[csf('today_ex_fac')];
			// $buyer_wise_ex_factory_arr[$exRow[csf('buyer_id')]]['total_ex_fac']+=$exRow[csf('total_ex_fac')];
		}
		// echo "<pre>";
		// print_r($ex_factory_arr);
		// echo "</pre>";
		// die();
		// =========================================== FOR FIN. FAB. REQ. QTY ========================================================
		$booking_no_fin_qnty_array=array();
		$buyer_booking_no_fin_qnty_array=array();
		$booking_sql=sql_select("SELECT a.po_break_down_id as po_id ,b.item_number_id as item_id, b.color_number_id as color_id,a.booking_no,a.fin_fab_qnty,c.buyer_name from wo_booking_dtls a,wo_po_color_size_breakdown b,wo_po_details_master c  where c.job_no=a.job_no and b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1  $po_cond4");
		foreach($booking_sql as $vals)
		{
			$booking_no_fin_qnty_array[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("color_id")]]+=$vals[csf("fin_fab_qnty")];
			// $buyer_booking_no_fin_qnty_array[$vals[csf("buyer_name")]]+=$vals[csf("fin_fab_qnty")];

		}
		// ============================================== FOR FAB. RCV AND ISSUE QTY ==============================================
		$po_cond_fab=str_replace("po_id_string", "c.po_breakdown_id", $po_cond); 
		// $fab_sql="SELECT po_breakdown_id,color_id,entry_form,sum(quantity) as quantity from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(18,37) $po_cond_fab group by po_breakdown_id,color_id,entry_form ";
		$fab_sql="SELECT c.po_breakdown_id,c.color_id,c.entry_form,sum(c.quantity) as quantity,a.buyer_name from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(18,37) $po_cond_fab group by c.po_breakdown_id,c.color_id,c.entry_form,a.buyer_name ";
		$fab_sql_res = sql_select($fab_sql);
		$issue_qnty_arr = array();
		$buyer_issue_qnty_arr = array();
		foreach($fab_sql_res as $values)
		{
		 	$issue_qnty_arr[$values[csf("po_breakdown_id")]][$values[csf("color_id")]][$values[csf("entry_form")]]+=$values[csf("quantity")];
		 	// $buyer_issue_qnty_arr[$values[csf("buyer_name")]][$values[csf("entry_form")]]+=$values[csf("quantity")];
		} 
		// ======================================= FOR COSTING PER ======================================
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$all_job = "'".implode("','", $all_job_array)."'";


		/******************************************************************************************************
		*																									  *
		*								GETTING PRICE QUOTATION WISE CM VALU							      *
		*																									  *			
		*******************************************************************************************************/
		$quotation_qty_sql="SELECT a.id  as quotation_id,a.mkt_no,a.sew_smv,a.sew_effi_percent,a.gmts_item_id,a.company_id,a.buyer_id,a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom,a.offer_qnty, a.total_set_qnty as ratio, a.quot_date,a.est_ship_date,b.costing_per_id,b.price_with_commn_pcs,b.total_cost,b.costing_per_id,c.job_no from wo_price_quotation a,wo_price_quotation_costing_mst b,wo_po_details_master c  where a.id=b.quotation_id and a.id=c.quotation_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.offer_qnty>0 and c.job_no in($all_job) order  by a.id ";
		// echo $quotation_qty_sql;die();
		$quotation_qty_sql_res = sql_select($quotation_qty_sql);
		$quotation_qty_array = array();
		$quotation_id_array = array();
		$all_jobs_array = array();
		$jobs_wise_quot_array = array();
		foreach ($quotation_qty_sql_res as $val) 
		{
			$quotation_qty_array[$val['JOB_NO']]['QTY_PCS'] += $val['OFFER_QNTY']*$val['RATIO'];
			$quotation_qty_array[$val['JOB_NO']]['COSTING_PER_ID'] += $val['COSTING_PER_ID'];
			$quotation_id_array[$val['QUOTATION_ID']] = $val['QUOTATION_ID'];
			$all_jobs_array[$val['JOB_NO']] = $val['JOB_NO'];
			$jobs_wise_quot_array[$val['JOB_NO']] = $val['QUOTATION_ID'];

			$quot_wise_arr[$val[csf("quotation_id")]]['offer_qnty']=$val[csf("offer_qnty")];
			$quot_wise_arr[$val[csf("quotation_id")]]['costing_per_id']=$val[csf("costing_per_id")];

			$style_wise_arr[$val[csf("job_no")]]['costing_per']=$val[csf("costing_per")];
			$style_wise_arr[$val[csf("job_no")]]['gmts_item_id']=$val[csf("gmts_item_id")];
			$style_wise_arr[$val[csf("job_no")]]['sew_smv']=$val[csf("sew_smv")];
			$style_wise_arr[$val[csf("job_no")]]['sew_effi_percent']=$val[csf("sew_effi_percent")];
			$style_wise_arr[$val[csf("job_no")]]['shipment_date'].=$val[csf('est_ship_date')].',';
			$style_wise_arr[$val[csf("job_no")]]['quotation_id']=$val[csf("quotation_id")];
			$style_wise_arr[$val[csf("job_no")]]['buyer_name']=$val[csf("buyer_id")];
			$offer_qnty_pcs=$val[csf('offer_qnty')]*$val[csf('ratio')];
			$style_wise_arr[$val[csf("job_no")]]['qty_pcs']+=$val[csf('offer_qnty')]*$val[csf('ratio')];
			$style_wise_arr[$val[csf("job_no")]]['qty']+=$val[csf('offer_qnty')];
			$style_wise_arr[$val[csf("job_no")]]['final_cost_pcs']+=$val[csf('price_with_commn_pcs')];
			$style_wise_arr[$val[csf("job_no")]]['total_cost']+=$offer_qnty_pcs*$val[csf('price_with_commn_pcs')];
		}	
		$all_quot_id = implode(",", $quotation_id_array);
		
		// print_r($style_wise_arr);die();
		// ===============================================================================
		$sql_fab = "SELECT a.quotation_id,sum(a.avg_cons) as cons_qnty, sum(a.amount) as amount,b.job_no from wo_pri_quo_fabric_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.fabric_source=2 and a.status_active=1 and b.status_active=1 group by  a.quotation_id,b.job_no"; 
		// echo $sql_fab;die();
		$data_array_fab=sql_select($sql_fab);
		foreach($data_array_fab as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$fab_order_price_per_dzn=12;}
			else if($costing_per_id==2){$fab_order_price_per_dzn=1;}
			else if($costing_per_id==3){$fab_order_price_per_dzn=24;}
			else if($costing_per_id==4){$fab_order_price_per_dzn=36;}
			else if($costing_per_id==5){$fab_order_price_per_dzn=48;}

			$fab_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn']+=$row[csf("amount")];
			$fab_summary_data[$row[csf("job_no")]]['fab_amount_total_value']+=($row[csf("amount")]/$fab_order_price_per_dzn)*$fab_order_job_qnty;
			//$yarn_amount_dzn+=$row[csf('amount')];
		}
		// ==================================================================================
		$sql_yarn = "SELECT a.quotation_id,sum(a.cons_qnty) as cons_qnty, sum(a.amount) as amount,b.job_no from wo_pri_quo_fab_yarn_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by  a.quotation_id,b.job_no"; 
		// echo $sql_yarn;die();
		$data_array_yarn=sql_select($sql_yarn);
		foreach($data_array_yarn as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$yarn_order_price_per_dzn=12;}
			else if($costing_per_id==2){$yarn_order_price_per_dzn=1;}
			else if($costing_per_id==3){$yarn_order_price_per_dzn=24;}
			else if($costing_per_id==4){$yarn_order_price_per_dzn=36;}
			else if($costing_per_id==5){$yarn_order_price_per_dzn=48;}
			$yarn_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			//$yarn_summary_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
			 $yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn']+=$row[csf("amount")];
			// $summary_data['yarn_amount_dzn']+=$yarn_summary_dzn;
			 //$yarn_summary_data['yarn_amount_total_value']+=($row[csf("amount")]/$yarn_order_price_per_dzn)*$yarn_order_job_qnty;
		}
		// ===================================================================================
		$conversion_cost_arr=array();
		$sql_conversion = "SELECT a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition,c.job_no
		from wo_po_details_master c, wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
		where a.quotation_id in($all_quot_id) and a.quotation_id=c.quotation_id and a.status_active=1  ";
		// echo $sql_conversion;die();
		$data_array_conversion=sql_select($sql_conversion);
		foreach($data_array_conversion as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$conv_order_price_per_dzn=12;}
			else if($costing_per_id==2){$conv_order_price_per_dzn=1;}
			else if($costing_per_id==3){$conv_order_price_per_dzn=24;}
			else if($costing_per_id==4){$conv_order_price_per_dzn=36;}
			else if($costing_per_id==5){$conv_order_price_per_dzn=48;}
			$conv_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn']+=$row[csf("amount")];

			$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_dzn']+=$row[csf('amount')];
			$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$conv_order_job_qnty;
		}
		// print_r($conversion_cost_arr);die();
		if($db_type==0)
		{
			$sql = "SELECT MAX(a.id),a.quotation_id,a.fabric_cost,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,a.offer_qnty,b.job_no
			from wo_price_quotation_costing_mst a,wo_po_details_master b
			where a.quotation_id in($all_quot_id) and a.status_active=1 and a.quotation_id=b.quotation_id and b.status_active=1 ";
		}
		if($db_type==2)
		{
			$sql = "SELECT MAX(a.id),a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no
			from wo_price_quotation_costing_mst a,wo_po_details_master b
			where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and b.status_active=1   group by a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no";
		}
		// echo $sql;die();
		$data_array=sql_select($sql);

        foreach( $data_array as $row )
        {
			//$sl=$sl+1;
			if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
			else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
			else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
			else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
			else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn']=$order_price_per_dzn;
			$price_dzn=$row[csf("confirm_price_dzn")];
			$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
			$summary_data[$row[csf('job_no')]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
			$summary_data[$row[csf('job_no')]]['price_with_total_value']+=($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['price_dzn']+=$row[csf("confirm_price_dzn")];
		    $summary_data[$row[csf('job_no')]]['commission_dzn']+=$row[csf("commission")];
			$summary_data[$row[csf('job_no')]]['commission_total_value']+=($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['trims_cost_dzn']+=$row[csf("trims_cost")];
			$summary_data[$row[csf('job_no')]]['trims_cost_total_value']+=($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['embel_cost_dzn']+=$row[csf("embel_cost")];
			$summary_data[$row[csf('job_no')]]['embel_cost_total_value']+=($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty;
			//$row[csf("commission")]
			$other_direct_expenses=$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];

			$summary_data[$row[csf('job_no')]]['other_direct_dzn']+=$other_direct_expenses;
			$summary_data[$row[csf('job_no')]]['other_direct_total_value']+=($other_direct_expenses/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['wash_cost_dzn']+=$row[csf("wash_cost")];
			$summary_data[$row[csf('job_no')]]['wash_cost_total_value']+=($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['lab_test_dzn']+=$row[csf("lab_test")];
			$summary_data[$row[csf('job_no')]]['lab_test_total_value']+=($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['inspection_dzn']+=$row[csf("inspection")];
			$summary_data[$row[csf('job_no')]]['inspection_total_value']+=($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['freight_dzn']+=$row[csf("freight")];
			$summary_data[$row[csf('job_no')]]['freight_total_value']+=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
			$freight_cost_data[$row[csf("job_no")]]['freight_total_value']=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['currier_pre_cost_dzn']+=$row[csf("currier_pre_cost")];
			$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']+=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['certificate_pre_cost_dzn']+=$row[csf("certificate_pre_cost")];
			$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']+=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['design_pre_cost_dzn']+=$row[csf("design_pre_cost")];
			$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value']+=($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['studio_pre_cost_dzn']+=$row[csf("studio_pre_cost")];
			$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']+=($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['studio_dzn_cost']=$row[csf("studio_percent")];
			$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['common_oh']=$row[csf("common_oh")];

			$fab_amount_dzn=$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn'];
			$summary_data[$row[csf('job_no')]]['fab_amount_dzn']+=$fab_amount_dzn;
			$summary_data[$row[csf('job_no')]]['fab_amount_total_value']+=($fab_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
			$yarn_amount_dzn=$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn'];
			//echo ($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty.'d';
			$summary_data[$row[csf('job_no')]]['yarn_amount_dzn']+=$yarn_amount_dzn;
			$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']+=($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
			$conv_amount_dzn=$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn'];
			$summary_data[$row[csf('job_no')]]['conversion_cost_dzn']+=$conv_amount_dzn;
			$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']+=($conv_amount_dzn/$order_price_per_dzn)*$order_job_qnty;

			//$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
			$net_value_dzn=$row[csf("price_with_commn_dzn")];

			$summary_data[$row[csf('job_no')]]['netfobvalue_dzn']+=($row[csf("price_with_commn_dzn")]);
			$summary_data[$row[csf('job_no')]]['netfobvalue']+=(($row[csf("price_with_commn_dzn")])/$order_price_per_dzn)*$order_job_qnty;

			//yarn_amount_total_value
			$all_cost_dzn=$yarn_amount_dzn+$fab_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses;
			//echo $yarn_amount_dzn.'Y='.$fab_amount_dzn.'F='.$conv_amount_dzn.'Cnv='.$row[csf("trims_cost")].'Tr='.$row[csf("embel_cost")].'Em='.$other_direct_expenses;
			$summary_data[$row[csf('job_no')]]['cost_of_material_service']+=$all_cost_dzn;
			$summary_data[$row[csf('job_no')]]['cost_of_material_service_total_value']+=($all_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
			$contribute_netfob_value_dzn=$net_value_dzn-($fab_amount_dzn+$yarn_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses);
			$summary_data[$row[csf('job_no')]]['contribution_margin_dzn']+=$contribute_netfob_value_dzn;
			$summary_data[$row[csf('job_no')]]['contribution_margin_total_value']+=(($contribute_netfob_value_dzn)/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['cm_cost_dzn']+=$row[csf("cm_cost")];
			$summary_data[$row[csf('job_no')]]['cm_cost_total_value']+=($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['comm_cost_dzn']+=$row[csf("comm_cost")];
			$summary_data[$row[csf('job_no')]]['comm_cost_total_value']+=($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['common_oh_dzn']+=$row[csf("common_oh")];
			$summary_data[$row[csf('job_no')]]['common_oh_total_value']+=($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty;
			//echo $netfob_value_dzn.'='.$row[csf("cm_cost")];
			$Contribution_Margin=$netfob_value_dzn-$LessCostOfMaterialServices;
			$tot_gross_profit_dzn=$contribute_netfob_value_dzn-$row[csf("cm_cost")];
			$summary_data[$row[csf('job_no')]]['gross_profit_dzn']+=$tot_gross_profit_dzn;
			$summary_data[$row[csf('job_no')]]['gross_profit_total_value']+=(($tot_gross_profit_dzn)/$order_price_per_dzn)*$order_job_qnty;

			//$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
			$operate_profit_loss_dzn=$tot_gross_profit_dzn;//-($row[csf("comm_cost")]+$row[csf("common_oh")]);
			$summary_data[$row[csf('job_no')]]['operating_profit_loss_dzn']+=$operate_profit_loss_dzn;
			$summary_data[$row[csf('job_no')]]['operating_profit_loss_total_value']+=($operate_profit_loss_dzn/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_dzn']+=$row[csf("depr_amor_pre_cost")];
			$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_total_value']+=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['interest_pre_cost_dzn']+=$row[csf("interest_pre_cost")];
			$summary_data[$row[csf('job_no')]]['interest_pre_cost_total_value']+=($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_dzn']+=$row[csf("income_tax_pre_cost")];
			$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_total_value']+=($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$net_profit_dzn=$operate_profit_loss_dzn-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);
			$summary_data[$row[csf('job_no')]]['net_profit_dzn']+=$net_profit_dzn;
			$summary_data[$row[csf('job_no')]]['net_profit_dzn_total_value']+=($net_profit_dzn/$order_price_per_dzn)*$order_job_qnty;
		}
		// echo "<pre>";
		// print_r($summary_data);
		// die();
		//======================================================================

		$sql_commi = "SELECT a.id,a.quotation_id,a.particulars_id,a.commission_base_id,a.commision_rate,a.commission_amount,a.status_active,b.job_no
		from  wo_pri_quo_commiss_cost_dtls a,wo_po_details_master b
		where  a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and a.commission_amount>0 and b.status_active=1";
		// echo $sql_commi;die();
		$result_commi=sql_select($sql_commi);
		$CommiData_foreign_cost=$CommiData_lc_cost=$foreign_dzn_commission_amount=$local_dzn_commission_amount=0;
		foreach($result_commi as $row)
		{
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];

			if($row[csf("particulars_id")]==1) //Foreign
			{
				$CommiData_foreign_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
				$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
				$CommiData_foreign_quot_cost_arr[$row[csf("job_no")]]+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
			else
			{
				$CommiData_lc_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
				$local_dzn_commission_amount+=$row[csf("commission_amount")];
				$commision_local_quot_cost_arr[$row[csf("job_no")]]=$row[csf("commision_rate")];
			}
		}
		//=====================================================================================
		$sql_comm="SELECT a.item_id,a.quotation_id,sum(a.amount) as amount,b.job_no from wo_pri_quo_comarcial_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by a.quotation_id,a.item_id,b.job_no";
		// echo $sql_comm;die();
		$tot_lc_dzn_Commer=$tot_without_lc_dzn_Commer=0;
		// $summary_data['comm_cost_dzn']=0;
		// $summary_data['comm_cost_total_value']=0;
		$result_comm=sql_select($sql_comm);
		$commer_lc_cost = array();
		$commer_without_lc_cost = array();
		foreach($result_comm as $row)
		{
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
			//$summary_data['comm_cost_dzn']=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			$comm_amtPri=$row[csf('amount')];
			$item_id=$row[csf('item_id')];
			if($item_id==1)//LC
			{
				$commer_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;

				$commer_lc_cost_quot_arr[$row[csf("job_no")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
			else
			{
				$commer_without_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
		}
		// echo "<pre>";print_r($summary_data);die();
		/********************************************************************************************************
		*																										*
		*													END													*	
		*																										*
		********************************************************************************************************/

		// =========================================== FOR ROWSPAN ===============================================
		$buyer_wise_cm_fob_calculate = array();
		$rowspan_arr = array();
		$chk_buyer  = array();
		foreach ($production_main_array as $style_key => $style_arr) 
		{
			foreach ($style_arr as $job_key => $job_arr) 
			{
				foreach ($job_arr as $po_key => $po_arr) 
				{
					foreach ($po_arr as $item_key => $item_arr) 
					{
						foreach ($item_arr as $color_key => $row) 
						{
							$rowspan_arr[$style_key][$job_key][$po_key][$item_key]++;

							$buyer_booking_no_fin_qnty_array[$row['buyer_name']]+=$booking_no_fin_qnty_array[$po_key][$item_key][$color_key];
							$buyer_issue_qnty_arr[$row['buyer_name']][18] += $issue_qnty_arr[$po_key][$color_key][18];
							$buyer_issue_qnty_arr[$row['buyer_name']][37] += $issue_qnty_arr[$po_key][$color_key][37];

							// calculate summary cm and fob
							$today_sewing_output_qty = $cutting_sewing_data[$job_key][$style_key][$po_key][$item_key][$color_key]["today_sewing_output"];
							$total_sewing_output_qty = $cutting_sewing_data[$job_key][$style_key][$po_key][$item_key][$color_key]["total_sewing_output"];
							$today_ex_fac_qty=$ex_factory_arr[$po_key][$item_key][$color_key]['today_ex_fac'];
							$total_ex_fac_qty=$ex_factory_arr[$po_key][$item_key][$color_key]['total_ex_fac'];

							$tot_bat_qty = $total_sewing_output_qty - $total_ex_fac_qty;
							$buyer_wise_ex_factory_arr[$row['buyer_name']]['total_ex_fac']+=$total_ex_fac_qty;
							$buyer_wise_ex_factory_arr[$row['buyer_name']]['total_bal_qty']+= $tot_bat_qty;
							$buyer_wise_ex_factory_arr[$row['buyer_name']]['total_fob_qty']+= ($tot_bat_qty*$row['unit_price']);

							$unit_price = $row['unit_price'];
							$today_fob_val = $today_sewing_output_qty * $unit_price;
							$total_fob_val = $total_sewing_output_qty * $unit_price;
							$today_exf_fob_val = $today_ex_fac_qty * $unit_price;
							$total_exf_fob_val = $total_ex_fac_qty * $unit_price;

							$costing_per=$costing_per_arr[$job_key];
							if($costing_per==1) $dzn_qnty=12;
							else if($costing_per==3) $dzn_qnty=12*2;
							else if($costing_per==4) $dzn_qnty=12*3;
							else if($costing_per==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;	
							$dzn_qnty=$dzn_qnty*$row['total_set_qnty'];
							$cm_gmt_cost_dzn=$cm_gmt_cost_dzn_arr_new[$po_key]['dzn'];
							//echo $cm_gmt_cost_dzn.'DD'.$po_id.', ';
							//$cm_per_pcs=(($unit_price*$dzn_qnty)-$total_cost_arr[$job_id])+$cm_cost_arr[$job_id];
							$cm_per_pcs=$cm_gmt_cost_dzn/$dzn_qnty;
							$today_cm_val = $today_sewing_output_qty * $cm_per_pcs;
							$total_cm_val = $total_sewing_output_qty * $cm_per_pcs;

							$today_exf_cm_val = $today_ex_fac_qty * $cm_per_pcs;
							$total_exf_cm_val = $total_ex_fac_qty * $cm_per_pcs;
							/*========================================================================================
							*																						  *
							*								Calculate cm valu 										  *	
							*																					  	  *	
							*========================================================================================*/
							$tot_dye_chemi_process_amount 	= $conversion_cost_arr[$job_key][101]['conv_amount_total_value'];
							$tot_yarn_dye_process_amount 	= $conversion_cost_arr[$job_key][30]['conv_amount_total_value'];
							$tot_aop_process_amount 		= $conversion_cost_arr[$job_key][35]['conv_amount_total_value'];
							
							$total_quot_qty=$total_quot_pcs_qty=$total_quot_amount=$total_sew_smv=$total_quot_amount_cal=0;
							$all_last_shipdates='';

				            foreach($style_wise_arr as $style_key=>$val)
							{	
								$total_cost=$val[('qty')]*$val[('final_cost_pcs')];
								$total_quot_qty+=$val[('qty')];
								$total_quot_pcs_qty+=$val[('qty_pcs')];
								$total_sew_smv+=$val[('sew_smv')];
								$total_quot_amount+=$total_cost;
								$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
							}
							$total_quot_amount_cal = $style_wise_arr[$job_key]['qty']*$style_wise_arr[$job_key]['final_cost_pcs'];
							$tot_cm_for_fab_cost=$summary_data[$job_key]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
							// echo $job_key."==".$summary_data[$job_key]['conversion_cost_total_value']."-(".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$tot_aop_process_amount.")<br>";
							$commision_quot_local=$commision_local_quot_cost_arr[$job_key];
							$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$job_key]+$commer_lc_cost_quot_arr[$job_key]+$freight_cost_data[$job_key]['freight_total_value']);
							$tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
							$tot_inspect_cour_certi_cost=$summary_data[$job_key]['inspection_total_value']+$summary_data[$job_key]['currier_pre_cost_total_value']+$summary_data[$job_key]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$job_key]['design_pre_cost_total_value'];
							// echo $summary_data[$job_key]['inspection_total_value']."+".$summary_data[$job_key]['currier_pre_cost_total_value']."+".$summary_data[$job_key]['certificate_pre_cost_total_value']."+".$tot_sum_amount_quot_calccc."+".$summary_data[$job_key]['design_pre_cost_total_value']."<br>";

							$tot_emblish_cost=$summary_data[$job_key]['embel_cost_total_value'];
							$pri_freight_cost_per=$summary_data[$job_key]['freight_total_value'];
							$pri_commercial_per=$commer_lc_cost[$job_key];
							$CommiData_foreign_cost=$CommiData_foreign_quot_cost_arr[$job_key];

							$total_btb=$summary_data[$job_key]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$job_key]['comm_cost_total_value']+$summary_data[$job_key]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$job_key]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$job_key]['common_oh_total_value']+$summary_data[$job_key]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
							// echo $summary_data[$job_key]['lab_test_total_value']."+".$tot_emblish_cost."+".$summary_data[$job_key]['comm_cost_total_value']."+".$summary_data[$job_key]['trims_cost_total_value']."+".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$summary_data[$job_key]['yarn_amount_total_value']."+".$tot_aop_process_amount."+".$summary_data[$job_key]['common_oh_total_value']."+".$summary_data[$job_key]['studio_pre_cost_total_value']."+".$tot_inspect_cour_certi_cost."<br>";
							$tot_quot_sum_amount=$total_quot_amount-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
							// echo $total_quot_amount."-(".$CommiData_foreign_cost."+".$pri_freight_cost_per."+".$pri_commercial_per.")<br>";
							$NetFOBValue_job = $tot_quot_sum_amount;
							// echo $NetFOBValue_job."<br>";
							$total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);

							$total_quot_pcs_qty = $quotation_qty_array[$job_key]['QTY_PCS'];
							// echo $total_cm_for_gmt;echo "<br>";
							$cm_valu_lc = 0;
							$cm_valu_lc = $total_cm_for_gmt/$total_quot_pcs_qty;
							$today_cm_val_lc = $today_sewing_output_qty * $cm_valu_lc;
							$total_cm_val_lc = $total_sewing_output_qty * $cm_valu_lc;

							$today_exf_cm_val_lc = $today_ex_fac_qty * $cm_valu_lc;
							$total_exf_cm_val_lc = $total_ex_fac_qty * $cm_valu_lc;
							// echo number_format(($total_cm_for_gmt/$total_quot_pcs_qty)*12,2); echo "<br>";
							/*========================================================================================
							*																						  *
							*											END											  *	
							*																					  	  *	
							*========================================================================================*/			

							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_cm_val'] 		+= $today_cm_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_cm_val'] 		+= $total_cm_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_cm_val_lc']		+= $today_cm_val_lc;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_cm_val_lc'] 	+= $total_cm_val_lc;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_fob_val'] 		+= $today_fob_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_fob_val'] 		+= $total_fob_val;

							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_exf_cm_val'] 	+= $today_exf_cm_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_exf_cm_val'] 	+= $total_exf_cm_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_exf_cm_val_lc']	+= $today_exf_cm_val_lc;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_exf_cm_val_lc']	+= $total_exf_cm_val_lc;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_exf_fob_val'] 	+= $today_exf_fob_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_exf_fob_val'] 	+= $total_exf_fob_val;
						}
					}
				}
			}
		}
		// echo "<pre>";
		// print_r($buyer_wise_all_info);
		// echo "</pre>";
		
		
		ob_start();	
		$summary_html = '';
					 
        $summary_html.='<div style="padding: 5px 10px;">
        <div id="summary_part">
        	<table width="3300" cellspacing="0" >
        		<tr style="border:none;">
        			<td colspan="24" align="center" style="border:none; font-size:24px;">
        				<strong>';?><? $comp_names=""; 
        					foreach(explode(",",$working_company_id) as $vals) 
        					{
        						$comp_names.=($comp_names !="") ? ' , '.$company_library[$vals] : $company_library[$vals];
        					}
        					$summary_html.=$comp_names.'
        					
        				</strong>                                
        			</td>
        		</tr>
        		<tr class="form_caption" style="border:none;">
        			<td colspan="24" align="center" style="border:none;font-size:14px; font-weight:bold" ><strong>
        				Floor wise Daily RMG Production Report
        			</strong></td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="24" align="center" style="border:none;font-size:14px; font-weight:bold" >'?>
        				<?
        				$dates=str_replace("'","",trim($txt_production_date));
        				if($dates)
        				{
        					$summary_html.='Date '.change_date_format($dates)  ;
        				}?>
        				
        			<? $summary_html.='</td>
        		</tr>
        	</table>';?>
        	<!-- =========================================== SUMMARY PART START ====================================== -->
        	<? $summary_html.='<div style="margin-bottom: 20px;">
        		<table width="2680" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="" align="left">
        			<caption style="text-align: center;font-weight: bold;font-size: 18px;">Summary Part</caption>
					<thead>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30" ><p>SL</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Buyer</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="70"><p>Order Qty</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Fabric Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Cut Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Cutting QC</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Delivery To Print</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Receive from Print</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Input Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Sewing Output</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Finishing</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Ex-Factory</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Export FOB Value</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Sewing CM Value BoM</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Sewing FOB</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="110"><p>Sewing - Exfactory Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="110"><p>Sewing - Exfactory FOB Value</p></th>
						</tr>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>F.Fab. Req.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab. Rcv.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab. Issue</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab.Inhand</p></th>
							
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Cut %</p></th>
							
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Cutting %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Print WIP</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60" title="(Total Sewing Input / Total Cut Status)*100"><p>Input %</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Sewing %</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Finish %</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="110"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="110"><p>Total</p></th>
						</tr>
						<tbody>';?>
						<?
						$sl=1;
						$buyer_total_order_qty 			= 0;
						$buyer_total_fin_fab_req 		= 0;
						$buyer_total_fab_rcv 			= 0;
						$buyer_total_fav_issue 			= 0;
						$buyer_total_fab_inhand 		= 0;
						$buyer_total_today_cut 			= 0;
						$buyer_total_cut 				= 0;
						$buyer_total_today_cutting 		= 0;
						$buyer_total_cutting 			= 0;
						$buyer_total_today_print_issue 	= 0;
						$buyer_total_print_issue 		= 0;
						$buyer_total_today_print_rcv 	= 0;
						$buyer_total_print_rcv 			= 0;
						$buyer_total_print_rej 			= 0;
						$buyer_total_today_input 		= 0;
						$buyer_total_input 				= 0;
						$buyer_total_today_output 		= 0;
						$buyer_total_output 			= 0;
						$buyer_total_today_fin 			= 0;
						$buyer_total_fin 				= 0;
						$buyer_total_today_carton 		= 0;
						$buyer_total_carton 			= 0;
						$buyer_total_today_exfact 		= 0;
						$buyer_total_exfact 			= 0;
						$buyer_total_today_export_cm 	= 0;
						$buyer_total_export_cm 			= 0;
						$buyer_total_today_export_cm_lc	= 0;
						$buyer_total_export_cm_lc		= 0;
						$buyer_total_today_export_fob 	= 0;
						$buyer_total_export_fob 		= 0;
						$buyer_total_today_sewing_cm 	= 0;
						$buyer_total_sewing_cm 			= 0;
						$buyer_total_today_sewing_cm_lc	= 0;
						$buyer_total_sewing_cm_lc		= 0;
						$buyer_total_today_sewing_fob 	= 0;
						$buyer_total_sewing_fob 		= 0;
						$buyer_total_sewing_ex_bal_qty	= 0;
						$buyer_total_sewing_ex_fob_qty	= 0;
						foreach ($buyer_wise_summary_array as $key => $val) 
						{
							$smry_order_quantity 		= $buyer_wise_order_qnty_array[$key];
							$smry_fab_required			= $buyer_booking_no_fin_qnty_array[$key];
							$smry_issue_qty 			= $buyer_issue_qnty_arr[$key][18];
							$smry_receive_qty 			= $buyer_issue_qnty_arr[$key][37];
							$smry_fab_in_hand 			= $smry_receive_qty-$smry_issue_qty;

							$smry_today_cut 			= $buyer_wise_lay_qnty_array[$key]['today_lay'];
							$smry_totalCut 				= $buyer_wise_lay_qnty_array[$key]['total_lay'];
							$smry_cutting_lay_percent 	= ($smry_totalCut / $smry_order_quantity)*100;

							$smry_today_qc 				= $buyer_wise_qc_qnty_array[$key]['today_qc'];
							$smry_totalQc 				= $buyer_wise_qc_qnty_array[$key]['total_qc'];
							$smry_totalRej 				= $buyer_wise_qc_qnty_array[$key]['total_rej'];
							$smry_cutting_qc_percent 	= ($smry_totalCut) ? ($smry_totalQc / $smry_totalCut)*100 : 0;

							$smry_today_cutting 		= $buyer_wise_cutting_sewing_data[$key]["today_cutting"];	
							$smry_total_cutting 		= $buyer_wise_cutting_sewing_data[$key]["total_cutting"];	
							$smry_today_issue_to_print 	= $buyer_wise_cutting_sewing_data[$key]["today_issue_to_print"];	
							$smry_total_issue_to_print 	= $buyer_wise_cutting_sewing_data[$key]["total_issue_to_print"];	
							$smry_today_rcv_frm_print 	= $buyer_wise_cutting_sewing_data[$key]["today_rcv_frm_print"];
							$smry_total_rcv_frm_print 	= $buyer_wise_cutting_sewing_data[$key]["total_rcv_frm_print"];	
							$smry_print_reject 			= $buyer_wise_cutting_sewing_data[$key]["print_reject"];
							$smry_print_wip 			= $smry_total_issue_to_print - $smry_total_rcv_frm_print;	 	
							$smry_today_sewing_input 	= $buyer_wise_cutting_sewing_data[$key]["today_sewing_input"];
							$smry_total_sewing_input 	= $buyer_wise_cutting_sewing_data[$key]["total_sewing_input"];
							$smry_input_percent 		= ($smry_totalCut) ? ($smry_total_sewing_input / $smry_totalCut)*100 : 0;	
							$smry_today_sewing_output 	= $buyer_wise_cutting_sewing_data[$key]["today_sewing_output"];
							$smry_total_sewing_output 	= $buyer_wise_cutting_sewing_data[$key]["total_sewing_output"];	
							$smry_output_percent 		= ($smry_total_sewing_input) ? ($smry_total_sewing_output / $smry_total_sewing_input)*100 : 0;
							$smry_today_finishing 		= $buyer_wise_cutting_sewing_data[$key]["today_finishing"];
							$smry_total_finishing 		= $buyer_wise_cutting_sewing_data[$key]["total_finishing"];
							$smry_finishing_percent 	= ($smry_total_sewing_output) ? ($smry_total_finishing / $smry_total_sewing_output)*100 : 0;
							$smry_today_carton_qty 		= $buyer_wise_cutting_sewing_data[$key]["today_carton_qty"];
							$smry_total_carton_qty 		= $buyer_wise_cutting_sewing_data[$key]["total_carton_qty"];

							$smry_today_ex_fact_qty 	= $buyer_wise_ex_factory_arr[$key]["today_ex_fac"];
							$smry_total_ex_fac_qty 		= $buyer_wise_ex_factory_arr[$key]["total_ex_fac"];

							$smry_total_ex_fac_bal_qty 	= $buyer_wise_ex_factory_arr[$key]["total_bal_qty"];
							$smry_total_ex_fac_fob_qty 	= $buyer_wise_ex_factory_arr[$key]["total_fob_qty"];

							// $smry_total_ex_fac_bal_qty 	= $smry_total_sewing_output - $smry_total_ex_fac_qty;
							// $smry_total_ex_fac_fob_qty 	= $smry_total_ex_fac_bal_qty * $val['unit_price'];

							$smry_today_fob_val 		= $buyer_wise_cm_fob_calculate[$key]['today_fob_val'];
							$smry_total_fob_val 		= $buyer_wise_cm_fob_calculate[$key]['total_fob_val'];
							$smry_today_cm_val 			= $buyer_wise_cm_fob_calculate[$key]['today_cm_val'];
							$smry_total_cm_val 			= $buyer_wise_cm_fob_calculate[$key]['total_cm_val'];
							$smry_today_cm_val_lc		= $buyer_wise_cm_fob_calculate[$key]['today_cm_val_lc'];
							$smry_total_cm_val_lc		= $buyer_wise_cm_fob_calculate[$key]['total_cm_val_lc'];


							$smry_today_exf_cm_val 		= $buyer_wise_cm_fob_calculate[$key]['today_exf_cm_val'];
							$smry_total_exf_cm_val 		= $buyer_wise_cm_fob_calculate[$key]['total_exf_cm_val'];
							$smry_today_exf_cm_val_lc	= $buyer_wise_cm_fob_calculate[$key]['today_exf_cm_val_lc'];
							$smry_total_exf_cm_val_lc	= $buyer_wise_cm_fob_calculate[$key]['total_exf_cm_val_lc'];
							$smry_today_exf_fob_val 	= $buyer_wise_cm_fob_calculate[$key]['today_exf_fob_val'];
							$smry_total_exf_fob_val 	= $buyer_wise_cm_fob_calculate[$key]['total_exf_fob_val'];

							// =========================================
							$buyer_total_order_qty 			+= $smry_order_quantity;
							$buyer_total_fin_fab_req 		+= $smry_fab_required;
							$buyer_total_fab_rcv 			+= $smry_receive_qty;
							$buyer_total_fav_issue 			+= $smry_issue_qty;
							$buyer_total_fab_inhand 		+= $smry_fab_in_hand;
							$buyer_total_today_cut 			+= $smry_today_cut;
							$buyer_total_cut 				+= $smry_totalCut;
							$buyer_total_cut_prsnt 			+= $smry_cutting_lay_percent;
							$buyer_total_today_cutting 		+= $smry_today_qc;
							$buyer_total_cutting 			+= $smry_totalQc;
							$buyer_cutting_percent 			+= $smry_cutting_qc_percent;
							$buyer_cutting_reject 			+= $smry_totalRej;
							$buyer_total_today_print_issue 	+= $smry_today_issue_to_print;
							$buyer_total_print_issue 		+= $smry_total_issue_to_print;
							$buyer_total_today_print_rcv 	+= $smry_today_rcv_frm_print;
							$buyer_total_print_rcv 			+= $smry_total_rcv_frm_print;
							$buyer_total_print_rej 			+= $smry_print_reject;
							$buyer_total_print_wip 			+= $smry_print_wip;
							$buyer_total_today_input 		+= $smry_today_sewing_input;
							$buyer_total_input 				+= $smry_total_sewing_input;
							$buyer_input_prsent				+= $smry_input_percent;
							$buyer_total_today_output 		+= $smry_today_sewing_output;
							$buyer_total_output 			+= $smry_total_sewing_output;
							$buyer_output_percent 			+= $smry_output_percent;
							$buyer_total_today_fin 			+= $smry_today_finishing;
							$buyer_total_fin 				+= $smry_total_finishing;
							$buyer_finishing_percent 		+= $smry_finishing_percent;
							$buyer_total_today_carton 		+= $smry_today_carton_qty;
							$buyer_total_carton 			+= $smry_total_carton_qty;
							$buyer_total_today_exfact 		+= $smry_today_ex_fact_qty;
							$buyer_total_exfact 			+= $smry_total_ex_fac_qty;
							$buyer_total_today_export_cm 	+= $smry_today_exf_cm_val;
							$buyer_total_export_cm 			+= $smry_total_exf_cm_val;
							$buyer_total_today_export_cm_lc	+= $smry_today_exf_cm_val_lc;
							$buyer_total_export_cm_lc		+= $smry_total_exf_cm_val_lc;
							$buyer_total_today_export_fob 	+= $smry_today_exf_fob_val;
							$buyer_total_export_fob 		+= $smry_total_exf_fob_val;
							$buyer_total_today_sewing_cm 	+= $smry_today_cm_val;
							$buyer_total_sewing_cm 			+= $smry_total_cm_val;
							$buyer_total_today_sewing_cm_lc	+= $smry_today_cm_val_lc;
							$buyer_total_sewing_cm_lc		+= $smry_total_cm_val_lc;
							$buyer_total_today_sewing_fob 	+= $smry_today_fob_val;
							$buyer_total_sewing_fob 		+= $smry_total_fob_val;
							$buyer_total_sewing_ex_bal_qty	+= $smry_total_ex_fac_bal_qty;
							$buyer_total_sewing_ex_fob_qty	+= $smry_total_ex_fac_fob_qty;
							
							$summary_html.='<tr>
								<td align="left" style="word-wrap: break-word;word-break: break-all;">'.$sl.'</td>
								<td align="left" style="word-wrap: break-word;word-break: break-all;">'.$buyer_library[$key].'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_order_quantity,0).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_fab_required,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_receive_qty,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_issue_qty,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_fab_in_hand,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_cut.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_totalCut.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_cutting_lay_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_qc.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_totalQc.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_cutting_qc_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_totalRej.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_issue_to_print.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_issue_to_print.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_rcv_frm_print.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_rcv_frm_print.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_print_reject.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_print_wip.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_sewing_input.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_sewing_input.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_input_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_sewing_output.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_sewing_output.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_output_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_finishing.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_finishing.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_finishing_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_ex_fact_qty.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_ex_fac_qty.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_today_exf_fob_val,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_total_exf_fob_val,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_today_cm_val,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_total_cm_val,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_today_fob_val,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_total_fob_val,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_total_ex_fac_bal_qty,0).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_total_ex_fac_fob_qty,2).'</td>
							</tr>';?>
							<?
							$sl++;
						}
						?>
					<? $summary_html.='</tbody>        			
					<tfoot>
						<tr>
							<th align="right" colspan="2">Total </th>
							<th align="right">'.number_format($buyer_total_order_qty,0).'</th>
							<th align="right">'.number_format($buyer_total_fin_fab_req,2).'</th>
							<th align="right">'.number_format($buyer_total_fab_rcv,2).'</th>
							<th align="right">'.number_format($buyer_total_fav_issue,2).'</th>
							<th align="right">'.number_format($buyer_total_fab_inhand,2).'</th>
							<th align="right">'.number_format($buyer_total_today_cut,0).'</th>
							<th align="right">'.number_format($buyer_total_cut,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_total_today_cutting,0).'</th>
							<th align="right">'.number_format($buyer_total_cutting,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_cutting_reject,0).'</th>
							<th align="right">'.number_format($buyer_total_today_print_issue,0).'</th>
							<th align="right">'.number_format($buyer_total_print_issue,0).'</th>
							<th align="right">'.number_format($buyer_total_today_print_rcv,0).'</th>
							<th align="right">'.number_format($buyer_total_print_rcv,0).'</th>
							<th align="right">'.number_format($buyer_total_print_rej,0).'</th>
							<th align="right">'.number_format($buyer_total_print_wip,0).'</th>
							<th align="right">'.number_format($buyer_total_today_input,0).'</th>
							<th align="right">'.number_format($buyer_total_input,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_total_today_output,0).'</th>
							<th align="right">'.number_format($buyer_total_output,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_total_today_fin,0).'</th>
							<th align="right">'.number_format($buyer_total_fin,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_total_today_exfact,0).'</th>
							<th align="right">'.number_format($buyer_total_exfact,0).'</th>
							<th align="right">'.number_format($buyer_total_today_export_fob,2).'</th>
							<th align="right">'.number_format($buyer_total_export_fob,2).'</th>
							<th align="right">'.number_format($buyer_total_today_sewing_cm,2).'</th>
							<th align="right">'.number_format($buyer_total_sewing_cm,2).'</th>
							<th align="right">'.number_format($buyer_total_today_sewing_fob,2).'</th>
							<th align="right">'.number_format($buyer_total_sewing_fob,2).'</th>
							<th align="right">'.number_format($buyer_total_sewing_ex_bal_qty,0).'</th>
							<th align="right">'.number_format($buyer_total_sewing_ex_fob_qty,2).'</th>
						</tr>
					</tfoot>
        		</table>
        	</div></div>';?>
        	<? echo $summary_html; ?>
        	<br clear="all">
        	<!-- ===================================== DETAILS PART START ===================================== -->
			<div>
				<table width="3500" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left">
					<caption style="text-align: center;font-weight: bold;font-size: 18px;">Details Part</caption>
					<thead>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30" ><p>SL</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Buyer</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Order No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Shipping Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30"><p>Img</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Style</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Internal Ref</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Floor Group</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Color</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="70"><p>Color Qty</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Fabric Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Cut Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Cutting QC</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Delivery To Print</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Receive from Print</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Input Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Sewing Output</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Finishing</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Ex-Factory</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Export FOB Value</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Sewing CM Value BoM</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Sewing FOB</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="105"><p>Sewing - Exfactory Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="105"><p>Sewing - Exfactory FOB Value</p></th>
						</tr>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>F.Fab. Req.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab. Rcv.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab. Issue</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab.Inhand</p></th>
							
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Cut %</p></th>
							
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Cutting %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Print WIP</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60" title="(Total Sewing Input / Total Cut Status)*100"><p>Input %</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Sewing %</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Finish %</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="105"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="105"><p>Total</p></th>
						</tr>					   
					</thead>
				</table>
				<div style="max-height:400px; overflow-y:scroll; width:3520px" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3500" rules="all" id="table_body" align="left">
					<?
					$k=1;
					$jj=1;	
					$gr_color_qty 			= 0;
					$gr_fab_req_qty 		= 0;
					$gr_fab_rcv_qty 		= 0;
					$gr_fab_iss_qty 		= 0;
					$gr_fab_inh_qty 		= 0;
					$gr_today_lay_qty 		= 0;
					$gr_total_lay_qty 		= 0;
					$gr_cutting_percent 	= 0;
					$gr_today_qc_qty 		= 0;
					$gr_total_qc_qty 		= 0;
					$gr_cutting_qc_percent 	= 0;
					$gr_cutting_qc_reject 	= 0;
					$gr_today_print_iss_qty = 0;
					$gr_total_print_iss_qty = 0;
					$gr_today_print_rcv_qty = 0;
					$gr_total_print_rcv_qty = 0;
					$gr_print_rej_qty 		= 0;
					$gr_print_wip_qty 		= 0;
					$gr_today_sew_in_qty 	= 0;
					$gr_total_sew_in_qty 	= 0;
					$gr_input_percent 		= 0;
					$gr_today_sew_out_qty 	= 0;
					$gr_total_sew_out_qty 	= 0;
					$gr_sewing_percent 		= 0;
					$gr_today_fin_qty 		= 0;
					$gr_total_fin_qty 		= 0;
					$gr_finish_percent 		= 0;
					$gr_today_ctn_qty 		= 0;
					$gr_total_ctn_qty 		= 0;
					$gr_today_exfact_qty 	= 0;
					$gr_total_exfact_qty 	= 0;
					$gr_today_exf_cm_qty 	= 0;
					$gr_total_exf_cm_qty 	= 0;
					$gr_today_exf_fob_qty 	= 0;
					$gr_total_exf_fob_qty 	= 0;
					$gr_today_sew_cm_qty 	= 0;
					$gr_total_sew_cm_qty 	= 0;
					$gr_today_fob_qty 		= 0;
					$gr_total_fob_qty 		= 0;
					$gr_total_ex_bal_qty 	= 0;
					$gr_total_ex_fob_qty 	= 0;
					
					foreach($production_main_array as $style_id=>$job_data)
					{				
						$style_wise_color_qty 			= 0;
						$style_wise_fab_req_qty 		= 0;
						$style_wise_fab_rcv_qty 		= 0;
						$style_wise_fab_iss_qty 		= 0;
						$style_wise_fab_inh_qty 		= 0;
						$style_wise_today_lay_qty 		= 0;
						$style_wise_total_lay_qty 		= 0;
						$style_wise_cutting_percent 	= 0;
						$style_wise_today_qc_qty 		= 0;
						$style_wise_total_qc_qty 		= 0;
						$style_wise_cutting_qc_percent 	= 0;
						$style_wise_cutting_qc_reject 	= 0;
						$style_wise_today_print_iss_qty = 0;
						$style_wise_total_print_iss_qty = 0;
						$style_wise_today_print_rcv_qty = 0;
						$style_wise_total_print_rcv_qty = 0;
						$style_wise_print_rej_qty 		= 0;
						$style_wise_print_wip_qty 		= 0;
						$style_wise_today_sew_in_qty 	= 0;
						$style_wise_total_sew_in_qty 	= 0;
						$style_wise_input_percent 		= 0;
						$style_wise_today_sew_out_qty 	= 0;
						$style_wise_total_sew_out_qty 	= 0;
						$style_wise_sewing_percent 		= 0;
						$style_wise_today_fin_qty 		= 0;
						$style_wise_total_fin_qty 		= 0;
						$style_wise_finish_percent 		= 0;
						$style_wise_today_ctn_qty 		= 0;
						$style_wise_total_ctn_qty 		= 0;
						$style_wise_today_exfact_qty 	= 0;
						$style_wise_total_exfact_qty 	= 0;
						$style_wise_today_exf_cm_qty 	= 0;
						$style_wise_total_exf_cm_qty 	= 0;
						$style_wise_today_exf_fob_qty 	= 0;
						$style_wise_total_exf_fob_qty 	= 0;
						$style_wise_today_sew_cm_qty 	= 0;
						$style_wise_total_sew_cm_qty 	= 0;
						$style_wise_today_fob_qty 		= 0;
						$style_wise_total_fob_qty 		= 0;
						$style_wise_total_ex_bal_qty 	= 0;
						$style_wise_total_ex_fob_qty 	= 0;

						foreach($job_data as $job_id=>$po_data)
						{
							
							foreach($po_data as $po_id=>$item_data)
							{
								$po_wise_color_qty 			= 0;
								$po_wise_fab_req_qty 		= 0;
								$po_wise_fab_rcv_qty 		= 0;
								$po_wise_fab_iss_qty 		= 0;
								$po_wise_fab_inh_qty 		= 0;
								$po_wise_today_lay_qty 		= 0;
								$po_wise_total_lay_qty 		= 0;
								$po_wise_cutting_percent 	= 0;
								$po_wise_today_qc_qty 		= 0;
								$po_wise_total_qc_qty 		= 0;
								$po_wise_cutting_qc_percent	= 0;
								$po_wise_cutting_qc_reject	= 0;
								$po_wise_today_print_iss_qty= 0;
								$po_wise_total_print_iss_qty= 0;
								$po_wise_today_print_rcv_qty= 0;
								$po_wise_total_print_rcv_qty= 0;
								$po_wise_print_rej_qty 		= 0;
								$po_wise_print_wip_qty 		= 0;
								$po_wise_today_sew_in_qty 	= 0;
								$po_wise_total_sew_in_qty 	= 0;
								$po_wise_input_percent 		= 0;
								$po_wise_today_sew_out_qty 	= 0;
								$po_wise_total_sew_out_qty 	= 0;
								$po_wise_sewing_percent 	= 0;
								$po_wise_today_fin_qty 		= 0;
								$po_wise_total_fin_qty 		= 0;
								$po_wise_finish_percent 	= 0;
								$po_wise_today_ctn_qty 		= 0;
								$po_wise_total_ctn_qty 		= 0;
								$po_wise_today_exfact_qty 	= 0;
								$po_wise_total_exfact_qty 	= 0;
								$po_wise_today_exf_cm_qty 	= 0;
								$po_wise_total_exf_cm_qty 	= 0;
								$po_wise_today_exf_fob_qty 	= 0;
								$po_wise_total_exf_fob_qty 	= 0;
								$po_wise_today_sew_cm_qty 	= 0;
								$po_wise_total_sew_cm_qty 	= 0;
								$po_wise_today_fob_qty 		= 0;
								$po_wise_total_fob_qty 		= 0;								
								$po_wise_total_ex_bal_qty 	= 0;								
								$po_wise_total_ex_fob_qty 	= 0;								

								foreach($item_data as $item_id=>$color_data)
								{ 
									$r=0;
									foreach($color_data as $color_id=>$row)
									{
										/*========================================================================================
										*																						  *
										*								Calculate cm valu 										  *	
										*																					  	  *	
										*========================================================================================*/
										$tot_dye_chemi_process_amount 	= $conversion_cost_arr[$job_id][101]['conv_amount_total_value'];
										$tot_yarn_dye_process_amount 	= $conversion_cost_arr[$job_id][30]['conv_amount_total_value'];
										$tot_aop_process_amount 		= $conversion_cost_arr[$job_id][35]['conv_amount_total_value'];
										
										$total_quot_qty=$total_quot_pcs_qty=$total_quot_amount=$total_sew_smv=$total_quot_amount_cal=0;
										$all_last_shipdates='';

							            foreach($style_wise_arr as $style_key=>$val)
										{	
											$total_cost=$val[('qty')]*$val[('final_cost_pcs')];
											$total_quot_qty+=$val[('qty')];
											$total_quot_pcs_qty+=$val[('qty_pcs')];
											$total_sew_smv+=$val[('sew_smv')];
											$total_quot_amount+=$total_cost;
											$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
										}
										$total_quot_amount_cal = $style_wise_arr[$job_id]['qty']*$style_wise_arr[$job_id]['final_cost_pcs'];
										$tot_cm_for_fab_cost=$summary_data[$job_id]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
										// echo $job_id."==".$summary_data[$job_id]['conversion_cost_total_value']."-(".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$tot_aop_process_amount.")<br>";
										$commision_quot_local=$commision_local_quot_cost_arr[$job_id];
										$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$job_id]+$commer_lc_cost_quot_arr[$job_id]+$freight_cost_data[$job_id]['freight_total_value']);
										$tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
										$tot_inspect_cour_certi_cost=$summary_data[$job_id]['inspection_total_value']+$summary_data[$job_id]['currier_pre_cost_total_value']+$summary_data[$job_id]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$job_id]['design_pre_cost_total_value'];
										// echo $summary_data[$job_id]['inspection_total_value']."+".$summary_data[$job_id]['currier_pre_cost_total_value']."+".$summary_data[$job_id]['certificate_pre_cost_total_value']."+".$tot_sum_amount_quot_calccc."+".$summary_data[$job_id]['design_pre_cost_total_value']."<br>";

										$tot_emblish_cost=$summary_data[$job_id]['embel_cost_total_value'];
										$pri_freight_cost_per=$summary_data[$job_id]['freight_total_value'];
										$pri_commercial_per=$commer_lc_cost[$job_id];
										$CommiData_foreign_cost=$CommiData_foreign_quot_cost_arr[$job_id];

										$total_btb=$summary_data[$job_id]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$job_id]['comm_cost_total_value']+$summary_data[$job_id]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$job_id]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$job_id]['common_oh_total_value']+$summary_data[$job_id]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
										// echo $summary_data[$job_id]['lab_test_total_value']."+".$tot_emblish_cost."+".$summary_data[$job_id]['comm_cost_total_value']."+".$summary_data[$job_id]['trims_cost_total_value']."+".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$summary_data[$job_id]['yarn_amount_total_value']."+".$tot_aop_process_amount."+".$summary_data[$job_id]['common_oh_total_value']."+".$summary_data[$job_id]['studio_pre_cost_total_value']."+".$tot_inspect_cour_certi_cost."<br>";
										$tot_quot_sum_amount=$total_quot_amount_cal-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
										// echo $total_quot_amount_cal."-(".$CommiData_foreign_cost."+".$pri_freight_cost_per."+".$pri_commercial_per.")<br>";
										$NetFOBValue_job = $tot_quot_sum_amount;
										// echo $NetFOBValue_job."<br>";
										$total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);

										$total_quot_pcs_qty = $quotation_qty_array[$job_id]['QTY_PCS'];
										// echo $total_cm_for_gmt;echo "<br>";
										$cm_valu_lc = 0;
										$cm_valu_lc = $total_cm_for_gmt/$total_quot_pcs_qty;
										// echo number_format(($total_cm_for_gmt/$total_quot_pcs_qty)*12,2); echo "<br>";
										/*========================================================================================
										*																						  *
										*											END											  *	
										*																					  	  *	
										*========================================================================================*/											
										$order_quantitys=$order_qnty_array[$po_id][$item_id][$color_id];								
										$fab_fin_req=$booking_no_fin_qnty_array[$po_id][$item_id][$color_id];	

									 	$today_lay_qnty=$lay_qnty_array[$job_id][$po_id][$item_id][$color_id]["today_lay"];
									 	$total_lay_qnty=$lay_qnty_array[$job_id][$po_id][$item_id][$color_id]["total_lay"];
									 	$cutting_lay_percent = ($total_lay_qnty / $order_quantitys)*100;

									 	$today_qc_qnty=$qc_qnty_array[$job_id][$po_id][$item_id][$color_id]["today_qc"];
									 	$total_qc_qnty=$qc_qnty_array[$job_id][$po_id][$item_id][$color_id]["total_qc"];
									 	$total_qc_rej=$qc_qnty_array[$job_id][$po_id][$item_id][$color_id]["total_rej"];
									 	$cutting_qc_percent = ($total_lay_qnty) ? ($total_qc_qnty / $total_lay_qnty)*100 : 0;

									 	$today_issue_to_print = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_issue_to_print"];
									 	$total_issue_to_print = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_issue_to_print"];
									 	$today_rcv_frm_print = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_rcv_frm_print"];
									 	$total_rcv_frm_print = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_rcv_frm_print"];
									 	$print_reject = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["print_reject"];
									 	$print_wip = $total_issue_to_print - $total_rcv_frm_print;


									 	$today_sewing_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
										$total_sewing_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_input"];
										$input_percent = ($total_lay_qnty) ? ($total_sewing_input / $total_lay_qnty)*100 : 0;

										$today_sewing_output = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_output"];
										$total_sewing_output = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_output"];
										$output_percent = ($total_sewing_input) ? ($total_sewing_output / $total_sewing_input)*100 : 0;

										$today_finishing = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id][0]["today_finishing"];
										$total_finishing = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id][0]["total_finishing"];
										$finishing_percent = ($total_sewing_output) ? ($total_finishing / $total_sewing_output)*100 : 0;

										$today_carton_qty = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_carton_qty"];
										$total_carton_qty = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_carton_qty"];


										$today_ex_fac=$ex_factory_arr[$po_id][$item_id][$color_id]['today_ex_fac'];
										$total_ex_fac=$ex_factory_arr[$po_id][$item_id][$color_id]['total_ex_fac'];	

										$total_sew_ex_fact_bal = $total_sewing_output - $total_ex_fac;
										$total_sew_ex_fact_fob = $total_sew_ex_fact_bal * $row['unit_price'];
										//$issue_qty=$issue_qnty_arr[$batch_mst_id_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]]];
										$issue_qty=$issue_qnty_arr[$po_id][$color_id][18];
										$receive_qty=$issue_qnty_arr[$po_id][$color_id][37];

										$fab_in_hand=$receive_qty-$issue_qty;
										$unit_price = $row['unit_price'];
										$today_fob_val = $today_sewing_output * $unit_price;
										$total_fob_val = $total_sewing_output * $unit_price;
										$today_exf_fob_val = $today_ex_fac * $unit_price;
										$total_exf_fob_val = $total_ex_fac * $unit_price;

										$costing_per=$costing_per_arr[$job_id];
										if($costing_per==1) $dzn_qnty=12;
										else if($costing_per==3) $dzn_qnty=12*2;
										else if($costing_per==4) $dzn_qnty=12*3;
										else if($costing_per==5) $dzn_qnty=12*4;
										else $dzn_qnty=1;	
										$dzn_qnty=$dzn_qnty*$row['total_set_qnty'];
										$cm_gmt_cost_dzn=$cm_gmt_cost_dzn_arr_new[$po_id]['dzn'];
										//echo $cm_gmt_cost_dzn.'DD'.$po_id.', ';
										//$cm_per_pcs=(($unit_price*$dzn_qnty)-$total_cost_arr[$job_id])+$cm_cost_arr[$job_id];
										$cm_per_pcs=$cm_gmt_cost_dzn/$dzn_qnty;
										$today_cm_val = $today_sewing_output * $cm_per_pcs;
										$total_cm_val = $total_sewing_output * $cm_per_pcs;
										$today_cm_val_lc = $today_sewing_output * $cm_valu_lc;
										$total_cm_val_lc = $total_sewing_output * $cm_valu_lc;

										$today_exf_cm_val = $today_ex_fac * $cm_per_pcs;
										$total_exf_cm_val = $total_ex_fac * $cm_per_pcs;
										$today_exf_cm_val_lc = $today_ex_fac * $cm_valu_lc;
										$total_exf_cm_val_lc = $total_ex_fac * $cm_valu_lc;
											
										$rowspan_num = $rowspan_arr[$style_id][$job_id][$po_id][$item_id];
									 
										if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
												<?
												$jj++;													
												?>
											 
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p><? echo $k;?></p></td>

												<? if($r==0){?>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="left"   width="80"><? echo $buyer_library[$row["buyer_name"]]; ?></td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100">
														<? echo implode(PHP_EOL, str_split($row["po_number"],10));?>														
												</td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100">
														<? echo $row['shiping_status'];?>														
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $rowspan_num;?>" width="30" align="center" valign="top">
													<a href="##" onClick="openmypage_image('requires/floor_wise_daily_rmg_production_controller.php?action=show_image&job_no=<? echo $job_id;?>','Image View')">
															<?if(isset($imge_arr[$job_id])){?>
															<!-- <div style="height:0;"> -->
																<img src="../../<? echo $imge_arr[$job_id];?>" height="28" width="28" />
															<!-- </div> -->
															<?}else{?>
																<img src="../../img/noimage.png" height="28" width="28"/>
															<?}?>
														
													</a>
												</td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100"><? echo implode(PHP_EOL, str_split($style_id,10));?></td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100">
														<? echo $row['grouping'];?>														
												</td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100">
													<? 
													$floor_group_name = "";
													foreach($sewing_floor_arr[$po_id][$item_id] as $v)
													{
														$floor_group_name .= $floor_group_library[$v].",";
													}
													//  echo $floor_group_library[$row['floor_id']];
													echo chop($floor_group_name,",");
													?>														
												</td>
												<?}$r++;?>

												<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100"><? echo $color_Arr_library[$color_id];?></td>
												<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="right"    width="70"><p><? echo number_format($order_quantitys,0); ?></p></td> 

												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60"><p><? echo number_format($fab_fin_req,2);?></p></td>

												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<a href="##" onClick="openmypage_fab_popup(<? echo $po_id;?>,<? echo $color_id?>,<? echo 1 ?>, 'fab_issue_popup');" >
														<p><? echo number_format($receive_qty,2);?></p>
													</a>
												</td>
												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<a href="##" onClick="openmypage_fab_popup(<? echo $po_id;?>,<? echo $color_id?>,<? echo 2 ?>, 'fab_issue_popup');" > 
														<p><? echo number_format($issue_qty,2);?></p>
													</a>
												</td>
												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<p><? echo number_format($fab_in_hand,2);?></p>
												</td>
																									
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,0,'A','production_qnty_popup','Today Cut','870','500');">
														<p><? echo number_format($today_lay_qnty,0);?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,0,'B','production_qnty_popup','Total Cut','870','500');">
														<p><? echo number_format($total_lay_qnty,0);?></p>
													</a>
												</td>
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<p><? echo number_format($cutting_lay_percent,0); ?>%</p>
												</td>

												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'A','production_qnty_popup','Today Cutting QC','870','500');">
														<p><? echo number_format($today_qc_qnty,0);?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'B','production_qnty_popup','Total Cutting QC','870','500');">
														<p><? echo number_format($total_qc_qnty,0);?></p>
													</a>
												</td>
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<p><? echo number_format($cutting_qc_percent,0); ?>%</p>
												</td>
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<!-- <a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'C','production_qnty_popup','Cutting QC Reject','870','500');"> --><p>
														<? echo number_format($total_qc_rej,0);?></p>
													<!-- </a> -->
												</td>

												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,2,'A','production_qnty_popup','Today delivery to print','870','500');">
														<p><? echo number_format($today_issue_to_print,0); ?></p>
													</a>														
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,2,'B','production_qnty_popup','Total delivery to print','870','500');">
														<p><? echo number_format($total_issue_to_print,0); ?></p>
													</a>														
												</td>

												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,3,'A','production_qnty_popup','Today receive from print','870','500');">
														<? echo number_format($today_rcv_frm_print,0); ?>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,3,'B','production_qnty_popup','Total receive from print','870','500');">
														<p><? echo number_format($total_rcv_frm_print,0); ?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<!-- <a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,3,'C','production_qnty_popup','Print Reject','870','500');"> -->
														<p><? echo number_format($print_reject,0); ?></p>
													<!-- </a> -->
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><!-- 
													<a href="##" onclick="openmypage_production_popup(<? //echo $po_id;?>,<? //echo $item_id;?>,<? //echo $color_id;?>,3,'D','production_qnty_popup','Print WIP','870','500');"> -->
														<p><? echo number_format($print_wip,0);?></p>
													<!-- </a> -->
												</td> 
																									
													
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,4,'A','production_qnty_popup','Today Sewing Input','870','500');"><p>
														<? echo number_format($today_sewing_input,0);?></p>
													</a>
												</td>

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,4,'B','production_qnty_popup','Total Sewing Input','870','500');"><p>
														<? echo number_format($total_sewing_input,0);?></p>
													</a>
												</td>
												
												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60"><p><? echo number_format($input_percent,0);?>%</p></td>
																 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,5,'A','production_qnty_popup','Today Sewing Output','870','500');"><p>
														<? echo number_format($today_sewing_output,0);?></p>
													</a>
												</td>								 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,5,'B','production_qnty_popup','Total Sewing Output','870','500');"><p>
														<? echo number_format($total_sewing_output,0);?></p>
													</a>
												</td>								 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="60"><p><? echo number_format($output_percent,0);?>%</p></td>

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,8,'A','production_qnty_popup','Today Finishing','870','500');"><p>
														<? echo number_format($today_finishing,0);?></p>
													</a>
												</td>								 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,8,'B','production_qnty_popup','Total Finishing','870','500');"><p>
														<? echo number_format($total_finishing,0);?></p>
													</a>
												</td>							 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60"><p><? echo number_format($finishing_percent,0); ?>%</p></td>

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_ex_fac_total('<? echo $po_id;?>**<? echo $item_id;?>**<? echo $color_id;?>**<? echo str_replace("'", "", $txt_production_date);?>**1','exfac_action');" ><p><? echo number_format($today_ex_fac,0);?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60"><a href="##" onClick="openmypage_ex_fac_total('<? echo $po_id;?>**<? echo $item_id;?>**<? echo $color_id;?>**<? echo str_replace("'", "", $txt_production_date);?>**2', 'exfac_action');" ><p><? echo number_format($total_ex_fac,0);?></p>
													</a>
												</td>

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60"><p>
													<? echo number_format($today_exf_fob_val,2); ?>
												</p></td>								 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60"><p>
													<? echo number_format($total_exf_fob_val,2); ?>
												</p></td>	

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60" title="Per Pcs=<? echo number_format($cm_per_pcs,4).',  CM Dzn='.number_format($cm_gmt_cost_dzn,4);?>"><p><? echo number_format($today_cm_val,2); ?></p></td>	
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60"><p>
													<? echo number_format($total_cm_val,2); ?>
												</p></td>	

												

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60"><p>
													<? echo number_format($today_fob_val,2); ?>
												</p></td>								 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60"><p>
													<? echo number_format($total_fob_val,2); ?>
												</p></td>									 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="105"><p>
													<? echo number_format($total_sew_ex_fact_bal,0); ?>
												</p></td>								 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="105"><p>
													<? echo number_format($total_sew_ex_fact_fob,2); ?>
												</p></td>							 
											</tr>	
										<?
										// ====po wise sub total=======											
										$po_wise_color_qty 			+= $order_quantitys;
										$po_wise_fab_req_qty 		+= $fab_fin_req;
										$po_wise_fab_rcv_qty 		+= $receive_qty;
										$po_wise_fab_iss_qty 		+= $issue_qty;
										$po_wise_fab_inh_qty 		+= $fab_in_hand;
										$po_wise_today_lay_qty 		+= $today_lay_qnty;
										$po_wise_total_lay_qty 		+= $total_lay_qnty;
										$po_wise_cutting_percent 	+= $cutting_percent;
										$po_wise_today_qc_qty 		+= $today_qc_qnty;
										$po_wise_total_qc_qty 		+= $total_qc_qnty;
										$po_wise_cutting_qc_percent	+= $cutting_qc_percent;
										$po_wise_cutting_qc_reject	+= $total_qc_rej;
										$po_wise_today_print_iss_qty+= $today_issue_to_print;
										$po_wise_total_print_iss_qty+= $total_issue_to_print;
										$po_wise_today_print_rcv_qty+= $today_rcv_frm_print;
										$po_wise_total_print_rcv_qty+= $total_rcv_frm_print;
										$po_wise_print_rej_qty 		+= $print_reject;
										$po_wise_print_wip_qty 		+= $print_wip;
										$po_wise_today_sew_in_qty 	+= $today_sewing_input;
										$po_wise_total_sew_in_qty 	+= $total_sewing_input;
										$po_wise_input_percent 		+= $input_percent;
										$po_wise_today_sew_out_qty 	+= $today_sewing_output;
										$po_wise_total_sew_out_qty 	+= $total_sewing_output;
										$po_wise_sewing_percent 	+= $output_percent;
										$po_wise_today_fin_qty 		+= $today_finishing;
										$po_wise_total_fin_qty 		+= $total_finishing;
										$po_wise_finish_percent 	+= $finishing_percent;
										$po_wise_today_ctn_qty 		+= $today_carton_qty;
										$po_wise_total_ctn_qty 		+= $total_carton_qty;
										$po_wise_today_exfact_qty 	+= $today_ex_fac;
										$po_wise_total_exfact_qty 	+= $total_ex_fac;
										$po_wise_today_exf_cm_qty 	+= $today_exf_cm_val;
										$po_wise_total_exf_cm_qty 	+= $total_exf_cm_val;
										$po_wise_today_exf_cm_qty_lc += $today_exf_cm_val_lc;
										$po_wise_total_exf_cm_qty_lc += $total_exf_cm_val_lc;
										$po_wise_today_exf_fob_qty 	+= $today_exf_fob_val;
										$po_wise_total_exf_fob_qty 	+= $total_exf_fob_val;
										$po_wise_today_sew_cm_qty 	+= $today_cm_val;
										$po_wise_total_sew_cm_qty 	+= $total_cm_val;
										$po_wise_today_sew_cm_qty_lc += $today_cm_val_lc;
										$po_wise_total_sew_cm_qty_lc += $total_cm_val_lc;
										$po_wise_today_fob_qty 		+= $today_fob_val;
										$po_wise_total_fob_qty 		+= $total_fob_val;
										$po_wise_total_ex_bal_qty	+= $total_sew_ex_fact_bal;
										$po_wise_total_ex_fob_qty	+= $total_sew_ex_fact_fob;
										// ====style wise sub total======
										$style_wise_color_qty 			+= $order_quantitys;
										$style_wise_fab_req_qty 		+= $fab_fin_req;
										$style_wise_fab_rcv_qty 		+= $receive_qty;
										$style_wise_fab_iss_qty 		+= $issue_qty;
										$style_wise_fab_inh_qty 		+= $fab_in_hand;
										$style_wise_today_lay_qty 		+= $today_lay_qnty;
										$style_wise_total_lay_qty 		+= $total_lay_qnty;
										$style_wise_cutting_percent 	+= $cutting_percent;
										$style_wise_today_qc_qty 		+= $today_qc_qnty;
										$style_wise_total_qc_qty 		+= $total_qc_qnty;
										$style_wise_cutting_qc_percent 	+= $cutting_qc_percent;
										$style_wise_cutting_qc_reject 	+= $total_qc_rej;
										$style_wise_today_print_iss_qty += $today_issue_to_print;
										$style_wise_total_print_iss_qty += $total_issue_to_print;
										$style_wise_today_print_rcv_qty += $today_rcv_frm_print;
										$style_wise_total_print_rcv_qty += $total_rcv_frm_print;
										$style_wise_print_rej_qty 		+= $print_reject;
										$style_wise_print_wip_qty 		+= $print_wip;
										$style_wise_today_sew_in_qty 	+= $today_sewing_input;
										$style_wise_total_sew_in_qty 	+= $total_sewing_input;
										$style_wise_input_percent 		+= $input_percent;
										$style_wise_today_sew_out_qty 	+= $today_sewing_output;
										$style_wise_total_sew_out_qty 	+= $total_sewing_output;
										$style_wise_sewing_percent 		+= $output_percent;
										$style_wise_today_fin_qty 		+= $today_finishing;
										$style_wise_total_fin_qty 		+= $total_finishing;
										$style_wise_finish_percent 		+= $finishing_percent;
										$style_wise_today_ctn_qty 		+= $today_carton_qty;
										$style_wise_total_ctn_qty 		+= $total_carton_qty;
										$style_wise_today_exfact_qty 	+= $today_ex_fac;
										$style_wise_total_exfact_qty 	+= $total_ex_fac;
										$style_wise_today_exf_cm_qty 	+= $today_exf_cm_val;
										$style_wise_total_exf_cm_qty 	+= $total_exf_cm_val;
										$style_wise_today_exf_fob_qty 	+= $today_exf_fob_val;
										$style_wise_total_exf_fob_qty 	+= $total_exf_fob_val;
										$style_wise_today_sew_cm_qty 	+= $today_cm_val;
										$style_wise_total_sew_cm_qty 	+= $total_cm_val;
										$style_wise_today_fob_qty 		+= $today_fob_val;
										$style_wise_total_fob_qty 		+= $total_fob_val;
										$style_wise_total_ex_bal_qty	+= $total_sew_ex_fact_bal;
										$style_wise_total_ex_fob_qty	+= $total_sew_ex_fact_fob;
										// ========= grand total =========== 
										$gr_color_qty 			+= $order_quantitys;
										$gr_fab_req_qty 		+= $fab_fin_req;
										$gr_fab_rcv_qty 		+= $receive_qty;
										$gr_fab_iss_qty 		+= $issue_qty;
										$gr_fab_inh_qty 		+= $fab_in_hand;
										$gr_today_lay_qty 		+= $today_lay_qnty;
										$gr_total_lay_qty 		+= $total_lay_qnty;
										$gr_cutting_percent 	+= $cutting_percent;
										$gr_today_qc_qty 		+= $today_qc_qnty;
										$gr_total_qc_qty 		+= $total_qc_qnty;
										$gr_cutting_qc_percent 	+= $cutting_qc_percent;
										$gr_cutting_qc_reject 	+= $total_qc_rej;
										$gr_today_print_iss_qty += $today_issue_to_print;
										$gr_total_print_iss_qty += $total_issue_to_print;
										$gr_today_print_rcv_qty += $today_rcv_frm_print;
										$gr_total_print_rcv_qty += $total_rcv_frm_print;
										$gr_print_rej_qty 		+= $print_reject;
										$gr_print_wip_qty 		+= $print_wip;
										$gr_today_sew_in_qty 	+= $today_sewing_input;
										$gr_total_sew_in_qty 	+= $total_sewing_input;
										$gr_input_percent 		+= $input_percent;
										$gr_today_sew_out_qty 	+= $today_sewing_output;
										$gr_total_sew_out_qty 	+= $total_sewing_output;
										$gr_sewing_percent 		+= $output_percent;
										$gr_today_fin_qty 		+= $today_finishing;
										$gr_total_fin_qty 		+= $total_finishing;
										$gr_finish_percent 		+= $finishing_percent;
										$gr_today_ctn_qty 		+= $today_carton_qty;
										$gr_total_ctn_qty 		+= $total_carton_qty;
										$gr_today_exfact_qty 	+= $today_ex_fac;
										$gr_total_exfact_qty 	+= $total_ex_fac;
										$gr_today_exf_cm_qty 	+= $today_exf_cm_val;
										$gr_total_exf_cm_qty 	+= $total_exf_cm_val;
										$gr_today_exf_cm_qty_lc += $today_exf_cm_val_lc;
										$gr_total_exf_cm_qty_lc += $total_exf_cm_val_lc;
										$gr_today_exf_fob_qty 	+= $today_exf_fob_val;
										$gr_total_exf_fob_qty 	+= $total_exf_fob_val;
										$gr_today_sew_cm_qty 	+= $today_cm_val;
										$gr_total_sew_cm_qty 	+= $total_cm_val;
										$gr_today_sew_cm_qty_lc += $today_cm_val_lc;
										$gr_total_sew_cm_qty_lc += $total_cm_val_lc;
										$gr_today_fob_qty 		+= $today_fob_val;
										$gr_total_fob_qty 		+= $total_fob_val;
										$gr_total_ex_bal_qty	+= $total_sew_ex_fact_bal;
										$gr_total_ex_fob_qty 	+= $total_sew_ex_fact_fob;
										
										$k++;											
									}
								}
								?>
								<tr bgcolor="#E4E4E4">
									<td colspan="9" align="right"><b>Order Wise Sub Total</b></td>
									<td align="right"><b><? echo number_format($po_wise_color_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_fab_req_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_fab_rcv_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_fab_iss_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_fab_inh_qty,2);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_lay_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_lay_qty,0);?></b></td>
									<td align="right"></td>

									<td align="right"><b><? echo number_format($po_wise_today_qc_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_qc_qty,0);?></b></td>
									<td align="right"></td>							
									<td align="right"><b><? echo number_format($po_wise_cutting_qc_reject,0);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_print_iss_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_print_iss_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_today_print_rcv_qty,0); ?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_print_rcv_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_print_rej_qty,0); ?></b></td>										
									<td align="right"><b><? echo number_format($po_wise_print_wip_qty,0);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_sew_in_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_sew_in_qty,0);?></b></td>
									<td align="right"><b></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_sew_out_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_sew_out_qty,0);?></b></td>
									<td align="right"><b><? //echo $order_wise_input_sewing_balance;?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_fin_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_fin_qty,0);?></b></td>
									<td align="right"><b><? //echo $order_wise_sewing_fin_balance;?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_exfact_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_exfact_qty,0);?></b></td>

									
									<td align="right"><b><? echo number_format($po_wise_today_exf_fob_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_exf_fob_qty,2);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_sew_cm_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_sew_cm_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_today_fob_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_fob_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_ex_bal_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_ex_fob_qty,2);?></b></td>
								</tr>
								<?
							}							
						}							
						?>
						<!-- <tr bgcolor="#E4E4E4">
							<td colspan="7" align="right"><b>Style Wise Sub Total</b></td>
							<td align="right"><b><? echo number_format($style_wise_color_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_req_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_rcv_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_iss_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($order_wise_fab_req,2);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_lay_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_lay_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($order_wise_fab_possible_qty,2);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_qc_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_qc_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_cutting_qc_percent,2);?></b></td>							
							<td align="right"><b><? echo number_format($style_wise_cutting_qc_reject,0);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_print_iss_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_print_iss_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_today_print_rcv_qty,0); ?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_print_rcv_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_print_rej_qty,0); ?></b></td>										
							<td align="right"><b><? echo number_format($style_wise_print_wip_qty,0);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_sew_in_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_sew_in_qty,0);?></b></td>
							<td align="right"><b></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_sew_out_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_sew_out_qty,0);?></b></td>
							<td align="right"><b><? //echo $order_wise_input_sewing_balance;?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_fin_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_fin_qty,0);?></b></td>
							<td align="right"><b><? //echo $order_wise_sewing_fin_balance;?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_exfact_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_exfact_qty,0);?></b></td>

							
							<td align="right"><b><? echo number_format($style_wise_total_ex_bal_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_ex_fob_qty,2);?></b></td>
						</tr> -->	
						<?
						$gr_inh_qty;
					}

					?>											
					</table>										  
				</div>	
			</div>
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3500" rules="all"  >
				<tr bgcolor="#E4E4E4"  >  
					<td valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p>&nbsp; </p></td>

					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p>&nbsp;</p></td>
					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>
					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>

					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="30"><p>&nbsp;</p></td>

					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>
					
					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>
					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>

					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><strong>Grand Total</strong></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="70"    align="right"><b><? echo number_format($gr_color_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60"   align="right"><b><? echo number_format($gr_fab_req_qty,2) ?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60"   align="right"><b><? echo number_format($gr_fab_rcv_qty,2) ?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60"   align="right"><b><? echo number_format($gr_fab_iss_qty,2) ?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_fab_inh_qty,2);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_lay_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_lay_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo number_format($gr_cutting_percent,2);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_qc_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_qc_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo number_format($gr_cutting_qc_percent,2);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_cutting_qc_reject,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_print_iss_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_print_iss_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_print_rcv_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_print_rcv_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_print_rej_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_print_wip_qty,0);?></b></td>



					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_sew_in_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_sew_in_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo $gr_today_sew_out_qty;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_sew_out_qty,0);?></b></td>		
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_sew_out_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo $gr_total_sew_out_qty;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_fin_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_fin_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo $gr_total_finishing;?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_exfact_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_exfact_qty,0);?></b></td>

																 
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_exf_fob_qty,2);?></b></td>											 
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_exf_fob_qty,2);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_sew_cm_qty,2);?></b></td>											 
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_sew_cm_qty,2);?></b></td>										 
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_fob_qty,2);?></b></td>											 
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_fob_qty,2);?></b></td>											 
					<td style="word-wrap: break-word;word-break: break-all;"  width="105" align="right"><b><? echo number_format($gr_total_ex_bal_qty,0);?></b></td>											 
					<td style="word-wrap: break-word;word-break: break-all;"  width="105" align="right"><b><? echo number_format($gr_total_ex_fob_qty,2);?></b></td>											 

				</tr>	
				
			</table>	
		 </div> 
        <?
        foreach (glob("*.xls") as $filename) {
	    @unlink($filename);
	    }
	    //---------end------------//
	    $name2=time();
	    $summary_filename="summary_".$name2.".xls";
	    $create_new_doc2 = fopen($summary_filename, 'w');	
	    $is_created2 = fwrite($create_new_doc2, $summary_html);
	    //======================================================
		
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename####$summary_filename";
		exit();	 
	}	
	elseif(str_replace("'","",$type)==2) // show2
	{
		// ============================= getting form value =============================
		$working_company_id = str_replace("'", "", $cbo_work_company_name);
		$location_id 		= str_replace("'", "", $cbo_location_name);
		$floor_id 			= str_replace("'", "", $cbo_floor_name);
		$group_name 		= str_replace("'", "", $cbo_group_name);
		$buyer_id 			= str_replace("'", "", $cbo_buyer_name);
		$year 				= str_replace("'", "", $cbo_year);
		$job_id 			= str_replace("'", "", $txt_job_no);
		$hidden_job_id 		= str_replace("'", "", $hidden_job_id);
		$txt_int_ref 		= str_replace("'", "", $txt_int_ref);
		$order_id 			= str_replace("'", "", $txt_order_no);
		$hidden_order_id 	= str_replace("'", "", $hidden_order_id);
		$shiping_status 	= str_replace("'", "", $cbo_shipping_status);
		// echo $location_id."_".$floor_id."_".$group_name;

		$sql_cond_lay 		= "";
		$sql_cond_qc 		= "";
		$sql_cond_prod 		= "";
		$sql_cond_exfact 	= "";

		$lay_cond 			= "";
		$qc_cond 			= "";
		$exfact_cond 		= "";

		if($working_company_id !="")
		{
			$sql_cond_lay 	= " and d.working_company_id in($working_company_id)";
			$sql_cond_qc 	= " and d.serving_company in($working_company_id)";
			$sql_cond_prod 	= " and d.serving_company in($working_company_id)";
			$sql_cond_exfact= " and f.delivery_company_id in($working_company_id)";

			$lay_cond 		.= " and a.working_company_id in($working_company_id)";
			$qc_cond 		.= " and a.serving_company in($working_company_id)";
			$exfact_cond	.= " and d.delivery_company_id in($working_company_id)";
		}

		if($location_id !="")
		{
			$sql_cond_lay 	.= " and d.location_id in($location_id)";
			$sql_cond_qc 	.= " and d.location_id in($location_id)";
			$sql_cond_prod 	.= " and d.location in($location_id)";
			$sql_cond_exfact.= " and f.delivery_location_id in($location_id)";

			$lay_cond 		.= " and a.location_id in($location_id)";
			$qc_cond 		.= " and a.location_id in($location_id)";
			$exfact_cond	.= " and d.delivery_location_id in($location_id)";
		}

		if($shiping_status !="")
		{
			$sql_cond_lay 	.= " and b.shiping_status in($shiping_status)";
			$sql_cond_qc 	.= " and b.shiping_status in($shiping_status)";
			$sql_cond_prod 	.= " and b.shiping_status in($shiping_status)";
			$sql_cond_exfact.= " and b.shiping_status in($shiping_status)";
		}

		if($group_name)
		{
			$group_cond="";
			$group_sql = sql_select("SELECT a.id from lib_prod_floor a where a.status_active=1 and a.group_name=$cbo_group_name order by a.id");
			foreach ($group_sql as $value) 
			{
				if($group_cond=="")
				{
					$group_cond = $value[csf('id')];
				}
				else
				{
					$group_cond .= ",".$value[csf('id')];
				}
			}
			$sql_cond_lay.=" and d.floor_id in($group_cond)";
			$sql_cond_qc.=" and d.floor_id in($group_cond)";
			$sql_cond_prod.=" and d.floor_id in($group_cond)";
			$sql_cond_exfact.=" and f.delivery_floor_id in($group_cond)";

			$lay_cond 		.= " and a.floor_id in($group_cond)";
			$qc_cond 		.= " and a.floor_id in($group_cond)";
			$exfact_cond	.= " and d.delivery_floor_id in($group_cond)";
		}
		else if($floor_id !="")
		{
			$sql_cond_lay 	.= " and d.floor_id in($floor_id)";
			$sql_cond_qc 	.= " and d.floor_id in($floor_id)";
			$sql_cond_prod 	.= " and d.floor_id in($floor_id)";
			$sql_cond_exfact.= " and f.delivery_floor_id in($floor_id)";

			$lay_cond 		.= " and a.floor_id in($floor_id)";
			$qc_cond 		.= " and a.floor_id in($floor_id)";
			$exfact_cond	.= " and d.delivery_floor_id in($floor_id)";
		}

		if($buyer_id !=0)
		{
			$sql_cond_lay 	.= " and a.buyer_name = $buyer_id";
			$sql_cond_qc 	.= " and a.buyer_name = $buyer_id";
			$sql_cond_prod 	.= " and a.buyer_name = $buyer_id";
			$sql_cond_exfact.= " and a.buyer_name = $buyer_id";
		}

		if($year !=0)
		{
			if($db_type==0)
			{
				$sql_cond_lay .=" and year(a.insert_date)=$year";
				$sql_cond_qc .=" and year(a.insert_date)=$year";
				$sql_cond_prod .=" and year(a.insert_date)=$year";
				$sql_cond_exfact .=" and year(a.insert_date)=$year";
			}
			else
			{
				$sql_cond_lay .=" and to_char(a.insert_date,'YYYY')=$year";
				$sql_cond_qc .=" and to_char(a.insert_date,'YYYY')=$year";
				$sql_cond_prod .=" and to_char(a.insert_date,'YYYY')=$year";
				$sql_cond_exfact .=" and to_char(a.insert_date,'YYYY')=$year";
			}
		}

		if($hidden_job_id!="")
		{
			$sql_cond_lay.=" and a.id in($hidden_job_id)";
			$sql_cond_qc.=" and a.id in($hidden_job_id)";
			$sql_cond_prod.=" and a.id in($hidden_job_id)";
			$sql_cond_exfact.=" and a.id in($hidden_job_id)";
		}
		
		if($hidden_order_id !="")
		{
			$sql_cond_lay.=" and b.id in($hidden_order_id)";
			$sql_cond_qc.=" and b.id in($hidden_order_id)";
			$sql_cond_prod.=" and b.id in($hidden_order_id)";
			$sql_cond_exfact.=" and b.id in($hidden_order_id)";
		} 

		/*if($txt_int_ref !="")
		{
			$sql_cond_lay.=" and b.grouping like '%$txt_int_ref%'";
			$sql_cond_qc.=" and b.grouping like '%$txt_int_ref%'";
			$sql_cond_prod.=" and b.grouping like '%$txt_int_ref%'";
			$sql_cond_exfact.=" and b.grouping like '%$txt_int_ref%'";
		}*/ 

		// ======================================== MAIN QUERY FOR LAY =========================================
		$today_lay_sql="SELECT a.company_name, c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
		where a.id=b.job_id and b.id=c.po_break_down_id and d.job_no=a.job_no and d.id=e.mst_id  and a.id=c.job_id and e.mst_id=f.mst_id and f.order_id=b.id and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_confirmed=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.entry_date=$txt_production_date $sql_cond_lay
		group by a.company_name,c.color_number_id,b.id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty"; 
		// echo $today_lay_sql;die();
		

		$po_id_array=array();
		$col_id_array=array();
		$production_main_array=array();
		$buyer_wise_summary_array=array();
		$buyer_wise_all_info=array();
		$lc_company_array = array();
		$all_job_array = array();
		foreach(sql_select($today_lay_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_job_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("working_company_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_set_qnty"]=$row[csf("total_set_qnty")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

			$buyer_wise_summary_array[$row[csf("buyer_name")]]['unit_price'] = $row[csf("unit_price")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['total_set_qnty'] = $row[csf("total_set_qnty")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['job_no'] = $row[csf("job_no")];

			$lc_company_array[$row[csf("company_name")]] = $row[csf("company_name")];

		}

		// ======================================== MAIN QUERY FOR CUTTING QC =========================================
		$today_qc_sql="SELECT a.company_name, c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.serving_company,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_gmts_cutting_qc_mst d,pro_gmts_cutting_qc_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.job_no=a.job_no and d.id=e.mst_id  and a.id=c.job_id and b.id=e.order_id  and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_confirmed=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.entry_date=$txt_production_date $sql_cond_qc
		group by a.company_name, c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.serving_company,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty"; //and c.id=e.color_size_id
		// echo $today_qc_sql;die();		
		
		
		foreach(sql_select($today_qc_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_job_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("serving_company")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_set_qnty"]=$row[csf("total_set_qnty")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

			$buyer_wise_summary_array[$row[csf("buyer_name")]]['unit_price'] = $row[csf("unit_price")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['total_set_qnty'] = $row[csf("total_set_qnty")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['job_no'] = $row[csf("job_no")];

			$lc_company_array[$row[csf("company_name")]] = $row[csf("company_name")];

		}

		// ======================================== MAIN QUERY FOR PRODUCTION =========================================
		$today_prod_sql="SELECT a.company_name, c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cut_no,b.po_number,d.serving_company,d.location,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty,d.production_type  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.id=e.mst_id  and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_confirmed=1  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.production_date=$txt_production_date $sql_cond_prod
		group by a.company_name, c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,d.cut_no,b.po_number,d.serving_company,d.location,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty,d.production_type  "; //and c.id=e.color_size_break_down_id
		// echo $today_prod_sql;die();		
		
		$sewing_floor_arr = array();
		foreach(sql_select($today_prod_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_job_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
			if($row[csf("production_type")]==4 || $row[csf("production_type")]==5)
			{
				$sewing_floor_arr[$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]['floor_id'] .= $row[csf("floor_id")].",";
			}

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("serving_company")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_set_qnty"]=$row[csf("total_set_qnty")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

			$buyer_wise_summary_array[$row[csf("buyer_name")]]['unit_price'] = $row[csf("unit_price")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['total_set_qnty'] = $row[csf("total_set_qnty")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['job_no'] = $row[csf("job_no")];

			$lc_company_array[$row[csf("company_name")]] = $row[csf("company_name")];

		}
		// echo "<pre>";print_r($sewing_floor_arr);die;
		// ======================================== MAIN QUERY FOR EX-FACTORY =========================================
		$today_exf_sql="SELECT a.company_name, c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,b.po_number,f.delivery_company_id,f.delivery_location_id,f.delivery_floor_id, a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_ex_factory_mst d,pro_ex_factory_dtls e, pro_ex_factory_delivery_mst f
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.id=e.mst_id and f.id=d.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_confirmed=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.ex_factory_date=$txt_production_date $sql_cond_exfact
		group by a.company_name, c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,b.po_number,f.delivery_company_id,f.delivery_location_id,f.delivery_floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty"; //and c.id=e.color_size_break_down_id
		// echo $today_exf_sql;die();		
		
		
		foreach(sql_select($today_exf_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_job_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("delivery_company_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("delivery_location_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("delivery_floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_set_qnty"]=$row[csf("total_set_qnty")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

			$buyer_wise_summary_array[$row[csf("buyer_name")]]['unit_price'] = $row[csf("unit_price")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['total_set_qnty'] = $row[csf("total_set_qnty")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['job_no'] = $row[csf("job_no")];

			$lc_company_array[$row[csf("company_name")]] = $row[csf("company_name")];

		}
		// echo "<pre>";
		// print_r($production_main_array);die;
		if(count($production_main_array)==0)
		{
			?>
			<div style="margin:20px auto; width: 90%">
				<div class="alert alert-error">
				  <strong>Data not found!</strong>
				</div>
			</div>
			<?
			die();
		} 

		$prod_po_ids=implode(",", $prod_po_id_array);
		if($prod_po_ids)
		{
			$po_conds2=" and b.id in($prod_po_ids)";
		}

		$po_ids=implode(",", $po_id_array);
		$color_ids=implode(",", $col_id_array);
		if(!$po_ids) $po_ids=0;
		if(!$color_ids) $color_ids=0;

		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( po_id_string in ($ids) ";
				else
					$po_cond.=" or   po_id_string in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and po_id_string in ($po_ids) ";
		}
		//echo $po_cond;die;
		// ===================================== FOR COLOR WISE ORDER QTY ========================================	
		$poIDs=implode(",", $po_id_array);
		// if($poIDs!="")
		// {
		// 	$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($working_company_id,$poIDs); 
		// 	//print_r($cm_gmt_cost_dzn_arr);
		// }
		$lc_company = implode(",", $lc_company_array);
		$cm_gmt_cost_dzn_arr=array();
		$cm_gmt_cost_dzn_arr_new=array();				 
		$new_arr=array_unique(explode(",", $poIDs));
		$chnk_arr=array_chunk($new_arr,50);
		foreach($chnk_arr as $vals )
		{
			$p_ids=implode(",", $vals);
			$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($lc_company,$p_ids); 
			 foreach($cm_gmt_cost_dzn_arr as $po_id=>$vv)
			 {
			 	$cm_gmt_cost_dzn_arr_new[$po_id]["dzn"]=$vv["dzn"] ;
			 }
		}

		$order_qnty_array=array();
		$buyer_wise_order_qnty_array=array();
		$po_cond1=str_replace("po_id_string", "po_break_down_id", $po_cond);
		$order_qnty_sqls="SELECT b.po_break_down_id,b.color_number_id,b.item_number_id,sum(b.order_quantity) as order_quantity,a.buyer_name from wo_po_details_master a, wo_po_color_size_breakdown b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $po_cond1 group by b.po_break_down_id,b.color_number_id,b.item_number_id,a.buyer_name";
		// echo $order_qnty_sqls;die();
		foreach(sql_select($order_qnty_sqls) as $values)
		{
		 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]=$values[csf("order_quantity")];

		 	$buyer_wise_order_qnty_array[$values[csf("buyer_name")]] += $values[csf("order_quantity")];
		}
		
		// echo "<pre>";
		// print_r($buyer_wise_summary_array);
		// echo "</pre>";
		// die();
		// ============================================ FOR PRODUCTION ================================================
		$po_cond2=str_replace("po_id_string", "b.id", $po_cond);
		$order_sql="SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no,c.color_number_id, 
		sum(c.order_quantity) as order_quantity, 
		sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_cutting ,
		sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,
        sum(case when d.production_type=1 and e.production_type=1 then e.reject_qty else 0 end ) as cut_reject_qty,

		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_issue_to_print ,
		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=1  then e.production_qnty else 0 end ) as total_issue_to_print ,
		sum(case when d.production_type=2 and e.production_type=63 and d.embel_name=2 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_issue_to_embroidery ,
		sum(case when d.production_type=2 and e.production_type=63 and d.embel_name=2  then e.production_qnty else 0 end ) as total_issue_to_embroidery ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_rcv_frm_print ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=1  then e.production_qnty else 0 end ) as total_rcv_frm_print,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=2 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_rcv_frm_embroidery ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=2 then e.production_qnty else 0 end ) as total_rcv_frm_embroidery ,
		sum(case when d.production_type in(2,3) and e.production_type in(2,3) and d.embel_name=1  then e.reject_qty else 0 end ) as print_reject ,
        sum(case when d.production_type in(2,3) and e.production_type in(2,3) and d.embel_name=2  then e.reject_qty else 0 end ) as reject_embroidery ,

		sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_input ,
		sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input ,

		sum(case when d.production_type=5 and e.production_type=5 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_output ,
		sum(case when d.production_type=5 and e.production_type=5   then e.production_qnty else 0 end ) as total_sewing_output ,

		sum(case when d.production_type=8 and e.production_type=8 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_finishing ,
		sum(case when d.production_type=8 and e.production_type=8   then e.production_qnty else 0 end ) as total_finishing,

		sum(case when d.production_type=8 and e.production_type=8 and d.production_date=$txt_production_date then d.carton_qty else 0 end ) as today_carton_qty,
		sum(case when d.production_type=8 and e.production_type=8  then d.carton_qty else 0 end ) as total_carton_qty

		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $sql_cond_prod $po_cond2 and d.production_type in(1,2,3,4,5,8,11) 
		group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  ,  c.color_number_id,e.cut_no ";

		// echo $order_sql;die;
		$order_sql_res = sql_select($order_sql);
		$cutting_sewing_data = array();
		$buyer_wise_cutting_sewing_data = array();
		foreach($order_sql_res as $vals)
		{
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_cutting"]+=$vals[csf("today_cutting")];			 

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_cutting"]+=$vals[csf("total_cutting")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["cut_reject_qty"]+=$vals[csf("cut_reject_qty")];
			 

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_issue_to_print"]+=$vals[csf("today_issue_to_print")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_issue_to_print"]+=$vals[csf("total_issue_to_print")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_rcv_frm_print"]+=$vals[csf("today_rcv_frm_print")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_rcv_frm_print"]+=$vals[csf("total_rcv_frm_print")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["print_reject"]+=$vals[csf("print_reject")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_issue_to_embroidery"]+=$vals[csf("today_issue_to_embroidery")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_issue_to_embroidery"]+=$vals[csf("total_issue_to_embroidery")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_rcv_frm_embroidery"]+=$vals[csf("today_rcv_frm_embroidery")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_rcv_frm_embroidery"]+=$vals[csf("total_rcv_frm_embroidery")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["reject_embroidery"]+=$vals[csf("reject_embroidery")];


			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

			 
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_output"]+=$vals[csf("today_sewing_output")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_output"]+=$vals[csf("total_sewing_output")];


			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][0]["today_finishing"]+=$vals[csf("today_finishing")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][0]["total_finishing"]+=$vals[csf("total_finishing")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_carton_qty"]+=$vals[csf("today_carton_qty")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_carton_qty"]+=$vals[csf("total_carton_qty")];
			// ======================================== BUYER WISE SUM ======================================================
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_cutting"]			+= $vals[csf("today_cutting")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_cutting"]			+= $vals[csf("total_cutting")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["cut_reject_qty"]			+= $vals[csf("cut_reject_qty")];

			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_issue_to_print"] 	+= $vals[csf("today_issue_to_print")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_issue_to_print"] 	+= $vals[csf("total_issue_to_print")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_rcv_frm_print"] 	+= $vals[csf("today_rcv_frm_print")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_rcv_frm_print"] 	+= $vals[csf("total_rcv_frm_print")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["print_reject"]	 		+= $vals[csf("print_reject")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_issue_to_embroidery"] 	+= $vals[csf("today_issue_to_embroidery")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_issue_to_embroidery"] 	+= $vals[csf("total_issue_to_embroidery")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_rcv_frm_embroidery"] 	+= $vals[csf("today_rcv_frm_embroidery")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_rcv_frm_embroidery"] 	+= $vals[csf("total_rcv_frm_embroidery")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["reject_embroidery"]	 		+= $vals[csf("reject_embroidery")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_sewing_input"] 	+= $vals[csf("today_sewing_input")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_sewing_input"] 	+= $vals[csf("total_sewing_input")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_sewing_output"] 	+= $vals[csf("today_sewing_output")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_sewing_output"] 	+= $vals[csf("total_sewing_output")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_finishing"]		+= $vals[csf("today_finishing")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_finishing"] 		+= $vals[csf("total_finishing")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_carton_qty"] 		+= $vals[csf("today_carton_qty")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_carton_qty"] 		+= $vals[csf("total_carton_qty")];
		}
		// echo "<pre>";
		// print_r($cutting_sewing_data);die;

		// =========================================== FOR CUTTING LAY QTY ==========================================
	  	$po_cond3=str_replace("po_id_string", "c.order_id", $po_cond); 	
	  	$lay_sqls="SELECT  a.job_no,c.order_id, b.gmt_item_id,b.color_id,sum( case when a.entry_date=$txt_production_date then  c.size_qty else 0 end) as today_lay,sum(c.size_qty) as total_lay,d.buyer_name from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c,wo_po_details_master d where a.job_no=d.job_no and a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond3  $lay_cond  group by a.job_no,c.order_id, b.gmt_item_id,b.color_id,d.buyer_name ";
	  	// echo $lay_sqls;die();
		$lay_qnty_array=array();
		$buyer_wise_lay_qnty_array=array();
		foreach(sql_select($lay_sqls) as $vals)
		{
			$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["today_lay"]+=$vals[csf("today_lay")];
			$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("total_lay")];
			$buyer_wise_lay_qnty_array[$vals[csf("buyer_name")]]["today_lay"]+=$vals[csf("today_lay")];
			$buyer_wise_lay_qnty_array[$vals[csf("buyer_name")]]["total_lay"]+=$vals[csf("total_lay")];
		}
		// echo "<pre>";
		// print_r($buyer_wise_lay_qnty_array);
		// echo "</pre>";
		// =========================================== FOR CUTTING QC ==========================================
	  	$po_cond_qc=str_replace("po_id_string", "b.order_id", $po_cond); 	
	  	$qc_sqls="SELECT  a.job_no,b.order_id, c.item_number_id,b.color_id,d.buyer_name,
	  	sum( case when a.entry_date=$txt_production_date then  b.qc_pass_qty else 0 end) as today_qc,
	  	sum(b.qc_pass_qty) as total_qc,
	  	sum(b.reject_qty) as total_rej 
	  	from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b,wo_po_color_size_breakdown c,wo_po_details_master d
	  	where d.job_no=a.job_no and a.id=b.mst_id and b.color_size_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 $po_cond_qc $qc_cond
	  	group by a.job_no,b.order_id, c.item_number_id,b.color_id,d.buyer_name";
	  	// echo $qc_sqls;die();
		$qc_qnty_array=array();
		$buyer_wise_qc_qnty_array=array();
		foreach(sql_select($qc_sqls) as $vals)
		{
			$qc_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("item_number_id")]][$vals[csf("color_id")]]["today_qc"]+=$vals[csf("today_qc")];
			$qc_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("item_number_id")]][$vals[csf("color_id")]]["total_qc"]+=$vals[csf("total_qc")];
			$qc_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("item_number_id")]][$vals[csf("color_id")]]["total_rej"]+=$vals[csf("total_rej")];
			$buyer_wise_qc_qnty_array[$vals[csf('buyer_name')]]["today_qc"]+=$vals[csf("today_qc")];
			$buyer_wise_qc_qnty_array[$vals[csf('buyer_name')]]["total_qc"]+=$vals[csf("total_qc")];
			$buyer_wise_qc_qnty_array[$vals[csf('buyer_name')]]["total_rej"]+=$vals[csf("total_rej")];
		}

		// ==================================================== FOR EX-FACTORY QTY ==========================================
		$po_cond4=str_replace("po_id_string", "a.po_break_down_id", $po_cond); 	
		$ex_factory_arr=array();
		$buyer_wise_ex_factory_arr=array();
		$ex_factory_data="SELECT a.po_break_down_id, a.item_number_id,c.color_number_id,d.buyer_id, sum(CASE WHEN a.entry_form!=85 and ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS today_ex_fac , sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c where d.id=a.delivery_mst_id  and  a.id=b.mst_id and b.color_size_break_down_id=c.id   and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $exfact_cond  $po_cond4  group by a.po_break_down_id, a.item_number_id,c.color_number_id,d.buyer_id";
		// echo $ex_factory_data;
		$ex_factory_data_res = sql_select($ex_factory_data);
		foreach($ex_factory_data_res as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['today_ex_fac']+=$exRow[csf('today_ex_fac')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['total_ex_fac']=+$exRow[csf('total_ex_fac')];
			$buyer_wise_ex_factory_arr[$exRow[csf('buyer_id')]]['today_ex_fac']+=$exRow[csf('today_ex_fac')];
			// $buyer_wise_ex_factory_arr[$exRow[csf('buyer_id')]]['total_ex_fac']+=$exRow[csf('total_ex_fac')];
		}
		// echo "<pre>";
		// print_r($ex_factory_arr);
		// echo "</pre>";
		// die();
		// =========================================== FOR FIN. FAB. REQ. QTY ========================================================
		$booking_no_fin_qnty_array=array();
		$buyer_booking_no_fin_qnty_array=array();
		$booking_sql=sql_select("SELECT a.po_break_down_id as po_id ,b.item_number_id as item_id, b.color_number_id as color_id,a.booking_no,a.fin_fab_qnty,c.buyer_name from wo_booking_dtls a,wo_po_color_size_breakdown b,wo_po_details_master c  where c.job_no=a.job_no and b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1  $po_cond4");
		foreach($booking_sql as $vals)
		{
			$booking_no_fin_qnty_array[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("color_id")]]+=$vals[csf("fin_fab_qnty")];
			// $buyer_booking_no_fin_qnty_array[$vals[csf("buyer_name")]]+=$vals[csf("fin_fab_qnty")];

		}
		// ============================================== FOR FAB. RCV AND ISSUE QTY ==============================================
		$po_cond_fab=str_replace("po_id_string", "c.po_breakdown_id", $po_cond); 
		// $fab_sql="SELECT po_breakdown_id,color_id,entry_form,sum(quantity) as quantity from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(18,37) $po_cond_fab group by po_breakdown_id,color_id,entry_form ";
		$fab_sql="SELECT c.po_breakdown_id,c.color_id,c.entry_form,sum(c.quantity) as quantity,a.buyer_name from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(18,37) $po_cond_fab group by c.po_breakdown_id,c.color_id,c.entry_form,a.buyer_name ";
		$fab_sql_res = sql_select($fab_sql);
		$issue_qnty_arr = array();
		$buyer_issue_qnty_arr = array();
		foreach($fab_sql_res as $values)
		{
		 	$issue_qnty_arr[$values[csf("po_breakdown_id")]][$values[csf("color_id")]][$values[csf("entry_form")]]+=$values[csf("quantity")];
		 	// $buyer_issue_qnty_arr[$values[csf("buyer_name")]][$values[csf("entry_form")]]+=$values[csf("quantity")];
		} 
		// ======================================= FOR COSTING PER ======================================
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$all_job = "'".implode("','", $all_job_array)."'";


		/******************************************************************************************************
		*																									  *
		*								GETTING PRICE QUOTATION WISE CM VALU							      *
		*																									  *			
		*******************************************************************************************************/
		$quotation_qty_sql="SELECT a.id  as quotation_id,a.mkt_no,a.sew_smv,a.sew_effi_percent,a.gmts_item_id,a.company_id,a.buyer_id,a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom,a.offer_qnty, a.total_set_qnty as ratio, a.quot_date,a.est_ship_date,b.costing_per_id,b.price_with_commn_pcs,b.total_cost,b.costing_per_id,c.job_no from wo_price_quotation a,wo_price_quotation_costing_mst b,wo_po_details_master c  where a.id=b.quotation_id and a.id=c.quotation_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.offer_qnty>0 and c.job_no in($all_job) order  by a.id ";
		// echo $quotation_qty_sql;die();
		$quotation_qty_sql_res = sql_select($quotation_qty_sql);
		$quotation_qty_array = array();
		$quotation_id_array = array();
		$all_jobs_array = array();
		$jobs_wise_quot_array = array();
		foreach ($quotation_qty_sql_res as $val) 
		{
			$quotation_qty_array[$val['JOB_NO']]['QTY_PCS'] += $val['OFFER_QNTY']*$val['RATIO'];
			$quotation_qty_array[$val['JOB_NO']]['COSTING_PER_ID'] += $val['COSTING_PER_ID'];
			$quotation_id_array[$val['QUOTATION_ID']] = $val['QUOTATION_ID'];
			$all_jobs_array[$val['JOB_NO']] = $val['JOB_NO'];
			$jobs_wise_quot_array[$val['JOB_NO']] = $val['QUOTATION_ID'];

			$quot_wise_arr[$val[csf("quotation_id")]]['offer_qnty']=$val[csf("offer_qnty")];
			$quot_wise_arr[$val[csf("quotation_id")]]['costing_per_id']=$val[csf("costing_per_id")];

			$style_wise_arr[$val[csf("job_no")]]['costing_per']=$val[csf("costing_per")];
			$style_wise_arr[$val[csf("job_no")]]['gmts_item_id']=$val[csf("gmts_item_id")];
			$style_wise_arr[$val[csf("job_no")]]['sew_smv']=$val[csf("sew_smv")];
			$style_wise_arr[$val[csf("job_no")]]['sew_effi_percent']=$val[csf("sew_effi_percent")];
			$style_wise_arr[$val[csf("job_no")]]['shipment_date'].=$val[csf('est_ship_date')].',';
			$style_wise_arr[$val[csf("job_no")]]['quotation_id']=$val[csf("quotation_id")];
			$style_wise_arr[$val[csf("job_no")]]['buyer_name']=$val[csf("buyer_id")];
			$offer_qnty_pcs=$val[csf('offer_qnty')]*$val[csf('ratio')];
			$style_wise_arr[$val[csf("job_no")]]['qty_pcs']+=$val[csf('offer_qnty')]*$val[csf('ratio')];
			$style_wise_arr[$val[csf("job_no")]]['qty']+=$val[csf('offer_qnty')];
			$style_wise_arr[$val[csf("job_no")]]['final_cost_pcs']+=$val[csf('price_with_commn_pcs')];
			$style_wise_arr[$val[csf("job_no")]]['total_cost']+=$offer_qnty_pcs*$val[csf('price_with_commn_pcs')];
		}	
		$all_quot_id = implode(",", $quotation_id_array);
		
		// print_r($style_wise_arr);die();
		// ===============================================================================
		$sql_fab = "SELECT a.quotation_id,sum(a.avg_cons) as cons_qnty, sum(a.amount) as amount,b.job_no from wo_pri_quo_fabric_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.fabric_source=2 and a.status_active=1 and b.status_active=1 group by  a.quotation_id,b.job_no"; 
		// echo $sql_fab;die();
		$data_array_fab=sql_select($sql_fab);
		foreach($data_array_fab as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$fab_order_price_per_dzn=12;}
			else if($costing_per_id==2){$fab_order_price_per_dzn=1;}
			else if($costing_per_id==3){$fab_order_price_per_dzn=24;}
			else if($costing_per_id==4){$fab_order_price_per_dzn=36;}
			else if($costing_per_id==5){$fab_order_price_per_dzn=48;}

			$fab_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn']+=$row[csf("amount")];
			$fab_summary_data[$row[csf("job_no")]]['fab_amount_total_value']+=($row[csf("amount")]/$fab_order_price_per_dzn)*$fab_order_job_qnty;
			//$yarn_amount_dzn+=$row[csf('amount')];
		}
		// ==================================================================================
		$sql_yarn = "SELECT a.quotation_id,sum(a.cons_qnty) as cons_qnty, sum(a.amount) as amount,b.job_no from wo_pri_quo_fab_yarn_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by  a.quotation_id,b.job_no"; 
		// echo $sql_yarn;die();
		$data_array_yarn=sql_select($sql_yarn);
		foreach($data_array_yarn as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$yarn_order_price_per_dzn=12;}
			else if($costing_per_id==2){$yarn_order_price_per_dzn=1;}
			else if($costing_per_id==3){$yarn_order_price_per_dzn=24;}
			else if($costing_per_id==4){$yarn_order_price_per_dzn=36;}
			else if($costing_per_id==5){$yarn_order_price_per_dzn=48;}
			$yarn_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			//$yarn_summary_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
			 $yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn']+=$row[csf("amount")];
			// $summary_data['yarn_amount_dzn']+=$yarn_summary_dzn;
			 //$yarn_summary_data['yarn_amount_total_value']+=($row[csf("amount")]/$yarn_order_price_per_dzn)*$yarn_order_job_qnty;
		}
		// ===================================================================================
		$conversion_cost_arr=array();
		$sql_conversion = "SELECT a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition,c.job_no
		from wo_po_details_master c, wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
		where a.quotation_id in($all_quot_id) and a.quotation_id=c.quotation_id and a.status_active=1  ";
		// echo $sql_conversion;die();
		$data_array_conversion=sql_select($sql_conversion);
		foreach($data_array_conversion as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$conv_order_price_per_dzn=12;}
			else if($costing_per_id==2){$conv_order_price_per_dzn=1;}
			else if($costing_per_id==3){$conv_order_price_per_dzn=24;}
			else if($costing_per_id==4){$conv_order_price_per_dzn=36;}
			else if($costing_per_id==5){$conv_order_price_per_dzn=48;}
			$conv_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn']+=$row[csf("amount")];

			$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_dzn']+=$row[csf('amount')];
			$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$conv_order_job_qnty;
		}
		// print_r($conversion_cost_arr);die();
		if($db_type==0)
		{
			$sql = "SELECT MAX(a.id),a.quotation_id,a.fabric_cost,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,a.offer_qnty,b.job_no
			from wo_price_quotation_costing_mst a,wo_po_details_master b
			where a.quotation_id in($all_quot_id) and a.status_active=1 and a.quotation_id=b.quotation_id and b.status_active=1 ";
		}
		if($db_type==2)
		{
			$sql = "SELECT MAX(a.id),a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no
			from wo_price_quotation_costing_mst a,wo_po_details_master b
			where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and b.status_active=1   group by a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no";
		}
		// echo $sql;die();
		$data_array=sql_select($sql);

        foreach( $data_array as $row )
        {
			//$sl=$sl+1;
			if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
			else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
			else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
			else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
			else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn']=$order_price_per_dzn;
			$price_dzn=$row[csf("confirm_price_dzn")];
			$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
			$summary_data[$row[csf('job_no')]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
			$summary_data[$row[csf('job_no')]]['price_with_total_value']+=($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['price_dzn']+=$row[csf("confirm_price_dzn")];
		    $summary_data[$row[csf('job_no')]]['commission_dzn']+=$row[csf("commission")];
			$summary_data[$row[csf('job_no')]]['commission_total_value']+=($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['trims_cost_dzn']+=$row[csf("trims_cost")];
			$summary_data[$row[csf('job_no')]]['trims_cost_total_value']+=($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['embel_cost_dzn']+=$row[csf("embel_cost")];
			$summary_data[$row[csf('job_no')]]['embel_cost_total_value']+=($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty;
			//$row[csf("commission")]
			$other_direct_expenses=$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];

			$summary_data[$row[csf('job_no')]]['other_direct_dzn']+=$other_direct_expenses;
			$summary_data[$row[csf('job_no')]]['other_direct_total_value']+=($other_direct_expenses/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['wash_cost_dzn']+=$row[csf("wash_cost")];
			$summary_data[$row[csf('job_no')]]['wash_cost_total_value']+=($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['lab_test_dzn']+=$row[csf("lab_test")];
			$summary_data[$row[csf('job_no')]]['lab_test_total_value']+=($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['inspection_dzn']+=$row[csf("inspection")];
			$summary_data[$row[csf('job_no')]]['inspection_total_value']+=($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['freight_dzn']+=$row[csf("freight")];
			$summary_data[$row[csf('job_no')]]['freight_total_value']+=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
			$freight_cost_data[$row[csf("job_no")]]['freight_total_value']=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['currier_pre_cost_dzn']+=$row[csf("currier_pre_cost")];
			$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']+=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['certificate_pre_cost_dzn']+=$row[csf("certificate_pre_cost")];
			$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']+=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['design_pre_cost_dzn']+=$row[csf("design_pre_cost")];
			$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value']+=($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['studio_pre_cost_dzn']+=$row[csf("studio_pre_cost")];
			$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']+=($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['studio_dzn_cost']=$row[csf("studio_percent")];
			$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['common_oh']=$row[csf("common_oh")];

			$fab_amount_dzn=$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn'];
			$summary_data[$row[csf('job_no')]]['fab_amount_dzn']+=$fab_amount_dzn;
			$summary_data[$row[csf('job_no')]]['fab_amount_total_value']+=($fab_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
			$yarn_amount_dzn=$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn'];
			//echo ($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty.'d';
			$summary_data[$row[csf('job_no')]]['yarn_amount_dzn']+=$yarn_amount_dzn;
			$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']+=($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
			$conv_amount_dzn=$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn'];
			$summary_data[$row[csf('job_no')]]['conversion_cost_dzn']+=$conv_amount_dzn;
			$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']+=($conv_amount_dzn/$order_price_per_dzn)*$order_job_qnty;

			//$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
			$net_value_dzn=$row[csf("price_with_commn_dzn")];

			$summary_data[$row[csf('job_no')]]['netfobvalue_dzn']+=($row[csf("price_with_commn_dzn")]);
			$summary_data[$row[csf('job_no')]]['netfobvalue']+=(($row[csf("price_with_commn_dzn")])/$order_price_per_dzn)*$order_job_qnty;

			//yarn_amount_total_value
			$all_cost_dzn=$yarn_amount_dzn+$fab_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses;
			//echo $yarn_amount_dzn.'Y='.$fab_amount_dzn.'F='.$conv_amount_dzn.'Cnv='.$row[csf("trims_cost")].'Tr='.$row[csf("embel_cost")].'Em='.$other_direct_expenses;
			$summary_data[$row[csf('job_no')]]['cost_of_material_service']+=$all_cost_dzn;
			$summary_data[$row[csf('job_no')]]['cost_of_material_service_total_value']+=($all_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
			$contribute_netfob_value_dzn=$net_value_dzn-($fab_amount_dzn+$yarn_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses);
			$summary_data[$row[csf('job_no')]]['contribution_margin_dzn']+=$contribute_netfob_value_dzn;
			$summary_data[$row[csf('job_no')]]['contribution_margin_total_value']+=(($contribute_netfob_value_dzn)/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['cm_cost_dzn']+=$row[csf("cm_cost")];
			$summary_data[$row[csf('job_no')]]['cm_cost_total_value']+=($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['comm_cost_dzn']+=$row[csf("comm_cost")];
			$summary_data[$row[csf('job_no')]]['comm_cost_total_value']+=($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['common_oh_dzn']+=$row[csf("common_oh")];
			$summary_data[$row[csf('job_no')]]['common_oh_total_value']+=($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty;
			//echo $netfob_value_dzn.'='.$row[csf("cm_cost")];
			$Contribution_Margin=$netfob_value_dzn-$LessCostOfMaterialServices;
			$tot_gross_profit_dzn=$contribute_netfob_value_dzn-$row[csf("cm_cost")];
			$summary_data[$row[csf('job_no')]]['gross_profit_dzn']+=$tot_gross_profit_dzn;
			$summary_data[$row[csf('job_no')]]['gross_profit_total_value']+=(($tot_gross_profit_dzn)/$order_price_per_dzn)*$order_job_qnty;

			//$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
			$operate_profit_loss_dzn=$tot_gross_profit_dzn;//-($row[csf("comm_cost")]+$row[csf("common_oh")]);
			$summary_data[$row[csf('job_no')]]['operating_profit_loss_dzn']+=$operate_profit_loss_dzn;
			$summary_data[$row[csf('job_no')]]['operating_profit_loss_total_value']+=($operate_profit_loss_dzn/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_dzn']+=$row[csf("depr_amor_pre_cost")];
			$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_total_value']+=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['interest_pre_cost_dzn']+=$row[csf("interest_pre_cost")];
			$summary_data[$row[csf('job_no')]]['interest_pre_cost_total_value']+=($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_dzn']+=$row[csf("income_tax_pre_cost")];
			$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_total_value']+=($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$net_profit_dzn=$operate_profit_loss_dzn-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);
			$summary_data[$row[csf('job_no')]]['net_profit_dzn']+=$net_profit_dzn;
			$summary_data[$row[csf('job_no')]]['net_profit_dzn_total_value']+=($net_profit_dzn/$order_price_per_dzn)*$order_job_qnty;
		}
		// echo "<pre>";
		// print_r($summary_data);
		// die();
		//======================================================================

		$sql_commi = "SELECT a.id,a.quotation_id,a.particulars_id,a.commission_base_id,a.commision_rate,a.commission_amount,a.status_active,b.job_no
		from  wo_pri_quo_commiss_cost_dtls a,wo_po_details_master b
		where  a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and a.commission_amount>0 and b.status_active=1";
		// echo $sql_commi;die();
		$result_commi=sql_select($sql_commi);
		$CommiData_foreign_cost=$CommiData_lc_cost=$foreign_dzn_commission_amount=$local_dzn_commission_amount=0;
		foreach($result_commi as $row)
		{
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];

			if($row[csf("particulars_id")]==1) //Foreign
			{
				$CommiData_foreign_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
				$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
				$CommiData_foreign_quot_cost_arr[$row[csf("job_no")]]+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
			else
			{
				$CommiData_lc_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
				$local_dzn_commission_amount+=$row[csf("commission_amount")];
				$commision_local_quot_cost_arr[$row[csf("job_no")]]=$row[csf("commision_rate")];
			}
		}
		//=====================================================================================
		$sql_comm="SELECT a.item_id,a.quotation_id,sum(a.amount) as amount,b.job_no from wo_pri_quo_comarcial_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by a.quotation_id,a.item_id,b.job_no";
		// echo $sql_comm;die();
		$tot_lc_dzn_Commer=$tot_without_lc_dzn_Commer=0;
		// $summary_data['comm_cost_dzn']=0;
		// $summary_data['comm_cost_total_value']=0;
		$result_comm=sql_select($sql_comm);
		$commer_lc_cost = array();
		$commer_without_lc_cost = array();
		foreach($result_comm as $row)
		{
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
			//$summary_data['comm_cost_dzn']=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			$comm_amtPri=$row[csf('amount')];
			$item_id=$row[csf('item_id')];
			if($item_id==1)//LC
			{
				$commer_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;

				$commer_lc_cost_quot_arr[$row[csf("job_no")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
			else
			{
				$commer_without_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
		}
		// echo "<pre>";print_r($summary_data);die();
		/********************************************************************************************************
		*																										*
		*													END													*	
		*																										*
		********************************************************************************************************/

		// =========================================== FOR ROWSPAN ===============================================
		$buyer_wise_cm_fob_calculate = array();
		$rowspan_arr = array();
		$chk_buyer  = array();
		foreach ($production_main_array as $style_key => $style_arr) 
		{
			foreach ($style_arr as $job_key => $job_arr) 
			{
				foreach ($job_arr as $po_key => $po_arr) 
				{
					foreach ($po_arr as $item_key => $item_arr) 
					{
						foreach ($item_arr as $color_key => $row) 
						{
							$rowspan_arr[$style_key][$job_key][$po_key][$item_key]++;

							$buyer_booking_no_fin_qnty_array[$row['buyer_name']]+=$booking_no_fin_qnty_array[$po_key][$item_key][$color_key];
							$buyer_issue_qnty_arr[$row['buyer_name']][18] += $issue_qnty_arr[$po_key][$color_key][18];
							$buyer_issue_qnty_arr[$row['buyer_name']][37] += $issue_qnty_arr[$po_key][$color_key][37];

							// calculate summary cm and fob
							$today_sewing_output_qty = $cutting_sewing_data[$job_key][$style_key][$po_key][$item_key][$color_key]["today_sewing_output"];
							$total_sewing_output_qty = $cutting_sewing_data[$job_key][$style_key][$po_key][$item_key][$color_key]["total_sewing_output"];
							$today_ex_fac_qty=$ex_factory_arr[$po_key][$item_key][$color_key]['today_ex_fac'];
							$total_ex_fac_qty=$ex_factory_arr[$po_key][$item_key][$color_key]['total_ex_fac'];

							$tot_bat_qty = $total_sewing_output_qty - $total_ex_fac_qty;
							$buyer_wise_ex_factory_arr[$row['buyer_name']]['total_ex_fac']+=$total_ex_fac_qty;
							$buyer_wise_ex_factory_arr[$row['buyer_name']]['total_bal_qty']+= $tot_bat_qty;
							$buyer_wise_ex_factory_arr[$row['buyer_name']]['total_fob_qty']+= ($tot_bat_qty*$row['unit_price']);

							$unit_price = $row['unit_price'];
							$today_fob_val = $today_sewing_output_qty * $unit_price;
							$total_fob_val = $total_sewing_output_qty * $unit_price;
							$today_exf_fob_val = $today_ex_fac_qty * $unit_price;
							$total_exf_fob_val = $total_ex_fac_qty * $unit_price;

							$costing_per=$costing_per_arr[$job_key];
							if($costing_per==1) $dzn_qnty=12;
							else if($costing_per==3) $dzn_qnty=12*2;
							else if($costing_per==4) $dzn_qnty=12*3;
							else if($costing_per==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;	
							$dzn_qnty=$dzn_qnty*$row['total_set_qnty'];
							$cm_gmt_cost_dzn=$cm_gmt_cost_dzn_arr_new[$po_key]['dzn'];
							//echo $cm_gmt_cost_dzn.'DD'.$po_id.', ';
							//$cm_per_pcs=(($unit_price*$dzn_qnty)-$total_cost_arr[$job_id])+$cm_cost_arr[$job_id];
							$cm_per_pcs=$cm_gmt_cost_dzn/$dzn_qnty;
							$today_cm_val = $today_sewing_output_qty * $cm_per_pcs;
							$total_cm_val = $total_sewing_output_qty * $cm_per_pcs;

							$today_exf_cm_val = $today_ex_fac_qty * $cm_per_pcs;
							$total_exf_cm_val = $total_ex_fac_qty * $cm_per_pcs;
							/*========================================================================================
							*																						  *
							*								Calculate cm valu 										  *	
							*																					  	  *	
							*========================================================================================*/
							$tot_dye_chemi_process_amount 	= $conversion_cost_arr[$job_key][101]['conv_amount_total_value'];
							$tot_yarn_dye_process_amount 	= $conversion_cost_arr[$job_key][30]['conv_amount_total_value'];
							$tot_aop_process_amount 		= $conversion_cost_arr[$job_key][35]['conv_amount_total_value'];
							
							$total_quot_qty=$total_quot_pcs_qty=$total_quot_amount=$total_sew_smv=$total_quot_amount_cal=0;
							$all_last_shipdates='';

				            foreach($style_wise_arr as $style_key=>$val)
							{	
								$total_cost=$val[('qty')]*$val[('final_cost_pcs')];
								$total_quot_qty+=$val[('qty')];
								$total_quot_pcs_qty+=$val[('qty_pcs')];
								$total_sew_smv+=$val[('sew_smv')];
								$total_quot_amount+=$total_cost;
								$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
							}
							$total_quot_amount_cal = $style_wise_arr[$job_key]['qty']*$style_wise_arr[$job_key]['final_cost_pcs'];
							$tot_cm_for_fab_cost=$summary_data[$job_key]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
							// echo $job_key."==".$summary_data[$job_key]['conversion_cost_total_value']."-(".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$tot_aop_process_amount.")<br>";
							$commision_quot_local=$commision_local_quot_cost_arr[$job_key];
							$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$job_key]+$commer_lc_cost_quot_arr[$job_key]+$freight_cost_data[$job_key]['freight_total_value']);
							$tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
							$tot_inspect_cour_certi_cost=$summary_data[$job_key]['inspection_total_value']+$summary_data[$job_key]['currier_pre_cost_total_value']+$summary_data[$job_key]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$job_key]['design_pre_cost_total_value'];
							// echo $summary_data[$job_key]['inspection_total_value']."+".$summary_data[$job_key]['currier_pre_cost_total_value']."+".$summary_data[$job_key]['certificate_pre_cost_total_value']."+".$tot_sum_amount_quot_calccc."+".$summary_data[$job_key]['design_pre_cost_total_value']."<br>";

							$tot_emblish_cost=$summary_data[$job_key]['embel_cost_total_value'];
							$pri_freight_cost_per=$summary_data[$job_key]['freight_total_value'];
							$pri_commercial_per=$commer_lc_cost[$job_key];
							$CommiData_foreign_cost=$CommiData_foreign_quot_cost_arr[$job_key];

							$total_btb=$summary_data[$job_key]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$job_key]['comm_cost_total_value']+$summary_data[$job_key]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$job_key]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$job_key]['common_oh_total_value']+$summary_data[$job_key]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
							// echo $summary_data[$job_key]['lab_test_total_value']."+".$tot_emblish_cost."+".$summary_data[$job_key]['comm_cost_total_value']."+".$summary_data[$job_key]['trims_cost_total_value']."+".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$summary_data[$job_key]['yarn_amount_total_value']."+".$tot_aop_process_amount."+".$summary_data[$job_key]['common_oh_total_value']."+".$summary_data[$job_key]['studio_pre_cost_total_value']."+".$tot_inspect_cour_certi_cost."<br>";
							$tot_quot_sum_amount=$total_quot_amount-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
							// echo $total_quot_amount."-(".$CommiData_foreign_cost."+".$pri_freight_cost_per."+".$pri_commercial_per.")<br>";
							$NetFOBValue_job = $tot_quot_sum_amount;
							// echo $NetFOBValue_job."<br>";
							$total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);

							$total_quot_pcs_qty = $quotation_qty_array[$job_key]['QTY_PCS'];
							// echo $total_cm_for_gmt;echo "<br>";
							$cm_valu_lc = 0;
							$cm_valu_lc = $total_cm_for_gmt/$total_quot_pcs_qty;
							$today_cm_val_lc = $today_sewing_output_qty * $cm_valu_lc;
							$total_cm_val_lc = $total_sewing_output_qty * $cm_valu_lc;

							$today_exf_cm_val_lc = $today_ex_fac_qty * $cm_valu_lc;
							$total_exf_cm_val_lc = $total_ex_fac_qty * $cm_valu_lc;
							// echo number_format(($total_cm_for_gmt/$total_quot_pcs_qty)*12,2); echo "<br>";
							/*========================================================================================
							*																						  *
							*											END											  *	
							*																					  	  *	
							*========================================================================================*/			

							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_cm_val'] 		+= $today_cm_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_cm_val'] 		+= $total_cm_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_cm_val_lc']		+= $today_cm_val_lc;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_cm_val_lc'] 	+= $total_cm_val_lc;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_fob_val'] 		+= $today_fob_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_fob_val'] 		+= $total_fob_val;

							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_exf_cm_val'] 	+= $today_exf_cm_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_exf_cm_val'] 	+= $total_exf_cm_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_exf_cm_val_lc']	+= $today_exf_cm_val_lc;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_exf_cm_val_lc']	+= $total_exf_cm_val_lc;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_exf_fob_val'] 	+= $today_exf_fob_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_exf_fob_val'] 	+= $total_exf_fob_val;
						}
					}
				}
			}
		}
		// echo "<pre>";
		// print_r($rowspan_arr);
		// echo "</pre>";
		
		
		ob_start();	
		$summary_html = '';
					 
        $summary_html.='<div style="padding: 5px 10px;">
        <div id="summary_part">
        	<table width="3300" cellspacing="0" >
        		<tr style="border:none;">
        			<td colspan="30" align="center" style="border:none; font-size:24px;">
        				<strong>';?><? $comp_names=""; 
        					foreach(explode(",",$working_company_id) as $vals) 
        					{
        						$comp_names.=($comp_names !="") ? ' , '.$company_library[$vals] : $company_library[$vals];
        					}
        					$summary_html.=$comp_names.'
        					
        				</strong>                                
        			</td>
        		</tr>
        		<tr class="form_caption" style="border:none;">
        			<td colspan="30" align="center" style="border:none;font-size:14px; font-weight:bold" ><strong>
        				Floor wise Daily RMG Production Report
        			</strong></td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="30" align="center" style="border:none;font-size:14px; font-weight:bold" >'?>
        				<?
        				$dates=str_replace("'","",trim($txt_production_date));
        				if($dates)
        				{
        					$summary_html.='Date '.change_date_format($dates)  ;
        				}?>
        				
        			<? $summary_html.='</td>
        		</tr>
        	</table>';?>
        	<!-- =========================================== SUMMARY PART START ====================================== -->
        	<? $summary_html.='<div style="margin-bottom: 20px;">
        		<table width="2685" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="" align="left">
        			<caption style="text-align: center;font-weight: bold;font-size: 18px;">Summary Part</caption>
					<thead>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30" ><p>SL</p>
							</th>

							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Buyer</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="70"><p>Order Qty</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Fabric Status</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Cut Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Cutting QC</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Delivery To Print</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Receive from Print</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Delivery To Embroidery</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Receive from Embroidery</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Input Status</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Sewing Output</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Finishing</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Carton</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Ex-Factory</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="115"><p>Sewing - Exfactory Balance</p></th>
						</tr>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>F.Fab. Req.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab. Rcv.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab. Issue</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab.Inhand</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Cut %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Cutting %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Print WIP</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Embroidery WIP</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60" title="(Total Sewing Input / Total Cut Status)*100"><p>Input %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Sewing %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Finish %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="110"><p>Total</p></th>
						
						</tr>
						<tbody>';?>
						<?
						$sl=1;
						$buyer_total_order_qty 			= 0;
						$buyer_total_fin_fab_req 		= 0;
						$buyer_total_fab_rcv 			= 0;
						$buyer_total_fav_issue 			= 0;
						$buyer_total_fab_inhand 		= 0;
						$buyer_total_today_cut 			= 0;
						$buyer_total_cut 				= 0;
						$buyer_total_today_cutting 		= 0;
						$buyer_total_cutting 			= 0;
						$buyer_total_today_print_issue 	= 0;
						$buyer_total_print_issue 		= 0;
						$buyer_total_today_print_rcv 	= 0;
						$buyer_total_print_rcv 			= 0;
						$buyer_total_print_rej 			= 0;
						$buyer_total_today_embroidery_issue 	= 0;
						$buyer_total_embroidery_issue 		= 0;
						$buyer_total_embroidery_print_rcv 	= 0;
						$buyer_total_embroidery_rcv 			= 0;
						$buyer_total_embroidery_rej 			= 0;
						$buyer_total_today_input 		= 0;
						$buyer_total_input 				= 0;
						$buyer_total_today_output 		= 0;
						$buyer_total_output 			= 0;
						$buyer_total_today_fin 			= 0;
						$buyer_total_fin 				= 0;
						$buyer_total_today_carton 		= 0;
						$buyer_total_carton 			= 0;
						$buyer_total_today_exfact 		= 0;
						$buyer_total_exfact 			= 0;
						$buyer_total_today_export_cm 	= 0;
						$buyer_total_export_cm 			= 0;
						$buyer_total_today_export_cm_lc	= 0;
						$buyer_total_export_cm_lc		= 0;
						$buyer_total_today_export_fob 	= 0;
						$buyer_total_export_fob 		= 0;
						$buyer_total_today_sewing_cm 	= 0;
						$buyer_total_sewing_cm 			= 0;
						$buyer_total_today_sewing_cm_lc	= 0;
						$buyer_total_sewing_cm_lc		= 0;
						$buyer_total_today_sewing_fob 	= 0;
						$buyer_total_sewing_fob 		= 0;
						$buyer_total_sewing_ex_bal_qty	= 0;
						$buyer_total_sewing_ex_fob_qty	= 0;
						foreach ($buyer_wise_summary_array as $key => $val) 
						{
							$smry_order_quantity 		= $buyer_wise_order_qnty_array[$key];
							$smry_fab_required			= $buyer_booking_no_fin_qnty_array[$key];
							$smry_issue_qty 			= $buyer_issue_qnty_arr[$key][18];
							$smry_receive_qty 			= $buyer_issue_qnty_arr[$key][37];
							$smry_fab_in_hand 			= $smry_receive_qty-$smry_issue_qty;

							$smry_today_cut 			= $buyer_wise_lay_qnty_array[$key]['today_lay'];
							$smry_totalCut 				= $buyer_wise_lay_qnty_array[$key]['total_lay'];
							$smry_cutting_lay_percent 	= ($smry_totalCut / $smry_order_quantity)*100;

							// $smry_today_qc 				= $buyer_wise_qc_qnty_array[$key]['today_qc'];
							// $smry_totalQc 				= $buyer_wise_qc_qnty_array[$key]['total_qc'];
							// $smry_totalRej 				= $buyer_wise_qc_qnty_array[$key]['total_rej'];
							// $smry_cutting_qc_percent 	= ($smry_totalCut) ? ($smry_totalQc / $smry_totalCut)*100 : 0;

							$smry_today_qc 		= $buyer_wise_cutting_sewing_data[$key]["today_cutting"];	
							$smry_totalQc 		= $buyer_wise_cutting_sewing_data[$key]["total_cutting"];
							$smry_totalRej 		= $buyer_wise_cutting_sewing_data[$key]["cut_reject_qty"];
							$smry_cutting_qc_percent 	= ($smry_totalCut) ? ($smry_totalQc / $smry_totalCut)*100 : 0;
								
							$smry_today_issue_to_print 	= $buyer_wise_cutting_sewing_data[$key]["today_issue_to_print"];	
							$smry_total_issue_to_print 	= $buyer_wise_cutting_sewing_data[$key]["total_issue_to_print"];	
							$smry_today_rcv_frm_print 	= $buyer_wise_cutting_sewing_data[$key]["today_rcv_frm_print"];
							$smry_total_rcv_frm_print 	= $buyer_wise_cutting_sewing_data[$key]["total_rcv_frm_print"];	
							$smry_print_reject 			= $buyer_wise_cutting_sewing_data[$key]["print_reject"];
							$smry_print_wip 			= $smry_total_issue_to_print - $smry_total_rcv_frm_print;	
							$smry_today_issue_to_embroidery 	= $buyer_wise_cutting_sewing_data[$key]["today_issue_to_embroidery"];	
							$smry_total_issue_to_embroidery 	= $buyer_wise_cutting_sewing_data[$key]["total_issue_to_embroidery"];	
							$smry_today_rcv_frm_embroidery 	= $buyer_wise_cutting_sewing_data[$key]["today_rcv_frm_embroidery"];
							$smry_total_rcv_frm_embroidery 	= $buyer_wise_cutting_sewing_data[$key]["total_rcv_frm_embroidery"];	
							$smry_embroidery_reject 			= $buyer_wise_cutting_sewing_data[$key]["reject_embroidery"];
							$smry_embroidery_wip 			= $smry_total_issue_to_embroidery - $smry_total_rcv_frm_embroidery; 	
							$smry_today_sewing_input 	= $buyer_wise_cutting_sewing_data[$key]["today_sewing_input"];
							$smry_total_sewing_input 	= $buyer_wise_cutting_sewing_data[$key]["total_sewing_input"];
							$smry_input_percent 		= ($smry_totalCut) ? ($smry_total_sewing_input / $smry_totalCut)*100 : 0;	
							$smry_today_sewing_output 	= $buyer_wise_cutting_sewing_data[$key]["today_sewing_output"];
							$smry_total_sewing_output 	= $buyer_wise_cutting_sewing_data[$key]["total_sewing_output"];	
							$smry_output_percent 		= ($smry_total_sewing_input) ? ($smry_total_sewing_output / $smry_total_sewing_input)*100 : 0;
							$smry_today_finishing 		= $buyer_wise_cutting_sewing_data[$key]["today_finishing"];
							$smry_total_finishing 		= $buyer_wise_cutting_sewing_data[$key]["total_finishing"];
							$smry_finishing_percent 	= ($smry_total_sewing_output) ? ($smry_total_finishing / $smry_total_sewing_output)*100 : 0;
							$smry_today_carton_qty 		= $buyer_wise_cutting_sewing_data[$key]["today_carton_qty"];
							$smry_total_carton_qty 		= $buyer_wise_cutting_sewing_data[$key]["total_carton_qty"];

							$smry_today_ex_fact_qty 	= $buyer_wise_ex_factory_arr[$key]["today_ex_fac"];
							$smry_total_ex_fac_qty 		= $buyer_wise_ex_factory_arr[$key]["total_ex_fac"];

							$smry_total_ex_fac_bal_qty 	= $buyer_wise_ex_factory_arr[$key]["total_bal_qty"];
							$smry_total_ex_fac_fob_qty 	= $buyer_wise_ex_factory_arr[$key]["total_fob_qty"];

							// $smry_total_ex_fac_bal_qty 	= $smry_total_sewing_output - $smry_total_ex_fac_qty;
							// $smry_total_ex_fac_fob_qty 	= $smry_total_ex_fac_bal_qty * $val['unit_price'];

							$smry_today_fob_val 		= $buyer_wise_cm_fob_calculate[$key]['today_fob_val'];
							$smry_total_fob_val 		= $buyer_wise_cm_fob_calculate[$key]['total_fob_val'];
							$smry_today_cm_val 			= $buyer_wise_cm_fob_calculate[$key]['today_cm_val'];
							$smry_total_cm_val 			= $buyer_wise_cm_fob_calculate[$key]['total_cm_val'];
							$smry_today_cm_val_lc		= $buyer_wise_cm_fob_calculate[$key]['today_cm_val_lc'];
							$smry_total_cm_val_lc		= $buyer_wise_cm_fob_calculate[$key]['total_cm_val_lc'];


							$smry_today_exf_cm_val 		= $buyer_wise_cm_fob_calculate[$key]['today_exf_cm_val'];
							$smry_total_exf_cm_val 		= $buyer_wise_cm_fob_calculate[$key]['total_exf_cm_val'];
							$smry_today_exf_cm_val_lc	= $buyer_wise_cm_fob_calculate[$key]['today_exf_cm_val_lc'];
							$smry_total_exf_cm_val_lc	= $buyer_wise_cm_fob_calculate[$key]['total_exf_cm_val_lc'];
							$smry_today_exf_fob_val 	= $buyer_wise_cm_fob_calculate[$key]['today_exf_fob_val'];
							$smry_total_exf_fob_val 	= $buyer_wise_cm_fob_calculate[$key]['total_exf_fob_val'];

							// =========================================
							$buyer_total_order_qty 			+= $smry_order_quantity;
							$buyer_total_fin_fab_req 		+= $smry_fab_required;
							$buyer_total_fab_rcv 			+= $smry_receive_qty;
							$buyer_total_fav_issue 			+= $smry_issue_qty;
							$buyer_total_fab_inhand 		+= $smry_fab_in_hand;
							$buyer_total_today_cut 			+= $smry_today_cut;
							$buyer_total_cut 				+= $smry_totalCut;
							$buyer_total_cut_prsnt 			+= $smry_cutting_lay_percent;
							$buyer_total_today_cutting 		+= $smry_today_qc;
							$buyer_total_cutting 			+= $smry_totalQc;
							$buyer_cutting_percent 			+= $smry_cutting_qc_percent;
							$buyer_cutting_reject 			+= $smry_totalRej;
							$buyer_total_today_print_issue 	+= $smry_today_issue_to_print;
							$buyer_total_print_issue 		+= $smry_total_issue_to_print;
							$buyer_total_today_print_rcv 	+= $smry_today_rcv_frm_print;
							$buyer_total_print_rcv 			+= $smry_total_rcv_frm_print;
							$buyer_total_print_rej 			+= $smry_print_reject;
							$buyer_total_print_wip 			+= $smry_print_wip;
							$buyer_total_today_embroidery_issue 	+= $smry_today_issue_to_embroidery;
							$buyer_total_embroidery_issue 		+= $smry_total_issue_to_embroidery;
							$buyer_total_today_embroidery_rcv 	+= $smry_today_rcv_frm_embroidery;
							$buyer_total_embroidery_rcv 			+= $smry_total_rcv_frm_embroidery;
							$buyer_total_embroidery_rej 			+= $smry_embroidery_reject ;
							$buyer_total_embroidery_wip 			+= $smry_embroidery_wip;
							
							$buyer_total_today_input 		+= $smry_today_sewing_input;
							$buyer_total_input 				+= $smry_total_sewing_input;
							$buyer_input_prsent				+= $smry_input_percent;
							$buyer_total_today_output 		+= $smry_today_sewing_output;
							$buyer_total_output 			+= $smry_total_sewing_output;
							$buyer_output_percent 			+= $smry_output_percent;
							$buyer_total_today_fin 			+= $smry_today_finishing;
							$buyer_total_fin 				+= $smry_total_finishing;
							$buyer_finishing_percent 		+= $smry_finishing_percent;
							$buyer_total_today_carton 		+= $smry_today_carton_qty;
							$buyer_total_carton 			+= $smry_total_carton_qty;
							$buyer_total_today_exfact 		+= $smry_today_ex_fact_qty;
							$buyer_total_exfact 			+= $smry_total_ex_fac_qty;
							$buyer_total_today_export_cm 	+= $smry_today_exf_cm_val;
							$buyer_total_export_cm 			+= $smry_total_exf_cm_val;
							$buyer_total_today_export_cm_lc	+= $smry_today_exf_cm_val_lc;
							$buyer_total_export_cm_lc		+= $smry_total_exf_cm_val_lc;
							$buyer_total_today_export_fob 	+= $smry_today_exf_fob_val;
							$buyer_total_export_fob 		+= $smry_total_exf_fob_val;
							$buyer_total_today_sewing_cm 	+= $smry_today_cm_val;
							$buyer_total_sewing_cm 			+= $smry_total_cm_val;
							$buyer_total_today_sewing_cm_lc	+= $smry_today_cm_val_lc;
							$buyer_total_sewing_cm_lc		+= $smry_total_cm_val_lc;
							$buyer_total_today_sewing_fob 	+= $smry_today_fob_val;
							$buyer_total_sewing_fob 		+= $smry_total_fob_val;
							$buyer_total_sewing_ex_bal_qty	+= $smry_total_ex_fac_bal_qty;
							$buyer_total_sewing_ex_fob_qty	+= $smry_total_ex_fac_fob_qty;
							
							$summary_html.='<tr>
								<td align="left" style="word-wrap: break-word;word-break: break-all;">'.$sl.'</td>
								<td align="left" style="word-wrap: break-word;word-break: break-all;">'.$buyer_library[$key].'</td>

								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_order_quantity,0).'</td>

								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_fab_required,2).'</td>

								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_receive_qty,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_issue_qty,2).'</td>

								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_fab_in_hand,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_cut.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_totalCut.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_cutting_lay_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_qc.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_totalQc.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_cutting_qc_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_totalRej.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_issue_to_print.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_issue_to_print.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_rcv_frm_print.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_rcv_frm_print.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_print_reject.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_print_wip.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_issue_to_embroidery.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_issue_to_embroidery.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'. $smry_today_rcv_frm_embroidery.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_rcv_frm_embroidery.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_embroidery_wip.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_print_wip.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_sewing_input.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_sewing_input.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_input_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_sewing_output.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_sewing_output.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_output_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_finishing.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_finishing.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_finishing_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_carton_qty.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_carton_qty.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_ex_fact_qty.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_ex_fac_qty.'</td>

								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_total_ex_fac_bal_qty,0).'</td>

								

							</tr>';?>
							<?
							$sl++;
						}
						?>
					<? $summary_html.='</tbody>        			
					<tfoot>
						<tr>
							<th align="right" colspan="2">Total </th>
							<th align="right">'.number_format($buyer_total_order_qty,0).'</th>
							<th align="right">'.number_format($buyer_total_fin_fab_req,2).'</th>
							<th align="right">'.number_format($buyer_total_fab_rcv,2).'</th>
							<th align="right">'.number_format($buyer_total_fav_issue,2).'</th>
							<th align="right">'.number_format($buyer_total_fab_inhand,2).'</th>
							<th align="right">'.number_format($buyer_total_today_cut,0).'</th>
							<th align="right">'.number_format($buyer_total_cut,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_total_today_cutting,0).'</th>
							<th align="right">'.number_format($buyer_total_cutting,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_cutting_reject,0).'</th>
							<th align="right">'.number_format($buyer_total_today_print_issue,0).'</th>
							<th align="right">'.number_format($buyer_total_print_issue,0).'</th>
							<th align="right">'.number_format($buyer_total_today_print_rcv,0).'</th>
							<th align="right">'.number_format($buyer_total_print_rcv,0).'</th>
							<th align="right">'.number_format($buyer_total_print_rej,0).'</th>
							<th align="right">'.number_format($buyer_total_print_wip,0).'</th>
							<th align="right">'.number_format($buyer_total_today_embroidery_issue,0).'</th>
							<th align="right">'.number_format($buyer_total_embroidery_issue,0).'</th>
							<th align="right">'.number_format($buyer_total_embroidery_print_rcv,0).'</th>
							<th align="right">'.number_format($buyer_total_embroidery_rcv,0).'</th>
							<th align="right">'.number_format($buyer_total_embroidery_rej,0).'</th>
							<th align="right">'.number_format($buyer_total_embroidery_wip,0).'</th>
							<th align="right">'.number_format($buyer_total_today_input,0).'</th>
							<th align="right">'.number_format($buyer_total_input,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_total_today_output,0).'</th>
							<th align="right">'.number_format($buyer_total_output,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_total_today_fin,0).'</th>
							<th align="right">'.number_format($buyer_total_fin,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_total_today_carton,0).'</th>
							<th align="right">'.number_format($buyer_total_carton,0).'</th>
							<th align="right">'.number_format($buyer_total_today_exfact,0).'</th>
							<th align="right">'.number_format($buyer_total_exfact,0).'</th>

							<th align="right">'.number_format($buyer_total_sewing_ex_bal_qty,0).'</th>
						</tr>
					</tfoot>
        		</table>
        	</div></div>';?>
        	<? echo $summary_html; ?>
        	<br clear="all">
        	<!-- ===================================== DETAILS PART START ===================================== -->
			<div>
				<table width="3500" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left">
					<caption style="text-align: center;font-weight: bold;font-size: 18px;">Details Part</caption>
					<thead>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30" ><p>SL</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Buyer</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Order No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Shipping Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30"><p>Img</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Style</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Internal Ref</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Floor Name</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Color</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="70"><p>Color Qty</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Fabric Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Cut Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Cutting QC</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Delivery To Print</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Receive from Print</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Delivery To Embroidery</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Receive from Embroidery</p></th>
							
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Input Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Sewing Output</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Finishing</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Carton</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Ex-Factory</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="105"><p>Sewing - Exfactory Balance</p></th>
						</tr>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>F.Fab. Req.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab. Rcv.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab. Issue</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab.Inhand</p></th>
							
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Cut %</p></th>
							
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Cutting %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Print WIP</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Embroidery WIP</p></th>


							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60" title="(Total Sewing Input / Total Cut Status)*100"><p>Input %</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Sewing %</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Finish %</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="105"><p>Total</p></th>
						</tr>					   
					</thead>
				</table>
				<div style="max-height:400px; overflow-y:scroll; width:3520px" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3500" rules="all" id="table_body" align="left">
					<?
					$k=1;
					$jj=1;	
					$gr_color_qty 			= 0;
					$gr_fab_req_qty 		= 0;
					$gr_fab_rcv_qty 		= 0;
					$gr_fab_iss_qty 		= 0;
					$gr_fab_inh_qty 		= 0;
					$gr_today_lay_qty 		= 0;
					$gr_total_lay_qty 		= 0;
					$gr_cutting_percent 	= 0;
					$gr_today_qc_qty 		= 0;
					$gr_total_qc_qty 		= 0;
					$gr_cutting_qc_percent 	= 0;
					$gr_cutting_qc_reject 	= 0;
					$gr_today_print_iss_qty = 0;
					$gr_total_print_iss_qty = 0;
					$gr_today_print_rcv_qty = 0;
					$gr_total_print_rcv_qty = 0;
					$gr_print_rej_qty 		= 0;
					$gr_print_wip_qty 		= 0;
					$gr_today_embr_iss_qty = 0;
					$gr_total_embr_iss_qty = 0;
					$gr_today_embr_rcv_qty = 0;
					$gr_total_embr_rcv_qty = 0;
					$gr_embr_rej_qty 		= 0;
					$gr_embr_wip_qty 		= 0;
					$gr_today_sew_in_qty 	= 0;
					$gr_total_sew_in_qty 	= 0;
					$gr_input_percent 		= 0;
					$gr_today_sew_out_qty 	= 0;
					$gr_total_sew_out_qty 	= 0;
					$gr_sewing_percent 		= 0;
					$gr_today_fin_qty 		= 0;
					$gr_total_fin_qty 		= 0;
					$gr_finish_percent 		= 0;
					$gr_today_ctn_qty 		= 0;
					$gr_total_ctn_qty 		= 0;
					$gr_today_exfact_qty 	= 0;
					$gr_total_exfact_qty 	= 0;
					$gr_today_exf_cm_qty 	= 0;
					$gr_total_exf_cm_qty 	= 0;
					$gr_today_exf_fob_qty 	= 0;
					$gr_total_exf_fob_qty 	= 0;
					$gr_today_sew_cm_qty 	= 0;
					$gr_total_sew_cm_qty 	= 0;
					$gr_today_fob_qty 		= 0;
					$gr_total_fob_qty 		= 0;
					$gr_total_ex_bal_qty 	= 0;
					$gr_total_ex_fob_qty 	= 0;
					
					foreach($production_main_array as $style_id=>$job_data)
					{				
						$style_wise_color_qty 			= 0;
						$style_wise_fab_req_qty 		= 0;
						$style_wise_fab_rcv_qty 		= 0;
						$style_wise_fab_iss_qty 		= 0;
						$style_wise_fab_inh_qty 		= 0;
						$style_wise_today_lay_qty 		= 0;
						$style_wise_total_lay_qty 		= 0;
						$style_wise_cutting_percent 	= 0;
						$style_wise_today_qc_qty 		= 0;
						$style_wise_total_qc_qty 		= 0;
						$style_wise_cutting_qc_percent 	= 0;
						$style_wise_cutting_qc_reject 	= 0;
						$style_wise_today_print_iss_qty = 0;
						$style_wise_total_print_iss_qty = 0;
						$style_wise_today_print_rcv_qty = 0;
						$style_wise_total_print_rcv_qty = 0;
						$style_wise_print_rej_qty 		= 0;
						$style_wise_print_wip_qty 		= 0;
						$style_wise_today_embr_iss_qty = 0;
						$style_wise_total_embr_iss_qty = 0;
						$style_wise_today_embr_rcv_qty = 0;
						$style_wise_total_embr_rcv_qty = 0;
						$style_wise_embr_rej_qty 		= 0;
						$style_wise_embr_wip_qty 		= 0;
						$style_wise_today_sew_in_qty 	= 0;
						$style_wise_total_sew_in_qty 	= 0;
						$style_wise_input_percent 		= 0;
						$style_wise_today_sew_out_qty 	= 0;
						$style_wise_total_sew_out_qty 	= 0;
						$style_wise_sewing_percent 		= 0;
						$style_wise_today_fin_qty 		= 0;
						$style_wise_total_fin_qty 		= 0;
						$style_wise_finish_percent 		= 0;
						$style_wise_today_ctn_qty 		= 0;
						$style_wise_total_ctn_qty 		= 0;
						$style_wise_today_exfact_qty 	= 0;
						$style_wise_total_exfact_qty 	= 0;
						$style_wise_today_exf_cm_qty 	= 0;
						$style_wise_total_exf_cm_qty 	= 0;
						$style_wise_today_exf_fob_qty 	= 0;
						$style_wise_total_exf_fob_qty 	= 0;
						$style_wise_today_sew_cm_qty 	= 0;
						$style_wise_total_sew_cm_qty 	= 0;
						$style_wise_today_fob_qty 		= 0;
						$style_wise_total_fob_qty 		= 0;
						$style_wise_total_ex_bal_qty 	= 0;
						$style_wise_total_ex_fob_qty 	= 0;

						foreach($job_data as $job_id=>$po_data)
						{
							
							foreach($po_data as $po_id=>$item_data)
							{
								$po_wise_color_qty 			= 0;
								$po_wise_fab_req_qty 		= 0;
								$po_wise_fab_rcv_qty 		= 0;
								$po_wise_fab_iss_qty 		= 0;
								$po_wise_fab_inh_qty 		= 0;
								$po_wise_today_lay_qty 		= 0;
								$po_wise_total_lay_qty 		= 0;
								$po_wise_cutting_percent 	= 0;
								$po_wise_today_qc_qty 		= 0;
								$po_wise_total_qc_qty 		= 0;
								$po_wise_cutting_qc_percent	= 0;
								$po_wise_cutting_qc_reject	= 0;
								$po_wise_today_print_iss_qty= 0;
								$po_wise_total_print_iss_qty= 0;
								$po_wise_today_print_rcv_qty= 0;
								$po_wise_total_print_rcv_qty= 0;
								$po_wise_print_rej_qty 		= 0;
								$po_wise_print_wip_qty 		= 0;
								$po_wise_today_embr_iss_qty= 0;
								$po_wise_total_embr_iss_qty= 0;
								$po_wise_today_embr_rcv_qty= 0;
								$po_wise_total_embr_rcv_qty= 0;
								$po_wise_embr_rej_qty 		= 0;
								$po_wise_embr_wip_qty 		= 0;
								$po_wise_today_sew_in_qty 	= 0;
								$po_wise_total_sew_in_qty 	= 0;
								$po_wise_input_percent 		= 0;
								$po_wise_today_sew_out_qty 	= 0;
								$po_wise_total_sew_out_qty 	= 0;
								$po_wise_sewing_percent 	= 0;
								$po_wise_today_fin_qty 		= 0;
								$po_wise_total_fin_qty 		= 0;
								$po_wise_finish_percent 	= 0;
								$po_wise_today_ctn_qty 		= 0;
								$po_wise_total_ctn_qty 		= 0;
								$po_wise_today_exfact_qty 	= 0;
								$po_wise_total_exfact_qty 	= 0;
								$po_wise_today_exf_cm_qty 	= 0;
								$po_wise_total_exf_cm_qty 	= 0;
								$po_wise_today_exf_fob_qty 	= 0;
								$po_wise_total_exf_fob_qty 	= 0;
								$po_wise_today_sew_cm_qty 	= 0;
								$po_wise_total_sew_cm_qty 	= 0;
								$po_wise_today_fob_qty 		= 0;
								$po_wise_total_fob_qty 		= 0;								
								$po_wise_total_ex_bal_qty 	= 0;								
								$po_wise_total_ex_fob_qty 	= 0;								

								foreach($item_data as $item_id=>$color_data)
								{ 
									$r=0;
									foreach($color_data as $color_id=>$row)
									{
										
										/*========================================================================================
										*																						  *
										*								Calculate cm valu 										  *	
										*																					  	  *	
										*========================================================================================*/
										$tot_dye_chemi_process_amount 	= $conversion_cost_arr[$job_id][101]['conv_amount_total_value'];
										$tot_yarn_dye_process_amount 	= $conversion_cost_arr[$job_id][30]['conv_amount_total_value'];
										$tot_aop_process_amount 		= $conversion_cost_arr[$job_id][35]['conv_amount_total_value'];
										
										$total_quot_qty=$total_quot_pcs_qty=$total_quot_amount=$total_sew_smv=$total_quot_amount_cal=0;
										$all_last_shipdates='';

							            foreach($style_wise_arr as $style_key=>$val)
										{	
											$total_cost=$val[('qty')]*$val[('final_cost_pcs')];
											$total_quot_qty+=$val[('qty')];
											$total_quot_pcs_qty+=$val[('qty_pcs')];
											$total_sew_smv+=$val[('sew_smv')];
											$total_quot_amount+=$total_cost;
											$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
										}
										$total_quot_amount_cal = $style_wise_arr[$job_id]['qty']*$style_wise_arr[$job_id]['final_cost_pcs'];
										$tot_cm_for_fab_cost=$summary_data[$job_id]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
										// echo $job_id."==".$summary_data[$job_id]['conversion_cost_total_value']."-(".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$tot_aop_process_amount.")<br>";
										$commision_quot_local=$commision_local_quot_cost_arr[$job_id];
										$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$job_id]+$commer_lc_cost_quot_arr[$job_id]+$freight_cost_data[$job_id]['freight_total_value']);
										$tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
										$tot_inspect_cour_certi_cost=$summary_data[$job_id]['inspection_total_value']+$summary_data[$job_id]['currier_pre_cost_total_value']+$summary_data[$job_id]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$job_id]['design_pre_cost_total_value'];
										// echo $summary_data[$job_id]['inspection_total_value']."+".$summary_data[$job_id]['currier_pre_cost_total_value']."+".$summary_data[$job_id]['certificate_pre_cost_total_value']."+".$tot_sum_amount_quot_calccc."+".$summary_data[$job_id]['design_pre_cost_total_value']."<br>";

										$tot_emblish_cost=$summary_data[$job_id]['embel_cost_total_value'];
										$pri_freight_cost_per=$summary_data[$job_id]['freight_total_value'];
										$pri_commercial_per=$commer_lc_cost[$job_id];
										$CommiData_foreign_cost=$CommiData_foreign_quot_cost_arr[$job_id];

										$total_btb=$summary_data[$job_id]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$job_id]['comm_cost_total_value']+$summary_data[$job_id]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$job_id]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$job_id]['common_oh_total_value']+$summary_data[$job_id]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
										// echo $summary_data[$job_id]['lab_test_total_value']."+".$tot_emblish_cost."+".$summary_data[$job_id]['comm_cost_total_value']."+".$summary_data[$job_id]['trims_cost_total_value']."+".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$summary_data[$job_id]['yarn_amount_total_value']."+".$tot_aop_process_amount."+".$summary_data[$job_id]['common_oh_total_value']."+".$summary_data[$job_id]['studio_pre_cost_total_value']."+".$tot_inspect_cour_certi_cost."<br>";
										$tot_quot_sum_amount=$total_quot_amount_cal-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
										// echo $total_quot_amount_cal."-(".$CommiData_foreign_cost."+".$pri_freight_cost_per."+".$pri_commercial_per.")<br>";
										$NetFOBValue_job = $tot_quot_sum_amount;
										// echo $NetFOBValue_job."<br>";
										$total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);

										$total_quot_pcs_qty = $quotation_qty_array[$job_id]['QTY_PCS'];
										// echo $total_cm_for_gmt;echo "<br>";
										$cm_valu_lc = 0;
										$cm_valu_lc = $total_cm_for_gmt/$total_quot_pcs_qty;
										// echo number_format(($total_cm_for_gmt/$total_quot_pcs_qty)*12,2); echo "<br>";
										/*========================================================================================
										*																						  *
										*											END											  *	
										*																					  	  *	
										*========================================================================================*/											
										$order_quantitys=$order_qnty_array[$po_id][$item_id][$color_id];								
										$fab_fin_req=$booking_no_fin_qnty_array[$po_id][$item_id][$color_id];	

									 	$today_lay_qnty=$lay_qnty_array[$job_id][$po_id][$item_id][$color_id]["today_lay"];
									 	$total_lay_qnty=$lay_qnty_array[$job_id][$po_id][$item_id][$color_id]["total_lay"];
									 	$cutting_lay_percent = ($total_lay_qnty / $order_quantitys)*100;

									 	// $today_qc_qnty=$qc_qnty_array[$job_id][$po_id][$item_id][$color_id]["today_qc"];
									 	// $total_qc_qnty=$qc_qnty_array[$job_id][$po_id][$item_id][$color_id]["total_qc"];
									 	// $total_qc_rej=$qc_qnty_array[$job_id][$po_id][$item_id][$color_id]["total_rej"];
									 	// $cutting_qc_percent = ($total_lay_qnty) ? ($total_qc_qnty / $total_lay_qnty)*100 : 0;

										 $today_qc_qnty 		= $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_cutting"];	
										 $total_qc_qnty 		= $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_cutting"];
										 $total_qc_rej 		= $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["cut_reject_qty"];
										 $cutting_qc_percent 	= ($total_lay_qnty) ? ($total_qc_qnty / $total_lay_qnty)*100 : 0;

									 	$today_issue_to_print = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_issue_to_print"];
									 	$total_issue_to_print = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_issue_to_print"];
									 	$today_rcv_frm_print = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_rcv_frm_print"];
									 	$total_rcv_frm_print = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_rcv_frm_print"];
									 	$print_reject = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["print_reject"];
									 	$print_wip = $total_issue_to_print - $total_rcv_frm_print;

										$today_issue_to_embr = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_issue_to_embroidery"];
									 	$total_issue_to_embr = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_issue_to_embroidery"];
									 	$today_rcv_frm_embr = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_rcv_frm_embroidery"];
									 	$total_rcv_frm_embr = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_rcv_frm_embroidery"];
									 	$embr_reject = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["reject_embroidery"];
									 	$embr_wip = $total_issue_to_embr - $total_rcv_frm_embr;



									 	$today_sewing_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
										$total_sewing_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_input"];
										$input_percent = ($total_lay_qnty) ? ($total_sewing_input / $total_lay_qnty)*100 : 0;

										$today_sewing_output = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_output"];
										$total_sewing_output = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_output"];
										$output_percent = ($total_sewing_input) ? ($total_sewing_output / $total_sewing_input)*100 : 0;

										$today_finishing = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id][0]["today_finishing"];
										$total_finishing = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id][0]["total_finishing"];
										$finishing_percent = ($total_sewing_output) ? ($total_finishing / $total_sewing_output)*100 : 0;

										$today_carton_qty = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_carton_qty"];
										$total_carton_qty = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_carton_qty"];


										$today_ex_fac=$ex_factory_arr[$po_id][$item_id][$color_id]['today_ex_fac'];
										$total_ex_fac=$ex_factory_arr[$po_id][$item_id][$color_id]['total_ex_fac'];	

										$total_sew_ex_fact_bal = $total_sewing_output - $total_ex_fac;
										$total_sew_ex_fact_fob = $total_sew_ex_fact_bal * $row['unit_price'];
										//$issue_qty=$issue_qnty_arr[$batch_mst_id_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]]];
										$issue_qty=$issue_qnty_arr[$po_id][$color_id][18];
										$receive_qty=$issue_qnty_arr[$po_id][$color_id][37];

										$fab_in_hand=$receive_qty-$issue_qty;
										$unit_price = $row['unit_price'];
										$today_fob_val = $today_sewing_output * $unit_price;
										$total_fob_val = $total_sewing_output * $unit_price;
										$today_exf_fob_val = $today_ex_fac * $unit_price;
										$total_exf_fob_val = $total_ex_fac * $unit_price;

										$costing_per=$costing_per_arr[$job_id];
										if($costing_per==1) $dzn_qnty=12;
										else if($costing_per==3) $dzn_qnty=12*2;
										else if($costing_per==4) $dzn_qnty=12*3;
										else if($costing_per==5) $dzn_qnty=12*4;
										else $dzn_qnty=1;	
										$dzn_qnty=$dzn_qnty*$row['total_set_qnty'];
										$cm_gmt_cost_dzn=$cm_gmt_cost_dzn_arr_new[$po_id]['dzn'];
										//echo $cm_gmt_cost_dzn.'DD'.$po_id.', ';
										//$cm_per_pcs=(($unit_price*$dzn_qnty)-$total_cost_arr[$job_id])+$cm_cost_arr[$job_id];
										$cm_per_pcs=$cm_gmt_cost_dzn/$dzn_qnty;
										$today_cm_val = $today_sewing_output * $cm_per_pcs;
										$total_cm_val = $total_sewing_output * $cm_per_pcs;
										$today_cm_val_lc = $today_sewing_output * $cm_valu_lc;
										$total_cm_val_lc = $total_sewing_output * $cm_valu_lc;

										$today_exf_cm_val = $today_ex_fac * $cm_per_pcs;
										$total_exf_cm_val = $total_ex_fac * $cm_per_pcs;
										$today_exf_cm_val_lc = $today_ex_fac * $cm_valu_lc;
										$total_exf_cm_val_lc = $total_ex_fac * $cm_valu_lc;
											
										$rowspan_num = $rowspan_arr[$style_id][$job_id][$po_id][$item_id];
									 
										if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
												<?
												$jj++;													
												?>
											 
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p><? echo $k;?></p></td>

												<? if($r==0){?>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="left"   width="80"><? echo $buyer_library[$row["buyer_name"]]; ?></td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100">
														<? echo implode(PHP_EOL, str_split($row["po_number"],10));?>														
												</td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100">
														<? echo $row['shiping_status'];?>														
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $rowspan_num;?>" width="30" align="center" valign="top">
													<a href="##" onClick="openmypage_image('requires/floor_wise_daily_rmg_production_controller.php?action=show_image&job_no=<? echo $job_id;?>','Image View')">
															<?if(isset($imge_arr[$job_id])){?>
															<!-- <div style="height:0;"> -->
																<img src="../../<? echo $imge_arr[$job_id];?>" height="28" width="28" />
															<!-- </div> -->
															<?}else{?>
																<img src="../../img/noimage.png" height="28" width="28"/>
															<?}?>
														
													</a>
												</td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100"><? echo implode(PHP_EOL, str_split($style_id,10));?></td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100">
														<? echo $row['grouping'];?>														
												</td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100">
														<?
														$floor_name = "";
														$flr_id_arr = array_filter(array_unique(explode(",",$sewing_floor_arr[$po_id][$item_id][$color_id]['floor_id'])));
														foreach($flr_id_arr as $v)
														{
															$floor_name .= $floor_arr[$v].",";
														}
														//  echo $floor_group_library[$row['floor_id']];
														echo chop($floor_name,",");
														 ?>														
												</td>
												<?}$r++;?>

												<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100"><? echo $color_Arr_library[$color_id];?></td>
												<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="right"    width="70"><p><? echo number_format($order_quantitys,0); ?></p></td> 

												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60"><p><? echo number_format($fab_fin_req,2);?></p></td>

												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<a href="##" onClick="openmypage_fab_popup(<? echo $po_id;?>,<? echo $color_id?>,<? echo 1 ?>, 'fab_issue_popup');" >
														<p><? echo number_format($receive_qty,2);?></p>
													</a>
												</td>
												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<a href="##" onClick="openmypage_fab_popup(<? echo $po_id;?>,<? echo $color_id?>,<? echo 2 ?>, 'fab_issue_popup');" > 
														<p><? echo number_format($issue_qty,2);?></p>
													</a>
												</td>
												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<p><? echo number_format($fab_in_hand,2);?></p>
												</td>
																									
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,0,'A','production_qnty_popup','Today Cut','870','500');">
														<p><? echo number_format($today_lay_qnty,0);?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,0,'B','production_qnty_popup','Total Cut','870','500');">
														<p><? echo number_format($total_lay_qnty,0);?></p>
													</a>
												</td>
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<p><? echo number_format($cutting_lay_percent,0); ?>%</p>
												</td>

												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'A','production_qnty_popup','Today Cutting QC','870','500');">
														<p><? echo number_format($today_qc_qnty,0);?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'B','production_qnty_popup','Total Cutting QC','870','500');">
														<p><? echo number_format($total_qc_qnty,0);?></p>
													</a>
												</td>
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<p><? echo number_format($cutting_qc_percent,0); ?>%</p>
												</td>
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<!-- <a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'C','production_qnty_popup','Cutting QC Reject','870','500');"> --><p>
														<? echo number_format($total_qc_rej,0);?></p>
													<!-- </a> -->
												</td>

												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,2,'A','production_qnty_popup','Today delivery to print','870','500');">
														<p><? echo number_format($today_issue_to_print,0); ?></p>
													</a>														
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,2,'B','production_qnty_popup','Total delivery to print','870','500');">
														<p><? echo number_format($total_issue_to_print,0); ?></p>
													</a>														
												</td>

												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,3,'A','production_qnty_popup','Today receive from print','870','500');">
														<? echo number_format($today_rcv_frm_print,0); ?>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,3,'B','production_qnty_popup','Total receive from print','870','500');">
														<p><? echo number_format($total_rcv_frm_print,0); ?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<!-- <a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,3,'C','production_qnty_popup','Print Reject','870','500');"> -->
														<p><? echo number_format($print_reject,0); ?></p>
													<!-- </a> -->
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><!-- 
													<a href="##" onclick="openmypage_production_popup(<? //echo $po_id;?>,<? //echo $item_id;?>,<? //echo $color_id;?>,3,'D','production_qnty_popup','Print WIP','870','500');"> -->
														<p><? echo number_format($print_wip,0);?></p>
													<!-- </a> -->
												</td> 
												 
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
														<p><? echo number_format($today_issue_to_embr,0); ?></p>
																											
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
														<p><? echo number_format($total_issue_to_embr,0); ?></p>
																								
												</td>

												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
														<? echo number_format($today_rcv_frm_embr,0); ?>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													
										         	<p><? echo number_format($total_rcv_frm_embr,0); ?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
												
														<p><? echo number_format($embr_reject,0); ?></p>
												
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													
														<p><? echo number_format($embr_wip,0);?></p>
													
												</td> 
																								
													
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,4,'A','production_qnty_popup','Today Sewing Input','870','500');"><p>
														<? echo number_format($today_sewing_input,0);?></p>
													</a>
												</td>

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,4,'B','production_qnty_popup','Total Sewing Input','870','500');"><p>
														<? echo number_format($total_sewing_input,0);?></p>
													</a>
												</td>
												
												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60"><p><? echo number_format($input_percent,0);?>%</p></td>
																 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,5,'A','production_qnty_popup','Today Sewing Output','870','500');"><p>
														<? echo number_format($today_sewing_output,0);?></p>
													</a>
												</td>								 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,5,'B','production_qnty_popup','Total Sewing Output','870','500');"><p>
														<? echo number_format($total_sewing_output,0);?></p>
													</a>
												</td>								 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="60"><p><? echo number_format($output_percent,0);?>%</p></td>

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,8,'A','production_qnty_popup','Today Finishing','870','500');"><p>
														<? echo number_format($today_finishing,0);?></p>
													</a>
												</td>								 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,8,'B','production_qnty_popup','Total Finishing','870','500');"><p>
														<? echo number_format($total_finishing,0);?></p>
													</a>
												</td>							 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60"><p><? echo number_format($finishing_percent,0); ?>%</p></td>

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<p>
														<? echo number_format($today_carton_qty,0);?>														
													</p>													
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<p><? echo number_format($total_carton_qty,0);?></p>													
												</td>	

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_ex_fac_total('<? echo $po_id;?>**<? echo $item_id;?>**<? echo $color_id;?>**<? echo str_replace("'", "", $txt_production_date);?>**1','exfac_action');" ><p><? echo number_format($today_ex_fac,0);?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60"><a href="##" onClick="openmypage_ex_fac_total('<? echo $po_id;?>**<? echo $item_id;?>**<? echo $color_id;?>**<? echo str_replace("'", "", $txt_production_date);?>**2', 'exfac_action');" ><p><? echo number_format($total_ex_fac,0);?></p>
													</a>
												</td>


												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="105"><p>
													<? echo number_format($total_sew_ex_fact_bal,0); ?>
												</p></td>

																		 
											</tr>	
										<?
										// ====po wise sub total=======											
										$po_wise_color_qty 			+= $order_quantitys;
										$po_wise_fab_req_qty 		+= $fab_fin_req;
										$po_wise_fab_rcv_qty 		+= $receive_qty;
										$po_wise_fab_iss_qty 		+= $issue_qty;
										$po_wise_fab_inh_qty 		+= $fab_in_hand;
										$po_wise_today_lay_qty 		+= $today_lay_qnty;
										$po_wise_total_lay_qty 		+= $total_lay_qnty;
										$po_wise_cutting_percent 	+= $cutting_percent;
										$po_wise_today_qc_qty 		+= $today_qc_qnty;
										$po_wise_total_qc_qty 		+= $total_qc_qnty;
										$po_wise_cutting_qc_percent	+= $cutting_qc_percent;
										$po_wise_cutting_qc_reject	+= $total_qc_rej;
										$po_wise_today_print_iss_qty+= $today_issue_to_print;
										$po_wise_total_print_iss_qty+= $total_issue_to_print;
										$po_wise_today_print_rcv_qty+= $today_rcv_frm_print;
										$po_wise_total_print_rcv_qty+= $total_rcv_frm_print;
										$po_wise_print_rej_qty 		+= $print_reject;
										$po_wise_print_wip_qty 		+= $print_wip;
										$po_wise_today_embr_iss_qty+= $today_issue_to_embr;
										$po_wise_total_embr_iss_qty+= $total_issue_to_embr;
										$po_wise_today_embr_rcv_qty+= $today_rcv_frm_embr;
										$po_wise_total_embr_rcv_qty+= $total_rcv_frm_embr;
										$po_wise_embr_rej_qty 		+= $embr_reject;
										$po_wise_embr_wip_qty 		+= $embr_wip;
										$po_wise_today_sew_in_qty 	+= $today_sewing_input;
										$po_wise_total_sew_in_qty 	+= $total_sewing_input;
										$po_wise_input_percent 		+= $input_percent;
										$po_wise_today_sew_out_qty 	+= $today_sewing_output;
										$po_wise_total_sew_out_qty 	+= $total_sewing_output;
										$po_wise_sewing_percent 	+= $output_percent;
										$po_wise_today_fin_qty 		+= $today_finishing;
										$po_wise_total_fin_qty 		+= $total_finishing;
										$po_wise_finish_percent 	+= $finishing_percent;
										$po_wise_today_ctn_qty 		+= $today_carton_qty;
										$po_wise_total_ctn_qty 		+= $total_carton_qty;
										$po_wise_today_exfact_qty 	+= $today_ex_fac;
										$po_wise_total_exfact_qty 	+= $total_ex_fac;
										$po_wise_today_exf_cm_qty 	+= $today_exf_cm_val;
										$po_wise_total_exf_cm_qty 	+= $total_exf_cm_val;
										$po_wise_today_exf_cm_qty_lc += $today_exf_cm_val_lc;
										$po_wise_total_exf_cm_qty_lc += $total_exf_cm_val_lc;
										$po_wise_today_exf_fob_qty 	+= $today_exf_fob_val;
										$po_wise_total_exf_fob_qty 	+= $total_exf_fob_val;
										$po_wise_today_sew_cm_qty 	+= $today_cm_val;
										$po_wise_total_sew_cm_qty 	+= $total_cm_val;
										$po_wise_today_sew_cm_qty_lc += $today_cm_val_lc;
										$po_wise_total_sew_cm_qty_lc += $total_cm_val_lc;
										$po_wise_today_fob_qty 		+= $today_fob_val;
										$po_wise_total_fob_qty 		+= $total_fob_val;
										$po_wise_total_ex_bal_qty	+= $total_sew_ex_fact_bal;
										$po_wise_total_ex_fob_qty	+= $total_sew_ex_fact_fob;
										// ====style wise sub total======
										$style_wise_color_qty 			+= $order_quantitys;
										$style_wise_fab_req_qty 		+= $fab_fin_req;
										$style_wise_fab_rcv_qty 		+= $receive_qty;
										$style_wise_fab_iss_qty 		+= $issue_qty;
										$style_wise_fab_inh_qty 		+= $fab_in_hand;
										$style_wise_today_lay_qty 		+= $today_lay_qnty;
										$style_wise_total_lay_qty 		+= $total_lay_qnty;
										$style_wise_cutting_percent 	+= $cutting_percent;
										$style_wise_today_qc_qty 		+= $today_qc_qnty;
										$style_wise_total_qc_qty 		+= $total_qc_qnty;
										$style_wise_cutting_qc_percent 	+= $cutting_qc_percent;
										$style_wise_cutting_qc_reject 	+= $total_qc_rej;
										$style_wise_today_print_iss_qty += $today_issue_to_print;
										$style_wise_total_print_iss_qty += $total_issue_to_print;
										$style_wise_today_print_rcv_qty += $today_rcv_frm_print;
										$style_wise_total_print_rcv_qty += $total_rcv_frm_print;
										$style_wise_print_rej_qty 		+= $print_reject;
										$style_wise_print_wip_qty 		+= $print_wip;
										$style_wise_today_embr_iss_qty += $today_issue_to_embr;
										$style_wise_total_embr_iss_qty += $total_issue_to_embr;
										$style_wise_today_embr_rcv_qty += $today_rcv_frm_embr;
										$style_wise_total_embr_rcv_qty += $total_rcv_frm_embr;
										$style_wise_embr_rej_qty 		+= $embr_reject;
										$style_wise_embr_wip_qty 		+= $embr_wip;
										$style_wise_today_sew_in_qty 	+= $today_sewing_input;
										$style_wise_total_sew_in_qty 	+= $total_sewing_input;
										$style_wise_input_percent 		+= $input_percent;
										$style_wise_today_sew_out_qty 	+= $today_sewing_output;
										$style_wise_total_sew_out_qty 	+= $total_sewing_output;
										$style_wise_sewing_percent 		+= $output_percent;
										$style_wise_today_fin_qty 		+= $today_finishing;
										$style_wise_total_fin_qty 		+= $total_finishing;
										$style_wise_finish_percent 		+= $finishing_percent;
										$style_wise_today_ctn_qty 		+= $today_carton_qty;
										$style_wise_total_ctn_qty 		+= $total_carton_qty;
										$style_wise_today_exfact_qty 	+= $today_ex_fac;
										$style_wise_total_exfact_qty 	+= $total_ex_fac;
										$style_wise_today_exf_cm_qty 	+= $today_exf_cm_val;
										$style_wise_total_exf_cm_qty 	+= $total_exf_cm_val;
										$style_wise_today_exf_fob_qty 	+= $today_exf_fob_val;
										$style_wise_total_exf_fob_qty 	+= $total_exf_fob_val;
										$style_wise_today_sew_cm_qty 	+= $today_cm_val;
										$style_wise_total_sew_cm_qty 	+= $total_cm_val;
										$style_wise_today_fob_qty 		+= $today_fob_val;
										$style_wise_total_fob_qty 		+= $total_fob_val;
										$style_wise_total_ex_bal_qty	+= $total_sew_ex_fact_bal;
										$style_wise_total_ex_fob_qty	+= $total_sew_ex_fact_fob;
										// ========= grand total =========== 
										$gr_color_qty 			+= $order_quantitys;
										$gr_fab_req_qty 		+= $fab_fin_req;
										$gr_fab_rcv_qty 		+= $receive_qty;
										$gr_fab_iss_qty 		+= $issue_qty;
										$gr_fab_inh_qty 		+= $fab_in_hand;
										$gr_today_lay_qty 		+= $today_lay_qnty;
										$gr_total_lay_qty 		+= $total_lay_qnty;
										$gr_cutting_percent 	+= $cutting_percent;
										$gr_today_qc_qty 		+= $today_qc_qnty;
										$gr_total_qc_qty 		+= $total_qc_qnty;
										$gr_cutting_qc_percent 	+= $cutting_qc_percent;
										$gr_cutting_qc_reject 	+= $total_qc_rej;
										$gr_today_print_iss_qty += $today_issue_to_print;
										$gr_total_print_iss_qty += $total_issue_to_print;
										$gr_today_print_rcv_qty += $today_rcv_frm_print;
										$gr_total_print_rcv_qty += $total_rcv_frm_print;
										$gr_print_rej_qty 		+= $print_reject;
										$gr_print_wip_qty 		+= $print_wip;
										$gr_today_embr_iss_qty += $today_issue_to_embr;
										$gr_total_embr_iss_qty += $total_issue_to_embr;
										$gr_today_embr_rcv_qty += $today_rcv_frm_embr;
										$gr_total_embr_rcv_qty += $total_rcv_frm_embr;
										$gr_embr_rej_qty 		+= $embr_reject;
										$gr_embr_wip_qty 		+= $embr_wip;
										$gr_today_sew_in_qty 	+= $today_sewing_input;
										$gr_total_sew_in_qty 	+= $total_sewing_input;
										$gr_input_percent 		+= $input_percent;
										$gr_today_sew_out_qty 	+= $today_sewing_output;
										$gr_total_sew_out_qty 	+= $total_sewing_output;
										$gr_sewing_percent 		+= $output_percent;
										$gr_today_fin_qty 		+= $today_finishing;
										$gr_total_fin_qty 		+= $total_finishing;
										$gr_finish_percent 		+= $finishing_percent;
										$gr_today_ctn_qty 		+= $today_carton_qty;
										$gr_total_ctn_qty 		+= $total_carton_qty;
										$gr_today_exfact_qty 	+= $today_ex_fac;
										$gr_total_exfact_qty 	+= $total_ex_fac;
										$gr_today_exf_cm_qty 	+= $today_exf_cm_val;
										$gr_total_exf_cm_qty 	+= $total_exf_cm_val;
										$gr_today_exf_cm_qty_lc += $today_exf_cm_val_lc;
										$gr_total_exf_cm_qty_lc += $total_exf_cm_val_lc;
										$gr_today_exf_fob_qty 	+= $today_exf_fob_val;
										$gr_total_exf_fob_qty 	+= $total_exf_fob_val;
										$gr_today_sew_cm_qty 	+= $today_cm_val;
										$gr_total_sew_cm_qty 	+= $total_cm_val;
										$gr_today_sew_cm_qty_lc += $today_cm_val_lc;
										$gr_total_sew_cm_qty_lc += $total_cm_val_lc;
										$gr_today_fob_qty 		+= $today_fob_val;
										$gr_total_fob_qty 		+= $total_fob_val;
										$gr_total_ex_bal_qty	+= $total_sew_ex_fact_bal;
										$gr_total_ex_fob_qty 	+= $total_sew_ex_fact_fob;
										
										$k++;											
									}
								}
								?>
								<tr bgcolor="#E4E4E4">
									<td colspan="9" align="right"><b>Order Wise Sub Total</b></td>
									<td align="right"><b><? echo number_format($po_wise_color_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_fab_req_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_fab_rcv_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_fab_iss_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_fab_inh_qty,2);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_lay_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_lay_qty,0);?></b></td>
									<td align="right"></td>

									<td align="right"><b><? echo number_format($po_wise_today_qc_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_qc_qty,0);?></b></td>
									<td align="right"></td>							
									<td align="right"><b><? echo number_format($po_wise_cutting_qc_reject,0);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_print_iss_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_print_iss_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_today_print_rcv_qty,0); ?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_print_rcv_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_print_rej_qty,0); ?></b></td>										
									<td align="right"><b><? echo number_format($po_wise_print_wip_qty,0);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_embr_iss_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($$gr_total_embr_iss_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_today_embr_rcv_qty,0); ?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_embr_rcv_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_embr_rej_qty,0); ?></b></td>										
									<td align="right"><b><? echo number_format($po_wise_embr_wip_qty,0);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_sew_in_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_sew_in_qty,0);?></b></td>
									<td align="right"><b></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_sew_out_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_sew_out_qty,0);?></b></td>
									<td align="right"><b><? //echo $order_wise_input_sewing_balance;?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_fin_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_fin_qty,0);?></b></td>
									<td align="right"><b><? //echo $order_wise_sewing_fin_balance;?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_ctn_qty,0) ;?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_ctn_qty,0);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_exfact_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_exfact_qty,0);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_total_ex_bal_qty,0);?></b></td>

									
								</tr>
								<?
							}							
						}							
						?>
						<!-- <tr bgcolor="#E4E4E4">
							<td colspan="7" align="right"><b>Style Wise Sub Total</b></td>
							<td align="right"><b><? echo number_format($style_wise_color_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_req_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_rcv_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_iss_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($order_wise_fab_req,2);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_lay_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_lay_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($order_wise_fab_possible_qty,2);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_qc_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_qc_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_cutting_qc_percent,2);?></b></td>							
							<td align="right"><b><? echo number_format($style_wise_cutting_qc_reject,0);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_print_iss_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_print_iss_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_today_print_rcv_qty,0); ?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_print_rcv_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_print_rej_qty,0); ?></b></td>										
							<td align="right"><b><? echo number_format($style_wise_print_wip_qty,0);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_sew_in_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_sew_in_qty,0);?></b></td>
							<td align="right"><b></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_sew_out_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_sew_out_qty,0);?></b></td>
							<td align="right"><b><? //echo $order_wise_input_sewing_balance;?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_fin_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_fin_qty,0);?></b></td>
							<td align="right"><b><? //echo $order_wise_sewing_fin_balance;?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_ctn_qty,0) ;?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_ctn_qty,0);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_exfact_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_exfact_qty,0);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_sew_cm_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_sew_cm_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_today_fob_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_fob_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_ex_bal_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_ex_fob_qty,2);?></b></td>
						</tr> -->	
						<?
						$gr_inh_qty;
					}

					?>											
					</table>										  
				</div>	
			</div>
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3500" rules="all"  >
				<tr bgcolor="#E4E4E4"  >  
					<td valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p>&nbsp; </p></td>

					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p>&nbsp;</p></td>
					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>
					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>

					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="30"><p>&nbsp;</p></td>

					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>
					
					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>
					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>

					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><strong>Grand Total</strong></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="70"    align="right"><b><? echo number_format($gr_color_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60"   align="right"><b><? echo number_format($gr_fab_req_qty,2) ?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60"   align="right"><b><? echo number_format($gr_fab_rcv_qty,2) ?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60"   align="right"><b><? echo number_format($gr_fab_iss_qty,2) ?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_fab_inh_qty,2);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_lay_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_lay_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo number_format($gr_cutting_percent,2);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_qc_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_qc_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo number_format($gr_cutting_qc_percent,2);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_cutting_qc_reject,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_print_iss_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_print_iss_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_print_rcv_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_print_rcv_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_print_rej_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_print_wip_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_embr_iss_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_embr_iss_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_embr_rcv_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_embr_rcv_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_embr_rej_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_embr_wip_qty,0);?></b></td>




					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_sew_in_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_sew_in_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo $gr_today_sew_out_qty;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_sew_out_qty,0);?></b></td>		
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_sew_out_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo $gr_total_sew_out_qty;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_fin_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_fin_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo $gr_total_finishing;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_ctn_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_ctn_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_exfact_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_exfact_qty,0);?></b></td>

																 
					<td style="word-wrap: break-word;word-break: break-all;"  width="105" align="right"><b><? echo number_format($gr_total_ex_bal_qty,0);?></b></td>											 											 

				</tr>	
				
			</table>	
		 </div> 
        <?
        foreach (glob("$user_id*.xls") as $filename) {
	    @unlink($filename);
	    }
	    //---------end------------//
	    $name2=time();
	    $summary_filename=$user_id."_"."summary_".$name2.".xls";
	    $create_new_doc2 = fopen($summary_filename, 'w');	
	    $is_created2 = fwrite($create_new_doc2, $summary_html);
	    //======================================================
		
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename####$summary_filename";
		exit();	 
	}
	elseif(str_replace("'","",$type)==2222) // show2 7-01-2023
	{
		// ============================= getting form value =============================
		$working_company_id = str_replace("'", "", $cbo_work_company_name);
		$location_id 		= str_replace("'", "", $cbo_location_name);
		$floor_id 			= str_replace("'", "", $cbo_floor_name);
		$group_name 		= str_replace("'", "", $cbo_group_name);
		$buyer_id 			= str_replace("'", "", $cbo_buyer_name);
		$year 				= str_replace("'", "", $cbo_year);
		$job_id 			= str_replace("'", "", $txt_job_no);
		$hidden_job_id 		= str_replace("'", "", $hidden_job_id);
		$txt_int_ref 		= str_replace("'", "", $txt_int_ref);
		$order_id 			= str_replace("'", "", $txt_order_no);
		$hidden_order_id 	= str_replace("'", "", $hidden_order_id);
		$shiping_status 	= str_replace("'", "", $cbo_shipping_status);
		// echo $location_id."_".$floor_id."_".$group_name;

		$sql_cond_lay 		= "";
		$sql_cond_qc 		= "";
		$sql_cond_prod 		= "";
		$sql_cond_exfact 	= "";

		$lay_cond 			= "";
		$qc_cond 			= "";
		$exfact_cond 		= "";

		if($working_company_id !="")
		{
			$sql_cond_lay 	= " and d.working_company_id in($working_company_id)";
			$sql_cond_qc 	= " and d.serving_company in($working_company_id)";
			$sql_cond_prod 	= " and d.serving_company in($working_company_id)";
			$sql_cond_exfact= " and f.delivery_company_id in($working_company_id)";

			$lay_cond 		.= " and a.working_company_id in($working_company_id)";
			$qc_cond 		.= " and a.serving_company in($working_company_id)";
			$exfact_cond	.= " and d.delivery_company_id in($working_company_id)";
		}

		if($location_id !="")
		{
			$sql_cond_lay 	.= " and d.location_id in($location_id)";
			$sql_cond_qc 	.= " and d.location_id in($location_id)";
			$sql_cond_prod 	.= " and d.location in($location_id)";
			$sql_cond_exfact.= " and f.delivery_location_id in($location_id)";

			$lay_cond 		.= " and a.location_id in($location_id)";
			$qc_cond 		.= " and a.location_id in($location_id)";
			$exfact_cond	.= " and d.delivery_location_id in($location_id)";
		}

		if($shiping_status !="")
		{
			$sql_cond_lay 	.= " and b.shiping_status in($shiping_status)";
			$sql_cond_qc 	.= " and b.shiping_status in($shiping_status)";
			$sql_cond_prod 	.= " and b.shiping_status in($shiping_status)";
			$sql_cond_exfact.= " and b.shiping_status in($shiping_status)";
		}

		if($group_name)
		{
			$group_cond="";
			$group_sql = sql_select("SELECT a.id from lib_prod_floor a where a.status_active=1 and a.group_name=$cbo_group_name order by a.id");
			foreach ($group_sql as $value) 
			{
				if($group_cond=="")
				{
					$group_cond = $value[csf('id')];
				}
				else
				{
					$group_cond .= ",".$value[csf('id')];
				}
			}
			$sql_cond_lay.=" and d.floor_id in($group_cond)";
			$sql_cond_qc.=" and d.floor_id in($group_cond)";
			$sql_cond_prod.=" and d.floor_id in($group_cond)";
			$sql_cond_exfact.=" and f.delivery_floor_id in($group_cond)";

			$lay_cond 		.= " and a.floor_id in($group_cond)";
			$qc_cond 		.= " and a.floor_id in($group_cond)";
			$exfact_cond	.= " and d.delivery_floor_id in($group_cond)";
		}
		else if($floor_id !="")
		{
			$sql_cond_lay 	.= " and d.floor_id in($floor_id)";
			$sql_cond_qc 	.= " and d.floor_id in($floor_id)";
			$sql_cond_prod 	.= " and d.floor_id in($floor_id)";
			$sql_cond_exfact.= " and f.delivery_floor_id in($floor_id)";

			$lay_cond 		.= " and a.floor_id in($floor_id)";
			$qc_cond 		.= " and a.floor_id in($floor_id)";
			$exfact_cond	.= " and d.delivery_floor_id in($floor_id)";
		}

		if($buyer_id !=0)
		{
			$sql_cond_lay 	.= " and a.buyer_name = $buyer_id";
			$sql_cond_qc 	.= " and a.buyer_name = $buyer_id";
			$sql_cond_prod 	.= " and a.buyer_name = $buyer_id";
			$sql_cond_exfact.= " and a.buyer_name = $buyer_id";
		}

		if($year !=0)
		{
			if($db_type==0)
			{
				$sql_cond_lay .=" and year(a.insert_date)=$year";
				$sql_cond_qc .=" and year(a.insert_date)=$year";
				$sql_cond_prod .=" and year(a.insert_date)=$year";
				$sql_cond_exfact .=" and year(a.insert_date)=$year";
			}
			else
			{
				$sql_cond_lay .=" and to_char(a.insert_date,'YYYY')=$year";
				$sql_cond_qc .=" and to_char(a.insert_date,'YYYY')=$year";
				$sql_cond_prod .=" and to_char(a.insert_date,'YYYY')=$year";
				$sql_cond_exfact .=" and to_char(a.insert_date,'YYYY')=$year";
			}
		}

		if($hidden_job_id!="")
		{
			$sql_cond_lay.=" and a.id in($hidden_job_id)";
			$sql_cond_qc.=" and a.id in($hidden_job_id)";
			$sql_cond_prod.=" and a.id in($hidden_job_id)";
			$sql_cond_exfact.=" and a.id in($hidden_job_id)";
		}
		
		if($hidden_order_id !="")
		{
			$sql_cond_lay.=" and b.id in($hidden_order_id)";
			$sql_cond_qc.=" and b.id in($hidden_order_id)";
			$sql_cond_prod.=" and b.id in($hidden_order_id)";
			$sql_cond_exfact.=" and b.id in($hidden_order_id)";
		} 

		/*if($txt_int_ref !="")
		{
			$sql_cond_lay.=" and b.grouping like '%$txt_int_ref%'";
			$sql_cond_qc.=" and b.grouping like '%$txt_int_ref%'";
			$sql_cond_prod.=" and b.grouping like '%$txt_int_ref%'";
			$sql_cond_exfact.=" and b.grouping like '%$txt_int_ref%'";
		}*/ 

		// ======================================== MAIN QUERY FOR LAY =========================================
		$today_lay_sql="SELECT a.company_name, c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.job_no=a.job_no and d.id=e.mst_id  and a.job_no=c.job_no_mst and e.mst_id=f.mst_id and f.order_id=b.id and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_confirmed=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.entry_date=$txt_production_date $sql_cond_lay
		group by a.company_name,c.color_number_id,b.id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty"; 
		// echo $today_lay_sql;die();
		

		$po_id_array=array();
		$col_id_array=array();
		$production_main_array=array();
		$buyer_wise_summary_array=array();
		$buyer_wise_all_info=array();
		$lc_company_array = array();
		$all_job_array = array();
		foreach(sql_select($today_lay_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_job_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("working_company_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_set_qnty"]=$row[csf("total_set_qnty")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

			$buyer_wise_summary_array[$row[csf("buyer_name")]]['unit_price'] = $row[csf("unit_price")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['total_set_qnty'] = $row[csf("total_set_qnty")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['job_no'] = $row[csf("job_no")];

			$lc_company_array[$row[csf("company_name")]] = $row[csf("company_name")];

		}

		// ======================================== MAIN QUERY FOR CUTTING QC =========================================
		$today_qc_sql="SELECT a.company_name, c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.serving_company,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_gmts_cutting_qc_mst d,pro_gmts_cutting_qc_dtls e
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.job_no=a.job_no and d.id=e.mst_id  and a.job_no=c.job_no_mst and b.id=e.order_id  and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_confirmed=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.entry_date=$txt_production_date $sql_cond_qc
		group by a.company_name, c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.serving_company,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty"; //and c.id=e.color_size_id
		// echo $today_qc_sql;die();		
		
		
		foreach(sql_select($today_qc_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_job_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("serving_company")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_set_qnty"]=$row[csf("total_set_qnty")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

			$buyer_wise_summary_array[$row[csf("buyer_name")]]['unit_price'] = $row[csf("unit_price")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['total_set_qnty'] = $row[csf("total_set_qnty")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['job_no'] = $row[csf("job_no")];

			$lc_company_array[$row[csf("company_name")]] = $row[csf("company_name")];

		}

		// ======================================== MAIN QUERY FOR PRODUCTION =========================================
		$today_prod_sql="SELECT a.company_name, c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cut_no,b.po_number,d.serving_company,d.location,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty,d.production_type  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.id=e.mst_id  and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_confirmed=1  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.production_date=$txt_production_date $sql_cond_prod
		group by a.company_name, c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,d.cut_no,b.po_number,d.serving_company,d.location,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty,d.production_type  "; //and c.id=e.color_size_break_down_id
		// echo $today_prod_sql;die();		
		
		$sewing_floor_arr = array();
		foreach(sql_select($today_prod_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_job_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
			if($row[csf("production_type")]==4)
			{
				$sewing_floor_arr[$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("floor_id")]] = $row[csf("floor_id")];
			}

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("serving_company")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_set_qnty"]=$row[csf("total_set_qnty")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

			$buyer_wise_summary_array[$row[csf("buyer_name")]]['unit_price'] = $row[csf("unit_price")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['total_set_qnty'] = $row[csf("total_set_qnty")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['job_no'] = $row[csf("job_no")];

			$lc_company_array[$row[csf("company_name")]] = $row[csf("company_name")];

		}
		// echo "<pre>";print_r($sewing_floor_arr);die;
		// ======================================== MAIN QUERY FOR EX-FACTORY =========================================
		$today_exf_sql="SELECT a.company_name, c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,b.po_number,f.delivery_company_id,f.delivery_location_id,f.delivery_floor_id, a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_ex_factory_mst d,pro_ex_factory_dtls e, pro_ex_factory_delivery_mst f
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.id=e.mst_id and f.id=d.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_confirmed=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.ex_factory_date=$txt_production_date $sql_cond_exfact
		group by a.company_name, c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,b.po_number,f.delivery_company_id,f.delivery_location_id,f.delivery_floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,b.unit_price,a.total_set_qnty"; //and c.id=e.color_size_break_down_id
		// echo $today_exf_sql;die();		
		
		
		foreach(sql_select($today_exf_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_job_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("delivery_company_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("delivery_location_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("delivery_floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_set_qnty"]=$row[csf("total_set_qnty")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

			$buyer_wise_summary_array[$row[csf("buyer_name")]]['unit_price'] = $row[csf("unit_price")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['total_set_qnty'] = $row[csf("total_set_qnty")];
			$buyer_wise_summary_array[$row[csf("buyer_name")]]['job_no'] = $row[csf("job_no")];

			$lc_company_array[$row[csf("company_name")]] = $row[csf("company_name")];

		}
		// echo "<pre>";
		// print_r($production_main_array);die;
		if(count($production_main_array)==0)
		{
			?>
			<div style="margin:20px auto; width: 90%">
				<div class="alert alert-error">
				  <strong>Data not found!</strong> Change a few things up and try submitting again.
				</div>
			</div>
			<?
			die();
		} 

		$prod_po_ids=implode(",", $prod_po_id_array);
		if($prod_po_ids)
		{
			$po_conds2=" and b.id in($prod_po_ids)";
		}

		$po_ids=implode(",", $po_id_array);
		$color_ids=implode(",", $col_id_array);
		if(!$po_ids) $po_ids=0;
		if(!$color_ids) $color_ids=0;

		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( po_id_string in ($ids) ";
				else
					$po_cond.=" or   po_id_string in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and po_id_string in ($po_ids) ";
		}
		//echo $po_cond;die;
		// ===================================== FOR COLOR WISE ORDER QTY ========================================	
		$poIDs=implode(",", $po_id_array);
		// if($poIDs!="")
		// {
		// 	$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($working_company_id,$poIDs); 
		// 	//print_r($cm_gmt_cost_dzn_arr);
		// }
		$lc_company = implode(",", $lc_company_array);
		$cm_gmt_cost_dzn_arr=array();
		$cm_gmt_cost_dzn_arr_new=array();				 
		$new_arr=array_unique(explode(",", $poIDs));
		$chnk_arr=array_chunk($new_arr,50);
		foreach($chnk_arr as $vals )
		{
			$p_ids=implode(",", $vals);
			$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($lc_company,$p_ids); 
			 foreach($cm_gmt_cost_dzn_arr as $po_id=>$vv)
			 {
			 	$cm_gmt_cost_dzn_arr_new[$po_id]["dzn"]=$vv["dzn"] ;
			 }
		}

		$order_qnty_array=array();
		$buyer_wise_order_qnty_array=array();
		$po_cond1=str_replace("po_id_string", "po_break_down_id", $po_cond);
		$order_qnty_sqls="SELECT b.po_break_down_id,b.color_number_id,b.item_number_id,sum(b.order_quantity) as order_quantity,a.buyer_name from wo_po_details_master a, wo_po_color_size_breakdown b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $po_cond1 group by b.po_break_down_id,b.color_number_id,b.item_number_id,a.buyer_name";
		// echo $order_qnty_sqls;die();
		foreach(sql_select($order_qnty_sqls) as $values)
		{
		 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]=$values[csf("order_quantity")];

		 	$buyer_wise_order_qnty_array[$values[csf("buyer_name")]] += $values[csf("order_quantity")];
		}
		
		// echo "<pre>";
		// print_r($buyer_wise_summary_array);
		// echo "</pre>";
		// die();
		// ============================================ FOR PRODUCTION ================================================
		$po_cond2=str_replace("po_id_string", "b.id", $po_cond);
		$order_sql="SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no,c.color_number_id, 
		sum(c.order_quantity) as order_quantity, 
		sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_cutting ,
		sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_issue_to_print ,
		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=1  then e.production_qnty else 0 end ) as total_issue_to_print ,
		sum(case when d.production_type=2 and e.production_type=63 and d.embel_name=2 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_issue_to_embroidery ,
		sum(case when d.production_type=2 and e.production_type=63 and d.embel_name=2  then e.production_qnty else 0 end ) as total_issue_to_embroidery ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_rcv_frm_print ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=1  then e.production_qnty else 0 end ) as total_rcv_frm_print,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=2 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_rcv_frm_embroidery ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=2 then e.production_qnty else 0 end ) as total_rcv_frm_embroidery ,
		sum(case when d.production_type in(2,3) and e.production_type in(2,3) and d.embel_name=1  then e.reject_qty else 0 end ) as print_reject ,
        sum(case when d.production_type in(2,3) and e.production_type in(2,3) and d.embel_name=2  then e.reject_qty else 0 end ) as reject_embroidery ,

		sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_input ,
		sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input ,

		sum(case when d.production_type=5 and e.production_type=5 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_output ,
		sum(case when d.production_type=5 and e.production_type=5   then e.production_qnty else 0 end ) as total_sewing_output ,

		sum(case when d.production_type=8 and e.production_type=8 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_finishing ,
		sum(case when d.production_type=8 and e.production_type=8   then e.production_qnty else 0 end ) as total_finishing,

		sum(case when d.production_type=8 and e.production_type=8 and d.production_date=$txt_production_date then d.carton_qty else 0 end ) as today_carton_qty,
		sum(case when d.production_type=8 and e.production_type=8  then d.carton_qty else 0 end ) as total_carton_qty

		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $sql_cond_prod $po_cond2 and d.production_type in(1,2,3,4,5,8,11) 
		group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  ,  c.color_number_id,e.cut_no ";

		// echo $order_sql;die;
		$order_sql_res = sql_select($order_sql);
		$cutting_sewing_data = array();
		$buyer_wise_cutting_sewing_data = array();
		foreach($order_sql_res as $vals)
		{
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_cutting"]+=$vals[csf("today_cutting")];			 

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_cutting"]+=$vals[csf("total_cutting")];
			 

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_issue_to_print"]+=$vals[csf("today_issue_to_print")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_issue_to_print"]+=$vals[csf("total_issue_to_print")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_rcv_frm_print"]+=$vals[csf("today_rcv_frm_print")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_rcv_frm_print"]+=$vals[csf("total_rcv_frm_print")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["print_reject"]+=$vals[csf("print_reject")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_issue_to_embroidery"]+=$vals[csf("today_issue_to_embroidery")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_issue_to_embroidery"]+=$vals[csf("total_issue_to_embroidery")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_rcv_frm_embroidery"]+=$vals[csf("today_rcv_frm_embroidery")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_rcv_frm_embroidery"]+=$vals[csf("total_rcv_frm_embroidery")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["reject_embroidery"]+=$vals[csf("reject_embroidery")];


			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

			 
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_output"]+=$vals[csf("today_sewing_output")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_output"]+=$vals[csf("total_sewing_output")];


			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][0]["today_finishing"]+=$vals[csf("today_finishing")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][0]["total_finishing"]+=$vals[csf("total_finishing")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_carton_qty"]+=$vals[csf("today_carton_qty")];
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_carton_qty"]+=$vals[csf("total_carton_qty")];
			// ======================================== BUYER WISE SUM ======================================================
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_cutting"]			+= $vals[csf("today_cutting")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_cutting"]			+= $vals[csf("total_cutting")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_issue_to_print"] 	+= $vals[csf("today_issue_to_print")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_issue_to_print"] 	+= $vals[csf("total_issue_to_print")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_rcv_frm_print"] 	+= $vals[csf("today_rcv_frm_print")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_rcv_frm_print"] 	+= $vals[csf("total_rcv_frm_print")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["print_reject"]	 		+= $vals[csf("print_reject")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_issue_to_embroidery"] 	+= $vals[csf("today_issue_to_embroidery")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_issue_to_embroidery"] 	+= $vals[csf("total_issue_to_embroidery")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_rcv_frm_embroidery"] 	+= $vals[csf("today_rcv_frm_embroidery")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_rcv_frm_embroidery"] 	+= $vals[csf("total_rcv_frm_embroidery")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["reject_embroidery"]	 		+= $vals[csf("reject_embroidery")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_sewing_input"] 	+= $vals[csf("today_sewing_input")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_sewing_input"] 	+= $vals[csf("total_sewing_input")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_sewing_output"] 	+= $vals[csf("today_sewing_output")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_sewing_output"] 	+= $vals[csf("total_sewing_output")];	
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_finishing"]		+= $vals[csf("today_finishing")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_finishing"] 		+= $vals[csf("total_finishing")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["today_carton_qty"] 		+= $vals[csf("today_carton_qty")];
			$buyer_wise_cutting_sewing_data[$vals[csf("buyer_name")]]["total_carton_qty"] 		+= $vals[csf("total_carton_qty")];
		}
		// echo "<pre>";
		// print_r($cutting_sewing_data);die;

		// =========================================== FOR CUTTING LAY QTY ==========================================
	  	$po_cond3=str_replace("po_id_string", "c.order_id", $po_cond); 	
	  	$lay_sqls="SELECT  a.job_no,c.order_id, b.gmt_item_id,b.color_id,sum( case when a.entry_date=$txt_production_date then  c.size_qty else 0 end) as today_lay,sum(c.size_qty) as total_lay,d.buyer_name from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c,wo_po_details_master d where a.job_no=d.job_no and a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond3  $lay_cond  group by a.job_no,c.order_id, b.gmt_item_id,b.color_id,d.buyer_name ";
	  	// echo $lay_sqls;die();
		$lay_qnty_array=array();
		$buyer_wise_lay_qnty_array=array();
		foreach(sql_select($lay_sqls) as $vals)
		{
			$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["today_lay"]+=$vals[csf("today_lay")];
			$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("total_lay")];
			$buyer_wise_lay_qnty_array[$vals[csf("buyer_name")]]["today_lay"]+=$vals[csf("today_lay")];
			$buyer_wise_lay_qnty_array[$vals[csf("buyer_name")]]["total_lay"]+=$vals[csf("total_lay")];
		}
		// echo "<pre>";
		// print_r($buyer_wise_lay_qnty_array);
		// echo "</pre>";
		// =========================================== FOR CUTTING QC ==========================================
	  	$po_cond_qc=str_replace("po_id_string", "b.order_id", $po_cond); 	
	  	$qc_sqls="SELECT  a.job_no,b.order_id, c.item_number_id,b.color_id,d.buyer_name,
	  	sum( case when a.entry_date=$txt_production_date then  b.qc_pass_qty else 0 end) as today_qc,
	  	sum(b.qc_pass_qty) as total_qc,
	  	sum(b.reject_qty) as total_rej 
	  	from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b,wo_po_color_size_breakdown c,wo_po_details_master d
	  	where d.job_no=a.job_no and a.id=b.mst_id and b.color_size_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 $po_cond_qc $qc_cond
	  	group by a.job_no,b.order_id, c.item_number_id,b.color_id,d.buyer_name";
	  	// echo $qc_sqls;die();
		$qc_qnty_array=array();
		$buyer_wise_qc_qnty_array=array();
		foreach(sql_select($qc_sqls) as $vals)
		{
			$qc_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("item_number_id")]][$vals[csf("color_id")]]["today_qc"]+=$vals[csf("today_qc")];
			$qc_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("item_number_id")]][$vals[csf("color_id")]]["total_qc"]+=$vals[csf("total_qc")];
			$qc_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("item_number_id")]][$vals[csf("color_id")]]["total_rej"]+=$vals[csf("total_rej")];
			$buyer_wise_qc_qnty_array[$vals[csf('buyer_name')]]["today_qc"]+=$vals[csf("today_qc")];
			$buyer_wise_qc_qnty_array[$vals[csf('buyer_name')]]["total_qc"]+=$vals[csf("total_qc")];
			$buyer_wise_qc_qnty_array[$vals[csf('buyer_name')]]["total_rej"]+=$vals[csf("total_rej")];
		}

		// ==================================================== FOR EX-FACTORY QTY ==========================================
		$po_cond4=str_replace("po_id_string", "a.po_break_down_id", $po_cond); 	
		$ex_factory_arr=array();
		$buyer_wise_ex_factory_arr=array();
		$ex_factory_data="SELECT a.po_break_down_id, a.item_number_id,c.color_number_id,d.buyer_id, sum(CASE WHEN a.entry_form!=85 and ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS today_ex_fac , sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c where d.id=a.delivery_mst_id  and  a.id=b.mst_id and b.color_size_break_down_id=c.id   and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $exfact_cond  $po_cond4  group by a.po_break_down_id, a.item_number_id,c.color_number_id,d.buyer_id";
		// echo $ex_factory_data;
		$ex_factory_data_res = sql_select($ex_factory_data);
		foreach($ex_factory_data_res as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['today_ex_fac']+=$exRow[csf('today_ex_fac')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['total_ex_fac']=+$exRow[csf('total_ex_fac')];
			$buyer_wise_ex_factory_arr[$exRow[csf('buyer_id')]]['today_ex_fac']+=$exRow[csf('today_ex_fac')];
			// $buyer_wise_ex_factory_arr[$exRow[csf('buyer_id')]]['total_ex_fac']+=$exRow[csf('total_ex_fac')];
		}
		// echo "<pre>";
		// print_r($ex_factory_arr);
		// echo "</pre>";
		// die();
		// =========================================== FOR FIN. FAB. REQ. QTY ========================================================
		$booking_no_fin_qnty_array=array();
		$buyer_booking_no_fin_qnty_array=array();
		$booking_sql=sql_select("SELECT a.po_break_down_id as po_id ,b.item_number_id as item_id, b.color_number_id as color_id,a.booking_no,a.fin_fab_qnty,c.buyer_name from wo_booking_dtls a,wo_po_color_size_breakdown b,wo_po_details_master c  where c.job_no=a.job_no and b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1  $po_cond4");
		foreach($booking_sql as $vals)
		{
			$booking_no_fin_qnty_array[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("color_id")]]+=$vals[csf("fin_fab_qnty")];
			// $buyer_booking_no_fin_qnty_array[$vals[csf("buyer_name")]]+=$vals[csf("fin_fab_qnty")];

		}
		// ============================================== FOR FAB. RCV AND ISSUE QTY ==============================================
		$po_cond_fab=str_replace("po_id_string", "c.po_breakdown_id", $po_cond); 
		// $fab_sql="SELECT po_breakdown_id,color_id,entry_form,sum(quantity) as quantity from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(18,37) $po_cond_fab group by po_breakdown_id,color_id,entry_form ";
		$fab_sql="SELECT c.po_breakdown_id,c.color_id,c.entry_form,sum(c.quantity) as quantity,a.buyer_name from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(18,37) $po_cond_fab group by c.po_breakdown_id,c.color_id,c.entry_form,a.buyer_name ";
		$fab_sql_res = sql_select($fab_sql);
		$issue_qnty_arr = array();
		$buyer_issue_qnty_arr = array();
		foreach($fab_sql_res as $values)
		{
		 	$issue_qnty_arr[$values[csf("po_breakdown_id")]][$values[csf("color_id")]][$values[csf("entry_form")]]+=$values[csf("quantity")];
		 	// $buyer_issue_qnty_arr[$values[csf("buyer_name")]][$values[csf("entry_form")]]+=$values[csf("quantity")];
		} 
		// ======================================= FOR COSTING PER ======================================
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$all_job = "'".implode("','", $all_job_array)."'";


		/******************************************************************************************************
		*																									  *
		*								GETTING PRICE QUOTATION WISE CM VALU							      *
		*																									  *			
		*******************************************************************************************************/
		$quotation_qty_sql="SELECT a.id  as quotation_id,a.mkt_no,a.sew_smv,a.sew_effi_percent,a.gmts_item_id,a.company_id,a.buyer_id,a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom,a.offer_qnty, a.total_set_qnty as ratio, a.quot_date,a.est_ship_date,b.costing_per_id,b.price_with_commn_pcs,b.total_cost,b.costing_per_id,c.job_no from wo_price_quotation a,wo_price_quotation_costing_mst b,wo_po_details_master c  where a.id=b.quotation_id and a.id=c.quotation_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.offer_qnty>0 and c.job_no in($all_job) order  by a.id ";
		// echo $quotation_qty_sql;die();
		$quotation_qty_sql_res = sql_select($quotation_qty_sql);
		$quotation_qty_array = array();
		$quotation_id_array = array();
		$all_jobs_array = array();
		$jobs_wise_quot_array = array();
		foreach ($quotation_qty_sql_res as $val) 
		{
			$quotation_qty_array[$val['JOB_NO']]['QTY_PCS'] += $val['OFFER_QNTY']*$val['RATIO'];
			$quotation_qty_array[$val['JOB_NO']]['COSTING_PER_ID'] += $val['COSTING_PER_ID'];
			$quotation_id_array[$val['QUOTATION_ID']] = $val['QUOTATION_ID'];
			$all_jobs_array[$val['JOB_NO']] = $val['JOB_NO'];
			$jobs_wise_quot_array[$val['JOB_NO']] = $val['QUOTATION_ID'];

			$quot_wise_arr[$val[csf("quotation_id")]]['offer_qnty']=$val[csf("offer_qnty")];
			$quot_wise_arr[$val[csf("quotation_id")]]['costing_per_id']=$val[csf("costing_per_id")];

			$style_wise_arr[$val[csf("job_no")]]['costing_per']=$val[csf("costing_per")];
			$style_wise_arr[$val[csf("job_no")]]['gmts_item_id']=$val[csf("gmts_item_id")];
			$style_wise_arr[$val[csf("job_no")]]['sew_smv']=$val[csf("sew_smv")];
			$style_wise_arr[$val[csf("job_no")]]['sew_effi_percent']=$val[csf("sew_effi_percent")];
			$style_wise_arr[$val[csf("job_no")]]['shipment_date'].=$val[csf('est_ship_date')].',';
			$style_wise_arr[$val[csf("job_no")]]['quotation_id']=$val[csf("quotation_id")];
			$style_wise_arr[$val[csf("job_no")]]['buyer_name']=$val[csf("buyer_id")];
			$offer_qnty_pcs=$val[csf('offer_qnty')]*$val[csf('ratio')];
			$style_wise_arr[$val[csf("job_no")]]['qty_pcs']+=$val[csf('offer_qnty')]*$val[csf('ratio')];
			$style_wise_arr[$val[csf("job_no")]]['qty']+=$val[csf('offer_qnty')];
			$style_wise_arr[$val[csf("job_no")]]['final_cost_pcs']+=$val[csf('price_with_commn_pcs')];
			$style_wise_arr[$val[csf("job_no")]]['total_cost']+=$offer_qnty_pcs*$val[csf('price_with_commn_pcs')];
		}	
		$all_quot_id = implode(",", $quotation_id_array);
		
		// print_r($style_wise_arr);die();
		// ===============================================================================
		$sql_fab = "SELECT a.quotation_id,sum(a.avg_cons) as cons_qnty, sum(a.amount) as amount,b.job_no from wo_pri_quo_fabric_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.fabric_source=2 and a.status_active=1 and b.status_active=1 group by  a.quotation_id,b.job_no"; 
		// echo $sql_fab;die();
		$data_array_fab=sql_select($sql_fab);
		foreach($data_array_fab as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$fab_order_price_per_dzn=12;}
			else if($costing_per_id==2){$fab_order_price_per_dzn=1;}
			else if($costing_per_id==3){$fab_order_price_per_dzn=24;}
			else if($costing_per_id==4){$fab_order_price_per_dzn=36;}
			else if($costing_per_id==5){$fab_order_price_per_dzn=48;}

			$fab_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn']+=$row[csf("amount")];
			$fab_summary_data[$row[csf("job_no")]]['fab_amount_total_value']+=($row[csf("amount")]/$fab_order_price_per_dzn)*$fab_order_job_qnty;
			//$yarn_amount_dzn+=$row[csf('amount')];
		}
		// ==================================================================================
		$sql_yarn = "SELECT a.quotation_id,sum(a.cons_qnty) as cons_qnty, sum(a.amount) as amount,b.job_no from wo_pri_quo_fab_yarn_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by  a.quotation_id,b.job_no"; 
		// echo $sql_yarn;die();
		$data_array_yarn=sql_select($sql_yarn);
		foreach($data_array_yarn as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$yarn_order_price_per_dzn=12;}
			else if($costing_per_id==2){$yarn_order_price_per_dzn=1;}
			else if($costing_per_id==3){$yarn_order_price_per_dzn=24;}
			else if($costing_per_id==4){$yarn_order_price_per_dzn=36;}
			else if($costing_per_id==5){$yarn_order_price_per_dzn=48;}
			$yarn_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			//$yarn_summary_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
			 $yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn']+=$row[csf("amount")];
			// $summary_data['yarn_amount_dzn']+=$yarn_summary_dzn;
			 //$yarn_summary_data['yarn_amount_total_value']+=($row[csf("amount")]/$yarn_order_price_per_dzn)*$yarn_order_job_qnty;
		}
		// ===================================================================================
		$conversion_cost_arr=array();
		$sql_conversion = "SELECT a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition,c.job_no
		from wo_po_details_master c, wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
		where a.quotation_id in($all_quot_id) and a.quotation_id=c.quotation_id and a.status_active=1  ";
		// echo $sql_conversion;die();
		$data_array_conversion=sql_select($sql_conversion);
		foreach($data_array_conversion as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$conv_order_price_per_dzn=12;}
			else if($costing_per_id==2){$conv_order_price_per_dzn=1;}
			else if($costing_per_id==3){$conv_order_price_per_dzn=24;}
			else if($costing_per_id==4){$conv_order_price_per_dzn=36;}
			else if($costing_per_id==5){$conv_order_price_per_dzn=48;}
			$conv_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn']+=$row[csf("amount")];

			$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_dzn']+=$row[csf('amount')];
			$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$conv_order_job_qnty;
		}
		// print_r($conversion_cost_arr);die();
		if($db_type==0)
		{
			$sql = "SELECT MAX(a.id),a.quotation_id,a.fabric_cost,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,a.offer_qnty,b.job_no
			from wo_price_quotation_costing_mst a,wo_po_details_master b
			where a.quotation_id in($all_quot_id) and a.status_active=1 and a.quotation_id=b.quotation_id and b.status_active=1 ";
		}
		if($db_type==2)
		{
			$sql = "SELECT MAX(a.id),a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no
			from wo_price_quotation_costing_mst a,wo_po_details_master b
			where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and b.status_active=1   group by a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no";
		}
		// echo $sql;die();
		$data_array=sql_select($sql);

        foreach( $data_array as $row )
        {
			//$sl=$sl+1;
			if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
			else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
			else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
			else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
			else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn']=$order_price_per_dzn;
			$price_dzn=$row[csf("confirm_price_dzn")];
			$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
			$summary_data[$row[csf('job_no')]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
			$summary_data[$row[csf('job_no')]]['price_with_total_value']+=($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['price_dzn']+=$row[csf("confirm_price_dzn")];
		    $summary_data[$row[csf('job_no')]]['commission_dzn']+=$row[csf("commission")];
			$summary_data[$row[csf('job_no')]]['commission_total_value']+=($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['trims_cost_dzn']+=$row[csf("trims_cost")];
			$summary_data[$row[csf('job_no')]]['trims_cost_total_value']+=($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['embel_cost_dzn']+=$row[csf("embel_cost")];
			$summary_data[$row[csf('job_no')]]['embel_cost_total_value']+=($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty;
			//$row[csf("commission")]
			$other_direct_expenses=$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];

			$summary_data[$row[csf('job_no')]]['other_direct_dzn']+=$other_direct_expenses;
			$summary_data[$row[csf('job_no')]]['other_direct_total_value']+=($other_direct_expenses/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['wash_cost_dzn']+=$row[csf("wash_cost")];
			$summary_data[$row[csf('job_no')]]['wash_cost_total_value']+=($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['lab_test_dzn']+=$row[csf("lab_test")];
			$summary_data[$row[csf('job_no')]]['lab_test_total_value']+=($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['inspection_dzn']+=$row[csf("inspection")];
			$summary_data[$row[csf('job_no')]]['inspection_total_value']+=($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['freight_dzn']+=$row[csf("freight")];
			$summary_data[$row[csf('job_no')]]['freight_total_value']+=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
			$freight_cost_data[$row[csf("job_no")]]['freight_total_value']=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['currier_pre_cost_dzn']+=$row[csf("currier_pre_cost")];
			$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']+=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['certificate_pre_cost_dzn']+=$row[csf("certificate_pre_cost")];
			$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']+=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['design_pre_cost_dzn']+=$row[csf("design_pre_cost")];
			$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value']+=($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['studio_pre_cost_dzn']+=$row[csf("studio_pre_cost")];
			$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']+=($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['studio_dzn_cost']=$row[csf("studio_percent")];
			$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['common_oh']=$row[csf("common_oh")];

			$fab_amount_dzn=$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn'];
			$summary_data[$row[csf('job_no')]]['fab_amount_dzn']+=$fab_amount_dzn;
			$summary_data[$row[csf('job_no')]]['fab_amount_total_value']+=($fab_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
			$yarn_amount_dzn=$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn'];
			//echo ($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty.'d';
			$summary_data[$row[csf('job_no')]]['yarn_amount_dzn']+=$yarn_amount_dzn;
			$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']+=($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
			$conv_amount_dzn=$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn'];
			$summary_data[$row[csf('job_no')]]['conversion_cost_dzn']+=$conv_amount_dzn;
			$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']+=($conv_amount_dzn/$order_price_per_dzn)*$order_job_qnty;

			//$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
			$net_value_dzn=$row[csf("price_with_commn_dzn")];

			$summary_data[$row[csf('job_no')]]['netfobvalue_dzn']+=($row[csf("price_with_commn_dzn")]);
			$summary_data[$row[csf('job_no')]]['netfobvalue']+=(($row[csf("price_with_commn_dzn")])/$order_price_per_dzn)*$order_job_qnty;

			//yarn_amount_total_value
			$all_cost_dzn=$yarn_amount_dzn+$fab_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses;
			//echo $yarn_amount_dzn.'Y='.$fab_amount_dzn.'F='.$conv_amount_dzn.'Cnv='.$row[csf("trims_cost")].'Tr='.$row[csf("embel_cost")].'Em='.$other_direct_expenses;
			$summary_data[$row[csf('job_no')]]['cost_of_material_service']+=$all_cost_dzn;
			$summary_data[$row[csf('job_no')]]['cost_of_material_service_total_value']+=($all_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
			$contribute_netfob_value_dzn=$net_value_dzn-($fab_amount_dzn+$yarn_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses);
			$summary_data[$row[csf('job_no')]]['contribution_margin_dzn']+=$contribute_netfob_value_dzn;
			$summary_data[$row[csf('job_no')]]['contribution_margin_total_value']+=(($contribute_netfob_value_dzn)/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['cm_cost_dzn']+=$row[csf("cm_cost")];
			$summary_data[$row[csf('job_no')]]['cm_cost_total_value']+=($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['comm_cost_dzn']+=$row[csf("comm_cost")];
			$summary_data[$row[csf('job_no')]]['comm_cost_total_value']+=($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['common_oh_dzn']+=$row[csf("common_oh")];
			$summary_data[$row[csf('job_no')]]['common_oh_total_value']+=($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty;
			//echo $netfob_value_dzn.'='.$row[csf("cm_cost")];
			$Contribution_Margin=$netfob_value_dzn-$LessCostOfMaterialServices;
			$tot_gross_profit_dzn=$contribute_netfob_value_dzn-$row[csf("cm_cost")];
			$summary_data[$row[csf('job_no')]]['gross_profit_dzn']+=$tot_gross_profit_dzn;
			$summary_data[$row[csf('job_no')]]['gross_profit_total_value']+=(($tot_gross_profit_dzn)/$order_price_per_dzn)*$order_job_qnty;

			//$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
			$operate_profit_loss_dzn=$tot_gross_profit_dzn;//-($row[csf("comm_cost")]+$row[csf("common_oh")]);
			$summary_data[$row[csf('job_no')]]['operating_profit_loss_dzn']+=$operate_profit_loss_dzn;
			$summary_data[$row[csf('job_no')]]['operating_profit_loss_total_value']+=($operate_profit_loss_dzn/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_dzn']+=$row[csf("depr_amor_pre_cost")];
			$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_total_value']+=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['interest_pre_cost_dzn']+=$row[csf("interest_pre_cost")];
			$summary_data[$row[csf('job_no')]]['interest_pre_cost_total_value']+=($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_dzn']+=$row[csf("income_tax_pre_cost")];
			$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_total_value']+=($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$net_profit_dzn=$operate_profit_loss_dzn-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);
			$summary_data[$row[csf('job_no')]]['net_profit_dzn']+=$net_profit_dzn;
			$summary_data[$row[csf('job_no')]]['net_profit_dzn_total_value']+=($net_profit_dzn/$order_price_per_dzn)*$order_job_qnty;
		}
		// echo "<pre>";
		// print_r($summary_data);
		// die();
		//======================================================================

		$sql_commi = "SELECT a.id,a.quotation_id,a.particulars_id,a.commission_base_id,a.commision_rate,a.commission_amount,a.status_active,b.job_no
		from  wo_pri_quo_commiss_cost_dtls a,wo_po_details_master b
		where  a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and a.commission_amount>0 and b.status_active=1";
		// echo $sql_commi;die();
		$result_commi=sql_select($sql_commi);
		$CommiData_foreign_cost=$CommiData_lc_cost=$foreign_dzn_commission_amount=$local_dzn_commission_amount=0;
		foreach($result_commi as $row)
		{
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];

			if($row[csf("particulars_id")]==1) //Foreign
			{
				$CommiData_foreign_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
				$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
				$CommiData_foreign_quot_cost_arr[$row[csf("job_no")]]+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
			else
			{
				$CommiData_lc_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
				$local_dzn_commission_amount+=$row[csf("commission_amount")];
				$commision_local_quot_cost_arr[$row[csf("job_no")]]=$row[csf("commision_rate")];
			}
		}
		//=====================================================================================
		$sql_comm="SELECT a.item_id,a.quotation_id,sum(a.amount) as amount,b.job_no from wo_pri_quo_comarcial_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by a.quotation_id,a.item_id,b.job_no";
		// echo $sql_comm;die();
		$tot_lc_dzn_Commer=$tot_without_lc_dzn_Commer=0;
		// $summary_data['comm_cost_dzn']=0;
		// $summary_data['comm_cost_total_value']=0;
		$result_comm=sql_select($sql_comm);
		$commer_lc_cost = array();
		$commer_without_lc_cost = array();
		foreach($result_comm as $row)
		{
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
			//$summary_data['comm_cost_dzn']=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			$comm_amtPri=$row[csf('amount')];
			$item_id=$row[csf('item_id')];
			if($item_id==1)//LC
			{
				$commer_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;

				$commer_lc_cost_quot_arr[$row[csf("job_no")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
			else
			{
				$commer_without_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
		}
		// echo "<pre>";print_r($summary_data);die();
		/********************************************************************************************************
		*																										*
		*													END													*	
		*																										*
		********************************************************************************************************/

		// =========================================== FOR ROWSPAN ===============================================
		$buyer_wise_cm_fob_calculate = array();
		$rowspan_arr = array();
		$chk_buyer  = array();
		foreach ($production_main_array as $style_key => $style_arr) 
		{
			foreach ($style_arr as $job_key => $job_arr) 
			{
				foreach ($job_arr as $po_key => $po_arr) 
				{
					foreach ($po_arr as $item_key => $item_arr) 
					{
						foreach ($item_arr as $color_key => $row) 
						{
							$rowspan_arr[$style_key][$job_key][$po_key][$item_key]++;

							$buyer_booking_no_fin_qnty_array[$row['buyer_name']]+=$booking_no_fin_qnty_array[$po_key][$item_key][$color_key];
							$buyer_issue_qnty_arr[$row['buyer_name']][18] += $issue_qnty_arr[$po_key][$color_key][18];
							$buyer_issue_qnty_arr[$row['buyer_name']][37] += $issue_qnty_arr[$po_key][$color_key][37];

							// calculate summary cm and fob
							$today_sewing_output_qty = $cutting_sewing_data[$job_key][$style_key][$po_key][$item_key][$color_key]["today_sewing_output"];
							$total_sewing_output_qty = $cutting_sewing_data[$job_key][$style_key][$po_key][$item_key][$color_key]["total_sewing_output"];
							$today_ex_fac_qty=$ex_factory_arr[$po_key][$item_key][$color_key]['today_ex_fac'];
							$total_ex_fac_qty=$ex_factory_arr[$po_key][$item_key][$color_key]['total_ex_fac'];

							$tot_bat_qty = $total_sewing_output_qty - $total_ex_fac_qty;
							$buyer_wise_ex_factory_arr[$row['buyer_name']]['total_ex_fac']+=$total_ex_fac_qty;
							$buyer_wise_ex_factory_arr[$row['buyer_name']]['total_bal_qty']+= $tot_bat_qty;
							$buyer_wise_ex_factory_arr[$row['buyer_name']]['total_fob_qty']+= ($tot_bat_qty*$row['unit_price']);

							$unit_price = $row['unit_price'];
							$today_fob_val = $today_sewing_output_qty * $unit_price;
							$total_fob_val = $total_sewing_output_qty * $unit_price;
							$today_exf_fob_val = $today_ex_fac_qty * $unit_price;
							$total_exf_fob_val = $total_ex_fac_qty * $unit_price;

							$costing_per=$costing_per_arr[$job_key];
							if($costing_per==1) $dzn_qnty=12;
							else if($costing_per==3) $dzn_qnty=12*2;
							else if($costing_per==4) $dzn_qnty=12*3;
							else if($costing_per==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;	
							$dzn_qnty=$dzn_qnty*$row['total_set_qnty'];
							$cm_gmt_cost_dzn=$cm_gmt_cost_dzn_arr_new[$po_key]['dzn'];
							//echo $cm_gmt_cost_dzn.'DD'.$po_id.', ';
							//$cm_per_pcs=(($unit_price*$dzn_qnty)-$total_cost_arr[$job_id])+$cm_cost_arr[$job_id];
							$cm_per_pcs=$cm_gmt_cost_dzn/$dzn_qnty;
							$today_cm_val = $today_sewing_output_qty * $cm_per_pcs;
							$total_cm_val = $total_sewing_output_qty * $cm_per_pcs;

							$today_exf_cm_val = $today_ex_fac_qty * $cm_per_pcs;
							$total_exf_cm_val = $total_ex_fac_qty * $cm_per_pcs;
							/*========================================================================================
							*																						  *
							*								Calculate cm valu 										  *	
							*																					  	  *	
							*========================================================================================*/
							$tot_dye_chemi_process_amount 	= $conversion_cost_arr[$job_key][101]['conv_amount_total_value'];
							$tot_yarn_dye_process_amount 	= $conversion_cost_arr[$job_key][30]['conv_amount_total_value'];
							$tot_aop_process_amount 		= $conversion_cost_arr[$job_key][35]['conv_amount_total_value'];
							
							$total_quot_qty=$total_quot_pcs_qty=$total_quot_amount=$total_sew_smv=$total_quot_amount_cal=0;
							$all_last_shipdates='';

				            foreach($style_wise_arr as $style_key=>$val)
							{	
								$total_cost=$val[('qty')]*$val[('final_cost_pcs')];
								$total_quot_qty+=$val[('qty')];
								$total_quot_pcs_qty+=$val[('qty_pcs')];
								$total_sew_smv+=$val[('sew_smv')];
								$total_quot_amount+=$total_cost;
								$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
							}
							$total_quot_amount_cal = $style_wise_arr[$job_key]['qty']*$style_wise_arr[$job_key]['final_cost_pcs'];
							$tot_cm_for_fab_cost=$summary_data[$job_key]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
							// echo $job_key."==".$summary_data[$job_key]['conversion_cost_total_value']."-(".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$tot_aop_process_amount.")<br>";
							$commision_quot_local=$commision_local_quot_cost_arr[$job_key];
							$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$job_key]+$commer_lc_cost_quot_arr[$job_key]+$freight_cost_data[$job_key]['freight_total_value']);
							$tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
							$tot_inspect_cour_certi_cost=$summary_data[$job_key]['inspection_total_value']+$summary_data[$job_key]['currier_pre_cost_total_value']+$summary_data[$job_key]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$job_key]['design_pre_cost_total_value'];
							// echo $summary_data[$job_key]['inspection_total_value']."+".$summary_data[$job_key]['currier_pre_cost_total_value']."+".$summary_data[$job_key]['certificate_pre_cost_total_value']."+".$tot_sum_amount_quot_calccc."+".$summary_data[$job_key]['design_pre_cost_total_value']."<br>";

							$tot_emblish_cost=$summary_data[$job_key]['embel_cost_total_value'];
							$pri_freight_cost_per=$summary_data[$job_key]['freight_total_value'];
							$pri_commercial_per=$commer_lc_cost[$job_key];
							$CommiData_foreign_cost=$CommiData_foreign_quot_cost_arr[$job_key];

							$total_btb=$summary_data[$job_key]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$job_key]['comm_cost_total_value']+$summary_data[$job_key]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$job_key]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$job_key]['common_oh_total_value']+$summary_data[$job_key]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
							// echo $summary_data[$job_key]['lab_test_total_value']."+".$tot_emblish_cost."+".$summary_data[$job_key]['comm_cost_total_value']."+".$summary_data[$job_key]['trims_cost_total_value']."+".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$summary_data[$job_key]['yarn_amount_total_value']."+".$tot_aop_process_amount."+".$summary_data[$job_key]['common_oh_total_value']."+".$summary_data[$job_key]['studio_pre_cost_total_value']."+".$tot_inspect_cour_certi_cost."<br>";
							$tot_quot_sum_amount=$total_quot_amount-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
							// echo $total_quot_amount."-(".$CommiData_foreign_cost."+".$pri_freight_cost_per."+".$pri_commercial_per.")<br>";
							$NetFOBValue_job = $tot_quot_sum_amount;
							// echo $NetFOBValue_job."<br>";
							$total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);

							$total_quot_pcs_qty = $quotation_qty_array[$job_key]['QTY_PCS'];
							// echo $total_cm_for_gmt;echo "<br>";
							$cm_valu_lc = 0;
							$cm_valu_lc = $total_cm_for_gmt/$total_quot_pcs_qty;
							$today_cm_val_lc = $today_sewing_output_qty * $cm_valu_lc;
							$total_cm_val_lc = $total_sewing_output_qty * $cm_valu_lc;

							$today_exf_cm_val_lc = $today_ex_fac_qty * $cm_valu_lc;
							$total_exf_cm_val_lc = $total_ex_fac_qty * $cm_valu_lc;
							// echo number_format(($total_cm_for_gmt/$total_quot_pcs_qty)*12,2); echo "<br>";
							/*========================================================================================
							*																						  *
							*											END											  *	
							*																					  	  *	
							*========================================================================================*/			

							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_cm_val'] 		+= $today_cm_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_cm_val'] 		+= $total_cm_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_cm_val_lc']		+= $today_cm_val_lc;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_cm_val_lc'] 	+= $total_cm_val_lc;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_fob_val'] 		+= $today_fob_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_fob_val'] 		+= $total_fob_val;

							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_exf_cm_val'] 	+= $today_exf_cm_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_exf_cm_val'] 	+= $total_exf_cm_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_exf_cm_val_lc']	+= $today_exf_cm_val_lc;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_exf_cm_val_lc']	+= $total_exf_cm_val_lc;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['today_exf_fob_val'] 	+= $today_exf_fob_val;
							$buyer_wise_cm_fob_calculate[$row['buyer_name']]['total_exf_fob_val'] 	+= $total_exf_fob_val;
						}
					}
				}
			}
		}
		// echo "<pre>";
		// print_r($rowspan_arr);
		// echo "</pre>";
		
		
		ob_start();	
		$summary_html = '';
					 
        $summary_html.='<div style="padding: 5px 10px;">
        <div id="summary_part">
        	<table width="3300" cellspacing="0" >
        		<tr style="border:none;">
        			<td colspan="30" align="center" style="border:none; font-size:24px;">
        				<strong>';?><? $comp_names=""; 
        					foreach(explode(",",$working_company_id) as $vals) 
        					{
        						$comp_names.=($comp_names !="") ? ' , '.$company_library[$vals] : $company_library[$vals];
        					}
        					$summary_html.=$comp_names.'
        					
        				</strong>                                
        			</td>
        		</tr>
        		<tr class="form_caption" style="border:none;">
        			<td colspan="30" align="center" style="border:none;font-size:14px; font-weight:bold" ><strong>
        				Floor wise Daily RMG Production Report
        			</strong></td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="30" align="center" style="border:none;font-size:14px; font-weight:bold" >'?>
        				<?
        				$dates=str_replace("'","",trim($txt_production_date));
        				if($dates)
        				{
        					$summary_html.='Date '.change_date_format($dates)  ;
        				}?>
        				
        			<? $summary_html.='</td>
        		</tr>
        	</table>';?>
        	<!-- =========================================== SUMMARY PART START ====================================== -->
        	<? $summary_html.='<div style="margin-bottom: 20px;">
        		<table width="2685" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="" align="left">
        			<caption style="text-align: center;font-weight: bold;font-size: 18px;">Summary Part</caption>
					<thead>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30" ><p>SL</p>
							</th>

							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Buyer</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="70"><p>Order Qty</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Fabric Status</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Cut Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Cutting QC</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Delivery To Print</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Receive from Print</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Delivery To Embroidery</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Receive from Embroidery</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Input Status</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Sewing Output</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Finishing</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Carton</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Ex-Factory</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="115"><p>Sewing - Exfactory Balance</p></th>
						</tr>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>F.Fab. Req.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab. Rcv.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab. Issue</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab.Inhand</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Cut %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Cutting %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Print WIP</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Embroidery WIP</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60" title="(Total Sewing Input / Total Cut Status)*100"><p>Input %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Sewing %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Finish %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="110"><p>Total</p></th>
						
						</tr>
						<tbody>';?>
						<?
						$sl=1;
						$buyer_total_order_qty 			= 0;
						$buyer_total_fin_fab_req 		= 0;
						$buyer_total_fab_rcv 			= 0;
						$buyer_total_fav_issue 			= 0;
						$buyer_total_fab_inhand 		= 0;
						$buyer_total_today_cut 			= 0;
						$buyer_total_cut 				= 0;
						$buyer_total_today_cutting 		= 0;
						$buyer_total_cutting 			= 0;
						$buyer_total_today_print_issue 	= 0;
						$buyer_total_print_issue 		= 0;
						$buyer_total_today_print_rcv 	= 0;
						$buyer_total_print_rcv 			= 0;
						$buyer_total_print_rej 			= 0;
						$buyer_total_today_embroidery_issue 	= 0;
						$buyer_total_embroidery_issue 		= 0;
						$buyer_total_embroidery_print_rcv 	= 0;
						$buyer_total_embroidery_rcv 			= 0;
						$buyer_total_embroidery_rej 			= 0;
						$buyer_total_today_input 		= 0;
						$buyer_total_input 				= 0;
						$buyer_total_today_output 		= 0;
						$buyer_total_output 			= 0;
						$buyer_total_today_fin 			= 0;
						$buyer_total_fin 				= 0;
						$buyer_total_today_carton 		= 0;
						$buyer_total_carton 			= 0;
						$buyer_total_today_exfact 		= 0;
						$buyer_total_exfact 			= 0;
						$buyer_total_today_export_cm 	= 0;
						$buyer_total_export_cm 			= 0;
						$buyer_total_today_export_cm_lc	= 0;
						$buyer_total_export_cm_lc		= 0;
						$buyer_total_today_export_fob 	= 0;
						$buyer_total_export_fob 		= 0;
						$buyer_total_today_sewing_cm 	= 0;
						$buyer_total_sewing_cm 			= 0;
						$buyer_total_today_sewing_cm_lc	= 0;
						$buyer_total_sewing_cm_lc		= 0;
						$buyer_total_today_sewing_fob 	= 0;
						$buyer_total_sewing_fob 		= 0;
						$buyer_total_sewing_ex_bal_qty	= 0;
						$buyer_total_sewing_ex_fob_qty	= 0;
						foreach ($buyer_wise_summary_array as $key => $val) 
						{
							$smry_order_quantity 		= $buyer_wise_order_qnty_array[$key];
							$smry_fab_required			= $buyer_booking_no_fin_qnty_array[$key];
							$smry_issue_qty 			= $buyer_issue_qnty_arr[$key][18];
							$smry_receive_qty 			= $buyer_issue_qnty_arr[$key][37];
							$smry_fab_in_hand 			= $smry_receive_qty-$smry_issue_qty;

							$smry_today_cut 			= $buyer_wise_lay_qnty_array[$key]['today_lay'];
							$smry_totalCut 				= $buyer_wise_lay_qnty_array[$key]['total_lay'];
							$smry_cutting_lay_percent 	= ($smry_totalCut / $smry_order_quantity)*100;

							$smry_today_qc 				= $buyer_wise_qc_qnty_array[$key]['today_qc'];
							$smry_totalQc 				= $buyer_wise_qc_qnty_array[$key]['total_qc'];
							$smry_totalRej 				= $buyer_wise_qc_qnty_array[$key]['total_rej'];
							$smry_cutting_qc_percent 	= ($smry_totalCut) ? ($smry_totalQc / $smry_totalCut)*100 : 0;

							$smry_today_cutting 		= $buyer_wise_cutting_sewing_data[$key]["today_cutting"];	
							$smry_total_cutting 		= $buyer_wise_cutting_sewing_data[$key]["total_cutting"];	
							$smry_today_issue_to_print 	= $buyer_wise_cutting_sewing_data[$key]["today_issue_to_print"];	
							$smry_total_issue_to_print 	= $buyer_wise_cutting_sewing_data[$key]["total_issue_to_print"];	
							$smry_today_rcv_frm_print 	= $buyer_wise_cutting_sewing_data[$key]["today_rcv_frm_print"];
							$smry_total_rcv_frm_print 	= $buyer_wise_cutting_sewing_data[$key]["total_rcv_frm_print"];	
							$smry_print_reject 			= $buyer_wise_cutting_sewing_data[$key]["print_reject"];
							$smry_print_wip 			= $smry_total_issue_to_print - $smry_total_rcv_frm_print;	
							$smry_today_issue_to_embroidery 	= $buyer_wise_cutting_sewing_data[$key]["today_issue_to_embroidery"];	
							$smry_total_issue_to_embroidery 	= $buyer_wise_cutting_sewing_data[$key]["total_issue_to_embroidery"];	
							$smry_today_rcv_frm_embroidery 	= $buyer_wise_cutting_sewing_data[$key]["today_rcv_frm_embroidery"];
							$smry_total_rcv_frm_embroidery 	= $buyer_wise_cutting_sewing_data[$key]["total_rcv_frm_embroidery"];	
							$smry_embroidery_reject 			= $buyer_wise_cutting_sewing_data[$key]["reject_embroidery"];
							$smry_embroidery_wip 			= $smry_total_issue_to_embroidery - $smry_total_rcv_frm_embroidery; 	
							$smry_today_sewing_input 	= $buyer_wise_cutting_sewing_data[$key]["today_sewing_input"];
							$smry_total_sewing_input 	= $buyer_wise_cutting_sewing_data[$key]["total_sewing_input"];
							$smry_input_percent 		= ($smry_totalCut) ? ($smry_total_sewing_input / $smry_totalCut)*100 : 0;	
							$smry_today_sewing_output 	= $buyer_wise_cutting_sewing_data[$key]["today_sewing_output"];
							$smry_total_sewing_output 	= $buyer_wise_cutting_sewing_data[$key]["total_sewing_output"];	
							$smry_output_percent 		= ($smry_total_sewing_input) ? ($smry_total_sewing_output / $smry_total_sewing_input)*100 : 0;
							$smry_today_finishing 		= $buyer_wise_cutting_sewing_data[$key]["today_finishing"];
							$smry_total_finishing 		= $buyer_wise_cutting_sewing_data[$key]["total_finishing"];
							$smry_finishing_percent 	= ($smry_total_sewing_output) ? ($smry_total_finishing / $smry_total_sewing_output)*100 : 0;
							$smry_today_carton_qty 		= $buyer_wise_cutting_sewing_data[$key]["today_carton_qty"];
							$smry_total_carton_qty 		= $buyer_wise_cutting_sewing_data[$key]["total_carton_qty"];

							$smry_today_ex_fact_qty 	= $buyer_wise_ex_factory_arr[$key]["today_ex_fac"];
							$smry_total_ex_fac_qty 		= $buyer_wise_ex_factory_arr[$key]["total_ex_fac"];

							$smry_total_ex_fac_bal_qty 	= $buyer_wise_ex_factory_arr[$key]["total_bal_qty"];
							$smry_total_ex_fac_fob_qty 	= $buyer_wise_ex_factory_arr[$key]["total_fob_qty"];

							// $smry_total_ex_fac_bal_qty 	= $smry_total_sewing_output - $smry_total_ex_fac_qty;
							// $smry_total_ex_fac_fob_qty 	= $smry_total_ex_fac_bal_qty * $val['unit_price'];

							$smry_today_fob_val 		= $buyer_wise_cm_fob_calculate[$key]['today_fob_val'];
							$smry_total_fob_val 		= $buyer_wise_cm_fob_calculate[$key]['total_fob_val'];
							$smry_today_cm_val 			= $buyer_wise_cm_fob_calculate[$key]['today_cm_val'];
							$smry_total_cm_val 			= $buyer_wise_cm_fob_calculate[$key]['total_cm_val'];
							$smry_today_cm_val_lc		= $buyer_wise_cm_fob_calculate[$key]['today_cm_val_lc'];
							$smry_total_cm_val_lc		= $buyer_wise_cm_fob_calculate[$key]['total_cm_val_lc'];


							$smry_today_exf_cm_val 		= $buyer_wise_cm_fob_calculate[$key]['today_exf_cm_val'];
							$smry_total_exf_cm_val 		= $buyer_wise_cm_fob_calculate[$key]['total_exf_cm_val'];
							$smry_today_exf_cm_val_lc	= $buyer_wise_cm_fob_calculate[$key]['today_exf_cm_val_lc'];
							$smry_total_exf_cm_val_lc	= $buyer_wise_cm_fob_calculate[$key]['total_exf_cm_val_lc'];
							$smry_today_exf_fob_val 	= $buyer_wise_cm_fob_calculate[$key]['today_exf_fob_val'];
							$smry_total_exf_fob_val 	= $buyer_wise_cm_fob_calculate[$key]['total_exf_fob_val'];

							// =========================================
							$buyer_total_order_qty 			+= $smry_order_quantity;
							$buyer_total_fin_fab_req 		+= $smry_fab_required;
							$buyer_total_fab_rcv 			+= $smry_receive_qty;
							$buyer_total_fav_issue 			+= $smry_issue_qty;
							$buyer_total_fab_inhand 		+= $smry_fab_in_hand;
							$buyer_total_today_cut 			+= $smry_today_cut;
							$buyer_total_cut 				+= $smry_totalCut;
							$buyer_total_cut_prsnt 			+= $smry_cutting_lay_percent;
							$buyer_total_today_cutting 		+= $smry_today_qc;
							$buyer_total_cutting 			+= $smry_totalQc;
							$buyer_cutting_percent 			+= $smry_cutting_qc_percent;
							$buyer_cutting_reject 			+= $smry_totalRej;
							$buyer_total_today_print_issue 	+= $smry_today_issue_to_print;
							$buyer_total_print_issue 		+= $smry_total_issue_to_print;
							$buyer_total_today_print_rcv 	+= $smry_today_rcv_frm_print;
							$buyer_total_print_rcv 			+= $smry_total_rcv_frm_print;
							$buyer_total_print_rej 			+= $smry_print_reject;
							$buyer_total_print_wip 			+= $smry_print_wip;
							$buyer_total_today_embroidery_issue 	+= $smry_today_issue_to_embroidery;
							$buyer_total_embroidery_issue 		+= $smry_total_issue_to_embroidery;
							$buyer_total_today_embroidery_rcv 	+= $smry_today_rcv_frm_embroidery;
							$buyer_total_embroidery_rcv 			+= $smry_total_rcv_frm_embroidery;
							$buyer_total_embroidery_rej 			+= $smry_embroidery_reject ;
							$buyer_total_embroidery_wip 			+= $smry_embroidery_wip;
							
							$buyer_total_today_input 		+= $smry_today_sewing_input;
							$buyer_total_input 				+= $smry_total_sewing_input;
							$buyer_input_prsent				+= $smry_input_percent;
							$buyer_total_today_output 		+= $smry_today_sewing_output;
							$buyer_total_output 			+= $smry_total_sewing_output;
							$buyer_output_percent 			+= $smry_output_percent;
							$buyer_total_today_fin 			+= $smry_today_finishing;
							$buyer_total_fin 				+= $smry_total_finishing;
							$buyer_finishing_percent 		+= $smry_finishing_percent;
							$buyer_total_today_carton 		+= $smry_today_carton_qty;
							$buyer_total_carton 			+= $smry_total_carton_qty;
							$buyer_total_today_exfact 		+= $smry_today_ex_fact_qty;
							$buyer_total_exfact 			+= $smry_total_ex_fac_qty;
							$buyer_total_today_export_cm 	+= $smry_today_exf_cm_val;
							$buyer_total_export_cm 			+= $smry_total_exf_cm_val;
							$buyer_total_today_export_cm_lc	+= $smry_today_exf_cm_val_lc;
							$buyer_total_export_cm_lc		+= $smry_total_exf_cm_val_lc;
							$buyer_total_today_export_fob 	+= $smry_today_exf_fob_val;
							$buyer_total_export_fob 		+= $smry_total_exf_fob_val;
							$buyer_total_today_sewing_cm 	+= $smry_today_cm_val;
							$buyer_total_sewing_cm 			+= $smry_total_cm_val;
							$buyer_total_today_sewing_cm_lc	+= $smry_today_cm_val_lc;
							$buyer_total_sewing_cm_lc		+= $smry_total_cm_val_lc;
							$buyer_total_today_sewing_fob 	+= $smry_today_fob_val;
							$buyer_total_sewing_fob 		+= $smry_total_fob_val;
							$buyer_total_sewing_ex_bal_qty	+= $smry_total_ex_fac_bal_qty;
							$buyer_total_sewing_ex_fob_qty	+= $smry_total_ex_fac_fob_qty;
							
							$summary_html.='<tr>
								<td align="left" style="word-wrap: break-word;word-break: break-all;">'.$sl.'</td>
								<td align="left" style="word-wrap: break-word;word-break: break-all;">'.$buyer_library[$key].'</td>

								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_order_quantity,0).'</td>

								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_fab_required,2).'</td>

								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_receive_qty,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_issue_qty,2).'</td>

								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_fab_in_hand,2).'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_cut.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_totalCut.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_cutting_lay_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_qc.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_totalQc.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_cutting_qc_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_totalRej.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_issue_to_print.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_issue_to_print.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_rcv_frm_print.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_rcv_frm_print.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_print_reject.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_print_wip.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_issue_to_embroidery.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_issue_to_embroidery.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'. $smry_today_rcv_frm_embroidery.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_rcv_frm_embroidery.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_embroidery_wip.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_print_wip.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_sewing_input.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_sewing_input.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_input_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_sewing_output.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_sewing_output.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_output_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_finishing.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_finishing.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_finishing_percent,0).'%</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_carton_qty.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_carton_qty.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_today_ex_fact_qty.'</td>
								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.$smry_total_ex_fac_qty.'</td>

								<td align="right" style="word-wrap: break-word;word-break: break-all;">'.number_format($smry_total_ex_fac_bal_qty,0).'</td>

								

							</tr>';?>
							<?
							$sl++;
						}
						?>
					<? $summary_html.='</tbody>        			
					<tfoot>
						<tr>
							<th align="right" colspan="2">Total </th>
							<th align="right">'.number_format($buyer_total_order_qty,0).'</th>
							<th align="right">'.number_format($buyer_total_fin_fab_req,2).'</th>
							<th align="right">'.number_format($buyer_total_fab_rcv,2).'</th>
							<th align="right">'.number_format($buyer_total_fav_issue,2).'</th>
							<th align="right">'.number_format($buyer_total_fab_inhand,2).'</th>
							<th align="right">'.number_format($buyer_total_today_cut,0).'</th>
							<th align="right">'.number_format($buyer_total_cut,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_total_today_cutting,0).'</th>
							<th align="right">'.number_format($buyer_total_cutting,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_cutting_reject,0).'</th>
							<th align="right">'.number_format($buyer_total_today_print_issue,0).'</th>
							<th align="right">'.number_format($buyer_total_print_issue,0).'</th>
							<th align="right">'.number_format($buyer_total_today_print_rcv,0).'</th>
							<th align="right">'.number_format($buyer_total_print_rcv,0).'</th>
							<th align="right">'.number_format($buyer_total_print_rej,0).'</th>
							<th align="right">'.number_format($buyer_total_print_wip,0).'</th>
							<th align="right">'.number_format($buyer_total_today_embroidery_issue,0).'</th>
							<th align="right">'.number_format($buyer_total_embroidery_issue,0).'</th>
							<th align="right">'.number_format($buyer_total_embroidery_print_rcv,0).'</th>
							<th align="right">'.number_format($buyer_total_embroidery_rcv,0).'</th>
							<th align="right">'.number_format($buyer_total_embroidery_rej,0).'</th>
							<th align="right">'.number_format($buyer_total_embroidery_wip,0).'</th>
							<th align="right">'.number_format($buyer_total_today_input,0).'</th>
							<th align="right">'.number_format($buyer_total_input,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_total_today_output,0).'</th>
							<th align="right">'.number_format($buyer_total_output,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_total_today_fin,0).'</th>
							<th align="right">'.number_format($buyer_total_fin,0).'</th>
							<th align="right"></th>
							<th align="right">'.number_format($buyer_total_today_carton,0).'</th>
							<th align="right">'.number_format($buyer_total_carton,0).'</th>
							<th align="right">'.number_format($buyer_total_today_exfact,0).'</th>
							<th align="right">'.number_format($buyer_total_exfact,0).'</th>

							<th align="right">'.number_format($buyer_total_sewing_ex_bal_qty,0).'</th>
						</tr>
					</tfoot>
        		</table>
        	</div></div>';?>
        	<? echo $summary_html; ?>
        	<br clear="all">
        	<!-- ===================================== DETAILS PART START ===================================== -->
			<div>
				<table width="3500" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left">
					<caption style="text-align: center;font-weight: bold;font-size: 18px;">Details Part</caption>
					<thead>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30" ><p>SL</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Buyer</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Order No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Shipping Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30"><p>Img</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Style</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Internal Ref</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Floor Group</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Color</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="70"><p>Color Qty</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Fabric Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Cut Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Cutting QC</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Delivery To Print</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Receive from Print</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Delivery To Embroidery</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="240"><p>Receive from Embroidery</p></th>
							
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Input Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Sewing Output</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Finishing</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Carton</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Ex-Factory</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="105"><p>Sewing - Exfactory Balance</p></th>
						</tr>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>F.Fab. Req.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab. Rcv.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab. Issue</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Fab.Inhand</p></th>
							
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Cut %</p></th>
							
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Cutting %</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Print WIP</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Reject Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Embroidery WIP</p></th>


							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60" title="(Total Sewing Input / Total Cut Status)*100"><p>Input %</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Sewing %</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Finish %</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="105"><p>Total</p></th>
						</tr>					   
					</thead>
				</table>
				<div style="max-height:400px; overflow-y:scroll; width:3520px" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3500" rules="all" id="table_body" align="left">
					<?
					$k=1;
					$jj=1;	
					$gr_color_qty 			= 0;
					$gr_fab_req_qty 		= 0;
					$gr_fab_rcv_qty 		= 0;
					$gr_fab_iss_qty 		= 0;
					$gr_fab_inh_qty 		= 0;
					$gr_today_lay_qty 		= 0;
					$gr_total_lay_qty 		= 0;
					$gr_cutting_percent 	= 0;
					$gr_today_qc_qty 		= 0;
					$gr_total_qc_qty 		= 0;
					$gr_cutting_qc_percent 	= 0;
					$gr_cutting_qc_reject 	= 0;
					$gr_today_print_iss_qty = 0;
					$gr_total_print_iss_qty = 0;
					$gr_today_print_rcv_qty = 0;
					$gr_total_print_rcv_qty = 0;
					$gr_print_rej_qty 		= 0;
					$gr_print_wip_qty 		= 0;
					$gr_today_embr_iss_qty = 0;
					$gr_total_embr_iss_qty = 0;
					$gr_today_embr_rcv_qty = 0;
					$gr_total_embr_rcv_qty = 0;
					$gr_embr_rej_qty 		= 0;
					$gr_embr_wip_qty 		= 0;
					$gr_today_sew_in_qty 	= 0;
					$gr_total_sew_in_qty 	= 0;
					$gr_input_percent 		= 0;
					$gr_today_sew_out_qty 	= 0;
					$gr_total_sew_out_qty 	= 0;
					$gr_sewing_percent 		= 0;
					$gr_today_fin_qty 		= 0;
					$gr_total_fin_qty 		= 0;
					$gr_finish_percent 		= 0;
					$gr_today_ctn_qty 		= 0;
					$gr_total_ctn_qty 		= 0;
					$gr_today_exfact_qty 	= 0;
					$gr_total_exfact_qty 	= 0;
					$gr_today_exf_cm_qty 	= 0;
					$gr_total_exf_cm_qty 	= 0;
					$gr_today_exf_fob_qty 	= 0;
					$gr_total_exf_fob_qty 	= 0;
					$gr_today_sew_cm_qty 	= 0;
					$gr_total_sew_cm_qty 	= 0;
					$gr_today_fob_qty 		= 0;
					$gr_total_fob_qty 		= 0;
					$gr_total_ex_bal_qty 	= 0;
					$gr_total_ex_fob_qty 	= 0;
					
					foreach($production_main_array as $style_id=>$job_data)
					{				
						$style_wise_color_qty 			= 0;
						$style_wise_fab_req_qty 		= 0;
						$style_wise_fab_rcv_qty 		= 0;
						$style_wise_fab_iss_qty 		= 0;
						$style_wise_fab_inh_qty 		= 0;
						$style_wise_today_lay_qty 		= 0;
						$style_wise_total_lay_qty 		= 0;
						$style_wise_cutting_percent 	= 0;
						$style_wise_today_qc_qty 		= 0;
						$style_wise_total_qc_qty 		= 0;
						$style_wise_cutting_qc_percent 	= 0;
						$style_wise_cutting_qc_reject 	= 0;
						$style_wise_today_print_iss_qty = 0;
						$style_wise_total_print_iss_qty = 0;
						$style_wise_today_print_rcv_qty = 0;
						$style_wise_total_print_rcv_qty = 0;
						$style_wise_print_rej_qty 		= 0;
						$style_wise_print_wip_qty 		= 0;
						$style_wise_today_embr_iss_qty = 0;
						$style_wise_total_embr_iss_qty = 0;
						$style_wise_today_embr_rcv_qty = 0;
						$style_wise_total_embr_rcv_qty = 0;
						$style_wise_embr_rej_qty 		= 0;
						$style_wise_embr_wip_qty 		= 0;
						$style_wise_today_sew_in_qty 	= 0;
						$style_wise_total_sew_in_qty 	= 0;
						$style_wise_input_percent 		= 0;
						$style_wise_today_sew_out_qty 	= 0;
						$style_wise_total_sew_out_qty 	= 0;
						$style_wise_sewing_percent 		= 0;
						$style_wise_today_fin_qty 		= 0;
						$style_wise_total_fin_qty 		= 0;
						$style_wise_finish_percent 		= 0;
						$style_wise_today_ctn_qty 		= 0;
						$style_wise_total_ctn_qty 		= 0;
						$style_wise_today_exfact_qty 	= 0;
						$style_wise_total_exfact_qty 	= 0;
						$style_wise_today_exf_cm_qty 	= 0;
						$style_wise_total_exf_cm_qty 	= 0;
						$style_wise_today_exf_fob_qty 	= 0;
						$style_wise_total_exf_fob_qty 	= 0;
						$style_wise_today_sew_cm_qty 	= 0;
						$style_wise_total_sew_cm_qty 	= 0;
						$style_wise_today_fob_qty 		= 0;
						$style_wise_total_fob_qty 		= 0;
						$style_wise_total_ex_bal_qty 	= 0;
						$style_wise_total_ex_fob_qty 	= 0;

						foreach($job_data as $job_id=>$po_data)
						{
							
							foreach($po_data as $po_id=>$item_data)
							{
								$po_wise_color_qty 			= 0;
								$po_wise_fab_req_qty 		= 0;
								$po_wise_fab_rcv_qty 		= 0;
								$po_wise_fab_iss_qty 		= 0;
								$po_wise_fab_inh_qty 		= 0;
								$po_wise_today_lay_qty 		= 0;
								$po_wise_total_lay_qty 		= 0;
								$po_wise_cutting_percent 	= 0;
								$po_wise_today_qc_qty 		= 0;
								$po_wise_total_qc_qty 		= 0;
								$po_wise_cutting_qc_percent	= 0;
								$po_wise_cutting_qc_reject	= 0;
								$po_wise_today_print_iss_qty= 0;
								$po_wise_total_print_iss_qty= 0;
								$po_wise_today_print_rcv_qty= 0;
								$po_wise_total_print_rcv_qty= 0;
								$po_wise_print_rej_qty 		= 0;
								$po_wise_print_wip_qty 		= 0;
								$po_wise_today_embr_iss_qty= 0;
								$po_wise_total_embr_iss_qty= 0;
								$po_wise_today_embr_rcv_qty= 0;
								$po_wise_total_embr_rcv_qty= 0;
								$po_wise_embr_rej_qty 		= 0;
								$po_wise_embr_wip_qty 		= 0;
								$po_wise_today_sew_in_qty 	= 0;
								$po_wise_total_sew_in_qty 	= 0;
								$po_wise_input_percent 		= 0;
								$po_wise_today_sew_out_qty 	= 0;
								$po_wise_total_sew_out_qty 	= 0;
								$po_wise_sewing_percent 	= 0;
								$po_wise_today_fin_qty 		= 0;
								$po_wise_total_fin_qty 		= 0;
								$po_wise_finish_percent 	= 0;
								$po_wise_today_ctn_qty 		= 0;
								$po_wise_total_ctn_qty 		= 0;
								$po_wise_today_exfact_qty 	= 0;
								$po_wise_total_exfact_qty 	= 0;
								$po_wise_today_exf_cm_qty 	= 0;
								$po_wise_total_exf_cm_qty 	= 0;
								$po_wise_today_exf_fob_qty 	= 0;
								$po_wise_total_exf_fob_qty 	= 0;
								$po_wise_today_sew_cm_qty 	= 0;
								$po_wise_total_sew_cm_qty 	= 0;
								$po_wise_today_fob_qty 		= 0;
								$po_wise_total_fob_qty 		= 0;								
								$po_wise_total_ex_bal_qty 	= 0;								
								$po_wise_total_ex_fob_qty 	= 0;								

								foreach($item_data as $item_id=>$color_data)
								{ 
									$r=0;
									foreach($color_data as $color_id=>$row)
									{
										
										/*========================================================================================
										*																						  *
										*								Calculate cm valu 										  *	
										*																					  	  *	
										*========================================================================================*/
										$tot_dye_chemi_process_amount 	= $conversion_cost_arr[$job_id][101]['conv_amount_total_value'];
										$tot_yarn_dye_process_amount 	= $conversion_cost_arr[$job_id][30]['conv_amount_total_value'];
										$tot_aop_process_amount 		= $conversion_cost_arr[$job_id][35]['conv_amount_total_value'];
										
										$total_quot_qty=$total_quot_pcs_qty=$total_quot_amount=$total_sew_smv=$total_quot_amount_cal=0;
										$all_last_shipdates='';

							            foreach($style_wise_arr as $style_key=>$val)
										{	
											$total_cost=$val[('qty')]*$val[('final_cost_pcs')];
											$total_quot_qty+=$val[('qty')];
											$total_quot_pcs_qty+=$val[('qty_pcs')];
											$total_sew_smv+=$val[('sew_smv')];
											$total_quot_amount+=$total_cost;
											$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
										}
										$total_quot_amount_cal = $style_wise_arr[$job_id]['qty']*$style_wise_arr[$job_id]['final_cost_pcs'];
										$tot_cm_for_fab_cost=$summary_data[$job_id]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
										// echo $job_id."==".$summary_data[$job_id]['conversion_cost_total_value']."-(".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$tot_aop_process_amount.")<br>";
										$commision_quot_local=$commision_local_quot_cost_arr[$job_id];
										$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$job_id]+$commer_lc_cost_quot_arr[$job_id]+$freight_cost_data[$job_id]['freight_total_value']);
										$tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
										$tot_inspect_cour_certi_cost=$summary_data[$job_id]['inspection_total_value']+$summary_data[$job_id]['currier_pre_cost_total_value']+$summary_data[$job_id]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$job_id]['design_pre_cost_total_value'];
										// echo $summary_data[$job_id]['inspection_total_value']."+".$summary_data[$job_id]['currier_pre_cost_total_value']."+".$summary_data[$job_id]['certificate_pre_cost_total_value']."+".$tot_sum_amount_quot_calccc."+".$summary_data[$job_id]['design_pre_cost_total_value']."<br>";

										$tot_emblish_cost=$summary_data[$job_id]['embel_cost_total_value'];
										$pri_freight_cost_per=$summary_data[$job_id]['freight_total_value'];
										$pri_commercial_per=$commer_lc_cost[$job_id];
										$CommiData_foreign_cost=$CommiData_foreign_quot_cost_arr[$job_id];

										$total_btb=$summary_data[$job_id]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$job_id]['comm_cost_total_value']+$summary_data[$job_id]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$job_id]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$job_id]['common_oh_total_value']+$summary_data[$job_id]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
										// echo $summary_data[$job_id]['lab_test_total_value']."+".$tot_emblish_cost."+".$summary_data[$job_id]['comm_cost_total_value']."+".$summary_data[$job_id]['trims_cost_total_value']."+".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$summary_data[$job_id]['yarn_amount_total_value']."+".$tot_aop_process_amount."+".$summary_data[$job_id]['common_oh_total_value']."+".$summary_data[$job_id]['studio_pre_cost_total_value']."+".$tot_inspect_cour_certi_cost."<br>";
										$tot_quot_sum_amount=$total_quot_amount_cal-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
										// echo $total_quot_amount_cal."-(".$CommiData_foreign_cost."+".$pri_freight_cost_per."+".$pri_commercial_per.")<br>";
										$NetFOBValue_job = $tot_quot_sum_amount;
										// echo $NetFOBValue_job."<br>";
										$total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);

										$total_quot_pcs_qty = $quotation_qty_array[$job_id]['QTY_PCS'];
										// echo $total_cm_for_gmt;echo "<br>";
										$cm_valu_lc = 0;
										$cm_valu_lc = $total_cm_for_gmt/$total_quot_pcs_qty;
										// echo number_format(($total_cm_for_gmt/$total_quot_pcs_qty)*12,2); echo "<br>";
										/*========================================================================================
										*																						  *
										*											END											  *	
										*																					  	  *	
										*========================================================================================*/											
										$order_quantitys=$order_qnty_array[$po_id][$item_id][$color_id];								
										$fab_fin_req=$booking_no_fin_qnty_array[$po_id][$item_id][$color_id];	

									 	$today_lay_qnty=$lay_qnty_array[$job_id][$po_id][$item_id][$color_id]["today_lay"];
									 	$total_lay_qnty=$lay_qnty_array[$job_id][$po_id][$item_id][$color_id]["total_lay"];
									 	$cutting_lay_percent = ($total_lay_qnty / $order_quantitys)*100;

									 	$today_qc_qnty=$qc_qnty_array[$job_id][$po_id][$item_id][$color_id]["today_qc"];
									 	$total_qc_qnty=$qc_qnty_array[$job_id][$po_id][$item_id][$color_id]["total_qc"];
									 	$total_qc_rej=$qc_qnty_array[$job_id][$po_id][$item_id][$color_id]["total_rej"];
									 	$cutting_qc_percent = ($total_lay_qnty) ? ($total_qc_qnty / $total_lay_qnty)*100 : 0;

									 	$today_issue_to_print = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_issue_to_print"];
									 	$total_issue_to_print = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_issue_to_print"];
									 	$today_rcv_frm_print = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_rcv_frm_print"];
									 	$total_rcv_frm_print = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_rcv_frm_print"];
									 	$print_reject = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["print_reject"];
									 	$print_wip = $total_issue_to_print - $total_rcv_frm_print;

										$today_issue_to_embr = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_issue_to_embroidery"];
									 	$total_issue_to_embr = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_issue_to_embroidery"];
									 	$today_rcv_frm_embr = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_rcv_frm_embroidery"];
									 	$total_rcv_frm_embr = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_rcv_frm_embroidery"];
									 	$embr_reject = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["reject_embroidery"];
									 	$embr_wip = $total_issue_to_embr - $total_rcv_frm_embr;



									 	$today_sewing_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
										$total_sewing_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_input"];
										$input_percent = ($total_lay_qnty) ? ($total_sewing_input / $total_lay_qnty)*100 : 0;

										$today_sewing_output = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_output"];
										$total_sewing_output = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_output"];
										$output_percent = ($total_sewing_input) ? ($total_sewing_output / $total_sewing_input)*100 : 0;

										$today_finishing = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id][0]["today_finishing"];
										$total_finishing = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id][0]["total_finishing"];
										$finishing_percent = ($total_sewing_output) ? ($total_finishing / $total_sewing_output)*100 : 0;

										$today_carton_qty = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_carton_qty"];
										$total_carton_qty = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_carton_qty"];


										$today_ex_fac=$ex_factory_arr[$po_id][$item_id][$color_id]['today_ex_fac'];
										$total_ex_fac=$ex_factory_arr[$po_id][$item_id][$color_id]['total_ex_fac'];	

										$total_sew_ex_fact_bal = $total_sewing_output - $total_ex_fac;
										$total_sew_ex_fact_fob = $total_sew_ex_fact_bal * $row['unit_price'];
										//$issue_qty=$issue_qnty_arr[$batch_mst_id_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]]];
										$issue_qty=$issue_qnty_arr[$po_id][$color_id][18];
										$receive_qty=$issue_qnty_arr[$po_id][$color_id][37];

										$fab_in_hand=$receive_qty-$issue_qty;
										$unit_price = $row['unit_price'];
										$today_fob_val = $today_sewing_output * $unit_price;
										$total_fob_val = $total_sewing_output * $unit_price;
										$today_exf_fob_val = $today_ex_fac * $unit_price;
										$total_exf_fob_val = $total_ex_fac * $unit_price;

										$costing_per=$costing_per_arr[$job_id];
										if($costing_per==1) $dzn_qnty=12;
										else if($costing_per==3) $dzn_qnty=12*2;
										else if($costing_per==4) $dzn_qnty=12*3;
										else if($costing_per==5) $dzn_qnty=12*4;
										else $dzn_qnty=1;	
										$dzn_qnty=$dzn_qnty*$row['total_set_qnty'];
										$cm_gmt_cost_dzn=$cm_gmt_cost_dzn_arr_new[$po_id]['dzn'];
										//echo $cm_gmt_cost_dzn.'DD'.$po_id.', ';
										//$cm_per_pcs=(($unit_price*$dzn_qnty)-$total_cost_arr[$job_id])+$cm_cost_arr[$job_id];
										$cm_per_pcs=$cm_gmt_cost_dzn/$dzn_qnty;
										$today_cm_val = $today_sewing_output * $cm_per_pcs;
										$total_cm_val = $total_sewing_output * $cm_per_pcs;
										$today_cm_val_lc = $today_sewing_output * $cm_valu_lc;
										$total_cm_val_lc = $total_sewing_output * $cm_valu_lc;

										$today_exf_cm_val = $today_ex_fac * $cm_per_pcs;
										$total_exf_cm_val = $total_ex_fac * $cm_per_pcs;
										$today_exf_cm_val_lc = $today_ex_fac * $cm_valu_lc;
										$total_exf_cm_val_lc = $total_ex_fac * $cm_valu_lc;
											
										$rowspan_num = $rowspan_arr[$style_id][$job_id][$po_id][$item_id];
									 
										if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
												<?
												$jj++;													
												?>
											 
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p><? echo $k;?></p></td>

												<? if($r==0){?>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="left"   width="80"><? echo $buyer_library[$row["buyer_name"]]; ?></td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100">
														<? echo implode(PHP_EOL, str_split($row["po_number"],10));?>														
												</td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100">
														<? echo $row['shiping_status'];?>														
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $rowspan_num;?>" width="30" align="center" valign="top">
													<a href="##" onClick="openmypage_image('requires/floor_wise_daily_rmg_production_controller.php?action=show_image&job_no=<? echo $job_id;?>','Image View')">
															<?if(isset($imge_arr[$job_id])){?>
															<!-- <div style="height:0;"> -->
																<img src="../../<? echo $imge_arr[$job_id];?>" height="28" width="28" />
															<!-- </div> -->
															<?}else{?>
																<img src="../../img/noimage.png" height="28" width="28"/>
															<?}?>
														
													</a>
												</td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100"><? echo implode(PHP_EOL, str_split($style_id,10));?></td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100">
														<? echo $row['grouping'];?>														
												</td>
												<td rowspan="<? echo $rowspan_num;?>"  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100">
														<?
														/* $floor_group_name = "";
														foreach($sewing_floor_arr[$po_id][$item_id] as $v)
														{
															$floor_group_name .= $floor_group_library[$v].",";
														} */
														 echo $floor_group_library[$row['floor_id']];
														// echo chop($floor_group_name,",");
														 ?>														
												</td>
												<?}$r++;?>

												<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="left"   width="100"><? echo $color_Arr_library[$color_id];?></td>
												<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="right"    width="70"><p><? echo number_format($order_quantitys,0); ?></p></td> 

												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60"><p><? echo number_format($fab_fin_req,2);?></p></td>

												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<a href="##" onClick="openmypage_fab_popup(<? echo $po_id;?>,<? echo $color_id?>,<? echo 1 ?>, 'fab_issue_popup');" >
														<p><? echo number_format($receive_qty,2);?></p>
													</a>
												</td>
												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<a href="##" onClick="openmypage_fab_popup(<? echo $po_id;?>,<? echo $color_id?>,<? echo 2 ?>, 'fab_issue_popup');" > 
														<p><? echo number_format($issue_qty,2);?></p>
													</a>
												</td>
												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<p><? echo number_format($fab_in_hand,2);?></p>
												</td>
																									
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,0,'A','production_qnty_popup','Today Cut','870','500');">
														<p><? echo number_format($today_lay_qnty,0);?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,0,'B','production_qnty_popup','Total Cut','870','500');">
														<p><? echo number_format($total_lay_qnty,0);?></p>
													</a>
												</td>
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<p><? echo number_format($cutting_lay_percent,0); ?>%</p>
												</td>

												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'A','production_qnty_popup','Today Cutting QC','870','500');">
														<p><? echo number_format($today_qc_qnty,0);?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'B','production_qnty_popup','Total Cutting QC','870','500');">
														<p><? echo number_format($total_qc_qnty,0);?></p>
													</a>
												</td>
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<p><? echo number_format($cutting_qc_percent,0); ?>%</p>
												</td>
												<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<!-- <a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'C','production_qnty_popup','Cutting QC Reject','870','500');"> --><p>
														<? echo number_format($total_qc_rej,0);?></p>
													<!-- </a> -->
												</td>

												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,2,'A','production_qnty_popup','Today delivery to print','870','500');">
														<p><? echo number_format($today_issue_to_print,0); ?></p>
													</a>														
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,2,'B','production_qnty_popup','Total delivery to print','870','500');">
														<p><? echo number_format($total_issue_to_print,0); ?></p>
													</a>														
												</td>

												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,3,'A','production_qnty_popup','Today receive from print','870','500');">
														<? echo number_format($today_rcv_frm_print,0); ?>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,3,'B','production_qnty_popup','Total receive from print','870','500');">
														<p><? echo number_format($total_rcv_frm_print,0); ?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													<!-- <a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,3,'C','production_qnty_popup','Print Reject','870','500');"> -->
														<p><? echo number_format($print_reject,0); ?></p>
													<!-- </a> -->
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><!-- 
													<a href="##" onclick="openmypage_production_popup(<? //echo $po_id;?>,<? //echo $item_id;?>,<? //echo $color_id;?>,3,'D','production_qnty_popup','Print WIP','870','500');"> -->
														<p><? echo number_format($print_wip,0);?></p>
													<!-- </a> -->
												</td> 
												 
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
														<p><? echo number_format($today_issue_to_embr,0); ?></p>
																											
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
														<p><? echo number_format($total_issue_to_embr,0); ?></p>
																								
												</td>

												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
														<? echo number_format($today_rcv_frm_embr,0); ?>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													
										         	<p><? echo number_format($total_rcv_frm_embr,0); ?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
												
														<p><? echo number_format($embr_reject,0); ?></p>
												
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60">
													
														<p><? echo number_format($embr_wip,0);?></p>
													
												</td> 
																								
													
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,4,'A','production_qnty_popup','Today Sewing Input','870','500');"><p>
														<? echo number_format($today_sewing_input,0);?></p>
													</a>
												</td>

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,4,'B','production_qnty_popup','Total Sewing Input','870','500');"><p>
														<? echo number_format($total_sewing_input,0);?></p>
													</a>
												</td>
												
												<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60"><p><? echo number_format($input_percent,0);?>%</p></td>
																 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,5,'A','production_qnty_popup','Today Sewing Output','870','500');"><p>
														<? echo number_format($today_sewing_output,0);?></p>
													</a>
												</td>								 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,5,'B','production_qnty_popup','Total Sewing Output','870','500');"><p>
														<? echo number_format($total_sewing_output,0);?></p>
													</a>
												</td>								 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="60"><p><? echo number_format($output_percent,0);?>%</p></td>

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,8,'A','production_qnty_popup','Today Finishing','870','500');"><p>
														<? echo number_format($today_finishing,0);?></p>
													</a>
												</td>								 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,8,'B','production_qnty_popup','Total Finishing','870','500');"><p>
														<? echo number_format($total_finishing,0);?></p>
													</a>
												</td>							 
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60"><p><? echo number_format($finishing_percent,0); ?>%</p></td>

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<p>
														<? echo number_format($today_carton_qty,0);?>														
													</p>													
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60">
													<p><? echo number_format($total_carton_qty,0);?></p>													
												</td>	

												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60">
													<a href="##" onClick="openmypage_ex_fac_total('<? echo $po_id;?>**<? echo $item_id;?>**<? echo $color_id;?>**<? echo str_replace("'", "", $txt_production_date);?>**1','exfac_action');" ><p><? echo number_format($today_ex_fac,0);?></p>
													</a>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="60"><a href="##" onClick="openmypage_ex_fac_total('<? echo $po_id;?>**<? echo $item_id;?>**<? echo $color_id;?>**<? echo str_replace("'", "", $txt_production_date);?>**2', 'exfac_action');" ><p><? echo number_format($total_ex_fac,0);?></p>
													</a>
												</td>


												<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="105"><p>
													<? echo number_format($total_sew_ex_fact_bal,0); ?>
												</p></td>

																		 
											</tr>	
										<?
										// ====po wise sub total=======											
										$po_wise_color_qty 			+= $order_quantitys;
										$po_wise_fab_req_qty 		+= $fab_fin_req;
										$po_wise_fab_rcv_qty 		+= $receive_qty;
										$po_wise_fab_iss_qty 		+= $issue_qty;
										$po_wise_fab_inh_qty 		+= $fab_in_hand;
										$po_wise_today_lay_qty 		+= $today_lay_qnty;
										$po_wise_total_lay_qty 		+= $total_lay_qnty;
										$po_wise_cutting_percent 	+= $cutting_percent;
										$po_wise_today_qc_qty 		+= $today_qc_qnty;
										$po_wise_total_qc_qty 		+= $total_qc_qnty;
										$po_wise_cutting_qc_percent	+= $cutting_qc_percent;
										$po_wise_cutting_qc_reject	+= $total_qc_rej;
										$po_wise_today_print_iss_qty+= $today_issue_to_print;
										$po_wise_total_print_iss_qty+= $total_issue_to_print;
										$po_wise_today_print_rcv_qty+= $today_rcv_frm_print;
										$po_wise_total_print_rcv_qty+= $total_rcv_frm_print;
										$po_wise_print_rej_qty 		+= $print_reject;
										$po_wise_print_wip_qty 		+= $print_wip;
										$po_wise_today_embr_iss_qty+= $today_issue_to_embr;
										$po_wise_total_embr_iss_qty+= $total_issue_to_embr;
										$po_wise_today_embr_rcv_qty+= $today_rcv_frm_embr;
										$po_wise_total_embr_rcv_qty+= $total_rcv_frm_embr;
										$po_wise_embr_rej_qty 		+= $embr_reject;
										$po_wise_embr_wip_qty 		+= $embr_wip;
										$po_wise_today_sew_in_qty 	+= $today_sewing_input;
										$po_wise_total_sew_in_qty 	+= $total_sewing_input;
										$po_wise_input_percent 		+= $input_percent;
										$po_wise_today_sew_out_qty 	+= $today_sewing_output;
										$po_wise_total_sew_out_qty 	+= $total_sewing_output;
										$po_wise_sewing_percent 	+= $output_percent;
										$po_wise_today_fin_qty 		+= $today_finishing;
										$po_wise_total_fin_qty 		+= $total_finishing;
										$po_wise_finish_percent 	+= $finishing_percent;
										$po_wise_today_ctn_qty 		+= $today_carton_qty;
										$po_wise_total_ctn_qty 		+= $total_carton_qty;
										$po_wise_today_exfact_qty 	+= $today_ex_fac;
										$po_wise_total_exfact_qty 	+= $total_ex_fac;
										$po_wise_today_exf_cm_qty 	+= $today_exf_cm_val;
										$po_wise_total_exf_cm_qty 	+= $total_exf_cm_val;
										$po_wise_today_exf_cm_qty_lc += $today_exf_cm_val_lc;
										$po_wise_total_exf_cm_qty_lc += $total_exf_cm_val_lc;
										$po_wise_today_exf_fob_qty 	+= $today_exf_fob_val;
										$po_wise_total_exf_fob_qty 	+= $total_exf_fob_val;
										$po_wise_today_sew_cm_qty 	+= $today_cm_val;
										$po_wise_total_sew_cm_qty 	+= $total_cm_val;
										$po_wise_today_sew_cm_qty_lc += $today_cm_val_lc;
										$po_wise_total_sew_cm_qty_lc += $total_cm_val_lc;
										$po_wise_today_fob_qty 		+= $today_fob_val;
										$po_wise_total_fob_qty 		+= $total_fob_val;
										$po_wise_total_ex_bal_qty	+= $total_sew_ex_fact_bal;
										$po_wise_total_ex_fob_qty	+= $total_sew_ex_fact_fob;
										// ====style wise sub total======
										$style_wise_color_qty 			+= $order_quantitys;
										$style_wise_fab_req_qty 		+= $fab_fin_req;
										$style_wise_fab_rcv_qty 		+= $receive_qty;
										$style_wise_fab_iss_qty 		+= $issue_qty;
										$style_wise_fab_inh_qty 		+= $fab_in_hand;
										$style_wise_today_lay_qty 		+= $today_lay_qnty;
										$style_wise_total_lay_qty 		+= $total_lay_qnty;
										$style_wise_cutting_percent 	+= $cutting_percent;
										$style_wise_today_qc_qty 		+= $today_qc_qnty;
										$style_wise_total_qc_qty 		+= $total_qc_qnty;
										$style_wise_cutting_qc_percent 	+= $cutting_qc_percent;
										$style_wise_cutting_qc_reject 	+= $total_qc_rej;
										$style_wise_today_print_iss_qty += $today_issue_to_print;
										$style_wise_total_print_iss_qty += $total_issue_to_print;
										$style_wise_today_print_rcv_qty += $today_rcv_frm_print;
										$style_wise_total_print_rcv_qty += $total_rcv_frm_print;
										$style_wise_print_rej_qty 		+= $print_reject;
										$style_wise_print_wip_qty 		+= $print_wip;
										$style_wise_today_embr_iss_qty += $today_issue_to_embr;
										$style_wise_total_embr_iss_qty += $total_issue_to_embr;
										$style_wise_today_embr_rcv_qty += $today_rcv_frm_embr;
										$style_wise_total_embr_rcv_qty += $total_rcv_frm_embr;
										$style_wise_embr_rej_qty 		+= $embr_reject;
										$style_wise_embr_wip_qty 		+= $embr_wip;
										$style_wise_today_sew_in_qty 	+= $today_sewing_input;
										$style_wise_total_sew_in_qty 	+= $total_sewing_input;
										$style_wise_input_percent 		+= $input_percent;
										$style_wise_today_sew_out_qty 	+= $today_sewing_output;
										$style_wise_total_sew_out_qty 	+= $total_sewing_output;
										$style_wise_sewing_percent 		+= $output_percent;
										$style_wise_today_fin_qty 		+= $today_finishing;
										$style_wise_total_fin_qty 		+= $total_finishing;
										$style_wise_finish_percent 		+= $finishing_percent;
										$style_wise_today_ctn_qty 		+= $today_carton_qty;
										$style_wise_total_ctn_qty 		+= $total_carton_qty;
										$style_wise_today_exfact_qty 	+= $today_ex_fac;
										$style_wise_total_exfact_qty 	+= $total_ex_fac;
										$style_wise_today_exf_cm_qty 	+= $today_exf_cm_val;
										$style_wise_total_exf_cm_qty 	+= $total_exf_cm_val;
										$style_wise_today_exf_fob_qty 	+= $today_exf_fob_val;
										$style_wise_total_exf_fob_qty 	+= $total_exf_fob_val;
										$style_wise_today_sew_cm_qty 	+= $today_cm_val;
										$style_wise_total_sew_cm_qty 	+= $total_cm_val;
										$style_wise_today_fob_qty 		+= $today_fob_val;
										$style_wise_total_fob_qty 		+= $total_fob_val;
										$style_wise_total_ex_bal_qty	+= $total_sew_ex_fact_bal;
										$style_wise_total_ex_fob_qty	+= $total_sew_ex_fact_fob;
										// ========= grand total =========== 
										$gr_color_qty 			+= $order_quantitys;
										$gr_fab_req_qty 		+= $fab_fin_req;
										$gr_fab_rcv_qty 		+= $receive_qty;
										$gr_fab_iss_qty 		+= $issue_qty;
										$gr_fab_inh_qty 		+= $fab_in_hand;
										$gr_today_lay_qty 		+= $today_lay_qnty;
										$gr_total_lay_qty 		+= $total_lay_qnty;
										$gr_cutting_percent 	+= $cutting_percent;
										$gr_today_qc_qty 		+= $today_qc_qnty;
										$gr_total_qc_qty 		+= $total_qc_qnty;
										$gr_cutting_qc_percent 	+= $cutting_qc_percent;
										$gr_cutting_qc_reject 	+= $total_qc_rej;
										$gr_today_print_iss_qty += $today_issue_to_print;
										$gr_total_print_iss_qty += $total_issue_to_print;
										$gr_today_print_rcv_qty += $today_rcv_frm_print;
										$gr_total_print_rcv_qty += $total_rcv_frm_print;
										$gr_print_rej_qty 		+= $print_reject;
										$gr_print_wip_qty 		+= $print_wip;
										$gr_today_embr_iss_qty += $today_issue_to_embr;
										$gr_total_embr_iss_qty += $total_issue_to_embr;
										$gr_today_embr_rcv_qty += $today_rcv_frm_embr;
										$gr_total_embr_rcv_qty += $total_rcv_frm_embr;
										$gr_embr_rej_qty 		+= $embr_reject;
										$gr_embr_wip_qty 		+= $embr_wip;
										$gr_today_sew_in_qty 	+= $today_sewing_input;
										$gr_total_sew_in_qty 	+= $total_sewing_input;
										$gr_input_percent 		+= $input_percent;
										$gr_today_sew_out_qty 	+= $today_sewing_output;
										$gr_total_sew_out_qty 	+= $total_sewing_output;
										$gr_sewing_percent 		+= $output_percent;
										$gr_today_fin_qty 		+= $today_finishing;
										$gr_total_fin_qty 		+= $total_finishing;
										$gr_finish_percent 		+= $finishing_percent;
										$gr_today_ctn_qty 		+= $today_carton_qty;
										$gr_total_ctn_qty 		+= $total_carton_qty;
										$gr_today_exfact_qty 	+= $today_ex_fac;
										$gr_total_exfact_qty 	+= $total_ex_fac;
										$gr_today_exf_cm_qty 	+= $today_exf_cm_val;
										$gr_total_exf_cm_qty 	+= $total_exf_cm_val;
										$gr_today_exf_cm_qty_lc += $today_exf_cm_val_lc;
										$gr_total_exf_cm_qty_lc += $total_exf_cm_val_lc;
										$gr_today_exf_fob_qty 	+= $today_exf_fob_val;
										$gr_total_exf_fob_qty 	+= $total_exf_fob_val;
										$gr_today_sew_cm_qty 	+= $today_cm_val;
										$gr_total_sew_cm_qty 	+= $total_cm_val;
										$gr_today_sew_cm_qty_lc += $today_cm_val_lc;
										$gr_total_sew_cm_qty_lc += $total_cm_val_lc;
										$gr_today_fob_qty 		+= $today_fob_val;
										$gr_total_fob_qty 		+= $total_fob_val;
										$gr_total_ex_bal_qty	+= $total_sew_ex_fact_bal;
										$gr_total_ex_fob_qty 	+= $total_sew_ex_fact_fob;
										
										$k++;											
									}
								}
								?>
								<tr bgcolor="#E4E4E4">
									<td colspan="9" align="right"><b>Order Wise Sub Total</b></td>
									<td align="right"><b><? echo number_format($po_wise_color_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_fab_req_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_fab_rcv_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_fab_iss_qty,2);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_fab_inh_qty,2);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_lay_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_lay_qty,0);?></b></td>
									<td align="right"></td>

									<td align="right"><b><? echo number_format($po_wise_today_qc_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_qc_qty,0);?></b></td>
									<td align="right"></td>							
									<td align="right"><b><? echo number_format($po_wise_cutting_qc_reject,0);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_print_iss_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_print_iss_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_today_print_rcv_qty,0); ?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_print_rcv_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_print_rej_qty,0); ?></b></td>										
									<td align="right"><b><? echo number_format($po_wise_print_wip_qty,0);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_embr_iss_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($$gr_total_embr_iss_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_today_embr_rcv_qty,0); ?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_embr_rcv_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_embr_rej_qty,0); ?></b></td>										
									<td align="right"><b><? echo number_format($po_wise_embr_wip_qty,0);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_sew_in_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_sew_in_qty,0);?></b></td>
									<td align="right"><b></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_sew_out_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_sew_out_qty,0);?></b></td>
									<td align="right"><b><? //echo $order_wise_input_sewing_balance;?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_fin_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_fin_qty,0);?></b></td>
									<td align="right"><b><? //echo $order_wise_sewing_fin_balance;?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_ctn_qty,0) ;?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_ctn_qty,0);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_today_exfact_qty,0);?></b></td>
									<td align="right"><b><? echo number_format($po_wise_total_exfact_qty,0);?></b></td>

									<td align="right"><b><? echo number_format($po_wise_total_ex_bal_qty,0);?></b></td>

									
								</tr>
								<?
							}							
						}							
						?>
						<!-- <tr bgcolor="#E4E4E4">
							<td colspan="7" align="right"><b>Style Wise Sub Total</b></td>
							<td align="right"><b><? echo number_format($style_wise_color_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_req_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_rcv_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_iss_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($order_wise_fab_req,2);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_lay_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_lay_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($order_wise_fab_possible_qty,2);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_qc_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_qc_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_cutting_qc_percent,2);?></b></td>							
							<td align="right"><b><? echo number_format($style_wise_cutting_qc_reject,0);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_print_iss_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_print_iss_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_today_print_rcv_qty,0); ?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_print_rcv_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_print_rej_qty,0); ?></b></td>										
							<td align="right"><b><? echo number_format($style_wise_print_wip_qty,0);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_sew_in_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_sew_in_qty,0);?></b></td>
							<td align="right"><b></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_sew_out_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_sew_out_qty,0);?></b></td>
							<td align="right"><b><? //echo $order_wise_input_sewing_balance;?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_fin_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_fin_qty,0);?></b></td>
							<td align="right"><b><? //echo $order_wise_sewing_fin_balance;?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_ctn_qty,0) ;?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_ctn_qty,0);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_exfact_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_exfact_qty,0);?></b></td>

							<td align="right"><b><? echo number_format($style_wise_today_sew_cm_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_sew_cm_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_today_fob_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_fob_qty,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_ex_bal_qty,0);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_total_ex_fob_qty,2);?></b></td>
						</tr> -->	
						<?
						$gr_inh_qty;
					}

					?>											
					</table>										  
				</div>	
			</div>
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3500" rules="all"  >
				<tr bgcolor="#E4E4E4"  >  
					<td valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p>&nbsp; </p></td>

					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p>&nbsp;</p></td>
					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>
					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>

					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="30"><p>&nbsp;</p></td>

					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>
					
					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>
					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p>&nbsp;</p></td>

					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><strong>Grand Total</strong></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="70"    align="right"><b><? echo number_format($gr_color_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60"   align="right"><b><? echo number_format($gr_fab_req_qty,2) ?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60"   align="right"><b><? echo number_format($gr_fab_rcv_qty,2) ?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60"   align="right"><b><? echo number_format($gr_fab_iss_qty,2) ?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_fab_inh_qty,2);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_lay_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_lay_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo number_format($gr_cutting_percent,2);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_qc_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_qc_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo number_format($gr_cutting_qc_percent,2);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_cutting_qc_reject,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_print_iss_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_print_iss_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_print_rcv_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_print_rcv_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_print_rej_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_print_wip_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_embr_iss_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_embr_iss_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_embr_rcv_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_embr_rcv_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_embr_rej_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_embr_wip_qty,0);?></b></td>




					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_sew_in_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_sew_in_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo $gr_today_sew_out_qty;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_sew_out_qty,0);?></b></td>		
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_sew_out_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo $gr_total_sew_out_qty;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_fin_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_fin_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? //echo $gr_total_finishing;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_ctn_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_ctn_qty,0);?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_today_exfact_qty,0);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="60" align="right"><b><? echo number_format($gr_total_exfact_qty,0);?></b></td>

																 
					<td style="word-wrap: break-word;word-break: break-all;"  width="105" align="right"><b><? echo number_format($gr_total_ex_bal_qty,0);?></b></td>											 											 

				</tr>	
				
			</table>	
		 </div> 
        <?
        foreach (glob("$user_id*.xls") as $filename) {
	    @unlink($filename);
	    }
	    //---------end------------//
	    $name2=time();
	    $summary_filename=$user_id."_"."summary_".$name2.".xls";
	    $create_new_doc2 = fopen($summary_filename, 'w');	
	    $is_created2 = fwrite($create_new_doc2, $summary_html);
	    //======================================================
		
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename####$summary_filename";
		exit();	 
	}
}

if($action=="exfac_action")
{
	extract($_REQUEST);
	list($po,$item,$color,$date,$type)=explode('**', $data);
	$work_comp=$_SESSION["work_comp"];
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$po_arr=return_library_array("select id,po_number from  wo_po_break_down where id=$po and status_active in(1,2,3) ","id","po_number");
	$title="";
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
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
	</div>

	<?
	if($type==1) // today
	{
		$title="Today Ex-Factory";
		$ex_factory_data=sql_select("SELECT a.po_break_down_id, a.item_number_id,c.color_number_id, a.ex_factory_date,  
		sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac 
		from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c 
		where d.id=a.delivery_mst_id and  a.id=b.mst_id and b.color_size_break_down_id=c.id and  a.po_break_down_id=$po and c.color_number_id=$color and a.item_number_id=$item and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.ex_factory_date='$date'   
		group by a.po_break_down_id, a.item_number_id,c.color_number_id , a.ex_factory_date");
	}
	else
	{
		$title="Total Ex-Factory";
		$ex_factory_data=sql_select("SELECT a.po_break_down_id, a.item_number_id,c.color_number_id, a.ex_factory_date,  
		sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac 
		from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c 
		where d.id=a.delivery_mst_id and  a.id=b.mst_id and b.color_size_break_down_id=c.id and  a.po_break_down_id=$po and c.color_number_id=$color and a.item_number_id=$item and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0   
		group by a.po_break_down_id, a.item_number_id,c.color_number_id , a.ex_factory_date");
	}

	 
	?>
     

    </head>
    <body>
        <div align="center" style="width:100%;" >
            <div id="details_reports" style="width: 310px">
            
             	<table width="310" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
             		<caption> <strong><?echo $title;?></strong></caption>
             		<thead>
             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="110">Order No</th>            				 
             				<th width="90">Date</th>            				 
              				<th width="80">Qnty</th>
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:310px; overflow:auto;">
             	<table  width="310" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
             	<?
             	$p=1;
              	 
              	$gr_total=0;
             	foreach($ex_factory_data as  $keys=> $rows)            		 
             	{
             		 
             		
             		?>
             			<tr>                	 
         						<td align="center" width="30" ><? echo $p++;?></td>
          						<td align="center"  width="110"><? echo $po_arr[$rows[csf("po_break_down_id")]];?></td>
          						<td align="right"  width="90"><b><? echo change_date_format($rows[csf("ex_factory_date")]);?></b></td>
          						<td align="right"  width="80"><b><? echo $qntys=$rows[csf("total_ex_fac")];?></b></td>
         						 
             						 
             			</tr>  
				<?
				$gr_total+=$qntys;
				}
				?>  
				<tr bgcolor="#E4E4E4">
				<td align="right" colspan="3">Total</td>
				<td align="right"><strong><? echo $gr_total;?></strong></td>
					
				</tr> 
						      		 
             		</table>
             		</div>

             		 
             </div>
             
        </div>
      
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}


if($action=="fab_issue_popup")
{
	extract($_REQUEST);	 
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$data=explode("_", $data);
	if($data[2]==1 || $data[2]==2) $color_cond=" and color_id =$data[1] ";
	 ?>

    </head>
    <body>
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
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
		</div>
        <div align="center" style="width:100%;" id="details_reports">            
            
             	<table width="660" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
             		<caption> <strong><? echo $data[3];?></strong></caption>
             		<thead>
             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="110">No</th>             				 
             				<th width="90">Challan No</th>             				 
             				<th width="90">Date</th>             				 
             				<th width="90">Batch No</th>           				 
             				<th width="90">Qnty</th>
             				<th width="160">Fabric Description</th>
             			</tr>           
             		</thead>
             	</table>
             	<div style="">
             	<table  width="660" border="1" rules="all" class="rpt_table" align="center">
             	<?
             	$p=1; 
             	if($data[2]==1)
             	{
             		$sqls=sql_select("SELECT a.recv_number as issue_number,a.receive_date as issue_date,a.challan_no,b.trans_id,sum(b.receive_qnty) as qnty,b.batch_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.order_id='$data[0]' group by a.recv_number,a.receive_date,a.challan_no,b.trans_id,b.batch_id");
             	}
             	else
             	{
             		$sqls=sql_select("SELECT a.issue_number,a.issue_date,a.challan_no,b.trans_id,sum(b.issue_qnty) as qnty,b.batch_id from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.order_id='$data[0]' group by a.issue_number,a.issue_date,a.challan_no,b.trans_id,b.batch_id");
             	}

             	$batch_sql="SELECT a.id, a.batch_no,b.item_description from pro_batch_create_mst a,pro_batch_create_dtls b  where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ";
             	foreach(sql_select($batch_sql) as $vals)
             	{
             		$batch_array[$vals[csf("id")]]["batch_no"]=$vals[csf("batch_no")];
             		$batch_array[$vals[csf("id")]]["item_description"]=$vals[csf("item_description")];
             	}

             	if($data[2]==1)
             	{
             		$qnty_sql=sql_select("SELECT po_breakdown_id,color_id,trans_id, sum(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=37 and po_breakdown_id=$data[0] $color_cond group by po_breakdown_id,color_id,trans_id  ");
             	}
             	else
             	{
             		$qnty_sql=sql_select("SELECT po_breakdown_id,color_id,trans_id, sum(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=18 and po_breakdown_id=$data[0] $color_cond group by po_breakdown_id,color_id,trans_id  ");
             	}
             	
             	foreach($qnty_sql as $vls)
             	{
             		$qnty_array[$vls[csf("po_breakdown_id")]][$vls[csf("trans_id")]][$vls[csf("color_id")]]=$vls[csf("qnty")];
             	}

             	$total=0;
             	foreach($sqls as  $keys=> $rows)            		 
             	{             		
             		$qntys = $qnty_array[$data[0]][$rows[csf("trans_id")]][$data[1]];
             		if($qntys >0)
             		{
	             		?>

	             		<tr>                	 
	         				<td align="center" width="30" ><? echo $p++;?></td>
	         				<td align="center"  width="110"><? echo $rows[csf("issue_number")];?></td>             				 
	         				<td align="center"  width="90"><? echo $rows[csf("challan_no")];?></td>             				 
	         				<td align="center"  width="90"><? echo $rows[csf("issue_date")];?></td>             				 
	         				<td align="center"  width="90"><? echo $batch_array[$rows[csf("batch_id")]]["batch_no"];?></td>           				 
	         				<td align="center"  width="90"><? echo $qntys;?></td>
	         				<td align="center"  width="160"><? echo $batch_array[$rows[csf("batch_id")]]["item_description"];?></td>
	             			</tr>   
	             			 
						<?
						$total+=$qntys;
					}
				}
				?>   
						<tr bgcolor="#E4E4E4" style="font-weight: bold;">                	 
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
 	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$dates=$_SESSION['txt_production_date'];
	$working_company_id=$_SESSION['working_company_id'];
	$location_id=$_SESSION['location_id'];
	?>
	<script> alert(document.getElementById("txt_production_date").value); </script>
	<?
	$date_cond="";
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
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
	</div>

	<?
	if($day=='A')
	{
		$date_cond=" and a.production_date=$dates";
		$date_cond_lay=" and a.entry_date=$dates";
		$date_cond_cut=" and a.entry_date=$dates";
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

	if( ($type==1 && $day=='A') ||($type==0 && $day=='A'))
	{		
		$size_id_array=array();
		$size_wise_qnty_array=array();
		$color_wise_qnty_array=array();

		$order_data="SELECT e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po and d.item_number_id=$item and d.color_number_id=$color  group by  e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date ";

		$order_data_summary="SELECT c.id, e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,d.color_number_id,d.size_number_id, sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po and d.item_number_id=$item and d.color_number_id=$color  group by c.id, e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,d.color_number_id,d.size_number_id order by d.color_number_id,d.size_number_id";

		$data_array=array();
		if($type==1) // cutting qc
		{
			$production_data="SELECT b.order_id,c.item_number_id,sum(b.qc_pass_qty) as production_qnty,b.size_id as size_number_id,b.color_id as color_number_id,b.country_id,a.location_id,a.floor_id,a.serving_company as working_company 
			from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b ,wo_po_color_size_breakdown c 
			where a.id=b.mst_id and b.color_size_id=c.id and a.status_active=1 and a.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.order_id=$po and c.item_number_id=$item and b.color_id=$color $date_cond_cut 
			group by  b.order_id,c.item_number_id,b.size_id,b.color_id,b.country_id,a.location_id,a.floor_id,a.serving_company ";
		}
		else if($type==0) // lay
		{
			$production_data="SELECT c.order_id as po_break_down_id,b.gmt_item_id as item_number_id,sum(c.size_qty) as production_qnty,c.size_id as size_number_id,b.color_id as color_number_id,c.country_id,a.location_id,a.floor_id,a.working_company_id as working_company
			from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c 
			where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and c.order_id=$po and b.gmt_item_id=$item and b.color_id=$color $date_cond_lay group by c.order_id ,b.gmt_item_id,c.size_id,b.color_id,c.country_id,a.location_id,a.floor_id,a.working_company_id ";
		}
		
		$production_data=sql_select($production_data);

		foreach($production_data as $vals)
		{

			$data_array[$vals[csf("color_number_id")]][$vals[csf("country_id")]][$vals[csf("working_company")]][$vals[csf("location_id")]][$vals[csf("floor_id")]]=$vals[csf("country_id")];
			$size_id_array[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
			$size_wise_qnty_array[$vals[csf("color_number_id")]][$vals[csf("country_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];
			$color_wise_qnty_array[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];

		}

		foreach(sql_select($order_data_summary) as $vals)
		{
			
			$col_size_id_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("po_qnty")];
			$col_id_arr[$vals[csf("color_number_id")]]+=$vals[csf("po_qnty")];
			$size_id_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
		}
		$counts=count($size_id_arr);  

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

							<?
						}//die;
						?>
					</thead>
				</table>
				<br>
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
						if($type==5 || $type==4)$tbl_wid=600;else $tbl_wid=600;

						?>
					</tbody>
				</table>
				<br>
				<!-- ===================== Details part =========================== -->
				<table width="<? echo 580+($counts*50); ?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
					<caption><strong> Details </strong></caption>
					<thead>
						<tr> 
							<th width="30" rowspan="2">SI</th>
							<th width="100" rowspan="2">Color</th>
							<th width="100" rowspan="2">Country Name</th>
							<th width="100" rowspan="2">Serving Company</th>
							<th width="100" rowspan="2">Location</th>
							<th width="100" rowspan="2">Floor</th>
							<th colspan="<? echo $counts;?>">Size</th>
							<th width="50" rowspan="2">Total</th>
						</tr>
						<tr>
							<?
							foreach ($size_id_arr as $value)
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
						<table  id="table_body"  width="<? echo 580+($counts*50); ?>" border="1" rules="all" class="rpt_table" align="left" >
							<?
							$p=1;              	 
							$gr_total=0;
							foreach($data_array as  $color_id=> $color_data)            		 
							{
								foreach($color_data as  $country_id=> $country_data)  
								{
									foreach($country_data as  $wc_id=> $wc_data)  
									{
										foreach($wc_data as  $location_id=> $location_data)  
										{
											foreach($location_data as  $floor_id=> $rows)  
											{
												?>
												<tr>                	 
													<td align="center" width="30" ><? echo $p++;?></td>
													<td align="center"  width="100"><? echo $colorarr[$color_id];?></td>
													<td align="center"  width="100"><? echo $countryarr[$country_id];?></td>
													<td align="center"  width="100"><? echo $companyarr[$wc_id];?></td>
													<td align="center"  width="100"><? echo $locationarr[$location_id];?></td>
													<td align="center"  width="100"><? echo $floorarr[$floor_id];?></td>
													<?
													$total_qnty=0;
													foreach ($size_id_arr as $value)
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
										}
									}
								}
								$gr_total+=$qntys;
								?>
								<tr>    
									<td align="right" colspan="6">Color Total: </td>
									<?
									$total_qnty=0;
									foreach ($size_id_arr as $value)
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
							<tr style="font-weight: bold;">   
								<td align="right" colspan="6"><b>Day Total: </b></td>
								<?
								$total_qnty=0;
								foreach ($size_id_arr as $value)
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
	else if( ($type==1 && $day=='B') || ($type==4 && $day=='B') || ($type==5 && $day=='B') || ($type==8 && $day=='B') || ($type==0 && $day=='B') || ($type==11 && $day=='B'))
	{
		$order_data="SELECT e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po and d.item_number_id=$item and d.color_number_id=$color  group by  e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date ";

		$order_data_summary="SELECT c.id, e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,d.color_number_id,d.size_number_id, sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po and d.item_number_id=$item and d.color_number_id=$color  group by c.id, e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,d.color_number_id,d.size_number_id order by d.color_number_id,d.size_number_id";

		if($type)
		{
			if($type==1)
			{
				$production_data="SELECT a.entry_date as production_date,a.location_id as location,a.floor_id,a.serving_company as serving_company, 
				sum(case when a.production_source=1 then b.qc_pass_qty else 0 end) as inhouse ,
				sum(case when a.production_source=3 then b.qc_pass_qty else 0 end) as outbound 
				from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b ,wo_po_color_size_breakdown c 
				where a.id=b.mst_id and b.color_size_id=c.id and a.status_active=1 and a.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.order_id=$po and c.item_number_id=$item and b.color_id=$color 
				group by  a.entry_date,a.location_id,a.floor_id,a.serving_company";

				$production_data_summary="SELECT b.order_id,c.item_number_id,sum(b.qc_pass_qty) as production_qnty,b.size_id as size_number_id,b.color_id as color_number_id,b.country_id,a.location_id,a.floor_id,a.serving_company as working_company 
			from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b ,wo_po_color_size_breakdown c 
			where a.id=b.mst_id and b.color_size_id=c.id and a.status_active=1 and a.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.order_id=$po and c.item_number_id=$item and b.color_id=$color $date_cond_cut 
			group by  b.order_id,c.item_number_id,b.size_id,b.color_id,b.country_id,a.location_id,a.floor_id,a.serving_company ";
			}
			else
			{
				$production_data="SELECT a.floor_id,a.sewing_line, a.production_date,a.serving_company,a.location,sum(case when production_source=1 then b.production_qnty else 0 end ) as inhouse ,sum(case when production_source=3 then b.production_qnty else 0 end ) as outbound   from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where a.id=b.mst_id and b.color_size_break_down_id=d.id and a.po_break_down_id=c.id and d.po_break_down_id=c.id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id=$po and a.production_type=$type  and a.item_number_id=$item and d.color_number_id=$color  group by   a.floor_id,a.sewing_line,a.production_date,a.serving_company,a.location ";

				$production_data_summary="SELECT c.id, c.color_number_id,c.size_number_id, sum(case when a.production_type=$type then b.production_qnty else 0 end ) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c  where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.po_break_down_id=$po and a.production_type=$type  and a.item_number_id=$item and c.color_number_id=$color group by c.id,c.color_number_id,c.size_number_id order by c.id";
			}
		}
		else if($type==0)
		{
			$production_data="SELECT a.entry_date as production_date,a.location_id as location,a.floor_id,a.working_company_id as serving_company,  sum(c.size_qty) as inhouse from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c  where  a.id=b.mst_id and b.id=c.dtls_id     and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active =1 and c.is_deleted=0  and c.order_id=$po    and b.gmt_item_id=$item and b.color_id=$color  group by a.entry_date ,a.location_id,a.floor_id ,a.working_company_id ";

			$production_data_summary="SELECT c.order_id as po_break_down_id,b.gmt_item_id as item_number_id,sum(c.size_qty) as production_qnty,c.size_id as size_number_id,b.color_id as color_number_id,c.country_id,a.location_id,a.floor_id,a.working_company_id as working_company
			from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c 
			where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and c.order_id=$po and b.gmt_item_id=$item and b.color_id=$color $date_cond_lay group by c.order_id ,b.gmt_item_id,c.size_id,b.color_id,c.country_id,a.location_id,a.floor_id,a.working_company_id ";
		}		 
		$production_data=sql_select($production_data);

		foreach(sql_select($production_data_summary) as $vals)
		{
			
			$col_size_id_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];
			$col_id_arr[$vals[csf("color_number_id")]]+=$vals[csf("production_qnty")];
			$size_id_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
		}
		$counts=count($size_id_arr); 
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

				<!-- ===================== Summary part =========================== -->
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
							?>
							<tr>
								<td align="center" width="30"><?echo $p++; ?></td>
								<td align="center" width="100"><?echo $colorarr[$col_id]; ?></td>
								<?
								$total=0;
								foreach($size_id_arr as $vals)
								{
									?>
									<td width="45"><? echo $tot= $col_size_id_arr[$col_id][$vals]; ?></td>


									<?
									$total+=$tot;
									$size_wise_vertical_arr[$vals]+=$tot;
								}
								?>

								<td align="center" width="100"><?echo $total; ?></td>
							</tr>
							<?
						}
						?>
						<tr style="font-weight: bold;">
							<td colspan="2" align="right">Total</td>
							<?
							$total=0;
							foreach($size_id_arr as $vals)
							{
								?>
								<td width="45"><? echo $tot=$size_wise_vertical_arr[$vals]; ?></td>


								<?
								$total+=$tot;
								$size_wise_vertical_arr[$vals]+=$tot;
							}
							?>

							<td align="center" width="100"><?echo $total; ?></td>
						</tr>

					</tbody>
			    </table>
				<br>
				<!-- ===================== Details part =========================== -->

				
			    <table style="margin-top: 10px;" width="<? echo $tbl_wid;?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
			    	<caption><strong>Details</strong></caption>
						<thead>
							<tr> 
								<th  width="30" rowspan="2">SI</th>
								<th width="100" rowspan="2"><? if($type==1 || $type==0)echo "Cutting Date";else if($type==4 || $type==5 ) echo "Sewing Date"; else if($type==8) echo "Finish Date";?></th>
								<th rowspan="2" width="100">Floor</th>
								<?
									if($type==5 || $type==4)
									{
										?>
										<th rowspan="2" width="100">Sewing Line</th>

										<?
									}
								?>
								<th width="100" colspan="2"><? if($type==1)echo "Cutting Qty";else  if($type==4 || $type==5) echo "Sewing Qty";else if($type==8) echo "Finish Qty";else if($type==0) echo "Cut Qty";?> </th>
								<th width="100" rowspan="2"><? if($type==1)echo "Cutting Company";else  if($type==4 || $type==5) echo "Sewing Comany";else if($type==8) echo "Finish Company";else if($type==0) echo "Cut Company";?></th>

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
									<td width="100" align="center"><? echo $floorarr[$vals[csf("floor_id")]]; ?></td>

									<?
									if($type==5 || $type==4)
									{
										?>
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

				<table width="<? echo $tbl_wid;?>"  border="1" rules="all" class="rpt_table"  >
					<tfoot>
						<tr style="font-weight: bold;">
							<?
							if($type==5 || $type==4)
							{
								?>
								<td width="30">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100" align="right"><strong>Grand Total</strong></td>	
								<?
							}
							else
							{
								?>
								<td width="30">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100" align="right"><strong>Grand Total</strong></td>
								<?
							}
							?>
							<td id="ttl_inhouse" align="center" width="50"><?//echo $total_inhouse; ?></td>
							<td id="ttl_outbound" align="center" width="50"><?//echo $total_out; ?></td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
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
						col: [3,4],
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
	else if( ($type==4 && $day=='A') || ($type==5 && $day=='A') || ($type==8 && $day=='A') || ($type==11 && $day=='A') )
	{
		$order_data="SELECT c.id, e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,d.color_number_id,d.size_number_id, sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po and d.item_number_id=$item and d.color_number_id=$color  group by c.id, e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,d.color_number_id,d.size_number_id ";

		$order_data2="SELECT e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po and d.item_number_id=$item and d.color_number_id=$color  group by  e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date ";
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


		$production_data="SELECT a.serving_company, a.country_id,a.production_source,a.challan_no,a.floor_id,a.sewing_line,d.color_number_id,d.size_number_id,sum(b.production_qnty) as qnty   from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where a.id=b.mst_id and b.color_size_break_down_id=d.id and a.po_break_down_id=c.id and d.po_break_down_id=c.id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id=$po and a.production_type=$type  and a.item_number_id=$item and d.color_number_id=$color $date_cond  group by  a.serving_company, a.country_id,a.production_source,a.challan_no,a.floor_id,a.sewing_line,d.color_number_id,d.size_number_id  ";		  

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
			<table width="<? echo 280+($counts*50); ?>" cellspacing="0" cellpadding="0" border="0"   rules="">
					<thead>						
						<tr>
							<td height="5">&nbsp;</td>
						</tr>
						<tr>
							<td><strong>Date : <? echo change_date_format(str_replace("'", "", $dates)); ?></strong></td> 
						</tr>							
					</thead>
				</table>
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
					foreach(sql_select($order_data2) as $vals)
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
					if($type==5 || $type==4)$tbl_wid=700;else $tbl_wid=500;

					?>
				</tbody>
			</table>
			<br>
			
					<?
					if($type!=8)
					{
						?>
						<div> 
							<table width="<? echo 630+($counts*45);?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
								<caption><strong>Details</strong></caption>
								<thead>
									<tr> 
										<th width="30" rowspan="2">SI</th>
										<th width="80" rowspan="2">Country Name</th>
										<th width="50" rowspan="2">Source</th>
										<th width="70" rowspan="2">Challan</th>
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
								<table id="table_body"   width="<? echo 630+($counts*45);?>" border="1" rules="all" class="rpt_table">
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
										<tr style="font-weight: bold;">
											<td align="center" width="30"> </td>
											<td align="center" width="80"> </td>
											<td align="center" width="50"> </td>
											<td align="center" width="70"> </td>
											<td align="center" width="100"> </td>
											<td align="center" width="100">	</td>									 
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
									<tr style="font-weight: bold;">
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
	else if( ($type==2 && $day=='A') || ($type==3 && $day=='A'))
	{		
		$size_id_array=array();
		$size_wise_qnty_array=array();
		$color_wise_qnty_array=array();

		$order_data="SELECT e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po and d.item_number_id=$item and d.color_number_id=$color  group by  e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date ";

		$data_array=array();
		$production_data="SELECT a.po_break_down_id,c.item_number_id,c.size_number_id,c.color_number_id,c.country_id,a.location,a.floor_id,a.serving_company, sum(case when a.production_type=$type and a.embel_name=1 then b.production_qnty else 0 end) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.po_break_down_id=$po and c.item_number_id=$item and c.color_number_id=$color and a.production_type=$type $date_cond group by  a.po_break_down_id,c.item_number_id,c.size_number_id,c.color_number_id,c.country_id,a.location,a.floor_id,a.serving_company ";
		$production_data=sql_select($production_data);

		foreach($production_data as $vals)
		{

			$data_array[$vals[csf("color_number_id")]][$vals[csf("country_id")]][$vals[csf("serving_company")]][$vals[csf("location")]][$vals[csf("floor_id")]]=$vals[csf("country_id")];

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
						if(($day=="A" && $type==2) || ($day=="A" && $type==3)) 
						{
							?>
							<tr>
								<td height="5">&nbsp;</td>
							</tr>
							<tr>
								<td><strong>Date : <? echo change_date_format(str_replace("'", "", $dates)); ?></strong></td> 
							</tr>

							<?
						}//die;
						?>
					</thead>
				</table>

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
						if($type==5 || $type==4)$tbl_wid=600;else $tbl_wid=600;

						?>
					</tbody>
				</table>
				<br>
				<table width="<? echo 580+($counts*50); ?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
					<caption><strong>Details</strong></caption>
					<thead>
						<tr> 
							<th width="30" rowspan="2">SI</th>
							<th width="100" rowspan="2">Color</th>
							<th width="100" rowspan="2">Country Name</th>
							<th width="100" rowspan="2">Working Company</th>
							<th width="100" rowspan="2">Location</th>
							<th width="100" rowspan="2">Floor</th>
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
						<table  id="table_body"  width="<? echo 580+($counts*50); ?>" border="1" rules="all" class="rpt_table" align="left" >
							<?
							$p=1;              	 
							$gr_total=0;
							foreach($data_array as  $color_id=> $color_data)            		 
							{
								foreach($color_data as  $country_id=> $country_data)  
								{
									foreach($country_data as  $wc_id=> $wc_data)  
									{
										foreach($wc_data as  $location_id=> $location_data)  
										{
											foreach($location_data as  $floor_id=> $rows)  
											{
												?>
												<tr>                	 
													<td align="center" width="30" ><? echo $p++;?></td>
													<td align="center"  width="100"><? echo $colorarr[$color_id];?></td>
													<td align="center"  width="100"><? echo $countryarr[$country_id];?></td>
													<td align="center"  width="100"><? echo $companyarr[$wc_id];?></td>
													<td align="center"  width="100"><? echo $locationarr[$location_id];?></td>
													<td align="center"  width="100"><? echo $floorarr[$floor_id];?></td>
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
										}
									}
								}
								$gr_total+=$qntys;
								?>
								<tr>    
									<td align="right" colspan="6">Color Total: </td>
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
							<tr style="font-weight: bold;">   
								<td align="right" colspan="6"><b>Day Total: </b></td>
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
	else if( ($type==2 && $day=='B') || ($type==3 && $day=='B'))
	{		
		$size_id_array=array();
		$size_wise_qnty_array=array();
		$color_wise_qnty_array=array();
		$order_data="SELECT e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po and d.item_number_id=$item and d.color_number_id=$color  group by  e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date ";
		$data_array=array();
		if($working_company_id !=""){$working_company_cond=" and a.serving_company in($working_company_id)";}
		if($location_id !=""){$location_cond=" and a.location in($location_id)";}
		
		$production_data="SELECT a.po_break_down_id,c.item_number_id,c.size_number_id,c.color_number_id,c.country_id,a.serving_company,a.location,a.floor_id, sum(case when a.production_type=$type and a.embel_name=1 then b.production_qnty else 0 end) as production_qnty,sum(case when a.production_source=1 then b.production_qnty else 0 end) as inhouse,sum(case when a.production_source=3 then b.production_qnty else 0 end) as outbound,c.id from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.po_break_down_id=$po and c.item_number_id=$item and c.color_number_id=$color and a.production_type=$type and a.embel_name=1 $working_company_cond $location_cond group by  a.po_break_down_id,c.item_number_id,c.size_number_id,c.color_number_id,c.country_id,a.serving_company,a.location,a.floor_id,c.id order by c.id ";
		// echo $production_data;
		$production_data=sql_select($production_data);

		foreach($production_data as $vals)
		{

			$data_array[$vals[csf("color_number_id")]][$vals[csf("country_id")]][$vals[csf("serving_company")]][$vals[csf("location")]][$vals[csf("floor_id")]]['inhouse']+=$vals[csf("inhouse")];
			$data_array[$vals[csf("color_number_id")]][$vals[csf("country_id")]][$vals[csf("serving_company")]][$vals[csf("location")]][$vals[csf("floor_id")]]['outbound']+=$vals[csf("outbound")];
			$size_id_array[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
			$color_id_array[$vals[csf("color_number_id")]]=$vals[csf("color_number_id")];
			$size_wise_qnty_array[$vals[csf("color_number_id")]][$vals[csf("country_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];
			$color_wise_qnty_array[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];

		}
		$counts=count($size_id_array);  
		// print_r($color_wise_qnty_array);
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
						if($type==5 || $type==4)$tbl_wid=600;else $tbl_wid=600;

						?>
					</tbody>
				</table>
				<!-- ====================================== summary part ================================== -->
				<? $width = 60*count($size_id_array); ?>
				<table width="<? echo  210+$width;?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
					<caption><strong>Summary</strong></caption>
					<thead>
						<tr> 
							<th width="30" rowspan="2">SI</th>
							<th width="100" rowspan="2">Color</th>
							<th width="<? echo $width;?>" colspan="<? echo count($size_id_array);?>" >Size</th>
							<th width="80" rowspan="2">Total</th>
						</tr>
						<tr>
							<?
								foreach ($size_id_array as $key => $val) 
								{
									echo '<th width="60">'.$linearr[$key].'</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;
						foreach ($color_id_array as $key => $val) 
						{
							?>
							<td><? echo $i;?></td>
							<td><? echo $colorarr[$key]; ?></td>
							<?
							$total=0;
							$size_total = array();
							foreach ($size_id_array as $size_key => $val) 
							{
								echo '<td width="60" align="right">'.$color_wise_qnty_array[$key][$size_key].'</td>';
								$total+=$color_wise_qnty_array[$key][$size_key];
								$size_total[$size_key]+=$color_wise_qnty_array[$key][$size_key];
							}
							?>
							<td align="right"><? echo $total;?></td>
							<?
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2">Total</th>
							<?
							$g_total=0;
							foreach ($size_id_array as $size_key => $val) 
							{
								echo '<th align="right" width="60">'.$size_total[$size_key].'</th>';
								$g_total+=$size_total[$size_key];
							}
							?>
							<th align="right"><? echo $g_total;?></th>
						</tr>						
					</tfoot>
				</table> 
				<br>
				<!-- ==================================== details part ====================================== -->
				<table width="690" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
					<caption><strong>Details</strong></caption>
					<thead>
						<tr> 
							<th width="30" rowspan="2">SI</th>
							<th width="100" rowspan="2">Color</th>
							<th width="100" rowspan="2">Country Name</th>
							<th width="100" rowspan="2">Working Company</th>
							<th width="100" rowspan="2">Location</th>
							<th width="100" rowspan="2">Floor</th>
							<th width="160" colspan="2">Production Qty</th>
						</tr>
						<tr>
							<th>Inhouse</th>
							<th>Outbound</th>
						</tr>
					</thead>
				</table>
					<div style="max-height:300px;  ">
						<table  id="table_body"  width="690" border="1" rules="all" class="rpt_table" align="left" >
							<?
							$p=1;              	 
							$gr_inhouse=0;
							$gr_outbound=0;
							foreach($data_array as  $color_id=> $color_data)            		 
							{
								foreach($color_data as  $country_id=> $country_data)  
								{
									foreach($country_data as  $wc_id=> $wc_data)  
									{
										foreach($wc_data as  $location_id=> $location_data)  
										{
											foreach($location_data as  $floor_id=> $rows)  
											{
												?>
												<tr>                	 
													<td align="center" width="30" ><? echo $p++;?></td>
													<td align="center"  width="100"><? echo $colorarr[$color_id];?></td>
													<td align="center"  width="100"><? echo $countryarr[$country_id];?></td>
													<td align="center"  width="100"><? echo $companyarr[$wc_id];?></td>
													<td align="center"  width="100"><? echo $locationarr[$location_id];?></td>
													<td align="center"  width="100"><? echo $floorarr[$floor_id];?></td>
													<td align="center"  width="80"><b><? echo $rows['inhouse'];?></b></td>
													<td align="center"  width="80"><b><? echo $rows['outbound'];?></b></td>
												</tr>  
												<?              	 
												$gr_inhouse+=$rows['inhouse'];
												$gr_outbound+=$rows['outbound'];
											}
										}
									}
								}
							}
							?>  
							<tr style="font-weight: bold;">   
								<td align="right" colspan="6"><b>Total: </b></td>
								
								<td align="center"  width="80"><b><? echo $gr_inhouse;?></b></td>
								<td align="center"  width="80"><b><? echo $gr_outbound;?></b></td>
							</tr> 
						</table>
					</div>
				</div>
			</div>
			<script>   setFilterGrid("table_body",-1);  </script> 		 
		<?

	}
	else if( ($type==1 && $day=='C') || ($type==3 && $day=='C'))
	{		
		$size_id_array=array();
		$size_wise_qnty_array=array();
		$color_wise_qnty_array=array();
		
		$data_array=array();
		if($type==1) // print issue
		{
			$production_data="SELECT b.order_id,c.item_number_id,sum(b.reject_qty) as production_qnty,b.size_id as size_number_id,b.color_id as color_number_id,b.country_id 
			from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b ,wo_po_color_size_breakdown c 
			where a.id=b.mst_id and b.color_size_id=c.id and a.status_active=1 and a.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.order_id=$po and c.item_number_id=$item and b.color_id=$color 
			group by  b.order_id,c.item_number_id,b.size_id,b.color_id,b.country_id ";
		}
		else if($type==3) // print receive
		{
			 $production_data="SELECT a.po_break_down_id,c.item_number_id,c.size_number_id,c.color_number_id,c.country_id,
			sum(case when a.production_type in(2,3) and a.embel_name=1 then b.reject_qty else 0 end) as production_qnty 
			from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c 
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.po_break_down_id=$po and c.item_number_id=$item and c.color_number_id=$color 
			group by  a.po_break_down_id,c.item_number_id,c.size_number_id,c.color_number_id,c.country_id ";
		}
		// echo $production_data;
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
						if(($day=="C" && $type==1) || ($day=="C" && $type==3)) 
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
						}//die;
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
	else
	{}

 	?>
 	          
  	  <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
  	  </body>
   	 </html>
    
    <?	
	exit();
}

if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	
	?>
    <table>
    <tr>
    <?
    foreach ($data_array as $row)
	{ 
		?>
	    <td><img src='../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
	    <?
	}
	?>
    </tr>
    </table>
    
    <?
	exit();
}
?>